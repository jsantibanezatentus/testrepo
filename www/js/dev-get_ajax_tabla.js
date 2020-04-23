/*
 * *** CODIGO CREADO POR: CARLOS SEPULVEDA *** *
 * *** FECHA CREACION 02-05-2016 *** *
 * 
 * FUNCION QUE PERMITE CAPTURAR LA DATA OBTENIDA DESDE DATOS_AJAX.PHP,
 * POSTERIORMENTE DISTRIBURUIRLAS EN UNA SERIE DE FUNCIONES QUE VALIDAN LA DATA Y LUEGO LA GRAFICAN (TABLA IFRAME)
*/

function show(time_analysys) {
    /*muestra el gif de carga y oculta el contenido*/
    $('.progress').show();
    $('#container').hide();
    $("#link").hide();
    $("#descripcion").hide();
    $('#titulo').hide();
    /*VARIABLES*/
    var n_chart = time_analysys.length;
    var time = time_analysys.substr(0, 1);
    title = time_analysys.substr(1, n_chart);
    var fil_name = [];
    var fil_value = [];
    var type_error = null;
    var validate_url = null;
    var has_data = null;

    /*datos del tpl*/
    var id_objetivo = $("#container").data("objetivo");
    var type_graphic = $("#tipo").data("tipo");

    var information = $("#load").data("informacion");
    var arr_link = $("#load").data("link");
    var name_type = $("#load").data("nombre_tipo");
    var link = arr_link.split(',');
    var info = information.split(',');
    /*MANEJO DE ERRORES PARA CUANDO NO ESTE CARGADO EL ELEMENTO*/
    
    try {
        $("#descripcion").html(info[time]);
        $("#link").html(link[time]);
        $("#link").attr("href", 'http://'+link[time]);
    } catch (e) {}
    
    /*CONTENEDOR DEL LOADING*/
    var loading = '<table width="100%" align="center"><tr><td style="background-color:#ffffff" width="35%">&nbsp;</td><td width="30" align="center" style="background-color:#ffffff"><img src="img/cargando.gif"></td><td class="textgris12" style="background-color:#ffffff">Por favor espere.<br>El reporte se esta generando.</td><td style="background-color:#ffffff">&nbsp;</td></tr></table>';
    $("#load").html(loading);
    
    /*CONDICION PARA LLAMAR A FUNCION QUE GENERA LOS IFRAME*/
    if (type_graphic == 6) {

        $.ajax({
            // En data puedes utilizar un objeto JSON, un array o un query string
            data: {"id_objetivo": id_objetivo, "tiempo": time, "tipo_grafico": type_graphic, "nombre_tipo": name_type},
            //Cambiar a type: GET si es necesario
            type: "POST",
            // Formato de datos que se espera en la respuesta
            dataType: "text",
            // URL a la que se enviará la solicitud Ajax
            url: "../datos_ajax.php",
            success: function (data) {
                getIframe(data);
                $("#load").delay(1000).fadeOut(100);
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
                    errorData(type_error);
                });
    }
    /* TRAE DATOS AJAX DE LA TABLA*/
    else {

        //peticion ajax
        $.ajax({
            // En data puedes utilizar un objeto JSON, un array o un query string
            data: {"id_objetivo": id_objetivo, "tiempo": time, "tipo_grafico": type_graphic, "nombre_tipo": name_type},
            //Cambiar a type: GET si es necesario
            type: "POST",
            // Formato de datos que se espera en la respuesta
            dataType: "json",
            // URL a la que se enviará la solicitud Ajax
            url: "../datos_ajax.php",
            success: function (data) {
                $("#load").fadeOut(100);
                $("#container").fadeIn(100);
                
            }
            
        })
                //Si la peticion se realizo correctamente
                .done(function (data, textStatus, jqXHR) {
                    type_error = true;
                    /*valida que los datos se puedan parsear*/
                    if (validateJson(data) == true) {

                        var dataparser = JSON.parse(data);
                        var count = 0;
                                                
                        validate_url = validarUrl(dataparser, type_graphic);
                        /*valida que la url se encuentre correcta*/
                        if (validate_url == true) {
                            has_data = tieneData(dataparser, type_graphic);
                            /*valida que contenga datos*/
                            if (has_data == true) {
                                $("#link").show();
                                $("#descripcion").show();

                                $(function () {
                                    /*recorre el objeto*/
                                    $.each(dataparser.facets, function (index, value) {
                                        fil_name.push(value.name);
                                        count++;
                                        $.each(value.results, function (index2, value2) {
                                            fil_value.push(value2);
                                        });
                                    });
                                    /*generar la tabla*/
                                    getTable(fil_name, fil_value, count);
                                });
                            }
                            /*ELSE SIN DATOS*/
                            else {
                                type_error = false;
                                errorData(type_error);
                            }
                            /*ELSE VALIDACION DE LA URL*/
                        } else {
                            errorData(type_error);
                        }
                        /*ELSE DE VALIDACION DEL JSON*/
                    } else {
                        errorData(type_error);
                    }


                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    errorData(type_error);
                });
    }
}
/*FUNCION QUE VALIDA SI LA VARIABLE DEVUELTA SE PUDO  PARSEAR CORRECTAMENTE EN JSON*/
function validateJson(data) {
    try {
        var dataparser = JSON.parse(data);
    } catch (e) {

        return false;
    }
    return true;
}
/*FUNCION QUE VALIDA CASOS DE BORDES*/
function validarUrl(dataparser, type_graphic) {
    var is_table = false;
    var retorno = false;
    var type = "";
    try {
        type = dataparser.tab_title;
        if (type == "Table") {
            var is_table = true;
        }
    } catch (e) {
        is_table = false;

    }

    /*Tiempo Respuesta mas elevado*/
    if (type_graphic == 5) {
        if (is_table == true) {

            retorno = true;
        }
    }
    return retorno;

}
/*FUNCION QUE VALIDA SI TRAE DATA*/
function tieneData(dataparser, type_graphic) {

    var retorno = false;
    try {
        var n_data = dataparser.facets.length;
    } catch (e) {
    }

    if (type_graphic == 5) {
        if (n_data != 0) {
            retorno = true;
        }
    }

    return retorno;
}
/*FUNCION QUE OBTIENE EL TEMPLATE DE ERROR*/
function errorData(type_error) {
    $("#imagen_descargar").css("display", "none");
    $.ajax({
        async: false,
        data: {"tipo_error": type_error},
        url: '../error_grafico.php',
        dataType: 'html',
        type: 'POST'
    })
            .done(function (data) {
                //llenamos el div "resultado" con lo obtenido de error_grafico.php
                $('#container').html(data);
            });
}

/*FUNCION QUE GENERA TABLA*/
function getTable(fil_name, fil_value, count) {
    $("#titulo").html(title);
    var content = '<br><table width="90%"> <td class="celdaborde celdanegra40">Nombre</td><td class="celdaborde celdanegra40">Duracion AVG</td>';
    for (var i = 0; i < count; i++) {
        content += '<tr><td class="celdaborde celdanegra10">' + fil_name[i] + '</td><td class="celdaborde celdanegra10">' + fil_value[i] + '</td></tr>';
    }
    content += "</table>";
   
    if ($('#selector_tiempo').length){
       $('#titulo').css({
            'float' : 'right'
        });
    }
    $("#container").html(content);
    $("#titulo").show();
   
    /*agrega una nueva tabla*/
    //$('#container').append(content);
}
/*FUNCION QUE GENERA IFRAME*/
function getIframe(url) {
    var url_ok=true;
    try {
        var content = '<iframe src="' + url + '" width="95%" height="400" scrolling="no" frameborder="no"></iframe>';
    }
    catch(e){
        url_ok=false;
    }
    if( url_ok==true){
        $("#container").html(content);
        $("#container").fadeIn(100);
        $("#link").show();
        $("#descripcion").show();
    }
    else{
        $("#imagen_descargar").css("display", "none");
        errorData(true);
    }
}