<?

class Usuario {
	/**
	 * Atributos publicos.
	 */
	var $usuario_id;
	var $clave_md5;
	var $nombre;
	var $email;
	var $telefono;
	var $cargo;
	var $zona_horaria_id;
	var $idioma_id;
	var $cliente_id;
	var $cliente_nombre;
	var $perfil_id;
	var $perfil_nombre;
	var $perfil_prioridad;
	var $periodo_semaforo_id;
	var $periodo_semaforo_inicio;
	var $orientacion_semaforo;
	var $solo_lectura;
	var $pais_id;

//	var $usa_googleanalytics;
	var $usa_horariohabil;
	var $usa_herramientas;

	var $cnt_usuarios;
	var $cnt_notificaciones;
	var $cnt_horariohabil;

	/**
	 * Atributos privados.
	 */
	var $__objetivos;
	var $__monitores;
	var $__grupos;
	var $__subclientes;
	var $__notificaciones;
	var $__destinatarios;
	var $__horarios;
	var $__usuarios_cliente;
	var $__tokens_ws;

	/**
	 * Constructor.
	 *
	 * @param integer $usuario_id
	 * @return Usuario
	 */
	function Usuario($usuario_id) {
		$this->usuario_id = $usuario_id;
	}

	/*************** FUNCIONES PROPIAS DEL USUARIO ***************/
	/*************** FUNCIONES PROPIAS DEL USUARIO ***************/
	/*************** FUNCIONES PROPIAS DEL USUARIO ***************/

	/**
	 * Constructor secundario.
	 * Se utiliza cuando se obtiene solo el usuario_id.
	 */
	function __Usuario() {
		global $mdb2;
		global $log;

		$sql = "SELECT cu.nombre AS cliente_usuario_nombre, clave, ".
			   "cu.zona_horaria_id, cu.lenguaje_id, c.pais_id, ".
			   "cu.email AS cliente_usuario_email, ".
			   "c.cliente_id, c.nombre AS cliente_nombre, ".
			   "c.max_usuarios, c.max_notificaciones_por_usuario, c.max_horariohabil, ".
			   "cu.telefono AS cliente_usuario_telefono, ".
			   "cu.cargo AS cliente_usuario_cargo, ".
			   "cu.preferencia_semaforo_orientacion AS orientacion_semaforo, ".
			   "rp.reporte_perfil_id AS perfil_id, ".
			   "rp.nombre AS perfil_nombre, ".
			   "rp.prioridad AS perfil_prioridad, ".
			   "c.usa_attools, c.usa_horariohabil, ".
			   "(SELECT preferencia_semaforo_id FROM public.cliente_usuario_miperfil(cliente_usuario_id)) as periodo_semaforo_id, ".
			   "(now() - cu.preferencia_semaforo_periodo) AS periodo_semaforo_inicio ".
			   "FROM cliente_usuario cu, cliente c, reporte_perfil rp ".
			   "WHERE cu.cliente_id=c.cliente_id ".
			   "AND cu.reporte_perfil_id=rp.reporte_perfil_id ".
			   "AND cu.cliente_usuario_id=".pg_escape_string($this->usuario_id);
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if ($row = $res->fetchRow()) {
			$this->clave_md5 = $row["clave"];
			$this->nombre = $row["cliente_usuario_nombre"];
			$this->email = $row["cliente_usuario_email"];
			$this->telefono = $row["cliente_usuario_telefono"];
			$this->cargo = $row["cliente_usuario_cargo"];
			$this->zona_horaria_id = $row["zona_horaria_id"];
			$this->idioma_id = $row["lenguaje_id"];
			$this->pais_id = $row["pais_id"];
			$this->cliente_id = $row["cliente_id"];
			$this->cliente_nombre = $row["cliente_nombre"];
			$this->perfil_id = $row["perfil_id"];
			$this->perfil_nombre = $row["perfil_nombre"];
			$this->perfil_prioridad = $row["perfil_prioridad"];
			$this->periodo_semaforo_id = $row["periodo_semaforo_id"];
			$this->periodo_semaforo_inicio = $row["periodo_semaforo_inicio"];
			$this->orientacion_semaforo = $row["orientacion_semaforo"];
//			$this->usa_googleanalytics = ($row["usa_googleanalytics"]=='t')?true:false;
			$this->usa_horariohabil = ($row["usa_horariohabil"]=='t')?true:false;
			$this->usa_herramientas = ($row["usa_attools"]=='t')?true:false;
			$this->cnt_usuarios = $row["max_usuarios"];
			$this->cnt_notificaciones = $row["max_notificaciones_por_usuario"];
			$this->cnt_horariohabil = $row["max_horariohabil"];
			$this->solo_lectura = $mdb2->only_read;
		}
	}

	/**
	 * Funcion que es utilizada para obtener el usuario_id a partir del email,
	 * esta funcion es utilizada solo despues de logearse.
	 *
	 * @param integer $grupo_id
	 * @return integer $usuario_id
	 */
	function UsuarioId($email) {
		global $mdb2;
		global $log;

		$sql = "SELECT cliente_usuario_id ".
			   "FROM cliente_usuario ".
			   "WHERE lower(email)=lower('".pg_escape_string($email)."')";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if ($row = $res->fetchRow()) {
			return $row["cliente_usuario_id"];
		}
		else {
			return 0;
		}
	}


	/*************** DATOS ***************/
	/*************** DATOS ***************/
	/*************** DATOS ***************/

	/**
	 * Funcion para obtener los objetivos del usuario.
	 *
	 * @param string $tipo
	 * @param integer $objetivo_id
	 * @return array<Objetivo>
	 */
	function getObjetivos($tipo, $objetivo_id = 0) {
		global $mdb2;
		global $log;

		if ($tipo == REP_DATOS_USUARIO) {
			$sql = "SELECT * FROM public.objetivo_lista_detalle(".
					pg_escape_string($this->usuario_id).",'online') ";
		}
		elseif ($tipo == REP_DATOS_PERIODICOS) {
			$sql = "SELECT * FROM public.objetivo_lista_detalle(".
					pg_escape_string($this->usuario_id).",'periodico') ";
		}
		//se agrega el nuevo servicio
		elseif ($tipo == REP_DATOS_NOTIFICACION) {
			$sql = "SELECT * FROM public.cliente_lista_objetivos(".
					pg_escape_string($this->usuario_id).") ".
				   "WHERE servicio_id IN (1,5,11,12,13,14,25,27,55,62,70,71,101,255,256,257,258,270,271,272,290,400,666,700) ".
				   "ORDER BY objetivo_nombre";
        }
        elseif ($tipo == REP_DATOS_MONITOREO) {
        	$sql = "SELECT * FROM public.cliente_lista_objetivos(".
        			pg_escape_string($this->usuario_id).") ".
        			"WHERE servicio_id IN (1,5,6,11,12,13,14,25,27,55,62,70,71,101,255,256,257,268,270,271,272,400,666) ".
        			"ORDER BY objetivo_nombre";
        }
        elseif ($tipo == REP_DATOS_ESPECIALES) {
        	$sql = "SELECT * FROM public.objetivo_lista_detalle(".
        			pg_escape_string($this->usuario_id).") ".
        			"WHERE servicio_id IN (800) ".
        			"ORDER BY objetivo_nombre";
        }
		elseif ($tipo == REP_DATOS_CLIENTE) {
			$sql = "SELECT * FROM public.cliente_lista_objetivos(".
					pg_escape_string($this->usuario_id).") ";
			if ($objetivo_id>0) {
				$sql.= "WHERE objetivo_id=".pg_escape_string($objetivo_id);
			}
			else {
				$sql.= "ORDER BY objetivo_nombre";
			}
		}
		/*SE AGREGA CONDICION PARA MOSTRAR NUEVO SERVICIO NEW RELIC APM*/
		elseif ($tipo == REP_DATOS_NEW_RELIC_APM){
                    $sql = "SELECT * FROM public.objetivo_lista_detalle(".
        			pg_escape_string($this->usuario_id).") ".
        			"WHERE servicio_id IN (810) ".
        			"ORDER BY objetivo_nombre";
		}
        /*SE AGREGA CONDICION PARA MOSTRAR NUEVO SERVICIO NEW RELIC RUM*/
		elseif ($tipo == REP_DATOS_NEW_RELIC_RUM){
                    $sql = "SELECT * FROM public.objetivo_lista_detalle(".
        			pg_escape_string($this->usuario_id).") ".
        			"WHERE servicio_id IN (811) ".
        			"ORDER BY objetivo_nombre";
        }
         /*SE AGREGA CONDICION PARA MOSTRAR NUEVO SERVICIO NEW RELIC MOBILE*/
		elseif ($tipo == REP_DATOS_NEW_RELIC_MOBILE){
                    $sql = "SELECT * FROM public.objetivo_lista_detalle(".
        			pg_escape_string($this->usuario_id).") ".
        			"WHERE servicio_id IN (812) ".
        			"ORDER BY objetivo_nombre";
        }
        /*SE AGREGA CONDICION PARA MOSTRAR NUEVO SERVICIO AUDEX*/
        elseif ($tipo == REP_DATOS_AUDEX){
        $sql = "SELECT * FROM public.objetivo_lista_detalle(".
        			pg_escape_string($this->usuario_id).") ".
                			"WHERE servicio_id IN (801) ".
        			"ORDER BY objetivo_nombre";
        }
        /*SE AGREGA CONDICION PARA MOSTRAR NUEVO SERVICIO ATDEX*/
        elseif ($tipo == REP_DATOS_ATDEX){
        	$sql = "SELECT * FROM public.objetivo_lista_detalle(".
        			pg_escape_string($this->usuario_id).") ".
        			"WHERE servicio_id IN (802) ".
        			"ORDER BY objetivo_nombre";
        }


		//echo($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$sql2="SELECT * FROM _to_cliente_tz(".pg_escape_string($this->usuario_id).",CURRENT_TIMESTAMP)";

		$res2 =& $mdb2->query($sql2);
		if (MDB2::isError($res2)) {
			$log->setError($sql2, $res->userinfo);
			exit();
		}
		$row2 = $res2->fetchRow();
		$fecha=$row2["_to_cliente_tz"];


		$this->__objetivos = array();
		while ($row = $res->fetchRow()) {
			$objetivo = new Objetivo($row["objetivo_id"]);
			$objetivo->nombre = $row["objetivo_nombre"];
			$objetivo->descripcion = $row["objetivo_descripcion"];


			if(($row["objetivo_fecha_expiracion"] =='')||(strtotime($fecha)<strtotime($row["objetivo_fecha_expiracion"]))){
				$objetivo->es_activo = true;
			}
			else{
				$objetivo->es_activo = false;
			}

			$servicio = new Servicio($row["servicio_id"]);
			$servicio->nombre = $row["servicio_nombre"];
			$objetivo->__servicio = $servicio;

			$this->__objetivos[$row["objetivo_id"]] = $objetivo;
		}
		return $this->__objetivos;
	}

	/**
	 * Funcion para obtener un objetivo especifico del usuario.
	 *
	 * @param integer $objetivo_id
	 * @return Objetivo
	 */
	function getObjetivo($objetivo_id, $datos_tipo = REP_DATOS_CLIENTE) {
		if (!isset($this->__objetivos[$objetivo_id])) {
			$this->getObjetivos($datos_tipo, $objetivo_id);
		}
		if (isset($this->__objetivos[$objetivo_id])) {
			return $this->__objetivos[$objetivo_id];
		}
		else {
			return null;
		}
	}

	function getConfigObjetivo($objetivo_id, $datos_tipo = REP_DATOS_USUARIO) {
		if (!isset($this->__objetivos[$objetivo_id])) {
			$this->getObjetivos($datos_tipo, $objetivo_id);
		}
		if (isset($this->__objetivos[$objetivo_id])) {
			return new ConfigObjetivo($objetivo_id);
		}
		else {
			return null;
		}
	}

	/**
	 * Funcion para obtener los monitores del usuario.
	 *
	 * @param string $tipo
	 * @param integer $monitor_id
	 * @return array<Monitor>
	 */
	function getMonitores($tipo, $monitor_id = 0, $nodo_id = 0) {
		global $mdb2;
		global $log;

		if ($tipo == REP_DATOS_USUARIO) {
			$sql = "SELECT * FROM public.cliente_usuario_monitores(".
					pg_escape_string($this->usuario_id).")";
		}
		elseif ($tipo == REP_DATOS_CLIENTE) {
			$sql = "SELECT * FROM public.cliente_herramienta_lista_monitor(".
					pg_escape_string($this->usuario_id).")";
		}

		if ($monitor_id>0) {
			$sql.= "WHERE monitor_id=".pg_escape_string($monitor_id);
		}

		if ($nodo_id>0) {
			$sql.= "WHERE nodo_id=".pg_escape_string($nodo_id);
		}
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$this->__monitores = array();
		while ($row = $res->fetchRow()) {
			$monitor = new Monitor($row["monitor_id"]);
			$monitor->nombre = $row["nodo_nombre"];
			$monitor->nodo_id = $row["nodo_id"];
			$monitor->descripcion = $row["nodo_descripcion"];
			$monitor->pais_id = $row["pais_id"];
			$monitor->pais_nombre = $row["pais_nombre"];
			$monitor->host = $row["monitor_hostname"];
			$this->__monitores[$row["monitor_id"]] = $monitor;
		}
		return $this->__monitores;
	}

	/**
	 * Funcion para obtener un monitor especifico del usuario.
	 *
	 * @param integer $monitor_id
	 * @return array<Monitor>
	 */
	function getMonitor($monitor_id) {
		if (!isset($this->__monitores[$monitor_id])) {
			$this->getMonitores(REP_DATOS_CLIENTE, $monitor_id);
		}
		if (isset($this->__monitores[$monitor_id])) {
			return $this->__monitores[$monitor_id];
		}
		else {
			return null;
		}
	}



	/**
	 * Funcion para obtener un monitor especifico del usuario llamando el id del Nodo.
	 *
	 * @param integer $nodo_id
	 * @return array<Monitor>
	 */
	function getNodo($nodo_id) {
		//if (!isset($this->__monitores)) {
		//	$this->getMonitores(REP_DATOS_USUARIO, 0,$nodo_id);
		//}
		//if (isset($this->__monitores)) {
		//	return $this->__monitores;
		//}
		global $mdb2;
		global $log;
		if(!$nodo_id){
			return null;
		}

		$sql = "SELECT * FROM public.nodo where nodo_id = ".$nodo_id;
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if($row = $res->fetchRow()) {
			$nodo = new stdClass();
			$nodo->nodo_id = $row["nodo_id"];
			$nodo->nombre = $row["nombre"];
			$nodo->descripcion = $row["descripcion"];
			$nodo->estado = $row["estado"];
			$nodo->es_publico = $row["es_publico"];
			$nodo->cliente_id = $row["cliente_id"];
			$nodo->pais_id = $row["pais_id"];
			$nodo->icono = $row["icono"];
			$nodo->orden = $row["orden"];
			$nodo->titulo = $row["titulo"];
			$nodo->subtitulo = $row["subtitulo"];
			return $nodo;
		}else{
			return null;
		}
	}

	/**
	 * Funcion para obtener las notificaciones del usuario.
	 *
	 * @return array<Notificacion>
	 */
	function getNotificaciones($notificacion_id = 0) {
		global $mdb2;
		global $log;

		$sql = "SELECT * FROM notificacion_lista_detalle(".
				pg_escape_string($this->usuario_id).") ";

		if ($notificacion_id>0) {
			$sql.= "WHERE notificacion_id=".pg_escape_string($notificacion_id);
		}
//		print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$this->__notificaciones = array();
		while ($row = $res->fetchRow()) {
			$notificacion = new Notificacion($row["notificacion_id"]);
			$notificacion->escalabilidad_desde = $row["notificacion_envia_desde"];
			$notificacion->escalabilidad_hasta = $row["notificacion_envia_hasta"];
			$notificacion->uptime_parcial =($row["notificacion_alerta_uptime_ok"]=='t')?true:false;
			$notificacion->downtime_parcial =($row["notificacion_alerta_parcial"]=='t')?true:false;
			$notificacion->downtime_grupal =($row["notificacion_alerta_grupal"]=='t')?true:false;
			$notificacion->downtime_global =($row["notificacion_alerta_global"]=='t')?true:false;
			$notificacion->sla = ($row["notificacion_alerta_sla"]=='t')?true:false;
			$notificacion->patron_inverso = ($row["notificacion_patron_inverso"]=='t')?true:false;
			$notificacion->intervalo_id = $row["intervalo_id"];
			$notificacion->usuario_cliente_id = $row["cliente_usuario_id"];

			$destinatario = new Destinatario($row["notificacion_destinatario_id"]);
			$destinatario->nombre = $row["notificacion_destinatario_nombre"];
			$notificacion->__destinatario = $destinatario;

			$objetivo = new Objetivo($row["objetivo_id"]);
			$objetivo->nombre = $row["objetivo_nombre"];
			$notificacion->__objetivo = $objetivo;

			$horario = new Horario($row["horario_id"]);
			$horario->nombre = $row["horario_nombre"];
			$notificacion->__horario = $horario;

			$this->__notificaciones[$row["notificacion_id"]] = $notificacion;
		}
		return $this->__notificaciones;
	}


	/**
	 * Funcion para obtener las notificaciones del usuario.
	 *
	 * @return array<Notificacion>
	 */
	function getNotificacionesObjetivo($notificacion_id = 0, $objetivo_id) {
		global $mdb2;
		global $log;

		$sql = "SELECT * FROM notificacion_lista_detalle_2(".
				pg_escape_string($this->usuario_id).",".$objetivo_id.") ";

		if ($notificacion_id>0) {
			$sql.= "WHERE notificacion_id=".pg_escape_string($notificacion_id);
		}
//		print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$this->__notificaciones = array();
		while ($row = $res->fetchRow()) {
			$notificacion = new Notificacion($row["notificacion_id"]);
			$notificacion->escalabilidad_desde = $row["notificacion_envia_desde"];
			$notificacion->escalabilidad_hasta = $row["notificacion_envia_hasta"];
			$notificacion->uptime_parcial =($row["notificacion_alerta_uptime_ok"]=='t')?true:false;
			$notificacion->downtime_parcial =($row["notificacion_alerta_parcial"]=='t')?true:false;
			$notificacion->downtime_grupal =($row["notificacion_alerta_grupal"]=='t')?true:false;
			$notificacion->downtime_global =($row["notificacion_alerta_global"]=='t')?true:false;
			$notificacion->sla = ($row["notificacion_alerta_sla"]=='t')?true:false;
			$notificacion->patron_inverso = ($row["notificacion_patron_inverso"]=='t')?true:false;
			$notificacion->intervalo_id = $row["intervalo_id"];
			$notificacion->usuario_cliente_id = $row["cliente_usuario_id"];

			$destinatario = new Destinatario($row["notificacion_destinatario_id"]);
			$destinatario->nombre = $row["notificacion_destinatario_nombre"];
			$notificacion->__destinatario = $destinatario;

			$objetivo = new Objetivo($row["objetivo_id"]);
			$objetivo->nombre = $row["objetivo_nombre"];
			$notificacion->__objetivo = $objetivo;

			$horario = new Horario($row["horario_id"]);
			$horario->nombre = $row["horario_nombre"];
			$notificacion->__horario = $horario;

			$this->__notificaciones[$row["notificacion_id"]] = $notificacion;
		}
		return $this->__notificaciones;
	}

	/**
	 * Funcion para obtener una notificacion especifica del usuario.
	 * Necesita ejecutarse antes getNotificaciones().
	 *
	 * @param integer $notificacion_id
	 * @return Notificacion
	 */
	function getNotificacion($notificacion_id) {
		if (!isset($this->__notificaciones[$notificacion_id])) {
			$this->getNotificaciones($notificacion_id);
		}
		if (isset($this->__notificaciones[$notificacion_id])) {
			return $this->__notificaciones[$notificacion_id];
		}
		else {
			return null;
		}
	}

	/**
	 * Funcion para obtener los destinatarios del usuario.
	 *
	 * @return array<Destinatario>
	 */
	function getDestinatarios($destinatario_id = 0) {
		global $mdb2;
		global $log;

		$sql = "SELECT * FROM notificacion_destinatario_lista_detalle(".
				pg_escape_string($this->usuario_id).") ";

		if ($destinatario_id>0) {
			$sql.= "WHERE notificacion_destinatario_id=".pg_escape_string($destinatario_id);
		}

		$res =& $mdb2->query($sql);
		//print($sql."<br>");
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$this->__destinatarios = array();
		while ($row = $res->fetchRow()) {
			$destinatario = new Destinatario($row["notificacion_destinatario_id"]);
			$destinatario->nombre = $row["notificacion_destinatario_nombre"];
			$destinatario->contacto = $row["notificacion_destinatario_destinatario"];
			$destinatario->tipo_id = $row["notificacion_tipo_id"];
			$destinatario->tipo_nombre = $row["notificacion_tipo_nombre"];
			$destinatario->usuario_cliente_id = $row["cliente_usuario_id"];
			$destinatario->telefono = $row["notificacion_destinatario_telefono"];
			$this->__destinatarios[$row["notificacion_destinatario_id"]] = $destinatario;
		}
		return $this->__destinatarios;
	}

	/**
	 * Funcion para obtener un destinatario especifico del usuario.
	 * Necesita ejecutarse antes getDestinatarios().
	 *
	 * @param integer $destinatario_id
	 * @return Destinatario
	 */
	function getDestinatario($destinatario_id) {
		if (!isset($this->__destinatarios[$destinatario_id])) {
			$this->getDestinatarios($destinatario_id);
		}
		if (isset($this->__destinatarios[$destinatario_id])) {
			return $this->__destinatarios[$destinatario_id];
		}
		else {
			return null;
		}
	}

	/**
	 * Funcion para obtener los horarios del usuario.
	 * Los horarios retornados son segun el tipo de horario.
	 *
	 * @param integer $tipo_horario
	 * @return array<Horario>
	 */
	function getHorarios($tipo_horario, $horario_id = 0,$user=false) {
		global $mdb2;
		global $log;

/*		if ($tipo_horario==REP_HORARIO_TODOS) {
			$sql = "SELECT * FROM horario_lista_detalle(".
					pg_escape_string($this->usuario_id).") ";
		}
		elseif ($tipo_horario==REP_HORARIO_ASIGNABLE) {
			$sql = "SELECT * FROM horario_lista_publicoatentus_detalle(".
					pg_escape_string($this->usuario_id).") ";
		}
		else {
			$sql = "SELECT * FROM horario_lista_detalle(".
					pg_escape_string($this->usuario_id).", ".
					pg_escape_string($tipo_horario).") ";
		}*/
		if($user){
			$sql = "SELECT h.* FROM public.horario AS h ".
					"WHERE h.cliente_usuario_id = ".$this->usuario_id;

		}else{
		$sql = "SELECT h.* FROM public.horario AS h, public.cliente_usuario AS cu ".
			   "WHERE h.cliente_id = cu.cliente_id AND cu.cliente_usuario_id = ".$this->usuario_id;
		}

		if ($horario_id > 0) {
			$sql.= " AND h.horario_id=".pg_escape_string($horario_id);
		}
		if ($tipo_horario > 0) {
			$sql.= " AND h.horario_tipo_id=".pg_escape_string($tipo_horario);
		}
// 		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$this->__horarios = array();
		while ($row = $res->fetchRow()) {
			$horario = new Horario($row["horario_id"]);
			$horario->nombre = $row["nombre"];
			$horario->descripcion = $row["descripcion"];
			$horario->tipo_id = $row["horario_tipo_id"];
//			$horario->tipo_nombre = $row["horario_tipo_nombre"];
			$horario->usuario_cliente_id = $this->usuario_id;
			$this->__horarios[$row["horario_id"]] = $horario;
		}
		return $this->__horarios;
	}

	/**
	 * Funcion para obtener un horario especifico del usuario.
	 * Necesita ejecutarse antes getHorarios().
	 *
	 * @param integer $horario_id
	 * @return Horario
	 */
	function getHorario($horario_id, $horario_tipo = REP_HORARIO_TODOS) {
		if ($horario_id == 0) {
			$horario = new Horario(0);
			$horario->nombre = "Todo Horario";
			$this->__horarios[0] = $horario;
		}
		elseif (!isset($this->__horarios[$horario_id])) {
			$this->getHorarios($horario_tipo, $horario_id);
		}
		if (isset($this->__horarios[$horario_id])) {
			return $this->__horarios[$horario_id];
		}
		else {
			return null;
		}
	}

	/**
	 * Funcion para obtener los usuarios que compartes el mismo cliente
	 * que el usuario logeado.
	 * Estos usuarios se demoninaran usuario_cliente.
	 *
	 * @return array<Usuario>
	 */
	function getUsuariosCliente($usuario_cliente_id = 0) {
		global $mdb2;
		global $log;

		$sql = "SELECT * FROM cliente_lista_usuarios(".
				pg_escape_string($this->usuario_id).") ";

		if ($usuario_cliente_id>0) {
			$sql.= "WHERE cliente_usuario_id=".pg_escape_string($usuario_cliente_id);
		}
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$this->__usuarios_cliente = array();
		while ($row = $res->fetchRow()) {
			$usuario_cliente = new Usuario($row["cliente_usuario_id"]);
			$usuario_cliente->usuario_id = $row["cliente_usuario_id"];
			$usuario_cliente->nombre = $row["cliente_usuario_nombre"];
			$usuario_cliente->email = $row["cliente_usuario_email"];
			$usuario_cliente->telefono = $row["cliente_usuario_telefono"];
			$usuario_cliente->cargo = $row["cliente_usuario_cargo"];
			$usuario_cliente->zona_horaria_id = $row["zona_horaria_id"];
			$usuario_cliente->idioma_id = $row["lenguaje_id"];
			$usuario_cliente->cliente_id = $row["cliente_id"];
			$usuario_cliente->cliente_nombre = $row["cliente_nombre"];
			$usuario_cliente->perfil_id = $row["perfil_id"];
			$usuario_cliente->perfil_nombre = $row["perfil_nombre"];
			$usuario_cliente->perfil_prioridad = $row["perfil_prioridad"];
			$this->__usuarios_cliente[$row["cliente_usuario_id"]] = $usuario_cliente;
		}
		return $this->__usuarios_cliente;
	}

	/**
	 * Funcion para obtener un usuario_cliente especifico.
	 * Necesita ejecutarse antes getUsuariosCliente().
	 *
	 * @param integer $usuario_cliente_id
	 * @return Usuario
	 */
	function getUsuarioCliente($usuario_cliente_id) {
		if (!isset($this->__usuarios_cliente[$usuario_cliente_id])) {
			$this->getUsuariosCliente($usuario_cliente_id);
		}
		if (isset($this->__usuarios_cliente[$usuario_cliente_id])) {
			return $this->__usuarios_cliente[$usuario_cliente_id];
		}
		else {
			return null;
		}
	}

	/**
	 * Funcion para obtener los subclientes del usuario.
	 *
	 * @return array<SubCliente>
	 */
	function getSubClientes($tipo, $subcliente_id = 0) {
		global $mdb2;
		global $log;

		if ($tipo == REP_DATOS_USUARIO) {
			$sql = "SELECT s.cliente_subcliente_id, s.nombre, s.descripcion ".
				   "FROM public.cliente_subcliente s, cliente_mapa_subcliente_usuario su ".
				   "WHERE s.cliente_subcliente_id=su.cliente_subcliente_id ".
				   "AND su.cliente_usuario_id=".
					pg_escape_string($this->usuario_id);
		}
		elseif ($tipo == REP_DATOS_CLIENTE) {
			$sql = "SELECT * FROM public.cliente_subcliente_lista_detalle(".
					pg_escape_string($this->usuario_id).") ";

			if ($subcliente_id > 0) {
				$sql.= "WHERE cliente_subcliente_id=".pg_escape_string($subcliente_id);
			}
			else {
				$sql.= "ORDER BY nombre";
			}
		}

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$this->__subclientes = array();
		while ($row = $res->fetchRow()) {
			$subcliente = new SubCliente($row["cliente_subcliente_id"]);
			$subcliente->nombre = $row["nombre"];
			$subcliente->descripcion = $row["descripcion"];
//			$subcliente->readonly = ($row["readonly"]=='t')?true:false;
			$this->__subclientes[$row["cliente_subcliente_id"]] = $subcliente;
		}
		return $this->__subclientes;
	}


	/**
	 * Funcion para obtener un subcliente especifico del usuario.
	 * Necesita ejecutarse antes getSubClientes().
	 *
	 * @param integer $subcliente_id
	 * @return SubCliente
	 */
	function getSubCliente($subcliente_id) {
		if (!isset($this->__subclientes[$subcliente_id])) {
			$this->getSubClientes(REP_DATOS_CLIENTE, $subcliente_id);
		}
		if (isset($this->__subclientes[$subcliente_id])) {
			return $this->__subclientes[$subcliente_id];
		}
		else {
			return null;
		}
	}

	/**
	 * Funcion que es utilizada para obtener los perfiles de usuario
	 * que puede modificar el usuario actual.
	 *
	 * @return array<String>
	 */
	function getTiposPerfiles($tipo, $perfil_id = 0) {
		global $mdb2;
		global $log;

		if ($tipo == REP_DATOS_CLIENTE) {
			$sql = "SELECT * FROM public.reporte_perfil ";
		}
		else {
			$sql = "SELECT * FROM public.reporte_perfil_detalle(".
					pg_escape_string($this->usuario_id).") ";
		}

		if ($perfil_id > 0) {
			$sql.= "WHERE reporte_perfil_id=".pg_escape_string($perfil_id);
		}
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$tipos_perfiles = array();
		while ($row = $res->fetchRow()) {
			$tipos_perfiles[$row["reporte_perfil_id"]]["nombre"] = $row["nombre"];
			$tipos_perfiles[$row["reporte_perfil_id"]]["descripcion"] = $row["descripcion"];
			$tipos_perfiles[$row["reporte_perfil_id"]]["prioridad"] = $row["prioridad"];
		}
		return $tipos_perfiles;
	}

	function getTipoPerfil($perfil_id, $tipo) {
		$perfiles = $this->getTiposPerfiles($tipo, $perfil_id);
		if (isset($perfiles[$perfil_id])) {
			return $perfiles[$perfil_id];
		}
		else {
			return null;
		}
	}

	function getTokensWS($token_id = 0) {
		global $mdb2;
		global $log;

		$sql = "SELECT * FROM public.webservices_token ".
			   "WHERE cliente_id=".pg_escape_string($this->cliente_id)." ";

		if ($token_id > 0) {
			$sql.= "AND token_id=".pg_escape_string($token_id);
		}
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$this->__tokens_ws = array();
		while ($row = $res->fetchRow()) {
			$token = new TokenWS($row["token_id"]);
			$token->nombre = $row["nombre"];
			$token->key = $row["key"];
			$token->fecha_creacion = $row["fecha_creacion"];
			$token->fecha_expiracion = $row["fecha_expiracion"];
			$this->__tokens_ws[$row["token_id"]] = $token;
		}
		return $this->__tokens_ws;
	}

	function getTokenWS($token_id) {
		if (!isset($this->__tokens_ws[$token_id])) {
			$this->getTokensWS($token_id);
		}
		if (isset($this->__tokens_ws[$token_id])) {
			return $this->__tokens_ws[$token_id];
		}
		else {
			return null;
		}
	}

	function getPonderacion() {
		global $mdb2;
		global $log;

		$sql = "SELECT * FROM public.cliente_ponderacion ".
			   "WHERE cliente_id=".pg_escape_string($this->cliente_id);
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if ($row = $res->fetchRow()) {
			$ponderacion = new Ponderacion();
			$ponderacion->ponderacion_id = $row["ponderacion_id"];
			$ponderacion->nombre = $row["nombre"];
			$ponderacion->intervalo = $row["intervalo"];
			return $ponderacion;
		}
		else {
			return null;
		}
	}


	/*************** ACCIONES ***************/
	/*************** ACCIONES ***************/
	/*************** ACCIONES ***************/

	/**
	 * Funcion que agrega un usuario.
	 *
	 * @return integer $usuario_id
	 */
	function agregar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		Validador::campoVacio($this->email, "E-Mail");
		Validador::campoVacio($this->nombre, "Nombre");
		Validador::campoVacio($this->clave, "Clave");
		Validador::campoEmail($this->email, "E-Mail");
		Validador::existeEmailUsuario(true, $this->email);
		Validador::existeNombreUsuario(true, $this->nombre);

		$sql = "SELECT * FROM public.cliente_usuario_agrega(".
				pg_escape_string($current_usuario_id).", LOWER(TRIM('".
				pg_escape_string($this->email)."')), '".
				pg_escape_string($this->clave)."', TRIM('".
				pg_escape_string($this->nombre)."'), TRIM('".
				pg_escape_string($this->telefono)."'), TRIM('".
				pg_escape_string($this->cargo)."'), ".
				pg_escape_string($this->zona_horaria_id).", ".
				pg_escape_string($this->idioma_id).", ".
				pg_escape_string($this->perfil_id).")";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$log->setChange("AGREGO USUARIO", $this->toString());

		if ($row = $res->fetchRow()) {
			$this->usuario_id = $row["cliente_usuario_agrega"];
		}
	}

	/**
	 * Funcion que modifica un usuario.
	 */
	function modificar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		Validador::campoVacio($this->email, "E-Mail");
		Validador::campoVacio($this->nombre, "Nombre");
		Validador::campoEmail($this->email, "E-Mail");
		Validador::existeEmailUsuario(true, $this->email, $this->usuario_id);
		Validador::existeNombreUsuario(true, $this->nombre, $this->usuario_id);

		$sql = "SELECT * FROM public.cliente_usuario_modifica(".
				pg_escape_string($current_usuario_id).", ".
				pg_escape_string($this->usuario_id).", LOWER(TRIM('".
				pg_escape_string($this->email)."')), ".
				(($this->clave=="")?"NULL":"'".pg_escape_string($this->clave)."'").", TRIM('".
				pg_escape_string($this->nombre)."'), TRIM('".
				pg_escape_string($this->telefono)."'), TRIM('".
				pg_escape_string($this->cargo)."'), ".
				pg_escape_string($this->zona_horaria_id).", ".
				pg_escape_string($this->idioma_id).", ".
				pg_escape_string($this->perfil_id).")";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$log->setChange("MODIFICO USUARIO", $this->toString());
	}

	/**
	 * Funcion que modifica el perfil del usuario.
	 */
	function modificarPerfil() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		$sql = "SELECT * FROM public.cliente_usuario_miperfil(".
				pg_escape_string($current_usuario_id).", TRIM('".
				pg_escape_string($this->nombre)."'), TRIM('".
				pg_escape_string($this->telefono)."'), TRIM('".
				pg_escape_string($this->cargo)."'), ".
				pg_escape_string($this->zona_horaria_id).", ".
				"(SELECT valor FROM intervalo WHERE intervalo_id=".pg_escape_string($this->periodo_semaforo_id)."), ".
				pg_escape_string($this->orientacion_semaforo).", ".
				(($this->clave=="")?"NULL":"'".pg_escape_string($this->clave)."'").")";

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}

	/**
	 * Funcion que elimina un usuario.
	 */
	function eliminar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}

		$sql = "SELECT * FROM public.cliente_usuario_elimina(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->usuario_id).")";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$log->setChange("ELIMINO USUARIO", $this->toString());
	}


	/*************** VALIDACIONES ***************/
	/*************** VALIDACIONES ***************/
	/*************** VALIDACIONES ***************/

	/**
	 * Funcion que indica si este usuario puede ser modificado.
	 *
	 * @return boolean
	 */
	function puedeModificar() {
		global $usr;
		if (!isset($this->perfil_prioridad)) {
			$perfil = $this->getTipoPerfil($this->perfil_id, REP_DATOS_CLIENTE);
			$this->perfil_prioridad = $perfil["prioridad"];
		}

		if ($this->perfil_prioridad < $usr->perfil_prioridad) {
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Funcion que indica si este usuario puede ser eliminado.
	 *
	 * @return boolean
	 */
	function puedeEliminar() {
		global $usr;
		if (!isset($this->perfil_prioridad)) {
			$perfil = $this->getTipoPerfil($this->perfil_id, REP_DATOS_CLIENTE);
			$this->perfil_prioridad = $perfil["prioridad"];
		}

		if ($this->usuario_id == $usr->usuario_id or
			$this->perfil_prioridad < $usr->perfil_prioridad) {
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Funcion que indica si el usuario puede logearse
	 * en sitio reportes.
	 *
	 * @return boolean
	 */
	function puedeLogin() {
		global $mdb2;
		global $log;

		$sql = "SELECT 1 FROM cliente c, cliente_usuario cu ".
			   "WHERE cu.cliente_id=c.cliente_id ".
			   "AND (c.fecha_expiracion>now() OR c.fecha_expiracion IS NULL) ".
			   "AND (cu.fecha_expiracion>now() OR cu.fecha_expiracion IS NULL) ".
			   "AND cu.cliente_usuario_id=".pg_escape_string($this->usuario_id);
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$sql_bloqued ="SELECT 1 FROM cliente_usuario 
											WHERE fecha_expiracion::date = '1900-12-01'
											AND cliente_usuario_id =".pg_escape_string($this->usuario_id);

		$res_block =& $mdb2->query($sql_bloqued);
		if (MDB2::isError($res_block)) {
			$log->setError($sql_bloqued, $res_block->userinfo);
			exit();
		}
		
		if ($row = $res->fetchRow()) {
			return 'userNoExpired';
		}
		else {
			if ($row = $res_block->fetchRow()) {
				return 'userBloqued';
			}else{
				return 'userExpired';
			}
		}
	}

	function puedeAgregarNotificaciones() {
		global $mdb2;
		global $log;

		$sql = "SELECT count(*) as cantidad FROM notificacion n, notificacion_destinatario nd ".
			   "WHERE n.notificacion_destinatario_id=nd.notificacion_destinatario_id ".
			   "AND nd.cliente_id=".pg_escape_string($this->cliente_id);
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if ($row = $res->fetchRow() and $row["cantidad"] < $this->cnt_notificaciones) {
			return $this->cnt_notificaciones - $row["cantidad"];
		}
		else {
			return 0;
		}
	}

	function puedeAgregarUsuarios() {
		global $mdb2;
		global $log;

		$sql = "SELECT count(*) as cantidad FROM cliente_usuario ".
			   "WHERE cliente_id=".pg_escape_string($this->cliente_id);
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if ($row = $res->fetchRow() and $row["cantidad"] < $this->cnt_usuarios) {
			return $this->cnt_usuarios - $row["cantidad"];
		}
		else {
			return 0;
		}
	}

	function puedeAgregarHorarioHabil() {
		global $mdb2;
		global $log;

		$sql = "SELECT count(*) as cantidad FROM horario ".
			   "WHERE horario_tipo_id=".REP_HORARIO_HABIL." ".
			   "AND cliente_id=".pg_escape_string($this->cliente_id);
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		if ($row = $res->fetchRow() and $row["cantidad"] < $this->cnt_horariohabil) {
			return $this->cnt_horariohabil - $row["cantidad"];
		}
		else {
			return 0;
		}
	}

	/*************** OTRAS ***************/
	/*************** OTRAS ***************/
	/*************** OTRAS ***************/

	/**
	 * Funcion que obtiene el horario por defecto del
	 * usuario para un objetivo.
	 *
	 * @param integer $objetivo_id
	 * @return integer $horario_id
	 */
	function getHorarioPorDefecto($objetivo_id) {
		global $mdb2;
		global $log;

		$sql = "SELECT * FROM public.cliente_usuario_preferencia_horario(".
				pg_escape_string($this->usuario_id).",".
				pg_escape_string($objetivo_id).")";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			return $row["cliente_usuario_preferencia_horario"];
		}
		else {
			return 0;
		}
	}

	/**
	 * Funcion que guarda el horario por defecto seleccionado
	 * por el usuario para un objetivo.
	 * 
	 * @param integer $objetivo_id
	 * @param integer $horario_id
	 */
	function setHorarioPorDefecto($objetivo_id, $horario_id) {
		global $mdb2;
		global $log;

		$sql = "SELECT * FROM public.cliente_usuario_preferencia_horario(".
				pg_escape_string($this->usuario_id).",".
				pg_escape_string($objetivo_id).",".
				pg_escape_string($horario_id).")";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}

	/**
	 * Funcion que es utilizada para mostrar en un string
	 * los datos mas importantes del usuario.
	 * Uso exclusivo para Log.
	 *
	 * @return string
	 */
	function toString() {
		$string = "NOMBRE ".$this->nombre.", ".
				  "EMAIL ".$this->email.", ".
				  "PERFIL ".$this->perfil_nombre.", ".
				  "SEMAFORO ".$this->periodo_semaforo_id;
		return $string;
	}

	/**
	 * Funcion para update sonido semaforo del usuario.
	 *
	 * @param integer $sonido_id
	 * @return boolean
	 */

	function setSonidoSemaforo($sonido) {
		global $current_usuario_id;
		global $mdb2;
		global $log;
		if($sonido=='0'){
			$sonido='False';
		}else{
			$sonido='True';
		}
		$sql = "SELECT * FROM public.cliente_usuario_extendido where usuario_id=".pg_escape_string($current_usuario_id);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$sql = "UPDATE public.cliente_usuario_extendido SET sonido_semaforo=".$sonido." WHERE usuario_id=".$current_usuario_id;
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
			return 'update';
		}
		else {
			$sql = "INSERT INTO public.cliente_usuario_extendido (usuario_id, sonido_semaforo ) VALUES (".$current_usuario_id.", ".$sonido.")";
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
			return 'insert';
		}
	}
}

?>