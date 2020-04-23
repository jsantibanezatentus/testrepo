/*METODO QUE REALIZA UNA LLAMADA AJAX QUE TRAE LOS DATOS A DESCARGAR EL CSV*/
function getDataCsv(time_analysys) {
    
    var time = time_analysys.substr(0, 1);
    var return_data, dataparser;
   
    /*data del tpl*/
    var id_objetivo = $("#container").data("objetivo");
    var type_graphic = $("#tipo").data("tipo");
    var information = $("#load").data("informacion");
    var name_type = $("#load").data("nombre_tipo");
   
    
    $.ajax({
        async: false,
        // En data puedes utilizar un objeto JSON, un array o un query string
        data: {"id_objetivo": id_objetivo, "tiempo": time, "tipo_grafico": type_graphic, "nombre_tipo": name_type},
        //Cambiar a type: POST si necesario
        type: "POST",
        // Formato de datos que se espera en la respuesta
        dataType: "json",
        // URL a la que se enviará la solicitud Ajax
        url: "../datos_ajax.php",
        success: function (data) {
            dataparser = JSON.parse(data);
            return_data =setData(type_graphic, dataparser, time, information);
        }
    });
    return return_data;
}
/*funcion que setea los datos extraidos desde newrelic para la descarga csv*/
function setData(type_graphic, dataparser, time, information){
    var data_csv = new Object();
    var arr_axis_y = [];
    var arr_axis_x = [];
    var arr_name_elem = [];
    
    var label_name = new Array();
    var dat =new Array();
    var j = 0;
    var info = information.split(',');
    var name_url=null;
    
    if (type_graphic != 4 && type_graphic !=7 && type_graphic !=8) {
        
        $.each(dataparser.series, function (index, value) {
            $.each(value.data, function (index2, value2) {
                arr_name_elem.push(value.name);
                arr_axis_y.push(value2.y);
                arr_axis_x.push(value2.x);
            });
        });
    } else if(type_graphic == 4){
        $.each(dataparser.json.facets, function (index, value) {
            $.each(value.results, function (index2, value2) {
                arr_name_elem.push(value.name);
                arr_axis_y.push(value2.count);
            });
        });
    } else if(type_graphic == 7){

        $.each(dataparser.data_table.cols, function(i,val){
           label_name.push(val.label);
        });
        $.each(dataparser.data_table.rows, function(index, value){
            $.each(value.c, function(index2, value2){
                dat[j] = {"labelName":label_name[index2], "data":value2.f,"dateTime": new Date(value2.t).getTime(), "information":info[time] };
                j++;
            });
        });
    }
    else if(type_graphic == 8){

        $.each(dataparser.data.cols, function(i,val){
           label_name.push(val.label);
        });
        $.each(dataparser.data.rows, function(index, value){
            $.each(value.c, function(index2, value2){
                if(index2==0){
                    name_url = value2.v;
                }
                dat[j] = {"labelName":label_name[index2], "data":value2.v, "name_url":name_url, "information":info[time] };
                j++;
            });
        });
    }
    if(type_graphic != 7 && type_graphic !=8){
        data_csv.eje_y = arr_axis_y;
        data_csv.nombre_elementos = arr_name_elem;
        data_csv.informacion = info[time];
        if (type_graphic != 4) {
            data_csv.eje_x = arr_axis_x;
        }
        return data_csv;
    }
    else if (type_graphic == 7 || type_graphic ==8){
        return dat;
    }
}