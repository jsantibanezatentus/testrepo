<?

class PonderacionItem {
	
	var $inicio;
	var $termino;
	var $valor;
	
	var $__ponderacion_id;

	function PonderacionItem() {
		
	}
	
	function agregar() {
		global $mdb2;
		global $log;
		global $current_usuario_id;
		
		$sql = "INSERT INTO cliente_ponderacion_item(ponderacion_id, inicio, termino, valor) ".
			   "VALUES (".
				pg_escape_string($this->__ponderacion_id).", '".
				pg_escape_string($this->inicio)."', '".
				pg_escape_string($this->termino)."', '".
				pg_escape_string($this->valor)."')";
//		print($sql."<br>");
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}
	
}


?>