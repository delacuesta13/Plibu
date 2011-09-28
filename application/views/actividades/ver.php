<?php
/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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
		<div class="row" style="margin-top">
			<div class="span16">
			</div><!-- /span16 -->
		</div><!-- /row -->
	</div><!-- /span16 -->
</div><!-- /row -->