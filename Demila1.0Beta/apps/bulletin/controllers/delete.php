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

	if(!isset($_GET['email'])) {
		refresh('/');
	}
	
	require_once ROOT_PATH . "/apps/bulletin/models/bulletin.class.php";
	$bulletinClass = new bulletin();
	
	$bulletinClass->deleteEmail($_GET['email']);
		
	addErrorMessage($_GET['email'].$langArray['complete_unsubscribe'], '', 'complete');

?>