<?php
    $obj_id =$_REQUEST["obj_id"];
    $fecha  =$_REQUEST["fecha"];
    $url = REP_CDN_HOST."/lista_imagenes_old.php?obj_id=".$obj_id."&fecha=".$fecha;
    $json = file_get_contents($url);
    $data_imagenes = json_decode($json, true);
    
    echo "<html>";
    echo "<head> <script src='https://code.jquery.com/jquery-1.11.3.min.js'></script>";
    echo "</head>";
    echo "<body>";

    $index = 0;
    foreach ($data_imagenes as $imagen)
    {
     echo "<div data='cdn' id='".$imagen['hash_md5']."'>img".$index."</div>";
     echo "</br>";
     
     $index++;
    }

    echo "</body>";
    echo "</html >";

?>