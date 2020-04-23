<html>
	<head>
		<title>Atentus.com: Reportes</title>
		<meta http-equiv="X-UA-Compatible" content="IE=9"/>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" href="css/especiales.css" type="text/css"/>
		<link rel="stylesheet" href="css/disponibilidad.css" type="text/css"/>
		<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
		<!-- <link rel="stylesheet" href="css/textos-reporte.css" type="text/css"/> -->
		
		<script type="text/javascript" src="{__path_jquery_ui}js/jquery-1.7.1.min.js"></script>
		<!-- <script type="text/javascript" src="{__path_jquery_ui}js/jquery.dataTables.js"></script>-->
 		<script type="text/javascript" src="{__path_jquery_ui}js/jquery-data-table/jquery.dataTables.js"></script>
		<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>
 		<script type="text/javascript" src="{__path_js}flash_detect.js"></script>
 		<script type="text/javascript" src="{__path_js}disponibilidad.js"></script>
		<script type="text/javascript" src="{__path_js}reportes.js"></script>
		<script type="text/javascript" src="tools/highcharts/highcharts.js"></script>
		<script type="text/javascript" src="tools/highcharts/modules/exporting.js"></script>
 		<script type="text/javascript" src="js/leyenda_svg.js"></script>
 		<script type="text/javascript" src="tools/dojo/dojo/dojo.js" djConfig="parseOnLoad:true"></script>
 		<script type="text/javascript" language="javascript" src="tools/anychart/js/AnyChart.js"></script>

 		<script type="text/javascript">
			function CargaPagina(){
			document.getElementById("cargapagina").style.visibility = "visible";
			document.getElementById("cargando").style.visibility = "hidden";
			}
		</script>
		<script type="text/JavaScript">
			dojo.require("dijit.Dialog");
			dojo.require("dijit.Tooltip");
  			dojo.require("dojo.parser");
			dojo.require("dojo.fx");
			dojo.require("dojox.layout.ContentPane");
            // Detecta flash.
			if(navigator.mimeTypes["application/x-shockwave-flash"] != undefined) {
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

			function logout() {
				location.href="login.php";
			}
		</script>
	</head>

	<body onload="CargaPagina();">
	<div id="cargando">
	
			<table align="center">
					<tr>
						<td width="30" align="center"><img src="../img/cargando.gif"></td>
						<td class="textgris12">Por favor espere.<br>El reporte se esta generando.</td>
					</tr>
			</table>
		<div id="seccion_mantenencion" data-seccion='{seccion}' data-calendario='{calendario}' data-historial='{historial}' data-agregar='{agregar}'></div>
		<form id="form_principal" name="form_principal" method="post" action="index.php">
		<div id="cargapagina" style="visibility:hidden;">
		<input type="hidden" name="sitio_id" value="1">
		<input type="hidden" name="menu_id" value="0">
		<input type="hidden" name="objeto_id" value="{__objetivo_id}">
		<input type="hidden" name="parent_objetivo_id" value="{__parent_objetivo_id}">
		<input type="hidden" name="accion" value="">
		<input type="hidden" name="ejecutar_accion" value="1">
		<table width="750px" align="center">
			<tr>
				<td style="padding: 4px 4px 4px 20px;"><i class="spriteImg spriteImg-header"></i></td>
			</tr>
			<tr>
				<td>
					<table width="100%" id="especial">
						<tr>
							<td height="40" style="padding: 4px 4px 4px 20px; background-color:#f47001;">
								<span class="txtBlanco15b">{__reporte_titulo}</span><br>
								<span class="txtBlanco13b">{__objetivo_nombre}</span>
							</td>
							<td style="padding: 8px; background-color:#626262;"  colspan="2" class="txtBlanco13b">{__fecha_inicio} al {__fecha_termino}</td>
						</tr>
					</table>
				</td>
			</tr>
		</div>
			<!-- BEGIN LISTA_ITEMS -->
			<tr>
				<td style="padding: 20px;">
					<table width="100%">
						<tr>
							<td height="5px"></td>
						</tr>
						<tr>
							<td colspan="2">{__item_contenido}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
           		<td height="20"></td>
           	</tr>
           	<!-- BEGIN LISTA_GRAFICOS -->
			<!-- END LISTA_GRAFICOS -->
			
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
		</div>
		</form>
		</div>
	</body>
</html>