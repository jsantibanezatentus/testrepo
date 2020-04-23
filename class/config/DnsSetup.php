<?

class DnsSetup {
	
	/** 
	 * Atributos publicos.
	 */
	var $monitor_id;
	var $dominio;
	var $resolver;
	var $patron;
	var $consulta;
	var $tipo;
	
	/**
	 * Constructor.
	 *
	 * @param integer $objetivo_id
	 * @return DnsService
	 */
	function DnsSetup($monitor_id) {
		if ($monitor_id == "" or $monitor_id == null) {
			$monitor_id = 0;
		}
		$this->monitor_id = $monitor_id;
	}

}

?>