<?php echo $start; ?>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tbody id="top" style="font-size: 8pt;">
		<tr>
			<td colspan="3">
				<table border="0" cellspacing="0" cellpadding="0" width="100%" class="cabeceraEncuesta">
					<tr>
						<td class="pregunta">Encuestador:</td>
						<td class="content"><?php echo $encuestador; ?></td>
						<td colspan="2"></td>
						<td class="pregunta">Folio:</td>
						<td class="content"><?php echo $items['P3']; ?></td>
					</tr>
					<tr>
						<td class="pregunta">Supervisor:</td>
						<td class="content"><?php echo $supervisor; ?></td>
						<td class="pregunta">Zona Encuestada:</td>
						<td class="content"><?php echo $zona; ?></td>
						<td class="pregunta">Sub Zona Encuestada:</td>
						<td class="content"><?php echo $subzona; ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
	<tbody id="encuesta" style="height: 350px; overflow-y: auto; overflow-x: hidden; font-size: 8pt;">
		<tr>
			<td colspan="3">¿El establecimiento vende bebidas no alcoholicas? <?php echo $items['P6']; ?></td>
		</tr>
		<tr>
			<td valign="top">
				<table border="0" cellspacing="2" cellpadding="2" width="100%">
					<tr>
						<td colspan="2" class="pregunta">Exterior:</td>
					</tr>
					<tr>
						<td>Fachada pintada COCA COLA</td>
						<td><?php echo $items['P33']; ?></td>
					</tr>
					<tr>
						<td>Toldo COCA COLA</td>
						<td><?php echo $items['P34']; ?></td>
					</tr>
					<tr>
						<td>Marquesina COCA COLA</td>
						<td><?php echo $items['P35']; ?></td>
					</tr>
					<tr>
						<td>Anuncio Luminoso COCA COLA</td>
						<td><?php echo $items['P36']; ?></td>
					</tr>
				</table>
			</td>
			<td></td>
			<td valign="top">
				<table border="0" cellspacing="2" cellpadding="2" width="100%">
					<tr>
						<td colspan="2" class="pregunta">Interior:</td>
					</tr>
					<tr>
						<td>Mesas COCA COLA</td>
						<td><?php echo $items['P37']; ?></td>
					</tr>
					<tr>
						<td>Sillas COCA COLA</td>
						<td><?php echo $items['P38']; ?></td>
					</tr>
					<tr>
						<td>Racks Garrafón CIEL/Victoria</td>
						<td><?php echo $items['P39']; ?></td>
					</tr>
					<tr>
						<td>Caseta</td>
						<td><?php echo $items['P40']; ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<table border="0" cellspacing="2" cellpadding="2" width="100%">
					<tr>
						<td colspan="2" class="pregunta">Cuentan con productos de la siguientes marcas:</td>
					</tr>
					<tr>
						<td>Pepsi</td>
						<td><?php echo $items['P41']; ?></td>
					</tr>
					<tr>
						<td>Bigcola</td>
						<td><?php echo $items['P42']; ?></td>
					</tr>
					<tr>
						<td>Bonafont</td>
						<td><?php echo $items['P68']; ?></td>
					</tr>
					<tr>
						<td>Agua diferente a Victoria / Ciel</td>
						<td><?php echo $items['P43']; ?></td>
					</tr>
					<tr>
						<td>Isotónicos diferentes a PowerAid</td>
						<td><?php echo $items['P44']; ?></td>
					</tr>
					<tr>
						<td>Jugos diferente a Del Valle</td>
						<td><?php echo $items['P45']; ?></td>
					</tr>
					<tr>
						<td>Leche diferente a Araceli</td>
						<td><?php echo $items['P46']; ?></td>
					</tr>
					<tr>
						<td>Refrescos COCA COLA</td>
						<td><?php echo $items['P47']; ?></td>
					</tr>
					<tr>
						<td>Agua Ciel / Victoria</td>
						<td><?php echo $items['P48']; ?></td>
					</tr>
					<tr>
						<td>Power Aid</td>
						<td><?php echo $items['P49']; ?></td>
					</tr>
					<tr>
						<td>Jugos Del Valle</td>
						<td><?php echo $items['P50']; ?></td>
					</tr>
					<tr>
						<td>Leche Araceli</td>
						<td><?php echo $items['P51']; ?></td>
					</tr>
				</table>
			</td>
			<td></td>
			<td valign="top">
				<table border="0" cellspacing="2" cellpadding="2" width="100%">
					<tr>
						<td colspan="2" class="pregunta">Tiene algún convenio con estos proveedores:</td>
					</tr>
					<tr>
						<td>Pepsi</td>
						<td><?php echo $items['P52']; ?></td>
					</tr>
					<tr>
						<td>Bigcola</td>
						<td><?php echo $items['P53']; ?></td>
					</tr>
					<tr>
						<td>Bonafont</td>
						<td><?php echo $items['P69']; ?></td>
					</tr>
					<tr>
						<td>Agua diferente a Victoria / Ciel</td>
						<td><?php echo $items['P54']; ?></td>
					</tr>
					<tr>
						<td>Isotónicos diferentes a PowerAid</td>
						<td><?php echo $items['P55']; ?></td>
					</tr>
					<tr>
						<td>Jugos diferente a Del Valle</td>
						<td><?php echo $items['P56']; ?></td>
					</tr>
					<tr>
						<td>Leche diferente a Araceli</td>
						<td><?php echo $items['P57']; ?></td>
					</tr>
					<tr>
						<td>Refrescos COCA COLA</td>
						<td><?php echo $items['P58']; ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<table width="100%" border="0" cellspacing="2" cellpadding="2">
					<tr>
						<td colspan="2" class="pregunta">Series</td>
					</tr>
					<tr>
						<td>Número de serie REFRIGERADOR 1</td>
						<td><?php echo $items['P59']; ?></td>
					</tr>
					<tr>
						<td>Modelo REFRIGERADOR 1</td>
						<td><?php echo $items['P60']; ?></td>
					</tr>
					<tr>
						<td>No. Código Barras REFRIGERADOR 1</td>
						<td><?php echo $items['P61']; ?></td>
					</tr>
					<tr>
						<td>Número de serie REFRIGERADOR 2</td>
						<td><?php echo $items['P62']; ?></td>
					</tr>
					<tr>
						<td>Modelo REFRIGERADOR 2</td>
						<td><?php echo $items['P63']; ?></td>
					</tr>
					<tr>
						<td>No. Código Barras REFRIGERADOR 2</td>
						<td><?php echo $items['P64']; ?></td>
					</tr>
					<tr>
						<td>Número de serie REFRIGERADOR 3</td>
						<td><?php echo $items['P65']; ?></td>
					</tr>
					<tr>
						<td>Modelo REFRIGERADOR 3</td>
						<td><?php echo $items['P66']; ?></td>
					</tr>
					<tr>
						<td>No. Código Barras REFRIGERADOR 3</td>
						<td><?php echo $items['P67']; ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3">
				<table width="100%" border="0" cellspacing="2" cellpadding="2">
					<tr>
						<td colspan="4" class="pregunta">Datos de la tienda y GPS</td>
					</tr>
					<tr>
						<td>Nombre del negocio</td>
						<td colspan="3"><?php echo $items['P10']; ?></td>
					</tr>
					<tr>
						<td>No. Código Barras ESTABLECIMIENTO</td>
						<td colspan="3"><?php echo $items['P11']; ?></td>
					</tr>
					<tr>
						<td>Nombre del propietario</td>
						<td colspan="3"><?php echo $items['P14']; ?></td>
					</tr>
					<tr>
						<td>Nombre del encargado/Gerente de negocio</td>
						<td colspan="3"><?php echo $items['P15']; ?></td>
					</tr>
					<tr>
						<td>Grupo de actividades del consumidor</td>
						<td colspan="3"><?php echo $items['P17']; ?></td>
					</tr>
					<tr>
						<td>Canal de Venta</td>
						<td colspan="3"><?php echo $items['P19']; ?></td>
					</tr>
					<tr>
						<td>Subcanal de Venta</td>
						<td colspan="3"><?php echo $items['P21']; ?></td>
					</tr>
					<tr>
						<td>Ciudad / Municipio</td>
						<td colspan="3"><?php echo $items['P22']; ?></td>
					</tr>
					<tr>
						<td>Estado</td>
						<td colspan="3"><?php echo $items['P23']; ?></td>
					</tr>
					<tr>
						<td>Colonia</td>
						<td colspan="3"><?php echo $items['P24']; ?></td>
					</tr>
					<tr>
						<td>Calle</td>
						<td colspan="3"><?php echo $items['P25']; ?></td>
					</tr>
					<tr>
						<td># Exterior</td>
						<td><?php echo $items['P26']; ?></td>
					</tr>
					<tr>
						<td># Interior</td>
						<td><?php echo $items['P27']; ?></td>
					</tr>
					<tr>
						<td>CP</td>
						<td><?php echo $items['P28']; ?></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>Telefono local (con Lada)</td>
						<td colspan="3">(<?php echo $items['P29']; ?>) - <?php echo $items['P30']; ?></td>
					</tr>
					<tr>
						<td>Telefono celular (con Lada)</td>
						<td colspan="3">(<?php echo $items['P31']; ?>) - <?php echo $items['P32']; ?></td>
					</tr>
					<tr>
						<td>Coordenadas GPS</td>
						<td>N: <?php echo $items['P12']; ?></td>
						<td>W: <?php echo $items['P13']; ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" align="center" class="footerEncuesta"><?php echo $items['submit']; ?></td>
		</tr>
	</tbody>
</table>
<?php echo $html; ?>
<?php echo $end; ?>
