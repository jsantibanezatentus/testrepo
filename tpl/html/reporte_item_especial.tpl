
<table width="100%" id="tabla_item_{__item_id}" data-item-url="{__item_url}" data-usuario-id="{__sitio_usuario_id}" data-objetivo-id="{__objetivo_id}" data-ga-tracking-id="{__ga_tracking_id}">

        <tr>
		<td class="tituloitem" >{__item_titulo}</td>
                <td class="tituloitem" id="imagen_descargar" width="5%"><a href="#" onclick="try{ downloadCsv(); }catch(e){ }" id="descargar_csv" style="display: none"><i  class="spriteButton spriteButton-exportar" border="0" title="Descarga CSV"></i></a></a></td>
               <!-- BEGIN TIENE_UPDATE -->
                <td class="tituloitem" id="img_update" width="5%" style="display: none"><a href="#" onclick="try{ update(); }catch(e){ }"><i  class="spriteButton spriteButton-reloaded" title="Actualizar"></i></a></td>
                <!--END TIENE_UPDATE  -->
                <td class="tituloitem" align="center" width="5%">
			<a href="#" onclick="abrirPopup(['item_id', '{__item_id}','reporte_id', {__reporte_id}]); return false;"><i class="spriteButton spriteButton-abrir_popup"></i></a>
                </td>
	</tr>
</table>
<table width="100%">
	<tr>
		<td class="contenidoitem" style="height: expression(this.height > 10?'auto':'10px');">
			<!-- BEGIN TIENE_GENERAR_REPORTE -->
			<table width="100%">

				<tr>
					<td align="right">
						<table onclick="mostrarCalendario('{__item_id}');return false;"
							style="border-left: solid 1px #e0e0e0; border-right: solid 1px #e0e0e0; border-bottom: solid 1px #e0e0e0;"
							onMouseOver="this.style.borderLeft='solid 2px #626262'; this.style.borderRight='solid 2px #626262'; this.style.borderBottom='solid 2px #626262';"
							onMouseOut="this.style.borderLeft='solid 1px #e0e0e0'; this.style.borderRight='solid 1px #e0e0e0'; this.style.borderBottom='solid 1px #e0e0e0';">
							<tr>
								<td style="padding: 3px;" class="textgris10">{__item_horario}</td>
								<td style="padding: 3px;" class="textnegro13">{__item_periodo}</td>
								<td style="padding: 2px; border-left: solid 1px #e0e0e0; background-color: #f6f6f6;"><i id="boton_calendario_{__item_id}" class="spriteButton spriteButton-abrir_calendario"></i></td>
							</tr>
							
						</table>
					</td>
				</tr>

				<tr>
					<td id="generar_reporte_{__item_id}" style="display:none;" align="center">
						<div style="border: solid 1px #e0e0e0; background-color:#f6f6f6;">
							<table style="border-spacing: 5px; border-collapse: separate;">
								<tr>
									<td valign="top" id="calendario_periodico_{__item_id}"></td>
									<td valign="top" id="calendario_{__item_id}"></td>
									<td valign="top" id="horario_{__item_id}"></td>
								</tr>
								<tr>
									<td align="center" colspan="3">
										<input type="button" id="generar" class="boton_accion" value="Generar Reporte" onclick="document.form_principal.submit();"/>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
			</table>
			<!-- END TIENE_GENERAR_REPORTE -->
		</td>
	</tr>
            <td class="informacion-tag textdescripcion" id="descripcion" style="width: 400px"></td>
	<tr>

		<td class="contenidoitem" style="border-bottom: solid 1px #626262;">
                    <div id="load_audex"  style="display:{__tiene_carga}; " align="center"><img src="img/cargando.gif" ><span class="textgris12"> Por favor espere.<br>El reporte se esta generando.</span></div>
			<div dojoType="dojox.layout.ContentPane" id="contenedor_{__item_id}" style="overflow:hidden;">
				<table align="center">

				</table>
			</div>
                                <div id="demo" ></div>
		</td>
	</tr>
</table>
<br>
<script type="text/javascript" language="javascript">
dojo.addOnLoad(function() {
//	{__item_function_js}('contenedor_{__item_id}','{__item_id}',0);
    cargarItem('contenedor_{__item_id}', '{__item_id}', '1', ['reporte_id', {__reporte_id}]);

});

//muestra barra cargando load
$('#generar').bind('click', function() {
    $("#sin_datos").css("display", "none");
    $("#generar_reporte_{__item_id}").css("display", "none");
    $("#container").css("display", "none");
    $("#descripcion_audex").css("display", "none");
    $("#load_audex").css("display", "block");

});
</script>