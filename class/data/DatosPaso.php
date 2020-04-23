<?

class DatosPaso {
	
	var $paso_id;
	var $nombre;
	
	var $promedio_respuesta;
	var $minimo_respuesta;
	var $maximo_respuesta;
	var $desviacion_respuesta;
	var $ip;
	var $estados;
	

	var $__eventos;
	var $__secuencias;
	var $__respuestas;
	var $__frecuencias;
	var $__datos; // Arreglo de nodos xml.
	var $__patrones;
	var $__excluye;
	var $__window;

	function DatosPaso($paso_id,$nombre) {
		$this->paso_id = $paso_id;
		$this->nombre = $nombre;

		$this->__eventos = array();
		$this->__secuencias = array();

		/* ARREGLOS SIMPLES CON DATOS(SIN OBJETOS DENTRO) */
		$this->__respuestas = array();
		$this->__frecuencias = array();
		$this->__patrones = array();
		$this->__excluye = array();
		$this->__window = array();
	}
	
	function getDatos() {
		$arr_eventos = array();
		foreach (Utiles::getElementsByArrayTagName($this->__datos, array("datos", "dato")) as $dato_pas) {
			$evento = new DatosPeriodo($dato_pas->getAttribute('inicio'), $dato_pas->getAttribute('termino'));
			$evento->evento_id = $dato_pas->getAttribute('evento_id');
//			$evento->fecha_inicio = $dato_pas->getAttribute('inicio');
//			$evento->fecha_termino = $dato_pas->getAttribute('termino');
			$arr_evento[] = $evento;
		}
		return $arr_evento;
	}
	
}

?>