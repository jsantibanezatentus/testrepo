<?
#  Controlador de template.
## Autor: Carlos Sepúlveda.
#  Objetivo: Manejar los template que se desplegaran según la sub-sección de mantenimiento.

$subSection = $_REQUEST["menu_id"];
$eventId = $_REQUEST["event"];
$timeZoneId = $usr->zona_horaria_id;
$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
$timeZone = $arrTime[$timeZoneId];
$clientId = $usr->cliente_id;
$nameClient = $usr->cliente_nombre;
$nameUser = $usr->nombre;
$userId = $usr->usuario_id;

if ($subSection == SUB_SECCION_MANTENIMIENTO_AGREGAR_EVENTO){
	$T->setFile('tpl_contenido', 'form_agregar_evento.tpl');
}
elseif($subSection == SUB_SECCION_MANTENIMIENTO_HISTORIAL){
	$T->setFile('tpl_contenido', 'lista_historial.tpl');
	$T->setVar('__evento_id', $eventId);
	$T->setVar('__cliente_id', $clientId);
	$T->setVar('__nombre_cliente', $nameClient);
	$T->setVar('__nombre_usuario', $nameUser);
	
}
else{

	$T->setFile('tpl_contenido', 'calendario_evento.tpl');
	$T->setBlock('tpl_contenido', 'BLOQUE_DATOS', 'bloque_datos');

	$event = new Event;
	$data = $event->getDataMaintance($clientId, $timeZone);

	foreach ($data as $register){

		if ($register['estado'] == 'Ingresado'){
			$chart = array("{", "}");
			$objetiveId = str_replace($chart, "", $register['objetivo_id']);
			$startDate = Utiles::convertDateTimeZone($register['fecha_inicio'], $timeZone);
			$endDate = Utiles::convertDateTimeZone( $register['fecha_termino'], $timeZone);
			
			$T->setVar('__fecha_inicio', $startDate);
			$T->setVar('__fecha_termino', $endDate);
			$T->setVar('__nombre', $register['nombre']);
			$T->setVar('__id', $register['id']);
			$T->setVar('__objetivos', (string)$objetiveId);
			$T->setVar('__titulo', $register['titulo']);
			$T->setVar('__usuario_id_evento', $register['usuario_id']);
			$T->parse('bloque_datos','BLOQUE_DATOS',true);
		}
	}
    $T->setVar('__path_full_calendar', REP_PATH_FULL_CALENDAR);

}

$T->setVar('__path_jquery_ui', REP_PATH_JQUERY_UI);
$T->setVar('__usuario_id', $userId);
$T->setVar('__nombre_usuario', $nameUser);
$T->setVar('__zona_horaria', $timeZone);

$T->setVar('__sitio_contenido', $T->parse('out', 'tpl_contenido'));

?>