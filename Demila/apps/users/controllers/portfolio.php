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

	$username = get_id(2);

_setTitle($username.$langArray['portfolio_setTitle']); 

	abr('checkItemsType', 'yes');

	$usersClass = new users();
	if(check_login_bool() && ($username == '' || $username == $_SESSION['user']['username'])) {
		$username = $_SESSION['user']['username'];
		$whereQuery = " AND (`status` = 'active' OR `status` = 'unapproved' ) ";
	}
	else {
		$whereQuery = " AND `status` = 'active' ";
	}
	$user = $usersClass->getByUsername($username);
	if(!is_array($user)) {
		header("HTTP/1.0 404 Not Found");
        header("Location: http://". DOMAIN ."/".$languageURL."error");	
	}
	abr('user', $user);
	
#加载作品	
	require_once ROOT_PATH.'/apps/items/models/items.class.php';
	$itemsClass = new items();
	
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
	
	$items = $itemsClass->getAll($start, $limit, " `user_id` = '".intval($user['user_id'])."' ".$whereQuery, $order);
	abr('items', $items);
	
	abr('paging', paging('/'.$languageURL.'user/portfolio/'.$username.'/?p=', '&sort_by='.$_GET['sort_by'].'&order='.$_GET['order'], PAGE, $limit, $itemsClass->foundRows));
	
#加载分类
	require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
	$categoriesClass = new categories();

	$categories = $categoriesClass->getAll();
	abr('categories', $categories);	
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/'.$user['username'].'" title="">'.$user['username'].'</a> \ <a href="/'.$languageURL.'users/'.$user['username'].'/portfolio" title="">'.$langArray['portfolio'].'</a>');		
	
	$discount = array();
	if($meta['prepaid_price_discount']) {
		if(strpos($meta['prepaid_price_discount'], '%')) {
			$discount = $meta['prepaid_price_discount'];
		} else {
			$discount = $currency['symbol'] . $meta['prepaid_price_discount'];
		}
	}
	abr('right_discount', $discount);	
	
	require_once ROOT_PATH.'/apps/system/models/badges.class.php';
	$badges = new badges();
	
	$badges_data = $badges->getAllFront();
	
	$other_badges = array_map('trim', explode(',', $user['badges']));
	
	$user_badges = array();
	
	if($user['exclusive_author'] == 'true' && isset($badges_data['system']['is_exclusive_author'])) {
		if($badges_data['system']['is_exclusive_author']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $badges_data['system']['is_exclusive_author']['photo'])) {
			$user_badges[] = array(
				'name' => $badges_data['system']['is_exclusive_author']['name'],
				'photo' => '/uploads/badges/' . $badges_data['system']['is_exclusive_author']['photo']
			);
		}
	}
	
	if($user['featured_author'] == 'true' && isset($badges_data['system']['has_been_featured'])) {
		if($badges_data['system']['has_been_featured']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $badges_data['system']['has_been_featured']['photo'])) {
			$user_badges[] = array(
				'name' => $badges_data['system']['has_been_featured']['name'],
				'photo' => '/uploads/badges/' . $badges_data['system']['has_been_featured']['photo']
			);
		}
	}
	
	if(isset($user['statuses']['freefile']) && $user['statuses']['freefile'] && isset($badges_data['system']['has_free_file_month'])) {
		if($badges_data['system']['has_free_file_month']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $badges_data['system']['has_free_file_month']['photo'])) {
			$user_badges[] = array(
				'name' => $badges_data['system']['has_free_file_month']['name'],
				'photo' => '/uploads/badges/' . $badges_data['system']['has_free_file_month']['photo']
			);
		}
	}
	
	if(isset($user['statuses']['featured']) && $user['statuses']['featured'] && isset($badges_data['system']['has_had_item_featured'])) {
		if($badges_data['system']['has_free_file_month']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $badges_data['system']['has_had_item_featured']['photo'])) {
			$user_badges[] = array(
				'name' => $badges_data['system']['has_had_item_featured']['name'],
				'photo' => '/uploads/badges/' . $badges_data['system']['has_had_item_featured']['photo']
			);
		}
	}
	
	if($user['buy'] && isset($badges_data['buyers']) && is_array($badges_data['buyers'])) {
		foreach($badges_data['buyers'] AS $k => $v) {
			list($from, $to) = explode('-', $k);
			if($from <= $user['buy'] && $to >= $user['buy']) {
				if($v['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $v['photo'])) {
					$user_badges[] = array(
						'name' => $v['name'],
						'photo' => '/uploads/badges/' . $v['photo']
					);
				}
				break;
			}
		}
	}
	
	if($user['sold'] && isset($badges_data['authors']) && is_array($badges_data['authors'])) {
		foreach($badges_data['authors'] AS $k => $v) {
			list($from, $to) = explode('-', $k);
			if($from <= $user['sold'] && $to >= $user['sold']) {
				if($v['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $v['photo'])) {
					$user_badges[] = array(
						'name' => $v['name'],
						'photo' => '/uploads/badges/' . $v['photo']
					);
				}
				break;
			}
		}
	}
	
	if($user['referals'] && isset($badges_data['referrals']) && is_array($badges_data['referrals'])) {
		foreach($badges_data['referrals'] AS $k => $v) {
			list($from, $to) = explode('-', $k);
			if($from <= $user['referals'] && $to >= $user['referals']) {
				if($v['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $v['photo'])) {
					$user_badges[] = array(
						'name' => $v['name'],
						'photo' => '/uploads/badges/' . $v['photo']
					);
				}
				break;
			}
		}
	}
	
	if(isset($badges_data['other']) && is_array($badges_data['other'])) {
		foreach($badges_data['other'] AS $k => $b) {
			if(in_array($k, $other_badges) && $b['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $b['photo'])) {
				$user_badges[] = array(
					'name' => $b['name'],
					'photo' => '/uploads/badges/' . $b['photo']
				);
			}
		}
	}
	
	if(isset($user['country']['photo']) && $user['country']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/countries/" . $user['country']['photo'])) {
		$user_badges[] = array(
			'name' => $user['country']['name'],
			'photo' => '/uploads/countries/' . $user['country']['photo']
		);
	} elseif(isset($badges_data['system']['location_global_community']) && $badges_data['system']['location_global_community']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $badges_data['system']['location_global_community']['photo'])) {
		$user_badges[] = array(
			'name' => $badges_data['system']['location_global_community']['name'],
			'photo' => '/uploads/badges/' . $badges_data['system']['location_global_community']['photo']
		);
	}

		if($user['power_elite_author'] == 'true' && isset($badges_data['system']['power_elite_author'])) {
		if($badges_data['system']['power_elite_author']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $badges_data['system']['has_been_featured']['photo'])) {
			$user_badges[] = array(
				'name' => $badges_data['system']['power_elite_author']['name'],
				'photo' => '/uploads/badges/' . $badges_data['system']['power_elite_author']['photo']
			);
		}
	}
	
	if($user['elite_author'] == 'true' && isset($badges_data['system']['elite_author'])) {
		if($badges_data['system']['elite_author']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $badges_data['system']['has_been_featured']['photo'])) {
			$user_badges[] = array(
				'name' => $badges_data['system']['elite_author']['name'],
				'photo' => '/uploads/badges/' . $badges_data['system']['elite_author']['photo']
			);
		}
	}
	
	abr('user_badges', $user_badges);
?>