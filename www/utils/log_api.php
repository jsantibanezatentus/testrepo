<?
include('../../config/common.php');
$objetivo_id= $_REQUEST['objetive'];
$log_fecha= $_REQUEST['log_fecha'];
$cliente_usuario_id= $_REQUEST['usuario'];
$informe_id= $_REQUEST['informe'];
$tipo_acceso_id= $_REQUEST['tipo_acceso_id'];
$cliente_id= $_REQUEST['cliente'];
$token= $_REQUEST['token'];

$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);


$url = REP_API_HOST.'/Intern/Insert/'.$objetivo_id.'/'.$cliente_id.'/'.$log_fecha.'/'.$cliente_usuario_id.'/'.$tipo_acceso_id.'/'.$informe_id.'/'.$token;
$url = str_replace(' ', "%20", $url);
$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
echo $json;