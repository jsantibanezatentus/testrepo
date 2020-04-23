<?

class ReporteItem {
	
	var $item_id;
	var $nombre;
	var $nombre_url;
	var $descripcion;
//	var $tipo;
	var $clase_nombre;
	var $metodo_nombre;

//	var $__usuario_id;
	
	function ReporteItem($item_id) {
		$this->item_id = $item_id;
	}
	
	function __ReporteItem() {
		global $mdb2;
		global $log;

		global $usr;
		$intervalos = Constantes::getIntervalos(REP_INTERVALO_SEMAFORO);

		$sql = "SELECT * FROM public.reporte_informe_item ".
			   "WHERE reporte_informe_item_id=".pg_escape_string($this->item_id);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql,$res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
// 			print_r($row);
			$this->nombre = $row["nombre"];
			$this->nombre_url = Utiles::parameterize($this->nombre);

			$this->descripcion = str_replace(array("{__intervalo_semaforo}"), array($intervalos[$usr->periodo_semaforo_id]), $row["descripcion"]);
			$this->clase_nombre = $row["clase"];

			// TODO: UNA VEZ REEMPLAZADOS TODOS LOS NOMBRES DE CLASES DE LA BASE DE DATOS
			// ESTE CODIGO NO SERA NECESARIO.
/*			if ($row["clase"] == "GraficoSVG") {
				$this->clase_nombre = $row["clase"];
			}
			elseif (preg_match("/Grafico/", $row["clase"])) {
				$this->clase_nombre = "Grafico";
			}
			elseif (preg_match("/Tabla/", $row["clase"])) {
				$this->clase_nombre = "Tabla";
			}
			else {
				$this->clase_nombre = $row["clase"];
			}*/

			$this->clase_nombre = $row["clase"];
			$this->metodo_nombre = $row["metodo"];
//			$this->tipo = (preg_match("/Grafico/", $this->clase_nombre))?'grafico':'tabla';
		}
	}
	
/*	function generarContenido($objeto_id, $subobjeto_id, $timestamp, $horario_id, $subgrafico_id = null) {
		if ($this->clase_nombre == "Grafico") {
			$clase_nombre = $_SESSION["clase_grafico"];
		}
		else {
			$clase_nombre = $this->clase_nombre;
		}
//		$clase_nombre = $this->clase_nombre;
		$metodo_nombre = $this->metodo_nombre;
		
		$clase = new $clase_nombre();
		$clase->tipo = $this->tipo;
		$clase->objetivo_id = $objeto_id;
		$clase->subobjetivo_id = $subobjeto_id;
		$clase->horario_id = $horario_id;
		$clase->timestamp = $timestamp;
		$clase->subgrafico_id = $subgrafico_id;
		$clase->__item_id = $this->item_id;
//		$clase->__usuario_id = $this->__usuario_id;
		$clase->$metodo_nombre();
		return $clase;
	}*/
	
	function getContenido($tiene_svg, $tiene_flash) {
		if ($this->clase_nombre == "Grafico" and $tiene_svg) {
			$clase_nombre = "GraficoSVG";
		}
		elseif ($this->clase_nombre == "Grafico" and $tiene_flash) {
			$clase_nombre = "GraficoFlash";
		}
		else {
			$clase_nombre = $this->clase_nombre;
		}

		$clase = new $clase_nombre();
		$clase->__item_id = $this->item_id;
		$clase->solicitud = $this->metodo_nombre;
		return $clase;
	}
	
}

?>