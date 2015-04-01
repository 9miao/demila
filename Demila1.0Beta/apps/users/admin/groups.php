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
_setTitle ( $langArray ['groups'] );
	require_once ROOT_PATH.'/apps/users/models/groups.class.php';
	$cms = new groups ( );

	$data = $cms->getAll(START, LIMIT);
	abr('data', $data);

	$p = paging ( "?m=" . $_GET ['m'] . "&c=groups&p=", "", PAGE, LIMIT, $cms->foundRows );
	abr ( 'paging', $p );
?>