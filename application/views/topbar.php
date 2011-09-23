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
					
<h3>
	<a class="brand" href="<?php echo BASE_PATH?>">Bienestar Universitario</a>
</h3>
<ul class="nav">
	<li<?php echo (strtolower($this->_controller)=='actividades') ? ' class="active"' : ''?>>
		<?php echo $html->link('Actividades', 'actividades')?>
	</li>
	<?php 
	/*
	 * si el usuario ha iniciado sesión,
	 * mostar el menú inscripciones.
	 */
	if (array_key_exists('logueado', $_SESSION) && $_SESSION['logueado']) {
	?>
	<li<?php echo (strtolower($this->_controller)=='inscripciones') ? ' class="active"' : ''?>>
		<?php echo $html->link('Inscripciones', 'inscripciones')?>
	</li>
	<?php 	
	}
	?>
</ul>
<ul class="nav secondary-nav">
	<?php 
	/*
	 * el usuario no ha iniciado sesión
	 */
	if ((!array_key_exists('logueado', $_SESSION) || !$_SESSION['logueado']) && (strtolower($this->_controller)!='personas' || strtolower($this->_action)!='login')) {
	?>
	<form method="post" name="formulario" id="formulario" class="pull-right" 
	action="<?php echo BASE_PATH . '/' . 'personas' . '/' . 'login'?>">
		<input type="text" name="identificacion" class="span3" placeholder="Identificación">
		<button type="submit" class="btn">Ingresar</button>
	</form>
	<?php 
	} elseif(array_key_exists('logueado', $_SESSION) && $_SESSION['logueado']) {
	?>
	<li style="padding: 10px 2px 11px">Identificado como</li>
	<li class="dropdown" data-dropdown="dropdown">
		<a href="#" class="dropdown-toggle" title="<?php echo $_SESSION['nombres'] . ' ' . $_SESSION['apellidos']?>">
			<?php echo $_SESSION['nombres']?>
		</a>
		<ul class="dropdown-menu">
			<li>
				<?php echo $html->link('Salir', 'personas/logout')?>
			</li>
		</ul>
	</li>
	<?php	
	} /* elseif */
	?>
</ul>