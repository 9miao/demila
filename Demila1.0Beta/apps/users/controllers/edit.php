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
_setTitle($langArray['edit_profile']);	
	
if(!check_login_bool()) {
		$_SESSION['temp']['golink'] = '/'.$languageURL.'edit/';
		refresh('/'.$languageURL.'sign_in/');
}
    //勋章
    require_once ROOT_PATH.'/apps/system/models/badges.class.php';
	$badges = new badges();
	
	$badges_data = $badges->getAllFront();
	
	$user = $_SESSION['user'];
	
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
#加载用户作品
	require_once ROOT_PATH.'/apps/items/models/items.class.php';
	$itemsClass = new items();

	$items = $itemsClass->getAll(0, 0, " `status` = 'active' AND `user_id` = '".intval($_SESSION['user']['user_id'])."' ");
	abr('items', $items);

#更改密码
	if(isset($_POST['change_password'])) {		
		$usersClass = new users();
		$s = $usersClass->editNewPassword();
		if($s === true) {
			refresh('/'.$languageURL.'edit/', $langArray['complete_change_password'], 'complete');
		}
		else {
			$message = '<ul>';
			foreach($s as $e) {
				$message .= '<li>'.$e.'</li>';
			}
			$message .= '</ul>';
			addErrorMessage($message, '', 'error');
		}		
	}

#推荐作品
	if(isset($_POST['feature_save'])) {		
		$usersClass = new users();
		$usersClass->editFeatureItem();
		refresh('/'.$languageURL.'edit/', $langArray['complete_save_feature'], 'complete');
	}
	
#独家作者
	if(isset($_POST['exclusive_false'])) {		
		$usersClass = new users();
		$usersClass->editExclusiveAuthor('false');
		refresh('/'.$languageURL.'edit/', $langArray['complete_exclusive_author_off'], 'complete');		
	}
	elseif(isset($_POST['exclusive_true'])) {		
		$usersClass = new users();
		$usersClass->editExclusiveAuthor('true');
		refresh('/'.$languageURL.'edit/', $langArray['complete_exclusive_author_on'], 'complete');		
	}
	
#更改许可类型
	if(isset($_POST['save_license'])) {
		$usersClass = new users();
		$s = $usersClass->editSaveLicense();
		if($s === true) {
			refresh('/'.$languageURL.'edit/', $langArray['complete_save_license'], 'complete');	
		}
		else {
			$message = '<ul>';
			foreach($s as $e) {
				$message .= '<li>'.$e.'</li>';
			}
			$message .= '</ul>';
			addErrorMessage($message, '', 'error');
		}
	}	
	
#更改头像和主页图片
	if(isset($_POST['change_avatar_image'])) {
		$usersClass = new users();
		$usersClass->editChangeAvatarImage();
		$message = '';
		if($usersClass->avatarError) {
			$message .= '<li>'.$usersClass->avatarError.'</li>';
		}
		if($usersClass->homeimageError) {
			$message .= '<li>'.$usersClass->homeimageError.'</li>';
		}
		if($message != '') {
			$message = '<ul>'.$message.'</li>';
			addErrorMessage($message, '', 'error');
		}
		else {
			refresh('/'.$languageURL.'edit/', $langArray['complete_change_avatar_image'], 'complete');
		}		
	}
	
#保存个人信息
	if(isset($_POST['personal_edit'])) {
		$usersClass = new users();
		$s = $usersClass->editPersonalInformation();
		if($s === true) {
			refresh('/'.$languageURL.'edit/', $langArray['complete_update_personal_info'], 'complete');	
		}
		else {
			$message = '<ul>';
			foreach($s as $e) {
				$message .= '<li>'.$e.'</li>';
			}
			$message .= '</ul>';
			addErrorMessage($message, '', 'error');
		}
	}	
	else {
		$_POST['nickname'] = $_SESSION['user']['nickname'];
		$_POST['email'] = $_SESSION['user']['email'];
		$_POST['firmname'] = $_SESSION['user']['firmname'];
		$_POST['profile_title'] = $_SESSION['user']['profile_title'];
		$_POST['profile_desc'] = $_SESSION['user']['profile_desc'];
		$_POST['live_city'] = $_SESSION['user']['live_city'];
		$_POST['country_id'] = $_SESSION['user']['country_id'];
		$_POST['freelance'] = $_SESSION['user']['freelance'];
	}
	

#保存社交应用连接
	if(isset($_POST['social_edit'])) {
		$usersClass = new users();
		$s = $usersClass->editSocialInformation();
		if($s === true) {
			refresh('/'.$languageURL.'edit/', $langArray['complete_update_personal_info'], 'complete');	
		}
	}	
	else {
		$_POST['weibo'] = $_SESSION['user']['weibo'];
		$_POST['tencent'] = $_SESSION['user']['tencent'];
		$_POST['baidu'] = $_SESSION['user']['baidu'];
		$_POST['netease'] = $_SESSION['user']['netease'];
		$_POST['sohu'] = $_SESSION['user']['sohu'];
		$_POST['renren'] = $_SESSION['user']['renren'];
	}
	

#家在国家或地区
	require_once ROOT_PATH.'/apps/countries/models/countries.class.php';
	$countriesClass = new countries();

	$countries = $countriesClass->getAll(0, 0, " `visible` = 'true' ");
	abr('countries', $countries);
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/dashboard/" title="">'.$langArray['my_account'].'</a> \ <a href="/'.$languageURL.'edit/" title="">'.$langArray['settings'].'</a>');		
	
	
?>