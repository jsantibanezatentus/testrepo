<?

include("../config/include.php");
include("../config/authentication.php");

$html = REP_LOCALHOST_DOMINIO."index.php?popup=1&pdf=1&session_id=".session_id()."&validador=".$_SESSION["ingreso_por_pdf"];
foreach ($_REQUEST as $nombre => $valor) {
	$html.="&".$nombre."=".$valor;
}
$pdf = "tmp/file_".session_id().".pdf";


/* NOMBRE PDF : OBJETIVO */
if ($_REQUEST["objeto_id"]) {
	$objetivo = $usr->getObjetivo($_REQUEST["objeto_id"]);
	$dwn_objetivo = $objetivo->nombre."_";
}
else {
	$dwn_objetivo = "";
}

/* NOMBRE PDF : PERIODO */
if ($_SESSION["usa_calendario_periodico"]) {
	$timestamp = new Timestamp($_SESSION["fecha_inicio_periodico"], $_SESSION["fecha_termino_periodico"]);
}
elseif ($_SESSION["usa_calendario"]) {
	$timestamp = new Timestamp($_SESSION["fecha_inicio"], $_SESSION["fecha_termino"]);
}
elseif ($_REQUEST["usa_periodo_semaforo"]) {
	$timestamp = new Timestamp($usr->periodo_semaforo_inicio);
}
else {
	$timestamp = new Timestamp();
}
$dwn_periodo = $timestamp->toString();

/* NOMBRE PDF : REPORTE */
$sactual = Seccion::getSeccionPorDefecto($_REQUEST["menu_id"]);
$secciones = $sactual->getSeccionesNivel(1);
$dwn_reporte = "";
foreach ($secciones as $seccion) {
	if ($seccion->es_parent) {
		$dwn_reporte = $seccion->nombre."_";
	}
}

/* NOMBRE COMPLETO PDF */
$dwn_completo = $dwn_reporte.$dwn_objetivo.$dwn_periodo.".pdf";
$dwn_completo = str_replace(array(" - ", " ", "/", ","), array("_", "-", "-", "-"), $dwn_completo);

if($_REQUEST["objeto_id"]==$_REQUEST["especial_objetivo_id"]){
	exec(REP_PATH_HTMLTOPDF." -T 30mm -B 18mm -L 2mm -R 2mm ".
		 "--header-spacing 10 --header-html ".REP_PATH_TEMPLATES."header2_pdf.html --javascript-delay 2500 ".
		 "--footer-html ".REP_PATH_TEMPLATES."footer2_pdf.html ".
		 escapeshellcmd($html)." '".escapeshellcmd($pdf)."'", $result);
}elseif ($_REQUEST["position"]==true) {
	exec(REP_PATH_HTMLTOPDF." -T 30mm -B 18mm -L 2mm -R 2mm -O landscape ".
		 "--header-spacing 10 --header-html ".REP_PATH_TEMPLATES."headerhacienda_pdf.html --javascript-delay 2500 ".
		 "--footer-html ".REP_PATH_TEMPLATES."footerhacienda_pdf.html ".
		 escapeshellcmd($html)." '".escapeshellcmd($pdf)."'", $result);
}else{
	exec(REP_PATH_HTMLTOPDF." -T 30mm -B 18mm -L 2mm -R 2mm ".
		 "--header-spacing 10 --header-html ".REP_PATH_TEMPLATES."header_pdf.html --javascript-delay 2500 ".
		 "--footer-html ".REP_PATH_TEMPLATES."footer_pdf.html ".
		 escapeshellcmd($html)." '".escapeshellcmd($pdf)."'", $result);
}
header("Cache-Control:  maxage=1");
header("Pragma: public");
header("Content-type: application/pdf");
header("Content-Disposition: attachment; filename=".$dwn_completo);
$file = fopen($pdf, "r");
fpassthru($file);

unlink($pdf);

$mdb2->disconnect();

?>
