<?php

class service extends base {
	
	function __construct() {
		global $config;
		
	}
    
    //获得客服人员
	public function getAll($start=0, $limit=0, $where='', $order='`time` DESC') {
		global $mysql;
		
		$limitQuery = "";
		if($limit!=0) {
			$limitQuery = "LIMIT $start,$limit";
		}
		
		if($where != '') {
			$where = " WHERE ".$where;
		}
		if($_POST['q'] != '') {
			$q = $_POST['q'];
			$where = "WHERE `user_name`='".$q."'";
		}

		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `service`
			$where
			ORDER BY $order
			$limitQuery
		");
            if($mysql->num_rows() == 0) {
                return false;
            }

            $return = array();
            while($d = $mysql->fetch_array()) {
                $return[] = $d;
            }
            $this->foundRows = $mysql->getFoundRows();
            return $return;
	}

	//添加客服人员
	public  function add(){
		global $mysql;
		$mysql->query("
			INSERT INTO `service` (
				`user_name`,
				`email`,
				`info`,
				`time`			
			)
			VALUES (
				'".sql_quote($_POST['username'])."',
				'".sql_quote($_POST['email'])."',
				'".sql_quote($_POST['info'])."',
				'".intval(time())."'
			)
		", __FUNCTION__);
		return true;
	}

	//获取单个客服信息
	public  function get($id = 0){
		global $mysql;
		
		if($id != 0) {
			$where = " WHERE id=".$id;
		}else{
			return false;
		}
		
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `service`
			$where
		");

		if($mysql->num_rows() == 0) {
			return false;
		}
		return $mysql->fetch_array();
	}
    
    //编辑客服
	public function edit($id) {
		global $mysql, $langArray;

		if(!isset($_POST['username']) || strlen(trim($_POST['username'])) < 1) {
			$error['username'] = $langArray['error_fill_this_field'];
		}
		
		if(!isset($_POST['email']) || strlen(trim($_POST['email'])) < 1) {
			$error['email'] = $langArray['error_fill_this_field'];
		}
		
		if(isset($error)) {
			return $error;
		}
		if($_POST['status']=='on'){
			$status = 'true';
		}else{
			$status = 'false';
		}
		
		$mysql->query("
			UPDATE `service` 
			SET `user_name` = '".sql_quote($_POST['username'])."',
				`email` = '".sql_quote($_POST['email'])."',
				`info` = '".sql_quote($_POST['info'])."',
				`status` = '".sql_quote($status)."'
			WHERE `id` = '".intval($id)."'
		", __FUNCTION__ );
		
		return true;
	}

	public function getAllfromuser($start=0, $limit=0,$id=0){
		global $mysql;
		$limitQuery = "";
		if($limit!=0) {
			$limitQuery = "LIMIT $start,$limit";
		}
        if($id != 0) {
			$where = " WHERE service_user_id=".$id;
		}else{
			return false;
		}
		
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `service_relation`
			$where
			
		");
		if($mysql->num_rows() == 0) {
			return false;
		}
		$arr = array();
		while($d = $mysql->fetch_array()) {
			$arr[] = $d['user_id'];
		}
		
		$str = implode(',',$arr);
		if(!empty($arr)){
			$where_01 = 'WHERE user_id in('.$str.')';
			if($_POST['q'] != '') {
				$q = $_POST['q'];
				$where_01 .= "AND nickname='".$q."'";
			}
			$mysql->query("
				SELECT SQL_CALC_FOUND_ROWS *
				FROM `users`
				$where_01
				$limitQuery
			");
		}
		$return = array();
		while($i = $mysql->fetch_array()) {
			$return[] = $i;
		}
		$this->foundRows = $mysql->getFoundRows();
		return $return;
		
	}

	//通过用户Id获取关联客服
	public  function getserviceByuserid($id = 0){
		global $mysql;
		
		if($id != 0) {
			$where = " WHERE user_id=".$id;
		}else{
			return false;
		}
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `service_relation`
			$where
		");

		if($mysql->num_rows() == 0) {
			return false;
		}

		$user = $mysql->fetch_array();
		
        $where_00 = " WHERE id=".$user['service_user_id'];
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `service`
			$where_00
		");
		return $mysql->fetch_array();
	}
    //判断是否安装
    public function is_setup(){
        global $mysql;

        $mysql->query("
			show tables like 'service_relation'
		");
        return $mysql->fetch_array();
    }
    //创建表
    public function setup(){

        mysql_query("
		CREATE TABLE IF NOT EXISTS `service` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `user_name` varchar(20) NOT NULL COMMENT '客服姓名',
		  `email` varchar(30) DEFAULT NULL COMMENT '邮件',
		  `info` varchar(255) DEFAULT NULL COMMENT '备注',
		  `status` enum('true','false') DEFAULT 'true' COMMENT '状态',
		  `time` int(11) DEFAULT NULL COMMENT '时间',
		  `service_num` int(11) DEFAULT NULL,
		  `service_status` tinyint(1) DEFAULT '1' COMMENT '服务状态（0：该轮已服务，1：该轮未服务）',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
	");

        mysql_query("
		CREATE TABLE IF NOT EXISTS `service_relation` (
		`user_id` int(11) NOT NULL COMMENT '用户id',
		`service_user_id` int(11) NOT NULL COMMENT '客服id',
		PRIMARY KEY (`user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客服用户关联表';
	");
            return true;
    }
}
?>