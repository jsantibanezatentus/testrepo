<?

class DatosRegistro {
	
	var $fecha;
	var $duracion;
	var $servidor;
	var $dns_primario; 
	var $email;
	var $serial;
	var $refresh;
	var $retry;
	var $expire;
	
	var $minimum;	
	var $__evento;
	var $__nombres;
	var $__tipos;
	var $__prioridades;
	var $__respuestas;
	
	function DatosRegistro($evento) {
		$this->__evento = $evento;
		
		$this->__nombres = array();
		$this->__tipos = array();
		$this->__prioridades = array();
		$this->__respuestas = array();
	}
	
}

?>
