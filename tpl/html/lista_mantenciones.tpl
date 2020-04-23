<script>

function validarEliminar(horario_id, horario_nombre) {
	if(confirm("¿Esta seguro que desea eliminar el horario '"+horario_nombre+"'?")) {
		abrirAccion(1,'eliminar_horario',['horario_id',horario_id]);
	}
}

</script>

<input type="hidden" name="horario_id" value="0">
<input type="hidden" name="horario_tipo_id" value="7">
<table width="100%">
	<tr>
		<td class="tituloseccion">{__sitio_titulo}</td>
	</tr>
	<tr>
		<td>
			<br>
			<div class="descripcion">
				&#8226; Los horarios s&oacute;lo pueden ser eliminados cuando no estan asociados a reportes o a alarmas.<br> 
			</div>
			<br>
			<!-- BEGIN PUEDE_AGREGAR -->
			<input type="button"  class="boton_cancelar" value="Agregar Horario" onclick="abrirAccion(0,'modificar_horario');">
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
					<th>Descripcion</th>
					<th width="30">&nbsp;</th>
					<th width="30">&nbsp;</th>
				</tr>
				<!-- BEGIN LISTA_HORARIOS -->
				<tr>
					<td>{__horario_nombre}</td>
					<td>{__horario_descripcion}&nbsp;</td>
					<td align="center">
						<a href="#" onclick="abrirAccion(0,'modificar_horario',['horario_id','{__horario_id}']);">
                                                    <i class="{__form_icon_detail}  border="0" title="{__form_label_detail}"></i></a>
					</td>
					<td align="center">
						<!-- BEGIN PUEDE_ELIMINAR -->
						<a href="#" onclick="validarEliminar('{__horario_id}','{__horario_nombre}'); return false;">
						<i class="spriteButton spriteButton-borrar" border="0" title="Eliminar Horario"></i></a>
						<!-- END PUEDE_ELIMINAR -->
					</td>
				</tr>
				<!-- END LISTA_HORARIOS -->
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<br>
			<!-- BEGIN PUEDE_AGREGAR -->
			<input type="button"  class="boton_cancelar" value="Agregar Horario" onclick="abrirAccion(0,'modificar_horario');">
			<br>
			<br>
			<!-- END PUEDE_AGREGAR -->
		</td>
	</tr>
</table>