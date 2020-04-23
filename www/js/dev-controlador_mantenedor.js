/* 
Controlador de acciones y procesamiento de datos.
Autor: Carlos Sepúlveda
Objetivo: Manejar los eventos del formulario Editar y tabla historial (Validaciónes, procesamiento de datos, envió de datos).
*/
dateFull = null;
$(document).ready(function()
{
   		
   eventId = $('#evento_id').val();
   clientId = $('#cliente_id').val();
   clientName = $('#nombre_cliente').val();
   nameUser = $('#nombre_usuario').val();
   statusEvent = null;
   
   // Sirven para saber si el datatable o el multiselect han sido inicializados.
   count = 0;
   countDataTableInit = 0;

	timeZone = $('#zona_horaria').val();
	getObjetive();
	
	/* Eventos.*/
	$('#dialog-message').on('dialogclose', function(event) {
    });
 	$( "#calendario" ).click(function() {
 		dialogCalendar();
	});
	$("#dialog-message").each(function(){
		input=$(this).find(':input');
	});
	$("#guardar").click(function () {
		var status = document.getElementById('estado');
		if(status.value==2){
			var result = window.confirm('¿Está seguro que desea cancelar el mantenimiento? (Esta acción no se puede revertir).');
        	if (result == true) {
        		actionSend();
    			getRegister();
        	};
		}else{
			actionSend();
			getRegister();
		}
        
		
	});	
	$( "#close" ).click(function() {
 		$( "#dialog-message").dialog( "close" );
	});

	$( "#descargar_csv" ).click(function() {
 		downloadCsv();
 	});

	setCss('#containerMessage', 'display', 'none', false);

	(function ($) {
        /*Obtiene los registros buscador*/
        getRegister();
    }(jQuery));   

   /* $('.inputMaintainer, .search').hover(
        function() {
        	border = $(this).css('border')
        	setCss(this, 'border', '2px solid rgba(0, 119, 255, 0.7)', false);
        }, function() {
        	setCss(this, 'border', border, false);
        }
    );*/
    
    /* Estilos para la tabla de registros.*/
    setCss('.dataTables_length', null, {"color":"#616265",'font-family': 'Verdana,Arial,Helvetica,sans-serif'}, true);
    setCss('.dataTables_info', null, {"color":"#616265",'font-family': 'Verdana,Arial,Helvetica,sans-serif'}, true);
    
});


/* Función que que realiza la llamada ajax para obtener los registros. */
function getRegister(){
	$.ajax({
		async: false,
        url: '../call_ajax.php',
        data:{'clientId':clientId, 'nameFunction':'getRegisterMaintaince'},
        type: 'POST',
        success: function(response) {
            BuildDataTable(response);
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
	        	}
	        	else if(response['status'] =='success'){
	        		text = 'Datos ingresados correctamente.';
	        		type = 'success';				
				}
				else if(response['status'] =='error-nodo'){
	        		text = 'El objetivo '+response['nameObj']+' no tiene monitor asignado.';
	        		type = 'error';
	        	}
				else{
					text = 'Ha ocurrido un error en el procesamiento de los datos.';
					type = 'error';
		       	}
				displayMessageForm(type,text);
				setTimeout(function() {$( "#dialog-message").dialog( "close" );},3000);
	        },
	        error: function(error) {	        	
	        	text = 'Ocurrio un error al procesar los datos.';
	        	displayMessageForm('error',text);
	        }
    	});
	}
	else{
		text = 'Por favor revise campos en rojo antes de guardar.';
		displayMessageForm('error',text);
	}
}   

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
/* Función para obtener todos los objetivos activos.*/
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


/* Función para obtener todos los objetivos del mantenimiento.*/
function getObjetiveid(ids){
var objetive;
	 $.ajax({
		async: false,
		url: '../call_ajax.php',
		data: {'ids':ids ,'nameFunction':'getObjetiveId' },
		type: 'POST',
        success: function(response) {
        	
        	objetive=  response;
            
        },
        error: function(error) {
        }
    });
    return objetive;
}


function showEvent(objetiveId){
	objetiveId = objetiveId.replace("{"," ");
	objetiveId = (objetiveId.replace("}"," ")).trim();

	var array = objetiveId.split(',');
	var show = false;
	$.each(OBJETIVES,function(index,value) {
		if (jQuery.inArray(value.objetivo_id, array)!='-1') {
			show = true;            
        }
    });
	return show;
}
/* Función que construye la tabla. */
function BuildDataTable(response){
	var usuarioId = parseInt($('#usuario_cliente_id').val());
	countDataTableInit = countDataTableInit + 1;
	allRegister = JSON.parse(response);
	$('#register').empty();

	$('#example').DataTable().fnClearTable(this);
	$( "#load_evento" ).hide();
	$.each(allRegister,function(index,value) {
		var idIcon=value.id + "-icon";
		var status = setStatus(value.estado);
		var dateStart = value.fecha_inicio;
		var dateEnd = value.fecha_termino;
		if (showEvent(value.objetivo_id) == true){
			ids =value.objetivo_id;
			ids=ids.replace('{',"");
			ids=ids.replace('}',"");
			ids='"'+ids+'"';
			if (value.usuario_id == usuarioId){
				element = "#"+idIcon;
				$('#register').append("<tr><td>"+value.id+"</td><td >"+value.nombre+"</td><td >"+dateStart+"</td><td>"+dateEnd+"</td><td >"+value.titulo+"</td><td >"+status+"</td><td> <a href='#' onclick='getObjetivos("+ids+");' >Ver todos</a></td><td ><a href='#' onclick='editMaintance("+JSON.stringify(value)+","+ids+");' ><i class='spriteButton spriteButton-editar' id= "+idIcon+" border='0' title='editar'></i></a></td></tr>");
				
				$(element).hover(function() {
					setCss(this, 'zoom', '1.5', false);
	        	},function() {
	        		try {setCss(this, 'zoom', '', false);}
					catch(err) {}       	
	        	}
	    		);	
			}
			else{
				$('#register').append("<tr><td>"+value.id+"</td><td >"+value.nombre+"</td><td >"+dateStart+"</td><td>"+dateEnd+"</td><td >"+value.titulo+"</td><td >"+status+"</td><td><a href='#' onclick='getObjetivos("+ids+");' >Ver todos</a></td><td ></td></tr>");
			}
		}
		
   	});

   	if(countDataTableInit > 1){

   		$('#example').DataTable().fnDestroy();
   		$('#example').dataTable({
    	"sPaginationType": "full_numbers",
    	"aaSorting": [[ 0, "desc" ]],
        "scrollY": "280px",
        "scrollCollapse": true,
     	"bDestroy": true});
	}
   	else{
   		$('#example').dataTable({
    	"sPaginationType": "full_numbers",
    	"aaSorting": [[ 0, "desc" ]],
        "scrollY": "280px",
        "scrollCollapse": true,
     	"bDestroy": true});
   	}   	
   	setCss('#example', 'display', 'inline', false);
   	setCss('#example', 'width', '100%', false);
   	setHover(['.odd', '.even']);
   	if((eventId != '0' || eventId!=0) && statusEvent == null){
   		setTimeout(function() { 
   			try{
   				$('.inputB').val(eventId).trigger('keyup');
   				document.getElementById(eventId+'-icon').click();
   			}
   			catch(err){
   				alert('No puede editar el evento Id: '+eventId);
   			} 
   		},800);
   		statusEvent = true;
    	$('#evento_id').val('0');
    }
    
     
}
/* Función para añadir evento  hover*/
function setHover(){
	/* Agregado de evento hover en la tabla.*/
	$.each( arguments[0], function( key, value ) {
		$(value).hover(
        function() {
        	setCss(this, 'opacity', '.7', false);
        }, function() {
        	setCss(this, 'opacity', '1', false);
        }
    );
	});
}
/* Permite que aparezca una ventana emergente con el formulario. */
function editMaintance(data,ids){
	startData = (data.fecha_inicio).split(" ");
	endData = (data.fecha_termino).split(" ");
	id = startData[0];
	dateFull = {startD:startData[0], startH:startData[1],endD:endData[0],endH:endData[1]};
	cleanForm();
	buildSelect(OBJETIVES, ids);
	displayMessage($('#containerMessage'), 'none', true);
	buildInfoForm(data);

	$(document).ready(function()
	{
		$( "#dialog-message" ).dialog({
	    	width:'715px',
	    	heigth:'600px',
	    	dialogClass: 'ui-dialog-osx',
	    	resizable: false,
	    	modal:true
		});
		$('#dialog-message').dialog("option", "resizable", false);

	});
	$('.icon').hover(

        function() {        	
        	setCss(this, 'zoom', 'document', false);
        },function() {
        	try {setCss(this, 'zoom', '', false);}
			catch(err) {}        	
        }
    );
    
   	
}


/* Muestra popup de calendario. */
function dialogCalendar(){
	//controllerRemoveClass('.dia', 'seleccionado');
	displayMessage($('#containerMessage'), 'none', true);
	loadCalendar();
	$(document).ready(function()
	{		
		$( "#dialog-calendario" ).dialog({
	    	width:'800px ',
	    	dialogClass: 'ui-dialog-osx',
	    	modal: true,
	    	close: function( event, ui ) {
	    	}
		});
	});
	/* Setear estilos.*/
	setCss('.ui-dialog-titlebar', {"background-color":"#f47001","color":"#fff"}, true);
   	setCss('.ui-dialog-title', {"background-color":"#f47001","color":"#fff", "float":"none"}, true);
	
}
/* Función para setear estilos.*/
function setCss(clase, property, value, multiple){
	if (multiple == true){
		$(clase).css(value);
	}
	else{
		$(clase).css(property, value);	
	}	
}

/* Cambia el color de los input, para distinguir los correctos de los incorrectos. */
function changeColor(input, colour){
	input.style.borderColor = colour;
}

/*Ocular lentamente.*/ 
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

/*Maneja el evento que muestra contenido.*/
function setMessage(elem, message){
	elem.html(message);	
}

/*Maneja el evento color.*/
function setColor(elem, colour){
	elem.css("background-color", colour);
}

/* quita bordes del input y limpia el calendario*/
function cleanForm(){
	setCss(".inputMaintainer", "border-color", "#b3b3b3", false);
	setCss(".selectMaintainer", "border-color", "#b3b3b3", false)
	controllerRemoveClass('.dia', 'seleccionado');
}
/* Remueve una clase.*/
function controllerRemoveClass(classSelec, removeClass){
	$(classSelec).removeClass(removeClass);
}

/* Función para transformar la fecha en la zona horaria correspondiente y para llamar funcion para dar formato. */
// No se ocupa.
function translateDate(date){
	moment().format();
	localTime = moment(date);
	return localTime.tz(timeZone).format('YYYY-MM-DD HH:mm:ss');
}


/* Asignar un 0 cuando sea solo un digito*/
function asignZero(str){
	return (str.length == 1)?(str= "0"+str):str;
}

/* Función que setea el estado del formulario. */
function setStatus(status){
	if(status=="Ingresado"){
		return status;
	}
	else if(status=="Cancelado"){
		return status;
	}
	else{
		return status;
	}
}

/* Setea los input del formulario con la data de la base de datos. */
function buildInfoForm(data){
	var status = null;
	if (data.estado == 'Cancelado')
	{
		$(input[3]).prop('disabled', 'disabled');
		$(input[8]).prop('disabled', 'disabled');
		$(input[9]).prop('disabled', 'disabled');
		$('#guardar').hide();
		status = 2
	}
	else{
		
		$(input[3]).removeAttr("disabled");
		$(input[8]).removeAttr("disabled");
		$(input[9]).removeAttr("disabled");
		$('#guardar').show();
		status = 1;
	}
	$(input[0]).val(data.id);
	$(input[1]).val(data.nombre);
	$(input[2]).val(data.usuario_id);
	$(input[3]).val(status);
	$(input[4]).val(data.fecha_inicio);
	$(input[5]).val(data.fecha_termino);
	$(input[6]).val(data.fecha_creacion);
	$(input[7]).val(data.fecha_modificacion);
	$(input[8]).val(data.titulo);
	$(input[9]).val(data.comentario);
	retryInsert = (data.estado=='Error')?true:false;
	
}

/* Valida el formulario antes de enviar.*/
function validateForm(){
	var status = document.getElementById('estado');
	var startDate = document.getElementById('fechaI');
	var endDate = document.getElementById('fechaT');
	var validate = true;
	var regex = /^[0-9][0-9][0-9][0-9]-([0-9]|0[0-9]|1[0-2])-([0-9]|0[0-9]|1[0-9]|2[0-9]|3[0-1]) ([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/;
	var regStatus = /^[1-2]$/
	var resultHours 
	if (status.value.search(regStatus) !=0){
		validate = false;
		changeColor(status, 'red');
	}
	else{changeColor(status, 'green');}
	if (startDate.value.search(regex)!=0 ){
		validate = false;
		changeColor(dateStart, 'red'); 
	}
	else{changeColor(startDate, 'green'); }
	if (endDate.value.search(regex) != 0){
		validate = false;
		changeColor(endDate, 'red'); 
	}
	else{changeColor(endDate, 'green'); }

	startDateFormat = moment(startDate.value);
  	endDateFormat = moment(endDate.value);
  	$('#startDate').html("startDate: " + startDateFormat.format());
	$('#endDate').html("endDate: " + endDateFormat.format());
	resultHours = endDateFormat.diff(startDateFormat, 'hours', true);
	
	if (resultHours <= 0){validate = false;changeColor(startDate, 'red');}
	else{changeColor(startDate, 'green');}
	
	return validate;
}
/* Construye el array con valores. */
function buildArrayValues(){

	var valueInput = new Array();
	var nameObj = '';	
	var objetives = getValueMultiSelect();
	$.each( input, function( index, value ) {
	  valueInput[index]=$(value).val();

	});

	$('#objetive :selected').each(function(i, selected){ 
	  nameObj = nameObj+$(selected).text()+','; 
	});
	valueInput[10] = '';
	valueInput[11] = 'Editar';
	valueInput[12] = timeZone;
	valueInput[13] = nameObj;
	valueInput[14] = retryInsert;
	return valueInput;
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
/* Maneja el evento del calendario. */
function loadCalendar(){
	jQuery(function($) {	 	
	    // Inicializa calendario
	    var clean = false;
	    var idStart = '#'+dateFull.startD;
	    var idEnd = '#'+dateFull.endD;
	    var calendarEspecial = $("#calendario_especial");
	    var params = {};
	    var dateCalendar = "";
	    var dateMin = "";
	    // Establece inputs setInputDate
	    var inputDateStart = $('<input type="hidden" name="fecha_inicio_periodico" id="fecha_inicio_periodico" value="">');
	    var inputDateEnd = $('<input type="hidden" name="fecha_termino_periodico" id="fecha_termino_periodico" value="">');
	    
	    var inputButton = $('<button class="btn" type="button" name="guardarFecha" id="guardarFecha" value="Definir" onclick="setInputDate();">Definir</button>');
	    var span = $('<div id="containerMessage" class="form-dialog" style="display:none;"><span id="menssageAlert" ><div>');
	    var br = $('<br/>');
	   
	    $( "#containerMessage").remove();
		/* Evita duplicidad de elementos agregados mas abajo. */	    
	    $('#fecha_inicio_periodico').remove();
	    $('#fecha_termino_periodico').remove();
	    $('#guardarFecha').remove();
	    $('#hourStartInput').remove();
		$('#hourEndInput').remove();
	   
	    if(dateCalendar.length > 0) {
	      params["fechaCalendario"] =  dateCalendar + "00:00:00";
	    }
		
	    if(dateMin.length > 0) {
	      params["fechaMinima"] = dateMin + "00:00:00";
	    }
	    /* Se asigna la hora 12 para que tome el día correspondiente. */
	    params['especial'] = true;
	    params['fechaInicio']= dateFull.startD + " 12:00:00";
	    params['fechaTermino']= dateFull.endD + " 12:00:00";
	    params["seleccion"] = {};
	    params["seleccion"]["activa"] = ("true" === "true");
	    params["seleccion"]["intervalo"] = ("true" === "true");

	    calendarEspecial.calendariou(params);
	    var calendariou = calendarEspecial.data("calendariou");	   
	    calendarEspecial.append(inputDateStart, inputDateEnd);
	    buildInputHour(clean);
	    // Escucha cambios en selección para propagarlos a inputs correspondientes
	    calendariou.seleccion.el().on("calendariou:seleccion:cambio", function() {
	      var dateIn = calendariou.seleccion.get("fechaInicio");
	      var dateEn = calendariou.seleccion.get("fechaTermino");
	      clean = true;
	      //fechaTermino = fechaTermino.setDate(fechaTermino.getDate() - 1);
	      inputDateStart.prop("value", dateIn === null ? null : dateIn.format("yyyy-mm-dd"));
	      inputDateEnd.prop("value", dateEn === null ? null : subtractOneDay(dateEn.format("yyyy-mm-dd")));
	      buildInputHour(clean);
	    });
	    calendarEspecial.append(span);
	    calendarEspecial.append(br);
	    calendarEspecial.append(inputButton);
	});
}
/* Función asignada a una variable.*/
String.prototype.replaceAt=function(index, character) {
    return this.substr(0, index) + character ;
}
/* Función para restar un día */ 
function subtractOneDay(dateEnd){
	day=dateEnd.substr(8, 9)
	day=String((parseInt(day)-01));
	if (day.length==1){
		day='0'+(day);
	}
	return dateEnd.replaceAt(8, day);
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

/* Construye los input que contendran la hora.*/
function buildInputHour(clean){
	$('#hourStartInput').remove();
	$('#hourEndInput').remove();
	hourStart = $('#hI');
	hourEnd = $('#hT');
	hourStart.html('');
	hourEnd.html('');
	
	hourStart.append($('<input type="text" name="hourStartInput" class="inputMaintainer" id="hourStartInput" value="" placeholder="03:01:30" maxlength="8" onBlur="validateHour(this);"> '));
	hourEnd.append($('<input type="text" name="hourEndInput" class="inputMaintainer" id="hourEndInput" value="" placeholder="03:01:30" maxlength="8" onBlur="validateHour(this);" >'));
	
	if (clean == false){
		$('#hourStartInput').val(dateFull.startH);
		$('#hourEndInput').val(dateFull.endH);
	}

}

/* Valida que la hora ingresada sea en formato correcto. */
function validateHour(inputHour){
	var reg = "^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$";	
	time = inputHour.value;	
	validate='ok';

	if (time.length!=8){
		validate='error';
	}

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

/* Función asignada a una variable.*/
String.prototype.largeStr=function() {
    return this.length;
}
/* Función para crear agregar option al select y definir los objetivos seleccionados.*/
function buildSelect(response, ids){
	count = count + 1;
	var objetive = getObjetiveid(ids)

	objetives = JSON.parse(objetive);
	$('#objetive').empty();
	$('<tr>').attr({id: 'trTitleObjetivos'}).appendTo('#objetive');
	$('<th>').text('Objetivo id').appendTo('#trTitleObjetivos');
	$('<th>').text('Nombre').attr({width: '50%'}).appendTo('#trTitleObjetivos');
	
	$.each( objetives, function( key, value ) {
		$('<tr>').attr({id: 'trOjetivo_'+value.objetivo_id}).appendTo('#objetive');
		$('<td>').text(value.objetivo_id).appendTo('#trOjetivo_'+value.objetivo_id);
		$('<td>').text(value.nombre).attr({width: '50%'}).appendTo('#trOjetivo_'+value.objetivo_id);
	});
}

/* Variable global. */
function saveData(response){
	OBJETIVES =JSON.parse(response);
}
/* Permite la descarga de csv*/
function downloadCsv() {               
    json = JSON.stringify(allRegister);
    /*CREANDO EL FORMULARIO*/
    $('<form>').attr({
        type: 'hidden',
        id: 'form_event',
        name: 'form_event',
        action: 'descarga_csv_eventos.php',
        method: 'POST',
        target: '_self'
    }).appendTo('body');

    $('#form_event').hide();
    $('<input>').attr({
        type: 'text',
        id: 'data',
        name: 'data',
        value: json
    }).appendTo('#form_event');
    $('<input>').attr({
        type: 'text',
        id: 'client',
        name: 'client',
        value: clientName
    }).appendTo('#form_event');
    $('<input>').attr({
        type: 'text',
        id: 'nameUser',
        name: 'nameUser',
        value: nameUser
    }).appendTo('#form_event');
    $("#form_event").submit();
} 