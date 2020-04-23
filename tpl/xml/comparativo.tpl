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
					<view name = "view_comparativo" type="Chart" width="100%" height="100%" />
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
							<action type="UpdateView" view="view_comparativo" source_mode="InternalData" source="chart_comparativo_{__paso_point_value}"/>
						</actions>
					</point>
					<!-- END PASO_POINT_ELEMENT -->
				</series>
			</data>
		</chart>
		
		<!-- BEGIN CHART_ELEMENT -->
	
		<chart name="chart_comparativo_{__chart_name}" plot_type="CategorizedVertical">
			<chart_settings>
				<title enabled="False"/>
				<chart_background enabled="False">
					<inside_margin all="0"/>
				</chart_background>
				<axes>
					<y_axis>
						<title enabled="true">
							<text>Disponibilidad (%)</text>
							<font size="10" color="#5A5A5A"></font>
						</title>
						<scale mode="Stacked" minimum="0" maximum="100"/>
						<labels>
							<font size="9"/>
							<format>{%Value}{numDecimals:2,decimalSeparator:,}</format>
						</labels>
					</y_axis>
					<x_axis>
						<title enabled="False" />
						<scale type="DateTime"  major_interval="1"  major_interval_unit="Day"/>
						<labels rotation = "75">
							<font size="9"/>
							<format>{%Value}{dateTimeFormat:%dd/%MM}</format>
						</labels>
					</x_axis>
					<extra>
						<y_axis name="extra_y_axis_1" position="Right">
					  		<scale maximum="{__extra_y_maximum}" minimum="0"/>					  	
							<title enabled="true">
								<text>Respuesta (Segs)</text>
								<font size="10" color="#5A5A5A"></font>
							</title>
	 						<labels>
								<font size="9"/>
								<format>{%Value}{numDecimals:2,decimalSeparator:,}</format>
							</labels>
						</y_axis>
					</extra>
				</axes>
				<legend enabled="True" ignore_auto_item="true" position="right" align="near" align_by="dataplot" columns="1" rows_padding="2" height="100" width="120" horizontal_padding="0">
					<background enabled="True">
						<fill enabled="False" />
						<border enabled="true" type="Solid" color="#b3b1b2"/>
					</background>
					<title enabled="true">
						<text>Leyenda</text>
					</title>
					<font size="9"/>
					<items>
						<item source="series"/>
					</items>
				</legend>
			</chart_settings>

			<data_plot_settings>
				<bar_series group_padding="{__bar_series_width}">
					<bar_style>
						<fill enabled="true" type="solid" opacity="0.80"/>
					</bar_style>
					<tooltip_settings enabled="true">
						<font bold="False" size="9"/>
						<format>
{%SeriesName}: {%YValue}{numDecimals:2,decimalSeparator:,}%
						</format>
					</tooltip_settings>
				</bar_series>
				<line_series>
					<line_style thickness="3">
						<line enabled="true" type="solid"/>
					</line_style>
					<marker_settings enabled="true" marker_type="Circle" color="#FFE25F">
						<marker size="5"/>
					</marker_settings>
					<tooltip_settings enabled="true">
						<font bold="False" size="9"/>
						<format>
{%SeriesName}: {%YValue}{numDecimals:2,decimalSeparator:,} segs
						</format>
					</tooltip_settings>
				</line_series>
				<area_series>
					<area_style>
						<line enabled="true" thickness="1" color="DarkColor(%Color)"/>
						<fill enabled="true" type="solid" opacity="0.80"/>
					</area_style>
					<marker_settings enabled="true" marker_type="Circle" color="#F7AF72">
						<marker size="5"/>
					</marker_settings>
					<tooltip_settings enabled="true">
						<format>
{%SeriesName}: {%YValue}{numDecimals:0}
						</format>
					</tooltip_settings>
				</area_series>
			</data_plot_settings>
			
			<data>
				<series name="Uptime" type="Bar" color="#55a51c">
					<!-- BEGIN DISPONIBILIDAD_POINT_ELEMENT -->
					<point name="{__disponibilidad_point_name}" y="{__disponibilidad_point_value}"  />
					<!-- END DISPONIBILIDAD_POINT_ELEMENT -->
				</series>

				<series name="Respuesta" type="Spline" y_axis="extra_y_axis_1" color="#FFE25F"> <!-- rendimiento -->
					<!-- BEGIN RENDIMIENTO_POINT_ELEMENT -->
					<point name="{__rendimiento_point_name}"  y="{__rendimiento_point_value}"/>
					<!-- END RENDIMIENTO_POINT_ELEMENT -->
				</series>

			</data>
		</chart>
		
		<!-- END CHART_ELEMENT -->
    
	</charts>
</anychart>