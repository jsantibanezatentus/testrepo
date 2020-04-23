<?

include('../../config/common.php');

$token= $_REQUEST['datos2'];
$objetivo_id= $_REQUEST['objetive'];

$url2 = REP_API_HOST.'/api-base/tiempo_respuesta_extendido/ultimo_registro/?format=json&token='.$token.'&objetivo_id='.$objetivo_id;
$json2 = file_get_contents($url2);
$data2=json_decode($json2, true);
$fecha_ajax = $data2['fecha'];
$fecha_ajax = str_replace(" ", "T", $fecha_ajax);
echo $fecha_ajax;

