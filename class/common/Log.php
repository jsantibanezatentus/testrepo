<?

class Log {
	
	var $usuario_id;
	var $sesion_id;
//	var $fecha;
	var $accion;
	var $url;
	var $ip;

	var $login_admin;
	
	function Log($usuario_id=null, $sesion_id=null) {
		$this->usuario_id = $usuario_id;
		$this->sesion_id = $sesion_id;
		$this->ip = $_SERVER["REMOTE_ADDR"];

		$this->login_admin = null;
		
		$content = array();
		foreach ($_REQUEST as $req_id => $req_value) {
			if (!in_array($req_id, array("__utma", "__utmz", "PHPSESSID", "authchallenge", "username", "password"))) {
				$content[] = $req_id."=".$req_value;
			}
		}
		if (count($content)>0) {
			$this->url = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."?".implode("&",$content);
		}
		else {
			$this->url = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	}
	
	function setLog($accion) {
		global $mdb2;
		
		if (!isset($mdb2->only_read) or $mdb2->only_read==0) {	
		$sql = "INSERT INTO log.login(sesion_id, cliente_usuario_id, fecha, accion, url, ip, admin_usuario_id) ".
			   "VALUES ('".
				pg_escape_string($this->sesion_id)."', ".
				pg_escape_string($this->usuario_id).", now(), '".
				pg_escape_string($accion)."', '".
				pg_escape_string($this->url)."', '".
				pg_escape_string($this->ip)."',".
        		// Si atributo no es null se agrega como código de administrador
        		pg_escape_string($this->login_admin?$this->login_admin:"null").")";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$this->setSyslog($sql, $res->userinfo);
			exit();
		}
		}
	}
	
	function setChange($accion, $detalle) {
		global $mdb2;
		
		if (!isset($mdb2->only_read) or $mdb2->only_read==0) {
			if (isset($_SESSION["admin_usuario_id"])) {
				$admin_usuario_id = $_SESSION["admin_usuario_id"];
			}
			else {
				$admin_usuario_id = "NULL";
			}
			$sql = "INSERT INTO log.cambio(cliente_usuario_id, admin_usuario_id, fecha, titulo, detalle) ".
				   "VALUES (".
					pg_escape_string($this->usuario_id).", ".
					pg_escape_string($admin_usuario_id).", now(), '".
					pg_escape_string($accion)."', '".
					pg_escape_string($detalle)."') ";
	//		print($sql);
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$this->setSyslog($sql, $res->userinfo);
				exit();
			}
		}
	}
	
	function setError($sql, $error, $sorry = true) {
		global $mdb2;
		
		if (!isset($mdb2->only_read) or $mdb2->only_read==0) {
		if ($this->usuario_id == null or $this->usuario_id == '') {
			$usuario_id = "NULL";
		}
		else {
			$usuario_id = pg_escape_string($this->usuario_id);
		}
		
		$sql = "INSERT INTO log.error(cliente_usuario_id, fecha, url, sql, resultado) ".
			   "VALUES (".
				$usuario_id.", now(), '".
				pg_escape_string($this->url)."', '".
				pg_escape_string($sql)."', '".
				pg_escape_string($error)."') ";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$this->setSyslog($sql, $res->userinfo);
			exit();
		}
		if ($sorry) {
		$this->getSorryServer();
		}
		}
		else {
			$this->setSyslog($error, $sql);
		}
	}
	
	function getSorryServer() {
		$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
		$T->setFile('tpl_contenido', 'sorry_server.tpl');
		
		$T->setVar('__path_img', REP_PATH_IMG);
		
		$T->pparse('out', 'tpl_contenido');
	}
	
	function setSyslog($error, $info) {
		syslog(LOG_CRIT, "ERROR: [".$error."], INFO: [".$info."]");
		
		$this->getSorryServer();
	}
	
}

?>