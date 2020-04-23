<script>
function validarFormulario() {
        var fecha_inicio = document.getElementById("fecha_inicio_periodico").value;
        var fecha_termino = document.getElementById("fecha_termino_periodico").value;

        if (fecha_inicio == 0) {
                alert("Debe seleccionar un periodo.");
                return false;
        }
        if (fecha_termino == 0) {
                alert("Debe seleccionar un periodo.");
                return false;
        }

    	if (document.form_principal.horario_id) {
    		if (document.form_principal.horario_id.value == "") {
    			alert("Debe seleccionar un horario habil.");
    			return false;
    		}
    	}

    	if (document.form_principal.objetivo_especial_id) {
    		if (document.form_principal.objetivo_especial_id.value == false) {
    			alert("Debe seleccionar un objetivo.");
    			return false;
    		}
    	}

        if (document.form_principal.tipo_content.value == "") {
                alert("Debe seleccionar una vista.");
                return false;
        }

        if (document.form_principal.tipo_content.value == "html") {
			window.open('', 'formpopup', 'width=800, height=600, menubar, resizeable, scrollbars');
			document.form_principal.action = 'index.php?tiene_flash='+tiene_flash+"&tiene_svg="+tiene_svg;
			document.form_principal.target = 'formpopup';
			document.form_principal.submit();
			document.form_principal.action = 'index.php';
			document.form_principal.target = '';
        }
        else {
			document.form_principal.submit();
        }

}

var tipo_content_anterior = 0;
function checkTipo(tipo_content) {

        if (document.getElementById("tipo_"+tipo_content_anterior)) {
                document.getElementById("tipo_"+tipo_content_anterior).className = "checkboxunselected";
        }
        if (document.getElementById("tipo_"+tipo_content)) {
                document.getElementById("tipo_"+tipo_content).className = "checkboxselected";
                document.form_principal.tipo_content.value = tipo_content;
                tipo_content_anterior = tipo_content;
        }
}

var subobjetivo_anterior = 0;
function checkSubobjetivo(subobjetivo_id) {

	if (document.getElementById("subobjetivo_"+subobjetivo_anterior)) {
		document.getElementById("subobjetivo_"+subobjetivo_anterior).className = "radiounselected";
		document.getElementById("elem_pasos_"+subobjetivo_anterior).style.display = "none";
	}
	if (document.getElementById("subobjetivo_"+subobjetivo_id)) {
		document.getElementById("subobjetivo_"+subobjetivo_id).className = "radioselected";
		document.getElementById("elem_pasos_"+subobjetivo_id).style.display = "inline";
		document.form_principal.subobjetivo_id.value = subobjetivo_id;
		seleccionarPaso(subobjetivo_id, $("#subobjetivo_"+subobjetivo_id).data("paso-default"));
		subobjetivo_anterior = subobjetivo_id;
	}
}

var horario_anterior = 0;
function checkHorario(horario_id) {

	if (document.getElementById("horario_"+horario_anterior)) 
	{
		document.getElementById("horario_"+horario_anterior).className = "radiounselected";
		document.getElementById("_horario_"+horario_anterior).className = "nada";
	}
	if (document.getElementById("horario_"+horario_id)) 
	{
		document.getElementById("horario_"+horario_id).className = "radioselected";
		document.getElementById("_horario_"+horario_id).className = "spriteImg spriteImg-bot_check";
		document.form_principal.horario_id.value = horario_id;
		horario_anterior = horario_id;
	}
}

var paso_anterior = 0;
function seleccionarPaso(subobjetivo_id, paso_id) {
	if (document.getElementById("elem_paso_"+subobjetivo_anterior+"_"+paso_anterior)) 
	{
		document.getElementById("elem_paso_"+subobjetivo_anterior+"_"+paso_anterior).className = "subcheckboxunselected";
	}
	if (document.getElementById("elem_paso_"+subobjetivo_id+"_"+paso_id)) 
	{
		document.getElementById("elem_paso_"+subobjetivo_id+"_"+paso_id).className = "subcheckboxselected";
	}
	limpiarMantenciones();

	var input = document.getElementById("man_paso_"+subobjetivo_id+"_"+paso_id).value;
	var mantenciones_ids = input.split("-");

	for (var i=0; i < mantenciones_ids.length; i++) {
		if (document.getElementById("elem_mantencion_"+mantenciones_ids[i])) {
			document.getElementById("elem_mantencion_"+mantenciones_ids[i]).className = "subcheckboxselected";
		}
	}

	paso_anterior = paso_id;
}

function seleccionarMantencion(mantencion_id) {
//	jQuery(function($) {

		if (document.getElementById("elem_mantencion_"+mantencion_id).className == "subcheckboxunselected") {
			document.getElementById("elem_mantencion_"+mantencion_id).className = "subcheckboxselected";
		}
		else {
			document.getElementById("elem_mantencion_"+mantencion_id).className = "subcheckboxunselected";
		}
	
		var exp = new RegExp("elem_mantencion_");
		var elementos = $(".subcheckboxselected");
		var man_select = new Array();
		var man_select_names = new Array("Sin mantenciones");
		var man_pos = 0;
		
		for (var i=0; i < elementos.length; i++) {
			if (exp.test(elementos[i].id)) {
				elem_mantencion = elementos[i].id.split("_");
				man_select[man_pos] = elem_mantencion[2];
				man_select_names[man_pos] = $(elementos[i]).data("name");
				man_pos++;
			}
		}
		document.getElementById("elem_paso_man_"+subobjetivo_anterior+"_"+paso_anterior).innerHTML = man_select_names.join(" / ");
		document.getElementById("man_paso_"+subobjetivo_anterior+"_"+paso_anterior).value = man_select.join("-");
//	});
}

function limpiarMantenciones() {
//	jQuery(function($) {
		var exp = new RegExp("elem_mantencion_");
		var elementos = $(".subcheckboxselected");
		
		for (var i=0; i < elementos.length; i++) {
			if (exp.test(elementos[i].id)) {
				elementos[i].className = "subcheckboxunselected";
			}
		}
//	});
}

</script>

<input type="hidden" name="popup" value="1" />
<input type="hidden" name="calendario_v2" value="1" />
<input type="hidden" name="cache" value="1" />

<!-- BEGIN BLOQUE_TIPO_DEFAULT -->
<input type="hidden" name="tipo_content" value="{__tipo_content}" />
<!-- END BLOQUE_TIPO_DEFAULT -->


<table width="100%">
        <tr>
                <td class="tituloseccion">{__reporte_titulo}</td>
        </tr>
</table>
<br>

<table align="center" width="80%">
        <tr>
                <td>
                        <div id="calendario_especial">
                                <img class="indicador-carga" src="/img/cargando.gif" title="cargando calendario" alt="cargando calendario" />
                        </div>
                        <script type="text/javascript">
                          jQuery(function($) {
                            // Inicializa calendario

                            var $calendarioEspecial = $("#calendario_especial");

                            // Establece parámetros
                            var params = {};

                            var fechaCalendario = "{__fecha_inicio}";
                            if(fechaCalendario.length > 0) {
                              params["fechaCalendario"] =  fechaCalendario + "T00:00:00";
                            }

                            var fechaMinima = "{__reporte_period_start}";
                            if(fechaMinima.length > 0) {
                              params["fechaMinima"] = fechaMinima + "T00:00:00";
                            }

                            params["seleccion"] = {};
                            params["seleccion"]["activa"] = ("{__calendario_permite_seleccionar}" === "true");
                            params["seleccion"]["intervalo"] = ("{__calendario_selecciona_intervalo}" === "true");


                            $calendarioEspecial.calendariou(params);

                            var calendariou = $calendarioEspecial.data("calendariou");


                            // Establece inputs
                            var $inputFechaInicio = $('<input type="hidden" name="fecha_inicio_periodico" id="fecha_inicio_periodico" value="{__fecha_inicio_periodo}">');
                            var $inputFechaTermino = $('<input type="hidden" name="fecha_termino_periodico" id="fecha_termino_periodico" value="{__fecha_termino_periodo}">');

                            $calendarioEspecial.append($inputFechaInicio, $inputFechaTermino);

                            // Escucha cambios en selección para propagarlos a inputs correspondientes
                            calendariou.seleccion.el().on("calendariou:seleccion:cambio", function() {
                              var fechaInicio = calendariou.seleccion.get("fechaInicio");
                              var fechaTermino = calendariou.seleccion.get("fechaTermino");
                              $inputFechaInicio.prop("value", fechaInicio === null ? null : fechaInicio.format("yyyy-mm-ddThh:mm:ss"));
                              $inputFechaTermino.prop("value", fechaTermino === null ? null : fechaTermino.format("yyyy-mm-ddThh:mm:ss"));
                            });
                          });
                        </script>

                        <!-- BEGIN BLOQUE_INFORMES_DISPONIBLES -->
                        <div id="informes_disponibles" class="informes-disponibles-en-formulario">
                        </div>
                        <script type="text/javascript">
                          jQuery(function($) {

                            var calendariou = $("#calendario_especial").data("calendariou");

                            // === Informes disponibles =====================================

                            var $informesDisponibles = $("#informes_disponibles");

                            var $inputReporteInformeSubtipoId = $('<input type="hidden" name="reporte_informe_subtipo_id" id="reporte_informe_subtipo_id" value="" />');
                            $informesDisponibles.append($inputReporteInformeSubtipoId);


                            // Inicializa objeto
                            var informesDisponibles = new Reporte2.ListaDeInformesDisponibles($informesDisponibles);

                            // Carga inicial de informes disponibles
                            informesDisponibles.cargar(document.form_principal.objeto_id.value, calendariou.selector.fecha.format("yyyy-mm-dd").slice(0,4), calendariou.selector.fecha.format("yyyy-mm-dd").slice(5,7));

                            // Cada vez que cambia el mes, actualiza informes disponibles
                            calendariou.mesCalendario.el().on("calendariou:mesCalendario:cambiaMes", function(event, data) {
                              informesDisponibles.cargar(document.form_principal.objeto_id.value, data["ano"], data["mes"]);
                            });

                            // Escucha evento de selección de informe
                            informesDisponibles.el().on("listaDeInformesDisponibles:seleccionaInforme", function(event, data) {
                              var fechaInicio = data["fechaInicio"];
                              var fechaTermino = data["fechaTermino"];
                              var reporteInformeSubtipoId = data["reporteInformeSubtipoId"];

                              // Actualiza valor de input de reporte_informe_subtipo_id
                              $inputReporteInformeSubtipoId.prop("value", reporteInformeSubtipoId);

                              // Actualiza selección en calendario
                              calendariou.actualizarSeleccion({
                                                               "fechaInicio":  fechaInicio,
                                                               "fechaTermino": fechaTermino
                              });
                            });

                          });
                        </script>
                        <!-- END BLOQUE_INFORMES_DISPONIBLES -->
                </td>
        </tr>
        <tr>
                <td height="15"></td>
        </tr>

	<!-- BEGIN BLOQUE_SUBOBJETIVOS -->
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td style="padding: 5px; background-color: #a2a2a2; color: #ffffff; font-size: 15; font-family: Trebuchet MS, Verdana, sans-serif; font-weight: bold; text-transform: uppercase;">Objetivos</td>
				</tr>
				<tr>
					<td align="center">
						<input name="subobjetivo_id" type="hidden" />
						<table width="100%">

							<!-- BEGIN LISTA_SUBOBJETIVOS_TR -->
							<tr>
								<!-- BEGIN LISTA_SUBOBJETIVOS_TD -->
								<td height="30" id="subobjetivo_{__subobjetivo_id}" data-paso-default="{__subobjetivo_paso_default}" onclick="checkSubobjetivo('{__subobjetivo_id}');" class="radiounselected" style="font-size: 13px; font-family: Trebuchet MS, Verdana, sans-serif; border: solid 1px #a2a2a2; padding: 4px 4px 4px 45px;">
									<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 290px;">{__subobjetivo_nombre}</div>
								</td>
								<!-- END LISTA_SUBOBJETIVOS_TD -->
							</tr>
							<!-- END LISTA_SUBOBJETIVOS_TR -->
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td width="50%" align="center" valign="top">
						<!-- BEGIN BLOQUE_PASOS -->
						<div id="elem_pasos_{__subobjetivo_id}" style="display: none;">
							<table width="90%">
								<tr>
									<td style="border: solid 1px #a2a2a2; padding: 5px; background-color: #f3f3f3; color: #626262; font-size: 15; font-family: Trebuchet MS, Verdana, sans-serif; font-weight: bold; text-transform: uppercase;">Pasos</td>
								</tr>
								<tr>
									<td height="5"></td>
								</tr>
								<!-- BEGIN LISTA_PASOS -->
								<tr>
									<td onclick="seleccionarPaso({__subobjetivo_id},{__paso_id});" id="elem_paso_{__subobjetivo_id}_{__paso_id}" style="border: solid 1px #a2a2a2; padding: 5px; font-size: 13; font-family: Trebuchet MS, Verdana, sans-serif;" class="subcheckboxunselected">
										{__paso_nombre}
										<input type="hidden" id="man_paso_{__subobjetivo_id}_{__paso_id}" name="man_paso_{__subobjetivo_id}_{__paso_id}" value="{__paso_man_ids}" />
									</td>
								</tr>
								<tr>
									<td id="elem_paso_man_{__subobjetivo_id}_{__paso_id}" class="txtNaranjo8">{__paso_man_nombres}</td>
								</tr>
								<tr>
									<td height="5"></td>
								</tr>
								<!-- END LISTA_PASOS -->
							</table>
						</div>
						<!-- END BLOQUE_PASOS -->
					</td>
					<td width="50%" align="center" valign="top">
						<table width="90%">
							<tr>
								<td style="border: solid 1px #a2a2a2; padding: 5px; background-color: #f3f3f3; color: #626262; font-size: 15; font-family: Trebuchet MS, Verdana, sans-serif; font-weight: bold; text-transform: uppercase;">Mantenciones</td>
							</tr>
							<tr>
								<td height="5"></td>
							</tr>
							<!-- BEGIN LISTA_MANTENCIONES -->
							<tr>
								<td onclick="seleccionarMantencion({__mantencion_id});" id="elem_mantencion_{__mantencion_id}" data-name="{__mantencion_nombre}" style="border: solid 1px #a2a2a2; padding: 5px; font-size: 13; font-family: Trebuchet MS, Verdana, sans-serif;" class="subcheckboxunselected">{__mantencion_nombre}</td>
							</tr>
							<tr>
								<td height="5"></td>
							</tr>
							<!-- END LISTA_MANTENCIONES -->
						</table>
					</td>
				</tr>
			</table>
			<script>
				checkSubobjetivo('{__subobjetivo_default}');
			</script>
		</td>
	</tr>
	
	<tr>
		<td height="15"></td>
	</tr>
	<!-- END BLOQUE_SUBOBJETIVOS -->
	
	<!-- BEGIN BLOQUE_HORARIOS -->
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td style="padding: 5px; background-color: #a2a2a2; color: #ffffff; font-size: 15; font-family: Trebuchet MS, Verdana, sans-serif; font-weight: bold; text-transform: uppercase;">Horarios Habiles</td>
				</tr>
				<tr>
					<td align="center">
						<input name="horario_id" type="hidden" />
						<table width="100%">
							<!-- BEGIN LISTA_HORARIOS_TR -->
							<tr>
								<!-- BEGIN LISTA_HORARIOS_TD -->
								<td height="30" id="horario_{__horario_id}" onclick="checkHorario('{__horario_id}');" class="radiounselected" style="font-size: 13px; padding: 0px 0px 0px 20px; cursor: pointer; font-family: Trebuchet MS, Verdana, sans-serif; border: solid 1px #a2a2a2;">
									<i id="_horario_{__horario_id}" onclick="checkHorario('{__horario_id}');" class="nada" style="position: absolute;"></i>
									<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 290px; float: left; padding: 0px 0px 0px 50px;">{__horario_nombre}</div>
								</td>
								<!-- END LISTA_HORARIOS_TD -->
							</tr>
							<!-- END LISTA_HORARIOS_TR -->
						</table>
					</td>
				</tr>
			</table>
			<script>
				checkHorario('{__horario_id_default}');
			</script>
		</td>
	</tr>
	<tr>
		<td height="15"></td>
	</tr>
	<!-- END BLOQUE_HORARIOS -->

        <!-- BEGIN BLOQUE_TIPOS -->
        <tr>
                <td>
                        <table width="100%">
                                <tr>
                                        <td style="padding: 5px; background-color: #a2a2a2; color: #ffffff; font-size: 15; font-family: Trebuchet MS, Verdana, sans-serif; font-weight: bold; text-transform: uppercase;">Vistas</td>
                                </tr>
                                <tr>
                                        <td align="center">
                                                <input name="tipo_content" type="hidden" />
                                                <table style="border-spacing: 10px; border-collapse: separate;">
                                                        <tr>
                                                                <!-- BEGIN LISTA_TIPOS -->
                                                                <td height="30" id="tipo_{__tipo_content}" onclick="checkTipo('{__tipo_content}');" width="150" class="checkboxunselected" style="font-size: 12px; font-family: Trebuchet MS, Verdana, sans-serif; border: solid 1px #a2a2a2; padding: 4px 4px 4px 28px;">{__tipo_nombre}</td>
                                                                <!-- END LISTA_TIPOS -->
                                                        </tr>
                                                </table>
                                        </td>
                                </tr>
                        </table>
                        <script>
                                checkTipo('{__tipo_content_default}');
                        </script>
                </td>
        </tr>
        <tr>
                <td height="15"></td>
        </tr>
        <!-- END BLOQUE_TIPOS -->

        <tr>
                <td align="center">
                        <input type="button" value="Generar Reporte" class="boton_accion" onclick="validarFormulario();"/>
                </td>
        </tr>
</table>
<br>
