<html>
	<head>
	
		<meta http-equiv="X-UA-Compatible" content="IE=9"/>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	
		<title>Atentus.com: Reportes</title>
		
		<link rel="stylesheet" href="css/disponibilidad.css" type="text/css"/>
		<link rel="stylesheet" href="css/textos-reporte.css" type="text/css"/>
		<link rel="stylesheet" href="css/especiales.css" type="text/css"/>
		<style type="text/css">
			@import "{__path_dojo}dijit/themes/nihilo/nihilo.css";
		</style>
		<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
		<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>
		<script type="text/javascript" src="{__path_jquery_ui}js/jquery-1.7.1.min.js"></script>
		<!-- <script type="text/javascript" src="{__path_jquery_ui}js/jquery-ui-1.8.17.custom.min.js"></script> -->
		<script type="text/javascript" src="{__path_jquery_ui}js/jquery.dataTables.js"></script>
		<script type="text/javascript" src="{__path_js}flash_detect.js"></script>
		
		<script type="text/javascript" src="{__path_js}disponibilidad.js"></script>
		<script type="text/javascript" src="{__path_js}reportes.js"></script>
		<script type="text/javascript" src="{__path_dojo}dojo/dojo.js" djConfig="parseOnLoad:true"></script>
		
		<script type="text/javascript" src="tools/highcharts/highcharts.js"></script>
		<script type="text/javascript" src="tools/highcharts/modules/exporting.js"></script>
 		<script type="text/javascript" src="{__path_js}leyenda_svg.js"></script>
		
		<script type="text/JavaScript">
			var grupo_semaforo_anterior = 0;
			var grupo_vista_anterior = 0;
			var monitor_elementos_monitoreos_anterior = 0;
			var monitor_elementos_estadisticas_anterior = 0;
			var grupo_disponibilidad_anterior = 100000;
			var grupo_estadistica_dia_anterior = 0;
			var grupo_estadistica_resumen = 0;
			var grupo_estadistica_detalle = 0;
			var grupo_ponderado_anterior = 0;
			var nodo_registro_plus = 0;
			dojo.require("dijit.Dialog");
			dojo.require("dijit.Tooltip");
  			dojo.require("dojo.parser");
			dojo.require("dojo.fx");
			dojo.require("dojox.layout.ContentPane");

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
			
			function logout() {
				window.opener.logout();
				window.close();
			}
		</script>
	</head>
	
	<body class="nihilo">
		<div id="seccion_mantenencion" data-seccion='{seccion}' data-calendario='{calendario}' data-historial='{historial}' data-agregar='{agregar}'></div>
		<form name="form_principal" method="POST" action="index.php">  <!--formulario principal-->
		<input type="hidden" name="sitio_id" value="{__sitio_id}">  <!--id del controlador que se va a cargar-->
		<input type="hidden" name="menu_id" value="{__menu_id}">  <!--id de la seccion que se va a cargar-->
		<input type="hidden" name="objeto_id" value="{__objeto_id}">  <!--id del objeto que se va a cargar (objetivo_id,monitor_id,horario_id)-->
		<input type="hidden" name="accion" value="">  <!--accion que se va a realizar-->
		<input type="hidden" name="ejecutar_accion" value="">  <!--id del controlador que se va a cargar si se realiza una accion-->

		<script type="text/javascript" language="javascript" src="{__path_anychart}"></script>
		
		<table width="755" bgcolor="#ffffff" align="center">
		<tr>
		<td>
		<table width="100%">
			<tr>
				<td class="tituloitem">{__item_titulo}</td>
				<td class="tituloitem" width="5%" align="center">
					<a href="#" onclick="cerrarPopupItem();"><i class="spriteButton spriteButton-cerrar_popup"></i></a>
				</td>
			</tr>
		</table>
		<table width="100%"> 
	
			<tr>
				<td class="contenidoitem" colspan="100%" style="border-bottom: solid 1px #626262; padding-top: 12px;">
			<div dojoType="dojox.layout.ContentPane" id="escala_{__item_id}" style="overflow:hidden;"></div>
					<div dojoType="dojox.layout.ContentPane" id="contenedor_{__item_id}" style="overflow:hidden;">
						<table align="center">
							<tr>
								<td width="30" align="center"><img src="{__path_img}cargando.gif"></td>
								<td class="textgris12">Por favor espere.<br>El reporte se esta generando.</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</table>
		</td>
		</tr>
		</table>
		</form>			
	</body>
</html>
<script type="text/javascript" language="javascript">
dojo.addOnLoad(function() {
	cargarItem('contenedor_{__item_id}', '{__item_id}', '1', ['popup', '1','reporte_id',{__reporte_id}]);

});
</script> 
