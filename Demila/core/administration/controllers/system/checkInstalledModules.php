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
 * 加载全部modules
 */
$installedModules = array ();
foreach ( array_diff ( scandir ( ROOT_PATH . "apps/" ), array (
	
	'.', 
	'..', 
	'index' 
) ) as $module ) {
	if (is_dir ( ROOT_PATH . "apps/" . $module )) {

		//检查管理员配置module
		if (file_exists ( ROOT_PATH . "/apps/" . $module . "/admin/admin_config.php" )) {
			require_once ROOT_PATH . "/apps/" . $module . "/admin/admin_config.php";
			if (! isset ( $admin_config ['show'] ) || $admin_config ['show'] === true) {
				$installedModules [$module] = $admin_config;
			}
		} else {
			$installedModules [$module] = true;
		}
		
	}
}
abr ( 'modules', $installedModules );
################################################


if (isset ( $_GET ['m'] ) && isset ( $_GET ['c'] ) && file_exists ( ROOT_PATH . '/apps/' . $_GET ['m'] . '/admin/' . $_GET ['c'] . '.php' )) {
	$smarty->assign ( 'content_template', ROOT_PATH . '/apps/' . $_GET ['m'] . '/admin/' . $_GET ['c'] . '.html' );
	require_once ROOT_PATH . '/apps/' . $_GET ['m'] . '/admin/' . $_GET ['c'] . '.php';
} elseif (isset ( $_GET ['m'] ) && file_exists ( ROOT_PATH . '/apps/' . $_GET ['m'] . '/admin/index.php' )) {
	$smarty->assign ( 'content_template', ROOT_PATH . '/apps/' . $_GET ['m'] . '/admin/index.html' );
	require_once ROOT_PATH . '/apps/' . $_GET ['m'] . '/admin/index.php';
} /*else {
	die ( "Controller未找到!" );
}*/

?>