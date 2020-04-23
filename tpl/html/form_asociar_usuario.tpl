<div dojoType="dijit.form.Form" encType="multipart/form-data" action="index.php" method="POST" name="form_asociar_usuario" id="form_asociar_usuario" style="margin: 0px;">

<input type="hidden" name="sitio_id" value="{__accion_sitio_id}">
<input type="hidden" name="menu_id" value="{__accion_menu_id}">
<input type="hidden" name="subcliente_id" value="{__subcliente_id}">
<input type="hidden" name="accion" value="asociar_subcliente_usuarios">
<input type="hidden" name="ejecutar_accion" value="1">

<div dojoType="dijit.layout.ContentPane" style="width:450px; max-height: 300px; height: expression(this.scrollHeight>299?'300px':'auto');">
<table width="100%" class="listado">
	<tr>
		<th>Usuario</th>
		<th>E-Mail</th>
		<th>&nbsp;</th>
	</tr>
	<!-- BEGIN USUARIOS_SUBCLIENTE -->
	<tr>
		<td>{__usuario_cliente_nombre}</td>
		<td>{__usuario_cliente_email}</td>
		<td width="50" align="center">
			<input type="checkbox" name="subcliente_usuario_{__usuario_cliente_id}" {__usuario_cliente_sel} {__form_disabled} />
		</td>
	</tr>
	<!-- END USUARIOS_SUBCLIENTE -->					
</table>
</div>
<br>
<!-- BEGIN PUEDE_MODIFICAR -->
<table align="center">
	<tr>
		<td style="text-align:right">
			<button  type="submit" style="border:0px" class="boton_accion">Guardar</button>
		</td>
		<td style="width:20px">&nbsp;</td>
		<td style="vtext-align:left">
			<input type="button" class="boton_cancelar" value="Cancelar" onclick="dijit.byId('dialog_subcliente').hide();" />
		</td>
	</tr>
</table>
<!-- END PUEDE_MODIFICAR -->
</div>