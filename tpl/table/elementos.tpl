<script type="text/javascript" charset="utf-8">

	var asInitVals{__monitor_id} = new Array();

	$(document).ready(function() {
    	var oTable{__monitor_id} = $('#tabla_elementos_{__monitor_id}').dataTable({
        	"oLanguage": {"sSearch": ""},
        	"bAutoWidth" : false,
			"aoColumns": [null, {"asSorting": []}, null, null, {"asSorting": []}],
	   	    "bPaginate": false,
	        "bInfo": false,
	        
	        "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
				var iTamano = 0;
				for ( var i=iStart ; i<iEnd ; i++ ) {
					var parcial = aaData[ aiDisplay[i] ][2].replace(",", ".");
					iTamano += (isNaN(parseFloat(parcial)) ? 0 : parcial ) *1;
				}

				var iRespuesta = 0;
				for ( var i=iStart ; i<iEnd ; i++ ) {
					var parcial = aaData[ aiDisplay[i] ][3].replace(",", ".");
					iRespuesta += (isNaN(parseFloat(parcial)) ? 0 : parcial ) *1;
				}
				var nCells = nRow.getElementsByTagName('th');
				nCells[2].innerHTML = String(iTamano.toFixed(3)).replace(".", ",");
				nCells[3].innerHTML = String(iRespuesta.toFixed(3)).replace(".", ",");
			}
        	
	    });
     
    	$("#tabla_elementos_{__monitor_id} tfoot input").keyup( function() {
        	oTable{__monitor_id}.fnFilter(this.value, $("#tabla_elementos_{__monitor_id} tfoot input").index(this));
    	});
     
		$("#tabla_elementos_{__monitor_id} tfoot div").each( function(i) {
			this.innerHTML = fnCreateSelect(oTable{__monitor_id}.fnGetColumnData(i));
			$('select', this).change( function() {
				oTable{__monitor_id}.fnFilter($(this).val(), i);
			});
		});
     
		$("#tabla_elementos_{__monitor_id} tfoot input").each( function(i) {
			asInitVals{__monitor_id}[i] = this.value;
		});
     
		$("#tabla_elementos_{__monitor_id} tfoot input").focus( function() {
			if (this.className == "textgrisclaro11") {
				this.className = "";
				this.value = "";
			}
		});
     
		$("#tabla_elementos_{__monitor_id} tfoot input").blur( function(i) {
			if (this.value == "") {
				this.className = "textgrisclaro11";
				this.value = asInitVals{__monitor_id}[$("#tabla_elementos_{__monitor_id} tfoot input").index(this)];
			}
		});
	});
</script>

<table width="40%">
	<tr>
		<td class="celdaborde celdanegra40">Monitor</td>
		<td class="celdaborde celdanegra10">{__monitor_nombre}</td>
	</tr>
	<tr>
		<td class="celdaborde celdanegra40">Fecha</td>
		<td class="celdaborde celdanegra10">{__monitoreo_fecha}</td>
	</tr>
</table>
<br>
<table width="100%" id="tabla_elementos_{__monitor_id}" class="tablesorter">
	<thead>
		<tr>
			<th height="30">Elemento</th>
			<th height="30" width="50" align="center">Tipo</th>
			<th height="30" width="60" align="center">Tamaño<br>(KB)</th>
			<th height="30" width="60" align="center">Respuesta<br>(segs)</th>
			<th height="30" width="80" align="center">Estado</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th class="celdaborde celdanegra20">Total</th>
			<th class="celdaborde celdanegra20">&nbsp;</th>
			<th class="celdaborde celdanegra20" align="right">{__monitor_tamano}</th>
			<th class="celdaborde celdanegra20" align="right">{__monitor_respuesta}</th>
			<th class="celdaborde celdanegra20">&nbsp;</th>
		</tr>
	</tfoot>
	<tbody>
		<!-- BEGIN LISTA_ELEMENTOS -->
		<tr>
			<td height="22">
				<div style="white-space: nowrap; overflow: hidden; width: 340px;">
					<a href="{__elemento_url}" class="textgris10" target="_blank">{__elemento_url_corto}</a>
				</div>
			</td>
			<td height="22" align="center">
				<img title="{__elemento_tipo}" src="{__elemento_tipo_icono}">
				<div style="display: none">{__elemento_tipo}</div>
			</td>
			<td align="right">{__elemento_tamano}</td>
			<td height="22" align="right">{__elemento_respuesta}</td>
			<td align="center" style="background-color: #{__elemento_estado_color}" id="vista_detalle_{__tooltip_id}">
				<img src="{__elemento_estado_icono}">
				<div style="display: none">{__elemento_estado_nombre}</div>

				<!-- BEGIN TIENE_TOOLTIP -->
				<span dojoType="dijit.Tooltip" connectId="vista_detalle_{__tooltip_id}" position="below">
					<table>
						<tr>
							<td width="80" height="26" align="center" style="background-color: #{__elemento_estado_color};">
							<img src="{__elemento_estado_icono}" /></td>
							<td width="170" class="textnegro13" style="padding: 3px;">{__elemento_estado_nombre}</td>
						</tr>
						<tr>
							<td colspan="2" class="textnegro12" style="padding: 3px;">{__elemento_estado_descripcion}</td>
						</tr>
					</table>
				</span>
				<!-- END TIENE_TOOLTIP -->
			</td>
		</tr>
		<!-- END LISTA_ELEMENTOS -->
	</tbody>
	<tfoot>
	 	<tr>
			<th>
				<div style="display:none;"></div>
				<input type="text" name="search_engine" value="Buscar Texto" class="textgrisclaro11" style="width: 100%;"/>
			</th>
			<th><div style="width:50px;"></div></th>
			<th><div style="display:none;"></div></th>
			<th><div style="display:none;"></div></th>
			<th><div style="width:80px;"></div></th>
		</tr>
	</tfoot>
</table>
<br>