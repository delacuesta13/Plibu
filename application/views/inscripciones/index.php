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

<div class="page-header">
	<h1>Inscripciones <small>Período <?php echo $periodoActual[0]['Periodo']['periodo']?></small></h1>
</div>

<div class="row">
	<div class="span16">
		<!-- tabs -->
		<div class="row">
			<div class="span16">
				<!-- nav -->
				<ul class="tabs" data-tabs="tabs" >
					<li class="active"><a href="#perfil">mi Perfil</a></li>
					<li><a href="#inscripciones">mis Inscripciones</a></li>
				</ul>
				<!-- content -->
				<div class="tab-content">
					<!-- perfil -->
					<div class="active" id="perfil">
						<div id="cargandoPerfil" style="display: none;">
							<p>
								Espere por favor, esta operación puede tardar algún tiempo.<br/>
								<?php echo $html->includeImg('ajax-loader.gif', 'Cargando')?>
							</p>
						</div>
						<div id="dynamicPerfil"></div>
					</div><!-- /perfil -->
					<!-- inscripciones -->
					<div id="inscripciones">
						<div id="cargandoInscripciones" style="display: none;">
							<p>
								Espere por favor, esta operación puede tardar algún tiempo.<br/>
								<?php echo $html->includeImg('ajax-loader.gif', 'Cargando')?>
							</p>
						</div>
						<div id="dynamicInscripciones"></div>
					</div><!-- /inscripciones -->
				</div>
			</div><!-- /span16 -->
		</div><!-- /row -->
		<!-- /tabs -->
	</div><!-- /span16 -->
</div><!-- /row -->