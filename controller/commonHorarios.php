<?

/* IMPORTANTE: ESTE CONTROLADOR ES LLAMADO DESDE OTROS CONTROLADORES, 
 * POR LO QUE PUEDE QUE EXISTAN VARIABLES (DENTRO DE ESTE CODIGO) QUE NO ESTEN DECLARADAS, 
 * YA QUE FUERON DECLARADAS ANTES DE LLAMAR A ESTE CONTROLADOR */

/* MUESTRA EL FOMULARIO PARA INGRESO Y MODIFICACION DE ITEMS */
if ($accion == "modificar_item") {

	$T->setFile('tpl_contenido', 'form_horario_item.tpl');
	$T->setBlock('tpl_contenido', 'PUEDE_MODIFICAR', 'puede_modificar');
	$T->setBlock('tpl_contenido', 'TIPOS_ITEM', 'tipos_item');
	$T->setBlock('tpl_contenido', 'FILTROS_ITEM', 'filtros_item');
	$T->setBlock('tpl_contenido', 'DIAS_ITEM', 'dias_item');
	$T->setBlock('tpl_contenido', 'MESES_ITEM', 'meses_item');
	$T->setBlock('tpl_contenido', 'ANNOS_ITEM', 'annos_item');
	$T->setBlock('tpl_contenido', 'DIAS_SEMANA_ITEM', 'dias_semana_item');
	
	$T->setVar('__accion_sitio_id', $sitio_id);
	$T->setVar('__accion_menu_id', $menu_id);
	
	/* OBTENER LOS DATOS DEL ITEM */
	if ($item_id) {
		$horario = $usr->getHorario($horario_id);
		$item = $horario->getHorarioItem($item_id);
		$T->setVar('__item_id', $item->item_id);
		$T->setVar('__horario_id', $horario_id);
		$T->setVar('__item_fecha_inicio', $item->fecha_inicio);
		$T->setVar('__item_fecha_termino', $item->fecha_termino);
		$T->setVar('__item_hora_inicio', $item->hora_inicio);
		$T->setVar('__item_hora_termino', $item->hora_termino);
	}
	else {
		$T->setVar('__item_id',0);
		$T->setVar('__horario_id', $horario_id);
	}

	/* TIPOS DE ITEM */
	foreach ($tipos_item as $id => $nombre) {
		$T->setVar('__item_tipo_id', $id);
		$T->setVar('__item_tipo_nombre', $nombre);
		$T->setVar('__item_tipo_sel', ($item and $item->es_incluido==$id)?"selected":"");
		$T->parse('tipos_item', 'TIPOS_ITEM', true);
	}
	
	/* FILTROS DEL ITEM */
	foreach ($filtros_item as $id => $nombre) {
		$T->setVar('__item_filtro_id', $id);
		$T->setVar('__item_filtro_nombre', $nombre);
		$T->setVar('__item_filtro_sel', ($item and $item->getTipoFiltro()==$id)?"selected":"");
		if (($item and $item->getTipoFiltro() == $id) or (!isset($item) and $id == 1)) {
			$T->setVar('__item_filtro_sel', "selected");
			$T->setVar('__item_filtro_display_'.$id, "inline");
		}
		else {
			$T->setVar('__item_filtro_sel', "");
			$T->setVar('__item_filtro_display_'.$id, "none");
		}
		$T->parse('filtros_item', 'FILTROS_ITEM', true);
	}
	
	/* DIAS PARA EL ITEM */
	for ($i=1; $i<32; $i++) {
		$T->setVar('__item_dia', $i);
		$T->setVar('__item_dia_sel', ($item and $item->dia==$i)?"selected":"");
		$T->parse('dias_item', 'DIAS_ITEM', true);
	}

	/* MESES PARA EL ITEM */
	foreach ($meses_anno as $id => $nombre) {
		$T->setVar('__item_mes_id', $id);
		$T->setVar('__item_mes_nombre', $nombre);
		$T->setVar('__item_mes_sel', ($item and $item->mes==$id)?"selected":"");
		$T->parse('meses_item', 'MESES_ITEM', true);
	}

	/* AÑOS PARA EL ITEM */
	for ($i=date("Y"); $i<date("Y")+2; $i++) {
		$T->setVar('__item_anno', $i);
		$T->setVar('__item_anno_sel', ($item and $item->anno==$i)?"selected":"");
		$T->parse('annos_item', 'ANNOS_ITEM', true);
	}

	/* DIAS DE LA SEMANA PARA EL ITEM */
	foreach ($dias_semana as $id => $nombre) {
		$T->setVar('__item_dia_semana_id', $id);
		$T->setVar('__item_dia_semana_nombre', $nombre);
		$T->setVar('__item_dia_semana_sel', ($item and $item->dia_semana==$id)?"selected":"");
		$T->parse('dias_semana_item', 'DIAS_SEMANA_ITEM', true);
	}
	
	/* VERIFICAR SI ES LECTURA O ESCRITURA */
	if (!$usr->solo_lectura and $sactual->getPermisos(1) == 'w') {
		$T->parse('puede_modificar', 'PUEDE_MODIFICAR', false);
	}
	else {
		$T->setVar('__form_disabled', 'disabled');
	}
	
	$T->pparse('out', 'tpl_contenido');
	exit();
}

/* MUESTRA EL FOMULARIO PARA INGRESO Y MODIFICACION DE HORARIOS */
elseif ($accion == "modificar_horario") {

	$T->setFile('tpl_contenido', 'form_horario.tpl');
	$T->setBlock('tpl_contenido', 'PUEDE_MODIFICAR', 'puede_modificar');
	$T->setBlock('tpl_contenido', 'PUEDE_VOLVER', 'puede_volver');
		
	$T->setBlock('tpl_contenido', 'LISTA_HORAS_TITULO', 'lista_horas_titulo');
	$T->setBlock('tpl_contenido', 'LISTA_HORAS_TOTAL', 'lista_horas_total');
	$T->setBlock('tpl_contenido', 'LISTA_HORAS_DIA', 'lista_horas_dia');
	$T->setBlock('tpl_contenido', 'LISTA_DIAS_SEMANA', 'lista_dias_semana');

	$T->setBlock('tpl_contenido', 'PUEDE_AGREGAR_ITEM', 'puede_agregar_item');
	$T->setBlock('tpl_contenido', 'PUEDE_ELIMINAR_ITEM', 'puede_eliminar_item');
	$T->setBlock('tpl_contenido', 'PUEDE_MODIFICAR_ITEM', 'puede_modificar_item');
	$T->setBlock('tpl_contenido', 'LISTA_ITEMS', 'lista_items');
	$T->setBlock('tpl_contenido', 'TIENE_CONFIGURACION_AVANZADA', 'tiene_configuracion_avanzada');
	$T->setBlock('tpl_contenido', 'LISTA_HORARIOS_ASIGNABLES', 'lista_horarios_asignables');
	$T->setBlock('tpl_contenido', 'TIENE_HORARIOS_ASIGNABLES', 'tiene_horarios_asignables');

	$T->setVar('__accion_sitio_id', $sitio_id);
	$T->setVar('__accion_menu_id', $menu_id);
//	$T->setVar('__padre_id', $sactual->padre_id);
//	$T->setVar('__seccion_id', $sactual->seccion_id);
//	$T->setVar('__nivel', $sactual->nivel);
	/* OBTENER LOS DATOS DEL HORARIO */
	if ($horario_id) {
		$horario = $usr->getHorario($horario_id);
		$T->setVar('__horario_id', $horario->horario_id);
		$T->setVar('__horario_nombre', $horario->nombre);
		$T->setVar('__horario_descripcion', $horario->descripcion);
		$T->setVar('__horario_tipo_nombre', $horario->tipo_nombre);
	}
	else {
		$T->setVar('__horario_id', 0);
		if ($sactual->seccion_id == REP_SECCION_OBJETIVO_HORARIO) {
			$T->setVar('__horario_tipo_nombre', 'Habil');
		}
		elseif ($sactual->seccion_id == REP_SECCION_NOTIFICACION_HORARIO) {
			$T->setVar('__horario_tipo_nombre', 'Alerta');
		}
	}
	
	/* CONFIGURACION BASICA : SELECTOR DE HORARIOS SEGUN DIA DE LA SEMANA */
	$todo_horas = array();
	foreach ($dias_semana as $dia_semana_id => $dia_semana_nombre) {
		$todo_dia = false;
			
		$T->setVar('__dia_id', $dia_semana_id);
		$T->setVar('__dia_nombre', $dia_semana_nombre);
			
		$T->setVar('lista_horas_dia', '');
		for ($i=0; $i<24; $i++) {
			$tiene_hora = false;
			if (isset($horario)) {
				foreach ($horario->getDiaSemanaItems($dia_semana_id) as $dia_semana_item) {
					$hora_inicio = explode(":", $dia_semana_item->hora_inicio);
					$hora_termino = explode(":", $dia_semana_item->hora_termino);
					if ($hora_inicio[0]<=$i and $hora_termino[0]>$i) {
						$tiene_hora = true;
						$todo_horas[$i]++; 
					}
					if ($hora_inicio[0]==0 and $hora_termino[0]==24) {
						$todo_dia = true;
					}
				}
			}
			$T->setVar('__hora_id', $i);
			$T->setVar('__hora_estilo', (!isset($horario) || $tiene_hora)?"celdanaranja50":"celdanegra10");
			$T->parse('lista_horas_dia', 'LISTA_HORAS_DIA', true);
		}
			
		$T->setVar('__dia_total_estilo', (!isset($horario) || $todo_dia)?"celdanaranja100":"celdanegra20");
		$T->parse('lista_dias_semana', 'LISTA_DIAS_SEMANA', true);
	}

	/* CONFIGURACION BASICA : HEADER Y FOOTER SELECTOR DE HORARIOS */
	for ($i=0; $i<24; $i++) {
		$T->setVar('__hora_id', $i);
		$T->setVar('__hora_nombre', sprintf("%02s", $i));
		$T->parse('lista_horas_titulo', 'LISTA_HORAS_TITULO', true);
		$T->setVar('__hora_total_estilo', (!isset($horario) || $todo_horas[$i]==7)?"celdanaranja100":"celdanegra20");
		$T->parse('lista_horas_total', 'LISTA_HORAS_TOTAL', true);
	}

	/* CONFIGURACION AVANZADA : LISTA DE ITEMS DEL HORARIO */
/*	foreach ($horario->getHorarioItems() as $item) {
		$T->setVar('puede_eliminar_item', '');
		if (!$usr->solo_lectura and $sactual->getPermisos(1) == 'w') {
			$T->setVar('__item_id', $item->item_id);
			$T->parse('puede_eliminar_item', 'PUEDE_ELIMINAR_ITEM', true);
		}
		$T->setVar('puede_modificar_item', '');
		if (!$usr->solo_lectura and $sactual->getPermisos(1) == 'w' and $item->puedeModificar()) {
			$T->setVar('__item_id', $item->item_id);
			$T->parse('puede_modificar_item', 'PUEDE_MODIFICAR_ITEM', true);
		}
		$T->setVar('__item_id', $item->item_id);
		$T->setVar('__item_incluido', ($item->es_incluido)?'Incluido':'Excluido');
		$T->setVar('__item_tipo', $item->toString());
		$T->parse('lista_items', 'LISTA_ITEMS', true);
	}
	$T->parse('tiene_configuracion_avanzada', 'TIENE_CONFIGURACION_AVANZADA', false);
*/
	/* LISTA DE HORARIOS ASIGNABLES PARA LINKEAR */
/*	foreach ($usr->getHorarios(REP_HORARIO_ASIGNABLE) as $asignable) {
		$T->setVar('__asignable_id', $asignable->horario_id);
		$T->setVar('__asignable_nombre', $asignable->nombre);
		$T->parse('lista_horarios_asignables', 'LISTA_HORARIOS_ASIGNABLES', true);
	}
	$T->parse('tiene_horarios_asignables', 'TIENE_HORARIOS_ASIGNABLES', false);
*/
	
	/* SI VIENE DESDE NOTIFICACION */
	if (isset($notificacion_id) and $notificacion_id != "") {
		$T->setVar('__notificacion_id', $notificacion_id);
		$T->setVar('__notificacion_destinatario_id', $_POST["notificacion_destinatario_id"]);
		$T->parse('puede_volver', 'PUEDE_VOLVER', true);
	}

	/* VERIFICAR SI ES LECTURA O ESCRITURA */
	if (!$usr->solo_lectura and $sactual->getPermisos(1) == 'w') {
		$T->parse('puede_modificar', 'PUEDE_MODIFICAR', false);
//		$T->parse('puede_agregar_item', 'PUEDE_AGREGAR_ITEM', false);
	}
	else {
		$T->setVar('__form_disabled', 'disabled');
	}
	
}

?>