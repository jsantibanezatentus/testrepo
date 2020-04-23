<script>
function validarForm() {
	if (trim(dojo.byId("usuario_nombre").value) == "") {
		alert("Debe ingresar un nombre.");
		return false;
	}
	if (existeNombreUsuario(dojo.byId("usuario_nombre").value, dojo.byId("usuario_cliente_id").value)=="1") {
		alert("Ya existe el nombre en el sistema.");
		return false;
	}
	if (dojo.byId("usuario_clave1").value!=dojo.byId("usuario_clave2").value) {
		alert("Las claves deben ser iguales.");
		return false;
	}
	if (dojo.byId("usuario_clave1").value!="" && existeClaveActualUsuario(dojo.byId("usuario_clave_actual").value)==0) {
		alert("Ingrese su clave actual.");
		return false;
	}

	var e = document.getElementById("sonido_id");
	var sound = e.options[e.selectedIndex].value;
	$.ajax({
        async: false,
        type: 'POST',
        url: '../call_ajax.php',
        data: {'sound': sound, 'nameFunction': 'SemaforoSonido'},
            success: function(data) {
            	console.log(data)
            },
            error: function(error) {
            }
        });
	abrirAccion(1,'guardar_perfil');
}

<!-- BEGIN MOSTRAR_MENSAJE -->
alert("Los cambios fueron realizados con éxito.");
<!-- END MOSTRAR_MENSAJE -->
</script>

<input type="hidden" name="usuario_cliente_id" id="usuario_cliente_id" value="{__usuario_cliente_id}">
<table width="100%">
	<tr>
		<td class="tituloseccion">Mi Perfil</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
			<table width="50%" class="formulario">
				<tr>
					<th width="140">Nombre</th>
					<td><input type="text" name="usuario_nombre" id="usuario_nombre" value="{__usuario_nombre}" {__form_disabled} /></td>
				</tr>
				<tr>
					<th>E-mail</th>
<!-- 					<td><input type="text" name="usuario_email" id="usuario_email" value="{__usuario_email}" /></td> -->
 					<td>{__usuario_email}</td>
				</tr>
				<tr>
					<th>Tel&eacute;fono</th>
					<td><input type="text" name="usuario_telefono" value="{__usuario_telefono}" {__form_disabled} /></td>
				</tr>
				<tr>
					<th>Cargo</th>
					<td><input type="text" name="usuario_cargo" value="{__usuario_cargo}" {__form_disabled} /></td>
				</tr>
				<tr>
					<th>Zona Horaria</th>
					<td>
						<select name="zona_horaria_id" {__form_disabled}>
							<!-- BEGIN ZONAS_HORARIAS_USUARIO -->
							<option value='{__zona_horaria_id}' {__zona_horaria_sel}>{__zona_horaria_nombre}</option>
							<!-- END ZONAS_HORARIAS_USUARIO -->
						</select>
					</td>
				</tr>
<!-- 				<tr>
					<th>Idioma</th>
					<td>
						<select name="idioma_id" {__form_disabled}> -->
							<!-- BEGIN IDIOMAS_USUARIO -->
<!-- 							<option value='{__idioma_id}' {__idioma_sel}>{__idioma_nombre}</option> -->
							<!-- END IDIOMAS_USUARIO -->
<!-- 						</select>
					</td>
				</tr> -->
			</table>
		</td>
	</tr>
	<tr>
		<td height="40"></td>
	</tr>
	<tr>
		<td class="tituloitem">Configuraciones del Sistema</td>
	</tr>
	<tr>
		<td>
			<br>
			<div class="descripcion">
				&#8226; Si se realiza alg&uacute;n cambio en esta configuraci&oacute;n, este puede demorar algunos minutos en reflejarse en la secci&oacute;n de reportes.<br>
				&#8226; Periodo Sem&aacute;foro es el rango de tiempo utilizado para mostrar la tabla Sem&aacute;foro y el gr&aacute;fico Disponibilidad Global en la secci&oacute;n de Reportes.<br> 
			</div>
			<br>
		</td>
	</tr>
	<tr>
		<td>
			<table width="50%" class="formulario">
				<tr>
					<th width="140">Periodo Sem&aacute;foro</th>
					<td>
						<select name="intervalo_id" {__form_disabled}>
							<!-- BEGIN INTERVALOS_USUARIO -->
							<option value='{__intervalo_id}' {__intervalo_sel}>{__intervalo_nombre}</option>
							<!-- END INTERVALOS_USUARIO -->
						</select>
					</td>
				</tr>
				<tr>
					<th>Orientaci&oacute;n Sem&aacute;foro</th>
					<td>
						<select name="orientacion_id">
							<option value='0' {__orientacion_normal_sel}>Normal</option>
							<option value='1' {__orientacion_invertida_sel}>Invertido</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Sonido Sem&aacute;foro</th>
					<td>
						<select id="sonido_id">
						  <option value="0" selected="selected">apagado</option>
						  <option value="1">encendido</option>
						</select>
					</td>
				</tr>
			</table>
		</td>
	</tr>
		<tr>
		<td height="40"></td>
	</tr>
	<tr>
		<td class="tituloitem">Cambiar Clave</td>
	</tr>
	<tr>
		<td>
			<br>
			<div class="descripcion">
				&#8226; Ingrese los siguientes campos solo si quiere cambiar la clave actual.<br> 
			</div>
			<br>
		</td>
	</tr>
	<tr>
		<td>
			<table width="50%" class="formulario">
				<tr>
					<th width="140">Clave Actual</th>
					<td><input type="password" name="usuario_clave_actual" id="usuario_clave_actual" value="" {__form_disabled} /></td>
				</tr>
				<tr>
					<th>Nueva Clave</th>
					<td><input type="password" name="usuario_clave1" id="usuario_clave1" value="" {__form_disabled} /></td>
				</tr>
				<tr>
					<th>Repetir Nueva Clave</th>
					<td><input type="password" name="usuario_clave2" id="usuario_clave2" value="" {__form_disabled} /></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<br>
			<br>
			<!-- BEGIN PUEDE_MODIFICAR -->
			<table align="center">
				<tr>
					<td style="text-align:right">
						<input type="button"  class="boton_accion" onclick="validarForm()" value="Guardar" />					
					</td>
					<td style="width:20px">&nbsp;</td>
					<td style="text-align:left">
						<input type="button" class="boton_cancelar" value="Cancelar" onclick="mostrarSubmenu({__padre_id},{__seccion_id},{__nivel})" />
					</td>
				</tr>
			</table>
			<br>
			<br>
			<!-- END PUEDE_MODIFICAR -->
		</td>
	</tr>
</table>