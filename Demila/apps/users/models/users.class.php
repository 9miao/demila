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


class users extends base {

	public $avatarError = false;
	public $homeimageError = false;

	function __construct() {
		global $config;

		$this->photoSizes = $config['avatar_photo_sizes'];
		$this->uploadFileDirectory = 'users/';
	}


/*
 * 获取函数
 */
//获取用户粉丝数量及个人信息
public function getAll($start=0, $limit=0, $where='', $order='`username` ASC') {
    global $mysql;

    $limitQuery = "";
    if($limit!=0) {
        $limitQuery = "LIMIT $start,$limit";
    }

    if($where != '') {
        $where = " WHERE ".$where;
    }

    $mysql->query("
        SELECT
            SQL_CALC_FOUND_ROWS *,
            ( SELECT COUNT(follow_id) FROM `users_followers` WHERE  `user_id` = `users`.`user_id` ) AS followers
        FROM `users`
        $where
        ORDER BY $order
        $limitQuery
    ");

    if($mysql->num_rows() == 0) {
        return false;
    }

    $return = array();
    while($d = $mysql->fetch_array()) {
        $return[$d['user_id']] = $d;

    }

    $this->foundRows = $mysql->getFoundRows();

    return $return;
}

//通过用户名密码获取用户信息
public function get_user_info_by_pn($uname='',$pwd=''){
    global $mysql;
    $mysql->query("
        SELECT *
        FROM `users`
        WHERE `username` = '".sql_quote($uname)."' AND `password` = '".md5(md5($pwd))."'
    ", __FUNCTION__ );
    $row = $mysql->fetch_array();
    return $row;
}

//通过用户id获取用户详细信息
public function get($id) {
    global $mysql, $langArray;

    $return = $mysql->getRow("
        SELECT *
        FROM `users`
        WHERE `user_id` = '".intval($id)."'
    " );

    if(is_array($langArray['moneyArr'])) {
        foreach($langArray['moneyArr'] as $k=>$v) {
            $return['moneyText'] = $v;
            if($return['buy'] <= $k) {
                break;
            }
        }
    }

    if(is_array($langArray['earningArr'])) {
        foreach($langArray['earningArr'] as $k=>$v) {
            $return['earningText'] = $v;
            if($return['sold'] <= $k) {
                break;
            }
        }
    }

    if($return['license'] != '') {
        $buff = unserialize($return['license']);
        unset($return['license']);
        $return['license'] = $buff;
        unset($buff);
    }
    if($return['social'] != '') {
        $buff = unserialize($return['social']);
        unset($return['social']);
        $return['social'] = $buff;
        unset($buff);
    }

    if($return['groups'] != '') {
        $groups = unserialize($return['groups']);
        unset($return['groups']);
        if(is_array($groups) && !empty($groups)) {

            $groupsWhere = '';

            foreach($groups as $k=>$v) {
                $return['groups'][$k] = $v;

                if($groupsWhere != '') {
                    $groupsWhere .= " OR ";
                }
                $groupsWhere .= " `ug_id` = '".intval($k)."' ";
            }

            $mysql->query("
                SELECT *
                FROM `user_groups`
                WHERE $groupsWhere
            ", __FUNCTION__ );

            if($mysql->num_rows() > 0) {
                $return['is_admin'] = true;

                while($d = $mysql->fetch_array()) {

                    $modules = unserialize($d['rights']);
                    foreach($modules as $k=>$v) {
                        if(!isset($return['modules'][$k])) {
                            $return['modules'][$k] = true;
                        }
                    }

                }

                $return['access'] = $return['modules'];
            }
            else {
                $return['modules'] = '';
            }

        }
        else {
            $return['groups'] = '';
        }
    }

    $mysql->query("
        SELECT *
        FROM `countries`
        WHERE `id` = '".intval($return['country_id'])."'
    ");

    $return['country'] = $mysql->fetch_array();

    $mysql->query("
        SELECT *
        FROM `users_status`
        WHERE `user_id` = '".intval($return['user_id'])."'
    ");

    if($mysql->num_rows() > 0) {
        while($d = $mysql->fetch_array()) {
            $return['statuses'][$d['status']] = $d;
        }
    }

    return $return;
}

//通过ID获取单个用户信息
public function getuserinfoById($id=0){
    global $mysql;
    $mysql->query("
            SELECT *
            FROM `users`
            WHERE `user_id` = '".intval($id)."'
    ");
    $return = $mysql->fetch_array();
    return $return;
}


    //二次发送激活邮件
    public function  res_send($id){
        global $mysql, $langArray, $languageURL, $config, $meta;
        $user_info = $this->getuserinfoById($id);
        
         require_once ENGINE_PATH.'/classes/email.class.php';

        //判断有无客服功能
        $have_service=false;
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/apps/service/controllers/index.php')) {
            //判断启动状态
            require_once ROOT_PATH.'/apps/app_extends/models/app_extends.class.php';
            $app_extends=new app_extends();
            $is_open=$app_extends->getStatus("客户服务管理");
            if($is_open){
                $have_service= true;
            }
        }
        if($have_service){
            //通过用户id获取关联客服
            require_once ROOT_PATH.'/apps/service/models/service.class.php';
            $service = new service();
            $theservice = $service->getserviceByuserid($user_info['user_id']);

            $emailClass = new email();
            $link = 'http://'.$config['domain'].'/'.$languageURL.'sign_in/?command=activate&user='.$user_info['username'].'&key='.$user_info['activate_key'];
            $link = '<a href="'.$link.'" target="_blank">'.$link.'</a>';
            $emailClass->contentType = 'text/html';
            $emailClass->fromEmail = 'no-reply@'.$config['domain'];
            $emailClass->subject = '['.$meta['meta_title'].']发给['.$user_info['nickname'].']的注册激活邮件';
            $emailClass->message = 'Hi！['.$user_info['nickname'].']：<br />
                                    &nbsp;&nbsp;&nbsp;&nbsp;幸亏没放弃，终于等到你！想给你写信已经很久了！<br />
                                    &nbsp;&nbsp;&nbsp;&nbsp;这是你的登录信息，激活前处于挂起状态：<br />
                                    &nbsp;&nbsp;&nbsp;&nbsp;帐号：['.$user_info['username'].']<br />
                                    &nbsp;&nbsp;&nbsp;&nbsp;密码：*********<br />
                                    &nbsp;&nbsp;&nbsp;&nbsp;这是你的激活连接，小编在等候你的回应：<br />
                                    &nbsp;&nbsp;&nbsp;&nbsp;'.$link.'<br />
                                    &nbsp;&nbsp;&nbsp;&nbsp;速速点击上面的激活链接来启用它们！<br />
                                    &nbsp;&nbsp;&nbsp;&nbsp;小编已经等不及啦！<br />
                                    <br />
                                    &nbsp;&nbsp;&nbsp;&nbsp;专属小编：['.$theservice['user_name'].']<br />
                                    &nbsp;&nbsp;&nbsp;&nbsp;['.$meta['meta_title'].']<br />
                                    &nbsp;&nbsp;&nbsp;&nbsp;['.date('Y-m-d H:i:s',time()).']<br />';

            $emailClass->to($user_info['email']);
            $emailClass->send();
            unset($emailClass);
            return true;
        }else{
            #发送激活链接
           
            $emailClass = new email();
            $emailClass->fromEmail = 'no-reply@'.$config['domain'];
            $emailClass->subject = '['.$config['domain'].'] '.$langArray['email_activate_subject'];
            $emailClass->message = langMessageReplace($langArray['email_activate_text'], array(
                'DOMAIN' => $config['domain'],
                'LINK' => 'http://'.$config['domain'].'/'.$languageURL.'sign_in/?command=activate&user='.$user_info['username'].'&key='.$user_info['activate_key']
            ));
            $emailClass->to($user_info['email']);
            $emailClass->send();
            unset($emailClass);
            return true;
        }
    }

    //重置邮箱
    public function res_mail($id){
        global $mysql, $langArray, $languageURL, $config;
        $user_info = $this->getuserinfoById($id);
        $mysql->query("
            UPDATE `users`
            SET `email` = '".sql_quote($_POST['email'])."'
            WHERE `user_id` = '".intval($user_info['user_id'])."'
            LIMIT 1
        ");
        $res_mail = 'http://'.$this->gotomail($_POST['email']);
        $_SESSION["THE_USER_RES_SEND_MAIL_4_M_MAIL"] = $res_mail;
        $res = $this->res_send($user_info['user_id']);
        if($res){
            return $res_mail;
        }
    }


    //查询未完成提现
    public function checkWithdraw($user_id){
        global $mysql;

        $mysql->query("
			SELECT *
			FROM `withdraw`
			WHERE `user_id` = '".intval($user_id)."'
			and `paid` = 'false'
		", __FUNCTION__ );

        if($mysql->num_rows() == 0) {
            return false;
        }
        return true;
    }

    //通过用户名密码获取单个用户信息
    public function getuserinfoByNamePwd($username='',$pwd=''){
        global $mysql;
        $mysql->query("
			SELECT *
			FROM `users`
			WHERE `username` = '".sql_quote($username)."' AND `password` = '".md5(md5($pwd))."'
		", __FUNCTION__ );

        $row = $mysql->fetch_array();
        return $row;
    }

    public function gotomail($mail){
        //功能：根据用户输入的Email跳转到相应的电子邮箱首页
        $t=explode('@',$mail);
        $t=strtolower($t[1]);
        $mails_address = array(
            array('key'=>'163.com','value'=>'mail.163.com'),
            array('key'=>'vip.163.com','value'=>'vip.163.com'),
            array('key'=>'126.com','value'=>'mail.126.com'),
            array('key'=>'qq.com','value'=>'mail.qq.com'),
            array('key'=>'vip.qq.com','value'=>'mail.qq.com'),
            array('key'=>'foxmail.com','value'=>'mail.qq.com/cgi-bin/loginpage?t=fox_loginpage&sid=,2,zh_CN&r=a5df221d27ddbb13cc2182e934baa805'),
            array('key'=>'gmail.com','value'=>'mail.google.com'),
            array('key'=>'sohu.com','value'=>'mail.sohu.com'),
            array('key'=>'vip.sina.com','value'=>'vip.sina.com'),
            array('key'=>'sina.com.cn','value'=>'mail.sina.com.cn'),
            array('key'=>'sina.com','value'=>'mail.sina.com.cn'),
            array('key'=>'sina.cn','value'=>'mail.sina.com.cn'),
            array('key'=>'tom.com','value'=>'mail.tom.com'),
            array('key'=>'yahoo.com.cn','value'=>'mail.cn.yahoo.com'),
            array('key'=>'yahoo.cn','value'=>'mail.cn.yahoo.com'),
            array('key'=>'yeah.net','value'=>'www.yeah.net'),
            array('key'=>'21cn.com','value'=>'mail.21cn.com'),
            array('key'=>'hotmail.com','value'=>'www.hotmail.com'),
            array('key'=>'sogou.com','value'=>'mail.sogou.com'),
            array('key'=>'188.com','value'=>'www.188.com'),
            array('key'=>'139.com','value'=>'mail.10086.cn'),
            array('key'=>'189.cn','value'=>'mail.189.cn'),
            array('key'=>'wo.com.cn','value'=>'mail.wo.com.cn/smsmail'),
            array('key'=>'aliyun.com','value'=>'mail.aliyun.com'),
            array('key'=>'263.net','value'=>'www.263.net'),
            array('key'=>'outlook.com','value'=>'www.outlook.com'),
        );
        foreach($mails_address as $address){
            $mail_arr[] = $address['key'];
        }
        if(!in_array($t, $mail_arr)){
            return 'www.baidu.com/s?wd='.$t;
        }else{
            foreach($mails_address as $item){
                if($t==$item['key']){
                    return $item['value'];
                    break;
                }
            }
        }
    }

	public function getByUsername($username) {
		global $mysql;
		
		$return = $mysql->getRow("
			SELECT *
			FROM `users`
			WHERE `username` = '".sql_quote($username)."'
		" );
		
		if(!is_array($return)) {
			return false;
		}
		
		$buff = unserialize($return['license']);
		unset($return['license']);
		$return['license'] = $buff;
		unset($buff);		
		$buff = unserialize($return['social']);
		unset($return['social']);
		$return['social'] = $buff;
		unset($buff);
		
		$groups = unserialize($return['groups']);
		unset($return['groups']);		
		if(is_array($groups) && !empty($groups)) {
			
			$groupsWhere = '';
			
			foreach($groups as $k=>$v) {
				$return['groups'][$k] = $v;
				
				if($groupsWhere != '') {
					$groupsWhere .= " OR ";
				}
				$groupsWhere .= " `ug_id` = '".intval($k)."' ";
			}
			
			$mysql->query("
				SELECT *
				FROM `user_groups`
				WHERE $groupsWhere
			", __FUNCTION__ );
			
			if($mysql->num_rows() > 0) {
				$return['is_admin'] = true;

				while($d = $mysql->fetch_array()) {
					
					$modules = unserialize($d['rights']);
					foreach($modules as $k=>$v) {						
						if(!isset($return['modules'][$k])) {													
							$return['modules'][$k] = true;	
						}
					}
					
				}
			}
			else {
				$return['modules'] = '';
			}
							
		}
		else {
			$return['groups'] = '';
		}
		
#加载国家或地区
		if($return['country_id'] != '0') {
			require_once ROOT_PATH.'/apps/countries/models/countries.class.php';
			$countriesClass = new countries();
			
			$return['country'] = $countriesClass->get($return['country_id']);
		}	

#加载状态
		$mysql->query("
			SELECT *
			FROM `users_status`
			WHERE `user_id` = '".intval($return['user_id'])."'
		");		
		
		if($mysql->num_rows() > 0) {
			while($d = $mysql->fetch_array()) {
				$return['statuses'][$d['status']] = $d;
			}
		}
		
		return $return;
	}
	
#通过用户名判断用户是否存在
	public function isExistUsername($username) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `users`
			WHERE `username` = '".sql_quote($username)."'
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return true;
	}
	
	private function isExistEmail($email, $without='') {
		global $mysql;
		
		$whereQuery = '';
		if($without != '') {
			$whereQuery = " AND `email` <> '".sql_quote($without)."' ";
		}
		
		$mysql->query("
			SELECT *
			FROM `users`
			WHERE `email` = '".sql_quote($email)."' $whereQuery
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return true;
	}
	
	/*
	 * 创建用户
	 */
	public function add() {
		global $mysql, $langArray, $languageURL, $config, $meta;
        
        //昵称
		if(!isset($_POST['nickname']) || trim($_POST['nickname']) == '') {
			$error['nickname'] = $langArray['error_fill_nickname'];
		}elseif(!preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]{2,15}$/u", $_POST['nickname'])) {
			$error['nickname'] = $langArray['error_not_valid_nickname'];
		}	

		
		if(!isset($_POST['email']) || trim($_POST['email']) == '') {
			$error['email'] = $langArray['error_fill_email'];
		}
		elseif(!check_email($_POST['email'])) {
			$error['email'] = $langArray['error_not_valid_email'];
		}
		elseif($this->isExistEmail($_POST['email'])) {
			$error['email'] = $langArray['error_exist_email'];
		}
		
		//if(!isset($_POST['email_confirm']) || trim($_POST['email_confirm']) == '') {
		//	$error['email_confirm'] = $langArray['error_fill_email_confirm'];
		//}
		//if(isset($_POST['email']) && isset($_POST['email_confirm']) && $_POST['email'] !== $_POST['email_confirm']) {
		//	$error['email_confirm'] = $langArray['error_emails_not_match'];
		//}		
		//取消邮箱确认和不符报错

		if(!isset($_POST['username']) || trim($_POST['username']) == '') {
			$error['username'] = $langArray['error_not_set_username'];
		}		
		elseif(!preg_match("/^[A-Za-z0-9_]{4,15}$/u", $_POST['username'])) {
			$error['username'] = $langArray['error_not_valid_username'];
		}
		elseif($this->isExistUsername($_POST['username'])) {
			$error['username'] = $langArray['error_exist_username'];
		}
        //验证码验证
		if(isset($_POST['verify'])) {
			if(empty($_POST['verify'])){
				$error['verify'] = $langArray['error_verify_invalid_empty'];
			}
	        require_once ROOT_PATH.'/classes/Verify.class.php';
		    $verify = new Verify(); 
		    $yz_verify = $verify->check($_POST['verify'], 1);
		    if(!$yz_verify){
		    	$error['verify'] = $langArray['error_invalid_verify'];
		    }

		}else{
			return 'error_verify_invalid_empty';
		}
		if(!isset($_POST['password']) || trim($_POST['password']) == '') {
			$error['password'] = $langArray['error_fill_password'];
		}
		if(!isset($_POST['password_confirm']) || trim($_POST['password_confirm']) == '') {
			$error['password_confirm'] = $langArray['error_fill_password_confirm'];
		}
		elseif(isset($_POST['password']) && isset($_POST['password_confirm']) && $_POST['password'] !== $_POST['password_confirm']) {
			$error['password_confirm'] = $langArray['error_password_not_match'];
		}
		
		if(!isset($_POST['terms'])) {
			$error['terms'] = $langArray['error_not_agree_with_terms'];
		}
		         
		
		if(isset($error)) {
			return $error;
		}
		
		if(!isset($_POST['status'])) {
			$_POST['status'] = 'waiting';
		}
		
		$groups = array();
		if(isset($_POST['groups']) && is_array($_POST['groups'])) {
			foreach($_POST['groups'] as $k=>$v) {
				$groups[$k] = $v;
			}			
		}
		
		$activationKey = md5(rand(0,10000).date('HisdmY').rand(0,10000));
		
		$referalID = 0;
		if(isset($_SESSION['temp']['referal'])) {
			if($this->isExistUsername($_SESSION['temp']['referal'])) {
				//推荐用户存在 返回用户ID
				$referalID = $mysql->fetch_array();
				$referalID = $referalID['user_id'];
			}
			unset($_SESSION['temp']['referal']);
		}
		
		//创建用户
		$mysql->query("
			INSERT INTO `users` (
				`username`,
				`password`,
				`email`,
				`nickname`,
				`register_datetime`,
				`status`,
				`groups`,
				`activate_key`,
				`referal_id`				
			)
			VALUES (
				'".sql_quote($_POST['username'])."',
				'".md5(md5($_POST['password']))."',
				'".sql_quote($_POST['email'])."',
				'".sql_quote($_POST['nickname'])."',
				NOW(),
				'".sql_quote($_POST['status'])."',
				'".serialize($groups)."',
				'".sql_quote($activationKey)."',
				'".intval($referalID)."'
			)
		", __FUNCTION__ );
		
		//用户推荐人数 +1
		if($referalID != 0) {
			$mysql->query("
				UPDATE `users`
				SET `referals` = `referals` + 1
				WHERE `user_id` = '".intval($referalID)."'
				LIMIT 1
			");
		}
		
        #添加邮件订阅
		if(isset($_POST['subscribed'])) {
			require_once ROOT_PATH.'/apps/bulletin/models/bulletin.class.php';
			$bulletinClass = new bulletin();
			
			$bulletinClass->addBulletinEmail();
		}

        //判断有无客服功能
            $have_service=false;
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/apps/service/controllers/index.php')) {
            //判断启动状态
            require_once ROOT_PATH.'/apps/app_extends/models/app_extends.class.php';
            $app_extends=new app_extends();
            $is_open=$app_extends->getStatus("客户服务管理");
            if($is_open){
                $have_service= true;
            }
        }
        $user_info = $this->get_user_info_by_pn($_POST['username'],$_POST['password']);
        if(!$have_service){


            #发送激活链接
            require_once ENGINE_PATH.'/classes/email.class.php';
            $emailClass = new email();
            $emailClass->fromEmail = 'no-reply@'.$config['domain'];
            $emailClass->subject = '['.$config['domain'].'] '.$langArray['email_activate_subject'];
            $emailClass->message = langMessageReplace($langArray['email_activate_text'], array(
                'DOMAIN' => $config['domain'],
                'LINK' => 'http://'.$config['domain'].'/'.$languageURL.'sign_in/?command=activate&user='.$_POST['username'].'&key='.$activationKey
            ));
            $emailClass->to($_POST['email']);
            $emailClass->send();
            unset($emailClass);


        }
        else{

            #SEND ACTIVATION LINK
            require_once ENGINE_PATH.'/classes/email.class.php';

            $mail = new email();

            //获取本次收通知邮件客服邮箱
            $where = "WHERE `status`='true' AND `service_status`=1";
            $order = "time ASC";
            $limitQuery = "LIMIT 1";
            $mysql->query("
				SELECT SQL_CALC_FOUND_ROWS *
				FROM `service`
				$where
				ORDER BY $order
				$limitQuery
			");

            if($mysql->num_rows() == 0) {
                $mysql->query("
				UPDATE `service`
				SET `service_status` = '".intval(1)."'
			", __FUNCTION__ );
                $mysql->query("
				SELECT SQL_CALC_FOUND_ROWS *
				FROM `service`
				$where
				ORDER BY $order
				$limitQuery
			");
            }
            $theservice = $mysql->fetch_array();

            
            $sendtext = '用户名：'.$_POST['username'].'<br />昵称：'.$_POST['nickname'].'<br />邮箱：'.$_POST['email'].'<br />手机号：'.$_POST['username'];
            $mail->to($theservice['email']);
            $mail->fromEmail = 'no-reply@'.$config['domain'];
            $mail->contentType = 'text/html';
            $mail->subject = $langArray['email_new_add_user'].' '.'用户ID：'.$user_info['user_id'];
            $mail->message = $sendtext;
            $mail->send();
            unset($mail);

            //分配用户给客服
            $mysql->query("
			INSERT INTO `service_relation` (
				`user_id`,
				`service_user_id`
			)
			VALUES (
				'".intval($user_info['user_id'])."',
				'".intval($theservice['id'])."'
			)
		", __FUNCTION__);
            //刷新用户服务数量
            $mysql->query("
				UPDATE `service`
				SET `service_num` = `service_num` + 1,
				`service_status` = '".intval(0)."'
				WHERE `id` = '".intval($theservice['id'])."'
				LIMIT 1
		");

            $emailClass = new email();
            $link = 'http://'.$config['domain'].'/'.$languageURL.'sign_in/?command=activate&user='.$_POST['username'].'&key='.$activationKey;
            $link = '<a href="'.$link.'" target="_blank">'.$link.'</a>';
            $emailClass->contentType = 'text/html';
            $emailClass->fromEmail = 'no-reply@'.$config['domain'];
            $emailClass->subject = '['.$meta['meta_title'].']发给['.$_POST['nickname'].']的注册激活邮件';
            $emailClass->message = 'Hi！['.$_POST['nickname'].']：<br />
								&nbsp;&nbsp;&nbsp;&nbsp;幸亏没放弃，终于等到你！想给你写信已经很久了！<br />
								&nbsp;&nbsp;&nbsp;&nbsp;这是你的登录信息，激活前处于挂起状态：<br />
								&nbsp;&nbsp;&nbsp;&nbsp;帐号：['.$_POST['username'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;密码：*********<br />
								&nbsp;&nbsp;&nbsp;&nbsp;这是你的激活连接，小编在等候你的回应：<br />
								&nbsp;&nbsp;&nbsp;&nbsp;'.$link.'<br />
								&nbsp;&nbsp;&nbsp;&nbsp;速速点击上面的激活链接来启用它们！<br />
								&nbsp;&nbsp;&nbsp;&nbsp;小编已经等不及啦！<br />
                                <br />
								&nbsp;&nbsp;&nbsp;&nbsp;专属小编：['.$theservice['user_name'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.$meta['meta_title'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.date('Y-m-d H:i:s',time()).']<br />';

            $emailClass->to($_POST['email']);
            $emailClass->send();
            unset($emailClass);
            
            

        }
        $res_mail = 'http://'.$this->gotomail($user_info['email']);
        $_SESSION["THE_USER_RES_SEND_MAIL_4_M_MAIL"] = $res_mail;
        $_SESSION["THE_USER_RES_SEND_MAIL_4_M"] = $user_info['user_id'];
        return true;

	}
	
	/*
	 * 编辑用户
	 */
	public function edit($id, $editFromAdmin=true) {
		global $mysql, $config, $langArray;

		$setQuery = "";
		
		if(isset($_POST['status'])) {
			$setQuery .= " `status` = '".sql_quote($_POST['status'])."' ";
		}

		if($editFromAdmin) {
            if($setQuery != '') {
                $setQuery .= ',';
            }

            if(isset($_POST['featured_author'])) {
                $setQuery .= "  `featured_author` = 'true'  ";
            }
            else {
                $setQuery .= "  `featured_author` = 'false'  ";
            }

            $groups = array();
            if(isset($_POST['groups']) && is_array($_POST['groups']) && !empty($_POST['groups'])) {
                foreach($_POST['groups'] as $k=>$v) {
                    $groups[$k] = $v;
                }
                $setQuery .= " , `groups` = '".serialize($groups)."' ";
            }

            if(isset($_POST['power_elite_author'])) {
                if(!empty($_POST['power_elite_author'])) {
                    $setQuery .= " , `power_elite_author` = 'true' ";
                }
                else {
                    $setQuery .= " , `power_elite_author` = 'false' ";
                }
            }


			if(isset($_POST['elite_author'])) {
				$setQuery .= " , `elite_author` = 'true' ";
			}
			else {
				$setQuery .= " , `elite_author` = 'false' ";
			}
			if(isset($_POST['badges'])) {
				$setQuery .= " , `badges` = '" . implode(',', $_POST['badges']) . "' ";
			} else {
				$setQuery .= " , `badges` = '' ";
			}
			
			if(isset($_POST['password']) && trim($_POST['password']) != '') {
				$setQuery .= " , `password` = '".md5(md5($_POST['password']))."' ";
			}
			
			if(isset($_POST['commission_percent'])) {
				$setQuery .= " , `commission_percent` = '".(int)$_POST['commission_percent']."' ";
			}
		}
				
		if($setQuery != '') {
			$mysql->query("
				UPDATE `users`
				SET $setQuery
				WHERE `user_id` = '".intval($id)."'
				LIMIT 1
			", __FUNCTION__ );
		}
		
		return true;
	}
	
	/*
	 * 删除用户
	 */
	public function delete($id) {
		global $mysql;

		recursive_rmdir(DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $id.'/', true);
		
		$mysql->query("
			DELETE FROM `users`
			WHERE `user_id` = '".intval($id)."'
			LIMIT 1
		", __FUNCTION__ );
		
		return true;
	}
	
	private function deleteAvatar($id) {
		global $mysql, $config;
		
		$user = $this->get($id);
		if(!is_array($user)) {
			return false;
		}
		deleteFile ( DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $id.'/' . $user ['avatar'] );
		if(is_array($config['avatar_photo_sizes'])) {
			foreach ( $config['avatar_photo_sizes'] as $k => $v ) {
				deleteFile ( DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $id.'/' . $k . '_' . $user ['avatar'] );
			}
		}
		
		$mysql->query("
			UPDATE `users`
			SET `avatar` = NULL
			WHERE `user_id` = '".intval($id)."'
			LIMIT 1
		", __FUNCTION__ );
		
		return true;
	}
	
	private function deleteHomeimage($id) {
		global $mysql, $config;
		
		$user = $this->get($id);
		if(!is_array($user)) {
			return false;
		}
		deleteFile ( DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $id.'/' . $user ['homeimage'] );
		if(is_array($config['homeimage_photo_sizes'])) {
			foreach ( $config['homeimage_photo_sizes'] as $k => $v ) {
				deleteFile ( DATA_SERVER_PATH . "/uploads/" . $this->uploadFileDirectory . $id.'/' . $k . '_' . $user ['homeimage'] );
			}
		}
		
		$mysql->query("
			UPDATE `users`
			SET `homeimage` = NULL
			WHERE `user_id` = '".intval($id)."'
			LIMIT 1
		", __FUNCTION__ );
		
		return true;
	}
	
	
	/*
	 * 登录
	 */
    public function login($admin=false) {
        global $mysql, $config;

        if(!isset($_POST['username']) || !isset($_POST['password'])) {
            return 'error_invalid_username_or_password';
        }
        //验证码验证
        if(isset($_POST['verify'])) {
            if(empty($_POST['verify'])){
                return 'error_verify_invalid_empty';
            }
            require_once ROOT_PATH.'/classes/Verify.class.php';
            $verify = new Verify();
            $yz_verify = $verify->check($_POST['verify'], 1);
            if(!$yz_verify){
                return 'error_invalid_verify';
            }

        }else{
            return 'error_verify_invalid_empty';
        }
        $mysql->query("
			SELECT *
			FROM `users`
			WHERE `username` = '".sql_quote($_POST['username'])."' AND `password` = '".md5(md5($_POST['password']))."'
		", __FUNCTION__ );

        if($mysql->num_rows() == 0) {
            return 'error_invalid_username_or_password';
        }

        $row = $mysql->fetch_array();

        if($row['status'] != 'activate'){
            return 'error_invalid_activation_no';
        }
        $user = $this->get($row['user_id']);

        if($user['last_login_datetime'] == '' || $user['last_login_datetime'] == '0000-00-00 00:00:00') {
            $user['first_login'] = 'yes';
        }

        if($admin && ($user['groups'] == false || count($user['groups']) < 1)) {
            return 'error_invalid_username_or_password';
        }


        $verKey = '';
        if(isset($_POST['rememberme'])) {

            $verKey = md5(rand(0,9999999).time().$user['user_id']);

            setcookie("user_id", $user['user_id'], time()+2592000, "/", ".".$config['domain']);
            setcookie("verifyKey", $verKey, time()+2592000, "/", ".".$config['domain']);
        }

        $mysql->query("
			UPDATE `users`
			SET `last_login_datetime` = NOW(),
					`ip_address` = '".sql_quote($_SERVER['REMOTE_ADDR'])."', 
					`remember_key` = '".sql_quote($verKey)."'
			WHERE `user_id` = '".intval($user['user_id'])."'
			LIMIT 1
		", __FUNCTION__ );

        $_SESSION['user'] = $user;

        return true;
    }
	
	
	public function isValidVerifyKey($user_id, $key) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `users`
			WHERE `user_id` = '".intval($user_id)."' AND `remember_key` = '".sql_quote($key)."'
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return true;
	}
	
	public function isValidActivateKey($username, $key) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `users`
			WHERE `username` = '".sql_quote($username)."' AND `activate_key` = '".sql_quote($key)."'
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return true;
	}
	
	public function activateUser($username, $key) {
		global $mysql, $langArray;
		
		if(!$this->isValidActivateKey($username, $key)) {
			$error['valid'] = $langArray['error_not_valid_activate_key'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		$mysql->query("
			UPDATE `users`
			SET `status` = 'activate',
					`activate_key` = NULL
			WHERE `username` = '".sql_quote($username)."' AND `activate_key` = '".sql_quote($key)."'
			LIMIT 1
		");
		
		$_SESSION['user'] = $this->getByUsername($username);
		
		return true;
	}
	
	/*
	 * 更改密码
	 */
	public function changePassword() {
		global $mysql, $langArray, $config,$meta;
		
		$mysql->query("
			SELECT *
			FROM `users`
			WHERE `username` = '".sql_quote($_POST['username'])."' AND `email` = '".sql_quote($_POST['email'])."'
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return 'error_invalid_username_email';
		}
		
		$d = $mysql->fetch_array();
		
	//生成密码
		$alphabet = array (
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'M', 'N', 'P', 'R', 'S', 'T', 'W', 'X', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '2', '3', '4', '5', '6', '7', '8', '9' 
		);
		
		$code = '';
		for($i = 0; $i < 7; $i ++) {
			$random_number = rand ( 0, count ( $alphabet ) - 1 );
			$code .= $alphabet [$random_number];
		}
		
		$mysql->query("
			UPDATE `users`
			SET `password` = '".md5(md5($code))."'
			WHERE `user_id` = '".intval($d['user_id'])."'
			LIMIT 1
		", __FUNCTION__ );


        //判断有无客服管理模块
        require_once ROOT_PATH.'/apps/app_extends/models/app_extends.class.php';

        $app_extends=new app_extends();

        if($app_extends->is_service()){
            //通过用户id获取关联客服
            require_once ROOT_PATH.'/apps/service/models/service.class.php';
            $service = new service();
            $theservice = $service->getserviceByuserid($d['user_id']);

            require_once ENGINE_PATH.'classes/email.class.php';
            $emailClass = new email();
            $emailClass->contentType = 'text/html';
            $emailClass->fromEmail = 'no-reply@'.$config['domain'];
            $emailClass->subject = '['.$meta['meta_title'].']发给['.$d['username'].']的找回密码邮件';
            $emailClass->message = '['.$d['username'].']!<br />
		                        &nbsp;&nbsp;&nbsp;&nbsp;什么？你竟然忘记密码？怪不得见不到你，知道小编等你等得多辛苦吗？你！真！残！忍！<br />
								&nbsp;&nbsp;&nbsp;&nbsp;这是你的新密码信息：<br />
								&nbsp;&nbsp;&nbsp;&nbsp;帐号：['.$d['username'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;密码：['.$code.']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;不许再忘哦！ <br />
                                <br />
								&nbsp;&nbsp;&nbsp;&nbsp;专属小编：['.$theservice['user_name'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.$meta['meta_title'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.date('Y-m-d H:i:s',time()).']<br />';
            $emailClass->to($d['email']);
            $emailClass->send();
            unset($emailClass);
            return true;
        }
		require_once ENGINE_PATH.'classes/email.class.php';
		$emailClass = new email();

		$emailClass->fromEmail = 'no-reply@'.$config['domain'];
		$emailClass->subject = '['.$config['domain'].'] '.$langArray['email_reset_password'];
		$emailClass->message = langMessageReplace($langArray['email_reset_password_text'], array(
        'DOMAIN' => $config['domain'],
        'USERNAME' => $d['username'],
        'PASSWORD' => $code
		));
		$emailClass->to($d['email']);
		
		$emailClass->send();
		unset($emailClass);
    return true;
	}

	/*
	 * 忘记用户名
	 */
	public function lostUsername() {
		global $mysql, $langArray, $config,$meta;
		
		$mysql->query("
			SELECT *
			FROM `users`
			WHERE `email` = '".sql_quote($_POST['email'])."'
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return 'error_invalid_user_email';
		}
		
		$d = $mysql->fetch_array();
        //判断有无客服管理模块
        require_once ROOT_PATH.'/apps/app_extends/models/app_extends.class.php';

        $app_extends=new app_extends();

        if($app_extends->is_service()){
            //通过用户id获取关联客服
            require_once ROOT_PATH.'/apps/service/models/service.class.php';
            $service = new service();
            $theservice = $service->getserviceByuserid($d['user_id']);

            require_once ENGINE_PATH.'classes/email.class.php';
            $emailClass = new email();

            $emailClass->fromEmail = 'no-reply@'.$config['domain'];
            $emailClass->contentType = 'text/html';
            $emailClass->subject = '['.$meta['meta_title'].']发给['.$d['username'].']的找回用户名邮件';
            $emailClass->message ='['.$d['username'].']!<br />
							    &nbsp;&nbsp;&nbsp;&nbsp;什么？你连用户名都能忘？你是不是打算把小编也忘了，太残忍了！ <br />
								&nbsp;&nbsp;&nbsp;&nbsp;算了，不跟你计较，这是你的用户名：<br />
								&nbsp;&nbsp;&nbsp;&nbsp;帐号：['.$d['username'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;不许再忘哦！<br />
                                <br />
								&nbsp;&nbsp;&nbsp;&nbsp;专属小编：['.$theservice['user_name'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.$meta['meta_title'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.date('Y-m-d H:i:s',time()).']<br />';

            $emailClass->to($d['email']);

            $emailClass->send();
            unset($emailClass);
            return true;
        }

		
		require_once ENGINE_PATH.'classes/email.class.php';
		$emailClass = new email();

		$emailClass->fromEmail = 'no-reply@'.$config['domain'];
		$emailClass->subject = '['.$config['domain'].'] '.$langArray['email_lost_username'];
		$emailClass->message = langMessageReplace($langArray['email_lost_username_text'], array(
																'DOMAIN' => $config['domain'],
																'USERNAME' => $d['username']
														));
		$emailClass->to($d['email']);
		
		$emailClass->send();
		unset($emailClass);
    return true;
	}
	
/* 
 * 编辑函数
 */
	public function editNewPassword() {
		global $mysql, $langArray;
		
		if(!isset($_POST['password']) || trim($_POST['password']) == '') {
			$error['password'] = $langArray['error_fill_password'];
		}
		else {
			$mysql->query("
				SELECT *
				FROM `users`
				WHERE `user_id` = '".intval($_SESSION['user']['user_id'])."' AND `password` = '".md5(md5($_POST['password']))."'
			");
			
			if($mysql->num_rows() == 0) {
				$error['password'] = $langArray['error_wrong_old_password'];
			}
		}
		
		if(!isset($_POST['new_password']) || trim($_POST['new_password']) == '') {
			$error['new_password'] = $langArray['error_fill_password'];
		}
		if(!isset($_POST['new_password_confirm']) || trim($_POST['new_password_confirm']) == '') {
			$error['new_password_confirm'] = $langArray['error_fill_password_confirm'];
		}
		elseif(isset($_POST['new_password']) && isset($_POST['new_password_confirm']) && $_POST['new_password'] !== $_POST['new_password_confirm']) {
			$error['new_password_confirm'] = $langArray['error_password_not_match'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		$mysql->query("
			UPDATE `users`
			SET `password` = '".md5(md5($_POST['new_password']))."'
			WHERE `user_id` = '".intval($_SESSION['user']['user_id'])."'
			LIMIT 1
		");
		
		return true;
	}	
	
	public function editFeatureItem() {
		global $mysql, $items;
		
		if(!isset($_POST['featured_item_id'])) {
			$_POST['featured_item_id'] = 0;
		}
		
		$_POST['featured_item_id'] = intval($_POST['featured_item_id']);
		
		if($_POST['featured_item_id'] != 0 && !array_key_exists($_POST['featured_item_id'], $items)) {
			$_POST['featured_item_id'] = 0;
		}
		
		$mysql->query("
			UPDATE `users`
			SET `featured_item_id` = '".intval($_POST['featured_item_id'])."'
			WHERE `user_id` = '".intval($_SESSION['user']['user_id'])."'
			LIMIT 1
		");
		
		$_SESSION['user']['featured_item_id'] = $_POST['featured_item_id'];
		
		return true;
	}
	
	public function editExclusiveAuthor($type='true',$user_id = 0) {
		global $mysql;
		
		$mysql->query("
			UPDATE `users`
			SET `exclusive_author` = '".sql_quote($type)."'
			WHERE `user_id` = '".intval($user_id)."'
			LIMIT 1
		");
		
		//$_SESSION['user']['exclusive_author'] = $type;
		
		return true;
	}
	
	public function editSaveLicense() {
		global $mysql, $langArray;
		
		if(!isset($_POST['license']) || !is_array($_POST['license'])) {
			$error['license'] = $langArray['error_choose_license'];
		}
        $license=serialize($_POST["license"]);
        if($license !='a:2:{s:8:"extended";s:8:"extended";s:8:"personal";s:8:"personal";}' && $license != 'a:1:{s:8:"personal";s:8:"personal";}' && $license !='a:1:{s:8:"extended";s:8:"extended";}')
            $error['license'] = $langArray['error_choose_license'];
        if(isset($error)) {
			return $error;
		}
        $mysql->query("
			UPDATE `users`
			SET `license` = '".$license."'
			WHERE `user_id` = '".intval($_SESSION['user']['user_id'])."'
			LIMIT 1
		");
		$_SESSION['user']['license'] = $_POST['license'];
		return true;
	}
	public function editChangeAvatarImage() {
		global $mysql, $langArray, $config;
		
		$this->photoSizes = $config['avatar_photo_sizes'];
		$avatar = $this->upload('profile_image', $_SESSION['user']['user_id'].'/', false, true);
		if(substr($avatar, 0, 6) == 'error_') {
			$this->avatarError = $langArray[$avatar];
		}
		
		$this->photoSizes = $config['homeimage_photo_sizes'];
		$homeimage = $this->upload('homepage_image', $_SESSION['user']['user_id'].'/', false, true);
		if(substr($homeimage, 0, 6) == 'error_') {
			$this->homeimageError = $langArray[$homeimage];
		}
		
		$setQuery = '';
		if($avatar != '' && substr($avatar, 0, 6) != 'error_') {
			$this->deleteAvatar($_SESSION['user']['user_id']);
			$setQuery .= " `avatar` = '".sql_quote($avatar)."' ";
			$_SESSION['user']['avatar'] = $avatar;
		}
		if($homeimage != '' && substr($homeimage, 0, 6) != 'error_') {
			$this->deleteHomeimage($_SESSION['user']['user_id']);
			if($setQuery != '') {
				$setQuery .= ',';
			}
			$setQuery .= " `homeimage` = '".sql_quote($homeimage)."' ";
			$_SESSION['user']['homeimage'] = $homeimage;
		}
		
		if($setQuery != '') {			
			$mysql->query("
				UPDATE `users`
				SET $setQuery
				WHERE `user_id` = '".intval($_SESSION['user']['user_id'])."'
				LIMIT 1
			");
		}
		
		return true;
	}
	
	public function editPersonalInformation() {
		global $mysql, $langArray;
		
		if(!isset($_POST['nickname']) || trim($_POST['nickname']) == '') {
			$error['nickname'] = $langArray['error_fill_nickname'];
		}		
		
		if(!isset($_POST['email']) || trim($_POST['email']) == '') {
			$error['email'] = $langArray['error_fill_email'];
		}
		elseif(!check_email($_POST['email'])) {
			$error['email'] = $langArray['error_not_valid_email'];
		}
		elseif($this->isExistEmail($_POST['email'], $_SESSION['user']['email'])) {
			$error['email'] = $langArray['error_exist_email'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		if(!isset($_POST['firmname'])) {
			$_POST['firmname'] = '';
		}

		if(!isset($_POST['profile_title'])) {
			$_POST['profile_title'] = '';
		}
		
		if(!isset($_POST['profile_desc'])) {
			$_POST['profile_desc'] = '';
		}
		
		if(!isset($_POST['live_city'])) {
			$_POST['live_city'] = '';
		}
		
		if(!isset($_POST['country_id']) || trim($_POST['country_id']) == '') {
			$_POST['country_id'] = '0';
		}
		
		if(!isset($_POST['custom_made'])) {
			$_POST['custom_made'] = 'false';
		}
        if(!isset($_POST['address'])) {
            $_POST['address'] = '';
        }
		
		$mysql->query("
			UPDATE `users`
			SET     `nickname` = '".sql_quote($_POST['nickname'])."',
					`email` = '".sql_quote($_POST['email'])."',
					`firmname` = '".sql_quote($_POST['firmname'])."',
					`profile_title` = '".sql_quote($_POST['profile_title'])."',
					`profile_desc` = '".sql_quote($_POST['profile_desc'])."',
					`live_city` = '".sql_quote($_POST['live_city'])."',
					`address` = '".sql_quote($_POST['address'])."',
					`country_id` = '".intval($_POST['country_id'])."',
					`custom_made` = '".sql_quote($_POST['custom_made'])."'
			WHERE `user_id` = '".intval($_SESSION['user']['user_id'])."'
			LIMIT 1
		");
		
		$_SESSION['user']['nickname'] = $_POST['nickname'];
		$_SESSION['user']['email'] = $_POST['email'];
		$_SESSION['user']['firmname'] = $_POST['firmname'];
		$_SESSION['user']['profile_title'] = $_POST['profile_title'];
		$_SESSION['user']['profile_desc'] = $_POST['profile_desc'];
		$_SESSION['user']['live_city'] = $_POST['live_city'];
        $_SESSION['user']['address'] = $_POST['address'];
        $_SESSION['user']['country_id'] = $_POST['country_id'];
		$_SESSION['user']['custom_made'] = $_POST['custom_made'];
		
		return true;
		
	}
	
	

	public function editSocialInformation() {
		global $mysql, $langArray;

		if(isset($error)) {
			return $error;
		}
		
		if(!isset($_POST['weibo'])) {
			$_POST['weibo'] = '';
		}
		
		if(!isset($_POST['tencent'])) {
			$_POST['tencent'] = '';
		}
		
		if(!isset($_POST['baidu'])) {
			$_POST['baidu'] = '';
		}
		
		if(!isset($_POST['netease'])) {
			$_POST['netease'] = '';
		}
		
		if(!isset($_POST['sohu'])) {
			$_POST['sohu'] = '';
		}
		
		if(!isset($_POST['renren'])) {
			$_POST['renren'] = '';
		}
						
		$mysql->query("
			UPDATE `users`
			SET `weibo` = '".sql_quote($_POST['weibo'])."',
					`tencent` = '".sql_quote($_POST['tencent'])."',
					`baidu` = '".sql_quote($_POST['baidu'])."',
					`netease` = '".sql_quote($_POST['netease'])."',
					`sohu` = '".sql_quote($_POST['sohu'])."',
					`renren` = '".sql_quote($_POST['renren'])."'
			WHERE `user_id` = '".intval($_SESSION['user']['user_id'])."'
			LIMIT 1
		");
		
		$_SESSION['user']['weibo'] = $_POST['weibo'];
		$_SESSION['user']['tencent'] = $_POST['tencent'];
		$_SESSION['user']['baidu'] = $_POST['baidu'];
		$_SESSION['user']['netease'] = $_POST['netease'];
		$_SESSION['user']['sohu'] = $_POST['sohu'];
		$_SESSION['user']['renren'] = $_POST['renren'];


		return true;
		
	}

	
	
	public function sendEmail() {
		global $mysql, $langArray, $user, $config;
		
		if(!isset($_POST['message']) || trim($_POST['message']) == '') {
			return $langArray['error_not_set_message'];
		}
		
		$mysql->query("
			INSERT INTO `users_emails` (
				`from_id`,
				`from_email`,
				`to_id`,
				`message`,
				`datetime`
			)
			VALUES (
				'".intval($_SESSION['user']['user_id'])."',
				'".sql_quote($_SESSION['user']['email'])."',
				'".intval($user['user_id'])."',
				'".sql_quote($_POST['message'])."',
				NOW()
			)
		");
		
#发送邮件		
		require_once ENGINE_PATH.'/classes/email.class.php';
		$emailClass = new email();
		
		$emailClass->fromEmail = 'no-reply@'.$config['domain'];
		$emailClass->subject = '['.$config['domain'].'] '.$langArray['email_profile_subject'];
		$emailClass->message = langMessageReplace($langArray['email_profile_text'], array(
																'USERNAME' => $_SESSION['user']['username'],
																'EMAIL' => $_SESSION['user']['email'],
																'MESSAGE' => $_POST['message']
														));
		$emailClass->to($user['email']);

		$emailClass->send();
		unset($emailClass);
		return true;
	}
	
/* 
 * 关注
 */	
	public function isFollow($id) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `users_followers`
			WHERE `user_id` = '".intval($_SESSION['user']['user_id'])."' AND `follow_id` = '".intval($id)."'
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return true;
	}
	
	public function addFollow($id) {
		global $mysql;
		
		$mysql->query("
			INSERT INTO `users_followers` (
				`user_id`,
				`follow_id`
			)
			VALUES (
				'".intval($_SESSION['user']['user_id'])."',
				'".intval($id)."'
			)
		");
		
		return true;
	}
	
	public function deleteFollow($id) {
		global $mysql;
		
		$mysql->query("
			DELETE FROM `users_followers`
			WHERE `user_id` = '".intval($_SESSION['user']['user_id'])."' AND `follow_id` = '".intval($id)."'
			LIMIT 1
		");
		
		return true;
	}
	
	public function followUser($id) {
		if($this->isFollow($id)) {
			$this->deleteFollow($id);
		}
		else {
			$this->addFollow($id);
		}
		
		return true;
	}
	
	public function getFollowers($userID, $start=0, $limit=0, $order='`user_id` ASC', $following=false) {
		global $mysql;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		if($following) {
			$whereQuery = " `follow_id` = '".intval($userID)."' ";
		}
		else {
			$whereQuery = " `user_id` = '".intval($userID)."' ";
		}
		
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `users_followers`
			WHERE $whereQuery
			ORDER BY $order
			$limitQuery
		");
			
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$whereQuery = '';
		while($d = $mysql->fetch_array()) {
			if($whereQuery != '') {
				$whereQuery .= " OR ";
			}
			
			if($following) {
				$whereQuery .= " `user_id` = '".intval($d['user_id'])."' ";
			}
			else {
				$whereQuery .= " `user_id` = '".intval($d['follow_id'])."' ";
			}
		}
		
		$this->foundRows = $mysql->getFoundRows();
		
		return $this->getAll(0, 0, $whereQuery);
	}
	
	public function getFollowersID($userID, $start=0, $limit=0, $order='`user_id` ASC', $following=false) {
		global $mysql;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		if($following) {
			$whereQuery = " `follow_id` = '".intval($userID)."' ";
		}
		else {
			$whereQuery = " `user_id` = '".intval($userID)."' ";
		}
		
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `users_followers`
			WHERE $whereQuery
			ORDER BY $order
			$limitQuery
		");
			
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		while($d = $mysql->fetch_array()) {
			$return[] = $d;
		}
		
		return $return;
	}
	
	public function updateQuiz($id, $type) {
		global $mysql;
		
		$mysql->query("
			UPDATE `users`
			SET `quiz` = '".sql_quote($type)."'
			WHERE `user_id` = '".intval($id)."'
			LIMIT 1
		");
		
		return true;
	}
	
	
	public function getUsersCount($whereQuery='') {
		global $mysql;
		
		if($whereQuery != '') {
			$whereQuery = " WHERE ".$whereQuery;
		}
		
		$mysql->query("
			SELECT *
			FROM `users`
			$whereQuery
		");
			
		return $mysql->num_rows();
	}
	
	public function getStatistic($id) {
		global $mysql;

		$return = array();
		
#充值		
		$mysql->query("
			SELECT SUM(`deposit`) as sum
			FROM `deposit`
			WHERE `user_id` = '".intval($id)."' AND `paid` = 'true'
			GROUP BY `user_id`
		");
		
		if($mysql->num_rows() == 0) {
			$return['deposit'] = 0;
		}
		else {
			$buff = $mysql->fetch_array();
			$return['deposit'] = $buff['sum'];
		}
		
#已购买的作品		
		$mysql->query("
			SELECT o.*, i.`name` AS item_name
			FROM `orders` AS o
			JOIN `items` AS i
			ON i.`id` = o.`item_id`
			WHERE o.`user_id` = '".intval($id)."' AND o.`paid` = 'true'			
		");
		
		if($mysql->num_rows() > 0) {
			$return['total'] = 0;
			while($d = $mysql->fetch_array()) {
				$return['items'][] = $d;
				$return['total'] += $d['price'];
			}						
		}
		
		
		return $return;
	}
	
	//获取推荐人在通过该用户购买作品获得到的分成次数
	public function getTotalReferals($id, $referal_id) {
		global $mysql;
		
		$mysql->query("
			SELECT COUNT(`id`) as sum
			FROM `users_referals_count`
			WHERE `user_id` = '".intval($id)."' AND `referal_id` = '".intval($referal_id)."'
			GROUP BY `referal_id`
			LIMIT 1
		");
		
		$buff = $mysql->fetch_array();
		if($buff) {
			return $buff['sum'];
		}
		
		return 0;
	}
	
}
?>