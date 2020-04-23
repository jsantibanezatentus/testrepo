<?

class MailSetup {

	/** 
	 * Atributos publicos.
	 */
	var $dominio;
	var $dominio_tipo;
	var $dominio_timeout;
	var $destinatario;
	var $remitente;
	var $usuario;
	var $clave;
	var $metodo;
	
	
	/**
	 * Constructor.
	 *
	 * @param integer $objetivo_id
	 * @return MailService
	 */
	function MailSetup($monitor_id) {
		if ($monitor_id == "" or $monitor_id == null) {
			$monitor_id = 0;
		}
		$this->monitor_id = $monitor_id;
	}
	
}

?>