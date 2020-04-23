<?

if ($accion == "verificar_nombre_destinatario") {
	if (Validador::existeNombreDestinatario(false, $_POST["destinatario_nombre"], $_POST["destinatario_id"])) {
		echo 1;
	}
	else {
		echo 0;
	}
	exit();
}
elseif($accion == "creaSVG"){
	$grafico = new GraficoSVG();
	if(!$_REQUEST["item_tipo"]){
		$grafico->tipo="html";
		$grafico->timestamp = new Timestamp(date("Y-m-d 00:00:00", strtotime("-1 day")),date("Y-m-d 00:00:00"));
	}
	else
		$grafico->tipo=$_REQUEST["item_tipo"];

		$grafico->tipo_grafico="simple";
		$grafico->__item_id = $_REQUEST["objeto_id"];
		$grafico->objetivo_id = $_REQUEST["objeto_id"];
		$grafico->timestamp = new Timestamp(date("Y-m-d 00:00:00", strtotime("-1 day")),date("Y-m-d 00:00:00"));
		$svg= $grafico->getConsolidadoDisponibilidadSimple();
		echo $svg;
		//return $svg;
}
/* GUARDAR NOTIFICACION */
elseif ($accion == "guardar_notificacion") {

	/* MODIFICAR NOTIFICACION */
	if ($_POST["notificacion_id"]) {

		$notificacion = $usr->getNotificacion($_POST["notificacion_id"]);
		$destinatario = $usr->getDestinatario($_POST["notificacion_destinatario_id"]);
		$horario = $usr->getHorario($_POST["notificacion_horario_id"], 6);

		if ($sactual->getPermisos(2) == 'w' and $notificacion != null and $destinatario != null and $horario != null) {

			$objetivo = $notificacion->getConfigObjetivo();
			$servicio = $objetivo->getServicio();
			$servicio->__Servicio();

			$notificacion->escalabilidad_desde = $_POST["notificacion_escalabilidad_desde"];
			$notificacion->escalabilidad_hasta = $_POST["notificacion_escalabilidad_hasta"];
			if ($servicio->notificacion_uptime_parcial) {
				$notificacion->uptime_parcial = ($_POST["notificacion_uptime_parcial"])?true:false;
			}
			if ($servicio->notificacion_downtime_parcial) {
				$notificacion->downtime_parcial = ($_POST["notificacion_downtime_parcial"])?true:false;
			}
			if ($servicio->notificacion_downtime_grupal) {
				$notificacion->downtime_grupal = ($_POST["notificacion_downtime_grupal"])?true:false;
			}
			if ($servicio->notificacion_downtime_global) {
				$notificacion->downtime_global = ($_POST["notificacion_downtime_global"])?true:false;
			}
			if ($servicio->notificacion_patron_inverso) {
				$notificacion->patron_inverso = ($_POST["notificacion_patron_inverso"])?true:false;
			}
			if ($servicio->notificacion_sla) {
				$notificacion->sla = ($_POST["notificacion_sla"])?true:false;
			}

			$notificacion->__destinatario = $destinatario;
			$notificacion->__horario = $horario;
			$resultado=$notificacion->modificar();
            if($resultado==true){
               echo "ok";
            }
            else{
               echo "no";
            }
		}
		else {
			echo "no";
		}
	}
	/* AGREGAR NOTIFICACION */
	else {

		if ($sactual->getPermisos(3) == 'w') {
			$objetivo = $usr->getConfigObjetivo($_POST["notificacion_objetivo_id"], REP_DATOS_NOTIFICACION);
		}
		else {
			$objetivo = $usr->getConfigObjetivo($_POST["notificacion_objetivo_id"], REP_DATOS_USUARIO);
		}

		$destinatario = $usr->getDestinatario($_POST["notificacion_destinatario_id"]);
		$horario = $usr->getHorario($_POST["notificacion_horario_id"], 6);

		if ($sactual->getPermisos(2) == 'w' and $objetivo != null and $destinatario != null and $objetivo != null and $usr->puedeAgregarNotificaciones()) {

			$notificacion = new Notificacion($_POST["notificacion_id"]);

			$servicio = $objetivo->getServicio();
			$servicio->__Servicio();

			$notificacion->escalabilidad_desde = $_POST["notificacion_escalabilidad_desde"];
			$notificacion->escalabilidad_hasta = $_POST["notificacion_escalabilidad_hasta"];
			if ($servicio->notificacion_uptime_parcial) {
				$notificacion->uptime_parcial = ($_POST["notificacion_uptime_parcial"])?true:false;
			}
			if ($servicio->notificacion_downtime_parcial) {
				$notificacion->downtime_parcial = ($_POST["notificacion_downtime_parcial"])?true:false;
			}
			if ($servicio->notificacion_downtime_grupal) {
				$notificacion->downtime_grupal = ($_POST["notificacion_downtime_grupal"])?true:false;
			}
			if ($servicio->notificacion_downtime_global) {
				$notificacion->downtime_global = ($_POST["notificacion_downtime_global"])?true:false;
			}
			if ($servicio->notificacion_patron_inverso) {
				$notificacion->patron_inverso = ($_POST["notificacion_patron_inverso"])?true:false;
			}
			if ($servicio->notificacion_sla) {
				$notificacion->sla = ($_POST["notificacion_sla"])?true:false;
			}
			$notificacion->__destinatario = $destinatario;
			$notificacion->__horario = $horario;
			$notificacion->__objetivo = $objetivo;
			$resultado=$notificacion->agregar();
            if($resultado==true){
               echo "ok";
               Utiles::enviaCorreo($destinatario,"new_alarma",$destinatario->tipo_id,$objetivo);

            }
            else{
               echo "no";
            }

		}
		else {
			echo "no";
		}
	}

	/* ASOCIAR SLA A OBJETIVO DE NOTIFICACION */
	if (isset($notificacion)) {
		if ($notificacion->sla and $notificacion->__objetivo->objetivo_id == $_POST["notificacion_objetivo_id"]) {
			foreach ($objetivo->getMonitores() as $id => $monitor) {
				$objetivo->eliminarSlaNotificacion($monitor->monitor_id);
				foreach ($objetivo->__pasos as $paso) {
					if(isset($_POST["paso_sla_".$monitor->monitor_id."_".$paso->paso_id]) and $_POST["paso_sla_".$monitor->monitor_id."_".$paso->paso_id]!="") {
						$objetivo->guardarSlaNotificacion($monitor->monitor_id, $paso->paso_id, $_POST["paso_sla_".$monitor->monitor_id."_".$paso->paso_id]);
					}
				}
			}
		}
	}

	unset($accion);
	unset($servicio);
	unset($objetivo);
	unset($destinatario);
	unset($horario);
	unset($notificacion);
	exit();
}

/* ELIMINAR NOTIFICACION */
elseif ($accion == "eliminar_notificacion") {

	$notificacion = $usr->getNotificacion($_POST["notificacion_id"]);
	$destinatario = $usr->getDestinatario($notificacion->__destinatario->destinatario_id);

	if ($sactual->getPermisos(3) == 'w') {
		$objetivo = $usr->getConfigObjetivo($notificacion->__objetivo->objetivo_id, REP_DATOS_NOTIFICACION);
	}
	else {
		$objetivo = $usr->getConfigObjetivo($notificacion->__objetivo->objetivo_id, REP_DATOS_USUARIO);
	}
	if ($sactual->getPermisos(2) == 'w' and $notificacion != null) {

		$resultado=$notificacion->eliminar();
        if($resultado==true){
          echo "ok";
          Utiles::enviaCorreo($destinatario,"rm_alarma",$destinatario->tipo_id,$objetivo);

        }
        else{
          echo "no";
        }

	}
	else {
		echo "no";
	}

	unset($accion);
	unset($notificacion);
	exit();
}

/* GUARDAR DESTINATARIO */
elseif ($accion == "guardar_destinatario") {
	/* MODIFICAR DESTINATARIO */
	if ($_POST["destinatario_id"]) {

		$destinatario = $usr->getDestinatario($_POST["destinatario_id"]);
		$destinatario_antiguo= clone $usr->getDestinatario($_POST["destinatario_id"]);

		if ($sactual->getPermisos(2) == 'w' and $destinatario != null) {

			$destinatario->nombre = $_POST["destinatario_nombre"];
			$destinatario->contacto = $_POST["destinatario_contacto"];
			$destinatario->tipo_id = $_POST["destinatario_tipo"];
			$destinatario->telefono = $_POST["destinatario_telefono"];
			$destinatario->modificar();
			unset($usr->__destinatarios[$_POST["destinatario_id"]]);
			$destinatario = $usr->getDestinatario($_POST["destinatario_id"]);

			if($destinatario->contacto != $destinatario_antiguo->contacto){
				Utiles::enviaCorreo($destinatario,"new_contacto",$destinatario->tipo_id);
				Utiles::enviaCorreo($destinatario_antiguo,"rm_contacto",$destinatario->tipo_id);
			}
			else{
				Utiles::enviaCorreo($destinatario,"edit_contacto",$destinatario->tipo_id);
			}


		}
		else {
			throw new Exception('No tiene permisos para modificar este destinatario.');
		}
	}

	/* AGREGAR DESTINATARIO */
	else {
		if ($sactual->getPermisos(2) == 'w') {
			$destinatario = new Destinatario($_POST["destinatario_id"]);
			$destinatario->nombre = $_POST["destinatario_nombre"];
			$destinatario->contacto = $_POST["destinatario_contacto"];
			$destinatario->tipo_id = $_POST["destinatario_tipo"];
			$destinatario->telefono = $_POST["destinatario_telefono"];
			$destinatario->agregar();
			Utiles::enviaCorreo($destinatario,"new_contacto",$destinatario->tipo_id);

		}
		else {
			throw new Exception('No tiene permisos para agregar un destinatario.');
		}
	}

	unset($accion);

	/* SI SE GUARDA DESDE UNA NOTIFICACION */
	if (isset($_POST["notificacion_id"]) and $_POST["notificacion_id"] != "") {
		$notificacion_destinatario_id = $destinatario->destinatario_id;
		$notificacion_horario_id = $_POST["notificacion_horario_id"];
		$accion = "modificar_notificacion";
	}
	unset($destinatario);
}

/* ELIMINAR UN DESTINATARIO */
elseif ($accion == "eliminar_destinatario") {

	$destinatario = $usr->getDestinatario($_POST["destinatario_id"]);

	if ($sactual->getPermisos(2)=='w' and $destinatario != null and $destinatario->puedeEliminar()) {
		$destinatario->eliminar();
		Utiles::enviaCorreo($destinatario,"rm_contacto",$destinatario->tipo_id);

	}
	else {
		throw new Exception('No tiene permisos para eliminar este destinatario.');
	}

	unset($accion);
	unset($destinatario);
}

/* ACCIONES RELACIONADAS A HORARIOS */
else {
	include("actionHorarios.php");
}

?>