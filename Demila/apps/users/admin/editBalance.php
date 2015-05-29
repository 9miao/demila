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

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	refresh('?m='.$_GET['m'].'&c=list', 'INVALID ID', 'error');
}

#加载余额
require_once ROOT_PATH.'/apps/users/models/balance.class.php';
$balanceClass = new balance();

$row = $balanceClass->get($_GET['id']);

if(!$row) {
	refresh('?m='.$_GET['m'].'&c=list', 'INVALID ID', 'error');
}

$_GET['user_id'] = $row['user_id'];
if (!isset ( $_POST ['edit'] )) {
	$_POST['balance'] = $row['deposit'];
}

if(!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
	refresh('?m='.$_GET['m'].'&c=list', 'INVALID ID', 'error');
}

$cms = new users();
$user = $cms->get($_GET['user_id']);
if(!$user) {
	refresh('?m='.$_GET['m'].'&c=list', 'INVALID ID', 'error');
}

_setTitle ( $user['username'] . ' › ' . $langArray ['balance1'] );

if (isset ( $_POST ['edit'] )) {
	
	$status = $balanceClass->edit ();
	if ($status !== true) {
		abr('error', $status);
	} else {
		refresh ( "?m=" . $_GET ['m'] . "&c=balance&id=" . $_GET['user_id'], $langArray ['add_complete'] );
	}	
}
require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';