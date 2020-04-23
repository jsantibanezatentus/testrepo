
<div style="page-break-inside: avoid;">
	<table width="100%" >
		<tr>
			<td>
				<div>
					<img src="/utils/imagenes_especiales.php?objetivo={__objetivo_padre}&t=2">
				</div>
			</td>
			<td style="padding-left: 300px">
				<div>
					<img src="/utils/imagenes_especiales.php?objetivo={__objetivo_padre}&t=1">
				</div>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="30">
		<tr>
			<hr color="black" size=3>
				<div style="font-family: Calibri;font-size:xx-large; text-align: center;">{__titulo}</div>	
			<hr color="black" size=3>
		</tr>
		<tr>
			<td  style="text-align: right;">Santiago, {__fecha}.</td>
		</tr>
	</table>
	<br>

	<!-- BEGIN BLOQUE_TR -->

		{__tr_obj}
		<!-- BEGIN BLOQUE_TD_OBJ -->
			{__obj}
		<!-- END BLOQUE_TD_OBJ -->
		{__medio_tr}
		<!-- BEGIN BLOQUE_COLOR -->
			{__color_obj}
		<!-- END BLOQUE_COLOR -->
		{__final_tr}
	<!-- END BLOQUE_TR -->
	<br>
	<table width="100%" cellpadding="10">
		<tr>
			<td style="color:green;">Verde(&#11044;):</td>
			<td style="text-align: justify;">{__leyenda}</td>
		</tr>
		<tr>
			<td style="color:red;">Rojo(&#11044;):</td>
			<td>{__leyenda_error}</td>
		</tr>
	</table>
	<br>
	<br>
	{__text}
	<br>
	<!-- BEGIN BLOQUE_ESTADOS_OBJETIVOS -->
	<table width="100%">
		<tr>
			<td colspan="3" style="background-color: #c8c8c8;font-size: 14px;border:solid 0.4px;font-weight: 700" align="center">{__nombre_objetivo}</td>
		</tr>
		
		<tr>
			<td align="center" , style="background-color: #c8c8c8;font-size: 14px;border:solid 0.5px;font-weight: 700">hora inicio</td>
			<td align="center" , style="background-color: #c8c8c8;font-size: 14px;border:solid 0.5px;font-weight: 700">hora termino</td>
			<td align="center" , style="background-color: #c8c8c8;font-size: 14px;border:solid 0.5px;font-weight: 700">hora duracion</td>
		</tr>
		<!-- BEGIN BLOQUE_HORARIO_EVENTOS -->
		<tr>
			<td align="center" ,  style="padding: 5px;background-color: #eeeeee;border: solid 0.5px;font-size: 12;font-family: Trebuchet MS, Verdana, sans-serif;font-weight: bold;">{__inicio}</td>
			<td align="center" , style="padding: 5px;background-color: #eeeeee;border: solid 0.5px;font-size: 12;font-family: Trebuchet MS, Verdana, sans-serif;font-weight: bold;">{__termino}</td>
			<td align="center" , style="padding: 5px;background-color: #eeeeee;border: solid 0.5px;font-size: 12;font-family: Trebuchet MS, Verdana, sans-serif;font-weight: bold;">{__duracion}</td>
		</tr>
		<!-- END BLOQUE_HORARIO_EVENTOS -->
	</table>
	<br>
	<!-- END BLOQUE_ESTADOS_OBJETIVOS -->
	<table width="600px">
		<tr>
			<td style="font-size: 14px;font-weight: 700;text-align: justify;">Este certificado es emitido por Servicios de Monitoreo S.A. (Atentus), que entrega servicios de monitoreo al Poder Judicial, dónde la metodología utilizada se basa en realizar ingresos automatizados mediante robots instalados dentro de los principales ISP (proveedores de Internet) de Chile, simulando la experiencia de un usuario real.</td>
		</tr>
	</table>
	<br>
	<br>
	<table width="600px">
		<tr>
			<td data="imagen1" style="font-size: 14px;font-weight: 700;text-align:justify;">{__texto}</td>
		</tr>
	</table>
	<br>
	<br>
	<table width="600px">
		<tr height="130px">
			<td align="center"></td>
		</tr>
	</table>
</div>
<script>
document.getElementById("especial").remove()
document.getElementById("especial2").remove()
	
</script>