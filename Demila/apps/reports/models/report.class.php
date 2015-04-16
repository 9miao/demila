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


class report {
	
	public function getReport() {
		global $mysql;
		
		$orderQuery = '';
		
		if(isset($_POST['from_date']) && trim($_POST['from_date']) != '') {
			$d = explode('-', $_POST['from_date']);
			if(checkdate($d[1], $d[2], $d[0])) {
				$orderQuery .= " AND `paid_datetime` >= '".sql_quote($_POST['from_date'])."' ";
			}
		} 

		if(isset($_POST['to_date']) && trim($_POST['to_date']) != '') {
			$d = explode('-', $_POST['to_date']);
			if(checkdate($d[1], $d[2], $d[0])) {
				$orderQuery .= " AND `paid_datetime` <= '".sql_quote($_POST['to_date'])."' ";
			}
		}

		$mysql->query("
			SELECT *
			FROM `orders`
			WHERE `paid` = 'true' $orderQuery
			ORDER BY `paid_datetime`
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		while($d = $mysql->fetch_array()) {
			$date = explode(' ', $d['paid_datetime']);
			$date = $date[0];
			
			if(!isset($return[$date]['total'])) {
				$return[$date]['total'] = 0;
				$return[$date]['receive'] = 0;
				$return[$date]['referal'] = 0;
			}
				
			if($d['type'] == 'buy') {
				$return[$date]['total'] += $d['price'];
				$return[$date]['receive'] += $d['receive'];				
			}
			else {
				$return[$date]['referal'] += $d['receive'];				
			}
			
			$return[$date]['win'] = floatval($return[$date]['total']) - floatval($return[$date]['receive']) - floatval($return[$date]['referal']);
		}
		
		return $return;
		
	}
	
	public function getDeposits() {
		global $mysql;
		
		$orderQuery = '';
		
		if(isset($_POST['from_date']) && trim($_POST['from_date']) != '') {
			$d = explode('-', $_POST['from_date']);
			if(checkdate($d[1], $d[2], $d[0])) {
				$orderQuery .= " AND `datetime` >= '".sql_quote($_POST['from_date'])."' ";
			}
		} 

		if(isset($_POST['to_date']) && trim($_POST['to_date']) != '') {
			$d = explode('-', $_POST['to_date']);
			if(checkdate($d[1], $d[2], $d[0])) {
				$orderQuery .= " AND `datetime` <= '".sql_quote($_POST['to_date'])."' ";
			}
		}

		$mysql->query("
			SELECT *
			FROM `deposit`
			WHERE `paid` = 'true' $orderQuery
			ORDER BY `datetime`
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		while($d = $mysql->fetch_array()) {
			$date = explode(' ', $d['datetime']);
			$date = $date[0];
			
			if(!isset($return[$date]['deposit'])) {
				$return[$date]['deposit'] = 0;
			}
				
			$return[$date]['deposit'] += $d['deposit'];
		}
		
		return $return;
		
	}
	
	public function getWithdraws() {
		global $mysql;
		
		$orderQuery = '';
		
		if(isset($_POST['from_date']) && trim($_POST['from_date']) != '') {
			$d = explode('-', $_POST['from_date']);
			if(checkdate($d[1], $d[2], $d[0])) {
				$orderQuery .= " AND `paid_datetime` >= '".sql_quote($_POST['from_date'])."' ";
			}
		} 

		if(isset($_POST['to_date']) && trim($_POST['to_date']) != '') {
			$d = explode('-', $_POST['to_date']);
			if(checkdate($d[1], $d[2], $d[0])) {
				$orderQuery .= " AND `paid_datetime` <= '".sql_quote($_POST['to_date'])."' ";
			}
		}

		$mysql->query("
			SELECT *
			FROM `withdraw`
			WHERE `paid` = 'true' $orderQuery
			ORDER BY `paid_datetime`
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		while($d = $mysql->fetch_array()) {
			$date = explode(' ', $d['paid_datetime']);
			$date = $date[0];
			
			if(!isset($return[$date]['amount'])) {
				$return[$date]['amount'] = 0;
			}
				
			$return[$date]['amount'] += $d['amount'];
		}
		
		return $return;
		
	}
	
}

?>