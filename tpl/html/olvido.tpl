<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Atentus.com: Reportes</title>
	<link rel="stylesheet" href="css/textos-reporte.css" type="text/css">
	<link rel="stylesheet" href="css/especiales.css" type="text/css"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	
	<script type="text/javascript" src="{__path_dojo}dojo/dojo.js" djconfig="parseOnLoad:true"></script>
		
	<script type="text/JavaScript">
		dojo.require("dojo.parser");

/*		function reloadImagen() {
			dojo.byId("cargaImagen").innerHTML = '<img src="imagen_validador.php?'+Date()+'"></img>';
		}*/
		
		function validarForm1() {
			var pass1 = document.olvidoForm.nuevaclave1.value;
			var pass2 = document.olvidoForm.nuevaclave2.value;
			if (pass1 != pass2) {
				alert("Las claves digitadas no coinciden.");
				return false;
			}
			if (pass1.length < 6 || pass1.length > 10) {
				alert("La clave no tiene el largo requerido.");
				return false;
			}
			document.olvidoForm.submit();
		}
	
		function validarForm2() {
			var correo=document.olvidoForm.email.value;
			var imagen=document.olvidoForm.clave_img.value;
			if(imagen == "") {
				alert("El texto de validación no puede ser vacio, vuelva a intentarlo.");
				return false
			}
			if(correo=="") {
				alert("El correo no es válido.");
				return false;
			}
			document.olvidoForm.submit();
		}	
	</script>
</head>
<body>
<form action="olvido.php" method="POST" name="olvidoForm" onsubmit="return validar()">
	<table align="center">
		<tr>
			<td class="box-olvido" valign="top">
				<table width="100%">
					<tr>
						<td height="80">&nbsp;</td>
					</tr>
					<tr>
						<td width="50%"></td>
						<td width="50%" align="center">
							<table width="80%">
								<tr>
									<td class="textblanco20">Recuperacion de Contrase&ntilde;a</td>
								</tr>
								<tr>
									<td class="textblanco12">{__mensaje}</td>
								</tr>
								<tr>
									<td height="30"></td>
								</tr>
								
								<!-- BEGIN BLOQUE_INGRESO -->
								<tr>
									<td class="textblanco18">Usuario</td>
								</tr>
								<tr>
									<td bgcolor="#ffffff" class="borderradius" height="25">
										<input style="padding: 0px; border: 0px;" name="email" size="35" type="text">
									</td>
								</tr>
								<tr>
									<td height="15"></td>
								</tr>
								<tr>
									<td class="textblanco18">Texto</td>
								</tr>
								<tr>
									<td>
										<table width="100%">
											<tr>
												<td width="100" bgcolor="#ffffff" class="borderradius" height="25">
													<input style="padding: 0px; border: 0px;" name="clave_img" size="12" type="text">
												</td>
 												<td width="15"></td>
												<td width="100" align="center" bgcolor="#929292" class="borderradius">
													<img src="imagen_validador.php" />
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td class="textblanco9">Ingrese el texto de la imagen</td>
								</tr>
								<tr>
									<td height="15"></td>
								</tr>
								<tr>
									<td align="center">
										<input type="button" onclick="validarForm2();" value="Enviar" class="botonnaranja borderradius"/>
										&nbsp;&nbsp;
										<input type="button" onclick="location='login.php';" value="Volver" class="botonnaranja borderradius"/>
									</td>
								</tr>
								<!-- END BLOQUE_INGRESO -->

								<!-- BEGIN BLOQUE_VALIDA -->
								<tr>
									<td class="textblanco18">Nueva Clave</td>
								</tr>
								<tr>
									<td bgcolor="#ffffff" class="borderradius" height="25">
										<input style="padding: 0px; border: 0px;" name="nuevaclave1" size="35" type="password">
										<input type="hidden" name="key1_clave" value="{__key1_clave}">
										<input type="hidden" name="key2_clave" value="{__key2_clave}">
									</td>
								</tr>
								<tr>
									<td height="15"></td>
								</tr>
								<tr>
									<td class="textblanco18">Repetir Nueva Clave</td>
								</tr>
								<tr>
									<td bgcolor="#ffffff" class="borderradius" height="25">
										<input style="padding: 0px; border: 0px;" name="nuevaclave2" size="35" type="password">
									</td>
								</tr>
								<tr>
									<td height="15"></td>
								</tr>
								<tr>
									<td align="center">
										<input type="button" onclick="validarForm1();" value="Aceptar" class="botonnaranja borderradius"/>
										&nbsp;&nbsp;
										<input type="button" onclick="location='login.php';" value="Volver" class="botonnaranja borderradius"/>
									</td>
								</tr>
								<!-- END BLOQUE_VALIDA -->
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="20" bgcolor="#54504f" class="textblanco9" align="center">Todos los derechos reservados - Atentus {__sitio_anno}</td>
		</tr>
	</table>
</form>
</body>
</html>
