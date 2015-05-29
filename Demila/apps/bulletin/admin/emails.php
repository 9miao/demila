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
_setTitle ( $langArray ['bulletin'] );

	require_once ROOT_PATH . "/apps/bulletin/models/bulletin.class.php";
	$cms = new bulletin ( );
	
	if(isset($_GET['subscribe']) && is_numeric($_GET['subscribe'])) {
		$cms->changeSubscribe($_GET['subscribe'], 'true');
	}
	elseif(isset($_GET['unsubscribe']) && is_numeric($_GET['unsubscribe'])) {
		$cms->changeSubscribe($_GET['unsubscribe'], 'false');
	}
	
	$data = $cms->getAllEmails(START, LIMIT);
	abr('data', $data);

	$p = paging ( "?m=" . $_GET ['m'] . "&c=emails&p=", "", PAGE, LIMIT, $cms->foundRows );
	abr ( 'paging', $p );
require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';

?>