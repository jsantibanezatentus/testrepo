<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>
<!-- BEGIN LISTA_EVENTOS_INICIO -->
<!-- sin datos -->
<!-- END LISTA_EVENTOS_INICIO -->
<!-- BEGIN LISTA_EVENTOS_FALTANTES -->
<!-- sin datos -->
<!-- END LISTA_EVENTOS_FALTANTES -->
<!-- BEGIN LISTA_EVENTOS_DURACION -->
<!-- sin datos -->
<!-- END LISTA_EVENTOS_DURACION -->

<div style="page-break-inside: avoid;">
<table width="100%" align="center">
	<tr>
		<td colspan="100%" class="txtNegro13" style="padding: 6px; border: solid 1px #ffffff;">{__item_orden}.{__monitor_orden}. {__monitor_nombre}</td>
	</tr>
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris">Paso</td>
		<td class="txtBlanco13b celdaTituloGris">Fecha</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">Estado</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">Duraci&oacute;n</td>		
	</tr>
	<!-- BEGIN LISTA_PASOS -->

	<!-- BEGIN LISTA_EVENTOS -->
	<tr>
		<td class="txtGris12 {__print_class}">{__paso_nombre}</td>
		<td class="txtGris12 {__print_class}">{__evento_inicio}</td>
		<td class="txtGris12 {__print_class}" align="center">
			<!-- BEGIN LISTA_EVENTOS_PATRONES -->
			{__evento_nombre}<br>
			<!-- END LISTA_EVENTOS_PATRONES -->
		</td>
		<td class="txtGris12 {__print_class}" align="center">{__evento_duracion_print}</td>
	</tr>
	<!-- END LISTA_EVENTOS -->

	<!-- END LISTA_PASOS -->
</table>
</div>
<br>

<script type="text/javascript">
$(function() {
	// Ejecuta la inialización del acordeon.
	if ('{__tiene_evento}' == 'true'){
		$('#man').show();
		createAccordion('accordionEvento');	
	}
});
</script>