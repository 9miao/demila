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
 * 检查config文件是否加载
 */
if (!isset($config)) {
	die(" 错误:config文件不存在！");
}

/*
 * 系统定义
 */
define("DOMAIN", $config['domain']);
define("ENGINE_PATH", $config['system_core']);
define("ROOT_PATH", $config['root_path']);
define("CACHE", ENGINE_PATH . "/data/cache/");
define("DATA_SERVER_PATH", $config['data_server_path']);
define("DATA_SERVER", $config['data_server']);
define("TEMPLATE_PATH", ROOT_PATH . "/html/");
define("VERSION", '1.0.1');
#END;


/*
 * php ini 设置
 */

/**bug
ini_set ( "session.cookie_domain", "." . DOMAIN );
ini_set ( "session.save_path", CACHE . "/session/" );
ini_set ( "session.use_only_cookies", true );
ini_set ( "session.use_trans_sid", false );
**/

ini_set ( "session.cookie_domain", "");
ini_set ( "session.save_path", CACHE . "/session/" );
ini_set ( "session.use_only_cookies", false );
ini_set ( "session.use_trans_sid", true );


ini_set ( "arg_separator.output", "&amp;" );
ini_set ( 'register_globals', "Off" );
ini_set ( 'allow_url_fopen', "Off" );
ini_set ( 'magic_quotes_gpc', "Off" );
ini_set ( 'magic_quotes_runtime', "Off" );
date_default_timezone_set ( "PRC" );


error_reporting ( 0 );
#END;


/*
 * 包含文件
 */
include_once ENGINE_PATH . '/system/functions.php';
include_once ENGINE_PATH . '/system/core.functions.php';
include_once ENGINE_PATH . '/system/core.security.php';
include_once ENGINE_PATH . '/system/core.template.php';
#END;


/**
 * 执行时间 php debug
 */
if (check_debug ()) {
	$execute = new execute();
	$execute->start(1);
	
	/*
	 * Debug container
	 */
	$debug = '<B>Debug container:</B><BR />';	
}
#END;

/*
 * CACHE
 */
$cache = new cache ( );
$cache->cacheDir = CACHE;
global $cache;

/*
 * SESSION
 */

$session = new session ( );

/*
 * MySQL连接
 */
$mysql = new mysql ( $config ['mysql_user'], $config ['mysql_pass'], $config ['mysql_db'], $config ['mysql_host'] );
global $mysql;


/*
 * Smarty设置
 */
$_layoutFile = 'index'; 
$_templateFile = ''; 

define ( 'SMARTY_DIR', ENGINE_PATH . "classes/Smarty/" );

include_once (SMARTY_DIR . "Smarty.class.php");
$smarty = new Smarty ( );
$smarty->compile_dir = CACHE . "/templates_cache/";
$smarty->compile_check = true;
$smarty->debugging = false;

abr ( 'domain', DOMAIN );
abr ( "root_path", ROOT_PATH );
abr ( "data_server", $config ['data_server'] );
$smarty->register_function ( 'createEditor', 'createTextAreaEditor' ); 

global $smarty;
#END;

/*
 * 读取$_SESSION中的flash信息
 */
if ($message = getRefreshMessage ()) {
	addErrorMessage ( $message['title'], $message['text'], $message['type'] );
}

/*
 * 设置默认分页变量
 * LIMIT = 10 
 */
if (! defined ( 'LIMIT' )) {
	define ( 'LIMIT', 10, true );
}
if (isset ( $_GET ['p'] ) && is_numeric ( $_GET ['p'] ) && $_GET ['p'] > 1) {
	define ( 'PAGE', intval ( $_GET ['p'] ) );
	define ( 'START', (PAGE - 1) * LIMIT );
} else {
	define ( 'PAGE', 1 );
	define ( 'START', 0 );
}
#END;

define ( 'adminURL', 'admin' );

include_once ENGINE_PATH . '/system/core.url.php';
include_once ENGINE_PATH . '/system/core.languages.php';

?>