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
//用户数量
require_once ROOT_PATH.'/apps/users/models/users.class.php';
$usersClass = new users();
abr('usersCount', $usersClass->getUsersCount(" `status` = 'activate' "));

//作品数量
require_once ROOT_PATH.'/apps/items/models/items.class.php';
$itemsClass = new items();
abr('itemsCount', $itemsClass->getItemsCount());

$admin_config = array (
	'show' => true,

	'add' => true, 
	'list' => true, 
	'edit' => false
);
?>