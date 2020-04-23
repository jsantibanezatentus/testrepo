<?

abstract class Constantes {

	/**
	 * Function para obtener el listado de intervalos.
	 * 
	 * @param integer $tipo
	 * @return array<String>
	 */
	function getIntervalos($tipo) {
		if ($tipo==REP_INTERVALO_MONITOREO) {
			$sql_tipo = "WHERE uso_monitoreo='t'";
		}
		elseif ($tipo==REP_INTERVALO_NOTIFICACION) {
			$sql_tipo = "WHERE uso_notificacion='t'";
		}
		elseif ($tipo==REP_INTERVALO_SEMAFORO) {
			$sql_tipo = "WHERE uso_semaforo='t'";
		}
		else {
			$sql_tipo = "";
		}
		
		global $mdb2;
		global $log;
			
		$sql = "SELECT * FROM public.intervalo ".$sql_tipo." ORDER BY valor";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$intervalos = array();
		while($row = $res->fetchRow()) {
			$intervalos[$row["intervalo_id"]] = $row["nombre"]; 
		}
		return $intervalos;
	}

	/**
	 * Function para obtener el listado de tipos de destinatarios.
	 * 
	 * @return array<String>
	 */
	function getTiposDestinatarios() {
		global $mdb2;
		global $log;
			
		$sql = "SELECT * FROM public.notificacion_tipo WHERE es_publico='t'";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$tipos_destinatarios = array();
		while($row = $res->fetchRow()) {
			$tipos_destinatarios[$row["notificacion_tipo_id"]] = $row["nombre"]; 
		}
		return $tipos_destinatarios;
	}

	/**
	 * Function para obtener el listado de tipos de horarios.
	 * 
	 * @return array<String>
	 */
	function getTiposHorarios() {
		global $mdb2;
		global $log;
		$sql = "SELECT * FROM public.horario_tipo";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$tipos_horarios = array();
		while($row = $res->fetchRow()) {
			$tipos_horarios[$row["horario_tipo_id"]] = $row["nombre"]; 
		}
		return $tipos_horarios;
	}

	/**
	 * Function para obtener el listado de tipos de perfiles.
	 * 
	 * @return array<String>
	 */
/*	function getTiposPerfiles() {
		global $mdb2;
		global $log;
		$sql = "SELECT * FROM public.reporte_perfil";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$tipos_perfiles = array();
		while($row = $res->fetchRow()) {
			$tipos_perfiles[$row["reporte_perfil_id"]] = $row["nombre"]; 
		}
		return $tipos_perfiles;
	}*/

	/**
	 * Function para obtener el listado de zonas horarias.
	 * 
	 * @return array<String>
	 */
	function getZonasHorarias() {
		global $mdb2;
		global $log;
		$sql = "SELECT * FROM public.zona_horaria WHERE activo='t' ORDER BY nombre";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$zonas_horarias = array();
		while($row = $res->fetchRow()) {
			$zonas_horarias[$row["zona_horaria_id"]] = $row["nombre"]; 
		}
		return $zonas_horarias;
	}

	/**
	 * Function para obtener el listado de idiomas.
	 * 
	 * @return array<String>
	 */
	function getIdiomas() {
		global $mdb2;
		global $log;
		$sql = "SELECT * FROM public.lenguaje";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$idiomas = array();
		while($row = $res->fetchRow()) {
			$idiomas[$row["lenguaje_id"]] = $row["nombre"]; 
		}
		return $idiomas;
	}
	
	function getPaises() {
		global $mdb2;
		global $log;
		$sql = "SELECT * FROM public.pais";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$paises = array();
		while($row = $res->fetchRow()) {
			$paises[$row["pais_id"]] = $row;
		}
		return $paises;
	}
	
	
	function getCodigos() {
		global $mdb2;
		global $log;
		$sql = "SELECT * FROM public.codigo ORDER BY codigo_tipo_id, codigo_id";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$codigos = array();
		while($row = $res->fetchRow()) {
			$codigos[] = $row;
		}
		return $codigos;
	}
	
}

?>