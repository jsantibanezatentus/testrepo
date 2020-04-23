<table width="100%">
	<!-- BEGIN TIENE_MOSTRAR_DETALLES -->
	<tr>
		<td align="right">
			<a href="#" onclick="cargarItem('subcontenedor_reg_{__monitor_id}', '{__item_id}', '0', ['monitor_id', '{__monitor_id}', 'pagina', '{__pagina}', 'mostrar_detalles', '{__mostrar}']); return false;">
			Ver / Ocultar Detalles</a>
		</td>
	</tr>
	<!-- END TIENE_MOSTRAR_DETALLES -->
	<tr>
		<td class="celdaborde celdanegra50">{__monitor_nombre}</td>
	</tr>
</table>
<table width="100%">
	<tr>
		<td class="celdaborde celdanegra40">Fecha</td>
		<td class="celdaborde celdanegra40" width="80">Duracion</td>
		<td class="celdaborde celdanegra40" width="80">Estado</td>
		<td class="celdaborde celdanegra40" width="120" {__tiene_servidor}>Servidor</td>
		<td class="celdaborde celdanegra40" width="170" {__tiene_primario}>Dns Primario</td>
		<td class="celdaborde celdanegra40" width="90" {__tiene_serial}>Serial</td>
		<td class="celdaborde celdanegra40" width="260" {__tiene_detalles}>Detalles</td>
		<td class="celdaborde celdanegra40" width="170" {__tiene_nombres}>Nombre</td>
		<td class="celdaborde celdanegra40" width="80" {__tiene_tipos}>Tipo</td>
		<td class="celdaborde celdanegra40" width="60" {__tiene_prioridad}>Prioridad</td>
		<td class="celdaborde celdanegra40" width="170" {__tiene_respuestas}>Respuesta</td>
	</tr>
	<!-- BEGIN LISTA_REGISTROS -->
	<tr>
		<td class="celdaborde {__class}" nowrap>{__registro_fecha}</td>
		<td class="celdaborde {__class}">{__registro_duracion}</td>
		<td align="center" class="celdaborde" bgcolor="#{__registro_estado_color}">
			<i class="{__registro_estado_icono}">
		</td>
		<td class="celdaborde {__class}" {__tiene_servidor}>{__registro_servidor}</td>
		<td class="celdaborde {__class}" {__tiene_primario}>{__registro_primario}</td>
		<td class="celdaborde {__class}" {__tiene_serial}>{__registro_serial}</td>
		<td class="celdaborde {__class}" width="120" {__tiene_detalles}>
			Dns Primario: {__registro_primario}<br>
			Serial: {__registro_serial}<br>
			E-Mail: {__registro_email}<br>
			Refresh: {__registro_refresh}<br>
			Retry: {__registro_retry}<br>
			Expire: {__registro_expire}<br>
			Minimun: {__registro_minimum}
		</td>
		<td class="celdaborde {__class}" {__tiene_nombres}>
			<!-- BEGIN LISTA_NOMBRES -->
			{__registro_nombre}&nbsp;<br>
			<!-- END LISTA_NOMBRES -->
		</td>
		<td class="celdaborde {__class}" {__tiene_tipos}>
			<!-- BEGIN LISTA_TIPOS -->
			{__registro_tipo}<br>
			<!-- END LISTA_TIPOS -->
		</td>
		<td class="celdaborde {__class}" {__tiene_prioridad}>
			<!-- BEGIN LISTA_PRIORIDAD -->
			{__registro_prioridad}<br>
			<!-- END LISTA_PRIORIDAD -->
		</td>
		<td class="celdaborde {__class}" {__tiene_respuestas}>
			<!-- BEGIN LISTA_RESPUESTAS -->
			{__registro_respuesta}<br>
			<!-- END LISTA_RESPUESTAS -->
		</td>
	</tr>
	<!-- END LISTA_REGISTROS -->
</table>
<table align="right" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<input type="button" class="{__class_boton_atras}"  {__disabled_atras}
			 onClick="cargarItem('subcontenedor_reg_{__monitor_id}', '{__item_id}', 0, ['monitor_id', '{__monitor_id}', 'pagina', '{__pagina_atras}', 'mostrar_detalles', '{__mostrar_actual}']); return false;">
		</td>
		<td class="celdanegra50" width="20" align="center">{__pagina}</td>
		<td>
			<input type="button" class="{__class_boton_adelante}" id="boton_adelante_{__monitor_id}" {__disabled_adelante}
			 onClick="cargarItem('subcontenedor_reg_{__monitor_id}', '{__item_id}', 0, ['monitor_id', '{__monitor_id}', 'pagina', '{__pagina_adelante}', 'mostrar_detalles', '{__mostrar_actual}']); return false;">
		</td>
	</tr>
</table>
<br>
<br>