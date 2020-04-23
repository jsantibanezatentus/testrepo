<script>

function validarEliminar(token_id, token_nombre) {
	if(confirm("¿Esta seguro que desea eliminar el token '"+token_nombre+"'?")) {
		abrirAccion(1, 'eliminar_token', ['token_id', token_id]);
	}
}

function validarFormTokenWS() {
	if (trim(dojo.byId("token_nombre").value) == "") {
		alert("Debe ingresar un nombre.");
		return false;
	}
/*	if (existeNombreDestinatario(dojo.byId("destinatario_nombre").value, dojo.byId("destinatario_id").value) == "1") {
		alert("Ya existe el nombre en el sistema.");
		return false;
	}*/
	dojo.byId("form_token_ws").submit();
}

</script>

<div dojoType="dijit.Dialog" id="dialog_usuario" title="Configuraci&oacute;n del Token"></div>

<!--
  -- Inicio de la lista de usuarios.
  -->
<input type="hidden" name="token_id" value="0">
<table width="100%">
	<tr>
		<td class="tituloseccion">Lista de Tokens</td>
	</tr>
	<tr>
		<td>
			<br>
			<!-- BEGIN PUEDE_AGREGAR -->
			<input type="button" class="boton_cancelar" value="Agregar Token" onclick="abrirFormulario('usuario',0,'modificar_token',['token_id','0']);">
			<br>
			<br>
			<!-- END PUEDE_AGREGAR -->
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" class="listado">
				<tr>
					<th>Nombre</th>
					<th>Key</th>
<!-- 					<th>Fecha Expiracion</th> -->
					<th width="30">&nbsp;</th>
					<th width="30">&nbsp;</th>
				</tr>
				<!-- BEGIN LISTA_TOKENS -->
				<tr>
					<td>{__token_nombre}</td>
					<td>{__token_key}</td>
<!-- 					<td>{__token_expiracion}</td> -->
					<td align="center">
						<a href="#" onclick="abrirFormulario('usuario',0,'modificar_token',['token_id','{__token_id}']); return false;">
                                                <i class="{__form_icon_detail} border="0" title="{__form_label_detail}"></i></a>
					</td>
					<td align="center">
						<!-- BEGIN PUEDE_ELIMINAR -->
						<a href="#" onclick="validarEliminar('{__token_id}','{__token_nombre}'); return false;">
						<i class="spriteButton spriteButton-borrar" border="0" title="Eliminar Token"></i></a>
						<!-- END PUEDE_ELIMINAR -->
					</td>
				</tr>
				<!-- END LISTA_TOKENS -->
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<br>
			<!-- BEGIN PUEDE_AGREGAR -->
			<input type="button"  class="boton_cancelar" value="Agregar Token" onclick="abrirFormulario('usuario',0,'modificar_token',['token_id','0']);">
			<br>
			<br>
			<!-- END PUEDE_AGREGAR -->
		</td>
	</tr>
</table>