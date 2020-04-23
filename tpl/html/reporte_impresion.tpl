<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=9"/>	
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Atentus.com: Reportes</title>
		
		<link rel="stylesheet" href="css/disponibilidad.css" type="text/css"/>
		<link rel="stylesheet" href="css/textos-reporte.css" type="text/css"/>
		<link rel="stylesheet" href="css/reportes_print.css" type="text/css"/>
		<link rel="stylesheet" href="css/especiales.css" type="text/css"/>
		<style type="text/css">
			@import "{__path_dojo}dijit/themes/nihilo/nihilo.css";
		</style>

		<script type="text/javascript" src="{__path_jquery_ui}js/jquery-1.7.1.min.js"></script>		
		<script type="text/javascript" src="{__path_jquery_ui}js/jquery.dataTables.js"></script>
		
 		
 		<script type="text/javascript" src="{__path_js}flash_detect.js"></script>
 		<script type="text/javascript" src="{__path_js}disponibilidad.js"></script>
		<script type="text/javascript" src="{__path_js}reportes.js"></script>
		<script type="text/javascript" language="javascript" src="{__path_anychart}"></script>
		<script type="text/javascript" src="{__path_dojo}dojo/dojo.js" djConfig="parseOnLoad:true"></script>

		<script type="text/javascript" src="tools/highcharts/highcharts.js"></script>
		<script type="text/javascript" src="tools/highcharts/modules/exporting.js"></script>
 		<script type="text/javascript" src="{__path_js}leyenda_svg.js"></script>

		<script type="text/JavaScript">
			dojo.require("dijit.Dialog");
			dojo.require("dijit.ProgressBar");
			dojo.require("dojox.layout.ContentPane");
			dojo.require("dojo.parser");

			
            // Detecta flash.
			if (FlashDetect.installed) {
            	tiene_flash = 1;
			}
			else {
				tiene_flash = 0;
			}
			
			// Detecta SVG.
			if (document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1") == true) {
				tiene_svg = 1;
			}
			else {
				tiene_svg = 0;
			}
			
			
			function cuadroSeleccion() {
				var cuadro = '<table width="250">'+
							 '<tr>'+
							 '<td align="center"><img src="img/botones/imprimir.png" '+
							 'onclick="dialog.hide(); setTimeout(\'window.print()\', 1000);"></td>'+
							 '<td align="center"><img src="img/botones/pdf.png" '+
							 'onclick="location.href=\'descargar_pdf.php?es_pdf=true&sitio_id={__sitio_id}&menu_id={__menu_id}&objeto_id={__objeto_id}&reporte_id={__reporte_id}&{__elemento_plus}&tiene_svg='+tiene_svg+'&tiene_flash='+tiene_flash+'\'"><td>'+
							 '</tr>'+
							 '<tr>'+
							 '<td align="center" class="textgris10">Imprimir Informe</td>'+
							 '<td align="center" class="textgris10">Exportar a PDF</td>'+
							 '</tr>'+
							 '</table>';
				return cuadro;
			}

			/* DATOS GRAFICOS ANYCHART */
			var xmls_chart = new Array();
			var cnt_draw_chart = new Array();
			var indice_chart = new Array();
			var tamanno_chart = new Array();

			<!-- BEGIN LISTA_GRAFICOS_CHART -->
			xmls_chart[{__grafico_indice_chart}] = '{__grafico_xml}';
			cnt_draw_chart[{__grafico_indice_chart}] = '{__grafico_cnt_draw}';
			indice_chart[{__grafico_indice_chart}] = '{__grafico_indice}';
			tamanno_chart[{__grafico_indice_chart}] = '{__grafico_tamanno}';
			<!-- END LISTA_GRAFICOS_CHART -->

			/* DATOS GRAFICOS ANYGANTT */
			var xmls_gantt = new Array();
			var indice_gantt = new Array();
			var tamanno_gantt = new Array();

			<!-- BEGIN LISTA_GRAFICOS_GANTT -->
			xmls_gantt[{__grafico_indice_gantt}] = '{__grafico_xml}';
			indice_gantt[{__grafico_indice_gantt}] = '{__grafico_indice}';
			tamanno_gantt[{__grafico_indice_gantt}] = '{__grafico_tamanno}';
			<!-- END LISTA_GRAFICOS_GANTT -->
		</script>
		<script type="text/javascript">
			function CargaPagina(){
			document.getElementById("cargapagina").style.visibility = "visible";
			document.getElementById("cargando").style.visibility = "hidden";
			}
		</script>
	</head>
	
	<body class="nihilo", onload="CargaPagina();">
	<div id="cargando">
		<table width="700" align="center">
			<tr>
				<td width="30" align="center"><img src="../img/cargando.gif"></td>
				<td class="textgris12">Por favor espere.<br>El reporte se esta generando.</td>
			</tr>
		</table>
	 	<div id="cargapagina" style="visibility:hidden;">
			<tr>
				<td style="padding: 4px 4px 4px 20px;"><i class="spriteImg spriteImg-header"></i></td>
			</tr>
			<tr>
				<td>
					<table width="100%">
						<tr>
							<td height="40" style="padding: 4px 4px 4px 20px; background-color:#f47001;">
								<span class="txtBlanco15b">{__reporte_titulo}</span><br>
								<span class="txtBlanco13b">{__objetivo_nombre}</span>
							</td>
							<td style="padding: 4px; background-color:#626262;" class="txtBlanco13b">{__fecha_inicio} al {__fecha_termino}</td>
						</tr>
						<tr>
							<td height="4" style="background-color: #f47001;"></td>
							<td height="4" style="background-color: #f47001;"></td>
						</tr>
		</div>
					</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			
			<!-- BEGIN LISTA_ITEMS -->
			<tr>
				<td style="padding: 0px 20px 20px 20px;">
					<table width="100%">
						<tr>
							<td class="txtNegro16">{__item_orden}. {__item_titulo}</td>
						</tr>
						<tr>
							<td height="5"></td>
						</tr>
						<tr>
							<td class="txtGris12">{__item_descripcion}</td>
						</tr>
						<tr>
							<td height="15"></td>
						</tr>
						<!-- BEGIN LISTA_TABLAS -->
						<tr>
							<td style="display: block;">{__item_contenido}</td>
						</tr>
						<br>
						<!-- END LISTA_TABLAS -->
						<!-- BEGIN LISTA_GRAFICOS -->
						<tr>
							<td id="contenedor_{__grafico_indice}" class="contenidoitem" align="center" valign="top">
								<img src="{__path_img}cargando.gif" id ="imagen_{__grafico_indice}"/>
							</td>
						</tr>
						<!-- END LISTA_GRAFICOS -->
	</div>
					</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<!-- END LISTA_ITEMS -->
		
			<tr>
				<td style="padding: 20px;">
					<table width="100%">
						<tr>
							<td align="center" class="txtNegro14b">E-mail: {__pais_email}</td>
						</tr>
						<tr>
							<td align="center" class="txtGrisClaro10b">
								Tel&eacute;fono: {__pais_telefono}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								www.atentus.com
							</td>
						</tr>
						<tr>
							<td align="center" style="border-top: solid 1px #626262;">
								<span class="txtNegro16">&copy;</span>
								<span class="txtNaranjo16">atentus</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<div id="cargando"></div>
	</body>
</html>

<div id="contenedor_gantt"></div>

<script type="text/JavaScript">



function ganttAddEventListener(gantt3, j) {
	setFuenteImagen(gantt3.getPng(),"imagen_"+indice_gantt[j]);
	document.getElementById("imagen_"+indice_gantt[j]).src ="imagen_grafico.php?nombre=imagen_"+indice_gantt[j];

/*	if (document.getElementById("___CONTAINER___N"+gantt3.id)) {
		document.getElementById("___CONTAINER___N"+gantt3.id).innerHTML = "";
	}
	else {
		var contenedor_chart = document.getElementById(gantt3.id);
		var contenedor_padre = contenedor_chart.parentNode;
		contenedor_padre.removeChild(contenedor_chart);
	}*/

	j++;
	if (j < xmls_gantt.length) {
//		alert(xmls_gantt[j]);
		total_graficados++;
		dialog.setContent('<div dojoType="dijit.ProgressBar" style="width:300px" jsId="jsProgress" id="downloadProgress" maximum="'+total_graficos+'" progress="'+total_graficados+'"></div>');

		var param = tamanno_gantt[j].split('|');
	    var gantt2 = new AnyChart('{__path_swf_anygantt}');
	    gantt2.width = param[0];
	    gantt2.height = param[1];
		gantt2.setXMLFile(xmls_gantt[j]);
//		alert("alert1");
	    gantt2.addEventListener("draw", function() {
	    	ganttAddEventListener(gantt2, j);
		});
	    gantt2.write("contenedor_gantt");
	}
	else {
		document.getElementById("contenedor_gantt").innerHTML = "";
		if (termino_chart == 1) {
			dialog.attr('title', 'Reporte Generado');
			dialog.setContent(cuadroSeleccion());
		}
		else {
			termino_gantt = 1;
		}
	}
}


	var total_graficos = indice_chart.length + indice_gantt.length;
	var total_graficados = 0;
	var termino_chart = 0;
	if (indice_chart.length == 0) {
		termino_chart = 1;
	}
	var termino_gantt = 0;
	if (indice_gantt.length == 0) {
		termino_gantt = 1;
	}

	var dialog;
	dialog = new dijit.Dialog({ title: "Generando Reporte"});
	if (termino_chart == 0 || termino_gantt == 0) {
		dialog.setContent('<div dojoType="dijit.ProgressBar" style="width:300px" jsId="jsProgress" id="downloadProgress" maximum="'+total_graficos+'"></div>');
	}
	else {
		dialog.attr('title', 'Reporte Generado');
		dialog.setContent(cuadroSeleccion());
	}
	dialog.show();
		
	/* GENERO LOS GRAFICOS ANYCHART */
	if (indice_chart.length > 0) { 			
		var i=0;
		var param = tamanno_chart[i].split('|');
	    var chart = new AnyChart('{__path_swf_anychart}');
	    chart.useBrowserResize = true;
	    chart.width = param[0];
	    chart.height = param[1];
	   	chart.cantidad = 0;
	    chart.setXMLFile(xmls_chart[i]);
	    chart.addEventListener("draw", function() {
	    	chart.cantidad += 1;
	    	if (chart.cantidad==cnt_draw_chart[i]) {
	    		setFuenteImagen(chart.getPng(),"imagen_"+indice_chart[i]);
	    		document.getElementById("imagen_"+indice_chart[i]).src ="imagen_grafico.php?nombre=imagen_"+indice_chart[i];
	    		i++;
		        /* CUANDO TERMINA DE CARGAR UN ANYCHART 
		         * LOS DATOS SON REEMPLAZADOS POR EL SIGUIENTE,
		         * ASI SOLO SE CARGA UN FLASH */
		    	if (i < xmls_chart.length) {
		    		total_graficados++;
		    		jsProgress.update({maximum: total_graficos, progress: total_graficados});
		    		param = tamanno_chart[i].split('|');
	    		    chart.updateSize(param[0],param[1]);
		    		chart.cantidad = 0;
		    		chart.setXMLFile(xmls_chart[i]);
		    	}
		    	else {
			    	
		    		if (termino_gantt == 1) {
						jsProgress.update({maximum: total_graficos, progress: total_graficos});
		    			//dialog.hide();
						dialog.attr('title', 'Reporte Generado');
						dialog.setContent(cuadroSeleccion());
		    		}
		    		else {
		    			termino_chart = 1;
		    		}
			    	
					if (document.getElementById("___CONTAINER___N"+chart.id)) {
						document.getElementById("___CONTAINER___N"+chart.id).innerHTML = "";
					}
					else {
			    		var contenedor_chart = document.getElementById(chart.id);
						var contenedor_padre = contenedor_chart.parentNode;
						contenedor_padre.removeChild(contenedor_chart);
					}
	    		}
	    	}
		});

	    chart.write();
	}

	/* GENERO LOS GRAFICOS ANYGANTT */
	if (indice_gantt.length>0) {
		var j = 0;
		var param = tamanno_gantt[j].split('|');
	    var gantt = new AnyChart('{__path_swf_anygantt}');
	    gantt.width = param[0];
	    gantt.height = param[1];
	    gantt.setXMLFile(xmls_gantt[j]);
	    gantt.addEventListener("draw", function() {
    		ganttAddEventListener(gantt, j);
		});
    	gantt.write("contenedor_gantt");
	}

	function tildes_unicode(str){
		str = str.replace('á','a');
		str = str.replace('é','e');
		str = str.replace('í','i');
		str = str.replace('ó','o');
		str = str.replace('ú','u');

		str = str.replace('Á','A');
		str = str.replace('É','E');
		str = str.replace('Í','I');
		str = str.replace('Ó','O');
		str = str.replace('Ú','U');

		str = str.replace('ñ','n');
		str = str.replace('Ñ','n');
		return str;
	}

	{__funciones}


</script>
