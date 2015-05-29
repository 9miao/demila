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
_setTitle($langArray['signup_setTitle']);

//重新发送邮件
if(isset($_POST['res_send']) && $_POST['res_send'] == 'yes' && isset($_POST['user_id']) && !empty($_POST['user_id'])){
    $usersClass = new users();
    $res = $usersClass->res_send($_POST['user_id']);
    if($res){
        die('success');
    }
}

if(check_login_bool()) {
	refresh('/'.$languageURL.'edit/');
}

	if(get_id(2) == 'verify') {
		abr('verify', 'yes');
	}
	elseif(get_id(2) == 'complete') {
		abr('complete', 'yes');
	}
	else {

		require_once ROOT_PATH.'/apps/pages/models/pages.class.php';
		$pagesClass = new pages();
		
		$terms = $pagesClass->getByKey('terms');
		abr('terms', $terms);
		
#LOAD RE-CAPTCHA	
		//require_once ROOT_PATH.'/classes/recaptchalib.php';
		
		//abr('recaptcha', recaptcha_get_html($config['recaptcha_public_key']));


        //判断用户名是否存在
        if (isset($_POST["username"]) && $_POST["action"]=="check") {
            $usersClass = new users();
            $result = $usersClass->isExistUsername($_POST["username"]);
            if ($result)
                die('1');
            else
                die('0');
        }

		#用户注册操作
		if(isset($_POST['add'])) {
            require_once ROOT_PATH.'/apps/system/models/system.class.php';

            $cms = new system ();

            $data = $cms->getAll(0,0,null,"send_mail");

            if($data[0]["value"] == 0){
                $_POST['status'] = 'activate';
            }
			$usersClass = new users();
			$s = $usersClass->add();
			if($s === true) {
                if($data[0]["value"] == 0){
                    refresh('/'.$languageURL.'sign_in/');
                }
				refresh('/'.$languageURL.'sign_up/verify/');
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
		
	}
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/registration/" title="">'.$langArray['sign_up'].'</a>');		
	
	
?>