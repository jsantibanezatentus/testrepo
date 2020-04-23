<table>
	<tr>
		<td width="750px">
			<div width="750px">
				<table width="100%">
					<tr>
						<!-- BEGIN TITULOS_OBJETIVOS -->
						<td valign="top">
							<table width="100%">
								<tr>
									<td>
										<tr>
											<td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #ffffff;" class="textblanco12b"></td>
										</tr>
										<tr>
											<td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco12b">Objetivo</td>
										</tr>
										<tr>
											<td height="20" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco9">&nbsp;</td>
										</tr>
										<tr>
											<td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #{__objetivo_color};" class="textblanco12" id="objetivo_{__objetivo_id}">
												<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 180px;">{__objetivo_nombre}</div>
											</td>
										</tr>
										{__pasos}
									</td>
								</tr>
							</table>
						</td>
						<td valign="top" style="z-index: 99999999">
							<div id="elementos_scroll" style="width: 100%; ">
								<table>
									<tr>
										<td colspan="{__cant_nodos}" height="26" align="center" style="padding: 2px; border-right: solid 0px #{__objetivo_color}; background-color: #{__objetivo_color};" class="textblanco12b">
											<div name="elemento_pagina" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: {__tamaño_titulo_objetivo}px;">{__objetivo_nombre}</div>
										</td>
									</tr>
									<tr>
										<!-- BEGIN LISTA_NODOS -->
										<td valign="top">
											<table>
												<tr>
													<td height="26" align="center" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco12b">
														<div name="elemento_pagina" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 80px;">{__nodo_nombre}</div>
													</td>
												</tr>
												<tr>
													<td height="20" align="right" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f58b32;" class="textblanco9" id="ubicacion_{__grupo_id}_{__nodo_id}">
														<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 80px;">{__nodo_ubicacion}</div>
													</td>
												</tr>
												<tr>
													<td height="26" align="center" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #{__objetivo_color};" class="textblanco12">{__evento_fecha}</td>
												</tr>
												<!-- BEGIN LISTA_PASOS -->
												<!-- BEGIN LISTA_PATRONES -->
												<tr>
													<td height="26" align="center" id="vista_{__tooltip_id}" class="tooltip" style="padding: 2px; border-right: solid 2px #ffffff; background-color:#{__evento_color};">
														<div style="width: 80px;">
															<i class="{__evento_icono}" />
														</i>
													</div>
													<table class="top">
														<tr>
															<td width="80" height="26" align="center" style="background-color: #{__evento_color};">
																<i class="{__evento_icono}" />
															</i>
														</td>
														<td width="170" class="textnegro13" style="padding: 3px;">{__evento_nombre}</td>
													</tr>
													<tr>
														<td colspan="2" class="textnegro12" style="padding: 3px;">{__evento_descripcion}</td>
													</tr>
													<tr>
														<td colspan="2" style="padding: 3px;">
															<span class="textnegro10b">Duraci&oacute;n:</span>
															<span class="textnegro10">{__tooltip_duracion}</span>
															<br>
															<span class="textnegro10b">Respuesta:</span>
															<span class="textnegro10">{__tooltip_respuesta}</span>
															<br>
															<span class="textnegro10b">Patron:</span>
															<span class="textnegro10">{__tooltip_patron}</span>
														</td>
													</tr>
												</table>
												<!-- END LISTA_PATRONES -->
												<tr>
													<td height="26" align="center" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #e9e9e9;" class="textgris12">{__evento_respuesta}</td>
												</tr>
												<!-- END LISTA_PASOS -->
											</table>
										</td>
										<!-- END LISTA_NODOS -->
									</tr>
								</table>
							</div>
						</td>
						<!-- END TITULOS_OBJETIVOS -->
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>
<script type="text/javascript">
	function actualizar(){
		location.reload(true);
	}
	setInterval("actualizar()",300000);
</script>
<style>
	.tooltip {
		position:relative;
	}

	.tooltip .top {
		width: 300px;
		top: 0px;
		left:100%;
		color:#000;
		background-color:#fff;
		font-weight:normal;
		font-size:14px;
		position:absolute;
		z-index:99999999;
		box-sizing:border-box;
		box-shadow:0 1px 8px rgba(0,0,0,0.5);
		display:none;
	}

	.tooltip:hover .top {
		display:block;
	}
</style>