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


if (isset ( $_GET ['module'] ) && isset ( $_GET ['controller'] )) {
	
	//加载administration...
	if (isAdministration ( $_GET ['module'] )) {
		define ( "inc", ENGINE_PATH . '/administration/controllers/' . $_GET ['controller'] . '.php' );
	} else {
		define ( "inc", ROOT_PATH . '/apps/' . $_GET ['module'] . '/controllers/' . $_GET ['controller'] . '.php' );
	}
	
	if (file_exists ( inc )) {
		include_once (inc);
	} else {		
		//加载index.php
		define ( "inc2", ROOT_PATH . '/apps/' . $_GET ['module'] . '/controllers/index.php' );		
		if (file_exists ( inc2 )) {
			include_once (inc2);
		} else {
		header("HTTP/1.0 404 Not Found");
        header("Location: http://". DOMAIN ."/".$languageURL."error");			 
		}
	}
} elseif (isset ( $_GET ['module'] )) {
	//加载administration
	if (isAdministration ( $_GET ['module'] )) {
		define ( "inc", ENGINE_PATH . '/administration/controllers/index.php' );
	} else {
		define ( "inc", ROOT_PATH . '/apps/' . $_GET ['module'] . '/controllers/index.php' );
	}
	
	if (file_exists ( inc )) {
		
		include_once (inc);
	
	} else {		
	/**
	 * 重定向到404页面
	 */
		header("HTTP/1.0 404 Not Found");
        header("Location: http://". DOMAIN ."/".$languageURL."error");
	}
} else {
	$_GET ['module'] = 'index';
	include_once (ROOT_PATH . "/apps/index/controllers/index.php");
}

/*
 * smarty显示
 */

if($_templateFile == '' && $_layoutFile=='') {
    //模板目录
    $_templateFile = ROOT_PATH.'/templates/'.$meta['template'].'/index/index.html';
    $smarty->display ($_templateFile);
}
else if ($_templateFile == '' && $_layoutFile!='') {
	restore_error_handler ();
	flush ();
    $smarty->display ( TEMPLATE_PATH ."html/". $_layoutFile . ".html" );
}
else if($_templateFile != '' && $_layoutFile!='') {
    restore_error_handler ();
    flush ();
    $smarty->display ( TEMPLATE_PATH ."html/". $_layoutFile . ".html" );
}
else{
    restore_error_handler ();
    flush ();
    $smarty->display ($_templateFile);
}
#END;


/*
 * debug
 */
include_once ('system/debug.php');

/*
 * 关闭mysql连接
 */
if (isset ( $mysql )) {
	$mysql->close ();
}

?>