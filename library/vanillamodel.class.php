<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class VanillaModel extends SQLQuery {
	protected $_model;

	function __construct() {

		global $inflect;

		$this->connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
		$this->_limit = PAGINATE_LIMIT;
		$this->_model = get_class($this);
		$this->_table = strtolower($inflect->pluralize($this->_model));		
	}

	function __destruct() {
	}
}