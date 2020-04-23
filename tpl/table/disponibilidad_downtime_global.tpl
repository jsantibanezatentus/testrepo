<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>
<script>
<!--
function cambiarGrupoDisponibilidad(grupo_id) {
	document.getElementById("grupo_disponibilidad_"+grupo_disponibilidad_anterior).style.backgroundColor="#f0ede8";
	document.getElementById("grupo_disponibilidad_"+grupo_disponibilidad_anterior).style.color="#525252";
	document.getElementById("grupo_disponibilidad_"+grupo_id).style.backgroundColor="#f36f00";
	document.getElementById("grupo_disponibilidad_"+grupo_id).style.color="#ffffff";
		
	document.getElementById("grupo_disponibilidad_sel_"+grupo_disponibilidad_anterior).style.display="none";
	document.getElementById("grupo_disponibilidad_sel_"+grupo_disponibilidad_anterior).style.visible="hidden";
	
	document.getElementById("grupo_disponibilidad_sel_"+grupo_id).style.display="block";
	document.getElementById("grupo_disponibilidad_sel_"+grupo_id).style.visible="visible";
	
	grupo_disponibilidad_anterior = grupo_id;
}
//-->
</script>

<table width="100%">
	<tr>
		<td >
		<!-- BEGIN BLOQUE_NOMBRE -->
		<div class='boton_elemento celdaselector' style='height:30px; width:135px; overflow:hidden;' id="grupo_disponibilidad_{__paso_id}" onClick="cambiarGrupoDisponibilidad({__paso_id})">
			<div style='top:25%; position:relative; width:131px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis'>{__paso_nombre}</div>
		</div>
		<!-- END BLOQUE_NOMBRE -->
		<br>
		</td>
	</tr>
</table>
<br>


<!-- BEGIN LISTA_PASOS -->

<div id="grupo_disponibilidad_sel_{__paso_id}" style="display:none">
	<table width="100%" >
		<tr>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="18%">Fecha</td>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="18%">Hora Inicio</td>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="18%">Hora Termino</td>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="18%">Duraci&oacute;n</td>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="28%">Tipo</td>
		</tr>
		<!-- BEGIN LISTA_DIAS -->
		<!-- BEGIN BLOQUE_DOWNTIME -->
		<tr>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__fecha}</td>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__horaInicio}</td>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__horaTermino}</td>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__duracion}</td>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__tipo}</td>
		</tr>
		<!-- END BLOQUE_DOWNTIME -->
		<!-- BEGIN BLOQUE_UPTIME -->
		<tr>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__fecha}</td>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center" colspan="4">Sin Caidas Globales</td>
		</tr>
		<!-- END BLOQUE_UPTIME -->
		<!-- END LISTA_DIAS -->
		<tr>
		 	<td style="border: solid 1px #ffffff;" class="celdanegra20" align="center" colspan="4">Downtime Acumulado</td>
			<td style="border: solid 1px #ffffff;" class="celdanegra20" align="center" >{__downtime_acumulado}</td>
		</tr>
		<tr>
		 	<td style="border: solid 1px #ffffff;" class="celdanegra20" align="center" colspan="4">No Monitoreo Acumulado</td>

			<td style="border: solid 1px #ffffff;" class="celdanegra20" align="center">{__no_monitoreo_acumulado}</td>
		</tr>
	</table>
</div>
<!-- END LISTA_PASOS -->
<br/>

<div id="man" style="display:none">
	<table width="100%" >
		<tr>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="100%" colspan="4">Datos Mantenimientos especiales</td>
		</tr>
		<tr>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="18%">Fecha Inicio</td>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="18%">Fecha Termino</td>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="18%">Duraci&oacute;n</td>
			<td style="border: solid 1px #ffffff;" class="celdanegra40" align="center" width="28%">Tipo</td>
		</tr>
		<!-- BEGIN BLOQUE_MANTENIMIENTO -->
		<tr>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__fecha_inicio_evento}</td>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__fecha_termino_evento}</td>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__duracion_evento}</td>
			<td style="border: solid 1px #ffffff;" class="{__class}" align="center">{__tipo_evento}</td>
		</tr>
		<!-- END BLOQUE_MANTENIMIENTO -->
			
		
	</table>
</div>
<script>
cambiarGrupoDisponibilidad('100000');
$(function() {
	name = '{__name}';
	// Ejecuta la inialización del acordeon.
	if ('{__tiene_evento}' == 'true'){
		$('#man').show();
		createAccordion(name);	
	}
});
</script>