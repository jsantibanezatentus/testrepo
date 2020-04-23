<!-- BEGIN BLOQUE_NOMBRE -->

<!-- END BLOQUE_NOMBRE -->

<!-- BEGIN LISTA_PASOS -->

<div style="page-break-inside: avoid;">
<table width="100%">
	<tr>		
		<td colspan="100%" class="txtNegro13" style="padding: 6px; border: solid 1px #ffffff;">{__item_orden}.{__paso_orden}. {__paso_nombre}</td>
	</tr>
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris" width="18%">Fecha</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center" width="18%">Hora Inicio</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center" width="18%">Hora Termino</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center" width="18%">Duraci&oacute;n</td>
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
<br>

<!-- END LISTA_PASOS -->
<br/>

<div id="man" style="display:none">
	<table width="100%" >
		<tr>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="100%" colspan="4">Datos Mantenimientos especiales</td>
		</tr>
		<tr>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="18%">Fecha Inicio</td>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="18%">Fecha Termino</td>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="18%">Duraci&oacute;n</td>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="28%">Tipo</td>
		</tr>
		<!-- BEGIN BLOQUE_MANTENIMIENTO -->
		<tr>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__fecha_inicio_evento}</td>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__fecha_termino_evento}</td>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__duracion_evento}</td>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__tipo_evento}</td>
		</tr>
		<!-- END BLOQUE_MANTENIMIENTO -->
			
		
	</table>
</div>
<script>
$(function() {
	name = '{__name}';
	// Ejecuta la inialización del acordeon.
	if ('{__tiene_evento}' == 'true'){		
		createAccordion(name);	
	}
});
</script>
