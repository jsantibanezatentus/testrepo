<?

class Seccion {

	/** 
	 * Atributos publicos.
	 */
	var $seccion_id;
	var $nombre;
	var $tipo;
	var $nivel;
	var $acceso;
	var $padre_id;
	var $es_parent;
	var $ayuda;
	var $path_analytics;
	
	/**
	 * Constructor.
	 *
	 * @param string $seccion_id
	 * @return Seccion
	 */
	function Seccion($seccion_id) {
		$this->seccion_id = $seccion_id;
	}
	
	function __Seccion() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		$sql = "SELECT *, ".
			   "public._seccion_acceso(".
				pg_escape_string($current_usuario_id).",".pg_escape_string($this->seccion_id).") ".
			   "FROM public.reporte_seccion s ".
			   "LEFT JOIN public.reporte_seccion_tipo t ON s.reporte_seccion_tipo_id=t.reporte_seccion_tipo_id ".
			   "WHERE s.reporte_seccion_id=".$this->seccion_id;
//		print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
//			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$this->nombre = $row["nombre"];
			$this->tipo = $row["key"];
			$this->nivel = $row["nivel"];
			$this->acceso = $row["_seccion_acceso"];
			$this->padre_id = $row["parent_reporte_seccion_id"];
			$this->path_analytics = $row["path_analytics"];
		}
	}

	function getPermisos($tipo_usuario) {
		
		$arr_acceso = array("-" => 0, "r" => 1, "w" => 2);
		$acceso_3 = $acceso = substr($this->acceso, 0, 1);
		$acceso_2 = $acceso = substr($this->acceso, 1, 1);
		$acceso_1 = $acceso = substr($this->acceso, 2, 1);
		
		/* SI SE PIDE ACCESO TOTAL */
		if ($tipo_usuario == 3) {
			return $acceso_3;
		}
		/* SI SE PIDE ACCES0 SUBCLIENTE PERO TIENE UN ACCESO TOTAL MAYOR */
		elseif ($tipo_usuario == 2 and $arr_acceso[$acceso_2]<$arr_acceso[$acceso_3]) {
			return $acceso_3;
		}
		/* SI SE PIDE ACCESO SUBCLIENTE Y ES MAYOR QUE EL ACCESO TOTAL */
		elseif ($tipo_usuario == 2) {
			return $acceso_2;
		}
		/* SI SE PIDE ACCESO USUARIO PERO TIENE UN ACCESO TOTAL MAYOR */
		elseif ($tipo_usuario == 1 and $arr_acceso[$acceso_1]<$arr_acceso[$acceso_3] and $arr_acceso[$acceso_2]<$arr_acceso[$acceso_3]) {
			return $acceso_3;
		}
		/* SI SE PIDE ACCESO USUARIO PERO TIENE UN ACCESO SUBCLIENTE MAYOR */
		elseif ($tipo_usuario == 1 and $arr_acceso[$acceso_1]<$arr_acceso[$acceso_2]) {
			return $acceso_2;
		}
		/* SI SE PIDE ACCESO USUARIO Y ES MAYOR QUE EL ACCESO TOTAL Y EL ACCESO SUBCLIENTE */
		elseif ($tipo_usuario == 1) {
			return $acceso_1;
		}
		/* ACCESO DEFAULT */
		else {
			return "-";
		}
	}

	/**
	 * Funcion para obtener las secciones del usuario
	 * segun el nivel de la seccion.
	 *
	 * @param integer $nivel
	 * @return array<Seccion>
	 */
	function getSeccionesNivel($nivel) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$sql = "SELECT * FROM reporte_seccion_lista_detalle(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->seccion_id).",".
				pg_escape_string($nivel).")";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
			
		$this->__secciones = array();
		while ($row = $res->fetchRow()) {
			$seccion = new Seccion($row["reporte_seccion_id"]);
			$seccion->nombre = $row["reporte_seccion_nombre"];
			$seccion->tipo = $row["reporte_seccion_tipo_key"];
			$seccion->nivel = $row["reporte_seccion_nivel"];
			$seccion->padre_id = $row["reporte_seccion_padre_id"];
			$seccion->es_parent =($row["es_ancestro"]=='t' or $this->seccion_id==$row["reporte_seccion_id"])?true:false;
			$this->__secciones[$row["reporte_seccion_id"]] = $seccion;
		}
		return $this->__secciones;
	}

	/**
	 * Funcion para obtener la seccion por defecto de una seccion padre.
	 * 
	 * @param integer $usuario_id
	 * @param integer $seccion_id
	 * @return Seccion 
	 */
	function getSeccionPorDefecto($seccion_id) {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		$sql = "SELECT * FROM ".
			   "public.reporte_seccion_por_defecto_resumen(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($seccion_id)."), ".
			   "public.reporte_ayuda(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($seccion_id).") ";
//		print($sql."<br>");		
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if ($row = $res->fetchRow()) {
			$seccion = new Seccion($row["reporte_seccion_por_defecto_resumen"]);
			$seccion->__Seccion();
			$seccion->ayuda = $row["ayuda"];
			return $seccion;			
		}
		else {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}
	
	function getReporte($objetivo_id) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($objetivo_id) {
			$sql = "SELECT rmsi.reporte_informe_id FROM ".
				   "public.reporte_mapa_seccion_informe rmsi, ".
				   "public.reporte_informe_mapa_informe_servicio rimis, ".
				   "public.objetivo o ".
				   "WHERE rmsi.reporte_seccion_id = ".
					pg_escape_string($this->seccion_id)." ".
				   "AND o.objetivo_id = ".
					pg_escape_string($objetivo_id)." ".
				   "AND rmsi.reporte_informe_id = rimis.reporte_informe_id ".
				   "AND (rimis.servicio_id = o.servicio_id OR rimis.servicio_id IS NULL) ".
				   "AND public._seccion_acceso(".
					pg_escape_string($current_usuario_id).", rmsi.reporte_seccion_id) != '---' ".
				   "AND (public._es_obj_usuario(".
					pg_escape_string($current_usuario_id).", o.objetivo_id) ".
				   "OR (o.servicio_id = 500 AND public._es_obj_cliente(".
					pg_escape_string($current_usuario_id).", o.objetivo_id)))";
		} 
		else {
			$sql = "SELECT reporte_informe_id ".
				   "FROM public.reporte_mapa_seccion_informe ".
				   "WHERE reporte_seccion_id = ".
					pg_escape_string($this->seccion_id)." ".
				   "AND public._seccion_acceso(".
					pg_escape_string($current_usuario_id).", reporte_seccion_id) != '---' ";
		}
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if ($row = $res->fetchRow()) {
			$reporte = new Reporte($row["reporte_informe_id"]);
			return $reporte;
		}
		else {
			return null;
		}
	}
	
	function tieneReporte($objetivo_id) {
		global $mdb2;
		global $log;
		global $current_usuario_id;
				
		if ($objetivo_id) {
			$sql = "SELECT rmsi.reporte_informe_id FROM ".
				   "public.reporte_mapa_seccion_informe rmsi, ".
				   "public.reporte_informe_mapa_informe_servicio rimis, ".
				   "public.objetivo o ".
				   "WHERE rmsi.reporte_seccion_id = ".
					pg_escape_string($this->seccion_id)." ".
				   "AND o.objetivo_id = ".
					pg_escape_string($objetivo_id)." ".
				   "AND rmsi.reporte_informe_id = rimis.reporte_informe_id ".
				   "AND (rimis.servicio_id = o.servicio_id OR rimis.servicio_id IS NULL) ".
				   "AND public._seccion_acceso(".
					pg_escape_string($current_usuario_id).", rmsi.reporte_seccion_id) != '---' ".
				   "AND (public._es_obj_usuario(".
					pg_escape_string($current_usuario_id).", o.objetivo_id) ".
				   "OR (o.servicio_id = 500 AND public._es_obj_cliente(".
					pg_escape_string($current_usuario_id).", o.objetivo_id)))";
		}
		else {
			$sql = "SELECT reporte_informe_id ".
				   "FROM public.reporte_mapa_seccion_informe ".
				   "WHERE reporte_seccion_id = ".
					pg_escape_string($this->seccion_id)." ".
				   "AND public._seccion_acceso(".
					pg_escape_string($current_usuario_id).", reporte_seccion_id) != '---' ";
		}
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if ($row = $res->fetchRow()) {
			return true;
		}
		else {
			return false;
		}
	}

}

?>