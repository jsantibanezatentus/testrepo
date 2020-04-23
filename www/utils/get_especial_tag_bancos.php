<?

    function get_especial_tag_banco($cliente_id,$user, $fecha, $tag, $horario, $objetivo, $token){
	    $url = REP_API_HOST3.'/reporte/especial_webService/TagBank/?date='.$fecha.
	    '&user='.$user.'&clientId=5761&tag='.$tag.'&idHorario='.$horario.'&objetivoId='.$objetivo.'&tokenWs='.$token.'&input=reporte';
	    $arrContextOptions=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,),);
		$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
		return $json;
	}
?>