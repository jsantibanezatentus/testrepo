<?

abstract class Validador {
	
	function existeEmailUsuario($warning, $email, $usuario_cliente_id = 0) {
		global $mdb2;
		global $log;

		$sql = "SELECT * FROM public.cliente_usuario ".
			   "WHERE LOWER(TRIM(email))=LOWER(TRIM('".pg_escape_string($email)."')) ";

		if ($usuario_cliente_id != 0) {
			$sql.= "AND cliente_usuario_id <> ".pg_escape_string($usuario_cliente_id);
		}

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		
		if ($warning) {
			if ($res->numRows() > 0) {
				throw new Exception('Se intento ingresar/modificar un email que ya existe y que pertenece a otro usuario.');
			}
		}
		else {
			if ($res->numRows() > 0) {
				return true;
			}
			else {
				return false;
			}
		}
	}
	
	function existeNombreUsuario($warning, $nombre, $usuario_cliente_id = 0) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$sql = "SELECT * FROM public.cliente_usuario ".
			   "WHERE LOWER(TRIM(nombre))=LOWER(TRIM('".pg_escape_string($nombre)."')) ".
			   "AND cliente_id=(SELECT cliente_id FROM cliente_usuario WHERE cliente_usuario_id=".pg_escape_string($current_usuario_id).") ";

		if ($usuario_cliente_id > 0) {
			$sql.= "AND cliente_usuario_id <> ".pg_escape_string($usuario_cliente_id);
		}
		
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		
		if ($warning) {
			if ($res->numRows() > 0) {
				throw new Exception('Se intento ingresar/modificar un nombre de usuario que ya existe para este cliente.');
			}
		}
		else {
			if ($res->numRows() > 0) {
				return true;
			}
			else {
				return false;
			}
		}
	}
	
	function existeNombreSubcliente($warning, $nombre, $subcliente_id = 0) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$sql = "SELECT * FROM public.cliente_subcliente ".
			   "WHERE LOWER(TRIM(nombre))=LOWER(TRIM('".pg_escape_string($nombre)."')) ".
			   "AND cliente_id=(SELECT cliente_id FROM cliente_usuario WHERE cliente_usuario_id=".pg_escape_string($current_usuario_id).") ";

		if ($subcliente_id > 0) {
			$sql.= "AND cliente_subcliente_id <> ".pg_escape_string($subcliente_id);
		}
		
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		
		if ($warning) {
			if ($res->numRows() > 0) {
				throw new Exception('Se intento ingresar/modificar un nombre de subcliente que ya existe para este cliente.');
			}
		}
		else {
			if ($res->numRows() > 0) {
				return true;
			}
			else {
				return false;
			}
		}
	}
	
	function existeNombreObjetivo($warning, $nombre, $objetivo_id) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$sql = "SELECT * FROM public.objetivo o, public.cliente_mapa_cliente_objetivo cmco, public.cliente_usuario cu ".
			   "WHERE cu.cliente_id=cmco.cliente_id ".
			   "AND o.objetivo_id=cmco.objetivo_id ".
			   "AND cu.cliente_usuario_id=".pg_escape_string($current_usuario_id)." ".
			   "AND LOWER(TRIM(o.nombre))=LOWER(TRIM('".pg_escape_string($nombre)."')) ".
			   "AND o.objetivo_id <> ".pg_escape_string($objetivo_id);
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		
		if ($warning) {
			if ($res->numRows() > 0) {
				throw new Exception('Se intento modificar un nombre de objetivo que ya existe para este cliente.');
			}
		}
		else {
			if ($res->numRows() > 0) {
				return true;
			}
			else {
				return false;
			}
		}
	}
	
	function existeNombreDestinatario($warning, $nombre, $destinatario_id = 0) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$sql = "SELECT * FROM public.notificacion_destinatario ".
			   "WHERE LOWER(TRIM(nombre))=LOWER(TRIM('".pg_escape_string($nombre)."')) ".
			   "AND cliente_id=(SELECT cliente_id FROM cliente_usuario WHERE cliente_usuario_id=".pg_escape_string($current_usuario_id).") ";

		if ($destinatario_id > 0) {
			$sql.= "AND notificacion_destinatario_id <> ".pg_escape_string($destinatario_id);
		}
		
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		
		if ($warning) {
			if ($res->numRows() > 0) {
				throw new Exception('Se intento ingresar/modificar un nombre de destinatario que ya existe para este cliente.');
			}
		}
		else {
			if ($res->numRows() > 0) {
				return true;
			}
			else {
				return false;
			}
		}
	}

	function existeNombreHorario($warning, $nombre, $tipo, $horario_id = 0) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$sql = "SELECT * FROM public.horario ".
			   "WHERE LOWER(TRIM(nombre))=LOWER(TRIM('".pg_escape_string($nombre)."')) AND horario_tipo_id=".pg_escape_string($tipo)." ".
			   "AND cliente_id=(SELECT cliente_id FROM cliente_usuario WHERE cliente_usuario_id=".pg_escape_string($current_usuario_id).") ";

		if ($horario_id > 0) {
			$sql.= "AND horario_id <> ".pg_escape_string($horario_id);
		}
		
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		
		if ($warning) {
			if ($res->numRows() > 0) {
				throw new Exception('Se intento ingresar/modificar un nombre de horario que ya existe para este cliente.');
			}
		}
		else {
			if ($res->numRows() > 0) {
				return true;
			}
			else {
				return false;
			}
		}
	}

	function campoVacio($campo, $nombre) {
		$campo = trim($campo);
		if ($campo == null or $campo == "") {
			throw new Exception('Se intento ingresar el campo '.$nombre.' en blanco.');
		}
	}
	
	function campoNumerico($campo, $nombre, $minimo = null, $maximo = null) {
		if (!is_numeric($campo)) {
			throw new Exception('Se intento ingresar al campo '.$nombre.' un valor no numerico.');
		}
		if ($minimo != null and $maximo != null) {
			if ($campo < $minimo or $campo > $maximo) {
				throw new Exception('Se intento ingresar al campo '.$nombre.' un valor que no esta en el rango permitido.');
			}
		}
	}
	
	function campoEmail($campo, $nombre) {
		if (preg_match("/\@.*\@|[\s|\!|\"|\#|\$|\%|\&|\/|\(|\)|\=|\?|\¿|\;|\:]/", $campo) or
			!preg_match("/^.+\@.+$/", $campo)) {
			throw new Exception('Se intento ingresar al campo '.$nombre.' un valor que no es un e-mail valido.');
		}
	}

}

?>