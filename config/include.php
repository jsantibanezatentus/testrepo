<?

/* INCLUIR LIBRERIAS PHP */
require_once('MDB2.php');
require_once('Auth.php');
require_once('HTML/Template/PHPLIB.php');
require_once('PHPExcel.php');
require_once('PHPExcel/IOFactory.php');
require_once('PHPExcel/Cell/DataType.php');
require_once('PHPExcel/Style/Conditional.php');
require_once('PHPExcel/Style/Color.php');
require_once'PHPWord/PHPWord.php';

/* INCLUIR ARCHIVOS DE CONFIGURACION */
include("connection.php");
include("common.php");
include("session.php");  // VALIDAR DESPUES

/* INCLUIR CLASES DEL SISTEMA */
include(REP_PATH_CLASS_COMMON."Timestamp.php");
include(REP_PATH_CLASS_COMMON."Utiles.php");
include(REP_PATH_CLASS_COMMON."Constantes.php");
//include(REP_PATH_CLASS_COMMON."GAPI.php");
include(REP_PATH_CLASS_COMMON."Log.php");
include(REP_PATH_CLASS_COMMON."Validador.php");

include(REP_PATH_CLASS_REPORT."Reporte.php");
include(REP_PATH_CLASS_REPORT."ReporteItem.php");
include(REP_PATH_CLASS_REPORT."XMLParserReporte.php");
include(REP_PATH_CLASS_REPORT."Grafico.php");
include(REP_PATH_CLASS_REPORT."GraficoFlash.php");
include(REP_PATH_CLASS_REPORT."GraficoSVG.php");
include(REP_PATH_CLASS_REPORT."Tabla.php");
include(REP_PATH_CLASS_REPORT."Planilla.php");
include(REP_PATH_CLASS_CONFIG."evento.php");
				
		

//include(REP_PATH_CLASS_REPORT."DatosAnalytics.php");
//include(REP_PATH_CLASS_REPORT."DatosGA.php");

include(REP_PATH_CLASS_DATA."DatosObjetivo.php");
include(REP_PATH_CLASS_DATA."DatosGrupo.php");
include(REP_PATH_CLASS_DATA."DatosPatron.php");
include(REP_PATH_CLASS_DATA."DatosExcluida.php");
include(REP_PATH_CLASS_DATA."DatosMonitor.php");
include(REP_PATH_CLASS_DATA."DatosPaso.php");
include(REP_PATH_CLASS_DATA."DatosEvento.php");
include(REP_PATH_CLASS_DATA."DatosRegistro.php");
include(REP_PATH_CLASS_DATA."DatosHorarioHabil.php");
include(REP_PATH_CLASS_DATA."DatosElemento.php");
include(REP_PATH_CLASS_DATA."DatosPeriodo.php");
include(REP_PATH_CLASS_DATA."DatosMonitoreo.php");
include(REP_PATH_CLASS_DATA."DatosSecuencia.php");
//include(REP_PATH_CLASS_DATA."DatosSubObjetivo.php");
include(REP_PATH_CLASS_DATA."DatosRegistroPlus.php");
include(REP_PATH_CLASS_DATA."DatosPonderacion.php");


include(REP_PATH_CLASS_CONFIG."NotificacionModal.php");
include(REP_PATH_CLASS_CONFIG."Usuario.php");
include(REP_PATH_CLASS_CONFIG."Seccion.php");
//include(REP_PATH_CLASS_CONFIG."Grupo.php");
include(REP_PATH_CLASS_CONFIG."SubCliente.php");
include(REP_PATH_CLASS_CONFIG."Servicio.php");
include(REP_PATH_CLASS_CONFIG."TokenWS.php");
include(REP_PATH_CLASS_CONFIG."Objetivo.php");
include(REP_PATH_CLASS_CONFIG."ConfigObjetivo.php");
include(REP_PATH_CLASS_CONFIG."ConfigEspecial.php");
include(REP_PATH_CLASS_CONFIG."DnsSetup.php");
include(REP_PATH_CLASS_CONFIG."MailSetup.php");
include(REP_PATH_CLASS_CONFIG."Paso.php");
include(REP_PATH_CLASS_CONFIG."PasoSetup.php");
include(REP_PATH_CLASS_CONFIG."Patron.php");
include(REP_PATH_CLASS_CONFIG."Ponderacion.php");
include(REP_PATH_CLASS_CONFIG."PonderacionItem.php");
include(REP_PATH_CLASS_CONFIG."Monitor.php");
include(REP_PATH_CLASS_CONFIG."Nodo.php");
include(REP_PATH_CLASS_CONFIG."Horario.php");
include(REP_PATH_CLASS_CONFIG."HorarioItem.php");
include(REP_PATH_CLASS_CONFIG."Notificacion.php");
include(REP_PATH_CLASS_CONFIG."Destinatario.php");
//include(REP_PATH_CLASS_CONFIG."CuentaGA.php");
include(REP_PATH_CLASS_CONFIG."TypeEspecial.php");


/* LOG */
$log = new Log();

/* CONEXION CON DB */
$i = 1;
foreach ($arr_dsn as $dsn) {
	$mdb2 =& MDB2::connect($dsn);
	if (MDB2::isError($mdb2)) {
		if (count($arr_dsn) == $i) {
			$log->setSyslog($mdb2->getMessage(), $mdb2->userinfo);
			exit();
		}
	}
	else {
		$mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC);
		$mdb2->setOption('debug', 2);
		
		$sql = "SELECT pg_is_in_recovery()";
		$res =& $mdb2->query($sql);
		if ($row = $res->fetchRow()) {
			$mdb2->only_read = ($row["pg_is_in_recovery"]=="t")?1:0;
		}
		break;
	}
	$i++;
}

?>