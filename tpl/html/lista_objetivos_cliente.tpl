<input type="hidden" name="objetivo_id" value="0">
<table width="100%">
	<tr>
		<td class="tituloseccion">{__sitio_titulo}</td>
	</tr>
    <tr>
      <td>&nbsp;</td>
    </tr>

    <!-- BEGIN LISTA_SERVICIOS_ONLINE -->
	<tr>
		<td>
    		<table width="100%">
    			<tr>
					<td class="celdanegra50" style="border-left: solid 1px #ffffff; border-right: solid 1px #ffffff;">{__nombre_de_servicio}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" class="listado">
				<tr>
					<th width="240">Nombre</th>
					<th width="370">Descripci&oacute;n</th>
					<th width="30">&nbsp;</th>
				</tr>
				<!-- BEGIN LISTA_OBJETIVOS_ONLINE -->
				<tr>
					<td><div style="white-space: nowrap; overflow: hidden; width: 235px;">{__objetivo_online_nombre}</div></td>
					<td><div style="white-space: nowrap; overflow: hidden; width: 365px;">{__objetivo_online_descripcion}</div></td>
					<td align="center">
						<a href="#" onclick="abrirAccion(0,'modificar_objetivo',['objetivo_id','{__objetivo_online_id}']); return false;">
						<i class="{__form_icon_detail} border="0" title="{__form_label_detail}"></i></a>
					</td>
				</tr>
				<!-- END LISTA_OBJETIVOS_ONLINE -->
			</table>
		</td>
	</tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <!-- END LISTA_SERVICIOS_ONLINE -->
</table>
<br>
