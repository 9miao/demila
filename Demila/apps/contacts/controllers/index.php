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
_setTitle($langArray['contacts']);
    //是否登录
    check_login();
#加载类别
	$categoriesClass = new ccategories();
	
	$categories = $categoriesClass->getAll(0, 0, " `visible` = 'true'");
	abr('categories', $categories);

#发送联系支持请求
	if(isset($_POST['action'])) {
		//验证码验证
		if(isset($_POST['verify'])) {
			if(empty($_POST['verify'])){
				addErrorMessage($langArray['error_verify_invalid_empty'], '', 'error');
			}
	        require_once ROOT_PATH.'/classes/Verify.class.php';
		    $verify = new Verify(); 
		    $yz_verify = $verify->check($_POST['verify'], 1);
		    if(!$yz_verify){
		    	addErrorMessage($langArray['error_invalid_verify'], '', 'error');
		    }else{
		    	$contactsClass = new contacts();
		
				$s = $contactsClass->add();
				if($s === true) {
					refresh('/'.$languageURL.'support/', $langArray['complete_send_email'], 'complete');
				}
				else {
					addErrorMessage($langArray['error_all_fields_required'], '', 'error');
				}
		    }

		}else{
			addErrorMessage($langArray['error_verify_invalid_empty'], '', 'error');
		}
		
	}	
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'contacts/" title="">'.$langArray['contacts'].'</a>');		
	

?>