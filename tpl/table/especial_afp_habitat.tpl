<!-- BEGIN HORARIO -->
<table>
	<tr>
		<td  style="text-align: left;width: '50%'; font-weight: bold;">{__nombre_horario}</td>
	</tr>
</table>
<!-- END HORARIO -->
<!-- BEGIN BLOQUE_OBJETIVOS -->
{__disp_res_objs}
<!-- END BLOQUE_OBJETIVOS -->
<!-- BEGIN BLOQUE_OBJETIVO -->
<table width="100%">
	<tr>
		<td height="15px"></td>
	</tr>
</table>
<h4 style="font-family: Trebuchet MS,Verdana,sans-serif; margin: 0px !important">{__nombre_objetivo}</h4>
<h5 style="font-family: Trebuchet MS,Verdana,sans-serif; color: #626262; margin-top: 2px !important; margin-bottom: 5px !important">Periodo del {__fecha_inicio} al {__fecha_termino}</h5>
{__consolidado}
{__disp_res}

<div style="page-break-inside: avoid;">
	<table width="100%">
		<tr>
			<td class="txtBlanco13b celdaTituloGris">Objetivo</td>
			<td class="txtBlanco13b celdaTituloGris">Nodo</td>
			<td class="txtBlanco13b celdaTituloGris">Pasos</td>
			<td align="center" style="border-right: solid 1px #ffffff;" class="txtBlanco12b celdaTituloNaranjo">Uptime</td>
			<td align="center" style="border-right: solid 1px #ffffff;" class="txtBlanco12b celdaTituloNaranjo">Downtime</td>
			<td align="center" style="border-right: solid 1px #ffffff;" class="txtBlanco12b celdaTituloNaranjo">No Monitoreo</td>
		</tr>

		<tr>
			<td width="220" rowspan="{__rowspan_obj}" class="txtGris12 celdaIteracion1">{__nombre_objetivo}</td>
			<!-- BEGIN BLOQUE_NODO -->
			<td width="120" rowspan="{__rowspan_nodo}" class="txtGris12 {__class_nodo}">{__nombre_nodo}</td>
			<!-- BEGIN BLOQUE_DATOS -->
			<td width="200" class="txtGris12 {__class_paso}">{__nombre_paso}</td>
			<td align="right" width="80" style="border: solid 1px #ffffff; padding: 2px; background-color: #{__color_uptime_obj};" class="textblanco12 {__class_paso}">{__uptime_porcentaje} %</td>
			<td align="right" width="80" style="border: solid 1px #ffffff; padding: 2px; background-color: #{__color_downtime_obj};" class="textblanco12 {__class_paso}">{__downtime_porcentaje} %</td>
			<td align="right" width="80" style="border: solid 1px #ffffff; padding: 2px; background-color: #{__color_no_mon_obj};" class="textblanco12 {__class_paso}">{__no_monitoreo_porcentaje} %</td>
		</tr>
		<!-- END BLOQUE_DATOS -->
		<!-- END BLOQUE_NODO -->
		<tr  height="5px"></tr>
	</table>
</div>
{__tiempos}
<!-- END BLOQUE_OBJETIVO -->
