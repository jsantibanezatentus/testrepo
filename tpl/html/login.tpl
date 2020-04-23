<html>
	<head>
		<title>Atentus.com: Reportes</title>
		<link rel="stylesheet" href="css/textos-reporte.css" type="text/css"/>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<script type="text/javascript" src="tools/jquery/js/jquery-1.7.1.min.js"></script>
		
        <script>
			if (window.opener) {
				window.opener.logout();
				window.close();
			}
		</script>
	</head>

	<body onload="document.login_form.username.focus();">
		<form name="login_form" method="post" onSubmit="return true;">
		<table align="center">
			<tr>
				<td class="box" valign="top">
					<table width="100%">
						<tr>
							<td colspan="100%" height="207"></td>
						</tr>
						<tr>
							<td width="50%"><div style="float: right; background-color: #f47001; padding: 1px;" class="borderradius"><img src="img/header_blanco.png"/></i></div></td>
							<td width="50%"></td>
						</tr>
						<tr>
							<td width="50%" class="celdablanca100 borderradius" align="center">Sistema de Reportes</td>
							<td width="50%"></td>
						</tr>
						<tr>
							<td colspan="100%" height="15"></td>
						</tr>
						<tr>
							<td width="50%">
								<table align="center">
									<tr>
										<td align="right"><div style="float: right;" class="celdanaranjalogin borderradius">Usuario:</div></td>
										<td width="5"></td>
										<td bgcolor="#ffffff" class="borderradius"><input style="padding: 0px; border: 0px; width: 100%" type="text" name="username" size="10"/></td>
									</tr>
									<tr>
										<td colspan="100%" height="5"></td>
									</tr>
									<tr>
										<td align="right"><div style="float: right;" class="celdanaranjalogin borderradius">Contraseña:</div></td>
										<td width="5"></td>
										<td bgcolor="#ffffff" class="borderradius"><input style="padding: 0px; border: 0px; width: 100%" type="password" name="password" size="20"/></td>
									</tr>
									<tr>
										<td colspan="100%" height="10"></td>
									</tr>
									<tr>
										<td colspan="100&" align="right">
											<p class="olvido" onclick="ShowModal()" style="width: 175px;">
												<a href="#" style="color: white; font-size: 13px;">Ha olvidado su contraseña?</a>
											</p>
										</td>
									</tr>
									<tr>
										<td colspan="100&" align="right" style="padding-top: 5px;">
											<div id="submit_form">
												<input type="submit" class="botonnaranja borderradius" value="Entrar" />
											</div>
										</td>
									</tr>
									
								</table>
							</td>
							<td valign="top">
						<!-- BEGIN TIENE_MENSAJE -->
						<table>
						<tr>
							<td class="celdanegra50 borderradius" align="center">
								{__msg_error}<br>
								
							</td>
						</tr>
						</table>
						<!-- END TIENE_MENSAJE -->
							</td>
						</tr>
						<tr>
							<td colspan="100%" height="70"></td>
						</tr>
						<tr>
							<td colspan="100%" align="right">
								<table>
									<tr>
										<td>
											<a href="https://itunes.apple.com/us/app/appmobile-atentus/id1326836795" target="_blank" >
												<img alt='Disponible en App Store' src='img/icono_appstore.png'  style="width: 85%; height: 85%;" />
											</a>
										</td>
										<td >
											<a href='https://play.google.com/store/apps/details?id=com.atentus.appmobileatentus&hl=es&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1' target="_blank" >
												<img alt='Disponible en Google Play' src='https://play.google.com/intl/en_us/badges/images/generic/es-419_badge_web_generic.png'  style=" width: 20%; height: 20%;" />
											</a>
										</td>
										<td><i class="sprite sprite-argentina"></i></td>
										<td width="5"></td>
										<td><i class="sprite sprite-chile"></i></td>
										<td width="5"></td>
										<td><i class="sprite sprite-colombia"></i></td>
										<td width="5"></td>
										<td><i class="sprite sprite-mexico"></i></td>
										<td width="5"></td>
										<td><i class="sprite sprite-peru"></i></td>
										<td width="5"></td>
										<td><i class="sprite sprite-uruguay"></i></td>
										<td width="5"></td>

									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height="20" bgcolor="#54504f" class="textblanco12" align="center">Ayuda y consultas a:   soporte@atentus.com</td>
			</tr>
			<tr>
				<td height="20" bgcolor="#54504f" class="textblanco9" align="center">Todos los derechos reservados - Atentus {__anno}</td>
			</tr>
		</table>
		<div id="counter"></div>
		</form>

		<div id="id01" class="modal_olvido" style="display: none;" >
		  <form class="modal-content animate" style="width: 350;height: 280;">
		    <div class="imgcontainer"  align="left">
		      <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
		      <img src="img/header_blanco.png" alt="Avatar"  class="avatar">
		    </div>
		    <div class="container" style="margin-top: 30px;">
		    	<p style="font-size: 13px; color: #535353; text-align: left;padding-left: 10px">  Ingrese su correo electrónico y le enviaremos un enlace para crear una nueva contraseña.</p>
		    	<div align="center">
			     	<input type="email" id="mail" onfocus="this.value=''"  placeholder="Correo electrónico" name="mail" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" style="height: 30px; width: 65%" required="">
			     	<p style="margin-bottom: 5px; font-size: 15px; text-align: center;height: 5px;" id = "callback_mail"><p>
		    	</div>
		    	<div id="buttons" align="center" >
		    		<input id="button_send" style="background-color: #f47001;width: 65%" type="button"  onclick="sendMail()" Value="CONTINUAR"></input>

			      	<input id="button_cancel" style="margin-top: 8px;width: 65%" type="button" onClick="Cancel()" value="CANCELAR"></input>
		    	</div>
		    </div>
		  </form>
		</div>
	</body>
	
	<script type="text/javascript">
		var	callback= $('#callback_mail')
		
		//FUNCION QUE MUESTRA MODAL
		function ShowModal(){
			document.getElementById("mail").value="";
			callback.html("")
			$("#id01").show()
		}

		//ESCONDE MODAL
		function Cancel(){
			mod = $("#id01")
			mod.hide()
		}
		//METODO QUE ENVIA CORREO A API
		function sendMail(){
			var email = document.getElementById("mail").value;
				$.ajax({
			            type: 'POST',
			            url: 'utils/recovery.php',
			            data: {'mail': email, 'function': 'mailCheck'},
			            success: function(data) {
			            	if(data=='true'){
			            		callback.css("color","green")
			            		callback.html('Un link ha sido enviado a su correo')
			            	}else{
			            		callback.css("color","red")
			            		callback.html("Ingrese un correo valido")
			            	}
			      		}
			        });
		}
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
	
</html>

<style>
	body {font-family: Arial, Helvetica, sans-serif;}

	input[type=button] {
	    padding:8px 15px; 
	    background:#ccc; 
	    border:0 none;
	    cursor:pointer;
	    -webkit-border-radius: 5px;
	    border-radius: 5px;
	    width: 100%;
	    box-shadow: 1px;
	    color:white;

	}
	p.olvido:hover {
			/*background-color: #f47001;*/
			text-shadow: 0 0 7px  #f47001;

		}

	/* Extra styles for the cancel button */
	.cancelbtn {
	  width: auto;
	  padding: 10px 18px;
	  background-color: #f44336;
	}

	/* Center the image and position the close button */
	.imgcontainer {
	  position: relative;
	  background-color: #f47001;
	}

	img.avatar {
	  width: 40%;
	  margin-top: 10px;
	  margin-left: 100px;
	}

	.container {
		padding-right: 8px;
		padding-left:8px;
		height: 100%;
	}


	/* The Modal (background) */
	.modal_olvido {
	  display: none; /* Hidden by default */
	  position: fixed; /* Stay in place */
	  z-index: 1; /* Sit on top */
	  left: 0;
	  top: 0;
	  width: 100%; /* Full width */
	  height: 100%; /* Full height */
	  overflow: auto; /* Enable scroll if needed */
	  background-color: rgb(0,0,0); /* Fallback color */
	  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
	}

	/* Modal Content/Box */
	.modal-content {
	  background-color: #fefefe;
	  margin: 5% auto 15% auto; /* 5% from the top, 15% from the bottom and centered */
	  border: 1px solid #888;
	  width: 80%; /* Could be more or less, depending on screen size */
	}

	/* The Close Button (x) */
	.close {
	  position: absolute;
	  right: 25px;
	  top: 0;
	  color: #696767;;
	  font-size: 35px;
	  font-weight: bold;
	}

	.close:hover,
	.close:focus {
	  color: white;
	  cursor: pointer;
	}

	/* Add Zoom Animation */
	.animate {
	  -webkit-animation: animatezoom 0.6s;
	  animation: animatezoom 0.6s
	}

	@-webkit-keyframes animatezoom {
	  from {-webkit-transform: scale(0)} 
	  to {-webkit-transform: scale(1)}
	}
	  
	@keyframes animatezoom {
	  from {transform: scale(0)} 
	  to {transform: scale(1)}
	}

	/* Change styles for span and cancel button on extra small screens */
	@media screen and (max-width: 300px) {
	  .cancelbtn {
	     width: 100%;
	  }
	}
</style>
