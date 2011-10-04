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
					<li><a href="#perfil">mi Perfil</a></li>
					<li class="active"><a href="#inscripciones">mis Inscripciones</a></li>
				</ul>
				<!-- content -->
				<div class="tab-content">
					<!-- perfil -->
					<div id="perfil">
						<div id="cargandoPerfil" style="display: none;">
							<p>
								Espere por favor, esta operación puede tardar algún tiempo.<br/>
								<?php echo $html->includeImg('ajax-loader.gif', 'Cargando')?>
							</p>
						</div>
						<div id="dynamicPerfil"></div>
					</div><!-- /perfil -->
					<!-- inscripciones -->
					<div class="active" id="inscripciones">
						<div class="row">
							<div class="span16">
								<form>
									<fieldset>
										<div class="row">
											<div class="span8">
												<div class="clearfix">
													<label for="regpag">Mostrar</label>
													<div class="input">
														<select class="medium" name="regpag" id="regpag">
															<option value="<?php echo PAGINATE_LIMIT?>"><?php echo PAGINATE_LIMIT?></option>
															<option value="20">20</option>
															<option value="50">50</option>
															<option value="100">100</option>
														</select>
														<span class="help-inline">registros por página</span>
													</div><!-- /input -->
												</div><!-- /clearfix -->
											</div><!-- /span8 -->
											<div class="span8">
												<div class="row">
													<div class="span6">
														<div class="clearfix">
															<input class="span6" id="search" name="search" size="30" type="search"/>
														</div><!-- /clearfix -->
													</div><!-- /span6 -->
													<div class="span2">	
														<a href="javascript:void(0);" class="btn primary" id="btn_search">Buscar</a>
													</div><!-- /span2 -->
												</div><!-- /row -->
											</div><!-- /span8 -->
										</div><!-- /row -->
									</fieldset>
								</form>
							</div><!-- /span16 -->
						</div><!-- /row -->
						<div class="row rowDynamicInsc">
							<div class="span16">
								<div id="cargandoInscripciones" style="display: none;">
									<p style="text-align: center;">
										<!-- Espere por favor, esta operación puede tardar algún tiempo.<br/> -->
										<?php echo $html->includeImg('ajax-loader.gif', 'Cargando')?>
									</p>
								</div>
								<div id="dynamicInscripciones"></div>
							</div><!-- /span16 -->
						</div><!-- /row -->
					</div><!-- /inscripciones -->
				</div>
			</div><!-- /span16 -->
		</div><!-- /row -->
		<!-- /tabs -->
	</div><!-- /span16 -->
</div><!-- /row -->