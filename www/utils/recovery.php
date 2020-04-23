<?

include('../../config/common.php');

if (isset($_REQUEST['function'])) {
	$function = $_REQUEST['function'];
	$function();
}

function mailCheck(){
	$mail = $_REQUEST['mail'];
	$mail = base64_encode($mail);
	$url = REP_API_HOST."/reporte/online/changepass/?mail=".$mail;
    $url = str_replace(' ', "%20", $url);
    $arrContextOptions=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,),);
    $return = file_get_contents($url, false, stream_context_create($arrContextOptions));
    
    echo $return;
    
}

?>