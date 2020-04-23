
	<table width="100%">
		<tr>
			<td>
				<div >
					<img src="{__img_base64}" height="51" width="51">
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
				<div style="font-family: Calibri;font-size:13px; text-align: center;">{__titulo}</div>	
			<hr color="black" size=3>
		</tr>
		<tr>
			<td  style="text-align: left;width: '50%'; font-weight: bold;">{__nombre_horario}</td>
			<td  style="text-align: right;width: '50%';">Santiago, {__fecha}.</td>
		</tr>
	</table>
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
	<div style="page-break-before: always;">
	<table width="100%" cellpadding="10">
		<tr>
			<td style="color:green; ">Verde(&#11044;):</td>
			<td style="text-align: justify;">{__leyenda}</td>
		</tr>
		<tr>
			<td style="color:red;">Rojo(&#11044;):</td>
			<td>{__leyenda_error}</td>
		</tr>
	</table>
	</div>
	
	{__text}
	
	<!-- BEGIN BLOQUE_ESTADOS_OBJETIVOS -->
	<table width="100%">
		<tr>
			<td colspan="3" style="background-color: #c8c8c8;font-size: 10px;border:solid 0.4px;font-weight: 700" align="center">{__nombre_objetivo}</td>
		</tr>
		
		<tr>
			<td align="center" , style="background-color: #c8c8c8;font-size: 10px;border:solid 0.5px;font-weight: 700">hora inicio</td>
			<td align="center" , style="background-color: #c8c8c8;font-size: 10px;border:solid 0.5px;font-weight: 700">hora termino</td>
			<td align="center" , style="background-color: #c8c8c8;font-size: 10px;border:solid 0.5px;font-weight: 700">hora duracion</td>
		</tr>
		<!-- BEGIN BLOQUE_HORARIO_EVENTOS -->
		<tr>
			<td align="center"  style="padding: 5px;border: solid 0.5px;font-size:10px;font-family: Trebuchet MS, Verdana, sans-serif;font-weight: bold; background-color: #{__color_no_mon_obj};" class="textblanco12 {__class_paso}">{__inicio}</td>
			<td align="center" style="padding: 5px;border: solid 0.5px;font-size: 10px;font-family: Trebuchet MS, Verdana, sans-serif;font-weight: bold; background-color: #{__color_no_mon_obj};" class="textblanco12 {__class_paso}">{__termino}</td>
			<td align="center" style="padding: 5px;border: solid 0.5px;font-size: 10px;font-family: Trebuchet MS, Verdana, sans-serif;font-weight: bold; background-color: #{__color_no_mon_obj};" class="textblanco12 {__class_paso}">{__duracion}</td>
		</tr>
		<!-- END BLOQUE_HORARIO_EVENTOS -->
	</table>
	<br>
	<!-- END BLOQUE_ESTADOS_OBJETIVOS -->
	<table width="600px">
		<tr>
			<td style="font-size:10px;font-weight: 700;text-align: justify;">Este certificado es emitido por Servicios de Monitoreo S.A. (Atentus), que entrega servicios de monitoreo a la Contraloría General de la República, dónde la metodología utilizada se basa en realizar ingresos automatizados mediante robots instalados dentro de los principales ISP (proveedores de Internet) de Chile, simulando la experiencia de un usuario real.</td>
		</tr>
	</table>
	<br>
	<br>
	<table width="600px">
		<tr>
			<td data="imagen1" style="font-size: 10px;font-weight: 700;text-align:justify;">{__texto}</td>
		</tr>
	</table>
	<br>
	<br>


<script>
document.getElementById("especial").remove()
document.getElementById("especial2").remove()
	
</script>