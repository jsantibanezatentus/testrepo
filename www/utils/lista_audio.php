<?
function get_audio_listado_resultado($objetivo_id, $fecha_inicio, $fecha_termino, $monitor, $tipo){

    $url = REP_CDN_IVR."/cdn/audios/".$monitor."/".$objetivo_id."/".$fecha_inicio."/".$fecha_termino."/".$tipo;
    
    $url = str_replace(' ', "%20", $url);
    $json = file_get_contents($url);
   // echo $json;
    return $json;
}
?>