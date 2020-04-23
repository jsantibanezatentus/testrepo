<script>
$(document).ready(function() {
	 iniciarPaginacion(); 
});
</script>
<style>
	.top{vertical-align: top;}
	.tabla-datos{color: #ffffff; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 10px; font-weight: normal;}
	.tabla-datos .cabecera, .tabla-datos .cuerpo, .tabla-datos .resumen{padding: 2px; border-right: solid 2px #ffffff;}
	.tabla-datos .cabecera{background-color: #F67000; color:#ffffff; font-weight: bold;}
	.tabla-datos .detalle{background-color: #F78B20; text-align: right; font-size:9px; font-weight: normal;}
	.tabla-datos .resumen{background-color: #7A8DBD; text-align: center; font-size:10px; font-weight: normal;}
	.tabla-datos .cuerpo{color:#5D5D5D;}
	.tabla-datos div.contenedor{white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
	.tabla-datos div.img {font-size: 10px;}
	.tabla-datos div.img img{margin-left: 3px;}
</style>

<table width="100%" class="tabla-datos">
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
					<td height="26" class="cabecera textblanco12b">Objetivos</td>
					<td height="26" class="cabecera textblanco12b">Global</td>
				</tr>
				<tr>
					<td height="20" class="cabecera textblanco9">&nbsp;</td>
					<td height="20" class="cabecera textblanco9">&nbsp;</td>
				</tr>
				<tr>
					<td height="26" class="resumen textblanco12">&nbsp;<!--Consolidado--></td>
					<td height="26" class="resumen textblanco12">{__tiempo_respuesta_sumatoria_global}</td>
				</tr>
				<!-- BEGIN LISTA_PASOS -->
				<tr style="background-color:#{__paso_color};">
					<td height="26" style="" class="cuerpo textblanco12" id="">
						<div class="contenedor" style="width: 106px;">{__paso_nombre}</div>
					</td>
					<td height="26" class="cuerpo textblanco12" id="">
						<div class="contenedor img" style="width: 80px;">
							<img alt="[img]" src="{__tiempo_respuesta_global_icono}" title="{__tiempo_respuesta_global_nombre}" />
							<span style="margin-left:5px;">{__tiempo_respuesta_global}</span>
						</div></td>
				</tr>
				<!-- END LISTA_PASOS -->
			</table>
		</td>
		<td valign="top" style="width: {__ancho_tabla};">
			<div id="elementos_scroll" style="width: {__ancho_tabla}; overflow: hidden;">
				<table>
					<tr>
						<!-- BEGIN LISTA_NODOS -->
						<td height="26" align="center" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco12b" id="monitor_{__nodo_id}">
							<div name="elemento_pagina" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 80px;">{__nodo_nombre}</div>
							<div dojoType="dijit.Tooltip" connectId="monitor_{__nodo_id}, ubicacion_{__nodo_id}" position="below">
								<div class="textnegro12">{__nodo_nombre}</div>
								<div class="textnegro9">{__nodo_descripcion}</div>
							</div>
						</td>
						<!-- END LISTA_NODOS -->
						<!-- BEGIN LISTA_MONITORES_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;"><div style="width: 80px;"></div></td>
						<!-- END LISTA_MONITORES_BLANCO -->
					</tr>
				 	<tr>
						<!-- BEGIN LISTA_NODOS_DET -->
						<td height="20" align="right" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f58b32;" class="textblanco9" id="ubicacion_{__nodo_id}">
							<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 80px;">{__nodo_descripcion}</div>
						</td>
						<!-- END LISTA_NODOS_DET -->
						<!-- BEGIN LISTA_MONITORES_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;"><div style="width: 80px;"></div></td>
						<!-- END LISTA_MONITORES_BLANCO -->
					</tr>
				 	<tr>
						<!-- BEGIN LISTA_TIEMPO_RESPUESTA_SUMATORIA -->
						<td height="26" class="resumen textblanco12">{__tiempo_respuesta_sumatoria}</td>
						<!-- END LISTA_TIEMPO_RESPUESTA_SUMATORIA -->
						<!-- BEGIN LISTA_MONITORES_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;"><div style="width: 80px;"></div></td>
						<!-- END LISTA_MONITORES_BLANCO -->
					</tr>
								
					<!-- BEGIN LISTA_TIEMPO_RESPUESTA -->
					<tr style="background-color:#{__paso_color};">
						<!-- BEGIN BLOQUE_SALTO_PRINT -->
						<!-- BEGIN LISTA_TIEMPO_RESPUESTA_DISPLAY -->
						<td height="26" class="cuerpo textblanco12" id="">
							<div class="contenedor img" style="width: 80px;"><img alt="[img]" src="{__tiempo_respuesta_icono}" title="{__tiempo_respuesta_nombre}" /><span style="margin-left:5px;">{__tiempo_respuesta_valor}</span></div></td>
						<!-- END LISTA_TIEMPO_RESPUESTA_DISPLAY -->
						<!-- END BLOQUE_SALTO_PRINT -->
						<!-- BEGIN LISTA_MONITORES_BLANCO -->
						<td style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;"><div style="width: 80px;"></div></td>
						<!-- END LISTA_MONITORES_BLANCO -->
					</tr>
					<!-- END LISTA_TIEMPO_RESPUESTA -->							
				</table>
			</div>
		</td>
	</tr>
</table>
