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
			<div class="celdaNoWrap" style="width: 100px;">Uptime</div>
		</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">
			<div class="celdaNoWrap" style="width: 100px;">Downtime</div>
		</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">
			<div class="celdaNoWrap" style="width: 100px;">Downtime Parcial</div>
		</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">
			<div class="celdaNoWrap" style="width: 100px;">T. Respuesta</div>
		</td>
	</tr>
	<!-- BEGIN LISTA_PASOS -->
	<tr>
		<td class="txtGris12 {__print_class}" align="left">{__paso_nombre}</td>
		<!-- BEGIN LISTA_DATOS -->
		<td class="{__print_class}" align="center">
			<span class="txtGris12">{__parcial_rendimiento}&nbsp;/</span>
			<span class="txtUptime">{__parcial_uptime}%&nbsp;</span>
		</td>
		<!-- END LISTA_DATOS -->
		<td class="{__print_class}" align="center">
			<span class="txtUptime">{__paso_uptime}%</span>
		</td>
		<td class="{__print_class}" align="center">
			<span class="txtDtGlobal">{__paso_downtime}%</span>
		</td>
		<td class="{__print_class}" align="center">
			<span class="txtDtParcial">{__paso_downtime_parcial}%</span>
		</td>
		<td class="{__print_class}" align="center">
			<span class="txtGris12">{__paso_tiempo_respuesta}</span>
		</td>
	</tr>
	<!-- END LISTA_PASOS -->
	<tr>
		<td class="txtBlanco12 celdaObjetivo" align="left">Total</td>
		<!-- BEGIN LISTA_NODOS_DATOS -->
		<td class="txtBlanco12 celdaObjetivo" width="100px" align="center">{__nodo_rendimiento}</td>
		<!-- END LISTA_NODOS_DATOS -->
		<td class="txtBlanco12 celdaObjetivo " width="100px" align="center">{__rendimiento_uptime}%</td>
		<td class="txtBlanco12 celdaObjetivo" width="100px" align="center">{__rendimiento_downtime}%</td>
		<td class="txtBlanco12 celdaObjetivo" width="100px" align="center">{__rendimiento_parcial}%</td>
		<td class="txtBlanco12 celdaObjetivo" width="100px" align="center">{__rendimiento_tiempo_respuesta}</td>
	</tr>
</table>
</div> 
