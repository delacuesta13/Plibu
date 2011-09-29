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
				periodo.id, 
				periodo.periodo, 
				curso.monitor_dni,
				curso.abierto, 
				curso.fecha_inic, 
				curso.fecha_fin, 
				curso.comentario 
		FROM	actividades actividad, 
				areas area, 
				periodos periodo, 
				cursos curso 
		WHERE	periodo.actual = \'1\' 
				AND periodo.id = curso.periodo_id 
				AND curso.id = \'' . mysql_real_escape_string($idCurso) . '\' 
				AND curso.actividad_id = actividad.id 
				AND actividad.area_id = area.id  
		';
		return $this->query($sql);
	}
	
	function listar_horarios ($idCurso) {
		$sql = '
		SELECT	horario.dia, 
				horario.hora_inic, 
				horario.hora_fin, 
				lugar.nombre, 
				lugar.direccion 
		FROM	horarios horario, 
				lugares lugar 
		WHERE	horario.curso_id = \'' . $idCurso . '\' 
				AND horario.lugar_id = lugar.id 
		ORDER	BY horario.dia ASC, 
				horario.hora_inic ASC, 
				horario.hora_fin ASC 
		';
		return $this->query($sql);
	}
	
}