<?
function get_eventos($user, $objetive, $fecha_inicio, $fecha_termino, $hash){
    $url = REP_API_HOST."/reporte/online/eventos/?user=".$user."&objetive=".$objetive."&datebegin=".$fecha_inicio."&finaldate=".$fecha_termino."&hash=".$hash;
    $url = str_replace(' ', "%20", $url);
    $arrContextOptions=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,),);
	$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
    return $json;
}
?>