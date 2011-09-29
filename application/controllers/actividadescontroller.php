<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ActividadesController extends VanillaController {
	
	function beforeAction () {
		session_start();
	}
	
	/*
	 * retorna los datos del periodo actual
	 */
	function periodo_actual () {
		return $this->Actividad->query ('select * from periodos where actual = \'1\'');
	}
	
	function consultar_periodo ($idPeriodo) {
		return $this->Actividad->query('select * from periodos where id = \'' . mysql_real_escape_string($idPeriodo) . '\' ');
	}
	
	function consultar_curso ($idCurso) {
		if (preg_match('/^[0-9]{1,}$/', $idCurso)) {
			return $this->Actividad->consultar_curso($idCurso);
		} else {
			return false;
		}
	}
	
	function listar_horarios ($idCurso) {
		if (preg_match('/^[0-9]{1,}$/', $idCurso)) {
			return $this->Actividad->listar_horarios ($idCurso);
		} else {
			return false;
		}
	}
	
	function index ($typeMessage = null, $idMessage = null) {
		
		$listMessages = array(
			'error' => array(
				'404' => array(
					'type' => 'info',
					'message' => '<strong>Oops!</strong> Al parecer la página que intentas acceder no está disponible o definitivamente no existe.'
				)
			)
		);
		
		## se recibe un mensaje para mostrar
		if (isset($typeMessage, $idMessage) && array_key_exists($typeMessage, $listMessages) && array_key_exists($idMessage, $listMessages[$typeMessage])) {
			$this->set('showMessage', $listMessages[$typeMessage][$idMessage]);
		}
		
		/*
		 * consultar el período actual,
		 * y así mostrar las actividades
		 * programadas en éste.
		 */
		$periodo_actual = $this->periodo_actual();
		## existe un periódo actual
		if (count($periodo_actual)!=0) {
			$this->set('periodo_actual', $periodo_actual);
		}
		
		$tag_js = '
		function loadDataTable (pag, sort, order) {
			$(function () {
				var url = url_project + "' . strtolower($this->_controller) . '/listar_cursos";
				if (pag.length!=0) url += "/pag=" + pag;
				url += "/record=" + $("#regpag").val();				
				if (sort.length!=0) url += "/sort=" + sort;				
				if (order.length!=0) url += "/order=" + order;
				var q = $("#search").val();
				if (q.length!=0) url += "/search=" + q;
				$.ajax(
					{
						url: url,
						dataType: "html",
						beforeSend: function() {
							$("#modal-cargando").modal({
								show: true,
								backdrop: "static",
								keyboard: false
							});
						},
						success: function( data ) {
							$("#modal-cargando").modal("hide");
							$("#dynamic").html(data);
						}
					}
				);
			});
		}
		
		$(function () {
			
			loadDataTable(\'\', \'\', \'\');
			
			$( "#regpag" ).change(function() {
				loadDataTable(1, \'\', \'\');
			});
			
			$( "#btn_search" ).bind("click", function() {
				loadDataTable(1, \'\', \'\');
			});
			
			$( "span[rel=twipsy]" ).twipsy({
				live: true,
				placement: "below"
			});
			
		});
		';
		$this->set('make_tag_js', $tag_js);
		
	}
	
	function listar_cursos () {
		
		global $inflect;
		
		$model = ucfirst($inflect->singularize(strtolower($this->_controller)));
				
		## recibo los parámetros
		$parametros = func_get_args();
		
		## parámetros por defecto
		$sortDft = 'actividad.nombre';
		$orderDft = 'ASC';
		
		/*
		 * tipo de parámetros que pueden recibirse y 
		 * pueden ser agregados al sql
		 */
		$tipo_params = array(
			'/^pag=/' => array(
				'name' => 'pag', ## nombre de la variable
				'regex' => '/^[0-9]{1,}$/', ## patrón con el debe coincidir el valor recibido
				'default' => 1 ## valor defualt sino se define o su valor no coincide con el valor
			),
			'/^record=/' => array(
				'name' => 'record',
				'regex' => '/^[0-9]{1,}$/',
				'default' => PAGINATE_LIMIT
			),
			'/^sort=/' => array(
				'name' => 'sort',
				'regex' => '/^[a-zA-Z0-9_\.]+$/',
				'default' => $sortDft
			),
			'/^order=/' => array(
				'name' => 'order',
				'regex' => '/^(asc|desc)$/i',
				'default' => $orderDft
			),
			'/^search=/' => array(
				'name' => 'search',
				'regex' => '/^[a-zA-Z0-9 ]{1,30}$/'
			)
		);
		
		/*
		 * los siguiente son las columnas que se mostrarán
		 * en la tabla, y es equivalente a los campos que
		 * se llamarán en la consulta
		 */
		$fieldsTable = array(
			'actividad.nombre' => array(
				'text' => 'Actividad',
				'color' => 'red',
				'params' => array(
					'showTable' => true,
					'sort' => true,
					'where' => true
				) /* end params */
			), /* end actividad.nombre */
			'area.nombre' => array(
				'text' => 'Área',
				'color' => 'blue',
				'params' => array(
					'showTable' => true,
					'sort' => true,
					'where' => true
				) /* end params */
			), /* end area.nombre */
			'curso.monitor_dni' => array(
				'text' => 'Monitor',
				'color' => 'green',
				'params' => array(
					'showTable' => true,
					'sort' => false,
					'where' => false
				) /* end params */				
			), /* end monitor.nombre */
			'curso.id' => array(
				'params' => array(
					'showTable' => false,
					'where' => false
				) /* end params */
			) /* end curso.id */
		);
		
		## editar el query según los parámetros recibidos
		$setQuery = array();
		$temp = '';
		
		for ($i = 0; $i < count($parametros); $i++) {
			foreach ($tipo_params as $param => $def) {
				## el parámetro recibido es válido
				if (preg_match($param, $parametros[$i])) {
					## obtengo el valor del parámetro recibido
					$temp = preg_replace($param, '', $parametros[$i]);
					/*
					 * si el valor recibido coincide con el patrón 
					 * de valores para el parámetro, lo asigno a 
					 * setQuery 
					 */
					if (preg_match($def['regex'], $temp)) {	
						$setQuery[$def['name']] = $temp;
					} elseif (array_key_exists('default', $def)) {
						$setQuery[$def['name']] = $def['default'];
					}
					unset ($tipo_params[$param]);
					break;
				} /* if */
			} /* foreach */
			unset ($param, $def);
		} /* for */
		
		## agrego las tablas (y sus alias) a la consulta
		$this->$model->addTable('actividades', 'actividad');
		$this->$model->addTable('areas', 'area');
		$this->$model->addTable('cursos', 'curso');
		$this->$model->addTable('periodos', 'periodo');
		
		$this->set ('fieldsTable', $fieldsTable);
		
		## agrego los campos
		foreach ($fieldsTable as $field => $def) {
			$this->$model->addField($field);
		}
		unset ($field, $def);
		
		## agrego los where
		$this->$model->where('periodo.actual', '1'); // mostrar actividades del período que se definió como actual
		$this->$model->where('periodo.id', 'curso.periodo_id', true);
		$this->$model->where('curso.abierto', '1'); // mostrar actividades públicas
		$this->$model->where('curso.actividad_id', 'actividad.id', true);
		$this->$model->where('actividad.area_id', 'area.id', true);
		
		## agrego los LIKE
		if (array_key_exists('search', $setQuery)) {
			foreach ($fieldsTable as $field => $def) {
				## puede buscarse en el campo
				if ($def['params']['where']) {
					$this->$model->like ($field, $setQuery['search']);
				}
			}
			unset ($field, $def);
		}
		
		/*
		 * defino página y record (limit) default.
		 */
		$this->set ('pag', 1);
		$this->set ('record', PAGINATE_LIMIT);
		
		## se definió la página
		if (array_key_exists('pag', $setQuery)) {
			$this->$model->setPage($setQuery['pag']);
			$this->set ('pag', $setQuery['pag']);
		}
		
		## se definió el limit
		if (array_key_exists('record', $setQuery)) {
			$this->$model->setLimit($setQuery['record']);
			$this->set ('record', $setQuery['record']);
		}
		
		$orderFields = $fieldsTable;
		
		## se definió la columna por la cual ordenar y su dirección
		if (array_key_exists('sort', $setQuery) && array_key_exists('order', $setQuery) && array_key_exists($setQuery['sort'], $fieldsTable)) {
			$this->$model->orderBy ($setQuery['sort'], $setQuery['order']);
			$this->set('sort', $setQuery['sort']);
			$this->set('order', $setQuery['order']);
			unset ($orderFields[$setQuery['sort']]);
		} else {
			$this->$model->orderBy ($sortDft, $orderDft);
			$this->set('sort', $sortDft);
			$this->set('order', $orderDft);
			unset ($orderFields[$sortDft]);
		}
		
		## termino de agregar los campos para su ordenamiento
		foreach ($orderFields as $field => $def) {
			## agrego el campo, sólo si se definió para que sea ordenado
			if (array_key_exists('sort', $def['params']) && $def['params']['sort']) {
				$this->$model->orderBy ($field);
			} /* if */
		} /* foreach */
		unset ($orderFields, $field, $def);
		
		$data_query = $this->$model->paginate();
		$this->set('data_query', $data_query);
		
		## número de registros sin cláusula LIMIT
		$this->set('totalRows', $this->$model->getNumTotalRows());
		## número de registros con cláusula LIMIT
		$this->set('limitRows', $this->$model->getNumLimitRows());
		
		## array de navegación
		$this->set('itemsNavigation', $this->$model->getNavigation());
		
		/****************************************************/
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	function ver ($idCurso = null, $actividad = null) {
		
		if (isset($idCurso, $actividad) && preg_match('/^[0-9]{1,}$/', $idCurso) && preg_match('/^[a-z0-9-]{2,60}$/', $actividad)) {
			
			$dataCurso = $this->Actividad->consultar_curso($idCurso);
			
			## no se recibió el nombre de la actividad (en formato URL) como debería de ser
			if ($actividad!=$this->getNombreUrl($idCurso) || count($dataCurso)==0 || $dataCurso[0]['Curso']['abierto']!=1) {
				redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '404'));
			}

			$tag_js = '
			
			function loadDataInscripcion () {
				$(function () {
					var url = url_project + "' . 'inscripciones' . '/' . 'getInscripcionCurso' . '/' . $idCurso . '";
					$.ajax(
					{
						url: url,
						dataType: "html",
						beforeSend: function() {
							$( ".cargandoInscripcion" ).css("display", "block");
						},
						success: function( data ) {
							$( ".cargandoInscripcion" ).css("display", "none");
							$( "#dynamicInscripcion" ).html(data);
						}
					}
					);
				});
			}
			
			function inscripcionCurso (idCurso) {
				alert("idCurso: " + idCurso);
			}
			
			loadDataInscripcion();
			
			$(function () {
				$( "td[title]" )
				.popover({
					html: true
				})
				.click(function(e) {
					e.preventDefault()
				});
			});
			
			';
			$this->set('make_tag_js', $tag_js);

			$this->set('dataCurso', $dataCurso);
			$this->set('listaHorarios', $this->listar_horarios($idCurso));
			$this->set('idCurso', $idCurso);
			$this->set('actividadUrl', $actividad);
			
		} else {
			redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '404'));
		}
		
	}
	
	/**
	 * 
	 * devolver el nombre de una actividad
	 * formateado para poner en una URL, eliminando
	 * caracteres especiales y más ...
	 * @param int $id
	 */
	function getNombreUrl ($id = null, $type = 'curso') {
		$nombreActividad = '';
		$especialCaract = array('á', 'é', 'í', 'ó', 'ú', 'ñ', '_');
		$replaceCaract = array('a', 'e', 'i', 'o', 'u', 'n', '-');
		$sql = 'SELECT actividad.nombre FROM actividades actividad';		
		## se definió un id
		if (isset($id) && preg_match('/^[0-9]{1,}$/', $id)) {
			if (strtolower($type)=='actividad') {
				/*
				 * nombre de una actividad. var id
				 * define el id de la actividad.
				 */
				$sql .= ' WHERE actividad.id = \'' . mysql_real_escape_string($id) . '\'';
				$nombreActividad = $this->Actividad->query($sql);
			} elseif (strtolower($type)=='curso') {
				/*
				 * nombre de la actividad programada (curso)
				 * en un periodo, se define el id del curso,
				 * y se devuelve el nombre de la actividad a
				 * la que éste pertenece.
				 */
				$sql .= ', cursos curso';
				$sql .= ' WHERE curso.id = \'' . mysql_real_escape_string($id) . '\'';
				$sql .= ' AND curso.actividad_id = actividad.id';
				$nombreActividad = $this->Actividad->query($sql);
			} /* elseif */
			if (is_array($nombreActividad) && count($nombreActividad)!=0) {
				$nombreActividad = strtolower($nombreActividad[0]['Actividad']['nombre']);
				$nombreActividad = str_replace($especialCaract, $replaceCaract, $nombreActividad);
				$nombreActividad = preg_replace('/-{2,}/', '-', preg_replace('/\s+/', '-', $nombreActividad));
			}
		} /* if */
		return $nombreActividad;
	}
		
	function afterAction () {
		
	}
	
}