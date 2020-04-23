<!-- BEGIN BLOQUE_TABLA -->
<div id="{_horario_id}">
	<table width="100%" id="{_horario_id}">
		<tr>
			<td class="celdaTituloNaranjo txtBlanco12b" style="border: 1px solid #a2a2a2; font-family: 'Varela Round', sans-serif;">Monitor</td>
			<td class="celdaTituloNaranjo txtBlanco12b" style="border: 1px solid #a2a2a2;">Paso</td>
			<td class="celdaTituloGris txtBlanco12b" style="border: 1px solid #a2a2a2;">Mínima (segs.)</td>
			<td class="celdaTituloGris txtBlanco12b" style="border: 1px solid #a2a2a2;">Máxima (segs.)</td>
			<td class="celdaTituloGris txtBlanco12b" style="border: 1px solid #a2a2a2;">Promedio (segs.)</td>
			<td class="celdaTituloGris txtBlanco12b" style="border: 1px solid #a2a2a2;">Desv. Estándar (segs.)</td>
		</tr>
		<!-- BEGIN BLOQUE_DATOS -->
		<tr>
			<td class="{__celdaIteracion_nodo}" style="border: 0px;">{_nodo_nombre}</td>
			<td class="{_celdaIteracion}" style="border: 0px;font-family: 'Varela Round', sans-serif;">{_paso_orden}</td>
			<td class="{_celdaIteracion}" style="border: 0px;text-align: right; font-family: 'Varela Round', sans-serif;">{_respuestamin}</td>
			<td class="{_celdaIteracion}" style="border: 0px;text-align: right; font-family: 'Varela Round', sans-serif;">{_respuestamax}</td>
			<td class="{_celdaIteracion}" style="border: 0px;text-align: right; font-family: 'Varela Round', sans-serif;">{_respuestaprom}</td>
			<td class="{_celdaIteracion}" style="border: 0px;text-align: right; font-family: 'Varela Round', sans-serif;">{_respuestadesvest}</td>
		</tr>
		<!-- END BLOQUE_DATOS -->
	</table>
	<div style="height: 25px"></div>
	<table width="100%"  id="{_horario_id}">
		<tr>
			<td class="celdaTituloNaranjo txtBlanco15b" colspan="{__cant_monitores}" style="text-align: center; width: 20%; text-shadow: #000000 1px 2px 3px;">Promedio por monitor (segundos)</td>
		</tr>
		<tr>
			<!-- BEGIN BLOQUE_NOMBRE_NODOS -->
			<td class="celdaTituloGris txtBlanco15b" style="text-align: center; border: 1px solid #a2a2a2;">{_nombre_nodos}</td>
			<!-- END BLOQUE_NOMBRE_NODOS -->
		</tr>
		<tr>
			<!-- BEGIN BLOQUE_PROMEDIOS -->
			<td style="background-color: #dedede; text-align: right; font-family: 'Varela Round', sans-serif;">{_promedios}</td>
			<!-- END BLOQUE_PROMEDIOS -->
		</tr>
	</table>

	<div style="height: 25px"></div>
	<table width="100%"  id="{_horario_id}">
		<tr>
			<td class="celdaTituloNaranjo txtBlanco15b" colspan="5" style="text-align: center; width: 20%; border: 1px solid #a2a2a2; text-shadow: #000000 1px 2px 3px;">Promedios estadísticos por monitor (segundos)</td>
		</tr>
		<tr>
			<td class="celdaTituloGris txtBlanco12b" style="text-align: center; border: 1px solid #a2a2a2; font-family: 'Varela Round', sans-serif;">Monitor</td>
			<td class="celdaTituloGris txtBlanco12b" style="text-align: center; border: 1px solid #a2a2a2;">Mínima</td>
			<td class="celdaTituloGris txtBlanco12b" style="text-align: center; border: 1px solid #a2a2a2;">Máxima</td>
			<td class="celdaTituloGris txtBlanco12b" style="text-align: center; border: 1px solid #a2a2a2;">Promedio</td>
			<td class="celdaTituloGris txtBlanco12b" style="text-align: center; border: 1px solid #a2a2a2;">Desv. Estándar</td>
		</tr>
		<!-- BEGIN BLOQUE_PROMEDIOS_POR_MONITOR -->
		<tr>
			<td class="txtGris12b" style="text-align: left; background-color: #{__celdaIteracion_monitor}; padding: 1px 6px 1px 6px; font-size: 14px;">{__nombre_nodo}</td>
			<td style="background-color: #{__celdaIteracion_monitor}; text-align: right; font-family: 'Varela Round', sans-serif;">{__minimo_promedios}</td>
			<td style="background-color: #{__celdaIteracion_monitor}; text-align: right; font-family: 'Varela Round', sans-serif;">{__maximo_promedios}</td>
			<td style="background-color: #{__celdaIteracion_monitor}; text-align: right; font-family: 'Varela Round', sans-serif;">{__promedio_promedios}</td>
			<td style="background-color: #{__celdaIteracion_monitor}; text-align: right; font-family: 'Varela Round', sans-serif;">{__desv_estandar_promedios}</td>
		</tr>
		<!-- END BLOQUE_PROMEDIOS_POR_MONITOR -->
		<tr>
		</tr>
	</table>

	<div style="height: 25px"></div>

	<table width="100%" id="{_horario_id}">
		<tr>
			
			<td class="celdaTituloNaranjo txtBlanco15b" colspan="4" style="text-align: center; width: 20%; text-shadow: #000000 1px 2px 3px;">Promedio consolidado (segundos)</td>
		</tr>
		<tr>
			<td class="celdaTituloGris txtBlanco12b" style="text-align: center; border: 1px solid #a2a2a2;">Mínima</td>
			<td class="celdaTituloGris txtBlanco12b" style="text-align: center; border: 1px solid #a2a2a2;">Máxima</td>
			<td class="celdaTituloGris txtBlanco12b" style="text-align: center; border: 1px solid #a2a2a2;">Promedio</td>
			<td class="celdaTituloGris txtBlanco12b" style="text-align: center; border: 1px solid #a2a2a2;">Desv. Estándar</td>
		</tr>
		<tr>
			<td style="background-color: #dedede; text-align: right; font-family: 'Varela Round', sans-serif;">{_prom_min}</td>
			<td style="background-color: #dedede; text-align: right; font-family: 'Varela Round', sans-serif;">{_prom_max}</td>
			<td style="background-color: #dedede; text-align: right; font-family: 'Varela Round', sans-serif;">{_prom_prom}</td>
			<td style="background-color: #dedede; text-align: right; font-family: 'Varela Round', sans-serif;">{_prom_desvest}</td>
		</tr>
		<tr style="height: 20px"></tr>
	</table>
</div>
<!-- END BLOQUE_TABLA -->
<style type="text/css">
@font-face {
	font-family: 'Varela Round';
	font-style: normal;
	font-weight: 400;
	src: local('Varela Round Regular'), local('VarelaRound-Regular'), url(varela_round.woff2) format('woff2');
	unicode-range: U+0590-05FF, U+20AA, U+25CC, U+FB1D-FB4F;
}
</style>