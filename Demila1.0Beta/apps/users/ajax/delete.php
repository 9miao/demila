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

define ( 'USING_LANGUAGE', false );

require_once '../../../config.php';
require_once $config ['root_path'] . '/core/functions.php';
include_once $config ['system_core'] . "/initEngine.php";

admin_login();

if (isset ( $_POST ['deleteUser'] ) && isset ( $_POST ['id'] )  && isset($_SESSION['user']['access']['users'])) {
	require_once ROOT_PATH . "/apps/users/models/users.class.php";
	$cms = new users( );
	
	$cms->delete ( intval ( $_POST ['id'] ) );
	die ( json_encode ( array_merge ( $_POST, array (
		'status' => 'true' 
	) ) ) );
}
elseif (isset ( $_POST ['deleteUserGroup'] ) && isset ( $_POST ['id'] ) && isset($_SESSION['user']['access']['users'])) {
	require_once ROOT_PATH . "/apps/users/models/groups.class.php";
	$cms = new groups( );
	
	$cms->delete ( intval ( $_POST ['id'] ) );
	die ( json_encode ( array_merge ( $_POST, array (
		'status' => 'true' 
	) ) ) );
}
elseif (isset ( $_POST ['deleteWithdraw'] ) && isset ( $_POST ['id'] ) && isset($_SESSION['user']['access']['users'])) {
	require_once ROOT_PATH . "/apps/users/models/deposit.class.php";
	$cms = new deposit( );
	
	$cms->deleteWithdraw ( intval ( $_POST ['id'] ) );
	die ( json_encode ( array_merge ( $_POST, array (
		'status' => 'true' 
	) ) ) );
}
elseif (isset ( $_POST ['deleteComment'] ) && isset ( $_POST ['id'] ) && isset($_SESSION['user']['access']['users'])) {
	require_once ROOT_PATH . "/apps/items/models/comments.class.php";
	$cms = new comments( );
	
	$cms->delete( intval ( $_POST ['id'] ) );
	die ( json_encode ( array_merge ( $_POST, array (
		'status' => 'true' 
	) ) ) );
}
elseif (isset ( $_POST ['deleteBalance'] ) && isset ( $_POST ['id'] ) && isset($_SESSION['user']['access']['users'])) {
	require_once ROOT_PATH . "/apps/users/models/balance.class.php";
	$cms = new balance( );
	
	$cms->delete( intval ( $_POST ['id'] ) );
	die ( json_encode ( array_merge ( $_POST, array (
		'status' => 'true' 
	) ) ) );
}

echo json_encode ( array_merge ( $_POST, array (
	'status' => 'unknown error' 
) ) );
die ();

?>