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

require_once 'init.php';

//加载管理员模板
$_templateFile = ENGINE_PATH . '/administration/views/login.html';
abr ( 'content_template', $_templateFile );

_setLayout ( "admin_login" );

require_once ROOT_PATH . '/apps/users/models/users.class.php';
$cms = new users ( );

/*
 * 检查登录cookie
 */
if (isset ( $_COOKIE ['user_id'] ) && isset ( $_COOKIE ['verifyKey'] )) {
	if ($cms->isValidVerifyKey ( $_COOKIE ['user_id'], $_COOKIE ['verifyKey'] )) {
		$_SESSION ['user'] = $cms->get ( $_COOKIE ['user_id'] );
		
		setcookie ( "user_id", $_COOKIE ['user_id'], time () + 2592000, "/", "." . $config ['domain'] );
		setcookie ( "verifyKey", $_COOKIE ['verifyKey'], time () + 2592000, "/", "." . $config ['domain'] );
		
		if (isset ( $_SESSION ['redirectUrl'] )) {
			$refreshURL = $_SESSION ['redirectUrl'];
			unset ( $_SESSION ['redirectUrl'] );
		} else {
			$refreshURL = '/' . $languageURL . adminURL . '/';
		}
		
		refresh ( $refreshURL );
	}
}

if (isset ( $_POST ['login'] )) {
	$status = $cms->login ( true );
	if ($status === true) {
		if (isset ( $_SESSION ['redirectUrl'] )) {
			$refreshURL = $_SESSION ['redirectUrl'];
			unset ( $_SESSION ['redirectUrl'] );
		} else {
			$refreshURL = '/' . $languageURL . adminURL . '/';
		}
		
		refresh ( $refreshURL );
	} else {
		addErrorMessage ( $langArray [$status], "", "error" );
	}
}

if(isset($_POST['send'])) {
	$status = $cms->changePassword ( true );
	if ($status === true) {
		if (isset ( $_SESSION ['redirectUrl'] )) {
			$refreshURL = '/' . $languageURL . adminURL . '/login/';		
		}
		
		refresh ( $refreshURL );
	} else {
		addErrorMessage ( $langArray [$status], "", "error" );
	}
}

?>