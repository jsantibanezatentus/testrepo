<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Atentus.com: Reportes</title>
        <script type="text/javascript" src="../../js/{__get_ajax_tipo}.js"></script>
        <script type="text/javascript" src="../../js/get_ajax_csv.js"></script>
        <script type="text/javascript" src="{__heatmap}"></script>
        
        <script type="text/javascript">
            /*ocultar botones para imprimir informes y mostrar boton descarga y actualizar*/
            $("#load").css("display", "none");
            $("#imprimirInforme").css("display", "none");
            $("#imprimirTodos").css("display", "none");
            $("#img_update").css("display", "table-cell");
            $("#descargar_csv").css("display", "table-cell");
        
            /*Mostrar boton de descarga csv*/
            if ({__tipo_grafico} == 5 || {__tipo_grafico} == 6) {
                $("#imagen_descargar").css("display", "none");
            }

            /*DEPENDE SI TRAE O NO MENU DESPLEGABLE*/
            if (({__contador}) == 1) {
                getGraphic("0{__titulo}");
            } else {
                
                /*AL CARGAR POR PRIMERA VEZ EL MENU DESPLEGABLE VALOR POR DEFECTO*/
                position = $("#seleccion")[0].selectedIndex; //posicion
                time = (document.getElementById('seleccion').options[position].text);
                getGraphic("0" + time);
            }
            /*MUESTRA GRAFICOS O TABLA*/
            function getGraphic(time) {
                show(time);
            }
            /*ACTUALLIZA GRAFICO O TABLA*/
            function update() {
                var time_update = 0;
                // comprueba si existe el menu de seleccion en el dom
                if ($('#seleccion').length) {
                    pos = $("#seleccion")[0].selectedIndex; //posicion
                    tiemp = (document.getElementById('seleccion').options[pos].text);
                    time_update = pos + tiemp;
                } else
                {
                    time_update = "0{__titulo}";
                }
                show(time_update);
            }
            function downloadCsv() {
                var time_down = "", data, json, data, pos, tiemp;
                
                if ($('#seleccion').length) {
                    pos = $("#seleccion")[0].selectedIndex;  //posicion

                    tiemp = (document.getElementById('seleccion').options[pos].text);
                    time_down = pos + tiemp;
                } else
                {
                    time_down = "0{__titulo}";
                }
                data = getDataCsv(time_down);
                json = JSON.stringify(data);
                    /*CREANDO EL FORMULARIO*/
                    $('<form>').attr({
                        type: 'hidden',
                        id: 'form',
                        name: 'form',
                        action: 'descarga_csv.php',
                        method: 'POST',
                        target: '_self'
                    }).appendTo('body');

                    $('#form').hide();
                    $('<input>').attr({
                        type: 'text',
                        id: 'datos',
                        name: 'datos',
                        value: json
                    }).appendTo('#form');

                    $('<input>').attr({
                        type: 'text',
                        id: 'id_objetivo',
                        name: 'id_objetivo',
                        value: {__objetivo_id}
                    }).appendTo('#form');

                    $('<input>').attr({
                        type: 'text',
                        id: 'tipo_grafico',
                        name: 'tipo_grafico',
                        value: {__tipo_grafico}
                    }).appendTo('#form');
                    $("#form").submit();
                }    
            
        </script>
    </head>
    <body>
        <!--MENU DESPLEGABLE-->
        <div  style="float: right">{__selector_tiempo}</div>
        
        <!--VARIABLE-->
        <span id="tipo" data-tipo={__tipo_grafico} ></span>
        <!--CONTENDOR DE GRAFICOS O TABLAS-->
        <div class="progress" id="load" style="text-align: center" data-informacion="{__informacion}" data-link="{__link}" data-nombre_tipo="{__nombre_tipo}"></div>
        <br>
    <center><span class="titulo" id="titulo" ></span></center>
    
    <div id="container" align="center"  style="width: auto; height: auto; margin: 0 auto; padding: 10px" data-objetivo={__objetivo_id} ></div>
        <a href="" id = "link" target="_blank" style="display: none;"></a>
    </body>
</html>