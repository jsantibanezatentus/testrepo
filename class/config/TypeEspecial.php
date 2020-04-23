<?

class TypeEspecial {
	
	var $order;
	var $nombre;
	var $content;
	var $informe_id;
	var $class;
	var $method;
	
	function TypeEspecial($nombre, $content) {
		$this->nombre = $nombre;
		$this->content = $content;
	}
	
	function getContenido() {
		$clase_nombre = $this->class;
		$clase = new $clase_nombre();
		$clase->solicitud = $this->method;
	
		return $clase;
	}
	
}

?>