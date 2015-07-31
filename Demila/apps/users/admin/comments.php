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

_setTitle ( $langArray ['report_comments'] );


	require_once ROOT_PATH.'/apps/items/models/comments.class.php';
	$commentsClass = new comments();
	
#检查评论
	if(isset($_GET['check']) && is_numeric($_GET['check'])) {
		$commentsClass->reported($_GET['check']);
		refresh('?m='.$_GET['m'].'&c=comments');
	}	

	if(isset($_GET['item']) && is_numeric($_GET['item'])) {
			$url = 'http://' . $_SERVER ["SERVER_NAME"] . '' . '/items/comments/'. $_GET['item'];
			header("Location: $url");
	}
	
	$data = $commentsClass->getAll(START, LIMIT, " `report_by` <> '0' ");
	abr('data', $data);

	$p = paging ( "?m=" . $_GET ['m'] . "&c=comments&p=", "", PAGE, LIMIT, $commentsClass->foundRows );
	abr ( 'paging', $p );
	
	if(is_array($data)) {
		
		$usersClass = new users();
		
		$usersWhere = '';
		foreach($data as $d) {
			$usersWhere[$d['report_by']] = $d['report_by'];
		}
		
		$usersWhere = '`user_id` = '.implode(' OR `user_id` = ', $usersWhere);
		
		$users = $usersClass->getAll(0, 0, $usersWhere);
		abr('users', $users);
	}

require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';
?>