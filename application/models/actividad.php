<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Actividad extends VanillaModel {
	
	function consultar_curso ($idCurso) {
		$sql = '
		SELECT	actividad.nombre, 
				area.nombre, 
				periodo.periodo, 
				curso.monitor_dni, 
				curso.fecha_inic, 
				curso.fecha_fin, 
				curso.comentario 
		FROM	actividades actividad, 
				areas area, 
				periodos periodo, 
				cursos curso 
		WHERE	periodo.actual = 1 
				AND periodo.id = curso.periodo_id 
				AND curso.id = \'' . mysql_real_escape_string($idCurso) . '\' 
				AND curso.actividad_id = actividad.id 
				AND actividad.area_id = area.id  
		';
		return $this->query($sql);
	}
	
}