<!-- <input type="hidden" name="periodo_tipo" value="{__periodo_tipo}"> -->

<script type="text/javascript" language="javascript" src="{__path_anychart}"></script>

<script>
	var grupo_semaforo_anterior = 0;
	var grupo_vista_anterior = 0;
	var grupo_disponibilidad_anterior = 100000;
	var grupo_ponderado_anterior = 0;
	var grupo_estadistica_dia_anterior = 0;
	var grupo_estadistica_resumen = 0;
	var grupo_estadistica_detalle = 0;
	var nodo_registro_plus = 0;
	var monitor_elementos_monitoreos_anterior = 0;
	var monitor_elementos_estadisticas_anterior = 0;

	var periodo_default = 0;
	var item_seleccionado = 0;
	var obj_calendario;
	var obj_periodico;
	function mostrarCalendario(item_id) {
		if (periodo_default == 0) {
		if (item_seleccionado!=0) {
			document.getElementById("calendario_"+item_seleccionado).innerHTML = '';
			document.getElementById("calendario_periodico_"+item_seleccionado).innerHTML = '';
			document.getElementById("horario_"+item_seleccionado).innerHTML = '';
			document.getElementById("generar_reporte_"+item_seleccionado).style.display = "none";
			document.getElementById("boton_calendario_"+item_seleccionado).className = "spriteButton spriteButton-abrir_calendario";
		}
		if (item_seleccionado==item_id) {
			item_seleccionado = 0;
			return false;
		}
  
		item_seleccionado = item_id;

		document.getElementById("boton_calendario_"+item_seleccionado).className = "spriteButton spriteButton-cerrar_calendario";
                }

		<!-- BEGIN TIENE_CALENDARIO -->
		obj_calendario = new Calendario('calendario_'+item_seleccionado,'{__fecha_inicio}','{__fecha_termino}','obj_calendario');
		<!-- END TIENE_CALENDARIO -->

		<!-- BEGIN TIENE_PERIODO -->
		obj_periodico = new CalendarioPeriodico('calendario_periodico_'+item_seleccionado,'{__fecha_inicio}','{__fecha_termino}','obj_periodico');
		<!-- END TIENE_PERIODO -->
		<!-- BEGIN TIENE_HORARIO -->
		var str_horario =
'<table style="border-spacing: 2px; border-collapse: separate;">'+
	'<tr>'+
		'<td class="celdanegra40" colspan="2" align="center">Horarios Habiles</td>'+
	'</tr>'+
	'<tr>'+
		'<td class="calendario periodos" align="center" width="30"><input type="radio" name="horario_id" value="0" checked></td>'+
		'<td class="calendario periodos">Todo Horario</td>'+
	'</tr>'+
	<!-- BEGIN LISTA_HORARIOS -->
	'<tr>'+
		'<td class="calendario periodos" align="center" width="30"><input type="radio" name="horario_id" value="{__horario_id}" {__horario_sel}></td>'+
		'<td class="calendario periodos">{__horario_nombre}&nbsp;&nbsp;&nbsp;</td>'+
	'</tr>'+
	<!-- END LISTA_HORARIOS -->
'</table>';
		document.getElementById("horario_"+item_seleccionado).innerHTML = str_horario;
		<!-- END TIENE_HORARIO -->

		if (periodo_default == 0) {
		document.getElementById("generar_reporte_"+item_seleccionado).style.display = "inline";
		}
	}

	function validarCalendario() {
		var fecha_inicio = document.getElementById("fecha_inicio_periodico").value;
		var fecha_termino = document.getElementById("fecha_termino_periodico").value;
		if (document.form_principal.horario_id) {
			if (document.form_principal.horario_id.value == null) {
				for (i=0; i<document.form_principal.horario_id.length; i++){
					if (document.form_principal.horario_id[i].checked) {
						var horario_id = document.form_principal.horario_id[i].value;
					}
				}
			}
			else {
				var horario_id = document.form_principal.horario_id.value;
			}
		}
		else {
			var horario_id = 0;
		}

		if (fecha_inicio == 0) {
			alert("Debe seleccionar un periodo.");
			return false;
		}
		if (fecha_termino == 0) {
			alert("Debe seleccionar un periodo.");
			return false;
		}
		if (document.getElementById("vista_reporte_0").checked) {
			document.form_principal.submit();
		}
		else {
			abrirPopup(['fecha_inicio_periodo', fecha_inicio, 'fecha_termino_periodo', fecha_termino, 'horario_id', horario_id]);
/*			var sitio_id = document.form_principal.sitio_id.value;
			var menu_id = document.form_principal.menu_id.value;
			var objeto_id = document.form_principal.objeto_id.value;

			var opciones = "toolbar=no, "+
						   "location=no, "+
						   "directories=no, "+
						   "status=no, "+
						   "menubar=yes, "+
						   "scrollbars=yes, "+
						   "resizable=yes, "+
						   "width=790, "+
						   "height=580 ";

			window.open("index.php?sitio_id="+sitio_id+"&menu_id="+menu_id+
						"&objeto_id="+objeto_id+"&subobjeto_id=0"+
						"&fecha_inicio_periodo="+fecha_inicio+"&fecha_termino_periodo="+fecha_termino+
						"&horario_id="+horario_id+"&popup=1&reporte_id=0",
						"", opciones);*/
		}
	}
</script>

<!--INICIO PRINCIPAL_CONTENIDO_REPORTE-->
<table width="100%">
<label id ='host' value={'__hostname'}></label>

{__hostname}
	<tr>
		<td>

			<!--INICIO PRINCIPAL_CONTENIDO_REPORTE_ENCABEZADO-->
			<table width="100%">
				<tr>
					<td class="tituloseccion">

					
						<!-- BEGIN TIENE_OBJETIVO -->
						<div style="float: left; position: relative; margin-top: -4px; margin-bottom: -4px;">
							<table>
								<tr>
									<td>
										<div class="textblanco11">{__objetivo_nombre}</div>
										<div class="textblanco9">{__objetivo_servicio} {__objetivo_intervalo}</div>
									</td>
									<td width="30" align="center">
										<i class="spriteImg spriteImg-barra"></i>
									</td>
								</tr>
							</table>
						</div>
						<!-- END TIENE_OBJETIVO -->
						<!-- BEGIN BLOQUE_SIN_HORARIO -->
						{__reporte_titulo}
						<!-- END BLOQUE_SIN_HORARIO -->
						<!-- BEGIN BLOQUE_HORARIO -->
						<div style="display:inline;float:left;position:relative">
						{__reporte_titulo}
						</div>
							<div style="display:inline; margin-top: -4px; margin-bottom: -4px;vertical-align:top">
								<table>
									<tr>
										<td width="30" align="center">
											<i class="spriteImg spriteImg-barra"></i>
										</td>
										<td class="textblanco11">
											Periodo: {__item_duracion}
										</td>
									</tr>
								</table>
							</div>
						<!-- END BLOQUE_HORARIO -->
					</td>
					<!-- BEGIN TIENE_IMPRESION -->
					<td class="tituloseccion" width="5%" align="center" id="imprimirInforme">
						<a href="#" onclick="abrirPopup(['reporte_id', '{__reporte_id}']); return false;">
					
						<i class="spriteButton spriteButton-exportar" border="0" title="Imprimir Informe Actual"></i></a>
                                        </td>
					<!-- END TIENE_IMPRESION -->
					<!-- BEGIN TIENE_IMPRESION_FULL -->
					<td class="tituloseccion" width="5%" align="center" id="imprimirTodos">
						<a href="#" onclick="abrirPopup(['reporte_id', '0']); return false;">
                                                <i class="spriteButton spriteButton-exportar_varios" border="0" title="Imprimir Todos los Informes"></i>
					</td>
					<!-- END TIENE_IMPRESION_FULL -->
				</tr>
 				<tr>
					<td colspan="100%">
						<table>
							<tr>
								<td>
								<!-- BEGIN REPORTE_SECCIONES -->
									<div class="menusuperior" style="float: left; position: relative;">
									<a href="#" onclick="abrirEnlace('{__sitio_id}','{__reporte_seccion_id}','{__objeto_id}');" class="{__reporte_seccion_class}">
									{__reporte_seccion_nombre}</a>
									  
									
									  
									   
									</div>
								<!-- END REPORTE_SECCIONES -->
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br>
			<!--FIN PRINCIPAL_CONTENIDO_REPORTE_ENCABEZADO-->
		</td>
	</tr>
	<tr>
		<td>
			<!-- BEGIN REPORTE_DESCRIPCION -->
			<div class="descripcion">{__reporte_descripcion}</div>
			<br>
			<!-- END REPORTE_DESCRIPCION -->
			<!-- BEGIN ITEMS_REPORTE -->
			{__reporte_item}
			<!-- END ITEMS_REPORTE -->
			<!-- BEGIN TIENE_PERIODO_DEFAULT -->
			<table style="border-spacing: 5px; border-collapse: separate;" align="center">
				<tr>
					<td valign="top" id="calendario_periodico_0"></td>
					<td valign="top" id="calendario_0"></td>
					<td valign="top" id="horario_0"></td>
				</tr>
				<tr>
					<td align="center" colspan="3" class="textgris12">
						<input type="radio" name="vista_reporte" id="vista_reporte_0" checked> Vista normal &nbsp;&nbsp;&nbsp;
						<input type="radio" name="vista_reporte" id="vista_reporte_1"> Vista impresa / exportacion PDF
					</td>
				</tr>
				<tr>
					<td height="10" colspan="3"></td>
				</tr>
				<tr>
					<td align="center" colspan="3">
						<input type="button" value="Generar Reporte" class="boton_accion" onclick="validarCalendario();"/>
					</td>
				</tr>
			</table>
			<br>
			<script>
				periodo_default = 1;
				mostrarCalendario(0);
			</script>
			<!-- END TIENE_PERIODO_DEFAULT -->
			<br>
		</td>
	</tr>
</table>
<!--FIN PRINCIPAL_CONTENIDO_REPORTE-->

