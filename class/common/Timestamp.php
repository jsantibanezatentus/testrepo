<?

class Timestamp {
	
	var $tipo_periodo;
	var $tipo_id;
	var $fecha_inicio;
	var $fecha_termino;
	var $zona_horaria;
	
	function Timestamp($fecha_inicio = null, $fecha_termino = null) {
/*		if (isset($tipo_id) and $tipo_id > 0) {
			$this->tipo_periodo = "especial";
			$this->tipo_id = $tipo_id;
		}*/
		
		if ((strtotime($fecha_termino)-strtotime($fecha_inicio)) < 86400) {
			$this->tipo_periodo = REP_PRD_DAY;
			$this->tipo_id = 1;
		}
		elseif (strtotime($fecha_termino." - 6 day") == strtotime($fecha_inicio)) {
			$this->tipo_periodo = REP_PRD_WEEK;
			$this->tipo_id = 2;
		}
		else {
			$this->tipo_periodo = REP_PRD_MONTH;
			$this->tipo_id = 3;
		}
		if ($fecha_inicio==null or $fecha_inicio=="") {
			$this->fecha_inicio = date("Y-m-d 00:00:00", strtotime("-1 day"));
		}
		else {
			$this->fecha_inicio = date('Y-m-d H:i:s', strtotime($fecha_inicio));
		}
		
		if ($fecha_termino==null or $fecha_termino=="") {
			$this->fecha_termino = date("Y-m-d 00:00:00");
		}
		else {
			$this->fecha_termino = date('Y-m-d H:i:s', strtotime($fecha_termino));
		}
	}
	
	function toString() {
		if (date("d/m/Y", strtotime($this->fecha_inicio))==date("d/m/Y", strtotime($this->fecha_termino))) {
			return date("d/m/Y", strtotime($this->fecha_inicio));
		}
		else {
			return date("d/m/Y", strtotime($this->fecha_inicio))." - ".date('d/m/Y', strtotime($this->fecha_termino));
		}
	}
	
	function getFormatoFecha($fecha) {
		global $meses_anno;

		if ($this->tipo_periodo == REP_PRD_DAY) {
			return date("d/m/Y", strtotime($fecha));
		}
		elseif ($this->tipo_periodo == REP_PRD_WEEK) {
			return date("d/m/Y", strtotime($fecha));
		}
		elseif ($this->tipo_periodo == REP_PRD_MONTH) {
			return $meses_anno[date("n", strtotime($fecha))]." ".date("Y", strtotime($fecha));
		}
		else {
			return date("d/m/Y", strtotime($fecha));
		}
	}
	
	function getFormatearFecha($fecha, $format="Y-m-d H:i:s") {
		return date($format, strtotime($fecha));
	}
	
	function getInicioPeriodoHistorico() {
		$si = strtotime($this->fecha_inicio);
		if ($this->tipo_periodo == REP_PRD_ULT24H or
			$this->tipo_periodo == REP_PRD_DAY) {
			$sih = mktime(date('s',$si), date('i',$si), date('H',$si), date('m',$si), date('d',$si)-7, date('Y',$si));
		}
		elseif ($this->tipo_periodo == REP_PRD_WEEK) {
			$sih = mktime(date('s',$si), date('i',$si), date('H',$si), date('m',$si), date('d',$si)-35, date('Y',$si));
		}
		elseif ($this->tipo_periodo == REP_PRD_MONTH or $this->tipo_periodo == "especial") {
			$sih = mktime(date('s',$si), date('i',$si), date('H',$si), date('m',$si)-12, 1, date('Y',$si));
		}
		return (date('Y-m-d H:i:s', $sih));
	}
	
	function getInicioPeriodo($format="Y-m-d H:i:s") {
		return date($format, strtotime($this->fecha_inicio));
	}
	
	function getTerminoPeriodo($format="Y-m-d H:i:s") {
		if ($this->tipo_periodo == "especial") {
			return date($format, strtotime($this->fecha_termino));
		}
		else {
			return date($format, (strtotime($this->fecha_termino) + 86400));
		}
	}

	function getFechaInicio($format="Y-m-d H:i:s") {
		return date($format, strtotime($this->fecha_inicio));
	}
	
	function getFechaTermino($format="Y-m-d H:i:s") {
		return date($format, strtotime($this->fecha_termino));
	}

}

?>