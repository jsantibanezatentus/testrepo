<?
include('../../config/common.php');
$token = $_REQUEST['token'];
$objetivo_id = $_REQUEST['objetivo'];
$url = REP_API_HOST3."/reporte/especial/getVRLipigas/?objetivo=".$objetivo_id."&token=".$token ;
$arrContextOptions = array("ssl" => array("verify_peer" => false, "verify_peer_name" => false));
$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
echo $json;
?>