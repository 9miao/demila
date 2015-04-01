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
_setTitle($langArray['categories_list_setTitle']);

	$categoryID = get_id(1);
	if(is_numeric($categoryID) || $categoryID == 'all') {
	
	_setTitle($langArray['all_categories_setTitle']);
	
		abr('checkItemsType', 'yes');
		
		$whereQuery = '';
		if(is_numeric($categoryID)) {

			$category = $categoriesClass->get($categoryID);
			if(!is_array($category) || $category['visible'] == 'false') {
				refresh('/'.$languageURL.'categories/', $langArray['wrong_category'], 'error');
			}
			abr('category', $category);
			
			$allCategories = $categoriesClass->getAll(0, 0, " `visible` = 'true' ");
			$categoryParent = $categoriesClass->getCategoryParents($allCategories, $categoryID);
			$categoryParent = explode(',', $categoryParent);
			$categoryParent = array_reverse($categoryParent);
			array_shift($categoryParent);
			
			$whereQuery = " AND `id` IN (SELECT `item_id` FROM `items_to_category` WHERE `categories` LIKE '%,".intval($categoryID).",%') ";
		}
		else {
			$categoryParent = array();
		}		
		
		$allCategories = $categoriesClass->getAllWithChilds(0, " `visible` = 'true' ");
		$categoriesList = $categoriesClass->generateList2($allCategories, $categoryParent);
		abr('categoriesList2', $categoriesList);
			
#加载分类中的热门作品
		$limit = 20;
		$start = (PAGE-1)*$limit;
		
		$order = '';
		if(!isset($_GET['sort_by'])) {
			$_GET['sort_by'] = '';
		}
		switch($_GET['sort_by']) {
			case 'name':
				$order = '`name`';
				break;
			case 'root_category':
				$order = '`categories`';
				break;
			case 'average_rating':
				$order = '`rating`';
				break;
			case 'sales_count':
				$order = '`sales`';
				break;
			case 'cost':
				$order = '`price`';
				break;
			
			default:
				$order = '`datetime`';
				break;
		}
		if(!isset($_GET['order']) || $_GET['order'] == '' || $_GET['order'] == 'desc') {
			$_GET['order'] = 'desc';
			$order .= ' DESC';
		}
		else {
			$_GET['order'] = 'asc';
			$order .= ' ASC';
		}
		
		$items = $itemsClass->getAll($start, $limit, " `status` = 'active' ".$whereQuery, $order);
		if(is_array($items)) {
			
			require_once ROOT_PATH.'/apps/users/models/users.class.php';
			$usersClass = new users();
			
			$users = $usersClass->getAll(0, 0, $itemsClass->usersWhere);
			abr('users', $users);
					
		}
		
		

		abr('items', $items);
	
		abr('paging', paging('/'.$languageURL.'categories/'.$categoryID.'/?p=', '&sort_by='.$_GET['sort_by'].'&order='.$_GET['order'], PAGE, $limit, $itemsClass->foundRows));	
	
#加载类别
		require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
		$categoriesClass = new categories();
	
		$categories = $categoriesClass->getAll();
		abr('categories', $categories);		
			
#面包屑	
		if(isset($categoryID) && is_numeric($categoryID)) {

#设置元信息	
			if($category['meta_title'] != '') {
				$smarty->assign('title', $category['meta_title']);
			}
			else {
				$smarty->assign('title', $category['name']); 
			}
			
			if($category['meta_keywords'] != '') {
				$smarty->assign('meta_keywords', $category['meta_keywords']);
			}
			if($category['meta_description'] != '') {
				$smarty->assign('meta_description', $category['meta_description']);
			}
			
			abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'categories/" title="">'.$langArray['categories'].'</a> \ <a href="/'.$languageURL.'categories/'.$category['id'].'" title="">'.$category['name'].'</a>');
		}
		else {
			abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'categories/" title="">'.$langArray['categories'].'</a> \ <a href="/'.$languageURL.'categories/all" title="">'.$langArray['all_files'].'</a>');
		}		
		
	}	
	else {
		$allCategories = $categoriesClass->getAllWithChilds(0, " `visible` = 'true' ");
		$categoriesList = $categoriesClass->generateList($allCategories);
		abr('categoriesList', $categoriesList);		

#面包屑	
		abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'categories/" title="">'.$langArray['categories'].'</a>');
	}
	
?>