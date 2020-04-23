<anychart>
	<margin all="0"/>
	
	<charts>
		<settings>
			<locale>
				<date_time_format>
					<format>%MM.%dd.%yyyy.%HH.%mm.%ss</format>
				</date_time_format>
			</locale>
		</settings>
		
		<!-- GRAFICO DE RENDIMIENTO POR PASO -->
		<chart plot_type="Scatter">
		
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
						<axis_markers>
							<lines>
							    <!-- BEGIN TIENE_SLA_OK -->
								<line value="{__sla_ok_value}" opacity="0.6" color="#54a51c" thickness="2" display_under_data="true"/>
								<line value="{__sla_ok_value}" opacity="0" color="#54a51c" thickness="0" display_under_data="false">
									<label enabled='True' position="near" padding="2" multi_line_align="center">
										<font color="#54a51c" bold="True" size="8"/>
										<format>{__sla_ok_value}</format>
										<background enabled="true">
											<border opacity="0.6"/>
											<fill opacity="0.5"/>
											<corners type="rounded" all="5"/>
										</background>
									</label>
								</line>
								<!-- END TIENE_SLA_OK -->
								<!-- BEGIN TIENE_SLA_ERROR -->
								<line value="{__sla_error_value}" opacity="0.6" color="#d22129" thickness="2" display_under_data="true"/>
								<line value="{__sla_error_value}" opacity="0" color="#d22129" thickness="0" display_under_data="false">
									<label enabled='True' position="far" padding="2" multi_line_align="center">
										<font color="#d22129" bold="True" size="8"/>
										<format>{__sla_error_value}</format>
										<background enabled="true">
											<border opacity="0.6"/>
											<fill opacity="0.5"/>
											<corners type="rounded" all="5"/>
										</background>
									</label>
								</line>
								<!-- END TIENE_SLA_ERROR -->
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
								<!-- BEGIN RANGE_ELEMENT -->
								<range minimum="{__range_minimum}" maximum="{__range_maximum}" display_under_data="False">
									<minimum_line enabled='False'/>
									<maximum_line enabled='False'/> 
									<fill enabled="True" color="#54a51c" opacity="0.1"/>
									<label enabled='False'/>
								</range>
								<!-- END RANGE_ELEMENT -->
							</ranges>
						</axis_markers>
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
				<controls>
					<legend enabled="True" ignore_auto_item="true" position="right" align="near" align_by="dataplot" columns="1" rows_padding="2" height="100" width="120" horizontal_padding="0">
						<background enabled="True">
							<fill enabled="False" />
							<border enabled="true" type="Solid" color="#b3b1b2"/>
						</background>
						<title enabled="true">
							<text>Leyenda</text>
						</title>
						<margin top="5"/>
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
								<icon color="#dae7d2" series_type="bar"></icon>
								<format>{%Icon} Horario Hábil</format>
							</item>
						</items>
					</legend>
				</controls>
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
				<!-- BEGIN SERIES_ELEMENT -->
				<series type="Line" id="{__series_id}" name="{__series_name}" color="#{__series_color}" style="solido">
					<!-- BEGIN POINT_ELEMENT -->
					<point x="{__point_name}" y="{__point_value}"/>
					<!-- END POINT_ELEMENT -->
				</series>
				<!-- END SERIES_ELEMENT -->
			</data>
		</chart>
		
	</charts>
</anychart>