<tr>
	<td width="30%">
		<div>
			<img style="max-width: 100%; height: auto;" src="/img/especiales/banco_chile_especial.png">
		</div>
	</td>
	<td width="70%">
		<table>
			<ul>
				<li class="leyendaDescripcion"><a style="font-weight: bold;">Uptime: </a>La funcionalidad responde correctamente al menos en un ISP con servicio.</li>
				<li class="leyendaDescripcion"><a style="font-weight: bold;">Downtime: </a>Todos los ISP's detectaron indisponibilidad en un mismo periodo.</li>
				<li class="leyendaDescripcion"><a style="font-weight: bold;">Tiempo de respuesta: </a>Tiempo de respuesta promedio en segundos.</li>
				<li class="leyendaDescripcion"><a style="font-weight: bold;">Resumen de los ISPs: </a>
					<!-- BEGIN BLOQUE_CATEGORIAS_2 -->
					<ul>
						<li>
							<a style="font-weight: bold;">{__nombre_categoria}: </a>
							{__nodo_mm_cat}
						</li>
					</ul>
					<!-- END BLOQUE_CATEGORIAS_2 -->
				</ul>
			</table>
		</td>
	</tr>
	<tr><td height="20px"></td></tr>
	<table width="100%">
		<tr>
			<td height="25px" style="border-left: solid 1px #fff; border-top: solid 1px #fff; border-bottom: solid 1px #fff;" align="left" class="txtBlanco12b celdaTituloNaranjoEspecial2"></td>
			<td align="left" style="border-right: solid 1px #fff; border-top: solid 1px #fff; border-bottom: solid 1px #fff;" class="celdaTituloNaranjoEspecial2"></td>
			<td align="center"style="border: solid 1px #fff;" class="txtClaro10bEspecial celdaTituloNaranjo">UPTIME</td>
			<td align="center"style="border: solid 1px #fff;" class="txtClaro10bEspecial celdaTituloNaranjo">DOWNTIME</td>
			<td align="center"style="border: solid 1px #fff;" class="txtClaro10bEspecial celdaTituloNaranjo">TIEMPO DE RESPUESTA [segs]</td>
		</tr>
		<!-- BEGIN BLOQUE_CATEGORIAS -->
		<tr>
			<td class="txtNegro14bEspecialBold categoria" width="31%" colspan="2">{__nombre_categoria}</td>
			<td class="txtNegro14bEspecialBold categoria" width="10%" align="center">{__uptime_real_total} %</td>
			<td class="txtNegro14bEspecialBold categoria" width="10%" align="center">{__downtime_real_total} %</td>
			<td class="txtNegro14bEspecialBold categoria" width="12%" align="center">{__tiempo_respuesta_total}</td>
		</tr>
		<!-- END BLOQUE_CATEGORIAS -->
		<tr>
			<td height="30px"></td>
		</tr>
	</table>
	<!-- BEGIN BLOQUE_CATEGORIA -->
	<div style="{__page_break}">
		<table width="100%">
			<tr >
				<div>
					<td height="25px" align="left" class="txtNegroClaro14bEspecialBold celdaTituloNaranjoEspecial">{__nombre_categoria}</td>
					<td height="25px" align="left" class="txtBlanco12b celdaTituloNaranjoEspecial">{__uptime_real_total} %</td>
					<td height="25px" style="border: solid 1px #cccccc;" align="center" class="txtNegro10bEspecial">UPTIME</td>
					<td height="25px" style="border: solid 1px #cccccc;" align="center" class="txtNegro10bEspecial">DOWNTIME</td>
					<td height="25px" style="border: solid 1px #cccccc; padding: 3px 0px 3px 0px; " align="center" class="txtNegro10bEspecial">TIEMPO DE RESPUESTA [segs]</td>
				</div>
			</tr>
			<!-- BEGIN BLOQUE_FUNCIONALIDAD -->
			<tr style="border-style: outset; border-color: #fff;">
				<td class="txtNegro14bEspecialBold {__class_iteracion}" width="31%" colspan="2">{__nombre_funcionalidad}</td>
				<td class="txtNegro14bEspecialBoldFuncionalidad {__class_iteracion}" width="10%" align="center">{__uptime_real} %</td>
				<td class="txtNegro14bEspecialBoldFuncionalidad {__class_iteracion}" width="10%" align="center">{__downtime_real} %</td>
				<td class="txtNegro14bEspecialBoldFuncionalidad {__class_iteracion}" width="12%" align="center">{__tiempo_respuesta}</td>
			</tr>
			<!-- BEGIN BLOQUE_PASO -->
			<tr>
				<td colspan="5" width="100%">
					<div style="{__page_break_paso}">
						<table width="100%" align="center">
							<tr>
								<td class="txtNegro12bEspecialPaso" colspan="2" width="31%" style="border-bottom: solid 1px #fff; padding: 1px 6px 1px 25px; background: #{__class_iteracion_paso}; color: {__color_text};">{__nombre_paso} {__nombre_objetivo}</td>
								<td class="txtNegro12bEspecialPaso" style="border-bottom: solid 1px #fff; background: #{__class_iteracion_paso}; color: {__color_text}; padding-left: 25px;" width="10%" align="center">{__uptime_real_paso} %</td>
								<td class="txtNegro12bEspecialPaso" style="border-bottom: solid 1px #fff; background: #{__class_iteracion_paso}; color: {__color_text}; padding-left: 25px;" width="10%" align="center">{__downtime_real_paso} %</td>
								<td class="txtNegro12bEspecialPaso" style="border-bottom: solid 1px #fff; background: #{__class_iteracion_paso}; color: {__color_text}; padding-left: 25px;" width="12%" align="center">{__tiempo_respuesta_paso}</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<!-- END BLOQUE_PASO -->
			<tr>
				<td colspan="4" height="5px"></td>
			</tr>
			<!-- END BLOQUE_FUNCIONALIDAD -->
			<tr>
				<td colspan="4" height="15px"></td>
			</tr>
		</table>
	</div>
	<!-- END BLOQUE_CATEGORIA -->

	<style type="text/css">
		.celdaTituloNaranjoEspecial {
			padding: 1px 6px 1px 6px;
			background-color: #ff9a44;
		}

		.txtNegro14bEspecialBold {
			color: #252525;
			font-family: Trebuchet MS, Verdana, sans-serif;
			font-size: 12px;
			font-weight: bold;
		}

		.txtNegro14bEspecialBoldFuncionalidad {
			color: #252525;
			font-family: Trebuchet MS, Verdana, sans-serif;
			font-size: 12px;
			font-weight: bold;
		}

		.txtNegroClaro14bEspecialBold {
			color: #fff;
			font-family: Trebuchet MS, Verdana, sans-serif;
			font-size: 14px;
			font-weight: bold;
		}

		.txtClaro10bEspecial {
			color: #fff;
			font-family: Trebuchet MS, Verdana, sans-serif;
			font-size: 10px;
			font-weight: bold;
		}

		.celdaTituloNaranjoEspecial2{
			padding: 1px 6px 1px 6px;
			background-color: #f47001;
		}

		.txtNegro10bEspecial {
			color: #252525;
			font-family: Trebuchet MS, Verdana, sans-serif;
			font-size: 10px;
			font-weight: bold;
		}

		.txtNegro12bEspecial {
			color: #252525;
			font-family: Trebuchet MS, Verdana, sans-serif;
			font-size: 14px;
		}

		.txtNegro12bEspecialPaso {
			font-family: Trebuchet MS, Verdana, sans-serif;
			font-size: 11px;
		}

		.categoria {
			border: solid 1px #fff; 
			padding: 4px 6px 4px 6px; 
			background-color: #ffb577;
		}

		.iteracionNaranjo {
			border: solid 1px #fff; 
			background-color: #ffb577; 
			padding: 4px 6px 4px 6px;
		}

		.iteracionGris {
			border: solid 1px #fff; 
			background-color: #c7c7c7; 
			padding: 4px 6px 4px 6px;
		}

		.leyendaDescripcion {
			font-family: Trebuchet MS, Verdana, sans-serif; 
			font-size: 11px;
		}
	</style>