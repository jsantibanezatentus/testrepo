<?
include('../../config/common.php');
if (isset($_REQUEST['function'])) {
   $function = $_REQUEST['function'];
   $function();
}

function configObjetive($user, $objetive, $step){
    $url = REP_API_HOST.'/reporte/online/objetiveConfig/?user='.$user.'&hash=1&objetive='.$objetive.'&step='.$step;
    $arrContextOptions=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,),);
	$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
    return  $json;
}
?>