<div style="page-break-inside: avoid;">
<table width="100%">
	<tr>
		<td colspan="100%" class="txtNegro13" style="padding: 6px; border: solid 1px #ffffff;">{__item_orden}.{__monitor_orden}. {__monitor_nombre}</td>		
	</tr>
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris" width="130">Fecha</td>
		<td class="txtBlanco13b celdaTituloGris" width="80" style="display:{__monitor_display};">Monitor</td>
		<td class="txtBlanco13b celdaTituloGris" width="100">Paso</td>
		<td class="txtBlanco13b celdaTituloGris" width="80">Patron</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">Registros</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="100" align="center">Estado</td>
	</tr>
	
	<!-- BEGIN BLOQUE_MONITOREOS -->
	
	<!-- BEGIN BLOQUE_PASOS -->
	
	<!-- BEGIN BLOQUE_PATRONES -->
	<tr>
		<!-- BEGIN BLOQUE_PASO_NOMBRE -->
		
		<!-- END BLOQUE_PASO_NOMBRE -->
		
		<td class="txtGris12 {__print_class}">{__monitoreo_fecha}</td>
		<td class="txtGris12 {__print_class}" style="display:{__monitor_display};">{__monitoreo_desde}</td>
		<td class="txtGris12 {__print_class}">{__paso_nombre}</td>
		<td class="txtGris12 {__print_class}">{__patron_nombre}</td>
		<td class="txtGris12 {__print_class}" align="center">
			<!-- BEGIN BLOQUE_REGISTROS -->
			{__registro_nombre} : {__registro_valor}<br>
			<!-- END BLOQUE_REGISTROS -->
		</td>
		<td class="txtGris12 {__print_class}" align="center">{__evento_nombre}</td>
	</tr>
	<!-- END BLOQUE_PATRONES -->
	
	<!-- END BLOQUE_PASOS -->

	<!-- END BLOQUE_MONITOREOS -->
</table>
</div>