<!-- BEGIN LISTA_PASOS_BLANCO -->

<!-- END LISTA_PASOS_BLANCO -->

<table width="100%" class="tabla-datos">
	<tr>
		<td valign="top">
			<table width="100%">
				<tr>
					<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
				</tr>
				<tr>
					<td height="26" class="txtBlanco12b celdaTituloGris" align="center">Objetivos</td>
					<td height="26" class="txtBlanco12b celdaTituloGris" align="center">
						<div class="celdaNoWrap" style="width: 80px;">Consolidado</div>
					</td>
				</tr>
				<tr>
					<td height="26" class="celdaIteracion1 txtGris12">Global</td>
					<td height="26" align="center" class="celdaObjetivo txtBlanco12">{__tiempo_respuesta_sumatoria_global}</td>
				</tr>
				
				<!-- BEGIN LISTA_NODOS -->
				<tr>
					<td height="26" class="{__print_class} txtGris12">
						<div class="celdaNoWrap" style="width: 100px;">{__nodo_nombre}</div>
					</td>
					<td height="26" align="center" class="celdaObjetivo txtBlanco12">{__tiempo_respuesta_sumatoria}</td>
				</tr>
				<!-- END LISTA_NODOS -->
			</table>
		</td>
		<td valign="top">
			<div>
				<table>
					<tr>
						<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
					</tr>
				
					<tr>
						<!-- BEGIN LISTA_PASOS -->
						<td height="26" class="txtBlanco12b celdaTituloNaranjo" align="center">
							<div class="celdaNoWrap" style="width: 80px;">{__paso_nombre}</div>
						</td>
						<!-- END LISTA_PASOS -->
					</tr>
					
				 	<tr>
						<!-- BEGIN LISTA_TIEMPO_RESPUESTA_SUMATORIA -->
						<td height="26" class="celdaIteracion1 txtGris12">
							<div class="celdaNoWrap" style="width: 80px;">
								<img src="{__tiempo_respuesta_global_icono}" title="{__tiempo_respuesta_global_nombre}" />
								<span style="margin-left:5px;">{__tiempo_respuesta_global}</span>
							</div>
						</td>
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
