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

	$itemID = get_id(1);
	
	$itemsClass = new items();


    //获取预览图
    $files = scandir(DATA_SERVER_PATH.'/uploads/items/'.$itemID.'/preview/');
    $previewFiles = array();
    if(is_array($files)) {
        foreach($files as $f) {
            if(file_exists(DATA_SERVER_PATH.'/uploads/items/'.$itemID.'/preview/'.$f)) {
                $fileInfo = pathinfo(DATA_SERVER_PATH.'/uploads/items/'.$itemID.'/preview/'.$f);
                if( isset($fileInfo['extension']) && ( strtolower($fileInfo['extension']) == 'jpg' || strtolower($fileInfo['extension']) == 'png' ) ) {
                    $previewFiles[] =  'http://'.$config['domain'].'/static/uploads/items/'.$itemID.'/preview/'.$f;
                }
            }
        }
    }
        abr('previewFiles', $previewFiles);
//作品详情
	$item = $itemsClass->get($itemID);
	if(!is_array($item) || $item['status'] == 'deleted') {
		header("HTTP/1.0 404 Not Found");
        header("Location: http://". DOMAIN ."/error");
	} elseif(!is_array($item) || (check_login_bool() && $item['status'] == 'unapproved' && $item['user_id'] != $_SESSION['user']['user_id']) || $item['status'] == 'queue' || $item['status'] == 'extended_buy') {

	}
	_setTitle($item['name']);	
	abr('meta_description', substr(strip_tags($item['description']), 0, 255));	

	require_once ROOT_PATH.'/apps/users/models/users.class.php';
	$usersClass = new users();
	
	//获取作者详情
	$item['user'] = $usersClass->get($item['user_id']);
	
	//用户登录且用户购买的不是自己的作品
	if(check_login_bool() && $item['user_id'] != $_SESSION['user']['user_id']) {
		$ordersClass = new orders();
		//用户是否购买过该作品
		if($ordersClass->isBuyed($item['id'])) {
			$item['is_buyed'] = langMessageReplace($langArray['already_buyed'], array('URL' => '/'.$languageURL.'users/downloads/'));
		}
	}	
	
	$item['description'] = replaceEmoticons($item['description']);
	
	abr('item', $item);

    //判断有截图包模块
    require_once ROOT_PATH.'/apps/app_extends/models/app_extends.class.php';
    $is_extends='false';
    $app_extends=new app_extends();

    if($app_extends->is_uploads()){
        $is_extends='true';
    }

    abr("is_extends",$is_extends);


    #BUY ITEM	
    //购买作品
	if(isset($_SESSION['tmp']['order_id']) && $_SESSION['tmp']['order_id']) {
		$_SESSION['tmp']['order_id'] = 0;
	}
	if(isset($_POST['licence'])) {	
		if(!check_login_bool()) {
			$_SESSION['temp']['golink'] = '/'.$languageURL.'items/'.$itemID;
			refresh('/'.$languageURL.'sign_in/');
		}
		
		$ordersClass = new orders();
				
		if($_POST['licence'] == 'regular') {
			//余额购买作品
			if(isset($_POST['pay_method']) && $_POST['pay_method'] == 'paymethod') {
				$orderID = $ordersClass->add($item['price']);
				if(isset($_SESSION['tmp']['deposit_id'])) {
					unset($_SESSION['tmp']['deposit_id']);
				}
				$_SESSION['tmp']['order_id'] = $orderID;
				refresh('/'.$languageURL.'items/payment/');
			}
			else {
				//判断余额
				if($_SESSION['user']['total'] < $item['prepaid_price']) {
					addErrorMessage($langArray['error_not_enought_money'], '', 'error');
				}
				else {
					$total_money = floatval($item['prepaid_price'])+floatval($item['your_profit']);
					$ordersClass->buy($total_money);
					refresh('/'.$languageURL.'download/', $langArray['complete_buy_theme'], 'complete');
				}
			}
		}
		elseif($_POST['licence'] == 'extended') {
		
			if(isset($_POST['pay_method']) && $_POST['pay_method'] == 'paymethod') {
				$orderID = $ordersClass->add($item['extended_price'], 'true');
				if(isset($_SESSION['tmp']['deposit_id'])) {
					unset($_SESSION['tmp']['deposit_id']);
				}
				$_SESSION['tmp']['order_id'] = $orderID;
				refresh('/'.$languageURL.'items/payment/');
			}
			else {
				if($_SESSION['user']['total'] < $item['extended_price']) {
					addErrorMessage($langArray['error_not_enought_money'], '', 'error');
				}
				else {
				
					$ordersClass->buy($item['extended_price'], true);
					refresh('/'.$languageURL.'download/', $langArray['complete_buy_theme'], 'complete');
				}
			}
		}
	}
	
#标签标记作品
	require_once ROOT_PATH.'/apps/items/controllers/bookmark.php';
	
#是否免费文件
	if($item['free_file'] == 'true') {
		abr('freeFileMessage', langMessageReplace($langArray['free_file_info'], array('URL' => '/'.$languageURL.'users/downloads/'.$item['id'])));
	}	
	
#加载其它作品
	$otherItems = $itemsClass->getAll(0, 6, " `status` = 'active' AND `id` <> '".intval($itemID)."' AND `user_id` = '".intval($item['user_id'])."' ", "RAND()");	
	abr('otherItems', $otherItems);
	if(!is_array($otherItems)) {
		abr('otherItemsCount', 0);
	}
	else {
		abr('otherItemsCount', count($otherItems));
	}

#加载属性
	require_once ROOT_PATH.'/apps/attributes/models/attributes.class.php';
	$attributesClass = new attributes();	

	$attributes = $attributesClass->getAll(0, 0, $itemsClass->attributesWhere);
	abr('attributes', $attributes);

	$attributeCategories = $attributesClass->getAllCategories(0, 0, $itemsClass->attributeCategoriesWhere);
	abr('attributeCategories', $attributeCategories);
	
#加载分类
	require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
	$categoriesClass = new categories();
	
	$categories = $categoriesClass->getAll();
	abr('categories', $categories);	
	
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'items/'.$item['id'].'" title="">'.$item['name'].'</a>');		
	
	
	require_once ROOT_PATH.'/apps/system/models/badges.class.php';
	$badges = new badges();
	
	$badges_data = $badges->getAllFront();
	
	$user = $item['user'];
	
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
    abr('meta',$meta);
    #FAQ 
    require_once ROOT_PATH . '/apps/items/models/faq.class.php';
    $faqClass = new faq();
    $faqs = $faqClass->CountAll($itemID);
    abr('faqs', $faqs);
	
?>