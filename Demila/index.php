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


#检查安装
if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/config/config.php')) {
	header('Location: /setup/index.php');
	die();
}

require_once 'config.php';
require_once $config ['root_path'] . '/core/functions.php';
include_once $config ['system_core'] . "/initEngine.php";

require_once ROOT_PATH.'/apps/system/models/system.class.php';
$systemClass = new system();
	
$currency = $systemClass->getActiveCurrency();
abr('currency', $currency);

#元数据
$meta = $systemClass->getAllKeyValue();


$smarty->assign('title', $meta['meta_title']);
$smarty->assign('meta_keywords', $meta['meta_keywords']);
$smarty->assign('meta_description', $meta['meta_description']);
$smarty->assign('site_logo', $meta['site_logo']);

	

if($_GET['module'] != 'admin') {
	
#订阅邮件新闻	
	if(isset($_POST['subscribe'])) {
		require_once ROOT_PATH.'/apps/bulletin/models/bulletin.class.php';
		$bulletinClass = new bulletin();				
		
		$s = $bulletinClass->addBulletinEmail();
		if($s === true) {
			refresh('', $langArray['complete_add_to_newsletter'], 'complete');
		}
		elseif($s == 'already') {
			refresh('', $langArray['already_in_newsletter'], 'info');
		}
		else {
			refresh('', $langArray['error_newsletter'], 'error');
		}
	}
	
#保存推荐人至session
	if(isset($_GET['ref'])) {
		$_SESSION['temp']['referal'] = $_GET['ref'];
	}
	
#加载页面至菜单
	require_once ROOT_PATH.'/apps/pages/models/pages.class.php';
	$pagesClass = new pages();
	
	$menuPages = $pagesClass->getAll(0, 0, " `visible` = 'true' AND `menu` = 'true' ", true);
	abr('menuPages', $menuPages);
	
	$footerPages = $pagesClass->getAll(0, 0, " `visible` = 'true' AND `footer` = 'true' ", true);
	abr('footerPages', $footerPages);
	
#加载主分类
	require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
	$categoriesClass = new categories();

	$mainCategories = $categoriesClass->getAll(0, 0, " `visible` = 'true' AND `sub_of` = '0' ");
	$allCats = $categoriesClass->getAllWithChilds(0, '`visible` = \'true\'');
	abr('mainCategories', $allCats[0]);
	unset($allCats[0]);
	abr('allCats', $allCats);
	//abr('mainCategories', $mainCategories);
	
#加载计数器
	require_once ROOT_PATH.'/apps/items/models/items.class.php';
	$itemsClass = new items();

	abr('itemsCount', $itemsClass->getItemsCount());	

	require_once ROOT_PATH.'/apps/users/models/users.class.php';
	$usersClass = new users();
	
	abr('usersCount', $usersClass->getUsersCount(" `status` = 'activate' "));

	
#更新用户数据

	if(check_login_bool()) {
		$_SESSION['user'] = $usersClass->get($_SESSION['user']['user_id']);
	}
	
}


	include_once $config ['system_core'] . "/endEngine.php";
	
?>