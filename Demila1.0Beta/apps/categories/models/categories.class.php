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
		$this->tableName = 'categories';
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
			FROM `categories`
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
			$d['clear_text'] = trim(strip_tags($d['text']));
			$return[$d['id']] = $d;
		}
		
		$this->foundRows = $mysql->getFoundRows();

		return $return;
	}
	
	public function get($id) {
		global $mysql, $language;
		
		$mysql->query("
			SELECT *
			FROM `categories`
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$d = $mysql->fetch_array();
		$d['clear_text'] = trim(strip_tags($d['text']));
		return $d;
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
			INSERT INTO `categories` (
				`sub_of`,
				`meta_title`,
				`meta_keywords`,
				`meta_description`,
				`name`,
				`text`,
				`visible`,
				`order_index`
			)
			VALUES (
				'".intval($_GET['sub_of'])."',
				'".sql_quote($_POST['meta_title'])."',
				'".sql_quote($_POST['meta_keywords'])."',
				'".sql_quote($_POST['meta_description'])."',
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
		
		if(!isset($_POST['sub_of']) || !is_numeric($_POST['sub_of'])) {
			$error['sub'] = $langArray['error_fill_this_field'];
		}
		
		if(isset($error)) {
			return $error;
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
			UPDATE `categories` 
			SET	`sub_of` = '".intval($_POST['sub_of'])."',
					`meta_title` = '".sql_quote($_POST['meta_title'])."',
					`meta_keywords` = '".sql_quote($_POST['meta_keywords'])."',
					`meta_description` = '".sql_quote($_POST['meta_description'])."',
					`name` = '".sql_quote($_POST['name'])."',
					`text` = '".sql_quote($_POST['text'])."',
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
		
		$info = $this->get($id);
		
		$this->orderWhere = " AND `sub_of` = '".intval($info['sub_of'])."' ";
		$orderIndex = $this->getNextOrderIndex();
		
		$mysql->query("
			UPDATE `categories`
			SET `sub_of` = '0',
					`order_index` = `order_index` + '".intval($orderIndex)."'
			WHERE `sub_of` = '".intval($id)."'
		");
		
		$mysql->query("
			DELETE FROM `categories`
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
		
		return true;
	}
	
	
	public function getAllWithChilds($id=0, $where='') {
		global $mysql;
		
		$whereQuery = '';
		if($id != 0) {
			$whereQuery = " WHERE `id` <> '".intval($id)."' ";
		}
		elseif($where!='') {
			$whereQuery = " WHERE ".$where;
		}
		
		$mysql->query("
			SELECT *
			FROM `categories`
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
				if(is_array($selected)) { 
					if(in_array($v['id'], $selected)) {
						$text .= ' selected="selected" ';
					}
				} else {
					if($v['id'] == $selected) {
						$text .= ' selected="selected" ';
					}
				}
				$text .= '>';
				
				if($depth > 0) {
					for($i=0; $i<$depth; $i++) {
						$text .= '&nbsp;&nbsp;';
					}
				}
				
				$text .= ' - '.$v['name'].'</option>';
				$text .= $this->generateSelect($array, $selected, $v['id'], $depth+1);
			}
		
		}
		
		return $text;
	}
	
	public function generateList($array, $subOf=0, $depth=0) {
		global $languageURL;
		
		$text = '';
		
		if(isset($array[$subOf])) {
		
			if($depth > 0) {
				$text .= '<ul class="category-tree" style="float: left; width: 220px;">';
			}
			
			foreach($array[$subOf] as $v) {
				if($depth == 0) {
					$text .= '<ul class="category-tree" style="float: left; width: 220px;">';
				}
			
				$text .= '<li><a href="/'.$languageURL.'categories/'.$v['id'].'">'.$v['name'].'</a>';
				$text .= $this->generateList($array, $v['id'], $depth+1);
				$text .= '</li>';

				if($depth == 0) {
					$text .= '</ul>';
				}
			}
			
			if($depth > 0) {
				$text .= '</ul>';
			}
		}
		
		return $text;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function generatebrowseList($array, $subOf=0, $depth=0) {
		global $languageURL;
		
		$text = '';
		
		if(isset($array[$subOf])) {
		
			if($depth > 0) {
				$text .= '<ul>';
			}
			
			foreach($array[$subOf] as $v) {
				if($depth == 0) {
					
				}
			
				$text .= '<li><a href="/'.$languageURL.'categories/'.$v['id'].'">'.$v['name'].'</a>';
				$text .= $this->generatebrowseList($array, $v['id'], $depth+1);
				$text .= '</li>';

				if($depth == 0) {
					
				}
			}
			
			if($depth > 0) {
				$text .= '</ul>';
			}
		}
		
		return $text;
	}
	
	public function generateList2($array, $selected=array(), $subOf=0, $depth=0) {
		global $languageURL;
		
		$selectID = array_shift($selected);
		
		$text = '';
		
		if(isset($array[$subOf])) {
		
			if($depth > 0) {
				$text .= '<ol>';
			}
			
			foreach($array[$subOf] as $v) {
				if($selectID == $v['id']) {
					$text .= '<li class="active"><a href="/'.$languageURL.'categories/'.$v['id'].'"><span>'.$v['name'].'</span></a>';
					$text .= $this->generateList2($array, $selected, $v['id'], $depth+1);
				}else{
					$text .= '<li><a href="/'.$languageURL.'categories/'.$v['id'].'"><span>'.$v['name'].'</span></a>';
				}
				$text .= '</li>';
			}
			
			if($depth > 0) {
				$text .= '</ol>';
			}
		}
		
		return $text;
	}

	public function getCategoryParents($categories, $categoryID) {
		
		$return = ''; 
		if(isset($categories[$categoryID])) {
			$return .= $categoryID.',';
			$return .= $this->getCategoryParents($categories, $categories[$categoryID]['sub_of']);
		}
		
		return $return;
		
	}
	
}

?>