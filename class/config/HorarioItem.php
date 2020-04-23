<?

class HorarioItem {
	
	/** 
	 * Atributos publicos.
	 */
	var $item_id;
	var $es_incluido;
	var $fecha_inicio;
	var $fecha_termino;
	var $hora_inicio;
	var $hora_termino;
	var $dia;
	var $mes;
	var $anno;
	var $dia_semana;
	var $descripcion;
	var $horario_link_nombre;
	
	var $__horario_id;
	
	/**
	 * Constructor.
	 * 
	 * @param integer $item_id
	 * @return HorarioItem
	 */
	function HorarioItem($item_id) {
		$this->item_id = $item_id;
	}

	/**
	 * Function para obtener el tipo de filtro del item.
	 * 
	 * @return integer
	 */
	function getTipoFiltro() {
		if ($this->fecha_inicio or $this->fecha_termino) {
			return 1;
		}
		elseif ($this->dia or $this->mes or $this->anno) {
			return 2;
		}
		else {
			return 3;
		}
	}
	
	/**
	 * Funcion que agrega un item.
	 */
	function agregar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		$sql = "SELECT * FROM public.horario_item_agrega(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->__horario_id).",".
				((!$this->es_incluido)?"'f'":"'t'").",".
				(($this->fecha_inicio=="")?"NULL":"'".pg_escape_string($this->fecha_inicio)."'").",".
				(($this->fecha_termino=="")?"NULL":"'".pg_escape_string($this->fecha_termino)."'").",".
				(($this->hora_inicio=="")?"NULL":"'".pg_escape_string($this->hora_inicio)."'").",".
				(($this->hora_termino=="")?"NULL":"'".pg_escape_string($this->hora_termino)."'").",".
				((!$this->dia)?"NULL":pg_escape_string($this->dia)).",".
				((!$this->mes)?"NULL":pg_escape_string($this->mes)).",".
				((!$this->anno)?"NULL":pg_escape_string($this->anno)).",".
				((!$this->dia_semana)?"NULL":pg_escape_string($this->dia_semana)).",".
				((!$this->descripcion)?"NULL":"'".pg_escape_string($this->descripcion)."'").")";
//		print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}
	
	/**
	 * Funcion que modifica un item.
	 */
	function modificar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		$sql = "SELECT * FROM public.horario_item_modifica(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->item_id).",".
				((!$this->es_incluido)?"'f'":"'t'").",".
				(($this->fecha_inicio=="")?"NULL":"'".pg_escape_string($this->fecha_inicio)."'").",".
				(($this->fecha_termino=="")?"NULL":"'".pg_escape_string($this->fecha_termino)."'").",".
				(($this->hora_inicio=="")?"NULL":"'".pg_escape_string($this->hora_inicio)."'").",".
				(($this->hora_termino=="")?"NULL":"'".pg_escape_string($this->hora_termino)."'").",".
				((!$this->dia)?"NULL":pg_escape_string($this->dia)).",".
				((!$this->mes)?"NULL":pg_escape_string($this->mes)).",".
				((!$this->anno)?"NULL":pg_escape_string($this->anno)).",".
				((!$this->dia_semana)?"NULL":pg_escape_string($this->dia_semana)).")";
//		print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}
	
	/**
	 * Funcion que elimina un item.
	 */
	function eliminar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		
		$sql = "SELECT * FROM public.horario_item_elimina(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->item_id).")";
//		print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql,$res->userinfo);
			exit();
		}
	}
	
	/**
	 * Funcion que indica si este HorarioItem puede ser modificado.
	 * 
	 * @return boolean 
	 */
	function puedeModificar() {
		if ($this->horario_link_nombre) {
			return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * Function que imprime el item segun sus parametros.
	 * 
	 * @return string
	 */
	function toString() {
		
		global $dias_semana;
		global $meses_anno;
		
		$string = "";
		
		if ($this->fecha_inicio and $this->fecha_termino) {
			$string = "Desde el ".date('d-m-Y',strtotime($this->fecha_inicio)).
					 " hasta ".date('d-m-Y',strtotime($this->fecha_termino))." ";
		}
		elseif ($this->dia or $this->mes or $this->anno) {
			$string =(( $this->dia)?"El dia ".$this->dia." ":"Todos los dias ").
					(( $this->mes)?"de ".$meses_anno[$this->mes]." ":"para todos los meses ").
					(( $this->anno)?"del año ".$this->anno:" ");
		}
		elseif ($this->dia_semana) {
			$string = "Todos los dias ".$dias_semana[$this->dia_semana]." ";
		}
		elseif ($this->horario_link_nombre) {
			$string = "Items Linkeados desde: ".$this->horario_link_nombre." ";
		}
		else {
			$string = "Todos los dias ";
		}
		
		if ($this->hora_inicio and $this->hora_termino) {
			$string.= "Desde las ".$this->hora_inicio." hasta las ".$this->hora_termino;
		}
		
		return $string;
	}
	
}

?>