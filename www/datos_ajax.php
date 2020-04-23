<?php

// *** CODIGO CREADO POR: CARLOS SEPULVEDA *** 
//  *** FECHA CREACION: 02-05-2016 *** 

/* ENCARGADO DE TRAER LOS DATOS JSON DESDE NEW RELIC */
include("../config/include.php");
include("../config/authentication.php");

/* VARIABLES */
$id_graphic = $_REQUEST['tipo_grafico'];
$name_type = $_REQUEST['nombre_tipo'];
$time = $_REQUEST['tiempo'];
$url = array();

/* TRAE LA CONFIGURACION DEL OBJETIVO */
$objetivo = new ConfigObjetivo($_REQUEST['id_objetivo']);

/* RECORRER LOS DATOS DEL XML */
foreach ($objetivo->__datos as $configuracion) {

    if ($configuracion->tipo == $name_type) {
        array_push($url, $configuracion->url);
    }
}

/* EXTRAER EL LA URL QUE SE UTILIZARA */
$type_url = $url[0][$time];
/* FUNCION QUE EXTRAE LOS DATOS JSON DESDE NEW RELIC */

function getDataNewRelic($url) {
    $json = @file_get_contents($url);
    return json_encode($json);
}

/* CONDICION PARA EXTRAER SOLO LA URL DEL IFRAME */
if ($id_graphic == 6) {
    echo $type_url;
} else {
    echo getDataNewRelic($type_url);
}
?>