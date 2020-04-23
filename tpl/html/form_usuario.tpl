<div dojoType="dijit.form.Form" encType="multipart/form-data" action="index.php" method="POST" name="form_usuario" id="form_usuario" style="margin: 0px;">

<input type="hidden" name="sitio_id" value="{__accion_sitio_id}">
<input type="hidden" name="menu_id" value="{__accion_menu_id}">
<input type="hidden" name="usuario_cliente_id" id="usuario_cliente_id" value="{__usuario_cliente_id}">
<input type="hidden" name="accion" value="guardar_usuario">
<input type="hidden" name="ejecutar_accion" value="1">

<div style="width: 450px; height: 340px" align="left">

<div dojoType="dijit.layout.TabContainer" style="width: 100%; height: 100%;">
	<div dojoType="dijit.layout.ContentPane" title="Usuario" selected="true">

<table width="100%" class="formulario">
	<tr>
		<th width="120">Nombre</th>
		<td><input type="text" name="usuario_cliente_nombre" id="usuario_cliente_nombre" value="{__usuario_cliente_nombre}" {__form_disabled}/></td>
	</tr>
	<tr>
		<th>E-mail</th>
		<td><input type="text" name="usuario_cliente_email" id="usuario_cliente_email" value="{__usuario_cliente_email}" {__form_disabled}/></td>
	</tr>
	<tr>
		<th>Tel&eacute;fono</th>
		<td><input type="text" name="usuario_cliente_telefono" value="{__usuario_cliente_telefono}" {__form_disabled}/></td>
	</tr>
	<tr>
		<th>Cargo</th>
		<td><input type="text" name="usuario_cliente_cargo" value="{__usuario_cliente_cargo}" {__form_disabled}/></td>
	</tr>
	<tr>
		<th>Perfil</th>
		<td>
			<select name="perfil_id" id="perfil_id" {__form_disabled}>
				<!-- BEGIN LISTA_PERFILES -->
				<option value='{__perfil_id}' {__perfil_sel}>{__perfil_nombre}</option>
				<!-- END LISTA_PERFILES -->
			</select>
		</td>
	</tr>
	<tr>
		<th>Zona Horaria</th>
		<td>
			<select name="zona_horaria_id" {__form_disabled}>
				<!-- BEGIN LISTA_ZONAS_HORARIAS -->
				<option value='{__zona_horaria_id}' {__zona_horaria_sel}>{__zona_horaria_nombre}</option>
				<!-- END LISTA_ZONAS_HORARIAS -->
			</select>
		</td>
	</tr>
<!--  	<tr>
		<th>Idioma</th>
		<td>
			<select name="idioma_id"> -->
				<!-- BEGIN LISTA_IDIOMAS -->
<!-- 				<option value='{__idioma_id}' {__idioma_sel}>{__idioma_nombre}</option> -->
				<!-- END LISTA_IDIOMAS -->
<!-- 			</select>
		</td>
	</tr>-->
</table>
<br>
<div class="textgris9" style="text-align: center;">Ingrese los siguientes campos solo si quiere cambiar la clave actual.</div>
<table width="100%" class="formulario">
	<tr>
		<th width="120">Clave</th>
		<td><input type="password" name="usuario_cliente_clave1" id="usuario_cliente_clave1" value="" {__form_disabled}/></td>
	</tr>
	<tr>
		<th>Repetir Clave</th>
		<td><input type="password" name="usuario_cliente_clave2" id="usuario_cliente_clave2" value="" {__form_disabled}/></td>
	</tr>
</table>

</div>
<div dojoType="dijit.layout.ContentPane" title="Grupos">

<table width="100%" class="listado">
	<tr>
		<th>Nombre</th>
		<th>&nbsp;</th>
	</tr>
	<!-- BEGIN LISTA_SUBCLIENTES -->
	<tr>
		<td>{__subcliente_nombre}</td>
		<td width="50" align="center">
			<input type="checkbox" name="usuario_subcliente_{__subcliente_id}" {__subcliente_sel} {__form_disabled} />
		</td>
	</tr>
	<!-- END LISTA_SUBCLIENTES -->
</table>

</div>
</div>
</div>
<br>
<!-- BEGIN PUEDE_MODIFICAR -->
	<table align="center">
		<tr>
			<td style="text-align:right">
				<input type="button" onclick="validarFormUsuario()" class="boton_accion" value="{__imagen}"/>
			</td>
			<td style="width:20px">&nbsp;</td>
			<td style="text-align:left">
				<input type="button" value="Cancelar" class="boton_cancelar" onclick="dijit.byId('dialog_usuario').hide();" />
			</td>
		</tr>
	</table>
	<br>
<!-- END PUEDE_MODIFICAR -->
</div>