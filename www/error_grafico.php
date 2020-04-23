<?php
// *** CODIGO CREADO POR: CARLOS SEPULVEDA ***
//  *** FECHA CREACION: 10-05-2016 *** 
include("../config/include.php");
include("../config/authentication.php");
/* TRUE = ERROR XML || FALSE = SIN DATOS */
$tipo_error = $_REQUEST["tipo_error"];

$clase = new GraficoSVG();
/* CONTENEDOR ERROR */
$generar_contenedor_error = $clase->_generarContenedor_ErrorXml($tipo_error);

echo $generar_contenedor_error;
?>
