<link rel="stylesheet" href="{__path_jquery_ui}css/multiselect/common.css" type="text/css" />
<link type="text/css" href="{__path_jquery_ui}css/multiselect/ui.multiselect.css" rel="stylesheet" />

<link rel="stylesheet" href="{__path_jquery_ui}css/jquery-ui-css/jquery-ui-1.10.min.css"></link>

<script type="text/javascript" src="{__path_jquery_ui}js/jquery-ui-1.10.min.js"></script>
<script type="text/javascript" src="../../js/controlador_agregar_evento.js"></script>
<script type="text/javascript" src="{__path_jquery_ui}js/jquery-multi-select/plugins/localisation/jquery.localisation-min.js"></script>
<script type="text/javascript" src="{__path_jquery_ui}js/jquery-multi-select/plugins/scrollTo/jquery.scrollTo-min.js"></script>
<script type="text/javascript" src="{__path_jquery_ui}js/jquery-multi-select/ui.multiselect.js"></script>

<input type="hidden" id="usuario_cliente_id" value="{__usuario_id}">
<input type="hidden" id="nombre_usuario" value="{__nombre_usuario}">
<input type="hidden" id="zona_horaria" value="{__zona_horaria}">

<table width="100%">
	<tr>
		<td class="tituloseccion">Agregar Evento </td>
	</tr>
	<tr>
		<td>
		<br>
		</td>
	</tr>
	<tr>
		<td>
			<div class="descripcion">
				• Este formulario que permite agregar un evento de mantenimiento, a solicitud del usuario actual.
				<br>
				• No es necesario llenar los campos : Fecha Creación, Fecha Modificación.
				<br>
				• Puede definir fecha inicio y fecha termino al hacer click en el icono <i class='spriteButton  icon-description'></i>.
			</div>
		</td>
	</tr>
	
</table>
</br>
<div id="dialog-calendario" title="Seleccione rango de fechas" style="display:none"; >
	<div id="calendario_especial">
	    <img class="indicador-carga" src="/img/cargando.gif" title="cargando calendario" alt="cargando calendario" />
	</div>
</div>
<div id="formCrear" style="display:block"; >

	<div class="divContainer">
	    <div class="divContainerFormLeft">
	    	<label  class="labelMantainer" for="nombre">Nombre: </label>
	    	<input class="inputMaintainer" type="text" name="nombre" id="nombre" title="Nombre usuario" required>
	    </div>
	    <div class="divContainerFormRigth">
		    <label class="labelMantainer" for="usuarioId">Usuario Id: </label>
	    	<input class="inputMaintainer" type="text" name="usuarioId" id="usuarioId" title="Id usuario" readonly="readonly" required>
		</div>
	</div>

	<div class="divContainer">
	    
	    <div class="divContainerFormLeft">
	    	<label class="labelMantainer" for="estado" id = "label">Estado: </label>
			<select class="selectMaintainer" name="select" id="estado" title="Estado evento">
			  <option value="1" selected>Ingresado</option> 
			  <option value="2" >Cancelado</option>
			</select>
	    </div>
	    <div class="divContainerFormRigth">
		    <label class="labelMantainer" for="fechaI" id = "label">Fecha Inicio: </label>
	    	<input class="inputMaintainer" type="text" name="fechaI" id="fechaI"  readonly="readonly" title="Fecha inicio" required>
		</div>
	</div>

	<div class="divContainer">
	    
	    <div class="divContainerFormLeft">
	    	<label class="labelMantainer" for="fechaT" id = "label">Fecha Termino: </label>
	    	<input class="inputMaintainer" type="text" name="fechaT" id="fechaT" readonly="readonly" title="Fecha termino" required>
	    </div>
	    <div class="divContainerFormRigth">
		    <label class="labelMantainer" for="fechaC" id = "label">Fecha Creación: </label>
	    	<input class="inputMaintainer" type="text" name="fechaC" id="fechaC" title="Fecha creación" readonly="readonly" placeholder="Se define automaticamente" required>
		</div>
	</div>
		

	<div class="divContainer">
	    
	    <div class="divContainerFormLeft">
	    	 <label class="labelMantainer" for="fechaT" id = "label">Fecha Modificación: </label>
	    	<input class="inputMaintainer" type="text" name="fechaM" id="fechaM" title="Fecha Modificación" readonly="readonly" placeholder="Se define automaticamente" required>
	    </div>
	    <div class="divContainerFormRigth">
	    	<label class="labelMantainer" for="fecha" >Definir Rango de fechas: </label>
	    	<a href="#" id="calendario"><i class='spriteButton spriteButton-calendario icon' border='0' title='Definir rango de fecha'></i></a>
	    </div>
	</div>
	</br>
	<div class="divContainer">
	    <div class="divContainerFormComplete">
		     <label class="labelMantainer" for="titulo" >Título: </label>
	    	<input class="inputMaintainerLarge" type="text" name="name" id="titulo" title="Título" required>
		</div>
	    
	</div>
	
	<div class="divContainer" style="height : 80px">
	    <div class="divContainerFormComplete">
		    <label class="labelMantainer" for="comentario" >Comentario: </label>
		    <textarea class="textAreaMaintainer" rows="6" cols="50" type="text" name="comentario" id="comentario" title="Comentario" required>
		    </textarea>
		</div>
	</div>
	</br>
	<div class="divContainer">	
	    <div class="divContainerFormLeft">
		    <label class="labelMantainer" for="objetivos" >Objetivos :</label>
	   	</div>
	</div>
	</br>	
	<div class="containerMultiSelect">
	<select id="objetive" class="multiselect" multiple="multiple" name="objetive[]">
	        
    </select>
    <br/>
	</div>
	<div class="divContainer" id="inputSave">

		<button class="btn" data-role="button" type="button" name="guardar" id="guardar" value="Crear" >Agregar</button>
		<button  class="boton_cancelar" value="Cancelar" onclick="mostrarSubmenu(128,130,1)">Cancelar</button>
	</div>
	
</div>