<?

date_default_timezone_set('UTC');

/* LEE ARCHIVO EXTERNO DE CONFIGURACIÓN */
$app_config = parse_ini_file("application.ini", false);
define('REP_CDN_HOST', $app_config["cdn_host"]);
define('REP_CDN_IVR', $app_config["cdn_ivr"]);
define('REP_API_IVR', $app_config["api_ivr"]);
define('REP_CDN_MOBILE', $app_config["cdn_mobile"]);
define('REP_CDN_ELEMENTOS', $app_config["cdn_elementos"]);
define('REP_API_INTERVAL', $app_config["api_interval"]);
define('REP_API_HOST', $app_config["api_host"]);
define('REP_API_HOST3', $app_config["api_host3"]);
define('REP_DOMINIO', $app_config["domain"]);
define('REP_LOCALHOST_DOMINIO', $app_config["domain_localhost"]);
define('VERSION', "2.84.9");

/* TRACKIND ID PARA GOOGLE ANALYTICS */
define('REP_GA_TRACKING_ID', $app_config["google_analytics_tracking_id"]);

/* PATH DE TEMPLATES */
define('REP_PATH_ROOT_DIR', $app_config["root_path"]);
define('REP_PATH_TEMPLATES', REP_PATH_ROOT_DIR . 'tpl/html/');
define('REP_PATH_TABLETEMPLATES', REP_PATH_ROOT_DIR . 'tpl/table/');
define('REP_PATH_PRINTTEMPLATES', REP_PATH_ROOT_DIR . 'tpl/print/');
define('REP_PATH_XHTMLTEMPLATES', REP_PATH_ROOT_DIR . 'tpl/xhtml/');
define('REP_PATH_XMLTEMPLATES', REP_PATH_ROOT_DIR . 'tpl/xml');
define('REP_PATH_ESPECIALTEMPLATES', REP_PATH_ROOT_DIR . 'tpl/especiales/');

/* PATH HERRAMIENTAS */
define('REP_PATH_CGI', $app_config["ext_cgi_path"]);
define('REP_PATH_TOOLS', REP_PATH_CGI . 'attools/');
define('REP_PATH_HTMLTOPDF', REP_PATH_CGI . 'wkhtmltopdf/' . $app_config["html_to_pdf_app_path"]);
define('REP_PATH_HTMLTOJPG', REP_PATH_CGI . 'wkhtmltoimage/' . $app_config["html_to_jpg_app_path"]);

//PATH TELEFONOS PAISES
define('TEL_ARG', $app_config["arg_tel"]); //ARGENTINA
define('TEL_CHI', $app_config["chi_tel"]); //CHILE
define('TEL_PER', $app_config["per_tel"]); //PERU
define('TEL_COL', $app_config["col_tel"]); //COLOMBIA
define('TEL_URU', $app_config["uru_tel"]); //URUGUAY

define('remote__addr', $_SERVER['REMOTE_ADDR']); //JSN
define('http__client__ip', $_SERVER['HTTP_CLIENT_IP']); //JSN



/* PATH ELEMENTOS WWW */
define('REP_PATH_IMG', 'img/');
//define('REP_PATH_IMG_CODIGO', 'img/codigos/');
define('REP_PATH_IMG_SEMAFORO', 'img/semaforo/');
define('REP_PATH_SPRITE_SEMAFORO', 'spriteSemaforo spriteSemaforo-');
define('REP_PATH_SPRITE_CODIGO', 'sprite sprite-');
define('REP_PATH_IMG_BOTONES', 'img/botones/');
define('REP_PATH_IMG_MIMETYPES', 'img/mimetypes/');
define('REP_PATH_CSS', 'css/');
define('REP_PATH_JS', 'js/');
define('REP_PATH_TMP', 'tmp/');

/* PATH CLASES */
define('REP_PATH_CLASS', '../class/');
define('REP_PATH_CLASS_COMMON', '../class/common/');
define('REP_PATH_CLASS_REPORT', '../class/report/');
define('REP_PATH_CLASS_CONFIG', '../class/config/');
define('REP_PATH_CLASS_DATA', '../class/data/');
define('REP_PATH_CONTROLLER', '../controller/');

/* PATH LIBRERIAS EXTERNAS */
define('REP_PATH_LIBRERIAS_EXTERNAS', $app_config["ext_lib_path"]);
define('REP_PATH_JQUERY_UI', REP_PATH_LIBRERIAS_EXTERNAS.'jquery/');
define('REP_PATH_DATA_TABLE', REP_PATH_LIBRERIAS_EXTERNAS.'data-table/');
define('REP_PATH_MOMENT', REP_PATH_LIBRERIAS_EXTERNAS.'moment/');
define('REP_PATH_FULL_CALENDAR', REP_PATH_LIBRERIAS_EXTERNAS.'full_calendar/');

/*Extrae version de navegador*/
$pos_chrome = strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome/');
$version_chrome = (int)substr($_SERVER['HTTP_USER_AGENT'], $pos_chrome+7,2);

if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) {
	if($version_chrome >= 51){
    	define('REP_PATH_DOJO', REP_PATH_LIBRERIAS_EXTERNAS . 'dojo/');
	}elseif($version_chrome <= 51){
	    define('REP_PATH_DOJO', REP_PATH_LIBRERIAS_EXTERNAS . 'dojo_old/');
	}
}elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident')) {
	define('REP_PATH_DOJO', REP_PATH_LIBRERIAS_EXTERNAS . 'dojo_old/');
}else{
	define('REP_PATH_DOJO', REP_PATH_LIBRERIAS_EXTERNAS . 'dojo/');
}

define('REP_PATH_WAVESURFER', REP_PATH_LIBRERIAS_EXTERNAS . 'wavesurfer/dist/wavesurfer.min.js');
define('REP_PATH_JSCHART', REP_PATH_LIBRERIAS_EXTERNAS . 'anychart/js/AnyChart.js');
define('REP_PATH_ACHART', REP_PATH_LIBRERIAS_EXTERNAS . 'anychart/swf/AnyChart.swf');
define('REP_PATH_AGANTT', REP_PATH_LIBRERIAS_EXTERNAS . 'anygantt/swf/AnyGantt.swf');
define('REP_PATH_HIGHCHARTS', REP_PATH_LIBRERIAS_EXTERNAS . 'highcharts/');
define('REP_PATH_DISPONIBILIDAD', REP_PATH_LIBRERIAS_EXTERNAS . 'disponibilidad/');
define('REP_PATH_AMCHARTS', REP_PATH_LIBRERIAS_EXTERNAS . 'amcharts');


/* FORMATO FECHA */
/*define('REP_FORMAT_DATE', 'Y-m-d');
define('REP_FORMAT_TIME', 'H:i:s');*/

/* PERIODOS DE BUSQUEDA DE REPORTES */
define('REP_PRD_ULT24H', 'dia');
define('REP_PRD_DAY', 'dia');
define('REP_PRD_WEEK', 'semana');
define('REP_PRD_MONTH', 'mes');

/* PRIVILEGIOS DE PERFIL */
/*define('REP_PRIV_NONE', '-');
define('REP_PRIV_READ', 'r');
define('REP_PRIV_WRITE', 'w');*/

/* SERVICIOS (EX-PROTOCOLOS) */
define('REP_PROT_HTTP', 1);
define('REP_PROT_HTTP_FULL', 6);
define('REP_PROT_DNS_SOA', 11);
define('REP_PROT_DNS_A', 12);
define('REP_PROT_DNS_MX', 13);
define('REP_PROT_DNS_CHAOS', 14);
define('REP_PROT_SMTP', 25);
define('REP_PROT_POP', 27);
define('REP_PROT_ECOMMERCE', 55);
define('REP_PROT_WEBCHECK', 60);
define('REP_PROT_MAILTRAFFIC', 62);
define('REP_PROT_TRANSACTION', 70);
define('REP_PROT_ECOMMERCE_VIRTUAL', 71);
define('REP_PROT_WEBSERVICES', 101);
define('REP_PROT_BROWSER_HTTP', 201);
define('REP_PROT_BROWSER_HTTP_FULL', 206);
define('REP_PROT_BROWSER_ECOMMERCE', 255);
define('REP_PROT_BROWSER_ECOMMERCE_CC', 256);
define('REP_PROT_BROWSER_ECOMMERCE_BANCO', 258);
define('REP_PROT_DIGIPASS', 257);
define('REP_PROT_BROWSER_TRANSACTION', 270);
define('REP_PROT_BROWSER_SCREENSHOT', 271);
define('REP_PROT_BROWSER_TRANSACTION_BANCO', 272);
define('REP_PROT_USSD', 400);
define('REP_PROT_IVR', 666);
define('REP_PROT_MOBILE', 700);
/*SE AGREGA LA NUEVA VARIABLE PARA NEW RELIC APM RUM MOBILE*/
define('REP_PROT_NEW_RELIC', 810);
define('REP_PROT_NEW_RELIC_RUM', 811);
define('REP_PROT_NEW_RELIC_MOBILE', 812);
define('REP_PROT_AUDEX', 801);
define('REP_PROT_ATDEX', 802);
//define('REP_PROT_PRECIOS', 600);

/* GRUPOS DE SERVICIOS SEGUN XML SETUP */
define('REP_SETUP_DNS', 1);
define('REP_SETUP_MAIL', 2);
define('REP_SETUP_WEB', 3);
define('REP_SETUP_BROWSER', 4);
define('REP_SETUP_MOBILE', 5);
define('REP_SETUP_IVR', 6);
/*SE AGREGA LA NUEVA VARIABLE  NEW RELIC PARA EL SETUP RUM APM*/
define('REP_SETUP_NEW_RELIC', 7);
define('REP_SETUP_NEW_RELIC_RUM', 8);
define('REP_SETUP_AUDEX', 9);
define('REP_SETUP_NEW_RELIC_MOBILE', 10);
define('REP_SETUP_ATDEX', 11);

/* SECCIONES DEL REPORTE */
define('REP_SECCION_NOTIFICACION', 39);
define('REP_SECCION_NOTIFICACION_DESTINATARIO', 43);
define('REP_SECCION_NOTIFICACION_HORARIO', 41);
define('REP_SECCION_OBJETIVO', 36);
define('REP_SECCION_OBJETIVO_TODOS', 88);
define('REP_SECCION_OBJETIVO_GRUPO', 37);
define('REP_SECCION_OBJETIVO_HORARIO', 38);
define('REP_SECCION_OBJETIVO_MANTENCION', 97);
define('REP_SECCION_OBJETIVO_PONDERACION', 94);
define('REP_SECCION_HORARIO', 33);
define('REP_SECCION_USUARIO', 31);
define('REP_SECCION_USUARIO_SUBCLIENTE', 32);
define('REP_SECCION_USUARIO_MIPERFIL', 53);
//define('REP_SECCION_USUARIO_GA', 65);
define('REP_SECCION_USUARIO_TOKENS', 93);

/* TIPOS DE HORARIOS */
define('REP_HORARIO_TODOS', 0);
define('REP_HORARIO_HABIL', 5);
define('REP_HORARIO_NOTIFICACION', 6);
define('REP_HORARIO_MANTENCION', 7);
define('REP_HORARIO_ASIGNABLE', 9999);

/* TIPOS DE HERRAMIENTAS */
define('REP_ATTOOL_PING', 60);
define('REP_ATTOOL_DIG', 61);
define('REP_ATTOOL_TRACE', 62);
define('REP_ATTOOL_FULL', 63);

/* TIPOS DE INTERVALOS */
define('REP_INTERVALO_MONITOREO', 1);
define('REP_INTERVALO_NOTIFICACION', 2);
define('REP_INTERVALO_SEMAFORO', 3);

/* INDICA A QUIEN PERTENECE LOS DATOS BUSCADOS
 * LOS DATOS PUEDEN SER USUARIOS, OBJETIVOS, SUBCLIENTES, ETC */
define('REP_DATOS_USUARIO', 1);
define('REP_DATOS_CLIENTE', 2);
//define('REP_DATOS_ANALYTICS', 3);
define('REP_DATOS_PERIODICOS', 4);
//define('REP_DATOS_STRESS', 5);
//define('REP_DATOS_PRECIO', 6);
define('REP_DATOS_NOTIFICACION', 7);
define('REP_DATOS_ESPECIALES', 8);
define('REP_DATOS_MONITOREO', 9);
/*CONSTANTE PARA NUEVO SERVICIO DE NEW RELIC APM y RUM*/
define('REP_DATOS_NEW_RELIC_APM', 10);
define('REP_DATOS_NEW_RELIC_RUM', 11);
define('REP_DATOS_AUDEX', 12);
define('REP_DATOS_NEW_RELIC_MOBILE', 13);
define('REP_DATOS_ATDEX', 14);

/* INFORMES DEL REPORTE (QUE TIENEN EXCEPCIONES) */
define('REP_INFORME_ONLINE', 9);
//define('REP_INFORME_ONLINE_ELEMENTOS', 44);
//define('REP_INFORME_ONLINE_ELEMENTOS_PLUS', 90);
//define('REP_INFORME_ONLINE_DATOS', 68);
//define('REP_INFORME_STRESS_SUBOBJETIVOS', 75);


define('REP_ITEM_EVENTOS', 21);
define('REP_ITEM_ELEMENTOS', 26);
define('REP_ITEM_ELEMENTOS_PLUS', 64);

/* INFORMES DE ATPRECIOS */
//define('REP_INFORME_PRECIOS_COMPARACION_GENERAL', 35);
//define('REP_INFORME_PRECIOS_ESTADISTICA_POR_PRODUCTO', 36);

define('REP_IMPRESION_INFORME', 1);
define('REP_IMPRESION_OBJETIVO', 2);

define('REP_MOSTRAR_DISPONIBLES_MINIMO', 10);

define('REP_GRAFICO_ANCHO', 725);

define('REMITENTE','soporte@atentus.com');

/* ARREGLO QUE INDICA EL CONTROLADOR SHOW
/* Variables globales para mantenimiento*/
define('SECCION_MANTENIMIENTO',128);# Sección padre mantenimiento
define('SUB_SECCION_MANTENIMIENTO',129);# Sub-Sección - Calendario.
define('SUB_SECCION_MANTENIMIENTO_HISTORIAL',130);# Sub-Sección - Historial.
define('SUB_SECCION_MANTENIMIENTO_AGREGAR_EVENTO',131);# Sub-Sección - Agregar Evento.


/* ARREGLO QUE INDICA EL CONTROLADOR SHOW 
 * QUE SE EJECUTARA PARA CADA SECCION */
$cod_pagina_show = array("1" => "showReportes.php",
						"2" => "showNotificaciones.php",
						"3" => "showHorarios.php",
						"4" => "showUsuarios.php",
						"6" => "showObjetivos.php",
						"30" => "showReportes.php",
                        "57" => "showHerramientas.php",
                        "128" => "showMantenedor.php",
                        );

/* ARREGLO QUE INDICA EL CONTROLADOR ACTION
 * QUE SE EJECUTARA PARA CADA SECCION */
$cod_pagina_action = array("1" => "actionReportes.php",
						"2" => "actionNotificaciones.php",
						"3" => "actionHorarios.php",
						"4" => "actionUsuarios.php",
						"6" => "actionObjetivos.php",
						"30" => "actionReportes.php",
                        "57" => "showHerramientas.php",
						);

/* ARREGLO QUE INDICA LOS DIAS DE LA SEMANA */
$dias_semana = array("1" => "Lunes",
					 "2" => "Martes",
					 "3" => "Miercoles",
					 "4" => "Jueves",
					 "5" => "Viernes",
					 "6" => "Sabado",
					 "7" => "Domingo"
					 );

/* ARREGLO QUE INDICA LOS MESES DEL AÑO */
$meses_anno = array("1" => "Enero",
					"2" => "Febrero",
					"3" => "Marzo",
					"4" => "Abril",
					"5" => "Mayo",
					"6" => "Junio",
					"7" => "Julio",
					"8" => "Agosto",
					"9" => "Septiembre",
					"10" => "Octubre",
					"11" => "Noviembre",
					"12" => "Diciembre"
					);

/* ARREGLO QUE INDICA LAS CLASIFICAIONES DE ITEM DE HORARIO */
$filtros_item = array("1" => "Entre Fechas",
					  "2" => "Fecha Especifica",
					  "3" => "Dia de la Semana"
					  );

/* ARREGLO QUE INDICA LOS TIPOS DE ITEM DE HORARIO */
$tipos_item = array("1" => "Incluido",
					"0" => "Excluido"
					);

$intervalos_dia = array(1 => "1 hora",
						2 => "2 horas",
						3 => "3 horas",
						4 => "4 horas",
						6 => "6 horas",
						8 => "8 horas",
						12 => "12 horas",
						0 => "Personalizado"
						);

$header_type = array("xls" => "application/vnd.ms-excel",
					 "xlsx" => "application/vnd.ms-excel",
					 "csv" => "application/CSV",
					 "docx" => "application/vnd.ms-word",
					 );
?>

