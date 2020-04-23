<link rel="stylesheet" href="{__path_full_calendar}css/full_calendar.css" rel='stylesheet' />
<link rel="stylesheet" href="{__path_full_calendar}css/calendar_print.css" media='print' />
<script type="text/javascript" src="../../js/controlador_calendario.js"></script>
<input type="hidden" id="usuario_cliente_id" value="{__usuario_id}">
<table width="100%">
	<tr>
		<td class="tituloseccion">Calendario</td>
	</tr>
	<tr>
		<td>
		<br>
		</td>
	</tr>
	<tr>
		<td>
			<div class="descripcion">
				• A continuación se muestra el calendario con los registros de eventos creados.
				<br>
				• Puede editar al hacer click en el evento.
			</div>
		</td>
	</tr>
	<tr>
		<td>
		<br>
		<div id='calendar'></div>
		</td>
	</tr>
</table>
<script>
	$(document).ready(function() {
		var data = [];
		getObjetive();
		<!-- BEGIN BLOQUE_DATOS -->
		if(showEvent('{__objetivos}') == true){
			data.push({id:   "{__id}",start: '{__fecha_inicio}', end:'{__fecha_termino}', title:'{__titulo}', color: CALENDAR.randomRgba(), value:'{__nombre}', userId:'{__usuario_id_evento}'});
		}
		<!-- END BLOQUE_DATOS -->
		CALENDAR.createCalendar(data);
	});
</script>


