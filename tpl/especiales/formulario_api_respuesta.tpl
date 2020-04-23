<script>
function validarFormulario() {

        if (document.form_principal.objetivo_especial_id) {
            if (document.form_principal.objetivo_especial_id.value == false) {
                alert("Debe seleccionar un objetivo.");
                return false;
            }
        }

        if (document.form_principal.tipo_id.value == "") {
                alert("Debe seleccionar una vista.");
                return false;
        }
       var api=true;
       if (document.form_principal.tipo_content.value == "html") {
            window.open('', 'formpopup', 'width=800, height=600, menubar, resizeable, scrollbars');
            document.form_principal.action = 'index.php?tiene_flash='+tiene_flash+"&tiene_svg="+tiene_svg+"&api="+api;
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

    document.getElementById("tipo_content").value = $("#tipo_"+tipo_content).data("tipo_content");
    if (document.getElementById("tipo_"+tipo_content_anterior))
    {
        document.getElementById("tipo_"+tipo_content_anterior).className = "checkboxunselected";
        document.getElementById("_tipo_"+tipo_content_anterior).className = "nada";
    }
    
    if (document.getElementById("tipo_"+tipo_content))
    {
        document.getElementById("tipo_"+tipo_content).className = "checkboxselected";
        document.getElementById("_tipo_"+tipo_content).className = "spriteImg spriteImg-bot_check";
        document.getElementById("tipo_id").value = tipo_content;
        tipo_content_anterior = tipo_content;
    }
}

var subobjetivo_anterior = 0;
function checkSubobjetivo(subobjetivo_id) {

    if (document.getElementById("subobjetivo_"+subobjetivo_anterior))
    {
        document.getElementById("subobjetivo_"+subobjetivo_anterior).className = "radiounselected";
        document.getElementById("sprite_"+subobjetivo_anterior).className = "nada";
    }

    if (document.getElementById("subobjetivo_"+subobjetivo_id))
    {
        document.getElementById("subobjetivo_"+subobjetivo_id).className = "radioselected";
        document.getElementById("sprite_"+subobjetivo_id).className = "spriteImg spriteImg-bot_check";
        document.form_principal.subobjetivo_id.value = subobjetivo_id;
        subobjetivo_anterior = subobjetivo_id;
    }
}

var horario_anterior = 0;
function checkHorario(horario_id) {

    if (document.getElementById("horario_"+horario_anterior))
    {
        document.getElementById("horario_"+horario_anterior).className = "radiounselected";
        document.getElementById("_sprite_"+horario_anterior).className = "nada";
    }
    
    if (document.getElementById("horario_"+horario_id))
    {
        document.getElementById("horario_"+horario_id).className = "radioselected";
        document.getElementById("_sprite_"+horario_id).className = "spriteImg spriteImg-bot_check";
        document.form_principal.horario_id.value = horario_id;
        horario_anterior = horario_id;
    }
}

</script>

<input type="hidden" name="popup" value="1" />
<input type="hidden" name="calendario_v2" value="1" />
<!-- BEGIN BLOQUE_TIPO_DEFAULT -->
<input name="tipo_id" type="hidden" id="tipo_id" value="{__tipo_orden}" />
<input name="tipo_content" type="hidden" id="tipo_content" value="{__tipo_content}" />
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
                    <div id="calendario_especial" style="display: none" >
                                
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
                        <div id="informes_disponibles" class="informes-disponibles-en-formulario"></div>
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
                                <td height="30" id="subobjetivo_{__subobjetivo_id}" onclick="checkSubobjetivo('{__subobjetivo_id}');" class="radiounselected" style="font-size: 13px; padding: 0px 0px 0px 20px; cursor: pointer; font-family: Trebuchet MS, Verdana, sans-serif; border: solid 1px #a2a2a2; ">
                                    <i id="sprite_{__subobjetivo_id}" onclick="checkSubobjetivo('{__subobjetivo_id}');" class="nada" style="position: absolute;"></i>
                                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 290px; float: left; padding: 0px 0px 0px 50px;">{__subobjetivo_nombre}</div>
                                </td>
                                <!-- END LISTA_SUBOBJETIVOS_TD -->
                            </tr>
                            <!-- END LISTA_SUBOBJETIVOS_TR -->
                        </table>
                    </td>
                </tr>
            </table>
            <script>
                checkSubobjetivo('{__subobjetivo_default}');
            </script>
        </td>
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
                                <td height="30" id="horario_{__horario_id}" onclick="checkHorario('{__horario_id}');" class="radiounselected" style="font-size: 13px; padding: 0px 0px 0px 20px; cursor: pointer; font-family: Trebuchet MS, Verdana, sans-serif; border: solid 1px #a2a2a2">
                                    <i id="_sprite_{__horario_id}" onclick="checkHorario('{__horario_id}');" class="nada" style="position: absolute;"></i>
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
                                                <input name="tipo_id" type="hidden" id="tipo_id" />
                                                <input name="tipo_content" type="hidden" id="tipo_content" />
                                                <table style="border-spacing: 10px; border-collapse: separate;">
                                                        <tr>
                                                                <!-- BEGIN LISTA_TIPOS -->
                                                                <td height="30" id="tipo_{__tipo_orden}" data-tipo_content="{__tipo_content}" onclick="checkTipo('{__tipo_orden}');" width="150" class="checkboxunselected" style="font-size: 13px; padding: 0px 0px 0px 20px; cursor: pointer; font-family: Trebuchet MS, Verdana, sans-serif; border: solid 1px #a2a2a2;">
                                                                    <i id="_tipo_{__tipo_orden}" data-tipo_content="{__tipo_content}" onclick="checkTipo('{__tipo_orden}');" class="nada" style="position: absolute;"></i>
                                                                    <div style="white-space: nowrap;  text-overflow: ellipsis; width: 90px; float: left; padding: 0px 0px 0px 50px;">{__tipo_nombre}</div>
                                                                </td>
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
