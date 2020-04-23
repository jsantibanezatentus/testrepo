<table width="100%" border="1" cellpadding="0" cellspacing="0">
<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
<tr>
	<td class="txtBlanco13b celdaTituloGris"  style="text-align: right;">Categoria</td>
	<td class="txtBlanco13b celdaTituloGris"  style="text-align: right;">Paso</td>
	<td class="txtBlanco13b celdaTituloGris"  style="text-align: right;">Mediciones</td>
	
	
	<!-- BEGIN BLOQUE_FECHA -->
	<td class="txtBlanco12b celdaTituloNaranjo" width="120" align="center"style="text-align: right;">{__fecha_titulo}</td>
	<!-- END BLOQUE_FECHA -->
</tr>
<!-- BEGIN BLOQUE_CATEGORIA -->

<!-- BEGIN BLOQUE_PASOS -->

<tr>
<!-- BEGIN BLOQUE_NOMBRE_CATEGORIA -->
	<td width="100px" style="text-align: right; color: #333333; font-family: Trebuchet MS, Verdana, sans-serif; font-size: 12px; padding: 1px 6px 1px 6px;  border: solid 1px #a2a2a2;" align="right" rowspan="{__rowspan_categoria}">{__categoria}</td>
<!-- END BLOQUE_NOMBRE_CATEGORIA -->

	<td width="100px" style="text-align: right; color: #333333; font-family: Trebuchet MS, Verdana, sans-serif; font-size: 12px; padding: 1px 6px 1px 6px;  border: solid 1px #a2a2a2;" align="right" rowspan="2">{__pasos}</td>
	<td class="txtGris12 celdaIteracion1" style="text-align: right;">Correctas</td>
	
	<!-- BEGIN BLOQUE_CORRECTO -->
	<td class="txtGris12 celdaIteracion1" style="text-align: right;">{__cantidad_ok}</td>
	<!-- END BLOQUE_CORRECTO -->
</tr>

<tr>
	<td class="txtGris12 celdaIteracion2" style="text-align: right;">Totales</td>
	
	<!-- BEGIN BLOQUE_TOTAL -->
	<td class="txtGris12 celdaIteracion2" style="text-align: right;">{__cantidad_totales}</td>
	<!-- END BLOQUE_TOTAL -->
</tr>

<!-- END BLOQUE_PASOS -->

<!-- END BLOQUE_CATEGORIA -->
</table>
<br>