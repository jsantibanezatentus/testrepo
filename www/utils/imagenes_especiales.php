<?
    include('../../config/common.php');

	if(isset($_REQUEST['objetivo'])) {
		$objetivo=$_REQUEST['objetivo'];
	}
	$tipo=$_REQUEST['t'];

    $url = REP_CDN_HOST.'/imagen_especial.php?objetivo='.$objetivo.'&tipo='.$tipo;
    header('Content-type: image/png');
    imagepng(imagecreatefrompng($url));
    
?>


