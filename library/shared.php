<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/** Check if environment is development and display errors **/

function setReporting() {
	if (DEVELOPMENT_ENVIRONMENT == true) {
		error_reporting(E_ALL);
		ini_set('display_errors','On');
	} else {
		error_reporting(E_ALL);
		ini_set('display_errors','Off');
		ini_set('log_errors', 'On');
		ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
	}
}

/** Check for Magic Quotes and remove them **/

function stripSlashesDeep($value) {
	$value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
	return $value;
}

function removeMagicQuotes() {
	if ( get_magic_quotes_gpc() ) {
		$_GET    = stripSlashesDeep($_GET   );
		$_POST   = stripSlashesDeep($_POST  );
		$_COOKIE = stripSlashesDeep($_COOKIE);
	}
}

/** Check register globals and remove them **/

function unregisterGlobals() {
	if (ini_get('register_globals')) {
		$array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
		foreach ($array as $value) {
			foreach ($GLOBALS[$value] as $key => $var) {
				if ($var === $GLOBALS[$key]) {
					unset($GLOBALS[$key]);
				}
			}
		}
	}
}

/** Secondary Call Function **/

function performAction($controller,$action,$queryString = null,$render = 0) {

	$controllerName = ucfirst($controller).'Controller';
	$dispatch = new $controllerName($controller, $action);
	$dispatch->render = $render;
	return call_user_func_array(array($dispatch,$action),$queryString);
}

/**
 *  Función de Redirección
 * 
 *  Definir la redirección hacia un controlador y una acción (de éste), enviando (opcionalmente) parámetros
 *  @param string $controller
 *  @param string $action
 *  @param array  $parameters
 *  
 *  Ejemplo 1:
 *  	Entrada:
 *  		<code>
 *  			redirectAction('controlador', 'accion', array('parametro_1', 'parametro_n'))
 *  		</code>
 *  	Salida:
 *  		<url>
 *  			BASE_PATH/controlador/accion/parametro_1/parametro_n
 *  		</url>  
 */

function redirectAction($controller, $action, $parameters = null) {

	$redirectUrl = BASE_PATH . '/' . $controller . '/' . (isset($action) ? $action : 'index');
	if(isset($parameters) && is_array($parameters) && count($parameters)!=0) {
		foreach($parameters as $parameter)	$redirectUrl .= '/' . $parameter;
		unset($parameter);
	}
	header("Location: $redirectUrl");
}

/** Main Call Function **/

function callHook() {
	global $url;
	global $default;
	
	$queryString = array();
	
	if(!isset($url)) {
		$controller = $default['controller'];
		$action = $default['action'];
	} else {
		$urlArray = array();
		$urlArray = explode("/",$url);
		$controller = $urlArray[0];
		array_shift($urlArray);
		if (isset($urlArray[0]) && strlen($urlArray[0])!=0) {
			$action = $urlArray[0];
			array_shift($urlArray);
		} else {
			$action = 'index'; // Default Action
		}
		$queryString = $urlArray;
	}
	
	$controllerName = ucfirst($controller).'Controller';	
	$dispatch = new $controllerName($controller, $action);
	
	if ((int)method_exists($controllerName, $action)) {
		call_user_func_array(array($dispatch,"beforeAction"),$queryString);
		call_user_func_array(array($dispatch,$action),$queryString);
		call_user_func_array(array($dispatch,"afterAction"),$queryString);
	} else {
		/* Error Generation Code Here */
		redirectAction($default['controller'], $default['action'], array('error', '404'));
	}	
}

/** Autoload any classes that are required **/

function __autoload($className) {
	global $default;
	
	if (file_exists(ROOT . DS . 'library' . DS . strtolower($className) . '.class.php')) {
		require_once(ROOT . DS . 'library' . DS . strtolower($className) . '.class.php');
	} else if (file_exists(ROOT . DS . 'application' . DS . 'controllers' . DS . strtolower($className) . '.php')) {
		require_once(ROOT . DS . 'application' . DS . 'controllers' . DS . strtolower($className) . '.php');
	} else if (file_exists(ROOT . DS . 'application' . DS . 'models' . DS . strtolower($className) . '.php')) {
		require_once(ROOT . DS . 'application' . DS . 'models' . DS . strtolower($className) . '.php');
	} else {
		/* Error Generation Code Here */
		redirectAction($default['controller'], $default['action'], array('error', '404'));
	}
}

$cache = new Cache();
$inflect = new Inflection();

setReporting();
removeMagicQuotes();
unregisterGlobals();
callHook();