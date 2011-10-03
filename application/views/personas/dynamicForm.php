<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (strtolower($nombrePerfil)=='estudiante') {
?>
<div id="inputPrograma" class="clearfix">
	<label for="programaAcademico">Programa académico</label>
	<div class="input">
		<select style="width: auto !important" name="programaAcademico" id="programaAcademico">
			<option>Seleccione un programa académico</option>
			<?php
			if (count($listaProgramas)!=0) {
				$strSelect = '';
				foreach ($listaProgramas as $facultad => $def) {
					$strSelect .= '<optgroup label="' . ((strlen($facultad)>40) ? $def['abrev'] : $facultad) . '">';
					for ($i = 0; $i < count($def['programas']); $i++) {
						$strSelect .= '<option value="' . $def['programas'][$i]['id'] . '">' . $def['programas'][$i]['nombre'] . '</option>';
					} /* for */
					$strSelect .= '</optgroup>';
				} /* foreach */
				echo $strSelect;
			} /* if */
			?>
		</select>
		<span class="help-inline"></span>
	</div>
</div><!-- /clearfix -->
<div id="inputJornada" class="clearfix">
	<label for="jornada">Jornada</label>
	<div class="input">
		<select style="width: auto !important" name="jornada" id="jornada">
			<option>Seleccione una jornada</option>
			<?php
			$strSelect = '';
			for ($i = 0; $i < count($listaJornadas); $i++) {
				$strSelect .= '<option value="' . $listaJornadas[$i]['Multientidad']['id'] . '">' . $listaJornadas[$i]['Multientidad']['nombre'] . '</option>';
			}
			echo $strSelect; 
			?>
		</select>
		<span class="help-inline"></span>
	</div>
</div><!-- /clearfix -->
<div id="inputSemestre" class="clearfix">
	<label for="jornada">Semestre</label>
	<div class="input">
		<input class="span2" id="jornada" name="jornada" type="text" />
		<span class="help-inline">Ingrese sólo números <em>arábigos</em> (0-9).</span>
	</div>
</div><!-- /clearfix -->
<?php	
} elseif (strtolower($nombrePerfil)=='egresado') {
?>
<div id="inputPrograma" class="clearfix">
	<label for="programaAcademico">Programa académico</label>
	<div class="input">
		<select style="width: auto !important" name="programaAcademico" id="programaAcademico">
			<option>Seleccione un programa académico</option>
			<?php
			if (count($listaProgramas)!=0) {
				$strSelect = '';
				foreach ($listaProgramas as $facultad => $def) {
					$strSelect .= '<optgroup label="' . ((strlen($facultad)>40) ? $def['abrev'] : $facultad) . '">';
					for ($i = 0; $i < count($def['programas']); $i++) {
						$strSelect .= '<option value="' . $def['programas'][$i]['id'] . '">' . $def['programas'][$i]['nombre'] . '</option>';
					} /* for */
					$strSelect .= '</optgroup>';
				} /* foreach */
				echo $strSelect;
			} /* if */
			?>
		</select>
		<span class="help-inline"></span>
	</div>
</div><!-- /clearfix -->
<?php		
} elseif (strtolower($nombrePerfil)=='familiar') {
?>
<script type="text/javascript">
$(function () {
	$("#inputApoderado label[title]").popover({
		html: true,
		placement: "right",
		offset: -340
	});
});
</script>
<div id="inputApoderado" class="clearfix">
	<label for="apoderado" title="Apoderado" 
	data-content="Persona que pertenece a la Universidad, por medio de la cual, puede acceder a la oferta de Bienestar Universitario.">
		Identificación del apoderado<sup>?</sup>
	</label>
	<div class="input">
		<input class="xlarge" id="apoderado" name="apoderado" type="text" />
		
	</div>
</div><!-- /clearfix -->
<div id="inputParentesco" class="clearfix">
	<label for="parentesco">Tipo de consanguineidad o afinidad</label>
	<div class="input">
		<select style="width: auto !important" name="parentesco" id="parentesco">
			<option>Seleccione un tipo de consanguineidad o afinidad</option>
			<?php
			$strSelect = '';
			for ($i = 0; $i < count($listaParentescos); $i++) {
				$strSelect .= '<option value="' . $listaParentescos[$i]['Multientidad']['id'] . '">' . 
				$listaParentescos[$i]['Multientidad']['nombre'] . '</option>';
			} /* for */
			echo $strSelect;	 
			?>
		</select>
	</div>
</div><!-- /clearfix -->
<?php		
}