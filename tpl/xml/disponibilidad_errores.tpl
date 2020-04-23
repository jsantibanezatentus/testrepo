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
					<view name="view_disponibilidad" type="Chart" width="50%" height="100%"/>
					<view name="view_downtime" type="Chart" width="50%" height="100%" />
				</hbox>
				<hbox width="100%" height="{__chart_view_height}%">
					<margin all="0"/>
					<view name = "view_errores" type="Chart" width="100%" height="100%" />
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
				<series>
					<!-- BEGIN PASO_POINT_ELEMENT -->
					<point row="r{__paso_point_row}" column="c{__paso_point_column}" y="{__paso_point_value}" selected="{__paso_point_selected}">
						<attributes>
							<attribute name="cdescrip">{__paso_attribute_content}</attribute>
						</attributes>
 						<actions>
							<action type="UpdateView" view="view_disponibilidad" source_mode="InternalData" source="chart_disponibilidad_{__paso_point_value}"/>
							<action type="UpdateView" view="view_downtime" source_mode="InternalData" source="chart_downtime_{__paso_point_value}"/>
							<action type="UpdateView" view="view_errores" source_mode="InternalData" source="chart_errores_{__paso_point_value}"/>
						</actions>
					</point>
					<!-- END PASO_POINT_ELEMENT -->
				</series>
			</data>
		</chart>

		<!-- BEGIN CHART_ELEMENT -->

		<!-- GRAFICO DE DISTRIBUCION DE DISPONIBILIDAD -->
		<chart name="chart_disponibilidad_{__chart_name}" plot_type="Pie">
		
			<!-- FORMATO DEL GRAFICO -->
			<chart_settings>
				<title enabled="true" text_align="center">
					<text>Disponibilidad Consolidada</text>
					<font bold="True" size="12" color="#5A5A5A"/>
				</title>
				<chart_background enabled="False">
					<inside_margin all="0"/>
				</chart_background>
				<legend enabled="True" ignore_auto_item="true" position="right" align="near" align_by="dataplot" columns="1" rows_padding="2" height="120" width="120" horizontal_padding="0">
					<margin right="2"/>
					<background enabled="True">
						<fill enabled="False"/>
						<border enabled="true" type="Solid" color="#b3b1b2"/>
					</background>
					<title enabled="true">
						<text>Leyenda</text>
					</title>
					<font size="9"/>
					<items>
						<item source="Points"/>
					</items>
				</legend>
			</chart_settings>
			
			<!-- FORMATO DE DATOS -->
			<data_plot_settings>
				<pie_series start_angle="-90" explode_on_click="false" radius="80" sort="desc">
					<label_settings enabled="true" mode="Inside" multi_line_align="center">
						<background enabled="true"><border enabled="false"/>
							<inside_margin all="0"/>
							<fill opacity="0.3" color="(%Color)"/>
							<corners type="rounded" all="3"/>
						</background>
						<position anchor="center" valign="center" padding="2"/>
						<format>{%YPercentOfSeries}{numDecimals:2,decimalSeparator:,}%</format>
						<font size="9" bold="False" color="white"></font>
					</label_settings>
					<tooltip_settings enabled="true">
						<font bold="False" size="9"/>
						<format>
{%Name}
{%YValue}{numDecimals:2,decimalSeparator:,}%
						</format>
					</tooltip_settings>
				</pie_series>
			</data_plot_settings>
			
			<!-- ESTILOS -->
			<styles>
				<pie_style name="solido">
					<fill enabled="true" type="Solid"/>
					<border enabled="False"/>
				</pie_style>
			</styles>
			
			<!-- DATOS -->
			<data>
				<series>
					<!-- BEGIN DISPONIBILIDAD_POINT_ELEMENT -->
					<point name="{__disponibilidad_point_name}" y="{__disponibilidad_point_value}" color="#{__disponibilidad_point_color}" style="solido"/>
					<!-- END DISPONIBILIDAD_POINT_ELEMENT -->
				</series>
			</data>
		</chart>
		
		<!-- GRAFICO DE DISTRIBUCION DE ERRORES -->
		<chart name="chart_downtime_{__chart_name}" plot_type="Pie">
		
			<!-- FORMATO DEL GRAFICO -->
			<chart_settings>
				<title enabled="True" text_align="center">
					<text>Downtime entre ISPs</text>
					<font bold="True" size="12" color="#5A5A5A" />
				</title>
				<chart_background enabled="False">
					<inside_margin all="0"/>
				</chart_background>
				<legend enabled="True" ignore_auto_item="true" position="right" align="near" align_by="dataplot" columns="1" rows_padding="2" height="200" width="120" horizontal_padding="0">
					<background enabled="True">
						<fill enabled="False" />
						<border enabled="true" type="Solid" color="#b3b1b2"/>
					</background>
					<title enabled="true">
						<text>ISP</text>
					</title>
					<font size="9"/>
					<items>
						<item source="Points"/>
					</items>
				</legend>
			</chart_settings>

			<!-- FORMATO DE DATOS -->
			<data_plot_settings>
				<pie_series start_angle="-90" radius="80" explode_on_click="false" sort="desc">
					<label_settings enabled="True" mode="Inside">
						<background enabled="true">
							<border enabled="false"/>
							<inside_margin all="0"/>
							<fill opacity="0.3" color="(%Color)"/>
							<corners type="rounded" all="3"/>
						</background>
						<position anchor="center" valign="center" padding="2"/>
						<format>{%YPercentOfSeries}{numDecimals:2}%</format>
						<font size="9" bold="False" color="white"></font>
					</label_settings>
					<tooltip_settings enabled="true">
						<font bold="False" size="9"/>
						<format>
{%Name}
{%YValue}{numDecimals:2,decimalSeparator:,}%
						</format>
					</tooltip_settings>
				</pie_series>
			</data_plot_settings>
			
			<!-- ESTILOS -->
			<styles>
				<pie_style name="solido">
					<fill enabled="true" type="Solid"/>
					<border enabled="False"/>
				</pie_style>
			</styles>

			<!-- DATOS -->
			<data>
				<series>
					<!-- BEGIN DOWNTIME_POINT_ELEMENT -->
					<point name="{__downtime_point_name}" y="{__downtime_point_value}" color="#{__downtime_point_color}" style="solido"/>
					<!-- END DOWNTIME_POINT_ELEMENT -->
				</series>
			</data>
		</chart>
		
		<!-- GRAFICO DE ERRORES POR MONITOR -->
		<chart name="chart_errores_{__chart_name}" plot_type="CategorizedBySeriesVertical">

			<!-- FORMATO DEL GRAFICO -->
			<chart_settings>
				<title enabled="True">
					<text>Distribucion de Errores por ISP</text>
					<font bold="True" size="12" color="#5A5A5A"/>
				</title>
				<chart_background enabled="False">
					<inside_margin all="0"/>
				</chart_background>
				<axes>
					<y_axis>
						<title>
							<text>Errores (%)</text>
							<font size="10" color="#5A5A5A"></font>
						</title>
						<scale mode="PercentStacked"/>
						<labels>
							<font size="9"/>
							<format>{%Value}{numDecimals:2,decimalSeparator:,}</format>
						</labels>		
					</y_axis>
					<x_axis>
						<title enabled="False"/>
						<labels rotation = "75">
							<font size="9"/>
						</labels>
					</x_axis>
				</axes>
				<legend enabled="True" ignore_auto_item="true" position="right" align="near" align_by="dataplot" columns="1" rows_padding="2" height="180" width="120" horizontal_padding="0">
					<background enabled="True">
						<fill enabled="False"/>
						<border enabled="true" type="Solid" color="#b3b1b2"/>
					</background>
					<title enabled="true">
						<text>Errores</text>
					</title>
					<font size="9"/>
					<items>
						<item source="Categories"/>
					</items>
				</legend>
			</chart_settings>

			<!-- FORMATO DE DATOS -->
			<data_plot_settings>
				<bar_series group_padding="{__bar_series_width}">
					<tooltip_settings enabled="true">
						<font bold="False" size="9"/>
						<format>
{%SeriesName}
{%Name}
{%Value}%
						</format>
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
				<!-- BEGIN ERRORES_SERIES_ELEMENT -->
				<series name="{__errores_series_name}">
					<!-- BEGIN ERRORES_POINT_ELEMENT -->
					<point name="{__errores_point_name}" y="{__errores_point_value}"  color="#{__errores_point_color}" style="solido"/>	
					<!-- END ERRORES_POINT_ELEMENT -->
				</series>
				<!-- END ERRORES_SERIES_ELEMENT -->				
			</data>
		</chart>

		<!-- END CHART_ELEMENT -->
		
	</charts>
</anychart>
