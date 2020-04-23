<?

abstract class Utiles {
	
	/**
	 * Funcion RECURSIVA para obtener los nodos hijos de un nodo dado.
	 * El arreglo $arr_nombres es el XPATH, donde el ultimo elemento
	 * es al que se le desean encontrar los hijos.
	 * El nodo $nodo_padre es el nodo donde se empieza a buscar.
	 * 
	 * @param Node $nodo_padre
	 * @param array<String> $arr_nombres
	 * @return Node
	 */
	function getElementsByArrayTagName($nodo_padre,$arr_nombres) {
		$ret = array();
		$nombre = array_shift($arr_nombres);
		if ($nodo_padre->hasChildNodes()) {
		foreach ($nodo_padre->childNodes as $nodo_hijo) {
			if ($nodo_hijo->nodeName==$nombre and count($arr_nombres)==0) {
				$ret[] = $nodo_hijo;
			}
			elseif ($nodo_hijo->nodeName==$nombre) {
				$ret = Utiles::getElementsByArrayTagName($nodo_hijo,$arr_nombres);
			}
		}
		}
		return $ret;
	}

	function formatDuracion($duracion,$saltar=1) {
		$duracion = ereg_replace('\.([0-9]{1,10})', '', $duracion);
		if ($saltar) {
//			$duracion = ereg_replace('([0-9]{2}):([0-9]{2}):([0-9]{2})', '<br>\\0', $duracion);
			$duracion = str_replace(array(" years"," year"," mons"," mon"," days"," day"), array("a","a","m","m","d<br>","d<br>"), $duracion);
			
		}
		else {
			$duracion = str_replace(array(" years"," year"," mons"," mon"," days"," day"), array("a","a","m","m"," día(s)"," día(s)"), $duracion);				
		}
		return $duracion;
	}
	
	function getWidthBar($cnt) {
		$bar_width = array(0 => 3, 1 => 3, 2 => 2, 3 => 1);
		if (isset($bar_width[$cnt])) {
			return $bar_width[$cnt];
		} 
		else {
			return 0.5;
		}
	}
	
	function getDefaultColor($id) {
		
		$arr_colores = array(
					"cccc66","629693","aad5d2","00a49a","e3c08a","f86d91","bdbc72",
					"bedca8","7192d0","de8800","94a3bf","dab3f1","f38d71","866a96",
					"848ab3","cbb6cb","59c7cb","a8c2c3","d3db8f","bf7b8c","aea0a3",
					"f1aca4","a8c3ad","efdfa6","dcd7c6","f1bbbb","c5bbf1","e7cf7b",
					"1251C7","04B404");
				
		if ($id<count($arr_colores)) {
			return $arr_colores[$id];
		}
		else {
			return $arr_colores[$id-(floor($id/count($arr_colores)) * count($arr_colores))];
		}
	}
	
	function getStyleDisponibilidad($id) {
		$arr_style = array(
					1 => "Uptime Global", 
					2 => "Downtime Global", 
					3 => "Downtime Parcial", 
					7 => "No Monitoreo",
					9 => "Marcado Especial");
		
		return $arr_style[$id];
	}
	
	function intervalToSeconds($interval) {
		$interval = str_replace(array(" mon ", " day "), array(" mons ", " days "), $interval);

		$amon = explode(" mons ", $interval);
		if (count($amon) == 1) {
			$day = 0;
			$interval = $amon[0];
		}
		elseif (count($amon) > 1) {
			$day = ($amon[0] * 30);
			$interval = $amon[1];
		}
		else {
			return 0;
		}
		
		$ainterval = explode(" days ", $interval);
		if (count($ainterval) == 1) {
//			$day = 0;
			$time = $ainterval[0];
		}
		elseif (count($ainterval) > 1) {
			$day += $ainterval[0];
			$time = $ainterval[1];
		}
		else {
			return 0;
		}
		
		$atime = explode(":", $time);
		if (count($atime) == 3) {
			return $day * 86400 + $atime[0] * 3600 + $atime[1] * 60 + $atime[2];
		}
		else {
			return $day * 86400;
		}
	}
	
	function getIconMime($mime) {
		$mime_corto = explode(";",$mime);
		$mime_corto = $mime_corto[0];

		$arr_mimetypes = array("-1" => "error.png", 
					"text/html" => "text-html.png", 
					"text/javascript" => "application-javascript.png", 
					"application/x-javascript" => "application-javascript.png", 
					"application/javascript" => "application-javascript.png", 
					"application/x-shockwave-flash" => "application-x-shockwave-flash.png", 
					"text/css" => "text-css.png", 
					"image/gif" => "image-gif.png", 
					"image/jpeg" => "image-jpeg.png", 
					"image/png" => "image-png.png", 
					"image/x-icon" => "image-icon.png",
					"text/xml" => "text-xml.png", 
					"application/xml" => "text-xml.png",
					"text/plain" => "text-plain.png", 
					"" => "blanco.png");
		if($arr_mimetypes[$mime_corto]=='')
			$arr_mimetypes[$mime_corto]="error.png";
		return $arr_mimetypes[$mime_corto];
	}

/*	function getUserAgent() {
		$plataformas = array("Windows" => "Windows", "Linux" => "Linux", "Macintosh" => "Macintosh", "Mobile" => "Mobile", "Android" => "Mobile");

		$useragent = $_SERVER["HTTP_USER_AGENT"];

		$texto_sb = "[\w|\/|\.|\,|\:|\;|\-|\+]*";
		$texto_cb = "[\w|\s|\/|\.|\,|\:|\;|\-|\+]*";
		$pattern = '/^('.$texto_sb.')( \(('.$texto_cb.')\)){0,1}( ('.$texto_sb.')){0,1}( \(('.$texto_cb.')\)){0,1}( ('.$texto_cb.')){0,1}( \(('.$texto_cb.')\)){0,1}( ('.$texto_sb.')){0,1}$/';

		$useragent = str_replace(" U;", "", $useragent);
		
		preg_match($pattern, $useragent, $matches);
		
		if (count($matches) == 0) {
			return null;
		}
		
		$browser = $matches[1];
		$browser_detalles = $matches[3];
		$plataforma = $matches[5];
		$plataforma_detalles = $matches[7];
		$extensiones = (isset($matches[13]))?$matches[13]:$matches[9];

		if ($extensiones != null) {
				
			$arr_extensiones = preg_split('/\s/', $extensiones);
			preg_match("/\/(\d*)\.(\d*)/", $arr_extensiones[0], $versiones);
			$version = $versiones[1].".".$versiones[2];
		
			if (strstr($arr_extensiones[0], "Version") == false) {
				$nombres = preg_split('/\//', $arr_extensiones[0]);
			}
			elseif (strstr($arr_extensiones[0], "Version") != false and count($arr_extensiones) == 1) {
				$nombres = preg_split('/\//', $browser);
			}
			elseif (strstr($arr_extensiones[1], "Mobile") == false) {
				$nombres = preg_split('/\//', $arr_extensiones[1]);
			}
			else {
				$nombres = preg_split('/\//', $arr_extensiones[2]);
			}
			$nombre = $nombres[0];
		}
		
		if ($browser_detalles != null) {
			
			$arr_browser_detalles = preg_split('/;\s/', $browser_detalles);
			
			if (strstr($browser_detalles, "IEMobile") == false) {
				$pos = 1;
			}
			else {
				$pos = 4;
			}
			$arr_datos = preg_split('/[\s|\/]/', $arr_browser_detalles[$pos]);
			$nombre_aux = $arr_datos[0];
				
			if (($nombre == "Safari" and $nombre_aux == "Android") or $extensiones == null) {
				$nombre = $nombre_aux;
				preg_match("/^(\d*)\.(\d*)/", $arr_datos[1], $versiones);
				$version = $versiones[1].".".$versiones[2];
			}
			elseif ($nombre == "Safari" and $nombre_aux == "BlackBerry") {
				$nombre = $nombre_aux;
			}
		}
		
		if (!isset($sistema) or $sistema == "") {
			foreach ($plataformas as $id => $plataforma) {
				if (strpos($useragent, $id) != false) {
					$sistema = $plataforma;
				}
			}
		}
		
		return array("browser" => $nombre, "version" => $version, "plataforma" => $sistema);
		
	}*/
	
	function getCoeficienteCorrelacion($array1, $array2) {
		$sum1 = 0;
		$sum2 = 0;
		$sumsq1 = 0;
		$sumsq2 = 0;
		$psum = 0;
		$n = count($array1);
		
		foreach ($array1 as $i => $value) {
			$sum1 += $array1[$i];
			$sum2 += $array2[$i];
			$sumsq1 += pow($array1[$i], 2);
			$sumsq2 += pow($array2[$i], 2);
			$psum += $array1[$i] * $array2[$i];
		}
		
		$psum1 = $sum1 / $n;
		$psum2 = $sum2 / $n;
		$num = $psum - (($sum1 * $sum2) / $n);
		$den = sqrt(($sumsq1 - pow($sum1, 2) / $n) * ($sumsq2 - pow($sum2, 2) / $n));
		$pen = ($sum1 * $sum2 - $n * $psum1 * $psum2) / ($sumsq1 - pow($sum1, 2));
		
		if ($den == 0) {
			return array("coeficiente" => 0, "ordenada" => 0, "pendiente" => 0);
		}
		else {
			return array("coeficiente" => ($num / $den), "ordenada" => ($psum2 - $pen * $psum1), "pendiente" => $pen);
		}
	}

    /*
     * Pasa array postgres a array php.
     * Código de phppgadmin: ADODB_base#phpArray($dbarr)
     */
    function pg_to_php_array($dbarr)
    {
		// Take off the first and last characters (the braces)
		$arr = substr($dbarr, 1, strlen($dbarr) - 2);

		// Pick out array entries by carefully parsing.  This is necessary in order
		// to cope with double quotes and commas, etc.
		$elements = array();
		$i = $j = 0;		
		$in_quotes = false;
		while ($i < strlen($arr)) {
			// If current char is a double quote and it's not escaped, then
			// enter quoted bit
			$char = substr($arr, $i, 1);
			if ($char == '"' && ($i == 0 || substr($arr, $i - 1, 1) != '\\')) 
				$in_quotes = !$in_quotes;
			elseif ($char == ',' && !$in_quotes) {
				// Add text so far to the array
				$elements[] = substr($arr, $j, $i - $j);
				$j = $i + 1;
			}
			$i++;
		}
		// Add final text to the array
		$elements[] = substr($arr, $j);

		// Do one further loop over the elements array to remote double quoting
		// and escaping of double quotes and backslashes
		for ($i = 0; $i < sizeof($elements); $i++) {
			$v = $elements[$i];
			if (strpos($v, '"') === 0) {
				$v = substr($v, 1, strlen($v) - 2);
				$v = str_replace('\\"', '"', $v);
				$v = str_replace('\\\\', '\\', $v);
				$elements[$i] = $v;
			}
		}

		return $elements;
    }


    /**
     *
     * Estandariza string, para ser usada en URLs.
     *
     * * Quita entidades XML
     * * Translitera hacia caracteres ASCII
     * * Quita caracteres que no son letras
     * * Retorna sólo minúsculas
     *
     */
    static function parameterize($string) {
        $html_entities_decoded = html_entity_decode($string, ENT_QUOTES, "UTF-8");

        $transliterated = iconv("UTF-8", "ASCII//TRANSLIT", $html_entities_decoded);
        if(!$transliterated) {
            $transliterated = $html_entities_decoded;
        }

        $parameterized = preg_replace("/[^a-z0-9\-_]+/i", "-", $transliterated);
        $parameterized = preg_replace("/[\-]{2,}/", "-", $parameterized);
        $parameterized = preg_replace("/^\-|\-$/", "", $parameterized);

        return strtolower($parameterized);
    }

    
    function enviaCorreo($destinatario, $caso, $tipo, $objetivo = false){
		require_once("PHPMailer/class.phpmailer.php");

		if ($caso == 'new_contacto') {
			$mensaje1 = "La presente es para informarle que ha sido agregado a la lista de contactos del Sistema de Alertas de Atentus.";
			$mensaje2 = "Esto significa que usted podrá recibir e-mails con los mensajes de Alerta cada vez que el resultado de un monitoreo coincida con la configuración de alarmas provista.";
			$asunto = "Fue agregado como contacto en el sistema de alertas de Atentus.com";
		}
		elseif ($caso == 'rm_contacto') {
			$mensaje1 = "Le informamos que su nombre de usuario ha sido eliminado de la lista de contactos del Sistema de Alertas de Atentus.";
			$mensaje2 = "Por lo tanto, usted dejará de recibir e-mails con los mensajes de Alerta cuando el resultado de un monitoreo lo requiera.";
			$asunto = "Ha sido eliminado como contacto del sistema de alertas de Atentus.com";
		}	    		 
		elseif ($caso == 'edit_contacto' ){
			$mensaje1 = "La presente es para informarle que el tipo de correo de alerta asignado a esta casilla ha sido cambiado a ".$destinatario->tipo_nombre.".";
			$mensaje2 = "A partir de ahora usted recibirá las nuevas alertas de Atentus en su bandeja de correo electrónico.";
			$asunto = "Sus datos han sido modificados en el sistema de alertas de Atentus.com";
		}
		if ($caso == 'new_alarma') {
			$mensaje1 = "La presente es para informarle que se ha creado una nueva alarma para el objetivo ".$objetivo->nombre." en el Sistema de Alertas de Atentus.";
			$mensaje2 = "";
			$asunto = "Ha sido creada una nueva alarma en el sistema de reportes Atentus.com";
		}
		elseif ($caso == 'rm_alarma') {
			$mensaje1 = "Le informamos que la alarma asociada al objetivo ".$objetivo->nombre." ha sido eliminada del Sistema de Alertas de Atentus.";
			$mensaje2 = "Por lo tanto, usted dejará de recibir e-mails con los mensajes de Alerta cuando el resultado de un monitoreo lo requiera.";
			$asunto = "Ha sido eliminada una alarma en el sistema de alertas de Atentus.com";
		}
		
		$fecha = Utiles::cliente_tz();
		
		$mail = new PHPMailer();
		$mail->Host = "atentus.com";
		$mail->SMTPAuth = true;
		$mail->Sender = REMITENTE;
		$mail->CharSet = 'UTF-8';
		$mail->AddAddress($destinatario->contacto);
		$mail->Subject = $asunto;
		$mail->From = REMITENTE;
		$mail->FromName = "Soporte Atentus";
		 
		$T = & new Template_PHPLIB(REP_PATH_TEMPLATES);
		if ($tipo==2) {
			$T->setFile('tpl_contenido', 'correo_notificacion.tpl');
			$mail->IsHTML(true);
			$mail->AddEmbeddedImage('img/header_transparente.png', 'imagen','image/png');
//			$mail->AltBody = $mensaje1."\\n".$mensaje2;
		}
		else {
			$T->setFile('tpl_contenido', 'correo_notificacion_plano.tpl');
		}

		$T->setVar('__tel_arg', TEL_ARG);
		$T->setVar('__tel_chi', TEL_CHI);
		$T->setVar('__tel_per', TEL_PER);
		$T->setVar('__tel_col', TEL_COL);
		$T->setVar('__tel_uru', TEL_URU);
		$T->setVar('__mensaje1', $mensaje1);
		$T->setVar('__mensaje2', $mensaje2);
		$T->setVar('__nombre', $destinatario->nombre);
		$T->setVar('__tipo', $destinatario->contacto);
		$T->setVar('__fecha', date("d-m-Y", strtotime($fecha)));
		$T->setVar('__hora', date("H:i:s", strtotime($fecha)));
		
		$mail->Body = $T->parse('out', 'tpl_contenido');
		
		if(!$mail->Send()) {
			echo "fallo_el_envio: " . $mail->ErrorInfo;
		}
    }

    function cliente_tz() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		$sql = "select * FROM public._to_cliente_tz(".$current_usuario_id.",now()) as fecha";
		
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql,$res->userinfo);		
			return false;			
		}
		if ($row = $res->fetchRow()) {
			return $row["fecha"]; 
		}
    }

    function busca_usuario($objetivo_id) {
		global $mdb2;
		global $log;

		$sql = "SELECT * FROM public.busca_usuario_por_objetivo(".$objetivo_id.") as foo";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if ($row = $res->fetchRow()) {
			return $row["foo"];
		}
		else{
			return false;
		}
	}	

	/* Función que covierte fecha en utc. */
	function convertDateUtc($dateCalendar, $timeZone){
		date_default_timezone_set($timeZone);
		$date = strtotime($dateCalendar);
		date_default_timezone_set("UTC");
		return date("Y-m-d H:i:s O",$date);
	}

	/* Función que convierte según zona horaria*/
	function convertDateTimeZone($date, $timeZone){
		$dateConvert = new DateTime($date);
		$dateConvert->setTimezone(new DateTimeZone($timeZone));
		$dateConvert->setTimezone(new DateTimeZone("UTC"));
		return $dateConvert->format("Y-m-d H:i:s");
	}
	/* Función que convierte según zona horaria*/
	function dateTimeZone($timeZone){
		date_default_timezone_set($timeZone);
		$date = date("Y-m-d H:i:s");
		date_default_timezone_set("UTC");
		
		return $date;
	}
	/* Función que permite obtener la zona horaria a travez del id*/
	function getNameZoneHor($zona_horaria_id){
		global $mdb2;
		global $log;
		$sql = "SELECT * FROM public.zona_horaria WHERE activo='t' and zona_horaria_id='$zona_horaria_id' ORDER BY nombre" ;
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$zonas_horarias = array();
		while($row = $res->fetchRow()) {
			$zonas_horarias[$row["zona_horaria_id"]] = $row["valor"]; 
		}
		return $zonas_horarias;
	}

}
?>