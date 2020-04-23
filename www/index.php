<?
include("../config/include.php");
include("../config/authentication.php");
include("seccion_dojo.php");

/* NO SACAR ESTA LINEA POR NINGUN MOTIVO.
 * ESTA LINEA HACE QUE SE VEAN LOS GRAFICOS EN IE8 */
header("Pragma: public");

$sitio_id = $_REQUEST["sitio_id"];
$menu_id = $_REQUEST["menu_id"];

$objeto_id = $_REQUEST["objeto_id"];
$accion = $_REQUEST["accion"];
$ejecutar_accion = $_REQUEST["ejecutar_accion"];
$popup = $_REQUEST["popup"];
$solo_action = $_REQUEST["solo_action"];

if (!$sitio_id) {
	$sitio_id = 0;
}

if (!$menu_id) {
	$menu_id = $sitio_id;
}

if (!$objeto_id) {
	$objeto_id = 0;
}


$sactual = Seccion::getSeccionPorDefecto($menu_id);
//TODO: quitar objetivo_id de seccion
$sactual->objeto_id = $objeto_id;

/* LA ACCION SE EJECUTA DENTRO DE UN TRY-CATCH POR SI SE PRODUCE UN ERROR */

$error_accion_estado = 0;
$error_accion_mensaje = "";

if (isset($ejecutar_accion) and $ejecutar_accion == 1) {
	try {
            include(REP_PATH_CONTROLLER.$cod_pagina_action[$sitio_id]);
//        }
	}catch (Exception $e) {
		unset($accion);
		$error_accion_estado = 1;
		$error_accion_mensaje = $e->getMessage();
	}
}

if ($popup) {
	if ($sactual->tipo == 'ES_LISTA_ESPECIALES') {
		include(REP_PATH_CONTROLLER."showEspeciales.php");
	}else {
		include(REP_PATH_CONTROLLER."showReportesPopup.php");
	}
}else {

	if($solo_action != 't') {
		
		$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
		$T->setFile('tpl_sitio', 'layout.tpl');
		$T->setVar('seccion', SECCION_MANTENIMIENTO);
		$T->setVar('calendario', SUB_SECCION_MANTENIMIENTO);
		$T->setVar('historial', SUB_SECCION_MANTENIMIENTO_HISTORIAL);
		$T->setVar('agregar', SUB_SECCION_MANTENIMIENTO_AGREGAR_EVENTO);
		
		$seccion= new Seccion_dojo();
		$seccion_dojo = Seccion_dojo::otorgarCapa($menu_id);
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) {
	    	if($version_chrome >= 51){
				$T->setVar('__seccion','tools/dojo/nueva_capa/'.$seccion_dojo);
	    	}elseif($version_chrome <= 51){
	    		$T->setVar('__seccion','tools/dojo_old/nueva_capa/'.$seccion_dojo);
	      	}
	    }elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident')) {
	      		$T->setVar('__seccion','tools/dojo_old/nueva_capa/'.$seccion_dojo);
	    }else{
	    	$T->setVar('__seccion','tools/dojo/nueva_capa/'.$seccion_dojo);
	    }
	  
		include(REP_PATH_CONTROLLER."showSecciones.php");
		
		if ($sactual->tipo == 'ES_LISTA_ESPECIALES') {
			include(REP_PATH_CONTROLLER."showEspeciales.php");
		}
		else {
			include(REP_PATH_CONTROLLER.$cod_pagina_show[$sitio_id]);
		}

		$T->pparse('out', 'tpl_sitio');
	}

}
$mdb2->disconnect();

?>