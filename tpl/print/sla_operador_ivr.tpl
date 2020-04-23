<script src="{__path_amcharts}/amcharts.js"></script>
<script src="{__path_amcharts}/pie.js"></script>
<script src="{__path_amcharts}/themes/light.js"></script>
<link rel="stylesheet" type="text/css" href="css/dev-estilos_ivr_operador.css">

<table width="100%" >
	<tr>
		<td class="celdaNaranja txtBlanco15b" colspan="2" style="height: 27px; text-align: center; border-bottom: solid 1px #fff;">Tiempos de Respuesta (segundos)</td>
	</tr>
	<tr>
		<td class="celdaSubTitulo ">Mínimo</td>
		<td class="bordes-datos-claro" width="35%">{__min_tiempo_resp}</td>
	</tr>
	<tr>
		<td class="celdaSubTituloB " >Máximo</td>
		<td class="bordes-datos-oscuro" width="35%">{__max_tiempo_resp}</td>
	</tr>
	<tr>
		<td class="celdaSubTitulo ">Promedio</td>
		<td class="bordes-datos-claro" style="font-weight: bold" width="35%">{__prom_tiempo_resp}</td>
	</tr>	
</table>

<div style="height: 30px"></div>

<table width="100%" >
	<tr>
		<td class="celdaNaranja txtBlanco15b" colspan="2" style="height: 27px; text-align: center; border-bottom: solid 1px #fff;">Disponibilidad (Cantidad de llamadas)</td>
	</tr>
	<tr>
		<td class="celdaSubTitulo">Llamadas contestadas</td>
		<td class="bordes-datos-claro efectoBoton textoBoton"  id="myBtn" width="50%">{__contestadas}</td>
	</tr>
	<tr>
		<td class="celdaSubTituloB">Llamadas no contestadas</td>
		<td class="bordes-datos-oscuro efectoBoton textoBoton" id="myBtn2" width="50%">{__no_contestadas}</td>
	</tr>
	<tr>
		<td class="celdaSubTitulo">Otros</td>
		<td class="bordes-datos-claro efectoBoton textoBoton" id="myBtn3" width="50%">{__otros}</td>
	</tr>
	<tr>
		<td class="celdaSubTituloB">Total</td>
		<td class="bordes-datos-oscuro" style="font-weight: bold;" width="50%">{__total}</td>
	</tr>
</table>

<div class="chartdiv" id="chartdiv_{__id_chart}"></div>

<div id="myModal" class="modal">
	<div class="modal-content">
		<table width="100%" class="modal-header">
			<tr>
				<td class="txtBlanco15b" style="text-align: center; margin: 12px;">
					Llamadas contestadas
				</td>
				<td>
					<a href="#" class="export" >
					<img  src="img/download.png">
					</a>
				</td>
				<td>
					<div class="close closex">&times;</div>
				</td>
			</tr>
		</table>
		<div class="modal-body">
			<div id="dvData">
				<table width="100%" >
					<tr>
						<td></td>
						<td class="celdaSubTitulo textoIvr" >Fecha Monitoreo</td>
						<td class="celdaSubTitulo textoIvr" >Estado</td>
						<td class="celdaSubTitulo textoIvr" >Tiempo de Respuesta (segundos)</td>
					</tr>
					<!-- BEGIN BLOQUE_MONITOREOS_CONTESTA -->
					<tr>
						<td class="estiloNumero">{__numero}</td>
						<td class="{__print_class} textoDato">{__fecha_monitoreo}</td>
						<td class="{__print_class} textoDato">{__estado}</td>
						<td class="{__print_class} textoDato">{__tiempo_respuesta}</td>
					</tr>
					<!-- END BLOQUE_MONITOREOS_CONTESTA -->
				</table>
			</div>
		</div>
	</div>
</div>

<div id="myModal2" class="modal">
	<div class="modal-content">
		<table width="100%" class="modal-header">
			<tr>
				<td class="txtBlanco15b" style="text-align: center; margin: 12px;">
					Llamadas no contestadas
				</td>
				<td>
					<a href="#" class="export2" >
					<img  src="img/download.png">
					</a>
				</td>
				<td>
					<div class="close2 closex">&times;</div>
				</td>
			</tr>
		</table>
		<div class="modal-body">
			<div id="dvData2">
				<table width="100%" >
					<tr>
						<td></td>
						<td class="celdaSubTitulo textoIvr" >Fecha Monitoreo</td>
						<td class="celdaSubTitulo textoIvr" >Estado</td>
						<td class="celdaSubTitulo textoIvr" >Tiempo de Respuesta</td>
					</tr>
					<!-- BEGIN BLOQUE_MONITOREOS_NO_CONTESTA -->
					<tr>
						<td class="estiloNumero">{__numero2}</td>
						<td class="{__print_class2} textoDato">{__fecha_monitoreo2}</td>
						<td class="{__print_class2} textoDato">{__estado2}</td>
						<td class="{__print_class2} textoDato">{__tiempo_respuesta2}</td>
					</tr>
					<!-- END BLOQUE_MONITOREOS_NO_CONTESTA -->
				</table>
			</div>
		</div>
	</div>
</div>

<div id="myModal3" class="modal">
	<div class="modal-content">
		<table width="100%" class="modal-header">
			<tr>
				<td class="txtBlanco15b" style="text-align: center; margin: 12px;">
					Otras llamadas
				</td>
				<td>
					<a href="#" class="export3" >
					<img  src="img/download.png">
					</a>
				</td>
				<td>
					<div class="close3 closex">&times;</div>
				</td>
			</tr>
		</table>
		<div class="modal-body">
			<div id="dvData3">
				<table width="100%" >
					<tr>
						<td></td>
						<td class="celdaSubTitulo textoIvr" >Fecha Monitoreo</td>
						<td class="celdaSubTitulo textoIvr" >Estado</td>
						<td class="celdaSubTitulo textoIvr" >Tiempo de Respuesta</td>
					</tr>
					<!-- BEGIN BLOQUE_MONITOREOS_OTRO -->
					<tr>
						<td class="estiloNumero">{__numero3}</td>
						<td class="{__print_class2} textoDato">{__fecha_monitoreo3}</td>
						<td class="{__print_class2} textoDato">{__estado3}</td>
						<td class="{__print_class2} textoDato">{__tiempo_respuesta3}</td>
					</tr>
					<!-- END BLOQUE_MONITOREOS_OTRO -->
				</table>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		var chart = AmCharts.makeChart( "chartdiv_{__id_chart}", {
				"type": "pie",
			  	"theme": "light",
			  	"legend": {
			    	"fontSize": 10,
			    	"align": "center"
		  	},
			  	"dataProvider": [ {
			    	"country": "Llamadas contestadas",
			    	"value": {__contestadas},
			     	"color": "#54a51c"
		  	}, {
			    	"country": "Llamadas no contestadas",
			    	"value": {__no_contestadas},
			     	"color": "#d22129"
		  	}, {
			    	"country": "Otras llamadas",
			    	"value": {__otros},
			    	"color": "#228BD2"
		  	}],
			  	"valueField": "value",
			  	"titleField": "country",
			  	"startEffect": "elastic",
			  	"colorField": "color",
			  	"startDuration": 2,
			  	"labelRadius": 15,
			  	"innerRadius": "40%",
			  	"depth3D": 20,
			  	"balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
			  	"angle": 50,
			  	"export": {
			   	"enabled": true
  			}
		} );

		function exportTableToCSV($table, filename) {

			var $rows = $table.find('tr:has(td)'),

			tmpColDelim = String.fromCharCode(11), 
			tmpRowDelim = String.fromCharCode(0), 

			colDelim = '","',
			rowDelim = '"\r\n"',

			csv = '"' + $rows.map(function(i, row) {
				var $row = $(row),
				$cols = $row.find('td');

				return $cols.map(function(j, col) {
					var $col = $(col),
					text = $col.text();

					return text.replace(/"/g, '""');

				}).get().join(tmpColDelim);

			}).get().join(tmpRowDelim)
			.split(tmpRowDelim).join(rowDelim)
			.split(tmpColDelim).join(colDelim) + '"';

			if (false && window.navigator.msSaveBlob) {

				var blob = new Blob([decodeURIComponent(csv)], {
					type: 'text/csv;charset=utf-8'
				});

				window.navigator.msSaveBlob(blob, filename);

			} else if (window.Blob && window.URL) {
				var blob = new Blob(["\uFEFF"+csv], {
					type: 'text/csv; charset=utf-18'
				});
				var csvUrl = URL.createObjectURL(blob);

				$(this)
				.attr({
					'download': filename,
					'href': csvUrl
				});
			} else {
				var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

				$(this)
				.attr({
					'download': filename,
					'href': csvData,
					'target': '_blank'
				});
			}
		}

		$(".export").on('click', function(event) {
			var args = [$('#dvData>table'), 'tmp/{__nombre_archivo_answer}.csv'];
			exportTableToCSV.apply(this, args);
		});

		$(".export2").on('click', function(event) {
			var args = [$('#dvData2>table'), 'tmp/{__nombre_archivo_no_answer}.csv'];
			exportTableToCSV.apply(this, args);
		});

		$(".export3").on('click', function(event) {
			var args = [$('#dvData3>table'), 'tmp/{__nombre_archivo_other}.csv'];
			exportTableToCSV.apply(this, args);
		});

	});
	var modal = document.getElementById('myModal');
	var btn = document.getElementById("myBtn");
	var span = document.getElementsByClassName("close")[0];

	var modal2 = document.getElementById('myModal2');
	var btn2 = document.getElementById("myBtn2");
	var span2 = document.getElementsByClassName("close2")[0];

	var modal3 = document.getElementById('myModal3');
	var btn3 = document.getElementById("myBtn3");
	var span3 = document.getElementsByClassName("close3")[0];

	btn.onclick = function() {
		if ({__boton} == 1) {
			modal.style.display = "block";
		}
	}
	span.onclick = function() {
		modal.style.display = "none";
	}

	span2.onclick = function() {
		modal2.style.display = "none";
	}
	btn2.onclick = function() {
		if ({__boton2} == 1) {
			modal2.style.display = "block";
		}
	}

	span3.onclick = function() {
		modal3.style.display = "none";
	}
	btn3.onclick = function() {
		if ({__boton3} == 1) {
			modal3.style.display = "block";
		}
	}

	window.onclick = function(event) {
		if (event.target == modal || event.target == modal2 || event.target == modal3) {
			modal.style.display = "none";
			modal2.style.display = "none";
			modal3.style.display = "none";
		}
	}

</script>
