<?

include("../config/include.php");
include("../config/authentication.php");



$png= "tmp/file_".session_id().".png";
$png_menu= "tmp/file_menu_".session_id().".png";


/* NOMBRE PDF : OBJETIVO */
if ($_REQUEST["objeto_id"]) {
	$objetivo = $usr->getObjetivo($_REQUEST["objeto_id"]);
	$dwn_objetivo = $objetivo->nombre."_";
	$titulo=$objetivo->nombre;
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
$dwn_completo = $dwn_reporte.$dwn_objetivo.$dwn_periodo.".docx";
$dwn_completo = str_replace(array(" - ", " ", "/", ","), array("_", "-", "-", "-"), $dwn_completo);



//se crea Imagen para el menu de Observaciones.
$html_menu= REP_DOMINIO."menu_observaciones.php?objetivo_id=".$objetivo->objetivo_id."&subobjetivo_id=".$_REQUEST[subobjetivo_id]."&reporte_us=".$usr->usuario_id;


$result = passthru(REP_PATH_HTMLTOJPG." --no-images --load-error-handling ignore  --crop-x 165 --crop-w 695  ".escapeshellarg($html_menu)." ".$png_menu."");


$html = REP_DOMINIO."index.php?popup=1&word=1&session_id=".session_id()."&validador=".$_SESSION["ingreso_por_word"]."&objetivo_id=".$objetivo->objetivo_id;
foreach ($_REQUEST as $nombre => $valor) {
	$html.="&".$nombre."=".$valor;
}

$result = passthru(REP_PATH_HTMLTOJPG." --no-images --load-error-handling ignore  --crop-x 165 --crop-w 695 ".escapeshellarg($html)." ".$png."");



// New Word Document
$PHPWord = new  PHPWord();

// New portrait section
$section = $PHPWord->createSection();

// Title styles
$PHPWord->addTitleStyle(1, array('size'=>20, 'color'=>'333333', 'bold'=>true,'valign'=>'center'));

// Add header
$header = $section->createHeader();
$table = $header->addTable();
$table->addRow();
$table->addCell(4500)->addImage(REP_PATH_IMG.'header_pdf.png', array('width'=>630, 'height'=>120, 'align'=>'left'));


// Add footer
$footer = $section->createFooter();
$table = $footer->addTable();
$table->addRow();
$table->addCell(4500)->addImage(REP_PATH_IMG.'footer_pdf.png', array('width'=>630, 'height'=>100, 'align'=>'left'));

/*SE AGREGA LA IMAGEN DEL REPORTE AL DOCUMENTO WORD*/
$section->addTitle($titulo, 1);
$section->addTextBreak(1);
$section->addImage($png,array('width'=>650, 'height'=>650, 'align'=>'right'));

$section->addTextBreak(2);

$section->addImage($png_menu,array('width'=>650, 'height'=>40, 'align'=>'right'));
$section->addTextBreak(1);
$section->addTextBreak(1);

// Define table style arrays
$styleTable = array('borderSize'=>6, 'borderColor'=>'000000', 'cellMargin'=>80,'unit' => 'pct','align' => 'center');
$styleFirstRow = array('borderBottomSize'=>18, 'borderBottomColor'=>'000000');

// Define cell style arrays
$styleCell = array('valign'=>'center');
$styleCellBTLR = array('valign'=>'center', 'textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR);

// Define font style for first row
$fontStyle = array('bold'=>true, 'align'=>'center');

// Add table style
$PHPWord->addTableStyle('myOwnTableStyle', $styleTable, $styleFirstRow);

// Add table
$table = $section->addTable('myOwnTableStyle');

// Add row
$table->addRow(400);

// Add cells
$table->addCell(2000, $styleCell)->addText('Paso Afectado',$fontStyle);
$table->addCell(3000, $styleCell)->addText('Incidentes o eventos',$fontStyle);
$table->addCell(2000, $styleCell)->addText('Observaciones',$fontStyle);

// Add more rows / cells
for($i = 1; $i <= 3; $i++) {
	$table->addRow();
	$table->addCell(2000)->addText("--");
	$table->addCell(3000)->addText("--");
	$table->addCell(2000)->addText("--");

}

$objWriter =  PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
header('Content-Transfer-Encoding: none');
header("Pragma: public");
header("Content-type: application/docx");
header("Content-Disposition: attachment; filename=".$dwn_completo);

ob_clean();
$objWriter->save('php://output');

unlink($png);
unlink($png_menu);

$mdb2->disconnect();

?>
