<?
include("../config/include.php");
session_start();
$objetivo_especial = $_REQUEST['objetivo'];
$horario_id = $_REQUEST['horario_id'];
$usuario_id = $_REQUEST['usuario_id'];
$fecha_inicio_js = $_REQUEST['fecha_inicio'];
$fecha_termino_js = $_REQUEST['fecha_termino'];
$token = $_REQUEST['token'];
global $mdb2;
global $log;

$objetivo_especial  = intval($objetivo_especial);
$horario_id  = intval($horario_id);
$usuario_id  = intval($usuario_id);

if (strtotime($fecha_termino_js) < strtotime($fecha_inicio_js)) {
	echo '{"ERROR": "La fecha de termino no puede ser menor a la fecha de inicio."}';
	exit;
}
if (strtotime($fecha_termino_js) == strtotime($fecha_inicio_js)) {
	echo '{"ERROR" : "La fecha de inicio y la fecha de termino es la misma."}';
	exit;
}
if ((strtotime($_REQUEST['fecha_termino'])-strtotime($_REQUEST['fecha_inicio']))>86400) {
	echo '{"ERROR" : "La consulta no debe exceder un día."}';
	exit;
}

$sql_token = "SELECT * FROM 
public.webservices_token 
WHERE 
key = '".$token."'";
$res_token_query =& $mdb2->query($sql_token);
if (MDB2::isError($res_token_query)) {
	$log->setError($sql_token, $res_token_query->userinfo);
	exit();
}
$fila_token = $res_token_query->fetchRow();

if ($res_token_query->numRows() == 0) {
	echo "Token incorrecto.";
	exit;
}

$usuario_id_sql = "SELECT * FROM 
public.cliente_usuario 
WHERE 
cliente_usuario_id =".$usuario_id." AND 
cliente_id = ".$fila_token['cliente_id'];
$res_usuario_query =& $mdb2->query($usuario_id_sql);
if (MDB2::isError($res_usuario_query)) {
	$log->setError($usuario_id_sql, $res_usuario_query->userinfo);
	exit();
}
$fila_usuario = $res_usuario_query->fetchRow();

if ($res_usuario_query->numRows() == 0) {
	echo "Usuario incorrecto.";
	exit;
}

$objetivo_sql = "SELECT * FROM 
public.cliente_mapa_subcliente_usuario su,
public.cliente_mapa_subcliente_objetivo so
WHERE 
su.cliente_usuario_id =".$fila_usuario['cliente_usuario_id']." AND 
su.cliente_subcliente_id = so.cliente_subcliente_id AND
so.objetivo_id = ".$objetivo_especial ;
$res_objetivo_query =& $mdb2->query($objetivo_sql);
if (MDB2::isError($res_objetivo_query)) {
	$log->setError($objetivo_sql, $res_objetivo_query->userinfo);
	exit();
}
$fila_objetivo = $res_objetivo_query->fetchRow();

if ($res_objetivo_query->numRows() == 0) {
	echo "Objetivo incorrecto.";
	exit;
}

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

if ( $_SESSION["dateinicio"] != $fecha_inicio_js || $_SESSION["datetermino"] != $fecha_termino_js ) {
	$_SESSION["dateinicio"] = $fecha_inicio_js;
	$_SESSION["datetermino"] = $fecha_termino_js;
}else{
	if (isset($_SESSION["beforedate"])) {
		if ((strtotime($dt->format("Y-m-d H:i:s")) - strtotime($_SESSION["beforedate"])) < 10) {
			echo $_SESSION['json'];
			exit;
		}else{
			$_SESSION["beforedate"] = $dt->format("Y-m-d H:i:s");
		}
	}else{
		$_SESSION["beforedate"] = $dt->format("Y-m-d H:i:s");
	}
}

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

		$disponibilidad = array();
		$sql = "SELECT * FROM reporte.especial_disponibilidad_resumen_objetivopaso_v2(".
		pg_escape_string($usuario_id).",".
		pg_escape_string($sql_objetivos).", ".
		pg_escape_string($horario_id).", '".
		pg_escape_string($fecha_inicio_js)."', '".
		pg_escape_string($fecha_termino_js)."')";

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
			$segundos_seleccionados = (strtotime($dt->format("Y-m-d H:i:s")) - strtotime($fecha_inicio_js));
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

			$paso_info= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$obj_id."]/paso[@paso_orden=".$paso->paso_orden."]")->item(0);
			$conf_dato = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$obj_id."]/detalles/detalle[@paso_orden=".$paso->paso_orden."]/datos/dato")->item(0);
			$objetivo_info = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$obj_id."]")->item(0);

			$json .= '{"nombre_paso": "'.$paso_info->getAttribute('nombre').'",';

			$uptime = $disponibilidad[$obj_id][$paso->paso_orden]['segundos_uptime'];
			$downtime = $disponibilidad[$obj_id][$paso->paso_orden]['segundos_downtime'];
			$factor_total = $disponibilidad[$obj_id][$paso->paso_orden]['factor_total'] ;
			$tiempo_respuesta = ($disponibilidad[$obj_id][$paso->paso_orden]['tiempo_prom']);

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

			if ($muestra_nombre == "") {
				$nombre_obj_paso = $paso_info->getAttribute('nombre').' - '.$objetivo_info->getAttribute('nombre');
			}else{
				$nombre_obj_paso = $paso_info->getAttribute('nombre');
			}

			$json .= '"nombre_objetivo": "'.(($muestra_nombre == "")?' - '.$objetivo_info->getAttribute('nombre') : "").'",';
			$json .= '"nombre_objetivo_tooltip": "'.(($muestra_nombre == "")?$objetivo_info->getAttribute('nombre') : "").'",';
			$json .= '"uptime_real_paso": "'.number_format(($uptime_real), 3, '.', '').'",';
			$json .= '"downtime_real_paso": "'.number_format((($downtime_real)), 3, '.', '').'",';
			$json .= '"tiempo_respuesta_paso": "'.number_format((($tiempo_respuesta)), 3, '.', '').'",';
			$json .= '"tiempo_porcentaje_uptime": "'.(($dias_uptime > 0)?$dias_uptime." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_uptime - ($dias_uptime * 86400)).'",';
			$json .= '"tiempo_porcentaje_downtime": "'.(($dias_downtime > 0)?$dias_downtime." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_downtime - ($dias_downtime * 86400)).'",';
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
$json .= '"tiempo_uptime_tooltip": "'. (($dias_uptime_cat > 0)?$dias_uptime_cat." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_uptime_cat - ($dias_uptime_cat * 86400)).'"';
$categorias .= '"disponibilidad": [{';
$categorias .= '"uptimecat": "'.number_format(($uptime_real_acumulado_total/$contador2), 3, '.', '').'",';
$categorias .= '"downtimecat": "'.number_format(($downtime_real_acumulado_total/$contador2), 3, '.', '').'",';
$categorias .= '"tiemporespuesta": "'.number_format(($tiempo_respuesta_acumulado_total/$contador2), 3, '.', '').'",';
$categorias .= '"tiempo_uptime_tooltip": "'. (($dias_uptime_cat > 0)?$dias_uptime_cat." dia(s) ":"0 dia(s) ").date("H:i:s", $tiempo_uptime_cat - ($dias_uptime_cat * 86400)).'",';
$categorias .= '"tiempo_downtime_tooltip": "0 dia(s) '.date("H:i:s", $tiempo_downtime_cat).'",';
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
$_SESSION['json'] =  $json;
echo $json;