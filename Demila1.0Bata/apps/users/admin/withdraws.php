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
_setTitle ( $langArray ['withdraws'] );

	$cms = new deposit ( );

	$data = $cms->getWithdraws(START, LIMIT);
	if(is_array($data)) {
		require_once ROOT_PATH.'/apps/users/models/users.class.php';
		$usersClass = new users();
		
		$users = $usersClass->getAll(0, 0, $cms->usersWhere);
		abr('users', $users);
	}
	abr('data', $data);

	$p = paging ( "?m=" . $_GET ['m'] . "&c=withdraws&p=", "", PAGE, LIMIT, $cms->foundRows );
	abr ( 'paging', $p );
?>