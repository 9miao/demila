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

	$username = get_id(1);
_setTitle($username);

$usersClass = new users();

$users = $usersClass->getByUsername($username);
if(!is_array($users)) {
		header("HTTP/1.0 404 Not Found");
        header("Location: http://". DOMAIN ."/".$languageURL."error");
	}

if(check_login_bool() && $_SESSION['user']['user_id'] != $users['user_id']) {
		$users['is_follow'] = $usersClass->isFollow($users['user_id']);
	}

	$users['profile_desc'] = replaceEmoticons($users['profile_desc']);
	
	abr('user', $users);
	
	
#加载分类
		require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
		$categoriesClass = new categories();
	
		$categories = $categoriesClass->getAll();
		abr('categories', $categories);	
	
#发送邮件
	if(check_login_bool() && isset($_POST['send_email'])) {
		$s = $usersClass->sendEmail();
		if($s === true) {
			refresh('/'.$languageURL.'user/'.$users['username'], $langArray['complete_send_email'], 'complete');
		}
		else {
			addErrorMessage($s, '', 'error');
		}
	}
		
#关注用户
	if(check_login_bool() && isset($_GET['follow']) && $_SESSION['user']['user_id'] != $users['user_id']) {
		$usersClass->followUser($users['user_id']);
		if(isset($_POST)) {			
			if($users['is_follow']) {
				$text = $langArray['follow'];
			}
			else {
				$text = $langArray['unfollow'];
			}
			die('
				jQuery("#follow").html("'.$text.'");
			');
		}
		refresh('/'.$languageURL.'user/'.$users['username']);
	}	
	
#加载公开书签集
	require_once ROOT_PATH.'/apps/collections/models/collections.class.php';
	$collectionsClass = new collections();

	$collections = $collectionsClass->getAll(0, 2, " `public` = 'true' AND `user_id` = '".intval($users['user_id'])."' ");
	abr('collections', $collections);
	
#获取推荐文件
	if($users['featured_item_id'] != '0') {
		require_once ROOT_PATH.'/apps/items/models/items.class.php';
		$itemsClass = new items();
		
		$featureItem = $itemsClass->get($users['featured_item_id'], true);
		abr('featureItem', $featureItem);
	}

#获取粉丝
	$follow['to'] = $usersClass->getFollowers($users['user_id'], 0, 9, 'RAND()', true);
	$follow['to_count'] = $usersClass->foundRows;
	$follow['from'] = $usersClass->getFollowers($users['user_id'], 0, 9, 'RAND()');
	$follow['from_count'] = $usersClass->foundRows;	
	abr('follow', $follow);
	
	
	$follow['toto'] = $usersClass->getFollowers($users['user_id'], 0, 10000000, 'RAND()', true);
	$follow['toto_count'] = $usersClass->foundRows;
	$follow['fromfrom'] = $usersClass->getFollowers($users['user_id'], 0, 10000000, 'RAND()');
	$follow['fromfrom_count'] = $usersClass->foundRows;	
	abr('follow', $follow);
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/'.$users['username'].'" title="">'.$users['username'].'</a>');
	
	require_once ROOT_PATH.'/apps/system/models/badges.class.php';
	$badges = new badges();
	
	$badges_data = $badges->getAllFront();
	
	$other_badges = array_map('trim', explode(',', $users['badges']));
	
	$user_badges = array();
	
	if($users['exclusive_author'] == 'true' && isset($badges_data['system']['is_exclusive_author'])) {
		if($badges_data['system']['is_exclusive_author']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $badges_data['system']['is_exclusive_author']['photo'])) {
			$user_badges[] = array(
				'name' => $badges_data['system']['is_exclusive_author']['name'],
				'photo' => '/uploads/badges/' . $badges_data['system']['is_exclusive_author']['photo']
			);
		}
	}
	
	if($users['featured_author'] == 'true' && isset($badges_data['system']['has_been_featured'])) {
		if($badges_data['system']['has_been_featured']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $badges_data['system']['has_been_featured']['photo'])) {
			$user_badges[] = array(
				'name' => $badges_data['system']['has_been_featured']['name'],
				'photo' => '/uploads/badges/' . $badges_data['system']['has_been_featured']['photo']
			);
		}
	}
	
	if(isset($users['statuses']['freefile']) && $users['statuses']['freefile'] && isset($badges_data['system']['has_free_file_month'])) {
		if($badges_data['system']['has_free_file_month']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $badges_data['system']['has_free_file_month']['photo'])) {
			$user_badges[] = array(
				'name' => $badges_data['system']['has_free_file_month']['name'],
				'photo' => '/uploads/badges/' . $badges_data['system']['has_free_file_month']['photo']
			);
		}
	}
	
	if(isset($users['statuses']['featured']) && $users['statuses']['featured'] && isset($badges_data['system']['has_had_item_featured'])) {
		if($badges_data['system']['has_free_file_month']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $badges_data['system']['has_had_item_featured']['photo'])) {
			$user_badges[] = array(
				'name' => $badges_data['system']['has_had_item_featured']['name'],
				'photo' => '/uploads/badges/' . $badges_data['system']['has_had_item_featured']['photo']
			);
		}
	}
	
	if($users['buy'] && isset($badges_data['buyers']) && is_array($badges_data['buyers'])) {
		foreach($badges_data['buyers'] AS $k => $v) {
			list($from, $to) = explode('-', $k);
			if($from <= $users['buy'] && $to >= $users['buy']) {
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
	
	if($users['sold'] && isset($badges_data['authors']) && is_array($badges_data['authors'])) {
		foreach($badges_data['authors'] AS $k => $v) {
			list($from, $to) = explode('-', $k);
			if($from <= $users['sold'] && $to >= $users['sold']) {
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
	
	if($users['referals'] && isset($badges_data['referrals']) && is_array($badges_data['referrals'])) {
		foreach($badges_data['referrals'] AS $k => $v) {
			list($from, $to) = explode('-', $k);
			if($from <= $users['referals'] && $to >= $users['referals']) {
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
	
	if(isset($users['country']['photo']) && $users['country']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/countries/" . $users['country']['photo'])) {
		$user_badges[] = array(
			'name' => $users['country']['name'],
			'photo' => '/uploads/countries/' . $users['country']['photo']
		);
	} elseif(isset($badges_data['system']['location_global_community']) && $badges_data['system']['location_global_community']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $badges_data['system']['location_global_community']['photo'])) {
		$user_badges[] = array(
			'name' => $badges_data['system']['location_global_community']['name'],
			'photo' => '/uploads/badges/' . $badges_data['system']['location_global_community']['photo']
		);
	}

	if($users['power_elite_author'] == 'true' && isset($badges_data['system']['power_elite_author'])) {
		if($badges_data['system']['power_elite_author']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $badges_data['system']['has_been_featured']['photo'])) {
			$user_badges[] = array(
				'name' => $badges_data['system']['power_elite_author']['name'],
				'photo' => '/uploads/badges/' . $badges_data['system']['power_elite_author']['photo']
			);
		}
	}
	
	if($users['elite_author'] == 'true' && isset($badges_data['system']['elite_author'])) {
		if($badges_data['system']['elite_author']['photo'] && file_exists(DATA_SERVER_PATH . "/uploads/badges/" . $badges_data['system']['has_been_featured']['photo'])) {
			$user_badges[] = array(
				'name' => $badges_data['system']['elite_author']['name'],
				'photo' => '/uploads/badges/' . $badges_data['system']['elite_author']['photo']
			);
		}
	}
	
	abr('user_badges', $user_badges);
	
?>