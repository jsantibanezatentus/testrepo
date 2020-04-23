<table width="100%">
	<tr>
		<td class="celdanegra50 celdaborde3">{__monitor_nombre}</td>
	</tr>
</table>
<table width="100%">
	<tr>
		<td class="celdanegra40 celdaborde3">Fecha</td>
		<td class="celdanegra40 celdaborde3" width="250" style="display:{__monitor_display};">Monitor</td>
		<td class="celdanegra40 celdaborde3" width="100">Estado</td>
		<td class="celdanegra40 celdaborde3" width="30"></td>
	</tr>
</table>

<!-- BEGIN BLOQUE_MONITOREOS -->
<table width="100%">
	<tr>
		<td class="{__class} celdaborde3">{__monitoreo_fecha}</td>
		<td class="{__class} celdaborde3" width="250" style="display:{__monitor_display};">{__monitoreo_desde}</td>
		<td class="{__class} celdaborde3" width="100" align="center">
                    <i class="{__monitoreo_evento_icono}"></i>
		</td>
		<td class="{__class} celdaborde3" id="td_regplus_{__monitor_id}_{__monitoreo_fecha_completa}" width="30" align="center" style="cursor:pointer">
			
<i id="imagen_regplus_{__monitor_id}_{__monitoreo_fecha_completa}" class="spriteButton spriteButton-abrir_calendario"  onClick="mostrarDetallePlus('regplus', '{__monitor_id}','{__monitoreo_fecha_completa}')"></i>
		</td>
	</tr>
</table>
<div style="display:none" id="pasos_regplus_{__monitor_id}_{__monitoreo_fecha_completa}">
	<table width="100%">
		<tr>
			<td class="celdaborde3" style="padding:15px 15px 25px 15px; background-color: #d0d0d0;">
				<table width="100%" class="listado_mini">
					<tr>
						<th	width="140">Paso / IP</th>							
						<th width="200">Registros</th>
						<th width="160">Patrón</th>
						<th width="80" align="center">Estado</th>
						<th width="70" align="center">Inverso / Obligatorio</th>
					</tr>
					
					<!-- BEGIN BLOQUE_PASOS -->
					
					<!-- BEGIN BLOQUE_PATRONES -->
					<tr>
					
						<!-- BEGIN BLOQUE_PASO_NOMBRE -->
						<td rowspan="{__paso_rowspan}" title="{__paso_nombre}">
							<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 135px;">{__paso_nombre}</div>
							<span style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8px; color: #525252;"><b>{__paso_ip}</b></span>
						</td>
						<td rowspan="{__paso_rowspan}">
							<!-- BEGIN BLOQUE_REGISTROS -->
								<div onclick="verRegistro('{__registro_valor}','{__monitoreo_fecha}','{__registro_nombre}');" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 195px;">{__registro_nombre} : {__registro_valor}</div>
								<br>
							<!-- END BLOQUE_REGISTROS -->
						</td>
						<!-- END BLOQUE_PASO_NOMBRE -->
						
						<td title="{__patron_nombre}">
							<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 155px;">{__patron_nombre}</div>
						</td>
						<td>
							<table width="100%">
								<tr>
									<td style="background-color: #{__evento_color};" align="center" height="22" id="tooltip_{__monitor_id}_{__monitoreo_fecha_completa}_{__paso_id}_{__patron_id}">					
						       			<i class="{__evento_imagen}" />
									</td>
								</tr>								
							</table>
							<div dojoType="dijit.Tooltip" connectId="tooltip_{__monitor_id}_{__monitoreo_fecha_completa}_{__paso_id}_{__patron_id}" position="below">
								<table>
									<tr>
										<td colspan="100%" class="textnegro13" height="22" valign="top">{__evento_nombre}</td>
									</tr>
									<tr>
										<td  align="center" >
											<table>
												<tr>
													<td height="22" width="80" style="background-color: #{__evento_color};" align="center">
													<i class="{__evento_imagen}" /></td>
												</tr>
											</table>
										</td>
										<td>&nbsp;&nbsp;</td>
										<td width="170" class="textnegro12">{__evento_descripcion}</td>
									</tr>
								</table>
							</div>
						</td>
						<td align="center">{__patron_inverso} / {__patron_opcional}</td>
					</tr>
					<!-- END BLOQUE_PATRONES -->	
					
					<!-- END BLOQUE_PASOS -->						
				</table>
			</td>
		</tr>
	</table>
</div>
<!-- END BLOQUE_MONITOREOS -->

<table align="right" class="celdabordederecha">
	<tr>
		<td>
			<input type="button" class="{__class_boton_atras}" {__disabled_atras} 
			 onClick="cargarItem('subcontenedor_regplus_{__monitor_id}', '{__item_id}', 0, ['monitor_id', '{__monitor_id}', 'pagina', '{__pagina_atras}']); return false;">
		</td>
		<td class="celdanegra50" width="20" align="center">{__pagina}</td>
		<td>
			<input type="button" class="{__class_boton_adelante}" id="boton_adelante_{__monitor_id}" {__disabled_adelante}
			 onClick="cargarItem('subcontenedor_regplus_{__monitor_id}', '{__item_id}', 0, ['monitor_id', '{__monitor_id}', 'pagina', '{__pagina_adelante}']); return false;">
		</td>
	</tr>
</table>
<br>
<br>