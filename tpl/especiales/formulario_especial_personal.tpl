<link rel="stylesheet" href="/tools/jquery/css/multiselect/common.css" type="text/css" />
<link type="text/css" href="/tools/jquery/css/multiselect/ui.multiselect.css" rel="stylesheet" />

<link rel="stylesheet" href="/tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>

<script type="text/javascript" src="/tools/jquery/js/jquery-ui-1.10.min.js"></script>
<script type="text/javascript" src="/tools/jquery/js/jquery-multi-select/plugins/localisation/jquery.localisation-min.js"></script>
<script type="text/javascript" src="/tools/jquery/js/jquery-multi-select/plugins/scrollTo/jquery.scrollTo-min.js"></script>
<script type="text/javascript" src="/tools/jquery/js/jquery-multi-select/ui.multiselect.js"></script>

<link rel="stylesheet" href="tools/jquery/css/jquery-data-table/jquery.dataTables.css"></link>
<script type="text/javascript" src="tools/jquery/js/jquery-data-table/jquery.dataTables.js"></script>

<script>

function validarFormulario() {
    
    var fecha_inicio = document.getElementById("fecha_inicio_periodico").value;
    var fecha_termino = document.getElementById("fecha_termino_periodico").value;
    var nombre_negocio = document.getElementById("titulo").value;
    if(nombre_negocio.length < 1 | nombre_negocio.length > 13 ){
    alert("Debe introducir máximo 12 caracteres");
        return false;
    }
    if (fecha_inicio == 0) {
        alert("Debe seleccionar un periodo.");
        return false;
    }
    if (fecha_termino == 0) {
        alert("Debe seleccionar un periodo.");
        return false;
    }
    if (document.form_principal.horario_id) {
        if (document.form_principal.horario_id.value == "") {
        alert("Debe seleccionar un horario habil.");
        return false;
        }
    }
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
    arrayObjId= JSON.stringify(arrayObjId);
    arrayObjId = btoa(arrayObjId);
    if (document.form_principal.tipo_content.value == "html" || document.form_principal.tipo_content.value == "pdf") {
        window.open('', 'formpopup', 'width=800, height=600, menubar, resizeable, scrollbars');
        document.form_principal.action = 'index.php?tiene_flash='+tiene_flash+"&tiene_svg="+tiene_svg+"&select_obj="+arrayObjId;
        document.form_principal.target = 'formpopup';
        document.form_principal.submit();
        document.form_principal.action = 'index.php';
        document.form_principal.target = '';
        document.getElementById("formbutton").style.display = "none";
        document.getElementById("button2").style.display = "";
    }
    else {
        document.form_principal.submit();
    }
    location.reload();
}
function RestoreButton(){
   if(PrimerLoading){
      PrimerLoading = false;
      return;
   }
   document.getElementById("formbutton").style.display = "";
   document.getElementById("button2").style.display = "none";
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
<input type="hidden" id="usuario_cliente_id" value="{__usuario_id}">
{__usuario_id}
<input type="hidden" id="nombre_usuario" value="{__nombre_usuario}">
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
        <td>
        <div id="formCrearNegocio" style="display:block;"; >

            <div class="divContainer"> 
                <div class="divContainerFormComplete">
                    <label class="labelMantainer" for="titulo" >Nombre Negocio: </label>
                    <input class="inputMaintainerLarge" type="text" id="titulo" title="Título" required>
                </div>
            <br>
                <div class="divContainerFormLeft">
                    <label class="labelMantainer" for="objetivos" >Objetivos :</label>
                </div>
            </div>
            </br>
            <div class="containerMultiSelect">
                <select id="objetive" class="multiselect" multiple="multiple" >
                </select>
            </div>
            <div class="divContainer" id="inputSave">

                <button class="btn" data-role="button" type="button" name="guardar" id="guardar" value="Crear" onclick="">Agregar</button>
            </div>
        </div>
        <div id="formCrearNegocio" style="display:block"; >
            <div class="divContainer">  
                <div class="divContainerFormLeft">
                </div>
            </div>
            <div  style="overflow-y: scroll; width:100%; height:250px; margin: 0; padding: 0" align="center">
            <table id="table_obj" cellspacing="0" class="dataTable listadoMantenedor" class="celdanegra40"  width="50%"  border="0" >
                <thead>
                    <tr>
                        <th class="txtBlanco13b celdaTituloGris" width="220">Negocio</th>
                        <th class="txtBlanco13b celdaTituloGris" width="220">Objetivos</th>
                    </tr>
                </thead>
                <tbody id="table_obj_tbd"><tr  >
                        <td class="txtGris12 {__paso_estilo}" type="text" name= "negocio"></td>
                        <td class="txtGris12 {__paso_estilo}" type="text" name= "objetivo"></td>
                    </tr></tbody>
            </table>
            </div>
            </br>   
        </div>
        </td>
        </tr>
        <tr>
                <td height="15"></td>
        </tr>
        <!-- END BLOQUE_TIPOS -->
        <tr>
            <td align="center">
                <div id="formbutton">
                    <input type="button" value="Generar Reporte" class="boton_accion" onclick="validarFormulario();"/>
                </div>
            </td>
        </tr>
        <tr>
            <td align="center">
            <div id="button2" style="margin-left:30px; display: none">
                <img src="../img/cargando.gif"></td>
            </div>
            </td>
        </tr>
</table>
<br>
<script>
$(document).ready(function()
{      
    var negocio;
    addSection = $('#seccion_especial').data("agregar");    
    getObjetive();
    buildSelect();
    /*Inicializar multiselect.*/
    $(function(){
        $(".multiselect").multiselect();
        setearOptionMultiSelect();
            
    });
    /* Guarda todos los input que esten dentro del div.*/
    $("#formCrearNegocio").each(function(){
        input=$(this).find(':input');
    });
    

    /* Evento enviar.*/
    $("#guardar").click(function () {
        actionSend();
    });

    setValueDefault();

    $('.icon').hover(

        function() {            
            setCss(this, 'zoom', 'document', false);
        },function() {
            try {setCss(this, 'zoom', '', false);}
            catch(err) {}           
        }
    );      
});
/* Función asignada a una variable.*/
String.prototype.replaceAt=function(index, character) {
    return this.substr(0, index) + character ;
}
/* Función asignada a una variable que devuelve el largo.*/
String.prototype.largeStr=function() {
    return this.length;
}
/* Función para setear estilos.*/
function setCss(clase, property, value, multiple){
    if (multiple == true){
        $(clase).css(value);
    }
    else{
        $(clase).css(property, value);  
    }   
}
/*Setea los valores por default (usuario id, id, nombre, usuario_id)*/
function setValueDefault(){
    $(input[0]).val($('#nombre_usuario').val());
    $(input[1]).val($('#usuario_cliente_id').val());
}

function setearOptionMultiSelect(){
    $('.ui-multiselect').hover(
        function() {
            border = $(this).css('border');
            setCss(this, 'border', '2px solid rgba(0, 119, 255, 0.7)', false);
        },function() {
            try {setCss(this, 'border', border, false);}
            catch(err) {}           
        }
    );
}
/* Función encargada de realizar llamada ajax que obtiene los objetivos del cliente.*/
function getObjetive(){
    return $.ajax({
        async: false,
        url: '../call_ajax.php',
        data: {'nameFunction':'getObjetive' },
        type: 'POST',
        success: function(response) {
            saveData(response);

        },
        error: function(error) {
        }
    });
}
/* Función para crear agregar option al select y definir los objetivos seleccionados.*/
function buildSelect(response, arr_objetive){

    $.each( OBJETIVES, function( key, value ) {
        objetiveId = key;
        name = value.nombre;//array de objetivos
        nameObj = name.largeStr()>46?(name.substring(1, 46)+' ...'):name;
        $('#objetive').append("<option value="+key+" >"+nameObj+" </option></option>");
    });     
}
/* Toma los id de objetivos agregados(multiselect). */
function getValueMultiSelect(){
    var count = 0;
    var objetives = new Array();
    $('.ui-multiselect .selected li').each(function(idx,el){
        var link = $(el).data('optionLink');
        if(link){
            if (count == 1){
                objetives = objetives+(link.val());
            }
            else{
                objetives = objetives+","+(link.val());
            }
        }
        count++;
    });
    objetives = objetives;
    return objetives;
}

/* Construye el array con valores. */
function buildArrayValues(){

    var valueInput = new Array();
    var dicObjetive = new Array();
    var objetives = getValueMultiSelect();
    var text = '';
    var negocio;

    var negocio=document.getElementById("titulo").value;


    $.each( input, function( index, value ) {
        if (index == 1){
            for (var i = 0; i < $('#objetive option:selected').length; i++) {
                text = text + $("#objetive option[value="+($(value).val())[i]+"]").text();
            }
        }
        else{
            valueInput[index]=$(value).val();
        }
    });
    valueInput[0] = negocio;
    valueInput[1] = objetives;    
    valueInput[2] = text;
    return valueInput;
}
arrayTitulo = new Array();
arrayObj = new Array();
var arrayObjId=null;
var cantNegocios=0;

//funcion que envie input-select a td de tabla y crea json
function actionSend(){
    
    value = new buildArrayValues();
    var objetivos;
    var nombre ;

    nombre =document.getElementById("titulo").value;
    nombre_obj =value[2];

    //valida que si es primera vez que se agrega un negocio y abre el json
    if(arrayObjId==null){
        arrayObjId = '{';
    }else{
        arrayObjId=arrayObjId.substring(0,arrayObjId.length-1);
        arrayObjId = arrayObjId+',';
    }

    if(cantNegocios>1){
        arrayObjId = arrayObjId;
    }
    //agrega el nombre del negocio al json y agrega a la tabla table_obj
    arrayObjId = arrayObjId+'"'+cantNegocios+'":{"nombre":"'+nombre+'","objetivos":{';

    $('<tr>').attr({id: 'trNegocio_'+cantNegocios}).appendTo('#table_obj_tbd');
    $('<td>').text(nombre).appendTo('#trNegocio_'+cantNegocios);
    $('<td>').text(nombre_obj).appendTo('#trNegocio_'+cantNegocios);

    $('.ui-sortable li').each(function(index){
        
        if($(this).data("selected-value")!=undefined){
            arrayObjId = arrayObjId+'"objetivo_id_'+index+'":'+$(this).data("selected-value");
            if(index<$('.ui-sortable li').length-1){
                arrayObjId = arrayObjId+',';
            }
        }
    });

    arrayObjId = arrayObjId+'}}';
    cantNegocios=cantNegocios+1;
    arrayObjId =  arrayObjId+'}';
}

/*Maneja el evento color.*/
function setColor(elem, colour){
    elem.css("background-color", colour);
}

/* Variable global. */
function saveData(response){
    OBJETIVES =JSON.parse(response);
}
</script>