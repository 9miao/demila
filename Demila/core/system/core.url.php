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

/*
 * 设置module和controller
 */


//path_info
$_GET ['url']=get_new_str($_SERVER["PATH_INFO"]);
$_GET ['url']=str_replace("index.php", "",  $_GET ['url']);

//url
$_GET['url']=$_GET['url']=="/author_dashboard/"?"/users/dashboard":$_GET['url'];
$_GET['url']=$_GET['url']=="/admin_dashboard/"?"/admin":$_GET['url'];
$_GET['url']=$_GET['url']=="/author_dashboard"?"/users/dashboard":$_GET['url'];
$_GET['url']=$_GET['url']=="/edit/"?"/users/edit":$_GET['url'];
$_GET['url']=$_GET['url']=="/edit"?"/users/edit":$_GET['url'];
$_GET['url']=$_GET['url']=="/earnings/"?"/users/earnings":$_GET['url'];
$_GET['url']=$_GET['url']=="/earnings"?"/users/earnings":$_GET['url'];
$_GET['url']=$_GET['url']=="/statement/"?"/users/statement":$_GET['url'];
$_GET['url']=$_GET['url']=="/statement"?"/users/statement":$_GET['url'];
$_GET['url']=$_GET['url']=="/sign_in/"?"/users/login":$_GET['url'];
$_GET['url']=$_GET['url']=="/sign_in/"?"/users/login":$_GET['url'];
$_GET['url']=$_GET['url']=="/sign_up/"?"/users/registration":$_GET['url'];
$_GET['url']=$_GET['url']=="/sign_up"?"/users/registration":$_GET['url'];
$_GET['url']=$_GET['url']=="/sign_up/verify/"?"/users/registration/verify":$_GET['url'];
$_GET['url']=$_GET['url']=="/sign_up/complete/"?"/users/registration/complete":$_GET['url'];
if(strstr($_GET['url'],"/user/")){
    $_GET['url']=str_replace("/user/","/users/",$_GET['url']);
}
if(strstr($_GET['url'],"/download/")){
    $_GET['url']=str_replace("/download/","/users/downloads/",$_GET['url']);
}
$_GET['url']=$_GET['url']=="/user/bookmarks/"?"/users/bookmarks":$_GET['url'];
$_GET['url']=$_GET['url']=="/user/bookmarks"?"/users/bookmarks":$_GET['url'];
$_GET['url']=$_GET['url']=="/deposit/"?"/users/deposit":$_GET['url'];
$_GET['url']=$_GET['url']=="/deposit"?"/users/deposit":$_GET['url'];
$_GET['url']=$_GET['url']=="/invoices/"?"/users/history":$_GET['url'];
$_GET['url']=$_GET['url']=="/invoices"?"/users/history":$_GET['url'];
$_GET['url']=$_GET['url']=="/withdrawal/"?"/users/withdrawal":$_GET['url'];
$_GET['url']=$_GET['url']=="/withdrawal"?"/users/withdrawal":$_GET['url'];
$_GET['url']=$_GET['url']=="/lost_username/"?"/users/lost_username":$_GET['url'];
$_GET['url']=$_GET['url']=="/lost_username"?"/users/lost_username":$_GET['url'];
$_GET['url']=$_GET['url']=="/reset_password/"?"/users/reset_password":$_GET['url'];
$_GET['url']=$_GET['url']=="/reset_password"?"/users/reset_password":$_GET['url'];
$_GET['url']=$_GET['url']=="/top_authors/"?"/users/top":$_GET['url'];
$_GET['url']=$_GET['url']=="/top_authors"?"/users/top":$_GET['url'];
$_GET['url']=$_GET['url']=="/free_file/"?"/users/free_file":$_GET['url'];
$_GET['url']=$_GET['url']=="/free_file"?"/users/free_file":$_GET['url'];
$_GET['url']=$_GET['url']=="/feature/"?"/items/feature":$_GET['url'];
$_GET['url']=$_GET['url']=="/feature"?"/items/feature":$_GET['url'];
$_GET['url']=$_GET['url']=="/top_sellers/"?"/items/top_sellers":$_GET['url'];
$_GET['url']=$_GET['url']=="/top_sellers"?"/items/top_sellers":$_GET['url'];
$_GET['url']=$_GET['url']=="/verify/"?"/users/verify":$_GET['url'];
$_GET['url']=$_GET['url']=="/verify"?"/users/verify":$_GET['url'];
$_GET['url']=$_GET['url']=="/support/"?"/contacts":$_GET['url'];
$_GET['url']=$_GET['url']=="/support"?"/contacts":$_GET['url'];

    if($_GET['url']!='')
    $_GET ['array_url'] = explode ( "/", $_GET ['url'] );

	//设置语言，URL /bg/module/page
	if (! isset ( $_GET ['array_url'] [0] ) || strlen ( $_GET ['array_url'] [0] ) != 2) {
		$_moduleOffset = 1;
		$_controllerOffset = 2;
	} else {
		$_GET ['language'] = $_GET ['array_url'] [0];
		$_moduleOffset = 1;
		$_controllerOffset = 2;
	}

#####################################################################
	//检查若未设置module，设置默认module为/apps/index/
	if (isset ( $_GET ['array_url'] [$_moduleOffset] )) {
		if ($_GET ['array_url'] [$_moduleOffset] != "") {
			$_GET ['module'] = $_GET ['array_url'] [$_moduleOffset];
		} else {
			$_GET ['module'] = "index";
		}
	}

//检查若未设置controller，设置默认controller为index
	if (isset ( $_GET ['array_url'] [$_controllerOffset] )) {
		if ($_GET ['array_url'] [$_controllerOffset] != "") {
			$_GET ['controller'] = $_GET ['array_url'] [$_controllerOffset];
		} else {
			$_GET ['controller'] = 'index';
		}
	}

//与检查module和controller
if (! isset ( $_GET ['module'] )) {
	$_GET ['module'] = 'index';
}
if (! isset ( $_GET ['controller'] )) {
	$_GET ['controller'] = 'index';
}

//清除黑客的module和controller输入
if (isset ( $_GET ['module'] )) {
	if (! (preg_match ( "/[a-z_0-9.\/-]*/i", $_GET ['module'] ) && ! preg_match ( "/\\.\\./", $_GET ['module'] ))) {
		die ( "Invalid request for MODULE" );
	}
}
if (isset ( $_GET ['controller'] )) {
	if (! (preg_match ( "/[a-z_ а-я0-9.\/-]*/iu", $_GET ['controller'] ) && ! preg_match ( "/\\.\\./", $_GET ['controller'] ))) {
		die ( "Invalid request for CONTROLLER" );
	}
}

?>