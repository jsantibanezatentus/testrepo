<?

$usuario_cliente_id = $_REQUEST["usuario_cliente_id"];
$subcliente_id = $_REQUEST["subcliente_id"];
$token_id = $_REQUEST["token_id"];

/* SI SE INGRESA AL MENU USUARIOS */
if ($sactual->seccion_id == REP_SECCION_USUARIO and $sactual->getPermisos(3) != '-') {

	/* MUESTRA EL FORMULARIO PARA INGRESO Y MODIFICACION DE USUARIOS */
	if ($accion == "modificar_usuario") {
		
		$T->setFile('tpl_contenido', 'form_usuario.tpl');
		$T->setBlock('tpl_contenido', 'PUEDE_MODIFICAR', 'puede_modificar');
		$T->setBlock('tpl_contenido', 'LISTA_PERFILES', 'lista_perfiles');
		$T->setBlock('tpl_contenido', 'LISTA_ZONAS_HORARIAS', 'lista_zonas_horarias');
//		$T->setBlock('tpl_contenido', 'LISTA_IDIOMAS', 'lista_idiomas');
		$T->setBlock('tpl_contenido', 'LISTA_SUBCLIENTES', 'lista_subclientes');

		$T->setVar('__accion_sitio_id', $sitio_id);
		$T->setVar('__accion_menu_id', $sactual->seccion_id);
	
		/* OBTENER LOS DATOS DEL USUARIO CLIENTE */
		if ($usuario_cliente_id) {
			$usuario_cliente = $usr->getUsuarioCliente($usuario_cliente_id);
			$subclientes = $usuario_cliente->getSubClientes(REP_DATOS_USUARIO);
			$T->setVar('__usuario_cliente_id', $usuario_cliente->usuario_id);
			$T->setVar('__usuario_cliente_nombre', $usuario_cliente->nombre);
			$T->setVar('__usuario_cliente_email', $usuario_cliente->email);
			$T->setVar('__usuario_cliente_telefono', $usuario_cliente->telefono);
			$T->setVar('__usuario_cliente_cargo', $usuario_cliente->cargo);
			$T->setVar('__imagen', 'Guardar');
			$puede_modificar = $usuario_cliente->puedeModificar();
		}
		else {
			$T->setVar('__usuario_cliente_id', 0);
			$T->setVar('__imagen', 'Crear');
			$puede_modificar = true;
		}

		/* TIPOS DEL USUARIO CLIENTE */
		$perfiles = $usr->getTiposPerfiles(REP_DATOS_USUARIO);
		if (!isset($perfiles[$usuario_cliente->perfil_id]) and $usuario_cliente_id) {
			$T->setVar('__perfil_id', $usuario_cliente->perfil_id);
			$T->setVar('__perfil_nombre', $usuario_cliente->perfil_nombre);
			$T->setVar('__perfil_sel', 'selected');
			$T->parse('lista_perfiles', 'LISTA_PERFILES', true);
		}
		foreach ($perfiles as $id => $perfil) {
			$T->setVar('__perfil_id', $id);
			$T->setVar('__perfil_nombre', $perfil["nombre"]);
			$T->setVar('__perfil_sel', ($usuario_cliente and $usuario_cliente->perfil_id==$id)?"selected":"");
			$T->parse('lista_perfiles', 'LISTA_PERFILES', true);
		}
		
		/* ZONAS HORARIAS DEL USUARIO CLIENTE */
		foreach (Constantes::getZonasHorarias() as $id => $nombre) {
			$T->setVar('__zona_horaria_id', $id);
			$T->setVar('__zona_horaria_nombre', $nombre);
			if ($usuario_cliente) {
				$T->setVar('__zona_horaria_sel', ($usuario_cliente->zona_horaria_id==$id)?"selected":"");
			}
			else {
				$T->setVar('__zona_horaria_sel', ($usr->zona_horaria_id==$id)?"selected":"");
			}
			$T->parse('lista_zonas_horarias', 'LISTA_ZONAS_HORARIAS', true);
		}

		/* IDIOMAS DEL USUARIO CLIENTE */
/*		foreach (Constantes::getIdiomas() as $id => $nombre) {
			$T->setVar('__idioma_id', $id);
			$T->setVar('__idioma_nombre', $nombre);
			$T->setVar('__idioma_sel', ($usuario_cliente and $usuario_cliente->idioma_id==$id)?"selected":"");
			$T->parse('lista_idiomas', 'LISTA_IDIOMAS', true);
		}*/

		/* SUBCLIENTES DEL USUARIO CLIENTE */
		foreach ($usr->getSubClientes(REP_DATOS_CLIENTE) as $subcliente) {
			$T->setVar('__subcliente_id', $subcliente->subcliente_id);
			$T->setVar('__subcliente_nombre', $subcliente->nombre);
/*			if (!$usr->solo_lectura and $sactual->getPermisos(2)=='w' and !$subcliente->readonly and $puede_modificar) {
				$T->setVar('__subcliente_disabled', '');
				$T->setVar('__subcliente_color', '525252');
			}
			else {
				$T->setVar('__subcliente_disabled', 'disabled');
				$T->setVar('__subcliente_color', 'a2a2a2');
			}*/
			$T->setVar('__subcliente_sel',($subclientes and $subclientes[$subcliente->subcliente_id])?"checked":"");
			$T->parse('lista_subclientes', 'LISTA_SUBCLIENTES', true);
		}
		
		/* VERIFICAR SI ES LECTURA O ESCRITURA */
		if (!$usr->solo_lectura and $sactual->getPermisos(2)=='w' and $puede_modificar) {
			$T->parse('puede_modificar', 'PUEDE_MODIFICAR', false);
		}
		else {
			$T->setVar('__form_disabled', 'disabled');
		}
		
		$T->pparse('out', 'tpl_contenido');
		exit();
	}

	/* MUESTRA LA LISTA DE USUARIOS */
	else {

		$T->setFile('tpl_contenido', 'lista_usuarios.tpl');
//		$T->setBlock('tpl_contenido', 'LISTA_PERFILES', 'lista_perfiles');
		$T->setBlock('tpl_contenido', 'PUEDE_AGREGAR', 'puede_agregar');
		$T->setBlock('tpl_contenido', 'PUEDE_ELIMINAR', 'puede_eliminar');
		$T->setBlock('tpl_contenido', 'LISTA_USUARIOS', 'lista_usuarios');
		$T->setBlock('tpl_contenido', 'MOSTRAR_USUARIOS_DISPONIBLES', 'mostrar_usuarios_disponibles');

/*		foreach ($usr->getTiposPerfiles() as $id => $perfil) {
			$T->setVar('__perfil_nombre', $perfil["nombre"]);
			$T->setVar('__perfil_descripcion', $perfil["descripcion"]);
			$T->parse('lista_perfiles', 'LISTA_PERFILES', true);
		}*/

		/* LISTA DE USUARIOS CLIENTE */
		foreach ($usr->getUsuariosCliente() as $usuario_cliente) {
			$T->setVar('puede_eliminar', '');
			if (!$usr->solo_lectura and $sactual->getPermisos(3) == 'w' and $usuario_cliente->puedeEliminar()) {
				$T->setVar('__usuario_cliente_id', $usuario_cliente->usuario_id);
				$T->setVar('__usuario_cliente_nombre', $usuario_cliente->nombre);
				$T->parse('puede_eliminar', 'PUEDE_ELIMINAR', false);
			}
			if (!$usr->solo_lectura and $sactual->getPermisos(2) == 'w' and $usuario_cliente->puedeModificar()) {
				$T->setVar('__form_icon_detail', 'spriteButton spriteButton-editar');
				$T->setVar('__form_label_detail', 'Modificar Usuario');
			}
			else {
				$T->setVar('__form_icon_detail', 'spriteButton spriteButton-ver');
				$T->setVar('__form_label_detail', 'Informaci&oacute;n Usuario');
			}
			$T->setVar('__usuario_cliente_id', $usuario_cliente->usuario_id);
			$T->setVar('__usuario_cliente_nombre', htmlspecialchars($usuario_cliente->nombre));
			$T->setVar('__usuario_cliente_email', htmlspecialchars($usuario_cliente->email));
			$T->setVar('__usuario_cliente_perfil', $usuario_cliente->perfil_nombre);
			$T->parse('lista_usuarios', 'LISTA_USUARIOS', true);
		}
		
		/* VERIFICAR SI ES LECTURA O ESCRITURA */
		$cnt_restante = $usr->puedeAgregarUsuarios();
		if (!$usr->solo_lectura and $sactual->getPermisos(3) == 'w' and $cnt_restante) {
			$T->parse('puede_agregar', 'PUEDE_AGREGAR', false);
		}
		if ($cnt_restante < REP_MOSTRAR_DISPONIBLES_MINIMO) {
			$T->setVar('__usuarios_disponible', $cnt_restante);
			$T->parse('mostrar_usuarios_disponibles', 'MOSTRAR_USUARIOS_DISPONIBLES', false);
		}
	}
}

/* SI SE INGRESA AL MENU SUBCLIENTES */
elseif ($sactual->seccion_id == REP_SECCION_USUARIO_SUBCLIENTE and $sactual->getPermisos(3) != '-') {
	
	/* MUESTRA EL FORMULARIO PARA INGRESO Y MODIFICACION DE SUBCLIENTES */
	if ($accion == 'modificar_subcliente') {
		$T->setFile('tpl_contenido', 'form_subcliente.tpl');
		$T->setBlock('tpl_contenido', 'PUEDE_MODIFICAR', 'puede_modificar');

		/* SETEAR DE NUEVO LA SECCION PORQUE ES UN POPUP */
		$T->setVar('__accion_sitio_id', $sitio_id);
		$T->setVar('__accion_menu_id', $sactual->seccion_id);

		/* OBTENER LOS DATOS DE LA NOTIFICACION */
		if ($subcliente_id) {
			$subcliente = $usr->getSubCliente($subcliente_id);
			$T->setVar('__subcliente_id', $subcliente->subcliente_id);
			$T->setVar('__subcliente_nombre', $subcliente->nombre);
			$T->setVar('__subcliente_descripcion', $subcliente->descripcion);
		}
		else {
			$T->setVar('__subcliente_id', 0);
		}

		/* VERIFICAR SI ES LECTURA O ESCRITURA */
		if (!$usr->solo_lectura and $sactual->getPermisos(3) == 'w') {
			$T->parse('puede_modificar', 'PUEDE_MODIFICAR', false);
		}
		else {
			$T->setVar('__form_disabled', 'disabled');
		}
				
		$T->pparse('out', 'tpl_contenido');
		exit();
	}

	/* MUESTRA EL FORMULARIO PARA ASOCIAR USUARIOS A UN SUBCLIENTE */
	elseif ($accion == 'asociar_subcliente_usuarios') {
		$T->setFile('tpl_contenido', 'form_asociar_usuario.tpl');
		$T->setBlock('tpl_contenido', 'PUEDE_MODIFICAR', 'puede_modificar');
		$T->setBlock('tpl_contenido', 'USUARIOS_SUBCLIENTE', 'usuarios_subcliente');

		/* SETEAR DE NUEVO LA SECCION PORQUE ES UN POPUP */
		$T->setVar('__accion_sitio_id', $sitio_id);
		$T->setVar('__accion_menu_id', $sactual->seccion_id);

		/* DATOS DEL SUBCLIENTE */
		$subcliente = $usr->getSubCliente($subcliente_id);
		$usuarios_cliente = $subcliente->getUsuarios();
		$T->setVar('__subcliente_id', $subcliente->subcliente_id);

		/* LISTA DE USUARIOS QUE PUEDE SELECCIONAR EL SUBCLIENTE */
		foreach ($usr->getUsuariosCliente() as $id => $usuario_cliente) {
			$T->setVar('__usuario_cliente_id', $usuario_cliente->usuario_id);
			$T->setVar('__usuario_cliente_nombre', htmlspecialchars($usuario_cliente->nombre));
			$T->setVar('__usuario_cliente_email', htmlspecialchars($usuario_cliente->email));
			if ($usuarios_cliente and $usuarios_cliente[$id]) {
				$T->setVar('__usuario_cliente_sel', "checked");
//				$T->setVar('__usuario_cliente_disabled', (count($usuarios_cliente[$id]->getSubClientes(REP_DATOS_USUARIO))==1)?"onclick='return false;' readonly":"");
			}
			else {
				$T->setVar('__usuario_cliente_sel', "");
//				$T->setVar('__usuario_cliente_disabled', "");
			}
			$T->parse('usuarios_subcliente', 'USUARIOS_SUBCLIENTE', true);
		}
		
		/* VERIFICAR SI ES LECTURA O ESCRITURA */
		if (!$usr->solo_lectura and $sactual->getPermisos(3) == 'w') {
			$T->parse('puede_modificar', 'PUEDE_MODIFICAR', false);
		}
		else {
			$T->setVar('__form_disabled', 'disabled');
		}
		
		$T->pparse('out', 'tpl_contenido');
		exit();
	}

	/* MUESTRA EL FORMULARIO PARA ASOCIAR OBJETIVOS A UN SUBCLIENTE */
	elseif ($accion == 'asociar_subcliente_objetivos') {
		$T->setFile('tpl_contenido', 'form_asociar_objetivo.tpl');
		$T->setBlock('tpl_contenido', 'PUEDE_MODIFICAR', 'puede_modificar');
		$T->setBlock('tpl_contenido', 'OBJETIVOS_SUBCLIENTE', 'objetivos_subcliente');

		/* SETEAR DE NUEVO LA SECCION PORQUE ES UN POPUP */
		$T->setVar('__accion_sitio_id', $sitio_id);
		$T->setVar('__accion_menu_id', $sactual->seccion_id);
		
		/* DATOS DEL SUBCLIENTE */
		$subcliente = $usr->getSubCliente($subcliente_id);
		$objetivos = $subcliente->getObjetivos();
		$T->setVar('__subcliente_id', $subcliente->subcliente_id);

		/* LISTA DE OBJETIVOS QUE PUEDE SELECCIONAR EL SUBCLIENTE */
		foreach ($usr->getObjetivos(REP_DATOS_CLIENTE) as $objetivo) {
			$T->setVar('__objetivo_id', $objetivo->objetivo_id);
			$T->setVar('__objetivo_nombre', $objetivo->nombre);
			$T->setVar('__objetivo_servicio', $objetivo->getServicio()->nombre);
			$T->setVar('__objetivo_sel', ($objetivos and $objetivos[$objetivo->objetivo_id])?"checked":"");
			$T->setVar('__objetivo_nodos', (count($objetivo->getNodos()) > 0) ?count($objetivo->getNodos()) : '-');
			$T->parse('objetivos_subcliente', 'OBJETIVOS_SUBCLIENTE', true);
		}
		
		/* VERIFICAR SI ES LECTURA O ESCRITURA */
		if (!$usr->solo_lectura and $sactual->getPermisos(3) == 'w') {
			$T->parse('puede_modificar', 'PUEDE_MODIFICAR', false);
		}
		else {
			$T->setVar('__form_disabled', 'disabled');
		}

		$T->pparse('out', 'tpl_contenido');
		exit();
	}

	/* MUESTRA LISTA DE SUBCLIENTES */
	else {

		$T->setFile('tpl_contenido', 'lista_subclientes.tpl');
		$T->setBlock('tpl_contenido', 'PUEDE_AGREGAR', 'puede_agregar');
		$T->setBlock('tpl_contenido', 'PUEDE_ELIMINAR', 'puede_eliminar');
		$T->setBlock('tpl_contenido', 'LISTA_SUBCLIENTES', 'lista_subclientes');

		$subclientes = $usr->getSubClientes(REP_DATOS_CLIENTE);
		$cnt = count($subclientes);
		
		/* LISTA DE SUBCLIENTES */
		foreach ($subclientes as $subcliente) {
			$T->setVar('puede_eliminar', '');
			if (!$usr->solo_lectura and $sactual->getPermisos(3) == 'w' and $cnt > 1) {
				$T->setVar('__subcliente_id', $subcliente->subcliente_id);
				$T->setVar('__subcliente_nombre', $subcliente->nombre);
				$T->parse('puede_eliminar', 'PUEDE_ELIMINAR', false);
			}
			$T->setVar('__subcliente_id', $subcliente->subcliente_id);
			$T->setVar('__subcliente_nombre', htmlspecialchars($subcliente->nombre));
			$T->setVar('__subcliente_descripcion', htmlspecialchars($subcliente->descripcion));
			$T->setVar('__subcliente_usuarios', count($subcliente->getUsuarios()));
			$T->setVar('__subcliente_objetivos', count($subcliente->getObjetivos()));
			$T->parse('lista_subclientes', 'LISTA_SUBCLIENTES', true);
		}

		/* VERIFICAR SI ES LECTURA O ESCRITURA */
		if (!$usr->solo_lectura and $sactual->getPermisos(3) == 'w') {
			$T->parse('puede_agregar', 'PUEDE_AGREGAR', false);
			$T->setVar('__form_icon_detail', 'spriteButton spriteButton-editar');
			$T->setVar('__form_label_detail', 'Modificar Grupo');
		}
		else {
			$T->setVar('__form_icon_detail', 'spriteButton spriteButton-ver');
			$T->setVar('__form_label_detail', 'Informaci&oacute;n Grupo');
		}
	}
}

/* SI SE INGRESA A MI PERFIL */
elseif ($sactual->seccion_id == REP_SECCION_USUARIO_MIPERFIL and $sactual->getPermisos(1) != '-') {
	
	$T->setFile('tpl_contenido', 'mi_perfil.tpl');
	$T->setBlock('tpl_contenido', 'MOSTRAR_MENSAJE', 'mostrar_mensaje');
	$T->setBlock('tpl_contenido', 'PUEDE_MODIFICAR', 'puede_modificar');
	$T->setBlock('tpl_contenido', 'INTERVALOS_USUARIO', 'intervalos_usuario');
	$T->setBlock('tpl_contenido', 'ZONAS_HORARIAS_USUARIO', 'zonas_horarias_usuario');
//	$T->setBlock('tpl_contenido', 'IDIOMAS_USUARIO', 'idiomas_usuario');

	/* DATOS DEL USUARIO */
	$T->setVar('__usuario_cliente_id', $usr->usuario_id);
	$T->setVar('__usuario_nombre', $usr->nombre);
	$T->setVar('__usuario_email', $usr->email);
	$T->setVar('__usuario_telefono', $usr->telefono);
	$T->setVar('__usuario_cargo', $usr->cargo);
//	$T->setVar('__padre_id', $sactual->padre_id);
//	$T->setVar('__seccion_id', $sactual->seccion_id);
//	$T->setVar('__nivel', $sactual->nivel);
	$T->setVar('__orientacion_normal_sel', ($usr->orientacion_semaforo)?'':'selected');
	$T->setVar('__orientacion_invertida_sel', ($usr->orientacion_semaforo)?'selected':'');
	
	if (isset($accion) and $accion == "guardar_perfil") {
		$T->parse('mostrar_mensaje', 'MOSTRAR_MENSAJE', false);
	}
	
	/* INTERVALOS DEL SEMAFORO */
	foreach (Constantes::getIntervalos(REP_INTERVALO_SEMAFORO) as $id => $nombre) {
		$T->setVar('__intervalo_id', $id);
		$T->setVar('__intervalo_nombre', $nombre);
		$T->setVar('__intervalo_sel', ($usr and $usr->periodo_semaforo_id==$id)?"selected":"");
		$T->parse('intervalos_usuario', 'INTERVALOS_USUARIO', true);
	}
	
	/* ZONAS HORARIAS DEL USUARIO */
	foreach (Constantes::getZonasHorarias() as $id => $nombre) {
		$T->setVar('__zona_horaria_id', $id);
		$T->setVar('__zona_horaria_nombre', $nombre);
		$T->setVar('__zona_horaria_sel', ($usr and $usr->zona_horaria_id==$id)?"selected":"");
		$T->parse('zonas_horarias_usuario', 'ZONAS_HORARIAS_USUARIO', true);
	}
	
	/* IDIOMAS DEL USUARIO */
/*	foreach (Constantes::getIdiomas() as $id => $nombre) {
		$T->setVar('__idioma_id', $id);
		$T->setVar('__idioma_nombre', $nombre);
		$T->setVar('__idioma_sel', ($usr and $usr->idioma_id==$id)?"selected":"");
		$T->parse('idiomas_usuario', 'IDIOMAS_USUARIO', true);
	}*/

	/* VERIFICAR SI ES LECTURA O ESCRITURA */
	if (!$usr->solo_lectura and $sactual->getPermisos(1) == 'w') {
		$T->parse('puede_modificar', 'PUEDE_MODIFICAR', false);
	}
	else {
		$T->setVar('__form_disabled', 'disabled');
	}
}

elseif ($sactual->seccion_id == REP_SECCION_USUARIO_TOKENS and $sactual->getPermisos(3) != '-') {

	/* MUESTRA FORMULARIO PARA INGRESO Y MODIFICACION DE TOKEN DE WEBSERVICES */
	if ($accion == 'modificar_token') {
		$T->setFile('tpl_contenido', 'form_token_ws.tpl');
		$T->setBlock('tpl_contenido', 'PUEDE_MODIFICAR', 'puede_modificar');

		/* SETEAR DE NUEVO LA SECCION PORQUE ES UN POPUP */
		$T->setVar('__accion_sitio_id', $sitio_id);
		$T->setVar('__accion_menu_id', $sactual->seccion_id);

		/* OBTENER LOS DATOS DE LA NOTIFICACION */
		if ($token_id) {
			$token = $usr->getTokenWS($token_id);
			$T->setVar('__token_id', $token->token_id);
			$T->setVar('__token_nombre', $token->nombre);
			$T->setVar('__token_key', $token->key);
//			$T->setVar('__token_expiracion', $token->fecha_expiracion);
		}
		else {
			$T->setVar('__token_id', 0);
		}

		/* VERIFICAR SI ES LECTURA O ESCRITURA */
		if (!$usr->solo_lectura and $sactual->getPermisos(3) == 'w') {
			$T->parse('puede_modificar', 'PUEDE_MODIFICAR', false);
		}
		else {
			$T->setVar('__form_disabled', 'disabled');
		}
				
		$T->pparse('out', 'tpl_contenido');
		exit();
	}

	/* MUESTRA LISTA DE TOKEN DE WEBSERVICES */
	else {
		$T->setFile('tpl_contenido', 'lista_tokens_ws.tpl');
		$T->setBlock('tpl_contenido', 'PUEDE_AGREGAR', 'puede_agregar');
		$T->setBlock('tpl_contenido', 'PUEDE_ELIMINAR', 'puede_eliminar');
		$T->setBlock('tpl_contenido', 'LISTA_TOKENS', 'lista_tokens');
		
		foreach ($usr->getTokensWS() as $token) {
			$T->setVar('puede_eliminar', '');
			if (!$usr->solo_lectura and $sactual->getPermisos(3) == 'w') {
				$T->setVar('__token_id', $token->token_id);
				$T->setVar('__token_nombre', $token->nombre);
				$T->parse('puede_eliminar', 'PUEDE_ELIMINAR', false);
			}
			$T->setVar('__token_id', $token->token_id);
			$T->setVar('__token_nombre', $token->nombre);
			$T->setVar('__token_key', $token->key);
//			$T->setVar('__token_expiracion', $token->fecha_expiracion);
			$T->parse('lista_tokens', 'LISTA_TOKENS', true);
		}
		
		/* VERIFICAR SI ES LECTURA O ESCRITURA */
		if (!$usr->solo_lectura and $sactual->getPermisos(3) == 'w') {
			$T->parse('puede_agregar', 'PUEDE_AGREGAR', false);
			$T->setVar('__form_icon_detail', 'spriteButton spriteButton-editar');
			$T->setVar('__form_label_detail', 'Modificar Token');
		}
		else {
			$T->setVar('__form_icon_detail', 'spriteButton spriteButton-ver');
			$T->setVar('__form_label_detail', 'Informaci&oacute;n Token');
		}
	}	
}

/* MUESTRA MENSAJE POR DEFECTO */
else {
	$T->setFile('tpl_contenido', 'sorry_seccion.tpl');
}

$T->setVar('__sitio_contenido', $T->parse('out', 'tpl_contenido'));

?>