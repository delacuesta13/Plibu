<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class VanillaController {
	
	protected $_controller;
	protected $_action;
	protected $_template;
	
	public $doNotRenderHeader;
	public $render;
	
	function __construct($controller, $action) {
	
		global $inflect;
	
		$this->_controller = ucfirst($controller);
		$this->_action = $action;
	
		$model = ucfirst($inflect->singularize($controller));
		$this->doNotRenderHeader = 0;
		$this->render = 1;
		$this->$model = new $model;
		$this->_template = new Template($controller,$action);
	
	}
	
	function set($name,$value) {
		$this->_template->set($name,$value);
	}
	
	function __destruct() {
		if ($this->render) {
			$this->_template->render($this->doNotRenderHeader);
		}
	}
	
}