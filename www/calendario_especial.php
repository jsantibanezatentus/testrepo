<?

include("../config/include.php");


$nombre_clase = $_REQUEST["nombre_clase"];
$objetivo_id = $_REQUEST["objetivo_id"];
$period = $_REQUEST["period"];
$period_start = $_REQUEST["period_start"];
$fecha_inicio = date("Y-m-d", strtotime($_REQUEST["fecha_inicio_mostrado"]));
$fecha_termino = date("Y-m-d", strtotime($_REQUEST["fecha_termino_mostrado"]));

$fecha_maxima = date("Y-m-d");
$fecha_minima = date("Y-m-d", strtotime("-6 months"));
$anno_mostrado = date("Y", strtotime($fecha_inicio));
$mes_mostrado = date("n", strtotime($fecha_inicio));

$T =& new Template_PHPLIB(REP_PATH_ESPECIALTEMPLATES);
$T->setFile('tpl_tabla', 'calendario_especial.tpl');
$T->setBlock('tpl_tabla', 'LISTA_REPORTES', 'lista_reportes');
$T->setBlock('tpl_tabla', 'LISTA_MESES_FIXED', 'lista_meses_fixed');
$T->setBlock('tpl_tabla', 'LISTA_SEMESTRE_FIXED', 'lista_semestre_fixed');
$T->setBlock('tpl_tabla', 'BLOQUE_CALENDARIO_FIXED', 'bloque_calendario_fixed');

$T->setBlock('tpl_tabla', 'LISTA_DIAS', 'lista_dias');
$T->setBlock('tpl_tabla', 'LISTA_SEMANAS', 'lista_semanas');
$T->setBlock('tpl_tabla', 'LISTA_MESES_FLEXIBLE', 'lista_meses_flexible');
$T->setBlock('tpl_tabla', 'LISTA_SEMESTRE_FLEXIBLE', 'lista_semestre_flexible');
$T->setBlock('tpl_tabla', 'BLOQUE_CALENDARIO_FLEXIBLE', 'bloque_calendario_flexible');

$T->setVar('__nombre_clase', $nombre_clase);
//$T->setVar('__fecha_inicio_periodo', $fecha_inicio);
//$T->setVar('__fecha_termino_periodo', $fecha_termino);

if ($period == "all") {
	$T->setVar('__texto_vista', 'Diario');
	$T->setVar('__calendario_flexible_display', 'none');
}

if ($period == "fixed" or $period == "all") {

	$T->setVar('__anno', $anno_mostrado);
	$T->setVar('__fecha_anterior', ($anno_mostrado-1)."-01-01");
	$T->setVar('__fecha_siguiente', ($anno_mostrado+1)."-01-01");
	
	/* VEO SI LAS FECHAS INGRESADAS PUEDEN SER USADAS POR EL USUARIO */
/*	$sql = "SELECT * FROM public.reporte_periodico_informes(".
			pg_escape_string($usr->usuario_id).", ".
			pg_escape_string($objetivo_id).", ".
			pg_escape_string($anno_mostrado).") ".
			"WHERE fecha_inicio::DATE='".pg_escape_string($fecha_inicio)."'::DATE ".
			"AND fecha_termino::DATE='".pg_escape_string($fecha_termino)."'::DATE + '1 day'::INTERVAL ";
	//	echo($sql);
	$res =& $mdb2->query($sql);
	if (MDB2::isError($res)) {
		//		echo($sql);
		exit();
	}
	
	if ($res->numRows() == 0) {
		$fecha_inicio = 0;
		$fecha_termino = 0;
	}*/
	
	/* BUSCO LAS FECHAS A LA QUE PUEDE ACCEDER EL USUARIO */
	$sql = "SELECT date_part('month', fecha) as mes, * FROM public.reporte_periodico_informes(".
			pg_escape_string($usr->usuario_id).", ".
			pg_escape_string($objetivo_id).", ".
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
//			$anno_mostrado = date("Y", strtotime($fecha_inicio));
//			$mes_mostrado = date("n", strtotime($fecha_inicio));
		}
		$fechas_meses[date("n", strtotime($row["fecha"]))][] = $row;
	}
	
	$cnt_fechas = 1;
	
	for ($fila = 1; $fila <= 6; $fila++) {
	
		$T->setVar('lista_meses_fixed', '');
		for ($col = 0; $col <= 1; $col++) {
			$id = $fila + ($col * 6);
				
			$T->setVar('__mes_id', $id);
			$T->setVar('__mes_nombre', $meses_anno[$id]);
	
			$T->setVar('lista_reportes', '');
			if (isset($fechas_meses[$id])) {
				foreach ($fechas_meses[$id] as $rep) {
					if (count($fechas_meses[$id]) > $cnt_fechas) {
						$cnt_fechas = count($fechas_meses[$id]);
					}
	
					$T->setVar('__reporte_nombre', $rep["nombre"]);
					$T->setVar('__reporte_inicio', date("d/m",strtotime($rep["fecha_inicio"])));
					$T->setVar('__reporte_termino', date("d/m",strtotime($rep["fecha_termino"])-1));
					$T->setVar('__interno_inicio', date("Y-m-d",strtotime($rep["fecha_inicio"])));
					$T->setVar('__interno_termino', date("Y-m-d",strtotime($rep["fecha_termino"])-1));
					$T->parse('lista_reportes', 'LISTA_REPORTES', true);
				}
			}
			$T->parse('lista_meses_fixed', 'LISTA_MESES_FIXED', true);
		}
		$T->parse('lista_semestre_fixed', 'LISTA_SEMESTRE_FIXED', true);
	}
	$T->parse('bloque_calendario_fixed', 'BLOQUE_CALENDARIO_FIXED', false);
}


if ($period == "flexible" or $period == "all") {

	if ($mes_mostrado >=1 and $mes_mostrado <=6) {
		$T->setVar('__fecha_anterior', ($anno_mostrado-1)."-07-01");
		$T->setVar('__fecha_siguiente', ($anno_mostrado)."-07-01");
		$T->setVar('__anno', $anno_mostrado);
		$inicio_fila = 1;
	}
	else {
		$T->setVar('__fecha_anterior', ($anno_mostrado)."-01-01");
		$T->setVar('__fecha_siguiente', ($anno_mostrado+1)."-01-01");
		$T->setVar('__anno', $anno_mostrado."/".($anno_mostrado+1));
		$inicio_fila = 7;
	}
	
	for ($fila = $inicio_fila; $fila < ($inicio_fila + 6); $fila++) {
	
		$T->setVar('lista_meses_flexible', '');
		for ($col = 0; $col <= 1; $col++) {
			$mes_actual = $fila + ($col * 6);
			
			if ($mes_actual > 12) {
				$mes_actual = $mes_actual - 12;
				$anno_actual = $anno_mostrado + 1;
			}
			else {
				$anno_actual = $anno_mostrado;
			}
	
			$T->setVar('__mes_id', $mes_actual);
			$T->setVar('__mes_nombre', $meses_anno[$mes_actual]);
	
			$ultimo_dia_mes = date("d", mktime(0, 0, 0, $mes_actual+1, 0, $anno_actual));
			$dia_actual = (2 - date("N", mktime(0, 0, 0, $mes_actual, 1, $anno_actual)));
			
			$T->setVar('lista_semanas', '');
			while ($dia_actual < 1 or $mes_actual == date("m", mktime(0, 0, 0, $mes_actual, $dia_actual, $anno_actual))) {
			
				$semana_actual = date("W", mktime(0, 0, 0, $mes_actual, $dia_actual, $anno_actual));
			
				$T->setVar('__semana_id', $semana_actual);
			
				$T->setVar('lista_dias', '');
				while ($semana_actual == date("W", mktime(0, 0, 0, $mes_actual, $dia_actual, $anno_actual))) {
					if ($dia_actual < 1 or $dia_actual > $ultimo_dia_mes) {
						$T->setVar('__dia_numero', '');
						$T->setVar('__dia_script', '');
						$T->setVar('__dia_id', '');
					}
					else {
						$time_aux = strtotime($anno_actual."-".$mes_actual."-".$dia_actual);
						$fecha_aux = date("Y-m-d", $time_aux);
						$T->setVar('__dia_numero', $dia_actual);
						if ($time_aux < strtotime($period_start) or $time_aux > time()) {
							$T->setVar('__dia_script', '');
							$T->setVar('__dia_id', '');
							$T->setVar('__dia_desactivado_style', 'text-decoration: line-through;');
						}
						else {
							$T->setVar('__dia_script', "$nombre_clase.seleccionarDia('dia|".$fecha_aux."')");
							$T->setVar('__dia_id', "dia|".$fecha_aux);
							$T->setVar('__dia_desactivado_style', '');
						}
					}
					$T->parse('lista_dias', 'LISTA_DIAS', true);
					$dia_actual++;
				}
				$T->parse('lista_semanas', 'LISTA_SEMANAS', true);
			}
			$T->parse('lista_meses_flexible', 'LISTA_MESES_FLEXIBLE', true);
		}
		$T->parse('lista_semestre_flexible', 'LISTA_SEMESTRE_FLEXIBLE', true);
	}
	$T->parse('bloque_calendario_flexible', 'BLOQUE_CALENDARIO_FLEXIBLE', false);
}

	
$T->setVar('__month_height', (($cnt_fechas>1)?($cnt_fechas*20):26));
$T->setVar('__period_height', (($cnt_fechas>1)?20:26));

$T->pparse('out','tpl_tabla');

$mdb2->disconnect();

?>