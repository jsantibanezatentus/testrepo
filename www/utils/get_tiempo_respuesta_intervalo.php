<?

include('../../config/common.php');
$user= $_REQUEST['user'];
$objetives= $_REQUEST['objetives'];
$step= $_REQUEST['step'];
$inicio= $_REQUEST['inicio'];
$termino= $_REQUEST['termino'];
$hash= $_REQUEST['hash'];

$url = REP_API_HOST.'/reporte/especiales/comparativo_banco/?user='.$user.'&objetives='.$objetives.'&step='.$step.'&datebegin='.$inicio.'&finaldate='.$termino.'&hash='.$hash;
$arrContextOptions=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,),);
$json = file_get_contents($url, false, stream_context_create($arrContextOptions));
echo $json;
