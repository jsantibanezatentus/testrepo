<link rel="stylesheet" href="{__path_jquery_ui}css/ui-lightness/jquery-ui-1.8.17.custom.css"></link>
<script type="text/javascript" src="{__path_jquery_ui}js/jquery-ui-1.8.17.custom.min.js"></script>

<script>
//FUNCIÓN ACORDEÓN
$(document).ready(function() {
  $("option").removeAttr("selected");
  $("#nodos_filtros_"+{__nodo_selected}).attr('selected', 'selected');
  if ({__isset} == 0) {
    $(".panel2").css('max-height', '211px');
  }
  if ({__display_filtro} == 60) {
    document.getElementById("elementos_plus_filtro").style.display = 'inline';
  }
});
function nodo_selected(a){
$("option").removeAttr("selected");
$("#"+a).attr('selected', 'selected');
}
function filtroElementosPlus(item_id, pagina){
  //SE OBTIENE EL VALOR DE HORA Y MINUTO DEL SLIDER, INICIO Y TERMINO
  var hora_inicio = document.getElementsByClassName('slider-time-hour1')[0].value;
  var minuto_inicio = document.getElementsByClassName('slider-time-minute1')[0].value;
  var hora_termino = document.getElementsByClassName('slider-time-hour2')[0].value;
  var minuto_termino = document.getElementsByClassName('slider-time-minute2')[0].value;
  //OBTIENE EL ID DEL NODO SELECCIONADO
  var id_nodo = $( "#select_filtro option:selected" ).data('id');
  var data_filtro = [hora_inicio, minuto_inicio, hora_termino, minuto_termino, id_nodo];
  cargaItemFiltroElementosPlus('contenedor_'+item_id, item_id, 0, ['pagina', pagina], data_filtro);
  document.getElementById("formbutton").style.display = "none";
  document.getElementById("button2").style.display = "";
  return true;
}
var PrimerLoading = true;
function RestoreButton()
{
   if( PrimerLoading )
   {
      PrimerLoading = false;
      return;
   }
   document.getElementById("formbutton").style.display = "";
   document.getElementById("button2").style.display = "none";
}

var acc = document.getElementsByClassName("accordion2");
var i;
for (i = 0; i < acc.length; i++) {
    acc[i].onclick = function(){
      <!-- BEGIN VALORES_SLIDER -->
      if ({__valor_slider_inicio} == 0 || {__valor_slider_termino} == 0) {
        var hora_inicio = parseInt(document.getElementsByClassName('slider-time-hour1')[0].value);
        var minuto_inicio = parseInt(document.getElementsByClassName('slider-time-minute1')[0].value);
        var hora_termino = parseInt(document.getElementsByClassName('slider-time-hour2')[0].value);
        var minuto_termino = parseInt(document.getElementsByClassName('slider-time-minute2')[0].value);
        var slider_inicio = (hora_inicio * 60) + minuto_inicio;
        var slider_termino = (hora_termino * 60) + minuto_termino;
        var slider_inicio_filtro = slider_inicio;
        var slider_termino_filtro =slider_termino;
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
      } else {
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
    slide: function (event, ui) {
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
        var x= parseInt(hours1);
        var y=parseInt(minutes1)
        //SE CAMBIAN LOS VALORES DE LOS INPUT MIENTRAS SE ARRASTRA EL SLIDER
        $('.slider-time-hour1').val(x);
        $('.slider-time-minute1').val(y);

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
        var a = parseInt(hours2);
        var b = parseInt(minutes2);
        $('.slider-time-hour2').val(a);
        $('.slider-time-minute2').val(b);


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

//SE ELIMINA LA OPCION SELECCIONADA EN EL COMBOBOX DEL FILTRO
//Y SE AGREGA ATRIBUTO SELECTED AL NODO SELECCIONADO

</script>
<style type="text/css">
/* ACORDEON */
input.accordion2 {
    background-color: #F47001;
    color: #fff;
    cursor: pointer;
    padding: 3px;
    width: 25%;
    text-align: center;
    font-size: 16px;
    border: none;
    outline: none;
    -webkit-border-radius: 5;
    -moz-border-radius: 5;
    border-radius: 5px;
    /*transition: 0.3s;*/
}

input.accordion2.active {
     background-color: #f47001;
}
input.accordion2:hover {
     background-color: #FFC99D;
}

div.panel2 {
    padding: 0 18px;
    max-height: 0;
    overflow: hidden;
    transition: max-height .2s ease-out;
}
/* FIN ACORDEON */

/* SLIDER */
.flat-slider.ui-corner-all,
.flat-slider .ui-corner-all {
  border-radius: 0;
}

.flat-slider.ui-slider {
  border: 0;
  background: #f8e5e2;
  border-radius: 7px;
}

.flat-slider.ui-slider-horizontal {
  height: 6px;
}

.flat-slider .ui-slider-handle {
  width: 20px;
  height: 20px;
  background: #f47001;
  border-radius: 50%;
  border: none;
  cursor: pointer;
}

.flat-slider.ui-slider-horizontal .ui-slider-handle {
  top: 50%;
  margin-top: -10px;
}

.flat-slider.ui-slider-vertical .ui-slider-handle {
  left: 50%;
  margin-left: -10px;
}

.flat-slider .ui-slider-handle:hover {
  opacity: 1;
}

.flat-slider .ui-slider-range {
  border: 0;
  border-radius: 7;
  background: #f38e46;
}

.flat-slider.ui-slider-horizontal .ui-slider-range {
  top: 0;
  height: 6px;
}
/* FIN SLIDER */

.spinner {
  margin: auto;
  width: 40px;
  height: 40px;
  position: relative;
  text-align: center;

  -webkit-animation: sk-rotate 2.0s infinite linear;
  animation: sk-rotate 2.0s infinite linear;
}

.dot1, .dot2 {
  width: 60%;
  height: 60%;
  display: inline-block;
  position: absolute;
  top: 0;
  background-color: #f47001;
  border-radius: 100%;

  -webkit-animation: sk-bounce 2.0s infinite ease-in-out;
  animation: sk-bounce 2.0s infinite ease-in-out;
}

.dot2 {
  top: auto;
  bottom: 0;
  -webkit-animation-delay: -1.0s;
  animation-delay: -1.0s;
}

@-webkit-keyframes sk-rotate { 100% { -webkit-transform: rotate(360deg) }}
@keyframes sk-rotate { 100% { transform: rotate(360deg); -webkit-transform: rotate(360deg) }}

@-webkit-keyframes sk-bounce {
  0%, 100% { -webkit-transform: scale(0.0) }
  50% { -webkit-transform: scale(1.0) }
}

@keyframes sk-bounce {
  0%, 100% {
    transform: scale(0.0);
    -webkit-transform: scale(0.0);
  } 50% {
    transform: scale(1.0);
    -webkit-transform: scale(1.0);
  }
}
</style> 

<div id="elementos_plus_filtro" style="display: none;">
 <input type="button" class="accordion2" style="float: center;" value="Filtrar contenido"></input>
   <div class="panel2" style="background-color: #f6f6f6;">
      <br />
      <div id="time-range" style="width:90%; padding-left: 30px;">
         <div>
            <a>ISP: </a>
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
            <div id="formbutton">
            <input type="button" class="boton_accion" value="Filtrar" style="cursor: pointer" onClick="filtroElementosPlus({__item_id}, 1);">
            </div>
            <div id="button2" style="margin-left:15px; display:none; height: 22px">
            <img src="img/cargando.gif">
            </div>
            <br>
         </div>
      </div>
   </div>
   <br>
</div>
<!-- BEGIN LISTA_CONTENEDORES -->
      <div dojoType="dojox.layout.ContentPane" id="subcontenedor_{__contenido_id}" style="height: auto;">
    {__contenido_tabla}
      </div>
<!-- END LISTA_CONTENEDORES -->
