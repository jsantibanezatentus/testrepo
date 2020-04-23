<!-- BEGIN TIENE_MOSTRAR_DETALLES -->
<!-- sin datos -->
<!-- END TIENE_MOSTRAR_DETALLES -->

<div style="page-break-inside: avoid;">
<table width="100%" align="center">
	<tr>
		<td colspan="100%" class="txtNegro13" style="padding: 6px; border: solid 1px #ffffff;">{__item_orden}.{__monitor_orden}. {__monitor_nombre}</td>		
	</tr>
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris">Fecha</td>
		<td class="txtBlanco12b celdaTituloNaranjo">Duracion</td>
		<td class="txtBlanco12b celdaTituloNaranjo">Estado</td>
		<td class="txtBlanco12b celdaTituloNaranjo" {__tiene_servidor}>Servidor</td>
		<td class="txtBlanco12b celdaTituloNaranjo" {__tiene_primario}>Dns Primario</td>
		<td class="txtBlanco12b celdaTituloNaranjo" {__tiene_serial}>Serial</td>
		<td class="txtBlanco12b celdaTituloNaranjo" {__tiene_nombres}>Nombre</td>
		<td class="txtBlanco12b celdaTituloNaranjo" {__tiene_tipos}>Tipo</td>
		<td class="txtBlanco12b celdaTituloNaranjo" {__tiene_prioridad}>Prioridad</td>
		<td class="txtBlanco12b celdaTituloNaranjo" {__tiene_respuestas}>Respuesta</td>
	</tr>
	<!-- BEGIN LISTA_REGISTROS -->
	<tr>
		<td class="txtGris12 {__print_class}">{__registro_fecha}&nbsp;</td>
		<td class="txtGris12 {__print_class}">{__registro_duracion}&nbsp;</td>
		<td class="txtGris12 {__print_class}">{__registro_estado_nombre}&nbsp;</td>
		<td class="txtGris12 {__print_class}" {__tiene_servidor}>{__registro_servidor}&nbsp;</td>
		<td class="txtGris12 {__print_class}" {__tiene_primario}>{__registro_primario}</td>
		<td class="txtGris12 {__print_class}" {__tiene_serial}>{__registro_serial}</td>
		<td class="txtGris12 {__print_class}" {__tiene_nombres}>
			<!-- BEGIN LISTA_NOMBRES -->
			{__registro_nombre}&nbsp;<br>
			<!-- END LISTA_NOMBRES -->
		</td>
		<td class="txtGris12 {__print_class}" {__tiene_tipos}>
			<!-- BEGIN LISTA_TIPOS -->
			{__registro_tipo}<br>
			<!-- END LISTA_TIPOS -->
		</td>
		<td class="txtGris12 {__print_class}" {__tiene_prioridad}>
			<!-- BEGIN LISTA_PRIORIDAD -->
			{__registro_prioridad}<br>
			<!-- END LISTA_PRIORIDAD -->
		</td>
		<td class="txtGris12 {__print_class}" {__tiene_respuestas}>
			<!-- BEGIN LISTA_RESPUESTAS -->
			{__registro_respuesta}<br>
			<!-- END LISTA_RESPUESTAS -->
		</td>
	</tr>
	<!-- END LISTA_REGISTROS -->
</table>
</div>
<br>