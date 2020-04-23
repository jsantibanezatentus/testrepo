<div dojoType="dijit.form.Form" encType="multipart/form-data" action="index.php" method="POST" name="form_subcliente" id="form_subcliente" style="margin: 0px;">

<input type="hidden" name="sitio_id" value="{__accion_sitio_id}">
<input type="hidden" name="menu_id" value="{__accion_menu_id}">
<input type="hidden" name="subcliente_id" id="subcliente_id" value="{__subcliente_id}">
<input type="hidden" name="accion" value="guardar_subcliente">
<input type="hidden" name="ejecutar_accion" value="1">

<table width="450" class="formulario">
	<tr>
		<th>Nombre</th>
		<td><input type="text" name="subcliente_nombre" id="subcliente_nombre" value="{__subcliente_nombre}" size="30" maxlength="30" {__form_disabled}></td>
	</tr>
	<tr>
		<th>Descripcion</th>
		<td><input type="text" name="subcliente_descripcion" id="subcliente_descripcion" value="{__subcliente_descripcion}" size="30" {__form_disabled} /></td>
	</tr>
</table>
<br>
<!-- BEGIN PUEDE_MODIFICAR -->
<table align="center">
	<tr>
		<td style="text-align:right">
			<input type="button" class="boton_accion" onclick="validarFormSubcliente()" value="Guardar" />
		</td>
		<td style="width:20px">&nbsp;</td>
		<td style="text-align:left">
			<input type="button" value="Cancelar" class="boton_cancelar" onclick="dijit.byId('dialog_subcliente').hide();" />
		</td>
	</tr>
</table>

<!-- END PUEDE_MODIFICAR -->
</div>