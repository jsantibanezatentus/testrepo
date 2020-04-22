<?

unset($_SESSION["fecha_tipo_id"]);

$T->parse('tiene_metadata', 'TIENE_METADATA', false);

$reporte = $sactual->getReporte($objeto_id);

if (isset($reporte)) {

$T->setFile('tpl_contenido', 'reporte.tpl');
$T->setBlock('tpl_contenido', 'BLOQUE_HORARIO', 'bloque_horario');
$T->setBlock('tpl_contenido', 'BLOQUE_SIN_HORARIO', 'bloque_sin_horario');
$T->setBlock('tpl_contenido', 'REPORTE_DESCRIPCION', 'reporte_descripcion');
$T->setBlock('tpl_contenido', 'LISTA_HORARIOS', 'lista_horarios');
$T->setBlock('tpl_contenido', 'TIENE_OBJETIVO', 'tiene_objetivo');
$T->setBlock('tpl_contenido', 'TIENE_IMPRESION', 'tiene_impresion');
$T->setBlock('tpl_contenido', 'TIENE_IMPRESION_FULL', 'tiene_impresion_full');
$T->setBlock('tpl_contenido', 'REPORTE_SECCIONES', 'reporte_secciones');
$T->setBlock('tpl_contenido', 'TIENE_HORARIO', 'tiene_horario');
$T->setBlock('tpl_contenido', 'TIENE_CALENDARIO', 'tiene_calendario');
$T->setBlock('tpl_contenido', 'TIENE_PERIODO', 'tiene_periodo');
$T->setBlock('tpl_contenido', 'TIENE_PERIODO_DEFAULT', 'tiene_periodo_default');
$T->setBlock('tpl_contenido', 'ITEMS_REPORTE', 'items_reporte');

$T->setVar('bloque_horario', '');
$T->setVar('bloque_sin_horario', '');
//$_SESSION["seccion_actual"] = $sactual->seccion_id;
$_SESSION["usa_calendario_limitado"] = $reporte->usa_calendario_limitado;
$timestamp = new Timestamp();
$horario_id = null;
$T->setVar('__hostname',getHostname());



if($menu_id!=92){
echo 'normal';
$_SESSION["usa_calendario_limitado"] = $reporte->usa_calendario_limitado;
}else{
echo 'sc', $reporte->usa_calendario_limitado;
$_SESSION["usa_calendario_limitado"] = 0;
$_SESSION["usa_calendario_limitado_sc"] = 1;
}

/* SI EL REPORTE USA CALENDARIO ONLINE */
if ($reporte->usa_calendario) {
	if (isset($_POST["fecha_inicio"]) and $_POST["fecha_inicio"] != "") {
		$timestamp = new Timestamp($_POST["fecha_inicio"], $_POST["fecha_termino"]);
	}
	

	/* ESTO ES SOLO PARA LOS ELEMENTOS,
	 * PORQUE DEBE MOSTRAR EL MISMO CALENDARIO PERO SOLO PARA UNA LISTA DE DIAS,
	 * ESTE IF SOLO VE SI LA FECHA SELECCIONADA ESTA DENTRO DE LA LISTA DE DIAS */
	elseif ($reporte->usa_calendario_limitado and isset($_SESSION["fecha_inicio"])) {
		$rep_dias = array();
		for($i=6; $i>=0; $i--){
			$rep_dias[]= date("Y-m-d 00:00:00", strtotime(date("Y-m-d") . "-".$i." day"));
 		}
		if (in_array($_SESSION["fecha_termino"], $rep_dias)) {
			$timestamp = new Timestamp($_SESSION["fecha_termino"], $_SESSION["fecha_termino"]);
		}
		else {
			$timestamp = new Timestamp(date("Y-m-d 00:00:00"), date("Y-m-d 00:00:00"));
		}
	}
	elseif (isset($_SESSION["fecha_inicio"]) and $_SESSION["fecha_inicio"] != "") {
		$timestamp = new Timestamp($_SESSION["fecha_inicio"], $_SESSION["fecha_termino"]);
	}
	else {
		$timestamp = new Timestamp();
	}
	$T->parse('tiene_calendario', 'TIENE_CALENDARIO', false);

	$_SESSION["usa_calendario"] = 1;
	$_SESSION["fecha_inicio"] = $timestamp->getFechaInicio();
	$_SESSION["fecha_termino"] = $timestamp->getFechaTermino();



}
else {
	$_SESSION["usa_calendario"] = 0;
}
//var_dump($_POST);


/* SI EL REPORTE USA CALENDARIO PERIODICO */
if ($reporte->usa_calendario_periodo) {
	if (isset($_POST["fecha_inicio_periodico"]) and $_POST["fecha_inicio_periodico"] != "") {

		$objetivo = $usr->getObjetivo($objeto_id);
		if ($objetivo->existeFechaPeriodico($_POST["fecha_inicio_periodico"], $_POST["fecha_termino_periodico"])) {
			$timestamp = new Timestamp($_POST["fecha_inicio_periodico"], $_POST["fecha_termino_periodico"]);
		}
		else {
			$anno = date("Y", strtotime($_POST["fecha_inicio_periodico"]));
			$fechas = $objetivo->getFechasPeriodicos($anno);
			$timestamp = $fechas[0];
		}
	}
	elseif (isset($_SESSION["fecha_inicio_periodico"]) and $_SESSION["fecha_inicio_periodico"] != "") {
//		echo("FECHA SESSION ".$_SESSION["fecha_inicio_periodico"]);
		$timestamp = new Timestamp($_SESSION["fecha_inicio_periodico"], $_SESSION["fecha_termino_periodico"]);
	}
	else {
		$timestamp = new Timestamp();
	}
	$T->parse('tiene_periodo', 'TIENE_PERIODO', false);

	$_SESSION["usa_calendario_periodico"] = 1;
	$_SESSION["fecha_inicio_periodico"] = $timestamp->getFechaInicio();
	$_SESSION["fecha_termino_periodico"] = $timestamp->getFechaTermino();
}
else {
	$_SESSION["usa_calendario_periodico"] = 0;
}

/* SI EL REPORTE USA HORARIO HABIL */
if ($reporte->usa_horario_habil) {
	if (isset($_POST["horario_id"]) and $_POST["horario_id"]!="") {
		if ($usr->solo_lectura) {
			$_SESSION["horario_id_".$objeto_id] = $_POST["horario_id"];
		}
		else {
			$usr->setHorarioPorDefecto($objeto_id, $_POST["horario_id"]);
		}
	}
	if ($usr->solo_lectura and isset($_SESSION["horario_id_".$objeto_id])) {
		$horario_id = $_SESSION["horario_id_".$objeto_id];
	}
	else {
		$horario_id = $usr->getHorarioPorDefecto($objeto_id);
	}

	$horarios = $usr->getHorarios(REP_HORARIO_HABIL);
	$T->setVar('lista_horarios', '');
	foreach ($horarios as $horario) {
		$T->setVar('__horario_id', $horario->horario_id);
		$T->setVar('__horario_nombre', $horario->nombre);
		$T->setVar('__horario_sel', ($horario->horario_id == $horario_id)?'checked':'');
		$T->parse('lista_horarios', 'LISTA_HORARIOS', true);
	}

	$_SESSION["usa_horario_habil"] = 1;

	$T->parse('tiene_horario', 'TIENE_HORARIO', false);
}
else {
	$_SESSION["usa_horario_habil"] = 0;
}

$intervalos = Constantes::getIntervalos(REP_INTERVALO_SEMAFORO);
/* DATOS DEL REPORTE */
$T->setVar('__reporte_id', $reporte->reporte_id);
$T->setVar('__reporte_titulo', $reporte->nombre);
$T->setVar('__fecha_inicio', $timestamp->getFechaInicio("Y-m-d"));
$T->setVar('__fecha_termino', $timestamp->getFechaTermino("Y-m-d"));

if ($sactual->objeto_id) {
	$objetivo = $usr->getConfigObjetivo($sactual->objeto_id, REP_DATOS_CLIENTE);

	$T->setVar('__objetivo_id', $objetivo->objetivo_id);
	$T->setVar('__objetivo_nombre', $objetivo->nombre);
	$T->setVar('__objetivo_intervalo', ($objetivo->intervalo_id)?"| Intervalo ".$objetivo->intervalo_nombre:"");
	$T->setVar('__objetivo_servicio', $objetivo->getServicio()->nombre);
	$T->parse('tiene_objetivo', 'TIENE_OBJETIVO', false);
}
else {
	$T->setVar('__objetivo_id', '<not set>');
}

if($reporte->muestra_intervalo_semaforo== 1){

	$T->setVar('__item_duracion', $intervalos[$usr->periodo_semaforo_id]);
	$T->parse('bloque_horario', 'BLOQUE_HORARIO', false);
}
else{
	$T->parse('bloque_sin_horario', 'BLOQUE_SIN_HORARIO', false);
}

/* SI USA CALENDARIO PERIODICO Y NO TIENE FECHAS SELECCIONADAS,
 * SE MUESTRA SOLO EL CALENDARIO DE PERIODICOS */
if ($reporte->usa_calendario_periodo and $menu_id == 58) {
	$T->parse('tiene_periodo_default', 'TIENE_PERIODO_DEFAULT', true);
	$T->setVar('__sitio_contenido', $T->parse('out', 'tpl_contenido'));
	$T->pparse('out', 'tpl_sitio');
	exit();
}

/* VERIFICO SI SE MUESTRA LA DESCRIPCION DEL INFORME (PSEUDO-AYUDA) */
if (isset($reporte->descripcion) and $reporte->descripcion != "") {
	$T->setVar('__reporte_descripcion', $reporte->descripcion);
	$T->parse('reporte_descripcion', 'REPORTE_DESCRIPCION', false);
}

/* VERIFICAR SI SE MUESTRA EL BOTON IMPRIMIR */
if ($reporte->getImpresionItems(REP_IMPRESION_INFORME)) {
	$T->parse('tiene_impresion', 'TIENE_IMPRESION', false);
}

/* VERIFICAR SI SE MUESTRA EL BOTON IMPRIMIR FULL */
if ($reporte->getImpresionItems(REP_IMPRESION_OBJETIVO)) {
	$T->parse('tiene_impresion_full', 'TIENE_IMPRESION_FULL', false);
}



/* INFORMES DE LA SECCION SELECCIONADA.
 * SE OBTIENEN LOS INFORMES ASOCIADOS A LA SECCION PADRE,
 * ES DECIR, LA SECCION ACTUAL Y SUS HERMANOS */
$secciones = $sactual->getSeccionesNivel(2);
foreach ($secciones as $seccion) {
/*	if ($seccion->tieneReporte($objeto_id) and $seccion->seccion_id == REP_INFORME_STRESS_SUBOBJETIVOS) {
		$stress = new ConfigStress($sactual->objeto_id);
		foreach ($stress->__subobjetivos as $subobjetivo) {
			if ($subobjetivo->objetivo_id == $sactual->subobjeto_id) {
				$reporte->nombre = $subobjetivo->nombre;
				$T->setVar('__reporte_seccion_class', 'textmenusuperiorsel');
			}
			else {
				$T->setVar('__reporte_seccion_class', 'textmenusuperior');
			}
			$T->setVar('__reporte_seccion_nombre', $subobjetivo->nombre);
			$T->setVar('__reporte_seccion_id', $seccion->seccion_id);
			$T->setVar('__reporte_subobjeto_id', $subobjetivo->objetivo_id);
			$T->parse('reporte_secciones', 'REPORTE_SECCIONES', true);
		}
	}*/
	if ($seccion->tieneReporte($objeto_id)) {
		$T->setVar('__reporte_seccion_nombre', $seccion->nombre);
		$T->setVar('__reporte_seccion_id', $seccion->seccion_id);
		$T->setVar('__reporte_seccion_class', ($seccion->es_parent)?'textmenusuperiorsel':'textmenusuperior');
//		$T->setVar('__reporte_subobjeto_id', 0);
		$T->parse('reporte_secciones', 'REPORTE_SECCIONES', true);
	}
}



/* ITEMS DEL REPORTE */
$T->setVar('items_reporte', '');

foreach ($reporte->getReporteItems() as $item) {
    /*ITEM ESPECIALES*/
    $itemEspecial = array (
        0 => "get_tiempo_respuesta_xy",
        1 => "get_tasa_errores_xy",
        2 => "get_apdex_puntuacion",
        3 => "get_tipo_error_torta",
        4 => "get_tiempo_respuesta_mas_elevado",
        5 => "get_iframe",
        6=>  "getLoadTimeBrowserXy",
        7=>  "getAvgLoadTimeBrowser",
        8=>  "getLoadUrl",
        9=>  "getErrorRateJS",
        10=> "getResponseAjax",
    	11=> "getAudex",
        12=> "getAvgInteractionApp",
        13=>"getUseVersionSO",
        14=>"getTimeResponseHTTP",
        15=>"getNumberErrors",
        16=>"getTimeInteractionEnabledDevice");

    $es_item_especial=false;
    /*BUCLE QUE BUSCA SI SE TRATA DE UN ITEM ESPECIAL*/
    for($i =0 ; $i < count($itemEspecial); $i++){
         /*EXCLUSIVO PARA ITEMS DE NEW RELIC*/
        if($itemEspecial[$i]==$item->metodo_nombre){
            $es_item_especial=true;
        }
    }
    /*SI ES ITEM ESPECIAL MOSTRAR UN TPL DE LO CONTRARIO EL OTRO*/
        $T->setFile('tpl_reporte_item',  ($es_item_especial==true)?'reporte_item_especial.tpl':'reporte_item.tpl');
        $T->setBlock('tpl_reporte_item', 'TIENE_GENERAR_REPORTE', 'tiene_generar_reporte');
        $T->setBlock('tpl_reporte_item', 'TIENE_UPDATE', 'tiene_update');

//	$descripcion = str_replace(array("{__intervalo_semaforo}"), array($intervalos[$usr->periodo_semaforo_id]), $item->descripcion);

	$T->setVar('__item_id', $item->item_id);
	$T->setVar('__item_titulo', $item->nombre);
	$T->setVar('__item_url', "/itemes/" . $item->nombre_url);
	$T->setVar('__item_descripcion', $item->descripcion);
	$T->setVar('__item_periodo', $timestamp->toString());
	$T->setVar('__item_horario', ($horario_id)?$usr->getHorario($horario_id)->nombre:'Todo Horario');

	
//	$T->setVar('__item_function_js', ($item->tipo == "tabla")?'cargarTabla':'cargarGrafico');




	$T->setVar('tiene_update','');
// 	$T->parse('tiene_update', 'TIENE_UPDATE', true);

    /*Solo para reportes Audex (setear load) */
        if($es_item_especial==true){
            $T->setVar('__tiene_carga', ($itemEspecial[11]==$item->metodo_nombre)?'block':'none');
        }

	/* SI EL ITEM TIENE EL SELECTOR DE CALENDARIO Y HORARIOS */
	if ($reporte->usa_calendario or $reporte->usa_calendario_periodo or $reporte->usa_horario_habil) {
		$T->parse('tiene_generar_reporte', 'TIENE_GENERAR_REPORTE', false);
	}

	$T->setVar('__reporte_item', $T->parse('out', 'tpl_reporte_item'));
	$T->parse('items_reporte', 'ITEMS_REPORTE', true);
}

}
else {
	$T->setFile('tpl_contenido', 'sorry_seccion.tpl');
}

$T->setVar('__sitio_contenido', $T->parse('out', 'tpl_contenido'));

?>
