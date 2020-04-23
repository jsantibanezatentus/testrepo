<?

class DatosSecuencia {
	
	var $orden;
	var $cantidad;
	var $fecha_inicio;
	var $fecha_termino;
	var $respuesta;
	var $downtime;
	var $accesos;
	
	var $__eventos;
	
	function DatosSecuencia($orden, $cantidad) {
		$this->orden = $orden;
		$this->cantidad = $cantidad;
		
		$this->__eventos = array();
	}
	
	function getFechaInicio($format="Y-m-d H:i:s") {
		return date($format, strtotime($this->fecha_inicio));
	}
	
	function getFechaTermino($format="Y-m-d H:i:s") {
		return date($format, strtotime($this->fecha_termino));
	}
	
	
}

?>