<script>

function validarEliminar(destinatario_id, destinatario_nombre) {
	if(confirm("¿Esta seguro que desea eliminar el contacto '"+destinatario_nombre+"'?")) {
		abrirAccion(1, 'eliminar_destinatario', ['destinatario_id', destinatario_id]);
	}
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
	
	if(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w+)+$/.test(trim(dojo.byId("destinatario_contacto").value))  == false){
		alert("Debe ingresar una casilla de destino.");
		return false;
	}
	
	dojo.byId("formDojo").submit();
}

</script>

<div dojoType="dijit.Dialog" id="dialog_destinatario" title="Informacion de Contacto"></div>

<input type="hidden" name="destinatario_id" value="0">
<table width="100%">
	<tr>
		<td class="tituloseccion">Lista de Contactos</td>
	</tr>
	<tr>
		<td>
			<br>
			<div class="descripcion">
		    &#8226; Los contactos solo pueden ser eliminados cuando no est&aacute;n asociados a alarmas.<br>
			</div>
			<br>
			<!-- BEGIN PUEDE_AGREGAR -->
			<input type="button" value="Agregar Contacto" class="boton_cancelar" onclick="abrirFormulario('destinatario',0,'modificar_destinatario',['destinatario_id','0']);">
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
					<th>Casilla de Destino</th>
					<th>Tipo</th>
					<th>Teléfono</th>
					<th width="30">&nbsp;</th>
					<th width="30">&nbsp;</th>
				</tr>
				<!-- BEGIN LISTA_DESTINATARIOS -->
				<tr>
					<td>{__destinatario_nombre}</td>
					<td>{__destinatario_contacto}</td>
					<td>{__destinatario_tipo}</td>
					<td>{__destinatario_telefono}</td>
					<td align="center">
						<a style="cursor: pointer" onclick="abrirFormulario('destinatario',0,'modificar_destinatario',['destinatario_id','{__destinatario_id}']);">
            <i class="{__form_icon_detail}"border="0" title="{__form_label_detail}"></i></a>

					</td>
					<td align="center">
						<!-- BEGIN PUEDE_ELIMINAR -->
						<a href="#" onclick="validarEliminar('{__destinatario_id}','{__destinatario_nombre}');">
						<i class="spriteButton spriteButton-borrar" border="0" title="Eliminar Alerta"></i></a>
						<!-- END PUEDE_ELIMINAR -->
					</td>
				</tr>
				<!-- END LISTA_DESTINATARIOS -->
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<br>
			<!-- BEGIN PUEDE_AGREGAR -->
			<input type="button" value="Agregar Contacto" class="boton_cancelar" onclick="abrirFormulario('destinatario',0,'modificar_destinatario',['destinatario_id','0']);">
			<br>
			<br>
			<!-- END PUEDE_AGREGAR -->
		</td>
	</tr>
</table>