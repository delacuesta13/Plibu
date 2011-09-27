<?php

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
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
	 * Este controlador permite
	 * la identificaci�n de las personas
	 * de la comunidad universitaria.
	 */
	function login () {
		
		/*
		 * si ya inici� sesi�n, redirecciono al home
		 */
		if (array_key_exists('logueado', $_SESSION) && $_SESSION['logueado']) {
			redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action']);
		}
		
		## env�o del formulario
		if (isset($_POST['identificacion'])) {
		
			$ind_error = array();	
		
			$validar_data = array(	
				'identificacion' => $_POST['identificacion']
			);
			
			## validar el n�mero de identificaci�n
			if (!preg_match('/^[0-9]{5,20}$/', $validar_data['identificacion'])) {
				$ind_error['identificacion'] = 'Ingrese un n�mero de identificaci�n v�lido.';
				$showAlert = array(	
					'type' => 'warning',
					'message' => 'Parte de la informaci�n es incorrecta. Corrija el formulario e int�ntelo de nuevo.'
				);
				$this->set('ind_error', $ind_error);
				$this->set('showAlert', $showAlert);
			} else {
				/* se ha ingresado un n�mero de identificaci�n v�lido */
				## consultar si la persona existe en el sistema
				$data_persona = $this->consultar_persona($validar_data['identificacion']);
				/*
				 * s�lo ingresa en el sistema si:
				 * 1. la persona existe
				 * 2. su estado es activo
				 */
				if (count($data_persona) && $data_persona[0]['Persona']['estado']==1) {
					$_SESSION['persona_dni'] = $data_persona[0]['Persona']['dni'];
					$_SESSION['nombres'] = $data_persona[0]['Persona']['nombres'];
					$_SESSION['apellidos'] = $data_persona[0]['Persona']['apellidos'];
					$_SESSION['logueado'] = true;
					## redirecciono al home de la aplicaci�n
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
							El n�mero de identificaci�n ingresado <span class="label important">no existe</span> en el sistema.
						</li>
						<li>
							El n�mero de identificaci�n ingresado corresponde a una persona que no est� activa en el sistema, 
							por lo cual, no est� <span class="label important">autorizado</span> su ingreso en el mismo.<br/>
						</li>
					</ul>
					';
					$this->set('showAlert', $showAlert);
				} /* else */
			} /* else */

		} /* env�o del formulario */
		
	}
	
	function logout () {
		## destruyo las variables de sesi�n
		session_unset();
		$_SESSION = array();
		## destruyo la sesi�n actual
		session_destroy();
		
		## redirecciono al login
		redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action']);
	}
	
	function afterAction () {
		
	}
	
}