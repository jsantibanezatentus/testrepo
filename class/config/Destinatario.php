<?

class Destinatario {

    /** 
	 * Atributos publicos.
	 */
	var $destinatario_id;
	var $nombre;
	var $contacto;
	var $tipo_id;
	var $tipo_nombre;
	var $usuario_cliente_id;
	var $telefono;

	/**
	 * Constructor.
	 *
	 * @param integer $destinatario_id
	 * @return Destinatario
	 */
	function Destinatario($destinatario_id) {
		$this->destinatario_id = $destinatario_id;
	}

	/**
	 * Funcion que agrega un destinatario.
	 */
	function agregar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		Validador::campoVacio($this->nombre, "Nombre");
		Validador::campoVacio($this->contacto, "Contacto");
		Validador::existeNombreDestinatario(true, $this->nombre);

		$sql = "SELECT * FROM public.notificacion_destinatario_agrega(".
				pg_escape_string($current_usuario_id).",'".
				pg_escape_string($this->nombre)."','".
				pg_escape_string($this->contacto)."',".
				pg_escape_string($this->tipo_id).",'".
				pg_escape_string($this->telefono)."')";
		//print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$log->setChange("AGREGO DESTINATARIO", $this->toString());

		if ($row = $res->fetchRow()) {
			$this->destinatario_id = $row["notificacion_destinatario_agrega"];
		}
	}

	/**
	 * Funcion que modifica un destinatario.
	 */
	function modificar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		Validador::campoVacio($this->nombre, "Nombre");
		Validador::campoVacio($this->contacto, "Contacto");
		Validador::existeNombreDestinatario(true, $this->nombre, $this->destinatario_id);

		$sql = "SELECT * FROM public.notificacion_destinatario_modifica(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->destinatario_id).", '".
				pg_escape_string($this->nombre)."', '".
				pg_escape_string($this->contacto)."',".
				pg_escape_string($this->tipo_id).", '".
				pg_escape_string($this->telefono)."')";
		//print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$log->setChange("MODIFICO DESTINATARIO", $this->toString());
	}

	/**
	 * Funcion que elimina un destinatario.
	 */
	function eliminar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		$sql = "SELECT * FROM public.notificacion_destinatario_elimina(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->destinatario_id).")";
		//print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$log->setChange("ELIMINO DESTINATARIO", $this->toString());
	}

	/**
	 * Funcion que indica si este destinatario puede ser eliminado,
	 * esto cuando no se esta usando en ninguna alerta.
	 *
	 * @return boolean
	 */
	function puedeEliminar() {
		global $mdb2;
		global $log;

		$sql = "SELECT notificacion_destinatario_id ".
			   "FROM public.notificacion ".
			   "WHERE notificacion_destinatario_id=".
				pg_escape_string($this->destinatario_id);
		//print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($res->numRows() > 0) {
			return false;
		}
		else {
			return true;
		}
	}

	function toString() {
		$string = "NOMBRE ".$this->nombre.", ".
				  "CONTACTO ".$this->contacto.", ".
				  "TIPO ".$this->tipo_nombre.",".
					"TELEFONO ".$this->telefono;
		return $string;
	}
}

?>