<script>
/*SE ENCARGA DE PREGUNTAR SI ESTÁ SEGURO DE LA ELIMINACIÓN*/
function validarEliminar(notificacion_id, destinatario_nombre) {
	if(confirm("¿Esta seguro que desea eliminar la alarma asociada al contacto '"+destinatario_nombre+"'?")) {
		abrirAccion(1,'eliminar_notificacion',['notificacion_id',notificacion_id]);
	}
}

//FUNCION ENCARGADA DE CARGAR EL GRAFICO DE DISPONIBILIDAD CONSOLIDADA SIMPLE
function cargaSVG(objetivo_id,reacomoda) {
	if (tiene_svg == 0) {
		dijit.byId("svg_"+objetivo_id).attr('content', '');
		return;
	}
	var sitio_id = document.form_principal.sitio_id.value;
	var menu_id = document.form_principal.menu_id.value;
	var objeto_id = document.form_principal.objeto_id.value;

	dojo.xhrPost({
		url: "index.php?sitio_id="+sitio_id+"&menu_id="+menu_id+
			 "&objeto_id="+objetivo_id+'&subobjeto_id='+objetivo_id+
			 "&ejecutar_accion=1&accion=creaSVG&solo_action=t"+
			 "&item_tipo=html",			 
		
		load: function(data){
			if (data.trim() === "LOGOUT") {
				logout();
				return;
			}
			var param = data.split('|');
			var tabla = param[0];
		
			dijit.byId("svg_"+objetivo_id).attr('content', data);
			document.getElementById("svg_"+objetivo_id).style.display="inline";
			if(reacomoda=='true'){
				document.getElementById("svg_"+objetivo_id).scrollIntoView(true);			
			}
		}
	});
}

/*FUNCION QUE RECORRE LOS PASOS SACANDO LOS VALORES DE UMBRAL*/
function datosUmbral(objetivo_id, notificacion_id){
	var temp=$("div.celdaselector[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]");
	var paso_id=0;
	var monitor_id=0;
	var monitor_selector=0;
	var inputs="";
	$("div.celdaselector[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").each(function(elem){
		monitor_selector=$(this).data("monitor_selector");
		monitor_id=$(this).data("monitor_id");

		$(".obj_mon_paso[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"][data-monitor_id="+monitor_id+"]").each(function(elem){
			paso_id=$(this).data("paso_id");
			inputs=inputs+"&paso_sla_"+monitor_id+"_"+paso_id+"="+$(".obj_mon_paso[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"][data-monitor_id="+monitor_id+"][data-paso_id="+paso_id+"]").val();							
		});
	});

	return inputs;
}


//FUNCIÓN QUE OCULTA O MUESTRA EL DETALLE DE LAS ALARMAS CUANDO SE PRESIONA EL BOTON EDITAR
$(".editar").live("click",function(){
	if(document.getElementById("descrip_alarma_"+$(this).data("notificacion_id")).style.display=="none"){
		$("#detalle_alarma_"+$(this).data("notificacion_id")).attr("style","display:none");
		$("#descrip_alarma_"+$(this).data("notificacion_id")).attr("style","display:inline");
		}else{
		$("#descrip_alarma_"+$(this).data("notificacion_id")).attr("style","display:none");
		$("#detalle_alarma_"+$(this).data("notificacion_id")).attr("style","display:inline");
	}
	
});


//GATILLO PARA MODIFICAR LA NOTIFICACION CADA VEZ QUE SE EDITE UN VALOR DEL UMBRAL
$(".obj_mon_paso").live("change",function(event) {		
	if(!isNaN($(this).val())){
		modificaNotificacion($(this).data("objetivo_id"),$(this).data("notificacion_id"), $(this).attr("id"));
	}else{
		alert("Los umbrales deben tener valor númerico");
		$(this).val("");
		return false;
	}
});


//GATILLO PARA CERRAR LA VENTANA DE NUEVA ALARMA
$(".cancela_nueva_alarma").live("click",function(event) {
	$("#detalle_alarma_objetivo_"+$(this).data("objetivo_id")).attr("style","display:none");
});
//GATILLO PARA ACTUALIZAR LA NOTIFICACION CUANDO SE MODIFIQUE EL DESTINATARIO
$(".destinatario").live("change",function(event) {		
	modificaNotificacion($(this).data("objetivo_id"),$(this).data("notificacion_id"), $(this).attr("id") );
});
//GATILLO PARA ACTUALIZAR LA NOTIFICACION CUANDO SE MODIFIQUE EL HORARIO
$(".horario").live("change",function(event) {		
	modificaNotificacion($(this).data("objetivo_id"),$(this).data("notificacion_id"), $(this).attr("id"));
});
//GATILLO PARA ACTUALIZAR LOS UMBRALES CUANDO SE PRESIONA EL CHECKBOX DE UMBRAL (HAY UN LLAMADO SIMILAR EN TABLA_DETALLE_NOTIFICACOION, ESE SE UTLIZA EN LA CARGA PARA DETERMINAR SI DEBE O NO ESTAR BLOQUEADO DE UN PRINCIPIO)

$( ".umbral" ).live("click",function(event) {
	bloqueaUmbral($(this));
	modificaNotificacion($(this).data("objetivo_id"),$(this).data("notificacion_id"), $(this).attr("id"));
});




$(".ayuda").live("click", function(){
	var objetivo_id=$(this).data("objetivo_id");
	var notificacion_id=$(this).data("notificacion_id");
	var tipo = $(this).data("tipo_ayuda");
	if(document.getElementById("ayuda_"+tipo+"_"+objetivo_id+"_"+notificacion_id).style.display!="none"){
		document.getElementById("ayuda_"+tipo+"_"+objetivo_id+"_"+notificacion_id).style.display="none";
	}
	else{
		document.getElementById("ayuda_"+tipo+"_"+objetivo_id+"_"+notificacion_id).style.display="inline-block";
	}
	
});


//FUNCION QUE BLOQUEA A EDICION LOS VALORES DE UMBRAL
function bloqueaUmbral(umbral){
	var objetivo=$(umbral).data("objetivo_id");
	
    if($(umbral).is(":checked")){
    	$(".obj_mon_paso[data-notificacion_id="+$(umbral).data("notificacion_id")+"]").prop("disabled",false);
	}
    else{
    	$(".obj_mon_paso[data-notificacion_id="+$(umbral).data("notificacion_id")+"]").prop("disabled",true);
    }	
}


/*FUNCIÓN ENCARGADA DE REPLICAR LOS CAMBIOS HECHOS EN EL UMBRAL PARA TODAS LAS NOTIFICACIONES*/
$( ".obj_mon_paso" ).live("change",function(event) {
	var objetivo=$(this).data("objetivo_id");
	var monitor=$(this).data("monitor_id");
	var paso = $(this).data("paso_id");	
	var notificacion =	$(this).data("notificacion_id");
	if(parseInt(notificacion)>0)
		$("input[data-objetivo_id="+objetivo+"][data-paso_id="+paso+"][data-monitor_id="+monitor+"]").val($(this).val());		
	
});


//FUNCION QUE MUESTRA U OCULTA LAS NOTIFICACIONES DE UN OBJETIVO
function cargaObjetivo(objetivo_id,tipo,reacomoda){
	var estado=document.getElementById("contenedor_obj_"+objetivo_id).style.display;
	
	cargaSVG(objetivo_id,reacomoda);
	if(tipo=='new'){
            document.getElementById("contenedor_obj_"+objetivo_id).style.display="inline";
            document.getElementById("detalle_alarma_objetivo_"+objetivo_id).style.display="inline";
            $("#tr_objetivo_"+objetivo_id).find("td").attr("class","celdanegra40");
            $("#div1_objetivo_"+objetivo_id).attr("class","textblanco12");
            $("#div2_objetivo_"+objetivo_id).attr("class","textblanco9");
            document.getElementById("abrir_objetivo_"+objetivo_id).className = "spriteButton spriteButton-cerrar_calendario_blanco";
	}
	else{
		if(estado=="inline"){
                    document.getElementById("contenedor_obj_"+objetivo_id).style.display="none";
                    $("#tr_objetivo_"+objetivo_id).find("td").attr("class","celdanegra10");
                    $("#div1_objetivo_"+objetivo_id).attr("class","textgris12");
                    $("#div2_objetivo_"+objetivo_id).attr("class","textgris9");
                    document.getElementById("abrir_objetivo_"+objetivo_id).className = "spriteButton spriteButton-abrir_calendario";
		}
		else{
                    document.getElementById("contenedor_obj_"+objetivo_id).style.display="inline";
                    $("#tr_objetivo_"+objetivo_id).find("td").attr("class","celdanegra40");
                    $("#div1_objetivo_"+objetivo_id).attr("class","textblanco12");
                    $("#div2_objetivo_"+objetivo_id).attr("class","textblanco9");
                    document.getElementById("abrir_objetivo_"+objetivo_id).className = "spriteButton spriteButton-cerrar_calendario_blanco";
		}
	}
}

//FUNCION ENCARGADA DE MOSTRAR LAS NOTIFICACIONES DE UN OBJETIVO SI SE HA ELIMINADO O CREADO UNA
$("input[name='objeto_id']").ready(function(){	
	if(!isNaN($("input[name='objeto_id']").val()) && $("input[name='objeto_id']").val()>0)
		cargaObjetivo($("input[name='objeto_id']").val(),'old','true');
});

//GATILLO PARA MOSTRAR U OCULTAR LOS DATOS ASOCIADOS LOS MONITORES
$(".celdaselector").live("click",function(event) {
	var objetivo_id=$(this).data("objetivo_id");
	var monitor_id=$(this).data("monitor_selector");
	var notificacion_id = $(this).data("notificacion_id");	
	cambiarGrupoConfiguracion(objetivo_id, notificacion_id, monitor_id)
});


//FUNCION QUE MUESTRA U OCULTA LOS DATOS ASOCIADOS LOS MONITORES
function cambiarGrupoConfiguracion(objetivo_id, notificacion_id, monitor_id){
	$(".grupo_objetivo_"+objetivo_id+"_"+notificacion_id).attr("style","display:none");
	$("#grupo_monitor_sel_"+objetivo_id+"_"+notificacion_id+"_"+monitor_id).attr("style","display:inline");
	$(".celdaselector."+objetivo_id+"_"+notificacion_id).css("background-color","#F0EDE8");
	$(".celdaselector."+objetivo_id+"_"+notificacion_id).css("color","#525252");
	$("#grupo_monitor_"+objetivo_id+"_"+notificacion_id+"_"+monitor_id).css("background-color","#F36F00");
	$("#grupo_monitor_"+objetivo_id+"_"+notificacion_id+"_"+monitor_id).css("color","#FFFFFF");
}


//FUNCION QUE MODIFICA UNA ALARMA
function modificaNotificacion(objetivo_id,notificacion_id,notificacion){

	if(notificacion_id!=0){
		//BUSCAR VALORES POR NUMERO DE NOTIFICACION
		var maximo='';
		var notificacion_sla="";
		
		var nombre_maximo=$("#maximo_"+objetivo_id+'_'+notificacion_id).val();
			
		if($("#maximo_"+objetivo_id+'_'+notificacion_id).val()!='Infinito')
			maximo=$("#maximo_"+objetivo_id+'_'+notificacion_id).val();
		
		var parcial='';
		if($("#parcial_"+objetivo_id+'_'+notificacion_id).is(":checked"))
			parcial="&notificacion_downtime_parcial=true";
		var uptime='';
		if($("#uptime_"+objetivo_id+'_'+notificacion_id).is(":checked"))
			uptime="&notificacion_uptime_parcial=true";
		var global='';
		if($("#global_"+objetivo_id+'_'+notificacion_id).is(":checked"))
			global="&notificacion_downtime_global=true";
		var grupal="";
		if($("#grupal_"+objetivo_id+'_'+notificacion_id).is(":checked"))
			grupal="&notificacion_downtime_grupal=true";
		
		if($("#umbral_obj_"+objetivo_id+'_'+notificacion_id).is(":checked"))
			notificacion_sla="&notificacion_sla=true";
		
		umbral=datosUmbral(objetivo_id, notificacion_id);
		
		minimo=$("#minimo_"+objetivo_id+'_'+notificacion_id).val();

		var destinatario=$("#notificacion_destinatario_id_"+objetivo_id+'_'+notificacion_id).val();		
		if(parseInt(destinatario)<0){
			$("#"+notificacion).parent().parent().find("div.contenedor_imagen").attr("style","display:none");
			alert("Debe Seleccionar Usuario");			
			return false;	
			
		}	
		var horario=$("#notificacion_horario_id_"+objetivo_id+'_'+notificacion_id).val();
		if(horario<0){
			$("#"+notificacion).parent().parent().find("div.contenedor_imagen").attr("style","display:none");
			alert("Debe Seleccionar Horario");
			return false;
		}
		
		


		$("#"+notificacion).parent().parent().find("div.contenedor_imagen").attr("style","display:inline");
		$("#"+notificacion).parent().parent().find("img.imagen").attr("src","img/cargando.gif");
		
		
		dojo.xhrPost({
			url: "index.php",
			postData:"sitio_id=2&ejecutar_accion=1&accion=guardar_notificacion&notificacion_id="+notificacion_id+
					"&notificacion_escalabilidad_desde="+minimo+
					"&notificacion_escalabilidad_hasta="+maximo+
					grupal+parcial+uptime+global+umbral+"&notificacion_objetivo_id="+objetivo_id+
					"&notificacion_destinatario_id="+destinatario+
					"&notificacion_horario_id="+horario+
					"&notificacion_patron_inverso=''"+notificacion_sla,
					sync: true,					
			load: function(data){
				if(data.trim() === 'ok'){
					//SI RETORNA OK CAMBIA LA IMAGEN DE CARGANDO A DONE.PNG
					$("#"+notificacion).parent().parent().find("img.imagen").attr("src","img/done.png");
					$("#"+notificacion).parent().parent().find("img.imagen").attr("title","Configuración Guardada Exitosamente");														
					$("#nombre_destinatario_"+objetivo_id+"_"+notificacion_id).html($("#notificacion_destinatario_id_"+objetivo_id+"_"+notificacion_id+" option:selected").text());
					$("#nombre_horario_"+objetivo_id+"_"+notificacion_id).html($("#notificacion_horario_id_"+objetivo_id+"_"+notificacion_id+" option:selected").text());
					$("#nombre_desde_"+objetivo_id+"_"+notificacion_id).html(minimo);
					$("#nombre_hasta_"+objetivo_id+"_"+notificacion_id).html(nombre_maximo);
					mensaje=true;
				}
				else{//SI FALLO LA MODIFICACION CAMBIA LA IMAGEN A ERROR.PNG					
					$("#"+notificacion).parent().parent().find("img.imagen").attr("src","img/error.png");
					$("#"+notificacion).parent().parent().find("img.imagen").attr("title","Ocurrio un Error en el Proceso");
					mensaje=false;
				}
	
			}
		});
		
	}
	else{
		mensaje=false;
		}
	return mensaje;

}


//FUNCION QUE CREA UNA NUEVA NOTIFICACION ASOCIADA A UN OBJETIVO
function creaNotificacion(objetivo_id,notificacion_id){

	//BUSCAR VALORES POR NUMERO DE NOTIFICACION
	var maximo='';
	var notificacion_sla="";
		
	if($("#maximo_"+objetivo_id+'_'+notificacion_id).val()!='Infinito')
		maximo=$("#maximo_"+objetivo_id+'_'+notificacion_id).val();
	var parcial='';
	if($("#parcial_"+objetivo_id+'_'+notificacion_id).is(":checked"))
		parcial="&notificacion_downtime_parcial=true";
	var uptime='';
	if($("#uptime_"+objetivo_id+'_'+notificacion_id).is(":checked"))
		uptime="&notificacion_uptime_parcial=true";
	var global='';
	if($("#global_"+objetivo_id+'_'+notificacion_id).is(":checked"))
		global="&notificacion_downtime_global=true";
	var grupal="";
	if($("#grupal_"+objetivo_id+'_'+notificacion_id).is(":checked"))
		grupal="&notificacion_downtime_grupal=true";
	if($("#umbral_obj_"+objetivo_id+'_'+notificacion_id).is(":checked"))
		notificacion_sla="&notificacion_sla=true";
	var destinatario=$("#notificacion_destinatario_id_"+objetivo_id+'_'+notificacion_id).val();
	if(parseInt(destinatario)<0){
		alert("Debe Seleccionar Usuario");
		return false;	
	}	
	var horario=$("#notificacion_horario_id_"+objetivo_id+'_'+notificacion_id).val();
	if(horario<0){
		alert("Debe Seleccionar Horario");
		return false;
	}
	umbral=datosUmbral(objetivo_id, notificacion_id);

	
	dojo.xhrPost({
		url: "index.php",
		postData:"sitio_id=2&ejecutar_accion=1&accion=guardar_notificacion&notificacion_objetivo_id="+objetivo_id+
				"&notificacion_escalabilidad_desde="+$("#minimo_"+objetivo_id+'_'+notificacion_id).val()+
				"&notificacion_escalabilidad_hasta="+maximo+umbral+
				grupal+parcial+uptime+global+"&notificacion_objetivo_id="+objetivo_id+
				"&notificacion_destinatario_id="+destinatario+
				"&notificacion_horario_id="+horario+"&solo_action=t"+
				"&notificacion_patron_inverso=''"+notificacion_sla,
		load: function(data){			
			if(data.trim() === 'ok'){			
				//RECARGA LA PAGINA Y MANDA EL OBJETIVO_ID PARA QUE PUEDAN MOSTRARSE AUTOMATICAMENTE LAS NOTIFICACIONES DE EL 
				abrirEnlace('2','0',objetivo_id,'0');				
			}
			else{
				if(data.trim() === 'no'){
					alert("Hubo un Problema en la Grabación");
				}
				else{
					if(data.indexOf('fallo_el_envio')==0){
						alert('Hubo un Problema en el Envio de Correo');
						abrirEnlace('2','0',objetivo_id,'0');				
					}
					else{
						alert('Hubo un Problema en el Proceso');
						abrirEnlace('2','0',objetivo_id,'0');			
					}
				}
				
				
			}
		}
	});
	
}

//FUNCION QUE SE ENCARGA DE ELIMINAR UNA NOTIFICACION
function eliminaNotificacion(objetivo_id,notificacion_id){
	if(confirm("¿Está seguro de eliminar la notificación?")){
		$("img.imagen_borrar[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").attr('src','img/cargando.gif');		
		dojo.xhrPost({
			url: "index.php",
			postData:"sitio_id=2&ejecutar_accion=1&accion=eliminar_notificacion&notificacion_id="+notificacion_id+"&solo_action=t",
			load: function(data){
				if(data.trim() === 'ok'){					
					//RECARGA LA PAGINA Y MANDA EL OBJETIVO_ID PARA QUE PUEDAN MOSTRARSE AUTOMATICAMENTE LAS NOTIFICACIONES DE EL
					abrirEnlace('2','0',objetivo_id,'0');					
				}
				else{
					if(data.trim() === 'no')
					  alert("Hubo un Problema en la Eliminación");
					else{				      
					  alert('La información se guardó pero ocurrió un error en el envío');
						abrirEnlace('2','0',objetivo_id,'0');					
					}
				}
	
			}
		});

	}
}


//ESTE BOTON MUESTR U OCULTA LOS PASOS CONTENIDOS EN EL MONITOR
$(".boton_avanzado").live("click",function(event) {
	var notificacion=$(this).data("notificacion_id");
	var objetivo=$(this).data("objetivo_id");	
	cambiarGrupoConfiguracion(objetivo, notificacion,'0');
	if(document.getElementById('avanzado_'+objetivo+'_'+notificacion).style.display=="none"){
		$('#avanzado_'+objetivo+'_'+notificacion).attr('style','display:inline');
	}
	else{
		$('#avanzado_'+objetivo+'_'+notificacion).attr('style','display:none');
	}

});

/*IDENTIFICA EL FONDO QUE DEBE TENER EL GRÁFICO DEPENDIENDO DE LOS CHECKBOX QUE SE ENCUENTREN ACTIVADOS*/
function cambioSeleccionEscalabilidad(padre){
	var objetivo_id=$(padre).data("objetivo_id");
	var notificacion_id=$(padre).data("notificacion_id");
	
	var imagen="barra";		
	if($(padre).find("input.global").is(":checked"))				
		imagen=imagen+"_roja";												

	if($(padre).find("input.parcial").is(":checked"))				
		imagen=imagen+"_amarilla";						

	if($(padre).find("input.grupal").is(":checked"))				
		imagen=imagen+"_naranja";						
			
	var barra=$(padre).find("div.slider_range").find(".ui-widget-header");
	var barra2=$(padre).find("div.slider_range");
	barra.attr("style","background:url(img/barras_slider/"+imagen+".png);background-size:18 18");		
	
	var minimo= $(padre).find("input.minimo").val();			
	var maximo= parseInt($(padre).find("input.maximo").val())+parseInt(reajuste);	

	if(maximo=='Infinito'){
		maximo=fin[objetivo_id+'_'+notificacion_id];			
	}
	else{		
		if(parseInt(minimo)> parseInt(maximo))
			alert("El mínimo no puede ser mayor que el máximo");
	}
	
	/*REDIBUJA EL GRÁFICO*/
	reacomodaSlider(barra2,minimo,maximo,false,fin[objetivo_id+'_'+notificacion_id]);
}

/*FUNCION QUE REDIBUJA EL SLIDER DEPENDIENDO DE LOS VALORES DE ENTRADA Y SI TIENE O NO LA POSIBILIDAD DE EDITAR EL VALOR DE TERMINO*/
/*(NO MODIFICA LOS VALORES DEL MAXIMO Y MINIMO, SOLO CAMBIA EL GRÁFICO)*/
function reacomodaSlider(actual,min,max, desactivado, max_escala){
	var notificacion_id=$(actual).data("notificacion_id");
	var objetivo_id=$(actual).data("objetivo_id");			
	var barra=$("div.slider_range[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]"); 
	if($("input.sin_limite[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").is(":checked")){				
		$(barra).slider({	
			value: parseInt(min),
			max: parseInt(max_escala),
			min:1					
		});				
		$(barra).slider({	
			value: parseInt(min),
			max: parseInt(max_escala),
			min:1					
		});				
	}
	else{
		$(barra).slider({	
			values: [ parseInt(min), parseInt(max) ],
			max: parseInt(max_escala),
			min:1
		});
		
		$(barra).slider({	
			values: [ parseInt(min), parseInt(max) ],
			max: parseInt(max_escala),
			min:1
		});			

	}
}


//FUNCION QUE BLOQUEA LA BARRA Y CUADROS DE ESCALABILIDAD, ELBOTON UPTIME JUNTO A LAS LETRAS
function bloqueaUptime(objetivo_id, notificacion_id){
	if(!$("#parcial_"+objetivo_id+"_"+notificacion_id).is(":checked") && !$("#global_"+objetivo_id+"_"+notificacion_id).is(":checked")){
		//BLOQUEA EL UPTIME Y LA BARRA
		$("#uptime_"+objetivo_id+"_"+notificacion_id).attr('checked', false);
		$("#uptime_"+objetivo_id+"_"+notificacion_id).prop('disabled',true);
		$("table[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").find("div.desabilitado").attr('style','display:inline-block; color:#e3e3e3');
		$("table[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").find("div#nalarmas").attr('style','display:inline-block; color:#e3e3e3; width:100px');
		var minimo=$("#minimo_"+objetivo_id+"_"+notificacion_id).val();
		var maximo=$("#maximo_"+objetivo_id+"_"+notificacion_id).val();
		$("#minimo_"+objetivo_id+"_"+notificacion_id).prop('disabled',true);
		$("#maximo_"+objetivo_id+"_"+notificacion_id).prop('disabled',true);
		$("#sin_limite_"+objetivo_id+"_"+notificacion_id).prop('disabled',true);
		$(".slider_range[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").slider({	
			disabled:true			
		});
	
	}
	else{
		$("#uptime_"+objetivo_id+"_"+notificacion_id).prop('disabled',false);
		var minimo=$("#minimo_"+objetivo_id+"_"+notificacion_id).val();
		var maximo=$("#maximo_"+objetivo_id+"_"+notificacion_id).val();
		$("#minimo_"+objetivo_id+"_"+notificacion_id).prop('disabled',false);
		$("table[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").find("div.desabilitado").attr('style','display:inline-block');				
		$("table[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").find("div#nalarmas").attr('style','display:inline-block; width:100px');
		$("#maximo_"+objetivo_id+"_"+notificacion_id).prop('disabled',false);
		$("#sin_limite_"+objetivo_id+"_"+notificacion_id).prop('disabled',false);
		$(".slider_range[data-objetivo_id="+objetivo_id+"][data-notificacion_id="+notificacion_id+"]").slider({	
			disabled:false			
		});
	}			
}

//EL REAJUSTE MARCA LA SEPARACION EXISTENTE EN LA BARRA
var reajuste=3;

//VARIABLE QUE CONTIENE EL MAXIMO VALOR DIBUJAR EL GRAFICO DE ESCALABILIDAD PARA CADA NOTIFICACION
var fin=[];
</script>


<!-- <div dojoType="dijit.Dialog" id="dialog_notificacion" title="Informacion de Alerta"></div> -->
<body id="body">
<input type="hidden" name="notificacion_id" value="0">
<table width="100%">
	<tr>
		<td class="tituloseccion">Lista de Alertas</td>
	</tr>
	<tr>
		<td>
			<br>
			<div class="descripcion">
				A continuación se muestran los objetivos que permiten alertas. Según el servicio verá las opciones disponibles.<br>
Como ayuda se despliega un gráfico de la disponibilidad de las últimas 24 horas, que le servirá para definir de 
mejor manera sus alarmas y escalabilidad de cada una.<br> 
			</div>
			<br>

		</td>
	</tr>
	<!-- BEGIN LISTA_OBJETIVO -->
	<tr>
		<td>
			<table width="100%">
				<tr id="tr_objetivo_{__idObjetivo}">					
					<td class="celdanegra10" width="350px">
						<div class="textgris12" id="div1_objetivo_{__idObjetivo}" style="width:320px;overflow:hidden">
							<b>{__nombreObjetivo}</b>
						</div>
						<div class="textgris9" id="div2_objetivo_{__idObjetivo}">
							{__nombreServicio}
						</div>
					</td>
					<td class="celdanegra10" width="130" style="{__resaltaAlarmas}">{__numeroAlarmas} Alertas</td>
					<!-- BEGIN MOSTRAR_NOTIFICACIONES_DISPONIBLES -->
					<td class="celdanegra10" width="150">Disponibles: {__notificacion_disponible}</td>
					<!-- END MOSTRAR_NOTIFICACIONES_DISPONIBLES -->	
					<!-- BEGIN PUEDE_AGREGAR -->
					<td class="celdanegra10" width="170"  onclick="cargaObjetivo({__idObjetivo},'new','false');abrirAccionDetalle('detalle_alarma_objetivo_{__idObjetivo}',0,'agregar_notificacion','a',{__idObjetivo})" style="cursor:pointer"><b>[Agregar Alerta]</b></td>
					<!-- END PUEDE_AGREGAR -->

					<td class="celdanegra10" style="cursor:pointer; padding:0 10 0 0" onclick="cargaObjetivo('{__idObjetivo}','old','false')" align="right"><i id="abrir_objetivo_{__idObjetivo}" class="spriteButton spriteButton-abrir_calendario"></i></td>
				</tr>
				
				<tr>
					<td colspan="100%">
						<div id="contenedor_obj_{__idObjetivo}" class="contenedor_objetivos" style="display:none">
							<table width="100%">
								<tr>
									<td colspan="100%" class="celdanegra10" style="padding-top:5px">
										<div id="svg_{__idObjetivo}" dojotype="dojox.layout.ContentPane" style="text-align: center" tabindex="-1">
										  <img src="img/cargando.gif"/>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="100%">
										<div id="detalle_alarma_objetivo_{__idObjetivo}" data-objetivo_id="{__idObjetivo}" data-notificacion_id="0" dojotype="dojox.layout.ContentPane" ></div>
									</td>
								</tr>
								<!-- BEGIN BLOQUE_SIN_NOTIFICACIONES -->
								<tr>
									<td class="celdanegra10" align="center"><div class="textgris12"><b>No tiene alertas creadas</b></div></td>
								</tr>
								<!-- END BLOQUE_SIN_NOTIFICACIONES -->
							<!-- BEGIN ALARMAS_OBJETIVO -->							
								<tr>
									<td class="celdanegra10">
										<div id="descrip_alarma_{__notificacion_id}">
											<table width="730px">
												<tr>
													<td class="celdanegra10" width="200px">
														<div class="textgris12" id="nombre_destinatario_{__idObjetivo}_{__notificacion_id}">
															{__destinatario_nombre}
														</div>
													</td>
													<td class="celdanegra10" width="150px" align="left">
														<div id="nombre_horario_{__idObjetivo}_{__notificacion_id}">
															{__horario_nombre}
														</div>
													</td>				
													<td class="celdanegra10" width="330px" align="left" style=" display:inline">Desde    <div id="nombre_desde_{__idObjetivo}_{__notificacion_id}" style="display:inline">{__escalabilidad_desde}</div>     Hasta <div id="nombre_hasta_{__idObjetivo}_{__notificacion_id}"  style="display:inline">   {__escalabilidad_hasta}</div></td>
													<td class="celdanegra10"  width="25px">
													<!-- BEGIN PUEDE_EDITAR -->
														<i title="Editar Notificación" style="cursor:pointer" class="editar spriteButton spriteButton-editar" data-notificacion_id="{__notificacion_id}"  onclick="abrirAccionDetalle('detalle_alarma_{__notificacion_id}',0,'modificar_notificacion',['notificacion_id','{__notificacion_id}'],{__idObjetivo});"></i>
													<!-- END PUEDE_EDITAR -->	
													</td>
													<td class="celdanegra10" width="25px">									
													<!-- BEGIN PUEDE_ELIMINAR -->
														<i class="imagen_borrar spriteButton spriteButton-borrar" data-objetivo_id="{__idObjetivo}"  data-notificacion_id="{__notificacion_id}" title="Eliminar Notificación" style="cursor:pointer" onclick="eliminaNotificacion('{__idObjetivo}','{__notificacion_id}')"></i>
													<!-- END PUEDE_ELIMINAR -->
													</td>									
	
												</tr>
											</table>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="100%" style="background-color:#F6F6F6;">										
										<div dojoType="dojox.layout.ContentPane" id="detalle_alarma_{__notificacion_id}" data-objetivo_id="{__idObjetivo}" data-notificacion_id="{__notificacion_id}" style="display:none" >
										</div>
									</td>
								</tr>
							<!-- END ALARMAS_OBJETIVO -->				
							</table>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="100%">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- END LISTA_OBJETIVO -->

</table>
</body>