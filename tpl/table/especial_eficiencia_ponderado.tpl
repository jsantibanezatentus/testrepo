<table width="100%" border="1" cellpadding="0" cellspacing="0">
<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
<tr>
	<td class="txtBlanco13b celdaTituloGris"style="text-align: right;">Categoria</td>
	<td class="txtBlanco13b celdaTituloGris"style="text-align: right;">Valor Ponderado</td>
	
	<!-- BEGIN BLOQUE_FECHA -->
	<td class="txtBlanco12b celdaTituloNaranjo" width="120" align="center"style="text-align: right;">{__fecha_titulo}</td>
	<!-- END BLOQUE_FECHA -->
</tr>
<!-- BEGIN BLOQUE_CATEGORIA -->
<tr>
	<td class="{__print_class} txtGris12" style="text-align: right;" rowspan="{__rowspan_categoria}">{__categoria}</td>
	<td class="txtGris12 {__print_class}" style="text-align: right;">{__ponderacion}</td>
	<!-- BEGIN BLOQUE_PONDERACION -->
	<td class="txtGris12 {__print_class}" style="text-align: right;">{__valor_ponderacion}</td>
	<!-- END BLOQUE_PONDERACION -->
</tr>
<!-- END BLOQUE_CATEGORIA -->
<tr>
<td class="txtGris12b" style="text-align: right;">Global</td>
<td class="txtGris12b" style="text-align: right;">100%</td>
<!-- BEGIN BLOQUE_PONDERACION_GLOBAL -->
	<td class="txtGris12b " style="text-align: right;">{__valor_ponderacion_global}</td>
<!-- END BLOQUE_PONDERACION_GLOBAL -->
</tr>
</table>
<br>