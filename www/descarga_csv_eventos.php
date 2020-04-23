<?
#  Permite la construcción y descarga del CSV.
## Autor: Carlos Sepúlveda.
#  Objetivo: Distribuir los datos para posteriormente imprimirlos en el csv y descargarlo.

$data = json_decode($_REQUEST['data']);
$clientName = $_REQUEST['client'];
$nameUser = $_REQUEST['nameUser'];

$jumpLine = "\n";
$semiColon = ";";
$result = "";
ob_clean();

/* PERMITE CREAR CSV */
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Type: application/csv');
header("Content-Disposition: attachment; filename=\"datos-.csv\"");

buildDocument();

/* Función principal de el armado de datos para el documento*/
function buildDocument(){
	buildInfo();
	buildHead();
	buildBody();
	writeCsv();
}
function buildInfo(){
	$GLOBALS['result'].= "*** Informacion ***".$GLOBALS['jumpLine'];
	$GLOBALS['result'].= "Ciente:".$GLOBALS['semiColon'];
	$GLOBALS['result'].= $GLOBALS['clientName'];
	$GLOBALS['result'].= $GLOBALS['jumpLine'];
	$GLOBALS['result'].= "Usuario:".$GLOBALS['semiColon'];
	$GLOBALS['result'].= $GLOBALS['nameUser'];
	$GLOBALS['result'].= $GLOBALS['jumpLine'].$GLOBALS['jumpLine'];
}
/* Añade los datos que contendra la cabezera. */
function buildHead(){
	$titleHead = array("ID", "NOMBRE", "COMENTARIO", "ESTADO", "FECHA INICIO", "FECHA TERMINO", "FECHA CREACION", "FECHA MODIFICACION", "TITULO", "OBJETIVOS ID");
	foreach ( $titleHead as $clave => $valor) {
		$GLOBALS['result'].= $valor.$GLOBALS['semiColon'];
	}
	$GLOBALS['result'].= $GLOBALS['jumpLine'];
}
/* Añade datos del cuerpo del documento a la variable*/
function buildBody(){
	
	foreach ( $GLOBALS['data'] as $clave => $valor) {
		$GLOBALS['result'].= $valor->id. $GLOBALS['semiColon'];
		$GLOBALS['result'].= $valor->nombre.$GLOBALS['semiColon'];
		$GLOBALS['result'].= $valor->comentario.$GLOBALS['semiColon'];
		$GLOBALS['result'].= (($valor->estado == 't')?'Activado':'Desactivado').$GLOBALS['semiColon'];
		$GLOBALS['result'].= $valor->fecha_inicio.$GLOBALS['semiColon'];
		$GLOBALS['result'].= $valor->fecha_termino.$GLOBALS['semiColon'];
		$GLOBALS['result'].= $valor->fecha_creacion.$GLOBALS['semiColon'];
		$GLOBALS['result'].= $valor->fecha_modificacion.$GLOBALS['semiColon'];
		$GLOBALS['result'].= $valor->titulo.$GLOBALS['semiColon'];
		$GLOBALS['result'].= $valor->objetivo_id.$GLOBALS['semiColon'];
		$GLOBALS['result'].= $GLOBALS['jumpLine'];
	}
}
/* Escribe los datos en el CSV.*/
function writeCsv(){
	echo $GLOBALS['result'];
}
?>