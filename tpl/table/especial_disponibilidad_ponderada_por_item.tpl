<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>
<script>
<!--
function cambiarGrupoPonderado(grupo_id) {
	if (document.getElementById("grupo_ponderado_"+grupo_ponderado_anterior)) {
		document.getElementById("grupo_ponderado_"+grupo_ponderado_anterior).style.backgroundColor="#f0ede8";
		document.getElementById("grupo_ponderado_"+grupo_ponderado_anterior).style.color="#525252";
		document.getElementById("grupo_ponderado_sel_"+grupo_ponderado_anterior).style.display="none";
		document.getElementById("grupo_ponderado_sel_"+grupo_ponderado_anterior).style.visible="hidden";
	}
	document.getElementById("grupo_ponderado_"+grupo_id).style.backgroundColor="#f36f00";
	document.getElementById("grupo_ponderado_"+grupo_id).style.color="#ffffff";
		
	document.getElementById("grupo_ponderado_sel_"+grupo_id).style.display="block";
	document.getElementById("grupo_ponderado_sel_"+grupo_id).style.visible="visible";
	
	grupo_ponderado_anterior = grupo_id;
}
//-->
</script>

<table width="100%">
	<tr>
		<td >
			<!-- BEGIN LISTA_PASOS_TITULO -->
			<div style="height:16px;width:179px; overflow:hidden" id="grupo_ponderado_{__paso_id}" class="celdaselector" onClick="cambiarGrupoPonderado({__paso_id})">{__paso_nombre}</div>
			<!-- END LISTA_PASOS_TITULO -->
		<br>
		</td>
	</tr>
</table>
<br>


<!-- BEGIN LISTA_PASOS -->

<div id="grupo_ponderado_sel_{__paso_id}" style="display:none">
	<table width="100%" >
		<tr>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="12%">Inicio</td>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="12%">Termino</td>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="15%">Ponderacion (%)</td>
			<!-- BEGIN BLOQUE_EVENTOS_TITULOS -->
			<td style="border: solid 1px #ffffff;" class="celdanegra40"  width="15%" align="center">{__evento_nombre} [%]</td>
			<!-- END BLOQUE_EVENTOS_TITULOS -->	
		</tr>
		<!-- BEGIN LISTA_ITEMS -->
		<tr>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__item_inicio}</td>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__item_termino}</td>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="right">{__item_valor}</td>
			<!-- BEGIN BLOQUE_EVENTOS -->
			<td style="border: solid 1px #ffffff; padding: 2px; background-color: #{__evento_color};" class="textblanco12" align="right">{__evento_valor}</td>
			<!-- END BLOQUE_EVENTOS -->
		</tr>
		<!-- END LISTA_ITEMS -->
		<tr>
		 	<td style="border: solid 1px #ffffff;" class="celdanegra20" align="center" colspan="3">Total Acumulado</td>
			<!-- BEGIN BLOQUE_EVENTOS_TOTAL -->
			<td style="border: solid 1px #ffffff;" class="celdanegra20" align="right">{__evento_total}</td>
			<!-- END BLOQUE_EVENTOS_TOTAL -->
		</tr>
	</table>
</div>
<!-- END LISTA_PASOS -->
<script>
$(function() {
  name = '{__name}';
  // Ejecuta la inialización del acordeon.
  if ('{__tiene_evento}' == 'true'){
    createAccordion(name);  
  }
});
cambiarGrupoPonderado('{__paso_id_default}');
</script>