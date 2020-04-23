<?

class Ponderacion {
	
	var $ponderacion_id;
	var $nombre;
	var $intervalo;
	
	var $__items;
	
	function Ponderacion() {
		
	}
	
	function getPonderacionItems() {
		global $mdb2;
		global $log;
		
		$sql = "SELECT *, extract(hour from inicio) as hora_inicio, extract(hour from termino) as hora_termino ".
			   "FROM public.cliente_ponderacion_item ".
			   "WHERE ponderacion_id=".pg_escape_string($this->ponderacion_id);
//		print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		
		$this->__items = array();
		while ($row = $res->fetchRow()) {
			$item = new PonderacionItem();
			$item->inicio = $row["hora_inicio"];
			$item->termino = $row["hora_termino"];
			$item->valor = $row["valor"];
//			$item->ponderacion_id = $row["ponderacion"];
			$this->__items[] = $item;
		}
		return $this->__items;
	}
	
	function eliminarItems() {
		global $mdb2;
		global $log;
		
		$sql = "DELETE FROM public.cliente_ponderacion_item WHERE ponderacion_id = ".pg_escape_string($this->ponderacion_id);
//		print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}
	
	function agregar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		$sql = "INSERT INTO cliente_ponderacion(cliente_id, nombre, intervalo) ".
			   "VALUES ((SELECT cliente_id FROM cliente_usuario WHERE cliente_usuario_id=".pg_escape_string($current_usuario_id)."), '".
				pg_escape_string($this->nombre)."', ".pg_escape_string($this->intervalo).")";
//		print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		
		$sql = "SELECT MAX(ponderacion_id) AS ponderacion_id FROM cliente_ponderacion WHERE cliente_id = (SELECT cliente_id FROM cliente_usuario WHERE cliente_usuario_id=".pg_escape_string($current_usuario_id).")";
//		print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
		if ($row = $res->fetchRow()) {
			$this->ponderacion_id = $row["ponderacion_id"];
		}
	}
	
	function modificar() {
		global $mdb2;
		global $log;
		
		$sql = "UPDATE cliente_ponderacion SET intervalo=".pg_escape_string($this->intervalo)." WHERE ponderacion_id=".pg_escape_string($this->ponderacion_id);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}
}

?>