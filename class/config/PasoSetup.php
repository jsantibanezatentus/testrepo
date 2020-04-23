<?

class PasoSetup {

	/** 
	 * Atributos publicos.
	 */
	var $monitor_id;
	var $url;
	var $comando;
	var $metodo;
	var $timeout;


	/**
	 * Constructor.
	 *
	 * @return Paso
	 */
	function PasoSetup($monitor_id) {
		if ($monitor_id == "" or $monitor_id == null) {
			$monitor_id = 0;
		}
		$this->monitor_id = $monitor_id;
	}
	
}

?>