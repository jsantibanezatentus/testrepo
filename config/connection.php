<?

/* LEE ARCHIVO EXTERNO DE CONFIGURACIÓN */
$db_config = parse_ini_file("connection.ini", true);


$arr_dsn = array();

/**
 * Utiles del sistema para conexion con BD.
 */
define('REP_DB_ID', $db_config["rw"]["id"]);
define('REP_DB_HOST', $db_config["rw"]["host"]);
define('REP_DB_USER', $db_config["rw"]["user"]);
define('REP_DB_PASS', $db_config["rw"]["pass"]);
define('REP_DB_BASE', $db_config["rw"]["db"]);
define('REP_DB_PORT', $db_config["rw"]["port"]);
define('REP_DB_PREFIX', $db_config["rw"]["prefix"]);
define('REP_DB_CLASS', $db_config["rw"]["class"]);

$dsn_w = array(
    'phptype'  => REP_DB_PREFIX,
    'username' => REP_DB_USER,
	'password' => REP_DB_PASS,
    'hostspec' => REP_DB_HOST,
    'database' => REP_DB_BASE,
    'port'     => REP_DB_PORT
);

$arr_dsn[REP_DB_ID] = $dsn_w;


/**
 * Utiles del sistema para conexion con BD (Solo lectura).
 */
define('REP_DB_READ_ID', $db_config["ro"]["id"]);
define('REP_DB_READ_HOST', $db_config["ro"]["host"]);
define('REP_DB_READ_USER', $db_config["ro"]["user"]);
define('REP_DB_READ_PASS', $db_config["ro"]["pass"]);
define('REP_DB_READ_BASE', $db_config["ro"]["db"]);
define('REP_DB_READ_PORT', $db_config["ro"]["port"]);
define('REP_DB_READ_PREFIX', $db_config["ro"]["prefix"]);
define('REP_DB_READ_CLASS', $db_config["ro"]["class"]);

$dsn_r = array(
    'phptype'  => REP_DB_READ_PREFIX,
    'username' => REP_DB_READ_USER,
	'password' => REP_DB_READ_PASS,
    'hostspec' => REP_DB_READ_HOST,
    'database' => REP_DB_READ_BASE,
    'port'     => REP_DB_READ_PORT
);

$arr_dsn[REP_DB_READ_ID] = $dsn_r;


/**
 * Utiles del sistema para login con AUTH.
 */
define('REP_AUTH_TABLE','public.cliente_usuario');
define('REP_AUTH_USER','email');
define('REP_AUTH_PASS','');
define('REP_AUTH_MD5_PASS','clave');
define('REP_AUTH_IDLE','1800');

?>