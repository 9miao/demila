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

_setView (__FILE__);
_setTitle ( $langArray ['edit'] );

	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		refresh('?m='.$_GET['m'].'&c=withdraws', 'INVALID ID', 'error');
	}

	$cms = new deposit();
	$data = $cms->getWithdraw($_GET['id']);
	abr('data', $data);
	
	$usersClass = new users();
	
	$user = $usersClass->get($data['user_id']);
	abr('user', $user);

	if(isset($_POST['edit'])) {
		$status = $cms->payoutWithdraw();
		if ($status !== true) {			
			addErrorMessage($status, '', 'error');
		} else {
			refresh ( "?m=" . $_GET ['m'] . "&c=withdraws", $langArray ['complete_withdraw'] );
		}
	}
	else {
		$_POST = $data;
	}
	
	
?>