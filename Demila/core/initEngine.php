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

define("VERSION", '1.0.3Beta');
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
ini_set ( "session.use_only_cookies", true ); //是否仅仅使用cookie在客户端保存会话sessionid
ini_set ( "session.use_trans_sid",  false );  //客户端浏览器禁止cookie的时候，页面上的链接会基于url传递SESSIONID


ini_set ( "arg_separator.output", "&amp;" );
ini_set ( 'register_globals', "Off" );
ini_set ( 'allow_url_fopen', "Off" );
ini_set ( 'magic_quotes_gpc', "Off" );
ini_set ( 'magic_quotes_runtime', "Off" );
date_default_timezone_set ( "PRC" );


error_reporting (1);
#END;




/*
 * 包含文件
 */
include_once ENGINE_PATH . '/system/functions.php';
include_once ENGINE_PATH . '/system/core.functions.php';
include_once ENGINE_PATH . '/system/core.security.php';

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
$smarty->register_function ( 'createEditor', 'createTextAreaEditor' );

global $smarty;
#END;


//模板设置

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

//模板目录
define("TEMPLATE_PATH", ROOT_PATH . "/templates/".$meta['template']."/html/");
$config['template_data_path'] = $config['data_server_path'].'templates/'.$meta['template'].'/';
$config['template_path'] = $config['root_path'].'templates/'.$meta['template'].'/';
$config['template_data'] = $config['data_server'].'templates/'.$meta['template'].'/';

//$config['data_server_path'] = $config['root_path'].'static/templates/'.$config['template'].'/';


abr ( 'domain', DOMAIN );
abr ( "root_path", ROOT_PATH );
abr ( "data_server", $config ['data_server'] );
abr ( "template_data", $config ['template_data'] );
abr ( "template_data_path", $config ['template_data_path'] );
abr ( "template_path", $config ['template_path'] );


/*
* 包含模板设置文件
*/
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