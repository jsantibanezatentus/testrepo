<?

class Horario {
	
	/** 
	 * Atributos publicos.
	 */
	var $horario_id;
	var $nombre;
	var $descripcion;
	var $tipo_id;
	var $tipo_nombre;
	var $usuario_cliente_id;
	
	/**
	 * Atributos privados.
	 */
	var $__items;
	
	/**
	 * Constructor.
	 * @param integer $horario_id
	 * @return Horario
	 */
	function Horario($horario_id) {
		$this->horario_id = $horario_id;
	}

	/**
	 * Funcion para obtener los items del horario.
	 * 
	 * @return array<HorarioItem>
	 */
	function getHorarioItems($item_id = 0) {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
/*		$sql = "SELECT * FROM horario_item_lista_detalle(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->horario_id).") ";

		if ($item_id>0) {
			$sql.= "WHERE horario_item_id=".pg_escape_string($item_id)." ";
		}
		$sql.= "ORDER BY horario_item_fecha_inicio DESC, horario_item_hora_inicio DESC";
		

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		
		$this->__items = array();
		while ($row = $res->fetchRow()) {
			$item = new HorarioItem($row["horario_item_id"]);
			$item->es_incluido =($row["horario_item_es_incluido"]=="t")?1:0;
			$item->fecha_inicio = $row["horario_item_fecha_inicio"];
			$item->fecha_termino = $row["horario_item_fecha_termino"];
			$item->hora_inicio = $row["horario_item_hora_inicio"];
			$item->hora_termino = $row["horario_item_hora_termino"];
			$item->dia = $row["horario_item_dia"];
			$item->mes = $row["horario_item_mes"];
			$item->anno = $row["horario_item_anno"];
			$item->dia_semana = $row["horario_item_dia_semana"];
			$item->horario_link_nombre = $row["horario_item_link_horario_nombre"];
			$this->__items[$row["horario_item_id"]] = $item;
		}*/

		$sql = "SELECT * FROM public.horario_item ".
		 		"WHERE horario_id = ".pg_escape_string($this->horario_id)." ";
		
		if ($item_id>0) {
			$sql.= "AND horario_item_id=".pg_escape_string($item_id)." ";
		}
		$sql.= "ORDER BY fecha_inicio DESC, hora_inicio DESC";
		
		
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		
		$this->__items = array();
		while ($row = $res->fetchRow()) {
			$item = new HorarioItem($row["horario_item_id"]);
			$item->es_incluido =($row["es_incluido"]=="t")?1:0;
			$item->fecha_inicio = $row["fecha_inicio"];
			$item->fecha_termino = $row["fecha_termino"];
			$item->hora_inicio = $row["hora_inicio"];
			$item->hora_termino = $row["hora_termino"];
			$item->dia = $row["dia"];
			$item->mes = $row["mes"];
			$item->anno = $row["anno"];
			$item->dia_semana = $row["dia_semana"];
//			$item->horario_link_nombre = $row["link_horario_nombre"];
			$item->descripcion = $row["descripcion"];
			$this->__items[$row["horario_item_id"]] = $item;
		}
		
		return $this->__items;
	}
	
	function getHorarioItem($item_id) {
		if (!isset($this->__items[$item_id])) {
			$this->getHorarioItems($item_id);
		}
		if (isset($this->__items[$item_id])) {
			return $this->__items[$item_id];
		}
		else {
			return null;
		}
			
	}

	function getDiaSemanaItems($dia_id) {
		global $mdb2;
		global $log;
		
		$sql = "SELECT * FROM horario_item ".
			   "WHERE horario_id = ".pg_escape_string($this->horario_id)." ".
			   "AND dia_semana = ".pg_escape_string($dia_id);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		
		$items = array();
		while ($row = $res->fetchRow()) {
			$item = new HorarioItem($row["horario_item_id"]);
			$item->es_incluido =($row["horario_item_es_incluido"]=="t")?true:false;
			$item->dia_semana = $row["dia_semana"];
			$item->hora_inicio = $row["hora_inicio"];
			$item->hora_termino = $row["hora_termino"];
			$items[$row["horario_item_id"]] = $item;
		}
		return $items;
	}
	
	/**
	 * Funcion que agrega un horario.
	 */
	function agregar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		
		Validador::campoVacio($this->nombre, "Nombre");
		Validador::existeNombreHorario(true, $this->nombre, $this->tipo_id);
		
		$sql = "SELECT * FROM public.horario_agrega(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->tipo_id).",'".
				pg_escape_string($this->nombre)."','".
				pg_escape_string($this->descripcion)."')";
//		print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$log->setChange("AGREGO HORARIO", $this->toString());
		
		if ($row = $res->fetchRow()) {
			$this->horario_id = $row["horario_agrega"];
		} 
	}
	
	/**
	 * Funcion que modifica un horario.
	 */
	function modificar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		
		Validador::campoVacio($this->nombre, "Nombre");
		Validador::existeNombreHorario(true, $this->nombre, $this->tipo_id, $this->horario_id);
		
		$sql = "SELECT * FROM public.horario_modifica(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->horario_id).",'".
				pg_escape_string($this->nombre)."','".
				pg_escape_string($this->descripcion)."')";
		//print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$log->setChange("MODIFICO HORARIO", $this->toString());
	}
	
	/**
	 * Funcion que elimina un horario.
	 */
	function eliminar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		$sql = "SELECT * FROM public.horario_elimina(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->horario_id).")";
		//print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$log->setChange("ELIMINO HORARIO", $this->toString());
	}

	function eliminarDiaSemanaItems() {
		global $mdb2;
		global $log;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		$sql = "DELETE FROM horario_item ".
			   "WHERE horario_id = ".pg_escape_string($this->horario_id)." ".
			   "AND dia_semana IS NOT NULL";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}
	
	/**
	 * Funcion que copia todos los items de un horario origen a este.
	 * 
	 * @param integer $horario_asignable_id
	 */
	function copiar($horario_asignable_id) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		
		$sql = "SELECT * FROM public.horario_copia(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($horario_asignable_id).",".
				pg_escape_string($this->horario_id).")";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}

	/**
	 * Funcion que linkea todos los items de un horario origen a este.
	 * 
	 * @param integer $horario_asignable_id
	 */
	function linkear($horario_asignable_id) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		$sql = "SELECT * FROM public.horario_link(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($horario_asignable_id).",".
				pg_escape_string($this->horario_id).")";
		//print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}
	
	/**
	 * Funcion que indica si este horario puede ser eliminado.
	 * 
	 * @return boolean
	 */
	function puedeEliminar() {
		global $mdb2;
		global $log;

		$sql = "SELECT horario_id FROM public.notificacion ".
			   "WHERE horario_id=".pg_escape_string($this->horario_id);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($res->numRows() > 0) {
			return false;
		}
		
		$sql = "SELECT link_horario_id FROM public.horario_item ".
			   "WHERE link_horario_id=".pg_escape_string($this->horario_id);
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
				  "DESCRIPCION ".$this->descripcion.", ".
				  "TIPO ".$this->tipo_nombre;
		return $string;
	}

}

?>