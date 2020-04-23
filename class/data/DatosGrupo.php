<?

class DatosGrupo {
	
	var $grupo_id;
	var $nombre;
	
	var $__monitores_ids;
	
	function DatosGrupo($grupo_id, $nombre) {
		$this->grupo_id = $grupo_id;
		$this->nombre = $nombre;
		
		$this->__monitores_ids = array();
	}
	
}

?>