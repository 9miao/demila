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
_setTitle($langArray['author_dashboard']);	

if(!check_login_bool()) {
		$_SESSION['temp']['golink'] = '/'.$languageURL.'author_dashboard/';
		refresh('/'.$languageURL.'sign_in/');
}

	if($_SESSION['user']['quiz'] != 'true') {
		refresh('/'.$languageURL.'make_money/become_an_author/');
	} 	

	require_once ROOT_PATH.'/apps/items/models/orders.class.php';
	$ordersClass = new orders();
	
	$weekStats = $ordersClass->getWeekStats();
	abr('weekStats', $weekStats);

#最新评论
	require_once ROOT_PATH.'/apps/items/models/comments.class.php';
	$commentsClass = new comments();
	
	$comments = $commentsClass->getAll(0, 100, " `owner_id` = '".intval($_SESSION['user']['user_id'])."' AND `reply_to` = '0' ", true, '`datetime` DESC');
	if(is_array($comments)) {
		$usersClass = new users();
		
		$users = $usersClass->getAll(0, 0, $commentsClass->usersWhere);
		abr('users', $users);		
	}
	abr('comments', $comments);
	

    require_once ROOT_PATH.'/apps/items/models/orders.class.php';
	$ordersClass = new orders();
	
	if(isset($_POST['ajax'])) {
		
		$month = get_id(2);
		$year = get_id(3);
		
		$text = '';
		$sales = $ordersClass->getAll(" `paid_datetime` > '".date('Y-m-d 23:59:59', mktime(0, 0, 0, ($month-1), date('t', mktime(0, 0, 0, ($month-1), 1, $year)), $year))."' AND `paid_datetime` < '".date('Y-m-d 00:00:00', mktime(0, 0, 0, ($month+1), 1, $year))."' AND `paid` = 'true' AND `type` = 'buy' AND `owner_id` = '".intval($_SESSION['user']['user_id'])."' ", "`paid_datetime` ASC");
		if(is_array($sales)) {
			$buff = array();
			foreach($sales as $s) {
				$day = explode(' ', $s['paid_datetime']);
				$day = explode('-', $day[0]);

				if(!isset($buff[$day[2]])) {
					$buff[$day[2]]['sale'] = 1;
					$buff[$day[2]]['earning'] = $s['receive'];
				}
				else {
					$buff[$day[2]]['sale']++;
					$buff[$day[2]]['earning'] += $s['receive'];
				}
			}
			
			foreach($buff as $day=>$r) {
				$text .= '<tr><td>'.$day.'</td><td>'.$r['sale'].' '.$langArray['sales'].'</td><td>$'.number_format($r['earning'], 2).'</td></tr>';
			}
		}
		
		die('
			jQuery("#month_'.$month.'_'.$year.'_details").html(\''.$text.'\');
			jQuery("#month_'.$month.'_'.$year.'_show").hide();
			jQuery("#month_'.$month.'_'.$year.'_hide").show(); 
		');
	}

	$usersClass = new users();
	
	$user = $usersClass->get($_SESSION['user']['user_id']);
	abr('user', $user);
	
#获取百分比
	require_once ROOT_PATH.'/apps/percents/models/percents.class.php';
	$percentsClass = new percents();
	
	$percent = $percentsClass->getPercentRow($user);
	if($percent['to'] == '0') {
		$percent['more'] = '-';
	}
	else {
		$percent['more'] = floatval($percent['to']) - floatval($user['sold']);
	}
	abr('percent', $percent);
	
	$earnings = array(
		'sales' => 0,
		'sales_earning' => 0,
		'referal' => 0,
		'total' => 0
	);	
	
	$maxSales = 0;
	
	$earningArr = false;
	
#获取销售金额
	$sales = $ordersClass->getAll(" `paid` = 'true' AND `type` = 'buy' AND `owner_id` = '".intval($user['user_id'])."' ", "`paid_datetime` ASC");
	if(is_array($sales)) {
		$buff = array();
		foreach($sales as $r) {
			
			$date = explode(' ', $r['paid_datetime']);
			$date = explode('-', $date[0]);
			
			if(isset($buff[$date[0]][$date[1]]['buy'])) {
				$buff[$date[0]][$date[1]]['buy']++;
			}
			else {
				$buff[$date[0]][$date[1]]['buy'] = 1;
			}
			
			if(isset($buff[$date[0]][$date[1]]['total'])) {
				$buff[$date[0]][$date[1]]['total'] += $r['receive'];
			}
			else {
				$buff[$date[0]][$date[1]]['total'] = $r['receive'];
			}
			
			if(isset($earningArr[$date[0]][$date[1]])) {
				$earningArr[$date[0]][$date[1]] += $r['receive'];
			}
			else {
				$earningArr[$date[0]][$date[1]] = $r['receive'];
			}
			
			if($buff[$date[0]][$date[1]]['buy'] > $maxSales) {
				$maxSales = $buff[$date[0]][$date[1]]['buy'];
			}
			
			$earnings['sales']++;
			$earnings['sales_earning'] += $r['receive'];
			$earnings['total'] += $r['receive'];
			
		}
		unset($sales);
		$sales = $buff;
		unset($buff);
	}	
	abr('sales', $sales);
	
	if($maxSales > 0) {
		$saleIndex = 300 / floatval($maxSales);		
	}
	else {
		$saleIndex = 0;
	}
	abr('saleIndex', $saleIndex);
	
#获取推广金额
	$referals = $ordersClass->getAll(" `paid` = 'true' AND `type` = 'referal' AND `owner_id` = '".intval($user['user_id'])."' ", "`paid_datetime` ASC");
	if(is_array($referals)) {
		$buff = array();
		foreach($referals as $r) {
			
			$date = explode(' ', $r['paid_datetime']);
			$date = explode('-', $date[0]);
			
			if($r['item_id'] == '0') {
				if(isset($buff[$date[0]][$date[1]]['deposit'])) {
					$buff[$date[0]][$date[1]]['deposit']++;
				}
				else {
					$buff[$date[0]][$date[1]]['deposit'] = 1;
				}
			}
			else {
				if(isset($buff[$date[0]][$date[1]]['buy'])) {
					$buff[$date[0]][$date[1]]['buy']++;
				}
				else {
					$buff[$date[0]][$date[1]]['buy'] = 1;
				}
			}
			
			if(isset($buff[$date[0]][$date[1]]['total'])) {
				$buff[$date[0]][$date[1]]['total'] += $r['receive'];
			}
			else {
				$buff[$date[0]][$date[1]]['total'] = $r['receive'];
			}
			
			if(isset($earningArr[$date[0]][$date[1]])) {
				$earningArr[$date[0]][$date[1]] += $r['receive'];
			}
			else {
				$earningArr[$date[0]][$date[1]] = $r['receive'];
			}			
			
			$earnings['referal'] += $r['receive'];
			$earnings['total'] += $r['receive'];
			
		}
		unset($referals);
		$referals = $buff;
		unset($buff);
	}	
	abr('referals', $referals);
	
	abr('earnings', $earnings);
	
	abr('earningArr', $earningArr);
	
	if(is_array($earningArr)) {
		$maxSales = 0;
		foreach($earningArr as $e) {
			foreach($e as $r) {
				if($r > $maxSales) {
					$maxSales = $r;
				}
			}
		}
		
		$earningIndex = ($maxSales>0) ? 300 / floatval($maxSales) : 0;
		abr('earningIndex', $earningIndex);
	}
    

    //推荐
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
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/dashboard/" title="">'.$langArray['my_account'].'</a>');		
	
?>