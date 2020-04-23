<!-- BEGIN LISTA_OBJETIVOS -->
<div style="page-break-inside: avoid;">
<table width="100%">
	<tr>
		<td colspan="100%" class="txtNegro13" style="padding: 6px; border: solid 1px #ffffff;">{__item_orden}.{__objetivo_orden}.  {__objetivo_nombre}</td>
	</tr>
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris" width="40%" align="left">Paso</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="15%" align="center">Uptime [%]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="15%" align="center">Downtime Parcial [%]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="15%" align="center">Downtime Global [%]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="15%" align="center">Sin Monitoreo [%]</td>		
	</tr>
	<!-- BEGIN LISTA_PASOS -->
	<tr >
		<td class="txtGris12 {__paso_estilo}" align="left">{__paso_nombre}</td>
		<td class="txtBlanco12 celdaUptime" align="right">{__paso_uptime}</td>
		<td class="txtBlanco12 celdaDtParcial" align="right">{__paso_downtime_parcial}</td>
		<td class="txtBlanco12 celdaDtGlobal" align="right">{__paso_downtime_global}</td>
		<td class="txtBlanco12 celdaSinMonitoreo" align="right">{__paso_sinmonitoreo}</td>
	</tr>
	<!-- END LISTA_PASOS -->
</table>
</div>
<br>
<!-- END LISTA_OBJETIVOS -->