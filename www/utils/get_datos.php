<?

include('../../config/common.php');
	$user = $_REQUEST['user'];
	$inicio=	$_REQUEST['inicio'];
	$termino= $_REQUEST['termino'];
	$objetive= $_REQUEST['objetive'];
	$url = REP_API_HOST3."/reporte/online/general/getData/?inicio=".$inicio."&termino=".$termino."&objetive=".$objetive."&user=".$user;
	//echo $url;
    $url = str_replace(' ', "%20", $url);
    $arrContextOptions=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,),);
    $return = file_get_contents($url, false, stream_context_create($arrContextOptions));
    echo  $return;
    
?>