<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>
<!-- BEGIN BLOQUE_HORARIOS -->
<table width="100%">
	<!-- BEGIN BLOQUE_TITULO_HORARIOS -->
	<tr>
		<td style="border: solid 1px #ffffff;" class="celdanegra50" colspan="100%">{__horario_nombre}</td>
	</tr>
	<!-- END BLOQUE_TITULO_HORARIOS -->
	<tr>
		<td style="border: solid 1px #ffffff;" class="celdanegra40"  width="40%" align="left">Paso</td>
		<!-- BEGIN BLOQUE_EVENTOS_TITULOS -->
		<td style="border: solid 1px #ffffff;" class="celdanegra40"  width="15%" align="center">{__evento_nombre} [%]</td>
		<!-- END BLOQUE_EVENTOS_TITULOS -->	
	</tr>
	<!-- BEGIN BLOQUE_PASOS -->
	<tr >
		<td style="border: solid 1px #ffffff;" class="{__class}" align="left">{__pasoNombre}</td>
		<!-- BEGIN BLOQUE_EVENTOS -->
		<td style="border: solid 1px #ffffff; padding: 2px; background-color: #{__evento_color};" class="textblanco12" align="right">{__evento_valor}</td>
		<!-- END BLOQUE_EVENTOS -->
	</tr>
	<!-- END BLOQUE_PASOS -->
</table>
<br>
<!-- END BLOQUE_HORARIOS -->
<script type="text/javascript">
$(function() {
	// Ejecuta la inialización del acordeon.
	if ('{__tiene_evento}' == 'true'){
		$('#man').show();
		createAccordion('{__name}');	
	}
});
</script>