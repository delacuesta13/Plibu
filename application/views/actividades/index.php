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
	<h1>
		Actividades
		<?php echo (isset($periodo_actual)) ? ('<small>Período ' . $periodo_actual[0]['Periodo']['periodo'] . '</small>') : ''?>
	</h1>
</div>

<div class="row">
	<div class="span16">
	
		<?php 
		## mostrar alert
		if (isset($showMessage)) {
		?>
		
		<div class="alert-message <?php echo $showMessage['type']?> fade in" data-alert="alert">
			<a class="close" href="#">&times;</a>
			<p><?php echo $showMessage['message']?></p>
		</div>
		
		<?php	
		}
		?>
		
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
		
		<div class="row">
			<div id="modal-cargando" class="modal hide fade">
				<div class="modal-header">
					<h3>Cargando...</h3>
				</div>
				<div class="modal-body">
					<p>Espere por favor, esta operación puede tardar algún tiempo.</p>
				</div>
			</div>
			<!-- div donde cargo el ajax -->
			<div class="span16" id="dynamic">
			</div><!-- /span16 -->
		</div><!-- /row -->
		
	</div><!-- /span16 -->
</div><!-- /row -->