
<div dojoType="dijit.form.Form" encType="multipart/form-data" action="index.php" method="POST" name="formDojo" id="formDojo" style="margin: 0px;">

<!-- Inputs usados solo en el dijit.Dialog -->
<input type="hidden" name="sitio_id" value="{__accion_sitio_id}">
<input type="hidden" name="menu_id" id="menu_id" value="{__accion_menu_id}">
<input type="hidden" name="destinatario_id" id="destinatario_id" value="{__destinatario_id}">
<input type="hidden" name="usuario_cliente_id" value="{__usuario_cliente_id}">
<input type="hidden" name="accion" value="guardar_destinatario">
<input type="hidden" name="ejecutar_accion" value="1">

<input type="hidden" name="notificacion_id" id="notificacion_id" value="{__notificacion_id}">
<input type="hidden" name="notificacion_horario_id" id="notificacion_horario_id" value="{__notificacion_horario_id}">

<table width="450" class="formulario">
	<tr>
		<th>Nombre</th>
		<td><input type="text" name="destinatario_nombre" id="destinatario_nombre" value="{__destinatario_nombre}" {__form_disabled}></td>
	</tr>
	<tr>
		<th>Casilla de Destino</th>
		<td><input type="text" name="destinatario_contacto" id="destinatario_contacto" value="{__destinatario_contacto}" {__form_disabled}></td>
	</tr>
	<tr>
		<th>Tipo</th>
		<td>
			<select name="destinatario_tipo" {__form_disabled}>
				<!-- BEGIN TIPOS_DESTINATARIOS -->
				<option value="{__destinatario_tipo_id}" {__destinatario_tipo_sel}>{__destinatario_tipo_nombre}</option>
				<!-- END TIPOS_DESTINATARIOS -->
			</select>
		</td>
	</tr>
	<tr>
		<th>Teléfono</th>
		<td><input type="text" name="destinatario_telefono" id="destinatario_telefono" value="{__destinatario_telefono}" {__form_disabled}></td>
	</tr>
</table>
<br>
<!-- BEGIN PUEDE_MODIFICAR -->
	<table align="center">
		<tr>
			<td style="text-align:right">
				<input type="button" class="boton_accion" onclick="validarFormContacto()" value="Guardar" />
			</td>
			<td style="width:20px">&nbsp;</td>
			<td style="text-align:left">
				<input type="button" class="boton_cancelar" value="Cancelar" onclick="dijit.byId('dialog_destinatario').hide();" />
			</td>
		</tr>
	</table>
<!-- END PUEDE_MODIFICAR -->
</div>