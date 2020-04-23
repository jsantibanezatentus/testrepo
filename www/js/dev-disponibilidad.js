//funciones para crear los graficos de disponibilidad

// Función para inicializar el acordeon y setear datos a la tabla.
this.createAccordion = function(name){

	// Remueve cuando existe un duplicado.
	accordionEvent = $('[id="accordionEvento"]');
	if (accordionEvent.length ==2){
		accordionEvent[1].remove();
	}
	try {
    	id = name ;    		
	   	$("#"+id).accordion({ autoHeight: true});
	    $("#"+id).accordion({ collapsible: true });
	    $("#"+id).accordion({ active: false });		
		createTableMain(id);		
	}
	catch(err) {console.log(err);}
}
/* Crea las filas de la tabla. */
this.createTableMain = function(id){
	var tbody = "";
	var duplicateChk = {};

	elem = $("#"+id);
	data = elem.data('mantain');
	$("#tbody_"+id).empty();

	$("#"+id).each (function () {
	    if (duplicateChk.hasOwnProperty(this.id)) {
	       $(this).remove();
	    } else {
	       duplicateChk[this.id] = 'true';
	    }
	});
	$.each( data, function( index, value ){
		objIds = '';
		objIds=value.objetivo_id;
		tbody += '<tr><td style="border: solid 1px #ffffff;" class="celdanegra10" align="left">' + value.nombre +'</td><td style="border: solid 1px #ffffff;" class="celdanegra10" align="left"><a href="#" class="link-objetivos" title="Click Para Ver Objetivos." onclick="return getObjetivos(\''+objIds+'\');">'+value.titulo+'</a></td><td style="border: solid 1px #ffffff;" class="celdanegra10" align="left">' + value.fecha_inicio + '</td><td style="border: solid 1px #ffffff;" class="celdanegra10" align="left">' + value.fecha_termino + '</td><td style="border: solid 1px #ffffff;" class="celdanegra10" align="left"><button type="button" class="butonLeft" onclick="verDetalle('+value.id+')">Detalle</button></td></tr>';	
	});
	$("#tbody_"+id).append(tbody);
	
}

/* Muestra modal con el id y el nombre de los objetivos*/
function disponibilidad(){
	//variable que guarda el color de los eventos asociados a el estilo que viene del php
	var self = this;
	this.contenedor  = '';
	this.contenedor_div = '';
	this.contenedor_svg = '';
	this.nombreGrafico = '';
	this.nombreObjeto = '';
	this.link_eventos = 't';
	
	this.color={"No Monitoreo": "#eeeeee",
	           "Uptime Global": "#54a51c",
	           "Downtime Global": "#d22129",
	           "Downtime Parcial": "#fdc72e",
	       	   "Marcado Especial": "#BE81F7"}

	
	this.day = {0:'Domingo', 1:'Lunes', 2:'Martes', 3:'Miércoles', 4:'Jueves', 5:'Viernes', 6:'Sábado'};
	this.month = {0:'Enero', 1:'Febrero', 2:'Marzo', 3:'Abril', 4:'Mayo', 5:'Junio', 6:'Julio', 7:'Agosto',8:'Septiembre',9:'Octubre',10:'Noviembre',11:'Diciembre'};
    
	this.resources = '';
	this.grupos ='';
	this.datos ='';
	this.fechaMayor;
	this.fechaMenor;
	//valor del ancho de la zona de gráfico, ésta vairable es usada en el momento de transformar el tamaño del gráfico
	this.ancho=610;
	//variable que tiene el ancho total de la zona de gráfico, incluye el espacio para poner los nombres
	this.anchoTotal=715;
	//variable que almacena la escala en que está dibujado el gráfico
	this.escala=1;
	this.escalaActual=1;
	this.fechaSelect='';
	this.mesFinalizado=[];
	this.dias;
	this.altoCalendario = 42;	
    this.tipo =''
	this.vali =0;
	this.diasCalendario = '';
	this.altoTotal = ''
	this.alto = '';
	this.fechaMayor='';
    this.fechaMenor='';
    this.segundosTotales = '';
	this.diferenciaT = '';
    this.dias ='';
    this.monitor_id='';
    this.nodo_id='';
    this.horario_habil= [];
    colorEventoEspecial = 'rgb(190, 129, 247)';
    

    this.desasociar = function(){
    	$('#'+this.contenedor_div).find('[class~="zoom"]').die('click');
    	$('#'+this.contenedor_div).find(".cuadro2 ").die('mousemove');
    	$('#'+this.contenedor_div).find(".cuadro2 ").die('mouseout');
    	$('#'+this.contenedor_div).die('mouseout');
    	
    }

	this.creaSVG = function(){
		this.ancho=$('#'+this.contenedor_div).width() - 100;
		this.anchoTotal=$('#'+this.contenedor_div).width() - 10;
		
		var svg=document.createElementNS("http://www.w3.org/2000/svg", "svg");
		svg.setAttribute("width",$('#'+this.contenedor_div).width());
		svg.setAttribute("id",this.contenedor_svg);
		
		svg.setAttribute("xmlns","http://www.w3.org/2000/svg");
		
	//	svg.setAttribute("xmlns:xlink","http://www.w3.org/1999/xlink");s
		
		document.getElementById(this.contenedor_div).appendChild(svg);
		
		var rect=document.createElementNS("http://www.w3.org/2000/svg", "rect");
		rect.setAttribute("x",0);
		rect.setAttribute("y",0);
		rect.setAttribute("height","100%");
		rect.setAttribute("width","100%");
		rect.setAttribute("id","fondo");
		rect.setAttribute("class","fondoBlanco");
		rect.setAttribute("style","fill:#ffffff");
		svg.appendChild(rect);
		
	

		 
		
		var grupo=document.createElementNS("http://www.w3.org/2000/svg", "g");
		grupo.setAttribute("id",this.contenedor);
		svg.appendChild(grupo);
	}

	this.asociar = function(){
	    if(self.tipo=='simple'){
	    	self.altoCalendario =0;
	    }
	    else{
	    	
	    	this.creaSVG();
	    	this.desasociar();
	    	
		    $('#'+this.contenedor_div).find(".cuadro2 ").live('mousemove',function(e){
		    	posX = e.pageX - $('#'+self.contenedor_div).offset().left;
		  		posY = e.pageY - $('#'+self.contenedor_div).offset().top;	  		
		  		self.muestraTooltip($(this), posX, posY);
		  		
		  	});
		    /*
				Asignar evento click al periodo marcado especial
		    */
		  	$('#'+this.contenedor_div).find(".cuadro2 ").live('click',function(e){
		  		borderColor = $(this).css("fill");
		  		if (colorEventoEspecial == borderColor){
		  			posX = e.pageX - $('#'+self.contenedor_div).offset().left;
		  			posY = e.pageY - $('#'+self.contenedor_div).offset().top;	  		
		  			self.showModal($(this), posX, posY);
		  		}
		  	});
		    
		    $('#'+this.contenedor_div).live('mouseout',function(e){
		    	self.ocultaTooltip();
		    });
		      
		    $('#'+this.contenedor_div).find(".cuadro2 ").live('mouseout',function(e){
		  		self.restauraCuadro($(this));
		 		});
		
		    $('#'+this.contenedor_div).find('[class~="zoom"]').live('click',function(e){
		  		self.zoom(this.getAttribute("data-dia"), this.getAttribute("data-fecha") ,this.getAttribute("data-equis"));
		 		});

		    if(this.link_eventos=="t"){
		    $('#'+this.contenedor_div).find(".cuadro2 ").live('click',function(e){
		    	pagina = this.getAttribute('pagina');
		    	nodo = this.getAttribute('nodo');
		    	paso = this.getAttribute('pasos');
		    	fecha_monitoreo = this.getAttribute('fecha_monitoreo');
		    	if(nodo!=""){
		    		cargarSubItem('contenedor_11','subcontenedor_even_'+nodo,'11','141', ['monitor_id',nodo,'paso_id',paso,'fecha_monitoreo',fecha_monitoreo,'pagina',pagina]);
		    	}
		    });
		    }
		}
	}

	/*FUNCIÓN QUE DIBUJA LAS LINEAS VERTICAL Y HORIZONTAL DEL GRÁFICO*/
	this.creaEscalas = function() {
		
		//DIBUJO DE LA LÍNEA HORIZONTAL
		if(!document.getElementById("eje_1"+this.nombreGrafico)){
			var line=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var line=document.getElementById("eje_1"+this.nombreGrafico);
		}
		line.setAttribute("x1",95);
		line.setAttribute("x2",this.ancho*this.escala+95);
		line.setAttribute("y1",this.altoCalendario+this.alto);
		line.setAttribute("y2",this.altoCalendario+this.alto);
		line.setAttribute("id","eje_1"+this.nombreGrafico);
		line.setAttribute("class",'separaciones7 negro');
		
		//DIBUJO DE LA LÍNEA VERTICAL
		if(!document.getElementById("eje_2"+this.nombreGrafico)){
			var line2=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var line2=document.getElementById("eje_2"+this.nombreGrafico);
		}	
		var line2=document.createElementNS("http://www.w3.org/2000/svg", "line");
		line2.setAttribute("x1",95);
		line2.setAttribute("x2",95);
		line2.setAttribute("y1",58);
		line2.setAttribute("y2",this.altoCalendario+this.alto);
		line2.setAttribute("id","eje_2"+this.nombreGrafico);
		line2.setAttribute("class",'separaciones7 negro');
		
		
		//DIBUJO DE LA LÍNEA VERTICAL QUE SEPARA EL NOMBRE DEL GRAFICO CUANDO LOS NOMBRES SON MUY EXTENSOS
		if(!document.getElementById("eje_2w"+this.nombreGrafico)){
			var line2w=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var line2w=document.getElementById("eje_2w"+this.nombreGrafico);
		}	
		var line2w=document.createElementNS("http://www.w3.org/2000/svg", "line");
		line2w.setAttribute("x1",93);
		line2w.setAttribute("x2",93);
		line2w.setAttribute("y1",58);
		line2w.setAttribute("y2",this.altoCalendario+this.alto);
		line2w.setAttribute("id","eje_2w"+this.nombreGrafico);
		line2w.setAttribute("style",'stroke:#FFFFFF; stroke-width:6px');
		document.getElementById(this.contenedor).appendChild(line);
		document.getElementById(this.contenedor).appendChild(line2);
		
		document.getElementById(this.contenedor).appendChild(line);
		document.getElementById(this.contenedor).appendChild(line2w);
		document.getElementById(this.contenedor).appendChild(line2);
	
	}

	/*FUNCIÓN QUE DIBUJA LOS VALORES Y LÍNEAS DEL EJE Y
	 * para este caso es necesario sacar relaciones del tamaño usado por el area a gráficar
	 */
	this.creaEjeY = function(escala2, tiene_consolidado){
		
		var espacio=(this.alto)/(this.resources.length);
		var altura=0;
		var grupo;
		var zonaHoraria;
		var tab=0;
		var dirTriangulo=1;
		var myVar;
		//zonaHoraria=this.creaBarraZonaHoraria(escala2);
		//DIBUJA EL TEXTO Y LAS LÍNEAS QUE DIVIDEN EL EJE Y
	    for(i=0;i<this.resources.length;i++){  
	    	if(this.tipo == 'simple'){
	    		i= this.resources.length-1;
				separacion = 13;
	    	}
	    	else{
				separacion = this.alto+this.altoCalendario-((espacio*this.resources.length))+((this.resources.length-i-1)*espacio)+13;
				if((this.resources.length-i-1 == 0)&&(tiene_consolidado == true)){//es el consolidado del objetivo
					separacion =this.alto+this.altoCalendario-((20*this.resources.length))+((this.resources.length-i-1)*20)+5;
				
				}
				else{
					separacion = this.alto+this.altoCalendario-((20*this.resources.length))+((this.resources.length-i-1)*20)+8;
				}
	    	}
	    	
	        altura=this.alto+this.altoCalendario-((espacio*i));        
	        myVar = this.resources[this.resources.length-i-1][0].split('-');
	        tab=myVar.length-1;
	        //crea las barras que indican la disponibilidad
	        		
	        if(!document.getElementById("texto_"+this.resources[this.resources.length-i-1][0]+this.nombreGrafico)) {	        	
	    		if(!document.getElementById("fondo_"+i+this.nombreGrafico)) {
	    			var rect=document.createElementNS("http://www.w3.org/2000/svg", "rect");
	    		}
	    		else{
	    			var rect=document.getElementById("fondo_"+i+this.nombreGrafico);
	    		}
	    		rect.setAttribute("x",5);
	    		rect.setAttribute("width",89);
	    		rect.setAttribute("y",separacion-12);
	    		rect.setAttribute("height",18);
	    		rect.setAttribute("id","fondo_"+i+this.nombreGrafico);
	    		if((i+1)%2==0){
	    			rect.setAttribute("style",'fill:rgb(243,241,238)');	
	    		}
	    		else
	    			{
	    			rect.setAttribute("style",'fill:rgb(255,255,255)');
	    		}
	    		
				document.getElementById(this.contenedor).appendChild(rect);
	        	
		    	var texto=document.createElementNS("http://www.w3.org/2000/svg", "text");    	
				var nodoTexto = document.createTextNode(this.resources[this.resources.length-i-1][1][0].substr(0,17));
				
				texto.setAttribute("x",5);							
				texto.setAttribute("y", separacion);
				texto.setAttribute("id","texto_"+this.resources[this.resources.length-i-1][0]+this.nombreGrafico);
				alt=this.alto+this.altoCalendario-((espacio*this.resources.length))+10;
				if(i+1==this.resources.length && (this.tipo == "consolidada" || this.tipo == "consolidada_monitor")){
					texto.setAttribute("class",'escala9b');	
				}
				else{
					texto.setAttribute("class",'escala9');
				}
				
				texto.setAttribute("width",'16');
				texto.appendChild(nodoTexto);
				document.getElementById(this.contenedor).appendChild(texto);
	    	}
	        
	       
			grupo=this.creaBarras(escala2,this.resources[this.resources.length-i-1][0],this.alto+this.altoCalendario-((espacio*this.resources.length)),(this.resources.length-i-1), tiene_consolidado);

			if(!document.getElementById("linea_"+i+this.nombreGrafico)){
				var line=document.createElementNS("http://www.w3.org/2000/svg", "line");
			}
			else{
				var line=document.getElementById("linea_"+i+this.nombreGrafico);
			}
			
			line.setAttribute("x1",95);
			line.setAttribute("x2",this.escala*this.ancho+96);
			line.setAttribute("y1",this.alto+this.altoCalendario-(espacio*i));
			line.setAttribute("y2",this.alto+this.altoCalendario-(espacio*i));
			line.setAttribute("id","linea_"+i+this.nombreGrafico);
			line.setAttribute("class",'separaciones4 plomo');
			document.getElementById(this.contenedor).appendChild(line);	
	        }	
	}
	
	/*FUNCIÓN QUE IMPRIME EL CALENDARIO JUNTO CON RECORRER LOS OBJETIVO-PASO-NODOS ACTIVANDO LA FUNCIÓN QUE CREA LAS BARRAS*/
	this.creaCalendario = function(escala2){
		var x1Semana=96;
		var x1SemanaZoom=0;
		var x2Semana;
		var x2SemanaZoom = 0;
		var x1Mes=96;
		var x1MesZoom=0;
		var x2Mes;
		var semanas=0;
		var meses=0;		
		var diasSemana=0;
		var fecha=this.fechaMenor;
		var espacio;
		var altoCeldaCalendario =17;
		var xAntigua = 0;
		var xAntiguaZoom = 0;
		var diasDibujable = Math.round(this.diasCalendario/30);
		var classremove='text-remove';
		if(diasDibujable == 0) {
			dasDibujable = 1;
		}
		
		// Remover todo elemento que contenga la clase text-remove
		$('.'+classremove).remove();
		
		if(escala2<1) {
			escala2=1;
		}
		
		//si se evalua menos de 1 día en el gráfico de disponibilidad global se toma como ancho el mismo espacio
		if(this.dias<1){
			espacio2=(this.ancho);		
		}
		else {
			espacio2=(this.ancho)/(this.dias);
		}
		
		//DIBUJO DE LA LÍNEA BASE
		if(!document.getElementById("linea_base"+this.nombreGrafico)){
			var line=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var line=document.getElementById("linea_base"+this.nombreGrafico);
		}
		
		line.setAttribute("x1",95);
		line.setAttribute("x2",(this.ancho* this.escala)+95);
		line.setAttribute("y1",53);
		line.setAttribute("y2",53);
		line.setAttribute("id","linea_base"+this.nombreGrafico);
		line.setAttribute("style","stroke:rgb(204,180,139)");
		
		//DIBUJO DE LA LÍNEA DIAS /SEMANAS
		if(!document.getElementById("linea_dias_semana"+this.nombreGrafico)){
			var lineds=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var lineds=document.getElementById("linea_dias_semana"+this.nombreGrafico);
		}
		
		lineds.setAttribute("x1",95);
		lineds.setAttribute("x2",(this.ancho* this.escala)+95);
		lineds.setAttribute("y1",37);
		lineds.setAttribute("y2",37);
		lineds.setAttribute("id","linea_dias_semana"+this.nombreGrafico);
		lineds.setAttribute("style","stroke:rgb(204,180,139)");
		
		//DIBUJO DE LA LÍNEA DIAS /SEMANAS
		if(!document.getElementById("linea_semana_mes"+this.nombreGrafico)){
			var linesm=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var linesm=document.getElementById("linea_semana_mes"+this.nombreGrafico);
		}
		
		linesm.setAttribute("x1",95);
		linesm.setAttribute("x2",(this.ancho* this.escala)+95);
		linesm.setAttribute("y1",20);
		linesm.setAttribute("y2",20);
		linesm.setAttribute("id","linea_semana_mes"+this.nombreGrafico);
		linesm.setAttribute("style","stroke:rgb(204,180,139)");	
	
		//DIBUJO DE LA LÍNEA INICIO VERTICAL
		if(!document.getElementById("vert_calendario"+this.nombreGrafico)){
			var linev=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var linev=document.getElementById("vert_calendario"+this.nombreGrafico);
		}
		
		linev.setAttribute("x1",95);
		linev.setAttribute("x2",95);
		linev.setAttribute("y1",2);
		linev.setAttribute("y2",53);
		linev.setAttribute("id","vert_calendario"+this.nombreGrafico);
		linev.setAttribute("style","stroke:rgb(204,180,139)");	
		
		//DIBUJO DE LA LÍNEA INICIO VERTICAL
		if(!document.getElementById("vert_calendario2"+this.nombreGrafico)){
			var linev2=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var linev2=document.getElementById("vert_calendario2"+this.nombreGrafico);
		}
		
		linev2.setAttribute("x1",96+(this.ancho* this.escala));
		linev2.setAttribute("x2",96+(this.ancho* this.escala));
		linev2.setAttribute("y1",2);
		linev2.setAttribute("y2",53);
		linev2.setAttribute("id","vert_calendario2"+this.nombreGrafico);
		linev2.setAttribute("style","stroke:rgb(204,180,139)");
		
		//DIBUJO DE LA LÍNEA INICIO HORIZONTAL
		if(!document.getElementById("hor_calendario"+this.nombreGrafico)){
			var lineh=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var lineh=document.getElementById("hor_calendario"+this.nombreGrafico);
		}
		
		lineh.setAttribute("x1",95);
		lineh.setAttribute("x2",95+(this.ancho* this.escala));
		lineh.setAttribute("y1",2);
		lineh.setAttribute("y2",2);
		lineh.setAttribute("id","hor_calendario"+this.nombreGrafico);
		lineh.setAttribute("style","stroke:rgb(204,180,139)");	
		
		if(!document.getElementById("fondo_calendario"+this.nombreGrafico)){
			var rfondo=document.createElementNS("http://www.w3.org/2000/svg", "rect");
		}
		else{
			var rfondo=document.getElementById("fondo_calendario"+this.nombreGrafico);
		}
		
		rfondo.setAttribute("x",95);
		rfondo.setAttribute("height",50);		
		rfondo.setAttribute("width",(this.ancho* this.escala)+'px');
		rfondo.setAttribute("y",2);
		rfondo.setAttribute("id","fondo_calendario"+this.nombreGrafico);
		rfondo.setAttribute("focusable","true");
		rfondo.setAttribute("style",'fill:rgb(240, 237, 232);	opacity:0.9;cursor:pointer');		
		
		document.getElementById(this.contenedor).appendChild(rfondo);
	
		//recorre los dias que dibujará el calendario (si es gráfico global pondrá un día más por que toma periodos diferentes al 00:00:00)
		for(i=0;i<this.diasCalendario;i++){
			diasSemana++;			
			if(i>0){
				fete= fecha;
				var fecha = new Date(fecha.getUTCFullYear(),fecha.getUTCMonth(),fecha.getUTCDate()+1);
				fecha=fecha;
			}
	
			if(i<=0){
				var fecha = this.fechaMenor;
				esc=1;
				var dia = fecha.getUTCDate();
				var mes = fecha.getUTCMonth();
				var anho = fecha.getUTCFullYear();
				var vali = 1;
				if(mes < 10)
					mes = '0'+mes;
				if(dia < 10)
					dia = '0'+dia;
				var fecha2 = this.formateaFecha2(String(anho)+'.'+String(mes)+'.'+String(dia)+'.'+'00.00.00');
	
				fecha2= new Date(fecha2.getUTCFullYear(), fecha2.getUTCMonth()+1, fecha2.getUTCDate());
				
				if(this.diferenciaEntreFechas(this.fechaMenor, this.fechaMayor)<86400){
					espacio = espacio2 * ((fecha2-fecha)/1000)/((this.fechaMayor.valueOf()-this.fechaMenor.valueOf())/1000);
				}
				else{
					espacio = espacio2 * (this.diferenciaEntreFechas(fecha,fecha2))/86400;
				}
				xAntigua = 96+espacio*i*esc;			
				xAntiguaZoom = -(espacio*escala2)+96;
			}		
			else{
				espacio = espacio2;
				esc=escala2;
			}
			//solamente es valido para el global, en el resto calcula el final con el periodo 00:00:00
			
			if(this.tipo =="global"){
				
				dia = fecha.getUTCDate();
				mes = fecha.getUTCMonth();
				anho = fecha.getUTCFullYear();
				if(mes < 10)
					mes = '0'+mes;
				if(dia < 10)
					dia = '0'+dia;
//				fecha2 = this.formateaFecha2(anho+'.'+mes+'.'+dia);
				fecha2= new Date(fecha.getUTCFullYear(), fecha.getUTCMonth(), fecha.getUTCDate()+1);
				if(fecha2.getUTCDate()<10)
					dia2='0'+fecha2.getUTCDate();
				else
					dia2=fecha2.getUTCDate();
				
				if(fecha2.getUTCMonth()+1<10)
					mes2='0'+(parseInt(fecha2.getUTCMonth())+1);
				else
					mes2=parseInt(fecha2.getUTCMonth())+1;
				
			
				fecha2=new Date(this.parseISO8601ToTimestamp(fecha2.getUTCFullYear()+"-"+mes2+"-"+dia2+"T00:00:00Z"));

				if(this.diasCalendario >= 1 ){
					if(this.diferenciaEntreFechas(this.fechaMenor, this.fechaMayor)<86400){
						if(fecha2 > this.fechaMayor){
							fecha2 = this.fechaMayor;
						}						
						asd1 = (this.diferenciaEntreFechas(fecha,fecha2));
						asd2 = (this.diferenciaEntreFechas(this.fechaMenor, this.fechaMayor));
						if(i>0){
							espacio = espacio2-espacioTemp;
						}	
						else{
							espacio = espacio2 * (this.diferenciaEntreFechas(fecha,fecha2))/(this.diferenciaEntreFechas(this.fechaMenor, this.fechaMayor));
							espacioTemp=espacio;
						}
						
					}
					else{
						if(i==0){
							asd=this.diferenciaEntreFechas(this.fechaMenor,fecha2);
							espacio = espacio2 * ((this.diferenciaEntreFechas(this.fechaMenor,fecha2)))/86400;
							xAntigua=96;
						}else{
							if(i+1==this.diasCalendario){
								espacio = espacio2 * (this.diferenciaEntreFechas( fecha2,this.fechaMayor)+86400)/86400;
								
								
							}
							else{
								espacio = espacio2;
							}
						}
					}
				}
				else{
					espacio = espacio2
				}
			}
			else{			
				espacio = espacio2
				
			}
			var vall=xAntigua+((espacio*escala2)/24);
			//horas del dia
			if(escala2>=7){
				var espaciohora = espacio2 * ((fecha2-fecha)/1000)/((this.fechaMayor.valueOf()-this.fechaMenor.valueOf())/1000);
				
				for(e=0;e<24;e++){
					if(!document.getElementById('text_hora_'+e+'_'+parseInt(parseInt(fecha.getUTCDate())+1)+this.nombreGrafico)){
						var texto=document.createElementNS("http://www.w3.org/2000/svg", "text");
						var nodoTexto = document.createTextNode(e);			
						texto.appendChild(nodoTexto);
						var rect=document.createElementNS("http://www.w3.org/2000/svg", "rect");
						
					}
					else{
						var texto=document.getElementById('text_hora_'+e+'_'+parseInt(parseInt(fecha.getUTCDate())+1)+this.nombreGrafico);
						var rect=document.getElementById('hora_'+e+'_'+parseInt(parseInt(fecha.getUTCDate())+1)+this.nombreGrafico);			
					}
					
					//completa los datos de la hora
					texto.setAttribute("y",50);		
					texto.setAttribute("id",'text_hora_'+e+'_'+parseInt(parseInt(fecha.getUTCDate())+1)+this.nombreGrafico);
					texto.setAttribute("class",'escala9 zoom ' + classremove);	
					texto.setAttribute("data-fecha","hora_"+e+'_'+parseInt(parseInt(fecha.getUTCDate())+1)+'_'+fecha.getUTCFullYear());
					texto.setAttribute("data-dia",this.dias);
					
					rect.setAttribute("x",vall);
					rect.setAttribute("class",'zoom ' + classremove);	
					rect.setAttribute("data-fecha","hora_"+e+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear());
					rect.setAttribute("data-dia",this.dias);
					
					rect.setAttribute("height",altoCeldaCalendario);		
					rect.setAttribute("width",espacio*escala2/24);
					rect.setAttribute("y",50);
					rect.setAttribute("focusable","true");
					rect.setAttribute("style",'fill:rgb(245, 237, 232);	opacity:0;cursor:pointer');							
					rect.setAttribute("id",'hora_'+e+'_'+parseInt(parseInt(fecha.getUTCDate())+1)+this.nombreGrafico);
					//mueve 4 pixeles los numeros dibujados que tengan más de 1 caracter
					var tex;
					if(parseInt(fecha.getUTCDate()) > 9){
						tex=vall-16;
					}
					else{
						tex=vall-13;
					}
					texto.setAttribute("x",tex);
				
								
					//DIBUJO DE LA LÍNEA QUE SEPARA LA HORA
					if(!document.getElementById("linea_hora"+e+'_'+parseInt(parseInt(fecha.getUTCDate())+1)+this.nombreGrafico)){
						var lineh=document.createElementNS("http://www.w3.org/2000/svg", "line");
					}
					else{
						var lineh=document.getElementById("linea_hora"+e+'_'+parseInt(parseInt(fecha.getUTCDate())+1)+this.nombreGrafico);
					}
				
					lineh.setAttribute("x1",vall);
					lineh.setAttribute("x2",vall);
					lineh.setAttribute("y1",36);
					lineh.setAttribute("y2",54);
					lineh.setAttribute("id","linea_hora"+e+'_'+parseInt(parseInt(fecha.getUTCDate())+1)+this.nombreGrafico);
					lineh.setAttribute("class",'line_hora ' + classremove);
					lineh.setAttribute("style","stroke:rgb(204,180,139)");
					
					vall+=(espacio*escala2)/24;
					
					document.getElementById(this.contenedor).appendChild(texto);
					document.getElementById(this.contenedor).appendChild(rect);		
					document.getElementById(this.contenedor).appendChild(lineh);
					
				}
				
			
			}
			
			//dibuja los días si no han sido creados
			if(!document.getElementById('text_dia_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico)){
				var texto=document.createElementNS("http://www.w3.org/2000/svg", "text");
				var nodoTexto = document.createTextNode(fecha.getUTCDate());			
				texto.appendChild(nodoTexto);
				var rect=document.createElementNS("http://www.w3.org/2000/svg", "rect");			
			}
			else{
				if(escala2>=7){
					$('#text_dia_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico).remove();
					var texto=document.createElementNS("http://www.w3.org/2000/svg", "text");
					var nodoTexto = document.createTextNode(fecha.getUTCDate());			
					texto.appendChild(nodoTexto);
					
				}else{
				var texto=document.getElementById('text_dia_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				var rect=document.getElementById('dia_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				}
			}
			
			//completa los datos del día
			if(escala2<7){
				texto.setAttribute("y",47);
				texto.setAttribute("data-dia",this.dias);
			}else{
				texto.setAttribute("y",31);
				texto.setAttribute("data-dia",1);
			}
			texto.setAttribute("id",'text_dia_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
			texto.setAttribute("class",'escala9 zoom');		
			texto.setAttribute("data-fecha","dia_"+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear());
//			texto.setAttribute("data-equis",parseFloat((xAntigua)+(espacio*escala2)-96));
			
			if(i==0){
				rect.setAttribute("data-equis",0);
				texto.setAttribute("data-equis",0);
			}
			else{
				rect.setAttribute("data-equis",(xAntigua-96));
				texto.setAttribute("data-equis",(xAntigua-96));
			}
			
			rect.setAttribute("x",xAntigua);
			rect.setAttribute("class",'zoom');
			rect.setAttribute("data-fecha","dia_"+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear());
			rect.setAttribute("data-dia",this.dias);
	//		rect.setAttribute("data-equis",parseFloat((xAntigua)));
			
			rect.setAttribute("height",altoCeldaCalendario);		
			rect.setAttribute("width",espacio*escala2);
			rect.setAttribute("y",36);
			rect.setAttribute("focusable","true");
			rect.setAttribute("style",'fill:rgb(240, 237, 232);	opacity:0;cursor:pointer');							
			rect.setAttribute("id",'dia_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
//----		rect.setAttribute("onclick",this.nombreObjeto+".zoom("+this.dias+",'dia_"+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear()+"',"+parseFloat((xAntiguaZoom)+(espacio*escala2)-96)+")");
	
			//mueve 4 pixeles los numeros dibujados que tengan más de 1 caracter
			if(parseInt(fecha.getUTCDate()) > 9){
				xval = xAntigua+(espacio*escala2)/2-4;
			}
			else{
				xval = xAntigua+(espacio*escala2)/2;
			}
			
			if(diasDibujable%2==0){
				
				if((i)%diasDibujable !=0 && escala2 ==1 && diasDibujable >1 && i !=0 ){				
					texto.setAttribute("x",xval + espacio/2);
					texto.setAttribute("display","none");
				}
				else{
					texto.setAttribute("display","");				
					if(escala2 == 1 && diasDibujable >1 && i>0){
						texto.setAttribute("x",xval +(espacio*(diasDibujable-1))/2);
						if((xval +(espacio*(diasDibujable-1))/2)>((this.ancho* this.escala)+90))
							texto.setAttribute("display","none");
					}
					else{
						texto.setAttribute("x",xval);
						if(xval-5>((this.ancho* this.escala)+90))
								texto.setAttribute("display","none");	
					}								
				}
				
			}
			else{
				if((i+1)%diasDibujable !=0 && escala2 ==1 && diasDibujable >1 && i !=0){
					texto.setAttribute("x",xval + espacio/2);
					texto.setAttribute("display","none");
				}
				else{
					texto.setAttribute("display","");				
					if(escala2 == 1 && diasDibujable >1 && i>0){
						texto.setAttribute("x",xval +(espacio*(diasDibujable-1))/2);
						if((xval +(espacio*(diasDibujable-1))/2)>((this.ancho* this.escala)+90)){
							texto.setAttribute("display","none");						
						}
					}
					else{
						texto.setAttribute("x",xval);
						if(xval-5>((this.ancho* this.escala)+90)){
							texto.setAttribute("display","none");
						}
					}				
				}
			}
			if(escala2>=7){
				
				//DIBUJO DE LA LÍNEA QUE SEPARA EL DIA
				if(!document.getElementById("linea_dia"+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico)){
					var lined=document.createElementNS("http://www.w3.org/2000/svg", "line");
				}
				else{
					$("#linea_dia"+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico).remove();
					var lined=document.createElementNS("http://www.w3.org/2000/svg", "line");
				}
				
				lined.setAttribute("x1",xAntigua+(espacio*escala2));
				lined.setAttribute("x2",xAntigua+(espacio*escala2));
				lined.setAttribute("y1",20);
				lined.setAttribute("y2",37);
				lined.setAttribute("id","linea_dia"+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				lined.setAttribute("class",'line_dia ' + classremove);
				lined.setAttribute("style","stroke:rgb(204,180,139)");	
				document.getElementById(this.contenedor).appendChild(lined);
			}else{
			//DIBUJO DE LA LÍNEA QUE SEPARA EL DIA
			if(!document.getElementById("linea_dia"+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico)){
				var lined=document.createElementNS("http://www.w3.org/2000/svg", "line");
			}
			else{
				var lined=document.getElementById("linea_dia"+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
			}
			lined.setAttribute("x1",xAntigua+(espacio*escala2));
			lined.setAttribute("x2",xAntigua+(espacio*escala2));
			lined.setAttribute("y1",37);
			lined.setAttribute("y2",53);
			lined.setAttribute("id","linea_dia"+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
			
			lined.setAttribute("style","stroke:rgb(204,180,139)");						
			document.getElementById(this.contenedor).appendChild(lined);
			}
			document.getElementById(this.contenedor).appendChild(rect);		
			
			document.getElementById(this.contenedor).appendChild(texto);
			
			//actualiza el valor de X que tiene antes de hacer el zoom para una ubicación más facil
			xAntiguaZoom = xAntigua;
			//actualiza el valor en que comenzará a dibujarse el nuevo día
		//	if(diasDibujable>2)
			xAntigua = xAntigua+(espacio*escala2);
	
			x2Semana = xAntigua -x1Semana;
			x2SemanaZoom=(espacio*i*esc+espacio*escala2)-x1SemanaZoom;
			x2Mes=(x1Semana+x2Semana)-x1Mes;
			
			x2MesZoom=(x1SemanaZoom+x2SemanaZoom)-x1MesZoom;
			numCar = x2Semana/8;
			
			if(fecha.getUTCDay()==0) {//termina la semana
				ultimaSemana='semana_'+this.inicioSemana(fecha,false);
				if(!document.getElementById('text_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico)){
					var texto=document.createElementNS("http://www.w3.org/2000/svg", "text");			
					texto.setAttribute("id",'text_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
					var rect=document.createElementNS("http://www.w3.org/2000/svg", "rect");
					rect.setAttribute("id",'semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
					var clip=document.createElementNS("http://www.w3.org/2000/svg", "clipPath");
					clip.setAttribute("id",'clip_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
					var rect2=document.createElementNS("http://www.w3.org/2000/svg", "rect");				
					rect2.setAttribute("id",'rect2_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				}
				else{
					if(escala2>=7){
						$('#text_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico).remove();
						var texto=document.createElementNS("http://www.w3.org/2000/svg", "text");
						texto.setAttribute("id",'text_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
					}else{
					var texto=document.getElementById('text_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);}
					var rect=document.getElementById('semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
					var rect2=document.getElementById('rect2_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
					var clip=document.getElementById('clip_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				}
				
			    while ( texto.childNodes.length >= 1 )
			    {
			    	 texto.removeChild( texto.firstChild );       
			    }				
				mensaje = 'Semana del '+this.inicioSemana(fecha,false);
				var nodoTexto = document.createTextNode(mensaje.substr(0,numCar));					
				texto.appendChild(nodoTexto);																		
				texto.setAttribute("x",x1Semana+10);
				rect.setAttribute("x",x1Semana);	
				if(escala2>=7){
					texto.setAttribute("y",15);
				}else{
					texto.setAttribute("y",31);
				}
				texto.setAttribute("class",'escala9 zoom 3');	
	//			rect.setAttribute("class",'zoom');
				texto.setAttribute("data-fecha","semana_"+this.inicioSemana(fecha,false)+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear());
				texto.setAttribute("data-dia",this.dias/diasSemana);
				texto.setAttribute("data-equis",(x1Semana));
				
				rect.setAttribute("class",'escala9');	
				rect.setAttribute("class",'zoom');
				rect.setAttribute("data-fecha","semana_"+this.inicioSemana(fecha,false)+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear());
				rect.setAttribute("data-dia",this.dias/diasSemana);
				rect.setAttribute("data-equis",(x1Semana));
				
	//			texto.setAttribute("onclick","zoom("+this.dias/diasSemana+",'semana_"+this.inicioSemana(fecha,false)+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear()+"',"+(x1SemanaZoom)+")");			
	
				rect.setAttribute("height",altoCeldaCalendario);
				rect.setAttribute("width",x2Semana);
				rect.setAttribute("y",20);					
	//			rect.setAttribute("onclick","zoom("+this.dias/diasSemana+",'semana_"+this.inicioSemana(fecha,false)+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear()+"',"+(x1SemanaZoom)+")");			
				rect.setAttribute("style",'fill:rgb(240, 237, 232);	opacity:0;cursor:pointer');
				diasSemana=0;
				rect2.setAttribute("height",altoCeldaCalendario);
				rect2.setAttribute("width",x2Semana);
				rect2.setAttribute("x",x1Semana);
				rect2.setAttribute("y",20);						
				clip.appendChild(rect2);
				document.getElementById(this.contenedor).appendChild(rect);
				document.getElementById(this.contenedor).appendChild(clip);
				document.getElementById(this.contenedor).appendChild(texto);

				//DIBUJO DE LA LÍNEA QUE SEPARA LAS SEMANAS
				if(!document.getElementById('linea_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico)){
					var lines=document.createElementNS("http://www.w3.org/2000/svg", "line");
				}
				else{
					var lines=document.getElementById('linea_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				}
				lines.setAttribute("x1",x1Semana+(x2Semana));
				lines.setAttribute("x2",x1Semana+(x2Semana));
				lines.setAttribute("y1",20);
				lines.setAttribute("y2",37);
				lines.setAttribute("id",'linea_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				lines.setAttribute("style","stroke:rgb(204,180,139)");
				document.getElementById(this.contenedor).appendChild(lines);
				
				
				
				x1Semana=x1Semana+x2Semana;
				x1SemanaZoom=x1SemanaZoom+x2SemanaZoom;
				semanas++;
			}
			
			numCarMes = x2Mes/8;
			if(this.finMes(fecha)){//si al fecha es fin de mes cierra el rectangulo y prepara la creación de uno nuevo
				this.mesFinalizado[parseInt(parseInt(fecha.getUTCMonth())+1)]=1;
				if(!document.getElementById('text_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico)){
					var texto=document.createElementNS("http://www.w3.org/2000/svg", "text");
					texto.setAttribute("id",'text_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
					var rect=document.createElementNS("http://www.w3.org/2000/svg", "rect");
					var clip=document.createElementNS("http://www.w3.org/2000/svg", "clipPath");	
					var rect2=document.createElementNS("http://www.w3.org/2000/svg", "rect");
					clip.setAttribute("id",'clip_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
					rect.setAttribute("id",'mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
					rect2.setAttribute("id",'rect2_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				}
				else{
					var texto=document.getElementById('text_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
					var rect =document.getElementById('mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
					var rect2 =document.getElementById('rect2_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
					var clip=document.getElementById('clip_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
					
					
				}				
							
				if((x1Mes-90+(x1Mes+x2Mes)/2)>x1Mes){
					texto.setAttribute("x",x1Mes-40+(x2Mes)/2);
				}
				else{
					texto.setAttribute("x",x1Mes+6);
				}		
			
			    while ( texto.childNodes.length >= 1 )
			    {
			    	 texto.removeChild( texto.firstChild );       
			    }				
				mensajeMes = this.month[fecha.getUTCMonth()]+' '+fecha.getUTCFullYear();					
				var nodoTexto2 = document.createTextNode(mensajeMes.substr(0,numCarMes));					
				texto.appendChild(nodoTexto2);				
				
				texto.setAttribute("y",15);
							
	//			texto.setAttribute("onclick","zoom('1','mes_"+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear()+"',"+x1MesZoom+")");
				texto.setAttribute("class",'escala9 zoom');						
				//texto.setAttribute("class",'zoom');
				texto.setAttribute("data-dia",1);
				texto.setAttribute("data-fecha",'mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear());
				texto.setAttribute("data-equis",x1MesZoom);
				
				rect.setAttribute("class",'escala9');						
				rect.setAttribute("class",'zoom');
				rect.setAttribute("data-dia",1);
				rect.setAttribute("data-fecha",'mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear());
				rect.setAttribute("data-equis",x1MesZoom);
				
				
				rect.setAttribute("x",x1Mes);
				rect.setAttribute("height",altoCeldaCalendario);
				rect.setAttribute("width",x2Mes);
				rect.setAttribute("y",3);
		
				
				rect.setAttribute("style",'fill:rgb(240, 237, 232);	opacity:0;cursor:pointer');
				rect2.setAttribute("x",x1Mes);
				rect2.setAttribute("height",altoCeldaCalendario);
				rect2.setAttribute("width",x2Mes);
				rect2.setAttribute("y",3);			
				
				clip.appendChild(rect2);
				document.getElementById(this.contenedor).appendChild(rect);
				document.getElementById(this.contenedor).appendChild(clip);
				document.getElementById(this.contenedor).appendChild(texto);
				
				//DIBUJO DE LA LÍNEA QUE SEPARA LOS MESES
				if(!document.getElementById('linea_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico)){
					var linem=document.createElementNS("http://www.w3.org/2000/svg", "line");
				}
				else{
					var linem=document.getElementById('linea_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				}
				linem.setAttribute("x1",x1Mes+(x2Mes));
				linem.setAttribute("x2",x1Mes+(x2Mes));
				linem.setAttribute("y1",3);
				linem.setAttribute("y2",20);
				linem.setAttribute("id",'linea_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				linem.setAttribute("style","stroke:rgb(204,180,139)");
				document.getElementById(this.contenedor).appendChild(linem);
				
				meses++;	
				x1Mes=x1Mes+x2Mes;
				x1MesZoom=x1MesZoom+x2MesZoom;
				}	
			}
		numCar = x2Semana/8;

		//IMPRIME EL CUADRO DE LA SEMANA FINAL
		if(fecha.getUTCDay()!=0 ){
			if(!document.getElementById('text_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico)){
				var texto=document.createElementNS("http://www.w3.org/2000/svg", "text");
				texto.setAttribute("id",'text_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				var rect=document.createElementNS("http://www.w3.org/2000/svg", "rect");
				rect.setAttribute("id",'semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				var clip=document.createElementNS("http://www.w3.org/2000/svg", "clipPath");
				clip.setAttribute("id",'clip_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				var rect2=document.createElementNS("http://www.w3.org/2000/svg", "rect");				
				rect2.setAttribute("id",'rect2_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
			}
			else{
				
				var rect=document.getElementById('semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				var rect2=document.getElementById('rect2_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				var texto=document.getElementById('text_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				var clip=document.getElementById('clip_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
			}
		
		    while ( texto.childNodes.length >= 1 )
		    {
		    	 texto.removeChild( texto.firstChild );       
		    }				
		    
			mensaje = 'Semana del '+this.inicioSemana(fecha,true);
			var nodoTexto = document.createTextNode(mensaje.substr(0,numCar));					
			texto.appendChild(nodoTexto);																		

			
			texto.setAttribute("x",x1Semana+10);
			rect.setAttribute("x",x1Semana);				
			if(escala2>=7){
				texto.setAttribute("y",15)
			}else{
			texto.setAttribute("y",31);}							
			texto.setAttribute("class",'escala9 zoom');
			texto.setAttribute("data-dia",this.dias/diasSemana);
			texto.setAttribute("data-fecha",'semana_'+this.inicioSemana(fecha,true)+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear());
			texto.setAttribute("data-equis",x1Semana);
//			texto.setAttribute("onclick","zoom("+this.dias/diasSemana+",'semana_"+this.inicioSemana(fecha,true)+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear()+"',"+x1SemanaZoom+")");
			
			rect.setAttribute("class",'zoom');
			rect.setAttribute("data-dia",this.dias/diasSemana);
			rect.setAttribute("data-fecha",'semana_'+this.inicioSemana(fecha,true)+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear());
			rect.setAttribute("data-equis",x1Semana);
//			texto.setAttribute("class",'zoom');

			rect.setAttribute("height",altoCeldaCalendario);
			rect.setAttribute("width",x2Semana);
			rect.setAttribute("y",20);
//			rect.setAttribute("onclick","zoom("+this.dias/diasSemana+",'semana_"+this.inicioSemana(fecha,true)+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear()+"',"+x1SemanaZoom+")");
			
			rect.setAttribute("style",'fill:rgb(240, 237, 232);	opacity:0;cursor:pointer');
			diasSemana=0;
			rect2.setAttribute("height",altoCeldaCalendario);
			rect2.setAttribute("width",x2Semana);
			rect2.setAttribute("x",x1Semana);
			rect2.setAttribute("y",20);						
			clip.appendChild(rect2);
			document.getElementById(this.contenedor).appendChild(clip);
			document.getElementById(this.contenedor).appendChild(rect);
			document.getElementById(this.contenedor).appendChild(texto);

			//DIBUJO DE LA LÍNEA QUE SEPARA LAS SEMANAS
			if(!document.getElementById('linea_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico)){
				var lines=document.createElementNS("http://www.w3.org/2000/svg", "line");
			}
			else{
				var lines=document.getElementById('linea_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
			}
			
			lines.setAttribute("x1",x1Semana+(x2Semana));
			lines.setAttribute("x2",x1Semana+(x2Semana));
			lines.setAttribute("y1",20);
			lines.setAttribute("y2",37);
			lines.setAttribute("id",'linea_semana_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
			lines.setAttribute("style","stroke:rgb(204,180,139)");
			document.getElementById(this.contenedor).appendChild(lines);
			
			x1Semana=x2Semana;
			x1SemanaZoom=x2SemanaZoom;
		}
		
		//IMPRIME EL CUADRO DEL MES FINAL
		numCarMes = x2Mes/8;
		if(this.mesFinalizado[parseInt(parseInt(fecha.getUTCMonth())+1)]!=1){
			if(!document.getElementById('text_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico)){
				var texto=document.createElementNS("http://www.w3.org/2000/svg", "text");
				texto.setAttribute("id",'text_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				var rect=document.createElementNS("http://www.w3.org/2000/svg", "rect");
				var clip=document.createElementNS("http://www.w3.org/2000/svg", "clipPath");	
				var rect2=document.createElementNS("http://www.w3.org/2000/svg", "rect");
				clip.setAttribute("id",'clip_mes_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				rect.setAttribute("id",'mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				rect2.setAttribute("id",'rect2_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
			}
			else{
				var texto=document.getElementById('text_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				var rect =document.getElementById('mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);			
				var rect2 =document.getElementById('rect2_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
				var clip=document.getElementById('clip_mes_'+fecha.getUTCDate()+'_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
			}
		
			
		    while ( texto.childNodes.length >= 1 )
		    {
		    	 texto.removeChild( texto.firstChild );       
		    }				
			mensajeMes = this.month[fecha.getUTCMonth()]+' '+fecha.getUTCFullYear();					
			var nodoTexto2 = document.createTextNode(mensajeMes.substr(0,numCarMes));					
			texto.appendChild(nodoTexto2);	
			
			rect.setAttribute("x",x1Mes);		
			texto.setAttribute("x",x1Mes+(x2Mes/2));					
			texto.setAttribute("y",15);
			texto.setAttribute("id",'text_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
			//texto.setAttribute("onclick","zoom('1','mes_"+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear()+"',"+x1MesZoom+")");	
			texto.setAttribute("class",'escala9 zoom');		
			//texto.setAttribute("class",'zoom');
			texto.setAttribute("data-dia",1);
			texto.setAttribute("data-fecha","mes_"+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear());
			texto.setAttribute("data-equis",x1MesZoom);
				
			rect.setAttribute("class",'zoom');
			rect.setAttribute("data-dia",1);
			rect.setAttribute("data-fecha",'mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear());
			rect.setAttribute("data-equis",x1MesZoom);
			
			rect.setAttribute("height",altoCeldaCalendario);
			rect.setAttribute("width",x2Mes);
			rect.setAttribute("y",3);		
			rect.setAttribute("id",'mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
//			rect.setAttribute("onclick","zoom('1','mes_"+parseInt(parseInt(fecha.getUTCMonth())+1)+'_'+fecha.getUTCFullYear()+"',"+x1MesZoom+")");			
			rect.setAttribute("style",'fill:rgb(240, 237, 232);	opacity:0 ;cursor:pointer');
			rect2.setAttribute("x",x1Mes);
			rect2.setAttribute("height",altoCeldaCalendario);
			rect2.setAttribute("width",x2Mes);
			rect2.setAttribute("y",3);			
			
			clip.appendChild(rect2);
			document.getElementById(this.contenedor).appendChild(rect);
			document.getElementById(this.contenedor).appendChild(clip);
			if(escala2<7){
			document.getElementById(this.contenedor).appendChild(texto);}
			//DIBUJO DE LA LÍNEA QUE SEPARA LOS MESES
			if(!document.getElementById('linea_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico)){
				var linem=document.createElementNS("http://www.w3.org/2000/svg", "line");
			}
			else{
				var linem=document.getElementById('linea_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
			}
			linem.setAttribute("x1",x1Mes+(x2Mes));
			linem.setAttribute("x2",x1Mes+(x2Mes));
			linem.setAttribute("y1",3);
			linem.setAttribute("y2",20);
			linem.setAttribute("id",'linea_mes_'+parseInt(parseInt(fecha.getUTCMonth())+1)+this.nombreGrafico);
			linem.setAttribute("style","stroke:rgb(204,180,139)");
			document.getElementById(this.contenedor).appendChild(linem);
			
			x1Semana=x2Semana;	
			x1SemanaZoom=x2SemanaZoom;
		}
		document.getElementById(this.contenedor).appendChild(line);
		document.getElementById(this.contenedor).appendChild(lineds);
		document.getElementById(this.contenedor).appendChild(linesm);
		document.getElementById(this.contenedor).appendChild(linev);
		document.getElementById(this.contenedor).appendChild(linev2);
		document.getElementById(this.contenedor).appendChild(lineh);
	
	}
	
	/*FUNCIÓN QUE INDICA SI LA FECHA INGRESADA ES FIN DE MES O NO*/
	this.finMes = function(fechaTemp){
		var mes=fechaTemp.getUTCMonth();
		
		fechaTemp = new Date(fechaTemp.getUTCFullYear(),fechaTemp.getUTCMonth(),fechaTemp.getUTCDate()+1)
		
	//	fechaTemp=fechaTemp.valueOf();
	//	fechaTemp=fechaTemp+(24 * 60 * 60 * 1000);
	//	fechaTemp= new Date(fechaTemp);
		
		if(mes!=fechaTemp.getUTCMonth()){
			return true;
		}
		else{
			return false;
		}
	}
	
	/*FUNCIÓN QUE INDICA EL DÍA EN QUE COMIENZA LA SEMANA*/
	this.inicioSemana = function(fecha,fin){
		var fechaTemp=fecha.valueOf();
		var numero=fecha.getUTCDay();
		if(fin==true){
			numero=6-(6-parseInt(numero)+1);
		}
		else{
			numero=6;
		}
		
		fechaTemp = new Date(fecha.getUTCFullYear(),fecha.getUTCMonth(),fecha.getUTCDate()-numero);
		//fechaTemp=fecha-(numero*24 * 60 * 60 * 1000);
		
		fechaTemp= new Date(fechaTemp);
		
		return fechaTemp.getUTCDate();
	}
	
	/*FUNCIÓN QUE DIBUJA LAS FECHAS QUE INDICAN LOS EVENTOS EN EL GRÁFICO
	 * RECIBE COMO PARAMETRO LA ESCALA A DIBUJAR, EL ELEMENTO QUE INDICA A QUE NODO-PASO-OBJETIVO PERTENECE Y LA ALTURA EN QUE DEBE SER DIBUJADA
	 */
	this.creaBarras = function(escalaval,elemento,altura,indice, tiene_consolidado){
		var segundos;
		var i=0;
		var j=1;	
		var k=this.resources.length-1;
		if(escalaval<1)
			escalaval=1;
		segundos=(this.fechaMayor-this.fechaMenor)/1000;
		espacio=(this.ancho)/(segundos);	
		
		if((indice == 0)&&(tiene_consolidado == true)){
			grosor = 16;
			separacion = altura+20+(20*indice)-4;
		}
		else{
			grosor = 16;
			separacion = altura+20+(20*indice)-4;
		}
		if(this.tipo=='simple'){
			separacion=0;
			grosor = 16;
		}
		
		j=0;
		
		
		
		var grupo=document.createElementNS("http://www.w3.org/2000/svg", "g");
		grupo.setAttribute("id","grupo_"+elemento+this.nombreGrafico);
		grupo.setAttribute("y",0);
		document.getElementById(this.contenedor).appendChild(grupo);
		
		//var elem="'"+elemento+"'";		
		if(elemento.indexOf("-")!=-1){
			grupo.style.visibility="visible";
		}
		if(this.datos[elemento])
			
			nodo = elemento.split('-')[1];
			paso= elemento.split('-')[2];
			paginas = this.datos[elemento].length;		
		if(nodo===undefined){
			nodo="";
		}
		if(paso===undefined){
			paso="";
		}
		while(this.datos[elemento][j]){
				if(paginas%6 == 0)	{
					pagina = paginas/6;
				}
				else {
					pagina = parseInt(paginas/6)+1;
				}
				paginas--;
				
				//fecha_monitoreo=this.datos[elemento][j][0];
					diferencia=(this.formateaFecha2(this.datos[elemento][j][0])-this.fechaMenor)/1000;
						if(j<1){
							x1=diferencia*espacio+96;
						}
						else{
							x1=(escalaval*diferencia*espacio+96);							
							}
//						fecha1 = this.formateaFecha2(this.datos[elemento][j][1]);
//						fecha2 = this.formateaFecha2(this.datos[elemento][j][0]);
					diferencia=(this.formateaFecha2(this.datos[elemento][j][1])-this.formateaFecha2(this.datos[elemento][j][0]))/1000;
					x2=escalaval*diferencia*espacio;
					fecha_monitoreo=this.formateaFecha2(this.datos[elemento][j][0]);
					if(!document.getElementById('rect_'+elemento+'_'+j+'_'+this.nombreGrafico)){
						var rect=document.createElementNS("http://www.w3.org/2000/svg", "rect");
					}	
					else{
						rect=document.getElementById('rect_'+elemento+'_'+j+'_'+this.nombreGrafico);
						}
				
					rect.setAttribute("x",x1);				
					rect.setAttribute("id",'rect_'+elemento+'_'+j+'_'+this.nombreGrafico);
					rect.setAttribute("height",grosor);
					rect.setAttribute("pagina",pagina);
					rect.setAttribute("fecha_monitoreo",fecha_monitoreo);
					rect.setAttribute("nodo",nodo);
					rect.setAttribute("pasos",paso);

					if(x2>1){
						rect.setAttribute("width",x2+0.4);
					}
					else{
						rect.setAttribute("width",1);
					}
					rect.setAttribute("y",separacion);
					rect.setAttribute("class",'cuadro2 '+this.nombreGrafico);
					rect.setAttribute("style","fill:"+this.color[this.datos[elemento][j][2]]);
					document.getElementById('grupo_'+elemento+this.nombreGrafico).appendChild(rect);
					j++;
			}
		i++;
		
		return grupo;
	}
	
	/*FUNCIÓN QUE BUSCA LA MAYOR DE LAS FECHAS INGRESADAS*/
	this.buscaFechaMayor =function(){
		var i=0;
		var j=0;
		var fecha=this.formateaFecha2("2000.01.01.00.00.00");
		
		while(this.resources[i]){
			if(this.datos[this.resources[i][0]]){
			while(this.datos[this.resources[i][0]][j]){
				
				if(fecha<this.formateaFecha2(this.datos[this.resources[i][0]][j][1])){
					fecha=this.formateaFecha2(this.datos[this.resources[i][0]][j][1]);
					}
				j++;
			}}
			i++;	
		}

		return fecha;	
	}
	
	//funcion que dibuja la zona horaria
	this.creaBarraZonaHoraria = function(escalaval,tiene_consolidado,monitor_id){
		
		var segundos;
		var j=1;
		var k=this.resources.length-1;
		var indice=0;
		var altura=0;
		
		if(escalaval<1)
			escalaval=1;
		segundos=(this.fechaMayor-this.fechaMenor)/1000;
		espacio=(this.ancho)/(segundos);	
		
		for(var i = 0; i<this.horario_habil.length; i++){
		
			diferencia=(this.formateaFecha2(this.horario_habil[i][0])-this.fechaMenor)/1000;
			if(i<1){
				if(escalaval<7){
							x1=diferencia*espacio+96;
					}else{
								x1=(escalaval*diferencia*espacio+96);
					
					}
			}
			else{
			
				x1=(escalaval*diferencia*espacio+96);							
			}

			diferencia=(this.formateaFecha2(this.horario_habil[i][1])-this.formateaFecha2(this.horario_habil[i][0]))/1000;
			x2=escalaval*diferencia*espacio;
				
			fecha_monitoreo=this.formateaFecha2(this.horario_habil[i][0]);
				
			if(!document.getElementById('rect__'+i+'_'+j+'_'+monitor_id+'_zona'+this.nombreGrafico)){
				var con=document.createElementNS("http://www.w3.org/2000/svg", "rect");
			}	
			else{
				con=document.getElementById('rect__'+i+'_'+j+'_'+monitor_id+'_zona'+this.nombreGrafico);
			}
		
			con.setAttribute("x",x1);				
			con.setAttribute("id",'rect__'+i+'_'+j+'_'+monitor_id+'_zona'+this.nombreGrafico);
			con.setAttribute("height",this.alto+this.altoCalendario-54);
			con.setAttribute("fecha_monitoreo",fecha_monitoreo);
			
			if(x2>1){
				con.setAttribute("width",x2+0.4);
			}
			else{
				con.setAttribute("width",1);
			}
			
			con.setAttribute("y",this.altoCalendario+12);
			con.setAttribute("class",'cuadrozona '+this.nombreGrafico);
			con.setAttribute("style","fill:rgba(84,165,28,0.2)");
			if(!document.getElementById('rect__'+i+'_'+j+'_'+monitor_id+'_zona'+this.nombreGrafico)){
				document.getElementById(this.contenedor).appendChild(con);
			}
		j++;
		}
		return con;
	}
	
	
	//FUNCIÓN QUE BUSCA LA MENOR DE TODAS LAS FECHAS INGRESADAS
	this.buscaFechaMenor = function(){
		var i=0;
		var j=0;
		var fecha=this.formateaFecha2("2100.01.01.00.00.00");
	
		while(this.resources[i]){
			j=0;
			if(this.datos[this.resources[i][0]])
			while(this.datos[this.resources[i][0]][j]){
				if(fecha>this.formateaFecha2(this.datos[this.resources[i][0]][j][0])){
					fecha=this.formateaFecha2(this.datos[this.resources[i][0]][j][0]);
					}
				j++;
			}
			i++;	
		}
		return fecha;	
		}
	
	
	/*FUNCIÓN QUE TOMA LA HORA A LA QUE SE DEBE DIBUJAR EL PUNTO Y LA TRASFORMA A PIXELES*/
	this.transformaHoraSegundos = function(fechaInicio, fechaFin){
		var fechaI;
		var fechaF;
		fechaI=this.formateaFecha2(this.fechaInicio);
		fechaF=this.formateaFecha2(this.fechaFin);
		
		var dif=(fechaF-fechaI)/1000;	
		return parseInt(segundos);
	}
	
	
	
	
	//FUNCIÓN QUE TOMA UNA FECHA Y LA ENTREGA EN FORMATO DD-MM-YYYY
	this.formateaFecha = function(fecha){
		temp=fecha.split('.');
		fecha=temp[2]+'-'+temp[1]+'-'+temp[0];
		return fecha;	
	}
	
	
	//FUNCIÓN QUE TOMA UNA FECHA Y LA ENTREGA EN FORMATO DE LA CLASE DATE
	this.formateaFecha2 = function(fecha){	
	
		var hora=fecha.substring(11,19);
		hora=hora.split('.');
		
		var fecha=fecha.substring(0,13);
		fecha=this.formateaFecha(fecha);
		fecha2=fecha.split('-');
		fecha=fecha.split('-');
		
		fecha=new Date(this.parseISO8601ToTimestamp(fecha[2]+"-"+fecha[1]+"-"+fecha[0]+"T"+hora[0]+":"+hora[1]+":"+hora[2]+"Z"));
		return fecha;		
	}
	
	this.diferenciaEntreFechas = function(fecha1, fecha2){
	 
		diferencia = (fecha2 - fecha1)/1000;
		return diferencia;
	}
	
//FUNCIÓN QUE TRANSFORMA LAS FECHAS ISO8601 A TIMESTAMP, SE HACE POR QUE LOS SVG NO SOPORTAN ESE FORMATO DE FECHA	
	this.parseISO8601ToTimestamp = function(date) {
	       var timestamp, struct, minutesOffset = 0;
	       var numericKeys = [1, 4, 5, 6, 7, 10, 11];

	       // 1 YYYY 2 MM 3 DD 4 HH 5 mm 6 ss 7 msec 8 Z 9 ± 10 tzHH 11 tzmm
	       if((struct = /^(\d{4}|[+\-]\d{6})(?:-(\d{2})(?:-(\d{2}))?)?(?:T(\d{2}):(\d{2})(?::(\d{2})(?:\.(\d{3}))?)?(?:(Z)|([+\-])(\d{2})(?::(\d{2}))?)?)?$/.exec(date))) {
	         // avoid NaN timestamps caused by “undefined” values being passed to Date.UTC
	         for(var i = 0, k; (k = numericKeys[i]); ++i) {
	           struct[k] = +struct[k] || 0;
	         }

	         // allow undefined days and months
	         struct[2] = (+struct[2] || 1) - 1;
	         struct[3] = +struct[3] || 1;

	         if(struct[8] !== 'Z' && struct[9] !== undefined) {
	           minutesOffset = struct[10] * 60 + struct[11];

	           if(struct[9] === '+') {
	             minutesOffset = 0 - minutesOffset;
	           }
	         }

	         timestamp = Date.UTC(struct[1], struct[2], struct[3], struct[4], struct[5] + minutesOffset, struct[6], struct[7]);
	       }
	       else {
	         timestamp = NaN;
	       }

	       return timestamp;
	     }

	//FUNCIÓN QUE CAMBIA EL TAMAÑO DE LA ZONA DE GRÁFICO, Y REDIBUJA TODAS BARRAS DEL GRÁFICO
	this.zoom = function(valor, id,x){
		if(parseFloat(valor)<1){
			this.escala=1;		
		}
		else{
			this.escala=parseFloat(valor);
			
		}
		if(this.escalaActual>=this.escala){
			porcentaje=this.escala/this.escalaActual;
		}
		else{
			porcentaje=this.escala/this.escalaActual;
		}
		if(document.getElementById(this.contenedor).getAttribute("x")){
			valorX=document.getElementById(this.contenedor).getAttribute("x");
		}
		else{
			valorX=0;
		}
		escalaAnterior = this.escalaActual;
		
		document.getElementById(this.contenedor).setAttribute('width',this.ancho*this.escala+120);	
		document.getElementById(this.contenedor_svg).setAttribute('width',this.ancho*this.escala+120);
		var grosor =this.ancho*this.escala+120;
		var espacioMovimiento = grosor/(x*valor);
		var diferenciaEspacio=grosor/valor;

		
		this.escalaActual=this.escala;
		escalat= this.escala;
		
		this.creaBarraZonaHoraria(this.escala,true,this.monitor_id);
		this.creaEjeY(this.escala);
		this.creaCalendario(valor);
		this.creaEscalas();
		
	   
		document.getElementById(this.contenedor).setAttribute("x",parseFloat(x*this.escala));
		fechaTemp=id.split('_');
		
		if(valor==1) {
			fechaSelect=new Date(fechaTemp[2],fechaTemp[1]-1,'01','00','00','00');
		}
		if(valor==4 || valor==8) {
			fechaSelect=new Date(fechaTemp[3],fechaTemp[2]-1,fechaTemp[1],'00','00','00');
		}
		$('[id="'+this.contenedor_div+'"]').scrollLeft(parseFloat(porcentaje*x));
	}
	
	
	//función que muestra el tooltip del evento en que el mouse se encuentre arriba
	this.muestraTooltip = function(rectangulo, x, y){
		
		nombre = $(rectangulo).attr('id');
		nombre = nombre.split('_');
		elemento = nombre[1];
		indice = nombre[2];
		rectangulo_nombre = $(rectangulo).attr('id');
		
	    anchoBarra = parseFloat(rectangulo.attr('width'));
	    color_tooltip = rectangulo.attr('style');
	    color_tooltip = color_tooltip.split(':');
	    color_tooltip = color_tooltip[1];
	    
		
		rect4n = document.getElementById(rectangulo_nombre);
		rect4n.setAttribute("style","fill:"+color_tooltip+"; stroke:#000000");
		
		xoriginal = x;
		y = y+ $('[id="'+this.contenedor_div+'"]').scrollTop()+10;
		x = x+ $('[id="'+this.contenedor_div+'"]').scrollLeft();
	
		altoTotal = document.getElementById(this.contenedor).getAttribute('height');
		
		if(!document.getElementById("tooltip"+this.nombreGrafico)){
			var grupo=document.createElementNS("http://www.w3.org/2000/svg", "g");
			grupo.setAttribute("id","tooltip"+this.nombreGrafico);
			document.getElementById(this.contenedor).appendChild(grupo);	
	
			}
		else{
			var grupo=document.getElementById("tooltip"+this.nombreGrafico);
		}
	
		if(!document.getElementById("rect_tooltip"+this.nombreGrafico)){
			var rect=document.createElementNS("http://www.w3.org/2000/svg", "rect");
			rect.setAttribute("id","rect_tooltip"+this.nombreGrafico);
			rect.setAttribute("rx","10");
			rect.setAttribute("ry","10");
			rect.setAttribute("style","fill:rgb(255,255,255); stroke:"+color_tooltip+'; stroke-width:2px');
			grupo.appendChild(rect);	
		}
		else{
			var rect=document.getElementById("rect_tooltip"+this.nombreGrafico);
			rect.setAttribute("style","fill:rgb(255,255,255); stroke:"+color_tooltip+'; stroke-width:2px');	
		}
	
		
		if(!document.getElementById("texto1_tooltip"+this.nombreGrafico)){
			var texto1=document.createElementNS("http://www.w3.org/2000/svg", "text");
			texto1.setAttribute("id","texto1_tooltip"+this.nombreGrafico);
			texto1.setAttribute("class","escala9");		
			grupo.appendChild(texto1);			
		}		
		else{
			var texto1=document.getElementById("texto1_tooltip"+this.nombreGrafico);
			texto1.removeChild(texto1.childNodes[0]);			
		}
	
		if(!document.getElementById("texto2_tooltip"+this.nombreGrafico)){
			var texto2=document.createElementNS("http://www.w3.org/2000/svg", "text");
			texto2.setAttribute("id","texto2_tooltip"+this.nombreGrafico);
			texto2.setAttribute("class","escala9");		
			grupo.appendChild(texto2);
		}
		else{
			var texto2=document.getElementById("texto2_tooltip"+this.nombreGrafico);
			texto2.removeChild(texto2.childNodes[0]);		
		}
	
		if(!document.getElementById("texto3_tooltip"+this.nombreGrafico)){
			var texto3=document.createElementNS("http://www.w3.org/2000/svg", "text");
			texto3.setAttribute("id","texto3_tooltip"+this.nombreGrafico);
			texto3.setAttribute("class","escala9 ");		
			grupo.appendChild(texto3);
		}
		else{
			var texto3=document.getElementById("texto3_tooltip"+this.nombreGrafico);
			texto3.removeChild(texto3.childNodes[0]);		
		}
	
		if(!document.getElementById("texto4_tooltip"+this.nombreGrafico)){
			var texto4=document.createElementNS("http://www.w3.org/2000/svg", "text");
			texto4.setAttribute("id","texto4_tooltip"+this.nombreGrafico);
			texto4.setAttribute("class","escala9 ");
			grupo.appendChild(texto4);
		}
		
		else{			
			var texto4=document.getElementById("texto4_tooltip"+this.nombreGrafico);
			texto4.removeChild(texto4.childNodes[0]);
		}
	
		if(!document.getElementById("texto0_tooltip"+this.nombreGrafico)){
			var texto0=document.createElementNS("http://www.w3.org/2000/svg", "text");
			texto0.setAttribute("id","texto0_tooltip"+this.nombreGrafico);
			texto0.setAttribute("class","escala9 ");
			grupo.appendChild(texto0);
			}
		else{			
			var texto0=document.getElementById("texto0_tooltip"+this.nombreGrafico);
			texto0.removeChild(texto0.childNodes[0]);
		}
		width=$('#'+this.contenedor_svg).width();	
		if(parseFloat(xoriginal)+200>this.anchoTotal){
			x=parseFloat(x)-210;		
		}
		
		if(parseFloat(y)+50>this.altoTotal){
			y=parseFloat(y)-50+parseFloat(parseFloat(this.altoTotal)-parseFloat(y));		
		}
				
		rect.setAttribute("x",x+5);
		rect.setAttribute("y",y);
		rect.setAttribute("width",220);
		rect.setAttribute("height",50);
		texto0.setAttribute("x",x+10 +5);
		texto0.setAttribute("y",y+10);
		texto1.setAttribute("x",x+5 +5);
		texto1.setAttribute("y",y+25);
		texto2.setAttribute("x",x+5 +5);
		texto2.setAttribute("y",y+40);
		texto3.setAttribute("x",x+95 +5);
		texto3.setAttribute("y",y+25);
		texto4.setAttribute("x",x+95 +5);
		texto4.setAttribute("y",y+40);
	
		var fecha=this.formateaFecha2(this.datos[elemento][indice][0]);
		fechaIni = ordenaFecha(fecha);
		var fecha=this.formateaFecha2(this.datos[elemento][indice][1]);
		fechaFin = ordenaFecha(fecha);
		
		for(i=0;i<this.resources.length;i++){			
			if(this.resources[i][0]==elemento){			
				nombre_texto = (this.resources[i][1][0]).substring(0,37);
				largo_nombre_texto = nombre_texto.replace(/[^A-Z]/g, "").length;
				num_char = 37-largo_nombre_texto/4;
				var nodoTexto0=	document.createTextNode((this.resources[i][1][0]).substring(0,num_char));			
			}
		}
		
		var nodoTexto1=	document.createTextNode("Fecha Inicio :");
		var nodoTexto2=	document.createTextNode("Fecha Termino :");	
		var nodoTexto3=	document.createTextNode(fechaIni);
		var nodoTexto4=	document.createTextNode(fechaFin);
		
		texto0.appendChild(nodoTexto0);
		texto1.appendChild(nodoTexto1);	
		texto2.appendChild(nodoTexto2);
		texto3.appendChild(nodoTexto3);
		texto4.appendChild(nodoTexto4);	
		grupo.style.visibility="visible";
	}
	
	this.ocultaTooltip = function(){
		if(document.getElementById('tooltip'+this.nombreGrafico))
			document.getElementById('tooltip'+this.nombreGrafico).style.visibility='hidden';
	}
	
	
	this.restauraCuadro = function(cuadroAntiguo){
	    color_temp = String(cuadroAntiguo.attr('style'));    
	    color_temp2 = color_temp.split(';');    
	    color_temp = color_temp2[0].split(':');    	    
	    color_antiguo = color_temp[1];	    		
		rect2 = document.getElementById(cuadroAntiguo.attr('id'));
		rect2.setAttribute("style","fill:"+color_antiguo);
	}

	/*
	Función para mostrar un modal al evento especial.
	*/
	this.showModal = function(rectangulo, x, y){
	
		var datosModal = self.contruirVariable(rectangulo);
		self.agregarHtmlModal(datosModal);
		$(document).ready(function()
		{
			$( "#dialog" ).dialog({
		    	width:'300px',
		    	heigth:'600px',
		    	dialogClass: 'ui-dialog-osx',
		    	resizable: false,
		    	modal:true
			});
		});
		
	}
	/* Construir la variable que contendra los datos del modal*/
	this.contruirVariable = function(rectangulo){
		var datosModal = new Array();
		var nombre = $(rectangulo).attr('id');
		nombre = nombre.split('_');
		var elemento = nombre[1];
		var indice = nombre[2];
		var rectangulo_nombre = $(rectangulo).attr('id');
		var idNombreObjetivo = '#texto_'+String(elemento)+'disponibilidadGlobal';
		
		datosModal['nombreObjetivo'] = $(idNombreObjetivo).html();
		datosModal['fechaInicio'] = this.datos[elemento][indice][0];
		datosModal['fechaTermino'] = this.datos[elemento][indice][1];
		datosModal['titulo'] = this.datos[elemento][indice][3];
		datosModal['nombreUsuario'] = this.datos[elemento][indice][4];
		datosModal['id'] = this.datos[elemento][indice][5];
		datosModal['fechaInicioReal'] = this.datos[elemento][indice][6];
		return datosModal; 
	}
	/* Agrega hmtl al modal.*/
	this.agregarHtmlModal = function(datosModal){
		$( "#dialog" ).empty();
		fechaInicio = self.formatoFecha((datosModal['fechaInicioReal']).replace(/[.]/g,'-'));
		fechaTermino = self.formatoFecha((datosModal['fechaTermino']).replace(/[.]/g,'-'));
		nombre = '<p>Nombre Objetivo: '+datosModal['nombreObjetivo']+' </p>';
		titulo = '<p>Titulo: '+datosModal['titulo']+'</p>';
		termino = '<p>Fecha termino: '+fechaTermino['fecha']+' '+fechaTermino['hora']+'</p>';
		inicio = '<p>Fecha inicio: '+fechaInicio['fecha']+' '+fechaInicio['hora']+'</p>';
		usuario = '<p>Usuario: '+datosModal['nombreUsuario']+' </p>';
		button = '<button class=butonLeft onclick=verDetalle('+datosModal['id']+')>Ver detalle</button>';
		$( "#dialog" ).append(inicio, termino, titulo, usuario, button);
	}
	/* Separa la fecha y hora */
	this.formatoFecha = function(fecha){
		pos = 10;
		fin = fecha.length;
		fechaArr = Array();
		fechaArr['fecha'] = fecha.substring(0, pos);
		fechaArr['hora'] = (fecha.substring(pos+1, fin)).replace(/[-]/g,':');
		return fechaArr;
	}
	
}
// - Muestra el detalle del evento mantenimiento.
verDetalle = function(id){
	elem = $('#seccion_mantenencion');
	seccion = elem.data('seccion');
	historial = elem.data('historial');
	mostrarSubmenu(seccion,historial,1,parseInt(id));
}
//- Construye la tabla y inicia el dialog.
buildTableObj = function(objs){
	var i=0;
	$('#tbodyObjMan').empty();
	objetives = JSON.parse(objs);
	$('<table>').attr({id: 'tableobjetivos',width:'100%',style:"background-color='#d0d0d0'"}).appendTo('#tbodyObjMan');
		$('<tr>').attr({id: 'trTitleObjetivos'}).appendTo('#tableobjetivos');
		$('<th>').text('Objetivo id').appendTo('#trTitleObjetivos');
		$('<th>').text('Nombre').attr({width: '50%'}).appendTo('#trTitleObjetivos');
		$('<th>').text('Pasos').attr({width: '20%'}).appendTo('#trTitleObjetivos');
		
		
	$.each(objetives, function( index, value ) {
		$('<table>').attr({id: 'objetivo_'+value.objetivo_id, width:'100%'}).appendTo('#tbodyObjMan');
			$('<tr>').attr({id: 'trOjetivo_'+value.objetivo_id}).appendTo('#objetivo_'+value.objetivo_id);
			$('<td>').text(value.objetivo_id).appendTo('#trOjetivo_'+value.objetivo_id);
			$('<td>').text(value.nombre).attr({width: '50%'}).appendTo('#trOjetivo_'+value.objetivo_id);
			$('<td>').attr({id: 'html_'+value.objetivo_id, width: '20%'}).appendTo('#trOjetivo_'+value.objetivo_id);
			$('<i>').attr({id: 'i_'+value.objetivo_id, class:'spriteButton spriteButton-abrir_calendario', onclick:'mostrarPasos('+value.objetivo_id+')'}).appendTo('#html_'+value.objetivo_id);

		$('<div>').attr({id:'divPasos_'+value.objetivo_id , width:'100%'}).appendTo('#tbodyObjMan').hide();
		$('<table>').attr({id:'tablePasos_'+i+'_'+value.objetivo_id, width:'99%',align:"center"}).appendTo('#divPasos_'+value.objetivo_id);
		
		$('<table>').attr({id:'tableTitlePasos_'+i+'_'+value.objetivo_id, width:'100%'}).appendTo('#tablePasos_'+i+'_'+value.objetivo_id);
		$('<tr>').attr({id: 'trTitlePasos_'+i+'_'+value.objetivo_id}).appendTo('#tableTitlePasos_'+i+'_'+value.objetivo_id);
		$('<th>').text('Paso Orden').appendTo('#trTitlePasos_'+i+'_'+value.objetivo_id);
		$('<th>').text('Nombre').attr({width: '50%'}).appendTo('#trTitlePasos_'+i+'_'+value.objetivo_id);
		$('<th>').html('Patrones').attr({width: '20%'}).appendTo('#trTitlePasos_'+i+'_'+value.objetivo_id);

		$.each(value.pasos, function( index, valuepasos ) {
			$('<table>').attr({id:'tablePaso_'+valuepasos.paso_id+'_'+value.objetivo_id, width:'100%'}).appendTo('#tablePasos_'+i+'_'+value.objetivo_id);
			$('<tr>').attr({id: 'trPaso_'+valuepasos.paso_id+'_'+value.objetivo_id}).appendTo('#tablePaso_'+valuepasos.paso_id+'_'+value.objetivo_id);
			$('<td>').text(valuepasos.paso_id).appendTo('#trPaso_'+valuepasos.paso_id+'_'+value.objetivo_id);
			$('<td>').text(valuepasos.nombre).attr({width: '50%'}).appendTo('#trPaso_'+valuepasos.paso_id+'_'+value.objetivo_id);
			
			$('<td>').attr({id: 'html_'+value.objetivo_id+'_'+valuepasos.paso_id, width: '20%'}).appendTo('#trPaso_'+valuepasos.paso_id+'_'+value.objetivo_id);
			if(typeof valuepasos.patrones !== 'undefined'){
				
				$('<i>').attr({id: 'i_'+value.objetivo_id+'_'+valuepasos.paso_id, class:'spriteButton spriteButton-abrir_calendario', onclick:'mostrarPatrones('+value.objetivo_id+','+valuepasos.paso_id+')'}).appendTo('#html_'+value.objetivo_id+'_'+valuepasos.paso_id);
				
				$('<div>').attr({id:'divPatrones_'+value.objetivo_id+'_'+valuepasos.paso_id , width:'100%'}).appendTo('#tablePasos_'+i+'_'+value.objetivo_id).hide();
				$('<table>').attr({id:'tablePatrones_'+value.objetivo_id+'_'+valuepasos.paso_id, align: 'center', width:'95%'}).appendTo('#divPatrones_'+value.objetivo_id+'_'+valuepasos.paso_id);
				$('<tr>').attr({id: 'trTitlePatrones_'+value.objetivo_id+'_'+valuepasos.paso_id	}).appendTo('#tablePatrones_'+value.objetivo_id+'_'+valuepasos.paso_id);
				$('<th>').text('Patron Orden').appendTo('#trTitlePatrones_'+value.objetivo_id+'_'+valuepasos.paso_id);
				$('<th>').text('Nombre').attr({width: '40%'}).appendTo('#trTitlePatrones_'+value.objetivo_id+'_'+valuepasos.paso_id);
				$('<th>').html('Valor').attr({width: '30%'}).appendTo('#trTitlePatrones_'+value.objetivo_id+'_'+valuepasos.paso_id);
				
				$.each(valuepasos.patrones, function( index, valuepatrones ) {
					$('<tr>').attr({id: 'trPatron_'+value.objetivo_id+'_'+valuepasos.paso_id+'_'+valuepatrones.patron_orden}).appendTo('#tablePatrones_'+value.objetivo_id+'_'+valuepasos.paso_id);
					$('<td>').text(valuepatrones.patron_orden).appendTo('#trPatron_'+value.objetivo_id+'_'+valuepasos.paso_id+'_'+valuepatrones.patron_orden);
					$('<td>').text(valuepatrones.nombre).attr({width: '40%'}).appendTo('#trPatron_'+value.objetivo_id+'_'+valuepasos.paso_id+'_'+valuepatrones.patron_orden);
					$('<td>').text(valuepatrones.valor).attr({width: '30%'}).appendTo('#trPatron_'+value.objetivo_id+'_'+valuepasos.paso_id+'_'+valuepatrones.patron_orden);
				});
			}
		});
		i++;
	});
	
	$( "#dialog-obj" ).dialog({
    	width:'500px',
    	heigth:'auto',
    	dialogClass: 'ui-dialog-osx',
    	resizable: true,
    	modal:true
	});
}

mostrarPasos = function(id){
	if (document.getElementById('divPasos_'+id).style.display == "none") {
        document.getElementById('divPasos_'+id).style.display = "inline";
        document.getElementById('i_' +id).className = "spriteButton spriteButton-cerrar_calendario";
    } else {
        document.getElementById('divPasos_'+id).style.display = "none";
        document.getElementById('i_' +id).className = "spriteButton spriteButton-abrir_calendario";
   }
}

mostrarPatrones = function(id1,id2){
	if (document.getElementById('divPatrones_'+id1+'_'+id2).style.display == "none") {
        document.getElementById('divPatrones_'+id1+'_'+id2).style.display = "inline";
        document.getElementById('i_' +id1+'_'+id2).className = "spriteButton spriteButton-cerrar_calendario";
    } else {
        document.getElementById('divPatrones_'+id1+'_'+id2).style.display = "none";
        document.getElementById('i_' +id1+'_'+id2).className = "spriteButton spriteButton-abrir_calendario";
   }
}

 //- Llamada ajax para obtener los objetivos.
getObjetivos = function(ids){
//	event.preventDefault();
	$.ajax({
		async: false,
        url: '../call_ajax.php',
        data:{'ids':ids ,'nameFunction':'getObjetiveName'},
        type: 'POST',
        success: function(response) {
        	buildTableObj(response);
        },
        error: function(error) {
        }
    });
}

this.ordenaFecha = function(fecha){
	if(parseInt(fecha.getUTCDate())<10)
		dia = '0'+fecha.getUTCDate();
	else
		dia = fecha.getUTCDate();

	if((parseInt(fecha.getUTCMonth())+1)<10)
		mes='0'+(fecha.getUTCMonth()+1);
	else
		mes=(fecha.getUTCMonth()+1);
	
	if(parseInt(fecha.getUTCHours())<10)
		hora='0'+fecha.getUTCHours();
	else
		hora=fecha.getUTCHours();
	
	if(parseInt(fecha.getUTCMinutes())<10)
		min='0'+fecha.getUTCMinutes();
	else
		min=fecha.getUTCMinutes();
	
	if(parseInt(fecha.getUTCSeconds())<10)
		sec='0'+fecha.getUTCSeconds();
	else
		sec=fecha.getUTCSeconds();
	
	
	return dia+'-'+mes+'-'+fecha.getUTCFullYear()+' '+hora+':'+min+':'+sec;

}