<input type="hidden" name="objetivo_id" value="0">
<table width="100%">
	<tr>
		<td class="tituloseccion">{__seccion_titulo}</td>
	</tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
    	<td>
    		<table width="100%">
    			<tr>
					<td class="celdanegra50" style="border-left: solid 1px #ffffff; border-right: solid 1px #ffffff;">Objetivos de monitoreo</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" class="listado">
				<tr>
					<th width="210">Nombre</th>
					<th width="260">Descripci&oacute;n</th>
					<th width="150">Servicio</th>
					<th width="30">&nbsp;</th>
				</tr>
				<!-- BEGIN LISTA_OBJETIVOS -->
				<tr>
					<td><div style="white-space: nowrap; overflow: hidden; width: 205px;">{__objetivo_nombre}</div></td>
					<td><div style="white-space: nowrap; overflow: hidden; width: 255px;">{__objetivo_descripcion}</div></td>
					<td>{__servicio_nombre}</td>
					<td align="center">
						<a href="#" onclick="abrirAccion(0,'modificar_objetivo',['objetivo_id','{__objetivo_id}']); return false;">
                                                    <i class="spriteButton spriteButton-ver" border="0" title="Informaci&oacute;n Objetivo" ></i></a>
					</td>
				</tr>
				<!-- END LISTA_OBJETIVOS -->
			</table>
		</td>
	</tr>
</table>
<br>
