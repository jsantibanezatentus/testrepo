<!-- BEGIN LISTA_PASOS_TITULO -->
<!-- sin datos -->
<!-- END LISTA_PASOS_TITULO -->

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
		<td class="txtBlanco13b celdaTituloGris" align="center" width="12%">Inicio</td>
		<td class="txtBlanco13b celdaTituloGris" align="center" width="12%">Termino</td>
		<td class="txtBlanco13b celdaTituloGris" align="center" width="15%">Ponderacion [%]</td>
		<!-- BEGIN BLOQUE_EVENTOS_TITULOS -->
		<td class="txtBlanco12b celdaTituloNaranjo" align="center" width="15%">{__evento_nombre} [%]</td>
		<!-- END BLOQUE_EVENTOS_TITULOS -->	
	</tr>
	<!-- BEGIN LISTA_ITEMS -->
	<tr>
		<td class="txtGris12 {__print_class}" align="center">{__item_inicio}</td>
		<td class="txtGris12 {__print_class}" align="center">{__item_termino}</td>
		<td class="txtGris12 {__print_class}" align="right">{__item_valor}</td>
		<!-- BEGIN BLOQUE_EVENTOS -->
		<td class="txtBlanco12 celdaIteracion1" style="background-color: #{__evento_color};" align="right">{__evento_valor}</td>
		<!-- END BLOQUE_EVENTOS -->
	</tr>
	<!-- END LISTA_ITEMS -->
	<tr>
	 	<td class="txtGris12 celdaIteracion2" colspan="3">Total Acumulado</td>
		<!-- BEGIN BLOQUE_EVENTOS_TOTAL -->
		<td class="txtGris12 celdaIteracion2" align="right">{__evento_total}</td>
		<!-- END BLOQUE_EVENTOS_TOTAL -->
	</tr>
</table>
</div>
<br>
<!-- END LISTA_PASOS -->