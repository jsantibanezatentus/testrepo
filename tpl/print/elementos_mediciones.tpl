<!-- BEGIN LISTA_MONITORES_BORRAR -->
<!-- sin datos -->
<!-- END LISTA_MONITORES_BORRAR -->
<!-- BEGIN LISTA_MONITORES_NOMBRE -->
<!-- sin datos -->
<!-- END LISTA_MONITORES_NOMBRE -->

<div style="page-break-inside: avoid;">
<table width="100%" align="center">
	<tr>
		<td colspan="100%" class="txtNegro13" style="padding: 6px; border: solid 1px #ffffff;">{__item_orden}.{__monitor_orden}. {__monitor_nombre}</td>		
	</tr>
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris">Fecha Monitoreo</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="80" align="center">Elementos</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="80" align="center">Tamaño<br>(KB)</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="80" align="center">Respuesta<br>(segs)</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="180" align="center">Estado</td>
	</tr>
	<!-- BEGIN LISTA_MONITOREOS -->
	<tr>
		<td class="txtGris12 {__print_class}">{__monitoreo_fecha_mostrar}</td>
		<td class="txtGris12 {__print_class}" align="right">{__monitoreo_elementos}</td>
		<td class="txtGris12 {__print_class}" align="right">{__monitoreo_tamanno}</td>
		<td class="txtGris12 {__print_class}" align="right">{__monitoreo_respuesta}</td>
		<td class="txtGris12 {__print_class}" align="center">{__monitoreo_estado_nombre}</td>
	</tr>
	<!-- END LISTA_MONITOREOS -->
</table>
</div>
<br>