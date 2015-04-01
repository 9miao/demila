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


class quiz extends base {
	
	function __construct() {
		$this->tableName = 'quiz';
	}
	
	/*
	 * 获取函数
	 */
	public function getAll($start=0, $limit=0, $where='', $order='`order_index` ASC') {
		global $mysql, $langArray;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		if($where!='') {
			$where = " WHERE ".$where;
		}
		
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `quiz`
			$where
			ORDER BY $order
			$limitQuery
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		$whereQuery = '';
		while($d = $mysql->fetch_array()) {
			$return[$d['id']] = $d;
		}
		
		$this->foundRows = $mysql->getFoundRows();
				
		return $return;
	}
	
	public function get($id) {
		global $mysql, $language;
		
		$mysql->query("
			SELECT *
			FROM `quiz`
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return $mysql->fetch_array();
	}
	
	/*
	 * 添加
	 */
	public function add() {
		global $mysql, $langArray;
		
		if(!isset($_POST['name']) || trim($_POST['name']) == '') {
			$error['name'] = $langArray['error_fill_this_field'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		$orderIndex = $this->getNextOrderIndex();
		
		$mysql->query("
			INSERT INTO `quiz` (
				`name`,
				`order_index`
			)
			VALUES (
				'".sql_quote($_POST['name'])."',
				'".intval($orderIndex)."'
			)
		", __FUNCTION__ );
			
		return true;
	}
	
	/*
	 * 编辑
	 */
	public function edit($id) {
		global $mysql, $langArray;
		
		if(!isset($_POST['name']) || trim($_POST['name']) == '') {
			$error['name'] = $langArray['error_fill_this_field'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		$mysql->query("
			UPDATE `quiz` 
			SET	`name` = '".sql_quote($_POST['name'])."'
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );

		return true;
	}
	
	/*
	 * 删除
	 */
	public function delete($id) {
		global $mysql;
		
		$mysql->query("
			DELETE FROM `quiz_answers`
			WHERE `quiz_id` = '".intval($id)."'
		");
		
		$mysql->query("
			DELETE FROM `quiz`
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
		
		return true;
	}
	
}

?>