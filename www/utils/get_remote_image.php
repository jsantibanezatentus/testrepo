<?
    include('../../config/common.php');

	if(isset($_REQUEST['token'])) {
		$token=$_REQUEST['token'];
	}
	if(isset($_REQUEST['t'])) {
		$tipo=$_REQUEST['t'];
	}
	if(isset($_REQUEST['servicio'])) {
        $servicio=$_REQUEST['servicio'];
    }
    if($servicio == "mobile"|| $servicio=="meta"){
        $url = REP_CDN_MOBILE.'/imagen.php?token='.$token.'&tipo=fz&t='.$tipo.'&servicio='.$servicio;
        header('Content-type: image/png');
        imagepng(imagecreatefrompng($url));
    }else{
        $url = REP_CDN_HOST.'/imagen.php?token='.$token.'&tipo=fz&t='.$tipo.'&servicio='.$servicio;
        header('Content-type: image/jpg');
        imagejpeg(imagecreatefromjpeg($url));
    }
?>


