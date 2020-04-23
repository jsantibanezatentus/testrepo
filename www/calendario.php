<? 

include("../config/include.php");
include("../config/authentication.php");

/**
 * Funcion que dibuja un calendario para el mes ingresado.
 * 
 * @param integer $mes_actual
 * @param integer $anno_actual
 */
function calendarioMes($mes_actual, $anno_actual, $fecha_minima, $fecha_maxima, $rep_dias = null) {
	global $meses_anno;
	global $T;
	global $nombre_clase;

	$ultimo_dia_mes = date("d", mktime(0, 0, 0, $mes_actual+1, 0, $anno_actual));
	
	$fecha_mostrada = date("Y-m-d", strtotime($anno_actual."-".$mes_actual."-1"));
	
	if ($fecha_mostrada >= $fecha_minima and $fecha_mostrada <= $fecha_maxima and !isset($rep_dias)) {
		$disabled_mes = 0;
	}
	else {
		$disabled_mes = 1;
	}





	/* SE SETEA LOS DATOS DEL MES */
	$T->setVar('__mes_id', "mes|".$fecha_mostrada);
	$T->setVar('__mes', $meses_anno[$mes_actual]." ".$anno_actual);
	$T->setVar('__mes_script', ($disabled_mes)?"":$nombre_clase.".seleccionarMes('mes|".$fecha_mostrada."');");
	$T->setVar('__mes_class', ($disabled_mes)?"mes_desactivado":"mes");
	
	$T->setVar('lista_semanas', '');



	$dia_semana = 1;
	/* DIAS DE LA PRIMERA SEMANA ANTES DE EMPEZAR EL MES */
	for ($dia_ant=1; $dia_ant<(date("N", strtotime($fecha_mostrada))); $dia_ant++) {

		if ($dia_ant==1) {
			$T->setVar('__semana', '');
			$T->setVar('__semana_id', '0');
			$T->setVar('__semana_script', '');
			$T->setVar('__semana_class', "semana");
		}
		
		$T->setVar('__dia', '');
		$T->setVar('__dia_id', '0');
		$T->parse('lista_dias', 'LISTA_DIAS', true);
		$dia_semana++;
	}

	/* DIAS DEL MES */
	for($dia=1; $dia<=$ultimo_dia_mes; $dia++) {
		$fecha_aux = date("Y-m-d", strtotime($anno_actual."-".$mes_actual."-".$dia));

		/* SI ES LUNES SE SETEA LOS DATOS DE LA SEMANA */
		if ($dia_semana==1) {
			if ($fecha_aux >= $fecha_minima and $fecha_aux <= $fecha_maxima and !isset($rep_dias)) {
				$disabled_semana = 0;
			}
			else {
				$disabled_semana = 1;
			}
			$semana_id = 'semana|'.$fecha_aux;
			$T->setVar('__semana', date("W", strtotime($fecha_aux)));
			$T->setVar('__semana_id', 'semana|'.$fecha_aux);
			$T->setVar('__semana_script', ($disabled_semana)?"":"$nombre_clase.seleccionarSemana('semana|".$fecha_aux."');");
			$T->setVar('__semana_class', ($disabled_semana)?"semana_desactivado":"semana");
		}

		if (($fecha_aux >= $fecha_minima and $fecha_aux <= $fecha_maxima and !isset($rep_dias)) or 
			(isset($rep_dias) and in_array($fecha_aux, $rep_dias))) {
			$disabled_dia = 0;
		}
		else {
			$disabled_dia = 1;
		}
		
		/* SE SETEA LOS DATOS DEL DIA */
		$T->setVar('__dia', $dia);
		$T->setVar('__dia_id', "dia|".$fecha_aux);
		$T->setVar('__dia_script', ($disabled_dia)?"":"$nombre_clase.seleccionarDia('dia|".$fecha_aux."')");
		$T->setVar('__dia_class', ($disabled_dia)?"dia_desactivado":"dia");
		$T->parse('lista_dias', 'LISTA_DIAS', true);

		if ($dia_semana==7 or $dia==$ultimo_dia_mes) {
			$T->parse('lista_semanas', 'LISTA_SEMANAS', true);
			$T->setVar('lista_dias', '');
			$dia_semana=0;
		}
		
		$dia_semana++;
	}

	$T->parse('lista_meses', 'LISTA_MESES', true);
}

$fecha_maxima = date("Y-m-d");
$fecha_minima = date("Y-m-d", strtotime("-6 months"));

/* SI SE QUIERE MOSTRAR EL CALENDARIO PERIODICO */
if (isset($_REQUEST["accion"]) and $_REQUEST["accion"]=="mostrar_calendario_periodico") {

	$nombre_clase = $_REQUEST["nombre_clase"];
	$fecha_inicio = date("Y-m-d", strtotime($_REQUEST["fecha_inicio_periodo"]));
	$fecha_termino = date("Y-m-d", strtotime($_REQUEST["fecha_termino_periodo"]));
	$anno_mostrado = date("Y", strtotime($fecha_inicio));
	$mes_mostrado = date("n", strtotime($fecha_inicio));

	/* VEO SI LAS FECHAS INGRESADAS PUEDEN SER USADAS POR EL USUARIO */
	$sql = "SELECT * FROM public.reporte_periodico_informes(".
			pg_escape_string($usr->usuario_id).", ".
			pg_escape_string($_REQUEST["objeto_id"]).", ".
			pg_escape_string($anno_mostrado).") ".
		   "WHERE fecha_inicio::DATE='".pg_escape_string($fecha_inicio)."'::DATE ".
		   "AND fecha_termino::DATE='".pg_escape_string($fecha_termino)."'::DATE + '1 day'::INTERVAL ";
//	echo($sql);
	$res =& $mdb2->query($sql);
	if (MDB2::isError($res)) {
		//echo($sql);
		exit();
	}

	if ($res->numRows() == 0) {
		$fecha_inicio = 0;
		$fecha_termino = 0;
	}
	
	/* BUSCO LAS FECHAS A LA QUE PUEDE ACCEDER EL USUARIO */
	$sql = "SELECT date_part('month', fecha) as mes, * FROM public.reporte_periodico_informes(".
			pg_escape_string($usr->usuario_id).", ".
			pg_escape_string($_REQUEST["objeto_id"]).", ".
			pg_escape_string($anno_mostrado).") ".
		   "ORDER BY mes DESC, reporte_informe_subtipo_id DESC, fecha_termino";
//	echo($sql);
	$res =& $mdb2->query($sql);
	if (MDB2::isError($res)) {
		echo($sql);
		exit();
	}

	$fechas_meses = array();
	while ($row = $res->fetchRow()) {
		/* SI LAS FECHAS INGRESADAS NO EXISTIAN PARA EL USUARIO,
		 * LE ASIGNO LA PRIMERA DE LA LISTA */
		if ($fecha_inicio == 0 and $fecha_termino == 0) {
			$fecha_inicio = date("Y-m-d", strtotime($row["fecha_inicio"]));
			$fecha_termino = date("Y-m-d", strtotime($row["fecha_termino"])-1);
			$anno_mostrado = date("Y", strtotime($fecha_inicio));
			$mes_mostrado = date("n", strtotime($fecha_inicio));
		}
		$fechas_meses[date("n", strtotime($row["fecha"]))][] = $row;
	}
	
	$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
	$T->setFile('tpl_tabla', 'calendario_periodico.tpl');
	$T->setBlock('tpl_tabla', 'LISTA_MESES_NOMBRE', 'lista_meses_nombre');
	$T->setBlock('tpl_tabla', 'LISTA_CUATRIMESTRE', 'lista_cuatrimestre');
	$T->setBlock('tpl_tabla', 'LISTA_DIAS', 'lista_dias');
	$T->setBlock('tpl_tabla', 'LISTA_SEMANAS', 'lista_semanas');
	$T->setBlock('tpl_tabla', 'LISTA_REPORTES', 'lista_reportes');
	$T->setBlock('tpl_tabla', 'LISTA_MESES', 'lista_meses');

	$T->setVar('__anno', $anno_mostrado);
	$T->setVar('__nombre_clase', $nombre_clase);
	$T->setVar('__fecha_anterior', ($anno_mostrado-1)."-12-01");
	$T->setVar('__fecha_siguiente', ($anno_mostrado+1)."-01-01");
	$T->setVar('__fecha_inicio_periodo', $fecha_inicio);
	$T->setVar('__fecha_termino_periodo', $fecha_termino);

	/* LISTA DE MESES DEL CALENDARIO,
	 * SE MUESTRAN LOS QUE TIENEN Y NO TIENEN DATOS */
	$cnt_meses = 1;
	foreach ($meses_anno as $id => $nombre) {
		$T->setVar('__mes_id', $id);
		$T->setVar('__mes_nombre', $nombre);
		$T->setVar('__mes_color', ($fechas_meses[$id] && $id==$mes_mostrado)?"#f7af72":"#ffffff");
		
		if ($fechas_meses[$id]) {
			$inicio = date("Y-m-d", strtotime($fechas_meses[$id][0]["fecha_inicio"]));
			$termino = date("Y-m-d", strtotime($fechas_meses[$id][0]["fecha_termino"])-1);
			$T->setVar('__mes_class', "celda_mes");
			$T->setVar('__mes_script', $nombre_clase.".cargarCalendario('".$inicio."','".$termino."')");
		}
		else {
			$T->setVar('__mes_class', "celda_mes_desactivado");
			$T->setVar('__mes_script', "");
		}
		$T->parse('lista_meses_nombre', 'LISTA_MESES_NOMBRE', true);

		$cnt_meses++;
		if ($cnt_meses > 4) {
			$T->parse('lista_cuatrimestre', 'LISTA_CUATRIMESTRE', true);
			$T->setVar('lista_meses_nombre', '');
			$cnt_meses = 1;
		}
	}

	/* LISTA DE FECHAS PARA EL MES SELECCIONADO */
	if (isset($fechas_meses[$mes_mostrado]) and $fechas_meses[$mes_mostrado][0]["solo_mensual"] == "f") {
		$rep_dias = array();
		foreach ($fechas_meses[$mes_mostrado] as $rep) {
			$inicio = date("Y-m-d", strtotime($rep["fecha_inicio"]));
			$termino = date("Y-m-d", strtotime($rep["fecha_termino"])-1);
			
			if ($rep["reporte_informe_subtipo_id"]==1) {
				$rep_dias[] = $inicio;
			}
			else {
				$T->setVar('__reporte_id', 'periodo|'.$inicio.'_'.$termino);
				$T->setVar('__reporte_script', $nombre_clase.".seleccionarPeriodo('".$inicio."','".$termino."')");
				$T->setVar('__reporte_nombre', $rep["nombre"]);
				$T->setVar('__reporte_inicio', date("d/m/Y",strtotime($rep["fecha_inicio"])));
				$T->setVar('__reporte_termino', date("d/m/Y",strtotime($rep["fecha_termino"])-1));
				$T->parse('lista_reportes', 'LISTA_REPORTES', true);
			}
		}
		calendarioMes($mes_mostrado, $anno_mostrado, $fecha_minima, $fecha_maxima, $rep_dias);
	}
	
	$T->pparse('out','tpl_tabla');
	exit();
}

/* SI QUIERE MOSTRAR EL CALENDARIO NORMAL */
else {

//	$seccion_actual = $_SESSION["seccion_actual"];
	$nombre_clase = $_REQUEST["nombre_clase"];
	$fecha_mostrada = date("Y-m-1", strtotime($_REQUEST["fecha_mostrada"]));

	
	
	/* ES ES SOLO PARA LOS ELEMENTOS
	 * MUESTRA EL CALENDARIO PERO SOLO PARA UNA LISTA DE DIAS */
	if ($_SESSION["usa_calendario_limitado"]) {
		$rep_dias = array();
		for($i=6; $i>=0; $i--){
		$rep_dias[]= date("Y-m-d", strtotime(date("Y-m-d") . "-".$i." day"));
		}
		}else if($_SESSION["usa_calendario_limitado_sc"]){
		for($i=4; $i>=0; $i--){
		$rep_dias[]= date("Y-m-d", strtotime(date("Y-m-d") . "-".$i." day"));
		}
		}

	$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
	//$T->setFile('tpl_tabla', 'calendarioScreenShot.tpl');
	$T->setFile('tpl_tabla', 'calendario.tpl');
	$T->setBlock('tpl_tabla', 'LISTA_DIAS', 'lista_dias');
	$T->setBlock('tpl_tabla', 'LISTA_SEMANAS', 'lista_semanas');
	$T->setBlock('tpl_tabla', 'LISTA_MESES', 'lista_meses');
	

	$T->setVar('__fecha_mostrada_anterior',  date("Y-m-d", strtotime($fecha_mostrada." -1 month")));
	$T->setVar('__fecha_mostrada_siguiente', date("Y-m-d", strtotime($fecha_mostrada." +1 month")));
		
	/* SI SON ELEMENTOS, SOLO SE PUEDE SELECCION POR DIA 
	 * Y NO POR RANGOS DE FECHAS */
	if ($_SESSION["usa_calendario_limitado"]) {
		$T->setVar('__dia_default', $nombre_clase.".dia_default=1;");
		$T->setVar('__fecha_sel_disabled', 'disabled');
		if ($rep_dias == null or count($rep_dias) == 0) {
			$T->setVar('__texto_calendario', 'No se pueden seleccionar d&iacute;as ya que no se encontraron datos');
		}
		else {
			$T->setVar('__texto_calendario', 'Puede elegir cualquier d&iacute;a entre el '.date("d/m/Y", strtotime($rep_dias[0])).' y el '.date("d/m/Y", strtotime($rep_dias[count($rep_dias)-1])));
		}
	}
	else {
		$T->setVar('__dia_default', '');
		$T->setVar('__fecha_sel_disabled', '');
		$T->setVar('__texto_calendario', 'Puede elegir cualquier d&iacute;a o intervalo de d&iacute;as entre el '.date("d/m/Y", strtotime($fecha_minima)).' y el '.date("d/m/Y", strtotime($fecha_maxima)));
	}

	/* SE DIBUJAN LOS TRES MESES QUE SE MUESTRAN EN EL CALENDARIO */
	calendarioMes(date("n", strtotime($fecha_mostrada." -2 month")), date("Y", strtotime($fecha_mostrada." -2 month")), $fecha_minima, $fecha_maxima, $rep_dias);
	calendarioMes(date("n", strtotime($fecha_mostrada." -1 month")), date("Y", strtotime($fecha_mostrada." -1 month")), $fecha_minima, $fecha_maxima, $rep_dias);
	calendarioMes(date("n", strtotime($fecha_mostrada)), date("Y", strtotime($fecha_mostrada)), $fecha_minima, $fecha_maxima, $rep_dias);

	$T->pparse('out', 'tpl_tabla');
}


$mdb2->disconnect();

?>