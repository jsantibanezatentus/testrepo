<?
// *** CODIGO CREADO POR: CARLOS SEPULVEDA *** 
//  *** FECHA CREACION: 02-05-2016 *** 

/*ENCARGADO DE GENERAR EL CSV*/
include("../config/include.php");
include("../config/authentication.php");
ob_clean();

$objetivo = new ConfigObjetivo($_REQUEST['id_objetivo']);
$data = json_decode($_REQUEST['datos']);
$type_graphic = $_REQUEST['tipo_grafico'];
$information = $_REQUEST['informacion'];

/* PERMITE CREAR CSV */
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Type: application/csv');
header("Content-Disposition: attachment; filename=\"datos-" . $objetivo->nombre . ".csv\"");

/*IMPRIME EN EL CSV*/
echo  getCsvNewRelic($objetivo, $data, $type_graphic, $information);

function getCsvNewRelic($xpath, $data, $type_graphic, $information) {
    $result = "";
    $quantity=0;
    $type_rum = false;
    $i=0;
    $count=0;
    
    /* FORMATEAR FECHA */
    date_default_timezone_set('America/Santiago');

    /* CONSTRUYENDO EN CSV */
    $result=setInfo($result, $xpath, $information);
    $result=setHeader($result, $type_graphic, $data);
    $result=setBody($result, $type_graphic, $data, $xpath);
    
    return $result;
}

/*Función que convierte la fecha*/
function translateDate($epoch){
    $timestamp = (int) substr($epoch, 0, -3);
    $time = date('Y-m-d H:i:s', $timestamp);
    return $time;
}

/*Función que genera la información del informe*/
function setInfo($result, $xpath, $information){
    $result.= "Nombre Servicio:  " . $xpath->__servicio->nombre . " - " . "Nombre Objetivo: " . $xpath->nombre . "\n\n";
    $result.= "Id Objetivo : " . $xpath->objetivo_id . "\n";
    $result.=($information!=null or $information != "")?"Informacion:".$information."\n":(($data->informacion)?"Informacion: " . $data->informacion . "\n":"Informacion: " . $data[0]->information . "\n");    
    return $result;
}

/*Función que genera la cabezera*/
function setHeader($result, $type_graphic, $data){
    
     if ($type_graphic == 1) {
        $result.="; Tiempo Respuesta Promedio (ms) ; Metrica ; Fecha ; Hora\n";
    }
    elseif($type_graphic == 2 || $type_graphic == 9){
        $result.="; Porcentaje de error de la Aplicacion %; Metrica ; Fecha ; Hora\n";
    }
    elseif($type_graphic == 3){
        $result.="; Indice de Rendimiento de la aplicacion ; Metrica ; Fecha ; Hora\n";
    }
    elseif($type_graphic == 4){
        $result.="; Cantidad  ; HTTP Codigo Respuesta  ; Porcentaje\n";
        foreach ($data->eje_y as $clave => $valor) {
             $quantity = $quantity+$valor;
        }
    }
    elseif($type_graphic == 7){
        $result.="; Browser; Segundos ; Fecha ; Hora\n";
    }
    elseif($type_graphic == 10){
        $result.="; Segundos; Metrica ; Fecha ; Hora\n";
    }
    elseif($type_graphic == 11){
        $result.="; Tiempo De Carga de Pagina en Navegador (s) ; Metrica ; Fecha ; Hora\n";
    }
     elseif($type_graphic == 12){
        $result.="\n";
        $result.="Indice de salud de los objetivos (s)\n";
        $result.="Fecha ; Hora; Indice\n";
    }
     elseif($type_graphic == 13){
       $result.="; Indice Interaccion Promedio ; Componente ; Fecha ; Hora\n";
    }
     elseif($type_graphic == 14){
        $result.="; Cantidad de Lanzamientos ; Sistema Operativo ; Fecha ; Hora\n";
    }
     elseif($type_graphic == 15){
        $result.="; Cantidad de Lanzamientos ; Sistema Operativo ; Fecha ; Hora\n";
    }
     elseif($type_graphic == 16){
        $result.="; Cantidad de Errores ; Sistema Operativo ; Fecha ; Hora\n";
    }
     elseif($type_graphic == 17){
        $result.="; Tiempo interaccion(ms) ; Dispositivo ; Fecha ; Hora\n";
    }
    return $result;
}

/*Función que genera el cuerpo del archivo*/
function setBody($result, $type_graphic, $data, $xpath){
    if($type_graphic==8){
        $result.="\n";
        $result.=";;;;Segundos\n";
        foreach ($data as  $valor) {
            $value=$valor->labelName;
            if($valor->labelName=='pageUrl'){
                $value = 'Url';
                $count=$count+1;
            }
            if($count==2){
                $result.="\n";
                break;
            }
            $value_replace = str_replace('to', 'a', $value );
            $result.= $value_replace.";" ;
        }
        foreach ($data as  $valor) {
            
                if(gettype($valor->data)=='integer'){
                    if($i==0){
                        $result.=$valor->name_url.";".$valor->data.";";
                    }
                    else{
                        $result.=$valor->data.";";
                    }
                    $i=$i+1;
                }
                else{
                    $i=0;
                    $result.="\n";
                }
        }
    }
    elseif($type_graphic == 12){
    	foreach ($data as $clave => $conf_dato) {
        	
        	$fecha= gmdate("Y-m-d H:i:s",substr($conf_dato[0],0,-3));

        	$separator_time = explode(" ", $fecha);
        	$date = $separator_time[0];
        	$hour = $separator_time[1];
        	$result.= $date . ";" .$hour  . ";" .number_format($conf_dato[1],3,'.',',') ."\n";
        }
    }
    elseif($type_graphic!=7 && $type_graphic!=8){
        foreach ($data->eje_y as $clave => $valor) {
            /* Variables para la conversion de epoch a fecha humana */
            $epoch = $data->eje_x[$clave];
            $time_total = translateDate($epoch);
            $separator_time = explode(" ", $time_total);
            $date = $separator_time[0];
            $hour = $separator_time[1];
            if ($type_graphic != 4) {
                $date = $separator_time[0];
                $hour = $separator_time[1];
                $result.= ";" . $valor . ";" . $data->nombre_elementos[$clave] . ";" . $date . ";" . $hour  ."\n";
            } 
            elseif($type_graphic == 4 ){
                $porcentaje = round((($valor*100)/$quantity),1,PHP_ROUND_HALF_UP);
                $result.= ";" . $valor . ";" . $data->nombre_elementos[$clave] .";"."$porcentaje"."%". "\n";
            }
        }
    }
    elseif($type_graphic==7){
        foreach ($data as  $valor) {
            $time_total = translateDate($valor->dateTime);
            $date =  split(" ", $time_total);
            if($valor->labelName!='Time'){
                $result.= ";" . $valor->labelName . "; ".$valor->data. ";" .$date[0]. ";".$date[1]."\n";
            }
        }
    }
    return $result;
}
?>
