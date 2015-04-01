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

	if(!isset($_GET['type'])) {
		$_GET['type'] = '';
	}
	if(!isset($_GET['term'])) {
		$_GET['term'] = '';
	}
	
	$s = '';
	if(isset($_GET['base'])) {
		$s = trim($_GET['base']).' ';
	}
	
	$s .= trim($_GET['term']);
	abr('searchText', $s);		

	$limit = 20;
	$start = (PAGE-1)*$limit;

	require_once ROOT_PATH.'/apps/users/models/users.class.php';
	$usersClass = new users();

	
#搜索位置
	switch($_GET['type']) {
		case 'users':
			
			abr('type', 'users');
			
			if(!isset($_GET['sort_by'])) {
				$_GET['sort_by'] = '';
			}
			switch($_GET['sort_by']) {
				case 'name':
					$order = '`username`';
					break;
				case 'average_rating':
					$order = '`rating`';
					break;
				case 'sales_count':
					$order = '`sales`';
					break;
				
				default:
					$order = '`register_datetime`';
					break;
			}
			
			$users = $usersClass->getAll($start, $limit, " `status` = 'activate' AND (`username` = '".sql_quote($s)."' OR `profile_desc` LIKE '%".sql_quote($s)."%') ", "$order ASC");
			abr('results', $users);
			
			abr('paging', paging('/'.$languageURL.'search/?type=users&term='.$s.'&p=', '&sort_by='.$_GET['sort_by'], PAGE, $limit, $usersClass->foundRows));
			
			break;
#在用户中结束搜索			

		case 'collections':
				
			abr('type', 'collections');
			
			require_once ROOT_PATH.'/apps/collections/models/collections.class.php';
			$collectionsClass = new collections();
						
			if(!isset($_GET['sort_by'])) {
				$_GET['sort_by'] = '';
			}
			switch($_GET['sort_by']) {
				case 'name':
					$order = '`name`';
					break;
				case 'average_rating':
					$order = '`rating`';
					break;
				
				default:
					$order = '`datetime`';
					break;
			}
			
			$collections = $collectionsClass->getAll($start, $limit, " `public` = 'true' AND (`name` = '".sql_quote($s)."' OR `text` LIKE '%".sql_quote($s)."%') ", false, "$order ASC");
			if(is_array($collections)) {
				$users = $usersClass->getAll(0, 0, $collectionsClass->usersWhere);
				abr('users', $users);
			}
			abr('results', $collections);
			
			abr('paging', paging('/'.$languageURL.'search/?type=collections&term='.$s.'&p=', '&sort_by='.$_GET['sort_by'], PAGE, $limit, $collectionsClass->foundRows));
			
			break;
#在书签集中结束搜索						
			
		default:
			
			abr('type', 'files');
			
			require_once ROOT_PATH.'/apps/items/models/items.class.php';
			$itemsClass = new items();
						
			if(!isset($_GET['sort_by'])) {
				$_GET['sort_by'] = '';
			}
			switch($_GET['sort_by']) {
				case 'name':
					$order = '`name` ASC';
					break;
				case 'average_rating':
					$order = '`rating` DESC';
					break;
				case 'sales_count':
					$order = '`sales` DESC';
					break;
				case 'average_rating':
					$order = '`price` ASC';
					break;
					
				default:
					$order = '`datetime` DESC';
					break;
			}

#分类
			$whereQuery = '';
			$pagingUrl = '';
			if(isset($_GET['categories']) && is_array($_GET['categories'])) {
				foreach($_GET['categories'] as $c=>$o) {
					if($whereQuery != '') {
						$whereQuery .= ' OR ';
					}
					$whereQuery .= " `categories` LIKE '%,".intval($c).",%' ";
					$pagingUrl .= '&categories['.$c.']=1';
				}
				$whereQuery = " AND (".$whereQuery.") ";
			}

#加载书签集作品
			if(isset($_GET['collection_id']) && is_numeric($_GET['collection_id'])) {
				
				require_once ROOT_PATH.'/apps/collections/models/collections.class.php';
				$collectionsClass = new collections();
				
				$items = $collectionsClass->getItems($_GET['collection_id'], $start, $limit, " AND `status` = 'active' AND (`name` = '".sql_quote($s)."' OR `description` LIKE '%".sql_quote($s)."%') ".$whereQuery, "$order", true);
				if(is_array($items)) {
					$users = $usersClass->getAll(0, 0, $collectionsClass->usersWhere);
					abr('users', $users);
				}
				abr('results', $items);
				
				abr('paging', paging('/'.$languageURL.'search/?type=files&term='.$s.'&p=', $pagingUrl.'&sort_by='.$_GET['sort_by'], PAGE, $limit, $collectionsClass->foundRows));
				
			}				
			else {		
			
				$items = $itemsClass->getAll($start, $limit, " `status` = 'active' AND (`name` = '".sql_quote($s)."' OR `description` LIKE '%".sql_quote($s)."%') ".$whereQuery, "$order");
				if(is_array($items)) {
					$users = $usersClass->getAll(0, 0, $itemsClass->usersWhere);
					abr('users', $users);
				}
				abr('results', $items);
				
				abr('paging', paging('/'.$languageURL.'search/?type=files&term='.$s.'&p=', $pagingUrl.'&sort_by='.$_GET['sort_by'], PAGE, $limit, $itemsClass->foundRows));
				
			}

#加载类别
			require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
			$categoriesClass = new categories();
		
			$categories = $categoriesClass->getAll();
			abr('categories', $categories);	
			
			
			break;
#在类别中结束搜索			
	}	
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'search/" title="">'.$langArray['searching'].'</a>');		
	
	
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