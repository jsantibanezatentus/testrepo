<?
unset($_SESSION["usa_horario_habil"]);
unset($_SESSION["usa_periodo_semaforo"]);
unset($_SESSION["usa_calendario_periodico"]);
unset($_SESSION["usa_calendario"]);
unset($_SESSION["fecha_inicio_periodico"]);
unset($_SESSION["fecha_termino_periodico"]);
unset($_SESSION["fecha_tipo_id"]);

$api = $_REQUEST["api"];
$arrayObjId = $_REQUEST["select_obj"];
$arrayObjId = base64_decode($arrayObjId);
$arrayObjId = json_decode($arrayObjId);

$objetivo = new ConfigEspecial($objeto_id);
if (isset($_REQUEST["popup"])) {

	/* SUBOBJETIVO */
	if (isset($_REQUEST["subobjetivo_id"]) and $_REQUEST["subobjetivo_id"] != null) {
		$subobjetivo = $objetivo->getSubobjetivo($_REQUEST["subobjetivo_id"]);
		if ($subobjetivo == null) {
			$log->getSorryServer();
			exit;
		}
	}

	/* HORARIO */
	
	if (isset($_REQUEST["horario_id"])) {
		$horario = $objetivo->getHorario($_REQUEST["horario_id"]);

		if ($horario == null) {
			$log->getSorryServer();
			exit;
		}

		$usr->setHorarioPorDefecto($objetivo->objetivo_id, $horario->horario_id);
		$_SESSION["usa_horario_habil"] = 1;
	}


	if (isset($_REQUEST["fecha_inicio_periodico"]) and isset($_REQUEST["fecha_termino_periodico"])) {

		if ($objetivo->date_selection != "day" and $objetivo->report_list == "true" and !$objetivo->existeFechaPeriodico($_REQUEST["fecha_inicio_periodico"], $_REQUEST["fecha_termino_periodico"])) {
			$log->getSorryServer();
			exit;
		}
		elseif ($objetivo->date_selection == "day" and (strtotime($_REQUEST["fecha_inicio_periodico"]) < strtotime($objetivo->period_start) or strtotime($_REQUEST["fecha_termino_periodico"]) > strtotime(date("Y-m-d 24:00:00")))) {
			$log->getSorryServer();
			exit;
		}


		$timestamp = new Timestamp($_REQUEST["fecha_inicio_periodico"], $_REQUEST["fecha_termino_periodico"]);
		($_REQUEST["reporte_informe_subtipo_id"]!='')?$timestamp->tipo_id = $_REQUEST["reporte_informe_subtipo_id"]:$timestamp->tipo_id = 3;
		$timestamp->tipo_periodo = "especial";

		$_SESSION["fecha_inicio_periodico"] = $timestamp->getFechaInicio();
		$_SESSION["fecha_termino_periodico"] = $timestamp->getFechaTermino();
		$_SESSION["fecha_tipo_id"] = $timestamp->tipo_id;
		$_SESSION["usa_calendario_periodico"] = 1;
	}
	if(isset($_REQUEST["tipo_content"])){
		$type = $objetivo->getType($_REQUEST["tipo_content"]);
	}
	else{
		$type = $objetivo->getType($_REQUEST["tipo_id"], true);
	}

	if (isset($_REQUEST["cache"]) and $_REQUEST["cache"] == 1) {
		$objetivo->setCache();
	}

	/* SI EL REPORTE ESPECIAL MUESTRA UN PDF */
	if($type->content == 'pdf' && !isset($_REQUEST["es_especial"])){
		$_SESSION["ingreso_por_pdf"] = md5(time());
		if($objetivo->position=='landscape'){
			$position=true;
		}else{
			$position=false;
		}
		$html = REP_DOMINIO."descargar_pdf.php?popup=1&es_especial=true&tiene_svg=1&es_pdf=true&select_obj=".$_REQUEST['select_obj']."&position=".$position;
		foreach ($_REQUEST as $nombre => $valor) {
			$html.="&".$nombre."=".urlencode($valor);
		}
		header("Location:".$html);
		
//		header("Location: descargar_pdf.php?sitio_id=1&menu_id=34&objeto_id=".$objetivo->objetivo_id."&subobjetivo_id=".$_REQUEST["subobjetivo_id"]."&popup=1&fecha_inicio_periodico=".$_REQUEST["fecha_inicio_periodico"].
//		"&fecha_termino_periodico=".$_REQUEST["fecha_termino_periodico"]."&reporte_informe_subtipo_id=".$_REQUEST["reporte_informe_subtipo_id"].
//		"&tipo_id=".$_REQUEST["tipo_id"]."&nodos_seleccionados=".$_REQUEST["nodos_seleccionados"]."&es_especial=true&tiene_svg=1&tiene_flash=0");
	}elseif($type->content == 'docx' && !isset($_REQUEST["es_especial"])){
		$_SESSION["ingreso_por_word"] = md5(time());
		
		$horarios = $objetivo->__horarios;
		foreach ($horarios as $tag_horario){
			$horario = $tag_horario->horario_id;
		}
		
		$html = REP_DOMINIO."descarga_word.php?popup=1&es_especial=true&tiene_svg=1&horario_id=".$horario;
		foreach ($_REQUEST as $nombre => $valor) {
			$html.="&".$nombre."=".urlencode($valor);
		}
		header("Location:".$html);
	}

	/* SI EL REPORTE ESPECIAL MUESTRA UN INFORME */
	elseif ($type != null and $type->informe_id != null) {
		
		$reporte = new Reporte($type->informe_id);     
		$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
		if(isset($_REQUEST["pdf"])){
			if($objetivo->position=='landscape'){
				$T->setFile('tpl_contenido',  'reporte_hacienda_pdf.tpl');
			}elseif ($_REQUEST["multi_obj"] > 0) {
				$T->setFile('tpl_contenido','reporte_multi_obj_especial_pdf.tpl');
			}elseif ($_REQUEST["contraloria"] > 0){
				$T->setFile('tpl_contenido',  'reporte_especial_contraloria.tpl');
			}else{
				if ($_REQUEST["pdf_especial"] == 0) {
					if ($_REQUEST['mostrar_grafico']=='false') {
						$T->setVar('__mostrar_grafico','none' );
					}

					if ($_REQUEST['mostrar_contacto']=='false') {
						$T->setVar('__mostrar_contacto','none' );
					}

					if ($_REQUEST['item_bloque']!='false') {
						$T->setVar('__item_bloque','always' );
					}

					$T->setFile('tpl_contenido',  'reporte_pdf.tpl');
				}else{
					$T->setFile('tpl_contenido',  'reporte_pdf_especial.tpl');
				}				
			}

		}elseif (isset($_REQUEST["word"])){
			$T->setFile('tpl_contenido',  'reporte_word.tpl');
		}elseif ($_REQUEST["multi_obj"] > 0){
			$T->setFile('tpl_contenido',  'reporte_especial_multi_objetivos.tpl');
		}elseif ($_REQUEST["img_especial"] > 0){
			$T->setFile('tpl_contenido',  'reporte_especial_img_especial.tpl');
		}elseif ($_REQUEST["comparativo"] > 0){
			$T->setFile('tpl_contenido',  'reporte_especial_comparativo.tpl');
		}elseif ($_REQUEST["contraloria"] > 0){
			$T->setFile('tpl_contenido',  'reporte_especial_contraloria.tpl');
		}else{
			$T->setFile('tpl_contenido','reporte_especial.tpl');
		}

		$T->setVar('__fecha_inicio', $timestamp->getFechaInicio("d/m/Y H:i:s"));
		if ($timestamp->getFechaTermino("H:i:s") != "00:00:00") {
			$T->setVar('__fecha_termino', $timestamp->getFechaTermino("d/m/Y H:i:s"));
		}
		else {
			if ($_REQUEST["img_especial"] == 0) {
				if ($_REQUEST["pdf_especial"] == 0) {
					$T->setVar('__fecha_termino', date("d/m/Y 24:00:00", (strtotime($timestamp->getFechaTermino("Y-m-d H:i:s")) - 1)));
				}else{
					$T->setVar('__fecha_termino', date("d/m/Y 23:59:59", (strtotime($timestamp->getFechaTermino("Y-m-d H:i:s")) - 1)));
				}
			}else{
				$sql2 = "SELECT _cliente_tz(".pg_escape_string($current_usuario_id).")";
				$res0 =& $mdb2->query($sql2);
				if (MDB2::isError($res0)) {
					$log->setError($sql2, $res0->userinfo);
					exit();
				}
				$row = $res0->fetchRow();
				$tz= $row["_cliente_tz"];
				$fecha_actual = time();				
				$dt = new DateTime("now", new DateTimeZone($tz));
				$dt->setTimestamp($fecha_actual);
				
				if (     strtotime($timestamp->getFechaTermino())    >    strtotime($dt->format("Y-m-d H:i:s"))  ) {
					$T->setVar('__fecha_termino', date("d/m/Y H:i:s", strtotime($dt->format("Y-m-d H:i:s"))));
				}else{
					$sql2 = "SELECT _cliente_tz(".pg_escape_string($current_usuario_id).")";
					$res0 =& $mdb2->query($sql2);
					if (MDB2::isError($res0)) {
						$log->setError($sql2, $res0->userinfo);
						exit();
					}
					$row = $res0->fetchRow();
					$tz= $row["_cliente_tz"];
					$fecha_actual = time();
					$dt = new DateTime("now", new DateTimeZone($tz));
					$dt->setTimestamp($fecha_actual);

					if (     strtotime($timestamp->getFechaTermino())    >    strtotime($dt->format("Y-m-d H:i:s"))  ) {
						$T->setVar('__fecha_termino', date("d/m/Y H:i:s", strtotime($dt->format("Y-m-d H:i:s"))));
					}else{
						$T->setVar('__fecha_termino', date("d/m/Y 23:59:59", (strtotime($timestamp->getFechaTermino("Y-m-d H:i:s")) - 1)));
					}
				}
			}
		}
		if ($_REQUEST["comparativo"] == 1) {
			$T->setVar('__fecha_termino', date("d/m/Y 23:59:59", (strtotime($timestamp->getFechaTermino("Y-m-d H:i:s")) - 1)));
		}
		if($api==true){
			$T->setVar('__fecha_termino', date("d/m/Y 24:00:00", (strtotime($timestamp->getFechaTermino("Y-m-d H:i:s")))));
		}

		$paises = Constantes::getPaises();
		$T->setVar('__pais_email', $paises[$usr->pais_id]["soporte_email"]);
		$T->setVar('__pais_telefono', $paises[$usr->pais_id]["soporte_telefono"]);
		$T->setVar('__reporte_titulo', $objetivo->nombre);
		$T->setVar('__parent_objetivo_id', $objetivo->objetivo_id);
		if (isset($_REQUEST["subobjetivo_id"]) and $_REQUEST["subobjetivo_id"] != null) {
			$T->setVar('__objetivo_id', $subobjetivo->objetivo_id);
			$T->setVar('__objetivo_nombre', $subobjetivo->nombre);
		}
		else {
			$T->setVar('__objetivo_id', $objetivo->objetivo_id);
			$T->setVar('__objetivo_nombre', '');
		}


		$T->setBlock('tpl_contenido', 'LISTA_ITEMS', 'lista_items');
		$T->setBlock('tpl_contenido', 'LISTA_SUBOBJETIVOS', 'lista_subobjetivos');
		/* BORRAR VARIABLE CUANDO SE ELIMINE FLASH */
		
		if($_REQUEST["tipo_obj"]>0 and $_REQUEST["multi_obj"] < 1){
			/* ITEMS DEL REPORTE */
			$T->setVar('items_reporte', '');

			$linea = 1;
			$grafico = $_REQUEST['tipo_obj'];
			$subobjetivos = $objetivo->getSubobjetivos();
			
			foreach($subobjetivos as $subobjetivo){
				foreach ($reporte->getReporteItems() as $item) {
					$clase = $item->getContenido($_REQUEST["tiene_svg"], $_REQUEST["tiene_flash"]);
					$clase->tipo = "html";
					$clase->objetivo_id = ($subobjetivo != null)?$subobjetivo->objetivo_id:$objetivo->objetivo_id;
					$clase->horario_id = ($horario != null)?$horario->horario_id:0;
					$clase->timestamp = $timestamp;
					$clase->subgrafico_id = 2;
					$clase->extra = array("imprimir" => 1, "item_orden" => $linea, "parent_objetivo_id" => $objetivo->objetivo_id, "reporte_id" => $type->informe_id, "segmento_id" => $_REQUEST["segmento_id"], "monitor"=> $grafico);
					$clase->generarResultado(false);

					$T->setVar('__item_id', $item->item_id);
					$T->setVar('__item_titulo', $item->nombre);
					$T->setVar('__item_orden', $linea);
					$T->setVar('__item_descripcion', ($objetivo->display_description == "true")?$item->descripcion:"");
					$T->setVar('__item_contenido', $clase->resultado);
					$T->parse('lista_items', 'LISTA_ITEMS', true);
					$linea++;
					$grafico++;
				}
			}
		}elseif ($_REQUEST["tipo_obj"] > 0 and $_REQUEST["multi_obj"] > 0) {
			$subobjetivos = $objetivo->getSubobjetivos();

			$objetivo = new ConfigEspecial($objetivo->objetivo_id);
			$horarios = $objetivo->getHorarios();
			
			$T->setVar('lista_subobjetivos', '');
			foreach($subobjetivos as $subobjetivo){
				$T->setVar('__nombre_objetivo', $subobjetivo->nombre);
				$T->setVar('lista_items', '');
				$linea = 1;
				$grafico = $_REQUEST['tipo_obj'];
				foreach ($reporte->getReporteItems() as $item) {
					foreach ($horarios as  $horario_item) {
						$clase = $item->getContenido($_REQUEST["tiene_svg"], $_REQUEST["tiene_flash"]);
						$clase->tipo = "html";
						$clase->objetivo_id = ($subobjetivo != null)?$subobjetivo->objetivo_id:$objetivo->objetivo_id;
						$clase->horario_id = $horario_item->horario_id;
						$clase->timestamp = $timestamp;
						$clase->subgrafico_id = 2;
						$clase->extra = array("imprimir" => 1, "item_orden" => $linea, "parent_objetivo_id" => $objetivo->objetivo_id, "reporte_id" => $type->informe_id, "segmento_id" => $_REQUEST["segmento_id"], "monitor"=> $grafico, "horario_id_item" => $horario_item->horario_id, "horario_nombre_item" => $horario_item->nombre);
						$clase->generarResultado(false);
						$T->setVar('__horario_nombre_item', $horario_item->nombre);
						$T->setVar('__item_id', $item->item_id);
						$T->setVar('__item_titulo', $item->nombre);
						$T->setVar('__item_orden', $linea);
						$T->setVar('__item_descripcion', ($objetivo->display_description == "true")?$item->descripcion:"");
						$T->setVar('__item_contenido', $clase->resultado);
						$T->parse('lista_items', 'LISTA_ITEMS', true);
						$linea++;
						$grafico++;
					}
				}
				$T->parse('lista_subobjetivos', 'LISTA_SUBOBJETIVOS', true);
			}
		}else {
			/* ITEMS DEL REPORTE */

			$T->setVar('items_reporte', '');
			
			if($horario==null){
				$horarios = $objetivo->getHorarios();
			}else{
				$horarios = null;
			}
			$linea = 1;
			foreach ($reporte->getReporteItems() as $item) {

				$clase = $item->getContenido($_REQUEST["tiene_svg"], $_REQUEST["tiene_flash"]);
				$clase->tipo = "html";
				$clase->objetivo_id = ($subobjetivo != null)?$subobjetivo->objetivo_id:$objetivo->objetivo_id;
				$clase->horario_id = ($horario != null)?$horario->horario_id:0;
				$clase->timestamp = $timestamp;
				$clase->subgrafico_id = 2;
				$clase->extra = array("imprimir" => 1, "item_orden" => $linea, "parent_objetivo_id" => $objetivo->objetivo_id, "reporte_id" => $type->informe_id, "segmento_id"  => $_REQUEST["segmento_id"],"horarios"=>$horarios, 'select_obj'=>$arrayObjId);
				if($_REQUEST["es_pdf"]==true){
					$es_pdf =$_REQUEST["es_pdf"];
					$clase->generarResultado($es_pdf);
				}
				else{
					$es_pdf =false;
					$clase->generarResultado($es_pdf);
				}

				$T->setVar('__item_id', $item->item_id);
				$T->setVar('__item_titulo', $item->nombre);
				$T->setVar('__item_orden', $linea);
				$T->setVar('__item_descripcion', ($objetivo->display_description == "true")?$item->descripcion:"");
				$T->setVar('__item_contenido', $clase->resultado);
				$T->parse('lista_items', 'LISTA_ITEMS', true);
				$linea++;
			}
		}
		$T->setVar('__path_dojo', REP_PATH_DOJO);
		$T->setVar('__path_jquery_ui', REP_PATH_JQUERY_UI);
		$T->setVar('__path_js', REP_PATH_JS);

		# Datos Globales mantenimiento.
		$T->setVar('seccion', SECCION_MANTENIMIENTO);
		$T->setVar('calendario', SUB_SECCION_MANTENIMIENTO);
		$T->setVar('historial', SUB_SECCION_MANTENIMIENTO_HISTORIAL);
		$T->setVar('agregar', SUB_SECCION_MANTENIMIENTO_AGREGAR_EVENTO);

		$T->pparse('out', 'tpl_contenido');
	}

	/* SI EL REPORTE ESPECIAL EJECUTA UN METODO DE UNA CLASE */
	elseif ($type != null and $type->class != null and $type->method != null) {

		if($type->file_name){
			$search = array('%yyyy', '%mm', '%dd', '%hh', '%objetivo_nombre');
			$replace = array($timestamp->getFechaInicio("Y"), $timestamp->getFechaInicio("m"), $timestamp->getFechaInicio("d"), isset($horario)?$horario:'', str_replace (" ", "_", $objetivo->nombre));
			$nombre_archivo = str_replace($search, $replace, $type->file_name);
		}
		else{
			$nombre_archivo = str_replace (" ", "_", $objetivo->nombre)."_".$timestamp->getFechaInicio("Y-m-d")."_".$timestamp->getFechaTermino("Y-m-d");
		}
		
		ob_clean();
		header('Content-Transfer-Encoding: none');
		header("Content-Type: ".$header_type[$type->content].";");
		header('Content-Disposition: attachment; filename="'.$nombre_archivo.'.'.$type->content.'"');
		
		$contenido = $type->getContenido();
		if (isset($_REQUEST["subobjetivo_id"])) {
			$contenido->objetivo_id = $subobjetivo->objetivo_id;
		}
		else {
			$contenido->objetivo_id = $objetivo->objetivo_id;
		}
		if($type->method=='getTagBanco'){
			$horario->horario_id= $_REQUEST["horario_id"];
		}
		$contenido->horario_id = ($horario == null)?0:$horario->horario_id;
		$contenido->timestamp = $timestamp;

		$contenido->extra = array("parent_objetivo_id" => $objetivo->objetivo_id, "evento_id" => $_REQUEST["evento_id"]);
		$contenido->generarResultado(false);
	}
	else {
		$log->getSorryServer();
		exit;
	}
}
else {
	$T->parse('tiene_metadata', 'TIENE_METADATA', false);

	$formulario = $objetivo->generarFormulario();
	$T->setVar('__sitio_contenido', $formulario);
}

?>
