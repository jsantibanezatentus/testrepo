<script>

function cambiarInputSla() {
	var inputs = document.getElementsByTagName("input");
	for (var i=0; i<inputs.length; i++) {
		if (inputs[i].type=="text" && inputs[i].name.match(/^paso_sla_/)) {
			if (document.form_principal.notificacion_sla.checked == true) {
				inputs[i].disabled = false;				 
			}
			else {
				inputs[i].disabled = true;
			}
		}
	}
}

function cambiarAlertaUptime() {
	if (document.getElementById("notificacion_uptime_parcial")) {
	var parcial = false;
	var grupal = false;
	var global = false;
	if (document.getElementById("notificacion_downtime_parcial")) {
		parcial = document.getElementById("notificacion_downtime_parcial").checked;
	}
	if (document.getElementById("notificacion_downtime_grupal")) {
		grupal = document.getElementById("notificacion_downtime_grupal").checked;
	}
	if (document.getElementById("notificacion_downtime_global")) {
		global = document.getElementById("notificacion_downtime_global").checked;
	}
	if (parcial == true || grupal == true || global == true) {
		document.getElementById("notificacion_uptime_parcial").disabled = false;
	}
	else {
		document.getElementById("notificacion_uptime_parcial").checked = false;
		document.getElementById("notificacion_uptime_parcial").disabled = true;
	}
	}
}

var grupo_configuracion_anterior = 0;
function cambiarGrupoConfiguracion(grupo_configuracion) {
	document.getElementById("grupo_monitor_sel_"+grupo_configuracion_anterior).style.display="none";
	document.getElementById("grupo_monitor_"+grupo_configuracion_anterior).style.backgroundColor="#f0ede8";
	document.getElementById("grupo_monitor_"+grupo_configuracion_anterior).style.color="#525252";

	grupo_configuracion_anterior = grupo_configuracion;
	document.getElementById("grupo_monitor_sel_"+grupo_configuracion).style.display="inline";
	document.getElementById("grupo_monitor_"+grupo_configuracion).style.backgroundColor="#f36f00";
	document.getElementById("grupo_monitor_"+grupo_configuracion).style.color="#ffffff";
}
</script>

<table width="100%">
	<tr>
		<td class="tituloitemconfig">Configuraci&oacute;n de Envio de Alertas</td>
	</tr>
</table>
<br>
<table width="55%" class="formulario">
	<tr>
		<th>Downtime</th>
		<td>
			<!-- BEGIN TIENE_NOTIFICACION_PARCIAL -->
			<input type="checkbox" name="notificacion_downtime_parcial" id="notificacion_downtime_parcial" {__notificacion_downtime_parcial} {__form_disabled} onchange="cambiarAlertaUptime();">&nbsp;Parcial&nbsp;&nbsp;&nbsp;
			<!-- END TIENE_NOTIFICACION_PARCIAL -->
			<!-- BEGIN TIENE_NOTIFICACION_GRUPAL -->
			<input type="checkbox" name="notificacion_downtime_grupal" id="notificacion_downtime_grupal" {__notificacion_downtime_grupal} {__form_disabled} onchange="cambiarAlertaUptime();">&nbsp;Grupal&nbsp;&nbsp;&nbsp;
			<!-- END TIENE_NOTIFICACION_GRUPAL -->
			<!-- BEGIN TIENE_NOTIFICACION_GLOBAL -->
			<input type="checkbox" name="notificacion_downtime_global" id="notificacion_downtime_global" {__notificacion_downtime_global} {__form_disabled} onchange="cambiarAlertaUptime();">&nbsp;Global&nbsp;&nbsp;&nbsp;
			<!-- END TIENE_NOTIFICACION_GLOBAL -->
		</td>
	</tr>
	<tr>
		<th width="30%">Uptime</th>
		<td>
			<!-- BEGIN TIENE_NOTIFICACION_OK -->
			<input type="checkbox" name="notificacion_uptime_parcial" id="notificacion_uptime_parcial" {__notificacion_uptime_parcial} {__form_disabled}>&nbsp;Si
			<!-- END TIENE_NOTIFICACION_OK -->
		</td>
	</tr>
	<!-- BEGIN TIENE_NOTIFICACION_PATRON_INVERSO -->
	<tr>
		<th>Patron Inverso</th>
		<td>
			<input type="checkbox" name="notificacion_patron_inverso" {__notificacion_patron_inverso} {__form_disabled}>&nbsp;Si
		</td>
	</tr>
	<!-- END TIENE_NOTIFICACION_PATRON_INVERSO -->
</table>

<!-- BEGIN TIENE_NOTIFICACION_SLA -->
<br>
<br>
<table width="100%">
	<tr>
		<td class="tituloitemconfig">Configuraci&oacute;n de Umbrales</td>
	</tr>
	<tr>
		<td>
			<br>
			<div class="descripcion">
				&#8226; Los umbrales definen el tiempo de respuesta que se debe obtener para enviar una alerta.<br>
				&#8226; Los umbrales estan asociados a los objetivos, por lo que las modificaciones a los tiempos de umbrales se haran efectivas para todas las alertas de este objetivo.<br>
			</div>
		</td>
	</tr>
</table>
<br>
<table width="55%" class="formulario">
	<tr>
		<th width="30%">Alerta Umbral</th>
		<td>
			<input type="checkbox" name="notificacion_sla" {__notificacion_sla} onclick="cambiarInputSla();" {__form_disabled}>&nbsp;Si
		</td>
	</tr>
</table>
<br>
<table width="100%">
	<tr>
		<td>
		<!-- BEGIN LISTA_MONITORES -->
		<div id="grupo_monitor_{__monitor_selector}" class="celdaselector" onclick="cambiarGrupoConfiguracion('{__monitor_selector}');">
			{__monitor_nombre}
		</div>
		<!-- END LISTA_MONITORES -->
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
			<!-- BEGIN LISTA_PASOS_MONITORES -->
			<div id="grupo_monitor_sel_{__monitor_selector}" style="display:none;">
				<table width="100%" class="listado">
					<tr>
						<th width="5%">&nbsp;</th>
						<th>Nombre</th>
						<th width="10%">Timeout</th>
						<th width="10%">Umbral</th>
					</tr>
					<!-- BEGIN LISTA_PASOS -->
					<tr>
						<td align="center">{__paso_orden}</td>
						<td>{__paso_nombre}</td>
						<td>{__paso_timeout}</td>
						<td><input type="text" size="6" name="paso_sla_{__monitor_id}_{__paso_id}" value="{__paso_sla}" {__form_disabled}></td>
					</tr>
					<!-- END LISTA_PASOS -->
				</table>
			</div>
			<!-- END LISTA_PASOS_MONITORES -->
		</td>
	</tr>
</table>
<script>
cambiarGrupoConfiguracion(grupo_configuracion_anterior);
cambiarInputSla();
</script>
<!-- END TIENE_NOTIFICACION_SLA -->
<script>
cambiarAlertaUptime();
</script>