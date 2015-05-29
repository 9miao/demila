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
require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';
$index = 'http://demila.org';
    //新闻
    require_once ROOT_PATH.'/classes/Http.class.php';
    $http = new Http();
    $url = $index.'/getnews';
    $news = $http->curlGet($url);
    $news = json_decode($news,1);
    abr('news',$news);
    $more_url = $index.'/news/list';
    abr('more',$more_url);

    //开发团队信息
    $http = new Http();
    $url = $index.'/getteam';
    $team = $http->curlGet($url);
    $team = json_decode($team,1);
    abr('team',$team[0]);
    
    //最新版本
    $http = new Http();
    $url = $index.'/getversion';
    $version = $http->curlGet($url);
    $version = json_decode($version,1);
    abr('version',$version[0]);
    $download=$index."/demiladata/data/";
    abr('download',$download);

    //系统信息
    $sysinfo = array();
    $sysinfo['mysqlv'] = mysql_get_server_info();
    $sysinfo['phpv'] = phpversion();
    $sysinfo['web_server'] = strpos($_SERVER['SERVER_SOFTWARE'], 'PHP')===false ? $_SERVER['SERVER_SOFTWARE'].'PHP/'.phpversion() : $_SERVER['SERVER_SOFTWARE'];
    $sysinfo['os'] = PHP_OS;
    $sysinfo['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'unknown';
    $sysinfo['unfun'] = get_cfg_var( "disable_functions")?get_cfg_var( "disable_functions"): "无" ;
    $sysinfo['ip'] = $_SERVER['SERVER_ADDR'] .':'.$_SERVER['SERVER_PORT'];
    $sysinfo['outtime'] = get_cfg_var( "max_execution_time");
    $sysinfo['server_time'] = date('Y-m-d H:i:s').'&nbsp;'.date_default_timezone_get();
    $sysinfo['root_path'] = ROOT_PATH;
    abr('sysinfo',$sysinfo);


?>