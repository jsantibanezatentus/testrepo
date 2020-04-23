<?

include('../../config/common.php');
$user= $_REQUEST['user'];
$inicio= $_REQUEST['inicio'];
$termino= $_REQUEST['termino'];
$data= $_REQUEST['data'];
$data_nodo= $_REQUEST['data_nodo'];
$url = REP_API_HOST3.'/reporte/especial/AvailabilityResponseStep/?user='.$user.'&begin='.$inicio.'&final='.$termino.'&data='.$data.'&data_nodo='.$data_nodo;
$arrContextOptions=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,),);
$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
echo $json;
