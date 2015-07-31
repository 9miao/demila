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

    global $meta,$config;
    //前台
    if(!strstr($template_file,"admin/")){
        $template_file_opt = str_ireplace ( "apps", "templates/home/".$meta['template'], $template_file );
        $template_file_opt = ROOT_PATH . str_ireplace ( "controllers/", '', $template_file_opt);
        if (file_exists ( $template_file_opt )) {
            $_templateFile = $template_file_opt;
        }else{
            $config['template_data_path'] = $config['data_server_path'].'home/default/';//前台模板资源目录
            $config['template_path'] = $config['root_path'].'templates/home/default/';//前台模板目录
            $config['template_data'] = $config['data_server'].'home/default/';//前台模板资源目录
            abr ( "template_data", $config ['template_data'] );
            abr ( "template_data_path", $config ['template_data_path'] );
            abr ( "template_path", $config ['template_path'] );
            $template_file = str_ireplace ( "apps", "templates/home/default", $template_file );
            $template_file = ROOT_PATH . str_ireplace ( "controllers/", '', $template_file);
            $_templateFile = $template_file;
        }
    }
    //后台
    else{
        $template_file_opt = str_replace("admin/","",$template_file);
        $template_file_opt = str_ireplace ( "apps", "templates/admin/".$meta["admin_template"], $template_file_opt );
        $template_file_opt = ROOT_PATH . $template_file_opt;
        if (file_exists ( $template_file_opt )) {
            $_templateFile = $template_file_opt;
        }else {
            $config['admin_template_data_path'] = $config['data_server_path'].'admin/default/';//后台模板资源目录
            $config['admin_template_path'] = $config['root_path'].'templates/admin/default/';//后台模板目录
            $config['admin_template_data'] = $config['data_server'].'admin/default/';//后台模板资源目录
            abr ( "admin_template_data", $config ['admin_template_data'] );
            abr ( "admin_template_data_path", $config ['admin_template_data_path'] );
            abr ( "admin_template_path", $config ['admin_template_path'] );
            $template_file = str_replace("admin/","",$template_file);
            $template_file = str_ireplace ( "apps", "templates/admin/default", $template_file );
            $template_file = ROOT_PATH . $template_file;
            $_templateFile = $template_file;
        }

    }
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