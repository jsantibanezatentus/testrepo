<table width="100%" align="center">
	<tr>
		<td colspan="100%" style="border: solid 1px #ffffff;" class="celdanegra50">{__horario_nombre}</td>
	</tr>
	<tr>
		<td style="border: solid 1px #ffffff;" class="celdanegra40">D&iacute;a de la Semana</td>
		<td style="border: solid 1px #ffffff;" class="celdanegra40"  align="center">Hora Inicio</td>
		<td style="border: solid 1px #ffffff;" class="celdanegra40"  align="center">Hora Termino</td>		
	</tr>
	<!-- BEGIN BLOQUE_HORARIO -->
	<tr>
		<!-- BEGIN ES_PRIMERO_DIA -->
		<td style="border: solid 1px #ffffff;" class="celdanegra10" rowspan="{__dia_rowspan}">{__dia}</td>
		<!-- END ES_PRIMERO_DIA -->
		<td style="border: solid 1px #ffffff;" class="{__class}"  align="center">{__horaInicio}</td>
		<td style="border: solid 1px #ffffff;" class="{__class}"  align="center">{__horaTermino}</td>		
	</tr>	
	<!-- END BLOQUE_HORARIO -->
	<!-- BEGIN BLOQUE_TODO_HORARIO -->
	<tr>
		<td style="border: solid 1px #ffffff;" class="celdanegra10">Lunes - Domingo</td>
		<td style="border: solid 1px #ffffff;" class="celdanegra10"  align="center">00:00:00</td>
		<td style="border: solid 1px #ffffff;" class="celdanegra10"  align="center">23:59:59</td>		
	</tr>	
	<!-- END BLOQUE_TODO_HORARIO -->
</table>