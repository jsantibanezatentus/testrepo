$(function() {
	Highcharts.setOptions({
		lang: {
			resetZoom: "Reiniciar Zoom",
			resetZoomTitle: "Reiniciar Zoom",
			decimalPoint: ",",
		}
	});
});

function leyenda(grafico, contenedores, dibuja_series, sla_ok, sla_error, desviacion, tipo, graficos, titulo, mantenimiento ) {
	var $legend0 = $('<div>').attr("id", "div_" + contenedores[0] + '_1').css({
		'width': $('#' + contenedores[0]).width(),
		'padding-top': 5,
		'padding-bottom': 5,
		'padding-left': 5,
		'padding-right': 5,
		'display': 'inline-block',
		'overflow': 'hidden',
		'border-top': 'solid #000000 0px',
		'border-left': 'solid #000000 0px',
		'border-right': 'solid #000000 0px',
		'border-bottom': 'solid #000000 0px',
		//'background-color':'#f2f2f2',
	}).appendTo($('#' + contenedores[0]));

	//VALIDA SI DEBE O NO DIBUJAR LAS SERIES
	if (dibuja_series == true) {
		
		//DIBUJA EL TITULO SI EL CAMPO NO VIENE VACIO
		if (titulo != '') {
			
			var $legendTitulo = $('<div>').attr('class', 'highcharts-legend-item').attr('id', 'id_').css({
				'position': 'relative',
				'marginLeft': 0,
				'float': 'left',
				'padding': '3 3 3 0',
				'width': 450,
				'height': 25,
			}).appendTo($legend0);

			var $texto = $('<div>').css({
				'position': 'relative',
				'padding-left': 2,
				'padding-right': 20,
				'padding-top': 2,
				'padding-bottom': 2,
				'width': 150,
				'overflow': 'hidden',
				'font-family': 'Lucida Grande , Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif',
				'font-size': '12px',
				'color': '#000000',
				'text-align': 'left',
				'font-weight': 'bold',
				'text-overflow': 'ellipsis',
				'white-space': 'nowrap',
				'display':'inline'
			}).html(titulo).appendTo($legendTitulo);
          contenedor_boton = $legendTitulo;
          display="inline";
		}
		else{
			contenedor_boton = $legend0;
			display="block";
		}
		
		if(tipo == 'linea'){	
			var $contendor_check = $('<div>').css({
			//	'width': $('#' + contenedores[0]).width()-125,
				'display':display,
				'padding-bottom':'5',
			    'padding-top':'10'
			}).appendTo(contenedor_boton);
			
			var $check = $('<input>').attr({
		            'id': 'ocultar',
		            'value': 'Ocultar',
		            'type':  'button',
		            'data-estado':"visible"
		         }).css({"width":"50px",
		        	     "color":"black"
		                  }).appendTo($contendor_check);
			
			$check.mouseover(function(){
				$(this).css({
						"background-color":"#41739D",
						"color":"white"
				});
			});

			$check.mouseout(function(){
				$(this).css({
						"background-color":"white",
						"color":"black"
				});
			});
			
			$check.click(function() {
				if($(this).data("estado")=="visible"){
					ocultaSeries($legend0, graficos, "visible");
					$(this).data("estado","invisible");
					$(this).val("Mostrar");
				}
				else{
					ocultaSeries($legend0, graficos, "invisible");
					$(this).val("Ocultar");
					$(this).data("estado","visible");
				}
			});

		}
		//DIBUJA LAS SERIES DEL GRÁFICO	           
		$.each(grafico.series, function(i, series) {
			
			//CALCULA EL TAMAÑO DEL CONTENEDOR
			nseries = grafico.series.length;
//			ancho = (contenedores[0] != contenedores[1])?(450 / nseries):(620 / nseries);
			ancho = ($('#' + contenedores[0]).width() / ((nseries > 3)?3:nseries));
			ancho = (ancho < 120)?120:ancho - 30;
			
			// crea los items de las series            
			var $legendItem = $('<div>').attr('class', 'highcharts-legend-item').attr('id',	'id_' + i).css({
				'position': 'relative',
				'marginLeft': 20,
				'cursor': (tipo != 'linea')?'arrow':'pointer',
				'float': 'left',
				'padding': 3,
				'width': parseInt(ancho),
			}).appendTo($legend0);

			var $texto = $('<div>').css({
				'position': 'relative',
				'padding': 2,
				'width': parseInt(ancho) - 5,
				'overflow': 'hidden',
				'font-family': "Lucida Grande , Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif",
				'font-size': '12px',
				'color': '#000000',
				'text-align': 'left',
				'font-weight': 'normal',
				'text-overflow': 'ellipsis',
				'white-space': 'nowrap',				
				}).html(series.name).attr("class","legend-item-text").appendTo($legendItem);

			//SOLAMENTE LOS GRÁFICOS TIPO LÍNEAS TIENEN ASOCIADA LA ACCIÓN QUE OCULTA LAS SERIES
			if (tipo != 'linea') {
				var $line = $('<div>').css({
					'width': 16,
					'position': 'absolute',
					'left': -20,
					'top': 10,
					'borderTop': '6px solid ' + (series.color)
				}).appendTo($legendItem);
			}
			else {
				var $line = $('<div>').css({
					'width': 16,
					'position': 'absolute',
					'left': -20,
					'top': 12,
					'borderTop': '2px solid ' + (series.color)
				}).appendTo($legendItem);
			}
			//ASOCIA LA FUNCIÓN QUE OCULTA LAS SERIES
			if (tipo == 'linea') {

				$legendItem.click(function() {
					clickItem(i, $legendItem, graficos);
				});

				$legendItem.mouseover(function() {
					resalta(series, $legendItem, $line);
				});
				$legendItem.mouseout(function() {
					opaca(series, $legendItem, $line);
				});
			}
		});
	}

	//CALCULA EL TAMAÑO DEL CONTENEDOR INTERNO	
	if (sla_ok == true || sla_error == true || desviacion == true || mantenimiento == true) {
		ancho2 = $('#' + contenedores[1]).width();

		//DIBUJA UN TITULO DENTRO DEL CONTENEDOR
		if (titulo != '') {
			var $legendTitulo = $('<div>').attr('class', 'highcharts-legend-item').attr('id', 'id_a').css({
				'position': 'relative',
				'marginLeft': 20,
				'float': 'left',
				'padding': 3,
				'width': ancho2,
				'height': 25
			}).appendTo($('#' + contenedores[1]))
		}

		//DECIDE SI DIBUJARÁ 1 O 2 CONTENEDORES
		if (contenedores[0] != contenedores[1]) {
			var $legend1 = $('<div>').attr("id", "div_" + contenedores[1] + '_2').css({
				'width': ancho2,
				'padding-top': 5,
				'padding-bottom': 5,
				'padding-left': 10,
				'padding-right': 10,
				'overflow': 'hidden',
				'display': 'inline-block',
				'background-color':'#f2f2f2',
				'border-top': 'solid #000000 0px',
				'border-left': 'solid #000000 0px',
				'border-right': 'solid #000000 0px',
				'border-bottom': 'solid #000000 0px',
				'background-color': '#f4f4f4',
			}).appendTo($('#' + contenedores[1]));
		}

		//DIBUJA SLA OK
		if (sla_ok == true) {
			var $legendItem = $('<div>').css({
				'position': 'relative',
				'marginLeft': 20,
				'cursor': (tipo != 'linea')?'arrow':'pointer',
				'float': 'left',
				'padding': 3,
				'width': 135,
			});

			//DECIDE EN QUE CONTENEDOR IRÁ LA LEYENDA COMPLEMENTARIA
			if (contenedores[0] == contenedores[1]) {
				$legendItem.appendTo($legend0);
			}
			else {
				$legendItem.appendTo($legend1);
			}

			var $texto = $('<div>').css({
				'position': 'relative',
				'padding': 2,
				'width': parseInt(ancho2) - 5,
				'overflow': 'hidden',
				'font-family': "Lucida Grande , Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif",
				'font-size': '12px',
				'color': '#000000',
				'text-align': 'left',
				'font-weight': 'normal',
				'text-overflow': 'ellipsis',
				'white-space': 'nowrap',
			}).html('SLA ok').appendTo($legendItem);

			var $line = $('<div>').css({
				'width': 16,
				'position': 'absolute',
				'left': -20,
				'top': 12,
				'borderTop': '2px solid #54a51c'
			}).appendTo($legendItem);

		}
		
		//SLA ERROR
		if (sla_error == true) {
			var $legendItem = $('<div>').css({
				'position': 'relative',
				'marginLeft': 20,
				'cursor': (tipo != 'linea')?'arrow':'pointer',
				'float': 'left',
				'padding': 3,
				'width': 135,
			});
			
			//DECIDE EN QUE CONTENEDOR IRÁ LA LEYENDA COMPLEMENTARIA   
			if (contenedores[0] == contenedores[1]) {
				$legendItem.appendTo($legend0);
			}
			else {
				$legendItem.appendTo($legend1);
			}

			var $texto = $('<div>').css({
				'position': 'relative',
				'padding': 2,
				'width': parseInt(ancho2) - 5,
				'overflow': 'hidden',
				'font-family': "Lucida Grande , Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif",
				'font-size': '12px',
				'color': '#000000',
				'text-align': 'left',
				'font-weight': 'normal',
				'text-overflow': 'ellipsis',
				'white-space': 'nowrap',
			}).html('SLA error').appendTo($legendItem);

			var $line = $('<div>').css({
				'width': 16,
				'position': 'absolute',
				'left': -20,
				'top': 12,
				'borderTop': '2px solid #d22129'
			}).appendTo($legendItem);
		}

		if (mantenimiento == true) {
			var $legendItem = $('<div>').css({
				'position': 'relative',
				'marginLeft': 20,
				'cursor': (tipo != 'linea')?'arrow':'pointer',
				'float': 'left',
				'padding': 3,
				'width': 135,
			});
			
			//DECIDE EN QUE CONTENEDOR IRÁ LA LEYENDA COMPLEMENTARIA   
			if (contenedores[0] == contenedores[1]) {
				$legendItem.appendTo($legend0);
			}
			else {
				$legendItem.appendTo($legend1);
			}

			var $texto = $('<div>').css({
				'position': 'relative',
				'padding': 2,
				'width': parseInt(ancho2) - 5,
				'overflow': 'hidden',
				'font-family': "Lucida Grande , Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif",
				'font-size': '12px',
				'color': '#000000',
				'text-align': 'left',
				'font-weight': 'normal',
				'text-overflow': 'ellipsis',
				'white-space': 'nowrap',
			}).html('Mantenimiento').appendTo($legendItem);

			var $line = $('<div>').css({
				'width': 16,
				'position': 'absolute',
				'left': -20,
				'top': 12,
				'borderTop': '2px solid #a779e0'
			}).appendTo($legendItem);
		}
	        
		//DESVIACION STANDARD
		if (desviacion == true) {
			var $legendItem = $('<div>').css({
				'position': 'relative',
				'marginLeft': 20,
				'cursor': (tipo != 'linea')?'arrow':'pointer',
				'float': 'left',
				'padding': 3,
				'width': 135
			});

			//DECIDE EN QUE CONTENEDOR IRÁ LA LEYENDA COMPLEMENTARIA
			if (contenedores[0] == contenedores[1]) {
				$legendItem.appendTo($legend0);
			}
			else {
				$legendItem.appendTo($legend1);
			}

			var $texto = $('<div>').css({
				'position': 'relative',
				'padding': 2,
				'width': parseInt(ancho2) - 5,
				'overflow': 'hidden',
				'font-family': "Lucida Grande , Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif",
				'font-size': '12px',
				'color': '#000000',
				'text-align': 'left',
				'font-weight': 'normal',
				'text-overflow': 'ellipsis',
				'white-space': 'nowrap',
			}).html('Desviación Estándar').appendTo($legendItem);


			var $line = $('<div>').css({
				'width': 16,
				'position': 'absolute',
				'left': -20,
				'top': 10,
				'borderTop': '6px solid #ccdceb'
			}).appendTo($legendItem);

			//PROMEDIO
			var $legendItem2 = $('<div>').css({
				'position': 'relative',
				'marginLeft': 20,
				'cursor': (tipo != 'linea')?'arrow':'pointer',
				'float': 'left',
				'padding': 3,
				'width': 135,
			})

			if (contenedores[0] == contenedores[1]) {
				$legendItem.appendTo($legend0);
				$legendItem2.appendTo($legend0);
			}
			else {
				$legendItem.appendTo($legend1);
				$legendItem2.appendTo($legend1);
			}

			var $texto2 = $('<div>').css({
				'position': 'relative',
				'padding': 2,
				'width': parseInt(ancho2) - 5,
				'overflow': 'hidden',
				'font-family': "Lucida Grande , Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif",
				'font-size': '12px',
				'color': '#000000',
				'text-align': 'left',
				'font-weight': 'normal',
				'text-overflow': 'ellipsis',
				'white-space': 'nowrap',
			}).html('Promedio').appendTo($legendItem2);

			var $line2 = $('<div>').css({
				'width': 16,
				'position': 'absolute',
				'left': -20,
				'top': 12,
				'borderTop': '2px solid #00529e'
			}).appendTo($legendItem2);
		}

	}
	

	
	

}

//DIBUJA EN LAS SERIES DEL EJE X NÚMEROS ADEMÁS DE CREAR UNA LEYENDA CON NÚMEROS Y NOMRES DE LAS SERIES (SE USA EN LOS GRÁFICOS DE BARRA CON MÁS DE 7 SERIES)
function leyendaLabel(grafico, contenedor) {
	
	//BUSCA LAS CATEGORÍAS QUE PUEDEN ESTAR EN 2 FORMATOS
	if (grafico.options.xAxis[0]) {
		if (grafico.options.xAxis[0].categories) {
			categorias = grafico.options.xAxis[0].categories;
		}
		else {
			return false;
		}
	}
	else {
		if (grafico.options.xAxis.categories) {
			categorias = grafico.options.xAxis.categories;
		}
		else {
			return false;
		}
	}
	
	//DIBUJA LOS NOMBRES DE LAS SERIES
	$.each(categorias, function(i, categoria) {
		nseries = categorias.length;
		ancho = ($('#' + contenedor).width() / ((nseries > 4)?4:nseries));
//		ancho = (450 / nseries);
		ancho = (ancho < 120)?120:ancho - 30;
	
		// crea los items de las series            
		var $legendItem = $('<div>').attr('class', 'highcharts-legend-item').attr('id', 'id_' + i).css({
			'position': 'relative',
			'marginLeft': 20,
			'cursor': 'arrow',
			'float': 'left',
			'padding': 3,
			'overflow': 'hidden',
			'text-overflow': 'ellipsis',
			'white-space': 'nowrap',
			'width': parseInt(ancho),
		}).appendTo($('#' + contenedor));

		var $indice = $('<div>').css({
			'display': 'inline',
			'position': 'relative',
			'padding': 2,
			'width': 20,
			'overflow': 'hidden',
			'font-family': "Lucida Grande , Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif",
			'font-size': '12px',
			'color': '#000000',
			'text-align': 'left',
			'font-weight': 'bold',
			'text-overflow': 'ellipsis',
			'white-space': 'nowrap',
		}).html(parseInt(i + 1)).appendTo($legendItem);

		var $texto = $('<div>').css({
			'display': 'inline',
			'position': 'relative',
			'padding': 2,
			'width': parseInt(ancho) - 5,
			'overflow': 'hidden',
			'font-family': "Lucida Grande , Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif",
			'font-size': '12px',
			'color': '#000000',
			'text-align': 'left',
			'font-weight': 'normal',
			'text-overflow': 'ellipsis',
			'white-space': 'nowrap',			
		}).html(categoria).attr("class","legend-item-text").appendTo($legendItem);

	});

}	
		
//FUNCIÓN QUE RESALTA LAS SERIES EN LOS GRÁFICOS DE LÍNEA CUANDO ESTA EN EL FOCO DEL MOUSE EL VALOR ANEXADO A LA LEYENDA
function resalta(series, $legendItem, $line) {
    try {series.graph.attr('stroke-width', 4);}
    catch(err) {}
}

//FUNCIÓN QUE VUELVE A SU ESTADO NORMAL A LAS SERIES DE LOS GRÁFICOS DE LÍNEA CUANDO SALE DEL FOCO DEL MOUSE SU ANEXO EN LA LEYENDA
function opaca(series, $legendItem, $line) {
    try {series.graph.attr('stroke-width', 2);}
    catch(err) {}
        
}

//FUNCIÓN QUE OCULTA O MUESTRA LAS SERIES EN LOS GRÁFICOS DE LÍNEA AL SER CLICKEADA EN LA LEYENDA		
function clickItem(i, $legendItem, graficos) {
	
	//RECORRE LOS GRÁFICOS PARA ENLAZAR SUS LÍNEAS SI LAS SERIES CONCUERDAN
	for (j = 0; j < graficos.length; j++) {
		chart = graficos[j];
		chart.series[i].setVisible();
		
		//CAMBIA EL VALOR DE LA LEYENDA ASOCIADA
		if (chart.series[i].visible == true) {
			$legendItem.children().css('color', '#000000');
		}
		else {
			$legendItem.children().css('color', '#a2a2a2');
		}
	}
}

function ocultaSeries($legendItem, graficos, estado){

	//RECORRE LOS GRÁFICOS PARA ENLAZAR SUS LÍNEAS SI LAS SERIES CONCUERDAN
    $legend= $legendItem.children();
	for (j = 0; j < graficos.length; j++) {
		chart = graficos[j];
		//Oculta o Muestra las líneas en el gráfico
		for(i=0; i< chart.series.length; i++){
			if(estado=="visible"){
				chart.series[i].setVisible(false);	
			}
			else{
				chart.series[i].setVisible(true);					
			}
		}
		//Cambia de color los Textos
		for(i=0; i< $legend.children().length; i++){
			if(estado=="visible"){	
				$($legend[i]).children(".legend-item-text").css('color', '#a2a2a2');
			}
			else{	
				$($legend[i]).children(".legend-item-text").css('color', '#000000');				
			}			
		}
	}
}