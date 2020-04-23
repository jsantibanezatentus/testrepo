<table width="100%">
	<tr>
		<td class="celdanegra50 celdaborde3">{__monitor_nombre}</td>
	</tr>
</table>
<table width="100%" >

	<tr>		
		<td class="celdaborde celdanegra40" height="30">Fecha Monitoreo</td>
		<td class="celdaborde celdanegra40" height="30" width="80" align="center">Elementos</td>
		<td class="celdaborde celdanegra40" height="30" width="80" align="center">Tamaño<br>(KB)</td>
		<td class="celdaborde celdanegra40" height="30" width="80" align="center">Respuesta<br>(segs)</td>
		<td class="celdaborde celdanegra40" height="30" width="80" align="center">Estado</td>
		<td class="celdaborde celdanegra40" height="30" width="30"></td>
	</tr>
	<!-- BEGIN LISTA_MONITOREOS -->
	<tr>
		<td class="celdaborde {__class}" height="22">{__monitoreo_fecha_mostrar}</td>
		<td class="celdaborde {__class}" align="right">{__monitoreo_elementos}</td>
		<td class="celdaborde {__class}" align="right">{__monitoreo_tamanno}</td>
		<td class="celdaborde {__class}" align="right">{__monitoreo_respuesta}</td>
		<td class="celdaborde" align="center" id="vista_{__tooltip_id}" bgcolor="#{__monitoreo_estado_color}">
			<i class="{__monitoreo_estado_icono}">

			<!-- BEGIN TIENE_TOOLTIP -->
			<div dojoType="dijit.Tooltip" connectId="vista_{__tooltip_id}" position="below">
				<table>
					<tr>
						<td width="80" height="26" align="center" style="background-color: #{__monitoreo_estado_color};"><i class="{__monitoreo_estado_icono}" /></td>
						<td width="170" class="textnegro13" style="padding: 3px;">{__monitoreo_estado_nombre}</td>
					</tr>
					<tr>
						<td colspan="2" class="textnegro12" style="padding: 3px;">{__monitoreo_estado_descripcion}</td>
					</tr>
				</table>
			</div>
			<!-- END TIENE_TOOLTIP -->

		</td>
		<td class="celdaborde {__class}" align="center">
			<a href="#" onclick="cargarSubItem('subcontenedor_elem_{__monitor_id}', 'subcontenedor_detelem_{__monitor_id}', '{__item_id}', '{__item_id_nuevo}', ['monitor_id', '{__monitor_id}', 'fecha_monitoreo', '{__monitoreo_fecha}', 'pagina', '{__pagina}']); return false;">
				<img src="{__path_img_boton}listado.png" border="0" />
			</a>
		</td>
	</tr>
	<!-- END LISTA_MONITOREOS -->
</table>

<table align="right" class="celdabordederecha">
	<tr>
		<td><input type="button" class="{__class_boton_atras}"
			{__disabled_atras}
			 onClick="cargarItem('subcontenedor_elem_{__monitor_id}', '{__item_id}', 0, ['monitor_id', '{__monitor_id}', 'pagina', '{__pagina_atras}']); return false;">
		</td>
		<td class="celdanegra50" width="20" align="center">{__pagina}</td>
		<td><input type="button" class="{__class_boton_adelante}"
			id="boton_adelante_{__monitor_id}"
			{__disabled_adelante}
			 onClick="cargarItem('subcontenedor_elem_{__monitor_id}', '{__item_id}', 0, ['monitor_id', '{__monitor_id}', 'pagina', '{__pagina_adelante}']); return false;">
		</td>
	</tr>
</table>
<br><br>