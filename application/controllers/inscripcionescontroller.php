<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class InscripcionesController extends VanillaController {
	
	function beforeAction () {
		session_start();
	}
	
	function index () {
		
	}
	
	/*
	 * mostrar link de inscripción,
	 * según el id del curso.
	 * Activar y desactivar el link,
	 * si está inscrito el usuario de la 
	 * sesión o no ha iniciado sesión.
	 */
	function getInscripcionCurso ($idCurso = null) {
		
		$listaMeses = array('Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		
		## recibo un id de un curso
		if (isset($idCurso) && preg_match('/^[0-9]{1,}$/', $idCurso)) {
			
			$dataCurso = performAction('actividades', 'consultar_curso', array($idCurso));
		
			$strSalida = '<script type="text/JavaScript">
			$( "a[rel=twipsy]" )
				.twipsy({
					live: true,
					placement: "right"
				});
			$( "a[rel=popover]" )
				.popover({
					offset: 10,
					html: true
				})
				.click(function(e) {
					e.preventDefault()
				});
			</script>';
			
			## revisar que se el usuario haya iniciado sesión
			if (array_key_exists('logueado', $_SESSION) && $_SESSION['logueado'] && count($dataCurso)!=0) {
				
				## obtengo el período actual.
				$periodoActual = performAction('actividades', 'periodo_actual', array());
			
				/*
				 * sólo se habilitarán las inscripciones, mientras
				 * que la fecha de la visita del usuario esté dentro
				 * del rango de las fechas del período (es decir, el 
				 * período actual) al que pertenece el curso. 
				 */
				$dataPeriodo = performAction('actividades', 'consultar_periodo', array($periodoActual[0]['Periodo']['id']));
				$fechaActual = strtotime(date('Y-m-d'));
				$plazoInscripcion = false;
				if (count($dataPeriodo)!=0 && strtotime($dataPeriodo[0]['Periodo']['fecha_inic']) <= $fechaActual  && $fechaActual <= strtotime($dataPeriodo[0]['Periodo']['fecha_fin'])) {
					$plazoInscripcion = true;
				}				
			
				## obtengo el perfil del usuario en el período actual
				$dataPerfil = performAction('personas', 'consultar_perfil', array($_SESSION['persona_dni'], $periodoActual[0]['Periodo']['id']));
			
				## revisar si la persona ya está inscrita en el curso
				$inscrito = $this->Inscripcion->query('select * from inscripciones where persona_dni = \'' . $_SESSION['persona_dni'] . 
				'\' and curso_id = \'' . mysql_real_escape_string($idCurso) . '\'');
				$inscrito = (is_array($inscrito) && count($inscrito)!=0) ? true : false;
				 
				## Si la persona no tiene un perfil, no se puede inscribir.
				if (count($dataPerfil)==0) {
					$strSalida .= '
					<a class="btn danger disabled" href="javaScript:void(0);" rel="popover" title="Inscripción" 
					data-content="Es necesario que tengas un perfil en el período <i>' . $periodoActual[0]['Periodo']['periodo'] . 
					'</i> para que puedas inscribirte en <strong>' . 
					$dataCurso[0]['Actividad']['nombre'] . '</strong>.">
						Incribirme
					</a>
					';		
				} elseif ($inscrito) {
					## la persona ya está inscrita
					$strSalida .= '
					<a class="btn success disabled" href="javaScript:void(0);" rel="popover" title="Inscripción" 
					data-content="Ya estás inscrito en <strong>' . $dataCurso[0]['Actividad']['nombre'] . '</strong>."> 
						Incribirme
					</a>
					';				
				} elseif (!$plazoInscripcion) {
					$strSalida .= '
					<a class="btn danger disabled" href="javaScript:void(0);" rel="popover" title="Inscripción" 
					data-content="Las inscripciones no están habilitadas. El período de inscripciones inicia <strong>'.
					substr($dataPeriodo[0]['Periodo']['fecha_inic'], 8, 2) . ' ' . 
						$listaMeses[intval(substr($dataPeriodo[0]['Periodo']['fecha_inic'], 5, 2)) - 1] . ' ' .
							substr($dataPeriodo[0]['Periodo']['fecha_inic'], 0, 4) .
					'</strong> y finaliza <strong>' .
					substr($dataPeriodo[0]['Periodo']['fecha_fin'], 8, 2) . ' ' . 
						$listaMeses[intval(substr($dataPeriodo[0]['Periodo']['fecha_fin'], 5, 2)) - 1] . ' ' .
							substr($dataPeriodo[0]['Periodo']['fecha_fin'], 0, 4) .
					'</strong>."> 
						Incribirme
					</a>
					';	
				} else {
					## la persona puede inscribirse
					$strSalida .= '
					<a class="btn primary" href="javaScript:void(0);" onclick="inscripcionCurso(' . $idCurso . ')" rel="twipsy" title="Inscribirme!"> 
						Incribirme
					</a>
					';
				}				 
				
			} elseif(count($dataCurso)!=0) {
				$strSalida .= '
				<a class="btn primary disabled" href="javaScript:void(0);" rel="popover" title="Inscripción" 
				data-content="Es necesario que te identifiques para que puedas inscribirte en <strong>' . 
				$dataCurso[0]['Actividad']['nombre'] . '</strong>.">
					Incribirme
				</a>
				';				
			} /* elseif */
			
			echo $strSalida;
		}
		
		/****************************************************/
		
		## Función de respuesta ajax
		$this->doNotRenderHeader = 1;
	
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	function afterAction () {
		
	}
	
}