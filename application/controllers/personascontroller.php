<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class PersonasController extends VanillaController {
	
	function beforeAction () {
		session_start();
	}
	
	function consultar_persona ($dni = null) {
		if (isset($dni) && preg_match('/^[0-9]{5,20}$/', $dni)) {
			return $this->Persona->consultar_persona ($dni);
		} else {
			return false;
		}/* else */
	}
	
	function consultar_perfil ($dni, $idPeriodo) {
		return $this->Persona->consultar_perfil ($dni, $idPeriodo);
	}
	
	/**
	 * 
	 * Muestra el perfil del usuario
	 * en el período actual.
	 */
	function perfil () {
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
		/*
		 * revisar que el usuario haya iniciado sesión,
		 * porque de éste se cargará la info del perfil
		 */
		if (!array_key_exists('logueado', $_SESSION) || !$_SESSION['logueado']) {
			$this->render = 0;
			## no cargar datos porque no ha iniciado sesión
			echo '<span class="label important">Error</span> ' .
			'Es necesario que te identifiques para que puedas ver la información de tu perfil.';
			exit;
		}
		
		/*
		 * revisar que se haya definido
		 * un perído actual.
		 */
		$periodoActual = performAction('actividades', 'periodo_actual', array());
		if (!is_array($periodoActual) || count($periodoActual)==0) {
			$this->render = 0;
			## no cargar datos porque no ha iniciado sesión
			echo '<span class="label notice">Noticia</span> ' .
			'Bienestar Universitario aún no ha definido un período para que gestiones tus inscripciones.';
			exit;
		}
		
		## consultar el perfil del usuario en el período actual
		$dataPerfil = $this->consultar_perfil($_SESSION['persona_dni'], $periodoActual[0]['Periodo']['id']);
		## el usuario tiene un perfil en período actual
		$perfilPeriodoActual = (count($dataPerfil)!=0) ? true : false;
		
		$this->set('periodoActual', $periodoActual);
		$this->set('dataPerfil', $dataPerfil);
		$this->set('perfilPeriodoActual', $perfilPeriodoActual);
		
		if ($perfilPeriodoActual) {
			## según el perfil que tenga, recojo los datos
			$sqlPerfil = 'SELECT ';
			if (strtolower($dataPerfil[0]['Multientidad']['nombre'])=='estudiante') {
				$sqlPerfil .= 'multientidad.nombre as jornada, programa.nombre, facultad.nombre, perfil.semestre
				FROM perfiles perfil, multientidad multientidad, programas programa, facultad facultad
				WHERE perfil.id = \'' . $dataPerfil[0]['Perfil']['id'] . '\' AND perfil.jornada_multientidad = multientidad.id
				AND perfil.programa_id = programa.id AND programa.facultad_id = facultad.id';
			} elseif (strtolower($dataPerfil[0]['Multientidad']['nombre'])=='docente') {
				$sqlPerfil .= 'multientidad.nombre as contrato, programa.nombre, facultad.nombre
				FROM perfiles perfil, multientidad multientidad, programas programa, facultad facultad
				WHERE perfil.id = \'' . $dataPerfil[0]['Perfil']['id'] . '\' AND perfil.contrato_multientidad = multientidad.id
				AND perfil.programa_id = programa.id AND programa.facultad_id = facultad.id';
			} elseif (strtolower($dataPerfil[0]['Multientidad']['nombre'])=='egresado') {
				$sqlPerfil .= 'programa.nombre, facultad.nombre	FROM perfiles perfil, programas programa, facultad facultad
				WHERE perfil.id = \'' . $dataPerfil[0]['Perfil']['id'] . '\' AND perfil.programa_id = programa.id 
				AND programa.facultad_id = facultad.id';
			} elseif (strtolower($dataPerfil[0]['Multientidad']['nombre'])=='funcionario') {
				$sqlPerfil .= 'programa.nombre, facultad.nombre FROM perfiles perfil, programas programa, facultad facultad
				WHERE perfil.id = \'' . $dataPerfil[0]['Perfil']['id'] . '\' AND perfil.programa_id = programa.id 
				AND programa.facultad_id = facultad.id';
			} elseif (strtolower($dataPerfil[0]['Multientidad']['nombre'])=='familiar') {
				$sqlPerfil .= 'multientidad.nombre as consanguinidad, apoderado.nombres, apoderado.apellidos
				FROM perfiles perfil, personas apoderado, multientidad multientidad
				WHERE perfil.id = \'' . $dataPerfil[0]['Perfil']['id'] . '\' AND perfil.parentesco_multientidad = multientidad.id
				AND perfil.apoderado_dni = apoderado.dni';
			} /* elseif */
			## información complementaria según el perfil
			$dataPerfil_complementaria = $this->Persona->query($sqlPerfil);
			$this->set('dataPerfil_complementaria', $dataPerfil_complementaria);
		} else {
			## listo los diferentes perfiles de la comunidad universitaria
			$this->set('listaPerfiles', $this->Persona->query('select * from multientidad where entidad = \'comunidad_universitaria\' order by nombre ASC'));
			## lista de perfiles que no se pueden gestionar
			$this->set('perfilesDisabled', array('docente', 'funcionario'));
		} /* else */
		
		/****************************************************/
		
		## controlador de respuesta ajax
		$this->doNotRenderHeader = 1;
		
	}
	
	/*
	 * función que agrupa por facultad
	 * los programas académicos ...
	 * facultad
	 * 	[i] -> array(
	 * 		[nombre]
	 * 		[id]
	 *  )
	 */
	function listarProgramas () {
		
		$listaProgramas = array();
		$sqlProgramas = 'select facultad.id, facultad.nombre, facultad.abrev, programa.id, programa.nombre, programa.abrev
		from facultad facultad, programas programa where programa.facultad_id = facultad.id order by facultad.nombre asc, programa.nombre';
		$tempProgramas = $this->Persona->query($sqlProgramas);
		$strTemp = '';
		
		for ($i = 0; $i < count($tempProgramas); $i++) {
			$listaProgramas[$tempProgramas[$i]['Facultad']['nombre']] = array(
				'id' => $tempProgramas[$i]['Facultad']['id'],
				'abrev' => $tempProgramas[$i]['Facultad']['abrev'],
				'programas' => array()
			);
			for ($j = $i; $j < count($tempProgramas); $j++) {
				if ($tempProgramas[$i]['Facultad']['nombre']==$tempProgramas[$j]['Facultad']['nombre']) {
					array_push($listaProgramas[$tempProgramas[$i]['Facultad']['nombre']]['programas'], 
						array(
							'id' => $tempProgramas[$j]['Programa']['id'], 
							'nombre' => $tempProgramas[$j]['Programa']['nombre'],
							'abrev' => $tempProgramas[$j]['Programa']['abrev']
						)
					);
				} else {
					break;
				}
			} /* for j */
			$j--;
			$i = $j;
		} /* for i */
		
		return $listaProgramas; 
		
	}
	
	/*
	 * carga los campos según
	 * el perfil seleccionado.
	 */
	function dynamicForm () {
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
		if (isset($_POST['idPerfil']) && preg_match('/^[0-9]{1,}$/', $_POST['idPerfil'])) {
			
			$idPerfil = $_POST['idPerfil'];
			## consulto el nombre del perfil recibido por id
			$nombrePerfil = $this->Persona->query('select multientidad.nombre from multientidad where id = \'' . mysql_real_escape_string($idPerfil) . '\'');
			## el id se ha asignado a un perfil en la BD
			if (is_array($nombrePerfil) && count($nombrePerfil)!=0) {
				$nombrePerfil = $nombrePerfil[0]['Multientidad']['nombre'];
				$this->set('nombrePerfil', $nombrePerfil);
				$this->set('listaJornadas', $this->Persona->query('select * from multientidad where entidad = \'jornadas\' order by nombre ASC'));
				$this->set('listaParentescos', $this->Persona->query('select * from multientidad where entidad = \'parentescos\' order by nombre ASC'));
				$this->set('listaProgramas', $this->listarProgramas());
			} else {
				$this->render = 0;
				echo 'Vaya! Se ha presentado un error mientras procesabamos tu solicitud.';
				exit;
			}
			
		} elseif (!isset($_POST['idPerfil'])) {
			$this->render = 0;
			echo 'Vaya! Se ha presentado un error mientras procesabamos tu solicitud.';
			exit;
		} else {
			$this->render = 0;
			exit;
		}
		
		/****************************************************/
		
		## controlador de respuesta ajax
		$this->doNotRenderHeader = 1;
		
	}
	
	/*
	 * procesar los datos para
	 * crear un nuevo formulario
	 */
	function crear_perfil () {
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
		## no se recibieron los campos para crear el formulario
		if (!isset($_POST['fields']) || !is_array($_POST['fields']) || count($_POST['fields'])==0) {
			$this->render = 0;
			echo '<span class="label important">Error</span> Se ha presentado un error mientras procesabamos tu solicitud.';
			exit;
		}
		
		$tempData = $_POST['fields'];
		$validarData = array();
		## recojo los datos recibidos
		for ($i = 0; $i < count($tempData); $i++) {
			foreach ($tempData[$i] as $field => $value) {
				$validarData[$field] = $value;
			}
			unset($field, $value);
		}
		
		$periodoActual = performAction('actividades', 'periodo_actual', array());
		$strSalida = '';
		$strJs = '';
		
		if (preg_match('/^[0-9]{1,}$/', $validarData['perfilUsuario'])) {
			
			$strJs .= '$("#inputPerfil").removeClass("error");';
			$strJs .= '$("#inputPerfil .input .help-inline").html("");';
			
			## consulto el nombre del perfil seleccionado
			$nombrePerfil = $this->Persona->query('select multientidad.nombre from multientidad where id = \'' . $validarData['perfilUsuario'] . '\'');
			$nombrePerfil = strtolower($nombrePerfil[0]['Multientidad']['nombre']);
			
			if ($nombrePerfil=='estudiante') {
				
				$errorFormulario = false;
				
				## validar que se seleccione un programa académico
				if (!preg_match('/^[0-9]{1,}$/', $validarData['programaAcademico'])) {
					$strJs .= '$("#inputPrograma").addClass("error");';
					$strJs .= '$("#inputPrograma .input .help-inline").html("Es necesario que seleccione un programa académico.");';
					$errorFormulario = true;
				} else {
					$strJs .= '$("#inputPrograma").removeClass("error");';
					$strJs .= '$("#inputPrograma .input .help-inline").html("");';
				} /* else */
				
				## validar que se seleccione una jornada
				if (!preg_match('/^[0-9]{1,}$/', $validarData['jornada'])) {
					$strJs .= '$("#inputJornada").addClass("error");';
					$strJs .= '$("#inputJornada .input .help-inline").html("Es necesario que seleccione una jornada.");';
					$errorFormulario = true;
				} else {
					$strJs .= '$("#inputJornada").removeClass("error");';
					$strJs .= '$("#inputJornada .input .help-inline").html("");';
				}
				
				## validar que se ingrese un semestre
				if (!preg_match('/^[0-9]{1,}$/', $validarData['semestre']) || $validarData['semestre'] < 1 || $validarData['semestre'] > 20) {
					$strJs .= '$("#inputSemestre").addClass("error");';
					$errorFormulario = true;
				}  else {
					$strJs .= '$("#inputSemestre").removeClass("error");';
				}
				
				if ($errorFormulario) {
					$strSalida = '<div class="alert-message warning fade in" data-alert="alert">
					<a class="close" href="#">&times;</a>
					<p>Parte de la información es incorrecta. Corrija el formulario e inténtelo de nuevo.</p>
					</div>';
				} else {
					/**
					 * no se recibieron errores
					 */
					## validar que no exista un perfil en el período actual
					$sqlPerfil = 'select * from perfiles where persona_dni = \'' . $_SESSION['persona_dni'] . '\' and periodo_id = \'' . 
					$periodoActual[0]['Periodo']['id'] . '\'';
					$perfilPeriodoActual = $this->Persona->query($sqlPerfil);
					$perfilPeriodoActual = (is_array($perfilPeriodoActual) && count($perfilPeriodoActual)!=0) ? true : false;
					## crear el perfil
					$sqlNuevo = 'INSERT INTO perfiles SET
					persona_dni = \'' . $_SESSION['persona_dni'] . '\',
					periodo_id = \'' . $periodoActual[0]['Periodo']['id'] . '\',
					perfil_multientidad = \'' . $validarData['perfilUsuario'] . '\',
					programa_id = \'' . $validarData['programaAcademico'] . '\',
					jornada_multientidad = \'' . $validarData['jornada'] . '\',
					semestre = \'' . $validarData['semestre'] . '\'';
					## éxito al crear 
					if (!$perfilPeriodoActual && $this->Persona->query($sqlNuevo)) {
						echo '<script type="text/javascript">loadDataTab("perfil");</script>';
						exit;
					} else {
						$strSalida = '<div class="alert-message error fade in" data-alert="alert">
						<a class="close" href="#">&times;</a>
						<p>Bueno, esto es vergonzoso. Se ha intentado guardar el perfil, pero al parecer existe un error.</p>
						</div>';
					} /* error creando */
				}
				
			} elseif ($nombrePerfil=='egresado') {
				
				$errorFormulario = false;
				
				## validar que se seleccione un programa académico
				if (!preg_match('/^[0-9]{1,}$/', $validarData['programaAcademico'])) {
					$strJs .= '$("#inputPrograma").addClass("error");';
					$strJs .= '$("#inputPrograma .input .help-inline").html("Es necesario que seleccione un programa académico.");';
					$errorFormulario = true;
				} else {
					$strJs .= '$("#inputPrograma").removeClass("error");';
					$strJs .= '$("#inputPrograma .input .help-inline").html("");';
				} /* else */
				
				if ($errorFormulario) {
					$strSalida = '<div class="alert-message warning fade in" data-alert="alert">
					<a class="close" href="#">&times;</a>
					<p>Parte de la información es incorrecta. Corrija el formulario e inténtelo de nuevo.</p>
					</div>';
				} else {
					/**
					 * no se recibieron errores
					 */
					## validar que no exista un perfil en el período actual
					$sqlPerfil = 'select * from perfiles where persona_dni = \'' . $_SESSION['persona_dni'] . '\' and periodo_id = \'' .
					$periodoActual[0]['Periodo']['id'] . '\'';
					$perfilPeriodoActual = $this->Persona->query($sqlPerfil);
					$perfilPeriodoActual = (is_array($perfilPeriodoActual) && count($perfilPeriodoActual)!=0) ? true : false;
					## crear el perfil
					$sqlNuevo = 'INSERT INTO perfiles SET
					persona_dni = \'' . $_SESSION['persona_dni'] . '\',
					periodo_id = \'' . $periodoActual[0]['Periodo']['id'] . '\',
					perfil_multientidad = \'' . $validarData['perfilUsuario'] . '\',
					programa_id = \'' . $validarData['programaAcademico'] . '\'';
					## éxito al crear 
					if (!$perfilPeriodoActual && $this->Persona->query($sqlNuevo)) {
						echo '<script type="text/javascript">loadDataTab("perfil");</script>';
						exit;
					} else {
						$strSalida = '<div class="alert-message error fade in" data-alert="alert">
						<a class="close" href="#">&times;</a>
						<p>Bueno, esto es vergonzoso. Se ha intentado guardar el perfil, pero al parecer existe un error.</p>
						</div>';
					} /* error creando */
				}
				
			} elseif ($nombrePerfil=='familiar') {
				
				$errorFormulario = false;
				
				## validar la identificación del apoderado
				if (!preg_match('/^[0-9]{5,20}$/', $validarData['apoderado'])) {
					$strJs .= '$("#inputApoderado").addClass("error");';
					$strJs .= '$("#inputApoderado .input .help-inline").html("Ingrese un número de identificación válido.");';
					$errorFormulario = true;
				} else {
					$strJs .= '$("#inputApoderado").removeClass("error");';
					$strJs .= '$("#inputApoderado .input .help-inline").html("");';
					## el apoderado no puede ser el mismo usuario
					if ($validarData['apoderado']==$_SESSION['persona_dni']) {
						$strJs .= '$("#inputApoderado").addClass("error");';
						$strJs .= '$("#inputApoderado .input .help-inline").html("Ingrese un número de identificación diferente del suyo.");';
						$errorFormulario = true;
					} else {
						$strJs .= '$("#inputApoderado").removeClass("error");';
						$strJs .= '$("#inputApoderado .input .help-inline").html("");';
						## consulto los datos del apoderado
						$dataApoderado = $this->consultar_persona($validarData['apoderado']);
						if (count($dataApoderado)==0 || $dataApoderado[0]['Persona']['estado']!='1') {
							$strJs .= '$("#inputApoderado").addClass("error");';
							$strError = 'El número de identificación ingresado corresponde a una persona que no existe o no está activa.';
							$strJs .= '$("#inputApoderado .input .help-inline").html("' . $strError . '");';
							unset($strError);
							$errorFormulario = true;
						}
						/**
						 * NOTA: no se valida que el apoderado tenga un perfil,
						 * porque éste puede no ser beneficiario de Bienestar en
						 * el período que se ha definido como actual, y como tal,
						 * es muy probable que no tenga perfil en dicho período.
						 * Pero que el apoderado no tenga un perfil, no significa
						 * que sus familiares no puedan inscribirse en actividades
						 * en el período definido como actual.
						 */
					}
				}
				
				## validar que se seleccione un parentesco
				if (!preg_match('/^[0-9]{1,}$/', $validarData['parentesco'])) {
					$strJs .= '$("#inputParentesco").addClass("error");';
					$strError = 'Seleccione el tipo de consanguinidad o afinidad que existe entre usted y su apoderado.';
					$strJs .= '$("#inputParentesco .input .help-inline").html("' . $strError . '");';
					unset($strError);
					$errorFormulario = true;
				} else {
					$strJs .= '$("#inputParentesco").removeClass("error");';
					$strJs .= '$("#inputParentesco .input .help-inline").html("");';
				}
				
				if ($errorFormulario) {
					$strSalida = '<div class="alert-message warning fade in" data-alert="alert">
					<a class="close" href="#">&times;</a>
					<p>Parte de la información es incorrecta. Corrija el formulario e inténtelo de nuevo.</p>
					</div>';
				} else {
					/**
					 * no se recibieron errores
					 */
					## validar que no exista un perfil en el período actual
					$sqlPerfil = 'select * from perfiles where persona_dni = \'' . $_SESSION['persona_dni'] . '\' and periodo_id = \'' .
					$periodoActual[0]['Periodo']['id'] . '\'';
					$perfilPeriodoActual = $this->Persona->query($sqlPerfil);
					$perfilPeriodoActual = (is_array($perfilPeriodoActual) && count($perfilPeriodoActual)!=0) ? true : false;
					## crear el perfil
					$sqlNuevo = 'INSERT INTO perfiles SET
					persona_dni = \'' . $_SESSION['persona_dni'] . '\',
					periodo_id = \'' . $periodoActual[0]['Periodo']['id'] . '\',
					perfil_multientidad = \'' . $validarData['perfilUsuario'] . '\',
					parentesco_multientidad = \'' . $validarData['parentesco'] . '\',
					apoderado_dni = \'' . $validarData['apoderado'] . '\'';
					## éxito al crear 
					if (!$perfilPeriodoActual && $this->Persona->query($sqlNuevo)) {
						echo '<script type="text/javascript">loadDataTab("perfil");</script>';
						exit;
					} else {
						$strSalida = '<div class="alert-message error fade in" data-alert="alert">
						<a class="close" href="#">&times;</a>
						<p>Bueno, esto es vergonzoso. Se ha intentado guardar el perfil, pero al parecer existe un error.</p>
						</div>';
					} /* error creando */
				}
				
			}
			
			$strSalida = '<script type="text/javascript">$(function () {' . $strJs . '});</script>' . $strSalida;
						
		} else {
			$strSalida = '<script type="text/javascript">
			$(function () {
				$("#inputPerfil").addClass("error");
				$("#inputPerfil .input .help-inline").html("Es necesario que seleccione un perfil para continuar.");
			});
			</script>
			<div class="alert-message warning fade in" data-alert="alert">
				<a class="close" href="#">&times;</a>
				<p>Parte de la información es incorrecta. Corrija el formulario e inténtelo de nuevo.</p>
			</div>';
		}
		
		echo $strSalida;
		
		/****************************************************/
		
		## controlador de respuesta ajax
		$this->doNotRenderHeader = 1;
	}
	
	/**
	 * Este controlador permite
	 * la identificación de las personas
	 * de la comunidad universitaria.
	 */
	function login () {
		
		/*
		 * si ya inició sesión, redirecciono al home
		 */
		if (array_key_exists('logueado', $_SESSION) && $_SESSION['logueado']) {
			redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action']);
		}
		
		## envío del formulario
		if (isset($_POST['identificacion'])) {
		
			$ind_error = array();	
		
			$validar_data = array(	
				'identificacion' => $_POST['identificacion']
			);
			
			## validar el número de identificación
			if (!preg_match('/^[0-9]{5,20}$/', $validar_data['identificacion'])) {
				$ind_error['identificacion'] = 'Ingrese un número de identificación válido.';
				$showAlert = array(	
					'type' => 'warning',
					'message' => 'Parte de la información es incorrecta. Corrija el formulario e inténtelo de nuevo.'
				);
				$this->set('ind_error', $ind_error);
				$this->set('showAlert', $showAlert);
			} else {
				/* se ha ingresado un número de identificación válido */
				## consultar si la persona existe en el sistema
				$data_persona = $this->consultar_persona($validar_data['identificacion']);
				/*
				 * sólo ingresa en el sistema si:
				 * 1. la persona existe
				 * 2. su estado es activo
				 */
				if (count($data_persona) && $data_persona[0]['Persona']['estado']==1) {
					$_SESSION['persona_dni'] = $data_persona[0]['Persona']['dni'];
					$_SESSION['nombres'] = $data_persona[0]['Persona']['nombres'];
					$_SESSION['apellidos'] = $data_persona[0]['Persona']['apellidos'];
					$_SESSION['logueado'] = true;
					## redirecciono al home de la aplicación
					redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action']);
				} else {
					$showAlert = array(
						'type' => 'error',
						'block' => true
					);
					$showAlert['message'] = '
					<p>Existe un error al ingresar en el sistema. Las posibles causas son:</p>
					<ul>
						<li>
							El número de identificación ingresado <span class="label important">no existe</span> en el sistema.
						</li>
						<li>
							El número de identificación ingresado corresponde a una persona que no está activa en el sistema, 
							por lo cual, no está <span class="label important">autorizado</span> su ingreso en el mismo.<br/>
						</li>
					</ul>
					';
					$this->set('showAlert', $showAlert);
				} /* else */
			} /* else */

		} /* envío del formulario */
		
	}
	
	function logout () {
		## destruyo las variables de sesión
		session_unset();
		$_SESSION = array();
		## destruyo la sesión actual
		session_destroy();
		
		## redirecciono al login
		redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action']);
	}
	
	function afterAction () {
		
	}
	
	
}