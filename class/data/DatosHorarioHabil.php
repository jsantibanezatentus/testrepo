<?

class DatosHorarioHabil {
	
	var $fecha_inicio;
	var $fecha_termino;
	
	function DatosHorarioHabil($fecha_inicio, $fecha_termino) {
		$this->fecha_inicio = $fecha_inicio;
		$this->fecha_termino = $fecha_termino;
	}
	
	function getFechaInicio($format="Y-m-d H:i:s") {
		return date($format, strtotime($this->fecha_inicio));
	}
	
	function getFechaTermino($format="Y-m-d H:i:s") {
		return date($format, strtotime($this->fecha_termino));
	}
	
}

?>