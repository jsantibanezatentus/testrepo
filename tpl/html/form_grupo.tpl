<div dojoType="dijit.form.Form" encType="multipart/form-data" action="index.php" method="POST" name="form_grupo" id="form_grupo">
	<input type="hidden" name="sitio_id" value="{__accion_sitio_id}">
	<input type="hidden" name="menu_id" value="{__accion_menu_id}">
	<input type="hidden" name="grupo_id" value="{__grupo_id}">
	<input type="hidden" name="accion" value="guardar_grupo">
	<input type="hidden" name="ejecutar_accion" value="1">

	<table width="450" border="0" cellpadding="0" cellspacing="0" class="formulario">
		<tr>
			<th>Nombre</th>
			<td><input type="text" name="grupo_nombre" value="{__grupo_nombre}"></td>
		</tr>
		<tr>
			<th>Descripcion</th>
			<td><input type="text" name="grupo_descripcion" value="{__grupo_descripcion}"></td>
		</tr>
	</table>
	<br>	
	<table align="center">
		<tr>
			<td style="text-align:right">
				<button  type="submit" class="boton_accion">Guardar</button>
			</td>
			<td style="width:20px">&nbsp;</td>
			<td style="text-align:left">
				<input type="button" value="Cancelar" class="boton_cancelar" onclick="dijit.byId('dialog_subcliente').hide();" />
			</td>
		</tr>
	</table>
	
</div>
	