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
_setTitle($langArray['signin_setTitle']);


if(check_login_bool()) {
	refresh('/'.$languageURL.'edit/');
}




    //重新发送邮件
    if(isset($_POST['res_send']) && $_POST['res_send'] == 'yes' && isset($_POST['user_id']) && !empty($_POST['user_id'])){
        $usersClass = new users();
        $res = $usersClass->res_send($_POST['user_id']);
        if($res){
            die('success');
        }
    }

#激活资料页	
	if(isset($_GET['command']) && $_GET['command'] == 'activate' && isset($_GET['user']) && isset($_GET['key'])) {
		$usersClass = new users();
		
		$s = $usersClass->activateUser($_GET['user'], $_GET['key']);
		if($s === true) {
			refresh('/'.$languageURL.'sign_up/complete/');
		}
		else {
			addErrorMessage($s['valid'], '', 'error');
		}
	}
	
	

#登录	
	if(isset($_POST['login'])) {
		$usersClass = new users();
		$s = $usersClass->login();
		if($s === true) {
			if(isset($_SESSION['temp']['golink'])) {
				$web = $_SESSION['temp']['golink'];
				unset($_SESSION['temp']['golink']);
				refresh($web);
			}
			refresh('/'.$languageURL);
		}
		else {
//            error_invalid_username_or_password
            //账号未激活
            if($s == 'error_invalid_activation_no'){
                //通过用户名密码获取用户信息
                $username = $_POST['username'];
                $password = $_POST['password'];
                $user_info = $usersClass->getuserinfoByNamePwd($username,$password);
                $usersClass->res_send($user_info['user_id']);
                $res_data['show_status'] = 1;
                $res_mail = 'http://'.$usersClass->gotomail($user_info['email']);
                $_SESSION["THE_USER_RES_SEND_MAIL_4_M_MAIL"] = $res_mail;
                $_SESSION["THE_USER_RES_SEND_MAIL_4_M"] = $user_info['user_id'];

            }else{
                $res_data['user_info'] = array();
                $res_data['show_status'] = 0;
            }
            abr('res_data',$res_data);
			addErrorMessage($langArray[$s], '', 'error');
		}
	}
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/login/" title="">'.$langArray['login'].'</a>');


?>