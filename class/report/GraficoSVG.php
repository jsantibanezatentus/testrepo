<?

class GraficoSVG extends Grafico {

	var $tipo;
	var $botones;

	function GraficoSVG() {
		$this->tiempo_expiracion = 86400;
		$this->resultado = $this->__generarMensajeError();
		$this->botones = null;
	}



	/*************** FUNCIONES DE GRAFICOS DE DISPONIBILIDAD ***************/
	/*************** FUNCIONES DE GRAFICOS DE DISPONIBILIDAD ***************/
	/*************** FUNCIONES DE GRAFICOS DE DISPONIBILIDAD ***************/
	/*
	Creado por:
	Modificado por: Carlos Sepúlveda
	Fecha de creacion:14-11-2016
	Fecha de ultima modificacion:16-06-2017
	*/
	// TODO: getDisponibilidadConsolidadoObjetivos()
	function getDisponibilidadSemaforo(){
		global $current_usuario_id;
		global $usr;
		
		$event = new Event;

		# Variables para eventos especiales marcados por el cliente codigo 9.
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		$periodoInicio = $usr->periodo_semaforo_inicio;
		$periodoTermino = date("Y-m-d H:i:s");
		$marcado = false;
		$objetives = null;
		
		$xpath = $this->getDatosDisponibilidadSemaforo();

		//	TEMPLATE DEL GRAFICO
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_global.xhtml');
		$T->setBlock('tpl_grafico', 'BLOQUE_RESOURCES', 'bloque_resources');
		$T->setBlock('tpl_grafico', 'BLOQUE_EVENTO', 'bloque_evento');
		$T->setBlock('tpl_grafico', 'BLOQUE_OBJETIVOS', 'bloque_objetivos');
		$T->setBlock('tpl_grafico', 'BLOQUE_GRUPOS', 'bloque_grupos');

		$T->setVar('bloque_resources', '');
		$T->setVar('bloque_evento', '');
		$T->setVar('bloque_objetivos', '');
		$T->setVar('bloque_grupos', '');

		$T->setVar('__path_jquery_ui', REP_PATH_JQUERY_UI);

		$datos='';
		$ids = '';
		
		$periodIni = strtotime(Utiles::convertDateTimeZone($periodoInicio, $timeZone));
		$periodEnd = strtotime(Utiles::dateTimeZone($timeZone));
		
		# Busca si exiten marcados y los almacena.
		foreach ($xpath->query("/atentus/resultados/detalles/marcado") as $tag_marcado) {
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
		}		

		# Obetener los datos de mantenimiento.
		if ($marcado == true) {

			$dataMant =$event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			# Guarda los objetivos del marcado.
			foreach ($dataMant as $key => $value) {
					
				$objetives[$key] = explode(',',str_replace($character,"",($value['objetivo_id'])));
			}
		}	
		foreach ($xpath->query("/atentus/resultados/propiedades/objetivos/objetivo") as $objetivo) {
			$T->setVar('__resource_id', $objetivo->getAttribute('objetivo_id'));
			$T->setVar('__resource_name', $objetivo->getAttribute('nombre'));
			$T->setVar('__resource_parent', '');
			$T->setVar('__resource_expanded', 'false');
			$T->setVar('__resource_style', '');
			$T->parse('bloque_resources', 'BLOQUE_RESOURCES', true);

			$datos=$datos."'".$objetivo->getAttribute('objetivo_id')."':[";

			foreach ($xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$objetivo->getAttribute('objetivo_id')."]/datos/dato") as $tag_dato) {
				$fechaini = strtotime($tag_dato->getAttribute('inicio'));
				$FechaInicio = date('Y.m.d.H.i.s',$fechaini);
				$fechaterm = strtotime($tag_dato->getAttribute('termino'));
				$FechaTermino = date('Y.m.d.H.i.s',$fechaterm);
				
				$datos= $datos."['".$FechaInicio."','".$FechaTermino."','".Utiles::getStyleDisponibilidad($tag_dato->getAttribute('evento_id'))."'],";
			}
			$datos =$datos."],";
		}
		$T->setVar('__datos', $datos);

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'));
	}
	/*
	Creado por: Aldo Cruz Romero
	Fecha de creacion:05-11-2018
	Fecha de ultima modificacion:
	*/
	
	function getDisponibilidadHora() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		$event = new Event;
		$xpath = $this->getDatosConsolidadoDisponibilidad();
		if($xpath==null){
            $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
            $T->setFile('tpl_contenido', 'sorry_reporte.tpl');
            $this->resultado =  $T->pparse('out', 'tpl_contenido');
            return;
        }
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id!=0]");
		
		if ($xpath->query("/atentus/resultados/propiedades[@es_recarga=1]")->length == 1) {
			$this->resultado = "RELOAD";
			$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
			return;
		}
		
		if ($conf_pasos->length == 0 or $xpath->query("/atentus/resultados/detalles/detalle/datos/dato")->length == 0){
			$this->resultado = $this->__generarContenedorSVG($this->__generarContenedorSinDatos());
			return;
		}
		//TEMPLATE DEL GRAFICO
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_hora.xhtml');
		$T->setBlock('tpl_grafico', 'BLOQUE_DATOS', 'bloque_datos');
		$T->setBlock('tpl_grafico', 'BLOQUE_DATOS_MONITORES', 'bloque_datos_monitores');
		$T->setBlock('tpl_grafico', 'BLOQUE_MONITORES', 'bloque_monitores');
		

		$T->setVar('__nombre_objetivo', $conf_objetivo->getAttribute("nombre"));
		$T->setVar('__objetive_id', $this->objetivo_id);
		$T->setVar('__path_amcharts',REP_PATH_AMCHARTS);
		
		//BLOQUE GRAFICO DISPONIBILIDAD GENERAL
		$T->setVar('bloque_datos', '');
		foreach ($xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$conf_objetivo->getAttribute('objetivo_id')."]/datos/dato") as $key2 => $tag_objetivo) {
			
			if($tag_objetivo->getAttribute("evento_id")=='1'){
				$evento='Uptime Global';
				$color='#54a51c';
			}elseif ($tag_objetivo->getAttribute("evento_id")=='2') {
				$evento='Downtime Global';
				$color='#d22129';
			}elseif ($tag_objetivo->getAttribute("evento_id")=='3') {
				$evento='Downtime Parcial';
				$color='#fdc72e';
			}elseif ($tag_objetivo->getAttribute("evento_id")=='9') {
				$evento='Marcado';
				$color='#0000FF';
			}elseif ($tag_objetivo->getAttribute("evento_id")=='7') {
				$evento='No Monitoreo';
				$color='#eeeeee';
			}
			$inicio=$tag_objetivo->getAttribute("inicio");
			$termino=$tag_objetivo->getAttribute("termino");
			$T->setVar('__evento_id',$evento);
			$T->setVar('__inicio',$inicio);
			$T->setVar('__termino',$termino);
			$T->setVar('__color',$color);
			$T->parse('bloque_datos', 'BLOQUE_DATOS', true);
		}
		
		//BlOQUE GRAFICO DISPONIBILIDAD POR NODO
		$T->setVar('bloque_monitores', '');
		foreach ($conf_nodos as $key => $data_nodos) {
			$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=".$data_nodos->getAttribute('nodo_id')."]/datos/dato");
			$T->setVar('__nombre_monitor',$data_nodos->getAttribute('nombre'));
			
			$T->setVar('bloque_datos_monitores', '');
			foreach ($tag_datos as $i => $tag_dato) {
				if($tag_dato->getAttribute("evento_id")=='1'){
					$evento_monitor='Uptime Global';
					$color_monitor='#54a51c';
				}elseif ($tag_dato->getAttribute("evento_id")=='2') {
					$evento_monitor='Downtime Global';
					$color_monitor='#d22129';
				}elseif ($tag_dato->getAttribute("evento_id")=='3') {
					$evento_monitor='Downtime Parcial';
					$color_monitor='#fdc72e';
				}elseif ($tag_dato->getAttribute("evento_id")=='9') {
					$evento_monitor='Marcado';
					$color_monitor='#0000FF';
				}elseif ($tag_dato->getAttribute("evento_id")=='7') {
					$evento_monitor='No Monitoreo';
					$color_monitor='#eeeeee';
				}
				$T->setVar('__inicio_monitor',$tag_dato->getAttribute("inicio"));
				$T->setVar('__termino_monitor',$tag_dato->getAttribute("termino"));
				$T->setVar('__evento_monitor',$evento_monitor);
				$T->setVar('__color_monitor',$color_monitor);
				$T->parse('bloque_datos_monitores', 'BLOQUE_DATOS_MONITORES', true);
			}
			$T->parse('bloque_monitores', 'BLOQUE_MONITORES', true);
		}
		$this->resultado = $T->parse('out', 'tpl_grafico');
	
	}

	/*
	Creado por:
	Modificado por: Carlos Sepúlveda
	Fecha de creacion:-.
	Fecha de ultima modificacion:16-06-2017
	*/
	// TODO: metodo getDisponibilidadConsolidado()
	function getConsolidadoDisponibilidad() {
		global $current_usuario_id;		
		global $usr;
		$event = new Event;
				
		# Variables para eventos especiales marcados por el cliente codigo 9.
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		
		$tieneEvento = 'false';
		$nameFunction = 'consolidado_disponibilidad'.$this->objetivo_id.''.$this->extra['horario_id_item'];

		$encode = null;
		$dataMant = null;
		$ids = null;
		$marcado = false;
		
		
		$xpath = $this->getDatosConsolidadoDisponibilidad();
            /*Se agrega condicion para cuando el cliente no este asociado al objetivo (Reportes Audex)*/
            if($xpath==null){
                $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
                $T->setFile('tpl_contenido', 'sorry_reporte.tpl');
                $this->resultado =  $T->pparse('out', 'tpl_contenido');
                return;
            }
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id!=0]");
		
		if ($xpath->query("/atentus/resultados/propiedades[@es_recarga=1]")->length == 1) {
			$this->resultado = "RELOAD";
			$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
			return;
		}
		
		if ($conf_pasos->length == 0 or $xpath->query("/atentus/resultados/detalles/detalle/datos/dato")->length == 0){
			$this->resultado = $this->__generarContenedorSVG($this->__generarContenedorSinDatos());
			return;
		}

		# Busca y guarda los eventos marcados por el cliente
		foreach ($xpath->query("/atentus/resultados/detalles/detalle/marcado") as $tag_marcado) {
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
		}			

		# Verifica si existe marcado.
		if ($marcado == true) {
			$dataMant =$event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			$tieneEvento = 'true';
			// Variable que sera enviada al tpl accordion.
			$encode = json_encode($dataMant);
		}

		//TEMPLATE DEL GRAFICO
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_consolidado.xhtml');
	
		$T->setBlock('tpl_grafico', 'BLOQUE_RESOURCES', 'bloque_resources');
		$T->setBlock('tpl_grafico', 'BLOQUE_GRUPOS', 'bloque_grupos');
		$T->setBlock('tpl_grafico', 'BLOQUE_MONITORES', 'bloque_monitores');
		$T->setVar('__path_jquery_ui', REP_PATH_JQUERY_UI);
		if ($_REQUEST['multi_obj'] < 1) {
			$pdf = 0;
		}else{
			$pdf = 1;
		}
		$T->setVar('__pdf', $pdf);
		$es_online = true;
		if (isset($this->extra["imprimir"]) and $this->extra["imprimir"] != 0) {
			$es_online = false;
		}

		$T->setVar('__item_orden', $this->extra["item_orden"]);
		$T->setVar('__link_eventos', ($this->extra["imprimir"])?'f':'t');
                if (isset($this->extra["monitor"]) and $this->extra["monitor"] != null) {
                    $T->setVar('__monitor_id', $this->extra["monitor"]);
                } else {
                    $T->setVar('__monitor_id', '0');
                }
		
		$T->setVar('__monitor_nombre', "Consolidado");
		$T->setVar('__monitor_orden', '1');
		$T->setVar('__tiene_titulo', ($es_online)?"none":"inline");
		$T->setVar('__tipo_id', 'consolidada');

		$T->setVar('__resource_id', $conf_objetivo->getAttribute('objetivo_id'));
		$T->setVar('__resource_name', $conf_objetivo->getAttribute('nombre'));
		$T->setVar('__resource_parent', '0');                                                                                                                                                                                          
		$T->parse('bloque_resources', 'BLOQUE_RESOURCES', true);

		$T->setVar('__grupo_id', $conf_objetivo->getAttribute('objetivo_id'));
		$T->parse('bloque_grupos', 'BLOQUE_GRUPOS', true);

		$datos = "'".$conf_objetivo->getAttribute('objetivo_id')."':[";
		$arrDataMaint =  array();
		# Se agregan los eventos especiales a los datos junto con los demás.

		foreach ($xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$conf_objetivo->getAttribute('objetivo_id')."]/datos/dato") as $key2 => $tag_objetivo) {
			$isEventSpecial = false;
			$id = null;
			$name = null;
			$title = null;
			# Agrega los eventos creados por el usuarios.
			if (!is_null($dataMant)){
				foreach ($dataMant as $key => $value) {
					$dateStart = strtotime(Utiles::convertDateTimeZone($value['fecha_inicio'], $timeZone));
					$dateEnd = strtotime(Utiles::convertDateTimeZone($value['fecha_termino'], $timeZone));
					
					if ((strtotime($tag_objetivo->getAttribute('inicio')) == $dateStart) && (strtotime($tag_objetivo->getAttribute('termino')) == $dateEnd)){
						$isEventSpecial = true;
						$id = $value['id'];
						$name = $value['nombre'];
						$title = $value['titulo'];
						$arrDataMaint[$key2] = array('id' => $id, 'name' => $name, 'title' => $title, 'objetiveId' => $conf_objetivo->getAttribute('objetivo_id'), 'idEvent'=>$key2);
						break;
					}				
				}
			}	

			$nameEvent = ($isEventSpecial == true)? Utiles::getStyleDisponibilidad(9): Utiles::getStyleDisponibilidad($tag_objetivo->getAttribute('evento_id'));
			
			$datos = $datos."['".date('Y.m.d.H.i.s', strtotime($tag_objetivo->getAttribute('inicio')))."','".
					 date('Y.m.d.H.i.s', strtotime($tag_objetivo->getAttribute('termino')))."','".$nameEvent."', '".$title."', '".$name."', '".$id."', '".date('Y.m.d.H.i.s', strtotime($tag_objetivo->getAttribute('inicio')))."'],";
		}
		$datos =$datos."],";
		// DATOS DEL MONITOR
		foreach ($conf_nodos as $key => $conf_nodo) {
			$T->setVar('__resource_id', $conf_objetivo->getAttribute('objetivo_id')."-".$conf_nodo->getAttribute('nodo_id'));
			$T->setVar('__resource_name', $conf_nodo->getAttribute('nombre'));
			$T->setVar('__resource_parent', $conf_objetivo->getAttribute('objetivo_id'));
			$T->parse('bloque_resources', 'BLOQUE_RESOURCES', true);

			$T->setVar('__grupo_id', $conf_objetivo->getAttribute('objetivo_id')."-".$conf_nodo->getAttribute('nodo_id'));
			$T->parse('bloque_grupos', 'BLOQUE_GRUPOS', true);

			$datos = $datos."'".$conf_objetivo->getAttribute('objetivo_id')."-".$conf_nodo->getAttribute('nodo_id')."':[";
			$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=".$conf_nodo->getAttribute('nodo_id')."]/datos/dato");
			# Se agregan los eventos especiales.
			foreach ($tag_datos as $i => $tag_dato) {
				$id = null;
				$name = null;
				$title = null;
				foreach ($arrDataMaint as $key => $value) {
					if ($value['idEvent'] == $i){
						$id = $value['id'];
						$name = $value['name'];
						$title = $value['title'];
						break;
					}
				}
				$datos = $datos."['".date('Y.m.d.H.i.s', strtotime($tag_dato->getAttribute('inicio')))."','".
						 date('Y.m.d.H.i.s', strtotime($tag_dato->getAttribute('termino')))."','".
						 Utiles::getStyleDisponibilidad($tag_dato->getAttribute('evento_id'))."', '".$title."', '".$name."', '".$id."', '".date('Y.m.d.H.i.s', strtotime($tag_dato->getAttribute('inicio')))."'],";
						
			}
			$datos = $datos."],";
		}

		$T->setVar('__datos', $datos);
		$T->setVar('__horario_habil', '');
		$horario_habil = "";
		foreach ($xpath->query("/atentus/resultados/propiedades/horarios_habiles/horario_habil") as $conf_horario) {
			$horario_habil.= ($horario_habil ? ",":"").
							 "[\"".date("Y.m.d.H.i.s", strtotime($conf_horario->getAttribute("inicio")))."\",".
							 "\"".date("Y.m.d.H.i.s", strtotime($conf_horario->getAttribute("termino")))."\"]";
		}
		
		$T->setVar('__horario_habil', $horario_habil);
		$T->setVar('__tiene_evento', $tieneEvento);
		$T->setVar('__name', $nameFunction);
		$T->parse('bloque_monitores', 'BLOQUE_MONITORES', true);
		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $T->parse('out', 'tpl_grafico');
		$this->resultado.= $this->getConsolidadoDisponibilidadMonitor($dataMant, $timeZone);
		
		# Agrega el acordeon cuando existan eventos.
		if (count($dataMant)>0){
			$this->resultado.= $this->getAccordion($encode, $nameFunction);
		}		
	}

	function getDisponibilidadConsolidadoEspecial() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$sql = "SELECT * FROM reporte.disponibilidad_detalle_consolidado_habil(".
  				pg_escape_string($current_usuario_id).", ".
  				pg_escape_string($this->objetivo_id).", ".
  				pg_escape_string($this->extra["horario_id_item"]).", '".
  				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
  				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

  		$res = & $mdb2->query($sql);
  		if (MDB2::isError($res)) {
 			$log->setError($sql, $res->userinfo);
 			exit();
 		}

 		if($row = $res->fetchRow()){
 			$dom = new DomDocument();
 			$dom->preserveWhiteSpace = FALSE;
  			$dom->loadXML($row['disponibilidad_detalle_consolidado_habil']);
  			$xpath = new DOMXpath($dom);
  			unset($row["disponibilidad_detalle_consolidado_habil"]);
		}

		$conf_uptime = $xpath->query("/atentus/resultados/detalles/detalle/estadisticas/estadistica[@evento_id=1]");
		$conf_downtime = $xpath->query("/atentus/resultados/detalles/detalle/estadisticas/estadistica[@evento_id=2]");
		$conf_parcial = $xpath->query("/atentus/resultados/detalles/detalle/estadisticas/estadistica[@evento_id=3]");
		$conf_no_monitoreo = $xpath->query("/atentus/resultados/detalles/detalle/estadisticas/estadistica[@evento_id=7]");
		$conf_marcado = $xpath->query("/atentus/resultados/detalles/detalle/estadisticas/estadistica[@evento_id=9]");

		$conf_uptime_nombre = $xpath->query(" /atentus/resultados/propiedades/eventos/evento[@evento_id=1]");
		$conf_downtime_nombre = $xpath->query(" /atentus/resultados/propiedades/eventos/evento[@evento_id=2]");
		$conf_parcial_nombre = $xpath->query(" /atentus/resultados/propiedades/eventos/evento[@evento_id=3]");
		$conf_no_monitoreo_nombre = $xpath->query(" /atentus/resultados/propiedades/eventos/evento[@evento_id=7]");
		$conf_marcado_nombre = $xpath->query(" /atentus/resultados/propiedades/eventos/evento[@evento_id=9]");

		$conf_datos = $xpath->query("/atentus/resultados/detalles/detalle/estadisticas/estadistica");

		//SI NO HAY DATOS MOSTRAR MENSAJE
    	if (!$conf_datos->length) {
  			$this->resultado = $this->__generarContenedorSinDatos();
  			return;
  		}

  		$T =& new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
  		$T->setFile('tpl_grafico', 'disponibilidad_consolidada_especial.xhtml');  	
  		$uptime_nombre = $conf_uptime_nombre->item(0)->getAttribute('nombre');
  		$downtime_nombre = $conf_downtime_nombre->item(0)->getAttribute('nombre');
  		$parcial_nombre = $conf_parcial_nombre->item(0)->getAttribute('nombre');
  		$no_monitoreo_nombre = $conf_no_monitoreo_nombre->item(0)->getAttribute('nombre');
  		$marcado_nombre = $conf_marcado_nombre->item(0)->getAttribute('nombre');

  		$uptime_color = $conf_uptime_nombre->item(0)->getAttribute('color');
  		$downtime_color = $conf_downtime_nombre->item(0)->getAttribute('color');
  		$parcial_color = $conf_parcial_nombre->item(0)->getAttribute('color');
  		$no_monitoreo_color = $conf_no_monitoreo_nombre->item(0)->getAttribute('color');
  		$marcado_color = $conf_marcado_nombre->item(0)->getAttribute('color');

  		$uptime = $conf_uptime->item(0)->getAttribute('porcentaje');
  		$downtime = $conf_downtime->item(0)->getAttribute('porcentaje');
  		$parcial = $conf_parcial->item(0)->getAttribute('porcentaje');
  		$no_monitoreo = $conf_no_monitoreo->item(0)->getAttribute('porcentaje');
  		$marcado = $conf_marcado->item(0)->getAttribute('porcentaje');

  		if (($marcado  + $no_monitoreo) !=100) {
			$uptime=$uptime+$parcial;
			$factor_total=$uptime+$downtime;
			$uptime_real = ($uptime * 100) / $factor_total;
			$downtime_real = ($downtime * 100) / $factor_total;
		}else{
			$uptime_real = 0;
			$downtime_real = 0;
		}
		
		$T->setVar('__uptime_real', number_format(round($uptime_real),2),2);
		$T->setVar('__downtime_real', number_format(round($downtime_real),2),2);

  		$T->setVar('__item_orden', $this->item_orden);

  		$T->setVar('__objetivo_id', $this->objetivo_id);
  		$T->setVar('__horario_id_item', $this->extra['horario_id_item']);

  		$T->setVar('__uptime_color', $uptime_color);
  		$T->setVar('__downtime_color',$downtime_color);
  		$T->setVar('__parcial_color', $parcial_color);
  		$T->setVar('__no_monitoreo_color', $no_monitoreo_color);
  		$T->setVar('__marcado_color', $marcado_color);

  		$T->setVar('__uptime_nombre', $uptime_nombre);
  		$T->setVar('__downtime_nombre',$downtime_nombre);
  		$T->setVar('__parcial_nombre', $parcial_nombre);
  		$T->setVar('__no_monitoreo_nombre', $no_monitoreo_nombre);
  		$T->setVar('__marcado_nombre', $marcado_nombre);

  		$T->setVar('__porcentaje_uptime', number_format(round($uptime),2),2);
  		$T->setVar('__porcentaje_downtime', number_format(round($downtime),2),2);
  		$T->setVar('__porcentaje_parcial', number_format(round($parcial),2),2);
		$T->setVar('__porcentaje_no_monitoreo', number_format(round($no_monitoreo),2),2);
		$T->setVar('__porcentaje_marcado', number_format(round($marcado),2),2);

 		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'));
	}

	# Plantilla accordeon.
	function getAccordion($encode,$nameFunction){
		
		$T = & new Template_PHPLIB(REP_PATH_TEMPLATES);
		$T->setFile('tpl_contenido', 'accordion.tpl');
		$T->setVar('__data', $encode);
		$T->setVar('__name', $nameFunction);
		return $T->parse('out', 'tpl_contenido');
	}
	
	// TODO: metodo getDisponibilidadPorNodo()
	function getConsolidadoDisponibilidadMonitor($dataMant, $timeZone){
		$xpath = $this->getDatosConsolidadoDisponibilidad();
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id!=0]");
		$this->botones = $conf_nodos;
                $datos = "";

		$es_online = true;
		if (isset($this->extra["imprimir"]) and $this->extra["imprimir"] != 0) {
			$es_online = false;
			$nodos = $conf_nodos;
		}
		elseif (isset($this->extra["monitor_id"])) {
			$nodos[] = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$this->extra["monitor_id"]."]")->item(0);
		}
		else {
			$nodos[] = $conf_nodos->item(0);
		}

		if($conf_pasos->length == 0) {
			return $this->__generarContenedorSVG($this->__generarContenedorSinDatos(), $this->extra["monitor_id"], $es_online);
		}

		//	TEMPLATE DEL GRAFICO
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_consolidado.xhtml');
		
		$T->setBlock('tpl_grafico', 'BLOQUE_RESOURCES', 'bloque_resources');
		$T->setBlock('tpl_grafico', 'BLOQUE_GRUPOS', 'bloque_grupos');
		$T->setBlock('tpl_grafico', 'BLOQUE_MONITORES', 'bloque_monitores');
		if ($_REQUEST['multi_obj'] < 1) {
			$pdf = 0;
		}else{
			$pdf = 1;
		}
		$T->setVar('__pdf', $pdf);

		$T->setVar('bloque_resources', '');
		$T->setVar('bloque_grupos', '');

		$T->setVar('__item_orden', $this->extra["item_orden"]);
		$T->setVar('__link_eventos', ($this->extra["imprimir"])?'f':'t');

		// DATOS DEL MONITOR
                 //permite que no se pise el grafico anterior
                if (isset($this->extra["monitor"])){
                    $mostrar_graficos = $this->extra["monitor"];  
                }
		$linea = 2;
		foreach ($nodos as $conf_nodo) {
			$monitor_elegido = $conf_nodo->getAttribute('nodo_id');
                         //condicion utilizada para envio pdf por email
                        if (isset($this->extra["monitor"]) and $this->extra["monitor"] != null) {
                            $mostrar_graficos++;
                            $T->setVar('__monitor_id', $mostrar_graficos);
                        } else {
                            $T->setVar('__monitor_id', $conf_nodo->getAttribute('nodo_id'));
                        }
			$T->setVar('__monitor_nombre', $conf_nodo->getAttribute('nombre'));
			$T->setVar('__monitor_orden', $linea);
			$T->setVar('__tiene_titulo', ($es_online)?"none":"inline");
			$T->setVar('__tipo_id', 'consolidada_monitor_'+$conf_nodo->getAttribute('nodo_id'));

			$T->setVar('bloque_resources', '');
			$T->setVar('bloque_grupos', '');

			foreach ($conf_pasos as $conf_paso) {
				$T->setVar('__resource_id', $conf_objetivo->getAttribute('objetivo_id')."-".$conf_nodo->getAttribute('nodo_id')."-".$conf_paso->getAttribute('paso_orden'));
				$T->setVar('__resource_name', $conf_paso->getAttribute('nombre'));
				$T->setVar('__resource_parent', $conf_objetivo->getAttribute('objetivo_id')."-".$conf_nodo->getAttribute('nodo_id'));
				$T->parse('bloque_resources', 'BLOQUE_RESOURCES', true);
				$T->setVar('__grupo_id', $conf_objetivo->getAttribute('objetivo_id')."-".$conf_nodo->getAttribute('nodo_id')."-".$conf_paso->getAttribute('paso_orden'));
				$T->parse('bloque_grupos', 'BLOQUE_GRUPOS', true);
                                
				$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=".$conf_nodo->getAttribute('nodo_id')."]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato");
				$datos = $datos."'".$conf_objetivo->getAttribute('objetivo_id')."-".$conf_nodo->getAttribute('nodo_id')."-".$conf_paso->getAttribute('paso_orden')."':[";
				if ($tag_datos->length > 0) {
					foreach ($tag_datos as $tag_dato) {
						$isEventSpecial = false;
						$id = null;
						$name = null;
						$title = null;
						# Agrega los eventos creados por el usuarios.
						if (!is_null($dataMant)){
							foreach ( $dataMant as $key => $value) {
								$dateStart = strtotime(Utiles::convertDateTimeZone($value['fecha_inicio'], $timeZone));
								$dateEnd = strtotime(Utiles::convertDateTimeZone($value['fecha_termino'], $timeZone));
								
								if ((strtotime($tag_dato->getAttribute('inicio')) == $dateStart) && (strtotime($tag_dato->getAttribute('termino')) == $dateEnd)){
									$isEventSpecial = true;
									$id = $value['id'];
									$name = $value['nombre'];
									$title = $value['titulo'];
									break;								
								}				
							}
						}
						$nameEvent = ($isEventSpecial == true)? Utiles::getStyleDisponibilidad(9): Utiles::getStyleDisponibilidad($tag_dato->getAttribute('evento_id'));
						
						$datos = $datos."['".date('Y.m.d.H.i.s', strtotime($tag_dato->getAttribute('inicio')))."','".
								 date('Y.m.d.H.i.s', strtotime($tag_dato->getAttribute('termino')))."','".$nameEvent."', '".$title."', '".$name."', '".$id."', '".date('Y.m.d.H.i.s', strtotime($tag_dato->getAttribute('inicio')))."'],";
					}
				}
				else {
					$datos = $datos."['".$this->timestamp->getInicioPeriodo("Y.m.d.H.i.s")."','".
							$this->timestamp->getTerminoPeriodo("Y.m.d.H.i.s")."','".
							Utiles::getStyleDisponibilidad(7)."'],";
				}
				$datos = $datos."],";
			}
			
			
			
			$T->parse('bloque_monitores', 'BLOQUE_MONITORES', true);
			$linea++;
		}
		$T->setVar('__datos', $datos);
		$T->setVar('__horario_habil', '');
		$horario_habil = "";
		foreach ($xpath->query("/atentus/resultados/propiedades/horarios_habiles/horario_habil") as $conf_horario) {
			$horario_habil.= ($horario_habil ? ",":"").
							 "[\"".date("Y.m.d.H.i.s", strtotime($conf_horario->getAttribute("inicio")))."\",".
							 "\"".date("Y.m.d.H.i.s", strtotime($conf_horario->getAttribute("termino")))."\"]";
		}

	
		$T->setVar('__horario_habil', $horario_habil);
		return $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), $monitor_elegido, $es_online);
	}	
	
	// TODO: metodo getDisponibilidadConsolidadoSimple()
	function getConsolidadoDisponibilidadSimple() {
		$this->horario_id = 0;
		$xpath = $this->getDatosConsolidadoDisponibilidad();
		
		if ($xpath == null) {
			return $this->__generarContenedorSinDatos();
		}
		
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id!=0]");

		//	TEMPLATE DEL GRAFICO
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_simple.xhtml');
		$T->setBlock('tpl_grafico', 'BLOQUE_RESOURCES', 'bloque_resources');
		$T->setBlock('tpl_grafico', 'BLOQUE_GRUPOS', 'bloque_grupos');
		$T->setVar('bloque_resources', '');
		$T->setVar('bloque_grupos', '');

		$T->setVar('__objetivo_id', $conf_objetivo->getAttribute('objetivo_id'));
		$T->setVar('__resource_id', $conf_objetivo->getAttribute('objetivo_id'));
		$T->setVar('__resource_name', $conf_objetivo->getAttribute('nombre'));
		$T->setVar('__resource_parent', '');
		$T->parse('bloque_resources', 'BLOQUE_RESOURCES', true);

		$datos = "'".$conf_objetivo->getAttribute('objetivo_id')."':[";
		foreach ($xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$conf_objetivo->getAttribute('objetivo_id')."]/datos/dato") as $tag_dato) {
			$datos = $datos."['".date('Y.m.d.H.i.s', strtotime($tag_dato->getAttribute('inicio')))."','".
					 date('Y.m.d.H.i.s', strtotime($tag_dato->getAttribute('termino')))."','".
					 Utiles::getStyleDisponibilidad($tag_dato->getAttribute('evento_id'))."'],";
		}
		$datos =$datos."],";

		$T->setVar('__datos', $datos);

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'));
		return $this->resultado;
	}
	/*
	Creado por:
	Modificado por: Carlos Sepúlveda
	Fecha de creacion:14-11-2016
	Fecha de ultima modificacion:16-06-2017
	*/
	// TODO: metodo getDisponibilidadResumen()
	function getDetalladoDisponibilidad($es_descarga) {
		global $current_usuario_id;
		global $usr;
		
		$event = new Event;

		# Variables para eventos especiales marcados por el cliente codigo 9.
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		$nameFunction =  'detallado_disponibilidad';
		$tieneEvento = 'false';
		$dataMant = null;
		$marcado = false;
		$encode = null;
		$ids= null;

		$xpath = $this->getDatosDetalladoDisponibilidad();
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		if(isset($_REQUEST['word'])) {
			$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=0]");
		}else{
			$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo");
		}
		$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$this->botones = $conf_nodos;

		if($conf_pasos->length == 0) {
			$this->resultado = $this->__generarContenedorSVG($this->__generarContenedorSinDatos());
			return;
		}

		# Busca marcados dentro del xml.

		foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
		}	
		
		# Verifica y asigna variables en caso de que exista marcado.
		if ($marcado == true) {

			$dataMant =$event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			$tieneEvento = 'true';
			$encode = json_encode($dataMant);
		}

		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		if(isset($_REQUEST['word'])){
			$T->setFile('tpl_grafico','disponibilidad_resumen_especial.xhtml');
		}else{
			$T->setFile('tpl_grafico','disponibilidad_resumen.xhtml');
			$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
			$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		}
		$T->setBlock('tpl_grafico', 'ANCHO_SERIE', 'ancho_serie');
		$T->setBlock('tpl_grafico', 'SERIES_NAME', 'series_name');
		$T->setBlock('tpl_grafico', 'POINT_SIN_NOM', 'point_sin_nom');
		$T->setBlock('tpl_grafico', 'SERIES_SIN_NOM', 'series_sin_nom');
		
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
		$T->setBlock('tpl_grafico', 'BLOQUE_MONITORES', 'bloque_monitores');
		
		// TODO: como imprimir de quedar igual a 1 o true
		$es_online = true;
		$T->setVar('__tiene_evento', $tieneEvento);
		if (isset($this->extra["imprimir"]) and $this->extra["imprimir"] != 0) {
			$es_online = false;
		}
		elseif (isset($this->extra["monitor_id"])) {
			$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$this->extra["monitor_id"]."]");
		}
		else {
			$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=0]");
		}

		$T->setVar('__item_orden', $this->extra["item_orden"]);

		$orden = 1;

        # Cuando es descarga pdf.
        if($es_descarga==true){
            $T->setVar('es_descarga', 'true');
        }
		foreach ($conf_nodos as $conf_nodo) {
			$monitor_elegido = $conf_nodo->getAttribute("nodo_id");
			$tag_nodo = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=".$conf_nodo->getAttribute("nodo_id")."]")->item(0);

			$T->setVar('__monitor_id', $conf_nodo->getAttribute("nodo_id"));
			$T->setVar('__monitor_nombre', $conf_nodo->getAttribute("nombre"));
			$T->setVar('__monitor_orden', $orden);
			$T->setVar('__tiene_titulo', ($es_online)?"none":"inline");
			$T->setVar('__pointWidth', ($conf_pasos->length < 4)?'pointWidth: 70':'');
			$T->setVar('__label', ($conf_pasos->length < 7)?'this.value':'(parseInt(this.axis.categories.indexOf(this.value))+1)');
			$T->setVar('__leyenda_label', (($conf_pasos->length < 7)?"":"leyendaLabel(chart_disponibilidad_detallada_".$monitor_elegido.",'leyenda_disponibilidad_detallada1_".$monitor_elegido."');"));
			$T->setVar('__step', ($conf_pasos->length < 7)?1:round($conf_pasos->length/7));
			$T->setVar('__ancho_leyenda1',"480");
			$T->setVar('__ancho_leyenda2',"120");

			$T->setVar('series_name', '');

			foreach ($conf_pasos as $conf_paso) {
				$T->setVar('__series_name', $conf_paso->getAttribute("nombre"));
				$T->parse('series_name', 'SERIES_NAME', true);
			}

			$T->setVar('series_element', '');
			$T->setVar('series_sin_nom', '');
			for ($i = $conf_eventos->length; $i > 0; $i--) {
				# Codigo agregado para el correcto orden y visualización de los colores en los graficos.
				# De esta manera el evento no monitoreo sera el ultimo valor agregado al grafico.
				$evento_id_xml = null;
				$evento_id_xml = $conf_eventos->item($i - 1)->getAttribute("evento_id");
				if ($evento_id_xml=='9'){
					$evento_id = '7';
					$conf_evento = $conf_eventos->item(3);
				}
				elseif($evento_id_xml=='7'){
					$evento_id = '9';
					$conf_evento = $conf_eventos->item(4);
				}
				else{
					$evento_id = $evento_id_xml;
					$conf_evento = $conf_eventos->item($i - 1);
				}
				if($marcado == false and $evento_id == '9'){
					continue;
				}
				else{
					$T->setVar('__point_name', htmlspecialchars($conf_evento->getAttribute("nombre")));
					$T->setVar('__serie_color', $conf_evento->getAttribute("color"));
				}

				$T->setVar('point_element', '');
				$T->setVar('point_sin_nom', '');
				foreach ($conf_pasos as $conf_paso) {
					$dato_sinmon = $xpath->query("detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=7]", $tag_nodo);
					$dato_sinmar = $xpath->query("detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=9]", $tag_nodo);
					# Porcentaje sin marcados evento_id 9.
					$porcentaje_sinmar = ($dato_sinmar->length == 0)?0:$dato_sinmar->item(0)->getAttribute("porcentaje");
					$porcentaje_sinmon = ($dato_sinmon->length == 0)?0:$dato_sinmon->item(0)->getAttribute("porcentaje");
					$dato = $xpath->query("detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=".$evento_id."]", $tag_nodo);

					$porcentaje = ($dato->length == 0)?0:$dato->item(0)->getAttribute("porcentaje");
					$T->setVar('__point_value', $porcentaje);
					# Excluye sin monitoreos y datos marcados clientes id = 9.
					$porcentaje_excluir = $porcentaje_sinmon + $porcentaje_sinmar;
					$T->setVar('__point_value_sin_mon', ($porcentaje_excluir==100)?0:(($porcentaje / (100-$porcentaje_excluir)) * 100));
					if(!isset($_REQUEST['word'])) {
						$T->parse('point_element', 'POINT_ELEMENT', true);
					}
					$T->parse('point_sin_nom', 'POINT_SIN_NOM', true);
				}
				if(!isset($_REQUEST['word'])) {
					$T->parse('series_element', 'SERIES_ELEMENT', true);
				}
				if (($conf_evento->getAttribute("evento_id") != 7 ) or ($conf_evento->getAttribute("evento_id") != 9 )) {
					$T->parse('series_sin_nom', 'SERIES_SIN_NOM', true);
				}
			}

			// DISPONIBILIDAD : SLA DEL GRAFICO
			$sla_ok = 'false';
			$sla_error = 'false';
			$T->setVar('tiene_sla_ok', '');
			if ($conf_objetivo->getAttribute("sla_disponibilidad_ok") != null and $conf_objetivo->getAttribute("sla_disponibilidad_ok") != "") {
				$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute("sla_disponibilidad_ok"));
				$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', true);
				$sla_ok = 'true';
			}

			$T->setVar('tiene_sla_error', '');
			if ($conf_objetivo->getAttribute("sla_disponibilidad_error") != null and $conf_objetivo->getAttribute("sla_disponibilidad_error") != "") {
				$T->setVar('__sla_error_value', $conf_objetivo->getAttribute("sla_disponibilidad_error"));
				$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', true);
				$sla_error = 'true';
			}

			$T->setVar('__muestra_sla_ok', $sla_ok);
			$T->setVar('__muestra_sla_error', $sla_error);
			$T->setVar('__name', $nameFunction);

			$T->parse('bloque_monitores', 'BLOQUE_MONITORES', true);
			$orden++;
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), $monitor_elegido, $es_online);
		# Verifica si lleva accordeon.
		if (count($dataMant)>0){
			$this->resultado.= $this->getAccordion($encode, $nameFunction);
		}
	}

	function getDetalladoDisponibilidadEspecial($es_descarga) {
	    $xpath = $this->getDatosDetalladoDisponibilidad();
	    
	    $conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
	    $conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo");
	    $conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento[@evento_id!=9]");
	    $conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
	    
	    if($conf_pasos->length == 0) {
	        $this->resultado = $this->__generarContenedorSVG($this->__generarContenedorSinDatos());
	        return;
	    }
	   
	    /* TEMPLATE DEL GRAFICO */
	    $T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
	    $T->setFile('tpl_grafico','disponibilidad_especial.xhtml');
	    
	    $T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
	    $T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
	    
	    $T->setBlock('tpl_grafico', 'BLOQUE_EVENTOS', 'bloque_eventos');
	    $T->setBlock('tpl_grafico', 'BLOQUE_PASOS', 'bloque_pasos');
	    $T->setBlock('tpl_grafico', 'BLOQUE_EVENTOS_TITULOS', 'eventos_titulos');
	    $T->setBlock('tpl_grafico', 'ANCHO_SERIE', 'ancho_serie');
	    $T->setBlock('tpl_grafico', 'SERIES_NAME', 'series_name');
	    $T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
	    $T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
	    $T->setBlock('tpl_grafico', 'BLOQUE_TABLA', 'bloque_tabla');
	    $T->setBlock('tpl_grafico', 'BLOQUE_GRAFICOS', 'bloque_graficos');
	    $T->setBlock('tpl_grafico', 'BLOQUE_MONITORES', 'bloque_monitores');
	    
	    // TODO: como imprimir de quedar igual a 1 o true
	    $es_online = true;
	    if (isset($this->extra["imprimir"]) and $this->extra["imprimir"] != 0) {
	        $es_online = false;
	    }
	    elseif (isset($this->extra["monitor_id"])) {
	        $conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$this->extra["monitor_id"]."]");
	    }
	    else {
	        $conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=0]");
	    }
	    
	    $T->setVar('__item_orden', $this->extra["item_orden"]);
	    
	    $orden = 1;
	    /*cuando es descarga pdf*/
	    if($es_descarga==true){
	        $T->setVar('es_descarga', 'true');
	    }
	    $graficos =0;
	    
	    foreach ($conf_nodos as $conf_nodo) {
	        $monitor_elegido = $conf_nodo->getAttribute("nodo_id");
	        $tag_nodo = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=".$conf_nodo->getAttribute("nodo_id")."]")->item(0);
	        
	        
	        $T->setVar('__monitor_id', $conf_nodo->getAttribute("nodo_id"));
	        $T->setVar('__monitor_nombre', $conf_nodo->getAttribute("nombre"));
	        $T->setVar('__monitor_orden', $orden);
	        $T->setVar('__tiene_titulo', ($es_online)?"none":"inline");
	        $T->setVar('__pointWidth', ($conf_pasos->length < 4)?'pointWidth: 70':'');
	        $T->setVar('__label', ($conf_pasos->length < 7)?'this.value':'(parseInt(this.axis.categories.indexOf(this.value))+1)');
	        $T->setVar('__leyenda_label', (($conf_pasos->length < 7)?"":"leyendaLabel(chart_disponibilidad_detallada_".$monitor_elegido.",'leyenda_disponibilidad_detallada1_".$monitor_elegido."');"));
	        $T->setVar('__step', ($conf_pasos->length < 7)?1:round($conf_pasos->length/7));
	        $T->setVar('__ancho_leyenda1',"480");
	        $T->setVar('__ancho_leyenda2',"120");
	        
	        if($monitor_elegido==0){
	            $graficos= 3;
	        }else{
	            $graficos= 2;
	        }
	       
	        $T->setVar('bloque_graficos', '');
	        for ($i=1;$i<=$graficos;$i++){
	            $linea=0;
	            switch ($i){
	                case 1:
	                    $text="disponibilidad_detallada_real_{__monitor_id}";
	                    $T->setVar('var__monitor_id', 'chart_disponibilidad_detallada_'.$monitor_elegido);
	                    $T->setVar('__render', 'disponibilidad_detallada_'.$monitor_elegido);
	                    $T->setVar('__content', 'disponibilidad_detallada_'.$monitor_elegido);
	                    $T->setVar('__text', 'Disponibilidad');
	                    
	                    break;
	                case 2:
	                    $T->setVar('var__monitor_id', 'chart_disponibilidad_detallada_real_'.$monitor_elegido);
	                    $T->setVar('__render', 'disponibilidad_detallada_real_'.$monitor_elegido);
	                    $T->setVar('__content', 'disponibilidad_detallada_real_'.$monitor_elegido);
	                    $T->setVar('__text', 'Disponibilidad Real');
	                    break;
	                case 3:
	                    $T->setVar('var__monitor_id', 'chart_disponibilidad_detallada_especial'.$monitor_elegido);
	                    $T->setVar('__render', 'disponibilidad_detallada_especial_'.$monitor_elegido);
	                    $T->setVar('__content', 'disponibilidad_detallada_especial_'.$monitor_elegido);
	                    $T->setVar('__text', 'Disponibilidad Especial');
	                    break;
	            }
	            
	            
	            /** AQUI SE GENERA LA TABLA CON EL RESUMEN DE LOS DATOS**/
	            $T->setVar('bloque_tabla', '');
	            $T->setVar('series_name', '');
	            $T->setVar('bloque_pasos', '');
	            foreach ($conf_pasos as $conf_paso) {
	                $T->setVar('__series_name', $conf_paso->getAttribute("nombre"));
	                $T->setVar('__paso_nombre', $conf_paso->getAttribute("nombre"));
	                $T->setVar('__print_class', ($linea % 2 == 0)?"celdaIteracion2":"celdaIteracion1");
	                
	                
	                $T->setVar('eventos_titulos', '');
	                $T->setVar('bloque_eventos', '');
	                foreach ($conf_eventos as $conf_evento){
	                    $dato = $xpath->query("detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]", $tag_nodo);
	                    $dato_snmon = $xpath->query("detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=7]", $tag_nodo);
	                    $dato_parcial = $xpath->query("detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=3]", $tag_nodo);
	                    
	                    $porcentaje = ($dato->length == 0)?0:$dato->item(0)->getAttribute("porcentaje");
	                    $porcentaje_sinmon =($dato_snmon->length == 0)?0:$dato_snmon->item(0)->getAttribute("porcentaje");
	                    $porcentaje_parcial =($dato_parcial->length == 0)?0:$dato_parcial->item(0)->getAttribute("porcentaje");
	                    if($i==1){
	                        $T->setVar('__evento_valor', number_format($porcentaje, 2, ',', ''));
	                        $T->setVar('__evento_color', $conf_evento->getAttribute("color"));
	                        
	                        $T->setVar('__evento_nombre', $conf_evento->getAttribute("nombre"));
	                        $T->parse('eventos_titulos', 'BLOQUE_EVENTOS_TITULOS', true);
	                        $T->parse('bloque_eventos', 'BLOQUE_EVENTOS', true);
	                    }elseif ($i==2 && $conf_evento->getAttribute("evento_id")!=7){
	                        
	                        $T->setVar('__evento_valor', number_format(($porcentaje_sinmon==100)?0:(($porcentaje / (100-$porcentaje_sinmon)) * 100), 2, ',', ''));
	                        $T->setVar('__evento_color', $conf_evento->getAttribute("color"));
	                        $T->setVar('__evento_nombre', $conf_evento->getAttribute("nombre"));
	                        $T->parse('eventos_titulos', 'BLOQUE_EVENTOS_TITULOS', true);
	                        $T->parse('bloque_eventos', 'BLOQUE_EVENTOS', true);
	                        
	                    }elseif ($i==3){
	                        if($conf_evento->getAttribute("evento_id")!=7 && $conf_evento->getAttribute("evento_id")!=3){
	                            
	                            $porcentaje_dparcial = ($porcentaje_parcial>0 && $conf_evento->getAttribute("evento_id")==1)?((($porcentaje+$porcentaje_parcial)/ (100-$porcentaje_sinmon)) * 100):(($porcentaje / (100-$porcentaje_sinmon)) * 100);
	                            $T->setVar('__evento_valor', number_format($porcentaje_dparcial, 2, ',', ''));
	                            $T->setVar('__evento_color', $conf_evento->getAttribute("color"));
	                            $T->setVar('__evento_nombre', $conf_evento->getAttribute("nombre"));
	                            $T->parse('eventos_titulos', 'BLOQUE_EVENTOS_TITULOS', true);
	                            $T->parse('bloque_eventos', 'BLOQUE_EVENTOS', true);
	                        }
	                    }
	                }
	                $linea++;
	                $T->parse('series_name', 'SERIES_NAME', true);
	                $T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
	            }
	            
	            
	            
	            $T->setVar('series_element', '');
	            for ($e = $conf_eventos->length; $e > 0; $e--) {
	                $conf_evento = $conf_eventos->item($e - 1);
	                $T->setVar('__point_name', htmlspecialchars($conf_evento->getAttribute("nombre")));
	                $T->setVar('__serie_color', $conf_evento->getAttribute("color"));
	                
	                $T->setVar('point_element', '');
	                foreach ($conf_pasos as $conf_paso) {
	                    
	                    $dato = $xpath->query("detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]", $tag_nodo);
	                    $dato_sinmon = $xpath->query("detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=7]", $tag_nodo);
	                    $dato_parcial = $xpath->query("detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=3]", $tag_nodo);
	                    
	                    $porcentaje = ($dato->length == 0)?0:$dato->item(0)->getAttribute("porcentaje");
	                    $porcentaje_sinmon =($dato_snmon->length == 0)?0:$dato_snmon->item(0)->getAttribute("porcentaje");
	                    $porcentaje_parcial =($dato_parcial->length == 0)?0:$dato_parcial->item(0)->getAttribute("porcentaje");
	                    
	                    if($i==1){
	                        $T->setVar('__point_value', $porcentaje);
	                        $T->parse('point_element', 'POINT_ELEMENT', true);
	                    }elseif ($i==2 && $conf_evento->getAttribute("evento_id")!=7){
	                        $T->setVar('__point_value', ($porcentaje_sinmon==100)?0:(($porcentaje / (100-$porcentaje_sinmon)) * 100));
	                        $T->parse('point_element', 'POINT_ELEMENT', true);
	                        
	                    }elseif ($i==3){
	                        if($conf_evento->getAttribute("evento_id")!=7 && $conf_evento->getAttribute("evento_id")!=3){
	                            $porcentaje_dparcial = ($porcentaje_parcial>0 && $conf_evento->getAttribute("evento_id")==1)?((($porcentaje+$porcentaje_parcial)/ (100-$porcentaje_sinmon)) * 100):(($porcentaje / (100-$porcentaje_sinmon)) * 100);
	                            $T->setVar('__point_value', $porcentaje_dparcial);
	                            $T->parse('point_element', 'POINT_ELEMENT', true);
	                        }
	                    }
	                }
	                $T->parse('series_element', 'SERIES_ELEMENT', true);
	            }
	            
	  
	            
	            // DISPONIBILIDAD : SLA DEL GRAFICO
	            $sla_ok = 'false';
	            $sla_error = 'false';
	            $T->setVar('tiene_sla_ok', '');
	            if ($conf_objetivo->getAttribute("sla_disponibilidad_ok") != null and $conf_objetivo->getAttribute("sla_disponibilidad_ok") != "") {
	                $T->setVar('__sla_ok_value', $conf_objetivo->getAttribute("sla_disponibilidad_ok"));
	                $T->parse('tiene_sla_ok', 'TIENE_SLA_OK', true);
	                $sla_ok = 'true';
	            }
	            
	            $T->setVar('tiene_sla_error', '');
	            if ($conf_objetivo->getAttribute("sla_disponibilidad_error") != null and $conf_objetivo->getAttribute("sla_disponibilidad_error") != "") {
	                $T->setVar('__sla_error_value', $conf_objetivo->getAttribute("sla_disponibilidad_error"));
	                $T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', true);
	                $sla_error = 'true';
	            }
	            
	            $T->setVar('__muestra_sla_ok', $sla_ok);
	            $T->setVar('__muestra_sla_error', $sla_error);
	            
	            if($monitor_elegido==0){
	                $T->parse('bloque_tabla', 'BLOQUE_TABLA', true);
	            }
	            
	            $T->parse('bloque_graficos', 'BLOQUE_GRAFICOS', true);
	        }
	        
	        $T->parse('bloque_monitores', 'BLOQUE_MONITORES', true);
	        $orden++;
	    }
	    $this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
	    $this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), $monitor_elegido, $es_online);
	}

	/**
	 * Funcion para obtener el grafico de
	 * Disponibilidad Detallada.
	 *
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
	// TODO: getDisponibilidadResumenFlexible()
	function getDetalladoDisponibilidadFlexible() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$this->extra["variable"] = 'true';

		//$flexible = ;

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."', ".
				(isset($this->extra["variable"])?$usr->cliente_id:'0').")";
//		print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row["disponibilidad_resumen_consolidado"]);
		$xpath = new DOMXpath($dom);

		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo");
		$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);

		if(!$conf_pasos->length){
			$this->resultado = $this->__generarContenedorSVG($this->__generarContenedorSinDatos());
			return;
		}

		$this->botones = $conf_nodos;

		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_resumen_flexible.xhtml');

		$T->setBlock('tpl_grafico', 'SERIES_NAME', 'series_name');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
		$T->setBlock('tpl_grafico', 'BLOQUE_MONITORES', 'bloque_monitores');

		// TODO: como imprimir de quedar igual a 1 o true
		$es_online = true;
		if (isset($this->extra["imprimir"]) and $this->extra["imprimir"] != 0) {
			$es_online = false;
		}
		elseif (isset($this->extra["monitor_id"])) {
			$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$this->extra["monitor_id"]."]");
		}
		else {
			$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=0]");
		}

		$T->setVar('__item_orden', $this->extra["item_orden"]);

		$orden = 1;
		foreach ($conf_nodos as $conf_nodo) {
			$monitor_elegido = $conf_nodo->getAttribute("nodo_id");
			$tag_nodo = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=".$conf_nodo->getAttribute("nodo_id")."]")->item(0);

			$T->setVar('__monitor_id', $conf_nodo->getAttribute("nodo_id"));
			$T->setVar('__monitor_nombre', $conf_nodo->getAttribute("nombre"));
			$T->setVar('__monitor_orden', $orden);
			$T->setVar('__tiene_titulo', ($es_online)?"none":"inline");
			$T->setVar('__pointWidth', ($conf_pasos->length < 4)?'pointWidth: 70':'');
			$T->setVar('__label', ($conf_pasos->length < 7)?'this.value':'(parseInt(this.axis.categories.indexOf(this.value))+1)');
			$T->setVar('__leyenda_label', (($conf_pasos->length < 7)?"":"leyendaLabel(chart_disponibilidad_detallada_flexible_".$monitor_elegido.",'leyenda_disponibilidad_detallada1_flexible_".$monitor_elegido."');"));
			$T->setVar('__step', ($conf_pasos->length < 7)?1:round($conf_pasos->length/7));
			$T->setVar('__ancho_leyenda1',"480");
			$T->setVar('__ancho_leyenda2',"120");

			$T->setVar('series_name', '');

			foreach ($conf_pasos as $conf_paso) {
				$T->setVar('__series_name', $conf_paso->getAttribute("nombre"));
				$T->parse('series_name', 'SERIES_NAME', true);
			}

			$T->setVar('series_element', '');
			for ($i = $conf_eventos->length; $i > 0; $i--) {
				$conf_evento = $conf_eventos->item($i - 1);
				$T->setVar('__point_name', htmlspecialchars($conf_evento->getAttribute("nombre")));
				$T->setVar('__serie_color', $conf_evento->getAttribute("color"));
				$T->setVar('point_element', '');
				foreach ($conf_pasos as $conf_paso) {
					$dato = $xpath->query("detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]", $tag_nodo);
					$porcentaje = ($dato->length == 0)?"0":$dato->item(0)->getAttribute("porcentaje");
					$T->setVar('__point_value', $porcentaje);
					$T->parse('point_element', 'POINT_ELEMENT', true);
				}
				$T->parse('series_element', 'SERIES_ELEMENT', true);
			}

			// DISPONIBILIDAD : SLA DEL GRAFICO
			$sla_ok = 'false';
			$sla_error = 'false';
			$T->setVar('tiene_sla_ok', '');
			if ($conf_objetivo->getAttribute("sla_disponibilidad_ok") != null and $conf_objetivo->getAttribute("sla_disponibilidad_ok") != "") {
				$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute("sla_disponibilidad_ok"));
				$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', true);
				$sla_ok = 'true';
			}

			$T->setVar('tiene_sla_error', '');
			if ($conf_objetivo->getAttribute("sla_disponibilidad_error") != null and $conf_objetivo->getAttribute("sla_disponibilidad_error") != "") {
				$T->setVar('__sla_error_value', $conf_objetivo->getAttribute("sla_disponibilidad_error"));
				$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', true);
				$sla_error = 'true';
			}

			$T->setVar('__muestra_sla_ok', $sla_ok);
			$T->setVar('__muestra_sla_error', $sla_error);

			$T->parse('bloque_monitores', 'BLOQUE_MONITORES', true);
			$orden++;
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), $monitor_elegido, $es_online);
	}

	/**
	 * Funcion para obtener el grafico de
	 * Distribucion Porcentual de Downtime y Errores.
	 * Este grafico contiene 3 graficos dentro por cada paso,
	 * que se cambian en forma dinamica al seleccionar el paso deseado.
	 *
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
	// TODO: metodo getDisponibilidadErrores()
	function getErroresDisponibilidad($es_descarga) {
		global $usr;

		# Variables para eventos especiales marcados por el cliente codigo 9.
		$event = new Event;		
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		$nameFunction =  'datos_errores_disponibilidad';
		$tieneEvento = 'false';
		$dataMant = null;
		$marcado = false;
		$encode = null;
		$ids= null;

		$xpath = $this->getDatosErroresDisponibilidad();
		$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_codigos = $xpath->query("/atentus/resultados/propiedades/codigos/codigo");
		$this->botones = $conf_pasos;
		
		if ($xpath->query("//detalles/detalle[@paso_orden]")->length == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_errores.xhtml');
		$T->setBlock('tpl_grafico', 'MOSTRAR_SCROLL', 'mostrar_scroll');
		$T->setBlock('tpl_grafico', 'SERIES_NAME', 'series_name');
		$T->setBlock('tpl_grafico', 'DISPONIBILIDAD_POINT_ELEMENT', 'disponibilidad_point_element');
		$T->setBlock('tpl_grafico', 'DOWNTIME_POINT_ELEMENT', 'downtime_point_element');
		$T->setBlock('tpl_grafico', 'ERRORES_POINT_ELEMENT', 'errores_point_element');
		$T->setBlock('tpl_grafico', 'ERRORES_SERIES_ELEMENT', 'errores_series_element');
		$T->setBlock('tpl_grafico', 'BLOQUE_PASOS', 'bloque_pasos');

		$es_online = true;
		# Busca marcados dentro del xml.
		foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
		}	
		
		# Verifica y asigna variables en caso de que exista marcado.
		if ($marcado == true) {

			$dataMant =$event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			$tieneEvento = 'true';
			$encode = json_encode($dataMant);
			$T->setVar('__tiene_evento', $tieneEvento);
			$T->setVar('__name', $nameFunction);
		}
		if (isset($this->extra["imprimir"]) and $this->extra["imprimir"] != 0) {
			$pasos = $conf_pasos;
			$es_online = false;
		}
		elseif (isset($this->extra["paso_id"])) {
			$pasos[] = $xpath->query("paso[@paso_orden=".$this->extra["paso_id"]."]", $conf_objetivo)->item(0);
		}
		else {
			$pasos[] = $conf_pasos->item(0);
		}

		$T->setVar('__item_orden', $this->extra["item_orden"]);

		$orden = 1;
        /*cuando es descarga pdf*/
        if($es_descarga==true){
            $T->setVar('es_descarga', 'true');
        }
             
		foreach ($pasos as $conf_paso) {
			$paso_elegido = $conf_paso->getAttribute('paso_orden');
			$tag_datos_global = $xpath->query("//detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/estadisticas/estadistica");
			$tag_datos_nodo = $xpath->query("//detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/detalles/detalle");
				
			$T->setVar('__paso_id', $conf_paso->getAttribute('paso_orden'));
			$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
			$T->setVar('__paso_orden', $orden);
			$T->setVar('__tiene_titulo', ($es_online)?"none":"inline");
			$T->setVar('__pointWidth', ($tag_datos_nodo->length<4)?'pointWidth: 70,':'');
			$T->setVar('__step', round($tag_datos_nodo->length/7));
			
			if($tag_datos_nodo->length < 7){
				$T->setVar('__label',"'".'<div style="width: 90px; overflow: hidden; text-overflow: ellipsis; float: left; white-space: nowrap">'."'+ this.value +'".' </div>'."'");
				$T->setVar('__leyenda_label',"");
				$T->setVar('__ancho_leyenda1',"0");
				$T->setVar('__ancho_leyenda2',"600");
			}
			else{
				$T->setVar('__label', 'parseInt(this.axis.categories.indexOf(this.value))+1');
				$T->setVar('__leyenda_label',"leyendaLabel(chat_error_distribucion_isp, 'leyenda_error_distribucion_isp');");
				$T->setVar('__ancho_leyenda1',"450");
				$T->setVar('__ancho_leyenda2',"150");
			}

			/* DISPONIBILIDAD : DATOS DEL GRAFICO */
			$T->setVar('disponibilidad_point_element', '');
			foreach ($tag_datos_global as $tag_dato) {
				$conf_evento = $xpath->query("//eventos/evento[@evento_id=".$tag_dato->getAttribute('evento_id')."]")->item(0);
				$T->setVar('__disponibilidad_point_name', $conf_evento->getAttribute('nombre'));
				$T->setVar('__disponibilidad_point_color', $conf_evento->getAttribute('color'));
				$T->setVar('__disponibilidad_point_value', $tag_dato->getAttribute('porcentaje'));
				$T->parse('disponibilidad_point_element', 'DISPONIBILIDAD_POINT_ELEMENT', true);
			}
	
			/* DOWNTIME : DATOS DEL GRAFICO */
			$T->setVar('downtime_point_element', '');
			$T->setVar('series_name', '');
			$T->setVar('errores_series_element', '');
			if ($tag_datos_nodo->length > 0) {
				foreach ($tag_datos_nodo as $id => $tag_dato) {
					$conf_nodo = $xpath->query("//nodos/nodo[@nodo_id=".$tag_dato->getAttribute('nodo_id')."]")->item(0);
					$T->setVar('__downtime_point_name', $conf_nodo->getAttribute('nombre'));
					$T->setVar('__downtime_point_color', Utiles::getDefaultColor($id));
					$T->setVar('__downtime_point_value', $tag_dato->getAttribute('porcentaje'));
					$T->parse('downtime_point_element', 'DOWNTIME_POINT_ELEMENT', true);
					
					$T->setVar('__errores_series_name', $conf_nodo->getAttribute('nombre'));
					$T->setVar('__series_name',$conf_nodo->getAttribute('nombre'));
					$T->parse('series_name', 'SERIES_NAME', true);
				}
				
				foreach ($conf_codigos as $id => $conf_codigo) {
					$T->setVar('__errores_point_name', $conf_codigo->getAttribute('nombre'));
					$T->setVar('__errores_point_color', Utiles::getDefaultColor($id));

					$T->setVar('errores_point_element', '');
					foreach ($tag_datos_nodo as $tag_dato_nodo) {
                                                $sumPorcentaje=0;
                                                $tag_dato = $xpath->query("estadisticas/estadistica[@codigo_id=".$conf_codigo->getAttribute('codigo_id')."]", $tag_dato_nodo);
                                                foreach ($tag_dato as $tag){
                                                    $sumPorcentaje += ($tag == null)?"0": (float)$tag->getAttribute('porcentaje');
                                                }
						$T->setVar('__errores_point_value', ($tag == null)?'0': (string)$sumPorcentaje);
						
						$T->parse('errores_point_element', 'ERRORES_POINT_ELEMENT', true);
					}
					$T->parse('errores_series_element', 'ERRORES_SERIES_ELEMENT', true);
				}
			}
			else {
				$T->setVar('__downtime_point_value', '100');
				$T->setVar('__downtime_point_name', 'Sin Errores');
				$T->setVar('__downtime_point_color', 'eeeeee');
				$T->parse('downtime_point_element', 'DOWNTIME_POINT_ELEMENT', true);
				
				$T->setVar('__errores_series_name', '');
				$T->setVar('__series_name','');
				$T->parse('series_name', 'SERIES_NAME', true);
				
				$T->setVar('errores_point_element', '');
				$T->setVar('__errores_point_name', 'Sin Errores');
				$T->setVar('__errores_point_color', "eeeeee");
				$T->setVar('__errores_point_value', 'null');
				$T->parse('errores_point_element', 'ERRORES_POINT_ELEMENT', true);
				$T->parse('errores_series_element', 'ERRORES_SERIES_ELEMENT', true);
			}

			$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
			$orden++;
		}
		
		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), $paso_elegido, $es_online);
		/* Agrega el acordeon cuando existan eventos*/
		if (count($dataMant)>0){
			$this->resultado.= $this->getAccordion($encode,$nameFunction);
		}
	}

	/**
	 * Funcion para obtener el grafico de
	 * Disponibilidad Historica.
	 *
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
	// TODO: getDisponibilidadHistoricoFlexible()
	function getHistoricoDisponibilidadPasoFlexible() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		if (isset($this->extra["parent_objetivo_id"])) {
			$parent_objetivo_id = $this->extra["parent_objetivo_id"];
		}
		else {
			$parent_objetivo_id = $this->objetivo_id;
		}

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.disponibilidad_resumen_global_historico(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($parent_objetivo_id).",".
				pg_escape_string($this->objetivo_id).",".
				pg_escape_string($this->timestamp->tipo_id).", ".
				pg_escape_string($this->horario_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodoHistorico())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."', ".
				$usr->cliente_id.")";
// 		print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row["disponibilidad_resumen_global_historico"]);
		$xpath = new DOMXpath($dom);

		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_fechas = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle");
		
		if(!$conf_pasos->length) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		$this->botones = $conf_pasos;

		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_historico_flexible.xhtml');

		$T->setBlock('tpl_grafico', 'SERIES_NAME', 'series_name');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
		$T->setBlock('tpl_grafico', 'BLOQUE_PASOS', 'bloque_pasos');

		// TODO: como imprimir de quedar igual a 1 o true
		$es_online = true;
		if (isset($this->extra["imprimir"]) and $this->extra["imprimir"] != 0) {
			$es_online = false;
		}
		elseif (isset($this->extra["paso_id"])) {
			$conf_pasos = $xpath->query("paso[@paso_orden=".$this->extra["paso_id"]."]", $conf_objetivo);
		}
		else {
			$conf_pasos = $xpath->query("paso[@paso_orden=".$conf_pasos->item(0)->getAttribute("paso_orden")."]", $conf_objetivo);
		}

		foreach ($conf_pasos as $conf_paso) {
			$paso_elegido = $conf_paso->getAttribute("paso_orden");

			$T->setVar('__paso_id', $conf_paso->getAttribute("paso_orden"));
			$T->setVar('__paso_nombre', $conf_paso->getAttribute("nombre"));
			$T->setVar('__tiene_titulo', ($es_online)?"none":"inline");
			$T->setVar('__pointWidth', ($conf_fechas->length < 4)?'pointWidth: 70':'');
			$T->setVar('__ancho_leyenda1',"480");
			$T->setVar('__ancho_leyenda2',"120");

			$T->setVar('series_name', '');

			foreach ($conf_fechas as $tag_fecha) {
				if ($this->timestamp->tipo_id == 1) {
					$format = "d/m";
				}
				elseif ($this->timestamp->tipo_id == 2) {
					$format = "d/m";
				}
				else {
					$format = "m/y";
				}
				$T->setVar('__series_name', date($format, strtotime($tag_fecha->getAttribute("fecha"))));
				$T->parse('series_name', 'SERIES_NAME', true);
			}

			$T->setVar('series_element', '');
			for ($i = $conf_eventos->length; $i > 0; $i--) {
				$conf_evento = $conf_eventos->item($i - 1);
				$T->setVar('__point_name', htmlspecialchars($conf_evento->getAttribute("nombre")));
				$T->setVar('__serie_color', $conf_evento->getAttribute("color"));
				$T->setVar('point_element', '');
				foreach ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle") as $tag_fecha) {
					$dato = $xpath->query("detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]", $tag_fecha);
					$porcentaje = ($dato->length == 0)?"0":$dato->item(0)->getAttribute("porcentaje");
					$T->setVar('__point_value', $porcentaje);
					$T->parse('point_element', 'POINT_ELEMENT', true);
				}
				$T->parse('series_element', 'SERIES_ELEMENT', true);
			}

			// DISPONIBILIDAD : SLA DEL GRAFICO
			$sla_ok = 'false';
			$sla_error = 'false';
			$T->setVar('tiene_sla_ok', '');
			if ($conf_objetivo->getAttribute("sla_disponibilidad_ok") != null and $conf_objetivo->getAttribute("sla_disponibilidad_ok") != "") {
				$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute("sla_disponibilidad_ok"));
				$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', true);
				$sla_ok = 'true';
			}

			$T->setVar('tiene_sla_error', '');
			if ($conf_objetivo->getAttribute("sla_disponibilidad_error") != null and $conf_objetivo->getAttribute("sla_disponibilidad_error") != "") {
				$T->setVar('__sla_error_value', $conf_objetivo->getAttribute("sla_disponibilidad_error"));
				$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', true);
				$sla_error = 'true';
			}

			$T->setVar('__muestra_sla_ok', $sla_ok);
			$T->setVar('__muestra_sla_error', $sla_error);

			$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));

		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), $paso_elegido, $es_online);
	}

	// TODO: metodo getDisponibilidadConsolidadoHistoricoFlexible()
	function getHistoricoDisponibilidadFlexible() {
		$this->extra["variable"] = 'true';
		echo $this->getHistoricoDisponibilidad();
	}

	// TODO: metodo getDisponibilidadConsolidadoHistorico()
	function getHistoricoDisponibilidad() {
		global $usr;
		
		$event = new Event;
		# Variables para eventos especiales marcados por el cliente codigo 9.
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		$nameFunction =  'disponibilidad_ponderada';
		$tieneEvento = 'false';
		$marcado = false;
		$dataMant = null;
		$ids = null;

		$xpath = $this->getDatosHistoricoDisponibilidad();
		$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
		$tag_fechas = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle");
		
		if (
			!$xpath->query("paso[@visible=1]", $conf_objetivo)->length
			or $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length==0
		) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_historico.xhtml');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_NAME', 'series_name');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');

		$T->setVar('__pointWidth', ($tag_fechas->length < 4)?'pointWidth: 70':'');
		$T->setVar('__label', ($tag_fechas->length < 7)?'this.value':'parseInt(this.axis.categories.indexOf(this.value))+1');
		$T->setVar('__leyenda_label', (($tag_fechas->length < 7)?"":"leyendaLabel(chart_disponibilidad_historica,'leyenda_disponibilidad_historica1');"));
		$T->setVar('__ancho_leyenda1',"450");
		$T->setVar('__ancho_leyenda2',"150");

		$T->setVar('series_name', '');
		# Busca marcados dentro del xml.
		
		foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
		}	
		
		# Verifica y asigna variables en caso de que exista marcado.
		if ($marcado == true) {

			$dataMant =$event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			$tieneEvento = 'true';
			$encode = json_encode($dataMant);
			$T->setVar('__tiene_evento', $tieneEvento);
			$T->setVar('__name', $nameFunction);
		}
		foreach ($tag_fechas as $tag_fecha) {
			$T->setVar('__series_name', $this->timestamp->getFormatoFecha($tag_fecha->getAttribute('fecha')));
			$T->parse('series_name', 'SERIES_NAME', true);
		}

		$T->setVar('series_element', '');
		for ($i = $conf_eventos->length; $i > 0; $i--) {

			# Codigo agregado para el correcto orden y visualización de los colores en los graficos.
			$evento_id_xml = null;
			$evento_id_xml = $conf_eventos->item($i - 1)->getAttribute("evento_id");
			if ($evento_id_xml=='9'){
				$evento_id = '7';
				$conf_evento = $conf_eventos->item(3);
			}
			elseif($evento_id_xml=='7'){
				$evento_id = '9';
				$conf_evento = $conf_eventos->item(4);
			}
			else{
				$evento_id = $evento_id_xml;
				$conf_evento = $conf_eventos->item($i - 1);
			}
			# Para que no muestre los datos eventos clientes cuando no existen en el periodo.
			if ($marcado == false and $conf_evento->getAttribute("evento_id") == '9'){
				continue;
			}
			$T->setVar('__point_name', htmlspecialchars($conf_evento->getAttribute("nombre")));
			$T->setVar('__serie_color', $conf_evento->getAttribute("color"));
			$T->setVar('point_element', '');
			foreach ($tag_fechas as $tag_fecha) {
				$tag_dato = $xpath->query("estadisticas/estadistica[@evento_id=".$evento_id."]", $tag_fecha);

				$porcentaje = ($tag_dato->length == 0)?"0":$tag_dato->item(0)->getAttribute("porcentaje");
				$T->setVar('__point_value', $porcentaje);
				$T->parse('point_element', 'POINT_ELEMENT', true);
			}
			$T->parse('series_element', 'SERIES_ELEMENT', true);
		}

		// DISPONIBILIDAD : SLA DEL GRAFICO
		$sla_ok = 'false';
		$sla_error = 'false';
		$T->setVar('tiene_sla_ok', '');
		if ($conf_objetivo->getAttribute("sla_disponibilidad_ok") != null and $conf_objetivo->getAttribute("sla_disponibilidad_ok") != "") {
			$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute("sla_disponibilidad_ok"));
			$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', true);
			$sla_ok = 'true';
		}

		$T->setVar('tiene_sla_error', '');
		if ($conf_objetivo->getAttribute("sla_disponibilidad_error") != null and $conf_objetivo->getAttribute("sla_disponibilidad_error") != "") {
			$T->setVar('__sla_error_value', $conf_objetivo->getAttribute("sla_disponibilidad_error"));
			$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', true);
			$sla_error = 'true';
		}

		$T->setVar('__muestra_sla_ok', $sla_ok);
		$T->setVar('__muestra_sla_error', $sla_error);

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));

		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'));
		/* Agrega el acordeon cuando existan eventos*/
		if (count($dataMant)>0){
			$this->resultado.= $this->getAccordion($encode,$nameFunction);
		}
	}



	/*************** FUNCIONES DE GRAFICOS DE RENDIMIENTO ***************/
	/*************** FUNCIONES DE GRAFICOS DE RENDIMIENTO ***************/
	/*************** FUNCIONES DE GRAFICOS DE RENDIMIENTO ***************/

	// TODO: metodo getRendimientoConsolidado()
	function getConsolidadoRendimiento($es_descarga) {
		global $usr;
		global $log;
		global $mdb2;

		$event = new Event;
		$encode = null;
		$dataMant = null;
		$ids = null;
		$marcado = false;
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		$nameFunction = 'consolidado_rendimiento';
		$tieneEvento = 'false';
		$xpathSql = $this->getDatosConsolidadoRendimientoEspecial();
		$xpath = $xpathSql['xpath'];
		$sql = $xpathSql['sql'];
		if (is_object($xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0))) {
			$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		}else{
			$res = & $mdb2->query($sql);
			$log->setError($sql, $res->userinfo);
			$this->resultado = $this->__generarContenedorErrorSql();
			return;
		}
		
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);

		if ($conf_pasos->length == 0 or $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		# Busca y guarda los eventos marcados por el cliente
		foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado){
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
		}			

		# Verifica si existe marcado.
		if ($marcado == true) {
			$dataMant =$event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			$tieneEvento = 'true';
			// Variable que sera enviada al tpl accordion.
			$encode = json_encode($dataMant);
		}
		$inicio = strtotime($this->timestamp->getInicioPeriodo());
		$termino = strtotime($this->timestamp->getTerminoPeriodo());
		$intervalo = $this->intervaloLinea($inicio, $termino);

		//CARGA LOS BLOQUES EN EL ARCHIVO TEMPLATE
		$T =& new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_consolidado.xhtml');
		$T->setBlock('tpl_grafico', 'TIENE_MAXIMO', 'tiene_maximo');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
		$T->setBlock('tpl_grafico', 'RANGE_ELEMENT', 'range_element');
		$T->setVar('__tiene_evento', $tieneEvento);
		$T->setVar('__name', $nameFunction);

		// DATOS DEL GRAFICO
                if (isset($this->extra["monitor"]) and $this->extra["monitor"] != null) {
                	$ver_graficos = $this->extra["monitor"];
                    $ver_graficos++;
                    $tabla = $ver_graficos;
                } else {
                    $ver_graficos = "";
                    $tabla = "";
                }
                /*cuando es descarga pdf*/
                if(isset($_REQUEST['es_pdf'])==true){
                	$es_descarga = true;
                     
                }else{
                	$es_descarga = false;
                }
                $T->setVar('es_descarga', $es_descarga);
            foreach ($conf_pasos as $conf_paso) {
            	$T->setVar('__graficos', $ver_graficos);
	            $T->setVar('__tabla', $tabla);
				$T->setVar('__series_id', $conf_paso->getAttribute('nombre'));
				$T->setVar('__series_name', $conf_paso->getAttribute('nombre'));
				$T->setVar('__series_color', Utiles::getDefaultColor($conf_paso->getAttribute('paso_orden')));
				$T->setVar('point_element', '');
				$ren_aux = 'null';
				for ($i = $inicio; $i < $termino; $i = $i + $intervalo) {
					$tag_dato = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato[@fecha='".date("Y-m-d\TH:i:s", $i)."']")->item(0);
					if (($tag_dato==null or $tag_dato->getAttribute("respuesta") == "S/I") and $ren_aux != 'null') {
						$ren_aux = 'null';
						continue;
					}
				$ren_aux = ($tag_dato==null || $tag_dato->getAttribute("respuesta") == "S/I")?'null':$tag_dato->getAttribute("respuesta");
				$T->setVar('__point_name', date("Y,(m - 1),d,H,i,s", $i));
				$T->setVar('__point_value', $ren_aux);
				$T->parse('point_element', 'POINT_ELEMENT', true);
			}
			$T->parse('series_element', 'SERIES_ELEMENT', true);
		}
		$maximo_escala = $_SESSION["valor_escala_".$conf_objetivo->getAttribute('objetivo_id')."_".$this->__item_id];
		$T->setVar('tiene_maximo', '');
		if ($maximo_escala > 0) {
			$T->setVar('__y_scale_maximum', ($maximo_escala>0)?$maximo_escala:'');
			$T->parse('tiene_maximo', 'TIENE_MAXIMO', false);
		}

		$T->setVar('tiene_sla_ok', '');
		if ($conf_objetivo->getAttribute('sla_rendimiento_ok') != "") {
			$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute('sla_rendimiento_ok'));
			$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', false);
		}

		$T->setVar('tiene_sla_error', '');
		if ($conf_objetivo->getAttribute('sla_rendimiento_error') != "") {
			$T->setVar('__sla_error_value', $conf_objetivo->getAttribute('sla_rendimiento_error'));
			$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', false);
		}

		// HORARIO HABIL DEL GRAFICO
		$T->setVar('range_element', '');
		 foreach ($dataMant as $key => $value) {
			$array_inicio_marcado = explode(',',str_replace($character,"",($value['fecha_inicio'])));
			$array_termino_marcado = explode(',',str_replace($character,"",($value['fecha_termino'])));
			$range_minumum = date("Y,(m - 1),d,H,i,s",strtotime($array_inicio_marcado['0']));
			$range_maximum = date("Y,(m - 1),d,H,i,s",strtotime($array_termino_marcado['0']));
			$T->setVar('__range_minimum', $range_minumum);
			$T->setVar('__range_maximum', $range_maximum);
			$T->parse('range_element', 'RANGE_ELEMENT', true);
		}

		// FORMATO DEL GRAFICO
		$T->setVar('__x_tick_interval', ($this->timestamp->tipo_periodo==REP_PRD_DAY)?(3600 * 1000):(24 * 3600 * 1000));
		$T->setVar('__x_format_value', ($this->timestamp->tipo_periodo==REP_PRD_DAY)?'%H:%M':'%d/%m');
		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), 0, false, ($this->extra["imprimir"]==1)?false:true);
		/* Agrega el acordeon cuando existan eventos*/
		if (count($dataMant)>0){
			$this->resultado.= $this->getAccordion($encode,$nameFunction);
		}	
	}

	/*
	Creado por:Aldo Cruz Romero
	Modificado por:
	Fecha de creacion:13-01-2018
	Fecha de ultima modificacion:
    */
	function getRendimientoApi($es_descarga){
		global $usr;
		include 'utils/get_tiempo.php';
		global $current_usuario_id;
		global $mdb2;

		$event = new Event;
		$encode = null;
		$ids = null;
		$nameFunction = 'consolidado_rendimiento';
		$tieneEvento = 'false';
		$xpath = $this->getDatosConsolidadoRendimiento();

		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);

		if ($conf_pasos->length == 0 or $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}
		//CARGA LOS BLOQUES EN EL ARCHIVO TEMPLATE
		$T =& new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_api.xhtml');
		$T->setBlock('tpl_grafico', 'TIENE_MAXIMO', 'tiene_maximo');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
		$T->setVar('__tiene_evento', $tieneEvento);
		$T->setVar('__name', $nameFunction);

        /*cuando es descarga pdf*/
        if($es_descarga==true){
            $T->setVar('es_descarga', 'true');
        }
        //ENVIO DE DATOS AL TEMPLATE PARA API
        $xpath_token = $this->getTokenApiBase($this->extra['parent_objetivo_id']);

		$token = $xpath_token->query("/atentus/config/especial/token")->item(0);
		$token_usuario = $token->getAttribute('token');

        $T->setVar('__objetive', $this->objetivo_id);
        $T->setVar('__token', $token_usuario);

        $maximo_escala = $_SESSION["valor_escala_".$conf_objetivo->getAttribute('objetivo_id')."_".$this->__item_id];
		$T->setVar('tiene_maximo', '');
		if ($maximo_escala > 0) {
			$T->setVar('__y_scale_maximum', ($maximo_escala>0)?$maximo_escala:'');
			$T->parse('tiene_maximo', 'TIENE_MAXIMO', false);
		}

		$T->setVar('tiene_sla_ok', '');
		if ($conf_objetivo->getAttribute('sla_rendimiento_ok') != "") {
			$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute('sla_rendimiento_ok'));
			$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', false);
		}

		$T->setVar('tiene_sla_error', '');
		if ($conf_objetivo->getAttribute('sla_rendimiento_error') != "") {
			$T->setVar('__sla_error_value', $conf_objetivo->getAttribute('sla_rendimiento_error'));
			$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', false);
		}

		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), 0, false, ($this->extra["imprimir"]==1)?false:true);
	}
	/*
	Creado por:Aldo Cruz Romero
	Modificado por:
	Fecha de creacion:13-01-2018
	Fecha de ultima modificacion:
    */
	function getConsolidadoRendimientoOnline($es_descarga) {
		global $usr;
		global $log;
		global $mdb2;
		global $cliente_id;
		global $current_usuario_id;

		$interval=REP_API_INTERVAL;
		$event = new Event;
		$encode = null;
		$ids = null;
		$nameFunction = 'consolidado_rendimiento';
		$tieneEvento = 'false';
		$xpathSql = $this->getDatosConsolidadoRendimientoOnline();
		$xpath = $xpathSql['xpath'];
		$sql = $xpathSql['sql'];
		
		if (is_object($xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0))) {
			$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		}else{
			$res = & $mdb2->query($sql);
			$log->setError($sql, $res->userinfo);
			$this->resultado = $this->__generarContenedorErrorSql();
			return;
		}
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		if ($conf_pasos->length == 0 or $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}
		# Busca y guarda los eventos marcados por el cliente
		foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado){
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
		}
		//CARGA LOS BLOQUES EN EL ARCHIVO TEMPLATE
		$T =& new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_consolidado_online.xhtml');
		$T->setBlock('tpl_grafico', 'BLOQUE_PASO', 'bloque_paso');
		$T->setBlock('tpl_grafico', 'TIENE_MAXIMO', 'tiene_maximo');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
		$T->setVar('__interval', $interval);
		$T->setVar('__tiene_evento', $tieneEvento);
		$T->setVar('__name', $nameFunction);
		$T->setVar('__objetive', $this->objetivo_id);
		$T->setVar('__usuario_id', $current_usuario_id);
		$T->setVar('__informe_id', $this->__item_id);
		$T->setVar('__cliente_id', $usr->cliente_id);
		$T->setVar('__token', $_SESSION["_authsession"]["challengecookie"]);

		$date=getdate();
		$date=new DateTime(pg_escape_string($this->timestamp->date));
		$date_utc=$date->setTimezone(new DateTimeZone("UTC"));
		$array_date=(array) $date_utc;
		$fecha=explode(".", $array_date["date"]);
		$fecha_split=explode(" ", $fecha[0]);
		$fecha_utc=explode(":", $fecha_split[1]);
		
		$hour_inicio_utc=intval($fecha_utc[0])-3;
		$hour_termino_utc=intval($fecha_utc[0]);
		if($hour_termino_utc>24){
			$hour_termino_utc='00';
		}
		if($hour_termino_utc<10){
			$hour_termino_utc='0'.$hour_termino_utc;
		}
		if($hour_inicio_utc<10){
			$hour_inicio_utc='0'.$hour_inicio_utc;
		}
		$fecha_inicio_utc=$fecha_split[0].' '.$hour_inicio_utc.':00';
		$fecha_termino_utc=$fecha_split[0].' '.$hour_termino_utc.':00';
		$T->setVar('__fecha_inicio_utc', $fecha_inicio_utc);
		$T->setVar('__fecha_termino_utc',$fecha_termino_utc);
		$T->setVar('__log_fecha', $fecha[0]);
		// DATOS DEL GRAFICO
        if (isset($this->extra["monitor"]) and $this->extra["monitor"] != null) {
        	$ver_graficos = $this->extra["monitor"];
            $ver_graficos++;
            $tabla = $ver_graficos;
        } else {
            $ver_graficos = "";
            $tabla = "";
        }
        /*cuando es descarga pdf*/
        if($es_descarga==true){
            $T->setVar('es_descarga', 'true');
        }
        $T->setVar('bloque_paso', '');
        foreach ($conf_pasos as $conf_paso) {
           	$T->setVar('__pasos', $conf_paso->getAttribute('paso_orden'));
           	$T->setVar('__nombre_pasos', $conf_paso->getAttribute('nombre'));
           	$T->setVar('__graficos', $ver_graficos);
	        $T->setVar('__tabla', $tabla);
			$T->setVar('__series_id', $conf_paso->getAttribute('nombre'));
			$T->setVar('__series_name', $conf_paso->getAttribute('nombre'));
			$T->setVar('__series_color', Utiles::getDefaultColor($conf_paso->getAttribute('paso_orden')));
			$T->parse('bloque_paso', 'BLOQUE_PASO', true);
		}
		$maximo_escala = $_SESSION["valor_escala_".$conf_objetivo->getAttribute('objetivo_id')."_".$this->__item_id];
		$T->setVar('tiene_maximo', '');
		if ($maximo_escala > 0) {
			$T->setVar('__y_scale_maximum', ($maximo_escala>0)?$maximo_escala:'');
			$T->parse('tiene_maximo', 'TIENE_MAXIMO', false);
		}

		$T->setVar('tiene_sla_ok', '');
		if ($conf_objetivo->getAttribute('sla_rendimiento_ok') != "") {
			$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute('sla_rendimiento_ok'));
			$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', false);
		}

		$T->setVar('tiene_sla_error', '');
		if ($conf_objetivo->getAttribute('sla_rendimiento_error') != "") {
			$T->setVar('__sla_error_value', $conf_objetivo->getAttribute('sla_rendimiento_error'));
			$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', false);
		}

		// FORMATO DEL GRAFICO
		$T->setVar('__x_tick_interval', ($this->timestamp->tipo_periodo==REP_PRD_DAY)?(3600 * 1000):(24 * 3600 * 1000));
		$T->setVar('__x_format_value', ($this->timestamp->tipo_periodo==REP_PRD_DAY)?'%H:%M':'%d/%m');
		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), 0, false, ($this->extra["imprimir"]==1)?false:true);
		/* Agrega el acordeon cuando existan eventos*/
		if (count($dataMant)>0){
			$this->resultado.= $this->getAccordion($encode,$nameFunction);
		}	
	}

	/**
	 * Funcion para obtener el grafico de
	 * Estadisticas y Detalle Rendimiento.
	 *
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
	// TODO: metodo getRendimientoParcial()
	function getEstadisticoRendimiento($es_descarga) {
		global $usr;

		$event = new Event;
		$encode = null;
		$dataMant = null;
		$ids = null;
		$marcado = false;
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId);
		$timeZone = $arrTime[$timeZoneId];
		$nameFunction = 'rendimiento_por_paso'.$this->objetivo_id.''.$this->extra['horario_id_item'];
		$tieneEvento = 'false';

		$xpath = $this->getDatosEstadisticoRendimiento();
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id!=0]");
		$conf_horarios = $xpath->query("/atentus/resultados/propiedades/horarios_habiles/horario_habil");
		$this->botones = $conf_pasos;
		
		if (!$conf_pasos->length or $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		# Busca y guarda los eventos marcados por el cliente
		foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado){
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
		}

		# Verifica si existe marcado.
		if ($marcado == true) {
			$dataMant =$event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			$tieneEvento = 'true';
			// Variable que sera enviada al tpl accordion.
			$encode = json_encode($dataMant);
		}

		$inicio = strtotime($this->timestamp->getInicioPeriodo());
		$termino = strtotime($this->timestamp->getTerminoPeriodo());
		$intervalo = $this->intervaloLinea($inicio, $termino);

		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_por_nodo.xhtml');
		$T->setBlock('tpl_grafico', 'TIENE_MAXIMO', 'tiene_maximo');
		$T->setBlock('tpl_grafico', 'ISP_TIENE_SLA_OK', 'isp_tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'ISP_TIENE_SLA_ERROR', 'isp_tiene_sla_error');
		$T->setBlock('tpl_grafico', 'ISP_POINT_ELEMENT', 'isp_point_element');
		$T->setBlock('tpl_grafico', 'ISP_SERIES_ELEMENT', 'isp_series_element');
		$T->setBlock('tpl_grafico', 'ISP_HORARIOS', 'isp_horarios');
		$T->setBlock('tpl_grafico', 'CONSOLIDADO_TIENE_SLA_OK', 'consolidado_tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'CONSOLIDADO_TIENE_SLA_ERROR', 'consolidado_tiene_sla_error');
		$T->setBlock('tpl_grafico', 'CONSOLIDADO_POINT_ELEMENT', 'consolidado_point_element');
		$T->setBlock('tpl_grafico', 'CONSOLIDADO_HORARIOS', 'consolidado_horarios');
		$T->setBlock('tpl_grafico', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setVar('__tiene_evento', $tieneEvento);
		$T->setVar('__name', $nameFunction);

		$es_online = true;
		if (isset($this->extra["imprimir"]) and $this->extra["imprimir"] != 0) {
			$pasos = $conf_pasos;
			$es_online = false;
		}
		elseif (isset($this->extra["paso_id"])) {
			$pasos[] = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@paso_orden=".$this->extra["paso_id"]."]")->item(0);
		}
		else {
			$pasos[] = $conf_pasos->item(0);
		}

		$T->setVar('__item_orden', $this->extra["item_orden"]);

		$orden = 1;
                /*cuando es descarga pdf*/
        if(isset($_REQUEST['es_pdf'])){
            $es_descarga = true;
        }else{
           	$es_descarga = false;
        }
        $T->setVar('es_descarga', $es_descarga);
		foreach ($pasos as $conf_paso) {
			$tag_paso = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]")->item(0);
			$paso_elegido = $conf_paso->getAttribute('paso_orden');
			$T->setVar('__paso_id', $conf_paso->getAttribute('paso_orden'));
			$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
			$T->setVar('__paso_orden', $orden);
			$T->setVar('__tiene_titulo', ($es_online)?"none":"inline");
			$T->setVar('__objetivo_id', $this->objetivo_id);
			$T->setVar('__horario_id_item', isset($this->extra['horario_id_item'])?$this->extra['horario_id_item']:'');

			$T->setVar('isp_series_element', '');
			foreach ($conf_nodos as $conf_nodo) {
				$T->setVar('__isp_series_id', $conf_nodo->getAttribute('nombre'));
				$T->setVar('__isp_series_name', $conf_nodo->getAttribute('nombre'));
				$T->setVar('__isp_series_color', Utiles::getDefaultColor($conf_nodo->getAttribute('nodo_id')));
				$T->setVar('isp_point_element', '');

				$ren_aux = 'null';
				for ($i = $inicio; $i < $termino; $i = $i + $intervalo) {
					$tag_dato = $xpath->query("detalles/detalle[@nodo_id=".$conf_nodo->getAttribute('nodo_id')."]/datos/dato[@fecha='".date("Y-m-d\TH:i:s", $i)."']", $tag_paso)->item(0);
					if (($tag_dato==null or $tag_dato->getAttribute("respuesta") == "S/I") and $ren_aux != 'null') {
						$ren_aux = 'null';
						continue;
					}
					$ren_aux = ($tag_dato==null || $tag_dato->getAttribute("respuesta") == "S/I")?'null':$tag_dato->getAttribute("respuesta");
					$T->setVar('__isp_point_name', date("Y,(m - 1),d,H,i,s", $i));
					$T->setVar('__isp_point_value', $ren_aux);
					$T->parse('isp_point_element', 'ISP_POINT_ELEMENT', true);
				}
				$T->parse('isp_series_element', 'ISP_SERIES_ELEMENT', true);
			}

			/* RENDIMIENTO : SLA DEL GRAFICO */
			$T->setVar('isp_tiene_sla_error', '');
			if ($conf_objetivo->getAttribute('sla_rendimiento_error') != "") {
				$T->setVar('__isp_sla_error_value', $conf_objetivo->getAttribute('sla_rendimiento_error'));
				$T->parse('isp_tiene_sla_error', 'ISP_TIENE_SLA_ERROR', true);
			}

			$T->setVar('isp_tiene_sla_ok', '');
			if ($conf_objetivo->getAttribute('sla_rendimiento_ok') != "") {
				$T->setVar('__isp_sla_ok_value', $conf_objetivo->getAttribute('sla_rendimiento_ok'));
				$T->parse('isp_tiene_sla_ok', 'ISP_TIENE_SLA_OK', true);
			}

			// HORARIO HABIL DEL GRAFICO
			$T->setVar('isp_horarios', '');
		 	foreach ($dataMant as $key => $value) {
				$array_inicio = explode(',',str_replace($character,"",($value['fecha_inicio'])));
				$array_termino = explode(',',str_replace($character,"",($value['fecha_termino'])));
				$range_min = date("Y,(m - 1),d,H,i,s",strtotime($array_inicio['0']));
				$range_max = date("Y,(m - 1),d,H,i,s",strtotime($array_termino['0']));
				$T->setVar('__isp_range_minimum', $range_min);
				$T->setVar('__isp_range_maximum', $range_max);
				$T->parse('isp_horarios', 'ISP_HORARIOS', true);
			}

			/* ESTADISTICAS : DATOS DEL GRAFICO */
			$tag_estadistica = $xpath->query("estadistica", $tag_paso)->item(0);

			if ($tag_estadistica != null) {
				$T->setVar('__consolidado_prom_line_value', floatVal($tag_estadistica->getAttribute('promedio')));
				$T->setVar('__desviacion_min', ($tag_estadistica->getAttribute('promedio') < $tag_estadistica->getAttribute('desviacion'))?'0':($tag_estadistica->getAttribute('promedio') - $tag_estadistica->getAttribute('desviacion')));
				$T->setVar('__desviacion_max', floatVal($tag_estadistica->getAttribute('promedio') + $tag_estadistica->getAttribute('desviacion')));
			}
			else {
				$T->setVar('__consolidado_prom_line_value', 0);
				$T->setVar('__desviacion_min', 0);
				$T->setVar('__desviacion_max', 0);
			}
			$T->setVar('__consolidado_series_name', $conf_paso->getAttribute('nombre'));
			$T->setVar('__consolidado_series_color', Utiles::getDefaultColor($conf_paso->getAttribute('paso_orden')));

			$ren_aux = 'null';
			$T->setVar('consolidado_point_element', '');
			for ($i = $inicio; $i < $termino; $i = $i + $intervalo) {
				$tag_dato = $xpath->query("datos/dato[@fecha='".date("Y-m-d\TH:i:s", $i)."']", $tag_paso)->item(0);
				if (($tag_dato==null or $tag_dato->getAttribute("respuesta") == "S/I") and $ren_aux != 'null') {
					$ren_aux = 'null';
					continue;
				}
				$ren_aux = ($tag_dato==null || $tag_dato->getAttribute("respuesta") == "S/I")?'null':$tag_dato->getAttribute("respuesta");
				$T->setVar('__consolidado_point_name', date("Y,(m - 1),d,H,i,s", $i));
				$T->setVar('__consolidado_point_value', $ren_aux);
				$T->parse('consolidado_point_element', 'CONSOLIDADO_POINT_ELEMENT', true);
			}

			/* ESTADISTICAS : SLA DEL GRAFICO */
			$T->setVar('consolidado_tiene_sla_ok', '');
			if ($conf_objetivo->getAttribute('sla_rendimiento_ok')!="" and  $conf_objetivo->getAttribute('sla_rendimiento_ok')> 0) {
				$T->setVar('__consolidado_sla_ok_value', $conf_objetivo->getAttribute('sla_rendimiento_ok'));
				$T->parse('consolidado_tiene_sla_ok', 'CONSOLIDADO_TIENE_SLA_OK', false);
			}

			$T->setVar('consolidado_tiene_sla_error', '');
			if ($conf_objetivo->getAttribute('sla_rendimiento_error')!=""and $conf_objetivo->getAttribute('sla_rendimiento_error') > 0) {
				$T->setVar('__consolidado_sla_error_value', $conf_objetivo->getAttribute('sla_rendimiento_error'));
				$T->parse('consolidado_tiene_sla_error', 'CONSOLIDADO_TIENE_SLA_ERROR', false);
			}

			// HORARIO HABIL DEL GRAFICO
			$T->setVar('consolidado_horarios', '');
		 	foreach ($dataMant as $key => $value) {
				$array_inicio = explode(',',str_replace($character,"",($value['fecha_inicio'])));
				$array_termino = explode(',',str_replace($character,"",($value['fecha_termino'])));
				$range_min = date("Y,(m - 1),d,H,i,s",strtotime($array_inicio['0']));
				$range_max = date("Y,(m - 1),d,H,i,s",strtotime($array_termino['0']));
				$T->setVar('__consolidado_range_minimum', $range_min);
				$T->setVar('__consolidado_range_maximum', $range_max);
				$T->parse('consolidado_horarios', 'CONSOLIDADO_HORARIOS', true);
			}


			/* FORMATO DEL GRAFICO */
			$maximo_escala = $_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id];
			$T->setVar('tiene_maximo', '');
			if($maximo_escala > 0) {
				$T->setVar('__y_scale_maximum', $maximo_escala);
				$T->parse('tiene_maximo', 'TIENE_MAXIMO', false);
			}

			$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
			$orden++;
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), $paso_elegido, $es_online, $es_online);
		if (count($dataMant)>0){
			$this->resultado.= $this->getAccordion($encode,$nameFunction);
		}

	}


	function getRendimientoPorDia($es_descarga) {
		global $dias_semana;

		$xpath = $this->getDatosRendimientoPorDia2();
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		
		if (!$conf_pasos->length or $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		$es_online = true;
		if (isset($this->extra["imprimir"]) and $this->extra["imprimir"] != 0) {
			$es_online = false;
		}

		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_por_dia.xhtml');
		$T->setBlock('tpl_grafico', 'TIENE_MAXIMO', 'tiene_maximo');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
                /*cuando es descarga pdf*/
                if($es_descarga==true){
                    $T->setVar('es_descarga', 'true');
                }
		/* DATOS DEL GRAFICO */
		foreach ($conf_pasos as $conf_paso) {
			$T->setVar('__series_id', $conf_paso->getAttribute('nombre'));
			$T->setVar('__series_name', $conf_paso->getAttribute('nombre'));
			$T->setVar('__series_color', Utiles::getDefaultColor($conf_paso->getAttribute('paso_orden')));

			$T->setVar('point_element', '');
			foreach ($dias_semana as $id => $nombre) {
				if ($id == 7) {
					$id = 0;
				}
				for ($hora = 0; $hora < 24; $hora++) {
					$hora_text = sprintf("%1$02d:00:00", $hora);
					$tag_dato = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/detalles/detalle[@dia_id=".$id."]/estadisticas/estadistica[@hora='".$hora_text."']")->item(0);
					$T->setVar('__point_value', ($tag_dato == null)?'null':$tag_dato->getAttribute('tiempo_prom'));
					$T->setVar('__point_name', $nombre." ".(($hora==0)?'':$hora_text));
					$T->parse('point_element', 'POINT_ELEMENT', true);
				}
			}
			$T->parse('series_element', 'SERIES_ELEMENT', true);
		}

		$maximo_escala = $_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id];
		$T->setVar('tiene_maximo','');

		if($maximo_escala>0){
			$T->setVar('__y_scale_maximum', $maximo_escala);
			$T->parse('tiene_maximo', 'TIENE_MAXIMO', false);
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), 0, false, $es_online);
	}

	/**
	 * Funcion para obtener el grafico de
	 * Rendimiento Historico.
	 *
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
	// TODO: metodo getRendimientoHistorico()
	function getHistoricoRendimiento($es_descarga) {
		global $usr;
		$horarios = array($usr->getHorario(0));
		if ($this->horario_id != 0) {
			$horarios[] = $usr->getHorario($this->horario_id);
			$this->horario_id = 0;
		}
		$xpathSql = $this->getDatosHistoricoRendimientoEspecial();
		$xpath = $xpathSql['xpath'];
		$sql = $xpathSql['sql'];
		if (is_object($xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0))) {
			$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		}else{
			$res = & $mdb2->query($sql);
			$log->setError($sql, $res->userinfo);
			$this->resultado = $this->__generarContenedorErrorSql();
			return;
		}
		
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		
		if ($conf_pasos->length == 0 or $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length == 0) {
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		$es_online = true;
		if (isset($this->extra["imprimir"]) and $this->extra["imprimir"] != 0) {
			$es_online = false;
		}

		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_historico.xhtml');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
		$T->setBlock('tpl_grafico', 'TIENE_MAXIMO', 'tiene_maximo');
		$T->setBlock('tpl_grafico', 'BLOQUE_LEYENDA', 'bloque_leyenda');
		$T->setBlock('tpl_grafico', 'BLOQUE_GRAFICOS','bloque_graficos');
		$T->setBlock('tpl_grafico', 'BLOQUE_CONTENEDORES', 'bloque_contenedores');
                /*cuando es descarga pdf*/
                if($es_descarga==true){
                    $T->setVar('es_descarga', 'true');
                }
		//CREA UN GRÁFICO POR CADA HORARIO
		foreach ($horarios as $horario) {

			if ($horario->horario_id != 0) {
				$this->horario_id = $horario->horario_id;
				$xpath = $this->getDatosHistoricoRendimiento();
				$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			}

			$T->setVar('__idHorario', $horario->horario_id);
			$T->setVar('__nombreHorario', (count($horarios)==1)?"":$horario->nombre);
//			$T->setVar('__tieneTitulo', (count($horarios)==1)?"false":"true");

			// DATOS DEL GRAFICO
			$T->setVar('series_element', '');
			foreach ($conf_pasos as $conf_paso) {
				$T->setVar('__series_id', $conf_paso->getAttribute('nombre'));
				$T->setVar('__series_name',$conf_paso->getAttribute('nombre'));
				$T->setVar('__series_color', Utiles::getDefaultColor($conf_paso->getAttribute('paso_orden')));
				$T->setVar('point_element', '');

				$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato");
				foreach ($tag_datos as $tag_dato) {
					$T->setVar('__point_name', date("Y,(m - 1),d,H,i,s", strtotime($tag_dato->getAttribute("fecha"))));
					$T->setVar('__point_value', ($tag_dato->getAttribute("respuesta") == "S/I")?"null":$tag_dato->getAttribute("respuesta"));
					$T->parse('point_element', 'POINT_ELEMENT', true);
				}
				$T->parse('series_element', 'SERIES_ELEMENT', true);
			}

			//CARGA LOS SLA
			$T->setVar('tiene_sla_ok', '');
			if ($conf_objetivo->getAttribute('sla_rendimiento_ok')!="" and $conf_objetivo->getAttribute('sla_rendimiento_ok') > 0) {
				$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute('sla_rendimiento_ok'));
				$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', false);
			}
			$T->setVar('tiene_sla_error', '');
			if ($conf_objetivo->getAttribute('sla_rendimiento_error')!="" and $conf_objetivo->getAttribute('sla_rendimiento_error') > 0) {
				$T->setVar('__sla_error_value', $conf_objetivo->getAttribute('sla_rendimiento_error'));
				$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', false);
			}

			/* FORMATO DEL GRAFICO */
			$maximo_escala = $_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id];
			$T->setVar('tiene_maximo', '');
			if($maximo_escala > 0){
				$T->setVar('__y_scale_maximum', ($maximo_escala>0)?$maximo_escala:'');
				$T->parse('tiene_maximo', 'TIENE_MAXIMO', false);
			}

			$T->parse('bloque_graficos', 'BLOQUE_GRAFICOS', true);
			$T->parse('bloque_leyenda', 'BLOQUE_LEYENDA', true);
			$T->parse('bloque_contenedores', 'BLOQUE_CONTENEDORES', true);
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), 0, false, $es_online);
	}

	// TODO: metodo getRendimientoFrecuencia()
	function getFrecuenciaRendimiento($es_descarga) {
		$xpath = $this->getDatosFrecuenciaRendimiento();
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);

		if(!$conf_pasos->length){
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}
	
		//CARGA EL ARCHIVO TEMPLATE E INICIALIZA SUS BLOQUES
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_frecuencia.xhtml');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
                /*cuando es descarga pdf*/
                if($es_descarga==true){
                    $T->setVar('es_descarga', 'true');
                }
		//RECORRE LOS PASOS DEL OBJETIVO
		foreach ($conf_pasos as $conf_paso) {
			$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato");

			$T->setVar('__series_id', $conf_paso->getAttribute('nombre'));
			$T->setVar('__series_name', $conf_paso->getAttribute('nombre'));
			$T->setVar('__series_color', Utiles::getDefaultColor($conf_paso->getAttribute('paso_orden')));

			$T->setVar('point_element', '');

			//SI TIENE VALORES RECORRE EL ARREGLO FRECUENCIAS PARA SACAR EL TIEMPO ASOCIADO
			if ($tag_datos->length > 0) {
				foreach ($tag_datos as $tag_dato) {
					$T->setVar('__point_name', $tag_dato->getAttribute('respuesta'));
					$T->setVar('__point_value', $tag_dato->getAttribute('cantidad'));
					$T->setVar('__marker', 'true');
					$T->parse('point_element', 'POINT_ELEMENT', true);
				}
			}

			//NO HAY VALORES E INICIALIZA EN 0 SUS VARIABLES
			else {
				$T->setVar('__point_name', 0);
				$T->setVar('__point_value', 0);
				$T->setVar('__marker', 'true');
				$T->parse('point_element', 'POINT_ELEMENT', true);
			}
			$T->parse('series_element', 'SERIES_ELEMENT', true);
		}

		//GUARDA EL VALOR MÁXIMO DE LA ESCALA INGRESADO POR EL SELECT
		$maximo_escala = $_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id];
		$T->setVar('__x_scale_maximum', ($maximo_escala>0)?'max:'.$maximo_escala.',':'');

		$T->setVar('tiene_sla_ok', '');
		if ($conf_objetivo->getAttribute("sla_rendimiento_ok") != "") {
			$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute("sla_rendimiento_ok"));
			$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', false);
		}

		$T->setVar('tiene_sla_error', '');
		if ($conf_objetivo->getAttribute("sla_rendimiento_error") != "") {
			$T->setVar('__sla_error_value', $conf_objetivo->getAttribute("sla_rendimiento_error"));
			$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', false);
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), 0, false, ($this->extra["imprimir"]==1)?false:true);
	}


	// TODO: metodo getRendimientoFrecuenciaAcumulada()
	function getFrecuenciaAculumada($es_descarga) {
		$xpath = $this->getDatosFrecuenciaAculumada();
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);

		if(!$conf_pasos->length){
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}
	
		//CARGA EL ARCHIVO TEMPLATE E INICIALIZA SUS BLOQUES
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_frecuencia_acumulada.xhtml');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
                /*cuando es descarga pdf*/
                if($es_descarga==true){
                    $T->setVar('es_descarga', 'true');
                }
		//RECORRE LOS PASOS DEL OBJETIVO
		foreach ($conf_pasos as $conf_paso) {
 			$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato");

 			$T->setVar('__series_id', $conf_paso->getAttribute('nombre'));
 			$T->setVar('__series_name', $conf_paso->getAttribute('nombre'));
 			$T->setVar('__series_color', Utiles::getDefaultColor($conf_paso->getAttribute('paso_orden')));
			$T->setVar('point_element', '');

			//SI TIENE VALORES RECORRE EL ARREGLO FRECUENCIAS PARA SACAR EL TIEMPO ASOCIADO
			if ($tag_datos->length > 0) {
				foreach ($tag_datos as $tag_dato) {
					$T->setVar('__point_name', $tag_dato->getAttribute('respuesta'));
 					$T->setVar('__point_value', $tag_dato->getAttribute('cantidad'));
					$T->parse('point_element', 'POINT_ELEMENT', true);
				}
			}
			else {
				$T->setVar('__point_name', 0);
				$T->setVar('__point_value', 0);
				$T->parse('point_element', 'POINT_ELEMENT', true);
			}
			$T->parse('series_element', 'SERIES_ELEMENT', true);
		}

		$maximo_escala = $_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id];
		$T->setVar('__x_scale_maximum', ($maximo_escala>0)?'max:'.$maximo_escala.',':'');

		//SLA GRÁFICO
		$T->setVar('tiene_sla_ok', '');
		if ($conf_objetivo->getAttribute("sla_rendimiento_ok") != "") {
			$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute("sla_rendimiento_ok"));
			$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', false);
		}

		$T->setVar('tiene_sla_error', '');
		if ($conf_objetivo->getAttribute("sla_rendimiento_error") != "") {
			$T->setVar('__sla_error_value', $conf_objetivo->getAttribute("sla_rendimiento_error"));
			$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', false);
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), 0, false, ($this->extra["imprimir"]==1)?false:true);
	}




	/*************** FUNCIONES DE GRAFICOS DE SLA ***************/
	/*************** FUNCIONES DE GRAFICOS DE SLA ***************/
	/*************** FUNCIONES DE GRAFICOS DE SLA ***************/

	function getRendimientoSlaSuavisado() {
		$xpath = $this->getDatosSLADetalladoRendimiento();
		$this->__getRendimientoSla($xpath, 'suavizado');
	}

	function getRendimientoSlaReal() {
		$xpath = $this->getDatosSLADetalladoRendimientoReal();
		$this->__getRendimientoSla($xpath, 'real');
	}

	function __getRendimientoSla($xpath, $tipo) {
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_slas = $xpath->query("/atentus/resultados/propiedades/slas/sla");

		if(!$conf_pasos->length){
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		//CARGA EL ARCHIVO TEMPLATE E INICIALIZA SUS BLOQUES
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_sla.xhtml');
		$T->setBlock('tpl_grafico', 'SERIES_NAME', 'series_name');
		$T->setBlock('tpl_grafico', 'ANCHO_SERIE', 'ancho_serie');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');

		$T->setVar('__sla_tipo', $tipo);
		$T->setVar('__step', round($conf_pasos->length/7));
		$T->setVar('__pointWidth', ($conf_pasos->length<4)?'pointWidth: 70,':'');
		$T->setVar('__group_padding', ($conf_pasos->length<4)?'groupPadding: 0.45,':'');
		$T->parse('ancho_serie', 'ANCHO_SERIE', true);

		//AJUSTA EL ANCHO DE LA LEYENDA
		if ($conf_pasos->length < 7) {
			$T->setVar('__label', "'".'<div style="width: 90px; overflow: hidden; text-overflow: ellipsis; float: left; white-space: nowrap">'."'+ this.value +'".' </div>'."'");
			$T->setVar('__leyenda_label', "");
			$T->setVar('__ancho_leyenda1', "0");
			$T->setVar('__ancho_leyenda2', "600");
		}
		else {
			$T->setVar('__label', 'parseInt(this.axis.categories.indexOf(this.value))+1');
			$T->setVar('__leyenda_label', "leyendaLabel(chart_rendimiento_sla_$tipo,'leyenda2rensla_$tipo');");
			$T->setVar('__ancho_leyenda1', "450");
			$T->setVar('__ancho_leyenda2', "150");
		}

		//RECORRE LOS PASOS DE OBJETIVO
		foreach ($conf_pasos as $conf_paso) {
			$T->setVar('__series_name', ($conf_pasos->length>15)?$conf_paso->getAttribute('paso_orden'):$conf_paso->getAttribute('nombre'));
			$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
			$T->parse('series_name', 'SERIES_NAME', true);
		}

		//ENVIA LOS DATOS CON EL FORMATO HIGHCHARTS
		foreach ($conf_slas as $conf_sla) {
			$T->setVar('__point_name', $conf_sla->getAttribute('nombre'));
			$T->setVar('__point_color', $conf_sla->getAttribute('color'));

			$T->setVar('point_element','');
			foreach ($conf_pasos as $conf_paso) {
				$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/estadisticas/estadistica[@sla_id=".$conf_sla->getAttribute('sla_id')."]");

				$T->setVar('__point_value', ($tag_datos->length==0)?0:$tag_datos->item(0)->getAttribute('porcentaje'));
				$T->parse('point_element', 'POINT_ELEMENT', true);
			}
			$T->parse('series_element', 'SERIES_ELEMENT', true);
		}
		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'));
	}


	/**
	 * Funcion para obtener el grafico de
	 * Superacion de SLA Historico.
	 *
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
	// TODO: metodo getRendimientoSlaHistorico()
	function getSLAHistoricoRendimiento() {
		$xpath = $this->getDatosSLAHistoricoRendimiento();
		$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_slas = $xpath->query("/atentus/resultados/propiedades/slas/sla");

		if(!$xpath->query("paso[@visible=1]", $conf_objetivo)->length){
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_sla_historico.xhtml');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		$T->setBlock('tpl_grafico', 'SERIES_NAME', 'series_name');

		/* DATOS DEL GRAFICO */
		$conf_fechas = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle");
		foreach ($conf_fechas as $conf_fecha) {
			$T->setVar('__series_name', $this->timestamp->getFormatoFecha($conf_fecha->getAttribute('fecha')));
			$T->parse('series_name', 'SERIES_NAME', true);
			$T->setVar('point_element', '');
		}

		$T->setVar('series_element', '');
		foreach ($conf_slas as $conf_sla){
			$T->setVar('__point_name',$conf_sla->getAttribute('nombre'));
			$T->setVar('__point_color',$conf_sla->getAttribute('color'));
			$T->setVar('point_element', '');

			foreach ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle/estadisticas/estadistica[@sla_id=".$conf_sla->getAttribute('sla_id')."]") as $tag_dato){
				$T->setVar('__point_value', $tag_dato->getAttribute('porcentaje'));
				$T->parse('point_element', 'POINT_ELEMENT', true);
			}
			$T->parse('series_element', 'SERIES_ELEMENT', true);
		}

		$T->setVar('__label',($conf_fechas->length<7)?"this.value":"(parseInt(this.axis.categories.indexOf(this.value))+1)");
		$T->setVar('__leyenda_label',($conf_fechas->length<7)?"":"leyendaLabel(chart_sla_historico,'leyenda_sla_historico1');");
		$T->setVar('__ancho_leyenda1',($conf_fechas->length<7)?"0":"450");
		$T->setVar('__ancho_leyenda2',($conf_fechas->length<7)?"600":"150");
		$T->setVar('__step', ($conf_fechas->length<7)?1:round(count($conf_fecha->length)/7));

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'));
	}

	/*************** FUNCIONES DE OTROS GRAFICOS ***************/
	/*************** FUNCIONES DE OTROS GRAFICOS ***************/
	/*************** FUNCIONES DE OTROS GRAFICOS ***************/

	function getComparativo($es_descarga) {
		$xpath = $this->getDatosComparativo();
		$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);

		if(!$conf_pasos->length){
			$this->resultado = $this->__generarContenedorSinDatos();
			return;
		}

		$this->botones = $conf_pasos;

		$es_online = true;
		if (isset($this->extra["imprimir"]) and $this->extra["imprimir"] != 0) {
			$pasos = $conf_pasos;
			$es_online = false;
		}
		elseif (isset($this->extra["paso_id"])) {
			$pasos[] = $xpath->query("paso[@paso_orden=".$this->extra["paso_id"]."]", $conf_objetivo)->item(0);
		}
		else {
			$pasos[] = $conf_pasos->item(0);
		}

		// TEMPLATE DEL GRAFICO
		$T =& new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'comparativo.xhtml');
		$T->setBlock('tpl_grafico', 'SERIES_NAME', 'series_name');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT_DISPONIBILIDAD', 'point_element_disponibilidad');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT_RENDIMIENTO', 'point_element_rendimiento');
		$T->setBlock('tpl_grafico', 'BLOQUE_PASOS', 'bloque_pasos');
		
		$T->setVar('__item_orden', $this->extra["item_orden"]);

		$orden = 1;
                 /*cuando es descarga pdf*/
                if($es_descarga==true){
                    $T->setVar('es_descarga', 'true');
                }

		foreach ($pasos as $conf_paso) {
			$paso_elegido = $conf_paso->getAttribute('paso_orden');
			$tag_datos = $count_datos=$xpath->query("//detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/estadisticas/estadistica");

			if ($_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id] > 0) {
				$max_y = $_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id] * 0.75;
			}
			else {
				$max_y = $xpath->evaluate("//detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/estadisticas/estadistica/@respuesta[not(. < //detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/estadisticas/estadistica/@respuesta)][1]")->item(0)->value;
			}

			$T->setVar('__paso_id', $conf_paso->getAttribute('paso_orden'));
			$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
			$T->setVar('__paso_orden', $orden);
			$T->setVar('__tiene_titulo', ($es_online)?"none":"inline");
			$T->setVar('__pointWidth', ($tag_datos->length < 4)?'pointWidth: 70':'');

			$T->setVar('__step', round($tag_datos->length/12));
			$T->setVar('__max_y_rendimiento', (round(($max_y / 3), 2) * 4));
			$T->setVar('__tickInterval_y_rendimiento', round(($max_y / 3), 2));

			$T->setVar('point_element_disponibilidad', '');
			$T->setVar('point_element_rendimiento', '');

			foreach ($tag_datos as $tag_dato) {
				$T->setVar('__series_name',date("Y,(m - 1),d,H,i,s", strtotime($tag_dato->getAttribute('fecha'))));
				$T->parse('series_name', 'SERIES_NAME', true);

				$T->setVar('__point_value_disponibilidad', $tag_dato->getAttribute('uptime'));
				$T->parse('point_element_disponibilidad', 'POINT_ELEMENT_DISPONIBILIDAD', true);

				$T->setVar('__point_value_rendimiento', $tag_dato->getAttribute('respuesta'));
				$T->parse('point_element_rendimiento', 'POINT_ELEMENT_RENDIMIENTO', true);
			}

			$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
			$orden++;
		}
		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), $paso_elegido, $es_online, $es_online);
	}

	// TODO: metodo getElementosPlus()
	// TODO: sacar variables de sesion.
	function getDetalleElementoPlus() {
		if(isset($this->extra["paso_id"])){
			$_SESSION['paso_elemento_plus'] = $this->extra["paso_id"];
			$_SESSION['fecha_monitoreo'] = $this->extra["fecha_monitoreo"];
			$_SESSION['monitor_id'] = $this->extra["monitor_id"];
			$_SESSION['objetivo_id'] = $this->objetivo_id;
		}
		$xpath = $this->getDatosDetalleElementosPlus();
		// TEMPLATE DEL GRAFICO
		$T =& new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		if (isset($_REQUEST['es_pdf'])) {
			$T->setFile('tpl_grafico', 'elementos_plus_print.xhtml');
		}else{
			$T->setFile('tpl_grafico', 'elementos_plus.xhtml');
	    }
		$T->setBlock('tpl_grafico', 'BLOQUE_DATOS', 'bloque_datos');
		$T->setBlock('tpl_grafico', 'BLOQUE_FORMATO', 'bloque_formato');
		$T->setBlock('tpl_grafico', 'BLOQUE_CATEGORIAS', 'bloque_categorias');
		$T->setBlock('tpl_grafico', 'BLOQUE_VARIABLES', 'bloque_variables');
		
		$T->setBlock('tpl_grafico', 'BLOQUE_VARIABLES_FORMATO', 'bloque_variables_formato');
		$T->setBlock('tpl_grafico', 'BLOQUE_VARIABLES_CATEGORIAS', 'bloque_variables_categorias');
		$tag_estadistica = $xpath->query('//detalle[@nodo_id='.$this->extra["monitor_id"].']/estadisticas/estadistica')->item(0);
		//XML de donde provienen los datos para elementos plus
		$tag_datos = $xpath->query('//detalle[@nodo_id='.$this->extra["monitor_id"].']/datos/dato');
		$tag_codigo = $xpath->query('/atentus/resultados/propiedades/codigos/codigo');
		$tag_descripcion = $xpath->query('/atentus/resultados/propiedades/objetivos/objetivo')->item(0);
		$T->setVar('__tiempoTotal', $tag_estadistica->getAttribute('tiempo_total'));
		$T->setVar('__id',$this->timestamp->getFormatearFecha($this->extra['fecha_monitoreo'], "d_m_Y_h_i_s").'_'.$this->extra["monitor_id"].'_'.$this->extra["paso_id"]);
		$tag_data_types=$xpath->query('//detalle[@nodo_id='.$this->extra["monitor_id"].']/datos/dato');
		$contador=1;
		$contador_total_archivos = 0;
		$cuenta_tipo = array();
		$tipo_archivo = array();
		$cuenta_status = array();
		$tipo_status = array();
		$datos_categoria_ok = array();
		$datos_categoria_redireccion = array();
		$datos_categoria_error = array();
		$datos_categoria_exclusion= array();
		foreach($tag_data_types as $tag_data_type){
			$status= $tag_data_type->getAttribute('status');
			$content_type = $tag_data_type->getAttribute('content_type');
			$contenido_formato = $tag_data_type->getAttribute('content_type');
			$status_data= $tag_data_type->getAttribute('status');
			$url_data= $tag_data_type->getAttribute('url');
			if ($contenido_formato==""){
				$contenido_formato="No_capturado";
		    }
			if($content_type=="text/html" ){
				$content_type="Html";
			}
			elseif(strtoupper(strpos($content_type,'javascript'))){
				$content_type="Javascript";

			}
			elseif(strtoupper(strpos($content_type,'TEXT/JAVASCRIPT'))){
				$content_type="Text/Javascript";
			}
			elseif(strtoupper(strpos($content_type,'mage'))  || strtoupper(strpos($content_type,'vector/x-svf')) ){
				$content_type="Img";
			}
			elseif(strtoupper(strpos($content_type,'css')) || strtoupper(strpos($content_type,'dsssl')) ){
				$content_type="Css";
			}
			else{
				$content_type="Otro";
			}
			if($status=="200" || $status=="204" || $status=="0" || $status=="201" || $status=="202" || $status=="203" || $status=="205" || $status=="206" || $status=="207" || $status=="208"){
				$status="OK";
				array_push($datos_categoria_ok,($url_data." ".$contenido_formato." ".$status_data." ".$status));
			}
			if( $status=="300" || $status=="301" || $status=="302" || $status=="303" || $status=="304" || $status=="305" || $status=="306" || $status=="307" || $status=="308"  ){
				$status="Redirección";
				array_push($datos_categoria_redireccion,($url_data." ".$contenido_formato." ".$status_data." ".$status));
			}
			if($status=="400" || $status=="401" || $status=="402" || $status=="403" || $status=="404" || $status=="405" || $status=="406" || $status=="407" || $status=="408" || $status=="409" || $status=="410" || $status=="411" || $status=="412" || $status=="413" || $status=="414" || $status=="415" || $status=="416" || $status=="417"  ||  $status=="418"  || $status=="422"  || $status=="423" || $status=="424" || $status=="425" || $status=="426" || $status=="428" || $status=="429"  || $status=="431" || $status=="439" || $status=="449" || $status=="451" || $status=="500" || $status=="501" || $status=="502" || $status=="503" || $status=="504" || $status=="505"||  $status=="506" ||  $status=="507" ||  $status=="508" ||  $status=="509" ||  $status=="510" ||  $status=="511" ||  $status=="512" || $status=="520" || $status=="521" || $status=="522" || $status=="1001" || $status=="1002" || $status=="1003" ||$status=="1004" || $status=="1005" || $status=="1006" || $status=="1999" || $status=="999999") {
				$status="Error";
				array_push($datos_categoria_error, $url_data." ".$contenido_formato." ".$status_data." ".$status);
			}
			if ($status=="1000" || $status=="1007"){
				$status="Exclusión";
				array_push($datos_categoria_exclusion, $url_data." ".$contenido_formato." ".$status_data." ".$status);
			}
			if($content_type==""){
				$content_type='No capturado';
			}
			array_push($tipo_archivo, $content_type);
			$cuenta_tipo[$content_type] += $contador;
			$T->setVar('__tipo_archivo',$content_type);
			array_push($tipo_status, $status);
			$cuenta_status[$status] += $contador;
			$T->setVar('__tipo_status',$status);
			$contador_total_archivos++;
		}
		$array_unico = array_unique($tipo_archivo);
		$array_status = array_unique($tipo_status);
		$T->setVar('bloque_formato','');
		foreach($array_unico as $tipo_archivo_unico){
			if($tipo_archivo_unico=='Html'){
				$color_formato="#3561CE";
			}
			if($tipo_archivo_unico=='Javascript'){
				$color_formato="#f4e74f";
			}
			if($tipo_archivo_unico=='Text/Javascript'){
				$color_formato="#ec7911";
			}
			if($tipo_archivo_unico=='Css'){
				$color_formato="#8aa817";
			}
			if($tipo_archivo_unico=='Img'){

				$color_formato="#a64ef4";
			}
			if($tipo_archivo_unico=='Otro'){
				$color_formato="#6e6e6e";
			}
			$porcen_archivo=($cuenta_tipo[$tipo_archivo_unico]/$contador_total_archivos)*100;
			$T->setVar('__porcen_archivo',round($porcen_archivo,1));
			$T->setVar('__nombre_formato',$tipo_archivo_unico);
			$T->setVar('__color_formato',$color_formato);
			$T->parse('bloque_formato','BLOQUE_FORMATO',true);
		}
		$T->parse('bloque_variables_formato','BLOQUE_VARIABLES_FORMATO',true);
		$T->setVar('bloque_categorias','');
		foreach($array_status as $tipo_categoria_status){
			if($tipo_categoria_status=="OK"){
				$color="#4caf50";
				$data='<div  style=\"overflow:scroll; width:auto; height:190px; z-index:3;\" class=\"contenedor_tooltip;\"  > <p> Lista de elementos en estado: '.$tipo_categoria_status.'</p><table border=\"1\" cellpadding=\"3\" style=\"table-layout:fixed; border-top:2px solid black; text-align:center;\" class=\"tabla_tooltip\"><tr style=\"background-color:#bbc3ff; font-size:11px; color:black; border-right-color:#bbc3ff;  \"><th>URL</th><th>Formato</th><th>Cod Status</th ><th >Status</th></tr>';
				foreach($datos_categoria_ok as $datos_categorias_ok){
					list($url,$formato,$num_status,$nom_status)=split('[ ]',$datos_categorias_ok);
					$formato_definido=str_replace("_"," ",$formato);
					$data .='<tr class=\"tr_tooltip\" style=\"font-size:10px;\"  ><td  style=\" max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; padding:7px 7px 7px 7px;\"  ><a href='.$url.' target=\"_blank\" title='.$url.' style=\"font-size:10px;\" >'.$url.'</a></td><td style=\" max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; padding:7px 7px 7px 7px; \" title='.$formato_definido.' >'.$formato_definido.'</td><td style=\"padding:7px 7px 7px 7px;\">'.$num_status.'</td><td style=\"padding:7px 7px 7px 7px;\">'.$nom_status.'</td></tr>';
				}
				$data .="</table></div>";
				$data=str_replace("'", "", $data);
				$T->setVar('__data',$data);
			}
			if($tipo_categoria_status=="Redirección"){
				$color="#FFB347";
				$data='<div  style=\"overflow:scroll; width:auto; height:190px; z-index:3;\" class=\"contenedor_tooltip;\"  > <p> Lista de elementos en estado: '.$tipo_categoria_status.'</p><table border=\"1\" cellpadding=\"3\" style=\"table-layout:fixed; border-top:2px solid black; text-align:center;\" class=\"tabla_tooltip\"><tr style=\"background-color:#bbc3ff; font-size:11px; color:black; border-right-color:#bbc3ff;  \"><th>URL</th><th>Formato</th><th>Cod Status</th ><th >Status</th></tr>';
				foreach($datos_categoria_redireccion as $datos_categorias_redireccion){
					list($url,$formato,$num_status,$nom_status)=split('[ ]',$datos_categorias_redireccion);
					$formato_definido=str_replace("_"," ",$formato);
					$data .='<tr class=\"tr_tooltip\" style=\"font-size:10px;\"  ><td  style=\" max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; padding:7px 7px 7px 7px;\"  ><a href='.$url.' target=\"_blank\" title='.$url.' style=\"font-size:10px;\" >'.$url.'</a></td><td style=\" max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; padding:7px 7px 7px 7px; \" title='.$formato_definido.' >'.$formato_definido.'</td><td style=\"padding:7px 7px 7px 7px;\">'.$num_status.'</td><td style=\"padding:7px 7px 7px 7px;\">'.$nom_status.'</td></tr>';
					if($formato=="" || $formato==null){
						$formato="No capturado";
					}
				}
				$T->setVar('__data',$data);
			}
			if($tipo_categoria_status=="Error"){
				$color="#F44336";
				$data='<div  style=\"overflow:scroll; width:auto; height:190px; z-index:3;\" class=\"contenedor_tooltip;\"  ><p> Lista de elementos en estado: '.$tipo_categoria_status.'</p><table border=\"1\" cellpadding=\"3\" style=\"table-layout:fixed; border-top:2px solid black; text-align:center;\" class=\"tabla_tooltip\"><tr style=\"background-color:#bbc3ff; font-size:11px; color:black; border-right-color:#bbc3ff;  \"><th>URL</th><th>Formato</th><th>Cod Status</th ><th >Status</th></tr>';
				foreach($datos_categoria_error as $datos_categorias_error){
					list($url,$formato,$num_status,$nom_status)=split('[ ]',$datos_categorias_error);
					$formato_definido=str_replace("_"," ",$formato);
					$data .='<tr class=\"tr_tooltip\" style=\"font-size:10px;\"  ><td  style=\" max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; padding:7px 7px 7px 7px;\"  ><a href='.$url.' target=\"_blank\" title='.$url.' style=\"font-size:10px;\" >'.$url.'</a></td><td style=\" max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; padding:7px 7px 7px 7px; \" title='.$formato_definido.' >'.$formato_definido.'</td><td style=\"padding:7px 7px 7px 7px;\">'.$num_status.'</td><td style=\"padding:7px 7px 7px 7px;\">'.$nom_status.'</td></tr>';
				}
				$data .="</table></div>";
				$T->setVar('__data',$data);
			}
			if($tipo_categoria_status=="Exclusión"){
				$color="#673AB7";
				$data='<div  style=\"overflow:scroll; width:auto; height:190px; z-index:3;\" class=\"contenedor_tooltip;\"  ><p> Lista de elementos en estado: '.$tipo_categoria_status.'</p><table border=\"1\" cellpadding=\"3\" style=\"table-layout:fixed; border-top:2px solid black; text-align:center;\" class=\"tabla_tooltip\"><tr style=\"background-color:#bbc3ff; font-size:11px; color:black; border-right-color:#bbc3ff;  \"><th>URL</th><th>Formato</th><th>Cod Status</th ><th >Status</th></tr>';
				foreach($datos_categoria_exclusion as $datos_categorias_exclusion){
					list($url,$formato,$num_status,$nom_status)=split('[ ]',$datos_categorias_exclusion);
					$formato_definido=str_replace("_"," ",$formato);
					$data .='<tr class=\"tr_tooltip\" style=\"font-size:10px;\"  ><td  style=\" max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; padding:7px 7px 7px 7px;\"  ><a href='.$url.' target=\"_blank\" title='.$url.' style=\"font-size:10px;\" >'.$url.'</a></td><td style=\" max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; padding:7px 7px 7px 7px; \" title='.$formato_definido.' >'.$formato_definido.'</td><td style=\"padding:7px 7px 7px 7px;\">'.$num_status.'</td><td style=\"padding:7px 7px 7px 7px;\">'.$nom_status.'</td></tr>';
				}
				$data .="</table></div>";
				$T->setVar('__data',$data);
			}
			$porcen_status=($cuenta_status[$tipo_categoria_status]/$contador_total_archivos)*100;
			$cantidad_status=$cuenta_status[$tipo_categoria_status]."/".$contador_total_archivos;
			$reporte_id= $_REQUEST["reporte_id"];
			$T->setVar('__porcen_status',round($porcen_status,1));
			$T->setVar('__nombre_status',$tipo_categoria_status);
			$T->setVar('__color',$color);
			$T->setVar('__cantidad_status',$cantidad_status);
			$T->setVar('__reporte',$reporte_id);
			$T->parse('bloque_categorias','BLOQUE_CATEGORIAS',true);
		}
		$T->parse('bloque_variables_categorias','BLOQUE_VARIABLES_CATEGORIAS',true);
		/*datos para csv*/
		$T->setVar('__objetivoId',$this->objetivo_id);
		$T->setVar('__servicio',$tag_descripcion->getAttribute('servicio'));
		$T->setVar('__objetivoNombre',$tag_descripcion->getAttribute('nombre'));
		$T->setVar('__fecha', $this->extra["fecha_monitoreo"]);
		$T->setVar('bloque_datos','');
		foreach ($tag_datos as $tag_dato) {
			if ($tag_dato->getAttribute('url') == '') {
				$url_validado = 'NO URL';
			}else{
				$url_validado = $tag_dato->getAttribute('url');
			}
			$contador++;
			$T->setVar('__elementos', str_replace(array('\'', '"'), '', $url_validado));
			$T->setVar('__ip', $tag_dato->getAttribute('ip'));
			$T->setVar('__dns', $tag_dato->getAttribute('tiempo_dns'));
			$T->setVar('__latencia', $tag_dato->getAttribute('latencia'));
			$T->setVar('__descarga', $tag_dato->getAttribute('descarga'));
			$T->setVar('__espera', $tag_dato->getAttribute('espera'));
			$T->setVar('__tamanoBody', $tag_dato->getAttribute('tamano_cuerpo'));
			$T->setVar('__tamanoHeader', $tag_dato->getAttribute('tamano_cabecera'));
			$T->setVar('__contentType', str_replace(array('\'', '"'), '', $tag_dato->getAttribute('content_type')));
			$T->setVar('__estado', $tag_dato->getAttribute('status'));
			$T->setVar('__es_ok', $tag_dato->getAttribute('es_ok'));
			foreach ($tag_codigo as $tag_codigos){
				if($tag_codigos->getAttribute('codigo_id')==$tag_dato->getAttribute('status')){
					$T->setVar('__nombre_estado', $tag_codigos->getAttribute('nombre'));
				}
			}
			$T->parse('bloque_datos','BLOQUE_DATOS',true);
		}	
		$T->parse('bloque_variables','BLOQUE_VARIABLES',true);
		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'));
	}
	function getEspecialDisponibilidadGlobalResumen() {
		$xpath = $this->getDatosDetalladoDisponibilidad();
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=0]");
		$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");

		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'especial_disponibilidad_global_resumen.xhtml');
		$T->setBlock('tpl_grafico', 'SERIES_NAME', 'series_name');
  		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
  		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');

		$es_online = true;
		if (isset($this->extra["imprimir"]) and $this->extra["imprimir"] != 0) {
			$es_online = false;
		}

		$T->setVar('__returnNull', ($conf_pasos->length < 8)?'':'return null;');
		$T->setVar('__pointWidth', ($conf_pasos->length < 4)?'pointWidth: 70':'');
		$T->setVar('__label', ($conf_pasos->length < 7)?'this.value':'(parseInt(this.axis.categories.indexOf(this.value))+1)');
		$T->setVar('__leyenda_label', (($conf_pasos->length < 7)?"":"leyendaLabel(chart_disponibilidad_resumen_global,'leyenda_disponibilidad_resumen_global1');"));
		$T->setVar('__step', ($conf_pasos->length < 7)?1:round($conf_pasos->length/7));
		$T->setVar('__ancho_leyenda1',"600");

		$T->setVar('series_name', '');
		foreach ($conf_pasos as $conf_paso) {
			$T->setVar('__series_name', $conf_paso->getAttribute('nombre'));
			$T->parse('series_name', 'SERIES_NAME', true);
		}

		$T->setVar('series_element', '');
		for ($i = $conf_eventos->length; $i > 0; $i--) {
			$conf_evento = $conf_eventos->item($i - 1);
			$T->setVar('__point_name', $conf_evento->getAttribute('nombre'));
			$T->setVar('__serie_color', $conf_evento->getAttribute('color'));

			$T->setVar('point_element', '');
			foreach ($conf_pasos as $conf_paso) {
				$dato = $xpath->query("//detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]");
				$porcentaje = ($dato->length == 0)?"0":$dato->item(0)->getAttribute("porcentaje");
				$T->setVar('__point_value', $porcentaje);
				$T->parse('point_element', 'POINT_ELEMENT', true);
			}
			$T->parse('series_element', 'SERIES_ELEMENT', true);
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), 0, $es_online);
	}
	function getEspecialErroresPorNodo() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		
		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.errores(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."','".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row["errores"]);
		$xpath = new DOMXpath($dom);
		
		$conf_objetivo = $xpath->query('/atentus/resultados/propiedades/objetivos/objetivo')->item(0);
		$conf_pasos = $xpath->query('paso[@visible=1]', $conf_objetivo);
		$conf_nodos = $xpath->query('/atentus/resultados/propiedades/nodos/nodo[@nodo_id!=0]');

		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'especial_errores_por_nodo.xhtml');

		$T->setBlock('tpl_grafico', 'ANCHO_SERIE', 'ancho_serie');
		$T->setBlock('tpl_grafico', 'SERIES_NAME', 'series_name');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');

		$T->setVar('__returnNull', ($conf_pasos->length < 8)?'':'return null;');
		$T->setVar('__pointWidth', ($conf_pasos->length < 4)?'pointWidth: 70,':'');
		$T->setVar('__label', ($conf_pasos->length < 8)?'this.value':'(parseInt(this.axis.categories.indexOf(this.value))+1)');
		$T->setVar('__leyenda_label', (($conf_pasos->length < 8)?"":"leyendaLabel(chart_errores_isp,'leyenda_errores_isp');"));
		$T->setVar('__step', ($conf_pasos->length < 8)?1:round($conf_pasos->length/7));
		$T->setVar('__ancho_leyenda1',"600");

		$T->setVar('series_name', '');
		foreach ($conf_pasos as $conf_paso) {
			$T->setVar('__series_name', $conf_paso->getAttribute('nombre'));
			$T->parse('series_name', 'SERIES_NAME', true);
		}
		
		$T->setVar('series_element', '');
		foreach ($conf_nodos as $key => $conf_nodo) {
			$T->setVar('__point_name', $conf_nodo->getAttribute('nombre'));
			$T->setVar('__serie_color', Utiles::getDefaultColor($key));
			
			$T->setVar('point_element', '');
			foreach ($conf_pasos as $conf_paso) {
				$dato = $xpath->query("//detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/detalles/detalle[@nodo_id=".$conf_nodo->getAttribute("nodo_id")."]");
				$porcentaje = ($dato->length == 0)?"0":$dato->item(0)->getAttribute("porcentaje");
				$T->setVar('__point_value', $porcentaje);
				$T->parse('point_element', 'POINT_ELEMENT', true);
			}
			$T->parse('series_element', 'SERIES_ELEMENT', true);
		}

		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), $conf_objetivo->getAttribute('objetivo_id'));
	}
	
	function getEspecialDisponibilidadFullObjetivos() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
	
		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT _propiedad_objetivo, ".
				"(date_trunc('hour', fecha_inicio) + date_part('minute', fecha_inicio)::int / 5 * interval '5 min') as fecha_inicio, ".
				"(date_trunc('hour', fecha_termino) + date_part('minute', fecha_termino)::int / 5 * interval '5 min') as fecha_termino ".
				"FROM (".
				"SELECT public._to_cliente_tz(".pg_escape_string($current_usuario_id).", now() - (SELECT preferencia_semaforo_periodo FROM public.cliente_usuario WHERE cliente_usuario_id =".pg_escape_string($current_usuario_id).")) as fecha_inicio,".
				"public._to_cliente_tz(".pg_escape_string($current_usuario_id).", now()) as fecha_termino ".
				") as foo, ".
				"reporte._propiedad_objetivo(".
				pg_escape_string($current_usuario_id).", ".
				"public._objetivos(".pg_escape_string($current_usuario_id)."), '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."')";
//		print($sql);
		$res = & $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql,$res->userinfo);
			exit();
		}
	
		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row['_propiedad_objetivo']);
		$xpath = new DOMXpath($dom);
	
		$conf_objetivos = $xpath->query("/objetivos/objetivo");
		$this->botones = $conf_objetivos;
	
		if (isset($this->extra["objetivo_id"])) {
			$conf_objetivo = $xpath->query("/objetivos/objetivo[@objetivo_id=".$this->extra["objetivo_id"]."]")->item(0);
		}
		else {
			$conf_objetivo = $conf_objetivos->item(0);
		}
	
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$objetivo_elegido = $conf_objetivo->getAttribute('objetivo_id');
		
		if ($conf_pasos->length == 0) {
			return $this->__generarContenedorSVG($this->__generarContenedorSinDatos(), $this->extra["objetivo_id"], true);
		}
		
		$this->objetivo_id = $conf_objetivo->getAttribute('objetivo_id');
		$this->timestamp->tipo_periodo = "especial";
		$this->timestamp->fecha_inicio = $row['fecha_inicio'];
		$this->timestamp->fecha_termino = $row['fecha_termino'];
		
		$xpath = $this->getDatosDisponibilidadGlobal();
	
		//	TEMPLATE DEL GRAFICO
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_por_objetivo.xhtml');
	
		$T->setBlock('tpl_grafico', 'BLOQUE_RESOURCES', 'bloque_resources');
		$T->setBlock('tpl_grafico', 'BLOQUE_GRUPOS', 'bloque_grupos');
		$T->setBlock('tpl_grafico', 'BLOQUE_OBJETIVOS', 'bloque_por_objetivo');
	
		$T->setVar('__link_eventos', 'f');
		$T->setVar('__objetivo_id', $conf_objetivo->getAttribute('objetivo_id'));
	
		$T->setVar('bloque_resources', '');
		$T->setVar('bloque_grupos', '');
		foreach ($conf_pasos as $conf_paso) {
			$T->setVar('__resource_id', $conf_objetivo->getAttribute('objetivo_id')."-".$conf_paso->getAttribute('paso_orden'));
			$T->setVar('__resource_name', $conf_paso->getAttribute('nombre'));
			$T->setVar('__resource_parent', $conf_objetivo->getAttribute('objetivo_id'));
			$T->parse('bloque_resources', 'BLOQUE_RESOURCES', true);
	
			$T->setVar('__grupo_id', $conf_objetivo->getAttribute('objetivo_id')."-".$conf_paso->getAttribute('paso_orden'));
			$T->parse('bloque_grupos', 'BLOQUE_GRUPOS', true);
	
			$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato");
			$datos = $datos."'".$conf_objetivo->getAttribute('objetivo_id')."-".$conf_paso->getAttribute('paso_orden')."':[";
			if ($tag_datos->length > 0) {
				foreach ($tag_datos as $tag_dato) {
					$datos = $datos."['".date('Y.m.d.H.i.s', strtotime($tag_dato->getAttribute('inicio')))."','".
							date('Y.m.d.H.i.s', strtotime($tag_dato->getAttribute('termino')))."','".
							Utiles::getStyleDisponibilidad($tag_dato->getAttribute('evento_id'))."'],";
				}
			}
			else {
				$datos = $datos."['".$this->timestamp->getInicioPeriodo("Y.m.d.H.i.s")."','".
						$this->timestamp->getTerminoPeriodo("Y.m.d.H.i.s")."','".
						Utiles::getStyleDisponibilidad(7)."'],";
			}
			$datos = $datos."],";
		}
	
		$T->setVar('__datos', $datos);
		$T->parse('bloque_por_objetivo', 'BLOQUE_OBJETIVOS', true);
		$this->tiempo_expiracion = 300;
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'), $objetivo_elegido, true);
	}

	public function getEspecialDisponibilidadObjetivoPaso() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$es_online = (isset($this->extra["imprimir"]) and $this->extra["imprimir"] != 0)?false:true;

		$event = new Event;
		$graficoSvg = new GraficoSVG();
		$usr = new Usuario($current_usuario_id);
		$usr->__Usuario();
		
		# Variables para eventos especiales marcados por el cliente codigo 9.
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
		$timeZone = $arrTime[$timeZoneId];
		$nameFunction = 'EspecialDisponibilidadObjetivoPasoGrafico';
		$dataMant = null;
		$marcado = false;
		$ids = null;

		//  TEMPLATE DEL GRAFICO
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'especial_disponibilidad_objetivo_paso.xhtml');
		
		$T->setBlock('tpl_grafico', 'BLOQUE_RESOURCES', 'bloque_resources');
		$T->setBlock('tpl_grafico', 'BLOQUE_GRUPOS', 'bloque_grupos');
		$T->setBlock('tpl_grafico', 'BLOQUE_MONITORES', 'bloque_monitores');

		$T->setVar('__item_orden', $this->extra["item_orden"]);
		$T->setVar('__link_eventos', ($this->extra["imprimir"])?'f':'t');
		$T->setVar('__monitor_id', '0');
		$T->setVar('__monitor_nombre', null);
		$T->setVar('__monitor_orden', null);
		$T->setVar('__tiene_titulo', "none");
		$T->setVar('__tipo_id', 'consolidada_monitor_');

		$T->setVar('bloque_resources', '');
		$T->setVar('bloque_grupos', '');

		$config_especial = new ConfigEspecial($this->objetivo_id);
		$datos_paso = new stdClass();

		foreach ($config_especial->__objetivos as $key=>$objetivo) {
			foreach ($objetivo->__pasos as $paso) {
				if (is_numeric($paso->paso_id)) {
					$sql = "SELECT * FROM reporte.disponibilidad_detalle_global_objetivopaso(".
							pg_escape_string($current_usuario_id).", ".
							pg_escape_string($objetivo->objetivo_id).", ".
							pg_escape_string($paso->paso_id).", ".
							pg_escape_string($this->horario_id).", '".
							pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
							pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

					$res = & $mdb2->query($sql);
					if (MDB2::isError($res)) {
						$log->setError($sql, $res->userinfo);
						exit();
					}
					$row = $res->fetchRow();
					if ($row != null){
						$dom = new DomDocument();
					 	$dom->preserveWhiteSpace = FALSE;
			 		 	$dom->loadXML($row['disponibilidad_detalle_global_objetivopaso']);
			 		 	$xpath = new DOMXpath($dom);
						# Busca si exiten marcados y los almacena.
						foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado_objetivo) {
							$ids = $ids.','.$tag_marcado_objetivo->getAttribute('mantenimiento_id');
							$marcado = true;
						}
						unset($row);	
						$paso_info = $xpath->query('//propiedades/objetivos/objetivo[@objetivo_id='.$objetivo->objetivo_id.']/paso[@paso_orden='.$paso->paso_id.']')->item(0);

						if (is_object($paso_info)) {
							$T->setVar('__resource_id', $objetivo->objetivo_id."-".$paso->paso_id);
							$T->setVar('__resource_name', $paso_info->getAttribute('nombre'));
							
							$T->parse('bloque_resources', 'BLOQUE_RESOURCES', true);
							
							$T->setVar('__grupo_id', $objetivo->objetivo_id."-".$paso->paso_id);
							$T->parse('bloque_grupos', 'BLOQUE_GRUPOS', true);
							$tiempos_paso = array();
							foreach ($xpath->query('//detalles/detalle/detalles/detalle[@paso_orden='.$paso->paso_id.']/datos/dato') as $dato) {
								$tiempos_paso[]= array(
										date('Y.m.d.H.i.s', strtotime($dato->getAttribute('inicio'))),
										date('Y.m.d.H.i.s', strtotime($dato->getAttribute('termino'))),
										Utiles::getStyleDisponibilidad($dato->getAttribute('evento_id'))
								);
							}
							$datos_paso->{$objetivo->objetivo_id."-".$paso->paso_id}= $tiempos_paso;
						}
					}
				}
			}
		}
		# Obetener los datos de mantenimiento.
		if ($marcado == true) {
			$dataMant =$event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			# Guarda los objetivos del marcado.
			foreach ($dataMant as $key => $value) {					
				$objetives[$key] = explode(',',str_replace($character,"",($value['objetivo_id'])));
			}
			$encode = json_encode($dataMant);
			$tieneEvento = 'true';
			$T->setVar('__tiene_evento', $tieneEvento);
			$T->setVar('__name', $nameFunction);
			
		}
		$T->setVar('__datos', json_encode($datos_paso));
		$T->parse('bloque_monitores', 'BLOQUE_MONITORES', true);
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'));
		if (count($dataMant)>0){
			$this->resultado.= $this->getAccordion($encode, $nameFunction);
		}
	}
	
	
	function getEspecialUptimePonderadaPorItem() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
	
		$objetivo_especial =new ConfigEspecial($this->extra['parent_objetivo_id']);
		$obj=$objetivo_especial->__objetivos[$this->objetivo_id];
		$metas =$obj->__metas[$this->objetivo_id];
		$datos= array();
		
		//TRAE LAS PONDERACIONES DEL OBJETIVO ESPECIAL
		$ponderaciones_horas = $objetivo_especial->getPonderaciones();
	
		$array_pasos=array();
		$array_codigos=null;
		foreach ($ponderaciones_horas as $ponderaciones_hora){
				
			$ponderacion = "array['".pg_escape_string($ponderaciones_hora->valor_ponderacion)."','".
					pg_escape_string($ponderaciones_hora->hora_inicio)."','".
					pg_escape_string($ponderaciones_hora->hora_termino)."']";
				
				
			$sql = "SELECT * FROM reporte.disponibilidad_uptime_poritem(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					$ponderacion.",' ".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
// 			  			echo $sql.'<br>';//exit;
				
			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
				
			if($row = $res->fetchRow()){
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_uptime_poritem']);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_uptime_poritem"]);
			}
				
				
			$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
			$conf_datos=$xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$conf_objetivo->getAttribute('objetivo_id')."]/detalles/detalle");
				

	
			if($array_codigos==null){
				$conf_codigos= $xpath->query("/atentus/resultados/propiedades/eventos/evento");
				foreach ($conf_codigos as $conf_codigo){
						
					$array_codigos[$conf_codigo->getAttribute('evento_id')]=$conf_codigo->getAttribute('color');
				}
				
			}
			foreach ($conf_pasos as $conf_paso){
				$paso=$conf_paso->getAttribute('paso_orden');
				foreach ($conf_datos as $conf_fecha){
					$fecha = date('Y/m/d',strtotime($conf_fecha->getAttribute('fecha')));
					
					if($xpath->query("datos/dato[@paso_orden=".$paso."]",$conf_fecha)->length==0){
						$array_pasos[$paso][$fecha]['eficiencia']+=0;
						$array_pasos[$paso][$fecha]['horas']+=0;
					}else{
						$tag_paso = $xpath->query("datos/dato[@paso_orden=".$paso."]",$conf_fecha)->item(0);
						if($array_pasos[$paso][$fecha]==null){
							$array_pasos[$paso][$fecha]['eficiencia']=0;
							$array_pasos[$paso][$fecha]['horas']=0;
						}
						$array_pasos[$paso][$fecha]['fecha']=$fecha;
						$array_pasos[$paso][$fecha]['horas']=$array_pasos[$paso][$fecha]['horas']+1;
						
						$array_pasos[$paso][$fecha]['eficiencia']=$array_pasos[$paso][$fecha]['eficiencia']+($tag_paso->getAttribute('eficiencia'));
					}
				}
			}
				
		}
		
		//	TEMPLATE DEL GRAFICO
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_uptime_ponderado.tpl');
 		$T->setBlock('tpl_grafico', 'BLOQUE_ACUMULADO', 'bloque_acumulado');
		$T->setBlock('tpl_grafico', 'BLOQUE_EFICIENCIA', 'bloque_eficiencia');
		$T->setBlock('tpl_grafico', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_grafico', 'BLOQUE_FECHA_EFICIENCIA', 'bloque_fecha_eficiencia');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
	
		$muestra = true;
		// DATOS DEL GRAFICO
		$T->setVar('series_element', '');
		$T->setVar('bloque_pasos', '');
		foreach ($conf_pasos as $conf_paso) {
			$acumudalo=0;
			$dias = 1;
			$T->setVar('__series_id', $conf_paso->getAttribute('nombre'));
			$T->setVar('__series_name',$conf_paso->getAttribute('nombre'));
			if ($conf_paso->getAttribute('paso_orden') == 1) {
				$color_grafico = 28;
				
			}elseif($conf_paso->getAttribute('paso_orden') == 3) {
				$color_grafico = 29;
				
			}else{
				$color_grafico = $conf_paso->getAttribute('paso_orden');
			}
			$T->setVar('__series_color', Utiles::getDefaultColor($color_grafico));
			$arr_pasos =$array_pasos[$conf_paso->getAttribute('paso_orden')];
			
			$T->setVar('__pasos',$conf_paso->getAttribute('nombre'));
			
			$T->setVar('point_element', '');
			$T->setVar('bloque_eficiencia', '');
			$T->setVar('bloque_acumulado', '');
			
			foreach ($arr_pasos as $arr_paso){
				$T->setVar('__point_name', date("Y,(m-1),d", strtotime($arr_paso['fecha'])));
				$T->setVar('__point_value',number_format($arr_paso['eficiencia'], 2, ',', ''));
				$acumudalo += $arr_paso['eficiencia'];
				if($muestra){
					$T->setVar('_dia_eficiencia',date('d/m/Y',strtotime($arr_paso['fecha'])));
					$T->parse('bloque_fecha_eficiencia', 'BLOQUE_FECHA_EFICIENCIA', true);
				}
				
				$comparar_color= number_format($arr_paso['eficiencia'], 2, '.', '');
				if($comparar_color>=$metas->indicador_uptime){
					$color = $array_codigos[1];
				}elseif($comparar_color>=$metas->indicador_dparcial && $comparar_color<$metas->indicador_uptime){
					$color = $array_codigos[3];
				}else{
					$color = $array_codigos[2];
				}
				
				$T->setVar('__evento_color',$color);
				$T->setVar('__eficiencia',number_format($arr_paso['eficiencia'], 2, ',', ''));
				$T->setVar('__acumulado',number_format($acumudalo/$dias, 2, ',', ''));
				array_push($datos,floatval($arr_paso['eficiencia']));
				$dias +=1;
				$T->parse('point_element', 'POINT_ELEMENT', true);
				$T->parse('bloque_eficiencia', 'BLOQUE_EFICIENCIA', true);
				$T->parse('bloque_acumulado', 'BLOQUE_ACUMULADO', true);
			}
			$muestra=false;
			
			$T->parse('series_element', 'SERIES_ELEMENT', true);
			$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
			
		}
		
		$max = max($datos);
		$min = min($datos);
		$ymin =ROUND($min-(($max-$min)*0.5));
		
		$T->setVar('__y_scale_min',$ymin<0?0:$ymin);
		$T->setVar('__y_scale_max',ROUND($max));

		$this->tiempo_expiracion = (strtotime($xpath->query("//fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("//fecha")->item(0)->nodeValue));
		$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'));
	}
	/*
	Creado por: Aldo Cruz Romero
	Fecha de creacion:4-10-2019
	Fecha de ultima modificacion:
	*/
	function getPerformance(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$usuario = new Usuario($current_usuario_id);
		$usuario->__Usuario();
		$objetivo=new ConfigEspecial($this->objetivo_id);
		$objetivos=($objetivo->__objetivos);
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'performance.xhtml');
		$T->setBlock('tpl_grafico', 'BLOQUE_OBJETIVOS', 'bloque_objetivos');
		$T->setBlock('tpl_grafico', 'BLOQUE_NODOS', 'bloque_nodos');
		$T->setBlock('tpl_grafico', 'BLOQUE_OBJETIVOS2', 'bloque_objetivos2');
		
		$T->setVar('__user',$usuario->usuario_id);
		foreach ($objetivos as $id => $valueObjetivo) {
			$nodes=(split(",",$valueObjetivo->nodos));
			$T->setVar('__nombre_objetivo',$valueObjetivo->nombre);
			$T->setVar('__id',$id);
			$T->setVar('__paso',$valueObjetivo->paso);
			$T->setVar('__inicio', explode(" ",$this->timestamp->getInicioPeriodo())[0]);
			$T->setVar('__termino',explode(" ",$this->timestamp->getTerminoPeriodo())[0]);
			$T->setVar('__sla_p',$valueObjetivo->sla_p);
			$T->setVar('__global',$valueObjetivo->global);
			$T->setVar('__sla_e',$valueObjetivo->sla_e);
			$T->setVar('__max',$valueObjetivo->max);
			$T->setVar('__nodos',$valueObjetivo->nodos);
			foreach ($nodes as $key => $value) {
				$T->setVar('__monitor',$value);
				$T->parse('bloque_nodos', 'BLOQUE_NODOS', true);
			}
			$T->parse('bloque_objetivos2', 'BLOQUE_OBJETIVOS2', true);
			$T->parse('bloque_objetivos', 'BLOQUE_OBJETIVOS', true);
		}
		$this->resultado = $T->parse('out', 'tpl_grafico');
	}

	/*
	Metodo clonado de getComparativoBanco
	Creado por: Aldo Cruz Romero
	Fecha de creacion:16-12-2019
	Fecha de ultima modificacion: 25-07-2019
	*/
	
	function getComparativoBancoChile() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		$usuario = new Usuario($current_usuario_id);
		$usuario->__Usuario();

		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'comparativo_banco_chile.xhtml');
		
		$T->setBlock('tpl_grafico', 'BLOQUE_OBJETIVOS2', 'bloque_objetivos2');
		$T->setBlock('tpl_grafico', 'BLOQUE_OBJETIVOS', 'bloque_objetivos');
		$T->setBlock('tpl_grafico', 'BLOQUE_NODOS', 'bloque_nodos');
		$T->setBlock('tpl_grafico', 'BLOQUE_GRUPO', 'bloque_grupo');		

		$objetivoEspecial=new ConfigEspecial($this->objetivo_id);

		$T->setVar('bloque_grupo', '');
		$T->setVar('bloque_objetivos2', '');
		$cont_print=1;
		foreach($objetivoEspecial->__grupos as $grupo){
			if($cont_print!=count($objetivoEspecial->__grupos)){
				$T->setVar('__break', "page-break-after: always;");
			}else{
				$T->setVar('__break', "");
			}
			$cont_print++;
			$T->setVar('__nombre_grupo', $grupo['nombre']);
			$objetivoArray=Array();
			$primero = true;
			$sql_objetivos = "ARRAY[";
			$steps = Array();
			$pasos='';
			$first = true;
			$sql_obj = "ARRAY[";
			
			foreach ($grupo['objetivos'] as $subobjetivo) {

				$objetivo = new Objetivo($subobjetivo->objetivo_id);
				
				array_push($objetivoArray, $objetivo->objetivo_id);
				$T->setVar('__nombre_objetivo', $objetivo->nombre_empresa);
				$T->setVar('__id_objetivo',$objetivo->objetivo_id);
				
				array_push($steps, $subobjetivo->paso_orden);
				$pasos = $pasos.",".$subobjetivo->paso_orden;
	
				$sql_objetivos.= (($primero)?"":",")."ARRAY[".$subobjetivo->objetivo_id.",".$subobjetivo->paso_orden."]";
				$sql_obj.= (($first)?"":",").$subobjetivo->objetivo_id;
				$primero = false;
				$first = false;
				$T->parse('bloque_objetivos2', 'BLOQUE_OBJETIVOS2', true);
			}
			$pasos=substr($pasos,1);
			$sql_objetivos.= "]";
			$sql_obj.= "]";
			$sql_nodos = "SELECT * FROM reporte._propiedad_nodo(".
			pg_escape_string($current_usuario_id).",".
			pg_escape_string($sql_obj).", '".
			pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
			pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

			$res_nodos =& $mdb2->query($sql_nodos);
			if (MDB2::isError($res_nodos)) {
				$log->setError($sql_nodos, $res_nodos->userinfo);
				exit();
			}

			$row_nodos = $res_nodos->fetchRow();
			$dom_nodos = new DomDocument();
			$dom_nodos->preserveWhiteSpace = FALSE;
			$dom_nodos->loadXML($row_nodos["_propiedad_nodo"]);
			$xpath_nodos = new DOMXpath($dom_nodos);

			$conf_objetivos_nodos = $xpath_nodos->query("/nodos/nodo");

			$T->setVar('bloque_nodos', '');
			$primer_nodo = 1;
			foreach ($conf_objetivos_nodos as $nodos_obj) {
				if ($nodos_obj->getAttribute('nodo_id') == '0') {
				continue;
				}
				$T->setVar('__nombre_nodos', $nodos_obj->getAttribute('nombre').($primer_nodo == ($conf_objetivos_nodos->length - 1) ?'.':','));
				$primer_nodo++;
				$T->parse('bloque_nodos', 'BLOQUE_NODOS', true);
			}
		
			$hora_inicio =split(" ", $this->timestamp->getInicioPeriodo());
			$hora_termino = split(" ", $this->timestamp->getTerminoPeriodo());
			$hora_inicio = $hora_inicio[0];
			$hora_termino = $hora_termino[0];
			$T->setVar('__pasos',$pasos);
			$T->setVar('__user',$current_usuario_id);
			$T->setVar('__inicio', $hora_inicio);
			$T->setVar('__termino',$hora_termino);
			$T->setVar('__hash', $usuario->clave_md5);
			if(isset($_REQUEST['es_pdf'])==true){
				$es_descarga = true;
				$T->setVar('__pdf', 'true');
			}else{
				$T->setVar('__pdf', 'false');
			}
			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT * FROM reporte.especial_disponibilidad_resumen_objetivopaso(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($sql_objetivos).", ".
					pg_escape_string($this->horario_id).",' ".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			//print($sql."<br>");

			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["especial_disponibilidad_resumen_objetivopaso"]);
			$xpath = new DOMXpath($dom);
			$T->setVar('bloque_objetivos', '');

			foreach ($grupo['objetivos'] as $conf_objetivo) {
				$conf_objetivos= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$conf_objetivo->objetivo_id."]")->item(0);
				
				$objetivo_id= $conf_objetivos->getAttribute("objetivo_id");
				$paso =$conf_objetivo->paso_orden;
				$conf_pasos = $xpath->query("paso[@paso_orden=".$paso."]", $conf_objetivos)->item(0);
				$conf_detalles = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$objetivo_id."]/detalles/detalle[@paso_orden=".$conf_objetivo->paso_orden."]/datos/dato")->item(0);
				$T->setVar('__objetivos_id',$objetivo_id);
				$T->setVar('__nombre_obj', $conf_objetivo->nombre_empresa);
				$T->setVar('__nombre_paso',$conf_pasos->getAttribute("nombre"));
				

				$up = doubleval($conf_detalles->getAttribute("uptime"));
				$up_t =(number_format($conf_detalles->getAttribute("uptime"),2)+number_format($conf_detalles->getAttribute("downtime_parcial"),2));
				$down = doubleval($conf_detalles->getAttribute("downtime"));
				$dp = doubleval($conf_detalles->getAttribute("downtime_parcial"));
				$no = doubleval($conf_detalles->getAttribute("sin_monitoreo"));
				$T->setVar('__up',$up);
				$T->setVar('__up_t',$up_t);
				$T->setVar('__down',$down);
				$T->setVar('__dp',$dp);
				$T->setVar('__no',$no);

				
				$T->parse('bloque_objetivos', 'BLOQUE_OBJETIVOS', true);
			}
			$T->setVar('__orden',$grupo['nombre']);

			$T->parse('bloque_grupo', 'BLOQUE_GRUPO', true);
		}


		$this->resultado = $T->parse('out', 'tpl_grafico');
	
	}

	/*
	Creado por: Aldo Cruz Romero
	Fecha de creacion:16-11-2018
	Fecha de ultima modificacion:
	*/
	
	function getComparativoBanco() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$usuario = new Usuario($current_usuario_id);
		$usuario->__Usuario();

		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'comparativo_banco.xhtml');
		$T->setBlock('tpl_grafico', 'BLOQUE_OBJETIVOS', 'bloque_objetivos');
		$T->setBlock('tpl_grafico', 'BLOQUE_OBJETIVOS2', 'bloque_objetivos2');

		$objetivo=new ConfigEspecial($this->objetivo_id);
		if(count($objetivo->__reporte['Disponibilidad'])>2){
			$leyenda_xml ="<table style='width: 800' align='center' cellpadding='2' cellspacing='2' border='1'><tr><td class='celdanegra40'> ".key($objetivo->__reporte['Disponibilidad'][0])."</td><td style='font-family: Trebuchet MS, Verdana, sans-serif;font-size: 12px;' class='celdaIteracion2'> ".$objetivo->__reporte['Disponibilidad'][0][key($objetivo->__reporte['Disponibilidad'][0])]."</td></tr><tr><td class='celdanegra40'>".key($objetivo->__reporte['Disponibilidad'][1])."</td><td style='font-family: Trebuchet MS, Verdana, sans-serif;font-size: 12px;' class='celdaIteracion2'> ".$objetivo->__reporte['Disponibilidad'][1][key($objetivo->__reporte['Disponibilidad'][1])]."</td></tr><tr><td class='celdanegra40'>".key($objetivo->__reporte['Disponibilidad'][2])."</td><td style='font-family: Trebuchet MS, Verdana, sans-serif;font-size: 12px;' class='celdaIteracion2'>".$objetivo->__reporte['Disponibilidad'][2][key($objetivo->__reporte['Disponibilidad'][2])]."</td></tr></table>";
			$T->setVar('__leyenda',$leyenda_xml);
		}
		$objetivoArray=Array();
		$primero = true;$T->setVar('__pasos',$pasos);
		$T->setVar('__user',$current_usuario_id);
		$T->setVar('__inicio', $hora_inicio);
		$T->setVar('__termino',$hora_termino);
		$T->setVar('__hash', $usuario->clave_md5);
		$sql_objetivos = "ARRAY[";
		$steps = Array();
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			array_push($objetivoArray, $subobjetivo->objetivo_id);
			$T->setVar('__id_objetivo',$subobjetivo->objetivo_id);
			$T->setVar('__nombre_objetivo',$subobjetivo->nombre);
			foreach ($subobjetivo->__pasos as $paso) {
				array_push($steps, $paso->paso_id);
				$pasos = $pasos.",".$paso->paso_id;
				$sql_objetivos.= (($primero)?"":",")."ARRAY[".$subobjetivo->objetivo_id.",".$paso->paso_id."]";
			}
			$primero = false;
			$T->parse('bloque_objetivos2', 'BLOQUE_OBJETIVOS2', true);
		}
		$pasos=substr($pasos,1);
		$sql_objetivos.= "]";
		
		$hora_inicio =split(" ", $this->timestamp->getInicioPeriodo());
		$hora_termino = split(" ", $this->timestamp->getTerminoPeriodo());
		$hora_inicio = $hora_inicio[0];
		$hora_termino = $hora_termino[0];
		$T->setVar('__pasos',$pasos);
		$T->setVar('__user',$current_usuario_id);
		$T->setVar('__inicio', $hora_inicio);
		$T->setVar('__termino',$hora_termino);
		$T->setVar('__hash', $usuario->clave_md5);
		if(isset($_REQUEST['es_pdf'])==true){
            $es_descarga = true;
            $T->setVar('__pdf', 'true');
        }else{
        	$T->setVar('__pdf', 'false');
        }
		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.especial_disponibilidad_resumen_objetivopaso(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($sql_objetivos).", ".
				pg_escape_string($this->horario_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		//print($sql."<br>");

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row["especial_disponibilidad_resumen_objetivopaso"]);
		$xpath = new DOMXpath($dom);
		$T->setVar('bloque_objetivos', '');
		foreach ($objetivoArray as $index_obj => $objetivos_id) {
			$conf_detalles = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$objetivos_id."]/detalles/detalle[@paso_orden=".$steps[$index_obj]."]/datos/dato")->item(0);

			$conf_obj = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$objetivos_id."]")->item(0);
			$nombre_obj = $conf_obj->getAttribute("nombre");

			$conf_pasos = $xpath->query("paso[@paso_orden=".$steps[$index_obj]."]", $conf_obj)->item(0);

			$T->setVar('__nombre_paso',$conf_pasos->getAttribute("nombre"));
			$T->setVar('__objetivos_id',$objetivos_id);
			$T->setVar('__nombre_obj',$nombre_obj);
			$up = doubleval($conf_detalles->getAttribute("uptime"));
			$down = doubleval($conf_detalles->getAttribute("downtime"));
			$dp = doubleval($conf_detalles->getAttribute("downtime_parcial"));
			$no = doubleval($conf_detalles->getAttribute("sin_monitoreo"));
			$T->setVar('__up',$up);
			$T->setVar('__down',$down);
			$T->setVar('__dp',$dp);
			$T->setVar('__no',$no);
			$T->parse('bloque_objetivos', 'BLOQUE_OBJETIVOS', true);
		}
		$this->resultado = $T->parse('out', 'tpl_grafico');
	
	}

	/*
	Creado por: Aldo Cruz Romero
	Fecha de creacion:26-12-2019
	Fecha de ultima modificacion:
	*/
	
	function getComparativoAlias() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$usuario = new Usuario($current_usuario_id);
		$usuario->__Usuario();

		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'comparativo_banco_alias.xhtml');
		$T->setBlock('tpl_grafico', 'BLOQUE_OBJETIVOS', 'bloque_objetivos');
		$T->setBlock('tpl_grafico', 'BLOQUE_OBJETIVOS2', 'bloque_objetivos2');
		$objetivo=new ConfigEspecial($this->objetivo_id);
		$objetivoArray=Array();
		$primero = true;
		$T->setVar('__tag_disponibilidad',$objetivo->disponibilidad_real);
		$T->setVar('__pasos',$pasos);
		$T->setVar('__user',$current_usuario_id);
		$T->setVar('__inicio', $hora_inicio);
		$T->setVar('__termino',$hora_termino);
		$T->setVar('__hash', $usuario->clave_md5);
		$sql_objetivos = "ARRAY[";
		$steps = Array();
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			$data=[$subobjetivo->objetivo_id=>$subobjetivo->alias];
			array_push($objetivoArray, $data);
			$T->setVar('__id_objetivo',$subobjetivo->objetivo_id);
			$T->setVar('__nombre_objetivo',$subobjetivo->nombre);
			foreach ($subobjetivo->__pasos as $paso) {
				$data_paso=[$paso->paso_id=>$paso->paso_alias];
				array_push($steps, $data_paso);
				$pasos = $pasos.",".$paso->paso_id;
				$sql_objetivos.= (($primero)?"":",")."ARRAY[".$subobjetivo->objetivo_id.",".$paso->paso_id."]";
			}
			$primero = false;
			$T->parse('bloque_objetivos2', 'BLOQUE_OBJETIVOS2', true);
		}
		$pasos=substr($pasos,1);
		$sql_objetivos.= "]";
		
		$hora_inicio =split(" ", $this->timestamp->getInicioPeriodo());
		$hora_termino = split(" ", $this->timestamp->getTerminoPeriodo());
		$hora_inicio = $hora_inicio[0];
		$hora_termino = $hora_termino[0];
		$T->setVar('__pasos',$pasos);
		$T->setVar('__user',$current_usuario_id);
		$T->setVar('__inicio', $hora_inicio);
		$T->setVar('__termino',$hora_termino);
		$T->setVar('__hash', $usuario->clave_md5);
		if(isset($_REQUEST['es_pdf'])==true){
            $es_descarga = true;
            $T->setVar('__pdf', 'true');
        }else{
        	$T->setVar('__pdf', 'false');
        }
		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.especial_disponibilidad_resumen_objetivopaso(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($sql_objetivos).", ".
				pg_escape_string($this->horario_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		//print($sql."<br>");

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row["especial_disponibilidad_resumen_objetivopaso"]);
		$xpath = new DOMXpath($dom);
		$T->setVar('bloque_objetivos', '');
		foreach ($objetivoArray as $index_obj => $objetivos_id) {
			$key_step=(array_keys($steps[$index_obj]));
			$key_obj=(array_keys($objetivos_id));
			$conf_detalles = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$key_obj[0]."]/detalles/detalle[@paso_orden=".$key_step[0]."]/datos/dato")->item(0);

			$conf_obj = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$key_obj[0]."]")->item(0);
			$nombre_obj = $conf_obj->getAttribute("nombre");

			$conf_pasos = $xpath->query("paso[@paso_orden=".$key_step[0]."]", $conf_obj)->item(0);
			$T->setVar('__nombre_paso',$conf_pasos->getAttribute("nombre"));
			$T->setVar('__objetivos_id',$key_obj[0]);
			$T->setVar('__nombre_obj',$nombre_obj);
			$T->setVar('__alias_obj',($objetivos_id[$key_obj[0]]));
			$T->setVar('__alias_step',($steps[$index_obj][0]));
			$up = doubleval($conf_detalles->getAttribute("uptime"));
			$down = doubleval($conf_detalles->getAttribute("downtime"));
			$dp = doubleval($conf_detalles->getAttribute("downtime_parcial"));
			$no = doubleval($conf_detalles->getAttribute("sin_monitoreo"));
			$T->setVar('__up',$up);
			$T->setVar('__down',$down);
			$T->setVar('__dp',$dp);
			$T->setVar('__no',$no);
			$T->parse('bloque_objetivos', 'BLOQUE_OBJETIVOS', true);
		}
		$this->resultado = $T->parse('out', 'tpl_grafico');
	
	}

	
	
	/*
	Metodo clonado de getComparativoBanco
	Creado por: Francisco Ormeño
	Fecha de creacion:20-06-2019
	Modificado por: Santiago Sepúlveda C.
	Fecha de ultima modificacion: 25-07-2019
	*/
	
	function getComparativoBancoReal() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
		$usuario = new Usuario($current_usuario_id);
		$usuario->__Usuario();

		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'comparativo_banco_real.xhtml');
		
		$T->setBlock('tpl_grafico', 'BLOQUE_OBJETIVOS2', 'bloque_objetivos2');
		$T->setBlock('tpl_grafico', 'BLOQUE_OBJETIVOS', 'bloque_objetivos');
		$T->setBlock('tpl_grafico', 'BLOQUE_NODOS', 'bloque_nodos');
		$T->setBlock('tpl_grafico', 'BLOQUE_GRUPO', 'bloque_grupo');		

		$objetivoEspecial=new ConfigEspecial($this->objetivo_id);

		$T->setVar('bloque_grupo', '');
		$T->setVar('bloque_objetivos2', '');
		$cont_print=1;
		foreach($objetivoEspecial->__grupos as $grupo){
			if($cont_print!=count($objetivoEspecial->__grupos)){
				$T->setVar('__break', "page-break-after: always;");
			}else{
				$T->setVar('__break', "");
			}
			$cont_print++;
			$T->setVar('__nombre_grupo', $grupo['nombre']);
			$objetivoArray=Array();
			$primero = true;
			$sql_objetivos = "ARRAY[";
			$steps = Array();
			$pasos='';
			$first = true;
			$sql_obj = "ARRAY[";
			
			foreach ($grupo['objetivos'] as $subobjetivo) {

				$objetivo = new Objetivo($subobjetivo->objetivo_id);
				
				array_push($objetivoArray, $objetivo->objetivo_id);
				$T->setVar('__nombre_objetivo', $objetivo->nombre_empresa);
				$T->setVar('__id_objetivo',$objetivo->objetivo_id);
				
				array_push($steps, $subobjetivo->paso_orden);
				$pasos = $pasos.",".$subobjetivo->paso_orden;
	
				$sql_objetivos.= (($primero)?"":",")."ARRAY[".$subobjetivo->objetivo_id.",".$subobjetivo->paso_orden."]";
				$sql_obj.= (($first)?"":",").$subobjetivo->objetivo_id;
				$primero = false;
				$first = false;
				$T->parse('bloque_objetivos2', 'BLOQUE_OBJETIVOS2', true);
			}
			$pasos=substr($pasos,1);
			$sql_objetivos.= "]";
			$sql_obj.= "]";
			$sql_nodos = "SELECT * FROM reporte._propiedad_nodo(".
			pg_escape_string($current_usuario_id).",".
			pg_escape_string($sql_obj).", '".
			pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
			pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

			$res_nodos =& $mdb2->query($sql_nodos);
			if (MDB2::isError($res_nodos)) {
				$log->setError($sql_nodos, $res_nodos->userinfo);
				exit();
			}

			$row_nodos = $res_nodos->fetchRow();
			$dom_nodos = new DomDocument();
			$dom_nodos->preserveWhiteSpace = FALSE;
			$dom_nodos->loadXML($row_nodos["_propiedad_nodo"]);
			$xpath_nodos = new DOMXpath($dom_nodos);

			$conf_objetivos_nodos = $xpath_nodos->query("/nodos/nodo");

			$T->setVar('bloque_nodos', '');
			$primer_nodo = 1;
			foreach ($conf_objetivos_nodos as $nodos_obj) {
				if ($nodos_obj->getAttribute('nodo_id') == '0') {
				continue;
				}
				$T->setVar('__nombre_nodos', $nodos_obj->getAttribute('nombre').($primer_nodo == ($conf_objetivos_nodos->length - 1) ?'.':','));
				$primer_nodo++;
				$T->parse('bloque_nodos', 'BLOQUE_NODOS', true);
			}
		
			$hora_inicio =split(" ", $this->timestamp->getInicioPeriodo());
			$hora_termino = split(" ", $this->timestamp->getTerminoPeriodo());
			$hora_inicio = $hora_inicio[0];
			$hora_termino = $hora_termino[0];
			$T->setVar('__pasos',$pasos);
			$T->setVar('__user',$current_usuario_id);
			$T->setVar('__inicio', $hora_inicio);
			$T->setVar('__termino',$hora_termino);
			$T->setVar('__hash', $usuario->clave_md5);
			if(isset($_REQUEST['es_pdf'])==true){
				$es_descarga = true;
				$T->setVar('__pdf', 'true');
			}else{
				$T->setVar('__pdf', 'false');
			}
			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT * FROM reporte.especial_disponibilidad_resumen_objetivopaso(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($sql_objetivos).", ".
					pg_escape_string($this->horario_id).",' ".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			//print($sql."<br>");

			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["especial_disponibilidad_resumen_objetivopaso"]);
			$xpath = new DOMXpath($dom);
			$T->setVar('bloque_objetivos', '');

			foreach ($grupo['objetivos'] as $conf_objetivo) {
				$conf_objetivos= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$conf_objetivo->objetivo_id."]")->item(0);
				
				$objetivo_id= $conf_objetivos->getAttribute("objetivo_id");
				$paso =$conf_objetivo->paso_orden;
				$conf_pasos = $xpath->query("paso[@paso_orden=".$paso."]", $conf_objetivos)->item(0);
				$conf_detalles = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$objetivo_id."]/detalles/detalle[@paso_orden=".$conf_objetivo->paso_orden."]/datos/dato")->item(0);
				$T->setVar('__objetivos_id',$objetivo_id);
				$T->setVar('__nombre_obj', $conf_objetivo->nombre_empresa);
				$T->setVar('__nombre_paso',$conf_pasos->getAttribute("nombre"));
				

				$up = doubleval($conf_detalles->getAttribute("uptime"));
				$up_t =(number_format($conf_detalles->getAttribute("uptime"),2)+number_format($conf_detalles->getAttribute("downtime_parcial"),2));
				$down = doubleval($conf_detalles->getAttribute("downtime"));
				$dp = doubleval($conf_detalles->getAttribute("downtime_parcial"));
				$no = doubleval($conf_detalles->getAttribute("sin_monitoreo"));
				$T->setVar('__up',$up);
				$T->setVar('__up_t',$up_t);
				$T->setVar('__down',$down);
				$T->setVar('__dp',$dp);
				$T->setVar('__no',$no);

				
				$T->parse('bloque_objetivos', 'BLOQUE_OBJETIVOS', true);
			}
			$T->setVar('__orden',$grupo['nombre']);

			$T->parse('bloque_grupo', 'BLOQUE_GRUPO', true);
		}


		$this->resultado = $T->parse('out', 'tpl_grafico');
	
	}


	/*
	Creado por: Aldo Cruz Romero
	Fecha de creacion:16-11-2018
	Fecha de ultima modificacion:
	*/
	
	function getSupervielleVR() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$usuario = new Usuario($current_usuario_id);
		$usuario->__Usuario();
		$objetivo=new ConfigEspecial($this->objetivo_id);
		$T = & new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
		$T->setFile('tpl_grafico', 'vista_rapida_supervielle.xhtml');
		$T->setBlock('tpl_grafico', 'BLOQUE_OBJETIVOS', 'bloque_objetivos');
		
		$T->setVar('__user',$usuario->usuario_id);
		$T->setVar('__objetivo_id',$objetivo->objetivo_id);
		$T->setVar('__inicio', explode(" ",$this->timestamp->getInicioPeriodo())[0]);
		$T->setVar('__termino',explode(" ",$this->timestamp->getTerminoPeriodo())[0]);

		$this->resultado = $T->parse('out', 'tpl_grafico');

	}

        /****** INFORMACION GENERAL ******
            __tipo_grafico:
         * __tipo_grafio = 1 corresponde a tiempo_respuesta_xy
         * __tipo_grafio = 2 corresponde a tasa_errores_xy 
         * __tipo_grafio = 3 corresponde a Apdex_puntuacion 
         * __tipo_grafio = 4 corresponde a tipo_errores_torta 
         * __tipo_grafio = 5 corresponde a top_promedio_transacciones
         * __tipo_grafio = 6 corresponde a iframe 
         
         * EL TIPO ASIGNADO DENTRO DEL XML DEBE SER TEXTUAL AL TIPO DEFINIDO  DENTRO DE LAS FUNCIONES
             
         *** SON UTILIZADAS PARA LA EXTRACCION DE LA URL DE LOS RESPECTIVOS GRAFICOS ***
        */
    /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE EL GRAFICO NEW RELIC DE TIEMPO DE RESPUESTA*/
    public function get_tiempo_respuesta_xy(){
        $tipo_grafico = "tiempo_respuesta_xy";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        $tipoIdGrafico = 1;
        $isHeatMap=False;
        $nameJS='get_ajax';
        $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
    }
    /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE EL GRAFICO NEW RELIC DE TASA DE ERRORES*/
    public function get_tasa_errores_xy(){
        $tipo_grafico = "tasa_errores_xy";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        $tipoIdGrafico = 2;
        $isHeatMap=False;
        $nameJS='get_ajax';
        $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
    }
     /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE EL GRAFICO NEW RELIC DE Apdex_Score*/
    public function get_apdex_puntuacion(){
        $tipo_grafico = "Apdex_puntuacion";

        $validacion = $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);

        $tipoIdGrafico = 3;
        $isHeatMap=False;
        $nameJS='get_ajax';
        $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
    }
     /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE EL GRAFICO NEW RELIC DE TIPO ERROR TORTA*/
     public function get_tipo_error_torta(){
        $tipo_grafico = "tipo_errores_torta";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        
        $tipoIdGrafico = 4;
        $isHeatMap=False;
        $nameJS='get_ajax';
        $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
    }
    
     /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE LA TABLA NEW RELIC TOP 5 TIEMPOS DE RESPUESTA MAS ELEVADOS*/
     public function get_tiempo_respuesta_mas_elevado(){
        $tipo_grafico = "top_promedio_transacciones";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        $tipoIdGrafico = 5;
        $isHeatMap=False;
        $nameJS='get_ajax_tabla';
        $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
    }
    
    /**/
    public function getAudex(){
        $objetivo = new ConfigEspecial($this->objetivo_id);
        $arr_orden= array();
    	$arr_tiempo= array();
     	$xpath = $this->getDatosAudex($this->objetivo_id);

    	$conf_objetivo= $xpath->query("/atentus/objetivo")->item(0);
    	$conf_fechas= $xpath->query("/atentus/fechas")->item(0);
    
    	$arr_orden[0]=array("0","1","2");
    	$arr_tiempo[0]= array("Periodo 30 Minutos","Periodo 60 Minutos","Periodo 3 Horas");
    
    	$T =& new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
    	$T->setFile('tpl_grafico','reporte_audex.xhtml');
    
    	$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
    
    	$T->setVar('__selector_tiempo', $this->generarSelectorTiempo($arr_orden, $arr_tiempo));
    	$T->setVar('__count_datos',$xpath->query("/atentus/salida/resultados")->length );
    	$T->setVar('__umbral_satisfactorio', $conf_objetivo->getAttribute('umbral_satisfactorio'));
    	$T->setVar('__umbral_intolerable', $conf_objetivo->getAttribute('umbral_intolerable'));
        $T->setVar('__umbral_excelente', $conf_objetivo->getAttribute('umbral_excelente'));
    	$T->setVar('__umbral_bueno', $conf_objetivo->getAttribute('umbral_bueno'));
    	$T->setVar('__nombre', $conf_objetivo->getAttribute('nombre'));
    	$T->setVar('__objetivo_id', $conf_objetivo->getAttribute('objetivo_id'));
        $T->setVar('__titulo', $objetivo->titulo);
        $T->setVar('__informacion', $objetivo->information);
        $T->setVar('__es_atdex', ',c:0');
        
        if(!$xpath->query("/atentus/salida/resultados")->length) {
    		$this->resultado = $this->__generarContenedorSinDatos();
    		return;
    	}
    
    	$contador=0;
    	foreach ($xpath->query("/atentus/salida/resultados") as $conf_datos){
    		 
    		$T->setVar('__point_count', $contador);
    		$T->setVar('__point_name', date("Y,(m - 1),d,H,i,s",strtotime($conf_datos->getAttribute('fecha'))));
    		 
    		$T->setVar('__point_value', floatval($conf_datos->getAttribute('valor_audex')));
    		$T->setVar('__point_umbral',floatval($conf_datos->getAttribute('umbral_audex')));
    		$T->parse('point_element', 'POINT_ELEMENT', true);
    		$contador++;
    	}
    	$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'));
    }
    
    
    /**/
    public function getAtdex(){
    	$objetivo = new ConfigEspecial($this->objetivo_id);
    	$arr_orden= array();
    	$arr_tiempo= array();
    	$xpath = $this->getDatosAudex($this->objetivo_id);
    
    	$conf_objetivo= $xpath->query("/atentus/objetivo")->item(0);
    	$conf_fechas= $xpath->query("/atentus/fechas")->item(0);
    
    	$arr_orden[0]=array("0","1","2");
    	$arr_tiempo[0]= array("Periodo 30 Minutos","Periodo 60 Minutos","Periodo 3 Horas");
    
    	$T =& new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
    	$T->setFile('tpl_grafico','reporte_audex.xhtml');
    
    	$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
    
    	$T->setVar('__selector_tiempo', $this->generarSelectorTiempo($arr_orden, $arr_tiempo));
    	$T->setVar('__count_datos',$xpath->query("/atentus/salida/resultados")->length );
    	$T->setVar('__umbral_satisfactorio', $conf_objetivo->getAttribute('umbral_satisfactorio'));
    	$T->setVar('__umbral_intolerable', $conf_objetivo->getAttribute('umbral_intolerable'));
    	$T->setVar('__umbral_excelente', $conf_objetivo->getAttribute('umbral_excelente'));
    	$T->setVar('__umbral_bueno', $conf_objetivo->getAttribute('umbral_bueno'));
    	$T->setVar('__nombre', $conf_objetivo->getAttribute('nombre'));
    	$T->setVar('__objetivo_id', $conf_objetivo->getAttribute('objetivo_id'));
    	$T->setVar('__titulo', $objetivo->titulo);
    	$T->setVar('__informacion', $objetivo->information);
    	$T->setVar('__es_atdex', ',c:1');
    	
    	if(!$xpath->query("/atentus/salida/resultados")->length) {
    		$this->resultado = $this->__generarContenedorSinDatos();
    		return;
    	}
    
    	$contador=0;
    	foreach ($xpath->query("/atentus/salida/resultados") as $conf_datos){
    		 
    		$T->setVar('__point_count', $contador);
    		$T->setVar('__point_name', date("Y,(m - 1),d,H,i,s",strtotime($conf_datos->getAttribute('fecha'))));
    		 
    		$T->setVar('__point_value', floatval($conf_datos->getAttribute('valor_atdex')));
    		$T->setVar('__point_umbral',floatval($conf_datos->getAttribute('umbral_atdex')));
    		$T->parse('point_element', 'POINT_ELEMENT', true);
    		$contador++;
    	}
    	$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'));
    }
    
    /**/
    public function getEspecialAudex(){
        
    	$objetivo = new ConfigEspecial($this->objetivo_id);
        foreach ($objetivo->getSubobjetivos() as $tag_subobjetivo) {
    		$subobjetivo = $tag_subobjetivo->objetivo_id;
    	}
    	 
    	$arr_orden= array();
    	$arr_tiempo= array();
    	$xpath = $this->getDatosAudex($subobjetivo);
    	$conf_objetivo= $xpath->query("/atentus/objetivo")->item(0);
    	$conf_fechas= $xpath->query("/atentus/fechas")->item(0);
    	 
    	$arr_orden[0]=array("0","1","2");
    	$arr_tiempo[0]= array("Periodo 30 Minutos","Periodo 60 Minutos","Periodo 3 Horas");
    	//$selector = $this->generarSelectorTiempo($arr_orden, $arr_tiempo);
    
    	$T =& new Template_PHPLIB(REP_PATH_XHTMLTEMPLATES);
    	$T->setFile('tpl_grafico','reporte_audex.xhtml');
    	 
    	$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
    
    	$T->setVar('__selector_tiempo', $this->generarSelectorTiempo($arr_orden, $arr_tiempo));
    	$T->setVar('__count_datos',$xpath->query("/atentus/salida/audex")->length );
    	$T->setVar('__umbral_satisfactorio', $conf_objetivo->getAttribute('umbral_satisfactorio'));
    	$T->setVar('__umbral_intolerable', $conf_objetivo->getAttribute('umbral_intolerable'));
    	$T->setVar('__nombre', $conf_objetivo->getAttribute('nombre'));
    	$T->setVar('__umbral_excelente', $conf_objetivo->getAttribute('umbral_excelente'));
    	$T->setVar('__umbral_bueno', $conf_objetivo->getAttribute('umbral_bueno'));
        
    	if(!$xpath->query("/atentus/salida/audex")->length) {
    		$this->resultado = $this->__generarContenedorSinDatos();
    		return;
    	}
    	 
    	$contador=0;
    	foreach ($xpath->query("/atentus/salida/audex") as $conf_datos){
    		 
    		$T->setVar('__point_count', $contador);
    		$T->setVar('__point_name', date("Y,(m - 1),d,H,i,s",strtotime($conf_datos->getAttribute('fecha'))));
    		$fechaini = strtotime($conf_datos->getAttribute('fecha'));
    		$FechaInicio = date('Y.m.d.H.i.s',$fechaini);
    	  
    		$T->setVar('__point_value', floatval($conf_datos->getAttribute('valor')));
    		$T->parse('point_element', 'POINT_ELEMENT', true);
    		$contador++;
    	}
    	$this->resultado = $this->__generarContenedorSVG($T->parse('out', 'tpl_grafico'));
    }
    
    
     /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE EL IFRAME*/
     public function get_iframe(){
         
        $tipo_grafico = "iframe";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        
        $tipoIdGrafico = 6;
        $isHeatMap=False;
        $nameJS='get_ajax_tabla';
        $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
    }

    /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE EL GRAFICO NEW RELIC DE TIEMPO DE RESPUESTA  DEL BROWSER*/
    public function getLoadTimeBrowserXy(){
        $tipo_grafico = "loadtime_browser_xy";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        
        $tipoIdGrafico = 11;
        $isHeatMap=False;
        $nameJS='get_ajax';
        $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
    }
    /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE EL GRAFICO NEW RELIC CARGA PROMEDIO POR BROWSER*/
    public function getAvgLoadTimeBrowser(){
        $tipo_grafico = "avgloadtime_browsers_xy";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        
        $tipoIdGrafico = 7;
        $isHeatMap=False;
        $nameJS='get_ajax';
        $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
    }
    /*FUNCION QUE GENERA GRAFICO CARGA POR URL*/
    public function getLoadUrl(){
        $tipo_grafico = "urlload_heatmap";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        
        $tipoIdGrafico = 8;
        $isHeatMap=True;
        $nameJS='get_ajax';
        $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
    }
    /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE EL GRAFICO NEW RELIC DE TASA DE ERRORES JS*/
    public function getErrorRateJS(){
        $tipo_grafico = "jserror_rate";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        
        $tipoIdGrafico = 9;
        $isHeatMap=False;
        $nameJS='get_ajax';
        $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
    }
     /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE EL GRAFICO NEW RELIC DE TASA DE ERRORES JS*/
    public function getResponseAjax(){
        $tipo_grafico = "ajax_responsetime";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        
        $tipoIdGrafico = 10;
        $isHeatMap=False;
        $nameJS='get_ajax';
        $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
    }
     /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE EL GRAFICO NEW RELIC PROMEDIO INTERACCION*/
    public function getAvgInteractionApp(){
        $tipo_grafico = "promedio_interaccion_xy";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        
        $tipoIdGrafico = 13;
        $isHeatMap=False;
        $nameJS='get_ajax';
        $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
    }
     /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE EL GRAFICO NEW RELIC DE USO VERSION SO*/
    public function getUseVersionSO(){
        $tipo_grafico = "uso_version_so_xy";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        
        $tipoIdGrafico = 14;
        $isHeatMap=False;
        $nameJS='get_ajax';
        $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
    }
    /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE EL GRAFICO NEW RELIC DE TIEMPO RESPUESTA HTTP*/
    public function getTimeResponseHTTP(){
         $tipo_grafico = "tiempo_respuesta_http_xy";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        
        $tipoIdGrafico = 15;
        $isHeatMap=False;
        $nameJS='get_ajax';
         $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
        
    }
    /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE EL GRAFICO NEW RELIC DE TIEMPO RESPUESTA HTTP*/
    public function getNumberErrors(){
        $tipo_grafico = "cantidad_errores_xy";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        
        $tipoIdGrafico = 16;
        $isHeatMap=False;
        $nameJS='get_ajax';
         $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
        
    }
    /*FUNCION QUE GENERA EL TEMPLATE QUE CONTIENE EL GRAFICO NEW RELIC DE TIEMPO RESPUESTA HTTP*/
    public function getTimeInteractionEnabledDevice(){
        $tipo_grafico = "tiempo_interaccion_dispositivo_xy";
        $validacion= $this->get_grafico_validacion($tipo_grafico);
        $titulo = $validacion["selector_url"][0][0];
        $selector = $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']);
        $T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
        
        $tipoIdGrafico = 17;
        $isHeatMap=False;
        $nameJS='get_ajax';
         $this->settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $nameJS, $isHeatMap);
        
    }
    /*Función que configura los reportes New Relic*/
    public function settingReportNewRelic($tipo_grafico, $validacion, $titulo, $selector, $T, $tipoIdGrafico, $tipoAjax, $heatMap ){
        if($validacion['url']==true and $validacion['visible']==true and $validacion['tipo_correcto']==true){
        
            $T->setFile('tpl_contenido','reporte_new_relic.tpl');
            $T->setVar('__objetivo_id', $this->objetivo_id);
            $T->setVar('__tipo_grafico', $tipoIdGrafico);
            $T->setVar('__nombre_tipo', $tipo_grafico);
            $T->setVar('__contador', ($selector=='')?1:2);
            $T->setVar('__selector_tiempo', $this->generarSelectorTiempo($validacion['orden'], $validacion['selector_url']));
            $T->setVar('__get_ajax_tipo', $tipoAjax); 
            $T->setVar('__titulo', $titulo);
            $T->setVar('__informacion', ($validacion['informacion'] != "")?$validacion['informacion']:'');
            $T->setVar('__link', ($validacion['link'] != "")?$validacion['link']:'');
            if($heatMap==True){
                $T->setVar('__heatmap', "tools/highcharts/modules/heatmap.js");
            }
            
            $T->setVar('__path_jquery_ui', REP_PATH_JQUERY_UI);
            $T->setVar('__path_highcharts', REP_PATH_HIGHCHARTS);
        }
        elseif ($validacion['visible']==false and $validacion['contador']!=0){
            $T->setFile('tpl_contenido', 'sorry_reporte.tpl');
            $T->setVar('__path_img', REP_PATH_IMG);
        }
        /*url  o tipo incorrecto*/
        elseif($validacion['url']==false or $validacion['tipo_correcto']==false){
            $T->setFile('tpl_contenido', 'sorry_xml.tpl');
            $T->setVar('__path_img', REP_PATH_IMG);
        }
        
        $this->resultado =  $T->pparse('out', 'tpl_contenido');
        
    }
    /*funcion llamada al encontrar un error o no tiene datos */
    public function _generarContenedor_ErrorXml($tipo_errors){
        $T = & new Template_PHPLIB(REP_PATH_TEMPLATES);
        $tipo_error = $tipo_errors==='true'? true:false;
        $T->setFile('tpl_tabla', ($tipo_error==true)?'sorry_xml.tpl':'sin_datos.tpl');
        $T->setVar('__titulo', '');
        $T->setVar('__path_img', REP_PATH_IMG);
        return $T->parse('out', 'tpl_tabla');
    }

	public function __generarContenedorSVG($contenido, $elemento_id = 0, $tiene_selector = false, $tiene_escala = false) {
		$T = & new Template_PHPLIB(REP_PATH_TEMPLATES);
		$T->setFile('tpl_tabla', 'contenedor_grafico_svg.tpl');
//		$T->setVar('__contenedor_nombre', $nombre);
//		$T->setVar('__contenedor_id', $id);
		$T->setVar('__svg_contenido', $contenido);
		$T->setVar('__svg_escala', ($tiene_escala)?$this->generarEscala():'');
		$T->setVar('__svg_botones', ($tiene_selector)?$this->generarBotones($elemento_id):'');
		return $T->parse('out', 'tpl_tabla');
	}

	private function __generarContenedorSinDatos() {
		$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
		$T->setFile('tpl_sindatos', 'sin_datos.tpl');
		$T->setBlock('tpl_sindatos', 'TIENE_TITULO', 'tiene_titulo');

		if ($titulo == null) {
			$T->setVar('tiene_titulo', '');
		}
		else {
			$T->setVar('__titulo', $titulo);
			$T->parse('tiene_titulo', 'TIENE_TITULO', false);
		}
		return $T->parse('out', 'tpl_sindatos');
	}

	private function __generarContenedorErrorSql() {
		$T =& new Template_PHPLIB(REP_PATH_TEMPLATES);
		$T->setFile('tpl_sindatos', 'error_especial.tpl');
		$T->setBlock('tpl_sindatos', 'TIENE_TITULO', 'tiene_titulo');

		if ($titulo == null) {
			$T->setVar('tiene_titulo', '');
		}
		else {
			$T->setVar('__titulo', $titulo);
			$T->parse('tiene_titulo', 'TIENE_TITULO', false);
		}
		return $T->parse('out', 'tpl_sindatos');
	}

	public function generarBotones($elemento_id) {

            
		$text = "<table><tr><td>";
		foreach($this->botones as $boton) {
			if (get_class($boton) == "DatosMonitor" or get_class($boton) == "DatosPaso") {
				$boton_id = (get_class($boton) == "DatosMonitor")?$boton->monitor_id:$boton->paso_id;
				$boton_nombre = (get_class($boton) == "DatosMonitor")?"monitor_id":"paso_id";
				$boton_descripcion = $boton->nombre;
			}
			else {
				if($boton->getAttribute("nodo_id") != null){$boton_id=$boton->getAttribute("nodo_id");$boton_nombre="monitor_id";}
				if($boton->getAttribute("paso_orden") != null){$boton_id=$boton->getAttribute("paso_orden");$boton_nombre="paso_id";}
				if($boton->getAttribute("objetivo_id") != null){$boton_id=$boton->getAttribute("objetivo_id");$boton_nombre="objetivo_id";}
				//$boton_nombre = ($boton->getAttribute("nodo_id") != null)?"monitor_id":"paso_id";
				$boton_descripcion = $boton->getAttribute("nombre");
			}

			$text.= "<div class='boton_elemento celdaselector' style='height:30px; width:135px; overflow:hidden; ".(($boton_id == $elemento_id)?'background-color:#f36f00; color:#ffffff':'')."' data-item_id='".$this->__item_id."' data-elemento_id='$boton_id' data-elemento_nombre='$boton_nombre'>".
			        "<div style='top:25%; position:relative; width:131px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis'>".$boton_descripcion."</div>".
				    "</div>";
		}
		$text.= "</td></tr></table><br>";
		return $text;
	}

}

?>