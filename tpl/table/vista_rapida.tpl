 <script>
 $(document).ready(function() {
	 iniciarPaginacion(); 
 });
 </script>
 
 <table width="100%">
 	<tr>
 		<td colspan="100%" align="right" style="border-bottom: solid 2px #ffffff;">
			<table style="display: {__display_botones};">
				<tr>
					<td height="30" style="border: solid 2px #ffffff;" class="textnegro10">
						<span id="elementos_mostrados" class="textnegro10b">1-6</span> de <span id="elementos_total" class="textnegro10b">&nbsp;</span> Monitores&nbsp;&nbsp;
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
					<td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco12b">Objetivos</td>
				</tr>
				<tr>
					<td height="20" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco9">&nbsp;</td>
				</tr>
				<!-- BEGIN TITULOS_OBJETIVOS -->
				<tr>
					<td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #{__objetivo_color};" class="textblanco12" id="objetivo_{__objetivo_id}">
						<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 180px;">{__objetivo_nombre}</div>
						<div dojoType="dijit.Tooltip" connectId="objetivo_{__objetivo_id}" position="below">
							<div class="textnegro12">{__objetivo_nombre}</div>
							<div class="textnegro9">{__objetivo_servicio}</div>
						</div>
					</td>
				</tr>
				<!-- BEGIN TITULOS_PASOS -->
				<!-- BEGIN TITULOS_PATRONES -->
				<tr>
					<td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color:#d4d4d4;" class="textgris12" id="paso_{__objetivo_id}_{__paso_id}">
						<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 180px;">{__paso_nombre}</div>
						<div dojoType="dijit.Tooltip" connectId="paso_{__objetivo_id}_{__paso_id}" position="below">
							<div class="textnegro12">{__paso_nombre}</div>
						</div>						
					</td>
				</tr>
				<!-- END TITULOS_PATRONES -->
				<tr>
					<td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color:#e9e9e9;">&nbsp;</td>
				</tr>
				<!-- END TITULOS_PASOS -->
				<!-- END TITULOS_OBJETIVOS -->
			</table>
		</td>
		<td style="width: {__ancho_tabla};">
			<div id="elementos_scroll" style="width: {__ancho_tabla}; overflow: hidden;">
				<table>
					<tr>
						<!-- BEGIN LISTA_NODOS -->
						<td height="26" align="center" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco12b" id="nodo_{__grupo_id}_{__nodo_id}">
							<div name="elemento_pagina" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 80px;">{__nodo_nombre}</div>
							<div dojoType="dijit.Tooltip" connectId="nodo_{__grupo_id}_{__nodo_id}, ubicacion_{__grupo_id}_{__nodo_id}" position="below">
								<div class="textnegro12">{__nodo_nombre}</div>
								<div class="textnegro9">{__nodo_ubicacion}</div>
							</div>
						</td>
						<!-- END LISTA_NODOS -->
						<!-- BEGIN LISTA_NODOS_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;" class="textblanco9"><div style="width:80px;">&nbsp;</div></td>
						<!-- END LISTA_NODOS_BLANCO -->
					</tr>
					<tr>
						<!-- BEGIN LISTA_NODOS_UBICACION -->
						<td height="20" align="right" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f58b32;" class="textblanco9" id="ubicacion_{__grupo_id}_{__nodo_id}">
							<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 80px;">{__nodo_ubicacion}</div>
						</td>
						<!-- END LISTA_NODOS_UBICACION -->
						<!-- BEGIN LISTA_NODOS_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;" class="textblanco9"><div style="width:80px;">&nbsp;</div></td>
						<!-- END LISTA_NODOS_BLANCO -->
					</tr>
					<!-- BEGIN LISTA_OBJETIVOS -->
					
					<!-- BEGIN LISTA_GRUPOS -->
					<tr>
						<!-- BEGIN LISTA_FECHA -->
						<td height="26" align="center" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #{__objetivo_color};" class="textblanco12">{__evento_fecha}</td>
						<!-- END LISTA_FECHA -->
						<!-- BEGIN LISTA_NODOS_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;" class="textblanco9"><div style="width:80px;">&nbsp;</div></td>
						<!-- END LISTA_NODOS_BLANCO -->
					</tr>
					<!-- BEGIN LISTA_PASOS -->
					<!-- BEGIN LISTA_PATRONES -->
					<tr>
						<!-- BEGIN LISTA_ESTADOS -->
						<td height="26" align="center" id="vista_{__tooltip_id}" style="padding: 2px; border-right: solid 2px #ffffff; background-color:#{__evento_color};">
							<div style="width: 80px;"><i class="{__evento_icono}" /></i></div>
							<!-- BEGIN TIENE_TOOLTIP -->
							<div dojoType="dijit.Tooltip" connectId="vista_{__tooltip_id}" position="below">
								<table>
									<tr>
										<td width="80" height="26" align="center" style="background-color: #{__evento_color};"><i class="{__evento_icono}" /></i></td>
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
						<!-- BEGIN LISTA_NODOS_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;" class="textblanco9"><div style="width:85px;">&nbsp;</div></td>
						<!-- END LISTA_NODOS_BLANCO -->
					</tr>
					<!-- END LISTA_PATRONES -->
					<tr>
						<!-- BEGIN LISTA_RESPUESTA -->
						<td height="26" align="center" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;" class="textgris12">{__evento_respuesta}</td>
						<!-- END LISTA_RESPUESTA -->
						<!-- BEGIN LISTA_NODOS_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;" class="textblanco9"><div style="width:85px;">&nbsp;</div></td>
						<!-- END LISTA_NODOS_BLANCO -->
					</tr>
					<!-- END LISTA_PASOS -->
					
					<!-- END LISTA_GRUPOS -->
					<!-- END LISTA_OBJETIVOS -->
				</table>
			</div>
		</td>
	</tr>
</table>
