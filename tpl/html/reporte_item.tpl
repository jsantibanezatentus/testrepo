<table width="100%" id="tabla_item_{__item_id}" data-item-url="{__item_url}" data-usuario-id="{__sitio_usuario_id}" data-objetivo-id="{__objetivo_id}" data-ga-tracking-id="{__ga_tracking_id}">
	<tr>
		<td class="tituloitem" >{__item_titulo}</td>
                <td class="tituloitem" align="center" width="5%">
			<a href="#" onclick="abrirPopup(['item_id', '{__item_id}','reporte_id', {__reporte_id}]); return false;"><i class="spriteButton spriteButton-abrir_popup"></i></a>
                </td>

	</tr>
</table>
<table width="100%">
	<tr>
		<td class="contenidoitem" style="height: expression(this.height > 10?'auto':'10px');">
			<!-- BEGIN TIENE_GENERAR_REPORTE -->
			<table width="100%">
				<tr>
					<td align="right">
						<table id = "mostrarcalendario" onclick="mostrarCalendario('{__item_id}');return false;"
							style="border-left: solid 1px #e0e0e0; border-right: solid 1px #e0e0e0; border-bottom: solid 1px #e0e0e0;"
							onMouseOver="this.style.borderLeft='solid 2px #626262'; this.style.borderRight='solid 2px #626262'; this.style.borderBottom='solid 2px #626262';"
							onMouseOut="this.style.borderLeft='solid 1px #e0e0e0'; this.style.borderRight='solid 1px #e0e0e0'; this.style.borderBottom='solid 1px #e0e0e0';">
							<tr>
								<td style="padding: 3px;" class="textgris10">{__item_horario}</td>
								<td style="padding: 3px;" class="textnegro13">{__item_periodo}</td>
								<td style="padding: 2px; border-left: solid 1px #e0e0e0; background-color: #f6f6f6;"><i id="boton_calendario_{__item_id}" class="spriteButton spriteButton-abrir_calendario"></i></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td id="generar_reporte_{__item_id}" style="display:none;" align="center">
						<div style="border: solid 1px #e0e0e0; background-color:#f6f6f6;">
							<table style="border-spacing: 5px; border-collapse: separate;">
								<tr>
									<td valign="top" id="calendario_periodico_{__item_id}"></td>
									<td valign="top" id="calendario_{__item_id}"></td>
									<td valign="top" id="horario_{__item_id}"></td>
								</tr>
								<tr>
									<td align="center" colspan="3">
										<input type="button" class="boton_accion" value="Generar Reporte" onclick="document.form_principal.submit();"/>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
			</table>
			<!-- END TIENE_GENERAR_REPORTE -->

		</td>
	</tr>
	<tr>
		<td class="contenidoitem textdescripcion">{__item_descripcion}</td>
		
	</tr>
	<tr>
		<td class="contenidoitem" style="border-bottom: solid 1px #626262;">
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
<br>

<div id = "data" data-item_id='{__item_id}' data-elemento_id='reporte_id' data-elemento_nombre='{__reporte_id}'></div>
<script type="text/javascript" language="javascript">
dojo.addOnLoad(function() {
//	{__item_function_js}('contenedor_{__item_id}','{__item_id}',0);
	
	cargarItem('contenedor_{__item_id}', '{__item_id}', '1', ['reporte_id', {__reporte_id}]);

});
</script> 
