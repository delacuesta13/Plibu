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
$listaDias = array('Lunes' , 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');

?>

<div class="page-header">
	<h1><?php echo $dataCurso[0]['Actividad']['nombre']?> <small><?php echo $dataCurso[0]['Area']['nombre']?></small></h1>
</div>

<div class="row">
	<div class="span16">
		<div class="row">
			<div class="span16">
				<ul class="breadcrumb">
					<li><?php echo $html->link('Actividades', '')?> <span class="divider">/</span></li>
					<li>
						<?php 
						echo $html->link('Ver', strtolower($this->_controller) . '/' . strtolower($this->_action) . '/' . $idCurso . '/' . $actividadUrl);
						?>
						<span class="divider">/</span>
					</li>
					<li class="active"><?php echo $dataCurso[0]['Actividad']['nombre']?></li>
				</ul>
			</div><!-- /span16 -->
		</div><!-- /row -->
		<div class="row" style="margin-bottom:20px">
			<div class="span16">
				<div class="cargandoInscripcion" style="display: none; padding-left:5px"><?php echo $html->includeImg('ajax-loader.gif', 'Cargando')?></div>
				<div id="dynamicInscripcion"></div>
			</div><!-- /span16 -->
		</div><!-- /row -->
		<div class="row">
			<div class="span16">
				<!-- tabs -->
				<!-- nav -->
				<ul class="tabs" data-tabs="tabs">
					<li><a href="#info">Información</a></li>
					<li class="active"><a href="#horarios">Horarios</a></li>
				</ul>
				<!-- content -->
				<div id="my-tab-content" class="tab-content">
					<div id="info">
						<div class="row">
							<div class="span5">
								<h3>Actividad</h3>
								<p><?php echo $dataCurso[0]['Actividad']['nombre']?></p>
								<h3>Área</h3>
								<p><?php echo $dataCurso[0]['Area']['nombre']?></p>
								<h3>Período</h3>
								<p><?php echo $dataCurso[0]['Periodo']['periodo']?></p>
							</div><!-- /span5 -->
							<div class="span5">
								<h3>Monitor</h3>
								<?php
								$monitor = '<em>Por definir</em>';
								if (strlen($dataCurso[0]['Curso']['monitor_dni'])!=0) {
									$dataMonitor = performAction('personas', 'consultar_persona', array($dataCurso[0]['Curso']['monitor_dni']));
									$monitor = ((count($dataMonitor)!=0) ? 
										($dataMonitor[0]['Persona']['nombres'] . ' ' . $dataMonitor[0]['Persona']['apellidos']) : 
										($monitor));
								} /* if */ 
								?>
								<p><?php echo $monitor?></p>
								<h3>Fecha de Inicio</h3>
								<p>
								<?php
								echo ((strlen($dataCurso[0]['Curso']['fecha_inic'])!=0 && $dataCurso[0]['Curso']['fecha_inic']!='0000-00-00') ? 
									(
									substr($dataCurso[0]['Curso']['fecha_inic'], 8, 2) . ' ' .
										$listaMeses[intval(substr($dataCurso[0]['Curso']['fecha_inic'], 5, 2)) - 1] . ' ' .
											substr($dataCurso[0]['Curso']['fecha_inic'], 0, 4)
									) : 
									'<em>Por definir</em>'); 
								?>
								</p>
								<h3>Fecha de Finalización</h3>
								<p>
								<?php
								echo ((strlen($dataCurso[0]['Curso']['fecha_fin'])!=0 && $dataCurso[0]['Curso']['fecha_fin']!='0000-00-00') ? 
									(
									substr($dataCurso[0]['Curso']['fecha_fin'], 8, 2) . ' ' .
										$listaMeses[intval(substr($dataCurso[0]['Curso']['fecha_fin'], 5, 2)) - 1] . ' ' .
											substr($dataCurso[0]['Curso']['fecha_fin'], 0, 4)
									) : 
									'<em>Por definir</em>'); 
								?>
								</p>
							</div><!-- /span5 -->
							<div class="span6">
								<h3>Comentario</h3>
								<p>
								<?php
								echo (strlen(trim($dataCurso[0]['Curso']['comentario']))!=0) ? $dataCurso[0]['Curso']['comentario'] : '<em>Ninguno</em>'; 
								?>
								</p>
							</div><!-- /span6 -->
						</div><!-- /row -->
					</div>
					<div class="active" id="horarios">
						<table class="zebra-striped">
							<thead>
								<tr>
									<th>Día</th>
									<th>Lugar</th>
									<th>Hora Inicio</th>
									<th>Hora Finalización</th>
								</tr>
							</thead>
							<tbody>
							<?php 
							## no se han definido horarios
							if (count($listaHorarios)==0) {
								?>
								<tr>
									<td colspan="4" style="text-align:center">Vaya! No se encontraron registros.</td>
								</tr>
								<?php
							} else {
								
								$tableDatos = array();
								$temp = array();
								$direcciones = array();
								
								for ($i = 0; $i < count($listaHorarios); $i++) {
									$temp = array(
										'dia' => $listaHorarios[$i]['Horario']['dia'],
										'hora_inic' => substr($listaHorarios[$i]['Horario']['hora_inic'], 0, 5),
										'hora_fin' => substr($listaHorarios[$i]['Horario']['hora_fin'], 0, 5),
										'lugar' => $listaHorarios[$i]['Lugar']['nombre']
									);
									if (!array_key_exists($listaHorarios[$i]['Lugar']['nombre'], $direcciones)) {
										$direcciones[$listaHorarios[$i]['Lugar']['nombre']] = $listaHorarios[$i]['Lugar']['direccion'];
									}
									array_push($tableDatos, $temp);
								} /* for */
								
								$groupRows = array();
								$groupItems = array('dia', 'lugar');
								$strTemp = '';
								
								for ($i = 0; $i < count($groupItems); $i++) {
									for ($j = 0; $j < count($tableDatos); $j++) {
										$strTemp = $tableDatos[$j][$groupItems[$i]];
										$groupRows[$groupItems[$i]][$tableDatos[$j][$groupItems[$i]]] = 0; 
										for ($k = $j; $k < count($tableDatos); $k++) {
											if ($strTemp==$tableDatos[$k][$groupItems[$i]]) {
												$groupRows[$groupItems[$i]][$tableDatos[$j][$groupItems[$i]]] += 1;
											} else {
												break;
											}
										} /* k */
										$k--;
										$j = $k;
									} /* j */
								} /* i */
								
								$rowsSalida = array();
								foreach ($groupRows as $item => $elems) {
									$k = 0;
									foreach ($elems as $uniqueData => $numVeces) {
										$rowsSalida[$k][$item] = $uniqueData;
										$k += $numVeces;
									}
									unset ($uniqueData, $numVeces);
								}
								unset($item, $rows);
								
								$temp = array();
								for ($i = 0; $i < count($tableDatos); $i++) {
									for ($j = 0; $j < count($groupItems); $j++) {
										unset ($tableDatos[$i][$groupItems[$j]]);
									}
									$temp = $tableDatos[$i];
									foreach ($temp as $col => $value) {
										$rowsSalida[$i][$col] = $value;
									} 
									unset ($col, $value);
								}
								
								$temp = array();
								$strSalida = '';
								for ($i = 0; $i < count($rowsSalida); $i++) {
									$strSalida .= '<tr>';
									for ($j = 0; $j < count($groupItems); $j++) {
										if (array_key_exists($groupItems[$j], $rowsSalida[$i])) {
											if ($groupItems[$j]=='dia') {
												$strSalida .= '<td rowspan="' . $groupRows[$groupItems[$j]][$rowsSalida[$i][$groupItems[$j]]] . '"
												style="vertical-align: middle;">' .	
												$listaDias[intval($rowsSalida[$i][$groupItems[$j]]) - 1] . '</td>';
											} elseif ($groupItems[$j]=='lugar') {
												$strSalida .= '<td rowspan="' . $groupRows[$groupItems[$j]][$rowsSalida[$i][$groupItems[$j]]] . '"
												title="Lugar" data-content="<address>
												<strong>' . $rowsSalida[$i][$groupItems[$j]] . '</strong><br/>
												' . $direcciones[$rowsSalida[$i][$groupItems[$j]]] . '
												</address>"
												style="vertical-align: middle;">' .	$rowsSalida[$i][$groupItems[$j]] . '</td>';
											} else {
												$strSalida .= '<td rowspan="' . $groupRows[$groupItems[$j]][$rowsSalida[$i][$groupItems[$j]]] . '"
												style="vertical-align: middle;">' .	$rowsSalida[$i][$groupItems[$j]] . '</td>';
											}
											unset ($rowsSalida[$i][$groupItems[$j]]);
										}
									}
									$temp = $rowsSalida[$i];
									foreach ($temp as $col => $value) {
										$strSalida .= '<td style="border-left: 1px solid #DDD;">' . $value . '</td>';
									}
									unset ($col, $value);
									$strSalida .= '</tr>';
								}
								
								echo $strSalida;
								
							} /* else */
							?>
							</tbody>
						</table>
					</div>
				</div>
				<!-- /tabs -->
			</div><!-- /span16 -->
		</div><!-- /row -->
	</div><!-- /span16 -->
</div><!-- /row -->
