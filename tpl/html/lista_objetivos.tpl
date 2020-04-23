<script>
function DropItems(target,nodes) {
	var lista = "";
	var x = contenedor.getAllNodes();
	for(i =0;i<x.length;i++) {
		lista = lista+"&"+x[i].id+"="+i;
	}
	guardarOrden(lista);
	if(lista !=""){
		updateOrdenapi()
	}
}
function updateOrdenapi(){
	$.ajax({
            async: false,
            type: 'POST',
            url: 'utils/updateObjetives.php',
            data: {'user': '{__cliente_usuario_id}','function': 'update_objetives'},
            		success: function(data) {
            			console.log(data)
            	},
                	error: function(error) {
                }
        });
}
function initDND(){
	dojo.dnd.Avatar.prototype._generateText = function() {
		return "<span class=textoblack2>Moviendo "+this.manager.nodes.length+" Objetivo"+ 
	    	   (this.manager.nodes.length != 1 ? "s" : "")+"</span>";
	};
    // usamos eventos dojo en vez de colgarnos de los topics (en este caso, /dnd/drop)
    dojo.connect(contenedor, "onDrop", DropItems);
}

dojo.addOnLoad(initDND);
</script>

<!--
  -- Inicio de la lista de objetivos 
  -->
<input type="hidden" name="objetivo_id" value="0">
<table width="100%">
	<tr>
		<td class="tituloseccion">{__sitio_titulo}</td>
	</tr>
 	<tr>
		<td>
			<br>
			<div class="descripcion">
				&#8226; En esta secci&oacute;n usted puede configurar el orden en que se ver&aacute;n sus objetivos en el sistema.<br> 
				&#8226; Para cambiar un objetivo de posici&oacute;n solo presione sobre &eacute;l y desl&iacute;celo a la posici&oacute;n que desee.
			</div>
			<br>
		</td>
	</tr>
    <tr>
    	<td>
    		<table width="100%">
    			<tr>
					<td class="celdanegra50" style="border-left: solid 1px #ffffff; border-right: solid 1px #ffffff;">Objetivos de monitoreo</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" class="listado">
				<tr>
					<th width="10"></th>
					<th width="200">Nombre</th>
					<th width="260">Descripci&oacute;n</th>
					<th width="150">Servicio</th>
					<th width="30">&nbsp;</th>
				</tr>
			</table>
			
			<div dojoType="dojo.dnd.Source" jsId="contenedor" class="dndContainer" style="width: {ancho_resumen}px;background-color: #ffffff;">
				<script type="dojo/method" event="creator" args="item, hint">
					var node = dojo.doc.createElement("div"), s = String(item);
					node.id = dojo.dnd.getUniqueId();
					node.className = "dojoDndItem";
					node.innerHTML = s;
					return {node: node, data: item, type: ["text"]};
				</script>

				<!-- BEGIN LISTA_OBJETIVOS -->
				<div class="dojoDndItem" id="objetivo_orden_{__objetivo_id}">
					<table width="100%" class="listado" style="margin-top: -1px;">
						<tr>
							<td width="10" align="center">
								<i class="spriteButton spriteButton-mover"></i>
							</td>
							<td width="200">
								<div style="white-space: nowrap; overflow: hidden; width: 195px;" id="mis_obj_{__objetivo_id}_{__objetivo_id}">{__objetivo_nombre}</div>
									<div dojoType="dijit.Tooltip" connectId="mis_obj_{__objetivo_id}_{__objetivo_id}">
								<div class="textnegro12">{__objetivo_nombre}</div>
							</div>
							</td>
							<td width="260"><div style="white-space: nowrap; overflow: hidden; width: 255px;" id="mis_obj_{__objetivo_id}_{__descripcion_id}">{__objetivo_descripcion}</div>
								<div dojoType="dijit.Tooltip" connectId="mis_obj_{__objetivo_id}_{__descripcion_id}">
									<div class="textnegro12">{__objetivo_descripcion}</div>
								</div>
							</td>
							<td width="150" id="mis_obj_{__objetivo_id}_{__servicio_id}">{__servicio_nombre}
								<div dojoType="dijit.Tooltip" connectId="mis_obj_{__objetivo_id}_{__servicio_id}">
								<div class="textnegro12">{__servicio_nombre}</div>
								</div>
							</td>
							<td width="30" align="center">
								<a href="#" onclick="abrirAccion(0,'modificar_objetivo',['objetivo_id','{__objetivo_id}']); return false;">
								<i class="spriteButton spriteButton-editar" border="0" title="Modificar Objetivo" ></i>
								</a>
							</td>
						</tr>
					</table>
				</div>
				<!-- END LISTA_OBJETIVOS -->
			</div>
		</td>
	</tr>
</table>
<br>
