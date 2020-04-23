<input type="hidden" name="fecha_inicio" id="fecha_inicio" value="">
<input type="hidden" name="fecha_termino" id="fecha_termino" value="">
<table style="border-spacing: 2px; border-collapse: separate;">
	<tr>
		<td class="titulo_calendario" colspan="3">Calendario</td>
	</tr>
	<tr>
		<!-- BEGIN LISTA_MESES -->
		<td valign="top" class="calendario" height="140">
			<table>JSN
				<tr>
					<td class="{__mes_class}" colspan="100%" id="{__mes_id}" onclick="{__mes_script}">{__mes}</td>
				</tr>
				<tr>
					<td class="nombre_dia">&nbsp;</td>
					<td class="nombre_dia">Lunn</td>
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
					<td class="{__dia_class}" id="{__dia_id}" onclick="{__dia_default}{__dia_script}">{__dia}</td>
					<!-- END LISTA_DIAS -->
				</tr>
				<!-- END LISTA_SEMANAS -->
			</table>
		</td>
		<!-- END LISTA_MESES -->
	</tr>
	
</table>
