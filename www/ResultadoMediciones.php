<?php
header("Content-Type: application/json");

include("../config/include.php");

include 'utils/getJsonResultado.php';
$token=$_REQUEST['token'];
$fecha=$_REQUEST['fecha'];
$url = REP_API_HOST3."/reporte/online/especial/getMeasuresByDay/?token=".$token."&fecha=".$fecha."&format=json";
$arrContextOptions=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,),);
$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
echo $json;
$mdb2->disconnect();

?>