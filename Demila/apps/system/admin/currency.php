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

_setView ( __FILE__ );
_setTitle ( $langArray ['language'] );

	$cms = new system ( );
	
	if(isset($_POST['save'])) {
		$cms->saveCurrency();
		refresh('?m='.$_GET['m'].'&c='.$_GET['c']);
	}
	
	$data = $cms->getCurrency();
	abr('data', $data);
require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';
	
?>