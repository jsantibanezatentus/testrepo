<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>

<div style="page-break-inside: avoid;">
<table width="100%">
	<tr>
		<td height="10px"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris" align="left" style="tex">Paso</td>
		<td class="txtBlanco12b celdaTituloNaranjo" style="border-right: solid 1px #ffffff;" width="15%" align="center">Uptime</td>
		<td class="txtBlanco12b celdaTituloNaranjo" style="border-right: solid 1px #ffffff;" width="15%" align="center">Downtime</td>
		<td class="txtBlanco12b celdaTituloNaranjo" style="border-right: solid 1px #ffffff;" width="15%" align="center">No Monitoreo</td>
	</tr>
	<!-- BEGIN BLOQUE_PASOS -->
	<tr>
		<td class="txtGris12 {__print_class}" width="40%" align="left">{__pasoNombre}</td>
		<td class="txtBlanco12 bordes" width="20%" style="background-color: #{__color_uptime};" align="right">{__uptime_real_o} %</td>
		<td class="txtBlanco12 bordes" width="20%" style="background-color: #{__color_downtime};" align="right">{__downtime_real_o} %</td>
		<td class="txtBlanco12 bordes" width="20%" style="background-color: #{__color_no_mon};" align="right">{__no_mon_real_o} %</td>
	</tr>
	<!-- END BLOQUE_PASOS -->
</table>
</div>
<br>
<style type="text/css">
	.bordes {
		padding: 1px 6px 1px 6px;
	    background-color: #ffffff;
	    border-bottom: solid 1px #fff;
	    border-left: solid 1px #fff;
	}

	.bordeDerecho {
		border-right: solid 1px #fff;
	}
</style>