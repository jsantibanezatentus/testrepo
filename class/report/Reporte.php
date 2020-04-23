<?

class Reporte {
	
	var $reporte_id;
	var $nombre;
	var $descripcion;
	var $usa_calendario;
	var $usa_calendario_periodo;
	var $usa_calendario_limitado;
	var $usa_horario_habil;	
	var $muestra_intervalo_semaforo;

//	var $__usuario_id;
	var $__items;
	
	function Reporte($reporte_id) {
		global $mdb2;
		global $log;

		$sql = "SELECT * FROM public.reporte_informe ".
			   "WHERE reporte_informe_id=".pg_escape_string($reporte_id);
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
			
		if ($row = $res->fetchRow()) {
			$this->reporte_id = $row["reporte_informe_id"];
			$this->nombre = $row["nombre"];
			$this->descripcion = $row["descripcion"];
			$this->usa_calendario =($row["usa_calendario"]=='t')?true:false;
			$this->usa_calendario_periodo =($row["usa_fecha_fija"]=='t')?true:false;
			$this->usa_calendario_limitado =($row["usa_calendario_limitado"]=='t')?true:false;
			$this->usa_horario_habil =($row["usa_horario_habil"]=='t')?true:false;
			$this->muestra_intervalo_semaforo = ($row["muestra_intervalo_semaforo"]=='t')?true:false;
			$this->xml_configuracion = $row["xml_configuracion"];
				
		}
	}
	
	function getReporteItems($tipo = 0) {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		global $usr;
		$intervalos = Constantes::getIntervalos(REP_INTERVALO_SEMAFORO);

		$sql = "SELECT * FROM reporte_informe_detalle_v3(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->reporte_id).")";

		if ($tipo == REP_IMPRESION_OBJETIVO) {
			$sql.= "WHERE visible_impresion_objetivo = 't'";
		}
		elseif ($tipo == REP_IMPRESION_INFORME) {
			$sql.= "WHERE visible_impresion_informe = 't'";
		}

// 		print $sql;
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$this->__items = array();
		while($row = $res->fetchRow()) {

//		if (class_exists($row["reporte_informe_item_clase"]) and 
//			method_exists($row["reporte_informe_item_clase"], $row["reporte_informe_item_metodo"])) {

		// TODO: UNA VEZ REEMPLAZADOS TODOS LOS NOMBRES DE CLASES DE LA BASE DE DATOS
		// ESTE CODIGO NO SERA NECESARIO.
		if ((class_exists($row["reporte_informe_item_clase"]) and method_exists($row["reporte_informe_item_clase"], $row["reporte_informe_item_metodo"])) or
			method_exists("Grafico", $row["reporte_informe_item_metodo"]) or method_exists("Tabla", $row["reporte_informe_item_metodo"])) {
			$item = new ReporteItem($row["reporte_informe_item_id"]);
			$item->nombre = $row["reporte_informe_item_nombre"];
			$item->nombre_url = Utiles::parameterize($item->nombre);
			$item->descripcion = str_replace(array("{__intervalo_semaforo}"), array($intervalos[$usr->periodo_semaforo_id]), $row["reporte_informe_item_descripcion"]);
//			$item->clase_nombre = $row["reporte_informe_item_clase"];

			// TODO: UNA VEZ REEMPLAZADOS TODOS LOS NOMBRES DE CLASES DE LA BASE DE DATOS
			// ESTE CODIGO NO SERA NECESARIO.
/*			if ($row["reporte_informe_item_clase"] == "GraficoSVG") {
				$item->clase_nombre = $row["reporte_informe_item_clase"];
			}
			elseif (preg_match("/Grafico/", $row["reporte_informe_item_clase"])) {
				$item->clase_nombre = "Grafico";
			}
			elseif (preg_match("/Tabla/", $row["reporte_informe_item_clase"])) {
				$item->clase_nombre = "Tabla";
			}
			else {
				$item->clase_nombre = $row["reporte_informe_item_clase"];
			}*/
			
			$item->clase_nombre = $row["reporte_informe_item_clase"];
			$item->metodo_nombre = $row["reporte_informe_item_metodo"];
//			$item->tipo = (preg_match("/Grafico/", $item->clase_nombre))?'grafico':'tabla';
//			$item->__usuario_id = $this->__usuario_id;
			$this->__items[] = $item;
			
		}
		}
		
		return $this->__items;
	}
	
	function getImpresionItems($tipo) {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		$sql = "SELECT count(*) as cantidad ".
			   "FROM reporte_informe_mapa_informe_item ".
			   "WHERE reporte_informe_id=".pg_escape_string($this->reporte_id)." ";

		if ($tipo == REP_IMPRESION_OBJETIVO) {
			$sql.= "AND visible_impresion_objetivo = 't'";
		}
		elseif ($tipo == REP_IMPRESION_INFORME) {
			$sql.= "AND visible_impresion_informe = 't'";
		}

//		echo($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if ($row = $res->fetchRow() and $row["cantidad"] > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
}

?>