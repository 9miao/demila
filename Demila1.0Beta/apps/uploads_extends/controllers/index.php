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

header("Content-type:text/html;charset=utf-8");

_setView(__FILE__);
//_setLayout('rss');
include_once $config ['system_core'] . "/initEngine.php";
require_once $config ['root_path'] . '/core/functions.php';

$uploads_extends=new uploads_extends();
#检查安装
$is_setup=$uploads_extends->is_setup();

if(!$is_setup) {
    $setup=$uploads_extends ->setup();
    if($setup)
    die("安装成功");
}else{
    die("已安装");
}


