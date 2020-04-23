<?

function adminLoginFunction($username = null, $status = null, &$auth = null) {
	global $auth;

	$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
	$T->setFile('tpl_contenido', 'login_admin.tpl');
	$T->setBlock('tpl_contenido','TIENE_MENSAJE','tiene_mensaje');

	$T->setVar('__usuario_id',$_REQUEST["usuario_id"]);
	$T->setVar('__ga_tracking_id', REP_GA_TRACKING_ID);
	$T->setVar('__path_ga', '/login_admin');

	if (isset($_REQUEST["usuario_email"])) {
		$T->setVar('__usuario_email',$_REQUEST["usuario_email"]);
		$T->setVar('__usuario_sel','readonly');
	}
	else {
		$T->setVar('__usuario_email','');
		$T->setVar('__usuario_sel','');
	}

	if (isset($auth->__msg_error)) {
		$T->setVar('__msg_error',$auth->__msg_error);
		$T->parse('tiene_mensaje','TIENE_MENSAJE',true);
	}

	$T->setVar('__path_img',REP_PATH_IMG);
	$T->pparse('out','tpl_contenido');
}

function loginFunction($username = null, $status = null, &$auth = null) {
	global $auth;
	
	if ( ($_REQUEST["item_id"] and !isset($_REQUEST["popup"])) or  
		((!$_REQUEST["item_id"] and !isset($_REQUEST["popup"])) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
		echo("LOGOUT");
		exit();
	}

	$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
	$T->setFile('tpl_contenido', 'login.tpl');
	$T->setBlock('tpl_contenido', 'TIENE_MENSAJE', 'tiene_mensaje');

	$T->setVar('__anno', date("Y"));
	$T->setVar('__ga_tracking_id', REP_GA_TRACKING_ID);
	$T->setVar('__path_ga', '/login');
	
	if (isset($auth->__msg_error)) {
		$T->setVar('__msg_error', $auth->__msg_error);
		$T->parse('tiene_mensaje', 'TIENE_MENSAJE', true);
	}
	$T->pparse('out', 'tpl_contenido');
}

function adminLoginErrorFunction() {
	global $auth;
	$auth->__msg_error = "Administrador y Contraseña no coinciden, o Usuario Reporte no existe.";
}
function loginErrorFunction() {
	global $auth;
	global $mdb2;

	++$_COOKIE['count'];
    setcookie("count", $_COOKIE['count']);
	if($_COOKIE['count']>=10){
		$sql = "UPDATE public.cliente_usuario SET fecha_expiracion='1900-12-01' WHERE email='".pg_escape_string($_REQUEST["username"])."'";
		$res =& $mdb2->query($sql);
		$auth->__msg_error = "Usuario bloqueado por 10 intentos fallidos. <br> Por favor contacte a su ejecutivo comercial.";
		setcookie("count", "");
	}else{
		$auth->__msg_error = "Nombre de Usuario y Contraseña no coinciden.<br/>Por favor, inténtelo nuevamente. Usted posee ".$_COOKIE['count']." de 10 intentos";
	}
}

function expiredErrorFunction() {
	global $auth;
	$auth->__msg_error = "Su sesión ha expirado.";
}

setSessionDB();
//if (isset($_REQUEST["session_id"]) and $_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"]) {
if (isset($_REQUEST["session_id"])) {
	$username_session = Session::getSessionById($_REQUEST["session_id"]);
	if ($username_session != '') {
		session_id($_REQUEST["session_id"]);
	}
}

/* AUTENTIFICACION ADMIN */
$login_admin = null;
if (isset($_POST["username"]) and $_POST["username"]!="") {

	/* VER EL EMAIL CON DIFERENCIAS ENTRE MAYUSCULAS Y MINUSCULAS */
	$sql = "SELECT cliente_usuario_id, email, clave FROM public.cliente_usuario ".
			"WHERE lower(email)=lower('".pg_escape_string($_POST["username"])."')";
	$res =& $mdb2->query($sql);
	if (MDB2::isError($res)) {
		$log->setError($sql, $res->userinfo);
		exit();
	}
	if ($row = $res->fetchRow()) {
		$_POST["username"] = $row["email"];
		$clave_tmp = $row["clave"];
		$usuario_id = $row["cliente_usuario_id"];
	}

	/* VER SI EXISTE EL USUARIO ADMINISTRADOR */
	$sql = "SELECT admin_usuario_id FROM public.admin_usuario ".
			"WHERE email='".pg_escape_string($_POST["username_admin"])."' ".
			"AND password=md5('".pg_escape_string($_POST["password_admin"])."')";
	$res =& $mdb2->query($sql);
	if (MDB2::isError($res)) {
		$log->setError($sql, $res->userinfo);
		exit();
	}
	if ($row = $res->fetchRow()) {
		$login_admin = $row["admin_usuario_id"];
		$_POST["password"] = $clave_tmp;
	}
	
}

/* AUTENTIFICACION */
$conf_auth = array(
		'dsn'         => $dsn,
		'cryptType'   => ($_REQUEST["admin"] == "t")?'':'md5',
		'table'       => REP_AUTH_TABLE,
		'usernamecol' => REP_AUTH_USER,
		'passwordcol' => REP_AUTH_MD5_PASS
);


$auth =& new Auth('MDB2', $conf_auth, ($_REQUEST["admin"] == "t")?'adminLoginFunction':'loginFunction');
$auth->loginFailedCallback = ($_REQUEST["admin"] == "t")?'adminLoginErrorFunction':'loginErrorFunction';
$auth->logoutCallback = 'expiredErrorFunction';

// MENSAJES SEGUN TIPO DE CIERRE DE SESION.
if ($_REQUEST["logout"] == "t") {
//	global $auth;
	$auth->__msg_error = "Su sesión ha finalizado.";
}

elseif ($_REQUEST["userExpired"] == "t") {
	$mensaje = "Usuario Expirado por favor contacte a su ejecutivo comercial.";
	if (isset($_REQUEST["clienteId"]) and $_REQUEST["clienteId"] > 0) {

		$sql = "SELECT mensaje_expiracion FROM public.cliente WHERE cliente_id=".$_REQUEST["clienteId"];
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$row = $res->fetchRow();
		if ($row['mensaje_expiracion'] != "" and $row['mensaje_expiracion'] != null) {
			$mensaje = $row['mensaje_expiracion'];
		}
	}
	$auth->__msg_error = $mensaje;
}elseif ($_REQUEST["userBloqued"] == "t") {
	$auth->__msg_error = 'Usuario bloqueado por 10 intentos fallidos. <br> Por favor contacte a su ejecutivo comercial.';
}

if (isset($_REQUEST["session_id"]) and $username_session != '') {
	$auth->setAuth($username_session);
	session_id($_REQUEST["session_id"]);
	session_start();
	if (isset($_REQUEST["word"])){
    	if(!isset($_SESSION["ingreso_por_word"]) or $_SESSION["ingreso_por_word"] != $_REQUEST["validador"]) {
			session_unset();
			exit;
        }
    }
    else{
    	if (!isset($_SESSION["ingreso_por_pdf"]) or $_SESSION["ingreso_por_pdf"] != $_REQUEST["validador"]) {
			session_unset();
			exit;
    	 }   
    }
}

$auth->setIdle(REP_AUTH_IDLE, true);
$auth->start();

// SI LA SESION HA EXPIRADO
if (!$auth->checkAuth()) {
	$auth->logout();
	session_unset();
	session_destroy();
	exit;
}

/* DATOS DEL USUARIO */
$current_usuario_id = Usuario::UsuarioId($auth->getUsername());
$usr = new Usuario($current_usuario_id);
$usr->__Usuario();

// SI EL USUARIO HA EXPIRADO Y YA NO TIENE PERMISOS
$intentoLogin = $usr->puedeLogin();
if ($intentoLogin == 'userExpired') {
	$cliente_id = $usr->cliente_id;
	$auth->logout();
	session_unset();
	session_destroy();
	header("location: index.php?userExpired=t&clienteId=".$cliente_id);
	exit;
}elseif ($intentoLogin == 'userBloqued') {
	$cliente_id = $usr->cliente_id;
	$auth->logout();
	session_unset();
	session_destroy();
	header("location: index.php?userBloqued=t&clienteId=".$cliente_id);
	exit;
}

// SI SE CERRO LA SESION
if ($_REQUEST["logout"] == 't') {
	if(isset($_COOKIE[session_name()])) {
		unset($_COOKIE);
	}
	$auth->logout();
	session_unset();
	session_destroy();
	header("location: index.php");
	exit;
}


$login = false;
if (isset($_REQUEST["password"]) or isset($_REQUEST["password_admin"])) {
	header("location: index.php");
	$login = true;
}

$log->usuario_id = $usr->usuario_id;
$log->sesion_id = session_id();

if ($login) {
  $log->login_admin = $login_admin;
  $log->setLog("LOGIN");
}

?>