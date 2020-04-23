<input type="hidden" name="sel_filtro" value="1" />
<input type="hidden" name="item_es_incluido" value="0" />

<table width="100%" class="listado">
	<tr>
		<th width="200">Inicio</th>
		<th width="200">Termino</th>
		<th>Descripcion</th>
		<th width="30">&nbsp;</th>
	</tr>
	<!-- BEGIN LISTA_ITEMS -->
	<tr>
		<td {__item_class}>{__item_inicio}</td>
		<td {__item_class}>{__item_termino}</td>
		<td {__item_class}>{__item_descripcion}</td>
		<td {__item_class} align="center">
		<!-- BEGIN PUEDE_ELIMINAR -->
			<a href="#" onclick="quitarPonderacionItem('{__item_hora_inicio}');">
			<i class="spriteButton spriteButton-borrar" border="0"></i></a>
		<!-- END PUEDE_ELIMINAR -->
		</td>
	</tr>
	<!-- END LISTA_ITEMS -->
	<!-- BEGIN PUEDE_AGREGAR -->
	<tr>
		<th>
			<input style="width: 120px; color: #525252;" dojoType="dijit.form.DateTextBox" type="text" id="item_fecha_inicio" name="item_fecha_inicio" value="{__item_fecha_inicio}"/>&nbsp;
			<input style="width: 60px;" type="text" name="item_hora_inicio" />
		</th>
		<th>
			<input style="width: 120px; color: #525252;" dojoType="dijit.form.DateTextBox" type="text" id="item_fecha_termino" name="item_fecha_termino" value="{__item_fecha_termino}" />&nbsp;
			<input style="width: 60px;" type="text" name="item_hora_termino" />
		</th>
		<th><input style="width: 100%;" type="text" name="item_descripcion" /></th>
		<th align="center"><a href="#" onclick="abrirAccion(1, 'guardar_item'); return false;"><i class="spriteButton spriteButton-nuevo"></i></a></th>
	</tr>
	<!-- END PUEDE_AGREGAR -->
</table>