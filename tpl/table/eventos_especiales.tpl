<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>
<link rel="stylesheet" href="css/galeria_sc.css"></link>

<table width="100%">
	<tr>
		<td style="border: solid 1px #ffffff;" class="celdanegra50">{__monitor_nombre}</td>
	</tr>
</table>
<table width="100%">
	<tr id="td_{__monitor_id}_empty">
		<td style="border: solid 1px #ffffff;" class="celdanegra40"  width="28%">{__objetivo_nombre}</td>
		<!-- BEGIN LISTA_EVENTOS_INICIO -->
		<td style="border: solid 1px #ffffff;" class="{__evento_style}" align="center" width="12%">{__evento_inicio}</td>
		<!-- END LISTA_EVENTOS_INICIO -->
	</tr>
	<!-- BEGIN LISTA_PASOS -->
	<tr>
		<td style="border: solid 1px #ffffff;" width="28%" class="{__estiloPaso} " valign="top" id="paso_{__evento_tooltip_id}_{__monitor_id}" title="{__paso_nombre_completo}">{__paso_nombre}</td>
		<!-- BEGIN LISTA_EVENTOS -->
		<td style="border: solid 1px #ffffff;" bgcolor="#c4c4c4" width="12%" >
			<table width="100%" >
				<!-- BEGIN LISTA_EVENTOS_PATRONES -->
				<tr>
					<td height="22" bgcolor="#{__evento_color}" align="center" id="evento_{__evento_tooltip_id}_{__monitor_id}">
						<i class="{__evento_icono}"></i>

						<!-- TOOLTIP -->
						<div dojoType="dijit.Tooltip" connectId="evento_{__evento_tooltip_id}_{__monitor_id}" position="below">
							<table >
								<tr>
									<td colspan="100%" class="textnegro13" height="22" valign="top">{__evento_nombre}</td>
								</tr>
								<tr>
									<td>
										<table>
											<tr>
												<td align="center" width="80" height="22" bgcolor="#{__evento_color}" >
													<i class="{__evento_icono}"></i>
												</td>
											</tr>
										</table>
									</td>
									<td>&nbsp;</td>
									<td width="170" class="textnegro12">{__evento_descripcion}</td>
								</tr>
								<!-- BEGIN BLOQUE_PATRON -->
								<tr>
									<td height="10"></td>
								</tr>
								<tr>
									<td colspan="100%" class="textnegro13">Patron:</td>
								</tr>
								<tr>
									<td colspan="100%" class="textnegro12">{__patron}</td>
								</tr>
								<!-- END BLOQUE_PATRON -->
							</table>
						</div>
					</td>
				</tr>
				<!-- END LISTA_EVENTOS_PATRONES -->
			</table>
		</td>
		<!-- END LISTA_EVENTOS -->
	</tr>
	<!-- END LISTA_PASOS -->
	<tr>
		<td style="border: solid 1px #ffffff;" class="celdaduracion" width="28%" height="35">&nbsp;</td>
		<!-- BEGIN LISTA_EVENTOS_DURACION -->
		<td style="border: solid 1px #ffffff;" class="celdaduracion" align="center" width="12%" height="35">{__evento_duracion}</td>
		<!-- END LISTA_EVENTOS_DURACION -->
	</tr>
	<tr id="td_{__id_monitor}" data ="td{__id_monitor}"></tr>
	<tr id="td_elem_{__id_monitor}" data ="td_elem_{__id_monitor}"></tr>
</table>
<table align="right" class="celdabordederecha">
	<tr>
		<td>
			<input type="button"  class="{__class_boton_atras}"  {__disabled_atras}
			 onClick="cargarItem('subcontenedor_even_{__monitor_id}', '{__item_id}', '0', ['monitor_id', '{__monitor_id}', 'pagina', '{__pagina_atras}']); return false;">
		</td>
		<td class="celdanegra50" width="20" align="center">{__pagina}</td>
		<td>
			<input type="button" class="{__class_boton_adelante}" id="boton_adelante_{__monitor_id}" {__disabled_adelante}
			 onClick="cargarItem('subcontenedor_even_{__monitor_id}', '{__item_id}', '0', ['monitor_id', '{__monitor_id}', 'pagina', '{__pagina_adelante}']); return false;">
		</td>
	</tr>
</table>
<br>
<br>							
<!--                MODAL SCREENSHOT         -->
<div dojoType="dijit.Dialog" id="sc{__monitor_nombre}" title = "Galeria Screenshot {__monitor_nombre}" style="font-family: Verdana,Arial,Helvetica,sans-serif;">
	<table>
	    <tr>
	        <td width="900px;" height="500px;" style="padding: 3px" title="">
				<div class="slideshow-container">
					<div id="slides" style =" overflow: scroll" data ="slides_{__id_monitor}"></div>
					<div align="center"><a href="#" onclick="abrirEnlace('1','92','{__obj}');" class="boton_accion" > Ir a Seccion Screenshot</a></div>
				</div>
				<br>
				<div style="padding-left: 380px;" id="" data="count_slides_{__id_monitor}"></div>
			</td>
		</tr>
	</table>
</div>
<style>
.dijitContentPane {
    overflow: hidden !important;
}
</style>

<script>

t = 'fz';
patron_cdn='{__patron_cdn}';
servicio = '{__servicio}';
var paso_evento=[];
var monitor_id= '{__monitor_id}'
<!-- BEGIN LISTA_PASOS_EVENTOS -->
paso_evento += '{{__id_paso_evento},{__eventos_paso}},'
<!-- END LISTA_PASOS_EVENTOS -->

pos = paso_evento.lastIndexOf(',');
cambio ='';
paso_evento = paso_evento.substring(0,pos) + cambio + paso_evento.substring(pos+1)

contador_td=0
<!-- BEGIN LISTA_EVENTOS_BOTON -->
contador_td++
 // SETEO VARTIABLES
var hora_inicio_utc= ('{__hora_inicio_tz}'.split("+"))[0]
var hora_termino_utc=('{__hora_termino_tz}'.split("+"))[0]
var monitor = '{__id_monitor}'
var objetivo = '{__obj}'
var td = $('<td data="button" />')
var nombre_monitor= '{__nombre_nodo}'
var ok = '{__evento_ok}'
var paso ='{__evento_cdn}'
pos = paso.lastIndexOf(',');
cambio ='';
paso = paso.substring(0,pos) + cambio + paso.substring(pos+1)
dato=monitor
var segundo_paso=paso
//CREACION BOTONES
paso= "'"+paso+"'";
button = $('<td align="center"><div><input type="button" class="spriteButton spriteButton-abrir_popup" style ="width:25%; background-color: #f47001;color: #fff; border-radius:3px" id="{__id_monitor}_{__contador}" title ="Screenshot" name="" value="" onclick="mostrarScreenshot('+monitor+','+paso+','+"'"+hora_inicio_utc+"'"+','+"'"+hora_termino_utc+"'"+','+objetivo+','+"'"+nombre_monitor+"',"+"'"+paso_evento+"'"+')"></div></td>')
$('#td_{__id_monitor}').append(button)

  //COMPROBACION AJAX DE DATA SIN SCREENSHOT
empty=''
paso=segundo_paso
if(paso!=empty){
	$.ajax({
		async: false,
		type: "POST",
		url: "utils/get_last_image_evento.php",
		data: {'datos':dato, monitor, paso, hora_inicio_utc, hora_termino_utc, objetivo},
		success: function(data) {
			if(data=='[]'||'{__codigo_id}'=='5'){
				$("#{__id_monitor}_{__contador}").remove()
			}
		}
	})
}
<!-- END LISTA_EVENTOS_BOTON -->
for (var i = contador_td; i <=5; i++) {
	$('#td_{__monitor_id}_empty').append('<td style="border: solid 1px #ffffff;" class="celdaenblanco" align="center" width="12%" rowspan="100%">&nbsp;</td>')
}
		//ESPACIOS INICIALES DE LA TABLE
$('#td_{__id_monitor}').prepend('<td />')

		//GALERIA
function mostrarScreenshot(monitor, paso, hora_inicio_utc, hora_termino_utc, objetivo, nombre_monitor, paso_evento){
	paso_div=paso.split(",")
	paso_evento=paso_evento.split("},")
	cont=0;
	$('div[data=slides_'+monitor+']').empty();
	$('div[data=cdn_'+cont+']').empty();
	$("div").remove("#mySlides");
	$("span").remove("#dots");
	dato =monitor
	$.ajax({
		async: false,
		type: "POST",
		url: "utils/get_last_image_evento.php",
		data: {'datos':dato, monitor, paso, hora_inicio_utc, hora_termino_utc, objetivo},
		success: function(data) {
			//console.log(data+'----data')
			json = JSON.parse(data);
			json = json[0]['detalle_fz'];
			monitor_json = JSON.stringify((json.split(",")).slice(1,2));
			json = JSON.stringify((json.split(",")).slice(3,100));
			json = ((json.replace("{"," ")).replace("}"," ")).replace(')', '')
			json = JSON.parse(json);
			$.each(json, function(index, token){
				token= token.replace('"', '');
				token = token.trim();
				cont=0;
				img=0
				$.each(paso_evento, function(index_array, pasos_text){
					pasos_text = (pasos_text.replace("{", "")).split(",")
					if(pasos_text[0]==index&&token!="NULL"){
						texto_pasos=(((pasos_text[1].replace(",",'')).replace("}",'')))
						//console.log('get_remote_image.php?token='+token+'&t='+t+'&servicio='+servicio)
						img = $('<img />').attr({'src':'/utils/get_remote_image.php?token='+token+'&t='+t+'&servicio='+servicio, 'id':'img_'+token.id, 'onError':'this.onerror=null;this.src="/img/screenshot_error.png"'}).on('load', function() {
				    		$("div[id=token_"+token.id+"]").attr('style', 'display: none');
							$(token).append(img);
						});
						img.attr('style','width:100%; display:block; margin:auto; padding-top:40px;');
						$.each(img, function(x,im){
							div= $('<div class="mySlides fade'+cont+' data="mySlides" id="mySlides" />').on('load', function(){
								div.attr('style, display:none;')
							})
							cdn = $('<div id="cdn" style="height:400px;" data="cdn_'+cont+'"/>').on('load', function(){
								cdn.attr('style, display:none;')
							})
							text = $('<div id="text" class="celdanegra50" align="center">'+texto_pasos+'</div>')
							count_slides = ('<span class="dot" id="dots" onclick="currentSlide('+cont+')" />')
							$(div).append(text);
							$(div).append(cdn);
							$('div[data=slides_'+monitor+']').append(div);
							$('div[data=cdn_'+cont+']').append(im);
							$('div[data=count_slides_'+monitor+']').append(count_slides)
						})
					}
					cont++;
				})
			})
		}
	})
	dijit.byId("sc"+nombre_monitor).show();

	var prev = $('<a class="prev" onclick="plusSlides(-1)">&#10094 </a>')
	var next = $('<a class="next" onclick="plusSlides(1)">&#10095 </a>')
	$('div[data=slides_'+monitor+']').append(prev);
	$('div[data=slides_'+monitor+']').append(next);

	var slideIndex = 1;
	showSlides(slideIndex);
	// Next/previous controls
	window.plusSlides=function plusSlides(n) {
	  showSlides(slideIndex += n);
	}
	// Thumbnail image controls
	function currentSlide(n) {
	  showSlides(slideIndex = n);
	}
	function showSlides(n) {
	  var i;
	  var slides = document.getElementsByClassName("mySlides");
		//console.log(slides)
	  var dots = document.getElementsByClassName("dot");
	  if (n > slides.length) {
	  	slideIndex = 1
	  } 
	  if (n < 1) {
	  	slideIndex = slides.length
	  }
	  for (i = 0; i < slides.length; i++) {
	      slides[i].style.display = "none"; 
	  }
	  for (i = 0; i < dots.length; i++) {
	      dots[i].className = dots[i].className.replace(" active", "");
	  }
	  slides[slideIndex-1].style.display = "block";
	  dots[slideIndex-1].className += " active";
	}

}
</script>
