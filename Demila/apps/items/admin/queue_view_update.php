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
_setTitle($langArray['queue']);

	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		refresh('?m='.$_GET['m'].'&c=queue_update', 'WRONG ID', 'error');
	}

	if(!isset($_GET['p'])) {
		$_GET['p'] = '';
	}

	$cms = new items ( );
	
	require_once ROOT_PATH.'/apps/users/models/users.class.php';
	$usersClass = new users();

    $data = $cms->getForUpdate($_GET['id']);
    //获取所有预览图
    $data['preview'] = $itemsClass->get_theme_preview($data['item_id'],true);
    $data['preview_status'] = 0;
    if(empty($data['theme_preview'])){
        $data['preview_status'] = 1;
    }
    //路径
    $data["thumbnail"] = empty($data["thumbnail"])?"":DATA_SERVER.'/uploads/items/'.$data['item_id'].'/'. $data["thumbnail"];
    $data["first_preview"]= empty($data["first_preview"])?"":DATA_SERVER.'/uploads/items/'.$data['item_id'].'/'. $data["first_preview"];
    $data["main_file"]= empty($data["main_file"])?"":DATA_SERVER.'/uploads/items/'.$data['item_id'].'/'. $data["main_file"];
    abr('data', $data);

	$item = $cms->get($data['item_id']);

	if(!is_array($item)) {
		refresh('?m='.$_GET['m'].'&c=queue_update', 'WRONG ID', 'error');
	}
	$item['user'] = $usersClass->get($item['user_id']);
    $item["thumbnail"] =DATA_SERVER.'/uploads/items/'.$data['item_id'].'/'. $item["thumbnail"];
    $item["theme_preview"]= DATA_SERVER.'/uploads/items/'.$data['item_id'].'/'. $item["theme_preview"];
    $item["main_file"]= DATA_SERVER.'/uploads/items/'.$data['item_id'].'/'. $item["main_file"];
	abr('item', $item);
	
	if(isset($_POST['submit'])) {
		
		if($_POST['action'] == 'approve') {
			$s = $cms->approveUpdate($_GET['id']);
			if($s === true) {
				refresh("?m=".$_GET['m']."&c=queue_update&p=".$_GET['p'], $langArray['complete_approve_item_update']);
			}
			else {
				addErrorMessage($s, '', 'error');
			}
		}
		elseif($_POST['action'] == 'delete') {
			$s = $cms->unapproveDeleteUpdate($_GET['id']);
			if($s === true) {
				refresh("?m=".$_GET['m']."&c=queue_update&p=".$_GET['p'], $langArray['complete_delete_item_update']);
			}
			else {
				addErrorMessage($s, '', 'error');
			}
		}		
	}

require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';
?>