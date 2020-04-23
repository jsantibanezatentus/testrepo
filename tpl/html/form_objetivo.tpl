<script>
    function validarForm() {
        if (trim(document.form_principal.objetivo_nombre.value) == "") {
            alert("Debe ingresar un nombre.");
            return false;
        }
        if (existeNombreObjetivo(document.form_principal.objetivo_nombre.value, document.form_principal.objetivo_id.value) == "1") {
            alert("Ya existe el nombre en el sistema.");
            return false;
        }

        if (isNaN(document.form_principal.objetivo_sla_dis_ok.value) ||
                parseFloat(document.form_principal.objetivo_sla_dis_ok.value) > 100 ||
                parseFloat(document.form_principal.objetivo_sla_dis_ok.value) < 0) {
            alert("Debe ingresar un SLA Disponibilidad Ok valido.");
            return false;
        }

        if (isNaN(document.form_principal.objetivo_sla_dis_error.value) ||
                parseFloat(document.form_principal.objetivo_sla_dis_error.value) > 100 ||
                parseFloat(document.form_principal.objetivo_sla_dis_error.value) < 0) {
            alert("Debe ingresar un SLA Disponibilidad Error valido.");
            return false;
        }

        if (isNaN(document.form_principal.objetivo_sla_ren_ok.value) ||
                parseFloat(document.form_principal.objetivo_sla_ren_ok.value) > 600 ||
                parseFloat(document.form_principal.objetivo_sla_ren_ok.value) < 0) {
            alert("Debe ingresar un SLA Rendimiento Ok valido.");
            return false;
        }

        if (isNaN(document.form_principal.objetivo_sla_ren_error.value) ||
                parseFloat(document.form_principal.objetivo_sla_ren_error.value) > 600 ||
                parseFloat(document.form_principal.objetivo_sla_ren_error.value) < 0) {
            alert("Debe ingresar un SLA Rendimiento Error valido.");
            return false;
        }

        if (parseFloat(document.form_principal.objetivo_sla_dis_ok.value) < parseFloat(document.form_principal.objetivo_sla_dis_error.value)) {
            alert("Debe ingresar un SLA Disponibilidad Ok mayor o igual al SLA Disponibilidad Error.");
            return false;
        }

        if (parseFloat(document.form_principal.objetivo_sla_ren_ok.value) > parseFloat(document.form_principal.objetivo_sla_ren_error.value)) {
            alert("Debe ingresar un SLA Rendimiento Ok menor o igual al SLA Rendimiento Error.");
            return false;
        }

        var inputs = document.getElementsByTagName("input");
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].type == "text") {
                if (inputs[i].name.match(/^paso_nombre_/) && trim(inputs[i].value) == "") {
                    alert("No puede ingresar campos en blanco.");
                    inputs[i].focus();
                    return false;
                }
            }
        }
        abrirAccion(1, 'guardar_objetivo');
        guardarObjetivo()
    }
    function guardarObjetivo(){
        $.ajax({
                async: false,
                type: 'POST',
                url: 'utils/updateObjetives.php',
                data: {'user': '{__current_usuario_id}','function': 'update_objetives'},
                        success: function(data) {
                            console.log(data)
                    },
                        error: function(error) {
                    }
            });
    }

    function mostrarPatrones(grupo_patrones) {
        if (document.getElementById(grupo_patrones).style.display == "none") {
            document.getElementById(grupo_patrones).style.display = "inline";
        } else {
            document.getElementById(grupo_patrones).style.display = "none";
        }
    }

    var grupo_configuracion_anterior = 0;
    function cambiarGrupoConfiguracion(grupo_configuracion, disabled) {
        if (!document.getElementById("grupo_monitor_sel_" + grupo_configuracion_anterior)) {
            return false;
        }

        document.getElementById("grupo_monitor_sel_" + grupo_configuracion_anterior).style.display = "none";
        document.getElementById("grupo_monitor_" + grupo_configuracion_anterior).style.backgroundColor = "#f0ede8";
        document.getElementById("grupo_monitor_" + grupo_configuracion_anterior).style.color = "#525252";

        if (disabled != 'disabled') {
            var inputs = document.getElementsByTagName("input");
            var re = new RegExp("^paso_nombre_\\d+_" + grupo_configuracion);
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].type == "text" && inputs[i].name.match(/^paso_nombre_/)) {
                    if (inputs[i].id.match(re)) {
                        inputs[i].disabled = false;
                        if (document.getElementById(inputs[i].name + "_" + grupo_configuracion_anterior)) {
                            inputs[i].value = document.getElementById(inputs[i].name + "_" + grupo_configuracion_anterior).value;
                        }
                    } else {
                        inputs[i].disabled = true;
                    }
                }
            }
        }

        grupo_configuracion_anterior = grupo_configuracion;
        document.getElementById("grupo_monitor_sel_" + grupo_configuracion).style.display = "inline";
        document.getElementById("grupo_monitor_" + grupo_configuracion).style.backgroundColor = "#f36f00";
        document.getElementById("grupo_monitor_" + grupo_configuracion).style.color = "#ffffff";
    }
    function showModal(id, name, pasoUrl, timeout, method, pattern, shortUrl) {

        var result = "";
        var url = pasoUrl.replace(pasoUrl, "<a   href='" + pasoUrl + "' target='_blank'>" + pasoUrl + "</a>");
        var patternFine = '"';
        var extract = "localhost";
        url = url.replace(patternFine, '');
        url = url.replace(extract, '');
        //validacion si el usuario esta utilizando IE
        if (navigator.userAgent.indexOf("MSIE") > 0) {

            result = '<table class="definicion" width="100%" ><tr><th>Id : </th></tr><tr><td>' + id + '</td></tr><tr><th>Nombre Paso: </th></tr><tr><td>' + name + '</td></tr> <tr><th> Url : </th></tr><tr><td>' + url + '</td></tr> <tr><th>Timeout : </th></tr><tr><td>' + timeout + '</td></tr> <tr><th>Metodo : </th></tr><tr><td>' + method + '</td></tr><tr><th>Patron : </th></tr><tr><td>' + pattern + '</td></tr></table>';
            dojo.byId("usoIE").innerHTML = result;
        }
        //resto de navegadores
        else {

            result = '<tr><th>Id : </th></tr><tr><td>' + id + '</td></tr> <br><tr><th>Nombre Paso: </th></tr><tr><td>' + name + '</td></tr> <br><tr><th> Url : </th></tr><tr><td>' + url + '</td></tr> <br><tr><th>Timeout : </th></tr><tr><td>' + timeout + '</td></tr> <br><tr><th>Metodo : </th></tr><tr><td>' + method + '</td></tr> <br><tr><th>Patron : </th></tr><tr><td>' + pattern + '</td></tr> ';
            dojo.byId("contenido2").innerHTML = result;
        }

        //atributos del cuadro dojo
        dojo.attr('ModalMuestra_title', {
            innerHTML: "Nombre Paso: " + name,
            title: "Nombre Paso: " + name
        });
        dojo.attr('ModalMuestra', {
            style: "widht: 500px;height: 300px; overflow-y: hidden;overflow-x: hidden;padding:2px"
        });
        dijit.byId("ModalMuestra").show();
    }
</script>

<input type="hidden" name="objetivo_id" value="{__objetivo_id}">
<table width="100%">
    <tr>
        <td class="tituloseccion">Informacion del Objetivo</td>
    </tr>
    <tr>
        <td>
            <br>
            <div class="descripcion">
                &#8226; Si realiza alg&uacute;n cambio en la configuraci&oacute;n de los objetivos, &eacute;ste puede demorar algunos minutos en reflejarse en la secci&oacute;n de reportes.<br>
                &#8226; Los cambios realizados a esta configuraci&oacute;n pueden afectar todo los tipos de reportes, como tambi&eacute;n las alarmas.<br>
                &#8226; Para tener una buena visibilidad de la informaci&oacute;n, recomendamos que el largo de los nombres de objetivos y pasos no supere los 20 caracteres y que las descripciones no superen los 40 caracteres.<br> 
            </div>
            <br>
        </td>
    </tr>
    <tr>
        <td>
            <table width="60%" class="formulario">
                <tr>
                    <th width="140">Nombre</th>
                    <td colspan="2"><input type="text" name="objetivo_nombre" id="objetivo_nombre" value="{__objetivo_nombre}" class="inputtextbox" {__form_disabled}/></td>
                </tr>
                <tr>
                    <th>Descripci&oacute;n</th>
                    <td colspan="2"><input type="text" name="objetivo_descripcion" value="{__objetivo_descripcion}" class="inputtextbox" {__form_disabled}/></td>
                </tr>
                <tr>
                    <th>Servicio</th>
                    <td colspan="2">{__servicio_nombre}</td>
                </tr>
                <tr>
                    <th>Intervalo</th>
                    <td colspan="2">{__intervalo_nombre}&nbsp;</td>
                </tr>
                <!-- 				<tr>
                                                        <th>Timeout</th>
                                                        <td colspan="2">{__objetivo_timeout} segs</td>
                                                </tr> -->
                <tr>
                    <th>SLA Disponibilidad</th>
                    <td>Ok <input type="text" size="5" maxlength="6" name="objetivo_sla_dis_ok" value="{__objetivo_sla_dis_ok}" {__form_disabled}/> %</td>
                    <td>Error <input type="text" size="5" maxlength="6" name="objetivo_sla_dis_error" value="{__objetivo_sla_dis_error}" {__form_disabled}/> %</td>
                </tr>
                <tr>
                    <th>SLA Rendimiento</th>
                    <td>Ok <input type="text" size="5" maxlength="6" name="objetivo_sla_ren_ok" value="{__objetivo_sla_ren_ok}" {__form_disabled}/> segs</td>
                    <td>Error <input type="text" size="5" maxlength="6" name="objetivo_sla_ren_error" value="{__objetivo_sla_ren_error}" {__form_disabled}/> segs</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td height="40"></td>
    </tr>
    <tr>
        <td class="tituloitemconfig">Configuracion por Monitor</td>
    </tr>
    <tr>
        <td>
            <br>
            <div class="descripcion">
                &#8226; Puede ver la configuraci&oacute;n de los pasos del objetivo seleccionando el monitor deseado.<br>
                &#8226; Para ver la configuraci&oacute;n de cada patr&oacute;n, seleccione la cantidad de patrones del paso.
            </div>
            <br>
        </td>
    </tr>
    <tr>
        <td>
            <!-- BEGIN LISTA_MONITORES -->
            <div id="grupo_monitor_{__monitor_selector}" class="celdaselector" onclick="cambiarGrupoConfiguracion('{__monitor_selector}', '{__form_disabled}');">
                {__monitor_nombre}
            </div>
            <!-- END LISTA_MONITORES -->
            <!-- BEGIN NO_TIENE_MONITORES -->
            <div class="error">Este objetivo no tiene ning&uacute;n monitor asociado.</div>
            <!-- END NO_TIENE_MONITORES -->
        </td>
    </tr>
    <tr>
        <td height="20"></td>
    </tr>

    <tr>
        <td>
            <!-- BEGIN SIN_CONFIGURACION -->
            <div id="grupo_monitor_sel_{__monitor_selector}" style="display:none;">
                <div class="error">Este monitor no tiene ninguna configuraci&oacute;n asociada.</div>
            </div>
            <!-- END SIN_CONFIGURACION -->

            <!-- BEGIN ES_MAILTRAFFIC -->
            <div id="grupo_monitor_sel_{__monitor_selector}" style="display:none;">
                <table width="60%" class="formulario">
                    <tr>
                        <th width="140">Dominio</th>
                        <td>{__objetivo_dominio}</td>
                    </tr>
                    <tr>
                        <th>Tipo de Dominio</th>
                        <td>{__objetivo_dominio_tipo}</td>
                    </tr>
                    <tr>
                        <th>Timeout Dominio</th>
                        <td>{__objetivo_dominio_timeout}</td>
                    </tr>
                    <tr>
                        <th>Destinatario</th>
                        <td>{__objetivo_destinatario}</td>
                    </tr>
                    <tr>
                        <th>Remitente</th>
                        <td>{__objetivo_remitente}</td>
                    </tr>
                    <tr>
                        <th>Usuario</th>
                        <td>{__objetivo_usuario}</td>
                    </tr>
                    <tr>
                        <th>Clave</th>
                        <td>{__objetivo_clave}</td>
                    </tr>
                </table>
            </div>
            <!-- END ES_MAILTRAFFIC -->
            <!-- BEGIN ES_POP -->
            <div id="grupo_monitor_sel_{__monitor_selector}" style="display:none;">
                <table width="60%" class="formulario">
                    <tr>
                        <th width="140">Dominio</th>
                        <td>{__objetivo_dominio}</td>
                    </tr>
                    <tr>
                        <th>M&eacute;todo</th>
                        <td>{__objetivo_metodo}</td>
                    </tr>
                </table>
            </div>
            <!-- END ES_POP -->
            <!-- BEGIN ES_SMTP -->
            <div id="grupo_monitor_sel_{__monitor_selector}" style="display:none;">
                <table width="60%" class="formulario">
                    <tr>
                        <th width="140">Dominio</th>
                        <td>{__objetivo_dominio}</td>
                    </tr>
                </table>
            </div>
            <!-- END ES_SMTP -->
            <!-- BEGIN ES_DNSCHAOS -->
            <div id="grupo_monitor_sel_{__monitor_selector}" style="display:none;">
                <table width="60%" class="formulario">
                    <tr>
                        <th width="140">Resolver</th>
                        <td>{__objetivo_resolver}</td>
                    </tr>
                    <tr>
                        <th>Consulta</th>
                        <td>{__objetivo_consulta}</td>
                    </tr>
                    <tr>
                        <th>Tipo</th>
                        <td>{__objetivo_tipo}</td>
                    </tr>
                </table>
            </div>
            <!-- END ES_DNSCHAOS -->
            <!-- BEGIN ES_DNS -->
            <div id="grupo_monitor_sel_{__monitor_selector}" style="display:none;">
                <table width="60%" class="formulario">
                    <tr>
                        <th width="140">Dominio</th>
                        <td>{__objetivo_dominio}</td>
                    </tr>
                    <tr>
                        <th>Resolver</th>
                        <td>{__objetivo_resolver}</td>
                    </tr>
                </table>
            </div>
            <!-- END ES_DNS -->
            <!-- BEGIN TIENE_PASOS -->
            <div id="grupo_monitor_sel_{__monitor_selector}" style="display:none;">
                <table width="100%" class="listado">
                    <tr>
                        <th width="5%">&nbsp;</th>
                        <th width="20%">Nombre</th>
                        <th width="{__width_paso}">{__tabla_paso}</th>
                        <th width="{__width_timeout}">{__tabla_timeout}</th>
                        <th width="{__width_metodo}">{__tabla_metodo}</th>
                        <th width="{__width_patron}">{__tabla_patron}</th>
                    </tr>
                </table>
                <!-- BEGIN LISTA_PASOS -->
                <table width="100%" class="listado">
                    <tr>
                        <td width="5%" align="center">{__paso_orden}</td>
                        <td width="20%"><input type="text" name="paso_nombre_{__paso_id}" id="paso_nombre_{__paso_id}_{__monitor_selector}" value="{__paso_nombre}" class="inputtextbox" {__form_disabled} /></td>
                        <td width="{__width_paso}" title="{__paso_url}"><a href="#" onclick="showModal('{__paso_orden}', '{__paso_nombre}', '{__paso_url}', '{__paso_timeout}', '{__paso_metodo}', '{__paso_patrones}', '{__paso_url_corta}');">{__paso_url_corta}</a></td>
                        <td width="{__width_timeout}" align="center">{__paso_timeout}</td>
                        <td width="{__width_metodo}" align="center">{__paso_metodo}</td>
                        <!-- BEGIN LINK_PATRON -->
                        <td width="{__width_patron}" align="center"><a href="#" onclick="mostrarPatrones('grupo_patrones_{__monitor_selector}_{__paso_id}');return false;">{__paso_patrones}</a></td>
                        <!-- END LINK_PATRON -->
                        <!-- BEGIN TD_LLAMADA -->
                        <td width="{__width_patron}" align="center">{__paso_patrones}</td>
                        <!-- END TD_LLAMADA -->
                    </tr>
                </table>
                <div>
                    <div id="grupo_patrones_{__monitor_selector}_{__paso_id}" style="display: none;">
                        <table width="100%" class="listado">
                            <tr>
                                <td style="padding: 15px; background-color: #f6f6f6;">
                                    <table align="center" class="listado_mini">
                                        <tr>
                                            <th width="150">Nombre</th>
                                            <th width="200">Valor</th>
                                            <th width="100">Tipo</th>
                                            <th width="60">Inverso</th>
                                            <th width="60">Opcional</th>
                                        </tr>
                                        <!-- BEGIN LISTA_PATRONES -->
                                        <tr>
                                            <td>{__patron_nombre}</td>
                                            <td title="{__patron_valor}">{__patron_valor_corto}</td>
                                            <td>{__patron_tipo}</td>
                                            <td align="center">{__patron_inverso}</td>
                                            <td align="center">{__patron_opcional}</td>
                                        </tr>
                                        <!-- END LISTA_PATRONES -->
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- END LISTA_PASOS -->
            </div>
            <!-- END TIENE_PASOS -->
        </td>
    </tr>
    <tr>
        <td>
            <br>
            <!-- BEGIN PUEDE_MODIFICAR -->
            <table align="center">
                <tr>
                    <td style="text-align:right">
                        <input type="button" class="boton_accion" onclick="validarForm()" value="Guardar"/>					
                    </td>
                    <td style="width:20px">&nbsp;</td>
                    <td style="text-align:left">
                        <input type="button" class="boton_cancelar" value="Cancelar" onclick="mostrarSubmenu({__padre_id},{__seccion_id},{__nivel})" />
                    </td>
                </tr>
            </table>
            <br>
            <!-- END PUEDE_MODIFICAR -->
        </td>
    </tr>
</table>
<div dojoType="dijit.Dialog" id="ModalMuestra" style="width: 500px">
    <div class="Modal" id="usoIE">
        <table class="definicion" style="width:480px;" id="contenido2" ></table>
    </div>
</div> 
<script>
    cambiarGrupoConfiguracion(grupo_configuracion_anterior, '{__form_disabled}');
</script>
