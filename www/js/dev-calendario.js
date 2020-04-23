/**
 * Clase javascript para el Calendario Periodico.
 * 
 * @param contenedor
 * @param fecha_inicio
 * @param fecha_termino
 * @param nombre_clase
 */
function CalendarioPeriodico(contenedor, fecha_inicio, fecha_termino, nombre_clase) {
	
	this.contenedor = contenedor;
	this.fecha_inicio = fecha_inicio;
	this.fecha_termino = fecha_termino;
	this.nombre_clase = nombre_clase;
	
	/**
	 * Funcion que se ejecuta cuando se crea el calendario periodico.
	 */
	this.cargarCalendario = function(fecha_inicio, fecha_termino) {
		var calendario = this;
		var accion='mostrar_calendario_periodico';
		var objeto_id = document.form_principal.objeto_id.value;
		dojo.xhrPost({
			url: "calendario.php?accion="+accion+"&nombre_clase="+this.nombre_clase+"&objeto_id="+objeto_id+
				 "&fecha_inicio_periodo="+fecha_inicio+"&fecha_termino_periodo="+fecha_termino+"&ajax=1",
			load: function(data){
				if (data.trim() === "LOGOUT") {
					logout();
					return;
				}
				document.getElementById(calendario.contenedor).innerHTML = data;
				calendario.fecha_inicio = document.getElementById("fecha_inicio_periodico").value;
				calendario.fecha_termino = document.getElementById("fecha_termino_periodico").value;
				calendario.setRangoCalendario(1);
			}
		});
	};

	this.seleccionarPeriodo = function(fecha1, fecha2) {
		this.setRangoCalendario(0);
		this.setFechaInicio(fecha1);
		this.setFechaTermino(fecha2);
		this.setRangoCalendario(1);
	};
	
	this.seleccionarDia = function(dia_id) {
		var dia_split = dia_id.split("|");
		
		this.setRangoCalendario(0);
		this.setFechaInicio(dia_split[1]);
		this.setFechaTermino(dia_split[1]);
		this.setRangoCalendario(1);
	};
	
	this.seleccionarSemana = function(semana_id) {
		var semana_split = semana_id.split("|");
		var fecha1 = semana_split[1];
		var fecha1_split = fecha1.split("-"); 
		var fecha2_date = new Date(fecha1_split[0],(parseInt(fecha1_split[1],10)-1),(parseInt(fecha1_split[2],10)+6));

		this.setRangoCalendario(0);
		this.setFechaInicio(fecha1);
		this.setFechaTermino(this.getDateToString(fecha2_date));
		this.setRangoCalendario(1);
	};
	
	/**
	 * Funcion que pinta el calendario.
	 * Si tipo = 0, pinta todo de blanco (no seleccionado).
	 * Si tipo = 1, pinta lo seleccionado entre fecha inicio y fecha termino.
	 */
	this.setRangoCalendario = function(tipo) {
		if (tipo==0) {
			if (document.getElementById("periodo|"+this.fecha_inicio+"_"+this.fecha_termino)) {
				document.getElementById("periodo|"+this.fecha_inicio+"_"+this.fecha_termino).style.backgroundColor="#ffffff";
			}
		}
		else {
			if (document.getElementById("periodo|"+this.fecha_inicio+"_"+this.fecha_termino)) {
				document.getElementById("periodo|"+this.fecha_inicio+"_"+this.fecha_termino).style.backgroundColor="#f7af72";
			}
		}
		
		var fecha_actual_date = this.getStringToDate(this.fecha_inicio);
		var fecha_inicio_date = this.getStringToDate(this.fecha_inicio);
		for (var i=fecha_inicio_date.getDate()+1;fecha_actual_date<=this.getStringToDate(this.fecha_termino);i++) {
			if (document.getElementById("dia|"+this.getDateToString(fecha_actual_date))) {
				if (tipo==0) {
					document.getElementById("dia|"+this.getDateToString(fecha_actual_date)).style.backgroundColor="#ffffff";
				}
				else {
					document.getElementById("dia|"+this.getDateToString(fecha_actual_date)).style.backgroundColor="#f7af72";
				}
			}
			fecha_actual_date = new Date(fecha_inicio_date.getFullYear(),fecha_inicio_date.getMonth(),i);
		}
	};

	this.setFechaInicio = function(fecha) {
		this.fecha_inicio = fecha;
		document.getElementById("fecha_inicio_periodico").value = fecha;
	};

	this.setFechaTermino = function(fecha) {
		this.fecha_termino = fecha;
		document.getElementById("fecha_termino_periodico").value = fecha;
	};

	this.getStringToDate = function(fecha) {
		var fecha_split = fecha.split("-");
		var fecha_date = new Date(fecha_split[0],(parseInt(fecha_split[1],10)-1),fecha_split[2]);
		return fecha_date;
	};

	this.getDateToString = function(fecha) {
		var dia;
		var mes;
		if (fecha.getUTCDate()<10) {
			dia = "0"+fecha.getUTCDate();
		}
		else {
			dia = fecha.getUTCDate();
		}
		if ((parseInt(fecha.getMonth(),10)+1)<10) {
			mes = "0"+(parseInt(fecha.getMonth(),10)+1);
		}
		else {
			mes = (parseInt(fecha.getMonth(),10)+1);
		}

		var fecha_string = fecha.getFullYear()+"-"+mes+"-"+dia;
		return fecha_string;
	};

	this.cargarCalendario(fecha_inicio, fecha_termino);
}

/**
 * Clase javascript para el Calendario Online.
 * 
 * @param contenedor
 * @param fecha_inicio
 * @param fecha_termino
 * @param nombre_clase
 * @return
 */
function Calendario(contenedor, fecha_inicio, fecha_termino, nombre_clase) {

	this.contenedor = contenedor;
	this.fecha_inicio = fecha_inicio;
	this.fecha_termino = fecha_termino;
	this.nombre_clase = nombre_clase;
	
	this.dia_default = 1;
	
	/**
	 * Funcion que utilizando ajax, llama a un php que dibuja el calendario.
	 */
	this.cargarCalendario = function(fecha_mostrada) {
		var objeto_id = document.form_principal.objeto_id.value;
		var calendario = this;

		dojo.xhrPost({
			url: 'calendario.php?fecha_mostrada='+fecha_mostrada+'&nombre_clase='+this.nombre_clase+"&objeto_id="+objeto_id+"&ajax=1",
			load: function(data){
				if (data.trim() === "LOGOUT") {
					logout();
					return;
				}
				document.getElementById(calendario.contenedor).innerHTML = data;
				calendario.setFechaInicio(calendario.fecha_inicio);
				calendario.setFechaTermino(calendario.fecha_termino);
				calendario.setRangoCalendario(1);
			}
		});
	};
	
	/**
	 * Funcion que se utiliza cuando se selecciona un dia en el calendario.
	 */
	this.seleccionarDia = function(dia_id) {
		var dia_split = dia_id.split("|");
		var fecha = dia_split[1];
		if (this.dia_default==1) {
			this.dia_default = 0;
			this.setRangoCalendario(0);
			this.setFechaInicio(fecha);
			this.setFechaTermino(fecha);
			this.setRangoCalendario(1);
		}
		else if (this.fecha_inicio == this.fecha_termino) {
			if (this.getStringToDate(fecha)<this.getStringToDate(this.fecha_inicio)) {
				this.setFechaInicio(fecha);
			}
			else {
				this.setFechaTermino(fecha);
			}
			this.setRangoCalendario(1);
		}
		else {
			this.setRangoCalendario(0);
			this.setFechaInicio(fecha);
			this.setFechaTermino(fecha);
			this.setRangoCalendario(1);
		}
	};
	
	/**
	 * Funcion que se utiliza cuando se selecciona una semana en el calendario.
	 */
	this.seleccionarSemana = function(semana_id) {
		var semana_split = semana_id.split("|");
		var fecha1 = semana_split[1];
		var fecha1_split = fecha1.split("-"); 
		var fecha2_date = new Date(fecha1_split[0],(parseInt(fecha1_split[1],10)-1),(parseInt(fecha1_split[2],10)+6));

		this.setRangoCalendario(0);
		this.setFechaInicio(fecha1);
		this.setFechaTermino(this.getDateToString(fecha2_date));
		this.setRangoCalendario(1);
	};
	
	/**
	 * Funcion que se utiliza cuando se selecciona un mes en el calendario.
	 */
	this.seleccionarMes = function(mes_id) {
		var mes_split = mes_id.split("|");
		var fecha1 = mes_split[1];
		var fecha1_split = fecha1.split("-");
		var fecha2_date = new Date(fecha1_split[0],parseInt(fecha1_split[1],10),(parseInt(fecha1_split[2],10)-1));
		
		this.setRangoCalendario(0);
		this.setFechaInicio(fecha1);
		this.setFechaTermino(this.getDateToString(fecha2_date));
		this.setRangoCalendario(1);
	};

	/**
	 * Funcion que se utiliza cuando se cambia una fecha en los textbox del calendario.
	 */
	this.seleccionarInput = function() {
		var exp_fecha = /^\d{1,2}\/\d{1,2}\/\d{2,4}$/;
		
		var fecha1 = document.getElementById("fecha_inicio_sel").value;
		var fecha2 = document.getElementById("fecha_termino_sel").value;
		var fecha1_split = fecha1.split("/");
		var fecha2_split = fecha2.split("/");
		var fecha1_date = this.getStringToDate(fecha1_split[2]+"-"+fecha1_split[1]+"-"+fecha1_split[0]);
		var fecha2_date = this.getStringToDate(fecha2_split[2]+"-"+fecha2_split[1]+"-"+fecha2_split[0]);
		var now_date = new Date();
		var fecha_tope_date = new Date(now_date.getFullYear(),now_date.getMonth()-6,1);

		document.getElementById("fecha_inicio_sel").value = this.getFormatoFecha(this.fecha_inicio);
		document.getElementById("fecha_termino_sel").value = this.getFormatoFecha(this.fecha_termino);

		if (!fecha1.match(exp_fecha) || !fecha2.match(exp_fecha)) {
			alert("Las fechas estan mal ingresadas.");
		}
		else if (fecha1_date > fecha2_date) {
			alert("La fecha de inicio no puede ser mayor que la fecha de termino.");
		}
		else if (fecha1_date > now_date || fecha2_date > now_date) {
			alert("El rango de busqueda no puede superar la fecha actual.");
		}
		else if (fecha1_date < fecha_tope_date || fecha2_date < fecha_tope_date) {
			alert("El rango de busqueda no puede ser anterior a 6 meses.");
		}
		else {
			this.setRangoCalendario(0);
			this.setFechaInicio(this.getDateToString(fecha1_date));
			this.setFechaTermino(this.getDateToString(fecha2_date));
			this.setRangoCalendario(1);
		}
	};
	
	/**
	 * Funcion que pinta el calendario.
	 * Si tipo = 0, pinta todo de blanco (no seleccionado).
	 * Si tipo = 1, pinta lo seleccionado entre fecha inicio y fecha termino.
	 */
	this.setRangoCalendario = function(tipo) {
		var fecha_actual_date = this.getStringToDate(this.fecha_inicio);
		var fecha_inicio_date = this.getStringToDate(this.fecha_inicio);
		for (var i=fecha_inicio_date.getDate()+1;fecha_actual_date<=this.getStringToDate(this.fecha_termino);i++) {
//			alert(fecha_actual_date);
			if (document.getElementById("dia|"+this.getDateToString(fecha_actual_date))) {
				if (tipo==0) {
					document.getElementById("dia|"+this.getDateToString(fecha_actual_date)).style.backgroundColor="#ffffff";
				}
				else {
					document.getElementById("dia|"+this.getDateToString(fecha_actual_date)).style.backgroundColor="#f7af72";
				}
			}
			fecha_actual_date = new Date(fecha_inicio_date.getFullYear(),fecha_inicio_date.getMonth(),i);
		}
	};
	
	/**
	 * Funcion que setea la fecha de inicio de la seleccion.
	 * Tambien cambia el textbox de la fecha inicio.
	 */
	this.setFechaInicio = function(fecha) {
		this.fecha_inicio = fecha;
		document.getElementById("fecha_inicio").value = fecha;
		document.getElementById("fecha_inicio_sel").value = this.getFormatoFecha(fecha);
	};

	/**
	 * Funcion que setea la fecha de termino de la seleccion.
	 * Tambien cambia el textbox de la fecha termino.
	 */
	this.setFechaTermino = function(fecha) {
		var fecha1_split = this.fecha_inicio.split("-");
		var fecha_tope_date = new Date(fecha1_split[0],(parseInt(fecha1_split[1],10)+2),fecha1_split[2]);
		if (this.getStringToDate(fecha) > fecha_tope_date) {
			alert("El rango de busqueda no puedo superar los 3 meses.");
			fecha = this.fecha_inicio;
		}

		this.fecha_termino = fecha;
		document.getElementById("fecha_termino").value = fecha;
		document.getElementById("fecha_termino_sel").value = this.getFormatoFecha(fecha);
	};
	
	/**
	 * Funcion que recibe un string y devuelve una fecha.
	 */
	this.getStringToDate = function(fecha) {
		var fecha_split = fecha.split("-");
		var fecha_date = new Date(fecha_split[0],(parseInt(fecha_split[1],10)-1),fecha_split[2]);
		return fecha_date;
	};

	/**
	 * Funcion que recibe una fecha y devuelve un string.
	 */
	this.getDateToString = function(fecha) {
		var dia;
		var mes;
		if (fecha.getUTCDate()<10) {
			dia = "0"+fecha.getUTCDate();
		}
		else {
			dia = fecha.getUTCDate();
		}
		if ((parseInt(fecha.getMonth(),10)+1)<10) {
			mes = "0"+(parseInt(fecha.getMonth(),10)+1);
		}
		else {
			mes = (parseInt(fecha.getMonth(),10)+1);
		}

		var fecha_string = fecha.getFullYear()+"-"+mes+"-"+dia;
		return fecha_string;
	};

	/**
	 * Funcion que tranforma una fecha de tipo YYYY-m-d a dd-mm-YYYY.
	 */
	this.getFormatoFecha = function(fecha) {
		var fecha_split = fecha.split("-");
		var dia;
		var mes;
		if (parseInt(fecha_split[2],10)<10) {
			dia = "0"+parseInt(fecha_split[2],10);
		}
		else {
			dia = parseInt(fecha_split[2],10);
		}
		if (parseInt(fecha_split[1],10)<10) {
			mes = "0"+parseInt(fecha_split[1],10);
		}
		else {
			mes = parseInt(fecha_split[1]);
		}
		return dia+"/"+mes+"/"+fecha_split[0];
	};

	/**
	 * Se inicia la carga del calendario.
	 */
	this.cargarCalendario(fecha_termino);
	
};

