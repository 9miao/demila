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


class pages extends base {
	
	function __construct() {
		$this->tableName = 'pages';
	}
	
	/*
	 * 获取函数
	 */
	public function getAll($start=0, $limit=0, $where='', $bySubOf=false) {
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
			FROM `pages`
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
			if($bySubOf) {
				$return[$d['sub_of']][] = $d;
			}
			else {
				$return[$d['id']] = $d;
			}
		}
		
		$this->foundRows = $mysql->getFoundRows();
				
		return $return;
	}
	
	public function get($id) {
		global $mysql, $language;
		
		$mysql->query("
			SELECT *
			FROM `pages`
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return $mysql->fetch_array();
	}
	
	public function getbyKey($key) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `pages`
			WHERE `key` = '".sql_quote($key)."'
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return $mysql->fetch_array();
	}
	
	/* 
	 * 检查函数 
	 */
	private function isExistKey($key, $id=false) {
		global $mysql;
		
		$whereQuery = '';
		if($id) {
			$whereQuery = " AND `id` <> '".intval($id)."' ";
		}
		
		$mysql->query("
			SELECT *
			FROM `pages`
			WHERE `key` = '".sql_quote($key)."' $whereQuery
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return true;
	}
	
	/*
	 * 添加
	 */
	public function add() {
		global $mysql, $langArray;
		
		if(!isset($_POST['name']) || trim($_POST['name']) == '') {
			$error['name'] = $langArray['error_fill_this_field'];
		}
		
		if(!isset($_POST['key']) || trim($_POST['key']) == '') {
			$error['key'] = $langArray['error_fill_this_field'];
		}
		elseif($this->isExistKey($_POST['key'])) {
			$error['key'] = $langArray['error_key_exist'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		if(!isset($_POST['menu'])) {
			$_POST['menu'] = 'false';
		}
		
		if(!isset($_POST['footer'])) {
			$_POST['footer'] = 'false';
		}
		
		if(!isset($_POST['visible'])) {
			$_POST['visible'] = 'false';
		}
		
		$parentID = 0;
		
		$this->orderWhere = " AND `sub_of` = '".intval($_GET['sub_of'])."' ";
		$orderIndex = $this->getNextOrderIndex();
		
		if(!isset($_POST['meta_title']) || $_POST['meta_title'] == '') {
			$_POST['meta_title'] = $_POST['name'];
		}
		
		if(!isset($_POST['meta_keywords']) || $_POST['meta_keywords'] == '') {
			$_POST['meta_keywords'] = $_POST['name'];
		}
		
		if(!isset($_POST['meta_description']) || $_POST['meta_description'] == '') {
			$_POST['meta_description'] = $_POST['name'];
		}
		
		if(!isset($_POST['text']) || $_POST['text'] == '') {
			$_POST['text'] = '';
		}
		
		$mysql->query("
			INSERT INTO `pages` (
				`sub_of`,
				`key`,
				`meta_title`,
				`meta_keywords`,
				`meta_description`,
				`name`,
				`text`,
				`menu`,
				`footer`,
				`visible`,
				`order_index`
			)
			VALUES (
				'".intval($_GET['sub_of'])."',
				'".sql_quote($_POST['key'])."',
				'".sql_quote($_POST['meta_title'])."',
				'".sql_quote($_POST['meta_keywords'])."',
				'".sql_quote($_POST['meta_description'])."',
				'".sql_quote($_POST['name'])."',
				'".sql_quote($_POST['text'])."',
				'".sql_quote($_POST['menu'])."',
				'".sql_quote($_POST['footer'])."',
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
		
		if(!isset($_POST['sub_of']) || !is_numeric($_POST['sub_of'])) {
			$error['sub'] = $langArray['error_fill_this_field'];
		}
		
		if(!isset($_POST['key']) || trim($_POST['key']) == '') {
			$error['key'] = $langArray['error_fill_this_field'];
		}
		elseif($this->isExistKey($_POST['key'], $id)) {
			$error['key'] = $langArray['error_key_exist'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		if(!isset($_POST['menu'])) {
			$_POST['menu'] = 'false';
		}
		
		if(!isset($_POST['footer'])) {
			$_POST['footer'] = 'false';
		}
		
		if(!isset($_POST['visible'])) {
			$_POST['visible'] = 'false';
		}
		
		$setQuery = '';
		if($_POST['sub_of'] != $_POST['sub_of_old']) {
			$info = $this->get($id);
			
			$this->orderWhere = " AND `sub_of` = '".intval($_POST['sub_of'])."' ";
			$orderIndex = $this->getNextOrderIndex();
			$setQuery .= " `order_index` = '".intval($orderIndex)."', ";
		}
		
		if(!isset($_POST['meta_title'])) {
			$_POST['meta_title'] = '';
		}
		
		if(!isset($_POST['meta_keywords'])) {
			$_POST['meta_keywords'] = '';
		}
		
		if(!isset($_POST['meta_description'])) {
			$_POST['meta_description'] = '';
		}
		
		if(!isset($_POST['text'])) {
			$_POST['text'] = '';
		}
		
		$mysql->query("
			UPDATE `pages` 
			SET	`sub_of` = '".intval($_POST['sub_of'])."',
					`key` = '".sql_quote($_POST['key'])."',
					`meta_title` = '".sql_quote($_POST['meta_title'])."',
					`meta_keywords` = '".sql_quote($_POST['meta_keywords'])."',
					`meta_description` = '".sql_quote($_POST['meta_description'])."',
					`name` = '".sql_quote($_POST['name'])."',
					`text` = '".sql_quote($_POST['text'])."',
					$setQuery						
					`menu` = '".sql_quote($_POST['menu'])."',
					`footer` = '".sql_quote($_POST['footer'])."',
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
		
		$info = $this->get($id);
		
		$this->orderWhere = " AND `sub_of` = '".intval($info['sub_of'])."' ";
		$orderIndex = $this->getNextOrderIndex();
		
		$mysql->query("
			UPDATE `pages`
			SET `sub_of` = '0',
					`order_index` = `order_index` + '".intval($orderIndex)."'
			WHERE `sub_of` = '".intval($id)."'
		");
		
		$mysql->query("
			DELETE FROM `pages`
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
		
		return true;
	}
	
	
	public function getAllWithChilds($id=0) {
		global $mysql;
		
		$whereQuery = '';
		if($id != 0) {
			$whereQuery = " WHERE `id` <> '".intval($id)."' ";
		}
		
		$mysql->query("
			SELECT *
			FROM `pages`
			$whereQuery			
			ORDER BY `order_index` ASC
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		while($d = $mysql->fetch_array()) {
			$return[$d['sub_of']][$d['id']] = $d;
		}
		
		return $return;
	}
	
	public function generateSelect($array, $selected=0, $subOf=0, $depth=0) {
		
		$text = '';
		
		if(isset($array[$subOf])) {
		
			foreach($array[$subOf] as $v) {
				$text .= '<option value="'.$v['id'].'"';
				if($v['id'] == $selected) {
					$text .= ' selected="selected" ';
				}
				$text .= '>';
				
				if($depth > 0) {
					for($i=0; $i<$depth; $i++) {
						$text .= '&nbsp;&nbsp;';
					}
				}
				
				$text .= $v['name'].'</option>';
				$text .= $this->generateSelect($array, $selected, $v['id'], $depth+1);
			}
		
		}
		
		return $text;
	}

	
}

?>