<script>

function agregarPonderacionItem() {
	var intervalo = document.getElementById("intervalo_id");
	var inicio = document.getElementById("item_inicio");
	var termino = document.getElementById("item_termino");

	if (parseFloat(inicio.options[inicio.selectedIndex].value) >= parseFloat(termino.options[termino.selectedIndex].value)) {
		alert("El horario de inicio debe ser menor que el horario de termino.");
		return false;
	}

	abrirDetalles('detalle_ponderacion',
				  'mostrar_ponderacion_detalle', 
				  ['intervalo_id', intervalo.options[intervalo.selectedIndex].value, 
				   'item_inicio', inicio.options[inicio.selectedIndex].value,  
				   'item_termino', termino.options[termino.selectedIndex].value]);
}

function quitarPonderacionItem(item_inicio) {
	var intervalo = document.getElementById("intervalo_id");
	
	abrirDetalles('detalle_ponderacion',
			  'mostrar_ponderacion_detalle', 
			  ['intervalo_id', intervalo.options[intervalo.selectedIndex].value, 
			   'item_inicio_quitar', item_inicio]);
}

<!-- BEGIN MOSTRAR_ERROR -->
alert("El intervalo ingresado ya existe.");
<!-- END MOSTRAR_ERROR -->

</script>
<table width="100%" class="listado">
	<tr>
		<th width="170">Inicio</th>
		<th width="170">Termino</th>
		<th width="170">Intervalo</th>
		<th>Ponderacion</th>
		<th width="30">&nbsp;</th>
	</tr>
	<!-- BEGIN LISTA_ITEMS -->
	<tr>
		<td {__item_class}>{__item_inicio}</td>
		<td {__item_class}>{__item_termino}</td>
		<td {__item_class}>{__item_intervalo}</td>
		<td {__item_class}><input type="text" size="6" name="ponderacion_{__item_hora_inicio}_{__item_hora_termino}" value="{__item_valor}" {__item_disabled}/>&nbsp;%</td>
		<td {__item_class} align="center">
		<!-- BEGIN PUEDE_ELIMINAR -->
			<a href="#" onclick="quitarPonderacionItem('{__item_hora_inicio}');">
			<i class="spriteButton spriteButton-borrar" border="0"></i></a>
		<!-- END PUEDE_ELIMINAR -->
		</td>
	</tr>
	<!-- END LISTA_ITEMS -->
	<!-- BEGIN PUEDE_AGREGAR -->
	<tr>
		<th>
			<select name="item_inicio" id="item_inicio">
				<option value="0">00:00:00</option>
				<!-- BEGIN LISTA_HORAS -->
				<option value="{__hora_id}">{__hora_nombre}</option>
				<!-- END LISTA_HORAS -->
			</select>
		</th>
		<th>
			<select name="item_termino" id="item_termino">
				<!-- BEGIN LISTA_HORAS -->
				<option value="{__hora_id}">{__hora_nombre}</option>
				<!-- END LISTA_HORAS -->
				<option value="24">24:00:00</option>
			</select>
		</th>
		<th></th>
		<th></th>
		<th align="center"><a href="#" onclick="agregarPonderacionItem(); return false;"><i class="spriteButton spriteButton-nuevo" border="0"></i></a></th>
	</tr>
	<!-- END PUEDE_AGREGAR -->
</table>