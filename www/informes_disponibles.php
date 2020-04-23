<?php

include("../config/include.php");
include("../config/authentication.php");

$objetivo_id = $_REQUEST["objetivo_id"];
$mes = $_REQUEST["mes"];
$ano = $_REQUEST["ano"];

$sql = "SELECT mes, ris.reporte_informe_subtipo_id, ris.nombre AS tipo, rpi.nombre, rpi.fecha, rpi.fecha_inicio, rpi.fecha_termino".
    " FROM (SELECT date_part('month', fecha) as mes, * FROM public.reporte_periodico_informes(".
    pg_escape_string($usr->usuario_id).", ".
    pg_escape_string($objetivo_id).", ".
    pg_escape_string($ano).") ".
    " ) AS rpi".
    " INNER JOIN public.reporte_informe_subtipo AS ris ON rpi.reporte_informe_subtipo_id = ris.reporte_informe_subtipo_id".
    " WHERE mes = ". pg_escape_string($mes).
    " ORDER BY rpi.fecha_termino ASC, ris.reporte_informe_subtipo_id DESC";

$res =& $mdb2->query($sql);
if (MDB2::isError($res)) {
    echo($sql);
    exit();
}

echo '<div class="informes">';
echo '<div class="lista">';
echo '<div class="leyenda">informes disponibles<img class="indicador-carga" src="/img/cargando.gif" /></div>';

echo '<ul>';

if($res->numRows() > 0) {
    while ($row = $res->fetchRow()) {
        $fecha_inicio = substr($row["fecha_inicio"], 0, 10) . "T" .substr($row["fecha_inicio"], 11, 16);
        $fecha_termino = substr($row["fecha_termino"], 0, 10). "T" .substr($row["fecha_termino"], 11, 16);
        $reporte_informe_subtipo_id = $row["reporte_informe_subtipo_id"];
        $tipo = $row["tipo"];
        $nombre = $row["nombre"];
        echo '<li class="informe" data-fecha-inicio="'. $fecha_inicio .'" data-fecha-termino="'. $fecha_termino .'" data-reporte-informe-subtipo-id="'. $reporte_informe_subtipo_id .'">';
        echo '<span class="check">&nbsp;</span>';
        echo '<span class="tipo">'. $tipo .'</span>';
        echo '<span class="fecha">'. $nombre .'</span>';
        echo '</li>';
    }
}
else {
    echo '<li class="flash">no hay informes</li>';
}


echo '</ul>';

echo '</div>';
echo '</div>';

$mdb2->disconnect();
