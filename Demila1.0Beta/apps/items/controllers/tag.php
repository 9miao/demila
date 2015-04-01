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

	$tag_type = get_id(2);	
	$tag = get_id(3);
	
	require_once ROOT_PATH.'/apps/tags/models/tags.class.php';
	$tagsClass = new tags();
	
_setTitle($tag);	
	
	$t = $tagsClass->isExistTag($tag);
	if(is_array($t)) {
		
		$itemsClass = new items();
		
#加载书签集作品
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
		
		$items = $itemsClass->getTagItems($t['id'], $tag_type, $start, $limit, " AND i.`status` = 'active' ", $order);
		if(is_array($items)) {
			
			require_once ROOT_PATH.'/apps/users/models/users.class.php';
			$usersClass = new users();
			
			$users = $usersClass->getAll(0, 0, $itemsClass->usersWhere);
			abr('users', $users);
					
		}
		abr('items', $items);
	
		abr('paging', paging('/'.$languageURL.'items/tag/'.$tag_type.'/'.$tag.'/?p=', '&sort_by='.$_GET['sort_by'].'&order='.$_GET['order'], PAGE, $limit, $itemsClass->foundRows));	
	
	#加载分类
		require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
		$categoriesClass = new categories();
	
		$categories = $categoriesClass->getAll();
		abr('categories', $categories);	
	
#面包屑	
		abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'items/tag/'.$tag_type.'/'.$tag.'/" title="">'.$tag.'</a>');		
		
	}
	
	$discount = array();
	if($meta['prepaid_price_discount']) {
		if(strpos($meta['prepaid_price_discount'], '%')) {
			$discount = $meta['prepaid_price_discount'];
		} else {
			$discount = $currency['symbol'] . $meta['prepaid_price_discount'];
		}
	}
	abr('right_discount', $discount);	

?>