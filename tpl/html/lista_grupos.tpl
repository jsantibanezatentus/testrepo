<div dojoType="dijit.Dialog" id="dialog_grupo" title="Informacion del Subcliente" widgetsInTemplate="true"></div>

<!--
  -- Inicio de la lista de grupos
  -->
<input type="hidden" name="grupo_id" value="0">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tituloseccion"><span class="textblancoup14">Lista de Grupos</span></td>
	</tr>
	<tr>
		<td>
			<br>
			<input type="button" class="boton_cancelar" value="Agregar Grupo" onclick="abrirFormulario('grupo',0,'modificar_grupo',['grupo_id','0']);">
			<br>
			<br>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="listado">
				<tr>
					<th>Nombre</th>
					<th>Descripcion</th>
					<th width="50">&nbsp;</th>
					<th width="50">&nbsp;</th>
				</tr>
				<!-- BEGIN LISTA_GRUPOS -->
				<tr>
					<td>{__grupo_nombre}</td>
					<td>{__grupo_descripcion}</td>
					<td align="center">
						<a href="#" onclick="abrirAccion(1,'eliminar_grupo',['grupo_id','{__grupo_id}']);">
						<i class="spriteButton spriteButton-borrar" border="0"></i></a>
					</td>
					<td align="center">
						<a href="#" onclick="abrirFormulario('grupo',0,'modificar_grupo',['grupo_id','{__grupo_id}']);">
						<i class="spriteButton spriteButton-editar" border="0"></i></a>
					</td>
				</tr>
				<!-- END LISTA_GRUPOS -->
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<br>
			<input type="button"  class="boton_cancelar" value="Agregar Grupo" onclick="abrirFormulario('grupo',0,'modificar_grupo',['grupo_id','0']);">
			<br>
			<br>
		</td>
	</tr>
</table>