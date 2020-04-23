<anychart>
	<margin all="0"/>
	<charts>
	
		<!-- GRAFICO DE FRECUENCIA -->
		<chart plot_type="Scatter">
			
			<!-- FORMATO DEL GRAFICO -->
			<chart_settings>
				<title enabled="False" />
				<chart_background enabled="False">
					<inside_margin all="0"/>
				</chart_background>
				<axes>
					<y_axis>
						<title>
							<text>Cantidad Mediciones</text>
							<font  size="10" color="#5A5A5A"/>
						</title>
						<labels>
							<font size="9"/>
							<format>{%Value}{numDecimals:0}</format>
						</labels>
						<scale type="Stacked" minimum="{__y_scale_minimum}" maximum="{__y_scale_maximum}"/>					
					</y_axis>
					<x_axis>
						<title>
							<text>Respuesta (Segs)</text>
							<font  size="10" color="#5A5A5A"/>
						</title>
						<labels>
							<font size="9"/>
							<format>{%Value}{numDecimals:2,decimalSeparator:,}</format>
						</labels>
					 	<scale type="Stacked" minimum="{__x_scale_minimum}" maximum="{__x_scale_maximum}"/>
						<!-- BEGIN TIENE_ZOOM -->
 						<zoom enabled="true" inside="false" start="{__x_scale_minimum}" end="{__x_scale_maximum}"/> 
 						<!-- END TIENE_ZOOM -->
						<axis_markers>
							<lines>
							    <!-- BEGIN TIENE_SLA_OK -->
								<line value="{__sla_ok_value}" opacity="0.6" color="#54a51c" thickness="2" display_under_data="true"/>
								<line value="{__sla_ok_value}" opacity="0" color="#54a51c" thickness="0" display_under_data="false">
									<label enabled='True' position="far" padding="2" multi_line_align="Center">
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
									<label enabled='True' position="far" padding="2" multi_line_align="Center">
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
					</x_axis>
				</axes>
				<legend enabled="True" ignore_auto_item="true" position="right" align="near" align_by="dataplot" columns="1" rows_padding="2" height="220" width="120" horizontal_padding="0">
 					<font size="9"/>
					<background enabled="True">
						<fill enabled="False"/>
						<border enabled="true" type="Solid" color="#b3b1b2"/>
					</background>
					<title enabled="true">
						<text>Pasos</text>
					</title>	  
					<items>
						<item source="series"/>
					</items>
				</legend>
				<controls>
					<legend enabled="True" ignore_auto_item="true" position="right" align="near" align_by="dataplot" columns="1" rows_padding="2" height="70" width="120" horizontal_padding="0">
						<font size="9"/>
						<margin top="5"/>
						<background enabled="True">
							<fill enabled="False"/>
							<border enabled="true" type="Solid" color="#b3b1b2"/>
						</background>
						<title enabled="true">
							<text>Leyenda</text>
						</title>
						<items>
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
					<!-- BEGIN TIENE_BOTONES_ZOOM -->
 					<label enabled="true" inside_dataplot="true" width="20"  text_align="center" position="fixed" anchor="RightTop" hailgn="center" horizontal_padding="20">
						<text> + </text>
						<font  size="10" color="#5A5A5A"/>
						<actions>
							<action type="zoom" x_start="{__x_scale_minimum}" x_end="{__x_scale_avg}"/>
						</actions>
						<background enabled="true">
							<corners type="Rounded" all="3"/>
						</background>
					</label>
					<label enabled="true" inside_dataplot="true" width="20" text_align="center" position="fixed" anchor="RightTop" hailgn="center">
						<text> - </text>
						<font  size="10" color="#5A5A5A"/>
						<actions>
							<action type="zoom" x_start="{__x_scale_minimum}" x_end="{__x_scale_maximum}"/>
						</actions>
						<background enabled="true">
							<corners type="Rounded" all="3"/>
						</background>
					</label>
					<!-- END TIENE_BOTONES_ZOOM -->
				</controls>
			</chart_settings>
			
			<!-- FORMATO DE DATOS -->
			<data_plot_settings>
				<line_series>
					<marker_settings enabled="{__marker}" marker_type="circle">
						<marker size="4" anchor="center"/>
					</marker_settings>				
					<tooltip_settings enabled="True">
						<font bold="False" size="9"/>
						<format>
{%SeriesName}
Con {%XValue}{numDecimals:2,decimalSeparator:,} segs de respuesta
{%YValue}{numDecimals:0,thousandSeparator:.} mediciones
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
				<series type="Spline" id="{__series_id}" name="{__series_name}" color="#{__series_color}" style="solido">
					<!-- BEGIN POINT_ELEMENT -->
					<point x="{__point_name}" y="{__point_value}"/>
					<!-- END POINT_ELEMENT -->
				</series>
				<!-- END SERIES_ELEMENT -->
			</data>
		</chart>
		
	</charts>
</anychart>