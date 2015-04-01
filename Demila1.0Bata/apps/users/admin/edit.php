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
		refresh('?m='.$_GET['m'].'&c=list', 'INVALID ID', 'error');
	}

	$cms = new users(); 
	
	if(isset($_POST['edit'])) {
		
		$adminEdit = true;
		if(isset($personalEdit)) {
			$adminEdit = false;
		}
		
		$status = $cms->edit ($_GET['id'], $adminEdit);
		
		if ($status !== true) {			
			abr('error', $status);
		} else {
			if(isset($personalEdit)) {
				refresh ( "?m=" . $_GET ['m'] . "&c=edit&id=".$_GET['id'], $langArray ['edit_complete'] );
			}
			else {
				refresh ( "?m=" . $_GET ['m'] . "&c=list", $langArray ['edit_complete'] );
			}
		}
	}
	else {
		//独家设置
		if(isset($_POST['exclusive_false'])) {		
			$usersClass = new users();
			$usersClass->editExclusiveAuthor('false',$_GET['id']);
		}
		elseif(isset($_POST['exclusive_true'])) {		
			$usersClass = new users();
			$usersClass->editExclusiveAuthor('true',$_GET['id']);
		}

		$_POST = $cms->get($_GET['id']);
		$badges = explode(',', $_POST['badges']);
		$_POST['badges'] = array();
		foreach($badges AS $badge) {
			$_POST['badges'][] = $badge;
		}
	}
	

	$user = $cms->get($_GET['id']);
	$user['stats'] = $cms->getStatistic($_GET['id']);
	abr('user', $user);

	require_once ROOT_PATH.'/apps/'.$_GET['m'].'/models/groups.class.php';
	$g = new groups();
	
	$groups = $g->getAll();
	abr('groups', $groups);	
	
	require_once ROOT_PATH.'/apps/system/models/badges.class.php';
	$badges = new badges();
	
	$badges_data = $badges->getAll(0, 0, "`type` = 'other'");
	abr('badges', $badges_data);
	
	if(isset($_POST['badges'])) {
		if(!is_array($_POST['badges'])) {
			$_POST['badges'] = explode(',', $_POST['badges']);
		}
	} else {
		$_POST['badges'] = array();
	}
	
?>