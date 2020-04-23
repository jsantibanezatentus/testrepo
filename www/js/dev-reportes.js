var textoseparacion = "SEPARACIONCELDA";
var regexp_date = /^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/;
var regexp_time = /^(2[0-3]|[0-1][0-9]):[0-5][0-9]$/;

if (typeof String.prototype.trim !== 'function') {
    String.prototype.trim = function () {
        return this.replace(/^\s+|\s+$/g, '');
    }
}

(function ($) {

    $.fn.dataTableExt.oApi.fnGetColumnData = function (oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty) {
        if (typeof iColumn == "undefined")
            return new Array();
        if (typeof bUnique == "undefined")
            bUnique = true;
        if (typeof bFiltered == "undefined")
            bFiltered = true;
        if (typeof bIgnoreEmpty == "undefined")
            bIgnoreEmpty = true;

        var aiRows;
        var asResultData = new Array();

        if (bFiltered == true)
            aiRows = oSettings.aiDisplay;
        else
            aiRows = oSettings.aiDisplayMaster;

        for (var i = 0, c = aiRows.length; i < c; i++) {

            iRow = aiRows[i];

            var aData = this.fnGetData(iRow);
            var sValue = aData[iColumn];

            var re = new RegExp("<div style=\"display: none\">(.+)<\/div>", "i");
            var tagDiv = re.exec(sValue);

            if (tagDiv == null || tagDiv.length != 2)
                continue;

            // ignore empty values?
            if (bIgnoreEmpty == true && tagDiv[1].length == 0)
                continue;

            // ignore unique values?
            else if (bUnique == true && jQuery.inArray(tagDiv[1], asResultData) > -1)
                continue;

            // else push the value onto the result data array
            else
                asResultData.push(tagDiv[1]);
        }
        return asResultData;
    }
}(jQuery));

function scrollBotonAdelante(monitoreo_id) {
    $("#monitoreo_" + monitoreo_id).animate({scrollLeft: '+=200'}, 500, function () {
        if (($("#monitoreo_" + monitoreo_id).scrollLeft() + $("#monitoreo_" + monitoreo_id).width()) == $("#monitoreo_tabla_" + monitoreo_id).width()) {
            document.getElementById("btn_adelante_" + monitoreo_id).style.display = "none";
        }
        if (document.getElementById("btn_atras_" + monitoreo_id).style.display == "none") {
            document.getElementById("btn_atras_" + monitoreo_id).style.display = "inline";
        }
    });
}

function scrollBotonAtras(monitoreo_id) {
    $("#monitoreo_" + monitoreo_id).animate({scrollLeft: '-=200'}, 500, function () {
        if ($("#monitoreo_" + monitoreo_id).scrollLeft() == 0) {
            document.getElementById("btn_atras_" + monitoreo_id).style.display = "none";
        }
        if (document.getElementById("btn_adelante_" + monitoreo_id).style.display == "none") {
            document.getElementById("btn_adelante_" + monitoreo_id).style.display = "inline";
        }
    })
}

function fnCreateSelect(aData) {
    var r = '<select style="width:100%;"><option value="">Seleccionar Todo</option>', i, iLen = aData.length;
    for (i = 0; i < iLen; i++) {
        r += '<option value="' + aData[i] + '">' + aData[i] + '</option>';
    }
    return r + '</select>';
}


var arr_tiempos = new Array();

function tiempoRecarga(tiempo, item_id) {
    if (tiempo == null || !tiempo.match(/^[0-9]+$/) || tiempo > 86400 || tiempo < 10) {
        if (arr_tiempos[item_id] == null) {
            return 86400000;
        } else {
            return arr_tiempos[item_id] * 1000;
        }
    } else {
        arr_tiempos[item_id] = tiempo;
        return tiempo * 1000;
    }
}

function mostrarDetallePlus(tipo, nodo, fecha) {

    dojo.attr('subcontenedor_'+tipo+'_'+nodo, {
        style: "widht: auto;height: auto;overflow-y: hidden;overflow-x: hidden"
    });

    if (document.getElementById('pasos_' + tipo + '_' + nodo + '_' + fecha).style.display == "none") {
        document.getElementById('pasos_' + tipo + '_' + nodo + '_' + fecha).style.display = "inline";
        document.getElementById('imagen_' + tipo + '_' + nodo + '_' + fecha).className = "spriteButton spriteButton-cerrar_calendario";
//      document.getElementById('td_'+tipo+'_'+nodo+'_'+fecha).style.backgroundColor="#d0d0d0";
    } else {
        document.getElementById('pasos_' + tipo + '_' + nodo + '_' + fecha).style.display = "none";
        document.getElementById('imagen_' + tipo + '_' + nodo + '_' + fecha).className = "spriteButton spriteButton-abrir_calendario";
//          document.getElementById('td_'+tipo+'_'+nodo+'_'+fecha).style.backgroundColor="#e2e2e2";
    }
}

function mostrarGraficoPlus(item_id, monitor_id, paso_id, fecha, downloadCsv) {
    if(downloadCsv=='true'){
        document.getElementById("download").value='true';
        cargarItem("elemplus_" + monitor_id + "_" + paso_id + "_" + fecha, item_id, 0, ['monitor_id', monitor_id, 'paso_id', paso_id, 'fecha_monitoreo', fecha]);
    }
    else{
       if (document.getElementById("elemplus_" + monitor_id + "_" + paso_id + "_" + fecha)) {
            if (document.getElementById("elemplus_" + monitor_id + "_" + paso_id + "_" + fecha).style.display == 'inline') {
                document.getElementById("download").value='false';
                dijit.byId("elemplus_" + monitor_id + "_" + paso_id + "_" + fecha).attr('style', 'display:none');
                document.getElementById("flecha_" + monitor_id + "_" + paso_id + "_" + fecha).className = "spriteButton spriteButton-abrir_calendario";
            } else {
                document.getElementById("download").value='false';
                cargarItem("elemplus_" + monitor_id + "_" + paso_id + "_" + fecha, item_id, 0, ['monitor_id', monitor_id, 'paso_id', paso_id, 'fecha_monitoreo', fecha]);
                dijit.byId("elemplus_" + monitor_id + "_" + paso_id + "_" + fecha).attr('style', 'display:inline');
                document.getElementById("flecha_" + monitor_id + "_" + paso_id + "_" + fecha).className = "spriteButton spriteButton-cerrar_calendario";
            }
        }
    }
}


function cambiarTamanoScreenshot() {

    if (document.getElementById("screenshot_imagen").width < 720) {
        return;
    }
    if (document.getElementById("aumentar_screenshot").className == "spriteButton spriteButton-lupa-menos") {
        document.getElementById("screenshot_imagen").style.width = "720";
        document.getElementById("aumentar_screenshot").className = "spriteButton spriteButton-lupa-mas";
    } else {
        document.getElementById("screenshot_imagen").style.width = "auto";
        document.getElementById("aumentar_screenshot").className = "spriteButton spriteButton-lupa-menos";
    }
}

function guardarOrden(objs) {
    var sitio_id = document.form_principal.sitio_id.value;
    var menu_id = document.form_principal.menu_id.value;
    var objeto_id = document.form_principal.objeto_id.value;

    dojo.xhrPost({
        url: "index.php",
        postData: "sitio_id=" + sitio_id + "&menu_id=" + menu_id + "&objeto_id=" + objeto_id +
                "&ejecutar_accion=1&accion=guardar_orden_objetivo" + objs,
        load: function (response, ioArgs) {
            return response;
        }
    });
}

function mostrarSubmenu(sitio_id, seccion_id, abrir_enlace, event) {
    if ((typeof event == 'undefined')){
        event = 0;
    }
    if (abrir_enlace == 1) {
        abrirEnlace(sitio_id, seccion_id, 0, event);
    } else {
 
        var currentAnimation;
        if (document.getElementById("submenu_" + seccion_id).style.display == "none") {
            document.getElementById("flecha_" + seccion_id).className = "flechamenuizp spriteButton spriteButton-flecha_abajo";
            currentAnimation = dojo.fx.wipeIn({node: "submenu_" + seccion_id, duration: 1000});
        } else {
            document.getElementById("flecha_" + seccion_id).className = "flechamenuizp spriteButton spriteButton-flecha_derecha";
            currentAnimation = dojo.fx.wipeOut({node: "submenu_" + seccion_id, duration: 1000});
        }
        currentAnimation.play();
    }
}

function abrirEnlace(sitio_id, menu_id, objeto_id, event) {

  
       
    post_to_url('index.php', {'sitio_id': sitio_id, 'menu_id': menu_id, 'objeto_id': objeto_id, 'event': event});

  

}

function abrirAccion(ejecutar_accion, accion) {
    document.form_principal.ejecutar_accion.value = ejecutar_accion;
    document.form_principal.accion.value = accion;
    if (arguments.length == 3) {
        parametros = arguments[2];
        for (i = 0; i < parametros.length; i = i + 2) {
            document.getElementsByName(parametros[i])[0].value = parametros[i + 1];
        }
    }
    document.form_principal.submit();
}

function abrirAccionDetalle(contenedor, ejecutar_accion, accion, notificacion, objetivo) {
    document.form_principal.ejecutar_accion.value = ejecutar_accion;
    document.form_principal.accion.value = accion;
    var url = "index.php?sitio_id=" + document.form_principal.sitio_id.value +
            "&menu_id=" + document.form_principal.sitio_id.value +
            "&objetivo_id=" + objetivo;
    dijit.byId(contenedor).attr('content', '<table width="100%" align="center"><tr><td style="background-color:#f6f6f6" width="35%">&nbsp;</td><td width="30" align="center" style="background-color:#f6f6f6"><img src="img/cargando.gif"></td><td class="textgris12" style="background-color:#f6f6f6">Por favor espere.<br>El reporte se esta generando.</td><td style="background-color:#f6f6f6">&nbsp;</td></tr></table>');

    if (notificacion[1])
        url = url + "&notificacion_id=" + notificacion[1];
    url = url + "&accion=" + accion;

    dojo.xhrPost({
        url: url,
        load: function (data) {
            if ((data.indexOf('password') > 0) && (data.indexOf('username') > 0)) {
                logout();
                return;
            }
            dijit.byId(contenedor).attr('content', data);
        }
    });

}

function abrirFormulario(tipo_contenedor, ejecutar_accion, accion) {
    var sitio_id = document.form_principal.sitio_id.value;
    var menu_id = document.form_principal.menu_id.value;
    var objeto_id = document.form_principal.objeto_id.value;
    var content = "";
    if (arguments.length == 4) {
        parametros = arguments[3];
        for (i = 0; i < parametros.length; i = i + 2) {
            content = content + "&" + parametros[i] + "=" + parametros[i + 1];
        }
    }
    dojo.xhrPost({
        url: "index.php?sitio_id=" + sitio_id + "&menu_id=" + menu_id + "&objeto_id=" + objeto_id +
                "&ejecutar_accion=" + ejecutar_accion + "&accion=" + accion +
                content,
        load: function (data) {
            if ((data.indexOf('password') > 0) && (data.indexOf('username') > 0)) {
                logout();
                return;
            }
            dijit.byId("dialog_" + tipo_contenedor).attr('content', data);
            dijit.byId("dialog_" + tipo_contenedor).show();
        }
    });
}

function abrirDetalles(contenedor, accion) {

    document.getElementById(contenedor).innerHTML = '<table align="center"><tr><td width="30" align="center"><img src="img/cargando.gif"></td><td class="textgris12">Por favor espere.<br>El reporte se esta generando.</td></tr></table>';

    var sitio_id = document.form_principal.sitio_id.value;
    var menu_id = document.form_principal.menu_id.value;
    var objeto_id = document.form_principal.objeto_id.value;
    var content = "";
    if (arguments.length == 3) {
        parametros = arguments[2];
        for (i = 0; i < parametros.length; i = i + 2) {
            content = content + "&" + parametros[i] + "=" + parametros[i + 1];
        }
    }

    dojo.xhrPost({
        url: "index.php?sitio_id=" + sitio_id + "&menu_id=" + menu_id + "&objeto_id=" + objeto_id +
                "&ejecutar_accion=0&accion=" + accion +
                content,
        load: function (data) {
            dijit.byId(contenedor).attr('content', data);
        }
    });
}

function cargaItemFiltroElementosPlus(contenedor, item_id, recargar, parametros, data_filtro) {
      var sitio_id = document.form_principal.sitio_id.value;
      var menu_id = document.form_principal.menu_id.value;
      var objeto_id = document.form_principal.objeto_id.value;
      var content = '';
      var content_return = '';

      var hora_inicio = data_filtro[0];
      var minuto_inicio = data_filtro[1];
      var hora_termino = data_filtro[2];
      var minuto_termino = data_filtro[3];
      var nodo_filtro = data_filtro[4];

      if (parametros != null) {
          for (i = 0; i < parametros.length; i = i + 2) {
              content = content + "&" + parametros[i] + "=" + parametros[i + 1];
              content_return = content_return + "'" + parametros[i] + "'," + "'" + parametros[i + 1] + "',";
          }
      }

      var startTime = new Date().getTime();

      dojo.xhrPost({
          url: "index.php?sitio_id=" + sitio_id +
                  "&menu_id=" + menu_id +
                  "&objeto_id=" + objeto_id +
                  "&ejecutar_accion=1" +
                  "&accion=buscar_item" +
                  "&item_id=" + item_id +
                  "&item_tipo=html" +
                  "&tiene_flash=" + tiene_flash +
                  "&tiene_svg=" + tiene_svg +
                  "&hora_inicio=" + hora_inicio +
                  "&minuto_inicio=" + minuto_inicio +
                  "&hora_termino=" + hora_termino +
                  "&minuto_termino=" + minuto_termino +
                  "&nodo_filtro="+ nodo_filtro +
                  content,
          load: function (data) {
              if (data.trim() === "LOGOUT") {
                  logout();
                  return;
              }

              var param = data.split(textoseparacion);
              var tabla = param[0];

              if (tabla == 0) {

                  if($("#boton_adelante_"+parametros[1]).addClass( "botonadelante_disabled" ))
                  alert("No existen más datos para mostrar.");
                  return;
              }

              if (tabla.trim() !== "RELOAD") {
                  var expr = new RegExp('>[ \t\r\n\v\f]*<', 'g');
                  tabla = tabla.replace(expr, '><');
                  dijit.byId(contenedor).attr('content', tabla);

              }

              var tiempo_recarga = tiempoRecarga(param[1], item_id);
              if (recargar == 1) {
                  setTimeout("cargarItem('" + contenedor + "', '" + item_id + "', '1', [" + content_return + "])", tiempo_recarga);
              }


              /*
               * Realiza tracking del ítem para Google Analytics
               *
               */
              var $tablaItem = $("#tabla_item_" + item_id);
              var gaTrackingId = $tablaItem.data("ga-tracking-id");
              var usuarioId = $tablaItem.data("usuario-id");
              var objetivoId = $tablaItem.data("objetivo-id");
              var itemURL = $tablaItem.data("item-url");

              var endTime = new Date().getTime();
              var timeSpent = endTime - startTime;


              var _gaq = window._gaq || [];

              _gaq.push(['_setAccount', gaTrackingId]);
              _gaq.push(['_setCustomVar', 1, 'uid', usuarioId, 3]);

              if (objetivoId) {
                  _gaq.push(['_setCustomVar', 2, 'oid', objetivoId, 3]);
              } else {
                  _gaq.push(['_setCustomVar', 2, 'oid', '<not set>', 3]);
              }
              _gaq.push(['_trackTiming', itemURL, 'loadTime', timeSpent, 'cargarItem', 100]);

          }
      });
  }

function cargaItemFiltroScreenshot(contenedor, item_id, recargar, parametros, data_filtro) {
      var sitio_id = document.form_principal.sitio_id.value;
      var menu_id = document.form_principal.menu_id.value;
      var objeto_id = document.form_principal.objeto_id.value;
      var content = '';
      var content_return = '';
      var hora_inicio = data_filtro[0];
      var minuto_inicio = data_filtro[1];
      var hora_termino = data_filtro[2];
      var minuto_termino = data_filtro[3];
      var nodo_filtro = data_filtro[4];

      if (parametros != null) {
          for (i = 0; i < parametros.length; i = i + 2) {
              content = content + "&" + parametros[i] + "=" + parametros[i + 1];
              content_return = content_return + "'" + parametros[i] + "'," + "'" + parametros[i + 1] + "',";
          } 
        }
        var startTime = new Date().getTime();
        dojo.xhrPost({
          url: "index.php?sitio_id=" + sitio_id +
                  "&menu_id=" + menu_id +
                  "&objeto_id=" + objeto_id +
                  "&ejecutar_accion=1" +
                  "&accion=buscar_item" +
                  "&item_id=" + item_id +
                  "&item_tipo=html" +
                  "&tiene_flash=" + tiene_flash +
                  "&tiene_svg=" + tiene_svg +
                  "&hora_inicio=" + hora_inicio +
                  "&minuto_inicio=" + minuto_inicio +
                  "&hora_termino=" + hora_termino +
                  "&minuto_termino=" + minuto_termino +
                  "&nodo_filtro="+ nodo_filtro +
                  content,
                  load: function (data) {
              if (data.trim() === "LOGOUT") {
                  logout();
                  return;
              }

              var param = data.split(textoseparacion);
              var tabla = param[0];

              if (tabla == 0) {

                  if($("#boton_adelante_"+parametros[1]).addClass( "botonadelante_disabled" ))
                  alert("No existen más datos para mostrar.");
                  return;
              }

              if (tabla.trim() !== "RELOAD") {
                  var expr = new RegExp('>[ \t\r\n\v\f]*<', 'g');
                  tabla = tabla.replace(expr, '><');
                  dijit.byId(contenedor).attr('content', tabla);

              }

              var tiempo_recarga = tiempoRecarga(param[1], item_id);
              if (recargar == 1) {
                  setTimeout("cargarItem('" + contenedor + "', '" + item_id + "', '1', [" + content_return + "])", tiempo_recarga);
              }


              /*
               * Realiza tracking del ítem para Google Analytics
               *
               */
              var $tablaItem = $("#tabla_item_" + item_id);
              var gaTrackingId = $tablaItem.data("ga-tracking-id");
              var usuarioId = $tablaItem.data("usuario-id");
              var objetivoId = $tablaItem.data("objetivo-id");
              var itemURL = $tablaItem.data("item-url");

              var endTime = new Date().getTime();
              var timeSpent = endTime - startTime;


              var _gaq = window._gaq || [];

              _gaq.push(['_setAccount', gaTrackingId]);
              _gaq.push(['_setCustomVar', 1, 'uid', usuarioId, 3]);

              if (objetivoId) {
                  _gaq.push(['_setCustomVar', 2, 'oid', objetivoId, 3]);
              } else {
                  _gaq.push(['_setCustomVar', 2, 'oid', '<not set>', 3]);
              }
              _gaq.push(['_trackTiming', itemURL, 'loadTime', timeSpent, 'cargarItem', 100]);

          }
      });
  }

function cargarItem(contenedor, item_id, recargar, parametros) {
    var sitio_id = document.form_principal.sitio_id.value;
    var menu_id = document.form_principal.menu_id.value;
    var objeto_id = document.form_principal.objeto_id.value;
//  var subobjeto_id = document.form_principal.subobjeto_id.value;
    var content = '';
    var content_return = '';

    if (parametros != null) {
        for (i = 0; i < parametros.length; i = i + 2) {
            content = content + "&" + parametros[i] + "=" + parametros[i + 1];
            content_return = content_return + "'" + parametros[i] + "'," + "'" + parametros[i + 1] + "',";
        }
    }

    var startTime = new Date().getTime();

    dojo.xhrPost({
        url: "index.php?sitio_id=" + sitio_id +
                "&menu_id=" + menu_id +
                "&objeto_id=" + objeto_id +
//           "&subobjeto_id="+subobjeto_id+
                "&ejecutar_accion=1" +
                "&accion=buscar_item" +
                "&item_id=" + item_id +
                "&item_tipo=html" +
                "&tiene_flash=" + tiene_flash +
                "&tiene_svg=" + tiene_svg +
                content,
        load: function (data) {
            if (data.trim() === "LOGOUT") {
                logout();
                return;
            }

            var param = data.split(textoseparacion);
            var tabla = param[0];

            if (tabla == 0) {

                if($("#boton_adelante_"+parametros[1]).addClass( "botonadelante_disabled" ))
                alert("No existen más datos para mostrar.");
                return;
            }
//          alert(tabla);
            if (tabla.trim() !== "RELOAD") {
                var expr = new RegExp('>[ \t\r\n\v\f]*<', 'g');
                tabla = tabla.replace(expr, '><');
                dijit.byId(contenedor).attr('content', tabla);

            }
            /*          else {
             alert('reload');
             }*/

            var tiempo_recarga = tiempoRecarga(param[1], item_id);
            if (recargar == 1) {
//              alert("cargarItem('"+contenedor+"', '"+item_id+"', '1', ["+content_return+"]), " + tiempo_recarga);
                setTimeout("cargarItem('" + contenedor + "', '" + item_id + "', '1', [" + content_return + "])", tiempo_recarga);
            }


            /*
             * Realiza tracking del ítem para Google Analytics
             *
             */
            var $tablaItem = $("#tabla_item_" + item_id);
            var gaTrackingId = $tablaItem.data("ga-tracking-id");
            var usuarioId = $tablaItem.data("usuario-id");
            var objetivoId = $tablaItem.data("objetivo-id");
            var itemURL = $tablaItem.data("item-url");

            var endTime = new Date().getTime();
            var timeSpent = endTime - startTime;


            var _gaq = window._gaq || [];

            _gaq.push(['_setAccount', gaTrackingId]);
            _gaq.push(['_setCustomVar', 1, 'uid', usuarioId, 3]);

            if (objetivoId) {
                _gaq.push(['_setCustomVar', 2, 'oid', objetivoId, 3]);
            } else {
                _gaq.push(['_setCustomVar', 2, 'oid', '<not set>', 3]);
            }

            //_gaq.push(['_trackPageview', itemURL]);

            _gaq.push(['_trackTiming', itemURL, 'loadTime', timeSpent, 'cargarItem', 100]);

        }
    });
}

function cargarSubItem(contenedor, contenedor_new, item_id, item_id_new, parametros,semaforo=1) {
    // Cerrar dialog que hallan quedado abiertos.
    try{$(".ui-dialog-content").dialog("close");}catch(err){}
    

    var sitio_id = document.form_principal.sitio_id.value;
    var menu_id = document.form_principal.menu_id.value;
    var objeto_id = document.form_principal.objeto_id.value;
    var objeto_id_old = document.form_principal.objeto_id.value;
    var content = '';
    var content_return = '';

    if (parametros != null) {
        for (i = 0; i < parametros.length; i = i + 2) {
            if (parametros[i] == "objetivo_id") {
                objeto_id = parametros[i + 1];
                document.form_principal.objeto_id.value = parametros[i + 1];
            } else {
                content = content + "&" + parametros[i] + "=" + parametros[i + 1];
                content_return = content_return + "'" + parametros[i] + "'," + "'" + parametros[i + 1] + "',";
            }
        }
    }

    dojo.xhrPost({
        url: "index.php?sitio_id=" + sitio_id +
                "&menu_id=" + menu_id +
                "&objeto_id=" + objeto_id +
                "&ejecutar_accion=1" +
                "&accion=buscar_item" +
                "&item_id=" + item_id_new +
                "&item_tipo=html" +
                "&tiene_flash=" + tiene_flash +
                "&tiene_svg=" + tiene_svg +
                "&semaforo=" + semaforo +
                content,
        load: function (data) {
            if (data.trim() === "LOGOUT") {
                logout();
                return;
            }

            var param = data.split(textoseparacion);
            var tabla = param[0];
            var expr = new RegExp('>[ \t\r\n\v\f]*<', 'g');
            tabla = tabla.replace(expr, '><');

            var subtabla = '<div id="' + contenedor_new + '" dojoType="dojox.layout.ContentPane">' + tabla + '</div>' +
                    '<input type="button" value="Volver" onclick="document.form_principal.objeto_id.value=\'' + objeto_id_old + '\'; ' +
                    'cargarItem(\'' + contenedor + '\', \'' + item_id + '\', \'0\', [' + content_return + ']);"><br>&nbsp;';

            dijit.byId(contenedor).attr('content', subtabla);
        }
    });
}

function descargarCSV(item_id) {
    var sitio_id = document.form_principal.sitio_id.value;
    var menu_id = document.form_principal.menu_id.value;
    var objeto_id = document.form_principal.objeto_id.value;
    var datos_codificacion = document.getElementById("datos_codificacion_" + item_id).options[document.getElementById("datos_codificacion_" + item_id).selectedIndex].value;

    var datos_separador = 0;
    for (i = 0; i < document.getElementsByName("datos_separador_" + item_id).length; i++) {
        if (document.getElementsByName("datos_separador_" + item_id)[i].checked) {
            datos_separador = document.getElementsByName("datos_separador_" + item_id)[i].value;
        }
    }

    var datos_decimal = 0;
    for (i = 0; i < document.getElementsByName("datos_decimal_" + item_id).length; i++) {
        if (document.getElementsByName("datos_decimal_" + item_id)[i].checked) {
            datos_decimal = document.getElementsByName("datos_decimal_" + item_id)[i].value;
        }
    }

    var datos_uptime = 0;
    if (document.getElementById("datos_uptime_" + item_id) && document.getElementById("datos_uptime_" + item_id).checked) {
        datos_uptime = 1;
    }

    var datos_downtime_parcial = 0;
    if (document.getElementById("datos_downtime_parcial_" + item_id) && document.getElementById("datos_downtime_parcial_" + item_id).checked) {
        datos_downtime_parcial = 1;
    }

    var datos_downtime_global = 0;
    if (document.getElementById("datos_downtime_global_" + item_id) && document.getElementById("datos_downtime_global_" + item_id).checked) {
        datos_downtime_global = 1;
    }

    var datos_nomonitoreo = 0;
    if (document.getElementById("datos_nomonitoreo_" + item_id) && document.getElementById("datos_nomonitoreo_" + item_id).checked) {
        datos_nomonitoreo = 1;
    }

    var datos_eventoespecial = 0;
    if (document.getElementById("datos_evento_cliente_" + item_id) && document.getElementById("datos_evento_cliente_" + item_id).checked) {
        datos_eventoespecial = 1;
    }

    if (document.getElementById("datos_uptime_" + item_id) && datos_uptime == 0 && datos_downtime_parcial == 0 && datos_downtime_global == 0 && datos_nomonitoreo == 0 && datos_eventoespecial == 0) {
        alert("Debe seleccionar por lo menos un tipo de disponibilidad a mostrar.");
        return false;
    }

    if (document.getElementById("datos_uptime_" + item_id) && datos_uptime == 1 && datos_downtime_parcial == 1 && datos_downtime_global == 1 && datos_nomonitoreo == 1  && datos_eventoespecial == 1) {
        alert("No puede seleccionar todos los tipos de disponibilidad al mismo tiempo.");
        return false;
    }
    window.open("index.php?sitio_id=" + sitio_id + "&menu_id=" + menu_id + "&objeto_id=" + objeto_id +
            "&ejecutar_accion=1&accion=buscar_item" +
            "&item_id=" + item_id + "&item_tipo=csv" +
            "&datos_codificacion=" + datos_codificacion +
            "&datos_separador=" + datos_separador +
            "&datos_decimal=" + datos_decimal +
            "&datos_uptime=" + datos_uptime +
            "&datos_downtime_parcial=" + datos_downtime_parcial +
            "&datos_downtime_global=" + datos_downtime_global +
            "&datos_nomonitoreo=" + datos_nomonitoreo+
            "&datos_eventoespecial=" + datos_eventoespecial,
            "targetWindow");
}

function getElementsByName_iefix(tag, name) {
    var elem = document.getElementsByTagName(tag);
    var arr = new Array();
    for (i = 0, iarr = 0; i < elem.length; i++) {
        att = elem[i].getAttribute("name");
        if (att == name) {
            arr[iarr] = elem[i];
            iarr++;
        }
    }
    return arr;
}

function abrirPopup(parametros) {
    var sitio_id = document.form_principal.sitio_id.value;
    var menu_id = document.form_principal.menu_id.value;
    var objeto_id = document.form_principal.objeto_id.value;
//  var subobjeto_id = document.form_principal.subobjeto_id.value;
    var content = '';

    for (i = 0; i < parametros.length; i = i + 2) {
        content = content + "&" + parametros[i] + "=" + parametros[i + 1];
    }

    var opciones = "toolbar=no, " +
            "location=no, " +
            "directories=no, " +
            "status=no, " +
            "menubar=no, " +
            "scrollbars=yes, " +
            "resizable=yes, " +
            "width=790, " +
            "height=470 ";

    window.open("index.php?" +
            "sitio_id=" + sitio_id +
            "&menu_id=" + menu_id +
            "&objeto_id=" + objeto_id +
//              "&subobjeto_id="+subobjeto_id+
            "&popup=1" +
            "&tiene_flash=" + tiene_flash +
            "&tiene_svg=" + tiene_svg +
            content,
            "", opciones);
}

function abrirPopupDefiniciones() {
    var opciones = "toolbar=no, " +
            "location=no, " +
            "directories=no, " +
            "status=no, " +
            "menubar=no, " +
            "scrollbars=yes, " +
            "resizable=yes, " +
            "width=790, " +
            "height=580 ";

    window.open("definiciones.html", "", opciones);
}

function cerrarPopupItem() {
    window.close();
}

function setFuenteImagen(fuente, nombre) {
    fuente = fuente.replace(/\+/gi, "CARACTERSUMA");
    dojo.xhrPost({
        url: "imagen_grafico.php",
        sync: true,
        postData: "src=" + fuente + "&nombre=" + nombre,
        load: function (data) {
        }
    });
    return true;
}

function trim(myString) {
    return myString.replace(/^\s+/g, '').replace(/\s+$/g, '');
}

function verRegistro(mensaje, fecha, registro) {
    mensaje2 = htmlEncode(mensaje, false);
    contenido = '<table id="tabla_contenido_registro" width="380" class="listado_mini"><tr><th width="30%">Fecha</th><th width="70%"> Nombre Registro</th></tr>' +
            '<tr><td id="tabla_contenido_registro_fecha">' + fecha + '</td><td id="tabla_contenido_registro_registro">' + registro + '</td></tr>' +
            '<tr><td colspan="2" >' +
            '<div style="width: 360px; height:166px" id="conte_registro" style="">' +
            '<textarea rows="9" style="width:100%; height:100%" id="textRegistro"></textarea>' +
            '</div>' +
            '</td></tr></table>';

    if (!document.getElementById('dialog_contenido_registro')) {
        dojo.require("dijit.Dialog");
        myDialog = new dijit.Dialog({
            title: "Detalle Registro",
            content: contenido,
            id: 'dialog_contenido_registro',
            style: "width: 380px; height:248"
        });
    } else {
        document.getElementById("tabla_contenido_registro_fecha").innerHTML = fecha;
        document.getElementById("tabla_contenido_registro_registro").innerHTML = registro;
        dijit.byId('dialog_contenido_registro').attr("title", fecha + "     Valor Registro   " + registro);
    }

    document.getElementById('textRegistro').innerHTML = mensaje2;
    dijit.byId('dialog_contenido_registro').show();

}

function post_to_url(path, params, method) {

    method = method || "post"; // Set method to post by default, if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for (var key in params) {
        if (params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
        }
    }

    document.body.appendChild(form);
    form.submit();
}


// Variables para screenshot
var windowHeight = 0;
windowHeight = $(window).height();



$(".boton_elemento").live('click', function () {
    item_id = $(this).data("item_id");
    elemento_id = $(this).data("elemento_id");
    elemento_nombre = $(this).data("elemento_nombre");
    if(item_id == null){
        // Retorna false cuando sea el item disponibilidad: Downtime Global.\\
        return false;
    }
    cargarItem('contenedor_' + item_id, item_id, 1, [elemento_nombre, elemento_id]);
});


/* USO DE PAGINAS EN ESTADO GENERAL */
var pagina_actual = 0;
var pagina_ultima = 0;
var elementos_cantidad = 0;

function iniciarPaginacion(cantidad_default) {
    if ($('#flechaPL').length == 1) {
        if (cantidad_default > 0) {
            elementos_cantidad = cantidad_default;
        } else {
            elementos_cantidad = $("[name~='elemento_pagina']").length;
        }
        pagina_ultima = Math.floor((elementos_cantidad - 1) / 6);
        $("#elementos_total").html(elementos_cantidad);
        $("#elementos_scroll").animate({scrollLeft: '+=' + (pagina_actual * 516)}, 0);
        actualizarTextoPaginacion();
        actualizarFlechasPaginacion();
    }
}

function actualizarTextoPaginacion() {
    if (pagina_actual == pagina_ultima) {
        $("#elementos_mostrados").html(((pagina_actual * 6) + 1) + "-" + elementos_cantidad);
    } else {
        $("#elementos_mostrados").html(((pagina_actual * 6) + 1) + "-" + ((pagina_actual * 6) + 6));
    }
}

function actualizarFlechasPaginacion() {
    $('#flechaPL').attr("class", "spriteButton spriteButton-arrow2_left");
    $('#flechaPR').attr("class", "spriteButton spriteButton-arrow2_right");
    if (pagina_actual == 0) {
        $('#flechaPL').attr("class", "spriteButton spriteButton-arrow2_left_t");
    }
    if (pagina_actual == pagina_ultima) {
        $('#flechaPR').attr("class", "spriteButton spriteButton-arrow2_right_t");
    }
}


/*
# Muestra el detalle del evento.
*/

function mostrarDetalleEvento(id){
    elem = $('#seccion_mantenencion');
    seccion = elem.data('seccion');
    historial = elem.data('historial');
    mostrarSubmenu(seccion,historial,1,parseInt(id));
}


$('#bttPR').live("click", function () {
    if (pagina_actual < pagina_ultima) {
        pagina_actual = pagina_actual + 1;
        $("#elementos_scroll").animate({scrollLeft: '+=516'}, 200);
        actualizarTextoPaginacion();
        actualizarFlechasPaginacion();
    }
});

$('#bttPL').live("click", function () {
    if (pagina_actual > 0) {
        pagina_actual = pagina_actual - 1;
        $("#elementos_scroll").animate({scrollLeft: '-=516'}, 200);
        actualizarTextoPaginacion();
        actualizarFlechasPaginacion();
    }
});