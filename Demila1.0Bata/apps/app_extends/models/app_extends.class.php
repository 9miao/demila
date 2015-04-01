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


class app_extends extends base {
	
	function __construct() {
		$this->tableName = 'app_extends';
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
			FROM `app_extends`
			$where
			ORDER BY `id` ASC
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

    //查询开启状态

    public function getStatus($extend_name){
        global $mysql;
        $mysql->query("
			SELECT `state`
			FROM	`app_extends`
			WHERE `extend_name` = '$extend_name'
		", __FUNCTION__ );

        $result=$mysql->fetch_array();
        return $result["state"];
    }

	/*
	 * 编辑开启状态
	 */
	public function edit($id,$state) {
		global $mysql;
		
        if($state==1){
            $mysql->query("
			UPDATE `app_extends`
			SET	`state` = 0

			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
        }else{
            $mysql->query("
			UPDATE `app_extends`
			SET	`state` = 1
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
        }
		return true;
	}
    /*
     * 卸载应用
     */
    public function unload($extend_name,$dir_name='',$table_name="") {
        global $mysql;
            $mysql->query("
			DELETE FROM `app_extends`
			WHERE `extend_name` = '".$extend_name."'
		", __FUNCTION__ );

        if(!empty($table_name)){
            $mysql->query("
			DROP TABLE `".$table_name."`
		", __FUNCTION__ );
        }
        $dir= $_SERVER['DOCUMENT_ROOT'].'/apps/'.$dir_name;
        deldir($dir);
        return true;
    }

    //返回客户启动状态
    public function is_service(){
        $have_service=false;
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/apps/service/controllers/index.php')) {
            $is_open=$this->getStatus("客户服务管理");
            if($is_open){
                $have_service= true;
            }
        }
        return $have_service;
    }

    //返回截图包启动状态
    public function is_uploads(){
        $have_service=false;
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/apps/uploads_extends/controllers/index.php')) {
            $is_open=$this->getStatus("截图包上传");
            if($is_open){
                $have_service= true;
            }
        }
        return $have_service;
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



}

?>