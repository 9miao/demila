<?php

class uploads_extends extends base {
	
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


    //判断是否安装
    public function is_setup(){
        global $mysql;
        $mysql->query("
			select * from app_extends WHERE extend_name ='截图包上传'
		");
        return $mysql->fetch_array();
    }
    //创建表
    public function setup(){
        mysql_query("insert into `app_extends`(`extend_name`,`state`,`m`,`c`)  VALUES  ('截图包上传',1,'uploads_extends','uploads_extends');");

        mysql_query("
		 alter table temp_items change theme_preview theme_preview TEXT;
	    ");

        mysql_query("
		 alter table temp_items ADD first_preview VARCHAR(255);
	    ");
        mysql_query("
		 CREATE TABLE IF NOT EXISTS upload_queue
        (
              id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
              item_id INT NOT NULL,
              dir VARCHAR(255) NOT NULL,
              type TINYINT NOT NULL,
              queue_type TINYINT NOT NULL,
              `key` VARCHAR(255) DEFAULT 'wait',
              user_id INT DEFAULT 0
            );
	    ");

        mysql_query("
		CREATE TABLE IF NOT EXISTS `preview` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `item_id` int(11) NOT NULL,
		  `dir` varchar(255) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
	");

            return true;
    }
}
?>