<?php
// +----------------------------------------------------------------------
// | Demila [ Beautiful Digital Content Trading System ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://demila.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Email author@demila.org
// +----------------------------------------------------------------------

$lang_file_for_module = null;
	if($_GET['module'] == 'admin') {
		if(isset($_GET['m'])) {
			$lang_file_for_module = ROOT_PATH.'/apps/' . $_GET['m'] . '/language/lang.php';
		}
	} else { 
		$lang_file_for_module = ROOT_PATH.'/apps/' . $_GET['module'] . '/language/lang.php';
	}

	require_once ROOT_PATH.'config/lang.php';
	
	if($lang_file_for_module && file_exists($lang_file_for_module)) {
		require_once $lang_file_for_module;
	}

	abr("lang", $langArray);
    $languageURL = 'index.php/';
	$languageURL .= '';
	
	abr("languageURL", $languageURL);
	
?>