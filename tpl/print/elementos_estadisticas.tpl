<div style="page-break-inside: avoid;">
<table width="100%">
	<tr>
		<td colspan="100%" class="txtNegro13" style="padding: 6px; border: solid 1px #ffffff;">{__item_orden}.{__monitor_orden}. {__monitor_nombre}</td>		
	</tr>
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris" width="180">Elemento</td>
		<td class="txtBlanco13b celdaTituloGris">Tipo</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">Tamaño [KB]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">Minimo [s]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">Maximo [s]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">Promedio [s]</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">Codigo (Cantidad)</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">Total</td>
	</tr>
	
	<!-- BEGIN LISTA_ELEMENTOS -->
	<tr>
		<td class="txtGris12 {__print_class}">
			<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 175px;">{__elemento_url}</div>
		</td>
		<td class="txtGris12 {__print_class}" align="center">{__elemento_tipo_nombre}</td>
		<td class="txtGris12 {__print_class}" align="right">{__elemento_tamanno}</td>
		<td class="txtGris12 {__print_class}" align="right">{__elemento_minimo}</td>
		<td class="txtGris12 {__print_class}" align="right">{__elemento_maximo}</td>
		<td class="txtGris12 {__print_class}" align="right">{__elemento_promedio}</td>
		<td class="txtGris12 {__print_class}">
			<!-- BEGIN LISTA_EVENTOS -->
			{__evento_nombre} ({__evento_cantidad})<br>
			<!-- END LISTA_EVENTOS -->
		</td>
		<td class="txtGris12 {__print_class}" align="right">{__elemento_cantidad}</td>
	</tr>
	<!-- END LISTA_ELEMENTOS -->
	
</table>
</div>
<br>
