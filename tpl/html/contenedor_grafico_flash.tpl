{__chart_escala}
<div dojoType="dojox.layout.ContentPane" id="chart_{__chart_item}" style="overflow:hidden;" />
<script type="text/javascript">
dojo.addOnLoad(function() {
	var chart{__chart_item} = new AnyChart("{__chart_path_flash}");
	var url{__chart_item} = "index.php?sitio_id="+document.form_principal.sitio_id.value+
							"&menu_id="+document.form_principal.menu_id.value+
							"&objeto_id="+document.form_principal.objeto_id.value+
							"&parent_objetivo_id={__parent_objetivo_id}"+
							"&reporte_informe_subtipo_id={__reporte_informe_subtipo_id}"+
							"&ejecutar_accion=1&accion=buscar_item"+
							"&item_id={__chart_item}&item_tipo=xml"+
							"&tiene_flash=1&tiene_svg=0";
	
	if (document.form_principal.parent_objetivo_id) {
		url{__chart_item} = url{__chart_item}+"&parent_objetivo_id="+document.form_principal.parent_objetivo_id.value
	}
	
	chart{__chart_item}.width = {__chart_ancho};
	chart{__chart_item}.height = {__chart_alto};
	chart{__chart_item}.setXMLFile(url{__chart_item});
	chart{__chart_item}.write("chart_{__chart_item}");
	
//	document.getElementById("chart_{__chart_item}").innerHTML = url{__chart_item};

/*	if (document.getElementById("direcciones_xml")) {
		oldHTML = document.getElementById("direcciones_xml").innerHTML;
		document.getElementById("direcciones_xml").innerHTML = oldHTML+url{__chart_item}+"<br>";
	}*/
});
</script>