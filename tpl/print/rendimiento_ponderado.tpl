<!-- BEGIN LISTA_PASOS_TITULO -->
 <!-- sin datos -->
 <!-- END LISTA_PASOS_TITULO -->
 
 <!-- BEGIN LISTA_PASOS -->
 <div style="page-break-inside: avoid;">
 <table width="100%">
 	<tr>
 		<td colspan="100%" class="txtNegro13" style="padding: 6px; border: solid 1px #ffffff;">{__paso_orden}. {__paso_nombre}</td>
 	</tr>
 	<tr>
 		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
 	</tr>
 	<tr>
 		<td class="txtBlanco13b celdaTituloGris" align="center" width="12%">Inicio</td>
 		<td class="txtBlanco13b celdaTituloGris" align="center" width="12%">Termino</td>
 		<td class="txtBlanco13b celdaTituloGris" align="center" width="15%">Ponderacion [%]</td>
 		<td class="txtBlanco12b celdaTituloNaranjo" align="center" width="15%">Mínimo</td>
 		<td class="txtBlanco12b celdaTituloNaranjo" align="center" width="15%">Máximo</td>
 		<td class="txtBlanco12b celdaTituloNaranjo" align="center" width="15%">Promedio</td>
 	</tr>
 	<!-- BEGIN LISTA_ITEMS -->
 	<tr>
 		<td class="txtGris12 {__print_class}" align="center">{__item_inicio}</td>
 		<td class="txtGris12 {__print_class}" align="center">{__item_termino}</td>
 		<td class="txtGris12 {__print_class}" align="right">{__item_valor}</td>
 
 		<td class="txtGris12 {__print_class}" align="right">{__paso_minimo}</td>
 		<td class="txtGris12 {__print_class}" align="right">{__paso_maximo}</td>
 		<td class="txtGris12 {__print_class}" align="right">{__paso_promedio}</td>
 	</tr>
 	<!-- END LISTA_ITEMS -->
 	<tr>
 	 	<td class="txtGris12 celdaIteracion2" colspan="3">Total Acumulado</td>
 
 		<td class="txtGris12 celdaIteracion2" align="right">{__min_total}</td>
 		<td class="txtGris12 celdaIteracion2" align="right">{__max_total}</td>
 		<td class="txtGris12 celdaIteracion2" align="right">{__prom_total}</td>
 	</tr>
 </table>
 </div>
 <br>
 <!-- END LISTA_PASOS --> 