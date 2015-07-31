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
		refresh('?m='.$_GET['m'].'&c=queue', 'WRONG ID', 'error');
	}

	if(!isset($_GET['p'])) {
		$_GET['p'] = '1';
	}

	$cms = new items ( );

	require_once ROOT_PATH.'/apps/users/models/users.class.php';
	$usersClass = new users();

    $data = $cms->get($_GET['id'], false);
    $data['preview'] = $cms->get_theme_preview($_GET['id']);
    $data['user'] = $usersClass->get($data['user_id']);
    //路径
    $data["thumbnail"] =DATA_SERVER.'/uploads/items/'.$_GET['id'].'/'. $data["thumbnail"];
    $data["theme_preview"]= DATA_SERVER.'/uploads/items/'.$_GET['id'].'/'. $data["theme_preview"];
    $data["main_file"]= DATA_SERVER.'/uploads/items/'.$_GET['id'].'/'. $data["main_file"];
    abr('data', $data);

if(isset($_POST['submit'])) {

		if($_POST['action'] == 'approve') {
			$s = $cms->approve($_GET['id']);
			if($s == true) {
				refresh("?m=".$_GET['m']."&c=queue&p=".$_GET['p'], $langArray['complete_approve_item']);
			}
			else {
				addErrorMessage($s, '', 'error');
			}
}
		elseif($_POST['action'] == 'unapprove') {
			$s = $cms->unapprove($_GET['id']);
			if($s == true) {

				refresh("?m=".$_GET['m']."&c=queue&p=".$_GET['p'], $langArray['complete_unapprove_item']);
			}
			else {
				addErrorMessage($s, '', 'error');
			}
		}
		elseif($_POST['action'] == 'delete') {
			$s = $cms->unapproveDelete($_GET['id']);
			if($s == true) {
				refresh("?m=".$_GET['m']."&c=queue&p=".$_GET['p'], $langArray['complete_delete_item']);
			}
			else {
				addErrorMessage($s, '', 'error');
			}
		}		
	}
	
#加载类别
	require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
	$categoriesClass = new categories();

	$categories = $categoriesClass->getAll();
	abr('categories', $categories);

    //获取所有推荐标签
    require_once ROOT_PATH.'/apps/tags/models/tags.class.php';
    $tagsClass = new tags();

    //获取当前作品推荐标签
    $tag_relation = $tagsClass ->get_tags_by_item_id($_GET['id']);
    abr('item_tags',json_encode($tag_relation));


require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';
?>