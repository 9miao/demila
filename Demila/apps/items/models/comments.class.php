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


class comments {
	
	public $foundRows = 0;
	public $usersWhere = '';
	
	public function getAll($start=0, $limit=0, $where='', $withReply=false, $order='`datetime` DESC') {
		global $mysql;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		$whereQuery = '';
		if($where!='') {
			$whereQuery = " WHERE ".$where;
		}
		
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `items_comments`
			$whereQuery
			ORDER BY $order
			$limitQuery
		");
			
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		$whereQuery = '';
		$this->usersQuery = '';
		while($d = $mysql->fetch_array()) {
			$d['comment'] = replaceEmoticons($d['comment']);
			$return[$d['id']] = $d;
			
			if($whereQuery != '') {
				$whereQuery .= ' OR ';
			}
			$whereQuery .= " `reply_to` = '".intval($d['id'])."' ";
			
			if($this->usersQuery != '') {
				$this->usersQuery .= ' OR ';
			}
			$this->usersQuery .= " `user_id` = '".intval($d['user_id'])."' ";
		}
		
		$this->foundRows = $mysql->getFoundRows();
		
		if($withReply && $whereQuery != '') {
			$mysql->query("
				SELECT *
				FROM `items_comments`
				WHERE $whereQuery
				ORDER BY `datetime` ASC
			");
			
			if($mysql->num_rows() > 0) {
				while($d = $mysql->fetch_array()) {
					
					$d['comment'] = replaceEmoticons($d['comment']);
					$return[$d['reply_to']]['reply'][$d['id']] = $d;
					
					if($this->usersQuery != '') {
						$this->usersQuery .= ' OR ';
					}
					$this->usersQuery .= " `user_id` = '".intval($d['user_id'])."' ";
				}
			}
		}
		
		return $return;
	}
	
	public function get($id) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `items_comments`
			WHERE `id` = '".intval($id)."'
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return $mysql->fetch_array();
	}
	
	public function add($replyTo=0) {
		global $mysql, $item, $languageURL, $langArray;
		
		if(!isset($_POST['comment']) || trim($_POST['comment']) == '') {
			return false;
		}
		
		if(!isset($_POST['reply_notification'])) {
			$_POST['reply_notification'] = 'false';
		}
		else {
			$_POST['reply_notification'] = 'true';
		}
		
		$mysql->query("
			INSERT INTO `items_comments` (
				`owner_id`,
				`item_id`,
				`item_name`,
				`user_id`,
				`comment`,
				`datetime`,
				`notify`,
				`reply_to`				
			)
			VALUES (
				'".intval($item['user_id'])."',
				'".intval($item['id'])."',
				'".sql_quote($item['name'])."',
				'".intval($_SESSION['user']['user_id'])."',
				'".sql_quote($_POST['comment'])."',
				NOW(),
				'".sql_quote($_POST['reply_notification'])."',
				'".intval($replyTo)."'
			)
		");
		
		if($replyTo != 0) {
			
			$comment = $this->get($replyTo);
			if($comment['notify'] == 'true') {
				
				require_once ROOT_PATH.'/apps/users/models/users.class.php';
				$usersClass = new users();
				
				$user = $usersClass->get($comment['user_id']);
				
				$emailClass = new email();
		
				$emailClass->to($user['email']);
				$emailClass->fromEmail = 'no-reply@'.DOMAIN;
				$emailClass->contentType = 'text/html';
				$emailClass->subject = "[".DOMAIN."] ".$langArray['email_new_reply_subject'];
				$emailClass->message = langMessageReplace($langArray['email_new_reply_text'], array(
																	'THEMENAME' => $item['name'],
																	'URL' => 'http://'.DOMAIN.'/'.$languageURL.'items/comments/'.$item['id']
																));
		
				$emailClass->send();
				
				unset($emailClass);
				
			}
			
		}
		else {
		
#评论			
			$mysql->query("
				UPDATE `items`
				SET `comments` = `comments` + 1
				WHERE `id` = '".intval($item['id'])."'
				LIMIT 1
			");
		
		}
			
		return true;
	}	
	
	public function delete($id) {
		global $mysql;
		
		$row = $this->get($id);
		if(!is_array($row)) {
			return true;
		}
		
		$mysql->query("
			DELETE FROM `items_comments`
			WHERE `id` = '".intval($id)."'
			LIMIT 1
		");
		
		$mysql->query("
			DELETE FROM `items_comments`
			WHERE `reply_to` = '".intval($id)."'			
		");
		
		if($row['reply_to'] == '0') {
			$mysql->query("
				UPDATE `items`
				SET `comments` = `comments` - 1
				WHERE `id` = '".intval($row['item_id'])."'
			");
		}
		
		return true;
	}
	
	public function report($id) {
		global $mysql, $langArray;
		
		$mysql->query("
			UPDATE `items_comments`
			SET `report_by` = '".intval($_SESSION['user']['user_id'])."'
			WHERE `id` = '".intval($id)."'
			LIMIT 1
		");
		
#给管理员发送电子邮件
		$mysql->query("
			SELECT *
			FROM `system`
			WHERE `key` = 'admin_mail' OR `key` = 'report_mail'
		");
		
		while($d = $mysql->fetch_array()) {
			if($d['key'] == 'report_mail') {
				$sendTo = $d['value'];
				break;
			}
			$sendTo = $d['value'];
		}
		
		$emailClass = new email();
		
		$emailClass->to($sendTo);
		$emailClass->fromEmail = 'no-reply@'.DOMAIN;
		$emailClass->contentType = 'text/html';
		$emailClass->subject = "[".DOMAIN."] ".$langArray['email_report_comment_subject'];
		$emailClass->message = $_SESSION['user']['username'].$langArray['email_report_comment_text'];

		$emailClass->send();
		
		unset($emailClass);

		return true;
	}
	
	//作品评论状态修改
	public function reported($id) {
		global $mysql, $langArray;
		
		$mysql->query("
			UPDATE `items_comments`
			SET `report_by` = '0'
			WHERE `id` = '".intval($id)."'
			LIMIT 1
		");
		
		return true;
	}
	
}

?>