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
	
	function consultar_perfil ($dni, $idPeriodo) {
		$sql = '
		SELECT	perfil.id,
				periodo.periodo,
				multientidad.nombre, 
				persona.tipo_dni, 
				persona.dni, 
				persona.nombres, 
				persona.apellidos
		FROM	perfiles perfil,
				periodos periodo, 
				multientidad multientidad, 
				personas persona 
		WHERE	perfil.periodo_id = \'' . mysql_real_escape_string($idPeriodo) . '\'
				AND perfil.periodo_id = periodo.id 
				AND perfil.persona_dni = \'' . mysql_real_escape_string($dni) . '\' 
				AND perfil.persona_dni = persona.dni 
				AND perfil.perfil_multientidad = multientidad.id 
		';
	}
	
}