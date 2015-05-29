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

class tags extends base {
	
	function __construct() {
	
	}

	
	/*
	 * 获取函数
	 */
	public function getAll($start=0, $limit=0, $where='') {
		global $mysql;
		
		$limitQuery = "";
		if($limit!=0) {
			$limitQuery = "LIMIT $start,$limit";
		}
		
		$whereQuery = '';
		if($where!='') {
			$whereQuery = " WHERE ".$where;
		}
		
		$return = $mysql->getAll("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `tags`
			$whereQuery
			ORDER BY `name` ASC
			$limitQuery
		");
			
		$this->foundRows = $mysql->getFoundRows();
		
		return $return;
	}
	
	public function get($id) {
		global $mysql;
		
		$return = $mysql->getRow("
			SELECT *
			FROM `tags`
			WHERE `id` = '".intval($id)."'
		");
		
		return $return;
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
		
		$mysql->query("
			INSERT INTO `tags` (
				`name`
			)
			VALUES (
				'".sql_quote($_POST['name'])."'
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
			UPDATE `tags` 
			SET `name` = '".sql_quote($_POST['name'])."'
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
			DELETE FROM `tags`
			WHERE `id` = '".intval($id)."'
			LIMIT 1
		", __FUNCTION__ );
		
		return true;
	}

    //通过作品ID删除已绑定的作品推荐标签
    public function del_by_item_id($item_id){
        global $mysql;

        $mysql->query("
			DELETE FROM `items_tags`
			WHERE `item_id` = '".intval($item_id)."'
		", __FUNCTION__ );

        return true;
    }
	
	public function isExistTag($tag) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `tags`
			WHERE `name` = '".sql_quote($tag)."'
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return $mysql->fetch_array();
	}
	
	public function getTagID($tag) {
		global $mysql;
		
		$row = $this->isExistTag($tag);
		if(is_array($row)) {
			return $row['id'];
		}
		
		$_POST['name'] = $tag;
		$this->add();
		
		return $mysql->insert_id();
	}

    //通过作品ID获取关联标签
    public function get_tags_by_item_id($item_id = 0){
        global $mysql;

        $return = $mysql->getAll("
			SELECT
			tags.id as id, tags.name as name, items_tags.item_id as item_id
			FROM `items_tags`
			JOIN `tags`
			ON `tags`.`id` = `items_tags`.`tag_id`
			WHERE `item_id` = '".$item_id."';
		");
        return $return;
    }

    //添加标签
    public function add_tags($data){
        global $mysql;
        $mysql->query("
			INSERT INTO `items_tags` (
				`item_id`,
				`tag_id`
			)
			VALUES (
				'".intval($data['item_id'])."',
				'".intval($data['tag_id'])."'
			)
		");
        return true;
    }



}
?>