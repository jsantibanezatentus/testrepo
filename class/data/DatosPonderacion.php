<?

class DatosPonderacion {
	
	var $ponderacion_item_id;
	var $hora_inicio;
	var $hora_termino;
	var $valor;

	var $__pasos;
	
	function DatosPonderacion($ponderacion_item_id) {
		$this->ponderacion_item_id = $ponderacion_item_id;
		
		$this->__pasos = array();
	}
}

?>