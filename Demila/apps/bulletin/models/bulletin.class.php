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


class bulletin extends base {
	
	/*
	 * 获取函数
	 */
	public function getAll($start=0, $limit=0, $where='') {
		global $mysql, $language, $langArray;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `bulletin`
			WHERE 1=1 $where
			ORDER BY `datetime` DESC
			$limitQuery
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		while($d = $mysql->fetch_array()) {
			$return[] = $d;
		}
		
		$this->foundRows = $mysql->getFoundRows();
		
		return $return;
	}
	
	public function getAllEmails($start=0, $limit=0) {
		global $mysql, $language, $langArray;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `bulletin_emails`
			ORDER BY `email` ASC
			$limitQuery
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		while($d = $mysql->fetch_array()) {
			$return[] = $d;
		}
		
		$this->foundRows = $mysql->getFoundRows();
		
		return $return;
	}
	
	public function get($id) {
		global $mysql, $language;
		
		$mysql->query("
			SELECT *
			FROM `bulletin`
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return $mysql->fetch_array();
	}		
	
	/*
	 * 编辑
	 */
	public function add() {
		global $mysql, $langArray, $config;
		
		if(!isset($_POST['name']) || trim($_POST['name']) == '') {
			$error['name'] = $langArray['error_fill_this_field'];
		}
		
		if(!isset($_POST['text']) || trim($_POST['text']) == '') {
			$error['text'] = $langArray['error_fill_this_field'];
		}
		
		if(!isset($_POST['send_to']) || trim($_POST['send_to']) == '') {
			$error['send_to'] = $langArray['error_fill_this_field'];
		}
		
		if(isset($_POST['send_to']) && $_POST['send_to'] == 'city' && (!isset($_POST['city_id']) || !is_numeric($_POST['city_id']))) {
			$error['city'] = $langArray['error_fill_this_field'];
		}
		
		if(isset($_POST['send_to']) && $_POST['send_to'] == 'group' && (!isset($_POST['bgroup_id']) || !is_numeric($_POST['bgroup_id']))) {
			$error['group'] = $langArray['error_fill_this_field'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		$sendID = 0;
		if($_POST['send_to'] == 'city') {
			$sendID = $_POST['city_id'];
		}
		elseif($_POST['send_to'] == 'group') {
			$sendID = $_POST['bgroup_id'];
		}
		
		$mysql->query("
			INSERT INTO `bulletin` (
				`name`,
				`text`,
				`datetime`,
				`send_to`,
				`send_id`
			)
			VALUES (
				'".sql_quote($_POST['name'])."',
				'".sql_quote($_POST['text'])."',
				NOW(),
				'".sql_quote($_POST['send_to'])."',
				'".intval($sendID)."'
			)
		", __FUNCTION__ );
		
		$bulletinID = $mysql->insert_id();
		
#加载订阅列表			
		if($_POST['send_to'] == 'city') {
			$mysql->query("
				SELECT *
				FROM `users`
				WHERE `city_id` = '".intval($_POST['city_id'])."' AND `bulletin_subscribe` = 'true'
			");
			
			if($mysql->num_rows() > 0) {
				while($d = $mysql->fetch_array()) {
					$emails[] = $d['email'];
				}
			}
		}
								
#加载分组			
		if($_POST['send_to'] == 'group') {
			$mysql->query("
				SELECT u.*
				FROM `bulletin_users` AS bg				
				JOIN `users` AS u
				ON u.`user_id` = bg.`user_id` AND u.`bulletin_subscribe` = 'true'
				WHERE bg.`bulletingroup_id` = '".intval($_POST['bgroup_id'])."'
			");
			
			if($mysql->num_rows() > 0) {
				while($d = $mysql->fetch_array()) {
					$emails[] = $d['email'];
				}
			}
		}
								
#加载全部			
		if($_POST['send_to'] == 'active') {
			$mysql->query("
				SELECT *
				FROM `users`
				WHERE `bulletin_subscribe` = 'true'
			");
			
			if($mysql->num_rows() > 0) {
				while($d = $mysql->fetch_array()) {
					$emails[] = $d['email'];
				}
			}
		}
		
#加载全部			
		if($_POST['send_to'] == 'admins') {
			$mysql->query("
				SELECT *
				FROM `admins`
				WHERE `bulletin_subscribe` = 'true'
			");
			
			if($mysql->num_rows() > 0) {
				while($d = $mysql->fetch_array()) {
					$emails[] = $d['email'];
				}
			}
		}
		
#加载全部			
		if($_POST['send_to'] == 'site') {
			$mysql->query("
				SELECT *
				FROM `bulletin_emails`
				WHERE `bulletin_subscribe` = 'true'
			");
			
			if($mysql->num_rows() > 0) {
				while($d = $mysql->fetch_array()) {
					$emails[] = $d['email'];
				}
			}
		}
		
#获取模板
		$mysql->query("
			SELECT *
			FROM `bulletin_template`
			ORDER BY `id` DESC
			LIMIT 1
		");		
		
		if($mysql->num_rows() > 0) {
			$template = $mysql->fetch_array();
			$template = $template['template'];
		}
		else {
			$template = '{$CONTENT}';
		}
		
		if(isset($emails)) {
			
			require_once $config['system_core'].'classes/email.class.php';
					
			foreach($emails as $email) {
				$mail = new email();
				
				$mail->fromEmail = 'no-reply@'.$config['domain'];
		    $mail->to($email);
		    $mail->subject = '['.$config['domain'].'] '.$_POST['name'];
		
				$mail->contentType = 'text/html';
		    
				$mail->message = langMessageReplace($template, array(
		    	'DOMAIN' => $config['domain'],
		    	'BULLETINID' => $bulletinID,
		    	'EMAIL' => $email,
		    	'CONTENT' => $_POST['text']
		    ));
            require_once ROOT_PATH.'/apps/system/models/system.class.php';
            $system = new system();
            $smtp = $system ->is_smtp();
            $smtpconf=$system->getAllKeyValue();
            if($smtp){
                $mail->email_sock($smtpconf["smtp_host"],$smtpconf["smtp_port"],0,'error',10,1,$smtpconf["smtp_user"],$smtpconf["smtp_pass"],$smtpconf["smtp_from"]);
                $mail->send_mail_sock($mail->subject,$mail->message,$email,$smtpconf["smtp_from_name"]) ;
                unset($emailClass);
            }else {
                $mail->send();
            }
		    unset($mail);
			}
		}
								
		return true;
	}
	
	public function incRead($id) {
		global $mysql;
		
		$mysql->query("
			UPDATE `bulletin`
			SET `readed` = `readed` + 1
			WHERE `id` = '".intval($id)."'
			LIMIT 1
		");
		
		return true;
	}
	
	
	public function deleteEmail($email) {
		global $mysql;
		
		$mysql->query("
			UPDATE `bulletin_emails`
			SET `bulletin_subscribe` = 'false'
			WHERE `email` = '".sql_quote($email)."'
		");
		
		return true;
	}
	
	public function deleteSEmail($id) {
		global $mysql;
		
		$mysql->query("
			DELETE FROM `bulletin_emails`
			WHERE `id` = '".intval($id)."'
		");
		
		return true;
	}
	
	#订阅邮件地址是否存在
	public function isExistBulletinEmail($email) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `bulletin_emails`
			WHERE `email` = '".sql_quote($email)."'
		");
		if($mysql->num_rows() == 0) {
			return false;
		}
		return $mysql->fetch_array();
	}
	
	#添加邮件订阅
	// public function addBulletinEmail() {
	// 	global $mysql;
		
	// 	//判断是否是电子邮件格式
	// 	if(!check_email($_POST['bulletin_email'])) {
	// 		return false;
	// 	}
	// 	//订阅邮件地址是否存在
	// 	$aEmail = $this->isExistBulletinEmail($_POST['bulletin_email']);
		
	// 	//订阅邮件存在
	// 	if($aEmail !== false) {
	// 		if(is_array($aEmail) && $aEmail['bulletin_subscribe'] == 'false') {
	// 			$mysql->query("
	// 				UPDATE `bulletin_emails`
	// 				SET `bulletin_subscribe` = 'true'
	// 				WHERE `email` = '".sql_quote($_POST['bulletin_email'])."'
	// 			");
	// 			return true;
	// 		}else{
	// 			return 'already';
	// 		}
	// 		return false;
	// 	}
		
	// 	if(!isset($_POST['bulletin_subname'])) {
	// 		$_POST['bulletin_subname'] = '';
	// 	}
	// 	if(!isset($_POST['bulletin_lname'])) {
	// 		$_POST['bulletin_lname'] = '';
	// 	}
		
	// 	//添加订阅邮件
	// 	$mysql->query("
	// 		INSERT INTO `bulletin_emails` (
	// 			`nickname`,
	// 			`email`
	// 		)
	// 		VALUES (
	// 			'".sql_quote($_POST['bulletin_subname'])."',
	// 			'".sql_quote($_POST['bulletin_lname'])."',
	// 			'".sql_quote($_POST['bulletin_email'])."'
	// 		)
	// 	");
		
	// 	return true;
	// }

    #添加邮件订阅
	public function addBulletinEmail() {
		global $mysql;
		
		//判断是否是电子邮件格式
		if(!check_email($_POST['email'])) {
			return false;
		}
		//订阅邮件地址是否存在
		$aEmail = $this->isExistBulletinEmail($_POST['email']);
		
		//订阅邮件存在
		if($aEmail !== false) {
			if(is_array($aEmail) && $aEmail['bulletin_subscribe'] == 'false') {
				$mysql->query("
					UPDATE `bulletin_emails`
					SET `bulletin_subscribe` = 'true'
					WHERE `email` = '".sql_quote($_POST['bulletin_email'])."'
				");
				return true;
			}else{
				return 'already';
			}
			return false;
		}
		
		if(!isset($_POST['username'])) {
			$_POST['username'] = '';
		}
		
		//添加订阅邮件
		$mysql->query("
			INSERT INTO `bulletin_emails` (
				`subname`,
				`email`
			)
			VALUES (
				'".sql_quote($_POST['username'])."',
				'".sql_quote($_POST['email'])."'
			)
		");
		
		return true;
	}
	
	public function changeSubscribe($id, $type='true') {
		global $mysql;
		
		$mysql->query("
			UPDATE `bulletin_emails`
			SET `bulletin_subscribe` = '".sql_quote($type)."'
			WHERE `id` = '".intval($id)."'
		");
		
		return true;
	}
	
	
	public function getTemplate() {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `bulletin_template`
			ORDER BY `id` DESC
			LIMIT 1
		");		
		
		if($mysql->num_rows() > 0) {
			$template = $mysql->fetch_array();
			$template = $template['template'];
		}
		else {
			$template = '{$CONTENT}';
		}
		
		return $template;
	}
	
}

?>