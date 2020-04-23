<?
function get_data_ivr($objetivo_id, $fecha_inicial, $fecha_final, $usuario_id, $hash){
    $url = REP_API_HOST.'/reporte/online/sla_operador_ivr/?objetivo_id='.$objetivo_id.'&fecha_inicial='.$fecha_inicial.'&fecha_final='.$fecha_final.'&usuario_id='.$usuario_id.'&hash='.$hash;
    $url = str_replace(' ', "%20", $url);
	$json = file_get_contents($url);
    return $json;
}
?>