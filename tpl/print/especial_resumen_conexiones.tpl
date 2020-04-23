<div style="page-break-inside: avoid;">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco13b celdaTituloGris" rowspan="3">Paso</td>
		<td class="txtBlanco12b celdaTituloNaranjo" rowspan="3" width="150" align="center">Conexiones OK</td>
		<td class="txtBlanco12b celdaTituloNaranjo" colspan="2" align="center">Conexiones Error</td>
	</tr>
	<tr>
		<td height="2" colspan="2" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
	<tr>
		<td class="txtBlanco12b celdaTituloNaranjo" width="150" align="center">Contenido</td>
		<td class="txtBlanco12b celdaTituloNaranjo" width="150" align="center">No Contenido</td>
	</tr>
	<!-- BEGIN LISTA_PASOS -->
	<tr>
		<td class="txtGris12 {__paso_estilo}">{__paso_nombre}</td>
		<td class="{__paso_estilo}" align="right">
			<table width="100%">
				<tr>
					<td align="right" width="50%" class="txtGris12">{__paso_cantidad_ok}&nbsp;/</td>
					<td align="left" width="50%" class="txtUptime">{__paso_porcentaje_ok}%</td>
				</tr>
			</table>
		</td>
		<td class="{__paso_estilo}" align="right">
			<table width="100%">
				<tr>
					<td align="right" width="50%" class="txtGris12">{__paso_cantidad_error_contenido}&nbsp;/</td>
					<td align="left" width="50%" class="txtDtGlobal">{__paso_porcentaje_error_contenido}%</td>
				</tr>
			</table>
		</td>
		<td class="{__paso_estilo}" align="right">
			<table width="100%">
				<tr>
					<td align="right" width="50%" class="txtGris12">{__paso_cantidad_error_nocontenido}&nbsp;/</td>
					<td align="left" width="50%" class="txtDtGlobal">{__paso_porcentaje_error_nocontenido}%</td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- END LISTA_PASOS -->
</table>
</div>
<br>