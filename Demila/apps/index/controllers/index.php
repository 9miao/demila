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

 
	require_once ROOT_PATH.'/apps/users/models/users.class.php';
	$usersClass = new users();
			
	$users = $usersClass->getAll(0, 0, $itemsClass->usersWhere);
	abr('users', $users);


#推荐作品
	$weeklyItems = $itemsClass->getAll(0, 10, " `status` = 'active' AND `weekly_to` >= '".date('Y-m-d')."' ", "`datetime` DESC");
	abr('weeklyItems', $weeklyItems);
	
	if($itemsClass->foundRows > 10) {
		abr('haveWeekly', 'yes');
	}

#加载分类
		require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
		$categoriesClass = new categories();
	
		$categories = $categoriesClass->getAll();
		abr('categories', $categories);	 
	
#近期作品
	$recentItems = $itemsClass->getAll(0, 40, " `status` = 'active' ", '`datetime` DESC');
	kshuffle($recentItems);
	abr('recentItems', $recentItems);
	
    //免费作品
	$freeItem = $itemsClass->getAll(0, 0, " `status` = 'active' AND `free_file` = 'true' ");
	abr('freeItem', $freeItem);
	
#推荐作者
	require_once ROOT_PATH.'/apps/users/models/users.class.php';
	$usersClass = new users();
    //获取用户信息及粉丝
	$featuredAuthor = $usersClass->getAll(0, 0, " `status` = 'activate' AND `featured_author` = 'true' ", 'RAND()');
	// if(is_array($featuredAuthor)) {
	// 	//作者的一些作品
	// 	$featuredItems = array();
    //      foreach($featuredAuthor as $itear){
	// 	    $featuredItems[$itear['user_id']] = $itemsClass->getAll(0, 2, " `status` = 'active' AND `user_id` = '".intval($itear['user_id'])."' ");
	// 	}
	// 	abr('featuredItems', $featuredItems);
		
	// 	abr('featuredAuthorInfo', langMessageReplace($langArray['featured_author_info'], array(
	// 															'USERNAME' => $featuredAuthor['username'],
	// 															'MONTH' => $langArray['monthArr'][date('n', strtotime($featuredAuthor['register_datetime']))],
	// 															'YEAR' => date('Y', strtotime($featuredAuthor['register_datetime'])),
	// 															'ITEMS' => $featuredAuthor['items'],
	// 															'SALES' => $featuredAuthor['sales']
	// 														)));
	// }
	abr('featuredAuthor', $featuredAuthor);
#用户关注的作者的最新作品
	if(check_login_bool()) {
		$following = $usersClass->getFollowersID($_SESSION['user']['user_id']);
		if(is_array($following)) {
			$whereQuery = '';
			foreach($following as $f) {
				if($whereQuery != '') {
					$whereQuery .= ' OR ';
				}
				$whereQuery .= " `user_id` = '".intval($f['follow_id'])."' ";
			}
			
			$followingItems = $itemsClass->getAll(0, 0, " `status` = 'active' AND ($whereQuery) ", "`datetime` DESC");
			abr('followingItems', $followingItems);
			
			abr('followingItemsCount', $itemsClass->foundRows);
			abr('emptyThumb', (10-$itemsClass->foundRows));
		}
	}
#王牌作者
	

		$topAuthors = $usersClass->getAll(0, 9, " `status` = 'activate' and `sales` > 0 ", "`sales` DESC");
		abr('topAuthors', $topAuthors);
		
		abr('topAuthorsCount', $usersClass->foundRows);
		abr('emptyThumb', (9-$usersClass->foundRows));
		
	

#随机分类
	$randCategories = array_rand($mainCategories, 5);
	abr('randCategories', $randCategories);	
	
#最低价格
	$lowPrice = $itemsClass->getAll(0, 1, " `status` = 'active' ", "`price` ASC");
	if(is_array($lowPrice)) {
		$lowPrice = array_shift($lowPrice);
		$lowPrice = $lowPrice['price'];
	}
	abr('lowPrice', $lowPrice);
	
#快讯
	
	require_once ROOT_PATH.'/apps/qnews/models/qnews.class.php';
	$qnews = new qnews();
	$data = array();
	foreach($qnews->getAll(0, 1, "`visible` = 'true'") AS $key => $value) {
		if($value['photo']) {
			$data[$key] = $value;
			$data[$key]['thumb'] = 'static/uploads/qnews/260x140/' . $value['photo'];
		}
	}
	
	abr('qnews_data', $data);
	
#面包屑
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a>');
	

	
?>