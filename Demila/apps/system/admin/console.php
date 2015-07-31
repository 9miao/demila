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
	require_once ROOT_PATH.'/apps/items/models/orders.class.php';
	$ordersClass = new orders();

	$total = $ordersClass->getSalesStatus();
	abr('total', $total);

	$ref = $ordersClass->getSalesStatus(" AND `datetime` > '".date('Y-m')."-01 00:00:00' ", 'referal');
	$sales = $ordersClass->getSalesStatus(" AND `datetime` > '".date('Y-m')."-01 00:00:00' ");
	if(is_array($sales)) {
		$sales['referal'] = $ref['receive'];
		$sales['win'] = floatval($sales['total']) - floatval($sales['receive']) - floatval($sales['referal']);
		abr('sales', $sales);
	}
	unset($ref);
	
#LOAD USERS COUNT
	require_once ROOT_PATH.'/apps/users/models/users.class.php';
	$usersClass = new users();
//	$users['month'] = $usersClass->getUsersCount(" `register_datetime` > '".date('Y-m')."-01 00:00:00' AND `status` = 'activate' ");
//	$users['total'] = $usersClass->getUsersCount(" `status` = 'activate' ");
	abr('users', $users);

	$topAuthors = $usersClass->getAll(0, 5, " `status` = 'activate' ", "`sales` DESC");
	abr('topAuthors', $topAuthors);
	
#LOAD WITHDRAW
	require_once ROOT_PATH.'/apps/users/models/deposit.class.php';
	$depositClass = new deposit();

	$withdraw['no'] = $depositClass->getWithdrawCount(" `paid` = 'false' AND `datetime` > '".date('Y-m')."-01 00:00:00' ");
	$withdraw['paid'] = $depositClass->getWithdrawCount(" `paid` = 'true' AND `paid_datetime` > '".date('Y-m')."-01 00:00:00' ");
	abr('withdraw', $withdraw);
	
#LOAD THEMES
	require_once ROOT_PATH.'/apps/items/models/items.class.php';
	$itemsClass = new items();
	
	$items = $itemsClass->getAll(0, 10, " `status` = 'queue' ");
	abr('items', $items);	
	
	$updated_items = $itemsClass->getAllForUpdate(0, 10);
	abr('updated_items', $updated_items);
	
#LOAD LAST REQUEST
	require_once ROOT_PATH.'/apps/contacts/models/contacts.class.php';
	$contactsClass = new contacts();

	$lastContact = $contactsClass->getAll(0, 10, " `answer` = '' ");

	abr('lastContact', $lastContact);
	
	
#CHECK FOR ATTRIBUTES
	require_once ROOT_PATH.'/apps/attributes/models/categories.class.php';
	$categoriesClass = new categories();

	$attributes = $categoriesClass->getAll();
	if(!is_array($attributes)) {
		abr('notHaveAttributes', 'true');
	}
	
	
	
	require_once ROOT_PATH.'/apps/reports/models/javascript.class.php';
	
	$referal_sum = $ordersClass->getSalesStatusByDay(" AND `datetime` > '".date('Y-m')."-01 00:00:00' ", 'referal');
	$sales_sum = $ordersClass->getSalesStatusByDay(" AND `datetime` > '".date('Y-m')."-01 00:00:00' ");
	
	$referal_money = array();
	$sales_money = array();
	$user_money = array();
	$win_money = array();
	$sales_num = array();
	$days = array();
	for($i=1; $i<= date('t'); $i++) {
		if(isset($referal_sum[date("Y-m-") . sprintf('%02d', $i)])) {
			$referal_money[] = number_format($referal_sum[date("Y-m-") . sprintf('%02d', $i)]['receive'], 2, '.', '');
		} else {
			$referal_money[] = 0;
		}
		if(isset($sales_sum[date("Y-m-") . sprintf('%02d', $i)])) {
			$sales_money[] = number_format($sales_sum[date("Y-m-") . sprintf('%02d', $i)]['total'], 2, '.', '');
			$user_money[] = number_format($sales_sum[date("Y-m-") . sprintf('%02d', $i)]['receive'], 2, '.', '');
			if(isset($referal_sum[date("Y-m-") . sprintf('%02d', $i)]['receive'])) {
				$sales_sum[date("Y-m-") . sprintf('%02d', $i)]['referal'] = $referal_sum[date("Y-m-") . sprintf('%02d', $i)]['receive'];
			}
			if(!isset($sales_sum[date("Y-m-") . sprintf('%02d', $i)]['referal'])) {
				$sales_sum[date("Y-m-") . sprintf('%02d', $i)]['referal'] = 0;
			}
			$sales_num[] = $sales_sum[date("Y-m-") . sprintf('%02d', $i)]['num'];
			$win_money[] = number_format( floatval($sales_sum[date("Y-m-") . sprintf('%02d', $i)]['total']) - floatval($sales_sum[date("Y-m-") . sprintf('%02d', $i)]['receive']) - floatval($sales_sum[date("Y-m-") . sprintf('%02d', $i)]['referal']), 2, '.', '');
		} else {
			$sales_money[] = 0;
			$user_money[] = 0;
			$win_money[] = 0;
			$sales_num[] = 0;
		}
		$days[] = $i;
	}
	
	$new_array = array();
	//$new_array[] = array('name' => $langArray['referal_money_this_month_short'], 'data' => $referal_money);
	$new_array[] = array('name' => $langArray['win'], 'data' => $sales_money);
	$new_array[] = array('name' => $langArray['user_win_this_month'], 'data' => $user_money);
	$new_array[] = array('name' => $langArray['grid_win'], 'data' => $win_money);
	$new_array2 = array();
	$new_array2[] = array('name' => $langArray['sales'], 'data' => $sales_num);
	
	abr('finance_array', javascript::encode($new_array));
	abr('sales_array', javascript::encode($new_array2));
	abr('days', json_encode($days));
	abr('valuta', html_entity_decode($currency['symbol'], ENT_QUOTES, 'utf-8'));
    require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';
	
?>