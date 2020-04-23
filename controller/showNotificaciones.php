<?

$notificacion_id = $_REQUEST["notificacion_id"];
$destinatario_id = $_REQUEST["destinatario_id"];
$horario_id = $_REQUEST["horario_id"];
$horario_tipo_id = $_REQUEST["horario_tipo_id"];
$item_id = $_REQUEST["item_id"];
//if(!$accion)
//$accion=$_REQUEST['accion'];
/* SI SE INGRESA AL MENU DE NOTIFICACIONES */

$T->parse('tiene_metadata', 'TIENE_METADATA', false);

//echo '<pre>';
//print_r($_REQUEST);
//print_r($sactual->seccion_id);
//echo 'REP='.REP_SECCION_NOTIFICACION.' permisos='.$sactual->getPermisos(1);
if ($sactual->seccion_id == REP_SECCION_NOTIFICACION and $sactual->getPermisos(1) != '-' && !$_REQUEST["item_tipo"]) {

	/* MUESTRA LA CONFIGURACION DE LA NOTIFICACION */
	/* MUESTRA EL FORMULARIO PARA INGRESO Y MODIFICACION DE NOTIFICACIONES */
	if ($accion == "modificar_notificacion" || $accion == "agregar_notificacion") {
		$T = & new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_contenido', 'tabla_detalle_notificacion.tpl');
		$T->setBlock('tpl_contenido', 'LISTA_MONITORES', 'lista_monitores');
		$T->setBlock('tpl_contenido', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_contenido', 'LISTA_PASOS_MONITORES', 'lista_pasos_monitores');
		$T->setBlock('tpl_contenido', 'TIENE_NOTIFICACION_SLA', 'tiene_notificacion_sla');
		$T->setBlock('tpl_contenido', 'TIENE_NOTIFICACION_OK', 'tiene_notificacion_ok');
		$T->setBlock('tpl_contenido', 'TIENE_NOTIFICACION_PARCIAL', 'tiene_notificacion_parcial');
		$T->setBlock('tpl_contenido', 'TIENE_NOTIFICACION_GRUPAL', 'tiene_notificacion_grupal');
		$T->setBlock('tpl_contenido', 'TIENE_NOTIFICACION_GLOBAL', 'tiene_notificacion_global');

		$T->setBlock('tpl_contenido', 'DESTINATARIOS_NOTIFICACION', 'destinatarios_notificacion');
		$T->setBlock('tpl_contenido', 'OBJETIVOS_NOTIFICACION', 'objetivos_notificacion');
		$T->setBlock('tpl_contenido', 'HORARIOS_NOTIFICACION', 'horarios_notificacion');
		$T->setBlock('tpl_contenido', 'BLOQUE_PUEDE_EDITAR', 'bloque_puede_editar');
		$T->setBlock('tpl_contenido', 'BLOQUE_NO_PUEDE_EDITAR', 'bloque_no_puede_editar');

		$T->setBlock('tpl_contenido', 'BLOQUE_ALARMA_NUEVA', 'bloque_alarma_nueva');
		$T->setBlock('tpl_contenido', 'BLOQUE_GUARDA_ALARMA_NUEVA', 'bloque_guarda_alarma_nueva');
		/* OBTENER LOS DATOS DE LA NOTIFICACION */
		if ($notificacion_id) {
			$notificacion = $usr->getNotificacion($notificacion_id);
			$objetivo = $notificacion->getConfigObjetivo();

			$T->setVar('__objetivo_id', $objetivo->objetivo_id);
			$T->setVar('__notificacion_id', $notificacion->notificacion_id);
			$T->setVar('__notificacion_escalabilidad_desde', $notificacion->escalabilidad_desde);
			$T->setVar('__notificacion_escalabilidad_hasta', $notificacion->escalabilidad_hasta);
			if($notificacion->escalabilidad_hasta=='')
				$T->setVar('__notificacion_escalabilidad_hasta_checked', "checked='true'");
			$servicio = $objetivo->getServicio();
			$servicio->__Servicio();

		$T->parse('bloque_puede_editar', 'BLOQUE_PUEDE_EDITAR', false);
		}
		else {
			$T->setVar('__notificacion_id', 0);
			$T->setVar('__notificacion_escalabilidad_desde', 1);
			$T->setVar('__notificacion_escalabilidad_hasta', 1);
			$T->parse('bloque_alarma_nueva', 'BLOQUE_ALARMA_NUEVA', false);
			$T->parse('bloque_no_puede_editar', 'BLOQUE_NO_PUEDE_EDITAR', false);
			$T->parse('bloque_guarda_alarma_nueva', 'BLOQUE_GUARDA_ALARMA_NUEVA', false);

			if ($sactual->getPermisos(3) == 'w') {
				$objetivo = $usr->getConfigObjetivo($_REQUEST["objetivo_id"], REP_DATOS_NOTIFICACION);
			}
			else {
				$objetivo = $usr->getConfigObjetivo($_REQUEST["objetivo_id"], REP_DATOS_USUARIO);
			}
			$servicio = $objetivo->getServicio();
			$servicio->__Servicio();

		}
		$T->setVar('__objetivo_id', $objetivo->objetivo_id);
			/* CONFIGURACION DE LA NOTIFICACION */

		if ($servicio->notificacion_uptime_parcial) {
			$T->setVar('__notificacion_uptime_parcial', ($notificacion->uptime_parcial)?"checked":"");
		$T->parse('tiene_notificacion_ok', 'TIENE_NOTIFICACION_OK', false);
		}
		if ($servicio->notificacion_downtime_parcial) {
			$T->setVar('__notificacion_downtime_parcial', ($notificacion->downtime_parcial)?"checked":"");
			$T->parse('tiene_notificacion_parcial', 'TIENE_NOTIFICACION_PARCIAL', false);
		}
		if ($servicio->notificacion_downtime_grupal) {
			$T->setVar('__notificacion_downtime_grupal', ($notificacion->downtime_grupal)?"checked":"");
			$T->parse('tiene_notificacion_grupal', 'TIENE_NOTIFICACION_GRUPAL', false);
		}
		if ($servicio->notificacion_downtime_global) {
			$T->setVar('__notificacion_downtime_global', ($notificacion->downtime_global)?"checked":"");
			$T->parse('tiene_notificacion_global', 'TIENE_NOTIFICACION_GLOBAL', false);
		}
		if ($servicio->notificacion_patron_inverso) {
			$T->setVar('__notificacion_patron_inverso', ($notificacion->patron_inverso)?"checked":"");
		}
		/* DESTINATARIOS PARA LA NOTIFICACION */
		$destinatarios = $usr->getDestinatarios();
		if (count($destinatarios)>0) {
			if (!isset($notificacion_destinatario_id)) {
				$notificacion_destinatario_id = ($notificacion)?$notificacion->__destinatario->destinatario_id:0;
			}
			foreach ($usr->getDestinatarios() as $destinatario) {
				$T->setVar('__destinatario_id', $destinatario->destinatario_id);
				$T->setVar('__destinatario_nombre', $destinatario->nombre);
				$T->setVar('__destinatario_sel', ($notificacion_destinatario_id==$destinatario->destinatario_id)?"selected":"");
				$T->parse('destinatarios_notificacion', 'DESTINATARIOS_NOTIFICACION', true);
			}
		}
		else {
			$T->setVar('__destinatario_id', '0');
			$T->setVar('__destinatario_nombre', 'Sin contacto');
			$T->parse('destinatarios_notificacion', 'DESTINATARIOS_NOTIFICACION', true);
		}

		/* HORARIOS PARA LA NOTIFICACION */
		if (!isset($notificacion_horario_id)) {
			$notificacion_horario_id = ($notificacion)?$notificacion->__horario->horario_id:-1;
		}

		/**CARGAR EL SELECCIONE Y TODO HORARIO*/
		$T->setVar('__horario_id', '0');
		$T->setVar('__horario_nombre', 'Todo Horario');
		if($notificacion_horario_id==0){
			$T->setVar('__horario_sel', "selected");
		}
		$T->parse('horarios_notificacion', 'HORARIOS_NOTIFICACION', true);

		foreach ($usr->getHorarios(6) as $horario) {
			$T->setVar('__horario_id', $horario->horario_id);
			$T->setVar('__horario_nombre', $horario->nombre);
			$T->setVar('__horario_sel', ($notificacion_horario_id==$horario->horario_id)?"selected":"");
			$T->parse('horarios_notificacion', 'HORARIOS_NOTIFICACION', true);
		}

		/* SE MUESTRA ESTA LISTA PARA SETEAR LOS UMBRALES */
		$monitores = $objetivo->getMonitores();
		if (count($objetivo->__pasos) > 0 and count($monitores) > 0 and $servicio->notificacion_sla) {
			$T->setVar('__notificacion_sla', ($notificacion->sla)?"checked":"");
			$posicion = 0;

			foreach ($monitores as $id => $monitor) {
				$arr_sla = $objetivo->getSlaNotificaciones($id);
				$T->setVar('__monitor_id', $monitor->monitor_id);
				$T->setVar('__monitor_nombre', $monitor->nombre);
				$T->setVar('__monitor_selector', $posicion);
				$T->parse('lista_monitores', 'LISTA_MONITORES', true);
				$T->setVar('lista_pasos', '');
				$orden = 0;

				foreach ($objetivo->__pasos as $paso) {
					$setup = $paso->__setups[($paso->__setups[$id])?$id:0];

					$T->setVar('__paso_orden', $orden);
					$T->setVar('__paso_id', $paso->paso_id);
					$T->setVar('__paso_nombre', $paso->nombre);
					$T->setVar('__paso_timeout', ($setup->timeout)?$setup->timeout:"N/A");
					$T->setVar('__paso_sla', $arr_sla[$paso->paso_id]);
					$T->parse('lista_pasos', 'LISTA_PASOS', true);
					$orden++;
				}
				$posicion++;
				$T->parse('lista_pasos_monitores', 'LISTA_PASOS_MONITORES', true);
			}
			$T->parse('tiene_notificacion_sla', 'TIENE_NOTIFICACION_SLA', false);
		}
		$T->pparse('out', 'tpl_contenido');
		exit();
	}
	/* MUESTRA LA LISTA DE NOTIFICACIONES */
	else {

		$T->setFile('tpl_contenido', 'lista_notificaciones.tpl');
		$T->setBlock('tpl_contenido', 'PUEDE_AGREGAR', 'puede_agregar');
		$T->setBlock('tpl_contenido', 'MAXIMAS_ALARMAS', 'maximas_alarmas');
		$T->setBlock('tpl_contenido', 'MOSTRAR_NOTIFICACIONES_DISPONIBLES', 'mostrar_notificaciones_disponibles');
		$T->setBlock('tpl_contenido', 'PUEDE_EDITAR', 'puede_editar');
		$T->setBlock('tpl_contenido', 'PUEDE_ELIMINAR', 'puede_eliminar');
		$T->setBlock('tpl_contenido', 'BLOQUE_SIN_NOTIFICACIONES', 'bloque_sin_notificaciones');
		$T->setBlock('tpl_contenido', 'ALARMAS_OBJETIVO', 'alarmas_objetivo');
		$T->setBlock('tpl_contenido', 'LISTA_OBJETIVO', 'lista_objetivo');

		if ($sactual->getPermisos(3) == 'w') {
			$objetivos = $usr->getObjetivos(REP_DATOS_NOTIFICACION);
		}
		else {
			$objetivos = $usr->getObjetivos(REP_DATOS_USUARIO);
		}
		$cnt_restante = $usr->puedeAgregarNotificaciones();

		foreach($objetivos as $objetivo){

			if($objetivo->es_activo == 1){
				$config = $objetivo->__servicio;
				$d = $config->nombre;
				$T->setVar('alarmas_objetivo', '');
				$T->setVar('bloque_sin_notificaciones', '');
				$T->setVar('__nombreServicio', $d);
				$T->setVar('__nombreObjetivo', $objetivo->nombre);
				$T->setVar('__idObjetivo', $objetivo->objetivo_id);
				$T->setVar('__resaltaAlarmas', (count($usr->getNotificacionesObjetivo('',$objetivo->objetivo_id))>0)?"font-weight:bold":"");
				$T->setVar('__numeroAlarmas', count($usr->getNotificacionesObjetivo('',$objetivo->objetivo_id)));

				/* LISTA DE NOTIFICACIONES */
				$nNotificaciones=0;
				foreach ($notificaciones = $usr->getNotificacionesObjetivo('',$objetivo->objetivo_id) as $notificacion) {
					$T->setVar('puede_agregar', '');
					$T->setVar('mostrar_notificaciones_disponibles', '');
					$T->setVar('puede_eliminar', '');
					$T->setVar('puede_editar', '');
					$T->setVar('__notificacion_id', $notificacion->notificacion_id);
					$T->setVar('__destinatario_nombre', $notificacion->__destinatario->nombre);
					$T->setVar('__objetivo_nombre', $notificacion->__objetivo->nombre);
					$T->setVar('__horario_nombre', $notificacion->__horario->nombre);
					$T->setVar('__escalabilidad_desde', $notificacion->escalabilidad_desde);
					$T->setVar('__escalabilidad_hasta', ($notificacion->escalabilidad_hasta=='')?'Infinito':$notificacion->escalabilidad_hasta);
					$T->setVar('__notificacion_id', $notificacion->notificacion_id);
					$T->setVar('__destinatario_nombre', $notificacion->__destinatario->nombre);

					if (!$usr->solo_lectura and $sactual->getPermisos(2) == 'w') {
						$T->parse('puede_eliminar', 'PUEDE_ELIMINAR', false);
						$T->parse('puede_editar', 'PUEDE_EDITAR', false);
					}
					$T->parse('alarmas_objetivo', 'ALARMAS_OBJETIVO', true);
						$nNotificaciones=1;
					}

					if($nNotificaciones==0){
							$T->parse('bloque_sin_notificaciones', 'BLOQUE_SIN_NOTIFICACIONES', false);
					}

					if ($sactual->getPermisos(3) == 'w' AND !$usr->solo_lectura) {//VERIFICA SI TIENE PERMISOS PARA ESCRIIR
						if ($cnt_restante and count($objetivos) > 0) {//VERIFICA SI QUEDAN ALARMAS
							$T->parse('puede_agregar', 'PUEDE_AGREGAR', false);
						}
						if ($cnt_restante < REP_MOSTRAR_DISPONIBLES_MINIMO) { //VERIFICA SI DEBE O NO DAR AVISO POR POCAS ALARMAS DISPONIBLES
							$T->setVar('__notificacion_disponible', $cnt_restante);
							$T->parse('mostrar_notificaciones_disponibles', 'MOSTRAR_NOTIFICACIONES_DISPONIBLES', false);
						}
					}
					else{
						if($sactual->getPermisos(3) == 'w' and $usr->cnt_notificaciones <= count($notificaciones)){
							$T->parse('maximas_alarmas', 'MAXIMAS_ALARMAS', false);
						}
					}
					$T->parse('lista_objetivo', 'LISTA_OBJETIVO', true);
				}
			}

	}
}


/* SI SE INGRESA AL MENU DESTINATARIOS */
elseif ($sactual->seccion_id == REP_SECCION_NOTIFICACION_DESTINATARIO and $sactual->getPermisos(1) != '-') {

	/* MUESTRA EL FORMULARIO PARA INGRESO Y MODIFICACION DE DESTINATARIOS */
	if ($accion == "modificar_destinatario") {
		$T->setFile('tpl_contenido', 'form_destinatario.tpl');
		$T->setBlock('tpl_contenido', 'PUEDE_MODIFICAR', 'puede_modificar');
		$T->setBlock('tpl_contenido', 'TIPOS_DESTINATARIOS', 'tipos_destinatarios');

		/* SETEAR DE NUEVO LA SECCION PORQUE ES UN POPUP */
		$T->setVar('__accion_sitio_id', $sitio_id);
		$T->setVar('__accion_menu_id', $sactual->seccion_id);

		/* OBTENER LOS DATOS DEL DESTINARARIO */
		if ($destinatario_id) {
			$destinatario = $usr->getDestinatario($destinatario_id);
			$T->setVar('__destinatario_id', $destinatario->destinatario_id);
			$T->setVar('__destinatario_nombre', $destinatario->nombre);
			$T->setVar('__destinatario_contacto', $destinatario->contacto);
			$T->setVar('__destinatario_telefono', $destinatario->telefono);
		}
		else {
			$T->setVar('__destinatario_id', 0);
		}

		/* TIPOS DEL DESTINATARIO */
		$tipos = Constantes::getTiposDestinatarios();
		if (!isset($tipos[$destinatario->tipo_id]) and $destinatario_id) {
			$T->setVar('__destinatario_tipo_id', $destinatario->tipo_id);
			$T->setVar('__destinatario_tipo_nombre', $destinatario->tipo_nombre);
			$T->setVar('__destinatario_tipo_sel', 'selected');
			$T->parse('tipos_destinatarios', 'TIPOS_DESTINATARIOS', true);
		}
		foreach ($tipos as $id => $nombre) {
			$T->setVar('__destinatario_tipo_id', $id);
			$T->setVar('__destinatario_tipo_nombre', $nombre);
			$T->setVar('__destinatario_tipo_sel', ($destinatario and $destinatario->tipo_id==$id)?"selected":"");
			$T->parse('tipos_destinatarios', 'TIPOS_DESTINATARIOS', true);
		}

		/* SI VIENE DESDE NOTIFICACION */
		if (isset($notificacion_id) and $notificacion_id != "") {
			$T->setVar('__notificacion_id', $notificacion_id);
			$T->setVar('__notificacion_horario_id', $_REQUEST["notificacion_horario_id"]);
		}

		/* VERIFICAR SI ES LECTURA O ESCRITURA */
		if (!$usr->solo_lectura and $sactual->getPermisos(2)=='w') {
			$T->parse('puede_modificar', 'PUEDE_MODIFICAR', false);
		}
		else {
			$T->setVar('__form_disabled', 'disabled');
		}

		$T->pparse('out', 'tpl_contenido');
		exit();
	}
	/* MUESTRA LA LISTA DE DESTINATARIOS */
	else {

		$T->setFile('tpl_contenido', 'lista_destinatarios.tpl');
		$T->setBlock('tpl_contenido', 'PUEDE_AGREGAR', 'puede_agregar');
		$T->setBlock('tpl_contenido', 'PUEDE_ELIMINAR', 'puede_eliminar');
		$T->setBlock('tpl_contenido', 'LISTA_DESTINATARIOS', 'lista_destinatarios');

		/* LISTA DE DESTINATARIOS */
		foreach ($usr->getDestinatarios() as $destinatario) {
			$T->setVar('puede_eliminar', '');
			if (!$usr->solo_lectura and $sactual->getPermisos(2) == 'w' and $destinatario->puedeEliminar()) {
				$T->setVar('__destinatario_id', $destinatario->destinatario_id);
				$T->setVar('__destinatario_nombre', $destinatario->nombre);
				$T->parse('puede_eliminar', 'PUEDE_ELIMINAR', true);
			}
			$T->setVar('__destinatario_id', $destinatario->destinatario_id);
			$T->setVar('__destinatario_nombre', $destinatario->nombre);
			$T->setVar('__destinatario_contacto', $destinatario->contacto);
			$T->setVar('__destinatario_tipo', $destinatario->tipo_nombre);
			$T->setVar('__destinatario_telefono', $destinatario->telefono);
			$T->parse('lista_destinatarios', 'LISTA_DESTINATARIOS', true);
		}

		/* VERIFICAR SI ES LECTURA O ESCRITURA */
		if (!$usr->solo_lectura and $sactual->getPermisos(2)=='w') {
			$T->parse('puede_agregar', 'PUEDE_AGREGAR', false);
			$T->setVar('__form_icon_detail', 'spriteButton spriteButton-editar');
			$T->setVar('__form_label_detail', 'Modificar Contacto');
		}
		else {
			$T->setVar('__form_icon_detail', 'spriteButton spriteButton-ver');
			$T->setVar('__form_label_detail', 'Informaci&oacute;n Contacto');
		}
	}
}

/* SI SE INGRESA AL MENU HORARIOS DE NOTIFICACION */
elseif ($sactual->seccion_id == REP_SECCION_NOTIFICACION_HORARIO and $sactual->getPermisos(1) != '-') {

	/* SI SE EJECUTA UNA ACCION PARA LOS HORARIOS */
	if (isset($accion) and $accion != "") {
		include("commonHorarios.php");
	}

	/* SI SE MUESTRA LA LISTA DE HORARIOS DE NOTIFICACION */
	else {
		$T->setFile('tpl_contenido', 'lista_horarios.tpl');
		$T->setBlock('tpl_contenido', 'PUEDE_AGREGAR', 'puede_agregar');
		$T->setBlock('tpl_contenido', 'PUEDE_ELIMINAR', 'puede_eliminar');
		$T->setBlock('tpl_contenido', 'LISTA_HORARIOS', 'lista_horarios');
		$T->setBlock('tpl_contenido', 'MOSTRAR_HORARIOS_DISPONIBLES', 'mostrar_horarios_disponibles');

		/* LISTA DE HORARIOS */
		foreach ($usr->getHorarios(REP_HORARIO_NOTIFICACION) as $horario) {
			$T->setVar('puede_eliminar', '');
			if (!$usr->solo_lectura and $sactual->getPermisos(1) == 'w' and $horario->puedeEliminar()) {
				$T->setVar('__horario_id' ,$horario->horario_id);
				$T->setVar('__horario_nombre', $horario->nombre);
				$T->parse('puede_eliminar', 'PUEDE_ELIMINAR',true);
			}
			$T->setVar('__horario_id', $horario->horario_id);
			$T->setVar('__horario_nombre', $horario->nombre);
			$T->setVar('__horario_descripcion', $horario->descripcion);
			$T->setVar('__horario_tipo_nombre', $horario->tipo_nombre);
			$T->parse('lista_horarios', 'LISTA_HORARIOS', true);
		}

		/* VERIFICAR SI ES LECTURA O ESCRITURA */
		if (!$usr->solo_lectura and $sactual->getPermisos(1) == 'w') {
			$T->parse('puede_agregar', 'PUEDE_AGREGAR', false);
			$T->setVar('__form_icon_detail', 'spriteButton spriteButton-editar');
			$T->setVar('__form_label_detail', 'Modificar Horario');
		}
		else {
			$T->setVar('__form_icon_detail', 'spriteButton spriteButton-ver');
			$T->setVar('__form_label_detail', 'Informaci&oacute;n Horario');
		}

	}
}

/* MUESTRA UN ERROR SI NO ENTRO A NINGUNA SECCION */
else {
	$T->setFile('tpl_contenido', 'sorry_seccion.tpl');
}

$T->setVar('__sitio_contenido', $T->parse('out', 'tpl_contenido'));

?>