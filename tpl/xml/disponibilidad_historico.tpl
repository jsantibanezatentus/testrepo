<anychart>
	<margin all="0"/>
	<charts>
	
		<!-- GRAFICO DE DISPONIBILIDAD POR PASO -->
		<chart plot_type="CategorizedBySeriesVertical">

			<!-- FORMATO DEL GRAFICO -->
			<chart_settings>
				<chart_background enabled="False">
					<inside_margin all="0"/>
				</chart_background>
				<title enabled="False" />
				<axes>
					<y_axis>
						<title>
							<text>Disponibilidad (%)</text>
							<font size="10" color="#5A5A5A"></font>
						</title>
						<scale mode="Stacked" minimum="{__scale_minimum}" maximum="100"/>
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
						<labels rotation = "75">
							<font size="9"/>
						</labels>
						<title enabled="False" />
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
				<bar_series>
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
				<!-- BEGIN SERIES_ELEMENT -->
				<series name="{__series_name}">
					<!-- BEGIN POINT_ELEMENT -->
					<point name="{__point_name}" y="{__point_value}" color="#{__point_color}" style="solido"/>
					<!-- END POINT_ELEMENT -->
					<tooltip>
						<format>
{__series_name}
<!-- BEGIN SERIES_TOOLTIP -->
{__point_name}: {__point_value}%
<!-- END SERIES_TOOLTIP -->
						</format>
					</tooltip>
				</series>
				<!-- END SERIES_ELEMENT -->
			</data>
		</chart>

	</charts>
</anychart>