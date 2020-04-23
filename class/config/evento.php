<?
#
## Clase que maneja los eventos de mantenimiento.
#
class Event{

	# 
	## Atributos publicos.
	#
	var $data;
	#
	##	Setea los valores para la variable que contendra los datos.
	#
	function define($data, $type) {
		$this->data = ($type == 'edit')?$this->prepareDataEdit($data):$this->prepareData($data);
	}
	

	# Función para guardar un registro de evento.
	function createData(){
		global $mdb2;
		global $log;
		$response = array();
		$count = 0;

		
		$sql = "INSERT INTO mantenimiento.mantenimiento(nombre, comentario, usuario_id, estado, fecha_inicio, fecha_termino, fecha_creacion, fecha_modificacion, titulo, objetivo_id) VALUES ('".$this->data->name."', '".$this->data->commentary."', ".pg_escape_string($this->data->idUser).", '".$this->data->status."', '".$this->data->dateStart."', '".$this->data->dateEnd."', '".$this->data->dateCreation."', '".$this->data->dateCreation."', '".$this->data->title."','".$this->data->arrObj."')";
		
    	$res =& $mdb2->query($sql);
		
		if (MDB2::isError($res)) {
			$response['status'] = 'error-int';
			return $response;
		}
		
		$idMaintainer = $mdb2->lastInsertID();
		foreach (explode(",",$this->data->objetives) as $key=>$objetive) {
			$obj = new Objetivo($objetive);
			$nodeId = $obj->getNodos();
			$node = '{';
			$empty = true;

			foreach ($nodeId as $key => $value) {
				$node = $node.$key.',';
				$empty = false;
			}

			$node = substr($node, 0, -1);
			$node = $node.'}';
			$node = ($empty==false)? $node:'';
		    
		    $sql = "INSERT INTO public.periodo_marcado(objetivo_id, nodos_id, fecha_inicio, fecha_termino, motivo, autorizacion, usuario, id_tipo_marcado, id_mantenimiento) VALUES ('".$objetive."', '".$node."', '".$this->data->dateStart."', '".$this->data->dateEnd."', '".$this->data->title."', '".$this->data->authorization."', '".$this->data->idUser."', '".$this->data->idTypeMarker."','".$idMaintainer."')";
			
			$res =& $mdb2->query($sql);			
			
			if (MDB2::isError($res)) {
				$textFind = '/anterioridad/i';
				$sql = "DELETE  FROM public.periodo_marcado WHERE id_mantenimiento='$idMaintainer'";
				if (preg_match($textFind, $res->userinfo)){

					$response['status'] = 'error-duplicate';
					$response['nameObj'] = $this->data->arrNameObj[$count];
				}
				else if(strlen($node)==0){
					$response['status'] = 'error-nodo';
					$response['nameObj'] = $this->data->arrNameObj[$count];

				}
				else{
					$response['status'] = 'error-int';
				}
				$res =& $mdb2->query($sql);

				$sql = "UPDATE mantenimiento.mantenimiento SET estado='Error' WHERE id='$idMaintainer'";
		
				$res =& $mdb2->query($sql);
				
				return $response;
			}
		$count = $count + 1;
		}
		/* Elimina el cache*/
		foreach (explode(',', $this->data->objetives) as $key => $value) {
			$sql = "DELETE from cache.cache_nivel1 where '".$value."'::text = ANY (parametro) AND fecha_creacion > '".$this->data->dateCreation."'";
			$res =& $mdb2->query($sql);
		}
		$response['status'] = 'success';
		return $response;
	}
	
	# Función para actualizar un registro de Evento.
	function editData(){
		global $mdb2;
		global $log;
		
		$count = 0;
		/*
		Cuando se cancela un evento.
		*/
		if ($this->data->status=='Cancelado'){
			$sql = "DELETE  FROM public.periodo_marcado WHERE id_mantenimiento=".pg_escape_string($this->data->idEvent)."";
			$res =& $mdb2->query($sql);
			
			if (MDB2::isError($res)) {
				$response['status'] = 'error-int';
				return $response;
			}
			$sql = "UPDATE mantenimiento.mantenimiento SET comentario='".$this->data->commentary."', estado='".$this->data->status."', fecha_inicio='".$this->data->dateStart."', fecha_termino='".$this->data->dateEnd."', titulo='".$this->data->title."', fecha_modificacion='".$this->data->dateModification."' WHERE id=".pg_escape_string($this->data->idEvent)."";
			$res =& $mdb2->query($sql);
			/* Elimina el cache*/
			foreach (explode(',', $this->data->objetives )as $key => $value) {
				$sql = "DELETE from cache.cache_nivel1 where '".$value."'::text = ANY (parametro) AND fecha_creacion > '".$this->data->dateCreate."'";
				$res =& $mdb2->query($sql);
			}	
		}
		/*
		Cuando el evento tiene estado error y se debe ingresar de nuevo.
		*/
		if(($this->data->retry == true or $this->data->retry == 'true') and  $this->data->status!='Cancelado'){
			
			foreach (explode(",",$this->data->objetives) as $key=>$objetive) {
				$obj = new Objetivo($objetive);
				$nodeId = $obj->getNodos();
				$node = '{';
				$empty = true; 
				foreach ($nodeId as $key => $value) {
					$node = $node.$key.',';
					$empty = false;
				}
				$node = substr($node, 0, -1);
				$node = $node.'}';
				$node = ($empty==false)? $node:'';
			    $sql = "INSERT INTO public.periodo_marcado(objetivo_id, nodos_id, fecha_inicio, fecha_termino, motivo, autorizacion, usuario, id_tipo_marcado, id_mantenimiento) VALUES ('".$objetive."', '".$node."', '".$this->data->dateStart."', '".$this->data->dateEnd."', '".$this->data->title."', '".$this->data->authorization."', '".$this->data->idUser."', '".$this->data->idTypeMarker."',".pg_escape_string($this->data->idEvent).")";
				$res =& $mdb2->query($sql);			

				if (MDB2::isError($res)) {
					$textFind = '/anterioridad/i';
					$sql = "DELETE  FROM public.periodo_marcado WHERE id_mantenimiento=".pg_escape_string($this->data->idEvent)."";
					if (preg_match($textFind, $res->userinfo)){

						$response['status'] = 'error-duplicate';
						$response['nameObj'] = $this->data->arrNameObj[$count];
					}
					else if(strlen($node)==0){
					$response['status'] = 'error-nodo';
					$response['nameObj'] = $this->data->arrNameObj[$count];

					}
					else{
						$response['status'] = 'error-ints';
					}
					$res =& $mdb2->query($sql);

					$sql = "UPDATE mantenimiento.mantenimiento SET estado='Error', fecha_modificacion='".$this->data->dateModification."' WHERE id=".pg_escape_string($this->data->idEvent)."";
			
					$res =& $mdb2->query($sql);
					
					return $response;
				}
			}
			$count = $count + 1;
			$sql = "UPDATE mantenimiento.mantenimiento SET comentario='".$this->data->commentary."', estado='".$this->data->status."', fecha_inicio='".$this->data->dateStart."', fecha_termino='".$this->data->dateEnd."', titulo='".$this->data->title."', fecha_modificacion='".$this->data->dateModification."' WHERE id=".pg_escape_string($this->data->idEvent)."";
			$res =& $mdb2->query($sql);
			$response['status'] = 'success';
			return $response;
		}
		/*
		Cuando solo se quiere actualizar el evento en estado ingresado.
		*/
		else{
			
			$sql = "UPDATE mantenimiento.mantenimiento SET comentario='".$this->data->commentary."', estado='".$this->data->status."', fecha_inicio='".$this->data->dateStart."', fecha_termino='".$this->data->dateEnd."', titulo='".$this->data->title."', fecha_modificacion='".$this->data->dateModification."' WHERE id=".pg_escape_string($this->data->idEvent)."";
			
			$res =& $mdb2->query($sql);
			
			if (MDB2::isError($res)) {

				$response['status'] = 'error-int';
				return $response;
			}
		}
		$response['status'] = 'success';
		return $response;
	}

	#
	## Devuelve un objeto con los datos necesarios para editar.
	#
	function prepareDataEdit($data){
		$character = array("{", "}");
		$objectData = new stdClass();
		$objectData->idEvent = $data[0];
		$objectData->name = $data[1];
		$objectData->idUser = (int)$data[2];
		$objectData->timeZone = $data[12];
		$objectData->dateEnd = Utiles::convertDateUtc($data[5], $objectData->timeZone);
		$objectData->dateStart = Utiles::convertDateUtc($data[4], $objectData->timeZone);
		$dates = Utiles::convertDateUtc($data[6], $objectData->timeZone);
		$objectData->dateCreate = strtotime ( '-1 day' , strtotime ($date));
		$objectData->dateCreate = date ( 'Y-m-d' , $objectData->dateCreate);		
		date_default_timezone_set($objectData->timeZone);
		$objectData->dateModification = Utiles::convertDateUtc((date("Y-m-d H:i:s",time())), $objectData->timeZone);
		$objectData->title = $data[8];
		$objectData->status = ($data[3]=='1')?'Ingresado':'Cancelado';
		$objectData->commentary=$data[9];
		$objectData->retry = (isset($data[14]))?$data[14]:null;
		$objectData->arrObj = $data[10];
		$objectData->objetives = str_replace($character, "", $data[10]);
		$objectData->arrNameObj = explode(',',$data[13]);
		$objectData->authorization = 'Reporte Atentus';
		$objectData->idTypeMarker = 9;
		
		if ($data[3]=='3'){
			$objectData->status = 'Error';
		}
		return $objectData;
	}

	#
	## Devuelve un objeto con los datos necesarios para crear.
	#
	function prepareData($data){
		$character = array("{", "}");
		$objectData = new stdClass();
		$objectData->name = $data[0];
		$objectData->idUser = (int)$data[1];
		$objectData->title = $data[7];
		$objectData->commentary = $data[8];
		$objectData->timeZone = $data[12];
		$objectData->dateEnd = Utiles::convertDateUtc($data[4], $objectData->timeZone);
		$objectData->dateStart = Utiles::convertDateUtc($data[3], $objectData->timeZone);
		
		date_default_timezone_set($data[12]);
		$dateCreation = new DateTime();
		$objectData->dateCreation = Utiles::convertDateUtc($dateCreation->format('Y-m-d H:i:s'), $objectData->timeZone);
		$objectData->status = ($data[2]=='1')?'Ingresado':'Cancelado';
		$objectData->authorization = 'Reporte Atentus';
		$objectData->idTypeMarker = 9;
		$objectData->objetives = str_replace($character, "", $data[10]);
		
		$objectData->arrObj = $data[10];
		$objectData->arrNameObj = explode(',',$data[9]);
		if ($data[2]=='3'){
			$objectData->status = 'Error';
		}
		return $objectData;
	}

	#
	## Función para traer los datos de mantención eventos.
	#
	function getDataMaintance($cliente_id,$timeZone){
		global $mdb2;
		global $log;
		
		$sql = "SELECT DISTINCT ON (m.id) m.id,m.nombre, m.comentario, m.usuario_id, m.estado, m.fecha_inicio AT TIME ZONE ('$timeZone') as fecha_inicio, m.fecha_termino AT TIME ZONE ('$timeZone') as fecha_termino, m.fecha_creacion AT TIME ZONE ('$timeZone') as fecha_creacion, m.fecha_modificacion AT TIME ZONE ('$timeZone') as fecha_modificacion, m.titulo, m.objetivo_id FROM mantenimiento.mantenimiento as m INNER JOIN public.cliente_usuario as cl ON (m.usuario_id = cl.cliente_usuario_id) where cl.cliente_id = ".$cliente_id."";
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchAll()) {
			return $row;
		}
		else{
			return false;
		}
	}

	#
	## Función para traer los datos de mantención eventos según el id.
	#
	function getData($ids, $timeZone){
		global $mdb2;
		global $log;

		$sql = "SELECT id, nombre, comentario, usuario_id, estado, fecha_inicio AT TIME ZONE ('$timeZone') as fecha_inicio,fecha_termino AT TIME ZONE ('$timeZone') as fecha_termino, fecha_creacion AT TIME ZONE ('$timeZone') as fecha_creacion, fecha_modificacion AT TIME ZONE ('$timeZone') as fecha_modificacion, titulo, array_to_string(objetivo_id, ',') as objetivo_id from mantenimiento.mantenimiento where id in (".$ids.")";
		#print $sql;
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchAll()) {
			 return $row;
		}
		else{
			return false;
		}
	}
}
?>