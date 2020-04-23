<?
include('../../config/common.php');

$objetivo_id = $_REQUEST['objetivo'];
$pasos = $_REQUEST['paso'];
$fecha_inicio = $_REQUEST['hora_inicio_utc'];
$fecha_termino = $_REQUEST['hora_termino_utc'];

$monitor = $_REQUEST['monitor'];

$url = REP_CDN_HOST."/evento_imagen.php?obj_id=".$objetivo_id."&monitor=".$monitor."&pasos=".$pasos."&fecha_inicio='".$fecha_inicio."'"."&fecha_termino='".$fecha_termino."'";
$json = file_get_contents($url);
echo $json;


?>