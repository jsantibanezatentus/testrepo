<html>
	<head>
		<title>Atentus.com: Reportes</title>
		<meta http-equiv="X-UA-Compatible" content="IE=9"/>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		
		<link rel="stylesheet" href="css/especiales.css" type="text/css"/>
		<link rel="stylesheet" href="css/disponibilidad.css" type="text/css"/>
		
		<script type="text/javascript" src="tools/jquery/js/jquery-1.7.1.min.js"></script>
 		<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.8.17.custom.min.js"></script>
		
		<script type="text/javascript" src="js/disponibilidad.js"></script>
		<script type="text/javascript" src="tools/highcharts/highcharts.js"></script>
		<script type="text/javascript" src="tools/highcharts/modules/exporting.js"></script>
 		<script type="text/javascript" src="js/leyenda_svg.js"></script>
 		<script type="text/javascript" src="tools/dojo/dojo/dojo.js" djConfig="parseOnLoad:true"></script>
 		<script type="text/javascript" language="javascript" src="tools/anychart/js/AnyChart.js"></script>
 		
		<script type="text/JavaScript">

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
	
	<body>
		<form id="form_principal" name="form_principal" method="post" action="index.php">
		<input type="hidden" name="sitio_id" value="1">
		<input type="hidden" name="menu_id" value="0">
		<input type="hidden" name="objeto_id" value="{__objetivo_id}">
		<input type="hidden" name="parent_objetivo_id" value="{__parent_objetivo_id}">
		<input type="hidden" name="accion" value="">
		<input type="hidden" name="ejecutar_accion" value="1">
		<table width="700" align="center">
				<tr>
				<td>
					<table width="100%">
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
				<td height="20"></td>
			</tr>
			<!-- BEGIN LISTA_ITEMS -->
			<tr>
				<td style="padding: 0px 20px 20px 20px;">
					<div style="page-break-after: always;">
					<table width="100%">
						<tr>
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
						<tr>
							<td class="contenidoitem" align="center" valign="top">
								<img src="imagen_grafico.php?nombre=imagen_{__grafico_indice}"/>
							</td>
						</tr>
						<!-- END LISTA_GRAFICOS -->
						<tr>
							<td height="15"></td>
						</tr>
					</table>
					</div>
				</td>
			</tr>
			<!-- END LISTA_ITEMS -->
			
			
		</table>
		</form>
	</body>
</html>