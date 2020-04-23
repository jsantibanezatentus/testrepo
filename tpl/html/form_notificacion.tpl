<script>
function validarForm() {
	if (document.form_principal.notificacion_destinatario_id.options[document.form_principal.notificacion_destinatario_id.selectedIndex].value == 0) {
		alert("Debe ingresar un contacto.");
		return false;
	}
    if (document.form_principal.notificacion_escalabilidad_desde.value == "" ||
    	isNaN(document.form_principal.notificacion_escalabilidad_desde.value) ||
    	parseFloat(document.form_principal.notificacion_escalabilidad_desde.value) == 0) {
		alert("Debe ingresar un valor numerico para escalabilidad inicial.");
		return false;
    }
    if (isNaN(document.form_principal.notificacion_escalabilidad_hasta.value)) {
		alert("Debe ingresar un valor numerico para escalabilidad final.");
		return false;
    }
    if (document.form_principal.notificacion_escalabilidad_hasta.value > 20000) {
		alert("Debe ingresar un valor para escalabilidad final menor a 20000.");
		return false;
    }
    if (document.form_principal.notificacion_escalabilidad_hasta.value != "" &&
    	parseFloat(document.form_principal.notificacion_escalabilidad_desde.value) > parseFloat(document.form_principal.notificacion_escalabilidad_hasta.value)) {
		alert("Debe ingresar un valor para 'escalabilidad hasta' mayor o igual que 'escalabilidad desde'.");
		return false;
    }

	var inputs = document.getElementsByTagName("input");
	for (var i=0; i<inputs.length; i++) {
		if (inputs[i].type=="text") {
			if (inputs[i].name.match(/^paso_sla_/) && isNaN(inputs[i].value)) {
				alert("Debe ingresar un valor numerico para los umbrales.");
				inputs[i].focus();
				return false;
			}
		}
	}
    
	abrirAccion(1, 'guardar_notificacion');
}

function abrirDestinatario() {
	document.form_principal.menu_id.value = 43;
	abrirFormulario('destinatario', 0, 'modificar_destinatario', ['destinatario_id', '0', 'notificacion_id', '{__notificacion_id}', 'notificacion_horario_id', document.form_principal.notificacion_horario_id.options[document.form_principal.notificacion_horario_id.selectedIndex].value]);
	document.form_principal.menu_id.value = 39;
}

function validarFormContacto() {
	if (trim(dojo.byId("destinatario_nombre").value) == "") {
		alert("Debe ingresar un nombre.");
		return false;
	}

	if (existeNombreDestinatario(dojo.byId("destinatario_nombre").value, dojo.byId("destinatario_id").value) == "1") {
		alert("Ya existe el nombre en el sistema.");
		return false;
	}

	if (trim(dojo.byId("destinatario_contacto").value) == "") {
		alert("Debe ingresar una casilla de destino.");
		return false;
	}
	dojo.byId("menu_id").value = 39;
	dojo.byId("formDojo").submit();
}

</script>

<div dojoType="dijit.Dialog" id="dialog_destinatario" title="Informacion de Contacto"></div>

<input type="hidden" name="notificacion_id" value="{__notificacion_id}">
<input type="hidden" name="horario_id" value="0">
<input type="hidden" name="horario_tipo_id" value="6">

<table width="100%">
	<tr>
		<td class="tituloseccion">Informacion de Alerta</td>
	</tr>
	<tr>
		<td>
			<br>
			<div class="descripcion">
				&#8226; Desde este formulario podr&aacute; agregar nuevos contactos y horarios.<br>
				&#8226; La configuraci&oacute;n de env&iacute;o de alertas puede variar seg&uacute;n el servicio del objetivo seleccionado.<br>
				&#8226; Escalabilidad define la cantidad de eventos consecutivos que deben ocurrir para enviar la alerta (desde), y en que cantidad se dejar&aacute; de enviar la alerta (hasta).<br> 
			</div>
			<br>
		</td>
	</tr>
	<tr>
		<td>
			<table width="55%" class="formulario">
				<tr>
					<th>Contacto</th>
					<td colspan="2">
						<select name="notificacion_destinatario_id" style="float: left; position: relative;" {__form_disabled}>
							<!-- BEGIN DESTINATARIOS_NOTIFICACION -->
							<option value="{__destinatario_id}" {__destinatario_sel}>{__destinatario_nombre}</option>
							<!-- END DESTINATARIOS_NOTIFICACION -->
						</select>
						<div style="float: right; position: relative; padding: 2px; visibility: {__form_link_agregar};">
							<a href="#" class="textgris9" onclick="abrirDestinatario(); return false;">Agregar</a>
						</div>
					</td>
				</tr>
				<tr>
					<th>Horario</th>
					<td colspan="2">
						<select name="notificacion_horario_id" style="float: left; position: relative;" {__form_disabled}>
							<option value="0">Todo Horario</option>
							<!-- BEGIN HORARIOS_NOTIFICACION -->
							<option value="{__horario_id}" {__horario_sel}>{__horario_nombre}</option>
							<!-- END HORARIOS_NOTIFICACION -->
						</select>
						<div style="float: right; position: relative; padding: 2px; visibility: {__form_link_agregar};">
							<a href="#" class="textgris9" onclick="abrirAccion(0,'modificar_horario',['menu_id','41']);">Agregar</a>
						</div>
					</td>
				</tr>
				<tr>
					<th>Objetivo</th>
					<td colspan="2">
						<select name="notificacion_objetivo_id" onchange="abrirDetalles('detalle_notificacion','mostrar_notificacion_detalle',['objetivo_id',this.options[this.selectedIndex].value]);" {__form_disabled} style="width: 100%;">
							<!-- BEGIN OBJETIVOS_NOTIFICACION -->
							<option value="{__objetivo_id}">{__objetivo_nombre} ({__objetivo_servicio})</option>
							<!-- END OBJETIVOS_NOTIFICACION -->
						</select>
					</td>
				</tr>
				<tr>
					<th>Escalabilidad</th>
					<td width="35%">
						desde <input type="text" size="2" value="{__notificacion_escalabilidad_desde}" name="notificacion_escalabilidad_desde" {__form_disabled}>
					</td>
					<td width="35%"> 
						hasta <input type="text" size="2" value="{__notificacion_escalabilidad_hasta}" name="notificacion_escalabilidad_hasta" {__form_disabled}>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="40"></td>
	</tr>
	<tr>
		<td>
			<div dojoType="dojox.layout.ContentPane" id="detalle_notificacion" style="overflow:hidden;"></div>
		</td>
	</tr>
	<tr>
		<td>
			<br>
			<!-- BEGIN PUEDE_MODIFICAR -->
			<br>
				<div class="boton_accion" onclick="validarForm();">Ejecutar </div>
			<br>
			<br>
			<!-- END PUEDE_MODIFICAR -->
		</td>
	</tr>
</table>

<script>
dojo.addOnLoad(function() {
	abrirDetalles('detalle_notificacion','mostrar_notificacion_detalle',['notificacion_id','{__notificacion_id}','objetivo_id',document.form_principal.notificacion_objetivo_id.options[document.form_principal.notificacion_objetivo_id.selectedIndex].value]);
});
</script>