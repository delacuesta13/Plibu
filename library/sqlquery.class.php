<?php

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class SQLQuery {

	protected $_dbHandle;
	protected $_result;
	
	/** Variables para la paginaci�n */
	var $_query; /* var que contiene el query */
	var $_tables; /* tablas que componen la consulta, con sus (opcional) respectivo alias */
	var $_fields; /* campos a obtener en la consulta */
	var $_extraConditions; /* par�metros para hacer la b�squeda m�s exacta (WHERE) */
	var $_likeConditions; /* par�metros para hacer la b�squeda m�s flexible (LIKE) */
	var $_order; /* ordenar y direccionar una columna */
	var $_page = 1; /* definir n�mero de p�gina */
	var $_limit = PAGINATE_LIMIT; /* n�mero de registros por p�gina */
	var $_numLimitRows = 0; /* n�mero de registros obtenidos en la consulta, contando s�lo los devueltos por la cl�usula LIMIT */
	var $_numTotalRows = 0; /* total de registros que devolver�a la consulta sin la cl�usula LIMIT */
	var $_pagAntes = 3; /* mostrar n�mero de p�ginas antes (tomando como referencia la p�gina actual) en la navegaci�n */
	var $_pagDespues = 3;
	var $_msjAnterior = '&larr; Anterior';
	var $_msjDespues = 'Siguiente &rarr;'; 

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
		
	/** Paginaci�n */
	
	function setPage ($page) {
		$this->_page = $page;
	}
	
	function setPagAntes ($numPagAntes) {
		$this->_pagAntes = $numPagAntes;
	}
	
	function setPagDespues ($numPagDespues) {
		$this->_pagDespues = $numPagDespues;
	}
	
	function setMsjAnterior ($msjAnterior) {
		$this->_msjAnterior = $msjAnterior;
	}
	
	function setMsjDespues ($msjDespues) {
		$this->_msjDespues = $msjDespues;
	}
	
	function setLimit ($limit) {
		$this->_limit = $limit;
	}
	
	/**
	 * 
	 * ordenamiento de la consulta ...
	 * @param string $orderBy
	 * @param strin $order
	 */
	function orderBy ($orderBy, $order = 'ASC') {
		$this->_order .= $orderBy . ' ' . strtoupper($order) . ', ';
	}
	
	function where ($field, $value, $join = false) {
		if (!$join) {
			$this->_extraConditions .= $field . ' = \'' . mysql_real_escape_string($value) . '\' AND ';
		} else {
			$this->_extraConditions .= $field . ' = ' . $value . ' AND ';
		}
	}
	
	function like ($field, $value) {
		$this->_likeConditions .= $field . ' LIKE \'%' . mysql_real_escape_string($value) . '%\' OR ';
	}
	
	function addTable ($table, $alias = null) {
		$this->_tables .= $table . ((isset($alias) && strlen($alias)!=0) ? (' ' . $alias) : '') . ', ';
	}
	
	/**
	 * agrega un campo a la consulta
	 */
	function addField ($field) {
		$this->_fields .= $field . ', ';
	}
	
	function paginate () {
		
		global $inflect;
		
		/*
		 * NOTA: cada vez que se agrege una 
		 * cl�usula al query, iniciar con un
		 * espacion en blanco.
		 */
		
		$this->_query = 'SELECT SQL_CALC_FOUND_ROWS';
		
		## agrego los campos a la consulta
		$this->_query .= (strlen($this->_fields)!=0) ? (' ' . substr_replace($this->_fields, '', -2)) : ' *';
		
		## agrego las tablas a la consulta
		$this->_query .= ' FROM '. ((strlen($this->_tables)!=0) ? substr_replace($this->_tables, '', -2) : $this->_table);
		
		## agrego cl�usula WHERE si hay extraConditions o likeConditions
		$this->_query .= (strlen($this->_extraConditions)!=0 || strlen($this->_likeConditions)!=0) ? ' WHERE' : '';

		## se agreg� la cla�sula where
		if (preg_match('/WHERE/i', $this->_query)) {
			$this->_query .= (strlen($this->_extraConditions)!=0) ? (' ' . substr_replace($this->_extraConditions, '', -5)) : '';
			$this->_query .= (strlen($this->_likeConditions)!=0) ? 
				((strlen($this->_extraConditions)!=0) ? 
					(' AND (' . substr_replace($this->_likeConditions, '', -4) . ')') : 
					(' ' . substr_replace($this->_likeConditions, '', -4))) :
				'';
		} /* if */
		
		## agrego cla�sula ORDER BY
		$this->_query .= ((strlen($this->_order)!=0) ?
			(' ORDER BY ' . substr_replace($this->_order, '', -2)) :
			'');
		
		## agrego cl�usula LIMIT
		if (preg_match('/^[0-9]{1,}$/', $this->_page) && preg_match('/^[0-9]{1,}$/', $this->_limit)) {
			$offset = ($this->_page - 1) * $this->_limit;
			$this->_query .= ' LIMIT ' . $offset . ', ' . $this->_limit;
		} /* if */
		
		## ejecuto el query
		$this->_result = mysql_query($this->_query, $this->_dbHandle);
		
		$result = array();
		$table = array();
		$field = array();
		$tempResults = array();
		
		## obtener el n�mero de columnas del query
		$numFields = mysql_num_fields($this->_result);
		for ($i = 0; $i < $numFields; $i++) {
			array_push($table, mysql_field_table($this->_result, $i));
			array_push($field, mysql_field_name($this->_result, $i));
		} /* for */
		## agregar los registros al arreglo result
		while ($row = mysql_fetch_row($this->_result)) {
			for ($i = 0;$i < $numFields; ++$i) {
				$table[$i] = ucfirst($inflect->singularize($table[$i]));
				$tempResults[$table[$i]][$field[$i]] = $row[$i];
			} /* for */
			array_push($result,$tempResults);
		} /* while */
		
		mysql_free_result($this->_result);
		
		## obtener el total del registros que la consulta devolver�a sin la cl�usula LIMIT
		$sqlTotal = 'SELECT FOUND_ROWS() as total';
		$rsTotal = mysql_query($sqlTotal, $this->_dbHandle);
		$rsTotal = mysql_fetch_assoc($rsTotal);
		$this->_numTotalRows = $rsTotal['total'];
		
		## obtener el n�mero de registros de la consulta, de s�lo los devueltos por la cl�usula LIMIT
		$this->_numLimitRows = count($result);
		
		return($result);
		
	}

	/**
	 * Las siguientes funciones (excluir desde query)
	 * s�lo han de ser utilizadas si se llam� la funci�n
	 * paginate.
	 * NOTA: Cualquier da�o que pueda ocasionar la omisi�n
	 * de la advertencia, es responsabilidad suya. ;)
	 */
	
	function getSqlPaginate () {
		return $this->_query;
	}
	
	/*
	 * obtener el n�mero de registros 
	 * de la consulta, excluyendo la cl�usula
	 * LIMIT.
	 */
	function getNumTotalRows () {
		return $this->_numTotalRows;
	}
	
	/*
	 * obtener el n�mero de registros
	 * de la consulta, donde s�lo
	 * se tiene en cuenta la cl�usula LIMIT.
	 */
	function getNumLimitRows () {
		return $this->_numLimitRows;
	}

	/**
	 * Devuelve en un array, 
	 * los elementos de navegaci�n
	 * de la paginaci�n ...
	 */
	function getNavigation () {
		
		$itemsNavigation = array();
		$numPaginas = ceil($this->getNumTotalRows() / $this->_limit);
		
		## no hay que paginar
		if ($numPaginas <= 1) {
			return $itemsNavigation;
		}
		
		$pagInic = (($this->_page - $this->_pagAntes) > 1) ? ($this->_page - $this->_pagAntes) : 1; // n�mero de p�gina inicial
		$pagFinal = (($this->_page + $this->_pagDespues) < $numPaginas) ? ($this->_page + $this->_pagDespues) : $numPaginas;
		
		if ((($pagFinal - $pagInic) != ($this->_pagAntes + $this->_pagDespues))) {
			$pagAntFaltantes = $this->_page - $this->_pagAntes; // n�mero de elementos que se dejaron de mostar antes de la p�gina actual
			/*
			 * hay p�ginas anteriores que se dejaron de mostrar,
			 * si pagAntFaltantes <= 0
			 */
			if ($pagAntFaltantes <= 0) {
				$pagAntFaltantes = abs($pagAntFaltantes) + 1;
				$pagFinal = (($pagFinal + $pagAntFaltantes) >= $numPaginas) ? $numPaginas : ($pagFinal + $pagAntFaltantes);
			} /* if */
			$pagDespFaltantes = ($this->_page + $this->_pagDespues) - $numPaginas;
			if ($pagDespFaltantes > 0) {
				$pagInic = (($pagInic - $pagDespFaltantes) <= 1) ? 1 : ($pagInic - $pagDespFaltantes);
			} /* if */
		} /* if */
		
		$mostrarAnterior = ($this->_page > 1) ? true : false;
		$mostrarSiguiente = ($this->_page < $numPaginas) ? true : false;
		
		$temp = array();
		for ($i = $pagInic; $i <= $pagFinal; $i++) {
			
			if ($i == $pagInic) {
				$temp = array(
					'text' => $this->_msjAnterior,
					'link' => ($this->_page - 1),
					'prev' => true,
					'next' => false,
					'disabled' => ((!$mostrarAnterior) ? true : false),
					'active' => false
				);
				array_push($itemsNavigation, $temp);
			}
			
			if ($i != $this->_page) {
				$temp = array(
					'text' => $i,
					'link' => $i,
					'prev' => false,
					'next' => false,
					'disabled' => false,
					'active' => false
				);
				array_push($itemsNavigation, $temp);
			} else {
				$temp = array(
					'text' => $i,
					'link' => $i,
					'prev' => false,
					'next' => false,
					'disabled' => true,
					'active' => true
				);
				array_push($itemsNavigation, $temp);
			}
			
			if ($i == $pagFinal) {
				$temp = array(
					'text' => $this->_msjDespues,
					'link' => ($this->_page + 1),
					'prev' => false,
					'next' => true,
					'disabled' => ((!$mostrarSiguiente) ? true : false),
					'active' => false
				);
				array_push($itemsNavigation, $temp);
			}
			
		}
		
		return $itemsNavigation; 
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
		 * Para insert, update y delete se retornar� un
		 * booleano que indica el resultado del query.
		 * 	true  -> query ejecutado exit�samente
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
	
	/** Get error string **/

	function getError() {
		return mysql_error($this->_dbHandle);
	}
	
}