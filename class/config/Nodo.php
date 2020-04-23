<?php

class Nodo {
	
	var $nodo_id;
	var $nombre;
	var $ubicacion;
	
	function Nodo($nodo_id, $nombre) {
		$this->nodo_id = $nodo_id;
		$this->nombre = $nombre;
	}
	
}

?>