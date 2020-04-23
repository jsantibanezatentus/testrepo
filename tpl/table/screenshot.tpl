<link rel="stylesheet" href="{__path_jquery_ui}css/ui-lightness/jquery-ui-1.8.17.custom.css"></link>
<script type="text/javascript" src="{__path_jquery_ui}js/jquery-ui-1.8.17.custom.min.js"></script>
<div>
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
						<a>Hora Inicio: </a>
						<input type="number" class="slider-time-hour1" name="hora_inicio" value="{__hora_inicio}" min="0" max="23" style='width:35px; height:20px'>
						<a> : </a>
						<input type="number" class="slider-time-minute1" name="minuto_inicio" value="{__minuto_inicio}" min="0" max="59" style='width:35px; height:20px'>
						<a>&nbsp; &nbsp;</a>
						<a>Hora Término: </a>
						<input type="number" class="slider-time-hour2" name="hora_termino" value="{__hora_termino}" min="0" max="23" style='width:35px; height:20px'>
						<a> : </a>
						<input type="number" class="slider-time-minute2" name="minuto_termino" value="{__minuto_termino}" min="0" max="59" style='width:35px; height:20px'>
						<!-- END VALORES_FILTRO -->
						<br>
				</div>
		</div>
		<div id="formbutton">
		<input type="button" class="boton_accion" value="Filtrar" style="cursor: pointer" onClick="filtroScreenshot({__item_id}, 1);">
		</div>
		<div id="button2" style="margin-left:30px; display:none;">
        <img src="img/cargando.gif">
        </div>
		<br>
</div>
</div>
<br>
<div dojoType="dijit.Dialog" id="dialog_screenshot" title="Detalle Screenshot">
<table width="750" class="formulario">
	<tr>
		<th width="120">Fecha</th>
		<td id="screenshot_fecha"></td>
	</tr>
	<tr>
		<th>Monitor</th>
		<td id="screenshot_nodo"></td>
	</tr>
	<tr>
		<th>Paso</th>
		<td id="screenshot_paso"></td>
	</tr>
	<tr>
		<th>Estado</th>
		<td id="screenshot_estados"></td>
	</tr>
	  
	<tr>
		<td colspan="2">
			<div style="margin-bottom: -25px; margin-right: -25px; background-color:#eeeeee; padding: 3px; position: relative; z-index:1; filter:alpha(opacity=60); float:left; -moz-opacity:.60; opacity:.60">
                            <i class="spriteButton spriteButton-lupa-mas" id="aumentar_screenshot" onclick="cambiarTamanoScreenshot();"></i>
                            <i class="spriteButton spriteButton-lupa-menos" id="disminuir_screenshot" style="display: none;"  onclick="cambiarTamanoScreenshot();"></i>
                        </div>
			<div style="width:740px; height:330px; overflow:auto; padding: 5px; text-align: center;" id="screenshot_scroll">
				<img id="screenshot_cargando" src="img/cargando.gif" />
				<img id="screenshot_imagen" style="display: none;" onload="onLoadImagen(this);" />
			</div>
		</td>
	</tr>
</table>
</div>

<!-- BEGIN BLOQUE_MONITOREOS -->
<table width="100%" style="margin-bottom:10px;">
	<tr>
		<td class="celdanegra40" width="100%">
			<table>
				<tr>
					<td width="180" class="textblanco12">{__monitoreo_fecha}</td>
					<td width="200" class="textblanco12b">{__nodo_nombre}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>

		<td style="background-color: #e2e2e2;">
			<div id="btn_atras_{__monitoreo_id}" onclick="scrollBotonAtras('{__monitoreo_id}');" style="display: none; cursor: pointer; float: left; position: relative; margin-right: -20px; width: 20px; height:150px; background-color: #c7c7c7; text-align: center;"><img style="padding-top: 60px;" src="img/botones/flecha_izq2.png"/></div>
			<div id="monitoreo_{__monitoreo_id}" style="float: left; width:720px; overflow:hidden;">
			<table width="{__monitoreo_ancho}" id="monitoreo_tabla_{__monitoreo_id}">
				<tr>
					<!-- BEGIN LISTA_PASOS -->
					<td style="padding: 10px;">
						<table>
							<tr>
								<td class="textblanco12" style="padding: 2px; background: {__paso_warning} #{__paso_color};" title="{__paso_nombre}">
									<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: {__paso_width};">{__paso_nombre}</div>
								</td>
							</tr>
							<tr>
								<td style="background-color: #f0f0f0; width: 130px; height: 100px;" align="center" valign="middle">
									<!-- BEGIN LISTA_SCREENSHOT -->
									<div name="screenshot_{__monitoreo_id}_{__paso_id}" id="screenshot_{__monitoreo_id}_{__paso_id}_{__screenshot_id}" style="display: {__screenshot_display};">
										<img src="imagen_screenshot.php?objetivo_id={__objetivo_id}&monitor_id={__nodo_id}&fecha={__monitoreo_fechacompleta}&paso={__paso_id}&tipo=screenshot&window={__screenshot_window}" onclick="mostrarScreenshot('{__monitoreo_id}', '{__paso_id}', '{__screenshot_window}');" />
  									</div>
  									<!-- END LISTA_SCREENSHOT -->
  								</td>
							</tr>
							<tr>
								<td align="center" style="padding-bottom: 5px; background-color: #f0f0f0;">
									<table>
										<tr>
											<!-- BEGIN LISTA_SCREENSHOT_BULLET -->
											<td width="15" align="center" onclick="cambiarScreenshot('{__monitoreo_id}', '{__paso_id}', '{__screenshot_id}');" title="{__screenshot_title}">
												<div name="bullet_{__monitoreo_id}_{__paso_id}" id="bullet_{__monitoreo_id}_{__paso_id}_{__screenshot_id}" style="width:4px; height:4px; font-size: 0; border: 1px solid #{__paso_color}; cursor: pointer; background-color: {__bullet_color};" />
											</td>
											<!-- END LISTA_SCREENSHOT_BULLET -->
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<!-- END LISTA_PASOS -->
				</tr>
			</table>
			</div>
			<div id="btn_adelante_{__monitoreo_id}" onclick="scrollBotonAdelante('{__monitoreo_id}');" style="display: {__boton_adelante_display}; cursor: pointer; float: right; margin-left: -20px; width: 20px; height:150px; background-color: #c7c7c7; text-align: center;"><img style="padding-top: 60px;" src="img/botones/flecha_der2.png"/></div>
		</td>
	</tr>
</table>

<!-- END BLOQUE_MONITOREOS -->
<div style="display: {__pagina_filtro}">
<table align="right" class="celdabordederecha">
	<tr>
		<td><input type="button" class="{__class_boton_atras}"
			{__disabled_atras}
			 onClick="filtroScreenshot({__item_id}, {__pagina_atras}); return false;">
		</td>
		<td class="celdanegra50" width="20" align="center">{__pagina}</td>
		<td><input type="button" class="{__class_boton_adelante}"
			id="boton_adelante_{__monitor_id}"
			{__disabled_adelante}
			 onClick="filtroScreenshot({__item_id}, {__pagina_adelante}); return false;">
		</td>
	</tr>
</table>
</div>

<script>
$(document).ready(function(){
	$("option").removeAttr("selected");
	$("#nodos_filtros_"+{__nodo_selected}).attr('selected', 'selected');
	if ({__isset} == 0) {
		$(".panel2").css('max-height', '211px');
	}
});

function cambiarScreenshot(monitoreo_id, paso_id, screenshot_id) {
	var bullet_color;
	var screenshot = getElementsByName_iefix("div", "screenshot_" + monitoreo_id + "_" + paso_id);
	var bullet = getElementsByName_iefix("div", "bullet_" + monitoreo_id + "_" + paso_id);

	for (i=0; i<screenshot.length; i++) {
		screenshot[i].style.display = "none";
	}

	for (i=0; i<bullet.length; i++) {
		if (bullet[i].style.backgroundColor != '' && bullet[i].style.backgroundColor != 'inherit') {
			bullet_color = bullet[i].style.backgroundColor;
		}
		bullet[i].style.backgroundColor = "";
	}

	document.getElementById("screenshot_" + monitoreo_id + "_" + paso_id + "_" + screenshot_id).style.display = "inline";
	document.getElementById("bullet_" + monitoreo_id + "_" + paso_id + "_" + screenshot_id).style.backgroundColor = bullet_color;
}

function onLoadImagen(imagen) {
	dojo.byId('screenshot_cargando').style.display = 'none';

	imagen.style.display = 'inline';
	imagen.style.width = "auto";
	if (imagen.width > 720) {
		imagen.style.width = "720";
	}
}

//SE ELIMINA LA OPCION SELECCIONADA EN EL COMBOBOX DEL FILTRO
//Y SE AGREGA ATRIBUTO SELECTED AL NODO SELECCIONADO
function nodo_selected(a){
$("option").removeAttr("selected");
$("#"+a).attr('selected', 'selected');
}

function filtroScreenshot(item_id, pagina){
	//SE OBTIENE EL VALOR DE HORA Y MINUTO DEL SLIDER, INICIO Y TERMINO
	var hora_inicio = document.getElementsByClassName('slider-time-hour1')[0].value;
	var minuto_inicio = document.getElementsByClassName('slider-time-minute1')[0].value;
	var hora_termino = document.getElementsByClassName('slider-time-hour2')[0].value;
	var minuto_termino = document.getElementsByClassName('slider-time-minute2')[0].value;

	//OBTIENE EL ID DEL NODO SELECCIONADO
	var id_nodo = $( "#select_filtro option:selected" ).data('id');

	var data_filtro = [hora_inicio, minuto_inicio, hora_termino, minuto_termino, id_nodo];
	cargaItemFiltroScreenshot('contenedor_'+item_id, item_id, 0, ['pagina', pagina], data_filtro);
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

function mostrarScreenshot(monitoreo_id, paso_id, window) {
	dojo.byId("screenshot_imagen").style.display = "none";
	dojo.byId("screenshot_cargando").style.display = "inline";
	document.getElementById("disminuir_screenshot").style.display="none";
	document.getElementById("aumentar_screenshot").style.display="inline";
	var datos = monitoreos[monitoreo_id][paso_id];
	var estados = "";
	var patrones = 0;

	if (window == 'main') {
		txt_window = "Ventana Principal";
	}
	else if (window == 'popup') {
		txt_window = "Ventana Popup";
	}
	else {
		txt_window = "Desconocido";
	}

	for (key in monitoreos[monitoreo_id][paso_id]["patrones"]) {
		if (key != "-1") {
			estados = estados + '<table><tr><td align="center" width="80" height="22" style="border: 0px; background-color: #' + datos["patrones"][key]["color"] + '"><i class="' + datos["patrones"][key]["icono"] + '" /></i></td><td style="border: 0px;"><b>&nbsp;&nbsp;Patr&oacute;n:</b>&nbsp;' + datos["patrones"][key]["nombre"] + '</td></tr></table>';
			patrones++;
		}
	}

	dojo.byId("screenshot_fecha").innerHTML = datos["fecha"];
	dojo.byId("screenshot_nodo").innerHTML = datos["nodo"];
	dojo.byId("screenshot_paso").innerHTML = datos["paso"] + " (" + txt_window + ")";
	dojo.byId("screenshot_estados").innerHTML = estados;
	if (windowHeight < 500) {
		dojo.byId("screenshot_scroll").style.height = (220 - (patrones*20));
	}
	else {
		dojo.byId("screenshot_scroll").style.height = (350 - (patrones*20));
	}
	dojo.byId("screenshot_imagen").src = 'imagen_screenshot.php?objetivo_id={__objetivo_id}&monitor_id=' + datos["nodo_id"] + '&fecha=' + datos["fecha_completa"] + '&paso=' + paso_id + '&tipo=full&window=' + window;

	dijit.byId("dialog_screenshot").show();
}

monitoreos = {

	<!-- BEGIN BLOQUE_MONITOREOS_SCRIPT -->
	'{__monitoreo_id}': {

		<!-- BEGIN LISTA_PASOS_SCRIPT -->
		'{__paso_id}': {
			'fecha_completa': '{__monitoreo_fechacompleta}',
			'fecha': '{__monitoreo_fecha}',
			'nodo_id': '{__nodo_id}',
			'nodo': '{__nodo_nombre}',
			'paso': '{__paso_nombre}',
			'patrones': {
				<!-- BEGIN LISTA_PATRONES_SCRIPT -->
				'{__patron_id}': {
						'nombre': '{__patron_nombre}',
						'icono': '{__estado_icono}',
						'color': '{__estado_color}'
				},
				<!-- END LISTA_PATRONES_SCRIPT -->
				'-1': 'default'
			}
		},
		<!-- END LISTA_PASOS_SCRIPT -->
		'-1': 'default'
	},
	<!-- END BLOQUE_MONITOREOS_SCRIPT -->
	'-1': 'default'
}

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
    -webkit-border-radius: 7;
    -moz-border-radius: 7;
    border-radius: 7px;
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

<script>
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
</script>