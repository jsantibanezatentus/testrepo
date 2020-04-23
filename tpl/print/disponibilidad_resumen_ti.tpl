<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>
<!-- BEGIN BLOQUE_HORARIOS -->
<div style="page-break-inside: avoid;">
<table width="100%">
	<!-- BEGIN BLOQUE_TITULO_HORARIOS -->
	<tr>
		<td colspan="100%" class="txtNegro13" style="padding: 6px; border: solid 1px #ffffff;">{__item_orden}.{__horario_orden}. {__horario_nombre}</td>
	</tr>
	<!-- END BLOQUE_TITULO_HORARIOS -->
	<tr>
		<td class="txtBlanco13b celdaTituloGris" align="left">Paso</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="15%" style="border-right: 1px #fff !important" align="center">Uptime TI [%]</td>
		<!-- BEGIN BLOQUE_EVENTOS_TITULOS -->
		<td class="txtBlanco12b celdaTituloNaranjo" width="15%" align="center">{__evento_nombre} [%]</td>
		<!-- END BLOQUE_EVENTOS_TITULOS -->		
	</tr>
	<!-- BEGIN BLOQUE_PASOS -->
	<tr>
		<td class="txtGris12 {__print_class}" align="left">{__pasoNombre}</td>
		<td class="txtGris12 {__print_class}" style="background-color: #2e6d00; color: #fff;" align="right">{__uptime_ti}</td>
		<!-- BEGIN BLOQUE_EVENTOS -->
		<td class="txtBlanco12 celdaIteracion1" style="background-color: #{__evento_color};" align="right">{__evento_valor}</td>
		<!-- END BLOQUE_EVENTOS -->
	</tr>
	<!-- END BLOQUE_PASOS -->
</table>
</div>
<br>
<!-- END BLOQUE_HORARIOS -->