<script>
<!--
function cambiarMonitorElementosEstadisticas(monitor_id) {
	if (monitor_id==0) {
		monitor_elementos_estadisticas_anterior = {__monitor_seleccion};
		monitor_id = {__monitor_seleccion};
	}
	
	document.getElementById("monitor_elementos_estadisticas_"+monitor_elementos_estadisticas_anterior).style.backgroundColor="#f0ede8";
	document.getElementById("monitor_elementos_estadisticas_"+monitor_elementos_estadisticas_anterior).style.color="#525252";
	document.getElementById("monitor_elementos_estadisticas_"+monitor_id).style.backgroundColor="#f36f00";
	document.getElementById("monitor_elementos_estadisticas_"+monitor_id).style.color="#ffffff";
	monitor_elementos_estadisticas_anterior = monitor_id;

	<!-- BEGIN LISTA_MONITORES_BORRAR -->
	document.getElementById("monitor_elementos_estadisticas_sel_{__monitor_id}").style.display="none";
	<!-- END LISTA_MONITORES_BORRAR -->
	document.getElementById("monitor_elementos_estadisticas_sel_"+monitor_id).style.display="inline";
}
//-->
</script>

<table width="100%">
	<tr>
		<td class="celdanegra50 celdaborde3">{__monitor_nombre}</td>
	</tr>
</table>
<div id="monitor_elementos_estadisticas_sel_{__monitor_id}">
<table width="100%">
	<tr>
		<td class="celdaborde celdanegra40" width="255">Elemento</td>
		<td class="celdaborde celdanegra40" align="center">Tipo</td>
		<td class="celdaborde celdanegra40" align="center">Tamaño<br>(KB)</td>
		<td class="celdaborde celdanegra40" align="center">Minimo<br>(Segs)</td>
		<td class="celdaborde celdanegra40" align="center">Maximo<br>(Segs)</td>
		<td class="celdaborde celdanegra40" align="center">Promedio<br>(Segs)</td>
		<td class="celdaborde" width="130">
			<table width="100%">
				<tr>
					<td class="celdanegra40" width="65%" align="center">Codigo</td>
					<td width="2"></td>
					<td class="celdanegra40" align="center">Cant.<br>Parcial</td>
				</tr>
			</table>
		</td>
		<td class="celdaborde celdanegra40" align="center">Cant.<br>Total</td>
	</tr>
	
	<!-- BEGIN LISTA_ELEMENTOS -->
	<tr>
		<td class="celdaborde {__class}">
			<div style="white-space: nowrap; overflow: hidden; width: 250px;">
				<a href="{__elemento_url}" class="textgris10" target="_blank">{__elemento_url}</a>
			</div>
		</td>
		<td class="celdaborde {__class}" align="center">
			<img title="{__elemento_tipo_nombre}" src="{__elemento_tipo_icono}">
		</td>
		<td class="celdaborde {__class}" align="right">{__elemento_tamanno}</td>
		<td class="celdaborde {__class}" align="right">{__elemento_minimo}</td>
		<td class="celdaborde {__class}" align="right">{__elemento_maximo}</td>
		<td class="celdaborde {__class}" align="right">{__elemento_promedio}</td>
		<td class="celdaborde">
			<table width="100%">
			
			<!-- BEGIN LISTA_EVENTOS -->
			<tr>
				<td align="center" bgcolor="#{__evento_color}" width="65%" height="25">
					<i class="{__evento_icono}"></i>
				</td>
				<td width="2"></td>
				<td class="{__class}" align="right" height="25">{__evento_cantidad}</td>
			</tr>
			<!-- END LISTA_EVENTOS -->
			
			</table>
		</td>
		<td class="celdaborde {__class}" align="right">{__elemento_cantidad}</td>
	</tr>
	<!-- END LISTA_ELEMENTOS -->
	
</table>
</div>
<table align="right" class="celdabordederecha">
	<tr>
		<td><input type="button" class="{__class_boton_atras}"
			{__disabled_atras}
			 onClick="cargarItem('subcontenedor_elem_estadistica_{__monitor_id}', '{__item_id}', 0, ['monitor_id', '{__monitor_id}', 'pagina', '{__pagina_atras}']); return false;">
		</td>
		<td class="celdanegra50" width="20" align="center">{__pagina}</td>
		<td><input type="button" class="{__class_boton_adelante}"
			id="boton_adelante_{__monitor_id}"
			{__disabled_adelante}
			 onClick="cargarItem('subcontenedor_elem_estadistica_{__monitor_id}', '{__item_id}', 0, ['monitor_id', '{__monitor_id}', 'pagina', '{__pagina_adelante}']); return false;">
		</td>
	</tr>
</table>
<br>
<br>
