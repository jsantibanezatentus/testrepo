
<!-- BEGIN BLOQUE_TABLA -->
<table width="100%">
	<!-- BEGIN BLOQUE_TITULO_HORARIOS -->
	<tr>		
		<td class="celdaborde celdanegra50" align="left" colspan="5">{__horario_nombre}</td>
	</tr>
	<!-- END BLOQUE_TITULO_HORARIOS -->
	<tr>
		<td class="celdaborde celdanegra40">Dia</td>
		<td class="celdaborde celdanegra40">Paso</td>
		<td class="celdaborde celdanegra40" width="120" align="center">Minimo (Segs)</td>
		<td class="celdaborde celdanegra40" width="120" align="center">Maximo (Segs)</td>
		<td class="celdaborde celdanegra40" width="120" align="center">Promedio (Segs)</td>
	</tr>
	<!-- BEGIN LISTA_PASOS -->
	<tr>
		<!-- BEGIN ES_PRIMERO_DIA -->
		<td class="celdaborde celdanegra10" rowspan="{__dia_rowspan}">{__dia_nombre}</td>
		<!-- END ES_PRIMERO_DIA -->
		<td class="celdaborde {__class}">{__paso_nombre}</td>
		<td class="celdaborde {__class}" align="right">{__paso_minimo}</td>
		<td class="celdaborde {__class}" align="right">{__paso_maximo}</td>
		<td class="celdaborde celdanegra20" align="right">{__paso_promedio}</td>
	</tr>
	<!-- END LISTA_PASOS -->
</table>
<br>
<!-- END BLOQUE_TABLA -->
