<html>
	<head>
		<link rel="stylesheet" href="css/textos-reporte.css" type="text/css"/>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

		<script>

		function validarForm() {
			if (document.login_admin.username_admin.value=="") {
				alert("Debe ingresar un usuario administrador.");
				return false;
			}
			if (document.login_admin.password_admin.value=="") {
				alert("Debe ingresar una contraseña.");
				return false;
			}
			if (document.login_admin.username.value=="") {
				alert("Debe ingresar un usuario con el cual ingresar al sistema.");
				return false;
			}
			document.login_admin.submit();
		}
		</script>
	</head>
	<body>
		<form onSubmit="return true;" method="post" name="login_admin">
		<img src="{__path_img}header_admin.png">
		<table width="500" height="150" align="center" style="margin-top: 5px; background-color: #ffffff; border: solid 3px #f47001">
			<!-- BEGIN TIENE_MENSAJE -->
			<tr>
				<td class="error" colspan="100%">
					{__msg_error}<br>
					Por favor, int&eacute;ntelo nuevamente.
				</td>
			</tr>
			<!-- END TIENE_MENSAJE -->
			<tr>
				<td>
					<table width="300" align="center" style="padding: 5px; border-spacing: 5px; border-collapse: separate;">
						<tr>
							<td class="textnegro12">Administrador</td>
							<td><input type="text" name="username_admin"></td>
						</tr>
						<tr>
							<td class="textnegro12">Contrase&ntilde;a</td>
							<td><input type="password" name="password_admin"></td>
						</tr>
						<tr>
							<td height="10" colspan="100%"></td>
						</tr>
						<tr>
							<td class="textnegro12">Usuario Reporte</td>
							<td><input type="text" name="username" value="{__usuario_email}" {__usuario_sel}></td>
						</tr>
						<tr>
							<td align="center" colspan="100%"><input type="button" class="boton_accion" value="Entrar" onclick="validarForm();"></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</form>
		<script type="text/javascript">
			var _gaq = _gaq || [];

			_gaq.push(['_setAccount', '{__ga_tracking_id}']);
			_gaq.push(['_setCustomVar', 1, 'uid', '<ninguno>', 3]);
			_gaq.push(['_setCustomVar', 2, 'oid', '<ninguno>', 3]);

			_gaq.push(['_trackPageview', '{__path_ga}']);

			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
	</body>
</html>
