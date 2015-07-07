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
 * 设置view文件
 */
function _setView($path) {
	_setTemplate($path);
	return true;
}

/*
 * 设置模板路径 /apps/<module>/views/<template>.html
 */
function _setTemplate($path, $prefix = "") {

	global $_templateFile;

	$dir = dirname ( $path );

	$file = basename ( $path, '.php' ) . '.html'; //文件名template.tmpl

	$num = strlen ( ROOT_PATH );

	$template_file = substr ( $dir, $num ) . '/' . $prefix . $file;

    if(!strstr($template_file,"admin/")){
        global $meta;
        $template=$meta['template'];
        $template_file = str_ireplace ( "apps\\", "templates/".$template."/", $template_file );
        $template_file = str_ireplace ( "apps/", "templates/".$template."/", $template_file );
    }

      $template_file = ROOT_PATH . str_ireplace ( "controllers/", '', $template_file );

    if (! file_exists ( $template_file )) {
		die ( "template not exist! function: " . __FUNCTION__ . " file: " . $template_file );
	}
	$_templateFile = $template_file;
	abr ( 'content_template', $template_file );


	return true;
}
/*
 * 设置header的title <title>$title</title>
 */
function _setTitle($title) {
	abr ( 'title', $title );
}

/*
 * 更改默认布局
 */
function _setLayout($layout) {
	global $_layoutFile;

	$_layoutFile = $layout;

	return true;
}


?>