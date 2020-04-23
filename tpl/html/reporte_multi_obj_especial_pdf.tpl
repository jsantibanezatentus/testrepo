<html>
<head>
	<title>Atentus.com: Reportes</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="css/especiales.css" type="text/css"/>
	<link rel="stylesheet" href="css/disponibilidad.css" type="text/css"/>
	<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>

	<script type="text/javascript" src="{__path_jquery_ui}js/jquery-1.7.1.min.js"></script>
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

 		
	<script type="text/javascript" language="javascript" src="{__path_anychart}"></script>
 		
</head>
<body>
	<div id="seccion_mantenencion" data-seccion='{seccion}' data-calendario='{calendario}' data-historial='{historial}' data-agregar='{agregar}'></div>
	<table width="700" align="center">
		<tr>
			<td >
				<table width="100%" id="especial" >
					<tr>
						<td height="40" style="padding: 4px 4px 4px 20px; background-color:#f47001;">
							<span class="txtBlanco15b">{__reporte_titulo}</span><br>

						</td>
						<td style="padding: 4px; background-color:#626262;" class="txtBlanco13b">{__fecha_inicio} al {__fecha_termino}</td>
					</tr>
				</table>
			</td>
		</tr>
		<!-- BEGIN LISTA_SUBOBJETIVOS -->
		<tr>
			<td height="35" style="padding: 4px 4px 4px 20px; background-color:#b7b7b7;">
				<span class="txtBlanco15b" style=" text-shadow: black 0.1em 0.1em 0.2em">{__nombre_objetivo}</span>
			</td>
			<tr>
				<td height="4" style="background-color: #f47001;"></td>
			</tr>
		</tr>
		<!-- BEGIN LISTA_ITEMS -->
		<tr>
			<td style="padding: 20px;">
				<table width="100%"  class="saltoPagina">
					<tr id="especial2">
						<td class="txtNegro16">{__item_orden}. {__item_titulo}</td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>
					<tr>
						<td class="txtGris12">{__item_descripcion}</td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>
					<tr>
						<td class="txtGris12">{__horario_nombre_item}</td>
					</tr>
					<tr>
						<td style="display: block;">{__item_contenido}</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>

		<!-- END LISTA_ITEMS -->
		<!-- END LISTA_SUBOBJETIVOS -->
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
<style type="text/css">	
.saltoPagina {
	page-break-after: always;
}
</style>
</body>
</html>