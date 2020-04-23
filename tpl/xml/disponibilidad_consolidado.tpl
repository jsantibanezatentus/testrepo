<anygantt>

	<!-- FORMATO GENERAL -->
	<margin all="0"/>
	<settings>
		<inner_margin all="1"/>
		<outer_margin all="0"/>
		<locale>
			<date_time_format week_starts_from_monday="True">
				<months>
					<names>Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre</names>
					<short_names>Ene,Feb,Mar,Abr,May,Jun,Jul,Ago,Sep,Oct,Nov,Dic</short_names>
				</months>
				<week_days>
					<names>Domingo,Lunes,Martes,Miercoles,Jueves,Viernes,Sabado</names>
					<short_names>Do,Lu,Ma,Mi,Ju,Vi,Sa</short_names>
				</week_days>
			</date_time_format>
		</locale>
		<background enabled="false"/>
		<title enabled="false"/>
	</settings>
	
	<!-- FORMATO LINEA DE TIEMPO -->
	<timeline>
	    <plot line_height="28" item_height="18" item_padding="5">
	    	<!-- BEGIN TIENE_HORARIO_HABIL -->
<!--  			<current_time show="false" mode="Custom" /> -->
  			<non_working_days show="true" >
				<fill enabled="true" type="solid" color="#ffffff" opacity="1"/>
			</non_working_days>
			<non_working_hours show="true">
				<fill enabled="true" type="Solid" color="#ffffff" opacity="1"/>
			</non_working_hours>
    		<background>
				<fill enabled="true" type="solid" color="#54a51c" opacity="0.1"/>
			</background>
  			<grid>
				<horizontal>
 					<even>
						<fill enabled="false"/>
					</even>
 					<odd>
						<fill enabled="false"/>
					</odd>
				</horizontal>
			</grid>
			<!-- END TIENE_HORARIO_HABIL -->
		</plot>
		<scale start="{__scale_start}" end="{__scale_end}" lines="3" padding_unit="Percent" left="0.0" right="-0.01">
			<levels>
				<level height="20">
  					<style>
						<vertical_separator enabled="true" color="#ccb48b"/>
						<horizontal_separator enabled="true" color="#ccb48b"/>
    					<tile>
      						<fill enabled="true" type="Solid" color="#f0ede8"/>
    					</tile>
  					</style>
				</level>
				<level height="20">
  					<style>
						<vertical_separator enabled="true" color="#ccb48b"/>
						<horizontal_separator enabled="true" color="#ccb48b"/>
    					<tile>
      						<fill enabled="true" type="Solid" color="#f0ede8"/>
    					</tile>
  					</style>
				</level>
				<level height="20">
  					<style>
						<vertical_separator enabled="true" color="#ccb48b"/>
						<horizontal_separator enabled="true" color="#ccb48b"/>
    					<tile>
      						<fill enabled="true" type="Solid" color="#f0ede8"/>
    					</tile>
  					</style>
				</level>
			</levels>
   			<patterns>
 				<minutes>
 					<pattern is_lower="true">%mm min</pattern>
					<pattern>%mm</pattern>
					<pattern>%mm min</pattern>
				</minutes>
 				<hours>
					<pattern is_lower="true">%HH:%mm</pattern>
					<pattern>%HH:%mm</pattern>
					<pattern>%HH:%mm hrs</pattern>
				</hours>
   				<days>
					<pattern is_lower="true">%dd</pattern>
					<pattern>%dd %MMMM %yyyy</pattern>
					<pattern>%dddd %dd %MMMM %yyyy</pattern>
				</days>
   				<weeks>
   					<pattern is_lower="true">Semana del %dd %MMMM</pattern>
					<pattern>Semana del %dd</pattern>
					<pattern>Semana del %dd %MMMM</pattern>
					<pattern>Semana del %dd %MMMM %yyyy</pattern>
				</weeks>
   				<months>
   					<pattern is_lower="true">%MMMM</pattern>
					<pattern>%MMMM %yyyy</pattern>
				</months>
   				<quarters>
   					<pattern is_lower="true">%q Trimestre</pattern>
   					<pattern>%q Trimestre</pattern>
				</quarters>
			</patterns>
			<intervals>
				<interval type="Day" interval="1"/>
				<interval type="Week" interval="1"/>
				<interval type="Month" interval="1"/>
			</intervals>
		</scale>
		<calendar>
			<exceptions>
				<!-- BEGIN EXCEPTION_ELEMENT -->
				<exception start_date="{__exception_start_date}" end_date="{__exception_end_date}" is_working="true">
					<work from="{__work_from}" to="{__work_to}" />
				</exception>
				<!-- END EXCEPTION_ELEMENT -->
			</exceptions>
		</calendar>
	</timeline>
	
	<!-- FORMATO GRILLA CON OBJETIVO -->
	<datagrid enabled="true" width="150">
		<columns>
			<column width="150" cell_align="LeftLevelPadding">
				<header>
					<text>Objetivo</text>
	 				<cell>
						<fill enabled="true" type="Solid" color="#f0ede8" />
					</cell>
				</header>
				<format>{%Name}</format>
				<cell>
					<states>
						<selected_normal>
							<fill enabled="true" type="Solid" color="#f7af72" opacity="0.6"/>
						</selected_normal>
						<selected_hover>
							<fill enabled="true" type="Solid" color="#f7af72" opacity="0.6"/>
						</selected_hover>
						<hover>
							<fill enabled="true" type="Solid" color="#f7af72" opacity="0.2"/>
						</hover>
					</states>
				</cell>
			</column>
		</columns>
	</datagrid>
	
	<!-- ESTILOS -->
	<styles>
		<resource_styles>
			<resource_style name="consolidado">
				<row_datagrid>
        			<cell>
          				<fill enabled="true" type="Solid" color="#a2a2a2" opacity="0.2" />
        			</cell>
				</row_datagrid>
				<row>
					<fill enabled="true" type="Solid" color="#a2a2a2" opacity="0.2" />
				</row>        			
			</resource_style>
		</resource_styles>
		<defaults>
			<resource>
				<resource_style>
					<row_datagrid>
						<tooltip enabled="False"/>
					</row_datagrid>
 					<row>
 						<fill enabled="false"/>
						<states>
							<selected_normal>
								<fill enabled="true" type="Solid" color="#f7af72" opacity="0.6"/>
							</selected_normal>
							<selected_hover>
								<fill enabled="true" type="Solid" color="#f7af72" opacity="0.6"/>
							</selected_hover>
							<hover>
								<fill enabled="true" type="Solid" color="#f7af72" opacity="0.2"/>
							</hover>
						</states>
					</row>
				</resource_style>
			</resource>
		</defaults>
		<tooltip_styles>
			<tooltip_style name="default">
				<text>
{%Name}
Inicio: {%PeriodStart}{dateTimeFormat:%d de %MMMM del %yyyy %HH:%mm:%ss hrs}
Termino: {%PeriodEnd}{dateTimeFormat:%d de %MMMM del %yyyy %HH:%mm:%ss hrs}
				</text>
			</tooltip_style>
		</tooltip_styles>
		<period_styles>
			<period_style name="Uptime Global">
				<tooltip enabled="True" tooltip_style="default"/>
				<bar_style>
					<middle>
						<fill enabled="true" type="Gradient" color="#54a51c"/>
						<border enabled="false"/>
					</middle>
				</bar_style>
			</period_style>
			<period_style name="Downtime Parcial">
				<tooltip enabled="True" tooltip_style="default"/>
				<bar_style>
					<middle>
						<fill enabled="true" type="Gradient" color="#fdc72e"/>
						<border enabled="false"/>
					</middle>
				</bar_style>
			</period_style>
			<period_style name="Downtime Global">
				<tooltip enabled="True" tooltip_style="default"/>
				<bar_style>
					<middle>
						<fill enabled="true" type="Gradient" color="#d22129"/>
						<border enabled="false"/>
					</middle>
				</bar_style>
			</period_style>
			<period_style name="No Monitoreo">
				<tooltip enabled="True" tooltip_style="default"/>
				<bar_style>
					<middle>
						<fill enabled="true" type="Gradient" coloe="#e0e0e0"/>
						<border enabled="false"/>
					</middle>
				</bar_style>
			</period_style>
		</period_styles>
	</styles>
	
	<!-- DATOS -->
	<resource_chart>
		<resources>
			<!-- BEGIN RESOURCE_ELEMENT -->
  			<resource name="{__resource_name}" id="{__resource_id}" parent="{__resource_parent}" expanded="{__resource_expanded}" {__resource_style}/>
			<!-- END RESOURCE_ELEMENT -->
		</resources>
		<periods>
			<!-- BEGIN PERIOD_ELEMENT -->
			<period resource_id="{__period_resource_id}" start="{__period_start}" end="{__period_end}" style="{__period_style}" >
			<!-- BEGIN BLOQUE_ACTION -->
				<actions>
					<action type="navigateToURL" url="javascript:cargarSubItem('contenedor_{__item_id}', 'subcontenedor_even_{__monitor_id}', '{__item_id}', '{__item_id_nuevo}', ['monitor_id', '{__monitor_id}', 'paso_id', '{__paso_id}', 'fecha_monitoreo', '{__fecha_monitoreo}', 'pagina', '{__pagina}'])" target="_self" />
				</actions>
			<!-- END BLOQUE_ACTION -->				
			</period>
			<!-- END PERIOD_ELEMENT -->
		</periods>
	</resource_chart>
</anygantt>