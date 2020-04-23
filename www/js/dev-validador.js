function existeClaveActualUsuario(usuario_clave_actual){
	var resultado = 0;
	var sitio_id = document.form_principal.sitio_id.value;
	var menu_id = document.form_principal.menu_id.value;
	var objeto_id = document.form_principal.objeto_id.value;
	dojo.xhrPost({
		url: "index.php",
		postData: "sitio_id="+sitio_id+"&menu_id="+menu_id+"&objeto_id="+objeto_id+
			 "&ejecutar_accion=1&accion=verificar_clave_actual_usuario"+
			 "&usuario_clave_actual="+usuario_clave_actual,
		sync: true,
		load: function(data){
			resultado = data;
		}
	});
	return resultado;
}

function existeEmailUsuario(usuario_cliente_email, usuario_cliente_id){
	var resultado = 1;
	var sitio_id = document.form_principal.sitio_id.value;
	var menu_id = document.form_principal.menu_id.value;
	var objeto_id = document.form_principal.objeto_id.value;
	dojo.xhrPost({
		url: "index.php",
		postData: "sitio_id="+sitio_id+"&menu_id="+menu_id+"&objeto_id="+objeto_id+
			 "&ejecutar_accion=1&accion=verificar_email_usuario"+
			 "&usuario_cliente_id="+usuario_cliente_id+"&usuario_cliente_email="+usuario_cliente_email,
		sync: true,
		load: function(data){
			resultado = data;
		}
	});
	return resultado;
}

function existeNombreUsuario(usuario_cliente_nombre, usuario_cliente_id){
	var resultado = 1;
	var sitio_id = document.form_principal.sitio_id.value;
	var menu_id = document.form_principal.menu_id.value;
	var objeto_id = document.form_principal.objeto_id.value;
	dojo.xhrPost({
		url: "index.php",
		postData: "sitio_id="+sitio_id+"&menu_id="+menu_id+"&objeto_id="+objeto_id+
			 "&ejecutar_accion=1&accion=verificar_nombre_usuario"+
			 "&usuario_cliente_id="+usuario_cliente_id+"&usuario_cliente_nombre="+usuario_cliente_nombre,
		sync: true,
		load: function(data){
			resultado = data;
		}
	});
	return resultado;
}

function existeNombreSubcliente(subcliente_nombre, subcliente_id){
	var resultado = 1;
	var sitio_id = document.form_principal.sitio_id.value;
	var menu_id = document.form_principal.menu_id.value;
	var objeto_id = document.form_principal.objeto_id.value;
	dojo.xhrPost({
		url: "index.php",
		postData: "sitio_id="+sitio_id+"&menu_id="+menu_id+"&objeto_id="+objeto_id+
			 "&ejecutar_accion=1&accion=verificar_nombre_subcliente"+
			 "&subcliente_id="+subcliente_id+"&subcliente_nombre="+subcliente_nombre,
		sync: true,
		load: function(data){
			resultado = data;
		}
	});
	return resultado;
}

function existeNombreObjetivo(objetivo_nombre, objetivo_id){
	var resultado = 1;
	var sitio_id = document.form_principal.sitio_id.value;
	var menu_id = document.form_principal.menu_id.value;
	var objeto_id = document.form_principal.objeto_id.value;
	dojo.xhrPost({
		url: "index.php",
		postData: "sitio_id="+sitio_id+"&menu_id="+menu_id+"&objeto_id="+objeto_id+
			 "&ejecutar_accion=1&accion=verificar_nombre_objetivo"+
			 "&objetivo_id="+objetivo_id+"&objetivo_nombre="+objetivo_nombre,
		sync: true,
		load: function(data){
			resultado = data;
		}
	});
	return resultado;
}

function existeNombreDestinatario(destinatario_nombre, destinatario_id){
	var resultado = 1;
	var sitio_id = document.form_principal.sitio_id.value;
	var menu_id = document.form_principal.menu_id.value;
	var objeto_id = document.form_principal.objeto_id.value;
	dojo.xhrPost({
		url: "index.php",
		postData: "sitio_id="+sitio_id+"&menu_id="+menu_id+"&objeto_id="+objeto_id+
			 "&ejecutar_accion=1&accion=verificar_nombre_destinatario"+
			 "&destinatario_id="+destinatario_id+"&destinatario_nombre="+destinatario_nombre,
		sync: true,
		load: function(data){
			resultado = data;
		}
	});
	return resultado;
}

function existeNombreHorario(horario_nombre, horario_id){
	var resultado = 1;
	var sitio_id = document.form_principal.sitio_id.value;
	var menu_id = document.form_principal.menu_id.value;
	var objeto_id = document.form_principal.objeto_id.value;
	dojo.xhrPost({
		url: "index.php",
		postData: "sitio_id="+sitio_id+"&menu_id="+menu_id+"&objeto_id="+objeto_id+
			 "&ejecutar_accion=1&accion=verificar_nombre_horario"+
			 "&horario_id="+horario_id+"&horario_nombre="+horario_nombre,
		sync: true,
		load: function(data){
			resultado = data;
		}
	});
	return resultado;
}
