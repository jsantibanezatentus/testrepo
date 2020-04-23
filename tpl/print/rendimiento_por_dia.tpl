<!-- BEGIN BLOQUE_TABLA -->
<div style="page-break-inside: avoid;">
<table width="100%">
	<!-- BEGIN BLOQUE_TITULO_HORARIOS -->
	<tr>		
		<td colspan="100%" class="txtNegro13" style="padding: 6px; border: solid 1px #ffffff;">{__item_orden}.{__horario_orden}. {__horario_nombre}</td>
	</tr>
	<!-- END BLOQUE_TITULO_HORARIOS -->
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris">Dia</td>
		<td class="txtBlanco13b celdaTituloGris">Paso</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="120" align="center">M&iacute;nimo [s]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="120" align="center">M&aacute;ximo [s]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="120" align="center">Promedio [s]</td>
	</tr>
	<!-- BEGIN LISTA_PASOS -->
	<tr>
		<!-- BEGIN ES_PRIMERO_DIA -->
		<td class="txtGris12 celdaIteracion1" rowspan="{__dia_rowspan}">{__dia_nombre}</td>
		<!-- END ES_PRIMERO_DIA -->
		<td class="txtGris12 {__print_class}">{__paso_nombre}</td>
		<td class="txtGris12 {__print_class}" align="right">{__paso_minimo}</td>
		<td class="txtGris12 {__print_class}" align="right">{__paso_maximo}</td>
		<td class="txtGris12 {__print_class}" align="right">{__paso_promedio}</td>
	</tr>
	<!-- END LISTA_PASOS -->
</table>
</div>
<br>
<!-- END BLOQUE_TABLA -->