<link rel="stylesheet" href="{__path_jquery_ui}css/multiselect/common.css" type="text/css" />
<link type="text/css" href="{__path_jquery_ui}css/multiselect/ui.multiselect.css" rel="stylesheet" />
<link type="text/css" href="{__path_jquery_ui}css/multiselect/ui.multiselect.css" rel="stylesheet" />
<link rel="stylesheet" href="{__path_jquery_ui}css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
<link rel="stylesheet" href="{__path_jquery_ui}css/jquery-data-table/jquery.dataTables.css"></link>

<script type="text/javascript" src="{__path_jquery_ui}js/jquery-ui-1.10.min.js"></script>
<script type="text/javascript" src="../../js/controlador_mantenedor.js"></script>
<script type="text/javascript" src="{__path_jquery_ui}js/jquery-multi-select/plugins/localisation/jquery.localisation-min.js"></script>
<script type="text/javascript" src="{__path_jquery_ui}js/jquery-multi-select/plugins/scrollTo/jquery.scrollTo-min.js"></script>
<script type="text/javascript" src="{__path_jquery_ui}js/jquery-multi-select/ui.multiselect.js"></script>
     
</head>

<input type="hidden" id="usuario_cliente_id" value="{__usuario_id}">
<input type="hidden" id="nombre_usuario" value="{__nombre_usuario}">
<input type="hidden" id="zona_horaria" value="{__zona_horaria}">
<input type="hidden" id="evento_id" value="{__evento_id}">
<input type="hidden" id="cliente_id" value="{__cliente_id}">
<input type="hidden" id="nombre_cliente" value="{__nombre_cliente}">

<table width="100%">
	<tr>
		<td class="tituloseccion">Historial <a href="#" id="descargar_csv" ><i class="spriteButton spriteButton-exportar imgLeft" border="0" title="Descarga CSV"></i></a></td>
	</tr>
	<tr>
		<td>
		
		<br>
		</td>
	</tr>
	<tr>
		<td>
			<div class="descripcion">
				• A continuación se muestra el historial de registros de eventos.
				<br>
				• Puede editar el evento al hacer click en el ícono <i class="spriteButton spriteButton-editar" ></i>
			</div>
		</td>
	</tr>
	<tr>
		<td>
		<br>
		<div id="load_evento" style="display: block;" align="center">
			<img src="img/cargando.gif">
				<span class="textgris12"> Por favor espere.<br>La información se esta cargando.</span>
		</div>
			<table cellpadding="0" cellspacing="0" border="0" class="dataTable listadoMantenedor" id="example">
				<thead>
					<tr>
						<th>Id</th>
						<th>Nombre Usuario</th>
						<th>F. Inicio</th>
						<th>F. Termino</th>
						<th>Título</th>
						<th>Estado</th>
						<th>Objetivos</th>
						<th width="30">&nbsp;</th>
					</tr>
				</thead>
				<tbody id="register" class="buscar" >
                </tbody>
			</table>
		</td>
	</tr>
</table>
<div id="dialog-obj" title="Objetivos" style="display:none"; >
	<table id = "tableObj" class="listado" cellspacing="0" width="100%">
		<tbody id="tbodyObjMan" style="background-color:#d0d0d0">
        </tbody>
	</table> 
</div>     
<div id="dialog-calendario" title="Seleccione rango de fechas" style="display:none"; >
	<div id="calendario_especial">
	    <img class="indicador-carga" src="/img/cargando.gif" title="cargando calendario" alt="cargando calendario" />
	</div>
</div>
<div id="dialog-message" title="Modificar Evento" style="display:none"; >
	<div class="descripcion addStyleDescr">
				• No es necesario llenar los campos : Fecha Creación, Fecha Modificación.
				<br>
				• Agregar o Quitar objetivos queda Deshabilitado, cuando el estado es "Cancelado" no se puede volver a habilitar.
				<br>
				• Puede definir fecha inicio y fecha termino al hacer click en el icono <i class='spriteButton  icon-description'></i>.
				
	</div>

	<div class="divContainer">
	    <div class="divContainerFormLeft">
		    <label class="labelMantainer" for="id" id = "label">Id: </label>
		    <input class="inputMaintainer" type="text" name="id" id="id" readonly="readonly"  disabled>
		</div>
	    <div class="divContainerFormRigth">
	    	<label  class="labelMantainer" for="nombre">nombre: </label>
	    	<input class="inputMaintainer" type="text" name="nombre" id="nombre" disabled>
	    </div>
	</div>

	<div class="divContainer">
	    <div class="divContainerFormLeft">
		    <label class="labelMantainer" for="usuarioId">Usuario Id: </label>
	    	<input class="inputMaintainer" type="text" name="usuarioId" id="usuarioId" readonly="readonly" disabled>
		</div>
	    <div class="divContainerFormRigth">
	    	<label class="labelMantainer" for="estado" id = "label">Estado: </label>
			<select class="selectMaintainer" name="select" id="estado">
			  <option value="1">Ingresado</option> 
			  <option value="2" >Cancelado</option>
			</select>
	    </div>
	</div>

	<div class="divContainer">
	    <div class="divContainerFormLeft">
		    <label class="labelMantainer" for="fechaI" id = "label">Fecha Inicio: </label>
	    	<input class="inputMaintainer" type="text" name="fechaI" id="fechaI"  readonly="readonly"  disabled>
		</div>
	    <div class="divContainerFormRigth">
	    	<label class="labelMantainer" for="fechaT" id = "label">Fecha Termino: </label>
	    	<input class="inputMaintainer" type="text" name="fechaT" id="fechaT" readonly="readonly"  disabled>
	    </div>
	</div>
		

	<div class="divContainer">
	    <div class="divContainerFormLeft">
		    <label class="labelMantainer" for="fechaC" id = "label">Fecha Creación: </label>
	    	<input class="inputMaintainer" type="text" name="fechaC" id="fechaC"  disabled>
		</div>
	    <div class="divContainerFormRigth">
	    	 <label class="labelMantainer" for="fechaT" id = "label">Fecha Modificación: </label>
	    	<input class="inputMaintainer" type="text" name="fechaM" id="fechaM"  disabled>
	    </div>
	</div>

	
	<br>
	<div class="divContainer">
	   <div class="divContainerFormComplete">
		     <label class="labelMantainer" for="titulo" >Título: </label>
	    	<input class="inputMaintainerLarge" type="text" name="name" id="titulo" required>
		</div>
	   
	</div>
	
	<div class="divContainer" style="height : 80px">
	    <div class="divContainerFormComplete">
		    <label class="labelMantainer" for="comentario" >Comentario: </label>
		    <textarea class="textAreaMaintainer" rows="4" cols="50" type="text" name="comentario" id="comentario" required>
		    </textarea>
		</div>
	</div>
	<div class="divContainer">	
	    <p class="panel">
		    <label class="labelMantainer" for="objetivos" >Objetivos :</label>
	   	</p>
	</div>
	</br>
	<div class="containerobjetivos">
		<table id="objetive" class="listado" style="width: 100%;"></table>
		<br/><br/>
	</div>
	<div class="divContainer" id="inputSave">
		<button class="btn" type="button" name="guardar" id="guardar" value="Editar" >Editar</button>
		<button  class="boton_cancelar" value="Cancelar" id="close"> Cancelar</button>
	</div>
</div>
