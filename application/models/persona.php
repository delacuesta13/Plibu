<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Persona extends VanillaModel {
	
	function consultar_persona ($dni) {
		return $this->query('select * from personas where dni = \'' . mysql_real_escape_string($dni) . '\'');
	}
	
}