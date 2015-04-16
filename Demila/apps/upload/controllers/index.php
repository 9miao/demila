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

_setView(__FILE__);

if(!check_login_bool()) {
		$_SESSION['temp']['golink'] = '/'.$languageURL.'upload/index/';
		refresh('/'.$languageURL.'sign_in/');
}

	if($_SESSION['user']['quiz'] != 'true') {
		refresh('/'.$languageURL.'quiz/');
	} 
	
?>