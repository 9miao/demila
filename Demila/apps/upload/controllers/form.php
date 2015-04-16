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

if(!check_login_bool()) {
		$_SESSION['temp']['golink'] = '/'.$languageURL.'upload/index/';
		refresh('/'.$languageURL.'sign_in/');
}

	if($_SESSION['user']['quiz'] != 'true') {
		refresh('/'.$languageURL.'quiz/');
	} 	

	if(!isset($_GET['category']) || !is_numeric($_GET['category']) || $_GET['category'] == '0') {
            $_GET['category'] = '';
	}
//判断有截图包模块
require_once ROOT_PATH.'/apps/app_extends/models/app_extends.class.php';
$is_extends=false;
$app_extends=new app_extends();

if($app_extends->is_uploads()){
    $is_extends=true;
}
abr("is_extends",$is_extends);

$allCategories = $categoriesClass->getAllWithChilds(0, " `visible` = 'true' ");
	if(!array_key_exists($_GET['category'], $allCategories)) {
		addErrorMessage($langArray['error_wrong_category'], '', 'error');
		abr('hideForm', 'true');
	}
	else {
		
		if(!isset($_POST['category'])) {
			$_POST['category'] = 0;
		}
		
		$categoriesSelect = $categoriesClass->generateSelect($allCategories, $_POST['category'], $_GET['category']);
		abr('categoriesSelect', $categoriesSelect);

#加载属性
		require_once ROOT_PATH.'/apps/attributes/models/attributes.class.php';
		$attributesClass = new attributes();
		
		$attributes = $attributesClass->getAllWithCategories(" `visible` = 'true' AND `categories` LIKE '%,".(int)$_GET['category'].",%' ");
		abr('attributes', $attributes);
		
#保存作品
		if(isset($_POST['upload'])) {
			require_once ROOT_PATH.'/apps/items/models/items.class.php';
			$itemsClass = new items();
			
			$s = $itemsClass->add();
			if($s === true) {
				refresh('/'.$languageURL.'author_dashboard/', $langArray['complete_upload_item'], 'complete');
			}
			else {
				$message = '<ul>';
				foreach($s as $e) {
					$message .= '<li>'.$e.'</li>';
				}
				$message .= '</ul>';
				addErrorMessage($message, '', 'error');
			}
		}		
		
		$fileTypes = '';
	  foreach($config['upload_ext'] as $ext) {
	  	if($fileTypes != '') {	  		
	  		$fileTypes .= ';';
	  	}
	  	$fileTypes .= '*.'.$ext;	  	
	  }
	  abr('fileTypes', $fileTypes);
	  
	  abr('sessID', session_id());
		
	}
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'upload/form/?category='.$_GET['category'].'" title="">'.$langArray['upload_theme'].'</a>');		
	

?>