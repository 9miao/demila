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


//资金流动统计
class transaction_details extends base {
	
	function __construct() {
		global $config;
	}
	
    //记录资金流动
    public function addRecord($uid=0,$type='',$value=0,$info=''){
        global $mysql;
		$mysql->query("
			INSERT INTO `transaction_details` (
				`uid`,
				`type`,
				`value`,
				`info`,
				`time`
			)
			VALUES (
				'".(int)$uid ."',
				'".$type."',
				'".$value."',
				'".sql_quote($info)."',
				NOW()
			)
		", __FUNCTION__ );

		return true;
    }

	//获取资金流动记录
	public function getRecord($userID){
        global $mysql;
		
		$mysql->query("
			SELECT * FROM `transaction_details`
			WHERE `uid` = '".intval($userID)."'
			ORDER BY `time` DESC
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
}
?>