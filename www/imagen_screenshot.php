<?php

include("../config/include.php");
include("../config/authentication.php");

if(isset($_REQUEST['paso'])) {
	$paso=$_REQUEST['paso'];
} 
else {
	$paso=0;
}
	
if(isset($_REQUEST['window'])){
	$window=$_REQUEST['window'];
}
else{
	$window='null';
}

$sql = "SELECT * FROM reporte.screenshot(".
		$usr->usuario_id.",".
		pg_escape_string($_REQUEST['objetivo_id']).",".
		pg_escape_string($paso).",'".
		pg_escape_string($_REQUEST['fecha'])."','".
		pg_escape_string($_REQUEST['tipo'])."','".
		pg_escape_string($window)."'".
		")as screenshot;";

$res =& $mdb2->query($sql);
if (MDB2::isError($res)) {
	ob_clean();
	header("Content-type: image/png");
	$archivo=fopen("img/screenshot_error.png", "rb");
	$imagen=base64_encode(fread($archivo, filesize("img/screenshot_error.png")));
	echo base64_decode($imagen);
	exit();
}

ob_clean();
		
$row = $res->fetchRow();
		
if ($row["screenshot"]==''){
	header("Content-type: image/png");
	$archivo=fopen("img/screenshot_error.png", "rb");
	$imagen=base64_encode(fread($archivo, filesize("img/screenshot_error.png")));			
}
else{
	header("Content-type: image/jpg");
	$imagen=$row["screenshot"];
}
echo base64_decode($imagen);

$mdb2->disconnect();

?>