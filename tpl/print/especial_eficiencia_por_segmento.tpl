 <!-- BEGIN BLOQUE_SUB_SEGMENTO -->
<table width="110%" border="1" cellpadding="0" cellspacing="0">
<tr>
		<td colspan="100%" style="border: solid 1px #ffffff;" class="celdanegra50">{__nombre_subsegmento}</td>
</tr>
<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
 <tr>
 	<td class="txtBlanco13b celdaTituloGris">Objetivo</td>
 	<td class="txtBlanco13b celdaTituloGris">Paso</td>
 	<td class="txtBlanco13b celdaTituloGris">Tipo</td>
 	
 	<!-- BEGIN BLOQUE_FECHA_EFICIENCIA -->
 	<td class="txtBlanco12b celdaTituloNaranjo" align="center" width="30">{_dia_eficiencia}</td>
 	<!-- END BLOQUE_FECHA_EFICIENCIA -->
 	
 	<td class="txtBlanco12b celdaTituloNaranjo" align="center">Promedio</td>
 	
</tr>
<!-- BEGIN BLOQUE_OBJETIVO -->
<tr>

 <td class="txtGris12 {__print_class}" rowspan="2" align="left">{__objetivos}</td>
 <td class="txtGris12 {__print_class}" rowspan="2" align="left">{__pasos}</td>
 <td class="txtGris12 {__print_class}" align="left">Diario</td>
   <!-- BEGIN BLOQUE_EFICIENCIA -->
 <td class="txtGris12 {__print_class}" align="right">{__eficiencia}</td>
  <!-- END BLOQUE_EFICIENCIA -->
 <td class="txtBlanco13b celdaTituloAzul" align="right">{__promedio_eficiencia}</td>
 </tr>
<tr>
<td class="txtGris12 {__print_class}" align="left">Acumulado</td>
   <!-- BEGIN BLOQUE_ACUMULADO -->
 <td class="txtGris12 {__print_class}" align="right">{__acumulado}</td>
  <!-- END BLOQUE_ACUMULADO -->
  <td class="txtBlanco13b celdaTituloAzul" align="right">{__promedio_acumulado}</td>
 </tr>
<!-- END BLOQUE_OBJETIVO -->


<tr>
	<td class="txtBlanco13b celdaTituloAzul" align="left" rowspan="2" colspan="" >Total</td>
	<td class="txtBlanco13b celdaTituloAzul" align="left"rowspan="2">({__ponderacion}%)</td>
	<td class="txtBlanco13b celdaTituloAzul" align="left">Diario</td>
	  <!-- BEGIN BLOQUE_TOTAL_DIARIO -->
 	<td class="txtBlanco13b celdaTituloAzul" align="right">{__diario_total}</td>
 	  <!-- END BLOQUE_TOTAL_DIARIO -->
 	  <td class="txtBlanco13b celdaTituloAzul" align="right">{__promedio_diario_total}</td>
</tr>
<tr>
	<td class="txtBlanco13b celdaTituloAzul" align="left">Acumulado</td>
	  <!-- BEGIN BLOQUE_ACUMULADO_TOTAL -->
 	<td class="txtBlanco13b celdaTituloAzul" align="right">{__acumulado_total}</td>
 	  <!-- END BLOQUE_ACUMULADO_TOTAL -->
 	<td class="txtBlanco13b celdaTituloAzul" align="right">{__promedio_acumulado_total}</td>
</tr>

</table>
</br></br>
<!-- END BLOQUE_SUB_SEGMENTO -->
</br></br>
<table width="110%" border="1" cellpadding="0" cellspacing="0">
<tr>
	<td class="txtBlanco13b celdaTituloAzul" align="left" rowspan="2" colspan="2" >PONDERADO</td>
	<td class="txtBlanco13b celdaTituloAzul" align="left">Diario</td>
	  <!-- BEGIN BLOQUE_PONDERACION_DIARIO -->
 	<td class="txtBlanco13b celdaTituloAzul" align="right">{__ponderacion_diario}</td>
 	  <!-- END BLOQUE_PONDERACION_DIARIO -->
 	  <td class="txtBlanco13b celdaTituloAzul" align="right">{__promedio_ponderacion_diario}</td>
</tr>
<tr>
	<td class="txtBlanco13b celdaTituloAzul" align="left">Acumulado</td>
	  <!-- BEGIN BLOQUE_PONDERACION_ACUMULADO -->
 	<td class="txtBlanco13b celdaTituloAzul" align="right">{__ponderacion_acumulado}</td>
 	  <!-- END BLOQUE_PONDERACION_ACUMULADO -->
 	<td class="txtBlanco13b celdaTituloAzul" align="right">{__promedio_ponderacion_acumulado}</td>
</tr>
</table>

