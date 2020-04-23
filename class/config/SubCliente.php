<?

class SubCliente {
	
	/** 
	 * Atributos publicos.
	 */
	var $subcliente_id;
	var $nombre;
	var $descripcion;

	/**
	 * Atributos privados.
	 */
	var $__usuarios;
	var $__objetivos;
	
	/**
	 * Constructor.
	 *
	 * @param integer $subcliente_id
	 * @return SubCliente
	 */
	function SubCliente($subcliente_id) {
		$this->subcliente_id = $subcliente_id;
	}

	/**
	 * Funcion para obtener los usuarios asociados al subcliente.
	 *
	 * @return array<Usuario>
	 */
	function getUsuarios($usuario_cliente_id = 0) {
		global $mdb2;
		global $log;
		global $current_usuario_id;
			
		$sql = "SELECT * FROM public.cliente_subcliente_lista_usuarios(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->subcliente_id).") ";
		
		if ($usuario_cliente_id) {
			$sql.= "WHERE cliente_usuario_id=".pg_escape_string($usuario_cliente_id);
		}
		
//		echo($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$this->__usuarios = array();
		while ($row = $res->fetchRow()) {
			$usuario_cliente = new Usuario($row["cliente_usuario_id"]);
			$usuario_cliente->nombre = $row["nombre"];
			$usuario_cliente->email = $row["email"];
			$this->__usuarios[$row["cliente_usuario_id"]] = $usuario_cliente;
		}
		return $this->__usuarios;
	}
	
	function getUsuario($usuario_cliente_id) {
		if (!isset($this->__usuarios[$usuario_cliente_id])) {
			$this->getUsuarios($usuario_cliente_id);
		}
		if (isset($this->__usuarios[$usuario_cliente_id])) {
			return $this->__usuarios[$usuario_cliente_id];
		}
		else {
			return null;
		}
	}
	
	/**
	 * Funcion para obtener los objetivos asociados al subcliente.
	 *
	 * @return Objetivo
	 */
	function getObjetivos() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
			
		$sql = "SELECT * FROM public.cliente_subcliente_lista_objetivos(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->subcliente_id).")";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$this->__objetivos = array();
		while ($row = $res->fetchRow()) {
			$objetivo = new Objetivo($row["objetivo_id"]);
			$objetivo->nombre = $row["objetivo_nombre"];
			$objetivo->descripcion = $row["objetivo_descripcion"];
			$this->__objetivos[$row["objetivo_id"]] = $objetivo;
		}
		return $this->__objetivos;
	}

	/**
	 * Funcion que agrega un subcliente.
	 */
	function agregar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		
		Validador::campoVacio($this->nombre, "Nombre");
		Validador::existeNombreSubcliente(true, $this->nombre);
		
		$sql = "SELECT * FROM public.cliente_subcliente_agrega(".
				pg_escape_string($current_usuario_id).", TRIM('".
				pg_escape_string($this->nombre)."'), TRIM('".
				pg_escape_string($this->descripcion)."'))";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$log->setChange("AGREGO SUBCLIENTE", $this->toString());
		
		if ($row = $res->fetchRow()) {
			$this->subcliente_id = $row["cliente_subcliente_agrega"];
		}
	}
	
	/**
	 * Funcion que modifica un subcliente.
	 */
	function modificar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		
		Validador::campoVacio($this->nombre, "Nombre");
		Validador::existeNombreSubcliente(true, $this->nombre, $this->subcliente_id);
		
		$sql = "SELECT * FROM public.cliente_subcliente_modifica(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->subcliente_id).", TRIM('".
				pg_escape_string($this->nombre)."'), TRIM('".
				pg_escape_string($this->descripcion)."'))";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$log->setChange("MODIFICO SUBCLIENTE", $this->toString());
	}
	
	/**
	 * Funcion que elimina un subcliente.
	 */
	function eliminar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		
		$sql = "SELECT * FROM public.cliente_subcliente_elimina(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->subcliente_id).")";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$log->setChange("ELIMINO SUBCLIENTE", $this->toString());
	}
	
	/**
	 * Funcion para asociar un usuario al subcliente.
	 * 
	 * @param integer $usuario_id
	 */
	function asociarUsuario($usuario_cliente_id) {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		
		$sql = "SELECT * FROM public.cliente_subcliente_asocia_usuario(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->subcliente_id).",".
				pg_escape_string($usuario_cliente_id).")";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}

	/**
	 * Funcion para desasociar un usuario al subcliente.
	 * 
	 * @param integer $usuario_id
	 */
	function desasociarUsuario($usuario_cliente_id) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

/*		$sql = "SELECT * FROM public.cliente_mapa_subcliente_usuario ".
			   "WHERE cliente_usuario_id=".pg_escape_string($usuario_cliente_id)." ".
			   "AND cliente_subcliente_id <> ".pg_escape_string($this->subcliente_id);
//		print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($res->numRows() == 0 and $this->getUsuario($usuario_cliente_id) != null) {
			throw new Exception('No puede desasociar un usuario con un solo subcliente.');
		}*/

		$sql = "SELECT * FROM public.cliente_subcliente_desasocia_usuario(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->subcliente_id).",".
				pg_escape_string($usuario_cliente_id).")";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}
	
	/**
	 * Funcion para asociar un objetivo al subcliente.
	 * 
	 * @param integer $objetivo_id
	 */
	function asociarObjetivo($objetivo_id) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		$sql = "SELECT * FROM public.cliente_subcliente_asocia_objetivo(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->subcliente_id).",".
				pg_escape_string($objetivo_id).")";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}

	/**
	 * Funcion para desasociar un objetivo al subcliente.
	 * 
	 * @param integer $objetivo_id
	 */
	function desasociarObjetivo($objetivo_id) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		$sql = "SELECT * FROM public.cliente_subcliente_desasocia_objetivo(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->subcliente_id).",".
				pg_escape_string($objetivo_id).")";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}

/*	function puedeEliminar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		$sql = "SELECT count(*) as cnt_usuarios FROM public.cliente_subcliente_lista_usuarios(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->subcliente_id).")";
//		echo($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow() and $row["cnt_usuarios"]>0) {
			return false;
		}
		else {
			return true;
		}
	}*/

	/**
	 * 
	 * Funcion que es utilizada para mostrar en un string 
	 * los datos mas importantes del subcliente.
	 * Uso exclusivo para Log.
	 * 
	 * @return string
	 */
	function toString() {
		$string = "NOMBRE ".$this->nombre.", ".
				  "DESCRIPCION ".$this->descripcion;
		return $string;
	}

}

?>