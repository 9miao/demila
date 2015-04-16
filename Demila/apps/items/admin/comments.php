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

_setView(__FILE__);

	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		refresh('?m='.$_GET['m'].'&c=list', 'WRONG ID', 'error');
	}

	$cms = new comments();
	
	if(isset($_GET['report']) && is_numeric($_GET['report'])) {
		$cms->reported($_GET['report']);
	}
	
	$data = $cms->getAll(START, LIMIT, " `item_id` = '".$_GET['id']."' ");
	if(is_array($data)) {
		
		require_once ROOT_PATH.'/apps/users/models/users.class.php';
		$usersClass = new users();
		
		$users = $usersClass->getAll(0, 0, $cms->usersWhere);
		abr('users', $users);
		
	}
	abr('data', $data);

	$p = paging ( "?m=" . $_GET ['m'] . "&c=comments&id=".$_GET['id']."&p=", "", PAGE, LIMIT, $cms->foundRows );
	abr ( 'paging', $p );
	
	$itemsClass = new items();

	$item = $itemsClass->get($_GET['id']);
	
	_setTitle($item['name'].' &rsaquo; '.$langArray['comments']);
	
?>