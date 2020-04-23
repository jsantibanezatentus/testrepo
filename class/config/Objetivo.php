<?

class Objetivo {

	/** 
	 * Atributos publicos.
	 */
	var $objetivo_id;
	var $nombre;
	var $descripcion;
	var $intervalo_id;
	var $intervalo_nombre;
	var $sla_dis_ok;
	var $sla_dis_error;
	var $sla_ren_ok;
	var $sla_ren_error;
	var $timeout;
	var $distribucion;
	var $umbral_satisfactorio;
	var $umbral_intolerable;
	var $umbral_excelente;
	var $umbral_bueno;
	
	/**
	 * Atributos privados.
	 */
	var $__xml_config;
	var $__servicio;
	var $__monitores;
	var $__pasos;
	var $__datos;
	var $__objetivos;
	var $__ponderacion;
	var $___objetivos;
	var $__metas;
	/**
	 * Constructor.
	 *
	 * @param integer $objetivo_id optional.
	 * @return Objetivo
	 */
	function Objetivo($objetivo_id=0) {
		$this->objetivo_id = $objetivo_id;
	}

	/**
	 * Constructor secundario.
	 * Se utiliza cuando se obtiene solo el objetivo_id.
	 */
	function __Objetivo() {
		global $mdb2;
		global $log;

		$sql = "SELECT o.*, s.nombre AS servicio_nombre, ".
			   "i.nombre AS intervalo_nombre,".
			   "sla[1] AS sla_ren_ok, sla[2] AS sla_ren_error, ".
			   "sla[3] AS sla_dis_ok, sla[4] AS sla_dis_error ".
			   "FROM public.objetivo_get_config(".
				pg_escape_string($this->objetivo_id).", now()) o ".
			   "LEFT JOIN intervalo i ON i.intervalo_id=o.intervalo_id ".
			   "LEFT JOIN servicio s ON s.servicio_id=o.servicio_id";
// 		print($sql);exit;
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if ($row = $res->fetchRow()) {
			$this->nombre = $row["nombre"];
			$this->descripcion = $row["descripcion"];
			$this->intervalo_id = $row["intervalo_id"];
			$this->intervalo_nombre = $row["intervalo_nombre"];
			$this->sla_ren_ok = $row["sla_ren_ok"];
			$this->sla_ren_error = $row["sla_ren_error"];
			$this->sla_dis_ok = $row["sla_dis_ok"];
			$this->sla_dis_error = $row["sla_dis_error"];
			$this->__servicio = new Servicio($row["servicio_id"]);
			$this->__servicio->nombre = $row["servicio_nombre"];
			$this->__xml_config = $row["xml_configuracion"];
		}
	}

	/**
	 * Funcion que obtiene el Servicio del objetivo.
	 *
	 * @return Servicio
	 */
	function getServicio() {
		return $this->__servicio;
	}

	/**
	 * Funcion para obtener los monitores del objetivo.
	 *
	 * @return array<Monitor>
	 */
	function getMonitores() {
		global $mdb2;
		global $log;
			
		$sql = "SELECT p.nombre AS pais_nombre, m.monitor_id, n.* ".
			   "FROM(".
			   "	SELECT unnest(monitor_id) AS monitor ".
			   "	FROM public.objetivo_config ".
			   "	WHERE objetivo_id=".pg_escape_string($this->objetivo_id)." ".
			   "    AND es_ultima_config='t' ".
			   ") as ids, monitor m, nodo n, pais p ".
			   "WHERE ids.monitor=m.monitor_id ".
			   "AND m.nodo_id=n.nodo_id ".
			   "AND p.pais_id=n.pais_id";
		$res =& $mdb2->query($sql);
// 		print $sql;
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
			
		$this->__monitores = array();
		while ($row = $res->fetchRow()) {
			$monitor = new Monitor($row["monitor_id"]);
			$monitor->nombre = $row["nombre"];
			$monitor->descripcion = $row["descripcion"];
			$monitor->pais_id = $row["pais_id"];
			$monitor->pais_nombre = $row["pais_nombre"];
			$this->__monitores[$row["monitor_id"]] = $monitor;
		}
		return $this->__monitores;
	}
	
	function getNodos() {
		global $mdb2;
		global $log;
			
		$sql = "SELECT DISTINCT n.* ".
				"FROM(".
				"	SELECT unnest(monitor_id) AS monitor ".
				"	FROM public.objetivo_config ".
				"	WHERE objetivo_id=".pg_escape_string($this->objetivo_id)." ".
				"    AND es_ultima_config='t' ".
				") as ids, monitor m, nodo n ".
				"WHERE ids.monitor=m.monitor_id ".
				"AND m.nodo_id=n.nodo_id";
		//echo $sql;
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
			
		$nodos = array();
		while ($row = $res->fetchRow()) {
			$nodo = new Nodo($row["nodo_id"], $row["nombre"]);
			$nodos[$row["nodo_id"]] = $nodo;
		}
		return $nodos;
	}
	
	function getFechasPeriodicos($anno_mostrado) {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		$sql = "SELECT date_part('month', fecha) as mes, * FROM public.reporte_periodico_informes(".
			pg_escape_string($current_usuario_id).", ".
			pg_escape_string($this->objetivo_id).", ".
			pg_escape_string($anno_mostrado).") ".
		   "ORDER BY mes DESC, reporte_informe_subtipo_id DESC, fecha_termino";
//		echo($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		
		$arr_fechas = array();
		while ($row = $res->fetchRow()) {
			$arr_fechas[] = new Timestamp($row["fecha_inicio"], date("Y-m-d", strtotime($row["fecha_termino"])-1));
		}
		
		return $arr_fechas;
	}
	
	function existeFechaPeriodico($fecha_inicio, $fecha_termino) {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		$anno_mostrado = date("Y", strtotime($fecha_inicio));
		
		// MODIFICACION para permitir fechas con hora incluida
		$sql = "SELECT * FROM public.reporte_periodico_informes(".
			pg_escape_string($current_usuario_id).", ".
			pg_escape_string($this->objetivo_id).", ".
			pg_escape_string($anno_mostrado).") ".
		  "WHERE fecha_inicio='".pg_escape_string($fecha_inicio)."' ".
		  "AND fecha_termino='".pg_escape_string($fecha_termino)."' ";
//		echo($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($res->numRows() == 0) {

			// TODO: Se debe eliminar esto cuando se implemente por completo el nuevo calendario.
			$sql2 = "SELECT * FROM public.reporte_periodico_informes(".
					pg_escape_string($current_usuario_id).", ".
					pg_escape_string($this->objetivo_id).", ".
					pg_escape_string($anno_mostrado).") ".
					"WHERE fecha_inicio::DATE='".pg_escape_string($fecha_inicio)."'::DATE ".
					"AND fecha_termino::DATE='".pg_escape_string($fecha_termino)."'::DATE + '1 day'::INTERVAL ";

			$res2 =& $mdb2->query($sql2);
			if (MDB2::isError($res2)) {
				$log->setError($sql2, $res2->userinfo);
				exit();
			}
			if ($res2->numRows() == 0) {
				return false;
			}
			else {
				return true;
			}
		}
		else {
			return true;
		}
	}
	
	function getSlaNotificaciones($monitor_id) {
		global $mdb2;
		global $log;
		
		$sql = "SELECT * FROM notificacion_umbral ".
			   "WHERE objetivo_id=".pg_escape_string($this->objetivo_id)." ".
			   "AND monitor_id=".pg_escape_string($monitor_id);
//		echo($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		
		$arr_sla = array();
		while ($row = $res->fetchRow()) {
			$arr_sla[$row["paso_orden"]] = $row["umbral"];
		}
		
		return $arr_sla;
	}
	
	function guardarSlaNotificacion($monitor_id, $paso_id, $sla) {
		global $mdb2;
		global $log;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		Validador::campoNumerico($sla, "Umbral", 0, 600);
		
		$sql = "INSERT INTO notificacion_umbral(monitor_id, objetivo_id, paso_orden, umbral) ".
			   "VALUES (".
				pg_escape_string($monitor_id).", ".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($paso_id).", ".
				pg_escape_string($sla).")";
//		echo($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}	
	}
	
	function eliminarSlaNotificacion($monitor_id) {
		global $mdb2;
		global $log;
		
		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		$sql = "DELETE FROM notificacion_umbral ".
			   "WHERE objetivo_id=".pg_escape_string($this->objetivo_id)." ".
			   "AND monitor_id=".pg_escape_string($monitor_id);
//		echo($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}	
	}
	
	function ordenar($orden) {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		
		Validador::campoNumerico($orden, "Orden Objetivo");
		
		$sql = "SELECT * FROM public.objetivo_preferencia_orden(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).",".
				pg_escape_string($orden).")";
//		echo($sql."\n");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}

	/*
	Obtiene los nombres de los objetivos consultados por id.
	*/
	function getObjetiveName($objIds){
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$sql = "SELECT objetivo_id, nombre from objetivo where objetivo_id in (".$objIds.")";
		#print $sql;
		$res =& $mdb2->query($sql);
		
		if (MDB2::isError($res)) {

			$response['status'] = 'error-int';
			return $response;
		}

		if ($row = $res->fetchAll()) {
			return $row;
		}

	}
}

?>