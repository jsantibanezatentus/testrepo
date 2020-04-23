<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>

<div style="page-break-inside: avoid;">
<table width="100%">
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris" align="left">Paso</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="15%" align="center">Uptime [%]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="15%" align="center">Downtime [%]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="15%" align="center">No Monitoreo [%]</td>
	</tr>
	<!-- BEGIN BLOQUE_PASOS -->
	<tr>
		<td class="txtGris12 {__print_class}" align="left">{__pasoNombre}</td>
		<td class="txtBlanco12 celdaIteracion1" style="background-color: #55a51c;" align="right">{__uptime_real_obj}</td>
		<td class="txtBlanco12 celdaIteracion1" style="background-color: #d3222a;" align="right">{__downtime_real_obj}</td>
		<td class="txtBlanco12 celdaIteracion1" style="background-color: #b2b2b2;" align="right">{__no_mon_real_obj}</td>
	</tr>
	<!-- END BLOQUE_PASOS -->
</table>
</div>
<br>