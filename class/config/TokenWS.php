<?php

class TokenWS {
	
	var $token_id;
	var $nombre;
	var $key;
	var $fecha_creacion;
	var $fecha_expiracion;
	
	public function TokenWS($token_id) {
		$this->token_id = $token_id;
	}
	
	public function generarKey() {
		global $current_usuario_id;
		
		$tope = rand(5, 15);
		$base = "abcdefghijklmnopqrstuvwxyz0123456789";
		
		$string = "";
		for ($i=0; $i<$tope; $i++) {
			$string.=$base[rand(0, 35)];
		}
		
		$key = md5($current_usuario_id.time()).$string;
		
		return $key;
	}
	
	public function agregar() {
		global $current_usuario_id;
		global $mdb2;
		global $log;
		
		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		
		Validador::campoVacio($this->nombre, "Nombre");
		
		$this->key = $this->generarKey();
		
		$sql = "INSERT INTO public.webservices_token(cliente_id, nombre, key) VALUES (".
			   "(SELECT cliente_id FROM cliente_usuario WHERE cliente_usuario_id=".
			   pg_escape_string($current_usuario_id)."),'".
			   pg_escape_string($this->nombre)."','".
			   pg_escape_string($this->key)."')";

//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}
	
	public function modificar() {
		global $current_usuario_id;
		global $mdb2;
		global $log;
		
		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		
		Validador::campoVacio($this->nombre, "Nombre");
		
		$this->key = $this->generarKey();
		
		$sql = "UPDATE public.webservices_token ".
			   "SET nombre = '".pg_escape_string($this->nombre)."' ".
			   "WHERE cliente_id in ".
			   "(SELECT cliente_id FROM cliente_usuario WHERE cliente_usuario_id=".
			   pg_escape_string($current_usuario_id).") AND token_id = ".
			   pg_escape_string($this->token_id);

//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}
	
	public function eliminar() {
		global $current_usuario_id;
		global $mdb2;
		global $log;
		
		if ($mdb2->only_read) {
			throw new Exception('Por el momento esta trabajando solo en modo lectura.');
		}
		
		$sql = "DELETE FROM public.webservices_token WHERE cliente_id in ".
			   "(SELECT cliente_id FROM cliente_usuario WHERE cliente_usuario_id=".
			   pg_escape_string($current_usuario_id).") AND token_id = ".
			   pg_escape_string($this->token_id);

//		print($sql);
		$res =& $mdb2->query($sql);
		if (MDB2::isError($res)) {
			$log->setError($sql, $res->userinfo);
			exit();
		}
	}
	
}

?>