<link rel="stylesheet" type="text/css" href="css/textos-reporte.css">
<!-- BEGIN LISTA_PASOS -->
<div style="page-break-inside: avoid;">
<table width="100%">
	<tr>
			<td style="border-bottom: solid 1px #ffffff !important" class="txtBlanco13b celdaTituloGris" align="center" width="100%" colspan="5">Disponibilidad Downtime Global</td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris" width="18%">Fecha</td>
		<td style="border-right: solid 1px #ffffff !important" class="txtBlanco12b celdaTituloNaranjo" align="center" width="18%">Hora Inicio</td>
		<td style="border-right: solid 1px #ffffff !important" class="txtBlanco12b celdaTituloNaranjo" align="center" width="18%">Hora Termino</td>
		<td style="border-right: solid 1px #ffffff !important" class="txtBlanco12b celdaTituloNaranjo" align="center" width="18%">Duraci&oacute;n</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center" width="28%">Tipo</td>
	</tr>
	<!-- BEGIN LISTA_DIAS -->
	<!-- BEGIN BLOQUE_DOWNTIME -->
	<tr>
		<td class="txtGris12 {__print_class}" align="center">{__fecha}</td>
		<td class="txtGris12 {__print_class}" align="center">{__horaInicio}</td>
		<td class="txtGris12 {__print_class}" align="center">{__horaTermino}</td>
		<td class="txtGris12 {__print_class}" align="center">{__duracion}</td>
		<td class="txtGris12 {__print_class}" align="center">{__tipo}</td>			
	</tr>
	<!-- END BLOQUE_DOWNTIME -->
	<!-- BEGIN BLOQUE_UPTIME -->
	<tr>
		<td class="txtGris12 {__print_class}" align="center">{__fecha}</td>
		<td class="txtGris12 {__print_class}" align="center" colspan="4">Sin Caidas Globales</td>
	</tr>
	<!-- END BLOQUE_UPTIME -->
	<!-- END LISTA_DIAS -->
	<tr>
	 	<td class="txtGris12 celdaIteracion2" align="center" colspan="4">Downtime Acumulado</td>
		<td class="txtGris12 celdaIteracion2" align="center">{__downtime_acumulado}</td>
	</tr>
	<tr>
	 	<td class="txtGris12 celdaIteracion2" align="center" colspan="4">No Monitoreo Acumulado</td>
		<td class="txtGris12 celdaIteracion2" align="center">{__no_monitoreo_acumulado}</td>		
	</tr>
</table>
</div>
<!-- END LISTA_PASOS -->
<br/>
<div id="man" style="display:none">
	<table width="100%">
		<tr>
			<td style="border-bottom: solid 1px #ffffff !important" class="txtBlanco13b celdaTituloGris" align="center" width="100%" colspan="4">Datos Mantenimientos especiales</td>
		</tr>
		<tr>
			<td style="border-right: solid 1px #ffffff !important" class="txtBlanco12b celdaTituloNaranjo" align="center" width="18%">Fecha Inicio</td>
			<td style="border-right: solid 1px #ffffff !important" class="txtBlanco12b celdaTituloNaranjo" align="center" width="18%">Fecha Termino</td>
			<td style="border-right: solid 1px #ffffff !important" class="txtBlanco12b celdaTituloNaranjo" align="center" width="18%">Duraci&oacute;n</td>
			<td class="txtBlanco12b celdaTituloNaranjo" align="center" width="28%">Tipo</td>
		</tr>
		<!-- BEGIN BLOQUE_MANTENIMIENTO -->
		<tr>
			<td class="{__class}" align="center">{__fecha_inicio_evento}</td>
			<td class="{__class}" align="center">{__fecha_termino_evento}</td>
			<td class="{__class}" align="center">{__duracion_evento}</td>
			<td class="{__class}" align="center">{__tipo_evento}</td>
		</tr>
		<!-- END BLOQUE_MANTENIMIENTO -->
	</table>
</div>
<br>
<script>
	if ('{__tiene_evento}' == 'true'){
		$('#man').show();
		createAccordion(name);	
	}
</script>