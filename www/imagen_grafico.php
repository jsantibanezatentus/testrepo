<?
session_start();

/* GUARDO EL CODIGO DE LA IMAGEN EN UNA VARIABLE DE SESION */
if (isset($_REQUEST["src"])) {
//	$codigo = str_replace(array("CARACTERSUMA"), array("+"), $_REQUEST["src"]);
	$_SESSION[$_REQUEST["nombre"]] ='';
	$_SESSION[$_REQUEST["nombre"]] = $_REQUEST["src"];	 
//	echo $_SESSION[$_REQUEST["nombre"]];	
	die();
}

/* GENERO LA IMAGEN CON LOS DATOS GUARDADOS EN LA SESSION */
else {
        ob_clean();
    
	$nombre = $_REQUEST["nombre"];
	$codigo = str_replace(array("CARACTERSUMA"), array("+"), $_SESSION[$nombre]);
	if($_REQUEST["nombre"]=="imagen_elemento_plus"){
		header("Content-type:image/svg+xml");
	}
	else{
		header("Content-type:image/png");
	}
	echo base64_decode($codigo);	
	die();
}
?>