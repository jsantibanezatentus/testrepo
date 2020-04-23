<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.8.17.custom.min.js"></script>
<link rel="stylesheet" href="tools/jquery/css/ui-lightness/jquery-ui-1.8.17.custom.css"></link>

<style>
	#demo-frame > div.demo { padding: 10px !important; };
</style>

<script>

//SE ASIGNA UN VALOR COMO MÁXIMO DE LA ESCALABILIDAD
fin['{__objetivo_id}_{__notificacion_id}'] = 103;

//ACTUALIZA EL VALOR DEL MÁXIMO EN CASO QUE EL MINIMO FUESE MAYOR QUE EL MAXIMO POR DEFECTO
$("#minimo_{__objetivo_id}_{__notificacion_id}").each(function(){			
	if($(this).val()>fin['{__objetivo_id}_{__notificacion_id}'])
		fin['{__objetivo_id}_{__notificacion_id}']=(2*parseInt($(this).val()));				
	});

/*
$("#notificacion_destinatario_id_{__objetivo_id}_{__notificacion_id}").each(function(){	
	destinatario_anterior['{__objetivo_id}_{__notificacion_id}']=$(this).val();
});
*/
$("#maximo_{__objetivo_id}_{__notificacion_id}").each(function(){			
	if($(this).val()>fin['{__objetivo_id}_{__notificacion_id}'])
		fin['{__objetivo_id}_{__notificacion_id}']=parseInt($(this).val())+3;				
	});

	
	$(function() {
		$( "#slider_range_{__objetivo_id}_{__notificacion_id}").each(function(){					
			var objetivo_id=$(this).data("objetivo_id");
			var notificacion_id=$(this).data("notificacion_id");	
			var mini = $("input.minimo[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").val();			
			var maxi = $("input.maximo[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").val();
			

			var contenedor ="";
			 if(notificacion_id!=0){
				 contenedor="div#detalle_alarma_"+notificacion_id;
			 }
			 else{
				 contenedor="div#detalle_alarma_objetivo_"+objetivo_id;
				 
			 }
			 
			if(isNaN(maxi)||maxi==''||maxi=='Infinito'){			
				maxi=fin['{__objetivo_id}_{__notificacion_id}'];				
				$("input.maximo[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").val("Infinito");
			}
			
			//BUSCA SI DEBE O NO BLOQUEAR EL MÁXIMO DE LA ESCALABILIDAD PARA QUE NO LA PUEDA MOVER
			bloqueaGrafico($("#sin_limite_{__objetivo_id}_{__notificacion_id}"));			
			cambioSeleccionEscalabilidad($(contenedor));		
			bloqueaUptime($(this).data("objetivo_id"),$(this).data("notificacion_id"));	
		});



		//CADA VEZ QUE SE MODIFIQUE EL MINIMO VALIDARÁ SU VALOR Y REDIBUJARÁ LA ESCALABILIDAD
		$( "#minimo_{__objetivo_id}_{__notificacion_id}" ).live("blur",function(event) {
			var minimo= $(this ).val();
			var maximo= $(this).parent().find("#maximo_{__objetivo_id}_{__notificacion_id}")[0].value;
			var objetivo_id=$(this).data("objetivo_id");
			var notificacion_id=$(this).data("notificacion_id");
			if(isNaN($(this ).val())||$(this ).val()=='' ){
				alert("Debe ingresar un valor númerico al mínimo");
				$(this).focus();
				return false;
			}
			if(maximo=='Infinito')
				maximo=fin['{__objetivo_id}_{__notificacion_id}']-reajuste;
			if(parseInt(minimo)<= parseInt(maximo)){
				reacomodaSlider($(".slider_range[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]"),minimo, maximo,false,fin['{__objetivo_id}_{__notificacion_id}']);
				if(parseInt(maximo)-parseInt(minimo)+1>1)
					cantidad="Alertas";
				else
					cantidad="Alerta";
				$(this).parent().parent().find("div#nalarmas").html("("+(parseInt(maximo)-parseInt(minimo)+1)+" "+cantidad+")");
			}
			else{
				if(fin['{__objetivo_id}_{__notificacion_id}']<(minimo*2))
					fin['{__objetivo_id}_{__notificacion_id}']=(minimo*2);
				reacomodaSlider($(".slider_range[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]"),minimo, (minimo*2),false,fin['{__objetivo_id}_{__notificacion_id}']);
				if($(this).parent().find("#maximo_{__objetivo_id}_{__notificacion_id}")[0].value!='Infinito'){					
					$(".maximo[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").val((minimo*2));															
				}
			}
			modificaNotificacion(objetivo_id,notificacion_id, $(this).attr("id"));	
		});

		
		//CADA VEZ 	QUE SE POSE SOBRE EL MÁXIMO VALIDARÁ SU VALOR Y REDIBIJARÁ EL GRÁFICO
		$( "#maximo_{__objetivo_id}_{__notificacion_id}" ).live("blur",function(event) {
			editaMaximo($(this));			
		});

		function editaMaximo(elem){
			var minimo= $(elem).parent().find("#minimo_{__objetivo_id}_{__notificacion_id}")[0].value;
			var maximo=parseInt($(elem).val())+parseInt(reajuste);
			var cantidad="Alerta";
			var objetivo_id=$(elem).data("objetivo_id");
			var notificacion_id=$(elem).data("notificacion_id");
			
			if($(elem).val()=='Infinito'){
				maximo=fin['{__objetivo_id}_{__notificacion_id}']-reajuste;
			}
			else{
				if($(elem).val()==''||(isNaN($(elem).val()) && $(elem).val()!='Infinito')){
					alert("Debe ingresar un valor númerico al máximo");
					$(elem).focus();
					return false;
				}
			}
			if(parseInt(maximo)>parseInt(fin['{__objetivo_id}_{__notificacion_id}'])){   
				fin['{__objetivo_id}_{__notificacion_id}']=maximo;
			}
			if(parseInt(minimo)<= parseInt(maximo)){
				reacomodaSlider($(".slider_range[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]"),minimo,maximo,false,parseInt(fin['{__objetivo_id}_{__notificacion_id}']));
				if(parseInt(maximo)-parseInt(minimo)+1>1)
					cantidad="Alertas";
				else
					cantidad="Alerta";
				$(elem).parent().parent().find("div#nalarmas").html("<b>"+(parseInt(maximo)-reajuste-parseInt(minimo)+1)+" "+cantidad+"</b>");
			}
			else{
				alert("El mínimo no puede ser mayor que el máximo");
				$(elem).focus();
				return false;
			}
			var result = modificaNotificacion($(elem).data("objetivo_id"),$(elem).data("notificacion_id"), $(elem).attr("id"));


		}

		$("#sin_limite_{__objetivo_id}_{__notificacion_id}").live("change",function(event) {
			var objetivo_id=$(this).data("objetivo_id");
			var notificacion_id=$(this).data("notificacion_id");	
			if(!$(this).is(":checked")){
				$("#maximo_"+objetivo_id+"_"+notificacion_id).val($("#minimo_"+objetivo_id+"_"+notificacion_id).val());
			}
					
			bloqueaGrafico($(this));	
			modificaNotificacion($(this).data("objetivo_id"),$(this).data("notificacion_id"), $(this).attr("id"));
		});			


		//CADA CHECKBOX CON OPCIONES PARA ASIGNAR VALORES AL GRÁFICO PASA POR ACA AL SER MODIFICADO		
		$(".chkbx").live("change",function(event) {
			var padre =$(this).parent().parent().parent().parent().parent();
			var objetivo_id=$(this).data("objetivo_id");
			var notificacion_id=$(this).data("notificacion_id");
			bloqueaUptime(objetivo_id, notificacion_id);
			 if(notificacion_id!=0)
				 contenedor=$("div#detalle_alarma_"+notificacion_id);
			 else
				 contenedor=$("div#detalle_alarma_objetivo_"+objetivo_id);			
			cambioSeleccionEscalabilidad($(contenedor));
			var result = modificaNotificacion($(this).data("objetivo_id"),$(this).data("notificacion_id"), $(this).attr("id"));
			if(result==true){				
				enviaCorreo($(this).data("tipo_notificacion"),$(this).is(":checked"));
			}

		});

		//IDENTIFICA CADA CHECKBOX CLASE UMBRAL PARA DETERMINAR SI DEBE O NO ESTAR BLOQUEADO
		$( ".umbral" ).each(function(elem) {
			bloqueaUmbral($(this));
			 
		});



		
		//FUNCION QUE BLOQUEA O DESBLOQUEA LA EDICIÓN DEL MÁXIMO EN LA ESCALABILIDAD (NO BLOQUEA, CAMBIA A OTRO TIPO DE GRÁFICO)
		function bloqueaGrafico(chk){
			var objetivo_id=$(chk).data("objetivo_id");
			var notificacion_id=$(chk).data("notificacion_id");			
			
			$(".slider_range[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").slider("destroy");
			var min=$(chk).parent().parent().find("input.minimo").val();
			var max=parseInt($(chk).parent().parent().find("input.maximo").val())+parseInt(reajuste);
		
			//SI ESTA EN ESTADO INFINITO BLOQUEA
			if($(chk).is(":checked")){				
				$(chk).parent().find("input.maximo")[0].value="Infinito";
				$(chk).parent().parent().find("input.maximo").prop("disabled",true);
				//REDIBUJA EL GRAFICO				
				$(".slider_range[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").slider({
					range:"max",	
					value: parseInt(min),
					max: parseInt(fin['{__objetivo_id}_{__notificacion_id}']),
					slide: function( event, ui ) {					
						var minimo=ui.value;						
						$("input.minimo[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").val(minimo);
						$("div#nalarmas[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").html("<b>Infinitas Alertas</b>");																		
					}
				});	
				$(chk).parent().parent().find("div#nalarmas").html("<b>Infinitas Alertas</b>");	
			}
			else{//DESBLOQUEA PARA PODER MODIFICAR EL MAXIMO
				var cantidad='Alerta';
				if($(chk).parent().parent().find("input.maximo")[0].value=='Infinito' || $(chk).parent().parent().find("input.maximo")[0].value==''){
					$(chk).parent().parent().find("input.maximo")[0].value='Infinito';
					max=parseInt(fin['{__objetivo_id}_{__notificacion_id}']);
				}
		
				$(chk).parent().parent().find("input.maximo").prop("disabled",false);
				
				//REDIBUJA EL GRAFICO
				$(".slider_range[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").slider({
					range:true,	
					values: [ parseInt(min), parseInt(max) ],
					max: parseInt(fin['{__objetivo_id}_{__notificacion_id}']),
					slide: function( event, ui ) {											
						var minimo=ui.values[0];
						var maximo = ui.values[1];				
						if(maximo-minimo<3){
							reacomodaSlider($(".slider_range[data-objetivo_id="+objetivo_id+"][daa-notificacion_id="+notificacion_id+"]"),min,max, false, fin['{__objetivo_id}_{__notificacion_id}']);
							return false;
						}								
						$("input.minimo[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").val(minimo);				
						$("input.maximo[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").val(maximo-reajuste);
																	
						if(parseInt(maximo)-parseInt(minimo)+1>1)
							cantidad="Alertas";
						else
							cantidad="Alerta";
						$("div#nalarmas[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").html("<b>"+(parseInt(maximo)-reajuste-parseInt(minimo)+1)+" "+cantidad+"</b>");
						modificaNotificacion(objetivo_id, notificacion_id, $("input.minimo[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").attr("id"));								
					}
				
				});
				
				$(chk).parent().parent().find("input.maximo")[0].value=parseInt(max)-parseInt(reajuste);	
				
				if(max-reajuste-min+1>1)
					cantidad="Alertas";
				else
					cantidad="Alerta";		
				$("div#nalarmas[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").html("<b>"+(max-reajuste-min+1)+" "+cantidad+"</b>");
			}				
			 if(notificacion_id!=0)
				 contenedor=$("div#detalle_alarma_"+notificacion_id);
			 else
				 contenedor=$("div#detalle_alarma_objetivo_"+objetivo_id);
			 
			cambioSeleccionEscalabilidad($(contenedor));
		}
			



	

		
	});
	
</script>
	<table>
	<!-- BEGIN BLOQUE_ALARMA_NUEVA -->
	<tr>
		<td colspan="100%" class="celdanegra10" style="background-color:#f6f6f6"><b>Nueva Alerta</b> </td>
	</tr>
	<!-- END BLOQUE_ALARMA_NUEVA -->
  	<tr>
    	<td style="padding:12 0 0 0; background-color:#f6f6f6">	
			<table  width="730px">
				<tr>
					
					<td style="background-color:#F6F6F6;" width="190px">
						<table>
							<tr>
								<td width="170px">
									<select id="notificacion_destinatario_id_{__objetivo_id}_{__notificacion_id}" name="notificacion_destinatario_id_{__objetivo_id}_{__notificacion_id}" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}" style="float: left; position: relative;" {__form_disabled}  data-tipo_notificacion="contacto" class="destinatario">
										<option value="-1" selected="selected">Seleccione Contacto</option>									
										<!-- BEGIN DESTINATARIOS_NOTIFICACION -->
										<option value="{__destinatario_id}" {__destinatario_sel}>{__destinatario_nombre}</option>
										<!-- END DESTINATARIOS_NOTIFICACION -->
									</select>
									<div style="height:22px; padding-top:7px" id="ayuda_destinatario_{__objetivo_id}_{__notificacion_id}">
                                                                            <i class="spriteImg spriteImg-ayuda" width="13px" id="imagen_ayuda_destinatario_{__objetivo_id}_{__notificacion_id}" style="display:inline-block"></i>
									</div>
									<div dojoType="dijit.Tooltip" connectId="imagen_ayuda_destinatario_{__objetivo_id}_{__notificacion_id}" position="below">
										<div class="textgris9" align="left">
											• Indica el contacto al que será enviada la alerta.<br>
											• Para agregar un nuevo contacto vaya a la parte izquierda de la pantalla y presione <b>"Contactos"</b> 
										</div>
									</div>
								</td>
								<td width="12px" style="background-color:#F6F6F6;">
									<div class="contenedor_imagen" style="display:none">
										<img src="img/cargando.gif" width="11px" class="imagen"/>
									</div>
								</td>								
							</tr>
						</table>
					</td>
					<td style="background-color:#F6F6F6;" width="160px">
						<table>
							<tr>
								<td width="148px">
									<select name="notificacion_horario_id_{__objetivo_id}_{__notificacion_id}" id="notificacion_horario_id_{__objetivo_id}_{__notificacion_id}" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}" style="float: left; position: relative;width:85%"  data-tipo_notificacion="horario" {__form_disabled} class="horario" >
										<option value="-1"  selected="selected">Seleccione Horario</option>																		
										<!-- BEGIN HORARIOS_NOTIFICACION -->
										<option value="{__horario_id}" {__horario_sel}>{__horario_nombre}</option>
										<!-- END HORARIOS_NOTIFICACION -->
									</select>
									<div style="height:22px; padding-top:7px" id="ayuda_horario_{__objetivo_id}_{__notificacion_id}">
									  <i class="spriteImg spriteImg-ayuda" width="13px" id="imagen_ayuda_horario_{__objetivo_id}_{__notificacion_id}" style="display:inline-block"></i>
									</div>
									<div dojoType="dijit.Tooltip" connectId="imagen_ayuda_horario_{__objetivo_id}_{__notificacion_id}" position="below">
										<div class="textgris9" align="left">
											• Señala el horario en que se envirán alerta.<br>
											• Para agregar un nuevo horario vaya a la parte izquierda de la pantalla y presione <b>"Horarios Alertas"</b> 
										</div>
									</div>
								</td>
								<td width="12px" style="background-color:#F6F6F6;">
									<div class="contenedor_imagen" style="display:none">
										<img src="img/cargando.gif" width="11px" class="imagen"/>
									</div>
								</td>	
							</tr>
						</table>
					</td>
					<td class="celdanegra10" style="background-color:#F6F6F6;" width="330px">
						<table width="295px" align="left">
							<tr>
								<td  align="center"   class="celdanegra10" style="background-color:#F6F6F6;">
									<div class="demo" style="width:295px">													
										<div id="slider_range_{__objetivo_id}_{__notificacion_id}" class="slider_range" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}"></div>
									</div>
									
								</td>
							</tr>										
						</table>
						<div style="height:22px; padding-top:7px" id="tooltip_escalabilidad_{__objetivo_id}_{__notificacion_id}">
                                                    <i class="spriteImg spriteImg-ayuda" width="13px" id="imagen_tooltip_escalabilidad_{__objetivo_id}_{__notificacion_id}" style="display:inline-block"></i>
						</div>
						<div dojoType="dijit.Tooltip" connectId="imagen_tooltip_escalabilidad_{__objetivo_id}_{__notificacion_id}" position="below">
							<div class="textgris9" align="left">
								• Indica a partir de cuando y el número de veces que serán enviadas las alertas<br>
								• Para su uso es necesario tener seleccionada notificaciones por downtime Global o Parcial   
							</div>
						</div>						
					</td>			
					<!-- BEGIN BLOQUE_PUEDE_EDITAR -->
					<td class="celdanegra10"  style="background-color:#F6F6F6;cursor:pointer" width="25px"  ><i class="sprite sprite-cerrar" width="18" class="editar" data-notificacion_id="{__notificacion_id}"  title="Terminar Edición"></i></td>
					<td class="celdanegra10" style="background-color:#F6F6F6;cursor:pointer" width="25px" onclick="eliminaNotificacion({__objetivo_id},{__notificacion_id})">
						<i class="imagen_borrar spriteButton spriteButton-borrar"  data-objetivo_id="{__objetivo_id}"  data-notificacion_id="{__notificacion_id}" title="Eliminar Notificación" />
					</td>
					<!-- END BLOQUE_PUEDE_EDITAR -->
					<!-- BEGIN BLOQUE_NO_PUEDE_EDITAR -->
					<td class="celdanegra10" style="background-color:#F6F6F6;"></td>
					<td class="celdanegra10" style="background-color:#F6F6F6;"></td>
					<!-- END BLOQUE_NO_PUEDE_EDITAR -->
										
				</tr>
				<tr>
					<td colspan="4" style="padding-left:25px;padding-right:25px;background-color:#F6F6F6; ">
						<table width="100%" id="escalabilidad" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}">
							<tr>
								<td class="celdanegra10" width="250px" style="padding:0 0 0 25px">
									<div style="display:inline-block" class="desabilitado"><b>Escalabilidad</b></div>
									<div class="textgris9 desabilitado" style="display:inline-block"> 
										Define el rango de eventos <i>downtime</i> consecutivos en los que se deben disparar alertas.
										<div class="ayuda" data-tipo_ayuda="escalabilidad" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}" style="display:inline;cursor:pointer">[...]</div>
										<div style="display:none" id="ayuda_escalabilidad_{__objetivo_id}_{__notificacion_id}" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}" >
										Por ejemplo: 
											* Si desea recibir solo la primera alerta, la escalabilidad es desde 1 hasta 1.
											* Si desea recibir desde la 2da alerta hasta la 5ta alerta, la escalabilidad es desde 2 hasta 5
											
											Sirve para alertar de manera diferenciada a diferentes unidades de su empresa, como el turno
											de operaciones, mesa de ayuda y jefes de áreas.
										</div>
									</div>
								</td>
								<td class="celdanegra10" style="font-size:10">
									<div style="display:inline" class="desabilitado">Desde</div> 
									<input type="text" id="minimo_{__objetivo_id}_{__notificacion_id}" class="minimo input_mini" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}" size="2" value="{__notificacion_escalabilidad_desde}" data-tipo_notificacion="escalabilidad"/>
									<div style="display:inline" class="desabilitado">Hasta</div>
									<input type="text" id="maximo_{__objetivo_id}_{__notificacion_id}" class="maximo input_mini" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}" size="2" value="{__notificacion_escalabilidad_hasta}"  data-tipo_notificacion="escalabilidad"/>
									<div id="nalarmas" style="display: inline; width:100px" class="textnegro9 desabilitado" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}"></div>
									&nbsp;<input type="checkbox" class="sin_limite" id="sin_limite_{__objetivo_id}_{__notificacion_id}" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}" {__notificacion_escalabilidad_hasta_checked}  data-tipo_notificacion="escalabilidad"/>
									<div style="display:inline" class="desabilitado"> Sin Limite</div>
									
									<div class="contenedor_imagen" style="display:none">
										<img src="img/cargando.gif" width="11px" class="imagen"/>
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="3" style="background-color:#F6F6F6;">&nbsp;</td>
							</tr>
						</table>
						<table>
						<!-- BEGIN TIENE_NOTIFICACION_GLOBAL -->
							<tr>
								<td  class="celdanegra10" width="160px" >
									<input type="checkbox" class="chkbx global" id="global_{__objetivo_id}_{__notificacion_id}" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}" data-tipo_notificacion="global" {__notificacion_downtime_global}/>
									<b>Downtime Global</b>
								</td>
								<td width="12px" class="celdanegra10">									
									<div class="contenedor_imagen" style="display:none">
										<img src="img/cargando.gif" width="11px" class="imagen"/>
									</div>
								</td>
								<td  class="celdanegra10" >
									<div class="textgris9">Activa el envío de alertas de eventos <i>downtime</i> cuando se detecta un error desde todos<br> 
										los ISP monitoreados. El envío de alerta depende de la escalabilidad y del horario elegido.
									</div>
								</td>
							</tr>
													
							<tr>
								<td colspan="3" style="background-color:#F6F6F6;">&nbsp;</td>
							</tr>
							<!-- END TIENE_NOTIFICACION_GLOBAL -->
							<!-- BEGIN TIENE_NOTIFICACION_PARCIAL -->
							<tr>
								<td class="celdanegra10" width="160px" >
									<input type="checkbox" class="chkbx parcial" id="parcial_{__objetivo_id}_{__notificacion_id}" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}"  data-tipo_notificacion="parcial" {__notificacion_downtime_parcial}/>
									<b>Downtime Parcial</b>
								</td>
								<td width="12px" class="celdanegra10">									
									<div class="contenedor_imagen" style="display:none">
										<img src="img/cargando.gif" width="11px" class="imagen"/>
									</div>
								</td>
								<td class="celdanegra10" >
								<div class="textgris9">
									Activa el envío de alertas de eventos <i>downtime</i> cuando se detecta un error en cualquier 
									de los ISP monitoreados. El envío de alerta depende de la escalabilidad y del horario elegido.
									<div class="ayuda" data-tipo_ayuda="parcial" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}" style="display:inline;cursor:pointer">[...]</div>
									<div style="display:none" id="ayuda_parcial_{__objetivo_id}_{__notificacion_id}" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}">
										Notar que recibirá una alerta desde cada uno de los ISP con error, de manera independiente.									
									</div> 

								</div></td>
							</tr>	 
							<tr>
								<td colspan="3"  style="background-color:#F6F6F6;">&nbsp;</td>
							</tr>	
							<!-- END TIENE_NOTIFICACION_PARCIAL -->	
							<!-- BEGIN TIENE_NOTIFICACION_GRUPAL -->																			
							<tr>
								<td class="celdanegra10" >
									<input type="checkbox" class="chkbx grupal" id="grupal_{__objetivo_id}_{__notificacion_id}" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}" data-tipo_notificacion="grupal" {__notificacion_downtime_grupal}/>
									<b>Downtime  Grupal</b>
								</td>
								<td width="12px" class="celdanegra10">									
									<div class="contenedor_imagen" style="display:none">
										<img src="img/cargando.gif" width="11px" class="imagen"/>
									</div>
								</td>
								<td class="celdanegra10" ><div class="textgris9">Enviar&aacute; notificaciones cuando la respuesta de un grupo de ISP no sean las esperadas.</div></td>
							</tr>							
							<tr>
								<td colspan="3" style="background-color:#F6F6F6;">&nbsp;</td>
							</tr>
							<!-- END TIENE_NOTIFICACION_GRUPAL -->
							<!-- BEGIN TIENE_NOTIFICACION_OK -->																																																					
							<tr>
								<td class="celdanegra10" >
									<input type="checkbox" class="chkbx uptime" id="uptime_{__objetivo_id}_{__notificacion_id}" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}"  data-tipo_notificacion="uptime" {__notificacion_uptime_parcial}/>
									<b>Uptime</b>
								</td>
								<td width="12px" class="celdanegra10">
									<div class="contenedor_imagen" style="display:none">
										<img src="img/cargando.gif" width="11px" class="imagen"/>
									</div>
								</td>
								<td class="celdanegra10" >
									<div class="textgris9">
										Activa el envío de una notificación cuando se pase de un downtime global y/o parcial a uptime,
										siempre y cuando haya sido generada una alerta anteriormente.
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="3" style="background-color:#F6F6F6;">&nbsp;</td>
							</tr>	
							<!-- END TIENE_NOTIFICACION_OK -->
							<!-- BEGIN TIENE_NOTIFICACION_SLA -->																																																																																																																							
							<tr>
								<td class="celdanegra10" >		
									<input type="checkbox" class="umbral" id="umbral_obj_{__objetivo_id}_{__notificacion_id}" data-objetivo_id="{__objetivo_id}" data-monitor_id="{__monitor_id}" data-notificacion_id="{__notificacion_id}" name="notificacion_sla"  data-tipo_notificacion="umbral" {__notificacion_sla}  {__form_disabled}>							
									<b>Alerta Umbral</b>
								</td>
								<td width="12px" class="celdanegra10">
								
									<div class="contenedor_imagen" style="display:none">
										<img src="img/cargando.gif" width="11px" class="imagen"/>
									</div>
								</td>
								<td class="celdanegra10" >
								<div class="textgris9" style="width: 300px; display:inline">
									Activa el envío de una alerta cada vez que el tiempo que demore alguno de los pasos del monitoreo 
									sea mayor al configurado, independiente de si el resultado es UPTIME o DOWNTIME.
									</div>								
								&nbsp;&nbsp;<div style="display:inline; cursor:pointer;" class="boton_avanzado" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}"><b>[Configurar]</b></div>
								&nbsp;&nbsp;<div class="ayuda textgris9" data-tipo_ayuda="umbral" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}" style="display:inline;cursor:pointer">[...]</div>
								<div style="display:none" class="textgris9" id="ayuda_umbral_{__objetivo_id}_{__notificacion_id}" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}" >
									La configuración de umbrales es por objetivo, por lo tanto si existe más de una alerta <br> 
									de umbral configurada, utilizarán la misma definición de umbrales.
								</div>

								
								</td>
							</tr>	
							<tr>
								<td colspan="3" class="celdanegra10" align="center" style="padding-top:15px">
									
									<table width="80%"><tr><td width="30" align="center"> <img src="img/advertencia.png" /></td><td class="celdanegra10"> De existir al menos una alerta de umbral definida, esta se enviará a todos los contactos de alertas parciales configuradas.</td></tr></table></div>
								</td>
							</tr>
							<!-- END TIENE_NOTIFICACION_SLA -->																					
						</table>
					</td>				
				</tr>

			</table>
			<br>
			<div style="padding:0 25 0 25">
				<div id="avanzado_{__objetivo_id}_{__notificacion_id}" style="display:none">					
					<table width="100%">
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>
							<!-- BEGIN LISTA_MONITORES -->
							<div id="grupo_monitor_{__objetivo_id}_{__notificacion_id}_{__monitor_selector}" data-objetivo_id="{__objetivo_id}" data-monitor_selector="{__monitor_selector}" data-monitor_id="{__monitor_id}" data-notificacion_id="{__notificacion_id}" class="celdaselector {__objetivo_id}_{__notificacion_id}" >
								{__monitor_nombre}
							</div>
							<!-- END LISTA_MONITORES -->
							</td>
						</tr>
						<tr>
							<td height="20"></td>
						</tr>
						<tr>
							<td>
								<!-- BEGIN LISTA_PASOS_MONITORES -->
								<div id="grupo_monitor_sel_{__objetivo_id}_{__notificacion_id}_{__monitor_selector}" class="grupo_objetivo_{__objetivo_id}_{__notificacion_id}" style="display:none;">
									<table width="100%" class="listado">
										<tr>
											<th width="5%">&nbsp;</th>
											<th>Nombre</th>
											<th width="10%">Timeout</th>
											<th width="20%">Umbral</th>											
										</tr>
										<!-- BEGIN LISTA_PASOS -->
										<tr>
											<td align="center">{__paso_orden}</td>
											<td>{__paso_nombre}</td>
											<td>{__paso_timeout}</td>
											<td>
												<input type="text" class="obj_mon_paso umbral_{__objetivo_id}_{__monitor_id}_{__paso_id}" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}" data-monitor_id="{__monitor_id}" data-paso_id="{__paso_id}" data-tipo_notificacion="umbral" size="6" id="paso_sla_{__notificacion_id}_{__monitor_id}_{__paso_id}" value="{__paso_sla}" {__form_disabled}>
												&nbsp;&nbsp;&nbsp;
												<div class="contenedor_imagen" style="display:none">
													<img src="img/cargando.gif" width="11px" class="imagen"/>
												</div>	
											</td>
										</tr>
										<!-- END LISTA_PASOS -->
									</table>
								</div>
								<!-- END LISTA_PASOS_MONITORES -->
							</td>
						</tr>
					</table>			
				</div>
			</div>
			<table align="center">
				<tr>	
					<!-- BEGIN BLOQUE_GUARDA_ALARMA_NUEVA -->
					<td align="right">
						<input type="button" class="boton_accion" value="Crear" onclick="creaNotificacion({__objetivo_id},{__notificacion_id});" />
					</td>
					<td style="width:20px">&nbsp;</td>
					<td align="left">
						<input type="button" style="background-color:#ffffff; border:1px solid #b3b3b3;font-size: 11px;padding: 3px 3px 3px;cursor: pointer;color: #000000;" value="Cancelar" class="cancela_nueva_alarma" data-objetivo_id="{__objetivo_id}" data-notificacion_id="{__notificacion_id}"/>
					</td>
					<!-- END BLOQUE_GUARDA_ALARMA_NUEVA -->
				</tr>
			</table>
			<br>
			 </td>
    
  </tr>
</table>