<script>
$(document).ready(function() {
	iniciarPaginacion({__cantidad_estados}); 
});
 
$(".objetivo,.paso").live("mousemove", function(e) {
	$(".dijitTooltipABLeft").css("left", e.pageX - 10);
});
</script>

<table width="100%">
	<tr>
		<td colspan="100%" align="right" style="border-bottom: solid 2px #ffffff;">
			<table style="display: {__display_botones};">
				<tr>
					<td height="30" style="border: solid 2px #ffffff;" class="textnegro10">
						<span id="elementos_mostrados" class="textnegro10b">1-6</span> de <span id="elementos_total" class="textnegro10b">&nbsp;</span> Estados&nbsp;&nbsp;
					</td>
					<td id="bttPL" height="30" width="30" align="center" style="cursor: pointer; background-color: #f47001;">
						<i id="flechaPL" class="spriteButton spriteButton-arrow2_left" /></i>
					</td>
					<td style="width: 2px;"></td>
					<td id="bttPR" height="30" width="30" align="center" style="cursor: pointer; background-color: #f47001;">
						<i id="flechaPR" class="spriteButton spriteButton-arrow2_right" /></i>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table style="width: 100%">
				<tr>
					<td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco12b">Monitores</td>
				</tr>
				<tr>
					<td height="20" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco9">&nbsp;</td>
				</tr>
				<!-- BEGIN TITULOS_NODOS -->
				<tr>
					<td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco12" id="nodo_{__grupo_id}_{__nodo_id}">
						<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 180px;">{__nodo_nombre}</div>
						<div dojoType="dijit.Tooltip" connectId="nodo_{__grupo_id}_{__nodo_id}" position="below">
							<div class="textnegro12">{__nodo_nombre}</div>
							<div class="textnegro9">{__nodo_ubicacion}</div>
						</div>
					</td>
				</tr>
				<tr>
					<td height="20" style="padding: 2px; border-right: solid 2px #ffffff; background-color:#f58b32;" class="textgris10">&nbsp;</td>
				</tr>
				<tr>
					<td height="20" style="padding: 2px; border-right: solid 2px #ffffff; background-color:#f58b32;" class="textgris10">&nbsp;</td>
				</tr>
				<!-- END TITULOS_NODOS -->
			</table>
		</td>
		<td style="width: {__ancho_tabla};" valign="top">
			<div id="elementos_scroll" style="width: {__ancho_tabla}; overflow: hidden;">
				<table>
					<tr>
						<!-- BEGIN LISTA_OBJETIVOS -->
						<td colspan="{__objetivo_colspan}" height="26" align="center" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #{__objetivo_color};" class="textblanco12b objetivo" id="objetivo_{__objetivo_id}">
							<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: {__objetivo_width}px;">{__objetivo_nombre}</div>
							<div dojoType="dijit.Tooltip" connectId="objetivo_{__objetivo_id}" position="below" showDelay="0">
								<div class="textnegro12">{__objetivo_nombre}</div>
								<div class="textnegro9">{__objetivo_servicio}</div>
							</div>
						</td>
						<!-- END LISTA_OBJETIVOS -->
						<!-- BEGIN LISTA_PASOS_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;" class="textblanco9"><div style="width:80px;">&nbsp;</div></td>
						<!-- END LISTA_PASOS_BLANCO -->
					</tr>
					<tr>
						<!-- BEGIN LISTA_PASOS -->
						<td colspan="{__paso_colspan}" height="20" align="center" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #a1a9c3;" class="textblanco9 paso" id="paso_{__objetivo_id}_{__paso_id}">
							<div name="elemento_pagina" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: {__paso_width}px;">{__paso_nombre}</div>
							<div dojoType="dijit.Tooltip" connectId="paso_{__objetivo_id}_{__paso_id}" position="below" showDelay="0">
								<div class="textnegro12">{__paso_nombre}</div>
							</div>
						</td>
						<!-- END LISTA_PASOS -->
						<!-- BEGIN LISTA_PASOS_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;" class="textblanco9"><div style="width:80px;">&nbsp;</div></td>
						<!-- END LISTA_PASOS_BLANCO -->
					</tr>
					<!-- BEGIN LISTA_NODOS -->

					<!-- BEGIN LISTA_PATRONES -->
					<tr>
						<!-- BEGIN LISTA_ESTADOS -->
						<td height="26" align="center" id="vista_{__tooltip_id}" style="padding: 2px; border-right: solid 2px #ffffff; background-color:#{__evento_color};">
							<div style="width: 80px;"><i class="{__evento_icono}" /></i></div>
							<!-- BEGIN TIENE_TOOLTIP -->
							<div dojoType="dijit.Tooltip" connectId="vista_{__tooltip_id}" position="below">
								<table>
									<tr>
										<td width="85" height="26" align="center" style="background-color: #{__evento_color};"><i class="{__evento_icono}" /></i></td>
										<td width="170" class="textnegro13" style="padding: 3px;">{__evento_nombre}</td>
									</tr>
									<tr>
										<td colspan="2" class="textnegro12" style="padding: 3px;">{__evento_descripcion}</td>
									</tr>
									<tr>
										<td colspan="2" style="padding: 3px;">
											<span class="textnegro10b">Duraci&oacute;n: </span><span class="textnegro10">{__tooltip_duracion}</span><br>
											<span class="textnegro10b">Respuesta: </span><span class="textnegro10">{__tooltip_respuesta}</span><br>
											<span class="textnegro10b">Patron: </span><span class="textnegro10">{__tooltip_patron}</span>
										</td>
									</tr>
								</table>
							</div>
							<!-- END TIENE_TOOLTIP -->
						</td>
						<!-- END LISTA_ESTADOS -->
						<!-- BEGIN LISTA_PASOS_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;" class="textblanco9"><div style="width:80px;">&nbsp;</div></td>
						<!-- END LISTA_PASOS_BLANCO -->
					</tr>
					<!-- END LISTA_PATRONES -->
					<tr>
						<!-- BEGIN LISTA_FECHA -->
						<td height="20" align="center" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;" class="textgris10">{__evento_fecha}</td>
						<!-- END LISTA_FECHA -->
						<!-- BEGIN LISTA_PASOS_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;" class="textblanco9"><div style="width:80px;">&nbsp;</div></td>
						<!-- END LISTA_PASOS_BLANCO -->
					</tr>
					<tr>
						<!-- BEGIN LISTA_RESPUESTA -->
						<td height="20" align="center" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;" class="textgris10">{__evento_respuesta}</td>
						<!-- END LISTA_RESPUESTA -->
						<!-- BEGIN LISTA_PASOS_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;" class="textblanco9"><div style="width:80px;">&nbsp;</div></td>
						<!-- END LISTA_PASOS_BLANCO -->
					</tr>
					<!-- END LISTA_NODOS -->
				</table>
			</div>
		</td>
	</tr>
</table>