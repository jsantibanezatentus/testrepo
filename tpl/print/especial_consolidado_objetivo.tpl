<table width="100%">
	{__fila}
	<!-- BEGIN BLOQUE_OBJETIVO -->
	<tr>
		<td width="40%" class="txtGris12 bordes1" style="font-weight: bold; background-color: #{__color_obj};">{__nombre_obj}</td>
		<td align="right" width="20%" style="{__style_obj} padding: 2px; background-color: #{__color_uptime};" class="textblanco12 bordeDerecho2">{__uptime} %</td>
		<td align="right" width="20%" style="{__style_obj} padding: 2px; background-color: #{__color_downtime};" class="textblanco12 bordeDerecho2">{__downtime} %</td>
		<td align="right" width="20%" style="{__style_obj} padding: 2px; background-color: #{__color_no_mon};" class="textblanco12 bordeDerecho2">{__no_monitoreo} %</td>
	</tr>
	<!-- END BLOQUE_OBJETIVO -->
</table>
<style type="text/css">
	.bordes1 {
		padding: 1px 6px 1px 6px;
	    border-bottom: solid 1px #a2a2a2;
	    border-left: solid 1px #a2a2a2;
	}

	.bordeDerecho2 {
		border-right: solid 1px #fff;
	}
</style>

