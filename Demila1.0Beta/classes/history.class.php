<?php 

class history {
	
	public function getAll($start=0, $limit=0, $where='') {
		global $mysql;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		if($where!='') {
			$where = " WHERE ".$where;
		}
		
		$mysql->query("
			SELECT *
			FROM `history`
			$where
			ORDER BY `datetime` DESC
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
	
	#添加到充值记录
	public function add($action, $transactionID, $userID=0) {
		global $mysql;
		
		if(!check_login_bool()) {
			return false;
		}
		
		if($userID == 0) {
			$userID = $_SESSION['user']['user_id'];
		}
		
		$mysql->query("
			INSERT INTO `history` (
				`user_id`,
				`action`,
				`transaction_id`,
				`datetime`
			)
			VALUES (
				'".intval($userID)."',
				'".sql_quote($action)."',
				'".sql_quote($transactionID)."',
				NOW()
			)
		");
		
		return true;
	}
	
}

?>