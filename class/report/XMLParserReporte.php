<?

class XMLParserReporte {
	
	var $dom;
	var $__objetivos;
	var $__sub_objetivos;
	var $__objetivos_ga;
	var $__monitores;
	var $__errores;
	var $__eventos;
	var $__codigos;
	var $__slas;
	var $__semaforos;
	var $__grupos;
	var $__horarios;
	var $__horarios_inhabiles;
	
	var $tipo;
	var $subtipo;
	var $fecha_creacion;
	var $fecha_expiracion;
	var $tiempo_expiracion;
	var $tiene_datos;
	
	/*
	 * Funcion para obtener los datos de un XML de resultados del sistema.
	 * Lee el encabezado del XML y obtiene las propiedades.
	 */
	function XMLParserReporte(&$xml) {
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($xml);
		
		$this->__objetivos = array();
		$this->__sub_objetivos = array();
//		$this->__objetivos_ga = array();
		$this->__grupos = array();
		$this->__monitores = array();
		$this->__horarios = array();
		$this->__horarios_inhabiles = array();
		$this->__eventos = array();
		$this->__codigos = array();
		$this->__slas = array();
		$this->__semaforos = array();
		
		$this->tiene_datos = false;
		
		$resultados = Utiles::getElementsByArrayTagName($dom, array("atentus", "resultados"));
		$this->dom = $resultados[0];
		
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("tipo")) as $xml_tipo) {
			$this->tipo = $xml_tipo->nodeValue;
		}
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("subtipo")) as $xml_subtipo) {
			$this->subtipo = $xml_subtipo->nodeValue;
		}
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("fecha")) as $xml_creacion) {
			$this->fecha_creacion = $xml_creacion->nodeValue;
		}
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("fecha_expiracion")) as $xml_expiracion) {
			$this->fecha_expiracion = $xml_expiracion->nodeValue;
		}
		$this->tiempo_expiracion = (strtotime($this->fecha_expiracion) - strtotime($this->fecha_creacion));
		
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("propiedades")) as $xml_propiedades) {
			
			/* LISTA DE OBJETIVOS DE GOOGLE ANALYTICS */
/*			foreach (Utiles::getElementsByArrayTagName($xml_propiedades, array("google_analytics", "ga")) as $xml_ga_objetivo) {
				$obj_ga = new DatosObjetivo($xml_ga_objetivo->getAttribute("objetivo_id"), '');
				foreach (Utiles::getElementsByArrayTagName($xml_ga_objetivo, array("paso")) as $xml_ga_paso) {
					$paso = new DatosPaso($xml_ga_paso->getAttribute("paso_orden"), '');
					$paso->ga_token_id = $xml_ga_paso->getAttribute("token");
					$paso->ga_perfil_id = $xml_ga_paso->getAttribute("perfil_id");
                    $paso->ga_tipo = $xml_ga_paso->getAttribute("tipo");
                    $paso->ga_api_base_url = $xml_ga_paso->getAttribute("api_base_url");
					$obj_ga->__pasos[$paso->paso_id] = $paso;
				}
				$this->__objetivos_ga[$obj_ga->objetivo_id] = $obj_ga;
			}*/
			
			/* LISTA DE OBJETIVOS */
			foreach (Utiles::getElementsByArrayTagName($xml_propiedades, array("objetivos", "objetivo")) as $xml_objetivo) {
				$objetivo = new DatosObjetivo($xml_objetivo->getAttribute("objetivo_id"), $xml_objetivo->getAttribute("nombre"));
				$objetivo->servicio = $xml_objetivo->getAttribute("servicio");
				$objetivo->sla_dis_ok = $xml_objetivo->getAttribute("sla_disponibilidad_ok");
				$objetivo->sla_dis_error = $xml_objetivo->getAttribute("sla_disponibilidad_error");
				$objetivo->sla_ren_ok = $xml_objetivo->getAttribute("sla_rendimiento_ok");
				$objetivo->sla_ren_error = $xml_objetivo->getAttribute("sla_rendimiento_error");
				
				/* LISTA DE PASOS DEL OBJETIVO */
				foreach (Utiles::getElementsByArrayTagName($xml_objetivo, array("paso")) as $xml_paso) {
					if ($xml_paso->getAttribute("visible") == 1) {
						$paso = new DatosPaso($xml_paso->getAttribute("paso_orden"), $xml_paso->getAttribute("nombre"));
/*						if (isset($this->__objetivos_ga[$objetivo->objetivo_id])) {
							$obj_ga = & $this->__objetivos_ga[$objetivo->objetivo_id];
							$paso->ga_token_id = $obj_ga->__pasos[0]->ga_token_id;
							$paso->ga_perfil_id = $obj_ga->__pasos[0]->ga_perfil_id;
                            $paso->ga_tipo = $obj_ga->__pasos[0]->ga_tipo;
                            $paso->ga_api_base_url = $obj_ga->__pasos[0]->ga_api_base_url;
						}
						foreach (Utiles::getElementsByArrayTagName($xml_paso, array("analytics")) as $xml_ga_paso) {
							$obj_ga = & $this->__objetivos_ga[$xml_ga_paso->getAttribute("objetivo_id")];
							$paso->ga_token_id = $obj_ga->__pasos[0]->ga_token_id;
							$paso->ga_perfil_id = $obj_ga->__pasos[0]->ga_perfil_id;
                            $paso->ga_tipo = $obj_ga->__pasos[0]->ga_tipo;
                            $paso->ga_api_base_url = $obj_ga->__pasos[0]->ga_api_base_url;

                            // Está bien lo que sigue? Por qué el if anterior es necesario?
                            $paso->ga_dimensions = $xml_ga_paso->getAttribute("dimensions");
                            $paso->ga_metrics = $xml_ga_paso->getAttribute("metrics");
                            $paso->ga_filters = $xml_ga_paso->getAttribute("filters");
                            $paso->ga_sort = $xml_ga_paso->getAttribute("sort");
						}*/
						
						foreach (Utiles::getElementsByArrayTagName($xml_paso, array("patron")) as $xml_patron) {
							$patron= new DatosPatron($xml_patron->getAttribute("orden"));
							$patron->nombre = $xml_patron->getAttribute("nombre");							
							$paso->__patrones[$patron->patron_id] = $patron;
						}

						foreach (Utiles::getElementsByArrayTagName($xml_paso, array("excluye")) as $xml_excluye) {
							$excluye= new DatosExcluida();
							$excluye->url = $xml_excluye->getAttribute("url");
							$excluye->monitor_id = $xml_excluye->getAttribute("monitor_id");
							$paso->__excluye[] = $excluye;
						}
						$objetivo->__pasos[$paso->paso_id] = $paso;
					}
				}

                /* SE INGRESAN LOS SUBOBJETIVOS */
				// TODO: REVISAR ESTO
/*				foreach (Utiles::getElementsByArrayTagName($xml_objetivo, array("subobjetivos")) as $xml_subobjetivos) {					
					foreach (Utiles::getElementsByArrayTagName($xml_subobjetivos, array("objetivos","objetivo")) as $xml_objetivos2) {
						$objetivo2 = new DatosObjetivo($xml_objetivos2->getAttribute("objetivo_id"), $xml_objetivos2->getAttribute("nombre"));
												
						foreach (Utiles::getElementsByArrayTagName($xml_objetivos2, array("paso")) as $xml_paso) {
							if ($xml_paso->getAttribute("visible") == 1) {
								$paso = new DatosPaso($xml_paso->getAttribute("paso_orden"), $xml_paso->getAttribute("nombre"));
								if (isset($this->__objetivos_ga[$objetivo2->objetivo_id])) {
									$obj_ga = & $this->__objetivos_ga[$objetivo2->objetivo_id];
									$paso->ga_token_id = $obj_ga->__pasos[0]->ga_token_id;
									$paso->ga_perfil_id = $obj_ga->__pasos[0]->ga_perfil_id;
		                            $paso->ga_tipo = $obj_ga->__pasos[0]->ga_tipo;
		                            $paso->ga_api_base_url = $obj_ga->__pasos[0]->ga_api_base_url;
								}
								foreach (Utiles::getElementsByArrayTagName($xml_paso, array("analytics")) as $xml_ga_paso) {
									$obj_ga = & $this->__objetivos_ga[$xml_ga_paso->getAttribute("objetivo_id")];
									$paso->ga_token_id = $obj_ga->__pasos[0]->ga_token_id;
									$paso->ga_perfil_id = $obj_ga->__pasos[0]->ga_perfil_id;
		                            $paso->ga_tipo = $obj_ga->__pasos[0]->ga_tipo;
		                            $paso->ga_api_base_url = $obj_ga->__pasos[0]->ga_api_base_url;
		
		                            // Está bien lo que sigue? Por qué el if anterior es necesario?
		                            $paso->ga_dimensions = $xml_ga_paso->getAttribute("dimensions");
		                            $paso->ga_metrics = $xml_ga_paso->getAttribute("metrics");
		                            $paso->ga_filters = $xml_ga_paso->getAttribute("filters");
		                            $paso->ga_sort = $xml_ga_paso->getAttribute("sort");
								}
								
								foreach (Utiles::getElementsByArrayTagName($xml_paso, array("patron")) as $xml_patron) {
									$patron= new DatosPatron($xml_patron->getAttribute("orden"));
									$patron->nombre = $xml_patron->getAttribute("nombre");
									$paso->__patrones[$patron->patron_id] = $patron;
								}
		
								foreach (Utiles::getElementsByArrayTagName($xml_paso, array("excluye")) as $xml_excluye) {
									$excluye= new DatosExcluida();
									$excluye->url = $xml_excluye->getAttribute("url");
									$excluye->monitor_id = $xml_excluye->getAttribute("monitor_id");
									$paso->__excluye[] = $excluye;
								}
								$objetivo2->__pasos[$paso->paso_id] = $paso;
							}
						}						
						$objetivo->__sub_objetivos[$objetivo2->objetivo_id] = $objetivo2;
						
					}					
				}*/
				$this->__objetivos[$objetivo->objetivo_id] = $objetivo;
			}

			/* LISTA DE GRUPOS DE MONITORES */
			foreach (Utiles::getElementsByArrayTagName($xml_propiedades, array("grupos", "grupo")) as $xml_grupo) {
				$grupo = new DatosGrupo($xml_grupo->getAttribute("orden"), $xml_grupo->getAttribute("nombre"));
				foreach (Utiles::getElementsByArrayTagName($xml_grupo, array("nodos", "nodo")) as $xml_nodo) {
					$grupo->__monitores_ids[] = $xml_nodo->getAttribute("nodo_id");
				}
				$this->__grupos[$grupo->grupo_id] = $grupo;
			}
			
			/* LISTA DE MONITORES */
			foreach (Utiles::getElementsByArrayTagName($xml_propiedades, array("nodos", "nodo")) as $xml_nodo) {
				$nodo = new DatosMonitor($xml_nodo->getAttribute("nodo_id"), $xml_nodo->getAttribute("nombre"));
				$nodo->nombre_corto = $xml_nodo->getAttribute("titulo");
				$nodo->ubicacion = $xml_nodo->getAttribute("subtitulo");
				$this->__monitores[$nodo->monitor_id] = $nodo;
			}

			/* LISTA DE HORARIOS HABILES */
			foreach (Utiles::getElementsByArrayTagName($xml_propiedades, array("horarios_habiles", "horario_habil")) as $xml_horario) {
				$horario = new DatosHorarioHabil($xml_horario->getAttribute("inicio"), $xml_horario->getAttribute("termino"));
				$this->__horarios[] = $horario;
			}

			/* LISTA DE HORARIOS INHABILES */
			foreach (Utiles::getElementsByArrayTagName($xml_propiedades, array("horarios_inhabiles", "horario_inhabil")) as $xml_horario) {
				$horario = new DatosHorarioHabil($xml_horario->getAttribute("inicio"), $xml_horario->getAttribute("termino"));
				$this->__horarios_inhabiles[] = $horario;
			}
			
			foreach (Utiles::getElementsByArrayTagName($xml_propiedades, array("ponderaciones", "item")) as $xml_ponderacion) {
				$ponderacion = new DatosPonderacion($xml_ponderacion->getAttribute("item_id"));
				$ponderacion->hora_inicio = $xml_ponderacion->getAttribute("inicio");
				$ponderacion->hora_termino = $xml_ponderacion->getAttribute("termino");
				$ponderacion->valor = $xml_ponderacion->getAttribute("valor");
				$this->__ponderaciones[$ponderacion->ponderacion_item_id] = $ponderacion;
			}
						
			/* LISTA DE EVENTOS (UPTIME, DOWNTIME, ETC) */
			foreach (Utiles::getElementsByArrayTagName($xml_propiedades, array("eventos", "evento")) as $xml_evento) {
				$evento = new DatosEvento($xml_evento->getAttribute("evento_id"), $xml_evento->getAttribute("nombre"));
				$evento->color = $xml_evento->getAttribute("color");
				$evento->orden = $xml_evento->getAttribute("orden");
				$this->__eventos[$evento->evento_id] = $evento;
			}
			
			/* LISTA DE ESTADOS DEL SLA */
			foreach (Utiles::getElementsByArrayTagName($xml_propiedades, array("slas", "sla")) as $xml_sla) {
				$sla = new DatosEvento($xml_sla->getAttribute("sla_id"), $xml_sla->getAttribute("nombre"));
				$sla->color = $xml_sla->getAttribute("color");
				$this->__slas[$sla->evento_id] = $sla;
			}
			
			/* LISTA DE CODIGOS (ESTADOS HTML Y ERRORES) */
			foreach (Utiles::getElementsByArrayTagName($xml_propiedades, array("codigos", "codigo")) as $xml_codigo) {
				$codigo = new DatosEvento($xml_codigo->getAttribute("codigo_id"), $xml_codigo->getAttribute("nombre"));
				$codigo->descripcion = $xml_codigo->getAttribute("descripcion");
				$codigo->icono = $xml_codigo->getAttribute("icono");
				$codigo->color = $xml_codigo->getAttribute("color");
				$this->__codigos[$codigo->evento_id] = $codigo;
			}
			
			/* LISTA DE ESTADOS DEL SEMAFORO */
			foreach (Utiles::getElementsByArrayTagName($xml_propiedades, array("semaforos", "semaforo")) as $xml_semaforo) {
				$semaforo = new DatosEvento($xml_semaforo->getAttribute("semaforo_id"), $xml_semaforo->getAttribute("nombre"));
				$semaforo->descripcion = $xml_semaforo->getAttribute("descripcion");
				$semaforo->icono = $xml_semaforo->getAttribute("icono");
				$semaforo->color = $xml_semaforo->getAttribute("color");
				$this->__semaforos[$semaforo->evento_id] = $semaforo;
			}
		}
	}
	
	/*************** FUNCIONES DE GRAFICOS DE DISPONIBILIDAD ***************/
	/*************** FUNCIONES DE GRAFICOS DE DISPONIBILIDAD ***************/
	/*************** FUNCIONES DE GRAFICOS DE DISPONIBILIDAD ***************/
	
	/**
	 * Funcion para obtener los datos del grafico de 
	 * Disponibilidad Consolidada.
	 */
	function getDatosConsolidadoDisponibilidad() {

		/* LISTA DE OBJETIVOS DEL GRAFICO */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE EVENTOS DEL OBJETIVO (Consolidado) */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("datos", "dato")) as $dato_obj) {
				$this->tiene_datos = true;
				
				$evento = new DatosPeriodo($dato_obj->getAttribute('inicio'), $dato_obj->getAttribute('termino'));
				$evento->evento_id = $dato_obj->getAttribute('evento_id');
				$objetivo->__eventos[] = $evento;
			}
			
			/* LISTA DE MONITORES DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_mon) {

				if(isset($this->__monitores[$detalle_mon->getAttribute("nodo_id")])){
				
					$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];
					
					/* LISTA DE EVENTOS DEL MONITOR */
					foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("datos", "dato")) as $dato_mon) {
						$this->tiene_datos = true;
						
						$evento = new DatosPeriodo($dato_mon->getAttribute('inicio'), $dato_mon->getAttribute('termino'));
						$evento->evento_id = $dato_mon->getAttribute('evento_id');
						$monitor->__eventos[] = $evento;
					}
					
					/* LISTA DE PASOS DEL OBJETIVO POR MONITOR */
					foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("detalles", "detalle")) as $detalle_pas) {
						if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
							$this->tiene_datos = true;
							
							$paso = clone $objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')];
							$paso->__datos = $detalle_pas;
	
							$monitor->__pasos[] = $paso;
						}
					}
					$objetivo->__monitores[$monitor->monitor_id] = $monitor;
				
				}
				
			}
		}
	}

	/* 
	 * Funcion para obtener los datos del grafico de 
	 * Disponibilidad Detallada.
	 */
	function getDatosDetalladoDisponibilidad() {
		
		/* LISTA DE OBJETIVOS DEL GRAFICO */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE MONITORES DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_mon) {
				if (isset($this->__monitores[$detalle_mon->getAttribute('nodo_id')])) {
				
				$monitor = clone $this->__monitores[$detalle_mon->getAttribute('nodo_id')];
				
				/* LISTA DE PASOS DEL OBJETIVO POR MONITOR */
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("detalles", "detalle")) as $detalle_pas) {
					if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
						$paso = clone $objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')];
						
						/* LISTA DE PORCENTAJES DE EVENTOS DEL PASO */
						foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("estadisticas", "estadistica")) as $dato_pas) {
							$this->tiene_datos = true;
							
							if (isset($this->__eventos[$dato_pas->getAttribute('evento_id')])) {
							$evento = clone $this->__eventos[$dato_pas->getAttribute('evento_id')];
							$evento->porcentaje = $dato_pas->getAttribute('porcentaje');
							$evento->duracion = $dato_pas->getAttribute('duracion');
							$paso->__eventos[$evento->orden] = $evento;
							}
						}
						ksort($paso->__eventos);
						$monitor->__pasos[] = $paso;
					}
				}
				$objetivo->__monitores[$monitor->monitor_id] = $monitor;
				
				}				
			}
		}
	}

	/*
	 * Funcion para obtener los datos del grafico de 
	 * Disponibilidad Historica.
	 */
	function getDatosHistoricoDisponibilidad() {
		
		/* LISTA DE OBJETIVOS DEL GRAFICO */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE PERIODOS DEL GRAFICO PARA EL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_fecha) {
				$objetivo->__eventos[$detalle_fecha->getAttribute('fecha')] = array();
				
				/* LISTA DE PORCENTAJES DE EVENTOS DEL PERIODO */
				foreach (Utiles::getElementsByArrayTagName($detalle_fecha, array("estadisticas", "estadistica")) as $estadistica_obj) {
					$this->tiene_datos = true;
					
					$evento = clone $this->__eventos[$estadistica_obj->getAttribute('evento_id')];
					$evento->porcentaje = $estadistica_obj->getAttribute('porcentaje');
					$objetivo->__eventos[$detalle_fecha->getAttribute('fecha')][$evento->orden] = $evento;
				}
				ksort($objetivo->__eventos[$detalle_fecha->getAttribute('fecha')]);
			}
		}
	}
	
	/*
	 * Funcion para obtener los datos del grafico de 
	 * Distribucion Porcentual de Downtime y Errores.
	 */
	function getDatosErroresDisponibilidad() {
		
		/* LISTA DE OBJETIVOS DEL GRAFICO */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE PASOS DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_pas) {
				if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
					$paso = & $objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')];
					
					/* LISTA DE PORCENTAJES DE EVENTOS DEL PASO */
					foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("estadisticas", "estadistica")) as $dato_pas) {
						$evento = clone $this->__eventos[$dato_pas->getAttribute('evento_id')];
						$evento->porcentaje = $dato_pas->getAttribute('porcentaje');
						$evento->duracion = $dato_pas->getAttribute('duracion');
						$paso->__eventos[] = $evento;
					}
					
					/* LISTA DE MONITORES DEL OBJETIVO POR PASO */
					foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("detalles", "detalle")) as $detalle_mon) {
						$this->tiene_datos = true;
						
						$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];
						
						/* PORCENTAJE DE DOWNTIME DEL MONITOR */
						$evento = clone $this->__eventos[2];
						$evento->porcentaje = $detalle_mon->getAttribute('porcentaje');
						$evento->duracion = $detalle_mon->getAttribute('duracion');
						$monitor->__eventos[] = $evento;
						
						/* LISTA DE PORCENTAJES DE ERRORES DEL MONITOR */
						foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("estadisticas", "estadistica")) as $error_mon) {
							$error = clone $this->__codigos[$error_mon->getAttribute('codigo_id')];
							$error->porcentaje = $error_mon->getAttribute('porcentaje');
							$monitor->__errores[$error->evento_id] = $error;
						}
						$paso->__monitores[] = $monitor;
					}
				}
			}
		}
	}
	
	/*************** FUNCIONES DE GRAFICOS DE RENDIMIENTO ***************/
	/*************** FUNCIONES DE GRAFICOS DE RENDIMIENTO ***************/
	/*************** FUNCIONES DE GRAFICOS DE RENDIMIENTO ***************/
	
	/*
	 * Funcion para obtener los datos de los graficos de 
	 * Rendiminto Consolidado - Rendimiento Historico - Frecuencia de Rendimiento.
	 */
	function getDatosConsolidadoRendimiento() {
		
		/* LISTA DE OBJETIVOS DEL GRAFICO */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE PASOS DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_pas) {
				if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
					$paso = & $objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')];
					
					/* LISTAS DE TIEMPO DE RESPUESTAS Y DE FRECUENCIA PARA EL PASO */
					foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("datos", "dato")) as $dato_pas) {
						$this->tiene_datos = true;
						
						$paso->__respuestas[$dato_pas->getAttribute('fecha')] = $dato_pas->getAttribute('respuesta');
						if ($dato_pas->getAttribute('cantidad')) {
							$paso->__frecuencias[$dato_pas->getAttribute('respuesta')] = $dato_pas->getAttribute('cantidad');
						}
					}
				}
			}
		}
	}
	
	/*
	 * Funcion para obtener los datos del grafico de
	 * Estadisticas y Detalle Rendimiento.
	 */
	function getDatosEstadisticoRendimiento() {
		
		/* LISTA DE OBJETIVOS DEL GRAFICO */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE PASOS DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_pas) {
				if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
					$paso = & $objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')];
					
					/* DATOS ESTADISTICOS DEL PASO */
					$paso->promedio_respuesta = $detalle_pas->getAttribute('promedio');
					$paso->desviacion_respuesta = $detalle_pas->getAttribute('desviacion');
					foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("estadistica")) as $estadistica_pas) {
						$paso->promedio_respuesta = $estadistica_pas->getAttribute('promedio');
						$paso->desviacion_respuesta = $estadistica_pas->getAttribute('desviacion');
					}
					
					/* LISTA DE TIEMPOS DE RESPUESTA DEL PASO */
					foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("datos", "dato")) as $dato_pas) {
						$this->tiene_datos = true;
						
						$paso->__respuestas[$dato_pas->getAttribute('fecha')] = $dato_pas->getAttribute('respuesta');
					}
					
					/* LISTA DE MONITORES DEL OBJETIVO POR PASO */
					foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("detalles", "detalle")) as $detalle_mon) {
						$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];
						
						/* LISTA DE TIEMPOS DE RESPUESTA DEL MONITOR */
						foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("datos", "dato")) as $dato_mon) {
							$this->tiene_datos = true;
							
							$monitor->__respuestas[$dato_mon->getAttribute('fecha')] = $dato_mon->getAttribute('respuesta');
						}
						$paso->__monitores[$monitor->monitor_id] = $monitor;
					}
				}
			}
		}
	}
	
	/*
	 * Funcion para obtener los datos del grafico de
	 * Superacion de SLA Detallado.
	 */
	function getDatosSLADetalladoRendimiento() {
		
		/* LISTA DE OBJETIVOS DEL GRAFICO */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE PASOS DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_pas) {
				if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
					$paso = & $objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')];
					
					/* LISTA DE PORCENTAJES DE SUPERACION DE SLA DEL PASO */
					foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("estadisticas", "estadistica")) as $estadistica_pas) {
						$this->tiene_datos = true;
						
						$sla = clone $this->__slas[$estadistica_pas->getAttribute('sla_id')];
						$sla->porcentaje = $estadistica_pas->getAttribute('porcentaje');
						$paso->__eventos[$sla->evento_id] = $sla;
					}
				}
			}
		}
	}

	/*
	 * Funcion para obtener los datos del grafico de
	 * Superacion de SLA Historico.
	 */
	function getDatosSLAHistoricoRendimiento() {
		
		/* LISTA DE OBJETIVOS DEL GRAFICO */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE PERIODOS DEL GRAFICO PARA EL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_fecha) {
				$objetivo->__eventos[$detalle_fecha->getAttribute('fecha')] = array();
				
				/* LISTA DE PORCENTAJES DE SUPERACION DE SLA DEL PASO */
				foreach (Utiles::getElementsByArrayTagName($detalle_fecha, array("estadisticas", "estadistica")) as $estadistica_obj) {
					$this->tiene_datos = true;
					
					$sla = clone $this->__slas[$estadistica_obj->getAttribute('sla_id')];
					$sla->porcentaje = $estadistica_obj->getAttribute('porcentaje');
					$objetivo->__eventos[$detalle_fecha->getAttribute('fecha')][] = $sla;
				}
			}
		}
	}

	/*************** FUNCIONES DE GRAFICOS DE GOOGLE ANALYTICS ***************/
	/*************** FUNCIONES DE GRAFICOS DE GOOGLE ANALYTICS ***************/
	/*************** FUNCIONES DE GRAFICOS DE GOOGLE ANALYTICS ***************/
	
	/*
	 * Funcion para obtener los datos del grafico de 
	 * Comparacion con Google Analytics.
	 */
	function getDatosComparativo() {
		
		/* LISTA DE OBJETIVOS DEL GRAFICO */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE PASOS DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_pas) {
				if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
					$paso = & $objetivo->__pasos[$detalle_pas->getAttribute("paso_orden")];
					
					$paso->__eventos = array();
					
					/* LISTA DE PORCENTAJES Y TIEMPOS DE LOS EVENTOS DEL PASO */
					foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("estadisticas", "estadistica")) as $estadistica_pas) {
						$this->tiene_datos = true;
						
						$dato = new DatosEvento('', '');
						$dato->porcentaje = $estadistica_pas->getAttribute('uptime');
						$dato->fecha_inicio = $estadistica_pas->getAttribute('fecha');
						$dato->duracion = $estadistica_pas->getAttribute('duracion');
						$dato->respuesta = $estadistica_pas->getAttribute('respuesta');
						$paso->__eventos[] = $dato;
					}
				}
			}
		}
	}
	
	/*************** FUNCIONES DE TABLAS ***************/
	/*************** FUNCIONES DE TABLAS ***************/
	/*************** FUNCIONES DE TABLAS ***************/

	/*
	 * Funcion para obtener los datos de la tabla de 
	 * Semaforo.
	 */
	function getDatosSemaforo() {
		
		/* LISTA DE OBJETIVOS */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE MONITORES DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_mon) {
				if (isset($this->__monitores[$detalle_mon->getAttribute("nodo_id")])) {
				$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];
				
				/* ULTIMO EVENTO DEL MONITOR */
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("datos", "dato")) as $dato_mon) {
					$this->tiene_datos = true;
					
					$semaforo = clone $this->__semaforos[$dato_mon->getAttribute('semaforo_id')];
					$semaforo->duracion = $dato_mon->getAttribute('duracion');
					$monitor->__eventos[] = $semaforo;
				}
				$objetivo->__monitores[$monitor->monitor_id] = $monitor;
				}
			}
		}
	}

	/**
	 * Funcion para obtener los datos de la tabla de 
	 * Vista Rapida.
	 */
	function getDatosVistaRapida() {
		
		$arr_orden = array();
		
		/* LISTA DE OBJETIVOS */
		$i = count($this->__objetivos);
		$j = 0;
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			$arr_orden[$objetivo->objetivo_id] = $i;
			
			/* LISTA DE MONITORES DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_mon) {
				
				/* LISTA DE PASOS DEL OBJETIVO POR MONITOR */
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("detalles", "detalle")) as $detalle_pas) {
					if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')]) and isset($this->__monitores[$detalle_mon->getAttribute("nodo_id")])) {
						$paso = & $objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')];
						$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];
						
						/* ULTIMO EVENTO DEL PASO */
						foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("datos", "dato")) as $dato_pas) {
							$this->tiene_datos = true;
							
							$codigo = clone $this->__codigos[$dato_pas->getAttribute('codigo_id')];
							$codigo->duracion = $dato_pas->getAttribute('duracion');
							$codigo->respuesta = $dato_pas->getAttribute('respuesta');
							$codigo->fecha = $dato_pas->getAttribute('fecha');
							$monitor->__eventos[] = $codigo;
							
							if ($codigo->color == "d3222a") {
								$arr_orden[$objetivo->objetivo_id] = $j;
								$objetivo->estado = 2;
							}
						}
						$paso->__monitores[$monitor->monitor_id] = $monitor;
					}
				}
			}
			$i++;
			$j++;
		}
		array_multisort($arr_orden, $this->__objetivos);
	}

	/*
	 * Funcion para obtener los datos de la tabla de 
	 * Eventos.
	 */
	function getDatosEventos() {
		
		/* LISTA DE OBJETIVOS */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE MONITORES DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_mon) {
				if (isset($this->__monitores[$detalle_mon->getAttribute("nodo_id")])) {
				$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];
				
				/* LISTA DE PASOS DEL OBJETIVO POR MONITOR */
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("detalles", "detalle")) as $detalle_pas) {
					if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
						$paso = clone $objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')];
						
						/* LISTA DE EVENTOS DEL PASO */
						foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("datos", "dato")) as $dato_pas) {
							$this->tiene_datos = true;
							
							$evento = new DatosPeriodo($dato_pas->getAttribute('fecha'), null);
							$evento->duracion = $dato_pas->getAttribute('duracion');
							$evento->__codigos = explode(",", $dato_pas->getAttribute('codigo_id'));
							
							$paso->__eventos[] = $evento;
						}
						$monitor->__pasos[] = $paso;
					}
				}
				$objetivo->__monitores[] = $monitor;
				}
			}
		}
	}
	
	/*
	 * Funcion para obtener los datos de la tabla de 
	 * Registros.
	 */
	function getDatosRegistros() {
		
		/* LISTA DE OBJETIVOS */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE MONITORES DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_mon) {
				$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];
				
				/* LISTA DE REGISTROS DEL MONITOR */
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("datos", "dato")) as $datos_mon) {
					$this->tiene_datos = true;
					
					$codigo = clone $this->__codigos[$datos_mon->getAttribute('codigo_id')];
					$registro = new DatosRegistro($codigo);
					$registro->fecha = $datos_mon->getAttribute('fecha_inicio');
					$registro->duracion = $datos_mon->getAttribute('duracion');
					$registro->servidor = $datos_mon->getAttribute('servidor');
					$registro->dns_primario = $datos_mon->getAttribute('dns_primario');
					$registro->email = $datos_mon->getAttribute('email');
					$registro->serial = $datos_mon->getAttribute('serial');
					$registro->refresh = $datos_mon->getAttribute('refresh');
					$registro->retry = $datos_mon->getAttribute('retry');
					$registro->expire = $datos_mon->getAttribute('expire');
					$registro->minimum = $datos_mon->getAttribute('minimum');

					if ($datos_mon->getAttribute('respuesta')) {
						$registro->__respuestas[] = $datos_mon->getAttribute('respuesta');
					}
					
					/* LISTA DE DETALLE POR REGISTRO */
					foreach (Utiles::getElementsByArrayTagName($datos_mon, array("registros", "registro")) as $registro_mon) {
						if ($registro_mon->getAttribute('nombre')) {
							$registro->__nombres[] = $registro_mon->getAttribute('nombre');
							$registro->__tipos[] = $registro_mon->getAttribute('tipo');
						}
						if ($registro_mon->getAttribute('prioridad')) {
							$registro->__prioridades[] = $registro_mon->getAttribute('prioridad');
						}
						if ($registro_mon->getAttribute('prioridad')) {
							$registro->__respuestas[] = $registro_mon->getAttribute('respuesta');
						}
					}
					$monitor->__registros[] = $registro;
				}
				$objetivo->__monitores[] = $monitor;
			}
		}
	}
	
	/*
	 * Funcion para obtener los datos de la tabla de 
	 * Ultimos Elementos.
	 */
	function getDatosElementos() {
		
		/* LISTA DE OBJETIVOS */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE MONITORES DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_mon) {
				$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];
				
				/* TOTALES DE LOS ELEMENTOS POR MONITOR */
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("estadisticas", "estadistica")) as $estadistica_mon) {
					$this->tiene_datos = true;
					
					$monitor->total_tamano = $estadistica_mon->getAttribute("tamano_total");
					$monitor->__respuestas[] = $estadistica_mon->getAttribute("respuesta_total");
				}
				
				/* LISTA DE ELEMENTOS POR MONITOR */
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("datos", "dato")) as $dato_mon) {
					$this->tiene_datos = true;
					
					$elemento = new DatosElemento();
					$elemento->url = $dato_mon->getAttribute("url");
					$elemento->tamano_header = $dato_mon->getAttribute("tamano_header");
					$elemento->tamano_body = $dato_mon->getAttribute("tamano_body");
					$elemento->tipo = $dato_mon->getAttribute("tipo");
					$elemento->estado = $dato_mon->getAttribute("status");
					$elemento->__respuestas[] = $dato_mon->getAttribute("respuesta");
					$monitor->__elementos[] = $elemento;
				}
				$objetivo->__monitores[] = $monitor;
			}
		}
	}

	function getDatosMonitoreosElementos() {
		
		/* LISTA DE OBJETIVOS */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE MONITORES DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_mon) {
				if (isset($this->__monitores[$detalle_mon->getAttribute("nodo_id")])) {
				
				$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];
				
				/* LISTA DE ELEMENTOS POR MONITOR */
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("datos", "dato")) as $dato_mon) {
					$this->tiene_datos = true;
					
					$fecha = new DatosMonitoreo($dato_mon->getAttribute("codigo_id"));
					$fecha->fecha = $dato_mon->getAttribute("fecha");
					$fecha->respuesta = $dato_mon->getAttribute("suma_tiempos");
					$fecha->total_elementos = $dato_mon->getAttribute("cantidad");
					$fecha->total_tamanno = $dato_mon->getAttribute("suma_tamanos");
					$monitor->__monitoreos[] = $fecha;
				}
				$objetivo->__monitores[] = $monitor;
				
				}
			}
		}
	}
	
	
	/*
	 * Funcion para obtener los datos de la tabla de 
	 * Elementos de un Periodo.
	 */
	function getDatosEstadisticasElementos() {
		
		/* LISTA DE OBJETIVOS */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE MONITORES DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_mon) {
				
				if (isset($this->__monitores[$detalle_mon->getAttribute("nodo_id")])) {
				
				$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];
				
				/* LISTA DE ELEMENTOS DEL MONITOREO */
				$url_anterior = "";
				$id_anterior = "";
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("datos", "dato")) as $id => $detalle_elem) {
					if ($detalle_elem->getAttribute("url") == $url_anterior) {
						$monitor->__elementos[$id_anterior]->__estados[$detalle_elem->getAttribute("status")] = $detalle_elem->getAttribute("cantidad_status");
					}
					else {
						$this->tiene_datos = true;
						
						$elemento = new DatosElemento();
						$elemento->url = $detalle_elem->getAttribute("url");
						$elemento->tipo = $detalle_elem->getAttribute("tipo");
						$elemento->cantidad = $detalle_elem->getAttribute("cantidad");
						$elemento->promedio_respuesta = $detalle_elem->getAttribute("tiempo_promedio");
						$elemento->minimo_respuesta = $detalle_elem->getAttribute("tiempo_min");
						$elemento->maximo_respuesta = $detalle_elem->getAttribute("tiempo_max");
						$elemento->promedio_tamanno = $detalle_elem->getAttribute("tamano_promedio");
						$elemento->__estados[$detalle_elem->getAttribute("status")] = $detalle_elem->getAttribute("cantidad_status");
						$url_anterior = $detalle_elem->getAttribute("url");
						$id_anterior = $id;
						$monitor->__elementos[$id] = $elemento;
					}
				}
				$objetivo->__monitores[] = $monitor;
				
				}
			}
		}
	}
	
	/* 
	 * Funcion para obtener los datos de la tabla de 
	 * Resumen de Rendimiento.
	 */
	function getDatosEstadisticaResumen() {
		
		/* LISTA DE OBJETIVOS */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE PASOS DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_pas) {
				if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
					$paso = & $objetivo->__pasos[$detalle_pas->getAttribute("paso_orden")];
					
					/* DATOS DEL RENDIMIENTO DEL PASO */
					foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("datos", "dato")) as $dato_pas) {
						$this->tiene_datos = true;
						
						$paso->promedio_respuesta = $dato_pas->getAttribute("tiempo_prom");
						$paso->minimo_respuesta = $dato_pas->getAttribute("tiempo_min");
						$paso->maximo_respuesta = $dato_pas->getAttribute("tiempo_max");
					}
				}
			}
		}
	}
	
	/* 
	 * Funcion para obtener los datos de la tabla de 
	 * Resumen de Rendimiento y Disponibilidad (REHACER).
	 */
	function getDatosEstadisticaDetallado() {
		
		/* LISTA DE OBJETIVOS */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE MONITORES DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_mon) {
				$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];
				
				/* LISTA DE CANTIDADES DE MONITOREOS POR MONITOR */
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("estadisticas", "estadistica")) as $estadistica_mon) {
					$monitor->total_monitoreos = $estadistica_mon->getAttribute("cantidad");
				}
				
				/* LISTA DE PASOS DEL OBJETIVO POR MONITOR */
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("detalles", "detalle")) as $detalle_pas) {
					if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
						$paso = clone $objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')];
						
						/* DATOS ESTADISTICOS DEL PASO */
						foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("estadisticas", "estadistica")) as $estadistica_pas) {
							$this->tiene_datos = true;
							
							$paso->promedio_respuesta = $estadistica_pas->getAttribute("tiempo_prom");
							$paso->minimo_respuesta = $estadistica_pas->getAttribute("tiempo_min");
							$paso->maximo_respuesta = $estadistica_pas->getAttribute("tiempo_max");
							$paso->__eventos[1] = new DatosEvento(1, "Uptime");
							$paso->__eventos[1]->porcentaje = $estadistica_pas->getAttribute("uptime");
							$paso->__eventos[2] = new DatosEvento(2, "Downtime");
							$paso->__eventos[2]->porcentaje = $estadistica_pas->getAttribute("downtime");
							$paso->__eventos[7] = new DatosEvento(7, "Sin Monitoreo");
							$paso->__eventos[7]->porcentaje = $estadistica_mon->getAttribute("sin_monitoreo");
							$monitor->__pasos[] = $paso;
						}
					}
				}
				$objetivo->__monitores[] = $monitor;
			}
		}
	}
	
	/* 
	 * Funcion para obtener los datos de la tabla de 
	 * Resumen de Rendimiento por Dia de la Semana.
	 */
	function getDatosEstadisticaPorDia() {
		
		/* LISTA DE OBJETIVOS */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			$objetivo->__dias = array();
			
			/* LISTA DE DIAS DE LA SEMANA PARA EL GRAFICO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_dia) {
				$dia_id = $detalle_dia->getAttribute("dia_id");
				if ($dia_id == 0) {
					$dia_id = 7;
				}
				$objetivo->__dias[$dia_id] = array();
				
				/* LISTA DE PASOS DEL OBJETIVO */
				foreach (Utiles::getElementsByArrayTagName($detalle_dia, array("detalles", "detalle")) as $detalle_pas) {
					if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
						$paso = clone $objetivo->__pasos[$detalle_pas->getAttribute("paso_orden")];
						
						/* DATOS DEL RENDIMIENTO DEL PASO */
						foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("estadisticas", "estadistica")) as $estadistica_pas) {
							$this->tiene_datos = true;
							
							$paso->promedio_respuesta = $estadistica_pas->getAttribute("tiempo_prom");
							$paso->minimo_respuesta = $estadistica_pas->getAttribute("tiempo_min");
							$paso->maximo_respuesta = $estadistica_pas->getAttribute("tiempo_max");
						}
						$objetivo->__dias[$dia_id][] = $paso;
					}
				}
			}
			ksort($objetivo->__dias);
		}
	}
	
	function getDatosRendimientoPorDia() {
		
		/* LISTA DE OBJETIVOS */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE PASOS DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_pas) {
				
				if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
					$paso = & $objetivo->__pasos[$detalle_pas->getAttribute("paso_orden")];
					$paso->__dias = array();

					/* LISTA DE DIAS DE LA SEMANA PARA EL GRAFICO */
					foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("detalles", "detalle")) as $detalle_dia) {
						$dia_id = $detalle_dia->getAttribute("dia_id");
						if ($dia_id == 0) {
							$dia_id = 7;
						}
						$paso->__dias[$dia_id] = array();
						
						/* DATOS DEL RENDIMIENTO DEL PASO */
						foreach (Utiles::getElementsByArrayTagName($detalle_dia, array("estadisticas", "estadistica")) as $estadistica_dia) {
							$this->tiene_datos = true;
							
							$paso->__dias[$dia_id][$estadistica_dia->getAttribute("hora")] = $estadistica_dia->getAttribute("tiempo_prom");
						}
					}
					ksort($paso->__dias);
				}
			}
		}
	}
	
	function getDatosRendimientoPonderado() {
		
		/* LISTA DE OBJETIVOS */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];

			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_pon) {
				$ponderacion = clone $this->__ponderaciones[$detalle_pon->getAttribute('item_id')];

				/* LISTA DE PASOS DEL OBJETIVO */
				foreach (Utiles::getElementsByArrayTagName($detalle_pon, array("detalles", "detalle")) as $detalle_pas) {
					if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
						$paso = clone $objetivo->__pasos[$detalle_pas->getAttribute("paso_orden")];

						/* DATOS DEL RENDIMIENTO DEL PASO */
						foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("datos", "dato")) as $dato_pas) {
							$this->tiene_datos = true;
							
							$paso->promedio_respuesta = $dato_pas->getAttribute("tiempo_prom");
							$paso->minimo_respuesta = $dato_pas->getAttribute("tiempo_min");
							$paso->maximo_respuesta = $dato_pas->getAttribute("tiempo_max");
						}
						$ponderacion->__pasos[$paso->paso_id] = $paso;
					}
				}
				$objetivo->__ponderaciones[$ponderacion->ponderacion_item_id] = $ponderacion;
			}
		}
	}
	
	function getDatosDisponibilidadPonderadaPorItem() {
		
		/* LISTA DE OBJETIVOS DEL GRAFICO */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE MONITORES DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_mon) {
				if (isset($this->__monitores[$detalle_mon->getAttribute('nodo_id')])) {
				
				$monitor = clone $this->__monitores[$detalle_mon->getAttribute('nodo_id')];
				
				/* LISTA DE PASOS DEL OBJETIVO POR MONITOR */
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("detalles", "detalle")) as $detalle_pas) {
					if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
						$paso = clone $objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')];

						/* LISTA DE PASOS DEL OBJETIVO POR MONITOR */
						foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("detalles", "detalle")) as $detalle_pon) {
							$ponderacion = clone $this->__ponderaciones[$detalle_pon->getAttribute('item_id')];
						
						
							/* LISTA DE PORCENTAJES DE EVENTOS DEL PASO */
							foreach (Utiles::getElementsByArrayTagName($detalle_pon, array("estadisticas", "estadistica")) as $dato_pas) {
								$this->tiene_datos = true;
								
								$evento = clone $this->__eventos[$dato_pas->getAttribute('evento_id')];
								$evento->porcentaje = $dato_pas->getAttribute('porcentaje');
								$evento->duracion = $dato_pas->getAttribute('duracion');
								$ponderacion->__eventos[$evento->evento_id] = $evento;
							}
							$paso->__ponderaciones[$ponderacion->ponderacion_item_id] = $ponderacion;
						}
						ksort($paso->__eventos);
						$monitor->__pasos[] = $paso;
					}
				}
				$objetivo->__monitores[$monitor->monitor_id] = $monitor;
				
				}
			}
		}
	}

	/* 
	 * Funcion para obtener los datos de la tabla de 
	 * Disponibilidad downtime global.
	 */
	function getDisponibilidadDowntime() {
		
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];

			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("datos")) as $datos_obj) {
				$objetivo->__acumulado= $datos_obj->getAttribute("acumulado");
			
				/*LISTA DE DATOS EVENTOS CONSOLIDADOS*/
				foreach (Utiles::getElementsByArrayTagName($datos_obj, array("dato")) as $dato_obj) {
					$this->tiene_datos = true;
					
					$dato = new DatosPeriodo($dato_obj->getAttribute("inicio"), $dato_obj->getAttribute("termino"));
					$dato->evento_id= $dato_obj->getAttribute("evento_id");
					$dato->duracion = $dato_obj->getAttribute("duracion");			
					$objetivo->__downtime[] = $dato;
				}
			}
			
			/* LISTA DE PASOS DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_pas) {
				
				if (isset($objetivo->__pasos[$detalle_pas->getAttribute("paso_orden")])) {
				$paso = & $objetivo->__pasos[$detalle_pas->getAttribute("paso_orden")];

				foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("datos")) as $datos_pas) {
					$paso->__acumulado= $datos_pas->getAttribute("acumulado");
					
					/* DATOS EVENTOS DEL PASO*/
					foreach (Utiles::getElementsByArrayTagName($datos_pas, array("dato")) as $dato_pas) {
						$this->tiene_datos = true;
						
						$dato = new DatosPeriodo($dato_pas->getAttribute("inicio"), $dato_pas->getAttribute("termino"));
						$dato->evento_id= $dato_pas->getAttribute("evento_id");
						$dato->duracion = $dato_pas->getAttribute("duracion");			
						$paso->__downtime[] = $dato;
					}
				}
				}
			}
		}
	}
	
	/*Funcion para obtener los datos parseados de elemento plus*/
	function getElementosPlus(){
		/* LISTA DE OBJETIVOS */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE MONITORES DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_mon) {
				$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];
				
				/* TOTALES DE LOS ELEMENTOS POR MONITOR */
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("estadisticas", "estadistica")) as $estadistica_mon) {
					$monitor->total_tamano = $estadistica_mon->getAttribute("tamano_total");
				}
				
				/* LISTA DE ELEMENTOS POR MONITOR */
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("datos", "dato")) as $dato_mon) {
					$this->tiene_datos = true;
					
					$elemento = new DatosElemento();
					$elemento->cantidad = $estadistica_mon->getAttribute("cantidad");
					$elemento->tiempo_total = $estadistica_mon->getAttribute("tiempo_total");
					$elemento->url = $dato_mon->getAttribute("url");
					$elemento->tamano_header = $dato_mon->getAttribute("tamano_cabecera");
					$elemento->tamano_body = $dato_mon->getAttribute("tamano_cuerpo");
					$elemento->tipo = $dato_mon->getAttribute("content_type");
					$elemento->http_status = $dato_mon->getAttribute("status");
					$elemento->ip= $dato_mon->getAttribute("ip");
					$elemento->espera= $dato_mon->getAttribute("espera");
					$elemento->latencia= $dato_mon->getAttribute("latencia");
					$elemento->descarga= $dato_mon->getAttribute("descarga");
					$elemento->tiempo_dns= $dato_mon->getAttribute("tiempo_dns");
					$elemento->es_ok= $dato_mon->getAttribute("es_ok");
					$monitor->__elementos[] = $elemento;
				}
				$objetivo->__monitores[] = $monitor;
			}
		}
		
	}

	function getDatosDetalleElementosPlus(){

		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE MONITORES DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_mon) {
				if(isset($this->__monitores[$detalle_mon->getAttribute("nodo_id")])){
					$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];
					
					/* TOTALES DE LOS ELEMENTOS POR MONITOR */
					foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("detalles", "detalle")) as $detalle_fecha) {
/*						if (isset($objetivo->__subobjetivos[$detalle_fecha->getAttribute('subobjetivo_id')])) {
							$subobjetivo = clone $objetivo->__subobjetivos[$detalle_fecha->getAttribute('subobjetivo_id')];
						}
						else {
							$subobjetivo = clone $this->__objetivos[$detalle_fecha->getAttribute('subobjetivo_id')];
						}*/
	
						$estado_general_id = 0;
						$monitoreo = new DatosMonitoreo($estado_general_id);
						foreach (Utiles::getElementsByArrayTagName($detalle_fecha, array("datos", "dato")) as $datos) {
							if (isset($objetivo->__pasos[$datos->getAttribute("paso_orden")])) {
							
							$this->tiene_datos = true;
							
							$paso = clone $objetivo->__pasos[$datos->getAttribute("paso_orden")];
							$paso->promedio_respuesta = $datos->getAttribute("respuesta");
							$paso->tamanno_total = $datos->getAttribute("tamano_total");
							$paso->elementos_ok = $datos->getAttribute("estado");
							$monitoreo->__pasos[$paso->paso_id] = $paso;
//							$subobjetivo->__pasos[$paso->paso_id] = $paso;
	
							if ($datos->getAttribute("estado") == 'true') {
								$estado_general_id = 1; 
							}
							}
						}
						
						$monitoreo->fecha = $detalle_fecha->getAttribute("fecha");
						$monitoreo->evento_id = $estado_general_id;
//						$monitoreo->__subobjetivo = $subobjetivo;
						$monitor->__monitoreos[] = $monitoreo;
					}
					$objetivo->__monitores[$monitor->monitor_id] = $monitor;
				}
			}
		}
	}
	
	
	function getDatosDetalleRegistrosPlus(){

		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
		
			/* LISTA DE MONITORES DEL OBJETIVO */		
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as  $detalle_mon) {
				
				if (isset($this->__monitores[$detalle_mon->getAttribute("nodo_id")])) {
				
				$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];				
							
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("detalles", "detalle")) as $detalle_monitoreo) {				
/*					if (isset($objetivo->__subobjetivos[$detalle_monitoreo->getAttribute('subobjetivo_id')])) {
						$subobjetivo = clone $objetivo->__subobjetivos[$detalle_monitoreo->getAttribute('subobjetivo_id')];
					}
					else {
						$subobjetivo = clone $this->__objetivos[$detalle_monitoreo->getAttribute('subobjetivo_id')];
					}*/
					
					$estado_general_id = 0;
					$monitoreo = new DatosMonitoreo($estado_general_id);
					
					/* LISTA DE PASOS DEL OBJETIVO */
					foreach (Utiles::getElementsByArrayTagName($detalle_monitoreo, array("detalles", "detalle")) as $detalle_pas) {
						
						if (isset($objetivo->__pasos[$detalle_pas->getAttribute("paso_orden")])) {
						
						$paso = clone $objetivo->__pasos[$detalle_pas->getAttribute("paso_orden")];

						/* LISTA DE ELEMENTOS POR MONITOR */
						foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("datos", "dato")) as $dato_dato) {
							$this->tiene_datos = true;
							
							$paso->ip = $dato_dato->getAttribute("ip");
							$codigos = explode(',', $dato_dato->getAttribute("estado"));

							foreach (Utiles::getElementsByArrayTagName($dato_dato, array("patrones", "patron")) as $xml_patron) {
								$patron = new DatosPatron($xml_patron->getAttribute("orden"));
								$patron->nombre = $xml_patron->getAttribute("nombre");
								$patron->inverso = $xml_patron->getAttribute("es_inverso");
								$patron->opcional = $xml_patron->getAttribute("es_opcional");
								$patron->__evento = clone $this->__codigos[$codigos[$patron->patron_id]];
								$paso->__patrones[$patron->patron_id] = $patron;
								
								// TODO: esto lo debe entregar el xml.
								if ($patron->__evento->evento_id != 0) {
									$estado_general_id = 1;
								}
							}
							foreach (Utiles::getElementsByArrayTagName($dato_dato, array("registros", "registro")) as $xml_registro) {
								$registro = new DatosRegistroPlus($xml_registro->getAttribute("orden"));
								$registro->nombre = $xml_registro->getAttribute("nombre");
								$registro->valor = $xml_registro->nodeValue;
								$paso->__registros[$registro->orden] = $registro;
							}
						}
//						$subobjetivo->__pasos[$paso->paso_id] = $paso;
						$monitoreo->__pasos[$paso->paso_id] = $paso;
						}
						
					}
					$monitoreo->fecha = $detalle_monitoreo->getAttribute("fecha");
					$monitoreo->evento_id = $estado_general_id;
					$monitoreo->monitor_nombre = $this->__monitores[$detalle_monitoreo->getAttribute("nodo_id")]->nombre;
					$monitoreo->nodo_id = $detalle_monitoreo->getAttribute("nodo_id");
//					$monitoreo->__subobjetivo = $subobjetivo;
					$monitor->__monitoreos[] = $monitoreo;
				}
				$objetivo->__monitores[$monitor->monitor_id] = $monitor;
				
				}
			}
		}
	}
	
	
	function getDatosDetalleScreenshot(){
	
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
	
			/* LISTA DE MONITORES DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_monitoreo) {
				$monitoreo = new DatosMonitoreo(0);
				$monitoreo->fecha = $detalle_monitoreo->getAttribute("fecha");
				$monitoreo->nodo_id = $detalle_monitoreo->getAttribute("nodo_id");
				
				foreach (Utiles::getElementsByArrayTagName($detalle_monitoreo, array("detalles", "detalle")) as $detalle_pas) {
					if (isset($objetivo->__pasos[$detalle_pas->getAttribute("paso_orden")])) {
					
					$this->tiene_datos = true;
	
					$paso = clone $objetivo->__pasos[$detalle_pas->getAttribute("paso_orden")];
					$paso->estados = $detalle_pas->getAttribute("estado");
					
					$paso->__screenshots = array();
					foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("datos", "dato")) as $detalle_screen) {
						$paso->__screenshots[] = $detalle_screen->getAttribute("window");
					}
					$monitoreo->__pasos[] = $paso;
					}
				}
				$objetivo->__monitoreos[] = $monitoreo;
			}
		}
	}
	
/*	function getDatosDetalleScreenshotOld(){

		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
		
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as  $detalle_mon) {
				$monitor = clone $this->__monitores[$detalle_mon->getAttribute("nodo_id")];									
				
				foreach (Utiles::getElementsByArrayTagName($detalle_mon, array("detalles", "detalle")) as $detalle_monitoreo) {									
					$subobjetivo = clone $objetivo;									
					$estado_general_id = 0;
					
					foreach (Utiles::getElementsByArrayTagName($detalle_monitoreo, array("datos", "dato")) as $detalle_pas) {
						$this->tiene_datos = true;
						
						$paso = clone $subobjetivo->__pasos[$detalle_pas->getAttribute("paso_orden")];
						$paso->estados = $detalle_pas->getAttribute("estado");
						$paso->screenshot = $detalle_pas->getAttribute("tipo_screenshot");
						$paso->__window = explode(",",$detalle_pas->getAttribute("window"));
						$subobjetivo->__pasos[$paso->paso_id] = $paso;
						$codigos = explode(',', $paso->estados);
						foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("patrones", "patron")) as $xml_patron) {
							$patron = new DatosPatron($xml_patron->getAttribute("orden"));
							$patron->nombre = $xml_patron->getAttribute("nombre");
							$patron->inverso = $xml_patron->getAttribute("es_inverso");
							$patron->opcional = $xml_patron->getAttribute("es_opcional");
							$patron->__evento = clone $this->__codigos[$codigos[$patron->patron_id]];
							$paso->__patrones[$patron->patron_id] = $patron;
								
							if ($patron->__evento->evento_id != 0) {
								$estado_general_id = 1;
							}
						}
					}
					$monitoreo = new DatosMonitoreo($estado_general_id);
					$monitoreo->fecha = $detalle_monitoreo->getAttribute("fecha");
					$monitoreo->monitor_nombre = $this->__monitores[$detalle_monitoreo->getAttribute("nodo_id")]->nombre;
					$monitoreo->nodo_id = $detalle_monitoreo->getAttribute("nodo_id");					
					$monitoreo->__subobjetivo = $subobjetivo;
					$monitor->__monitoreos[] = $monitoreo;
					
				}
				$objetivo->__monitores[$monitor->monitor_id] = $monitor;
			}
		}
	}*/
	
	function getDatosDisponibilidadPorDia() {
		
		/* LISTA DE OBJETIVOS */
		foreach (Utiles::getElementsByArrayTagName($this->dom, array("detalles", "detalle")) as $detalle_obj) {
			$objetivo = & $this->__objetivos[$detalle_obj->getAttribute('objetivo_id')];
			
			/* LISTA DE PASOS DEL OBJETIVO */
			foreach (Utiles::getElementsByArrayTagName($detalle_obj, array("detalles", "detalle")) as $detalle_pas) {
				
				if (isset($objetivo->__pasos[$detalle_pas->getAttribute('paso_orden')])) {
					$paso = & $objetivo->__pasos[$detalle_pas->getAttribute("paso_orden")];

					foreach (Utiles::getElementsByArrayTagName($detalle_pas, array("detalles", "detalle")) as $detalle_fecha) {
						$paso->__eventos[$detalle_fecha->getAttribute('fecha')] = array();
				
						/* LISTA DE PORCENTAJES DE EVENTOS DEL PERIODO */
						foreach (Utiles::getElementsByArrayTagName($detalle_fecha, array("estadisticas", "estadistica")) as $estadistica_obj) {
							$this->tiene_datos = true;
							
							$evento = clone $this->__eventos[$estadistica_obj->getAttribute('evento_id')];
							$evento->porcentaje = $estadistica_obj->getAttribute('porcentaje');
							$paso->__eventos[$detalle_fecha->getAttribute('fecha')][$evento->evento_id] = $evento;
						}
//						ksort($objetivo->__eventos[$detalle_fecha->getAttribute('fecha')]);
					}
				}
			}
		}
	}
	
	
}


?>