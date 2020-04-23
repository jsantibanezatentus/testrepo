<?
/* PERMITE CREAR CSV */

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Type: application/csv');
header("Content-Disposition: attachment; filename=\"" . date("Y-m-d") . "-elementos-plus-" . $targetId . ".csv\"");
ob_clean();

$targetId = $_REQUEST['objetivoId'];
$data = json_decode(utf8_decode($_REQUEST['data']), true);
$targetName = $_REQUEST['objetivoName'];
$service = $_REQUEST['serviceId'];
$date = $_REQUEST['date'];
$timeDownload = $_REQUEST['time'];

echo build($data, $targetId, $targetName, $service, $date, $timeDownload);

function build($data, $targetId, $targetName, $service, $strDate, $timeDownload) {
    
    $elementOk = 0;
    $totalSize = 0;
    $totalSec = 0;
    $date = explode("T", $strDate);
    $hour = explode(".", $date[1]);
    $result = "";
    $result.= "\n ******* Detalle informacion objetivo *******";
    $result.= "\n Objetivo Id: ; " . $targetId . " \n Nombre Objetivo: ; " . $targetName . " \n Nombre Servicio: ; " . $service . "\n Fecha Monitoreo: ;" . $date[0] . " " . $hour[0] . "\n \n";
    $result.="; Id; Url ; Nombre Corto Url; Ip; Tiempo Total Descarga (s); Latencia (s); Descarga (s); Tamano Body (Bytes);  Tipo; Codigo Estado ; Es Ok; Descripcion Estado \n";
    $result.="\n";
  
    /* Escribiendo datos */
    for ($i = 0; $i < count($data); $i++) {
        for ($j = 0; $j < count($data[0]); $j++) {
            /*omitir algunas lineas*/
            if ($j != 2 && $j != 5 && $j != 7) {
                $str = str_replace(";", "-", $data[$i][$j]);
                if ($str == ' ' || $str == null) {
                    $str = ($j!=1)?0:'NO CAPTURADO';
                }
                /*col url*/
                if ($j == 0) {
                    $str = $i . ";" . $str;
                } /*col ip*/
                elseif ($j == 1) {
                    $splitUrl = split('[?]', $data[$i][$j - 1]);
                    $shortUrl = split('[/]', $splitUrl[0]);
                    $str = $shortUrl[(count($shortUrl)) - 1] . ";" . $str;
                }/*col Latencia*/
                elseif($j == 3){
                    $replace = str_replace(':', '', $str);
                    $strSec = str_replace('.','', $replace);
                    $totalSec = $totalSec + $strSec;
                    $sumDownload = getSumTime($data[$i][$j], $data[$i][$j+1]); 
                    $result.= ";" . $sumDownload;
                }/*col tamaño*/
                elseif($j == 6){
                    $strSize = (int)$str;
                    $totalSize = $totalSize + $strSize;
                }/*col status*/
                elseif($j == 10){
                    if($str == 'true'){
                        $str = 'Si';
                        $elementOk++;
                    }else{$str = 'No';}
                }/*col nombre estado*/
                elseif($j == 11){
                    $str = replaceAccentMark($str);
                }
                $result.= ";" . $str;
            }
        }
        $result.="\n";
    }
    return $result;
}
function getSumTime ($strLatency, $strDownload){
    
    $latency = str_replace(':', '', $strLatency);
    $download = str_replace(':', '', $strDownload);
    
    return str_replace('.', ',', $latency + $download);
}
function replaceAccentMark($str){
    
    $notAllowed= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
    $allowed= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
    $str_return = str_replace($notAllowed, $allowed ,$str);
    return $str_return;
}
?>