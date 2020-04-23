<?

class DatosEvento {
	
	var $evento_id;
	var $nombre;
	var $icono;
	var $color;

	var $porcentaje;
	var $duracion;
	var $cantidad;

	function DatosEvento($evento_id, $nombre) {
		$this->evento_id = $evento_id;
		$this->nombre = $nombre;
	}
	
}

?>