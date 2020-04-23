<html>
	<head>
		<title>Atentus.com: Reportes</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" href="css/disponibilidad.css" type="text/css"/>
		<link rel="stylesheet" href="css/textos-reporte.css" type="text/css"/>
		<link rel="stylesheet" href="css/reportes_print.css" type="text/css"/>
		<link rel="stylesheet" href="css/especiales.css" type="text/css"/>

		<script type="text/javascript" src="{__path_jquery_ui}js/jquery-1.7.1.min.js"></script>
 		<script type="text/javascript" src="{__path_jquery_ui}js/jquery.dataTables.js"></script>
 		<!--<script type="text/javascript" src="{__path_jquery_ui}js/jquery-ui-1.8.17.custom.min.js"></script>-->
 		
 		<script type="text/javascript" src="{__path_js}disponibilidad.js"></script>
		<script type="text/javascript" src="{__path_js}reportes.js"></script>
		<script type="text/javascript" language="javascript" src="{__path_anychart}"></script>
		<script type="text/javascript" src="{__path_dojo}dojo/dojo.js" djConfig="parseOnLoad:true"></script>

		<script type="text/javascript" src="tools/highcharts/highcharts.js"></script>
		<script type="text/javascript" src="tools/highcharts/modules/exporting.js"></script>
 		<script type="text/javascript" src="{__path_js}leyenda_svg.js"></script>
 		
		<script type="text/javascript">

		$(document).ready(function() {
			setTimeout('lineas()', 1000);	
		});

		function lineas(){
			var paths = document.getElementsByTagName("path");
			for (var i = paths.length - 1; i >= 0; i--) {
	     		var path = paths[i];
	     		var strokeOpacity = path.getAttribute('stroke-opacity');
	     		if (strokeOpacity != null && strokeOpacity < 0.2) {
	          		path.parentNode.removeChild(path);
	     		}
			}
		}
		</script>

	</head>

	<body>
	<div id="seccion_mantenencion" data-seccion='{seccion}' data-calendario='{calendario}' data-historial='{historial}' data-agregar='{agregar}'></div>
            <!-- BEGIN CONTENIDO -->
		<table width="700" align="center">
			<tr>
				<td>
					<table width="100%" id="especial">
						<tr>
							<td height="40" style="padding: 4px 4px 4px 4px; background-color:#f47001;">
								<span class="txtBlanco15b">{__reporte_titulo}</span><br>
								<span class="txtBlanco13b">{__objetivo_nombre}</span>
							</td>
							<td style="padding: 4px; background-color:#626262;" class="txtBlanco13b">{__fecha_inicio} al {__fecha_termino}</td>
						</tr>
						<tr>
							<td height="4" style="background-color: #f47001;"></td>
							<td height="4" style="background-color: #f47001;"></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height="15"></td>
			</tr>
			
			<!-- BEGIN LISTA_ITEMS -->
			<tr>
				<td style="padding: 0px 20px 20px 20px;">
					<div style="page-break-after: {__item_bloque};">
					<table width="100%">
						<tr id="especial2">
							<td class="txtNegro16">{__item_orden}. {__item_titulo}</td>
						</tr>
						<tr>
							<td height="15"></td>
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
						<!-- END LISTA_TABLAS -->
						<!-- BEGIN LISTA_GRAFICOS -->
						<tr style="display: {__mostrar_grafico};">
							<td class="contenidoitem" align="center" valign="top">
								<img src="imagen_grafico.php?nombre=imagen_{__grafico_indice}"/>
							</td>
						</tr>
						<!-- END LISTA_GRAFICOS -->
						<tr >
							<td height="15"></td>
						</tr>
					</table>
					</div>
				</td>
			</tr>
			<!-- END LISTA_ITEMS -->
<!-- 		
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
 -->
		</table>
            <!-- END CONTENIDO -->
	</body>
</html>