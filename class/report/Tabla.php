<?
// ini_set('display_errors', 1);
class Tabla {

//	var $tipo;
	var $tiempo_expiracion;
	var $extra;
	var $solicitud;
	var $resultado;

	/*
	 * Funcion Constructor.
	 * @param string $tipo (grafico|tabla|csv|configuracion)
	 */
	function Tabla() {
//		$this->tipo = $tipo;
		$this->tiempo_expiracion = 86400;
	}
	public function generarResultado() {
		if (method_exists($this, $this->solicitud)) {
			$metodo_nombre = $this->solicitud;
			$this->$metodo_nombre();
		}
	}

	/*************** FUNCIONES DE TABLAS GENERALES ***************/
	/*************** FUNCIONES DE TABLAS GENERALES ***************/
	/*************** FUNCIONES DE TABLAS GENERALES ***************/

	function getSemaforo() {
		global $usr;

		if ($this->extra["imprimir"]) {
			$this->resultado = $this->__getSemaforoNormal();
		}
		elseif ($usr->orientacion_semaforo == 0) {
			$this->resultado = $this->__getSemaforoNormal();
		}
		else {
			$this->resultado = $this->__getSemaforoInvertido();
		}
	}

	/**
	 * Funcion para obtener la nueva tabla de
	 * Semaforo.
	 */
	function __getSemaforoNormal() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$sql = "SELECT sonido_semaforo FROM public.cliente_usuario_extendido where usuario_id=".pg_escape_string($current_usuario_id);
	//	print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$sonido = $row["sonido_semaforo"];


		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.semaforo(".
				pg_escape_string($current_usuario_id).")";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = false;
		$dom->loadXML($row["semaforo"]);
		$xpath = new DOMXpath($dom);
		unset($row["semaforo"]);

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (!$xpath->query('//datos/dato')->length) {
			return $this->__generarContenedorSinDatos();
		}

		$conf_objetivos = $xpath->query('//objetivos/objetivo[@objetivo_id>0]');
		$conf_nodos = $xpath->query('//propiedades/nodos/nodo[@nodo_id!=0]');

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'semaforo.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_MONITORES', 'lista_monitores');
		$T->setBlock('tpl_tabla', 'LISTA_MONITORES_UBICACION', 'lista_monitores_ubicacion');
		$T->setBlock('tpl_tabla', 'LISTA_MONITORES_BLANCO', 'lista_monitores_blanco');
		$T->setBlock('tpl_tabla', 'TIENE_TOOLTIP', 'tiene_tooltip');

		$T->setBlock('tpl_tabla', 'LISTA_MONITORES_ESTADO', 'lista_monitores_estado');
		$T->setBlock('tpl_tabla', 'ESTADO_BLANCO', 'estado_blanco');
		$T->setBlock('tpl_tabla', 'BLOQUE_SALTO_PRINT', 'bloque_salto_print');
		$T->setBlock('tpl_tabla', 'LISTA_OBJETIVOS', 'lista_objetivos');
		$T->setBlock('tpl_tabla', 'LISTA_OBJETIVOS0', 'lista_objetivos0');

		$T->setVar('__item_id', $this->__item_id);
		$T->setVar('__item_id_nuevo', REP_ITEM_EVENTOS);
		$T->setVar('__ancho_tabla', ($this->extra["popup"])?'':'514px');
		$T->setVar('__display_botones', ($this->extra["popup"])?'none':'inline');
		$T->setVar('__popup', ($this->extra["popup"])?'1':'0');

		$tooltip_id = 0;
		$cnt_objetivos = 0;

		//OBTENER TIMEZONE DEL USUARIO
		$sql2 = "SELECT _cliente_tz"."(".pg_escape_string($current_usuario_id).")";
		$res0 =& $mdb2->query($sql2);
		
		if (MDB2::isError($res0)) {
		    $log->setError($sql2, $res0->userinfo);
		    exit();
		}
		$row = $res0->fetchRow();
		$tz= $row["_cliente_tz"];
		
		date_default_timezone_set('UTC');
		
		// RECORRE LOS OBJETIVOS
		$check_color='False';
		foreach ($conf_objetivos as $conf_objetivo) {

			//SEGUIRÁ SOLO SI EL OBJETIVO SE ENCUENTRA BIEN FORMADO
			$T->setVar('__objetivo_id', $conf_objetivo->getAttribute('objetivo_id'));
			$T->setVar('__objetivo_nombre', $conf_objetivo->getAttribute('nombre'));
			$T->setVar('__objetivo_servicio', $conf_objetivo->getAttribute('servicio'));
			$T->setVar('__objetivo_color', (($cnt_objetivos % 2) == 0)?'e9e9e9':'d4d4d4');
			$num_monitor = 0;
			$marcados =array();
			
			 /* OBTENER LOS DATOS Y PARSEARLO */
			$sql2 = "SELECT RANK() OVER (PARTITION BY ".pg_escape_string($current_usuario_id)." ORDER BY termino DESC),* FROM public._periodos_marcados_v2(".
			 			 pg_escape_string($current_usuario_id).",".
			 			 pg_escape_string($conf_objetivo->getAttribute('objetivo_id')).",".
			 			 "(now() - '1 DAY'::INTERVAL) AT TIME ZONE '".$tz."',".
			 			 //pg_escape_string($this->timestamp->getInicioPeriodo())."',".
			 			 "now() AT TIME ZONE '".$tz."')AS foo ORDER BY termino   DESC ";
//            print($sql2);
			 $res2 =& $mdb2->query($sql2);
			 if (MDB2::isError($res2)) {
			     $log->setError($sql2, $res2->userinfo);
			     exit();
			 }
			 		
			 while($row2 = $res2->fetchRow()) {
			     $marcados[$row2["rank"]]['marcado']= $row2["marcado"];
			     $marcados[$row2["rank"]]['inicio']= substr($row2["inicio"],0,-3);
			     $marcados[$row2["rank"]]['termino']= substr($row2["termino"],0,-3);
			 }
			 date_default_timezone_set($tz);
			 $now = new DateTime('now');
//  			 echo '<pre>';	if($marcados[2]['marcado']=="t")var_dump($marcados);
			
			if ($this->extra["imprimir"]) {
				$total_nodos = $xpath->query('//detalle[@objetivo_id='.$conf_objetivo->getAttribute('objetivo_id').']/detalles/detalle')->length;
			}
			else {
				$total_nodos = $conf_nodos->length;
			}

			$T->setVar('lista_monitores_estado', '');
			$T->setVar('estado_blanco', '');
			$T->setVar('bloque_salto_print', '');
			
			
			if($marcados[1]['marcado']=="t" ){
			    $inicio = new DateTime($marcados[1]['inicio']);
			    $intervalo = $now->diff($inicio);
			   
			    if($intervalo->format('%a')>0){
			        $duracion= $intervalo->format('%a days %H:%I:%S')."\n";
			    }else{
			        $duracion= $intervalo->format('%H:%I:%S')."\n";
			    }
			   
			    foreach ($conf_nodos as $conf_nodo) {
			        $T->setVar('__monitor_id', $conf_nodo->getAttribute('nodo_id'));
			        $T->setVar('__monitor_orden', $num_monitor);
			        $T->setVar('__monitor_nombre', $conf_nodo->getAttribute('titulo'));
			        $T->setVar('__monitor_ubicacion', $conf_nodo->getAttribute('subtitulo'));
			        $T->setVar('__tooltip_id', $tooltip_id);
			        
			        $tag_dato = $xpath->query('//detalle[@objetivo_id='.$conf_objetivo->getAttribute('objetivo_id').']/detalles/detalle[@nodo_id='.$conf_nodo->getAttribute('nodo_id').']/datos/dato')->item(0);
			        
			        if ($tag_dato == null and $this->extra["imprimir"]) {
			            continue;
			        }
			        elseif ($tag_dato != null) {
			        
    			        $conf_semaforo = $xpath->query('//semaforos/semaforo[@semaforo_id=6]')->item(0);
    			        
    			        $T->setVar('__evento_nombre', $conf_semaforo->getAttribute('nombre'));
    			        $T->setVar('__evento_descripcion', $conf_semaforo->getAttribute('descripcion'));
    			        $T->setVar('__evento_icono', REP_PATH_SPRITE_SEMAFORO.substr($conf_semaforo->getAttribute('icono'), 0, -4));
    			        $T->setVar('__evento_duracion', Utiles::formatDuracion($duracion, 0));
    			        $T->parse('tiene_tooltip', 'TIENE_TOOLTIP', false);
			        }
			        else {
			            $T->setVar('__evento_nombre', '');
			            $T->setVar('__evento_icono', 'sprite sprite-vacio');
			            $T->setVar('__evento_duracion', '');
			            $T->setVar('tiene_tooltip', '');
			        }
			        
			        $T->parse('lista_monitores_estado', 'LISTA_MONITORES_ESTADO', true);
			        if ($cnt_objetivos == 0) {
			            $T->parse('lista_monitores', 'LISTA_MONITORES', true);
			            $T->parse('lista_monitores_ubicacion', 'LISTA_MONITORES_UBICACION', true);
			        }
			        $tooltip_id++;
			        $num_monitor++;
			        
			        if ($num_monitor % 6 == 0 or $num_monitor == $total_nodos) {
			            $T->parse('bloque_salto_print', 'BLOQUE_SALTO_PRINT', true);
			            $T->setVar('lista_monitores_estado', '');
			        }
			    
			    }
			    
			}else{
			    	    
    			foreach ($conf_nodos as $conf_nodo) {
    				$T->setVar('__monitor_id', $conf_nodo->getAttribute('nodo_id'));
    				$T->setVar('__monitor_orden', $num_monitor);
    				$T->setVar('__monitor_nombre', $conf_nodo->getAttribute('titulo'));
    				$T->setVar('__monitor_ubicacion', $conf_nodo->getAttribute('subtitulo'));
    				$T->setVar('__tooltip_id', $tooltip_id);
    
    				$tag_dato = $xpath->query('//detalle[@objetivo_id='.$conf_objetivo->getAttribute('objetivo_id').']/detalles/detalle[@nodo_id='.$conf_nodo->getAttribute('nodo_id').']/datos/dato')->item(0);
    
    				if ($tag_dato == null and $this->extra["imprimir"]) {
    					continue;
    				}
    				elseif ($tag_dato != null) {
    					$conf_semaforo = $xpath->query('//semaforos/semaforo[@semaforo_id='.$tag_dato->getAttribute('semaforo_id').']')->item(0);
    
    					$duracion =$tag_dato->getAttribute('duracion');
    					
    					if($marcados[2]['marcado']=="t" && $tag_dato->getAttribute('semaforo_id')!=4)
    					{
    					    $inicio= new DateTime($marcados[2]['inicio']);
    					    $termino= new DateTime($marcados[2]['termino']);
    					    $diferencia = $termino->diff($inicio);
    					    if($diferencia->format('%a')>0){
    					        $duracion_marcado= $diferencia->format('%a days + %H hours + %I minutes + %S seconds')."\n";
        					}else{
        					    $duracion_marcado= $diferencia->format('%H hours + %I minutes + %S seconds')."\n";
        					}
        					
        					$fecha_nueva =  new DateTimeImmutable($tag_dato->getAttribute('fecha'));
        					$datetime=$fecha_nueva->modify('+ '.$duracion_marcado);
        					$intervalo = $now->diff($datetime);
        					if($intervalo->format('%a')>0){
        					    $duracion= $intervalo->format('%a days %H:%I:%S')."\n";
        					}else{
        					    $duracion= $intervalo->format('%H:%I:%S')."\n";
        					}
        					
    					}
    					
    					$T->setVar('__evento_nombre', $conf_semaforo->getAttribute('nombre'));
    					$T->setVar('__evento_descripcion', $conf_semaforo->getAttribute('descripcion'));
    					$T->setVar('__evento_icono', REP_PATH_SPRITE_SEMAFORO.substr($conf_semaforo->getAttribute('icono'), 0, -4));
    					if($conf_semaforo->getAttribute('icono')=='rojo.png'){
    						$check_color='True';
    					}
    					$T->setVar('__evento_duracion', Utiles::formatDuracion($duracion, 0));
    					$T->parse('tiene_tooltip', 'TIENE_TOOLTIP', false);
    				}
    				else {
    					$T->setVar('__evento_nombre', '');
    					$T->setVar('__evento_icono', 'sprite sprite-vacio');
    					$T->setVar('__evento_duracion', '');
    					$T->setVar('tiene_tooltip', '');
    				}
    				$T->parse('lista_monitores_estado', 'LISTA_MONITORES_ESTADO', true);
    
    				if ($cnt_objetivos == 0) {
    					$T->parse('lista_monitores', 'LISTA_MONITORES', true);
    					$T->parse('lista_monitores_ubicacion', 'LISTA_MONITORES_UBICACION', true);
    				}
    				$tooltip_id++;
    				$num_monitor++;
    
    				if ($num_monitor % 6 == 0 or $num_monitor == $total_nodos) {
    					$T->parse('bloque_salto_print', 'BLOQUE_SALTO_PRINT', true);
    					$T->setVar('lista_monitores_estado', '');
    				}
    			}
            }
			$T->setVar('estado_blanco', '');
			if($num_monitor == 0 && $this->extra["imprimir"]){
				$T->parse('estado_blanco', 'ESTADO_BLANCO', false);
				$T->parse('bloque_salto_print', 'BLOQUE_SALTO_PRINT', true);
			}

			$T->parse('lista_objetivos', 'LISTA_OBJETIVOS', true);
			$T->parse('lista_objetivos0', 'LISTA_OBJETIVOS0', true);
			$cnt_objetivos++;
		}
		/* sirve para sonido por color*/
		$T->setVar('__session', $_SESSION["alerta"]);
		$T->setVar('__checkcolor', $check_color);
		if($sonido =='t'){
			if($_SESSION["alerta"]=="" AND $check_color=='True'){
				$T->setVar('__check', $check_color);
				$_SESSION["alerta"] = True;
			}
			if($_SESSION["alerta"]==True AND $check_color=='False'){
				$_SESSION["alerta"] = False;
			}

		}
		$T->setVar('lista_monitores_blanco', '');
		if (($num_monitor % 6) != 0 and !$this->extra["popup"]) {
			for ($i = ($num_monitor % 6); $i <= 6; $i++) {
				$T->parse('lista_monitores_blanco', 'LISTA_MONITORES_BLANCO', true);
			}
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		return $T->parse('out', 'tpl_tabla');
	}
	
	/**
	 * Funcion para obtener la tabla de
	 * Semaforo INACAP.
	 */
	function __getSemaforoInvertido() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.semaforo(".
				pg_escape_string($current_usuario_id).")";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = false;
		$dom->loadXML($row["semaforo"]);
		$xpath = new DOMXpath($dom);
		unset($row["semaforo"]);

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (!$xpath->query('//datos/dato')->length) {
			return $this->resultado = $this->__generarContenedorSinDatos();
		}

		$conf_objetivos = $xpath->query('//objetivos/objetivo[@objetivo_id>0]');
		$conf_nodos = $xpath->query('//propiedades/nodos/nodo[@nodo_id!=0]');

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'semaforo_invertido.tpl');
		$T->setBlock('tpl_tabla', 'TIENE_TOOLTIP', 'tiene_tooltip');
		$T->setBlock('tpl_tabla', 'LISTA_OBJETIVOS', 'lista_objetivos');
		$T->setBlock('tpl_tabla', 'LISTA_OBJETIVOS0', 'lista_objetivos0');
		$T->setBlock('tpl_tabla', 'LISTA_OBJETIVO_VACIO', 'lista_objetivo_vacio');
		$T->setBlock('tpl_tabla', 'LISTA_MONITORES0', 'lista_monitores0');
		$T->setBlock('tpl_tabla', 'LISTA_MONITORES', 'lista_monitores');

		$T->setVar('__item_id', $this->__item_id);
		$T->setVar('__item_id_nuevo', REP_ITEM_EVENTOS);
		$T->setVar('__ancho_tabla', ($this->extra["popup"])?'':'514px');
		$T->setVar('__display_botones', ($this->extra["popup"])?'none':'inline');
		$T->setVar('__popup', ($this->extra["popup"])?'1':'0');

		$tooltip_id = 0;
		$num_monitor = 0;
		$T->setVar('lista_monitores_estado','');
		foreach ($conf_nodos as $conf_nodo) {
			$T->setVar('__monitor_id', $conf_nodo->getAttribute('nodo_id'));
			$T->setVar('__monitor_nombre', $conf_nodo->getAttribute('titulo'));
			$T->setVar('__monitor_ubicacion', $conf_nodo->getAttribute('subtitulo'));
			$T->setVar('__monitor_color', (($num_monitor % 2) == 0)?'e9e9e9':'d4d4d4');
			$T->setVar('lista_objetivos', '');

			foreach ($conf_objetivos as $conf_objetivo) {
				$T->setVar('__objetivo_id', $conf_objetivo->getAttribute('objetivo_id'));
				$T->setVar('__objetivo_nombre', $conf_objetivo->getAttribute('nombre'));
				$T->setVar('__objetivo_servicio', $conf_objetivo->getAttribute('servicio'));
				$T->setVar('__tooltip_id', $tooltip_id);

				$tag_dato = $xpath->query('//detalle[@objetivo_id='.$conf_objetivo->getAttribute('objetivo_id').']/detalles/detalle[@nodo_id='.$conf_nodo->getAttribute('nodo_id').']/datos/dato')->item(0);

				if ($tag_dato != null) {
					$conf_semaforo = $xpath->query('//semaforos/semaforo[@semaforo_id='.$tag_dato->getAttribute('semaforo_id').']')->item(0);

					$T->setVar('__evento_nombre', $conf_semaforo->getAttribute('nombre'));
					$T->setVar('__evento_descripcion', $conf_semaforo->getAttribute('descripcion'));
					$T->setVar('__evento_icono', REP_PATH_SPRITE_SEMAFORO.substr($conf_semaforo->getAttribute('icono'), 0, -4));
					$T->setVar('__evento_duracion', Utiles::formatDuracion($tag_dato->getAttribute('duracion'), 0));
					$T->parse('tiene_tooltip', 'TIENE_TOOLTIP', false);
				}
				else {
					$T->setVar('__evento_nombre', '');
					$T->setVar('__evento_icono', 'sprite sprite-vacio');
					$T->setVar('__evento_duracion', '');
					$T->setVar('tiene_tooltip', '');
				}

				$tooltip_id++;
				$T->parse('lista_objetivos', 'LISTA_OBJETIVOS', true);

				if ($num_monitor == 0) {
					$T->parse('lista_objetivos0', 'LISTA_OBJETIVOS0', true);
				}
			}
			$T->parse('lista_monitores0', 'LISTA_MONITORES0', true);
			$T->parse('lista_monitores', 'LISTA_MONITORES', true);
			$num_monitor++;
		}

		$T->setVar('lista_objetivo_vacio', '');
		$count_objetivo = $xpath->query('/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id>0]')->length;
		if (($count_objetivo % 6) != 0 and !$this->extra["popup"]) {
			for ($i = ($count_objetivo % 6); $i <= 6; $i++) {
				$T->parse('lista_objetivo_vacio', 'LISTA_OBJETIVO_VACIO', true);
			}
		}
		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));

		return $T->parse('out', 'tpl_tabla');
	}

	function getVistaRapida() {
		global $usr;

		if ($this->extra["imprimir"]) {
			$this->resultado = $this->__getVistaRapidaNormal();
		}
		elseif ($usr->orientacion_semaforo == 0) {
			$this->resultado = $this->__getVistaRapidaNormal();
		}
		else {
			$this->resultado = $this->__getVistaRapidaInvertida();
		}
	}

	function __getVistaRapidaNormal() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$estado_cname = array("" => "s/i", "-1" => "desconocido", "0" => "ok", "3" => "timeout", "5" => "s/m", "6" => "timeout",
				"7" => "timeout", "8" => "ok", "9" => "dns ok", "13" => "e/patron", "14" => "dns ok", "15" => "s/respuesta",
				"16" => "s/respuesta", "23" => "e/inverso", "27" => "timeout js", "30" => "ok", "31" => "e/envio", "33" => "timeout");

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.vistarapida(".
				pg_escape_string($current_usuario_id).")";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

        $row = $res->fetchRow();
        $dom = new DomDocument();
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($row["vistarapida"]);
        $xpath = new DOMXpath($dom);

        /* SI NO HAY DATOS MOSTRAR MENSAJE */
        if (!$xpath->query('//datos/dato')->length) {
            return $this->resultado = $this->__generarContenedorSinDatos();
        }


		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'vista_rapida.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS_BLANCO', 'lista_nodos_blanco');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS', 'lista_nodos');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS_UBICACION', 'lista_nodos_ubicacion');
		$T->setBlock('tpl_tabla', 'TITULOS_PATRONES', 'titulos_patrones');
		$T->setBlock('tpl_tabla', 'TITULOS_PASOS', 'titulos_pasos');
		$T->setBlock('tpl_tabla', 'TITULOS_OBJETIVOS', 'titulos_objetivos');
		$T->setBlock('tpl_tabla', 'TIENE_TOOLTIP', 'tiene_tooltip');
		$T->setBlock('tpl_tabla', 'LISTA_ESTADOS', 'lista_estados');
		$T->setBlock('tpl_tabla', 'LISTA_PATRONES', 'lista_patrones');
		$T->setBlock('tpl_tabla', 'LISTA_RESPUESTA', 'lista_respuesta');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_tabla', 'LISTA_FECHA', 'lista_fecha');
		$T->setBlock('tpl_tabla', 'ESTADO_BLANCO', 'estado_blanco');
		$T->setBlock('tpl_tabla', 'LISTA_GRUPOS', 'lista_grupos');
		$T->setBlock('tpl_tabla', 'LISTA_OBJETIVOS', 'lista_objetivos');

		$codigos = array();
		$codigos["null"] = array("nombre" => "", "descripcion" => "", "color" => "d4d4d4", "icono" => "vacio.png");
		foreach ($xpath->query("/atentus/resultados/propiedades/codigos/codigo") as $tag_codigo) {
			$codigos[$tag_codigo->getAttribute("codigo_id")] = array("id" => $tag_codigo->getAttribute("codigo_id"),
																	 "nombre" => $tag_codigo->getAttribute("nombre"),
																	 "descripcion" => $tag_codigo->getAttribute("descripcion"),
																	 "color" => $tag_codigo->getAttribute("color"),
																	 "icono" => $tag_codigo->getAttribute("icono"));
		}


		$T->setVar('__ancho_tabla', ($this->extra["popup"])?'':'514px');
		$T->setVar('__display_botones', ($this->extra["popup"])?'none':'inline');

		$arr_nodos = array();
		foreach ($xpath->query("/atentus/resultados/propiedades/grupos/grupo") as $tag_grupo) {
			foreach ($xpath->query("nodos/nodo", $tag_grupo) as $tag_nodo) {
				$arr_nodos[] = $tag_nodo;
				$dato = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$tag_nodo->getAttribute("nodo_id")."]")->item(0);
				$T->setVar('__grupo_id', $tag_grupo->getAttribute("orden"));
				$T->setVar('__nodo_id', $dato->getAttribute("nodo_id"));
				$T->setVar('__nodo_nombre', $dato->getAttribute("titulo"));
				$T->setVar('__nodo_ubicacion', $dato->getAttribute("subtitulo"));
				$T->parse('lista_nodos', 'LISTA_NODOS', true);
				$T->parse('lista_nodos_ubicacion', 'LISTA_NODOS_UBICACION', true);
			}
		}

		$T->setVar('lista_nodos_blanco', '');
		if ((count($arr_nodos) % 6) != 0 and !$this->extra["popup"]) {
			for ($i = (count($arr_nodos) % 6); $i < 6; $i++) {
				$T->parse('lista_nodos_blanco', 'LISTA_NODOS_BLANCO', true);
			}
		}

		$det_objetivos = array();
		foreach ($xpath->query("/atentus/resultados/detalles/detalle[@semaforo_id=2]") as $det_objetivo) {
			$det_objetivos[] = $det_objetivo;
		}
		foreach ($xpath->query("/atentus/resultados/detalles/detalle[@semaforo_id!=2]") as $det_objetivo) {
			$det_objetivos[] = $det_objetivo;
		}


		$tooltip_id = 1;
		foreach ($det_objetivos as $det_objetivo) {

			$tag_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$det_objetivo->getAttribute("objetivo_id")."]")->item(0);

			if ($xpath->query("paso", $tag_objetivo)->length == 0) {
				continue;
			}

			$T->setVar('__objetivo_id', $tag_objetivo->getAttribute("objetivo_id"));
			$T->setVar('__objetivo_nombre', $tag_objetivo->getAttribute("nombre"));
			$T->setVar('__objetivo_servicio', $tag_objetivo->getAttribute("servicio"));
			$T->setVar('__objetivo_color', ($det_objetivo->getAttribute("semaforo_id")==2)?"d3222a":"7b8ebb");

			$T->setVar('lista_grupos', '');

			if ($this->extra["imprimir"]) {
				$arr_nodos = array();
				foreach ($xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$tag_objetivo->getAttribute("objetivo_id")."]/detalles/detalle") as $tag_nodo) {
					$arr_nodos[] = $tag_nodo;
				}
				$arr_slicenodos = array_slice($arr_nodos, 0, 6);
			}
			else {
				$arr_slicenodos = $arr_nodos;
			}

			$T->setVar('estado_blanco', '');
			if(count($arr_nodos) == 0 && $this->extra["imprimir"]){
				$T->parse('estado_blanco', 'ESTADO_BLANCO', false);
			}

			$arr_pos = count($arr_slicenodos);
			while (count($arr_slicenodos) > 0) {
				$es_objetivo = true;

				$T->setVar('lista_fecha', '');
				$T->setVar('titulos_pasos', '');
				$T->setVar('lista_pasos', '');

				foreach ($xpath->query("paso", $tag_objetivo) as $tag_paso) {
					if ($tag_paso->getAttribute("visible") == 0) {
						continue;
					}

					$es_paso = true;

					$T->setVar('__paso_id', $tag_paso->getAttribute("paso_orden"));
					$T->setVar('lista_respuesta', '');

					if ($xpath->query("patron", $tag_paso)->length > 0) {
						$patrones = $xpath->query("patron", $tag_paso);
					}
					else {
						$patrones = array(null);
					}

					$T->setVar('titulos_patrones', '');
					$T->setVar('lista_patrones', '');
					foreach ($patrones as $id_patron => $tag_patron) {

						$T->setVar('lista_estados', '');

						foreach ($arr_slicenodos as $tag_nodo) {

							$datos = $xpath->query("detalles/detalle[@nodo_id=".$tag_nodo->getAttribute("nodo_id")."]/detalles/detalle[@paso_orden=".$tag_paso->getAttribute("paso_orden")."]/datos/dato", $det_objetivo);
							if ($datos->length >= ($id_patron + 1)) {
								$dato = $datos->item($id_patron);
								$codigo = $codigos[$dato->getAttribute("codigo_id")];
							}
							elseif ($datos->length > 0) {
								$dato = $datos->item(0);
								$codigo = $codigos[$dato->getAttribute("codigo_id")];
							}
							else {
								$dato = null;
								$codigo = $codigos["null"];
							}

							$T->setVar('__paso_nombre', ($es_paso)?$tag_paso->getAttribute("nombre"):'');

							$T->setVar('__tooltip_id', $tooltip_id);
							$T->setVar('__evento_nombre', $codigo["nombre"]);
							$T->setVar('__evento_nombre_print', (array_key_exists("id",$codigo)?(($estado_cname[$codigo["id"]])?$estado_cname[$codigo["id"]]:$codigo["id"]):null));
							$T->setVar('__evento_descripcion', $codigo["descripcion"]);
							$T->setVar('__evento_color', $codigo["color"]);
							$T->setVar('__evento_icono', REP_PATH_SPRITE_CODIGO.substr($codigo["icono"], 0, -4));
							$T->parse('lista_estados', 'LISTA_ESTADOS', true);

							$T->setVar('tiene_tooltip', '');
							if ($dato != null) {
								$T->setVar('__tooltip_duracion', Utiles::formatDuracion($dato->getAttribute("duracion"), 0));
								$T->setVar('__tooltip_patron', ($tag_patron == null)?'':$tag_patron->getAttribute("nombre"));
								$T->setVar('__tooltip_respuesta', ($dato->getAttribute("respuesta") >= 0)?number_format(($dato->getAttribute("respuesta")/1000), 2, ',', '').' [s]':"Sin Información");
								$T->parse('tiene_tooltip', 'TIENE_TOOLTIP', false);
							}

							if ($es_paso) {
								if ($dato != null) {
									$strtime = floor(strtotime($dato->getAttribute("duracion")) / 86400000);
									$T->setVar('__evento_respuesta', ($dato->getAttribute("respuesta") >= 0)?number_format(($dato->getAttribute("respuesta")/1000), 2, ',', '').' [s]':"S/I");
									$T->setVar('__evento_duracion', ($strtime > 0)?"+$strtime d&iacute;a(s)":$dato->getAttribute("duracion"));
								}
								else {
									$T->setVar('__evento_respuesta', '');
									$T->setVar('__evento_duracion', '');

								}
								$T->parse('lista_respuesta', 'LISTA_RESPUESTA', true);
							}

							if ($es_objetivo) {
								$conf_nodo = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$tag_nodo->getAttribute("nodo_id")."]")->item(0);
								$T->setVar('__evento_nodo', $conf_nodo->getAttribute("titulo"));
								$T->setVar('__evento_ubicacion', $conf_nodo->getAttribute("subtitulo"));
								if ($dato != null) {
									$T->setVar('__evento_fecha', $this->timestamp->getFormatearFecha($dato->getAttribute("fecha"), "H:i:s"));
								}
								else {
									$T->setVar('__evento_fecha', '&nbsp;');
								}
								$T->parse('lista_fecha', 'LISTA_FECHA', true);
							}
							$tooltip_id++;
						}
						$T->parse('titulos_patrones', 'TITULOS_PATRONES', true);
						$T->parse('lista_patrones', 'LISTA_PATRONES', true);
						$es_paso = false;
						$es_objetivo = false;
					}
					$T->parse('titulos_pasos', 'TITULOS_PASOS', true);
					$T->parse('lista_pasos', 'LISTA_PASOS', true);
				}
				$arr_slicenodos = array_slice($arr_nodos, $arr_pos, 6);
				$arr_pos = $arr_pos + 6;
				$T->parse('lista_grupos', 'LISTA_GRUPOS', true);
			}
			$T->parse('titulos_objetivos', 'TITULOS_OBJETIVOS', true);
			$T->parse('lista_objetivos', 'LISTA_OBJETIVOS', true);
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		return $T->parse('out', 'tpl_tabla');
	}

	function __getVistaRapidaInvertida() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.vistarapida(".
				pg_escape_string($current_usuario_id).")";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

        $row = $res->fetchRow();
        $dom = new DomDocument();
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($row["vistarapida"]);
        $xpath = new DOMXpath($dom);

        /* SI NO HAY DATOS MOSTRAR MENSAJE */
        if (!$xpath->query('//datos/dato')->length) {
            return $this->resultado = $this->__generarContenedorSinDatos();
        }

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'vista_rapida_invertida.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_OBJETIVOS', 'lista_objetivos');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS_BLANCO', 'lista_pasos_blanco');
		$T->setBlock('tpl_tabla', 'TITULOS_NODOS', 'titulos_nodos');
		$T->setBlock('tpl_tabla', 'TIENE_TOOLTIP', 'tiene_tooltip');
		$T->setBlock('tpl_tabla', 'LISTA_ESTADOS', 'lista_estados');
		$T->setBlock('tpl_tabla', 'LISTA_RESPUESTA', 'lista_respuesta');
		$T->setBlock('tpl_tabla', 'LISTA_FECHA', 'lista_fecha');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS', 'lista_nodos');

		$T->setVar('__ancho_tabla', ($this->extra["popup"])?'':'514px');
		$T->setVar('__display_botones', ($this->extra["popup"])?'none':'inline');

		$codigos = array();
		$codigos["null"] = array("nombre" => "", "descripcion" => "", "color" => "d4d4d4", "icono" => "vacio.png");
		foreach ($xpath->query("/atentus/resultados/propiedades/codigos/codigo") as $tag_codigo) {
			$codigos[$tag_codigo->getAttribute("codigo_id")] = array("nombre" => $tag_codigo->getAttribute("nombre"),
					"descripcion" => $tag_codigo->getAttribute("descripcion"),
					"color" => $tag_codigo->getAttribute("color"),
					"icono" => $tag_codigo->getAttribute("icono"));
		}

		$det_objetivos = array();
		foreach ($xpath->query("/atentus/resultados/detalles/detalle[@semaforo_id=2]") as $det_objetivo) {
			$tag_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$det_objetivo->getAttribute("objetivo_id")."]")->item(0);
			if ($tag_objetivo != null) {
				$det_objetivos[] = array("data" => $det_objetivo, "conf" => $tag_objetivo);
			}
		}
		foreach ($xpath->query("/atentus/resultados/detalles/detalle[@semaforo_id!=2]") as $det_objetivo) {
			$tag_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$det_objetivo->getAttribute("objetivo_id")."]")->item(0);
			if ($tag_objetivo != null) {
				$det_objetivos[] = array("data" => $det_objetivo, "conf" => $tag_objetivo);
			}
		}

		$tooltip_id = 1;
		$cnt_nodo = 0;
		foreach ($xpath->query("/atentus/resultados/propiedades/grupos/grupo") as $tag_grupo) {
			foreach ($xpath->query("nodos/nodo", $tag_grupo) as $tag_nodo) {
				$es_nodo = true;
				$cnt_estados = 0;


				$dato = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$tag_nodo->getAttribute("nodo_id")."]")->item(0);

				$T->setVar('__grupo_id', $tag_grupo->getAttribute("orden"));
				$T->setVar('__nodo_id', $dato->getAttribute("nodo_id"));
				$T->setVar('__nodo_nombre', $dato->getAttribute("titulo"));
				$T->setVar('__nodo_ubicacion', $dato->getAttribute("subtitulo"));

				$T->setVar('lista_estados', '');
				$T->setVar('lista_respuesta', '');
				$T->setVar('lista_fecha', '');
				foreach ($det_objetivos as $info_objetivo) {
					$det_objetivo = $info_objetivo["data"];
					$tag_objetivo = $info_objetivo["conf"];

					$T->setVar('__objetivo_id', $tag_objetivo->getAttribute("objetivo_id"));
					$T->setVar('__objetivo_nombre', $tag_objetivo->getAttribute("nombre"));
					$T->setVar('__objetivo_servicio', $tag_objetivo->getAttribute("servicio"));
					$T->setVar('__objetivo_color', ($det_objetivo->getAttribute("semaforo_id")==2)?"d3222a":"7b8ebb");

					$cnt_pasos = 0;

					if ($xpath->query("paso", $tag_objetivo)->length == 0) {
						continue;
					}

					foreach ($xpath->query("paso", $tag_objetivo) as $tag_paso) {

						if ($tag_paso->getAttribute("visible") == 0) {
							continue;
						}

						$T->setVar('__paso_id', $tag_paso->getAttribute("paso_orden"));
						$T->setVar('__paso_nombre', $tag_paso->getAttribute("nombre"));


						if ($xpath->query("paso", $tag_objetivo)->length == 0) {
							continue;
						}

						if ($xpath->query("patron", $tag_paso)->length > 0) {
							$patrones = $xpath->query("patron", $tag_paso);
						}
						else {
							$patrones = array(null);
						}

						$cnt_patrones = 0;

						foreach ($patrones as $id_patron => $tag_patron) {
							$datos = $xpath->query("detalles/detalle[@nodo_id=".$tag_nodo->getAttribute("nodo_id")."]/detalles/detalle[@paso_orden=".$tag_paso->getAttribute("paso_orden")."]/datos/dato", $det_objetivo);
							if ($datos->length >= ($id_patron + 1)) {
								$dato = $datos->item($id_patron);
								$codigo = $codigos[$dato->getAttribute("codigo_id")];
							}
							elseif ($datos->length > 0) {
								$dato = $datos->item(0);
								$codigo = $codigos[$dato->getAttribute("codigo_id")];
							}
							else {
								$dato = null;
								$codigo = $codigos["null"];
							}

							$T->setVar('__tooltip_id', $tooltip_id);
							$T->setVar('__evento_nombre', $codigo["nombre"]);
							$T->setVar('__evento_descripcion', $codigo["descripcion"]);
							$T->setVar('__evento_color', $codigo["color"]);
							$T->setVar('__evento_icono', REP_PATH_SPRITE_CODIGO.substr($codigo["icono"], 0, -4));
							$T->parse('lista_estados', 'LISTA_ESTADOS', true);

							$T->setVar('tiene_tooltip', '');
							if ($dato != null) {
								$T->setVar('__tooltip_duracion', Utiles::formatDuracion($dato->getAttribute("duracion"), 0));
								$T->setVar('__tooltip_patron', ($tag_patron == null)?'':$tag_patron->getAttribute("nombre"));
								$T->setVar('__tooltip_respuesta', ($dato->getAttribute("respuesta") >= 0)?number_format(($dato->getAttribute("respuesta")/1000), 2, ',', '').' [s]':"Sin Información");
								$T->parse('tiene_tooltip', 'TIENE_TOOLTIP', false);
							}

							if ($es_nodo) {
								if ($dato != null) {
									$T->setVar('__evento_respuesta', ($dato->getAttribute("respuesta") >= 0)?number_format(($dato->getAttribute("respuesta")/1000), 2, ',', '').' [s]':"S/I");
								}
								else {
									$T->setVar('__evento_respuesta', '');
								}
								$T->parse('lista_respuesta', 'LISTA_RESPUESTA', true);
							}

							if ($dato != null) {
								$T->setVar('__evento_fecha', $this->timestamp->getFormatearFecha($dato->getAttribute("fecha"), "H:i:s"));
							}
							else {
								$T->setVar('__evento_fecha', '');
							}
							$T->parse('lista_fecha', 'LISTA_FECHA', true);

							$tooltip_id++;
							$cnt_patrones++;
							$cnt_pasos++;
							$cnt_estados++;
						}
						if ($cnt_nodo == 0) {
							$T->setVar('__paso_colspan', $cnt_patrones);
							$T->setVar('__paso_width', (80 * $cnt_patrones));
							$T->parse('lista_pasos', 'LISTA_PASOS', true);
						}
					}
					if ($cnt_nodo == 0) {
						$T->setVar('__objetivo_colspan', $cnt_pasos);
						$T->setVar('__objetivo_width', (80 * $cnt_pasos));
						$T->parse('lista_objetivos', 'LISTA_OBJETIVOS', true);
					}
				}
				$es_nodo = false;
				$T->parse('titulos_nodos', 'TITULOS_NODOS', true);
				$T->parse('lista_nodos', 'LISTA_NODOS', true);
				$cnt_nodo++;
			}
		}

		$T->setVar('__cantidad_estados', $cnt_estados);
		$T->setVar('lista_pasos_blanco', '');
		if (($cnt_estados % 6) != 0 and !$this->extra["popup"]) {
			for ($i = ($cnt_estados % 6); $i < 6; $i++) {
				$T->parse('lista_pasos_blanco', 'LISTA_PASOS_BLANCO', true);
			}
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		return $T->parse('out', 'tpl_tabla');
	}

	function getVistaRapidaNormalHorizontal() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$estado_cname = array("" => "s/i", "-1" => "desconocido", "0" => "ok", "3" => "timeout", "5" => "s/m", "6" => "timeout",
				"7" => "timeout", "8" => "ok", "9" => "dns ok", "13" => "e/patron", "14" => "dns ok", "15" => "s/respuesta",
				"16" => "s/respuesta", "23" => "e/inverso", "27" => "timeout js", "30" => "ok", "31" => "e/envio", "33" => "timeout");

		$sql = "SELECT * FROM reporte.vistarapida(".pg_escape_string($current_usuario_id).")";
		//echo $sql;
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

        $row = $res->fetchRow();
        $dom = new DomDocument();
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($row["vistarapida"]);
        $xpath = new DOMXpath($dom);

        if (!$xpath->query('//datos/dato')->length) {
            return $this->resultado = $this->__generarContenedorSinDatos();
        }

		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'vista_rapida_horizontal.tpl');
		$T->setBlock('tpl_tabla', 'FECHA_NODO', 'fecha_nodo');
		$T->setBlock('tpl_tabla', 'LISTA_PATRONES', 'lista_patrones');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS', 'lista_nodos');
		$T->setBlock('tpl_tabla', 'LISTA_FECHA', 'lista_fecha');		
		$T->setBlock('tpl_tabla', 'TITULOS_PASOS', 'titulos_pasos');
		$T->setBlock('tpl_tabla', 'TITULOS_OBJETIVOS', 'titulos_objetivos');
		$T->setBlock('tpl_tabla', 'TD_BLANCOS', 'td_blancos');

		$codigos = array();
		$codigos["null"] = array("nombre" => "", "descripcion" => "", "color" => "d4d4d4", "icono" => "vacio.png");

		foreach ($xpath->query("/atentus/resultados/propiedades/codigos/codigo") as $tag_codigo) {
			$codigos[$tag_codigo->getAttribute("codigo_id")] = array("id" => $tag_codigo->getAttribute("codigo_id"),
			"nombre" => $tag_codigo->getAttribute("nombre"),
			"descripcion" => $tag_codigo->getAttribute("descripcion"),
			"color" => $tag_codigo->getAttribute("color"),
			"icono" => $tag_codigo->getAttribute("icono"));
		}

		$T->setVar('__ancho_tabla', ($this->extra["popup"])?'':'514px');
		$T->setVar('__display_botones', ($this->extra["popup"])?'none':'inline');

		$det_objetivos = array();
		foreach ($xpath->query("/atentus/resultados/detalles/detalle[@semaforo_id=2]") as $det_objetivo) {
			if ($xpath->query('/atentus/resultados/detalles/detalle[@objetivo_id="'.$det_objetivo->getAttribute("objetivo_id").'"]/detalles/detalle/@nodo_id')->item(0)->value == "") {
				continue;
			}
			$det_objetivos[] = $det_objetivo;
		}
		foreach ($xpath->query("/atentus/resultados/detalles/detalle[@semaforo_id!=2]") as $det_objetivo) {
			if ($xpath->query('/atentus/resultados/detalles/detalle[@objetivo_id="'.$det_objetivo->getAttribute("objetivo_id").'"]/detalles/detalle/@nodo_id')->item(0)->value == "") {
				continue;
			}
			$det_objetivos[] = $det_objetivo;
		}

		$grupo_id = 1;
		$T->setVar('titulos_objetivos', '');
		foreach ($det_objetivos as $det_objetivo) {
			$sql_nodos ="SELECT foo.nodo_id FROM (
											SELECT DISTINCT unnest(_nodos_id) AS nodo_id 
											FROM _nodos_id(".pg_escape_string($current_usuario_id).", ".$det_objetivo->getAttribute("objetivo_id").", '".$this->timestamp->getInicioPeriodo()."')) AS foo, nodo n 
											WHERE foo.nodo_id=n.nodo_id 
											ORDER BY orden";

			$res_nodos =& $mdb2->query($sql_nodos);
			if (MDB2::isError($res_nodos)) {
				$log->setError($sql_nodos, $res_nodos->userinfo);
				exit();
			}

			$nodos_ids = array();
			if ($res_nodos->numRows() == 0) {
				$nodos_ids[] = NULL;
			}
			while($row_nodos = $res_nodos->fetchRow()) {
				$nodos_ids[] = $row_nodos["nodo_id"];
			}

			$conf_paso = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$det_objetivo->getAttribute("objetivo_id")."]/paso[@visible=1]");
			$pasos_str = '';
			foreach ($conf_paso  as $paso) {
				$conf_patron = $xpath->query("patron", $paso);
				if ($conf_patron->length != 0) {
					$cont_patron = 0;
					foreach ($conf_patron as $patron) {
						$pasos_str .='<tr>
							<td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color:#d4d4d4;" class="textgris12" id="paso_'.$det_objetivo->getAttribute("objetivo_id").'_'.$paso->getAttribute("paso_orden").'">';
							if ($cont_patron <1) {
								$pasos_str .= '<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 180px;">'.$paso->getAttribute("nombre").'</div>';
							}
						
						$pasos_str .= '
							</td>
						</tr>';
						$cont_patron++;
					}					
				}else{
					$pasos_str .='<tr>
					<td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color:#d4d4d4;" class="textgris12" id="paso_'.$det_objetivo->getAttribute("objetivo_id").'_'.$paso->getAttribute("paso_orden").'">
						<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 180px;">'.$paso->getAttribute("nombre").'</div>
					</td>
				</tr>';
				}				
					$pasos_str .= '<tr><td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color:#e9e9e9;">&nbsp;</td></tr>';
				}
				$T->setVar('__pasos', $pasos_str);			
		
			$tag_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$det_objetivo->getAttribute("objetivo_id")."]")->item(0);
			$T->setVar('__objetivo_id', $tag_objetivo->getAttribute("objetivo_id"));
			$T->setVar('__objetivo_nombre', $tag_objetivo->getAttribute("nombre"));
			$T->setVar('__objetivo_servicio', $tag_objetivo->getAttribute("servicio"));
			$T->setVar('__objetivo_color', ($det_objetivo->getAttribute("semaforo_id")==2)?"d3222a":"7b8ebb");

			$T->setVar('lista_nodos', '');
			$cant_nodos = count($nodos_ids);
			$T->setVar('__cant_nodos', strval($cant_nodos));
			$T->setVar('__tamaño_titulo_objetivo', $cant_nodos*80);
			foreach ($nodos_ids as $nodo) {
				if ($nodo == null) {
					$T->setVar('lista_nodos', '');
					$T->setVar('__grupo_id', $grupo_id);
					$T->setVar('__nodo_id', '_sin_monitor_asociado_');
					$T->setVar('__nodo_nombre', 'Sin monitor asociado');
					$T->setVar('__nodo_ubicacion','');
					$T->setVar('__evento_fecha', '&nbsp;');
					$T->setVar('lista_pasos', '');
					foreach ($xpath->query("paso[@visible=1]", $tag_objetivo) as $tag_paso) {
						$conf_patron = $xpath->query("patron", $tag_paso);
						$es_paso = true;
						if ($conf_patron->length != 0) {
							$cont_patron = 0;
							$T->setVar('lista_patrones', '');
							$T->setVar('lista_estados', '');
							foreach ($conf_patron as $patron) {
								if ($cont_patron <1) {
									$T->setVar('__tooltip_id', '');
								$T->setVar('__evento_nombre', '');
								$T->setVar('__evento_nombre_print', '');
								$T->setVar('__evento_descripcion', '');
								$T->setVar('__evento_color', 'd4d4d4');
								$T->setVar('__evento_icono', '');
									
									$T->setVar('__tooltip_duracion', '');
									$T->setVar('__tooltip_patron', '');
									$T->setVar('__tooltip_respuesta', '');
								}
								$T->setVar('__tooltip_id', '');
								$T->setVar('__evento_nombre', '');
								$T->setVar('__evento_nombre_print', '');
								$T->setVar('__evento_descripcion', '');
								$T->setVar('__evento_color', 'd4d4d4');
								$T->setVar('__evento_icono', '');
								
								$T->setVar('__tooltip_duracion', '');
								$T->setVar('__tooltip_patron', '');
								$T->setVar('__tooltip_respuesta', '');
								if ($es_paso) {
									$T->setVar('__evento_respuesta', '');
									$T->setVar('__evento_duracion', '');
								}

								$T->parse('lista_patrones', 'LISTA_PATRONES', true);
								$cont_patron++;
								$es_paso = false;
							}
						}else{
							$T->setVar('__evento_nombre', '');
							$T->setVar('__evento_color', 'd4d4d4');
						}
						$T->parse('lista_pasos', 'LISTA_PASOS', true);
					}
					$T->parse('lista_nodos', 'LISTA_NODOS', true);
					continue;
				}

				$dato_nodo = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$nodo."]")->item(0);
				$T->setVar('__grupo_id', $grupo_id);
				$T->setVar('__nodo_id', $dato_nodo->getAttribute("nodo_id"));
				$T->setVar('__nodo_nombre', $dato_nodo->getAttribute("titulo"));
				$T->setVar('__nodo_ubicacion', $dato_nodo->getAttribute("subtitulo"));
				
				$paso_id = 0;
				$T->setVar('lista_pasos', '');
				foreach ($xpath->query("paso", $tag_objetivo) as $tag_paso) {
					if ($tag_paso->getAttribute("visible") == 0) {
						continue;
					}

					$T->setVar('__paso_id', $tag_paso->getAttribute("paso_orden"));

					if ($xpath->query("patron", $tag_paso)->length > 0) {
						$patrones = $xpath->query("patron", $tag_paso);
					}
					else {
						$patrones = array(null);
					}
					$es_paso = true;
					$patron_id = 0;
					$T->setVar('lista_patrones', '');
					foreach ($patrones as $id_patron => $tag_patron) {
						$datos = $xpath->query("detalles/detalle[@nodo_id=".$nodo."]/detalles/detalle[@paso_orden=".$tag_paso->getAttribute("paso_orden")."]/datos/dato", $det_objetivo);

						if ($datos->length >= ($id_patron + 1)) {
							$dato = $datos->item($id_patron);
							$codigo = $codigos[$dato->getAttribute("codigo_id")];
						}
						elseif ($datos->length > 0) {
							$dato = $datos->item(0);
							$codigo = $codigos[$dato->getAttribute("codigo_id")];
						}
						else {
							$dato = null;
							$codigo = $codigos["null"];
						}

						$T->setVar('__tooltip_id', 'id_'.$nodo.'_'.$tag_objetivo->getAttribute("objetivo_id").'_'.$paso_id.'_'.$patron_id);
						$T->setVar('__evento_nombre', $codigo["nombre"]);
						$T->setVar('__evento_nombre_print', (array_key_exists("id",$codigo)?(($estado_cname[$codigo["id"]])?$estado_cname[$codigo["id"]]:$codigo["id"]):null));
						$T->setVar('__evento_descripcion', $codigo["descripcion"]);
						$T->setVar('__evento_color', $codigo["color"]);
						$T->setVar('__evento_icono', REP_PATH_SPRITE_CODIGO.substr($codigo["icono"], 0, -4));
						
						if ($dato != null) {
							$T->setVar('__tooltip_duracion', Utiles::formatDuracion($dato->getAttribute("duracion"), 0));
							$T->setVar('__tooltip_patron', ($tag_patron == null)?'':$tag_patron->getAttribute("nombre"));
							$T->setVar('__tooltip_respuesta', ($dato->getAttribute("respuesta") >= 0)?number_format(($dato->getAttribute("respuesta")/1000), 2, ',', '').' [s]':"Sin Información");
						}
						
						if ($es_paso) {
							if ($dato != null) {
								$strtime = floor(strtotime($dato->getAttribute("duracion")) / 86400000);
								$T->setVar('__evento_respuesta', ($dato->getAttribute("respuesta") >= 0)?number_format(($dato->getAttribute("respuesta")/1000), 2, ',', '').' [s]':"S/I");
								$T->setVar('__evento_duracion', ($strtime > 0)?"+$strtime d&iacute;a(s)":$dato->getAttribute("duracion"));
							}
							else {
								$T->setVar('__evento_respuesta', '');
								$T->setVar('__evento_duracion', '');
							}
						}
						$es_paso = false;
						$patron_id++;
						$T->parse('lista_patrones', 'LISTA_PATRONES', true);
						
					}
					$paso_id++;
					$T->parse('lista_pasos', 'LISTA_PASOS', true);
				}
				$conf_nodo = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$nodo."]")->item(0);
				$T->setVar('__evento_nodo', $conf_nodo->getAttribute("titulo"));
				$T->setVar('__evento_ubicacion', $conf_nodo->getAttribute("subtitulo"));

				if ($dato != null) {
					$T->setVar('__evento_fecha', $this->timestamp->getFormatearFecha($dato->getAttribute("fecha"), "H:i:s"));
				}		
				$T->parse('lista_nodos', 'LISTA_NODOS', true);
			}
			$T->parse('titulos_objetivos', 'TITULOS_OBJETIVOS', true);
			$grupo_id++;
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	function getVistaRapidaConsolidado() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.vistarapida_consolidado(".
				pg_escape_string($current_usuario_id).")";
// 				print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if($row = $res->fetchRow()){
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["vistarapida_consolidado"]);
			$xpath = new DOMXpath($dom);
			unset($row["vistarapida_consolidado"]);
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'vista_rapida_consolidado.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_ESTADOS', 'lista_estados');
		$T->setBlock('tpl_tabla', 'LISTA_OBJETIVOS', 'lista_objetivos');


		$conf_codigos = $xpath->query("/atentus/resultados/propiedades/codigos/codigo");
		$T->setVar('__ancho_tabla', ($this->extra["popup"])?'':'514px');

		$det_objetivos = array();
		foreach ($xpath->query("/atentus/resultados/detalles/detalle[@semaforo_id=2]") as $det_objetivo) {
			$det_objetivos[] = $det_objetivo;
		}
		foreach ($xpath->query("/atentus/resultados/detalles/detalle[@semaforo_id!=2]") as $det_objetivo) {
			$det_objetivos[] = $det_objetivo;
		}

		$tooltip_id = 1;
		$cnt_objetivos = 0;
		foreach ($det_objetivos as $det_objetivo) {
			$T->setVar('__evento_color', '');
			$tag_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$det_objetivo->getAttribute("objetivo_id")."]")->item(0);
			$T->setVar('__objetivo_id', $tag_objetivo->getAttribute("objetivo_id"));
			$T->setVar('__objetivo_nombre', $tag_objetivo->getAttribute("nombre"));
			$T->setVar('__objetivo_servicio', $tag_objetivo->getAttribute("servicio"));
			$T->setVar('__objetivo_color', ($det_objetivo->getAttribute("semaforo_id")==2)?"d3222a":"7b8ebb");
			$T->setVar('__estado_color', (($cnt_objetivos % 2) == 0)?'e9e9e9':'d4d4d4');
			$T->setVar('lista_estados', '');
			$T->setVar('lista_nodos_blanco', '');
			foreach ($conf_codigos as $conf_codigo){
				$tag_dato =  $xpath->query("datos/dato[@codigo_id=".$conf_codigo->getAttribute("codigo_id")."]",$det_objetivo)->item(0);
				if($conf_codigo->getAttribute("codigo_id")==0){
					$color= 'spriteSemaforo spriteSemaforo-verde';
				}elseif ($conf_codigo->getAttribute("codigo_id")==1){
					$color= 'spriteSemaforo spriteSemaforo-rojo';
				}else{
					$color= 'spriteSemaforo spriteSemaforo-blanco';
				}

				if($tag_dato!=null){
					$T->setVar('__evento_color', $conf_codigo->getAttribute("color"));
					$T->setVar('__evento_icono',$color);
					$T->parse('lista_estados', 'LISTA_ESTADOS', true);

				}
			}
			$cnt_objetivos++;
			$T->parse('lista_objetivos', 'LISTA_OBJETIVOS', true);
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		$this->resultado =$T->parse('out', 'tpl_tabla');
	}

	/*************** FUNCIONES DE TABLAS DE DISPONIBILIDAD ***************/
	/*************** FUNCIONES DE TABLAS DE DISPONIBILIDAD ***************/
	/*************** FUNCIONES DE TABLAS DE DISPONIBILIDAD ***************/

	/**
	 * Funcion que muestra la tabla resumen de disponibilidad.
	 * Si se selecciona un horario habil se muestra una tabla para
	 * todo horario y una para horario habil.
	 */
	// TODO: metodo getDisponibilidadResumenFlexible()
	function getTablaDisponibilidadDetalladaFlexible() {
		$this->extra["variable"] = 'true';
		echo $this->getTablaDisponibilidadDetallada();
	}

	/*
	Creado por:
	Modificado por: Carlos sepúlveda
	Fecha de creacion:
	Fecha de ultima modificacion: 16-06-2017
	*/
	// TODO: metodo getDisponibilidadResumen()
	function getTablaDisponibilidadDetallada(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$event = new Event;
		$graficoSvg = new GraficoSVG();

		$objetivo = new ConfigObjetivo($this->objetivo_id);
		$servicio_id = $objetivo->getServicio()->servicio_id;

		if ($servicio_id != 800) {
			$horario_preferido = 'true';
		}else{
			$objetivo_padre = $this->extra["parent_objetivo_id"];
			$objetivo = new ConfigEspecial($objetivo_padre);
			$horario_preferido = $objetivo->horario_preferido;
		}

		# Variables para eventos especiales marcados por el cliente codigo 9.
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		$nameFunction =  'tabla_detallado_disponibilidad';
		$tieneEvento = 'false';
		$dataMant = null;
		$marcado = false;
		$encode = null;
		$ids= null;
	
		$horarios = array($usr->getHorario($this->horario_id));
		if (!isset($_REQUEST['word'])) {

			if ($horario_preferido != 'true') {
				if ($this->horario_id != 0) {
					$horarios[] = $usr->getHorario(0);
					$horarios[($this->horario_id*-1)] = new Horario(($this->horario_id*-1));
					$horarios[($this->horario_id*-1)]->nombre = "Horario Inhabil";
				}
			}
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'disponibilidad_resumen.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS_TITULOS', 'bloque_eventos_titulos');
		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS', 'bloque_eventos');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_TITULO_HORARIOS', 'bloque_titulo_horarios');
		$T->setBlock('tpl_tabla', 'BLOQUE_HORARIOS', 'bloque_horarios');

		$orden = 1;
		// RECORRE TODOS LOS HORARIOS, (SI SE HA ASIGNADO UNO DIFERENTE A TODO HORARIO)

		foreach ($horarios as $horario) {
			$T->setVar('bloque_pasos','');
			$T->setVar('bloque_titulo_horarios','');

			$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado (".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					pg_escape_string($horario->horario_id).",' ".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."', ".
					(isset($this->extra["variable"])?$usr->cliente_id:"0").")";
//			print($sql);
			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["disponibilidad_resumen_consolidado"]);
			$xpath = new DOMXpath($dom);
			
			if ($orden == 1){
				# Busca marcados dentro del xml.
				foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
				$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
				$marcado = true;
				}	
				
				# Verifica y asigna variables en caso de que exista marcado.
				if ($marcado == true) {

					$dataMant =$event->getData(substr($ids, 1), $timeZone);
					$character = array("{", "}");
					$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
					$tieneEvento = 'true';
					$encode = json_encode($dataMant);
					$T->setVar('__tiene_evento', $tieneEvento);
					$T->setVar('__name', $nameFunction);
				}	

			}
			
			$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
				

			/* SI NO HAY DATOS MOSTRAR MENSAJE */
			if (!$xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]")->length) {
				$this->resultado = $this->__generarContenedorSinDatos();
				return;
			}

			if(isset($_REQUEST['word'])){
				$T->setVar('__item_orden', $this->extra["item_orden"]);
				$T->setVar('__horario_orden', $orden);
				$T->setVar('__horario_nombre',$horario->nombre);
				$T->parse('bloque_titulo_horarios', 'BLOQUE_TITULO_HORARIOS', false);
			}elseif(count($horarios) > 1) {
				$T->setVar('__item_orden', $this->extra["item_orden"]);
				$T->setVar('__horario_orden', $orden);
				$T->setVar('__horario_nombre',$horario->nombre);
				$T->parse('bloque_titulo_horarios', 'BLOQUE_TITULO_HORARIOS', false);
			}elseif(count($horarios) == 1) {
				$T->setVar('__item_orden', $this->extra["item_orden"]);
				$T->setVar('__horario_orden', $orden);
				$T->setVar('__horario_nombre',$horario->nombre);
				$T->parse('bloque_titulo_horarios', 'BLOQUE_TITULO_HORARIOS', false);
			}
	
			$T->setVar('bloque_eventos_titulos', '');
			foreach ($conf_eventos as $conf_evento) {
				# Para que no muestre los eventos clientes cuando no existen en el periodo.
				if ($marcado == false and $conf_evento->getAttribute("evento_id") == '9'){
					continue;
				}
				$T->setVar('__evento_nombre', $conf_evento->getAttribute("nombre"));
				$T->parse('bloque_eventos_titulos', 'BLOQUE_EVENTOS_TITULOS', true);
			}
				
			$linea = 1;
			foreach ($xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]") as $conf_paso) {
				$tag_paso = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]")->item(0);
				$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
				$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
				$T->setVar('__pasoNombre', $conf_paso->getAttribute("nombre"));
	
				$T->setVar('bloque_eventos', '');
				foreach ($conf_eventos as $conf_evento) {
					# Para que no muestre los datos eventos clientes cuando no existen en el periodo.
					if ($marcado == false and $conf_evento->getAttribute("evento_id") == '9'){
						continue;
					}

					$tag_evento = $xpath->query("estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]", $tag_paso);
					$T->setVar('__evento_valor', number_format(($tag_evento->length == 0)?"0":$tag_evento->item(0)->getAttribute("porcentaje"), 3, '.', ''));
					$T->setVar('__evento_color', ($conf_evento->getAttribute("color") == "f0f0f0")?"b2b2b2":$conf_evento->getAttribute("color"));
					$T->parse('bloque_eventos', 'BLOQUE_EVENTOS', true);
				}

				$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
				$linea++;
			}

			$T->parse('bloque_horarios', 'BLOQUE_HORARIOS', true);
			$orden++;
		}
		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));

		$this->resultado = $T->parse('out', 'tpl_tabla');
		/* Agrega el acordeon cuando existan eventos*/
		if (count($dataMant)>0){
			$this->resultado.= $graficoSvg->getAccordion($encode,$nameFunction);
		}
	}

	function getTablaDisponibilidadDetalladaEspecial($sub_obj){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		//exit;
		$event = new Event;
		$graficoSvg = new GraficoSVG();

		$objetivo = new ConfigObjetivo($sub_obj);
		# Variables para eventos especiales marcados por el cliente codigo 9.
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		//exit;
		$timeZone = $arrTime[$timeZoneId];
		$nameFunction =  'tabla_detallado_disponibilidad';
		$tieneEvento = 'false';
		$dataMant = null;
		$marcado = false;
		$encode = null;
		$ids= null;

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'disponibilidad_resumen_especial.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_TITULO_HORARIOS', 'bloque_titulo_horarios');
		$T->setBlock('tpl_tabla', 'BLOQUE_HORARIOS', 'bloque_horarios');

		$orden = 1;
		$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado (".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($sub_obj).", ".
				pg_escape_string($this->horario_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."', ".
				(isset($this->extra["variable"])?$usr->cliente_id:"0").")";

		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row["disponibilidad_resumen_consolidado"]);
		$xpath = new DOMXpath($dom);
		
		if ($orden == 1){
			# Busca marcados dentro del xml.
			foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
			}	
			# Verifica y asigna variables en caso de que exista marcado.
			if ($marcado == true) {
				$dataMant =$event->getData(substr($ids, 1), $timeZone);
				$character = array("{", "}");
				$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
				$tieneEvento = 'true';
				$encode = json_encode($dataMant);
				$T->setVar('__tiene_evento', $tieneEvento);
				$T->setVar('__name', $nameFunction);
			}
		}
		
		$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");			

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (!$xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]")->length) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		$T->setVar('bloque_pasos','');
		$uptime = 0;
		$downtime = 0;
		$d_parcial  = 0;
		$no_mon = 0;
		$uptime_real = 0;
		$factor = 0;
		$uptime_real_obj = 0;
		$downtime_real_obj = 0;
		$no_mon_real_obj = 0;
		$linea = 1;
		foreach ($xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]") as $conf_paso) {
			$tag_paso = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]")->item(0);
			$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion1":"celdaIteracion2");
			$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
			$T->setVar('__color_uptime', ($linea % 2 == 0)?"71c137":"55a51c");
			$T->setVar('__color_downtime', ($linea % 2 == 0)?"e04f56":"d3222a");
			$T->setVar('__color_no_mon', ($linea % 2 == 0)?"b2b2b2":"909090");
			$T->setVar('__pasoNombre', $conf_paso->getAttribute("nombre"));

			foreach ($conf_eventos as $conf_evento) {
				# Para que no muestre los datos eventos clientes cuando no existen en el periodo.
				if ($marcado == false and $conf_evento->getAttribute("evento_id") == '9'){
					continue;
				}

				$tag_evento = $xpath->query("estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]", $tag_paso);

				if ($tag_evento->length != 0) {
					if ($conf_evento->getAttribute("evento_id") == 1) {
						$uptime = $tag_evento->item(0)->getAttribute("porcentaje");
					}				
					if ($conf_evento->getAttribute("evento_id") == 2) {
						$downtime = $tag_evento->item(0)->getAttribute("porcentaje");
					}
					if ($conf_evento->getAttribute("evento_id") == 3) {
						$d_parcial = $tag_evento->item(0)->getAttribute("porcentaje");
					}
					if ($conf_evento->getAttribute("evento_id") == 7) {
						$no_mon = $tag_evento->item(0)->getAttribute("porcentaje");
					}
				}else{
					if ($conf_evento->getAttribute("evento_id") == 1) {
						$uptime = '0';
					}				
					if ($conf_evento->getAttribute("evento_id") == 2) {
						$downtime = '0';				
					}
					if ($conf_evento->getAttribute("evento_id") == 3) {
						$d_parcial = '0';
					}
					if ($conf_evento->getAttribute("evento_id") == 7) {
						$no_mon = '0';
					}
				}			
			}
			
			$uptime_real = $uptime + $d_parcial;
			$factor = ( $uptime_real + $downtime + $no_mon );
			$uptime_real_obj = ($uptime_real * 100) / $factor;
			$downtime_real_obj = ($downtime * 100) / $factor;
			$no_mon_real_obj = ($no_mon * 100) / $factor;
			
			$T->setVar('__uptime_real_o', number_format($uptime_real_obj, 2, '.', ''));
			$T->setVar('__downtime_real_o', number_format($downtime_real_obj, 2, '.', ''));
			$T->setVar('__no_mon_real_o', number_format($no_mon_real_obj, 2, '.', ''));

			$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
			$linea++;
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));

		return $T->parse('out', 'tpl_tabla');
		/* Agrega el acordeon cuando existan eventos*/
		if (count($dataMant)>0){
			$this->resultado.= $graficoSvg->getAccordion($encode,$nameFunction);
		}
	}

	function getEspecialDisponibilidadResumenTi(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$event = new Event;
		$graficoSvg = new GraficoSVG();

		$objetivo = new ConfigObjetivo($this->objetivo_id);
		$servicio_id = $objetivo->getServicio()->servicio_id;

		if ($servicio_id != 800) {
			$horario_preferido = 'true';
		}else{
			$objetivo_padre = $this->extra["parent_objetivo_id"];
			$objetivo = new ConfigEspecial($objetivo_padre);
			$horario_preferido = $objetivo->horario_preferido;
		}

		# Variables para eventos especiales marcados por el cliente codigo 9.
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		$nameFunction =  'tabla_detallado_disponibilidad';
		$tieneEvento = 'false';
		$dataMant = null;
		$marcado = false;
		$encode = null;
		$ids= null;
	
		$horarios = array($usr->getHorario($this->horario_id));
		if (!isset($_REQUEST['word'])) {

			if ($horario_preferido != 'true') {
				if ($this->horario_id != 0) {
					$horarios[] = $usr->getHorario(0);
					$horarios[($this->horario_id*-1)] = new Horario(($this->horario_id*-1));
					$horarios[($this->horario_id*-1)]->nombre = "Horario Inhabil";
				}
			}
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'disponibilidad_resumen_ti.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS_TITULOS', 'bloque_eventos_titulos');
		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS', 'bloque_eventos');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_TITULO_HORARIOS', 'bloque_titulo_horarios');
		$T->setBlock('tpl_tabla', 'BLOQUE_HORARIOS', 'bloque_horarios');

		$orden = 1;
		// RECORRE TODOS LOS HORARIOS, (SI SE HA ASIGNADO UNO DIFERENTE A TODO HORARIO)

		foreach ($horarios as $horario) {
			$T->setVar('bloque_pasos','');
			$T->setVar('bloque_titulo_horarios','');

			$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado (".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					pg_escape_string($horario->horario_id).",' ".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."', ".
					(isset($this->extra["variable"])?$usr->cliente_id:"0").")";
			//print($sql);
			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["disponibilidad_resumen_consolidado"]);
			$xpath = new DOMXpath($dom);
			
			if ($orden == 1){
				# Busca marcados dentro del xml.
				foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
				$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
				$marcado = true;
				}	
				
				# Verifica y asigna variables en caso de que exista marcado.
				if ($marcado == true) {

					$dataMant =$event->getData(substr($ids, 1), $timeZone);
					$character = array("{", "}");
					$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
					$tieneEvento = 'true';
					$encode = json_encode($dataMant);
					$T->setVar('__tiene_evento', $tieneEvento);
					$T->setVar('__name', $nameFunction);
				}	

			}
			
			$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
				

			/* SI NO HAY DATOS MOSTRAR MENSAJE */
			if (!$xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]")->length) {
				$this->resultado = $this->__generarContenedorSinDatos();
				return;
			}

			if(isset($_REQUEST['word'])){
				$T->setVar('__item_orden', $this->extra["item_orden"]);
				$T->setVar('__horario_orden', $orden);
				$T->setVar('__horario_nombre',$horario->nombre);
				$T->parse('bloque_titulo_horarios', 'BLOQUE_TITULO_HORARIOS', false);
			}elseif(count($horarios) > 1) {
				$T->setVar('__item_orden', $this->extra["item_orden"]);
				$T->setVar('__horario_orden', $orden);
				$T->setVar('__horario_nombre',$horario->nombre);
				$T->parse('bloque_titulo_horarios', 'BLOQUE_TITULO_HORARIOS', false);
			}elseif(count($horarios) == 1) {
				$T->setVar('__item_orden', $this->extra["item_orden"]);
				$T->setVar('__horario_orden', $orden);
				$T->setVar('__horario_nombre',$horario->nombre);
				$T->parse('bloque_titulo_horarios', 'BLOQUE_TITULO_HORARIOS', false);
			}
	
			$T->setVar('bloque_eventos_titulos', '');
			foreach ($conf_eventos as $conf_evento) {
				# Para que no muestre los eventos clientes cuando no existen en el periodo.
				if ($marcado == false and $conf_evento->getAttribute("evento_id") == '9'){
					continue;
				}
				$T->setVar('__evento_nombre', $conf_evento->getAttribute("nombre"));
				$T->parse('bloque_eventos_titulos', 'BLOQUE_EVENTOS_TITULOS', true);
			}

			$linea = 1;
			foreach ($xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]") as $conf_paso) {
				$tag_paso = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]")->item(0);
				$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
				$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
				$T->setVar('__pasoNombre', $conf_paso->getAttribute("nombre"));
		
				$acumulado_ti = 0;
				$T->setVar('bloque_eventos', '');
				foreach ($conf_eventos as $conf_evento) {
					# Para que no muestre los datos eventos clientes cuando no existen en el periodo.
					if ($marcado == false and $conf_evento->getAttribute("evento_id") == '9'){
						continue;
					}
					$tag_evento = $xpath->query("estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]", $tag_paso);
					if ($conf_evento->getAttribute("evento_id") != 2) {
						$acumulado_ti += ($tag_evento->length == 0)?"0":$tag_evento->item(0)->getAttribute("porcentaje").'<br>';
					}
					$T->setVar('__evento_valor', ($tag_evento->length == 0)?"0":$tag_evento->item(0)->getAttribute("porcentaje"));
					$T->setVar('__evento_color', ($conf_evento->getAttribute("color") == "f0f0f0")?"b2b2b2":$conf_evento->getAttribute("color"));
					$T->parse('bloque_eventos', 'BLOQUE_EVENTOS', true);
				}
				$T->setVar('__uptime_ti', number_format($acumulado_ti, 2, '.', ''));
				$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
				$linea++;
			}
			$T->parse('bloque_horarios', 'BLOQUE_HORARIOS', true);
			$orden++;
		}
		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));

		$this->resultado = $T->parse('out', 'tpl_tabla');
	}
	
	/*
	Creado por:
	Modificado por: Carlos sepúlveda
	Fecha de creacion:
	Fecha de ultima modificacion: 16-06-2017
	*/
	function getDisponibilidadPonderada() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$event = new Event;
		$graficoSvg = new GraficoSVG();

		# Variables para eventos especiales marcados por el cliente codigo 9.
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		
		$nameFunction =  'disponibilidad_ponderada';
		$tieneEvento = 'false';
		
		$nodos = array();
		$marcado = false;
		$dataMant = null;
		$ids = null;
		
	
		$ponderacion = $usr->getPonderacion();
	
		if ($ponderacion == null) {
			$ponderacion_id = 0;
		}
		else {
			$ponderacion_id = $ponderacion->ponderacion_id;
		}

		$sql = "SELECT * FROM reporte.disponibilidad_resumen_global_ponderado(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($ponderacion_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if($row = $res->fetchRow()){
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["disponibilidad_resumen_global_ponderado"]);
			$xpath = new DOMXpath($dom);
			unset($row["disponibilidad_resumen_global_ponderado"]);
		}

		$conf_pasos = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]");
		$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (!$conf_pasos->length or $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'disponibilidad_resumen.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS_TITULOS', 'bloque_eventos_titulos');
		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS', 'bloque_eventos');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_TITULO_HORARIOS', 'bloque_titulo_horarios');
		$T->setBlock('tpl_tabla', 'BLOQUE_HORARIOS', 'bloque_horarios');
	
		$T->setVar('bloque_titulo_horarios', '');
		$T->setVar('bloque_eventos_titulos', '');
		
		/* Datos de mantenimiento marcado por el usuario*/

		foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
		}	

		foreach ($conf_eventos as $conf_evento) {
			# Para mostrar o no los eventos especials.
			if ($marcado == false and $conf_evento->getAttribute("evento_id") == '9') {
			continue;
			}
			$T->setVar('__evento_nombre', $conf_evento->getAttribute("nombre"));
			$T->parse('bloque_eventos_titulos', 'BLOQUE_EVENTOS_TITULOS', true);
		}
			
		$linea = 1;

		# Captura los nodos involucrados para esa fecha.
		foreach ($xpath->query("/atentus/resultados/propiedades/nodos/nodo") as $key => $tagNodo) {
			array_push($nodos, $tagNodo->getAttribute('nodo_id'));
		}
	
		$nodoId = '0';
		#Verifica que exista marcado.	
		if ($marcado == true) {

			$dataMant = $event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			
			$tieneEvento = 'true';
			$encode = json_encode($dataMant);
			
			$T->setVar('__tiene_evento', $tieneEvento);
			$T->setVar('__name', $nameFunction);
		}
		
		foreach ($conf_pasos as $conf_paso) {
			$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
			$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
			$T->setVar('__pasoNombre', $conf_paso->getAttribute("nombre"));
			
			$path_dato = "//detalles/detalle[@nodo_id=".$nodoId."]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica/@porcentaje";
			$diferencia = $xpath->evaluate("sum(".$path_dato.")") - 100;
			$maximo = $xpath->evaluate($path_dato."[not(. < ".$path_dato.")][1]")->item(0)->value;
			
			$T->setVar('bloque_eventos', '');
			foreach ($conf_eventos as $conf_evento) {
				# Para mostrar o no los eventos especials.
				if ($marcado == false and $conf_evento->getAttribute("evento_id") == '9') {
				continue;
				}
				$tag_dato = $xpath->query("//detalles/detalle[@nodo_id=".$nodoId."]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]")->item(0);

				if ($tag_dato == null) {
					$porcentaje = 0;
				}
				elseif ($diferencia != 0 and $maximo == $tag_dato->getAttribute("porcentaje")) {
					$porcentaje = $tag_dato->getAttribute("porcentaje") - $diferencia;
					$diferencia = 0;
				}
				else {
					$porcentaje = $tag_dato->getAttribute("porcentaje");
				}

				$T->setVar('__evento_valor', number_format($porcentaje, 3, '.', ''));
				$T->setVar('__evento_color', ($conf_evento->getAttribute("color") == "f0f0f0")?"b2b2b2":$conf_evento->getAttribute("color"));
				$T->parse('bloque_eventos', 'BLOQUE_EVENTOS', true);
			}

			$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
			$linea++;
		}
		$T->parse('bloque_horarios', 'BLOQUE_HORARIOS', true);
	
		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		$this->resultado = $T->parse('out', 'tpl_tabla');
		/* Agrega el acordeon cuando existan eventos*/
		if (count($dataMant)>0){
			$this->resultado.= $graficoSvg->getAccordion($encode,$nameFunction);
		}
	}

	/*
	 Creado por:
	Modificado por: Francisco Ormeño
	Fecha de creacion:14-11-2016
	Fecha de ultima modificacion:24-11-2016
	*/
	function getDisponibilidadPonderadaReal() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
	
		$event = new Event;
		$graficoSvg = new GraficoSVG();
		
		# Variables para eventos especiales marcados por el cliente codigo 9.
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId);
		$timeZone = $arrTime[$timeZoneId];
		
		$nameFunction =  'disponibilidad_ponderada_real';
		$tieneEvento = 'false';
		
		$nodos = array();
		$marcado = false;
		$dataMant = null;
		$ids = null;
		
		$ponderacion = $usr->getPonderacion();
		

		if ($ponderacion == null) {
			$ponderacion_id = 0;
		}
		else {
			$ponderacion_id = $ponderacion->ponderacion_id;
		}
		$sql = "SELECT * FROM reporte.disponibilidad_resumen_global_ponderado_real(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($ponderacion_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
// 				print($sql);exit;

		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row["disponibilidad_resumen_global_ponderado_real"]);
		$xpath = new DOMXpath($dom);
	
		$conf_pasos = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]");
		$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento[@evento_id!=7][@evento_id!=9]");
	
		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (!$conf_pasos->length or $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'disponibilidad_resumen.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS_TITULOS', 'bloque_eventos_titulos');
		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS', 'bloque_eventos');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_TITULO_HORARIOS', 'bloque_titulo_horarios');
		$T->setBlock('tpl_tabla', 'BLOQUE_HORARIOS', 'bloque_horarios');
	
		$T->setVar('bloque_titulo_horarios', '');
		$T->setVar('bloque_eventos_titulos', '');
		
		/* Datos de mantenimiento marcado por el usuario*/
		
		foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
		    $ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
		    $marcado = true;
		}	
		
		foreach ($conf_eventos as $conf_evento) {
				$T->setVar('__evento_nombre', $conf_evento->getAttribute("nombre"));
				$T->parse('bloque_eventos_titulos', 'BLOQUE_EVENTOS_TITULOS', true);
		}

		#Verifica que exista marcado.
		if ($marcado == true) {
		    
		    $dataMant = $event->getData(substr($ids, 1), $timeZone);
		    $character = array("{", "}");
		    $objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
		    
		    $tieneEvento = 'true';
		    $encode = json_encode($dataMant);
		    
		    $T->setVar('__tiene_evento', $tieneEvento);
		    $T->setVar('__name', $nameFunction);
		}

		$linea = 1;
		foreach ($conf_pasos as $conf_paso) {
			$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
			$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
			$T->setVar('__pasoNombre', $conf_paso->getAttribute("nombre"));

			$path_dato = "//detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica/@porcentaje";
			$diferencia = $xpath->evaluate("sum(".$path_dato.")") - 100;
			$maximo = $xpath->evaluate($path_dato."[not(. < ".$path_dato.")][1]")->item(0)->value;

			$T->setVar('bloque_eventos', '');
			foreach ($conf_eventos as $conf_evento) {
				$tag_dato = $xpath->query("//detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]")->item(0);
				if ($tag_dato == null) {
					$porcentaje = 0;
				}
				elseif ($diferencia != 0 and $maximo == $tag_dato->getAttribute("porcentaje")) {
					$porcentaje = $tag_dato->getAttribute("porcentaje") - $diferencia;
					$diferencia = 0;
				}
				else {
					$porcentaje = $tag_dato->getAttribute("porcentaje");
				}

				$T->setVar('__evento_valor', number_format($porcentaje, 3, '.', ''));
				$T->setVar('__evento_color', ($conf_evento->getAttribute("color") == "f0f0f0")?"b2b2b2":$conf_evento->getAttribute("color"));
				$T->parse('bloque_eventos', 'BLOQUE_EVENTOS', true);
			}
	
			$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
			$linea++;
	
		}
		$T->parse('bloque_horarios', 'BLOQUE_HORARIOS', true);
	
		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		$this->resultado = $T->parse('out', 'tpl_tabla');
		/* Agrega el acordeon cuando existan eventos*/
		if (count($dataMant)>0){
		    $this->resultado.= $graficoSvg->getAccordion($encode,$nameFunction);
		}
	}
	
	/*
	Creado por:
	Modificado por: Carlos Sepúlveda.
	Fecha de creacion:
	Fecha de ultima modificacion: 14-07-2017
	*/
	function getDisponibilidadPonderadaPorItem() {
  		global $mdb2;
  		global $log;
  		global $current_usuario_id;
  		global $usr;

		//DATO PARA LA CONSULTA SQL, LISTA LAS HORAS DEL DÍA
		//SI ES 0 TRAE LAS 24 HORAS
		$ponderacion = $usr->getPonderacion();

		$event = new Event;
		$graficoSvg = new GraficoSVG();

		# Variables para eventos especiales marcados por el cliente codigo 9.
		$timeZoneId = $usr->zona_horaria_id;			
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		
		$nameFunction =  'disponibilidad_ponderada_real';
		$tieneEvento = 'false';
		
		$marcado = false;	
		$dataMant = null;
		$data = null;
		$ids= null;

		if ($ponderacion == null) {
			$ponderacion_id = 0;
		}
		else {
			$ponderacion_id = $ponderacion->ponderacion_id;
		}

  		$sql = "SELECT * FROM reporte.disponibilidad_resumen_global_ponderado_poritem(".
  				pg_escape_string($current_usuario_id).",".
  				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($ponderacion_id).",' ".
  				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
  				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
  		$res = & $mdb2->query($sql);
  		if (MDB2::isError($res)) {
 			$log->setError($sql, $res->userinfo);
 			exit();
 		}

 		if($row = $res->fetchRow()){
 			$dom = new DomDocument();
 			$dom->preserveWhiteSpace = FALSE;
  			$dom->loadXML($row['disponibilidad_resumen_global_ponderado_poritem']);
  			$xpath = new DOMXpath($dom);
  			unset($row["disponibilidad_resumen_global_ponderado_poritem"]);
		}

		# Busca si exiten marcados y los almacena.
		foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
		}

		# Verifica y asigna variables en caso de que exista marcado.
		if ($marcado == true) {

			$dataMant =$event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			$tieneEvento = 'true';
			$data = json_encode($dataMant);
			$tieneEvento = 'true';
		}
		
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
  		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
  		$conf_ponderaciones = $xpath->query("/atentus/resultados/propiedades/ponderaciones/item");
  		$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
		$count_pasos = $conf_pasos->length;

		//SI NO HAY DATOS MOSTRAR MENSAJE
    	if (!$conf_pasos->length or $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length == 0) {
  			$this->resultado = $this->__generarContenedorSinDatos();
  			return;
  		}

  		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
  		$T->setFile('tpl_tabla', 'especial_disponibilidad_ponderada_por_item.tpl');
  		$T->setBlock('tpl_tabla', 'LISTA_PASOS_TITULO', 'lista_pasos_titulo');
 		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS', 'bloque_eventos');
 		$T->setBlock('tpl_tabla', 'LISTA_ITEMS', 'lista_items');
 		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS_TITULOS', 'bloque_eventos_titulos');
 		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS_TOTAL', 'bloque_eventos_total');
 		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
 		$T->setVar('__paso_id_default', $conf_pasos->item(0)->getAttribute('paso_orden'));
  		$T->setVar('__item_orden', $this->extra["item_orden"]);

  		$T->setVar('__tiene_evento', $tieneEvento);
		$T->setVar('__name', $nameFunction);

  		$orden = 1;
  		foreach ($conf_pasos as $conf_paso) {
  			$tag_paso = $xpath->query("//detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]")->item(0);
  			$T->setVar('__paso_id', $conf_paso->getAttribute('paso_orden'));
 			$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
 			$T->setVar('__paso_orden', $orden);
 			$T->parse('lista_pasos_titulo', 'LISTA_PASOS_TITULO', true);

  			$linea = 1;
  			$T->setVar('lista_items', '');
			$array_porcentaje = array();
  			foreach ($conf_ponderaciones as $conf_ponderacion) {

  				if ($conf_ponderacion->getAttribute('valor') == 0) {
  					continue;
  				}

  				$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
  				$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
  				$T->setVar('__item_inicio', $conf_ponderacion->getAttribute('inicio'));
  				$T->setVar('__item_termino', $conf_ponderacion->getAttribute('termino'));

  				$valor=$conf_ponderacion->getAttribute('valor');
  				$T->setVar('__item_valor', number_format($valor, 2, '.', ''));

  				$T->setVar('bloque_eventos', '');
  				foreach ($conf_eventos as $conf_evento) {
  					$tag_dato_item = $xpath->query("detalles/detalle[@item_id=".$conf_ponderacion->getAttribute('item_id')."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]", $tag_paso)->item(0);

  					# Para mostrar o no los eventos especials.

  					if ($marcado == false and $conf_evento->getAttribute("evento_id") == '9') {
  						continue;
  					}
 					// SE REGISTRAN LOS VALORES POR ITEM.
 					if ($tag_dato_item == null) {
 						$porcentaje = 0;
  					}else {
  						$porcentaje = $tag_dato_item->getAttribute("porcentaje");
  					}
  					//Cálculo de ponderación
					$array_porcentaje[$conf_evento->getAttribute("evento_id")] += $porcentaje* ($valor/100);

    				$T->setVar('__evento_valor', number_format($porcentaje, 2, '.', ''));
  					$T->setVar('__evento_color', ($conf_evento->getAttribute("color") == "f0f0f0")?"b2b2b2":$conf_evento->getAttribute("color"));

  					$T->parse('bloque_eventos', 'BLOQUE_EVENTOS', true);
				}
  				$T->parse('lista_items', 'LISTA_ITEMS', true);
  				$linea++;
  			}

  			$T->setVar('bloque_eventos_titulos', '');
  			$T->setVar('bloque_eventos_total', '');
  			foreach ($conf_eventos as $conf_evento) {
  				# Para mostrar o no los eventos especiales.
  				if ($marcado == false and $conf_evento->getAttribute("evento_id") == '9') {
					continue;
				}
   				$T->setVar('__evento_nombre', $conf_evento->getAttribute("nombre"));
   				//Se muestran los porcentajes por total de cada paso
				$T->setVar('__evento_total', number_format($array_porcentaje[$conf_evento->getAttribute("evento_id")], 2, ',', ''));
  				$T->parse('bloque_eventos_titulos', 'BLOQUE_EVENTOS_TITULOS', true);
  				$T->parse('bloque_eventos_total', 'BLOQUE_EVENTOS_TOTAL', true);
  			}
  			$T->parse('lista_pasos', 'LISTA_PASOS', true);
  			$orden++;
  		}
 		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
  		$this->resultado = $T->parse('out', 'tpl_tabla');
  		if (count($dataMant)>0){
			$this->resultado.= $graficoSvg->getAccordion($data,$nameFunction);
		}
  	}

  	function getDisponibilidadDowntimeEspecial($objetivo_id) {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $dias_semana;
		global $usr;

		$event = new Event;
		$graficoSvg = new GraficoSVG();
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		$nameFunction = 'disponibilidad_downtime';
		$tieneEvento = 'false';
		$marcado = false;		
		$dataMant = null;
		$ids = null;
		
		$sql = "SELECT * FROM reporte.disponibilidad_downtime_global(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['disponibilidad_downtime_global']);
			$xpath = new DOMXpath($dom);
			unset($row["disponibilidad_downtime_global"]);
		}

		$conf_pasos = $xpath->query("//objetivos/objetivo | //objetivos/objetivo/paso[@visible=1]");

		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'disponibilidad_downtime_global_especial.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_DOWNTIME', 'bloque_downtime');
		$T->setBlock('tpl_tabla', 'BLOQUE_UPTIME', 'bloque_uptime');
		$T->setBlock('tpl_tabla', 'LISTA_DIAS', 'lista_dias');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');

		$T->setVar('__item_orden', $this->extra["item_orden"]);
		$T->setVar('lista_pasos', '');

		foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
		}

		if ($marcado == true) {
			$T->setBlock('tpl_tabla', 'BLOQUE_MANTENIMIENTO', 'bloque_mantenimiento');
			$dataMant =$event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			$tieneEvento = 'true';
			$encode = json_encode($dataMant);
		}
		$T->setVar('__tiene_evento', $tieneEvento);
		$T->setVar('__name', $nameFunction);

		if (!is_null($dataMant)){
			$class_man = 0;
			foreach ($dataMant as $key => $value) {
				$datetimeI = date_create($value['fecha_inicio']);
				$datetimeT = date_create($value['fecha_termino']);
				$interval = date_diff($datetimeI, $datetimeT);
				$duracion =  $interval->format('%d días %h horas %i minutos %s Segundos');
				$T->setVar('__fecha_inicio_evento', $value['fecha_inicio']);
				$T->setVar('__fecha_termino_evento', $value['fecha_termino']);
				$T->setVar('__duracion_evento', $duracion);
				$T->setVar('__tipo_evento', 'Marcado Especial');
				$T->setVar('__class', ($class_man % 2 == 0)?"txtGris12 celdaIteracion1":"txtGris12 celdaIteracion2");
				$class_man++;
				$T->parse('bloque_mantenimiento', 'BLOQUE_MANTENIMIENTO', true);			
			}
		}		
		
		foreach ($conf_pasos as $id => $conf_paso) {
			if ($conf_paso->getAttribute('paso_orden') != null) {
				continue;
			}

			$fecha_inicio = strtotime($this->timestamp->fecha_inicio);
			$fecha_termino = strtotime($this->timestamp->fecha_termino);
			$total_nomonitoreo = 0;
			$total_downtime = 0;
			$linea = 1;

			$T->setVar('lista_dias', '');
			while ($fecha_inicio < $fecha_termino) {
				$T->setVar('bloque_downtime', '');
				$T->setVar('bloque_uptime', '');

				$T->setVar('__fecha', date("d-m-Y", $fecha_inicio));

				$tag_datos = $xpath->query("//detalle[@objetivo_id]/datos/dato[contains(@inicio, '".date("Y-m-d", $fecha_inicio)."')]");

				if ($tag_datos->length == 0) {
					$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
					$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
					$T->parse('bloque_uptime', 'BLOQUE_UPTIME', false);
					$linea++;
				}
				else {
					foreach ($tag_datos as $tag_dato) {
						if ($tag_dato->getAttribute('evento_id') != 2) {
							continue;
						}
						
						$total_nomonitoreo += ($tag_dato->getAttribute('evento_id') == 7)?(strtotime($tag_dato->getAttribute('duracion')) - strtotime('00:00:00')):0;
						$total_downtime += ($tag_dato->getAttribute('evento_id') == 2)?(strtotime($tag_dato->getAttribute('duracion')) - strtotime('00:00:00')):0;
						$hora_fin = date("H:i:s", strtotime($tag_dato->getAttribute('termino')));
						$conf_evento = $xpath->query("//eventos/evento[@evento_id=".$tag_dato->getAttribute('evento_id')."]")->item(0);

						$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
						$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
						$T->setVar('__duracion', ($tag_dato->getAttribute('duracion') == "1 day")?"24:00:00":$tag_dato->getAttribute('duracion'));
						$T->setVar('__tipo', ($conf_paso->getAttribute('paso_orden') == null and $tag_dato->getAttribute('evento_id') == 2)?'Downtime Consolidado':$conf_evento->getAttribute('nombre'));
						$T->setVar('__horaInicio', date("H:i:s", strtotime($tag_dato->getAttribute('inicio'))));
						$T->setVar('__horaTermino', ($hora_fin == "00:00:00")?"24:00:00":$hora_fin);
						$T->parse('bloque_downtime', 'BLOQUE_DOWNTIME', true);
						$linea++;
					}
				}
				$T->parse('lista_dias', 'LISTA_DIAS', true);
				$fecha_inicio = $fecha_inicio + 86400;
			}
			//echo $total_nomonitoreo.'<br>';
			$dias_nomonitoreo = floor($total_nomonitoreo / 86400);
			$dias_downtime = floor($total_downtime / 86400);
			$T->setVar('__no_monitoreo_acumulado', (($dias_nomonitoreo > 0)?$dias_nomonitoreo." dia(s) ":"").date("H:i:s", $total_nomonitoreo - ($dias_nomonitoreo * 86400)));
			$T->setVar('__downtime_acumulado', (($dias_downtime > 0)?$dias_downtime." dia(s) ":"").date("H:i:s", $total_downtime - ($dias_downtime * 86400)));
			$T->parse('lista_pasos', 'LISTA_PASOS', true);
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));

		return $T->parse('out', 'tpl_tabla');
		# Agrega el acordeon cuando existan eventos.
		if (count($dataMant)>0){
			$this->resultado.= $graficoSvg->getAccordion($encode,$nameFunction);
		}	
	}
	
	/*
	Creado por:
	Modificado por: Carlos Sepúlveda
	Fecha de creacion:-
	Fecha de ultima modificacion:16-06-2017
	*/
	// TODO: Revisar la generacion de los string de las fechas.
	function getDisponibilidadDowntime() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $dias_semana;
		global $usr;

		$event = new Event;
		$graficoSvg = new GraficoSVG();		

		# Variables para eventos especiales marcados por el cliente codigo 9.
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		$nameFunction = 'disponibilidad_downtime';
		$tieneEvento = 'false';
		$marcado = false;		
		$dataMant = null;
		$ids = null;
		
		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.disponibilidad_downtime_global(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
 		//print($sql);
		$res =& $mdb2->query($sql);

		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['disponibilidad_downtime_global']);
			$xpath = new DOMXpath($dom);
			unset($row["disponibilidad_downtime_global"]);
		}

		$conf_pasos = $xpath->query("//objetivos/objetivo | //objetivos/objetivo/paso[@visible=1]");

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'disponibilidad_downtime_global.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_NOMBRE', 'bloque_nombre');
		$T->setBlock('tpl_tabla', 'BLOQUE_DOWNTIME', 'bloque_downtime');
		$T->setBlock('tpl_tabla', 'BLOQUE_UPTIME', 'bloque_uptime');
		$T->setBlock('tpl_tabla', 'LISTA_DIAS', 'lista_dias');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');

		$T->setVar('__item_orden', $this->extra["item_orden"]);
		$T->setVar('bloque_nombre', '');
		$T->setVar('lista_pasos', '');

		
		/* Obtiene el tag marcado del xml.*/
		foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
		}
		/* Define y asigna ciertos parametros a ocupar */
		if ($marcado == true) {
			$T->setBlock('tpl_tabla', 'BLOQUE_MANTENIMIENTO', 'bloque_mantenimiento');
			$dataMant =$event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			$tieneEvento = 'true';
			$encode = json_encode($dataMant);
		}
		$T->setVar('__tiene_evento', $tieneEvento);
		$T->setVar('__name', $nameFunction);
		# Agrega los eventos creados por el usuarios.
		if (!is_null($dataMant)){
			/* Envia cada dato a utilizar al template (por bloque.)*/
			foreach ($dataMant as $key => $value) {
				$datetimeI = date_create($value['fecha_inicio']);
				$datetimeT = date_create($value['fecha_termino']);
				$interval = date_diff($datetimeI, $datetimeT);
				$duracion =  $interval->format('%d días %h horas %i minutos %s Segundos');
				$T->setVar('__fecha_inicio_evento', $value['fecha_inicio']);
				$T->setVar('__fecha_termino_evento', $value['fecha_termino']);
				$T->setVar('__duracion_evento', $duracion);
				$T->setVar('__tipo_evento', 'Marcado Especial');
				$T->parse('bloque_mantenimiento', 'BLOQUE_MANTENIMIENTO', true);			
			}
		}		
		
		foreach ($conf_pasos as $id => $conf_paso) {
			$T->setVar('__paso_orden', $id + 1);
			$T->setVar('__paso_id', ($conf_paso->getAttribute('paso_orden') != null)?$conf_paso->getAttribute('paso_orden'):'100000');
			$T->setVar('__paso_nombre', ($conf_paso->getAttribute('paso_orden') != null)?$conf_paso->getAttribute('nombre'):'Consolidado');
			$T->parse('bloque_nombre', 'BLOQUE_NOMBRE', true);

			$fecha_inicio = strtotime($this->timestamp->fecha_inicio);
			$fecha_termino = strtotime($this->timestamp->fecha_termino);
			$total_nomonitoreo = 0;
			$total_downtime = 0;
			$linea = 1;

			$T->setVar('lista_dias', '');
			while ($fecha_inicio <= $fecha_termino) {
				$T->setVar('bloque_downtime', '');
				$T->setVar('bloque_uptime', '');

				$T->setVar('__fecha', date("d-m-Y", $fecha_inicio));

				$tag_datos = $xpath->query("//detalle[".(( $conf_paso->getAttribute('paso_orden')  >= "0")?"@paso_orden=".$conf_paso->getAttribute('paso_orden'):"@objetivo_id")."]/datos/dato[contains(@inicio, '".date("Y-m-d", $fecha_inicio)."')]");

				if ($tag_datos->length == 0) {
					$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
					$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
					$T->parse('bloque_uptime', 'BLOQUE_UPTIME', false);
					$linea++;
				}
				else {
					foreach ($tag_datos as $tag_dato) {
						$newValue=$tag_dato->getAttribute('duracion');
						if($tag_dato->getAttribute('duracion')=="1 day"){
							$newValue='24:00:00';
						}
						$total_nomonitoreo += ($tag_dato->getAttribute('evento_id') == 7)?(strtotime($newValue) - strtotime('00:00:00')):0;
						$total_downtime += ($tag_dato->getAttribute('evento_id') == 2)?(strtotime($newValue) - strtotime('00:00:00')):0;
						$hora_fin = date("H:i:s", strtotime($tag_dato->getAttribute('termino')));
						$conf_evento = $xpath->query("//eventos/evento[@evento_id=".$tag_dato->getAttribute('evento_id')."]")->item(0);

						$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
						$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
						$T->setVar('__duracion', ($tag_dato->getAttribute('duracion') == "1 day")?"24:00:00":$tag_dato->getAttribute('duracion'));
						$T->setVar('__tipo', ($conf_paso->getAttribute('paso_orden') == null and $tag_dato->getAttribute('evento_id') == 2)?'Downtime Consolidado':$conf_evento->getAttribute('nombre'));
						$T->setVar('__horaInicio', date("H:i:s", strtotime($tag_dato->getAttribute('inicio'))));
						$T->setVar('__horaTermino', ($hora_fin == "00:00:00")?"24:00:00":$hora_fin);
						$T->parse('bloque_downtime', 'BLOQUE_DOWNTIME', true);
						$linea++;
					}
				}
				$T->parse('lista_dias', 'LISTA_DIAS', true);
				$fecha_inicio = $fecha_inicio + 86400;
			}

			$dias_nomonitoreo = floor($total_nomonitoreo / 86400);
			$dias_downtime = floor($total_downtime / 86400);
			$T->setVar('__no_monitoreo_acumulado', (($dias_nomonitoreo > 0)?$dias_nomonitoreo." dia(s) ":"").date("H:i:s", $total_nomonitoreo - ($dias_nomonitoreo * 86400)));
			$T->setVar('__downtime_acumulado', (($dias_downtime > 0)?$dias_downtime." dia(s) ":"").date("H:i:s", $total_downtime - ($dias_downtime * 86400)));
			$T->parse('lista_pasos', 'LISTA_PASOS', true);
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));

		$this->resultado = $T->parse('out', 'tpl_tabla');
		# Agrega el acordeon cuando existan eventos.
		if (count($dataMant)>0){
			$this->resultado.= $graficoSvg->getAccordion($encode,$nameFunction);
		}	
	}
	
	
	/*************** FUNCIONES DE TABLAS DE RENDIMIENTO ***************/
	/*************** FUNCIONES DE TABLAS DE RENDIMIENTO ***************/
	/*************** FUNCIONES DE TABLAS DE RENDIMIENTO ***************/
	
	/**
	 * Funcion para obtener la tabla de
	 * Resumen de Rendimiento.
	 */
	// TODO: metodo getRendimientoResumen()
	function getEstadisticaResumen() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'rendimiento_resumen.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_TITULO_HORARIOS', 'bloque_titulo_horarios');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_TABLA', 'bloque_tabla');

		$horarios = array($usr->getHorario($this->horario_id));
		if ($this->horario_id != 0) {
			$horarios[] = $usr->getHorario(0);
			$horarios[($this->horario_id*-1)] = new Horario(($this->horario_id*-1));
			$horarios[($this->horario_id*-1)]->nombre = "Horario Inhabil";
		}

		$orden = 1;
		$T->setVar('bloque_titulo_horarios', '');
		foreach ($horarios as $horario) {
			$T->setVar('lista_pasos', '');

			$sql = "SELECT * FROM reporte.rendimiento_resumen_global(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					pg_escape_string($horario->horario_id).",' ".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//					print $sql;
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = false;
			$dom->loadXML($row["rendimiento_resumen_global"]);
			$xpath = new DOMXpath($dom);
			unset($row["rendimiento_resumen_global"]);

			$conf_objetivo = $xpath->query('/atentus/resultados/propiedades/objetivos/objetivo')->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);

			/* SI NO HAY DATOS MOSTRAR MENSAJE */
			if ($horario->horario_id == "0" and $xpath->query('//detalle[@paso_orden]/datos/dato')->length == 0) {
				$this->resultado = $this->__generarContenedorSinDatos();
				return;
			}

			if(count($horarios) > 1) {
				$T->setVar('__item_orden', $this->extra["item_orden"]);
				$T->setVar('__horario_orden', $orden);
				$T->setVar('__horario_nombre', $horario->nombre);
				$T->parse('bloque_titulo_horarios', 'BLOQUE_TITULO_HORARIOS', false);
			}

			/* DATOS DE LA TABLA */
			$linea = 1;
			foreach($conf_pasos as $conf_paso) {
				$tag_dato = $xpath->query('//detalle[@paso_orden='.$conf_paso->getAttribute('paso_orden').']/datos/dato')->item(0);
				if ($tag_dato == null) {
					continue;
				}
				$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
				$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
				$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
				$T->setVar('__paso_minimo', number_format($tag_dato->getAttribute('tiempo_min'), 3, ',', ''));
				$T->setVar('__paso_maximo', number_format($tag_dato->getAttribute('tiempo_max'), 3, ',', ''));
				$T->setVar('__paso_promedio', number_format($tag_dato->getAttribute('tiempo_prom'), 3, ',', ''));
				$T->parse('lista_pasos', 'LISTA_PASOS', true);
				$linea++;
			}
			$T->parse('bloque_tabla', 'BLOQUE_TABLA', true);
			$orden++;
		}
		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		$this->resultado =$T->parse('out', 'tpl_tabla');
	}

	/**
	 * Funcion para obtener la tabla de
	 * Resumen de Rendimiento por Dia de la Semana.
	 */
	// TODO: metodo getRendimientoPorDia
	function getEstadisticaPorDia() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $dias_semana;
		global $usr;

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'rendimiento_por_dia.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_TITULO_HORARIOS', 'bloque_titulo_horarios');
		$T->setBlock('tpl_tabla', 'ES_PRIMERO_DIA', 'es_primero_dia');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_TABLA', 'bloque_tabla');


		$T->setVar('__item_orden', $this->extra["item_orden"]);

		$horarios = array($usr->getHorario($this->horario_id));
		if ($this->horario_id != 0) {
			$horarios[] = $usr->getHorario(0);
		}

		$orden = 1;
		$T->setVar('bloque_titulo_horarios', '');
		foreach ($horarios as $horario) {

			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT * FROM reporte.rendimiento_resumen_global_pordiasemana(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					pg_escape_string($horario->horario_id).",' ".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = false;
			$dom->loadXML($row["rendimiento_resumen_global_pordiasemana"]);
			$xpath = new DOMXpath($dom);
			unset($row["rendimiento_resumen_global_pordiasemana"]);

			/* SI NO HAY DATOS MOSTRAR MENSAJE */
			if ($xpath->query('//detalle[@paso_orden]/estadisticas/estadistica')->length == 0) {
				$this->resultado = $this->__generarContenedorSinDatos();
				return;
			}

			$conf_objetivo = $xpath->query('/atentus/resultados/propiedades/objetivos/objetivo')->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);

			if(count($horarios) > 1) {
				$T->setVar('__item_horario', $this->extra["item_orden"]);
				$T->setVar('__horario_orden', $orden);
				$T->setVar('__horario_nombre',$horario->nombre);
				$T->parse('bloque_titulo_horarios', 'BLOQUE_TITULO_HORARIOS', false);
			}

			$T->setVar('__objetivo_nombre', $conf_objetivo->getAttribute('nombre'));

			$linea = 1;
			$T->setVar('lista_pasos', '');
			foreach($dias_semana as $dia_id => $dia_nombre){
				$primero = true;

				/* LISTA DE PASOS */
				foreach ($conf_pasos as $conf_paso) {
					$tag_dato = $xpath->query('//detalle[@dia_id='.(($dia_id == 7)?0:$dia_id).']/detalles/detalle[@paso_orden='.$conf_paso->getAttribute('paso_orden').']/estadisticas/estadistica')->item(0);

					$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
					$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");

					$T->setVar('es_primero_dia', '');
					if ($primero) {
						$T->setVar('__dia_nombre', $dia_nombre);
						$T->setVar('__dia_rowspan', $conf_pasos->length);
						$T->parse('es_primero_dia', 'ES_PRIMERO_DIA', false);
					}
					$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
					$T->setVar('__paso_minimo', number_format(($tag_dato == null)?0:$tag_dato->getAttribute('tiempo_min'), 3, ',', ''));
					$T->setVar('__paso_maximo', number_format(($tag_dato == null)?0:$tag_dato->getAttribute('tiempo_max'), 3, ',', ''));
					$T->setVar('__paso_promedio', number_format(($tag_dato == null)?0:$tag_dato->getAttribute('tiempo_prom'), 3, ',', ''));
					$T->parse('lista_pasos', 'LISTA_PASOS', true);
					$primero = false;
					$linea++;
				}
			}
			$T->parse('bloque_tabla', 'BLOQUE_TABLA', true);
			$orden++;
		}
		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	/*
	Creado por: Santiago Sepulveda
	Modificado por:
	Fecha de creacion:18-01-2017
	Fecha de ultima modificacion:
	*/
	function getEspecialRendimientoPonderado(){
  		global $mdb2;
  		global $log;
  		global $current_usuario_id;
  		global $usr;

		//TRAE LAS PONDERACIONES DEL OBJETIVO ESPECIAL
  		$ponderacion_hora = $this->extra['ponderaciones'];

		//DATO PARA LA CONSULTA SQL, LISTA LAS HORAS DEL DÍA
		//SI ES 0 TRAE LAS 24 HORAS
		$ponderacion = $usr->getPonderacion();

		if ($ponderacion == null) {
			$ponderacion_id = 0;
		}
		else {
			$ponderacion_id = $ponderacion->ponderacion_id;
		}

  		$sql = "SELECT * FROM reporte.rendimiento_resumen_global_ponderado(".
  				pg_escape_string($current_usuario_id).", ".
  				pg_escape_string($this->objetivo_id).", ".
  				pg_escape_string($ponderacion_id).",' ".
  				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
  				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

		//echo '<1>'.$sql.'<br>';

  		$res =& $mdb2->query($sql);
  		if (MDB2::isError($res)) {
 			$log->setError($sql, $res->userinfo);
 			exit();
 		}

 		if($row = $res->fetchRow()){
 			$dom = new DomDocument();
  			$dom->preserveWhiteSpace = FALSE;
  			$dom->loadXML($row['rendimiento_resumen_global_ponderado']);
  			$xpath = new DOMXpath($dom);
   			unset($row["rendimiento_resumen_global_ponderado"]);
  		}

  		$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
  		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
  		$conf_ponderaciones = $xpath->query("/atentus/resultados/propiedades/ponderaciones/item");
		$conf_min_max = $xpath->query("/atentus/resultados/propiedades/eventos/evento");

		//SE MUESTRA TEMPLATE SIN DATOS
  		if ($xpath->query("//detalles/detalle/detalles/detalle")->length == 0) {
  			$this->resultado = $this->__generarContenedorSinDatos();
  			return;
  		}

		//VALIDA QUE NO HAYA MAS DEL 100% DE PONDERACIONES
		foreach ($ponderacion_hora as $value) {
			$suma_pond += $value->valor_ponderacion;
		}

		if (number_format($suma_pond, 2) > 100) {
			$this->resultado = 'EL TOTAL DE LAS PONDERACIONES DEBE SER IGUAL A 100%.<br>ES SUPERIROR AL 100%.';
			return;
		}elseif (number_format($suma_pond, 2)  < 100) {
			$this->resultado = 'EL TOTAL DE LAS PONDERACIONES DEBE SER IGUAL A 100%.<br>ES MENOR AL 100%.';
			return;
  		}

		//TEMPLATE DEL REPORTE
  		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_rendimiento_ponderado.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS_TITULO', 'lista_pasos_titulo');
		$T->setBlock('tpl_tabla', 'LISTA_ITEMS', 'lista_items');
		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS_TITULOS', 'bloque_eventos_titulos');
		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS_TOTAL', 'bloque_eventos_total');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');


		$T->setVar('__paso_id_default', $conf_pasos->item(0)->getAttribute('paso_orden'));
 		$T->setVar('__item_orden', $this->extra["item_orden"]);
		$orden = 1;
		foreach ($conf_pasos as $conf_paso) {
			$T->setVar('__paso_id', $conf_paso->getAttribute('paso_orden'));
			$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
			$T->setVar('__paso_orden', $orden);

			$total_min_ponderado = 0;
			$total_max_ponderado = 0;
			$total_prom_ponderado = 0;

			$T->setVar('lista_items', '');
			foreach ($conf_ponderaciones as $conf_ponderacion) {
				$valor=$ponderacion_hora[$conf_ponderacion->getAttribute('inicio')];
				if ($valor->valor_ponderacion == 0) {
					continue;
				}
				$tag_dato = $xpath->query("//detalles/detalle/detalles/detalle[@item_id=".$conf_ponderacion->getAttribute("item_id")."]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/datos/dato")->item(0);

  				$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
  				$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
				$T->setVar('__item_inicio', $conf_ponderacion->getAttribute('inicio'));
				$T->setVar('__item_termino', $conf_ponderacion->getAttribute('termino'));

				$T->setVar('__paso_minimo', ($tag_dato == null)?'S/I':number_format($tag_dato->getAttribute('tiempo_min'), 2, ',', ''));
				$T->setVar('__paso_maximo', ($tag_dato == null)?'S/I':number_format($tag_dato->getAttribute('tiempo_max'), 2, ',', ''));
				$T->setVar('__paso_promedio', ($tag_dato == null)?'S/I':number_format($tag_dato->getAttribute('tiempo_prom'), 2, ',', ''));

				if ($tag_dato == null) {
					$tiempo_minimo = 0;
					$tiempo_maximo = 0;
					$tiempo_promedio = 0;
				}else{
					$tiempo_minimo = $tag_dato->getAttribute('tiempo_min');
					$tiempo_maximo = $tag_dato->getAttribute('tiempo_max');
					$tiempo_promedio = $tag_dato->getAttribute('tiempo_prom');
				}

				//SE CALCULA EL PONDERADO
				$total_min_ponderado = (($valor->valor_ponderacion/100) * $tiempo_minimo) + $total_min_ponderado;
				$total_max_ponderado = (($valor->valor_ponderacion/100) * $tiempo_maximo) + $total_max_ponderado;
				$total_prom_ponderado = (($valor->valor_ponderacion/100) * $tiempo_promedio) + $total_prom_ponderado;

				//SE ASIGNA VALOR DE LA PONDERACIÓN
 			$T->setVar('__item_valor', number_format($valor->valor_ponderacion, 2, '.', ''));

				$T->parse('lista_items', 'LISTA_ITEMS', true);
  				$linea++;
  			}

			$T->setVar('__min_total', number_format($total_min_ponderado, 2, ',', ''));
			$T->setVar('__max_total', number_format($total_max_ponderado, 2, ',', ''));
			$T->setVar('__prom_total', number_format($total_prom_ponderado, 2, ',', ''));

			$T->parse('lista_pasos', 'LISTA_PASOS', true);
  			$orden++;
  		}

  		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
  		$this->resultado =$T->parse('out', 'tpl_tabla');
  	}

	/*
	Creado por: Santiago Sepulveda
	Modificado por:
	Fecha de creacion:18-01-2017
	Fecha de ultima modificacion:
	*/
	function getEspecialDisponibilidadPonderadaPorItem() {
  		global $mdb2;
  		global $log;
  		global $current_usuario_id;
  		global $usr;

		//TRAE LAS PONDERACIONES DEL OBJETIVO ESPECIAL
		$ponderaciones_hora = $this->extra['ponderaciones'];
		//DATO PARA LA CONSULTA SQL, LISTA LAS HORAS DEL DÍA
		//SI ES 0 TRAE LAS 24 HORAS
		$ponderacion = $usr->getPonderacion();

		if ($ponderacion == null) {
			$ponderacion_id = 0;
		}
		else {
			$ponderacion_id = $ponderacion->ponderacion_id;
		}

  		$sql = "SELECT * FROM reporte.disponibilidad_resumen_global_ponderado_poritem(".
  				pg_escape_string($current_usuario_id).",".
  				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($ponderacion_id).",' ".
  				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
  				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		//echo '<2.1>'.$sql.'<br>';

  		$res = & $mdb2->query($sql);
  		if (MDB2::isError($res)) {
 			$log->setError($sql, $res->userinfo);
 			exit();
 		}

 		if($row = $res->fetchRow()){
 			$dom = new DomDocument();
 			$dom->preserveWhiteSpace = FALSE;
  			$dom->loadXML($row['disponibilidad_resumen_global_ponderado_poritem']);
  			$xpath = new DOMXpath($dom);
  			unset($row["disponibilidad_resumen_global_ponderado_poritem"]);
		}

  		$sql = "SELECT * FROM reporte.disponibilidad_resumen_global_ponderado(".
  				pg_escape_string($current_usuario_id).",".
  				pg_escape_string($this->objetivo_id).", ".
  				pg_escape_string($ponderacion_id).",' ".
  				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
  				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		//echo '<2.2>'.$sql.'<br>';

  		$res = & $mdb2->query($sql);
  		if (MDB2::isError($res)) {
  			$log->setError($sql, $res->userinfo);
 			exit();
 		}

 		$row = $res->fetchRow();
 		$dom2 = new DomDocument();
 		$dom2->preserveWhiteSpace = FALSE;
 		$dom2->loadXML($row["disponibilidad_resumen_global_ponderado"]);
 		$xpath2 = new DOMXpath($dom2);

 		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
  		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
  		$conf_ponderaciones = $xpath->query("/atentus/resultados/propiedades/ponderaciones/item");
  		$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
		$count_pasos = $conf_pasos->length;

		//SI NO HAY DATOS MOSTRAR MENSAJE
    		if (!$conf_pasos->length or $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length == 0) {
  			$this->resultado = $this->__generarContenedorSinDatos();
  			return;
  		}

		//VALIDA QUE NO HAYA MAS DEL 100% DE PONDERACIONES
		foreach ($ponderaciones_hora as $value) {
			$suma_pond += $value->valor_ponderacion;
		}

		if (number_format($suma_pond, 2) > 100) {
			$this->resultado = 'EL TOTAL DE LAS PONDERACIONES DEBE SER IGUAL A 100%.<br>ES SUPERIROR AL 100%.';
			return;
		}elseif (number_format($suma_pond, 2)  < 100) {
			$this->resultado = 'EL TOTAL DE LAS PONDERACIONES DEBE SER IGUAL A 100%.<br>ES MENOR AL 100%.';
			return;
  		}

  	$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
  	$T->setFile('tpl_tabla', 'especial_disponibilidad_ponderada_por_item.tpl');
  	$T->setBlock('tpl_tabla', 'LISTA_PASOS_TITULO', 'lista_pasos_titulo');
 		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS', 'bloque_eventos');
 		$T->setBlock('tpl_tabla', 'LISTA_ITEMS', 'lista_items');
 		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS_TITULOS', 'bloque_eventos_titulos');
 		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS_TOTAL', 'bloque_eventos_total');
 		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');

 		$T->setVar('__paso_id_default', $conf_pasos->item(0)->getAttribute('paso_orden'));
  		$T->setVar('__item_orden', $this->extra["item_orden"]);

  		$orden = 1;
  		foreach ($conf_pasos as $conf_paso) {
  			$tag_paso = $xpath->query("//detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]")->item(0);

  			$T->setVar('__paso_id', $conf_paso->getAttribute('paso_orden'));
 			$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
 			$T->setVar('__paso_orden', $orden);
 			$T->parse('lista_pasos_titulo', 'LISTA_PASOS_TITULO', true);

  			$linea = 1;
  			$T->setVar('lista_items', '');

			$array_porcentaje = array();
  			foreach ($conf_ponderaciones as $conf_ponderacion) {
  				if ($ponderaciones_hora[$conf_ponderacion->getAttribute('inicio')]->valor_ponderacion == 0) {
  					continue;
  				}

  				$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
  				$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
  				$T->setVar('__item_inicio', $conf_ponderacion->getAttribute('inicio'));
  				$T->setVar('__item_termino', $conf_ponderacion->getAttribute('termino'));

				//Se pasan los valores de ponderacion del xml configuracion del objetivo del reporte especial
  				$valor=$ponderaciones_hora[$conf_ponderacion->getAttribute('inicio')];
  				$T->setVar('__item_valor', number_format($valor->valor_ponderacion, 2, '.', ''));

  				$T->setVar('bloque_eventos', '');
  				foreach ($conf_eventos as $conf_evento) {
  					$tag_dato_item = $xpath->query("detalles/detalle[@item_id=".$conf_ponderacion->getAttribute('item_id')."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]", $tag_paso)->item(0);
  					$tag_dato_total = $xpath2->query("//detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]")->item(0);

 					// SE REGISTRAN LOS VALORES POR ITEM.
 					if ($tag_dato_item == null) {
 						$porcentaje = 0;
  					}else {
  						$porcentaje = $tag_dato_item->getAttribute("porcentaje");
  					};

					$array_porcentaje[$conf_evento->getAttribute("evento_id")] += $porcentaje* ($valor->valor_ponderacion/100);

    				$T->setVar('__evento_valor', number_format($porcentaje, 2, '.', ''));
  					$T->setVar('__evento_color', ($conf_evento->getAttribute("color") == "f0f0f0")?"b2b2b2":$conf_evento->getAttribute("color"));

  					$T->parse('bloque_eventos', 'BLOQUE_EVENTOS', true);

				}
  				$T->parse('lista_items', 'LISTA_ITEMS', true);
  				$linea++;
  			}

  			$T->setVar('bloque_eventos_titulos', '');
  			$T->setVar('bloque_eventos_total', '');

  			foreach ($conf_eventos as $conf_evento) {
  				$tag_dato_total = $xpath2->query("//detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]")->item(0);
				//SE REGISTRAN LOS VALORES TOTALES POR EVENTO.
  				if ($tag_dato_total == null) {
  					$porcentaje = 0;
  				}else {
  					$porcentaje = $tag_dato_total->getAttribute("porcentaje");
  				}
   				$T->setVar('__evento_nombre', $conf_evento->getAttribute("nombre"));
				$T->setVar('__evento_total', number_format($array_porcentaje[$conf_evento->getAttribute("evento_id")], 2, ',', ''));
  				$T->parse('bloque_eventos_titulos', 'BLOQUE_EVENTOS_TITULOS', true);
  				$T->parse('bloque_eventos_total', 'BLOQUE_EVENTOS_TOTAL', true);
  			}

  			$T->parse('lista_pasos', 'LISTA_PASOS', true);
  			$orden++;
  		}

 		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
  		$this->resultado = $T->parse('out', 'tpl_tabla');
  	}

	/*
	Creado por: Santiago Sepulveda
	Modificado por:
	Fecha de creacion:18-01-2017
  	Fecha de ultima modificacion:
	*/
	function getEspecialDisponibilidadPonderada() {
  		global $mdb2;
  		global $log;
  		global $current_usuario_id;
  		global $usr;

		//TRAE LAS PONDERACIONES DEL OBJETIVO ESPECIAL
		$ponderaciones_hora = $this->extra['ponderaciones'];
		//DATO PARA LA CONSULTA SQL, LISTA LAS HORAS DEL DÍA
		//SI ES 0 TRAE LAS 24 HORAS
		$ponderacion = $usr->getPonderacion();

		if ($ponderacion == null) {
			$ponderacion_id = 0;
		}
		else {
			$ponderacion_id = $ponderacion->ponderacion_id;
		}

		$sql = "SELECT * FROM reporte.disponibilidad_resumen_global_ponderado_poritem(".
  				pg_escape_string($current_usuario_id).",".
  				pg_escape_string($this->objetivo_id).", ".
  				pg_escape_string($ponderacion_id).",' ".
  				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
  				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
 		//echo '<3>'.$sql.'<br>';

  		$res = & $mdb2->query($sql);
  		if (MDB2::isError($res)) {
 			$log->setError($sql, $res->userinfo);
 			exit();
 		}

  		if($row = $res->fetchRow()){
  			$dom = new DomDocument();
  			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["disponibilidad_resumen_global_ponderado_poritem"]);
  			$xpath = new DOMXpath($dom);
			unset($row["disponibilidad_resumen_global_ponderado_poritem"]);
  		}


  		$conf_pasos = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]");
  		$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
		$conf_ponderaciones = $xpath->query("/atentus/resultados/propiedades/ponderaciones/item");

  		//SI NO HAY DATOS MOSTRAR MENSAJE
  		if (!$conf_pasos->length or $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length == 0) {
  			$this->resultado = $this->__generarContenedorSinDatos();
  			return;
  		}
		//VALIDA QUE NO HAYA MAS DEL 100% DE PONDERACIONES
		foreach ($ponderaciones_hora as $value) {
			$suma_pond += $value->valor_ponderacion;
		}

		if (number_format($suma_pond, 2) > 100) {
			$this->resultado = 'EL TOTAL DE LAS PONDERACIONES DEBE SER IGUAL A 100%.<br>ES SUPERIROR AL 100%.';
			return;
		}elseif (number_format($suma_pond, 2)  < 100) {
			$this->resultado = 'EL TOTAL DE LAS PONDERACIONES DEBE SER IGUAL A 100%.<br>ES MENOR AL 100%.';
			return;
  		}

  		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
  		$T->setFile('tpl_tabla', 'especial_disponibilidad_resumen.tpl');
 		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS_TITULOS', 'bloque_eventos_titulos');
 		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS', 'bloque_eventos');
 		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
 		$T->setBlock('tpl_tabla', 'BLOQUE_TITULO_HORARIOS', 'bloque_titulo_horarios');
 		$T->setBlock('tpl_tabla', 'BLOQUE_HORARIOS', 'bloque_horarios');

 		$T->setVar('bloque_titulo_horarios', '');
 		$T->setVar('bloque_eventos_titulos', '');
 		foreach ($conf_eventos as $conf_evento) {
 			$T->setVar('__evento_nombre', $conf_evento->getAttribute("nombre"));
 			$T->parse('bloque_eventos_titulos', 'BLOQUE_EVENTOS_TITULOS', true);
 		}

  		$linea = 1;
  		foreach ($conf_pasos as $conf_paso) {
			$array_porcentaje_por_paso = array();
			$tag_paso = $xpath->query("//detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]")->item(0);
  			$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
  			$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
  			$T->setVar('__pasoNombre', $conf_paso->getAttribute("nombre"));

  			$path_dato = "//detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica/@porcentaje";
  			$diferencia = $xpath->evaluate("sum(".$path_dato.")") - 100;
  			$maximo = $xpath->evaluate($path_dato."[not(. < ".$path_dato.")][1]")->item(0)->value;

			foreach ($conf_ponderaciones as $conf_ponderacion) {
				if ($ponderaciones_hora[$conf_ponderacion->getAttribute('inicio')]->valor_ponderacion == 0) {
					continue;
  				}

				$valor=$ponderaciones_hora[$conf_ponderacion->getAttribute('inicio')];

				foreach ($conf_eventos as $conf_evento) {
					$tag_dato_item = $xpath->query("detalles/detalle[@item_id=".$conf_ponderacion->getAttribute('item_id')."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]", $tag_paso)->item(0);

					// SE REGISTRAN LOS VALORES POR ITEM.
					if ($tag_dato_item == null) {
						$porcentaje = 0;
					}else {
						$porcentaje = $tag_dato_item->getAttribute("porcentaje");
					}
					$array_porcentaje_por_paso[$conf_evento->getAttribute("evento_id")] += $porcentaje* ($valor->valor_ponderacion/100);
  				}

			}

			$T->setVar('bloque_eventos', '');
			foreach ($conf_eventos as $conf_evento) {

				$T->setVar('__evento_valor', number_format($array_porcentaje_por_paso[$conf_evento->getAttribute("evento_id")], 2, '.', ''));
  				$T->setVar('__evento_color', ($conf_evento->getAttribute("color") == "f0f0f0")?"b2b2b2":$conf_evento->getAttribute("color"));
  				$T->parse('bloque_eventos', 'BLOQUE_EVENTOS', true);
  			}

 			$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
 			$linea++;

 		}
 		$T->parse('bloque_horarios', 'BLOQUE_HORARIOS', true);

 		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
 		$this->resultado = $T->parse('out', 'tpl_tabla');
 	}


	/*
	Creado por:
	Modificado por: Santiago Sepulveda
	Fecha de creacion:
	Fecha de ultima modificacion: 07-03-2017
	*/
	function getRendimientoPonderado() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		//DATO PARA LA CONSULTA SQL, LISTA LAS HORAS DEL DÍA
		//SI ES 0 TRAE LAS 24 HORAS
		$ponderacion = $usr->getPonderacion();

		if ($ponderacion == null) {
			$ponderacion_id = 0;
		}
		else {
			$ponderacion_id = $ponderacion->ponderacion_id;
		}

		$sql = "SELECT * FROM reporte.rendimiento_resumen_global_ponderado(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($ponderacion_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";


		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if($row = $res->fetchRow()){
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['rendimiento_resumen_global_ponderado']);
			$xpath = new DOMXpath($dom);
			unset($row["rendimiento_resumen_global_ponderado"]);
		}

		$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_ponderaciones = $xpath->query("/atentus/resultados/propiedades/ponderaciones/item");

		//SE MUESTRA TEMPLATE SIN DATOS
		if ($xpath->query("//detalles/detalle/detalles/detalle")->length == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		//TEMPLATE DEL REPORTE
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'rendimiento_ponderado.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_ITEMS', 'lista_items');
		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS_TITULOS', 'bloque_eventos_titulos');
		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS_TOTAL', 'bloque_eventos_total');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');


		$T->setVar('__paso_id_default', $conf_pasos->item(0)->getAttribute('paso_orden'));
		//$T->setVar('__item_orden', $this->extra["item_orden"]);
		$orden = 1;
		foreach ($conf_pasos as $conf_paso) {
			$T->setVar('__paso_id', $conf_paso->getAttribute('paso_orden'));
			$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
			$T->setVar('__paso_orden', $orden);

			$total_min_ponderado = 0;
			$total_max_ponderado = 0;
			$total_prom_ponderado = 0;

			$T->setVar('lista_items', '');
			foreach ($conf_ponderaciones as $conf_ponderacion) {
				if ($conf_ponderacion->getAttribute('valor') == 0) {
					continue;
				}
				$tag_dato = $xpath->query("//detalles/detalle/detalles/detalle[@item_id=".$conf_ponderacion->getAttribute("item_id")."]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/datos/dato")->item(0);

				$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
				$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
				$T->setVar('__item_inicio', $conf_ponderacion->getAttribute('inicio'));
				$T->setVar('__item_termino', $conf_ponderacion->getAttribute('termino'));

				$T->setVar('__paso_minimo', ($tag_dato == null)?'S/I':number_format($tag_dato->getAttribute('tiempo_min'), 2, ',', ''));
				$T->setVar('__paso_maximo', ($tag_dato == null)?'S/I':number_format($tag_dato->getAttribute('tiempo_max'), 2, ',', ''));
				$T->setVar('__paso_promedio', ($tag_dato == null)?'S/I':number_format($tag_dato->getAttribute('tiempo_prom'), 2, ',', ''));

				if ($tag_dato == null) {
					$tiempo_minimo = 0;
					$tiempo_maximo = 0;
					$tiempo_promedio = 0;
				}else{
					$tiempo_minimo = $tag_dato->getAttribute('tiempo_min');
					$tiempo_maximo = $tag_dato->getAttribute('tiempo_max');
					$tiempo_promedio = $tag_dato->getAttribute('tiempo_prom');
				}

				//SE CALCULA EL PONDERADO
				$total_min_ponderado = (($conf_ponderacion->getAttribute('valor')/100) * $tiempo_minimo) + $total_min_ponderado;
				$total_max_ponderado = (($conf_ponderacion->getAttribute('valor')/100) * $tiempo_maximo) + $total_max_ponderado;
				$total_prom_ponderado = (($conf_ponderacion->getAttribute('valor')/100) * $tiempo_promedio) + $total_prom_ponderado;

				//SE ASIGNA VALOR DE LA PONDERACIÓN
 			$T->setVar('__item_valor', number_format($conf_ponderacion->getAttribute('valor'), 2, '.', ''));

				$T->parse('lista_items', 'LISTA_ITEMS', true);
				$linea++;
			}

			$T->setVar('__min_total', number_format($total_min_ponderado, 2, ',', ''));
			$T->setVar('__max_total', number_format($total_max_ponderado, 2, ',', ''));
			$T->setVar('__prom_total', number_format($total_prom_ponderado, 2, ',', ''));

			$T->parse('lista_pasos', 'LISTA_PASOS', true);
			$orden++;
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		$this->resultado =$T->parse('out', 'tpl_tabla');
	}

	// TODO: metodo getRendimientoPonderadoInvertido()
	function getRendimientoPonderadoResumen() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$ponderacion = $usr->getPonderacion();
		if ($ponderacion == null) {
			$ponderacion_id = 0;
		}
		else {
			$ponderacion_id = $ponderacion->ponderacion_id;
		}

		$sql = "SELECT * FROM reporte.rendimiento_resumen_global_ponderado(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($ponderacion_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row['rendimiento_resumen_global_ponderado']);
		$xpath = new DOMXpath($dom);
		unset($row["rendimiento_resumen_global_ponderado"]);

		$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_ponderaciones = $xpath->query("//ponderaciones/item[@valor>0]");

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if ($xpath->query("//detalles/detalle[@paso_orden]/datos/dato")->length == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_tabla', 'rendimiento_ponderado_invertido.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_PONDERACION_TITULO', 'lista_ponderacion_titulo');
		$T->setBlock('tpl_tabla', 'LISTA_PONDERACION', 'lista_ponderacion');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');

		$T->setVar('lista_ponderacion_titulo', '');
		foreach ($conf_ponderaciones as $conf_ponderacion) {
			$T->setVar('__ponderacion_periodo', date("H", strtotime($conf_ponderacion->getAttribute('inicio')))." - ".date("H", strtotime($conf_ponderacion->getAttribute('termino')))." Hrs");
			$T->parse('lista_ponderacion_titulo', 'LISTA_PONDERACION_TITULO', true);
		}

		$linea = 1;
		$T->setVar('lista_pasos', '');
		foreach ($conf_pasos as $conf_paso) {
			$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
			$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));

			$T->setVar('lista_ponderacion', '');
			foreach ($conf_ponderaciones as $conf_ponderacion) {
				$tag_dato = $xpath->query("//detalles/detalle[@item_id=".$conf_ponderacion->getAttribute('item_id')."]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato")->item(0);

				$T->setVar('__paso_promedio', ($tag_dato == null)?'':number_format($tag_dato->getAttribute('tiempo_prom'), 3, ',', ''));
				$T->parse('lista_ponderacion', 'LISTA_PONDERACION', true);

			}
			$T->parse('lista_pasos', 'LISTA_PASOS', true);
			$linea++;
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		$this->resultado =$T->parse('out', 'tpl_tabla');

	 }


	/*************** FUNCIONES DE TABLAS OTROS ***************/
	/*************** FUNCIONES DE TABLAS OTROS ***************/
	/*************** FUNCIONES DE TABLAS OTROS ***************/

	/**
	 * Funcion para obtener la tabla de
	 * Resumen de Rendimiento y Disponibilidad.
	 */
	// TODO: getComparativoResumen()
	function getEstadisticaDetallado() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		# Variables para eventos especiales marcados por el cliente codigo 9.
		$event = new Event;
		$usr = new Usuario($current_usuario_id);
		$usr->__Usuario();
		$graficoSvg = new GraficoSVG();


		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		$tieneEvento = 'false';
		$arrayDateStart = array();		
		$nameFunction = 'EstadisticaDet';
		$data = null;
		$ids = null;

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'comparativo_resumen.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_TITULO_HORARIOS', 'bloque_titulo_horarios');
		$T->setBlock('tpl_tabla', 'ES_PRIMERO_MONITOR', 'es_primero_monitor');
		$T->setBlock('tpl_tabla', 'ES_PRIMERO_TOTAL', 'es_primero_total');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_TABLA', 'bloque_tabla');

		$T->setVar('__item_orden', $this->extra["item_orden"]);
		# Variables para mantenimiento.
		$T->setVar('__path_jquery_ui', REP_PATH_JQUERY_UI);
	
		$horarios = array($usr->getHorario($this->horario_id));
		if ($this->horario_id != 0) {
			$horarios[] = $usr->getHorario(0);
		}

		$orden = 1;
		$T->setVar('bloque_titulo_horarios', '');
		foreach ($horarios as $horario) {

			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT * FROM reporte.comparativo_resumen_parcial(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					pg_escape_string($horario->horario_id).",' ".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			$res =& $mdb2->query($sql);
// 			print($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = false;
			$dom->loadXML($row["comparativo_resumen_parcial"]);
			$xpath = new DOMXpath($dom);
			unset($row["comparativo_resumen_parcial"]);

			if ($xpath->query('//detalle[@paso_orden]/estadisticas/estadistica')->length == 0) {
				$this->resultado = $this->__generarContenedorSinDatos();
				return;
			}

			$conf_objetivo = $xpath->query('/atentus/resultados/propiedades/objetivos/objetivo')->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
			$conf_nodos = $xpath->query('//nodos/nodo');

			if(count($horarios) > 1) {
				$T->setVar('__item_horario', $this->extra["item_orden"]);
				$T->setVar('__horario_orden', $orden);
				$T->setVar('__horario_nombre',$horario->nombre);
				$T->parse('bloque_titulo_horarios', 'BLOQUE_TITULO_HORARIOS', false);
			}

			
			# Obtención de los eventos especiales marcados por el cliente
			foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
			}		
			
			# Verifica que exista marcado evento cliente.
			if ($marcado == true) {

				$dataMant = $event->getData(substr($ids, 1), $timeZone);
				$character = array("{", "}");
				$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
				$tieneEvento = 'true';
				$encode = json_encode($dataMant);
				$nodoId = (string)0;
				$T->setVar('__tiene_evento', $tieneEvento);
				$T->setVar('__name', $nameFunction);

			}

			/* LISTA DE MONITORES */
			$linea = 1;
			$T->setVar('lista_pasos', '');
			foreach($conf_nodos as $conf_nodo) {
				$primero = true;
				$tag_nodo = $xpath->query('//detalle[@nodo_id='.$conf_nodo->getAttribute('nodo_id').']/estadisticas/estadistica')->item(0);

				/* LISTA DE PASOS */
				foreach($conf_pasos as $conf_paso) {
					$tag_dato = $xpath->query('//detalle[@nodo_id='.$conf_nodo->getAttribute('nodo_id').']/detalles/detalle[@paso_orden='.$conf_paso->getAttribute('paso_orden').']/estadisticas/estadistica')->item(0);
					if($tag_dato != null) {
						$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
						$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");

						$T->setVar('es_primero_monitor', '');
						$T->setVar('es_primero_total', '');
						if ($primero) {
							$T->setVar('__monitor_nombre', $conf_nodo->getAttribute('nombre'));
							$T->setVar('__monitor_rowspan', $conf_pasos->length);
							$T->setVar('__monitor_total_monitoreo', $tag_nodo->getAttribute('cantidad'));
							$T->parse('es_primero_monitor', 'ES_PRIMERO_MONITOR', false);
							$T->parse('es_primero_total', 'ES_PRIMERO_TOTAL', false);
						}
						$T->setVar('__paso_nombre', $conf_paso->getAttribute("nombre"));
						$T->setVar('__paso_minimo', number_format($tag_dato->getAttribute('tiempo_min'), 3, ',', ''));
						$T->setVar('__paso_maximo', number_format($tag_dato->getAttribute('tiempo_max'), 3, ',', ''));
						$T->setVar('__paso_promedio', number_format($tag_dato->getAttribute('tiempo_prom'), 3, ',', ''));
						$T->setVar('__paso_uptime', number_format($tag_dato->getAttribute('uptime'), 3, ',', ''));
						$T->setVar('__paso_downtime', number_format($tag_dato->getAttribute('downtime'), 3, ',', ''));
						$T->setVar('__paso_no_monitoreo', number_format($tag_dato->getAttribute('sin_monitoreo'), 3, ',', ''));
						$T->setVar('__paso_evento_especial', number_format($tag_dato->getAttribute('marcado_cliente'), 3, ',', ''));

						$T->parse('lista_pasos', 'LISTA_PASOS', true);
						$primero = false;
						$linea++;
					}
				}
			}
			$T->parse('bloque_tabla', 'BLOQUE_TABLA', true);
			$orden++;
		}
		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		$this->resultado = $T->parse('out', 'tpl_tabla');

		# Agrega el acordeon cuando existan eventos.
		if (count($dataMant)>0){
			$this->resultado.= $graficoSvg->getAccordion($encode,$nameFunction);
		}
		return $this->resultado;
	}
	

	// TODO: metodo getElementos()
	function getDetalleElementos() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql= "SELECT * FROM reporte.elementos(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).",".
				pg_escape_string($this->extra["monitor_id"]).",'".
				pg_escape_string($this->extra["fecha_monitoreo"])."')";

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql,$res->userinfo);
			exit();
		}

		$row= $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row['elementos']);
		$xpath = new DOMXpath($dom);

		$conf_nodo = $xpath->query("//nodos/nodo[@nodo_id=".$this->extra["monitor_id"]."]")->item(0);

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'elementos.tpl');
		$T->setBlock('tpl_tabla', 'TIENE_TOOLTIP', 'tiene_tooltip');
		$T->setBlock('tpl_tabla', 'LISTA_ELEMENTOS', 'lista_elementos');
		$T->setBlock('tpl_tabla', 'LISTA_MONITORES', 'lista_monitores');

		/* LISTA DE MONITORES */
		$T->setVar('__monitoreo_fecha', date("d/m/Y H:i:s", strtotime($this->extra["fecha_monitoreo"])));
		$T->setVar('__monitor_id', $conf_nodo->getAttribute("nodo_id"));
		$T->setVar('__monitor_nombre', $conf_nodo->getAttribute("nombre"));

		$tooltip_id = 0;
		$T->setVar('lista_elementos', '');

		foreach ($xpath->query("//detalle[@nodo_id=".$this->extra["monitor_id"]."]/estadisticas/estadistica") as $estadistica_mon) {
			$T->setVar('__monitor_tamano', number_format(($estadistica_mon->getAttribute("tamano_total")/1024), 3, ',', ''));
			$T->setVar('__monitor_respuesta', number_format($estadistica_mon->getAttribute("respuesta_total"), 3, ',', ''));
		}

		/* LISTA DE ELEMENTOS OBTENIDOS DESDE EL MONITOR */
		foreach ($xpath->query("//detalle[@nodo_id=".$this->extra["monitor_id"]."]/datos/dato") as $dato_mon) {
			$conf_codigo = $xpath->query("/atentus/resultados/propiedades/codigos/codigo[@codigo_id=".$dato_mon->getAttribute("status")."]")->item(0);

			$T->setVar('__elemento_url', $dato_mon->getAttribute("url"));
			$T->setVar('__elemento_url_corto', substr($dato_mon->getAttribute("url"), 0, 75));
			//$T->setVar('__elemento_tamano', ($dato_mon->getAttribute("tamano_body")=='-2')?"Vacio":number_format((($dato_mon->getAttribute("tamano_body")+$dato_mon->getAttribute("tamano_header"))/1024), 3, ',', ''));
			$T->setVar('__elemento_tamano', ($dato_mon->getAttribute("tamano_body")=='-2' || ($dato_mon->getAttribute("tamano_body")+$dato_mon->getAttribute("tamano_header") < '0'))?"Vacio":number_format((($dato_mon->getAttribute("tamano_body")+$dato_mon->getAttribute("tamano_header"))/1024), 3, ',', ''));
			$T->setVar('__elemento_tipo', ($dato_mon->getAttribute("tipo"))?"Desconocido":$dato_mon->getAttribute("tipo"));
			$T->setVar('__elemento_tipo_icono', REP_PATH_IMG_MIMETYPES.Utiles::getIconMime($dato_mon->getAttribute("tipo")));
			$T->setVar('__elemento_estado_color',$conf_codigo->getAttribute("color"));
			$T->setVar('__elemento_estado_nombre', $conf_codigo->getAttribute("nombre"));
			$T->setVar('__elemento_estado_icono', REP_PATH_SPRITE_CODIGO.substr($conf_codigo->getAttribute("icono"), 0, -4));
			$T->setVar('__elemento_respuesta', number_format($dato_mon->getAttribute("respuesta"), 3, ',', ''));

			$T->setVar('tiene_tooltip', '');
			$T->setVar('__tooltip_id', $this->extra["monitor_id"].(++$tooltip_id));
			$T->setVar('__elemento_estado_descripcion', $conf_codigo->getAttribute('descripcion'));
			$T->parse('tiene_tooltip', 'TIENE_TOOLTIP', false);

			$T->parse('lista_elementos', 'LISTA_ELEMENTOS', true);
		}

		$this->resultado = $T->parse('out', 'tpl_tabla');
	}
	

	function getEspecialUptimePonderadaPorItemTabla() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		
		$objetivo_especial =new ConfigEspecial($this->extra['parent_objetivo_id']);
		$obj=$objetivo_especial->__objetivos[$this->objetivo_id];
		$metas =$obj->__metas[$this->objetivo_id];
		//TRAE LAS PONDERACIONES DEL OBJETIVO ESPECIAL
		$ponderaciones_horas = $objetivo_especial->getPonderaciones();
	
		$array_pasos=array();
		foreach ($ponderaciones_horas as $ponderaciones_hora){
	
			$ponderacion = "array['".pg_escape_string($ponderaciones_hora->valor_ponderacion)."','".
					pg_escape_string($ponderaciones_hora->hora_inicio)."','".
					pg_escape_string($ponderaciones_hora->hora_termino)."']";
	
	
			$sql = "SELECT * FROM reporte.disponibilidad_uptime_poritem(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					$ponderacion.",' ".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			// 			  			echo $sql.'<br>';exit;
	
			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
	
			if($row = $res->fetchRow()){
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_uptime_poritem']);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_uptime_poritem"]);
			}
	
	
			$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
			$conf_datos=$xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$conf_objetivo->getAttribute('objetivo_id')."]/detalles/detalle");
	

	
			foreach ($conf_pasos as $conf_paso){
				$paso=$conf_paso->getAttribute('paso_orden');
				foreach ($conf_datos as $conf_fecha){
					$fecha = date('Y/m/d',strtotime($conf_fecha->getAttribute('fecha')));
					
					if($xpath->query("datos/dato[@paso_orden=".$paso."]",$conf_fecha)->length==0){
						$array_pasos[$paso][$fecha]['eficiencia']+=0;
						$array_pasos[$paso][$fecha]['horas']+=0;
					}else{
						$tag_paso = $xpath->query("datos/dato[@paso_orden=".$paso."]",$conf_fecha)->item(0);
						if($array_pasos[$paso][$fecha]==null){
							$array_pasos[$paso][$fecha]['eficiencia']=0;
							$array_pasos[$paso][$fecha]['horas']=0;
						}
						$array_pasos[$paso][$fecha]['fecha']=$fecha;
						$array_pasos[$paso][$fecha]['horas']=$array_pasos[$paso][$fecha]['horas']+1;
						
						$array_pasos[$paso][$fecha]['eficiencia']=$array_pasos[$paso][$fecha]['eficiencia']+($tag_paso->getAttribute('eficiencia'));
					}	
				}
			}
	
		}
		
		//	TEMPLATE DEL GRAFICO
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'disponibilidad_uptime_del_dia.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
		
		
		// DATOS DE LA TABLA
		$T->setVar('bloque_pasos', '');
		foreach ($conf_pasos as $conf_paso) {
			$dias =0;
			$acumulado = 0;
			$arr_pasos =$array_pasos[$conf_paso->getAttribute('paso_orden')];

			$T->setVar('__pasos',$conf_paso->getAttribute('nombre'));
			$T->setVar('__meta',number_format($metas->indicador_uptime, 2, ',', ''));
			
			foreach ($arr_pasos as $arr_paso){
				if(date('d/m/Y', strtotime($arr_paso['fecha']))==date('d/m/Y', strtotime($this->timestamp->getInicioPeriodo()))){
					$T->setVar('__eficiencia',number_format($arr_paso['eficiencia'], 2, ',', ''));
				}
				$dias +=1;
				$acumulado += floatval($arr_paso['eficiencia']);
			}
			$T->setVar('__acumulado',number_format($acumulado/$dias, 2, ',', ''));
			$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
		}
	
		
		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		$this->resultado = $T->parse('out', 'tpl_tabla');
	}
	
	
	
	
	/**
	 * Funcion para obtener la tabla de 
	 * Eventos (para todos los monitores).
	 */
	/*
	Creado por:
	Modificado por: Carlos Sepúlveda
	Fecha de creacion:
	Fecha de ultima modificacion:16-06-2017
	*/
	function getEventos() {
	    global $mdb2;
	    global $log;
	    global $current_usuario_id;
	    global $data;
	    global $marcado;
	    global $usr;
	    
	    $event = new Event;
	    $graficoSvg = new GraficoSVG();
	    
	    /* TEMPLATE DEL GRAFICO */
	    $T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
	    $T->setFile('tpl_tabla', 'contenedor_tabla.tpl');
	    $T->setBlock('tpl_tabla', 'LISTA_CONTENEDORES', 'lista_contenedores');    
	    
	    
	    # Variables para eventos especiales marcados por el cliente codigo 9.
	    $timeZoneId = $usr->zona_horaria_id;
	    $arrTime = Utiles::getNameZoneHor($timeZoneId);
	    $timeZone = $arrTime[$timeZoneId];
	    $arrayDateStart = array();
	    $tieneEvento = 'false';
	    $data = null;
	    $ids = null;
	    
	    $sql1 = "SELECT * FROM reporte._detalle_marcado(".
	   	    pg_escape_string($current_usuario_id).",ARRAY[".
	   	    pg_escape_string($this->objetivo_id)."],'".
	   	    pg_escape_string($this->timestamp->getInicioPeriodo())."','".
	   	    pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
// 	   	    print($sql1);
	  
	   	$res1 =& $mdb2->query($sql1);
	   	if (MDB2::isError($res1)) {
	   	   $log->setError($sql1, $res1->userinfo);
	   	   exit();
	   	}
	   	    
	   	if($row1= $res1->fetchRow()){
	   	   $dom1 = new DomDocument();
	   	   $dom1->preserveWhiteSpace = FALSE;
	   	   $dom1->loadXML($row1['_detalle_marcado']);
	   	   $xpath1 = new DOMXpath($dom1);
	   	   unset($row1["_detalle_marcado"]);
	   	} 
	   	
	   	$tag_marcardo_mantenimientos = $xpath1->query("/detalles_marcado/detalle/marcado");
	   	    
	   	# Busca y guarda si existen marcados dentro del xml.
	   	foreach ($tag_marcardo_mantenimientos as $tag_marcado)
	   	{
	   	   $ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
	   	   $marcado = true;
	   	}
	   	    
	   	# Verifica que existan marcados por el usuario.
	   	    
	   	if ($marcado == true) {
	   	   $dataMant =$event->getData(substr($ids, 1), $timeZone);
	   	   $character = array("{", "}");
	   	   $objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
	   	   $tieneEvento = 'true';
	   	   
	   	   $data = json_encode($dataMant);
	   	}
	   
		
		if (isset($this->extra["monitor_id"]) and isset($this->extra["pagina"])) {
		    if($this->extra["semaforo"]==2){
		        $semaforo=2;
		    }else{
		        $semaforo=1;
		    }
		    
		    $T->setVar('__contenido_id', 'even__'.$this->extra["monitor_id"]);
		    $T->setVar('__contenido_tabla', $this->getDetalleEventos($this->extra["monitor_id"], $this->extra["pagina"], $semaforo));
		    $T->parse('lista_contenedores', 'LISTA_CONTENEDORES', true);
		    
		    $T->setVar('__tiene_evento', $tieneEvento);
		    
		    return $this->resultado = $T->parse('out', 'tpl_tabla');
		}
		


		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT foo.nodo_id FROM (".
			   "SELECT DISTINCT unnest(_nodos_id) AS nodo_id FROM _nodos_id(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).",'".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')) AS foo, nodo n ".
			   "WHERE foo.nodo_id=n.nodo_id ORDER BY orden";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$monitor_ids = array();
		while($row = $res->fetchRow()) {
			$monitor_ids[] = $row["nodo_id"];
		}

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (count($monitor_ids) == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		
		
		$T->setVar('__tiene_evento', $tieneEvento);
		/* LISTA DE MONITORES */
		$orden = 1;
		foreach ($monitor_ids as $monitor_id) {
			$T->setVar('__contenido_id', 'even_'.$monitor_id);
			$T->setVar('__contenido_tabla', $this->getDetalleEventos($monitor_id, 1, 1, $orden));
			$T->parse('lista_contenedores', 'LISTA_CONTENEDORES', true);
			$orden++;
		}
		$this->resultado = $T->parse('out', 'tpl_tabla');
		if ($data != null){
		    
			$this->resultado.= $graficoSvg->getAccordion($data,'accordionEvento');
		}
	}
	/*
	Creado por:Aldo Cruz Romero
	Modificado por: 16-03-2018
	Fecha de creacion:-
	Fecha de ultima modificacion:
	*/
	function getEventosScreenshotElemento() {
	    global $mdb2;
	    global $log;
	    global $current_usuario_id;
	    global $data;
	    global $marcado;
	    global $usr;
	    include 'utils/get_eventos.php';
	    $event = new Event;
	    $graficoSvg = new GraficoSVG();

	    if($this->extra["imprimir"]){
	    	$objetivo = new ConfigEspecial($this->objetivo_id);

			$nombre_objetivo = $objetivo->nombre;
			//echo $nombre_objetivo;

	    	$usuario = new Usuario($current_usuario_id);
			$usuario->__Usuario();
	    	$json = get_eventos($current_usuario_id, $this->objetivo_id, date("Y-m-d H:i", strtotime($this->timestamp->getInicioPeriodo())), date("Y-m-d H:i",strtotime($this->timestamp->getTerminoPeriodo())), $usuario->clave_md5);
	    	$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		    $T->setFile('tpl_tabla', 'contenedor_tabla.tpl');
		    $T->setVar('__nombre_obj', $nombre_objetivo);
		    $T->setVar('__contenido', $json);
		    $T->setVar('__valid_contenido', true);
		    $this->resultado = $T->parse('out', 'tpl_tabla');
	    }else{

		    /* TEMPLATE DEL GRAFICO */
		    $T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		    $T->setFile('tpl_tabla', 'contenedor_tabla.tpl');
		    $T->setBlock('tpl_tabla', 'LISTA_CONTENEDORES', 'lista_contenedores');
		    $T->setVar('__valid_contenido', false);
		    # Variables para eventos especiales marcados por el cliente codigo 9.
		    $timeZoneId = $usr->zona_horaria_id;
		    $arrTime = Utiles::getNameZoneHor($timeZoneId);
		    $timeZone = $arrTime[$timeZoneId];
		    $arrayDateStart = array();
		    $tieneEvento = 'false';
		    $data = null;
		    $ids = null;

		    $sql1 = "SELECT * FROM reporte._detalle_marcado(".pg_escape_string($current_usuario_id).",ARRAY[".pg_escape_string($this->objetivo_id)."],'".pg_escape_string($this->timestamp->getInicioPeriodo())."','".pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
	// 	   	    print($sql1);
		   	$res1 =& $mdb2->query($sql1);
		   	if (MDB2::isError($res1)) {
		   	   $log->setError($sql1, $res1->userinfo);
		   	   exit();
		   	}
		   	if($row1= $res1->fetchRow()){
		   	   $dom1 = new DomDocument();
		   	   $dom1->preserveWhiteSpace = FALSE;
		   	   $dom1->loadXML($row1['_detalle_marcado']);
		   	   $xpath1 = new DOMXpath($dom1);
		   	   unset($row1["_detalle_marcado"]);
		   	}
		   	$tag_marcardo_mantenimientos = $xpath1->query("/detalles_marcado/detalle/marcado");
		   	# Busca y guarda si existen marcados dentro del xml.
		   	foreach ($tag_marcardo_mantenimientos as $tag_marcado){
		   	   $ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
		   	   $marcado = true;
		   	}
		   	# Verifica que existan marcados por el usuario.
		   	if ($marcado == true) {
		   	   $dataMant =$event->getData(substr($ids, 1), $timeZone);
		   	   $character = array("{", "}");
		   	   $objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
		   	   $tieneEvento = 'true';
		   	   $data = json_encode($dataMant);
		   	}
			if (isset($this->extra["monitor_id"]) and isset($this->extra["pagina"])) {
			    if($this->extra["semaforo"]==2){
			        $semaforo=2;
			    }else{
			        $semaforo=1;
			    }
			    //$T->setVar('__contenido_id', 'even__'.$this->extra["monitor_id"]);
			    $T->setVar('__contenido_tabla', $this->getDetalleEventosScreenshotElementos($this->extra["monitor_id"], $this->extra["pagina"], $semaforo));
			    $T->parse('lista_contenedores', 'LISTA_CONTENEDORES', true);
			    $T->setVar('__tiene_evento', $tieneEvento);
			    return $this->resultado = $T->parse('out', 'tpl_tabla');
			}
			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT foo.nodo_id FROM (".
				   "SELECT DISTINCT unnest(_nodos_id) AS nodo_id FROM _nodos_id(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).",'".
					pg_escape_string($this->timestamp->getInicioPeriodo())."','".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')) AS foo, nodo n ".
				   "WHERE foo.nodo_id=n.nodo_id ORDER BY orden";
	//		print($sql);
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
			$monitor_ids = array();
			while($row = $res->fetchRow()) {
				$monitor_ids[] = $row["nodo_id"];
			}
			/* SI NO HAY DATOS MOSTRAR MENSAJE */
			if (count($monitor_ids) == 0) {
				$this->resultado = $this->__generarContenedorSinDatos();
				return;
			}

			$T->setVar('__tiene_evento', $tieneEvento);
			/* LISTA DE MONITORES */
			$orden = 1;
			foreach ($monitor_ids as $monitor_id) {
				$T->setVar('__contenido_id', 'even_'.$monitor_id);
				$T->setVar('__contenido_tabla', $this->getDetalleEventosScreenshotElementos($monitor_id, 1, 1, $orden));
				$T->parse('lista_contenedores', 'LISTA_CONTENEDORES', true);
				$orden++;
			}
			$this->resultado = $T->parse('out', 'tpl_tabla');
			if ($data != null){
				$this->resultado.= $graficoSvg->getAccordion($data,'accordionEvento');
			}
		}
	}
	/*
	Creado por:
	Modificado por: Carlos Sepúlveda
	Fecha de creacion:-
	Fecha de ultima modificacion:16-06-2017
	*/
	/**
	 * Funcion para obtener la tabla de 
	 * Eventos (por monitor, se ejecuta al cambiar la pagina).
	 * 
	 * @param integer $monitor_id
	 * @param integer $pagina
	 * @param integet $isSemaforo -- parametro para verificar si viene desde semaforo. 
	 */
	function getDetalleEventos($monitor_id, $pagina, $isSemaforo, $orden = 1) {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $data;
		global $marcado;
		global $usr;
		
		$arrayDateStart =array();
		
		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.eventos(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($monitor_id).", ".
				'0'.",'".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."',".
				(($this->extra["imprimir"])?100:6).", ".
				pg_escape_string($pagina).")";
		//print($sql).'<br>';
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row= $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row['eventos']);
		$xpath = new DOMXpath($dom);
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_validador_pag= intval($xpath->query("/atentus/resultados/parametros")->item(0)->getAttribute("validador_pagina"));
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_nodo = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=$monitor_id]")->item(0);
		
		
		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (
			(!$conf_pasos->length) or
			($xpath->query("//datos/dato")->length == 0 and $pagina == 1) or 

			($xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->length == 0)
		) {
			return $this->__generarContenedorSinDatos(($xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->length == 0)?'':$xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->item(0)->getAttribute('nombre'));
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'eventos.tpl');

		$T->setBlock('tpl_tabla', 'LISTA_EVENTOS_INICIO', 'lista_eventos_inicio');
		$T->setBlock('tpl_tabla', 'BLOQUE_PATRON', 'bloque_patron');
		$T->setBlock('tpl_tabla', 'LISTA_EVENTOS_PATRONES', 'lista_eventos_patrones');
		$T->setBlock('tpl_tabla', 'LISTA_EVENTOS', 'lista_eventos');
		$T->setBlock('tpl_tabla', 'LISTA_EVENTOS_FALTANTES', 'lista_eventos_faltantes');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_tabla', 'LISTA_EVENTOS_DURACION', 'lista_eventos_duracion');

		$T->setVar('__item_id', $this->__item_id);
		$T->setVar('__item_orden', $this->extra["item_orden"]);
		$T->setVar('__objetivo_nombre', $conf_objetivo->getAttribute('nombre'));
		$T->setVar('__monitor_id', $conf_nodo->getAttribute("nodo_id"));
		$T->setVar('__monitor_nombre', $conf_nodo->getAttribute("nombre"));
		$T->setVar('__monitor_orden', $orden);

		/* LISTA DE PASOS */
		$tooltip_id = 0;
		$cnt = 0;
		$linea = 1;
		$id = 0;
		$T->setVar('lista_pasos', '');
		foreach ($conf_pasos as $conf_paso) {
		
			// MARCAR PASO DESDE DISPONIBILIDAD
			if(isset($this->extra['paso_id']) and $this->extra['paso_id'] == $conf_paso->getAttribute('paso_orden')) {
				$T->setVar('__estiloPaso', 'celdanegra10');
			}
			else {
				$T->setVar('__estiloPaso', 'celdanegra20');
			}
			
			$T->setVar('__paso_id', $conf_paso->getAttribute('paso_orden'));
			$T->setVar('__paso_nombre', substr($conf_paso->getAttribute('nombre'),0,30));
			$T->setVar('__paso_nombre_completo', $conf_paso->getAttribute('nombre'));

			$T->setVar('lista_eventos_inicio', '');
			$T->setVar('lista_eventos', '');
			$T->setVar('lista_eventos_faltantes', '');
			$T->setVar('lista_eventos_duracion', '');

			$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato");

			/* LISTA DE EVENTOS DEL PASO */
			$cnt = 0;
			foreach ($tag_datos as $tag_dato) {
				$T->setVar('lista_eventos_patrones','');

				$arr_codigos = explode(",", $tag_dato->getAttribute('codigo_id'));

				/* SI TIENE MAS DE UN PATRON */
				foreach ($arr_codigos as $codigo_orden => $codigo_id) {
					$conf_codigo = $xpath->query("/atentus/resultados/propiedades/codigos/codigo[@codigo_id=".$codigo_id."]")->item(0);

					if ($this->subgrafico_id != 1) {
						$T->setVar('bloque_patron', '');

						$conf_patron = $xpath->query("patron[@orden=".$codigo_orden."]", $conf_paso)->item(0);
						if ($conf_patron != null) {
							$T->setVar('__patron', $conf_patron->getAttribute('nombre'));
							$T->parse('bloque_patron', 'BLOQUE_PATRON', false);
						}
					}
					if ($conf_codigo == null) {
						$T->setVar('__evento_nombre', "Codigo Desconocido");
						$T->setVar('__evento_descripcion', "Codigo Desconocido");
						$T->setVar('__evento_color', "c4c4c4");
						$T->setVar('__evento_icono', "sprite sprite-desconocido");
					}
					else {
						$T->setVar('__evento_nombre', $conf_codigo->getAttribute('nombre'));
						$T->setVar('__evento_descripcion', $conf_codigo->getAttribute('descripcion'));
						$T->setVar('__evento_color', $conf_codigo->getAttribute('color'));
						$T->setVar('__evento_icono', REP_PATH_SPRITE_CODIGO.substr($conf_codigo->getAttribute('icono'), 0, -4));
						$T->setVar('__id',(string)$id.substr($conf_codigo->getAttribute('icono'), 0, -4));
						$id++;

					}
					$T->setVar('__evento_tooltip_id', $tooltip_id);
					$T->parse('lista_eventos_patrones', 'LISTA_EVENTOS_PATRONES', true);
					$tooltip_id++;
				}

				$evento_inicio = date('d-m-Y H:i:s', strtotime($tag_dato->getAttribute('fecha')));
				if (in_array($evento_inicio, $arrayDateStart)) {
				    echo "Existe ";
				}
				$T->setVar('__evento_duracion', Utiles::formatDuracion($tag_dato->getAttribute('duracion')));
				$T->setVar('__evento_duracion_print', Utiles::formatDuracion($tag_dato->getAttribute('duracion'), 0));
				$T->setVar('__evento_inicio', $evento_inicio);
				$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
		
				// MARCAR FECHA DESDE DISPONIBILIDAD
				if(isset($this->extra['fecha_monitoreo']) and strtotime($this->extra['fecha_monitoreo']) == strtotime($tag_dato->getAttribute('fecha'))) {
					$T->setVar('__evento_style', 'celdanegra10');
				}
				else{
					$T->setVar('__evento_style', 'celdanegra40');
				}

				$T->parse('lista_eventos_inicio', 'LISTA_EVENTOS_INICIO', true);
				$T->parse('lista_eventos', 'LISTA_EVENTOS', true);
				$T->parse('lista_eventos_duracion', 'LISTA_EVENTOS_DURACION', true);
				$cnt++;
				$linea++;
			}
			for ($se=$cnt+1; $se<=6; $se++) {
				$T->parse('lista_eventos_faltantes', 'LISTA_EVENTOS_FALTANTES', true);
			}
			$T->parse('lista_pasos','LISTA_PASOS',true);
		}
		/* FORMATO DE PAGINAS */
		$T->setVar('__pagina', $pagina);
		$T->setVar('__pagina_atras', $pagina-1);
		$T->setVar('__pagina_adelante', $pagina+1);
		$T->setVar('__disabled_atras', ($pagina==1)?'disabled':'');
		$T->setVar('__disabled_adelante', ($conf_validador_pag==0)?'disabled':'');
		$T->setVar('__class_boton_atras', ($pagina==1)?'spriteButton spriteButton-atras_desactivado':'spriteButton spriteButton-atras');
		$T->setVar('__class_boton_adelante', ($conf_validador_pag==0)?'spriteButton spriteButton-adelante_desactivado':'spriteButton spriteButton-adelante');

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		
		$this->resultado = $T->parse('out', 'tpl_tabla');

		/**
		 *Solamente crea el accordeon si este es consultado a travez de reportes online cuando es por semaforo *se crea en getEventos().
		**/
		if ($isSemaforo == 2){
		  $graficoSvg = new GraficoSVG();
		    $T->setVar('__tiene_evento', true);
			if ($data != null){
				if($data != 'null')
					$this->resultado.= $graficoSvg->getAccordion($data,'accordionEvento');
			}
		}		
		return $this->resultado;
	}
	/*
	Creado por:Aldo Cruz Romero
	Modificado por: 16-03-2018
	Fecha de creacion:-
	Fecha de ultima modificacion:
	*/
	function getDetalleEventosScreenshotElementos($monitor_id, $pagina, $isSemaforo, $orden = 1) {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $data;
		global $marcado;
		global $usr;
		$objetivo = new ConfigObjetivo($this->objetivo_id);
		$servicio_id = $objetivo->getServicio()->servicio_id;

		$arrayDateStart =array();
		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.eventos(".pg_escape_string($current_usuario_id).", ".pg_escape_string($this->objetivo_id).", ".pg_escape_string($monitor_id).", ".'0'.",'".pg_escape_string($this->timestamp->getInicioPeriodo())."','".pg_escape_string($this->timestamp->getTerminoPeriodo())."', 6, ".pg_escape_string($pagina).")";
		//print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$row= $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row['eventos']);
		$xpath = new DOMXpath($dom);
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_nodo = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=$monitor_id]")->item(0);
		$cuenta_monitor = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/relacion")->length;
		$conf_validador_pag= intval($xpath->query("/atentus/resultados/parametros")->item(0)->getAttribute("validador_pagina"));


		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (
			(!$conf_pasos->length) or
			($xpath->query("//datos/dato")->length == 0 and $pagina == 1) or 
			($xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->length == 0)
		) {
			return $this->__generarContenedorSinDatos(($xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->length == 0)?'':$xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->item(0)->getAttribute('nombre'));
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'eventos_especiales.tpl');
		
		$T->setBlock('tpl_tabla', 'BLOQUE_PATRON', 'bloque_patron');
		$T->setBlock('tpl_tabla', 'LISTA_EVENTOS_PATRONES', 'lista_eventos_patrones');
		$T->setBlock('tpl_tabla', 'LISTA_EVENTOS_DURACION', 'lista_eventos_duracion');
		$T->setBlock('tpl_tabla', 'LISTA_EVENTOS_BOTON', 'lista_eventos_boton');
		$T->setBlock('tpl_tabla', 'LISTA_EVENTOS', 'lista_eventos');
		$T->setBlock('tpl_tabla', 'LISTA_EVENTOS_INICIO', 'lista_eventos_inicio');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS_EVENTOS', 'lista_pasos_eventos');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');

		$T->setVar('__item_id', $this->__item_id);
		$T->setVar('__item_orden', $this->extra["item_orden"]);
		$T->setVar('__objetivo_nombre', $conf_objetivo->getAttribute('nombre'));
		$T->setVar('__monitor_id', $conf_nodo->getAttribute("nodo_id"));
		$T->setVar('__monitor_nombre', $conf_nodo->getAttribute("nombre"));
		$T->setVar('__monitor_orden', $orden);
		$monitores[]= $conf_nodo->getAttribute("nombre");
		
		/* LISTA DE PASOS */
		$tooltip_id = 0;
		$cnt = 0;
		$linea = 1;
		$id = 0;

		$T->setVar('lista_pasos', '');
		$T->setVar('lista_pasos_eventos', '');
		$conf_monitor = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/relacion[@nodo=".$monitor_id."]")->item(0);
		$id_monitor=$conf_monitor->getAttribute("monitor");
		$array_dato= array();
		foreach ($conf_pasos as $conf_paso) {
		
			// MARCAR PASO DESDE DISPONIBILIDAD
			if(isset($this->extra['paso_id']) and $this->extra['paso_id'] == $conf_paso->getAttribute('paso_orden')) {
				$T->setVar('__estiloPaso', 'celdanegra10');
			}
			else {
				$T->setVar('__estiloPaso', 'celdanegra20');
			}
			$T->setVar('__id_paso_evento', $conf_paso->getAttribute('paso_orden'));
			$T->setVar('__paso_id', $conf_paso->getAttribute('paso_orden'));
			$T->setVar('__eventos_paso', substr($conf_paso->getAttribute('nombre'),0,30));
			$T->setVar('__paso_nombre', substr($conf_paso->getAttribute('nombre'),0,30));
			$T->setVar('__paso_nombre_completo', $conf_paso->getAttribute('nombre'));
			$T->setVar('lista_eventos_duracion', '');
			$T->setVar('lista_eventos_inicio', '');
			$T->setVar('lista_eventos', '');


			$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato");

			/* LISTA DE EVENTOS DEL PASO */
			foreach ($tag_datos as $tag_dato) {
				
				$T->setVar('lista_eventos_patrones','');
				if(!isset($array_dato[$tag_dato->getAttribute('fecha')])){
					$array_dato[$tag_dato->getAttribute('fecha')];
					$array_dato[$tag_dato->getAttribute('fecha')]['fecha_inicio']=$tag_dato->getAttribute('fecha');
					$array_dato[$tag_dato->getAttribute('fecha')]['fecha_termino']=$tag_dato->getAttribute('fecha_termino');
					$array_dato[$tag_dato->getAttribute('fecha')]['hora_inicio_tz']=$tag_dato->getAttribute('hora_inicio_tz');
					$array_dato[$tag_dato->getAttribute('fecha')]['hora_termino_tz']=$tag_dato->getAttribute('hora_termino_tz');
					$array_dato[$tag_dato->getAttribute('fecha')]['codigo_id']=$tag_dato->getAttribute('codigo_id');
				}
				$arr_codigos = explode(",", $tag_dato->getAttribute('codigo_id'));
				$paso=$conf_paso->getAttribute('paso_orden');
				
				$coma=',';
				$array_dato[$tag_dato->getAttribute('fecha')]['pasos'] .= $paso.$coma;
				
				/* SI TIENE MAS DE UN PATRON */
				foreach ($arr_codigos as $codigo_orden => $codigo_id) {
					$conf_codigo = $xpath->query("/atentus/resultados/propiedades/codigos/codigo[@codigo_id=".$codigo_id."]")->item(0);

					if ($this->subgrafico_id != 1) {
						$T->setVar('bloque_patron', '');

						$conf_patron = $xpath->query("patron[@orden=".$codigo_orden."]", $conf_paso)->item(0);
						if ($conf_patron != null) {
							$T->setVar('__patron', $conf_patron->getAttribute('nombre'));
							$T->parse('bloque_patron', 'BLOQUE_PATRON', false);
						}
					}
					if ($conf_codigo == null) {
						$T->setVar('__evento_nombre', "Codigo Desconocido");
						$T->setVar('__evento_descripcion', "Codigo Desconocido");
						$T->setVar('__evento_color', "c4c4c4");
						$T->setVar('__evento_icono', "sprite sprite-desconocido");
					}
					else {
						$T->setVar('__evento_nombre', $conf_codigo->getAttribute('nombre'));
						$T->setVar('__evento_descripcion', $conf_codigo->getAttribute('descripcion'));
						$T->setVar('__evento_color', $conf_codigo->getAttribute('color'));
						$T->setVar('__evento_icono', REP_PATH_SPRITE_CODIGO.substr($conf_codigo->getAttribute('icono'), 0, -4));
						$T->setVar('__id',(string)$id.substr($conf_codigo->getAttribute('icono'), 0, -4));
						$id++;

					}
					
					$T->setVar('__evento_tooltip_id', $tooltip_id);
					$T->parse('lista_eventos_patrones', 'LISTA_EVENTOS_PATRONES', true);
					$tooltip_id++;
				}
				$evento_inicio = date('d-m-Y H:i:s', strtotime($tag_dato->getAttribute('fecha')));
				if (in_array($evento_inicio, $arrayDateStart)) {
				    echo "Existe ";
				}
				$T->setVar('__evento_duracion', Utiles::formatDuracion($tag_dato->getAttribute('duracion')));
				$T->setVar('__evento_duracion_print', Utiles::formatDuracion($tag_dato->getAttribute('duracion'), 0));
				$T->setVar('__evento_inicio', $evento_inicio);
				$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
				
				// MARCAR FECHA DESDE DISPONIBILIDAD
				if(isset($this->extra['fecha_monitoreo']) and strtotime($this->extra['fecha_monitoreo']) == strtotime($tag_dato->getAttribute('fecha'))) {
					$T->setVar('__evento_style', 'celdanegra10');
				}else{
					$T->setVar('__evento_style', 'celdanegra40');
				}
				$T->parse('lista_eventos_inicio', 'LISTA_EVENTOS_INICIO', true);
				$T->parse('lista_eventos', 'LISTA_EVENTOS', true);
				$T->parse('lista_eventos_duracion', 'LISTA_EVENTOS_DURACION', true);
				$linea++;
			}
			$T->setVar('__obj', $this->objetivo_id);
			$T->parse('lista_pasos_eventos','LISTA_PASOS_EVENTOS',true);
			$T->parse('lista_pasos','LISTA_PASOS',true);
		}
		$T->setVar('lista_eventos_boton', '');
		$cont=0;
		$T->setVar('__array_dato', $array_dato);
		if($servicio_id==700){
			$servicio_id='mobile';
		}elseif ($servicio_id==290) {
			$servicio_id='meta';
		}else{
			$servicio_id='screenshot';
		}
		foreach ($array_dato as $key => $value) {
			$cont++;
			$date_inicio=($value['hora_inicio_tz']);
			$date_inicio_utc=explode(' ', $date_inicio);
			$fecha_inicio_utc=$date_inicio_utc[0].'T'.$date_inicio_utc[1];
			$date_termino=($value['hora_termino_tz']);
			$date_termino_utc=explode(' ', $date_termino);
			$array_date_termino=explode(':',$date_termino_utc[1]);
			$hour_date_termino=$array_date_termino[0];
			$minute_date_termino=$array_date_termino[1];
			$array_seg_termino=explode('+',$array_date_termino[2]);
			$seg_termino_utc=intval($array_seg_termino[0])-1;
			if($seg_termino_utc<0){
				$seg_termino_utc='00';
			}
			if($seg_termino_utc==59){
				$seg_termino_utc='00';
			}
			if($seg_termino_utc<10){
				$seg_termino_utc='0'.$seg_termino_utc;
			}
			$fecha_termino_utc=$date_termino_utc[0].'T'.$hour_date_termino.':'.$minute_date_termino.':'.$seg_termino_utc;
			$T->setVar('__contador', $cont);
			$T->setVar('__value', $value);
			$T->setVar('__evento_cdn', $value['pasos']);
			$T->setVar('__hora_inicio_tz',$fecha_inicio_utc);
			$T->setVar('__hora_termino_tz',$fecha_termino_utc);
			$T->setVar('__evento_inicio_fecha',$value['fecha_inicio']);
			$T->setVar('__evento_termino_fecha',$value['fecha_termino']);
			$T->setVar('__codigo_id',$value['codigo_id']);
			$T->setVar('__id_monitor', $id_monitor);
			$T->setVar('__nombre_nodo', $conf_nodo->getAttribute("nombre"));
			$T->setvar('__servicio', $servicio_id);
			$cnt++;
			$T->parse('lista_eventos_boton', 'LISTA_EVENTOS_BOTON', true);
		}
		/* FORMATO DE PAGINAS */
		$T->setVar('__pagina', $pagina);
		$T->setVar('__pagina_atras', $pagina-1);
		$T->setVar('__pagina_adelante', $pagina+1);
		$T->setVar('__disabled_atras', ($pagina==1)?'disabled':'');
		$T->setVar('__disabled_adelante', ($conf_validador_pag==0)?'disabled':'');
		$T->setVar('__class_boton_atras', ($pagina==1)?'spriteButton spriteButton-atras_desactivado':'spriteButton spriteButton-atras');
		$T->setVar('__class_boton_adelante', ($conf_validador_pag==0)?'spriteButton spriteButton-adelante_desactivado':'spriteButton spriteButton-adelante');

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		
		$this->resultado = $T->parse('out', 'tpl_tabla');

		/**
		 *Solamente crea el accordeon si este es consultado a travez de reportes online cuando es por semaforo *se crea en getEventos().
		**/
		if ($isSemaforo == 2){
		  $graficoSvg = new GraficoSVG();
		    $T->setVar('__tiene_evento', true);
			if ($data != null){
				if($data != 'null')
					$this->resultado.= $graficoSvg->getAccordion($data,'accordionEvento');
			}
		}
		return $this->resultado;
	}

	/**
	 * Funcion para obtener la tabla de 
	 * Registros (para todos los monitores).
	 */
	function getRegistros() {
		if (isset($this->extra["monitor_id"]) and isset($this->extra["pagina"])) {
			$this->resultado = $this->getDetalleRegistros($this->extra["monitor_id"], $this->extra["pagina"]);
			return;
		}

		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT unnest(_nodos_id) AS nodo_id FROM _nodos_id(".
			   pg_escape_string($current_usuario_id).", ".
			   pg_escape_string($this->objetivo_id).", '".
			   pg_escape_string($this->timestamp->getInicioPeriodo())."','".
			   pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		//print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$monitor_ids = array();
		while($row = $res->fetchRow()) {
			$monitor_ids[] = $row["nodo_id"];
		}

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (count($monitor_ids) == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'contenedor_tabla.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_CONTENEDORES', 'lista_contenedores');

		/* LISTA DE MONITORES */
		$orden = 1;
		foreach ($monitor_ids as $monitor_id) {
			$T->setVar('__contenido_id', 'reg_'.$monitor_id);
			$T->setVar('__contenido_tabla', $this->getDetalleRegistros($monitor_id, 1, $orden));
			$T->parse('lista_contenedores', 'LISTA_CONTENEDORES', true);
			$orden++;
		}

		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	/**
	 * Funcion para obtener la tabla de
	 * Registros (por monitor, se ejecuta al cambiar la pagina).
	 *
	 * @param integer $monitor_id
	 * @param integer $pagina
	 */
	function getDetalleRegistros($monitor_id, $pagina, $orden = 1) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.registros(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($monitor_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."',".
				(($this->extra["imprimir"])?100:12).", ".
				pg_escape_string($pagina).")";
//		print($sql);

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row['registros']);
		$xpath = new DOMXpath($dom);
		unset($row["registros"]);

		$conf_objetivo = $xpath->query('/atentus/resultados/propiedades/objetivos/objetivo')->item(0);
		$conf_nodo = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=$monitor_id]")->item(0);

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (!$xpath->query('//datos/dato')->length and $pagina == 1) {
			return $this->__generarContenedorSinDatos($conf_nodo->getAttribute('nombre'));
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'registros_mediciones.tpl');
		$T->setBlock('tpl_tabla', 'TIENE_MOSTRAR_DETALLES', 'tiene_mostrar_detalles');
		$T->setBlock('tpl_tabla', 'LISTA_NOMBRES', 'lista_nombres');
		$T->setBlock('tpl_tabla', 'LISTA_TIPOS', 'lista_tipos');
		$T->setBlock('tpl_tabla', 'LISTA_PRIORIDAD', 'lista_prioridad');
		$T->setBlock('tpl_tabla', 'LISTA_RESPUESTAS', 'lista_respuestas');
		$T->setBlock('tpl_tabla', 'LISTA_REGISTROS', 'lista_registros');

		$T->setVar('__item_id', $this->__item_id);
		$T->setVar('__item_orden', $this->extra["item_orden"]);

		$T->setVar('__monitor_id', $conf_nodo->getAttribute('nodo_id'));
		$T->setVar('__monitor_nombre', $conf_nodo->getAttribute('nombre'));
		$T->setVar('__monitor_orden', $orden);

		$tag_datos = $xpath->query('//detalles/detalle[@nodo_id='.$conf_nodo->getAttribute('nodo_id').']/datos/dato');

		/* LISTA DE REGISTROS DEL MONITOR */
		$linea = 1;
		$T->setVar('lista_registros', '');
		foreach ($tag_datos as $tag_dato) {
			$conf_codigo =$xpath->query("/atentus/resultados/propiedades/codigos/codigo[@codigo_id=".$tag_dato->getAttribute('codigo_id')."]")->item(0);
			$tag_registros = $xpath->query('registros/registro', $tag_dato);

			$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
			$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
			$T->setVar('__registro_fecha', $this->timestamp->getFormatearFecha($tag_dato->getAttribute('fecha_inicio'), "d-m-Y H:i:s"));
			$T->setVar('__registro_duracion', Utiles::formatDuracion($tag_dato->getAttribute('duracion'), 0));
			$T->setVar('__registro_estado_color', $conf_codigo->getAttribute('color'));
			$T->setVar('__registro_estado_icono', REP_PATH_SPRITE_CODIGO.substr($conf_codigo->getAttribute('icono'), 0, -4));
			$T->setVar('__registro_estado_nombre', $conf_codigo->getAttribute('nombre'));
			$T->setVar('__registro_servidor', $tag_dato->getAttribute('servidor'));	// A - MX - SOA
			$T->setVar('__registro_primario', $tag_dato->getAttribute('dns_primario'));		// SOA
			$T->setVar('__registro_serial', $tag_dato->getAttribute('serial'));		// SOA
			$T->setVar('__registro_email', $tag_dato->getAttribute('email'));		// DETALLES SOA
			$T->setVar('__registro_refresh', $tag_dato->getAttribute('refresh'));	// DETALLES SOA
			$T->setVar('__registro_retry', $tag_dato->getAttribute('retry'));		// DETALLES SOA
			$T->setVar('__registro_expire', $tag_dato->getAttribute('expire'));		// DETALLES SOA
			$T->setVar('__registro_minimum', $tag_dato->getAttribute('minimum'));	// DETALLES SOA

			$T->setVar('lista_nombres', '');
			foreach ($tag_registros as $tag_registro) {
				$T->setVar('__registro_nombre', ($tag_registro->getAttribute('nombre'))?$tag_registro->getAttribute('nombre'):''); // A
				$T->parse('lista_nombres', 'LISTA_NOMBRES', true);
			}
			$T->setVar('lista_tipos', '');
			foreach ($tag_registros as $tag_registro) {
				$T->setVar('__registro_tipo', $tag_registro->getAttribute('tipo')?$tag_registro->getAttribute('tipo'):''); // A
				$T->parse('lista_tipos', 'LISTA_TIPOS', true);
			}
			$T->setVar('lista_prioridad','');
			foreach ($tag_registros as $tag_registro) {
				$T->setVar('__registro_prioridad', ($tag_registro->getAttribute('prioridad'))?$tag_registro->getAttribute('prioridad'):''); // MX
				$T->parse('lista_prioridad','LISTA_PRIORIDAD',true);
			}
			$T->setVar('lista_respuestas', '');
			foreach ($tag_registros as $tag_registro) {
				$T->setVar('__registro_respuesta', ($tag_registro->getAttribute('respuesta'))?$tag_registro->getAttribute('respuesta'):''); // MX - CHAOS
				$T->parse('lista_respuestas', 'LISTA_RESPUESTAS', true);
			}

			$T->parse('lista_registros', 'LISTA_REGISTROS', true);
			$linea++;
		}

		/* FORMATO DE PAGINAS */
		$T->setVar('__pagina', $pagina);
		$T->setVar('__pagina_atras', $pagina-1);
		$T->setVar('__pagina_adelante', $pagina+1);
		$T->setVar('__disabled_atras', ($pagina==1)?'disabled':'');
		$T->setVar('__disabled_adelante', (($tag_datos->length<12) ? 'disabled':''));
		$T->setVar('__class_boton_atras', ($pagina==1)?'spriteButton spriteButton-atras_desactivado':'spriteButton spriteButton-atras');
		$T->setVar('__class_boton_adelante', (($tag_datos->length<12)?'spriteButton spriteButton-adelante_desactivado':'spriteButton spriteButton-adelante'));


		if ($tag_datos->length == 0 and $pagina != 1) {
			return 0;
		}

		$T->setVar('tiene_mostrar_detalles', '');

		$tag_subtipo = strtolower( $xpath->query("/atentus/resultados/subtipo")->item(0)->nodeValue );
		if ($tag_subtipo == "dns_a") {
			$T->setVar('__tiene_primario', 'style="display:none;"');
			$T->setVar('__tiene_serial', 'style="display:none;"');
			$T->setVar('__tiene_detalles', 'style="display:none;"');
			$T->setVar('__tiene_prioridad', 'style="display:none;"');
			$T->setVar('__tiene_respuestas', 'style="display:none;"');
		}
		elseif ($tag_subtipo == "dns_mx") {
			$T->setVar('__tiene_primario', 'style="display:none;"');
			$T->setVar('__tiene_serial', 'style="display:none;"');
			$T->setVar('__tiene_detalles', 'style="display:none;"');
			$T->setVar('__tiene_nombres', 'style="display:none;"');
			$T->setVar('__tiene_tipos', 'style="display:none;"');
		}
		elseif ($tag_subtipo == "dns_soa") {
			$T->setVar('__mostrar', ($_REQUEST["mostrar_detalles"])?0:1);
			$T->setVar('__mostrar_actual', ($_REQUEST["mostrar_detalles"])?1:0);
			$T->setVar('__tiene_detalles', ($_REQUEST["mostrar_detalles"])?'':'style="display:none;"');
			$T->setVar('__tiene_primario', ($_REQUEST["mostrar_detalles"])?'style="display:none;"':'');
			$T->setVar('__tiene_serial', ($_REQUEST["mostrar_detalles"])?'style="display:none;"':'');
			$T->parse('tiene_mostrar_detalles', 'TIENE_MOSTRAR_DETALLES', false);

			$T->setVar('__tiene_nombres', 'style="display:none;"');
			$T->setVar('__tiene_tipos', 'style="display:none;"');
			$T->setVar('__tiene_prioridad', 'style="display:none;"');
			$T->setVar('__tiene_respuestas', 'style="display:none;"');
		}
		else {
			$T->setVar('__tiene_servidor', 'style="display:none;"');
			$T->setVar('__tiene_primario', 'style="display:none;"');
			$T->setVar('__tiene_serial', 'style="display:none;"');
			$T->setVar('__tiene_detalles', 'style="display:none;"');
			$T->setVar('__tiene_nombres', 'style="display:none;"');
			$T->setVar('__tiene_tipos', 'style="display:none;"');
			$T->setVar('__tiene_prioridad', 'style="display:none;"');
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));

		return $T->parse('out', 'tpl_tabla');
	}

	// TODO: metodo getElementosMediciones()
	function getMonitoreosElementos() {
		if (isset($this->extra["monitor_id"]) and isset($this->extra["pagina"])) {
			$this->resultado = $this->getDetalleMonitoreosElementos($this->extra["monitor_id"], $this->extra["pagina"]);
			return;
		}

		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT foo.nodo_id FROM (".
			   "SELECT DISTINCT unnest(_nodos_id) AS nodo_id FROM _nodos_id(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).",'".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')) AS foo, nodo n ".
			   "WHERE foo.nodo_id=n.nodo_id ORDER BY orden";

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$monitor_ids = array();
		while ($row = $res->fetchRow()) {
			$monitor_ids[] = $row["nodo_id"];
		}

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (count($monitor_ids) == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'contenedor_tabla.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_CONTENEDORES', 'lista_contenedores');

		/* LISTA DE MONITORES */
		$orden = 1;
		foreach ($monitor_ids as $monitor_id) {
			$T->setVar('__contenido_id', 'elem_'.$monitor_id);
			$T->setVar('__contenido_tabla', $this->getDetalleMonitoreosElementos($monitor_id, 1, $orden));
			$T->parse('lista_contenedores', 'LISTA_CONTENEDORES', true);
			$orden++;
		}
		$this->resultado = $T->parse('out', 'tpl_tabla');

	}

	// TODO: metodo getDetalleElementosMediciones
	function getDetalleMonitoreosElementos($monitor_id, $pagina, $orden = 1) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.elementos_mediciones(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->objetivo_id).",".
				pg_escape_string($monitor_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."',".
				pg_escape_string($pagina).",".
				(($this->extra["imprimir"])?100:12).")";

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row['elementos_mediciones']);
		$xpath = new DOMXpath($dom);

		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_nodo = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$monitor_id."]")->item(0);

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (
			!$conf_pasos->length
			or ($xpath->query("//datos/dato")->length == 0 and $pagina == 1)
			or $xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->length == 0
		) {
			return $this->__generarContenedorSinDatos(($xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->length == 0)?'':$xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->item(0)->getAttribute('nombre'));
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'elementos_mediciones.tpl');
		if(!$this->extra["imprimir"]){
			$T->setBlock('tpl_tabla', 'TIENE_TOOLTIP', 'tiene_tooltip');
		}
		$T->setBlock('tpl_tabla', 'LISTA_MONITOREOS', 'lista_monitoreos');

		$T->setVar('__item_id', $this->__item_id);
		$T->setVar('__item_id_nuevo', REP_ITEM_ELEMENTOS);
		$T->setVar('__item_orden', $this->extra["item_orden"]);

		$T->setVar('__monitor_id', $conf_nodo->getAttribute('nodo_id'));
		$T->setVar('__monitor_nombre', $conf_nodo->getAttribute('nombre'));
		$T->setVar('__monitor_orden', $orden);

		/* LISTA DE ELEMENTOS OBTENIDOS DESDE EL MONITOR */
		$linea = 1;
		$tooltip_id = 0;
		$T->setVar('lista_monitoreos', '');
		$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=".$conf_nodo->getAttribute('nodo_id')."]/datos/dato");
		foreach ($tag_datos as $tag_dato) {
			$conf_codigo =$xpath->query("/atentus/resultados/propiedades/codigos/codigo[@codigo_id=".$tag_dato->getAttribute('codigo_id')."]")->item(0);
			$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
			$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
			$T->setVar('__monitoreo_fecha', $tag_dato->getAttribute('fecha'));
			$T->setVar('__monitoreo_fecha_mostrar', $this->timestamp->getFormatearFecha($tag_dato->getAttribute('fecha'), "d/m/Y H:i:s"));
			$T->setVar('__monitoreo_estado_color', $conf_codigo->getAttribute('color'));
			$T->setVar('__monitoreo_estado_icono', REP_PATH_SPRITE_CODIGO.substr($conf_codigo->getAttribute('icono'), 0, -4));
			$T->setVar('__monitoreo_estado_nombre', $conf_codigo->getAttribute('nombre'));
			$T->setVar('__monitoreo_respuesta', number_format(($tag_dato->getAttribute('suma_tiempos')/1000), 3, ',', ''));
			$T->setVar('__monitoreo_tamanno', number_format(($tag_dato->getAttribute('suma_tamanos')/1024), 3, ',', ''));
			$T->setVar('__monitoreo_elementos', $tag_dato->getAttribute('cantidad'));

			if (!$this->extra["imprimir"]) {
				$T->setVar('tiene_tooltip', '');
				$T->setVar('__tooltip_id', $monitor_id.(++$tooltip_id));
				$T->setVar('__monitoreo_estado_descripcion', $conf_codigo->getAttribute('descripcion'));
				$T->parse('tiene_tooltip', 'TIENE_TOOLTIP', false);
			}

			$T->parse('lista_monitoreos', 'LISTA_MONITOREOS', true);
			$linea++;
		}

		$T->setVar('__monitor_seleccion', $monitor_id);
		$T->setVar('__path_img_boton', REP_PATH_IMG_BOTONES);
		$T->setVar('__pagina', $pagina);
		$T->setVar('__pagina_atras', $pagina-1);
		$T->setVar('__pagina_adelante', $pagina+1);
		$T->setVar('__disabled_atras', ($pagina==1)?'disabled':'');
		$T->setVar('__disabled_adelante', ($tag_datos->length<12)?'disabled':'');
		$T->setVar('__class_boton_atras', ($pagina==1)?'spriteButton spriteButton-atras_desactivado':'spriteButton spriteButton-atras');
		$T->setVar('__class_boton_adelante', ($tag_datos->length<12)?'spriteButton spriteButton-adelante_desactivado':'spriteButton spriteButton-adelante');

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));

		return $this->resultado = $T->parse('out', 'tpl_tabla');
	}

	/**
	 * Funcion para obtener la tabla de
	 * Elementos de un Periodo.
	 */
	// TODO: metodo getElementosEstadisticas()
	function getEstadisticasElementos() {
		if (isset($this->extra["monitor_id"]) and isset($this->extra["pagina"])) {
			$this->resultado = $this->getDetalleEstadisticasElementos($this->extra["monitor_id"], $this->extra["pagina"], 1);
			return;
		}

		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT foo.nodo_id FROM (".
			   "SELECT DISTINCT unnest(_nodos_id) AS nodo_id FROM _nodos_id(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).",'".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')) AS foo, nodo n ".
			   "WHERE foo.nodo_id=n.nodo_id ORDER BY orden";

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$monitor_ids = array();
		while ($row = $res->fetchRow()) {
			$monitor_ids[] = $row["nodo_id"];
		}

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (count($monitor_ids) == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'contenedor_tabla.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_CONTENEDORES', 'lista_contenedores');

		/* LISTA DE MONITORES */
		$orden = 1;
		foreach ($monitor_ids as $monitor_id) {
			$T->setVar('__contenido_id', 'elem_estadistica_'.$monitor_id);
			$T->setVar('__contenido_tabla', $this->getDetalleEstadisticasElementos($monitor_id, 1, $orden));
			$T->parse('lista_contenedores', 'LISTA_CONTENEDORES', true);
			$orden++;
		}
		$this->resultado = $T->parse('out', 'tpl_tabla');
	}


	function getDetalleEstadisticasElementos($monitor_id, $pagina, $orden) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.elementos_estadistica(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->objetivo_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."')";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
 		$dom->preserveWhiteSpace = FALSE;
 		$dom->loadXML($row['elementos_estadistica']);
 		$xpath = new DOMXpath($dom);

 		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
 		$conf_pasos=$xpath->query("paso[@visible=1]",$conf_objetivo);
		$conf_nodo = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$monitor_id."]")->item(0);
		$cnt_pagina = ($this->extra["imprimir"])?100:12;

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (
			$conf_pasos->length ==0
			or ($xpath->query("//detalle[@nodo_id=".$monitor_id."]/datos/dato")->length == 0 and $pagina == 1)
			or $xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->length == 0
		) {
			return $this->__generarContenedorSinDatos(($xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->length == 0)?'':$xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->item(0)->getAttribute('nombre'));
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'elementos_estadisticas.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_EVENTOS', 'lista_eventos');
		$T->setBlock('tpl_tabla', 'LISTA_ELEMENTOS', 'lista_elementos');

		$T->setVar('__item_id', $this->__item_id);
		$T->setVar('__item_orden', $this->extra["item_orden"]);

		$T->setVar('__monitor_id',  $conf_nodo->getAttribute('nodo_id'));
		$T->setVar('__monitor_nombre',  $conf_nodo->getAttribute('nombre'));
		$T->setVar('__monitor_orden', $orden);

		$linea = 1;
		$T->setVar('lista_elementos', '');
		$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$conf_objetivo->getAttribute('objetivo_id')."]/detalles/detalle[@nodo_id=".$conf_nodo->getAttribute('nodo_id')."]/datos/dato");
		for ($indice = (($pagina - 1) * $cnt_pagina); $linea <= $cnt_pagina and $indice < $tag_datos->length; $indice++) {
			$tag_dato = $tag_datos->item($indice);
			$tipo_part = explode("/", $tag_dato->getAttribute('tipo'));

			$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
			$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
			$T->setVar('__elemento_url', $tag_dato->getAttribute('url'));
			$T->setVar('__elemento_tipo_nombre', $tipo_part[1]);
			$T->setVar('__elemento_tipo_icono', REP_PATH_IMG_MIMETYPES.Utiles::getIconMime($tag_dato->getAttribute('tipo')));
			$T->setVar('__elemento_tamanno', number_format(($tag_dato->getAttribute('tamano_promedio')/1024), 3, ',', ''));
			$T->setVar('__elemento_minimo', number_format(($tag_dato->getAttribute('tiempo_min')/1000), 3, ',', ''));
			$T->setVar('__elemento_maximo', number_format(($tag_dato->getAttribute('tiempo_max')/1000), 3, ',', ''));
			$T->setVar('__elemento_promedio', number_format(($tag_dato->getAttribute('tiempo_promedio')/1000), 3, ',', ''));
			$T->setVar('__elemento_cantidad', $tag_dato->getAttribute('cantidad'));

			$T->setVar('lista_eventos', '');
			$entrar = true;
			while ($entrar == true) {
				$conf_codigo = $xpath->query("/atentus/resultados/propiedades/codigos/codigo[@codigo_id=".$tag_dato->getAttribute("status")."]")->item(0);
				$T->setVar('__evento_nombre', $conf_codigo->getAttribute('nombre'));
				$T->setVar('__evento_color',$conf_codigo->getAttribute('color'));
				$T->setVar('__evento_icono', REP_PATH_SPRITE_CODIGO.substr($conf_codigo->getAttribute('icono'), 0, -4));
				$T->setVar('__evento_cantidad', $tag_dato->getAttribute('cantidad_status'));
				$T->parse('lista_eventos', 'LISTA_EVENTOS', true);

				if (($indice) + 1 < $tag_datos->length and $tag_dato->getAttribute('url') == $tag_datos->item($indice + 1)->getAttribute('url') and
					$tag_dato->getAttribute('tipo') == $tag_datos->item($indice + 1)->getAttribute('tipo') and
					$tag_dato->getAttribute('status') != $tag_datos->item($indice + 1)->getAttribute('status')) {
					$tag_dato = $tag_datos->item($indice + 1);
					$indice++;
				}
				else {
					$entrar = false;
				}
			}
			$T->parse('lista_elementos', 'LISTA_ELEMENTOS', true);
			$linea++;
		}

		$T->setVar('__monitor_seleccion', $monitor_id);
		$T->setVar('__pagina', $pagina);
		$T->setVar('__pagina_atras', $pagina-1);
		$T->setVar('__pagina_adelante', $pagina+1);
		$T->setVar('__disabled_atras', ($pagina==1)?'disabled':'');
		$T->setVar('__disabled_adelante', ($linea<$cnt_pagina)?'disabled':'');
		$T->setVar('__class_boton_atras', ($pagina==1)?'spriteButton spriteButton-atras_desactivado':'spriteButton spriteButton-atras');
		$T->setVar('__class_boton_adelante', ($linea<$cnt_pagina)?'spriteButton spriteButton-adelante_desactivado':'spriteButton spriteButton-adelante');

		if ($linea == 1 and $pagina != 1) {
			return 0;
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		return	$this->resultado = $T->parse('out', 'tpl_tabla');
	}


	/*************** FUNCIONES DE TABLAS PLUS ***************/
	/*************** FUNCIONES DE TABLAS PLUS ***************/
	/*************** FUNCIONES DE TABLAS PLUS ***************/

	/**
	 * Funcion que muestra la lista de elementos plus
	 * para todos los monitores.
	 */
	// TODO: metodo getElementosPlusMediciones
	function getElementosPlus() {	

		global $mdb2;
		global $log;
		global $current_usuario_id;

		
		//Y GUARDANDO OS DATOS EN VARIABLES
		if (isset($_REQUEST['hora_inicio'])) {
			$hora_inicio = $_REQUEST['hora_inicio'];
			$minuto_inicio = $_REQUEST['minuto_inicio'];
			$hora_termino = $_REQUEST['hora_termino'];
			$minuto_termino = $_REQUEST['minuto_termino'];
		}else{
			$hora_termino  = '23';
			$minuto_termino  = '59';
			$hora_inicio = '00';
			$minuto_inicio  = '00';
		}

		if (isset($_REQUEST['nodo_filtro'])) {
			$nodo_filtro = $_REQUEST['nodo_filtro'];
			$isset = 1;
		}else{
			$nodo_filtro = -1;
			$isset = 0;
		}


		//SE GUARDA DIA MES Y AÑO EN VARIABLE Y SE LE AGREGA LA HORA QUE SE PASA POR POST POR LOS INPUTS DEL FILTRO
		$fechainicioSinHora = date("Y-m-d", strtotime($this->timestamp->getInicioPeriodo()));
		$fecha_inicio_filtro = $fechainicioSinHora.' '.$hora_inicio.':'.$minuto_inicio.':00';
		$fechaTerminoSinHora = date("Y-m-d", strtotime($this->timestamp->getInicioPeriodo()));
		$fecha_termino_filtro = $fechaTerminoSinHora.' '.$hora_termino.':'.$minuto_termino.':59';

		//SI SE ACTIVA EL FILTRO SE GUARDA FECHA PASADA POR POST SINO LA FECHA ES LA DEL CALENDARIO
		if (isset($_REQUEST['hora_inicio'])) {
			$fecha_inicial = $fecha_inicio_filtro;
			$fecha_termino = $fecha_termino_filtro;
		}else{
			$fecha_inicial = $this->timestamp->getInicioPeriodo();
			$fecha_termino = $this->timestamp->getTerminoPeriodo();
		}
		$h1=($hora_inicio);
		$m1=($minuto_inicio);
		$h2=($hora_termino);
		$m2=($minuto_termino);
		//EN CASO DE HABER PAGINADO EL MONITOR SOLO SE CARGA LA FUNCIÓN getDetalleElementosPlus()
		if (isset($this->extra["monitor_id"]) and isset($this->extra["pagina"])) {
			$this->resultado = $this->resultado = $this->getDetalleElementosPlus($this->extra["monitor_id"], $this->extra["pagina"], 1, $fecha_inicial, $fecha_termino, $isset=1,$h1, $m1, $h2, $m2);
			return;
		}
		/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT titulo, foo.nodo_id FROM (".
			   "SELECT DISTINCT unnest(_nodos_id) AS nodo_id FROM _nodos_id(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).",'".
				pg_escape_string($fecha_inicial)."','".
				pg_escape_string($fecha_termino)."')) AS foo, nodo n ".
			   "WHERE foo.nodo_id=n.nodo_id ORDER BY orden";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		
		$datos_nodo = array();
		while ($row = $res->fetchRow()) {
			$dato_nodo = ($row["titulo"]);
			$datos_nodo[$row["nodo_id"]] = $dato_nodo;
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'contenedor_tabla_filtro.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_CONTENEDORES', 'lista_contenedores');
		$T->setBlock('tpl_tabla', 'LISTA_PATRONES_SCRIPT', 'lista_patrones_script');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS_FILTRO', 'lista_nodos_filtro');
		$T->setBlock('tpl_tabla', 'VALORES_FILTRO', 'valores_filtro');
		$T->setBlock('tpl_tabla', 'VALORES_SLIDER', 'valores_slider');
		$T->setBlock('tpl_tabla', 'BLOQUE_MONITOREOS', 'bloque_monitoreos');
		$T->setBlock('tpl_tabla', 'BLOQUE_MONITOREOS_SCRIPT', 'bloque_monitoreos_script');
		$T->setVar('__path_jquery_ui', REP_PATH_JQUERY_UI);
		$slider_inicio = ($hora_inicio * 60) + $minuto_inicio;
		$slider_termino = ($hora_termino * 60) + $minuto_termino;

		$new = (isset($_GET['new'])) ? $_GET['new'] : true;

		$T->setVar('__item_id', $this->__item_id);

		//SE PASAN LAS VARIABLES AL FILTRO QUE SE PASARON POR POST AL HACER EL FILTRADO
		$T->setVar('__nodo_selected', $nodo_filtro);
		$T->setVar('__isset', $isset);

		$T->setVar('valores_slider', '');
		$T->setVar('__valor_slider_inicio', $slider_inicio);
		$T->setVar('__valor_slider_termino', $slider_termino);
		$T->parse('valores_slider', 'VALORES_SLIDER', true);

		$T->setVar('valores_filtro', '');
		$T->setVar('__hora_inicio', $hora_inicio);
		$T->setVar('__minuto_inicio', $minuto_inicio);
		$T->setVar('__hora_termino', $hora_termino);
		$T->setVar('__minuto_termino', $minuto_termino);
		$T->parse('valores_filtro', 'VALORES_FILTRO', true);

		$T->setVar('lista_nodos_filtro', '');
		foreach ($datos_nodo as $nodo_id => $titulo_nodo) {
			if ($nodo_id != 0) {
			$T->setVar('__nodo_filtro', $titulo_nodo);
			$T->setVar('__nodoid_filtro', $nodo_id);
			}else{
				continue;
			}
			$T->parse('lista_nodos_filtro', 'LISTA_NODOS_FILTRO', true);
		}


		$orden = 1;
		$cuenta_nodos = 0;
		foreach ($datos_nodo as $nodo_id => $titulo_nodo) {
			if (!isset($_REQUEST['nodo_filtro']) || $_REQUEST['nodo_filtro'] == -1) {
				$nodo_id_filtro = $nodo_id;
				$T->setVar('__contenido_id', 'elem_'.$nodo_id_filtro);
				$T->setVar('__display_filtro', $this->__item_id);
				$T->setVar('__contenido_tabla', $this->getDetalleElementosPlus($nodo_id_filtro, 1, $orden, $fecha_inicial, $fecha_termino, $isset,$h1, $m1, $h2, $m2, $cuenta_nodos));
				$T->parse('lista_contenedores', 'LISTA_CONTENEDORES', true);
				$orden++;
			}else{
				if ($_REQUEST['nodo_filtro'] == $nodo_id) {
					$nodo_id_filtro = $_REQUEST['nodo_filtro'];
					$T->setVar('__contenido_id', 'elem_'.$nodo_id_filtro);
					$T->setVar('__display_filtro', $this->__item_id);
					$T->setVar('__contenido_tabla', $this->getDetalleElementosPlus($nodo_id_filtro, 1, $orden, $fecha_inicial, $fecha_termino, $isset,$h1, $m1, $h2, $m2, $cuenta_nodos));
					$T->parse('lista_contenedores', 'LISTA_CONTENEDORES', true);
					$orden++;
				}else{
					continue;
				}
			}
			$cuenta_nodos++;
		}
		
		$this->resultado = $T->parse('out', 'tpl_tabla');
	}


	/**
	 * Funcion que muestra los elementos plus para un monitor
	 * y pagina dados.
	 */
	// TODO: metodo getDetalleElementosPlusMediciones
	// TODO: metodo getDetalleElementosPlusMediciones
	function getDetalleElementosPlus($nodo_id_filtro, $pagina, $orden = 1, $fecha_inicial, $fecha_termino, $isset,$h1, $m1, $h2, $m2, $cuenta_nodos) {
		if ($isset != 0) {
			global $mdb2;
			global $log;
			global $current_usuario_id;
			global $usr;

			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT * FROM reporte.elementosplus_mediciones_filtro_marcado(".
			pg_escape_string($current_usuario_id).",".
			pg_escape_string($this->objetivo_id).",".
			pg_escape_string($nodo_id_filtro).",".
			(($this->subgrafico_id==1)?100:12).", ".
			pg_escape_string($pagina).", '".
			pg_escape_string($fecha_inicial)."', '".
			pg_escape_string($fecha_termino)."')";
			
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['elementosplus_mediciones_filtro_marcado']);
			$xpath = new DOMXpath($dom);
			unset($row["elementosplus_mediciones_filtro_marcado"]);

			$mantenimiento = $xpath->query('/atentus/resultados/detalles/detalle')->item(0)->getAttribute('marcado');

			if ($mantenimiento == 1) {
				if ($cuenta_nodos == 0) {
					return $this->__generarContenedorConMantenimiento();
				}else{
					return "";
				}
			}

			$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
			$conf_nodo = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$nodo_id_filtro."]")->item(0);
			$tag_mediciones = $xpath->query('//detalles/detalle[@nodo_id='.$nodo_id_filtro.']/detalles/detalle');
			
			/* SI NO HAY DATOS MOSTRAR MENSAJE */
			if ($xpath->query('//datos/dato')->length == 0 and $pagina == 1) {
				return $this->__generarContenedorSinDatos($usr->getNodo($nodo_id_filtro)->nombre);
			}

			// TEMPLATE DEL GRAFICO
			$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
			$T->setFile('tpl_tabla', 'elementos_plus_mediciones.tpl');
			$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
			$T->setBlock('tpl_tabla', 'BLOQUE_DATOS', 'bloque_datos');

			$T->setVar('__item_id', $this->__item_id);
			$T->setVar('__item_id_nuevo', REP_ITEM_ELEMENTOS_PLUS);
			$T->setVar('__item_orden', $this->extra["item_orden"]);

			$T->setVar('__monitor_id', $conf_nodo->getAttribute('nodo_id'));
			$T->setVar('__monitor_nombre', $conf_nodo->getAttribute('nombre'));
			$T->setVar('__monitor_orden', $orden);

			$linea_monitor = 1;
			$linea = 1;
			$T->setVar('bloque_datos', '');
			foreach ($tag_mediciones as $tag_medicion) {

				$T->setVar('__class', ($linea_monitor % 2 == 0)?"celdanegra15":"celdanegra10");
				$T->setVar('__fecha', $this->timestamp->getFormatearFecha($tag_medicion->getAttribute('fecha'), "d-m-Y H:i:s"));
				$T->setVar('__fechaCompleta', $tag_medicion->getAttribute('fecha'));
				$T->setVar('__pagina', $pagina);

				$medicion_error = false;
				$T->setVar('bloque_pasos', '');
				foreach ($conf_pasos as $conf_paso) {
					$tag_dato = $xpath->query('datos/dato[@paso_orden='.$conf_paso->getAttribute('paso_orden').']', $tag_medicion)->item(0);


					if ($tag_dato == null) {
						continue;
					}

					$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
					$T->setVar('__nombrePaso',$conf_paso->getAttribute('nombre'));
					$T->setVar('__idPaso',$conf_paso->getAttribute('paso_orden'));
					$T->setVar('__tamanoTotal', number_format(($tag_dato->getAttribute('tamano_total')/1024),3,',','.'));

					if (trim($tag_dato->getAttribute('respuesta')) == '') {
						$T->setVar('__tiempoTotal', 'Error en la descarga');
					} else {
						preg_match("/(?P<hor>\d{2}):(?P<min>\d{2}):(?P<seg>\d{2}).(?P<mseg>\d+)/", $tag_dato->getAttribute('respuesta'), $tiempos);
						$seg = ($tiempos["hor"]*3600)+($tiempos["min"]*60)+($tiempos["seg"]);
						$T->setVar('__tiempoTotal', $seg.'.'.$tiempos["mseg"]);
					}

					if ($tag_dato->getAttribute('estado') == "false" && trim($tag_dato->getAttribute('respuesta')) != "") {
						$T->setVar('__estadoPaso', 'spriteImg spriteImg-elementos_encontrados');
						$T->setVar('__titlePaso', 'Todos los elementos fueron descargados correctamente en este paso');
						$T->setVar('__textoPaso', 'Elementos OK');
					}
					else {
						$T->setVar('__estadoPaso', 'spriteImg spriteImg-elementos_faltantes');
						$T->setVar('__titlePaso', 'Existen elementos que no lograron ser descargados para este paso');
						$T->setVar('__textoPaso', 'Elementos Error');
						$medicion_error = true;
					}

					$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
					$linea++;
				}

				if (!$medicion_error) {
					$T->setVar('__estado', 'spriteImg spriteImg-elementos_encontrados');
					$T->setVar('__titleMonitoreo', 'Todos los elementos del monitoreo fueron descargados correctamente');
					$T->setVar('__textoMonitoreo', 'Elementos OK');
				}
				else {
					$T->setVar('__estado', 'spriteImg spriteImg-elementos_faltantes');
					$T->setVar('__titleMonitoreo', 'Existen elementos que no fueron descargados en este paso');
					$T->setVar('__textoMonitoreo', 'Elementos Error');
				}

				$T->parse('bloque_datos', 'BLOQUE_DATOS', true);
				$linea_monitor++;
			}

			/* FORMATO DE PAGINAS */
			$T->setVar('_h1', $h1);
			$T->setVar('_m1', $m1);
			$T->setVar('_h2', $h2);
			$T->setVar('_m2', $m2);
			$T->setVar('__pagina', $pagina);
			$T->setVar('__pagina_atras', $pagina-1);
			$T->setVar('__pagina_adelante', $pagina+1);
			$T->setVar('__disabled_atras', ($pagina==1)?'disabled':'');
			$T->setVar('__disabled_adelante', ($tag_mediciones->length<12)?'disabled':'');
			$T->setVar('__class_boton_atras', ($pagina==1)?'spriteButton spriteButton-atras_desactivado':'spriteButton spriteButton-atras');
			$T->setVar('__class_boton_adelante', ($tag_mediciones->length<12)?'spriteButton spriteButton-adelante_desactivado':'spriteButton spriteButton-adelante');

			if ($tag_mediciones->length == 0 and $pagina != 1) {
				return 0;
			}

			$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));

			return $this->resultado = $T->parse('out', 'tpl_tabla');
		}
	}

	/**
	 * Funcion que muestra la lista de registros plus
	 * para todos los monitores.
	 */
	// TODO: metodo getRegistrosPlusMediciones
	function getRegistrosPlus() {
		if (isset($this->extra["monitor_id"]) and isset($this->extra["pagina"])) {
			$this->resultado = $this->getDetalleRegistrosPlus($this->extra["monitor_id"], $this->extra["pagina"]);
			return;
		}

		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT foo.nodo_id FROM (".
			   "SELECT DISTINCT unnest(_nodos_id) AS nodo_id FROM _nodos_id(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).",'".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')) AS foo, nodo n ".
			   "WHERE foo.nodo_id=n.nodo_id ORDER BY orden";

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$monitor_ids = array();
		while ($row = $res->fetchRow()) {
			$monitor_ids[] = $row["nodo_id"];
		}

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (count($monitor_ids) == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'contenedor_tabla.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_CONTENEDORES', 'lista_contenedores');

		/* LISTA DE MONITORES */
		$orden = 1;
		foreach ($monitor_ids as $monitor_id) {
			$T->setVar('__contenido_id', 'regplus_'.$monitor_id);
			$T->setVar('__contenido_tabla', $this->getDetalleRegistrosPlus($monitor_id, 1, $orden));
			$T->parse('lista_contenedores', 'LISTA_CONTENEDORES', true);
		}

		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	// TODO: metodo getDetalleRegistrosPlusMediciones
	function getDetalleRegistrosPlus($monitor_id, $pagina, $orden = 1) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		// OBTENER LOS DATOS Y PARSEARLO
		$sql = "SELECT * FROM reporte.registrosplus(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."',".
				pg_escape_string($monitor_id).", ".
				(($this->subgrafico_id==1)?500:12).", ".
				pg_escape_string($pagina).") ";
		$res =& $mdb2->query($sql);
//		echo $sql;
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row['registrosplus']);
		$xpath = new DOMXpath($dom);

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (
			($xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]")->length == 0 )
			or ($xpath->query("//detalle[@nodo_id=".$monitor_id."]/detalles/detalle")->length == 0 and $pagina == 1)
			or $xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->length == 0
		) {
			return $this->__generarContenedorSinDatos(($xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->length == 0)?'':$xpath->query("//nodos/nodo[@nodo_id=$monitor_id]")->item(0)->getAttribute('nombre'));
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));

		$this->resultado = $this->__getTemplateDetalleRegistrosPlus($xpath, $monitor_id, $pagina, $orden);
		return $this->resultado;
	}

	function getRegistrosPlusErrores() {
		if (isset($this->extra["pagina"])) {
			$this->resultado = $this->getDetalleRegistrosPlusErrores($this->extra["pagina"]);
			return;
		}

		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'contenedor_tabla.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_CONTENEDORES', 'lista_contenedores');

		$T->setVar('__contenido_id', 'regplus_0');
		$T->setVar('__contenido_tabla', $this->getDetalleRegistrosPlusErrores(1));
		$T->parse('lista_contenedores', 'LISTA_CONTENEDORES', true);

		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	function getDetalleRegistrosPlusErrores($pagina) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		// OBTENER LOS DATOS Y PARSEARLO
		$sql = "SELECT * FROM reporte.registrosplus(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."',".
				pg_escape_string(0).", ".
				(($this->subgrafico_id==1)?500:20).", ".
				pg_escape_string($pagina).") ";
//		echo $sql;
		$res =& $mdb2->query($sql);

		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row['registrosplus']);
		$xpath = new DOMXpath($dom);

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (
			($xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]")->length == 0)
			or ($xpath->query("//detalle[@nodo_id=0]/detalles/detalle")->length == 0 and $pagina == 1)
			or $xpath->query("//nodos/nodo[@nodo_id=0]")->length == 0
		) {
			return $this->__generarContenedorSinDatos(($xpath->query("//nodos/nodo[@nodo_id=0]")->length == 0)?'':$xpath->query("//nodos/nodo[@nodo_id=0]")->item(0)->getAttribute('nombre'));
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));

		$this->resultado = $this->__getTemplateDetalleRegistrosPlus($xpath, 0, $pagina, 1);
		return $this->resultado;
	}

	function __getTemplateDetalleRegistrosPlus($xpath, $monitor_id, $pagina, $orden) {
		$conf_nodo = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$monitor_id."]")->item(0);
		$conf_pasos = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]");
		$tag_mediciones = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=".$monitor_id."]/detalles/detalle");

		if ($tag_mediciones->length == 0 and $pagina != 1) {
			return 0;
		}

		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'registros_plus_mediciones.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_REGISTROS', 'bloque_registros');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASO_NOMBRE', 'bloque_paso_nombre');
		$T->setBlock('tpl_tabla', 'BLOQUE_PATRONES', 'bloque_patrones');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_MONITOREOS', 'bloque_monitoreos');

		$T->setVar('__item_id', $this->__item_id);
		$T->setVar('__item_orden', $this->extra["item_orden"]);

		$T->setVar('__monitor_id', $conf_nodo->getAttribute('nodo_id'));
		$T->setVar('__monitor_nombre', $conf_nodo->getAttribute('nombre'));
		$T->setVar('__monitor_orden', $orden);
		$T->setVar('__monitor_display', ($monitor_id == 0)?'':'none');

		$linea = 1;
		$linea_medicion = 1;
		foreach ($tag_mediciones as $tag_medicion) {
			if ($monitor_id == 0) {
				$conf_nodo = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$tag_medicion->getAttribute('nodo_id')."]")->item(0);
			}

			$T->setVar('__monitoreo_desde', $conf_nodo->getAttribute('nombre'));
			$T->setVar('__monitoreo_fecha', date("d-m-Y H:i:s", strtotime($tag_medicion->getAttribute('fecha'))));
			$T->setVar('__monitoreo_fecha_completa', $tag_medicion->getAttribute('fecha'));
			$T->setVar('__class', ($linea_medicion % 2 == 0)?"celdanegra15":"celdanegra10");

			$estado_general_id = 0;
			$T->setVar('bloque_pasos', '');
			foreach ($conf_pasos as $conf_paso) {
				$tag_dato = $xpath->query("detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato", $tag_medicion)->item(0);
				$tag_patrones = $xpath->query("patrones/patron", $tag_dato);

				$T->setVar('__paso_id', $conf_paso->getAttribute('paso_orden'));
				$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
				$T->setVar('__paso_ip', ($tag_dato->getAttribute('ip') != "" and $tag_dato->getAttribute('ip') != -1)?($tag_dato->getAttribute('ip')):'IP no encontrada');
				$T->setVar('__paso_rowspan', $tag_patrones->length);

				$T->setVar('bloque_registros', '');
				foreach ($xpath->query("registros/registro", $tag_dato) as $tag_registro){
					$T->setVar('__registro_valor', htmlentities($tag_registro->getAttribute('valor')));
					$T->setVar('__registro_nombre', strip_tags($tag_registro->getAttribute('nombre')));
					$T->parse('bloque_registros', 'BLOQUE_REGISTROS', true);
				}

				$T->setVar('bloque_patrones', '');
				foreach ($tag_patrones as $tag_patron) {
					$T->setVar('bloque_paso_nombre', '');
					if ($tag_patron->getAttribute('orden') == 0) {
						$T->parse('bloque_paso_nombre', 'BLOQUE_PASO_NOMBRE', false);
					}

					$T->setVar('__patron_id', $tag_patron->getAttribute('orden'));
					$T->setVar('__patron_inverso', ($tag_patron->getAttribute('es_inverso')==0)?'No':'Si');
					$T->setVar('__patron_opcional', ($tag_patron->getAttribute('es_opcional')==0)?'Si':'No');
					$T->setVar('__patron_nombre', $tag_patron->getAttribute('nombre'));
					$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");

					$codigo_ids = explode(',', $tag_dato->getAttribute('estado'));
					$conf_codigo = $xpath->query("/atentus/resultados/propiedades/codigos/codigo[@codigo_id=".$codigo_ids[$tag_patron->getAttribute('orden')]."]")->item(0);

					$T->setVar('__evento_nombre', $conf_codigo->getAttribute('nombre'));
					$T->setVar('__evento_color', $conf_codigo->getAttribute('color'));
					$T->setVar('__evento_imagen', REP_PATH_SPRITE_CODIGO.substr($conf_codigo->getAttribute('icono'), 0, -4));
					$T->setVar('__evento_descripcion', $conf_codigo->getAttribute('descripcion'));
					$T->parse('bloque_patrones', 'BLOQUE_PATRONES', true);

					if ($codigo_ids[$tag_patron->getAttribute('orden')] != 0) {
						$estado_general_id = 1;
					}
					$linea++;
				}
				$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
			}

			$T->setVar('__monitoreo_evento_icono', ($estado_general_id == 0)?'spriteSemaforo spriteSemaforo-verde':'spriteSemaforo spriteSemaforo-rojo');
			$T->parse('bloque_monitoreos', 'BLOQUE_MONITOREOS', true);
			$linea_medicion++;
		}

		/* FORMATO DE PAGINAS */
		$T->setVar('__pagina', $pagina);
		$T->setVar('__pagina_atras', $pagina-1);
		$T->setVar('__pagina_adelante', $pagina+1);
		$T->setVar('__disabled_atras', ($pagina==1)?'disabled':'');
		$T->setVar('__disabled_adelante', ($tag_mediciones->length<12)?'disabled':'');
		$T->setVar('__class_boton_atras', ($pagina==1)?'spriteButton spriteButton-atras_desactivado':'spriteButton spriteButton-atras');
		$T->setVar('__class_boton_adelante', ($tag_mediciones->length<12)?'spriteButton spriteButton-adelante_desactivado':'spriteButton spriteButton-adelante');

		return $T->parse('out', 'tpl_tabla');
	}

	function getAudioCdn(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		include 'utils/lista_audio.php';

		if (isset($_REQUEST['hora_inicio'])) {
			$hora_inicio = $_REQUEST['hora_inicio'];
			$minuto_inicio = $_REQUEST['minuto_inicio'];
			$hora_termino = $_REQUEST['hora_termino'];
			$minuto_termino = $_REQUEST['minuto_termino'];
		}else{
			$hora_termino  = '23';
			$minuto_termino  = '59';
			$hora_inicio = '00';
			$minuto_inicio  = '00';
		}

		if (isset($_REQUEST['nodo_filtro'])) {
			$nodo_filtro = $_REQUEST['nodo_filtro'];
			$isset = 1;
		}else{
			$nodo_filtro = -1;
			$isset = 0;
		}

		//PAGINA ACTUAL
		if (isset($_REQUEST['pagina'])) {
			$pagina = $_REQUEST['pagina'];
		}
		else {
			$pagina = "1";
		}

		//OBTENER TIMEZONE DEL USUARIO
		$sql2 = "SELECT _cliente_tz(".pg_escape_string($current_usuario_id).")";
		$res0 =& $mdb2->query($sql2);

		if (MDB2::isError($res0)) {
			$log->setError($sql2, $res0->userinfo);
			exit();
		}
		$row = $res0->fetchRow();
		$tz= $row["_cliente_tz"];

		date_default_timezone_set($tz);

		//OBTIENE EL NOMBRE DE LOS PASOS DEL OBJETIVO
		$sql_nombres = "SELECT nombre_pasos(".pg_escape_string($this->objetivo_id).")";
		$res_nombres =& $mdb2->query($sql_nombres);

		if (MDB2::isError($res_nombres)) {
			$log->setError($sql_nombres, $res_nombres->userinfo);
			exit();
		}
		$row = $res_nombres->fetchRow();
		$nombres_paso = $row["nombre_pasos"];

		//FECHA SETEADA EN UTC
		$fecha_inicio_contz = new DateTime(pg_escape_string($this->timestamp->getInicioPeriodo()));
		$fecha_inicio_contz->setTimezone(new DateTimeZone("UTC"));

		$fecha_termino_contz = new DateTime(pg_escape_string($this->timestamp->getInicioPeriodo()));
		$fecha_termino_contz->setTimezone(new DateTimeZone("UTC"));

		//SE GUARDA DIA MES Y AÑO EN VARIABLE Y SE LE AGREGA LA HORA QUE SE PASA POR POST POR LOS INPUTS DEL FILTRO
		$fecha_inicial = date("Y-m-d", strtotime($this->timestamp->getInicioPeriodo()));
		$fecha_inicio_filtro = $fecha_inicial.' '.$hora_inicio.':'.$minuto_inicio.':00';

		$fecha_terminal = date("Y-m-d", strtotime($this->timestamp->getInicioPeriodo()));
		$fecha_termino_filtro = $fecha_terminal.' '.$hora_termino.':'.$minuto_termino.':59';

		//SETEO DE FECHA DE FILTRO EN UTC
		$fecha_inicio_filtro_tz = new DateTime(pg_escape_string($fecha_inicio_filtro));
		$fecha_inicio_filtro_tz->setTimezone(new DateTimeZone("UTC"));

		$fecha_termino_filtro_tz = new DateTime(pg_escape_string($fecha_termino_filtro));
		$fecha_termino_filtro_tz->setTimezone(new DateTimeZone("UTC"));

		//SI SE ACTIVA EL FILTRO SE GUARDA FECHA PASADA POR POST SINO LA FECHA ES LA DEL CALENDARIO
		if (isset($_REQUEST['hora_inicio'])) {
			$fecha_inicio = $fecha_inicio_filtro_tz;
			$fecha_termino = $fecha_termino_filtro_tz;
		}else{
			$fecha_inicio = $fecha_inicio_contz;
			$fecha_termino = $fecha_termino_contz;
		}

		//se obtienen los nodos asignados al objetivo
		$sql1 = "SELECT p.monitor_id , m.nombre, m.nodo_id, n.nombre FROM(
					SELECT DISTINCT UNNEST(oc.monitor_id) AS monitor_id FROM public.objetivo_config AS oc
					WHERE
						oc.objetivo_id      = 	(".pg_escape_string($this->objetivo_id).") AND
						oc.es_ultima_config = 'true'
					) AS p,
				monitor AS m, 
				nodo AS n
				WHERE
				m.monitor_id = p.monitor_id AND
				n.nodo_id = m.nodo_id";

		$res1 =& $mdb2->query($sql1);
		if (MDB2::isError($res1)) {
			$log->setError($sql1, $res1->userinfo);
			exit();
		}

		$datos_nodo = array();
		while ($row = $res1->fetchRow()) {
			$dato_nodo = ($row["nombre"]);
			$datos_nodo[$row["monitor_id"]] = $dato_nodo;
		}
		//obtiene los datos de audio resultado
		$json_audio_resultado = get_audio_listado_resultado($this->objetivo_id, $fecha_inicio->format("Y-m-d H:i"), $fecha_termino->format("Y-m-d H:i"), $nodo_filtro, 'resultado');
		$json_dec_resultado = json_decode($json_audio_resultado, true);

		//obtiene los datos de audio patron
		$json_audio_patron = get_audio_listado_resultado($this->objetivo_id, $fecha_inicio->format("Y-m-d H:i"), $fecha_termino->format("Y-m-d H:i"), '-1', 'patron');
		$json_dec_patron = json_decode($json_audio_patron, true);
			
		if ($json_dec_resultado['Estado'] != 'Sin datos') {
			$arr = array();
			foreach($json_dec_resultado as $key => $item){
				$arr[$item['fecha'].",".$item['monitor_id']][$key] = $item;
			}
		}
		
		$cant_datos=count($arr);
		$cant_paginas=ceil($cant_datos/10);
		$first_ele=($pagina*10)-10;

		$diez_elementos=array_slice($arr, $first_ele, 10);
		$validar_contenido=count($diez_elementos);

		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'audio_cdn.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS_FILTRO', 'lista_nodos_filtro');
		$T->setBlock('tpl_tabla', 'VALORES_FILTRO', 'valores_filtro');
		$T->setBlock('tpl_tabla', 'VALORES_SLIDER', 'valores_slider');
		$T->setBlock('tpl_tabla', 'BLOQUE_MONITOREOS', 'bloque_monitoreos');
		$T->setBlock('tpl_tabla', 'BLOQUE_DE_PATRONES', 'bloque_de_patrones');
		$T->setBlock('tpl_tabla', 'BLOQUE_DE_PASOS', 'bloque_de_pasos');

		$T->setVar('bloque_de_pasos', '');
		$array_patron_json[]  = array();
		$array_json[]  = array();
		$nombre_pasos = explode(',', $nombres_paso);
		$nombre_pasos_format = str_replace('"', "", $nombre_pasos);
		$nombre_pasos_format = str_replace('{', "", $nombre_pasos_format);
		$nombre_pasos_format = str_replace('}', "", $nombre_pasos_format);
		$nombre_pasos_format = array_splice($nombre_pasos_format, 1);
		foreach ($nombre_pasos_format as $index_paso => $nombre_paso){
			$cont=0;
			foreach($json_dec_patron as $index_patron => $patron){
				if ($index_paso+1 == $patron['paso_orden']) {
					$array_patron_json[$cont]['hash_md5'] = $patron['hash_md5'];
					$array_patron_json[$cont]['direccion'] = $patron['direccion'];
					$cont++;
				}
			}
			$i=1;
			$array_json=$array_patron_json;
			unset($array_patron_json);
			$T->setVar('bloque_de_patrones', '');
			foreach ($array_json as $key => $info_patron) {
				$direccion_patron = explode('/', $info_patron['direccion']);
				$nombre_patron = str_replace('.wav', "", $direccion_patron[7]);
				$T->setVar('__hash_patron', $info_patron['hash_md5']);
				$T->setVar('__nombre_patron', $nombre_patron);
				$T->setVar('__id_patron', 'id_patron_'.$i);
				$T->setVar('__nombre_paso', $nombre_paso);
				$T->parse('bloque_de_patrones', 'BLOQUE_DE_PATRONES', true);
				$i++;
			}
			$T->setVar('__nombre_paso_patron', $nombre_paso);
			$T->parse('bloque_de_pasos', 'BLOQUE_DE_PASOS', true);
		}
		
		$slider_inicio = ($hora_inicio * 60) + $minuto_inicio;
		$slider_termino = ($hora_termino * 60) + $minuto_termino;

		$T->setVar('__item_id', $this->__item_id);

		//SE PASAN LAS VARIABLES AL FILTRO QUE SE PASARON POR POST AL HACER EL FILTRADO
		$T->setVar('__nodo_selected', $nodo_filtro);
		$T->setVar('__isset', $isset);

		$T->setVar('valores_slider', '');
		$T->setVar('__valor_slider_inicio', $slider_inicio);
		$T->setVar('__valor_slider_termino', $slider_termino);
		$T->parse('valores_slider', 'VALORES_SLIDER', true);

		$T->setVar('valores_filtro', '');
		$T->setVar('__hora_inicio', $hora_inicio);
		$T->setVar('__minuto_inicio', $minuto_inicio);
		$T->setVar('__hora_termino', $hora_termino);
		$T->setVar('__minuto_termino', $minuto_termino);
		$T->parse('valores_filtro', 'VALORES_FILTRO', true);

		if ($validar_contenido == 0) {
			$tiene_mediciones=false;
			$T->setVar('bloque_monitoreos', $this->__generarContenedorSinFiltroAudio());
		}
		else{
			$tiene_mediciones=true;
		}
		$tiene_mediciones=($tiene_mediciones)?'1':'0';
		$T->setVar('lista_nodos_filtro', '');
		foreach ($datos_nodo as $key=>$tag_nodo_filtro) {
			if ($key != 0) {
				$T->setVar('__nodo_filtro', $tag_nodo_filtro);
				$T->setVar('__nodoid_filtro', $key);	
			}else{
				continue;
			}
			$T->parse('lista_nodos_filtro', 'LISTA_NODOS_FILTRO', true);
		}
		$sql_paso="SELECT (xpath('/atentus/config/*/paso/@visible', xml_configuracion))::TEXT[]::INTEGER[] FROM objetivo_config WHERE objetivo_id = (".pg_escape_string($this->objetivo_id).") and es_ultima_config='t'";
		$res =& $mdb2->query($sql_paso);
		if (MDB2::isError($res)) {
			$log->setError($sql_paso, $res->userinfo);
			exit();
		}
		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row["xpath"]);
		$xpath = new DOMXpath($dom);
		$orden_paso_visible=$row["xpath"];

		$numero_paso_visible = explode(',', $orden_paso_visible);
		$numero_paso_visible = str_replace('"', "", $numero_paso_visible);
		$numero_paso_visible = str_replace('{', "", $numero_paso_visible);
		$numero_paso_visible = str_replace('}', "", $numero_paso_visible);
		$numero_paso_visible = array_splice($numero_paso_visible, 0);

		//ARRAY DATOS DEL PATRON
		$array_patron[] = array();
		//RECORRER DE 10 EN 10 LOS ELEMENTOS DEL ARCHIVO JSON
		foreach ($diez_elementos as $key => $elementos) {
			$fecha_monitor_id = explode(",", $key);
			$fecha_monitoreo = $fecha_monitor_id[0];
			$monitor_id = $fecha_monitor_id[1];

			if ($elementos['Estado'] != 'Sin Datos') {
				//FOR PARA LLENAR VARIABLES Y PASARLAS AL TPL
				$T->setVar('lista_pasos', '');
				$array_patron[] = array();
				$contador = 0;

				$contador_invisible=0;
				foreach ($elementos as $key => $elemento) {

					if($numero_paso_visible[$contador+1]==1){
						$contador_invisible++;
						$token = $elemento['hash_md5'];
						$paso_orden = $elemento['paso_orden'];
						if ($json_dec_patron['Estado'] != 'Sin datos') {
							foreach($json_dec_patron as $key => $item){

								$array_direccion = explode('/', $item['direccion']);
								$array_paso = explode('-', $array_direccion[7]);
								$array_paso_numero = explode('o', $array_paso[0]);
								if ($array_paso_numero[1] == $paso_orden) {
									$array_patron_hash[0]['paso'] = $array_paso[0];
									$array_patron_hash[0]['hash_md5'] = $item['hash_md5'];
								}
							$T->setVar('_hash_md5', $item['hash_md5']);
							}
						}

						//CONSULTA PARA OBTENER DATOS DE RESULTADO DE AUDIO
						$sql = "SELECT _datos_ivr_cdn(".($this->objetivo_id).", '" .($fecha_monitoreo)."' ,".($monitor_id).",".($paso_orden).")";
						$res =& $mdb2->query($sql);
						if (MDB2::isError($res)) {
							$log->setError($sql, $res->userinfo);
							exit();
						}

						$row = $res->fetchRow();
						$dom = new DomDocument();
						$dom->preserveWhiteSpace = FALSE;
						$dom->loadXML($row["_datos_ivr_cdn"]);
						$xpath = new DOMXpath($dom);
						unset($row["_datos_ivr_cdn"]);
						$conf_objetivo = $xpath->query('/objetivo')->item(0);
						$conf_paso = $xpath->query('paso',$conf_objetivo)->item(0);
						$conf_nodo = $xpath->query('nodo',$conf_objetivo)->item(0);
						$conf_patrones = $xpath->query('audio_patron', $conf_paso);
						$cant_patron = $xpath->query('audio_patron', $conf_paso)->length;

						$T->setVar('__cant_patron', $cant_patron);
						$T->setVar('__nombre_monitor', $conf_nodo->getAttribute('nombre_nodo'));
						$error = '';
						if ($cant_patron != 0) {
							foreach ($conf_patrones as $conf_patron) {
								$conf_codigo = $xpath->query('codigo', $conf_paso)->item(0);

								$T->setVar('__codigo_icono', substr($conf_codigo->getAttribute('icono_codigo'), 0, -4));
								$T->setVar('__color_codigo', $conf_codigo->getAttribute('color_codigo'));
							}

							$estado = $conf_codigo->getAttribute('codigo_id');
							if ($estado != 0) {
								$error = true;
								$clase_tooltip = 'tooltiptextError';
							}else{
								$error = false;
								$clase_tooltip = 'tooltiptextOk';
							}

							$array_patron[0]['hash_md5'] = $array_patron_hash[0]["hash_md5"];
							$array_patron[0]['codigo'] = substr($conf_codigo->getAttribute('icono_codigo'), 0, -4);
							$array_patron[0]['color'] = $conf_codigo->getAttribute('color_codigo');
							$array_patron[0]['nombre_cod'] = $conf_codigo->getAttribute('nombre_codigo');
							$array_patron[0]['codigo_id'] = $clase_tooltip;

							$json_patron = json_encode($array_patron);
							$T->setVar('__json_patron', $json_patron);

							date_default_timezone_set('UTC');
							$fecha_tz_modal = new DateTime(pg_escape_string($fecha_monitoreo));
							$fecha_tz_modal->setTimezone(new DateTimeZone($tz));

						    //DATOS MINIATURAS Y MODAL
							$T->setVar('__paso_warning', ($error)?'url(img/warning.png) no-repeat 112px 2px':'');
							$T->setVar('__paso_id', $paso_orden);
							$T->setVar('__token_resultado', $token);
							$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre_paso'));
							$T->setVar('__monitoreo_fecha_modal', $fecha_tz_modal->format("Y-m-d H:i:s"));
							$T->parse('lista_pasos', 'LISTA_PASOS', true);
						}
					}
					$contador++;
				}
				date_default_timezone_set('UTC');
				//PASAR FECHA UTC A FECHA SEGUN ZONA HORARIA DEL USUARIO/////
				$fecha_tz = new DateTime(pg_escape_string($fecha_monitoreo));
				$fecha_tz->setTimezone(new DateTimeZone($tz));

				$resta_contador=$contador-$contador_invisible;
				//ANCHO DE LA TABLA DONDE SE ECNUENTRAN TODOS LOS AUDIOS (720 ES EL MINIMO)
				$ancho = ($contador-$resta_contador) * 150;
				$fecha_id = strtotime($fecha_monitoreo);
				
				$T->setVar('__monitoreo_ancho', $ancho);
				$T->setVar('__monitoreo_id', $monitor_id);
				$T->setVar('__fecha_int', $fecha_id);
				$T->setVar('__monitoreo_fecha', $fecha_tz->format("Y-m-d H:i:s"));
				$T->setVar('__boton_adelante_display', ($ancho>=720)?'inline':'none');
				$T->parse('bloque_monitoreos', 'BLOQUE_MONITOREOS', true);
			}else{
				continue;
			}
		}

		//FIN FOREACH
		$array_pag=array();
		for ($i=0; $i <= $cant_paginas; $i++) {
			$array_pag[$i]=$i+1;
		}
		$array_pag=json_encode($array_pag);

		//ICONOS PARA PAGINACION
		$T->setVar('__wavesurfer', REP_PATH_WAVESURFER);
		$T->setVar('__path_jquery_ui', REP_PATH_JQUERY_UI);
		
		$T->setVar('_h_slider', $hora_inicio);
		$T->setVar('_m_slider', $minuto_inicio);
		$T->setVar('_h2_slider', $hora_termino);
		$T->setVar('_m2_slider', $minuto_termino);
		$T->setVar('__arr_pag', $array_pag);
		$T->setVar('__cant_pag', $cant_paginas);
		$T->setVar('__pagina', $pagina);
		$T->setVar('__pagina_atras', $pagina-1);
		$T->setVar('__pagina_adelante', $pagina+1);
		$T->setVar('__disabled_atras', ($pagina==1)?'disabled':'');
		$T->setVar('__disabled_adelante', ($pagina == $cant_paginas)?'disabled':'');
		$T->setVar('__class_boton_atras', ($pagina==1)?'spriteButton spriteButton-atras_desactivado':'spriteButton spriteButton-atras');
		$T->setVar('__class_boton_adelante', ($pagina == $cant_paginas)?'spriteButton spriteButton-adelante_desactivado':'spriteButton spriteButton-adelante');
		$T->setVar('__mediciones', $tiene_mediciones);

		return $this->resultado = $T->parse('out', 'tpl_tabla');
	}

	function getScreenshot(){
		$objetivo = new ConfigObjetivo($this->objetivo_id);
		$servicio_id = $objetivo->getServicio()->servicio_id;
		if ($servicio_id==256||$servicio_id==700||$servicio_id==255||$servicio_id==257||$servicio_id==258||$servicio_id==270||$servicio_id==271||$servicio_id==272||$servicio_id==290) {
			$this->resultado = $this->getScreenshotCdn();
		}else{
			$this->resultado = $this->getScreenshotNormal();
		}
	}

	function getScreenshotCdn(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		include 'utils/get_remote_image_lista.php';

		if (isset($_REQUEST['hora_inicio'])) {
			$hora_inicio = $_REQUEST['hora_inicio'];
			$minuto_inicio = $_REQUEST['minuto_inicio'];
			$hora_termino = $_REQUEST['hora_termino'];
			$minuto_termino = $_REQUEST['minuto_termino'];
		}else{
			$hora_termino  = '23';
			$minuto_termino  = '59';
			$hora_inicio = '00';
			$minuto_inicio  = '00';
		}
		if (isset($_REQUEST['nodo_filtro'])) {
			$nodo_filtro = $_REQUEST['nodo_filtro'];
			$isset = 1;
		}else{
			$nodo_filtro = -1;
			$isset = 0;
		}

		//OBTENER TIMEZONE DEL USUARIO
		$sql2 = "SELECT _cliente_tz"."(".pg_escape_string($current_usuario_id).")";
		$res0 =& $mdb2->query($sql2);

		if (MDB2::isError($res0)) {
		$log->setError($sql2, $res0->userinfo);
		exit();
		}
		$row = $res0->fetchRow();
		$tz= $row["_cliente_tz"];

		date_default_timezone_set($tz);

		/////FECHA SETEADA EN UTC
		$fecha_inicio_contz = new DateTime(pg_escape_string($this->timestamp->getInicioPeriodo()));
		$fecha_inicio_contz->setTimezone(new DateTimeZone("UTC"));

		$fecha_termino_contz = new DateTime(pg_escape_string($this->timestamp->getInicioPeriodo()));
		$fecha_termino_contz->setTimezone(new DateTimeZone("UTC"));
		//SE GUARDA DIA MES Y AÑO EN VARIABLE Y SE LE AGREGA LA HORA QUE SE PASA POR POST POR LOS INPUTS DEL FILTRO
		$fecha_inicial = date("Y-m-d", strtotime($this->timestamp->getInicioPeriodo()));

		$fecha_inicio_filtro = $fecha_inicial.' '.$hora_inicio.':'.$minuto_inicio.':00';

		$fecha_terminal = date("Y-m-d", strtotime($this->timestamp->getInicioPeriodo()));
		$fecha_termino_filtro = $fecha_terminal.' '.$hora_termino.':'.$minuto_termino.':59';
		
		//SETEO DE FECHA DE FILTRO
		$fecha_inicio_filtro_tz = new DateTime(pg_escape_string($fecha_inicio_filtro));
		$fecha_inicio_filtro_tz->setTimezone(new DateTimeZone("UTC"));

		$fecha_termino_filtro_tz = new DateTime(pg_escape_string($fecha_termino_filtro));
		$fecha_termino_filtro_tz->setTimezone(new DateTimeZone("UTC"));
		//SI SE ACTIVA EL FILTRO SE GUARDA FECHA PASADA POR POST SINO LA FECHA ES LA DEL CALENDARIO
		
		if (isset($_REQUEST['hora_inicio'])) {
			$fecha_inicio = $fecha_inicio_filtro_tz;
			$fecha_termino = $fecha_termino_filtro_tz;
		}else{
			$fecha_inicio = $fecha_inicio_contz;
			$fecha_termino = $fecha_termino_contz;
		}

		$h_slider=($hora_inicio);
		$m_slider=($minuto_inicio);
		$h2_slider=($hora_termino);
		$m2_slider=($minuto_termino);

		///PAGINA ACTUAL
		if (isset($_REQUEST['pagina'])) {
			$pagina = $_REQUEST['pagina'];
		}
		else {
			$pagina = "1";
		}
		$sql1 = "SELECT p.monitor_id , m.nombre, m.nodo_id, n.nombre FROM(
					SELECT DISTINCT UNNEST(oc.monitor_id) AS monitor_id FROM public.objetivo_config AS oc
				WHERE
			        oc.objetivo_id      = 	(".pg_escape_string($this->objetivo_id).") AND
   					oc.es_ultima_config = 'true'
				) AS p,
				monitor AS m, 
				nodo AS n
				WHERE
				m.monitor_id = p.monitor_id AND
				n.nodo_id = m.nodo_id";

		$res1 =& $mdb2->query($sql1);
		if (MDB2::isError($res1)) {
			$log->setError($sql1, $res1->userinfo);
			exit();
		}
		$datos_nodo = array();
		while ($row = $res1->fetchRow()) {
			$dato_nodo = ($row["nombre"]);
			$datos_nodo[$row["monitor_id"]] = $dato_nodo;
		}
		$sql_pasos="SELECT 
        (xpath('//paso[@visible=1]/@paso_orden', xml_configuracion))::TEXT[]::INTEGER[]
      	FROM
        objetivo_config
     	WHERE
        objetivo_id = (".pg_escape_string($this->objetivo_id).") and es_ultima_config='t'
        limit 1";
        $res_sql =& $mdb2->query($sql_pasos);

		if (MDB2::isError($res_sql)) {
		$log->setError($sql_pasos, $res_sql->userinfo);
		exit();
		}
		$row = $res_sql->fetchRow();
		$pasos_visible= $row["xpath"];
		$pasos_visible= str_replace("{", "", $pasos_visible);
		$pasos_visible = str_replace("}", "", $pasos_visible);
		$json = get_listado($this->objetivo_id, $fecha_inicio->format("Y-m-dTH:i:s"), $fecha_termino->format("Y-m-dTH:i:s"), $nodo_filtro, $pasos_visible);
    	$data_imagenes=json_decode($json, true);
    	$json_deco=json_encode($json, true);
    	$cant_datos=count($data_imagenes);
		$cant_paginas=ceil($cant_datos/10);
		$first_ele=($pagina*10)-10;
		//MUESTRA 10 DATO DE MONITOREO
		$diez_elementos=array_slice($data_imagenes, $first_ele, 10);
		$validar_contenido=count($diez_elementos);

		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'screenshot_cdn.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_SCREENSHOT', 'lista_screenshot');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS_FILTRO', 'lista_nodos_filtro');
		$T->setBlock('tpl_tabla', 'VALORES_FILTRO', 'valores_filtro');
		$T->setBlock('tpl_tabla', 'VALORES_SLIDER', 'valores_slider');
		$T->setBlock('tpl_tabla', 'BLOQUE_MONITOREOS', 'bloque_monitoreos');
		$T->setVar('__path_jquery_ui', REP_PATH_JQUERY_UI);
		$objetivo = new ConfigObjetivo($this->objetivo_id);
		$servicio_id = $objetivo->getServicio()->servicio_id;
		
		if ($servicio_id==700) {
			$tipo="mobile";
			$T->setvar('__servicio', $tipo);
		}elseif ($servicio_id==290) {
			$tipo="meta";
			$T->setvar('__servicio', $tipo);
		}else{
			$tipo="screenshot";
			$T->setvar('__servicio', $tipo);
		}

		$slider_inicio = ($hora_inicio * 60) + $minuto_inicio;
		$slider_termino = ($hora_termino * 60) + $minuto_termino;
		$new = (isset($_GET['new'])) ? $_GET['new'] : true;

		$T->setVar('__item_id', $this->__item_id);

		//SE PASAN LAS VARIABLES AL FILTRO QUE SE PASARON POR POST AL HACER EL FILTRADO
		$T->setVar('__nodo_selected', $nodo_filtro);
		$T->setVar('__isset', $isset);
		$T->setVar('valores_slider', '');
		$T->setVar('__valor_slider_inicio', $slider_inicio);
		$T->setVar('__valor_slider_termino', $slider_termino);
		$T->parse('valores_slider', 'VALORES_SLIDER', true);

		$T->setVar('valores_filtro', '');
		$T->setVar('__hora_inicio', $hora_inicio);
		$T->setVar('__minuto_inicio', $minuto_inicio);
		$T->setVar('__hora_termino', $hora_termino);
		$T->setVar('__minuto_termino', $minuto_termino);
		$T->parse('valores_filtro', 'VALORES_FILTRO', true);
		/////VALIDAR SI SE TRAEN DATOS A SCREENSHOT, SINO SE MUESTRA PANTALLA QUE NO HAY DATOS
		if ($validar_contenido == 0) {
			$tiene_mediciones=false;
            	$T->setVar('bloque_monitoreos', $this->__generarContenedorSinFiltro());
			}
		else{
			$T->setVar('bloque_monitoreos', '');
			$tiene_mediciones=true;
		}
		$tiene_mediciones=($tiene_mediciones)?'1':'0';
		$T->setVar('lista_nodos_filtro', '');
		foreach ($datos_nodo as $key=>$tag_nodo_filtro) {
			if ($key != 0) {
				$T->setVar('__nodo_filtro', $tag_nodo_filtro);
				$T->setVar('__nodoid_filtro', $key);	
			}else{
				continue;
			}
			$T->parse('lista_nodos_filtro', 'LISTA_NODOS_FILTRO', true);
		}
		///ARRAY DATOS DEL PATRON
		$array_patron[] = array();
		/////RECORRER DE 10 EN 10 LOS ELEMENTOS DEL ARCHIVO JSON
		foreach ($diez_elementos as $key => $elementos) {
			$contador=0;
			$array_json = explode(',', $elementos['detalle_fz_screenshot']);
			$largo_array= count($array_json)-1;
			$T->setVar('lista_pasos', '');
			$fecha_monitoreo=str_replace('"', "", $array_json[2]);
			$monitor=$array_json[1];
			/////FOR PARA LLENAR VARIABLES Y PASARLAS AL TPL
			for ($i=3; $i <= $largo_array; $i++) {
				$pasos=($i-3);
					$error = false;
					//REEMPLAZA LAS LLAVES DE LOS TOKENS EN UN PASO O MAS DE UNO
					$posicion_coincidencia = strpos($array_json[$largo_array], '}")');
					if ($posicion_coincidencia == true) {
						//MAS DE UN PASO
						$array_json[3]=str_replace('"{', "", $array_json[3]);
						$array_json[$largo_array]=str_replace('}")', "", $array_json[$largo_array]);
					}else{
						//UN PASO
						$array_json[3]=str_replace('{', "", $array_json[3]);
						$array_json[$largo_array]=str_replace('})', "", $array_json[$largo_array]);
					}
					$token= $array_json[$i];
					if ($token != 'NULL'){
						//echo $token;
							/////CONSULTA PARA OBTENER DATOS DE LOS SCREENSHOTS
						$sql = "SELECT _datos_cdn (".($this->objetivo_id).", '" .($fecha_monitoreo)."' ,".($monitor).",".($pasos).")";
						$res =& $mdb2->query($sql);
						//print $sql.'<br>';
						if (MDB2::isError($res)) {
						    $log->setError($sql, $res->userinfo);
						    exit();
						}
						$row = $res->fetchRow();
						$dom = new DomDocument();
						$dom->preserveWhiteSpace = FALSE;
						$dom->loadXML($row["_datos_cdn"]);
						$xpath = new DOMXpath($dom);
						unset($row["_datos_cdn"]);
						$conf_objetivo = $xpath->query('/objetivo')->item(0);
						$conf_paso = $xpath->query('paso',$conf_objetivo)->item(0);
						$conf_nodo = $xpath->query('nodo',$conf_objetivo)->item(0);
						$conf_patrones = $xpath->query('patron', $conf_paso);
						$conf_codigos = $xpath->query('codigo', $conf_paso);
						$cant_patron = $xpath->query('patron', $conf_paso)->length;
						$T->setVar('__nombre_monitor', $conf_nodo->getAttribute('nombre_nodo'));
						///SE OBTIENEN LOS DATOS DE PATRON
						$color='false';
							$contador++;
							if ($cant_patron!=0) {
								$T->setVar('__cant_patron', $cant_patron);
								foreach ($conf_patrones as $conf_patron) {

									$conf_codigo = $xpath->query('codigo[@patron_orden="'.$conf_patron->getAttribute('patron_orden').'"]', $conf_paso)->item(0);
									$array_patron[$conf_patron->getAttribute('patron_orden')]['nombre'] = $conf_patron->getAttribute('patron_nombre');
									$array_patron[$conf_patron->getAttribute('patron_orden')]['codigo'] = substr($conf_codigo->getAttribute('icono_codigo'), 0, -4);
									$array_patron[$conf_patron->getAttribute('patron_orden')]['color'] = $conf_codigo->getAttribute('color_codigo');
									$array_patron[$conf_patron->getAttribute('patron_orden')]['codigo_error'] = $conf_codigo->getAttribute('codigo_id');
									foreach ($array_patron as $value) {
										if(($value["color"]=='d3222a' || $value["color"]=="d3222a")&&$value["color"]!=NULL){
											$color= 'true';
										}
									}
								}
							}else{
								$T->setVar('__cant_patron', 1);
								foreach ($conf_codigos as $conf_patron) {

									$conf_codigo = $xpath->query('codigo[@patron_orden=0]', $conf_paso)->item(0);
									$array_patron[0]['nombre'] = 'SIN PATRON';
									$array_patron[0]['codigo'] = substr($conf_codigo->getAttribute('icono_codigo'), 0, -4);
									$array_patron[0]['color'] = $conf_codigo->getAttribute('color_codigo');
									$array_patron[0]['codigo_error'] = $conf_codigo->getAttribute('codigo_id');
									foreach ($array_patron as $value) {
										if($value["color"]==d3222a || $value["color"]=="d3222a"){
											$color= 'true';
										}
									}
								}
							}
							$T->setVar('__nombre_patron', $array_patron[0]['nombre']);

							$T->setVar('__codigo_icono', $array_patron[0]['codigo']);
							if($color=='false'){
								$T->setVar('__color_codigo', $array_patron[0]['color']);
							}else{
								$T->setVar('__color_codigo', d3222a);
							}
							$nombre_ventana = $conf_paso->getAttribute('target');
							$estado = $array_patron[0]['codigo_error'];
							$json_patron = json_encode($array_patron);
							$T->setVar('__json_patron', $json_patron);
							if ($estado != 0) {
		                    	$error = true;
		                    }
			                ///VENTANA EN LA QUE SE ENCONTRO EL ERROR
			                if ($nombre_ventana == 'main') {
			     	          	$nombre_ventana = 'Ventana Principal';
			                }elseif ($nombre_ventana == 'popup') {
		               			$nombre_ventana = 'Popup';
		                	}else{
		                	$nombre_ventana = 'Desconocido';
		               		}
			                $fecha_tz_modal = new DateTime(pg_escape_string($fecha_monitoreo));
							$fecha_tz_modal->setTimezone(new DateTimeZone($tz));
				            ///DATOS SCREENSHOT MINIATURAS Y MODAL
				            $T->setVar('__paso_warning', ($error)?'url(img/warning.png) no-repeat 112px 2px':'');
							$T->setVar('__nombre_ventana', $nombre_ventana);
							$T->setVar('__paso_id', $pasos);
							$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre_paso'));
							$T->setVar('__token_id', $token);
							$T->setVar('__monitoreo_fecha_modal', $fecha_tz_modal->format("Y-m-d H:i:s"));
							$T->parse('lista_pasos', 'LISTA_PASOS', true);	
					}
				}

			/////PASAR FECHA UTC A FECHA SEGUN ZONA HORARIA DEL USUARIO/////
			$fecha_tz = new DateTime(pg_escape_string($fecha_monitoreo));
			$fecha_tz->setTimezone(new DateTimeZone($tz));

			///ANCHO DE LA TABLA DONDE SE ECNUENTRAN TODOS LOS SCREENSHOT (720 ES EL MINIMO)
			
				$ancho = $contador * 150;
				$objetivo=str_replace("(", "", $array_json[0]);
				$fecha_id=strtotime($fecha_monitoreo);
				$T->setVar('__monitoreo_ancho', $ancho);
				$T->setVar('__monitoreo_id', $nodo_filtro);
				$T->setVar('__fecha_int', $fecha_id);
				$T->setVar('__monitoreo_fecha', $fecha_tz->format("Y-m-d H:i:s"));
				$T->setVar('__boton_adelante_display', ($ancho>=720)?'inline':'none');
				$T->parse('bloque_monitoreos', 'BLOQUE_MONITOREOS', true);
		}
		// FIN FOREACH
		$array_pag=array();
		for ($i=0; $i <= $cant_paginas; $i++) {
			$array_pag[$i]=$i+1;
		}
		$array_pag=json_encode($array_pag);
		///ICONOS PARA PAGINACION
		$T->setVar('_h_slider', $h_slider);
		$T->setVar('_m_slider', $m_slider);
		$T->setVar('_h2_slider', $h2_slider);
		$T->setVar('_m2_slider', $m2_slider);
		$T->setVar('__arr_pag', $array_pag);
		$T->setVar('__cant_pag', $cant_paginas);
		$T->setVar('__pagina', $pagina);
		$T->setVar('__pagina_atras', $pagina-1);
		$T->setVar('__pagina_adelante', $pagina+1);
		$T->setVar('__disabled_atras', ($pagina==1)?'disabled':'');
		$T->setVar('__disabled_adelante', ($pagina == $cant_paginas)?'disabled':'');
		$T->setVar('__class_boton_atras', ($pagina==1)?'spriteButton spriteButton-atras_desactivado':'spriteButton spriteButton-atras');
		$T->setVar('__class_boton_adelante', ($pagina == $cant_paginas)?'spriteButton spriteButton-adelante_desactivado':'spriteButton spriteButton-adelante');
		$T->setVar('__mediciones', $tiene_mediciones);
		return $this->resultado = $T->parse('out', 'tpl_tabla');
	}

	function getScreenshotNormal() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		//SE VERIFICA SI UNO DE LOS INPUTS DEL FILTRO SE HA PASADO POR POST
		//Y GUARDANDO OS DATOS EN VARIABLES
		if (isset($_REQUEST['hora_inicio'])) {
			$hora_inicio = $_REQUEST['hora_inicio'];
			$minuto_inicio = $_REQUEST['minuto_inicio'];
			$hora_termino = $_REQUEST['hora_termino'];
			$minuto_termino = $_REQUEST['minuto_termino'];
		}else{
			$hora_termino  = '23';
			$minuto_termino  = '59';
			$hora_inicio = '00';
			$minuto_inicio  = '00';
		}
		if (isset($_REQUEST['nodo_filtro'])) {
			$nodo_filtro = $_REQUEST['nodo_filtro'];
			$isset = 1;
		}else{
			$nodo_filtro = -1;
			$isset = 0;
		}

		//SE GUARDA DIA MES Y AÑO EN VARIABLE Y SE LE AGREGA LA HORA QUE SE PASA POR POST POR LOS INPUTS DEL FILTRO
		$fechainicioSinHora = date("Y-m-d", strtotime($this->timestamp->getInicioPeriodo()));
		$fecha_inicio_filtro = $fechainicioSinHora.' '.$hora_inicio.':'.$minuto_inicio.':00';

		$fechaTerminoSinHora = date("Y-m-d", strtotime($this->timestamp->getInicioPeriodo()));
		$fecha_termino_filtro = $fechaTerminoSinHora.' '.$hora_termino.':'.$minuto_termino.':59';

		//SI SE ACTIVA EL FILTRO SE GUARDA FECHA PASADA POR POST SINO LA FECHA ES LA DEL CALENDARIO
		if (isset($_REQUEST['hora_inicio'])) {
			$fecha_inicial = $fecha_inicio_filtro;
			$fecha_termino = $fecha_termino_filtro;
		}else{
			$fecha_inicial = $this->timestamp->getInicioPeriodo();
			$fecha_termino = $this->timestamp->getTerminoPeriodo();
		}

		if (isset($_REQUEST['pagina'])) {
			$pagina = $_REQUEST['pagina'];
		}
		else {
			$pagina = "1";
		}


		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.screenshot_mediciones_filtro(".pg_escape_string($current_usuario_id).", ".
						pg_escape_string($this->objetivo_id).", ".
						(($this->subgrafico_id==1)?500:10).", ".
						pg_escape_string($pagina).", '".
						pg_escape_string($fecha_inicial)."', '".
						pg_escape_string($fecha_termino)."')";
        		$res =& $mdb2->query($sql);
						//echo $sql;
		if (MDB2::isError($res)) {
    		     $log->setError($sql, $res->userinfo);
      		     exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row["screenshot_mediciones_filtro"]);
		$xpath = new DOMXpath($dom);
		unset($row["screenshot_mediciones_filtro"]);

		$conf_objetivo = $xpath->query('//propiedades/objetivos/objetivo')->item(0);
		$tag_mediciones = $xpath->query('//detalles/detalle[@fecha]');
		$conf_nodo_filtro = $xpath->query('//resultados/propiedades/nodos/nodo');
		if ($xpath->query('//detalles/detalle[@nodo_id]')->length == 0) {
				/*A  TRAVES DE LOS echo SE MUESTRA UN BOTON PARA RECARGAR
				LA PAGINA EN CASO DE NO HABER DATOS PARA EL MONITOREO*/
				echo '<input type="button" class="boton_accion" value="Volver al filtro" style="cursor: pointer" onclick="recarga();">';
				echo '<script>';
				echo 'function recarga(){';
				echo 'location.reload();';
				echo '}';
				echo '</script>';
			return $this->resultado = $this->__generarContenedorSinDatos();
		}

		// TEMPLATE DEL GRAFICO
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'screenshot.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_SCREENSHOT', 'lista_screenshot');
		$T->setBlock('tpl_tabla', 'LISTA_SCREENSHOT_BULLET', 'lista_screenshot_bullet');
		$T->setBlock('tpl_tabla', 'LISTA_PATRONES_SCRIPT', 'lista_patrones_script');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS_SCRIPT', 'lista_pasos_script');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS_FILTRO', 'lista_nodos_filtro');
		$T->setBlock('tpl_tabla', 'VALORES_FILTRO', 'valores_filtro');
		$T->setBlock('tpl_tabla', 'VALORES_SLIDER', 'valores_slider');
		$T->setBlock('tpl_tabla', 'BLOQUE_MONITOREOS', 'bloque_monitoreos');
		$T->setBlock('tpl_tabla', 'BLOQUE_MONITOREOS_SCRIPT', 'bloque_monitoreos_script');
		$T->setVar('__path_jquery_ui', REP_PATH_JQUERY_UI);
		$slider_inicio = ($hora_inicio * 60) + $minuto_inicio;
		$slider_termino = ($hora_termino * 60) + $minuto_termino;

		$new = (isset($_GET['new'])) ? $_GET['new'] : true;

		$T->setVar('__item_id', $this->__item_id);
		$T->setVar('__objetivo_id', $conf_objetivo->getAttribute('objetivo_id'));

		//SE PASAN LAS VARIABLES AL FILTRO QUE SE PASARON POR POST AL HACER EL FILTRADO
		$T->setVar('__nodo_selected', $nodo_filtro);
		$T->setVar('__isset', $isset);

		$T->setVar('valores_slider', '');
		$T->setVar('__valor_slider_inicio', $slider_inicio);
		$T->setVar('__valor_slider_termino', $slider_termino);
		$T->parse('valores_slider', 'VALORES_SLIDER', true);

		$T->setVar('valores_filtro', '');
		$T->setVar('__hora_inicio', $hora_inicio);
		$T->setVar('__minuto_inicio', $minuto_inicio);
		$T->setVar('__hora_termino', $hora_termino);
		$T->setVar('__minuto_termino', $minuto_termino);
		$T->parse('valores_filtro', 'VALORES_FILTRO', true);

		$T->setVar('lista_nodos_filtro', '');
		foreach ($conf_nodo_filtro as $tag_nodo_filtro) {
			if ($tag_nodo_filtro->getAttribute('nodo_id') != 0) {
			$T->setVar('__nodo_filtro', $tag_nodo_filtro->getAttribute('nombre'));
			$T->setVar('__nodoid_filtro', $tag_nodo_filtro->getAttribute('nodo_id'));
			}else{
				continue;
			}
			$T->parse('lista_nodos_filtro', 'LISTA_NODOS_FILTRO', true);
		}

		/*almacena relacion entre un nodo y monitor*/
		$array_nodo_monitor = array();
		foreach ( $xpath->query('relacion', $conf_objetivo) as $value) {
        	 $array_nodo_monitor[$value->getAttribute('nodo')]=$value->getAttribute('monitor');
		}

		foreach ($tag_mediciones as $id => $tag_medicion) {
			if(isset($_REQUEST['hora_inicio'])){
			if (!isset($_REQUEST['nodo_filtro']) || $_REQUEST['nodo_filtro'] == -1) {
				$conf_nodo = $xpath->query("//nodos/nodo[@nodo_id=".$tag_medicion->getAttribute('nodo_id')."]")->item(0);
			}else{
				if ($tag_medicion->getAttribute('nodo_id') == $_REQUEST['nodo_filtro']) {
					$conf_nodo = $xpath->query("//nodos/nodo[@nodo_id=".$tag_medicion->getAttribute('nodo_id')."]")->item(0);
				}else{
					continue;
				}
			}

			$tag_pasos = $xpath->query('detalles/detalle', $tag_medicion);
			$ancho = $tag_pasos->length * 150;

			$T->setVar('__monitoreo_id', $id);
			$T->setVar('__monitoreo_fecha', date("d-m-Y H:i:s", strtotime($tag_medicion->getAttribute('fecha'))));
			$T->setVar('__monitoreo_fechacompleta', $tag_medicion->getAttribute('fecha'));
			$T->setVar('__nodo_id', $conf_nodo->getAttribute('nodo_id'));
			$T->setVar('__nodo_nombre', $conf_nodo->getAttribute('nombre'));

			$tiene_mediciones = false;
			$cnt_pasos = 0;
			$T->setVar('lista_pasos', '');
			$T->setVar('lista_pasos_script', '');
			foreach ($tag_pasos as $tag_paso) {

                            $conf_paso = $xpath->query("//objetivo/paso[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]")->item(0);


                            if(strlen($tag_medicion->getAttribute('fecha'))<29){
                            	$cadena='';
                            	$tm_fecha =strlen($tag_medicion->getAttribute('fecha'));
                            	$diferencia=29 - $tm_fecha;

                            	for($i=0;$i<$diferencia;$i++){
                            		$cadena.='0';
                            	}
                            	$fecha_completa = substr($tag_medicion->getAttribute('fecha'),0,-3).$cadena.substr($tag_medicion->getAttribute('fecha'),$tm_fecha-3);
                            }

                            if ($conf_paso->getAttribute('visible') == "0") {
                                continue;
                            }
                            $T->setVar('__paso_id', $conf_paso->getAttribute('paso_orden'));
                            $T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));

                            $tag_datos = $xpath->query('datos/dato', $tag_paso);

                            $total_ok = true;
                            $T->setVar('lista_patrones_script', '');
                            foreach (split(',', $tag_paso->getAttribute('estado')) as $patron_id => $estado) {
                                foreach($array_nodo_monitor as $nodo=>$monitor)
                                {
                                    if( $conf_nodo->getAttribute('nodo_id')==$nodo){
                                        $monitor_id = $monitor;
                                    }
                                }
                                /*Se evalua si existe el elemento patron*/
                                if (($xpath->query('patron', $conf_paso)->item(0))) {

                                    $tiene_monitor=$xpath->query('patron[@orden='.$patron_id.']', $conf_paso)->item(0);
                                    $existe_atributo= $tiene_monitor->hasAttribute('monitor_id');
                                    /*Evalua si existe atributo monitor*/
                                    if($existe_atributo==1){
                                        $conf_patron = $xpath->query('patron[@monitor_id='.$monitor_id.']', $conf_paso)->item(0);
                                    }
                                    else{
                                        $conf_patron = $xpath->query('patron[@orden='.$patron_id.']', $conf_paso)->item(0);
                                    }
                                }
                                else{
                                    $conf_patron = null;
                                }

                                $conf_codigo = $xpath->query("//codigos/codigo[@codigo_id=".$estado."]")->item(0);

                                $T->setVar('__patron_id', ($conf_patron == null)?'':$conf_patron->getAttribute('orden'));
                                $T->setVar('__patron_nombre', ($conf_patron == null)?'':$conf_patron->getAttribute('nombre'));
                                $T->setVar('__estado_icono', REP_PATH_SPRITE_CODIGO.substr($conf_codigo->getAttribute('icono'), 0, -4));
                                $T->setVar('__estado_color', $conf_codigo->getAttribute('color'));
                                $T->parse('lista_patrones_script', 'LISTA_PATRONES_SCRIPT', true);

                                if ($estado != 0) {
                                        $total_ok = false;
                                }
                            }

                            $T->setVar('__paso_warning', ($total_ok)?'':'url(img/warning.png) no-repeat 112px 2px');
                            $T->setVar('__paso_width', ($total_ok)?'120px':'105px');
                            $T->setVar('__paso_color', ($total_ok)?'54a51c':'d22129');

                            $T->setVar('lista_screenshot', '');
                            $T->setVar('lista_screenshot_bullet', '');
                            foreach ($tag_datos as $id_dato => $tag_dato) {
                                if ($id_dato == 0) {
                                    $T->setVar('__screenshot_display', 'inline');
                                    $T->setVar('__bullet_color', ($total_ok)?'#54a51c':'#d22129');
                                } else {
                                    $T->setVar('__screenshot_display', 'none');
                                    $T->setVar('__bullet_color', 'inherit');
                                }
                                $T->setVar('__screenshot_id', $id_dato);
                                $T->setVar('__screenshot_window', $tag_dato->getAttribute('window'));
                                $T->setVar('__screenshot_title', ($tag_dato->getAttribute('window')=="main")?"Ventana Principal":"Ventana Popup");
                                $T->parse('lista_screenshot', 'LISTA_SCREENSHOT', true);
                                $T->parse('lista_screenshot_bullet', 'LISTA_SCREENSHOT_BULLET', true);
                            }

                            $T->parse('lista_pasos', 'LISTA_PASOS', true);
                            $T->parse('lista_pasos_script', 'LISTA_PASOS_SCRIPT', true);
                            $cnt_pasos++;
                            $tiene_mediciones = true;
                    }

                    $ancho = $cnt_pasos * 150;
                    $T->setVar('__monitoreo_ancho', $ancho);
                    $T->setVar('__boton_adelante_display', ($ancho>720)?"inline":"none");

                    if ($cnt_pasos > 0) {
                        $T->parse('bloque_monitoreos', 'BLOQUE_MONITOREOS', true);
                        $T->parse('bloque_monitoreos_script', 'BLOQUE_MONITOREOS_SCRIPT', true);
                    }

                }else{
                	continue;
                }
		}
	
		if (isset($_REQUEST['hora_inicio'])) {
			
		}
		if (!$tiene_mediciones) {
                    $T->setVar('bloque_monitoreos', $this->__generarContenedorSinFiltro());
                    $T->setVar('bloque_monitoreos_script', '');
		}

		$T->setVar('__pagina_filtro', (!$tiene_mediciones)?'none' : 'inline');
		$T->setVar('__pagina', $pagina);
		$T->setVar('__pagina_atras', $pagina-1);
		$T->setVar('__pagina_adelante', $pagina+1);
		$T->setVar('__disabled_atras', ($pagina==1)?'disabled':'');
		$T->setVar('__disabled_adelante', ($tag_mediciones->length<10)?'disabled':'');
		$T->setVar('__class_boton_atras', ($pagina==1)?'spriteButton spriteButton-atras_desactivado':'spriteButton spriteButton-atras');
		$T->setVar('__class_boton_adelante', ($tag_mediciones->length<10)?'spriteButton spriteButton-adelante_desactivado':'spriteButton spriteButton-adelante');

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));

		return $this->resultado = $T->parse('out', 'tpl_tabla');
	}



	/*************** FUNCIONES DE TABLAS DE DESCRIPCION ***************/
	/*************** FUNCIONES DE TABLAS DE DESCRIPCION ***************/
	/*************** FUNCIONES DE TABLAS DE DESCRIPCION ***************/

	/**
	 * Funcion que muestra la tabla con los horarios
	 * habiles utilizados en el reporte.
	 */
	function getDescripcionHorario(){
		global $usr;
		global $dias_semana;

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'descripcion_horario.tpl');
		$T->setBlock('tpl_tabla', 'ES_PRIMERO_DIA', 'es_primer_dia');
		$T->setBlock('tpl_tabla', 'BLOQUE_HORARIO', 'bloque_horario');
		$T->setBlock('tpl_tabla', 'BLOQUE_TODO_HORARIO', 'bloque_todo_horario');

		$T->setVar('bloque_horario', '');
		$T->setVar('bloque_todo_horario', '');

		$horario = $usr->getHorario($this->horario_id);
		$T->setVar('__item_orden', $this->extra["item_orden"]);
		$T->setVar('__horario_orden', 1);
		$T->setVar('__horario_nombre',$horario->nombre);
		$tiene_horarios = false;

		$linea = 1;
		foreach ($dias_semana as $dia_id => $dia_nombre) {
			$items = $horario->getDiaSemanaItems($dia_id);
			$primero = true;
			foreach ($items as $id => $item) {
				if ($primero == true) {
					$T->setVar('__dia', $dia_nombre);
					$T->setVar('__dia_rowspan', count($items));
					$T->parse('es_primer_dia', 'ES_PRIMERO_DIA', false);
				}
				else {
					$T->setVar('es_primer_dia', '');
				}

				$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
				$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
				$T->setVar('__horaInicio', $item->hora_inicio);
				$T->setVar('__horaTermino', $item->hora_termino);
				$T->parse('bloque_horario', 'BLOQUE_HORARIO', true);
				$primero = false;
				$tiene_horarios = true;
				$linea++;
			}
		}
		if ($tiene_horarios == false) {
			$T->parse('bloque_todo_horario', 'BLOQUE_TODO_HORARIO', false);
		}
		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	/**
	 * Funcion que muestra las url excluidas durante
	 * el monitoreo del objetivo.
	 */
	// TODO: metodo getDescripcionUrlExcluidas()
	function getUrlExcluidas() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.url_excluidas(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = false;
		$dom->loadXML($row["url_excluidas"]);
		$xpath = new DOMXpath($dom);
		unset($row["url_excluidas"]);

		$conf_objetivo = $xpath->query('/atentus/resultados/propiedades/objetivos/objetivo')->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if ($xpath->query('//paso/excluye')->length == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		// CARGA DEL TEMPLATE Y LIMPIEZA DE BLOQUES
		$T =& new Template_PHPLIB(($this->extra["imprimir"])?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'descripcion_url_excluidas.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_NOMBRE', 'bloque_nombre');
		$T->setBlock('tpl_tabla', 'BLOQUE_EXCLUIDA', 'bloque_excluida');

		$linea = 0;
		if ($conf_pasos->length > 0) {
			foreach ($conf_pasos as $conf_paso) {
				foreach ($xpath->query('excluye', $conf_paso) as $id => $excluida) {
					$T->setVar('bloque_nombre', '');
					if ($id == 0) {
						$T->setVar('__idPaso', $conf_paso->getAttribute('nombre'));
						$T->setVar('__rowSpan', $xpath->query('excluye', $conf_paso)->length);
						$T->parse('bloque_nombre', 'BLOQUE_NOMBRE', false);
					}
					$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
					$T->setVar('__class', ($linea % 2 == 0)?"celdanegra15":"celdanegra10");
					$T->setVar('__url', $excluida->getAttribute('url'));
					$T->parse('bloque_excluida', 'BLOQUE_EXCLUIDA', true);
					$linea++;
				}
			}
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));

		$this->resultado = $T->parse('out', 'tpl_tabla');
	}



	/*************** FUNCIONES DE TABLAS DE ESPECIALES ***************/
	/*************** FUNCIONES DE TABLAS DE ESPECIALES ***************/
	/*************** FUNCIONES DE TABLAS DE ESPECIALES ***************/

	function getEspecialDisponibilidadFlexiblePorDia() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$T = & new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_tabla', 'especial_disponibilidad_diaria.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS_TITULOS', 'bloque_eventos_titulos');
		$T->setBlock('tpl_tabla', 'BLOQUE_EVENTOS', 'bloque_eventos');
		$T->setBlock('tpl_tabla', 'BLOQUE_DIAS', 'bloque_dias');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');

		$inicio = strtotime($this->timestamp->fecha_inicio);

		while ($inicio < strtotime($this->timestamp->fecha_termino)) {
			$termino = mktime(0, 0, 0, date("m", $inicio), date("d", $inicio) + 1, date("Y", $inicio));

			$T->setVar('bloque_pasos','');
			$T->setVar('bloque_titulo_horarios','');

			$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", 0,' ".
					pg_escape_string(date("Y-m-d", $inicio))."', '".
					pg_escape_string(date("Y-m-d", $termino))."', ".
					pg_escape_string($usr->cliente_id).")";
//			print($sql);
			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["disponibilidad_resumen_consolidado"]);
			$xpath = new DOMXpath($dom);

			foreach ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=0]/detalles/detalle") as $tag_paso) {
				$arr_valores[$tag_paso->getAttribute("paso_orden")][$inicio] = array();
				foreach ($xpath->query("estadisticas/estadistica", $tag_paso) as $tag_evento) {
					$arr_valores[$tag_paso->getAttribute("paso_orden")][$inicio][$tag_evento->getAttribute("evento_id")] = $tag_evento->getAttribute("porcentaje");
				}
			}
			$inicio = mktime(0, 0, 0, date("m", $inicio), date("d", $inicio) + 1, date("Y", $inicio));
		}

		$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
		$T->setVar('bloque_eventos_titulos', '');
		foreach ($conf_eventos as $conf_evento) {
			$T->setVar('__evento_nombre', $conf_evento->getAttribute("nombre"));
			$T->parse('bloque_eventos_titulos', 'BLOQUE_EVENTOS_TITULOS', true);
		}

		$T->setVar('bloque_pasos', '');
		$orden = 1;
		foreach ($xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]") as $conf_paso) {
			$T->setVar('__ordenItem', $this->extra["item_orden"]);
			$T->setVar('__ordenPaso', $orden);
			$T->setVar('__paso_nombre', $conf_paso->getAttribute("nombre"));

			$T->setVar('bloque_dias', '');
			$linea = 1;
			foreach ($arr_valores[$conf_paso->getAttribute("paso_orden")] as $fecha => $eventos) {
				$T->setVar('__evento_fecha', date("d/m/Y", $fecha));
				$T->setVar('__paso_estilo', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");

				$T->setVar('bloque_eventos', '');
				foreach ($conf_eventos as $conf_evento) {
					$T->setVar('__evento_valor', number_format(($eventos[$conf_evento->getAttribute("evento_id")])?$eventos[$conf_evento->getAttribute("evento_id")]:"0", 3, '.', ''));
					$T->setVar('__evento_color', ($conf_evento->getAttribute("color") == "f0f0f0")?"b2b2b2":$conf_evento->getAttribute("color"));
					$T->parse('bloque_eventos', 'BLOQUE_EVENTOS', true);
				}
				$T->parse('bloque_dias', 'BLOQUE_DIAS', true);
				$linea++;
			}
			$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
			$orden++;
		}

		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	function getEspecialMedicionesPorEstado() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_tabla', 'especial_resumen_conexiones.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.cantidad_global(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string(0).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//		print($sql."<br>");

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row["cantidad_global"]);
		$xpath = new DOMXpath($dom);

		$linea = 1;
		foreach ($xpath->query("/atentus/resultados/detalles/detalle") as $detalle_obj) {
			$tag_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$detalle_obj->getAttribute('objetivo_id')."]")->item(0);

			/* LISTA DE PASOS */
			foreach ($xpath->query("detalles/detalle", $detalle_obj) as $detalle_paso) {
				$tag_paso = $xpath->query("paso[@paso_orden=".$detalle_paso->getAttribute('paso_orden')."]", $tag_objetivo)->item(0);

				if ($tag_paso->getAttribute('visible') == 1) {

					foreach ($xpath->query("datos/dato", $detalle_paso) as $dato_paso) {
						$total = $dato_paso->getAttribute("cantidad_ok") + $dato_paso->getAttribute("cantidad_error_contenido") + $dato_paso->getAttribute("cantidad_error_nocontenido");

						$T->setVar('__paso_estilo', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
						$T->setVar('__paso_nombre', $tag_paso->getAttribute('nombre'));
						$T->setVar('__paso_cantidad_ok', $dato_paso->getAttribute("cantidad_ok"));
						$T->setVar('__paso_cantidad_error_contenido', $dato_paso->getAttribute("cantidad_error_contenido"));
						$T->setVar('__paso_cantidad_error_nocontenido', $dato_paso->getAttribute("cantidad_error_nocontenido"));
						$T->setVar('__paso_porcentaje_ok', number_format(($dato_paso->getAttribute("cantidad_ok") / $total) * 100, 2, ',', ''));
						$T->setVar('__paso_porcentaje_error_contenido', number_format(($dato_paso->getAttribute("cantidad_error_contenido") / $total) * 100, 2, ',', ''));
						$T->setVar('__paso_porcentaje_error_nocontenido', number_format(($dato_paso->getAttribute("cantidad_error_nocontenido") / $total) * 100, 2, ',', ''));
						$T->parse('lista_pasos', 'LISTA_PASOS', true);
						$linea++;
					}
				}
			}
		}
		$this->tiempo_expiracion = $parser->tiempo_expiracion;
		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	function getEspecialMedicionesPorEficiencia() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$objetivo = new ConfigEspecial($this->objetivo_id);
		$nodos_seleccionados = $_REQUEST["nodos_seleccionados"];

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_tabla', 'especial_mediciones_por_porcentaje.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS', 'lista_nodos');
		$T->setBlock('tpl_tabla', 'LISTA_EXCLUIDOS', 'lista_excluidos');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setVar('lista_monitores', '');
		$T->setVar('lista_excluidos', '');
		$T->setVar('lista_pasos', '');

		$linea = 1;
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {

			foreach($subobjetivo->__pasos as $paso) {
				$sql =  "SELECT * FROM reporte.especial_mediciones_por_eficiencia(".
						pg_escape_string($current_usuario_id).",".
						pg_escape_string($subobjetivo->objetivo_id).",".
						pg_escape_string($paso->paso_id).",ARRAY[".
						pg_escape_string($nodos_seleccionados)."],'".
						pg_escape_string($this->timestamp->getInicioPeriodo())."','".
						pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//				echo $sql;
				$res =& $mdb2->query($sql);
				if (MDB2::isError($res)) {
					$log->setError($sql, $res->userinfo);
					exit();
				}

				$row = $res->fetchRow();
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row["especial_mediciones_por_eficiencia"]);
				$xpath = new DOMXpath($dom);

				$tag_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$subobjetivo->objetivo_id."]")->item(0);
				$tag_paso = $xpath->query("paso[@paso_orden=".$paso->paso_id."]", $tag_objetivo)->item(0);
				$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$subobjetivo->objetivo_id."]/detalles/detalle[@paso_orden=".$paso->paso_id."]/datos")->item(0);
				$tag_dato90 = $xpath->query("dato[@porcentaje=90]", $tag_datos)->item(0);
				$tag_dato10 = $xpath->query("dato[@porcentaje=10]", $tag_datos)->item(0);

				$T->setVar('__objetivo_nombre', $tag_objetivo->getAttribute("nombre"));
				$T->setVar('__objetivo_id', $tag_objetivo->getAttribute("objetivo_id"));
				$T->setVar('__paso_estilo', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
				$T->setVar('__paso_nombre', $tag_paso->getAttribute("nombre"));
				$T->setVar('__paso_orden', $tag_paso->getAttribute("paso_orden"));
				$T->setVar('__total_trx1', number_format($tag_dato90->getAttribute("cantidad"), 0, ',', '.'));
				$T->setVar('__total_seg1', number_format($tag_dato90->getAttribute("tiempo_total"), 2, ',', '.'));
				$T->setVar('__promedio1', number_format($tag_dato90->getAttribute("tiempo_prom"), 2, ',', '.'));
				$T->setVar('__total_trx2', number_format($tag_dato10->getAttribute("cantidad"), 0, ',', '.'));
				$T->setVar('__total_seg2', number_format($tag_dato10->getAttribute("tiempo_total"), 2, ',', '.'));
				$T->setVar('__promedio2', number_format($tag_dato10->getAttribute("tiempo_prom"), 2, ',', '.'));
				$T->setVar('__total', number_format($tag_datos->getAttribute("tiempo_total"), 0, ',', '.'));
				$T->parse('lista_pasos', 'LISTA_PASOS', true);
				$linea++;

			}

			foreach ($subobjetivo->getNodos() as $nodo) {
				$nodos[$nodo->nodo_id] = $nodo;
			}
		}

		$nodos_ids = explode(',', $nodos_seleccionados);

		if(count($nodos_ids) != count($nodos)){
			foreach ($nodos as $nodo) {
				if (!in_array($nodo->nodo_id, $nodos_ids)) {
					$T->setVar('__nodo_id', $nodo->nodo_id);
					$T->setVar('__nodo_nombre', $nodo->nombre);
					$T->parse('lista_nodos', 'LISTA_NODOS', true);
				}
			}
			$T->parse('lista_excluidos', 'LISTA_EXCLUIDOS', true);
		}

		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	function getEspecialMedicionesPorRendimiento() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$objetivo = new ConfigEspecial($this->objetivo_id);
		$nodos_seleccionados = $_REQUEST["nodos_seleccionados"];

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_tabla', 'especial_mediciones_por_rendimiento.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS', 'lista_nodos');
		$T->setBlock('tpl_tabla', 'LISTA_EXCLUIDOS', 'lista_excluidos');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setVar('lista_monitores', '');
		$T->setVar('lista_excluidos', '');
		$T->setVar('lista_pasos', '');

		$linea = 1;
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {

			foreach($subobjetivo->__pasos as $paso) {
				$sql =  "SELECT * FROM reporte.especial_mediciones_por_rendimiento(".
						pg_escape_string($current_usuario_id).",".
						pg_escape_string($subobjetivo->objetivo_id).",".
						pg_escape_string($paso->paso_id).",ARRAY[".
						pg_escape_string($nodos_seleccionados)."],'".
						pg_escape_string($this->timestamp->getInicioPeriodo())."','".
						pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//						echo $sql;
				$res =& $mdb2->query($sql);
				if (MDB2::isError($res)) {
					$log->setError($sql, $res->userinfo);
					exit();
				}

				$row = $res->fetchRow();
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row["especial_mediciones_por_rendimiento"]);
				$xpath = new DOMXpath($dom);

				$tag_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$subobjetivo->objetivo_id."]")->item(0);
				$tag_paso = $xpath->query("paso[@paso_orden=".$paso->paso_id."]", $tag_objetivo)->item(0);
				$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$subobjetivo->objetivo_id."]/detalles/detalle[@paso_orden=".$paso->paso_id."]/datos")->item(0);
				$tag_datomenor = $xpath->query("dato[@eficiente='true']", $tag_datos)->item(0);
				$tag_datomayor = $xpath->query("dato[@eficiente='false']", $tag_datos)->item(0);

				$T->setVar('__objetivo_nombre', $tag_objetivo->getAttribute("nombre"));
				$T->setVar('__objetivo_id', $tag_objetivo->getAttribute("objetivo_id"));
				$T->setVar('__paso_estilo', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
				$T->setVar('__paso_nombre', $tag_paso->getAttribute("nombre"));
				$T->setVar('__paso_orden', $tag_paso->getAttribute("paso_orden"));
				$T->setVar('__total_trx', number_format($tag_datos->getAttribute("cantidad_total"), 0, ',', '.'));
				$T->setVar('__cantidad1', number_format(($tag_datomenor == null)?0:$tag_datomenor->getAttribute("cantidad"), 0, ',', '.'));
				$T->setVar('__porcentaje1', number_format(($tag_datomenor == null)?0:$tag_datomenor->getAttribute("porcentaje"), 2, ',', '.'));
				$T->setVar('__cantidad2', number_format(($tag_datomayor == null)?0:$tag_datomayor->getAttribute("cantidad"), 0, ',', '.'));
				$T->setVar('__porcentaje2', number_format(($tag_datomayor == null)?0:$tag_datomayor->getAttribute("porcentaje"), 2, ',', '.'));
				$T->parse('lista_pasos', 'LISTA_PASOS', true);
				$linea++;
			}

			foreach ($subobjetivo->getNodos() as $nodo) {
				$nodos[$nodo->nodo_id] = $nodo;
			}
		}

		$nodos_ids = explode(',', $nodos_seleccionados);

		if(count($nodos_ids) != count($nodos)){
			foreach ($nodos as $nodo) {
				if (!in_array($nodo->nodo_id, $nodos_ids)) {
					$T->setVar('__nodo_id', $nodo->nodo_id);
					$T->setVar('__nodo_nombre', $nodo->nombre);
					$T->parse('lista_nodos', 'LISTA_NODOS', true);
				}
			}
			$T->parse('lista_excluidos', 'LISTA_EXCLUIDOS', true);
		}

		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	function getEspecialDisponibilidadObjetivoPaso() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		
		$objetivo = new ConfigEspecial($this->objetivo_id);
		$primero = true;
		$marcado = false;
		$sql_objetivos = "ARRAY[";

		$event = new Event;
		$graficoSvg = new GraficoSVG();
		$usr = new Usuario($current_usuario_id);
		$usr->__Usuario();
		
		# Variables para eventos especiales marcados por el cliente codigo 9.
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		$nameFunction = 'EspecialDisponibilidadObjetivoPaso';
		$dataMant = null;
		$ids = null;

		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			foreach ($subobjetivo->__pasos as $paso) {
				$sql_objetivos.= (($primero)?"":",")."ARRAY[".$subobjetivo->objetivo_id.",".$paso->paso_id."]";
			}
			$primero = false;
		}
		$sql_objetivos.= "]";

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_tabla', 'especial_disponibilidad_objetivo_paso.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_tabla', 'TIENE_EVENTO_DATO', 'tiene_evento_dato');
		$T->setBlock('tpl_tabla', 'TIENE_EVENTO', 'tiene_evento');
		
		#$T->setVar('tiene_evento', '');

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.especial_disponibilidad_resumen_objetivopaso(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($sql_objetivos).", ".
				pg_escape_string($this->horario_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//		print($sql."<br>");

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row["especial_disponibilidad_resumen_objetivopaso"]);
		$xpath = new DOMXpath($dom);
		
		$linea = 1;
		# Busca si exiten marcados y los almacena.
		foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle") as $tag_marcado_objetivo) {
			foreach ($xpath->query('marcado', $tag_marcado_objetivo) as $tag_marcado) {
				$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
				$marcado = true;
			}
		}		

		# Obetener los datos de mantenimiento.
		if ($marcado == true) {
			$dataMant =$event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			$encode = json_encode($dataMant);
			$tieneEvento = 'true';
			$T->setVar('__tiene_evento', $tieneEvento);
			$T->setVar('__name', $nameFunction);
			$T->parse('tiene_evento', 'TIENE_EVENTO', true);
			#
		}
		else{
			$T->setVar('__mostrar', 'NoMostrarEvento');
		}
		foreach ($xpath->query("/atentus/resultados/detalles/detalle") as $detalle_obj) {
			$tag_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$detalle_obj->getAttribute('objetivo_id')."]")->item(0);
		
			/* LISTA DE PASOS */
			foreach ($xpath->query("detalles/detalle", $detalle_obj) as $detalle_paso) {
				$tag_paso = $xpath->query("paso[@paso_orden=".$detalle_paso->getAttribute('paso_orden')."]", $tag_objetivo)->item(0);
				
				foreach ($xpath->query("datos/dato", $detalle_paso) as $dato_paso) {

					$T->setVar('__paso_estilo', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
					$T->setVar('__objetivo_nombre', $tag_objetivo->getAttribute('nombre'));
					$T->setVar('__paso_nombre', $tag_paso->getAttribute('nombre'));
					$T->setVar('__paso_promedio', number_format($dato_paso->getAttribute("tiempo_prom"), 3, ',', ''));
					$T->setVar('__paso_uptime', number_format($dato_paso->getAttribute("uptime"), 3, ',', ''));
					$T->setVar('__paso_downtime', number_format($dato_paso->getAttribute("downtime"), 3, ',', ''));
					$T->setVar('__paso_downtime_parcial', number_format($dato_paso->getAttribute("downtime_parcial"), 3, ',', ''));
					$T->setVar('__paso_no_monitoreo', number_format($dato_paso->getAttribute("sin_monitoreo"), 3, ',', ''));
					$T->setVar('__paso_evento_especial', number_format($dato_paso->getAttribute("marcado_cliente"), 3, ',', ''));

					$T->parse('lista_pasos', 'LISTA_PASOS', true);
					$linea++;
				}
			}
		}
		$this->tiempo_expiracion = $parser->tiempo_expiracion;
		$this->resultado = $T->parse('out', 'tpl_tabla');
		# Agrega el acordeon cuando existan eventos.
		if (count($dataMant)>0){

			$this->resultado.= $graficoSvg->getAccordion($encode, $nameFunction);
		}
	}
	/*
     	Creado por: Santiago Sepúlveda
    	Modificado por: 
    	Fecha de creacion: 09/10/2019
    	Fecha de ultima modificacion:
    */
	function vistaRapidaUnificadaAjax($objetivo_especial, $horario_id, $usuario_id, $fecha_inicio_js, $fecha_termino_js){
		global $mdb2;
		global $log;

		$sql2 = "SELECT _cliente_tz(".pg_escape_string($usuario_id).")";
		$res0 =& $mdb2->query($sql2);

		if (MDB2::isError($res0)) {
			$log->setError($sql2, $res0->userinfo);
			exit();
		}
		$row = $res0->fetchRow();
		$tz= $row["_cliente_tz"];

		$timestamp = time();
		$dt = new DateTime("now", new DateTimeZone($tz));
		$dt->setTimestamp($timestamp);

		$objetivoEspecial=new ConfigEspecial($objetivo_especial);

		$cuenta_cat = 1;
		$tiempo_uptime_cat = 0;
		$dias_uptime_cat = 0;
		$primera_pagina=0;
		$json = '{"vru": [{ "categoria": [';
		$cant_cat = count($objetivoEspecial->__conjuntos);
		foreach ($objetivoEspecial->__conjuntos as $nombre_categoria => $conjuntos) {
			$json .= '{"nombre_categoria": "'.$nombre_categoria.'",';
			$categorias .= '{"nombre_categoria": "'.$nombre_categoria.'",';
			$json .= '"page_break_categoria": '.(($cant_cat == $cuenta_cat)?'"",' : '"page-break-after: always;",');

			$contador_salto = 1;
			$primer_nodo_menor = true;
			$nodo_anterior_cat = 0;
			$nodo_menor_cat = 0;
			$nodo_anterior_mayor_cat = 0;
			$nodo_mayor_cat = 0;
			$contador_total = 0;
			$uptime_real_acumulado_total = 0;
			$downtime_real_acumulado_total = 0;
			$tiempo_respuesta_acumulado_total = 0;
			$downtime_real_acumulado = 0;
			$tiempo_respuesta_acumulado = 0;
			$contador2 = 0;
			$primer_nodo = true;
			$uptime_real_acumulado_pasos = 0;
			$contador_pasos = 0;
			$cuenta_func = 1;
			$cant_func = count($conjuntos);
			$json .= '"funcionalidad": [';
			foreach ($conjuntos as $nombre_funcionalidad => $conjunto) {
				$contador_pasos = 0;
				foreach ($conjunto as $objetivos3) {
					foreach ($objetivos3 as $pasos) {
						$contador_pasos++;
					}
				}

				$json .= '{"nombre_funcionalidad": "'.$nombre_funcionalidad.'",';

				if ($contador_total%2 == 0) {
					$iteracion_func = 'iteracionGris';
				}else{
					$iteracion_func = 'iteracionNaranjo';
				}

				$json .= '"class_iteracion_func": "'.$iteracion_func.'",';

				$primero = true;
				$sql_objetivos = "ARRAY[";
				foreach ($conjunto as $objetivo_id => $objetivo) {
					$objetivo_id =explode('|', $objetivo_id)[0];
					foreach ($objetivo as $paso_orden => $paso) {
						$paso_orden = explode('|', $paso_orden)[0];
						$sql_objetivos.= (($primero)?"":",")."ARRAY[".$objetivo_id.",".$paso_orden."]";
						$primero = false;
					}
				}
				$sql_objetivos .= "]";

				$fecha_inicio = strtotime($fecha_inicio_js);
				$fecha_termino = strtotime($fecha_termino_js);
				$dia = 86400;
				$disponibilidad = array();
				$cuenta_dias_consultados=0;
				while ($fecha_inicio < $fecha_termino) {
					$fecha_inicio_consulta = date("Y-m-d H:i:s", $fecha_inicio);
					$fecha_termino_consulta = date("Y-m-d H:i:s", $fecha_inicio+86400);
					$sql = "SELECT * FROM reporte.especial_disponibilidad_resumen_objetivopaso_v2(".
					pg_escape_string($usuario_id).",".
					pg_escape_string($sql_objetivos).", ".
					pg_escape_string($horario_id).", '".
					pg_escape_string($fecha_inicio_consulta)."', '".
					pg_escape_string($fecha_termino_consulta)."')";

					$fecha_inicio += $dia;
					$res =& $mdb2->query($sql);
					if (MDB2::isError($res)) {
						$log->setError($sql, $res->userinfo);
						exit();
					}
					$row = $res->fetchRow();
					$dom = new DomDocument();
					$dom->preserveWhiteSpace = FALSE;
					$dom->loadXML($row["especial_disponibilidad_resumen_objetivopaso_v2"]);
					$xpath = new DOMXpath($dom);
					foreach ($conjunto as $obj_id => $objetivo) {
						$obj_id =explode('|', $obj_id)[0];
						foreach ($objetivo as $key_pasos => $paso) {
							$conf_dato = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$obj_id."]/detalles/detalle[@paso_orden=".$paso->paso_orden."]/datos/dato")->item(0);
							$disponibilidad[$obj_id][$paso->paso_orden]['uptime'] += $conf_dato->getAttribute('uptime') +  $conf_dato->getAttribute('downtime_parcial');
							$disponibilidad[$obj_id][$paso->paso_orden]['downtime'] += $conf_dato->getAttribute('downtime');
							$disponibilidad[$obj_id][$paso->paso_orden]['tiempo_prom'] += $conf_dato->getAttribute('tiempo_prom');
							$disponibilidad[$obj_id][$paso->paso_orden]['sin_monitoreo'] += $conf_dato->getAttribute('sin_monitoreo');

							$disponibilidad[$obj_id][$paso->paso_orden]['segundos_uptime'] += $conf_dato->getAttribute('segundos_uptime') +  $conf_dato->getAttribute('segundos_downtime_parcial');
							$disponibilidad[$obj_id][$paso->paso_orden]['segundos_downtime'] += $conf_dato->getAttribute('segundos_downtime');
							$disponibilidad[$obj_id][$paso->paso_orden]['segundos_sin_monitoreo'] += $conf_dato->getAttribute('segundos_sin_monitoreo');
							$disponibilidad[$obj_id][$paso->paso_orden]['factor_total'] += $conf_dato->getAttribute('segundos_uptime') +  $conf_dato->getAttribute('segundos_downtime_parcial') + $conf_dato->getAttribute('segundos_downtime');
						}
					}
					$cuenta_dias_consultados++;
				}

				$res =& $mdb2->query($sql);
				if (MDB2::isError($res)) {
					$log->setError($sql, $res->userinfo);
					exit();
				}

				$row = $res->fetchRow();
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row["especial_disponibilidad_resumen_objetivopaso_v2"]);
				$xpath = new DOMXpath($dom);
				//periodo seleccionado en segundos
				if ( $fecha_termino > strtotime(date("Y-m-d H:i:s")) ) {
					$segundos_seleccionados = (strtotime($dt->format("Y-m-d H:i:s")) - $fecha_inicio);
				}else{
					$segundos_seleccionados = ($fecha_termino - $fecha_inicio);
				}
				$primer_nodo = true;
				$nodo_menor = 0;
				$nodo_anterior = 0;
				$nodo_actual = 0;
				$nodo_mayor = 0;
				$nodo_anterior_mayor = 0;
				$tiempo_uptime_func = 0;
				$dias_uptime_func = 0;
				$uptime_real_acumulado = 0;
				$downtime_real_acumulado = 0;
				$tiempo_respuesta_acumulado = 0;
				$contador = 0;
				$cuenta_pasos = 1;
				$first = true;
				$sql_obj_func = "ARRAY[";
				$json .= '"pasos": [';
				foreach ($conjunto as $obj_id => $objetivo) {
					$muestra_nombre = explode('|', $obj_id)[1];
					$obj_id =explode('|', $obj_id)[0];

					$sql_nodos_mm = "SELECT count(*) as cuenta_nodos from (
																	SELECT unnest(_nodos_id) from (
																		SELECT _nodos_id(".
																		pg_escape_string($usuario_id).", ".
																		pg_escape_string($obj_id).", '".
																		pg_escape_string($fecha_inicio_js)."'
																		)
																	) as foo
																) as foo2";
					$res_nodos_mm =& $mdb2->query($sql_nodos_mm);
					if (MDB2::isError($res_nodos_mm)) {
						$log->setError($sql_nodos_mm, $res_nodos_mm->userinfo);
						exit();
					}

					$row_nodos_mm = $res_nodos_mm->fetchRow();

					$nodo_actual = $row_nodos_mm['cuenta_nodos'];
					if ($primer_nodo == false) {
						$nodo_anterior = $nodo_menor;
					}else{
						$nodo_anterior = $nodo_actual;
					}

					if ($nodo_anterior <= $nodo_actual ) {
						$nodo_menor = $nodo_anterior;
					}else{
						$nodo_menor = $nodo_actual;
					}

					if ($primer_nodo == false) {
						$nodo_anterior_mayor = $nodo_mayor;
					}else{
						$nodo_anterior_mayor = $nodo_actual;
					}

					if ($nodo_anterior_mayor >= $nodo_actual ) {
						$nodo_mayor = $nodo_anterior_mayor;
					}else{
						$nodo_mayor = $nodo_actual;
					}

					$tiempo_uptime = 0;
					$dias_uptime = 0;
					$color_uptime = '';
					foreach ($objetivo as $key_pasos => $paso) {
						$screenshot_hash = explode('|', $key_pasos)[1];
						$id_paso = $nombre_categoria.'_'.$nombre_funcionalidad.'_'.$obj_id.'_'.$paso->paso_orden.'_'.$screenshot_hash;

						if ($screenshot_hash != "") {
							$class_tooltip_paso = 'screenshotPaso';
							$muestra_screenshot = 'inline';
							$muestra_descarga = 'inline';
						}else{
							$class_tooltip_paso = 'nombreObjetivo';
							$muestra_screenshot = 'none';
							$muestra_descarga = 'none';
						}

						$paso_info= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$obj_id."]/paso[@paso_orden=".$paso->paso_orden."]")->item(0);
						$conf_dato = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$obj_id."]/detalles/detalle[@paso_orden=".$paso->paso_orden."]/datos/dato")->item(0);
						$objetivo_info = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$obj_id."]")->item(0);
						//datos pasos
						$json .= '{"nombre_paso": "'.$paso_info->getAttribute('nombre').'",';
						$id_paso = str_replace(" ", "_", $id_paso);
						$json .= '"id_paso": "'.$id_paso.'",';

						$uptime = $disponibilidad[$obj_id][$paso->paso_orden]['segundos_uptime'];
						$downtime = $disponibilidad[$obj_id][$paso->paso_orden]['segundos_downtime'];
						$factor_total = $disponibilidad[$obj_id][$paso->paso_orden]['factor_total'] ;
						$tiempo_respuesta = ($disponibilidad[$obj_id][$paso->paso_orden]['tiempo_prom']/$cuenta_dias_consultados);

						$factor_total=$uptime + $downtime;
						$uptime_real = ($uptime * 100) / $factor_total;
						$downtime_real = ($downtime * 100) / $factor_total;
						$uptime_real_acumulado += $uptime_real;
						$downtime_real_acumulado += $downtime_real;
						$tiempo_respuesta_acumulado += $tiempo_respuesta;

						$tiempo_uptime = ($segundos_seleccionados * number_format(($uptime_real), 3, '.', '')) / 100;
						$dias_uptime = floor($tiempo_uptime / 86400);
						$tiempo_downtime = ($segundos_seleccionados * number_format(($downtime_real), 3, '.', '')) / 100;
						$dias_downtime = floor($tiempo_downtime / 86400);						

						if (number_format(($uptime_real), 3, '.', '') >= '99.500' && number_format(($uptime_real), 3, '.', '') <= '100.000' ) {
							$color_uptime = "99ffb4";
						}elseif (number_format(($uptime_real), 3, '.', '') <= '99.499' && number_format(($uptime_real), 3, '.', '') >= '98.000' ) {
							$color_uptime = $objetivoEspecial->color_moderado;
						}elseif (number_format(($uptime_real), 3, '.', '') <= '98.000' ) {
							$color_uptime = $objetivoEspecial->color_critico;
						}

						if ($muestra_nombre == "") {
							$nombre_obj_paso = $paso_info->getAttribute('nombre').' - '.$objetivo_info->getAttribute('nombre');
						}else{
							$nombre_obj_paso = $paso_info->getAttribute('nombre');
						}

						if ( strlen($nombre_obj_paso) >= 52) {
							$contador_salto = $contador_salto+2;
						}else{
							$contador_salto++;
						}

						if ($contador_salto >= 26 && $primera_pagina==0) {
							$json .= '"page_break_paso": "page-break-after: always;",';
							$primera_pagina=1;
							$contador_salto=0;
						}elseif($contador_salto >=45  && $primera_pagina!=0) {
							$json .= '"page_break_paso": "page-break-after: always;",';
							$contador_salto=0;
						}else{
							$json .= '"page_break_paso": "",';
						}

						if($paso->flujo){
							$valid= 'inline';
						}else{
							$valid= 'none';
						}
						
						$json .= '"flujo": "'.$paso->flujo.'",';
						$json .= '"pasos_flujo": "'.$paso->pasos.'",';
						$json .= '"valida_secuencia": "'.$valid.'",';

						$json .= '"nombre_objetivo": "'.(($muestra_nombre == "")?' - '.$objetivo_info->getAttribute('nombre') : "").'",';
						$json .= '"nombre_objetivo_tooltip": "'.(($muestra_nombre == "")?$objetivo_info->getAttribute('nombre') : "").'",';
						$json .= '"uptime_real_paso": "'.number_format(($uptime_real), 3, '.', '').'",';
						$json .= '"downtime_real_paso": "'.number_format((($downtime_real)), 3, '.', '').'",';
						$json .= '"tiempo_respuesta_paso": "'.number_format((($tiempo_respuesta)), 3, '.', '').'",';
						$json .= '"tiempo_porcentaje_uptime": "'.(($dias_uptime > 0)?$dias_uptime." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_uptime - ($dias_uptime * 86400)).'",';
						$json .= '"tiempo_porcentaje_downtime": "'.(($dias_downtime > 0)?$dias_downtime." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_downtime - ($dias_downtime * 86400)).'",';
						$json .= '"class_iteracion_paso": "'.$color_uptime.'",';
						$json .= '"color_text": "252525",';
						$json .= '"class_tooltip_paso": "'.$class_tooltip_paso.'",';
						$json .= '"muestra_screenshot": "'.$muestra_screenshot.'",';
						$json .= '"muestra_descarga": "'.$muestra_descarga.'",';
						$json .= '"cant_nodo_paso": "'.$nodo_actual.'",';
						$json .= '"hash": "'.$screenshot_hash.'"';
						$json .= '}'.(($cuenta_pasos == $contador_pasos)?"" : ",");

						$contador++;
						$contador2++;
						$cuenta_pasos++;
					}
					$primer_nodo = false;
				}

				$json .= ']';
				//nodo menor por categoria
				if ($primer_nodo_menor == false) {
					$nodo_anterior_cat = $nodo_menor_cat;
				}else{
					$nodo_anterior_cat = $nodo_menor;
				}

				if ($nodo_anterior_cat <= $nodo_menor ) {
					$nodo_menor_cat = $nodo_anterior_cat;
				}else{
					$nodo_menor_cat = $nodo_menor;
				}
				//nodo mayor por categoria
				if ($primer_nodo == false) {
					$nodo_anterior_mayor_cat = $nodo_mayor_cat;
				}else{
					$nodo_anterior_mayor_cat = $nodo_mayor;
				}

				if ($nodo_anterior_mayor_cat >= $nodo_mayor ) {
					$nodo_mayor_cat = $nodo_anterior_mayor_cat;
				}else{
					$nodo_mayor_cat = $nodo_mayor;
				}
				if ($nodo_menor != $nodo_mayor) {
					$json .= ', "nodo_mm": "ISPs: '.$nodo_menor." - ".$nodo_mayor.'",';
				}else{
					$json .= ', "nodo_mm": "ISPs: '.$nodo_menor.'",';
				}
				
				$uptime_real_acumulado_total += $uptime_real_acumulado;
				$downtime_real_acumulado_total += $downtime_real_acumulado;
				$tiempo_respuesta_acumulado_total += $tiempo_respuesta_acumulado;
				$tiempo_uptime_func = ($segundos_seleccionados * number_format((($uptime_real_acumulado/$contador)), 3, '.', '')) / 100;
				$dias_uptime_func = floor($tiempo_uptime_func / 86400);
				$tiempo_downtime_func = ($segundos_seleccionados * number_format((($downtime_real_acumulado/$contador)), 3, '.', '')) / 100;
				$dias_downtime_func = floor($tiempo_downtime_func / 86400);

				$json .= '"tiempo_uptime_tooltip": "'.((($dias_uptime_func > 0)?$dias_uptime_func." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_uptime_func - ($dias_uptime_func * 86400))).'",';
				$json .= '"tiempo_downtime_tooltip": "'.((($dias_downtime_func > 0)?$dias_downtime_func." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_downtime_func - ($dias_downtime_func * 86400))).'",';
				$json .= '"uptime_real": "'. number_format((($uptime_real_acumulado/$contador)), 3, '.', '').'",';
				$json .= '"downtime_real": "'.number_format((($downtime_real_acumulado/$contador)), 3, '.', '').'",';
				$json .= '"tiempo_respuesta": "'.number_format((($tiempo_respuesta_acumulado/$contador)), 3, '.', '').'"';

				$json .= '}'.(($cant_func == $cuenta_func)?"" : ",");
				$primer_nodo_menor = false;
				$contador_total++;
				$cuenta_func++;
			}
			$json .= '],';
			$tiempo_uptime_cat = ($segundos_seleccionados * number_format(($uptime_real_acumulado_total/$contador2), 3, '.', '')) / 100;
			$dias_uptime_cat = floor($tiempo_uptime_cat / 86400);
			$tiempo_downtime_cat = ($segundos_seleccionados * number_format(($downtime_real_acumulado_total/$contador2), 3, '.', '')) / 100;
			$dias_downtime_cat = floor($tiempo_downtime_cat / 86400);

			$json .= '"uptimecat": "'.number_format(($uptime_real_acumulado_total/$contador2), 3, '.', '').'",';
			$json .= '"downtimecat": "'.number_format(($downtime_real_acumulado_total/$contador2), 3, '.', '').'",';
			$json .= '"tiemporespuesta": "'.number_format(($tiempo_respuesta_acumulado_total/$contador2), 3, '.', '').'",';
			$json .= '"tiempo_uptime_tooltip": "'.((($dias_uptime_cat > 0)?$dias_uptime_cat." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_uptime_cat - ($dias_uptime_cat * 86400))).'"';
			$categorias .= '"disponibilidad": [{';
			$categorias .= '"uptimecat": "'.number_format(($uptime_real_acumulado_total/$contador2), 3, '.', '').'",';
			$categorias .= '"downtimecat": "'.number_format(($downtime_real_acumulado_total/$contador2), 3, '.', '').'",';
			$categorias .= '"tiemporespuesta": "'.number_format(($tiempo_respuesta_acumulado_total/$contador2), 3, '.', '').'",';
			$categorias .= '"tiempo_uptime_tooltip": "'.((($dias_uptime_cat > 0)?$dias_uptime_cat." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_uptime_cat - ($dias_uptime_cat * 86400))).'",';
			$categorias .= '"tiempo_downtime_tooltip": "'.((($dias_downtime_cat > 0)?$dias_downtime_cat." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_downtime_cat - ($dias_downtime_cat * 86400))).'",';
			$categorias .= '"min_max" : "Mínimo de ISPs: '.$nodo_menor_cat.' - Máximo de ISPs: '.$nodo_mayor_cat.'"';
			$categorias .= '}]}';

			$json .= '}'.(($cant_cat == $cuenta_cat)?"" : ",");
			$categorias .= (($cant_cat == $cuenta_cat)?"" : ",");

			$cuenta_cat++;
		}
		$json .= ']';
		$json .= ',"categoria_global" : [';
		$json .= $categorias;
		$json .= ']}';
		$json .= ']}';

		return $json;

	}

	function vistaRapidaUnificada(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$T = & new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		if (isset($_REQUEST['es_pdf'])) {
			$T->setFile('tpl_tabla', 'vista_rapida_unificada_print.tpl');
		}else{
			$T->setFile('tpl_tabla', 'vista_rapida_unificada.tpl');
		}
		$T->setBlock('tpl_tabla', 'BLOQUE_PASO', 'bloque_paso');
		$T->setBlock('tpl_tabla', 'BLOQUE_FUNCIONALIDAD', 'bloque_funcionalidad');
		$T->setBlock('tpl_tabla', 'BLOQUE_CATEGORIAS_2', 'bloque_categorias_2');
		$T->setBlock('tpl_tabla', 'BLOQUE_CATEGORIAS', 'bloque_categorias');
		$T->setBlock('tpl_tabla', 'BLOQUE_CATEGORIA', 'bloque_categoria');

		$sql2 = "SELECT _cliente_tz(".pg_escape_string($current_usuario_id).")";
		$res0 =& $mdb2->query($sql2);

		if (MDB2::isError($res0)) {
			$log->setError($sql2, $res0->userinfo);
			exit();
		}
		$row = $res0->fetchRow();
		$tz= $row["_cliente_tz"];

		$timestamp = time();
		$dt = new DateTime("now", new DateTimeZone($tz));
		$dt->setTimestamp($timestamp);

		$T->setVar('__objetivo_especial', $this->objetivo_id);
		$T->setVar('__horario_id', $this->horario_id);
		$T->setVar('__usuario_id', $current_usuario_id);

		$objetivoEspecial=new ConfigEspecial($this->objetivo_id);

		$T->setVar('__fecha_inicio', date("Y-m-d"));
		$T->setVar('__reporte_period_start', $objetivoEspecial->period_start);
		switch($objetivoEspecial->date_selection) {
			case "no":
			$T->setVar('__calendario_permite_seleccionar', 'false');
			$T->setVar('__calendario_selecciona_intervalo', 'false');
			break;
			case "day":
			$T->setVar('__calendario_permite_seleccionar', 'true');
			$T->setVar('__calendario_selecciona_intervalo', 'false');
			break;
			case "interval":
			$T->setVar('__calendario_permite_seleccionar', 'true');
			$T->setVar('__calendario_selecciona_intervalo', 'true');
			break;
			default:
			$T->setVar('__calendario_permite_seleccionar', 'true');
			$T->setVar('__calendario_selecciona_intervalo', 'true');
		}

		$T->setVar('bloque_categoria', '');
		$T->setVar('bloque_categoria_2', '');
		$cuenta_cat = 1;
		$tiempo_uptime_cat = 0;
		$dias_uptime_cat = 0;
		$primera_pagina=0;
		$cant_cat = count($objetivoEspecial->__conjuntos);		
		foreach ($objetivoEspecial->__conjuntos as $nombre_categoria => $conjuntos) {
			$T->setVar('__nombre_categoria', $nombre_categoria);
			$T->setVar('__page_break', ($cant_cat == $cuenta_cat)?"" : "page-break-after: always;");

			$contador_salto = 1;
			$primer_nodo_menor = true;
			$nodo_anterior_cat = 0;
			$nodo_menor_cat = 0;
			$nodo_anterior_mayor_cat = 0;
			$nodo_mayor_cat = 0;
			$contador_total = 0;
			$uptime_real_acumulado_total = 0;
			$downtime_real_acumulado_total = 0;
			$tiempo_respuesta_acumulado_total = 0;
			$downtime_real_acumulado = 0;
			$tiempo_respuesta_acumulado = 0;
			$contador2 = 0;
			$primer_nodo = true;
			$uptime_real_acumulado_pasos = 0;
			$T->setVar('bloque_funcionalidad', '');
			foreach ($conjuntos as $nombre_funcionalidad => $conjunto) {
				if ($contador_total%2 == 0) {
					$T->setVar('__class_iteracion', 'iteracionGris');
				}else{
					$T->setVar('__class_iteracion', 'iteracionNaranjo');
				}

				$T->setVar('__nombre_funcionalidad', $nombre_funcionalidad);

				$primero = true;
				$sql_objetivos = "ARRAY[";
				foreach ($conjunto as $objetivo_id => $objetivo) {
					$objetivo_id =explode('|', $objetivo_id)[0];
					foreach ($objetivo as $paso_orden => $paso) {
						$paso_orden = explode('|', $paso_orden)[0];
						$sql_objetivos.= (($primero)?"":",")."ARRAY[".$objetivo_id.",".$paso_orden."]";
						$primero = false;
					}
				}
				$sql_objetivos .= "]";

				$fecha_inicio = strtotime($this->timestamp->getInicioPeriodo());
				$fecha_termino = strtotime($this->timestamp->getTerminoPeriodo());
				$dia = 86400;
				$disponibilidad = array();
				$cuenta_dias_consultados=0;
				while ($fecha_inicio < $fecha_termino) {
					$fecha_inicio_consulta = date("Y-m-d H:i:s", $fecha_inicio);
					$fecha_termino_consulta = date("Y-m-d H:i:s", $fecha_inicio+86400);
					$sql = "SELECT * FROM reporte.especial_disponibilidad_resumen_objetivopaso_v2(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($sql_objetivos).", ".
					pg_escape_string($this->horario_id).", '".
					pg_escape_string($fecha_inicio_consulta)."', '".
					pg_escape_string($fecha_termino_consulta)."')";

					$fecha_inicio += $dia;
					$res =& $mdb2->query($sql);
					if (MDB2::isError($res)) {
						$log->setError($sql, $res->userinfo);
						exit();
					}
					$row = $res->fetchRow();
					$dom = new DomDocument();
					$dom->preserveWhiteSpace = FALSE;
					$dom->loadXML($row["especial_disponibilidad_resumen_objetivopaso_v2"]);
					$xpath = new DOMXpath($dom);
					foreach ($conjunto as $obj_id => $objetivo) {
						$obj_id =explode('|', $obj_id)[0];
						foreach ($objetivo as $key_pasos => $paso) {
							$conf_dato = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$obj_id."]/detalles/detalle[@paso_orden=".$paso->paso_orden."]/datos/dato")->item(0);
							$disponibilidad[$obj_id][$paso->paso_orden]['uptime'] += $conf_dato->getAttribute('uptime') +  $conf_dato->getAttribute('downtime_parcial');
							$disponibilidad[$obj_id][$paso->paso_orden]['downtime'] += $conf_dato->getAttribute('downtime');
							$disponibilidad[$obj_id][$paso->paso_orden]['tiempo_prom'] += $conf_dato->getAttribute('tiempo_prom');
							$disponibilidad[$obj_id][$paso->paso_orden]['sin_monitoreo'] += $conf_dato->getAttribute('sin_monitoreo');

							$disponibilidad[$obj_id][$paso->paso_orden]['segundos_uptime'] += $conf_dato->getAttribute('segundos_uptime') +  $conf_dato->getAttribute('segundos_downtime_parcial');
							$disponibilidad[$obj_id][$paso->paso_orden]['segundos_downtime'] += $conf_dato->getAttribute('segundos_downtime');
							$disponibilidad[$obj_id][$paso->paso_orden]['segundos_sin_monitoreo'] += $conf_dato->getAttribute('segundos_sin_monitoreo');
							$disponibilidad[$obj_id][$paso->paso_orden]['factor_total'] += $conf_dato->getAttribute('segundos_uptime') +  $conf_dato->getAttribute('segundos_downtime_parcial') + $conf_dato->getAttribute('segundos_downtime');
						}
					}
					$cuenta_dias_consultados++;
				}

				$res =& $mdb2->query($sql);
				if (MDB2::isError($res)) {
					$log->setError($sql, $res->userinfo);
					exit();
				}

				$row = $res->fetchRow();
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row["especial_disponibilidad_resumen_objetivopaso_v2"]);
				$xpath = new DOMXpath($dom);
				//periodo seleccionado en segundos
				if ( strtotime($this->timestamp->getTerminoPeriodo()) > strtotime(date("Y-m-d H:i:s")) ) {
					$segundos_seleccionados = (strtotime($dt->format("Y-m-d H:i:s")) - strtotime($this->timestamp->getInicioPeriodo()));
				}else{
					$segundos_seleccionados = (strtotime($this->timestamp->getTerminoPeriodo()) - strtotime($this->timestamp->getInicioPeriodo()));
				}
				$primer_nodo = true;
				$nodo_menor = 0;
				$nodo_anterior = 0;
				$nodo_actual = 0;
				$nodo_mayor = 0;
				$nodo_anterior_mayor = 0;
				$tiempo_uptime_func = 0;
				$dias_uptime_func = 0;
				$contador_pasos = 0;
				$uptime_real_acumulado = 0;
				$downtime_real_acumulado = 0;
				$tiempo_respuesta_acumulado = 0;
				$contador = 0;
				$first = true;
				$sql_obj_func = "ARRAY[";
				$T->setVar('bloque_paso', '');
				foreach ($conjunto as $obj_id => $objetivo) {
					$muestra_nombre = explode('|', $obj_id)[1];
					$obj_id =explode('|', $obj_id)[0];

					$sql_nodos_mm = "SELECT count(*) as cuenta_nodos from (
																	SELECT unnest(_nodos_id) from (
																		SELECT _nodos_id(".
																		pg_escape_string($current_usuario_id).", ".
																		pg_escape_string($obj_id).", '".
																		pg_escape_string($this->timestamp->getInicioPeriodo())."'
																		)
																	) as foo
																) as foo2";
					$res_nodos_mm =& $mdb2->query($sql_nodos_mm);
					if (MDB2::isError($res_nodos_mm)) {
						$log->setError($sql_nodos_mm, $res_nodos_mm->userinfo);
						exit();
					}

					$row_nodos_mm = $res_nodos_mm->fetchRow();

					$nodo_actual = $row_nodos_mm['cuenta_nodos'];

					if ($primer_nodo == false) {
						$nodo_anterior = $nodo_menor;
					}else{
						$nodo_anterior = $nodo_actual;
					}

					if ($nodo_anterior <= $nodo_actual ) {
						$nodo_menor = $nodo_anterior;
					}else{
						$nodo_menor = $nodo_actual;
					}

					if ($primer_nodo == false) {
						$nodo_anterior_mayor = $nodo_mayor;
					}else{
						$nodo_anterior_mayor = $nodo_actual;
					}

					if ($nodo_anterior_mayor >= $nodo_actual ) {
						$nodo_mayor = $nodo_anterior_mayor;
					}else{
						$nodo_mayor = $nodo_actual;
					}

					$tiempo_uptime = 0;
					$dias_uptime = 0;
					$color_uptime = '';
					foreach ($objetivo as $key_pasos => $paso) {
						$arraypasosflujo=(json_decode(($paso->pasos), true));
						$screenshot_hash = explode('|', $key_pasos)[1];
						$id_paso = $nombre_categoria.'_'.$nombre_funcionalidad.'_'.$obj_id.'_'.$paso->paso_orden.'_'.$screenshot_hash;

						if ($screenshot_hash != "") {
							$T->setVar('__class_tooltip_paso', 'screenshotPaso');
							$T->setVar('__muestra_screenshot', 'inline');
							$T->setVar('__muestra_descarga', 'inline');
						}else{
							$T->setVar('__class_tooltip_paso', 'nombreObjetivo');
							$T->setVar('__muestra_screenshot', 'none');
							$T->setVar('__muestra_descarga', 'none');
						}
						
						$id_paso = str_replace(" ", "_", $id_paso);
						$T->setVar('__id_paso', $id_paso);
						$T->setVar('__screenshot_hash', $screenshot_hash);

						$paso_info= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$obj_id."]/paso[@paso_orden=".$paso->paso_orden."]")->item(0);
						$conf_dato = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$obj_id."]/detalles/detalle[@paso_orden=".$paso->paso_orden."]/datos/dato")->item(0);
						$objetivo_info = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$obj_id."]")->item(0);

						$uptime = $disponibilidad[$obj_id][$paso->paso_orden]['segundos_uptime'];
						$downtime = $disponibilidad[$obj_id][$paso->paso_orden]['segundos_downtime'];
						$factor_total = $disponibilidad[$obj_id][$paso->paso_orden]['factor_total'] ;
						$tiempo_respuesta = ($disponibilidad[$obj_id][$paso->paso_orden]['tiempo_prom']/$cuenta_dias_consultados);

						$uptime_real = ($uptime * 100) / $factor_total;
						$downtime_real = ($downtime * 100) / $factor_total;
						$uptime_real_acumulado += $uptime_real;
						$downtime_real_acumulado += $downtime_real;
						$tiempo_respuesta_acumulado += $tiempo_respuesta;

						$tiempo_uptime = ($segundos_seleccionados * number_format(($uptime_real), 3, '.', '')) / 100;
						$dias_uptime = floor($tiempo_uptime / 86400);
						$tiempo_downtime = ($segundos_seleccionados * number_format(($downtime_real), 3, '.', '')) / 100;
						$dias_downtime = floor($tiempo_downtime / 86400);

						if($paso->flujo){
							$valid= 'inline';
						}else{
							$valid= 'none';
						}
						$T->setVar('__pasos_flujo', $paso->pasos);
						$T->setVar('__flujo', $paso->flujo);
						$T->setVar('__valid', $valid);
						$T->setVar('__nombre_objetivo', ($muestra_nombre == "")?' - '.$objetivo_info->getAttribute('nombre') : "");
						$T->setVar('__nombre_objetivo_tooltip', ($muestra_nombre == "")?$objetivo_info->getAttribute('nombre') : "");
						$T->setVar('__nombre_paso', $paso_info->getAttribute('nombre'));
						$T->setVar('__uptime_real_paso', number_format(($uptime_real), 3, '.', ''));
						$T->setVar('__tiempo_porcentaje_uptime', (($dias_uptime > 0)?$dias_uptime." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_uptime - ($dias_uptime * 86400)));
						$T->setVar('__tiempo_porcentaje_downtime', (($dias_downtime > 0)?$dias_downtime." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_downtime - ($dias_downtime * 86400)));
						$T->setVar('__downtime_real_paso',number_format((($downtime_real)), 3, '.', '') );
						$T->setVar('__tiempo_respuesta_paso',number_format((($tiempo_respuesta)), 3, '.', ''));	
						$T->setVar('__cant_nodo_paso', $nodo_actual);

						if (number_format(($uptime_real), 3, '.', '') >= '99.500' && number_format(($uptime_real), 3, '.', '') <= '100.000' ) {
							$color_uptime = "99ffb4";
						}elseif (number_format(($uptime_real), 3, '.', '') <= '99.499' && number_format(($uptime_real), 3, '.', '') >= '98.000' ) {
							$color_uptime = $objetivoEspecial->color_moderado;
						}elseif (number_format(($uptime_real), 3, '.', '') <= '98.000' ) {
							$color_uptime = $objetivoEspecial->color_critico;
						}

						$T->setVar('__class_iteracion_paso', $color_uptime);
						$T->setVar('__color_text', "252525");
						if ($muestra_nombre == "") {
							$nombre_obj_paso = $paso_info->getAttribute('nombre').' - '.$objetivo_info->getAttribute('nombre');
						}else{
							$nombre_obj_paso = $paso_info->getAttribute('nombre');
						}

						if ( strlen($nombre_obj_paso) >= 52) {
							$contador_salto = $contador_salto+2;
						}else{
							$contador_salto++;
						}

						if ($contador_salto >= 26 && $primera_pagina==0) {
							$T->setVar('__page_break_paso', "page-break-after: always;");
							$primera_pagina=1;
							$contador_salto=0;
						}elseif($contador_salto >=45  && $primera_pagina!=0) {
							$T->setVar('__page_break_paso', "page-break-after: always;");
							$contador_salto=0;
						}else{
							$T->setVar('__page_break_paso', "");
						}
						$T->parse('bloque_paso', 'BLOQUE_PASO', true);
						$contador++;
						$contador2++;

						
					}					
					$primer_nodo = false;
				}

				//nodo menor por categoria
				if ($primer_nodo_menor == false) {
					$nodo_anterior_cat = $nodo_menor_cat;
				}else{
					$nodo_anterior_cat = $nodo_menor;
				}

				if ($nodo_anterior_cat <= $nodo_menor ) {
					$nodo_menor_cat = $nodo_anterior_cat;
				}else{
					$nodo_menor_cat = $nodo_menor;
				}
				//nodo mayor por categoria
				if ($primer_nodo == false) {
					$nodo_anterior_mayor_cat = $nodo_mayor_cat;
				}else{
					$nodo_anterior_mayor_cat = $nodo_mayor;
				}

				if ($nodo_anterior_mayor_cat >= $nodo_mayor ) {
					$nodo_mayor_cat = $nodo_anterior_mayor_cat;
				}else{
					$nodo_mayor_cat = $nodo_mayor;
				}
				if ($nodo_menor != $nodo_mayor) {
					$T->setVar('__nodo_mm', $nodo_menor." - ".$nodo_mayor);
				}else{
					$T->setVar('__nodo_mm', $nodo_menor);
				}
				
				$uptime_real_acumulado_total += $uptime_real_acumulado;
				$downtime_real_acumulado_total += $downtime_real_acumulado;
				$tiempo_respuesta_acumulado_total += $tiempo_respuesta_acumulado;
				$tiempo_uptime_func = ($segundos_seleccionados * number_format((($uptime_real_acumulado/$contador)), 3, '.', '')) / 100;
				$dias_uptime_func = floor($tiempo_uptime_func / 86400);
				$tiempo_downtime_func = ($segundos_seleccionados * number_format((($downtime_real_acumulado/$contador)), 3, '.', '')) / 100;
				$dias_downtime_func = floor($tiempo_downtime_func / 86400);

				$T->setVar('__tiempo_porcentaje_uptime_func', (($dias_uptime_func > 0)?$dias_uptime_func." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_uptime_func - ($dias_uptime_func * 86400)));
				$T->setVar('__tiempo_porcentaje_downtime_func', (($dias_downtime_func > 0)?$dias_downtime_func." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_downtime_func - ($dias_downtime_func * 86400)));
				$T->setVar('__uptime_real', number_format((($uptime_real_acumulado/$contador)), 3, '.', ''));
				$T->setVar('__downtime_real',number_format((($downtime_real_acumulado/$contador)), 3, '.', '') );
				$T->setVar('__tiempo_respuesta',number_format((($tiempo_respuesta_acumulado/$contador)), 3, '.', ''));
				$primer_nodo_menor = false;
				$contador_total++;
				$T->parse('bloque_funcionalidad', 'BLOQUE_FUNCIONALIDAD', true);
			}
			$tiempo_uptime_cat = ($segundos_seleccionados * number_format(($uptime_real_acumulado_total/$contador2), 3, '.', '')) / 100;
			$dias_uptime_cat = floor($tiempo_uptime_cat / 86400);
			$tiempo_downtime_cat = ($segundos_seleccionados * number_format(($downtime_real_acumulado_total/$contador2), 3, '.', '')) / 100;
			$dias_downtime_cat = floor($tiempo_downtime_cat / 86400);

			$T->setVar('__tiempo_porcentaje_uptime_cat', (($dias_uptime_cat > 0)?$dias_uptime_cat." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_uptime_cat - ($dias_uptime_cat * 86400)));
			$T->setVar('__tiempo_porcentaje_downtime_cat', (($dias_downtime_cat > 0)?$dias_downtime_cat." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_downtime_cat - ($dias_downtime_cat * 86400)));
			$T->setVar('__uptime_real_total', number_format(($uptime_real_acumulado_total/$contador2), 3, '.', ''));
			$T->parse('bloque_categoria', 'BLOQUE_CATEGORIA', true);

			$T->setVar('__tiempo_porcentaje_uptime_cat', (($dias_uptime_cat > 0)?$dias_uptime_cat." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_uptime_cat - ($dias_uptime_cat * 86400)));
			$T->setVar('__tiempo_porcentaje_downtime_cat', (($dias_downtime_cat > 0)?$dias_downtime_cat." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_downtime_cat - ($dias_downtime_cat * 86400)));
			$T->setVar('__uptime_real_total', number_format(($uptime_real_acumulado_total/$contador2), 3, '.', ''));
			$T->setVar('__downtime_real_total', number_format(($downtime_real_acumulado_total/$contador2), 3, '.', ''));
			$T->setVar('__tiempo_respuesta_total', number_format(($tiempo_respuesta_acumulado_total/$contador2), 3, '.', ''));
			$T->parse('bloque_categorias', 'BLOQUE_CATEGORIAS', true);

			$T->setVar('__nodo_mm_cat', ("Mínimo de ISPs: ".$nodo_menor_cat." - Máximo de ISPs: ".$nodo_mayor_cat));
			$T->parse('bloque_categorias_2', 'BLOQUE_CATEGORIAS_2', true);
			$cuenta_cat++;
		}
		$this->resultado = $T->parse('out', 'tpl_tabla');
	}
	    /*
     	Creado por:
    	Modificado por: Aldo Cruz Romero
    	Fecha de creacion:23-12-2017
    	Fecha de ultima modificacion:19-01-2018
    	*/
		public function getEspecialPersonal(){
		
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		
		$negocios= $this->extra['select_obj'];
		$negocios = json_decode($negocios);
		$conf_eventos=null;
		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_tabla', 'especial_personal.tpl');
		$T->setBlock('tpl_tabla', 'TIENE_EVENTO_DATO', 'tiene_evento_dato');
		$T->setBlock('tpl_tabla', 'LISTA_NEGOCIOS', 'lista_negocios');
		$T->setBlock('tpl_tabla', 'BLOQUE_TITULO_EVENTO', 'bloque_titulo_evento');
		$T->setBlock('tpl_tabla', 'BLOQUE_DISPONIBILIDAD', 'bloque_disponibilidad');

		$subObjetivo = null;
		$muestra_titulo=true;

		$T->setVar('bloque_disponibilidad', '');
		for ($i=0; $i <3 ; $i++) {
			$T->setVar('lista_negocios', '');
			//obtiene nombre negocio
			foreach ($negocios as $key => $conf_negocio) {
				$T->setVar('__negocio', $conf_negocio->nombre);
				$acumulador_porcentaje=null;
				$count_obj=0;
				$T->setVar('tiene_evento_dato', '');
				//obtiene id de objetivo por negocio
				foreach ($conf_negocio->objetivos as $key => $conf_objetivo) {
					$count_obj ++;
					$subobjetivo_id=$conf_objetivo;
					$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($subobjetivo_id).", ".
					pg_escape_string($this->horario_id).",' ".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."', ".
					(isset($this->extra["variable"])?$usr->cliente_id:'0').")";
					//echo $sql.'<br>';
					$res =& $mdb2->query($sql);
					if (MDB2::isError($res)) {
						$log->setError($sql, $res->userinfo);
						exit();
					}
					if($row = $res->fetchRow()){
						$dom = new DomDocument();
						$dom->preserveWhiteSpace = FALSE;
						$dom->loadXML($row["disponibilidad_resumen_consolidado"]);
						$xpath = new DOMXpath($dom);
						unset($row["disponibilidad_resumen_consolidado"]);
					}
					$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
	  				$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
	  				$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
	  				$subObjetivo = $conf_objetivo->getAttribute('nombre');
	  				$T->setVar('__paso_objetivos', $subObjetivo);
					$T->parse('tiene_evento_dato', 'TIENE_EVENTO_DATO', true);

	  				foreach ($conf_pasos as $conf_paso) {
	  					$tag_paso =$xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$subobjetivo_id."]/detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]")->item(0);
	  					foreach ($conf_eventos as $conf_evento) {
	  						$evento = $conf_evento->getAttribute('evento_id');
	  						$tag_dato = $xpath->query("estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute('evento_id')."]",$tag_paso)->item(0);
	  						$porcentaje= isset($tag_dato)?$tag_dato->getAttribute('porcentaje'):0;
	  						$acumulador_porcentaje[$evento] += $porcentaje/$conf_pasos->length;	
		  				}
	  				}
	  				if($muestra_titulo){
	  					$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
	  				}
	  				$muestra_titulo=false;
	  			}
	  			foreach ($conf_eventos as $conf_evento) {
	  				$evento = $conf_evento->getAttribute('evento_id');
	  				$acumulador_negocio[$evento] = $acumulador_porcentaje[$evento]/$count_obj;
				}
				//numero acumulado de eventos por negocio
			  	$uptime = $acumulador_negocio[1];
				$downtime = $acumulador_negocio[2];
				$downtime_parcial = $acumulador_negocio[3];
				$sin_monitoreo = $acumulador_negocio[7];

				if($i==0){
					//establece calculo para disponibilidad especial
					$uptime_especial = $uptime + $downtime_parcial;
					$total_especial = $uptime_especial + $downtime;
					$uptime_especial = $uptime_especial*100/$total_especial;
					$downtime_especial = $downtime*100/$total_especial;
					
					$uptime_td_especial = '<td class="txtBlanco12 celdaUptime" align="right">'.number_format($uptime_especial, 2, '.', '').'</td>';
					$downtime_td_especial = '<td class="txtBlanco12 celdaDtGlobal" align="right">'.number_format($downtime_especial, 2, '.', '').'</td>';
					
					$T->setVar('__paso_uptime', $uptime_td_especial );
					$T->setVar('__paso_downtime_global', $downtime_td_especial);
					$T->setVar('__paso_down_parcial', '');
					$T->setVar('__paso_sin_monitoreo', '');
				}
				if($i==1){
					//establece calculo para disponibilidad real
					$total_real = $uptime + $downtime+ $downtime_parcial;
					$uptime_real = $uptime*100/$total_real;
					$downtime_real = $downtime*100/$total_real;
					$downtime_parcial_real = $downtime_parcial*100/$total_real;

					$uptime_td_real = '<td class="txtBlanco12 celdaUptime" align="right">'.number_format($uptime_real, 2, '.', '').'</td>';
					$downtime_td_real = '<td class="txtBlanco12 celdaDtGlobal" align="right">'.number_format($downtime_real, 2, '.', '').'</td>';
					$downtime_td_parcial_real = '<td class="txtBlanco12 celdaDtParcial" align="right">'.number_format($downtime_parcial_real, 2, '.', '').'</td>';
					
					$T->setVar('__paso_uptime', $uptime_td_real);
					$T->setVar('__paso_downtime_global',$downtime_td_real);
					$T->setVar('__paso_down_parcial', $downtime_td_parcial_real);
					$T->setVar('__paso_sin_monitoreo', '');
				}
				if($i==2){
					//establece calculo para disponibilidad normal
					$total_disponible = $uptime + $downtime+$downtime_parcial + $sin_monitoreo;
					$uptime_disponible = $uptime*100/$total_disponible;
					$downtime_disponible = $downtime*100/$total_disponible;
					$downtime_parcial_disponible = $downtime_parcial*100/$total_disponible;
					$sin_monitoreo_disponible = $sin_monitoreo*100/$total_disponible;
					
					$uptime_td_disponible = '<td class="txtBlanco12 celdaUptime" align="right">'.number_format($uptime_disponible, 2, '.', '').'</td>';
					$downtime_td_disponible = '<td class="txtBlanco12 celdaDtGlobal" align="right">'.number_format($downtime_disponible, 2, '.', '').'</td>';
					$downtime_td_parcial_disponible = '<td class="txtBlanco12 celdaDtParcial" align="right">'.number_format($downtime_parcial_disponible, 2, '.', '').'</td>';
					$sin_monitoreo_td_disponible = '<td class="txtBlanco12 celdaSinMonitoreo"  align="right">'.number_format($sin_monitoreo_disponible, 2, '.', '').'</td>';
					
					$T->setVar('__paso_uptime', $uptime_td_disponible);
					$T->setVar('__paso_downtime_global',$downtime_td_disponible);
					$T->setVar('__paso_down_parcial', $downtime_td_parcial_disponible);
					$T->setVar('__paso_sin_monitoreo', $sin_monitoreo_td_disponible);
				}
				$T->parse('lista_negocios', 'LISTA_NEGOCIOS', true);
			}
			$T->setVar('bloque_titulo_evento', '');
			foreach ($conf_eventos as $conf_evento) {
				if($i==0 && ($conf_evento->getAttribute('orden')==0 || $conf_evento->getAttribute('orden')==2 )){
					$evento = $conf_evento->getAttribute('nombre');
					$T->setVar('__nombre_tabla_negocio', 'Disponibilidad Especial'.'<br>');
					$T->setVar('__titulo_evento', $evento);
					$T->parse('bloque_titulo_evento', 'BLOQUE_TITULO_EVENTO',true);
				}
				if($i==1 && ($conf_evento->getAttribute('orden')==0 || $conf_evento->getAttribute('orden')==2 || $conf_evento->getAttribute('orden')==1)){
					$evento = $conf_evento->getAttribute('nombre');
					$T->setVar('__nombre_tabla_negocio', 'Disponibilidad Real'.'<br>');
					$T->setVar('__titulo_evento', $evento);
					$T->parse('bloque_titulo_evento', 'BLOQUE_TITULO_EVENTO',true);
				}
				if($i==2 && ($conf_evento->getAttribute('orden')==0 || $conf_evento->getAttribute('orden')==2 || $conf_evento->getAttribute('orden')==1||$conf_evento->getAttribute('orden')==3 )){
					$evento = $conf_evento->getAttribute('nombre');
					$T->setVar('__nombre_tabla_negocio', 'Disponibilidad Normal'.'<br>');
					$T->setVar('__titulo_evento', $evento);
					$T->parse('bloque_titulo_evento', 'BLOQUE_TITULO_EVENTO',true);
				}
			}
			$T->parse('bloque_disponibilidad', 'BLOQUE_DISPONIBILIDAD', true);
		}
		$this->tiempo_expiracion = $parser->tiempo_expiracion;
			$this->resultado = $T->parse('out', 'tpl_tabla');
	}


/*	function getEspecialDisponibilidadFullObjetivos() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		global $externa;

		$objetivo = new ConfigEspecial($this->objetivo_id);

		$T = & new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_tabla', 'especial_disponibilidad_objetivos.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_tabla', 'LISTA_OBJETIVOS', 'lista_objetivos');

		$indice = 1;

		$T->setVar('lista_objetivos', '');
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {

			$sql0 = "SELECT DISTINCT su.cliente_usuario_id
			FROM cliente_usuario u, cliente_subcliente s, cliente_mapa_subcliente_objetivo so, cliente_mapa_subcliente_usuario su
			WHERE u.cliente_usuario_id=".$current_usuario_id." AND s.cliente_id=u.cliente_id AND s.cliente_subcliente_id=so.cliente_subcliente_id AND s.cliente_subcliente_id=su.cliente_subcliente_id AND so.objetivo_id=".$subobjetivo->objetivo_id." LIMIT 1;";

			$res0 = & $mdb2->query($sql0);
			if (MDB2::isError($res0)) {
//				exit();
				continue;
			}
			if ($row0 = $res0->fetchRow()) {
				$nuevo_usuario = $row0["cliente_usuario_id"];
			}

			$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado(".
					pg_escape_string($nuevo_usuario).",".
					pg_escape_string($subobjetivo->objetivo_id).", ".
					pg_escape_string($this->horario_id).",' ".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."', 0)";

			//print($sql);
			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
//				$log->setError($sql, $res->userinfo);
//				exit();
				continue;
			}
			if ($row = $res->fetchRow()) {
				$parser = new XMLParserReporte($row["disponibilidad_resumen_consolidado"]);
				$parser->getDatosDetalladoDisponibilidad();
				$tag_objetivo =& $parser->__objetivos[$subobjetivo->objetivo_id];
				unset($row["disponibilidad_resumen_consolidado"]);
			}


			if (count($tag_objetivo->__pasos) > 0) {

				$T->setVar('__item_orden', $this->extra["item_orden"]);
				$T->setVar('__objetivo_orden', $indice);
				$T->setVar('__objetivo_nombre', $subobjetivo->nombre);

				$pasos = $tag_objetivo->__monitores[0]->__pasos;
				$estilo_fila = 'celdaIteracion1';

				$T->setVar('lista_pasos', '');
				foreach ($pasos as $paso) {
					if ($estilo_fila == 'celdaIteracion2') {
						$estilo_fila = 'celdaIteracion1';
					}
					else{
						$estilo_fila = 'celdaIteracion2';
					}
					if(isset($externa) && $externa == true){

						$T->setVar('__paso_estilo_mail', ($estilo_fila == 'celdaIteracion1')?'padding: 1px 6px 1px 6px; background-color: #ffffff; border: solid 1px #a2a2a2;':'padding: 1px 6px 1px 6px; background-color: #ebebeb; border: solid 1px #a2a2a2;');
					}


					$T->setVar('__paso_nombre', $paso->nombre);
					$T->setVar('__paso_estilo', $estilo_fila);
					$T->setVar('__paso_uptime', number_format($paso->__eventos[0]->porcentaje, 2, '.', ''));
					$T->setVar('__paso_downtime_parcial', number_format($paso->__eventos[1]->porcentaje, 2, '.', ''));
					$T->setVar('__paso_downtime_global', number_format($paso->__eventos[2]->porcentaje, 2, '.', ''));
					$T->setVar('__paso_sinmonitoreo', number_format($paso->__eventos[3]->porcentaje, 2, '.', ''));
					$T->parse('lista_pasos', 'LISTA_PASOS', true);
				}

				$T->parse('lista_objetivos', 'LISTA_OBJETIVOS', true);
				$indice++;
			}
		}
		$this->resultado = $T->parse('out', 'tpl_tabla');		
	}
*/

/*	function getEspecialRendimientoDiasPeak() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$fechas = array(array("inicio" => "'%Y-%m-01 00:00:00'", "termino" => "'%Y-%m-11 00:00:00'"),
				array("inicio" => "'%Y-%m-11 00:00:00'", "termino" => "'%Y-%m-13 13:45:00'"),
				array("inicio" => "'%Y-%m-14 00:00:00'", "termino" => "'%Y-%m-01 00:00:00':: TIMESTAMP WITHOUT TIME ZONE + '1 month'::INTERVAL"));

		$ponderacion = $usr->getPonderacion();
		if ($ponderacion == null) {
			$ponderacion_id = 0;
		}
		else {
			$ponderacion_id = $ponderacion->ponderacion_id;
		}

		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_tabla', 'especial_rendimiento_dias_peak.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_PONDERACION_TITULO', 'lista_ponderacion_titulo');
		$T->setBlock('tpl_tabla', 'LISTA_PONDERACION', 'lista_ponderacion');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_tabla', 'LISTA_PONDERACION_PROMEDIO', 'lista_ponderacion_promedio');
		$T->setBlock('tpl_tabla', 'LISTA_FECHAS', 'lista_fechas');
		$T->setBlock('tpl_tabla', 'RESUMEN_PONDERACION', 'resumen_ponderacion');
		$T->setBlock('tpl_tabla', 'RESUMEN_FECHAS', 'resumen_fechas');
		$T->setBlock('tpl_tabla', 'RESUMEN_PONDERACION_PROMEDIO', 'resumen_ponderacion_promedio');

		foreach ($fechas as $i => $fecha) {
			$inicio = strftime($fecha["inicio"], strtotime($this->timestamp->fecha_inicio));
			$termino = strftime($fecha["termino"], strtotime($this->timestamp->fecha_inicio));

			$sql = "SELECT * FROM reporte.rendimiento_resumen_global_ponderado(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					pg_escape_string($ponderacion_id).", ".
					$inicio.", ".
					$termino.")";
//			print($sql);
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["rendimiento_resumen_global_ponderado"]);
			$xpath = new DOMXpath($dom);

			$param = $xpath->query("/atentus/resultados/parametros")->item(0);
			$ponderaciones = $xpath->query("/atentus/resultados/propiedades/ponderaciones/item[@valor!=0]");
			$pasos = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]");

			$T->setVar('__item_orden', $this->extra["item_orden"]);
			$T->setVar('__fecha_orden', ($i + 1));
			$T->setVar('__fecha_descripcion', "D&iacute;as ".date("d", strtotime($param->getAttribute("fecha_inicio")))."-".date("d", strtotime($param->getAttribute("fecha_termino")) - 1));

			$linea = 1;
			$T->setVar('lista_ponderacion_titulo', '');
			foreach ($ponderaciones as $ponderacion) {
				$fecha_promedio[$ponderacion->getAttribute("item_id")] = 0;
//				$ponderado_promedio[$ponderacion->getAttribute("item_id")] = 0;
				$T->setVar('__ponderacion_periodo', date("H", strtotime($ponderacion->getAttribute("inicio")))." - ".date("H", strtotime($ponderacion->getAttribute("termino")))." Hrs");
				$T->parse('lista_ponderacion_titulo', 'LISTA_PONDERACION_TITULO', true);
			}

			$T->setVar('lista_pasos', '');
			foreach ($pasos as $paso) {
				$T->setVar('__paso_estilo', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
				$T->setVar('__paso_nombre', $paso->getAttribute("nombre"));

				$T->setVar('lista_ponderacion', '');

				$paso_total = 0;
				foreach ($ponderaciones as $ponderacion) {
					$dato = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@item_id=".$ponderacion->getAttribute("item_id")."]/detalles/detalle[@paso_orden=".$paso->getAttribute("paso_orden")."]/datos/dato")->item(0);
					$paso_total += ($dato == null)?0:($dato->getAttribute("tiempo_prom") / $ponderaciones->length);
					$fecha_promedio[$ponderacion->getAttribute("item_id")] += ($dato == null)?0:($dato->getAttribute("tiempo_prom") / $pasos->length);
					$T->setVar('__paso_valor', number_format(($dato == null)?"0":$dato->getAttribute("tiempo_prom"), 3, ',', ''));
					$T->parse('lista_ponderacion', 'LISTA_PONDERACION', true);
				}

				$T->setVar('__paso_total', number_format($paso_total, 3, ',', ''));
				$T->parse('lista_pasos', 'LISTA_PASOS', true);
				$linea++;
			}

			$T->setVar('__promedio_estilo', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
			$T->setVar('lista_ponderacion_promedio', '');
			$fecha_total = 0;
			foreach ($fecha_promedio as $valor) {
				$fecha_total += $valor / $ponderaciones->length;
				$T->setVar('__fecha_valor', number_format($valor, 3, ',', ''));
				$T->parse('lista_ponderacion_promedio', 'LISTA_PONDERACION_PROMEDIO', true);
			}

			$T->setVar('__fecha_total', number_format($fecha_total, 3, ',', ''));
			$T->parse('lista_fechas', 'LISTA_FECHAS', true);

			$T->setVar('__resumen_estilo', ($i % 2 == 0)?"celdaIteracion1":"celdaIteracion2");
			$T->setVar('resumen_ponderacion', '');
			$fecha_total = 0;
			foreach ($fecha_promedio as $item_id => $valor) {
				$fecha_total += $valor / $ponderaciones->length;
				$ponderacion_promedio[$item_id] += $valor / count($fechas);
				$T->setVar('__fecha_valor', number_format($valor, 3, ',', ''));
				$T->parse('resumen_ponderacion', 'RESUMEN_PONDERACION', true);
			}

			$T->setVar('__fecha_total', number_format($fecha_total, 3, ',', ''));
			$T->parse('resumen_fechas', 'RESUMEN_FECHAS', true);
		}

		$T->setVar('__promedio_estilo', (($i + 1) % 2 == 0)?"celdaIteracion1":"celdaIteracion2");
		$T->setVar('resumen_ponderacion_promedio', '');
		$ponderacion_total = 0;
		foreach ($ponderacion_promedio as $valor) {
			$ponderacion_total += $valor / $ponderaciones->length;
			$T->setVar('__ponderacion_valor', number_format($valor, 3, ',', ''));
			$T->parse('resumen_ponderacion_promedio', 'RESUMEN_PONDERACION_PROMEDIO', true);
		}
		$T->setVar('__ponderacion_total', number_format($ponderacion_total, 3, ',', ''));
	
//		$this->tiempo_expiracion = $parser->tiempo_expiracion;
		$this->resultado =$T->parse('out', 'tpl_tabla');
	}*/
	/*
	Creado por: Aldo Cruz Romero
	Fecha de creacion:11-10-2018
	Fecha de ultima modificacion:
	*/
	function geEspecialConsolidadoHora() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$sql = "SELECT * FROM reporte.disponibilidad_resumen_global_ponderado_poritem(".
  				pg_escape_string($current_usuario_id).",".
  				pg_escape_string($this->objetivo_id).",5,' ".
  				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
  				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
  		//print $sql;
  		$res = & $mdb2->query($sql);
  		if (MDB2::isError($res)) {
 			$log->setError($sql, $res->userinfo);
 			exit();
 		}
 		if($row = $res->fetchRow()){
 			$dom = new DomDocument();
 			$dom->preserveWhiteSpace = FALSE;
  			$dom->loadXML($row['disponibilidad_resumen_global_ponderado_poritem']);
  			$xpath = new DOMXpath($dom);
  			unset($row["disponibilidad_resumen_global_ponderado_poritem"]);
		}
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
  		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
  		$conf_ponderaciones = $xpath->query("/atentus/resultados/propiedades/ponderaciones/item");
  		$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");

		//SI NO HAY DATOS MOSTRAR MENSAJE
    	if (!$conf_pasos->length or $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length == 0) {
  			$this->resultado = $this->__generarContenedorSinDatos();
  			return;
  		}
  		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
  		$T->setFile('tpl_tabla', 'consolidado_hora.tpl');
  		$T->setBlock('tpl_tabla', 'BLOQUE_HORARIO', 'bloque_horario');
  		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
  		
  		$T->setVar('bloque_pasos', '');
  		foreach ($conf_pasos as $conf_paso) {
  			$tag_paso = $xpath->query("//detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]")->item(0);
 			$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
			$array_porcentaje = array();
			$cont_iterador=0;
  			$T->setVar('bloque_horario', '');
  			foreach ($conf_ponderaciones as $conf_ponderacion) {
  				$cont_iterador++;
  				$T->setVar('__print_class', ($cont_iterador % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
  				$T->setVar('__class', ($cont_iterador % 2 == 0)?"celdanegra15":"celdanegra10");
  				$T->setVar('__inicio', $conf_ponderacion->getAttribute('inicio'));
  				$T->setVar('__termino', $conf_ponderacion->getAttribute('termino'));
  				$arrayeventos=Array();
  				foreach ($conf_eventos as $conf_evento) {
  					$tag_dato_item = $xpath->query("detalles/detalle[@item_id=".$conf_ponderacion->getAttribute('item_id')."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]", $tag_paso)->item(0);
 					if ($tag_dato_item == null) {
 						$porcentaje = 0;
  					}else {
  						$porcentaje = $tag_dato_item->getAttribute("porcentaje");
  					}
  					$arrayeventos[$conf_evento->getAttribute("evento_id")]=$porcentaje;
				}
				$noMOn=$arrayeventos[7];
				$marcado=$arrayeventos[9];
				if(($marcado+$noMOn)!=100){
					$uptime=$arrayeventos[1];
					$downtime=$arrayeventos[2];
					$downtimep=$arrayeventos[3];
					$uptime=$uptime+$downtimep;
					$total=$uptime+$downtime;
					$uptimeReal=$uptime*100/$total;
					$downtime_real=$downtime*100/$total;
				}else{
					$uptimeReal=0;
					$downtime_real=0;
				}
				$T->setVar('__uptime', number_format(round($uptimeReal, 2), 2));
				$T->setVar('__downtime', number_format(round($downtime_real, 2), 2));
				$T->parse('bloque_horario', 'BLOQUE_HORARIO', true);
  			}
  			$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
  		}
 		$this->resultado = $T->parse('out', 'tpl_tabla');
	}
	/*
	Creado por: Aldo Cruz Romero
	Fecha de creacion:21-09-2018
	Fecha de ultima modificacion:
	*/
	function getEspecialHacienda() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $meses_anno;

		$objetivo = new ConfigEspecial($this->objetivo_id);
		$titulo=$objetivo->__reporte['Titulo']->titulo;
		$footer_text=$objetivo->__reporte['Footer'];
		//SETEO DE FECHA DE REPORTE
		$fecha_titulo_inicio=substr($this->timestamp->getInicioPeriodo(), 0,10);
		$fecha_titulo_termino=substr($this->timestamp->getTerminoPeriodo(), 0, 10);
		$fecha_titulo_termino = strtotime($fecha_titulo_termino);
		$fecha_titulo_termino = strtotime('-1 day', $fecha_titulo_termino);
		$fecha_titulo_termino = date('Y-m-d', $fecha_titulo_termino);

		$date=explode('-', $fecha_titulo_inicio);
		$dateTermino=explode('-', $fecha_titulo_termino);
		
		foreach ($meses_anno as $key_mes => $value_mes) {
			if(intval($date[1])==$key_mes){
				$mes= $value_mes;
			}
			if(intval($dateTermino[1])==$key_mes){
				$mes_termino=$value_mes;
			}
		}
		$date=$date[2].' de '.$mes;
		$dateTermino=$dateTermino[2].' de '.$mes_termino.' del '.$dateTermino[0];
		$date= 'Periodo del '.$date.' al	'.$dateTermino;

		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_contenido', 'especial_hacienda.tpl');
		
		$T->setBlock('tpl_contenido', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_contenido', 'BLOQUE_NODOS_NOMBRE', 'bloque_nodos_nombre');
		if($objetivo->tipo_reporte!=1){
			$T->setBlock('tpl_contenido', 'BLOQUE_DESCRIPCIONES', 'bloque_descripciones');
			$T->setBlock('tpl_contenido', 'BLOQUE_CONSOLIDADO', 'bloque_consolidado');
		}
		$T->setBlock('tpl_contenido', 'BLOQUE_NODOS', 'bloque_nodos');
		$T->setBlock('tpl_contenido', 'BLOQUE_OBJETIVO', 'bloque_objetivo');
		

		//SETEO DE FECHA DE REPORTE
		$T->setVar('__fecha',$date);
		$T->setVar('__footer_text',$footer_text);

		//SETEO DE TITULO DE REPORTE
		$T->setVar('__titulo_principal',$titulo);

		$res = '';
		if($objetivo->tipo_reporte!=1){
			$T->setVar('bloque_descripciones', '');
			foreach ($objetivo->__reporte['Consolidado'] as $key => $descripcion) {
				$descripciones='<tr><td  class="txtNegro13" style="font-weight: bold;" colspan="3">'.'('.$key.')'.$descripcion.'</td></tr>';
				if($key==0){
					$T->setVar('__resumen',$descripcion);
					$res = $descripcion;
				}else{
					$T->setVar('__descripciones',$descripciones);
					$T->parse('bloque_descripciones', 'BLOQUE_DESCRIPCIONES', true);
				}
			}
		}
		$T->setVar('bloque_objetivo', '');
		if($objetivo->tipo_reporte!=1){
			$T->setVar('bloque_consolidado', '');
			$tabla_consolidado='<div class="pagebreak">
		<table width="100%">
			<tr>
				<div style="font-family: Calibri;font-size:x-large; text-align: left; font-weight: bold;">Resumen</div>
				<hr color="black" size=3>
				<div style="font-family: Calibri; text-align: left; font-weight: bold;">'.$date.'</div>
			</tr>
		</table>
		<br><br>
		<table width="100%">
			<tr>
				<td colspan="3" class="txtNegro13" style="text-align: left;font-weight: bold; ">'.$res.'</td>
			</tr>
			<tr style="text-align: center;" >
				<td class="txtBlanco13b celdaTituloGris">Nº</td>
				<td class="txtBlanco13b celdaTituloGris">Objetivos</td>
				<td class="txtBlanco12b celdaTituloNaranjo">Disponibilidad</td>
			</tr>';
			$T->setVar('__tabla_consolidado',$tabla_consolidado);
		}
		$cont_obj=1;
		$array_uptime=Array();
		$nombre_nodos=Array();
		$uptime_max = Array();
		
		//SE RECORRE DATA POR OBJETIVO
		foreach ($objetivo->getSubobjetivos() as $objetivo_key => $subobjetivo) {
			$keys_pasos=(array_keys($subobjetivo->__pasos));
			$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($objetivo_key).", ".
				pg_escape_string($this->horario_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."', ".
				(isset($this->extra["variable"])?$usr->cliente_id:'0').")";
			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_resumen_consolidado']);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_resumen_consolidado"]);
			}
			$conf_nodos=$xpath->query("/atentus/resultados/propiedades/nodos/nodo");
			$conf_obj= $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=0]/detalles/detalle/estadisticas/estadistica");
			$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
			$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
			$TotalUptimes=Array();
			$objetivo_nombre=($conf_objetivo->getAttribute("nombre"));
			
			//SE CREA BLOQUE DE NODOS
			$T->setVar('bloque_nodos_nombre', '');
			foreach ($conf_nodos as $key => $value_nodo) {
				if($value_nodo->getAttribute("nombre")!='Global'){
					array_push($nombre_nodos,$value_nodo->getAttribute("nombre"));
				}
				if($value_nodo->getAttribute("nodo_id")!=0){
					$nombre_nodo=$value_nodo->getAttribute("nombre");
					$nodo_id=($value_nodo->getAttribute("nodo_id"));
					$tag_nodo = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=".$nodo_id."]")->item(0);
					$T->setVar('bloque_data', '');
					$T->setVar('bloque_pasos', '');
					if($key%2==0){
						$class_nodo='class="txtGris12 celdaIteracion2"';
					}else{
						$class_nodo='class="txtGris12 celdaIteracion1"';
					}
					//ARRAY QUE ACUMULA UPTIMES
					$Uptimes=Array();
					//CONTADOR DE PASOS PARA REALIZAR ROWSPAN DE NODOS
					$cont_pasos=0;
					foreach ($conf_pasos as $key_pasos => $value_pasos) {
						$paso_nombre=$value_pasos->getAttribute("nombre");
						$paso=$value_pasos->getAttribute("paso_orden");
						$paso_descripcion =$subobjetivo->__pasos[$paso]->descripcion;
						if($paso_descripcion == ''){
							$paso_descripcion = $paso_nombre;
						}
						if($key_pasos%2==0){
							$class='class="txtGris12 celdaIteracion2"';
						}else{
							$class='class="txtGris12 celdaIteracion1"';
						}
						//SE VALIDA SI EN XML ATRIBUTO PASOS
						if(strval($keys_pasos)=='Array'){
							//SE VALIDA SI LOS PASOS VISIBLES ESTAN DENTRO DE LOS PASOS DEL XML
							if(in_array($paso, $keys_pasos)){
								$dato = $xpath->query("detalles/detalle[@paso_orden=".$paso."]/estadisticas/estadistica", $tag_nodo);
								$acumulado=Array();
								foreach ($dato as $key_dato => $value) {
									$evento=($value->getAttribute("evento_id"));
									$acumulado[$evento]=$value->getAttribute("porcentaje");
									$uptime=$acumulado[1];
									$no_monitoreo=$acumulado[7];
									$downtime=$acumulado[2];
									$marcado=$acumulado[9];
								}
								$empty='';
								if($uptime!=$empty){
									array_push($Uptimes, $uptime);
								}
								if($downtime==$empty){
									$downtime=0;
								}
								if($uptime==$empty){
									$uptime=0;
								}
								if($no_monitoreo==$empty){
									$no_monitoreo=0;
								}
								if($marcado==$empty){
									$marcado=0;
								}
								$cont_pasos++;
								$T->setVar('__data','<td width="80" '.$class.' style="text-align:right;">'.$uptime.' %</td><td '.$class.' style="text-align:right;">'.$no_monitoreo.' %</td><td '.$class.'style="text-align:right;">'.$downtime.' %</td><td '.$class.' style="text-align:right;">'.$marcado.' %</td></tr>');
								$T->setVar('__paso_nombre','<td width="350" '.$class.'>'.$paso_descripcion.'</td>');
								$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
							}
						}else{
							$dato = $xpath->query("detalles/detalle[@paso_orden=".$paso."]/estadisticas/estadistica", $tag_nodo);
							$acumulado=Array();
							foreach ($dato as $key_dato => $value) {
								$evento=($value->getAttribute("evento_id"));
								$acumulado[$evento]=$value->getAttribute("porcentaje");
								$uptime=$acumulado[1];
								$no_monitoreo=$acumulado[7];
								$downtime=$acumulado[2];
								$marcado=$acumulado[9];
							}
							$empty='';
							if($uptime!=$empty){
								array_push($Uptimes, $uptime);
							}
							if($downtime==$empty){
								$downtime=0;
							}
							if($uptime==$empty){
								$uptime=0;
							}
							if($no_monitoreo==$empty){
								$no_monitoreo=0;
							}
							if($marcado==$empty){
								$marcado=0;
							}
							$cont_pasos++;
							$T->setVar('__data','<td width="80" '.$class.' style="text-align:right;">'.$uptime.' %</td><td '.$class.' style="text-align:right;">'.$no_monitoreo.' %</td><td '.$class.'style="text-align:right;">'.$downtime.' %</td><td '.$class.' style="text-align:right;">'.$marcado.' %</td></tr>');
							$T->setVar('__paso_nombre','<td width="350" '.$class.'>'.$paso_descripcion.'</td>');
							$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
						}
					}
					array_push($TotalUptimes,$Uptimes);
					$key_pasos= ($cont_pasos);
					$T->setVar('__nodo_nombre','<td width=120 '.$class_nodo.' rowspan="'.$key_pasos.'">'.$nombre_nodo.'</td>');
					$T->parse('bloque_nodos_nombre', 'BLOQUE_NODOS_NOMBRE', true);
				}
			}
			$new_key= $key_pasos*$key;
			if($cont_obj%2==0){
				$clase='class="txtGris12 celdaIteracion2"';
			}else{
				$clase='class="txtGris12 celdaIteracion1"';
			}

			$nombre_objetivo='<tr><td width="220" '.$clase.' rowspan="'.$new_key.'" width="30%">'.$objetivo_nombre.'</td>';
			
			//SETEA VARIABLES EN PRIMERA TABLA
			$UptimeValid=Array();
			$tamaño=Array();
			$maximo = Array();
			foreach ($TotalUptimes as $keyuptime => $uptime){
				if(sizeof($uptime)>0){
					array_push($tamaño, sizeof($uptime));
					array_push($UptimeValid, array_sum($uptime));
				}
				if(sizeof($uptime)>0){
					$maxUpByNode = max($uptime);
					array_push($maximo, $maxUpByNode);
				}else{
					array_push($maximo, 0);
				}
			}
			$tamaño=array_sum($tamaño);
			$UptimeValid=array_sum($UptimeValid);
			$uptimeObj= number_format(round($UptimeValid/$tamaño, 2), 2);
			array_push($array_uptime, $uptimeObj);
			array_push($uptime_max, max($maximo));
			if($cont_obj%2==0){
				$class='class="txtGris12 celdaIteracion2"';
			}else{
				$class='class="txtGris12 celdaIteracion1"';
			}
			$tr_objetivo='<tr ><td  '.$class.' >'.$cont_obj++.'</td><td  '.$class.' >'.$subobjetivo->nombre.'</td><td '.$class.' style="text-align:right;">'.max($maximo).' %(1)</td></tr>';

			$T->setVar('__objetivo',$nombre_objetivo);
			
			$T->parse('bloque_objetivo', 'BLOQUE_OBJETIVO', true);
			if($objetivo->tipo_reporte!=1){
				$T->setVar('__tr_objetivo',$tr_objetivo);
				$T->parse('bloque_consolidado', 'BLOQUE_CONSOLIDADO', true);
			}
		}
		$count_uptime=count($uptime_max);
		if($count_uptime>0){
			$prom_uptime=number_format(round(array_sum($uptime_max)/$count_uptime, 2), 2);
		}else{
			$prom_uptime=0;
		}
		//SETEA PROMEDIO GENERAL
		if($cont_obj%2==0){
				$class='class="txtGris12 celdaIteracion2"';
			}else{
				$class='class="txtGris12 celdaIteracion1"';
			}
		if($objetivo->tipo_reporte==2){
			$T->setVar('__promedio','<tr"><td '.$class.' style="text-align:center;"colspan="2"> Promedio </td><td '.$class.' style="text-align:right;">'.$prom_uptime.' %</td></tr>');
		}
		//ESTABLECE FILA Y COLUMNAS DE TABLA DE NODOS
		
		$T->setVar('__titulo_presentacion','<tr><td  class="txtNegro13" style="text-align: left;font-weight: bold; ">'.$objetivo->__reporte['Presentacion'][0]->texto.'.</td></tr>');
		$T->setVar('__segunda_presentacion','<tr><td  class="txtNegro13" style="text-align: left;font-weight: bold; ">'.$objetivo->__reporte['Presentacion'][1]->texto.':</td></tr>');


		$nombre_nodos=array_unique($nombre_nodos);
		$T->setVar('bloque_nodos', '');
		foreach ($nombre_nodos as $key => $nodo) {
			$T->setVar('__nodo','<tr ><td style="text-align:left;"><li>'.$nodo.' </li></td></tr>');
			$T->parse('bloque_nodos', 'BLOQUE_NODOS', true);
		}
		
		$this->resultado = $T->parse('out', 'tpl_contenido');
	}

	/*Creado por: Santiago Sepulveda
	Modificado por:
	Fecha de creacion: 01-07-2019
	Fecha de ultima modificacion:
	*/
	function getEspecialAfpHabitat(){
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$objetivo = new ConfigEspecial($this->objetivo_id);
		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_contenido', 'especial_afp_habitat.tpl');
		$T->setBlock('tpl_contenido', 'BLOQUE_DATOS', 'bloque_datos');
		$T->setBlock('tpl_contenido', 'BLOQUE_NODO', 'bloque_nodo');
		$T->setBlock('tpl_contenido', 'BLOQUE_OBJETIVO', 'bloque_objetivo');
		$T->setBlock('tpl_contenido', 'BLOQUE_OBJETIVOS', 'bloque_objetivos');

		$T->setBlock('tpl_contenido', 'HORARIO', 'horario');
		$horario = $objetivo->getHorario($this->horario_id);
		$nombre_horario = $horario->nombre;
		$T->setVar('__nombre_horario',$nombre_horario);
		$T->parse('horario', 'HORARIO', true);

		$T->setVar('bloque_objetivos', '');
		$valida_fila = 0;
		foreach ($objetivo->getSubobjetivos() as $subobjetivo_id => $subobjetivo) {
			if ($valida_fila == 0) {
				$valida = true;
			}else{
				$valida = false;
			}
			$T->setVar('__disp_res_objs', $this->getConsolidadoPorcentajeObjetivoEspecial($subobjetivo_id, $valida, $valida_fila, $multi_obj = true));
			$T->parse('bloque_objetivos', 'BLOQUE_OBJETIVOS', true);
			$valida_fila++;
		}

		$contador_class_objetivo = 1;
		$T->setVar('bloque_objetivo', '');
		foreach ($objetivo->getSubobjetivos() as $subobjetivo_id => $subobjetivo) {
			$nombre_subobjetivo = $subobjetivo->nombre;
			$T->setVar('__nombre_objetivo', $nombre_subobjetivo);

			$T->setVar('__fecha_inicio', $this->timestamp->getInicioPeriodo("d/m/Y H:i:s"));
			$mes = $this->timestamp->getInicioPeriodo("F");
			$meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
			$meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
			$nombreMes = str_replace($meses_EN, $meses_ES, $mes);
			$T->setVar('__mes', $nombreMes);
			$T->setVar('__fecha_termino', date("d/m/Y 24:00:00", (strtotime($this->timestamp->getTerminoPeriodo("Y-m-d H:i:s")) - 1)));

			$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($subobjetivo_id).", ".
				pg_escape_string($this->horario_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."', ".
				(isset($this->extra["variable"])?$usr->cliente_id:'0').")";

			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_resumen_consolidado']);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_resumen_consolidado"]);
			}

			$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo");
			$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
			$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
			$cant_nodos = ($xpath->query("/atentus/resultados/propiedades/nodos/nodo")->length)-1;
			$cant_pasos = ($xpath->query("paso[@visible=1]", $conf_objetivo)->length);

			$rowspan_objetivo = ($cant_nodos * $cant_pasos);
			$T->setVar('__rowspan_obj', $rowspan_objetivo);

			$contador_class_nodo = 1;
			$cuenta_datos = 0;
			$T->setVar('bloque_nodo', '');
			foreach ($conf_nodos as $conf_nodo) {
				$nodo_id = $conf_nodo->getAttribute("nodo_id");
				if ( $conf_nodo->getAttribute("nodo_id") == "0") {
					continue;
				}
				if($contador_class_nodo%2 == 0){
					$T->setVar('__class_nodo', 'celdaIteracion2');
				}else{
					$T->setVar('__class_nodo', 'celdaIteracion1');
				}
				$T->setVar('__rowspan_nodo', $cant_pasos);
				$T->setVar('__nombre_nodo', $conf_nodo->getAttribute("nombre"));

				$uptime = '';
				$downtime = '';
				$no_monitoreo = '';
				$mantenimiento = '';
				$contador_class_paso = 1;
				$T->setVar('bloque_datos', '');
				foreach ($conf_pasos as $paso) {
					$paso_orden = $paso->getAttribute('paso_orden');
					$T->setVar('__nombre_paso', $paso->getAttribute('nombre'));

					if($contador_class_paso%2 == 0){
						$T->setVar('__class_paso', 'celdaIteracion2');
						$T->setVar('__color_uptime_obj', '55a51c');
						$T->setVar('__color_downtime_obj', 'd3222a');
						$T->setVar('__color_no_mon_obj', '909090');
					}else{
						$T->setVar('__class_paso', 'celdaIteracion1');
						$T->setVar('__color_uptime_obj', '71c137');
						$T->setVar('__color_downtime_obj', 'e04f56');
						$T->setVar('__color_no_mon_obj', 'b2b2b2');
					}

					foreach ($conf_eventos as $eventos) {
						$evento_id = $eventos->getAttribute('evento_id');
						$conf_estadistica = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=".$nodo_id."]/detalles/detalle[@paso_orden=".$paso_orden."]/estadisticas/estadistica[@evento_id=".$evento_id."]");						
						if ($conf_estadistica->length == 0) {
							if ($evento_id == 2) {
								$downtime = '0.00';
							}
							if ($evento_id == 1) {
								$uptime = '0.00';
							}
							if ($evento_id == 7) {
								$no_monitoreo = '0.00';
							}
							if ($evento_id == 3) {
								$parcial = '0.00';
							}
							if ($evento_id == 9) {
								$mantenimiento = '0.00';
							}
						}else{
							$conf_estadistica_dato = $conf_estadistica->item(0);
							$porcentaje = $conf_estadistica_dato->getAttribute('porcentaje');
							if ($evento_id == 2) {
								$downtime = $porcentaje;						
							}
							if ($evento_id == 1) {
								$uptime = $porcentaje;						
							}
							if ($evento_id == 7) {
								$no_monitoreo =  $porcentaje;
							}
							if ($evento_id == 3) {
								$parcial = $porcentaje;
							}
							if ($evento_id == 9) {
								$mantenimiento = $porcentaje;
							}
						}
					}

					$uptime_par = $uptime + $parcial;
					$factor_total=$uptime_par + $downtime + $no_monitoreo;
					$uptime_real = ($uptime_par * 100) / $factor_total;
					$downtime_real = ($downtime * 100) / $factor_total;
					$no_monitoreo_real = ($no_monitoreo * 100) / $factor_total;

					$T->setVar('__downtime_porcentaje', number_format($downtime_real ,2));
					$T->setVar('__uptime_porcentaje', number_format($uptime_real ,2));
					$T->setVar('__no_monitoreo_porcentaje', number_format($no_monitoreo_real ,2));

					$contador_class_paso++;
					$cuenta_datos++;
					$T->parse('bloque_datos', 'BLOQUE_DATOS', true);
				}
				$contador_class_nodo++;
				$T->parse('bloque_nodo', 'BLOQUE_NODO', true);
			}

			$T->setVar('__consolidado', $this->getConsolidadoPorcentajeObjetivoEspecial($subobjetivo_id, $valida=true, $valida_fila, $multi_obj = false));
			$T->setVar('__tiempos', $this->getDisponibilidadDowntimeEspecial($subobjetivo_id));
			$T->setVar('__disp_res', $this->getTablaDisponibilidadDetalladaEspecial($subobjetivo_id));
			$contador_class_objetivo++;

			$T->parse('bloque_objetivo', 'BLOQUE_OBJETIVO', true);
		}		
		$this->resultado = $T->parse('out', 'tpl_contenido');
	}

	function getConsolidadoPorcentajeObjetivoEspecial($subobjetivo_id, $valida, $class_obj, $multi_obj) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$T =& new Template_PHPLIB( $this->extra["imprimir"]?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_consolidado_objetivo.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_OBJETIVO', 'bloque_objetivo');

		$sql_consolidado = "SELECT * FROM reporte.disponibilidad_detalle_semaforo_especial(".
		pg_escape_string($current_usuario_id).",".
		pg_escape_string($subobjetivo_id).", ".
		pg_escape_string($this->horario_id).", '".
		pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
		pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

		$T->setVar('__color_uptime', ($class_obj % 2 == 0)?"55a51c":"71c137");
		$T->setVar('__color_downtime', ($class_obj % 2 == 0)?"d3222a":"e04f56");
		$T->setVar('__color_no_mon', ($class_obj % 2 == 0)?"909090":"b2b2b2");
		$T->setVar('__color_obj', ($class_obj % 2 == 0)?"ebebeb":"fff");
		$T->setVar('__style_obj', ($multi_obj == true)?"":"font-weight: bold; font-size: 19px;");

		if ($valida == true) {
			$fila = '<tr>'.
							'<td class="txtBlanco13b celdaTituloGris" align="left" style="background-color: #626262;">Objetivo</td>'.
							'<td class="txtBlanco12b celdaTituloNaranjo" style="border-right: solid 1px #ffffff;" width="15%" align="center">Uptime</td>'.
							'<td class="txtBlanco12b celdaTituloNaranjo" style="border-right: solid 1px #ffffff;" width="15%" align="center">Downtime</td>'.
							'<td class="txtBlanco12b celdaTituloNaranjo" style="border-right: solid 1px #ffffff;" width="15%" align="center">No Monitoreo</td>'.
						'</tr>';
		}else{
			$fila = '';
		}
		$T->setVar('__fila', $fila);

		$res_consolidado = & $mdb2->query($sql_consolidado);
		if (MDB2::isError($res_consolidado)) {
			$log->setError($sql_consolidado, $res_consolidado->userinfo);
			exit();
		}

		if ($row_consolidado = $res_consolidado->fetchRow()) {
			$dom_consolidado = new DomDocument();
			$dom_consolidado->preserveWhiteSpace = FALSE;
			$dom_consolidado->loadXML($row_consolidado['disponibilidad_detalle_semaforo_especial']);
			$xpath_consolidado = new DOMXpath($dom_consolidado);
			unset($row_consolidado["disponibilidad_detalle_semaforo_especial"]);
		}
		
		$objetivo = $xpath_consolidado->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_estadisticas = $xpath_consolidado->query("/atentus/resultados/detalles/detalle/estadisticas/estadistica");

		foreach ($conf_estadisticas as $estadistica){
			if ($estadistica->getAttribute('evento_id') == 1) {
				$uptime_con_obj = $estadistica->getAttribute('porcentaje');
			}
			if ($estadistica->getAttribute('evento_id') == 2) {
				$downtime_con_obj = $estadistica->getAttribute('porcentaje');
			}
			if ($estadistica->getAttribute('evento_id') == 3) {
				$parcial_con_obj = $estadistica->getAttribute('porcentaje');
			}
			if ($estadistica->getAttribute('evento_id') == 7) {
				$no_mon_con_obj = $estadistica->getAttribute('porcentaje');
			}
		}

		$uptime_con_obj_real = ($uptime_con_obj + $parcial_con_obj);
		$factor = $uptime_con_obj_real + $downtime_con_obj + $no_mon_con_obj;
		$uptime_real = ($uptime_con_obj_real * 100) / $factor;
		$downtime_real = ($downtime_con_obj * 100) / $factor;
		$no_mon_real = ($no_mon_con_obj * 100) / $factor;
		$T->setVar('__uptime', number_format($uptime_real, 2));
		$T->setVar('__downtime', number_format($downtime_real, 2));
		$T->setVar('__no_monitoreo', number_format($no_mon_real, 2));
		$T->setVar('__nombre_obj', $objetivo->getAttribute('nombre'));

		$T->parse('bloque_objetivo', 'BLOQUE_OBJETIVO', true);
		return $T->parse('out', 'tpl_tabla');
	}

	/*Creado por:Aldo Cruz Romero
	Modificado por:
	Fecha de creacion:31-06-2018
	Fecha de ultima modificacion:
	*/
	function getEspecialDisponibilidadPodJudicial() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $meses_anno;

		$objetivo = new ConfigEspecial($this->objetivo_id);
		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_contenido', 'especial_disponibilidad_pod_jud.tpl');
		$T->setBlock('tpl_contenido', 'BLOQUE_HORARIO_EVENTOS', 'bloque_horario_eventos');
		$T->setBlock('tpl_contenido', 'BLOQUE_ESTADOS_OBJETIVOS', 'bloque_estados_objetivos');
		$T->setBlock('tpl_contenido', 'BLOQUE_COLOR', 'bloque_color');
		$T->setBlock('tpl_contenido', 'BLOQUE_TD_OBJ', 'bloque_td_obj');
		$T->setBlock('tpl_contenido', 'BLOQUE_TR', 'bloque_tr');
		$mes;
		
		//CONSULTA QUE OBTIENE TAGD DEL XML CONFIGURACION
		$sql_config = "SELECT xml_configuracion FROM objetivo_config where es_ultima_config='t' and objetivo_id=".$this->objetivo_id;
			//echo $sql_config.'<br>';
			$res =& $mdb2->query($sql_config);
		if (MDB2::isError($res)) {
			$log->setError($sql_config, $res->userinfo);
		exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['xml_configuracion']);
			$xpath_config = new DOMXpath($dom);
			unset($row["xml_configuracion"]);
		}

		$conf_xml= $xpath_config->query("/atentus/config/especial");
		$conf_descripciones= $xpath_config->query("/atentus/config/especial/descripciones/leyendas/leyenda");
		$conf_texto= $xpath_config->query("/atentus/config/especial/descripciones/texto")->item(0);
		$conf_xml2 = $xpath_config->query("/atentus/config/especial")->item(0);
		$estados= $conf_xml2->getAttribute("tag_estado");

		$T->setVar('__objetivo_padre',$this->objetivo_id);
		//SETEO DE TAGS ESPECIALES PARA EL REPORTE
		foreach ($conf_xml as $conf_obj_xml) {
			$T->setVar('__titulo',$conf_obj_xml->getAttribute("nombre"));
			$T->setVar('__estados',$conf_obj_xml->getAttribute("tag_estado"));
		}
		$descripcion=$conf_descripciones->item(0);
		$descripcion_error=$conf_descripciones->item(1);
		$descripcion_ok= $descripcion->getAttribute("descripcion_ok");
		$descripcion_error=$descripcion_error->getAttribute("descripcion_error");
		$T->setVar('__leyenda',$descripcion_ok);
		$T->setVar('__leyenda_error',$descripcion_error);
		$T->setVar('__texto',$conf_texto->getAttribute("descripcion"));
		$validador;
		$cont=0;
		$array_obj=Array();
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			$cont++;
			$array_obj[$cont]=$subobjetivo->nombre;
		}
		$T->setVar('bloque_tr', '');
		$T->setVar('bloque_estados_objetivos', '');
		$cont_color=0;
		$array_color=Array();
		$newarray=Array();

				//CONSTRUCCION DE TABLAS SEGUN DATA DE CADA OBJETIVO
		
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			$usuario = Utiles::busca_usuario($subobjetivo->objetivo_id);
			//echo $subobjetivo->nombre.'<br>';
			
			$sql = "SELECT * FROM reporte.disponibilidad_downtime_global(".
			pg_escape_string($current_usuario_id).",".
			pg_escape_string($subobjetivo->objetivo_id).", ".
			pg_escape_string($this->horario_id).", '".
			pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
			pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			//echo $sql.'<br>';
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
			exit();
			}
			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_downtime_global']);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_downtime_global"]);
			}
			$conf_objetivo= $xpath->query("/atentus/resultados/detalles/detalle/datos/dato");
			$fecha_titulo=substr($this->timestamp->getInicioPeriodo(), 0,10);
			$date=explode('-', $fecha_titulo);

			//SETEO DE FECHA DE REPORTE
			
			foreach ($meses_anno as $key_mes => $value_mes) {
				if(intval($date[1])==$key_mes){
					$mes= $value_mes;
				}
			}
			$date=$date[2].' de '.$mes.' del '.$date[0];
			$T->setVar('__fecha',$date);
			
						//CONSTRUCCION DE ARRAY POR OBJETIVO CON EVENTO Y DATOS
			
			$array_dato=Array();
			foreach ($conf_objetivo as $objetivo) {
				$array_data=Array();
				$array_data["nombre"]=$subobjetivo->nombre;
				$array_data["inicio"]=$objetivo->getAttribute('inicio');
				$array_data["termino"]=$objetivo->getAttribute('termino');
				$array_data["duracion"]=$objetivo->getAttribute('duracion');
				$array_data["color"]=$objetivo->getAttribute('evento_id');
				array_push($array_dato, $array_data);
			}
			$arrayDta=Array();
			if(sizeof($array_dato)!=0){
				foreach ($array_dato as $valuearray) {
					if($valuearray["color"]=='2'){
						$array=Array();
						$array["nombre"]=$valuearray["nombre"];
						$array["inicio"]=$valuearray["inicio"];
						$array["termino"]=$valuearray["termino"];
						$array["duracion"]=$valuearray["duracion"];
						$array["color"]='d3222a';
						array_push($arrayDta,$array);
					}
				}
			}else{
				$array=Array();
				$array["nombre"]=$subobjetivo->nombre;
				$array["color"]='green';
				array_push($arrayDta, $array);
			}
			if(sizeof($arrayDta)>0){
				$newarrayDta=$arrayDta;
			}

						// CONSTRUCCION SEGUNDA TABLA
			
			$arraytabla=Array();
			if($subobjetivo->nombre==$newarrayDta[0]["nombre"]&&$newarrayDta[0]["color"]!='green'){
				//echo $subobjetivo->nombre.'<br>';
				$arraytabla["nombre"]=$subobjetivo->nombre;
				$arraytabla["color"]=$newarrayDta[0]["color"];
				$T->setVar('__nombre_objetivo', $subobjetivo->nombre);
				$T->setVar('bloque_horario_eventos', '');
				foreach ($newarrayDta as $key => $value) {
					$validador='true';
					$duracion=$value["duracion"];
					if($value["duracion"]=='1 day'){
						$duracion='1 Día';
					}
					$T->setVar('__inicio',$value["inicio"]);
					$T->setVar('__termino',$value["termino"]);
					$T->setVar('__duracion',$duracion);
					$T->parse('bloque_horario_eventos', 'BLOQUE_HORARIO_EVENTOS', true);
				}
				$T->parse('bloque_estados_objetivos', 'BLOQUE_ESTADOS_OBJETIVOS', true);
			}elseif($subobjetivo->nombre==$newarrayDta[0]["nombre"]&&$newarrayDta[0]["color"]='green'){
				$arraytabla["nombre"]=$subobjetivo->nombre;
				$arraytabla["color"]=$newarrayDta[0]["color"]; 
			}
			if(sizeof($arraytabla)>0){
				array_push($newarray, $arraytabla);
			}
		}

						//			Construccion de primera Tabla
		
		$array=array_chunk($newarray, 4);

		foreach ($array as $key => $value) {
			$T->setVar('__tr_obj','<table style="width:100%"><tr height="35px"><td style="font-family: Trebuchet MS, Verdana, sans-serif; font-size: 12px; padding: 1px 6px 1px 6px;border: solid 1px #626262"></td>');
			$T->setVar('bloque_td_obj', '');
			$T->setVar('bloque_color', '');
			foreach ($value as $key => $value1) {
				$T->setVar('__obj', '<td style="width:100px;font-family: Trebuchet MS, Verdana, sans-serif;font-size: 12px; padding: 1px 6px 1px 6px;border: solid 1px #626262;font-weight: 500;">'.$value1["nombre"].'</td>');
				$T->setVar('__color_obj','<td style="font-family: Trebuchet MS, Verdana, sans-serif;font-size: 12px; padding: 1px 6px 1px 6px;border: solid 1px #626262;color:'.$value1["color"].';text-align: center">&#11044;</td>');
				$T->parse('bloque_color', 'BLOQUE_COLOR', true);
				$T->parse('bloque_td_obj', 'BLOQUE_TD_OBJ', true);
			}
			$T->setVar('__medio_tr','<tr height="35px"><td style="font-family: Trebuchet MS, Verdana, sans-serif; font-size: 12px; padding: 1px 6px 1px 6px;width:70px;border: solid 1px #626262">'.$estados.'</td>');
			$T->setVar('__final_tr','</tr></table><br><br>');

			$T->parse('bloque_tr', 'BLOQUE_TR', true);
		}
		if($validador=='true'){
			$T->setVar('__text','<div style="font-weight: 500; ">Detalle de horarios en que no hubo conectividad:</div>');
		}else{
			$T->setVar('__text', '');
		}
		$this->resultado = $T->parse('out', 'tpl_contenido');
	}

	function getEspecialDisponibilidadContraloria() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $meses_anno;
		$objetivo = new ConfigEspecial($this->objetivo_id);
		$img_base64 = $objetivo->img_base64;		
		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_contenido', 'especial_contraloria.tpl');
		$T->setBlock('tpl_contenido', 'BLOQUE_HORARIO_EVENTOS', 'bloque_horario_eventos');
		$T->setBlock('tpl_contenido', 'BLOQUE_ESTADOS_OBJETIVOS', 'bloque_estados_objetivos');
		$T->setBlock('tpl_contenido', 'BLOQUE_COLOR', 'bloque_color');
		$T->setBlock('tpl_contenido', 'BLOQUE_TD_OBJ', 'bloque_td_obj');
		$T->setBlock('tpl_contenido', 'BLOQUE_TR', 'bloque_tr');
        $T->setVar('__img_base64',$img_base64);
		
		//CONSULTA QUE OBTIENE TAGD DEL XML CONFIGURACION
		$sql_config = "SELECT xml_configuracion FROM objetivo_config where es_ultima_config='t' and objetivo_id=".$this->objetivo_id;
			//echo $sql_config.'<br>';
			$res =& $mdb2->query($sql_config);
		if (MDB2::isError($res)) {
			$log->setError($sql_config, $res->userinfo);
		exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['xml_configuracion']);
			$xpath_config = new DOMXpath($dom);
			unset($row["xml_configuracion"]);
		}
		$conf_xml= $xpath_config->query("/atentus/config/especial");
		$conf_descripciones= $xpath_config->query("/atentus/config/especial/descripciones/leyendas/leyenda");
		$conf_texto= $xpath_config->query("/atentus/config/especial/descripciones/texto")->item(0);
		$conf_xml2 = $xpath_config->query("/atentus/config/especial")->item(0);
		$estados= $conf_xml2->getAttribute("tag_estado");
		$T->setVar('__objetivo_padre',$this->objetivo_id);
		//SETEO DE TAGS ESPECIALES PARA EL REPORTE
		foreach ($conf_xml as $conf_obj_xml) {
			$T->setVar('__titulo',$conf_obj_xml->getAttribute("nombre"));
			$T->setVar('__estados',$conf_obj_xml->getAttribute("tag_estado"));
		}
		$descripcion=$conf_descripciones->item(0);
		$descripcion_error=$conf_descripciones->item(1);
		$descripcion_ok= $descripcion->getAttribute("descripcion_ok");
		$descripcion_error=$descripcion_error->getAttribute("descripcion_error");
		$T->setVar('__leyenda',$descripcion_ok);
		$T->setVar('__leyenda_error',$descripcion_error);
		$T->setVar('__texto',$conf_texto->getAttribute("descripcion"));
		$validador;
		$cont=0;
		$array_obj=Array();
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			$cont++;
			$array_obj[$cont]=$subobjetivo->nombre;
		}
		$T->setVar('bloque_tr', '');
		$T->setVar('bloque_estados_objetivos', '');
		$cont_color=0;
		$array_color=Array();
		$newarray=Array();
		//Obtiene nombre de horario
		$horario = $objetivo->getHorario($this->horario_id);
		$nombre_horario = $horario->nombre;
		$T->setVar('__nombre_horario',$nombre_horario);
		//CONSTRUCCION DE TABLAS SEGUN DATA DE CADA OBJETIVO		
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			$usuario = Utiles::busca_usuario($subobjetivo->objetivo_id);
			$sql = "SELECT * FROM reporte.disponibilidad_downtime_global(".
			pg_escape_string($current_usuario_id).",".
			pg_escape_string($subobjetivo->objetivo_id).", ".
			pg_escape_string($this->horario_id).", '".
			pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
			pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
			exit();
			}
			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_downtime_global']);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_downtime_global"]);
			}
			$conf_objetivo= $xpath->query("/atentus/resultados/detalles/detalle/datos/dato");
			$fecha_titulo=substr($this->timestamp->getInicioPeriodo(), 0,10);
			$date=explode('-', $fecha_titulo);
			//SETEO DE FECHA DE REPORTE			
			foreach ($meses_anno as $key_mes => $value_mes) {
				if(intval($date[1])==$key_mes){
					$mes= $value_mes;
				}
			}
			$date=$date[2].' de '.$mes.' del '.$date[0];
			$T->setVar('__fecha',$date);
			//CONSTRUCCION DE ARRAY POR OBJETIVO CON EVENTO Y DATOS			
			$array_dato=Array();
			foreach ($conf_objetivo as $objetivo) {
				$array_data=Array();
				$array_data["nombre"]=$subobjetivo->nombre;
				$array_data["inicio"]=$objetivo->getAttribute('inicio');
				$array_data["termino"]=$objetivo->getAttribute('termino');
				$array_data["duracion"]=$objetivo->getAttribute('duracion');
				$array_data["color"]=$objetivo->getAttribute('evento_id');
				array_push($array_dato, $array_data);
			}
			$arrayDta=Array();
			if(sizeof($array_dato)!=0){
				foreach ($array_dato as $valuearray) {
					if($valuearray["color"]=='2'){
						$array=Array();
						$array["nombre"]=$valuearray["nombre"];
						$array["inicio"]=$valuearray["inicio"];
						$array["termino"]=$valuearray["termino"];
						$array["duracion"]=$valuearray["duracion"];
						$array["color"]='d3222a';
						array_push($arrayDta,$array);
					}
				}
			}else{
				$array=Array();
				$array["nombre"]=$subobjetivo->nombre;
				$array["color"]='green';
				array_push($arrayDta, $array);
			}
			if(sizeof($arrayDta)>0){
				$newarrayDta=$arrayDta;
			}
			// CONSTRUCCION SEGUNDA TABLA
			$arraytabla=Array();
			if($subobjetivo->nombre==$newarrayDta[0]["nombre"]&&$newarrayDta[0]["color"]!='green'){
				$arraytabla["nombre"]=$subobjetivo->nombre;
				$arraytabla["color"]=$newarrayDta[0]["color"];
				$T->setVar('__nombre_objetivo', $subobjetivo->nombre);
				$T->setVar('bloque_horario_eventos', '');
				$contador_class_paso;
				$contador_class_nodo;
				foreach ($newarrayDta as $key => $value) {
					$validador='true';
					$duracion=$value["duracion"];
					if($value["duracion"]=='1 day'){
						$duracion='1 Día';
					}
					$T->setVar('__inicio',$value["inicio"]);
					$T->setVar('__termino',$value["termino"]);
					$T->setVar('__duracion',$duracion);
					$T->parse('bloque_horario_eventos', 'BLOQUE_HORARIO_EVENTOS', true);
					if($contador_class_nodo%2 == 0){
						$T->setVar('__class_nodo', 'celdaIteracion2');
					}else{
						$T->setVar('__class_nodo', 'celdaIteracion1');
					}
					if($contador_class_paso%2 == 0 ){
						$T->setVar('__class_paso', 'celdaIteracion2');
						$T->setVar('__color_no_mon_obj', '909090');
					}else{
						$T->setVar('__class_paso', 'celdaIteracion1');
						$T->setVar('__color_no_mon_obj', 'b2b2b2');
					}
					$contador_class_nodo++;
					$contador_class_paso++;
				}
				$T->parse('bloque_estados_objetivos', 'BLOQUE_ESTADOS_OBJETIVOS', true);
			}elseif($subobjetivo->nombre==$newarrayDta[0]["nombre"]&&$newarrayDta[0]["color"]='green'){
				$arraytabla["nombre"]=$subobjetivo->nombre;
				$arraytabla["color"]=$newarrayDta[0]["color"]; 
			}
			if(sizeof($arraytabla)>0){
				array_push($newarray, $arraytabla);
			}
		}
		//			Construccion de primera Tabla		
		$array=array_chunk($newarray, 4);
		foreach ($array as $key => $value) {
			$T->setVar('__tr_obj','<table style="width:100%"><tr height="35px"><td style="font-family: Trebuchet MS, Verdana, sans-serif; font-size: 12px; padding: 1px 6px 1px 6px;border: solid 1px #626262"></td>');
			$T->setVar('bloque_td_obj', '');
			$T->setVar('bloque_color', '');
			foreach ($value as $key => $value1) {
				$T->setVar('__obj', '<td style="width:100px;font-family: Trebuchet MS, Verdana, sans-serif;font-size: 12px; padding: 1px 6px 1px 6px;border: solid 1px #626262;font-weight: 500;">'.$value1["nombre"].'</td>');
				$T->setVar('__color_obj','<td style="font-family: Trebuchet MS, Verdana, sans-serif;font-size: 12px; padding: 1px 6px 1px 6px;border: solid 1px #626262;color:'.$value1["color"].';text-align: center">&#11044;</td>');
				$T->parse('bloque_color', 'BLOQUE_COLOR', true);
				$T->parse('bloque_td_obj', 'BLOQUE_TD_OBJ', true);
			}
			$T->setVar('__medio_tr','<tr height="35px"><td style="font-family: Trebuchet MS, Verdana, sans-serif; font-size: 12px; padding: 1px 6px 1px 6px;width:70px;border: solid 1px #626262">'.$estados.'</td>');
			$T->setVar('__final_tr','</tr></table><br><br>');
			$T->parse('bloque_tr', 'BLOQUE_TR', true);
		}
		if($validador=='true'){
			$T->setVar('__text','<div style="font-weight: 500; page-break-before: always;">Detalle de horarios en que no hubo conectividad:</div>');
		}else{
			$T->setVar('__text', '');
		}
		$this->resultado = $T->parse('out', 'tpl_contenido');
	}

	function getEspecialDisponibilidadPonderadoFullObjetivos() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		global $externa;

		$uptime = array (1,3,7);
		$ponderacion = $usr->getPonderacion();
		$objetivo = new ConfigEspecial($this->objetivo_id);

		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_contenido', 'especial_disponibilidad_ponderada_objetivos.tpl');
		$T->setBlock('tpl_contenido', 'PASOS_HEADER', 'pasos_header');
		$T->setBlock('tpl_contenido', 'PASOS_DATOS', 'pasos_datos');
		$T->setBlock('tpl_contenido', 'ITEMS_DATOS', 'items_datos');
		$T->setBlock('tpl_contenido', 'PASOS_HEADER_RESUMEN', 'pasos_header_resumen');
		$T->setBlock('tpl_contenido', 'PASOS_DATOS_RESUMEN', 'pasos_datos_resumen');
		$T->setBlock('tpl_contenido', 'LISTA_OBJETIVOS', 'lista_objetivos');

		$T->setVar('__item_orden', $this->extra["item_orden"]);

		$orden = 1;
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			$usuario = Utiles::busca_usuario($subobjetivo->objetivo_id);

			$sql = "SELECT * FROM reporte.disponibilidad_resumen_global_ponderado_poritem(".
					pg_escape_string($usuario).",".
					pg_escape_string($subobjetivo->objetivo_id).", ".
					pg_escape_string($ponderacion->ponderacion_id).",' ".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

			//echo $sql;
			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
//			echo $sql;
				$log->setError($sql,$res->userinfo);
				exit();
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["disponibilidad_resumen_global_ponderado_poritem"]);
			$xpath = new DOMXpath($dom);

			$uptime_ponderado = array();
			$conf_ponderaciones = $xpath->query("/atentus/resultados/propiedades/ponderaciones/item");
			$conf_pasos = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$subobjetivo->objetivo_id."]/paso[@visible=1]");

			$T->setVar('__objetivo_nombre', $subobjetivo->nombre);
			$T->setVar('__objetivo_orden', $orden);

			$T->setVar('items_datos', '');

			foreach ($conf_ponderaciones as $i => $conf_ponderacion) {
				$T->setVar('__item_descripcion', str_replace(":00:00", ":00", $conf_ponderacion->getAttribute("inicio")." - ".$conf_ponderacion->getAttribute("termino")));
				$T->setVar('__item_estilo', ($i % 2 == 0)?"#ffffff":"#ebebeb");

				$T->setVar('pasos_datos', '');
				foreach ($conf_pasos as $conf_paso) {
					$uptime_paso = 0;
					$datos = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$subobjetivo->objetivo_id."]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/detalles/detalle[@item_id=".$conf_ponderacion->getAttribute("item_id")."]/estadisticas/estadistica");
					foreach ($datos as $dato) {
						if (in_array($dato->getAttribute("evento_id"), $uptime)) {
							$uptime_paso += $dato->getAttribute("porcentaje");
							$uptime_ponderado[$conf_paso->getAttribute("paso_orden")] += $dato->getAttribute("porcentaje_ponderado");
						}
					}
					$T->setVar('__paso_uptime', number_format($uptime_paso, 2, ',', ''));
					$T->parse('pasos_datos', 'PASOS_DATOS', true);
				}
				$T->parse('items_datos', 'ITEMS_DATOS', true);
			}

			$T->setVar('pasos_datos_resumen', '');
			$T->setVar('pasos_header', '');
			$T->setVar('pasos_header_resumen', '');
			foreach ($conf_pasos as $conf_paso) {
				$T->setVar('__paso_uptime_ponderado', number_format($uptime_ponderado[$conf_paso->getAttribute("paso_orden")], 2, ',', ''));
				$T->parse('pasos_datos_resumen', 'PASOS_DATOS_RESUMEN', true);

				$T->setVar('__paso_nombre', $conf_paso->getAttribute("nombre"));
				$T->parse('pasos_header', 'PASOS_HEADER', true);
				$T->parse('pasos_header_resumen', 'PASOS_HEADER_RESUMEN', true);
			}

			$T->parse('lista_objetivos', 'LISTA_OBJETIVOS', true);
			$orden++;
		}
		$this->resultado = $T->parse('out', 'tpl_contenido');
	}

	public function getEspecialRendimientoPorRangosMix(){
		global $usr;
		if ($usr->orientacion_semaforo == 0) {
			$this->resultado = $this->__getEspecialRendimientoPorRangosMix();
		} else {
			$this->resultado = $this->__getEspecialRendimientoPorRangosMixInvertido();
		}
	}

	private function __getEspecialRendimientoPorRangosMixInvertido() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		global $externa;

		$reporte = new Reporte($this->extra['reporte_id']);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($reporte->xml_configuracion);
		$xpath = new DOMXpath($dom);

		$conf_rangos = $xpath->query('//rangos_rendimiento/rango');
		$conf_grupos = $xpath->query('//grupos/grupo/relacion');

		$data = array();
		$conf_pasos = array();
		$suma_pasos = array();
		$cont_suma_pasos = array();

		foreach ($conf_grupos as $key => $relacion) {
			$sql = "SELECT * FROM reporte.rendimiento_resumen_parcial(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($relacion->getAttribute('objetivo_id')).")";

			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit;
			}
			$row = $res->fetchRow();
			if ($row == null) {
				continue;
			}

			$dom2 = new DomDocument();
			$dom2->preserveWhiteSpace = FALSE;
			$dom2->loadXML($row["rendimiento_resumen_parcial"]);
			$xpath2 = new DOMXpath($dom2);
			$conf_nodos = $xpath->query('relacion[@nodo_id]', $relacion);

			if($key == 0) {
				$conf_pasos = $xpath2->query('//propiedades/objetivos/objetivo[@objetivo_id='.$relacion->getAttribute('objetivo_id').']/paso[@visible=1]');
			}

			foreach ($conf_nodos as $conf_nodo) {
				$nodo_sel = $xpath2->query('//nodos/nodo[@nodo_id='.$conf_nodo->getAttribute('nodo_id').']');
				if ($nodo_sel->length > 0) {

					foreach ($conf_pasos as $conf_paso) {
						if (!array_key_exists($conf_nodo->getAttribute('nodo_id'), $data)) {
							$data[$conf_nodo->getAttribute('nodo_id')] = array();
						}
						if (!array_key_exists($conf_paso->getAttribute('paso_orden'), $suma_pasos)) {
							$suma_pasos[$conf_paso->getAttribute('paso_orden')] = null;
							$cont_suma_pasos[$conf_paso->getAttribute('paso_orden')]= 0;
						}
						$resultado = new stdClass();
						$estadistica = $xpath2->query('//detalles/detalle[@nodo_id='.$conf_nodo->getAttribute('nodo_id').']/detalles/detalle[@paso_orden='.$conf_paso->getAttribute('paso_orden').']/estadisticas/estadistica')->item(0);
						$resultado->paso_nombre = $conf_paso->getAttribute('nombre');
						$resultado->tiempo_prom = $estadistica ? $estadistica->getAttribute('tiempo_prom') : null;
						$resultado->rango_nombre = null;
						$resultado->rango_imagem = null;

						if(!is_null($resultado->tiempo_prom)) {
							$suma_pasos[$conf_paso->getAttribute('paso_orden')]+= $resultado->tiempo_prom;
							$cont_suma_pasos[$conf_paso->getAttribute('paso_orden')]++;
						}

						foreach($conf_rangos as $rango) {
							$min = str_replace(",", ".", $rango->getAttribute('minimo'));
							$max = $rango->getAttribute('maximo') ? str_replace(",", ".", $rango->getAttribute('maximo')) : null;

							if (is_null($resultado->tiempo_prom) and is_null($min) and is_null($max)) {
								$resultado->rango_nombre = $rango->getAttribute('nombre');
								$resultado->rango_imagem = $rango->getAttribute('imagen');
								$resultado->max = $max;
								$resultado->min = $min;
							}
							elseif ($resultado->tiempo_prom >= $min and $resultado->tiempo_prom < $max) {
								$resultado->rango_nombre = $rango->getAttribute('nombre');
								$resultado->rango_imagem = $rango->getAttribute('imagen');
								$resultado->max = $max;
								$resultado->min = $min;
							}
							elseif ($resultado->tiempo_prom >= $min and is_null($max)) {
								$resultado->rango_nombre = $rango->getAttribute('nombre');
								$resultado->rango_imagem = $rango->getAttribute('imagen');
								$resultado->max = $max;
								$resultado->min = $min;
							}
						}
						$data[$conf_nodo->getAttribute('nodo_id')][$conf_paso->getAttribute('paso_orden')] = $resultado;
					}
				}
			}
		}

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (count($data) == 0) {
			return $this->__generarContenedorSinDatos();
		}

		$T =& new Template_PHPLIB( $this->extra["imprimir"]?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_rendimiento_por_rangos_invertido.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS_BLANCO', 'lista_pasos_blanco');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS_DET', 'lista_pasos_det');
		$T->setBlock('tpl_tabla', 'LISTA_TIEMPO_RESPUESTA_DISPLAY', 'lista_tiempo_respuesta_display');
		$T->setBlock('tpl_tabla', 'LISTA_TIEMPO_RESPUESTA', 'lista_tiempo_respuesta');
		$T->setBlock('tpl_tabla', 'LISTA_TIEMPO_RESPUESTA_SUMATORIA', 'lista_tiempo_respuesta_sumatoria');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS', 'lista_nodos');

		$T->setVar('__ancho_tabla', ($this->extra["popup"])?'':'514px');
		$T->setVar('__display_botones', ($this->extra["popup"])?'none':'inline');
		$T->setVar('__popup', ($this->extra["popup"])?'1':'0');

		$print_paso = true;
		$count_paso = 1; // Comienza en 1 ya que la primera columna Total cuenta como un registro mas dentro del total de pasos
		$count_ciclos = 1;
		$suma_global = 0;
		$linea = 0;

		// RECORRE LOS PASOS
		foreach ($data as $conf_nodo_id => $conf_nodo) {
			if($print_paso) {
				foreach ($data[$conf_nodo_id] as $paso_id => $value) {
					$T->setVar('__paso_id', $paso_id);
					$T->setVar('__paso_nombre', $value->paso_nombre);
					$T->parse('lista_pasos', 'LISTA_PASOS', true);
					if(!is_null($suma_pasos[$paso_id])) {
						$suma_pasos[$paso_id]/= $cont_suma_pasos[$paso_id];
					}

					$T->setVar('__tiempo_respuesta_global', (is_null($suma_pasos[$paso_id])?null:number_format($suma_pasos[$paso_id],2,",",null)));
					foreach($conf_rangos as $rango) {
						$min = str_replace(",", ".", $rango->getAttribute('minimo'));
						$max = $rango->getAttribute('maximo') ? str_replace(",", ".", $rango->getAttribute('maximo')) : null;

						if (is_null( $suma_pasos[$paso_id] ) and is_null( $min ) and is_null( $max ) ) {
							$T->setVar('__tiempo_respuesta_global_nombre', $rango->getAttribute('nombre'));
							$T->setVar('__tiempo_respuesta_global_icono', REP_PATH_IMG_SEMAFORO.$rango->getAttribute('imagen'));
						}
						elseif ($suma_pasos[$paso_id] >= $min and $suma_pasos[$paso_id] < $max) {
							$T->setVar('__tiempo_respuesta_global_nombre', $rango->getAttribute('nombre'));
							$T->setVar('__tiempo_respuesta_global_icono', REP_PATH_IMG_SEMAFORO.$rango->getAttribute('imagen'));
						}
						elseif ($suma_pasos[$paso_id] >= $min and is_null( $max )){
							$T->setVar('__tiempo_respuesta_global_nombre', $rango->getAttribute('nombre'));
							$T->setVar('__tiempo_respuesta_global_icono', REP_PATH_IMG_SEMAFORO.$rango->getAttribute('imagen'));
						}
					}

					if(is_null($suma_pasos[$paso_id])) {
						$suma_global = NULL;
					}
					elseif(!is_null($suma_global)) {
						$suma_global+= $suma_pasos[$paso_id];
					}

					$T->parse('lista_tiempo_respuesta_sumatoria', 'LISTA_TIEMPO_RESPUESTA_SUMATORIA', true);
					$count_paso++;
				}
				$print_paso = false;
			}

			$respuesta_sumatoria = 0;
			$T->setVar('lista_tiempo_respuesta_display', '');
			foreach ($data[$conf_nodo_id] as $paso_id=>$info) {
				$T->setVar('__nodo_color', ($linea % 2 == 0)?'D4D4D4':'E9E9E9');
				$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");

				$T->setVar('__tiempo_respuesta_icono', REP_PATH_IMG_SEMAFORO.$info->rango_imagem);
				$T->setVar('__tiempo_respuesta_nombre', $info->rango_nombre);
				$T->setVar('__tiempo_respuesta_valor', $info->tiempo_prom?number_format($info->tiempo_prom,2,",",null):null);

				$T->parse('lista_tiempo_respuesta_display', 'LISTA_TIEMPO_RESPUESTA_DISPLAY', true);

				if(is_null($info->tiempo_prom)) {
					$respuesta_sumatoria = NULL;
				}
				elseif(!is_null($respuesta_sumatoria)) {
					$respuesta_sumatoria+= $info->tiempo_prom;
				}
			}
			$T->setVar('__tiempo_respuesta_sumatoria', (is_null($respuesta_sumatoria) ? '--' : number_format($respuesta_sumatoria,2,",",null)));
			$T->parse('lista_tiempo_respuesta', 'LISTA_TIEMPO_RESPUESTA', true);

			$nodo = $usr->getNodo($conf_nodo_id);
			$T->setVar('__nodo_id', $conf_nodo_id);
			$T->setVar('__nodo_nombre', $nodo->nombre);
			$T->setVar('__nodo_descripcion', $nodo->descripcion);

			$T->parse('lista_nodos', 'LISTA_NODOS', true);
			$linea++;
		}
		$T->setVar('lista_pasos_blanco', '');
		if (($count_paso % 6) != 0 and !$this->extra["popup"]) {
			for ($i = ($count_paso % 6); $i < 6; $i++) {
				$T->parse('lista_pasos_blanco', 'LISTA_PASOS_BLANCO', true);
			}
		}

		$T->setVar('__tiempo_respuesta_sumatoria_global', (is_null($suma_global) ? '--' : number_format($suma_global,2,",",null)));

		$this->tiempo_expiracion = strtotime( date("Y-m-d H:i:s", strtotime("+5 minutes")) ) - strtotime(date("Y-m-d H:i:s"));
		return $T->parse('out', 'tpl_tabla');
	}

	private function __getEspecialRendimientoPorRangosMix() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		global $externa;

		$reporte = new Reporte($this->extra['reporte_id']);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($reporte->xml_configuracion);
		$xpath = new DOMXpath($dom);

		$conf_rangos = $xpath->query('//rangos_rendimiento/rango');
		$conf_grupos = $xpath->query('//grupos/grupo/relacion');

		$data = array();
		$conf_pasos = array();
		$suma_nodos = array();

		foreach ($conf_grupos as $key => $relacion) {
			$sql = "SELECT * FROM reporte.rendimiento_resumen_parcial(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($relacion->getAttribute('objetivo_id')).")";

			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit;
			}

			$row = $res->fetchRow();
			if ($row == null) {
				continue;
			}

			$dom2 = new DomDocument();
			$dom2->preserveWhiteSpace = FALSE;
			$dom2->loadXML($row["rendimiento_resumen_parcial"]);
			$xpath2 = new DOMXpath($dom2);

			$conf_nodos = $xpath->query('relacion[@nodo_id]', $relacion);

			if ($key == 0) {
				$conf_pasos = $xpath2->query('//propiedades/objetivos/objetivo[@objetivo_id='.$relacion->getAttribute('objetivo_id').']/paso[@visible=1]');
			}

			foreach ($conf_nodos as $conf_nodo) {
				$nodo_sel = $xpath2->query('//nodos/nodo[@nodo_id='.$conf_nodo->getAttribute('nodo_id').']');
				if ($nodo_sel->length > 0) {

					foreach ($conf_pasos as $conf_paso) {
						if(!array_key_exists($conf_paso->getAttribute('paso_orden'), $data)) {
							$data[$conf_paso->getAttribute('paso_orden')] = array();
						}
						if(!array_key_exists($conf_nodo->getAttribute('nodo_id'),$suma_nodos)) {
							$suma_nodos[$conf_nodo->getAttribute('nodo_id')] = 0;
						}
						$resultado = new stdClass();
						$estadistica = $xpath2->query('//detalles/detalle[@nodo_id='.$conf_nodo->getAttribute('nodo_id').']/detalles/detalle[@paso_orden='.$conf_paso->getAttribute('paso_orden').']/estadisticas/estadistica')->item(0);
						$resultado->tiempo_prom = $estadistica ? $estadistica->getAttribute('tiempo_prom') : null;
						$resultado->rango_nombre = null;
						$resultado->rango_imagem = null;

						if(is_null($resultado->tiempo_prom)){
							$suma_nodos[$conf_nodo->getAttribute('nodo_id')] = NULL;
						}
						elseif (!is_null($suma_nodos[$conf_nodo->getAttribute('nodo_id')])) {
							$suma_nodos[$conf_nodo->getAttribute('nodo_id')] += $resultado->tiempo_prom;
						}

						foreach ($conf_rangos as $rango) {
							$min = str_replace(",", ".", $rango->getAttribute('minimo'));
							$max = $rango->getAttribute('maximo') ? str_replace(",", ".", $rango->getAttribute('maximo')) : null;

							if (is_null($resultado->tiempo_prom) and is_null($min) and is_null($max)) {
								$resultado->rango_nombre = $rango->getAttribute('nombre');
								$resultado->rango_imagem = $rango->getAttribute('imagen');
								$resultado->max = $max; $resultado->min = $min;
							}
							elseif ($resultado->tiempo_prom >= $min and $resultado->tiempo_prom < $max) {
								$resultado->rango_nombre = $rango->getAttribute('nombre');
								$resultado->rango_imagem = $rango->getAttribute('imagen');
								$resultado->max = $max; $resultado->min = $min;
							}
							elseif ($resultado->tiempo_prom >= $min and is_null($max)) {
								$resultado->rango_nombre = $rango->getAttribute('nombre');
								$resultado->rango_imagem = $rango->getAttribute('imagen');
								$resultado->max = $max; $resultado->min = $min;
							}
						}
						$data[$conf_paso->getAttribute('paso_orden')][$conf_nodo->getAttribute('nodo_id')] = $resultado;
					}
				}
			}
		}

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if (count($data) == 0) {
			return $this->__generarContenedorSinDatos();
		}

		$T =& new Template_PHPLIB( $this->extra["imprimir"]?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_rendimiento_por_rangos.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_MONITORES_BLANCO', 'lista_monitores_blanco');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS', 'lista_nodos');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS_DET', 'lista_nodos_det');
		$T->setBlock('tpl_tabla', 'LISTA_TIEMPO_RESPUESTA_DISPLAY', 'lista_tiempo_respuesta_display');
		$T->setBlock('tpl_tabla', 'LISTA_TIEMPO_RESPUESTA', 'lista_tiempo_respuesta');
		$T->setBlock('tpl_tabla', 'LISTA_TIEMPO_RESPUESTA_SUMATORIA', 'lista_tiempo_respuesta_sumatoria');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');

		$T->setVar('__ancho_tabla', ($this->extra["popup"])?'':'514px');
		$T->setVar('__display_botones', ($this->extra["popup"])?'none':'inline');
		$T->setVar('__popup', ($this->extra["popup"])?'1':'0');

		$print_nodo = true;
		$count_paso = 0;
		$count_nodo = 0;
		$suma_global = 0;
		$linea = 1;

		// RECORRE LOS PASOS
		foreach ($conf_pasos as $key=> $conf_paso) {

			//SEGUIRÁ SOLO SI EL PASOS SE ENCUENTRA BIEN FORMADO
			if(isset($data[$conf_paso->getAttribute('paso_orden')])) {

				if($print_nodo) {
					foreach ($data[$conf_paso->getAttribute('paso_orden')] as $nodo_id => $value) {
						$nodo = $usr->getNodo($nodo_id);
						$T->setVar('__nodo_id', $nodo_id);
						$T->setVar('__nodo_nombre', $nodo->nombre);
						$T->setVar('__nodo_descripcion', $nodo->descripcion);
						$T->parse('lista_nodos', 'LISTA_NODOS', true);
						$T->parse('lista_nodos_det', 'LISTA_NODOS_DET', true);

						$T->setVar('__tiempo_respuesta_sumatoria', (is_null($suma_nodos[$nodo_id]) ? '--' : number_format($suma_nodos[$nodo_id],2,",",null)));
						$T->parse('lista_tiempo_respuesta_sumatoria', 'LISTA_TIEMPO_RESPUESTA_SUMATORIA', true);
						$count_nodo++;
					}
					$print_nodo = false;
				}

				$suma = 0;
				$contador = 0;
				$T->setVar('lista_tiempo_respuesta_display', '');
				foreach ($data[$conf_paso->getAttribute('paso_orden')] as $nodo_id=>$info) {
					$T->setVar('__paso_color', ($linea % 2 == 0)?'D4D4D4':'E9E9E9');
					$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");

					$T->setVar('__tiempo_respuesta_icono', REP_PATH_IMG_SEMAFORO.$info->rango_imagem);
					$T->setVar('__tiempo_respuesta_nombre', $info->rango_nombre);
					$T->setVar('__tiempo_respuesta_valor', $info->tiempo_prom?number_format($info->tiempo_prom,2,",",null):null);
					if($info->tiempo_prom){
						$suma+= $info->tiempo_prom; $contador++;
					}
					$T->parse('lista_tiempo_respuesta_display', 'LISTA_TIEMPO_RESPUESTA_DISPLAY', true);
				}
				$T->parse('lista_tiempo_respuesta', 'LISTA_TIEMPO_RESPUESTA', true);

				$valor_global = $contador?($suma/$contador):NULL;
				if(is_null($valor_global)) {
					$suma_global = NULL;
				}
				elseif(!is_null($suma_global)) {
					$suma_global += $valor_global;
				}

				foreach($conf_rangos as $rango) {
					$min = str_replace(",", ".", $rango->getAttribute('minimo'));
					$max = $rango->getAttribute('maximo') ? str_replace(",", ".", $rango->getAttribute('maximo')) : null;

					if (is_null( $valor_global ) and is_null( $min ) and is_null( $max ) ) {
						$T->setVar('__tiempo_respuesta_global_nombre', $rango->getAttribute('nombre'));
						$T->setVar('__tiempo_respuesta_global_icono', REP_PATH_IMG_SEMAFORO.$rango->getAttribute('imagen'));
					}
					elseif ($valor_global >= $min and $valor_global < $max) {
						$T->setVar('__tiempo_respuesta_global_nombre', $rango->getAttribute('nombre'));
						$T->setVar('__tiempo_respuesta_global_icono', REP_PATH_IMG_SEMAFORO.$rango->getAttribute('imagen'));
					}
					elseif ($valor_global >= $min and is_null( $max )){
						$T->setVar('__tiempo_respuesta_global_nombre', $rango->getAttribute('nombre'));
						$T->setVar('__tiempo_respuesta_global_icono', REP_PATH_IMG_SEMAFORO.$rango->getAttribute('imagen'));
					}
				}
				$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
				$T->setVar('__tiempo_respuesta_global', is_null($valor_global)?$valor_global:number_format($valor_global,2,",",null));
				$T->parse('lista_pasos', 'LISTA_PASOS', true);
				$linea++;
			}
		}
		$T->setVar('lista_monitores_blanco', '');
		if (($count_nodo % 6) != 0 and !$this->extra["popup"]) {
			for ($i = ($count_nodo % 6); $i < 6; $i++) {
				$T->parse('lista_monitores_blanco', 'LISTA_MONITORES_BLANCO', true);
			}
		}

		$T->setVar('__tiempo_respuesta_sumatoria_global', (is_null($suma_global) ? '--' : number_format($suma_global,2,",",null)));

		$this->tiempo_expiracion = strtotime( date("Y-m-d H:i:s", strtotime("+5 minutes")) ) - strtotime(date("Y-m-d H:i:s"));
		return $T->parse('out', 'tpl_tabla');
	}
	
	private function getEspecialRendimientoPorRangos() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

        $objetivo = new ConfigEspecial($this->extra['parent_objetivo_id']);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($objetivo->__xml_config);
		$xpath = new DOMXpath($dom);

		$conf_rangos = $xpath->query('//rangos_rendimiento/rango');

        $sql = "SELECT * FROM reporte.rendimiento_resumen_parcial(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).",".
				pg_escape_string($this->horario_id).",".
				"'".pg_escape_string($this->timestamp->getInicioPeriodo())."',".
				"'".pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
// 		print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit;
		}

		$row = $res->fetchRow();
		$dom2 = new DomDocument();
		$dom2->preserveWhiteSpace = FALSE;
		$dom2->loadXML($row["rendimiento_resumen_parcial"]);
		$xpath2 = new DOMXpath($dom2);

		/* SI NO HAY DATOS MOSTRAR MENSAJE */
		if ($xpath2->query('/atentus/resultados/detalles/detalle/detalles/detalle')->length == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
            return;
		}

		$T =& new Template_PHPLIB( $this->extra["imprimir"]?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_rendimiento_por_rangos.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_MONITORES_BLANCO', 'lista_monitores_blanco');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS', 'lista_nodos');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS_DET', 'lista_nodos_det');
		$T->setBlock('tpl_tabla', 'LISTA_TIEMPO_RESPUESTA_DISPLAY', 'lista_tiempo_respuesta_display');
		$T->setBlock('tpl_tabla', 'LISTA_TIEMPO_RESPUESTA', 'lista_tiempo_respuesta');
		$T->setBlock('tpl_tabla', 'LISTA_TIEMPO_RESPUESTA_SUMATORIA', 'lista_tiempo_respuesta_sumatoria');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');

		$T->setVar('__ancho_tabla', ($this->extra["popup"])?'':'514px');
		$T->setVar('__display_botones', ($this->extra["popup"])?'none':'inline');
		$T->setVar('__popup', ($this->extra["popup"])?'1':'0');

		$suma_nodo = array();
		$count_nodo = 0;
		$suma_global = 0;
		$linea = 1;

		$conf_objetivo= $xpath2->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath2->query("paso[@visible=1]", $conf_objetivo);
		$conf_nodos = $xpath2->query("//nodos/nodo[@nodo_id!=0]");

		foreach ($conf_pasos as $conf_paso) {

			$detalle_objetivo= $xpath2->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$conf_objetivo->getAttribute('objetivo_id')."]")->item(0);
			$suma = 0;
			$contador = 0;

			$T->setVar('lista_tiempo_respuesta_display', '');
			foreach ($conf_nodos as  $conf_nodo) {

					$conf_detalles = $xpath2->query('detalles/detalle[@nodo_id='.$conf_nodo->getAttribute('nodo_id').']/detalles/detalle[@paso_orden='.$conf_paso->getAttribute('paso_orden').']/estadisticas/estadistica',$detalle_objetivo);
					$T->setVar('__paso_color', ($linea % 2 == 0)?'D4D4D4':'E9E9E9');
					$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");

					if($conf_detalles->length == 0){
						$conf_rango = $conf_rangos->item(0);
						$T->setVar('__tiempo_respuesta_icono', REP_PATH_IMG_SEMAFORO.$conf_rango->getAttribute('imagen'));
						$T->setVar('__tiempo_respuesta_nombre', $conf_rango->getAttribute('nombre'));
						$T->setVar('__tiempo_respuesta_valor', null);
                        $T->parse('lista_tiempo_respuesta_display', 'LISTA_TIEMPO_RESPUESTA_DISPLAY', true);
//                        $contador++;
					}
                    else {
 						foreach ($conf_detalles as $conf_detalle) {

							foreach ($conf_rangos as $rango) {
								$min = str_replace(",", ".", $rango->getAttribute('minimo'));
								$max = $rango->getAttribute('maximo') ? str_replace(",", ".", $rango->getAttribute('maximo')) : null;

								if (is_null($conf_detalle->getAttribute('tiempo_prom')) and is_null($min) and is_null($max)) {
									$T->setVar('__tiempo_respuesta_icono', REP_PATH_IMG_SEMAFORO.$rango->getAttribute('imagen'));
									$T->setVar('__tiempo_respuesta_nombre', $rango->getAttribute('nombre'));
								}
								elseif ($conf_detalle->getAttribute('tiempo_prom') >= $min and $conf_detalle->getAttribute('tiempo_prom') < $max) {
									$T->setVar('__tiempo_respuesta_icono', REP_PATH_IMG_SEMAFORO.$rango->getAttribute('imagen'));
									$T->setVar('__tiempo_respuesta_nombre', $rango->getAttribute('nombre'));
								}
								elseif ($conf_detalle->getAttribute('tiempo_prom') >= $min and is_null($max)) {
									$T->setVar('__tiempo_respuesta_icono', REP_PATH_IMG_SEMAFORO.$rango->getAttribute('imagen'));
									$T->setVar('__tiempo_respuesta_nombre', $rango->getAttribute('nombre'));
								}
							}

							$T->setVar('__tiempo_respuesta_valor', $conf_detalle->getAttribute('tiempo_prom')?number_format($conf_detalle->getAttribute('tiempo_prom'),2,",",null):null);
							if($conf_detalle->getAttribute('tiempo_prom')){
								$suma+= $conf_detalle->getAttribute('tiempo_prom');
                                $contador++;
                                $suma_nodo[$conf_nodo->getAttribute('nodo_id')] += $conf_detalle->getAttribute('tiempo_prom');
							}
							$T->parse('lista_tiempo_respuesta_display', 'LISTA_TIEMPO_RESPUESTA_DISPLAY', true);
						}
					}
				}
				$T->parse('lista_tiempo_respuesta', 'LISTA_TIEMPO_RESPUESTA', true);

				$valor_global = $contador?($suma/$contador):NULL;
				if (is_null($valor_global)) {
					$suma_global = NULL;
				}
				elseif (!is_null($suma_global)) {
					$suma_global += $valor_global;
				}

				foreach($conf_rangos as $rango) {
					$min = str_replace(",", ".", $rango->getAttribute('minimo'));
					$max = $rango->getAttribute('maximo') ? str_replace(",", ".", $rango->getAttribute('maximo')) : null;

					if (is_null( $valor_global ) and is_null( $min ) and is_null( $max ) ) {
						$T->setVar('__tiempo_respuesta_global_nombre', $rango->getAttribute('nombre'));
						$T->setVar('__tiempo_respuesta_global_icono', REP_PATH_IMG_SEMAFORO.$rango->getAttribute('imagen'));
					}
					elseif ($valor_global >= $min and $valor_global < $max) {
						$T->setVar('__tiempo_respuesta_global_nombre', $rango->getAttribute('nombre'));
						$T->setVar('__tiempo_respuesta_global_icono', REP_PATH_IMG_SEMAFORO.$rango->getAttribute('imagen'));
					}
					elseif ($valor_global >= $min and is_null($max)){
						$T->setVar('__tiempo_respuesta_global_nombre', $rango->getAttribute('nombre'));
						$T->setVar('__tiempo_respuesta_global_icono', REP_PATH_IMG_SEMAFORO.$rango->getAttribute('imagen'));
					}
				}

			$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
			$T->setVar('__tiempo_respuesta_global', is_null($valor_global)?$valor_global:number_format($valor_global, 2, ",", null));
			$T->parse('lista_pasos', 'LISTA_PASOS', true);
			$linea++;
		}

		foreach ($conf_nodos as  $conf_nodo) {
			$T->setVar('__nodo_id', $conf_nodo->getAttribute('nodo_id'));
			$T->setVar('__nodo_nombre', $conf_nodo->getAttribute('nombre'));
			$T->setVar('__nodo_descripcion', $conf_nodo->getAttribute('descripcion'));
			$T->parse('lista_nodos', 'LISTA_NODOS', true);
			$T->parse('lista_nodos_det', 'LISTA_NODOS_DET', true);

			$T->setVar('__tiempo_respuesta_sumatoria', (is_null($suma_nodo[$conf_nodo->getAttribute('nodo_id')]) ? '--' : number_format($suma_nodo[$conf_nodo->getAttribute('nodo_id')], 2, ",", null)));
			$T->parse('lista_tiempo_respuesta_sumatoria', 'LISTA_TIEMPO_RESPUESTA_SUMATORIA', true);
			$count_nodo++;
		}

		$T->setVar('__tiempo_respuesta_sumatoria_global', (is_null($suma_global) ? '--' : number_format($suma_global, 2, ",", null)));

		$this->resultado = $T->parse('out', 'tpl_tabla');

	}

    /*
	public function getEspecialComparativoMixObjetivos() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$objetivo = new ConfigEspecial($this->objetivo_id);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($objetivo->__xml_config);
		$xpath = new DOMXpath($dom);

		$array_total = array();
		$array_data = array();
		$conf_grupos = $xpath->query('//grupos/grupo/relacion');

		foreach ($conf_grupos as $key => $relacion) {
			$sql = "SELECT * FROM reporte.comparativo_resumen_parcial(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($relacion->getAttribute('objetivo_id')).", ".
					pg_escape_string($this->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				continue;
			}

			$row = $res->fetchRow();
			$dom2 = new DomDocument();
			$dom2->preserveWhiteSpace = FALSE;
			$dom2->loadXML($row["comparativo_resumen_parcial"]);
			$xpath2 = new DOMXpath($dom2);

			$conf_pasos = $xpath2->query('//objetivos/objetivo/paso[@visible=1]');
			$conf_nodos = $xpath->query('relacion[@nodo_id]', $relacion);

			foreach ($conf_nodos as $conf_nodo) {
				$nodo_sel = $xpath2->query('//nodos/nodo[@nodo_id='.$conf_nodo->getAttribute('nodo_id').']');
				if ($nodo_sel->length > 0) {

					$array_total[$conf_nodo->getAttribute('nodo_id')] = 0;
					foreach($conf_pasos as $conf_paso) {

						if (!isset($array_data[$conf_paso->getAttribute('paso_orden')])) {
							$array_data[$conf_paso->getAttribute('paso_orden')] = array();
						}

						$data = $xpath2->query('//detalle[@nodo_id='.$conf_nodo->getAttribute('nodo_id').']/detalles/detalle[@paso_orden='.$conf_paso->getAttribute('paso_orden').']/estadisticas/estadistica')->item(0);
						if ($data != null) {
							$paso = new stdClass();
							$paso->tiempo_prom = $data->getAttribute('tiempo_prom');
							$paso->uptime = $data->getAttribute('uptime');

							$array_data[$conf_paso->getAttribute('paso_orden')][$conf_nodo->getAttribute('nodo_id')] = $paso;
							$array_total[$conf_nodo->getAttribute('nodo_id')] += $data->getAttribute('tiempo_prom');
						}
					}
				}
			}
		}

		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_tabla', 'especial_comparativo_resumen.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS_TITULO', 'lista_nodos_titulos');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS_DATOS', 'lista_nodos_datos');
		$T->setBlock('tpl_tabla', 'LISTA_DATOS', 'lista_datos');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');

		$T->setVar('lista_nodos_titulos', '');
		$T->setVar('lista_nodos_datos', '');
		foreach ($array_total as $nodo_id => $total) {
			$nodo = $usr->getNodo($nodo_id);
			$T->setVar('__nodo_nombre', $nodo->nombre);
			$T->parse('lista_nodos_titulos', 'LISTA_NODOS_TITULO', true);

			$T->setVar('__nodo_rendimiento', number_format($total, 2, ',', '.'));
			$T->parse('lista_nodos_datos', 'LISTA_NODOS_DATOS', true);
		}

		$total_rendimiento = 0;
		$linea = 1;
		$T->setVar('lista_pasos', '');
		foreach ($conf_pasos as $conf_paso) {

			if (isset($array_data[$conf_paso->getAttribute('paso_orden')])) {
				$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
				$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");

				$paso_rendimiento = 0;
				$paso_uptime = 0;
				$cnt_nodos = 0;
				$T->setVar('lista_datos', '');
				foreach ($array_total as $nodo_id => $total) {
					$data = $array_data[$conf_paso->getAttribute('paso_orden')][$nodo_id];
					if ($data == null) {
						$T->setVar('__parcial_rendimiento', '-');
						$T->setVar('__parcial_uptime', '-');
					}
					else {
						$T->setVar('__parcial_rendimiento', $data->tiempo_prom);
						$T->setVar('__parcial_uptime', $data->uptime);
						$paso_rendimiento += $data->tiempo_prom;
						$paso_uptime += $data->uptime;
						$cnt_nodos++;
					}
					$T->parse('lista_datos', 'LISTA_DATOS', true);
				}
				$T->setVar('__paso_rendimiento', number_format(($paso_rendimiento/$cnt_nodos), 2, ',', '.'));
				$T->setVar('__paso_uptime', number_format(($paso_uptime/$cnt_nodos), 2, ',', '.'));
				$T->parse('lista_pasos', 'LISTA_PASOS', true);
				$total_rendimiento += ($paso_rendimiento/$cnt_nodos);
				$linea++;
			}
		}
		$T->setVar('__rendimiento_total', number_format($total_rendimiento, 2, ',', '.'));
		$this->resultado = $T->parse('out', 'tpl_tabla');
	}
    */

	/*
	 Creado por: Francisco Ormeño
	 Modificado por:
	 Fecha de modificacion:22/04/2016
	 */
    public function getEspecialComparativoObjetivos() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		$total=array();

		/* OBTENER LOS DATOS  Y PARSEARLO */
		$sql = "SELECT * FROM reporte.comparativo_resumen_parcial(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//					print $sql.'<br>';
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if($row = $res->fetchRow()){
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["comparativo_resumen_parcial"]);
			$xpath = new DOMXpath($dom);
		}

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql2 = "SELECT * FROM reporte.disponibilidad_resumen_consolidado (".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).",'".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."', ".
				(isset($this->extra["variable"])?$usr->cliente_id:"0").")";
// 							print $sql2.'<br>';
		$res = & $mdb2->query($sql2);
		if (MDB2::isError($res)) {
			$log->setError($sql2, $res->userinfo);
			exit();
		}

		if($row2 = $res->fetchRow()){
			$dom2 = new DomDocument();
			$dom2->preserveWhiteSpace = FALSE;
			$dom2->loadXML($row2["disponibilidad_resumen_consolidado"]);
			$xpath2 = new DOMXpath($dom2);
		}

		$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query('//objetivos/objetivo/paso[@visible=1]');
		$conf_nodos = $xpath->query('//nodos/nodo[@nodo_id!=0]');

		/**SE CUENTAN LA CANTIDAD DE RESULTADO PARA EL OBJETIVO**/
		$count_datos=$xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$conf_objetivo->getAttribute('objetivo_id')."]/detalles/detalle")->length;

		if ($count_datos==0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_PRINTTEMPLATES);
		$T->setFile('tpl_tabla', 'especial_comparativo_resumen.tpl');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS_TITULO', 'lista_nodos_titulos');
		$T->setBlock('tpl_tabla', 'LISTA_NODOS_DATOS', 'lista_nodos_datos');
		$T->setBlock('tpl_tabla', 'LISTA_DATOS', 'lista_datos');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');

		$cnt_pasos = 0;
		$total_uptime=0;
		$total_downtime=0;
		$total_downtime_parcial=0;
		$total_rendimiento = 0;
		$linea = 1;

		/*SE RECORREN LOS PASOS VISIBLES DEL OBJETIVO */
		$T->setVar('lista_pasos', '');
		foreach ($conf_pasos as $conf_paso) {
			$tag_paso = $xpath2->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]")->item(0);

			$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
			$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");

			$paso_rendimiento = 0;
			$cnt_nodos = 0;

			/*SE RECORREN LOS NODOS DEL OBJETIVO */
			$T->setVar('lista_datos', '');
			foreach ($conf_nodos as $conf_nodo) {
				$data = $xpath->query('//detalle[@nodo_id='.$conf_nodo->getAttribute('nodo_id').']/detalles/detalle[@paso_orden='.$conf_paso->getAttribute('paso_orden').']/estadisticas/estadistica')->item(0);
				if ($data == null) {
					$T->setVar('__parcial_rendimiento', '-');
					$T->setVar('__parcial_uptime', '-');
				}
				else {
					$total_porcentaje = $data->getAttribute('uptime') + $data->getAttribute('downtime');
					$uptime_nodo = number_format(round(($data->getAttribute('uptime')*100)/$total_porcentaje,2), 2, ',', '.');

					$T->setVar('__parcial_rendimiento', $data->getAttribute('tiempo_prom'));
					$T->setVar('__parcial_uptime', $uptime_nodo);


					$total[$conf_nodo->getAttribute('nodo_id')]+=$data->getAttribute('tiempo_prom');
					$paso_rendimiento += $data->getAttribute('tiempo_prom');
                    $cnt_nodos++;
				}
				$T->parse('lista_datos', 'LISTA_DATOS', true);
			}

			/**SE EXTRAEN LOS DATOS SEGUN EL EVENTO UPTIME,DOWNTIME Y DOWNTIME PARCIAL**/
			$tag_uptime = $xpath2->query("estadisticas/estadistica[@evento_id=1]", $tag_paso);
			$tag_downtime= $xpath2->query("estadisticas/estadistica[@evento_id=2]", $tag_paso);
			$tag_downtime_parcial= $xpath2->query("estadisticas/estadistica[@evento_id=3]", $tag_paso);
			$paso_rendimiento = $paso_rendimiento / $cnt_nodos;

			/**sE VERIFICA SI HAY DATOS PARA CADA EVENTO**/
			$dato_uptime=(($tag_uptime->length > 0)?$tag_uptime->item(0)->getAttribute("porcentaje"):0);
			$dato_downtime=(($tag_downtime->length > 0)?$tag_downtime->item(0)->getAttribute("porcentaje"):0);
			$dato_downtime_parcial=(($tag_downtime_parcial->length > 0)?$tag_downtime_parcial->item(0)->getAttribute("porcentaje"):0);

			/**sE CALCULA EL 100% SUMANDO LAS 3 VARIABLES Y DESPUES SE SACA SU RESPECTIVO PORCENTAJE **/
			$total_porcentaje =$dato_uptime+$dato_downtime+$dato_downtime_parcial;
			$uptime_global = number_format(round(($dato_uptime*100)/$total_porcentaje,2), 2, ',', '.');
			$downtime_global = number_format(round(($dato_downtime*100)/$total_porcentaje,2), 2, ',', '.');
			$downtime_parcial_global = number_format(round(($dato_downtime_parcial*100)/$total_porcentaje,2), 2, ',', '.');

			/**SE INSERTAN LAS VARIABLES DENTRO DEL TEMPLATE **/
			$T->setVar('__paso_uptime',$uptime_global);
			$T->setVar('__paso_downtime', $downtime_global);
			$T->setVar('__paso_downtime_parcial', $downtime_parcial_global);
			$T->setVar('__paso_tiempo_respuesta', number_format($paso_rendimiento, 2, ',', '.'));

			/**SE GUARDA EN UNA VARIABLE PARA SACAR UN PROMEDIO POR PASO**/
			$total_uptime += $uptime_global;
			$total_downtime += $downtime_global;
			$total_downtime_parcial +=$downtime_parcial_global;
			$total_rendimiento += $paso_rendimiento;

			$linea++;
			$cnt_pasos++;
			$T->parse('lista_pasos', 'LISTA_PASOS', true);
		}

		$T->setVar('__rendimiento_uptime', number_format(($total_uptime/$cnt_pasos), 2, ',', '.'));
		$T->setVar('__rendimiento_downtime', number_format(($total_downtime/$cnt_pasos), 2, ',', '.'));
		$T->setVar('__rendimiento_parcial', number_format(($total_downtime_parcial/$cnt_pasos), 2, ',', '.'));
 		$T->setVar('__rendimiento_tiempo_respuesta', number_format(($total_rendimiento/$cnt_pasos), 2, ',', '.'));


		$T->setVar('lista_nodos_titulos', '');
		$T->setVar('lista_nodos_datos', '');
		foreach ($conf_nodos as $conf_nodo) {
			$T->setVar('__nodo_nombre', $conf_nodo->getAttribute('nombre'));
			$T->parse('lista_nodos_titulos', 'LISTA_NODOS_TITULO', true);

			$T->setVar('__nodo_rendimiento', number_format(($total[$conf_nodo->getAttribute('nodo_id')]/$cnt_pasos), 2, ',', '.'));
			$T->parse('lista_nodos_datos', 'LISTA_NODOS_DATOS', true);
		}

		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	/*
	--Creado por: Francisco Ormeño
	--Modificado por:
	--Fecha de creacion:10/02/2017
	--Fecha de modificacion:
	*/
	public function getEspecialHabitat() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		$tipo_horario ='';
		$last_paso_orden=-1;
		$horarios = $this->extra['horarios'];
		unset($horarios[0]);

		$array_detalle = array();

		$array_mensual_semana = array();
		$array_mensual_findesemana = array();
		$count_dia_semana =0;
		$count_dia_findesemana=0;

		foreach ($horarios as $key =>$horario){

			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT * FROM reporte.disponibilidad_resumen_mensual_ponderado(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).",".
					pg_escape_string($horario->horario_id).",'".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
// 			print($sql);//exit;
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = false;
			$dom->loadXML($row["disponibilidad_resumen_mensual_ponderado"]);
			$xpath = new DOMXpath($dom);
			unset($row["disponibilidad_resumen_mensual_ponderado"]);

			$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);

			$count_datos=$xpath->query("//detalles/detalle[@objetivo_id=".$conf_objetivo->getAttribute('objetivo_id')."]/detalles/detalle")->length;
 			if ($count_datos==0) {
 				continue;
 			}
 			$tag_objetivo = $xpath->query("//detalles/detalle[@objetivo_id= ".$conf_objetivo->getAttribute('objetivo_id')."]")->item(0);
 			$array_tipo_horario =  explode(' ',$tag_objetivo->getAttribute('tipo_horario'));

 			if($array_tipo_horario[0]=='Lu'){
 				$titulo_horario =substr($tag_objetivo->getAttribute('tipo_horario'), 0,8);
 				$array_mensual_semana[0]+=$tag_objetivo->getAttribute('ponderado')*$tag_objetivo->getAttribute('ponderacion_horario');
 				$count_dia_semana+=1;
 			}else{
 				$titulo_horario =substr($tag_objetivo->getAttribute('tipo_horario'), 0,9);
 				$array_mensual_findesemana[0]+=$tag_objetivo->getAttribute('ponderado')*$tag_objetivo->getAttribute('ponderacion_horario');
 				$count_dia_findesemana+=1;
 			}

 			if($titulo_horario!=$tipo_horario){
 				$array_detalle[$key]['tipo_horario'] =$titulo_horario;
 			}

 			foreach ($conf_pasos as $conf_paso){
 				$med_ok=0;
 				$med_totales=0;

 				if($titulo_horario!=$tipo_horario){
 					$paso = new stdClass();
 					$paso->paso_orden=$conf_paso->getAttribute('paso_orden');
 					$paso->nombre=$conf_paso->getAttribute('nombre');
 					$paso->medicion_ok=0;
 					$paso->medicion_totales=0;
 					$array_detalle[$key]['pasos'][$conf_paso->getAttribute('paso_orden')]=$paso;
 					$ultimo_paso=$key;
 				}

 				foreach ($xpath->query("detalles/detalle",$tag_objetivo)as $tag_fechas){
					$tag_paso = $xpath->query("detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]",$tag_fechas)->item(0);
					$tag_dato = $xpath->query("datos/dato",$tag_paso)->item(0);
					$med_ok+=(($tag_dato!=null)?$tag_dato->getAttribute('uptime'):0);
					$med_totales+=(($tag_dato!=null)?$tag_dato->getAttribute('total'):0);
				}
				$pass=$array_detalle[$ultimo_paso]['pasos'][$conf_paso->getAttribute('paso_orden')];
				$pass->medicion_ok+=$med_ok;
				$pass->medicion_totales+=$med_totales;

			}
			$tipo_horario=$titulo_horario;
		}


		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'disponibilidad_mensual_ponderada.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_tabla', 'LISTA_HORARIO', 'lista_horario');


		$T->setVar('__porcentaje_semana', number_format($array_mensual_semana[0], 2, ',', '.'));
		$T->setVar('__porcentaje_findesemana', number_format($array_mensual_findesemana[0], 2, ',', '.'));
		$T->setVar('__porcentaje_mensual', number_format(($array_mensual_semana[0]+$array_mensual_findesemana[0])/2, 2, ',', '.'));

		$T->setVar('lista_horario', '');
		foreach ($array_detalle as $tag_horario){
			$T->setVar('__tipo_horario', $tag_horario['tipo_horario']);

			$T->setVar('bloque_pasos', '');
			foreach ($tag_horario['pasos']as $tag_paso){
				$porcentaje = number_format(($tag_paso->medicion_ok*100/$tag_paso->medicion_totales), 2, ',', '.');
				$T->setVar('__paso_nombre', $tag_paso->nombre);
				$T->setVar('__cantidad_ok', $tag_paso->medicion_ok);
				$T->setVar('__cantidad_total', $tag_paso->medicion_totales);
				$T->setVar('__porcentaje_paso', $porcentaje);
				$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
			}

			$T->parse('lista_horario', 'LISTA_HORARIO', true);
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		$this->resultado = $T->parse('out', 'tpl_tabla');

	}

	public function getEstadisticaEventos() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$index = 0;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.evento_resumen_global(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", 0,' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = false;
		$dom->loadXML($row["evento_resumen_global"]);
		$xpath = new DOMXpath($dom);
		unset($row["evento_resumen_global"]);

		$conf_pasos = $xpath->query('//propiedades/objetivos/objetivo[@objetivo_id='.$this->objetivo_id.']/paso[@visible=1]');
		$conf_detalles = $xpath->query('//detalles/detalle[@objetivo_id='.$this->objetivo_id.']/detalles')->item(0);

		if ( $xpath->query('detalle/datos/dato', $conf_detalles)->length === 0 ){
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB($this->extra["imprimir"]?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'estadistica_eventos.tpl');
		$T->setBlock('tpl_tabla', 'PASO_DESCRIPCION', 'paso_descripcion');
		$T->setBlock('tpl_tabla', 'LISTA_EVENTOS', 'lista_eventos');
		$T->setBlock('tpl_tabla', 'LISTA_PASOS', 'lista_pasos');

		foreach ( $conf_pasos as $paso ) {
			$conf_datos = $xpath->query('detalle[@paso_orden='.$paso->getAttribute('paso_orden').']/datos/dato', $conf_detalles);
			$T->setVar('__paso_nombre', $paso->getAttribute('nombre'));
			$T->setVar('__rowspan', $conf_datos->length);
			$T->parse('paso_descripcion', 'PASO_DESCRIPCION', true);

			// Ajuste de porcentajes
			$total_porcentaje = 0;
			$codigo_porcentajes = array();
			$codigo_mayor_porcentaje = 0;
			$codigo_porcentaje = 0;
			foreach ($conf_datos as $dato) {
				$total_porcentaje+= (float) $dato->getAttribute('porcentaje');
				$codigo_porcentajes[(int) $dato->getAttribute('codigo_id')]= (float) $dato->getAttribute('porcentaje');
				if ((float) $dato->getAttribute('porcentaje') > $codigo_porcentaje) {
					$codigo_mayor_total = (int) $dato->getAttribute('codigo_id');
					$codigo_porcentaje = (float) $dato->getAttribute('porcentaje');
				}
			}

			if ($total_porcentaje <> 100) {
				if ($total_porcentaje > 100) {
					$codigo_porcentajes[$codigo_mayor_total]-= ($total_porcentaje - 100);
				} elseif ($total_porcentaje < 100) {
					$codigo_porcentajes[$codigo_mayor_total]+= (100 - $total_porcentaje);
				}
			}
			foreach ( $conf_datos as $dato ) {
				$class = ++$index%2==0 ? ($this->extra["imprimir"]?'celdaIteracion2':'celdanegra15') : ($this->extra["imprimir"]?'celdaIteracion1':'celdanegra10') ;

				$conf_codigo = $xpath->query('//propiedades/codigos/codigo[@codigo_id='.$dato->getAttribute('codigo_id').']');

				$T->setVar('__evento', ($conf_codigo->length == 1)?($conf_codigo->item(0)->getAttribute('nombre')):'Desconocido');
				$T->setVar('__total_monitoreo', $dato->getAttribute('total_mediciones'));
				$T->setVar('__porcentaje', number_format($codigo_porcentajes[(int) $dato->getAttribute('codigo_id')], 2, ',', '.'));
				$T->setVar('__class', $class);
				$T->parse('lista_eventos', 'LISTA_EVENTOS', false);
				$T->parse('lista_pasos', 'LISTA_PASOS', true);
				$T->setVar('paso_descripcion', null);
			}
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		$this->resultado = $T->parse('out', 'tpl_tabla');
	}
	/**
	 * Creado por: Francisco Ormeño
	 * Modificado por: Francisco ORmeño
	 * Fecha ultima modificacion: 22/12/2016
	 */
	public function getEficienciaPorSegmento(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		$array_ponderados=array();
		$objetivo = new ConfigEspecial($this->objetivo_id);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($objetivo->__xml_config);
		$xpath = new DOMXpath($dom);

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB($this->extra["imprimir"]?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_eficiencia_por_segmento.tpl');

		$T->setBlock('tpl_tabla', 'BLOQUE_PONDERACION_ACUMULADO', 'bloque_ponderacion_acumulado');
		$T->setBlock('tpl_tabla', 'BLOQUE_PONDERACION_DIARIO', 'bloque_ponderacion_diario');
		$T->setBlock('tpl_tabla', 'BLOQUE_ACUMULADO_TOTAL', 'bloque_acumulado_total');
		$T->setBlock('tpl_tabla', 'BLOQUE_TOTAL_DIARIO', 'bloque_total_diario');
		$T->setBlock('tpl_tabla', 'BLOQUE_ACUMULADO', 'bloque_acumulado');
		$T->setBlock('tpl_tabla', 'BLOQUE_EFICIENCIA', 'bloque_eficiencia');
		$T->setBlock('tpl_tabla', 'BLOQUE_OBJETIVO', 'bloque_objetivo');
		$T->setBlock('tpl_tabla', 'BLOQUE_FECHA_EFICIENCIA', 'bloque_fecha_eficiencia');
		$T->setBlock('tpl_tabla', 'BLOQUE_SUB_SEGMENTO', 'bloque_sub_segmento');

		$conf_segmento = $xpath->query('//grupos/grupo[@id_segmento='.$this->extra["segmento_id"].']')->item(0);

		$titulo_horario=true;


		$dif_dias =((strtotime($this->timestamp->fecha_termino) - strtotime($this->timestamp->fecha_inicio))/86400);
		$conf_subsegmentos = $xpath->query('grupo',$conf_segmento);


		## Se recorren los subsegmentos encontrados en la configuración del reporte especial
		$T->setVar('bloque_sub_segmento', '');
		foreach ($conf_subsegmentos as $key => $conf_subsegmento){
			$linea=1;
			$arr_datos=array();
			$count_objetivos=0;

			$T->setVar('__nombre_subsegmento', $conf_subsegmento->getAttribute('nombre'));
			$tag_objetivos =$xpath->query('relacion',$conf_subsegmento);

			$T->setVar('__ponderacion',($conf_subsegmento->getAttribute('ponderacion')*100));

			## Se recorren los objetivos encontrados en la configuración del reporte especial
			$T->setVar('bloque_objetivo', '');
			foreach( $tag_objetivos as  $tag_objetivo) {
				$count_fechas=1;
				$promedio_diario=0;
				$promedio_acumulado=0;

				$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");

				$T->setVar('bloque_eficiencia', '');
				$T->setVar('bloque_acumulado', '');
				for ($i = 0; $i < $dif_dias; $i++) {

					$interval_fechaincio = "'$i day'::INTERVAL";
					$dia_siguiente=$i+1;
					$interval_fechatermino = "'$dia_siguiente day'::INTERVAL";
					$sql = "SELECT * FROM reporte.especial_eficiencia(".
							pg_escape_string($current_usuario_id).",".
							pg_escape_string($tag_objetivo->getAttribute('objetivo_id')).",3,0,'".
							pg_escape_string($this->timestamp->getInicioPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE + ".$interval_fechaincio.",'".
							pg_escape_string($this->timestamp->getInicioPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE + ".$interval_fechatermino.")";
// 										print($sql.'<br><br/>');
					$res =& $mdb2->query($sql);
					if (MDB2::isError($res)) {
						$log->setError($sql, $res->userinfo);
						exit();
					}

					if($row =$res->fetchRow()){
						$dom = new DomDocument();
						$dom->preserveWhiteSpace = FALSE;
						$dom->loadXML($row['especial_eficiencia']);
						$xpath2 = new DOMXpath($dom);
						unset($row["especial_eficiencia"]);
					}

					$conf_objetivo= $xpath2->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
					$paso_orden = $xpath2->evaluate("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]/@paso_orden[not(. < /atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]/@paso_orden)][1]")->item(0)->value;
					$conf_pasos = $xpath2->query("paso[@paso_orden=".$paso_orden."]", $conf_objetivo)->item(0);
					$tag_datos=$xpath2->query('//detalle[@paso_orden='.$paso_orden.']/datos/dato');
					$conf_parametros=$xpath2->query('//parametros')->item(0);


					if(!isset($arr_datos['fechas'])){
						$arr_datos['fechas']=array();
					}
					$fecha_inicio = date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')));
					$arr_datos['fechas'][$fecha_inicio]=$fecha_inicio;

					if ($tag_datos->length ==0) {
						$formatear_echa= date('m/d/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')));
						$dia_anterior=strtotime("-1 day",strtotime($formatear_echa)).'<br>';
						$dia_anterior = date('d/m/Y',$dia_anterior);


						if(isset($arr_datos[0][$tag_objetivo->getAttribute('objetivo_id')][$dia_anterior]['acumulado'])){
							$T->setVar('__acumulado',$arr_datos[0][$tag_objetivo->getAttribute('objetivo_id')][$dia_anterior]['acumulado']);
							$arr_datos[0][$tag_objetivo->getAttribute('objetivo_id')][$fecha_inicio]['acumulado']+=$arr_datos[0][$tag_objetivo->getAttribute('objetivo_id')][$dia_anterior]['acumulado'];
							$arr_datos[$fecha_inicio]['acumulado']+=$arr_datos[0][$tag_objetivo->getAttribute('objetivo_id')][$dia_anterior]['acumulado'];
						}else {
							$T->setVar('__acumulado','S/I');
						}
						$T->setVar('__eficiencia','S/I');


						$T->parse('bloque_eficiencia', 'BLOQUE_EFICIENCIA', true);
						$T->parse('bloque_acumulado', 'BLOQUE_ACUMULADO', true);


						if(!isset($arr_datos[1][$fecha_inicio]['cant_objetivo'])){
							$arr_datos[1][$fecha_inicio]['cant_objetivo']=0;
						}else {
							$arr_datos[1][$fecha_inicio]['cant_objetivo']=$arr_datos[1][$fecha_inicio]['cant_objetivo'];
							$arr_datos[1][$fecha_inicio]['diario']+=0;
						}
					}else {
						$count_fechas++;
						foreach ($tag_datos as $tag_dato){

							$T->setVar('__eficiencia',number_format($tag_dato->getAttribute('eficiencia'),2,",",null));
							$T->setVar('__acumulado',number_format($tag_dato->getAttribute('acumulado'),2,",",null));

							##se guardan los datos en un array para generar el total de eficiencia para cada sub segmento
							$arr_datos[0][$tag_objetivo->getAttribute('objetivo_id')][date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]['diario']+=$tag_dato->getAttribute('eficiencia');
							$arr_datos[0][$tag_objetivo->getAttribute('objetivo_id')][date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]['acumulado']+=$tag_dato->getAttribute('acumulado');
							$arr_datos[1][date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]['diario']+=$tag_dato->getAttribute('eficiencia');
							$arr_datos[1][date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]['acumulado']+=$tag_dato->getAttribute('acumulado');
							$arr_datos[1][date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]['cant_objetivo']+=1;
							$promedio_diario= $tag_dato->getAttribute('eficiencia');
							$promedio_acumulado=$tag_dato->getAttribute('acumulado');

							$T->parse('bloque_eficiencia', 'BLOQUE_EFICIENCIA', true);
							$T->parse('bloque_acumulado', 'BLOQUE_ACUMULADO', true);
						}
					}
				}

				##se genera el titulo de los dias seleccionados
				if($titulo_horario){
					$T->setVar('bloque_fecha_eficiencia', '');
					foreach ($arr_datos['fechas'] as $fecha){
						$T->setVar('_dia_eficiencia',$fecha);
						$T->parse('bloque_fecha_eficiencia', 'BLOQUE_FECHA_EFICIENCIA', true);

					}
				}

				$titulo_horario=false;

				$T->setVar('__objetivos',$conf_objetivo->getAttribute('nombre') );
				$T->setVar('__pasos',$conf_pasos->getAttribute('nombre'));
				$T->setVar('__promedio_eficiencia',number_format($promedio_diario,2,",",null));
				$T->setVar('__promedio_acumulado',number_format($promedio_acumulado,2,",",null));

				$linea++;

				$T->parse('bloque_objetivo', 'BLOQUE_OBJETIVO', true);
			}

			##se inicializan las variables de promedio del subsegmento
			$promedio_total_diario=0;
			$promedio_total_acumulado=0;

			##Se recorre por cada dia los datos de eficiencia para el subsegmento y se genera la sumatoria por dia de la eficiencia
			$T->setVar('bloque_total_diario', '');
			$T->setVar('bloque_acumulado_total', '');
			foreach ($arr_datos['fechas'] as $fecha){

				$_total_diario= $arr_datos[1][$fecha]['diario'];
				$_total_acumulado= $arr_datos[1][$fecha]['acumulado'];
				$_total_objetivos= ($arr_datos[1][$fecha]['cant_objetivo']==0)?1:$arr_datos[1][$fecha]['cant_objetivo'];

				$T->setVar('__diario_total',number_format(($_total_diario/$_total_objetivos),2,",",null));
				$T->setVar('__acumulado_total',number_format(($_total_acumulado/$_total_objetivos),2,",",null));

				$promedio_total_diario=($_total_diario/$_total_objetivos);
				$promedio_total_acumulado=($_total_acumulado/$_total_objetivos);

				## se agrega el ponderado de cada dia
				$array_ponderados[$fecha]['diario']+=($_total_diario/$_total_objetivos)*$conf_subsegmento->getAttribute('ponderacion');
				$array_ponderados[$fecha]['acumulado']+=($_total_acumulado/$_total_objetivos)*$conf_subsegmento->getAttribute('ponderacion');

				$T->parse('bloque_total_diario', 'BLOQUE_TOTAL_DIARIO', true);
				$T->parse('bloque_acumulado_total', 'BLOQUE_ACUMULADO_TOTAL', true);
			}


			$T->setVar('__promedio_diario_total',number_format($promedio_total_diario,2,",",null));
			$T->setVar('__promedio_acumulado_total',number_format($promedio_total_acumulado,2,",",null));
			$T->parse('bloque_sub_segmento', 'BLOQUE_SUB_SEGMENTO', true);

		}

		##se inicializan las variables de promedio para la ponderacion del segmento
		$promedio_ponderado_diario=0;
		$promedio_ponderado_acumulado=0;

		$T->setVar('bloque_ponderacion_diario', '');
		$T->setVar('bloque_ponderacion_acumulado', '');
		foreach ($arr_datos['fechas'] as $fecha){
			$_total_ponderado_diario= $array_ponderados[$fecha]['diario'];
			$_total_ponderado_acumulado= $array_ponderados[$fecha]['acumulado'];

			$T->setVar('__ponderacion_diario',number_format($_total_ponderado_diario,2,",",null));
			$T->setVar('__ponderacion_acumulado',number_format($_total_ponderado_acumulado,2,",",null));

			$promedio_ponderado_diario=$_total_ponderado_diario;
			$promedio_ponderado_acumulado=$_total_ponderado_acumulado;

			$T->parse('bloque_ponderacion_diario', 'BLOQUE_PONDERACION_DIARIO', true);
			$T->parse('bloque_ponderacion_acumulado', 'BLOQUE_PONDERACION_ACUMULADO', true);
		}

		$T->setVar('__promedio_ponderacion_diario',number_format($promedio_ponderado_diario,2,",",null));
		$T->setVar('__promedio_ponderacion_acumulado',number_format($promedio_ponderado_acumulado,2,",",null));


		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	public function getEficienciaPorHora(){
		global $usr;

		if ($this->extra["segmento_id"] == 1) {
			$this->resultado = $this->__getEficienciaPorHoraPublico();
		}
		else {
			$this->resultado = $this->__getEficienciaPorHoraPrivado();
		}

	}

	public function __getEficienciaPorHoraPublico(){

		global $mdb2;
		global $log;
		global $current_usuario_id;

		$objetivo = new ConfigEspecial($this->objetivo_id);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($objetivo->__xml_config);
		$xpath = new DOMXpath($dom);

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB($this->extra["imprimir"]?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_eficiencia_por_hora_publico.tpl');

		$T->setBlock('tpl_tabla', 'BLOQUE_PONDERACION', 'bloque_ponderacion');
		$T->setBlock('tpl_tabla', 'BLOQUE_EFICIENCIA', 'bloque_eficiencia');
		$T->setBlock('tpl_tabla', 'BLOQUE_OBJETIVO', 'bloque_objetivo');
		$T->setBlock('tpl_tabla', 'BLOQUE_HORA_EFICIENCIA', 'bloque_hora_eficiencia');

		if(!isset($arr_fechas['fechas'])){
			$arr_fechas['fechas']=array();
		}

		$T->setVar('bloque_hora_eficiencia', '');
		for($i=0;$i<24;$i++){
			$hora=($i<10)?'0'.$i.':00:00':$i.':00:00';
			$arr_fechas['fechas'][$hora]=$hora;
			$T->setVar('_hora_eficiencia',$hora);
			$T->parse('bloque_hora_eficiencia', 'BLOQUE_HORA_EFICIENCIA', true);

		}


		$linea=1;
		$arr_datos=array();
		$count_objetivos=0;
		$tag_objetivos =$xpath->query('//grupos/grupo[@id_segmento='.$this->extra["segmento_id"].']/objetivos/objetivo');

		$T->setVar('bloque_objetivo', '');
		foreach( $tag_objetivos as  $tag_objetivo) {
			$count_horas=1;
			$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");

			$sql = "SELECT * FROM reporte.especial_eficiencia(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($tag_objetivo->getAttribute('objetivo_id')).",1,0,'".
					pg_escape_string($this->timestamp->getInicioPeriodo())."','".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			// 				print($sql.'<br><br/>');
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			if($row =$res->fetchRow()){
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['especial_eficiencia']);
				$xpath2 = new DOMXpath($dom);
				unset($row["especial_eficiencia"]);
			}

			$promedio=0;
			$conf_objetivo= $xpath2->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$paso_orden = $xpath2->evaluate("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]/@paso_orden[not(. < /atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]/@paso_orden)][1]")->item(0)->value;
			$conf_pasos = $xpath2->query("paso[@paso_orden=".$paso_orden."]", $conf_objetivo)->item(0);
			$tag_datos=$xpath2->query('//detalle[@paso_orden='.$paso_orden.']/datos/dato');

			$T->setVar('bloque_eficiencia', '');
			foreach ($arr_fechas['fechas'] as $tag_fecha){
				$fecha=date('Y-m-d',strtotime($this->timestamp->getInicioPeriodo())).'T'.$tag_fecha;

				$tag_dato =$xpath2->query('//detalle[@paso_orden='.$paso_orden.']/datos/dato[@fecha="'.$fecha.'"]')->item(0);
				$T->setVar('__eficiencia',isset($tag_dato)?number_format($tag_dato->getAttribute('eficiencia'),2,",",null):'S/I');
				$arr_datos[$tag_fecha]['eficiencia']+=isset($tag_dato)?$tag_dato->getAttribute('eficiencia'):0;

				if(isset($tag_dato)){
					$arr_datos[$tag_fecha]['count_ob']+=1;
					$count_horas++;
				}else{
					$arr_datos[$tag_fecha]['count_ob']=1;

				}
				$promedio+= isset($tag_dato)?$tag_dato->getAttribute('eficiencia'):0;
				$T->parse('bloque_eficiencia', 'BLOQUE_EFICIENCIA', true);
			 }

			$T->setVar('__objetivos',$conf_objetivo->getAttribute('nombre') );
			$T->setVar('__pasos',$conf_pasos->getAttribute('nombre'));
			$T->setVar('__promedio_eficiencia',number_format(($promedio/($count_horas-1)),2,",",null));

			$T->parse('bloque_objetivo', 'BLOQUE_OBJETIVO', true);
			$count_objetivos++;
			$linea++;
		}

		$promedio_ponderado=0;
		$count_horas_promedio=1;
		$T->setVar('bloque_ponderacion', '');
		foreach ($arr_fechas['fechas'] as $ponderado){

			$_ponderado= $arr_datos[$ponderado]['eficiencia'];
			$_count_ob= $arr_datos[$ponderado]['count_ob'];
			if($_ponderado>0){
				$count_horas_promedio++;
			}
			$T->setVar('__ponderacion',number_format($_ponderado/$_count_ob,2,",",null));
			$promedio_ponderado+=$_ponderado/$count_objetivos;

			$T->parse('bloque_ponderacion', 'BLOQUE_PONDERACION', true);
		}

		$T->setVar('__promedio_ponderacion',number_format(($promedio_ponderado/($count_horas_promedio-1)),2,",",null));


		return $T->parse('out', 'tpl_tabla');
	}

	public function __getEficienciaPorHoraPrivado(){
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$objetivo = new ConfigEspecial($this->objetivo_id);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($objetivo->__xml_config);
		$xpath = new DOMXpath($dom);

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB($this->extra["imprimir"]?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_eficiencia_por_hora_privado.tpl');

		$T->setBlock('tpl_tabla', 'BLOQUE_PONDERACION', 'bloque_ponderacion');
		$T->setBlock('tpl_tabla', 'BLOQUE_EFICIENCIA', 'bloque_eficiencia');
		$T->setBlock('tpl_tabla', 'BLOQUE_OBJETIVO', 'bloque_objetivo');
		$T->setBlock('tpl_tabla', 'BLOQUE_HORA_EFICIENCIA', 'bloque_hora_eficiencia');
		$T->setBlock('tpl_tabla', 'BLOQUE_CATEGORIA', 'bloque_categoria');
		$T->setBlock('tpl_tabla', 'BLOQUE_SEGMENTO', 'bloque_segmento');


		## Se recorren los objetivos encontrados en la configuración del reporte especial
		$conf_subsegmentos = $xpath->query('//grupos/grupo[@id_segmento='.$this->extra["segmento_id"].']/grupo');


		if(!isset($arr_fechas['fechas'])){
			$arr_fechas['fechas']=array();
		}

		$T->setVar('bloque_hora_eficiencia', '');
		for($i=0;$i<24;$i++){
			$hora=($i<10)?'0'.$i.':00:00':$i.':00:00';
			$arr_fechas['fechas'][$hora]=$hora;
			$T->setVar('_hora_eficiencia',$hora);
			$T->parse('bloque_hora_eficiencia', 'BLOQUE_HORA_EFICIENCIA', true);

		}

		foreach ($conf_subsegmentos as $conf_subsegmento){
			$conf_categorias = $xpath->query('grupo',$conf_subsegmento);

			$T->setVar('__nombre_segmento', $conf_subsegmento->getAttribute('nombre'));

			$titulo_horario=true;

			$T->setVar('bloque_categoria', '');
			foreach ($conf_categorias as $conf_categoria){

				$T->setVar('__nombre_categoria', $conf_categoria->getAttribute('nombre'));
				$tag_objetivos =$xpath->query('relacion',$conf_categoria);
				$linea=1;
				$count_objetivos=0;
				$arr_datos=array();

				$T->setVar('bloque_objetivo', '');
				foreach( $tag_objetivos as  $tag_objetivo) {
					$count_horas=1;
					$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");

					$sql = "SELECT * FROM reporte.especial_eficiencia(".
									pg_escape_string($current_usuario_id).",".
									pg_escape_string($tag_objetivo->getAttribute('objetivo_id')).",1,0,'".
									pg_escape_string($this->timestamp->getInicioPeriodo())."','".
									pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
	// 				print($sql.'<br><br/>');
					$res =& $mdb2->query($sql);
					if (MDB2::isError($res)) {
						$log->setError($sql, $res->userinfo);
						exit();
					}

					if($row =$res->fetchRow()){
						$dom = new DomDocument();
						$dom->preserveWhiteSpace = FALSE;
						$dom->loadXML($row['especial_eficiencia']);
						$xpath2 = new DOMXpath($dom);
						unset($row["especial_eficiencia"]);
					}

					$promedio=0;
					$conf_objetivo= $xpath2->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
					$paso_orden = $xpath2->evaluate("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]/@paso_orden[not(. < /atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]/@paso_orden)][1]")->item(0)->value;
					$conf_pasos = $xpath2->query("paso[@paso_orden=".$paso_orden."]", $conf_objetivo)->item(0);
					$tag_datos=$xpath2->query('//detalle[@paso_orden='.$paso_orden.']/datos/dato');

					$T->setVar('bloque_eficiencia', '');
					foreach ($arr_fechas['fechas'] as $tag_fecha){

						$fecha=date('Y-m-d',strtotime($this->timestamp->getInicioPeriodo())).'T'.$tag_fecha;

						$tag_dato =$xpath2->query('//detalle[@paso_orden='.$paso_orden.']/datos/dato[@fecha="'.$fecha.'"]')->item(0);
						$T->setVar('__eficiencia',isset($tag_dato)?number_format($tag_dato->getAttribute('eficiencia'),2,",",null):'S/I');
						$arr_datos[$tag_fecha]['eficiencia']+=isset($tag_dato)?($tag_dato->getAttribute('eficiencia')):0;
						if(isset($tag_dato)){
							$arr_datos[$tag_fecha]['count_ob']+=1;
							$count_horas++;
						}else{
							$arr_datos[$tag_fecha]['count_ob']=1;
						}
						$promedio+= isset($tag_dato)?$tag_dato->getAttribute('eficiencia'):0;
						$T->parse('bloque_eficiencia', 'BLOQUE_EFICIENCIA', true);

					}

					$T->setVar('__objetivos',$conf_objetivo->getAttribute('nombre') );
					$T->setVar('__pasos',$conf_pasos->getAttribute('nombre'));
					$T->setVar('__promedio_eficiencia',number_format(($promedio/($count_horas-1)),2,",",null));

					$T->parse('bloque_objetivo', 'BLOQUE_OBJETIVO', true);
					$count_objetivos++;
					$linea++;
				}
				$promedio_ponderado=0;
				$count_horas_promedio=1;
				$T->setVar('bloque_ponderacion', '');
				foreach ($arr_fechas['fechas'] as $ponderado){
					$_ponderado= $arr_datos[$ponderado]['eficiencia'];
					$_count_ob= $arr_datos[$ponderado]['count_ob'];
					if($_ponderado>0){
						$count_horas_promedio++;
					}
					$T->setVar('__ponderacion',number_format($_ponderado/$_count_ob,2,",",null));
					$promedio_ponderado+=$_ponderado/$count_objetivos;

					$T->parse('bloque_ponderacion', 'BLOQUE_PONDERACION', true);
				}

				$T->setVar('__promedio_ponderacion',number_format(($promedio_ponderado/($count_horas_promedio-1)),2,",",null));
				$T->parse('bloque_categoria', 'BLOQUE_CATEGORIA', true);
			}

			$T->parse('bloque_segmento', 'BLOQUE_SEGMENTO', true);
		}
		return $T->parse('out', 'tpl_tabla');
	}


	public function getEspecialEficienciaEjecutivo() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$objetivo = new ConfigEspecial($this->objetivo_id);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($objetivo->__xml_config);
		$xpath = new DOMXpath($dom);

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB($this->extra["imprimir"]?REP_PATH_PRINTTEMPLATES:REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_eficiencia_ejecutivo.tpl');

		$T->setBlock('tpl_tabla', 'BLOQUE_NOMBRE_SUBSEGMENTO', 'bloque_nombre_subsegmento');
		$T->setBlock('tpl_tabla', 'BLOQUE_NOMBRE_SEGMENTO', 'bloque_nombre_segmento');
		$T->setBlock('tpl_tabla', 'BLOQUE_OBJETIVO', 'bloque_objetivo');
		$linea=1;

		## Se recorren los objetivos encontrados en la configuración del reporte especial
		$T->setVar('bloque_objetivo', '');
		foreach($xpath->query('//objetivos/objetivo') as  $tag_objetivo) {
			$T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");

			$sql = "SELECT * FROM reporte.especial_eficiencia(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($tag_objetivo->getAttribute('objetivo_id')).",3,0,'".
					pg_escape_string($this->timestamp->getInicioPeriodo())."','".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
// 			print($sql.'<br><br/>');
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
			if($row =$res->fetchRow()){
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['especial_eficiencia']);
				$xpath2 = new DOMXpath($dom);
				unset($row["especial_eficiencia"]);
			}

			$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado_por_paso(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($tag_objetivo->getAttribute('objetivo_id')).",0,'".
					pg_escape_string($this->timestamp->getInicioPeriodo())."','".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."', ".
					(isset($this->extra["variable"])?$usr->cliente_id:'0').")";
			//print($sql.'<br><br/>');

			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
			if($row =$res->fetchRow()){
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_resumen_consolidado_por_paso']);
				$xpath3 = new DOMXpath($dom);
				unset($row["disponibilidad_resumen_consolidado_por_paso"]);
			}

			$conf_objetivo= $xpath2->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$paso_orden = $xpath2->evaluate("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]/@paso_orden[not(. < /atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]/@paso_orden)][1]")->item(0)->value;
			$conf_pasos = $xpath2->query("paso[@paso_orden=".$paso_orden."]", $conf_objetivo)->item(0);
			$detalle_eficiencia=$xpath2->query("//detalle[@paso_orden=".$paso_orden."]/datos/dato")->item(0);
			$conf_nodos=$xpath3->query("//nodos/nodo[@nodo_id!=0]");

			$tag_downtime=0;

			foreach ($conf_nodos as $conf_nodo){
				$detalle_estadisticas = $xpath3->query("//detalle[@nodo_id=".$conf_nodo->getAttribute('nodo_id')."]/detalles/detalle[@paso_orden=".$paso_orden."]/estadisticas/estadistica");

				foreach ($detalle_estadisticas as $detalle_estadistica){
					$tag_downtime+= $detalle_estadistica->getAttribute('segundos');
				}
			}
			$tag_downtime=($tag_downtime>86400)?date('z:H:i:s',$tag_downtime):date('H:i:s',$tag_downtime) ;

			$T->setVar('__objetivos',$conf_objetivo->getAttribute('nombre') );
			$T->setVar('__pasos',$conf_pasos->getAttribute('nombre'));
			$T->setVar('__eficiencia',isset($detalle_eficiencia)?number_format($detalle_eficiencia->getAttribute('eficiencia'),2,",",null):"S/I");
			$T->setVar('__acumulado',isset($detalle_eficiencia)?number_format($detalle_eficiencia->getAttribute('acumulado'),2,",",null):"S/I");
			$T->setVar('__downtime',$tag_downtime);
			$linea++;
			$T->parse('bloque_objetivo', 'BLOQUE_OBJETIVO', true);
		}

		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	public function especialEficienciaPorCantidad() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$arr_datos=array();

		$objetivo = new ConfigEspecial($this->objetivo_id);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($objetivo->__xml_config);
		$xpath = new DOMXpath($dom);

		## Se recorren los objetivos encontrados en la configuración del reporte especial
		foreach($xpath->query('//grupos/grupo') as $key=> $tag_grupo) {

			$tag_objetivos = $xpath->query('relacion',$tag_grupo);
			foreach($tag_objetivos as  $tag_objetivo) {

				for ($i = 0; $i < 10; $i++) {

					if ((strtotime($this->timestamp->fecha_termino) - strtotime($this->timestamp->fecha_inicio)) > 86400) {
						$interval = "'$i month'::INTERVAL";
					}
					else {
						$interval = "'$i day'::INTERVAL";
					}

					$sql = "SELECT * FROM reporte.cantidad_global(".
					pg_escape_string($current_usuario_id).",".
							pg_escape_string($tag_objetivo->getAttribute('objetivo_id')).",".
							pg_escape_string($this->horario_id).", '".
							pg_escape_string($this->timestamp->getInicioPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.",'".
							pg_escape_string($this->timestamp->getTerminoPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.")";
									//  				print($sql.'<br><br/>');
						$res =& $mdb2->query($sql);
					if (MDB2::isError($res)) {
					$log->setError($sql, $res->userinfo);
					exit();
					}
					$row =$res->fetchRow();
					$dom = new DomDocument();
					$dom->preserveWhiteSpace = FALSE;
					$dom->loadXML($row['cantidad_global']);
					$xpath2 = new DOMXpath($dom);
					unset($row["cantidad_global"]);

					$conf_objetivo= $xpath2->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
					$conf_parametros=$xpath2->query('//parametros')->item(0);
					if(!isset($arr_datos['fechas'])){
						$arr_datos['fechas']=array();
					}
					$arr_datos['fechas'][date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]=date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')));

					foreach($xpath->query('relacion',$tag_objetivo) as  $tag_paso) {

						$tag_datos= $xpath2->query("//detalle[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]/datos/dato");
						$conf_paso = $xpath2->query("paso[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]", $conf_objetivo)->item(0);
						if(!isset($arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')])){
							$paso = new stdClass();
							$paso->nombre=$conf_paso->getAttribute('nombre');
							$paso->paso_orden=$conf_paso->getAttribute('paso_orden');
							$paso->fechas=array();
						}else{
							$paso=$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')];
						}
						if ($tag_datos->length > 0) {
							$datos= new stdClass();
							$datos->correctas=$tag_datos->item(0)->getAttribute('cantidad_ok');
							$datos->total=$tag_datos->item(0)->getAttribute('total');

							$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
							$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')] = $paso;

						}else{
							$datos= new stdClass();
							$datos->correctas="S/I";
							$datos->total="S/I";
							$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
							$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')] = $paso;
						}
					}
				}
			}
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_eficiencia_por_cantidad.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_TOTAL', 'bloque_total');
		$T->setBlock('tpl_tabla', 'BLOQUE_CORRECTO', 'bloque_correcto');
		$T->setBlock('tpl_tabla', 'BLOQUE_NOMBRE_CATEGORIA', 'bloque_nombre_categoria');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_CATEGORIA', 'bloque_categoria');
		$T->setBlock('tpl_tabla', 'BLOQUE_FECHA', 'bloque_fecha');

		$T->setVar('bloque_categoria', '');
		foreach($xpath->query('//grupos/grupo') as $key => $tag_grupo) {
			$tag_pasos_grupo = $xpath->query('relacion/relacion',$tag_grupo);
			$muestra_nombre = true;
			if(count($arr_datos[$key])>0){
				foreach ($arr_datos[$key] as $objetivo_id=>$objetivo_datos){
					$linea=0;
					$T->setVar('bloque_pasos', '');
					foreach ($objetivo_datos as $paso_orden=>$paso_datos){

						$T->setVar('bloque_nombre_categoria', '');
						$T->setVar('bloque_correcto', '');
						$T->setVar('bloque_total', '');
						$T->setVar('bloque_fecha', '');
						$T->setVar('__pasos', $paso_datos->nombre);

						foreach ($arr_datos['fechas'] as $tag_fecha_titulo){

							$T->setVar('__fecha_titulo', $tag_fecha_titulo);
							$T->parse('bloque_fecha', 'BLOQUE_FECHA', true);
							if ($muestra_nombre) {
								$T->setVar('__categoria', $tag_grupo->getAttribute('nombre'));
								$T->setVar('__rowspan_categoria', ($tag_pasos_grupo->length*2));
								$T->parse('bloque_nombre_categoria', 'BLOQUE_NOMBRE_CATEGORIA', false);
							}
							$muestra_nombre=false;
							$datos = $paso_datos->fechas[$tag_fecha_titulo];
							$T->setVar('__cantidad_ok',isset($datos)?$datos->correctas:'S/I');
							$T->setVar('__cantidad_totales',isset($datos)?$datos->total:'S/I');
							$T->parse('bloque_correcto', 'BLOQUE_CORRECTO', true);
							$T->parse('bloque_total', 'BLOQUE_TOTAL', true);
						}
						$linea++;
						$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
					}
					$T->parse('bloque_categoria', 'BLOQUE_CATEGORIA', true);
				}
				$this->resultado = $T->parse('out', 'tpl_tabla');
			}
		}
	}

	/**
	 * Creado Por: Francisco Ormeño
	 * Modificado Por:--
	 * Fecha creacion:10-01-2017
	 * Fecha ultima Modificacion:10-01-2017
	 */
	public function especialEficienciaPorCantidadFiltrada() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$arr_datos=array();

		$objetivo = new ConfigEspecial($this->objetivo_id);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($objetivo->__xml_config);
		$xpath = new DOMXpath($dom);

		## Se recorren los objetivos encontrados en la configuración del reporte especial
		foreach($xpath->query('//grupos/grupo') as $key=> $tag_grupo) {

		$tag_objetivos = $xpath->query('relacion',$tag_grupo);
		foreach($tag_objetivos as  $tag_objetivo) {

		for ($i = 0; $i < 10; $i++) {

		if ((strtotime($this->timestamp->fecha_termino) - strtotime($this->timestamp->fecha_inicio)) > 86400) {
		$interval = "'$i month'::INTERVAL";
		}
		else {
		$interval = "'$i day'::INTERVAL";
		}

		$sql = "SELECT * FROM reporte.cantidad_global_filtrada(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($tag_objetivo->getAttribute('objetivo_id')).",".
				pg_escape_string($this->horario_id).", '".
						pg_escape_string($this->timestamp->getInicioPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.",'".
						pg_escape_string($this->timestamp->getTerminoPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.")";
						//  				print($sql.'<br><br/>');
								$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
		$log->setError($sql, $res->userinfo);
		exit();
		}
		$row =$res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row['cantidad_global_filtrada']);
		$xpath2 = new DOMXpath($dom);
		unset($row["cantidad_global_filtrada"]);

		$conf_objetivo= $xpath2->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_parametros=$xpath2->query('//parametros')->item(0);
					if(!isset($arr_datos['fechas'])){
						$arr_datos['fechas']=array();
		}
						$arr_datos['fechas'][date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]=date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')));


						foreach($xpath->query('relacion',$tag_objetivo) as  $tag_paso) {

						$tag_datos= $xpath2->query("//detalle[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]/datos/dato");
			$conf_paso = $xpath2->query("paso[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]", $conf_objetivo)->item(0);
			if(!isset($arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')])){
			$paso = new stdClass();
			$paso->nombre=$conf_paso->getAttribute('nombre');
					$paso->paso_orden=$conf_paso->getAttribute('paso_orden');
					$paso->fechas=array();
			}else{
			$paso=$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')];
			}
			if ($tag_datos->length > 0) {
			$datos= new stdClass();
			$datos->correctas=$tag_datos->item(0)->getAttribute('cantidad_ok');
					$datos->total=$tag_datos->item(0)->getAttribute('total');

			$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
					$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')] = $paso;

		}else{
		$datos= new stdClass();
		$datos->correctas="S/I";
		$datos->total="S/I";
		$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
		$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')] = $paso;
			}
			}
			}
			}
			}

			/* TEMPLATE DEL GRAFICO */
			$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
			$T->setFile('tpl_tabla', 'especial_eficiencia_por_cantidad.tpl');
					$T->setBlock('tpl_tabla', 'BLOQUE_TOTAL', 'bloque_total');
			$T->setBlock('tpl_tabla', 'BLOQUE_CORRECTO', 'bloque_correcto');
			$T->setBlock('tpl_tabla', 'BLOQUE_NOMBRE_CATEGORIA', 'bloque_nombre_categoria');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_CATEGORIA', 'bloque_categoria');
			$T->setBlock('tpl_tabla', 'BLOQUE_FECHA', 'bloque_fecha');

		$T->setVar('bloque_categoria', '');
			foreach($xpath->query('//grupos/grupo') as $key => $tag_grupo) {
			$tag_pasos_grupo = $xpath->query('relacion/relacion',$tag_grupo);
			$muestra_nombre = true;
			if(count($arr_datos[$key])>0){
			foreach ($arr_datos[$key] as $objetivo_id=>$objetivo_datos){
			$linea=0;
					$T->setVar('bloque_pasos', '');
					foreach ($objetivo_datos as $paso_orden=>$paso_datos){

					$T->setVar('bloque_nombre_categoria', '');
					$T->setVar('bloque_correcto', '');
					$T->setVar('bloque_total', '');
					$T->setVar('bloque_fecha', '');
					$T->setVar('__pasos', $paso_datos->nombre);


					foreach ($arr_datos['fechas'] as $tag_fecha_titulo){

							$T->setVar('__fecha_titulo', $tag_fecha_titulo);
										$T->parse('bloque_fecha', 'BLOQUE_FECHA', true);
			if ($muestra_nombre) {
			$T->setVar('__categoria', $tag_grupo->getAttribute('nombre'));
			$T->setVar('__rowspan_categoria', ($tag_pasos_grupo->length*2));
				$T->parse('bloque_nombre_categoria', 'BLOQUE_NOMBRE_CATEGORIA', false);
			}
							$muestra_nombre=false;
								$datos = $paso_datos->fechas[$tag_fecha_titulo];
								$T->setVar('__cantidad_ok',isset($datos)?$datos->correctas:'S/I');
										$T->setVar('__cantidad_totales',isset($datos)?$datos->total:'S/I');
												$T->parse('bloque_correcto', 'BLOQUE_CORRECTO', true);
												$T->parse('bloque_total', 'BLOQUE_TOTAL', true);
			}
			$linea++;
			$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
			}
					$T->parse('bloque_categoria', 'BLOQUE_CATEGORIA', true);
			}
			$this->resultado = $T->parse('out', 'tpl_tabla');
			}
		}
	}

	public function especialEficienciaPorPorcentaje() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		$orden=0;
		$arr_datos=array();


		$objetivo = new ConfigEspecial($this->objetivo_id);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($objetivo->__xml_config);
		$xpath = new DOMXpath($dom);

		## Se recorren los objetivos encontrados en la configuración del reporte especial
		foreach($xpath->query('//grupos/grupo') as $key=> $tag_grupo) {

			$tag_objetivos = $xpath->query('relacion',$tag_grupo);
			foreach($tag_objetivos as  $tag_objetivo) {

				for ($i = 0; $i < 10; $i++) {

					if ((strtotime($this->timestamp->fecha_termino) - strtotime($this->timestamp->fecha_inicio)) > 86400) {
						$interval = "'$i month'::INTERVAL";
					}
					else {
						$interval = "'$i day'::INTERVAL";
					}

					$sql = "SELECT * FROM reporte.cantidad_global(".
							pg_escape_string($current_usuario_id).",".
							pg_escape_string($tag_objetivo->getAttribute('objetivo_id')).",".
							pg_escape_string($this->horario_id).", '".
							pg_escape_string($this->timestamp->getInicioPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.",'".
							pg_escape_string($this->timestamp->getTerminoPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.")";
					//  				print($sql.'<br><br/>');
					$res =& $mdb2->query($sql);
					if (MDB2::isError($res)) {
						$log->setError($sql, $res->userinfo);
						exit();
					}
					$row =$res->fetchRow();
					$dom = new DomDocument();
					$dom->preserveWhiteSpace = FALSE;
					$dom->loadXML($row['cantidad_global']);
					$xpath2 = new DOMXpath($dom);
					unset($row["cantidad_global"]);

					$conf_objetivo= $xpath2->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
					$conf_parametros=$xpath2->query('//parametros')->item(0);
					if(!isset($arr_datos['fechas'])){
						$arr_datos['fechas']=array();
					}
					$arr_datos['fechas'][date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]=date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')));
					foreach($xpath->query('relacion',$tag_objetivo) as  $tag_paso) {

						$tag_datos= $xpath2->query("//detalle[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]/datos/dato");
						$conf_paso = $xpath2->query("paso[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]", $conf_objetivo)->item(0);
						if(!isset($arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')])){
							$paso = new stdClass();
							$paso->nombre=$conf_paso->getAttribute('nombre');
							$paso->paso_orden=$conf_paso->getAttribute('paso_orden');
							$paso->fechas=array();

						}else{
							$paso=$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')];
						}

						if ($tag_datos->length > 0) {
							$datos= new stdClass();
							$datos->porcentaje=$tag_datos->item(0)->getAttribute('porcentaje');
							$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
							$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')] = $paso;
						}else{
							$datos= new stdClass();
							$datos->porcentaje="S/I";
							$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
							$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')] = $paso;
						}
					}
				}
			}
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_eficiencia_porcentual.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_PORCENTAJE', 'bloque_porcentaje');
		$T->setBlock('tpl_tabla', 'BLOQUE_NOMBRE_CATEGORIA', 'bloque_nombre_categoria');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_CATEGORIA', 'bloque_categoria');
		$T->setBlock('tpl_tabla', 'BLOQUE_FECHA', 'bloque_fecha');

		$T->setVar('bloque_categoria', '');
		foreach($xpath->query('//grupos/grupo') as $key => $tag_grupo) {
			$tag_pasos_grupo = $xpath->query('relacion/relacion',$tag_grupo);
			$muestra_nombre = true;
			if(count($arr_datos[$key])>0){
				foreach ($arr_datos[$key] as $objetivo_id=>$objetivo_datos){
					$T->setVar('bloque_pasos', '');
					foreach ($objetivo_datos as $paso_orden=>$paso_datos){

						$T->setVar('bloque_nombre_categoria', '');
						$T->setVar('bloque_porcentaje', '');
						$T->setVar('bloque_total', '');
						$T->setVar('__pasos', $paso_datos->nombre);
						$T->setVar('__print_class', ($orden % 2 == 0)?"celdaIteracion1":"celdaIteracion2");

						$T->setVar('bloque_fecha', '');
						foreach ($arr_datos['fechas'] as $tag_fecha_titulo){

							$T->setVar('__fecha_titulo', $tag_fecha_titulo);
							$T->parse('bloque_fecha', 'BLOQUE_FECHA', true);
							if ($muestra_nombre) {
								$T->setVar('__categoria', $tag_grupo->getAttribute('nombre'));
								$T->setVar('__rowspan_categoria', $tag_pasos_grupo->length);
								$T->parse('bloque_nombre_categoria', 'BLOQUE_NOMBRE_CATEGORIA', false);
							}
							$muestra_nombre=false;
							$datos=$paso_datos->fechas[$tag_fecha_titulo];
							$porcentaje= $datos->porcentaje!="S/I"?number_format($datos->porcentaje,2,",",null):$datos->porcentaje;
							$T->setVar('__porcentaje',$porcentaje);
							$T->parse('bloque_porcentaje', 'BLOQUE_PORCENTAJE', true);
						}
						$orden++;
						$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
					}
					$T->parse('bloque_categoria', 'BLOQUE_CATEGORIA', true);
				}
				$this->resultado = $T->parse('out', 'tpl_tabla');
			}
		}

	}

	/**
	 * Creado Por: Francisco Ormeño
	 * Modificado Por:--
	 * Fecha creacion:10-01-2017
	 * Fecha ultima Modificacion:10-01-2017
	 */
	public function especialEficienciaPorPorcentajeFiltrada() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		$orden=0;
		$arr_datos=array();

		$objetivo = new ConfigEspecial($this->objetivo_id);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($objetivo->__xml_config);
		$xpath = new DOMXpath($dom);

		## Se recorren los objetivos encontrados en la configuración del reporte especial
		foreach($xpath->query('//grupos/grupo') as $key=> $tag_grupo) {

			$tag_objetivos = $xpath->query('relacion',$tag_grupo);
			foreach($tag_objetivos as  $tag_objetivo) {

					for ($i = 0; $i < 10; $i++) {

						if ((strtotime($this->timestamp->fecha_termino) - strtotime($this->timestamp->fecha_inicio)) > 86400) {
							$interval = "'$i month'::INTERVAL";
						}
						else {
							$interval = "'$i day'::INTERVAL";
						}

					$sql = "SELECT * FROM reporte.cantidad_global_filtrada(".
							pg_escape_string($current_usuario_id).",".
							pg_escape_string($tag_objetivo->getAttribute('objetivo_id')).",".
							pg_escape_string($this->horario_id).", '".
							pg_escape_string($this->timestamp->getInicioPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.",'".
							pg_escape_string($this->timestamp->getTerminoPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.")";
	//  				print($sql.'<br><br/>');
					$res =& $mdb2->query($sql);
					if (MDB2::isError($res)) {
						$log->setError($sql, $res->userinfo);
						exit();
					}
					$row =$res->fetchRow();
					$dom = new DomDocument();
					$dom->preserveWhiteSpace = FALSE;
					$dom->loadXML($row['cantidad_global_filtrada']);
					$xpath2 = new DOMXpath($dom);
					unset($row["cantidad_global_filtrada"]);

					$conf_objetivo= $xpath2->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
					$conf_parametros=$xpath2->query('//parametros')->item(0);
					if(!isset($arr_datos['fechas'])){
						$arr_datos['fechas']=array();
					}
					$arr_datos['fechas'][date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]=date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')));
					foreach($xpath->query('relacion',$tag_objetivo) as  $tag_paso) {

						$tag_datos= $xpath2->query("//detalle[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]/datos/dato");
						$conf_paso = $xpath2->query("paso[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]", $conf_objetivo)->item(0);
						if(!isset($arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')])){
							$paso = new stdClass();
							$paso->nombre=$conf_paso->getAttribute('nombre');
							$paso->paso_orden=$conf_paso->getAttribute('paso_orden');
							$paso->fechas=array();

						}else{
							$paso=$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')];
						}

						if ($tag_datos->length > 0) {
							$datos= new stdClass();
							$datos->porcentaje=$tag_datos->item(0)->getAttribute('porcentaje');
							$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
							$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')] = $paso;
						}else{
							$datos= new stdClass();
							$datos->porcentaje="S/I";
							$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
							$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')] = $paso;
						}
					}
				}
			}
		}
		
		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_eficiencia_porcentual.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_PORCENTAJE', 'bloque_porcentaje');
		$T->setBlock('tpl_tabla', 'BLOQUE_NOMBRE_CATEGORIA', 'bloque_nombre_categoria');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_CATEGORIA', 'bloque_categoria');
		$T->setBlock('tpl_tabla', 'BLOQUE_FECHA', 'bloque_fecha');

		$T->setVar('bloque_categoria', '');
		foreach($xpath->query('//grupos/grupo') as $key => $tag_grupo) {
			$tag_pasos_grupo = $xpath->query('relacion/relacion',$tag_grupo);
			$muestra_nombre = true;
			if(count($arr_datos[$key])>0){
				foreach ($arr_datos[$key] as $objetivo_id=>$objetivo_datos){
					$T->setVar('bloque_pasos', '');
					foreach ($objetivo_datos as $paso_orden=>$paso_datos){

						$T->setVar('bloque_nombre_categoria', '');
						$T->setVar('bloque_porcentaje', '');
						$T->setVar('bloque_total', '');
						$T->setVar('__pasos', $paso_datos->nombre);
						$T->setVar('__print_class', ($orden % 2 == 0)?"celdaIteracion1":"celdaIteracion2");

						$T->setVar('bloque_fecha', '');
						foreach ($arr_datos['fechas'] as $tag_fecha_titulo){

							$T->setVar('__fecha_titulo', $tag_fecha_titulo);
							$T->parse('bloque_fecha', 'BLOQUE_FECHA', true);
							if ($muestra_nombre) {
								$T->setVar('__categoria', $tag_grupo->getAttribute('nombre'));
								$T->setVar('__rowspan_categoria', $tag_pasos_grupo->length);
								$T->parse('bloque_nombre_categoria', 'BLOQUE_NOMBRE_CATEGORIA', false);
							}
							$muestra_nombre=false;
							$datos=$paso_datos->fechas[$tag_fecha_titulo];
							$porcentaje= $datos->porcentaje!="S/I"?number_format($datos->porcentaje,2,",",null):$datos->porcentaje;
							$T->setVar('__porcentaje',$porcentaje);
							$T->parse('bloque_porcentaje', 'BLOQUE_PORCENTAJE', true);
						}
						$orden++;
						$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
					}
					$T->parse('bloque_categoria', 'BLOQUE_CATEGORIA', true);
				}
				$this->resultado = $T->parse('out', 'tpl_tabla');
			}
		}
	}

	public function especialEficienciaPorPonderacion(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		$orden=0;
		$arr_datos=array();


		$objetivo = new ConfigEspecial($this->objetivo_id);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($objetivo->__xml_config);
		$xpath = new DOMXpath($dom);

		## Se recorren los objetivos encontrados en la configuración del reporte especial
		foreach($xpath->query('//grupos/grupo') as $key=> $tag_grupo) {
			$tag_objetivos = $xpath->query('relacion',$tag_grupo);
			foreach($tag_objetivos as  $tag_objetivo) {

				for ($i = 0; $i <10; $i++) {

					if ((strtotime($this->timestamp->fecha_termino) - strtotime($this->timestamp->fecha_inicio)) > 86400) {
						$interval = "'$i month'::INTERVAL";
					}
					else {
						$interval = "'$i day'::INTERVAL";
					}

					$sql = "SELECT * FROM reporte.cantidad_global(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($tag_objetivo->getAttribute('objetivo_id')).",".
					pg_escape_string($this->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.",'".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.")";
// 					print($sql.'<br><br/>');
					$res =& $mdb2->query($sql);
					if (MDB2::isError($res)) {
						$log->setError($sql, $res->userinfo);
						exit();
					}
					if ($row = $res->fetchRow()) {
						$dom = new DomDocument();
						$dom->preserveWhiteSpace = FALSE;
						$dom->loadXML($row['cantidad_global']);
						$xpath2 = new DOMXpath($dom);
						unset($row["cantidad_global"]);
					}
						$conf_objetivo= $xpath2->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
						$conf_parametros=$xpath2->query('//parametros')->item(0);
						if(!isset($arr_datos['fechas'])){
							$arr_datos['fechas']=array();
						}
						$arr_datos['fechas'][date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]=date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')));
						foreach($xpath->query('relacion',$tag_objetivo) as  $tag_paso) {

							$tag_datos= $xpath2->query("//detalle[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]/datos/dato");
							$conf_paso = $xpath2->query("paso[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]", $conf_objetivo)->item(0);
							if(!isset($arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')])){
								$paso = new stdClass();
								$paso->nombre=$conf_paso->getAttribute('nombre');
								$paso->paso_orden=$conf_paso->getAttribute('paso_orden');
								$paso->ponderacion=$tag_paso->getAttribute('ponderacion');
								$paso->fechas=array();

							}else{
								$paso=$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')];
							}

							if ($tag_datos->length > 0) {
								$datos= new stdClass();
								$datos->porcentaje=$tag_datos->item(0)->getAttribute('porcentaje');
								$datos->correctas=$tag_datos->item(0)->getAttribute('cantidad_ok');
								$datos->total=$tag_datos->item(0)->getAttribute('total');
								$conf_ponderacion=$xpath->query("relacion",$tag_paso);
								$valida_ponderacion=false;
								if($conf_ponderacion->length>0){
									foreach ($conf_ponderacion as $tag_ponderacion){
										if(date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))>=$tag_ponderacion->getattribute('fecha_inicio')
										&& date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))<=$tag_ponderacion->getattribute('fecha_termino')){
											if(!$valida_ponderacion){
												$datos->ponderacion =$tag_ponderacion->getAttribute('valor_ponderacion');
												$valida_ponderacion=true;
											}
										}
									}
									if(!isset($datos->ponderacion)){
										$datos->ponderacion =$tag_paso->getAttribute('ponderacion');
									}
								}else {
									$datos->ponderacion =$tag_paso->getAttribute('ponderacion');
								}
								$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
								$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')] = $paso;
							}else {
								$datos= new stdClass();
								$datos->porcentaje="S/I";
								$datos->correctas="S/I";
								$datos->total="S/I";
								$datos->ponderacion ="S/I";
								$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
								$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')] = $paso;
							}
						}
				}
			}
		}


		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_eficiencia_ponderado.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_PONDERACION_GLOBAL', 'bloque_ponderacion_global');
		$T->setBlock('tpl_tabla', 'BLOQUE_PONDERACION', 'bloque_ponderacion');
		$T->setBlock('tpl_tabla', 'BLOQUE_CATEGORIA', 'bloque_categoria');
		$T->setBlock('tpl_tabla', 'BLOQUE_FECHA', 'bloque_fecha');

		$T->setVar('bloque_categoria', '');
		$T->setVar('bloque_ponderacion_global', '');
		$ponderacion_global=array();
		foreach($xpath->query('//grupos/grupo') as $key => $tag_grupo) {
			if(count($arr_datos[$key])>0){
				$tag_pasos_grupo = $xpath->query('relacion/relacion',$tag_grupo);
				$T->setVar('__categoria', $tag_grupo->getAttribute('nombre'));
				$T->setVar('__ponderacion',number_format(($tag_grupo->getAttribute('ponderacion')*100),2,",",null)."%");
				$T->setVar('__print_class', ($orden % 2 == 0)?"celdaIteracion1":"celdaIteracion2");
				$T->setVar('bloque_ponderacion', '');

				$T->setVar('bloque_fecha', '');
				foreach ($arr_datos['fechas'] as $tag_fecha_titulo){
					if (!isset($ponderacion_global[$tag_fecha_titulo])) {
						$ponderacion_global[$tag_fecha_titulo] = 0;
					}
					$ponderacion_total=0;
					$T->setVar('__fecha_titulo', $tag_fecha_titulo);
					foreach ($arr_datos[$key] as $objetivo_id=>$objetivo_datos){
						foreach ($objetivo_datos as $paso_orden=>$paso_datos){
							$datos=$paso_datos->fechas[$tag_fecha_titulo];
							$porcentaje=$datos->porcentaje=='S/I'?0:$datos->porcentaje;
							$ponderacion=$datos->porcentaje=='S/I'?0:$datos->ponderacion;
							$ponderacion_total+=$porcentaje*$ponderacion;
						}
					}
					$T->setVar('__valor_ponderacion',isset($ponderacion_total)?number_format($ponderacion_total,2,",",null).'%':'S/I');
 					$ponderacion_global[$tag_fecha_titulo]+=$ponderacion_total*$tag_grupo->getAttribute('ponderacion');
					$T->parse('bloque_fecha', 'BLOQUE_FECHA', true);
					$T->parse('bloque_ponderacion', 'BLOQUE_PONDERACION', true);
				}
				$orden++;
				$T->parse('bloque_categoria', 'BLOQUE_CATEGORIA', true);
			}
		}
		foreach ($arr_datos['fechas'] as $tag_fecha_titulo){
			$T->setVar('__valor_ponderacion_global',isset($ponderacion_global[$tag_fecha_titulo])?number_format($ponderacion_global[$tag_fecha_titulo],2,",",null).'%':'S/I');
			$T->parse('bloque_ponderacion_global', 'BLOQUE_PONDERACION_GLOBAL', true);
		}

		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	/**
	 * Creado Por: Francisco Ormeño
	 * Modificado Por:--
	 * Fecha creacion:10-01-2017
	 * Fecha ultima Modificacion:10-01-2017
	 */

	public function especialEficienciaPorPonderacionFiltrada(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		$orden=0;
		$arr_datos=array();


		$objetivo = new ConfigEspecial($this->objetivo_id);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($objetivo->__xml_config);
		$xpath = new DOMXpath($dom);

		## Se recorren los objetivos encontrados en la configuración del reporte especial
		foreach($xpath->query('//grupos/grupo') as $key=> $tag_grupo) {
		$tag_objetivos = $xpath->query('relacion',$tag_grupo);
		foreach($tag_objetivos as  $tag_objetivo) {

		for ($i = 0; $i <10; $i++) {

		if ((strtotime($this->timestamp->fecha_termino) - strtotime($this->timestamp->fecha_inicio)) > 86400) {
		$interval = "'$i month'::INTERVAL";
		}
		else {
		$interval = "'$i day'::INTERVAL";
		}

			$sql = "SELECT * FROM reporte.cantidad_global_filtrada(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($tag_objetivo->getAttribute('objetivo_id')).",".
					pg_escape_string($this->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.",'".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.")";
// 					print($sql.'<br><br/>');
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
			if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['cantidad_global_filtrada']);
			$xpath2 = new DOMXpath($dom);
			unset($row["cantidad_global_filtrada"]);
					}
						$conf_objetivo= $xpath2->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
						$conf_parametros=$xpath2->query('//parametros')->item(0);
						if(!isset($arr_datos['fechas'])){
							$arr_datos['fechas']=array();
			}
			$arr_datos['fechas'][date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]=date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')));
			foreach($xpath->query('relacion',$tag_objetivo) as  $tag_paso) {

			$tag_datos= $xpath2->query("//detalle[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]/datos/dato");
							$conf_paso = $xpath2->query("paso[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]", $conf_objetivo)->item(0);
							if(!isset($arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')])){
							$paso = new stdClass();
							$paso->nombre=$conf_paso->getAttribute('nombre');
							$paso->paso_orden=$conf_paso->getAttribute('paso_orden');
							$paso->ponderacion=$tag_paso->getAttribute('ponderacion');
							$paso->fechas=array();


							}else{
							$paso=$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')];
							}

							if ($tag_datos->length > 0) {
							$datos= new stdClass();
							$datos->porcentaje=$tag_datos->item(0)->getAttribute('porcentaje');
							$datos->correctas=$tag_datos->item(0)->getAttribute('cantidad_ok');
							$datos->total=$tag_datos->item(0)->getAttribute('total');
							$conf_ponderacion=$xpath->query("relacion",$tag_paso);
									$valida_ponderacion=false;
									if($conf_ponderacion->length>0){
									foreach ($conf_ponderacion as $tag_ponderacion){
									if(date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))>=$tag_ponderacion->getattribute('fecha_inicio')
									&& date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))<=$tag_ponderacion->getattribute('fecha_termino')){
											if(!$valida_ponderacion){
											$datos->ponderacion =$tag_ponderacion->getAttribute('valor_ponderacion');
											$valida_ponderacion=true;
									}
									}
									}
										if(!isset($datos->ponderacion)){
										$datos->ponderacion =$tag_paso->getAttribute('ponderacion');
									}
									}else {
									$datos->ponderacion =$tag_paso->getAttribute('ponderacion');
									}
									$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
									$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')] = $paso;
							}else {
							$datos= new stdClass();
							$datos->porcentaje="S/I";
							$datos->correctas="S/I";
									$datos->total="S/I";
									$datos->ponderacion ="S/I";
									$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
									$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')][$tag_paso->getAttribute('paso_orden')] = $paso;
									}
									}
			}
			}
		}

			/* TEMPLATE DEL GRAFICO */
			$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
					$T->setFile('tpl_tabla', 'especial_eficiencia_ponderado.tpl');
			$T->setBlock('tpl_tabla', 'BLOQUE_PONDERACION_GLOBAL', 'bloque_ponderacion_global');
			$T->setBlock('tpl_tabla', 'BLOQUE_PONDERACION', 'bloque_ponderacion');
		$T->setBlock('tpl_tabla', 'BLOQUE_CATEGORIA', 'bloque_categoria');
			$T->setBlock('tpl_tabla', 'BLOQUE_FECHA', 'bloque_fecha');

		$T->setVar('bloque_categoria', '');
					$T->setVar('bloque_ponderacion_global', '');
							$ponderacion_global=array();
							foreach($xpath->query('//grupos/grupo') as $key => $tag_grupo) {
			if(count($arr_datos[$key])>0){
				$tag_pasos_grupo = $xpath->query('relacion/relacion',$tag_grupo);
					$T->setVar('__categoria', $tag_grupo->getAttribute('nombre'));
					$T->setVar('__ponderacion',number_format(($tag_grupo->getAttribute('ponderacion')*100),2,",",null)."%");
					$T->setVar('__print_class', ($orden % 2 == 0)?"celdaIteracion1":"celdaIteracion2");
					$T->setVar('bloque_ponderacion', '');
  
				$T->setVar('bloque_fecha', '');
			foreach ($arr_datos['fechas'] as $tag_fecha_titulo){
					if (!isset($ponderacion_global[$tag_fecha_titulo])) {
						$ponderacion_global[$tag_fecha_titulo] = 0;
			}
			$ponderacion_total=0;
			$T->setVar('__fecha_titulo', $tag_fecha_titulo);
			foreach ($arr_datos[$key] as $objetivo_id=>$objetivo_datos){
			foreach ($objetivo_datos as $paso_orden=>$paso_datos){
			$datos=$paso_datos->fechas[$tag_fecha_titulo];
			$porcentaje=$datos->porcentaje=='S/I'?0:$datos->porcentaje;
			$ponderacion=$datos->porcentaje=='S/I'?0:$datos->ponderacion;
			$ponderacion_total+=$porcentaje*$ponderacion;
			}
			}
			$T->setVar('__valor_ponderacion',isset($ponderacion_total)?number_format($ponderacion_total,2,",",null).'%':'S/I');
					$ponderacion_global[$tag_fecha_titulo]+=$ponderacion_total*$tag_grupo->getAttribute('ponderacion');
					$T->parse('bloque_fecha', 'BLOQUE_FECHA', true);
					$T->parse('bloque_ponderacion', 'BLOQUE_PONDERACION', true);
					}
					$orden++;
					$T->parse('bloque_categoria', 'BLOQUE_CATEGORIA', true);
					}
		}
		foreach ($arr_datos['fechas'] as $tag_fecha_titulo){
			$T->setVar('__valor_ponderacion_global',isset($ponderacion_global[$tag_fecha_titulo])?number_format($ponderacion_global[$tag_fecha_titulo],2,",",null).'%':'S/I');
			$T->parse('bloque_ponderacion_global', 'BLOQUE_PONDERACION_GLOBAL', true);
		}

		$this->resultado = $T->parse('out', 'tpl_tabla');
			}



	public function especialEficienciaPorCantidadObjetivo(){
	global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$arr_datos=array();

		$objetivo = new ConfigEspecial($this->objetivo_id);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($objetivo->__xml_config);
		$xpath = new DOMXpath($dom);

		## Se recorren los objetivos encontrados en la configuración del reporte especial
		foreach($xpath->query('//grupos/grupo') as $key=> $tag_grupo) {

			$tag_objetivos = $xpath->query('relacion',$tag_grupo);
			foreach($tag_objetivos as  $tag_objetivo) {

				for ($i = 0; $i < 10; $i++) {

					if ((strtotime($this->timestamp->fecha_termino) - strtotime($this->timestamp->fecha_inicio)) > 86400) {
						$interval = "'$i month'::INTERVAL";
					}
					else {
						$interval = "'$i day'::INTERVAL";
					}

					$sql = "SELECT * FROM reporte.cantidad_global(".
					pg_escape_string($current_usuario_id).",".
							pg_escape_string($tag_objetivo->getAttribute('objetivo_id')).",".
							pg_escape_string($this->horario_id).", '".
							pg_escape_string($this->timestamp->getInicioPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.",'".
							pg_escape_string($this->timestamp->getTerminoPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.")";
									//  				print($sql.'<br><br/>');
						$res =& $mdb2->query($sql);
					if (MDB2::isError($res)) {
						$log->setError($sql, $res->userinfo);
						exit();
					}
					if($row =$res->fetchRow()){
						$dom = new DomDocument();
						$dom->preserveWhiteSpace = FALSE;
						$dom->loadXML($row['cantidad_global']);
						$xpath2 = new DOMXpath($dom);
						unset($row["cantidad_global"]);
					}
					$conf_objetivo= $xpath2->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
					$conf_parametros=$xpath2->query('//parametros')->item(0);
					if(!isset($arr_datos['fechas'])){
						$arr_datos['fechas']=array();
					}
					$arr_datos['fechas'][date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]=date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')));

					foreach($xpath->query('relacion',$tag_objetivo) as  $tag_paso) {

						if(!isset($arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')])){
							$subObjetivo = new stdClass();
							$subObjetivo->nombre=$conf_objetivo->getAttribute('nombre');
							$subObjetivo->pasos=array();
						}else{
							$paso=$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')];
						}

						$tag_datos= $xpath2->query("//detalle[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]/datos/dato");
						$conf_paso = $xpath2->query("paso[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]", $conf_objetivo)->item(0);
						if(!isset($arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')]->pasos[$tag_paso->getAttribute('paso_orden')])){
							$paso = new stdClass();
							$paso->nombre=$conf_paso->getAttribute('nombre');
							$paso->paso_orden=$conf_paso->getAttribute('paso_orden');
							$paso->fechas=array();
						}else{
							$paso=$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')]->pasos[$tag_paso->getAttribute('paso_orden')];
						}
						if ($tag_datos->length > 0) {
							$datos= new stdClass();
							$datos->correctas=$tag_datos->item(0)->getAttribute('cantidad_ok');
							$datos->total=$tag_datos->item(0)->getAttribute('total');

							$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
							$subObjetivo->pasos[$tag_paso->getAttribute('paso_orden')]=$paso;
							$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')] = $subObjetivo;

						}else{
							$datos= new stdClass();
							$datos->correctas="S/I";
							$datos->total="S/I";
							$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
							$subObjetivo->pasos[$tag_paso->getAttribute('paso_orden')]=$paso;
							$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')] = $subObjetivo;
						}
					}
				}
			}
		}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_eficiencia_por_cantidad_por_objetivo.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_TOTAL', 'bloque_total');
		$T->setBlock('tpl_tabla', 'BLOQUE_CORRECTO', 'bloque_correcto');
		$T->setBlock('tpl_tabla', 'BLOQUE_NOMBRE_OBJETIVO', 'bloque_nombre_objetivo');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_OBJETIVO', 'bloque_objetivo');
		$T->setBlock('tpl_tabla', 'BLOQUE_FECHA', 'bloque_fecha');

		$T->setVar('bloque_objetivo', '');
		foreach ($arr_datos[0] as $objetivo_id=>$objetivo_datos){
			$linea=0;
			$muestra_nombre=true;
			$tag_pasos_grupo = $xpath->query('relacion[@objetivo_id='.$objetivo_id.']/relacion',$tag_grupo);
			$T->setVar('bloque_pasos', '');


			foreach ($objetivo_datos->pasos as $paso_orden=>$paso_datos){

				$T->setVar('bloque_nombre_objetivo', '');
				$T->setVar('bloque_correcto', '');
				$T->setVar('bloque_total', '');
				$T->setVar('bloque_fecha', '');
				$T->setVar('__pasos', $paso_datos->nombre);


				foreach ($arr_datos['fechas'] as $tag_fecha_titulo){

					$T->setVar('__fecha_titulo', $tag_fecha_titulo);
					$T->parse('bloque_fecha', 'BLOQUE_FECHA', true);
					if ($muestra_nombre) {
						$T->setVar('__nombre_objetivo', $objetivo_datos->nombre);
						$T->setVar('__rowspan_objetivo', ($tag_pasos_grupo->length*2));
						$T->parse('bloque_nombre_objetivo', 'BLOQUE_NOMBRE_OBJETIVO', false);
					}
					$muestra_nombre=false;
					$datos = $paso_datos->fechas[$tag_fecha_titulo];
					$T->setVar('__cantidad_ok',isset($datos)?$datos->correctas:'S/I');
					$T->setVar('__cantidad_totales',isset($datos)?$datos->total:'S/I');
					$T->parse('bloque_correcto', 'BLOQUE_CORRECTO', true);
					$T->parse('bloque_total', 'BLOQUE_TOTAL', true);
				}
				$linea++;
				$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
			}
			$T->parse('bloque_objetivo', 'BLOQUE_OBJETIVO', true);
		}
		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	public function especialEficienciaPorPorcentajeObjetivo() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		$orden=0;
		$arr_datos=array();

		$objetivo = new ConfigEspecial($this->objetivo_id);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($objetivo->__xml_config);
		$xpath = new DOMXpath($dom);

		## Se recorren los objetivos encontrados en la configuración del reporte especial
		foreach($xpath->query('//grupos/grupo') as $key=> $tag_grupo) {


			foreach($xpath->query('relacion',$tag_grupo) as  $tag_objetivo) {

				for ($i = 0; $i < 10; $i++) {

					if ((strtotime($this->timestamp->fecha_termino) - strtotime($this->timestamp->fecha_inicio)) > 86400) {
					$interval = "'$i month'::INTERVAL";
					}
					else {
						$interval = "'$i day'::INTERVAL";
					}

					$sql = "SELECT * FROM reporte.cantidad_global(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($tag_objetivo->getAttribute('objetivo_id')).",".
					pg_escape_string($this->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.",'".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."'::TIMESTAMP WITHOUT TIME ZONE - ".$interval.")";
	//  			print($sql.'<br><br/>');
					$res =& $mdb2->query($sql);
					if (MDB2::isError($res)) {
						$log->setError($sql, $res->userinfo);
						exit();
				 	}
					if($row =$res->fetchRow()){
						$dom = new DomDocument();
						$dom->preserveWhiteSpace = FALSE;
						$dom->loadXML($row['cantidad_global']);
						$xpath2 = new DOMXpath($dom);
						unset($row["cantidad_global"]);
					}
					$conf_objetivo= $xpath2->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
					$conf_parametros=$xpath2->query('//parametros')->item(0);

					if(!isset($arr_datos['fechas'])){
						$arr_datos['fechas']=array();
					}
					$arr_datos['fechas'][date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]=date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')));

					foreach($xpath->query('relacion',$tag_objetivo) as  $tag_paso) {

						if(!isset($arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')])){
							$subObjetivo = new stdClass();
							$subObjetivo->nombre=$conf_objetivo->getAttribute('nombre');
							$subObjetivo->pasos=array();
						}else{
							$paso=$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')];
						}

						$tag_datos= $xpath2->query("//detalle[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]/datos/dato");
						$conf_paso = $xpath2->query("paso[@paso_orden=".$tag_paso->getAttribute('paso_orden')."]", $conf_objetivo)->item(0);

						if(!isset($arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')]->pasos[$tag_paso->getAttribute('paso_orden')])){
							$paso = new stdClass();
							$paso->nombre=$conf_paso->getAttribute('nombre');
							$paso->paso_orden=$conf_paso->getAttribute('paso_orden');
							$paso->fechas=array();

						}else{
							$paso=$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')]->pasos[$tag_paso->getAttribute('paso_orden')];
						}

						if ($tag_datos->length > 0) {
							$datos= new stdClass();
							$datos->porcentaje=$tag_datos->item(0)->getAttribute('porcentaje');
							$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
							$subObjetivo->pasos[$tag_paso->getAttribute('paso_orden')]=$paso;
							$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')] = $subObjetivo;
						}else{
							$datos= new stdClass();
							$datos->porcentaje="S/I";
							$paso->fechas[date('d/m/Y',strtotime($conf_parametros->getAttribute('fecha_inicio')))]= $datos;
							$subObjetivo->pasos[$tag_paso->getAttribute('paso_orden')]=$paso;
							$arr_datos[$key][$tag_objetivo->getAttribute('objetivo_id')]= $subObjetivo;
						}
					}
				}
			}

		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'especial_eficiencia_por_porcentaje_por_objetivo.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_PORCENTAJE', 'bloque_porcentaje');
		$T->setBlock('tpl_tabla', 'BLOQUE_NOMBRE_OBJETIVO', 'bloque_nombre_objetivo');
		$T->setBlock('tpl_tabla', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_tabla', 'BLOQUE_OBJETIVO', 'bloque_objetivo');
		$T->setBlock('tpl_tabla', 'BLOQUE_FECHA', 'bloque_fecha');

		$T->setVar('bloque_objetivo', '');

		foreach ($arr_datos[0] as $objetivo_id=>$objetivo_datos){

			$muestra_nombre = true;
			$tag_pasos_grupo = $xpath->query('relacion[@objetivo_id='.$objetivo_id.']/relacion',$tag_grupo);
			$T->setVar('bloque_pasos', '');

			foreach ($objetivo_datos->pasos as $paso_orden=>$paso_datos){

				$T->setVar('bloque_nombre_objetivo', '');
				$T->setVar('bloque_porcentaje', '');
				$T->setVar('bloque_total', '');
				$T->setVar('bloque_fecha', '');
				$T->setVar('__pasos', $paso_datos->nombre);
				$T->setVar('__print_class', ($orden % 2 == 0)?"celdaIteracion1":"celdaIteracion2");

				foreach ($arr_datos['fechas'] as $tag_fecha_titulo){

					$T->setVar('__fecha_titulo', $tag_fecha_titulo);
					$T->parse('bloque_fecha', 'BLOQUE_FECHA', true);
					if ($muestra_nombre) {
						$T->setVar('__nombre_objetivo', $objetivo_datos->nombre);
						$T->setVar('__rowspan_objetivo', $tag_pasos_grupo->length);
						$T->parse('bloque_nombre_objetivo', 'BLOQUE_NOMBRE_OBJETIVO', false);
					}
					$muestra_nombre=false;
					$datos=$paso_datos->fechas[$tag_fecha_titulo];
					$porcentaje= $datos->porcentaje!="S/I"?number_format($datos->porcentaje,2,",",null):$datos->porcentaje;
					$T->setVar('__porcentaje',$porcentaje);
					$T->parse('bloque_porcentaje', 'BLOQUE_PORCENTAJE', true);
				}
				$orden++;
				$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
			}
			$T->parse('bloque_objetivo', 'BLOQUE_OBJETIVO', true);
		}
		$this->resultado = $T->parse('out', 'tpl_tabla');
	}
        }

    function tiempoRespuestaEstadisticas(){
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'estadistica_tiempo_respuesta.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_PROMEDIOS', 'bloque_promedios');
		$T->setBlock('tpl_tabla', 'BLOQUE_DATOS', 'bloque_datos');
		$T->setBlock('tpl_tabla', 'BLOQUE_PROMEDIOS_POR_MONITOR', 'bloque_promedios_por_monitor');
		$T->setBlock('tpl_tabla', 'BLOQUE_NOMBRE_NODOS', 'bloque_nombre_nodos');
		$T->setBlock('tpl_tabla', 'BLOQUE_TABLA', 'bloque_tabla');

		$T->setVar('bloque_tabla', '');
		$T->setVar('_nombre_horario',  $this->extra["horario_nombre_item"]);
		$T->setVar('_horario_id',  $this->extra["horario_id_item"]);

		$sql = "SELECT 
			nodo_id,
			paso_orden,
			MIN(respuesta)  AS \"respuestamin\" , 
			MAX(respuesta) AS \"respuestamax\", 
			AVG(respuesta) AS \"respuestaprom\",
			STDDEV_POP(respuesta) AS \"respuestadesvest\"
			FROM procesado._rendimiento_real_sin_redondeo_habil(".
			pg_escape_string($current_usuario_id).", ".
			pg_escape_string($this->objetivo_id).", ".
			pg_escape_string($this->extra["horario_id_item"]).",'".
			pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
			pg_escape_string($this->timestamp->getTerminoPeriodo())."') 
			group by nodo_id, paso_orden 
			order by nodo_id, paso_orden";
		
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		//DEVUELVE NODOS_ID POR OBJETIVO
		$sql_nodos = "SELECT * FROM _nodos_id(".
		pg_escape_string($current_usuario_id).", ".
		pg_escape_string($this->objetivo_id).", '".
		pg_escape_string($this->timestamp->getInicioPeriodo())."')";

		$resNodosId =& $mdb2->query($sql_nodos);
		if (MDB2::isError($resNodosId)) {
			$log->setError($sql_nodos, $resNodosId->userinfo);
			exit();
		}
		$nodosId = $resNodosId->fetchRow();
		$nodosId = str_replace('{', "", $nodosId['_nodos_id']);
		$nodosId = str_replace('}', "", $nodosId);
		$nodosIdObjetivo = explode(",", $nodosId);

		//PASOS _ORDEN VISIBLES
		$sql_pasos_visibles = "SELECT 
		(xpath('//paso[@visible=1]/@paso_orden', xml_configuracion))::TEXT[] AS pasos_orden_visible
		FROM
		objetivo_config
		WHERE
		objetivo_id = (".pg_escape_string($this->objetivo_id).") and es_ultima_config='t'
		LIMIT 1";
		$resVisibles =& $mdb2->query($sql_pasos_visibles);
		if (MDB2::isError($resVisibles)) {
			$log->setError($sql_pasos_visibles, $resVisibles->userinfo);
			exit();
		}
		$rowVisibles = $resVisibles->fetchRow();
		$rowVisibles = str_replace('{', "", $rowVisibles['pasos_orden_visible']);
		$rowVisibles = str_replace('}', "", $rowVisibles);
		$pasosVisibles = explode(",", $rowVisibles);

		$array=Array();
		$cuenta_estadisticas = 0;
		while($row = $res->fetchRow()) {
			$estadisticas= array();
			array_push($estadisticas, $row ["nodo_id"], $row ["paso_orden"], $row["respuestamin"], $row["respuestamax"], $row["respuestaprom"], $row["respuestadesvest"]);
			array_push($array, $estadisticas);
		}

		$arr = array();
		foreach($array as $key => $item){
			$arr[$item[0]][$item[1]] = $item;
		}

		foreach ($nodosIdObjetivo as $key => $nodosId_Objetivo) {
			$cuenta_pasos = 0;
			foreach ($pasosVisibles as $key => $pasos_Visibles) {
				if ($arr[$nodosId_Objetivo][$pasos_Visibles] == '') {
					$arr[$nodosId_Objetivo][$pasos_Visibles][0] = $nodosId_Objetivo;
					$arr[$nodosId_Objetivo][$pasos_Visibles][1] = $pasos_Visibles;
					$arr[$nodosId_Objetivo][$pasos_Visibles][2] = '-1000';
					$arr[$nodosId_Objetivo][$pasos_Visibles][3] = '-1000';
					$arr[$nodosId_Objetivo][$pasos_Visibles][4] = '-1000';
					$arr[$nodosId_Objetivo][$pasos_Visibles][5] = '-1000';
				}
				$cuenta_pasos++;
			}
		}
		
		$array_promedio_consolidado = array();
		$total_pasos = array();
		$contador = 0;
		$cuentaNodos = 0;
		$contadorElementos = 0;
		$T->setVar('bloque_datos', '');
		foreach ($arr as $key => $value) {
			foreach ($value as $value2) {
				if (in_array($value2[1], $pasosVisibles)) {
					$nodo_id = $value2[0];
					$paso_orden = $value2[1];
					$respuestamin = $value2[2];
					$respuestamax = $value2[3];
					$respuestaprom = $value2[4];
				 	$respuestadesvest = $value2[5];

				 	//NOMBRE DE LOS NODOS
					$sql2 = "SELECT  nombre FROM nodo 
					WHERE nodo_id = ".$nodo_id;
					$res2 =& $mdb2->query($sql2);
					if (MDB2::isError($res2)) {
						$log->setError($sql2, $res2->userinfo);
						exit();
					}
					$row2 = $res2->fetchRow();

					//NOMBRE DE LOS PASOS
					$sql3 = "SELECT * FROM nombre_paso_orden(". $this->objetivo_id.", ".$paso_orden.")";
					$res3 =& $mdb2->query($sql3);
					if (MDB2::isError($res3)) {
						$log->setError($sql3, $res3->userinfo);
						exit();
					}
					$row3 = $res3->fetchRow();
					$nombre_paso = str_replace('{', "", $row3['nombre_paso_orden']);
					$nombre_paso = str_replace('}', "", $nombre_paso);
					$nombre_paso = str_replace('"', "", $nombre_paso);

					$T->setVar('__celdaIteracion_nodo',(($cuentaNodos % 2) == 0)?'celdaIteracion2':'celdaIteracion1');

					$T->setVar('_nodo_nombre', ($paso_orden == 0)?$row2['nombre']:'');
					$T->setVar('_class_nodo', ($paso_orden == 0)?'style="border-top: 1px solid #a2a2a2; border-left: 1px solid #a2a2a2; text-align: left;  font-family: \'Varela Round\', sans-serif; padding: 1px 6px 1px 6px;"':'');
					$T->setVar('_paso_orden',$nombre_paso );
					$T->setVar('_respuestamin', number_format(round($respuestamin/1000, 2), 2));
					$T->setVar('_respuestamax',  number_format(round($respuestamax/1000, 2), 2));
					$T->setVar('_respuestaprom',  number_format(round($respuestaprom/1000, 2), 2));
					$T->setVar('_respuestadesvest',  number_format(round($respuestadesvest/1000, 2), 2));
					$T->setVar('_celdaIteracion',(($contadorElementos % 2) == 0)?'celdaIteracion2':'celdaIteracion1');
					if ($respuestamin != -1000 || $respuestamax != -1000 || $respuestaprom != -1000 || $respuestadesvest != -1000) {
						$prom_respuesta_min = $prom_respuesta_min + $respuestamin;
						$prom_respuesta_max = $prom_respuesta_max +$respuestamax;
						$prom_respuesta_prom = $prom_respuesta_prom + $respuestaprom;
						$prom_respuesta_desvEst = $prom_respuesta_desvEst + $respuestadesvest;

						$nodo_ids[$nodo_id] = $nodo_id;
						$nombre_nodo[$nodo_id] = $row2['nombre'];
						$array_minimo_consolidado[$nodo_id] =  $array_minimo_consolidado[$nodo_id] + $respuestamin;
						$array_maximo_consolidado[$nodo_id] =  $array_maximo_consolidado[$nodo_id] + $respuestamax;
						$array_promedio_consolidado[$nodo_id] =  $array_promedio_consolidado[$nodo_id] + $respuestaprom;
						$array_desv_estandar_consolidado[$nodo_id] =  $array_desv_estandar_consolidado[$nodo_id] + $respuestadesvest;
						$total_pasos[$nodo_id] =  $total_pasos[$nodo_id]  + 1;

						$contador++;
					}
					$T->parse('bloque_datos', 'BLOQUE_DATOS', true);
					$nodo_nombre = $row2['nombre'];
					$contadorElementos++;
				}
			}
			$cuentaNodos++;
		}

		$T->setVar('_prom_min', number_format(round((($prom_respuesta_min/$contador)/1000), 2), 2) );
		$T->setVar('_prom_max', number_format(round((($prom_respuesta_max/$contador)/1000), 2), 2)  );
		$T->setVar('_prom_prom', number_format(round((($prom_respuesta_prom/$contador)/1000), 2), 2) );
		$T->setVar('_prom_desvest', number_format(round((($prom_respuesta_desvEst/$contador)/1000), 2), 2) );

		//PROMEDIO TOTAL POR CADA MONITOR
		$cont_mon = 1;
		$T->setVar('bloque_promedios', '');
		foreach ($nodo_ids as $nodos_id) {
			$T->setVar('_promedios', number_format(round((($array_promedio_consolidado[$nodos_id]/ $total_pasos[$nodos_id])/1000), 2),2));
			$T->parse('bloque_promedios', 'BLOQUE_PROMEDIOS', true);
			$cont_mon++;
		}
		$T->setVar('__cant_monitores', $cont_mon);

		//NOMBRE DE LOS NODOS
		$T->setVar('bloque_nombre_nodos', '');
		foreach ($nombre_nodo as $key => $nombre_nodos) {
			$T->setVar('_nombre_nodos',$nombre_nodos );
			$T->parse('bloque_nombre_nodos', 'BLOQUE_NOMBRE_NODOS', true);
		}

		//PROMEDIO DE CADA ESTADISTICA POR MONITOR
		$celdaIteracion = 0;
		$T->setVar('bloque_promedios_por_monitor', '');
		foreach ($nodo_ids as $key => $nodos_id) {
			$T->setVar('__celdaIteracion',(($celdaIteracion % 2) == 0)?'celdaIteracion2':'celdaIteracion1');
			$T->setVar('__celdaIteracion_monitor',(($celdaIteracion % 2) == 0)?'dedede':'fff');
			$T->setVar('__nombre_nodo', $nombre_nodo[$nodos_id]);
			$T->setVar('__minimo_promedios', number_format(round((($array_minimo_consolidado[$nodos_id]/ $total_pasos[$nodos_id])/1000), 2),2));
			$T->setVar('__maximo_promedios', number_format(round((($array_maximo_consolidado[$nodos_id]/ $total_pasos[$nodos_id])/1000), 2),2));
			$T->setVar('__promedio_promedios', number_format(round((($array_promedio_consolidado[$nodos_id]/ $total_pasos[$nodos_id])/1000), 2),2));
			$T->setVar('__desv_estandar_promedios', number_format(round((($array_desv_estandar_consolidado[$nodos_id]/ $total_pasos[$nodos_id])/1000), 2),2));
			$T->parse('bloque_promedios_por_monitor', 'BLOQUE_PROMEDIOS_POR_MONITOR', true);
			$celdaIteracion++;
		}
		$T->parse('bloque_tabla', 'BLOQUE_TABLA', true);
	
		return $this->resultado =  $T->parse('out', 'tpl_tabla');
	}

	function getDisponibilidadConsolidadoRealHabil() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$sql = "SELECT * FROM reporte.disponibilidad_detalle_consolidado_habil(".
  				pg_escape_string($current_usuario_id).", ".
  				pg_escape_string($this->objetivo_id).", ".
  				pg_escape_string($this->extra["horario_id_item"]).", '".
  				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
  				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

  		$res = & $mdb2->query($sql);
  		if (MDB2::isError($res)) {
 			$log->setError($sql, $res->userinfo);
 			exit();
 		}

 		if($row = $res->fetchRow()){
 			$dom = new DomDocument();
 			$dom->preserveWhiteSpace = FALSE;
  			$dom->loadXML($row['disponibilidad_detalle_consolidado_habil']);
  			$xpath = new DOMXpath($dom);
  			unset($row["disponibilidad_detalle_consolidado_habil"]);
		}

		$conf_uptime = $xpath->query("/atentus/resultados/detalles/detalle/estadisticas/estadistica[@evento_id=1]");
		$conf_downtime = $xpath->query("/atentus/resultados/detalles/detalle/estadisticas/estadistica[@evento_id=2]");
		$conf_parcial = $xpath->query("/atentus/resultados/detalles/detalle/estadisticas/estadistica[@evento_id=3]");
		$conf_no_monitoreo = $xpath->query("/atentus/resultados/detalles/detalle/estadisticas/estadistica[@evento_id=7]");
		$conf_marcado = $xpath->query("/atentus/resultados/detalles/detalle/estadisticas/estadistica[@evento_id=9]");
		$conf_datos = $xpath->query("/atentus/resultados/detalles/detalle/estadisticas/estadistica");

		//SI NO HAY DATOS MOSTRAR MENSAJE
    	if (!$conf_datos->length) {
  			$this->resultado = $this->__generarContenedorSinDatos();
  			return;
  		}

  		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
  		$T->setFile('tpl_tabla', 'disponibilidad_upt_down_consolidado.tpl');  	
  		$uptime = $conf_uptime->item(0)->getAttribute('porcentaje');
  		$downtime = $conf_downtime->item(0)->getAttribute('porcentaje');
  		$parcial = $conf_parcial->item(0)->getAttribute('porcentaje');
  		$no_monitoreo = $conf_no_monitoreo->item(0)->getAttribute('porcentaje');
  		$marcado = $conf_marcado->item(0)->getAttribute('porcentaje');

  		if (($marcado  + $no_monitoreo) !=100) {
			$uptime=$uptime+$parcial;
			$factor_total=$uptime+$downtime;
			$uptime_real = ($uptime * 100) / $factor_total;
			$downtime_real = ($downtime * 100) / $factor_total;
		}else{
			$uptime_real = 0;
			$downtime_real = 0;
		}

		$T->setVar('__uptime_real', number_format(round($uptime_real),2),2);
		$T->setVar('__downtime_real', number_format(round($downtime_real),2),2);

 		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	function getSlaIvr(){
		global $current_usuario_id;
		include 'utils/get_sla_operador_ivr.php';
		$usuario = new Usuario($current_usuario_id);
		$usuario->__Usuario();
		

		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'sla_operador_ivr.tpl');
		$T->setBlock('tpl_tabla', 'BLOQUE_MONITOREOS_CONTESTA', 'bloque_monitoreos_contesta');
		$T->setBlock('tpl_tabla', 'BLOQUE_MONITOREOS_NO_CONTESTA', 'bloque_monitoreos_no_contesta');
		$T->setBlock('tpl_tabla', 'BLOQUE_MONITOREOS_OTRO', 'bloque_monitoreos_otro');

		$json_data_ivr = get_data_ivr($this->objetivo_id, $this->timestamp->getInicioPeriodo(), $this->timestamp->getTerminoPeriodo(), $current_usuario_id, $usuario->clave_md5);
		$data_ivr = json_decode($json_data_ivr, true);

		if ($data_ivr['Estado'] == 'noData' || $data_ivr == NULL) {
			$T->setVar('tpl_tabla', $this->__generarContenedorSinDatos());
		}

		if ($data_ivr['Mensaje'] == 'Objetivo no posee atributo *tipo_comparacion*') {
			$T->setVar('tpl_tabla', $this->__generarContenedorSinTipoComparacion());
		}
		
		$T->setVar('__nombre_archivo_answer', 'llamada_contestada_'.$this->timestamp->getInicioPeriodo().'_'.$this->timestamp->getTerminoPeriodo());
		$T->setVar('__nombre_archivo_no_answer', 'llamada_no_contestada'.$this->timestamp->getInicioPeriodo().'_'.$this->timestamp->getTerminoPeriodo());
		$T->setVar('__nombre_archivo_other', 'llamada_otro'.$this->timestamp->getInicioPeriodo().'_'.$this->timestamp->getTerminoPeriodo());
		$T->setVar('__id_chart', $this->objetivo_id.'_'.$this->extra["item_orden"]);

		$T->setVar('__path_amcharts',REP_PATH_AMCHARTS);

		$clase = 0;
		$T->setVar('bloque_monitoreos_contesta', '');
		foreach ($data_ivr[1] as $datos_ivr) {
			$dato_ivr = split(',', $datos_ivr);
			$T->setVar('__fecha_monitoreo', $dato_ivr[0]);
			$T->setVar('__estado', $dato_ivr[1]);
			$T->setVar('__tiempo_respuesta', number_format(round($dato_ivr[2]/1000,2),2));
			$T->setVar('__print_class', ($clase % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
			$T->setVar('__numero', $clase+1);
			$T->parse('bloque_monitoreos_contesta', 'BLOQUE_MONITOREOS_CONTESTA', true);
			$clase++;
		}

		$clase2 = 0;
		$T->setVar('bloque_monitoreos_no_contesta', '');
		foreach ($data_ivr[2] as $datos_ivr) {
			$dato_ivr2 = split(',', $datos_ivr);
			$T->setVar('__fecha_monitoreo2', $dato_ivr2[0]);
			$T->setVar('__estado2', $dato_ivr2[1]);
			$T->setVar('__tiempo_respuesta2', $dato_ivr2[2]);
			$T->setVar('__print_class2', ($clase2 % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
			$T->setVar('__numero2', $clase2+1);
			$T->parse('bloque_monitoreos_no_contesta', 'BLOQUE_MONITOREOS_NO_CONTESTA', true);
			$clase2++;
		}

		$clase3 = 0;
		$T->setVar('bloque_monitoreos_otro', '');
		foreach ($data_ivr[3] as $datos_ivr) {
			$dato_ivr3 = split(',', $datos_ivr);
			$T->setVar('__fecha_monitoreo3', $dato_ivr3[0]);
			$T->setVar('__estado3',  $dato_ivr3[1]);
			$T->setVar('__tiempo_respuesta3', $dato_ivr3[2]);
			$T->setVar('__print_class3', ($clase3 % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
			$T->setVar('__numero3', $clase3+1);
			$T->parse('bloque_monitoreos_otro', 'BLOQUE_MONITOREOS_OTRO', true);
			$clase3++;
		}

		$T->setVar('__objetivo_id', $this->objetivo_id);
		$T->setVar('__prom_tiempo_resp', number_format(round(($data_ivr[0]['prom']/1000),2),2));
		$T->setVar('__min_tiempo_resp', number_format(round(($data_ivr[0]['min']/1000),2),2));
		$T->setVar('__max_tiempo_resp', number_format(round(($data_ivr[0]['max']/1000),2),2));
		$T->setVar('__contestadas', $data_ivr[0]['contestadas']);
		$T->setVar('__no_contestadas', $data_ivr[0]['noContestadas']);
		$T->setVar('__otros', $data_ivr[0]['otros']);
		$T->setVar('__total', $data_ivr[0]['total']);

		$T->setVar('__boton', ($data_ivr[0]['contestadas'] != 0)?1:0);
		$T->setVar('__boton2', ($data_ivr[0]['noContestadas'] != 0)?1:0);
		$T->setVar('__boton3', ($data_ivr[0]['otros'] != 0)?1:0);

		$T->setVar('__contestadas_porcentaje', number_format(round(($data_ivr[0]['contestadas']*100)/$data_ivr[0]['total'],2),2));
		$T->setVar('__noContestadas_porcentaje', number_format(round(($data_ivr[0]['noContestadas']*100)/$data_ivr[0]['total'],2),2));
		$T->setVar('__otros_porcentaje', number_format(round(($data_ivr[0]['otros']*100)/$data_ivr[0]['total'],2),2));

		$this->resultado = $T->parse('out', 'tpl_tabla');
	}

	private function __generarContenedorSinTipoComparacion($titulo = null) {
		$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
		$T->setFile('tpl_sindatos', 'sin_tipo_comparacion.tpl');
		$T->setBlock('tpl_sindatos', 'TIENE_TITULO', 'tiene_titulo');
		if ($titulo == null) {
			$T->setVar('tiene_titulo', '');
		}
		else {
			$T->setVar('__titulo', $titulo);
			$T->parse('tiene_titulo', 'TIENE_TITULO', false);
		}
		return $T->parse('out', 'tpl_sindatos');
	}

	private function __generarContenedorSinDatos($titulo = null) {
		$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
		$T->setFile('tpl_sindatos', 'sin_datos.tpl');
		$T->setBlock('tpl_sindatos', 'TIENE_TITULO', 'tiene_titulo');


		if ($titulo == null) {
			$T->setVar('tiene_titulo', '');
		}
		else {
			$T->setVar('__titulo', $titulo);
			$T->parse('tiene_titulo', 'TIENE_TITULO', false);
		}
		return $T->parse('out', 'tpl_sindatos');
	}


	private function __generarContenedorSinFiltro($titulo = null) {
		$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
		$T->setFile('tpl_sindatos', 'sin_filtro.tpl');
		$T->setBlock('tpl_sindatos', 'TIENE_TITULO', 'tiene_titulo');

		$T->setVar('tiene_titulo', '');

		return $T->parse('out', 'tpl_sindatos');
	}

	private function __generarContenedorSinFiltroAudio($titulo = null) {
		$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
		$T->setFile('tpl_sindatos', 'sin_filtro_audio.tpl');
		$T->setBlock('tpl_sindatos', 'TIENE_TITULO', 'tiene_titulo');

		$T->setVar('tiene_titulo', '');

		return $T->parse('out', 'tpl_sindatos');
	}

	private function __generarContenedorConMantenimiento($titulo = null) {
		$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
		$T->setFile('tpl_sindatos', 'mantenimiento.tpl');
		$T->setBlock('tpl_sindatos', 'TIENE_TITULO', 'tiene_titulo');

		$T->setVar('tiene_titulo', '');

		return $T->parse('out', 'tpl_sindatos');
	}

}

?>