<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/** Configuration Variables **/

define ('DEVELOPMENT_ENVIRONMENT',true);

define('DB_NAME', 'sigabu');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_HOST', 'localhost');

define('BASE_PATH','http://localhost/plibu');

define('PAGINATE_LIMIT', '10');

/** Configuración del comportamiento del proyecto **/

/*
 * INSCRIPCIONES_CRUCEHRS * 
 * tipo: boolean
 * default: false
 * 
 * INSCRIPCIONES_CRUCEHRS (o "inscripción cruce de horarios"), según su valor,
 * valida si la inscripción de una persona en un curso (programación de una 
 * actividad en un determinado periodo) se cruza con los horarios de otras
 * inscripciones (del mismo periodo).
 * 
 * ++ Comportamiento (según su valor) ++
 * true: valida que no se crucen los horarios de la inscripción a realizar,
 * con las inscripciones existentes. Si se encuentra que hay cruce de horarios,
 * no se permite la nueva inscripción.
 * false: no valida los cruces de los horarios.    
 */
define('INSCRIPCIONES_CRUCEHRS', false);