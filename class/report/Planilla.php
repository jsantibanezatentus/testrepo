<?

class Planilla {

	var $tiempo_expiracion;
	var $extra;
	var $solicitud;
	var $resultado;

	function Planilla() {
		$this->tiempo_expiracion = 86400;
	}

	public function generarResultado() {
		if (method_exists($this, $this->solicitud)) {
			$metodo_nombre = $this->solicitud;
			$this->$metodo_nombre();
		}
	}



	/*************** FUNCIONES DE PLANILLAS DE DATOS ***************/
	/*************** FUNCIONES DE PLANILLAS DE DATOS ***************/
	/*************** FUNCIONES DE PLANILLAS DE DATOS ***************/

	/**
	 * Funcion para obtener el
	 * CVS de los Datos.
	
	 *Creado por:
	 *Modificado por:Aldo Cruz Romero
	 *Fecha de creacion:3-10-2019
	 *Informacion la rescata desde APi y no de procedimiento
	*/
	function getDatos() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;
    	# Variables para eventos especiales marcados por el cliente codigo 9.
		$event = new Event;
		$graficoSvg = new GraficoSVG();
		$objetive = new Objetivo();
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 
		$timeZone = $arrTime[$timeZoneId];
		$objetivo = new ConfigObjetivo($this->objetivo_id);
		#date_default_timezone_set($timeZone);
		#$fechaTermino = date('Y-m-d H:i:s');
		$marcado = false;
		$dataMant = null;
		$data = null;
		$ids = null;
		$nameFunction = 'datosDisponibilidad';
		$tieneEvento = 'false';

		/* MUESTRA LA TABLA CON EL LINK PARA DESCARGAR */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'descarga_datos.tpl');
		$T->setVar('__item_id', $this->__item_id);
		$T->setVar('__usuario', $usr->usuario_id);
		$T->setVar('__inicio', explode(" ",$this->timestamp->getInicioPeriodo())[0]);
		$T->setVar('__termino',explode(" ",$this->timestamp->getTerminoPeriodo())[0]);
		$T->setVar('__objetivo', $this->objetivo_id);
		$T->setVar('__nombre_objetivo', $objetivo->nombre);
		$T->setVar('__datos_imagen', REP_PATH_IMG.'datos_mediciones.png');

		$this->resultado = $T->parse('out', 'tpl_tabla');

		# Datos para mantenimiento cliente.
		$T->setVar('__tiene_evento', $tieneEvento);
		$T->setVar('__name', $nameFunction);
		# Obtener marcados por separado
		/*
		$sql = "SELECT * FROM reporte._detalle_marcado (".
				pg_escape_string($current_usuario_id).",ARRAY[".
				pg_escape_string($this->objetivo_id)."],'".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		
		$res =& $mdb2->query($sql);
// 		print($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['_detalle_marcado']);
			$xpath = new DOMXpath($dom);
			unset($row["_detalle_marcado"]);
		}
		# Busca los eventos.
		foreach ($xpath->query("/detalles_marcado/detalle/marcado") as $tag_marcado) {
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
		}

		# Verifica y asigna variables en caso de que exista marcado.
		if ($marcado == true) {
			$dataMant = $event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			$tieneEvento = 'true';
			$data = json_encode($dataMant);
		}
*/
		/* SI SE DESCARGO EL CSV */
/*		if ($this->tipo=="csv") {
*/			/* OBTENER LOS DATOS Y PARSEARLO */
/*			$sql = "SELECT * FROM reporte.datos(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).",'".
					pg_escape_string($this->timestamp->getInicioPeriodo())."','".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			//echo($sql);exit;
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
			if ($this->extra["datos_separador"] == 1) {
				$separador = ",";
			}
			else {
				$separador = ";";
			}
			$this->resultado = "";
			$primero = True;
			while($row = $res->fetchRow()) {
*/
				/* SI ES EL ENCABEZADO */
/*				if ($row["filas"]==NULL) {
					$dom = new DomDocument();
					$dom->preserveWhiteSpace = FALSE;
					$dom->loadXML($row['xml']);
					$xpath = new DOMXpath($dom);
					unset($row["xml"]);
					
					$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
				
					$this->resultado.="Datos Objetivo ".$conf_objetivo->getAttribute('nombre')."\n\n";
					$this->resultado.="Leyenda \n";
					$this->resultado.="   Servidor : servidor que realizo el monitoreo. \n";
					$this->resultado.="   Fecha : fecha cuando se realizo el monitoreo. \n";
					$this->resultado.="   Hora : hora cuando se realizo el monitoreo. \n";
					$this->resultado.="   Status : codigo de estado del monitoreo. \n";
					$this->resultado.="   Delay : tiempo de respuesta del monitoreo. \n\n";
					$this->resultado.="Codigos de Estados \n";
					
					$array_codigo= array();
					foreach ($xpath->query("//codigos/codigo") as $codigos) {
						$array_codigo[$codigos->getAttribute('codigo_id')] = $codigos->getAttribute('nombre');
					}
					ksort($array_codigo);
					
					foreach ($array_codigo as $id=> $codigo) {	
						$this->resultado.="   ".$id." : ".$codigo."\n";
					}
					
					$this->resultado.="\n\n";
*/
					/* Datos de mantenimiento marcado por el usuario*/
/*					foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
						$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
						$marcado = true;
					}

					# Verifica y asigna variables en caso de que exista marcado.
					if ($marcado == true) {
						$this->resultado.= "Datos Mantenimiento Programado \n \n";
						$this->resultado.="Usuario ; Titulo ; Fecha Inicio ; Fecha Termino; Objetivos \n";
						
						$dataMant = $event->getData(substr($ids, 1), $timeZone);
						$character = array("{", "}");
						$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
						
						foreach ($dataMant as $row) {
							$arr = $objetive->getObjetiveName($row['objetivo_id']);
							$nameObjetives = implode(" ", $arr[0]);
							$this->resultado.= $row['nombre'].";".$row['titulo'].";".$row['fecha_inicio'].";".$row['fecha_termino'].";".$nameObjetives.";";
							$this->resultado.="\n";
						}
					}
					$this->resultado.="\n\n";
				}
*/
				/* SI SON LOS DATOS */
/*				else {
					if($primero ==True){
						$array_titulo =(explode(",", $row["filas"]));
						$primero =False;
						$cuenta_estados= substr_count($row["filas"], "estado");
						$cuenta_tiempo = substr_count($row["filas"], "respuesta");
						$array_titulo = (array_slice($array_titulo, 3, -($cuenta_tiempo)));
						$this->resultado.=str_replace(array("{","}",","), array("","",$separador), preg_replace('(,-\d*)', ',-1000', $row["filas"]))."\n";
					}else{
						//echo count(split(",",$row["filas"]));
						//if($cuenta_estados==$cuenta_tiempo){
							$this->resultado.=str_replace(array("{","}",","), array("","",$separador), preg_replace('(,-\d*)', ',-1000', $row["filas"]))."\n";
						/*
						}else{
							$array_filas = explode(",",$row["filas"]);
							$estados =(array_slice($array_filas, 3, -($cuenta_tiempo)));
							$primer_patron = True;
							$patron = '';
							$patron2 = '';
							$pos_insert = 0;
							$cont_patrones = 0;
							foreach ($array_titulo as $key => $patrones) {
								if($primer_patron==True){
									$primer_patron=False;
									$patron = substr($patrones, 1,-13);
								}else{
									$patron2 = substr($patrones, 1,-13);
									//el 3 son las constantes nodo, fecha, hora
									if($patron==$patron2){
										$cont_patrones ++;
										$pos_insert = $key+3;
									}
								}
								$patron = $patron2;
							}
							for ($i=0; $i < $cont_patrones; $i++) {
								array_splice($array_filas, $pos_insert-($cont_patrones-1), 0, '');
							}
							$row["filas"]=implode(",", ($array_filas));
							$this->resultado.=str_replace(array("{","}",","), array("","",$separador), preg_replace('(,-\d*)', ',-1000', $row["filas"]))."\n";
						}*/
/*					}
				}
			}
			return;
		}
*/

		$this->resultado = $T->parse('out', 'tpl_tabla');
		if (count($dataMant)>0){
			$this->resultado.= $graficoSvg->getAccordion($data,$nameFunction);
		}
	}

	function getEstadosExitosos(){
        global $mdb2;
		global $log;
		global $usr;
		global $current_usuario_id;

		$estilo_medio_oscuro = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'ebebeb')));
		$estilo_claro_oscuro = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'fffff')));
		$style_align         = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$style_title_sheet = array('font' => array('color' => array('rgb' => '000000'), 'size' => '10','name'  => 'Calibri', 'bold' => true));
		$style_border_all = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'))));
		
		$objPHPExcel = new PHPExcel();
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$objetivo = new ConfigEspecial($this->objetivo_id);
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId);
		$timeZone = $arrTime[$timeZoneId];

		$fechaInicioTz = $this->timestamp->getInicioPeriodo();
		$fechaTerminoTz = $this->timestamp->getTerminoPeriodo();

		$nombre_objetivo = $objetivo->nombre;

		$objWorksheet->getStyle('4')->applyFromArray($style_align);
		$objWorksheet->getStyle('A1:ZZ29')->applyFromArray($style_align);
		$objWorksheet->mergeCells('A1:C1');
		$objWorksheet->mergeCells('E1:F1');
		$objWorksheet->mergeCells('H1:I1');
		if (strlen($nombre_objetivo) > 30) {
			$nombre_objetivo = substr($nombre_objetivo, 0, 28);
		}
		$objWorksheet->setCellValue('A1', $nombre_objetivo);
		$objWorksheet->setCellValue('D1', 'Intervalo:');
		$objWorksheet->setCellValue('E1', $objetivo->intervalo_nombre);
		$objWorksheet->setCellValue('G1', 'Fecha:');
		$objWorksheet->setCellValue('H1', date('Y-m-d', strtotime($fechaInicioTz)));
		$objWorksheet->setCellValue('A4', "HORAS");
		$objWorksheet->setCellValue('A29', "TOTAL");
		$objWorksheet->getStyle('E1:F1')->applyFromArray($style_align);
		$objWorksheet->getStyle('H1:I1')->applyFromArray($style_align);
		$objWorksheet->getStyle('A29')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('A29')->applyFromArray($style_border_all);
		$objWorksheet->getStyle('29')->applyFromArray($style_align);
		$objWorksheet->getStyle('A1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('A4')->applyFromArray($style_align);
		$objWorksheet->getStyle('A4')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('A4')->applyFromArray($style_border_all);
		$objWorksheet->getStyle('E1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('H1')->applyFromArray($style_title_sheet);

		$nombre_objetivo = str_replace("[", " ", $nombre_objetivo);
		$nombre_objetivo = str_replace("]", " ", $nombre_objetivo);
		$nombre_objetivo = str_replace("*", " ", $nombre_objetivo);
		$nombre_objetivo = str_replace("?", " ", $nombre_objetivo);
		$nombre_objetivo = str_replace(":", " ", $nombre_objetivo);
		$nombre_objetivo = str_replace("/", " ", $nombre_objetivo);
		$nombre_objetivo = str_replace("\"", " ", $nombre_objetivo);
		
		if (strlen($nombre_objetivo) > 20) {
			$nombre_objetivo = substr($nombre_objetivo, 0, 20);
		}

		$sqlPasosVisibles = "SELECT public.buscar_pasos(".$objetivo->objetivo_id.")";
		$resPasosVisibles =& $mdb2->query($sqlPasosVisibles);
		if (MDB2::isError($resPasosVisibles)) {
			$log->setError($sqlPasosVisibles, $resPasosVisibles->userinfo);
			exit();
		}

		$rowPasosVisibles = $resPasosVisibles->fetchRow();
		$pasosVisibles = split('{', $rowPasosVisibles['buscar_pasos']);
		$pasosVisibles = str_replace("}", "", $pasosVisibles[1]);
		$pasosVisibles = explode(',', $pasosVisibles);
		$objWorksheet->setTitle($nombre_objetivo);

		$sqlMonitores = 'SELECT * FROM objetivo_config WHERE objetivo_id = '.$objetivo->objetivo_id.' AND es_ultima_config = true';
		$resMonitores =& $mdb2->query($sqlMonitores);
		$rowMonitores = $resMonitores->fetchRow();
		$monitoresId = str_replace("{", "", $rowMonitores['monitor_id']);
		$monitoresId = str_replace("}", "", $monitoresId);
		$array_monitores = explode(",",$monitoresId);

		$arrayAlfabeto = array(1 => "A", 2 => "B", 3 => "C", 4 => "D", 5 => "E", 6 => "F", 7 => "G", 8 => "H", 9 => "I", 10 => "J", 11 => "K", 12 => "L", 13 => "M", 14 => "N", 15 => "O", 16 => "P", 17 => "Q", 18 => "R", 19 => "S", 20 => "T", 21 => "U", 22 => "V", 23 => "W", 24 => "X", 25 => "Y", 26 => "Z");
		
		$contSuma = 0;
		foreach ($array_monitores as $key => $monitor_id) {		
			$sqlNodo = "SELECT m.monitor_id, n.nombre, n.nodo_id
			FROM monitor m, nodo n 
			WHERE m.nodo_id=n.nodo_id 
			AND m.monitor_id =".$monitor_id;

			$resNodo =& $mdb2->query($sqlNodo);
			if (MDB2::isError($resNodo)) {
				$log->setError($sqlNodo, $resNodo->userinfo);
				exit();
			}
			$rowNodo = $resNodo->fetchRow();

			$sumaEstadosExitososTotales = 0;
			$sumaEstadosNoExitososTotales = 0;
			$contHora = 5;
			for ($i=0; $i <= 23; $i++) {
				if ($i < 10) {
					$horaCeldas = ('0'.$i);
				}else{
					$horaCeldas = $i;
				}
				$objWorksheet->setCellValue('A'.($i+5), $horaCeldas.':00');
				$objWorksheet->getStyle('A'.($i+5))->applyFromArray($style_border_all);
				$objWorksheet->getStyle('A'.($i+5))->applyFromArray($style_title_sheet);
				$objWorksheet->getStyle('A'.($i+5))->applyFromArray($style_align);
				$nuevafecha = strtotime( '+'.$i.' hour' , strtotime ( $fechaInicioTz ) ) ;
				$fechaDate = date( 'Y-m-d H:i:s' , $nuevafecha );
				//FECHA UTC
				$fechaInicioUtcEnCero = Utiles::convertDateUtc(date("Y-m-d H:i:s", strtotime($fechaDate)), $timeZone);
				$fechaTerminoUtcFinal = date('Y-m-d H:i:s', strtotime ( '+59 minute' , strtotime ( $fechaInicioUtcEnCero ) ) );
				$fechaTerminoUtcFinal2 =  date('Y-m-d H:i:s', strtotime ( '+59 second' , strtotime ( $fechaTerminoUtcFinal ) )) ;

				//FECHAS TIMEZONE
				$fechaInicioTzEnCero = date("Y-m-d H:i:s", strtotime($fechaDate));
				$fechaTerminoTzFinal = date('Y-m-d H:i:s', strtotime ( '+59 minute' , strtotime ( $fechaInicioTzEnCero ) ) );
				$fechaTerminoTzFinal2 =  date('Y-m-d H:i:s', strtotime ( '+59 second' , strtotime ( $fechaTerminoTzFinal ) )) ;

				$fechaInicioUTC = Utiles::convertDateUtc(date("Y-m-d H:i:s", strtotime($fechaInicioTz)), $timeZone);
				$fechaTerminoUTC = Utiles::convertDateUtc(date("Y-m-d H:i:s", strtotime($fechaTerminoTz)), $timeZone);
				//_periodos_marcados_v2 SE LE ENTREGAN FECHAS CON TIMEZONE Y ENTREGA FECHAS CON TIMEZONE
				$sqlMarc = "SELECT  * FROM _periodos_marcados_v2(".$current_usuario_id.", ".$this->objetivo_id.", ".$rowNodo['nodo_id'].", '".$fechaInicioTzEnCero."', '".$fechaTerminoTzFinal2."') WHERE marcado is true";

				$resMarc =& $mdb2->query($sqlMarc);
				if (MDB2::isError($resMarc)) {
					$log->setError($sqlMarc, $resMarc->userinfo);
					exit();
				}

				$fechaMarcado = '';
				while ($rowMarc = $resMarc->fetchRow()) {
					$fechaTerminoUtcInicio = Utiles::convertDateUtc(date("Y-m-d H:i:s", strtotime($rowMarc['inicio'])), $timeZone);
					$fechaTerminoUtcTermino = Utiles::convertDateUtc(date("Y-m-d H:i:s", strtotime($rowMarc['termino'])), $timeZone);
					//SE AGREGA "NOT BETWEEN" SI HUBIESE MARCADO PARA DISCRIMINAR MONITOREOS DENTRO DE MANTENIMIENTO PROGRAMADO
					$fechaMarcado = $fechaMarcado. " AND fecha NOT BETWEEN '".$fechaTerminoUtcInicio."'  AND  '".$fechaTerminoUtcTermino."'";
				}

				$sql = "SELECT objetivo_id, monitor_id, fecha, estado FROM resultado.resultado 
				WHERE objetivo_id =  ".$objetivo->objetivo_id."
				AND monitor_id = ".$monitor_id."
				AND xml_resultado IS NOT NULL 
				AND fecha BETWEEN '".$fechaInicioUtcEnCero."'  AND  '".$fechaTerminoUtcFinal2."'";
				$sql = $sql.$fechaMarcado;
				$sql = $sql. " ORDER BY monitor_id ASC";
				
				$res =& $mdb2->query($sql);
				if (MDB2::isError($res)) {
					$log->setError($sql, $res->userinfo);
					exit();
				}

				$sumaExitosos = 0;
				$sumaNoExitosos = 0;
				while($row = $res->fetchRow()) {
					$estados = explode("|", $row['estado']);          
					$sumaEstadosExitosos = 0;
					$sumaEstadosNoExitosos = 0;
					$contadorEstados = 0;
					$contadorPasosVisibles = 0;

					foreach ($estados as $estado) {
						$patrones = explode(",", $estado);
						$estadoPatron = 0; 
						$estado = 0;

						foreach ($patrones as $patron) {
							if ($patron > 0) {
								$estadoPatron = 1; 
							}
						}

						if ($estadoPatron == 1) {
							$estado = '27';
						}else{
							$estado = '0';
						}
						
						if ($pasosVisibles[$contadorEstados] == 1) {
							if ($estado == 0) {
								$sumaEstadosExitosos = 1 + $sumaEstadosExitosos;
							}else{
								$sumaEstadosNoExitosos = 1 + $sumaEstadosNoExitosos;
							}
							$contadorPasosVisibles++;
						}
						$contadorEstados++;
					}

					if ($sumaEstadosExitosos == $contadorPasosVisibles) {
						$sumaExitosos = 1 + $sumaExitosos;
					}else{
						$sumaNoExitosos = 1 + $sumaNoExitosos;
					}
				}

				$objWorksheet->mergeCells($arrayAlfabeto[($contadorAlfabeto+2)].'3:'.$arrayAlfabeto[($contadorAlfabeto+3)].'3');
				$objWorksheet->setCellValue($arrayAlfabeto[($contadorAlfabeto+2)].'3', $rowNodo['nombre']);
				$objWorksheet->getStyle($arrayAlfabeto[($contadorAlfabeto+2)].'3')->applyFromArray($style_title_sheet);
				$objWorksheet->getStyle($arrayAlfabeto[($contadorAlfabeto+2)].'3')->applyFromArray($style_align);
				$objWorksheet->getStyle($arrayAlfabeto[($contadorAlfabeto+2)].'3:'.$arrayAlfabeto[($contadorAlfabeto+3)].'3')->applyFromArray($style_border_all);
				$objWorksheet->setCellValue($arrayAlfabeto[($contadorAlfabeto+2)].'4', 'Exitosos');
				$objWorksheet->setCellValue($arrayAlfabeto[($contadorAlfabeto+3)].'4', 'No Exitosos');
				$objWorksheet->getStyle($arrayAlfabeto[($contadorAlfabeto+2)].'4')->applyFromArray($style_border_all);
				$objWorksheet->getStyle($arrayAlfabeto[($contadorAlfabeto+3)].'4')->applyFromArray($style_border_all);
				$objWorksheet->getStyle($arrayAlfabeto[($contadorAlfabeto+2)].'4')->applyFromArray($style_title_sheet);
				$objWorksheet->getStyle($arrayAlfabeto[($contadorAlfabeto+3)].'4')->applyFromArray($style_title_sheet);


				$array_suma_horas[$i][$contSuma] = $sumaExitosos;
				$array_suma_horas[$i][$contSuma+1] = $sumaNoExitosos;

				$objWorksheet->setCellValue($arrayAlfabeto[($contadorAlfabeto+2)].$contHora, $sumaExitosos);
				$objWorksheet->setCellValue($arrayAlfabeto[($contadorAlfabeto+3)].$contHora, $sumaNoExitosos);

				$objWorksheet->getStyle($arrayAlfabeto[($contadorAlfabeto+2)].$contHora)->applyFromArray($style_border_all);
				$objWorksheet->getStyle($arrayAlfabeto[($contadorAlfabeto+3)].$contHora)->applyFromArray($style_border_all);

				$sumaEstadosExitososTotales = $sumaExitosos  + $sumaEstadosExitososTotales;
				$sumaEstadosNoExitososTotales = $sumaNoExitosos  + $sumaEstadosNoExitososTotales;

				$objWorksheet->getColumnDimension($arrayAlfabeto[($i+1)])->setWidth(13);
				$contHora++;
			}

			$objWorksheet->setCellValue($arrayAlfabeto[($contadorAlfabeto+2)].'29', $sumaEstadosExitososTotales);
			$objWorksheet->setCellValue($arrayAlfabeto[($contadorAlfabeto+3)].'29', $sumaEstadosNoExitososTotales);
			$objWorksheet->getStyle($arrayAlfabeto[($contadorAlfabeto+2)].'29')->applyFromArray($style_border_all);
			$objWorksheet->getStyle($arrayAlfabeto[($contadorAlfabeto+3)].'29')->applyFromArray($style_border_all);
			$objWorksheet->getStyle($arrayAlfabeto[($contadorAlfabeto+2)].'29')->applyFromArray($style_title_sheet);
			$objWorksheet->getStyle($arrayAlfabeto[($contadorAlfabeto+3)].'29')->applyFromArray($style_title_sheet);
			$contSuma = $contSuma+2;
			$contadorAlfabeto = $contadorAlfabeto + 2;
		}

		$objWorksheet->mergeCells('B2:'.$arrayAlfabeto[(($contadorAlfabeto-2)+3)].'2');
		$objWorksheet->setCellValue('B2', 'ISP');
		$objWorksheet->getStyle('B2:'.$arrayAlfabeto[(($contadorAlfabeto-2)+3)].'2')->applyFromArray($style_align);
		$objWorksheet->getStyle('B2:'.$arrayAlfabeto[(($contadorAlfabeto-2)+3)].'2')->applyFromArray($style_border_all);
		$objWorksheet->getStyle('B2:'.$arrayAlfabeto[(($contadorAlfabeto-2)+3)].'2')->applyFromArray($style_title_sheet);
		
		for ($i=5; $i <= 28; $i++) { 
			$objWorksheet->getStyle('A'.$i.':'.$arrayAlfabeto[(($contadorAlfabeto-2)+3)].$i)->applyFromArray(($i % 2 == 0)?$estilo_medio_oscuro:$estilo_claro_oscuro);
		}

		$objWorksheet->getStyle('A1:I1')->applyFromArray($style_border_all);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		
	}

	function getDatosDisponibilidadEspecial() {
		global $mdb2;
		global $log;
		global $usr;
		global $current_usuario_id;

		$estilo_oscuro       = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'a2a2a2')));
		$estilo_medio_oscuro = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'ebebeb')));
		$estilo_claro_oscuro = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'e2e2e2')));
		$style_align         = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$style_title_sheet = array('font' => array('color' => array('rgb' => 'FFFFFF'), 'size' => '12','name'  => 'Calibri'));
		$style_border = array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM, 'color' => array('rgb' => 'FFFFFF'))));
		$style_border_all = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM, 'color' => array('rgb' => 'FFFFFF'))));

		$objetivo = new ConfigEspecial($this->objetivo_id);
		$objPHPExcel = new PHPExcel();
		$count_sheet = 0;
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			$event = new Event;
			$timeZoneId = $usr->zona_horaria_id;
			$arrTime = Utiles::getNameZoneHor($timeZoneId); 		
			$timeZone = $arrTime[$timeZoneId];
			$marcado = false;
			$dataMant = null;

			$objetivo_id_usuario = $subobjetivo->objetivo_id;
			$nombre_objetivo = $subobjetivo->nombre;
			$objWorksheet = $objPHPExcel->createSheet($count_sheet);
			$objWorksheet->getColumnDimension('A')->setWidth(15);
			$objWorksheet->getColumnDimension('B')->setWidth(15);
			$objWorksheet->getColumnDimension('C')->setWidth(15);
			$objWorksheet->getColumnDimension('D')->setWidth(15);
			$objWorksheet->getColumnDimension('E')->setWidth(30);
			$objWorksheet->getColumnDimension('G')->setWidth(25);
			$objWorksheet->getColumnDimension('H')->setWidth(25);
			$objWorksheet->getColumnDimension('I')->setWidth(35);
			$objWorksheet->getColumnDimension('J')->setWidth(20);
			$objWorksheet->getDefaultStyle()->applyFromArray($style_align);
			$objWorksheet->getStyle('A1')->applyFromArray($style_border_all);
			$objWorksheet->getStyle('B1')->applyFromArray($style_border_all);
			$objWorksheet->getStyle('C1')->applyFromArray($style_border_all);
			$objWorksheet->getStyle('D1')->applyFromArray($style_border_all);
			$objWorksheet->getStyle('E1')->applyFromArray($style_border_all);
			$objWorksheet->getStyle('A1:E1')->applyFromArray($style_title_sheet);
			$objWorksheet->getStyle('G1:J2')->applyFromArray($style_title_sheet);
			$objWorksheet->getStyle('A1:E1')->applyFromArray($estilo_oscuro);
			$objWorksheet->setCellValue('A1', "Fecha");
			$objWorksheet->setCellValue('B1', "Hora Inicio");
			$objWorksheet->setCellValue('C1', "Hora Término");
			$objWorksheet->setCellValue('D1', "Duración");
			$objWorksheet->setCellValue('E1', "Tipo");
			$nombre_objetivo = str_replace("[", " ", $nombre_objetivo);
			$nombre_objetivo = str_replace("]", " ", $nombre_objetivo);
			$nombre_objetivo = str_replace("*", " ", $nombre_objetivo);
			$nombre_objetivo = str_replace("?", " ", $nombre_objetivo);
			$nombre_objetivo = str_replace(":", " ", $nombre_objetivo);
			$nombre_objetivo = str_replace("/", " ", $nombre_objetivo);
			$nombre_objetivo = str_replace("\"", " ", $nombre_objetivo);
			if (strlen($nombre_objetivo) > 20) {
				$nombre_objetivo = substr($nombre_objetivo, 0, 20);
			}
			$objWorksheet->setTitle($nombre_objetivo);

			$sql = "SELECT * FROM reporte.disponibilidad_downtime_global(".
			pg_escape_string($current_usuario_id).",".
			pg_escape_string($objetivo_id_usuario).", ".
			pg_escape_string($this->horario_id).", '".
			pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
			pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

			$res =& $mdb2->query($sql);

			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_downtime_global']);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_downtime_global"]);
			}

			$ids = null;
			foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
				$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
				$marcado = true;
			}

			if ($marcado == true) {
				$dataMant =$event->getData(substr($ids, 1), $timeZone);
			}

			if (!is_null($dataMant)){
				$count_marc = 3;
				foreach ($dataMant as $key => $value) {
					$objWorksheet->mergeCells('G1:J1');
					$objWorksheet->setCellValue('G1', "Datos Mantenimientos especiales");
					$objWorksheet->getStyle('G1:J1')->applyFromArray($estilo_oscuro);
					$objWorksheet->getStyle('G2:J2')->applyFromArray($estilo_oscuro);
					$objWorksheet->getStyle('G2:J2')->applyFromArray($style_border);
					$objWorksheet->getStyle('G1:J1')->applyFromArray($style_border);
					$datetimeI = date_create($value['fecha_inicio']);
					$datetimeT = date_create($value['fecha_termino']);
					$interval = date_diff($datetimeI, $datetimeT);
					$duracion =  $interval->format('%d días %h horas %i minutos %s Segundos');
					$objWorksheet->getStyle('G'.$count_marc.':J'.$count_marc)->applyFromArray(($count_marc % 2 == 0)?$estilo_medio_oscuro:$estilo_claro_oscuro);
					$objWorksheet->setCellValue('G2', "Inicio Periodo");
					$objWorksheet->setCellValue('H2', "Término Periodo");
					$objWorksheet->setCellValue('I2', "Duración");
					$objWorksheet->setCellValue('J2', "Tipo");
					$objWorksheet->setCellValue('G'.$count_marc, $value['fecha_inicio']);
					$objWorksheet->setCellValue('H'.$count_marc, $value['fecha_termino']);
					$objWorksheet->setCellValue('I'.$count_marc, $duracion);
					$objWorksheet->setCellValue('J'.$count_marc, 'Marcado Especial');	
					$count_marc++;	
				}
			}

			$fecha_inicio = strtotime($this->timestamp->fecha_inicio);
			$fecha_termino = strtotime($this->timestamp->fecha_termino);

			$row_datos = 2;
			while ($fecha_inicio < $fecha_termino) {
				$tag_datos = $xpath->query("//detalle[@objetivo_id]/datos/dato[contains(@inicio, '".date("Y-m-d", $fecha_inicio)."')]");

				if ($tag_datos->length == 0) {
					$objWorksheet->getStyle('A'.$row_datos.':E'.$row_datos)->applyFromArray(($row_datos % 2 == 0)?$estilo_medio_oscuro:$estilo_claro_oscuro);
					$objWorksheet->setCellValue('A'.$row_datos, date("d-m-Y", $fecha_inicio));
					$objWorksheet->mergeCells('B'.$row_datos.':E'.$row_datos);
					$objWorksheet->getStyle('A'.$row_datos.':E'.$row_datos)->applyFromArray($style_border);
					$objWorksheet->setCellValue('B'.$row_datos, "Sin Caidas Globales");
					$row_datos++;
				}else{
					foreach ($tag_datos as $tag_dato) {
						$hora_fin = date("H:i:s", strtotime($tag_dato->getAttribute('termino')));
						$conf_evento = $xpath->query("//eventos/evento[@evento_id=".$tag_dato->getAttribute('evento_id')."]")->item(0);
						$objWorksheet->getStyle('A'.$row_datos.':E'.$row_datos)->applyFromArray(($row_datos % 2 == 0)?$estilo_medio_oscuro:$estilo_claro_oscuro);
						$objWorksheet->setCellValue('A'.$row_datos, date("d-m-Y", strtotime($tag_dato->getAttribute('inicio'))));
						$objWorksheet->setCellValue('B'.$row_datos, date("H:i:s", strtotime($tag_dato->getAttribute('inicio'))));
						$objWorksheet->setCellValue('C'.$row_datos, ($hora_fin == "00:00:00")?"24:00:00":$hora_fin);
						$objWorksheet->setCellValue('D'.$row_datos, ($tag_dato->getAttribute('duracion') == "1 day")?"24:00:00":$tag_dato->getAttribute('duracion'));
						$objWorksheet->setCellValue('E'.$row_datos, ($tag_dato->getAttribute('evento_id') == 2)?'Downtime Consolidado':$conf_evento->getAttribute('nombre'));
						$objWorksheet->getStyle('A'.$row_datos.':E'.$row_datos)->applyFromArray($style_border_all);
						$row_datos++;
					}
				}	
				$fecha_inicio = $fecha_inicio + 86400;
			}
			$count_sheet++;
		}	
		$objPHPExcel->removeSheetByIndex($count_sheet);
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
	
	function getDescargaDatosIVR() {
			global $mdb2;
			global $log;
			global $current_usuario_id;
	
			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT * FROM reporte.datos(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).",'".
					pg_escape_string($this->timestamp->getInicioPeriodo())."','".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
			if ($this->extra["datos_separador"] == 1) {
				$separador = ",";
			}
			else {
				$separador = ";";
			}
			$this->resultado = "";
			
			$primero = true;
			while($row = $res->fetchRow()) {
	
				/* SI ES EL ENCABEZADO */
				if ($row["filas"]==NULL) {
					$dom = new DomDocument();
					$dom->preserveWhiteSpace = FALSE;
					$dom->loadXML($row['xml']);
					$xpath = new DOMXpath($dom);
					unset($row["xml"]);
						
					$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
	
					$this->resultado.="Datos Objetivo ".$conf_objetivo->getAttribute('nombre')."\n\n";
					$this->resultado.="Leyenda \n";
					$this->resultado.="   Servidor : servidor que realizo el monitoreo. \n";
					$this->resultado.="   Fecha : fecha cuando se realizo el monitoreo. \n";
					$this->resultado.="   Hora : hora cuando se realizo el monitoreo. \n";
					$this->resultado.="   Status : codigo de estado del monitoreo. \n";
					$this->resultado.="   Delay : tiempo de respuesta del monitoreo. \n\n";
					$this->resultado.="Codigos de Estados \n";
						
					$array_codigo= array();
					foreach ($xpath->query("//codigos/codigo") as $codigos) {
						$array_codigo[$codigos->getAttribute('codigo_id')] = $codigos->getAttribute('nombre');
					}
					ksort($array_codigo);
						
					foreach ($array_codigo as $id=> $codigo) {
						$this->resultado.="   ".$id." : ".$codigo."\n";
					}
						
					$this->resultado.="\n\n";
				}
	
				/* SI SON LOS DATOS */
				else {
					
					$tupla=str_replace(array("{","}",","), array("","",$separador), preg_replace('(,-\d*)', ',-1000', $row["filas"]));
					$this->resultado.=$tupla;
					if ($primero) {
						$this->resultado.=";Estado Final;Duración Llamada\n";
					}
					else {
					
						$tupla_arr = explode($separador, $tupla);
						$duracion = 0;
						$cnt_pasos = floor((count($tupla_arr) -3) / 2);
						$estadoOk = true;
						for($i=3; $i< ($cnt_pasos + 3); $i++) {
							if ($tupla_arr[$i] != 0) {
								$estadoOk = false;
							}
							if ($tupla_arr[$i + $cnt_pasos] != "-1000") {
								$duracion += $tupla_arr[$i + $cnt_pasos];
							}
						}
						$this->resultado.=$separador.(($estadoOk)?"OK":"ERROR");//$cabecera_siguiente["pasos"]+4;
						$this->resultado.=$separador.$duracion."\n";
					
					}
					$primero = false;
				}
			}
			
			print $this->resultado;

	}
        
    function getDatosSegregadosIvr() {
        global $mdb2;
        global $log;
        global $current_usuario_id;
        
//        $current_usuario_id = Utiles::busca_usuario($this->objetivo_id);
        
        // CODIGOS IVR
        $codigos_ivr = array(600,601,603,604,607,610,613);
        $codigos_ivr_segregado = array(700,701,703,704,707,710,713);
        
        
        // OBTIENE ZONA HORARIA CLIENTE
/*        $sql = "SELECT * FROM public._cliente_tz(".$current_usuario_id.")";
        $res = & $mdb2->query($sql);
        if (MDB2::isError($res)) {
            $log->setError($sql, $res->userinfo);
            exit();
        }
        
        if ($row = $res->fetchRow()) {
            $tz = $row["_cliente_tz"];
        }
        else {
            $tz = "America/Santiago";
        }*/
        
        $tz = "America/Bogota";
        
        // OBTIENE DATOS DEL OBJETIVO
        $sql = "SELECT * FROM reporte._propiedad_objetivo(".
                $current_usuario_id.", ".
                "ARRAY[".pg_escape_string($this->objetivo_id)."], '".
                pg_escape_string($this->timestamp->getInicioPeriodo())."':: TIMESTAMP WITHOUT TIME ZONE)";
        $res = & $mdb2->query($sql);
        if (MDB2::isError($res)) {
            $log->setError($sql, $res->userinfo);
            exit();
        }

        $row = $res->fetchRow();
        $dom = new DomDocument();
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($row['_propiedad_objetivo']);
        $xpath = new DOMXpath($dom);

        $paso_estado = "";
        $paso_rendimiento = "";
        
        $conf_objetivo = $xpath->query("//objetivo")->item(0);
        $conf_pasos = $xpath->query("paso", $conf_objetivo);
        foreach ($conf_pasos as $id => $conf_paso) {
            $paso_estado.= ";".$conf_paso->getAttribute('nombre')." - estado ".($id + 1);
            $paso_rendimiento.= ";".$conf_paso->getAttribute('nombre')." - respuesta ".($id + 1);
        }

        // OBTIENE DATOS DEL NODO
        $sql = "SELECT m.monitor_id, n.nombre FROM monitor m, nodo n WHERE m.nodo_id=n.nodo_id ";
        $res = & $mdb2->query($sql);
        if (MDB2::isError($res)) {
            $log->setError($sql, $res->userinfo);
            exit();
        }
        
        $nombre_monitores = array();
        while ($row = $res->fetchRow()) {
            $nombre_monitores[$row['monitor_id']] = $row['nombre'];
        }

        
        // OBTIENE LAS FECHA QUE SE REINTENTO
        $sql = "SELECT monitor_id, (xpath('//ativr/@fecha_monitoreo', xml_resultado)::TEXT[])[1] AS fecha_monitoreo ".
                "FROM resultado.resultado ".
                "WHERE objetivo_id=".pg_escape_string($this->objetivo_id)." AND ".
                "fecha BETWEEN ('".pg_escape_string($this->timestamp->getInicioPeriodo())."'::TIMESTAMP AT TIME ZONE '".$tz."') AND ('".
                pg_escape_string($this->timestamp->getTerminoPeriodo())."'::TIMESTAMP AT TIME ZONE '".$tz."') AND ".
                "public.es_estado_ok(estado) = true and (xpath('//ativr/@intento', xml_resultado)::TEXT[])[1] != '1'";
//        echo($sql);
        $res = & $mdb2->query($sql);
        if (MDB2::isError($res)) {
            $log->setError($sql, $res->userinfo);
            exit();
        }
        
        $arr_fechas = array();
        while ($row = $res->fetchRow()) {
            $arr_fechas[$row["monitor_id"]][] = $row["fecha_monitoreo"];
        }
        
        // OBTIENE TODAS LAS MEDICIONES
        $sql = "SELECT monitor_id, fecha AT TIME ZONE '".$tz."' AS fecha, estado, tiempo, ".
                "(xpath('//ativr/@fecha_monitoreo', xml_resultado)::TEXT[])[1] AS fecha_monitoreo, ".
                "(xpath('//ativr/@intento', xml_resultado)::TEXT[])[1], public.es_estado_ok(estado) ".
                "FROM resultado.resultado ".
                "WHERE objetivo_id=".pg_escape_string($this->objetivo_id)." AND ".
                "fecha BETWEEN ('".pg_escape_string($this->timestamp->getInicioPeriodo())."'::TIMESTAMP AT TIME ZONE '".$tz."') AND ('".
                pg_escape_string($this->timestamp->getTerminoPeriodo())."'::TIMESTAMP AT TIME ZONE '".$tz."')";
//        echo($sql);
        $res = & $mdb2->query($sql);
        if (MDB2::isError($res)) {
            $log->setError($sql, $res->userinfo);
            exit();
        }
        
        $this->resultado = "Datos Objetivo ".$conf_objetivo->getAttribute('nombre')."\n\n";
        $this->resultado.= <<<EOT
Leyenda
  Servidor : servidor que realizo el monitoreo.
  Fecha : fecha cuando se realizo el monitoreo.
  Hora : hora cuando se realizo el monitoreo.
  Status : codigo de estado del monitoreo.
  Delay : tiempo de respuesta del monitoreo.


Codigos de Estados
   0 : OK
   600 : Error desconocido IVR
   601 : Troncal congestionado
   603 : Troncal ocupado
   604 : Llamada no contestada
   607 : Timeout
   610 : LLamada colgada
   613 : Error de contenido
   700 : Error desconocido IVR Segregado
   701 : Troncal congestionado Segregado
   703 : Troncal ocupado Segregado
   704 : Llamada no contestada Segregado
   707 : Timeout Segregado
   710 : LLamada colgada Segregado
   713 : Error de contenido Segregado



EOT;

        $this->resultado.= "servidor;fecha;hora".$paso_estado.$paso_rendimiento."\n";
        while ($row = $res->fetchRow()) {

            // SI SE REINTENTO REEMPLAZA EL CODIGO
           if ($row["xpath"]>1) {
                $estado = str_replace($codigos_ivr, $codigos_ivr_segregado, $row["estado"]);
            }
            else {
                $estado = $row["estado"];
            }
            
            $tupla = $nombre_monitores[$row["monitor_id"]].";".
                    date("Y-m-d;H:i:s", strtotime($row["fecha"])).";".
                    str_replace("|",";",$estado).";".
                    str_replace("|",";",$row["tiempo"])."\n";
            $this->resultado.=$tupla;
        }

        print $this->resultado;
    }

    /*
		Creado por:
		Modificado por: Carlos Sepúlveda
		Fecha de creacion:-
		Fecha de ultima modificacion:16-06-2017
	*/
    function getDatosDisponibilidad() {
    	global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		#var_dump(get_defined_vars());
    	# Variables para eventos especiales marcados por el cliente codigo 9.
		$event = new Event;
		$graficoSvg = new GraficoSVG();
		$objetive = new Objetivo();
		$timeZoneId = $usr->zona_horaria_id;
		$arrTime = Utiles::getNameZoneHor($timeZoneId); 
		$timeZone = $arrTime[$timeZoneId];


		#date_default_timezone_set($timeZone);
		#$fechaTermino = date('Y-m-d H:i:s');
		
		$marcado = false;
		$dataMant = null;
		$data = null;
		$ids = null;

		$nameFunction = 'datosDisponibilidadPorDia';
		$tieneEvento = 'false';
		/*Obtener marcados por separado*/

		$sql = "SELECT * FROM reporte._detalle_marcado (".
		  		pg_escape_string($current_usuario_id).",ARRAY[".
		  		pg_escape_string($this->objetivo_id)."],'".
		  		pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
		  		pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		#print $sql;
		#exit();
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['_detalle_marcado']);
			$xpath = new DOMXpath($dom);
			unset($row["_detalle_marcado"]);
		}
		foreach ($xpath->query("/detalles_marcado/detalle/marcado") as $tag_marcado) {
			$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
			$marcado = true;
		}

		# Verifica y asigna variables en caso de que exista marcado.
		if ($marcado == true) {
			$dataMant = $event->getData(substr($ids, 1), $timeZone);
			$character = array("{", "}");
			$objetives = explode(',',str_replace($character,"",($dataMant[0]['objetivo_id'])));
			$tieneEvento = 'true';
			$data = json_encode($dataMant);
		}

		/* SI SE DESCARGO EL CSV */
		if ($this->tipo=="csv") {
			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT * FROM reporte.disponibilidad_por_dia(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					"0, '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_por_dia']);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_por_dia"]);
			}

			if ($this->extra["datos_separador"] == 1) {
				$separador = ",";
			}
			else {
				$separador = ";";
			}
			
			if ($this->extra["datos_decimal"] == 1) {
				$decimal = ",";
			}
			else {
				$decimal = ".";
			}

			/* Datos de mantenimiento marcado por el usuario*/
			foreach ($xpath->query("/atentus/resultados/detalles_marcado/detalle/marcado") as $tag_marcado) {
				$ids = $ids.','.$tag_marcado->getAttribute('mantenimiento_id');
				$marcado = true;
			}

			$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
			$conf_fechas = $xpath->query("//detalles/detalle[1]/detalles/detalle[@fecha]");
			
			$encabezado = "Disponibilidad ".$conf_objetivo->getAttribute('nombre')."\n\n";
			$encabezado.= "SLA Sitio Web"."\n";
			$encabezado.= "   SLA Ok".$separador."Superior a ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_ok'), 2, $decimal, '')."%"."\n";
			$encabezado.= "   SLA Normal".$separador."Entre ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_error'), 2, $decimal, '')."% y ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_ok'), 2, $decimal, '')."%"."\n";
			$encabezado.= "   SLA Error".$separador."Inferior a ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_error'), 2, $decimal, '')."%"."\n\n";
			 
			if ($conf_fechas->length > 0) {
				$encabezado.= "Los valores considerados corresponden a: "."\n";
				if ($this->extra["datos_uptime"]) {
					$encabezado.= "   ".$xpath->query("//eventos/evento[@evento_id=1]")->item(0)->getAttribute('nombre')."\n";
				}
				if ($this->extra["datos_downtime_parcial"]) {
					$encabezado.= "   ".$xpath->query("//eventos/evento[@evento_id=3]")->item(0)->getAttribute('nombre')."\n";
				}
				if ($this->extra["datos_downtime_global"]) {
					$encabezado.= "   ".$xpath->query("//eventos/evento[@evento_id=2]")->item(0)->getAttribute('nombre')."\n";
				}
				if ($this->extra["datos_nomonitoreo"]) {
					$encabezado.= "   ".$xpath->query("//eventos/evento[@evento_id=7]")->item(0)->getAttribute('nombre')."\n";
				}
				if ($this->extra["datos_eventoespecial"]) {
					$encabezado.= "   ".$xpath->query("//eventos/evento[@evento_id=9]")->item(0)->getAttribute('nombre')."\n";
				}
				
				$encabezado.= "\n";
				$encabezado.= "Promedio Por Paso"."\n";

				$this->resultado = "Promedio Por Fecha"."\n";
				$this->resultado.= "Pasos / Fechas";
				
				foreach ($conf_fechas as $conf_fecha) {
					$this->resultado.= $separador.$this->timestamp->getFormatearFecha($conf_fecha->getAttribute('fecha'), "d-m-Y");
				}
				$this->resultado.= "\n";
				
				$promedio_total = 0;
				foreach ($conf_pasos as $conf_paso) {
					$promedio = 0;
					$this->resultado.= "   ".$conf_paso->getAttribute('nombre');

					$tag_fechas = $xpath->query("//detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/detalles/detalle");
					foreach ($tag_fechas as $tag_fecha) {
						$porcentaje = 0;
						foreach ($xpath->query("estadisticas/estadistica", $tag_fecha) as $tag_dato) {

							if ($this->extra["datos_uptime"] and $tag_dato->getAttribute('evento_id') == 1) {
								$porcentaje += $tag_dato->getAttribute('porcentaje');
							}
							if ($this->extra["datos_downtime_parcial"] and $tag_dato->getAttribute('evento_id') == 3) {
								$porcentaje += $tag_dato->getAttribute('porcentaje');
							}
							if ($this->extra["datos_downtime_global"] and $tag_dato->getAttribute('evento_id') == 2) {
								$porcentaje += $tag_dato->getAttribute('porcentaje');
							}
							if ($this->extra["datos_nomonitoreo"] and $tag_dato->getAttribute('evento_id') == 7) {
								$porcentaje += $tag_dato->getAttribute('porcentaje');
							}
							if ($this->extra["datos_eventoespecial"] and $tag_dato->getAttribute('evento_id') == 9)
							{
								$porcentaje += $tag_dato->getAttribute('porcentaje');
							}
						}
						$this->resultado.= $separador.number_format($porcentaje, 2, $decimal, '')."%";
						$promedio += $porcentaje;
					}
					$this->resultado.= "\n";
					
					$count_fechas=$xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$conf_objetivo->getAttribute('objetivo_id')."]/detalles/detalle/detalles/detalle")->length;
					$promedio = $promedio / $conf_fechas->length;
					$promedio_total += $promedio;
					$encabezado.= "   ".$conf_paso->getAttribute('nombre').$separador.number_format($promedio, 2, $decimal, '')."%"."\n";
				}

				$promedio_total = $promedio_total / $conf_pasos->length;
				$encabezado.= "Promedio Global".$separador.number_format($promedio_total, 2, $decimal, '')."%"."\n";

			}
			$this->resultado = $encabezado."\n".$this->resultado;

			$this->resultado.="\n\n";
		
			# Verifica y asigna variables en caso de que exista marcado.
			if ($marcado == true) {
				$this->resultado.= "Datos Mantenimiento Programado \n \n";
				$this->resultado.="Usuario ; Titulo ; Fecha Inicio ; Fecha Termino; Objetivos \n";
				
				$dataMant = $event->getData(substr($ids, 1), $timeZone);
				$character = array("{", "}");
				$objetives = explode(' ',str_replace($character,"",($dataMant[0]['objetivo_id'])));
				
				foreach ($dataMant as $row) {
					$arr = $objetive->getObjetiveName($row['objetivo_id']);
					$nameObjetives = implode(" ", $arr[0]);
					$this->resultado.= $row['nombre'].";".$row['titulo'].";".$row['fecha_inicio'].";".$row['fecha_termino'].";".$nameObjetives.";";
					$this->resultado.="\n";
					#unset($nameObjetives);
				}
			}
			$this->resultado.="\n\n";
			return;
		}

		/* MUESTRA LA TABLA CON EL LINK PARA DESCARGAR */
		$T =& new Template_PHPLIB(REP_PATH_TABLETEMPLATES);
		$T->setFile('tpl_tabla', 'descarga_disponibilidad_por_dia.tpl');
		$T->setVar('__item_id', $this->__item_id);
		$T->setVar('__datos_imagen', REP_PATH_IMG.'datos_disponibilidad.png');
		
		# Datos para mantenimiento cliente.
		$T->setVar('__tiene_evento', $tieneEvento);
		$T->setVar('__name', $nameFunction);
		$this->resultado = $T->parse('out', 'tpl_tabla');
		if (count($dataMant)>0){
			$this->resultado.= $graficoSvg->getAccordion($data,$nameFunction);
		}
	}



	/*************** FUNCIONES DE PLANILLAS ESPECIALES ***************/
	/*************** FUNCIONES DE PLANILLAS ESPECIALES ***************/
	/*************** FUNCIONES DE PLANILLAS ESPECIALES ***************/

	function getEspecialUptimePorDiaFullObjetivos() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$objetivo = new ConfigEspecial($this->objetivo_id);

		$objPHPExcel = new PHPExcel();

		$sheetid =1;
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {

			$objWorksheet = $objPHPExcel->createSheet();
			$objWorksheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(8);
			$objWorksheet->setTitle(substr(str_replace(array("https://","http://","[","]","\\","/",":"), array("","","(",")"," "," ",""), $subobjetivo->nombre), 0, 30));
			$objWorksheet->getColumnDimension('A')->setWidth(20);

			$objPHPExcel->setActiveSheetIndex($sheetid);
			$sheetid++;

			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT * FROM reporte.disponibilidad_por_dia(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($subobjetivo->objetivo_id).", ".
					"0, '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row["disponibilidad_por_dia"]);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_por_dia"]);
			}

			$conf_objetivo = $xpath->query('//propiedades/objetivos/objetivo')->item(0);
			$conf_pasos = $xpath->query('paso[@visible=1]', $conf_objetivo);

			$conditionRed = new PHPExcel_Style_Conditional();
			$conditionRed->setConditionType(PHPExcel_Style_Conditional::CONDITION_CELLIS);
			$conditionRed->setOperatorType(PHPExcel_Style_Conditional::OPERATOR_LESSTHAN);
			$conditionRed->addCondition($conf_objetivo->getAttribute('sla_disponibilidad_error'));
			$conditionRed->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$conditionRed->getStyle()->getFill()->getEndColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
			$conditionRed->getStyle()->getNumberFormat()->setFormatCode('0.00');

			$conditionYellow = new PHPExcel_Style_Conditional();
			$conditionYellow->setConditionType(PHPExcel_Style_Conditional::CONDITION_CELLIS);
			$conditionYellow->setOperatorType(PHPExcel_Style_Conditional::OPERATOR_BETWEEN);
			$conditionYellow->addCondition($conf_objetivo->getAttribute('sla_disponibilidad_error'));
			$conditionYellow->addCondition($conf_objetivo->getAttribute('sla_disponibilidad_ok'));
			$conditionYellow->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$conditionYellow->getStyle()->getFill()->getEndColor()->setARGB(PHPExcel_Style_Color::COLOR_YELLOW);
			$conditionYellow->getStyle()->getNumberFormat()->setFormatCode('0.00');

			$conditionGreen = new PHPExcel_Style_Conditional();
			$conditionGreen->setConditionType(PHPExcel_Style_Conditional::CONDITION_CELLIS);
			$conditionGreen->setOperatorType(PHPExcel_Style_Conditional::OPERATOR_GREATERTHAN);
			$conditionGreen->addCondition($conf_objetivo->getAttribute('sla_disponibilidad_ok'));
			$conditionGreen->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$conditionGreen->getStyle()->getFill()->getEndColor()->setARGB(PHPExcel_Style_Color::COLOR_GREEN);
			$conditionGreen->getStyle()->getNumberFormat()->setFormatCode('0.00');

			$conditionalStyles = array($conditionRed, $conditionYellow, $conditionGreen);

			$objWorksheet->setCellValueByColumnAndRow(0, 1, "Disponibilidad ".$conf_objetivo->getAttribute('nombre'));
			$objWorksheet->setCellValueByColumnAndRow(0, 3, "SLA Sitio Web");
			$objWorksheet->setCellValueByColumnAndRow(0, 4, "SLA Ok");
			$objWorksheet->setCellValueByColumnAndRow(0, 5, "SLA Normal");
			$objWorksheet->setCellValueByColumnAndRow(0, 6, "SLA Error");

			$objWorksheet->setCellValueByColumnAndRow(1, 4, "Superior a ".number_format($datosobjetivo->sla_dis_ok, 2, '.', '')."%");
			$objWorksheet->setCellValueByColumnAndRow(1, 4, "Superior a ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_ok'), 2, '.', '')."%");
			$objWorksheet->setCellValueByColumnAndRow(1, 5, "Entre ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_error'), 2, '.', '')."% y ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_ok'), 2, '.', '')."%");
			$objWorksheet->setCellValueByColumnAndRow(1, 6, "Inferior a ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_error'), 2, '.', '')."%");

			if ($xpath->query('//estadisticas/estadistica')->length > 0) {

				$objWorksheet->setCellValueByColumnAndRow(0, 8, "Promedio Por Paso");
				$objWorksheet->setCellValueByColumnAndRow(0, $conf_pasos->length + 9, "Promedio");
				$objWorksheet->setCellValueByColumnAndRow(1, $conf_pasos->length + 9, '=AVERAGE(B9:B'.($conf_pasos->length + 8).')');
				$objWorksheet->setConditionalStyles("B".($conf_pasos->length + 9), $conditionalStyles);

				$objWorksheet->setCellValueByColumnAndRow(0, $conf_pasos->length + 11, "Promedio Por Fecha");
				$objWorksheet->setCellValueByColumnAndRow(0, $conf_pasos->length + 12, "Pasos / Fechas");

				$fpasoprom = 9;
				$fpaso = $conf_pasos->length + 13;

				foreach ($conf_pasos as $paso) {
					$tag_detalles = $xpath->query('//detalle[@paso_orden='.$paso->getAttribute('paso_orden').']/detalles/detalle');

					$cinicio = $objWorksheet->getCellByColumnAndRow(1, $fpaso)->getColumn();
					$cfin = $objWorksheet->getCellByColumnAndRow($tag_detalles->length, $fpaso)->getColumn();

					$objWorksheet->setConditionalStyles("B".$fpasoprom, $conditionalStyles);
					$objWorksheet->setConditionalStyles($cinicio.$fpaso.':'.$cfin.$fpaso, $conditionalStyles);

					$objWorksheet->setCellValueByColumnAndRow(0, $fpasoprom, $paso->getAttribute('nombre'));
					$objWorksheet->setCellValueByColumnAndRow(1, $fpasoprom, '=AVERAGE('.$cinicio.$fpaso.':'.$cfin.$fpaso.')');
					$objWorksheet->setCellValueByColumnAndRow(0, $fpaso, $paso->getAttribute('nombre'));

					$cpaso = 1;
					foreach ($tag_detalles as $tag_detalle) {

						$evento_1 = $xpath->query('estadisticas/estadistica[@evento_id=1]', $tag_detalle)->item(0);
						$evento_3 = $xpath->query('estadisticas/estadistica[@evento_id=3]', $tag_detalle)->item(0);
						$evento_7 = $xpath->query('estadisticas/estadistica[@evento_id=7]', $tag_detalle)->item(0);
						$porcentaje = (($evento_1)?$evento_1->getAttribute('porcentaje'):0) + 
					                  (($evento_3)?$evento_3->getAttribute('porcentaje'):0) + 
					                  (($evento_7)?$evento_7->getAttribute('porcentaje'):0);
						
						$objWorksheet->setCellValueByColumnAndRow($cpaso, $conf_pasos->length + 12,  $this->timestamp->getFormatearFecha($tag_detalle->getAttribute('fecha'), "d-m-Y"));
						$objWorksheet->setCellValueByColumnAndRow($cpaso, $fpaso, number_format($porcentaje, 2, '.', ''));
						$cpaso++;
					}

					$fpasoprom++;
					$fpaso++;
				}
			}
		}

		$objPHPExcel->removeSheetByIndex(0);
		$objPHPExcel->setActiveSheetIndex(0);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	function getEspecialUptimePorDia() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$estilo_titulo = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '000000')),
				'font' => array('size' => 8, 'bold' => true, 'color' => array('rgb' => 'FFFFFF')));
		$estilo_subtitulo = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '929292')),
				'font' => array('size' => 8, 'color' => array('rgb' => 'FFFFFF')));
		$estilo_parametro = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'f47001')),
				'font' => array('size' => 8, 'color' => array('rgb' => 'FFFFFF')));

		$objetivo = new ConfigEspecial($this->extra["parent_objetivo_id"]);
		$subobjetivo = $objetivo->getSubobjetivo($this->objetivo_id);
		$subobjetivo->__Objetivo();
		$horario = $objetivo->getHorario($this->horario_id);

		$horarios = array($horario);
		if ($this->horario_id != 0) {
			$horarios[($this->horario_id*-1)] = new Horario(($this->horario_id*-1));
			$horarios[($this->horario_id*-1)]->nombre = "Horario Inhabil";
		}

		$objPHPExcel = new PHPExcel();
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$objWorksheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(8);
		$objWorksheet->getColumnDimension('A')->setWidth(20);
		$objWorksheet->getColumnDimension('B')->setWidth(12);

		$objWorksheet->getStyle('A1')->getFont()->setBold(true);
		$objWorksheet->getStyle('A3:A5')->applyFromArray($estilo_parametro);
		$objWorksheet->setCellValueByColumnAndRow(0, 1, $objetivo->nombre);
		$objWorksheet->setCellValueByColumnAndRow(0, 3, "Objetivo");
		$objWorksheet->setCellValueByColumnAndRow(0, 4, "Horario");
		$objWorksheet->setCellValueByColumnAndRow(0, 5, "Fecha");
		$objWorksheet->setCellValueByColumnAndRow(1, 3, $subobjetivo->nombre);
		$objWorksheet->setCellValueByColumnAndRow(1, 4, $horario->nombre);
		$objWorksheet->setCellValueByColumnAndRow(1, 5, $this->timestamp->getInicioPeriodo("d-m-Y H:i:s")." hasta ".$this->timestamp->getTerminoPeriodo("d-m-Y H:i:s"));

		$objWorksheet->mergeCells('A8:C8');
		$objWorksheet->getStyle('A8:C8')->applyFromArray($estilo_titulo);
		$objWorksheet->getStyle('A9:A11')->applyFromArray($estilo_subtitulo);
		$objWorksheet->setCellValueByColumnAndRow(0, 8, "SLA Sitio Web");
		$objWorksheet->setCellValueByColumnAndRow(0, 9, "SLA Ok");
		$objWorksheet->setCellValueByColumnAndRow(0, 10, "SLA Normal");
		$objWorksheet->setCellValueByColumnAndRow(0, 11, "SLA Error");
		$objWorksheet->setCellValueByColumnAndRow(1, 9, "Superior a ".number_format($subobjetivo->sla_dis_ok, 2, '.', '')."%");
		$objWorksheet->setCellValueByColumnAndRow(1, 10, "Entre ".number_format($subobjetivo->sla_dis_error, 2, '.', '')."% y ".number_format($subobjetivo->sla_dis_ok, 2, '.', '')."%");
		$objWorksheet->setCellValueByColumnAndRow(1, 11, "Inferior a ".number_format($subobjetivo->sla_dis_error, 2, '.', '')."%");

		$fhorario = 14;
		foreach ($horarios as $horario) {

			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT * FROM reporte.especial_disponibilidad_pordia(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					pg_escape_string($horario->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			//			echo($sql);

			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["especial_disponibilidad_pordia"]);
			$xpath = new DOMXpath($dom);

			$fpaso = $fhorario + 2;
			$tag_pasos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=0]/detalles/detalle");
			$cfin = "B";

			if ($tag_pasos->length > 0) {
				foreach ($xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]") as $conf_paso) {
					$tag_paso = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]")->item(0);
					$tag_fechas = $xpath->query("detalles/detalle", $tag_paso);
					$cfin = $objWorksheet->getCellByColumnAndRow($tag_fechas->length + 1, $fpaso)->getColumn();

					$objWorksheet->getStyle("B".$fpaso.':'.$cfin.$fpaso)->getNumberFormat()->setFormatCode('0.00');
					$objWorksheet->getStyle("B".$fpaso)->getFont()->setBold(true);
					$objWorksheet->setCellValueByColumnAndRow(0, $fpaso, $conf_paso->getAttribute("nombre"));
					if($tag_fechas->length){
						$objWorksheet->setCellValueByColumnAndRow(1, $fpaso, '=AVERAGE(C'.$fpaso.':'.$cfin.$fpaso.')');
					}

					$cpaso = 2;
					foreach ($tag_fechas as $tag_fecha) {

						$porcentaje = 0;
						foreach ($xpath->query("estadisticas/estadistica", $tag_fecha) as $dato) {
							if (in_array($dato->getAttribute("evento_id"), array(1))) {
								$porcentaje += $dato->getAttribute("porcentaje");
							}
						}

						$letra = $objWorksheet->getCellByColumnAndRow($cpaso, $fpaso)->getColumn();
						$objWorksheet->getColumnDimension($letra)->setWidth(12);
						$objWorksheet->setCellValueByColumnAndRow($cpaso, $fhorario + 1,  $this->timestamp->getFormatearFecha($tag_fecha->getAttribute("fecha"), "d-m-Y"));
						$objWorksheet->setCellValueByColumnAndRow($cpaso, $fpaso, number_format($porcentaje, 2, '.', ''));
						$cpaso++;
					}
					$fpaso++;
				}
			}

			$objWorksheet->mergeCells('A'.$fhorario.':'.$cfin.$fhorario);
			$objWorksheet->getStyle('A'.$fhorario.':'.$cfin.$fhorario)->applyFromArray($estilo_titulo);
			$objWorksheet->getStyle('A'.($fhorario + 1).':'.$cfin.($fhorario + 1))->applyFromArray($estilo_subtitulo);
			$objWorksheet->getStyle('B'.($fhorario + 1).':'.$cfin.($fhorario + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objWorksheet->setCellValueByColumnAndRow(0, $fhorario, $horario->nombre);
			$objWorksheet->setCellValueByColumnAndRow(0, $fhorario + 1, "Pasos");
			$objWorksheet->setCellValueByColumnAndRow(1, $fhorario + 1, "Promedio");

			$fhorario = $fpaso + 2;
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	function getEspecialUptimeParcialPorDia() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$estilo_titulo = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '000000')),
				'font' => array('size' => 8, 'bold' => true, 'color' => array('rgb' => 'FFFFFF')));
		$estilo_subtitulo = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '929292')),
				'font' => array('size' => 8, 'color' => array('rgb' => 'FFFFFF')));
		$estilo_parametro = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'f47001')),
				'font' => array('size' => 8, 'color' => array('rgb' => 'FFFFFF')));

		$objetivo = new ConfigEspecial($this->extra["parent_objetivo_id"]);
		$subobjetivo = $objetivo->getSubobjetivo($this->objetivo_id);
		$subobjetivo->__Objetivo();
		$horario = $objetivo->getHorario($this->horario_id);

		$horarios = array($horario);
		if ($this->horario_id != 0) {
			$horarios["-1"] = new Horario("-1");
			$horarios["-1"]->nombre = "Horario Inhabil";
		}

		$objPHPExcel = new PHPExcel();
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$objWorksheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(8);
		$objWorksheet->getColumnDimension('A')->setWidth(20);
		$objWorksheet->getColumnDimension('B')->setWidth(12);

		$objWorksheet->getStyle('A1')->getFont()->setBold(true);
		$objWorksheet->getStyle('A3:A5')->applyFromArray($estilo_parametro);
		$objWorksheet->setCellValueByColumnAndRow(0, 1, $objetivo->nombre);
		$objWorksheet->setCellValueByColumnAndRow(0, 3, "Objetivo");
		$objWorksheet->setCellValueByColumnAndRow(0, 4, "Horario");
		$objWorksheet->setCellValueByColumnAndRow(0, 5, "Fecha");
		$objWorksheet->setCellValueByColumnAndRow(1, 3, $subobjetivo->nombre);
		$objWorksheet->setCellValueByColumnAndRow(1, 4, $horario->nombre);
		$objWorksheet->setCellValueByColumnAndRow(1, 5, $this->timestamp->getInicioPeriodo("d-m-Y H:i:s")." hasta ".$this->timestamp->getTerminoPeriodo("d-m-Y H:i:s"));

		$objWorksheet->mergeCells('A8:C8');
		$objWorksheet->getStyle('A8:C8')->applyFromArray($estilo_titulo);
		$objWorksheet->getStyle('A9:A11')->applyFromArray($estilo_subtitulo);
		$objWorksheet->setCellValueByColumnAndRow(0, 8, "SLA Sitio Web");
		$objWorksheet->setCellValueByColumnAndRow(0, 9, "SLA Ok");
		$objWorksheet->setCellValueByColumnAndRow(0, 10, "SLA Normal");
		$objWorksheet->setCellValueByColumnAndRow(0, 11, "SLA Error");
		$objWorksheet->setCellValueByColumnAndRow(1, 9, "Superior a ".number_format($subobjetivo->sla_dis_ok, 2, '.', '')."%");
		$objWorksheet->setCellValueByColumnAndRow(1, 10, "Entre ".number_format($subobjetivo->sla_dis_error, 2, '.', '')."% y ".number_format($subobjetivo->sla_dis_ok, 2, '.', '')."%");
		$objWorksheet->setCellValueByColumnAndRow(1, 11, "Inferior a ".number_format($subobjetivo->sla_dis_error, 2, '.', '')."%");

		/* OBTENER LOS DATOS Y PARSEARLO */
		$sql = "SELECT * FROM reporte.especial_disponibilidad_pordia(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($this->objetivo_id).", ".
				pg_escape_string($this->horario_id).", '".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
		//		echo($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}

		$row = $res->fetchRow();
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($row["especial_disponibilidad_pordia"]);
		$xpath = new DOMXpath($dom);


		$tag_nodos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle");
		$tag_fechas = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=0]/detalles/detalle");

		$fnodo = 15;
		foreach ($tag_nodos as $tag_nodo) {
			$conf_nodo = $xpath->query("/atentus/resultados/propiedades/nodos/nodo[@nodo_id=".$tag_nodo->getAttribute("nodo_id")."]")->item(0);

			$fpaso = $fnodo + 2;
			foreach ($xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]") as $conf_paso) {
				$tag_paso = $xpath->query("detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]", $tag_nodo)->item(0);

				$cfin = $objWorksheet->getCellByColumnAndRow($tag_fechas->length + 1, $fpaso)->getColumn();

				$objWorksheet->getStyle("B".$fpaso.':'.$cfin.$fpaso)->getNumberFormat()->setFormatCode('0.00');
				$objWorksheet->getStyle("B".$fpaso)->getFont()->setBold(true);
				$objWorksheet->setCellValueByColumnAndRow(0, $fpaso, $conf_paso->getAttribute("nombre"));
				if($tag_fechas->length){
					$objWorksheet->setCellValueByColumnAndRow(1, $fpaso, '=AVERAGE(C'.$fpaso.':'.$cfin.$fpaso.')');
				}

				$cpaso = 2;
				foreach ($tag_fechas as $tag_fecha) {

					$porcentaje = 0;
					foreach ($xpath->query("detalles/detalle[@fecha='".$tag_fecha->getAttribute("fecha")."']/estadisticas/estadistica", $tag_paso) as $dato) {
						if (in_array($dato->getAttribute("evento_id"), array(1))) {
							$porcentaje += $dato->getAttribute("porcentaje");
						}
					}

					$letra = $objWorksheet->getCellByColumnAndRow($cpaso, $fpaso)->getColumn();
					$objWorksheet->getColumnDimension($letra)->setWidth(12);
					$objWorksheet->setCellValueByColumnAndRow($cpaso, $fnodo + 1,  $this->timestamp->getFormatearFecha($tag_fecha->getAttribute("fecha"), "d-m-Y"));
					$objWorksheet->setCellValueByColumnAndRow($cpaso, $fpaso, number_format($porcentaje, 2, '.', ''));
					$cpaso++;
				}
				$fpaso++;
			}

			$objWorksheet->mergeCells('A'.$fnodo.':'.$cfin.$fnodo);
			$objWorksheet->getStyle('A'.$fnodo.':'.$cfin.$fnodo)->applyFromArray($estilo_titulo);
			$objWorksheet->getStyle('A'.($fnodo + 1).':'.$cfin.($fnodo + 1))->applyFromArray($estilo_subtitulo);
			$objWorksheet->getStyle('B'.($fnodo + 1).':'.$cfin.($fnodo + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objWorksheet->setCellValueByColumnAndRow(0, $fnodo, $conf_nodo->getAttribute("nombre"));
			$objWorksheet->setCellValueByColumnAndRow(0, $fnodo + 1, "Pasos");
			$objWorksheet->setCellValueByColumnAndRow(1, $fnodo + 1, "Promedio");

			$fnodo = $fpaso + 2;
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	function getEspecialRendimientoPorDia() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$estilo_titulo = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '000000')),
				'font' => array('size' => 8, 'bold' => true, 'color' => array('rgb' => 'FFFFFF')));
		$estilo_subtitulo = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '929292')),
				'font' => array('size' => 8, 'color' => array('rgb' => 'FFFFFF')));
		$estilo_parametro = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'f47001')),
				'font' => array('size' => 8, 'color' => array('rgb' => 'FFFFFF')));

		$objetivo = new ConfigEspecial($this->extra["parent_objetivo_id"]);
		$subobjetivo = $objetivo->getSubobjetivo($this->objetivo_id);
		$subobjetivo->__Objetivo();
		$horario = $objetivo->getHorario($this->horario_id);

		$horarios = array($horario);
		if ($this->horario_id != 0) {
			$horarios["-".$this->horario_id] = new Horario("-".$this->horario_id);
			$horarios["-".$this->horario_id]->nombre = "Horario Inhabil";
		}

		$objPHPExcel = new PHPExcel();
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$objWorksheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(8);
		$objWorksheet->getColumnDimension('A')->setWidth(20);
		$objWorksheet->getColumnDimension('B')->setWidth(12);

		$objWorksheet->getStyle('A1')->getFont()->setBold(true);
		$objWorksheet->getStyle('A3:A5')->applyFromArray($estilo_parametro);
		$objWorksheet->setCellValueByColumnAndRow(0, 1, $objetivo->nombre);
		$objWorksheet->setCellValueByColumnAndRow(0, 3, "Objetivo");
		$objWorksheet->setCellValueByColumnAndRow(0, 4, "Horario");
		$objWorksheet->setCellValueByColumnAndRow(0, 5, "Fecha");
		$objWorksheet->setCellValueByColumnAndRow(1, 3, $subobjetivo->nombre);
		$objWorksheet->setCellValueByColumnAndRow(1, 4, $horario->nombre);
		$objWorksheet->setCellValueByColumnAndRow(1, 5, $this->timestamp->getInicioPeriodo("d-m-Y H:i:s")." hasta ".$this->timestamp->getTerminoPeriodo("d-m-Y H:i:s"));

		$objWorksheet->mergeCells('A8:C8');
		$objWorksheet->getStyle('A8:C8')->applyFromArray($estilo_titulo);
		$objWorksheet->getStyle('A9:A11')->applyFromArray($estilo_subtitulo);
		$objWorksheet->setCellValueByColumnAndRow(0, 8, "SLA Sitio Web");
		$objWorksheet->setCellValueByColumnAndRow(0, 9, "SLA Ok");
		$objWorksheet->setCellValueByColumnAndRow(0, 10, "SLA Normal");
		$objWorksheet->setCellValueByColumnAndRow(0, 11, "SLA Error");
		$objWorksheet->setCellValueByColumnAndRow(1, 9, "Superior a ".number_format($subobjetivo->sla_dis_ok, 2, '.', '')."%");
		$objWorksheet->setCellValueByColumnAndRow(1, 10, "Entre ".number_format($subobjetivo->sla_dis_error, 2, '.', '')."% y ".number_format($subobjetivo->sla_dis_ok, 2, '.', '')."%");
		$objWorksheet->setCellValueByColumnAndRow(1, 11, "Inferior a ".number_format($subobjetivo->sla_dis_error, 2, '.', '')."%");

		$fhorario = 14;
		foreach ($horarios as $horario) {

			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT * FROM reporte.especial_rendimiento_detalle_global_diario(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					pg_escape_string($horario->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			// print $sql;
			// exit;
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["especial_rendimiento_detalle_global_diario"]);
			$xpath = new DOMXpath($dom);

			$fpaso = $fhorario + 2;
			$tag_pasos = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle");
			$cfin = "B";

			if ($tag_pasos->length > 0) {
				foreach ($xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]") as $conf_paso) {
					$tag_paso = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]")->item(0);
					$tag_fechas = $xpath->query("datos/dato", $tag_paso);
					$cfin = $objWorksheet->getCellByColumnAndRow($tag_fechas->length + 1, $fpaso)->getColumn();

					$objWorksheet->getStyle("B".$fpaso.':'.$cfin.$fpaso)->getNumberFormat()->setFormatCode('0.00');
					$objWorksheet->getStyle("B".$fpaso)->getFont()->setBold(true);
					$objWorksheet->setCellValueByColumnAndRow(0, $fpaso, $conf_paso->getAttribute("nombre"));
					if($tag_fechas->length){
						$objWorksheet->setCellValueByColumnAndRow(1, $fpaso, '=AVERAGE(C'.$fpaso.':'.$cfin.$fpaso.')');
					}

					$cpaso = 2;
					foreach ($tag_fechas as $tag_fecha) {
						$letra = $objWorksheet->getCellByColumnAndRow($cpaso, $fpaso)->getColumn();
						$objWorksheet->getColumnDimension($letra)->setWidth(12);
						$objWorksheet->setCellValueByColumnAndRow($cpaso, $fhorario + 1,  $this->timestamp->getFormatearFecha($tag_fecha->getAttribute("fecha"), "d-m-Y"));
						$objWorksheet->setCellValueByColumnAndRow($cpaso, $fpaso, number_format($tag_fecha->getAttribute("respuesta"), 2, '.', ''));
						$cpaso++;
					}
					$fpaso++;
				}
			}

			$objWorksheet->mergeCells('A'.$fhorario.':'.$cfin.$fhorario);
			$objWorksheet->getStyle('A'.$fhorario.':'.$cfin.$fhorario)->applyFromArray($estilo_titulo);
			$objWorksheet->getStyle('A'.($fhorario + 1).':'.$cfin.($fhorario + 1))->applyFromArray($estilo_subtitulo);
			$objWorksheet->getStyle('B'.($fhorario + 1).':'.$cfin.($fhorario + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objWorksheet->setCellValueByColumnAndRow(0, $fhorario, $horario->nombre);
			$objWorksheet->setCellValueByColumnAndRow(0, $fhorario + 1, "Pasos");
			$objWorksheet->setCellValueByColumnAndRow(1, $fhorario + 1, "Promedio");

			$fhorario = $fpaso + 2;
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	function getEspecialIbankingObjetivos() {
		global $usr;
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$estilo_titulo = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '4f6228')),
				'font' => array('size' => 9, 'bold' => true, 'color' => array('rgb' => 'FFFFFF')),
				'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$estilo_subtitulo = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '4f6228')),
				'font' => array('size' => 9, 'color' => array('rgb' => 'FFFFFF')));
		$estilo_sla = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'c3d69b')),
				'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));

		$objetivo = new ConfigEspecial($this->objetivo_id);
		$objPHPExcel = new PHPExcel();

		$horario = $usr->getHorario($this->horario_id);

		$sheetid =1;
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {

			$objWorksheet = $objPHPExcel->createSheet();
			$objWorksheet->setTitle(substr(str_replace(array("https://","http://","[","]","\\","/",":"), array("","","(",")"," "," ",""), $subobjetivo->nombre), 0, 30));
			$objWorksheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(8);

			$objPHPExcel->setActiveSheetIndex($sheetid);
			$sheetid++;

			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT * FROM reporte.ranking_uptime(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($subobjetivo->objetivo_id).", ".
					pg_escape_string($this->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//			print($sql);

			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
//				$objWorksheet->setCellValueByColumnAndRow(0, 0, $sql);
//				$log->setError($sql, $res->userinfo);
				exit();
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["ranking_uptime"]);
			$xpath = new DOMXpath($dom);

			$tag_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);

			$objWorksheet->getColumnDimension('H')->setWidth(2);
			$objWorksheet->getColumnDimension('K')->setWidth(42);

			$objWorksheet->mergeCells('A1:K1');
			$objWorksheet->mergeCells('A5:G5');
			$objWorksheet->mergeCells('A6:B6');
			$objWorksheet->mergeCells('A7:B7');
			$objWorksheet->mergeCells('A8:B8');
			$objWorksheet->mergeCells('A9:B19');

			$objWorksheet->mergeCells('C6:G6');
			$objWorksheet->mergeCells('C7:G7');
			$objWorksheet->mergeCells('C8:G8');
			$objWorksheet->mergeCells('C9:G19');

			$objWorksheet->getStyle('A9:B19')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objWorksheet->getStyle('C9:G19')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setWrapText(true);
			$objWorksheet->getStyle('C3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objWorksheet->getStyle('E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objWorksheet->getStyle('G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objWorksheet->getStyle('A1:K1')->applyFromArray($estilo_titulo);
			$objWorksheet->getStyle('A3')->applyFromArray($estilo_subtitulo);
			$objWorksheet->getStyle('B3')->applyFromArray($estilo_sla);
			$objWorksheet->getStyle('D3')->applyFromArray($estilo_sla);
			$objWorksheet->getStyle('F3')->applyFromArray($estilo_sla);
			$objWorksheet->getStyle('I3:K3')->applyFromArray($estilo_titulo);
			$objWorksheet->getStyle('A5:G5')->applyFromArray($estilo_titulo);
			$objWorksheet->getStyle('A6:B6')->applyFromArray($estilo_subtitulo);
			$objWorksheet->getStyle('A7:B7')->applyFromArray($estilo_subtitulo);
			$objWorksheet->getStyle('A8:B8')->applyFromArray($estilo_subtitulo);
			$objWorksheet->getStyle('A9:B19')->applyFromArray($estilo_subtitulo);

			$objWorksheet->setCellValue('A1', strtoupper($subobjetivo->nombre));
			$objWorksheet->setCellValue('A3', "Estándar");
			$objWorksheet->setCellValue('B3', "Bajo");
			$objWorksheet->setCellValue('D3', "Medio");
			$objWorksheet->setCellValue('F3', "Alto");
			$objWorksheet->setCellValue('I3', "Fecha");
			$objWorksheet->setCellValue('J3', "Uptime [%]");
			$objWorksheet->setCellValue('K3', "Nodo");
			$objWorksheet->setCellValue('A5', "Uptime");
			$objWorksheet->setCellValue('A6', "Objetivo");
			$objWorksheet->setCellValue('A7', "Fuente");
			$objWorksheet->setCellValue('A8', "Horario");
			$objWorksheet->setCellValue('A9', "Descripción");

			$objWorksheet->setCellValue('C3', "0 - ".$tag_objetivo->getAttribute("sla_disponibilidad_error"));
			$objWorksheet->setCellValue('E3', $tag_objetivo->getAttribute("sla_disponibilidad_error")." - ".$tag_objetivo->getAttribute("sla_disponibilidad_ok"));
			$objWorksheet->setCellValue('G3', $tag_objetivo->getAttribute("sla_disponibilidad_error")." - 100");
			$objWorksheet->setCellValue('C6', $subobjetivo->nombre);
			$objWorksheet->setCellValue('C7', "Atentus");
			$objWorksheet->setCellValue('C8', $horario->nombre);

			$objWorksheet->setCellValue('C9', 'Corresponde a la  disponibilidad del servicio  web de Ibanking, expresado como porcentaje del tiempo de disponibilidad respecto al tiempo total de Servicio.

			∑〖Tiempo Servicio Disponible〗 * 100
			------------------------------------
			∑〖Tiempo total en Servicio〗');

			$arr_nodos = array();
			foreach ($xpath->query("/atentus/resultados/propiedades/nodos/nodo") as $tag_nodo) {
				$arr_nodos[$tag_nodo->getAttribute("nodo_id")] = $tag_nodo->getAttribute("nombre");
			}
			$arr_nodos[0] = "";

			$linea = 4;
			foreach ($xpath->query("/atentus/resultados/detalles/detalle") as $detalle_obj) {
				foreach ($xpath->query("estadisticas/estadistica", $detalle_obj) as $estadistica) {
					$objWorksheet->getStyle("I".$linea)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objWorksheet->getStyle("J".$linea)->getNumberFormat()->setFormatCode('0.00');

					$objWorksheet->setCellValueByColumnAndRow(8, $linea, date("d/m/Y", strtotime($estadistica->getAttribute("fecha"))));
					$objWorksheet->setCellValueByColumnAndRow(9, $linea, number_format($estadistica->getAttribute("uptime"), 2, '.', ''));
					$objWorksheet->setCellValueByColumnAndRow(10, $linea, $arr_nodos[$estadistica->getAttribute("nodo_id")]);
					$linea++;
				}
			}
		}

		$objPHPExcel->removeSheetByIndex(0);
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}


	function getEspecialDisponibilidadFullObjetivos() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$objetivo = new ConfigEspecial($this->objetivo_id);

		$objPHPExcel = new PHPExcel();
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$estilo_subtitulo = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '929292')),
				'font' => array('size' => 8, 'color' => array('rgb' => 'FFFFFF')));
		$estilo_parametro = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'f47001')),
				'font' => array('size' => 8, 'color' => array('rgb' => 'FFFFFF')));
		$objWorksheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(8);

		$objPHPExcel->getActiveSheet()->getStyle('A6:K6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('OOOOOOOO');
		$objWorksheet->getCell('A1')->setValueExplicit($objetivo->nombre, PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorksheet->getStyle('A1')->getFont()->setBold(true);
		$objWorksheet->getCell('A3')->setValueExplicit('Fecha', PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorksheet->getCell('B3')->setValueExplicit($this->timestamp->getInicioPeriodo("d-m-Y H:i:s")." hasta ".$this->timestamp->getTerminoPeriodo("d-m-Y H:i:s"), PHPExcel_Cell_DataType::TYPE_STRING);

		$objWorksheet->getCell('A6')->setValueExplicit('Objetivo', PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorksheet->getCell('B6')->setValueExplicit('Paso', PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorksheet->getCell('K6')->setValueExplicit('Tiempo Promedio', PHPExcel_Cell_DataType::TYPE_STRING);

		$objWorksheet->getStyle('A3')->applyFromArray($estilo_parametro);
		$objWorksheet->getStyle('A6:K6')->applyFromArray($estilo_subtitulo);

		$objWorksheet->getColumnDimension('A')->setWidth(30);
		$objWorksheet->getColumnDimension('B')->setWidth(25);
		$objWorksheet->getColumnDimension('C')->setWidth(12);
		$objWorksheet->getColumnDimension('D')->setWidth(12);
		$objWorksheet->getColumnDimension('E')->setWidth(12);
		$objWorksheet->getColumnDimension('F')->setWidth(12);
		$objWorksheet->getColumnDimension('G')->setWidth(12);
		$objWorksheet->getColumnDimension('H')->setWidth(12);
		$objWorksheet->getColumnDimension('I')->setWidth(12);
		$objWorksheet->getColumnDimension('J')->setWidth(12);
		$objWorksheet->getColumnDimension('K')->setWidth(12);

		$patron = "/^((\d) day)?\s?((\d\d):(\d\d):(\d\d))?/";

		$fila = 7;
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			$nuevo_usuario = Utiles::busca_usuario($subobjetivo->objetivo_id);
			if (!$nuevo_usuario) {
				continue;
			}

			$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado(".
					pg_escape_string($nuevo_usuario).",".
					pg_escape_string($subobjetivo->objetivo_id).", ".
					pg_escape_string($this->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."', 0)";

			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
//				$log->setError($sql, $res->userinfo);
//				exit();
				continue;
			}
			
			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['disponibilidad_resumen_consolidado']);
			$xpath = new DOMXpath($dom);

			$sql2 = "SELECT * FROM reporte.rendimiento_resumen_global(".
					pg_escape_string($nuevo_usuario).",".
					pg_escape_string($subobjetivo->objetivo_id).", ".
					pg_escape_string($this->horario_id).",' ".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

			$res2 = & $mdb2->query($sql2);
			if (MDB2::isError($res2)) {
//				$log->setError($sql, $res->userinfo);
//				exit();
				continue;
			}
			
			$row2 = $res2->fetchRow();
			$dom2 = new DomDocument();
			$dom2->preserveWhiteSpace = FALSE;
			$dom2->loadXML($row2['rendimiento_resumen_global']);
			$xpath2 = new DOMXpath($dom2);

			$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
			$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");

			$col_porcentaje = 2;
			$col_minutos = 6;
			$filatitulo = 6;
			foreach ($conf_eventos as $conf_evento) {
				$objWorksheet->setCellValueByColumnAndRow($col_porcentaje, $filatitulo, $conf_evento->getAttribute('nombre').' [%]');
				$objWorksheet->setCellValueByColumnAndRow($col_minutos, $filatitulo, $conf_evento->getAttribute('nombre').' [min]');
				$col_porcentaje++;
				$col_minutos++;
			}

			if ($conf_pasos->length > 0) {
				foreach ($conf_pasos as $paso) {
					$objWorksheet->getStyle("C".$fila.':F'.$fila)->getNumberFormat()->setFormatCode('0.00');
					$objWorksheet->setCellValueByColumnAndRow(0, $fila, $subobjetivo->nombre);
					$objWorksheet->setCellValueByColumnAndRow(1, $fila, $paso->getAttribute('nombre'));

					$col_porcentaje = 2;
					$col_minutos = 6;

					$array_min = array();
					$array_seg = array();
					foreach ($conf_eventos as $key => $conf_evento) {
						$tag_dato = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$paso->getAttribute('paso_orden')."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute('evento_id')."]");

						if ($tag_dato->length > 0) {
							preg_match($patron, $tag_dato->item(0)->getAttribute('duracion'), $matches);
							$objWorksheet->setCellValueByColumnAndRow($col_porcentaje, $fila, number_format($tag_dato->item(0)->getAttribute('porcentaje'), 2, '.', ''));
							$array_min[$key] = ($matches[2]*1440 + $matches[4]*60 + $matches[5]);
							$array_seg[$key] = $matches[6];
						}
						else {
							$objWorksheet->setCellValueByColumnAndRow($col_porcentaje, $fila, 0);
							$array_min[$key] = 0;
						}
						$col_porcentaje++;
					}

					for ($i = 0; $i < (array_sum($array_seg) / 60); $i++) {
						$index = array_search(max($array_seg), $array_seg);
						$array_min[$index] += 1;
						unset($array_seg[$index]);
					}
					
					foreach ($array_min as $key => $minutos) {
						$objWorksheet->setCellValueByColumnAndRow(($key+$col_minutos), $fila, $minutos);
					}

					$tag_promedio = $xpath2->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$subobjetivo->objetivo_id."]/detalles/detalle[@paso_orden=".$paso->getAttribute('paso_orden')."]/datos/dato");
					$objWorksheet->setCellValueByColumnAndRow(10, $fila, number_format(($tag_promedio->length == 0)?"0":$tag_promedio->item(0)->getAttribute('tiempo_prom'), 2, '.', ''));
					$fila++;
				}
			}
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	function getEspecialDisponibilidadFullObjetivosCSV() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$salto_linea = "\n";
		$csv_cabecera = "Objetivo;Paso;Uptime [%];Downtime parcial [%];Downtime [%];No monitoreo [%];Uptime [min];Downtime parcial [min];Downtime [min];No monitoreo [min];Tiempo Promedio".$salto_linea;
		$csv_contenido = "";

		$patron = "/^((\d) day)?\s?((\d\d):(\d\d):(\d\d))?/";

		$objetivo = new ConfigEspecial($this->objetivo_id);
		foreach ($objetivo->getSubobjetivos() as $k=>$subobjetivo) {
			$nuevo_usuario = Utiles::busca_usuario($subobjetivo->objetivo_id);
			if (!$nuevo_usuario) {
				continue;
			}

			$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado(".
					pg_escape_string($nuevo_usuario).",".
					pg_escape_string($subobjetivo->objetivo_id).", ".
					pg_escape_string($this->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."', 0)";

			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				continue;
			}

			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['disponibilidad_resumen_consolidado']);
			$xpath = new DOMXpath($dom);

			$sql = "SELECT * FROM reporte.rendimiento_resumen_global(".
					pg_escape_string($nuevo_usuario).",".
					pg_escape_string($subobjetivo->objetivo_id).", ".
					pg_escape_string($this->horario_id).",' ".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				continue;
			}
			
			$row = $res->fetchRow();
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['rendimiento_resumen_global']);
			$xpath2 = new DOMXpath($dom);

			$conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
			$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");

			if ($conf_pasos->length) {
				foreach ($conf_pasos as $paso) {
					$csv_contenido.= trim($subobjetivo->nombre);
					$csv_contenido.= ";".trim($paso->getAttribute('nombre'));
					
					$columns_por = "";
					$array_min = array();
					$array_seg = array();
					foreach ($conf_eventos as $key=>$conf_evento) {
						$tag_dato = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$paso->getAttribute('paso_orden')."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute('evento_id')."]");
						if ($tag_dato->length) {
							$columns_por.= ";". number_format($tag_dato->item(0)->getAttribute('porcentaje'), 2, '.', '');

							preg_match($patron, $tag_dato->item(0)->getAttribute('duracion'), $matches);
							$array_min[$key] = ($matches[2]*1440 + $matches[4]*60 + $matches[5]);
							$array_seg[$key] = $matches[6];
						}
						else {
							$array_min[$key] = 0;
							$columns_por.= ";". number_format(0, 2, '.', '');
						}
					}

					$csv_contenido.= $columns_por;

					for ($i = 0; $i < (array_sum($array_seg) / 60); $i++) {
						$index = array_search(max($array_seg), $array_seg);
						$array_min[$index] += 1;
						unset($array_seg[$index]);
					}

					$csv_contenido.= ";". join($array_min,";");

					$tag_promedio = $xpath2->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$subobjetivo->objetivo_id."]/detalles/detalle[@paso_orden=".$paso->getAttribute('paso_orden')."]/datos/dato");

					$csv_contenido.= ";". number_format( (!$tag_promedio->length) ? 0 : $tag_promedio->item(0)->getAttribute('tiempo_prom'), 2, '.', '' );
					$csv_contenido.= $salto_linea;
				}
			}
		}

		print $csv_cabecera;
		print $csv_contenido;
	}

/*	function getEspecialRendimientoDiasPeak() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $usr;

		$estilo_titulo = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '000000')),
				'font' => array('size' => 8, 'bold' => true, 'color' => array('rgb' => 'FFFFFF')),
				'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$estilo_subtitulo = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '929292')),
				'font' => array('size' => 8, 'color' => array('rgb' => 'FFFFFF')),
				'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$estilo_parametro = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'f47001')),
				'font' => array('size' => 8, 'color' => array('rgb' => 'FFFFFF')));

		$fechas = array(array("inicio" => "'%Y-%m-01 00:00:00'", "termino" => "'%Y-%m-11 00:00:00'"),
				array("inicio" => "'%Y-%m-11 00:00:00'", "termino" => "'%Y-%m-13 13:45:00'"),
				array("inicio" => "'%Y-%m-14 00:00:00'", "termino" => "'%Y-%m-01 00:00:00':: TIMESTAMP WITHOUT TIME ZONE + '1 month'::INTERVAL"));

		$ponderacion = $usr->getPonderacion();
		if ($ponderacion == null) {
			$ponderacion_id = 0;
		}
		else {
			$ponderacion_id = $ponderacion->ponderacion_id;
		}

		$objetivo = new ConfigEspecial($this->extra["parent_objetivo_id"]);
		$horario = $objetivo->getHorario($this->horario_id);
		$subobjetivo = $objetivo->getSubobjetivo($this->objetivo_id);

		$objPHPExcel = new PHPExcel();
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$objWorksheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(8);

		$objWorksheet->getStyle('A1')->getFont()->setBold(true);
		$objWorksheet->getStyle('A3:A5')->applyFromArray($estilo_parametro);
		$objWorksheet->setCellValueByColumnAndRow(0, 1, $objetivo->nombre);
		$objWorksheet->setCellValueByColumnAndRow(0, 3, "Objetivo");
		$objWorksheet->setCellValueByColumnAndRow(0, 4, "Horario");
		$objWorksheet->setCellValueByColumnAndRow(0, 5, "Fecha");
		$objWorksheet->setCellValueByColumnAndRow(1, 3, $subobjetivo->nombre);
		$objWorksheet->setCellValueByColumnAndRow(1, 4, $horario->nombre);
		$objWorksheet->setCellValueByColumnAndRow(1, 5, $this->timestamp->getInicioPeriodo("d-m-Y H:i:s")." hasta ".$this->timestamp->getTerminoPeriodo("d-m-Y H:i:s"));

		$columna_general = 0;
		$col_index = "";
		$set_cg = 0;
		foreach ($fechas as $i => $fecha) {
			$inicio = strftime($fecha["inicio"], strtotime($this->timestamp->fecha_inicio));
			$termino = strftime($fecha["termino"], strtotime($this->timestamp->fecha_inicio));

			$sql = "SELECT * FROM reporte.rendimiento_resumen_global_ponderado(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					pg_escape_string($ponderacion_id).", ".
					$inicio.", ".
					$termino.")";
//			print($sql);
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			$row = $res->fetchRow();

			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["rendimiento_resumen_global_ponderado"]);
			$xpath = new DOMXpath($dom);

			$param = $xpath->query("/atentus/resultados/parametros")->item(0);
			$ponderaciones = $xpath->query("/atentus/resultados/propiedades/ponderaciones/item[@valor!=0]");
			$pasos = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo/paso[@visible=1]");

			$linea = 7;
			$linea_resumen = $linea + 5 + $pasos->length;

			if ($columna_general == 0) {
				$linea_resumen_datos = $linea_resumen + 2;
				$objWorksheet->mergeCells(chr(65 + $columna_general).$linea_resumen.':'.chr(66 + $columna_general + $ponderaciones->length).$linea_resumen);
				$objWorksheet->getStyle(chr(65 + $columna_general).$linea_resumen)->applyFromArray($estilo_titulo);
				$objWorksheet->getStyle(chr(65 + $columna_general).($linea_resumen + 1).':'.chr(66 + $columna_general + $ponderaciones->length).($linea_resumen + 1))->applyFromArray($estilo_subtitulo);
				$objWorksheet->setCellValueByColumnAndRow(0, $linea_resumen, "Tiempos Promedios");
				$objWorksheet->setCellValueByColumnAndRow(0, $linea_resumen + 1, "Fecha");
				$objWorksheet->setCellValueByColumnAndRow($ponderaciones->length + 1, $linea_resumen + 1, "Promedio");
				$objWorksheet->setCellValueByColumnAndRow(0, $linea_resumen + count($fechas) + 2, "Promedio");
			}

			// ESTILO DE TITULO Y ANCHO
			$dia = "Días ".date("d", strtotime($param->getAttribute("fecha_inicio")))."-".date("d", strtotime($param->getAttribute("fecha_termino")) - 1);
			$mod = 0;
			if($columna_general>25){
				$mod = floor($columna_general/25);
				$col_index = chr(64+$mod) . $col_index;
				$set_cg = 26 * $mod;
			}

			$mc = ($columna_general-$set_cg) + $ponderaciones->length;
			$merge_col = chr(66 + $mc);
			if($mc > 25){
				$m = floor($mc/25);
				$merge_col = chr(64+($m + $mod));
				$merge_col.=chr(66 + ($mc - $m - 25) );
			}
			
			$col = $col_index.chr(65 + ($columna_general - $set_cg)).$linea;

			$objWorksheet->mergeCells($col_index.chr(65 + ($columna_general-$set_cg)).$linea.':'.$merge_col.$linea);
			$objWorksheet->getStyle($col_index.chr(65 + ($columna_general-$set_cg)).$linea)->applyFromArray($estilo_titulo);
			$objWorksheet->setCellValueByColumnAndRow($columna_general, $linea, $dia);
			$linea++;

			// ESTILO DE SUBTITULO Y ANCHO
			$objWorksheet->getStyle($col_index.chr(65 + ($columna_general-$set_cg)).$linea.':'.$merge_col.$linea)->applyFromArray($estilo_subtitulo);
			//$objWorksheet->getColumnDimension(chr(67 + $columna_general + $ponderaciones->length))->setWidth(2);
			$objWorksheet->getColumnDimension($col_index.chr(65 + ($columna_general-$set_cg)))->setWidth(18);

			$objWorksheet->setCellValueByColumnAndRow($columna_general, $linea, "Paso");
			$objWorksheet->setCellValueByColumnAndRow($columna_general + $ponderaciones->length + 1, $linea, "Promedio");
			$objWorksheet->setCellValueByColumnAndRow($columna_general, $pasos->length + $linea + 1, "Promedio");

			$objWorksheet->setCellValueByColumnAndRow(0, $linea_resumen_datos, $dia);

			$columna = $columna_general + 1;

			foreach ($ponderaciones as $ponderacion) {
				$fecha_promedio[$ponderacion->getAttribute("item_id")] = 0;
				$objWorksheet->setCellValueByColumnAndRow($columna, $linea, date("H", strtotime($ponderacion->getAttribute("inicio")))." - ".date("H", strtotime($ponderacion->getAttribute("termino")))." Hrs");
				if ($columna_general == 0) {
					$objWorksheet->setCellValueByColumnAndRow($columna, $linea_resumen + 1, date("H", strtotime($ponderacion->getAttribute("inicio")))." - ".date("H", strtotime($ponderacion->getAttribute("termino")))." Hrs");
				}
				$columna++;
			}

			$linea++;
			foreach ($pasos as $paso) {
				$columna = $columna_general;
				$objWorksheet->getStyle($col_index.chr(66 + ($columna-$set_cg)).$linea.':'. $merge_col.$linea)->getNumberFormat()->setFormatCode('0.00');
				$objWorksheet->setCellValueByColumnAndRow($columna, $linea, $paso->getAttribute("nombre"));
				$columna++;

				$paso_total = 0;
				foreach ($ponderaciones as $k => $ponderacion) {
					$dato = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@item_id=".$ponderacion->getAttribute("item_id")."]/detalles/detalle[@paso_orden=".$paso->getAttribute("paso_orden")."]/datos/dato")->item(0);
					$paso_total += ($dato == null)?0:($dato->getAttribute("tiempo_prom") / $ponderaciones->length);
					$fecha_promedio[$ponderacion->getAttribute("item_id")] += ($dato == null)?0:($dato->getAttribute("tiempo_prom") / $pasos->length);
					$objWorksheet->setCellValueByColumnAndRow($columna, $linea, number_format(($dato == null)?"0":$dato->getAttribute("tiempo_prom"), 2, '.', ''));
					$columna++;
				}

				// PROMEDIO DE PASOS
				$objWorksheet->getStyle($merge_col.$linea)->getFont()->setBold(true);
				$objWorksheet->getStyle($col_index.chr(65 + ($columna-$set_cg)).$linea)->getFont()->setBold(true);
				$objWorksheet->setCellValueByColumnAndRow($columna, $linea, number_format($paso_total, 2, '.', ''));
				$linea++;
			}

			$columna = $columna_general + 1;
			$fecha_total = 0;
			$objWorksheet->getStyle($col_index.chr(65 + ($columna-$set_cg - 1 )).$linea.':'.$merge_col.$linea)->getFont()->setBold(true);
			$objWorksheet->getStyle($col_index.chr(65 + ($columna-$set_cg )).$linea.':'. $merge_col.$linea)->getNumberFormat()->setFormatCode('0.00');
			foreach ($fecha_promedio as $k=>$valor) {
				$fecha_total += $valor / $ponderaciones->length;
				$objWorksheet->setCellValueByColumnAndRow($columna, $linea, number_format($valor, 2, '.', ''));
				$columna++;
			}

			$objWorksheet->setCellValueByColumnAndRow($columna, $linea, number_format($fecha_total, 2, '.', ''));

// 			$columna = 1;
			$fecha_total = 0;
			$columna_promedio=1;
			$objWorksheet->getStyle($merge_col.$linea.':'.$merge_col.$linea)->getNumberFormat()->setFormatCode('0.00');
			foreach ($fecha_promedio as $item_id => $valor) {
				$fecha_total += $valor / $ponderaciones->length;
				$ponderacion_promedio[$item_id] += $valor / count($fechas);
				$objWorksheet->setCellValueByColumnAndRow($columna_promedio, $linea_resumen_datos, number_format($valor, 2, '.', ''));
				$columna_promedio++;
			}
			
			// PROMEDIOS DE FECHAS
			$objWorksheet->getStyle($merge_col.$linea_resumen_datos)->getFont()->setBold(true);
			$objWorksheet->setCellValueByColumnAndRow($columna_promedio, $linea_resumen_datos, number_format($fecha_total, 2, '.', ''));

			$columna_general += $ponderaciones->length + 3;
			$linea_resumen_datos++;
			
		}

		$columna = 1;
		$ponderacion_total = 0;
		
 		$objWorksheet->getStyle(chr(65 + $columna - 1).$linea_resumen_datos.':'.chr(65 + $columna + $ponderaciones->length).$linea_resumen_datos)->getFont()->setBold(true);
 		$objWorksheet->getStyle(chr(65 + $columna).$linea_resumen_datos.':'.chr(65 + $columna + $ponderaciones->length).$linea_resumen_datos)->getNumberFormat()->setFormatCode('0.00');
		foreach ($ponderacion_promedio as $valor) {
			$ponderacion_total += $valor / $ponderaciones->length;
			$objWorksheet->setCellValueByColumnAndRow($columna, $linea_resumen_datos, number_format($valor, 2, '.', ''));
			$columna++;
		}
		$objWorksheet->setCellValueByColumnAndRow($columna, $linea_resumen_datos, number_format($ponderacion_total, 2, '.', ''));

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}*/

	function getEspecialDatosConLetraFullObjetivos(){
		global $mdb2;
		global $log;
//		global $current_usuario_id;

		ini_set("memory_limit","300M");

		$objetivo = new ConfigEspecial($this->objetivo_id);
		$objPHPExcel = new PHPExcel();
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$objWorksheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(8);
		$objWorksheet->getColumnDimension('B')->setWidth(15);
		$objWorksheet->getColumnDimension('C')->setWidth(15);
		$objWorksheet->getColumnDimension('D')->setWidth(15);

		$col=1;
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			$usuario_id = Utiles::busca_usuario($subobjetivo->objetivo_id);

			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT * FROM reporte.datos(".
					pg_escape_string($usuario_id).",".
					pg_escape_string($subobjetivo->objetivo_id).",'".
					pg_escape_string($this->timestamp->getInicioPeriodo())."','".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//			echo($sql);

			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				exit();
			}

			while ($row = $res->fetchRow()) {
				if(strstr($row['filas'],"servidor") == false && $row['filas'] != null){
					$filas = str_replace(array("{","}"),"",$row["filas"]);
					$filas2 = explode(',', $filas);
					$objWorksheet->setCellValueByColumnAndRow(0, $col, $subobjetivo->letra);
					for($i=0; $i<count($filas2); $i++){
						$objWorksheet->setCellValueByColumnAndRow($i+1, $col, $filas2[$i]);
					}
					$col = $col+1;
				}
			}
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	/*
	Creado por:Aldo Cruz Romero
	Fecha de creacion:21-09-2018
	Fecha de ultima modificacion:
	*/
	

	function getEspecialHacienda(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $meses_anno;

		function cambioutf($text){
			$text=utf8_decode($text);
			return $text;
		}

		//CONSULTA QUE OBTIENE TAGD DEL XML CONFIGURACION
		$objetivo = new ConfigEspecial($this->objetivo_id);
		$tipo_reporte = $objetivo->tipo_reporte;
		$titulo=$objetivo->__reporte['Titulo']->titulo;
		$footer_text=$objetivo->__reporte['Footer'];
		$footer_text=cambioutf($footer_text);
		
		//SETEO DE FECHA DE REPORTE
		$fecha_titulo_inicio=substr($this->timestamp->getInicioPeriodo(), 0,10);
		$fecha_titulo_termino=substr($this->timestamp->getTerminoPeriodo(), 0, 10);
		$fecha_titulo_termino = strtotime($fecha_titulo_termino);
		$fecha_titulo_termino = strtotime('-1 day', $fecha_titulo_termino);
		$fecha_titulo_termino = date('Y-m-d', $fecha_titulo_termino);

		$date=explode('-', $fecha_titulo_inicio);
		$dateTermino=explode('-', $fecha_titulo_termino);
		
		foreach ($meses_anno as $key_mes => $value_mes) {
			if(intval($date[1])==$key_mes){
				$mes= $value_mes;
			}
			if(intval($dateTermino[1])==$key_mes){
				$mes_termino=$value_mes;
			}
		}
		$date=$date[2].' de '.$mes;
		$dateTermino=$dateTermino[2].' de '.$mes_termino.' del '.$dateTermino[0];
		$date= 'Periodo del '.$date.' al '.$dateTermino;
		
		//SE RECORRE OBJETIVOS CREANDO ARRAYS PARA CREAR OBJETOS DE TABLAS SIGUIENTES
		$nombre_nodos=Array();
		$array_obj=Array();
		$cont_obj=0;
		$array_disp=Array();
		$consolidado_array=Array();
		foreach ($objetivo->getSubobjetivos() as $key => $subobjetivo) {
			$keys_pasos=(array_keys($subobjetivo->__pasos));
			$cont_obj++;
			$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado(".
				pg_escape_string($current_usuario_id).",".
				pg_escape_string($key).", ".
				pg_escape_string($this->horario_id).",' ".
				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
				pg_escape_string($this->timestamp->getTerminoPeriodo())."', ".
				(isset($this->extra["variable"])?$usr->cliente_id:'0').")";
			//echo $sql.'<br>';
			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_resumen_consolidado']);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_resumen_consolidado"]);
			}
			$conf_nodos=$xpath->query("/atentus/resultados/propiedades/nodos/nodo");
			$conf_obj= $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=0]/detalles/detalle/estadisticas/estadistica");
			$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
			$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");
			foreach ($conf_nodos as $key_nodo => $value_nodo) {
				if($value_nodo->getAttribute("nombre")!='Global'){
					array_push($nombre_nodos,$value_nodo->getAttribute("nombre"));
				}
			}
			
			$nombre_obj=$conf_objetivo->getAttribute("nombre");
			$consolidado_array[$nombre_obj]=Array();
			$array_up=Array();
			foreach ($conf_obj as $key => $value_obj) {
				if ($value_obj->getAttribute("evento_id")==1){
					if ($value_obj->getAttribute("porcentaje")!='0.00'){
						array_push($array_up, $value_obj->getAttribute("porcentaje"));
					}
				}
			}

			$array_nodo=Array();
			foreach ($conf_nodos as $key => $value_nodo) {
				if($value_nodo->getAttribute("nodo_id")!=0){
					$nombre_nodo=$value_nodo->getAttribute("nombre");
					$array_nodo[$nombre_nodo]=Array();
					$nodo_id=($value_nodo->getAttribute("nodo_id"));
					$tag_nodo = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=".$nodo_id."]")->item(0);
					$array_paso=Array();
					foreach ($conf_pasos as $key_pasos => $value_pasos) {
						$consolidado_data=Array();
						$paso=$value_pasos->getAttribute("paso_orden");
						$paso_nombre=$value_pasos->getAttribute("nombre");
						$paso_descripcion =$subobjetivo->__pasos[$paso]->descripcion;
						if($paso_descripcion == ''){
							$paso_descripcion = $paso_nombre;
						}
						//SE VALIDA SI EN XML ATRIBUTO PASOS
						if(strval($keys_pasos)=='Array'){
							//SE VALIDA SI LOS PASOS VISIBLES ESTAN DENTRO DE LOS PASOS DEL XML
							if(in_array($paso, $keys_pasos)){
								$dato = $xpath->query("detalles/detalle[@paso_orden=".$paso."]/estadisticas/estadistica", $tag_nodo);
								$acumulado=Array();
								foreach ($dato as $key_dato => $value) {
									$evento=($value->getAttribute("evento_id"));
									$acumulado[$evento]=$value->getAttribute("porcentaje");
									$uptime=$acumulado[1];
									$no_monitoreo=$acumulado[7];
									$downtime=$acumulado[2];
									$marcado=$acumulado[9];
								}
								$empty='';
								if($downtime==$empty){
									$downtime=0;
								}
								if($uptime==$empty){
									$uptime=0;
								}
								if($no_monitoreo==$empty){
									$no_monitoreo=0;
								}
								if($marcado==$empty){
									$marcado=0;
								}
								array_push($consolidado_data, $uptime);
								array_push($consolidado_data, $no_monitoreo);
								array_push($consolidado_data, $downtime);
								array_push($consolidado_data, $marcado);
								$array_paso[$paso_descripcion]=$consolidado_data;
							}
						}else{
							$dato = $xpath->query("detalles/detalle[@paso_orden=".$paso."]/estadisticas/estadistica", $tag_nodo);
							$acumulado=Array();
							foreach ($dato as $key_dato => $value) {
								$evento=($value->getAttribute("evento_id"));
								$acumulado[$evento]=$value->getAttribute("porcentaje");
								$uptime=$acumulado[1];
								$no_monitoreo=$acumulado[7];
								$downtime=$acumulado[2];
								$marcado=$acumulado[9];
							}
							$empty='';
							if($downtime==$empty){
								$downtime=0;
							}
							if($uptime==$empty){
								$uptime=0;
							}
							if($no_monitoreo==$empty){
								$no_monitoreo=0;
							}
							if($marcado==$empty){
								$marcado=0;
							}
							array_push($consolidado_data, $uptime);
							array_push($consolidado_data, $no_monitoreo);
							array_push($consolidado_data, $downtime);
							array_push($consolidado_data, $marcado);
							$array_paso[$paso_descripcion]=$consolidado_data;
						}
						
						
					}
					//var_dump($array_paso);
					array_push($array_nodo[$nombre_nodo], $array_paso);
				}
			}
			array_push($consolidado_array[$nombre_obj],$array_nodo);
		}

		$PHPWord = new PHPWord();
		$PHPWord->addFontStyle('TitleStyle', array('size'=>20,'name'=>'Calibri', 'color'=>'white', 'bold'=>true, 'spaceAfter'=>100, 'spaceBefore' => 0));
		$PHPWord->addFontStyle('LogoTitleStyle', array('size'=>24,'name'=>'Calibri', 'color'=>'333333', 'bold'=>true, 'spaceAfter'=>100, 'spaceBefore' => 0));
		$PHPWord->addFontStyle('TitleStyleDate', array('size'=>12,'name'=>'Calibri', 'color'=>'white', 'bold'=>true, 'spaceAfter'=>100, 'spaceBefore' => 0));
		$PHPWord->addParagraphStyle('StyleTitle', array('align'=>'left', 'spaceBefore' => 0));
		$PHPWord->addParagraphStyle('StyleTitle2', array('align'=>'right', 'spaceBefore' => 0));
		$PHPWord->addFontStyle('firstcellfont', array('size'=>12,'name'=>'Calibri', 'color'=>'white', 'bold'=>true, 'spaceAfter'=>100));
		$PHPWord->addParagraphStyle('firstcell', array('align'=>'center'));
		$PHPWord->addFontStyle('cellfont', array('size'=>11,'name'=>'Calibri', 'color'=>'black', 'spaceAfter'=>100));
		$PHPWord->addParagraphStyle('cell', array('align'=>'center'));
		$PHPWord->addParagraphStyle('cellleft', array('align'=>'left'));
		$initialcell =  array('size'=>24,'name'=>'Calibri', 'color'=>'333333', 'bold'=>true, 'spaceAfter'=>100);
		$border=array('borderBottomSize'=>18);
		$border_td=array('borderBottomSize'=>5, "borderLeftSize"=>5, "borderRightSize"=>5, "borderTopSize"=>5);
		$borderleft=array("borderLeftSize"=>5);
		$borderleftTop=array("borderLeftSize"=>5, "borderTopSize"=>5);
		$borderBottomleft=array("borderLeftSize"=>5, "borderBottomSize"=>5);
		$cellstyle=array("bgColor"=>"626262");
		$cellstyle2=array( "bgColor"=>"f47001");
		$celinter1=array( "bgColor"=>"ffffff", 'borderBottomSize'=>5);
		$celinter2=array("bgColor"=>"D3D3D3", 'borderBottomSize'=>5);
		$cellColSpan = array("bgColor"=>"green","gridSpan" => 2, "valign" => "center");

		// New portrait section
		$section = $PHPWord->createSection(array('orientation'=>'landscape'));
		//SECTION ES LA PRIMERA INSTANCIA PARA CREAR PRIMERA HOJA DISTINTA
		$header = $section->createHeader();
		$table = $header->addTable();
        $table->addRow();
        $table->addCell(1)->addImage(REP_PATH_IMG.'arriba.png', array('width'=>0.1, 'height'=>0.1, 'align'=>'left'));
		
		//SE CREA FOOTER DE PRIMERA PAGINA
		$footer=$section->createFooter();
		$textempty='';
		$footer->addText($textempty);

		//SE CREA TEXTO DE PRIMERA PAGINA
		$table_titulo = $section->addTable();
		$table_titulo->addRow();
		$table_titulo->addCell(18000,$cellstyle2)->addText($date,'TitleStyleDate','StyleTitle2');
		$table_titulo->addRow(5500);
		$table_titulo->addCell(18000, $cellstyle2);
		$table_titulo->addRow();
		$titulo=cambioutf($titulo);
		$table_titulo->addCell(18000, $cellstyle2)->addText($titulo,'TitleStyle');
		$table_titulo->addRow(1500);
		$table_titulo->addCell(18000, $cellstyle2);
		$table_titulo->addRow();
		$table_titulo->addCell(18000, $cellstyle2)->addImage(REP_PATH_IMG.'header_blanco.png', array('width'=>160, 'height'=>40, 'align'=>'right', 'color'=>'white'));

		//COMIENZO DE SEGUNDA INSTANCIA, COMIENZO DEL REPORTE
		$section2 = $PHPWord->createSection(array('orientation'=>'landscape'));

		$sec2Header = $section2->createHeader();
		$table2 = $sec2Header->addTable();
        $table2->addRow();
		$table2->addCell(4500)->addImage(REP_PATH_IMG.'atentus_logo.png', array('width'=>200, 'height'=>60, 'align'=>'left'));
		$table2->addCell(4500)->addImage(REP_PATH_IMG.'footer_pdf.png', array('width'=>800, 'height'=>80, 'align'=>'left'));
		$sec2Footer = $section2->createFooter();
		$sec2Footer->addPreserveText('pagina {PAGE} de {NUMPAGES} 				'.$footer_text);
		
		//CREACION DE PRESENTACION
		
		$table_presentacion= $section2->addTable();
		$PHPWord->addFontStyle('presentacionStyle', array('size'=>12,'name'=>'Calibri', 'color'=>'333333', 'bold'=>true, 'spaceAfter'=>100));
		$PHPWord->addParagraphStyle('presentacionTitle', array('align'=>'both'));
		$table_presentacion->addRow();
		$table_presentacion->addCell(90000, $border)->addText(cambioutf('PRESENTACIÓN'),$initialcell);

		//SE ESTABLECE TEXTO DE LA TABLA PRESENTACION
		
		$table_presentacion->addRow();
		$presentacion=cambioutf($objetivo->__reporte['Presentacion'][0]->texto);
		$presentacion2=cambioutf($objetivo->__reporte['Presentacion'][1]->texto);
		$table_presentacion->addCell(90000)->addText($presentacion.'.','presentacionStyle', 'presentacionTitle');
		$table_presentacion->addRow();
		$table_presentacion->addCell(90000)->addText($presentacion2.':','presentacionStyle', 'presentacionTitle');
		$nombre_nodos=array_unique($nombre_nodos);

		//SE CREAN NODOS DE LA TABLA PRESENTACION
		$contador_nodo=1;
		foreach ($nombre_nodos as $value_nodos) {
			$table_presentacion->addRow();
			$nodo=$contador_nodo.') '.$value_nodos;
			$nodo=cambioutf($nodo);
			$table_presentacion->addCell(1)->addText($nodo);
			$contador_nodo++;
		}
		$section2->addPageBreak();
		
		if($tipo_reporte!=1){
			$table_consolidado= $section2->addTable();
			$table_consolidado->addRow();
			$table_consolidado->addCell(90000, $border)->addText(cambioutf('RESUMEN'),$initialcell);
			$table_consolidado->addRow();
			$table_consolidado->addCell(90000)->addText($date);
			$section2->addTextBreak(1);
			$linea_resumen=$objetivo->__reporte['Consolidado'][0].':';
			$table_consolidado->addRow();
			$table_consolidado->addCell(90000)->addText($linea_resumen,'presentacionStyle', 'presentacionTitle');
			
		
			//SE CREA PRIMERA FILA DE TABLA DE RESUMEN
			$table_consolidado2= $section2->addTable();
			$table_consolidado2->addRow();
			$table_consolidado2->addCell(2000);
			$table_consolidado2->addCell(3000, $cellstyle)->addText(cambioutf('Nº'), 'firstcellfont', 'firstcell');
			$table_consolidado2->addCell(3000, $cellstyle)->addText('HOST', 'firstcellfont', 'firstcell');
			$table_consolidado2->addCell(3000, $cellstyle2)->addText('DISPONIBILIDAD', 'firstcellfont', 'firstcell');
			
			$contador_obj=1;
			foreach ($consolidado_array as $key_objetivos => $value_objetivos) {
				$cont=Array();
				$Suma=Array();
				$consolidado_uptime = Array();
				foreach ($value_objetivos[0] as $key_mon => $value_monitor){
					$dataUptime=Array();
					foreach($value_monitor[0] as $key_data => $data){
						if ($data[0]!=0){
							array_push($consolidado_uptime, $data[0]);
							array_push($dataUptime, $data[0]);
						}
					}
					if(sizeof($dataUptime)>0){
						array_push($Suma, (array_sum($dataUptime)));
						array_push($cont, sizeof($dataUptime));
					}
				}

				$promedio = max($consolidado_uptime);

				//SE CREA VALORES POR OBJETIVO DE LA TABLA RESUMEN
				array_push($array_disp, $promedio);
				$cont++;
				if($contador_obj%2==0){
					$cell=$celinter1;
				}else{
					$cell=$celinter2;
				}
				$table_consolidado2->addRow();
				$table_consolidado2->addCell(2000);
				$table_consolidado2->addCell(3000, $cell)->addText(strval($contador_obj), 'cellfont', 'cell');
				$table_consolidado2->addCell(3000, $cell)->addText($key_objetivos, 'cellfont', 'cellleft');
				$table_consolidado2->addCell(3000, $cell)->addText(number_format(round($promedio, 2), 2).' % (1)', 'cellfont', 'cell');
				$contador_obj++;
			}

			$len_disp=count($array_disp);
			if($len_disp>0){
				$disponibilidad=number_format(round((array_sum($array_disp)/$len_disp),2), 2);
			}else{
				$disponibilidad=0;
			}
			if($cont%2==0){
				$cell=$celinter2;
			}else{
				$cell=$celinter1;
			}
			
			$table_consolidado2->addRow();
			$table_consolidado2->addCell(1000);
			if($tipo_reporte==2){
				$table_consolidado2->addCell(2000, $cell)->addText('', 'cellfont', 'cellleft');
				$table_consolidado2->addCell(2000, $cell)->addText('Promedio', 'cellfont', 'cellleft');
				$table_consolidado2->addCell(2000, $cell)->addText($disponibilidad.' %', 'cellfont', 'cell');
			}
			$section2->addTextBreak(1);
			
			//SE CREA TABLA DE DESCRIPCIONES(TEXTO) DE TABLA RESUMEN
			$table_consolidado3= $section2->addTable();
			foreach ($objetivo->__reporte['Consolidado'] as $key => $descripcion) {
				if($key!=0){
					$descripciones=cambioutf($descripcion);
					$table_consolidado3->addRow();
					$table_consolidado3->addCell(20000)->addText('('.$key.') '.$descripciones.'.', 'presentacionStyle', 'presentacionTitle');
				}
			}
		
			$section2->addPageBreak();
		}

		//SE CREA TABLA DE RESUMEN DE SERVICIOS

		// SE CREA ENCABEZADO
		
		$table__presentacion_resumen= $section2->addTable();
		$table__presentacion_resumen->addRow();
		$table__presentacion_resumen->addCell(90000, $border)->addText(cambioutf('Detalle De Servicios'),$initialcell);
		$section2->addTextBreak();
		$cellRowSpan = array('vMerge' => 'restart');
		$cellRowContinue = array('vMerge' => 'continue');
		$cont_obj=0;
		// SE CREA TABLA POR OBJETIVO
		$len_obj= count($consolidado_array);
		foreach ($consolidado_array as $objetivo_nombre => $nodos) {
			$table_resumen= $section2->addTable();
			$table_resumen->addRow();
			$table_resumen->addCell(90000)->addText($date);
			$table_resumen->addRow();
			$table_resumen->addCell(2000, $cellstyle)->addText('Host', 'firstcellfont', 'firstcell');
			$table_resumen->addCell(2000, $cellstyle)->addText('Isp', 'firstcellfont', 'firstcell');
			$table_resumen->addCell(2000, $cellstyle)->addText('Pasos', 'firstcellfont', 'firstcell');
			$table_resumen->addCell(2000, $cellstyle2)->addText('Uptime', 'firstcellfont', 'firstcell');
			$table_resumen->addCell(2000, $cellstyle2)->addText('No Monitoreo', 'firstcellfont', 'firstcell');
			$table_resumen->addCell(2000, $cellstyle2)->addText('Downtime Global', 'firstcellfont', 'firstcell');
			$table_resumen->addCell(2000, $cellstyle2)->addText('Mantenimiento', 'firstcellfont', 'firstcell');
			$table_resumen->addRow();
			$table_resumen->addCell(4000, $borderleftTop )->addText($objetivo_nombre);
			$primero=true;
			foreach ($nodos[0] as $nodo => $pasos) {
				if($primero==true){
						$primero=false;
						$table_resumen->addCell(2000, $borderleftTop)->addText($nodo);
					}else{
						$table_resumen->addRow();
						$table_resumen->addCell(2000, $borderleft,$cellRowContinue);
						$table_resumen->addCell(2000, $borderleftTop)->addText($nodo);
					}
				$primero=true;
				$len_pasos= count($pasos[0]);
				$cont_paso=0;
				foreach ($pasos[0] as $paso => $value_data) {
					$cont_paso++;
					if($primero==true){
						$primero=false;
						$table_resumen->addCell(2000, $border_td)->addText($paso);
					}else{
						$table_resumen->addRow();
						$table_resumen->addCell(2000, $borderleft, $cellRowContinue);
						if($cont_paso!=$len_pasos){
							$table_resumen->addCell(2000, $borderleft, $cellRowContinue);
						}else{
							$table_resumen->addCell(2000, $borderBottomleft, $cellRowContinue);
						}
						$table_resumen->addCell(2000, $border_td)->addText($paso);
					}
					foreach ($value_data as $key => $data) {
						$table_resumen->addCell(500, $border_td)->addText($data.' %', 'cellfont','cell');
					}
				}
			}
			$table_resumen->addRow();
			$table_resumen->addCell(2000, array("borderTopSize"=>5));
			$cont_obj++;
			if($cont_obj!=$len_obj){
				$section2->addPageBreak();
			}
		}
		$objWriter =  PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
		ob_clean();
		$objWriter->save('php://output');
		
	}
	/*
	Creado por:Aldo Cruz Romero
	Modificado por: 16-03-2018
	Fecha de creacion:-
	Fecha de ultima modificacion:
	*/
	function getEspecialPodJudicial(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $meses_anno;


		$objetivo = new ConfigEspecial($this->objetivo_id);
		
		$PHPWord = new PHPWord();

		// New portrait section
		$section = $PHPWord->createSection();

		//CONSULTA QUE OBTIENE TAGD DEL XML CONFIGURACION
		$sql_config = "SELECT xml_configuracion FROM objetivo_config where es_ultima_config='t' and objetivo_id=".$this->objetivo_id;
			//echo $sql_config.'<br>';
			$res =& $mdb2->query($sql_config);
		if (MDB2::isError($res)) {
			$log->setError($sql_config, $res->userinfo);
		exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['xml_configuracion']);
			$xpath_config = new DOMXpath($dom);
			unset($row["xml_configuracion"]);
		}

		$conf_xml= $xpath_config->query("/atentus/config/especial");
		$conf_descripciones= $xpath_config->query("/atentus/config/especial/descripciones/leyendas/leyenda");
		$conf_texto= $xpath_config->query("/atentus/config/especial/descripciones/texto")->item(0);
		$conf_xml2 = $xpath_config->query("/atentus/config/especial")->item(0);
		$estados= $conf_xml2->getAttribute("tag_estado");
		$titulo= $conf_xml2->getAttribute("nombre");
		$descripcion_texto=$conf_texto->getAttribute("descripcion");
		$descripcion_texto=utf8_decode($descripcion_texto);
		
		$descripcion=$conf_descripciones->item(0);
		$descripcion_error=$conf_descripciones->item(1);
		$descripcion_ok= $descripcion->getAttribute("descripcion_ok");
		$descripcion_ok= utf8_decode($descripcion_ok);

		$descripcion_error=$descripcion_error->getAttribute("descripcion_error");
		$descripcion_error=utf8_decode($descripcion_error);
		// Title styles
		$PHPWord->addFontStyle('TitleStyle', array('size'=>24,'name'=>'Calibri', 'color'=>'333333', 'bold'=>true, 'spaceAfter'=>100));
		$PHPWord->addParagraphStyle('StyleTitle', array('align'=>'center'));

		// Add header
		$header = $section->createHeader();
		$table = $header->addTable();
		$table->addRow();
		$table->addCell(4500)->addImage(REP_PATH_IMG.'poder.png', array('width'=>140, 'height'=>60, 'align'=>'left'));
		$table->addCell(4500)->addImage(REP_PATH_IMG.'atentus_logo.png', array('width'=>160, 'height'=>60, 'align'=>'right'));
		
		$styleTable_Titulo = array('borderTopSize'=>6, 'borderColor'=>'000000', 'cellMargin'=>80,'unit' => 'pct','align' => 'center');
		$styleFirstRow_Titulo = array('borderBottomSize'=>18, 'borderTopSize'=>18);

		// Define cell style arrays
		$styleCell = array('valign'=>'center');
		$styleCellBTLR = array('valign'=>'center', 'textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR);

		// Define font style for first row
		$fontStyle = array('bold'=>true, 'align'=>'center');

		// Add table style
		$PHPWord->addTableStyle('MyTable', $styleTable_Titulo, $styleFirstRow_Titulo);

		$table_titulo = $section->addTable('MyTable');
		$table_titulo->addRow();
		$table_titulo->addCell(9000,$styleFirstRow2)->addText($titulo,'TitleStyle', 'StyleTitle');

		$section->addTextBreak(1);
		$date;
		$array_obj=Array();
		$cont=0;
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			$cont++;
			$array_obj[$cont]=$subobjetivo->nombre;
		}
		$cont_color=0;
		$array_color=Array();
		$array_datos=array();
		$arrayObjData=Array();
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			$usuario = Utiles::busca_usuario($subobjetivo->objetivo_id);
			$nombre_obj=$subobjetivo->nombre;
			$sql = "SELECT * FROM reporte.disponibilidad_downtime_global(".
			pg_escape_string($current_usuario_id).",".
			pg_escape_string($subobjetivo->objetivo_id).", ".
			pg_escape_string($this->horario_id).", '".
			pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
			pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			//echo $sql.'<br>';exit();
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
			exit();
			}
			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_downtime_global']);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_downtime_global"]);
			}
			$conf_objetivo= $xpath->query("/atentus/resultados/detalles/detalle/datos/dato");
			$fecha_titulo=substr($this->timestamp->getInicioPeriodo(), 0,10);
			
			$date=explode('-', $fecha_titulo);
			foreach ($meses_anno as $key_mes => $value_mes) {
				if(intval($date[1])==$key_mes){
					$mes= $value_mes;
				}
			}
			$array_dato=Array();
			$date=$date[2].' de '.$mes.' del '.$date[0];
			$arrayDta=Array();
			foreach ($conf_objetivo as $conf_objetivos) {
				$array_dato['nombre']=$nombre_obj;
				$array_dato['inicio']=$conf_objetivos->getAttribute('inicio');
				$array_dato['termino']=$conf_objetivos->getAttribute('termino');
				$array_dato['duracion']=$conf_objetivos->getAttribute('duracion');
				$array_dato['color']=$conf_objetivos->getAttribute('evento_id');
				array_push($arrayDta, $array_dato);
			}
			$arrayData=Array();
			if(sizeof($arrayDta)>0){
				foreach ($arrayDta as $key => $valuedata) {
					if($valuedata["color"]=='2'){
						$array=Array();
						$array["nombre"]=$valuedata["nombre"];
						$array["inicio"]=$valuedata["inicio"];
						$array["termino"]=$valuedata["termino"];
						$array["duracion"]=$valuedata["duracion"];
						$array["color"]='d3222a';
						array_push($arrayData, $array);
					}
				}
			}else{
				$array=Array();
				$array["nombre"]=$subobjetivo->nombre;
				$array["color"]='green';
				array_push($arrayData, $array);
			}
			if(sizeof($arrayData)>0){
				array_push($arrayObjData, $arrayData);
			}
		}
		//var_dump($arrayObjData);
		$ArrayObj=Array();
		foreach ($arrayObjData as $key => $value) {
			$arrayDatas=Array();
			foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
				if($value[0]["nombre"]==$subobjetivo->nombre){
					$array=Array();
					$array["nombre"]=$subobjetivo->nombre;
					$array["color"]=$value[0]["color"];
					$array["data"]=$value;
					$arrayDatas=$array;
				}
			}
			array_push($ArrayObj, $arrayDatas);
		}
		$arrayValido=array_chunk($ArrayObj,4);
		
		$PHPWord->addFontStyle('date', array('size'=>8, 'color'=>'333333', 'spaceAfter'=>50));
		$PHPWord->addParagraphStyle('date_1', array('align'=>'right'));
		$PHPWord->addParagraphStyle('text_center', array('align'=>'center'));

		$section->addText("Santiago, ".$date, 'date', 'date_1');
		$section->addTextBreak(1);

		// Define table style arrays
		$styleTable = array('borderSize'=>3, 'borderColor'=>'000000','align' => 'center');

		// Define cell style arrays
		$styleCell = array('align'=>'center');

		// Define font style for first row
		$fontStyle = array('align'=>'center');

		// Add table style
		$PHPWord->addTableStyle('myOwnTableStyle', $styleTable);

		$styleTableData = array('borderSize'=>6, 'borderColor'=>'000000','align' => 'center');
		$PHPWord->addTableStyle('data', $styleTableData);

		$validador_error='false';
		foreach ($arrayValido as $key => $array_obj_estado) {
			$table = $section->addTable('myOwnTableStyle');
			// Add row
			$table->addRow(400);
			$table->addCell(1500, $styleCell)->addText("");
			foreach ($array_obj_estado as $index => $value_objetivos) {
				for ($i=0; $i <4 ; $i++) {
					if($i==$index){
						$table->addCell(2000, $styleCell)->addText($value_objetivos["nombre"],'date', 'text_center');
					}
				}
			}
			$table->addRow(400);
			$table->addCell(1800, $styleCell)->addText($estados, 'date', 'text_center');
			foreach ($array_obj_estado as $index => $value_objetivos) {
				for ($i=0; $i <4 ; $i++) {
					if($i==$index){
						$color=$value_objetivos["color"];
						if($color==='green'){
							$table->addCell(2000)->addImage(REP_PATH_IMG.'done.png', array('width'=>15, 'height'=>9, 'align'=>'center'));
						}else{
							$validador_error='true';
							$table->addCell(2000)->addImage(REP_PATH_IMG.'error.png', array('width'=>15, 'height'=>9, 'align'=>'center'));
						}
					}
				}
			}
			$section->addTextBreak(1);
		}
		$section->addTextBreak(1);

		$table2 = $section->addTable();
		$table2->addRow(600);
		$table2->addCell()->addText("Verde(");
		$table2->addCell()->addImage(REP_PATH_IMG.'done.png', array('width'=>15, 'height'=>9, 'align'=>'center'));
		$table2->addCell()->addText("):  ");
		$table2->addCell(20000000)->addText($descripcion_ok,array('width'=>2000, 'height'=>9, 'align'=>'center'));
		$table2->addRow(600);
		$table2->addCell()->addText("Rojo(");
		$table2->addCell()->addImage(REP_PATH_IMG.'error.png', array('width'=>15, 'height'=>9, 'align'=>'center'));
		$table2->addCell()->addText("):  ");
		$table2->addCell(20000000)->addText($descripcion_error,array('width'=>2000, 'height'=>9, 'align'=>'center'));
		
		$section->addTextBreak(1);

		$table_error = $section->addTable();
		if($validador_error=='true'){
			$table_error->addRow(600);
			$table_error->addCell(20000000)->addText("Detalle de horarios en que no hubo conectividad: ",array('width'=>200000, 'height'=>9, 'align'=>'center'));
			$section->addTextBreak(1);
		}

		// Define table style arrays
		$styleTable2 = array('borderSize'=>1, 'borderColor'=>'000000','align' => 'center');
		$styleFirstRow2 = array( 'bgColor'=>'B7B4B4');
		$normalCellData= array( 'bgColor'=>'eeeeee','color'=>'white');
		// Add table style
		$PHPWord->addTableStyle('myOwnTableStyle2', $styleTable2);

		foreach ($ArrayObj as $key => $value_data) {
			if($value_data["data"][0]["inicio"]!=NULL){
				$objetivos=$value_data["nombre"];
				$table_obj = $section->addTable('myOwnTableStyle2');
				$table_obj->addRow();
				$table_obj->addCell(9000,$styleFirstRow2)->addText($objetivos,'date', 'text_center');
				foreach ($value_data["data"] as $objetivos=> $value) {
					$inicio=$value["inicio"];
					$termino=$value["termino"];
					$duracion=$value["duracion"];
					$table_data = $section->addTable('data');
					$table_data->addRow();
					$table_data->addCell(3000, $styleFirstRow2)->addText("Hora Inicio", 'date', 'text_center');
					$table_data->addCell(3000, $styleFirstRow2)->addText("Hora Termino", 'date', 'text_center');
					$table_data->addCell(3000, $styleFirstRow2)->addText("Duracion", 'date', 'text_center');
					if($duracion=='1 day'){
						$duracion='1 Día';
						$duracion=utf8_decode($duracion);
					}
					$table_mediciones = $section->addTable('data');
					$table_mediciones->addRow();
					$table_mediciones->addCell(3000, $normalCellData)->addText($inicio,'date','text_center');
					$table_mediciones->addCell(3000, $normalCellData)->addText($termino,'date','text_center');
					$table_mediciones->addCell(3000, $normalCellData)->addText($duracion,'date','text_center');
				}
			}
			$section->addTextBreak(1);
		}
		$text = "Este certificado es emitido por Servicios de Monitoreo S.A. (Atentus), que entrega servicios de monitoreo al Poder Judicial, dónde la metodología utilizada se basa en realizar ingresos automatizados mediante robots instalados dentro de los principales ISP (proveedores de Internet) de Chile, simulando la experiencia de un usuario real.";
		$text=utf8_decode($text);
		$PHPWord->addFontStyle('r2Style', array('bold'=>true, 'italic'=>false, 'size'=>10));
		$PHPWord->addParagraphStyle('p2Style', array('align'=>'both', 'spaceAfter'=>100));
		$section->addText($text, 'r2Style', 'p2Style');
		$section->addTextBreak(1);

		
		$PHPWord->addFontStyle('Style', array('bold'=>true, 'italic'=>false, 'size'=>8));
		$section->addText($descripcion_texto, 'Style', 'p2Style');

		$objWriter =  PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
		ob_clean();
		$objWriter->save('php://output');
	}

	function getEspecialContraloria(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $meses_anno;


		$objetivo = new ConfigEspecial($this->objetivo_id);
		
		$PHPWord = new PHPWord();

		// New portrait section
		$section = $PHPWord->createSection();

		//CONSULTA QUE OBTIENE TAGD DEL XML CONFIGURACION
		$sql_config = "SELECT xml_configuracion FROM objetivo_config where es_ultima_config='t' and objetivo_id=".$this->objetivo_id;
		$res =& $mdb2->query($sql_config);
		if (MDB2::isError($res)) {
			$log->setError($sql_config, $res->userinfo);
		exit();
		}
		if ($row = $res->fetchRow()) {
			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row['xml_configuracion']);
			$xpath_config = new DOMXpath($dom);
			unset($row["xml_configuracion"]);
		}

		$conf_xml= $xpath_config->query("/atentus/config/especial");
		$conf_descripciones= $xpath_config->query("/atentus/config/especial/descripciones/leyendas/leyenda");
		$conf_texto= $xpath_config->query("/atentus/config/especial/descripciones/texto")->item(0);
		$conf_xml2 = $xpath_config->query("/atentus/config/especial")->item(0);
		$estados= $conf_xml2->getAttribute("tag_estado");
		$titulo= $conf_xml2->getAttribute("nombre");
		$titulo = utf8_decode($titulo);
		$descripcion_texto=$conf_texto->getAttribute("descripcion");
		$descripcion_texto=utf8_decode($descripcion_texto);
		
		$descripcion=$conf_descripciones->item(0);
		$descripcion_error=$conf_descripciones->item(1);
		$descripcion_ok= $descripcion->getAttribute("descripcion_ok");
		$descripcion_ok= utf8_decode($descripcion_ok);

		$descripcion_error=$descripcion_error->getAttribute("descripcion_error");
		$descripcion_error=utf8_decode($descripcion_error);
		// Title styles
		$PHPWord->addFontStyle('TitleStyle', array('size'=>24,'name'=>'Calibri', 'color'=>'333333', 'bold'=>true, 'spaceAfter'=>100));
		$PHPWord->addParagraphStyle('StyleTitle', array('align'=>'center'));

		// Add header
		$header = $section->createHeader();
		$table = $header->addTable();
		$table->addRow();
		$table->addCell(4500)->addImage(REP_PATH_IMG.'contraloria.jpeg', array('width'=>70, 'height'=>70, 'align'=>'left'));
		$table->addCell(4500)->addImage(REP_PATH_IMG.'atentus_logo.png', array('width'=>200, 'height'=>60, 'align'=>'right'));
		
		$styleTable_Titulo = array('borderTopSize'=>6, 'borderColor'=>'000000', 'cellMargin'=>80,'unit' => 'pct','align' => 'center');
		$styleFirstRow_Titulo = array('borderBottomSize'=>18, 'borderTopSize'=>18);

		// Define cell style arrays
		$styleCell = array('valign'=>'center');
		$styleCellBTLR = array('valign'=>'center', 'textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR);

		// Define font style for first row
		$fontStyle = array('bold'=>true, 'align'=>'center');

		// Add table style
		$PHPWord->addTableStyle('MyTable', $styleTable_Titulo, $styleFirstRow_Titulo);

		$table_titulo = $section->addTable('MyTable');
		$table_titulo->addRow();
		$table_titulo->addCell(9000,$styleFirstRow2)->addText($titulo,'TitleStyle', 'StyleTitle');

		$section->addTextBreak(1);
		$date;
		$array_obj=Array();
		$cont=0;
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			$cont++;
			$array_obj[$cont]=$subobjetivo->nombre;
		}
		$cont_color=0;
		$array_color=Array();
		$array_datos=array();
		$arrayObjData=Array();
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
			$usuario = Utiles::busca_usuario($subobjetivo->objetivo_id);
			$nombre_obj=$subobjetivo->nombre;
			$sql = "SELECT * FROM reporte.disponibilidad_downtime_global(".
			pg_escape_string($current_usuario_id).",".
			pg_escape_string($subobjetivo->objetivo_id).", ".
			pg_escape_string($this->horario_id).", '".
			pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
			pg_escape_string($this->timestamp->getTerminoPeriodo())."')";

			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
			exit();
			}
			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_downtime_global']);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_downtime_global"]);
			}
			$conf_objetivo= $xpath->query("/atentus/resultados/detalles/detalle/datos/dato");
			$fecha_titulo=substr($this->timestamp->getInicioPeriodo(), 0,10);
			
			$date=explode('-', $fecha_titulo);
			foreach ($meses_anno as $key_mes => $value_mes) {
				if(intval($date[1])==$key_mes){
					$mes= $value_mes;
				}
			}
			$array_dato=Array();
			$date=$date[2].' de '.$mes.' del '.$date[0];
			$arrayDta=Array();
			foreach ($conf_objetivo as $conf_objetivos) {
				$array_dato['nombre']=$nombre_obj;
				$array_dato['inicio']=$conf_objetivos->getAttribute('inicio');
				$array_dato['termino']=$conf_objetivos->getAttribute('termino');
				$array_dato['duracion']=$conf_objetivos->getAttribute('duracion');
				$array_dato['color']=$conf_objetivos->getAttribute('evento_id');
				array_push($arrayDta, $array_dato);
			}
			$arrayData=Array();
			if(sizeof($arrayDta)>0){
				foreach ($arrayDta as $key => $valuedata) {
					if($valuedata["color"]=='2'){
						$array=Array();
						$array["nombre"]=$valuedata["nombre"];
						$array["inicio"]=$valuedata["inicio"];
						$array["termino"]=$valuedata["termino"];
						$array["duracion"]=$valuedata["duracion"];
						$array["color"]='d3222a';
						array_push($arrayData, $array);
					}
				}
			}else{
				$array=Array();
				$array["nombre"]=$subobjetivo->nombre;
				$array["color"]='green';
				array_push($arrayData, $array);
			}
			if(sizeof($arrayData)>0){
				array_push($arrayObjData, $arrayData);
			}
		}
		
		$ArrayObj=Array();
		foreach ($arrayObjData as $key => $value) {
			$arrayDatas=Array();
			foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
				if($value[0]["nombre"]==$subobjetivo->nombre){
					$array=Array();
					$array["nombre"]=$subobjetivo->nombre;
					$array["color"]=$value[0]["color"];
					$array["data"]=$value;
					$arrayDatas=$array;
				}
			}
			array_push($ArrayObj, $arrayDatas);
		}
		$arrayValido=array_chunk($ArrayObj,4);
		
		$PHPWord->addFontStyle('date', array('size'=>8, 'color'=>'333333', 'spaceAfter'=>50));
		$PHPWord->addParagraphStyle('date_1', array('align'=>'right'));
		$PHPWord->addParagraphStyle('text_center', array('align'=>'center'));

		$section->addText("Santiago, ".$date, 'date', 'date_1');
		$section->addTextBreak(1);

		// Define table style arrays
		$styleTable = array('borderSize'=>3, 'borderColor'=>'000000','align' => 'center');

		// Define cell style arrays
		$styleCell = array('align'=>'center');

		// Define font style for first row
		$fontStyle = array('align'=>'center');

		// Add table style
		$PHPWord->addTableStyle('myOwnTableStyle', $styleTable);

		$styleTableData = array('borderSize'=>6, 'borderColor'=>'000000','align' => 'center');
		$PHPWord->addTableStyle('data', $styleTableData);

		$validador_error='false';
		foreach ($arrayValido as $key => $array_obj_estado) {
			$table = $section->addTable('myOwnTableStyle');
			// Add row
			$table->addRow(400);
			$table->addCell(1500, $styleCell)->addText("");
			foreach ($array_obj_estado as $index => $value_objetivos) {
				for ($i=0; $i <4 ; $i++) {
					if($i==$index){
						$table->addCell(2000, $styleCell)->addText($value_objetivos["nombre"],'date', 'text_center');
					}
				}
			}
			$table->addRow(400);
			$table->addCell(1800, $styleCell)->addText($estados, 'date', 'text_center');
			foreach ($array_obj_estado as $index => $value_objetivos) {
				for ($i=0; $i <4 ; $i++) {
					if($i==$index){
						$color=$value_objetivos["color"];
						if($color==='green'){
							$table->addCell(2000)->addImage(REP_PATH_IMG.'done.png', array('width'=>15, 'height'=>9, 'align'=>'center'));
						}else{
							$validador_error='true';
							$table->addCell(2000)->addImage(REP_PATH_IMG.'error.png', array('width'=>15, 'height'=>9, 'align'=>'center'));
						}
					}
				}
			}
			$section->addTextBreak(1);
		}
		$section->addTextBreak(1);

		$table2 = $section->addTable();
		$table2->addRow(600);
		$table2->addCell()->addText("Verde(");
		$table2->addCell()->addImage(REP_PATH_IMG.'done.png', array('width'=>15, 'height'=>9, 'align'=>'center'));
		$table2->addCell()->addText("):  ");
		$table2->addCell(20000000)->addText($descripcion_ok,array('width'=>2000, 'height'=>9, 'align'=>'center'));
		$table2->addRow(600);
		$table2->addCell()->addText("Rojo(");
		$table2->addCell()->addImage(REP_PATH_IMG.'error.png', array('width'=>15, 'height'=>9, 'align'=>'center'));
		$table2->addCell()->addText("):  ");
		$table2->addCell(20000000)->addText($descripcion_error,array('width'=>2000, 'height'=>9, 'align'=>'center'));
		
		$section->addTextBreak(1);

		$table_error = $section->addTable();
		if($validador_error=='true'){
			$table_error->addRow(600);
			$table_error->addCell(20000000)->addText("Detalle de horarios en que no hubo conectividad: ",array('width'=>200000, 'height'=>9, 'align'=>'center'));
			$section->addTextBreak(1);
		}

		// Define table style arrays
		$styleTable2 = array('borderSize'=>1, 'borderColor'=>'000000','align' => 'center');
		$styleFirstRow2 = array( 'bgColor'=>'B7B4B4');
		$normalCellData= array( 'bgColor'=>'eeeeee','color'=>'white');
		// Add table style
		$PHPWord->addTableStyle('myOwnTableStyle2', $styleTable2);

		foreach ($ArrayObj as $key => $value_data) {
			if($value_data["data"][0]["inicio"]!=NULL){
				$objetivos=$value_data["nombre"];
				$table_obj = $section->addTable('myOwnTableStyle2');
				$table_obj->addRow();
				$table_obj->addCell(9000,$styleFirstRow2)->addText($objetivos,'date', 'text_center');
				foreach ($value_data["data"] as $objetivos=> $value) {
					$inicio=$value["inicio"];
					$termino=$value["termino"];
					$duracion=$value["duracion"];
					$table_data = $section->addTable('data');
					$table_data->addRow();
					$table_data->addCell(3000, $styleFirstRow2)->addText("Hora Inicio", 'date', 'text_center');
					$table_data->addCell(3000, $styleFirstRow2)->addText("Hora Termino", 'date', 'text_center');
					$table_data->addCell(3000, $styleFirstRow2)->addText("Duracion", 'date', 'text_center');
					if($duracion=='1 day'){
						$duracion='1 Día';
						$duracion=utf8_decode($duracion);
					}
					$table_mediciones = $section->addTable('data');
					$table_mediciones->addRow();
					$table_mediciones->addCell(3000, $normalCellData)->addText($inicio,'date','text_center');
					$table_mediciones->addCell(3000, $normalCellData)->addText($termino,'date','text_center');
					$table_mediciones->addCell(3000, $normalCellData)->addText($duracion,'date','text_center');
				}
			}
			$section->addTextBreak(1);
		}
		$text = "Este certificado es emitido por Servicios de Monitoreo S.A. (Atentus), que entrega servicios de monitoreo a la Contraloría General de la República, dónde la metodología utilizada se basa en realizar ingresos automatizados mediante robots instalados dentro de los principales ISP (proveedores de Internet) de Chile, simulando la experiencia de un usuario real.";
		$text=utf8_decode($text);
		$PHPWord->addFontStyle('r2Style', array('bold'=>true, 'italic'=>false, 'size'=>10));
		$PHPWord->addParagraphStyle('p2Style', array('align'=>'both', 'spaceAfter'=>100));
		$section->addText($text, 'r2Style', 'p2Style');
		$section->addTextBreak(1);

		
		$PHPWord->addFontStyle('Style', array('bold'=>true, 'italic'=>false, 'size'=>8));
		$section->addText($descripcion_texto, 'Style', 'p2Style');

		$objWriter =  PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
		ob_clean();
		$objWriter->save('php://output');
	}

	function getEspecialDisponibilidadSinMantencion() {
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$estilo_titulo = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '000000')),
				'font' => array('size' => 8, 'bold' => true, 'color' => array('rgb' => 'FFFFFF')),
				'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$estilo_subtitulo = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '929292')),
				'font' => array('size' => 8, 'color' => array('rgb' => 'FFFFFF')),
				'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$estilo_parametro = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'f47001')),
				'font' => array('size' => 8, 'color' => array('rgb' => 'FFFFFF')));
		$estilo_downtime = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '7f0000')),
				'font' => array('size' => 8, 'bold' => true, 'color' => array('rgb' => 'FFFFFF')),
				'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));

		$objetivo = new ConfigEspecial($this->extra["parent_objetivo_id"]);
		$horario = $objetivo->getHorario($this->horario_id);
		$cache = $objetivo->getCacheMantencion();
		$subobjetivo = new ConfigObjetivo($this->objetivo_id);

		$objPHPExcel = new PHPExcel();
		$objWorksheet = $objPHPExcel->getActiveSheet();

		$objWorksheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(8);

		$objWorksheet->getColumnDimension('A')->setWidth(15);
		$objWorksheet->getColumnDimension('B')->setWidth(15);
		$objWorksheet->getColumnDimension('C')->setWidth(15);
		$objWorksheet->getColumnDimension('D')->setWidth(15);
		$objWorksheet->getColumnDimension('E')->setWidth(15);
		$objWorksheet->getColumnDimension('F')->setWidth(15);

		$objWorksheet->getStyle('A1')->getFont()->setBold(true);
		$objWorksheet->getStyle('A3:A6')->applyFromArray($estilo_parametro);
		$objWorksheet->setCellValueByColumnAndRow(0, 1, $objetivo->nombre);
		$objWorksheet->setCellValueByColumnAndRow(0, 3, "Objetivo");
		$objWorksheet->setCellValueByColumnAndRow(0, 4, "Horario");
		$objWorksheet->setCellValueByColumnAndRow(0, 5, "Fecha");
		$objWorksheet->setCellValueByColumnAndRow(0, 6, "Consideraciones");
		$objWorksheet->setCellValueByColumnAndRow(1, 3, $subobjetivo->nombre);
		$objWorksheet->setCellValueByColumnAndRow(1, 4, $horario->nombre);
		$objWorksheet->setCellValueByColumnAndRow(1, 5, $this->timestamp->getInicioPeriodo("d-m-Y H:i:s")." hasta ".$this->timestamp->getTerminoPeriodo("d-m-Y H:i:s"));
		$objWorksheet->setCellValueByColumnAndRow(1, 6, "En este informe se considera Uptime = Uptime + Downtime Parcial + No Monitoreo");

		$horarios = array($horario);
		if ($this->horario_id != 0) {
			$horarios[] = $objetivo->getHorario(0);
			$horarios[($this->horario_id*-1)] = new Horario(($this->horario_id*-1));
			$horarios[($this->horario_id*-1)]->nombre = "Horario Inhabil";
		}

		$total_diff = array();
		$tiene_mantenciones = false;

		foreach ($horarios as $horario) {

			if ($horario->horario_id < "0") {
				continue;
			}

			/* OBTENER LOS DATOS Y PARSEARLO */
			$sql = "SELECT * FROM reporte.disponibilidad_downtime_global(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					pg_escape_string($horario->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
//			print($sql);

			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				continue;
			}
			$row = $res->fetchRow();

			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["disponibilidad_downtime_global"]);
			$xpath = new DOMXpath($dom);

			$conf_pasos = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$this->objetivo_id."]/paso[@visible=1]");

			if ($horario->horario_id == $this->horario_id) {
				$fila = (($conf_pasos->length + 6) * count($horarios)) + 9;
			}

			foreach ($conf_pasos as $conf_paso) {
				$total_diff[$horario->horario_id][$conf_paso->getAttribute("paso_orden")] = 0;

				$tag_paso = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$this->objetivo_id."]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]")->item(0);

				if ($horario->horario_id == $this->horario_id) {
					$objWorksheet->mergeCells('A'.$fila.':F'.$fila);
					$objWorksheet->getStyle('A'.$fila.':F'.$fila)->applyFromArray($estilo_downtime);
					$objWorksheet->setCellValueByColumnAndRow(0, $fila, $conf_paso->getAttribute("nombre"));
					$fila++;

					$objWorksheet->getStyle('A'.$fila.':F'.$fila)->applyFromArray($estilo_subtitulo);
					$objWorksheet->setCellValueByColumnAndRow(0, $fila, "Fecha");
					$objWorksheet->setCellValueByColumnAndRow(1, $fila, "Inicio");
					$objWorksheet->setCellValueByColumnAndRow(2, $fila, "Termino");
					$objWorksheet->setCellValueByColumnAndRow(3, $fila, "Intervalo");
					$objWorksheet->setCellValueByColumnAndRow(4, $fila, "Mantención");
					$objWorksheet->setCellValueByColumnAndRow(5, $fila, "Descripción");
					$fila++;
				}

				foreach ($xpath->query("datos/dato[@evento_id=2]", $tag_paso) as $dato_paso) {
					$mantencion_nombre = "";
					$mantencion_descripcion = "";

					if (isset($cache[$this->objetivo_id][$tag_paso->getAttribute("paso_orden")])) {
						$inicio = "'".pg_escape_string($dato_paso->getAttribute("inicio"))."'::TIMESTAMP WITHOUT TIME ZONE";
						$termino = "'".pg_escape_string($dato_paso->getAttribute("termino"))."'::TIMESTAMP WITHOUT TIME ZONE";
						$horario_ids = pg_escape_string(implode(",", $cache[$this->objetivo_id][$tag_paso->getAttribute("paso_orden")]));

						$sql = "SELECT h.nombre, i.descripcion, ".
								"CASE WHEN fecha_inicio + hora_inicio < $inicio THEN $inicio ELSE fecha_inicio + hora_inicio END AS fecha_inicio, ".
								"CASE WHEN fecha_termino + hora_termino > $termino THEN $termino ELSE fecha_termino + hora_termino END AS fecha_termino ".
								"FROM public.horario_item i, public.horario h ".
								"WHERE h.horario_id IN ($horario_ids) AND ".
								"h.horario_id = i.horario_id AND ".
								"($inicio, $termino) OVERLAPS (fecha_inicio + hora_inicio, fecha_termino + hora_termino) ".
								"ORDER BY fecha_inicio, fecha_termino";

						$res = & $mdb2->query($sql);

						if (MDB2::isError($res)) {
							continue;
						}

						$diff = 0;
						$fi_anterior = strtotime($dato_paso->getAttribute("inicio"));
						$ft_anterior = strtotime($dato_paso->getAttribute("inicio"));
						while ($row = $res->fetchRow()) {
							$tiene_mantenciones = true;
							$mantencion_nombre = $row["nombre"];
							$mantencion_descripcion = $row["descripcion"];
							$fi = strtotime($row["fecha_inicio"]);
							$ft = strtotime($row["fecha_termino"]);
							if ($ft < $ft_anterior) {
								continue;
							}
							elseif ($fi < $ft_anterior) {
								$fi = $ft_anterior;
							}
							$fi_anterior = $fi;
							$ft_anterior = $ft;
							$diff += $ft - $fi;
						}
						$total_diff[$horario->horario_id][$tag_paso->getAttribute("paso_orden")] += $diff;
						//                                                $objWorksheet->setCellValueByColumnAndRow(2, $fila, $diff);
					}
					if ($horario->horario_id == $this->horario_id) {
						$objWorksheet->getStyle('A'.$fila.':D'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objWorksheet->setCellValueByColumnAndRow(0, $fila, date("d-m-Y", strtotime($dato_paso->getAttribute("inicio"))));
						$objWorksheet->setCellValueByColumnAndRow(1, $fila, date("H:i:s", strtotime($dato_paso->getAttribute("inicio"))));
						$objWorksheet->setCellValueByColumnAndRow(2, $fila, (date("H:i:s", strtotime($dato_paso->getAttribute("termino"))) == "00:00:00")?"24:00:00":date("H:i:s", strtotime($dato_paso->getAttribute("termino"))));
						$objWorksheet->setCellValueByColumnAndRow(3, $fila, ($dato_paso->getAttribute("duracion") == "1 day")?"24:00:00":$dato_paso->getAttribute("duracion"));
						$objWorksheet->setCellValueByColumnAndRow(4, $fila, $mantencion_nombre);
						$objWorksheet->setCellValueByColumnAndRow(5, $fila, $mantencion_descripcion);
						$fila++;
					}
				}
				if ($horario->horario_id == $this->horario_id) {
					$fila = $fila + 2;
				}
			}
		}

		$fila = 9;
		foreach ($horarios as $horario) {
			$objWorksheet->mergeCells('A'.$fila.(($tiene_mantenciones)?':E':':C').$fila);
			$objWorksheet->getStyle('A'.$fila)->applyFromArray($estilo_titulo);
			$objWorksheet->setCellValueByColumnAndRow(0, $fila, $horario->nombre);
			$fila++;

			$objWorksheet->mergeCells('B'.$fila.':C'.$fila);
			if ($tiene_mantenciones) {
				$objWorksheet->mergeCells('D'.$fila.':E'.$fila);
			}
			$objWorksheet->getStyle('B'.$fila.':E'.$fila)->getFont()->setBold(true);
			$objWorksheet->getStyle('B'.$fila.':E'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objWorksheet->setCellValueByColumnAndRow(1, $fila, "Normal Atentus [%]");
			if ($tiene_mantenciones) {
				$objWorksheet->setCellValueByColumnAndRow(3, $fila, "Con Mantención [%]");
			}
			$fila++;

			$objWorksheet->getStyle('A'.$fila.(($tiene_mantenciones)?':E':':C').$fila)->applyFromArray($estilo_subtitulo);
			$objWorksheet->setCellValueByColumnAndRow(0, $fila, "Paso");
			$objWorksheet->setCellValueByColumnAndRow(1, $fila, "Uptime");
			$objWorksheet->setCellValueByColumnAndRow(2, $fila, "Downtime");
			if ($tiene_mantenciones) {
				$objWorksheet->setCellValueByColumnAndRow(3, $fila, "Uptime");
				$objWorksheet->setCellValueByColumnAndRow(4, $fila, "Downtime");
			}
			$fila++;

			$sql = "SELECT * FROM reporte.disponibilidad_resumen_consolidado(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					pg_escape_string($horario->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."', 0)";

//			echo($sql);
			$res = & $mdb2->query($sql);
			if (MDB2::isError($res)) {
				continue;
			}

			$row = $res->fetchRow();

			$dom = new DomDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($row["disponibilidad_resumen_consolidado"]);
			$xpath = new DOMXpath($dom);

			$conf_pasos = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo[@objetivo_id=".$this->objetivo_id."]/paso[@visible=1]");

			foreach ($conf_pasos as $conf_paso) {
				$paso_id = $conf_paso->getAttribute("paso_orden");

				$tag_paso = $xpath->query("/atentus/resultados/detalles/detalle[@objetivo_id=".$this->objetivo_id."]/detalles/detalle[@nodo_id=0]/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]")->item(0);

				$uptime = 0;
				$uptime_tiempo = 0;
				$downtime = 0;
				$downtime_tiempo = 0;
				$total_tiempo = 0;
				foreach ($xpath->query("estadisticas/estadistica", $tag_paso) as $dato_paso) {
					$total_tiempo += Utiles::intervalToSeconds($dato_paso->getAttribute("duracion"));
					if (in_array($dato_paso->getAttribute("evento_id"), array("1", "3", "7"))) {
						$uptime += $dato_paso->getAttribute("porcentaje");
						$uptime_tiempo += Utiles::intervalToSeconds($dato_paso->getAttribute("duracion"));
					}
					if ($dato_paso->getAttribute("evento_id") == 2) {
						$downtime = $dato_paso->getAttribute("porcentaje");
						$downtime_tiempo = Utiles::intervalToSeconds($dato_paso->getAttribute("duracion"));
					}
				}

				if ($total_tiempo == 0 or ($total_diff[$horario->horario_id][$paso_id] == 0 and $horario->horario_id != "-1") or ($horario->horario_id == "-1" and $total_diff[0][$paso_id] == $total_diff[$this->horario_id][$paso_id])) {
					$uptime_man = $uptime;
					$downtime_man = $downtime;
				}
				elseif ($horario->horario_id == "-1") {
					$uptime_man = ($uptime_tiempo + ($total_diff[0][$paso_id] - $total_diff[$this->horario_id][$paso_id])) * 100 / $total_tiempo;
					$downtime_man = ($downtime_tiempo - ($total_diff[0][$paso_id] - $total_diff[$this->horario_id][$paso_id])) * 100 / $total_tiempo;
				}
				else {
					$uptime_man = ($uptime_tiempo + $total_diff[$horario->horario_id][$paso_id]) * 100 / $total_tiempo;
					$downtime_man = ($downtime_tiempo - $total_diff[$horario->horario_id][$paso_id]) * 100 / $total_tiempo;
				}

				$objWorksheet->getStyle("B".$fila.':F'.$fila)->getNumberFormat()->setFormatCode('0.00');
				$objWorksheet->setCellValueByColumnAndRow(0, $fila, $conf_paso->getAttribute("nombre"));
				$objWorksheet->setCellValueByColumnAndRow(1, $fila, number_format($uptime, 2, '.', ''));
				$objWorksheet->setCellValueByColumnAndRow(2, $fila, number_format($downtime, 2, '.', ''));
				if ($tiene_mantenciones) {
					$objWorksheet->setCellValueByColumnAndRow(3, $fila, number_format($uptime_man, 2, '.', ''));
					$objWorksheet->setCellValueByColumnAndRow(4, $fila, number_format($downtime_man, 2, '.', ''));
				}
				$fila++;
			}

			$objWorksheet->getStyle("B".$fila.':F'.$fila)->getNumberFormat()->setFormatCode('0.00');
			$objWorksheet->getStyle('B'.$fila.':E'.$fila)->getFont()->setBold(true);
			$objWorksheet->setCellValueByColumnAndRow(1, $fila, '=AVERAGE(B'.($fila - $conf_pasos->length).':B'.($fila - 1).')');
			$objWorksheet->setCellValueByColumnAndRow(2, $fila, '=AVERAGE(C'.($fila - $conf_pasos->length).':C'.($fila - 1).')');
			if ($tiene_mantenciones) {
				$objWorksheet->setCellValueByColumnAndRow(3, $fila, '=AVERAGE(D'.($fila - $conf_pasos->length).':D'.($fila - 1).')');
				$objWorksheet->setCellValueByColumnAndRow(4, $fila, '=AVERAGE(E'.($fila - $conf_pasos->length).':E'.($fila - 1).')');
			}
			$fila = $fila + 3;
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}	

	function getEspecialRendimientoResumen() {
	    global $mdb2;
	    global $log;
	    global $current_usuario_id;
	    $array_monitor =array();
	    $objetivo = new ConfigEspecial($this->objetivo_id);
	  
	    
	    foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
	        
	        /* OBTENER LOS DATOS Y PARSEARLO */
	        $sql = "SELECT * FROM reporte.rendimiento_especial_global(".
	   	        pg_escape_string($current_usuario_id).",".
	   	        pg_escape_string($subobjetivo->objetivo_id).", '".
	   	        pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
	   	        pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
// 	   	        print($sql).'<br>';
	   	        
	   	        $res = & $mdb2->query($sql);
	   	        if (MDB2::isError($res)) {
	   	            continue;
	   	        }
	   	       
	   	        if($row = $res->fetchRow()){
    	   	        $dom = new DomDocument();
    	   	        $dom->preserveWhiteSpace = FALSE;
    	   	        $dom->loadXML($row["rendimiento_especial_global"]);
    	   	        $xpath = new DOMXpath($dom);
	   	        }
	   	       
	   	        $conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
	   	        $conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
	   	        $conf_nodos = $xpath->query('/atentus/resultados/propiedades/nodos/nodo[@nodo_id!=0]');
	   	        
	   	        $objetivo_id= $conf_objetivo->getAttribute('objetivo_id');
	   	        
	   	        foreach ($conf_nodos as $conf_nodo){
	   	            $monitor_id= $conf_nodo->getAttribute('nodo_id');
	   	            $tag_nodo= $xpath->query("//detalles/detalle[@objetivo_id=".$objetivo_id."]/detalles/detalle[@nodo_id=".$monitor_id."]")->item(0);
	   	            
	   	            if(!isset($array_monitor[$monitor_id])){
	   	               $array_monitor[$monitor_id]['nombre']=$conf_nodo->getAttribute('nombre');
	   	            }
	   	            $array_monitor[$monitor_id]['objetivos'][$objetivo_id]['nombre']=$conf_objetivo->getAttribute('nombre');
	   	            $array_monitor[$monitor_id]['objetivos'][$objetivo_id]['servicio']=$conf_objetivo->getAttribute('servicio');
	   	            
	   	            foreach ($conf_pasos as $conf_paso){
	   	                $paso =$conf_paso->getAttribute('paso_orden');
	   	                $total_reg= $xpath->query("detalles/detalle[@paso_orden=".$paso."]/datos/dato",$tag_nodo)->length;
	   	                $path_estado = "detalles/detalle[@paso_orden=".$paso."]/datos/dato[@estado=0]";
	   	                $path_dato = "detalles/detalle[@paso_orden=".$paso."]/datos/dato/@tiempo";
	   	                
	   	                $cant_disp =$xpath->evaluate("count(".$path_estado.")",$tag_nodo);
	   	                $sum =$xpath->evaluate("sum(".$path_dato.")",$tag_nodo);
	   	                
	   	                $array_monitor[$monitor_id]['objetivos'][$objetivo_id]['pasos'][$paso]['nombre']=$conf_paso->getAttribute('nombre');
	   	                $array_monitor[$monitor_id]['objetivos'][$objetivo_id]['pasos'][$paso]['total']=$total_reg;
	   	                $array_monitor[$monitor_id]['objetivos'][$objetivo_id]['pasos'][$paso]['ok']=$cant_disp;
	   	                $array_monitor[$monitor_id]['objetivos'][$objetivo_id]['pasos'][$paso]['respuesta']=$sum;
	   	            }   
	   	        }
	    }

	    $estilo_parametro = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'f47001')),
	        'font' => array('size' => 8, 'color' => array('rgb' => 'FFFFFF')));
	    $estilo_subtitulo = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '929292')),
	        'font' => array('size' => 8, 'color' => array('rgb' => 'FFFFFF')));
	    
	   
	    $objPHPExcel = new PHPExcel();
	    $objWorksheet = $objPHPExcel->getActiveSheet();
	    $objWorksheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(8);
	    $objWorksheet->getColumnDimension('A')->setWidth(20);
	    $objWorksheet->getColumnDimension('B')->setWidth(20);
	    $objWorksheet->getColumnDimension('C')->setWidth(20);
	    $objWorksheet->getColumnDimension('D')->setWidth(20);
	    
	    
	    
	    $filasede = 2;
	    foreach ($array_monitor as $conf_nodo){
	        $objWorksheet->setCellValueByColumnAndRow(1, $filasede, $conf_nodo['nombre']);
	        $filasede++;
	    }
	    $filadetalle=$filasede+2;
	    
	    $objWorksheet->getStyle('A1')->getFont()->setBold(true);
	    $objWorksheet->getStyle('A'.$filadetalle)->getFont()->setBold(true);
	    $objWorksheet->getStyle('A'.$filasede)->getFont()->setBold(true);
	    $objWorksheet->getStyle('A1:D1')->applyFromArray($estilo_parametro);
	    $objWorksheet->getStyle('A'.$filasede.':D'.$filasede)->applyFromArray($estilo_parametro);
	    $objWorksheet->getStyle('A'.$filadetalle.':D'.$filadetalle)->applyFromArray($estilo_parametro);
	    
	    $objWorksheet->setCellValueByColumnAndRow(0, 1, "Promedio General");
	    $objWorksheet->setCellValueByColumnAndRow(1, 1, "Sede");
	    $objWorksheet->setCellValueByColumnAndRow(2, 1, "T. Respuesta");
	    $objWorksheet->setCellValueByColumnAndRow(3, 1, "Eficiencia");
	    
	    $objWorksheet->setCellValueByColumnAndRow(0, $filasede, "Total");
	    
	    $objWorksheet->setCellValueByColumnAndRow(0, $filadetalle, "Sede");
	    $objWorksheet->setCellValueByColumnAndRow(1, $filadetalle, "Servicio");
	    $objWorksheet->setCellValueByColumnAndRow(2, $filadetalle, "T. Respuesta");
	    $objWorksheet->setCellValueByColumnAndRow(3, $filadetalle, "Eficiencia");
	    
	    $filatitulo = $filadetalle+1;
	    $filaobjetivo = $filadetalle+1;
	    $cant_nodos =count($array_monitor);
	    $eficiencia_gbl =0.0;
	    $respuesta_gbl =0.0;
	    $filasede = 2;
	    foreach ($array_monitor as $conf_nodo){
	        $eficiencia_nodo =0.0;
	        $respuesta_nodo =0.0;
	        $cant_objetivos =count($conf_nodo['objetivos']);
	        $objWorksheet->setCellValueByColumnAndRow(0, $filatitulo, $conf_nodo['nombre']);
	        
	        foreach ($conf_nodo['objetivos'] as $conf_objetivo){
	            $eficiencia_obj =0;
	            $respuesta_obj =0;
	            
	            $objWorksheet->getStyle('B'.$filaobjetivo.':D'.$filaobjetivo)->applyFromArray($estilo_subtitulo);
	            $objWorksheet->setCellValueByColumnAndRow(1, $filaobjetivo, $conf_objetivo['nombre']);
	            $cant_pasos = count($conf_objetivo['pasos']);
	            
	            $filapaso = $filaobjetivo+1;
	            foreach ($conf_objetivo['pasos'] as $conf_paso){
	                
	                $eficiencia= number_format(($conf_paso['ok']*100)/$conf_paso['total'], 2, ',','')."%";
	                $respuesta =  number_format($conf_paso['respuesta']/ $conf_paso['total'], 2, '.','');
	                
	                $objWorksheet->setCellValueByColumnAndRow(1, $filapaso, $conf_paso['nombre']);
	                $objWorksheet->setCellValueByColumnAndRow(2, $filapaso,$respuesta);
	                $objWorksheet->setCellValueByColumnAndRow(3, $filapaso, $eficiencia);
	                
	                $eficiencia_obj += ($conf_paso['ok']*100)/$conf_paso['total'];
	                $respuesta_obj += $respuesta;
	                
	                $filapaso++;
	            }
	            $eficiencia_nodo+=$eficiencia_obj/$cant_pasos;
	            $respuesta_nodo+=$respuesta_obj/$cant_pasos;
	            
	            $objWorksheet->setCellValueByColumnAndRow(2, $filaobjetivo, number_format(($respuesta_obj/$cant_pasos), 2, '.',''));
	            $objWorksheet->setCellValueByColumnAndRow(3, $filaobjetivo,number_format(($eficiencia_obj/$cant_pasos), 2, ',','')."%");
	            
	            
	            $filaobjetivo=$filapaso;
    	        
	        }
	        
	        $eficiencia_gbl+=($eficiencia_nodo/$cant_objetivos);
	        $respuesta_gbl+=($respuesta_nodo/$cant_objetivos);
	        
	        $objWorksheet->setCellValueByColumnAndRow(2,  $filasede, number_format(($respuesta_nodo/$cant_objetivos), 2, '.',''));
	        $objWorksheet->setCellValueByColumnAndRow(3, $filasede,number_format(($eficiencia_nodo/$cant_objetivos), 2, ',','')."%");
	        $filasede++;
	        $filatitulo=$filaobjetivo;
	    }
	    
	    $objWorksheet->setCellValueByColumnAndRow(2,  $filadetalle-2, number_format(($respuesta_gbl/$cant_nodos), 2, '.',''));
	    $objWorksheet->setCellValueByColumnAndRow(3, $filadetalle-2,number_format(($eficiencia_gbl/$cant_nodos), 2, ',','')."%");
	    
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	    $objWriter->save('php://output');
	}

	/*
	 Creado por: Francisco Ormeño
	 Modificado por: --
	 Fecha de creacion:14/02/2018
	 Fecha de modificacion: --
	 */
	function getEspecialAndreani(){
	    global $mdb2;
	    global $log;
	    global $current_usuario_id;
	    global $usr;
	     
	    $border=array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' => '000000'))));
	    
	    $style_title_sheet = array('font' => array('bold'  => true,'size' => '14','name'  => 'Calibri'));
	    $font_style_title = array('font' => array('color' => array('rgb' => 'FFFFFF'),'name'  => 'Calibri'));
	    
	    $style_title_table = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '5b5b5b')),
	        'font' => array('size' => 14,'bold'  => true),
	        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
	    
	    $estilo_mes = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '5b5b5b')),
	        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
	        'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' => '5b5b5b'))));
	    

	    $objetivo = new ConfigEspecial($this->objetivo_id);
	    $cant_objetivos= count($objetivo->getSubobjetivos());//Se cuentan los objetivos para poder setear bien los estilos de las tablas.
	    
	    $objPHPExcel = new PHPExcel();
	    $objWorksheet = $objPHPExcel->createSheet();
	    
	    $objWorksheet->getSheetView()->setZoomScale(80);
	    $this->timestamp->tipo_periodo =REP_PRD_MONTH;
	    $mes =$this->timestamp->getFormatoFecha($this->timestamp->getInicioPeriodo());
	    
	    $objWorksheet->getColumnDimension('A')->setWidth(5);
	    $objWorksheet->getColumnDimension('B')->setWidth(40);
	    $objWorksheet->getColumnDimension('C')->setWidth(50);
	    $objWorksheet->getColumnDimension('D')->setWidth(2);
	    $objWorksheet->getColumnDimension('E')->setWidth(10);
	    $objWorksheet->getRowDimension('2')->setRowHeight(20);
	    $objWorksheet->getRowDimension('4')->setRowHeight(120);
	    
	    $objWorksheet->mergeCells("E3:M3")->getStyle('E3:M3')->applyFromArray($estilo_mes);
	    $objWorksheet->getStyle('A2')->applyFromArray($style_title_sheet);
	    
	    $objWorksheet->getStyle('A4:C4')->applyFromArray($font_style_title)->applyFromArray($style_title_table)->applyFromArray($border);
	    $objWorksheet->getStyle('E4:M4')->applyFromArray($font_style_title)->getAlignment()->setTextRotation(90)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
	    $objWorksheet->getStyle('E3')->applyFromArray($font_style_title);
	    $objWorksheet->getStyle('E4:F4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('54a51c');
	    $objWorksheet->getStyle('G4:H4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('fdc72e');
	    $objWorksheet->getStyle('I4:J4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d22129');
	    $objWorksheet->getStyle('K4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('a2a2a2');
	    $objWorksheet->getStyle('L4:M4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('de7506'); 
	    
	    $objWorksheet->setCellValueByColumnAndRow(0,2,'Objetivos Web - Disponibilidad y Performance ');
	    $objWorksheet->setCellValueByColumnAndRow(0,4,'#');
	    $objWorksheet->setCellValueByColumnAndRow(1,4,'Objetivo');
	    $objWorksheet->setCellValueByColumnAndRow(2,4,'URL');
	    $objWorksheet->setCellValueByColumnAndRow(4,3,$mes);
	    $objWorksheet->setCellValueByColumnAndRow(4, 4,'  Disponible [%] (1)');
	    $objWorksheet->setCellValueByColumnAndRow(5, 4,'  Variación respecto Mes Anterior (%)');
	    $objWorksheet->setCellValueByColumnAndRow(6, 4,'  Disponible Parcial  [%]');
	    $objWorksheet->setCellValueByColumnAndRow(7, 4,'  Variación respecto Mes Anterior (%)');
	    $objWorksheet->setCellValueByColumnAndRow(8, 4,'  No Disponible [%]');
	    $objWorksheet->setCellValueByColumnAndRow(9, 4,'  Variación respecto Mes Anterior (%)');
	    $objWorksheet->setCellValueByColumnAndRow(10, 4,'  No Monitoreo [%]');
	    $objWorksheet->setCellValueByColumnAndRow(11, 4,'  Tiempo  Respuesta Promedio [Seg.]');
	    $objWorksheet->setCellValueByColumnAndRow(12, 4,'  Variación respecto mes anterior (Seg.)');
        
	    $objWorksheet->getStyle('A4:C'.(4+$cant_objetivos))->applyFromArray($border);
	    $objWorksheet->getStyle('E4:M'.(4+$cant_objetivos))->applyFromArray($border);
	    $numero=1;
	    foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
	        /* OBTENER LOS DATOS Y PARSEARLO */
	        $sql = "SELECT * FROM reporte.disponibilidad_consolidado_especial(".
	   	        pg_escape_string($current_usuario_id).",".
	   	        pg_escape_string($subobjetivo->objetivo_id).", ".
	   	        pg_escape_string($this->horario_id).",' ".
	   	        pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
	   	        pg_escape_string($this->timestamp->getTerminoPeriodo())."', ".
	   	        (isset($this->extra["variable"])?$usr->cliente_id:'0').")";
	   	        //     	        echo $sql;
	   	        $res = & $mdb2->query($sql);
	   	        if (MDB2::isError($res)) {
	   	            $log->setError($sql, $res->userinfo);
	   	            exit();
	   	        }
	   	        
	   	        if ($row = $res->fetchRow()) {
	   	            $dom = new DomDocument();
	   	            $dom->preserveWhiteSpace = FALSE;
	   	            $dom->loadXML($row['disponibilidad_consolidado_especial']);
	   	            $xpath = new DOMXpath($dom);
	   	            unset($row["disponibilidad_consolidado_especial"]);
	   	        }
	   	        
	   	        $objetivo = new ConfigObjetivo($subobjetivo->objetivo_id);
	   	        
	   	        $conf_objetivo= $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
	   	        $conf_paso_0 = $xpath->query("paso[@paso_orden=0]", $conf_objetivo)->item(0);
	   	        $conf_eventos=$xpath->query("//eventos/evento[@evento_id!=9]");
	   	        $paso=$objetivo->__pasos[$conf_paso_0->getAttribute('paso_orden')]->__setups;
	   	        $url=$paso[0]->url;
	   	        
	   	        $objWorksheet->setCellValueByColumnAndRow(0,4+$numero,$numero);
	   	        $objWorksheet->setCellValueByColumnAndRow(1,4+$numero,$conf_objetivo->getAttribute('nombre'));
	   	        $objWorksheet->setCellValueByColumnAndRow(2,4+$numero,isset($url)?$url:"N/A");
	   	        
	   	        $tag_nodo = $xpath->query("/atentus/resultados/detalles/detalle/detalles/detalle[@nodo_id=0]")->item(0);
	   	        foreach ($conf_eventos as $conf_evento){
	   	            $conf_estadistica=$xpath->query("estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute('evento_id')."]", $tag_nodo)->item(0);
	   	           
	   	            if($conf_evento->getAttribute('evento_id')==1){
	   	                $col = 4;
	   	            }elseif ($conf_evento->getAttribute('evento_id')==3){
	   	                $col = 6;
	   	            }elseif ($conf_evento->getAttribute('evento_id')==2){
	   	                $col = 8;
	   	            }elseif ($conf_evento->getAttribute('evento_id')==7){
	   	                $col = 10;
	   	            }
	   	            $porcentaje = isset($conf_estadistica)?$conf_estadistica->getAttribute('porcentaje'):'0.0';
	   	            if ($conf_evento->getAttribute('evento_id')!=7){
	   	                $diferencia = isset($conf_estadistica)?$conf_estadistica->getAttribute('diferencia_mes_pasado'):'0.0';
	   	            }
	   	            $objWorksheet->setCellValueByColumnAndRow($col,4+$numero,$porcentaje);
	   	            $objWorksheet->setCellValueByColumnAndRow($col+1,4+$numero,$diferencia);
	   	           
	   	            
	   	        }
	   	        
	   	        $objWorksheet->setCellValueByColumnAndRow(11,4+$numero,number_format($tag_nodo->getAttribute('mes_actual'), 2, '.', ''));
	   	        $objWorksheet->setCellValueByColumnAndRow(12,4+$numero,number_format(($tag_nodo->getAttribute('mes_actual')-$tag_nodo->getAttribute('mes_anterior')), 2, '.', ''));
	   	        
	   	        $numero++;
	    }
	    $objWorksheet->setCellValueByColumnAndRow(0, 5+$numero,'(1) Se incluye el % de No Monitoreo');
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	    $objWriter->save('php://output');
	}
	
	public function getDatosBancoChile(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		$objPHPExcel = new PHPExcel();
		
		$objetivo = new ConfigEspecial($this->objetivo_id);
		
	
		$sheetid =1;
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
		
			$objWorksheet = $objPHPExcel->createSheet();
			$objWorksheet->getDefaultStyle()->getFont()->setName('Liberation Sans')->setSize(10);
			$objWorksheet->setTitle(substr(str_replace(array("https://","http://","[","]","\\","/",":"), array("","","(",")"," "," ",""), $subobjetivo->nombre), 0, 30));
			$objWorksheet->getColumnDimension('A')->setWidth(40);
			$objWorksheet->getColumnDimension('B')->setWidth(20);
		
			$objPHPExcel->setActiveSheetIndex($sheetid);
			$sheetid++;
			
			/* OBTENER LOS DATOS Y PARSEARLO*/ 
			$sql = "SELECT * FROM reporte.disponibilidad_por_dia(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($subobjetivo->objetivo_id).", ".
					pg_escape_string($this->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
// 			print($sql);exit;
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
				
			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_por_dia']);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_por_dia"]);
			}	
				
			
			
			$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
			$conf_fechas = $xpath->query("//detalles/detalle[1]/detalles/detalle[@fecha]");
			$conf_eventos=$xpath->query("//eventos/evento");
			


			$objWorksheet->setCellValueByColumnAndRow(0, 1, "Disponibilidad ".$conf_objetivo->getAttribute('nombre'));
			$objWorksheet->setCellValueByColumnAndRow(0, 3, "SLA Sitio Web");
			$objWorksheet->setCellValueByColumnAndRow(0, 4, "SLA Ok");
			$objWorksheet->setCellValueByColumnAndRow(0, 5, "SLA Normal");
			$objWorksheet->setCellValueByColumnAndRow(0, 6, "SLA Error");
			$objWorksheet->setCellValueByColumnAndRow(0, 8, "Los valores considerados corresponden a:");

			$objWorksheet->setCellValueByColumnAndRow(1, 4, "Superior a ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_ok'), 2, '.', '')."%");
			$objWorksheet->setCellValueByColumnAndRow(1, 5, "Entre ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_error'), 2, '.', '')."% y ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_ok'), 2, '.', '')."%");
			$objWorksheet->setCellValueByColumnAndRow(1, 6, "Inferior a ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_error'), 2, '.', '')."%");
			
			
			
			if ($conf_fechas->length > 0) {
				
				if($this->extra['evento_id']==0){
					$indice=9;
					foreach ($conf_eventos as $conf_evento){
						
						$objWorksheet->setCellValueByColumnAndRow(0, $indice, $conf_evento->getAttribute('nombre'));
						$indice++;
					}
				}else{
					$objWorksheet->setCellValueByColumnAndRow(0, 9, $xpath->query("//eventos/evento[@evento_id=".$this->extra['evento_id']."]")->item(0)->getAttribute('nombre'));
				}
				
				$objWorksheet->setCellValueByColumnAndRow(0, 14, "Promedio Por Paso");
				$objWorksheet->setCellValueByColumnAndRow(0, $conf_pasos->length + 15, "Promedio Global");
 				
				$objWorksheet->setCellValueByColumnAndRow(0, $conf_pasos->length + 17, "Promedio Por Fecha");
				$objWorksheet->setCellValueByColumnAndRow(0, $conf_pasos->length + 18, "Pasos / Fechas");
				
				$fecha=1;
				foreach ($conf_fechas as $conf_fecha) {
					$objWorksheet->setCellValueByColumnAndRow($fecha, $conf_pasos->length + 18, $this->timestamp->getFormatearFecha($conf_fecha->getAttribute('fecha'), "d-m-Y"));
					$fecha++;
				}
				
				$promedio_total=0;
				$fpasoprom = 15;
				$fpaso = $conf_pasos->length + 19;
				
				foreach ($conf_pasos as $conf_paso) {
					$porcentaje = 0;
					$cpaso = 1;
					$promedio = 0;
					$tag_fechas = $xpath->query("//detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/detalles/detalle");
					
					foreach ($tag_fechas as $tag_fecha) {
					
						$porcentaje_fecha=0;
						$porcentaje = 0;
						$objWorksheet->setCellValueByColumnAndRow(0, $fpasoprom, $conf_paso->getAttribute('nombre'));
						$objWorksheet->setCellValueByColumnAndRow(0, $fpaso, $conf_paso->getAttribute('nombre'));
						if ($this->extra["evento_id"] == 0) {
							foreach ($xpath->query("estadisticas/estadistica",$tag_fecha) as $tag_dato) {
								$porcentaje_fecha += $tag_dato->getAttribute('porcentaje');
								$porcentaje += $tag_dato->getAttribute('porcentaje');
							}
								
						}else {
							$tag_datos = $xpath->query("estadisticas/estadistica[@evento_id=".$this->extra["evento_id"]."]",$tag_fecha);
							foreach ($tag_datos as $tag_dato) {
								$porcentaje_fecha = $tag_dato->getAttribute('porcentaje');
								$porcentaje += $tag_dato->getAttribute('porcentaje');
							}
								
						}
						$objWorksheet->setCellValueByColumnAndRow($cpaso, $fpaso, number_format($porcentaje_fecha, 2, '.', '').'%');
						$promedio += $porcentaje;
						
						$cpaso++;
					}
					
					$promedio = $promedio / $conf_fechas->length;
					$promedio_total += $promedio;
					$objWorksheet->setCellValueByColumnAndRow(1, $fpasoprom,number_format($promedio, 2, '.', '').'%');
					
					$fpasoprom++;
					$fpaso++;
				}
				$promedio_total = $promedio_total / $conf_pasos->length;
				$objWorksheet->setCellValueByColumnAndRow(1, $conf_pasos->length + 15, number_format($promedio_total, 2, '.', '').'%');
			}
		}
		
		$objPHPExcel->removeSheetByIndex(0);
		$objPHPExcel->setActiveSheetIndex(0);
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
	
	
	public function getDatosSII(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
		$objPHPExcel = new PHPExcel();
	
		$objetivo = new ConfigEspecial($this->objetivo_id);
	
	
		$sheetid =1;
		foreach ($objetivo->getSubobjetivos() as $subobjetivo) {
	
			$objWorksheet = $objPHPExcel->createSheet();
			$objWorksheet->getDefaultStyle()->getFont()->setName('Liberation Sans')->setSize(10);
			$objWorksheet->setTitle(substr(str_replace(array("https://","http://","[","]","\\","/",":"), array("","","(",")"," "," ",""), $subobjetivo->nombre), 0, 30));
			$objWorksheet->getColumnDimension('A')->setWidth(40);
			$objWorksheet->getColumnDimension('B')->setWidth(20);
	
			$objPHPExcel->setActiveSheetIndex($sheetid);
			$sheetid++;
				
			/* OBTENER LOS DATOS Y PARSEARLO*/
			$sql = "SELECT * FROM reporte.disponibilidad_por_dia(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($subobjetivo->objetivo_id).", ".
					pg_escape_string($this->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
// 						print($sql);exit;
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
	
			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_por_dia']);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_por_dia"]);
			}
	
				
				
			$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
			$conf_fechas = $xpath->query("//detalles/detalle[1]/detalles/detalle[@fecha]");
			$conf_eventos=$xpath->query("//eventos/evento");
				
	
	
			$objWorksheet->setCellValueByColumnAndRow(0, 1, "Disponibilidad ".$conf_objetivo->getAttribute('nombre'));
			$objWorksheet->setCellValueByColumnAndRow(0, 3, "SLA Sitio Web");
			$objWorksheet->setCellValueByColumnAndRow(0, 4, "SLA Ok");
			$objWorksheet->setCellValueByColumnAndRow(0, 5, "SLA Normal");
			$objWorksheet->setCellValueByColumnAndRow(0, 6, "SLA Error");
			$objWorksheet->setCellValueByColumnAndRow(0, 8, "Los valores considerados corresponden a:");
	
			$objWorksheet->setCellValueByColumnAndRow(1, 4, "Superior a ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_ok'), 2, '.', '')."%");
			$objWorksheet->setCellValueByColumnAndRow(1, 5, "Entre ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_error'), 2, '.', '')."% y ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_ok'), 2, '.', '')."%");
			$objWorksheet->setCellValueByColumnAndRow(1, 6, "Inferior a ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_error'), 2, '.', '')."%");
				
				
				
			if ($conf_fechas->length > 0) {
	
				if($this->extra['evento_id']==0){
					$objWorksheet->setCellValueByColumnAndRow(0, 9, $xpath->query("//eventos/evento[@evento_id=1]")->item(0)->getAttribute('nombre'));
					$objWorksheet->setCellValueByColumnAndRow(0, 10, $xpath->query("//eventos/evento[@evento_id=3]")->item(0)->getAttribute('nombre'));
					
				}else{
					$objWorksheet->setCellValueByColumnAndRow(0, 9, $xpath->query("//eventos/evento[@evento_id=".$this->extra['evento_id']."]")->item(0)->getAttribute('nombre'));
				}
	
				$objWorksheet->setCellValueByColumnAndRow(0, 14, "Promedio Por Paso");
				$objWorksheet->setCellValueByColumnAndRow(0, $conf_pasos->length + 15, "Promedio Global");
					
				$objWorksheet->setCellValueByColumnAndRow(0, $conf_pasos->length + 17, "Promedio Por Fecha");
				$objWorksheet->setCellValueByColumnAndRow(0, $conf_pasos->length + 18, "Pasos / Fechas");
	
				$fecha=1;
				foreach ($conf_fechas as $conf_fecha) {
					$objWorksheet->setCellValueByColumnAndRow($fecha, $conf_pasos->length + 18, $this->timestamp->getFormatearFecha($conf_fecha->getAttribute('fecha'), "d-m-Y"));
					$fecha++;
				}
	
				$promedio_total=0;
				$fpasoprom = 15;
				$fpaso = $conf_pasos->length + 19;
	
				foreach ($conf_pasos as $conf_paso) {
					$porcentaje = 0;
					$cpaso = 1;
					$promedio = 0;
					$tag_fechas = $xpath->query("//detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/detalles/detalle");
						
					foreach ($tag_fechas as $tag_fecha) {
							
						$porcentaje_fecha=0;
						$porcentaje = 0;
						$objWorksheet->setCellValueByColumnAndRow(0, $fpasoprom, $conf_paso->getAttribute('nombre'));
						$objWorksheet->setCellValueByColumnAndRow(0, $fpaso, $conf_paso->getAttribute('nombre'));
						$porcentaje_total=0;
						foreach ($xpath->query("estadisticas/estadistica[@evento_id!=7]",$tag_fecha) as $tag_dato) {
							$porcentaje_total += $tag_dato->getAttribute('porcentaje');
						}
						 
						if ($this->extra["evento_id"] == 0) {

							$porcentaje_fecha = $porcentaje_total;
							$porcentaje += $porcentaje_total;
							
						}else {
							if($this->extra["evento_id"]==1){
								$tag_datos_update = $xpath->query("estadisticas/estadistica[@evento_id=".$this->extra["evento_id"]."]",$tag_fecha)->item(0);
								$tag_datos_parcial = $xpath->query("estadisticas/estadistica[@evento_id=3]",$tag_fecha)->item(0);
								$tag_datos_parcial=$tag_datos_parcial!=null?$tag_datos_parcial->getAttribute('porcentaje'):0;
								
								$total= $tag_datos_update->getAttribute('porcentaje')+$tag_datos_parcial;
								
								$porcentaje_fecha = ($total*100)/$porcentaje_total;
								$porcentaje += ($total*100)/$porcentaje_total;
							}else{
								
								$tag_dato = $xpath->query("estadisticas/estadistica[@evento_id=".$this->extra["evento_id"]."]",$tag_fecha)->item(0);
								$tag_dato=$tag_dato!=null?$tag_dato->getAttribute('porcentaje'):0;
								$porcentaje_fecha = ($tag_dato*100)/$porcentaje_total;
								$porcentaje += ($tag_dato*100)/$porcentaje_total;
								
							}
	
						}
						$objWorksheet->setCellValueByColumnAndRow($cpaso, $fpaso, number_format($porcentaje_fecha, 2, '.', '').'%');
						$promedio += $porcentaje;
	
						$cpaso++;
					}
						
					$promedio = $promedio / $conf_fechas->length;
					$promedio_total += $promedio;
					$objWorksheet->setCellValueByColumnAndRow(1, $fpasoprom,number_format($promedio, 2, '.', '').'%');
						
					$fpasoprom++;
					$fpaso++;
				}
				$promedio_total = $promedio_total / $conf_pasos->length;
				$objWorksheet->setCellValueByColumnAndRow(1, $conf_pasos->length + 15, number_format($promedio_total, 2, '.', '').'%');
			}
		}
	
		$objPHPExcel->removeSheetByIndex(0);
		$objPHPExcel->setActiveSheetIndex(0);
	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
	
	
	public function getDatosUptime(){
		global $mdb2;
		global $log;
		global $current_usuario_id;
	
			/* OBTENER LOS DATOS Y PARSEARLO*/
			$sql = "SELECT * FROM reporte.disponibilidad_por_dia(".
					pg_escape_string($current_usuario_id).",".
					pg_escape_string($this->objetivo_id).", ".
					pg_escape_string($this->horario_id).", '".
					pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
					pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
// 									print($sql);exit;
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}
	
			if ($row = $res->fetchRow()) {
				$dom = new DomDocument();
				$dom->preserveWhiteSpace = FALSE;
				$dom->loadXML($row['disponibilidad_por_dia']);
				$xpath = new DOMXpath($dom);
				unset($row["disponibilidad_por_dia"]);
			}
			
			$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
			$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
			$conf_fechas = $xpath->query("//detalles/detalle[1]/detalles/detalle[@fecha]");
			$conf_eventos=$xpath->query("//eventos/evento");
	
			$objPHPExcel = new PHPExcel();
			
			$objWorksheet = $objPHPExcel->createSheet();
			$objWorksheet->getDefaultStyle()->getFont()->setName('Liberation Sans')->setSize(10);
			$objWorksheet->setTitle(substr(str_replace(array("https://","http://","[","]","\\","/",":"), array("","","(",")"," "," ",""), $conf_objetivo->getAttribute('nombre')), 0, 30));
			$objWorksheet->getColumnDimension('A')->setWidth(40);
			$objWorksheet->getColumnDimension('B')->setWidth(20);
	
	
			$objWorksheet->setCellValueByColumnAndRow(0, 1, "Disponibilidad ".$conf_objetivo->getAttribute('nombre'));
			$objWorksheet->setCellValueByColumnAndRow(0, 3, "SLA Sitio Web");
			$objWorksheet->setCellValueByColumnAndRow(0, 4, "SLA Ok");
			$objWorksheet->setCellValueByColumnAndRow(0, 5, "SLA Normal");
			$objWorksheet->setCellValueByColumnAndRow(0, 6, "SLA Error");
			$objWorksheet->setCellValueByColumnAndRow(0, 8, "Los valores considerados corresponden a:");
	
			$objWorksheet->setCellValueByColumnAndRow(1, 4, "Superior a ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_ok'), 2, '.', '')."%");
			$objWorksheet->setCellValueByColumnAndRow(1, 5, "Entre ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_error'), 2, '.', '')."% y ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_ok'), 2, '.', '')."%");
			$objWorksheet->setCellValueByColumnAndRow(1, 6, "Inferior a ".number_format($conf_objetivo->getAttribute('sla_disponibilidad_error'), 2, '.', '')."%");
	
	
	
			if ($conf_fechas->length > 0) {
	
				if($this->extra['evento_id']==0){
					$objWorksheet->setCellValueByColumnAndRow(0, 9, $xpath->query("//eventos/evento[@evento_id=1]")->item(0)->getAttribute('nombre'));
					$objWorksheet->setCellValueByColumnAndRow(0, 10, $xpath->query("//eventos/evento[@evento_id=3]")->item(0)->getAttribute('nombre'));
						
				}else{
					$objWorksheet->setCellValueByColumnAndRow(0, 9, $xpath->query("//eventos/evento[@evento_id=".$this->extra['evento_id']."]")->item(0)->getAttribute('nombre'));
				}
	
				$objWorksheet->setCellValueByColumnAndRow(0, 14, "Promedio Por Paso");
				$objWorksheet->setCellValueByColumnAndRow(0, $conf_pasos->length + 15, "Promedio Global");
					
				$objWorksheet->setCellValueByColumnAndRow(0, $conf_pasos->length + 17, "Promedio Por Fecha");
				$objWorksheet->setCellValueByColumnAndRow(0, $conf_pasos->length + 18, "Pasos / Fechas");
	
				$fecha=1;
				foreach ($conf_fechas as $conf_fecha) {
					$objWorksheet->setCellValueByColumnAndRow($fecha, $conf_pasos->length + 18, $this->timestamp->getFormatearFecha($conf_fecha->getAttribute('fecha'), "d-m-Y"));
					$fecha++;
				}
	
				$promedio_total=0;
				$fpasoprom = 15;
				$fpaso = $conf_pasos->length + 19;
	
				foreach ($conf_pasos as $conf_paso) {
					$porcentaje = 0;
					$cpaso = 1;
					$promedio = 0;
					$tag_fechas = $xpath->query("//detalles/detalle[@paso_orden=".$conf_paso->getAttribute('paso_orden')."]/detalles/detalle");
	
					foreach ($tag_fechas as $tag_fecha) {
							
						$porcentaje_fecha=0;
						$porcentaje = 0;
						$objWorksheet->setCellValueByColumnAndRow(0, $fpasoprom, $conf_paso->getAttribute('nombre'));
						$objWorksheet->setCellValueByColumnAndRow(0, $fpaso, $conf_paso->getAttribute('nombre'));
						$porcentaje_total=0;
						foreach ($xpath->query("estadisticas/estadistica[@evento_id!=7]",$tag_fecha) as $tag_dato) {
							$porcentaje_total += $tag_dato->getAttribute('porcentaje');
						}
							
						if ($this->extra["evento_id"] == 0) {
	
							$porcentaje_fecha = $porcentaje_total;
							$porcentaje += $porcentaje_total;
								
						}else {
							if($this->extra["evento_id"]==1){
								$tag_datos_update = $xpath->query("estadisticas/estadistica[@evento_id=".$this->extra["evento_id"]."]",$tag_fecha)->item(0);
								$tag_datos_parcial = $xpath->query("estadisticas/estadistica[@evento_id=3]",$tag_fecha)->item(0);
								$tag_datos_parcial=$tag_datos_parcial!=null?$tag_datos_parcial->getAttribute('porcentaje'):0;
	
								$total= $tag_datos_update->getAttribute('porcentaje')+$tag_datos_parcial;
	
								$porcentaje_fecha = ($total*100)/$porcentaje_total;
								$porcentaje += ($total*100)/$porcentaje_total;
							}else{
	
								$tag_dato = $xpath->query("estadisticas/estadistica[@evento_id=".$this->extra["evento_id"]."]",$tag_fecha)->item(0);
								$tag_dato=$tag_dato!=null?$tag_dato->getAttribute('porcentaje'):0;
								$porcentaje_fecha = ($tag_dato*100)/$porcentaje_total;
								$porcentaje += ($tag_dato*100)/$porcentaje_total;
	
							}
	
						}
						$objWorksheet->setCellValueByColumnAndRow($cpaso, $fpaso, number_format($porcentaje_fecha, 2, '.', '').'%');
						$promedio += $porcentaje;
	
						$cpaso++;
					}
	
					$promedio = $promedio / $conf_fechas->length;
					$promedio_total += $promedio;
					$objWorksheet->setCellValueByColumnAndRow(1, $fpasoprom,number_format($promedio, 2, '.', '').'%');
	
					$fpasoprom++;
					$fpaso++;
				}
				$promedio_total = $promedio_total / $conf_pasos->length;
				$objWorksheet->setCellValueByColumnAndRow(1, $conf_pasos->length + 15, number_format($promedio_total, 2, '.', '').'%');
			}
		
		$objPHPExcel->removeSheetByIndex(0);
		$objPHPExcel->setActiveSheetIndex(0);
	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	/*
	 Creado por: Aldo Cruz Romero
	 Modificado por: --
	 Fecha de creacion:12/10/2018
	 Fecha de modificacion: --
	 */
	public function getConsolidadoHora() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		global $mdb2;
		global $log;
		global $current_usuario_id;

		$sql = "SELECT * FROM reporte.disponibilidad_resumen_global_ponderado_poritem(".
  				pg_escape_string($current_usuario_id).",".
  				pg_escape_string($this->objetivo_id).",5,' ".
  				pg_escape_string($this->timestamp->getInicioPeriodo())."', '".
  				pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
  		//print $sql;
  		$res = & $mdb2->query($sql);
  		if (MDB2::isError($res)) {
 			$log->setError($sql, $res->userinfo);
 			exit();
 		}
 		if($row = $res->fetchRow()){
 			$dom = new DomDocument();
 			$dom->preserveWhiteSpace = FALSE;
  			$dom->loadXML($row['disponibilidad_resumen_global_ponderado_poritem']);
  			$xpath = new DOMXpath($dom);
  			unset($row["disponibilidad_resumen_global_ponderado_poritem"]);
		}
		$conf_objetivo = $xpath->query("/atentus/resultados/propiedades/objetivos/objetivo")->item(0);
  		$conf_pasos = $xpath->query("paso[@visible=1]", $conf_objetivo);
  		$conf_ponderaciones = $xpath->query("/atentus/resultados/propiedades/ponderaciones/item");
  		$conf_eventos = $xpath->query("/atentus/resultados/propiedades/eventos/evento");

  		$objPHPExcel = new PHPExcel();
			
		$objWorksheet = $objPHPExcel->createSheet();
		$objWorksheet->getDefaultStyle()->getFont()->setName('Liberation Sans')->setSize(10);
		$objWorksheet->setTitle(substr(str_replace(array("https://","http://","[","]","\\","/",":"), array("","","(",")"," "," ",""), $conf_objetivo->getAttribute('nombre')), 0, 30));
		$objWorksheet->getColumnDimension('A')->setWidth(40);
		$objWorksheet->getColumnDimension('B')->setWidth(20);
		$objWorksheet->getColumnDimension('C')->setWidth(20);
		$objWorksheet->getColumnDimension('D')->setWidth(20);
		$style_align = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$objWorksheet->setCellValueByColumnAndRow(0, 1, "Disponibilidad ".$conf_objetivo->getAttribute('nombre'));
		$objWorksheet->setCellValueByColumnAndRow(0, 3, "Los valores considerados corresponden a:");
		$objWorksheet->setCellValueByColumnAndRow(1, 5, "CONSOLIDADO POR ISP");
  		
  		$primero=true;
  		$contPaso=8;
  		foreach ($conf_pasos as $conf_paso) {
  			$objWorksheet->getStyle('C'.$contPaso)->applyFromArray($style_align);
  			$objWorksheet->getStyle('D'.$contPaso)->applyFromArray($style_align);
  			$tag_paso = $xpath->query("//detalles/detalle/detalles/detalle[@paso_orden=".$conf_paso->getAttribute("paso_orden")."]")->item(0);
  			$objWorksheet->setCellValueByColumnAndRow(0, $contPaso-1, 'Paso :'.$conf_paso->getAttribute('nombre'));
  			$objWorksheet->setCellValueByColumnAndRow(0, $contPaso, "Horario Inicio");
  			$objWorksheet->setCellValueByColumnAndRow(1, $contPaso, "Horario Termino");
			$objWorksheet->setCellValueByColumnAndRow(2, $contPaso, "Uptime Global (%)");
			$objWorksheet->setCellValueByColumnAndRow(3, $contPaso, "Downtime Global (%)");
			$array_porcentaje = array();
  			foreach ($conf_ponderaciones as $conf_ponderacion) {
  				$contPaso++;
  				$objWorksheet->setCellValueByColumnAndRow(0, $contPaso, $conf_ponderacion->getAttribute('inicio'));
  				$objWorksheet->setCellValueByColumnAndRow(1, $contPaso, $conf_ponderacion->getAttribute('termino'));
  				$arrayeventos=Array();
  				foreach ($conf_eventos as $conf_evento) {
  					$tag_dato_item = $xpath->query("detalles/detalle[@item_id=".$conf_ponderacion->getAttribute('item_id')."]/estadisticas/estadistica[@evento_id=".$conf_evento->getAttribute("evento_id")."]", $tag_paso)->item(0);
 					if ($tag_dato_item == null) {
 						$porcentaje = 0;
  					}else {
  						$porcentaje = $tag_dato_item->getAttribute("porcentaje");
  					}
  					$arrayeventos[$conf_evento->getAttribute("evento_id")]=$porcentaje;
				}
				$noMOn=$arrayeventos[7];
				$marcado=$arrayeventos[9];
				if(($noMOn+$marcado)!=100){
					$uptime=$arrayeventos[1];
					$downtime=$arrayeventos[2];
					$downtimep=$arrayeventos[3];
					$uptime=$uptime+$downtimep;
					$total=$uptime+$downtime;
					$uptimeReal=$uptime*100/$total;	
					$downtime_real=$downtime*100/$total;
				}else{
					$uptimeReal=0;
					$downtime_real=0;
				}
				$objWorksheet->setCellValueByColumnAndRow(2, $contPaso, number_format(round($uptimeReal, 2), 2));
				$objWorksheet->setCellValueByColumnAndRow(3, $contPaso, number_format(round($downtime_real, 2), 2));
  			}
  			($contPaso+=4);
  		}
		$objPHPExcel->removeSheetByIndex(0);
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
	
	public function getEspecialObjetivosPasosComunesAFC() {
		/* SI SE DESCARGO EL CSV */
		//if ($this->tipo=="csv") {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		$separador = ";";
		$salto_linea = "\n";
		$this->resultado = "";

		$objetivo = new ConfigEspecial($this->objetivo_id);

		$dom = new DomDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadXML($objetivo->__xml_config);
		$xpath_config_especial = new DOMXpath($dom);

		$reporte_period_start = date("Y-m-d");

		## generación de las fechas
		if($objetivo->period_start && $objetivo->intervalo_resta_fecha_actual && ConfigEspecial::_validaFecha($objetivo->period_start) && ConfigEspecial::_validaIntervalo($objetivo->intervalo_resta_fecha_actual)) {
			$fecha_menos_intervalo = date( 'Y-m-d', strtotime('-'.str_replace('-', '', $objetivo->intervalo_resta_fecha_actual)));
			$reporte_period_start = strtotime($objetivo->period_start) >= strtotime($fecha_menos_intervalo) ? $objetivo->period_start : $fecha_menos_intervalo;
		} elseif($objetivo->period_start && ConfigEspecial::_validaFecha($objetivo->period_start)) {
			$reporte_period_start = $objetivo->period_start;
		} elseif($objetivo->intervalo_resta_fecha_actual && ConfigEspecial::_validaIntervalo($objetivo->intervalo_resta_fecha_actual)) {
			$reporte_period_start = date( 'Y-m-d', strtotime('-'.str_replace('-', '', $objetivo->intervalo_resta_fecha_actual) ) );
		}


		if (strtotime($this->timestamp->getInicioPeriodo()) < strtotime($reporte_period_start) 
			|| strtotime($this->timestamp->getInicioPeriodo()) > strtotime(date("Y-m-d"))) {
			print "La fecha ingresada no es válida.";
			return;
		}

		## Se recorren los objetivos encontrados en la configuración del reporte especial
		foreach($xpath_config_especial->query('///grupos/grupo/relacion') as $key => $relacion) {
			$sql = "SELECT * FROM reporte.datos(".
							pg_escape_string($current_usuario_id).",".
							pg_escape_string($relacion->getAttribute('objetivo_id')).",'".
							pg_escape_string($this->timestamp->getInicioPeriodo())."','".
							pg_escape_string($this->timestamp->getTerminoPeriodo())."')";
			$res =& $mdb2->query($sql);
			if (MDB2::isError($res)) {
				$log->setError($sql, $res->userinfo);
				exit();
			}

			## Se recorre cada resultado obtenido desde la función reporte.datos en base al objetivo encontrado
			foreach ($res->fetchAll() as $row_key => $row) {

				## ingreso si es el primer objetivo encontrado
				if (is_null($row["filas"]) && $key === 0) {
					$dom = new DomDocument();
					$dom->preserveWhiteSpace = FALSE;
					$dom->loadXML($row['xml']);
					$xpath = new DOMXpath($dom);
					unset($row["xml"]);

					## Leyenda del documento
					$this->resultado.="Leyenda".$salto_linea;
					$this->resultado.="   Servidor : servidor que realizo el monitoreo.".$salto_linea;
					$this->resultado.="   Fecha : fecha cuando se realizo el monitoreo.".$salto_linea;
					$this->resultado.="   Hora : hora cuando se realizo el monitoreo.".$salto_linea;
					$this->resultado.="   Status : codigo de estado del monitoreo.".$salto_linea;
					$this->resultado.="   Delay : tiempo de respuesta del monitoreo.".$salto_linea.$salto_linea;
					$this->resultado.="Codigos de Estados".$salto_linea;

					## Se extraen los códigos de error desde el xml obtenido desde el primer objetivo encontrado
					$array_codigo= array();
					foreach ($xpath->query("//codigos/codigo") as $codigos) {
						$array_codigo[$codigos->getAttribute('codigo_id')] = $codigos->getAttribute('nombre');
					}
					ksort($array_codigo);

					## Se imprimen los código en la salida
					foreach ($array_codigo as $codigo=> $definicion) {
						$this->resultado.="   ".$codigo." : ".$definicion.$salto_linea;
					}
					$this->resultado.= $salto_linea.$salto_linea;

				} else {
					## Si es la primera iteración (La primera iteración nos entrega la descripción de cada columna)
					## de los resultados que no son cabecera se contabilizan los pasos y tiempos
					## key = 0 es el primer objetivo encontrado
					## row_key === 1  segunda tupla donde se encuentra la descripcion de cada columna
					if($key === 0 && $row_key === 1) {
						$cabecera_siguiente = $cabecera = array("pasos"=>0, "tiempo"=>0);

						## se registra la cantidad de pasos y tiempos que contiene el primer objetivo
						foreach(json_decode(str_replace(array("servidor,fecha,hora,","{","}"), array("","[","]"), $row["filas"])) as $val) {
							preg_match("/^(.*)\s?(\[ms\])$/i", $val) ? $cabecera["tiempo"]++ : $cabecera["pasos"]++;
						}
						$cabecera_siguiente["tiempo"]= $cabecera["tiempo"];
						$cabecera_siguiente["pasos"]= $cabecera["pasos"];
					}

					## Toda descripción de columnas de otro objetivo
					if ($key !== 0 && $row_key === 1) {
						$cabecera_siguiente = array("pasos"=>0, "tiempo"=>0);

						## se registra la cantidad de pasos y tiempos que contiene los siguientes objetivos
						foreach(json_decode(str_replace(array("servidor,fecha,hora,","{","}"), array("","[","]"), $row["filas"])) as $val) {
							preg_match("/^(.*)\s?(\[ms\])$/i", $val) ? $cabecera_siguiente["tiempo"]++ : $cabecera_siguiente["pasos"]++;
						}
						continue;
					} elseif (!is_null($row["filas"])) {
						$tupla = str_replace(array("{","}",",","\""), array("","",$separador,""), preg_replace('(,-\d*)', ',-1000', $row["filas"]));

						## se registra la cabecera de las columnas solo cuando sera el primer objetivo
						if($key === 0 && $row_key === 1) {
							$this->resultado.= $tupla;
						} else {
							## se convierte el resultado de la tupla en un array
							$tupla = explode($separador, $tupla);
							## se registran los valores estaticos (servidor, fecha, hora)
							$this->resultado.= $tupla[0].$separador.$tupla[1].$separador.$tupla[2];

							## se registan los PASOS de manera dinámica
							for($i=3; $i<(3+$cabecera["pasos"]); $i++) {
								$this->resultado.= $separador.((array_key_exists($i, $tupla) && ($i-3)<$cabecera_siguiente["pasos"])?$tupla[$i]:"");
							}

							## se registan los TIEMPOS de manera dinámica
							for($i=(3+$cabecera_siguiente["pasos"]); $i<(3+$cabecera_siguiente["pasos"]+$cabecera["tiempo"]); $i++) {
								$this->resultado.= $separador.(array_key_exists($i, $tupla)?$tupla[$i]:"");
							}
						}

						$this->resultado.= $salto_linea;
					}
				}
			}
		}
		print $this->resultado;
	}
	/*
	Creado por:Aldo Cruz Romero
	Fecha de creacion:02-09-2019
	Fecha de ultima modificacion:
	*/
	public function getBancoScotiabank(){
		global $mdb2;
	    global $log;
	    global $current_usuario_id;
	    include 'utils/get_especial_scotiabank.php';

	    //VARIABLES GLOBALES
	    $usuario = new Usuario($current_usuario_id);
		$usuario->__Usuario();
		$fecha_inicio = explode(" ",$this->timestamp->fecha_inicio)[0];
		$fecha_termino = explode(" ",$this->timestamp->fecha_termino)[0];
		$cliente_id=($usuario->cliente_id);
		$user=($usuario->usuario_id);

		//INSTANCIA PHPEXCEL
		$objPHPExcel = new PHPExcel();
		$objWorksheet = $objPHPExcel->getActiveSheet()->setAutoFilter('A1:H4000');;
	    
	    //SE CREA SEGUNDA HPJA
		$objPHPExcel->createSheet();

		// SE AGREGA SEGUNDA HOJA
		$objPHPExcel->setActiveSheetIndex(1);
		$Obj2=$objPHPExcel->getActiveSheet()->setAutoFilter('A2:C4000');
		
		//SE DEFINEN ESTILOS
	    $style_align  = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$style_title_sheet = array('font' => array('color' => array('rgb' => '000000'), 'size' => '12','name'  => 'Calibri', 'bold' => true));
		$style_border_all = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'))));

		
		$Obj2->getStyle('A1')->applyFromArray($style_title_sheet);
		$Obj2->getStyle('B1')->applyFromArray($style_align);
		$Obj2->getStyle('C1')->applyFromArray($style_title_sheet);
		$Obj2->getColumnDimension('A')->setWidth(10);
		$Obj2->getColumnDimension('B')->setWidth(30);
		$Obj2->getColumnDimension('C')->setWidth(20);
		$Obj2->setCellValue('A1', 'FECHA');
		$Obj2->setCellValue('B1', $fecha_inicio);
		$Obj2->setCellValue('A2', 'Id');
		$Obj2->setCellValue('B2', 'Nombre Objetivo');
		$Obj2->setCellValue('C2', 'Paso');

		$objWorksheet->getStyle('D')->applyFromArray($style_align);
		$objWorksheet->getStyle('F')->applyFromArray($style_align);
		$objWorksheet->getStyle('A1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('B1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('C1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('D1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('E1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('F1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('G1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('H1')->applyFromArray($style_title_sheet);
		$objWorksheet->getColumnDimension('B')->setWidth(20);
		$objWorksheet->getColumnDimension('C')->setWidth(15);
		$objWorksheet->getColumnDimension('D')->setWidth(20);
		$objWorksheet->getColumnDimension('E')->setWidth(15);
		$objWorksheet->getColumnDimension('F')->setWidth(10);
		$objWorksheet->getColumnDimension('G')->setWidth(20);
		$objWorksheet->getColumnDimension('H')->setWidth(20);
		$objWorksheet->setCellValue('A1', 'Id');
		$objWorksheet->setCellValue('B1', 'Infraestructura');
		$objWorksheet->setCellValue('C1', 'Destino');
		$objWorksheet->setCellValue('D1', 'Identificacion Canal');
		$objWorksheet->setCellValue('E1', 'Tipo Servicio');
		$objWorksheet->setCellValue('F1', "Uptime");
		$objWorksheet->setCellValue('G1', "Horario Inicio");
		$objWorksheet->setCellValue('H1', "Horario Termino");

		$data= get_especial_scotiabank($cliente_id,$user, $fecha_inicio, $fecha_termino);
		$json_data=(json_decode($data));
		$contService=2;
		$contId=3;
		foreach ($json_data as $key => $value) {
			foreach ($value->data_hora as $hora => $canal_destino) {
				foreach ($canal_destino->data as $key2 => $dataHora) {
					if($dataHora->promedio!=0){
						$termino=$hora+1;
						$objWorksheet->setCellValue('A'.$contService, $contService-1);
						$objWorksheet->setCellValue('E'.$contService, $value->servicio);
						$objWorksheet->setCellValue('G'.$contService, $hora.':00:00');
						$objWorksheet->setCellValue('H'.$contService, $termino.':00:00');
						$objWorksheet->setCellValue('B'.$contService, $dataHora->canal);
						$objWorksheet->setCellValue('C'.$contService, $dataHora->destino);
						if($dataHora->destino==1){
							$canal=" 069 ";
						}else{
							$canal=" 070 ";
						}
						$objWorksheet->setCellValue('D'.$contService, $canal);
						$objWorksheet->setCellValue('F'.$contService, number_format($dataHora->promedio, 2));
						foreach ($dataHora->objetivos as $keyObjetivos => $objetivo) {
							$Obj2->setCellValue('A'.$contId, $contService-1);
							$Obj2->setCellValue('B'.$contId, $objetivo->objetivo);
							$Obj2->setCellValue('C'.$contId, $objetivo->paso);
							$contId++;
						}
						$contService++;
					}
				}
			}
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	/*
	Creado por:Aldo Cruz Romero
	Fecha de creacion:05-12-2019
	Fecha de ultima modificacion:
	*/
	public function getTagBanco(){
		global $mdb2;
	    global $log;
	    global $current_usuario_id;
	    include 'utils/get_especial_tag_bancos.php';

	    //VARIABLES GLOBALES
	    $usuario = new Usuario($current_usuario_id);
		$usuario->__Usuario();
		$fecha_inicio = explode(" ",$this->timestamp->fecha_inicio)[0];
		$fecha_termino = explode(" ",$this->timestamp->fecha_termino)[0];
		$cliente_id=($usuario->cliente_id);
		$user=($usuario->usuario_id);
		$objetivoEspecial = new ConfigEspecial($this->objetivo_id);
		$objetivo=($objetivoEspecial->objetivo_id);
		$tag=($objetivoEspecial->tag);
		$token=($objetivoEspecial->token);

		//INSTANCIA PHPEXCEL
		$objPHPExcel = new PHPExcel();
		$objWorksheet = $objPHPExcel->getActiveSheet()->setAutoFilter('A1:H4000');;
	    
	    //SE CREA SEGUNDA HPJA
		$objPHPExcel->createSheet();

		// SE AGREGA SEGUNDA HOJA
		$objPHPExcel->setActiveSheetIndex(1);
		$Obj2=$objPHPExcel->getActiveSheet()->setAutoFilter('A2:C4000');
		
		//SE DEFINEN ESTILOS
	    $style_align  = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$style_title_sheet = array('font' => array('color' => array('rgb' => '000000'), 'size' => '12','name'  => 'Calibri', 'bold' => true));
		$style_border_all = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'))));
		
		$Obj2->getStyle('A1')->applyFromArray($style_title_sheet);
		$Obj2->getStyle('B1')->applyFromArray($style_align);
		$Obj2->getStyle('C1')->applyFromArray($style_title_sheet);
		$Obj2->getColumnDimension('A')->setWidth(10);
		$Obj2->getColumnDimension('B')->setWidth(30);
		$Obj2->getColumnDimension('C')->setWidth(20);
		$Obj2->setCellValue('A1', 'FECHA');
		$Obj2->setCellValue('B1', $fecha_inicio);
		$Obj2->setCellValue('A2', 'Id');
		$Obj2->setCellValue('B2', 'Nombre Objetivo');
		$Obj2->setCellValue('C2', 'Paso');

		$objWorksheet->getStyle('D')->applyFromArray($style_align);
		$objWorksheet->getStyle('F')->applyFromArray($style_align);
		$objWorksheet->getStyle('A1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('B1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('C1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('D1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('E1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('F1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('G1')->applyFromArray($style_title_sheet);
		$objWorksheet->getStyle('H1')->applyFromArray($style_title_sheet);
		$objWorksheet->getColumnDimension('B')->setWidth(20);
		$objWorksheet->getColumnDimension('C')->setWidth(15);
		$objWorksheet->getColumnDimension('D')->setWidth(20);
		$objWorksheet->getColumnDimension('E')->setWidth(15);
		$objWorksheet->getColumnDimension('F')->setWidth(10);
		$objWorksheet->getColumnDimension('G')->setWidth(20);
		$objWorksheet->getColumnDimension('H')->setWidth(20);
		$objWorksheet->setCellValue('A1', 'Id');
		$objWorksheet->setCellValue('B1', 'Infraestructura');
		$objWorksheet->setCellValue('C1', 'Destino');
		$objWorksheet->setCellValue('D1', 'Identificacion Canal');
		$objWorksheet->setCellValue('E1', 'Tipo Servicio');
		$objWorksheet->setCellValue('F1', "Uptime");
		$objWorksheet->setCellValue('G1', "Horario Inicio");
		$objWorksheet->setCellValue('H1', "Horario Termino");
		$data= get_especial_tag_banco($cliente_id,$user, $fecha_inicio, $tag, $this->horario_id, $objetivo, $token);
		$json_data=(json_decode($data));
		if($json_data==1){
			$objWorksheet->setCellValue('A3', 'Horario Seleccionado Inválido');
		}else if($json_data==0){
			$objWorksheet->setCellValue('A3', 'Parametro de entrada objetivoId no corresponde a ningun objetivo funcional del usuario');
		}else if($json_data==2){
			$objWorksheet->setCellValue('A3', 'No es posible acceder en estos momentos al informe, por favor contacte a nuestra plataforma de ayuda');
		}else if($json_data=='Por Favor vuelva a intentar en 20 segundos'){
			$objWorksheet->setCellValue('A3', "Por Favor vuelva a intentar en 20 segundos");
		}else{
			$contService=2;
			$contId=3;
			foreach ($json_data as $key => $value) {
				foreach ($value->data_hora as $hora => $canal_destino) {
					$hora=$canal_destino->hour;
					foreach ($canal_destino->data as $key2 => $dataHora) {
						if($dataHora->promedio!=0){
							$termino=$hora+1;
							$objWorksheet->setCellValue('A'.$contService, $contService-1);
							$objWorksheet->setCellValue('E'.$contService, $value->servicio);
							$objWorksheet->setCellValue('G'.$contService, $hora.':00:00');
							$objWorksheet->setCellValue('H'.$contService, $termino.':00:00');
							$objWorksheet->setCellValue('B'.$contService, $dataHora->canal);
							$objWorksheet->setCellValue('C'.$contService, $dataHora->destino);
							if($dataHora->destino==1){
								$canal=" 069 ";
							}else{
								$canal=" 070 ";
							}
							$objWorksheet->setCellValue('D'.$contService, $canal);
							$objWorksheet->setCellValue('F'.$contService, number_format($dataHora->promedio, 2));
							foreach ($dataHora->objetivos as $keyObjetivos => $objetivo) {
								$Obj2->setCellValue('A'.$contId, $contService-1);
								$Obj2->setCellValue('B'.$contId, $objetivo->objetivo);
								$Obj2->setCellValue('C'.$contId, $objetivo->paso);
								$contId++;
							}
							$contService++;
						}
					}
				}
			}
		}
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
}

?>
