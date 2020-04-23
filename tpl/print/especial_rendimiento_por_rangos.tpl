<!-- BEGIN LISTA_MONITORES_BLANCO -->

<!-- END LISTA_MONITORES_BLANCO -->

<!-- BEGIN LISTA_NODOS_DET -->

<!-- END LISTA_NODOS_DET -->

<table width="100%">
	<tr>
		<td valign="top">
			<table width="100%">
				<tr>
					<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
				</tr>
				<tr>
					<td height="26" class="txtBlanco12b celdaTituloGris" align="center">Objetivos</td>
					<td height="26" class="txtBlanco12b celdaTituloGris" align="center">Global</td>
				</tr>
				<tr>
					<td height="26" align="center" class="celdaObjetivo txtBlanco12">&nbsp;</td>
					<td height="26" align="center" class="celdaObjetivo txtBlanco12">{__tiempo_respuesta_sumatoria_global}</td>
				</tr>
				<!-- BEGIN LISTA_PASOS -->
				<tr>
					<td height="26" class="{__print_class} txtGris12">
						<div class="celdaNoWrap" style="width: 106px;">{__paso_nombre}</div>
					</td>
					<td height="26" class="{__print_class} txtGris12">
						<div class="celdaNoWrap" style="width: 80px;">
							<img src="{__tiempo_respuesta_global_icono}" title="{__tiempo_respuesta_global_nombre}" />
							<span style="margin-left:5px;">{__tiempo_respuesta_global}</span>
						</div>
					</td>
				</tr>
				<!-- END LISTA_PASOS -->
			</table>
		</td>
		<td valign="top">
			<div id="elementos_scroll">
				<table>
					<tr>
						<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
					</tr>
					<tr>
						<!-- BEGIN LISTA_NODOS -->
						<td height="26" class="txtBlanco12b celdaTituloNaranjo" align="center">
							<div class="celdaNoWrap" style="width: 80px;">{__nodo_nombre}</div>
						</td>
						<!-- END LISTA_NODOS -->
					</tr>
					
				 	<tr>
						<!-- BEGIN LISTA_TIEMPO_RESPUESTA_SUMATORIA -->
						<td height="26" align="center" class="celdaObjetivo txtBlanco12">{__tiempo_respuesta_sumatoria}</td>
						<!-- END LISTA_TIEMPO_RESPUESTA_SUMATORIA -->
					</tr>
								
					<!-- BEGIN LISTA_TIEMPO_RESPUESTA -->
					<tr>
						<!-- BEGIN LISTA_TIEMPO_RESPUESTA_DISPLAY -->
						<td height="26" class="{__print_class} txtGris12">
							<div class="celdaNoWrap" style="width: 80px;">
								<img src="{__tiempo_respuesta_icono}" title="{__tiempo_respuesta_nombre}" />
								<span style="margin-left:5px;">{__tiempo_respuesta_valor}</span>
							</div>
						</td>
						<!-- END LISTA_TIEMPO_RESPUESTA_DISPLAY -->
					</tr>
					<!-- END LISTA_TIEMPO_RESPUESTA -->							
				</table>
			</div>
		</td>
	</tr>
</table>
