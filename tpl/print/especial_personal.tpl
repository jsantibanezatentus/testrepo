<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>
<!-- BEGIN BLOQUE_DISPONIBILIDAD -->
<div style="page-break-inside: avoid;">

<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;" id="">{__nombre_tabla_negocio}
<br>
	<tr>
		<td height="2" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"><td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris">Negocio</td>
		<td class="txtBlanco13b celdaTituloGris" width="120">Objetivos</td>
		<!-- BEGIN BLOQUE_TITULO_EVENTO -->
		<td class="txtBlanco12b celdaTituloNaranjo" width="70" align="center">{__titulo_evento}[%]</td>
		<!-- END BLOQUE_TITULO_EVENTO -->
	</tr >
	<!-- BEGIN LISTA_NEGOCIOS -->
	<tr >
		<td class="txtGris12" style= "width: 100px; border: 1px solid black" align="center">{__negocio}</td>
		<td  class = "td_negocio" style=" width: 100%; border: 1px solid black">
			<!-- BEGIN TIENE_EVENTO_DATO -->
			<li>{__paso_objetivos}</li>
			<!-- END TIENE_EVENTO_DATO -->
		</td>
		{__paso_uptime}
		{__paso_down_parcial}
		{__paso_downtime_global}
		{__paso_sin_monitoreo}
	</tr>
	<!-- END LISTA_NEGOCIOS -->
</table>
<br>
</div>
<!-- END BLOQUE_DISPONIBILIDAD -->
