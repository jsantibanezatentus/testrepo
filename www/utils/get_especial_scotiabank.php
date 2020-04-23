<?

    function get_especial_scotiabank($cliente_id,$user, $fecha_inicio, $fecha_termino){
	    $url = REP_API_HOST3.'/reporte/online/webservices/getUptimeHourBank/?clienteId='.$cliente_id.'&user='.$user.'&fechaInicio='.$fecha_inicio.'&fechaTermino='.($fecha_termino).'&format=json&key=07374CEAC2EAEF2EB3441BA0C433F9834E6CF6E2&&tokenWS=b424eb320c9cd56e3324c00719eb1a3c0w74adumn';
	    $arrContextOptions=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,),);
		$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
		return $json;
	}
?>