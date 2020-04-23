<?
include('../../config/common.php');
$user= $_REQUEST['user'];
$objetivo= $_REQUEST['objetivo'];
$inicio=$_REQUEST["inicio"];
$termino=$_REQUEST["termino"];

$url = REP_API_HOST3.'/reporte/especial/getVRSupervielle/?user='.$user.'&begin='.$inicio.'&final='.$termino.'&objetive='.$objetivo;
$arrContextOptions=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,),);
$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
echo $json;
