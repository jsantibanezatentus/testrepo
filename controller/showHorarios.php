<?

/******************** DEPRECATED ********************/
/******************** DEPRECATED ********************/
/******************** DEPRECATED ********************/

/* INCLUYO EL CONTROLADOR QUE ES USADO POR TODAS LAS SECCIONES CON HORARIOS */
include("commonHorarios.php");

/* SI SE MUESTRA LA LISTA DE HORARIOS */
if (!isset($accion) or $accion=="") {

	$tipos_horario = $usr->getTiposHorarios();
	$horarios = $usr->getHorarios();
	
	$T->setFile('tpl_contenido', 'lista_horarios.tpl');
	$T->setBlock('tpl_contenido','PUEDE_ELIMINAR','puede_eliminar');
	$T->setBlock('tpl_contenido','LISTA_HORARIOS','lista_horarios');
	
	$T->setVar('__accion_sitio_id',$sitio_id);
	$T->setVar('__accion_menu_id',$menu_id);
	
	foreach ($horarios as $horario) {
		$T->setVar('puede_eliminar','');
		if ($horario->puedeEliminar()) {
			$T->setVar('__horario_id',$horario->horario_id);
			$T->parse('puede_eliminar','PUEDE_ELIMINAR',true);
		}
		$T->setVar('__horario_id',$horario->horario_id);
		$T->setVar('__horario_nombre',$horario->nombre);
		$T->setVar('__horario_descripcion',$horario->descripcion);
		$T->setVar('__horario_tipo',$tipos_horario[$horario->tipo]);
		$T->parse('lista_horarios','LISTA_HORARIOS',true);
	}
	
	$T->setVar('__sitio_contenido', $T->parse('out', 'tpl_contenido'));

}

?>