<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>
<table class="formulario" width="60%">
	<tr>
		<th>Juego de Caracteres</th>
		<td>
			<select name="datos_codificacion_{__item_id}" id="datos_codificacion_{__item_id}">
				<option value="0" selected>Windows (LATIN1)</option>
				<option value="1">Otros (UTF-8)</option>
			</select>
		</td>
	</tr>
	<tr>
		<th>Caracter Decimal</th>
		<td>
			<input type="radio" name="datos_decimal_{__item_id}" value="0" checked> Punto &nbsp;&nbsp;&nbsp;
			<input type="radio" name="datos_decimal_{__item_id}" value="1"> Coma
		</td>
	</tr>
	<tr>
		<th>Disponibilidad Mostrada</th>
		<td>
			<input type="checkbox" name="datos_uptime_{__item_id}" id="datos_uptime_{__item_id}" checked> Uptime<br>
			<input type="checkbox" name="datos_downtime_parcial_{__item_id}" id="datos_downtime_parcial_{__item_id}"> Downtime Parcial<br>
			<input type="checkbox" name="datos_downtime_global_{__item_id}" id="datos_downtime_global_{__item_id}"> Downtime Global<br>
			<input type="checkbox" name="datos_nomonitoreo_{__item_id}" id="datos_nomonitoreo_{__item_id}"> No Monitoreo<br>
			<input type="checkbox" name="datos_evento_cliente_{__item_id}" id="datos_evento_cliente_{__item_id}"> Evento Cliente
		</td>
	</tr>
	<tr>
		<th colspan="100%" align="center" style="border-top: 5px solid #ffffff;">
			Ejemplo CSV<br>
			<img src="{__datos_imagen}" style="background-color: #ffffff" width="456"/>
		</th>
	</tr>
</table>
<br>
<input type="button" class="boton_accion" onclick="descargarCSV({__item_id}); return false;" value="Descargar CSV"></td>
<script>
$(function() {
	name = '{__name}';
	// Ejecuta la inialización del acordeon.
	if ('{__tiene_evento}' == 'true'){		
		createAccordion(name);	
	}
});
</script>