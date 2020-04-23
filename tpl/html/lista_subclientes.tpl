<script>
function validarEliminar(subcliente_id, subcliente_nombre) {
	if(confirm("¿Esta seguro que desea eliminar el subcliente '"+subcliente_nombre+"'?")) {
		abrirAccion(1,'eliminar_subcliente',['subcliente_id', subcliente_id]);
	}
}

function validarFormSubcliente() {
	if (trim(dojo.byId("subcliente_nombre").value) == "") {
		alert("Debe ingresar un nombre.");
		return false;
	}
	if (existeNombreSubcliente(dojo.byId("subcliente_nombre").value, dojo.byId("subcliente_id").value) == "1") {
		alert("Ya existe el nombre en el sistema.");
		return false;
	}
	dojo.byId("form_subcliente").submit();
}
</script>

<div dojoType="dijit.Dialog" id="dialog_subcliente" title="Informacion del Grupo"></div>

<!--
  -- Inicio de la lista de subclientes
  -->
<input type="hidden" name="subcliente_id" value="0">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tituloseccion"><span class="textblancoup14">Lista de Grupos</span></td>
	</tr>
	<tr>
		<td>
			<br>
			<div class="descripcion">
				&#8226; Un usuario ver&aacute; solo los objetivos de los grupos a los que pertenece.
			</div>
			<br>
			<!-- BEGIN PUEDE_AGREGAR -->
			<input type="button"  class="boton_cancelar" value="Agregar Grupo" onclick="abrirFormulario('subcliente',0,'modificar_subcliente',['subcliente_id','0']);">
			<br>
			<br>
			<!-- END PUEDE_AGREGAR -->
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="listado">
				<tr>
					<th width="200">Nombre</th>
					<th>Descripcion</th>
					<th width="30">&nbsp;</th>
					<th width="30">&nbsp;</th>
					<th width="38">&nbsp;</th>
					<th width="38" >&nbsp;</th>
				</tr>
				<!-- BEGIN LISTA_SUBCLIENTES -->
				<tr>
					<td>{__subcliente_nombre}&nbsp;</td>
					<td>{__subcliente_descripcion}&nbsp;</td>
					<td align="center">
						<a href="#" onclick="abrirFormulario('subcliente',0,'modificar_subcliente',['subcliente_id','{__subcliente_id}']); return false;">
                                                    <i class="{__form_icon_detail} border="0" title="{__form_label_detail}"></i></a>
					</td>
					<td align="center">
						<!-- BEGIN PUEDE_ELIMINAR -->
						<a href="#" onclick="validarEliminar('{__subcliente_id}','{__subcliente_nombre}'); return false;">
						<i class="spriteButton spriteButton-borrar" border="0" title="Eliminar Grupo"></i></a>
						<!-- END PUEDE_ELIMINAR -->
					</td>
					<td align="left">
						<a href="#" onclick="abrirFormulario('subcliente',0,'asociar_subcliente_usuarios',['subcliente_id','{__subcliente_id}']); return false;">
						<i class="spriteButton spriteButton-usuarios" style="padding-left: 4px;" border="0" title="Lista Usuarios Asociados"></i></a>
						<span class="textrojo8">{__subcliente_usuarios}</span>
					</td>
					<td align="left"  >
						<a href="#" onclick="abrirFormulario('subcliente',0,'asociar_subcliente_objetivos',['subcliente_id','{__subcliente_id}']); return false;">
						<i class="spriteButton spriteButton-objetivos" style="padding-left: 4px;" border="0" title="Lista Objetivos Asociados"></i></a>
						<span class="textrojo8">{__subcliente_objetivos}</span>
					</td>
				</tr>
				<!-- END LISTA_SUBCLIENTES -->
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<br>
			<!-- BEGIN PUEDE_AGREGAR -->
			<input type="button" class="boton_cancelar" value="Agregar Grupo" onclick="abrirFormulario('subcliente',0,'modificar_subcliente',['subcliente_id','0']);">
			<br>
			<br>
			<!-- END PUEDE_AGREGAR -->
		</td>
	</tr>
</table>