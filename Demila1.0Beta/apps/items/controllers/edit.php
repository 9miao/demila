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

if(!check_login_bool()) {
	refresh('/'.$languageURL.'users/login/');
}

	$itemID = get_id(2);
	
	$itemsClass = new items();
	$item = $itemsClass->get($itemID);
	if(!is_array($item) || $item['user_id'] != $_SESSION['user']['user_id']) {
		header("HTTP/1.0 404 Not Found");
        header("Location: http://". DOMAIN ."/error");
	}
	abr('item', $item);


#加载分类
	require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
	$categoriesClass = new categories();
	
	$categories = $categoriesClass->getAll();
	abr('categories', $categories);	
	

#加载属性
	require_once ROOT_PATH.'/apps/attributes/models/attributes.class.php';
	$attributesClass = new attributes();
	
	$first_category = 0;
	foreach($item['categories'] AS $cat) {
		if(is_array($cat)) {
			foreach($cat AS $c) {
				if($c) {
					$first_category = $c;
					break;
				}
			}
		} else {
			if($cat) {
				$first_category = $cat;
				break;
			}
		}
	}
	
	//$first_category = array_shift($item['categories']);
	$attributes = $attributesClass->getAllWithCategories(" `visible` = 'true' AND `categories` LIKE '%,".(int)$first_category.",%'");
	abr('attributes', $attributes);
	
	///////////////////
	
	if(!isset($_POST['category'])) {
		if(isset($_POST['save'])) {
			$_POST['category'] = 0;
		} else {
			if($item['categories']) {
				foreach($item['categories'] AS $c) {
					$_POST['category'][] = end($c);
				}
			} else {
				$_POST['category'] = 0;
			}	
		}
	}
	
	$allCategories = $categoriesClass->getAllWithChilds(0, " `visible` = 'true' ");
	$categoriesSelect = $categoriesClass->generateSelect($allCategories, $_POST['category'], $first_category);
	abr('categoriesSelect', $categoriesSelect);
	
	/////////////////////
	
#检查作品更新队列
	if($itemsClass->isInUpdateQueue($itemID)) {
		abr('inUpdateQueue', 'yes');
	}	
	
	
	if(isset($_POST['save'])) {
		$s = $itemsClass->edit($itemID);
		if($s === true) {
			refresh('/'.$languageURL.'items/'.$itemID.'/', $langArray['complete_update_item'], 'complete');
		}
		else {
			$message = '<ul>';
			foreach($s as $e) {
				$message .= '<li>'.$e.'</li>';
			}
			$message .= '</ul>';
			addErrorMessage($message, '', 'error');
		}
	}
	elseif(isset($_POST['upload'])) {
		$s = $itemsClass->edit_upload($itemID);
		if($s === true) {
			refresh('/'.$languageURL.'items/'.$itemID.'/', $langArray['complete_update_upload_item'], 'complete');
		}
		else {
			$message = '<ul>';
			foreach($s as $e) {
				$message .= '<li>'.$e.'</li>';
			}
			$message .= '</ul>';
			addErrorMessage($message, '', 'error');
		}
	}
	elseif(isset($_POST['delete'])) {
		$itemsClass->delete($itemID);
		refresh('/'.$languageURL.'users/dashboard/', $langArray['complete_delete_item'], 'complete');
	}
	else {
		$_POST = $item;
		unset($_POST['tags']);
		if( isset($item['tags']) && is_array($item['tags']) ) {
			foreach($item['tags'] as $type=>$arr) {
				foreach($arr as $t) {
					if(!isset($_POST['tags'][$type])) {
						$_POST['tags'][$type] = '';
					}
					$_POST['tags'][$type] .= $t.',';
				}
			}
		}
	}
	
	abr('sessID', session_id());
	
?>