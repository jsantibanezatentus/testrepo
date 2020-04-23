<div dojoType="dijit.form.Form" encType="multipart/form-data" action="index.php" method="POST" name="form_token_ws" id="form_token_ws" style="margin: 0px;">

<input type="hidden" name="sitio_id" value="{__accion_sitio_id}">
<input type="hidden" name="menu_id" value="{__accion_menu_id}">
<input type="hidden" name="token_id" id="token_id" value="{__token_id}">
<input type="hidden" name="accion" value="guardar_token">
<input type="hidden" name="ejecutar_accion" value="1">

<table width="450" class="formulario">
	<tr>
		<th>Nombre</th>
		<td><input type="text" name="token_nombre" id="token_nombre" value="{__token_nombre}" size="30" maxlength="30" {__form_disabled}></td>
	</tr>
	<tr>
		<th>Key</th>
		<td>{__token_key}</td>
	</tr>
<!-- 	<tr>
		<th>Fecha Inicio</th>
		<td><input dojoType="dijit.form.DateTextBox" type="text" id="token_expiracion" name="token_expiracion" value="{__token_expiracion}"/></td>
	</tr> -->
</table>
<br>
<!-- BEGIN PUEDE_MODIFICAR -->
<input type="button" class="boton_accion" value="Guardar" onclick="validarFormTokenWS();">
<!-- END PUEDE_MODIFICAR -->
</div>