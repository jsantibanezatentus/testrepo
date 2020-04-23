<?

$objetivo_id = $_REQUEST["objetivo_id"];
$horario_id = $_REQUEST["horario_id"];
$horario_tipo_id = $_REQUEST["horario_tipo_id"];
$item_id = $_REQUEST["item_id"];

//print_r($sactual);

/* SI SE INGRESA AL MENU OBJETIVOS */
if (($sactual->seccion_id == REP_SECCION_OBJETIVO or $sactual->seccion_id == REP_SECCION_OBJETIVO_TODOS) and $sactual->getPermisos(1) != '-') {

	/* SI SE MUESTRA EL FORMULARIO DE OBJETIVOS */
	if ($accion == "modificar_objetivo" and $objetivo_id) {

		$T->setFile('tpl_contenido', 'form_objetivo.tpl');
		$T->setBlock('tpl_contenido', 'PUEDE_MODIFICAR', 'puede_modificar');
		$T->setBlock('tpl_contenido', 'LISTA_MONITORES', 'lista_monitores');
		$T->setBlock('tpl_contenido', 'NO_TIENE_MONITORES', 'no_tiene_monitores');
		$T->setBlock('tpl_contenido', 'LISTA_PATRONES', 'lista_patrones');
		$T->setBlock('tpl_contenido', 'TD_LLAMADA', 'td_llamada');
		$T->setBlock('tpl_contenido', 'LINK_PATRON', 'link_patron');
		$T->setBlock('tpl_contenido', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_contenido', 'TIENE_PASOS', 'tiene_pasos');
		$T->setBlock('tpl_contenido', 'ES_MAILTRAFFIC', 'es_mailtraffic');
		$T->setBlock('tpl_contenido', 'ES_POP', 'es_pop');
		$T->setBlock('tpl_contenido', 'ES_SMTP', 'es_smtp');
		$T->setBlock('tpl_contenido', 'ES_DNSCHAOS', 'es_dnschaos');
		$T->setBlock('tpl_contenido', 'ES_DNS', 'es_dns');
		$T->setBlock('tpl_contenido', 'SIN_CONFIGURACION', 'sin_configuracion');

		/* OBTENER LOS DATOS DEL OBJETIVO */
		if ($sactual->seccion_id == REP_SECCION_OBJETIVO_TODOS) {
			$objetivo = $usr->getConfigObjetivo($objetivo_id, REP_DATOS_CLIENTE);
		}
		else {
			$objetivo = $usr->getConfigObjetivo($objetivo_id);
		}

		if ($objetivo->getServicio()->getTipoSetup() == REP_SETUP_IVR) {
			$T->setVar('__tabla_paso', 'Audio');	
			$T->setVar('__tabla_timeout','Timeout' );
			$T->setVar('__tabla_metodo', 'Dtmf');
			$T->setVar('__tabla_patron', 'Numero Llamada');
			$T->setVar('__width_paso', '39%');
			$T->setVar('__width_timeout', '8%');
			$T->setVar('__width_patron', '14%');
			$T->setVar('__width_patron', '15%');
		}else{
			
			$T->setVar('__tabla_paso', 'Paso');
			$T->setVar('__tabla_timeout', 'Timeout');
			$T->setVar('__tabla_metodo', 'M&eacute;todo');
			$T->setVar('__tabla_patron', 'Patr&oacute;n');
			$T->setVar('__width_paso', '46%');
			$T->setVar('__width_timeout', '8%');
			$T->setVar('__width_patron', '14%');
			$T->setVar('__width_patron', '8%');
		}
//		$T->setVar('__padre_id', $sactual->padre_id);
//		$T->setVar('__seccion_id', $sactual->seccion_id);
//		$T->setVar('__nivel', $sactual->nivel);
		global $current_usuario_id;
		$T->setVar('__current_usuario_id', $current_usuario_id);
		$T->setVar('__objetivo_id', $objetivo->objetivo_id);
		$T->setVar('__objetivo_nombre', $objetivo->nombre);
		$T->setVar('__objetivo_descripcion', $objetivo->descripcion);
		$T->setVar('__objetivo_timeout', ($objetivo->timeout)?$objetivo->timeout:"N/A");
		$T->setVar('__objetivo_sla_dis_ok', $objetivo->sla_dis_ok);
		$T->setVar('__objetivo_sla_dis_error', $objetivo->sla_dis_error);
		$T->setVar('__objetivo_sla_ren_ok', $objetivo->sla_ren_ok);
		$T->setVar('__objetivo_sla_ren_error', $objetivo->sla_ren_error);
		$T->setVar('__servicio_nombre', $objetivo->getServicio()->nombre);
		$T->setVar('__intervalo_nombre', $objetivo->intervalo_nombre);

		/* LA CONFIGURACION DE OBJETIVO SE MUESTRA POR MONITOR */
		$posicion = 0;
		$monitores = $objetivo->getMonitores();
		if (count($monitores) == 0) {
			$T->parse('no_tiene_monitores', 'NO_TIENE_MONITORES', false);
		}
		foreach ($monitores as $id => $monitor) {
			$T->setVar('__monitor_id', $monitor->monitor_id);
			$T->setVar('__monitor_nombre', $monitor->nombre);
			$T->setVar('__monitor_selector', $posicion);
			$T->parse('lista_monitores', 'LISTA_MONITORES', true);
		
			/* CONFIGURACION DE OBJETIVOS */
			if (in_array($objetivo->getServicio()->getTipoSetup(), array(REP_SETUP_MAIL, REP_SETUP_DNS))) {
				$setup = $objetivo->__setups[($objetivo->__setups[$id])?$id:0];
				$T->setVar('__objetivo_dominio', ($setup->dominio)?$setup->dominio:"N/A");	// POP - SMTP - TRAFFIC - A - SOA - MX
				$T->setVar('__objetivo_dominio_tipo', ($setup->dominio_tipo)?$setup->dominio_tipo:"N/A");	// TRAFFIC
				$T->setVar('__objetivo_dominio_timeout', ($setup->dominio_timeout)?$setup->dominio_timeout:"N/A");	// TRAFFIC
				$T->setVar('__objetivo_destinatario', ($setup->destinatario)?$setup->destinatario:"N/A");	// TRAFFIC
				$T->setVar('__objetivo_remitente', ($setup->remitente)?$setup->remitente:"N/A");	// TRAFFIC
				$T->setVar('__objetivo_usuario', ($setup->usuario)?$setup->usuario:"N/A");	// TRAFFIC
				$T->setVar('__objetivo_clave', ($setup->clave)?$setup->clave:"N/A");	// TRAFFIC
				$T->setVar('__objetivo_metodo', ($setup->metodo)?$setup->metodo:"N/A");	// POP
				$T->setVar('__objetivo_resolver', ($setup->resolver && $setup->resolver==".")?"Resolver por defecto del ISP/Proveedor":$setup->resolver);	// A - SOA - MX - CHAOS
				$T->setVar('__objetivo_consulta', ($setup->consulta)?$setup->consulta:"N/A");	// CHAOS
				$T->setVar('__objetivo_tipo', ($setup->tipo)?$setup->tipo:"N/A");	// CHAOS

				/* SEGUN EL SERVICIO VEO QUE CONFIGURACION MOSTRAR */
				if ($objetivo->getServicio()->servicio_id == REP_PROT_MAILTRAFFIC) {
					$T->parse('es_mailtraffic', 'ES_MAILTRAFFIC', true);
				}
				elseif ($objetivo->getServicio()->servicio_id == REP_PROT_POP) {
					$T->parse('es_pop', 'ES_POP', true);
				}
				elseif ($objetivo->getServicio()->servicio_id == REP_PROT_SMTP) {
					$T->parse('es_smtp', 'ES_SMTP', true);
				}
				elseif ($objetivo->getServicio()->servicio_id == REP_PROT_DNS_CHAOS) {
					$T->parse('es_dnschaos', 'ES_DNSCHAOS', true);
				}
				elseif (in_array($objetivo->getServicio()->servicio_id, array(REP_PROT_DNS_A, REP_PROT_DNS_MX, REP_PROT_DNS_SOA))) {
					$T->parse('es_dns', 'ES_DNS', true);
				}
			}
			
			/* CONFIGURACION DE OBJETIVO QUE CONTIENE PASOS */
			elseif (in_array($objetivo->getServicio()->getTipoSetup(), array(REP_SETUP_WEB, REP_SETUP_BROWSER, REP_SETUP_MOBILE,REP_SETUP_IVR))) {
				
				/* LISTA DE PASOS */
				$T->setVar('lista_pasos', '');
				$orden = 0;
				if (count($objetivo->__pasos) > 0) {
				foreach ($objetivo->__pasos as $paso) {
					
					$T->setVar('td_llamada', '');
					$T->setVar('link_patron', '');
					if ($objetivo->getServicio()->getTipoSetup()== REP_SETUP_IVR) {
						$setup = $paso->__setups[($paso->__setups[$paso->paso_id])?$paso->paso_id:0];
					}
					else {
						$setup = $paso->__setups[($paso->__setups[$id])?$id:0];
						//$T->setVar('__paso_id', $paso->paso_id);
					}
					$patrones = $paso->__patrones[($paso->__patrones[$id])?$id:0];
					
					$T->setVar('__paso_orden', $orden);
					$T->setVar('__paso_id', $paso->paso_id);
					$T->setVar('__paso_nombre', $paso->nombre);
					
					
					/* SI ES DE TIPO MOBILE */
					if ($objetivo->getServicio()->getTipoSetup() == REP_SETUP_MOBILE) {
						$T->setVar('__paso_url_corta', substr($setup->comando,0,45));
						$T->setVar('__paso_url', $setup->comando);
					}
					elseif ($objetivo->getServicio()->getTipoSetup() == REP_SETUP_IVR) {
						$T->setVar('__paso_url_corta',($paso->__audio)?substr($paso->__audio,0,45):"N/A");
						$T->setVar('__paso_url', ($paso->__audio)?$paso->__audio:"N/A");
					}
					else {
						$T->setVar('__paso_url_corta', ($setup->url)?substr($setup->url,0,45):"N/A");
						$T->setVar('__paso_url', ($setup->url)?$setup->url:"N/A");
					}
					
					if($objetivo->getServicio()->getTipoSetup()== REP_SETUP_IVR) {
						$T->setVar('__paso_metodo', ($paso->__dtmf)?$paso->__dtmf:"N/A");
						$T->setVar('__paso_timeout', ($setup->timeout)?$setup->timeout:"N/A");
						$T->setVar('__paso_patrones', ($paso->__numero_llamada)?$paso->__numero_llamada:"N/A");
						$T->parse('td_llamada', 'TD_LLAMADA', true);
					}
					else {
						$T->setVar('__paso_id', $paso->paso_id);
						$T->setVar('__paso_metodo', ($setup->metodo)?$setup->metodo:"POST");
						$T->setVar('__paso_timeout', ($setup->timeout)?$setup->timeout:"N/A");
						$T->setVar('__paso_patrones', count($patrones));
						$T->parse('link_patron', 'LINK_PATRON', true);
						
						/* LISTA DE PATRONES */
						$T->setVar('lista_patrones', '');
						
						if (count($patrones)>0) {
							foreach($patrones as $patron) {
								
								$T->setVar('__patron_nombre', $patron->nombre);
								$T->setVar('__patron_valor', $patron->valor);
								$T->setVar('__patron_valor_corto', substr($patron->valor,0,50));
								$T->setVar('__patron_tipo', $patron->tipo);
								$T->setVar('__patron_inverso', ($patron->es_inverso)?"Si":"No");
								$T->setVar('__patron_opcional', ($patron->es_opcional)?"Si":"No");
								
								$T->parse('lista_patrones', 'LISTA_PATRONES', true);
								
							}
							
						}
					}

					$T->parse('lista_pasos', 'LISTA_PASOS', true);
					$orden++;
				}
				
					$T->parse('tiene_pasos', 'TIENE_PASOS', true);
				}
				else {
					$T->parse('sin_configuracion', 'SIN_CONFIGURACION', true);
				}
			}
			$posicion++;
		}

		/* VERIFICAR SI ES LECTURA O ESCRITURA */
		if (!$usr->solo_lectura and $sactual->getPermisos(2) == 'w') {
			$T->parse('puede_modificar', 'PUEDE_MODIFICAR', false);
		}
		else {
			$T->setVar('__form_disabled', 'disabled');
		}
	}

	/* SI SE MUESTRA LA LISTA DE OBJETIVOS*/
	else {
		if ($sactual->seccion_id == REP_SECCION_OBJETIVO_TODOS) {
			$T->setFile('tpl_contenido', 'lista_objetivos_cliente.tpl');
			$T->setBlock('tpl_contenido', 'LISTA_SERVICIOS_ONLINE', 'lista_servicios_online');
			$T->setBlock('LISTA_SERVICIOS_ONLINE', 'LISTA_OBJETIVOS_ONLINE', 'lista_objetivos_online');
		
			if(!$usr->solo_lectura and $sactual->getPermisos(1) == 'w') {
				$T->setVar('__form_icon_detail', 'spriteButton spriteButton-editar');
				$T->setVar('__form_label_detail', 'Modificar Objetivo');
			}
			else {
				$T->setVar('__form_icon_detail', 'spriteButton spriteButton-ver');
				$T->setVar('__form_label_detail', 'Informaci&oacute;n Objetivo');
			}
		
			$objetivos = $usr->getObjetivos(REP_DATOS_MONITOREO);
		
			$objetivos_segun_servicio = array();
		
			foreach ($objetivos as $objetivo) {
				$servicio_id = $objetivo->getServicio()->servicio_id;
				if (empty($objetivos_segun_servicio[$servicio_id])) {
					$objetivos_segun_servicio[$servicio_id] = array();
					$objetivos_segun_servicio[$servicio_id]["objetivos"] = array();
					$objetivos_segun_servicio[$servicio_id]["nombre"] = $objetivo->getServicio()->nombre;
				}
				$objetivos_segun_servicio[$servicio_id]["objetivos"][] = $objetivo;
			}
		
			ksort($objetivos_segun_servicio); // ordena los servicios según su id, de menor a mayor
		
			foreach($objetivos_segun_servicio as $objetivos_de_tipo) {
				$T->setVar('__nombre_de_servicio', $objetivos_de_tipo["nombre"]);
		
				foreach($objetivos_de_tipo["objetivos"] as $objetivo) {
					$T->setVar('__objetivo_online_id', $objetivo->objetivo_id);
					$T->setVar('__objetivo_online_nombre', htmlspecialchars($objetivo->nombre));
					$T->setVar('__objetivo_online_descripcion', htmlspecialchars($objetivo->descripcion));
					$T->parse('lista_objetivos_online', 'LISTA_OBJETIVOS_ONLINE', true);
				}
				$T->parse('lista_servicios_online', 'LISTA_SERVICIOS_ONLINE', true);
				$T->clearVar('lista_objetivos_online');
			}
		}
		else {
			if (!$usr->solo_lectura and $sactual->getPermisos(1) == 'w') {
				$T->setFile('tpl_contenido', 'lista_objetivos.tpl');			}
			else {
				$T->setFile('tpl_contenido', 'lista_objetivos_ro.tpl');
			}
		
			$T->setBlock('tpl_contenido', 'LISTA_OBJETIVOS', 'lista_objetivos');
		
			/* LISTA DE OBJETIVOS */
			$cont_desc = 5000;
			$T->setVar('lista_objetivos', '');
			$T->setVar('__cliente_usuario_id', $usr->usuario_id);
			foreach ($usr->getObjetivos(REP_DATOS_USUARIO) as $objetivo) {
				$T->setVar('__objetivo_id', $objetivo->objetivo_id);
				$T->setVar('__objetivo_nombre', htmlspecialchars($objetivo->nombre));
				$T->setVar('__objetivo_descripcion', htmlspecialchars($objetivo->descripcion));
				$T->setVar('__descripcion_id', $cont_desc);
				$T->setVar('__servicio_nombre', $objetivo->getServicio()->nombre);
				$T->setVar('__servicio_id', $objetivo->__servicio->servicio_id);
				$cont_desc++;
				$T->parse('lista_objetivos', 'LISTA_OBJETIVOS', true);
			}
		}
	}
}

/* SI SE MUESTRA EL MENU HORARIOS HABILES */
elseif ($sactual->seccion_id == REP_SECCION_OBJETIVO_HORARIO and $sactual->getPermisos(1) != '-') {
	/* MOSTRAR MENSAJE DE CONTRATAR SERVICIO */
	if (!$usr->usa_horariohabil) {
		$T->setFile('tpl_contenido', 'contratar.tpl');
		$T->setVar('__sitio_contenido', $T->parse('out', 'tpl_contenido'));
		$T->pparse('out', 'tpl_sitio');
		exit();
	}

	/* SI SE EJECUTA UNA ACCION PARA LOS HORARIOS */
	if (isset($accion) and $accion != "") {
		include("commonHorarios.php");
	}
	
	/* SI SE MUESTRA LA LISTA DE HORARIOS HABILES */
	else {
		$T->setFile('tpl_contenido', 'lista_horarios.tpl');
		$T->setBlock('tpl_contenido', 'PUEDE_AGREGAR', 'puede_agregar');
		$T->setBlock('tpl_contenido', 'PUEDE_ELIMINAR', 'puede_eliminar');
		$T->setBlock('tpl_contenido', 'LISTA_HORARIOS', 'lista_horarios');
		$T->setBlock('tpl_contenido', 'MOSTRAR_HORARIOS_DISPONIBLES', 'mostrar_horarios_disponibles');

		/* LISTA DE HORARIOS */
		foreach ($usr->getHorarios(REP_HORARIO_HABIL) as $horario) {
			$T->setVar('puede_eliminar','');
			if (!$usr->solo_lectura and $sactual->getPermisos(1) == 'w' and $horario->puedeEliminar()) {
				$T->setVar('__horario_id',$horario->horario_id);
				$T->setVar('__horario_nombre', $horario->nombre);
				$T->parse('puede_eliminar','PUEDE_ELIMINAR',true);
			}
			$T->setVar('__horario_id', $horario->horario_id);
			$T->setVar('__horario_nombre', htmlspecialchars($horario->nombre));
			$T->setVar('__horario_descripcion', htmlspecialchars($horario->descripcion));
			$T->setVar('__horario_tipo_nombre', $horario->tipo_nombre);
			$T->parse('lista_horarios', 'LISTA_HORARIOS', true);
		}

		/* VERIFICAR SI ES LECTURA O ESCRITURA */
		$cnt_restante = $usr->puedeAgregarHorarioHabil();
		if (!$usr->solo_lectura and $sactual->getPermisos(1) == 'w') {
			if ($cnt_restante) {
				$T->parse('puede_agregar', 'PUEDE_AGREGAR', false);
			}
			$T->setVar('__form_icon_detail', 'spriteButton spriteButton-editar');
			$T->setVar('__form_label_detail', 'Modificar Horario');
		}
		else {
			$T->setVar('__form_icon_detail', 'spriteButton spriteButton-ver');
			$T->setVar('__form_label_detail', 'Informaci&oacute;n Horario');
		}
		if ($cnt_restante < REP_MOSTRAR_DISPONIBLES_MINIMO) {
			$T->setVar('__horarios_disponible', $cnt_restante);
			$T->parse('mostrar_horarios_disponibles', 'MOSTRAR_HORARIOS_DISPONIBLES', false);
		}
	}
}

/* SI SE MUESTRA EL MENU HORARIOS MANTENCION */
elseif ($sactual->seccion_id == REP_SECCION_OBJETIVO_MANTENCION and $sactual->getPermisos(1) != '-') {

	if (isset($accion) and $accion != "") {
		
		$T->setFile('tpl_contenido', 'form_mantencion.tpl');
		$T->setBlock('tpl_contenido', 'LISTA_ITEMS', 'lista_items');
		$T->setBlock('tpl_contenido', 'TIENE_ITEMS', 'tiene_items');
		
		if ($horario_id) {
			$horario = $usr->getHorario($horario_id);
			$T->setVar('__horario_id', $horario->horario_id);
			$T->setVar('__horario_nombre', $horario->nombre);
			$T->setVar('__horario_descripcion', $horario->descripcion);
//			$T->setVar('__horario_tipo_nombre', $horario->tipo_nombre);

			foreach ($horario->getHorarioItems() as $item) {
				$T->setVar('__item_id', $item->item_id);
				$T->setVar('__item_inicio', date("d/m/Y H:i:s", strtotime($item->fecha_inicio." ".$item->hora_inicio)));
				$T->setVar('__item_termino', date("d/m/Y H:i:s", strtotime($item->fecha_termino." ".$item->hora_termino)));
				$T->setVar('__item_descripcion', $item->descripcion);
				$T->parse('lista_items', 'LISTA_ITEMS', true);
			}
			$T->parse('tiene_items', 'TIENE_ITEMS', false);
			
		}
		else {
			$T->setVar('__horario_id', 0);
		}
		
	}

	/* SI SE MUESTRA LA LISTA DE HORARIOS MANTENCION */
	else {
		$T->setFile('tpl_contenido', 'lista_mantenciones.tpl');
		$T->setBlock('tpl_contenido', 'PUEDE_AGREGAR', 'puede_agregar');
		$T->setBlock('tpl_contenido', 'PUEDE_ELIMINAR', 'puede_eliminar');
		$T->setBlock('tpl_contenido', 'LISTA_HORARIOS', 'lista_horarios');
		$T->setBlock('tpl_contenido', 'MOSTRAR_HORARIOS_DISPONIBLES', 'mostrar_horarios_disponibles');

		/* LISTA DE HORARIOS */
		foreach ($usr->getHorarios(REP_HORARIO_MANTENCION) as $horario) {
			$T->setVar('puede_eliminar','');
			if (!$usr->solo_lectura and $sactual->getPermisos(1) == 'w' and $horario->puedeEliminar()) {
				$T->setVar('__horario_id',$horario->horario_id);
				$T->setVar('__horario_nombre', $horario->nombre);
				$T->parse('puede_eliminar','PUEDE_ELIMINAR',true);
			}
			$T->setVar('__horario_id', $horario->horario_id);
			$T->setVar('__horario_nombre', htmlspecialchars($horario->nombre));
			$T->setVar('__horario_descripcion', htmlspecialchars($horario->descripcion));
			$T->setVar('__horario_tipo_nombre', $horario->tipo_nombre);
			$T->parse('lista_horarios', 'LISTA_HORARIOS', true);
		}

		/* VERIFICAR SI ES LECTURA O ESCRITURA */
		if (!$usr->solo_lectura and $sactual->getPermisos(1) == 'w') {
			$T->parse('puede_agregar', 'PUEDE_AGREGAR', false);
			$T->setVar('__form_icon_detail', 'spriteButton spriteButton-editar');
			$T->setVar('__form_label_detail', 'Modificar Horario');
		}
		else {
			$T->setVar('__form_icon_detail', 'spriteButton spriteButton-ver');
			$T->setVar('__form_label_detail', 'Informaci&oacute;n Horario');
		}
	}
}

elseif ($sactual->seccion_id == REP_SECCION_OBJETIVO_PONDERACION and $sactual->getPermisos(2) != '-') {

	$disabled = (!$usr->solo_lectura and $sactual->getPermisos(1) == 'w') ? null : "disabled";
	if ($accion == "mostrar_ponderacion_detalle") {
		$T->setFile('tpl_contenido', 'form_ponderacion_detalle.tpl');
		$T->setBlock('tpl_contenido', 'MOSTRAR_ERROR', 'mostrar_error');
		$T->setBlock('tpl_contenido', 'PUEDE_ELIMINAR', 'puede_eliminar');
		$T->setBlock('tpl_contenido', 'LISTA_ITEMS', 'lista_items');
		$T->setBlock('tpl_contenido', 'LISTA_HORAS', 'lista_horas');
		$T->setBlock('tpl_contenido', 'PUEDE_AGREGAR', 'puede_agregar');

		$intervalo = $_REQUEST["intervalo_id"];
		$ponderacion = $usr->getPonderacion();
		
		if ($ponderacion != null and $ponderacion->intervalo == $intervalo and $ponderacion->intervalo != 0) {

			foreach ($ponderacion->getPonderacionItems() as $item) {
				$T->setVar('__item_inicio', sprintf("%'02s:00:00\n",  $item->inicio));
				$T->setVar('__item_termino', sprintf("%'02s:00:00\n",  $item->termino));
				$T->setVar('__item_valor', $item->valor);
				$T->setVar('__item_hora_inicio', $item->inicio);
				$T->setVar('__item_hora_termino', $item->termino);
				$T->setVar('__item_intervalo', sprintf("%'02s:00:00\n",  $ponderacion->intervalo));
				$T->setVar('__item_disabled', $disabled);
				$T->parse('lista_items', 'LISTA_ITEMS', true);
			}
		}
		elseif ($intervalo > 0) {

			for ($i=0; $i<24; $i=$i+$intervalo) {
				$f = $i + $intervalo;
				$T->setVar('__item_inicio', sprintf("%'02s:00:00\n",  $i));
				$T->setVar('__item_termino', sprintf("%'02s:00:00\n",  $f));
				$T->setVar('__item_hora_inicio', $i);
				$T->setVar('__item_hora_termino', $f);
				$T->setVar('__item_intervalo', sprintf("%'02s:00:00\n",  $intervalo));
				$T->setVar('__item_disabled', $disabled);
				$T->parse('lista_items', 'LISTA_ITEMS', true);
			}
		}
		else {

			if (isset($_REQUEST["item_inicio"]) and isset($_REQUEST["item_termino"]) and ($_REQUEST["item_inicio"] < $_REQUEST["item_termino"])) {
				$valido = true; 
				foreach ($_SESSION["item_inicio"] as $key => $value) {
					if (!($_REQUEST["item_inicio"] < $_SESSION["item_inicio"][$key] and $_REQUEST["item_termino"] <= $_SESSION["item_inicio"][$key]) and 
						!($_REQUEST["item_inicio"] >= $_SESSION["item_termino"][$key] and $_REQUEST["item_termino"] > $_SESSION["item_termino"][$key])) {
						$valido = false;
					}
				}
				
				if ($valido) {
					$_SESSION["item_inicio"][] = $_REQUEST["item_inicio"];
					$_SESSION["item_termino"][] = $_REQUEST["item_termino"];
					$_SESSION["item_valor"][] = "";
				}
				else {
					$T->parse('mostrar_error', 'MOSTRAR_ERROR', false);
				}
			}
			elseif (isset($_REQUEST["item_inicio_quitar"])) {
				$key = array_search($_REQUEST["item_inicio_quitar"], $_SESSION["item_inicio"]);
				unset($_SESSION["item_termino"][$key]);
				unset($_SESSION["item_inicio"][$key]);
				unset($_SESSION["item_valor"][$key]);
			}
			
			$default = 0;
			for ($i=0; $i<24; $i++) {

				if (in_array($i, $_SESSION["item_inicio"])) {
					$key = array_search($i, $_SESSION["item_inicio"]);
					$inicio = $_SESSION["item_inicio"][$key];
					$termino = $_SESSION["item_termino"][$key];
					$valor = $_SESSION["item_valor"][$key];
					$entrar = true;
					$auto = false;
					$default = $termino;
					$i = $termino - 1;
				}
				elseif (in_array(($i+1), $_SESSION["item_inicio"]) || $i == 23) {
					$inicio = $default;
					$termino = $i+1;
					$valor = "";
					$entrar = true;
					$auto = true;
				}
				else {
					$entrar = false;
				}
				
				if ($entrar == true) {
					$T->setVar('__item_inicio', sprintf("%'02s:00:00\n", $inicio));
					$T->setVar('__item_termino', sprintf("%'02s:00:00\n", $termino));
					$T->setVar('__item_valor', $valor);
					$T->setVar('__item_hora_inicio', $inicio);
					$T->setVar('__item_hora_termino', $termino);
					if ($auto) {
						$T->setVar('puede_eliminar', '');
						$T->setVar('__item_class', "style='background-color: #f0ede8; color: #909090'");
						$T->setVar('__item_intervalo', 'Autogenerado');
					}
					else {
						$T->parse('puede_eliminar', 'PUEDE_ELIMINAR', false);
						$T->setVar('__item_class', '');
						$T->setVar('__item_intervalo', sprintf("%'02s:00:00\n", ($termino-$inicio)));
					}
					$T->setVar('__item_disabled', $disabled);
					$T->parse('lista_items', 'LISTA_ITEMS', true);
				}
			}
			for ($i=1; $i<24; $i++) {
				$T->setVar('__hora_id', $i);
				$T->setVar('__hora_nombre', sprintf("%'02s:00:00\n",  $i));
				$T->parse('lista_horas', 'LISTA_HORAS', true);
			}
			$T->parse('puede_agregar', 'PUEDE_AGREGAR', false);		
		}
		
		$T->pparse('out', 'tpl_contenido');
		exit();
	}
	else {
		$T->setFile('tpl_contenido', 'form_ponderacion.tpl');
		$T->setBlock('tpl_contenido', 'LISTA_INTERVALOS', 'lista_intervalos');
		$T->setBlock('tpl_contenido', 'ACCIONES', 'acciones');

//		$T->setVar('__padre_id', $sactual->padre_id);
//		$T->setVar('__seccion_id', $sactual->seccion_id);
//		$T->setVar('__nivel', $sactual->nivel);
		
		$ponderacion = $usr->getPonderacion();
		$T->setVar('__disabled', $disabled);
		foreach ($intervalos_dia as $id => $valor) {
			$T->setVar('__intervalo_id', $id);
			$T->setVar('__intervalo_nombre', $valor);
			$T->setVar('__intervalo_sel', ($ponderacion!=null && $ponderacion->intervalo==$id)?"selected":"");
			$T->parse('lista_intervalos', 'LISTA_INTERVALOS', true);
		}

		if(!$disabled){
			$T->parse('acciones', 'ACCIONES', true);	
		}

		$_SESSION["item_inicio"] = array();
		$_SESSION["item_termino"] = array();
		$_SESSION["item_valor"] = array();
		
		if ($ponderacion != null and $ponderacion->intervalo == 0) {
			foreach ($ponderacion->getPonderacionItems() as $item) {
				$_SESSION["item_inicio"][] = $item->inicio;
				$_SESSION["item_termino"][] = $item->termino;
				$_SESSION["item_valor"][] = $item->valor;
			}
		}
	}
}
else {
	$T->setFile('tpl_contenido', 'sorry_seccion.tpl');
}

$T->setVar('__sitio_contenido', $T->parse('out', 'tpl_contenido'));

?>