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
	<h1>Identificarme</h1>
</div>

<div class="row">
	<div class="span4">
		<p>
			No tienes una cuenta? Acércate a la oficina de
			<span class="label success">Bienestar Universitario</span>
			y regístrate.
		</p>
	</div> <!-- /span4 -->
	<div class="span12">
		<?php
		/*
		 * Alertas:
		 * 
		 * $showAlert['block'] -> boolean
		 * 
		 * Si se define y su valor es true, se mostrará
		 * un bloque de texto como mensaje. Para lo
		 * cual, el mensaje debe ser definido con sus
		 * respectivas etiquetas html (<p> por ejemplo).
		 * 
		 * Si no se define o su valor es false, sólo se ingresa
		 * el texto, y se le adicionan los tags html para decorar
		 * la única línea de texto que se mostrará.
		 */ 
		## mostrar alerta
		if (isset($showAlert) && is_array($showAlert)) {
		?>
		<div class="alert-message <?php echo ((array_key_exists('block', $showAlert) && $showAlert['block']) ? 'block-message ' : '') . $showAlert['type'];?>" 
		data-alert="alert">
			<a class="close" href="#">&times;</a>
			<?php
			echo (array_key_exists('block', $showAlert) && $showAlert['block']) ? 
			($showAlert['message']) : 
			('<p>' . $showAlert['message'] . '</p>'); 
			?>
      	</div>
		<?php	
		} /* if */
		?>
		<form method="post" name="formulario" id="formulario" action="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . $this->_action?>">
			<fieldset>
				<legend>Ingresar al sistema</legend>
				<?php
				$identificacion_error = (isset($ind_error) && array_key_exists('identificacion', $ind_error)) ? true : false;
				?>
				<div class="clearfix<?php echo ($identificacion_error) ? ' error' : '';?>">
					<label for="identificacion">Identificación</label>
					<div class="input">
						<input class="xlarge<?php echo ($identificacion_error) ? ' error' : '';?>"
						id="identificacion" name="identificacion" size="30" type="text" autocomplete="off"
						<?php echo (isset($_POST['identificacion'])) ? ('value="' . $_POST['identificacion'] . '"') : '';?>
						/>
						<?php echo ($identificacion_error) ? ('<span class="help-inline">' . $ind_error['identificacion'] . '</span>') : '';?>
					</div>
				</div><!-- /clearfix -->
				<div class="actions">
					<input type="submit" class="btn primary" value="Ingresar">
				</div>
			</fieldset>
		</form>
	</div><!-- /span12 -->
</div><!-- /row -->