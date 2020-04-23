
	<head>	
		<meta http-equiv="X-UA-Compatible" content="IE=9"/>
		<meta http-equiv="content-type" content="text/html" charset="utf-8">
	</head>
<link rel="stylesheet" href="tools/jquery/css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
<script type="text/javascript" src="tools/jquery/js/jquery-ui-1.10.min.js"></script>
<table class="formulario" width="60%">
	<tr>
		<th>Juego de Caracteres</th>
		<td>
			<select name="datos_codificacion_{__item_id}" id="datos_codificacion_{__item_id}">
				<option value="0" selected>Windows (LATIN1)</option>
				<option value="1">Otros (UTF-8)</option>
			</select>
		</td>
	</tr>
	<tr>
		<th>Separador</th>
		<td>
			<input type="radio" name="datos_separador_{__item_id}" value="0" checked> Punto y coma &nbsp;&nbsp;&nbsp;
			<input type="radio" name="datos_separador_{__item_id}" value="1"> Coma
		</td>
	</tr>
	<tr>
		<th colspan="100%" align="center" style="border-top: 5px solid #ffffff;">
			Ejemplo CSV<br>
			<img src="{__datos_imagen}" style="background-color: #ffffff" width="456"/>
		</th>
	</tr>
</table>
<br>
	<div style="width: 100%">
		<div id ="datos" align="left" style="width: 15%;float: left;display: inline;">
			<input type="button" class="boton_accion" onclick="getDatosCsv('{__inicio}','{__termino}','{__objetivo}','{__usuario}', 'normal'); return false;" value="Descargar CSV"/>
		</div>
		<div id ="datosSpinner" style="float: left;display: none;text-align: center;width: 15%" class="lds-ellipsis" valign="center"><div></div><div></div><div></div><div></div></div>
		<div id="datosT" align="right" style="width: 19%;float: left;display: inline;">
			<input type="button" class="boton_accion" onclick="getDatosCsv('{__inicio}','{__termino}','{__objetivo}','{__usuario}', 'tabulado'); return false;" value="Descargar CSV Tabulado"/>
		</div>
		<div id="datosTSpinner" style="float: left;display: none;text-align: center;width: 19%" class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
	</div>
	
<script>
	datos=document.getElementById("datos")
	datosSpinner=document.getElementById("datosSpinner")
	datosT=document.getElementById("datosT")
	datosTSpinner=document.getElementById("datosTSpinner")
	function getDatosCsv(inicio, termino,objetive, user, metodo){
		
		if(metodo=="normal"){
			datos.style.display="none"
			datosSpinner.style.display="inline"
		}else{
			datosT.style.display="none"
			datosTSpinner.style.display="inline"
		}
		setTimeout(function(){
			$.ajax({
		        async: false,
		        type: 'POST',
		        url: 'utils/get_datos.php',
		        data: {'inicio':inicio,'termino':termino,'objetive':objetive,'user':user},
		        	success: function(data) {
		        		//console.log(data)
		        		json=(JSON.parse(data))
		        		list = new Array()
		        		if(Object.keys(json).length!=1){
			        		steps=(json.data_monitores[0].data_monitor[0].data)
			        		codes=''
			        		//SE RECORRE POR MONITOR
			        		$.each(json.data_monitores, function(i, ele){
			        				//Se RECORREN FECHAS
				        		$.each(ele.data_monitor, function(index, data){
				        			date=data["fecha"].split("T")
				        			fecha=date[0]
				        			hora=date[1]
				        			dct={"servidor":ele.monitor, "fecha":fecha, "hora":hora}
				        			if(metodo=='normal'){
					        			$.each(steps, function(j, pasos){
					        				patrones=((data["data"][j]["estado"][0]).split(","))
					        				$.each(patrones, function(indexp, patron) {
					        					dct[data["data"][j].nombre_paso+" - estado - "+(indexp+1)]=patron
					        				})
					        			})
					        			$.each(steps, function(j, pasos){
					        				
					        				dct[data["data"][j].nombre_paso+" -respuesta [ms]"]=data["data"][j].respuesta
					       				})
					       			}else{
					       				$.each(steps, function(j, pasos){
					       					patrones=((data["data"][j]["estado"][0]).split(","))
					       					$.each(patrones, function(indexp, patron) {
					       						dct[data["data"][j].nombre_paso+" - estado - "+(indexp+1)]=patron
					       					})
					       					dct[data["data"][j].nombre_paso+" -respuesta [ms]"]=data["data"][j].respuesta
					       				})
				        			}
			        				list.push(dct)
		        				})
			        		})
			        	}else{
			        		codes=(json["codigos"])
			        	}
		        		stockData=list
		        		downloadCSV(stockData, codes)
		        		if(metodo=="normal"){
							datos.style.display="inline"
							datosSpinner.style.display="none"
						}else{
							datosT.style.display="inline"
							datosTSpinner.style.display="none"
						}
		            },
		        })
			}, 1)	
	}

	function convertArrayOfObjectsToCSV(stockData, codes) {
        var result, ctr, keys, columnDelimiter, lineDelimiter, data;

        data = stockData.data || null;
        if (data == null || !data.length) {
            result ="Datos Objetivo: {__nombre_objetivo}\n\n"+"Leyenda \n"+"   Servidor : servidor que realizo el monitoreo. \n"+
	        "   Fecha : fecha cuando se realizo el monitoreo. \n"+"   Hora : hora cuando se realizo el monitoreo. \n"+
	        "   Status : codigo de estado del monitoreo. \n"+"   Delay : tiempo de respuesta del monitoreo. \n"+"\nCodigos de Estados\n"
	        $.each(codes, function(i, code){
	        	result+="  "+(code["codigo_id"])+" : "+(code["nombre"])+"\n"
	        })
	        result+="\n\n"+"servidor;fecha;hora;"
	        return result
        }


        columnDelimiter = stockData.columnDelimiter || ';';
        lineDelimiter = stockData.lineDelimiter || '\n';
        keys = Object.keys(data[0]);
        result ="Datos Objetivo: {__nombre_objetivo}\n\n"+"Leyenda \n"+"   Servidor : servidor que realizo el monitoreo. \n"+
        "   Fecha : fecha cuando se realizo el monitoreo. \n"+"   Hora : hora cuando se realizo el monitoreo. \n"+
        "   Status : codigo de estado del monitoreo. \n"+"   Delay : tiempo de respuesta del monitoreo. \n"+"\nCodigos de Estados\n"
        $.each(codes, function(i, code){
        	result+="  "+code["codigo_id"]+" : "+(code["nombre"])+"\n"
        })
        result+="\n\n\n\n"
        result += keys.join(columnDelimiter);
        result += lineDelimiter;

        data.forEach(function(item) {
            ctr = 0;
            keys.forEach(function(key) {
                if (ctr > 0) result += columnDelimiter;

                result += item[key];
                ctr++;
            });
            result += lineDelimiter;
        });

        return result;
    }
    function downloadCSV(stockData, codes) {
        var data, filename, link;

        var csv = convertArrayOfObjectsToCSV({
            data: stockData
        }, codes);
        if (csv == null) return;

        filename = stockData.filename || 'datos - {__inicio}.csv';
        
        var type='data:application/csv;charset=utf-8,'
        var blob = new Blob(["\uFEFF"+csv], {
			type: 'text/csv; charset=utf-18'
		});
		data = URL.createObjectURL(blob);

        link = document.createElement('a');
        link.setAttribute('href', data);
        link.setAttribute('download', filename);
        link.click();
    }
    

$(function() {
	name = '{__name}';
	// Ejecuta la inialización del acordeon.
	if ('{__tiene_evento}' == 'true'){		
		createAccordion(name);	
	}
});
</script>
<style>
.lds-ellipsis {
  display: inline-block;
  position: relative;
  width: 64px;
  height: 22px;
}
.lds-ellipsis div {
  position: absolute;
  top: 5.5px;
  width: 11px;
  height: 11px;
  border-radius: 50%;
  background: #f36f00;
  animation-timing-function: cubic-bezier(0, 1, 1, 0);
}
.lds-ellipsis div:nth-child(1) {
  left: 6px;
  animation: lds-ellipsis1 0.6s infinite;
}
.lds-ellipsis div:nth-child(2) {
  left: 6px;
  animation: lds-ellipsis2 0.6s infinite;
}
.lds-ellipsis div:nth-child(3) {
  left: 26px;
  animation: lds-ellipsis2 0.6s infinite;
}
.lds-ellipsis div:nth-child(4) {
  left: 45px;
  animation: lds-ellipsis3 0.6s infinite;
}
@keyframes lds-ellipsis1 {
  0% {
    transform: scale(0);
  }
  100% {
    transform: scale(1);
  }
}
@keyframes lds-ellipsis3 {
  0% {
    transform: scale(1);
  }
  100% {
    transform: scale(0);
  }
}
@keyframes lds-ellipsis2 {
  0% {
    transform: translate(0, 0);
  }
  100% {
    transform: translate(19px, 0);
  }
}


</style>