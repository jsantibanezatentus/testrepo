<?

include('../../config/common.php');
$objetivo_id= $_REQUEST['objetive'];
$fecha_inicio= $_REQUEST['fecha_inicio'];
$fecha_termino= $_REQUEST['fecha_termino'];

$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);


$url = REP_API_HOST.'/Intern/Intern/'.$objetivo_id.'/'.$fecha_inicio.'/'.$fecha_termino;
$url = str_replace(' ', "%20", $url);
$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
echo $json;