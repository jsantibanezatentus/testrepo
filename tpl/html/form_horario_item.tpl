<div dojoType="dijit.form.Form" encType="multipart/form-data" action="index.php" method="POST" name="form_item" id="form_item">

<!-- Este script se ejecuta automaticamente al hacer un submit -->
<script type="dojo/method" event="onSubmit">
	if (dijit.byId("item_fecha_termino").getValue()!=null && dijit.byId("item_fecha_inicio").getValue()==null) {
		alert("No existe fecha inicio.")
		return false;
	}
	if (dijit.byId("item_hora_termino").getValue()!=null && dijit.byId("item_hora_inicio").getValue()==null) {
		alert("No existe hora inicio.")
		return false;
	}
	if (dijit.byId("item_hora_termino").getValue()==null && dijit.byId("item_hora_inicio").getValue()!=null) {
		alert("No existe hora termino.")
		return false;
	}
	if (dijit.byId("item_hora_termino_dia").getValue()!=null && dijit.byId("item_hora_inicio_dia").getValue()==null) {
		alert("No existe hora inicio.")
		return false;
	}
	if (dijit.byId("item_hora_termino_dia").getValue()==null && dijit.byId("item_hora_inicio_dia").getValue()!=null) {
		alert("No existe hora termino.")
		return false;
	}
	if (dojo.date.compare(dijit.byId("item_fecha_inicio").getValue(), dijit.byId("item_fecha_termino").getValue())==1) {
		alert("La fecha de termino debe ser mayor que la fecha de inicio.")
		return false;
	}
	if (dojo.date.compare(dijit.byId("item_hora_inicio").getValue(), dijit.byId("item_hora_termino").getValue())==1) {
		alert("La hora de termino debe ser mayor que la hora de inicio.")
		return false;
	}
</script>
		
<!-- Inputs usados solo en el dijit.Dialog -->
<input type="hidden" name="sitio_id" value="{__accion_sitio_id}">
<input type="hidden" name="menu_id" value="{__accion_menu_id}">
<input type="hidden" name="horario_id" value="{__horario_id}">
<input type="hidden" name="item_id" id="item_id" value="{__item_id}">
<input type="hidden" name="accion" value="guardar_item">
<input type="hidden" name="ejecutar_accion" value="1">

<table width="450" border="0" cellpadding="0" cellspacing="0" class="formulario">
	<tr>
		<th>Tipo</th>
		<td>
			<select name="item_es_incluido">
				<!-- BEGIN TIPOS_ITEM -->
				<option value="{__item_tipo_id}" {__item_tipo_sel}>{__item_tipo_nombre}</option>
				<!-- END TIPOS_ITEM -->
			</select>
		</td>
	</tr>
	<tr>
		<th>Filtrar por</th>
		<td>
			<select onchange="cambiarFiltro();" name="sel_filtro" id="sel_filtro">
				<!-- BEGIN FILTROS_ITEM -->
				<option value="{__item_filtro_id}" {__item_filtro_sel}>{__item_filtro_nombre}</option>
				<!-- END FILTROS_ITEM -->
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="100%">
			<div id="div_entre_fechas" style="display: {__item_filtro_display_1};">
				<div class="descripcion" style="margin-top: 6px; margin-bottom: 8px;">
					El item ingresado tendr&aacute; un fecha de inicio y una de termino, que puede o no incluir la hora. (Si deja el campo de hora vaci&oacute; se considerada todo el d&iacute;a)<br><br>
					Por ejemplo:<br>
					&#8226; entre el 25-09-2011 y el 28-09-2011.<br>
					&#8226; entre el 25-09-2011 09:00:00 y el 25-09-2011 18:30:00.<br>
					&#8226; entre el 25-09-2011 09:00:00 y el 28-09-2011 18:30:00.
				</div>
				<table width="100%">
					<tr>
						<td>
							Fecha Inicio:<br>
							<input dojoType="dijit.form.DateTextBox" type="text" id="item_fecha_inicio" name="item_fecha_inicio" value="{__item_fecha_inicio}"/>&nbsp;
							<input dojoType="dijit.form.TimeTextBox" type="text" id="item_hora_inicio" name="item_hora_inicio" value="T{__item_hora_inicio}"/>
						</td>
					</tr>
					<tr>
						<td>
							Fecha Termino:<br>
							<input dojoType="dijit.form.DateTextBox" type="text" id="item_fecha_termino" name="item_fecha_termino" value="{__item_fecha_termino}"/>&nbsp;
							<input dojoType="dijit.form.TimeTextBox" type="text" id="item_hora_termino" name="item_hora_termino" value="T{__item_hora_termino}"/>
						</td>
					</tr>
				</table>
			</div>
			<div id="div_fecha_especifica" style="display: {__item_filtro_display_2};">
				<div class="descripcion" style="margin-top: 6px; margin-bottom: 8px;">
					El item ingresado corresponder&aacute; a una fecha con la combinaci&oacute;n de d&iacute;a-mes-año.<br><br>
					Por ejemplo:<br>
					&#8226; todos los d&iacute;as, de Septiembre, del 2011.<br>
					&#8226; el 25, de todos los meses, del 2011.<br>
					&#8226; el 25, de Septiembre, de todos los años.<br>
					&#8226; el 25, de Septiembre, del 2011.
				</div>
				<table width="100%">
					<tr>
						<td>
							D&iacute;a:<br>
							<select name="item_dia">
								<option value="0">Todos</option>
								<!-- BEGIN DIAS_ITEM -->
								<option value="{__item_dia}" {__item_dia_sel}>{__item_dia}</option>
								<!-- END DIAS_ITEM -->
							</select>
						</td>
						<td>
							Mes:<br>
							<select name="item_mes">
								<option value="0">Todos</option>
								<!-- BEGIN MESES_ITEM -->
								<option value="{__item_mes_id}" {__item_mes_sel}>{__item_mes_nombre}</option>
								<!-- END MESES_ITEM -->
							</select>
						</td>
						<td>
							Año:<br>
							<select name="item_anno">
								<option value="0">Todos</option>
								<!-- BEGIN ANNOS_ITEM -->
								<option value="{__item_anno}" {__item_anno_sel}>{__item_anno}</option>
								<!-- END ANNOS_ITEM -->
							</select>
						</td>
					</tr>
				</table>
			</div>
			<div id="div_dia_semana" style="display: {__item_filtro_display_3};">
				<div class="descripcion" style="margin-top: 6px; margin-bottom: 8px;">
					El item ingresado corresponder&aacute; a un d&iacute;a de la semana. (Si deja el campo de hora vacio se considerada todo el d&iacute;a)<br><br>
					Por ejemplo:<br>
					&#8226; todos los d&iacute;as Lunes.<br>
					&#8226; todos los d&iacute;as Lunes entre las 09:00:00 y las 18:30:00.
				</div>
				<table width="100%">
					<tr>
						<td>
							D&iacute;a:<br>
							<select name="item_dia_semana">
								<!-- BEGIN DIAS_SEMANA_ITEM -->
								<option value="{__item_dia_semana_id}" {__item_dia_semana_sel}>{__item_dia_semana_nombre}</option>
								<!-- END DIAS_SEMANA_ITEM -->
							</select>
						</td>
						<td>
							Hora Inicio:<br>
							<input dojoType="dijit.form.TimeTextBox" type="text" id="item_hora_inicio_dia" name="item_hora_inicio_dia" value="T{__item_hora_inicio}"/>
						</td>
						<td>
							Hora T&eacute;rmino:<br>
							<input dojoType="dijit.form.TimeTextBox" type="text" id="item_hora_termino_dia" name="item_hora_termino_dia" value="T{__item_hora_termino}"/>
						</td>
					</tr>
				</table>
			</div>
		</td>
</table>
<br>
<!-- BEGIN PUEDE_MODIFICAR -->
<button  type="submit" class="boton_accion">Guardar</button>
<!-- END PUEDE_MODIFICAR -->
</div>