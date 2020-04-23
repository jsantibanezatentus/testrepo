<link rel="stylesheet" href="css/especial_banco_chile.css" type="text/css"/>
<tr>
	<td>
		<table width="100%">
			<tr>
				<td>
					<div>
						<img style="max-width: 100%; height: auto;" src="/img/especiales/banco_chile_especial.png">
					</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top: 15px;">
					<ul>
						<li class="leyendaDescripcion"><a style="font-weight: bold;">Uptime: </a>La funcionalidad responde correctamente al menos en un ISP con servicio.</li>
						<li class="leyendaDescripcion"><a style="font-weight: bold;">Downtime: </a>Todos los ISP's detectaron indisponibilidad en un mismo periodo.</li>
						<li class="leyendaDescripcion"><a style="font-weight: bold;">Tiempo de respuesta: </a>Tiempo de respuesta promedio en segundos.</li>
						<li class="leyendaDescripcion" id="il_cat"><a style="font-weight: bold;">Resumen de los ISPs: </a>
							<div id="ley_cat">
								<!-- BEGIN BLOQUE_CATEGORIAS_2 -->
								<ul>
									<li>
										<a style="font-weight: bold;">{__nombre_categoria}: </a>{__nodo_mm_cat}
									</li>
								</ul>
								<!-- END BLOQUE_CATEGORIAS_2 -->
							</div>
						</li>
					</ul>
				</td>
			</tr>
		</table>
	</td>
	<td>
		<table width="100%">
			<tr>
				<td>
					<div id="calendario_especial">
						<img class="indicador-carga" src="/img/cargando.gif" title="cargando calendario" alt="cargando calendario" />
					</div>
				</td>
			</tr>
			<tr>
				<td align="center">
					<div id="formbutton">
						<input type="button" value="Generar Reporte" class="boton_accion" onclick="actualizaPopup('{__objetivo_especial}', '{__horario_id}', '{__usuario_id}');"/>
					</div>
					<div id="button2" style="height: 22px; display: none;">
						<div class="spinner">
						    <div class="rect1"></div>
						    <div class="rect2"></div>
						    <div class="rect3"></div>
						    <div class="rect4"></div>
						    <div class="rect5"></div>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr><td height="20px"></td></tr>
<tr id="vru">
	<td colspan="2" id="td_vru">
		<table width="100%">
			<tr>
				<td height="25px" style="border-left: solid 1px #fff; border-top: solid 1px #fff; border-bottom: solid 1px #fff;" align="left" class="txtBlanco12b celdaTituloNaranjoEspecial2"></td>
				<td align="center"style="border: solid 1px #fff;" class="txtClaro10bEspecial celdaTituloNaranjo">UPTIME</td>
				<td align="center"style="border: solid 1px #fff;" class="txtClaro10bEspecial celdaTituloNaranjo">DOWNTIME</td>
				<td align="center"style="border: solid 1px #fff;" class="txtClaro10bEspecial celdaTituloNaranjo">TIEMPO DE RESPUESTA [segs]</td>
			</tr>
			<!-- BEGIN BLOQUE_CATEGORIAS -->
			<tr>
				<td class="txtNegro14bEspecialBold categoria" width="40%" >{__nombre_categoria}</td>
				<td class="txtNegro14bEspecialBold categoria tooltip" width="20%" align="center">{__uptime_real_total} %
					<table class="uptime" style="width: 90%;">
						<tr>
							<td style="text-align: center;">{__tiempo_porcentaje_uptime_cat}</td>
						</tr>
					</table>
				</td>
				<td class="txtNegro14bEspecialBold categoria tooltip" width="20%" align="center">{__downtime_real_total} %
					<table class="downtime" style="width: 90%;">
						<tr>
							<td style="text-align: center;">{__tiempo_porcentaje_downtime_cat}</td>
						</tr>
					</table>
				</td>
				<td class="txtNegro14bEspecialBold categoria" width="20%" align="center">{__tiempo_respuesta_total}</td>
			</tr>
			<!-- END BLOQUE_CATEGORIAS -->
			<tr>
				<td height="30px"></td>
			</tr>
		</table>
		<!-- BEGIN BLOQUE_CATEGORIA -->
		<div style="{__page_break}" id="div_vru_cat">
				<table width="100%" id="table_cat">
					<tr id="tr_cat">
						<div>
							<td height="25px" align="left" class="txtNegroClaro14bEspecialBold celdaTituloNaranjoEspecial">{__nombre_categoria}</td>
							<td height="25px" align="center" class="txtBlanco12b celdaTituloNaranjoEspecial tooltip" >{__uptime_real_total} %
								<table class="uptime" style="width: 150%;">
									<tr>
										<td style="text-align: center;">{__tiempo_porcentaje_uptime_cat}</td>
									</tr>
								</table>
							</td>
							<td height="25px" style="border: solid 1px #cccccc;" align="center" class="txtNegro10bEspecial">UPTIME</td>
							<td height="25px" style="border: solid 1px #cccccc;" align="center" class="txtNegro10bEspecial">DOWNTIME</td>
							<td height="25px" style="border: solid 1px #cccccc;" align="center" class="txtNegro10bEspecial">TIEMPO DE RESPUESTA [segs]</td>
						</div>
					</tr>
					<!-- BEGIN BLOQUE_FUNCIONALIDAD -->
					<tr class="acordeon" style="border-style: outset; border-color: #fff; cursor: pointer;" id="tr_func">
						<td class="txtNegro14bEspecialBold {__class_iteracion} tooltip" width="40%" colspan="2">{__nombre_funcionalidad}</td>
						<td class="txtNegro14bEspecialBoldFuncionalidad {__class_iteracion} tooltip" width="20%" align="center">{__uptime_real} %
							<table class="uptime" style="width: 90%;">
								<tr>
									<td style="text-align: center;">{__tiempo_porcentaje_uptime_func}</td>
								</tr>
							</table>
						</td>
						<td class="txtNegro14bEspecialBoldFuncionalidad {__class_iteracion} tooltip" width="20%" align="center">{__downtime_real} %
							<table class="downtime" style="width: 90%;">
								<tr>
									<td style="text-align: center;">{__tiempo_porcentaje_downtime_func}</td>
								</tr>
							</table>
						</td>
						<td class="txtNegro14bEspecialBoldFuncionalidad {__class_iteracion}" width="20%" align="center">{__tiempo_respuesta}</td>
					</tr>
					<tr class="panel" id="tr_paso">
						<td colspan="5" >
							<table width="100%">
								<!-- BEGIN BLOQUE_PASO -->
								<tr>
									<td>
										<table width="100%">
											<tr>
												<td class="txtNegro12bEspecialPaso tooltip" width="40%" colspan="2"  style=" max-width: 250px; padding: 1px 6px 1px 25px; background: #{__class_iteracion_paso}; color: {__color_text};" onmouseover="document.getElementById('{__screenshot_hash}').style.display = 'inline'" onmouseout="document.getElementById('{__screenshot_hash}').style.display = 'none'">
													<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; cursor: default;">
														{__nombre_paso} {__nombre_objetivo}
													</div>
													<table class="{__class_tooltip_paso}" id="{__screenshot_hash}"> <!-- /////////////////// SCREENSHOT /////////////////// -->
														<tr>
															<td valign="center" style="text-align: center; ">
																<table width="100%">
																	<tr>
																		<td colspan="2">
																			<div id="{__screenshot_hash}" data='cdn' style="width: 100%; display: {__muestra_screenshot};"></div>
																		</td>
																	</tr>
																	<tr>
																		<td style="text-align: center; font-weight: bold;" valign="center">
																			<a style="color: #3a4c4c">ISPs: {__cant_nodo_paso}</a>
																			<a style="color: white;">{__nombre_paso}  {__nombre_objetivo_tooltip}</a>
																		</td>
																		<td style="text-align: center; padding-top: 6px; font-weight: bold; width: 20%; display: {__muestra_descarga};" valign="center">
																			<a class="descarga"  href="utils/screenshot_especial.php?token={__screenshot_hash}" download="{__nombre_categoria}_{__nombre_funcionalidad}_{__nombre_objetivo_tooltip}_{__nombre_paso}">
																				<img src="/img/download.png">
																			</a>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td>
																<table width="100%" align="center" style="color: white;">
																	<tr>
																		<td style="font-size: 12px;">Uptime</td>
																		<td style="font-size: 12px; text-align: right;">{__uptime_real_paso} %</td>
																	</tr>
																	<tr style="background-color: #868686;">
																		<td style="font-size: 12px;">Downtime</td>
																		<td style="font-size: 12px; text-align: right;">{__downtime_real_paso} %</td>
																	</tr>
																	<tr>
																		<td style="font-size: 12px;">T. Respuesta</td>
																		<td style="font-size: 12px; text-align: right;">{__tiempo_respuesta_paso} segs</td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td>
																<table width="100%">
																	<tr>
																		<td align="center">
																			<input type="button" style="display:{__valid}" name="flujo" value="Secuencia" onclick="getFlujo('{__pasos_flujo}', '{__flujo}',      document.getElementById('{__screenshot_hash}')     )" class="myButton"/>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>											
												</td>
												<td class="txtNegro12bEspecialPaso tooltip" style="background: #{__class_iteracion_paso}; color: {__color_text}; padding-left: 25px" width="20%" align="center">{__uptime_real_paso} %
													<table class="uptime" style="width: 90%;">
														<tr>
															<td style="text-align: center;">{__tiempo_porcentaje_uptime}</td>
														</tr>
													</table>
												</td>
												<td class="txtNegro12bEspecialPaso tooltip" style="background: #{__class_iteracion_paso}; color: {__color_text}; padding-left: 25px" width="20%" align="center">{__downtime_real_paso} %
													<table class="downtime" style="width: 90%;">
														<tr>
															<td style="text-align: center;">{__tiempo_porcentaje_downtime}</td>
														</tr>
													</table>
												</td>
												<td class="txtNegro12bEspecialPaso tooltip" style="background: #{__class_iteracion_paso}; color: {__color_text}; padding-left: 25px" width="20%" align="center">{__tiempo_respuesta_paso}</td>
											</tr>
										</table>
									</td>
								</tr>
								<!-- END BLOQUE_PASO -->
							</table>
						</td>
					</tr>
					<!-- END BLOQUE_FUNCIONALIDAD -->
					<tr>
						<td colspan="5" height="15px"></td>
					</tr>
				</table>
		</div>
		<!-- END BLOQUE_CATEGORIA -->
	</td>
</tr>
<div id="flujo" class="modal_flujo" style="display: none;">
	<span onclick="document.getElementById('flujo').style.display='none'" class="close" title="Close Modal">&times;</span>
</div>
<script>
	//ACORDEON
	var acc = document.getElementsByClassName("acordeon");
	var i;

	for (i = 0; i < acc.length; i++) {
		acc[i].addEventListener("click", function() {
			this.classList.toggle("active");
			var panel = this.nextElementSibling;
			if (panel.style.display === "contents") {
				panel.style.display = "none";
			} else {
				panel.style.display = "contents";
			}
		});
	}

	//CARGA IMAGENES TOOLTIP
	$(document).ready(function(){
		jQuery('div[data=cdn]').each(function(i, ele) {
			var img = $('<img />').attr({'src':'utils/screenshot_especial.php?token='+ele.id, 'id':'img_'+ele.id,  'height': "510px", 'max-width': "120%", 'onError':'this.onerror=null;this.src="/img/screenshot_error.png"'}).on('load', function() {
				$(ele).append(img);
			});
		});
	});

	//ACTUALIZA REPORTE ESPECIAL
	function actualizaPopup(objetivo_especial, horario_id, usuario_id){
		var fecha_inicio = document.getElementById("fecha_inicio_periodico").value;
		var fecha_termino = document.getElementById("fecha_termino_periodico").value;
		if (fecha_inicio == 0) {
			alert("Debe seleccionar un periodo.");
			return false;
		}
		if (fecha_termino == 0) {
			alert("Debe seleccionar un periodo.");
			return false;
		}
		document.getElementById('fecha_periodo').remove()

		var fecha_inicio_str = new Date(fecha_inicio)
		var fecha_termino_str = new Date(fecha_termino)
		var fecha_termino_final = new Date(fecha_termino_str.getTime()-86400000)

		var today = new Date();
		fecha_hoy = String(today.getDate()).padStart(2, '0')+'/'+String((today.getMonth())+1).padStart(2, '0')+'/'+today.getFullYear()+' '+String(today.getHours()).padStart(2, '0')+':'+String(today.getMinutes()).padStart(2, '0')+':'+String(today.getSeconds()).padStart(2, '0')
		
		anio_inicio = fecha_inicio_str.getFullYear()
		mes_inicio = String(fecha_inicio_str.getMonth()+1).padStart(2, '0')
		dia_inicio = String(fecha_inicio_str.getDate()).padStart(2, '0')

		anio_termino = fecha_termino_final.getFullYear()
		mes_termino = String(fecha_termino_final.getMonth()+1).padStart(2, '0')
		dia_termino = String(fecha_termino_final.getDate()).padStart(2, '0')

		fecha_inicio_p = dia_inicio+'/'+mes_inicio+'/'+anio_inicio+' '+'00:00:00'
		fecha_termino_p = dia_termino+'/'+mes_termino+'/'+anio_termino+' '+'23:59:59'


		if ( fecha_termino_str > today ) {
			fechas_periodo = '<td id="fecha_periodo" style="padding: 8px; background-color:#626262;"  colspan="2" width="80%" class="txtBlanco13b">'+fecha_inicio_p+' al '+fecha_hoy+'</td>'
		}else{
			fechas_periodo = '<td id="fecha_periodo" style="padding: 8px; background-color:#626262;"  colspan="2" width="80%" class="txtBlanco13b">'+fecha_inicio_p+' al '+fecha_termino_p+'</td>'
		}

		fecha_periodo = document.getElementById('div_fecha_periodo')

		$(fecha_periodo).append(fechas_periodo)
		
		document.getElementById('button2').style.display = ''
		document.getElementById('formbutton').style.display = 'none'

		setTimeout(function(){
			$.ajax({
				async: false,
				type: 'POST',
				url: '../call_ajax.php',
				data: {'nameFunction':'vruAjax', 'objetivo_especial': objetivo_especial, 'horario_id': horario_id, 'usuario_id': usuario_id, 'fecha_inicio': fecha_inicio, 'fecha_termino': fecha_termino},
				success: function(data) {
					document.getElementById('button2').style.display = 'none'
					document.getElementById('formbutton').style.display = ''
					vrunificada = JSON.parse(data)
					json = JSON.parse(vrunificada)
					div_vru_c = ''
					tr_func = ''
					li_nn_desc = document.getElementById("il_cat")
					mm_cat_desc = document.getElementById("ley_cat")
					tr_vru = document.getElementById("vru")
					td_vru = document.getElementById("td_vru")
					div_vru_cat = document.getElementById("div_vru_cat")
					mm_cat_desc.remove()
					td_vru.remove()
					div_vru_cat.remove()
					ul = '<div id="ley_cat">'
					td_vru = '<td colspan="2" id="td_vru">'+
											'<table width="100%">'+
												'<tr>'+
													'<td height="25px" style="border-left: solid 1px #fff; border-top: solid 1px #fff; border-bottom: solid 1px #fff;" align="left" class="txtBlanco12b celdaTituloNaranjoEspecial2"></td>'+
													'<td align="center"style="border: solid 1px #fff;" class="txtClaro10bEspecial celdaTituloNaranjo">UPTIME</td>'+
													'<td align="center"style="border: solid 1px #fff;" class="txtClaro10bEspecial celdaTituloNaranjo">DOWNTIME</td>'+
													'<td align="center"style="border: solid 1px #fff;" class="txtClaro10bEspecial celdaTituloNaranjo">TIEMPO DE RESPUESTA [segs]</td>'+
												'</tr>'
					$.each(json, function (index, value) {
						$.each(value[0].categoria_global, function (i, categoria_global) {
							nom_cat_g = categoria_global.nombre_categoria
							mm_cat = categoria_global.disponibilidad[0].min_max
							ul += '<ul><li><a style="font-weight: bold;">'+nom_cat_g+': </a>'+mm_cat+'</li></ul>'
							td_vru += '<tr>'+
								'<td class="txtNegro14bEspecialBold categoria" width="40%" >'+nom_cat_g+'</td>'+
								'<td class="txtNegro14bEspecialBold categoria tooltip" width="20%" align="center">'+categoria_global.disponibilidad[0].uptimecat+' %'+
									'<table class="uptime" style="width: 90%;">'+
										'<tr>'+
											'<td style="text-align: center;">'+categoria_global.disponibilidad[0].tiempo_uptime_tooltip+'</td>'+
										'</tr>'+
									'</table>'+
								'</td>'+
								'<td class="txtNegro14bEspecialBold categoria tooltip" width="20%" align="center">'+categoria_global.disponibilidad[0].downtimecat+' %'+
									'<table class="downtime" style="width: 90%;">'+
										'<tr>'+
											'<td style="text-align: center;">'+categoria_global.disponibilidad[0].tiempo_downtime_tooltip+'</td>'+
										'</tr>'+
									'</table>'+
								'</td>'+
								'<td class="txtNegro14bEspecialBold categoria" width="20%" align="center">'+categoria_global.disponibilidad[0].tiemporespuesta+'</td>'+
							'</tr>'
						})
						td_vru +=	'<tr>'+
												'<td height="30px"></td>'+
											'</tr>'
						$.each(value[0].categoria, function (i, categoria) {
							nom_cat = categoria.nombre_categoria
							div_vru_c +=	'<div style="'+categoria.page_break_categoria+'" id="div_vru_cat">'
							table_c = '<table width="100%" id="table_cat">'
							div_vru_c += table_c
							div_vru_c += '<tr >'+
														'<div>'+
															'<td height="25px" align="left" class="txtNegroClaro14bEspecialBold celdaTituloNaranjoEspecial">'+nom_cat+'</td>'+
															'<td height="25px" align="center" class="txtBlanco12b celdaTituloNaranjoEspecial tooltip" >'+categoria.uptimecat+' %'+
																'<table class="uptime" style="width: 150%;">'+
																	'<tr>'+
																		'<td style="text-align: center;">'+categoria.tiempo_uptime_tooltip+'</td>'+
																	'</tr>'+
																'</table>'+
															'</td>'+
															'<td height="25px" style="border: solid 1px #cccccc;" align="center" class="txtNegro10bEspecial">UPTIME</td>'+
															'<td height="25px" style="border: solid 1px #cccccc;" align="center" class="txtNegro10bEspecial">DOWNTIME</td>'+
															'<td height="25px" style="border: solid 1px #cccccc;" align="center" class="txtNegro10bEspecial">TIEMPO DE RESPUESTA [segs]</td>'+
														'</div>'+
													'</tr>'
							$.each(categoria.funcionalidad, function (j, funcionalidad) {
								nom_func = funcionalidad.nombre_funcionalidad
								t_cat = document.getElementById("table_cat")
								div_vru_c += '<tr class="acordeon" style="border-style: outset; border-color: #fff; cursor: pointer;">'+
															'<td class="txtNegro14bEspecialBold '+funcionalidad.class_iteracion_func+' tooltip" width="40%" colspan="2">'+nom_func+'</td>'+
															'<td class="txtNegro14bEspecialBoldFuncionalidad '+funcionalidad.class_iteracion_func+' tooltip" width="20%" align="center">'+funcionalidad.uptime_real+' %'+
																'<table class="uptime" style="width: 90%;">'+
																	'<tr>'+
																		'<td style="text-align: center;">'+funcionalidad.tiempo_uptime_tooltip+'</td>'+
																	'</tr>'+
																'</table>'+
															'</td>'+
															'<td class="txtNegro14bEspecialBoldFuncionalidad '+funcionalidad.class_iteracion_func+' tooltip" width="20%" align="center">'+funcionalidad.downtime_real+' %'+
																'<table class="downtime" style="width: 90%;">'+
																	'<tr>'+
																		'<td style="text-align: center;">'+funcionalidad.tiempo_downtime_tooltip+'</td>'+
																	'</tr>'+
																'</table>'+
															'</td>'+
															'<td class="txtNegro14bEspecialBoldFuncionalidad '+funcionalidad.class_iteracion_func+'" width="20%" align="center">'+funcionalidad.tiempo_respuesta+'</td>'+
														'</tr>'+
														'<tr class="panel">'+
															'<td colspan="5" >'+
																'<table width="100%">'
								$.each(funcionalidad.pasos, function (j, pasos) {
									div_vru_c += '<tr>'+
																'<td>'+
																	'<table width="100%">'+
																		'<tr>'+
																			'<td class="txtNegro12bEspecialPaso tooltip" width="40%" colspan="2"  style=" max-width: 250px; padding: 1px 6px 1px 25px; background: #'+pasos.class_iteracion_paso+'; color:  #'+pasos.color_text+';" onmouseover="document.getElementById(\''+pasos.hash+'\').style.display = \'inline\'" onmouseout="document.getElementById(\''+pasos.hash+'\').style.display = \'none\'">'+
																				'<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; cursor: default;">'+pasos.nombre_paso+' '+pasos.nombre_objetivo+
																				'</div>'+
																				'<table class="'+pasos.class_tooltip_paso+'" id="'+pasos.hash+'">'+
																					'<tr>'+
																						'<td valign="center" style="text-align: center; ">'+
																							'<table width="100%">'+
																								'<tr>'+
																									'<td colspan="2">'+
																										'<div id="'+pasos.hash+'" data=\'cdn\' style="width: 100%; display: '+pasos.muestra_screenshot+';"></div>'+
																									'</td>'+
																								'</tr>'+
																								'<tr>'+
																									'<td style="text-align: center; font-weight: bold;" valign="center">'+
																										'<a style="color: #3a4c4c">ISPs: '+pasos.cant_nodo_paso+'</a>'+
																										'<a style="color: white;"> '+pasos.nombre_paso+'  '+pasos.nombre_objetivo_tooltip+'</a>'+
																									'</td>'+
																									'<td style="text-align: center; padding-top: 6px; font-weight: bold; width: 20%; display: '+pasos.muestra_descarga+';" valign="center">'+
																										'<a class="descarga"  href="utils/screenshot_especial.php?token='+pasos.hash+'" download="'+nom_cat+'_'+nom_func+'_'+pasos.nombre_objetivo_tooltip+'_'+pasos.nombre_paso+'">'+
																											'<img src="/img/download.png">'+
																										'</a>'+
																									'</td>'+
																								'</tr>'+
																							'</table>'+
																						'</td>'+
																					'</tr>'+
																					'<tr>'+
																						'<td>'+
																							'<table width="100%" align="center" style="color: white;">'+
																								'<tr>'+
																									'<td style="font-size: 12px;">Uptime</td>'+
																									'<td style="font-size: 12px; text-align: right;">'+pasos.uptime_real_paso+' %</td>'+
																								'</tr>'+
																								'<tr style="background-color: #868686;">'+
																									'<td style="font-size: 12px;">Downtime</td>'+
																									'<td style="font-size: 12px; text-align: right;">'+pasos.downtime_real_paso+' %</td>'+
																								'</tr>'+
																								'<tr>'+
																									'<td style="font-size: 12px;">T. Respuesta</td>'+
																									'<td style="font-size: 12px; text-align: right;">'+pasos.tiempo_respuesta_paso+' segs</td>'+
																								'</tr>'+
																							'</table>'+
																						'</td>'+
																					'</tr>'+
																					'<tr>'+
																						'<td>'+
																							'<table width="100%">'+
																								'<tr>'+
																									'<td align="center">'+
																										'<input type="button" style="display: '+pasos.valida_secuencia+'" name="flujo" value="Secuencia" onclick="getFlujo(\''+pasos.pasos_flujo+'\', \''+pasos.flujo+'\', document.getElementById(\''+pasos.hash+'\'))" class="myButton"/>'+
																									'</td>'+
																								'</tr>'+
																							'</table>'+
																						'</td>'+
																					'</tr>'+
																				'</table>'+
																			'</td>'+
																			'<td class="txtNegro12bEspecialPaso tooltip" style="background: #'+pasos.class_iteracion_paso+'; color: #'+pasos.color_text+'; padding-left: 25px" width="20%" align="center">'+pasos.uptime_real_paso+' %'+
																				'<table class="uptime" style="width: 90%;">'+
																					'<tr>'+
																						'<td style="text-align: center;">'+pasos.tiempo_porcentaje_uptime+'</td>'+
																					'</tr>'+
																				'</table>'+
																			'</td>'+
																			'<td class="txtNegro12bEspecialPaso tooltip" style="background: #'+pasos.class_iteracion_paso+'; color: #'+pasos.color_text+'; padding-left: 25px" width="20%" align="center">'+pasos.downtime_real_paso+' %'+
																				'<table class="downtime" style="width: 90%;">'+
																					'<tr>'+
																						'<td style="text-align: center;">'+pasos.tiempo_porcentaje_downtime+'</td>'+
																					'</tr>'+
																				'</table>'+
																			'</td>'+
																			'<td class="txtNegro12bEspecialPaso tooltip" style="background: #'+pasos.class_iteracion_paso+'; color: #'+pasos.color_text+'; padding-left: 25px" width="20%" align="center">'+pasos.tiempo_respuesta_paso+'</td>'+
																		'</tr>'+
																	'</table>'+
																'</td>'+
															'</tr>'
								})
								div_vru_c +=    '</table>'+
																'</td>'+
															'</tr>'
							})
							div_vru_c +='<tr>'+
														'<td colspan="5" height="15px"></td>'+
													'</tr>'+
													'</table>'+
												'</div>'
						})
						td_vru += div_vru_c
					})
					td_vru += 	'</table></td>'
					
					$(li_nn_desc).append(ul)
					$(tr_vru).append(td_vru)
					jQuery('div[data=cdn]').each(function(i, ele) {
						var img = $('<img />').attr({'src':'utils/screenshot_especial.php?token='+ele.id, 'id':'img_'+ele.id,  'height': "510px", 'max-width': "120%", 'onError':'this.onerror=null;this.src="/img/screenshot_error.png"'}).on('load', function() {
							$(ele).append(img);
						});
					});

					var acc = document.getElementsByClassName("acordeon");
					var i;

					for (i = 0; i < acc.length; i++) {
						acc[i].addEventListener("click", function() {
							this.classList.toggle("active");
							var panel = this.nextElementSibling;
							if (panel.style.display === "contents") {
								panel.style.display = "none";
							} else {
								panel.style.display = "contents";
							}
						});
					}
				},
				error: function(error) {
					alert('Ha ocurrido un problema en la carga de datos. Se recargará la página.')
					location.reload();
				}
			})
 		}, 1)
	}

	//SE OBTIENE MODAL DE LOS FLUJOS
	function getFlujo(pasos, flujo, primer_tooltip){
		primer_tooltip.style.display = 'none'
		var divDescarga=''
		flujo = flujo.replace(" ", "")
		$('img[id="flujo"]').remove();
		$('div[id="descarga_pasos"]').remove();
		var img = $('<img />').attr({'src':'utils/screenshot_especial.php?token='+flujo, 'id': 'flujo', 'height': "81%", 'onError':'this.onerror=null;this.src="/img/screenshot_error.png"'})
		$('div[id="flujo"]').append(img)
		var res = pasos.split("*")
		divDescarga = '<div id="descarga_pasos"  style="height: 19%; overflow-y: auto;"><div><table align="center" width="100%" id="descarga_pasos" >'
		divDescarga += '<tr><td width="33%" class="txtNegro12bEspecialPaso tooltip" align="center" style="color: white; background: #404040; height: 30px; font-weight: bold;">Orden</td><td width="33%" class="txtNegro12bEspecialPaso tooltip" align="center" style="color: white; background: #404040; height: 30px; font-weight: bold;">Nombre Paso</td><td width="33%" class="txtNegro12bEspecialPaso tooltip" align="center" style="color: white; background: #404040; height: 30px; font-weight: bold;">Descargar Imagen</td></tr>'
		
		var contador = 0
		$(res).each(function(index, steps){
			if (contador % 2 == 0){
				background = '#b9b9b9'
				color = 'white'
			}else{
				background = '#868686'
				color = 'white'
			}
			var orden = steps.split("|")[0]
			var hash = steps.split("|")[1]
			var nombre = steps.split("|")[2]
			if(hash){
				divDescarga += '<tr><td width="33%" class="txtNegro12bEspecialPaso tooltip" align="center" style="color: '+color+'; background: '+background+'; height: 30px; font-weight: bold;">'+orden+'</td><td width="33%" class="txtNegro12bEspecialPaso tooltip" align="center" style="color: '+color+'; background: '+background+'; height: 30px; font-weight: bold;">'+nombre+'</td><td  class="txtNegro12bEspecialPaso tooltip" width="33%" style="text-align: center; background: '+background+'; height: 30px; color: '+color+'"><a class="descarga"  href="utils/screenshot_especial.php?token='+hash+'"download="'+nombre+'"><img src="/img/download.png"></a></td></tr>'
			}else{
				divDescarga += ''
			}
			contador++
		})
		divDescarga+='</table></div></div>'
		$('div[id="flujo"]').append(divDescarga)
		
		document.getElementById('flujo').style.display = "inline";
	}

	//CALENDARIO
	jQuery(function($) {
		var $calendarioEspecial = $("#calendario_especial");
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
		var $inputFechaInicio = $('<input type="hidden" name="fecha_inicio_periodico" id="fecha_inicio_periodico" value="{__fecha_inicio_periodo}">');
		var $inputFechaTermino = $('<input type="hidden" name="fecha_termino_periodico" id="fecha_termino_periodico" value="{__fecha_termino_periodo}">');

		$calendarioEspecial.append($inputFechaInicio, $inputFechaTermino);

		calendariou.seleccion.el().on("calendariou:seleccion:cambio", function() {
			var fechaInicio = calendariou.seleccion.get("fechaInicio");
			var fechaTermino = calendariou.seleccion.get("fechaTermino");
			$inputFechaInicio.prop("value", fechaInicio === null ? null : fechaInicio.format("yyyy-mm-ddThh:mm:ss"));
			$inputFechaTermino.prop("value", fechaTermino === null ? null : fechaTermino.format("yyyy-mm-ddThh:mm:ss"));
		});
	});
</script>
