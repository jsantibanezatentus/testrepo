<?

class DatosElemento {
	
	var $url;
	var $tamano_header;
	var $tamano_body;
	var $tipo;
	var $estado;
	var $ip;
	var $espera;
	var $latencia;
	var $descarga;
	var $tiempo_dns;
	var $http_status;
	var $paso;
	var $es_ok;
	
	var $promedio_tamanno;
	var $minimo_respuesta;
	var $maximo_respuesta;
	var $promedio_respuesta;
	var $cantidad;
	var $tiempo_total;
	var $tamano_total;
	var $fecha;
	
	var $__respuestas;
	var $__estados;
	
	function DatosElemento() {
		
		/* ARREGLOS SIMPLES CON DATOS(SIN OBJETOS DENTRO) */
		$this->__respuestas = array();
		$this->__estados = array();
	}
	
}

?>