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


class groups extends base {
	
	function __construct() {
	
	}

	
	/*
	 * GET FUNCTIONS
	 */
	public function getAll($start=0, $limit=0) {
		global $mysql;
		
		$limitQuery = "";
		if($limit!=0) {
			$limitQuery = "LIMIT $start,$limit";
		}
		
		$return = $mysql->getAll("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `user_groups`
			ORDER BY `name` ASC
			$limitQuery
		" );
			
		$this->foundRows = $mysql->getFoundRows();
		
		return $return;
	}
	
	public function get($id) {
		global $mysql;
		
		$return = $mysql->getRow("
			SELECT *
			FROM `user_groups`
			WHERE `ug_id` = '".intval($id)."'
		" );
		
		$rights = unserialize($return['rights']);
		if(is_array($rights)) {
			foreach($rights as $k=>$v) {
				$return['modules'][$k] = $v;
			}
		}
		
		return $return;
	}
	
	
	/*
	 * 添加
	 */
	public function add() {
		global $mysql, $langArray;

		if(!isset($_POST['name']) || strlen(trim($_POST['name'])) < 1) {
			$error['name'] = $langArray['error_fill_this_field'];
		}
		
		if(!isset($_POST['description']) || strlen(trim($_POST['description'])) < 1) {
			$error['desc'] = $langArray['error_fill_this_field'];
		}
		
		if(!isset($_POST['modules'])) {
			$error['modules'] = $langArray['error_fill_this_field'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		$rights = array();		
		if(is_array($_POST['modules'])) {
			foreach($_POST['modules'] as $k=>$v) {
				$rights[$k] = $v;
			}
		}
		
		$mysql->query("
			INSERT INTO `user_groups` (
				`name`,
				`description`,
				`rights`
			)
			VALUES (
				'".sql_quote($_POST['name'])."',
				'".sql_quote($_POST['description'])."',
				'".serialize($rights)."'
			)
		", __FUNCTION__ );
		
		return true;
	}
	
	
	/*
	 * 编辑
	 */
	public function edit($id) {
		global $mysql, $langArray;

		if(!isset($_POST['name']) || strlen(trim($_POST['name'])) < 1) {
			$error['name'] = $langArray['error_fill_this_field'];
		}
		
		if(!isset($_POST['description']) || strlen(trim($_POST['description'])) < 1) {
			$error['desc'] = $langArray['error_fill_this_field'];
		}
		
		if(!isset($_POST['modules'])) {
			$error['modules'] = $langArray['error_fill_this_field'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		$rights = array();		
		if(is_array($_POST['modules'])) {
			foreach($_POST['modules'] as $k=>$v) {
				$rights[$k] = $v;
			}
		}
		
		$mysql->query("
			UPDATE `user_groups` 
			SET `name` = '".sql_quote($_POST['name'])."',
					`description` = '".sql_quote($_POST['description'])."',
					`rights` = '".serialize($rights)."'
			WHERE `ug_id` = '".intval($id)."'
		", __FUNCTION__ );
		
		return true;
	}
	
	/*
	 * 删除
	 */
	public function delete($id) {
		global $mysql;

		$mysql->query("
			DELETE FROM `user_groups`
			WHERE `ug_id` = '".intval($id)."'
			LIMIT 1
		", __FUNCTION__ );
		
		return true;
	}

}
?>