<?

class Servicio {

	/** 
	 * Atributos publicos.
	 */
	var $servicio_id;
	var $nombre;
	var $descripcion;
	var $notificacion_downtime_parcial;
	var $notificacion_downtime_grupal;
	var $notificacion_downtime_global;
	var $notificacion_uptime_parcial;
	var $notificacion_patron_inverso;
	var $notificacion_sla;

	/**
	 * Constructor.
	 *
	 * @param integer $servicio_id
	 * @return Servicio
	 */
	function Servicio($servicio_id) {
		$this->servicio_id = $servicio_id;
	}
	
	function __Servicio() {
		global $mdb2;
		global $log;

		$sql = "SELECT * FROM servicio WHERE servicio_id=".$this->servicio_id;
//		echo($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql,$res->userinfo);
			exit();
		}
		
		if ($row = $res->fetchRow()) {
			$this->notificacion_downtime_parcial = ($row["soporta_notificacion_parcial"]=='t')?true:false;
			$this->notificacion_downtime_grupal = ($row["soporta_notificacion_grupal"]=='t')?true:false;
			$this->notificacion_downtime_global = ($row["soporta_notificacion_global"]=='t')?true:false;
			$this->notificacion_uptime_parcial = ($row["soporta_notificacion_ok"]=='t')?true:false;
			$this->notificacion_patron_inverso = ($row["soporta_notificacion_patroninverso"]=='t')?true:false;
			$this->notificacion_sla = ($row["soporta_notificacion_sla"]=='t')?true:false;
		}
	}

	/**
	 * Funcion para obtener el nombre del tag
	 * correspondiente al tipo de configuracion.
	 *
	 * @return string
	 */
	function getTagConfigXML() {
		if (in_array($this->servicio_id, array(REP_PROT_DNS_SOA, REP_PROT_DNS_A, REP_PROT_DNS_MX, REP_PROT_DNS_CHAOS))) {
			return "atdns";
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_SMTP, REP_PROT_POP, REP_PROT_MAILTRAFFIC))) {
			return "atmail";
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_HTTP))) {
			return "atweb";
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_HTTP_FULL))) {
			return "atcontent";
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_ECOMMERCE))) {
			return "attransaction";
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_TRANSACTION))) {
			return "attransaction_plus";
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_ECOMMERCE_VIRTUAL))) {
			return "virtual";
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_BROWSER_HTTP, REP_PROT_BROWSER_HTTP_FULL, REP_PROT_BROWSER_ECOMMERCE, REP_PROT_BROWSER_ECOMMERCE_BANCO,REP_PROT_BROWSER_ECOMMERCE_CC, REP_PROT_BROWSER_TRANSACTION,REP_PROT_BROWSER_TRANSACTION_BANCO, REP_PROT_BROWSER_SCREENSHOT, REP_PROT_DIGIPASS))) {
			return "atbrowser";
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_WEBSERVICES))) {
			return "atwebservices";
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_USSD))) {
			return "atmobile_ussd";
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_IVR))) {
			return "ativr";
		}
		//se agrega el nuevo servicio atappmobile de prueba
		elseif (in_array($this->servicio_id, array(REP_PROT_MOBILE))) {
			return "atappmobile";
		}
                elseif (in_array($this->servicio_id, array(REP_PROT_NEW_RELIC))) {
			return REP_SETUP_NEW_RELIC;
		}
                elseif (in_array($this->servicio_id, array(REP_PROT_NEW_RELIC_RUM))) {
			return REP_SETUP_NEW_RELIC_RUM;
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_AUDEX,REP_PROT_ATDEX))) {
			return "audex";
		}
		 elseif (in_array($this->servicio_id, array(REP_PROT_NEW_RELIC_MOBILE))) {
			return REP_SETUP_NEW_RELIC_MOBILE;
		}
		else {
			return null;
		}
	}

	/*
	 * Funcion para obtener el nombre del tag
	 * correspondiente al tipo de setup.
	 *
	 * @return string
	 */
	function getTagSetupXML() {
		if ($this->servicio_id==REP_PROT_DNS_SOA) {
			return "soa";
		}
		elseif ($this->servicio_id==REP_PROT_DNS_A) {
			return "a";
		}
		elseif ($this->servicio_id==REP_PROT_DNS_MX) {
			return "mx";
		}
		elseif ($this->servicio_id==REP_PROT_DNS_CHAOS) {
			return "chaos";
		}
		elseif ($this->servicio_id==REP_PROT_SMTP) {
			return "smtp";
		}
		elseif ($this->servicio_id==REP_PROT_POP) {
			return "pop";
		}
		elseif ($this->servicio_id==REP_PROT_MAILTRAFFIC) {
			return "traffic";
		}
		else {
			return "setup_paso";
		}
	}

	/**
	 * Funcion para obtener el tipo de setup.
	 * Este un clasificacion de los servicios segun el formato del setup.
	 *
	 * @return integer
	 */
	function getTipoSetup() {
		if (in_array($this->servicio_id, array(REP_PROT_DNS_SOA, REP_PROT_DNS_A, REP_PROT_DNS_MX, REP_PROT_DNS_CHAOS))) {
			return REP_SETUP_DNS;
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_SMTP, REP_PROT_POP, REP_PROT_MAILTRAFFIC))) {
			return REP_SETUP_MAIL;
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_HTTP, REP_PROT_HTTP_FULL, REP_PROT_ECOMMERCE, REP_PROT_TRANSACTION, REP_PROT_ECOMMERCE_VIRTUAL, REP_PROT_WEBSERVICES))) {
			return REP_SETUP_WEB;
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_BROWSER_HTTP, REP_PROT_BROWSER_HTTP_FULL, REP_PROT_BROWSER_ECOMMERCE, REP_PROT_BROWSER_ECOMMERCE_BANCO,REP_PROT_BROWSER_ECOMMERCE_CC, REP_PROT_BROWSER_TRANSACTION,REP_PROT_BROWSER_TRANSACTION_BANCO, REP_PROT_BROWSER_SCREENSHOT, REP_PROT_DIGIPASS))) {
			return REP_SETUP_BROWSER;
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_USSD))) {
			return REP_SETUP_MOBILE;
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_IVR))) {
			return REP_SETUP_IVR;
		}
		elseif (in_array($this->servicio_id, array(REP_PROT_MOBILE))) {
			return REP_SETUP_MOBILE;
		}
                elseif (in_array($this->servicio_id, array(REP_PROT_NEW_RELIC))) {
                    return REP_SETUP_NEW_RELIC;
		}
                elseif (in_array($this->servicio_id, array(REP_PROT_NEW_RELIC_RUM))) {
                    return REP_SETUP_NEW_RELIC_RUM;
		}

		elseif (in_array($this->servicio_id, array(REP_PROT_AUDEX,REP_SETUP_ATDEX))) {
			return REP_SETUP_AUDEX;
		}
        elseif (in_array($this->servicio_id, array(REP_PROT_NEW_RELIC_MOBILE))) {
            return REP_SETUP_NEW_RELIC_MOBILE;
		}
		else {
			return null;
		}
	}
	
	function tieneNuevoConfig() {
		$prot_nuevo_config = array(REP_PROT_TRANSACTION, REP_PROT_BROWSER_TRANSACTION,REP_PROT_IVR, REP_PROT_MOBILE, REP_PROT_BROWSER_TRANSACTION_BANCO);
		if (in_array($this->servicio_id, $prot_nuevo_config)) {
			return true;
		}
		else {
			return false;
		}
	}
	
}

?>