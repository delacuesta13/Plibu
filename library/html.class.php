<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Plibu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class HTML {
	
	private $js = array();
	
	function link($text,$path) {		
		$data = '<a href="'.BASE_PATH.'/'.$path.'">'.$text.'</a>';		
		return $data;
	}
	
	function link_to_function($text,$ejec) {		
		$data = '<a href="#" onclick="'.$ejec.'; return none;">'.$text.'</a>';		
		return $data;
	}

	function includeJs($fileName) {
		$data = '<script type="text/javascript" src="'.BASE_PATH.'/js/'.$fileName.'.js"></script>';
		return $data;
	}

	function includeCss($fileName) {
		$data = '<link rel="stylesheet" media="screen,projection" type="text/css" href="'.BASE_PATH.'/css/'.$fileName.'.css"/>';
		return $data;
	}
	
	function includeImg($fileName, $alt){
		$data = '<img src="'.BASE_PATH.'/img/'.$fileName.'" '.((strlen($alt)>0) ? 'alt="'.$alt.'"' : 'alt="'.$fileName.'"').'/>';
		return $data;
	}
	
	function javascript_tag($code){
		$data = '<script type="text/JavaScript">
        //<![CDATA['.	
		$code
		.'
        //]]>
        </script>';
		return $data;		
	}
	
	function css_tag($code){
		$data = '<style type="text/css">'.	
		$code
		.'
        </style>';
		return $data;	
	}
	
}