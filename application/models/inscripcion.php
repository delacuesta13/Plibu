<?php

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Inscripcion extends VanillaModel {
	
	function nueva_inscripcion ($dni, $idCurso) {
		$sql = '
		INSERT INTO inscripciones SET
		persona_dni = \'' . $dni . '\',
		curso_id = \'' . $idCurso . '\',
		fecha_inscripcion = NOW(),
		created_at = NOW() 
		';
		return $this->query($sql);
	}
	
}