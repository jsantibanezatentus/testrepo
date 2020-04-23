<?

class GraficoFlash extends Grafico {
	
	var $tipo;
	var $path_flash;
	var $__subgraficos;
	
	/*
	 * Funcion Constructor.
	 * @param string $tipo (grafico|tabla|csv|configuracion)
	 */
	function GraficoFlash() {
		$this->tiempo_expiracion = 86400;
		$this->resultado = $this->__generarMensajeError();
	}
	
	
	
	/*************** FUNCIONES DE GRAFICOS DE DISPONIBILIDAD ***************/
	/*************** FUNCIONES DE GRAFICOS DE DISPONIBILIDAD ***************/
	/*************** FUNCIONES DE GRAFICOS DE DISPONIBILIDAD ***************/

	/**
	 * Funcion para obtener el grafico de 
	 * Disponibilidad Global.
	 * 
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
	
	// TODO: getDisponibilidadConsolidadoObjetivos()
	function getDisponibilidadSemaforo() {
		$xpath = $this->getDatosDisponibilidadSemaforo();
		
		$conf_objetivos = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo");
		
		if ($this->tipo == "html") {
			if ($xpath->query("/atentus/resultados/detalles/detalle/datos/dato")->length > 0) {
				$this->path_flash = REP_PATH_AGANTT;
				$this->ancho = REP_GRAFICO_ANCHO;
				$this->alto = ($conf_objetivos->length>0)?(80+$conf_objetivos->length*30):140;
				$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
				$this->resultado = $this->__generarContenedorFlash(false);
			}
			else {
				$this->resultado = $this->__generarContenedorSinDatos();
			}
			return;
		}

		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_consolidado.tpl');
		$T->setBlock('tpl_grafico', 'RESOURCE_ELEMENT', 'resource_element');
		$T->setBlock('tpl_grafico', 'BLOQUE_ACTION', 'bloque_action');
		$T->setBlock('tpl_grafico', 'PERIOD_ELEMENT', 'period_element');
		$T->setBlock('tpl_grafico', 'EXCEPTION_ELEMENT', 'exception_element');
		$T->setBlock('tpl_grafico', 'TIENE_HORARIO_HABIL', 'tiene_horario_habil');
		
		/* DATOS DEL GRAFICO */
		foreach ($conf_objetivos as $objetivo){
			$T->setVar('__resource_id', $objetivo->getAttribute('objetivo_id'));
			$T->setVar('__resource_name', $objetivo->getAttribute('nombre'));
			$T->setVar('__resource_parent', '');
			$T->setVar('__resource_expanded', 'false');
			$T->setVar('__resource_style', '');
			$T->parse('resource_element', 'RESOURCE_ELEMENT', true);

			$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$objetivo->getAttribute('objetivo_id')."]/datos/dato");
			foreach ($tag_datos as $tag_dato) {
				$T->setVar('__period_resource_id', $objetivo->getAttribute('objetivo_id'));
				$T->setVar('__period_start', date('Y-m-d H:i:s', strtotime($tag_dato->getAttribute('inicio'))));
				$T->setVar('__period_end', date('Y-m-d H:i:s', strtotime($tag_dato->getAttribute('termino'))));
				$T->setVar('__period_style', Utiles::getStyleDisponibilidad($tag_dato->getAttribute('evento_id')));
				$T->parse('period_element', 'PERIOD_ELEMENT', true);
			}
		}
	
		/* FORMATO DEL GRAFICO */
		$T->setVar('__scale_start', date('Y.m.d.H.i.s', strtotime($tag_datos->item(0)->getAttribute('inicio'))));
		$T->setVar('__scale_end', date('Y.m.d.H.i.s', strtotime($tag_datos->item($tag_datos->length-1)->getAttribute('termino'))));
		
		$this->resultado = $T->parse('out', 'tpl_grafico');
	}
	
	
	/**
	 * Funcion para obtener el grafico de 
	 * Disponibilidad Consolidado.
	 * 
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */

	// TODO: metodo getDisponibilidadConsolidado()
	function getConsolidadoDisponibilidad() {
		
		$xpath = $this->getDatosConsolidadoDisponibilidad();
		$conf_resultado = $xpath->query("/atentus/resultados")->item(0);
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id!=0]");

		if ($this->tipo == "html") {			
			if ($xpath->query("/atentus/resultados/detalles/detalle/datos/dato")->length > 0) {
				$this->path_flash = REP_PATH_AGANTT;
				$this->ancho = REP_GRAFICO_ANCHO;
				
				if ($this->subgrafico_id == null) {
					$this->alto = ($conf_nodos->length>8)?(120+($conf_nodos->length*30)):350;
				}
				else {
					$this->alto = 150 + ($conf_nodos->length*30);
				}
				$this->tiempo_expiracion = (strtotime($conf_resultado->getAttribute('fecha_expiracion')) - strtotime($conf_resultado->getAttribute('fecha')));
				$this->resultado = $this->__generarContenedorFlash(false);
				$this->setSubgraficos($conf_nodos);
			}
			else {
				$this->resultado = $this->__generarContenedorSinDatos();
			}
			return;
		}
		
		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_consolidado.tpl');
		$T->setBlock('tpl_grafico', 'RESOURCE_ELEMENT', 'resource_element');
		$T->setBlock('tpl_grafico', 'BLOQUE_ACTION', 'bloque_action');
		$T->setBlock('tpl_grafico', 'PERIOD_ELEMENT', 'period_element');
		$T->setBlock('tpl_grafico', 'EXCEPTION_ELEMENT', 'exception_element');
		$T->setBlock('tpl_grafico', 'TIENE_HORARIO_HABIL', 'tiene_horario_habil');
		
		/* DATOS DEL GRAFICO */
		$T->setVar('__resource_id', $conf_objetivo->getAttribute('objetivo_id'));
		$T->setVar('__resource_name', $conf_objetivo->getAttribute('nombre'));
		$T->setVar('__resource_parent', '');
		$T->setVar('__resource_expanded', 'true');
		$T->setVar('__resource_style', 'style="consolidado"');
		$T->parse('resource_element', 'RESOURCE_ELEMENT', true);
		
		/* DATOS DEL OBJETIVO */
		foreach ($xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=". $conf_objetivo->getAttribute('objetivo_id')."]/datos/dato") as $tag_objetivo) {
			$T->setVar('__period_resource_id', $conf_objetivo->getAttribute('objetivo_id'));
			$T->setVar('__period_start', date("Y.m.d.H.i.s", strtotime($tag_objetivo->getAttribute('inicio'))));
			$T->setVar('__period_end', date("Y.m.d.H.i.s", strtotime($tag_objetivo->getAttribute('termino'))));
			$T->setVar('__period_style', Utiles::getStyleDisponibilidad($tag_objetivo->getAttribute('evento_id')));
			$T->setVar('bloque_action', '');
			$T->parse('period_element', 'PERIOD_ELEMENT', true);
		}
		
		/* DATOS DEL NODO */
		foreach ($conf_nodos as $conf_nodo){
			if ($this->subgrafico_id == null or $this->subgrafico_id == $conf_nodo->getAttribute('nodo_id')) {
				$T->setVar('__resource_id', $conf_objetivo->getAttribute('objetivo_id')."-".$conf_nodo->getAttribute('nodo_id'));
				$T->setVar('__resource_name', $conf_nodo->getAttribute('nombre'));
				$T->setVar('__resource_parent', $conf_objetivo->getAttribute('objetivo_id'));
				$T->setVar('__resource_expanded', ($this->subgrafico_id == null)?'false':'true');
				$T->setVar('__resource_style', '');

				$T->parse('resource_element', 'RESOURCE_ELEMENT', true);
				
				$linea = 0;
				$tag_datos_nodo = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=".$conf_nodo->getAttribute('nodo_id')."]/datos/dato");
				foreach ($tag_datos_nodo as $tag_dato_nodo) {
					$T->setVar('__period_resource_id', $conf_objetivo->getAttribute('objetivo_id')."-".$conf_nodo->getAttribute('nodo_id'));
					$T->setVar('__period_start', date("Y.m.d.H.i.s", strtotime($tag_dato_nodo->getAttribute('inicio'))));
					$T->setVar('__period_end', date("Y.m.d.H.i.s", strtotime($tag_dato_nodo->getAttribute('termino'))));
					$T->setVar('__period_style', Utiles::getStyleDisponibilidad($tag_dato_nodo->getAttribute('evento_id')));
					$T->setVar('__monitor_id', $conf_nodo->getAttribute('nodo_id'));
					$T->setVar('__paso_id', '');
					$T->setVar('__fecha_monitoreo', strtotime($tag_dato_nodo->getAttribute('inicio')));
					$T->setVar('__pagina', ((($tag_datos_nodo->length - $linea) % 6 == 0)?0:1) + intval(($tag_datos_nodo->length - $linea) / 6));
				
					$T->parse('bloque_action', 'BLOQUE_ACTION', false);
					$T->parse('period_element', 'PERIOD_ELEMENT', true);
					$linea++;
				}
				
				foreach ($conf_pasos as $conf_paso){				
					$T->setVar('__resource_id', $conf_objetivo->getAttribute('objetivo_id')."-".$conf_nodo->getAttribute('nodo_id')."-".$conf_paso->getAttribute('paso_orden'));
					$T->setVar('__resource_name', $conf_paso->getAttribute('nombre'));
					$T->setVar('__resource_parent', $conf_objetivo->getAttribute('objetivo_id')."-".$conf_nodo->getAttribute('nodo_id'));
					
					$T->parse('resource_element', 'RESOURCE_ELEMENT', true);
					
					if ($tag_datos_nodo->length > 0) {
						
						$linea = 0;
						$tag_datos_paso = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=".$conf_nodo->getAttribute('nodo_id')."]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato");
						foreach ($tag_datos_paso as $tag_dato_paso) {
							$T->setVar('__period_resource_id', $conf_objetivo->getAttribute('objetivo_id')."-".$conf_nodo->getAttribute('nodo_id')."-".$conf_paso->getAttribute('paso_orden'));
							$T->setVar('__period_start',date("Y.m.d.H.i.s", strtotime($tag_dato_paso->getAttribute('inicio'))));
							$T->setVar('__period_end', date("Y.m.d.H.i.s", strtotime($tag_dato_paso->getAttribute('termino'))));
							$T->setVar('__period_style', Utiles::getStyleDisponibilidad($tag_dato_paso->getAttribute('evento_id')));
							$T->setVar('__monitor_id', $conf_nodo->getAttribute('nodo_id'));
							$T->setVar('__paso_id', $conf_paso->getAttribute('paso_orden'));
							$T->setVar('__fecha_monitoreo', strtotime($tag_dato_paso->getAttribute('inicio')));
							$T->setVar('__pagina', ((($tag_datos_nodo->length - $linea) % 6 == 0)?0:1) + intval(($tag_datos_nodo->length - $linea) / 6));
						
							$T->parse('bloque_action', 'BLOQUE_ACTION', false);
							$T->parse('period_element', 'PERIOD_ELEMENT', true);
							$linea++;
						}
					}
				}
			}
		}
		
		$T->setVar('exception_element', '');
		$T->setVar('__item_id', $this->__item_id);
		$T->setVar('__item_id_nuevo', REP_ITEM_EVENTOS);
		
		$T->setVar('__scale_start', $this->timestamp->getInicioPeriodo("Y.m.d.H.i.s"));
		$T->setVar('__scale_end', $this->timestamp->getTerminoPeriodo("Y.m.d.H.i.s"));
		$this->resultado = $T->parse('out', 'tpl_grafico');
	}
	

	/**
	 * Funcion para obtener el grafico de 
	 * Disponibilidad Detallada.
	 * 
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
	// TODO: metodo getDisponibilidadResumen()
	function getDetalladoDisponibilidad() {
	
		$xpath = $this->getDatosDetalladoDisponibilidad();
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo");
		
		/* SI SOLO SE QUIERE OBTENER LA CONFIGURACION */
		if ($this->tipo == "html") {
			$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
			
			if ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length > 0) {
				$this->path_flash = REP_PATH_ACHART;
				$this->ancho = REP_GRAFICO_ANCHO;
				$this->alto = 600;
				$this->resultado = $this->__generarContenedorFlash(false);
				$this->setSubgraficos($conf_nodos);
			}
			else {
				$this->resultado = $this->__generarContenedorSinDatos();
			}
			return;
		}
		
		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_resumen.tpl');
		$T->setBlock('tpl_grafico', 'MOSTRAR_SCROLL', 'mostrar_scroll');
		$T->setBlock('tpl_grafico', 'MONITOR_POINT_ELEMENT', 'monitor_point_element');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
		$T->setBlock('tpl_grafico', 'DISPONIBILIDAD_POINT_ELEMENT', 'disponibilidad_point_element');
		$T->setBlock('tpl_grafico', 'DISPONIBILIDAD_SERIES_TOOLTIP', 'disponibilidad_series_tooltip');
		$T->setBlock('tpl_grafico', 'DISPONIBILIDAD_SERIES_ELEMENT', 'disponibilidad_series_element');
		$T->setBlock('tpl_grafico', 'SINNOMONITOREO_POINT_ELEMENT', 'sinnomonitoreo_point_element');
		$T->setBlock('tpl_grafico', 'SINNOMONITOREO_SERIES_TOOLTIP', 'sinnomonitoreo_series_tooltip');
		$T->setBlock('tpl_grafico', 'SINNOMONITOREO_SERIES_ELEMENT', 'sinnomonitoreo_series_element');
		$T->setBlock('tpl_grafico', 'CHART_ELEMENT', 'chart_element');
		
		/* DATOS DEL GRAFICO */
		$fila = 0;
		$columna = 0;
		foreach ($conf_nodos as $conf_nodo) {
			if ($this->subgrafico_id == null or $this->subgrafico_id == $conf_nodo->getAttribute('nodo_id')) {
				
				/* SELECCION DE PASOS : DATOS DEL GRAFICO */
				$T->setVar('__monitor_point_row', $fila);
				$T->setVar('__monitor_point_column', $columna);
				$T->setVar('__monitor_point_value', $conf_nodo->getAttribute('nodo_id'));
				$T->setVar('__monitor_point_selected', ($fila == 0 && $columna == 0)?'True':'False');
				$T->setVar('__monitor_attribute_content', $conf_nodo->getAttribute('nombre'));
				$T->parse('monitor_point_element', 'MONITOR_POINT_ELEMENT', true);
				$columna ++;
				
				if ($columna == 4 and ($fila + 1)*4 != $conf_nodos->length) {
					$columna = 0;
					$fila ++;
				}
				
				/* DISPONIBILIDAD : DATOS DEL GRAFICO */
				$min_value = 100;
				$T->setVar('disponibilidad_series_element', '');
				$T->setVar('sinnomonitoreo_series_element', '');
				
				if($conf_pasos->length > 0) {					
					foreach ($conf_pasos as $conf_paso) {
						
//						$nomonitoreo = 0;
						$tag_datos_sinmon = $xpath->query("//detalles/detalle[@nodo_id=".$conf_nodo->getAttribute('nodo_id')."]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]/estadisticas/estadistica[@evento_id=7]");
						$porcentaje_sinmon = ($tag_datos_sinmon->length == 0)?0:$tag_datos_sinmon->item(0)->getAttribute("porcentaje");
						
						$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=".$conf_nodo->getAttribute('nodo_id')."]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/estadisticas/estadistica");
						
						$T->setVar('__series_name', ($conf_pasos->length>15)?$conf_paso->getAttribute('paso_orden'):$conf_paso->getAttribute('nombre'));
//						$T->setVar('__sinnomonitoreo_series_name', ($conf_pasos->length>15)?$conf_paso->getAttribute('paso_orden'):$conf_paso->getAttribute('nombre'));
						
						$T->setVar('disponibilidad_point_element', '');
						$T->setVar('disponibilidad_series_tooltip', '');
						foreach ($tag_datos as $tag_dato) {
							
/*							if ($tag_dato->getAttribute('evento_id') == 1 and $tag_dato->getAttribute('porcentaje') < $min_value) {
								$min_value = $tag_dato->getAttribute('porcentaje');
							}
							elseif ($tag_dato->getAttribute('evento_id') == 7) {
								$nomonitoreo = $tag_dato->getAttribute('porcentaje');
							}*/
							
							$conf_evento = $xpath->query("/atentus/resultados/propiedades/eventos/evento[@evento_id=".$tag_dato->getAttribute('evento_id')."]")->item(0);
							$T->setVar('__disponibilidad_point_name', $conf_evento->getAttribute('nombre'));
							$T->setVar('__disponibilidad_point_value', $tag_dato->getAttribute('porcentaje'));
							$T->setVar('__disponibilidad_point_color', $conf_evento->getAttribute('color'));
							$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
							$T->parse('disponibilidad_point_element', 'DISPONIBILIDAD_POINT_ELEMENT', true);
							$T->parse('disponibilidad_series_tooltip', 'DISPONIBILIDAD_SERIES_TOOLTIP', true);
						}
						$T->parse('disponibilidad_series_element', 'DISPONIBILIDAD_SERIES_ELEMENT', true);
						
						$T->setVar('sinnomonitoreo_point_element', '');
						$T->setVar('sinnomonitoreo_series_tooltip', '');
						foreach ($tag_datos as $tag_dato) {
							if ($tag_dato->getAttribute('evento_id') != 7) {
								$conf_evento = $xpath->query("/atentus/resultados/propiedades/eventos/evento[@evento_id=".$tag_dato->getAttribute('evento_id')."]")->item(0);
								$T->setVar('__sinnomonitoreo_point_name', $conf_evento->getAttribute('nombre'));
								$T->setVar('__sinnomonitoreo_point_value', number_format(($tag_dato->getAttribute('porcentaje')/ (100-$porcentaje_sinmon)) * 100, 2, '.', ''));
								$T->setVar('__sinnomonitoreo_point_color', $conf_evento->getAttribute('color'));
								$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
								$T->parse('sinnomonitoreo_point_element', 'SINNOMONITOREO_POINT_ELEMENT', true);
								$T->parse('sinnomonitoreo_series_tooltip', 'SINNOMONITOREO_SERIES_TOOLTIP', true);
							}
						}
						$T->parse('sinnomonitoreo_series_element', 'SINNOMONITOREO_SERIES_ELEMENT', true);
						
					}
					
				}
				else {
				
					$T->setVar('__disponibilidad_series_name', 'Sin datos');
					$T->parse('disponibilidad_series_element', 'DISPONIBILIDAD_SERIES_ELEMENT', true);
					$T->setVar('__sinnomonitoreo_series_name', 'Sin datos');
					$T->parse('sinnomonitoreo_series_element', 'SINNOMONITOREO_SERIES_ELEMENT', true);
					$conf_pasos = array(0);
				}
				if ($min_value!=100 and $conf_objetivo->getAttribute("sla_disponibilidad_error")<$min_value) {
					$min_value = $conf_objetivo->getAttribute("sla_disponibilidad_error");
				}
				
				/* DISPONIBILIDAD : SLA DEL GRAFICO */
				$T->setVar('tiene_sla_ok', '');
				if ($conf_objetivo->getAttribute("sla_disponibilidad_ok")!="") {
					$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute("sla_disponibilidad_ok"));
					$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', false);
				}
				$T->setVar('tiene_sla_error', '');
				if ($conf_objetivo->getAttribute("sla_disponibilidad_error")!="") {
					$T->setVar('__sla_error_value', $conf_objetivo->getAttribute("sla_disponibilidad_error"));
					$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', false);
				}
				
				/* DISPONIBILIDAD : FORMATO DEL GRAFICO */
				$T->setVar('__bar_series_width', Utiles::getWidthBar($conf_pasos->length));
				$T->setVar('__disponibilidad_scale_minimum', (($min_value-5)<0 or $min_value == 100)?0:floor($min_value-5));
				
				$T->setVar('__chart_name', $conf_nodo->getAttribute('nodo_id'));
				$T->parse('chart_element', 'CHART_ELEMENT', true);
			}
			
		}
		/* SELECCION DE BOTONES DEL GRAFICO */
		$T->setVar('mostrar_scroll', '');
		$T->setVar('__monitor_view_height', ($fila>4)?17.5:(8.5+($fila*3)));
		$T->setVar('__monitor_view_width', ($fila>0)?100:($columna*25));
		$T->setVar('__chart_view_height', ($fila>4)?41.25:(45.75-floor(($fila*3)/2)));
		if ($fila>4) {
			$T->parse('mostrar_scroll', 'MOSTRAR_SCROLL', true);
		}
		
		$this->resultado = $T->parse('out', 'tpl_grafico');
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
	function getErroresDisponibilidad() {
		$xpath = $this->getDatosErroresDisponibilidad();
		$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_codigos = $xpath->query("/atentus/resultados/propiedades/codigos/codigo");
		
		
		/* SI SOLO SE QUIERE OBTENER LA CONFIGURACION */
		if ($this->tipo == "html") {
			$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
			
			if ($xpath->query("//detalles/detalle[@paso_orden]")->length > 0) {
				$this->path_flash = REP_PATH_ACHART;
				$this->ancho = REP_GRAFICO_ANCHO;
				$this->alto = 600;
				$this->resultado = $this->__generarContenedorFlash(false);
				$this->setSubgraficos($conf_pasos);
			}
			else {
				$this->resultado = $this->__generarContenedorSinDatos();
			}
			return;
		}
		
		$arr_colores = array();
		$i=0;
		foreach ($conf_codigos as $error) {
			$arr_colores[$error->getAttribute('codigo_id')] = Utiles::getDefaultColor($i);
			$i++;
		}
		
		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_errores.tpl');
		$T->setBlock('tpl_grafico', 'MOSTRAR_SCROLL', 'mostrar_scroll');
		$T->setBlock('tpl_grafico', 'PASO_POINT_ELEMENT', 'paso_point_element');
		$T->setBlock('tpl_grafico', 'DISPONIBILIDAD_POINT_ELEMENT', 'disponibilidad_point_element');
		$T->setBlock('tpl_grafico', 'DOWNTIME_POINT_ELEMENT', 'downtime_point_element');
		$T->setBlock('tpl_grafico', 'ERRORES_POINT_ELEMENT', 'errores_point_element');
		$T->setBlock('tpl_grafico', 'ERRORES_SERIES_ELEMENT', 'errores_series_element');
		$T->setBlock('tpl_grafico', 'CHART_ELEMENT', 'chart_element');
		
		/* DATOS DEL GRAFICO */
		$fila = 0;
		$columna = 0;
		foreach ($conf_pasos as $conf_paso) {
			if ($this->subgrafico_id == null or $this->subgrafico_id == $conf_paso->getAttribute('paso_orden')) {
				$tag_datos_global = $xpath->query("//detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/estadisticas/estadistica");
				$tag_datos_nodo = $xpath->query("//detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/detalles/detalle");
					
				/* SELECCION DE PASOS : DATOS DEL GRAFICO */
				$T->setVar('__paso_point_row', $fila);
				$T->setVar('__paso_point_column', $columna);
				$T->setVar('__paso_point_value', $conf_paso->getAttribute('paso_orden'));
				$T->setVar('__paso_point_selected', ($fila == 0 && $columna == 0)?'True':'False');
				$T->setVar('__paso_attribute_content', $conf_paso->getAttribute('nombre'));
				$T->parse('paso_point_element', 'PASO_POINT_ELEMENT', true);
				
				$T->setVar('__chart_name', $conf_paso->getAttribute('paso_orden'));
				$T->setVar('__bar_series_width', Utiles::getWidthBar($tag_datos_nodo->length));
				
				$columna ++;
				if ($columna == 4 and ($fila + 1) * 4 != $conf_pasos->length) {
					$columna = 0;
					$fila ++;
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
				$T->setVar('errores_series_element', '');
				if ($tag_datos_nodo->length > 0) {
					foreach ($tag_datos_nodo as $id => $tag_dato_nodo) {
						$conf_nodo = $xpath->query("//nodos/nodo[@nodo_id=".$tag_dato_nodo->getAttribute('nodo_id')."]")->item(0);
						$T->setVar('__downtime_point_name', $conf_nodo->getAttribute('nombre'));
						$T->setVar('__downtime_point_color', Utiles::getDefaultColor($id));
						$T->setVar('__downtime_point_value', $tag_dato_nodo->getAttribute('porcentaje'));
						$T->parse('downtime_point_element', 'DOWNTIME_POINT_ELEMENT', true);
						
						$T->setVar('__errores_series_name', $conf_nodo->getAttribute('nombre'));
						
						$T->setVar('errores_point_element', '');
						foreach($xpath->query("estadisticas/estadistica", $tag_dato_nodo) as $id => $tag_dato){
								
							$conf_codigo = $xpath->query("//codigos/codigo[@codigo_id=".$tag_dato->getAttribute("codigo_id")."]")->item(0);
							$T->setVar('__errores_point_name', $conf_codigo->getAttribute('nombre'));
							$T->setVar('__errores_point_color',  $arr_colores[$conf_codigo->getAttribute('codigo_id')]);
							$T->setVar('__errores_point_value', $tag_dato->getAttribute('porcentaje'));
							$T->parse('errores_point_element', 'ERRORES_POINT_ELEMENT', true);
						}
						$T->parse('errores_series_element', 'ERRORES_SERIES_ELEMENT', true);
					}
				}
				else {
					$T->setVar('__downtime_point_value', 100);
					$T->setVar('__downtime_point_name', 'Sin Errores');
					$T->setVar('__downtime_point_color', 'f0f0f0');
					$T->parse('downtime_point_element', 'DOWNTIME_POINT_ELEMENT', true);
					
					$T->setVar('__errores_series_name', 'Sin Monitores');
					$T->setVar('__errores_point_value', 100);
					$T->setVar('__errores_point_name', 'Sin Errores');
					$T->setVar('__errores_point_color', 'f0f0f0');
					$T->parse('errores_point_element', 'ERRORES_POINT_ELEMENT', true);
					$T->parse('errores_series_element', 'ERRORES_SERIES_ELEMENT', true);
				}
			
				$T->parse('chart_element', 'CHART_ELEMENT', true);
			}
		}
		
		/* SELECCION DE BOTONES DEL GRAFICO */
		$T->setVar('mostrar_scroll', '');
		$T->setVar('__paso_view_height', ($fila>4)?17.5:(8.5+($fila*3)));
		$T->setVar('__paso_view_width', ($fila>0)?100:($columna*25));
		$T->setVar('__chart_view_height', ($fila>4)?41.25:(45.75-floor(($fila*3)/2)));
		if ($fila>4) {
			$T->parse('mostrar_scroll', 'MOSTRAR_SCROLL', true);
		}
		
		$this->resultado = $T->parse('out', 'tpl_grafico');
	}
	
	/**
	 * Funcion para obtener el grafico de
	 * Disponibilidad Historica.
	 *
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
	// TODO: metodo getDisponibilidadConsolidadoHistorico()
	function getHistoricoDisponibilidad() {
		$xpath = $this->getDatosHistoricoDisponibilidad();
		$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		
		/* SI SOLO SE QUIERE OBTENER LA CONFIGURACION */
		if ($this->tipo == "html") {
			if ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length > 0) {
				$this->path_flash = REP_PATH_ACHART;
				$this->ancho = REP_GRAFICO_ANCHO;
				$this->alto = 350;
				$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
				$this->resultado = $this->__generarContenedorFlash(false);
			}
			else {
				$this->resultado = $this->__generarContenedorSinDatos();
			}
			return;
		}
		
		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XMLTEMPLATES);
		$T->setFile('tpl_grafico', 'disponibilidad_historico.tpl');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_TOOLTIP', 'series_tooltip');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		
		/* DATOS DEL GRAFICO */
		$min_value = 100;
		$T->setVar('series_element', '');
		
		foreach ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle") as $tag_fecha) {
			$T->setVar('__series_name', $this->timestamp->getFormatoFecha($tag_fecha->getAttribute('fecha')));
			
			$T->setVar('point_element', '');
			$T->setVar('series_tooltip', '');
			
			foreach ($xpath->query("estadisticas/estadistica", $tag_fecha) as $tag_dato) {
				if ($tag_dato->getAttribute('evento_id') == 1 and $tag_dato->getAttribute('porcentaje') < $min_value) {
					$min_value = $tag_dato->getAttribute('porcentaje');
				}
				$conf_evento = $xpath->query("/atentus/resultados/propiedades/eventos/evento[@evento_id=".$tag_dato->getAttribute('evento_id')."]")->item(0);
				$T->setVar('__point_name', $conf_evento->getAttribute('nombre'));
				$T->setVar('__point_value', $tag_dato->getAttribute('porcentaje'));
				$T->setVar('__point_color', $conf_evento->getAttribute('color'));
				$T->parse('point_element', 'POINT_ELEMENT', true);
				$T->parse('series_tooltip', 'SERIES_TOOLTIP', true);
			}
			
			$T->parse('series_element', 'SERIES_ELEMENT', true);
		}
		
		
		/* SLA DEL GRAFICO */
		$T->setVar('tiene_sla_ok', '');
		if ($conf_objetivo->getAttribute('sla_disponibilidad_ok')!="") {
			$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute('sla_disponibilidad_ok'));
			$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', false);
		}
		$T->setVar('tiene_sla_error', '');
		if ($conf_objetivo->getAttribute('sla_disponibilidad_error')!="") {
			$T->setVar('__sla_error_value', $conf_objetivo->getAttribute('sla_disponibilidad_error'));
			$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', false);
		}
		
		/* FORMATO DEL GRAFICO */
		$T->setVar('__scale_minimum', (($min_value-5)<0 or $min_value==100)?0:floor($min_value-5));
		
		$this->resultado = $T->parse('out', 'tpl_grafico');
	}
	
	/*************** FUNCIONES DE GRAFICOS DE RENDIMIENTO ***************/
	/*************** FUNCIONES DE GRAFICOS DE RENDIMIENTO ***************/
	/*************** FUNCIONES DE GRAFICOS DE RENDIMIENTO ***************/

	/**
	 * Funcion para obtener el grafico de 
	 * Rendimiento Consolidado.
	 * 
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
	
	// TODO: metodo getRendimientoConsolidado()
	function getConsolidadoRendimiento() {
		$xpath = $this->getDatosConsolidadoRendimiento();
		$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
				
		if ($this->tipo == "html") {
			if ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length > 0) {
				$this->path_flash = REP_PATH_ACHART;
				$this->ancho = REP_GRAFICO_ANCHO;
				$this->alto = 350;
				$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
				$this->resultado = $this->__generarContenedorFlash(true);
			}
			else {
				$this->resultado = $this->__generarContenedorSinDatos();
			}
			return;
		}
		
		$inicio = strtotime($this->timestamp->getInicioPeriodo());
		$termino = strtotime($this->timestamp->getTerminoPeriodo());
		$intervalo = $this->intervaloLinea($inicio, $termino);
		
		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_consolidado.tpl');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
		$T->setBlock('tpl_grafico', 'RANGE_ELEMENT', 'range_element');
		
		/* DATOS DEL GRAFICO */
		foreach ($conf_pasos as $conf_paso) {
			$T->setVar('__series_id', $conf_paso->getAttribute('nombre'));
			$T->setVar('__series_name', $conf_paso->getAttribute('nombre'));
			$T->setVar('__series_color', Utiles::getDefaultColor($conf_paso->getAttribute('paso_orden')));
			
			$T->setVar('point_element', '');
			for ($i = $inicio; $i < $termino; $i = $i + $intervalo) {
				$tag_dato = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato[@fecha='".date("Y-m-d\TH:i:s", $i)."']")->item(0);
				$T->setVar('__point_name', date("Y.m.d.H.i.s", $i));
				$T->setVar('__point_value', ($tag_dato==null || $tag_dato->getAttribute("respuesta") == "S/I")?"":$tag_dato->getAttribute("respuesta"));
				$T->parse('point_element', 'POINT_ELEMENT', true);
			}
			$T->parse('series_element', 'SERIES_ELEMENT', true);
		}
		
		$min = $xpath->evaluate("//dato/@respuesta[not(. > //dato/@respuesta)][1]")->item(0)->value;
		$max = $xpath->evaluate("//dato/@respuesta[not(. < //dato/@respuesta)][1]")->item(0)->value;
		
		/* SLA DEL GRAFICO */
		$T->setVar('tiene_sla_ok', '');
		if ($conf_objetivo->getAttribute('sla_rendimiento_ok')!="" and $conf_objetivo->getAttribute('sla_rendimiento_ok') > $min and $conf_objetivo->getAttribute('sla_rendimiento_ok') < $max) {
			$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute('sla_rendimiento_ok'));
			$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', false);
		}
		$T->setVar('tiene_sla_error', '');
		if ($conf_objetivo->getAttribute('sla_rendimiento_error')!="" and $conf_objetivo->getAttribute('sla_rendimiento_error') > $min and $conf_objetivo->getAttribute('sla_rendimiento_error') < $max) {
			$T->setVar('__sla_error_value', $conf_objetivo->getAttribute('sla_rendimiento_error'));
			$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', false);
		}
		
		$T->setVar('range_element', '');
		foreach ($xpath->query("/atentus/resultados/propiedades/horarios_habiles/horario_habil") as $conf_horario) {
			$T->setVar('__range_minimum', date('Y.m.d.H.i.s', strtotime($conf_horario->getAttribute('inicio'))));
			$T->setVar('__range_maximum', date('Y.m.d.H.i.s', strtotime($conf_horario->getAttribute('termino'))));
			$T->parse('range_element', 'RANGE_ELEMENT', true);
		}
		
		/* FORMATO DEL GRAFICO */
		$maximo_escala = $_SESSION["valor_escala_".$conf_objetivo->getAttribute('objetivo_id')."_".$this->__item_id];
		$T->setVar('__y_scale_maximum', ($maximo_escala>0)?$maximo_escala:"auto");
		$T->setVar('__y_scale_minimun', ($maximo_escala>0)?"0":"auto");
		$T->setVar('__x_scale_minimun', $this->timestamp->getInicioPeriodo("Y.m.d.H.i.s"));
		$T->setVar('__x_scale_maximun', $this->timestamp->getTerminoPeriodo("Y.m.d.H.i.s"));
		$T->setVar('__x_major_interval_unit', ($this->timestamp->tipo_periodo==REP_PRD_DAY)?'Hour':'Day');
		$T->setVar('__x_format_value', ($this->timestamp->tipo_periodo==REP_PRD_DAY)?'%HH:%mm':'%dd/%MM');
		$this->resultado = $T->parse('out', 'tpl_grafico');
	}

	/**
	 * Funcion para obtener el grafico de 
	 * Estadisticas y Detalle Rendimiento.
	 * 
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
	// TODO: metodo getRendimientoPorNodo()
	function getEstadisticoRendimiento() {
		$xpath = $this->getDatosEstadisticoRendimiento();
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		$conf_nodos = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id!=0]");
		$conf_horarios = $xpath->query("/atentus/resultados/propiedades/horarios_habiles/horario_habil");
		
		/* SI SOLO SE QUIERE OBTENER LA CONFIGURACION */
		if ($this->tipo == "html") {
			if ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length > 0) {
				$this->path_flash = REP_PATH_ACHART;
				$this->ancho = REP_GRAFICO_ANCHO;
				$this->alto = 600;
				$this->setSubgraficos($conf_pasos);
				$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
				$this->resultado = $this->__generarContenedorFlash(true);
			}
			else {
				$this->resultado = $this->__generarContenedorSinDatos();
			}
			return;
		}
		
		$inicio = strtotime($this->timestamp->getInicioPeriodo());
		$termino = strtotime($this->timestamp->getTerminoPeriodo());
		$intervalo = $this->intervaloLinea($inicio, $termino);
		
		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_por_nodo.tpl');
		$T->setBlock('tpl_grafico', 'MOSTRAR_SCROLL', 'mostrar_scroll');
		$T->setBlock('tpl_grafico', 'PASO_POINT_ELEMENT', 'paso_point_element');
		$T->setBlock('tpl_grafico', 'RENDIMIENTO_POINT_ELEMENT', 'rendimiento_point_element');
		$T->setBlock('tpl_grafico', 'RENDIMIENTO_SERIES_ELEMENT', 'rendimiento_series_element');
		$T->setBlock('tpl_grafico', 'TIENE_RENDIMIENTO_SLA_OK', 'tiene_rendimiento_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_RENDIMIENTO_SLA_ERROR', 'tiene_rendimiento_sla_error');
		$T->setBlock('tpl_grafico', 'RENDIMIENTO_RANGE_ELEMENT', 'rendimiento_range_element');
		$T->setBlock('tpl_grafico', 'ESTADISTICA_POINT_ELEMENT', 'estadistica_point_element');
		$T->setBlock('tpl_grafico', 'TIENE_ESTADISTICA_SLA_OK', 'tiene_estadistica_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_ESTADISTICA_SLA_ERROR', 'tiene_estadistica_sla_error');
		$T->setBlock('tpl_grafico', 'ESTADISTICA_RANGE_ELEMENT', 'estadistica_range_element');
		$T->setBlock('tpl_grafico', 'CHART_ELEMENT', 'chart_element');
		
		/* DATOS DEL GRAFICO */
		$fila = 0;
		$columna = 0;
		foreach ($conf_pasos as $conf_paso) { 
			
			if ($this->subgrafico_id == null or $this->subgrafico_id == $conf_paso->getAttribute('paso_orden')) {
				$tag_paso = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]")->item(0);
			
				/* SELECCION DE PASOS : DATOS DEL GRAFICO */
				$T->setVar('__paso_point_row', $fila);
				$T->setVar('__paso_point_column', $columna);
				$T->setVar('__paso_point_value', $conf_paso->getAttribute('paso_orden'));
				$T->setVar('__paso_point_selected',($fila == 0 && $columna == 0)?'True':'False');
				$T->setVar('__paso_attribute_content', $conf_paso->getAttribute('nombre'));
				$T->parse('paso_point_element', 'PASO_POINT_ELEMENT', true);
				$columna ++;
				if ($columna == 4 AND ($fila + 1) * 4 != $conf_pasos->length){
					$columna = 0;
					$fila ++;
				}
				
				/* RENDIMIENTO : DATOS DEL GRAFICO */
				$T->setVar('rendimiento_series_element', '');
				foreach ($conf_nodos as $conf_nodo) {
					$T->setVar('__rendimiento_series_id', $conf_nodo->getAttribute('nombre'));
					$T->setVar('__rendimiento_series_name', $conf_nodo->getAttribute('nombre'));
					$T->setVar('__rendimiento_series_color', Utiles::getDefaultColor($conf_nodo->getAttribute('nodo_id')));
			
					$T->setVar('rendimiento_point_element', '');
					for ($i = $inicio; $i < $termino; $i = $i + $intervalo) {
						$tag_dato = $xpath->query("detalles/detalle[@nodo_id=".$conf_nodo->getAttribute('nodo_id')."]/datos/dato[@fecha='".date("Y-m-d\TH:i:s", $i)."']", $tag_paso)->item(0);
						$T->setVar('__rendimiento_point_name', date("Y.m.d.H.i.s", $i));
						$T->setVar('__rendimiento_point_value', ($tag_dato==null)?"":$tag_dato->getAttribute("respuesta"));
						$T->parse('rendimiento_point_element', 'RENDIMIENTO_POINT_ELEMENT', true);
					}
					$T->parse('rendimiento_series_element', 'RENDIMIENTO_SERIES_ELEMENT', true);
				}
				
				$min = $xpath->evaluate("detalles/detalle/dato/@respuesta[not(. > detalles/detalle/dato/@respuesta)][1]", $tag_paso)->item(0)->value;
				$max = $xpath->evaluate("detalles/detalle/dato/@respuesta[not(. < detalles/detalle/dato/@respuesta)][1]", $tag_paso)->item(0)->value;
				
				/* RENDIMIENTO : SLA DEL GRAFICO */
				$T->setVar('tiene_rendimiento_sla_ok', '');
				if ($conf_objetivo->getAttribute('sla_rendimiento_ok')!="" and $conf_objetivo->getAttribute('sla_rendimiento_ok') > $min and $conf_objetivo->getAttribute('sla_rendimiento_ok') < $max) {
					$T->setVar('__rendimiento_sla_ok_value', $conf_objetivo->getAttribute('sla_rendimiento_ok'));
					$T->parse('tiene_rendimiento_sla_ok', 'TIENE_RENDIMIENTO_SLA_OK', false);
				}
				$T->setVar('tiene_rendimiento_sla_error', '');
				if ($conf_objetivo->getAttribute('sla_rendimiento_error')!="" and $conf_objetivo->getAttribute('sla_rendimiento_error') > $min and $conf_objetivo->getAttribute('sla_rendimiento_error') < $max) {
					$T->setVar('__rendimiento_sla_error_value', $conf_objetivo->getAttribute('sla_rendimiento_error'));
					$T->parse('tiene_rendimiento_sla_error', 'TIENE_RENDIMIENTO_SLA_ERROR', false);
				}
				
				/* RENDIMIENTO : HORARIO HABIL DEL GRAFICO */
				$T->setVar('rendimiento_range_element', '');
				foreach ($conf_horarios as $conf_horario) {
					$T->setVar('__range_minimum', date('Y,m,d,H,i,s', strtotime($conf_horario->getAttribute('inicio'))));
					$T->setVar('__range_maximum', date('Y,m,d,H,i,s', strtotime($conf_horario->getAttribute('termino'))));
					$T->parse('rendimiento_range_element', 'RENDIMIENTO_RANGE_ELEMENT', true);
				}
				
				$tag_estadistica = $xpath->query("estadistica", $tag_paso)->item(0);
				
				/* ESTADISTICAS : DATOS DEL GRAFICO */
				if ($tag_estadistica != null) {
					$T->setVar('__estadistica_prom_line_value', $tag_estadistica->getAttribute('promedio'));
					$T->setVar('__estadistica_range_minimum', ($tag_estadistica->getAttribute('promedio') < $tag_estadistica->getAttribute('desviacion'))?'0':($tag_estadistica->getAttribute('promedio') - $tag_estadistica->getAttribute('desviacion')));
					$T->setVar('__estadistica_range_maximum', $tag_estadistica->getAttribute('promedio') + $tag_estadistica->getAttribute('desviacion'));
				}
				else {
					$T->setVar('__estadistica_prom_line_value', 0);
					$T->setVar('__estadistica_range_minimum', 0);
					$T->setVar('__estadistica_range_maximum', 0);
				}
				
				$T->setVar('__estadistica_series_id', $conf_paso->getAttribute('nombre'));
				$T->setVar('__estadistica_series_name', $conf_paso->getAttribute('nombre'));
				$T->setVar('__estadistica_series_color', Utiles::getDefaultColor($conf_paso->getAttribute('paso_orden')));
				$T->setVar('estadistica_point_element', '');
				
				$T->setVar('estadistica_point_element', '');
				for ($i = $inicio; $i < $termino; $i = $i + $intervalo) {
					$tag_dato = $xpath->query("datos/dato[@fecha='".date("Y-m-d\TH:i:s", $i)."']", $tag_paso)->item(0);
					$T->setVar('__estadistica_point_name', date("Y.m.d.H.i.s", $i));
					$T->setVar('__estadistica_point_value', ($tag_dato==null)?"":$tag_dato->getAttribute("respuesta"));
					$T->parse('estadistica_point_element', 'ESTADISTICA_POINT_ELEMENT', true);
				}
				
				$min = $xpath->evaluate("dato/@respuesta[not(. > dato/@respuesta)][1]", $tag_paso)->item(0)->value;
				$max = $xpath->evaluate("dato/@respuesta[not(. < dato/@respuesta)][1]", $tag_paso)->item(0)->value;
				
				/* ESTADISTICAS : SLA DEL GRAFICO */
				$T->setVar('tiene_estadistica_sla_ok', '');
				if ($conf_objetivo->getAttribute('sla_rendimiento_ok')!="" and $conf_objetivo->getAttribute('sla_rendimiento_ok') > $min and $conf_objetivo->getAttribute('sla_rendimiento_ok') < $max) {
					$T->setVar('__estadistica_sla_ok_value', $conf_objetivo->getAttribute('sla_rendimiento_ok'));
					$T->parse('tiene_estadistica_sla_ok', 'TIENE_ESTADISTICA_SLA_OK', false);
				}
				$T->setVar('tiene_estadistica_sla_error', '');
				if ($conf_objetivo->getAttribute('sla_rendimiento_error')!="" and $conf_objetivo->getAttribute('sla_rendimiento_error') > $min and $conf_objetivo->getAttribute('sla_rendimiento_error') < $max) {
					$T->setVar('__estadistica_sla_error_value', $conf_objetivo->getAttribute('sla_rendimiento_error'));
					$T->parse('tiene_estadistica_sla_error', 'TIENE_ESTADISTICA_SLA_ERROR', false);
				}
				
				/* ESTADISTICAS : HORARIO HABIL DEL GRAFICO */
				$T->setVar('estadistica_range_element', '');
				foreach ($conf_horarios as $conf_horario) {
					$T->setVar('__range_minimum', date('Y,m,d,H,i,s', strtotime($conf_horario->getAttribute('inicio'))));
					$T->setVar('__range_maximum', date('Y,m,d,H,i,s', strtotime($conf_horario->getAttribute('termino'))));
					$T->parse('estadistica_range_element', 'ESTADISTICA_RANGE_ELEMENT', true);
				}
				
				$T->setVar('__chart_name', $conf_paso->getAttribute('paso_orden'));
				$T->parse('chart_element', 'CHART_ELEMENT', true);
			}
			
		}
		/* FORMATO DEL GRAFICO */
		$maximo_escala = $_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id];
		$T->setVar('__y_scale_maximum', ($maximo_escala>0)?$maximo_escala:"auto");
		$T->setVar('__y_scale_minimun', ($maximo_escala>0)?"0":"auto");
		
		$T->setVar('__x_scale_minimun', $this->timestamp->getInicioPeriodo("Y.m.d.H.i.s"));
		$T->setVar('__x_scale_maximun', $this->timestamp->getTerminoPeriodo("Y.m.d.H.i.s"));
		$T->setVar('__x_major_interval_unit', ($this->timestamp->tipo_periodo==REP_PRD_DAY)?'Hour':'Day');
		$T->setVar('__x_format_value', ($this->timestamp->tipo_periodo==REP_PRD_DAY)?'%HH:%mm':'%dd/%MM');
		
		/* SELECCION DE BOTONES DEL GRAFICO */
		$T->setVar('mostrar_scroll', '');
		$T->setVar('__paso_view_height', ($fila>4)?17.5:(8.5+($fila*3)));
		$T->setVar('__paso_view_width', ($fila>0)?100:($columna*25));
		$T->setVar('__chart_view_height', ($fila>4)?41.25:(45.75-floor(($fila*3)/2)));
		
		if ($fila>4) {
			$T->parse('mostrar_scroll', 'MOSTRAR_SCROLL', true);
		}
		
		$this->resultado = $T->parse('out', 'tpl_grafico');
	}
	
	function getRendimientoPorDia() {
		global $dias_semana;
	
		$xpath = $this->getDatosRendimientoPorDia2();
		$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		
		if ($this->tipo == "html") {
 			if ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length > 0) {
				$this->path_flash = REP_PATH_ACHART;
				$this->ancho = REP_GRAFICO_ANCHO;
				$this->alto = 350;
				$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
				$this->resultado = $this->__generarContenedorFlash(true);
			}
			else {
				$this->resultado = $this->__generarContenedorSinDatos();
			}
			return;
		}
		
		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_por_dia.tpl');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		
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
					$T->setVar('__point_value', ($tag_dato == null)?'':$tag_dato->getAttribute('tiempo_prom'));
					$T->setVar('__point_name', $nombre." ".(($hora==0)?'':$hora_text));
					$T->parse('point_element', 'POINT_ELEMENT', true);
				}
			}
			$T->parse('series_element', 'SERIES_ELEMENT', true);
		}
		
		$maximo_escala = $_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id];
		$T->setVar('__y_scale_maximum', ($maximo_escala>0)?$maximo_escala:"auto");
		$T->setVar('__y_scale_minimun', ($maximo_escala>0)?"0":"auto");
		
		$this->resultado = $T->parse('out', 'tpl_grafico');
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
	function getHistoricoRendimiento() {
		global $usr;
	
		$horarios = array($usr->getHorario(0));
		if ($this->horario_id != 0) {
			$horarios[] = $usr->getHorario($this->horario_id);
			$this->horario_id = 0;
		}
		
		$xpath = $this->getDatosHistoricoRendimiento();
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		
		/* SI SOLO SE QUIERE OBTENER LA CONFIGURACION */
		if ($this->tipo == "html") {
			if ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length > 0 and $conf_pasos->length > 0) {
				$this->path_flash = REP_PATH_ACHART;
				$this->ancho = REP_GRAFICO_ANCHO;
				$this->alto = (count($horarios)==1)?350:600;
				$this->resultado = $this->__generarContenedorFlash(true);
				$this->__subgraficos = array($this->horario_id);
			}
			else {
				$this->resultado = $this->__generarContenedorSinDatos();
			}
			return;
		}
		
		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_historico.tpl');
		$T->setBlock('tpl_grafico', 'BLOQUE_DASHBOARD', 'bloque_dashboard');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
		$T->setBlock('tpl_grafico', 'BLOQUE_GRAFICOS','bloque_graficos');
		
		foreach ($horarios as $horario) {
			if ($horario->horario_id != 0) {
				$this->horario_id = $horario->horario_id;
				$xpath = $this->getDatosHistoricoRendimiento();
			}
			
			$T->setVar('__view_nombre', "view_".$horario->horario_id);
			$T->setVar('__view_height', (count($horarios)==1)?"100%":"50%");
			$T->parse('bloque_dashboard', 'BLOQUE_DASHBOARD', true);
			$T->setVar('__nombreHorario', $horario->nombre);
			$T->setVar('__tieneTitulo', (count($horarios)==1)?"false":"true");
			
			/* DATOS DEL GRAFICO */
			$T->setVar('series_element', '');
			foreach ($conf_pasos as $conf_paso) {
				$T->setVar('__series_id', $conf_paso->getAttribute('nombre'));
				$T->setVar('__series_name', $conf_paso->getAttribute('nombre'));
				$T->setVar('__series_color', Utiles::getDefaultColor($conf_paso->getAttribute('paso_orden')));
				
				$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$conf_objetivo->getAttribute('objetivo_id')."]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato");
				$T->setVar('point_element', '');
				foreach ($tag_datos as $tag_dato) {
					$T->setVar('__point_name', $this->timestamp->getFormatearFecha($tag_dato->getAttribute('fecha'), "Y.m.d.H.i.s"));
					$T->setVar('__point_value', $tag_dato->getAttribute('respuesta'));
					$T->parse('point_element', 'POINT_ELEMENT', true);
				}
				$T->parse('series_element', 'SERIES_ELEMENT', true);
			}
			
			$min_y = $xpath->evaluate("//dato/@respuesta[not(. > //dato/@respuesta)][1]")->item(0)->value;
			$max_y = $xpath->evaluate("//dato/@respuesta[not(. < //dato/@respuesta)][1]")->item(0)->value;
			
			$T->setVar('tiene_sla_ok', '');
			if ($conf_objetivo->getAttribute('sla_rendimiento_ok')!="" and $conf_objetivo->getAttribute('sla_rendimiento_ok') > $min_y and $conf_objetivo->getAttribute('sla_rendimiento_ok') < $max_y) {
				$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute('sla_rendimiento_ok'));
				$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', false);
			}
			$T->setVar('tiene_sla_error', '');
			if ($conf_objetivo->getAttribute('sla_rendimiento_error')!=""and $conf_objetivo->getAttribute('sla_rendimiento_error') > $min_y and $conf_objetivo->getAttribute('sla_rendimiento_error') < $max_y) {
				$T->setVar('__sla_error_value', $conf_objetivo->getAttribute('sla_rendimiento_error'));
				$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', false);
			}
			
			/* FORMATO DEL GRAFICO */
			$maximo_escala = $_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id];
			$T->setVar('__y_scale_maximum', ($maximo_escala>0)?$maximo_escala:"auto");
			$T->setVar('__y_scale_minimun', ($maximo_escala>0)?"0":"auto");
			
			$T->setVar('__x_scale_minimun', $this->timestamp->getInicioPeriodoHistorico("Y.m.d.H.i.s"));
			$T->setVar('__x_scale_maximun', $this->timestamp->getTerminoPeriodo("Y.m.d.H.i.s"));
			$T->setVar('__x_major_interval', ($this->timestamp->tipo_periodo==REP_PRD_WEEK)?'7':'1');
			$T->setVar('__x_major_interval_unit', ($this->timestamp->tipo_periodo==REP_PRD_MONTH or $this->timestamp->tipo_periodo == "especial")?'Month':'Day');
			$T->setVar('__x_format_value', ($this->timestamp->tipo_periodo==REP_PRD_MONTH)?'%MM/%yyyy':'%dd/%MM');
			$T->parse('bloque_graficos', 'BLOQUE_GRAFICOS', true);
		}
		
		$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
		$this->resultado = $T->parse('out', 'tpl_grafico');
	}
	
	/**
	 * Funcion para obtener el grafico de 
	 * Frecuencia de Rendimiento.
	 * 
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
	// TODO: metodo getRendimientoFrecuencia()
	
	function getFrecuenciaRendimiento() {
		$xpath = $this->getDatosFrecuenciaRendimiento();
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
	
		/* SI SOLO SE QUIERE OBTENER LA CONFIGURACION */
		if ($this->tipo == "html") {
			if ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length > 0) {
				$this->path_flash = REP_PATH_ACHART;
				$this->ancho = REP_GRAFICO_ANCHO;
				$this->alto = 350;
				$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
				$this->resultado = $this->__generarContenedorFlash(true);
			}
			else {
				$this->resultado = $this->__generarContenedorSinDatos();
			}
			return;
		}
	
		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_frecuencia.tpl');
		$T->setBlock('tpl_grafico', 'TIENE_ZOOM', 'tiene_zoom');
		$T->setBlock('tpl_grafico', 'TIENE_BOTONES_ZOOM', 'tiene_botones_zoom');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
	
		/* DATOS DEL GRAFICO */
		foreach ($conf_pasos as $conf_paso) {
			$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato");
			
			$T->setVar('__series_id', $conf_paso->getAttribute('nombre'));
			$T->setVar('__series_name', $conf_paso->getAttribute('nombre'));
			$T->setVar('__series_color', Utiles::getDefaultColor($conf_paso->getAttribute('paso_orden')));

			$T->setVar('point_element', '');
			
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
		
		if ($this->subgrafico_id != null or $tag_datos->length< 4) {
			$T->setVar('tiene_zoom', '');
			$T->setVar('tiene_botones_zoom', '');
		}
		else {
			$T->parse('tiene_zoom', 'TIENE_ZOOM', false);
			$T->parse('tiene_botones_zoom', 'TIENE_BOTONES_ZOOM', false);
		}

		$min_y = $xpath->evaluate("//dato/@cantidad[not(. > //dato/@cantidad)][1]")->item(0)->value;
		$max_y = $xpath->evaluate("//dato/@cantidad[not(. < //dato/@cantidad)][1]")->item(0)->value;
		$min_x = $xpath->evaluate("//dato/@respuesta[not(. > //dato/@respuesta)][1]")->item(0)->value;
		$max_x = $xpath->evaluate("//dato/@respuesta[not(. < //dato/@respuesta)][1]")->item(0)->value;
		
		$maximo_escala = $_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id];
		if ($maximo_escala > 0) {
			$max_x = $maximo_escala;
		}
		
		$T->setVar('__marker', ($tag_datos->length > 1)?'false':'true');
		$T->setVar('__x_scale_avg', (($max_x-$min_x)/4)+$min_x);
		$T->setVar('__x_scale_maximum', $max_x*1.05);
		$T->setVar('__x_scale_minimum', ($min_x<$max_x*0.05)?0:($min_x-$max_x*0.05));
		$T->setVar('__y_scale_maximum', $max_y*1.05);
		$T->setVar('__y_scale_minimum', ($min_y<$max_y*0.05)?0:($min_y-$max_y*0.05));
	
		/* SLA DEL GRAFICO */
		$T->setVar('tiene_sla_ok', '');
		if ($conf_objetivo->getAttribute('sla_rendimiento_ok')!="" and $conf_objetivo->getAttribute('sla_rendimiento_ok') > ($min_x-1) and $conf_objetivo->getAttribute('sla_rendimiento_ok') < ($max_x+1)) {
			$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute('sla_rendimiento_ok'));
			$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', false);
		}
		$T->setVar('tiene_sla_error', '');
		if ($conf_objetivo->getAttribute('sla_rendimiento_error')!="" and $conf_objetivo->getAttribute('sla_rendimiento_error') > ($min_x-1) and $conf_objetivo->getAttribute('sla_rendimiento_error') < ($max_x+1)) {
			$T->setVar('__sla_error_value', $conf_objetivo->getAttribute('sla_rendimiento_error'));
			$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', false);
		}
	
		$this->resultado = $T->parse('out', 'tpl_grafico');
	}
	
	/**
	 * Funcion para obtener el grafico de 
	 * Frecuencia de Rendimiento Acumulado.
	 * 
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
	// TODO: metodo getRendimientoFrecuenciaAcumulada()
	
	function getFrecuenciaAculumada() {
		$xpath = $this->getDatosFrecuenciaAculumada();
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		
		/* SI SOLO SE QUIERE OBTENER LA CONFIGURACION */
		if ($this->tipo == "html") {
			if ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length > 0) {
				$this->path_flash = REP_PATH_ACHART;
				$this->ancho = REP_GRAFICO_ANCHO;
				$this->alto = 350;
				$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
				$this->resultado = $this->__generarContenedorFlash(true);
			}
			else {
				$this->resultado = $this->__generarContenedorSinDatos();
			}
			return;
		}
		
		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_frecuencia_acumulada.tpl');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_OK', 'tiene_sla_ok');
		$T->setBlock('tpl_grafico', 'TIENE_SLA_ERROR', 'tiene_sla_error');
		
		/* DATOS DEL GRAFICO */
		foreach ($conf_pasos as $conf_paso) {
			$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/datos/dato");
			
			$T->setVar('__series_id', $conf_paso->getAttribute('nombre'));
			$T->setVar('__series_name', $conf_paso->getAttribute('nombre'));
			$T->setVar('__series_color', Utiles::getDefaultColor($conf_paso->getAttribute('paso_orden')));
			
			$T->setVar('point_element', '');
			
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
		
		$min_y = $xpath->evaluate("//dato/@cantidad[not(. > //dato/@cantidad)][1]")->item(0)->value;
		$max_y = $xpath->evaluate("//dato/@cantidad[not(. < //dato/@cantidad)][1]")->item(0)->value;
		$min_x = $xpath->evaluate("//dato/@respuesta[not(. > //dato/@respuesta)][1]")->item(0)->value;
		$max_x = $xpath->evaluate("//dato/@respuesta[not(. < //dato/@respuesta)][1]")->item(0)->value;
		
		$maximo_escala = $_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id];
		if ($maximo_escala > 0) {
			$max_x = $maximo_escala;
		}
		
		$T->setVar('__marker', ($tag_datos->length > 1)?'false':'true');
		$T->setVar('__x_scale_avg', (($max_x-$min_x)/4)+$min_x);
		$T->setVar('__x_scale_maximum', $max_x*1.05);
		$T->setVar('__x_scale_minimum', ($min_x<$max_x*0.05)?0:($min_x-$max_x*0.05));
		$T->setVar('__y_scale_maximum', $max_y*1.05);
		$T->setVar('__y_scale_minimum', ($min_y<$max_y*0.05)?0:($min_y-$max_y*0.05));
		
		/* SLA DEL GRAFICO */
		$T->setVar('tiene_sla_ok', '');
		if ($conf_objetivo->getAttribute('sla_rendimiento_ok')!="" and $conf_objetivo->getAttribute('sla_rendimiento_ok') > ($minimo-1) and $conf_objetivo->getAttribute('sla_rendimiento_ok') < ($maximo+1)) {
 			$T->setVar('__sla_ok_value', $conf_objetivo->getAttribute('sla_rendimiento_ok'));
			$T->parse('tiene_sla_ok', 'TIENE_SLA_OK', false);
		}
		$T->setVar('tiene_sla_error', '');
		if ($conf_objetivo->getAttribute('sla_rendimiento_error')!="" and $conf_objetivo->getAttribute('sla_rendimiento_error') > ($minimo-1) and $conf_objetivo->getAttribute('sla_rendimiento_error') < ($maximo+1)) {
 			$T->setVar('__sla_error_value', $conf_objetivo->getAttribute('sla_rendimiento_error'));
			$T->parse('tiene_sla_error', 'TIENE_SLA_ERROR', false);
		}
		
		$this->resultado = $T->parse('out', 'tpl_grafico');
	}
	
	

	/*************** FUNCIONES DE GRAFICOS DE SLA ***************/
	/*************** FUNCIONES DE GRAFICOS DE SLA ***************/
	/*************** FUNCIONES DE GRAFICOS DE SLA ***************/
	
	/**
	 * Funcion para obtener el grafico de 
	 * Superacion de SLA Detallado.
	 * 
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
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
		
		/* SI SOLO SE QUIERE OBTENER LA CONFIGURACION */
		if ($this->tipo == "html") {
			if ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length > 0) {
				$this->path_flash = REP_PATH_ACHART;
				$this->ancho = REP_GRAFICO_ANCHO;
				$this->alto = 350;
				$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
				$this->resultado = $this->__generarContenedorFlash(false);
			}
			else {
				$this->resultado = $this->__generarContenedorSinDatos();
			}
			return;
		}
		
		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_sla.tpl');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		
		/* DATOS DEL GRAFICO */
		$T->setVar('__bar_series_width', Utiles::getWidthBar(count($objetivo->__pasos)));
		
		$T->setVar('series_element', '');
		foreach ($conf_pasos as $conf_paso) {
			$T->setVar('__series_name', ($conf_pasos>15)?$conf_paso->getAttribute('paso_orden'):substr($conf_paso->getAttribute('nombre'), 0, 35));
			$T->setVar('__paso_nombre', $conf_paso->getAttribute('nombre'));
			$T->setVar('point_element', '');
			
			foreach ($conf_slas as $conf_sla) {
				$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/estadisticas/estadistica[@sla_id=".$conf_sla->getAttribute('sla_id')."]");
								
				$T->setVar('__point_name', $conf_sla->getAttribute('nombre'));
				$T->setVar('__point_color', $conf_sla->getAttribute('color'));
				$T->setVar('__point_value', ($tag_datos->length==0)?0:$tag_datos->item(0)->getAttribute('porcentaje'));
				$T->parse('point_element', 'POINT_ELEMENT', true);
			}
			$T->parse('series_element', 'SERIES_ELEMENT', true);
		}			
		$this->resultado = $T->parse('out', 'tpl_grafico');
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
		
		
		/* SI SOLO SE QUIERE OBTENER LA CONFIGURACION */
		if ($this->tipo == "html") {
			if ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length > 0) {
				$this->path_flash = REP_PATH_ACHART;
				$this->ancho = REP_GRAFICO_ANCHO;
				$this->alto = 350;
				$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
				$this->resultado = $this->__generarContenedorFlash(false);
			}
			else {
				$this->resultado = $this->__generarContenedorSinDatos();
			}
			return;
		}
		
		/* TEMPLATE DEL GRAFICO */
		$T = & new Template_PHPLIB(REP_PATH_XMLTEMPLATES);
		$T->setFile('tpl_grafico', 'rendimiento_sla_historico.tpl');
		$T->setBlock('tpl_grafico', 'POINT_ELEMENT', 'point_element');
		$T->setBlock('tpl_grafico', 'SERIES_ELEMENT', 'series_element');
		
		/* DATOS DEL GRAFICO */
		$T->setVar('series_element', '');
		foreach ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle") as $conf_fecha) {
			$T->setVar('__series_name', $this->timestamp->getFormatoFecha($conf_fecha->getAttribute('fecha')));
			
			$T->setVar('point_element', '');
			foreach ($xpath->query("estadisticas/estadistica", $conf_fecha) as $tag_dato){

				$conf_sla = $xpath->query("/atentus/resultados/propiedades/slas/sla[@sla_id=".$tag_dato->getAttribute('sla_id')."]")->item(0);
				$T->setVar('__point_name', $conf_sla->getAttribute('nombre'));
				$T->setVar('__point_color', $conf_sla->getAttribute('color'));
				$T->setVar('__point_value', $tag_dato->getAttribute('porcentaje'));
				$T->parse('point_element', 'POINT_ELEMENT', true);
			}
			$T->parse('series_element', 'SERIES_ELEMENT', true);
		}	
		$this->resultado = $T->parse('out', 'tpl_grafico');
	}
	
	/*************** FUNCIONES DE OTROS GRAFICOS ***************/
	/*************** FUNCIONES DE OTROS GRAFICOS ***************/
	/*************** FUNCIONES DE OTROS GRAFICOS ***************/
	
	/**
	 * Funcion para obtener el grafico de 
	 * Comparativo.
	 * 
	 * @param integer $objetivo_id
	 * @param Timestamp $timestamp
	 * @param integer $horario_id
	 */
	function getComparativo() {
		$xpath = $this->getDatosComparativo();
		$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
		
		/* SI SOLO SE QUIERE OBTENER LA CONFIGURACION */
		if ($this->tipo == "html") {
			if ($xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle")->length > 0) {
				$this->path_flash = REP_PATH_ACHART;
				$this->ancho = REP_GRAFICO_ANCHO;
				$this->alto = 350;
				$this->setSubgraficos($conf_pasos);
				$this->tiempo_expiracion = (strtotime($xpath->query("/atentus/resultados/fecha_expiracion")->item(0)->nodeValue) - strtotime($xpath->query("/atentus/resultados/fecha")->item(0)->nodeValue));
				$this->resultado = $this->__generarContenedorFlash(true);
			}
			else {
				$this->resultado = $this->__generarContenedorSinDatos();
			}
			return;
		}
		
		/* TEMPLATE DEL GRAFICO */
		$T =& new Template_PHPLIB(REP_PATH_XMLTEMPLATES);
		$T->setFile('tpl_grafico', 'comparativo.tpl');
		$T->setBlock('tpl_grafico', 'MOSTRAR_SCROLL', 'mostrar_scroll');
		$T->setBlock('tpl_grafico', 'PASO_POINT_ELEMENT', 'paso_point_element');
		$T->setBlock('tpl_grafico', 'DISPONIBILIDAD_POINT_ELEMENT', 'disponibilidad_point_element');
		$T->setBlock('tpl_grafico', 'RENDIMIENTO_POINT_ELEMENT', 'rendimiento_point_element');
		$T->setBlock('tpl_grafico', 'CHART_ELEMENT', 'chart_element');
		
		/* DATOS DEL GRAFICO */
		$fila = 0;
		$columna = 0;
		foreach ($conf_pasos as $conf_paso){

			if ($this->subgrafico_id == null or $this->subgrafico_id == $conf_paso->getAttribute('paso_orden')) {

				/* SELECCION DE PASOS : DATOS DEL GRAFICO */
				$T->setVar('__paso_point_row', $fila);
				$T->setVar('__paso_point_column', $columna);
				$T->setVar('__paso_point_value', $conf_paso->getAttribute('paso_orden'));
				$T->setVar('__paso_point_selected', ($fila==0&&$columna==0)?'True':'False');
				$T->setVar('__paso_attribute_content', $conf_paso->getAttribute('nombre'));
				$T->parse('paso_point_element', 'PASO_POINT_ELEMENT', true);
				$columna++;

				if ($columna==4 and ($fila+1)*4 != $conf_pasos->length) {
					$columna = 0;
					$fila++;
				}
				
				if ($_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id] > 0) {
					$max_y = $_SESSION["valor_escala_".$this->objetivo_id."_".$this->__item_id] * 0.80;
				}
				else {
					$max_y = $xpath->evaluate("//detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/estadisticas/estadistica/@respuesta[not(. < //detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/estadisticas/estadistica/@respuesta)][1]")->item(0)->value;
				}
				
				$T->setVar('__extra_y_maximum', ($max_y > 0.15)?(round(($max_y / 8), 2) * 10):0.2);				
				$T->setVar('__bar_series_width', Utiles::getWidthBar($tag_datos->lenth));
				$T->setVar('__chart_name', $conf_paso->getAttribute('paso_orden'));
		
				/* COMPARATIVO : DATOS DEL GRAFICO */
				$T->setVar('disponibilidad_point_element', '');
				$T->setVar('rendimiento_point_element', '');
				$tag_datos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/estadisticas/estadistica");
				foreach ($tag_datos as $tag_dato) {
					$T->setVar('__disponibilidad_point_name', $tag_dato->getAttribute('fecha'));
					$T->setVar('__disponibilidad_point_value', $tag_dato->getAttribute('uptime'));
					$T->parse('disponibilidad_point_element', 'DISPONIBILIDAD_POINT_ELEMENT', true);
					
					$T->setVar('__rendimiento_point_name', $tag_dato->getAttribute('fecha'));
					$T->setVar('__rendimiento_point_value', $tag_dato->getAttribute('respuesta'));
					$T->parse('rendimiento_point_element', 'RENDIMIENTO_POINT_ELEMENT', true);
				}
				$T->parse('chart_element', 'CHART_ELEMENT', true);
			}
		}

		/* SELECCION DE BOTONES DEL GRAFICO */
		$T->setVar('mostrar_scroll', '');
		$T->setVar('__paso_view_height', ($fila>4)?30:(15+($fila*5)));
		$T->setVar('__paso_view_width', ($fila>0)?100:($columna*25));
		$T->setVar('__chart_view_height', ($fila>4)?70:(85-($fila*5)));
		if ($fila>4) {
			$T->parse('mostrar_scroll', 'MOSTRAR_SCROLL', true);
		}
		$this->resultado = $T->parse('out', 'tpl_grafico');
	}
	
	function setSubgraficos($subgraficos) {
		foreach ($subgraficos as $subgrafico) {
			$this->__subgraficos[] = ($subgrafico->getAttribute("nodo_id") != null)?$subgrafico->getAttribute("nodo_id"):$subgrafico->getAttribute("paso_orden");
		}
	}
	
	private function __generarContenedorFlash($tiene_escala) {
		/* TEMPLATE DE LA TABLA */
		$T = & new Template_PHPLIB(REP_PATH_TEMPLATES);
		$T->setFile('tpl_tabla', 'contenedor_grafico_flash.tpl');
		$T->setVar('__chart_item', $this->__item_id);
		$T->setVar('__chart_path_flash', $this->path_flash);
		$T->setVar('__chart_ancho', $this->ancho);
		$T->setVar('__chart_alto', $this->alto);
		$T->setVar('__parent_objetivo_id', $this->extra["parent_objetivo_id"]);
		$T->setVar('__reporte_informe_subtipo_id', $this->timestamp->tipo_id);
		$T->setVar('__chart_escala', ($tiene_escala and $this->extra["imprimir"] != 2)?$this->generarEscala():'');
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

}

?>
