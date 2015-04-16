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
if (isset ( $_GET ['url'] )) {
	$_GET ['array_url'] = explode ( "/", $_GET ['url'] );
	
	//设置语言，URL /bg/module/page
	if (! isset ( $_GET ['array_url'] [0] ) || strlen ( $_GET ['array_url'] [0] ) != 2) {
		$_moduleOffset = 0;
		$_controllerOffset = 1;
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