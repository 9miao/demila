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


class faq {
	
	public function getAll($id) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `items_faqs`
			WHERE `item_id` = '".intval($id)."'
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		while($d = $mysql->fetch_array()) {
			$return[$d['id']] = $d;
		}
		
		return $return;
	}
	
	public function add($id) {
		global $mysql, $langArray;
		
		if(!isset($_POST['question']) || trim($_POST['question']) == '') {
			$error['question'] = $langArray['error_not_set_question'];
		}

		if(!isset($_POST['answer']) || trim($_POST['answer']) == '') {
			$error['answer'] = $langArray['error_not_set_answer'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		$mysql->query("
			INSERT INTO `items_faqs` (
				`item_id`,
				`user_id`,
				`question`,
				`answer`,
				`datetime`
			)
			VALUES (
				'".intval($id)."',
				'".intval($_SESSION['user']['user_id'])."',
				'".sql_quote($_POST['question'])."',
				'".sql_quote($_POST['answer'])."',
				NOW()
			)
		");
		
		return true;
	}
	
	public function delete($id, $itemID) {
		global $mysql;
		
		$mysql->query("
			DELETE FROM `items_faqs`
			WHERE `id` = '".intval($id)."' AND `item_id` = '".intval($itemID)."'
			LIMIT 1
		");
		
		return true;
	}
	
	public function CountAll($itemID) {
	    global $mysql;
		
		$mysql->query("
			SELECT COUNT(`id`) as count
			FROM `items_faqs`
			WHERE `item_id` = '".intval($itemID)."'
		");
		
		$r = $mysql->fetch_array();
		
		return $r['count'];
	}
	
}

?>