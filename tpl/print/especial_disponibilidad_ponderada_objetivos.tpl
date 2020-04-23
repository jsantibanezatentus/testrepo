<!-- BEGIN LISTA_OBJETIVOS -->
<div style="page-break-inside: avoid;">
<table width="600px">
	<tr>
		<td style="padding: 6px; border: solid 1px #ffffff; color: #000000; font-family: Trebuchet MS, Verdana, sans-serif; font-size: 13px;">{__item_orden}.{__objetivo_orden}. {__objetivo_nombre}</td>
	</tr>
</table>

<table style="border-spacing: 0px; border-collapse: collapse;">
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td width="140px" align="left" style="color: #ffffff; font-family: Trebuchet MS, Verdana, sans-serif; font-size: 13px; font-weight: bold; padding: 1px 6px 1px 6px; background-color: #626262; border: solid 1px #626262;">Hora</td>
		<!-- BEGIN PASOS_HEADER -->
		<td width="100px" align="center" style="color: #ffffff; font-family: Trebuchet MS, Verdana, sans-serif; font-size: 12px; font-weight: bold; padding: 1px 6px 1px 6px; background-color: #f47001; border: solid 1px #f47001;">{__paso_nombre}</td>
		<!-- END PASOS_HEADER -->
	</tr>
	<!-- BEGIN ITEMS_DATOS -->
	<tr>
		<td width="140px" style="color: #333333; font-family: Trebuchet MS, Verdana, sans-serif; font-size: 12px; padding: 1px 6px 1px 6px; background-color: {__item_estilo}; border: solid 1px #a2a2a2;" align="center">{__item_descripcion}</td>
		<!-- BEGIN PASOS_DATOS -->
		<td width="100px" style="color: #333333; font-family: Trebuchet MS, Verdana, sans-serif; font-size: 12px; padding: 1px 6px 1px 6px; background-color: {__item_estilo}; border: solid 1px #a2a2a2;" align="right">{__paso_uptime}%</td>
		<!-- END PASOS_DATOS -->
	</tr>
	<!-- END ITEMS_DATOS -->
</table>
<br>
<table style="border-spacing: 0px; border-collapse: collapse;">
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<th width="140px" style="color: #ffffff; font-family: Trebuchet MS, Verdana, sans-serif; font-size: 13px; font-weight: bold; padding: 1px 6px 1px 6px; background-color: #626262; border: solid 1px #626262;"></th>
		<!-- BEGIN PASOS_HEADER_RESUMEN -->
		<th width="100px" width="100px" align="center" style="color: #ffffff; font-family: Trebuchet MS, Verdana, sans-serif; font-size: 12px; font-weight: bold; padding: 1px 6px 1px 6px; background-color: #f47001; border: solid 1px #f47001;">{__paso_nombre}</th>
		<!-- END PASOS_HEADER_RESUMEN -->
	</tr>
	<tr>
		<td width="140px" style="color: #333333; font-family: Trebuchet MS, Verdana, sans-serif; font-size: 12px; spadding: 1px 6px 1px 6px; background-color: #ffffff; border: solid 1px #a2a2a2;" align="center">Ponderado</td>
		<!-- BEGIN PASOS_DATOS_RESUMEN -->
		<td width="100px" style="color: #333333; font-family: Trebuchet MS, Verdana, sans-serif; font-size: 12px; spadding: 1px 6px 1px 6px; background-color: #ffffff; border: solid 1px #a2a2a2;" align="right">{__paso_uptime_ponderado}%</td>
		<!-- END PASOS_DATOS_RESUMEN -->
	</tr>
</table>
</div>
<br>
<br>
<!-- END LISTA_OBJETIVOS -->