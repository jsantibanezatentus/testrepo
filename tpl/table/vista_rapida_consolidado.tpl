 <table width="50%">
 
	<tr>
		<td height="26" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco12b">Objetivos</td>
		<td height="20" style="padding: 2px; border-right: solid 2px #ffffff; background-color: #f47001;" class="textblanco12b">Estado</td>
	</tr>
 <!-- BEGIN LISTA_OBJETIVOS -->
	<tr>
		<td  height="26" style="padding: 2px; border-right: solid 2px #ffffff;  background-color: #{__objetivo_color}; width: {__ancho_tabla};" class="textblanco12" id="objetivo_{__objetivo_id}">
			<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; ">{__objetivo_nombre}</div>
			<div dojoType="dijit.Tooltip" connectId="objetivo_{__objetivo_id}" position="below">
				<div class="textnegro12">{__objetivo_nombre}</div>
				<div class="textnegro9">{__objetivo_servicio}</div>
			</div>
		</td>
		<td height="26" align="center" id="objetivo_{__tooltip_id}" style="padding: 2px; border-right: solid 2px #ffffff; background-color:#{__estado_color};">
		<!-- BEGIN LISTA_ESTADOS -->																
			<i class="{__evento_icono}" style="cursor: pointer; border-style: solid 1px;"/></i>
		<!-- END LISTA_ESTADOS -->
		</td>
	</tr>
<!-- END LISTA_OBJETIVOS -->
 </table>