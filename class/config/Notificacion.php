<?

class Notificacion {

	/** 
	 * Atributos publicos.
	 */
	var $notificacion_id;
	var $intervalo_id;
	var $escalabilidad_desde;
	var $escalabilidad_hasta;
	var $uptime_parcial;
	var $downtime_parcial;
	var $downtime_grupal;
	var $downtime_global;
	var $sla;
	var $patron_inverso;
	var $usuario_cliente_id;

	/**
	 * Atributos privados.
	 */
	var $__destinatario;
	var $__objetivo;
	var $__horario;
	
	/**
	 * Constructor.
	 * 
	 * @param integer $notificacion_id.
	 * @return Notificacion
	 */
	function Notificacion($notificacion_id) {
		$this->notificacion_id = $notificacion_id;
	}
	
	/**
	 * Funcion que retorna la configuracion del objetivo de la notificacion.
	 * 
	 * @return ConfigObjetivo
	 */
	function getConfigObjetivo() {
		return new ConfigObjetivo($this->__objetivo->objetivo_id);
	}

	/**
	 * Funcion que agrega una notificacion.
	 */
	function agregar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		
		Validador::campoNumerico($this->escalabilidad_desde, "Escalabilidad Desde", 1, 20000);
		if ($this->escalabilidad_hasta != "") {
			Validador::campoNumerico($this->escalabilidad_hasta, "Escalabilidad Hasta", $this->escalabilidad_desde, 20000);
		}
		
		if (!$this->downtime_parcial and !$this->downtime_grupal and !$this->downtime_global) {
			$this->uptime_parcial = false;
		}
		
		$sql = "SELECT * FROM public.notificacion_agrega(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->__objetivo->objetivo_id).",".
				pg_escape_string($this->__destinatario->destinatario_id).",".
				"NULL, ".
				pg_escape_string($this->__horario->horario_id).",".
				(($this->uptime_parcial)?"'t'":"'f'").",".
				(($this->downtime_parcial)?"'t'":"'f'").",".
				(($this->downtime_grupal)?"'t'":"'f'").",".
				(($this->downtime_global)?"'t'":"'f'").",".
				(($this->sla)?"'t'":"'f'").",".
				(($this->patron_inverso)?"'t'":"'f'").",".
				"NULL, '".
				pg_escape_string($this->escalabilidad_desde)."', ".
				(($this->escalabilidad_hasta=="")?"NULL":"'".pg_escape_string($this->escalabilidad_hasta)."'").")";
//		print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql,$res->userinfo);
			//SE ELIMINÓ EL EXIT() CAMBIANDOSE POR UN RETURN FALSE POE QUE SE NECESITA SABER SI SE PUDO O NO REALIZAR LA OPERACIÓN
			return false;			
		}
		if ($row = $res->fetchRow()) {
			$this->notificacion_id = $row["notificacion_agrega"]; 
		}
		$log->setChange("AGREGO NOTIFICACION", $this->toString());
		return true;
	}

	/**
	 * Funcion que modifica una notificacion.
	 */
	function modificar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		Validador::campoNumerico($this->escalabilidad_desde, "Escalabilidad Desde", 1, 20000);
		if ($this->escalabilidad_hasta != "") {
			Validador::campoNumerico($this->escalabilidad_hasta, "Escalabilidad Hasta", $this->escalabilidad_desde, 20000);
		}

		if (!$this->downtime_parcial and !$this->downtime_grupal and !$this->downtime_global) {
			$this->uptime_parcial = false;
		}
		
		$sql = "SELECT * FROM public.notificacion_modifica(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->notificacion_id).",".
				pg_escape_string($this->__destinatario->destinatario_id).",".
//				pg_escape_string($this->intervalo_id).",".
				"NULL, ".
				pg_escape_string($this->__horario->horario_id).",".
				(($this->uptime_parcial)?"'t'":"'f'").",".
				(($this->downtime_parcial)?"'t'":"'f'").",".
				(($this->downtime_grupal)?"'t'":"'f'").",".
				(($this->downtime_global)?"'t'":"'f'").",".
				(($this->sla)?"'t'":"'f'").",".
				(($this->patron_inverso)?"'t'":"'f'").",".
//				((!$this->sensibilidad)?"NULL":"'".$this->sensibilidad."'").",".
				"NULL, '".
				pg_escape_string($this->escalabilidad_desde)."', ".
				(($this->escalabilidad_hasta=="")?"NULL":"'".pg_escape_string($this->escalabilidad_hasta)."'").")";				
		//print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql,$res->userinfo);
			//SE ELIMINÓ EL EXIT() CAMBIANDOSE POR UN RETURN FALSE POE QUE SE NECESITA SABER SI SE PUDO O NO REALIZAR LA OPERACIÓN
			return false;			

		}
		$log->setChange("MODIFICO NOTIFICACION", $this->toString());
		return true;
	}
	
	/**
	 * Funcion que elimina una notificacion.
	 */
	function eliminar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		$sql = "SELECT * FROM public.notificacion_elimina(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->notificacion_id).")";
		//print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			
			$log->setError($sql,$res->userinfo);
			//SE ELIMINÓ EL EXIT() CAMBIANDOSE POR UN RETURN FALSE POE QUE SE NECESITA SABER SI SE PUDO O NO REALIZAR LA OPERACIÓN
			return false;

		}
		$log->setChange("ELIMINO NOTIFICACION", $this->toString());
		return true;
	}

	
	/**
	 * Funcion que es utilizada para mostrar en un string 
	 * los datos mas importantes de la notificacion.
	 * Uso exclusivo para Log.
	 * 
	 * @return string
	 */
	function toString() {
		$string = "OBJETIVO ".$this->__objetivo->nombre.", ".
				  "CONTACTO ".$this->__destinatario->nombre.", ".
				  "HORARIO ".$this->__horario->nombre;
		return $string;
	}
	
}

?>