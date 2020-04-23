<script type="text/javascript">

function validarForm() {
	if (trim(dojo.byId("horario_nombre").value) == "") {
		alert("Debe ingresar un nombre.");
		return false;
	}

	if (existeNombreHorario(dojo.byId("horario_nombre").value, dojo.byId("horario_id").value) == "1") {
		alert("Ya existe el nombre en el sistema.");
		return false;
	}

	alert("Se han guardado los cambios.");
	abrirAccion(1, 'guardar_horario');
}

function validarAgregarItem() {
	if (!regexp_date.test($('#item_fecha_inicio').val()) || !regexp_time.test($('#item_hora_inicio').val())) {
		alert("Fecha de inicio incorrecta.");
		return false;
	}
	if (!regexp_date.test($('#item_fecha_termino').val()) || !regexp_time.test($('#item_hora_termino').val())) {
		alert("Fecha de termino incorrecta.");
		return false;
	}
	
	var arr_date1 = $('#item_fecha_inicio').val().split("/");
	var arr_date2 = $('#item_fecha_termino').val().split("/");
	var arr_time1 = $('#item_hora_inicio').val().split(":");
	var arr_time2 = $('#item_hora_termino').val().split(":");
	
	var date_inicio = new Date(arr_date1[2], arr_date1[1], arr_date1[0], arr_time1[1], arr_time1[0]);
	var date_termino = new Date(arr_date2[2], arr_date2[1], arr_date2[0], arr_time2[1], arr_time2[0]);
	
	if (date_inicio >= date_termino) {
		alert("Fecha de inicio debe ser menor a la fecha de termino.");
		return false;		
	}
	
	abrirAccion(1, 'guardar_item');
}

</script>

<input type="hidden" name="horario_id" id="horario_id" value="{__horario_id}">
<input type="hidden" name="sel_filtro" value="1" />
<input type="hidden" name="item_es_incluido" value="0" />
<input type="hidden" name="item_id" id="item_id" value="0" />

<table width="100%">
	<tr>
		<td class="tituloseccion">Informaci&oacute;n Mantenci&oacute;n</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
			<table width="50%" class="formulario">
				<tr>
					<th>Nombre</th>
					<td><input type="text" name="horario_nombre" id="horario_nombre" value="{__horario_nombre}" {__form_disabled}></td>
				</tr>
				<tr>
					<th>Descripci&oacute;n</th>
					<td><input type="text" name="horario_descripcion" value="{__horario_descripcion}" {__form_disabled}></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="15"></td>
	</tr>
	<tr>
		<td>
			<!-- BEGIN PUEDE_MODIFICAR -->
			<table align="center">
				<tr>
					<td style="text-align:right">
						<input type="button" class="boton_accion" onclick="validarForm()"  id="guardar_horario" value="Guardar">
					</td>
					<td style="width:20px">&nbsp;</td>
					<td style="text-align:left">
						<input type="button" class="boton_cancelar" value="Cancelar" onclick="mostrarSubmenu({__padre_id},{__seccion_id},{__nivel})" />
					</td>
				</tr>
			</table>
			<!-- END PUEDE_MODIFICAR -->
		</td>
	</tr>
	<tr>
		<td height="15"></td>
	</tr>
	<!-- BEGIN TIENE_ITEMS -->
	<tr>
		<td>
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
						<a href="#" onclick="$('#item_id').val('{__item_id}'); abrirAccion(1, 'eliminar_item');">
						<i class="spriteButton spriteButton-borrar" border="0"></i></a>
					<!-- END PUEDE_ELIMINAR -->
					</td>
				</tr>
				<!-- END LISTA_ITEMS -->
				<!-- BEGIN PUEDE_AGREGAR -->
				<tr>
					<th>
						<input style="width: 120px; color: #525252;" constraints="{ datePattern:'dd/MM/yyyy' }" dojoType="dijit.form.DateTextBox" type="text" id="item_fecha_inicio" name="item_fecha_inicio" />&nbsp;
						<input style="width: 60px;" type="text" name="item_hora_inicio" id="item_hora_inicio" value="hh:mm" onclick="this.value='';" />
					</th>
					<th>
						<input style="width: 120px; color: #525252;" constraints="{ datePattern:'dd/MM/yyyy' }" dojoType="dijit.form.DateTextBox" type="text" id="item_fecha_termino" name="item_fecha_termino" />&nbsp;
						<input style="width: 60px;" type="text" name="item_hora_termino" id="item_hora_termino" value="hh:mm" onclick="this.value='';"/>
					</th>
					<th><input style="width: 100%;" type="text" name="item_descripcion" id="item_descripcion" /></th>
					<th align="center"><a href="#" onclick="validarAgregarItem();"><i class="spriteButton spriteButton-nuevo" border="0"></i></a></th>
				</tr>
				<!-- END PUEDE_AGREGAR -->
			</table>
		</td>
	</tr>
	<!-- END TIENE_ITEMS -->
</table>