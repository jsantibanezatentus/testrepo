<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>
<div style="page-break-inside: avoid;">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris">Objetivo</td>
		<td class="txtBlanco13b celdaTituloGris" width="120">Paso</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="70" align="center">Promedio [s]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="70" align="center">Uptime [%]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="70" align="center">Downtime [%]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="70" align="center">Downtime Parcial [%]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="70" align="center">Sin Monitoreo [%]</td>
		<!-- BEGIN TIENE_EVENTO -->
		<td class="txtBlanco12b celdaTituloNaranjo" width="70" align="center">Mant. Prog. [%]</td>
		<!-- END TIENE_EVENTO -->
	</tr>
	<!-- BEGIN LISTA_PASOS -->
	<tr>
		<td class="txtGris12 {__paso_estilo}">{__objetivo_nombre}</td>
		<td class="txtGris12 {__paso_estilo}">{__paso_nombre}</td>
		<td class="txtGris12 {__paso_estilo}" align="right">{__paso_promedio}</td>
		<td class="txtBlanco12 celdaUptime" align="right">{__paso_uptime}</td>
		<td class="txtBlanco12 celdaDtGlobal" align="right">{__paso_downtime}</td>
		<td class="txtBlanco12 celdaDtParcial" align="right">{__paso_downtime_parcial}</td>
		<td class="txtBlanco12 celdaSinMonitoreo" align="right">{__paso_no_monitoreo}</td>		
		<td class="txtBlanco12 celdaEventoEspecial {__mostrar}" align="right">{__paso_evento_especial}</td>
	</tr>
	<!-- END LISTA_PASOS -->
</table>
</div>
<br>
<script>
$(function() {
	name = '{__name}';
	// Ejecuta la inialización del acordeon.
	if ('{__tiene_evento}' == 'true'){
		$('#man').show();
		createAccordion(name);	
	}
});
</script>