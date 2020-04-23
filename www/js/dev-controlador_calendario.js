/* Controlador de acciones.
   Autor: Carlos Sepúlveda
   Objetivo: Manejar los eventos del plugin full calendario.

*/
var CALENDAR = CALENDAR || {};

section = $('#seccion_mantenencion').data("seccion");
calendarSection = $('#seccion_mantenencion').data("calendario");
historySection = $('#seccion_mantenencion').data("historial");
addSection = $('#seccion_mantenencion').data("agregar");

CALENDAR = {
	/* Función que devuelve un color rgba random*/
	randomRgba: function(){

		red = (Math.floor(Math.random() * 256));
		green = (Math.floor(Math.random() * 256));
		blue = (Math.floor(Math.random() * 256));
		alpha = 0.7;
		rgba = 'rgba(' + red + ',' + green + ',' + blue + ',' + alpha +')';
		return rgba;
	},

	/* Función que crea el toolip para los eventos. */
	createTooltip: function (data, element){
		tooltip = '<div class="tooltiptopicevent" >' + 'Id: ' + ': ' + data.id + '</br>' + 'Nombre: ' + ': ' + data.value + '</br>' + 'Titulo: ' + ': ' + data.title + '</br>'+'Inicio: ' + ': ' + data._start._i + '</br>' + 'Termino: ' + ': ' + data._end._i + '</br>'  +'</div>';
		$("body").append(tooltip);
		colour =$(element).css('backgroundColor');
		if (colour == 'rgba(0, 0, 0, 0)'){
			colour = '#FF9124';
		}
		$('.tooltiptopicevent').css('background', colour);
		$(element).mouseover(function (e) {
			
		    $(element).css('z-index', 10000);
		    $('.tooltiptopicevent').fadeIn('500');
		    $('.tooltiptopicevent').fadeTo('10', 1.9);
		}).mousemove(function (e) {

		    $('.tooltiptopicevent').css('top', e.pageY + 10);
		    $('.tooltiptopicevent').css('left', e.pageX + 20);
		});
	},

	/* Función que remueve el tooltip al perder el foco. */
	removeTooltip:function (element){

		$(element).css('z-index', 8);
		$('.tooltiptopicevent').remove();
	},

	/* Que inicializa el full calendar. */
	createCalendar:function (data){
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			defaultDate: moment().format("YYYY-MM-DD"),
			weekNumberCalculation: 'ISO',
			navLinks: true, // can click day/week names to navigate views
			editable: false,
			eventLimit: true, // allow "more" link when too many events
			events: data,
			eventTextColor: '#616265',
			monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
	    	monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
	    	dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
	    	dayNamesShort: ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'],
	    	lang: 'es',
	    	defaultView: 'agendaWeek',
	    	eventClick: function(calEvent, jsEvent, view) {
	    		if($('#usuario_cliente_id').val() == calEvent.userId){
	    			mostrarSubmenu(section,historySection,1,calEvent.id);	
	    		}
	    		else{alert('Solo el usuario que ha creado el evento puede modificarlo.')}
	    		
	    	},
	    	eventMouseover: function (data, event, view) {
	    		element = this;
	    		CALENDAR.createTooltip(data, element);
			},
	        eventMouseout: function (data, event, view) {
	        	element = this;
	          	CALENDAR.removeTooltip(element);
	        },
	        eventRender: function(event, element)
			{ 
			    element.find('.fc-event-title').append("<br/>" + event.value); 
			},
		});
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
/* Define una variable global. */
function saveData(response){
	OBJETIVES =JSON.parse(response);
}

function showEvent(objetiveId){
	var array = objetiveId.split(',');
	var show = false;
	$.each(OBJETIVES,function(index,value) {
		if (jQuery.inArray(value.objetivo_id, array)!='-1') {
			show = true;            
        }
    });
	return show;
}

