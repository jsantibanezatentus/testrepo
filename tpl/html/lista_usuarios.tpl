<script>
function validarEliminar(usuario_cliente_id, usuario_cliente_nombre) {
	if(confirm("¿Esta seguro que desea eliminar el usuario '"+usuario_cliente_nombre+"'?")) {
		abrirAccion(1, 'eliminar_usuario', ['usuario_cliente_id', usuario_cliente_id]);
	}
}

function validarFormUsuario() {
	if (trim(dojo.byId("usuario_cliente_nombre").value) == "") {
		alert("Debe ingresar un nombre.");
		return false;
	}
	if (existeNombreUsuario(dojo.byId("usuario_cliente_nombre").value, dojo.byId("usuario_cliente_id").value) == "1") {
		alert("Ya existe el nombre en el sistema.");
		return false;
	}
	if (trim(dojo.byId("usuario_cliente_email").value) == "") {
		alert("Debe ingresar un e-mail.");
		return false;
	}
	if (/\@.*\@|[\s|\!|\"|\#|\$|\%|\&|\/|\(|\)|\=|\?|\¿|\;|\:]/.test(dojo.byId("usuario_cliente_email").value) || 
		/^.+\@.+$/.test(dojo.byId("usuario_cliente_email").value) == false) {
		alert("Debe ingresar un e-mail valido.");
		return false;
	}
	if (existeEmailUsuario(dojo.byId("usuario_cliente_email").value, dojo.byId("usuario_cliente_id").value) == "1") {
		alert("Ya existe el e-mail en el sistema.");
		return false;
	}
	if (dojo.byId("usuario_cliente_id").value == 0 && 
		trim(dojo.byId("usuario_cliente_clave1").value) == "") {
		alert("Debe ingresar una clave.");
		return false;
	}
	if (trim(dojo.byId("usuario_cliente_clave1").value) != trim(dojo.byId("usuario_cliente_clave2").value)) {
		alert("Las claves deben ser iguales.");
		return false;
	}

	var inputs = document.getElementsByTagName("input");
	var tiene_subcliente = false;
	for (var i=0; i<inputs.length; i++) {
		if (inputs[i].type == "checkbox" && inputs[i].checked == true) {
			tiene_subcliente = true;
		}
	}

	if (tiene_subcliente == false) {
/*		alert("Debe ingresar por lo menos un subcliente por usuario.");
		return false;*/
		if(!confirm("¿Esta seguro que desea guardar un usuario sin grupos?")) {
			return false;
		}
	}

	dojo.byId("form_usuario").submit();
}
</script>

<div dojoType="dijit.Dialog" id="dialog_usuario" title="Configuraci&oacute;n del Usuario"></div>

<!--
  -- Inicio de la lista de usuarios.
  -->
<input type="hidden" name="usuario_cliente_id" value="0">
<table width="100%">
	<tr>
		<td class="tituloseccion">Lista de Usuarios</td>
	</tr>
	<tr>
		<td>
<!-- 			<br>
			<div class="descripcion">
				Tipos de Usuarios<br> -->
				<!-- BEGIN LISTA_PERFILES -->
<!-- 				&nbsp;&nbsp;&nbsp;&#8226; {__perfil_nombre} : {__perfil_descripcion}<br> --> 
				<!-- END LISTA_PERFILES -->
<!-- 			</div> -->
			<br>
			<!-- BEGIN PUEDE_AGREGAR -->
			<input type="button"  class="boton_cancelar" value="Agregar Usuario" onclick="abrirFormulario('usuario',0,'modificar_usuario',['usuario_cliente_id','0']);">
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
					<th>E-Mail</th>
					<th>Perfil</th>
					<th width="30">&nbsp;</th>
					<th width="30">&nbsp;</th>
				</tr>
				<!-- BEGIN LISTA_USUARIOS -->
				<tr>
					<td>{__usuario_cliente_nombre}&nbsp;</td>
					<td>{__usuario_cliente_email}&nbsp;</td>
					<td>{__usuario_cliente_perfil}&nbsp;</td>
					<td align="center">
						<a href="#" onclick="abrirFormulario('usuario',0,'modificar_usuario',['usuario_cliente_id','{__usuario_cliente_id}']); return false;">
                                                    <i class="{__form_icon_detail} border="0" title="{__form_label_detail}"></i></a>
					</td>
					<td align="center">
						<!-- BEGIN PUEDE_ELIMINAR -->
						<a href="#" onclick="validarEliminar('{__usuario_cliente_id}','{__usuario_cliente_nombre}'); return false;">
						<i class="spriteButton spriteButton-borrar" border="0" title="Eliminar Usuario"></i></a>
						<!-- END PUEDE_ELIMINAR -->
					</td>
				</tr>
				<!-- END LISTA_USUARIOS -->
				<!-- BEGIN MOSTRAR_USUARIOS_DISPONIBLES -->
				<tr>
					<th colspan="100%">Usuarios disponibles: {__usuarios_disponible}</th>
				</tr>
				<!-- END MOSTRAR_USUARIOS_DISPONIBLES -->
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<br>
			<!-- BEGIN PUEDE_AGREGAR -->
			<input type="button" class="boton_cancelar" value="Agregar Usuario" onclick="abrirFormulario('usuario',0,'modificar_usuario',['usuario_cliente_id','0']);">
			<br>
			<br>
			<!-- END PUEDE_AGREGAR -->
		</td>
	</tr>
</table>