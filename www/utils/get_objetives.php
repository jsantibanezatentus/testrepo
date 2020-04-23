<?
function get_objetives($user, $hash){
    $url = REP_API_HOST."/reporte/online/general/?user=".$user."&hash=".$hash;
    $arrContextOptions=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,),);
	$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
    return $json;
}
?>