<?

if ($accion == "verificar_nombre_horario") {
	if ($sactual->seccion_id == REP_SECCION_OBJETIVO_HORARIO) {
		$horario_tipo = REP_HORARIO_HABIL;
	}
	elseif ($sactual->seccion_id == REP_SECCION_NOTIFICACION_HORARIO) {
		$horario_tipo = REP_HORARIO_NOTIFICACION;	
	}
	elseif ($sactual->seccion_id == REP_SECCION_OBJETIVO_MANTENCION) {
		$horario_tipo = REP_HORARIO_MANTENCION;
	}
	if (Validador::existeNombreHorario(false, $_POST["horario_nombre"], $horario_tipo, $_POST["horario_id"])) {
		echo 1;
	}
	else {
		echo 0;
	}
	exit();
}

/* GUARDAR HORARIO */
elseif ($accion == "guardar_horario") {
	
	/* MODIFICAR HORARIO */
	if ($_POST["horario_id"]) {
		
		if ($sactual->seccion_id == REP_SECCION_OBJETIVO_HORARIO) {
			$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_HABIL);
		}
		elseif ($sactual->seccion_id == REP_SECCION_OBJETIVO_MANTENCION) {
			$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_MANTENCION);
		}
		elseif ($sactual->seccion_id == REP_SECCION_NOTIFICACION_HORARIO) {
			$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_NOTIFICACION);
		}
		
		if ($sactual->getPermisos(1) == 'w' and $horario != null) {
			$horario->nombre = $_POST["horario_nombre"];
			$horario->descripcion = $_POST["horario_descripcion"];
			$horario->modificar();
		}
		else {
			throw new Exception('No tiene permisos para modificar este horario.');
		}
	}
	
	/* AGREGAR HORARIO */
	else {

		if ($sactual->seccion_id == REP_SECCION_OBJETIVO_HORARIO) {
			$horario_tipo = REP_HORARIO_HABIL;
			$puede_agregar = $usr->puedeAgregarHorarioHabil();
		}
		elseif ($sactual->seccion_id == REP_SECCION_OBJETIVO_MANTENCION) {
			$horario_tipo = REP_HORARIO_MANTENCION;
			$puede_agregar = true;
		}
		elseif ($sactual->seccion_id == REP_SECCION_NOTIFICACION_HORARIO or $sactual->seccion_id == REP_SECCION_NOTIFICACION) {
			$horario_tipo = REP_HORARIO_NOTIFICACION;
			$puede_agregar = true;
		}
		
		if ($sactual->getPermisos(1) == 'w' and $horario_tipo != null and $puede_agregar) {
			$horario = new Horario($_POST["horario_id"]);
			$horario->nombre = $_POST["horario_nombre"];
			$horario->descripcion = $_POST["horario_descripcion"];
			$horario->tipo_id = $horario_tipo;
			$horario->agregar();
		}
		else {
			throw new Exception('No tiene permisos para agregar un horario.');
		}
	}

	/* ASOCIAR ITEM A HORARIO */
	if (isset($horario)) {
		$horario->eliminarDiaSemanaItems();
		foreach ($_POST as $id => $valor) {
			if (preg_match('/dia_semana_([0-9]+)_([0-9]+)_([0-9]+)/', $id, $reg)) {
				$item = new HorarioItem(0);
				$item->es_incluido = true;
				$item->dia_semana = $reg[1];
				$item->hora_inicio = $reg[2].":00:00";
				$item->hora_termino = $reg[3].":00:00";
				$item->__horario_id = $horario->horario_id;
				$item->agregar();
			}
		}
	}
	
	unset($accion);
	
	/* DEPRECATED: SI SE GUARDA DESDE UNA NOTIFICACION */
	if (isset($_POST["notificacion_id"]) and $_POST["notificacion_id"] != "") {
		$notificacion_horario_id = $horario->horario_id;
		$notificacion_destinatario_id = $_POST["notificacion_destinatario_id"];
		$accion = "modificar_notificacion";
	}
	
	if ($sactual->seccion_id == REP_SECCION_OBJETIVO_MANTENCION) {
		$_REQUEST["horario_id"] = $horario->horario_id;
		$accion = "modificar_horario";
	}
	
	unset($horario);
}

/* SI SE QUIERE ELIMINAR UN HORARIO */
elseif ($accion == "eliminar_horario") {

	if ($sactual->seccion_id == REP_SECCION_OBJETIVO_HORARIO) {
		$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_HABIL);
	}
	elseif ($sactual->seccion_id == REP_SECCION_OBJETIVO_MANTENCION) {
		$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_MANTENCION);
	}
	elseif ($sactual->seccion_id == REP_SECCION_NOTIFICACION_HORARIO) {
		$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_NOTIFICACION);
	}
	
	if ($sactual->getPermisos(1) == 'w' and $horario != null and $horario->puedeEliminar()) {
		$horario->eliminar();
	}
	else {
		throw new Exception('No tiene permisos para eliminar este horario.');
	}
	
	unset($accion);
	unset($horario);
}

/* GUARDAR UN ITEM */
elseif ($accion=="guardar_item") {
	
	if ($sactual->seccion_id == REP_SECCION_OBJETIVO_HORARIO) {
		$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_HABIL);
	}
	elseif ($sactual->seccion_id == REP_SECCION_NOTIFICACION_HORARIO) {
		$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_NOTIFICACION);
	}
	elseif ($sactual->seccion_id == REP_SECCION_OBJETIVO_MANTENCION) {
		$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_MANTENCION);
	}
	
	if ($horario != null) {

		/* MODIFICAR ITEM */
		if ($_POST["item_id"]) {
			$item = $horario->getHorarioItem($_POST["item_id"]);
		
			if ($sactual->getPermisos(1) == 'w' and $item != null) {

				/* DEPENDIENDO DEL TIPO DE FILTRO SON LOS DATOS QUE SE GUARDARAN */
				if ($_POST["sel_filtro"] == 1) {
					$item->fecha_inicio = $_POST["item_fecha_inicio"];
					$item->fecha_termino = $_POST["item_fecha_termino"];
				}
				elseif ($_POST["sel_filtro"] == 2) {
					$item->dia = $_POST["item_dia"];
					$item->mes = $_POST["item_mes"];
					$item->anno = $_POST["item_anno"];
				}
				else {
					$item->dia_semana = $_POST["item_dia_semana"];
				}
				$item->hora_inicio = $_POST["item_hora_inicio"];
				$item->hora_termino = $_POST["item_hora_termino"];
				$item->es_incluido = $_POST["item_es_incluido"];
				$item->__horario_id = $horario->horario_id;
				$item->modificar();
			}
			else {
				throw new Exception('No tiene permisos para modificar este item de horario.');
			}
		}

		/* AGREGAR ITEM */
		else {
			if ($sactual->getPermisos(1) == 'w') {

				$item = new HorarioItem($_POST["item_id"]);

				/* DEPENDIENDO DEL TIPO DE FILTRO SON LOS DATOS QUE SE GUARDARAN */
				if ($_POST["sel_filtro"] == 1) {
					$item->fecha_inicio = $_POST["item_fecha_inicio"];
					$item->fecha_termino = $_POST["item_fecha_termino"];
				}
				elseif ($_POST["sel_filtro"] == 2) {
					$item->dia = $_POST["item_dia"];
					$item->mes = $_POST["item_mes"];
					$item->anno = $_POST["item_anno"];
				}
				else {
					$item->dia_semana = $_POST["item_dia_semana"];
				}
				$item->hora_inicio = $_POST["item_hora_inicio"];
				$item->hora_termino = $_POST["item_hora_termino"];
				$item->es_incluido = $_POST["item_es_incluido"];
				$item->descripcion = $_POST["item_descripcion"];
				$item->__horario_id = $horario->horario_id;
				$item->agregar();
			}
			else {
				throw new Exception('No tiene permisos para agregar un item a este horario.');
			}
		}
	}
	else {
		throw new Exception('No tiene permisos para modificar este horario.');
	}

	$accion = "modificar_horario";
	unset($item);
	unset($horario);
}

/* SI SE QUIERE ELIMINAR UN ITEM */
elseif ($accion=="eliminar_item") {
	if ($sactual->seccion_id == REP_SECCION_OBJETIVO_HORARIO) {
		$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_HABIL);
	}
	elseif ($sactual->seccion_id == REP_SECCION_OBJETIVO_MANTENCION) {
		$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_MANTENCION);
	}
	elseif ($sactual->seccion_id == REP_SECCION_NOTIFICACION_HORARIO) {
		$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_NOTIFICACION);
	}
	
	if ($horario != null) {
		$item = $horario->getHorarioItem($_POST["item_id"]);
		
		if ($sactual->getPermisos(1) == 'w' and $item != null) {
			$item->eliminar();
		}
		else {
			throw new Exception('No tiene permisos para eliminar este item.');
		}
	}
	else {
		throw new Exception('No tiene permisos para modificar este horario.');
	}

	$accion = "modificar_horario";
	unset($item);
	unset($horario);
}

/* SI SE QUIERE COPIAR LOS ITEMS DE UN HORARIO A OTRO */
elseif ($accion == "copiar_horario") {

	if ($sactual->seccion_id == REP_SECCION_OBJETIVO_HORARIO) {
		$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_HABIL);
	}
	elseif ($sactual->seccion_id == REP_SECCION_NOTIFICACION_HORARIO) {
		$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_NOTIFICACION);
	}
	
	$horario_asignable = $usr->getHorario($_POST["asignable_id"], REP_HORARIO_ASIGNABLE);
	
	if ($sactual->getPermisos(1) == 'w' and $horario != null and $horario_asignable != null) {
		$horario->copiar($horario_asignable->horario_id);
	}
	else {
		throw new Exception('No tiene permisos para copiar items a este horario.');
	}

	$accion = "modificar_horario";
	unset($horario);
	unset($horario_asignable);
}

/* SI SE QUIERE LINKEAR LOS ITEMS DE UN HORARIO A OTRO */
elseif ($accion == "linkear_horario") {
	if ($sactual->seccion_id == REP_SECCION_OBJETIVO_HORARIO) {
		$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_HABIL);
	}
	elseif ($sactual->seccion_id == REP_SECCION_NOTIFICACION_HORARIO) {
		$horario = $usr->getHorario($_POST["horario_id"], REP_HORARIO_NOTIFICACION);
	}
	
	$horario_asignable = $usr->getHorario($_POST["asignable_id"], REP_HORARIO_ASIGNABLE);
	
	if ($sactual->getPermisos(1) == 'w' and $horario != null and $horario_asignable != null) {
		$horario->linkear($horario_asignable->horario_id);
	}
	else {
		throw new Exception('No tiene permisos para copiar items a este horario.');
	}

	$accion = "modificar_horario";
	unset($horario);
	unset($horario_asignable);
}

/* MENSAJE DE ERROR CUANDO NO SE REALIZO ACCION */
else {
	throw new Exception('La acci&oacute;n que intento realizar no es valida.');
}

?>