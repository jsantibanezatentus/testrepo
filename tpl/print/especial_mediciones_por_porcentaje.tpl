<div style="page-break-inside: avoid;">
<table width="100%">
	<tr>
		<td colspan="3"></td>
		<td colspan="3" class="txtNegro14b" style="color:#365c94; background-color:#efefef; text-align:center; border-right:2px solid white">Primer 90% de Trx</td>
		<td colspan="3" class="txtNegro14b" style="color:#365c94; background-color:#efefef; text-align:center; border-left:2px solid white">10% Restante de Trx</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="txtNegro13" style="color:#a2a2a2; padding: 1px 6px 2px 6px; font-weight:bold; text-align:right">Id</td>
		<td class="txtNegro13" style="color:#a2a2a2; padding: 1px 6px 2px 6px; font-weight:bold">Objetivo</td>
		<td class="txtNegro13" style="color:#a2a2a2; padding: 1px 6px 2px 6px; font-weight:bold">Paso</td>
		<td class="txtNegro13" style="color:#a2a2a2; padding: 1px 6px 2px 6px; font-weight:bold; text-align:right">Total TRX</td>
		<td class="txtNegro13" style="color:#a2a2a2; padding: 1px 6px 2px 6px; font-weight:bold; text-align:right">Total Segundos</td>
		<td class="txtNegro13" style="color:#a2a2a2; padding: 1px 6px 2px 6px; font-weight:bold; text-align:right">Promedio</td>
		<td class="txtNegro13" style="color:#a2a2a2; padding: 1px 6px 2px 6px; font-weight:bold; text-align:right">Total TRX</td>
		<td class="txtNegro13" style="color:#a2a2a2; padding: 1px 6px 2px 6px; font-weight:bold; text-align:right">Total Segundos</td>
		<td class="txtNegro13" style="color:#a2a2a2; padding: 1px 6px 2px 6px; font-weight:bold; text-align:right">Promedio</td>
		<td class="txtNegro13" style="color:#a2a2a2; padding: 1px 6px 2px 6px; font-weight:bold; text-align:right">Total</td>
	</tr>

	<!-- BEGIN LISTA_PASOS -->
	<tr>
		<td class="txtGris12" style="padding: 1px 6px 2px 6px; text-align:right">{__objetivo_id}</td>
		<td class="txtGris12" style="padding: 1px 6px 2px 6px; text-align:left; width:180px">
			<div style="width:180px;white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
				{__objetivo_nombre}		
			</div>
		</td>
		<td class="txtGris12" style="padding: 1px 6px 2px 6px; text-align:left">{__paso_nombre}</td>
		<td class="txtGris12" style="padding: 1px 6px 2px 6px; text-align:right">{__total_trx1}</td>
		<td class="txtGris12" style="padding: 1px 6px 2px 6px; text-align:right">{__total_seg1}</td>
		<td class="txtGris12" style="padding: 1px 6px 2px 6px; text-align:right">{__promedio1}</td>
		<td class="txtGris12" style="padding: 1px 6px 2px 6px; text-align:right">{__total_trx2}</td>
		<td class="txtGris12" style="padding: 1px 6px 2px 6px; text-align:right">{__total_seg2}</td>
		<td class="txtGris12" style="padding: 1px 6px 2px 6px; text-align:right">{__promedio2}</td>
		<td class="txtGris12" style="padding: 1px 6px 2px 6px; text-align:right">{__total}</td>
	</tr>
	<!-- END LISTA_PASOS -->	
</table>
<br><br>
<!-- BEGIN LISTA_EXCLUIDOS -->
<table>
	<tr>
		<td colspan="2" class="txtNegro14b" style="padding: 1px 6px 6px 6px; text-align:left">Para la generaci&oacute;n del reporte se excluyeron los siguientes monitores:</td>
	</tr>
	<!-- BEGIN LISTA_NODOS -->	
	<tr>
		<td style="width:20px"></td>
		<td class="txtGris12" style="padding: 1px 12px 2px 6px; text-align:left">{__nodo_nombre}</td>
	</tr>
	<!-- END LISTA_NODOS -->
</table>
</div>
<!-- END LISTA_EXCLUIDOS -->