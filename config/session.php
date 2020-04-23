<?

class Session {

	private static $_sess_db;
	private static $_sess_log;

	/**
	 * Funcion que se ejecuta al principio de cada pagina con sesion.
	 */
	public static function open() {
		global $mdb2;
		global $log;
		self::$_sess_db = $mdb2;
		self::$_sess_log = $log;
	}

	/**
	 * Funcion que se ejecuta al final de cada pagina con sesion.
	 */
	public static function close() {
		return true;
	}

	/**
	 * Funcion que lee los datos de la sesion, 
	 * se ejecuta al principio de la carga de la pagina.
	 * 
	 * @param String $id session_id
	 */
	public static function read($id) {
		$sql = "SELECT sesion_data FROM log.sesion ".
			   "WHERE sesion = '".pg_escape_string($id)."' ";
//			   "AND sesion_ip = '".pg_escape_string($_SERVER["REMOTE_ADDR"])."'";
		$res =& self::$_sess_db->query($sql);
		if (MDB2::isError($res)) {
			self::$_sess_log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			return $row["sesion_data"];
		}
		return '';
	}

	/**
	 * Funcion que escribe los datos de la sesion, 
	 * se ejecuta al final de la carga de la pagina.
	 * 
	 * @param String $id session_id
	 * @param String $data session_data
	 */
	public static function write($id, $data) {
		$sql = "SELECT sesion_data FROM log.sesion ".
			   "WHERE sesion = '".pg_escape_string($id)."'";
		$res =& self::$_sess_db->query($sql);
		if ($res->numRows()>0) {
			$sql = "UPDATE log.sesion SET sesion_expires='".time()."', ".
				   "sesion_data='".pg_escape_string($data)."' ".
				   "WHERE sesion = '".pg_escape_string($id)."'";
			$res =& self::$_sess_db->query($sql);
			if (MDB2::isError($res)) {
				self::$_sess_log->setError($sql, $res->userinfo);
				exit();
			}
		}
		else {
			$username = self::parse($data, "username");
			$sql = "SELECT cliente_usuario_id ".
				   "FROM cliente_usuario ".
				   "WHERE email='".pg_escape_string($username)."'";
			$res =& self::$_sess_db->query($sql);
			if (MDB2::isError($res)) {
				self::$_sess_log->setError($sql, $res->userinfo);
				exit();
			}
			if ($res->numRows() > 0) {
			$row = $res->fetchRow();
			
			$sql = "INSERT INTO log.sesion(cliente_usuario_id, sesion, sesion_ip, sesion_expires, sesion_data) ".
				   "VALUES (".$row["cliente_usuario_id"].", '".
					pg_escape_string($id)."', '".
					pg_escape_string($_SERVER["REMOTE_ADDR"])."', '".time()."', '".
					pg_escape_string($data)."')";
			$res =& self::$_sess_db->query($sql);
			if (MDB2::isError($res)) {
				die($sql);
				self::$_sess_log->setError($sql, $res->userinfo);
				exit();
			}
			}
		}
	}

	/**
	 * Funcion que elimina la sesion utilizada actualmente,
	 * se ejecuta cada vez que se cierra la sesion.
	 * 
	 * @param String $id session_id
	 */
	public static function destroy($id) {
		$sql = "DELETE FROM log.sesion ".
			   "WHERE sesion = '".pg_escape_string($id)."'";
		$res =& self::$_sess_db->query($sql);
		if (MDB2::isError($res)) {
			self::$_sess_log->setError($sql, $res->userinfo);
			exit();
		}
		return true;
	}

	/**
	 * Funcion que elimina las sesiones que estan vencidas.
	 * Se ejecuta al principio de la carga de la pagina.
	 * 
	 * @param integer $max segundos de expiracion
	 */
	public static function gc($max) {
		$sql = "DELETE FROM log.sesion WHERE sesion_expires < ".(time() - $max);
		$res =& self::$_sess_db->query($sql);
		if (MDB2::isError($res)) {
			self::$_sess_log->setError($sql, $res->userinfo);
			exit();
		}
		return true;
	}
	
	/**
	 * Funcion que parsea los datos de sesion en busca de la variable buscada.
	 * 
	 * @param String $data session_data
	 * @param String $match
	 */
	public function parse($data, $match) {
		if(preg_match('/[a-z]:[0-9]+:"'.$match.'";[a-z]:[0-9]+:"([a-zA-Z0-9|\.|\@|\-|\_]+)";/', $data, $reg)) {
			return $reg[1];
		}
		else {
			return null;
		}
	}
	
	public function getSessionById($id) {
		global $mdb2;
		global $log;
		
		$sql = "SELECT sesion_data FROM log.sesion ".
			   "WHERE sesion = '".pg_escape_string($id)."' ";
//			   "AND sesion_ip = '".pg_escape_string($_SERVER["REMOTE_ADDR"])."'";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			return Session::parse($row["sesion_data"], "username");
		}
		else {
			return '';
		}
	}
}

function setSessionDB() {

	global $mdb2;
	
	if (!isset($mdb2->only_read) or $mdb2->only_read==0) {

		ini_set('session.save_handler', 'user');

		session_set_save_handler(array('Session', 'open'),
								 array('Session', 'close'),
								 array('Session', 'read'),
								 array('Session', 'write'),
								 array('Session', 'destroy'),
								 array('Session', 'gc')
								 );
	}
}

/* function setSessionGrafico($tiene_flash, $guardar = false) {
	global $mdb2;
	global $log;
	global $current_usuario_id;
	
	$useragent = Utiles::getUserAgent();
	
	if ($useragent != null) {
		
//		print_r($useragent);

		$version = explode(".", $useragent["version"]);
		$major = pg_escape_string($version[0]);
		$minor = pg_escape_string($version[1]);
		
		if (is_numeric($major) and is_numeric($minor)) {
		
		$desde_major = "split_part(version_desde, '.', 1)::INTEGER";
		$desde_minor = "split_part(version_desde, '.', 2)::INTEGER";
		$hasta_major = "split_part(version_hasta, '.', 1)::INTEGER";
		$hasta_minor = "split_part(version_hasta, '.', 2)::INTEGER";
	
		$sql = "SELECT * FROM browser ".
			   "WHERE nombre='".pg_escape_string($useragent["browser"])."' ".
			   "AND ($desde_major < $major OR ($desde_major = $major AND $desde_minor <= $minor)) ".
			   "AND (version_hasta is null OR $hasta_major > $major OR ($hasta_major = $major AND $hasta_minor > $minor)) ".
			   "AND (tiene_flash='".(($tiene_flash)?"t":"f")."' OR tiene_flash is null)".
			   "AND mobile='".(($useragent["plataforma"]=="Mobile")?"t":"f")."'";
//		print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$grafico_clase = $row["clase"];
		}
		
		}
	}
	
	if (isset($grafico_clase)) {
		return $grafico_clase;
	}
	else {
		if ($guardar == true) {
			$log->setError("USERAGENT NO IDENTIFICADO", "USERAGENT: {".$_SERVER["HTTP_USER_AGENT"]."}, PARSEADO: {".print_r($useragent, true)."}", false);
		}
		if ($tiene_flash) {
			return "GraficoFlash";
		}
		else {
			return "GraficoImagen";
		}
	}
}*/

?>