<?php

function conectarbd(){


    $host= "host=localhost";
    $port="port=5432";
    $dbname="dbname=usuario";
    $user="user=postgres";
    $password="password=root";
    
    $conn =pg_connect("$host $port $dbname $user $password");
    if (!$conn){
        echo "Erro".pg_last_error;
    }else{
        echo "<h3>Conexion exitosa php </h3><hr>";
     
        
        return $conn;
    }
    }
    


require_once 'conexion.php';



$conn= conectarbd();

 $usser = $_POST['usuario'];
 $contrasena= $_POST['contrasena'];

$result = pg_query($conn, "SELECT id,  usser , contrasena   FROM 
datos where  usser = '$usser' and  contrasena = '$contrasena'");



$login_check = pg_num_rows($result);

if($login_check > 0){
    
    while($row = pg_fetch_array($result)){
        foreach ($row as $key => $val){
            $key = stripslashes( $val );
        }
        echo 'Usuario : ';
        echo "<br>";
        echo $_SESSION['usser'] = $row['usser'];
        header('Location: template.php');
        
      //  echo $_SESSION['contrasena'] = $row['contrasena'];
    
            
    }

    
}else{



    header('Location: index.html');

}



    ?>