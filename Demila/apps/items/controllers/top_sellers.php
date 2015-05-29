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
_setTitle($langArray['popular_files']);
	abr('checkItemsType', 'yes');
	$year = get_id(2);
	$month = get_id(3);
	$day = get_id(4);
	if(!checkdate(intval($month), intval($day), intval($year))) {
		$year = date('Y');
		$month = date('m');
		$day = date('d');
	}
    $dayOfWeek = date('N', mktime(0, 0, 0, $month, $day, $year));

	$dayOfWeek = 7-$dayOfWeek;
	if($dayOfWeek > 0) {
		$endDate = date('Y-m-d', mktime(0, 0, 0, $month, ($day + $dayOfWeek), $year));
	}
	else {
		if(strlen($month) == 1) {
			$month = '0'.$month;
		}
		if(strlen($day) == 1) {
			$day = '0'.$day;
		}
		$endDate = $year.'-'.$month.'-'.$day;
	}
	$startDate = date('Y-m-d', (strtotime($endDate) - 604800));
	abr('endDate', $endDate);
	
#创建上页和下页
	if(strtotime($endDate) < strtotime(date('Y-m-d'))) {
		abr('nextDate', date('Y/m/d', (strtotime($endDate) + 604800)));
	}	
	abr('prevDate', date('Y/m/d', strtotime($startDate)));
	
	$month = date('m', strtotime($endDate)) - 1;
	
	$endMonthlyDate = date('Y-m-d', mktime(0, 0, 0, $month, date('t', mktime(0, 0, 0, $month, 1, date('Y'))), date('Y')));
	$startMonthlyDate = date('Y-m-d', mktime(0, 0, 0, ($month-3), 1, date('Y')));
	abr('endMonthlyDate', $endMonthlyDate);
	
	$endMonthlyDate2 = date('Y-m-d', mktime(0, 0, 0, $month, date('t', mktime(0, 0, 0, $month, 1, date('Y'))), date('Y')));
	$startMonthlyDate2 = date('Y-m-d', mktime(0, 0, 0, $month, 1, date('Y')));
	abr('month', $month);
	
#获取作品	
	$ordersClass = new orders();

	require_once ROOT_PATH.'/apps/users/models/users.class.php';
	$usersClass = new users();
	
	$users = $usersClass->getAll(0, 0, $itemsClass->usersWhere);
	abr('users', $users);
	
	$topSellItems = $ordersClass->getTopSellers(0, 50, " AND `paid_datetime` > '$startDate 23:59:59' AND `paid_datetime` < '$endDate 23:59:59' ");
	if(is_array($topSellItems)) {
		
		$users = $usersClass->getAll(0, 0, $ordersClass->usersWhere);
		abr('users', $users);
	}
	abr('topSellItems', $topSellItems);	
	
	$topMonthlyItems = $ordersClass->getTopSellers(0, 50, " AND `paid_datetime` > '$startMonthlyDate 00:00:00' AND `paid_datetime` < '$endMonthlyDate 23:59:59' ");
	if(is_array($topMonthlyItems)) {
		$users2 = $usersClass->getAll(0, 0, $ordersClass->usersWhere);
		abr('users2', $users2);
	}
	abr('topMonthlyItems', $topMonthlyItems);	
	
	
#加载分类
	require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
	$categoriesClass = new categories();

	$categories = $categoriesClass->getAll();
	abr('categories', $categories);
	
	
#王牌作者
	$topAuthors = $ordersClass->getTopAuthors(0, 20, " AND `paid_datetime` > '$startMonthlyDate2 00:00:00' AND `paid_datetime` < '$endMonthlyDate2 23:59:59' ");
	abr('topAuthors', $topAuthors);	
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'items/top_sellers/" title="">'.$langArray['popular_files'].'</a>');		
	

?>