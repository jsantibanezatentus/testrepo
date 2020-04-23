<table width="110%" border="1" cellpadding="0" cellspacing="0">
<tr>
		<td colspan="100%" style="border: solid 1px #ffffff;" class="celdanegra50">{__nombre_categoria}</td>
</tr>
<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
 <tr>
 	<td class="txtBlanco13b celdaTituloGris">Objetivo</td>
 	<td class="txtBlanco13b celdaTituloGris">Paso</td>
 	
 	<!-- BEGIN BLOQUE_HORA_EFICIENCIA -->
 	<td class="txtBlanco12b celdaTituloNaranjo" align="center" width="30">{_hora_eficiencia}</td>
 	<!-- END BLOQUE_HORA_EFICIENCIA -->
 	
 	<td class="txtBlanco12b celdaTituloNaranjo" align="center">Promedio</td>
 	
</tr>
 
<!-- BEGIN BLOQUE_OBJETIVO -->
<tr>

 <td class="txtGris12 {__print_class}" align="left">{__objetivos}</td>
 <td class="txtGris12 {__print_class}" align="left">{__pasos}</td>
 
  <!-- BEGIN BLOQUE_EFICIENCIA -->
 <td class="txtGris12 {__print_class}" align="right">{__eficiencia}</td>
  <!-- END BLOQUE_EFICIENCIA -->
 <td class="txtBlanco13b celdaTituloAzul" align="right">{__promedio_eficiencia}</td>
</tr>

<!-- END BLOQUE_OBJETIVO -->
<tr>
	<td class="txtBlanco13b celdaTituloAzul" align="left" colspan="2" >Ponderado</td>
	  <!-- BEGIN BLOQUE_PONDERACION -->
 	<td class="txtBlanco13b celdaTituloAzul" align="right">{__ponderacion}</td>
 	  <!-- END BLOQUE_PONDERACION -->
 	  <td class="txtBlanco13b celdaTituloAzul" align="right">{__promedio_ponderacion}</td>
</tr>

</table>
</br></br>