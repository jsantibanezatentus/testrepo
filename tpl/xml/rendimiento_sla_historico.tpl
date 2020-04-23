<anychart>
	<margin all="0"/>
	<charts>

		<!-- GRAFICO DE SLA -->
		<chart plot_type="CategorizedBySeriesVertical">
		
			<!-- FORMATO DEL GRAFICO -->
			<chart_settings>
				<title enabled="False" />
				<chart_background enabled="False">
					<inside_margin all="0"/>
				</chart_background>
				<axes>
					<y_axis>
						<title>
							<text>Mediciones (%)</text>
							<font  size="10" color="#5A5A5A"></font>
						</title>
						<scale mode="Normal" minimum="0" maximum="100"/>
						<labels>
							<font size="9"/>
							<format>{%Value}{numDecimals:2,decimalSeparator:,}</format>
						</labels>
					</y_axis>
					<x_axis>
						<title enabled="False" />
						<labels rotation = "75">
							<font size="9"/>
						</labels>
					</x_axis>
				</axes>
				<legend enabled="True" ignore_auto_item="true" position="right" align="near" align_by="dataplot" columns="1" rows_padding="2" height="80" width="120" horizontal_padding="0">
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
					</items>
				</legend>
			</chart_settings>
			
			<!-- FORMATO DE DATOS -->
			<data_plot_settings>
				<bar_series point_padding="-0.5">
					<tooltip_settings enabled="true">
						<font bold="False" size="9"/>
						<format>
{%SeriesName}
{%Name}: {%Value}%
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
				<!-- BEGIN SERIES_ELEMENT -->
				<series name="{__series_name}">
					<!-- BEGIN POINT_ELEMENT -->
					<point name="{__point_name}" y="{__point_value}" color="#{__point_color}" style="solido"/>
					<!-- END POINT_ELEMENT -->
				</series>
				<!-- END SERIES_ELEMENT -->
			</data>
		</chart>
		
	</charts>
</anychart>
