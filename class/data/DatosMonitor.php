<?

class DatosMonitor {
	
	var $monitor_id;
	var $nombre;
	var $nombre_corto;
	var $ubicacion;

	var $total_tamano;
	var $total_monitoreos;
	
	var $__eventos;
	var $__elementos;
	var $__registros;
	
	var $__respuestas;
	
	function DatosMonitor($monitor_id, $nombre) {
		$this->monitor_id = $monitor_id;
		$this->nombre = $nombre;

		$this->__eventos = array();
		$this->__elementos = array();
		$this->__registros = array();

		/* ARREGLOS SIMPLES CON DATOS(SIN OBJETOS DENTRO) */
		$this->__respuestas = array();	
	}
	
}

?>