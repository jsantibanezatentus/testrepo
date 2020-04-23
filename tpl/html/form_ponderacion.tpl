<script>
function validarForm() {
	var inputs = document.getElementsByTagName("input");

	var suma = 0;
	for (var i=0; i<inputs.length; i++) {
		if (inputs[i].type=="text") {
			if (inputs[i].name.match(/^ponderacion_/)) {
				if (parseFloat(inputs[i].value) > 0 && parseFloat(inputs[i].value) <= 100) {
					// Parece tonto esta multiplicacion, pero js tiene problemas para igualar con varios decimales
					suma = suma + (parseFloat(inputs[i].value) * 1000);
				}
				else {
					inputs[i].value = 0;
				}
			}
		}
	}

	if ((suma/1000) != 100) {
		alert("La suma de las ponderaciones por horario debe ser 100%.");
		return false;
	}
	abrirAccion(1,'guardar_ponderacion');
}
</script>
<table width="100%">
	<tr>
		<td class="tituloseccion">{__sitio_titulo}</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
			<table width="50%" class="formulario">
				<tr>
					<th>Intervalo de Divisi&oacute;n</th>
					<td>
						<select name="intervalo_id" id="intervalo_id" onchange="abrirDetalles('detalle_ponderacion','mostrar_ponderacion_detalle',['intervalo_id',this.options[this.selectedIndex].value]);" {__disabled}>
							<!-- BEGIN LISTA_INTERVALOS -->
							<option value="{__intervalo_id}" {__intervalo_sel}>{__intervalo_nombre}</option>
							<!-- END LISTA_INTERVALOS -->
						</select>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
			<div dojoType="dojox.layout.ContentPane" id="detalle_ponderacion" style="overflow:hidden;"></div>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<!-- BEGIN ACCIONES -->
	<tr>
		<td>
			<table align="center">
				<tr>
					<td style="text-align:right">
						<input type="button" class="boton_accion" onclick="validarForm()" value="Guardar"/>								
					</td>
					<td style="width:20px">&nbsp;</td>
					<td style="text-align:left">
						<input type="button" class="boton_cancelar" value="Cancelar" onclick="mostrarSubmenu({__padre_id},{__seccion_id},{__nivel})" />
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- END ACCIONES -->
</table>
<script>
abrirDetalles('detalle_ponderacion','mostrar_ponderacion_detalle',['intervalo_id', document.getElementById("intervalo_id").options[document.getElementById("intervalo_id").selectedIndex].value]);
</script>