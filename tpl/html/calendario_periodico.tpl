<input type="hidden" name="fecha_inicio_periodico" id="fecha_inicio_periodico" value="{__fecha_inicio_periodo}">
<input type="hidden" name="fecha_termino_periodico" id="fecha_termino_periodico" value="{__fecha_termino_periodo}">
<table style="border-spacing: 2px; border-collapse: separate;">
	<tr>
		<td class="titulo_calendario" colspan="4">Periodos</td>
	</tr>
	<tr>
		<td colspan="4">
			<table width="100%">
				<tr>
					<td width="20" class="fechas">
						<input type="button" class="spriteButton spriteButton-atras" onclick="{__nombre_clase}.cargarCalendario('{__fecha_anterior}','{__fecha_anterior}'); return false;">
					</td>
					<td class="fechas">{__anno}</td>
					<td width="20" class="fechas">
						<input type="button" class="spriteButton spriteButton-adelante" onclick="{__nombre_clase}.cargarCalendario('{__fecha_siguiente}','{__fecha_siguiente}'); return false;">
					</td>
				</tr>
			</table>
		</td>
	</tr>
 	<tr>
		<td colspan="4"></td>
	</tr>
	<tr>
		<td colspan="4" height="2" bgcolor="#a2a2a2"></td>
	</tr>
	<tr>
		<td colspan="4"></td>
	</tr>
	<!-- BEGIN LISTA_CUATRIMESTRE -->
	<tr>
		<!-- BEGIN LISTA_MESES_NOMBRE -->
		<td class="{__mes_class}" bgcolor="{__mes_color}" width="100" align="center" onclick="{__mes_script}">{__mes_nombre}</td>
		<!-- END LISTA_MESES_NOMBRE -->
	</tr>
	<!-- END LISTA_CUATRIMESTRE -->
	<!-- BEGIN LISTA_MESES -->
 	<tr>
		<td colspan="4"></td>
	</tr>
	<tr>
		<td colspan="4" height="2" bgcolor="#a2a2a2"></td>
	</tr>
	<tr>
		<td colspan="100%"></td>
	</tr>
	<tr>
		<td colspan="4">
			<table width="100%">
				<tr>
					<td class="calendario" valign="top" height="140" width="135">
						<table>
							<tr>
								<td class="mes" colspan="8">Diarios</td>
							</tr>
							<tr>
								<td class="nombre_dia">&nbsp;</td>
								<td class="nombre_dia">Lu</td>
								<td class="nombre_dia">Ma</td>
								<td class="nombre_dia">Mi</td>
								<td class="nombre_dia">Ju</td>
								<td class="nombre_dia">Vi</td>
								<td class="nombre_dia">Sa</td>
								<td class="nombre_dia">Do</td>
							</tr>
							<!-- BEGIN LISTA_SEMANAS -->
							<tr>
								<td class="{__semana_class}" id="{__semana_id}" onclick="{__semana_script}">{__semana}</td>
								<!-- BEGIN LISTA_DIAS -->
								<td class="{__dia_class}" id="{__dia_id}" onclick="{__dia_script}">{__dia}</td>
								<!-- END LISTA_DIAS -->
							</tr>
							<!-- END LISTA_SEMANAS -->
						</table>
					</td>
					<td width="5" height="140"></td>
					<td class="calendario" valign="top" height="140">
						<table width="100%">
							<tr>
								<td class="mes" colspan="3">Semanales / Mensuales</td>
							</tr>
							<tr>
								<td class="nombre_dia" width="40%">Tipo</td>
								<td class="nombre_dia" width="30%">Inicio</td>
								<td class="nombre_dia" width="30%">Termino</td>
							</tr>
							<!-- BEGIN LISTA_REPORTES -->
							<tr>
								<td colspan="3">
									<div id="{__reporte_id}" onclick="{__reporte_script}">
										<table width="100%">
											<tr>
							 					<td class="periodos" width="40%">{__reporte_nombre}</td>
												<td class="periodos" width="30%" align="center">{__reporte_inicio}</td>
												<td class="periodos" width="30%" align="center">{__reporte_termino}</td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
							<!-- END LISTA_REPORTES -->
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- END LISTA_MESES -->
</table>