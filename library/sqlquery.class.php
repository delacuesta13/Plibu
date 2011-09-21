<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class SQLQuery {

	protected $_dbHandle;
	protected $_result;

	/** Connects to database **/

	function connect($address, $account, $pwd, $name) {
		$this->_dbHandle = @mysql_connect($address, $account, $pwd);
		if ($this->_dbHandle != 0) {
			if (mysql_select_db($name, $this->_dbHandle)) {
				return 1;
			}
			else {
				return 0;
			}
		}
		else {
			return 0;
		}
	}

	/** Disconnects from database **/

	function disconnect() {
		if (@mysql_close($this->_dbHandle) != 0) {
			return 1;
		}  else {
			return 0;
		}
	}

	/** Custom SQL Query **/

	function query($query) {
		
		global $inflect;

		$this->_result = mysql_query($query, $this->_dbHandle);

		$result = array();
		$table = array();
		$field = array();
		$tempResults = array();

		if(substr_count(strtoupper($query),"SELECT")>0) {				
			if($this->getNumRows()>0){
				$numOfFields = mysql_num_fields($this->_result);
				for ($i = 0; $i < $numOfFields; ++$i) {
					array_push($table,mysql_field_table($this->_result, $i));
					array_push($field,mysql_field_name($this->_result, $i));
				}

				while ($row = mysql_fetch_row($this->_result)) {
					for ($i = 0;$i < $numOfFields; ++$i) {
						$table[$i] = ucfirst($inflect->singularize($table[$i]));
						$tempResults[$table[$i]][$field[$i]] = $row[$i];
					}
					array_push($result,$tempResults);
				}				
			}			
			mysql_free_result($this->_result);
			return($result);
		}

		/**
		 * Para insert, update y delete se retornará un
		 * booleano que indica el resultado del query.
		 * 	true  -> query ejecutado exitósamente
		 * 	false -> error ejecutando el query
		 */
		else {
			if($this->_result):
				return true;
			else:
				return false;
			endif;
		}

	}

	/** Get number of rows **/
	function getNumRows() {
		return mysql_num_rows($this->_result);
	}

	/** Free resources allocated by a query **/

	function freeResult() {
		mysql_free_result($this->_result);
	}

	/** Get error string **/

	function getError() {
		return mysql_error($this->_dbHandle);
	}
}