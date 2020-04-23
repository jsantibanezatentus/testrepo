<anychart>
	<margin all="0"/>

	<dashboard>
		<view type="Dashboard">
			<margin all="0"/>
			<background enabled="False" />
			<title enabled="False" />
			<vbox width="100%" height="100%">
				<margin all="0"/>
				<hbox width="100%" height="{__paso_view_height}%">
					<margin all="0"/>
					<view source="chart_pasos" type="Chart" width="{__paso_view_width}%" height="100%"/>
				</hbox>
				<hbox width="100%" height="{__chart_view_height}%">
					<margin all="0"/>
					<view name = "view_estadistica" type="Chart" width="100%" height="100%" />
				</hbox>
				<hbox width="100%" height="{__chart_view_height}%">
					<margin all="0"/>
					<view name="view_rendimiento" type="Chart" width="100%" height="100%"/>
				</hbox>
			</vbox>
		</view>
	</dashboard>

	<charts>
	
		<!-- GRAFICO DE SELECCION DE NOMBRES -->
		<chart plot_type="HeatMap" name="chart_pasos">
		
			<!-- FORMATO DEL GRAFICO -->
			<chart_settings>
				<title enabled="true" align="Near">
					<text>Paso:</text>
					<font size="10" color="#5A5A5A"></font>
				</title>
				<chart_background enabled="False">
					<inside_margin all="0"/>
				</chart_background>
				<axes>
					<x_axis enabled="True">
						<labels enabled="False"/>
						<title enabled="False"/>
						<major_tickmark enabled="False"/>
						<line color="#ccb48b" />
					</x_axis>
					<y_axis enabled="True" position="Opposite">
						<!-- BEGIN MOSTRAR_SCROLL -->
						<zoom enabled="true" start="0" end="4" allow_drag="false"/>
						<!-- END MOSTRAR_SCROLL -->
						<scale inverted="true"/>
						<labels enabled="False"/>
						<title enabled="False"/>
						<major_tickmark enabled="False"/>
						<line color="#ccb48b" />
					</y_axis>
				</axes>
				<legend enabled="false" />
				<title enabled="False" />
			</chart_settings>
			
			<!-- FORMATO DE DATOS -->
			<data_plot_settings>
				<heat_map>
					<heat_map_style>
						<fill enabled="true" type="Solid" color="#f0ede8" />
						<border enabled="true" type="Solid" color="#ccb48b"/>
						<states>
							<hover>
								<hatch_fill enabled="true" type="Percent50" color="#ccb48b"/>
							</hover>
							<selected_normal>
								<fill enabled="true" type="Solid" color="#f47000"/>
							</selected_normal>
 							<selected_hover>
								<fill enabled="true" type="Solid" color="#f47000"/>
							</selected_hover> 
						</states>
					</heat_map_style>
					<label_settings enabled="true">
						<position anchor="Center" valign="Center" halign="Center" padding="0"/>
						<format>{%cdescrip}</format>
 						<states>
 							<selected_normal>
								<font color="#ffffff"/>
							</selected_normal> 
 							<selected_hover>
								<font color="#ffffff"/>
							</selected_hover> 
						</states>
					</label_settings>
				</heat_map>
			</data_plot_settings>
			
			<!-- DATOS -->
			<data>
				<series threshold="autoTr">
					<!-- BEGIN PASO_POINT_ELEMENT -->
					<point row="r{__paso_point_row}" column="c{__paso_point_column}" y="{__paso_point_value}" selected="{__paso_point_selected}">
						<attributes>
							<attribute name="cdescrip">{__paso_attribute_content}</attribute>
						</attributes>
 						<actions>
							<action type="UpdateView" view="view_rendimiento" source_mode="InternalData" source="chart_rendimiento_{__paso_point_value}"/>
							<action type="UpdateView" view="view_estadistica" source_mode="InternalData" source="chart_estadistica_{__paso_point_value}"/>
						</actions>
					</point>
					<!-- END PASO_POINT_ELEMENT -->
				</series>
			</data>
		</chart>

		<!-- BEGIN CHART_ELEMENT -->

		<!-- GRAFICO DE ESTADISTICAS -->
		<chart plot_type="Scatter" name="chart_estadistica_{__chart_name}">

			<!-- FORMATO DEL GRAFICO -->
			<chart_settings>
				<title enabled="True">
					<text>Informacion Estadistica Consolidada</text>
					<font bold="True" size="12" color="#5A5A5A" />
				</title>
				<chart_background enabled="False">
					<inside_margin all="0"/>
				</chart_background>
				<axes>
					<y_axis>						
					  	<scale maximum="{__y_scale_maximum}" minimum="{__y_scale_minimun}" />					  	
						<title>
							<text>Respuesta (Segs)</text>
							<font size="10" color="#5A5A5A"></font>
						</title>
 						<labels>
 							<font size="9"/>
							<format>{%Value}{numDecimals:2,decimalSeparator:,}</format>
						</labels>
						<axis_markers>
							<lines>
								<line value="{__estadistica_prom_line_value}" opacity="0.6" color="#00529e" thickness="2" display_under_data="true"/>
								<line value="{__estadistica_prom_line_value}" opacity="0" color="#00529e" thickness="0" display_under_data="false">
									<label enabled='True' position="far" padding="2" multi_line_align="center">
 										<font color="#00529e" bold="True" size="8"/>
										<background enabled="true"><border opacity="0.6"/>
											<fill opacity="0.5"/>
											<corners type="rounded" all="5"/>
										</background>
										<format>{__estadistica_prom_line_value}</format>
									</label>
								</line>
							    <!-- BEGIN TIENE_ESTADISTICA_SLA_OK -->
								<line value="{__estadistica_sla_ok_value}" opacity="0.6" color="#54a51c" thickness="2" display_under_data="true"/>
								<line value="{__estadistica_sla_ok_value}" opacity="0" color="#54a51c" thickness="0" display_under_data="false">
									<label enabled='True' position="near" padding="2">
										<font color="#54a51c" bold="True" size="8"/>
										<format>{__estadistica_sla_ok_value}</format>
										<background enabled="true">
											<border opacity="0.6"/>
											<fill opacity="0.5"/>
											<corners type="rounded" all="5"/>
										</background>
									</label>
								</line>
								<!-- END TIENE_ESTADISTICA_SLA_OK -->
								<!-- BEGIN TIENE_ESTADISTICA_SLA_ERROR -->
								<line value="{__estadistica_sla_error_value}" opacity="0.6" color="#d22129" thickness="2" display_under_data="true"/>
								<line value="{__estadistica_sla_error_value}" opacity="0" color="#d22129" thickness="0" display_under_data="false">
									<label enabled='True' position="far" padding="2">
										<font color="#d22129" bold="True" size="8"/>
										<format>{__estadistica_sla_error_value}</format>
										<background enabled="true">
											<border opacity="0.6"/>
											<fill opacity="0.5"/>
											<corners type="rounded" all="5"/>
										</background>
									</label>
								</line>
								<!-- END TIENE_ESTADISTICA_SLA_ERROR -->
							</lines>
							<ranges>
								<range minimum="{__estadistica_range_minimum}" maximum="{__estadistica_range_maximum}" display_under_data="False">
									<minimum_line enabled='False'/>
									<maximum_line enabled='False'/> 
									<fill enabled="True" color="#00529e" opacity="0.2"/>
									<label enabled='False'/>
								</range>
							</ranges>
						</axis_markers>
					</y_axis>
					<x_axis>
						<title enabled="False" />
						<scale type="DateTime"  major_interval="1"  major_interval_unit="{__x_major_interval_unit}" minimum="{__x_scale_minimun}" maximum="{__x_scale_maximun}"/>
						<labels rotation = "75">
							<font size="9"/>
							<format>{%Value}{dateTimeFormat:{__x_format_value}}</format>
						</labels>
						<axis_markers>
							<ranges>
								<!-- BEGIN ESTADISTICA_RANGE_ELEMENT -->
								<range minimum="{__range_minimum}" maximum="{__range_maximum}" display_under_data="False">
									<minimum_line enabled='False'/>
									<maximum_line enabled='False'/> 
									<fill enabled="True" color="#54a51c" opacity="0.1"/>
									<label enabled='False'/>
								</range>
								<!-- END ESTADISTICA_RANGE_ELEMENT -->
							</ranges>
						</axis_markers>
					</x_axis>
				</axes>
				<legend enabled="True" ignore_auto_item="true" position="right" align="near" align_by="dataplot" columns="1" rows_padding="2" height="160" width="120" horizontal_padding="0">
					<background enabled="True">
						<fill enabled="False" />
						<border enabled="true" type="Solid" color="#b3b1b2"/>
					</background>
					<title enabled="true">
						<text>Leyenda</text>
					</title>
					<font size="9"/>
					<items>
						<item>
							<icon color="#32CD32" series_type="Line"></icon>
							<format>{%Icon} SLA Ok</format>
						</item>
						<item>
							<icon color="#FF4500" series_type="Line"></icon>
							<format>{%Icon} SLA Error</format>
						</item>
						<item>
							<icon color="#00529e" series_type="Line"></icon>
							<format>{%Icon} Promedio</format>
						</item>
						<item>
							<icon color="#aec2d5" series_type="bar"></icon>
							<format>
{%Icon} Desviación
 Estandar
							</format>
						</item>
						<item>
							<icon color="#dae7d2" series_type="bar"></icon>
							<format>{%Icon} Horario Hábil</format>
						</item>
					</items>
				</legend>
			</chart_settings>
			
			<!-- FORMATO DE DATOS -->
			<data_plot_settings>
				<line_series>
					<marker_settings enabled="False"/>
					<tooltip_settings enabled="True">
						<font bold="false" size="9"/>
						<format>
{%SeriesName}
Fecha: {%XValue}{dateTimeFormat:%dd-%MM-%yyyy %HH:%mm}
Respuesta: {%YValue}{numDecimals:2,decimalSeparator:,} segs
						</format>
					</tooltip_settings>
				</line_series>
			</data_plot_settings>
			
			<!-- ESTILOS -->
			<styles>
				<line_style name="solido">
					<line enabled="true" type="Solid"/>
					<states>
						<hover>
							<line thickness="4" color="#f47000"/>
						</hover>
					</states>
				</line_style>
			</styles>
			
			<data>
				<series type="Line" id="{__estadistica_series_id}" name="{__estadistica_series_name}" color="#{__estadistica_series_color}" style="solido">
					<!-- BEGIN ESTADISTICA_POINT_ELEMENT -->
					<point x="{__estadistica_point_name}" y="{__estadistica_point_value}"/>
					<!-- END ESTADISTICA_POINT_ELEMENT -->
				</series>
			</data>
		</chart>

		<!-- GRAFICO DE RENDIMIENTO POR MONITOR -->
		<chart plot_type="Scatter" name="chart_rendimiento_{__chart_name}">
		
			<!-- FORMATO DEL GRAFICO -->
			<chart_settings>
				<title enabled="True">
					<text>Tiempos de Respuesta por ISP</text>
					<font bold="True" size="12" color="#5A5A5A" />
				</title>
				<chart_background enabled="False">
					<inside_margin all="0"/>
				</chart_background>
				<axes>
					<y_axis>
					  	<scale maximum="{__y_scale_maximum}" minimum="{__y_scale_minimun}" />
						<title>
							<text>Respuesta (Segs)</text>
							<font  size="10" color="#5A5A5A"></font>
						</title>
 						<labels>
 							<font size="9"/>
							<format>{%Value}{numDecimals:2,decimalSeparator:,}</format>
						</labels>
						<axis_markers>
							<lines>
							    <!-- BEGIN TIENE_RENDIMIENTO_SLA_OK -->
								<line value="{__rendimiento_sla_ok_value}" opacity="0.6" color="#54a51c" thickness="2" display_under_data="true"/>
								<line value="{__rendimiento_sla_ok_value}" opacity="0" color="#54a51c" thickness="0" display_under_data="false">
									<label enabled='True' position="near" padding="2" multi_line_align="center">
										<font color="#54a51c" bold="True" size="8"/>
										<format>{__rendimiento_sla_ok_value}</format>
										<background enabled="true">
											<border opacity="0.6"/>
											<fill opacity="0.5"/>
											<corners type="rounded" all="5"/>
										</background>
									</label>
								</line>
								<!-- END TIENE_RENDIMIENTO_SLA_OK -->
								<!-- BEGIN TIENE_RENDIMIENTO_SLA_ERROR -->
								<line value="{__rendimiento_sla_error_value}" opacity="0.6" color="#d22129" thickness="2" display_under_data="true"/>
								<line value="{__rendimiento_sla_error_value}" opacity="0" color="#d22129" thickness="0" display_under_data="false">
									<label enabled='True' position="far" padding="2">
										<font color="#d22129" bold="True" size="8"/>
										<format>{__rendimiento_sla_error_value}</format>
										<background enabled="true">
											<border opacity="0.6"/>
											<fill opacity="0.5"/>
											<corners type="rounded" all="5"/>
										</background>
									</label>
								</line>
								<!-- END TIENE_RENDIMIENTO_SLA_ERROR -->
							</lines>
						</axis_markers>
					</y_axis>
					<x_axis>
						<title enabled="False" />
						<labels rotation = "75">
							<font size="9"/>
							<format>{%Value}{dateTimeFormat:{__x_format_value}}</format>
						</labels>
						<scale type="DateTime"  major_interval="1"  major_interval_unit="{__x_major_interval_unit}" minimum="{__x_scale_minimun}" maximum="{__x_scale_maximun}"/>
						<axis_markers>
							<ranges>
								<!-- BEGIN RENDIMIENTO_RANGE_ELEMENT -->
								<range minimum="{__range_minimum}" maximum="{__range_maximum}" display_under_data="False">
									<minimum_line enabled='False'/>
									<maximum_line enabled='False'/> 
									<fill enabled="True" color="#54a51c" opacity="0.1"/>
									<label enabled='False'/>
								</range>
								<!-- END RENDIMIENTO_RANGE_ELEMENT -->
							</ranges>
						</axis_markers>
					</x_axis>
				</axes>
				<legend enabled="True" ignore_auto_item="true" position="right" align="near" align_by="dataplot" columns="1" rows_padding="2" height="180" width="120" horizontal_padding="0">
					<background enabled="True"><fill enabled="False"/><border enabled="true" type="Solid" color="#b3b1b2"/></background>
					<title enabled="true"><text>ISP</text></title>
					<font size="9"/>
					<items>
						<item source="Series"/>
					</items>
				</legend>
			</chart_settings>
			
			<!-- FORMATO DE DATOS -->
			<data_plot_settings>
				<line_series>
					<marker_settings enabled="False"/>
					<tooltip_settings enabled="True">
						<font bold="False" size="9"/>
						<format>
{%SeriesName}
Fecha: {%XValue}{dateTimeFormat:%dd-%MM-%yyyy %HH:%mm}
Respuesta: {%YValue}{numDecimals:2,decimalSeparator:,} segs
						</format>
					</tooltip_settings>
				</line_series>
			</data_plot_settings>
			
			<!-- ESTILOS -->
			<styles>
				<line_style name="solido">
					<line enabled="true" type="Solid"/>
					<states>
						<hover>
							<line thickness="4" color="#f47000"/>
						</hover>
					</states>
				</line_style>
			</styles>
			
			<!-- DATOS -->
			<data>
				<!-- BEGIN RENDIMIENTO_SERIES_ELEMENT -->
				<series type="Line" id="{__rendimiento_series_id}" name="{__rendimiento_series_name}" color="#{__rendimiento_series_color}" style="solido">
					<!-- BEGIN RENDIMIENTO_POINT_ELEMENT -->
					<point x="{__rendimiento_point_name}" y="{__rendimiento_point_value}"/>
					<!-- END RENDIMIENTO_POINT_ELEMENT -->
				</series>
				<!-- END RENDIMIENTO_SERIES_ELEMENT -->
			</data>
		</chart>
		
		<!-- END CHART_ELEMENT -->
		
	</charts>
</anychart>
