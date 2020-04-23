<?
include('../../config/common.php');
if (isset($_REQUEST['function'])) {
   $function = $_REQUEST['function'];
   $function();
}

function update_objetives(){
	$user=$_REQUEST['user'];
    $url = REP_API_HOST."/reporte/online/cacheItask/?function=checkuser&user=".$user."&key=07374CEAC2EAEF2EB3441BA0C433F9834E6CF6E2";
    $arrContextOptions=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,),);
	$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
    return $json;
}
?>