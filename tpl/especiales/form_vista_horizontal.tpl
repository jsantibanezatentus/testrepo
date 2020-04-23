<script>
function validarFormulario() {
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

</script>
<input type="hidden" name="popup" value="1" />
<input type="hidden" name="calendario_v2" value="1" />
<input name="tipo_content" type="hidden" id="tipo_content" value="{__tipo_content}" />
<table width="100%">
        <tr>
                <td class="tituloseccion">{__reporte_titulo}</td>
        </tr>
</table>
<br>

<table align="center" width="80%">
        <tr>
                <td>
                    <div id="calendario_especial" style="display: none;"></div>
                        <script type="text/javascript">
                          jQuery(function($) {
                            var $calendarioEspecial = $("#calendario_especial");
                            var params = {};

                            var fechaCalendario = "{__fecha_inicio}";
                            if(fechaCalendario.length > 0) {
                              params["fechaCalendario"] =  fechaCalendario + "T00:00:00";
                            }

                            var fechaMinima = '';
                            if(fechaMinima.length > 0) {
                              params["fechaMinima"] = fechaMinima + "T00:00:00";
                            }

                            params["seleccion"] = {};
                            params["seleccion"]["activa"] = "true";
                            params["seleccion"]["intervalo"] = "true";

                            $calendarioEspecial.calendariou(params);
                            var calendariou = $calendarioEspecial.data("calendariou");

                            var $inputFechaInicio = $('<input type="hidden" name="fecha_inicio_periodico" id="fecha_inicio_periodico" value="{__fecha_inicio_periodo}">');
                            var $inputFechaTermino = $('<input type="hidden" name="fecha_termino_periodico" id="fecha_termino_periodico" value="{__fecha_termino_periodo}">');

                            $calendarioEspecial.append($inputFechaInicio, $inputFechaTermino);

                            calendariou.seleccion.el().on("calendariou:seleccion:cambio", function() {
                                var fechaInicio = calendariou.seleccion.get("fechaInicio");
                                var fechaTermino = calendariou.seleccion.get("fechaTermino");
                                $inputFechaInicio.prop("value", fechaInicio === null ? null : fechaInicio.format("yyyy-mm-ddThh:mm:ss"));
                                $inputFechaTermino.prop("value", fechaTermino === null ? null : fechaTermino.format("yyyy-mm-ddThh:mm:ss"));
                            });
                          });
                        </script>
                </td>
        </tr>
        <tr>
                <td height="15"></td>
        </tr>
        <tr>
                <td align="center">
                        <input type="button" value="Generar Vista Rápida" class="boton_accion" onclick="validarFormulario();"/>
                </td>
        </tr>
</table>
<br>
