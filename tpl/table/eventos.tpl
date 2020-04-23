<table width="100%">
	<tr>
		<td style="border: solid 1px #ffffff;" class="celdanegra50">{__monitor_nombre}</td>
	</tr>
</table>
<table width="100%">
	<tr>
		<td style="border: solid 1px #ffffff;" class="celdanegra40"  width="28%">{__objetivo_nombre}</td>
		<!-- BEGIN LISTA_EVENTOS_INICIO -->
		<td style="border: solid 1px #ffffff;" class="{__evento_style}" align="center" width="12%">{__evento_inicio}</td>
		<!-- END LISTA_EVENTOS_INICIO -->
		<!-- BEGIN LISTA_EVENTOS_FALTANTES -->
		<td style="border: solid 1px #ffffff;" class="celdaenblanco" align="center" width="12%" rowspan="100%">&nbsp;</td>
		<!-- END LISTA_EVENTOS_FALTANTES -->
	</tr>
	<!-- BEGIN LISTA_PASOS -->
	<tr>
		<td style="border: solid 1px #ffffff;" width="28%" class="{__estiloPaso} " valign="top" id="paso_{__evento_tooltip_id}_{__monitor_id}" title="{__paso_nombre_completo}">{__paso_nombre}</td>
		<!-- BEGIN LISTA_EVENTOS -->
		<td style="border: solid 1px #ffffff;" bgcolor="#c4c4c4" width="12%" >
			<table width="100%" >
				<!-- BEGIN LISTA_EVENTOS_PATRONES -->
				<tr>
					<td height="22" bgcolor="#{__evento_color}" align="center" id="evento_{__evento_tooltip_id}_{__monitor_id}">
						<i class="{__evento_icono}"></i>

						<div dojoType="dijit.Tooltip" connectId="evento_{__evento_tooltip_id}_{__monitor_id}" position="below">
							<table >
								<tr>
									<td colspan="100%" class="textnegro13" height="22" valign="top">{__evento_nombre}</td>
								</tr>
								<tr>
									<td>
										<table>
											<tr>
												<td align="center" width="80" height="22" bgcolor="#{__evento_color}" >
													<i class="{__evento_icono}"></i>
												</td>
											</tr>
										</table>
									</td>
									<td>&nbsp;</td>
									<td width="170" class="textnegro12">{__evento_descripcion}</td>
								</tr>
								<!-- BEGIN BLOQUE_PATRON -->
								<tr>
									<td height="10"></td>
								</tr>
								<tr>
									<td colspan="100%" class="textnegro13">Patron:</td>
								</tr>
								<tr>
									<td colspan="100%" class="textnegro12">{__patron}</td>
								</tr>
								<!-- END BLOQUE_PATRON -->
							</table>
						</div>

					</td>
				</tr>
				<!-- END LISTA_EVENTOS_PATRONES -->
			</table>
		</td>
		<!-- END LISTA_EVENTOS -->
	</tr>
	<!-- END LISTA_PASOS -->
	<tr>
		<td style="border: solid 1px #ffffff;" class="celdaduracion" width="28%" height="35">&nbsp;</td>
		<!-- BEGIN LISTA_EVENTOS_DURACION -->
		<td style="border: solid 1px #ffffff;" class="celdaduracion" align="center" width="12%" height="35">{__evento_duracion}</td>
		<!-- END LISTA_EVENTOS_DURACION -->
	</tr>	
</table>

<table align="right" class="celdabordederecha">
	<tr>
		<td>
			<input type="button"  class="{__class_boton_atras}"  {__disabled_atras}
			 onClick="cargarItem('subcontenedor_even_{__monitor_id}', '{__item_id}', '0', ['monitor_id', '{__monitor_id}', 'pagina', '{__pagina_atras}']); return false;">
		</td>
		<td class="celdanegra50" width="20" align="center">{__pagina}</td>
		<td>
			<input type="button" class="{__class_boton_adelante}" id="boton_adelante_{__monitor_id}" {__disabled_adelante}
			 onClick="cargarItem('subcontenedor_even_{__monitor_id}', '{__item_id}', '0', ['monitor_id', '{__monitor_id}', 'pagina', '{__pagina_adelante}']); return false;">
		</td>
	</tr>
</table>
<br>
<br>