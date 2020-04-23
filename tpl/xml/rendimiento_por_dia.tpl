<anychart>
	<margin all="0"/>
	
	<charts>
		
		<!-- GRAFICO DE RENDIMIENTO POR PASO -->
		<chart plot_type="CategorizedVertical">
		
			<!-- FORMATO DEL GRAFICO -->
			<chart_settings>
				<title enabled="False" />
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
					</y_axis>
					<x_axis>
 						<title enabled="False" />
						<labels>
							<font size="9"/>
 							<format>{%Value}</format>
						</labels>
						<scale major_interval="24" />
					</x_axis>
				</axes>
				<legend enabled="True" ignore_auto_item="true" position="right" align="near" align_by="dataplot" columns="1" rows_padding="2" height="200" width="120" horizontal_padding="0">
					<background enabled="True">
						<fill enabled="False" />
						<border enabled="true" type="Solid" color="#b3b1b2"/>
					</background>
					<title enabled="true">
						<text>Pasos</text>
					</title>
					<font size="9"/>
					<items>
						<item source="Series"/>
					</items>
				</legend>
			</chart_settings>
			
			<!-- FORMATO DE DATOS -->
			<data_plot_settings>
				<line_series>
					<marker_settings enabled="True" marker_type="circle">
						<marker size="2" anchor="center"/>
					</marker_settings>
					<tooltip_settings enabled="True">
						<font bold="False" size="9"/>
						<format>
{%SeriesName}
{%Name}
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
				<!-- BEGIN SERIES_ELEMENT -->
				<series type="Line" id="{__series_id}" name="{__series_name}" color="#{__series_color}" style="solido">
					<!-- BEGIN POINT_ELEMENT -->
					<point name="{__point_name}" y="{__point_value}" />
					<!-- END POINT_ELEMENT -->
				</series>
				<!-- END SERIES_ELEMENT -->
			</data>
		</chart>
		
	</charts>
</anychart>