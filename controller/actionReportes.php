<?

$item_id = $_REQUEST["item_id"];
$tipo = $_REQUEST["item_tipo"];
$subgrafico_id = $_REQUEST["subgrafico_id"];
$monitor_id = $_REQUEST["monitor_id"];
$pagina = $_REQUEST["pagina"];
$paso_id = $_REQUEST["paso_id"];
$fecha_monitoreo = $_REQUEST["fecha_monitoreo"];
$tiene_svg = $_REQUEST["tiene_svg"];
$tiene_flash = $_REQUEST["tiene_flash"];
//TODO: quitar
$item_elemento = $_REQUEST["item_elemento"];

$datos_codificacion = $_REQUEST["datos_codificacion"];
$datos_separador = $_REQUEST["datos_separador"];
$datos_decimal = $_REQUEST["datos_decimal"];
$datos_uptime = $_REQUEST["datos_uptime"];
$datos_downtime_parcial = $_REQUEST["datos_downtime_parcial"];
$datos_downtime_global = $_REQUEST["datos_downtime_global"];
$datos_nomonitoreo = $_REQUEST["datos_nomonitoreo"];
$datos_eventoespecial = $_REQUEST["datos_eventoespecial"];


/* OBTENGO EL HORARIO.
 * SELECCIONADO POR EL CLIENTE, 
 * INGRESADAS EN VARIABLES DE SESION DESDE showReporte */

if ($_SESSION["usa_horario_habil"]) {
	if ($usr->solo_lectura and isset($_SESSION["horario_id_".$objeto_id])) {
		$horario_id = $_SESSION["horario_id_".$objeto_id];
	}
	else {
		$horario_id = $usr->getHorarioPorDefecto($objeto_id);
	}
}
else {
	$horario_id = 0;
}

/* OBTENGO EL PERIODO DEL REPORTE */
if ($_SESSION["usa_calendario_periodico"]) {
	$timestamp = new Timestamp($_SESSION["fecha_inicio_periodico"], $_SESSION["fecha_termino_periodico"]);
}
elseif ($_SESSION["usa_calendario"]) {
	$timestamp = new Timestamp($_SESSION["fecha_inicio"], $_SESSION["fecha_termino"]);
}
/* SOLO PARA CUANDO SE LLAMA A LOS EVENTOS DESDE EL SEMAFORO */
elseif ($_REQUEST["usa_periodo_semaforo"]) {
	$timestamp = new Timestamp($usr->periodo_semaforo_inicio);
}
else {
	$timestamp = new Timestamp();
}

if ($_REQUEST["parent_objetivo_id"] != '') {
	$timestamp->tipo_periodo = "especial";
	$timestamp->tipo_id = $_REQUEST["reporte_informe_subtipo_id"];
}


/* SI SE QUIERE TENER LOS GRAFICOS Y TABLAS DEL INFORME */
if (isset($accion) and $accion == "buscar_item") {

	if (isset($_REQUEST['valor_escala']) and $_REQUEST['valor_escala'] != 0) {
		$_SESSION["valor_escala_".$objeto_id."_".$item_id] = $_REQUEST['valor_escala'];
	}

	/* ITEM A GENERAR */
	$item = new ReporteItem($item_id);
	$item->__ReporteItem();
	$contenido = $item->getContenido($tiene_svg, $tiene_flash);
	$contenido->tipo = $tipo;
	$contenido->objetivo_id = $objeto_id;
//	$contenido->subobjetivo_id = $subobjeto_id;
	$contenido->horario_id = $horario_id;
	$contenido->timestamp = $timestamp;
        $imprimir = "";
	// TODO: quitar esta varible y pasarla como extra.
	$contenido->subgrafico_id = $subgrafico_id;

	$contenido->extra = array("monitor_id" => $_REQUEST["monitor_id"],
							  "pagina" => $_REQUEST["pagina"],
							  "fecha_monitoreo" => $fecha_monitoreo,
							  "subgrafico_id" => $subgrafico_id,
							  "paso_id" => $paso_id,
							  "imprimir" => $imprimir,
							  "popup" => $_REQUEST["popup"],
							  "parent_objetivo_id" => $_REQUEST["parent_objetivo_id"],
							  "datos_separador" => $datos_separador,
							  "datos_decimal" => $datos_decimal,
							  "datos_uptime" => $datos_uptime,
							  "datos_downtime_parcial" => $datos_downtime_parcial,
							  "datos_downtime_global" => $datos_downtime_global,
							  "datos_nomonitoreo" => $datos_nomonitoreo,
							  "datos_eventoespecial" =>$datos_eventoespecial,
							  "objetivo_id" => $_REQUEST["objetivo_id"],
							  //"objetivo_id" => $objetivo_id,
							  "reporte_id" => $_REQUEST["reporte_id"],
							  "segmento_id" => $_REQUEST["segmento_id"],
	                          "semaforo" => $_REQUEST["semaforo"]
							  );

	$contenido->generarResultado(false);

	/* SI ES UN GRAFICO EL RESULTADO DEBE SER UN XML */
	if ($contenido->tipo == "xml") {
                ob_clean();

		header('Content-type: text/xml');
		echo($contenido->resultado);
	}

	/* SI SON LOS DATOS EXPORTADOS EL RESULTADO ES UN CSV */
	elseif ($contenido->tipo == "csv") {
            ob_clean();

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Type: application/csv');
		header("Content-Disposition: attachment; filename=\"datos-".$timestamp->getFechaInicio("Y-m-d").".csv\"");
                echo(($datos_codificacion == 1)?$contenido->resultado:mb_convert_encoding($contenido->resultado, "ISO-8859-1", "UTF-8"));
        }

	/* SI ES UNA TABLA EL RESULTADO DEBE SER TEXTO PLANO */
	else {
		echo($contenido->resultado."SEPARACIONCELDA".$contenido->tiempo_expiracion);
	}

	exit();
}

?>