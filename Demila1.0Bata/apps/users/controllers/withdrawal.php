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
_setTitle($langArray['withdrawal_setTitle']); 

if(!check_login_bool()) {
	$_SESSION['temp']['golink'] = '/'.$languageURL.'withdrawal/';
	refresh('/'.$languageURL.'sign_in/');
}

    $have_service=false;
    //判断有无安装客户服务
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/apps/service/controllers/index.php')) {
    //判断启动状态
    require_once ROOT_PATH.'/apps/app_extends/models/app_extends.class.php';
    $app_extends=new app_extends();
    $is_open=$app_extends->getStatus("客户服务管理");
    if($is_open){
        require_once ROOT_PATH.'/apps/service/models/service.class.php';
        $serviceClass= new service();
        $service= $serviceClass->getserviceByuserid($_SESSION['user']['user_id']);
        abr("service",$service);
        $have_service= true;
    }
}
    abr("have_service",$have_service);

	$usersClass = new users();
	
	$user = $usersClass->get($_SESSION['user']['user_id']);
	abr('user', $user);

    /*
     * 查询未完成提现
    */
    $checkWithdraw=$usersClass->checkWithdraw($_SESSION['user']['user_id']);

    if($checkWithdraw)
        $checkWithdraw=1;
    else
        $checkWithdraw=0;
    abr('checkWithdraw', $checkWithdraw);

	$date['year'] = date('Y');
	$date['month'] = date('n');
	$date['day'] = date("t");
	abr('date', $date);	
	
	if(isset($_POST['submit'])) {
		$depositClass = new deposit();
		
		$s = $depositClass->addWithdraw();
		if($s === true) {
			refresh('/'.$languageURL.'withdrawal/', $langArray['complete_add_withdrawal'], 'complete');
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
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/dashboard/" title="">'.$langArray['my_account'].'</a> \ <a href="/'.$languageURL.'users/withdrawal/" title="">'.$langArray['withdrawal'].'</a>');		
	


?>