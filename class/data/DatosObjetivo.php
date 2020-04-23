<?

class DatosObjetivo {
	
	var $objetivo_id;
	var $nombre;
	var $servicio;
	var $estado;
	var $sla_dis_ok;
	var $sla_dis_error;
	var $sla_ren_ok;
	var $sla_ren_error;

	var $__eventos;
	var $__secuencias;
//	var $__respuestas;
	
	function DatosObjetivo($objetivo_id, $nombre) {
		$this->objetivo_id = $objetivo_id;
		$this->nombre = $nombre;

		$this->__eventos = array();
		$this->__secuencias = array();
//		$this->__respuestas = array();
	}
	
}

?>