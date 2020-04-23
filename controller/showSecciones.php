<?
require 'utils/get_objetives.php';
$usuario = new Usuario($current_usuario_id);
$usuario->__Usuario();

/* GUARDO EL LOG */
$log->setLog("URL");

/* TEMPLATE PRINCIPAL DE TODO EL SITIO */
$T->setBlock('tpl_sitio', 'TIENE_METADATA', 'tiene_metadata');
$T->setBlock('tpl_sitio', 'SECCIONES_SITIO', 'secciones_sitio');
$T->setBlock('tpl_sitio', 'OBJETOS_MENU', 'objetos_menu');
$T->setBlock('tpl_sitio', 'SECCIONES_MENU', 'secciones_menu');
$T->setBlock('tpl_sitio', 'TIENE_ERROR_SISTEMA', 'tiene_error_sistema');
/* Bloques agregados para los diferentes sub-menu de la sección mantenimiento. */
$T->setBlock('tpl_sitio', 'ES_SECCION_MANTENIMIENTO', 'es_seccion_mantenimiento');
$T->setBlock('tpl_sitio', 'ES_SECCION_CALENDARIO', 'es_seccion_calendario');
////////////////////////////////////////////////////////////////////
$T->setBlock('tpl_sitio', 'BLOQUE_NOTIFICACION_GLOBAL', 'bloque_notificacion_global');
$T->setBlock('tpl_sitio', 'BLOQUE_NOTIFICACION_CLIENTE', 'bloque_notificacion_cliente');
$T->setBlock('tpl_sitio', 'BLOQUE_NOTIFICACION_USUARIO', 'bloque_notificacion_usuario');

if ($_SESSION['notificacion'] != 1) {
	$notificaciones = NotificacionModal::getNotificationModal(date("Y-m-d"), $usr->cliente_id, $usr->usuario_id);
	$T->setVar('bloque_notificacion_global', '');
	$T->setVar('bloque_notificacion_cliente', '');
	$T->setVar('bloque_notificacion_usuario', '');
	$validadorG = false;
	$validadorC = false;
	$validadorU = false;
	$array_notificacionesG = array();
	$array_notificacionesC = array();
	$array_notificacionesU = array();
	foreach ($notificaciones as $key => $notificacion) {
		if ($notificacion['notificacion_tipo_id'] == '1') {
			$T->setVar('__notificacion_id', $notificacion['notificacion_id']);
			$T->setVar('__notificacion_tipo_id', $notificacion['notificacion_tipo_id']);
			$T->setVar('__notificacion_titulo', $notificacion['titulo']);
			$T->setVar('__notificacion_cuerpo', $notificacion['cuerpo']);
			array_push($array_notificacionesG,  $notificacion['notificacion_id']);
			$validadorG = true;
			$T->parse('bloque_notificacion_global', 'BLOQUE_NOTIFICACION_GLOBAL', true);
		}

		if ($notificacion['notificacion_tipo_id'] == '2') {
			$T->setVar('__notificacion_id', $notificacion['notificacion_id']);
			$T->setVar('__notificacion_tipo_id', $notificacion['notificacion_tipo_id']);
			$T->setVar('__notificacion_titulo', $notificacion['titulo']);
			$T->setVar('__notificacion_cuerpo', $notificacion['cuerpo']);
			array_push($array_notificacionesC,  $notificacion['notificacion_id']);
			$validadorC = true;
			$T->parse('bloque_notificacion_cliente', 'BLOQUE_NOTIFICACION_CLIENTE', true);
		}

		if ($notificacion['notificacion_tipo_id'] == '3') {
			$T->setVar('__notificacion_id', $notificacion['notificacion_id']);
			$T->setVar('__notificacion_tipo_id', $notificacion['notificacion_tipo_id']);
			$T->setVar('__notificacion_titulo', $notificacion['titulo']);
			$T->setVar('__notificacion_cuerpo', $notificacion['cuerpo']);
			array_push($array_notificacionesU,  $notificacion['notificacion_id']);
			$validadorU = true;
			$T->parse('bloque_notificacion_usuario', 'BLOQUE_NOTIFICACION_USUARIO', true);
		}
	}
	$T->setVar('__cliente_usuario_cliente', $usr->usuario_id);
	$T->setVar('__array_notificacionesG', json_encode($array_notificacionesG));
	$T->setVar('__array_notificacionesC', json_encode($array_notificacionesC));
	$T->setVar('__array_notificacionesU', json_encode($array_notificacionesU));
	$T->setVar('__validadorG', ($validadorG)?1:0);
	$T->setVar('__validadorC', ($validadorC)?1:0);
	$T->setVar('__validadorU', ($validadorU)?1:0);
}else{
	$array_vacio = array();
	$T->setVar('__array_notificacionesG', $array_vacio );
	$T->setVar('__array_notificacionesC', $array_vacio );
	$T->setVar('__array_notificacionesU', $array_vacio );
	$T->setVar('__validadorG', 0);
	$T->setVar('__validadorC', 0);
	$T->setVar('__validadorU', 0);
	$T->setVar('__cliente_usuario_cliente', -1);
}

/* SECCIONES PRINCIPALES DEL SITIO */
//$secciones = $sactual->getSeccionesNivel(0);
$T->setVar('secciones_sitio', '');
foreach ($sactual->getSeccionesNivel(0) as $seccion) {
/*	if (($sitio_id == 0 and $seccion->es_parent) or $sitio_id == $seccion->seccion_id) {
		$sitio_id = $seccion->seccion_id;
		$sitio_nombre = $seccion->nombre;
	}*/
	$T->setVar('__sitio_seccion_nombre', $seccion->nombre);
	$T->setVar('__sitio_seccion_id', $seccion->seccion_id);

	if ($seccion->es_parent) {
		$sitio_id = $seccion->seccion_id;
		$sitio_nombre = $seccion->nombre;
		$T->setVar('__sitio_seccion_class', 'textmenusuperiorsel');
	}
	else {
		$T->setVar('__sitio_seccion_class', 'textmenusuperior');
	}

	$T->parse('secciones_sitio', 'SECCIONES_SITIO', true);
}

$mostrar_contratar = false;
$json = get_objetives($current_usuario_id, $usuario->clave_md5);
$json = json_decode($json);

/* SECCIONES DEL MENU IZQUIERDO */
//$secciones = $sactual->getSeccionesNivel(1);
$json = get_objetives($current_usuario_id, $usuario->clave_md5);
$json = json_decode($json);
$T->setVar('secciones_menu', '');
foreach ($sactual->getSeccionesNivel(1) as $seccion) {

	$mostrar_seccion = true;

	/* DATOS DE LA SECCION */
	$T->setVar('__menu_seccion_nombre', $seccion->nombre);
	$T->setVar('__menu_seccion_id', $seccion->seccion_id);
//	$T->setVar('__menu_seccion_class', ($seccion->es_parent)?'menuizqsel':'menuizq');

	if ($seccion->es_parent) {
		$menu_nombre = $seccion->nombre;
		$T->setVar('__menu_seccion_class', 'menuizqsel');
	}
	else {
		$T->setVar('__menu_seccion_class', 'menuizq');
	}
	
	$es_lista_objetivo = true;
/*	if ($seccion->tipo == "ES_LISTA_GOOGLE_ANALYTICS") {
		$objetivos = $usr->getObjetivos(REP_DATOS_ANALYTICS);
	}*/
	if ($seccion->tipo == "ES_LISTA_OBJETIVOS_PERIODICOS") {
		
		$objetivos = $json->periodo;
	}
/*	elseif ($seccion->tipo == "ES_LISTA_STRESS") {
		$objetivos = $usr->getObjetivos(REP_DATOS_STRESS);
	}*/
	elseif ($seccion->tipo == "ES_LISTA_OBJETIVOS_ONLINE") {
		//var_dump($json->online);
		$objetivos = $json->online;
	}
/*	elseif ($seccion->tipo == "ES_LISTA_OBJETIVOS_PRECIO") {
		$objetivos = $usr->getObjetivos(REP_DATOS_PRECIO);
	}*/
	elseif ($seccion->tipo == "ES_LISTA_ESPECIALES") {
		$objetivos = $json->especiales;
	}
	/*SE INCORPORA LA NUEVA SECCION DE NEW RELIC*/
	elseif ($seccion->tipo == "ES_INFORME_NEW_RELIC_APM") {
		$objetivos = $json->APM;
	}
	/*SE INCORPORA LA NUEVA SECCION DE NEW RELIC RUM*/
	elseif ($seccion->tipo == "ES_INFORME_NEW_RELIC_RUM") {
		$objetivos = $json->RUM;
	}
        /*SE INCORPORA LA NUEVA SECCION DE NEW RELIC MOBILE*/
	elseif ($seccion->tipo == "ES_INFORME_NEW_RELIC_MOBILE") {
		$objetivos = $json->MobileNR;
	}
	/*SE INCORPORA LA NUEVA SECCION DE AUDEX*/
	elseif ($seccion->tipo == "ES_INFORME_AUDEX") {
		$objetivos = $json->Audex;
	}
	/*SE INCORPORA LA NUEVA SECCION DE ATDEX*/
	elseif ($seccion->tipo == "ES_INFORME_ATDEX") {
		$objetivos = $json->Atdex;
	}
	else {
		$es_lista_objetivo = false;
	}

	$T->setVar('objetos_menu', '');
	if ($es_lista_objetivo and count($objetivos)>0) {

		/* SI NO TIENE OBJETO_ID Y LO NECESITA */
/*		if (!$sactual->objeto_id and $sactual->padre_id == $seccion->seccion_id) {
			$sactual->objeto_id = current($objetivos);
		}*/

		foreach ($objetivos as $objetivo) {
			$T->setVar('__menu_objeto_nombre', $objetivo->nombre);
			$T->setVar('__menu_objeto_padding', '');
			$T->setVar('__menu_objeto_display', '');
			$T->setVar('__menu_objeto_tipo', $objetivo->nombre_servicio);
			$T->setVar('__menu_objeto_id', $objetivo->objetivo_id);
			if ($seccion->es_parent and ($sactual->objeto_id == $objetivo->objetivo_id or $sactual->objeto_id == 0)) {
				$sactual->objeto_id = $objetivo->objetivo_id;
				$objetivo_nombre = $objetivo->nombre;
				$T->setVar('__menu_objeto_class', 'textmenusuperiorsel');
			}else {
				$T->setVar('__menu_objeto_class', 'textmenusuperior');
			}
			$T->parse('objetos_menu', 'OBJETOS_MENU', true);
		}
		/* FORMATO SUBMENU */
		$T->setVar('__menu_abrir_enlace', 0);
		if (($seccion->es_parent) or
			($sactual->objeto_id == 0 and $seccion->seccion_id == REP_INFORME_ONLINE)) {
			$T->setVar('__menu_seccion_display', 'inline');
			$T->setVar('__menu_flecha_posicion', 'flechamenuizp spriteButton spriteButton-flecha_abajo');
		}
		else {
			$T->setVar('__menu_seccion_display', 'none');
			$T->setVar('__menu_flecha_posicion', 'flechamenuizp spriteButton spriteButton-flecha_derecha');
		}
		$T->parse('secciones_menu', 'SECCIONES_MENU', true);
	}

	/* SI NO TIENE SUBMENU */
	else {
		if ($es_lista_objetivo and count($objetivos) == 0 and $seccion->es_parent) {
			$mostrar_contratar = true;
		}
		if ($seccion->tipo == "ES_LISTA" and count($usr->getObjetivos(REP_DATOS_USUARIO)) == 0 and $seccion->es_parent) {
			$mostrar_contratar = true;
		}
		$T->setVar('__menu_abrir_enlace', 1);
		$T->setVar('__menu_seccion_display', 'none');
		$T->setVar('__menu_flecha_posicion', 'flechamenuizp spriteButton spriteButton-flecha_derecha');
		$T->parse('secciones_menu', 'SECCIONES_MENU', true);
	}
}

if ($error_accion_estado) {
	$T->setVar('__error_sistema', $error_accion_mensaje);
	$T->parse('tiene_error_sistema', 'TIENE_ERROR_SISTEMA', true);
}

/* DATOS DE LA SECCION ACTUAL */
$T->setVar('__sitio_id', $sitio_id);
if ($sactual->nivel == 2) {
	$T->setVar('__sitio_titulo', (($sactual->objeto_id)?$objetivo_nombre:$menu_nombre)." - ".$sactual->nombre);
}
else {
	$T->setVar('__sitio_titulo', $sactual->nombre);
}
$T->setVar('__menu_id', $sactual->seccion_id);
$T->setVar('__objeto_id', $sactual->objeto_id);
//$T->setVar('__subobjeto_id', $sactual->subobjeto_id);
$T->setVar('__sitio_nombre', $sitio_nombre);
$T->setVar('__sitio_ayuda', $sactual->ayuda);
$T->setVar('__sitio_anno', date("Y"));
$T->setVar('__version', VERSION);

$T->setVar('__padre_id', $sactual->padre_id);
$T->setVar('__seccion_id', $sactual->seccion_id);
$T->setVar('__nivel', $sactual->nivel);

/* DATOS DEL USUARIO */
$T->setVar('__sitio_usuario_id', $usr->usuario_id);
$T->setVar('__sitio_usuario_nombre', $usr->nombre);
$T->setVar('__sitio_usuario_cliente', $usr->cliente_nombre);

/* PATH DE ARCHIVOS EXTERNOS */
$T->setVar('__path_jquery_ui', REP_PATH_JQUERY_UI);
$T->setVar('__path_dojo', REP_PATH_DOJO);
$T->setVar('__path_js', REP_PATH_JS);
$T->setVar('__path_img', REP_PATH_IMG);
$T->setVar('__path_img_boton', REP_PATH_IMG_BOTONES);
$T->setVar('__path_anychart', REP_PATH_JSCHART);
$T->setVar('__path_moment_js', REP_PATH_MOMENT);
$T->setVar('__path_full_calendar', REP_PATH_FULL_CALENDAR);
$T->setVar('__ga_tracking_id', REP_GA_TRACKING_ID);
$T->setVar('__path_ga', $sactual->path_analytics);
/* Validaciones para cargar ciertas librerias en el layout*/
if($sactual->seccion_id==SUB_SECCION_MANTENIMIENTO){
	$T->parse('es_seccion_mantenimiento', 'ES_SECCION_MANTENIMIENTO', true);
	$T->parse('es_seccion_calendario', 'ES_SECCION_CALENDARIO', true);
	
}
elseif($sactual->seccion_id==SUB_SECCION_MANTENIMIENTO_HISTORIAL or $sactual->seccion_id==SUB_SECCION_MANTENIMIENTO_AGREGAR_EVENTO){
	$T->parse('es_seccion_mantenimiento', 'ES_SECCION_MANTENIMIENTO', true);
}

//echo($sactual->seccion_id." ".$sactual->path_analytics);

if ($mostrar_contratar) {
	$T->setFile('tpl_contenido', 'contratar.tpl');
	$T->setVar('__sitio_contenido', $T->parse('out','tpl_contenido'));
	$T->pparse('out', 'tpl_sitio');
	exit();
}

?>