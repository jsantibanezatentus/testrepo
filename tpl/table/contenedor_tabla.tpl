<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>
<!-- BEGIN LISTA_CONTENEDORES -->
      <div dojoType="dojox.layout.ContentPane" id="subcontenedor_{__contenido_id}" style="height: auto;">
    {__contenido_tabla}
      </div>
<!-- END LISTA_CONTENEDORES -->
<table id ="tableEventos">
</table>


<script type="text/javascript">
$(function() {

// Ejecuta la inialización del acordeon.
if ('{__tiene_evento}' == 'true'){

	$('#man').show();
	createAccordion('accordionEvento');	
}
});
if('{__valid_contenido}'==true){
	json = ('{__contenido}')

	nombreobj = ('{__nombre_obj}')

	json = JSON.parse(json)
	array_paso = (json[0]["data_nodo"][0][0]["pasos"])
	$.each(json, function( key_nodos, nodos) {
		tr = '<tr><td style="border: solid 1px #ffffff;" class="celdanegra50" colspan="7">'+nodos.nombre_nodo+'</td></tr>'
		//parte de nodos
		$.each(nodos["data_nodo"], function(key_eventos, eventos){
			tr += '<tr><td style="border: solid 1px #ffffff;" class="celdanegra40" width="28%">'+nombreobj+'</td>'
			//parte de nombre y eventos
			$.each(eventos, function(key_data, data){
				tr += '<td style="border: solid 1px #ffffff;" class="celdanegra40" width="12%" align="center">'+data["desde"]+'</td>'
			})
			//cierre de nombre y eventos
			tr +='</tr>'
			//cierre de nodos
			for (var i =0; i <= array_paso.length-1; i++) {
				if(array_paso[i].patrones){
					for (var j = 0; j <= array_paso[i].patrones.length-1; j++) {
						if(j == 0){
							nombre_paso = array_paso[i].nombre_paso
						}else{
							nombre_paso= ''
						}
						tr += '<tr><td style="border-right: solid 1px #ffffff;" class="celdanegra20 " width="28%" valign="top">'+nombre_paso+'</td>'
						$.each(eventos, function(index_event, event){
							if(event.pasos[i].patrones[j].estado==0||event.pasos[i].patrones[j].estado==602){
								tr += '<td height="22" bgcolor="#55a51c" align="center">'
								if(event.pasos[i].patrones[j].estado==0){
									estado = 'ok'
								}else{
									estado = 'answer'
								}
								tr += '<i class="sprite sprite-'+estado+'"></i></td>'
							}else{
								if(event.pasos[i].patrones[j].estado=='sin monitoreo'){
									tr +='<td  height="22" bgcolor="#c4c4c4" align="center"><i class="sprite sprite-no_monitoreo"></i></td>'
								}else{
									tr += '<td height="22" bgcolor="#d3222a" align="center">'
									if(event.pasos[i].patrones[j].estado==3){
										estado = 'timeout';
									}else if(event.pasos[i].patrones[j].estado==13){
										estado = 'sin_contenido'
									}else if(event.pasos[i].patrones[j].estado==27){
										estado = 'timeout_js'
									}else if(event.pasos[i].patrones[j].estado==1006){
										estado = 'timeout_elemento'
									}else if(event.pasos[i].patrones[j].estado==500){
										estado = '500'
									}else if(event.pasos[i].patrones[j].estado==613){
										estado = 'error_contenido'
									}else if(event.pasos[i].patrones[j].estado==603){
										estado = 'call_busy'
									}else if(event.pasos[i].patrones[j].estado==610){
										estado = 'call_hangup'
									}else if(event.pasos[i].patrones[j].estado==999999){
										estado = 'script'
									}else if(event.pasos[i].patrones[j].estado==400){
										estado = '400'
									}else if(event.pasos[i].patrones[j].estado==607){
										estado ='timeout_call'
									}else if(event.pasos[i].patrones[j].estado==601){
										estado = 'call_congestion'
									}else if(event.pasos[i].patrones[j].estado==605){
										estado = 'no_answer'
									}else{
										estado = 'sin_contenido'
									}
									tr +='<i class="sprite sprite-'+estado+'"></i></td>'
								}
							}
						})
						tr += '</tr>'
					}
				}else{
					tr += '<tr><td style="border: solid 1px #ffffff;" class="celdanegra20 " width="28%" valign="top">'+array_paso[i].nombre_paso+'</td>'
					$.each(eventos, function(index_event, event){
						if(event.pasos[i].estado==0|| event.pasos[i].estado==602){
							tr += '<td height="22" bgcolor="#55a51c" align="center">'
							if(event.pasos[i].estado==0){
								estado = 'ok'
							}else{
								estado = 'answer'
							}
							tr += '<i class="sprite sprite-'+estado+'"></i></td>'
						}else{
							if(event.pasos[i].estado=='sin monitoreo'){
								tr +='<td  height="22" bgcolor="#c4c4c4" align="center"><i class="sprite sprite-no_monitoreo"></i></td>'
							}else{
								tr += '<td height="22" bgcolor="#d3222a" align="center">'
								if(event.pasos[i].estado==3){
									estado = 'timeout';
								}else if(event.pasos[i].estado==13){
									estado = 'sin_contenido'
								}else if(event.pasos[i].estado==27){
									estado = 'timeout_js'
								}else if(event.pasos[i].estado==1006){
									estado = 'timeout_elemento'
								}else if(event.pasos[i].estado==500){
									estado = '500'
								}else if(event.pasos[i].estado==613){
									estado = 'error_contenido'
								}else if(event.pasos[i].estado==603){
									estado = 'call_busy'
								}else if(event.pasos[i].estado==610){
									estado = 'call_hangup'
								}else if(event.pasos[i].estado==999999){
									estado = 'script'
								}else if(event.pasos[i].estado==400){
									estado = '400'
								}else if(event.pasos[i].estado==607){
									estado = 'timeout_call'
								}else if(event.pasos[i].estado==601){
									estado = 'call_congestion'
								}else if(event.pasos[i].estado==605){
									estado = 'no_answer'
								}else if(event.pasos[i].estado==604){
									estado = 'call_noanswer'
								}else{
									estado = 'sin_contenido'
								}
								tr +='<i class="sprite sprite-'+estado+'"></i></td>'
							}
						}
					})
					tr += '</tr>'
				}
			}
			//abre tr de duracion
			tr += '<tr>'
			primero = true
			$.each(eventos, function(key_data, data){

				if(primero==true){
					primero = false
					tr += '<td style="border: solid 1px #ffffff;" class="celdaduracion" width="28%" height="35">&nbsp;</td><td style="border: solid 1px #ffffff;" class="celdaduracion" width="12%" height="35" align="center">'+data.duracion+'</td>'
				}else{
					tr += '<td style="border: solid 1px #ffffff;" class="celdaduracion" width="12%" height="35" align="center">'+data.duracion+'</td>'
				}
			})
			tr += '</tr>'
			//cierre de tr de duracion
		})
		$('#tableEventos').append(tr)
	})
}
</script>