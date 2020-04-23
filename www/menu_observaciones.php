<?

include("../config/include.php");

ob_clean();


$usr = new Usuario($_REQUEST["reporte_us"]);
$usr->__Usuario();

$objetivo = $usr->getObjetivo($_REQUEST['objetivo_id'], REP_DATOS_ESPECIALES);

$objetivo = new ConfigEspecial($objetivo->objetivo_id);
if (isset($_REQUEST['subobjetivo_id'])) {
	$subobjetivo = $objetivo->getSubobjetivo($_REQUEST["subobjetivo_id"]);
	if ($subobjetivo == null) {
		header("HTTP/1.0 400 error de parametros");
 		echo "subobjetivo_id invalido";
		exit();
	}	
}


$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
$T->setFile('tpl_contenido', 'observaciones.tpl');


$T->setVar('__reporte_titulo', $objetivo->nombre);
$T->setVar('__objetivo_nombre', $subobjetivo->nombre);

$T->pparse('out', 'tpl_contenido');

?>