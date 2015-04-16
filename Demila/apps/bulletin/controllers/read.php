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

_setView(__FILE__); 

	if(!isset($_GET['bulletin_id'])) {
		$_GET['bulletin_id'] = '0';
	}
	
	if(!isset($_COOKIE['bulletin'.$_GET['bulletin_id']])) {
		require_once ROOT_PATH . "/apps/bulletin/models/bulletin.class.php";
		$bulletinClass = new bulletin();
		
		$bulletinClass->incRead($_GET['bulletin_id']);
	
		setcookie('bulletin'.$_GET['bulletin_id'], 'read', time()+2592000, "/", ".".$config['domain']);
	}

	header ( "Content-type: image/png" );
	
	//创建图像
	$image = imagecreate ( 1, 1 ) or die ( 'image create error' );
	$background_color = imagecolorallocate ( $image, 255, 255, 255 );
	imagepng ( $image );
	
?>