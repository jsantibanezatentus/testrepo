<?

class Grupo {
	
	/** 
	 * Atributos publicos.
	 */
	var $grupo_id;
	var $nombre;
	var $descripcion;
	
	var $__usuario_id; // Atributo de resguardo.
	
	/**
	 * Constructor.
	 *
	 * @param integer $grupo_id
	 * @return Grupo
	 */
	function Grupo($grupo_id) {
		$this->grupo_id = $grupo_id;
	}
	
	/**
	 * Funcion que agrega un grupo.
	 */
	function agregar() {
		global $mdb2;
		$sql = "SELECT * FROM public.cliente_usuario_grupo_agrega(".
				pg_escape_string($this->__usuario_id).",'".
				pg_escape_string($this->nombre)."','".
				pg_escape_string($this->descripcion)."')";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			die($sql);
		}
	}
	
	/**
	 * Funcion que modifica un grupo.
	 */
	function modificar() {
		global $mdb2;
		$sql = "SELECT * FROM public.cliente_usuario_grupo_modifica(".
				pg_escape_string($this->__usuario_id).",".
				pg_escape_string($this->grupo_id).", '".
				pg_escape_string($this->nombre)."','".
				pg_escape_string($this->descripcion)."')";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			die($sql);
		}
	}
	
	/**
	 * Funcion que elimina un grupo.
	 */
	function eliminar() {
		global $mdb2;
		$sql = "SELECT * FROM public.cliente_usuario_grupo_elimina(".
				pg_escape_string($this->__usuario_id).",".
				pg_escape_string($this->grupo_id).")";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			die($sql);
		}
	}

}

?>