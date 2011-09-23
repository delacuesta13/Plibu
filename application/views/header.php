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
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="iso-8859-1">
		<title>Plataforma de Inscripciones de Bienestar Universitario</title>
		
		<!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->	
		
		<!-- ficheros css -->
		<?php echo $html->includecss('bootstrap.min');?>	
		<?php echo $html->includeCss('custom');?>
			
		<!-- include de ficheros css -->
		<?php 
		/*
		 * Códigos CSS propios de cada vista. Es necesario declarar
		 * la variable (en la función del controlador) como makecss
		 * e ingresar los css en ésta como un array.
		 * ej:
		 * $this->set('makecss',array('elemento_1','elemento_n'));
		 * */	
		if (isset($makecss) && is_array($makecss) && count($makecss)>0) {
			foreach ($makecss as $printcss) {			
				echo "\t\t" . $html->includeCss($printcss) . "\n";			
			} /* foreach */
			unset ($printcss);
		} /* if */
		?>		
        	
		<!-- código css -->	
		<?php if(isset($make_tag_css)) echo $html->css_tag($make_tag_css);?>
			
		<!-- javascript -->	
		<?php echo $html->javascript_tag("\n\t\tvar url_project = \"". BASE_PATH ."/\";");?>
			
		<!-- ficheros javascript -->
		<?php echo $html->includeJs('jquery-1.6.4.min');?>	
		<?php echo $html->includeJs('bootstrap/bootstrap-dropdown');?>
		<?php echo $html->includeJs('bootstrap/bootstrap-alerts');?>
			
		<!-- include de ficheros javascript -->	
		<?php 
		if (isset($makejs) && is_array($makejs) && count($makejs)>0) {
			foreach($makejs as $printjs){			
				echo "\t\t" . $html->includeJs($printjs) . "\n";			
			} /* foreach */
			unset ($printjs);		 
		} /* if */	
    	?>
    		
		<!-- código javascript -->	
		<?php if(isset($make_tag_js)) echo $html->javascript_tag($make_tag_js);?>
		
		<!-- ícono -->
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo BASE_PATH;?>/img/icons/favicon.ico"/>
				    	
	</head>
	
	<body>
	
		<!-- Topbar -->
		<div class="topbar">
			<div class="fill">
				<div class="container">	
					<?php include 'topbar.php';?>	
				</div>
			</div>
		</div>
		
		<div class="container">
		
			<section>			