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
_setTitle ( $langArray ['edit'] );

	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		refresh('?m='.$_GET['m'].'&c=list', 'INVALID ID', 'error');
	}

	if(!isset($_GET['p'])) {
		$_GET['p'] = '';
	}	
	
	$cms = new items();
	
	require_once ROOT_PATH.'/apps/users/models/users.class.php';
	$usersClass = new users();
	
	$data = $cms->get($_GET['id'], false);
	$data['user'] = $usersClass->get($data['user_id']);
    //路径
    $data["thumbnail"] =DATA_SERVER.'/uploads/items/'.$_GET['id'].'/'. $data["thumbnail"];
    $data["theme_preview"]= DATA_SERVER.'/uploads/items/'.$_GET['id'].'/'. $data["theme_preview"];
    $data["main_file"]= DATA_SERVER.'/uploads/items/'.$_GET['id'].'/'. $data["main_file"];

    //获取所有预览图
    $data['preview'] = $itemsClass->get_theme_preview($_GET['id']);
    abr('data', $data);
	
#加载属性
	require_once ROOT_PATH.'/apps/attributes/models/attributes.class.php';
	$attributesClass = new attributes();
	
	if(isset($data['categories'][0]) && is_array($data['categories'][0])) {
		$first_category = array_shift($data['categories'][0]);
	} else {
		$first_category = 0;
	}
	
	$attributes = $attributesClass->getAllWithCategories(" `visible` = 'true' AND `categories` LIKE '%,".(int)$first_category.",%' ");
	abr('attributes', $attributes);
	
	if(isset($_POST['edit'])) {
		$status = $cms->edit ($_GET['id'], true);
		if ($status !== true) {
			abr('error', $status);
		} else {
			refresh ( "?m=" . $_GET ['m'] . "&c=list&p=".$_GET['p'], $langArray ['edit_complete'] );
		}
	}
	else {
		$_POST = $data;
	}	
	
#加载类别
	require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
	$categoriesClass = new categories();

	if(!isset($_POST['categories'])) {
		$_POST['categories'] = array();
	}
	
	$tmp = array();
	if(isset($_POST['category'])) {
		$tmp = (array)$_POST['category'];
	} else {
		foreach($_POST['categories'] AS $row => $categories1) {
			$cid = end($categories1);
			$tmp[$cid] = $cid;
		} 
	}

	
	$allCategories = $categoriesClass->getAllWithChilds(0, " `visible` = 'true' ");
	$categoriesSelect = $categoriesClass->generateSelect($allCategories, $tmp, (int)$first_category);
	abr('categoriesSelect', $categoriesSelect);

    //获取所有推荐标签
    require_once ROOT_PATH.'/apps/tags/models/tags.class.php';
    $tagsClass = new tags();
    $tags_all = $tagsClass->getAll();
    $all_tags = json_encode($tags_all);
    abr('all_tags',$all_tags);

    //获取当前作品推荐标签
    $tag_relation = $tagsClass ->get_tags_by_item_id($_GET['id']);
    abr('item_tags',json_encode($tag_relation));
    abr('sessID', session_id());

require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';
?>