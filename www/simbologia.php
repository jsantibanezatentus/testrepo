<?php

include("../config/include.php");

$codigos_deprecated = array(2, 28, 29, 60, 62, 64, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96);

$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
$T->setFile('tpl_tabla', 'simbologia.tpl');
$T->setBlock('tpl_tabla', 'LISTA_CODIGOS', 'lista_codigos');

$linea = 1;
foreach (Constantes::getCodigos() as $codigo) {
	if (!in_array($codigo["codigo_id"], $codigos_deprecated)) {
		$T->setVar('__codigo_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
		$T->setVar('__codigo_id', $codigo["codigo_id"]);
		$T->setVar('__codigo_nombre', $codigo["nombre"]);
		$T->setVar('__codigo_descripcion', $codigo["descripcion"]);
		$T->setVar('__codigo_icono', substr($codigo["icono"], 0, -4));
		$T->setVar('__codigo_color', $codigo["color"]);
		$T->parse('lista_codigos', 'LISTA_CODIGOS', true);
		$linea++;
	}
}

$T->pparse('out', 'tpl_tabla');

$mdb2->disconnect();

?>