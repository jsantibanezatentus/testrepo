<?
class NotificacionModal{

	var $notificacion_id;
	var $cuerpo;
	var $titulo;
	var $notificacion_tipo_id;

	function NotificacionModal() {
		$this->notificacion_id;
	}

	function getNotificationModal(){
		global $mdb2;
		global $log;
		global $usr;

		$sqlGlobal = "SELECT * FROM reporte.notificacion 
								WHERE  now() > notificacion_fecha_creacion and (cliente_id IS null AND cliente_usuario_id IS null AND notificacion_tipo_id = 1)";

		$sqlCliente = "SELECT * FROM reporte.notificacion 
								WHERE now() > notificacion_fecha_creacion 
								AND (cliente_id = ".$usr->cliente_id." AND notificacion_tipo_id = 2)";

		$sqlUsuario = "SELECT * FROM reporte.notificacion 
									WHERE now() > notificacion_fecha_creacion 
									AND (cliente_usuario_id = ".$usr->usuario_id." AND notificacion_tipo_id = 3)";

		$resGlobal =& $mdb2->query($sqlGlobal);
		if (MDB2::isError($resGlobal)) {
			$log->setError($sqlGlobal, $resGlobal->userinfo);
			exit();
		}
		$resCliente =& $mdb2->query($sqlCliente);
		if (MDB2::isError($resCliente)) {
			$log->setError($sqlCliente, $resCliente->userinfo);
			exit();
		}
		$resUsuario =& $mdb2->query($sqlUsuario);
		if (MDB2::isError($resUsuario)) {
			$log->setError($sqlUsuario, $resUsuario->userinfo);
			exit();
		}
		$notificacionModalClass = new NotificacionModal();
		$notificaciones = array();
		$notificacionesG = array();
		$notificacionesC = array();
		$notificacionesU = array();
		$cont=0;
		$empty='';
		$date=new DateTime();
		$date=$date->format('Y-m-d H:i:s');
		while($rowG =& $resGlobal->fetchRow()) {
			$validaNotificacionControl = $notificacionModalClass->getNotificationModalControl($rowG['notificacion_id'], $usr->usuario_id);
			
			if ($validaNotificacionControl == 0 and ($rowG["notificacion_fecha_termino"]==$empty or $rowG["notificacion_fecha_termino"]>$date)){
				$notificacion[$cont]['cuerpo'] = $rowG["notificacion_cuerpo"]; 
				$notificacion[$cont]['titulo'] = $rowG["notificacion_titulo"]; 
				$notificacion[$cont]['notificacion_id'] = $rowG["notificacion_id"]; 
				$notificacion[$cont]['notificacion_tipo_id'] = $rowG["notificacion_tipo_id"]; 
				$notificacionesG = $notificacion;
				$cont++;
			}
		}
		while($rowC =& $resCliente->fetchRow()) {
			$validaNotificacionControl = $notificacionModalClass->getNotificationModalControl($rowC['notificacion_id'], $usr->usuario_id);
			if ($validaNotificacionControl == 0 and ($rowC["notificacion_fecha_termino"]==$empty or $rowC["notificacion_fecha_termino"]>$date)) {
				$notificacion[$cont]['cuerpo'] = $rowC["notificacion_cuerpo"]; 
				$notificacion[$cont]['titulo'] = $rowC["notificacion_titulo"]; 
				$notificacion[$cont]['notificacion_id'] = $rowC["notificacion_id"]; 
				$notificacion[$cont]['notificacion_tipo_id'] = $rowC["notificacion_tipo_id"]; 
				$notificacionesC = $notificacion;
				$cont++;
			}
		}
		while($rowU =& $resUsuario->fetchRow()) {
			$validaNotificacionControl = $notificacionModalClass->getNotificationModalControl($rowU['notificacion_id'], $usr->usuario_id);
			if ($validaNotificacionControl == 0 and ($rowU["notificacion_fecha_termino"]==$empty or $rowU["notificacion_fecha_termino"]>$date)) {
				$notificacion[$cont]['cuerpo'] = $rowU["notificacion_cuerpo"]; 
				$notificacion[$cont]['titulo'] = $rowU["notificacion_titulo"]; 
				$notificacion[$cont]['notificacion_id'] = $rowU["notificacion_id"]; 
				$notificacion[$cont]['notificacion_tipo_id'] = $rowU["notificacion_tipo_id"]; 
				$notificacionesU = $notificacion;
				$cont++;
			}
		}
		if ($notificacionesG != null) {
			$notificaciones = $notificacionesG;
		}
		if ($notificacionesC != null) {
			$notificaciones = $notificacionesC;
		}
		if ($notificacionesU != null) {
			$notificaciones = $notificacionesU;
		}
		return $notificaciones;
	}

	function getNotificationModalControl($notificacion_id, $usuario_id){
		global $mdb2;
		global $log;
		global $usr;

		$sql = "SELECT COUNT(*) FROM reporte.notificacion_control 
						WHERE notificacion_id = ".$notificacion_id." 
						AND cliente_usuario_id=".$usuario_id;

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		$row =& $res->fetchRow();
		$bool = $row['count'];

		return $bool;
	}

	function getNotificationModalInsert($notificacion_id, $usuario_id){
		global $mdb2;
		global $log;

		$sql = "INSERT INTO reporte.notificacion_control (cliente_usuario_id, notificacion_id) VALUES (".$usuario_id.", ".$notificacion_id.")";

		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}	
	}
}