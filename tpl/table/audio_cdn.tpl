<script src="{__wavesurfer}" type="text/javascript"></script>
<script type="text/javascript" src="/tools/jquery/js/jquery-ui-1.10.min.js"></script>
<link rel="stylesheet" href="{__path_jquery_ui}css/ui-lightness/jquery-ui-1.8.17.custom.css"></link>
<link rel="stylesheet" type="text/css" href="css/estilos_audio.css">

<div style="float: right;">
    <input type="button" class="patrones" name="patrones" value="Patrones" onclick="mostrarModalPatrones()">
</div>

<div>
    <input type="button" class="accordion2" style="float: center;" value="Filtrar contenido"></input>
    <div class="panel2" style="background-color: #f6f6f6;">
        <br>
        <div id="time-range" style="width:90%; padding-left: 30px;">
            <div>
                <a>ISP:&nbsp;</a>
                <select id="select_filtro" onchange="nodo_selected(this.value)">
                    <option id="nodos_filtros_-1" data-id="-1" value="nodos_filtros_todos" selected="selected">Todos</option>
                    <!-- BEGIN LISTA_NODOS_FILTRO -->
                    <option id="nodos_filtros_{__nodoid_filtro}" data-id="{__nodoid_filtro}" value="nodos_filtros_{__nodoid_filtro}">{__nodo_filtro}</option>
                    <!-- END LISTA_NODOS_FILTRO -->
                </select>
                <br>
                <br>
                <div class="flat-slider" id="flat-slider"></div>
                <br><br>
                <!-- BEGIN VALORES_FILTRO -->
                <div align="center">
                    <a>Hora Inicio: </a>
                    <input type="number" class="slider-time-hour1" name="hora_inicio" value="{__hora_inicio}" min="0" max="23" style='width:35px; height:20px'>
                    <a> : </a>
                    <input type="number" class="slider-time-minute1" name="minuto_inicio" value="{__minuto_inicio}" min="0" max="59" style='width:35px; height:20px'>
                    <a>&nbsp; &nbsp;</a>
                    <a>Hora Término: </a>
                    <input type="number" class="slider-time-hour2" name="hora_termino" value="{__hora_termino}" min="0" max="23" style='width:35px; height:20px'>
                    <a> : </a>
                    <input type="number" class="slider-time-minute2" name="minuto_termino" value="{__minuto_termino}" min="0" max="59" style='width:35px; height:20px'>
                </div>
                <!-- END VALORES_FILTRO -->
                <br>     
            </div>
        </div>
        <div id="formbutton">
            <input type="button" class="boton_accion" value="Filtrar" style="cursor: pointer" onClick="filtroAudio({__item_id}, 1);">
        </div>
        <div id="button2" style="margin-left:30px; display:none;">
            <img src="img/cargando.gif">
        </div>
        <br>
    </div>
</div>
<br>

<a id="paginaarriba"></a>

<table align="right" style="float: right;">
    <tr>
        <td>
            <input type="button" id="boton_izq" class="{__class_boton_atras}" {__disabled_atras} onClick="cargaItemFiltroScreenshot('contenedor_{__item_id}',{__item_id}, 0,['pagina', {__pagina_atras}],[ {_h_slider}, {_m_slider}, {_h2_slider}, {_m2_slider}, {__monitoreo_id}]); return false;">
        </td>
        <td width="1px"></td>
        <td width="10px" class="celdapagina" style="display:inline">{__pagina}</td>
        <td>
            <input type="button" id="boton_der" class="{__class_boton_adelante}" {__disabled_adelante} onClick="cargaItemFiltroScreenshot('contenedor_{__item_id}',{__item_id}, 0,['pagina', {__pagina_adelante}],[ {_h_slider}, {_m_slider}, {_h2_slider}, {_m2_slider}, {__monitoreo_id}]); return false;">
        </td>
    </tr>
</table>
<br><br>

<!-- >>>>>>>>>>>>>>>>>>>>COMIENZA MODAL>>>>>>>>>>>>>>>>>>>>>> -->
<div dojoType="dijit.Dialog" id="dialog_audio" title="DETALLE AUDIO" style="font-family: Verdana,Arial,Helvetica,sans-serif;">
    <table width="750" class="formulario">
        <tr>
            <th width="120">Fecha</th>
            <td id="audio_fecha"></td>
        </tr>
        <tr>
            <th>Monitor</th>
            <td id="audio_nodo"></td>
        </tr>
        <tr>
            <th>Paso</th>
            <td id="audio_paso"></td>
        </tr>
        <tr>
            <th>Estado</th>
            <td id="audio_estados"></td>
        </tr>
        <table style="width: 100%; background: url('../img/atentus_logo.png') no-repeat center; background-size: 30%; background-position: 0% 0%">
            <tr>
                <td colspan="4" id="waveform">
                </td>
            </tr>
            <tr>
                <td align="left">
                    <a class="waveform__counter">0:00</a>
                </td>
                <td style="text-align: center">
                    <button id="buttonPlay" onclick="playAudio()">
                        <i class="o-play-btn__icon">
                            <div class="o-play-btn__mask"/>
                        </i>
                    </button>
                </td>
                <td align="right">
                    <a class="waveform__duration">0:00</a>
                </td>
            </tr>
        </table>
    </table>
</div>
<!-- >>>>>>>>>>>>>>>>>>>>TERMINA MODAL>>>>>>>>>>>>>>>>>>>>>> -->

<!-- >>>>>>>>>>>>>>>>>>>>EMPIEZA MODAL PATRON>>>>>>>>>>>>>>>>>>>>>> -->
<div dojoType="dijit.Dialog" id="dialog_patron" title="DETALLE PATRÓN" style="font-family: Verdana,Arial,Helvetica,sans-serif;">
    <table width="750px" style="background: url('../img/atentus_logo.png') no-repeat center; background-size: 30%; background-position: 0% 0%">
        <tr>
            <td colspan="4" id="waveform_patron">
            </td>
        </tr>
        <tr>
            <td align="left">
                <a class="waveform__counter_patron">0:00</a>
            </td>
            <td style="text-align: center">
                <button id="buttonPlayPatron" onclick="playAudioPatron()">
                    <i class="o-play-btn_patron__icon">
                        <div class="o-play-btn_patron__mask"/>
                    </i>
                </button>
            </td>
            <td align="right">
                <a class="waveform__duration_patron">0:00</a>
            </td>
        </tr>
    </table>
</div>
<!-- >>>>>>>>>>>>>>>>>>>>TERMINA MODAL PATRON>>>>>>>>>>>>>>>>>>>>>> -->

<!-- >>>>>>>>>>>>>>>>>>>>EMPIEZA MODAL PATRONES>>>>>>>>>>>>>>>>>>>>>> -->
<div dojoType="dijit.Dialog" id="dialog_patrones" title="DETALLE PATRONES" style="font-family: Verdana,Arial,Helvetica,sans-serif;">
    <table>
        
            <!-- BEGIN BLOQUE_DE_PASOS -->
            <tr>
                
                <button class="accordion3" type="button" style="float: left; width: 100%">{__nombre_paso_patron}</button>
                
                <div class="panel3">
                <!-- BEGIN BLOQUE_DE_PATRONES -->
                    <button class="accordion4" type="button" onclick="mostrarPatronesAudio('{__hash_patron}', '{__id_patron}')" style="float: left; width: 100%, background-color: #F47001;">Nombre Patron:&nbsp;&nbsp;{__nombre_patron}</button>
                    <a class="descarga" style="float: right" href="utils/get_audio.php?token={__hash_patron}" download="{__nombre_paso}_{__id_patron}">Descarga Audio&nbsp;&nbsp;<img src="/img/download.png"></a>
                 <!-- END BLOQUE_DE_PATRONES -->
                </div>

            </tr>
             <!-- END BLOQUE_DE_PASOS -->
    </table>
           
        </tr>
    </table>
</div>
<!-- >>>>>>>>>>>>>>>>>>>>TERMINA MODAL PATRONES>>>>>>>>>>>>>>>>>>>>>> -->

<!-- >>>>>>>>>>>>>>>>>>>>EMPIEZA MODAL PATRONES_WAVESURFER>>>>>>>>>>>>>>>>>>>>>> -->
<div dojoType="dijit.Dialog" id="dialog_patrones_wav" title="DETALLE PATRONES" style="font-family: Verdana,Arial,Helvetica,sans-serif;">

    <table width="750px" style="background: url('../img/atentus_logo.png') no-repeat center; background-size: 30%; background-position: 0% 0%">

        <tr>
            <td style="width: 700px" colspan="4" id="waveform_patrones">
            </td>
        </tr>
        <tr>
            <td align="left">
                <a class="waveform__counter_patrones">0:00</a>
            </td>
            <td style="text-align: center">
                <button id="buttonPlayPatrones" onclick="playAudioPatrones()">
                    <i class="o-play-btn_patrones_detalle__icon">
                        <div class="o-play-btn_patrones_detalle__mask"/>
                    </i>
                </button>
            </td>
            <td align="right">
                <a class="waveform__duration_patrones">0:00</a>
            </td>
        </tr>  
    </table>
</div>
<!-- >>>>>>>>>>>>>>>>>>>>TERMINA MODAL PATRONES_WAVESURFER>>>>>>>>>>>>>>>>>>>>>> -->

<!-- >>>>>>>>>>>>>>>>>>>>EMPIEZA MODAL PATRONES DETALLE>>>>>>>>>>>>>>>>>>>>>> -->
<div dojoType="dijit.Dialog" id="dialog_patrones_detalle" title="DETALLE PATRONES" style="font-family: Verdana,Arial,Helvetica,sans-serif;">
    <table width="750px" style="background: url('../img/atentus_logo.png') no-repeat center; background-size: 30%; background-position: 0% 0%">
        <tr>
            <td style="width: 700px" colspan="4" id="waveform_patrones">
            </td>
        </tr>
        <tr>
            <td align="left">
                <a class="waveform__counter_patrones">0:00</a>
            </td>
            <td style="text-align: center">
                <button id="buttonPlayPatrones" onclick="playAudioPatrones()">
                    <i class="o-play-btn_patrones_detalle__icon">
                        <div class="o-play-btn_patrones_detalle__mask"/>
                    </i>
                </button>
            </td>
            <td align="right">
                <a class="waveform__duration_patrones">0:00</a>
            </td>
        </tr>
    </table>
</div>
<!-- >>>>>>>>>>>>>>>>>>>>TERMINA MODAL PATRONES DETALLE>>>>>>>>>>>>>>>>>>>>>> -->

<!-- BEGIN BLOQUE_MONITOREOS -->
<table width="100%" style="margin-bottom:10px;">
    <tr>
        <td class="celdanegra40" width="100%">
            <table>
                <tr>
                    <td width="180" class="textblanco12" style="padding-left: 10;">{__monitoreo_fecha}</td>
                    <td width="200" class="textblanco12b">{__nombre_monitor}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="background-color: #e2e2e2;">
            <div id="btn_atras_{__fecha_int}" onclick="scrollBotonAtras('{__fecha_int}');" style="display: none; cursor: pointer; float: left; position: relative; margin-right: -20px; width: 20px; height:144px; background-color: #c7c7c7; text-align: center;">
                <img style="padding-top: 60px;" src="img/botones/flecha_izq2.png"/>
            </div>
            <div id="monitoreo_{__fecha_int}" style="float: left; width: 720px; overflow:hidden;">
                <table width="{__monitoreo_ancho}" id="monitoreo_tabla_{__fecha_int}">
                    <tr>
                        <!-- BEGIN LISTA_PASOS -->
                        <td style="padding: 10px;">
                            <table>
                                <tr>
                                    <td class="textblanco12" style="padding: 2px 2px 0px 02px; background: {__paso_warning} #{__color_codigo};" title="{__paso_nombre}">
                                        <div style="white-space: nowrap; padding-left: 4px; padding-bottom: 2px; overflow: hidden; text-overflow: ellipsis; width: 120px; padding-top: 2px"><a style="color: white;">{__paso_nombre}</a></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: #{__color_codigo}; width: 130px;" align="center" valign="middle">
                                        <div id="{__token_resultado}" data='cdn' data-json_patron = '{__json_patron}' onclick="mostrarAudio('{__nodo_filtro}', '{__paso_nombre}', '{__token_resultado}','{__monitoreo_fecha_modal}','{__codigo_icono}','{__color_codigo}','{__cant_patron}', '{__fecha_int}_{__paso_id}');" name="audio_{__fecha_int}_{__paso_id}"  style="cursor: pointer;">
                                            <img src="/img/play_audio.png">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: #{__color_codigo}; width: 130px;  padding-bottom: 3px; padding-top: 2px" align="center" valign="middle">
                                        <a style="color: white; font-size: 10px" href="utils/get_audio.php?token={__token_resultado}" download="{__paso_nombre}_{__monitoreo_fecha_modal}">Descarga Audio&nbsp;&nbsp;<img src="/img/download.png"></a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <!-- END LISTA_PASOS -->
                    </tr>
                </table>
            </div>
            <div id="btn_adelante_{__fecha_int}" onclick="scrollBotonAdelante('{__fecha_int}');" style="display: {__boton_adelante_display}; cursor: pointer; float: right; margin-left: -20px; width: 20px; height:144px; background-color: #c7c7c7; text-align: center;">
                <img style="padding-top: 60px;" src="img/botones/flecha_der2.png"/>
            </div>
        </td>
    </tr>
</table>

<!-- END BLOQUE_MONITOREOS -->
<a id="paginaabajo"></a>

<table align="right">
    <tr>
        <td>
            <input type="button" id="boton_izq" class="{__class_boton_atras}" {__disabled_atras} onClick="cargaItemFiltroScreenshot('contenedor_{__item_id}',{__item_id}, 0,['pagina', {__pagina_atras}],[ {_h_slider}, {_m_slider}, {_h2_slider}, {_m2_slider}, {__monitoreo_id}]); return false;">
        </td>
        <td width="1px"></td>
        <td width="10px" class="celdapagina" style="display:inline">{__pagina}</td>
        <td>
            <input type="button" id="boton_der" class="{__class_boton_adelante}" {__disabled_adelante} onClick="cargaItemFiltroScreenshot('contenedor_{__item_id}',{__item_id}, 0,['pagina', {__pagina_adelante}],[ {_h_slider}, {_m_slider}, {_h2_slider}, {_m2_slider}, {__monitoreo_id}]); return false;">
        </td>
    </tr>
</table>
<style>
.dijitDialog, #dialog_patrones {
    width:450px;
    height: 250px;
    overflow-y: scroll;

}
.dijitDialog, #dialog_patrones_wav{
    width:auto;
    height: auto;

}
.accordion3 {
    background-color: #F47001;
    border-radius: 10px;
    color: #fff;
    cursor: pointer;
    padding: 25px;
    width: 100%;
    height: 20px;
    border-color: white;
    text-align: left;
    outline: none;
    font-size: 15px;
    transition: 0.4s;
}
.accordion4 {
    background-color: #f7a138;
    border-radius: 10px;
    color: #fff;
    cursor: pointer;
    padding: 25px;
    height: 20px;
    width: 100%;
    border-color: white;
    text-align: left;
    outline: none;
    font-size: 15px;
    transition: 0.4s;
}

.active, .accordion3:hover {
    background-color: #F47001;
    color: #fff;;

}

.panel3 {
    padding: 0 18px;
    display: none;
    background-color: white;
    overflow: hidden;
}
</style>

<script>


//ACORDEON DE PATRONES
var acc = document.getElementsByClassName("accordion3");
var i;

for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var panel3 = this.nextElementSibling;
        if (panel3.style.display === "block") {
            panel3.style.display = "none";
        } else {
            panel3.style.display = "block";
        }
    });
}


    $(document).ready(function(){
        $("#buttonPlay").addClass("o-play-btn");
        $("#buttonPlayPatron").addClass("o-play-btn_patron");
        $("#buttonPlayPatrones").addClass("o-play-btn_patrones_detalle");
        $("option").removeAttr("selected");
        $("#nodos_filtros_"+{__nodo_selected}).attr('selected', 'selected');
        if ({__isset} == 0) {
            $(".panel2").css('max-height', '211px');
        }
        var wavesurfer;

    });   



    //FUNCIÓN ACORDEÓN
    var acc = document.getElementsByClassName("accordion2");
    var i;
    for (i = 0; i < acc.length; i++) {
        acc[i].onclick = function(){
            <!-- BEGIN VALORES_SLIDER -->
            //SI EL VALOR DEL SLIDER NO SE HA PASADO POR POST
            //RECUPERAR EL VALOR ACTUAL DE CADA INPUT DE LA HORA
            if ({__valor_slider_inicio} == 0 || {__valor_slider_termino} == 0) {
                var hora_inicio = parseInt(document.getElementsByClassName('slider-time-hour1')[0].value);
                var minuto_inicio = parseInt(document.getElementsByClassName('slider-time-minute1')[0].value);
                var hora_termino = parseInt(document.getElementsByClassName('slider-time-hour2')[0].value);
                var minuto_termino = parseInt(document.getElementsByClassName('slider-time-minute2')[0].value);
                var slider_inicio = (hora_inicio * 60) + minuto_inicio;
                var slider_termino = (hora_termino * 60) + minuto_termino;
                var slider_inicio_filtro = slider_inicio;
                var slider_termino_filtro = slider_termino;
            }else{
                var slider_inicio_filtro = {__valor_slider_inicio};
                var slider_termino_filtro = {__valor_slider_termino};
            }
            $('#flat-slider').slider('values', 0, slider_inicio_filtro);
            $('#flat-slider').slider('values', 1, slider_termino_filtro);
            <!-- END VALORES_SLIDER -->
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.maxHeight){
                panel.style.maxHeight = null;
            }else{
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        }
    }
    //FIN ACORDEÓN

    //SLIDER
    $("#flat-slider").slider({
        range: true,
        min: 0,
        max: 1439,
        step: 1,
        values: [0, 1440],
        slide: function (e, ui) {
            var hours1 = Math.floor(ui.values[0] / 60);
            var minutes1 = ui.values[0] - (hours1 * 60);
            var hours1String = hours1.toString();
            var minutes1String = minutes1.toString();
            if (hours1String.length == 1) {
                hours1 = '0' + hours1;
            }
            if (minutes1String.length == 1) {
                minutes1 = '0' + minutes1;
            }
            //SE CAMBIAN LOS VALORES DE LOS INPUT MIENTRAS SE ARRASTRA EL SLIDER
            $('.slider-time-hour1').val(hours1);
            $('.slider-time-minute1').val(minutes1);
            var hours2 = Math.floor(ui.values[1] / 60);
            var minutes2 = ui.values[1] - (hours2 * 60);
            var hours2String = hours2.toString();
            var minutes2String = minutes2.toString();
            if (hours2String.length == 1) {
                hours2 = '0' + hours2;
            }
            if (minutes2String.length == 1) {
                minutes2 = '0' + minutes2;
            }
            $('.slider-time-hour2').val(hours2);
            $('.slider-time-minute2').val(minutes2);
        }
    });
    //SE CAMBIAN LOS VALORES DEL SLIDER DE ACUERDO A LO INGRESADO EN LOS INPUTS
    $(".slider-time-hour1").change(function() {
        $(".slider-time-hour1").attr({
            "max": $(".slider-time-hour2").val()
        });
        var hora1 = $(".slider-time-hour1").val();
        var minuto1 = parseInt($(".slider-time-minute1").val());
        var horaSlider1 = (hora1 * 60 + minuto1);
        $("#flat-slider").slider('values',0, horaSlider1);
    });

    $(".slider-time-minute1").change(function() {
        if ($(".slider-time-hour1").val() == $(".slider-time-hour2").val()) {
            $(".slider-time-minute1").attr({
                "max": $(".slider-time-minute2").val()
            });
        }
        var hora1 = $(".slider-time-hour1").val();
        var minuto1 = parseInt($(".slider-time-minute1").val());
        var horaSlider1 = (hora1 * 60 + minuto1);
        $("#flat-slider").slider('values',0, horaSlider1);
    });

    $(".slider-time-hour2").change(function() {
        $(".slider-time-hour2").attr({
            "min": $(".slider-time-hour1").val()
        });
        var hora2 = $(".slider-time-hour2").val();
        var minuto2 = parseInt($(".slider-time-minute2").val());
        var horaSlider2 = (hora2 * 60 + minuto2);
        $("#flat-slider").slider('values',1, horaSlider2);
    });

    $(".slider-time-minute2").change(function() {
        $(".slider-time-minute2").attr({
            "min": $(".slider-time-minute1").val()
        });
        var hora2 = $(".slider-time-hour2").val();
        var minuto2 = parseInt($(".slider-time-minute2").val());
        var horaSlider2 = (hora2 * 60 + minuto2);
        $("#flat-slider").slider('values',1, horaSlider2);
    });
    //FIN SLIDER

    if ({__mediciones} == '1') {
        document.getElementById("paginaarriba").innerHTML = "Página {__pagina} de {__cant_pag}";
        document.getElementById("paginaabajo").innerHTML = "Página {__pagina} de {__cant_pag}";
    }else{
        $("a").remove(".paginaarriba");
        $("a").remove(".paginaabajo");
        $("input").remove("#boton_izq");
        $("input").remove("#boton_der");
        $("td").remove(".celdapagina");
        $("input").remove(".patrones");
    }

    function mostrarAudio(monitor, paso, token_resultado, fecha, nombre_codigo, paso_color, cant_patrones, id_fecha_paso) {
        $("<div>", {'id': 'waveform_'+id_fecha_paso}).appendTo('#wavesurf');
        var estados = "";
        var patrones = 0;
        var button = document.getElementById(token_resultado);
        var json_patron = button.getAttribute('data-json_patron');
        var obj = JSON.parse(json_patron);

        for (i = 0; i < cant_patrones; i++) {
            estados = estados + 
            '<table>' +
                '<tr>' +
                    '<td align="center" width="80" height="22" style="border: 0px; background-color: #' + obj[i].color + '">'+
                        '<div class="tooltip">'+
                            '<span class="'+ obj[i].codigo_id +'">'+obj[i].nombre_cod+'</span>'+
                            '<i class="sprite sprite-' + obj[i].codigo + '"/></i>'+
                        '</div>'+
                    '</td>'+
                    '<td style="border: 0px;">'+
                    '<input type="button" class="patron" name="patrones" value="Patrón Audio" onclick="mostrarPatron(\''+obj[i].hash_md5+'\', \''+id_fecha_paso+'_'+nombre_codigo+'\')">'+
                    '</td>'+
                '</tr>'+
            '</table>';
        }

        waveform = '<div id="waveform_'+id_fecha_paso+'"></div>';
        dojo.byId("audio_fecha").innerHTML = fecha;
        dojo.byId("audio_nodo").innerHTML = monitor;
        dojo.byId("audio_paso").innerHTML = paso;
        dojo.byId("audio_estados").innerHTML = estados;
        dojo.byId("waveform").innerHTML = waveform;
        /////////////////////////////////////////////////////////
        //////////////////////WAVESURFER/////////////////////////
        /////////////////////////////////////////////////////////

        //se instancia la clase wavesurfer
        wavesurfer = WaveSurfer.create({
            //opciones de wavesurfer
            container: '#waveform_' + id_fecha_paso,
            cursorWidth: 0,
            waveColor: '#FEDEC8', //naranjo claro
            cursorColor: '#FF5733', //naranjo
            progressColor: '#F47001',
            height: '250',
            barHeight: '0.5'
        });

        wavesurfer.load('utils/get_audio.php?token='+token_resultado);

        //SE MUESTRA TIEMPO TRANSCURRIDO DEL AUDIO Y LA DURACIÓN
        var formatTime = function (time) {
            return [
            Math.floor((time % 3600) / 60), // MINUTOS
            ('00' + Math.floor(time % 60)).slice(-2) // SEGUNDOS
            ].join(':');
        };

        // Muestra el tiempo transcurrido
        wavesurfer.on('audioprocess', function () {
            $('.waveform__counter').text(formatTime(wavesurfer.getCurrentTime()));
        });

        // Muestra el tiempo total del audio
        wavesurfer.on('ready', function () {
            $('.waveform__duration').text(formatTime(wavesurfer.getDuration()));
        });

        //cuando termine el audio el icono pause cambiara a play
        wavesurfer.on('finish', function () {
            $('.o-play-btn').toggleClass('o-play-btn--playing');
        });
        //cambiar atributo al span destroy
        $("#dialog_audio .dijitDialogCloseIcon").click(function() {
            wavesurfer.destroy();
            $(".o-play-btn").removeClass("o-play-btn--playing");
        });

        $(document).keyup(function(e) {
            if (e.keyCode == 32) { 
                wavesurfer.playPause();
            }
            if (e.keyCode == 27) { 
                wavesurfer.destroy();
                $(".o-play-btn").removeClass("o-play-btn--playing");
            }
        });

        //////////////////////////////////////////////////////////
        //////////////////////FIN WAVESURFER//////////////////////
        //////////////////////////////////////////////////////////

        dijit.byId("dialog_audio").show();

    }

    function mostrarPatron(token_patron, id_fecha_paso){
        /////////////////////////////////////////////////////////
        //////////////////////WAVESURFER/////////////////////////
        /////////////////////////////////////////////////////////
        waveform = '<div id="waveform_'+id_fecha_paso+'"></div>';
        dojo.byId("waveform_patron").innerHTML = waveform;
        //se instancia la clase wavesurfer
        wavesurfer_patron = WaveSurfer.create({
            //opciones de wavesurfer
            container: '#waveform_' + id_fecha_paso,
            cursorWidth: 0,
            waveColor: '#FEDEC8', //naranjo claro
            cursorColor: '#FF5733', //naranjo
            progressColor: '#F47001',
            height: '250',
            barHeight: '0.5'
        });

        wavesurfer_patron.load('utils/get_audio.php?token='+token_patron);

        //SE MUESTRA TIEMPO TRANSCURRIDO DEL AUDIO Y LA DURACIÓN
        var formatTime = function (time) {
            return [
            Math.floor((time % 3600) / 60), // MINUTOS
            ('00' + Math.floor(time % 60)).slice(-2) // SEGUNDOS
            ].join(':');
        };

        // Muestra el tiempo transcurrido
        wavesurfer_patron.on('audioprocess', function () {
            $('.waveform__counter_patron').text(formatTime(wavesurfer_patron.getCurrentTime()));
        });

        // Muestra el tiempo total del audio
        wavesurfer_patron.on('ready', function () {
            $('.waveform__duration_patron').text(formatTime(wavesurfer_patron.getDuration()));
        });

        //cuando termine el audio el icono pause cambiara a play
        wavesurfer_patron.on('finish', function () {
            $('.o-play-btn_patron').toggleClass('o-play-btn_patron--playing');
        });
        //cambiar atributo al span destroy
        $("#dialog_patron .dijitDialogCloseIcon").click(function() {
            wavesurfer_patron.destroy();
            $(".o-play-btn_patron").removeClass("o-play-btn_patron--playing");
        });

        $(document).keyup(function(e) {
            if (e.keyCode == 32) { 
                wavesurfer_patron.playPause();
            }
            if (e.keyCode == 27) { 
                wavesurfer_patron.destroy();
                $(".o-play-btn_patron").removeClass("o-play-btn_patron--playing");
            }
        });

        //////////////////////////////////////////////////////////
        //////////////////////FIN WAVESURFER//////////////////////
        //////////////////////////////////////////////////////////

        dijit.byId("dialog_patron").show();
    }

    function mostrarModalPatrones(){
        dijit.byId("dialog_patrones").show(); 
    }

    function mostrarPatronesAudio(token_patron, id_patron){

        /////////////////////////////////////////////////////////
        //////////////////////WAVESURFER/////////////////////////
        /////////////////////////////////////////////////////////
        waveform = '<div id="waveform_'+id_patron+'"></div>';
        dojo.byId("waveform_patrones").innerHTML = waveform;
        //se instancia la clase wavesurfer
        wavesurfer_patrones = WaveSurfer.create({
            //opciones de wavesurfer
            container: '#waveform_'+id_patron,
            cursorWidth: 0,
            waveColor: '#FEDEC8', //naranjo claro
            cursorColor: '#FF5733', //naranjo
            progressColor: '#F47001',
            height: '250',
            barHeight: '0.5',
            maxCanvasWidth: '8000'

        });
        
        wavesurfer_patrones.load('utils/get_audio.php?token='+token_patron);

        //SE MUESTRA TIEMPO TRANSCURRIDO DEL AUDIO Y LA DURACIÓN
        var formatTime = function (time) {
            return [
            Math.floor((time % 3600) / 60), // MINUTOS
            ('00' + Math.floor(time % 60)).slice(-2) // SEGUNDOS
            ].join(':');
        };

        // Muestra el tiempo transcurrido
        wavesurfer_patrones.on('audioprocess', function () {
            $('.waveform__counter_patrones').text(formatTime(wavesurfer_patrones.getCurrentTime()));
        });

        // Muestra el tiempo total del audio
        wavesurfer_patrones.on('ready', function () {
            $('.waveform__duration_patrones').text(formatTime(wavesurfer_patrones.getDuration()));
        });

        //cuando termine el audio el icono pause cambiara a play
        wavesurfer_patrones.on('finish', function () {
            $('.o-play-btn_patrones_detalle').toggleClass('o-play-btn_patrones_detalle--playing');
        });
        //cambiar atributo al span destroy
        $("#dialog_patrones_detalle .dijitDialogCloseIcon").click(function() {
            wavesurfer_patrones.destroy();
            $(".o-play-btn_patrones_detalle").removeClass("o-play-btn_patrones_detalle--playing");
        });

        $(document).keyup(function(e) {
            if (e.keyCode == 32) { 
                wavesurfer_patrones.playPause();
            }
            if (e.keyCode == 27) { 
                wavesurfer_patrones.destroy();
                $(".o-play-btn_patrones_detalle").removeClass("o-play-btn_patrones_detalle--playing");
            }
        });
        //////////////////////////////////////////////////////////
        //////////////////////FIN WAVESURFER//////////////////////
        //////////////////////////////////////////////////////////

        dijit.byId("dialog_patrones_wav").show();
    }

    function playAudio(){
        wavesurfer.playPause();
        //cambia la clase del boton play y alterna entre play y pause al hacer click
        if ($('.o-play-btn').hasClass('o-play-btn--playing')){
            $(".o-play-btn").removeClass("o-play-btn--playing");
        }else{
            $(".o-play-btn").addClass("o-play-btn--playing");
        }
    }

    function playAudioPatron(){
        wavesurfer_patron.playPause();
        //cambia la clase del boton play y alterna entre play y pause al hacer click
        if ($('.o-play-btn_patron').hasClass('o-play-btn_patron--playing')){
            $(".o-play-btn_patron").removeClass("o-play-btn_patron--playing");
        }else{
            $(".o-play-btn_patron").addClass("o-play-btn_patron--playing");
        }
    }

    function playAudioPatrones(){
        wavesurfer_patrones.playPause();
        //cambia la clase del boton play y alterna entre play y pause al hacer click
        if ($(".o-play-btn_patrones_detalle").hasClass('o-play-btn_patrones_detalle--playing')){
            $(".o-play-btn_patrones_detalle").removeClass("o-play-btn_patrones_detalle--playing");
        }else{
            $(".o-play-btn_patrones_detalle").addClass("o-play-btn_patrones_detalle--playing");
        }
    }

    function nodo_selected(a){
        $("option").removeAttr("selected");
        $("#"+a).attr('selected', 'selected');
    }

    function filtroAudio(item_id, pagina){
        //SE OBTIENE EL VALOR DE HORA Y MINUTO DEL SLIDER, INICIO Y TERMINO
        var hora_inicio = document.getElementsByClassName('slider-time-hour1')[0].value;
        var minuto_inicio = document.getElementsByClassName('slider-time-minute1')[0].value;
        var hora_termino = document.getElementsByClassName('slider-time-hour2')[0].value;
        var minuto_termino = document.getElementsByClassName('slider-time-minute2')[0].value;
        //OBTIENE EL ID DEL NODO SELECCIONADO
        var id_nodo = $( "#select_filtro option:selected" ).data('id');
        var data_filtro = [hora_inicio, minuto_inicio, hora_termino, minuto_termino, id_nodo];
        cargaItemFiltroScreenshot('contenedor_'+item_id, item_id, 0, pagina, data_filtro);
        document.getElementById("formbutton").style.display = "none";
        document.getElementById("button2").style.display = "";
        return true;
    }

    var PrimerLoading = true;
    function RestoreButton(){
        if(PrimerLoading){
            PrimerLoading = false;
            return;
        }
        document.getElementById("formbutton").style.display = "";
        document.getElementById("button2").style.display = "none";
    }

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

</script>