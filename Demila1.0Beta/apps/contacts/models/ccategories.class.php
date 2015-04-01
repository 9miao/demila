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


class ccategories extends base {
	
	function __construct() {
		$this->tableName = 'contacts_categories';
	}
	
	/*
	 * 获取函数
	 */
	public function getAll($start=0, $limit=0, $where='') {
		global $mysql;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		if($where!='') {
			$where = "WHERE ".$where;
		}
		
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `contacts_categories`
			$where
			ORDER BY `order_index` ASC
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
			FROM `contacts_categories`
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
		
		if(!isset($_POST['visible'])) {
			$_POST['visible'] = 'false';
		}
		
		$orderIndex = $this->getNextOrderIndex();
		
		if(!isset($_POST['text']) || $_POST['text'] == '') {
			$_POST['text'] = '';
		}
		
		$mysql->query("
			INSERT INTO `contacts_categories` (
				`name`,
				`text`,
				`visible`,
				`order_index`
			)
			VALUES (
				'".sql_quote($_POST['name'])."',
				'".sql_quote($_POST['text'])."',
				'".sql_quote($_POST['visible'])."',
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
		
		if(!isset($_POST['visible'])) {
			$_POST['visible'] = 'false';
		}
		
		if(!isset($_POST['text'])) {
			$_POST['text'] = '';
		}
		
		$mysql->query("
			UPDATE `contacts_categories` 
			SET	`name` = '".sql_quote($_POST['name'])."',
					`text` = '".sql_quote($_POST['text'])."',
					`visible` = '".sql_quote($_POST['visible'])."'
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
			DELETE FROM `contacts_categories`
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
		
		return true;
	}
	
}

?>