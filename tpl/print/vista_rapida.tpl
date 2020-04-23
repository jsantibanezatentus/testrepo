<!-- BEGIN LISTA_NODOS_BLANCO -->

<!-- END LISTA_NODOS_BLANCO -->
<!-- BEGIN LISTA_NODOS -->

<!-- END LISTA_NODOS -->
<!-- BEGIN LISTA_NODOS_UBICACION -->

<!-- END LISTA_NODOS_UBICACION -->

<!-- BEGIN TITULOS_OBJETIVOS -->
<!-- BEGIN TITULOS_PASOS -->
<!-- BEGIN TITULOS_PATRONES -->

<!-- END TITULOS_PATRONES -->
<!-- END TITULOS_PASOS -->
<!-- END TITULOS_OBJETIVOS -->
<!-- BEGIN TIENE_TOOLTIP -->

<!-- END TIENE_TOOLTIP -->

<div style="page-break-inside: avoid;">
<table width="100%">
	<!-- BEGIN LISTA_OBJETIVOS -->
	<tr>
		<td class="txtBlanco13b celdaTituloGris" colspan="100%">{__objetivo_nombre}</td>
	</tr>
	<!-- BEGIN LISTA_GRUPOS -->
	<tr>
		<td class="txtBlanco12">&nbsp;</td>
		<!-- BEGIN LISTA_FECHA -->
		<td align="center" style="width: 85px;">
			<div class="txtBlanco12" style="padding: 2px; background-color: #f47001; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 85px;">
				{__evento_nodo}
			</div>
			<div class="txtBlanco12" style="padding: 2px; background-color: #f47001; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 85px;">
				{__evento_ubicacion}
			</div>
			<div class="txtBlanco12" style="padding: 2px; background-color: #f58b32;">{__evento_fecha}</div>
		</td>
		<!-- END LISTA_FECHA -->
	</tr>
	<!-- BEGIN LISTA_PASOS -->
	<!-- BEGIN LISTA_PATRONES -->
	<tr>
		<td class="txtGris12" align="right" style="padding: 2px;">{__paso_nombre}</td>
		<!-- BEGIN LISTA_ESTADOS -->
		<td align="center">
			<div class="txtGris10b" style="padding: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 85px;">
				{__evento_nombre_print}
			</div>
		</td>
		<!-- END LISTA_ESTADOS -->
	</tr>
	<!-- END LISTA_PATRONES -->
	<tr>
		<td class="txtGris12">&nbsp;</td>
		<!-- BEGIN LISTA_RESPUESTA -->
		<td align="center">
			<div class="txtGris10" style="padding: 2px;">{__evento_respuesta}</div>
			<div class="txtGris10" style="padding: 2px;">{__evento_duracion}</div>
		</td>
		<!-- END LISTA_RESPUESTA -->
	</tr>
	<!-- END LISTA_PASOS -->
	
	<!-- END LISTA_GRUPOS -->
	
	<!-- BEGIN ESTADO_BLANCO -->
	<tr>
		<td class="txtBlanco12">&nbsp;</td>
		<td width="85" align="center">
			<div class="txtBlanco12" style="background-color:#f47001; padding: 2px; text-overflow: ellipsis; white-space: nowrap; width: 85px; overflow: hidden">
				Sin Monitor<br>Asociado
			</div>
			<div class="txtBlanco12" style="padding: 2px; background-color: #f58b32;">&nbsp;</div>
			<div class="txtGris10b" style="height: 32px; padding: 2px;width: 85px;">s/i</div>
		</td>
	</tr>
	<!-- END ESTADO_BLANCO -->
	
	<!-- END LISTA_OBJETIVOS -->
	<tr>
		<td></td>
		<td align="center" style="width: 85px;"></td>
		<td align="center" style="width: 85px;"></td>
		<td align="center" style="width: 85px;"></td>
		<td align="center" style="width: 85px;"></td>
		<td align="center" style="width: 85px;"></td>
		<td align="center" style="width: 85px;"></td>
	</tr>
</table>
</div>