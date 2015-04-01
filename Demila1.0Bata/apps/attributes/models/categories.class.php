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


class categories extends base {
	
	function __construct() {
		$this->tableName = 'attributes_categories';
	}
	
	/*
	 * 获取函数
	 */
	public function getAll($start=0, $limit=0, $where='') {
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
			FROM `attributes_categories`
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
			FROM `attributes_categories`
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = $mysql->fetch_array();
		
		if($return['categories'] != '') {
			$buff = explode(',', $return['categories']);
			if(is_array($buff)) {
				foreach($buff as $v) {
					$v = trim($v);
					if($v != '') {
						$return['category'][$v] = $v;
					}
				}
			}
		}
		
		return $return;
	}
	
	/*
	 * 添加属性类别
	 */
	public function add() {
		global $mysql, $langArray;
		
		if(!isset($_POST['name']) || trim($_POST['name']) == '') {
			$error['name'] = $langArray['error_fill_this_field'];
		}
		
		if(!isset($_POST['type']) || trim($_POST['type']) == '') {
			$error['select'] = $langArray['error_fill_this_field'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		if(!isset($_POST['visible'])) {
			$_POST['visible'] = 'false';
		}
		
		$categories = '';
		if(isset($_POST['category']) && is_array($_POST['category'])) {
			$categories = ',';
			foreach($_POST['category'] as $c) {
				$categories .= $c.',';
			}
		}
		
		$orderIndex = $this->getNextOrderIndex();
		
		$mysql->query("
			INSERT INTO `attributes_categories` (
				`name`,
				`type`,
				`categories`,
				`visible`,
				`order_index`
			)
			VALUES (
				'".sql_quote($_POST['name'])."',
				'".sql_quote($_POST['type'])."',
				'".sql_quote($categories)."',
				'".sql_quote($_POST['visible'])."',
				'".intval($orderIndex)."'
			)
		", __FUNCTION__ );
			
		return true;
	}
	
	/*
	 * 编辑属性类别
	 */
	public function edit($id) {
		global $mysql, $langArray;
		
		if(!isset($_POST['name']) || trim($_POST['name']) == '') {
			$error['name'] = $langArray['error_fill_this_field'];
		}
		
		if(!isset($_POST['type']) || trim($_POST['type']) == '') {
			$error['select'] = $langArray['error_fill_this_field'];
		}
				
		if(isset($error)) {
			return $error;
		}
		
		if(!isset($_POST['visible'])) {
			$_POST['visible'] = 'false';
		}
		
		$categories = '';
		if(isset($_POST['category']) && is_array($_POST['category'])) {
			$categories = ',';
			foreach($_POST['category'] as $c) {
				$categories .= $c.',';
			}
		}
		
		$mysql->query("
			UPDATE `attributes_categories` 
			SET	`name` = '".sql_quote($_POST['name'])."',
					`type` = '".sql_quote($_POST['type'])."',
					`categories` = '".sql_quote($categories)."',
					`visible` = '".sql_quote($_POST['visible'])."'
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );

		return true;
	}
	
	/*
	 * 删除属性类别
	 */
	public function delete($id) {
		global $mysql;
		
		require_once ROOT_PATH.'/apps/attributes/models/attributes.class.php';
		$attributesClass = new attributes();
		
		$attributes = $attributesClass->getAll(0, 0, " `category_id` = '".intval($id)."' ");
		if(is_array($attributes)) {
			foreach($attributes as $a) {
				$attributesClass->delete($a['id']);
			}
		}
		
		$mysql->query("
			DELETE FROM `attributes_categories`
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
		
		return true;
	}
	
}

?>