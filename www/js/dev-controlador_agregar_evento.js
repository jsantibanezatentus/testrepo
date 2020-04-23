/* Controlador de acciones.
   Autor: Carlos Sepúlveda
   Objetivo: Manejar los eventos del formulario Agregar (Validaciónes, procesamiento de datos, envió de datos).

*/
$(document).ready(function()
{
	section = $('#seccion_mantenencion').data("seccion");
	calendarSection = $('#seccion_mantenencion').data("calendario");
	historySection = $('#seccion_mantenencion').data("historial");
	addSection = $('#seccion_mantenencion').data("agregar");
	
	$('#comentario').val('');
	timeZone = $('#zona_horaria').val();
	getObjetive();
	buildSelect();
	/*Inicializar multiselect.*/
	$(function(){
		$(".multiselect").multiselect();
		setearOptionMultiSelect();
			
	});
	/* Guarda todos los input que esten dentro del div.*/
	$("#formCrear").each(function(){
		input=$(this).find(':input');
	});
	
	/* Evento click sobre calendario*/
	$( "#calendario" ).click(function() {
	 		dialogCalendar();
	});

	/* Evento enviar.*/
	$("#guardar").click(function () {
		actionSend();
	});

	setValueDefault();
	/*$('.inputMaintainer').hover(

        function() {
        	border = $(this).css('border');
        	setCss(this, 'border', '2px solid rgba(0, 119, 255, 0.7)', false);
        },function() {
        	try {setCss(this, 'border', border, false);}
			catch(err) {}        	
        }
    );*/
    $('.icon').hover(

        function() {        	
        	setCss(this, 'zoom', 'document', false);
        },function() {
        	try {setCss(this, 'zoom', '', false);}
			catch(err) {}        	
        }
    );		

});
/* Función para setear estilos.*/
function setCss(clase, property, value, multiple){
	if (multiple == true){
		$(clase).css(value);
	}
	else{
		$(clase).css(property, value);	
	}	
}
/*Setea los valores por default (usuario id, id, nombre, usuario_id)*/
function setValueDefault(){
	$(input[0]).val($('#nombre_usuario').val());
	$(input[1]).val($('#usuario_cliente_id').val());
}
/* Maneja el evento del calendario. */
function loadCalendar(){
	jQuery(function($) {
		$('#containerMessage').remove();	 	
	    // Inicializa calendario
	    var $calendarioEspecial = $("#calendario_especial");

	    /* Evita duplicidad de elementos agregados mas abajo. */	    
	    $('#fecha_inicio_periodico').remove();
	    $('#fecha_termino_periodico').remove();
	    $('#guardarFecha').remove();
	    $('#hourStartInput').remove();
		$('#hourEndInput').remove();
	
	    // Establece parámetros
	    var params = {};

	    var fechaCalendario = "";
	    if(fechaCalendario.length > 0) {
	      params["fechaCalendario"] =  fechaCalendario + "00:00:00";
	    } 
	    params["mantenimiento"]=true;
	    
		var fechaMinima = "";
	    if(fechaMinima.length > 0) {
	      params["fechaMinima"] = fechaMinima + "00:00:00";
	    }
	    params["seleccion"] = {};
	    params["seleccion"]["activa"] = ("true" === "true");
	    params["seleccion"]["intervalo"] = ("true" === "true");
	    params["seleccion"]["mantenimiento"] = true;

	    $calendarioEspecial.calendariou(params);
		var calendariou = $calendarioEspecial.data("calendariou");

	    // Establece inputs
	    var $inputFechaInicio = $('<input type="hidden" name="fecha_inicio_periodico" id="fecha_inicio_periodico" value="">');
	    var $inputFechaTermino = $('<input type="hidden" name="fecha_termino_periodico" id="fecha_termino_periodico" value="">');
	    var $inputButton = $('<button class="btn" type="button" name="guardarFecha" id="guardarFecha" value="Definir" onclick="setInputDate();">Definir</button>');
	    var $span = $('<div id="containerMessage" class="form-dialog" style="display:none;"><span id="menssageAlert" ><div>');
	    var $br = $('<br/>');
	    $calendarioEspecial.append($inputFechaInicio, $inputFechaTermino);
	    $(".calendariou").append('<div id="nota" style="display: block; float: right; clear: right; width: 360px; height: 160px; color: #a2a2a2; overflow: hidden; font-size: small;"><p>Especifique el intervalo de inserción de registros.</p><p>Los intervalos deben ser expresados en formato HH:MM:SS, sin expresar zona horaria.</p>Ejemplo: 20:00:00<div');
	    
	    // Escucha cambios en selección para propagarlos a inputs correspondientes
	    calendariou.seleccion.el().on("calendariou:seleccion:cambio", function() {
	      var fechaInicio = calendariou.seleccion.get("fechaInicio");
	      var fechaTermino = calendariou.seleccion.get("fechaTermino");
	      //fechaTermino = fechaTermino.setDate(fechaTermino.getDate() - 1);
	      $inputFechaInicio.prop("value", fechaInicio === null ? null : fechaInicio.format("yyyy-mm-dd"));
	      $inputFechaTermino.prop("value", fechaTermino === null ? null : subtractOneDay(fechaTermino.format("yyyy-mm-dd")));
	      buildInputHour();
	      });
	    $calendarioEspecial.append($span);
	    $calendarioEspecial.append($br);
	    $calendarioEspecial.append($inputButton);
	});
}
/* Función asignada a una variable.*/
String.prototype.replaceAt=function(index, character) {
    return this.substr(0, index) + character ;
}
/* Función asignada a una variable que devuelve el largo.*/
String.prototype.largeStr=function() {
    return this.length;
}
/* Función para sumar un día */ 
function subtractOneDay(dateEnd){
	finalDate = (new Date(dateEnd))
	finalDate =finalDate.getTime()
	finalDate = (new Date(finalDate))
	year = String(finalDate.getFullYear())
	month = String(finalDate.getMonth()+1)
	day = String(finalDate.getDate())
	if (day.length==1){
		day='0'+(day);
	}
	if(month.length==1){
		month = '0'+month
	}
	finalDate = year+'-'+month+'-'+day
	return finalDate;
}
/*Maneja el evento que muestra contenido.*/
function setMessage(elem, message){
	elem.html(message);	
}
/* Cambia el color de los input, para distinguir los correctos de los incorrectos. */
function changeColor(input, colour){
	input.style.borderColor = colour;
}
/* Valida que los input esten correctos antes de setear las fechas y horas en sus input correspondientes.*/
function validateAfterSet(){
	hourStart = $('#hI');
	hourEnd = $('#hT');
	
	if (($('#hourStartInput').css('border-color')=='rgb(255, 0, 0)') || ($('#hourEndInput').css('border-color')=='rgb(255, 0, 0)')){
		return [false, 1];
	}
	else{
		if (($('#hourStartInput').val()=='') || ($('#hourEndInput').val()=='') || (typeof $('#hourStartInput').val()==='undefined') || (typeof $('#hourEndInput').val()==='undefined')){
			return [false, 2];
		}
		else{return [true,0];}
	}

}
/* Setea los valores en sus input.*/
function setInputDate(){
	dateStart = $('#fecha_inicio_periodico').val();
	dateEnd = $('#fecha_termino_periodico').val();
	returnVal = validateAfterSet()
	if (returnVal[0] == true){
		$('#dialog-calendario').dialog('close');
		displayMessage($('#containerMessage'), 'none', true);
		fechaInicio = $('#fecha_inicio_periodico').val() + ' ' + $('#hourStartInput').val();
		fechaTermino = $('#fecha_termino_periodico').val() + ' ' + $('#hourEndInput').val();

		$('#fechaI').val(fechaInicio);
		$('#fechaT').val(fechaTermino);

	}
	else{
		if (returnVal[1] == 1){
			setMessage($('#menssageAlert'), 'Formato de hora incorrecto.')
		}	
		else{
			setMessage($('#menssageAlert'), 'Debe seleccionar un rango de fecha y hora.')
		}
		displayMessage($('#containerMessage'), 'block', true);
	}	
}

/* Construye los input que contendran la hora.*/
function buildInputHour(){
	$('#hourStartInput').remove();
	$('#hourEndInput').remove();
	hourStart = $('#hI');
	hourEnd = $('#hT');
	hourStart.html('');
	hourEnd.html('');
	hourStart.append($('<input type="text" name="hourStartInput" id="hourStartInput" class="inputMaintainer" value=""  maxlength="8" onBlur="validateHour(this);"> '));
	hourEnd.append($('<input type="text" name="hourEndInput" id="hourEndInput" class="inputMaintainer" value=""  maxlength="8" onBlur="validateHour(this);" >'));
}

/* Muestra popup de calendario. */
function dialogCalendar(){
	controllerRemoveClass('.dia', 'seleccionado');
	displayMessage($('#containerMessage'), 'none', true);
	loadCalendar();
	$(document).ready(function()
	{		
		$( "#dialog-calendario" ).dialog({
	    	width:'800px',
	    	dialogClass: 'ui-dialog-osx',
		});
	});
}

/* Valida que la hora ingresada sea en formato correcto. */
function validateHour(inputHour){
	
	time = inputHour.value;	
	validate='ok';
	if (time.length!=8){
		validate='error';
	}	

	var reg = "^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$";

	valReg = time.search(reg);

	if (valReg != 0) { 
		validate='error';
	}
		
	if (validate == 'ok'){
		changeColor(inputHour, 'green'); 
	}
	
	else{
		changeColor(inputHour, 'red'); 
	}  
}

/* Permite ocultar.*/
function hiddenSlow(id){
	$(id).fadeOut(4000);
}
/*Maneja el evento display.*/
function displayMessage(elem, display, hidden){
	elem.css("display", display);
	if (hidden == true){
		hiddenSlow(elem.selector);	
	}	
}
/* Permite remover clases*/ 
function controllerRemoveClass(classSelec, removeClass){
	$(classSelec).removeClass(removeClass);
}
function setearOptionMultiSelect(){
	$('.ui-multiselect').hover(

        function() {
        	border = $(this).css('border');
        	setCss(this, 'border', '2px solid rgba(0, 119, 255, 0.7)', false);
        },function() {
        	try {setCss(this, 'border', border, false);}
			catch(err) {}        	
        }
    );
}

/* Función para crear agregar option al select y definir los objetivos seleccionados.*/
function buildSelect(response, arr_objetive){
	$.each( OBJETIVES, function( key, value ) {
		objetiveId = key;
		name = value.nombre;
		nameObj = name.largeStr()>46?(name.substring(1, 46)+' ...'):name;
		$('#objetive').append("<option value="+key+" >"+nameObj+" </option></option>");
	});		
}

/* Función encargada de realizar llamada ajax que obtiene los objetivos del cliente.*/
function getObjetive(){

	return $.ajax({
		async: false,
		url: '../call_ajax.php',
		data: {'nameFunction':'getObjetive' },
		type: 'POST',
        success: function(response) {
        	saveData(response);
            
        },
        error: function(error) {
        }
    });
    
}

/* Evento del boton enviar. */
function actionSend(){
	var text = null;
	var type = null;
	$('#containerMessage').remove();
	var span = $('<div id="containerMessage" class="form-dialog" style="display:none;"><span id="menssageAlertForm" ><div>');
	$('#inputSave').prepend(span);
	if (validateForm() == true){

		value = buildArrayValues();
		data = JSON.stringify(value);
		$.ajax({
			async: false,
	        url: '../call_ajax.php',
	        type: 'POST',
	        data: { 'data': data, 'nameFunction':'queryEvent' },
	        success: function(response) {
	        	if (response['status'] =='error-duplicate'){
	        		text = 'Error: Ya existe un evento de mantenimiento asociado al objetivo '+response['nameObj']+' para el periodo indicado, favor de revisar datos.';
	        		type = 'error';
	        		displayMessageForm(type,text);
					//setTimeout(function() {mostrarSubmenu(133,134,1);},3000);	
	        		      		       		
	        	}
	        	else if(response['status'] =='success'){

	        		text = 'Datos ingresados correctamente.';
	        		type = 'success';
	        		displayMessageForm(type,text);
					setTimeout(function() {mostrarSubmenu(section,historySection,1);},3000);
				}
				else if(response['status'] =='error-nodo'){

	        		text = 'El objetivo '+response['nameObj']+' no tiene monitor asignado.';
		        	type = 'error';
		        	displayMessageForm(type,text);
				}
				else{

					text = 'Ha ocurrido un error en el procesamiento de los datos.';
					type = 'error';
					displayMessageForm(type,text);
					setTimeout(function() {mostrarSubmenu(section,historySection,1);},3000);		        	
				}
				
	        	
		 	},
	        error: function(error) {

	        	saveEventError(value);
	        	text = 'Ha ocurrido un error en el procesamiento de los datos.';
	        	displayMessageForm('error',text);
	        	setTimeout(function() {mostrarSubmenu(section,historySection,1);;},3000);
			}
    	});
	}
	else{
		text = 'Por favor revise campos en rojo antes de guardar.';
		displayMessageForm('error',text);
	}
}
/* Permite el manejo de mensajes de error y success*/
function displayMessageForm(typeMessage,text){
	if (typeMessage == 'success'){
		setMessage($('#menssageAlertForm'), text);
		displayMessage($('#containerMessage'), 'block', true);
		setColor($('#containerMessage'), '#D0F5A9');
		setCss('#containerMessage', null, {'color':'#468847',
										   'background-color':'dff0d8',
										   'border-color':'#d6e9c'
										   }, true);
	}
	else{
	setMessage($('#menssageAlertForm'), text);
	displayMessage($('#containerMessage'), 'block', true);
	setColor($('#containerMessage'), '#F5A9A9');
	setCss('#containerMessage', null, {'color':'#b94a48',
									   'background-color':'f2dede',
									   'border-color':'#ebccd1'
									   }, true);
	}
}
/* Llamada para guardar los datos con un estado error!*/
function saveEventError(value){
	value[2] = '3';
	var data = JSON.stringify(value);
	$.ajax({
			async: false,
	        url: '../call_ajax.php',
	        type: 'POST',
	        data: { 'data': data,'nameFunction':'queryEvent' },
	        success: function(response) {
	        	mostrarSubmenu(section,historySection,1);
		 	},
	        error: function(error) {}
    });
}
/* Toma los id de objetivos agregados(multiselect). */
function getValueMultiSelect(){
	var count = 0;
	var objetives = "{";
	$('.ui-multiselect .selected li').each(function(idx,el){
	    var link = $(el).data('optionLink');
	    if(link){
	    	if (count == 1){
	    		objetives = objetives+(link.val());
	    	}
	    	else{
	    		objetives = objetives+","+(link.val());
	    	}
	    }
	    count++;
	});
	objetives = objetives+"}";
	return objetives;

}
/* Construye el array con valores. */
function buildArrayValues(){

	var valueInput = new Array();
	var dicObjetive = new Array();		
	var objetives = getValueMultiSelect();
	var text = '';
	$.each( input, function( index, value ) {
		if (index == 9){
			for (var i = 0; i < $('#objetive option:selected').length; i++) {
				text = text + $("#objetive option[value="+($(value).val())[i]+"]").text()+',';
			}
			valueInput[index]=text;
		}
		else{
			valueInput[index]=$(value).val();
		}
	});
	valueInput[0] = $('#nombre_usuario').val();
	valueInput[1] = $('#usuario_cliente_id').val();
	valueInput[10] = objetives;
	valueInput[11] = 'Nuevo';
	valueInput[12] = timeZone;
	return valueInput;
}
/*Maneja el evento color.*/
function setColor(elem, colour){
	elem.css("background-color", colour);
}
/* Valida el formulario antes de enviarlos.*/
function validateForm(){
	var status = document.getElementById('estado');
	var startDate = document.getElementById('fechaI');
	var endDate = document.getElementById('fechaT');
	var titulo = document.getElementById('titulo');
	var comentario = document.getElementById('comentario');
	
	var validate = true;
	var regex = /^[0-9][0-9][0-9][0-9]-([0-9]|0[0-9]|1[0-2])-([0-9]|0[0-9]|1[0-9]|2[0-9]|3[0-1]) ([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/;
	var regStatus = /^[1-2]$/
	
	if (status.value.search(regStatus) !=0){
		validate = false;
		changeColor(status, 'red');
	}
	else{changeColor(status, 'green');}
	if (startDate.value.search(regex)!=0 ){
		validate = false;
		changeColor(startDate, 'red'); 
	}
	else{changeColor(startDate, 'green'); }
	if (endDate.value.search(regex) != 0){
		validate = false;
		changeColor(endDate, 'red'); 
	}
	else{changeColor(endDate, 'green'); }
	if (titulo.value == ""){
		validate = false;
		changeColor(titulo, 'red'); 
	}
	else{changeColor(titulo, 'green'); }
	if (comentario.value == ""){
		validate = false;
		changeColor(comentario, 'red'); 
	}
	else{changeColor(comentario, 'green'); }
	
	startDateFormat = moment(startDate.value);
  	endDateFormat = moment(endDate.value);

  	$('#startDate').html("startDate: " + startDateFormat.format());
	$('#endDate').html("endDate: " + endDateFormat.format());
	resultHours = endDateFormat.diff(startDateFormat, 'hours', true);
	
	if (resultHours <= 0){validate = false;changeColor(startDate, 'red');}
	else{changeColor(startDate, 'green');}
	
	if ($(".selected ul li").length < 2){validate = false;setCss('.ui-multiselect', 'border', '2.5px solid rgba(255, 0, 0, 0.7)', false);}
	else{setCss('.ui-multiselect', 'border', '1px solid rgb(244, 112, 1)', false);}
	
	return validate;
}
/* Variable global. */
function saveData(response){
	OBJETIVES =JSON.parse(response);
}