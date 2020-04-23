<?


/* FUNCION AJAX QUE GUARDA EL ORDEN DE LOS OBJETIVOS */
if ($accion == "guardar_orden_objetivo") {
	if ($sactual->getPermisos(1) == 'w') {
		foreach ($usr->getObjetivos(REP_DATOS_USUARIO) as $objetivo) {
			if($_POST["objetivo_orden_".$objetivo->objetivo_id]>=0) {
				$objetivo->ordenar($_POST["objetivo_orden_".$objetivo->objetivo_id]);
			}
		}
	}
	exit();
}

/* FUNCION AJAX QUE VERIFICA EL NOMBRE DEL OBJETIVO */
elseif ($accion == "verificar_nombre_objetivo") {
	if (Validador::existeNombreObjetivo(false, $_POST["objetivo_nombre"], $_POST["objetivo_id"])) {
		echo 1;
	}
	else {
		echo 0;
	}
	exit();
}

/* SI SE QUIERE GUARDAR UN OBJETIVO */
elseif ($accion == "guardar_objetivo") {
	
	$objetivo_id = $_REQUEST["objetivo_id"];
	
	/* BUSCO LOS DATOS DEL OBJETIVO DEPENDIENDO DE LA SECCION */
	if ($sactual->seccion_id == 88) {
		$objetivo = $usr->getConfigObjetivo($objetivo_id, REP_DATOS_CLIENTE);
	}
	else {
		$objetivo = $usr->getConfigObjetivo($objetivo_id);
	}
	
	if ($sactual->getPermisos(2) == 'w' and $objetivo != null) {
	
		$objetivo->nombre = $_POST["objetivo_nombre"];
		$objetivo->descripcion = $_POST["objetivo_descripcion"];
		$objetivo->sla_dis_ok = $_POST["objetivo_sla_dis_ok"];
		$objetivo->sla_dis_error = $_POST["objetivo_sla_dis_error"];
		$objetivo->sla_ren_ok = $_POST["objetivo_sla_ren_ok"];
		$objetivo->sla_ren_error = $_POST["objetivo_sla_ren_error"];
	
		if (in_array($objetivo->getServicio()->getTipoSetup(), array(REP_SETUP_WEB, REP_SETUP_BROWSER, REP_SETUP_MOBILE,REP_SETUP_IVR))) {
			foreach ($objetivo->__pasos as $paso) {
				if (isset($_POST["paso_nombre_".$paso->paso_id])) {
					$paso->nombre = $_POST["paso_nombre_".$paso->paso_id];
				}
			}
		}
		$objetivo->modificar();
	}
	else {
		throw new Exception('No tiene permisos para modificar este objetivo.');
	}
	unset($accion);
	unset($objetivo);
}

elseif ($accion == "guardar_ponderacion") {

	if ($sactual->getPermisos(2) != 'w') {
		throw new Exception('No tiene permisos para modificar este ponderaciones.');	
	}
	
	$suma = 0;
	foreach ($_POST as $id => $valor) {
		if (preg_match('/ponderacion_([0-9]+)_([0-9]+)/', $id, $reg)) {
			if (is_numeric($valor)) {
				$suma += $valor;
			}
		}
	}
	
	// Parece tonto esta multiplicacion, pero php tiene problemas para igualar con 3 decimales
	if (floor($suma) !== 100) {
		$ponderacion = $usr->getPonderacion();
	
		if ($ponderacion == null) {
			$ponderacion = new Ponderacion();
			$ponderacion->nombre = "Default";
			$ponderacion->intervalo = (isset($_REQUEST["intervalo_id"]))?$_REQUEST["intervalo_id"]:0;
			$ponderacion->agregar();
		}
		else {
			$ponderacion->intervalo = (isset($_REQUEST["intervalo_id"]))?$_REQUEST["intervalo_id"]:0;
			$ponderacion->modificar();
		}
	
		$ponderacion->eliminarItems();
		foreach ($_POST as $id => $valor) {
			if (preg_match('/ponderacion_([0-9]+)_([0-9]+)/', $id, $reg)) {
				$item = new PonderacionItem();
				$item->inicio = $reg[1].":00:00";
				$item->termino = $reg[2].":00:00";
				$item->valor = ($valor>0 && $valor<=100)?$valor:0;
				$item->__ponderacion_id = $ponderacion->ponderacion_id;
				$item->agregar();
			}
		}
	}
	else {
		throw new Exception('La suma de las ponderaciones por horario debe ser 100%.');
	}
}

/* ACCIONES RELACIONADAS A HORARIOS */
else {
	include("actionHorarios.php");
}

?>