<div style="page-break-inside: avoid;">
<table width="100%">
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris" align="left">
			<div class="celdaNoWrap" style="width:180px;">Paso</div>
		</td>
		<!-- BEGIN LISTA_NODOS_TITULO -->
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">
			<div class="celdaNoWrap" style="width: 100px;">{__nodo_nombre}</div>
		</td>
		<!-- END LISTA_NODOS_TITULO -->
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">
			<div class="celdaNoWrap" style="width: 100px;">Global</div>
		</td>
	</tr>
	<!-- BEGIN LISTA_PASOS -->
	<tr>
		<td class="txtGris12 {__print_class}" align="left">{__paso_nombre}</td>
		<!-- BEGIN LISTA_DATOS -->
		<td class="{__print_class}" align="center">
			<span class="txtGris12">{__parcial_rendimiento}&nbsp;/</span>
			<span class="txtUptime">{__parcial_uptime}%</span>
		</td>
		<!-- END LISTA_DATOS -->
		<td class="{__print_class}" align="center">
			<span class="txtGris12">{__paso_rendimiento}&nbsp;/</span>
			<span class="txtUptime">{__paso_uptime}%</span>
		</td>
	</tr>
	<!-- END LISTA_PASOS -->
	<tr>
		<td class="txtBlanco12 celdaObjetivo" align="left">Total</td>
		<!-- BEGIN LISTA_NODOS_DATOS -->
		<td class="txtBlanco12 celdaObjetivo" width="100px" align="center">{__nodo_rendimiento}</td>
		<!-- END LISTA_NODOS_DATOS -->
		<td class="txtBlanco12 celdaObjetivo" width="100px" align="center">{__rendimiento_total}</td>
	</tr>
</table>
</div>