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
	_setTitle ( $langArray ['service'] );

	$data = array();
	$service = new service();
	$data = $service->getAll(START, LIMIT);
	abr('data',$data);
	$p = paging ( "?m=" . $_GET ['m'] . "&c=service&p=", "&id=".$_GET['id'], PAGE, LIMIT, $service->foundRows );
	abr ( 'paging', $p );
require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';
?>