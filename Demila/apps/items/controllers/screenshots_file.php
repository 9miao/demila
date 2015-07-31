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
	
	$itemID = get_id(2);
	
	$itemsClass = new items();
	
	$item = $itemsClass->get($itemID);
	if(!is_array($item) || (check_login_bool() && $item['status'] == 'unapproved' && $item['user_id'] != $_SESSION['user']['user_id']) || $item['status'] == 'queue' || $item['status'] == 'extended_buy') {
		die();
	}
	abr('item', $item);
	
	if(!isset($_GET['index']) || !is_numeric($_GET['index'])) {
		$_GET['index'] = 0;
	}
	
	$files = scandir(DATA_SERVER_PATH.'/uploads/items/'.$itemID.'/preview/');
	$previewFiles = array();
	if(is_array($files)) {
		foreach($files as $f) {
			if(file_exists(DATA_SERVER_PATH.'/uploads/items/'.$itemID.'/preview/'.$f)) {
				$fileInfo = pathinfo(DATA_SERVER_PATH.'/uploads/items/'.$itemID.'/preview/'.$f);
				if( isset($fileInfo['extension']) && ( strtolower($fileInfo['extension']) == 'jpg' || strtolower($fileInfo['extension']) == 'png' ) ) {
					$previewFiles[] = $f;					
				}
			}
		}
	}
	if(isset($previewFiles[$_GET['index']])) {
		abr('previewFile', $previewFiles[$_GET['index']]);
	}

?>