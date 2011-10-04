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

## el usuario tiene un perfil
if ($perfilPeriodoActual) {
	?>
	<div class="row">
		<div class="span5">
			<h3>Período</h3>
			<p><?php echo $dataPerfil[0]['Periodo']['periodo']?></p>
			<h3>Perfil</h3>
			<p><?php echo $dataPerfil[0]['Multientidad']['nombre']?></p>
		</div><!-- /span5-->
	<?php
	if (strtolower($dataPerfil[0]['Multientidad']['nombre'])=='estudiante') {
	?>
		<div class="span6">
			<h3>Facultad</h3>
			<p><?php echo $dataPerfil_complementaria[0]['Facultad']['nombre']?></p>
			<h3>Programa Académico</h3>
			<p><?php echo $dataPerfil_complementaria[0]['Programa']['nombre']?></p>
		</div><!-- /span6-->
		<div class="span5">
			<h3>Jornada</h3>
			<p><?php echo $dataPerfil_complementaria[0]['Multientidad']['jornada']?></p>
			<h3>Semestre</h3>
			<p><?php echo $dataPerfil_complementaria[0]['Perfil']['semestre']?></p>
		</div><!-- /span5-->
	<?php
	} elseif (strtolower($dataPerfil[0]['Multientidad']['nombre'])=='docente') {
	?>
		<div class="span6">
			<h3>Facultad</h3>
			<p><?php echo $dataPerfil_complementaria[0]['Facultad']['nombre']?></p>
			<h3>Programa Académico</h3>
			<p><?php echo $dataPerfil_complementaria[0]['Programa']['nombre']?></p>
		</div><!-- /span6-->
		<div class="span5">	
			<h3>Tipo de Contrato</h3>
			<p><?php echo $dataPerfil_complementaria[0]['Multientidad']['contrato']?></p>
		</div><!-- /span5-->
	<?php	
	} elseif (strtolower($dataPerfil[0]['Multientidad']['nombre'])=='egresado') {
	?>
		<div class="span6">
			<h3>Facultad</h3>
			<p><?php echo $dataPerfil_complementaria[0]['Facultad']['nombre']?></p>
			<h3>Programa Académico</h3>
			<p><?php echo $dataPerfil_complementaria[0]['Programa']['nombre']?></p>
		</div><!-- /span6-->
	<?php 	
	} elseif (strtolower($dataPerfil[0]['Multientidad']['nombre'])=='funcionario') {
	?>
		<div class="span6">
		<?php
		## pertenece a un programa
		if (count($dataPerfil_complementaria)!=0) {
		?>
			<h3>Facultad</h3>
			<p><?php echo $dataPerfil_complementaria[0]['Facultad']['nombre']?></p>
			<h3>Programa Académico</h3>
			<p><?php echo $dataPerfil_complementaria[0]['Programa']['nombre']?></p>
		<?php
		} else {
		## es funcionario administrativo
		?>
			<h3>Área</h3>
			<p>Administrativa</p>
		<?php	
		}
		?>
		</div><!-- /span6-->
	<?php 	
	} elseif (strtolower($dataPerfil[0]['Multientidad']['nombre'])=='familiar') {
	?>
		<div class="span6">
			<h3>Apoderado</h3>
			<p><?php echo $dataPerfil_complementaria[0]['Apoderado']['nombres'] . ' ' . $dataPerfil_complementaria[0]['Apoderado']['apellidos']?></p>
			<h3>Tipo de consanguinidad o Afinidad</h3>
			<p><?php echo $dataPerfil_complementaria[0]['Multientidad']['consanguinidad']?></p>
		</div><!-- /span6-->
	<?php	
	}
	?>
	</div><!-- /row -->
	<?php
} elseif (strtotime($periodoActual[0]['Periodo']['fecha_inic']) <= strtotime(date('Y-m-d')) && strtotime(date('Y-m-d')) <= strtotime($periodoActual[0]['Periodo']['fecha_fin'])) {
	?>
	<script type="text/javascript">
	$(function () {
		$("#perfilUsuario").change(function () {
			$.ajax(
				{
					url: url_project + "<?php echo strtolower($this->_controller) . '/dynamicForm'?>",
					type: "POST",
					dataType: "html",
					data: {idPerfil: $("#perfilUsuario").val()},
					beforeSend: function() {
						$("#dynamicForm").html('<?php echo $html->includeImg('ajax-loader.gif', 'Cargando');?>');
					},
					success: function( data ) {
						$("#dynamicForm").html(data);
					}
				}
			);		
		});
		$("#form").submit(function () {

			var url = url_project + "<?php echo strtolower($this->_controller) . '/crear_perfil'?>";

			var values = {};
		    $.each($('#form').serializeArray(), function(i, field) {
		        values[field.name] = field.value;
		    });

		    $.ajax(
				{
					url: url,
					type: "POST",
					dataType: "html",
					data: {"fields[]": values},
					beforeSend: function() {
						$("#procesaPerfil").html('<?php echo $html->includeImg('ajax-loader.gif', 'Cargando');?>');
					},
					success: function( data ) {
						$("#procesaPerfil").html(data);
					}
				}
			);
			
			return false;
		});
	});
	</script>
	<div class="row">
		<div class="span5">
			<h2>Perfil</h2>
			<p>
				<span class="label notice">Info</span> Es necesario que tengas un perfil en el período <strong>
				<?php echo $periodoActual[0]['Periodo']['periodo']?></strong>, para que puedas inscribirte en las 
				actividades programadas por <strong>Bienestar Universitario</strong>.
			</p>
		</div><!-- /span5-->
		<div class="span11">
			<div id="procesaPerfil"></div>
			<form id="form" class="form-stacked">
				<fieldset>
					<legend>Nuevo perfil</legend>
					<div id="inputPerfil" class="clearfix">
						<label for="perfilUsuario">Perfil</label>
						<div class="input">
							<select name="perfilUsuario" id="perfilUsuario">
								<option>Seleccione un perfil</option>
								<?php
								$strSelect = '';
								for ($i = 0; $i < count($listaPerfiles); $i++) {
									if (isset($perfilesDisabled) && is_array($perfilesDisabled) && in_array(strtolower($listaPerfiles[$i]['Multientidad']['nombre']), $perfilesDisabled)) {
										$strSelect .= '<option disabled="disabled">' . $listaPerfiles[$i]['Multientidad']['nombre'] . '</option>';
									} else {
										$strSelect .= '<option value="' . $listaPerfiles[$i]['Multientidad']['id'] . '">' . 
										$listaPerfiles[$i]['Multientidad']['nombre'] . '</option>';
									} /* else */
								} /* for */
								echo $strSelect;
								?>
							</select>
							<span class="help-inline"></span>
						</div>
					</div><!-- /clearfix -->
					<div id="dynamicForm"></div>
					<?php
					$strPerfiles = '';
					if (isset($perfilesDisabled) && is_array($perfilesDisabled) && count($perfilesDisabled)!=0) { 
						## recojo los perfiles deshabilitados
						for ($i = 0; $i < count($perfilesDisabled); $i++) {
							$strPerfiles .= ucfirst($perfilesDisabled[$i]) . ', ';
						}
						$strPerfiles = substr_replace($strPerfiles, '', -2);
						if (count($perfilesDisabled)>1) {
							$strPerfiles = preg_replace('/\, ' . $perfilesDisabled[count($perfilesDisabled) - 1] . '$/i', 
							' y ' . ucfirst($perfilesDisabled[count($perfilesDisabled) - 1]), $strPerfiles);
						} /* if */
						?>
						<div class="clearfix">
							<div class="input">
								<span class="help-block">
									Los siguientes perfiles no están habilitados para gestionarse a través de esta plataforma: <?php echo $strPerfiles?>.
									Si requiere gestionar alguno de los perfiles anteriores, por favor acérquese a la oficina de Bienestar Universitario. 
								</span>
							</div>
						</div><!-- /clearfix -->
						<?php
					} 
					?>
				</fieldset>
				<div class="actions">
					<button type="submit" class="btn primary">Guardar</button>
				</div>
			</form>
		</div><!-- /span11-->
	</div><!-- /row -->
	<?php
} else {
	?>
	<p>
		<span class="label notice">Info</span> Las inscripciones no están habilitadas.<br/>
		El período de inscripción inicia <strong>
		<?php
		echo substr($periodoActual[0]['Periodo']['fecha_inic'], 8, 2) . ' ' .
			$listaMeses[intval(substr($periodoActual[0]['Periodo']['fecha_inic'], 5, 2)) - 1] . ' ' .
				substr($periodoActual[0]['Periodo']['fecha_inic'], 0, 4);		                                       
		?>
		</strong> y finaliza <strong>
		<?php
		echo substr($periodoActual[0]['Periodo']['fecha_fin'], 8, 2) . ' ' .
			$listaMeses[intval(substr($periodoActual[0]['Periodo']['fecha_fin'], 5, 2)) - 1] . ' ' .
				substr($periodoActual[0]['Periodo']['fecha_fin'], 0, 4);		                                       
		?>
		</strong>.
	</p>
	<?php
} /* else */

?>