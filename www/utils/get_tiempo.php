<?

include('../../config/common.php');
$token= $_REQUEST['datos'];
$objetivo_id= $_REQUEST['objetive'];
$fecha_ini= $_REQUEST['date1'];
$fecha_ter= $_REQUEST['date2'];


$url = REP_API_HOST.'/api-base/tiempo_respuesta_extendido/consolidado/?format=json&token='.$token.'&objetivo_id='.$objetivo_id.'&fecha_inicio='.$fecha_ini.'&fecha_termino='.$fecha_ter;
$url = str_replace(' ', "%20", $url);
$json = file_get_contents($url);
echo $json;