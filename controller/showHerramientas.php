<?

if (!$usr->usa_herramientas) {
	$T->setFile('tpl_contenido', 'contratar.tpl');
}

elseif ($sactual->seccion_id==REP_ATTOOL_PING or
	$sactual->seccion_id==REP_ATTOOL_DIG or
	$sactual->seccion_id==REP_ATTOOL_TRACE or
	$sactual->seccion_id==REP_ATTOOL_FULL) {
	
$host = $_POST["host"];
$accion = $_POST["accion"];

$monitores = $usr->getMonitores(REP_DATOS_CLIENTE);

$T->setFile('tpl_contenido', 'herramientas.tpl');
$T->setBlock('tpl_contenido','TIENE_ERROR','tiene_error');
$T->setBlock('tpl_contenido','LISTA_MONITORES','lista_monitores');
$T->setBlock('tpl_contenido','TIENE_NODOS','tiene_nodos');
$T->setBlock('tpl_contenido','TIENE_MENSAJE_NODOS','tiene_mensaje_nodos');
$T->setBlock('tpl_contenido','RESULTADO_COLUMNA','resultado_columna');
$T->setBlock('tpl_contenido','RESULTADO_FILA','resultado_fila');
$T->setBlock('tpl_contenido','LISTA_MONITORES_RESULTADO','lista_monitores_resultado');

//$T->setVar('__padre_id', $sactual->padre_id);
//$T->setVar('__seccion_id', $sactual->seccion_id);
//$T->setVar('__nivel', $sactual->nivel);

$T->setVar('__herramienta_host',$host);

/* TITULO DE LA SECCION SEGUN LA HERRAMIENTE A EJECUTAR */
if ($sactual->seccion_id==REP_ATTOOL_PING) {
	$T->setVar('__herramienta_nombre','Ping');
	$T->setVar('__herramienta_descripcion','Muestra el tiempo de ida y regreso de un paquete ICMP desde el servidor de monitoreo (requiere que el host responda las peticiones ICMP). Para realizar un ping, ingrese la url y escoja los servidores que quiere testear.');
}
elseif ($sactual->seccion_id==REP_ATTOOL_DIG) {
	$T->setVar('__herramienta_nombre','Dig');
	$T->setVar('__herramienta_descripcion','Entrega la respuesta del registro A que entrega el DNS RESOLVER del servidor de monitoreo (corresponde al DNS RESOLVER del ISP), de un host. Equivalente a "nslookup". Para realizar un dig, ingrese la URL y escoja los servidores que quiere testear.');
}
elseif ($sactual->seccion_id==REP_ATTOOL_TRACE) {
	$T->setVar('__herramienta_nombre','Traceroute');
	$T->setVar('__herramienta_descripcion','Muestra el trazado de red que siguen los paquetes de datos desde el servidor de monitoreo (trazado de gateways por donde pasan los paquetes). Para obtener el traceroute, ingrese la URL y escoja los servidores que quiere testear.');
}
elseif ($sactual->seccion_id==REP_ATTOOL_FULL) {
	$T->setVar('__herramienta_nombre','Bajada de P&aacute;gina');
	$T->setVar('__herramienta_descripcion','Genera una bajada de página sobre una URL y entrega el detalle de los elementos de la misma (estado, tiempo y peso). Para obtener información sobre los elementos descargados, ingrese la URL y escoja los servidores que quiere testear.');
}

/* LISTA DE MONITORES PARA LA HERRAMIENTA */
if (count($monitores) > 0) {
foreach ($monitores as $monitor) {
	$T->setVar('__monitor_nombre', $monitor->nombre);
	$T->setVar('__monitor_host', $monitor->host);
	$T->setVar('__monitor_id', $monitor->monitor_id);
	$T->setVar('__monitor_sel', (isset($_POST["monitor_".$monitor->monitor_id]))?"checked":"");
	$T->parse('lista_monitores', 'LISTA_MONITORES', true);
}
$T->parse('tiene_nodos', 'TIENE_NODOS', false);
}
else {
	$T->parse('tiene_mensaje_nodos', 'TIENE_MENSAJE_NODOS', false);
}

/* SI YA SE EJECUTO LA HERRAMIENTA */
if ($accion=="iniciar_herramienta") {
	
	if ($sactual->seccion_id==REP_ATTOOL_FULL) {
		if (!preg_match("/^http/",$host)) {
			$host = "http://".$host;
		}
	}
	else {
		$host = str_replace(array("http://","https://"), array(""), $host);
	}

	/* VARIABLES DE EJECUCION SEGUN LA HERRAMIENTA */
	if ($sactual->seccion_id==REP_ATTOOL_PING) {
		$ejecutable = "doPing";
		$destino = "--destino";
		$objeto = "";
	}
	elseif ($sactual->seccion_id==REP_ATTOOL_DIG) {
		$ejecutable = "doDig";
		$destino = "--destino";
		$objeto = "";
	}
	elseif ($sactual->seccion_id==REP_ATTOOL_TRACE) {
		$ejecutable = "doTrace";
		$destino = "--destino";
		$objeto = "";		
	}
	elseif ($sactual->seccion_id==REP_ATTOOL_FULL) {
		$ejecutable = "doFull";
		$destino = "--objetivo";
		$objeto = "--objeto='/'";		
	}
	
	/* SE EJECUTA LA HERRAMIENTA PARA CADA MONITOR */
	foreach ($monitores as $monitor) {
		
		if (isset($_POST["monitor_".$monitor->monitor_id])) {
			/* EJECUCION DE LA HERRAMIENTA */
			$resultado="";
			exec(REP_PATH_TOOLS.$ejecutable." --origen='".escapeshellcmd($monitor->host)."' ".$destino."='".escapeshellcmd($host)."' ".$objeto, $resultado, $status);
			
			/* RESULTADOS DE LA HERRAMIENTA */
			$T->setVar('__monitor_nombre',$monitor->nombre);
			$T->setVar('resultado_fila','');

			/* RESULTADO SI LA HERRAMIENTA ES FULL */
			if ($sactual->seccion_id==REP_ATTOOL_FULL and $status==0) {
				$xml_resultado = implode($resultado);
				$xml_resultado = str_replace(array("%3A","%2F"), array(":", "/"), $xml_resultado);
				
				/* PARSEO EL XML RESULTADO */
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($xml_resultado);
				
				/* DIBUJO LOS TITULOS DE LA TABLA */
				$T->setVar('resultado_columna','');
				foreach (array("Url","Codigo","Tamaño (bytes)","Respuesta (ms)") as $titulo) {
					$T->setVar('__resultado_colspan','1');
					$T->setVar('__resultado_class','celdanegra40 celdabordesuperior');
					$T->setVar('__resultado_valor',$titulo);
					$T->parse('resultado_columna','RESULTADO_COLUMNA',true);
				}
				$T->parse('resultado_fila','RESULTADO_FILA',true);
				
				/* DIBUJO LOS DATOS DE LA TABLA */
				foreach (Utiles::getElementsByArrayTagName($dom, array("monitoreo","objeto")) as $elemento) {
					$T->setVar('resultado_columna','');
					foreach ($elemento->childNodes as $nodo_hijo) {
						if (in_array($nodo_hijo->nodeName,array("url","codigo","contentlength","delta"))) {
							$T->setVar('__resultado_colspan','1');
							$T->setVar('__resultado_class','celdanegra10 celdaborde');
							$T->setVar('__resultado_valor',$nodo_hijo->nodeValue);
							$T->parse('resultado_columna','RESULTADO_COLUMNA',true);
						}
					}
					$T->parse('resultado_fila','RESULTADO_FILA',true);
				}
				$T->setVar('tiene_error',null);				
			}
			/* RESULTADO SI LA HERRAMIENTA ES CUALQUIERA MENOS FULL */
			elseif ($status==0) {
				/* DIVIDO EL TEXTO DE RESULTADO EN FILAS */
				foreach ($resultado as $key => $fila) {
					if ($fila != "") {
						
						/* DIVIDO LA FILA EN COLUMNAS SEGUN EL TIPO DE HERRAMIENTA */
						if ($sactual->seccion_id==REP_ATTOOL_TRACE) {
							$fila = str_replace(array("*"), array("*  *"), $fila);
							$columnas = explode("  ",$fila);
						}
						elseif($sactual->seccion_id==REP_ATTOOL_DIG) {
							$columnas = explode("\t", $fila);
							if (count($columnas)>1) {
								$fila = str_replace(array(" "), array("\t"), $fila);
								$fila = str_replace(array("\t\t\t"), array("\t"), $fila);
								$fila = str_replace(array("\t\t"), array("\t"), $fila);
								$fila = str_replace(array(".\tIN","ANY"), array(".\t \tIN","ANY\t "), $fila);
								$columnas = explode("\t", $fila);
							}
						}
						else {
							$columnas = array($fila);
						}
						
						/* DIBUJO LAS FILAS */
						$T->setVar('resultado_columna','');
						if (count($columnas)==1) {
							/* SI LA FILA ES UNA TITULO */
							if (preg_match("/PING|DiG|traceroute|SECTION/", $fila)) {
								$T->setVar('__resultado_class','celdanegra40 celdabordesuperior');
							}
							/* SI LA FILA ES UN SUBTITULO */
							elseif (preg_match("/;; Query|---/", $fila)) {
								$T->setVar('__resultado_class','celdanegra10 celdabordesuperior');
							}
							/* SI LA FILA ES DATO */
							else {
								$T->setVar('__resultado_class','celdanegra10 celdaborde');
							}
							$T->setVar('__resultado_colspan','100%');
							$T->setVar('__resultado_valor',$fila);
							$T->parse('resultado_columna','RESULTADO_COLUMNA',true);
						}
						
						/* DIBUJO LAS COLUMNAS */
						else {
							foreach ($columnas as $id => $columna) {
								$T->setVar('__resultado_colspan','1');
								$T->setVar('__resultado_class','celdanegra10 celdaborde');
								$T->setVar('__resultado_valor',$columna);
								$T->parse('resultado_columna','RESULTADO_COLUMNA',true);
							}
						}
						$T->parse('resultado_fila','RESULTADO_FILA',true);
					}
				}
				$T->setVar('tiene_error',null);
			}
			else {
				$T->parse('tiene_error','TIENE_ERROR',false);
			}
			
			$T->parse('lista_monitores_resultado','LISTA_MONITORES_RESULTADO',true);
		}
	}
}

}
else {
	$T->setFile('tpl_contenido', 'sorry_seccion.tpl');
}

$T->setVar('__sitio_contenido', $T->parse('out', 'tpl_contenido'));

?>