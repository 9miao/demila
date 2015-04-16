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
_setTitle($langArray['featured_files']);

	abr('checkItemsType', 'yes');

	require_once ROOT_PATH.'/apps/users/models/users.class.php';
	$usersClass = new users();
		
#推荐作品
	$sixMonthsAgo = date('Y-m-d', mktime(0, 0, 0, (date('m')-6), date('d'), date('Y')));

	$items = $itemsClass->getAll(0, 0, " `status` = 'active' AND `weekly_to` >= '".date('Y-m-d')."' AND `weekly_to` >= '".$sixMonthsAgo."' ", "`datetime` DESC");
	
	if(is_array($items)) {
		
		abr('topItem', array_shift($items));
		
		$users = $usersClass->getAll(0, 0, $itemsClass->usersWhere);
		abr('users', $users);
				
	}
	abr('items', $items);
	
	#加载分类
	require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
	$categoriesClass = new categories();

	$categories = $categoriesClass->getAll();
	abr('categories', $categories);	
	
#推荐作者
	$featuredAuthors = $usersClass->getAll(0, 0, " `status` = 'activate' AND `featured_author` = 'true' ");
	abr('featuredAuthors', $featuredAuthors);

#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'items/feature/" title="">'.$langArray['featured_files'].'</a>');		
	 
?>