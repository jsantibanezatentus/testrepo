/*
 * *** CODIGO CREADO POR: CARLOS SEPULVEDA *** *
 * *** FECHA CREACION 02-05-2016 *** *
 * 
 * FUNCION QUE PERMITE CAPTURAR LA DATA OBTENIDA DESDE DATOS_AJAX.PHP,
 * POSTERIORMENTE DISTRIBURUIRLAS EN UNA SERIE DE FUNCIONES QUE VALIDAN LA DATA Y LUEGO LA GRAFICAN*/
function show(time_analysys) {
    /*muestra el gif de carga y oculta el contenido*/
    $('.progress').show();
    $('#container').hide();
    $("#link").hide();
    $("#descripcion").hide();
    

    /*VARIABLES*/
    var n_chart = time_analysys.length;
    var time = time_analysys.substr(0, 1);
    var title = time_analysys.substr(1, n_chart);
    var dataparser = "";
    var validate_url = "";
    var has_data = null;
    var error_type = null;

    /*data del tpl*/
    var id_objetivo = $("#container").data("objetivo");
    var graphic_type = $("#tipo").data("tipo");
    var information = $("#load").data("informacion");
    var arr_link = $("#load").data("link");
    var name_type = $("#load").data("nombre_tipo");
    var loading = '<table width="100%" align="center"><tr><td style="background-color:#ffffff" width="35%">&nbsp;</td><td width="30" align="center" style="background-color:#ffffff"><img src="img/cargando.gif"></td><td class="textgris12" style="background-color:#ffffff">Por favor espere.<br>El reporte se esta generando.</td><td style="background-color:#ffffff">&nbsp;</td></tr></table>';
    var info = information.split(',');
    var link = arr_link.split(',');

    /*agregar contenido en respectivos div*/
    $("#load").html(loading);

    /*MANEJO DE ERRORES PARA CUANDO NO ESTE CARGADO EL ELEMENTO*/
    try {
        $("#descripcion").html(info[time]);
        $("#link").html(link[time]);
        $("#link").attr("href", 'http://' + link[time]);
    } catch (e) {
    }
    /*llamada ajax que trae los datos desde el sitio*/
    $.ajax({
        async: true,
        // En data puedes utilizar un objeto JSON, un array o un query string
        data: {"id_objetivo": id_objetivo, "tiempo": time, "tipo_grafico": graphic_type, "nombre_tipo": name_type},
        //Cambiar a type: POST si necesario
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
                
                /*VALIDA QUE LOS DATOS DEVUELTOS DE LA LLAMADA AJAX PUEDAN CONVERTIRSE EN JSON*/
                if (validateJson(data) == true) {
                    error_type = true;
                    dataparser = JSON.parse(data);
                    validate_url = validateUrl(dataparser, graphic_type);
                    /*VALILDA QUE LA URL SEA CONSISTENTE CON EL TIPO DE GRAFICO*/
                    if (validate_url == true && data != false) {
                        has_data = existsData(dataparser, graphic_type);
                        /*VALIDA QUE CONTENGA DATOS*/
                        if (has_data == true) {
                            $("#link").show();
                            $("#descripcion").show();
                            /*PARA TODOS LOS GRAFICOS A EXCEPCION al DE TORTA Y CARGA PROMEDIO POR BROWSER*/
                            if ((graphic_type != 4) && (graphic_type != 7 && (graphic_type !=8))) {
                                getGraphics(title, dataparser, graphic_type);
                                /*PARA LOS GRAFICOS TIPO PIE*/
                            } else if (graphic_type == 4) {
                                var arr_data = [];
                                var name_time = "";
                                var sum_values = 0;
                                name_time = dataparser.facet;

                                /*recorre el objeto guardando variables necesarias para la creacion del grafico*/
                                $.each(dataparser.json.facets, function (index, value) {
                                    var dict = {};
                                    /*captura el nombre*/
                                    dict['name'] = (value.name);

                                    $.each(value.results, function (index2, value2) {
                                        /*captura el contador*/
                                        sum_values = value2.count + sum_values;
                                        dict['y'] = (value2.count);
                                    });
                                    arr_data.push(dict);
                                });
                                // FUNCION QUE GENERA EL GRAFICO 
                                getPie(sum_values, name_time, arr_data, title);
                            } else if (graphic_type == 7) {
                                getGraphicAverageBrowser(title, dataparser, graphic_type);
                            }
                            else if (graphic_type == 8) {
                                getLoadUrl(title, dataparser, graphic_type);
                            }
                        }
                        /*else tiene datos*/
                        else {
                            error_type = false;
                            errorData(error_type);
                        }
                    }
                    /*else url sea consistente con el tipo de grafico*/
                    else {
                        errorData(error_type);
                    }
                }
                /*else de validacion de json*/
                else {
                    errorData(error_type);
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                errorData(error_type);
            });

}

/*FUNCION QUE GRAFICO PIE Id(4)*/
function getPie(sum_values, name_time, arr_data, title) {
    //total de la suma de los valores
    var suma = parseFloat(sum_values);

    $(function () {

        $('#container').highcharts({
            /*CARACTERISTICAS DEL GRAFICO*/
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false,
                height: 300,
                renderTo: 'container'
            },
            credits: {
                enabled: false
            },
            title: {
                text: title
            },
            subtitle: {
                text: '<b>' + suma.toFixed(2) + '<b><br>' + 'HTTP<br>CÓDIGOS<br>RESPUESTA',
                align: 'center',
                verticalAlign: 'middle',
                y: -20
            },
            /*TOOLTIP QUE DESPLIEGA AL MOVER EL RATON POR ENCIMA DEL DATO*/
            tooltip: {
                pointFormat: '{point.y:.2f} <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        distance: 10,
                        enabled: true,
                        style: {
                             color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    startAngle: 0,
                    endAngle: 360,
                    center: ['50%', '50%']
                }
            },
            /*SERIES SE ENCUENTRAN LOS DATOS DEL GRAFICO*/
            series: [{
                    type: 'pie',
                    name: 'Tipo de errores',
                    innerSize: '60%',
                    data: arr_data
                }
            ]
        });
    });
    var chart = $('#container').highcharts();
    chart.setTitle({fontFamily: "'Verdana, Arial', sans-serif", fontSize: "15px"});
}
/*FUNCION QUE GENERA GRAFICOS tiempo de respuesta, tasa errores, apdex puntuacion, Tiempo de Carga de Pagina en navegador Id(1,2,3)*/
function getGraphics(title, dataparser, graphic_type) {
    var show_label = true;
    var from = new Array();
    var to = new Array();
    var colour = new Array();
    var val_apdex = new Array();
    var date = null;
    var date_apdex = null;

    /*ARREGLOS QUE CONTENDRAN CADA UNA DE LAS LINEAS DEL GRAFICO*/
    try {
        var tiene_apdex = dataparser.xAxis.plotBands[0].from;
        var cantidad_plotBands = dataparser.xAxis.plotBands.length;
    } catch (e) {
        show_label = false;
    }
    if (cantidad_plotBands != undefined) {
        for (var j = 0; j < cantidad_plotBands; j++) {
            from.push(dataparser.xAxis.plotBands[j].from);
            to.push(dataparser.xAxis.plotBands[j].to);
            colour.push(dataparser.xAxis.plotBands[j].color);
            //val_apdex[j]=dataparser.xAxis.plotBands[j].nrTooltip;
            
        }
    }
    val_apdex=["Apdex score < 0.7","Error rate > 5.0%"];

    var seriesname = [];
    /*variables de cambio (ingles-español)*/
    var replacetooltip = ["a", "desde", "Tiempo de Respuesta ", "Tiempo usado en ", "Procesar DOM", "Renderizacion de paginas", "Aplicacion Web", "Red", "minuto", "hora", "horas", "dias", "de", "por solicitud ", "transacciones", "seg", "lanzamientos", "lanzamiento", "Ocurrencias"];
    var replace = ["to", "from", "Response time", "Time spent in", "Page rendering", "DOM processing", "Web application", "Network", "minute", "hour", "hours", "days", "of", "per request", "transactions", "sec", "launches", "launch", "Occurrences"];

    var replacetooltip2 = ["a", "Tasa de error", "Errores por minuto", "Solicitud por minuto"];
    var replace2 = ["to", "Error rate", "Errors per minute", "Requests per minute"];

    var replacetooltip3 = ["Aceptable", "Excelente", "Tamaño muestra", "Satisfecho", "Tolerado", "Frustrado", "Bajo", "Bueno", "Inaceptable"];
    var replace3 = ["Fair", "Excellent", "Sample Size", "Satisfied", "Tolerating", "Frustrated", "Poor", "Good", "Unacceptable"];
    
    var replacetooltip4 = ["a","minuto de", "Porcentaje Error", "Errores por minuto" ];
    var replace4 = ["to", "minute from",  "Error percentage", "Errors per minute"];
    
    var replacetooltip5 = ["a","Tiempo de Respuesta","minuto de", "seg"];
    var replace5 = ["to","ResponseTime", "minute from",  "sec"];
    //grafico highchart
    var cant_metrics = dataparser.series.length;
    var tooltip_metric = new Array(cant_metrics + 1);
    $(function () {
        //setea la fecha 
        Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });

        //Configuracion del grafico
        $('#container').highcharts({
            chart: dataparser.chart,
            credits: {
                enabled: false
            },
            title: {
                text: title
            },
            subtitle: {
                text: '',
                align: 'right',
                x: -5,
                y: 50,
                floating: true,
                useHTML: true
            },
            tooltip: {
                //formatea el tooltip segun donde se encuentre posicionado el puntero dentro del cuadro diferenciados por cada tipo_de graficos

                formatter: function () {

                    //recorriendo los datos
                    $.each(dataparser.series, function (index2, value2) {
                        seriesname.push(value2.name);
                        tooltip_metric[index2] = new Array(cant_metrics + 1);
                        $.each(value2.data, function (index3, value3) {
                            var str = "";
                            str = String(value3.tooltip);
                            
                            /*REEMPLAZAR TEXTO EN INGLES SEGUN EL TIPO DE GRAFICO*/
                            /*GRAFICO DE TIEMPO DE RESPUESTA*/
                            if (graphic_type == 1 || graphic_type == 11 || graphic_type == 12 || graphic_type == 13 || graphic_type == 14 || graphic_type == 15 || graphic_type == 16 || graphic_type == 17) {
                                //reemplaza palabras  ingles por español
                                for (var i = 0; i < replacetooltip.length; i++) {
                                    str = str.replace(replace[i], replacetooltip[i]);
                                }
                                //generar salto de linea dentro del  tooltip, 
                                //Indice es la posicion en donde se encuentra un determinado caracter

                                indice = str.indexOf('</strong>');
                                if(graphic_type !=12 && graphic_type !=13 && graphic_type !=17 && graphic_type !=14 && graphic_type !=16){
                                    //str = str.substr(0, indice) + "  <br/>" + str.substr(indice);
                                    change_tooltip = str.indexOf('dias');
                                    if (change_tooltip == -1) {
                                         indice2 = str.indexOf(":");
                                        indice2 = indice2 + 11;
                                        str = str.substr(0, indice2) + " <br/>" + str.substr(indice2 + 1);
                                        indice3 = str.indexOf("por solicitud");
                                        indice3 = indice3 + 13;
                                        str = str.substr(0, indice3) + " <br/>" + str.substr(indice3 + 1);
                                    } else {
                                        indice2 = change_tooltip - 2;
                                        str = str.substr(0, indice2) + " <br/>" + str.substr(indice2 - 1);
                                        indice3 = str.indexOf(":");
                                        indice3 = indice3 + 17;
                                        str = str.substr(0, indice3) + " <br/>" + str.substr(indice3 + 1);
                                        indice4 = str.indexOf("por solicitud");
                                        indice4 = indice4 + 13;
                                        str = str.substr(0, indice4) + " <br/>" + str.substr(indice4 + 1);

                                    }
                                }
                                if(graphic_type ==13 ){
                                    str = str.substr(0, indice) + " <br/>" + str.substr(indice);
                                    indice2 = str.indexOf("Avg");
                                    str = str.substr(0, indice2) + " <br/>" + str.substr(indice2);
                                }
                                if(graphic_type ==14 || graphic_type ==15 || graphic_type ==16 || graphic_type ==17){
                                    str = str.substr(0, indice) + " <br/>" + str.substr(indice);
                                    //indice2 = str.indexOf("Avg");
                                    //str = str.substr(0, indice2) + " <br/>" + str.substr(indice2);
                                }
                                /*GUARDA LOS DATOS */

                                tooltip_metric[index2][index3] = str;
                            }
                            /*GRAFICO ERROR RATE*/
                            else if (graphic_type == 2) {
                                //reemplaza palabras en ingles por español
                                for (var i = 0; i < replacetooltip2.length; i++) {
                                    str = str.replace(replace2[i], replacetooltip2[i]);
                                }
                                //generar salto de linea dentro del  tooltip
                                n = str.indexOf("T");
                                str = str.substr(0, n) + " <br/>" + str.substr(n);
                                n2 = str.indexOf("%");
                                str = str.substr(0, n2) + "%<br/>" + str.substr(n2 + 2);
                                n3 = str.indexOf("S");
                                str = str.substr(0, n3) + " <br/>" + str.substr(n3);
                                /*GUARDA LOS DATOS */
                                tooltip_metric[index2][index3] = str;
                            }
                            /*GRAFICO APDEX*/
                            else if (graphic_type == 3) {
                                //reemplaza palabras en ingles por español
                                for (var i = 0; i < replacetooltip3.length; i++) {
                                    str = str.replace(replace3[i], replacetooltip3[i]);
                                }
                                //generar salto de linea dentro del  tooltip
                                n = str.indexOf("T");
                                str = str.substr(0, n) + " <br/>" + str.substr(n);
                                n2 = str.indexOf("S");
                                str = str.substr(0, n2) + "<br/>" + str.substr(n2);
                                n3 = str.indexOf("To");
                                str = str.substr(0, n3) + " <br/>" + str.substr(n3);
                                n4 = str.indexOf("Fr");
                                str = str.substr(0, n4) + " <br/>" + str.substr(n4);
                                
                                tooltip_metric[index2][index3] = str;
                            }
                            /*error rate js*/
                            else if (graphic_type == 9) {
                                //reemplaza palabras en ingles por español
                                for (var i = 0; i < replacetooltip4.length; i++) {
                                    str = str.replace(replace4[i], replacetooltip4[i]);
                                }
                                //generar salto de linea dentro del  tooltip
                                n = str.indexOf("P");
                                str = str.substr(0, n) + " <br/>" + str.substr(n-1);
                                n2 = str.indexOf("%");
                                str = str.substr(0, n2) + " %<br/>" + str.substr(n2+1);
                                
                                tooltip_metric[index2][index3] = str;
                            }
                             /*Tiempo respuesta Ajax*/
                            else if (graphic_type == 10) {
                                //reemplaza palabras en ingles por español
                                for (var i = 0; i < replacetooltip5.length; i++) {
                                    str = str.replace(replace5[i], replacetooltip5[i]);
                                }
                                //generar salto de linea dentro del  tooltip
                                n = str.indexOf('</strong>');
                                str = str.substr(0, n) + " <br/>" + str.substr(n);
                                n2 = str.indexOf(':');
                                str = str.substr(0,n2+12) + "<br/> " + str.substr(n2+12);
                                
                                
                                tooltip_metric[index2][index3] = str;
                            }
                        });
                    });
                    /*CONDICIONES PARA MOSTRAR LOS DATOS EN SUS LINEAS RESPECTIVAS (TOOLTIP)*/
                    for (var i = 0; i < cant_metrics; i++) {
                        if (this.series.name == seriesname[i]) {
                            if(graphic_type==3){
                                date = setDate(new Date(this.x));
                                date_apdex = date.date+"-"+date.month+"-"+date.year+" "+date.hours+":"+date.minutes+":"+date.seconds;
                                return tooltip_metric[i][this.series.data.indexOf(this.point)]+"<br/>" +date_apdex;
                            }
                            return tooltip_metric[i][this.series.data.indexOf(this.point)];
                          
                        }
                    }
                },
                backgroundColor: 'rgba(255, 255, 255, 0.85)',
                borderRadius: 0,
                borderWidth: 1,
                hideDelay: 0,
                style: {
                    color: '#555555'
                },
                crosshairs: [
                    {
                        width: 1,
                        color: '#888888',
                        zIndex: 5
                    },
                    null
                ]
            },
            /*EJES*/
            xAxis: dataparser.xAxis,
            yAxis: dataparser.yAxis,
            plotOptions: dataparser.plotOptions,
            dateFormat: dataparser.dateFormat,
            colors: dataparser.colors,
            /*CONTIENE LOS DATOS DEL GRAFICO*/
            series: dataparser.series
        });
    });
    /*Dar estilo al titulo*/
    var chart = $('#container').highcharts();
    chart.setTitle({fontFamily: "'Verdana, Arial', sans-serif", fontSize: "15px"});
    chart.xAxis[0].update({
            labels: {
                enabled: true
            }
        });
    /*SOlo para el grafico tiempo respuesta genera label para el apdex score*/
    if (graphic_type == 1 || graphic_type == 2 || graphic_type == 9 || graphic_type == 10 || graphic_type == 11) {
        if (show_label == true) {
            for (var j = 0; j < cantidad_plotBands; j++) {
                /*Agrega el plotBand */
                chart.xAxis[0].addPlotBand({
                    "color": colour[j],
                    "from": from[j],
                    "to": to[j],
                    /*Eventos que muestran el label segun el PlotBand*/
                    events: {
                        mouseover: function () {
                            var chart = $('#container').highcharts();
                            chart.setTitle(null, {text: '<label style="background-color:white; border-radius:1px; border: 2px rgba(238, 51, 68, 0.40) solid;">'+val_apdex[0]+'</label>'});
                        },
                        mouseout: function () {
                            var chart = $('#container').highcharts();
                            chart.setTitle(null, {text: ''});
                        }
                    }
                });
            }
        }
    }
}
/*FUNCION QUE GENERA GRAFICO CARGA PROMEDIO POR BROWSER Id(7)*/
function getGraphicAverageBrowser(title, dataparser, graphic_type) {

    var time = Array();
    var value = Array();
    var total = Array();
    var data_series = Array();
    var name_series = Array();
    var n_series = dataparser.data_table.cols.length;
    var n_values = dataparser.data_table.rows.length;
    var date;
    var fecha;
    
    /*obtiene los valores  del objeto daparser*/
    for (var i=1; i<n_series;i++){

        time=[];
        value=[];
        data_series=[];
        name_series.push(dataparser.data_table.cols[i].label);
        for(var j=0;j<n_values;j++){
            
            value[j] = parseFloat(dataparser.data_table.rows[j].c[i].f);
            time[j] = new Date(dataparser.data_table.rows[j].c[i].t).getTime();
            data_series[j]=[time[j],value[j]];
        }
        total[i]=data_series;
    }
    
    $(function () {
        Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });
        $('#container').highcharts({
            title: {
                text: title,
                x: -20 //center
            },
            credits: {
                enabled: false
            },
            subtitle: {
                text: '',
                x: -20
            },
            xAxis: {
                 type: 'datetime'
            },
            yAxis: {
                title: {
                    text: 'Segundos',
                    y:5
                }
            },
            plotOptions: {
                series: {
                    marker: {
                        enabled: false
                    }
                }
            },
            tooltip: {
                formatter: function () {
                    date = new Date(this.x);
                    fecha=setDate(date);
                    return '<b>' + this.series.name + ': </b>'+this.y+" seg."+'<br/>' +
                        fecha.date+"-"+fecha.month+"-"+fecha.year+"  "+fecha.hours+":"+fecha.minutes+":"+fecha.seconds;
                }
            },
            legend: {
                layout: 'horizontal',
                backgroundColor: '#FFFFFF',
                floating: false,
                align: 'center',
                x: 0,
                verticalAlign: 'bottom',
                y: 0
            },
            series: [{}]
        });
    });
    addSeries(total, name_series, n_series);
}
/*FUNCION QUE GENERA GRAFICO CARGA PROMEDIO POR BROWSER Id(7)*/
function getLoadUrl(title, dataparser, graphic_type) {
    
    var value = Array();
    var data_series = Array();
    var name_yaxis = Array();
    var name_xaxis = Array();
    var name_yaxis_short;
    var n_values = dataparser.data.rows[0].c.length;
    var n_series = dataparser.data.rows.length;
    var h=0;
    
    /*almacenar valores del dataparser*/
    for (var i=0; i<n_series;i++){
        value=[];
        name_yaxis.push(dataparser.data.rows[i].c[0].v);
       
        for(var j=1;j<n_values;j++){
            if(i==0){
                
                name_xaxis.push((dataparser.data.cols[j].label).replace("to", "a")); 
            }
            value[j] = dataparser.data.rows[i].c[j].v;
            data_series[h]=[j-1,i,value[j]];
            h++;
        }
    }
    name_yaxis_short = shortText(name_yaxis);
    $(function () {
        $('#container').highcharts({

            chart: {
                type: 'heatmap',
                marginTop: 40,
                marginBottom: 80,
                plotBorderWidth: 1
            },
            title: {
                text: title
            },
            credits: {
                enabled: false
            },

            xAxis: {
                categories: name_xaxis
            },

            yAxis: {
                categories: name_yaxis_short,
                title: null
            },

            colorAxis: {
                min: 0,
                minColor: '#FFFFFF',
                maxColor: Highcharts.getOptions().colors[0]
            },

            legend: {
                align: 'right',
                layout: 'vertical',
                margin: 0,
                verticalAlign: 'top',
                y: 25,
                symbolHeight: 280
            },

            tooltip: {
                formatter: function () {
                    return '<b>' + this.series.xAxis.categories[this.point.x] + '</b> seg <br><b>' +
                        this.point.value + '</b> Paginas visitadas <br><b>' + name_yaxis[this.point.y] + '</b>';
                }
            },

            series: [{
                name: '',
                borderWidth: 1,
                data: data_series,
                dataLabels: {
                    enabled: false,
                    color: '#000000'
                }
            }]
        });
    });
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

/*FUNCION QUE VALIDA CASOS DE BORDE*/
function validateUrl(dataparser, graphic_type) {
    var graphic_series_type = "";
    var existe_obj = true;
    var return_validate = false;
    var es_Pie = false;
    var exists_data_table = true;
    
    try {
        var type = dataparser.tab_title;
        if (type == "Pie Chart") {
            es_Pie = true;
        }
    } catch (e) {
        es_Pie = false;
    }

    try {
        var graphic_series_type = dataparser.chart.defaultSeriesType;
    } catch (e) {
        existe_obj = false;
    }
    try {
        var  tiene_data_table= dataparser.data_table.cols;
        
    } catch (e) {
        exists_data_table = false;
    }
    /*TIEMPO DE RESPUESTA  tiempo carga pagina en navegador*/
    if (graphic_type == 1 || graphic_type == 11 || graphic_type == 14 || graphic_type == 16) {
        if (existe_obj == true) {
            if (graphic_series_type == 'area') {
                return_validate = true;
            }
            return_validate = true;
        }
       
    } 
      /*Promedio interaccion con app*/
    if (graphic_type == 13 || graphic_type == 17 || graphic_type == 15) {
        if (existe_obj == true) {
            if (graphic_series_type == 'line') {
                return_validate = true;
            }
        }
    }
     /*TASA DE ERRORES*/
    else if (graphic_type == 2 || graphic_type == 9 || graphic_type == 10) {
        if (existe_obj == true) {
            if (graphic_series_type == 'line') {
                if (dataparser.series.length != 2) {
                    return_validate = true;
                }
            }
        }
        /*APDEX PUNTUACION*/
    } else if (graphic_type == 3) {
        if (existe_obj == true) {
            if (graphic_series_type == 'line') {
                if (dataparser.series.length != 1) {
                    return_validate = true;
                }
            }
        }
        /*TIPO DE ERRORES*/
    } else if (graphic_type == 4) {
        if (existe_obj == false) {
            if (es_Pie == true) {
                return_validate = true;
            }
        }
       /* TIEMPO DE RESPUESTA BROWSER */
    }else if (graphic_type == 7) {
        if (existe_obj == false) {
            if (es_Pie == false) {
                if (exists_data_table == true){
                    return_validate = true;
                }
            }
        }
        return_validate = true;
    }
    else if (graphic_type == 8) {
        return_validate = true;
    }
    return return_validate;
}
/*FUNCION QUE VALIDA SI CONTIENE DATA*/
function existsData(dataparser, graphic_type) {
    var return_validate = false;
    var n_adata = 0;
    var n_data_browser = 0;
    var n_data_load = 0;
    try {
        n_adata = dataparser.series.length;
    } catch (e) {
        n_adata = 0;
    }
    try {
        n_data_browser = (dataparser.data_table.cols).length;
    }catch (e){
        n_data_browser = 0;
    }
    try {
        n_data_load = (dataparser.data.rows).length;
    }catch (e){
        n_data_load = 0;
    }
    if (graphic_type == 4) {
        return_validate = true;
    }
    /*TIEMPO DE RESPUESTA, TASA DE ERRORES, APDEX, TIEMPO RESPUESTA BROWSER TIEMPO DE CARGA POR PAGINA*/
    else if ((graphic_type == 1) || (graphic_type == 2) || (graphic_type == 3) || graphic_type == 9 || graphic_type == 10 || graphic_type == 11 || graphic_type == 12 || graphic_type == 13 || graphic_type == 14 || graphic_type == 15 || graphic_type == 16 || graphic_type == 17) {
        if (n_adata != 0) {
            return_validate = true;
        }
    }
    else if(graphic_type == 7){
        if(n_data_browser > 1){
            return_validate = true;
        }
    }
    else if(graphic_type == 8){
        if(n_data_load >= 1){
            return_validate = true;
        }
    }
    return return_validate;
    
}
/*FUNCION QUE OBTIENE Y POSTERIORMENTE GENERA EL TEMPLATE DE ERROR*/
function errorData(error_type) {
$("#imagen_descargar").css("display", "none");
    $.ajax({
        async: false,
        data: {"tipo_error": error_type},
        url: '../error_grafico.php',
        dataType: 'html',
        type: 'POST'
    })
            .done(function (data) {
                //llenamos el div "resultado" con lo obtenido de error_grafico.php
                $('#container').html(data);
            });
}
/*Agrega las series para el grafico de id 7*/
function addSeries(total, name_series, n_series){
    var chart = $('#container').highcharts();
    chart.series[0].remove();
    for(var i=1;i<n_series;i++){
        chart.addSeries({
            name:name_series[i-1],
            data:total[i]
        });
    }
}
/*setear fecha*/
function setDate(date){
    var fecha = new Object();
    fecha.month = ((String(date.getMonth() + 1)).length==1)?"0"+(date.getMonth() + 1):(date.getMonth() + 1);
    fecha.date = (String(date.getDate()).length==1)?"0"+date.getDate():date.getDate();
    fecha.year = date.getFullYear();
    fecha.hours = (String(date.getHours()).length==1)?"0"+date.getHours():date.getHours();
    fecha.minutes = (String(date.getMinutes()).length==1)?"0"+date.getMinutes():date.getMinutes();
    fecha.seconds = (String(date.getSeconds()).length==1)?"0"+date.getSeconds():date.getSeconds();
    return fecha;
}
/*abreviar cadena de texto*/
function shortText(arr_name) {
    var arr_text = new Array(), arr_tmp = new Array ();
    var str = "", indx=null,txt="";
    $.each(arr_name, function( index, value ) {
        indx = value.indexOf('//');
        txt=value.substring(0,indx+2);
        arr_tmp = value.split('//')[1].split('/');
        arr_text[index]=txt+arr_tmp[0]+'/'+arr_tmp[1].substring(0,3)+'...';
    });
    return arr_text;
}
