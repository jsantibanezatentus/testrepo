<?

class ConfigObjetivo extends Objetivo {

	var $__setups;
	var $pagina_completa;
	/**
	 * Constructor.
	 *
	 * @param integer $objetivo_id
	 * @return Objetivo
	 */
	function ConfigObjetivo($objetivo_id) {
		
		$this->objetivo_id = $objetivo_id;
		$this->__setups = array();
		
		$this->__Objetivo();

		/* EJECUTE LA FUNCION QUE SETEA LA CONFIGURACION.
		 * DEPENDIENDO EL TIPO DE OBJETIVO. */
		if ($this->getServicio()->getTipoSetup() == REP_SETUP_DNS) {
			$this->setDnsSetup();
		}
		elseif ($this->getServicio()->getTipoSetup() == REP_SETUP_MAIL) {
			$this->setMailSetup();
		}
		elseif ($this->getServicio()->getTipoSetup() == REP_SETUP_WEB) {
			$this->setWebSetup();
		}
		elseif ($this->getServicio()->getTipoSetup() == REP_SETUP_BROWSER) {
			$this->setBrowserSetup();
		}
		elseif ($this->getServicio()->getTipoSetup() == REP_SETUP_MOBILE) {
			$this->setMobileSetup();
		}
		elseif ($this->getServicio()->getTipoSetup() == REP_SETUP_IVR) {
			$this->setIvrSetup();
		}
		elseif ($this->getServicio()->getTipoSetup() == REP_SETUP_NEW_RELIC) {
			$this->setNewRelic();
		}
		elseif ($this->getServicio()->getTipoSetup() == REP_SETUP_AUDEX) {
			$this->setAudex();
		}
		elseif ($this->getServicio()->getTipoSetup() == REP_SETUP_ATDEX) {
			$this->setAudex();
		}
        elseif ($this->getServicio()->getTipoSetup() == REP_SETUP_NEW_RELIC_RUM) {
			$this->setNewRelicRUM();
		}
        elseif ($this->getServicio()->getTipoSetup() == REP_SETUP_NEW_RELIC_MOBILE) {
			$this->setNewRelicMobile();
		}
	}

	/**
	 * Funcion que setea los parametros de un objetivo tipo Dns.
	 * Ver en clase Servicio los protocolos de cada tipo.
	 */
	function setDnsSetup() {
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($this->__xml_config);
		$xpath = new DOMXpath($dom);
		
		foreach ($xpath->query("/atentus/config/".$this->getServicio()->getTagConfigXML()) as $config) {
			$this->timeout = $config->getAttribute('timeout');
            
			/* LISTA DE SETUP DEL OBJETIVO MAIL. 
			 * PUEDEN EXISTIR DIFERENCIAS ENTRE LOS MONITORES. */
			foreach ($xpath->query("paso/".$this->getServicio()->getTagSetupXML(), $config) as $detail) {
				$setup = new DnsSetup($detail->getAttribute('monitor_id'));
				$setup->dominio = $detail->getAttribute('dominio');
				$setup->resolver = $detail->getAttribute('resolver');
				$setup->patron = $detail->getAttribute('patron');
				$setup->consulta = $detail->getAttribute('consulta');
				$setup->tipo = $detail->getAttribute('tipo');
				$this->__setups[$setup->monitor_id] = $setup;
			}
		}		
	}
	
	/**
	 * Funcion que setea los parametros de un objetivo tipo Mail.
	 * Ver en clase Servicio los protocolos de cada tipo.
	 */
	function setMailSetup() {
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($this->__xml_config);
		$xpath = new DOMXpath($dom);
		
		foreach ($xpath->query("/atentus/config/".$this->getServicio()->getTagConfigXML()) as $config) {
			$this->timeout = $config->getAttribute('timeout');

			/* LISTA DE SETUP DEL OBJETIVO MAIL. 
			 * PUEDEN EXISTIR DIFERENCIAS ENTRE LOS MONITORES. */
			foreach ($xpath->query("paso/".$this->getServicio()->getTagSetupXML(), $config) as $detail) {
				$setup = new MailSetup($detail->getAttribute('monitor_id'));
				$setup->dominio = $detail->getAttribute('servidor');
				$setup->dominio_tipo = $detail->getAttribute('protocolo');
				$setup->dominio_timeout = $detail->getAttribute('timeout_server');
				$setup->destinatario = $detail->getAttribute('destinatario');
				$setup->remitente = $detail->getAttribute('remitente');
				$setup->usuario = $detail->getAttribute('usuario');
				$setup->clave = $detail->getAttribute('password');
				$setup->metodo = $detail->getAttribute('metodo');
				$this->__setups[$setup->monitor_id] = $setup;
			}
		}
	}
	
	/**
	 * Funcion que setea los parametros de un objetivo tipo Web.
	 * Ver en clase Servicio los protocolos de cada tipo.
	 */
	function setWebSetup() {
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($this->__xml_config);
		$xpath = new DOMXpath($dom);
		
		$nuevo = $this->getServicio()->tieneNuevoConfig();
		$tipo_patron = array(0 => "String", 1 => "Expresion", 2 => "String");
		
		foreach ($xpath->query("/atentus/config/".$this->getServicio()->getTagConfigXML()) as $config) {

			$this->timeout = $config->getAttribute('timeout');
			
			/* LISTA DE PASOS */
			foreach ($xpath->query("paso[@visible=1 or not(@visible)]", $config) as $tag_paso) {
				$paso = new Paso();
				$paso->paso_id = $tag_paso->getAttribute('paso_orden');
				$paso->nombre = $tag_paso->getAttribute('nombre');

				/* LISTA DE SETUP DEL PASO. 
				 * PUEDEN EXISTIR DIFERENCIAS ENTRE LOS MONITORES. */
				foreach ($xpath->query(($nuevo)?"ejecutar":$this->getServicio()->getTagSetupXML(), $tag_paso) as $setup_paso) {
					$setup = new PasoSetup($setup_paso->getAttribute('monitor_id'));
					if ($nuevo) {
						$setup->url = $xpath->query("url/href", $setup_paso)->item(0)->nodeValue;
						$setup->metodo = $xpath->query("url/metodo", $setup_paso)->item(0)->nodeValue;
						// TODO: ver por que no funciona en reporte-beta.
//						$setup->timeout = $xpath->query("timeout[@monitor_id=".$setup_paso->getAttribute('monitor_id')." or not(@monitor_id)]/paso", $tag_paso)->item(0)->nodeValue;
					}
					else {
						$setup->url = $setup_paso->getAttribute(($setup_paso->getAttribute('uri'))?"uri":"url");
						$setup->metodo = ($setup_paso->getAttribute('metodo'))?$setup_paso->getAttribute('metodo'):"GET";
						$setup->timeout = $setup_paso->getAttribute('timeout');
						if(empty($setup->timeout)){
							$setup->timeout =$this->timeout;
						}
					}
					$paso->__setups[$setup->monitor_id] = $setup;
				}

				/* LISTA DE PATRONES DEL PASO. 
				 * PUEDEN EXISTIR DIFERENCIAS ENTRE LOS MONITORES. */
				foreach ($xpath->query(($nuevo)?"patrones/patron":"patron", $tag_paso) as $tag_patron) {
					$patron = new Patron();
					$patron->orden = $tag_patron->getAttribute('orden');
					$patron->nombre = $tag_patron->getAttribute('nombre');
					if ($nuevo) {
						$patron->valor = $xpath->query("valor", $tag_patron)->item(0)->nodeValue;
						$patron->tipo = $tipo_patron[$xpath->query("tipo", $tag_patron)->item(0)->nodeValue];
						$patron->es_opcional = ($xpath->query("es_opcional", $tag_patron)->item(0)->nodeValue)?true:false;
						$patron->es_inverso = ($xpath->query("es_inverso", $tag_patron)->item(0)->nodeValue)?true:false;
					}
					else {
						$patron->valor = $tag_patron->getAttribute('patron');
						$patron->tipo = ($tag_patron->getAttribute('tipo'))?$tipo_patron[$tag_patron->getAttribute('tipo')]:"String";
						$patron->es_opcional = false;
						$patron->es_inverso = (($tag_patron->getAttribute('tipo')=='2')?true:false);
					}
					$paso->addPatron($patron, $tag_patron->getAttribute('monitor_id'));
				}
				$this->__pasos[$paso->paso_id] = $paso;
			}
		}
	}

	/**
	 * Funcion que setea los parametros de un objetivo tipo Browser.
	 * Ver en clase Servicio los protocolos de cada tipo.
	 */
	function setIvrSetup() {
		
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($this->__xml_config);
		$xpath = new DOMXpath($dom);

		//$nuevo = $this->getServicio()->tieneNuevoConfig();
		
		foreach ($xpath->query("/atentus/config/".$this->getServicio()->getTagConfigXML()) as $config) {
		
			foreach ($xpath->query("setup", $config) as $tag_setup) {
				$this->timeout = $tag_setup->getAttribute('timeoutroot');
			}
			
			
			/* LISTA DE PASOS */
			$this->__pasos = array();
			foreach ($xpath->query("paso[@visible=1 or not(@visible)]", $config) as $tag_paso) {
				$paso = new Paso();
				$paso->paso_id = $tag_paso->getAttribute('paso_orden');
				$paso->nombre = $tag_paso->getAttribute('nombre');
			
				/* LISTA DE SETUP DEL PASO.
				 * PUEDEN EXISTIR DIFERENCIAS ENTRE LOS MONITORES. */
				foreach ($xpath->query($this->getServicio()->getTagSetupXML(), $tag_paso) as $setup_paso) {
					$setup = new PasoSetup($setup_paso->getAttribute('monitor_id'));
					$setup->timeout = $setup_paso->getAttribute('timeoutstep');
					$paso->__setups[$paso->paso_id] = $setup;
				}
				
				/* LISTA DE DTMF DEL PASO.
				 * PUEDEN EXISTIR DIFERENCIAS ENTRE LOS MONITORES. */
				if($xpath->query('dtmf', $tag_paso)->length>0){
					$dtmf=$xpath->query('dtmf', $tag_paso)->item(0);
					$paso->__dtmf=$dtmf->getAttribute('valor');
				}

				/* LISTA DE NUMERO LLAMADA DEL PASO.
				 * PUEDEN EXISTIR DIFERENCIAS ENTRE LOS MONITORES. */
				if($xpath->query('numero_llamada', $tag_paso)->length>0){
					$llamada=$xpath->query('numero_llamada', $tag_paso)->item(0);
					$paso->__numero_llamada= $llamada->getAttribute('valor');
				}
				
				/* LISTA DEL AUDIO DEL PASO.
				 * PUEDEN EXISTIR DIFERENCIAS ENTRE LOS MONITORES. */
				if($xpath->query('audio', $tag_paso)->length>0){
					$audio=$xpath->query('audio', $tag_paso)->item(0);
					$paso->__audio= $audio->getAttribute('path');
				}
				
				$this->__pasos[$paso->paso_id] = $paso;
			}
		}
	}
	/**
	 * Funcion que setea los parametros de un objetivo tipo Browser.
	 * Ver en clase Servicio los protocolos de cada tipo.
	 */
	function setBrowserSetup() {
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($this->__xml_config);
		$xpath = new DOMXpath($dom);
		$nuevo = $this->getServicio()->tieneNuevoConfig();
		
		foreach ($xpath->query("/atentus/config/".$this->getServicio()->getTagConfigXML()) as $config) {
            
			foreach ($xpath->query("setup", $config) as $tag_setup) {

				$this->timeout = $tag_setup->getAttribute('timeoutroot');
				$this->timeoutstep = $tag_setup->getAttribute('timeoutstep');
				$tag_pagina_completa = $xpath->query("timeout",$tag_setup)->item(0);
				$this->pagina_completa = isset($tag_pagina_completa)?$tag_pagina_completa->getAttribute('pagina_completa'):"N/A";
				
			    
			}   
			/* LISTA DE PASOS */
			$this->__pasos = array(); 
			foreach ($xpath->query("paso[@visible=1 or not(@visible)]", $config) as $tag_paso) {
				$paso = new Paso();
				$paso->paso_id = $tag_paso->getAttribute('paso_orden');
				$paso->nombre = $tag_paso->getAttribute('nombre');

				/* LISTA DE SETUP DEL PASO. 
				 * PUEDEN EXISTIR DIFERENCIAS ENTRE LOS MONITORES. */
              
				foreach ($xpath->query($this->getServicio()->getTagSetupXML(), $tag_paso) as $setup_paso) {
					$setup = new PasoSetup($setup_paso->getAttribute('monitor_id'));
					$setup->timeout = $setup_paso->getAttribute('timeoutstep');
					if (empty($setup->timeout)) {
						$setup->timeout = $this->timeoutstep;
    				}
					$paso->__setups[$setup->monitor_id] = $setup;
                    $tag_timeout=$xpath->query("timeout", $setup_paso);
                    if($tag_timeout->length == 0 && empty($setup->timeout)){
                    	$setup = new PasoSetup($setup_paso->getAttribute('monitor_id'));
						$setup->timeout = $this->pagina_completa;
						$paso->__setups[$setup->monitor_id] = $setup;
                    } 
                    foreach ($tag_timeout as $time_out){
                            $setup = new PasoSetup($time_out->getAttribute('monitor_id'));
							$setup->timeout = $time_out->getAttribute('pagina_completa');

							if (empty($setup->timeout)) {
								$setup->timeout = $this->pagina_completa;
	    					}
							$paso->__setups[$setup->monitor_id] = $setup;
						}			
   					
                 } 
                
				/* LISTA DE URL DEL PASO.
				 * PUEDEN EXISTIR DIFERENCIAS ENTRE LOS MONITORES. */
				foreach ($xpath->query("url", $tag_paso) as $url_paso) {
                      
					$monitor_id = ($url_paso->getAttribute('monitor_id'))?$url_paso->getAttribute('monitor_id'):0;
					if (!isset($paso->__setups[$monitor_id]) and isset($paso->__setups[0])) {
						$paso->__setups[$monitor_id] = clone $paso->__setups[0];
					}
					elseif (!isset($paso->__setups[$monitor_id])) {
						$paso->__setups[$monitor_id] = new PasoSetup($monitor_id);
					}
					$paso->__setups[$monitor_id]->url = $url_paso->getAttribute('href');

					if(empty($paso->__setups[$monitor_id]->url)){
						$paso->__setups[$monitor_id]->metodo = "POST";
					}
					elseif (!empty($paso->__setups[$monitor_id]->url)) {
						$paso->__setups[$monitor_id]->metodo = "GET";
					}
					
					
				}

				/* LISTA DE SCRIPTS DEL PASO.
				 * PUEDEN EXISTIR DIFERENCIAS ENTRE LOS MONITORES. */
				foreach ($xpath->query("script", $tag_paso) as $script_paso) {
					$monitor_id = ($script_paso->getAttribute('monitor_id'))?$script_paso->getAttribute('monitor_id'):0;
					if (!isset($paso->__setups[$monitor_id]) and isset($paso->__setups[0])) {
						$paso->__setups[$monitor_id] = clone $paso->__setups[0];
					}
					elseif (!isset($paso->__setups[$monitor_id])) {
						$paso->__setups[$monitor_id] = new PasoSetup($monitor_id);
					}
				//	$paso->__setups[$monitor_id]->metodo = $script_paso->getAttribute('tipo');
				}
				
				foreach ($xpath->query("ejecutar", $tag_paso) as $ejecutar_paso) {
					$monitor_id = ($ejecutar_paso->getAttribute('monitor_id'))?$ejecutar_paso->getAttribute('monitor_id'):0;
					if (!isset($paso->__setups[$monitor_id]) and isset($paso->__setups[0])) {
						$paso->__setups[$monitor_id] = clone $paso->__setups[0];
					}
					elseif (!isset($paso->__setups[$monitor_id])) {
						$paso->__setups[$monitor_id] = new PasoSetup($monitor_id);
					}
					$findTagUrl = $xpath->query("url", $ejecutar_paso);
                                        $findTagHref = $xpath->query("url/href", $ejecutar_paso);

                                        
                                        if($findTagUrl->length){
                                            $paso->__setups[$monitor_id]->url = $xpath->query("url", $ejecutar_paso)->item(0)->nodeValue;
                                        }
                                        elseif($findTagHref->length){
                                            $paso->__setups[$monitor_id]->url = $xpath->query("url/href", $ejecutar_paso)->item(0)->nodeValue;
                                        }
					if(empty($paso->__setups[$monitor_id]->url)){
						$paso->__setups[$monitor_id]->metodo = "POST";
					}
					elseif (!empty($paso->__setups[$monitor_id]->url)) {
						$paso->__setups[$monitor_id]->metodo = "GET";
					}
					//$paso->__setups[$monitor_id]->metodo = ($xpath->query("scripts/script/tipo", $ejecutar_paso)->item(0))?$xpath->query("scripts/script/tipo", $ejecutar_paso)->item(0)->nodeValue:"GET";
					
				}
				foreach ($xpath->query("patron", $tag_paso) as $patron_tipo) {
					$monitor_id = ($patron_tipo->getAttribute('monitor_id'))?$patron_tipo->getAttribute('monitor_id'):0;
					if (!isset($paso->__setups[$monitor_id]) and isset($paso->__setups[0])) {
						$paso->__setups[$monitor_id] = clone $paso->__setups[0];
					}
					elseif (!isset($paso->__setups[$monitor_id])) {
						$paso->__setups[$monitor_id] = new PasoSetup($monitor_id);
					}

					//$paso->__setups[$monitor_id]->metodo = $patron_tipo->getAttribute('tipo');
					//$paso->__setups[$monitor_id]->metodo = ($xpath->query($patron_tipo)->item(0))?$xpath->query($patron_tipo)->item(0)->nodeValue:"GET";
					
				}
				/* LISTA DE PATRONES DEL PASO. 
				 * PUEDEN EXISTIR DIFERENCIAS ENTRE LOS MONITORES. */
				foreach ($xpath->query(($nuevo)?"patrones/patron":"patron", $tag_paso) as $tag_patron) {

					$patron = new Patron();
					$patron->orden = $tag_patron->getAttribute('orden');
					$patron->nombre = $tag_patron->getAttribute('nombre');
					if ($nuevo) {
						$patron->valor = $xpath->query("valor", $tag_patron)->item(0)->nodeValue;
						//$patron->tipo = $xpath->query("tipo", $tag_patron)->item(0)->nodeValue;
						$patron->es_opcional = ($xpath->query("es_opcional", $tag_patron)->item(0)->nodeValue)?true:false;
						$patron->es_inverso = ($xpath->query("es_inverso", $tag_patron)->item(0)->nodeValue)?true:false;
					}
					else {
						$patron->valor = $tag_patron->getAttribute('valor');
						//$patron->tipo = $tag_patron->getAttribute('tipo');
						$patron->es_opcional = (($tag_patron->getAttribute('es_opcional') == 't')?true:false);
						$patron->es_inverso = (($tag_patron->getAttribute('es_inverso')=='t')?true:false);
					}
					$paso->addPatron($patron, $tag_patron->getAttribute('monitor_id'));
				}
				 foreach ($xpath->query("patron", $tag_paso) as $tag_patron) {
					$patron = new Patron();
					$patron->orden = $tag_patron->getAttribute('orden');
					$patron->nombre = $tag_patron->getAttribute('nombre');
					if ($nuevo) {
						$patron->valor = $tag_patron->nodeValue;
						$patron->tipo = $tag_patron->getAttribute('tipo');
						$patron->tipo = (empty($patron->tipo)) ? "N/A" : $tag_patron->getAttribute('tipo');
						$patron->es_opcional = (($tag_patron->getAttribute('es_opcional') == 't')?true:false);
						$patron->es_inverso = (($tag_patron->getAttribute('es_inverso')=='t')?true:false);
					}
					else {
						$patron->valor = $tag_patron->getAttribute('valor');
						$patron->valor = (empty($patron->valor)) ? $tag_patron->nodeValue : $tag_patron->getAttribute('valor');
						$patron->tipo = $tag_patron->getAttribute('tipo');
						$patron->es_opcional = (($tag_patron->getAttribute('es_opcional') == 't')?true:false);
						$patron->es_inverso = (($tag_patron->getAttribute('es_inverso')=='t')?true:false);
					}
					$paso->addPatron($patron, $tag_patron->getAttribute('monitor_id'));
				}
				
				foreach ($xpath->query("log", $tag_paso) as $key=>$tag_log) {
					$logs = new stdClass();
					$logs->on_ok= $tag_log->getAttribute('on_ok');
					$logs->on_error=$tag_log->getAttribute('on_error');
					$paso->__logs[$key]=$logs;
					
				}
				
				
				$this->__pasos[$paso->paso_id] = $paso;
			}
		}
		
	}
	
	/*
	 * Funcion que setea los parametros de un objetivo tipo Mobile.
	 * Ver en clase Servicio los protocolos de cada tipo.
	 */
	function setMobileSetup() {
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($this->__xml_config);
		$xpath = new DOMXpath($dom);
		
		$preloads = array();

		$version_mobile = $xpath->query("/atentus/config")->item(0)->getAttribute('version');
		
		foreach ($xpath->query("/atentus/config/".$this->getServicio()->getTagConfigXML()) as $config) {
			$this->timeout = $config->getAttribute('timeout');

			/* INSTRUCCIONES PRE-CARGADAS */
			foreach ($xpath->query("preload", $config) as $tag_preload) {
				$monitor_id = ($tag_preload->getAttribute('monitor_id'))?$tag_preload->getAttribute('monitor_id'):0;
				if (!isset($preloads[$monitor_id])) {
					$preloads[$monitor_id] = array();
				}
				$value = str_replace(array_keys($preloads[$monitor_id]), $preloads[$monitor_id], $tag_preload->getAttribute('value'));
				$preloads[$monitor_id]["{".$tag_preload->getAttribute('name')."}"] = $value;
			}
			
			/* LISTA DE PASOS */
			foreach ($xpath->query("paso[@visible=1 or not(@visible)]", $config) as $tag_paso) {
				$paso = new Paso();
				$paso->paso_id = $tag_paso->getAttribute('paso_orden');
				$paso->nombre = $tag_paso->getAttribute('nombre');
				$paso->timeout = $tag_paso->getAttribute('timeout');

				/* LISTAS DE INSTRUCCIONES DEL PASO. 
				 * QUE PARA ESTE CASO SERA CONSIDERADO COMO EL SETUP DEL PASO.
				 * PUEDEN EXISTIR DIFERENCIAS ENTRE LOS MONITORES. */
				foreach ($xpath->query("instruccion", $tag_paso) as $setup_paso) {
					$setup = new PasoSetup($setup_paso->getAttribute('monitor_id'));
					if (!isset($preloads[$setup->monitor_id])) {
						$setup->comando = $setup_paso->getAttribute('comando');
					}
					else {
						$setup->comando = str_replace(array_keys($preloads[$setup->monitor_id]), $preloads[$setup->monitor_id], $setup_paso->getAttribute('comando'));
					}
					$setup->metodo = "USSD";
					$setup->timeout = $tag_paso->getAttribute('timeout');
					$paso->__setups[$setup->monitor_id] = $setup;
				}

				/* LISTA DE PATRONES DEL PASO. 
				 * PUEDEN EXISTIR DIFERENCIAS ENTRE LOS MONITORES. */
				foreach ($xpath->query("patron", $tag_paso) as $tag_patron) {
					$patron = new Patron();
					$patron->orden = $tag_patron->getAttribute('orden');
					$patron->nombre = $tag_patron->getAttribute('nombre');
					$patron->valor = ($version_mobile == '1.0')?$tag_patron->getAttribute('valor'):$tag_patron->getAttribute('patron');
					$patron->tipo = $tag_patron->getAttribute('tipo');
					$patron->es_opcional = (($tag_patron->getAttribute('es_opcional') == '1')?true:false);
					$patron->es_inverso = (($tag_patron->getAttribute('es_inverso')=='1')?true:false);
					$paso->addPatron($patron, $tag_patron->getAttribute('monitor_id'));
				}
				$this->__pasos[$paso->paso_id] = $paso;
			}
		}
	}
	/**
	 * Funcion que setea los parametros de un objetivo tipo New Relic.
	 * Ver en clase Servicio los protocolos de cada tipo.
	 */
	function setNewRelic() {
            $dom = new DomDocument();
            $dom->preserveWhiteSpace = FALSE;
            $dom->loadXML($this->__xml_config);
            $xpath = new DOMXpath($dom);
            foreach ($xpath->query("/atentus/config/apm") as $config) {
                $this->nombre= $config->getAttribute('nombre');
                $this->descripcion= $config->getAttribute('descripcion');
                $cont=0;
               
                /* LISTA DE COMPONENTES. */
                foreach ($xpath->query("panel/componente", $config) as $componente) {
                    $cont_orden=0;
                    $cont_url=0;
                    $cont_inf=0;
                    $datos = new stdClass();
                    $datos->tipo = $componente->getAttribute('tipo');
                    $datos->captura = $componente->getAttribute('captura');
                    $datos->titulo = $componente->getAttribute('titulo');
                    $datos->visible = $componente->getAttribute('visible');
                    foreach($xpath->query("grupo/elemento", $componente) as $elemento){
                        $datos->orden[$cont_orden] = $elemento->getAttribute('orden');
                        $datos->titulo [$cont_orden]= $elemento->getAttribute('titulo');
                        
                        foreach ($xpath->query("url", $elemento) as  $tag_url) {
                            $datos->url[$cont_url] = $tag_url->nodeValue;
                            $cont_url++;
                        }
                        foreach ($xpath->query("informacion", $elemento) as  $tag_informacion) {
                            $datos->informacion[$cont_inf] = $tag_informacion->nodeValue;
                            $cont_inf++;
                        }
                        $datos->link[$cont_orden]  = $xpath->query("link", $elemento)->item(0)->nodeValue;
                        $cont_orden++;
                        
                    }
                        
                    $this->__datos[$cont] = $datos;
                    $cont++;
                }
            }
        }
        /**
	 * Funcion que setea los parametros de un objetivo tipo New Relic.
	 * Ver en clase Servicio los protocolos de cada tipo.
	 */
	function setNewRelicRUM() {
            $dom = new DomDocument();
            $dom->preserveWhiteSpace = FALSE;
            $dom->loadXML($this->__xml_config);
            $xpath = new DOMXpath($dom);
            foreach ($xpath->query("/atentus/config/rum") as $config) {
                $this->nombre= $config->getAttribute('nombre');
                $this->descripcion= $config->getAttribute('descripcion');
                $cont=0;
               
                /* LISTA DE COMPONENTES. */
                foreach ($xpath->query("panel/componente", $config) as $componente) {
                    $cont_orden=0;
                    $cont_url=0;
                    $cont_inf=0;
                    $datos = new stdClass();
                    $datos->tipo = $componente->getAttribute('tipo');
                    $datos->captura = $componente->getAttribute('captura');
                    $datos->titulo = $componente->getAttribute('titulo');
                    $datos->visible = $componente->getAttribute('visible');
                    foreach($xpath->query("grupo/elemento", $componente) as $elemento){
                        $datos->orden[$cont_orden] = $elemento->getAttribute('orden');
                        $datos->titulo [$cont_orden]= $elemento->getAttribute('titulo');
                        
                        foreach ($xpath->query("url", $elemento) as  $tag_url) {
                            $datos->url[$cont_url] = $tag_url->nodeValue;
                            $cont_url++;
                        }
                        foreach ($xpath->query("informacion", $elemento) as  $tag_informacion) {
                            $datos->informacion[$cont_inf] = $tag_informacion->nodeValue;
                            $cont_inf++;
                        }
                        $datos->link[$cont_orden]  = $xpath->query("link", $elemento)->item(0)->nodeValue;
                        $cont_orden++;
                        
                    }
                        
                    $this->__datos[$cont] = $datos;
                    $cont++;
                }
            }
        }
        /**
	 * Funcion que setea los parametros de un objetivo tipo New Relic MOBILE.
	 * Ver en clase Servicio los protocolos de cada tipo.
	 */
	function setNewRelicMobile() {
            $dom = new DomDocument();
            $dom->preserveWhiteSpace = FALSE;
            $dom->loadXML($this->__xml_config);
            $xpath = new DOMXpath($dom);
            foreach ($xpath->query("/atentus/config/mobile_newrelic") as $config) {
                $this->nombre= $config->getAttribute('nombre');
                $this->descripcion= $config->getAttribute('descripcion');
                $cont=0;
               
                /* LISTA DE COMPONENTES. */
                foreach ($xpath->query("panel/componente", $config) as $componente) {
                    $cont_orden=0;
                    $cont_url=0;
                    $cont_inf=0;
                    $datos = new stdClass();
                    $datos->tipo = $componente->getAttribute('tipo');
                    $datos->captura = $componente->getAttribute('captura');
                    $datos->titulo = $componente->getAttribute('titulo');
                    $datos->visible = $componente->getAttribute('visible');
                    foreach($xpath->query("grupo/elemento", $componente) as $elemento){
                        $datos->orden[$cont_orden] = $elemento->getAttribute('orden');
                        $datos->titulo [$cont_orden]= $elemento->getAttribute('titulo');
                        
                        foreach ($xpath->query("url", $elemento) as  $tag_url) {
                            $datos->url[$cont_url] = $tag_url->nodeValue;
                            $cont_url++;
                        }
                        foreach ($xpath->query("informacion", $elemento) as  $tag_informacion) {
                            $datos->informacion[$cont_inf] = $tag_informacion->nodeValue;
                            $cont_inf++;
                        }
                        $datos->link[$cont_orden]  = $xpath->query("link", $elemento)->item(0)->nodeValue;
                        $cont_orden++;
                        
                    }
                        
                    $this->__datos[$cont] = $datos;
                    $cont++;
                }
            }
        }
        
    /**
     * Funcion que setea los parametros de un objetivo tipo New Relic .
     * Ver en clase Servicio los protocolos de cada tipo.
    */
    function setAudex() {
    	$dom = new DomDocument();
    	$dom->preserveWhiteSpace = FALSE;
    	$dom->loadXML($this->__xml_config);
    	$xpath = new DOMXpath($dom);
    	
    	foreach ($xpath->query("/atentus/config/audex") as $config) {

    		$this->nombre= $config->getAttribute('nombre');
    		$this->descripcion= $config->getAttribute('descripcion');
    		$this->umbral_excelente= $config->getAttribute('umbral_excelente');
    		$this->umbral_bueno= $config->getAttribute('umbral_bueno');
    		$this->umbral_satisfactorio= $config->getAttribute('umbral_satisfactorio');
    		$this->umbral_intolerable= $config->getAttribute('umbral_intolerable');
    		
    		foreach ($xpath->query("objetivos/objetivo",$config) as $conf_objetivo) {
    			
    			$datos = new stdClass();
    			$datos->objetivo_id = $conf_objetivo->getAttribute('objetivo_id');
    			$datos->errores_dependencia = $conf_objetivo->getAttribute('errores_dependencia');
    			
    			foreach ($xpath->query("paso",$conf_objetivo) as $conf_paso) {
    				$paso = new Paso();
    				$paso->paso_id = $conf_paso->getAttribute('paso_orden');
    				$paso->nombre = $conf_paso->getAttribute('nombre'); 
    				$paso->dependencia = $conf_paso->getAttribute('dependencia');
    				$paso->identificador = $conf_paso->getAttribute('identificador');
    				$datos->__pasos[$paso->paso_id] = $paso;
    			}
    		}
    	}
    }    
	
	/**
	 * Funcion que modifica un objetivo.
	 */
	function modificar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		
		Validador::campoVacio($this->nombre, "Nombre");
		Validador::existeNombreObjetivo(true, $this->nombre, $this->objetivo_id);
		
		if ($this->sla_dis_ok == "") {
			$this->sla_dis_ok = "NULL";
		}
		else {
			Validador::campoNumerico($this->sla_dis_ok, "SLA Disponibilidad Ok", 0, 100);
		}
		if ($this->sla_dis_error == "") {
			$this->sla_dis_error = "NULL";
		}
		else {
			Validador::campoNumerico($this->sla_dis_error, "SLA Disponibilidad Error", 0, ($this->sla_dis_ok == "NULL")?100:$this->sla_dis_ok);
		}
		if ($this->sla_ren_ok == "") {
			$this->sla_ren_ok = "NULL";
		}
		else {
			Validador::campoNumerico($this->sla_ren_ok, "SLA Rendimiento Ok", 0, 600);
		}
		if ($this->sla_ren_error == "") {
			$this->sla_ren_error = "NULL";
		}
		else {
			Validador::campoNumerico($this->sla_ren_error, "SLA Rendimiento Error", ($this->sla_ren_ok == "NULL")?0:$this->sla_ren_ok, 600);
		}
		
		
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($this->__xml_config);
		$xpath = new DOMXpath($dom);

		foreach ($xpath->query("/atentus/config/".$this->getServicio()->getTagConfigXML()) as $config) {
			$config->setAttribute("nombre", $this->nombre);
			$config->setAttribute("descripcion", $this->descripcion);

			if (in_array($this->getServicio()->getTipoSetup(), array(REP_SETUP_WEB, REP_SETUP_BROWSER, REP_SETUP_MOBILE,REP_SETUP_IVR))) {
				foreach ($xpath->query("paso[@visible=1 or not(@visible)]", $config) as $tag_paso) {
					Validador::campoVacio($this->__pasos[$tag_paso->getAttribute('paso_orden')]->nombre, "Nombre Paso");
					$tag_paso->setAttribute("nombre", $this->__pasos[$tag_paso->getAttribute('paso_orden')]->nombre);
				}
			}
		}

		$slas = "{".$this->sla_ren_ok.",".$this->sla_ren_error.",".$this->sla_dis_ok.",".$this->sla_dis_error."}";
		
		$sql = "SELECT * FROM public.objetivo_modifica(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", '".
				pg_escape_string($slas)."','".
				pg_escape_string($dom->saveXML())."','f')";
//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$log->setChange("MODIFICO OBJETIVO", $this->toString());
	}
	
	function toString() {
		$string = "NOMBRE ".$this->nombre.", ".
				  "OBJETIVO ID ".$this->objetivo_id.", ".
				  "DESCRIPCION ".$this->descripcion.", ".
				  "SLA RENDIMIENTO OK ".$this->sla_ren_ok.", ".
				  "SLA RENDIMIENTO ERROR ".$this->sla_ren_error.", ".
				  "SLA DISPONIBILIDAD OK ".$this->sla_dis_ok.", ".
				  "SLA DISPONIBILIDAD ERROR ".$this->sla_dis_error;
		return $string;
	}

}

?>