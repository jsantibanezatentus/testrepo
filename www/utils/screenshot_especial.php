<?
    include('../../config/common.php');

	if(isset($_REQUEST['token'])) {
		$token=$_REQUEST['token'];
	}
	
    $url = REP_API_IVR."/reporte/online/webservices/getScreenshotStepObjetive/?hash=".$token;
    $url = str_replace('', "%20", $url);
    header('Content-type: image/jpg');
    imagejpeg(imagecreatefromjpeg($url));

?>


