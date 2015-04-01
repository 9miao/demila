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


include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';



$config = array (

'system_core' => $configArr['system_core'], 	
'root_path' => $configArr['root_path'], 
'domain' => $configArr['domain'],  
'site_title' => 'DeMiLa',  
'use_language' => false,
'default_language' => 'zh',
'langs' => array('zh'),

'debug' => false,
'debug_ips' => array (
	'localhost', '::1', 
), 

'mysql_host' => $configArr['mysql_host'], 
'mysql_user' => $configArr['mysql_user'], 
'mysql_pass' => $configArr['mysql_pass'], 
'mysql_db' => $configArr['mysql_db'], 

'max_file_size' => 1024 * 1024 * 10,  //10 MB
'file_ext' => array (
	'pdf',
	'xls',
	'xlsx',
	'doc',
	'docx',
	'txt',
	'rtf',
	'png',
	'jpg' 
),

'max_upload_size' => 1024 * 1024 * 32,  //32 MB
'upload_ext' => array(
	'jpg',
	'png',
	'zip',
	'mp3',
	'wma'
),

'max_photo_size' => 1024 * 1024 * 10,  //10 MB
'photo_ext' => array (
	'jpg', 
	'gif', 
	'png' 
), 

'photo_sizes' => array (
	'A' => '50x50' 
),

'avatar_photo_sizes' => array (
	'A' => '80x80' 
),

'homeimage_photo_sizes' => array (
	'A' => '590x242' 
),
	
	
);


$config['data_server_path'] = $config['root_path'].'static/';

if(substr($_SERVER['SERVER_NAME'], 0, 4) == 'www.') {
	$config['data_server'] = 'http://www.'.$config['domain'].'/static/';
}
else {
	$config['data_server'] = 'http://'.$config['domain'].'/static/';
}

?>