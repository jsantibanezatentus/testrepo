<div style="page-break-inside: avoid;">
<table width="100%" align="center">
	<tr>
		<td colspan="100%" class="txtNegro13" style="padding: 6px; border: solid 1px #ffffff;">{__item_orden}.{__horario_orden}. {__horario_nombre}</td>
	</tr>
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris">D&iacute;a de la Semana</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">Hora Inicio</td>
		<td class="txtBlanco12b celdaTituloNaranjo" align="center">Hora Termino</td>		
	</tr>
	<!-- BEGIN BLOQUE_HORARIO -->
	<tr>
		<!-- BEGIN ES_PRIMERO_DIA -->
		<td class="txtGris12 celdaIteracion1" rowspan="{__dia_rowspan}">{__dia}</td>
		<!-- END ES_PRIMERO_DIA -->
		<td class="txtGris12 {__print_class}" width="200" align="center">{__horaInicio}</td>
		<td class="txtGris12 {__print_class}" width="200" align="center">{__horaTermino}</td>		
	</tr>	
	<!-- END BLOQUE_HORARIO -->
	<!-- BEGIN BLOQUE_TODO_HORARIO -->
	<tr>
		<td class="txtGris12 celdaIteracion1">Lunes - Domingo</td>
		<td class="txtGris12 celdaIteracion1" width="200" align="center">00:00:00</td>
		<td class="txtGris12 celdaIteracion1" width="200" align="center">24:00:00</td>
	</tr>	
	<!-- END BLOQUE_TODO_HORARIO -->
</table>
</div>