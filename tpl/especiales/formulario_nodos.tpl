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

	if ($('#nodos_seleccionados').val() == '') {
		alert("Debe seleccionar al menos un nodo.");
		return false;
	}

	window.open('', 'formpopup', 'width=800, height=600, menubar, resizeable, scrollbars');
	document.form_principal.target = 'formpopup';
	document.form_principal.submit();
	document.form_principal.action = 'index.php';
	document.form_principal.target = '';
}

var tipo_content_anterior = 0;
function checkTipo(tipo_content) {

	if (document.getElementById("tipo_"+tipo_content_anterior)) {
		document.getElementById("tipo_"+tipo_content_anterior).className = "checkboxunselected";
	}
	if (document.getElementById("tipo_"+tipo_content)) {
		document.getElementById("tipo_"+tipo_content).className = "checkboxselected";
		document.getElementById("tipo_id").value = tipo_content;
		tipo_content_anterior = tipo_content;
	}
}

function checknodo(nodo_id) {
	if (document.getElementById("nodo_"+nodo_id).className == "radiounselected") {
		document.getElementById("nodo_"+nodo_id).className = "radioselected";
	}
	else{
		document.getElementById("nodo_"+nodo_id).className = "radiounselected";
	}
}

function compruebanodos(){
	nodos= '';
	contador = 0;
	$('input[name=nodos_opcionales]::checked').each(function(){			
		if(contador == 0){
			nodos = $(this).val();
		}
		else{
			nodos = nodos+','+$(this).val();	
		}
		contador++;
	})
	$('#nodos_seleccionados').val(nodos);
}

</script>

<input type="hidden" name="popup" value="1" />



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


	<!-- BEGIN LISTA_NODOS -->
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td style="padding: 5px; background-color: #a2a2a2; color: #ffffff; font-size: 15; font-family: Trebuchet MS, Verdana, sans-serif; font-weight: bold; text-transform: uppercase;">nodos</td>
				</tr>
				<tr>
					<td align="center">
						<input id="nodos_seleccionados" type="hidden" name="nodos_seleccionados"/>
						<table width="100%">
							<!-- BEGIN LISTA_NODOS_TR -->
							<tr>
								<!-- BEGIN LISTA_NODOS_TD -->
								<td height="30" width="50%" style="font-size: 13px; font-family: Trebuchet MS, Verdana, sans-serif; border: solid 1px #a2a2a2; padding: 4px 4px 4px 45px;">
									<input  type="checkbox" checked="checked" name="nodos_opcionales" id="nodo_{__nodo_id}" value="{__nodo_id}" onclick="compruebanodos();"/>										
									<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 290px; display:inline">{__nodo_nombre}</div>
								</td>
								<!-- END LISTA_NODOS_TD -->
							</tr>
							<!-- END LISTA_NODOS_TR -->
						</table>
					</td>
				</tr>
			</table>
			<script>
				compruebanodos();
			</script>
		</td>
	</tr>
	<tr>
		<td height="15"></td>
	</tr>
	<!-- END LISTA_NODOS -->
    <!-- BEGIN BLOQUE_TIPOS -->
    <tr>
        <td>
           <table width="100%">
    	       <tr>
                   <td style="padding: 5px; background-color: #a2a2a2; color: #ffffff; font-size: 15; font-family: Trebuchet MS, Verdana, sans-serif; font-weight: bold; text-transform: uppercase;">Vistas</td>
               </tr>
               <tr>
                   <td align="center">
                       <input name="tipo_id" type="hidden" id="tipo_id" />
                       <table style="border-spacing: 10px; border-collapse: separate;">
                           <tr>
                               <!-- BEGIN LISTA_TIPOS -->
                               <td height="30" id="tipo_{__tipo_orden}" onclick="checkTipo('{__tipo_orden}');" width="150" class="checkboxunselected" style="font-size: 12px; font-family: Trebuchet MS, Verdana, sans-serif; border: solid 1px #a2a2a2; padding: 4px 4px 4px 28px; cursor:pointer">{__tipo_nombre}</td>
                               <!-- END LISTA_TIPOS -->
                            </tr>
                       </table>
                    </td>
                </tr>
            </table>
            <script>
            	checkTipo('{__tipo_orden_default}');
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
