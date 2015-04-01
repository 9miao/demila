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


class balance extends base {
	
	function __construct() {
		$this->tableName = 'deposit';
	}

	public function getUserBalance($id, $is_pay = false) {
		global $mysql, $language;
		
		$sql = "
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `deposit` 
			WHERE `user_id` = " . (int)$id . " 
		";
		
		if($is_pay !== null) {
			$sql .= "AND `paid` = '" . ($is_pay ? 'true' : 'false') . "'";
		}
		
		$sql .= "ORDER BY `id` DESC";
		
		$mysql->query($sql, __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		while($d = $mysql->fetch_array()) {
			$return[$d['id']] = $d;
		}
		
		$this->foundRows = $mysql->getFoundRows();
		
		return $return;
	}

	public function getTotalUserBalance($id, $is_pay = false) {
		global $mysql, $language;
		
		$sql = "
			SELECT COUNT(`id`) AS `total`
			FROM `deposit` 
			WHERE `user_id` = " . (int)$id . " 
		";
		
		if($is_pay !== null) {
			$sql .= "AND `paid` = '" . ($is_pay ? 'true' : 'false') . "'";
		}
		
		$sql .= "LIMIT 1";
		
		$mysql->query($sql, __FUNCTION__ );
		
		$d = $mysql->fetch_array();
		return $d['total'];
	}

	public function getTotalUserBalanceByType($id) {
		global $mysql, $language;
		
		$mysql->query("
			SELECT SUM(IF(`paid` = 'true', 1, 0)) AS `paid`,SUM(IF(`paid` = 'false', 1, 0)) AS `not_paid` 
			FROM `deposit` 
			WHERE `user_id` = " . (int)$id . " 
			GROUP BY `user_id`
		", __FUNCTION__ );
		
		$d = $mysql->fetch_array();
		
		if(!$d) {
			return array('paid' => 0, 'not_paid' => 0);
		}
		
		return $d;
	}
	
	public function get($id) {
		global $mysql, $language;
		
		$mysql->query("
			SELECT *
			FROM `deposit` 
			WHERE `id` = " . (int)$id . " 
			LIMIT 1
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return $mysql->fetch_array();
	}
	
	public function delete($id) {
		global $mysql;
		
		$row = $this->get($id);
		if(!is_array($row)) {
			return true;
		}
		
		$mysql->query("
			DELETE FROM `deposit`
			WHERE `id` = '".intval($id)."'
			LIMIT 1
		", __FUNCTION__ );
		
		$mysql->query("
		UPDATE `users` 
		SET `deposit` = `deposit` - " . (int)$row['deposit'] . ",
		`total` = `total` - " . (int)$row['deposit'] . " 
		WHERE `user_id` = '" . (int)$row['user_id'] . "'
		", __FUNCTION__ );
		
		return true;
	}
	
	public function add() {
		global $mysql, $langArray;

		if(!isset($_POST['balance']) || !preg_match('/^([0-9]{1,})$/', $_POST['balance']) ) {
			$error['balance'] = $langArray['the_field_must_be_an_integer_value'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		$mysql->query("
		INSERT INTO 
			`deposit` 
		SET 
			`user_id` = " . (int)$_GET['user_id'] . ",
			`deposit` = " . (int)$_POST['balance'] . ",
			`paid` = 'true',
			`datetime` = NOW(),
			`from_admin` = 1
		", __FUNCTION__ );
		
		$mysql->query("
		UPDATE 
			`users` 
		SET 
			`deposit` = `deposit` + " . (int)$_POST['balance'] . ",
			`total` = `total` + " . (int)$_POST['balance'] . " 
		WHERE 
			`user_id` = '" . (int)$_GET['user_id'] . "'
		", __FUNCTION__ );

		return true;
		
	}
	
	public function edit() {
		global $mysql, $langArray;

		if(!isset($_POST['balance']) || !preg_match('/^([0-9]{1,})$/', $_POST['balance']) ) {
			$error['balance'] = $langArray['the_field_must_be_an_integer_value'];
		}
		
		$row = $this->get($_GET['id']);
		
		if(!$row) {
			$error['warning'] = $langArray['no_records'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		
		$mysql->query("
		UPDATE 
			`deposit` 
		SET 
			`deposit` = " . (int)$_POST['balance'] . "
		WHERE
			`id` = " . (int)$_GET['id'] . "
		", __FUNCTION__ );
		
		$mysql->query("
		UPDATE 
			`users` 
		SET 
			`deposit` = `deposit` + " . ( -(int)$row['deposit'] + (int)$_POST['balance'] ) . ",
			`total` = `total` + " . ( -(int)$row['deposit'] + (int)$_POST['balance'] ) . " 
		WHERE 
			`user_id` = '" . (int)$_GET['user_id'] . "'
		", __FUNCTION__ );

		return true;
		
	}
	
}

?>