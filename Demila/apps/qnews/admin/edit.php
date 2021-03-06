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

_setView ( ROOT_PATH . "/apps/" . $_GET ['m'] . "/admin/add.php" );
_setTitle ( $langArray ['edit'].' '.@$langArray['qnews'] );

	if(!isset($_GET['fid']) || !is_numeric($_GET['fid'])) {
		refresh('?m='.$_GET['m'].'&c=list&id='.$_GET['id'], 'INVALID ID', 'error');
	}

	$cms = new qnews();
	
	if(isset($_POST['edit'])) {
		$status = $cms->edit ($_GET['fid']);
		if ($status !== true) {			
			abr('error', $status);
		} else {
			refresh ( "?m=" . $_GET ['m'] . "&c=list", $langArray ['edit_complete'] );
		}
	}
	else {
		$_POST = $cms->get($_GET['fid']);
		$_POST['thumb'] = '/static/uploads/qnews/192x64/' . $_POST['photo'];
	}

require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';
?>