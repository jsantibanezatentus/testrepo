<script type="text/javascript" src="tools/jquery/js/jquery-data-table/jquery.dataTables.print.js"></script>
<link rel="stylesheet" href="tools/jquery/css/jquery-data-table/jquery.dataTables.css"></link>
<table id="example"  cellspacing="0" width="100%" class="dataTable listadoMantenedor">
  <thead>
    <tr>
      <th>Paso</th>
      <th>Evento</th>
      <th>Total Monitoreo</th>
      <th>Porcentaje</th>
    </tr>
  </thead>
</table>

<script>
$("#example").dataTable({

  "aaData":[
  <!-- BEGIN LISTA_PASOS -->
                    <!-- BEGIN PASO_DESCRIPCION -->
                    
                    <!-- END PASO_DESCRIPCION -->
                    <!-- BEGIN LISTA_EVENTOS -->
                    ["{__paso_nombre}", "{__evento}", "{__total_monitoreo}", "{__porcentaje}"],
                    <!-- END LISTA_EVENTOS -->
                    <!-- END LISTA_PASOS -->    
  ],

  "aoColumnDefs":[{
        "sTitle":"Site name"
      , "aTargets": [ "site_name" ]
  },{
        "aTargets": [ 1 ]
      , "bSortable": false
      , "mRender": function ( url, type, full )  {
          return  '<a href="'+url+'">' + url + '</a>';
      }
  },{
        "aTargets":[ 3 ]
      , "sType": "date"
      , "mRender": function(url, type, full) {
          return (full[2] == "Table") 
                    ? new Date(date).toDateString()
                    : "N/A" ;
      }  
  }]
});

</script>
