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

class system extends base {
	
	function __construct() {
		$this->uploadFileDirectory = 'logo/';
	}

	
	/*
	 * 获取函数
	 */
	public function getLogo() {
		global $mysql;
		
		$return = $mysql->getRow("
			SELECT *
			FROM `system`
			WHERE `key` = 'site_logo'
		");
		
		return $return;
	}

	
	//获取用户配置信息
	public function getAllKeyValue() {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `system`
			ORDER BY `id` ASC
		", __FUNCTION__ );

		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		while($d = $mysql->fetch_array()) {
			$return[$d['key']] = $d['value'];
		}

		return $return;
	}

    public function getAll($start=0, $limit=0, $group = null,$key= null) {
        global $mysql;

        $limitQuery = "";
        if($limit!=0) {
            $limitQuery = "LIMIT $start,$limit";
        }

        $where = "WHERE id <> 0";
        if($group) {
            $where .= " and `group` = '" . sql_quote($group) . "'";
        }
        if($key) {
            $where .= " and `key` = '" . sql_quote($key) . "'";
        }
		$return = $mysql->getAll("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `system`
			$where
			ORDER BY `id` ASC
			$limitQuery
		");	
			
		$this->foundRows = $mysql->getFoundRows();
		
		return $return;
	}
	
	public function get($id) {
		global $mysql;
		
		$return = $mysql->getRow("
			SELECT *
			FROM `system`
			WHERE `id` = '".intval($id)."'
		");
		
		return $return;
	}
	
	
	/*
	 * 添加
	 */
	public function add() {
		global $mysql, $langArray;

		if(!isset($_POST['key']) || strlen(trim($_POST['key'])) < 1) {
			$error['key'] = $langArray['error_fill_this_field'];
		}
		
		if(!isset($_POST['value']) || strlen(trim($_POST['value'])) < 1) {
			$error['value'] = $langArray['error_fill_this_field'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		$mysql->query("
			INSERT INTO `system` (
				`key`,
				`value`
			)
			VALUES (
				'".sql_quote($_POST['key'])."',
				'".sql_quote($_POST['value'])."'
			)
		", __FUNCTION__ );
		
		return true;
	}

    /*
     * 添加删除
     * */

    public function editStatus($type=""){
        global $mysql;
        if($type=="Alipay"){
            $mysql->query("
			DELETE FROM `system` WHERE `key` = 'use_demilapay'

			", __FUNCTION__ );
        }
        else{
            $mysql->query("
			INSERT INTO `system` (
				`key`,
				`value`
			)
			VALUES (
				'use_demilapay',
				'1'
			)
		", __FUNCTION__ );
        }
        return true;
    }
	/*
	 * 编辑
	 */
	
	public function editLogo() {
		global $mysql, $langArray;
		
		$this->uploadFileDirectory = 'logo/';
		
		$photo = $this->upload('value', '', false);
		if(substr($photo, 0, 6) == 'error_') {
			$error['photo'] = $langArray[$photo];
		}
		
		if(isset($error)) {
			return $error;
		}
	
		if($photo != '') {

			$post = $this->getLogo();
			if($post) {
				$this->deletePhoto($post['id']);
				$this->delete($post['id']);
			}
			
			$mysql->query("
				INSERT INTO `system` (
					`key`,
					`value`
				)
				VALUES (
					'site_logo',
					'".sql_quote($photo)."'
				)
			", __FUNCTION__ );

		}
		
		return true;
	}
	
	private function deletePhoto($id) {
		global $mysql, $config;
		
		$post = $this->get($id);
		if($post && $post['value'] != '') {
			@unlink(DATA_SERVER_PATH.'uploads/'.$this->uploadFileDirectory.$post['value']);
		}
		
		return true;
	}
	
	public function edit($id) {
		global $mysql, $langArray;

		if(!isset($_POST['key']) || strlen(trim($_POST['key'])) < 1) {
			$error['key'] = $langArray['error_fill_this_field'];
		}
		
		if(!isset($_POST['value']) || strlen(trim($_POST['value'])) < 1) {
			$error['value'] = $langArray['error_fill_this_field'];
		}
		
		if(isset($error)) {
			return $error;
		}
				
		$mysql->query("
			UPDATE `system` 
			SET `key` = '".sql_quote($_POST['key'])."',
					`value` = '".sql_quote($_POST['value'])."'
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
		
		return true;
	}
	
	public function editGroup($group, $data = array()) {
		global $mysql, $meta;
		
		$this->uploadFileDirectory = $_GET['m'] . '/';
		
		$photo = $this->upload('photo', '', false);
		
		$return = $mysql->getRow("
			SELECT *
			FROM `system`
			WHERE `key` = '" . $group . '_logo' . "'
		");
		
		if($photo != '') {
			if(substr($photo, 0, 6) != 'error_' && $return['id']) {
				$this->deletePhoto($return['id']);
				$this->delete($return['id']);
			}
			if(substr($photo, 0, 6) != 'error_') {
				$data[$group . '_logo'] = $photo;
			}
		} elseif ( isset($meta[$group . '_logo']) ) {
			$data[$group . '_logo'] = $meta[$group . '_logo'];
		}
		
		$mysql->query("
			DELETE FROM `system`
			WHERE `group` = '".sql_quote($group)."'
		", __FUNCTION__ );
		
		foreach($data AS $key => $value) {
			
			$mysql->query("
				INSERT INTO `system` (
					`key`,
					`value`,
					`group`
				)
				VALUES (
					'".sql_quote($key)."',
					'".sql_quote($value)."',
					'".sql_quote($group)."'
				)
			", __FUNCTION__ );
			
		}
	}
	
	/*
	 * 删除
	 */
	public function delete($id) {
		global $mysql;
		
		$mysql->query("
			DELETE FROM `system`
			WHERE `id` = '".intval($id)."' AND system != 1
			LIMIT 1
		", __FUNCTION__ );
		
		return true;
	}
	
	
	
/* 货币 */	
	
	public function getCurrency() {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `currency`
			ORDER BY `name` ASC
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
	
	public function getActiveCurrency() {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `currency`
			WHERE `active` = 'yes'
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return $mysql->fetch_array();
	}
	
	public function saveCurrency() {
		global $mysql;
		
		if(!isset($_POST['code'])) {
			$_POST['code'] = '';
		}
		
		$mysql->query("
			UPDATE `currency`
			SET `active` = 'no'
		");
		
		$mysql->query("
			UPDATE `currency`
			SET `active` = 'yes'
			WHERE `code` = '".sql_quote($_POST['code'])."'
		");
		
		return true;
	}

    /*SMTP*/
    public function smtp_edit($data){
         global $mysql;
         if(isset($data["smtp_host"]) && !empty($data["smtp_host"])){
             $mysql->query("
			UPDATE `system`
			SET `value` = '".sql_quote($data["smtp_host"])."'
			WHERE `key` = 'smtp_host'
		    ", __FUNCTION__ );
         }
         if(isset($data["smtp_from"]) && !empty($data["smtp_from"])){
             $mysql->query("
			UPDATE `system`
			SET `value` = '".sql_quote($data["smtp_from"])."'
			WHERE `key` = 'smtp_from'
		    ", __FUNCTION__ );
         }
         if(isset($data["smtp_port"]) && !empty($data["smtp_port"])){
             $mysql->query("
			UPDATE `system`
			SET `value` = '".sql_quote($data["smtp_port"])."'
			WHERE `key` = 'smtp_port'
		    ", __FUNCTION__ );
         }
         if(isset($data["smtp_user"]) && !empty($data["smtp_user"])){
             $mysql->query("
			UPDATE `system`
			SET `value` = '".sql_quote($data["smtp_user"])."'
			WHERE `key` = 'smtp_user'
		    ", __FUNCTION__ );
         }
         if(isset($data["smtp_pass"]) && !empty($data["smtp_pass"])){
             $mysql->query("
			UPDATE `system`
			SET `value` = '".sql_quote($data["smtp_pass"])."'
			WHERE `key` = 'smtp_pass'
		    ", __FUNCTION__ );
         }
         if(isset($data["smtp_from_name"]) && !empty($data["smtp_from_name"])){
             $mysql->query("
			UPDATE `system`
			SET `value` = '".sql_quote($data["smtp_from_name"])."'
			WHERE `key` = 'smtp_from_name'
		    ", __FUNCTION__ );
         }
         return true;
     }

    public function is_smtp(){
        global $mysql;

        $return=$mysql->getAll("
        SELECT 'id' FROM `system` WHERE `key` = 'use_smtp'
        ");

        return $return;
    }
    public function use_smtp(){
        global $mysql;
        $mysql->query("
			INSERT INTO `system` (
				`key`,
				`value`
			)
			VALUES (
				'use_smtp',
				'1'
			)
		", __FUNCTION__ );

        return true;
    }
    public function unuse_smtp(){
        global $mysql;

        $mysql->query("
			DELETE FROM `system` WHERE `key` = 'use_smtp'
			", __FUNCTION__ );

        return true;
    }

    //模板设置
    public function edit_template($val = ""){
        global $mysql;
        $mysql->query("
			UPDATE `system` set `value` = '".sql_quote($val)."'
			WHERE `key` = 'template'
			", __FUNCTION__ );
        return true;
    }

    //后台模板设置
    public function edit_admin_template($val = ""){
        global $mysql;
        $mysql->query("
			UPDATE `system` set `value` = '".sql_quote($val)."'
			WHERE `key` = 'admin_template'
			", __FUNCTION__ );

        return true;
    }



    }
?>