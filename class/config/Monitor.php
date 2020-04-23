<?

class Monitor {
	
	/** 
	 * Atributos publicos.
	 */
	var $monitor_id;
	var $nombre;
	var $descripcion;
	var $nodo_id;
	var $pais_id;
	var $pais_nombre;
	var $host;
	
	/**
	 * Constructor.
	 *
	 * @param integer $monitor_id
	 * @return Monitor
	 */
	function Monitor($monitor_id) {
		$this->monitor_id = $monitor_id;
	}

}

?>
