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

	for (var dia=1; dia<8; dia++) {
		var hora_inicio = null;
		var hora_termino = null;

		for (var hora=0; hora<24; hora++) {
			if (document.getElementById("hora_"+dia+"_"+hora).className == "celdanaranja50") {
				if (hora_inicio == null) {
					hora_inicio = hora;
				}
				if (hora == 23) {
					var input = document.createElement("input");
					input.setAttribute("type", "hidden");
					input.setAttribute("name", "dia_semana_"+dia+"_"+hora_inicio+"_24");
					input.setAttribute("value", "1");
					document.form_principal.appendChild(input);
					
					hora_inicio = null;
				}
			}
			else {
				if (hora_inicio != null) {
					var input = document.createElement("input");
					input.setAttribute("type", "hidden");
					input.setAttribute("name", "dia_semana_"+dia+"_"+hora_inicio+"_"+hora);
					input.setAttribute("value", "1");
					document.form_principal.appendChild(input);

					hora_inicio = null;
				}
			}
		}
	}

	if (document.form_principal.notificacion_id.value != "") {
		document.form_principal.menu_id.value = 39;
	}
	abrirAccion(1, 'guardar_horario');
}

function cambiarFiltro() {
	selector = document.getElementById("sel_filtro");
	if (selector.options[selector.selectedIndex].value == "1") {
		document.getElementById("div_entre_fechas").style.display = "inline";
		document.getElementById("div_fecha_especifica").style.display = "none";
		document.getElementById("div_dia_semana").style.display = "none";
	}
	else if (selector.options[selector.selectedIndex].value == "2") {
		document.getElementById("div_entre_fechas").style.display = "none";
		document.getElementById("div_fecha_especifica").style.display = "inline";
		document.getElementById("div_dia_semana").style.display = "none";
	}
	else if (selector.options[selector.selectedIndex].value == "3") {
		document.getElementById("div_entre_fechas").style.display = "none";
		document.getElementById("div_fecha_especifica").style.display = "none";
		document.getElementById("div_dia_semana").style.display = "inline";
	}
}

function seleccionarHora(dia, hora, forzar, disabled) {
	if (disabled != 'disabled') {
		if ((document.getElementById("hora_"+dia+"_"+hora).className == "celdanegra10" && forzar == "0") || forzar == "1") {
			var total_dia = "celdanaranja100";
			var total_hora = "celdanaranja100";

			document.getElementById("hora_"+dia+"_"+hora).className = "celdanaranja50";
			
			for (var hora_aux=0; hora_aux<24; hora_aux++) {
				if (document.getElementById("hora_"+dia+"_"+hora_aux).className == "celdanegra10") {
					total_dia = "celdanegra20";
				}
			}
			document.getElementById("total_dia_"+dia).className = total_dia;

			for (var dia_aux=1; dia_aux<8; dia_aux++) {
				if (document.getElementById("hora_"+dia_aux+"_"+hora).className == "celdanegra10") {
					total_hora = "celdanegra20";
				}
			}
			document.getElementById("total_hora_"+hora).className = total_hora;
		}
		else if ((document.getElementById("hora_"+dia+"_"+hora).className == "celdanaranja50" && forzar == "0") || forzar == "2") {
			document.getElementById("hora_"+dia+"_"+hora).className = "celdanegra10";
			document.getElementById("total_dia_"+dia).className = "celdanegra20";
			document.getElementById("total_hora_"+hora).className = "celdanegra20";
		}
	}
}

function seleccionarTotalDia(dia, disabled) {
	if (disabled != 'disabled') {
		if (document.getElementById("total_dia_"+dia).className == "celdanaranja100") {
			for (var hora=0; hora<24; hora++) {
				seleccionarHora(dia, hora, 2, disabled);
			}
		}
		else {
			for (var hora=0; hora<24; hora++) {
				seleccionarHora(dia, hora, 1, disabled);
			}
		}
	}
}

function seleccionarTotalHora(hora, disabled) {
	if (disabled != 'disabled') {
		if (document.getElementById("total_hora_"+hora).className == "celdanaranja100") {
			for (var dia=1; dia<8; dia++) {
				seleccionarHora(dia, hora, 2, disabled);
			}
		}
		else {
			for (var dia=1; dia<8; dia++) {
				seleccionarHora(dia, hora, 1, disabled);
			}
		}
	}
}

</script>

<div dojoType="dijit.Dialog" id="dialog_item" title="Informacion de Item"></div>

<!-- BEGIN TIENE_HORARIOS_ASIGNABLES -->
<div dojoType="dijit.Dialog" id="dialog_linkear" title="Ingresar Items Desde Otro Horario">
	<div dojoType="dijit.form.Form" encType="multipart/form-data" action="index.php" method="POST" name="form_item2" id="form_item2">
	<input type="hidden" name="sitio_id" value="{__accion_sitio_id}">
	<input type="hidden" name="menu_id" value="{__accion_menu_id}">
	<input type="hidden" name="horario_id" value="{__horario_id}">
	<input type="hidden" name="item_id" id="item_id" value="0">
	<input type="hidden" name="accion" value="linkear_horario">
	<input type="hidden" name="ejecutar_accion" value="1">
		<table width="400" class="formulario">
			<tr>
				<th>Horario</th>
				<td>
					<select name="asignable_id">
						<!-- BEGIN LISTA_HORARIOS_ASIGNABLES -->
						<option value="{__asignable_id}">{__asignable_nombre}</option>
						<!-- END LISTA_HORARIOS_ASIGNABLES -->
					</select>
				</td>
			</tr>
		</table>
		<br>		
			<table  align="center">
				<tr>
					<td style="vertical-align:middle">
						<button  type="submit" class="boton_accion">Guardar</button>						
					</td>
					<td style="vertical-align:middle">
						<input type="button" class="boton_cancelar" value="Cancelar" onclick="mostrarSubmenu({__padre_id},{__seccion_id},{__nivel})" />
					</td>
				</tr>
			</table>				
	</div>
</div>
<!-- END TIENE_HORARIOS_ASIGNABLES -->

<!--
  -- Inicio del formulario para horario
  -->
<input type="hidden" name="horario_id" id="horario_id" value="{__horario_id}">
<input type="hidden" name="item_id" value="0">

<input type="hidden" name="notificacion_id" value="{__notificacion_id}">
<input type="hidden" name="notificacion_destinatario_id" value="{__notificacion_destinatario_id}">

<table width="100%">
	<tr>
		<td class="tituloseccion">Informaci&oacute;n del Horario</td>
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
				<tr>
					<th>Tipo</th>
					<td>{__horario_tipo_nombre}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="40"></td>
	</tr>
	<tr>
		<td class="tituloitem">Configuraci&oacute;n de Horario</td>
	</tr>
	<tr>
		<td>
			<br>
			<div class="descripcion">
				&#8226; Si desea asociar o desasociar horas solo debe hacer click sobre el recuadro correspondiente.<br>
				&#8226; El icono (&#9668;) indica que puede asociar o desasociar toda la fila o columna.
			</div>
			<br>
			<table align="center">
				<tr>
					<td class="celdanegra40" width="80"></td>
					<!-- BEGIN LISTA_HORAS_TITULO -->
					<td class="celdanegra40" width="20"><div style="float: left; position: relative; margin-left: -8px;">{__hora_nombre}</div></td>
					<!-- END LISTA_HORAS_TITULO -->
					<td class="celdanegra40" width="20"><div style="float: left; position: relative; margin-left: -8px;">24</div></td>
				</tr>
				<!-- BEGIN LISTA_DIAS_SEMANA -->
				<tr>
					<td class="celdanegra50">{__dia_nombre}</td>
					<!-- BEGIN LISTA_HORAS_DIA -->
					<td class="{__hora_estilo}" style="border-right: solid 1px #f9f9f9;" id="hora_{__dia_id}_{__hora_id}" onclick="seleccionarHora('{__dia_id}', '{__hora_id}', 0, '{__form_disabled}');"></td>
					<!-- END LISTA_HORAS_DIA -->
					<td align="center" class="{__dia_total_estilo}" id="total_dia_{__dia_id}" onclick="seleccionarTotalDia('{__dia_id}', '{__form_disabled}');">&#9668;</td>
				</tr>
				<tr>
					<td colspan="100%" style="height: 1px; background-color: #828282; border-right: solid 1px #f9f9f9;"></td>
				</tr>
				<!-- END LISTA_DIAS_SEMANA -->
				<tr>
					<td></td>
					<!-- BEGIN LISTA_HORAS_TOTAL -->
					<td align="center" class="{__hora_total_estilo}" id="total_hora_{__hora_id}" style="border-right: solid 1px #f9f9f9;" onclick="seleccionarTotalHora('{__hora_id}', '{__form_disabled}');" width="20">&#9650;</td>
					<!-- END LISTA_HORAS_TOTAL -->
					<td></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
			<br>
			<!-- BEGIN PUEDE_VOLVER -->
			<input type="button" class="boton_cancelar" value="Cancelar" onclick="abrirAccion(0,'modificar_notificacion',['menu_id','39']);">&nbsp;&nbsp;&nbsp;
			<!-- END PUEDE_VOLVER -->
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

	<!-- BEGIN TIENE_CONFIGURACION_AVANZADA -->
	<tr>
		<td height="40"></td>
	</tr>
	<tr>
		<td class="tituloitem">Configuraci&oacute;n Avanzada de Horario</td>
	</tr>
	<tr>
		<td>
			<br>
			<div class="descripcion">
				&#8226; Un horario esta compuesto de distintos Items, un Item es un periodo de tiempo con un inicio y un t&eacute;rmino, si un evento ocurre dentro de este per&iacute;odo sera considerado.<br> 
				&#8226; Un Item puede crearse seleccionando 'Agregar Item' o pueden usarse items desde otro horario seleccionando 'Linkear Items'.
			</div>
			<br>
			<!-- BEGIN PUEDE_AGREGAR_ITEM -->
			<input type="button" class="boton_cancelar" value="Agregar Item" onclick="abrirFormulario('item',0,'modificar_item',['horario_id','{__horario_id}','item_id','0']);">
			&nbsp;&nbsp;
			<input type="button" class="boton_cancelar" value="Linkear Items" onclick="dijit.byId('dialog_linkear').show();">
			<br>
			<br>
			<!-- END PUEDE_AGREGAR_ITEM -->
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" class="listado">
				<tr>
					<th>Tipo</th>
					<th>Detalle</th>
					<th width="30">&nbsp;</th>
					<th width="30">&nbsp;</th>
				</tr>
				<!-- BEGIN LISTA_ITEMS -->
				<tr>
					<td>{__item_incluido}</td>
					<td>{__item_tipo}</td>
					<td align="center">
						<!-- BEGIN PUEDE_MODIFICAR_ITEM -->
						<a href="#" onclick="abrirFormulario('item',0,'modificar_item',['horario_id','{__horario_id}','item_id','{__item_id}']); return false;">
						<i class="spriteButton spriteButton-editar" border="0" title="Modificar Item"></i></a>
						<!-- END PUEDE_MODIFICAR_ITEM -->
					</td>
					<td align="center">
						<!-- BEGIN PUEDE_ELIMINAR_ITEM -->
						<a href="#" onclick="abrirAccion(1,'eliminar_item',['horario_id','{__horario_id}','item_id','{__item_id}']); return false;">
						<i class="spriteButton spriteButton-borrar" border="0" title="Eliminar Item"></i></a>
						<!-- END PUEDE_ELIMINAR_ITEM -->
					</td>
				</tr>
				<!-- END LISTA_ITEMS -->
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<br>
			<!-- BEGIN PUEDE_AGREGAR_ITEM -->
			<input type="button" class="boton_cancelar" value="Agregar Item" onclick="abrirFormulario('item',0,'modificar_item',['horario_id','{__horario_id}','item_id','0']);">
			&nbsp;&nbsp;
			<input type="button" class="boton_cancelar" value="Linkear Item" onclick="dijit.byId('dialog_linkear').show();">
			<br>
			<br>
			<!-- END PUEDE_AGREGAR_ITEM -->
		</td>
	</tr>
	<!-- END TIENE_CONFIGURACION_AVANZADA -->
</table>