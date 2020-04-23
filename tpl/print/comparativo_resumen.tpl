
<link rel="stylesheet" href="{__path_jquery_ui}css/jquery-data-table/jquery.dataTables.css"></link>
<link rel="stylesheet" href="{__path_jquery_ui}css/jquery-ui-css/jquery-ui-1.10.min.css"></link>
<script type="text/javascript" src="{__path_jquery_ui}js/jquery-ui-1.10.min.js"></script>
<script type="text/javascript" src="{__path_jquery_ui}js/jquery-data-table/jquery.dataTables.print.js"></script>

<div style="page-break-inside: avoid;">
  <table>
  	<tr>
  		<td>
  		</td>
  		<td>
  		<table id="example2" cellspacing="0" class="dataTable listadoMantenedor" class="celdanegra40" width="50%"  border="0" >
  		  <thead>
  		    <tr>
    		    <th colspan="2">Monitoreo</th>
    		    <th colspan="3">Tiempo de Respuesta</th>
    		    <th colspan="4">Disponibilidad</th>
  		    </tr>
  		    <tr>
  		      <th >Monitor</th>
  		  		<th >Paso</th>
  		  		<th >Minimo(seg)</th>
  		  		<th >Maximo(seg)</th>
  		  		<th >Promedio (seg)</th>
  		  		<th >Up.(%)</th>
  		  		<th >Down.(%)</th>
  		  		<th >Sin Mon.(%)</th>
  		  		<th >Total Mon.</th>
  		  	</tr>
  		  </thead>
  		</table>
  		</td>
  	</tr>
  </table>
</div>	
<br>

<script>
 $("#example2").dataTable({

  "aaData":[
  <!-- BEGIN BLOQUE_TABLA -->
    <!-- BEGIN BLOQUE_TITULO_HORARIOS -->
    <!-- END BLOQUE_TITULO_HORARIOS -->
    <!-- BEGIN LISTA_PASOS -->
  			["{__monitor_nombre}", "{__paso_nombre}", "{__paso_minimo}", "{__paso_maximo}", "{__paso_promedio}","{__paso_uptime}", "{__paso_downtime}", "{__paso_no_monitoreo}", "{__monitor_total_monitoreo}"],
      <!-- BEGIN ES_PRIMERO_MONITOR -->
      <!-- END ES_PRIMERO_MONITOR -->
      <!-- BEGIN ES_PRIMERO_TOTAL -->
      <!-- END ES_PRIMERO_TOTAL -->
    <!-- END LISTA_PASOS -->
<!-- END BLOQUE_TABLA -->
  ],
  "aoColumnDefs":[{
        "sTitle":"Site name"
      , "aTargets": [ "site_name" ]
  },{
        "aTargets": [ 1 ]
      , "bSortable": false
      , "mRender": function ( url, type, full ) {
          return  '<a href="'+url+'">' + url + '</a>';
      }
  }]
});
</script>
<script>
 $(function() {
   name = '{__name}';
   // Ejecuta la inialización del acordeon.
   if ('{__tiene_evento}' == 'true'){
     $('#man').show();
     createAccordion(name);
   }
 });
 </script>