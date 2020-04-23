<?
/*
 Controlador de llamadas ajax.
 Autor: Carlos Sepúlveda.
 Objetivo: Archivo utilizado para todas las llamadas ajax,
 solo se debe mandar la variable con el nombre de la función a ejecutar,
 crear la función  y definir en esta las variables request a utilizar.  .
*/
include("../config/include.php");
include("../config/authentication.php");

/* Archivo que maneja las llamadas ajax. */

$function = $_REQUEST['nameFunction'];
$request = $function();

/*
Función que ejecuta el hilo para realizar insert or update.
*/
function queryEvent(){
	header('Content-type: application/json');

	$data = json_decode($_REQUEST['data']);

	$event = instance();

	if ($data[11] == 'Editar'){

		$event->define($data,'edit');
		$request  = $event->editData();
	}
	else{
		$event->define($data, 'crear');
		$request  = $event->createData();
	}
	return $request;
}
/* 
Función que ejecuta el hilo para devolver los objetivos del usuario actual.
*/
function getObjetive(){
	global $usr;

	$objetives=$usr->getObjetivos(1);
	return $objetives;
}
function getObjetiveId(){
    $objIds = $_REQUEST['ids'];
    $objetive = new Objetivo(1);
    return $objetive->getObjetiveName($objIds);
}
/* 
Función que ejecuta el hilo para devolver los registros del mantenedor.
*/
function getRegisterMaintaince(){
	global $usr;
	
	$timeZoneId = $usr->zona_horaria_id;
	$arrTime = Utiles::getNameZoneHor($timeZoneId);
	$timeZone = $arrTime[$timeZoneId];	
	$event = instance();
	$clientId = $_REQUEST['clientId'];
	$datos = $event->getDataMaintance($clientId, $timeZone);
	return $datos;
}
/*
Sirve para instanciar el objeto evento.
*/
function instance(){
	$event = new Event;  
	return $event;
}

function vruAjax(){
	$objetivo_especial = $_REQUEST['objetivo_especial'];
	$horario_id = $_REQUEST['horario_id'];
	$usuario_id = $_REQUEST['usuario_id'];
	$fecha_inicio = $_REQUEST['fecha_inicio'];
	$fecha_termino = $_REQUEST['fecha_termino'];

	$tabla = new Tabla;
	return $tabla->vistaRapidaUnificadaAjax($objetivo_especial, $horario_id, $usuario_id, $fecha_inicio, $fecha_termino);
}
/*
Trae los nombres de los objetivos junto a sus ids.
*/
function getObjetiveName(){
	$objIds = $_REQUEST['ids'];
	$objetivos = explode(",", $objIds);
	$array_obj = array();
	foreach ($objetivos as $objetivo){
	    $subobjetivo=new ConfigObjetivo($objetivo);
	    $array_obj[$objetivo]['objetivo_id']=$subobjetivo->objetivo_id;
	    $array_obj[$objetivo]['nombre']=$subobjetivo->nombre;
	    
	   
	   foreach ($subobjetivo->__pasos as $paso){
	       $array_obj[$objetivo]['pasos'][$paso->paso_id]['paso_id']=$paso->paso_id;
	       $array_obj[$objetivo]['pasos'][$paso->paso_id]['nombre']=$paso->nombre;
	       foreach ($paso->__patrones[0] as $patron){
	           $array_obj[$objetivo]['pasos'][$paso->paso_id]['patrones'][$patron->orden]['patron_orden']=$patron->orden;
	           $array_obj[$objetivo]['pasos'][$paso->paso_id]['patrones'][$patron->orden]['nombre']=$patron->nombre;
	           $array_obj[$objetivo]['pasos'][$paso->paso_id]['patrones'][$patron->orden]['valor']=$patron->valor;	        
	       }
	   }	    
	}	
	return $array_obj;
}

function notificacionControlInsert(){
	$notificacion_id = $_REQUEST['notificacion_id'];
	$usuario_id = $_REQUEST['cliente_usuario_id'];
	$noMostrar = $_REQUEST['noMostrar'];
	//se marca con un 1 para no volver a mostrar la notificacion, mientras dure la sesion y no aparezca cada vez que el usuario cambie de sección
	$_SESSION['notificacion'] = 1;
	if ($noMostrar == 1) {
		$notificacion = new NotificacionModal();
		//se crea el insert en la tabla reporte.notificacion_control, asi al volver iniciar sesión no aparezca
		$notificacion->getNotificationModalInsert($notificacion_id, $usuario_id);
	}
}

function SemaforoSonido(){
	global $usr;
	$sound = $_REQUEST['sound'];
	$sonido = $usr->setSonidoSemaforo($sound);
	return $sonido;
}

echo json_encode($request);
?>