<?php

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
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
	 * inscribir al usuario de la sesi�n
	 * en un curso. Se debe de recibir
	 * idCurso (id del curso) por POST.
	 */
	function inscripcionCurso () {
		
		$listaMeses = array('Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$listaDias = array('Lunes', 'Martes', 'Mi�rcoles', 'Jueves', 'Viernes', 'S�bado', 'Domingo');
		
		echo '<script type="text/JavaScript">
		$("#closeModal").bind("click", function () {
			$("#modal-inscripcion").modal("hide");
		});
		</script>';
		
		if (isset($_POST['idCurso']) && preg_match('/^[0-9]{1,}$/', $_POST['idCurso'])) {
			
			$idCurso = $_POST['idCurso'];
			$dataCurso = performAction('actividades', 'consultar_curso', array($idCurso));
			$periodoActual = performAction('actividades', 'periodo_actual', array());
			
			## el curso existe y las inscripciones est�n abiertas, adem�s pertenece al per�odo actual
			if (count($dataCurso)!=0 && $dataCurso[0]['Curso']['abierto']=='1' && count($periodoActual)!=0 && $dataCurso[0]['Periodo']['id']==$periodoActual[0]['Periodo']['id']) {
				## el usuario debe haber iniciado sesi�n
				if (array_key_exists('logueado', $_SESSION) && $_SESSION['logueado']) {
				/****************************************************/
				
				## consultar el perfil del usuarios en el per�odo al cual pertenece el curso
				$dataPerfil = performAction('personas', 'consultar_perfil', array($_SESSION['persona_dni'], $dataCurso[0]['Periodo']['id']));
				## revisar si el plazo de inscripciones est� activo
				$fechaActual = strtotime(date('Y-m-d'));
				$plazoInscripcion = ((strtotime($periodoActual[0]['Periodo']['fecha_inic']) <= $fechaActual &&  $fechaActual <= strtotime($periodoActual[0]['Periodo']['fecha_fin'])) ? 
					(true) : 
					(false));
				## revisar si el usuario ya est� inscrito en el curso
				$sqlInscrito = 'select * from inscripciones where persona_dni = \'' . $_SESSION['persona_dni'] . '\' and curso_id = \'' . $idCurso . '\'';
				$inscrito = $this->Inscripcion->query($sqlInscrito);
				$inscrito = (is_array($inscrito) && count($inscrito)!=0) ? true : false;
				
				## la persona no tiene un perfil
				if (count($dataPerfil)==0) {
					echo 'Es necesario que tengas un perfil en el per�odo <i>' . $periodoActual[0]['Periodo']['periodo'] . '</i>
					para que puedas inscribirte en <strong>' . $dataCurso[0]['Actividad']['nombre'] . '</strong>.';
				} elseif ($inscrito) {
					## el usuario ya est� inscrito en el curso
					echo 'Ya est�s inscrito en <strong>' . $dataCurso[0]['Actividad']['nombre'] . '</strong>.';
				} elseif (!$plazoInscripcion) {
					echo 'Las inscripciones no est�n habilitadas. El per�odo de inscripci�n inicia <strong>' .
					substr($periodoActual[0]['Periodo']['fecha_inic'], 8, 2) . ' ' .
						$listaMeses[intval(substr($periodoActual[0]['Periodo']['fecha_inic'], 5, 2)) - 1] . ' ' .
							substr($periodoActual[0]['Periodo']['fecha_inic'], 0, 4)
					. '</strong> y finaliza <strong>'.
					substr($periodoActual[0]['Periodo']['fecha_fin'], 8, 2) . ' ' .
						$listaMeses[intval(substr($periodoActual[0]['Periodo']['fecha_fin'], 5, 2)) - 1] . ' ' .
							substr($periodoActual[0]['Periodo']['fecha_fin'], 0, 4)
					. '</strong>.';
				} else {
					
					## si es true se puede inscribir en el curso
					$inscripcionCurso = true;
					
					/*
					 * revisar si los horarios de los cursos en 
					 * los que ya est� inscrito se cruzan con los 
					 * horarios del curso a inscribirse.
					 */
					if (INSCRIPCIONES_CRUCEHRS) {
						
						## obtengo los horarios del curso en el cual se va a inscribir la persona
						$horariosCurso = performAction('actividades', 'listar_horarios', array($idCurso));
						
						## obtengo los horarios de los cursos en los que ya est� inscrita la persona
						$sqlHorarios = 'select actividad.nombre, horario.dia, horario.hora_inic, horario.hora_fin
						from actividades actividad, inscripciones inscripcion, cursos curso, horarios horario, periodos periodo
						where periodo.actual = \'1\' and periodo.id = curso.periodo_id and curso.id = inscripcion.curso_id
						and inscripcion.persona_dni = \'' . $_SESSION['persona_dni'] . '\' and curso.id = horario.curso_id
						and curso.actividad_id = actividad.id
						order by horario.dia asc, horario.hora_inic asc, horario.hora_fin asc, actividad.nombre asc';
						$horariosInscripciones = $this->Inscripcion->query($sqlHorarios);
						
						$strTemp = '';
						$finalizarRevision = false;
						
						for ($i = 0; $i < count($horariosCurso); $i++) {
							for ($j = 0; $j < count($horariosInscripciones); $j++) {
								## est�n en el mismo d�a los horarios
								if ($horariosCurso[$i]['Horario']['dia']==$horariosInscripciones[$j]['Horario']['dia']) {
									$strTemp = 'El horario de <strong>' . $dataCurso[0]['Actividad']['nombre'] . '</strong> del d�a ' . 
									$listaDias[intval($horariosCurso[$i]['Horario']['dia']) - 1] . 
									' (' . substr($horariosCurso[$i]['Horario']['hora_inic'], 0, 5) . ' - ' . substr($horariosCurso[$i]['Horario']['hora_fin'], 0, 5) . ')' .
									' se cruza con el de <strong>' . $horariosInscripciones[$j]['Actividad']['nombre'] . '</strong> del mismo d�a' .
									' (' . substr($horariosInscripciones[$j]['Horario']['hora_inic'], 0, 5) . ' - ' . substr($horariosInscripciones[$j]['Horario']['hora_fin'], 0, 5) . ').';
									if (strtotime($horariosInscripciones[$j]['Horario']['hora_inic']) <= strtotime($horariosCurso[$i]['Horario']['hora_inic']) && strtotime($horariosCurso[$i]['Horario']['hora_inic']) <= strtotime($horariosInscripciones[$j]['Horario']['hora_fin'])) {
										echo $strTemp;
										$inscripcionCurso = false;
										$finalizarRevision = true;
										break;
									} elseif (strtotime($horariosInscripciones[$j]['Horario']['hora_inic']) <= strtotime($horariosCurso[$i]['Horario']['hora_fin']) && strtotime($horariosCurso[$i]['Horario']['hora_fin']) <= strtotime($horariosInscripciones[$j]['Horario']['hora_fin'])) {
										echo $strTemp;
										$inscripcionCurso = false;
										$finalizarRevision = true;
										break;
									} elseif (strtotime($horariosCurso[$i]['Horario']['hora_inic']) <= strtotime($horariosInscripciones[$j]['Horario']['hora_inic']) && strtotime($horariosInscripciones[$j]['Horario']['hora_inic']) <= strtotime($horariosCurso[$i]['Horario']['hora_fin'])) {
										echo $strTemp;
										$inscripcionCurso = false;
										$finalizarRevision = true;
										break;
									}
								} /* if -> mientras sea el mismo d�a */
							} /* for j -> horariosInscripciones */
							if ($finalizarRevision) {
								break;
							} /* if */
						} /* for i -> horariosCurso */
					
					} /* if INSCRIPCIONES_CRUCEHRS */
					
					## se puede realizar la inscripci�n
					if ($inscripcionCurso) {
						## recibo el resultado del procesamiento de la inscripci�n
						$inscripcionCurso = $this->Inscripcion->nueva_inscripcion($_SESSION['persona_dni'], $idCurso);
						## �xito al crear la inscripci�n
						if ($inscripcionCurso) {
							echo 'Ahora est�s inscrito en <strong>' . $dataCurso[0]['Actividad']['nombre'] . '</strong>.';
						} else {
							echo 'Bueno, esto es vergonzoso. Se ha intentado guardar tu inscripci�n, pero al parecer existe un error.';
						} /* else */
					} /* if */
					
				} /* else */
				
				/****************************************************/
				} else {
					echo 'Es necesario que te identifiques para que puedas inscribirte en <strong>' . 
					$dataCurso[0]['Actividad']['nombre'] . '</strong>.';
				} /* else */
			} else {
				echo 'Vaya! Se ha presentado un error mientras procesabamos tu solicitud.';
			} /* else */
		} else {
			echo 'Vaya! Se ha presentado un error mientras procesabamos tu solicitud.';
		} /* else */
		
		/****************************************************/
		
		## Funci�n de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	/*
	 * mostrar link de inscripci�n,
	 * seg�n el id del curso.
	 * Activar y desactivar el link,
	 * si est� inscrito el usuario de la 
	 * sesi�n o no ha iniciado sesi�n.
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
			
			## revisar que se el usuario haya iniciado sesi�n
			if (array_key_exists('logueado', $_SESSION) && $_SESSION['logueado'] && count($dataCurso)!=0) {
				
				## obtengo el per�odo actual.
				$periodoActual = performAction('actividades', 'periodo_actual', array());
			
				/*
				 * s�lo se habilitar�n las inscripciones, mientras
				 * que la fecha de la visita del usuario est� dentro
				 * del rango de las fechas del per�odo (es decir, el 
				 * per�odo actual) al que pertenece el curso. 
				 */
				$dataPeriodo = performAction('actividades', 'consultar_periodo', array($periodoActual[0]['Periodo']['id']));
				$fechaActual = strtotime(date('Y-m-d'));
				$plazoInscripcion = false;
				if (count($dataPeriodo)!=0 && strtotime($dataPeriodo[0]['Periodo']['fecha_inic']) <= $fechaActual  && $fechaActual <= strtotime($dataPeriodo[0]['Periodo']['fecha_fin'])) {
					$plazoInscripcion = true;
				}				
			
				## obtengo el perfil del usuario en el per�odo actual
				$dataPerfil = performAction('personas', 'consultar_perfil', array($_SESSION['persona_dni'], $periodoActual[0]['Periodo']['id']));
			
				## revisar si la persona ya est� inscrita en el curso
				$inscrito = $this->Inscripcion->query('select * from inscripciones where persona_dni = \'' . $_SESSION['persona_dni'] . 
				'\' and curso_id = \'' . mysql_real_escape_string($idCurso) . '\'');
				$inscrito = (is_array($inscrito) && count($inscrito)!=0) ? true : false;
				 
				## Si la persona no tiene un perfil, no se puede inscribir.
				if (count($dataPerfil)==0) {
					$strSalida .= '
					<a class="btn danger disabled" href="javaScript:void(0);" rel="popover" title="Inscripci�n" 
					data-content="Es necesario que tengas un perfil en el per�odo <i>' . $periodoActual[0]['Periodo']['periodo'] . 
					'</i> para que puedas inscribirte en <strong>' . 
					$dataCurso[0]['Actividad']['nombre'] . '</strong>.">
						Incribirme
					</a>
					';		
				} elseif ($inscrito) {
					## la persona ya est� inscrita
					$strSalida .= '
					<a class="btn success disabled" href="javaScript:void(0);" rel="popover" title="Inscripci�n" 
					data-content="Ya est�s inscrito en <strong>' . $dataCurso[0]['Actividad']['nombre'] . '</strong>."> 
						Incribirme
					</a>
					';				
				} elseif (!$plazoInscripcion) {
					$strSalida .= '
					<a class="btn danger disabled" href="javaScript:void(0);" rel="popover" title="Inscripci�n" 
					data-content="Las inscripciones no est�n habilitadas. El per�odo de inscripciones inicia <strong>'.
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
					<a class="btn primary" href="javaScript:void(0);" data-controls-modal="modal-inscripcion" data-backdrop="static" rel="twipsy" title="Inscribirme!"> 
						Incribirme
					</a>
					';
				}				 
				
			} elseif(count($dataCurso)!=0) {
				$strSalida .= '
				<a class="btn primary disabled" href="javaScript:void(0);" rel="popover" title="Inscripci�n" 
				data-content="Es necesario que te identifiques para que puedas inscribirte en <strong>' . 
				$dataCurso[0]['Actividad']['nombre'] . '</strong>.">
					Incribirme
				</a>
				';				
			} /* elseif */
			
			echo $strSalida;
		}
		
		/****************************************************/
		
		## Funci�n de respuesta ajax
		$this->doNotRenderHeader = 1;
	
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	function afterAction () {
		
	}
	
}