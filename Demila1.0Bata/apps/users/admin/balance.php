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

require_once ROOT_PATH.'/apps/users/models/users.class.php';
$usersClass = new users();

$users = $usersClass->getAll(0, 0, @$cms->usersWhere);
abr('users', $users);
		

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	refresh('?m='.$_GET['m'].'&c=list', 'INVALID ID', 'error');
}

$cms = new users();
$user = $cms->get($_GET['id']);
if(!$user) {
	refresh('?m='.$_GET['m'].'&c=list', 'INVALID ID', 'error');
}

_setTitle ( $user['username'] . ' › ' . $langArray ['balance1'] );

#加载余额
require_once ROOT_PATH.'/apps/users/models/balance.class.php';
$balanceClass = new balance();

$data = $balanceClass->getUserBalance($_GET['id'], null);

abr('data', $data); 


?>