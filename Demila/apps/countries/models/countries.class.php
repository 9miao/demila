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


class countries extends base {
	
	function __construct() {
		$this->tableName = 'countries';
		$this->uploadFileDirectory = 'countries/';
	}
	
	/*
	 * 获取函数
	 */
	public function getAll($start=0, $limit=0, $where='') {
		global $mysql, $language;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		if($where!='') {
			$where = " WHERE ".$where;
		}
		
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `countries`
			$where
			ORDER BY `order_index` ASC
			$limitQuery
		", __FUNCTION__ );
		
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
	
	public function get($id) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `countries`
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
		global $mysql, $langArray, $config;
		
		if(!isset($_POST['name']) || trim($_POST['name']) == '') {
			$error['name'] = $langArray['error_fill_this_field'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		$photo = $this->upload('photo', '', false);
		if(substr($photo, 0, 6) == 'error_') {
			$error['photo'] = $langArray[$photo];
		}
				
		if(isset($error)) {
			return $error;
		}
		
		if(!isset($_POST['visible'])) {
			$_POST['visible'] = 'false';
		}
		
		$orderIndex = $this->getNextOrderIndex();
		
		$mysql->query("
			INSERT INTO `countries` (
				`name`,
				`photo`,
				`visible`,
				`order_index`
			)
			VALUES (
				'".sql_quote($_POST['name'])."',
				'".sql_quote($photo)."',
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
		
		$photo = $this->upload('photo', '', false);
		if(substr($photo, 0, 6) == 'error_') {
			$error['photo'] = $langArray[$photo];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		$setQuery = '';
		if($photo != '' || isset($_POST['deletePhoto'])) {
			$this->deletePhoto($id);
		}
		if($photo != '') {
			$setQuery .= " `photo` = '".sql_quote($photo)."', ";
		}
		
		if(!isset($_POST['visible'])) {
			$_POST['visible'] = 'false';
		}
		
		$mysql->query("
			UPDATE `countries`
			SET `name` = '".sql_quote($_POST['name'])."',
					$setQuery
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
		
		$this->deletePhoto($id);
		
		$mysql->query("
			DELETE FROM `countries`
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
		
		return true;
	}	
	
	private function deletePhoto($id) {
		global $mysql, $config;
		
		$post = $this->get($id);
		if($post['photo'] != '') {
			@unlink(DATA_SERVER_PATH.'uploads/'.$this->uploadFileDirectory.$post['photo']);
		}
		
		$mysql->query("
			UPDATE `countries`
			SET `photo` = ''
			WHERE `id` = '".intval($id)."'
		");
		
		return true;
	}
	
}

?>