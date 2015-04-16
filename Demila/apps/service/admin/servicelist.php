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
	$data = $service->getAllfromuser(START, LIMIT,$_GET['id']);
	abr('data',$data);
	$p = paging ( "?m=" . $_GET ['m'] . "&c=servicelist&p=", '&id='.$_GET['id'], PAGE, LIMIT, $service->foundRows );
	abr ( 'paging', $p );
?>