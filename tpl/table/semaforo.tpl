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
			<table width="100%">
				<tr>
					<td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco12b">Objetivos</td>
				</tr>
				<tr>
					<td height="20" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco9">&nbsp;</td>
				</tr>
				<!-- BEGIN LISTA_OBJETIVOS0 -->												
				<tr>
					<td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #7b8ebb;" class="textblanco12" id="objetivo_{__objetivo_id}">
						<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 180px;">{__objetivo_nombre}</div>
						<div dojoType="dijit.Tooltip" connectId="objetivo_{__objetivo_id}" position="below">
							<div class="textnegro12">{__objetivo_nombre}</div>
							<div class="textnegro9">{__objetivo_servicio}</div>
						</div>
					</td>
				</tr>
				<!-- END LISTA_OBJETIVOS0 -->
			</table>
		</td>
		<td valign="top" style="width: {__ancho_tabla};">
			<div id="elementos_scroll" style="width: {__ancho_tabla}; overflow: hidden;">
				<table>
					<tr>
						<!-- BEGIN LISTA_MONITORES -->
						<td height="26" align="center" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco12b" id="monitor_{__monitor_orden}">
							<div name="elemento_pagina" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 80px;">{__monitor_nombre}</div>
							<div dojoType="dijit.Tooltip" connectId="monitor_{__monitor_orden}, ubicacion_{__monitor_orden}" position="below">
								<div class="textnegro12">{__monitor_nombre}</div>
								<div class="textnegro9">{__monitor_ubicacion}</div>
							</div>
						</td>
						<!-- END LISTA_MONITORES -->
						<!-- BEGIN LISTA_MONITORES_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;"><div style="width: 80px;"></div></td>
						<!-- END LISTA_MONITORES_BLANCO -->
					</tr>
				 	<tr>
						<!-- BEGIN LISTA_MONITORES_UBICACION -->
						<td height="20" align="right" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f58b32;" class="textblanco9" id="ubicacion_{__monitor_orden}">
							<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 80px;">{__monitor_ubicacion}</div>
						</td>
						<!-- END LISTA_MONITORES_UBICACION -->
						<!-- BEGIN LISTA_MONITORES_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;"><div style="width: 80px;"></div></td>
						<!-- END LISTA_MONITORES_BLANCO -->
					</tr>
								
					<!-- BEGIN LISTA_OBJETIVOS -->
					<tr>
						<!-- BEGIN BLOQUE_SALTO_PRINT -->
						<!-- BEGIN LISTA_MONITORES_ESTADO -->
						<td height="26" align="center" id="semaforo_{__tooltip_id}" style="padding: 2px; border-right: solid 2px #ffffff; background-color:#{__objetivo_color};">														
							<i class="{__evento_icono}" style="cursor: pointer; border-style: solid 1px;" onclick="cargarSubItem('contenedor_{__item_id}', 'subcontenedor_even_{__monitor_id}', '{__item_id}', '{__item_id_nuevo}', ['objetivo_id', '{__objetivo_id}', 'monitor_id', '{__monitor_id}', 'pagina', '1', 'popup', '{__popup}'],2); return false;" /></i>

							<!-- BEGIN TIENE_TOOLTIP -->
							<div dojoType="dijit.Tooltip" connectId="semaforo_{__tooltip_id}" position="below">
								<table>
									<tr>
										<td width="40" height="26" align="center"><i class="{__evento_icono}" /></i></td>
										<td width="200" class="textnegro13" style="padding: 3px;">{__evento_nombre}</td>
									</tr>
									<tr>
										<td colspan="2" class="textnegro12" style="padding: 3px;">{__evento_descripcion}</td>
									</tr>
									<tr>
										<td colspan="2" style="padding: 3px;">
											<span class="textnegro10b">Duraci&oacute;n: </span><span class="textnegro10">{__evento_duracion}</span><br>
										</td>
									</tr>
								</table>
							</div>
							<!-- END TIENE_TOOLTIP -->
						</td>
						<!-- END LISTA_MONITORES_ESTADO -->
						<!-- END BLOQUE_SALTO_PRINT -->
						<!-- BEGIN LISTA_MONITORES_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;"><div style="width: 80px;"></div></td>
						<!-- END LISTA_MONITORES_BLANCO -->
					</tr>
					<!-- END LISTA_OBJETIVOS -->							
				</table>
			</div>
		</td>
	</tr>
</table>
<audio id="myAudio">
        <source  src="audio/alert.wav">
</audio>
<input style="display: none" type="button" id="player" onclick="play()"></input>
    <script>
    	var backgroundMusic = new Audio();
        backgroundMusic.src = "audio/alert.wav";
        if('{__check}'=='True'){
        	backgroundMusic.play()
    	}
    </script>