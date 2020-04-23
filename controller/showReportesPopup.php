<?

$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);

$reporte_id = $_REQUEST["reporte_id"];
$item_id = $_REQUEST["item_id"];

/* SI SE QUIERE MOSTRAR EL POPUP DE UN ITEM DETERMINADO */
if (isset($item_id) and $item_id > 0) {

	/* GUARDO EL LOG */
	$log->setLog("POPUP");
	
	$item = new ReporteItem($item_id);
	$item->__ReporteItem();

	$T->setFile('tpl_contenido', 'reporte_item_popup.tpl');
	
	/* DATOS DEL ITEM*/ 
	$T->setVar('__item_id', $item->item_id);
	$T->setVar('__item_titulo', $item->nombre);
	# Datos Globales mantenimiento.
	$T->setVar('seccion', SECCION_MANTENIMIENTO);
	$T->setVar('calendario', SUB_SECCION_MANTENIMIENTO);
	$T->setVar('historial', SUB_SECCION_MANTENIMIENTO_HISTORIAL);
	$T->setVar('agregar', SUB_SECCION_MANTENIMIENTO_AGREGAR_EVENTO);
//	$T->setVar('__item_descripcion', $item->descripcion);
//	$T->setVar('__item_function_js', ($item->tipo == "tabla")?'cargarTabla':'cargarGrafico');
}

/* SI SE QUIERE MOSTRAR LA VISTA DE IMPRESION */
else {
	$tiene_svg = $_REQUEST["tiene_svg"];
	$tiene_flash = $_REQUEST["tiene_flash"];
	
	$_SESSION["ingreso_por_pdf"] = md5(time());

	/* GUARDO EL LOG */
	$log->setLog("IMPRIMIR");
	
	/* OBTENGO EL HORARIO HABIL DEL REPORTE */
	if ($_SESSION["usa_horario_habil"]) {
		if (isset($_REQUEST["horario_id"])) {
			$horario_id = $_REQUEST["horario_id"];
			if ($usr->solo_lectura) {
				$_SESSION["horario_id_".$objeto_id] = $horario_id;
			}
			else {
				$usr->setHorarioPorDefecto($objeto_id, $horario_id);
			}
		}
		elseif (isset($_SESSION["horario_id_".$objeto_id])) {
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
	if (isset($_REQUEST["fecha_inicio_periodo"]) and isset($_REQUEST["fecha_termino_periodo"])) {
		$_SESSION["fecha_inicio_periodico"] = $_REQUEST["fecha_inicio_periodo"];
		$_SESSION["fecha_termino_periodico"] = $_REQUEST["fecha_termino_periodo"];
		$timestamp = new Timestamp($_REQUEST["fecha_inicio_periodo"], $_REQUEST["fecha_termino_periodo"]); 
	}
	elseif ($_SESSION["usa_calendario_periodico"]) {
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
	
	/* VER QUE TEMPLATES USAR, EL DE LA IMPRESION O EL DEL PDF */
	if (isset($_REQUEST["pdf"])) {
		if ($_REQUEST['multi_obj'] < 1) {
			$T->setFile('tpl_contenido',  'reporte_pdf.tpl');			
		}else{
			$T->setFile('tpl_contenido',  'reporte_pdf_multi_obj.tpl');
		}
	}
	else {
		$T->setFile('tpl_contenido',  'reporte_impresion.tpl');
		$T->setBlock('tpl_contenido', 'LISTA_GRAFICOS_CHART', 'lista_graficos_chart');
		$T->setBlock('tpl_contenido', 'LISTA_GRAFICOS_GANTT', 'lista_graficos_gantt');
		$T->setVar('seccion', SECCION_MANTENIMIENTO);
		$T->setVar('calendario', SUB_SECCION_MANTENIMIENTO);
		$T->setVar('historial', SUB_SECCION_MANTENIMIENTO_HISTORIAL);
		$T->setVar('agregar', SUB_SECCION_MANTENIMIENTO_AGREGAR_EVENTO);
	}
	
//	$T->setBlock('tpl_contenido', 'TIENE_DETALLE', 'tiene_detalle');
	$T->setBlock('tpl_contenido', 'LISTA_GRAFICOS', 'lista_graficos');
	$T->setBlock('tpl_contenido', 'LISTA_TABLAS', 'lista_tablas');
	$T->setBlock('tpl_contenido', 'LISTA_ITEMS', 'lista_items');
//	$T->setBlock('tpl_contenido', 'LISTA_REPORTES', 'lista_reportes');

	/* OBTENGO LA SECCION ACTUAL Y LOS INFORMES A IMPRIMIR 
	 * SI $reporte_id = 0 => IMPRIMO TODOS LOS INFORMES
	 * SI $reporte_id = $seccion_id => IMPRIMO LA SECCION ACTUAL */
	$sactual = Seccion::getSeccionPorDefecto($menu_id);	
	$sactual->objeto_id = $objeto_id;
	if ($reporte_id == 0) {
		$informes = $sactual->getSeccionesNivel(2);
		$menu = new Seccion($sactual->padre_id);
		$menu->__Seccion();
		$T->setVar('__reporte_titulo', $menu->nombre);
	}
	else {
		$informes[] = $sactual;
		$T->setVar('__reporte_titulo', $sactual->nombre);
	}
	
	$T->setVar('__objetivo_nombre', ($objeto_id)?$usr->getObjetivo($objeto_id)->nombre:'');
	$T->setVar('__fecha_inicio', $timestamp->getFechaInicio("d/m/Y H:i:s"));
	$T->setVar('__fecha_termino', date("d/m/Y 24:00:00", strtotime($timestamp->getFechaTermino("Y-m-d H:i:s"))));
	
	$indice_global = 0;
	$indice_chart = 0;
	$indice_gantt = 0;

	/* LISTA DE INFORMES DE LA SECCION */
	$orden= 1;
	foreach ($informes as $seccion) {

		if (!$seccion->tieneReporte($objeto_id)) {
			continue;
		}

		/* CREO EL REPORTE QUE SE QUIERE IMPRIMIR */
		$reporte = $seccion->getReporte($objeto_id);

		/* SI ES STRESS DE CREA UN INFORME POR CADA SUBOBJETIVO */
/*		if ($seccion->seccion_id == REP_INFORME_STRESS_SUBOBJETIVOS and $subobjeto_id == 0) {
			$stress = new ConfigStress($objeto_id);
			$subobjetivos = array_keys($stress->__subobjetivos);
		}
		else {
			$subobjetivos = array($subobjeto_id);
		}*/
			
/*		if ($reporte->usa_calendario OR $reporte->usa_calendario_periodo) {
			$T->parse('tiene_detalle', 'TIENE_DETALLE', false);
		}
		else {
			$T->setVar('tiene_detalle', '');
		}*/
	
		/* LISTA DE SUBOBJETIVOS, SI NO TIENE EJECUTA UNA SOLA VEZ */
//		foreach ($subobjetivos as $subobjetivo_id) {
	
			if ($_REQUEST["imprimir_item_id"]) {
				$item = new ReporteItem($_REQUEST["imprimir_item_id"]);
				$item->__ReporteItem();
				$items = array($item);				
			}
			else {
				$items = $reporte->getReporteItems(($reporte_id)?REP_IMPRESION_INFORME:REP_IMPRESION_OBJETIVO);
			}
			
			if (count($items) > 0) {
				
				/* SE CAMBIA EL NOMBRE DEL REPORTES POR EL DEL SUBOJETIVO */
/*				if (isset($stress->__subobjetivos[$subobjetivo_id])) {
					$reporte->nombre = $stress->__subobjetivos[$subobjetivo_id]->nombre;
				}*/

				if (isset($_REQUEST["fecha_monitoreo"])) {
					$T->setVar('__elemento_plus', "&fecha_monitoreo=".$_REQUEST["fecha_monitoreo"]."&paso_id=".$_REQUEST["paso_id"]."&monitor_id=".$_REQUEST["monitor_id"]."&imprimir_item_id=".$_REQUEST["imprimir_item_id"]);
				}
				
//				$T->setVar('__reporte_titulo', $reporte->nombre);
//				$T->setVar('__reporte_descripcion', ($objeto_id)?$usr->getObjetivo($objeto_id)->nombre:$reporte->nombre);
//				$T->setVar('__reporte_periodo', $timestamp->toString());
//				$T->setVar('__reporte_horario', ($horario_id)?$usr->getHorario($horario_id)->nombre:'Todo Horario');
	
				/* ITEMS DEL REPORTE */
//				$T->setVar('lista_items', '');
				foreach ($items as $item) {
					//discrimina elementos plus de reportes full impresos
					if ($reporte_id == 0 && $item->item_id == 60) {
						continue;
					}
/*					if($nitems == count($items)){
						$T->setVar('__page_break', 'auto');
					}
					else{
						$T->setVar('__page_break', 'always');
						$nitems++;
					}*/
//					$T->setVar('__page_break', ($orden == count($items))?'auto':'always');
					$T->setVar('__item_id', $item->item_id);
					$T->setVar('__item_titulo', $item->nombre);
					$T->setVar('__item_orden', $orden);
					$T->setVar('__item_descripcion', $item->descripcion);

					$T->setVar('lista_tablas', '');
					$T->setVar('lista_graficos', '');

					$clase = $item->getContenido($tiene_svg, $tiene_flash);
					
					$clase->tipo = "html";
					$clase->objetivo_id = $objeto_id;
//					$clase->subobjetivo_id = $subobjeto_id;
					
					$clase->horario_id = $horario_id;
					$clase->timestamp = $timestamp;

					// TODO: quitar el subgrafico_id
					$clase->subgrafico_id = 1;
					$clase->extra = array("imprimir" => 1, "monitor_id" => $_REQUEST["monitor_id"], "paso_id" => $_REQUEST["paso_id"], "fecha_monitoreo" => $_REQUEST["fecha_monitoreo"], "item_orden" => $orden);
//					echo("<pre>");
//					print_r($clase);
					/*cuando es descarga de pdf (soluciona temas de grafico)*/
                                        if($_REQUEST["es_pdf"]==true){
                                            $es_pdf =$_REQUEST["es_pdf"];
                                            $clase->generarResultado($es_pdf);
                                        }
                                        else{
                                            $clase->generarResultado(false);
                                        }
					/* SI EL ITEM ES UNA TABLA */
					if (!isset($clase->path_flash) or $clase->path_flash == null) {
						$T->setVar('__item_contenido', $clase->resultado);
						$T->parse('lista_tablas', 'LISTA_TABLAS', true);
					}
					
					/* SI EL ITEM ES UNO O VARIOS GRAFICOS */
					else {
						if ($clase->__subgraficos == null) {
							$subgraficos = array(0);
						}
						else {
							$subgraficos = $clase->__subgraficos;
						}
						
						/* RECORRO TODOS LOS GRAFICOS GENERADOS DENTRO DE UN ANYCHART */
						foreach ($subgraficos as $subgrafico_id) {

							if (isset($_REQUEST["pdf"])) {
								$T->setVar('__grafico_indice', $indice_global);
							}
							else {
								$url_xml = "index.php?sitio_id=".$sitio_id."&menu_id=".$menu_id.
										   "&objeto_id=".$objeto_id.
//										   "&subobjeto_id=".$subobjetivo_id.
										   "&ejecutar_accion=1&accion=buscar_item".
										   "&item_id=".$item->item_id."&item_tipo=xml".
										   "&subgrafico_id=".$subgrafico_id."&tiene_flash=1";

								$T->setVar('__grafico_indice', $indice_global);
								$T->setVar('__grafico_tamanno', $clase->ancho."|".$clase->alto);
								$T->setVar('__grafico_xml', $url_xml);
								$T->setVar('__grafico_cnt_draw', ($clase->__subgraficos == null)?1:2);
			
								if ($clase->path_flash == REP_PATH_ACHART) {
									$T->setVar('__grafico_indice_chart', $indice_chart);
									$T->parse('lista_graficos_chart', 'LISTA_GRAFICOS_CHART', true);
									$indice_chart++;
								}
								else {
									$T->setVar('__grafico_indice_gantt', $indice_gantt);
									$T->parse('lista_graficos_gantt', 'LISTA_GRAFICOS_GANTT', true);
									$indice_gantt++;
								}
							}								
							$T->parse('lista_graficos', 'LISTA_GRAFICOS', true);
							$indice_global++;
						}
					}
					$T->parse('lista_items', 'LISTA_ITEMS', true);
					$orden++;
				}
//				$T->parse('lista_reportes', 'LISTA_REPORTES', true);
			}
//		}
	}
}

$paises = Constantes::getPaises();
$T->setVar('__pais_email', $paises[$usr->pais_id]["soporte_email"]);
$T->setVar('__pais_telefono', $paises[$usr->pais_id]["soporte_telefono"]);

/* PATH DE ARCHIVOS EXTERNOS */
$T->setVar('__path_dojo', REP_PATH_DOJO);
$T->setVar('__path_jquery_ui', REP_PATH_JQUERY_UI);
$T->setVar('__path_js', REP_PATH_JS);
$T->setVar('__path_img', REP_PATH_IMG);
$T->setVar('__path_img_boton', REP_PATH_IMG_BOTONES);
$T->setVar('__path_anychart', REP_PATH_JSCHART);
$T->setVar('__path_swf_anychart', REP_PATH_ACHART);
$T->setVar('__path_swf_anygantt', REP_PATH_AGANTT);

/* DATOS DE LA SECCION ACTUAL */
$T->setVar('__sitio_id', $sitio_id);
$T->setVar('__menu_id', $menu_id);
$T->setVar('__objeto_id', $objeto_id);
//$T->setVar('__subobjeto_id', $subobjeto_id);
$T->setVar('__reporte_id', $reporte_id);
$T->setVar('__sitio_anno', date("Y"));

$T->pparse('out', 'tpl_contenido');

?>
