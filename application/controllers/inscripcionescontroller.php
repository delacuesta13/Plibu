<?php

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class InscripcionesController extends VanillaController {
	
	function beforeAction () {
		
		session_start();
		
		$loginPlibu = performAction('personas', 'loginPlibu', array());
		
		if (!$loginPlibu) {
			## destruyo las variables de sesi�n
			session_unset();
			$_SESSION = array();
			
			## destruyo la sesi�n actual
			session_destroy();
			
			session_start();
		}
		
	}
	
	function index () {
		
		## revisar que el usuario haya iniciado sesi�n
		if (!array_key_exists('logueado', $_SESSION) || !$_SESSION['logueado']) {
			redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', 'login'));
			exit;
		}
		
		/*
		 * es necesario que se haya definido un
		 * per�odo como actual, pues en base a �ste
		 * se gestiona el perfil y las inscripciones del
		 * usuario.
		 */
		$periodoActual = performAction('actividades', 'periodo_actual', array());
		if (!is_array($periodoActual) || count($periodoActual)==0) {
			redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '404'));
			exit;
		}
		
		$tag_js = '
		
		function loadDataTab (tab) {
			$(function () {
				var url = url_project;
				var divLoading = "";
				var divDynamic = "";
				if (tab.toLowerCase() == "perfil") {
					url += "personas/perfil";
					divLoading = "cargandoPerfil";
					divDynamic = "dynamicPerfil";
				}
				/* se recibi� una tab v�lida */
				if (divLoading.length > 0) {
					$.ajax(
						{
							url: url,
							dataType: "html",
							beforeSend: function() {
								$( "#" + divLoading ).css("display", "block");
							},
							success: function( data ) {
								$( "#" + divLoading ).css("display", "none");
								$( "#" + divDynamic).html(data);
							}
						}
					);
				}
			});
		}
		
		loadDataTab ("perfil");
		
		function loadDataTable (pag, sort, order) {
			$(function () {
				var url = url_project + "inscripciones/listar_inscripciones";
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
							$("#dynamicInscripciones").css("opacity", "0.4");
							$("#cargandoInscripciones").css("display", "block");
						},
						success: function( data ) {
							$("#dynamicInscripciones").html(data);
							$("#dynamicInscripciones").css("opacity", "1.0");
							$("#cargandoInscripciones").css("display", "none");
						}
					}
				);
			});
		}
		
		loadDataTable("", "", "");
		
		$(function () {
		
			$( "#regpag" ).change(function() {
				loadDataTable(1, "", "");
			});
			
			$( "#btn_search" ).bind("click", function() {
				loadDataTable(1, "", "");
			});
		
		});
		
		';
		$this->set('make_tag_js', $tag_js);
		
		$this->set('periodoActual', $periodoActual);
		
	}
	
	function listar_inscripciones () {
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
		/*
		 * revisar que el usuario haya iniciado sesi�n,
		 * porque de �ste se cargar� la info de las inscripciones
		 */
		if (!array_key_exists('logueado', $_SESSION) || !$_SESSION['logueado']) {
			$this->render = 0;
			## no cargar datos porque no ha iniciado sesi�n
			echo '<span class="label important">Error</span> ' .
			'Es necesario que te identifiques para que puedas ver tus inscripciones.';
			exit;
		}
		
		/***************************************************************************/
		
		global $inflect;		
		$model = ucfirst($inflect->singularize(strtolower($this->_controller)));
		
		## recibo los par�metros
		$parametros = func_get_args();
		
		## par�metros por defecto
		$sortDft = 'inscripcion.fecha_inscripcion';
		$orderDft = 'DESC';
		
		/*
		 * tipo de par�metros que pueden recibirse y 
		 * pueden ser agregados al sql
		 */
		$tipo_params = array(
			'/^pag=/' => array(
				'name' => 'pag', ## nombre de la variable
				'regex' => '/^[0-9]{1,}$/', ## patr�n con el debe coincidir el valor recibido
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
		 * los siguiente son las columnas que se mostrar�n
		 * en la tabla, y es equivalente a los campos que
		 * se llamar�n en la consulta
		 */
		$fieldsTable = array(
			'curso.id' => array(
				'params' => array(
					'showTable' => false,
					'where' => false
				) /* end params */
			), /* end curso.id */
			'curso.abierto' => array(
				'params' => array(
					'showTable' => false,
					'where' => false
				) /* end params */
			), /* end curso.abierto */
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
				'text' => '�rea',
				'color' => 'blue',
				'params' => array(
					'showTable' => true,
					'sort' => true,
					'where' => true
				) /* end params */
			), /* end area.nombre */
			'inscripcion.fecha_inscripcion' => array(
				'text' => 'Fecha Inscripci�n',
				'color' => 'green',
				'params' => array(
					'showTable' => true,
					'sort' => true,
					'where' => false
				) /* end params */
			) /* end inscripcion.fecha_inscripcion */
		);
		
		## editar el query seg�n los par�metros recibidos
		$setQuery = array();
		$temp = '';
		
		for ($i = 0; $i < count($parametros); $i++) {
			foreach ($tipo_params as $param => $def) {
				## el par�metro recibido es v�lido
				if (preg_match($param, $parametros[$i])) {
					## obtengo el valor del par�metro recibido
					$temp = preg_replace($param, '', $parametros[$i]);
					/*
					 * si el valor recibido coincide con el patr�n 
					 * de valores para el par�metro, lo asigno a 
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
		$this->$model->addTable('cursos', 'curso');
		$this->$model->addTable('inscripciones', 'inscripcion');
		$this->$model->addTable('actividades', 'actividad');
		$this->$model->addTable('areas', 'area');
		$this->$model->addTable('periodos', 'periodo');
		
		$this->set ('fieldsTable', $fieldsTable);
		
		## agrego los campos
		foreach ($fieldsTable as $field => $def) {
			$this->$model->addField($field);
		}
		unset ($field, $def);
		
		## agrego los where
		$this->$model->where('periodo.actual', 1);
		$this->$model->where('periodo.id', 'curso.periodo_id', true);
		$this->$model->where('curso.actividad_id', 'actividad.id', true);
		$this->$model->where('actividad.area_id', 'area.id', true);
		$this->$model->where('inscripcion.persona_dni', $_SESSION['persona_dni']);
		$this->$model->where('inscripcion.curso_id', 'curso.id', true);
		
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
		 * defino p�gina y record (limit) default.
		 */
		$this->set ('pag', 1);
		$this->set ('record', PAGINATE_LIMIT);
		
		## se defini� la p�gina
		if (array_key_exists('pag', $setQuery)) {
			$this->$model->setPage($setQuery['pag']);
			$this->set ('pag', $setQuery['pag']);
		}
		
		## se defini� el limit
		if (array_key_exists('record', $setQuery)) {
			$this->$model->setLimit($setQuery['record']);
			$this->set ('record', $setQuery['record']);
		}
		
		$orderFields = $fieldsTable;
		
		## se defini� la columna por la cual ordenar y su direcci�n
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
			## agrego el campo, s�lo si se defini� para que sea ordenado
			if (array_key_exists('sort', $def['params']) && $def['params']['sort']) {
				$this->$model->orderBy ($field);
			} /* if */
		} /* foreach */
		unset ($orderFields, $field, $def);
		
		$data_query = $this->$model->paginate();
		$this->set('data_query', $data_query);
		
		## n�mero de registros sin cl�usula LIMIT
		$this->set('totalRows', $this->$model->getNumTotalRows());
		## n�mero de registros con cl�usula LIMIT
		$this->set('limitRows', $this->$model->getNumLimitRows());
		
		## array de navegaci�n
		$this->set('itemsNavigation', $this->$model->getNavigation());
		
		/***************************************************************************/
		
		## funci�n de respuesta ajax
		$this->doNotRenderHeader = 1;
		
	}
	
	/*
	 * inscribir al usuario de la sesi�n
	 * en un curso. Se debe de recibir
	 * idCurso (id del curso) por POST.
	 */
	function inscripcionCurso () {
		
		$listaMeses = array('Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$listaDias = array('Lunes', 'Martes', 'Mi�rcoles', 'Jueves', 'Viernes', 'S�bado', 'Domingo');
		
		echo '<script type="text/JavaScript">
		$("#closeModal").bind("click", function () {
			$("#modal-inscripcion").modal("hide");
		});
		</script>';
		
		if (isset($_POST['idCurso']) && preg_match('/^[0-9]{1,}$/', $_POST['idCurso'])) {
			
			$idCurso = $_POST['idCurso'];
			$dataCurso = performAction('actividades', 'consultar_curso', array($idCurso));
			$periodoActual = performAction('actividades', 'periodo_actual', array());
			
			## el curso existe y las inscripciones est�n abiertas, adem�s pertenece al per�odo actual
			if (count($dataCurso)!=0 && $dataCurso[0]['Curso']['abierto']=='1' && count($periodoActual)!=0 && $dataCurso[0]['Periodo']['id']==$periodoActual[0]['Periodo']['id']) {
				## el usuario debe haber iniciado sesi�n
				if (array_key_exists('logueado', $_SESSION) && $_SESSION['logueado']) {
				/****************************************************/
				
				## consultar el perfil del usuarios en el per�odo al cual pertenece el curso
				$dataPerfil = performAction('personas', 'consultar_perfil', array($_SESSION['persona_dni'], $dataCurso[0]['Periodo']['id']));
				## revisar si el plazo de inscripciones est� activo
				$fechaActual = strtotime(date('Y-m-d'));
				$plazoInscripcion = ((strtotime($periodoActual[0]['Periodo']['fecha_inic']) <= $fechaActual &&  $fechaActual <= strtotime($periodoActual[0]['Periodo']['fecha_fin'])) ? 
					(true) : 
					(false));
				## revisar si el usuario ya est� inscrito en el curso
				$sqlInscrito = 'select * from inscripciones where persona_dni = \'' . $_SESSION['persona_dni'] . '\' and curso_id = \'' . $idCurso . '\'';
				$inscrito = $this->Inscripcion->query($sqlInscrito);
				$inscrito = (is_array($inscrito) && count($inscrito)!=0) ? true : false;
				
				## la persona no tiene un perfil
				if (count($dataPerfil)==0) {
					echo 'Es necesario que tengas un perfil en el per�odo <i>' . $periodoActual[0]['Periodo']['periodo'] . '</i>
					para que puedas inscribirte en <strong>' . $dataCurso[0]['Actividad']['nombre'] . '</strong>.';
				} elseif ($inscrito) {
					## el usuario ya est� inscrito en el curso
					echo 'Ya est�s inscrito en <strong>' . $dataCurso[0]['Actividad']['nombre'] . '</strong>.';
				} elseif (!$plazoInscripcion) {
					echo 'Las inscripciones no est�n habilitadas. El per�odo de inscripci�n inicia <strong>' .
					substr($periodoActual[0]['Periodo']['fecha_inic'], 8, 2) . ' ' .
						$listaMeses[intval(substr($periodoActual[0]['Periodo']['fecha_inic'], 5, 2)) - 1] . ' ' .
							substr($periodoActual[0]['Periodo']['fecha_inic'], 0, 4)
					. '</strong> y finaliza <strong>'.
					substr($periodoActual[0]['Periodo']['fecha_fin'], 8, 2) . ' ' .
						$listaMeses[intval(substr($periodoActual[0]['Periodo']['fecha_fin'], 5, 2)) - 1] . ' ' .
							substr($periodoActual[0]['Periodo']['fecha_fin'], 0, 4)
					. '</strong>.';
				} else {
					
					## si es true se puede inscribir en el curso
					$inscripcionCurso = true;
					
					/*
					 * revisar si los horarios de los cursos en 
					 * los que ya est� inscrito se cruzan con los 
					 * horarios del curso a inscribirse.
					 */
					if (INSCRIPCIONES_CRUCEHRS) {
						
						## obtengo los horarios del curso en el cual se va a inscribir la persona
						$horariosCurso = performAction('actividades', 'listar_horarios', array($idCurso));
						
						## obtengo los horarios de los cursos en los que ya est� inscrita la persona
						$sqlHorarios = 'select actividad.nombre, horario.dia, horario.hora_inic, horario.hora_fin
						from actividades actividad, inscripciones inscripcion, cursos curso, horarios horario, periodos periodo
						where periodo.actual = \'1\' and periodo.id = curso.periodo_id and curso.id = inscripcion.curso_id
						and inscripcion.persona_dni = \'' . $_SESSION['persona_dni'] . '\' and curso.id = horario.curso_id
						and curso.actividad_id = actividad.id
						order by horario.dia asc, horario.hora_inic asc, horario.hora_fin asc, actividad.nombre asc';
						$horariosInscripciones = $this->Inscripcion->query($sqlHorarios);
						
						$strTemp = '';
						$finalizarRevision = false;
						
						for ($i = 0; $i < count($horariosCurso); $i++) {
							for ($j = 0; $j < count($horariosInscripciones); $j++) {
								## est�n en el mismo d�a los horarios
								if ($horariosCurso[$i]['Horario']['dia']==$horariosInscripciones[$j]['Horario']['dia']) {
									$strTemp = 'El horario de <strong>' . $dataCurso[0]['Actividad']['nombre'] . '</strong> del d�a ' . 
									$listaDias[intval($horariosCurso[$i]['Horario']['dia']) - 1] . 
									' (' . substr($horariosCurso[$i]['Horario']['hora_inic'], 0, 5) . ' - ' . substr($horariosCurso[$i]['Horario']['hora_fin'], 0, 5) . ')' .
									' se cruza con el de <strong>' . $horariosInscripciones[$j]['Actividad']['nombre'] . '</strong> del mismo d�a' .
									' (' . substr($horariosInscripciones[$j]['Horario']['hora_inic'], 0, 5) . ' - ' . substr($horariosInscripciones[$j]['Horario']['hora_fin'], 0, 5) . ').';
									if (strtotime($horariosInscripciones[$j]['Horario']['hora_inic']) <= strtotime($horariosCurso[$i]['Horario']['hora_inic']) && strtotime($horariosCurso[$i]['Horario']['hora_inic']) <= strtotime($horariosInscripciones[$j]['Horario']['hora_fin'])) {
										echo $strTemp;
										$inscripcionCurso = false;
										$finalizarRevision = true;
										break;
									} elseif (strtotime($horariosInscripciones[$j]['Horario']['hora_inic']) <= strtotime($horariosCurso[$i]['Horario']['hora_fin']) && strtotime($horariosCurso[$i]['Horario']['hora_fin']) <= strtotime($horariosInscripciones[$j]['Horario']['hora_fin'])) {
										echo $strTemp;
										$inscripcionCurso = false;
										$finalizarRevision = true;
										break;
									} elseif (strtotime($horariosCurso[$i]['Horario']['hora_inic']) <= strtotime($horariosInscripciones[$j]['Horario']['hora_inic']) && strtotime($horariosInscripciones[$j]['Horario']['hora_inic']) <= strtotime($horariosCurso[$i]['Horario']['hora_fin'])) {
										echo $strTemp;
										$inscripcionCurso = false;
										$finalizarRevision = true;
										break;
									}
								} /* if -> mientras sea el mismo d�a */
							} /* for j -> horariosInscripciones */
							if ($finalizarRevision) {
								break;
							} /* if */
						} /* for i -> horariosCurso */
					
					} /* if INSCRIPCIONES_CRUCEHRS */
					
					## se puede realizar la inscripci�n
					if ($inscripcionCurso) {
						## recibo el resultado del procesamiento de la inscripci�n
						$inscripcionCurso = $this->Inscripcion->nueva_inscripcion($_SESSION['persona_dni'], $idCurso);
						## �xito al crear la inscripci�n
						if ($inscripcionCurso) {
							echo 'Ahora est�s inscrito en <strong>' . $dataCurso[0]['Actividad']['nombre'] . '</strong>.';
						} else {
							echo 'Bueno, esto es vergonzoso. Se ha intentado guardar tu inscripci�n, pero al parecer existe un error.';
						} /* else */
					} /* if */
					
				} /* else */
				
				/****************************************************/
				} else {
					echo 'Es necesario que te identifiques para que puedas inscribirte en <strong>' . 
					$dataCurso[0]['Actividad']['nombre'] . '</strong>.';
				} /* else */
			} else {
				echo 'Vaya! Se ha presentado un error mientras procesabamos tu solicitud.';
			} /* else */
		} else {
			echo 'Vaya! Se ha presentado un error mientras procesabamos tu solicitud.';
		} /* else */
		
		/****************************************************/
		
		## Funci�n de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	/*
	 * mostrar link de inscripci�n,
	 * seg�n el id del curso.
	 * Activar y desactivar el link,
	 * si est� inscrito el usuario de la 
	 * sesi�n o no ha iniciado sesi�n.
	 */
	function getInscripcionCurso ($idCurso = null) {
		
		$listaMeses = array('Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		
		## recibo un id de un curso
		if (isset($idCurso) && preg_match('/^[0-9]{1,}$/', $idCurso)) {
			
			$dataCurso = performAction('actividades', 'consultar_curso', array($idCurso));
		
			$strSalida = '<script type="text/JavaScript">
			$( "a[rel=twipsy]" )
				.twipsy({
					live: true,
					placement: "right"
				});
			$( "a[rel=popover]" )
				.popover({
					offset: 10,
					html: true
				})
				.click(function(e) {
					e.preventDefault()
				});
			</script>';
			
			## revisar que se el usuario haya iniciado sesi�n
			if (array_key_exists('logueado', $_SESSION) && $_SESSION['logueado'] && count($dataCurso)!=0) {
				
				## obtengo el per�odo actual.
				$periodoActual = performAction('actividades', 'periodo_actual', array());
			
				/*
				 * s�lo se habilitar�n las inscripciones, mientras
				 * que la fecha de la visita del usuario est� dentro
				 * del rango de las fechas del per�odo (es decir, el 
				 * per�odo actual) al que pertenece el curso. 
				 */
				$dataPeriodo = performAction('actividades', 'consultar_periodo', array($periodoActual[0]['Periodo']['id']));
				$fechaActual = strtotime(date('Y-m-d'));
				$plazoInscripcion = false;
				if (count($dataPeriodo)!=0 && strtotime($dataPeriodo[0]['Periodo']['fecha_inic']) <= $fechaActual  && $fechaActual <= strtotime($dataPeriodo[0]['Periodo']['fecha_fin'])) {
					$plazoInscripcion = true;
				}				
			
				## obtengo el perfil del usuario en el per�odo actual
				$dataPerfil = performAction('personas', 'consultar_perfil', array($_SESSION['persona_dni'], $periodoActual[0]['Periodo']['id']));
			
				## revisar si la persona ya est� inscrita en el curso
				$inscrito = $this->Inscripcion->query('select * from inscripciones where persona_dni = \'' . $_SESSION['persona_dni'] . 
				'\' and curso_id = \'' . mysql_real_escape_string($idCurso) . '\'');
				$inscrito = (is_array($inscrito) && count($inscrito)!=0) ? true : false;
				 
				## Si la persona no tiene un perfil, no se puede inscribir.
				if (count($dataPerfil)==0) {
					$strSalida .= '
					<a class="btn danger disabled" href="javaScript:void(0);" rel="popover" title="Inscripci�n" 
					data-content="Es necesario que tengas un perfil en el per�odo <i>' . $periodoActual[0]['Periodo']['periodo'] . 
					'</i> para que puedas inscribirte en <strong>' . 
					$dataCurso[0]['Actividad']['nombre'] . '</strong>.">
						Incribirme
					</a>
					';		
				} elseif ($inscrito) {
					## la persona ya est� inscrita
					$strSalida .= '
					<a class="btn success disabled" href="javaScript:void(0);" rel="popover" title="Inscripci�n" 
					data-content="Ya est�s inscrito en <strong>' . $dataCurso[0]['Actividad']['nombre'] . '</strong>."> 
						Incribirme
					</a>
					';				
				} elseif (!$plazoInscripcion) {
					$strSalida .= '
					<a class="btn danger disabled" href="javaScript:void(0);" rel="popover" title="Inscripci�n" 
					data-content="Las inscripciones no est�n habilitadas. El per�odo de inscripciones inicia <strong>'.
					substr($dataPeriodo[0]['Periodo']['fecha_inic'], 8, 2) . ' ' . 
						$listaMeses[intval(substr($dataPeriodo[0]['Periodo']['fecha_inic'], 5, 2)) - 1] . ' ' .
							substr($dataPeriodo[0]['Periodo']['fecha_inic'], 0, 4) .
					'</strong> y finaliza <strong>' .
					substr($dataPeriodo[0]['Periodo']['fecha_fin'], 8, 2) . ' ' . 
						$listaMeses[intval(substr($dataPeriodo[0]['Periodo']['fecha_fin'], 5, 2)) - 1] . ' ' .
							substr($dataPeriodo[0]['Periodo']['fecha_fin'], 0, 4) .
					'</strong>."> 
						Incribirme
					</a>
					';	
				} else {
					## la persona puede inscribirse
					$strSalida .= '
					<a class="btn primary" href="javaScript:void(0);" data-controls-modal="modal-inscripcion" data-backdrop="static" rel="twipsy" title="Inscribirme!"> 
						Incribirme
					</a>
					';
				}				 
				
			} elseif(count($dataCurso)!=0) {
				$strSalida .= '
				<a class="btn primary disabled" href="javaScript:void(0);" rel="popover" title="Inscripci�n" 
				data-content="Es necesario que te identifiques para que puedas inscribirte en <strong>' . 
				$dataCurso[0]['Actividad']['nombre'] . '</strong>.">
					Incribirme
				</a>
				';				
			} /* elseif */
			
			echo $strSalida;
		}
		
		/****************************************************/
		
		## Funci�n de respuesta ajax
		$this->doNotRenderHeader = 1;
	
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	function afterAction () {
		
	}
	
}