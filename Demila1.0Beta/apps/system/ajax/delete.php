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

if (isset ( $_POST ['deleteKey'] ) && isset ( $_POST ['id'] ) && isset($_SESSION['user']['access']['system'])) {
	require_once ROOT_PATH . "/apps/system/models/system.class.php";
	$cms = new system( );
	
	$cms->delete ( intval ( $_POST ['id'] ) );
	die ( json_encode ( array_merge ( $_POST, array (
		'status' => 'true' 
	) ) ) );
} elseif (isset ( $_POST ['deleteRow'] ) && isset ( $_POST ['id'] ) && isset($_SESSION['user']['access']['system'])) {
	require_once ROOT_PATH.'/apps/system/models/badges.class.php';
	$badges = new badges();
	
	$badges->delete ( intval ( $_POST ['id'] ) );
	die ( json_encode ( array_merge ( $_POST, array (
		'status' => 'true' 
	) ) ) );
} 

echo json_encode ( array_merge ( $_POST, array (
	'status' => 'unknown error' 
) ) );
die ();

?>