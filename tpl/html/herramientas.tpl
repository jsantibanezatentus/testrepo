<script>
function validarForm() {
	if (trim(document.form_principal.host.value) == "") {
		alert("No puede ingresar la URL en blanco.");
		document.form_principal.host.focus();
		return false;
	}
	var inputs = document.getElementsByTagName("input");
	var tiene_monitores = false;
	for (var i=0; i<inputs.length; i++) {
		if (inputs[i].type=="checkbox") {
			if (inputs[i].checked == true) {
				var tiene_monitores = true;
			}
		}
	}
	if (tiene_monitores==false) {
		alert("Debe seleccionar un monitor desde donde ejecutar la herramienta.");
		return false;
	}
	abrirAccion(0,'iniciar_herramienta');
}
</script>

<input type="hidden" name="attool_id" value="0">

<table width="100%">
	<tr>
		<td class="tituloseccion">{__herramienta_nombre}</td>
	</tr>
	<tr>
		<td>
			<br>
			<div class="descripcion">
				{__herramienta_descripcion}
			</div>
			<br>
		</td>
	</tr>
	<!-- BEGIN TIENE_NODOS -->
	<tr>
		<td>
			<table width="50%" class="formulario">
				<tr>
					<th width="80">URL</th>
					<td><input name="host" id="host" size="40" type="text" value="{__herramienta_host}" class="inputtextbox"></td>
				</tr>
			</table>
			<br>
			<table width="50%" class="formulario">
				<tr>
					<th width="30">&nbsp;</th>
					<th>Monitor</th>
					<th>Hostname</th>
				</tr>
				<!-- BEGIN LISTA_MONITORES -->
				<tr>
					<td align="center">
						<input type="checkbox" name="monitor_{__monitor_id}" {__monitor_sel}>
					</td>
					<td>{__monitor_nombre}</td>
					<td>{__monitor_host}</td>
				</tr>
				<!-- END LISTA_MONITORES -->
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<br>
			<table align="center">
				<tr>
					<td style="text-align:right">
						<input type="button" value="Ejecutar" onclick="validarForm();" class="boton_accion" />
					</td>
					<td style="width:20px">&nbsp;</td>
					<td style="text-align:left">
						<input type="button"  class="boton_cancelar" value="Cancelar" onclick="$('#host').val('');$('input:checkbox').removeAttr('checked');mostrarSubmenu({__padre_id},{__seccion_id},{__nivel}); " />
					</td>
				</tr>
			</table>
			<br>
		</td>
	</tr>
	<!-- END TIENE_NODOS -->
	<!-- BEGIN TIENE_MENSAJE_NODOS -->
	<tr>
		<td>
			<br>
			<table align="center">
				<tr>
					<td width="20"><img src="img/advertencia.png" border="0"></td>
					<td align="center" class="textgris12">No tiene nodos configurados para ejecutar esta herramienta.</td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- END TIENE_MENSAJE_NODOS -->
	
	<!-- BEGIN LISTA_MONITORES_RESULTADO -->
	<tr>
		<td class="celdanegra50">{__monitor_nombre}</td>
	</tr>
	<!-- BEGIN TIENE_ERROR -->
	<tr>
		<td>
			<br>
			<table align="center">
				<tr>
					<td width="20"><img src="img/advertencia.png" border="0"></td>
					<td align="center" class="textgris12">No se encontraron datos desde este monitor.</td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- END TIENE_ERROR -->
	<tr>
		<td>
			<table width="100%">
<!-- 				<tr>
					<td class="celdanegra20" colspan="100%">Resultado</td>
				</tr> -->
				<!-- BEGIN RESULTADO_FILA -->
				<tr>
					<!-- BEGIN RESULTADO_COLUMNA -->
					<td class="{__resultado_class}" colspan="{__resultado_colspan}">{__resultado_valor}</td>
					<!-- END RESULTADO_COLUMNA -->
				</tr>
				<!-- END RESULTADO_FILA -->
			</table>
			<br>
		</td>
	</tr>
	<!-- END LISTA_MONITORES_RESULTADO -->
</table>