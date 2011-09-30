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
					<li class="active"><a href="#info">Información</a></li>
					<li><a href="#horarios">Horarios</a></li>
				</ul>
				<!-- content -->
				<div id="my-tab-content" class="tab-content">
					<div class="active" id="info">
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
					<div id="horarios">
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
								
								$horariosCurso = array();
								$temp = array();
								$direccionLugar = array();
								
								for ($i = 0; $i < count($listaHorarios); $i++) {
									$temp = array(
										'dia' => $listaHorarios[$i]['Horario']['dia'],
										'lugar' => $listaHorarios[$i]['Lugar']['nombre'],
										'hora_inic' => substr($listaHorarios[$i]['Horario']['hora_inic'], 0, 5),
										'hora_fin' => substr($listaHorarios[$i]['Horario']['hora_fin'], 0, 5)
									);
									array_push($horariosCurso, $temp);
									if (!array_key_exists($listaHorarios[$i]['Lugar']['nombre'], $direccionLugar)) {
										$direccionLugar[$listaHorarios[$i]['Lugar']['nombre']] = $listaHorarios[$i]['Lugar']['direccion'];
									}
								}
								
								$groupItems = array('dia', 'lugar', 'hora_inic', 'hora_fin');
								$strTemp = '';
								$strSalida = '';
								$countItem = 0;
								$temp = array();
								
								for ($i = 0; $i < count($horariosCurso); $i++) {
									
									$strSalida .= '<tr>';
									
									for ($j = 0; $j < count($groupItems); $j++) {
										if (array_key_exists($groupItems[$j], $horariosCurso[$i])) {
											## contar el número de veces que aparece el ítem de forma seguida
											$countItem = 0;
											$strTemp = $horariosCurso[$i][$groupItems[$j]];
											for ($k = $i; $k < count($horariosCurso); $k++) {
												if ($strTemp==$horariosCurso[$k][$groupItems[$j]]) {
													$countItem++;
													unset ($horariosCurso[$k][$groupItems[$j]]);
												} else {
													break;
												} /* else */
											} /* for k */
											if ($groupItems[$j]=='dia') {
												$strSalida .= '<td rowspan="' . $countItem . '" style="vertical-align: middle; border-left: 1px solid #DDD;">' .
												$listaDias[intval($strTemp) - 1]												                                                       
												. '</td>';
											} elseif ($groupItems[$j]=='lugar') {
												$strSalida .= '<td rowspan="' . $countItem . '" style="vertical-align: middle; border-left: 1px solid #DDD;" 
												title="Lugar" data-content="<address>
												<strong>' . $strTemp . '</strong><br/>
												' . $direccionLugar[$strTemp] . '
												</address>">' .
												$strTemp . '</td>';
											} else {
												$strSalida .= '<td rowspan="' . $countItem . '" style="vertical-align: middle; border-left: 1px solid #DDD;">' . 
												$strTemp . '</td>';
											} /* else */
										} /* if */
									} /* for j -> groupItems */
									
									foreach ($horariosCurso[$i] as $value) {
										$strSalida .= '<td style="border-left: 1px solid #DDD;">' . $value . '</td>';
									} /* foreach */
									unset ($value);
									
									$strSalida .= '</tr>';
									
								} /* for i */
								
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
		<div class="row" style="margin-top: 20px;">
			<!-- social -->
			<div class="span2">
				<?php
				$textTwitter = $dataCurso[0]['Actividad']['nombre'] . ' - Período ' . $dataCurso[0]['Periodo']['periodo'] . '.' .
				' Universidad Cooperativa de Colombia - Cali. vía Bienestar Universitario';
				?>
				<a href="https://twitter.com/share" class="twitter-share-button" data-text="<?php echo $textTwitter?>" data-count="none">Tweet</a>
			</div>
			<div class="span2"><g:plusone></g:plusone></div>
			<div class="span9">
				<div class="fb-like" data-send="false" data-width="450" data-show-faces="true"></div>
			</div>
		</div><!-- /row -->
	</div><!-- /span16 -->
</div><!-- /row -->

<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
  {lang: 'es'}
</script>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/es_ES/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>