<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$listaMeses = array('Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');

?>

<table class="zebra-striped">
	<thead>
		<tr>
		<?php
		foreach ($fieldsTable as $field => $def) {
			if ($def['params']['showTable'] && $def['params']['sort']) {
				## orden del direccionamiento
				$orderDir = ((strtolower($sort)==strtolower($field)) ? 
					((strtolower($order)=='asc') ? 
						('desc') : 
						('asc')) : 
					('asc'));
				$extraClass = ($orderDir=='asc') ? 'headerSortUp' : 'headerSortDown'; 
				## ordenar por este campo;
				$orderField = (strtolower($sort)==strtolower($field)) ? true : false;
			?>
			<th class="<?php echo $def['color']?> header<?php echo ($orderField) ? (' ' . $extraClass) : ''?>"
			onclick="loadDataTable (1, '<?php echo $field?>', '<?php echo $orderDir?>')">
			<?php echo $def['text']?>
			</th>
			<?php	
			} elseif ($def['params']['showTable']) {
			?>
			<th><?php echo $def['text']?></th>
			<?php	
			} /* elseif */
		} /* foreach */ 
		unset ($field, $def);
		?>
			<th>Opciones</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		
		for ($i = 0; $i < count($data_query); $i++) {
			?>
			<tr>
				<td><?php echo $data_query[$i]['Actividad']['nombre']?></td>			
				<td><?php echo $data_query[$i]['Area']['nombre']?></td>
				<td>
					<?php
					$fechaInscripcion = $data_query[$i]['Inscripcion']['fecha_inscripcion'];
					echo substr($fechaInscripcion, 8, 2) . ' ' .
						$listaMeses[intval(substr($fechaInscripcion, 5, 2)) - 1] . ' ' .
							substr($fechaInscripcion, 0, 4) . ' ' .
								substr($fechaInscripcion, 11, 5);
					?>
				</td>
				<td>
					<?php
					if ($data_query[$i]['Curso']['abierto']=='1') {
						## nombre de la actividad en formato url
						$urlActividad = performAction('actividades', 'getNombreUrl', array($data_query[$i]['Curso']['id']));
						echo $html->link('ver', 'actividades/ver/' . $data_query[$i]['Curso']['id'] . '/' . $urlActividad);
					} /* if -> curso abierto */
					?>
				</td>		
			</tr>
			<?php
		} /* for */
		
		## no se encontraron registros
		if (count($data_query)==0) {
			?>
			<tr>
				<td colspan="4" style="text-align: center;">Vaya! No se encontraron registros.</td>
			</tr>
			<?php
		} /* if */
		
		?>
	</tbody>
</table>

<?php 

/**
 * Paginación ;)
 */

if (count($itemsNavigation)!=0) {
	?>
	<div class="pagination">
		<ul>
	<?php
	for ($i = 0; $i < count($itemsNavigation); $i++) {
		$link = 'onclick="loadDataTable(' . $itemsNavigation[$i]['link'] . ', \'' . $sort . '\', \'' . $order . '\')"';
		if ($itemsNavigation[$i]['prev']) {
			?>
			<li class="prev<?php echo ($itemsNavigation[$i]['disabled']) ? (' disabled') : '';?>">
				<a href="javascript:void(0);"<?php echo (!$itemsNavigation[$i]['disabled']) ? (' ' . $link) : ''?>>
					<?php echo $itemsNavigation[$i]['text']?>
				</a>
			</li>
			<?php
		} elseif ($itemsNavigation[$i]['next']) {
			?>
			<li class="next<?php echo ($itemsNavigation[$i]['disabled']) ? (' disabled') : '';?>">
				<a href="javascript:void(0);"<?php echo (!$itemsNavigation[$i]['disabled']) ? (' ' . $link) : ''?>>
					<?php echo $itemsNavigation[$i]['text']?>
				</a>
			</li>
			<?php
		} elseif ($itemsNavigation[$i]['active']) {
			?>
			<li class="active">
				<a href="javascript:void(0);">
					<?php echo $itemsNavigation[$i]['text']?>
				</a>
			</li>
			<?php
		} else {
			?>
			<li>
				<a href="javascript:void(0);" <?php echo $link?>>
					<?php echo $itemsNavigation[$i]['text']?>
				</a>
			</li>
			<?php
		} /* else */
	} /* for */
	?>
		</ul>
	</div>
	<?php
} /* if */

?>