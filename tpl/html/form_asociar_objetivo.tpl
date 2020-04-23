<div dojoType="dijit.form.Form" encType="multipart/form-data" action="index.php" method="POST" name="form_asociar_subcliente" id="form_asociar_subcliente" style="margin: 0px;">

<input type="hidden" name="sitio_id" value="{__accion_sitio_id}">
<input type="hidden" name="menu_id" value="{__accion_menu_id}">
<input type="hidden" name="subcliente_id" value="{__subcliente_id}">
<input type="hidden" name="accion" value="asociar_subcliente_objetivos">
<input type="hidden" name="ejecutar_accion" value="1">

<div dojoType="dijit.layout.ContentPane" style="width:700px; max-height: 500px; height: expression(this.scrollHeight>299?'300px':'auto');">
<table width="100%" class="listado">
	<tr>
		<th>Objetivo</th>
		<th>Nodos</th>
		<th>Servicio</th>
		<th>&nbsp;</th>
	</tr>
	<!-- BEGIN OBJETIVOS_SUBCLIENTE -->
	<tr>
		<td width="370" title="{__objetivo_nombre}">
			<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 350px;">{__objetivo_nombre}</div>
		</td>
		<td width="60" align="center">{__objetivo_nodos}</td>
		<td><div class="textnegro10b">{__objetivo_servicio}</div></td>
		<td width="50" align="center">
			<input type="checkbox" name="subcliente_objetivo_{__objetivo_id}" {__objetivo_sel} {__form_disabled} />
		</td>
	</tr>
	<!-- END OBJETIVOS_SUBCLIENTE -->
</table>
</div>
<br>
<!-- BEGIN PUEDE_MODIFICAR -->

<table  align="center">
	<tr>
		<td style="text-align:right">
			<button  type="submit" class="boton_accion">Guardar</button>
		</td>
		<td style="width:20px">&nbsp;</td>
		<td style="text-align:left">
			<input type="button" class="boton_cancelar" value="Cancelar" onclick="dijit.byId('dialog_subcliente').hide();" />
		</td>
	</tr>
</table>
<!-- END PUEDE_MODIFICAR -->
</div>
