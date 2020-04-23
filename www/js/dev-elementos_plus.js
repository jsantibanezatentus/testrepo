function buildCsv(data, objetivoId, objetivoName, service, date, timeDownload){
   
    var json = JSON.stringify(data);
   /*CREANDO EL FORMULARIO*/
    $('<form>').attr({
        type: 'hidden',
        id: 'form',
        name: 'form',
        action: '../utils/descarga_csv_elementos_plus.php',
        method: 'POST',
        target: '_self'
    }).appendTo('body');

    $('#form').hide();
    
    $('<input>').attr({
        type: 'text',
        id: 'data',
        name: 'data',
        value: unescape(encodeURIComponent(json))
    }).appendTo('#form');
    
    $('<input>').attr({
        type:'text',
        id: 'objetivoId',
        name:'objetivoId',
        value: objetivoId
    }).appendTo('#form');
    
      
    $('<input>').attr({
        type:'text',
        id: 'serviceId',
        name:'serviceId',
        value: service
    }).appendTo('#form');
    
    $('<input>').attr({
        type:'text',
        id: 'objetivoName',
        name:'objetivoName',
        value: objetivoName
    }).appendTo('#form');
    
    $('<input>').attr({
        type:'text',
        id: 'date',
        name:'date',
        value: date
    }).appendTo('#form');
    
    $('<input>').attr({
        type:'text',
        id: 'time',
        name:'time',
        value: timeDownload
    }).appendTo('#form');
        
    $("#form").submit();
    $("#form").remove();
}
//Funcion que despliega modal de ayuda y funcion de destruccion  id 
function modal(){
    this.mostrarModal = function(){
        var estados = "";
        var i=0;
        var contador = 8;
        var elem='https://reporte2.atentus.com/simbologia.php';
        var enlace = elem.replace(elem,"<a   href='"+elem+"' target='_blank'>"+elem+"</a>"); 
        var ayuda = [
            "Url ",
            "Estado: ",
            "Bytes: ",
            "Segundos: ",
            "Ip: ",
            "Latencia",
            "Dns",
            "Descarga"
        ];
        var descripcion = [   
            "Hace referencia la URL del elemento actual.",
            "Indica el código del elemento HTTP que devolvió el servidor WEB o el error de red que detecto el atentubot, más información de códigos en: "+enlace+".",
            "Columna que indica el tamaño de los elementos.",
            "Columna que indica el tiempo total que tardo un elemento en descargar.",
            "Muestra la dirección IP del elemento actual.",
            "Tiempo que transcurrio entre la petición del elemento hasta que empezo la descarga del primer byte.",
            "Tiempo que demora el servidor DNS en resolver.",
            "Tiempo que transcurrio entre la descarga del primer byte y el termino de la descarga completa."
         ];        
        //recorre los array para posicionarlos en tablas separadas
        for ( i=0; i<contador; i++){
            estados = estados + '<table class="definicion" width="100%" ><tr><th>' + ayuda[i] + '</th></tr><tr><td>' + descripcion[i] + '</td></tr></table></br>';
        }
        dojo.byId("contenido").innerHTML = estados;
        dijit.byId("ModalAyuda").show();
    };
//Destruye identificador si esta no ha sido definida
    this.cerrarAyuda= function(){
        if(typeof dijit.byId("ModalAyuda") != "undefined"){
            dijit.byId("ModalAyuda").destroyRecursive();
        }
    };
    //Destruye identificador si esta  ha sido definida
    this.cerrarElemento= function(){
        if(typeof dijit.byId("ModalMuestra") != "undefined"){
            dijit.byId("ModalMuestra").destroyRecursive();
        }
    };
    
}
//Funcion que despliega modal elementos
function ShowModal(indice, elem, tipo, ip, latencia, descarga, tamano, estado, dns, descripcion, titulo) {
    
    var patron='"';
    var extraer="localhost";
    var contenido = "";
    var i=0;
    var h=1;
    var contador = 8;
    var timeDownload = timeTotalDownload(latencia, descarga);
    
    if ((tipo==null) || (tipo.length == 0) || (tipo=="")){
        tipo="NO CAPTURADO";
    }
    if ((latencia==null) || (latencia.length == 0) || (latencia=="")){
        latencia="0";
    }
    if ((descarga===null) || (descarga.length == 0) || (descarga=="")){
        descarga="0";
    }
    if ((tamano==null) || (tamano.length == 0) || (tamano=="")){
        tamano="0";
    }
    
    latencia=(latencia*1000)+' ms';
    descarga=(descarga*1000)+' ms';
    tamano=tamano+' Bytes';
    dns=dns+' ms';
    
    if ((ip==null) || (ip.length == 0) || (ip=="")){
        ip="NO CAPTURADO";
    }
    if ((dns==null) || (dns.length == 0) || (dns=="")){
        dns="0.000 ms";
    }
    if(elem.length>69){
        //corta la url si es muy larga
        for(var j=0; j<elem.length;j++){
            if(j===(h*70)){
                elem=elem.substring(0,j)+"\n"+elem.substring(j,elem.length);
                h++;
            }
        }
    }
    //corta el nombre del modal si es muy largo
    if(titulo.length>34){
         var tituloCut=titulo.substring(0,35)+' ...';
    }
    else{
        tituloCut=titulo;
    }
    //reemplaza por enlace
    var text = elem.replace(elem,"<a   href='"+elem+"' target='_blank'>"+elem+"</a>"); 
    estado=estado+"  ("+descripcion+")";
    //extraccion de ciertos patrones
    text=text.replace(patron,'');
    text=text.replace(extraer,'');
    
    var ayuda = [
        "Url: ",
        "Tipo: ",
        "Ip: ",
        "Latencia: ",
        "Descarga: ",
        "Tiempo total Descarga",
        "Tamaño: ",
        "Estado",
        "Dns"                 
    ];
    var datos = [   
        text,
        tipo,
        ip,
        latencia,
        descarga,
        timeDownload,
        tamano,
        estado,
        dns
    ];
    //validacion si el usuario esta utilizando IE
    if ( navigator.userAgent.indexOf("MSIE")>0 ){
        for ( i=0; i<contador; i++){
            contenido = contenido + '<table class="definicion" width="100%" ><tr><th>' + ayuda[i] + '</th></tr><tr><td>' + datos[i] + '</td></tr></table></br>';
        }
        dojo.byId("usoIE").innerHTML = contenido;
    }
    //resto de navegadores
    else{
        //recorre para guardar cada array en su fila correspondiente
        for ( i=0; i<contador; i++){
            contenido = contenido + '<tr><th>' + ayuda[i] + '</th></tr><tr><td>' + datos[i] + '</td></tr>';
        }
        dojo.byId("contenido2").innerHTML = contenido;
        
    }
    
    //atributos del cuadro dojo
    dojo.attr('ModalMuestra_title',{
        innerHTML: "Elemento "+indice+": "+tituloCut,
        title: "Elemento: "+indice+" "+titulo
    });
    dojo.attr('ModalMuestra', {
        style: "widht: 500px;height: 365px; overflow-y: hidden;overflow-x: hidden;padding:2px"
    });
    dijit.byId("ModalMuestra").show();
}

function timeTotalDownload(latency, download){
    
    var timeDownload = ((parseInt(latency.replace('.',''))) + (parseInt(download.replace('.','')))).toString();
    var longTime = timeDownload.length;
    
    if(longTime == 4){
        timeDownload = timeDownload.substr(0, 1)+'.'+timeDownload.substr(1);
    }else if(longTime == 5){
        timeDownload = timeDownload.substr(0, 2)+'.'+timeDownload.substr(2);
    }else if(longTime == 6){
        timeDownload = timeDownload.substr(0, 3)+'.'+timeDownload.substr(3);
    }
    
    return timeDownload + ' ms';
}

function elementosPlus(id){
        
	var self = this;
	this.divisiones=10;         	
	this.anchoTotal=715;
    this.anchoDibujable;
	this.anchoCeldasDerechas=50;
	this.anchoNombres=105;
	this.altoElementos=0;
	this.totalTiempo=0;
	this.totalBody=0;
	this.totalHeader=0;
	this.datos= '';
	this.tiempoTotal = '';
	this.nombreGrafico = id;
	this.alto='';
	
	var svg=document.createElementNS("http://www.w3.org/2000/svg", "svg");
	svg.setAttribute("width",this.anchoTotal);
	svg.setAttribute("id","svgelem_"+this.nombreGrafico);
	svg.setAttribute("xmlns","http://www.w3.org/2000/svg");
        
	
//	svg.setAttribute("xmlns:xlink","http://www.w3.org/1999/xlink");s
	document.getElementById("contenedor_"+this.nombreGrafico).appendChild(svg);
        //document.getElementById("contenedor_2"+this.nombreGrafico).appendChild(svg);
	
	var rect=document.createElementNS("http://www.w3.org/2000/svg", "rect");
	rect.setAttribute("x",0);
	rect.setAttribute("y",0);
	rect.setAttribute("height","100%");
	rect.setAttribute("width","100%");
	rect.setAttribute("id","fondo");
	rect.setAttribute("class","rect_cont");
	rect.setAttribute("style","fill:#ffffff");
	svg.appendChild(rect);
	
//se agrego el nombre de estado, que trae la descripcion de status para ser mostrado en el tooltip de estado
	this.variables={
			0:'elementos',
			1:'ip',
			2:'espera',
			3:'latencia',		
			4:'descarga',
			5:'DNS',		
			6:'tamanoBody',
			7:'tamanoHeader',
			8:'contentType',
			9:'status',
			10:'es_ok',
                        11:'nombre_estado'
			};
	this.textTooltipb="font-family: Monospace; font-size: 9px; color: #323232; font-weight:bold; text-transform: uppercase;";
	//Se agrego el puntero al estilo
        this.textTooltip="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 9px; color: #323232;";
        this.textTooltip2="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 9px; color: #323232;cursor: pointer;text-decoration: underline;";
        //this.textTooltip3="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 9px; color: #323232; font-weight: bold;"; 
	this.download = function(){
	
        $.ajax({
            async:true,
            data:{"data": data},
            url:'../utils/descarga_csv_elementos_plus.php',
            type:'POST'
        });
	};
        this.asociar = function(){
	    $('#contenedor_'+this.nombreGrafico).find(".tiene_tooltip ").live('mousemove',function(e){
	    	posX = e.pageX - $('#contenedor_'+self.nombreGrafico).offset().left;
	  		posY = e.pageY - $('#contenedor_'+self.nombreGrafico).offset().top;
	  		indice = this.getAttribute("data-indice");
	  		url = this.getAttribute("data-url");
	  		self.cuadroTooltip(indice, posX, posY, url);
                        });
                        
            $('#contenedor_'+this.nombreGrafico).find(".tiene_tooltip2 ").live('mousemove',function(e){
	    	posX2 = e.pageX - $('#contenedor_'+self.nombreGrafico).offset().left;
	  		posY2 = e.pageY - $('#contenedor_'+self.nombreGrafico).offset().top;
	  		indice2 = this.getAttribute("data-indice");
	  		url2 = this.getAttribute("data-url");
	  		self.cuadroTooltip2(indice2, posX2, posY2, url2);
	  		});
            $('#contenedor_'+this.nombreGrafico).live('mouseout',function(e){
	    	self.borraCuadroTexto();
            });
	};
       
	this.creaGraficos = function(){
           
            if(this.datos[0].length>0){
			//modifica el alto por la cantidad de elementos a gráficar
			
			if(this.alto<60){
                            
				document.getElementById('svgelem_'+this.nombreGrafico).setAttribute("height",210);
			}
			else{
		     document.getElementById('svgelem_'+this.nombreGrafico).setAttribute("height",this.alto+140);
			}
		     //CREA LAS ESCALAS DUBUJANDO LAS LINEAS HORIZONTALES Y VERTICALES QUE SEPARAN EL GRÁFICO
		     this.creaEscalas();
		     var i=0;
		     //TRANSFORMA EL TIEMPO TOTAL DE DESCARGA A SEGUNDOS
		     this.tiempoTotal=this.transformaSegundos(this.tiempoTotal);
		     //GUARDA EL VALOR DEL ANCHO QUE SE DISPONE PARA DIBUJAR LAS BARRAS
		     this.anchoDibujable=((this.anchoTotal-(this.anchoCeldasDerechas*2)-this.anchoNombres)*.95)-8;
		     //LLAMA A LA FUNCIÓN QUE CREA EL EJE "X"
		     this.creaEjeX();
		     //REPITE POR CADA ELEMENTO CAPTURADO
		     while(this.datos[i]){
		     	//LLAMADO A LA FUNCIÓN QUE DIBUJA EL EJE Y            
			     this.creaEjeY(i);
			     //LLAMA A LA FUNCIÓN QUE DIBUJA LAS BARRAS DE LATENCIA Y DESCARGA	     
			     this.creaBarra(i);
			     //LLAMA A LA FUNCIÓN QUE CARGA LOS VALORES UBICADOS A LA DERECHA DE LA PANTALLA
			     this.creaCeldasDerechas(i);
			     //INCREMENTA LA SUMATORIA DE VALORES EN EL BODY Y HEADER DEPENDIENDO SI VIENEN O NO LOS VALORES 
			     if(!isNaN(parseFloat(this.datos[i][6]))){
			     	this.totalBody=this.totalBody+parseFloat(this.datos[i][6]);
			     }
			     if(this.datos[i][7]!=-1 && !isNaN(this.datos[i][7])){
			     	this.totalHeader=this.totalBody+parseFloat(this.datos[i][7]);
			     }
	
			     i++;
		     }
		     //LLAMADO A LA FUNCIÓN QUE DIBUJA LOS VALORES TOTALES EN LA PARTE INFERIOR DE LA PANTALLA
		     this.creaTotales();
		}
		else{
			var img = document.getElementById('logo_advertencia_'+this.nombreGrafico);
			img.style.display='inline';          
			var cuadrotext0 = document.createElementNS("http://www.w3.org/2000/svg",'text');
			var nodoTexto0 = document.createTextNode("No se encontraron datos para este monitoreo.");
			cuadrotext0.setAttribute("x",260);		
			cuadrotext0.setAttribute("y",36);	
			cuadrotext0.setAttribute("style","font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;color: #525252;text-decoration: none;font-weight: normal;padding: 1px;");
			cuadrotext0.appendChild(nodoTexto0);				
			document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext0);
			}	}
	
	/*FUNCIÓN QUE DIBUJA LAS LINEAS SEPARADORAS DEL EJE "X" E "Y"*/
	this.creaEscalas = function(){
		//DIBUJO DE LA LÍNEA HORIZONTAL
		if(!document.getElementById("eje_1_"+this.nombreGrafico)){
			var line=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var line=document.getElementById("eje_1_"+this.nombreGrafico);
		}
		line.setAttribute("x1",5);
		line.setAttribute("x2",this.anchoTotal);
		line.setAttribute("y1",40);
		line.setAttribute("y2",40);
		line.setAttribute("id","eje_1_"+this.nombreGrafico);
		line.setAttribute("style",'stroke:black;stroke-width:0.7;fill:#000000');
		
		//DIBUJO DE LA LÍNEA VERTICAL 1
		var line2=document.createElementNS("http://www.w3.org/2000/svg", "line");
		line2.setAttribute("x1",this.anchoTotal-(this.anchoCeldasDerechas*2));
		line2.setAttribute("x2",this.anchoTotal-(this.anchoCeldasDerechas*2));
		line2.setAttribute("y1",40);
		line2.setAttribute("y2",this.alto+140);
		line2.setAttribute("id","eje_2_"+this.nombreGrafico);
		line2.setAttribute("style",'stroke:black;stroke-width:0.7;fill:#000000');
	
		//DIBUJO DE LA LÍNEA VERTICAL 2
		var line3=document.createElementNS("http://www.w3.org/2000/svg", "line");
		line3.setAttribute("x1",this.anchoTotal-(this.anchoCeldasDerechas));
		line3.setAttribute("x2",this.anchoTotal-(this.anchoCeldasDerechas));
		line3.setAttribute("y1",40);
		line3.setAttribute("y2",this.alto+20);
		line3.setAttribute("id","eje_3_"+this.nombreGrafico);
		line3.setAttribute("style",'stroke:black;stroke-width:0.7;fill:#000000');
	
		//DIBUJO DE LA LÍNEA VERTICAL 3
		var line4=document.createElementNS("http://www.w3.org/2000/svg", "line");
		line4.setAttribute("x1",this.anchoTotal);
		line4.setAttribute("x2",this.anchoTotal);
		line4.setAttribute("y1",40);
		line4.setAttribute("y2",this.alto+140);
		line4.setAttribute("id","eje_4_"+this.nombreGrafico);
		line4.setAttribute("style",'stroke:black;stroke-width:0.7;fill:#000000');
	
		var rect1=document.createElementNS("http://www.w3.org/2000/svg", "rect");
		rect1.setAttribute("x",0);
		rect1.setAttribute("width",this.anchoTotal);
		rect1.setAttribute("y",(this.datos.length+2)*20-2);
		rect1.setAttribute("height",120);
		rect1.setAttribute("fill","#f4f7f9");
		rect1.setAttribute("opacity","1");
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(rect1);
	
		var cuadrotext1=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var nodoTexto1 = document.createTextNode('Segundos');
		cuadrotext1.setAttribute("x",this.anchoTotal/2+15);		
		cuadrotext1.setAttribute("y",15);			
		cuadrotext1.setAttribute('style',this.textTooltipb+'text-anchor: end');	
		cuadrotext1.appendChild(nodoTexto1);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext1);
	
		//DIBUJO DE LA LÍNEA QUE ACOMPAÑA AL TITULO SEGUNDOS
		var line5=document.createElementNS("http://www.w3.org/2000/svg", "line");
		line5.setAttribute("x1",this.anchoNombres+10);
		line5.setAttribute("x2",(this.anchoTotal/2)-55);
		line5.setAttribute("y1",13);
		line5.setAttribute("y2",13);
		line5.setAttribute("id","eje_5_"+this.nombreGrafico);
		line5.setAttribute("style",'stroke:black;stroke-width:0.7;fill:#000000');	
	
		//DIBUJO DE LA LÍNEA QUE ACOMPAÑA AL TITULO SEGUNDOS
		var line6=document.createElementNS("http://www.w3.org/2000/svg", "line");
		line6.setAttribute("x1",(this.anchoTotal/2)+55);
		line6.setAttribute("x2",(this.anchoTotal)-102);
		line6.setAttribute("y1",13);
		line6.setAttribute("y2",13);
		line6.setAttribute("id","eje_6_"+this.nombreGrafico);
		line6.setAttribute("style",'stroke:black;stroke-width:0.7;fill:#000000');
		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(line);
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(line2);
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(line3);
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(line4);
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(line5);
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(line6);
	}
	
	
	
	this.creaTotales = function(){		
		//DIBUJO DE LA LÍNEA HORIZONTAL INFERIOR 1
		if(!document.getElementById("fin_1_"+this.nombreGrafico)){
			var line=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var line=document.getElementById("fin_1_"+this.nombreGrafico);
		}
		line.setAttribute("x1",0);
		line.setAttribute("x2",this.anchoTotal);
		line.setAttribute("y1",this.altoElementos);
		line.setAttribute("y2",this.altoElementos);	
		line.setAttribute("id","fin_1_"+this.nombreGrafico);
		line.setAttribute("style",'stroke:black;stroke-width:0.7;fill:#000000');
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(line);			
		
		//DIBUJO DE LA LÍNEA HORIZONTAL INFERIOR 2
		if(!document.getElementById("fin_2_"+this.nombreGrafico)){
			var line=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var line=document.getElementById("fin_2_"+this.nombreGrafico);
		}
		line.setAttribute("x1",0);
		line.setAttribute("x2",this.anchoTotal);
		line.setAttribute("y1",this.altoElementos+20);
		line.setAttribute("y2",this.altoElementos+20);	
		line.setAttribute("id","fin_2_"+this.nombreGrafico);
		line.setAttribute("style",'stroke:black;stroke-width:0.7;fill:#000000');
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(line);
	
		//imprime el valor del tiempo total
		var cuadrotext1=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var nodoTexto1 = document.createTextNode("Tiempo Descarga (Segs)");
		cuadrotext1.setAttribute("x",10);		
		cuadrotext1.setAttribute("y",this.altoElementos+16);	
		cuadrotext1.setAttribute("style",this.textTooltipb);
		cuadrotext1.appendChild(nodoTexto1);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext1);
	
		var cuadrotext2=document.createElementNS("http://www.w3.org/2000/svg",'text');
                var nodoTexto2 = document.createTextNode(this.tiempoTotal.toFixed(3));
                cuadrotext2.setAttribute("x",this.anchoTotal-5);		
		cuadrotext2.setAttribute("y",this.altoElementos+16);			
		cuadrotext2.setAttribute('style',this.textTooltipb+'text-anchor: end');
		cuadrotext2.appendChild(nodoTexto2);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext2);
		
		
		//DIBUJO DE LA LÍNEA HORIZONTAL INFERIOR 2
		if(!document.getElementById("fin_3_"+this.nombreGrafico)){
			var line=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var line=document.getElementById("fin_3_"+this.nombreGrafico);
		}
		line.setAttribute("x1",0);
		line.setAttribute("x2",this.anchoTotal);
		line.setAttribute("y1",this.altoElementos+40);
		line.setAttribute("y2",this.altoElementos+40);	
		line.setAttribute("id","fin_1_"+this.nombreGrafico);
		line.setAttribute("style",'stroke:black;stroke-width:0.7;fill:#000000');
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(line);
	
		//IMPRIME EL VALOR DEL SUMATORIA DE TIEMPO
		var cuadrotext3=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var nodoTexto3 = document.createTextNode("Sumatoria Tiempos (Segs)");
		cuadrotext3.setAttribute("x",10);		
		cuadrotext3.setAttribute("y",this.altoElementos+36);	
		cuadrotext3.setAttribute("style",this.textTooltipb);
		cuadrotext3.appendChild(nodoTexto3);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext3);
		var cuadrotext4=document.createElementNS("http://www.w3.org/2000/svg",'text');
                var nodoTexto4 = document.createTextNode(this.totalTiempo.toFixed(3));
		cuadrotext4.setAttribute("x",this.anchoTotal-5);		
		cuadrotext4.setAttribute("y",this.altoElementos+36);	
		cuadrotext4.setAttribute('style',this.textTooltipb+'text-anchor: end');
	
		cuadrotext4.appendChild(nodoTexto4);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext4);	
	
		
		
		
		//DIBUJO DE LA LÍNEA HORIZONTAL INFERIOR 3
		if(!document.getElementById("fin_4_"+this.nombreGrafico)){
			var line=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var line=document.getElementById("fin_4_"+this.nombreGrafico);
		}
		line.setAttribute("x1",0);
		line.setAttribute("x2",this.anchoTotal);
		line.setAttribute("y1",this.altoElementos+60);
		line.setAttribute("y2",this.altoElementos+60);	
		line.setAttribute("id","fin_4_"+this.nombreGrafico);
		line.setAttribute("style",'stroke:black;stroke-width:0.7;fill:#000000');
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(line);
	
		//IMPRIME EL VALOR DEL TAMAÑO TOTAL
		var cuadrotext4=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var nodoTexto4 = document.createTextNode("Tamaño Total (KB)");
		cuadrotext4.setAttribute("x",10);		
		cuadrotext4.setAttribute("y",this.altoElementos+56);	
		cuadrotext4.setAttribute('style',this.textTooltipb);
		cuadrotext4.appendChild(nodoTexto4);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext4);
	
		var cuadrotext5=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var totalTamano=(this.totalBody+this.totalHeader)/1024;
		var nodoTexto5 = document.createTextNode(totalTamano.toFixed(3));
		cuadrotext5.setAttribute("x",this.anchoTotal-5);		
		cuadrotext5.setAttribute("y",this.altoElementos+56);	
		cuadrotext5.setAttribute('style',this.textTooltipb+'text-anchor: end');
		cuadrotext5.appendChild(nodoTexto5);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext5);
                
                //DIBUJO DE LA LÍNEA HORIZONTAL INFERIOR 4
		if(!document.getElementById("fin_5_"+this.nombreGrafico)){
			var line=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var line=document.getElementById("fin_5_"+this.nombreGrafico);
		}
		line.setAttribute("x1",0);
		line.setAttribute("x2",this.anchoTotal);
		line.setAttribute("y1",this.altoElementos+80);
		line.setAttribute("y2",this.altoElementos+80);	
		line.setAttribute("id","fin_5_"+this.nombreGrafico);
		line.setAttribute("style",'stroke:black;stroke-width:0.7;fill:#000000');
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(line);
	
		//IMPRIME EL VALOR DEL TOTAL ELEMENTOS
		var cuadrotext5=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var nodoTexto5 = document.createTextNode("Total Elementos Descargados");
		cuadrotext5.setAttribute("x",10);		
		cuadrotext5.setAttribute("y",this.altoElementos+76);	
		cuadrotext5.setAttribute('style',this.textTooltipb);
		cuadrotext5.appendChild(nodoTexto5);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext5);
	
		var cuadrotext6=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var nodoTexto6 = document.createTextNode(this.datos.length);
		cuadrotext6.setAttribute("x",this.anchoTotal-5);		
		cuadrotext6.setAttribute("y",this.altoElementos+76);	
		cuadrotext6.setAttribute('style',this.textTooltipb+'text-anchor: end');
		cuadrotext6.appendChild(nodoTexto6);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext6);
                
                
                for (var i=0, elementOk = 0, elementError = 0;this.datos.length>i;i++){
                    if(this.datos[i][10] == 'true'){
                        elementOk = elementOk+1;
                    }
                    else{
                        elementError = elementError+1;
                    }
                }
               //DIBUJO DE LA LÍNEA HORIZONTAL INFERIOR 5
		if(!document.getElementById("fin_6_"+this.nombreGrafico)){
			var line=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var line=document.getElementById("fin_6_"+this.nombreGrafico);
		}
		line.setAttribute("x1",0);
		line.setAttribute("x2",this.anchoTotal);
		line.setAttribute("y1",this.altoElementos+100);
		line.setAttribute("y2",this.altoElementos+100);	
		line.setAttribute("id","fin_6_"+this.nombreGrafico);
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(line);
	
		//IMPRIME EL VALOR DEL ELEMENTOS OK
		var cuadrotext6=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var nodoTexto6 = document.createTextNode("Total Elementos Descargados OK");
		cuadrotext6.setAttribute("x",10);		
		cuadrotext6.setAttribute("y",this.altoElementos+96);	
		cuadrotext6.setAttribute('style',this.textTooltipb);
		cuadrotext6.appendChild(nodoTexto6);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext6);
	
		var cuadrotext7=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var nodoTexto7 = document.createTextNode(elementOk);
		cuadrotext7.setAttribute("x",this.anchoTotal-5);		
		cuadrotext7.setAttribute("y",this.altoElementos+96);	
		cuadrotext7.setAttribute('style',this.textTooltipb+'text-anchor: end');
		cuadrotext7.appendChild(nodoTexto7);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext7);

		//DIBUJO DE LA LÍNEA HORIZONTAL INFERIOR 6
		if(!document.getElementById("fin_7_"+this.nombreGrafico)){
			var line=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var line=document.getElementById("fin_7_"+this.nombreGrafico);
		}
		line.setAttribute("x1",0);
		line.setAttribute("x2",this.anchoTotal);
		line.setAttribute("y1",this.altoElementos+100);
		line.setAttribute("y2",this.altoElementos+100);	
		line.setAttribute("id","fin_7_"+this.nombreGrafico);
		line.setAttribute("style",'stroke:black;stroke-width:0.7;fill:#000000');
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(line);
	
		//IMPRIME EL VALOR DEL ELEMENTOS ERROR
		var cuadrotext7=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var nodoTexto7= document.createTextNode("Total Elementos Descargados con Error");
		cuadrotext7.setAttribute("x",10);		
		cuadrotext7.setAttribute("y",this.altoElementos+116);	
		cuadrotext7.setAttribute('style',this.textTooltipb);
		cuadrotext7.appendChild(nodoTexto7);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext7);
	
		var cuadrotext8=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var nodoTexto8 =  document.createTextNode(elementError);
		cuadrotext8.setAttribute("x",this.anchoTotal-5);		
		cuadrotext8.setAttribute("y",this.altoElementos+116);	
		cuadrotext8.setAttribute('style',this.textTooltipb+'text-anchor: end');
		cuadrotext8.appendChild(nodoTexto8);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext8);
                
                //DIBUJO DE LA LÍNEA HORIZONTAL INFERIOR 7
		if(!document.getElementById("fin_8_"+this.nombreGrafico)){
			var line=document.createElementNS("http://www.w3.org/2000/svg", "line");
		}
		else{
			var line=document.getElementById("fin_8_"+this.nombreGrafico);
		}
		line.setAttribute("x1",0);
		line.setAttribute("x2",this.anchoTotal);
		line.setAttribute("y1",this.altoElementos+120);
		line.setAttribute("y2",this.altoElementos+120);	
		line.setAttribute("id","fin_8_"+this.nombreGrafico);
		line.setAttribute("style",'stroke:black;stroke-width:0.7;fill:#000000');
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(line);
    }
	
	
	/*FUNCION QUE CREA EL EJE "X" CON LAS SEPARACIONES DE TIEMPO Y TITULOS DE CATEGORÍA */
	this.creaEjeX = function(){
		valor=this.tiempoTotal/this.divisiones;
		var i=0;
		//CREA LAS DIVISIONES POR TIEMPO UBICADAS EN LA PARTE SUPERIOR DE LA PANTALLA
		for(i=0;i<=this.divisiones;i++){
			//CARGA LOS VALORES		
			var cuadrotext1=document.createElementNS("http://www.w3.org/2000/svg",'text');
			var nodoTexto1 = document.createTextNode(parseFloat(valor*i).toFixed(2));
			cuadrotext1.setAttribute("x",this.anchoNombres+i*((this.anchoDibujable)/this.divisiones)+8);		
			cuadrotext1.setAttribute("y",35);	
			cuadrotext1.setAttribute("style",this.textTooltip);
			cuadrotext1.appendChild(nodoTexto1);		
			document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext1);
			//CARGA LAS LINEAS QUE SEPARAN LAS LINEAS DE TIEMPO
			var linea=document.createElementNS("http://www.w3.org/2000/svg", "line");
			linea.setAttribute("x1",this.anchoNombres+i*((this.anchoDibujable)/this.divisiones)+8);
			linea.setAttribute("x2",this.anchoNombres+i*((this.anchoDibujable)/this.divisiones)+8);
			linea.setAttribute("y1",40);
			linea.setAttribute("y2",this.alto+20);
			linea.setAttribute("style","fill:rgb(168,162,162); stroke:rgb(0,0,0); opacity:0.15");
			document.getElementById('svgelem_'+this.nombreGrafico).appendChild(linea);						
		}
		//CREA EL TITULO ELEMENTOS
		var cuadrotext2=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var nodoTexto2 = document.createTextNode('ELEMENTOS');
		cuadrotext2.setAttribute("x",5);		
		cuadrotext2.setAttribute("y",35);	
		cuadrotext2.setAttribute("style",this.textTooltipb);
		cuadrotext2.appendChild(nodoTexto2);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext2);
                //CREA EL TITULO ESTADO
		var cuadrotext3=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var nodoTexto3 = document.createTextNode('ESTADO');
		cuadrotext3.setAttribute("x",this.anchoNombres-30);		
		cuadrotext3.setAttribute("y",35);	
		cuadrotext3.setAttribute("style",this.textTooltipb);
		cuadrotext3.appendChild(nodoTexto3);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext3);	
		//CREA EL TITULO BYTES
		var cuadrotext4=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var nodoTexto4 = document.createTextNode('BYTES');
		cuadrotext4.setAttribute("x",this.anchoTotal-92);		
		cuadrotext4.setAttribute("y",35);	
		cuadrotext4.setAttribute("style",this.textTooltipb);
		cuadrotext4.appendChild(nodoTexto4);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext4);
		//CREA EL TITULO SEGUNDOS
		var cuadrotext5=document.createElementNS("http://www.w3.org/2000/svg",'text');
		var nodoTexto5 = document.createTextNode('SEGS');
		cuadrotext5.setAttribute("x",this.anchoTotal-43);		
		cuadrotext5.setAttribute("y",35);	
		cuadrotext5.setAttribute("style",this.textTooltipb);
		cuadrotext5.appendChild(nodoTexto5);		
		document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrotext5);
                
	}
	
	
	/*FUNCION QUE CREA EL EJE "Y" DIBUJANDO LAS LINEAS PLOMAS,
	 * CARGANDO SUS NOMBRES, ESTADO Y CAPTURANDO LA ALTURA DEL ULTIMO ELEMENTO DIBUJADO 
	 **/
	
	this.creaEjeY = function(indice){
		y=50;    
		//SI EL VALOR ES IMPAR DIBUJA UNA LINEA PLOMA DE FONDO
		if((indice+1)%2==0){
			var rectPlomo=document.createElementNS("http://www.w3.org/2000/svg", "rect");	
			rectPlomo.setAttribute("x",0);
			rectPlomo.setAttribute("y",parseInt(y-20+(indice*20)+8));
			rectPlomo.setAttribute("width",this.anchoTotal);
			rectPlomo.setAttribute("height",20);
			rectPlomo.setAttribute("id",'plomo_'+indice);
			rectPlomo.setAttribute("style","fill:rgb(168,162,162); stroke:rgb(0,0,0); opacity:0.15");
			document.getElementById('svgelem_'+this.nombreGrafico).appendChild(rectPlomo);
		}
			
		//ELIMINA TODOS LOS DATOS PASADOS EN LA URL 
		var tmpNombre=this.datos[indice][0].split('?');
		//SEPARA LAS SECCIONES DE LA URL DEJANDO EL ELEMENTO EN LA ULTIMA SECCION
		var nombre=tmpNombre[0].split('/');	
		
		if(nombre[nombre.length-1]=='')
			var nombre2=nombre[nombre.length-2];
		else
			var nombre2=nombre[nombre.length-1];
	
		if(nombre2.length!=0){
			if(nombre2.length>9){
				var nodoTexto = document.createTextNode(indice+"-"+nombre2.substring(0,8)+'...');
			}
			else{
				var nodoTexto = document.createTextNode(indice+"-"+nombre2.substring(0,9));
			}
		}
		else
			var nodoTexto = document.createTextNode(indice+"-"+'NO CAPTURADO');
	
		//CARGA EL NOMBRE DEL ELEMENTO
		var cuadrot=document.createElementNS("http://www.w3.org/2000/svg",'text');
               
		cuadrot.setAttribute("x",5);		
		cuadrot.setAttribute("y",parseInt(y+(indice*20)));	
		cuadrot.setAttribute("style",this.textTooltip);
		cuadrot.appendChild(nodoTexto);
//        	cuadrot.setAttribute("onmouseover","cuadroTooltip("+indice+","+parseFloat(5)+","+parseFloat(indice*20+50)+",true)");
		//cuadrot.setAttribute("class",'tiene_tooltip2');
                //variables para el evento onclick que despliega modal de elementos
                var elementoM = this.datos[indice][0];
                var ipM = this.datos[indice][1];
                
                var latenciaM = this.transformaSegundos(this.datos[indice][3]);;
                var descargaM = this.transformaSegundos(this.datos[indice][4]);
                var tamanoM = this.datos[indice][6];
                var estadoM = this.datos[indice][9];
                var dnsM = this.transformaSegundos(this.datos[indice][5]);
                var tipoM = this.datos[indice][8];
                var descripcionM = this.datos[indice][11];
                cuadrot.setAttribute("data-indice",indice);
		cuadrot.setAttribute("data-url",'true');
                cuadrot.setAttribute("onclick","ShowModal('"+indice+"','"+elementoM+"','"+tipoM+"','"+ipM+"','"+latenciaM+"','"+descargaM+"','"+tamanoM+"','"+estadoM+"','"+dnsM+"','"+descripcionM+"','"+nombre2+"');");
                cuadrot.setAttribute("style",this.textTooltip2);
               
                document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrot);
	        //CARGA EL ESTADO
		if(isNaN(this.datos[indice][9]))			
			var nodoTexto2 = document.createTextNode('NO CAPTURADO');
		else
			var nodoTexto2 = document.createTextNode(this.datos[indice][9]);
		
		var cuadrot2=document.createElementNS("http://www.w3.org/2000/svg",'text');

		if(this.datos[indice][10]!='true' && this.datos[indice][9]!=201){

			cuadrot2.setAttribute("fill",'red');
			}
		cuadrot2.setAttribute("x",this.anchoNombres-20);		
		cuadrot2.setAttribute("y",parseInt(y+(indice*20)));	
		cuadrot2.setAttribute("style",this.textTooltip);
		cuadrot2.appendChild(nodoTexto2);
//		cuadrot2.setAttribute("onmouseover","cuadroTooltip("+indice+","+parseFloat(5)+","+parseFloat(indice*20+50)+",true)");
		cuadrot2.setAttribute("class",'tiene_tooltip2');
		cuadrot2.setAttribute("data-indice",indice);
		cuadrot2.setAttribute("data-url",'true');
                document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrot2);
                
               
		//GUARDA EL ALTO DEL ULTIMO ELEMENTO
		this.altoElementos=parseFloat(y+(indice*20)+8);
		
	}
	
	/*FUNCIÓN QUE CREA LAS BARRAS DE LATENCIA Y DESCARGA*/
	this.creaBarra = function(indice){
		//SI LOS DATOS DE ESPERA, LATENCIA Y DESCARGA VIENEN EN BUEN ESTADO SE PROCEDE CON EL DIBUJO, DE LO CONTRARIO NO SE APARECERÁN ESAS BARRAS
		if(this.datos[indice][2].indexOf(':')!=-1 ){
			//CALCULA EL ANCHO DE LA BARRA EN RELACION AL TIEMPO TOTAL Y ANCHO DIBUJABLE
			var esperaTemp=parseFloat(this.anchoDibujable*parseFloat(this.transformaSegundos(this.datos[indice][2]))/this.tiempoTotal);
			if(this.datos[indice][3].indexOf(':')!=-1 ){
				var latenciaTemp=this.anchoDibujable*this.transformaSegundos(this.datos[indice][3])/this.tiempoTotal;
			}
			else{
				var latenciaTemp=1;
				}
			if(this.datos[indice][4].indexOf(':')!=-1 ){
				var descargaTemp=this.anchoDibujable*this.transformaSegundos(this.datos[indice][4])/this.tiempoTotal;
			}
			else{
				var descargaTemp=1;
				}
	
			//SI LOS VALORES DE LATENCIA Y DESCARGA SON MENORES A 1 PIXEL, SE DIBUJARÁN COMO 1 PIXEL	
			if(latenciaTemp<1)
				latenciaTemp=1;
			if(descargaTemp<1)
				descargaTemp=1;
	
			var esperaTotal=parseFloat(this.anchoNombres)+parseFloat(esperaTemp)+8;
			//CREA UN RECTANGULO DE CLASE CUADRO1 PARA GRAFICAR LA LATENCIA
			var rect1=document.createElementNS("http://www.w3.org/2000/svg", "rect");
			rect1.setAttribute("id","cuadro_rect_"+indice+"_1");
			rect1.setAttribute("rx","2");
			rect1.setAttribute("ry","2");
			rect1.setAttribute("x",esperaTotal.toFixed(3));
			rect1.setAttribute("y",indice*20+40);
			rect1.setAttribute("width",latenciaTemp);
			rect1.setAttribute("height",16);
			if(this.datos[indice][10]!='true'){
				rect1.setAttribute("style","cursor:pointer");			
				rect1.setAttribute("fill","#f7f7f7");
				rect1.setAttribute("opacity","0.3");
			}
			else{
				rect1.setAttribute("style","cursor:pointer");			
				rect1.setAttribute("fill",this.determinaColor(this.datos[indice][8]));
				rect1.setAttribute("opacity","0.3");
				}
			//rect1.setAttribute("onmouseover","cuadroTooltip("+indice+","+parseFloat(esperaTemp)+","+parseFloat(indice*20+40)+",false)");
			rect1.setAttribute("data-indice",indice);
			rect1.setAttribute("data-url",'false');
			rect1.setAttribute("class",'tiene_tooltip');
			document.getElementById('svgelem_'+this.nombreGrafico).appendChild(rect1);
	
			//CREA UN RECTANGULO DE CLASE CUADRO2 PARA GRAFICAR LA DESCARGA
			var rect2=document.createElementNS("http://www.w3.org/2000/svg", "rect");
			rect2.setAttribute("id","cuadro_rect_"+indice+"_2");
			rect2.setAttribute("rx","2");
			rect2.setAttribute("ry","2");
			rect2.setAttribute("x",this.anchoNombres+esperaTemp+latenciaTemp+8);
			rect2.setAttribute("y",indice*20+40);
			rect2.setAttribute("width",descargaTemp);
			rect2.setAttribute("height",16);
			
			if(this.datos[indice][10]!='true'){
				rect2.setAttribute("style","cursor:pointer");
				rect2.setAttribute("fill","#f7f7f7");
			}
			else{
				rect2.setAttribute("style","cursor:pointer");
				rect2.setAttribute("fill",this.determinaColor(this.datos[indice][8]));
			}
			//rect2.setAttribute("onmouseover","cuadroTooltip("+indice+","+parseFloat(esperaTemp+latenciaTemp)+","+parseFloat(indice*20+40)+",false)");
			rect2.setAttribute("data-indice",indice);
			rect2.setAttribute("data-url",'false');
			rect2.setAttribute("class",'tiene_tooltip');

			document.getElementById('svgelem_'+this.nombreGrafico).appendChild(rect2);			
		}
	}
	
	/*FUNCIÓN QUE LLENA LOS VALORES UBICADOS A LA DERECHA DE LA PANTALLA*/
	this.creaCeldasDerechas = function(indice){
		//Y TOMA EL VALOR 50 PARA UBICARSE BAJO EL TITULO
		y=50;
		//REVISA SI LOS VALORES SON CORRECTOS PARA PROCEDER A SUMARLOS
		if(this.datos[indice][7]!=-1 && !isNaN(this.datos[indice][7]) ){
			var tamanoTotal=parseFloat(this.datos[indice][6])+parseFloat(this.datos[indice][7]);
		}
		else{
			var tamanoTotal=parseFloat(this.datos[indice][6]);
		}
		//SI LOS VALORES SON CORRECTOS SE PROCEDE CON LA SUMA E IMPRESION
		if(!isNaN(tamanoTotal)){			
			var cuadrot=document.createElementNS("http://www.w3.org/2000/svg",'text');
			var nodoTexto = document.createTextNode(tamanoTotal);
                        cuadrot.setAttribute("x",this.anchoTotal-(this.anchoCeldasDerechas)-5);		
			cuadrot.setAttribute("y",parseInt(y+(indice*20)));			
			cuadrot.setAttribute("style",this.textTooltip+"text-anchor:end");		
			cuadrot.appendChild(nodoTexto);				
			document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrot);		
		}
	
		//SI LOS VALORES DE LATENCIA Y DESCARGA SON CORRECTOS SE SUMARÁN Y DIBUJARÁN
		if(this.datos[indice][3].indexOf(':')!=-1 && this.datos[indice][4].indexOf(':')!=-1){
			var tiempoDescarga=this.transformaSegundos(this.datos[indice][3])+this.transformaSegundos(this.datos[indice][4]);
			var cuadrot2=document.createElementNS("http://www.w3.org/2000/svg",'text');
			var nodoTexto2 = document.createTextNode(tiempoDescarga.toFixed(3));
			cuadrot2.setAttribute("x",this.anchoTotal-5);		
			cuadrot2.setAttribute("y",parseInt(y+(indice*20)));	
			cuadrot2.setAttribute("style",this.textTooltip+"text-anchor:end");			
			cuadrot2.appendChild(nodoTexto2);		
			document.getElementById('svgelem_'+this.nombreGrafico).appendChild(cuadrot2);	
			this.totalTiempo=parseFloat(this.totalTiempo)+parseFloat(tiempoDescarga.toFixed(3));
		}
	}
	
	
	/*FUNCION QUE TRANSFORMA EL TIEMPO DE FORMATO HH:MM:SS A SEGUNDOS CON MILISEGUNDOS*/
	this.transformaSegundos = function(tiempo){
		//SI VIENE EL FORMATO CORRECTO LO TRANSFORMA DE LO CONTRARIO RETORNA 0
		if(tiempo.indexOf(':')!=-1){
			var tiempoTemp=tiempo.split(':');
				tiempo=parseFloat(tiempoTemp[0]*3600)+parseFloat(tiempoTemp[1]*60)+parseFloat(tiempoTemp[2]);
		}
		else{
			tiempo=0;
			}
		return tiempo;
	}
	
	
	/*FUNCION QUE CREA EL TOOLTIP1
	 *ES LLAMADA AL UBICARSE SOBRE UNA BARRA O EL NOMBRE DE UN ELEMENTO, LOS PARAMETROS INDICAN EL ID DEL ELEMENTO,
	*EL VALOR DE "X" E "Y" DONDE SE ENCUENTRA Y SI DEBE O NO IMPRIMIR LA URL COMPLETA  
	 **/
	this.cuadroTooltip = function(indice,x,y,url){
		var espacio_nombres=95;	
		//REVISA SI HA SIDO CREADO EL TOOLTIP, DE HABERLO HECHO LLAMA POR ID A LOS OBJETOS CREADOS, ASÍ EVITA QUE SE ACOPLEN CON LOS YA CREADOS
		//LOS OBJETOS DE id TERMINADAS EN 1 CONTIENEN LOS NOMBRES DE LA INFORMACION A MOSTRAR EJ: "NOMBRE ELEMENTO". lOS DE id TERMINADA EN 2 MUESTRA EN CONTENIDO EJ: LOGO.PNG
	    if(document.getElementById("cuadro_tooltip_"+this.nombreGrafico)){
			var rect=document.getElementById("cuadro_tooltip_"+this.nombreGrafico);		
			var elem1=document.getElementById("elem_1_"+this.nombreGrafico);
			this.borrarChildNodes(elem1);
			var elem2=document.getElementById("elem_2_"+this.nombreGrafico);
			this.borrarChildNodes(elem2);
			var ip1=document.getElementById("ip_1_"+this.nombreGrafico);
			this.borrarChildNodes(ip1);
			var ip2=document.getElementById("ip_2_"+this.nombreGrafico);
			this.borrarChildNodes(ip2);
	
			var latencia1=document.getElementById("latencia_1_"+this.nombreGrafico);
			this.borrarChildNodes(latencia1);
			var latencia2=document.getElementById("latencia_2_"+this.nombreGrafico);
			this.borrarChildNodes(latencia2);
			var descarga1=document.getElementById("descarga_1_"+this.nombreGrafico);
			this.borrarChildNodes(descarga1);
			var descarga2=document.getElementById("descarga_2_"+this.nombreGrafico);
			this.borrarChildNodes(descarga2);
			var tamanoB1=document.getElementById("tamano_b_1_"+this.nombreGrafico);
			this.borrarChildNodes(tamanoB1);
			var tamanoB2=document.getElementById("tamano_b_2_"+this.nombreGrafico);
			this.borrarChildNodes(tamanoB2);
			var estado1=document.getElementById("estado_1_"+this.nombreGrafico);
			this.borrarChildNodes(estado1);
			var estado2=document.getElementById("estado_2_"+this.nombreGrafico);
			this.borrarChildNodes(estado2);
			var dns1=document.getElementById("dns_1_"+this.nombreGrafico);
			this.borrarChildNodes(dns1);
			var dns2=document.getElementById("dns_2_"+this.nombreGrafico);
			this.borrarChildNodes(dns2);
	
		}
		else{
			//LOS OBJETOS NO EXISTEN ASÍ QUE SE CREARÁNS
			var grupo=document.createElementNS("http://www.w3.org/2000/svg", "g");
			grupo.setAttribute("id","cuadro_"+this.nombreGrafico);
			document.getElementById('svgelem_'+this.nombreGrafico).appendChild(grupo);
			
			var rect=document.createElementNS("http://www.w3.org/2000/svg", "rect");
			rect.setAttribute("id","cuadro_tooltip_"+this.nombreGrafico);		
			rect.setAttribute("rx","6");
			rect.setAttribute("ry","12");
			rect.setAttribute("style","fill:#FFFFFF; stroke:#000000");
					
			var elem1=document.createElementNS("http://www.w3.org/2000/svg", "text");
			elem1.setAttribute("id","elem_1_"+this.nombreGrafico);
			elem1.setAttribute("style",this.textTooltipb);
			
			var elem2=document.createElementNS("http://www.w3.org/2000/svg", "text");
			elem2.setAttribute("id","elem_2_"+this.nombreGrafico);
			elem2.setAttribute("style",this.textTooltip);
			
			var ip1=document.createElementNS("http://www.w3.org/2000/svg", "text");
			ip1.setAttribute("id","ip_1_"+this.nombreGrafico);
			ip1.setAttribute("style",this.textTooltipb);
			
			var ip2=document.createElementNS("http://www.w3.org/2000/svg", "text");
			ip2.setAttribute("id","ip_2_"+this.nombreGrafico);
			ip2.setAttribute("style",this.textTooltip);
	/*		
			var espera1=document.createElementNS("http://www.w3.org/2000/svg", "text");
			espera1.setAttribute("id","espera_1");
			espera1.setAttribute("class","textTooltipb 7");
			
			var espera2=document.createElementNS("http://www.w3.org/2000/svg", "text");
			espera2.setAttribute("id","espera_2");
			espera2.setAttribute("class","textTooltip 7");
	*/	
	
			var latencia1=document.createElementNS("http://www.w3.org/2000/svg", "text");
			latencia1.setAttribute("id","latencia_1_"+this.nombreGrafico);
			latencia1.setAttribute("style",this.textTooltipb);
			
			var latencia2=document.createElementNS("http://www.w3.org/2000/svg", "text");
			latencia2.setAttribute("id","latencia_2_"+this.nombreGrafico);
			latencia2.setAttribute("style",this.textTooltip);
			
			var descarga1=document.createElementNS("http://www.w3.org/2000/svg", "text");
			descarga1.setAttribute("id","descarga_1_"+this.nombreGrafico);
			descarga1.setAttribute("style",this.textTooltipb);
			
			var descarga2=document.createElementNS("http://www.w3.org/2000/svg", "text");
			descarga2.setAttribute("id","descarga_2_"+this.nombreGrafico);
			descarga2.setAttribute("style",this.textTooltip);
			
			var dns1=document.createElementNS("http://www.w3.org/2000/svg", "text");
			dns1.setAttribute("id","dns_1_"+this.nombreGrafico);
			dns1.setAttribute("style",this.textTooltipb);
			
			var dns2=document.createElementNS("http://www.w3.org/2000/svg", "text");
			dns2.setAttribute("id","dns_2_"+this.nombreGrafico);
			dns2.setAttribute("style",this.textTooltip);		
	/*		
			var tamanoH1=document.createElementNS("http://www.w3.org/2000/svg", "text");
			tamanoH1.setAttribute("id","tamano_h_1");
			tamanoH1.setAttribute("class","textTooltipb 7");
			
			var tamanoH2=document.createElementNS("http://www.w3.org/2000/svg", "text");
			tamanoH2.setAttribute("id","tamano_h_2");
			tamanoH2.setAttribute("class","textTooltip 7");
	*/
			var tamanoB1=document.createElementNS("http://www.w3.org/2000/svg", "text");
			tamanoB1.setAttribute("id","tamano_b_1_"+this.nombreGrafico);
			tamanoB1.setAttribute("style",this.textTooltipb);
			
			var tamanoB2=document.createElementNS("http://www.w3.org/2000/svg", "text");
			tamanoB2.setAttribute("id","tamano_b_2_"+this.nombreGrafico);
			tamanoB2.setAttribute("style",this.textTooltip);
			
			var estado1=document.createElementNS("http://www.w3.org/2000/svg", "text");
			estado1.setAttribute("id","estado_1_"+this.nombreGrafico);
			estado1.setAttribute("style",this.textTooltipb);
			
			var estado2=document.createElementNS("http://www.w3.org/2000/svg", "text");
			estado2.setAttribute("id","estado_2_"+this.nombreGrafico);
			estado2.setAttribute("style",this.textTooltip);		
		}
	
		//docdocument.getElementById('cuadro_2'+this.nombreGrafico).appendChild('hola');ument.getElementById('cuadro_2'+this.nombreGrafico).appendChild('hola');CALCULA EL VALOR DE "Y" PARA EVITAR QUE EL TOOLTIP SE CORTE CUANDO REVISA UN ELEMENTO UBICADO EN LA PARTE INFERIOR DEL GRÁFICO SVG
		if(y+(8*15)>document.getElementById("contenedor_"+this.nombreGrafico).clientHeight)
			y=y-((y+(8*15))-document.getElementById("contenedor_"+this.nombreGrafico).clientHeight)-5;
	
	
		//CARGA DEL TEXTO PARA LOS OBJETOS DE ID TERMINADA EN 1
		var nodoElem1 = document.createTextNode('ELEMENTO: ');
		var nodoIp1 = document.createTextNode('IP: ');
	//	var nodoEspera1 = document.createTextNode('TIEMPO PRETRANSFERENCIA: ');
		var nodoLatencia1 = document.createTextNode('LATENCIA: ');
		var nodoDescarga1 = document.createTextNode('DESCARGA: ');
	//	var nodoTamanoH1 = document.createTextNode('Tamaño Header: ');
		var nodoTamanoB1 = document.createTextNode('TAMAÑO: ');
		var nodoEstado1 = document.createTextNode('ESTADO: ');
		var nodoDns1 = document.createTextNode('DNS: ');
		
		elem1.appendChild(nodoElem1);
		ip1.appendChild(nodoIp1);
	//	espera1.appendChild(nodoEspera1);
		latencia1.appendChild(nodoLatencia1);
		descarga1.appendChild(nodoDescarga1);	
		dns1.appendChild(nodoDns1);
		//tamanoH1.appendChild(nodoTamanoH1);		
		tamanoB1.appendChild(nodoTamanoB1);			
		estado1.appendChild(nodoEstado1);			
	
		//CARGA DE LOS VALORES PARA LOS OBJETOS DE ID TERMINADOS EN 2	
		var tmpNombre=this.datos[indice][0].split('?');
		if(url==false){//si no debe cargar la URL completa la dividesu contenido y muestra solo la ultima parte
			var nombre=tmpNombre[0].split('/');
			if(nombre[nombre.length-1]=='')
				var nombre2=this.datos[indice][0].split('?')[0];
			else
				var nombre2=nombre[nombre.length-1];
			elem2.setAttribute('onclick','');
			elem2.setAttribute('text-decoration','none');
		}
		else{//muestra la URL completa y agrega una función window.open para cargar el elemento en un popup
			var nombre2=tmpNombre[0];
			if(this.determinaColor(this.datos[indice][8])!='plomo'){			
				elem2.setAttribute('onclick',"window.open('"+this.datos[indice][0]+"','Elementos Plus','width=700','height=300',top=200,left=300)");		
				elem2.setAttribute('text-decoration','underline');
			}
			else{
				elem2.setAttribute('onclick','');
				elem2.setAttribute('text-decoration','none');
				}
		}
	
		var nodoElem2 = document.createTextNode(nombre2);
		if(this.datos[indice][1].indexOf('.')==-1){
			var nodoIp2 = document.createTextNode('NO CAPTURADO');
		}
		else{
			var nodoIp2 = document.createTextNode(this.datos[indice][1]);
		}
	/*	if(datos[indice][2].indexOf(':')==-1){
			var nodoEspera2 = document.createTextNode('NO CAPTURADO');
		}
		else{
			var nodoEspera2 = document.createTextNode(transformaSegundos(datos[indice][2])+' Segs');
		}
	*/	
		if(this.datos[indice][3].indexOf(':')==1){
			var nodoLatencia2 = document.createTextNode('NO CAPTURADO');
		}
		else{
			var nodoLatencia2 = document.createTextNode(this.transformaSegundos(this.datos[indice][3])+' Segs');
		}	
	
		if(this.datos[indice][4].indexOf(':')==-1){
			var nodoDescarga2 = document.createTextNode('NO CAPTURADO');
		}
		else{
			var nodoDescarga2 = document.createTextNode(this.transformaSegundos(this.datos[indice][4])+' Segs');
		}
	
		if(parseFloat(this.datos[indice][5])<0 ){
			var nodoDns2 = document.createTextNode('NO CAPTURADO');
		}
		else{
			var nodoDns2 = document.createTextNode((parseFloat(this.datos[indice][5])/1000).toFixed(3)+' Segs');
		}	
		
		
	/*
		if(isNaN(datos[indice][7]) || parseInt(datos[indice][7])==-1){
			var nodoTamanoH2 = document.createTextNode('NO CAPTURADO');
		}
		else{
			var nodoTamanoH2 = document.createTextNode(datos[indice][7]+' Bytes');
		}
	*/	
		if(isNaN(this.datos[indice][6])){
			var nodoTamanoB2 = document.createTextNode('NO CAPTURADO');
		}
		else{
			var nodoTamanoB2 = document.createTextNode(this.datos[indice][6]+' bytes');
		}
	
		if(this.datos[indice][9]==-1 || this.datos[indice][9]=='NULL'){
			var nodoEstado2 = document.createTextNode('NO CAPTURADO');
		}
		else{
			var nodoEstado2 = document.createTextNode(this.datos[indice][9]);
		}

		if(this.datos[indice][10]!='true' && this.datos[indice][9]!=201){

			elem2.setAttribute('fill','red');
			ip2.setAttribute('fill','red');
	//		espera2.setAttribute('fill','red');
			latencia2.setAttribute('fill','red');
			descarga2.setAttribute('fill','red');
	//		tamanoH2.setAttribute('fill','red');
			tamanoB2.setAttribute('fill','red');
			estado2.setAttribute('fill','red');
			dns2.setAttribute('fill','red');	
		}
		else{
			elem2.setAttribute('fill','black');
			ip2.setAttribute('fill','black');
	//		espera2.setAttribute('fill','black');
			latencia2.setAttribute('fill','black');
			descarga2.setAttribute('fill','black');
	//		tamanoH2.setAttribute('fill','black');
			tamanoB2.setAttribute('fill','black');
			estado2.setAttribute('fill','black');
                        estado1.setAttribute('fill','black');
			dns2.setAttribute('fill','black');
			}
	
		
		elem2.appendChild(nodoElem2);
		ip2.appendChild(nodoIp2);
	//	espera2.appendChild(nodoEspera2);
		latencia2.appendChild(nodoLatencia2);
		descarga2.appendChild(nodoDescarga2);
		dns2.appendChild(nodoDns2);
	//	tamanoH2.appendChild(nodoTamanoH2);		
		tamanoB2.appendChild(nodoTamanoB2);			
		estado2.appendChild(nodoEstado2);
	
		
		if(x+(espacio_nombres+(nombre2.length)*7)>document.getElementById("contenedor_"+this.nombreGrafico).clientWidth){
			x=x-(x+espacio_nombres+(nombre2.length)*7-document.getElementById("contenedor_"+this.nombreGrafico).clientWidth-5);
			}
		if(x<0)
			x=0;
		//CAMBIA LOS VALORES DE LOS OBJETOS DEPENDIENDO DE LOS VALORES "X" E "Y" RECIBIDOS POR PARAMETROS
		rect.setAttribute("y",parseInt(y));
		rect.setAttribute("x",parseInt(x));
		yTexto = y+10;
		
		elem1.setAttribute("x",parseInt(x+5));
		elem1.setAttribute("y",parseInt(yTexto));	
		elem2.setAttribute("x",parseInt(x+5+espacio_nombres));
		elem2.setAttribute("y",parseInt(yTexto));
		
		ip1.setAttribute("x",parseInt(x+5));
		ip1.setAttribute("y",parseInt(yTexto+15));	
		ip2.setAttribute("x",parseInt(x+5+espacio_nombres));
		ip2.setAttribute("y",parseInt(yTexto+15));
	/*	
		espera1.setAttribute("x",parseInt(x+5));
		espera1.setAttribute("y",parseInt(yTexto+30));	
		espera2.setAttribute("x",parseInt(x+5+espacio_nombres));
		espera2.setAttribute("y",parseInt(yTexto+30));
	*/
		latencia1.setAttribute("x",parseInt(x+5));
		latencia1.setAttribute("y",parseInt(yTexto+30));	
		latencia2.setAttribute("x",parseInt(x+5+espacio_nombres));
		latencia2.setAttribute("y",parseInt(yTexto+30));
		
		descarga1.setAttribute("x",parseInt(x+5));
		descarga1.setAttribute("y",parseInt(yTexto+45));
		descarga2.setAttribute("x",parseInt(x+5+espacio_nombres));
		descarga2.setAttribute("y",parseInt(yTexto+45));
	
		/*
		tamanoH1.setAttribute("x",parseInt(x+5));
		tamanoH1.setAttribute("y",parseInt(yTexto+75));
		tamanoH2.setAttribute("x",parseInt(x+5+espacio_nombres));
		tamanoH2.setAttribute("y",parseInt(yTexto+75));
		*/
		
		tamanoB1.setAttribute("x",parseInt(x+5));
		tamanoB1.setAttribute("y",parseInt(yTexto+60));
		tamanoB2.setAttribute("x",parseInt(x+5+espacio_nombres));
		tamanoB2.setAttribute("y",parseInt(yTexto+60));
		
		estado1.setAttribute("x",parseInt(x+5));
		estado1.setAttribute("y",parseInt(yTexto+75));
		estado2.setAttribute("x",parseInt(x+5+espacio_nombres));
		estado2.setAttribute("y",parseInt(yTexto+75));
		
		dns1.setAttribute("x",parseInt(x+5));
		dns1.setAttribute("y",parseInt(yTexto+90));
		dns2.setAttribute("x",parseInt(x+5+espacio_nombres));
		dns2.setAttribute("y",parseInt(yTexto+90));
		
	
		
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(rect);								
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(elem1);		
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(elem2);
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(ip1);
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(ip2);
	//	document.getElementById('cuadro').appendChild(espera1);
	//	document.getElementById('cuadro').appendChild(espera2);	
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(latencia1);
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(latencia2);
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(descarga1);
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(descarga2);
		//document.getElementById('cuadro').appendChild(tamanoH1);
		//document.getElementById('cuadro').appendChild(tamanoH2);
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(tamanoB1);
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(tamanoB2);
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(estado1);
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(estado2);
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(dns1);
		document.getElementById('cuadro_'+this.nombreGrafico).appendChild(dns2);
		
		
		rect.setAttribute("height",7*15);
		if(nombre2.length<15){
			var	caracteres=15;		
		}
		else{
			var caracteres=nombre2.length;
		}
		
		rect.setAttribute("width",espacio_nombres+6*caracteres);	
		document.getElementById('cuadro_'+this.nombreGrafico).style.visibility="visible";
		}
        /*FUNCION QUE CREA EL TOOLTIP2
	 *ES LLAMADA AL UBICARSE SOBRE EL ELEMENTO ESTADO, LOS PARAMETROS INDICAN EL ESTADO Y SU DESCRIPCION,
	*EL VALOR DE "X" E "Y" DONDE SE ENCUENTRA
	 **/
	this.cuadroTooltip2 = function(indice,x,y,url){
		var espacio_nombres=70;	
		//REVISA SI HA SIDO CREADO EL TOOLTIP, DE HABERLO HECHO LLAMA POR ID A LOS OBJETOS CREADOS, ASÍ EVITA QUE SE ACOPLEN CON LOS YA CREADOS
		//LOS OBJETOS DE id TERMINADAS EN 1 CONTIENEN LOS NOMBRES DE LA INFORMACION A MOSTRAR EJ: "NOMBRE ELEMENTO". lOS DE id TERMINADA EN 2 MUESTRA EN CONTENIDO EJ: LOGO.PNG
		if(document.getElementById("cuadro_tooltip_2"+this.nombreGrafico)){
			var rect=document.getElementById("cuadro_tooltip_2"+this.nombreGrafico);		
			var estado1=document.getElementById("estado_1_"+this.nombreGrafico);
			this.borrarChildNodes(estado1);
			var estado2=document.getElementById("estado_2_"+this.nombreGrafico);
			this.borrarChildNodes(estado2);
                        var descripcion1=document.getElementById("descripcion_1_"+this.nombreGrafico);
			this.borrarChildNodes(descripcion1);
			var descripcion2=document.getElementById("descripcion_2_"+this.nombreGrafico);
			this.borrarChildNodes(descripcion2);
		}
		else{
			//LOS OBJETOS NO EXISTEN ASÍ QUE SE CREARÁNS
			var grupo=document.createElementNS("http://www.w3.org/2000/svg", "g");
			grupo.setAttribute("id","cuadro_2"+this.nombreGrafico);
			document.getElementById('svgelem_'+this.nombreGrafico).appendChild(grupo);
			
			var rect=document.createElementNS("http://www.w3.org/2000/svg", "rect");
			rect.setAttribute("id","cuadro_tooltip_2"+this.nombreGrafico);		
			rect.setAttribute("rx","6");
			rect.setAttribute("ry","12");
			rect.setAttribute("style","fill:#FFFFFF; stroke:#000000");
						
			var estado1=document.createElementNS("http://www.w3.org/2000/svg", "text");
			estado1.setAttribute("id","estado_1_"+this.nombreGrafico);
			estado1.setAttribute("style",this.textTooltipb);
			
			var estado2=document.createElementNS("http://www.w3.org/2000/svg", "text");
			estado2.setAttribute("id","estado_2_"+this.nombreGrafico);
			estado2.setAttribute("style",this.textTooltip);	
                        
                        var descripcion1=document.createElementNS("http://www.w3.org/2000/svg", "text");
			descripcion1.setAttribute("id","descripcion_1_"+this.nombreGrafico);
			descripcion1.setAttribute("style",this.textTooltipb);
			
			var descripcion2=document.createElementNS("http://www.w3.org/2000/svg", "text");
			descripcion2.setAttribute("id","descripcion_2_"+this.nombreGrafico);
			descripcion2.setAttribute("style",this.textTooltip);		
		}
	       
		//CALCULA EL VALOR DE "Y" PARA EVITAR QUE EL TOOLTIP SE CORTE CUANDO REVISA UN ELEMENTO UBICADO EN LA PARTE INFERIOR DEL GRÁFICO SVG
		if(y+(8*15)>document.getElementById("contenedor_"+this.nombreGrafico).clientHeight)
			y=y-((y+(8*15))-document.getElementById("contenedor_"+this.nombreGrafico).clientHeight)-5;
	       	
		//CARGA DEL TEXTO PARA LOS OBJETOS DE ID TERMINADA EN 1
		var nodoEstado1 = document.createTextNode('Codigo:');
		estado1.appendChild(nodoEstado1);	
                
                var nodoDescripcion1 = document.createTextNode('Descripcion: ');
		descripcion1.appendChild(nodoDescripcion1);
	
		//CARGA DE LOS VALORES PARA LOS OBJETOS DE ID TERMINADOS EN 2	
		var tmpNombre=this.datos[indice][0].split('?');
		if(url==false){//si no debe cargar la URL completa la dividesu contenido y muestra solo la ultima parte
			var nombre=tmpNombre[0].split('/');
			if(nombre[nombre.length-1]=='')
				var nombre2=this.datos[indice][0].split('?')[0];
			else
				var nombre2=nombre[nombre.length-1];
		}
		else{//muestra la URL completa y agrega una función window.open para cargar el elemento en un popup
			var nombre2=tmpNombre[0];
			
		}
	    if(this.datos[indice][9]==-1 || this.datos[indice][9]=='NULL'){
			var nodoEstado2 = document.createTextNode('NO CAPTURADO');
		}
		else{
			var nodoEstado2 = document.createTextNode(this.datos[indice][9]);
		}
            if(this.datos[indice][11]=='' || this.datos[indice][9]=='NULL'){
			var nodoDescripcion2 = document.createTextNode('NO REGISTRADO');
		}
		else{
			var nodoDescripcion2 = document.createTextNode(this.datos[indice][11]);
		}

	    if(this.datos[indice][10]!='true' && this.datos[indice][9]!=201){

			
			estado2.setAttribute('fill','red');
                        descripcion2.setAttribute('fill','red');
                        estado1.setAttribute('fill','red');
                        descripcion1.setAttribute('fill','red');
	    }
		else{
			estado2.setAttribute('fill','black');
                        descripcion2.setAttribute('fill','black');
                        estado1.setAttribute('fill','black');
                        descripcion1.setAttribute('fill','black');
			
			
			}
	
		estado2.appendChild(nodoEstado2);
                descripcion2.appendChild(nodoDescripcion2);
	
		
		if(x+(espacio_nombres+(nombre2.length)*7)>document.getElementById("contenedor_"+this.nombreGrafico).clientWidth){
			x=x-(x+espacio_nombres+(nombre2.length)*7-document.getElementById("contenedor_"+this.nombreGrafico).clientWidth-5);
			}
		if(x<0)
			x=0;
		//CAMBIA LOS VALORES DE LOS OBJETOS DEPENDIENDO DE LOS VALORES "X" E "Y" RECIBIDOS POR PARAMETROS
		rect.setAttribute("y",parseInt(y));
		rect.setAttribute("x",parseInt(x));
		yTexto = y+10;
		
		estado1.setAttribute("x",parseInt(x+5));
		estado1.setAttribute("y",parseInt(yTexto));
		estado2.setAttribute("x",parseInt(x+5+espacio_nombres));
		estado2.setAttribute("y",parseInt(yTexto));
                
                descripcion1.setAttribute("x",parseInt(x+5));
		descripcion1.setAttribute("y",parseInt(yTexto+15));
		descripcion2.setAttribute("x",parseInt(x+8+espacio_nombres));
		descripcion2.setAttribute("y",parseInt(yTexto+15));
		
		document.getElementById('cuadro_2'+this.nombreGrafico).appendChild(rect);								
		document.getElementById('cuadro_2'+this.nombreGrafico).appendChild(estado1);
		document.getElementById('cuadro_2'+this.nombreGrafico).appendChild(estado2);
                document.getElementById('cuadro_2'+this.nombreGrafico).appendChild(descripcion1);
		document.getElementById('cuadro_2'+this.nombreGrafico).appendChild(descripcion2);
		
		rect.setAttribute("height",7*5);
		if(nombre2.length<15){
			var	caracteres=15;		
		}
		else{
			var caracteres=nombre2.length;
		}
		var ancho=this.datos[indice][11];
                var cantidad=ancho.length;
		rect.setAttribute("width",espacio_nombres+6*cantidad+20);	
		document.getElementById('cuadro_2'+this.nombreGrafico).style.visibility="visible";
        }
	this.borraCuadro = function(){	
		this.borrarChildNodes();
		document.getElementById('cuadro_'+this.nombreGrafico).style.visibility="hidden";
                document.getElementById('cuadro_2'+this.nombreGrafico).style.visibility="hidden";
		}
	
	this.borrarChildNodes = function(elemento){
		 if(elemento){
			 if ( elemento.hasChildNodes() )
			 {
			     while ( elemento.childNodes.length >= 1 )
			     {
			    	 elemento.removeChild( elemento.firstChild );       
			     } 
			 }
		 }
	 }
	
	this.cuadroTexto = function(indice,x,y){
		texto=document.createTextNode(this.elementos[indice]);
		document.getElementById('tspanTexto').removeChild(document.getElementById('tspanTexto').childNodes[0]);
		document.getElementById('tspanTexto').appendChild(texto);
		document.getElementById('cuadroTexto').x=parseInt(x);		
		document.getElementById('cuadroTexto').y=parseInt(y);
		document.getElementById('cuadroTexto-rect').setAttribute("x",parseInt(x));		
		document.getElementById('cuadroTexto-rect').setAttribute("y",parseInt(y));
		document.getElementById('cuadroTexto-text').setAttribute("x",parseInt(x)+5);		
		document.getElementById('cuadroTexto-text').setAttribute("y",parseInt(y)+15);
		
		document.getElementById('cuadroTexto_'+this.nombreGrafico).style.visibility="visible";
	}
	
	this.borraCuadroTexto = function(){
		if(document.getElementById('cuadro_'+this.nombreGrafico)){
			document.getElementById('cuadro_'+this.nombreGrafico).style.visibility="hidden";
		}
                if(document.getElementById('cuadro_2'+this.nombreGrafico)){
			document.getElementById('cuadro_2'+this.nombreGrafico).style.visibility="hidden";
		}
	}	 
       
	this.determinaColor = function(tipo){
		if(tipo.toUpperCase().indexOf('HTML')!=-1) {
			return '#3561CE'; 					
		}
		else if(tipo.toUpperCase().indexOf('JAVASCRIPT')!=-1){
			return '#f4e74f';
		}
		else if(tipo.toUpperCase().indexOf('TEXT/JAVASCRIPT')!=-1){
			return '#ec7911';
		}
		else if(tipo.toUpperCase().indexOf('audio')!=-1 || tipo.toUpperCase().indexOf('x-music')!=-1 || tipo.toUpperCase().indexOf('x-music')!=-1){
			return '#f7f7f7';
		}		
		else if(tipo.toUpperCase().indexOf('IMAGE')!=-1 || tipo.toUpperCase().indexOf('vector/x-svf')!=-1){
			return '#a64ef4';
		}		
		else if(tipo.toUpperCase().indexOf('CSS')!=-1 || tipo.toUpperCase().indexOf('dsssl')!=-1){
			return '#8aa817';
		}
		else{
			return '#6e6e6e';
		}
	};
        this.noData = function(id){
            var svg = document.getElementById("svgelem_"+id);
            var fondo = document.getElementById("fondo");
            var img = document.createElementNS('http://www.w3.org/2000/svg','image');
            var cuadrotext0 = document.createElementNS("http://www.w3.org/2000/svg",'text');
            var nodoTexto0 = document.createTextNode("No se encontraron datos para este paso.");
            
            svg.setAttribute("height",50);
            fondo.setAttribute("height",50);
            
            img.setAttributeNS(null,'height','15');
            img.setAttributeNS(null,'width','15');
            img.setAttributeNS('http://www.w3.org/1999/xlink','href', 'img/advertencia.png');
            img.setAttributeNS(null,'x','240');
            img.setAttributeNS(null,'y','23');
            img.setAttributeNS(null, 'visibility', 'visible');
            
            cuadrotext0.setAttribute("x",260);		
            cuadrotext0.setAttribute("y",36);
            cuadrotext0.setAttribute("id",'txt'+id);
            cuadrotext0.setAttribute("fill", "#525252");
            cuadrotext0.setAttribute("class", "textgris12");
            cuadrotext0.appendChild(nodoTexto0);
            
            svg.appendChild(img);
            svg.appendChild(cuadrotext0);
	};           
        
}
