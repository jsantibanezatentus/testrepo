<?

class Paso {

	/** 
	 * Atributos publicos.
	 */
	var $paso_id;
	var $nombre;
	var $proxy;
	var $cookies;
	var $headers;
	var $url_excluidas;
	var $screenshot;


	/**
	 * Atributos privados.
	 */
	var $__setups;
	var $__patrones;
	var $__dtmf;
	var $__numero_llamada;
	var $__audio;
	var $__logs;

	/**
	 * Constructor.
	 *
	 * @return Paso
	 */
	function Paso() {
		$this->__setups = array();
		$this->__patrones = array();
	}
	
	function addPatron($patron, $monitor_id) {
		if ($monitor_id == "" or $monitor_id == null) {
			$monitor_id = 0;
		}
		$this->__patrones[$monitor_id][$patron->orden] = $patron;
	}
	
}

?>