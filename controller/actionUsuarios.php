<?
/* FUNCION AJAX QUE DICE SI EL EMAIL YA EXITE */
if ($accion == "verificar_email_usuario") {
	if (Validador::existeEmailUsuario(false, $_POST["usuario_cliente_email"], $_POST["usuario_cliente_id"])) {
		echo 1;
	}
	else {
		echo 0;
	}
	exit();
}

elseif ($accion == "verificar_nombre_usuario") {
	if (Validador::existeNombreUsuario(false, $_POST["usuario_cliente_nombre"], $_POST["usuario_cliente_id"])) {
		echo 1;
	}
	else {
		echo 0;
	}
	exit();
}

/* FUNCION AJAX QUE DICE SI LA CLAVE ANTERIOR DEL USUARIO ES CORRECTA */
elseif ($accion == "verificar_clave_actual_usuario") {
	if (md5($_POST["usuario_clave_actual"]) == $usr->clave_md5) {
		echo 1;
	}
	else {
		echo 0;
	}
	exit();
}

elseif ($accion == "verificar_nombre_subcliente") {
	if (Validador::existeNombreSubcliente(false, $_POST["subcliente_nombre"], $_POST["subcliente_id"])) {
		echo 1;
	}
	else {
		echo 0;
	}
	exit();
}

/* SI SE QUIERE GUARDAR EL PERFIL DEL USUARIO */
elseif ($accion == "guardar_perfil") {
	
	if ($sactual->getPermisos(1) == 'w' and ($_POST["usuario_clave1"] == "" or $usr->clave_md5 == md5($_POST["usuario_clave_actual"]))) {
		$usr->nombre = $_POST["usuario_nombre"];
		$usr->telefono = $_POST["usuario_telefono"];
		$usr->cargo = $_POST["usuario_cargo"];
		$usr->clave = $_POST["usuario_clave1"];
		$usr->zona_horaria_id = $_POST["zona_horaria_id"];
		$usr->idioma_id = 1; //$_POST["idioma_id"];
		$usr->periodo_semaforo_id = $_POST["intervalo_id"];
		$usr->orientacion_semaforo = $_POST["orientacion_id"];
//		$usr->modificar();
		$usr->modificarPerfil();

		/* SI CAMBIO EL EMAIL DEL USUARIO QUE SE ESTA UTILIZANDO, 
		 * TENGO QUE CAMBIARLO TAMBIEN EN LA SESION */
		$auth->session["username"] = $usr->email;
		$log->setChange("MODIFICO USUARIO", $usr->toString());
	}
	else {
		throw new Exception('No tiene permisos para modificar su perfil.');
	}
}

/* SI SE QUIERE GUARDAR UN USUARIO */
elseif ($accion == "guardar_usuario") {

/*	$usuario_subclientes = array();
	foreach ($usr->getSubClientes(REP_DATOS_CLIENTE) as $subcliente) {
		if($_POST["usuario_subcliente_".$subcliente->subcliente_id]) {
			$usuario_subclientes[] = $subcliente->subcliente_id;
		}
	}
	
	if (count($usuario_subclientes) == 0) {
		throw new Exception('No puede crear/modificar un usuario sin ningun subcliente.');
	}*/
	
	/* MODIFICAR USUARIO CLIENTE */
	if ($_POST["usuario_cliente_id"]) {
		$usuario_cliente = $usr->getUsuarioCliente($_POST["usuario_cliente_id"]);
		if ($usuario_cliente->perfil_id == $_POST["perfil_id"]) {
			$perfil = $usr->getTipoPerfil($_POST["perfil_id"], REP_DATOS_CLIENTE);
		}
		else {
			$perfil = $usr->getTipoPerfil($_POST["perfil_id"], REP_DATOS_USUARIO);
		}
		
		if ($sactual->getPermisos(3) == 'w' and $usuario_cliente != null and $usuario_cliente->puedeModificar() and $perfil != null) {
			$usuario_cliente->nombre = $_POST["usuario_cliente_nombre"];
			$usuario_cliente->email = $_POST["usuario_cliente_email"];
			$usuario_cliente->telefono = $_POST["usuario_cliente_telefono"];
			$usuario_cliente->cargo = $_POST["usuario_cliente_cargo"];
			$usuario_cliente->clave = $_POST["usuario_cliente_clave1"];
			$usuario_cliente->perfil_id = $_POST["perfil_id"];
			$usuario_cliente->zona_horaria_id = $_POST["zona_horaria_id"];
			$usuario_cliente->modificar();

			/* SI CAMBIO EL EMAIL DEL USUARIO QUE SE ESTA UTILIZANDO, 
			 * TENGO QUE CAMBIARLO TAMBIEN EN LA SESION */
			if ($usuario_cliente->usuario_id == $usr->usuario_id) {
				$auth->session["username"] = $usuario_cliente->email;
				$usr->__Usuario();
				$sactual->__Seccion();
			}
		}
		else {
			throw new Exception('No tiene permisos para modificar este usuario.');
		}
	}
	
	/* AGREGAR USUARIO CLIENTE */
	else {
		
		$perfil = $usr->getTipoPerfil($_POST["perfil_id"], REP_DATOS_USUARIO);
		
		if ($sactual->getPermisos(3) == 'w' and $perfil != null and $usr->puedeAgregarUsuarios()) {
			$usuario_cliente = new Usuario($_POST["usuario_cliente_id"]);
			$usuario_cliente->nombre = $_POST["usuario_cliente_nombre"];
			$usuario_cliente->email = $_POST["usuario_cliente_email"];
			$usuario_cliente->telefono = $_POST["usuario_cliente_telefono"];
			$usuario_cliente->cargo = $_POST["usuario_cliente_cargo"];
			$usuario_cliente->clave = $_POST["usuario_cliente_clave1"];
			$usuario_cliente->perfil_id = $_POST["perfil_id"];
			$usuario_cliente->zona_horaria_id = $_POST["zona_horaria_id"];
			$usuario_cliente->idioma_id = 1;
			$usuario_cliente->agregar();
		}
		else {
			throw new Exception('No tiene permisos para agregar un usuario.');
		}
	}

	/* SI SE QUIERE ASOCIAR UN USUARIO A UN SUBCLIENTE */
	if (isset($usuario_cliente)) {
/*		$subclientes = $usr->getSubClientes(REP_DATOS_CLIENTE);
		foreach ($subclientes as $subcliente) {
			if (!$subcliente->readonly and in_array($subcliente->subcliente_id, $usuario_subclientes)) {
				$subcliente->asociarUsuario($usuario_cliente->usuario_id);
			}
		}
		foreach ($subclientes as $subcliente) {
			if (!$subcliente->readonly and !in_array($subcliente->subcliente_id, $usuario_subclientes)) {
				$subcliente->desasociarUsuario($usuario_cliente->usuario_id);
			}
		}*/
		foreach ($usr->getSubClientes(REP_DATOS_CLIENTE) as $subcliente) {
			if (!$subcliente->readonly) {
				if($_POST["usuario_subcliente_".$subcliente->subcliente_id]) {
					$subcliente->asociarUsuario($usuario_cliente->usuario_id);
				}
				else {
					$subcliente->desasociarUsuario($usuario_cliente->usuario_id);
				}
			}
		}
	}
	
	unset($accion);
	unset($perfil);
	unset($usuario_cliente);
}

/* SI SE QUIERE ELIMINAR UN USUARIO */
elseif ($accion == "eliminar_usuario") {
	$usuario_cliente = $usr->getUsuarioCliente($_POST["usuario_cliente_id"]);
	
	if ($sactual->getPermisos(3) == 'w' and  $usuario_cliente != null and $usuario_cliente->puedeEliminar()) {
		$usuario_cliente->eliminar();
	}
	else {
		throw new Exception('No tiene permisos para eliminar este usuario.');
	}
	
	unset($accion);
	unset($usuario_cliente);
}

/* SI SE QUIERE GUARDAR UN SUBCLIENTE */
elseif ($accion == "guardar_subcliente") {
	
	/* MODIFICAR SUBCLIENTE */
	if ($_POST["subcliente_id"]) {
		$subcliente = $usr->getSubCliente($_POST["subcliente_id"]);
		
		if ($sactual->getPermisos(3) == 'w' and $subcliente != null) {
			$subcliente->nombre = $_POST["subcliente_nombre"];
			$subcliente->descripcion = $_POST["subcliente_descripcion"];
			$subcliente->modificar();
		}
		else {
			throw new Exception('No tiene permisos para modificar este subcliente.');
		}
	}
	
	/* AGREGAR SUBCLIENTE */
	else {
		
		if ($sactual->getPermisos(3) == 'w') {
			$subcliente = new SubCliente($_POST["subcliente_id"]);
			$subcliente->nombre = substr($_POST["subcliente_nombre"], 0, 30);
			$subcliente->descripcion = $_POST["subcliente_descripcion"];
			$subcliente->agregar();
		}
		else {
			throw new Exception('No tiene permisos para agregar un subcliente.');
		}
	}
	
	unset($accion);
	unset($subcliente);
}

/* SI SE QUIERE ELIMINAR UN SUBCLIENTE */
elseif ($accion == "eliminar_subcliente") {
	$subcliente = $usr->getSubCliente($_POST["subcliente_id"]);
	
	if ($sactual->getPermisos(3) == 'w' and $subcliente != null) {
		$subcliente->eliminar();
	}
	else {
		throw new Exception('No tiene permisos para eliminar este subcliente.');
	}

	unset($accion);
	unset($subcliente);
}

/* SI SE QUIERE ASOCIAR A UN SUBCLIENTE VARIOS USUARIOS */
elseif ($accion == "asociar_subcliente_usuarios") {
	$subcliente = $usr->getSubCliente($_POST["subcliente_id"]);
	if ($sactual->getPermisos(3) == 'w' and $subcliente != null) {
		foreach ($usr->getUsuariosCliente() as $usuario_cliente) {
			if($_POST["subcliente_usuario_".$usuario_cliente->usuario_id]) {
				$subcliente->asociarUsuario($usuario_cliente->usuario_id);
			}
			else {
				$subcliente->desasociarUsuario($usuario_cliente->usuario_id);
			}
		}
		$group =$subcliente->subcliente_id;
		$name_group = $subcliente->nombre;
		$users = array_keys($usr->getUsuariosCliente());
		$users = (implode(",", $users));
		$detalle = 'USUARIOS: '.$users.' GRUPO:'.$group.' NOMBRE GRUPO: '.$name_group;
		$log->setChange('MODIFICO_USUARIO_GRUPO_REPORTE',$detalle);
	}
	else {
		throw new Exception('No tiene permisos para asociar usuarios a este subcliente.');
	}
	unset($accion);
	unset($subcliente);
}

/* SI SE QUIERE ASOCIAR A UN SUBCLIENTE VARIOS OBJETIVOS */
elseif ($accion == "asociar_subcliente_objetivos") {
	$subcliente = $usr->getSubCliente($_POST["subcliente_id"]);
	
	if ($sactual->getPermisos(3) == 'w' and $subcliente != null) {
		foreach ($usr->getObjetivos(REP_DATOS_CLIENTE) as $objetivo) {
			if($_POST["subcliente_objetivo_".$objetivo->objetivo_id]) {
				$subcliente->asociarObjetivo($objetivo->objetivo_id);
			}
			else {
				$subcliente->desasociarObjetivo($objetivo->objetivo_id);
			}
		}
		$group =$subcliente->subcliente_id;
		$name_group = $subcliente->nombre;
		$users = array_keys($usr->getUsuariosCliente());
		$users = (implode(",", $users));
		$detalle = 'USUARIOS: '.$users.' GRUPO:'.$group.' NOMBRE GRUPO: '.$name_group;
		$log->setChange('MODIFICO_OBJETIVO_GRUPO_REPORTE',$detalle);
	}
	else {
		throw new Exception('No tiene permisos para asociar objetivos a este subcliente.');
	}

	unset($accion);
	unset($subcliente);
}

/* SI SE QUIERE GUARDAR UN SUBCLIENTE */
elseif ($accion == "guardar_token") {
	
	/* MODIFICAR SUBCLIENTE */
	if ($_POST["token_id"]) {
		$token = $usr->getTokenWS($_POST["token_id"]);
		
		if ($sactual->getPermisos(3) == 'w' and $token != null) {
			$token->nombre = $_POST["token_nombre"];
//			$token->fecha_expiracion = $_POST["fecha_expiracion"];
			$token->modificar();
		}
		else {
			throw new Exception('No tiene permisos para modificar este token.');
		}
	}
	
	/* AGREGAR SUBCLIENTE */
	else {
		
		if ($sactual->getPermisos(3) == 'w') {
			$token = new TokenWs($_POST["token_id"]);
			$token->nombre = $_POST["token_nombre"];
//			$token->fecha_expiracion = $_POST["fecha_expiracion"];
			$token->agregar();
		}
		else {
			throw new Exception('No tiene permisos para agregar un token.');
		}
	}
	
	unset($accion);
	unset($token);
}

/* SI SE QUIERE ELIMINAR UN SUBCLIENTE */
elseif ($accion == "eliminar_token") {
	$token = $usr->getTokenWS($_POST["token_id"]);
	
	if ($sactual->getPermisos(3) == 'w' and $token != null) {
		$token->eliminar();
	}
	else {
		throw new Exception('No tiene permisos para eliminar este token.');
	}

	unset($accion);
	unset($token);
}

/* MENSAJE DE ERROR CUANDO NO SE REALIZO ACCION */
else {
	throw new Exception('La acci&oacute;n que intento realizar no es valida.');
}



?>