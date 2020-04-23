<link rel="stylesheet" href="css/galeria_sc.css"></link>

<table width="100%">
	<tr>
		<td style="border: solid 1px #ffffff;" class="celdanegra50">{__monitor_nombre}</td>
	</tr>
</table>
<table width="auto">
	<tr id="td_{__monitor_id}_empty">
		<td style="border: solid 1px #ffffff;" class="celdanegra40"  width="28%">{__objetivo_nombre}</td>
		<!-- BEGIN LISTA_EVENTOS_INICIO -->
		<td style="border: solid 1px #ffffff;" class="{__evento_style}" align="center" width="12%">{__evento_inicio}</td>
		<!-- END LISTA_EVENTOS_INICIO -->
	</tr>
	<!-- BEGIN LISTA_PASOS -->
	<tr>
		<td style="border: solid 1px #ffffff;" width="28%" class="{__estiloPaso} " valign="top" id="paso_{__evento_tooltip_id}_{__monitor_id}" title="{__paso_nombre_completo}">{__paso_nombre}</td>
		<!-- BEGIN LISTA_EVENTOS -->
		<td style="border: solid 1px #ffffff;" bgcolor="#c4c4c4" width="12%" >
			<table width="100%" >
				<!-- BEGIN LISTA_EVENTOS_PATRONES -->
				<tr>
					<td height="22" bgcolor="#{__evento_color}" align="center" id="evento_{__evento_tooltip_id}_{__monitor_id}">
						<i class="{__evento_icono}"></i>
					</td>
				</tr>
				<!-- END LISTA_EVENTOS_PATRONES -->
			</table>
		</td>
		<!-- END LISTA_EVENTOS -->
	</tr>
	<!-- END LISTA_PASOS -->
	<tr>
		<td style="border: solid 1px #ffffff;" class="celdaduracion" width="28%" height="35">&nbsp;</td>
		<!-- BEGIN LISTA_EVENTOS_DURACION -->
		<td style="border: solid 1px #ffffff;" class="celdaduracion" align="center" width="12%" height="35">{__evento_duracion}</td>
		<!-- END LISTA_EVENTOS_DURACION -->
	</tr>
	<tr id="td_{__id_monitor}" data ="td{__id_monitor}"></tr>
	<tr id="td_elem_{__id_monitor}" data ="td_elem_{__id_monitor}"></tr>
</table>

<br>
<br>

<script>
t = 'fz';
patron_cdn='{__patron_cdn}';
servicio = '{__servicio}';
var paso_evento=[];
var monitor_id= '{__monitor_id}'
<!-- BEGIN LISTA_PASOS_EVENTOS -->
paso_evento += '{{__id_paso_evento},{__eventos_paso}},'
<!-- END LISTA_PASOS_EVENTOS -->

pos = paso_evento.lastIndexOf(',');
cambio ='';
paso_evento = paso_evento.substring(0,pos) + cambio + paso_evento.substring(pos+1)

contador_td=0
<!-- BEGIN LISTA_EVENTOS_BOTON -->
contador_td++
 // SETEO VARTIABLES
var hora_inicio_utc= ('{__hora_inicio_tz}'.split("+"))[0]
var hora_termino_utc=('{__hora_termino_tz}'.split("+"))[0]
var monitor = '{__id_monitor}'
var objetivo = '{__obj}'
var td = $('<td data="button" />')
var nombre_monitor= '{__nombre_nodo}'
var ok = '{__evento_ok}'
var paso ='{__evento_cdn}'
pos = paso.lastIndexOf(',');
cambio ='';
paso = paso.substring(0,pos) + cambio + paso.substring(pos+1)
dato=monitor
var segundo_paso=paso
//CREACION BOTONES
paso= "'"+paso+"'";

  //COMPROBACION AJAX DE DATA SIN SCREENSHOT
empty=''
paso=segundo_paso
if(paso!=empty){
	$.ajax({
		async: false,
		type: "POST",
		url: "utils/get_last_image_evento.php",
		data: {'datos':dato, monitor, paso, hora_inicio_utc, hora_termino_utc, objetivo},
		success: function(data) {
			if(data=='[]'||'{__codigo_id}'=='5'){
				$("#{__id_monitor}_{__contador}").remove()
			}
		}
	})
}
<!-- END LISTA_EVENTOS_BOTON -->
for (var i = contador_td; i <=5; i++) {
	$('#td_{__monitor_id}_empty').append('<td style="border: solid 1px #ffffff;" class="celdaenblanco" align="center" width="12%" rowspan="100%">&nbsp;</td>')
}
		//ESPACIOS INICIALES DE LA TABLE
$('#td_{__id_monitor}').prepend('<td />')

</script>
