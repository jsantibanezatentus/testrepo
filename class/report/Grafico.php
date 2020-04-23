<?
class Grafico {

	public $objetivo_id;
//	public $subobjetivo_id;
	public $horario_id;
	public $timestamp;
	public $extra;

	public $tiempo_expiracion;
	public $ancho;
	public $alto;
	public $solicitud;
	public $resultado;

	public function Grafico() {
		$this->tiempo_expiracion = 86400;
		$this->resultado = $this->__generarMensajeError();
	}

	public function getDisponibilidadSemaforo() {}
	public function getConsolidadoDisponibilidad() {}
	public function getDetalladoDisponibilidad() {}
	public function getDetalladoDisponibilidadEspecial() {}
	public function getDetalladoDisponibilidadFlexible() {}
	public function getDisponibilidadConsolidadoEspecial() {}
	public function getHistoricoDisponibilidad() {}
	public function getHistoricoDisponibilidadFlexible() {}
	public function getHistoricoDisponibilidadPasoFlexible() {}
	public function getErroresDisponibilidad() {}
	public function getConsolidadoRendimiento() {}
	public function getEstadisticoRendimiento() {}
	public function getHistoricoRendimiento() {}
	public function getFrecuenciaRendimiento() {}
	public function getFrecuenciaAculumada() {}
	public function getRendimientoPorDia() {}
	public function getRendimientoSlaSuavisado() {}
	public function getRendimientoSlaReal() {}
	public function getSLAHistoricoRendimiento() {}
	public function getVisitas() {}
	public function getCorrelacion() {}
	public function getComparativo() {}
	public function getDetalleElementoPlus() {}
	public function getConsolidadoDisponibilidadSimple() {}
	public function getPaginasYTiempo(){}
	public function getConsolidadoDisponibilidadMonitor() {}
	public function getAudex() {}
	public function getAtdex() {}
	public function getEspecialErroresPorNodo() {}
	public function getEspecialDisponibilidadFullObjetivos() {}
	public function getEspecialDisponibilidadObjetivoPaso() {}
	public function getEspecialUptimePonderadaPorItem() {}
	public function getEspecialDisponibilidadGlobalResumen() {}
	public function getRendimientoApi() {}
	public function getConsolidadoRendimientoOnline() {}
	public function getDisponibilidadHora(){}
	public function getComparativoBanco(){}
	public function getComparativoBancoReal(){}
	public function getPerformance(){}
	public function getSupervielleVR(){}
	public function getComparativoBancoChile(){}
	public function getComparativoAlias(){}
//	public function getEspecialRendimientoEjecutivo() {}
	// METODOS NUEVOS PARA NEW RELIC
	public function get_tiempo_respuesta_xy(){}
        public function get_tasa_errores_xy(){}
        public function get_apdex_puntuacion(){}
        public function get_tipo_error_torta(){}
        public function get_tiempo_respuesta_mas_elevado(){}
        public function get_iframe(){}
        public function getAvgLoadTimeBrowser(){}
        public function getLoadTimeBrowserXy(){}
        public function getLoadUrl(){}
        public function getErrorRateJS(){}
        public function getResponseAjax(){}
        // METODOS PARA APP MOBILE
        public function getAvgInteractionApp(){}
        public function getUseVersionSO(){}
        public function getTimeResponseHTTP(){}
        public function getNumberErrors(){}
        public function getTimeInteractionEnabledDevice(){}

	public function generarResultado($es_pdf) {
		if (method_exists($this, $this->solicitud)) {
			$metodo_nombre = $this->solicitud;
                        if($es_pdf==true){
                            $this->$metodo_nombre($es_pdf);
                        }
                        else{
                            $es_pdf==false;
                            $this->$metodo_nombre($es_pdf);
                        }
		}
	}

	public function __generarMensajeError() {
		$T = & new Template_PHPLIB(REP_PATH_TEMPLATES);
		$T->setFile('tpl_tabla', 'sorry_item.tpl');
		$T->setVar('__path_img', REP_PATH_IMG);
		return $T->parse('out', 'tpl_tabla');
	}

	public function getDatosDisponibilidadSemaforo() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.disponibilidad_detalle_semaforo(".
				pg_escape_string($current_usuario_id).")";
//		print($sql);

		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql,$res->userinfo);
			exit();
		}

		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['disponibilidad_detalle_semaforo']);
			$xpath = new DOMXpath($dom);
			unset($row["disponibilidad_detalle_semaforo"]);
			return $xpath;
		}
		else {
			return null;
		}
	}

	public function getDatosDisponibilidadGlobal() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.disponibilidad_global(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
// 		print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['disponibilidad_global']);
			$xpath = new DOMXpath($dom);
			unset($row["disponibilidad_global"]);
			return $xpath;
		}
		else{
			return null;
		}
	}


	public function getDatosConsolidadoDisponibilidad($tipo = null) {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		$nuevo_usuario=$current_usuario_id;

		if($tipo=='disp_simple'){
			//VALIDA QUE EL USUARIO TENGA ACCESO AL OBJETIVO
			$sql0="SELECT DISTINCT su.cliente_usuario_id
			FROM cliente_usuario u, cliente_subcliente s, cliente_mapa_subcliente_objetivo so, cliente_mapa_subcliente_usuario su
			WHERE u.cliente_usuario_id=".$current_usuario_id." AND s.cliente_id=u.cliente_id AND s.cliente_subcliente_id=so.cliente_subcliente_id AND s.cliente_subcliente_id=su.cliente_subcliente_id AND so.objetivo_id=".$this->objetivo_id." LIMIT 1;";

			$res0 = & $mdb2->query($sql0);
			if (MDB2::isError($res0)) {
				exit();
			}
			if ($row0 = $res0->fetchRow()) {
				$nuevo_usuario=$row0["cliente_usuario_id"];
				unset($row["cliente_usuario_id"]);
			}
		}

		if(count($nuevo_usuario)>0){
			/* OBTENER LOS DATOS Y PARSEARLO */
			if ($_REQUEST['multi_obj'] < 1) {
				$sql = "SELECT * FROM reporte.disponibilidad_detalle_consolidado(";
			}else{
				$sql = "SELECT * FROM reporte.disponibilidad_detalle_consolidado_habil(";
			}

			$sql = $sql.
					pg_escape_string($nuevo_usuario).", ".
					pg_escape_string($this->objetivo_id).", ".
					pg_escape_string($this->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
 			 	$dom->preserveWhiteSpace = FALSE;

 			 	if ($_REQUEST['multi_obj'] < 1) {
 			 		$proc_alm  = "disponibilidad_detalle_consolidado";
				}else{
					$proc_alm  = "disponibilidad_detalle_consolidado_habil";
				}
				$dom->loadXML($row[$proc_alm]);
				$xpath = new DOMXpath($dom);
				unset($row[$proc_alm]);

       		 	return $xpath;
			}
			else {
				return null;
			}
		}
		else{
			return null;
		}

	}

	public function getDatosConsolidadoRendimientoEspecial() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.rendimiento_detalle_global(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//				print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
 	        $dom->preserveWhiteSpace = FALSE;
            $dom->loadXML($row['rendimiento_detalle_global']);
            $xpath = new DOMXpath($dom);
			unset($row["rendimiento_detalle_global"]);
			$xpathSql = array('xpath' => $xpath, 'sql' => $sql);
			return $xpathSql;
		}
		else {
			return null;
		}
	}
	public function getDatosConsolidadoRendimientoOnline() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.rendimiento_detalle_global(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
				//print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
 	        $dom->preserveWhiteSpace = FALSE;
            $dom->loadXML($row['rendimiento_detalle_global']);
            $xpath = new DOMXpath($dom);
			unset($row["rendimiento_detalle_global"]);
			$xpathSql = array('xpath' => $xpath, 'sql' => $sql);
			return $xpathSql;
		}
		else {
			return null;
		}
	}

	public function getDatosConsolidadoRendimiento() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.rendimiento_detalle_global(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//				print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
 	        $dom->preserveWhiteSpace = FALSE;
            $dom->loadXML($row['rendimiento_detalle_global']);
            $xpath = new DOMXpath($dom);
			unset($row["rendimiento_detalle_global"]);
			return $xpath;
		}
		else {
			return null;
		}
	}

	public function getDatosDetalladoDisponibilidad() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."', ".
				(isset($this->extra["variable"])?$usr->cliente_id:'0').")";
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['disponibilidad_resumen_consolidado']);
			$xpath = new DOMXpath($dom);
			unset($row["disponibilidad_resumen_consolidado"]);
			return $xpath;
		}
		else {
			return null;
		}
	}


	public function getDatosHistoricoDisponibilidad() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado_historico(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->timestamp->tipo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodoHistorico())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."', ".
				(isset($this->extra["variable"])?$usr->cliente_id:'NULL').")";
		//print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['disponibilidad_resumen_consolidado_historico']);
			$xpath = new DOMXpath($dom);
			unset($row["disponibilidad_resumen_consolidado_historico"]);
			return $xpath;
		}
		else {
			return null;
		}
	}

	public function getDatosErroresDisponibilidad() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.errores(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
// 		print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['errores']);
			$xpath = new DOMXpath($dom);
			return $xpath;
		}
		else {
			return null;
		}
	}

	public function getDatosEstadisticoRendimiento() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		if ($_REQUEST['multi_obj'] < 1) {
			$sql = "SELECT * FROM reporte.rendimiento_detalle_consolidado(";
		}else{
			$sql = "SELECT * FROM reporte.rendimiento_detalle_consolidado_habil(";
		}
		$sql = $sql.
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//		print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			if ($_REQUEST['multi_obj'] < 1) {
				$proc_alm = 'rendimiento_detalle_consolidado';
			}else{
				$proc_alm = 'rendimiento_detalle_consolidado_habil';
			}
			$dom->loadXML($row[$proc_alm]);
			$xpath = new DOMXpath($dom);
			unset($row[$proc_alm]);
			return $xpath;
		}
		else {
			return null;
		}
	}

	public function getDatosHistoricoRendimientoEspecial() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if (isset($this->extra["parent_objetivo_id"]) and $this->extra["parent_objetivo_id"] != "") {
			$parent_objetivo_id = $this->extra["parent_objetivo_id"];
		}
		else {
			$parent_objetivo_id = $this->objetivo_id;
		}
		$sql = "SELECT * FROM reporte.rendimiento_detalle_global_historico(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($parent_objetivo_id).",".
				pg_escape_string($this->objetivo_id).",".
				pg_escape_string($this->timestamp->tipo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodoHistorico())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

		//		print($sql."<br>");
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['rendimiento_detalle_global_historico']);
			$xpath = new DOMXpath($dom);
			unset($row["rendimiento_detalle_global_historico"]);
			$xpathSql = array('xpath' => $xpath, 'sql' => $sql);
			return $xpathSql;
		}
		else {
			return null;
		}
	}

	public function getDatosHistoricoRendimiento() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if (isset($this->extra["parent_objetivo_id"]) and $this->extra["parent_objetivo_id"] != "") {
			$parent_objetivo_id = $this->extra["parent_objetivo_id"];
		}
		else {
			$parent_objetivo_id = $this->objetivo_id;
		}
		$sql = "SELECT * FROM reporte.rendimiento_detalle_global_historico(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($parent_objetivo_id).",".
				pg_escape_string($this->objetivo_id).",".
				pg_escape_string($this->timestamp->tipo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodoHistorico())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

		//		print($sql."<br>");
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['rendimiento_detalle_global_historico']);
			$xpath = new DOMXpath($dom);
			unset($row["rendimiento_detalle_global_historico"]);
			return $xpath;
		}
		else {
			return null;
		}
	}

	public function getDatosFrecuenciaRendimiento() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.frecuencia_global(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
// 		print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['frecuencia_global']);
			$xpath = new DOMXpath($dom);
			unset($row["frecuencia_global"]);
			return $xpath;
		}
		else {
			return null;
		}
	}



	public function getDatosFrecuenciaAculumada() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.frecuencia_acumulada_global(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {

			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['frecuencia_acumulada_global']);
 			$xpath = new DOMXpath($dom);
  			unset($row["frecuencia_acumulada_global"]);

			return $xpath;
		}
		else {
			return null;
		}
	}

	public function getDatosRendimientoPorDia() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.rendimiento_detalle_global_pordiasemana(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//		print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$parser = new XMLParserReporte($row["rendimiento_detalle_global_pordiasemana"]);
			$parser->getDatosRendimientoPorDia();
			unset($row["rendimiento_detalle_global_pordiasemana"]);
			return $parser;
		}
		else {
			return null;
		}
	}

	public function getDatosRendimientoPorDia2() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.rendimiento_detalle_global_pordiasemana(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		//		print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['rendimiento_detalle_global_pordiasemana']);
			$xpath = new DOMXpath($dom);
			unset($row["rendimiento_detalle_global_pordiasemana"]);
			return $xpath;
		}
		else {
			return null;
		}
	}
	public function getDatosSLADetalladoRendimiento() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.sla_global(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//		print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
 			$dom->preserveWhiteSpace = FALSE;
 			$dom->loadXML($row['sla_global']);
  			$xpath = new DOMXpath($dom);
   			unset($row["sla_global"]);
			return $xpath;
		}
		else {
			return null;
		}
	}

	public function getDatosSLADetalladoRendimientoReal() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.sla_global_real(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//		print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
			if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
 			$dom->preserveWhiteSpace = FALSE;
 			$dom->loadXML($row['sla_global_real']);
  			$xpath = new DOMXpath($dom);
   			unset($row["sla_global_real"]);
			return $xpath;
		}
		else {
			return null;
		}
	}

	public function getDatosSLAHistoricoRendimiento() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.sla_consolidado_historico(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->objetivo_id).", '".
				pg_escape_string($this->timestamp->tipo_periodo)."', ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodoHistorico())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		//		print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['sla_consolidado_historico']);
			$xpath = new DOMXpath($dom);
			return $xpath;
		}
		else {
			return null;
		}
	}

	public function getDatosComparativo() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.comparativo_resumen_global(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['comparativo_resumen_global']);
			$xpath = new DOMXpath($dom);
			return $xpath;
		}
		else {
			return null;
		}
	}

	public function getDatosAudex($objetivo_id){
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.calcula_salud(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($objetivo_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
 //		print_r($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['calcula_salud']);
			$xpath = new DOMXpath($dom);
			unset($row["calcula_salud"]);
			return $xpath;
		}
		else {
			return null;
		}

	}

	public function getDatosDetalleElementosPlus() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.elementosplus(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).",".
				pg_escape_string($this->extra["paso_id"]).",".
				pg_escape_string($this->extra["monitor_id"]).",'".
				pg_escape_string($this->extra["fecha_monitoreo"])."') ";

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['elementosplus']);
			$xpath = new DOMXpath($dom);
			unset($row["elementosplus"]);
			return $xpath;
		}
		else {
			return null;
		}
	}
        /*VALIDACION DE PARAMETROS Y VISIBILIDAD DE GRAFICO*/
        public function get_grafico_validacion($tipo_grafico) {

            $objetivo = new ConfigObjetivo($this->objetivo_id);
            $contador = 0;
            $validate = array(
                "visible"  => false,
                "url" => false,
                "titulo" => "",
                "selector_url" => array(),
                "orden" => array(),
                "contador"=> 0,
                "informacion"=>"",
                "tipo_correcto"=>false,
                "link"=>"",
+               "contador"=>0
            );
            $informacion = array();
            $link = array();

            foreach($objetivo->__datos as $configuracion){

                if($configuracion->tipo==$tipo_grafico){
                    array_push($link, $configuracion->link);
                    $validate["tipo_correcto"]=true;
                    array_push($validate["selector_url"], $configuracion->titulo);
                    array_push($validate["orden"], $configuracion->orden);
                    $validate["contador"]=count($configuracion->titulo);
                    array_push($informacion, $configuracion->informacion);
                    if($configuracion->visible==1){
                        $validate["visible"]=true;
                    }
                    if(!empty($configuracion->url) or ($configuracion->url != "")){
                        $validate["url"]=true;
                    }
                    $contador ++;
                }
            }
            /*verifica si existe el tipo de grafico*/
+            $validate["contador"]=$contador;
            /*cadena de informacion separada por coma  para posteriormente convertirla en array en javascript*/
            if($validate["visible"]==true && $validate["url"]== true){
                $validate["informacion"]=implode(",",$informacion[0]);
                $validate["link"]=implode(",",$link[0]);
            }

            return $validate;
        }
        public function getTokenApiBase($objetivo_id){
			global $mdb2;
			global $log;
			global $current_usuario_id;
			$sql = "SELECT xml_configuracion
					FROM objetivo_config
					WHERE objetivo_id = ".$objetivo_id."
					AND
					es_ultima_config = 't'";

			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
	 	        $dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['xml_configuracion']);
	            $xpath = new DOMXpath($dom);
				unset($row['xml_configuracion']);
				return $xpath;
			}
			else {
				return null;
			}
		}


	protected function generarEscala() {
		$valor = $_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id];
        //		$this->tiene_escala = ($valor > 0)?$maximo_escala:'-1';

		$arr_escala = array("90", "60", "30", "10", "5", "1");

		$text = "<table>".
				"<tr>".
				"<td class=\"celdanegra40\">Escala Segundos:&nbsp;&nbsp;</td>".
				"<td>".
				"<select onchange=\"cargarItem('contenedor_".$this->__item_id."', '".$this->__item_id."', '1', ['valor_escala', this.value]);\">".
				"<option value=\"-1\">Automatico</option>";
		foreach ($arr_escala as $escala) {
			$text.= "<option value=\"$escala\" ".(($escala==$valor)?"selected":"").">$escala Segundos </option>";
		}
		$text.= "</selected>".
				"</td>".
				"</tr>".
				"</table><br>";

		return $text;
	}
        /*GENERA MENU DESPLEGABLE*/
        protected function generarSelectorTiempo($orden, $tiempo) {
                $arr_orden = $orden;
                $arr_tiempo = $tiempo;
                $contador = 0;
                $con_elemento=count($arr_tiempo[0]);
                $valor=0;
                /*CREACION DEL MENU DESPLEGABLE*/
                if($con_elemento !=1 and $con_elemento !=0){
                    $text = "<table id=\"selector_tiempo\">".
                                "<tr>".
                                "<td class=\"celdanegra40\">Seleccionar: </td>".
                                "<td>".

                                "<select id=\"seleccion\" onchange=\"getGraphic(this.value);\">";
                foreach ($arr_tiempo as $arr_select) {
                    foreach ($arr_select as $select) {
                        $value=$arr_orden[0][$contador];
                        $value=$value." ".$select;
                        $text.= "<option value=\"$value.\" ".(($value==$valor)?"selected":"").">$select </option>";
                        $contador++;
                    }
		}
		$text.= "</selected>".
                            "</td>".
                            "</tr>".
                            "</table><br>";

                }
                else{
                    $text="";
                }
                return $text;
	}

	protected function intervaloLinea($inicio, $termino) {
		$diff = ($termino - $inicio)/3600;
		if ($diff < 168) {
			$intervalo = 1 * 3600;
		}
		elseif ($diff < 336) {
			$intervalo = 6 * 3600;
		}
		else {
			$intervalo = 12 * 3600;
		}
		return $intervalo;
	}
}

?>