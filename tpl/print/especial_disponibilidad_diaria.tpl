<!-- BEGIN BLOQUE_PASOS -->
<div style="page-break-inside: avoid;">
<table width="100%">
	<tr>
		<td colspan="100%" class="txtNegro13" style="padding: 6px; border: solid 1px #ffffff;">{__ordenItem}.{__ordenPaso}. {__paso_nombre}</td>
	</tr>
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris" width="40%" align="left">D&iacute;a</td>
		<!-- BEGIN BLOQUE_EVENTOS_TITULOS -->
		<td class="txtBlanco12b celdaTituloNaranjo" width="15%" align="center">{__evento_nombre} [%]</td>
		<!-- END BLOQUE_EVENTOS_TITULOS -->		
	</tr>
	<!-- BEGIN BLOQUE_DIAS -->
	<tr >
		<td class="txtGris12 {__paso_estilo}" align="left">{__evento_fecha}</td>
		<!-- BEGIN BLOQUE_EVENTOS -->
		<td class="txtBlanco12 celdaIteracion1" style="background-color: #{__evento_color};" align="right">{__evento_valor}</td>
		<!-- END BLOQUE_EVENTOS -->
	</tr>
	<!-- END BLOQUE_DIAS -->
</table>
</div>
<br>
<!-- END BLOQUE_PASOS -->