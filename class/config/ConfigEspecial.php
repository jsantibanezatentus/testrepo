<?

class ConfigEspecial extends Objetivo {

	var $__objetivos;
	var $__horarios;
	var $__types;
	var $__segmentos;
	var $__eventos;
	var $__ponderacion;
	var $___objetivos;
	var $___horario_ponderacion;
	
//	var $period;
	var $period_start;
	var $period_end;
	var $form_method;
	var $form_template;
	var $display_description;
	var $objetivos_selector;
	var $objetivos_resource;
	var $horarios_selector;
	var $horarios_resource;
	var $report_list;     // "true"|"false"
	var $date_selection;  // "no"|"interval"|"day"
	var $tiene_obj;//true | false
	var $multi_obj;//true | false
	var $contraloria;//true | false
	var $pdf_especial;//true | false
	var $img_especial;//true | false
	var $horario_preferido;
//	var $cache;

	// Nuevo campo para definir el intervalo inferior a la fecha actual para mostrar como rango en el calendario.
	var $intervalo_resta_fecha_actual;

	function ConfigEspecial($objetivo_id) {
		global $usr;

		$this->objetivo_id = $objetivo_id;
		$this->__Objetivo();

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($this->__xml_config);
                $xpath = new DOMXpath($dom);
               
/*		foreach ($xpath->query("/atentus/cache/param") as $param) {
			$arr_param = array();
			foreach ($param->attributes as $attr) {
				$arr_param[$attr->nodeName] = $attr->nodeValue;
			}
			foreach ($xpath->query("param", $param) as $subparam) {
				$arr_subparam = array();
				foreach ($subparam->attributes as $attr) {
					$arr_subparam[$attr->nodeName] = $attr->nodeValue;
				}
				$arr_param["array"][] = $arr_subparam;
			}
			$this->cache[] = $arr_param;
		}*/
                foreach ($xpath->query("/atentus/config/audex") as $information) {
                    $this->titulo = $xpath->query("titulo", $information)->item(0)->nodeValue;
                    $this->information = $xpath->query("informacion", $information)->item(0)->nodeValue;
                }
                
                foreach ($xpath->query("/atentus/config/atdex") as $information) {
                	$this->titulo = $xpath->query("titulo", $information)->item(0)->nodeValue;
                	$this->information = $xpath->query("informacion", $information)->item(0)->nodeValue;
                }
                
		foreach ($xpath->query("/atentus/config/especial") as $config) {
			$this->position = $config->getAttribute('position');
			foreach ($xpath->query("setup", $config) as $tag_setup) {
				$this->tipo_reporte = $tag_setup->getAttribute('tipo_reporte');
//				$this->period = $tag_setup->getAttribute('period');
				$this->objetivo_id_especial = $tag_setup->getAttribute('especial_objetivo_id');
				$this->tag = $tag_setup->getAttribute('tag');
				$this->token = $tag_setup->getAttribute('token');
				$this->disponibilidad_real = $tag_setup->getAttribute('disponibilidad_real');
				$this->period_start = $tag_setup->getAttribute('period_start');
				$this->period_end = $tag_setup->getAttribute('period_end');
				$this->form_method = $tag_setup->getAttribute('form_method');
				$this->display_description = $tag_setup->getAttribute('display_description');
				$this->intervalo_resta_fecha_actual = $tag_setup->getAttribute('relative_interval_from_now');
				$this->tiene_obj = ($tag_setup->getAttribute('tiene_obj')=="true"?1:0);
				$this->multi_obj = ($tag_setup->getAttribute('multi_obj')=="true"?1:0);
				$this->contraloria = ($tag_setup->getAttribute('contraloria')=="true"?1:0);
				$this->pdf_especial = ($tag_setup->getAttribute('pdf_especial')=="true"?1:0);
				$this->img_especial = ($tag_setup->getAttribute('img_especial')=="true"?1:0);
				$this->comparativo = ($tag_setup->getAttribute('comparativo')=="true"?1:0);
				$this->horario_preferido = $tag_setup->getAttribute('horario_preferido');
				$this->color_critico = $tag_setup->getAttribute('color_critico');
				$this->color_moderado = $tag_setup->getAttribute('color_moderado');
				$this->img_base64 = $tag_setup->getAttribute('img_base64');
				$this->mostrar_grafico = $tag_setup->getAttribute('mostrar_grafico');
				$this->header_atentus = $tag_setup->getAttribute('header_atentus');
				$this->mostrar_contacto = $tag_setup->getAttribute('mostrar_contacto');
				$this->item_bloque = $tag_setup->getAttribute('item_bloque');
				
				if ($tag_setup->getAttribute('form_template') != null or $tag_setup->getAttribute('form_template') != "") {
					$this->form_template = $tag_setup->getAttribute('form_template');
				}
				else {
					$this->form_template = "formulario_default.tpl";
				}

				// Permite seleccionar fecha?
				// Por defecto si, con intervalos.
				if ($tag_setup->getAttribute('date_selection') != null or $tag_setup->getAttribute('date_selection') != "") {
					$this->date_selection = $tag_setup->getAttribute('date_selection');
				}
				else {
					$this->date_selection = "interval";
				}

				// Muestra la lista de informes disponibles?
				// Por defecto no.
				if ($tag_setup->getAttribute('report_list') != null or $tag_setup->getAttribute('report_list') != "") {
					$this->report_list = $tag_setup->getAttribute('report_list');
				}
				else {
					$this->report_list = "false";
				}
			}

			$this->__types = array();
			foreach ($xpath->query("types/type", $config) as $tag_types) {
				$type = new TypeEspecial($tag_types->getAttribute('name'), $tag_types->getAttribute('content-type'));

				$type->informe_id = $tag_types->getAttribute('informe_id');
				$type->order = $tag_types->getAttribute('order');
				$type->class = $tag_types->getAttribute('class');
				$type->method = $tag_types->getAttribute('method');
				$type->file_name = $tag_types->getAttribute('file-name');
				$this->__types[$tag_types->getAttribute('order')] = $type;
			}
			
			$this->__contenedor = array();
			foreach ($xpath->query("contenedor", $config) as $tag_contenedor) {
				$this->__conjuntos = array();
			    foreach ($xpath->query("conjuntos", $tag_contenedor) as $tag_conjuntos) {
			        $this->__ponderacion = array();
			        $this->__conjunto = array();
			        foreach ($xpath->query("conjunto", $tag_conjuntos) as $tag_conjunto) {
			            foreach ($xpath->query("ponderaciones/ponderacion", $tag_conjunto) as $tag_ponderacion) {
			                $ponderacion = new stdClass();
			                $ponderacion->valor_ponderacion = $tag_ponderacion->getAttribute('valor');
			                $ponderacion->hora_inicio = $tag_ponderacion->getAttribute('hora_inicio');
			                $ponderacion->hora_termino = $tag_ponderacion->getAttribute('hora_termino');
			                $this->__ponderacion[$tag_ponderacion->getAttribute('hora_inicio')]=$ponderacion;
			            }
			            $this->___objetivos_conjunto= array();
			            foreach ($xpath->query("categoria", $tag_conjunto) as $tag_categoria) {
			                $objetivos = new stdClass();
			                $pasos_categoria = $xpath->query("categoria", $tag_categoria);
			                $this->___pasos = array();
			                foreach ($pasos_categoria as  $paso) {
			                    $pasos = new stdClass();
			                    $pasos->paso_orden = $paso->getAttribute('paso_orden');
			                    $pasos->flujo = $paso->getAttribute('flujo');
			                    $pasos->pasos = $paso->getAttribute('pasos');
			                    $this->___pasos[$paso->getAttribute('paso_orden').'|'.$paso->getAttribute('screenshot_paso')]=$pasos;
			                }
			                $this->___objetivos_conjunto[$tag_categoria->getAttribute('objetivo_id').'|'.$tag_categoria->getAttribute('muestra_nombre')] = $this->___pasos;
			            }
			            $this->__conjunto[$tag_conjunto->getAttribute('nombre')]=$tag_conjunto->getAttribute('nombre');
			            $this->__conjunto[$tag_conjunto->getAttribute('nombre')]=$this->___objetivos_conjunto;
			        }
			        $this->__conjuntos[$tag_conjuntos->getAttribute('nombre')] = $this->__conjunto;
			    }
			}

			$this->__ponderacion = array();
			$this->__grupos = array();
			foreach ($xpath->query("grupos/grupo", $config) as $tag_grupo) {
				foreach ($xpath->query("ponderaciones/ponderacion", $tag_grupo) as $tag_ponderacion) {
					$ponderacion = new stdClass();
					$ponderacion->valor_ponderacion = $tag_ponderacion->getAttribute('valor');
					$ponderacion->hora_inicio = $tag_ponderacion->getAttribute('hora_inicio');
					$ponderacion->hora_termino = $tag_ponderacion->getAttribute('hora_termino');
					$this->__ponderacion[$tag_ponderacion->getAttribute('hora_inicio')]=$ponderacion;
				}
				$this->___objetivos = array();
				foreach ($xpath->query("relacion", $tag_grupo) as $tag_relacion) {
					$objetivos = new stdClass();
					$objetivos->objetivo_id = $tag_relacion->getAttribute('objetivo_id');
					$objetivos->nombre_empresa = $tag_relacion->getAttribute('nombre_empresa');
					$paso= $xpath->query("relacion", $tag_relacion)->item(0);
					$objetivos->paso_orden = $paso->getAttribute('paso_orden');
					$this->___objetivos[$tag_relacion->getAttribute('objetivo_id')]=$objetivos;
				}
				$this->__grupos[$tag_grupo->getAttribute('nombre')]['nombre']=$tag_grupo->getAttribute('nombre');
				$this->__grupos[$tag_grupo->getAttribute('nombre')]['objetivos']=$this->___objetivos;
			}

			$this->__segmentos=array();
			foreach ($xpath->query("grupos/grupo", $config) as $tag_segmentos) {
				$segmento = new stdClass();
				$segmento->segmento_id = $tag_segmentos->getAttribute('id_segmento');
				$segmento->nombre = $tag_segmentos->getAttribute('nombre');
				$this->__segmentos[$tag_segmentos->getAttribute('id_segmento')]=$segmento;
			}
			
			$this->__eventos=array();
			foreach ($xpath->query("eventos/evento", $config) as $tag_eventos) {
				$evento = new stdClass();
				$evento->evento_id = $tag_eventos->getAttribute('evento_id');
				$evento->nombre = $tag_eventos->getAttribute('nombre');
				$this->__eventos[$tag_eventos->getAttribute('evento_id')]=$evento;
			}
			
			$this->__objetivos = array();
            foreach ($xpath->query("objetivos", $config) as $tag_objetivos) {
				$this->objetivos_selector = ($tag_objetivos->getAttribute('selector')=="true")?true:false;
				$this->objetivos_resource = $tag_objetivos->getAttribute('resource');
				if ($this->objetivos_resource != "user" and $this->objetivos_resource != "client") {
					foreach ($xpath->query("objetivo", $tag_objetivos) as $tag_objetivo) {
						$objetivo = new Objetivo($tag_objetivo->getAttribute('objetivo_id'));
						$objetivo->__Objetivo();
						$objetivo->letra = $tag_objetivo->getAttribute('letra');
						$objetivo->paso = $tag_objetivo->getAttribute('paso');
						$objetivo->sla_p = $tag_objetivo->getAttribute('sla_performance');
						$objetivo->sla_e = $tag_objetivo->getAttribute('sla_error_performance');
						$objetivo->max = $tag_objetivo->getAttribute('max');
						$objetivo->global = $tag_objetivo->getAttribute('global');
						$objetivo->parcial = $tag_objetivo->getAttribute('parcial');
						$objetivo->nodos = $tag_objetivo->getAttribute('nodos');
						$objetivo->alias = $tag_objetivo->getAttribute('alias');
						foreach ($xpath->query("paso", $tag_objetivo) as $tag_paso) {
							$paso = new Paso();
							$paso->paso_id = $tag_paso->getAttribute('paso_orden');
							$paso->paso_alias = $tag_paso->getAttribute('alias');
							$paso->descripcion = $tag_paso->getAttribute('descripcion');
							$objetivo->__pasos[$paso->paso_id] = $paso;
						}
						
						$tag_meta = $xpath->query("grupos/grupo/objetivos/objetivo[@objetivo_id=".$tag_objetivo->getAttribute('objetivo_id')."]", $config)->item(0);
						if($tag_meta!=null){
						    $meta = new stdClass();
							$meta->indicador_uptime = $tag_meta->getAttribute('indicador_uptime');
							$meta->indicador_dparcial = $tag_meta->getAttribute('indicador_dparcial');
							$objetivo->__metas[$tag_objetivo->getAttribute('objetivo_id')]=$meta;
						}
						$this->__objetivos[$objetivo->objetivo_id] = $objetivo;
					}
				}
				
			}

			$this->__horarios = array();
			foreach ($xpath->query("horarios_habiles", $config) as $tag_horarios) {
				$this->horarios_selector = ($tag_horarios->getAttribute('selector')=="true")?true:false;
				$this->horarios_resource = $tag_horarios->getAttribute('resource');

				if ($this->horarios_resource != "user" and $this->horarios_resource != "client") {
					foreach ($xpath->query("horario_habil", $tag_horarios) as $tag_horario) {
						$horario = $usr->getHorario($tag_horario->getAttribute('horario_id'), REP_HORARIO_HABIL);
						$this->__horarios[$horario->horario_id] = $horario;
					}
				}
			}
		}
		$this->__reporte = array();
		foreach ($xpath->query("/atentus/config/especial/reporte") as $key => $reporte) {
			$titulo = new stdClass();
			$titulo->titulo = $reporte->getAttribute("titulo");
			$this->__reporte['Titulo'] = $titulo;
			foreach ( $xpath->query("presentacion/descripcion", $reporte) as $presentacion) {
				//var_dump($presentacion->getAttribute("orden"));
				$presentaciones = new stdClass();
				$presentaciones->orden_presentacion = $presentacion->getAttribute("orden");
				$presentaciones->texto=$presentacion->getAttribute("texto");
				//var_dump($presentaciones);
				$this->__reporte['Presentacion'][$presentaciones->orden_presentacion] = $presentaciones;
			}
			foreach ( $xpath->query("consolidado/descripcion", $reporte) as $consolidado) {
				$consolidaciones = new stdClass();
				$consolidaciones->orden = $consolidado->getAttribute("orden");
				$consolidaciones->texto = $consolidado->getAttribute("texto");
				$this->__reporte['Consolidado'][$consolidaciones->orden] = $consolidaciones->texto;
			}
			foreach ( $xpath->query("disponibilidad/descripcion", $reporte) as $disponibilidad) {
				$disponibilidades = new stdClass();
				$disponibilidades->orden = $disponibilidad->getAttribute("orden");
				$disponibilidades->texto = $disponibilidad->getAttribute("texto");
				$disponibilidades->nombre = $disponibilidad->getAttribute("name");
				$this->__reporte['Disponibilidad'][$disponibilidades->orden][$disponibilidades->nombre] = $disponibilidades->texto;
			}
			
			foreach ( $xpath->query("footer", $reporte) as $footer) {
				$this->__reporte['Footer'] = $footer->getAttribute("texto");	
			}
		}
	}

	function generarFormulario() {
		if (!isset($this->form_method) or $this->form_method == null) {
			return $this->getDefaultForm();
		}
		elseif (method_exists($this, $this->form_method)) {
			$form_method = $this->form_method;
			return $this->$form_method();
		}
	}
	
	function getPonderaciones() {
		
			return $this->__ponderacion;
	}
	
	function  getSegmentosForm(){
		global $usr;
		
		$T =& new Template_PHPLIB(REP_PATH_ESPECIALTEMPLATES);
		$T->setFile('tpl_especial', 'formulario_segmentos.tpl');
		$T->setBlock('tpl_especial', 'LISTA_SEGMENTOS_TD', 'lista_segmentos_td');
		$T->setBlock('tpl_especial', 'LISTA_SEGMENTOS_TR', 'lista_segmentos_tr');
		$T->setBlock('tpl_especial', 'BLOQUE_SEGMENTOS', 'bloque_segmentos');
		$T->setBlock('tpl_especial', 'LISTA_HORARIOS_TD', 'lista_horarios_td');
		$T->setBlock('tpl_especial', 'LISTA_HORARIOS_TR', 'lista_horarios_tr');
		$T->setBlock('tpl_especial', 'BLOQUE_HORARIOS', 'bloque_horarios');
		$T->setBlock('tpl_especial', 'LISTA_TIPOS', 'lista_tipos');
		$T->setBlock('tpl_especial', 'BLOQUE_TIPOS', 'bloque_tipos');
		$T->setBlock('tpl_especial', 'BLOQUE_TIPO_DEFAULT', 'bloque_tipo_default');
		$T->setBlock('tpl_especial', 'BLOQUE_INFORMES_DISPONIBLES', 'bloque_informes_disponibles');
		
		$reporte_period_start = null;

		if($this->period_start && $this->intervalo_resta_fecha_actual && self::_validaFecha($this->period_start) && self::_validaIntervalo($this->intervalo_resta_fecha_actual)) {
			$fecha_menos_intervalo = date( 'Y-m-d', strtotime('-'.str_replace('-', '', $this->intervalo_resta_fecha_actual)));
			$reporte_period_start = strtotime($this->period_start) >= strtotime($fecha_menos_intervalo) ? $this->period_start : $fecha_menos_intervalo;
		} elseif($this->period_start && self::_validaFecha($this->period_start)) {
			$reporte_period_start = $this->period_start;
		} elseif($this->intervalo_resta_fecha_actual && self::_validaIntervalo($this->intervalo_resta_fecha_actual)) {
			$reporte_period_start = date( 'Y-m-d', strtotime('-'.str_replace('-', '', $this->intervalo_resta_fecha_actual) ) );
		}

		$T->setVar('__reporte_titulo', $this->nombre);
		$T->setVar('__reporte_period', $this->period);
		$T->setVar('__reporte_period_start', $reporte_period_start);
		$T->setVar('__fecha_inicio', date("Y-m-d"));
		$T->setVar('__fecha_termino', date("Y-m-d"));
		
		/* LISTA DE SEGMENTOS */
		$T->setVar('lista_segmentos_td', '');
			$indice = 1;
			$segmentos = $this->__segmentos;

			$T->setVar('__segmento_default', current($segmentos)->segmento_id);

			foreach ($segmentos as $segmento) {
				$T->setVar('__segmento_id', $segmento->segmento_id);
				$T->setVar('__segmento_nombre', $segmento->nombre);
				$T->parse('lista_segmentos_td', 'LISTA_SEGMENTOS_TD', true);
				if (($indice % 2) == 0 or $indice == count($segmentos)) {
					$T->parse('lista_segmentos_tr', 'LISTA_SEGMENTOS_TR', true);
					$T->setVar('lista_segmentos_td', '');
				}
				$indice++;
			}
			$T->parse('bloque_segmentos', 'BLOQUE_SEGMENTOS', true);
		
		
		/* LISTA DE HORARIOS */
		$T->setVar('lista_horarios_td', '');
		if ($this->horarios_selector == true) {
			$indice = 1;
			$horarios = $this->getHorarios();
		
			$T->setVar('__horario_id_default', current($horarios)->horario_id);
			foreach ($horarios as $horario) {
				$T->setVar('__horario_id', $horario->horario_id);
				$T->setVar('__horario_nombre', $horario->nombre);
				$T->parse('lista_horarios_td', 'LISTA_HORARIOS_TD', true);
				if (($indice % 2) == 0 or $indice == count($horarios)) {
					$T->parse('lista_horarios_tr', 'LISTA_HORARIOS_TR', true);
					$T->setVar('lista_horarios_td', '');
				}
				$indice++;
			}
			$T->parse('bloque_horarios', 'BLOQUE_HORARIOS', true);
		}
		
		/* LISTA DE TIPOS */
		$T->setVar('lista_tipos', '');
		if (count($this->__types) > 1) {
			$T->setVar('__tipo_content_default', current($this->__types)->order);
			foreach ($this->__types as $type) {
				$T->setVar('__tipo_nombre', $type->nombre);
				$T->setVar('__tipo_orden', $type->order);
				$T->setVar('__tipo_content', $type->content);
				$T->parse('lista_tipos', 'LISTA_TIPOS', true);
			}
			$T->parse('bloque_tipos', 'BLOQUE_TIPOS', true);
		}
		elseif (count($this->__types) == 1) {
			$T->setVar('__tipo_orden', current($this->__types)->order);
			$T->setVar('__tipo_content', current($this->__types)->content);
			$T->parse('bloque_tipo_default', 'BLOQUE_TIPO_DEFAULT', false);
		}
		if($this->report_list === "true") {
			$T->parse('bloque_informes_disponibles', 'BLOQUE_INFORMES_DISPONIBLES', true);
		}
		
		switch($this->date_selection) {
			case "no":
				$T->setVar('__calendario_permite_seleccionar', 'false');
				$T->setVar('__calendario_selecciona_intervalo', 'false');
				break;
			case "day":
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'false');
				break;
			case "interval":
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'true');
				break;
			default:
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'true');
		}
		
		return $T->parse('out', 'tpl_especial');
	}

		public function getPersonalForm(){

		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$T =& new Template_PHPLIB(REP_PATH_ESPECIALTEMPLATES);
		$T->setFile('tpl_especial', 'formulario_especial_personal.tpl');
		$T->setBlock('tpl_especial', 'LISTA_SUBOBJETIVOS_TD', 'lista_subobjetivos_td');
		$T->setBlock('tpl_especial', 'LISTA_SUBOBJETIVOS_TR', 'lista_subobjetivos_tr');
		$T->setBlock('tpl_especial', 'BLOQUE_SUBOBJETIVOS', 'bloque_subobjetivos');
		$T->setBlock('tpl_especial', 'LISTA_HORARIOS_TD', 'lista_horarios_td');
		$T->setBlock('tpl_especial', 'LISTA_HORARIOS_TR', 'lista_horarios_tr');
		$T->setBlock('tpl_especial', 'BLOQUE_HORARIOS', 'bloque_horarios');
		$T->setBlock('tpl_especial', 'LISTA_TIPOS', 'lista_tipos');
		$T->setBlock('tpl_especial', 'BLOQUE_TIPOS', 'bloque_tipos');
		$T->setBlock('tpl_especial', 'BLOQUE_TIPO_DEFAULT', 'bloque_tipo_default');
		$T->setBlock('tpl_especial', 'BLOQUE_INFORMES_DISPONIBLES', 'bloque_informes_disponibles');

		if($this->period_start && $this->intervalo_resta_fecha_actual && self::_validaFecha($this->period_start) && self::_validaIntervalo($this->intervalo_resta_fecha_actual)) {
			$fecha_menos_intervalo = date( 'Y-m-d', strtotime('-'.str_replace('-', '', $this->intervalo_resta_fecha_actual)));
			$reporte_period_start = strtotime($this->period_start) >= strtotime($fecha_menos_intervalo) ? $this->period_start : $fecha_menos_intervalo;
		} elseif($this->period_start && self::_validaFecha($this->period_start)) {
			$reporte_period_start = $this->period_start;
		} elseif($this->intervalo_resta_fecha_actual && self::_validaIntervalo($this->intervalo_resta_fecha_actual)) {
			$reporte_period_start = date( 'Y-m-d', strtotime('-'.str_replace('-', '', $this->intervalo_resta_fecha_actual) ) );
		}

		$T->setVar('__reporte_titulo', $this->nombre);
		$T->setVar('__reporte_period', $this->period);
		$T->setVar('__reporte_period_start', $reporte_period_start);
		$T->setVar('__fecha_inicio', date("Y-m-d"));
		$T->setVar('__fecha_termino', date("Y-m-d"));

		/* LISTA DE HORARIOS */
		$T->setVar('lista_horarios_td', '');
		if ($this->horarios_selector == true) {
			$indice = 1;
			$horarios = $this->getHorarios();

			$T->setVar('__horario_id_default', current($horarios)->horario_id);

			foreach ($horarios as $horario) {
				$T->setVar('__horario_id', $horario->horario_id);
				$T->setVar('__horario_nombre', $horario->nombre);
				$T->parse('lista_horarios_td', 'LISTA_HORARIOS_TD', true);
				if (($indice % 2) == 0 or $indice == count($horarios)) {
					$T->parse('lista_horarios_tr', 'LISTA_HORARIOS_TR', true);
					$T->setVar('lista_horarios_td', '');
				}
				$indice++;
			}
			$T->parse('bloque_horarios', 'BLOQUE_HORARIOS', true);
		}

		/* LISTA DE TIPOS */
		$T->setVar('lista_tipos', '');
		if (count($this->__types) > 0) {
			$T->setVar('__tipo_content_default', current($this->__types)->order);
			foreach ($this->__types as $type) {

				$T->setVar('__tipo_nombre', $type->nombre);
				$T->setVar('__tipo_orden', $type->order);
				$T->setVar('__tipo_content', $type->content);
				$T->parse('lista_tipos', 'LISTA_TIPOS', true);
			}
			$T->parse('bloque_tipos', 'BLOQUE_TIPOS', true);
		}


		if($this->report_list === "true") {
			$T->parse('bloque_informes_disponibles', 'BLOQUE_INFORMES_DISPONIBLES', true);
		}

		switch($this->date_selection) {
			case "no":
				$T->setVar('__calendario_permite_seleccionar', 'false');
				$T->setVar('__calendario_selecciona_intervalo', 'false');
				break;
			case "day":
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'false');
				break;
			case "interval":
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'true');
				break;
			default:
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'true');
		}

		return $T->parse('out', 'tpl_especial');
	}
	
	public function getApiRespuesta(){
		$T =& new Template_PHPLIB(REP_PATH_ESPECIALTEMPLATES);
		$T->setFile('tpl_especial', 'formulario_api_respuesta.tpl');
		$T->setBlock('tpl_especial', 'LISTA_SUBOBJETIVOS_TD', 'lista_subobjetivos_td');
		$T->setBlock('tpl_especial', 'LISTA_SUBOBJETIVOS_TR', 'lista_subobjetivos_tr');
		$T->setBlock('tpl_especial', 'BLOQUE_SUBOBJETIVOS', 'bloque_subobjetivos');
		$T->setBlock('tpl_especial', 'LISTA_HORARIOS_TD', 'lista_horarios_td');
		$T->setBlock('tpl_especial', 'LISTA_HORARIOS_TR', 'lista_horarios_tr');
		$T->setBlock('tpl_especial', 'BLOQUE_HORARIOS', 'bloque_horarios');
		$T->setBlock('tpl_especial', 'LISTA_TIPOS', 'lista_tipos');
		$T->setBlock('tpl_especial', 'BLOQUE_TIPOS', 'bloque_tipos');
		$T->setBlock('tpl_especial', 'BLOQUE_TIPO_DEFAULT', 'bloque_tipo_default');
		$T->setBlock('tpl_especial', 'BLOQUE_INFORMES_DISPONIBLES', 'bloque_informes_disponibles');

		$reporte_period_start = null;

		if($this->period_start && $this->intervalo_resta_fecha_actual && self::_validaFecha($this->period_start) && self::_validaIntervalo($this->intervalo_resta_fecha_actual)) {
			$fecha_menos_intervalo = date( 'Y-m-d', strtotime('-'.str_replace('-', '', $this->intervalo_resta_fecha_actual)));
			$reporte_period_start = strtotime($this->period_start) >= strtotime($fecha_menos_intervalo) ? $this->period_start : $fecha_menos_intervalo;
		} elseif($this->period_start && self::_validaFecha($this->period_start)) {
			$reporte_period_start = $this->period_start;
		} elseif($this->intervalo_resta_fecha_actual && self::_validaIntervalo($this->intervalo_resta_fecha_actual)) {
			$reporte_period_start = date( 'Y-m-d', strtotime('-'.str_replace('-', '', $this->intervalo_resta_fecha_actual) ) );
		}

		$T->setVar('__reporte_titulo', $this->nombre);
		$T->setVar('__reporte_period', $this->period);
		$T->setVar('__reporte_period_start', $reporte_period_start);
		$T->setVar('__fecha_inicio', date("Y-m-d"));
		$T->setVar('__fecha_termino', date("Y-m-d"));

		/* LISTA DE SUBOBJETIVOS */
		$T->setVar('lista_objetivos_td', '');
		if ($this->objetivos_selector == true) {
			$indice = 1;
			$subobjetivos = $this->getSubobjetivos();

			$T->setVar('__subobjetivo_default', current($subobjetivos)->objetivo_id);

			foreach ($subobjetivos as $subobjetivo) {
				$T->setVar('__subobjetivo_id', $subobjetivo->objetivo_id);
				$T->setVar('__subobjetivo_nombre', $subobjetivo->nombre);
				$T->parse('lista_subobjetivos_td', 'LISTA_SUBOBJETIVOS_TD', true);
				if (($indice % 2) == 0 or $indice == count($subobjetivos)) {
					$T->parse('lista_subobjetivos_tr', 'LISTA_SUBOBJETIVOS_TR', true);
					$T->setVar('lista_subobjetivos_td', '');
				}
				$indice++;
			}
			$T->parse('bloque_subobjetivos', 'BLOQUE_SUBOBJETIVOS', true);
		}

		/* LISTA DE HORARIOS */
		$T->setVar('lista_horarios_td', '');
		if ($this->horarios_selector == true) {
			$indice = 1;
			$horarios = $this->getHorarios();

			$T->setVar('__horario_id_default', current($horarios)->horario_id);

			foreach ($horarios as $horario) {
				$T->setVar('__horario_id', $horario->horario_id);
				$T->setVar('__horario_nombre', $horario->nombre);
				$T->parse('lista_horarios_td', 'LISTA_HORARIOS_TD', true);
				if (($indice % 2) == 0 or $indice == count($horarios)) {
					$T->parse('lista_horarios_tr', 'LISTA_HORARIOS_TR', true);
					$T->setVar('lista_horarios_td', '');
				}
				$indice++;
			}
			$T->parse('bloque_horarios', 'BLOQUE_HORARIOS', true);
		}

		/* LISTA DE TIPOS */
		$T->setVar('lista_tipos', '');
		if (count($this->__types) > 1) {
			$T->setVar('__tipo_content_default', current($this->__types)->order);
			foreach ($this->__types as $type) {
				$T->setVar('__tipo_nombre', $type->nombre);
				$T->setVar('__tipo_orden', $type->order);
				$T->setVar('__tipo_content', $type->content);
				$T->parse('lista_tipos', 'LISTA_TIPOS', true);
			}
			$T->parse('bloque_tipos', 'BLOQUE_TIPOS', true);
		}
		elseif (count($this->__types) == 1) {
			$T->setVar('__tipo_orden', current($this->__types)->order);
			$T->setVar('__tipo_content', current($this->__types)->content);
			$T->parse('bloque_tipo_default', 'BLOQUE_TIPO_DEFAULT', false);
		}

		if($this->report_list === "true") {
			$T->parse('bloque_informes_disponibles', 'BLOQUE_INFORMES_DISPONIBLES', true);
		}

		switch($this->date_selection) {
			case "no":
				$T->setVar('__calendario_permite_seleccionar', 'false');
				$T->setVar('__calendario_selecciona_intervalo', 'false');
				break;
			case "day":
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'false');
				break;
			case "interval":
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'true');
				break;
			default:
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'true');
		}

		return $T->parse('out', 'tpl_especial');
	}

	function getNodosForm(){
		global $usr;

		$T =& new Template_PHPLIB(REP_PATH_ESPECIALTEMPLATES);
		$T->setFile('tpl_especial', 'formulario_nodos.tpl');
		$T->setBlock('tpl_especial', 'LISTA_NODOS_TD', 'lista_nodos_td');
		$T->setBlock('tpl_especial', 'LISTA_NODOS_TR', 'lista_nodos_tr');
		$T->setBlock('tpl_especial', 'LISTA_NODOS', 'lista_nodos');
		$T->setBlock('tpl_especial', 'LISTA_TIPOS', 'lista_tipos');
		$T->setBlock('tpl_especial', 'BLOQUE_TIPOS', 'bloque_tipos');
		$T->setBlock('tpl_especial', 'BLOQUE_TIPO_DEFAULT', 'bloque_tipo_default');

		$T->setVar('__reporte_titulo', $this->nombre);

		$indice = 1;
		foreach ($this->getSubobjetivos() as $subobjetivo) {
			foreach ($subobjetivo->getNodos() as $nodo) {
				$nodos[$nodo->nodo_id] = $nodo;
			}
		}
		$T->setVar('__monitor_default', current($nodos)->nodo_id);

		$T->setVar('lista_nodos_td', '');
		foreach ($nodos as $nodo) {
			$T->setVar('__nodo_id', $nodo->nodo_id);
			$T->setVar('__nodo_nombre', $nodo->nombre);
			$T->parse('lista_nodos_td', 'LISTA_NODOS_TD', true);
			if (($indice % 2) == 0 or $indice == count($nodos)) {
				$T->parse('lista_nodos_tr', 'LISTA_NODOS_TR', true);
				$T->setVar('lista_nodos_td', '');
			}
			$indice++;
		}
		$T->parse('lista_nodos', 'LISTA_NODOS', true);

		/* LISTA DE TIPOS */
		$T->setVar('lista_tipos', '');
		$T->setVar('__tipo_orden_default', current($this->__types)->order);
		foreach ($this->__types as $type) {
			$T->setVar('__tipo_nombre', $type->nombre);
			$T->setVar('__tipo_orden', $type->order);
			$T->parse('lista_tipos', 'LISTA_TIPOS', true);
		}
		$T->parse('bloque_tipos', 'BLOQUE_TIPOS', true);
		return $T->parse('out', 'tpl_especial');
	}

	function getDefaultForm() {
		$T =& new Template_PHPLIB(REP_PATH_ESPECIALTEMPLATES);
		$T->setFile('tpl_especial', $this->form_template);
		$T->setBlock('tpl_especial', 'LISTA_SUBOBJETIVOS_TD', 'lista_subobjetivos_td');
		$T->setBlock('tpl_especial', 'LISTA_SUBOBJETIVOS_TR', 'lista_subobjetivos_tr');
		$T->setBlock('tpl_especial', 'BLOQUE_SUBOBJETIVOS', 'bloque_subobjetivos');
		$T->setBlock('tpl_especial', 'LISTA_HORARIOS_TD', 'lista_horarios_td');
		$T->setBlock('tpl_especial', 'LISTA_HORARIOS_TR', 'lista_horarios_tr');
		$T->setBlock('tpl_especial', 'BLOQUE_HORARIOS', 'bloque_horarios');
		$T->setBlock('tpl_especial', 'LISTA_TIPOS', 'lista_tipos');
		$T->setBlock('tpl_especial', 'BLOQUE_TIPOS', 'bloque_tipos');
		$T->setBlock('tpl_especial', 'BLOQUE_TIPO_DEFAULT', 'bloque_tipo_default');
		$T->setBlock('tpl_especial', 'BLOQUE_INFORMES_DISPONIBLES', 'bloque_informes_disponibles');

		$reporte_period_start = null;

		if($this->period_start && $this->intervalo_resta_fecha_actual && self::_validaFecha($this->period_start) && self::_validaIntervalo($this->intervalo_resta_fecha_actual)) {
			$fecha_menos_intervalo = date( 'Y-m-d', strtotime('-'.str_replace('-', '', $this->intervalo_resta_fecha_actual)));
			$reporte_period_start = strtotime($this->period_start) >= strtotime($fecha_menos_intervalo) ? $this->period_start : $fecha_menos_intervalo;
		} elseif($this->period_start && self::_validaFecha($this->period_start)) {
			$reporte_period_start = $this->period_start;
		} elseif($this->intervalo_resta_fecha_actual && self::_validaIntervalo($this->intervalo_resta_fecha_actual)) {
			$reporte_period_start = date( 'Y-m-d', strtotime('-'.str_replace('-', '', $this->intervalo_resta_fecha_actual) ) );
		}

		$T->setVar('__reporte_period', $this->period);
		$T->setVar('__reporte_period_start', $reporte_period_start);
		$T->setVar('__fecha_inicio', date("Y-m-d"));
		$T->setVar('__fecha_termino', date("Y-m-d"));
		$T->setVar('__tipo_obj', $this->tiene_obj);
		$T->setVar('__multi_obj', $this->multi_obj);
		$T->setVar('__contraloria', $this->contraloria);
		$T->setVar('__pdf_especial', $this->pdf_especial);
		$T->setVar('__img_especial', $this->img_especial);
		$T->setVar('__comparativo', $this->comparativo);
		$T->setVar('__mostrar_grafico', $this->mostrar_grafico);
		$T->setVar('__atentus_header', $this->atentus_header);
		$T->setVar('__mostrar_contacto', $this->mostrar_contacto);
		$T->setVar('__item_bloque', $this->item_bloque);
		$T->setVar('__objetivo_id_especial', $this->objetivo_id_especial);
        /* LISTA DE SUBOBJETIVOS */
		$T->setVar('lista_objetivos_td', '');
		if ($this->objetivos_selector == true) {
			$indice = 1;
			$subobjetivos = $this->getSubobjetivos();

			$T->setVar('__subobjetivo_default', current($subobjetivos)->objetivo_id);

			foreach ($subobjetivos as $subobjetivo) {

				$T->setVar('__reporte_titulo', $this->nombre);
				$T->setVar('__subobjetivo_id', $subobjetivo->objetivo_id);
				$T->setVar('__subobjetivo_nombre', $subobjetivo->nombre);
				$T->parse('lista_subobjetivos_td', 'LISTA_SUBOBJETIVOS_TD', true);
				if (($indice % 2) == 0 or $indice == count($subobjetivos)) {
					$T->parse('lista_subobjetivos_tr', 'LISTA_SUBOBJETIVOS_TR', true);
					$T->setVar('lista_subobjetivos_td', '');
				}
				$indice++;
			}
			$T->parse('bloque_subobjetivos', 'BLOQUE_SUBOBJETIVOS', true);
		}

		/* LISTA DE HORARIOS */
		$T->setVar('lista_horarios_td', '');
		if ($this->horarios_selector == true) {
			$indice = 1;
			$horarios = $this->getHorarios();

			$T->setVar('__horario_id_default', current($horarios)->horario_id);

			foreach ($horarios as $horario) {
				$T->setVar('__horario_id', $horario->horario_id);
				$T->setVar('__horario_nombre', $horario->nombre);
				$T->parse('lista_horarios_td', 'LISTA_HORARIOS_TD', true);
				if (($indice % 2) == 0 or $indice == count($horarios)) {
					$T->parse('lista_horarios_tr', 'LISTA_HORARIOS_TR', true);
					$T->setVar('lista_horarios_td', '');
				}
				$indice++;
			}
			$T->parse('bloque_horarios', 'BLOQUE_HORARIOS', true);
		}

		/* LISTA DE TIPOS */
		$T->setVar('lista_tipos', '');
		if (count($this->__types) > 1) {
			$T->setVar('__tipo_content_default', current($this->__types)->order);
			foreach ($this->__types as $type) {
				$T->setVar('__tipo_nombre', $type->nombre);
				$T->setVar('__tipo_orden', $type->order);
				$T->setVar('__tipo_content', $type->content);
				$T->parse('lista_tipos', 'LISTA_TIPOS', true);
			}
			$T->parse('bloque_tipos', 'BLOQUE_TIPOS', true);
		}
		elseif (count($this->__types) == 1) {
			$T->setVar('__tipo_orden', current($this->__types)->order);
			$T->setVar('__tipo_content', current($this->__types)->content);
			$T->parse('bloque_tipo_default', 'BLOQUE_TIPO_DEFAULT', false);
		}

		if($this->report_list === "true") {
			$T->parse('bloque_informes_disponibles', 'BLOQUE_INFORMES_DISPONIBLES', true);
		}

		switch($this->date_selection) {
			case "no":
				$T->setVar('__calendario_permite_seleccionar', 'false');
				$T->setVar('__calendario_selecciona_intervalo', 'false');
				break;
			case "day":
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'false');
				break;
			case "interval":
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'true');
				break;
			default:
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'true');
		}

		return $T->parse('out', 'tpl_especial');
	}

	function getVistaHorizontalForm(){
		$T =& new Template_PHPLIB(REP_PATH_ESPECIALTEMPLATES);
		$T->setFile('tpl_especial', 'form_vista_horizontal.tpl');
		$reporte_period_start = null;
		if($this->period_start && $this->intervalo_resta_fecha_actual && self::_validaFecha($this->period_start) && self::_validaIntervalo($this->intervalo_resta_fecha_actual)) {
			$fecha_menos_intervalo = date( 'Y-m-d', strtotime('-'.str_replace('-', '', $this->intervalo_resta_fecha_actual)));
			$reporte_period_start = strtotime($this->period_start) >= strtotime($fecha_menos_intervalo) ? $this->period_start : $fecha_menos_intervalo;
		} elseif($this->period_start && self::_validaFecha($this->period_start)) {
			$reporte_period_start = $this->period_start;
		} elseif($this->intervalo_resta_fecha_actual && self::_validaIntervalo($this->intervalo_resta_fecha_actual)) {
			$reporte_period_start = date( 'Y-m-d', strtotime('-'.str_replace('-', '', $this->intervalo_resta_fecha_actual) ) );
		}
		$T->setVar('__reporte_period', $this->period);
		$T->setVar('__reporte_period_start', $reporte_period_start);
		$T->setVar('__fecha_inicio', date("Y-m-d"));
		$T->setVar('__fecha_termino', date("Y-m-d"));
		$T->setVar('__reporte_titulo', $this->nombre);
		$T->setVar('__tipo_orden', current($this->__types)->order);
		$T->setVar('__tipo_content', current($this->__types)->content);
		return $T->parse('out', 'tpl_especial');
	}

	
	function getEventosForm(){
		
		$T =& new Template_PHPLIB(REP_PATH_ESPECIALTEMPLATES);
		$T->setFile('tpl_especial', 'formulario_eventos.tpl');
		$T->setBlock('tpl_especial', 'LISTA_SUBOBJETIVOS_TD', 'lista_subobjetivos_td');
		$T->setBlock('tpl_especial', 'LISTA_SUBOBJETIVOS_TR', 'lista_subobjetivos_tr');
		$T->setBlock('tpl_especial', 'BLOQUE_SUBOBJETIVOS', 'bloque_subobjetivos');
		$T->setBlock('tpl_especial', 'LISTA_EVENTOS_TD', 'lista_eventos_td');
		$T->setBlock('tpl_especial', 'LISTA_EVENTOS_TR', 'lista_eventos_tr');
		$T->setBlock('tpl_especial', 'BLOQUE_EVENTOS', 'bloque_eventos');
		$T->setBlock('tpl_especial', 'LISTA_HORARIOS_TD', 'lista_horarios_td');
		$T->setBlock('tpl_especial', 'LISTA_HORARIOS_TR', 'lista_horarios_tr');
		$T->setBlock('tpl_especial', 'BLOQUE_HORARIOS', 'bloque_horarios');
		$T->setBlock('tpl_especial', 'LISTA_TIPOS', 'lista_tipos');
		$T->setBlock('tpl_especial', 'BLOQUE_TIPOS', 'bloque_tipos');
		$T->setBlock('tpl_especial', 'BLOQUE_TIPO_DEFAULT', 'bloque_tipo_default');
		$T->setBlock('tpl_especial', 'BLOQUE_INFORMES_DISPONIBLES', 'bloque_informes_disponibles');
		
		$reporte_period_start = null;
		
		if($this->period_start && $this->intervalo_resta_fecha_actual && self::_validaFecha($this->period_start) && self::_validaIntervalo($this->intervalo_resta_fecha_actual)) {
			$fecha_menos_intervalo = date( 'Y-m-d', strtotime('-'.str_replace('-', '', $this->intervalo_resta_fecha_actual)));
			$reporte_period_start = strtotime($this->period_start) >= strtotime($fecha_menos_intervalo) ? $this->period_start : $fecha_menos_intervalo;
		} elseif($this->period_start && self::_validaFecha($this->period_start)) {
			$reporte_period_start = $this->period_start;
		} elseif($this->intervalo_resta_fecha_actual && self::_validaIntervalo($this->intervalo_resta_fecha_actual)) {
			$reporte_period_start = date( 'Y-m-d', strtotime('-'.str_replace('-', '', $this->intervalo_resta_fecha_actual) ) );
		}
		
		$T->setVar('__reporte_titulo', $this->nombre);
		$T->setVar('__reporte_period', $this->period);
		$T->setVar('__reporte_period_start', $reporte_period_start);
		$T->setVar('__fecha_inicio', date("Y-m-d"));
		$T->setVar('__fecha_termino', date("Y-m-d"));
		$T->setVar('__tipo_obj', $this->tiene_obj);
		
		/* LISTA DE SUBOBJETIVOS */
		$T->setVar('lista_objetivos_td', '');
		if ($this->objetivos_selector == true) {
			$indice = 1;
			$subobjetivos = $this->getSubobjetivos();
		
			$T->setVar('__subobjetivo_default', current($subobjetivos)->objetivo_id);
		
			foreach ($subobjetivos as $subobjetivo) {
				$T->setVar('__subobjetivo_id', $subobjetivo->objetivo_id);
				$T->setVar('__subobjetivo_nombre', $subobjetivo->nombre);
				$T->parse('lista_subobjetivos_td', 'LISTA_SUBOBJETIVOS_TD', true);
				if (($indice % 2) == 0 or $indice == count($subobjetivos)) {
					$T->parse('lista_subobjetivos_tr', 'LISTA_SUBOBJETIVOS_TR', true);
					$T->setVar('lista_subobjetivos_td', '');
				}
				$indice++;
			}
			$T->parse('bloque_subobjetivos', 'BLOQUE_SUBOBJETIVOS', true);
		}
		
		
		/* LISTA EVENTOS*/
		$indice = 1;
		$eventos = $this->__eventos;
		$T->setVar('__evento_id_default', current($eventos)->evento_id);
		//echo $eventos->lengt;
		$T->setVar('lista_evento_td', '');
		foreach ($eventos as $evento) {
			$T->setVar('__evento_id', $evento->evento_id);
			$T->setVar('__evento_nombre', $evento->nombre);
			$T->parse('lista_eventos_td', 'LISTA_EVENTOS_TD', true);
			
			if (($indice % 2) == 0 or $indice == count($eventos)) {
				$T->parse('lista_eventos_tr', 'LISTA_EVENTOS_TR', true);
				$T->setVar('lista_eventos_td', '');
			}
			$indice++;
			
		}
		$T->parse('bloque_eventos', 'BLOQUE_EVENTOS', true);
		
		/* LISTA DE HORARIOS */
		$T->setVar('lista_horarios_td', '');
		if ($this->horarios_selector == true) {
			$indice = 1;
			$horarios = $this->getHorarios();
		
			$T->setVar('__horario_id_default', current($horarios)->horario_id);
		
			foreach ($horarios as $horario) {
				$T->setVar('__horario_id', $horario->horario_id);
				$T->setVar('__horario_nombre', $horario->nombre);
				$T->parse('lista_horarios_td', 'LISTA_HORARIOS_TD', true);
				if (($indice % 2) == 0 or $indice == count($horarios)) {
					$T->parse('lista_horarios_tr', 'LISTA_HORARIOS_TR', true);
					$T->setVar('lista_horarios_td', '');
				}
				$indice++;
			}
			$T->parse('bloque_horarios', 'BLOQUE_HORARIOS', true);
		}
		
		/* LISTA DE TIPOS */
		$T->setVar('lista_tipos', '');
		if (count($this->__types) > 1) {
			$T->setVar('__tipo_content_default', current($this->__types)->order);
			foreach ($this->__types as $type) {
				$T->setVar('__tipo_nombre', $type->nombre);
				$T->setVar('__tipo_orden', $type->order);
				$T->setVar('__tipo_content', $type->content);
				$T->parse('lista_tipos', 'LISTA_TIPOS', true);
			}
			$T->parse('bloque_tipos', 'BLOQUE_TIPOS', true);
		}
		elseif (count($this->__types) == 1) {
			$T->setVar('__tipo_orden', current($this->__types)->order);
			$T->setVar('__tipo_content', current($this->__types)->content);
			$T->parse('bloque_tipo_default', 'BLOQUE_TIPO_DEFAULT', false);
		}
		
		if($this->report_list === "true") {
			$T->parse('bloque_informes_disponibles', 'BLOQUE_INFORMES_DISPONIBLES', true);
		}
		
		switch($this->date_selection) {
			case "no":
				$T->setVar('__calendario_permite_seleccionar', 'false');
				$T->setVar('__calendario_selecciona_intervalo', 'false');
				break;
			case "day":
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'false');
				break;
			case "interval":
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'true');
				break;
			default:
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'true');
		}
		
		return $T->parse('out', 'tpl_especial');
	}
	
	
	function getMantencionForm() {
		global $usr;

		$cache = $this->getCacheMantencion();

		$T =& new Template_PHPLIB(REP_PATH_ESPECIALTEMPLATES);
		$T->setFile('tpl_especial', 'formulario_mantencion.tpl');
		$T->setBlock('tpl_especial', 'LISTA_SUBOBJETIVOS_TD', 'lista_subobjetivos_td');
		$T->setBlock('tpl_especial', 'LISTA_SUBOBJETIVOS_TR', 'lista_subobjetivos_tr');
		$T->setBlock('tpl_especial', 'LISTA_PASOS', 'lista_pasos');
		$T->setBlock('tpl_especial', 'BLOQUE_PASOS', 'bloque_pasos');
		$T->setBlock('tpl_especial', 'LISTA_MANTENCIONES', 'lista_mantenciones');
		$T->setBlock('tpl_especial', 'BLOQUE_SUBOBJETIVOS', 'bloque_subobjetivos');
		$T->setBlock('tpl_especial', 'LISTA_HORARIOS_TD', 'lista_horarios_td');
		$T->setBlock('tpl_especial', 'LISTA_HORARIOS_TR', 'lista_horarios_tr');
		$T->setBlock('tpl_especial', 'BLOQUE_HORARIOS', 'bloque_horarios');
		$T->setBlock('tpl_especial', 'LISTA_TIPOS', 'lista_tipos');
		$T->setBlock('tpl_especial', 'BLOQUE_TIPOS', 'bloque_tipos');
		$T->setBlock('tpl_especial', 'BLOQUE_TIPO_DEFAULT', 'bloque_tipo_default');
		$T->setBlock('tpl_especial', 'BLOQUE_INFORMES_DISPONIBLES', 'bloque_informes_disponibles');


		$T->setVar('__reporte_titulo', $this->nombre);
		$T->setVar('__reporte_period', $this->period);
		$T->setVar('__reporte_period_start', $this->period_start);
		$T->setVar('__fecha_inicio', date("Y-m-d"));
		$T->setVar('__fecha_termino', date("Y-m-d"));

		/* LISTA DE SUBOBJETIVOS */
		$T->setVar('lista_objetivos_td', '');
		if ($this->objetivos_selector == true) {
			$indice = 1;
			$subobjetivos = $this->getSubobjetivos();

			$T->setVar('__subobjetivo_default', current($subobjetivos)->objetivo_id);

			$mantenciones = $usr->getHorarios(REP_HORARIO_MANTENCION);
			$T->setVar('lista_mantenciones', '');
			foreach ($mantenciones as $mantencion) {
				$T->setVar('__mantencion_id', $mantencion->horario_id);
				$T->setVar('__mantencion_nombre', $mantencion->nombre);
				$T->parse('lista_mantenciones', 'LISTA_MANTENCIONES', true);
			}

			foreach ($subobjetivos as $subobjetivo) {
				$subobjetivo = new ConfigObjetivo($subobjetivo->objetivo_id);

				if (count($subobjetivo->__pasos) > 0) {

				$T->setVar('__subobjetivo_id', $subobjetivo->objetivo_id);
				$T->setVar('__subobjetivo_nombre', $subobjetivo->nombre);
				$T->setVar('__subobjetivo_paso_default', current($subobjetivo->__pasos)->paso_id);
				$T->parse('lista_subobjetivos_td', 'LISTA_SUBOBJETIVOS_TD', true);
				if (($indice % 2) == 0 or $indice == count($subobjetivos)) {
					$T->parse('lista_subobjetivos_tr', 'LISTA_SUBOBJETIVOS_TR', true);
					$T->setVar('lista_subobjetivos_td', '');
				}

				$T->setVar('lista_pasos', '');
				foreach ($subobjetivo->__pasos as $paso) {

					$man_ids = array();
					$man_nombres = array();
					if (isset($cache[$subobjetivo->objetivo_id][$paso->paso_id])) {
						foreach ($cache[$subobjetivo->objetivo_id][$paso->paso_id] as $param) {
							$man_ids[] = $param;
							$man_nombres[] = $mantenciones[$param]->nombre;
						}
					}

					$T->setVar('__paso_id', $paso->paso_id);
					$T->setVar('__paso_nombre', $paso->nombre);
					$T->setVar('__paso_man_ids', implode("-", $man_ids));
					$T->setVar('__paso_man_nombres', ((count($man_nombres) == 0)?"Sin mantenciones":implode(" / ", $man_nombres)));
					$T->parse('lista_pasos', 'LISTA_PASOS', true);
				}
				$T->setVar('__subobjetivo_id', $subobjetivo->objetivo_id);
				$T->parse('bloque_pasos', 'BLOQUE_PASOS', true);
				$indice++;
				}
			}

			$T->parse('bloque_subobjetivos', 'BLOQUE_SUBOBJETIVOS', true);
		}

		/* LISTA DE HORARIOS */
		$T->setVar('lista_horarios_td', '');
		if ($this->horarios_selector == true) {
			$indice = 1;
			$horarios = $this->getHorarios();

			$T->setVar('__horario_id_default', current($horarios)->horario_id);

			foreach ($horarios as $horario) {
				$T->setVar('__horario_id', $horario->horario_id);
				$T->setVar('__horario_nombre', $horario->nombre);
				$T->parse('lista_horarios_td', 'LISTA_HORARIOS_TD', true);
				if (($indice % 2) == 0 or $indice == count($horarios)) {
					$T->parse('lista_horarios_tr', 'LISTA_HORARIOS_TR', true);
					$T->setVar('lista_horarios_td', '');
				}
				$indice++;
			}
			$T->parse('bloque_horarios', 'BLOQUE_HORARIOS', true);
		}

		/* LISTA DE TIPOS */
		$T->setVar('lista_tipos', '');
		if (count($this->__types) > 1) {

			$T->setVar('__tipo_content_default', current($this->__types)->content);

			foreach ($this->__types as $type) {
				$T->setVar('__tipo_nombre', $type->nombre);
				$T->setVar('__tipo_content', $type->content);
				$T->parse('lista_tipos', 'LISTA_TIPOS', true);
			}
			$T->parse('bloque_tipos', 'BLOQUE_TIPOS', true);
		}
		elseif (count($this->__types) == 1) {
			$T->setVar('__tipo_content', current($this->__types)->content);
			$T->parse('bloque_tipo_default', 'BLOQUE_TIPO_DEFAULT', false);
		}

		if($this->report_list === "true") {
			$T->parse('bloque_informes_disponibles', 'BLOQUE_INFORMES_DISPONIBLES', true);
		}

		switch($this->date_selection) {
			case "no":
				$T->setVar('__calendario_permite_seleccionar', 'false');
				$T->setVar('__calendario_selecciona_intervalo', 'false');
				break;
			case "day":
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'false');
				break;
			case "interval":
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'true');
				break;
			default:
				$T->setVar('__calendario_permite_seleccionar', 'true');
				$T->setVar('__calendario_selecciona_intervalo', 'true');
		}

		return $T->parse('out', 'tpl_especial');
	}

	function getHorarios() {
		global $usr;

		if (count($this->__horarios) > 0) {
			return $this->__horarios;
		}
		elseif ($this->horarios_resource == "user") {
			$this->__horarios = $usr->getHorarios(REP_HORARIO_HABIL,0,true);
			$this->__horarios[0] = $usr->getHorario(0);
			ksort($this->__horarios);
			return $this->__horarios;
		}elseif ($this->horarios_resource == "client") {
			$this->__horarios = $usr->getHorarios(REP_HORARIO_HABIL);
			$this->__horarios[0] = $usr->getHorario(0);
			ksort($this->__horarios);
			return $this->__horarios;
		}
		else {
			return null;
		}
	}

	function getHorario($horario_id) {
		global $usr;

		if (isset($this->__horarios[$horario_id])) {
			return $this->__horarios[$horario_id];
		}
		elseif ($this->horarios_resource == "client") {
			return $usr->getHorario($horario_id, REP_HORARIO_HABIL);
		}
		elseif ($this->horarios_resource == "user") {
			return $usr->getHorario($horario_id, REP_HORARIO_HABIL);
		}
		else {
			return null;
		}
	}

	function getSubobjetivos() {
		global $usr;

		if (count($this->__objetivos) > 0) {
			return $this->__objetivos;
		}
		elseif ($this->objetivos_resource == "client") {
			$this->__objetivos = $usr->getObjetivos(REP_DATOS_MONITOREO);
			return $this->__objetivos;
		}
		elseif ($this->objetivos_resource == "user") {
			$this->__objetivos = $usr->getObjetivos(REP_DATOS_USUARIO);
			return $this->__objetivos;
		}
		else {
			return null;
		}
	}

	function getSubobjetivo($subobjetivo_id) {
		global $usr;

		if (isset($this->__objetivos[$subobjetivo_id])) {
			return $this->__objetivos[$subobjetivo_id];
		}
		elseif ($this->objetivos_resource == "client") {
			return $usr->getObjetivo($subobjetivo_id, REP_DATOS_MONITOREO);
		}
		elseif ($this->objetivos_resource == "user") {
			return $usr->getObjetivo($subobjetivo_id, REP_DATOS_USUARIO);
		}
		else {
			return null;
		}
	}

	function getType($valor, $orden = false) {
		if($orden != false){
			$type = $this->__types[$valor];
			return $type;
		}
		else{
			foreach ($this->__types as $type) {
				if ($type->content == $valor) {
					return $type;
				}
			}
		}
		return null;
	}

	function getTypeById($type_id) {
		$type = $this->__types[$type_id];
		if ($type != null) {
			return $type;
		}
		else {
			return null;
		}
	}

	function setCache() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($this->__xml_config);

		$xpath = new DOMXpath($dom);

		$tag_cache = $xpath->query("/atentus/cache")->item(0);
		if ($tag_cache == null) {
			$tag_especial = $xpath->query("/atentus")->item(0);
			$tag_cache = $dom->createElement("cache");
			$tag_especial->appendChild($tag_cache);
		}

		if (isset($_REQUEST["subobjetivo_id"])) {
			$tag_objetivo = $dom->createElement("param");
			$tag_objetivo->setAttribute('subobjetivo_id', $_REQUEST["subobjetivo_id"]);

			foreach ($_REQUEST as $id => $value) {
				if ($value != null and $value != "") {
					if (ereg ("man_paso_".$_REQUEST["subobjetivo_id"]."_([0-9]*)", $id, $ids)) {
						foreach (explode("-", $value) as $horario_id) {
							$tag_param = $dom->createElement("param");
							$tag_param->setAttribute('paso_orden', $ids[1]);
							$tag_param->setAttribute('horario_id', $horario_id);
							$tag_objetivo->appendChild($tag_param);
						}
					}
				}
			}

			$tag_remove = $xpath->query("/atentus/cache/param[@subobjetivo_id=".$_REQUEST["subobjetivo_id"]."]")->item(0);
			if ($tag_remove != null) {
				$tag_cache->removeChild($tag_remove);
			}

			$tag_cache->appendChild($tag_objetivo);
		}

		$sql = "SELECT * FROM public.objetivo_modifica(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", NULL,'".
				pg_escape_string($dom->saveXML())."','f')";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}

	function getCacheMantencion() {
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($this->__xml_config);
		$xpath = new DOMXpath($dom);

		foreach ($xpath->query("/atentus/cache/param") as $param) {
			$this->cache[$param->getAttribute("subobjetivo_id")] = array();
			foreach ($xpath->query("param", $param) as $subparam) {
				$this->cache[$param->getAttribute("subobjetivo_id")][$subparam->getAttribute("paso_orden")][] = $subparam->getAttribute("horario_id");
			}
		}
		return $this->cache;
	}


	static function _validaIntervalo($val) {
		return preg_match("/^[-]?(\d+)\s(day|month|year|week)[s]?$/i", $val);
	}

	static function _validaFecha($val) {
		return preg_match("/^(?:20|19)[0-9]{2}([-.\\/])(?:0?[1-9]|1[012])\\1(?:0?[1-9]|[12][0-9]|3[01])$/", $val);
	}
}

?>
