<table style="width: 100%;">
	<tr>
		<td class="celdanegra50 celdaborde3">{__monitor_nombre}</td>
	</tr>
	<tr>
		<td>
			<table width="100%">
				<tr>					
					<td class="celdanegra40 celdaborde3">Fecha</td>
					<td class="celdanegra40 celdaborde3" width="80">Estado</td>
					<td class="celdanegra40 celdaborde3" width="30"></td>
				</tr>
			</table>
			<!-- BEGIN BLOQUE_DATOS -->
			<table width="100%" >
				<tr>					
					<td class="{__class} celdaborde3">{__fecha}</td>
					<td class="{__class} celdaborde3" width="80" align="center" >
						<i class="{__estado}" title="{__titleMonitoreo}"></i>
					</td>
					<td class="{__class} celdaborde3" width="30" id="td_elem_{__monitor_id}_{__fechaCompleta}" align="center">
						<i id="imagen_elem_{__monitor_id}_{__fechaCompleta}" class="spriteButton spriteButton-abrir_calendario" onclick="mostrarDetallePlus('elem','{__monitor_id}','{__fechaCompleta}')" style="cursor:pointer" title="Mostrar Detalle"></i>
					</td>
				</tr>
			</table>
                        <input id="download"type="text" style="display:none" value="false">
			<div id="pasos_elem_{__monitor_id}_{__fechaCompleta}" style="display:none">
				<table width="100%">
					<tr>
						<td class="celdaborde3" style="padding:5px 5px 5px 5px ; background-color: #d0d0d0;">				
							<table class="listado_mini" width="100%">
								<tr>				    		
									<th width="200">Paso</th>
									<th width="100">Tamaño Total (KBytes)</th>
									<th width="100">Tiempo Total (Segs)</th>
                                                                        <th width="83">Estado Elementos</th>
                                                                        <th width="40"></th>
                                                                        <th width="40"></th>
									<th width="40"></th>
								</tr>
							</table>
							<!-- BEGIN BLOQUE_PASOS -->
							<table width="100%" class="listado_mini">
								<tr>									
									<td width="200" style="border-bottom:0">{__nombrePaso}</td>
									<td align="right" width="100" style="border-bottom:0">{__tamanoTotal}</td>
									<td align="right" width="100" style="border-bottom:0">{__tiempoTotal}</td>
                                                                        <td align="center" width="83" style="border-bottom:0"> <i class="{__estadoPaso}" title="{__titlePaso}"></i></td>
									<td align="center" width="40" style="border-bottom:0; cursor:pointer"><i class="spriteButton spriteButton-imprimir_gris"title="Imprimir" onclick="abrirPopup(['reporte_id', '2', 'imprimir_item_id', '{__item_id_nuevo}', 'monitor_id', '{__monitor_id}', 'paso_id', {__idPaso}, 'fecha_monitoreo', '{__fechaCompleta}']);"></i></td>
                                                                        <td align="center" width="40"><i class="spriteButton spriteButton-exportar_plomo" title="Descargar Csv" id="downloadCsv" onclick="mostrarGraficoPlus('{__item_id_nuevo}', '{__monitor_id}', '{__idPaso}', '{__fechaCompleta}', 'true');"></i></td>
                                                                        <td align="center" width="40" style="border-bottom:0"><i id="flecha_{__monitor_id}_{__idPaso}_{__fechaCompleta}" class="spriteButton spriteButton-abrir_calendario" onclick="mostrarGraficoPlus('{__item_id_nuevo}', '{__monitor_id}', '{__idPaso}', '{__fechaCompleta}', 'false');" style="cursor:pointer" title="Ver Gr&aacute;fico Elementos"></i></td>
								</tr>
							</table>
							<div id="elemplus_{__monitor_id}_{__idPaso}_{__fechaCompleta}" dojotype="dojox.layout.ContentPane" style="width: 100%;background-color: #ffffff; display:none"></div>						
							<!-- END BLOQUE_PASOS -->							
						</td>
					</tr>
				</table>
			</div>				
			<!-- END BLOQUE_DATOS -->
		</td>
	</tr>
</table>
<table align="right" class="celdabordederecha">
	<tr>
		<td><input type="button" class="{__class_boton_atras}"
			{__disabled_atras}
			  onClick="cargaItemFiltroElementosPlus('subcontenedor_elem_'+{__monitor_id},{__item_id},0,['monitor_id', '{__monitor_id}','pagina', {__pagina_atras}],[ {_h1}, {_m1}, {_h2}, {_m2}]); return false;">
		</td>
		<td class="celdanegra50" width="20" align="center">{__pagina}</td>
		<td><input type="button" class="{__class_boton_adelante}"
			id="boton_adelante_{__monitor_id}"
			{__disabled_adelante}
			 onClick="cargaItemFiltroElementosPlus('subcontenedor_elem_'+{__monitor_id},{__item_id},0,['monitor_id', '{__monitor_id}','pagina', {__pagina_adelante}],[ {_h1}, {_m1}, {_h2}, {_m2}]); return false;">
		</td>
	</tr>
</table>