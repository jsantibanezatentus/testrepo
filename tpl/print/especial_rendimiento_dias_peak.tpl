<!-- BEGIN LISTA_FECHAS -->
<div style="page-break-inside: avoid;">
<table width="100%">
	<tr>
		<td colspan="100%" class="txtNegro13" style="padding: 6px; border: solid 1px #ffffff;">{__item_orden}.{__fecha_orden}. {__fecha_descripcion}</td>
	</tr>
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris">Paso</td>
		<!-- BEGIN LISTA_PONDERACION_TITULO -->
		<td class="txtBlanco12b celdaTituloNaranjo" width="90" align="center">{__ponderacion_periodo}</td>
		<!-- END LISTA_PONDERACION_TITULO -->
		<td class="txtBlanco12b celdaTituloNaranjo" width="90" align="center">Promedio</td>
	</tr>
	<!-- BEGIN LISTA_PASOS -->
	<tr>
		<td class="txtGris12 {__paso_estilo}">{__paso_nombre}</td>
		<!-- BEGIN LISTA_PONDERACION -->
		<td class="txtGris12 {__paso_estilo}" align="right">{__paso_valor}</td>
		<!-- END LISTA_PONDERACION -->
		<td class="txtGris12b {__paso_estilo}" align="right">{__paso_total}</td>
	</tr>
	<!-- END LISTA_PASOS -->
	<tr>
		<td class="txtGris12b {__promedio_estilo}">Promedio</td>
		<!-- BEGIN LISTA_PONDERACION_PROMEDIO -->
		<td class="txtGris12b {__promedio_estilo}" align="right">{__fecha_valor}</td>
		<!-- END LISTA_PONDERACION_PROMEDIO -->
		<td class="txtGris12b {__promedio_estilo}" align="right">{__fecha_total}</td>
	</tr>	
</table>
</div>
<br>
<!-- END LISTA_FECHAS -->

<div style="page-break-inside: avoid;">
<table width="100%">
	<tr>
		<td colspan="100%" class="txtNegro13" style="padding: 6px; border: solid 1px #ffffff;">{__item_orden}.4. Tiempos Promedios</td>
	</tr>
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris">Fecha</td>
		<!-- BEGIN LISTA_PONDERACION_TITULO -->
		<td class="txtBlanco12b celdaTituloNaranjo" width="90" align="center">{__ponderacion_periodo}</td>
		<!-- END LISTA_PONDERACION_TITULO -->
		<td class="txtBlanco12b celdaTituloNaranjo" width="90" align="center">Promedio</td>
	</tr>
	<!-- BEGIN RESUMEN_FECHAS -->
	<tr>
		<td class="txtGris12 {__resumen_estilo}">{__fecha_descripcion}</td>
		<!-- BEGIN RESUMEN_PONDERACION -->
		<td class="txtGris12 {__resumen_estilo}" align="right">{__fecha_valor}</td>
		<!-- END RESUMEN_PONDERACION -->
		<td class="txtGris12b {__resumen_estilo}" align="right">{__fecha_total}</td>
	</tr>
	<!-- END RESUMEN_FECHAS -->
	<tr>
		<td class="txtGris12b {__promedio_estilo}">Promedio</td>
		<!-- BEGIN RESUMEN_PONDERACION_PROMEDIO -->
		<td class="txtGris12b {__promedio_estilo}" align="right">{__ponderacion_valor}</td>
		<!-- END RESUMEN_PONDERACION_PROMEDIO -->
		<td class="txtGris12b {__promedio_estilo}" align="right">{__ponderacion_total}</td>
	</tr>
</table>
</div>