<?

    function get_listado($objetivo_id, $fecha_inicio, $fecha_termino, $monitor, $pasos){
	    $url = REP_CDN_HOST."/evento_imagenes.php?obj_id=".$objetivo_id."&monitor=".$monitor."&pasos=".$pasos."&fecha_inicio='".$fecha_inicio."'"."&fecha_termino='".$fecha_termino."'";
		//echo $url;
		$json = file_get_contents($url);
		return $json;
	}
?>