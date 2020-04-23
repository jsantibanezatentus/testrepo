<anychart>
	<margin all="0"/>
		
	<dashboard>
		<view type="Dashboard">
			<margin all="0"/>
			<background enabled="False" />
			<title enabled="False" />
			<vbox width="100%" height="100%">
				<margin all="0"/>
				<hbox width="100%" height="{__monitor_view_height}%">
					<margin all="0"/>
					<view source="chart_monitores" type="Chart" width="{__monitor_view_width}%" height="100%"/>
				</hbox>
				<hbox width="100%" height="{__chart_view_height}%">
					<margin all="0"/>
					<view name = "view_disponibilidad" type="Chart" width="100%" height="100%" />
				</hbox>
				<hbox width="100%" height="{__chart_view_height}%">
					<margin all="0"/>
					<view name = "view_sinnomonitoreo" type="Chart" width="100%" height="100%" />
				</hbox>
			</vbox>
		</view>
	</dashboard>

	<charts>

		<!-- GRAFICO DE SELECCION DE NOMBRES -->
		<chart plot_type="HeatMap" name="chart_monitores">
		
			<!-- FORMATO DEL GRAFICO -->
			<chart_settings>
				<title enabled="true" align="Near">
					<text>Monitor:</text>
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
				<series>
					<!-- BEGIN MONITOR_POINT_ELEMENT -->
					<point row="r{__monitor_point_row}" column="c{__monitor_point_column}" y="{__monitor_point_value}" selected="{__monitor_point_selected}">
						<attributes>
							<attribute name="cdescrip">{__monitor_attribute_content}</attribute>
						</attributes>

 						<actions>
							<action type="UpdateView" view="view_disponibilidad" source_mode="InternalData" source="chart_disponibilidad_{__monitor_point_value}"/>
							<action type="UpdateView" view="view_sinnomonitoreo" source_mode="InternalData" source="chart_sinnomonitoreo_{__monitor_point_value}"/>
						</actions>
					</point>
					<!-- END MONITOR_POINT_ELEMENT -->
				</series>
			</data>
		</chart>

		<!-- BEGIN CHART_ELEMENT -->
		
		<!-- GRAFICO DE DISPONIBILIDAD POR PASO -->
		<chart name="chart_disponibilidad_{__chart_name}" plot_type="CategorizedBySeriesVertical">
		
			<!-- FORMATO DEL GRAFICO -->
			<chart_settings>
				<title enabled="True">
					<text>Disponibilidad Detallada por Paso</text>
					<font bold="True" size="12" color="#5A5A5A" />
				</title>
				<chart_background enabled="False">
					<inside_margin all="0"/>
				</chart_background>
				<axes>
					<y_axis>
						<title>
							<text>Disponibilidad (%)</text>
							<font size="10" color="#5A5A5A"></font>
						</title>
						<scale mode="Stacked" minimum="{__disponibilidad_scale_minimum}" maximum="100"/>
						<labels>
							<font size="9"/>
							<format>{%Value}{numDecimals:2,decimalSeparator:,}</format>
						</labels>
						<axis_markers>
					    	<lines>
							    <!-- BEGIN TIENE_SLA_OK -->
								<line value="{__sla_ok_value}" opacity="0.6" color="#54a51c" thickness="2" display_under_data="true"/>
								<line value="{__sla_ok_value}" opacity="0" color="#54a51c" thickness="0" display_under_data="false">
									<label enabled='True' position="near" padding="2">
										<font color="#54a51c" bold="True" size="8"/>
										<format>{__sla_ok_value}</format>
										<background enabled="true">
											<border opacity="0.9"/>
											<fill opacity="0.8"/>
											<corners type="Rounded" all="5"/>
										</background>
									</label>
								</line>
								<!-- END TIENE_SLA_OK -->
								<!-- BEGIN TIENE_SLA_ERROR -->
								<line value="{__sla_error_value}" opacity="0.6" color="#d22129" thickness="2" display_under_data="true"/>
								<line value="{__sla_error_value}" opacity="0" color="#d22129" thickness="0" display_under_data="false">
									<label enabled='True' position="far" padding="2">
										<font color="#d22129" bold="True" size="8"/>
										<format>{__sla_error_value}</format>
										<background enabled="true">
											<border opacity="0.9"/>
											<fill opacity="0.8"/>
											<corners type="Rounded" all="5"/>
										</background>
									</label>
								</line>
								<!-- END TIENE_SLA_ERROR -->
							</lines>
						</axis_markers>	
					</y_axis>
					<x_axis>
						<labels display_mode="Stager" align="inside" allow_overlap="true">
							<font size="9"/>
						</labels>
						<title enabled="False"/>
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
						<item source="Categories"/>
						<item>
							<icon color="#32CD32" series_type="Line"></icon>
							<format>{%Icon} SLA Ok</format>
						</item>
						<item>
							<icon color="#FF4500" series_type="Line"></icon>
							<format>{%Icon} SLA Error</format>
						</item>
					</items>
				</legend>
			</chart_settings>
			
			<!-- FORMATO DE DATOS -->
			<data_plot_settings>
				<bar_series group_padding="{__bar_series_width}">
 					<tooltip_settings enabled="true">
						<font bold="False" size="9"/>
					</tooltip_settings>
				</bar_series>
			</data_plot_settings>
			
			<!-- ESTILOS -->
			<styles>
				<bar_style name="solido">
					<fill enabled="true" type="Solid"/>
					<border enabled="False"/>
					<effects enabled="False" />
					<states>
						<hover>
							<fill enabled="true" type="Solid" opacity="0.6" color="(%Color)"/>
						</hover>
					</states>
				</bar_style>
			</styles>
			
			<!-- DATOS -->
			<data>
				<!-- BEGIN DISPONIBILIDAD_SERIES_ELEMENT -->
				<series name="{__series_name}">
					<!-- BEGIN DISPONIBILIDAD_POINT_ELEMENT -->
					<point name="{__disponibilidad_point_name}" y="{__disponibilidad_point_value}" color="#{__disponibilidad_point_color}" style="solido">
						<attributes>
							<attribute name="paso_nombre">{__paso_nombre}</attribute>
						</attributes>
					</point>
					<!-- END DISPONIBILIDAD_POINT_ELEMENT -->
					<tooltip>
						<format>
{__paso_nombre}
<!-- BEGIN DISPONIBILIDAD_SERIES_TOOLTIP -->
{__disponibilidad_point_name}: {__disponibilidad_point_value}%
<!-- END DISPONIBILIDAD_SERIES_TOOLTIP -->
						</format>
					</tooltip>
				</series>
				<!-- END DISPONIBILIDAD_SERIES_ELEMENT -->
			</data>
		</chart>


		<chart name="chart_sinnomonitoreo_{__chart_name}" plot_type="CategorizedBySeriesVertical">
		
			<!-- FORMATO DEL GRAFICO -->
			<chart_settings>
				<title enabled="True">
					<text>Disponibilidad Sin Considerar No Monitoreo</text>
					<font bold="True" size="12" color="#5A5A5A" />
				</title>
				<chart_background enabled="False">
					<inside_margin all="0"/>
				</chart_background>
				<axes>
					<y_axis>
						<title>
							<text>Disponibilidad (%)</text>
							<font size="10" color="#5A5A5A"></font>
						</title>
						<scale mode="Stacked" minimum="{__disponibilidad_scale_minimum}" maximum="100"/>
						<labels>
							<font size="9"/>
							<format>{%Value}{numDecimals:2,decimalSeparator:,}</format>
						</labels>
						<axis_markers>
					    	<lines>
							    <!-- BEGIN TIENE_SLA_OK -->
								<line value="{__sla_ok_value}" opacity="0.6" color="#54a51c" thickness="2" display_under_data="true"/>
								<line value="{__sla_ok_value}" opacity="0" color="#54a51c" thickness="0" display_under_data="false">
									<label enabled='True' position="near" padding="2">
										<font color="#54a51c" bold="True" size="8"/>
										<format>{__sla_ok_value}</format>
										<background enabled="true">
											<border opacity="0.9"/>
											<fill opacity="0.8"/>
											<corners type="Rounded" all="5"/>
										</background>
									</label>
								</line>
								<!-- END TIENE_SLA_OK -->
								<!-- BEGIN TIENE_SLA_ERROR -->
								<line value="{__sla_error_value}" opacity="0.6" color="#d22129" thickness="2" display_under_data="true"/>
								<line value="{__sla_error_value}" opacity="0" color="#d22129" thickness="0" display_under_data="false">
									<label enabled='True' position="far" padding="2">
										<font color="#d22129" bold="True" size="8"/>
										<format>{__sla_error_value}</format>
										<background enabled="true">
											<border opacity="0.9"/>
											<fill opacity="0.8"/>
											<corners type="Rounded" all="5"/>
										</background>
									</label>
								</line>
								<!-- END TIENE_SLA_ERROR -->
							</lines>
						</axis_markers>	
					</y_axis>
					<x_axis>
						<labels display_mode="Stager" align="inside" allow_overlap="true">
							<font size="9"/>
						</labels>
						<title enabled="False"/>
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
						<item source="Categories"/>
						<item>
							<icon color="#32CD32" series_type="Line"></icon>
							<format>{%Icon} SLA Ok</format>
						</item>
						<item>
							<icon color="#FF4500" series_type="Line"></icon>
							<format>{%Icon} SLA Error</format>
						</item>
					</items>
				</legend>
			</chart_settings>
			
			<!-- FORMATO DE DATOS -->
			<data_plot_settings>
				<bar_series group_padding="{__bar_series_width}">
 					<tooltip_settings enabled="true">
						<font bold="False" size="9"/>
					</tooltip_settings>
				</bar_series>
			</data_plot_settings>
			
			<!-- ESTILOS -->
			<styles>
				<bar_style name="solido">
					<fill enabled="true" type="Solid"/>
					<border enabled="False"/>
					<effects enabled="False" />
					<states>
						<hover>
							<fill enabled="true" type="Solid" opacity="0.6" color="(%Color)"/>
						</hover>
					</states>
				</bar_style>
			</styles>
			
			<!-- DATOS -->
			<data>
				<!-- BEGIN SINNOMONITOREO_SERIES_ELEMENT -->
				<series name="{__series_name}">
					<!-- BEGIN SINNOMONITOREO_POINT_ELEMENT -->
					<point name="{__sinnomonitoreo_point_name}" y="{__sinnomonitoreo_point_value}" color="#{__sinnomonitoreo_point_color}" style="solido">
						<attributes>
							<attribute name="paso_nombre">{__paso_nombre}</attribute>
						</attributes>
					</point>
					<!-- END SINNOMONITOREO_POINT_ELEMENT -->
					<tooltip>
						<format>
{__paso_nombre}
<!-- BEGIN SINNOMONITOREO_SERIES_TOOLTIP -->
{__sinnomonitoreo_point_name}: {__sinnomonitoreo_point_value}%
<!-- END SINNOMONITOREO_SERIES_TOOLTIP -->
						</format>
					</tooltip>
				</series>
				<!-- END SINNOMONITOREO_SERIES_ELEMENT -->
			</data>
		</chart>
		<!-- END CHART_ELEMENT -->
		
	</charts>
</anychart>