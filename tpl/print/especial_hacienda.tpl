<table width="1400">
	<div>
		<table width="100%" style="background-color:#f47001 ">
			<tr>
				<td  style="text-align: right; color: white;">Santiago, {__fecha}.</td>
			</tr>
			<tr style="height: 400px" />
			<tr>
				<td style="font-family: Calibri;font-size:xx-large; text-align: left; color: white;">{__titulo_principal}</div>
			</tr>
			<tr style="height: 50px" />
			<tr>
				<td><img src="../../img/header_blanco.png" width="160" height="40" align="right" /></td>
			</tr>

		</table >
	</div>
	<div class="pagebreak">
		<table width="100%">
			<div style="font-family: Calibri;font-size:x-large; text-align: left; font-weight: bold;">Presentacion</div>
			<hr color="black" size=3>

		</table>
		<table width="100%">

			{__titulo_presentacion}
			{__segunda_presentacion}
			<!-- BEGIN BLOQUE_NODOS -->
			{__nodo}
			<!-- END BLOQUE_NODOS -->
		</table>
	</div>
		{__tabla_consolidado}
			<!-- BEGIN BLOQUE_CONSOLIDADO -->
				{__tr_objetivo}
			<!-- END BLOQUE_CONSOLIDADO -->
			{__promedio}
		</table>
			<br>
		<table>
			<!-- BEGIN BLOQUE_DESCRIPCIONES -->
				{__descripciones}
			<!-- END BLOQUE_DESCRIPCIONES -->
		</table>
	</div>
	<div class="pagebreak">
		<table width="100%">
			<div style="font-family: Calibri;font-size:x-large; text-align: left; font-weight: bold;">Detalle de Servicios</div>
			<hr color="black" size=3>
			
		</table>
		<!-- BEGIN BLOQUE_OBJETIVO -->
		<div class="pagebreaklater">
			<table width="100%">
				<div style="font-family: Calibri; text-align: left; font-weight: bold;">{__fecha}</div>
				<tr style="text-align: center;">
					<td class="txtBlanco13b celdaTituloGris">Objetivo</td>
					<td class="txtBlanco13b celdaTituloGris">Nodo</td>
					<td class="txtBlanco13b celdaTituloGris">Pasos</td>
					<td class="txtBlanco12b celdaTituloNaranjo">Uptime</td>
					<td class="txtBlanco12b celdaTituloNaranjo">No Monitoreo</td>
					<td class="txtBlanco12b celdaTituloNaranjo">Downtime Global</td>
					<td class="txtBlanco12b celdaTituloNaranjo">Mantenimiento</td>
				</tr>
				
				{__objetivo}
					<!-- BEGIN BLOQUE_NODOS_NOMBRE -->
						{__nodo_nombre}
						<!-- BEGIN BLOQUE_PASOS -->
						{__paso_nombre}
								{__data}
						<!-- END BLOQUE_PASOS -->
					<!-- END BLOQUE_NODOS_NOMBRE -->
				
			</table>
		</div>
		<!-- END BLOQUE_OBJETIVO -->
	</div>
</table>
<style >
	.pagebreak { page-break-before: always; }
    .pagebreaklater {page-break-after: always;}
</style>