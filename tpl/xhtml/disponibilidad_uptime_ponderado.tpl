<script type="text/javascript">
		
	var chart_disponibilidad_uptime;
        
      
	$(document).ready(function() {
            var es_descarga_pdf= true;
            var color_plot_bands = 'rgba(84,165,28,0.2)';
            if('{es_descarga}'=='true'){
               es_descarga_pdf = false;
               color_plot_bands = '#CFF8B1';
            }
            chart_disponibilidad_uptime = new Highcharts.Chart({
			exporting: { enabled: false},
			credits : {
			    enabled : false
			},
			height:300,
			chart: {
				renderTo: 'disponibilidad_uptime{__graficos}', 
				defaultSeriesType: 'line',
				zoomType: 'x',
				height:300,
                                ignoreHiddenSeries : false,
				alignTicks: false,
				marginRight:30,
				marginTop:20,
	            resetZoomButton: {
	                theme: {
	                    fill: 'white',
	                    stroke: 'silver',
	                    r: 0,
	                    states: {
	                        hover: {
	                            fill: '#41739D',
	                            style: {
	                                color: 'white'
	                            },
	                        }
	                    }
	                }
	            }
			},
			title: {
				enabled:false,
				style: { color: '#5A5A5A', fontSize: '14px', fontWeight: 'bold' },
				text: ''
			},
			xAxis: {
				lineColor: '#c0c0c0',
				type: 'datetime',
				
				minorGridLineColor: '#E0E0E0',
			    minorTickLength: 0,
			    gridLineWidth: 0.3,
			    showFirstLabel:true,
		        dateTimeLabelFormats: {
		            day: "<br/><b>%e/%b</b>",
		            month: '%b %y',
		            year: '%Y'
		        },
		        labels: {
		        	useHTML: true,				            
		        },		
				title: {
					style: { color: '#5A5A5A' },
					text: 'Período',
					useHTML:true,
				}
			},
			yAxis: {
				title: {
					style: { color: '#5A5A5A' }
				},
				labels: {
					formatter: function() {
						if(this.value % 1 != 0)
							return this.value.toFixed(2);
						else
							return this.value;
						},
				},
						
				gridLineWidth: 0.3,
				min:{__y_scale_min},
				max:{__y_scale_max},
				lineWidth:1,
		
				        
				lineColor: '#c0c0c0'
			},
			tooltip: {
				style: { fontSize: '11px' },
				backgroundColor:'rgba(255,255,255,1)',
				formatter: function() {
					return '<b>'+this.series.name+'</b><br/>'+
					       '<b>Fecha :</b> '+Highcharts.dateFormat('%d/%b/%Y', this.x)+'<br/>'+
					       '<b>Uptime :</b> '+Highcharts.numberFormat(this.y,2,',')+'%';
				},
			},

			legend: {
				enabled:false,
			},
	        plotOptions: {
	            series: {
                        enableMouseTracking: false,
    					shadow: false,
    					animation: false,
	            }
	        },
	        series: [
		        <!-- BEGIN SERIES_ELEMENT -->
				{
					name: '{__series_name}',
					color: '#{__series_color}',
					connectNulls:false,
			        marker: {
	        	        enabled: false
		            },							           																		
					data:[
						<!-- BEGIN POINT_ELEMENT -->							
						[Date.UTC({__point_name}),{__point_value}],
						<!-- END POINT_ELEMENT -->
				    ]	
				},
				<!-- END SERIES_ELEMENT -->                    
			]
		});

		//ADAPTAR LEYENDA 
		leyenda(chart_disponibilidad_uptime, ['leyenda1rencon{__tabla}','leyenda2rencon{__tabla}'], true, true, true, false, 'linea', [chart_disponibilidad_uptime], 'Pasos');
	});
</script>

<div style="page-break-inside: avoid;">
	<div id="disponibilidad_uptime{__graficos}"></div>
	<table align="center">
		<tr>
			<td style="vertical-align:top"><div style="width:450px" id="leyenda1rencon{__tabla}"></div></td>

		</tr>
	</table>
</div>
</br></br></br></br>

<table width="110%" border="1" cellpadding="0" cellspacing="0">
<tr>
		<td colspan="100%" style="border: solid 1px #ffffff;" class="celdanegra50">{__nombre_subsegmento}</td>
</tr>
<tr>
		<td height="4" colspan="100%" style="background-color: #626262; border: solid 1px #626262;"></td>
	</tr>
 <tr>
 	<td class="txtBlanco13b celdaTituloGris">Paso</td>
 	<td class="txtBlanco13b celdaTituloGris">Tipo</td>
 	
 	<!-- BEGIN BLOQUE_FECHA_EFICIENCIA -->
 	<td class="txtBlanco12b celdaTituloNaranjo" align="center" width="30">{_dia_eficiencia}</td>
 	<!-- END BLOQUE_FECHA_EFICIENCIA -->
 	
 	<!-- <td class="txtBlanco12b celdaTituloNaranjo" align="center">Promedio</td> -->
 	
</tr>
<!-- BEGIN BLOQUE_PASOS -->
<tr>

 <td class="txtGris12 {__print_class}" rowspan="2" align="left">{__pasos}</td>
 <td class="txtGris12 {__print_class}" align="left">Diario</td>
   <!-- BEGIN BLOQUE_EFICIENCIA -->
 <td class="txtGris12 {__print_class}" align="right"style="background-color: #{__evento_color};">{__eficiencia}</td>
  <!-- END BLOQUE_EFICIENCIA -->
<!--  <td class="txtBlanco13b celdaTituloAzul" align="right">{__promedio_eficiencia}</td> -->
 </tr>
<tr>
<td class="txtGris12 {__print_class}" align="left">Acumulado</td>
   <!-- BEGIN BLOQUE_ACUMULADO -->
 <td class="txtGris12 {__print_class}" align="right">{__acumulado}</td>
  <!-- END BLOQUE_ACUMULADO -->
<!--   <td class="txtBlanco13b celdaTituloAzul" align="right">{__promedio_acumulado}</td> -->
 </tr>
<!-- END BLOQUE_PASOS -->

<!-- 
<tr>
	<td class="txtBlanco13b celdaTituloAzul" align="left" rowspan="2" colspan="" >Total</td>
	<td class="txtBlanco13b celdaTituloAzul" align="left"rowspan="2">({__ponderacion}%)</td>
	<td class="txtBlanco13b celdaTituloAzul" align="left">Diario</td>
	  <!-- BEGIN BLOQUE_TOTAL_DIARIO
 	<td class="txtBlanco13b celdaTituloAzul" align="right">{__diario_total}</td>
 	  <!-- END BLOQUE_TOTAL_DIARIO ->
 	  <td class="txtBlanco13b celdaTituloAzul" align="right">{__promedio_diario_total}</td>
</tr>
<tr>
	<td class="txtBlanco13b celdaTituloAzul" align="left">Acumulado</td>
	  <!-- BEGIN BLOQUE_ACUMULADO_TOTAL ->
 	<td class="txtBlanco13b celdaTituloAzul" align="right">{__acumulado_total}</td>
 	  <!-- END BLOQUE_ACUMULADO_TOTAL ->
 	<td class="txtBlanco13b celdaTituloAzul" align="right">{__promedio_acumulado_total}</td>
</tr>
 -->
</table>
</br></br>


