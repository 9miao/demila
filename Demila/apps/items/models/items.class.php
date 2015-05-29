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


class items {
	
	public $uploadFileDirectory = '';
	public $foundRows = 0;
	public $attributesWhere = '';
	public $attributeCategoriesWhere = '';
	public $usersWhere = '';
	
	public function __construct() {
		$this->uploadFileDirectory = 'items/';
	}
	
	//获取作品详情及分类信息
	public function getAll($start=0, $limit=0, $where='', $order='`datetime` ASC') {
		global $mysql;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		if($where!='') {
			$where = " WHERE ".$where;
		}
		
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *,
			(SELECT GROUP_CONCAT(`categories` SEPARATOR '|') FROM `items_to_category` WHERE `item_id` = `items`.`id`) AS `categories`
			FROM `items`
			$where
			ORDER BY $order
			$limitQuery
		");

		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$this->usersWhere = '';
		$return = array();
		while($d = $mysql->fetch_array()) {
			$categories = explode('|', $d['categories']);
			unset($d['categories']);
			$d['categories'] = array();
			$row=0;
			foreach($categories AS $cat) {
				$categories1 = explode(',', $cat);
				foreach($categories1 as $c) {
					$c = trim($c);
					if($c != '') {
						$d['categories'][$row][$c] = $c;
					}
				}
				$row++;
			}
			$return[$d['id']] = $d;
			
			if($this->usersWhere != '') {
				$this->usersWhere .= ' OR ';
			}
			$this->usersWhere .= " `user_id` = '".intval($d['user_id'])."' ";
		}
		
		$this->foundRows = $mysql->getFoundRows();
		
		return $return;
		
	}
	
	public function getAllForUpdate($start=0, $limit=0, $where='', $order='`datetime` ASC') {
		global $mysql;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		if($where!='') {
			$where = " WHERE ".$where;
		}
		
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `temp_items`
			$where
			ORDER BY $order
			$limitQuery
		");
			
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$whereQuery = '';
		$return = array();
		while($d = $mysql->fetch_array()) {
			$return[$d['id']] = $d;
		}
		
		$this->foundRows = $mysql->getFoundRows();
		
		return $return;
		
	}
	
	//通过作品id获取作品详情
	public function get($id, $active = false) {
		global $mysql, $meta;
		
		//预付折扣
		$percents = 0;
		if(isset($meta['prepaid_price_discount'])) {
			$percents = $meta['prepaid_price_discount'];
		}
		//扩展许可价格
		$extended_price = 1;
		if(isset($meta['extended_price'])) {
			$extended_price = (int)$meta['extended_price'];
		}
		//获取作品详情
		$sql = "
			SELECT *
			FROM `items`
			WHERE `id` = '".intval($id)."'
		";
		
		if($active) {
			$sql .= " AND `status` = 'active'";
		}
		
		$mysql->query($sql);
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = $mysql->fetch_array();
		if(strpos($percents, '%') !== false) {
			$return['prepaid_price'] = $return['price'] - ( ( $return['price'] / 100 ) * (int)$percents );
			$return['your_profit'] = (int)( ( $return['price'] / 100 ) * (int)$percents );
		} else {
			$return['prepaid_price'] = $return['price'] - (int)$percents;
			$return['your_profit'] = (int)$percents;
		}
		//优惠金额
		$return['extended_price'] = $return['price']*$extended_price;
		
		//作品所属分类
		$mysql->query("
					SELECT 
						* 
					FROM 
						`items_to_category` 
					WHERE 
						`item_id` = '".intval($id)."'
				");
		
		$return['categories'] = array();
		if($mysql->num_rows() > 0) {
			$row=0;
			while($ca = $mysql->fetch_array()) {
				$categories = explode(',', $ca['categories']);
				foreach($categories as $c) {
					$c = trim($c);
					if($c != '') {
						$return['categories'][$row][$c] = $c;
					}
				}
				$row++;
			}
		}
		
        #加载标签
		$mysql->query("
			SELECT *
			FROM `items_tags` AS it
			JOIN `tags` AS t
			ON t.`id` = it.`tag_id`
			WHERE it.`item_id` = '".intval($id)."'			
		");	
		
		if($mysql->num_rows() > 0) {
			while($d = $mysql->fetch_array()) {
				$return['tags'][$d['type']][$d['tag_id']] = $d['name'];
			}
		}
		
        #加载属性
		$mysql->query("
			SELECT *
			FROM `items_attributes`
			WHERE `item_id` = '".intval($id)."'			
		");		
				
		if($mysql->num_rows() > 0) {
			$res_attributes = array();
			while($d = $mysql->fetch_array()) {

				$res_attributes[] = $d;
			}
			$return['attributes'] = array();
			foreach($res_attributes as $icpe){
				$return['attributes'][$icpe['category_id']][$icpe['attribute_id']] = $icpe['attribute_id'];
			}
		}
		
		return $return;
	}
	
	public function getForUpdate($id) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `temp_items`
			WHERE `id` = '".intval($id)."'
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = $mysql->fetch_array();
		
#加载标签
		$mysql->query("
			SELECT *
			FROM `temp_items_tags` AS it
			JOIN `tags` AS t
			ON t.`id` = it.`tag_id`
			WHERE it.`item_id` = '".intval($return['item_id'])."'			
		");	
		
		if($mysql->num_rows() > 0) {
			while($d = $mysql->fetch_array()) {
				$return['tags'][$d['type']][$d['tag_id']] = $d['name'];
			}
		}
		
		return $return;
	}
	
	/*
	 * 添加
	 */
	public function add() {
            global $mysql, $langArray, $attributes;
            $user_id = $_SESSION['user']['user_id'];
            if(!$user_id){
                return false;
            }


        //临时文件目录
             $temporary_dir=    DATA_SERVER_PATH.'/uploads/temporary/'.$user_id.'/';

            if(!isset($_POST['name']) || trim($_POST['name']) == '') {
                $error['name'] = $langArray['error_not_set_name'];
            }

            if(!isset($_POST['description']) || trim($_POST['description']) == '') {
                $error['description'] = $langArray['error_not_set_description'];
            }

            //（缩略图）
            if(!isset($_POST['thumbnail']) || trim($_POST['thumbnail']) == '') {
                $error['thumbnail'] = $langArray['error_not_set_thumbnail'];
            }else {
                $file = pathinfo($_POST['thumbnail']);
                if(!in_array(strtolower($file['extension']),$this->support_format(1))) {
                    $error['thumbnail'] = $langArray['error_thumbnail_jpg'];
                }
                elseif(!file_exists($temporary_dir.$_POST['thumbnail'])) {
                    $error['thumbnail'] = $langArray['error_thumbnail_jpg'];
                }
            }

            // (预览图)
            $theme_preview_arr = explode(',', $_POST['theme_preview']);
            if(!isset($theme_preview_arr) || !is_array($theme_preview_arr)) {
                $error['theme_preview'] = $langArray['error_not_set_theme_preview'];
            }else{
                foreach($theme_preview_arr as $theme_f){
                    $theme_file = pathinfo($theme_f);
                    if(!in_array(strtolower($theme_file['extension']),$this->support_format(0))) {
                        $error['theme_preview'] = $langArray['error_theme_preview_jpg'];
                    }elseif(!file_exists($temporary_dir.$theme_f)){
                        $error['theme_preview'] = $langArray['error_not_file'];
                    }
                }
            }

            // (封面)

            if(!isset($_POST['first_preview']) || trim($_POST['first_preview']) == '') {
                $error['first_preview'] = $langArray['error_not_set_first_preview'];
            }else {
                $file = pathinfo($_POST['first_preview']);
                if(!in_array(strtolower($file['extension']),$this->support_format(0))) {
                    $error['first_preview'] = $langArray['error_theme_preview_jpg'];
                }elseif(!file_exists($temporary_dir.$_POST['first_preview'])) {
                    $error['first_preview'] = $langArray['error_not_file'];
                }
            }

            // （主程序包）
            if(!isset($_POST['main_file']) || trim($_POST['main_file']) == '') {
                $error['main_file'] = $langArray['error_not_set_main_file'];
            }else {
                $file = pathinfo($_POST['main_file']);
                if(strtolower($file['extension']) != 'zip') {
                    $error['main_file'] = $langArray['error_main_file_zip'];
                }
                elseif(!file_exists($temporary_dir .$_POST['main_file'])) {
                    $error['main_file'] = $langArray['error_main_file_zip'];
                }
            }

            if(!isset($_POST['category'])) {
                $error['category'] = $langArray['error_not_set_category'];
            } elseif(!is_array($_POST['category'])) {
                $error['category'] = $langArray['error_not_set_category'];
            } elseif(!count($_POST['category'])) {
                $error['category'] = $langArray['error_not_set_category'];
            }

            if(is_array($attributes)) {
                $attributesError = false;
                foreach($attributes as $a) {
                    if(!isset($_POST['attributes'][$a['id']])) {
                        $attributesError = true;
                        break;
                    }
                }

                if($attributesError) {
                    $error['attributes'] = $langArray['error_set_all_attributes'];
                }
            }

            //标签
            if(!isset($_POST['tags']) || empty($_POST['tags'])) {
                $error['tags_features'] = $langArray['error_not_set_tags'];
            }else{
                $tags_data = explode(',', $_POST['tags']);
                if(count($tags_data) > 6){
                    $error['tags_features'] = $langArray['error_not_set_tags_tomore'];
                }
            }


            if(!isset($_POST['source_license'])) {
                $error['source_license'] = $langArray['error_not_set_source_license'];
            }

            if(isset($_POST['demo_url']) && trim($_POST['demo_url']) && filter_var($_POST['demo_url'], FILTER_VALIDATE_URL) === false) {
                $error['demo_url'] = $langArray['error_demo_url'];
            }

            if($_POST['suggested_price'] && !preg_match('#^\d+(?:\.\d{1,})?$#', $_POST['suggested_price'])) {
                $error['suggested_price'] = $langArray['error_suggested_price'];
            }

            if(isset($error)) {
                return $error;
            }

            if(!isset($_POST['demo_url'])) {
                $_POST['demo_url'] = '';
            }

            if(!isset($_POST['comments_to_reviewer'])) {
                $_POST['comments_to_reviewer'] = '';
            }

            if(!isset($_POST['free_request'])) {
                $_POST['free_request'] = 'true';
            }
            require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
            $categoriesClass = new categories();

            $mysql->query("
			INSERT INTO `items` (
				`user_id`,
				`name`,
				`description`,
				`main_file_name`,
                `thumbnail`,
				`theme_preview`,
				`main_file`,
				`demo_url`,
				`reviewer_comment`,
				`datetime`,
				`status`,
				`suggested_price`,
				`free_request`
			)
			VALUES (
				'".intval($_SESSION['user']['user_id'])."',
				'".sql_quote($_POST['name'])."',
				'".sql_quote($_POST['description'])."',
				'".sql_quote($_SESSION['temp']['uploaded_files'][$_POST['main_file']]['name'])."',
                '".sql_quote($_POST['thumbnail'])."',
				'".sql_quote($_POST['first_preview'])."',
				'".sql_quote($_POST['main_file'])."',
				'".sql_quote($_POST['demo_url'])."',
				'".sql_quote($_POST['comments_to_reviewer'])."',
				NOW(),
                'queue',
				'".(float)$_POST['suggested_price']."',
				'".sql_quote($_POST['free_request'])."'
			)
		");

            $itemID = $mysql->insert_id();

            $allCategories = $categoriesClass->getAll();
            if(is_array($_POST['category'])) {
                foreach($_POST['category'] AS $category_id) {
                    $categories = $categoriesClass->getCategoryParents($allCategories, $category_id);
                    $categories = explode(',', $categories);
                    array_pop($categories);
                    $categories = array_reverse($categories);
                    $categories = ','.implode(',', $categories).',';
                    $mysql->query("
					INSERT INTO `items_to_category` (
						`item_id`,
						`categories`
					)
					VALUES (
						'".sql_quote($itemID)."',
						'".sql_quote($categories)."'
					)
				");
                }
            } else {
                $categories = $categoriesClass->getCategoryParents($allCategories, $_POST['category']);
                $categories = explode(',', $categories);
                array_pop($categories);
                $categories = array_reverse($categories);
                $categories = ','.implode(',', $categories).',';
                $mysql->query("
                    INSERT INTO `items_to_category` (
                        `item_id`,
                        `categories`
                    )
                    VALUES (
                        '".sql_quote($itemID)."',
                        '".sql_quote($categories)."'
                    )
                ");
            }

            #创建文件夹
            recursive_mkdir(DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$itemID.'/');
            recursive_mkdir(DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$itemID.'/preview/');

            #剪裁缩略图并创建预览图
            require_once ENGINE_PATH.'/classes/image.class.php';
            $imageClass = new Image();
            $imageClass->crop($temporary_dir.$_POST['thumbnail'], 90, 90);
            copy($temporary_dir.$_POST['thumbnail'], DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$itemID.'/'.$_POST['thumbnail']);
            @unlink($temporary_dir.$_POST['thumbnail']);
            $file_first  = pathinfo($_POST['first_preview']);
            if(in_array(strtolower($file_first['extension']),$this->support_format(1))){
                //第一张预览图地址
                copy($temporary_dir.$_POST['first_preview'], DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$itemID.'/'.$_POST['first_preview']);
                @unlink($temporary_dir.$_POST['first_preview']);
                $imageClass->crop(DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$itemID.'/'.$_POST['first_preview'], 590, 300);
            }elseif(in_array(strtolower($file_first['extension']),$this->support_format(2))){
                copy($temporary_dir.$_POST['first_preview'], DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$itemID.'/'.$_POST['first_preview']);
                @unlink($temporary_dir.$_POST['first_preview']);
            }

            //主程序包路径
            copy($temporary_dir.$_POST['main_file'], DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$itemID.'/'.$_POST['main_file']);
            @unlink($temporary_dir.$_POST['main_file']);
            //预览包
            foreach($theme_preview_arr as $item_dir){
            copy($temporary_dir.$item_dir, DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$itemID.'/preview/'.$item_dir);
            @unlink($temporary_dir.$item_dir);
            #插入预览图
            $this->save_theme_preview($itemID,DATA_SERVER.'/uploads/'.$this->uploadFileDirectory.$itemID.'/preview/'.$item_dir);
            }

            unset($_SESSION['temp']['uploaded_files']);

#插入标签
            require_once ROOT_PATH.'/apps/tags/models/tags.class.php';
            $tags = new tags();
            //删除原绑定标签
            $tags -> del_by_item_id($itemID);
            //重写绑定标签
            $tags_data = explode(',', $_POST['tags']);
            $tag_arr = array();
            foreach($tags_data as $val){
                $tags -> add_tags(array('item_id' => $itemID,'tag_id' => $val));
            }
#插入属性
            $_POST['attributes'] = (array)(isset($_POST['attributes']) ? $_POST['attributes'] : array());
            foreach($_POST['attributes'] as $cID=>$a) {
                if(is_array($a)) {
                    foreach($a as $ai) {
                        $mysql->query("
						INSERT INTO `items_attributes` (
							`item_id`,
							`attribute_id`,
							`category_id`
						)
						VALUES (
							'".intval($itemID)."',
							'".sql_quote($ai)."',
							'".sql_quote($cID)."'
						)
					");
                    }
                }
                else {
                    $mysql->query("
					INSERT INTO `items_attributes` (
						`item_id`,
						`attribute_id`,
						`category_id`
					)
					VALUES (
						'".intval($itemID)."',
						'".sql_quote($a)."',
						'".sql_quote($cID)."'
					)
				");
                }
            }
            return true;
        }

    //通过itemID更新作品
    public function update_item_by_upload_queue($id=0,$setQuery=''){
        global $mysql;

        $mysql->query("
			UPDATE `items`
			SET
			$setQuery
			WHERE `id` = '".intval($id)."'
			LIMIT 1
		");
    }

    //创建预览包
    public function save_theme_preview($item_id,$dir){
        global $mysql;
        $mysql->query("
			INSERT INTO `preview` (
				`item_id`,
				`dir`
			)
			VALUES (
				'".intval($item_id)."',
				'".sql_quote($dir)."'
			)
		");
        return true;
    }

    //通过作品id获取预览图
    public function get_theme_preview($item_id=0){
        global $mysql;

        $mysql->query("
			SELECT *
			FROM `preview`
			WHERE `item_id` = '".intval($item_id)."'
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

    //通过作品id删除作品预览
    public function del_preview($item_id=0){
        global $mysql;

        $mysql->query("
			DELETE FROM `preview`
			WHERE `item_id` = '".intval($item_id)."'
		");
        return true;
    }
    //获取队列作品信息
    public function get_temp_data($id){
        global $mysql;

        $mysql->query("
			SELECT *
			FROM `temp_items`
			WHERE `id` = '".intval($id)."'
		");

        return $mysql->fetch_array();

    }

    //获取队列作品信息
    public function get_temp_data_byitem_id($id){
        global $mysql;

        $mysql->query("
			SELECT *
			FROM `temp_items`
			WHERE `item_id` = '".intval($id)."'
		");

        return $mysql->fetch_array();

    }

    public function edit_upload($id) {
            global $mysql, $langArray, $item;

            $user_id = $_SESSION['user']['user_id'];
            if(!$user_id){
                return false;
            }
            //临时文件目录
            $temporary_dir = DATA_SERVER_PATH.'uploads/temporary/'.$user_id.'/';

            //缩略图
            if(isset($_POST['thumbnail']) && trim($_POST['thumbnail']) != '') {
                $file = pathinfo($_POST['thumbnail']);
                if(!in_array(strtolower($file['extension']),$this->support_format(1))) {
                    $error['thumbnail'] = $langArray['error_thumbnail_jpg'];
                }
            }


            // 预览图
            $theme_preview_arr = explode(',', $_POST['theme_preview']);
            if(!isset($theme_preview_arr) || !is_array($theme_preview_arr)) {
                $error['theme_preview'] = $langArray['error_not_set_theme_preview'];
            }else{
                foreach($theme_preview_arr as $theme_f){
                    $theme_file = pathinfo($theme_f);
                    if(!in_array(strtolower($theme_file['extension']),$this->support_format(0))) {
                        $error['theme_preview'] = $langArray['error_theme_preview_jpg'];
                    }
                }
            }

            // 封面
            if(isset($_POST['first_preview']) && trim($_POST['first_preview']) != '') {
                $file = pathinfo($_POST['first_preview']);
                if(!in_array(strtolower($file['extension']),$this->support_format(0))) {
                    $error['first_preview'] = $langArray['error_theme_preview_jpg'];
                }
            }

            //主程序包
            if(isset($_POST['main_file']) && trim($_POST['main_file']) != '') {
                $file = pathinfo($_POST['main_file']);
                if(strtolower($file['extension']) != 'zip') {
                    $error['main_file'] = $langArray['error_main_file_zip'];
                }
            }

            if(isset($error)) {
                return $error;
            }

            if(!isset($_POST['comments_to_reviewer'])) {
                $_POST['comments_to_reviewer'] = '';
            }

            $colQuery = '';
            $valQuery = '';

            //处理上传文件	-------------------------------------------------------------------------------
            $colQuery = '';
            $valQuery = '';

            //修改次数
            $e = 0;

            //修改次数
            $e = 0;
            //缩略图
            if(isset($_POST['thumbnail']) && trim($_POST['thumbnail']) != '' && file_exists($temporary_dir.$_POST['thumbnail'])) {
                //检测文件是否被修改
                $thumbnail_is = $this->is_edit_item_file($id);
                $path_file_name = pathinfo($thumbnail_is['thumbnail']);
                if($_POST['thumbnail'] != $path_file_name['basename']){
                    require_once ENGINE_PATH.'/classes/image.class.php';
                    $imageClass = new Image();
                    //缩略图
                    $temporary_thumbnail_file = $temporary_dir.$_POST['thumbnail'];
                    //裁剪缩略图
                    $imageClass->crop($temporary_thumbnail_file, 90, 90);
                    copy($temporary_thumbnail_file, DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$id.'/'.$_POST['thumbnail']);
                    @unlink($temporary_thumbnail_file);
                    $colQuery .= " `thumbnail`, ";
                    $valQuery .= " '".$_POST['thumbnail']."', ";
                    $e++;
                    //$colQuery .= " `thumbnail`, ";
                    //$valQuery .= " '".sql_quote($item['thumbnail'])."', ";
                }

            }

            //预览图
            $j = 0;
            $i = 0;
            $edit_theme_arr = array();
            $theme_preview_arr = explode(',', $_POST['theme_preview']);
            if(isset($theme_preview_arr) && is_array($theme_preview_arr)) {
                //获取所有预览图
                $all_preview = $this->get_theme_preview($id);

                //预览图路径
                $all_preview_dir = array();
                $all_preview_file = array();
                foreach($all_preview as $pre){
                    $all_preview_file[] = $pre['dir'];
                    $view = pathinfo($pre['dir']);
                    $all_preview_dir[] = $view['basename'];
                    //文件是否修改
                    if(!in_array($view['basename'], $theme_preview_arr)){
                        $edit_theme_arr[] = $pre['dir'];
                        //删除预览
                        $j++;
                        $i++;
                        $this->del_preview_by_id($pre['id']);
                        unlink(DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$id.'/preview/'.$view['basename']);
                    }
                }

                foreach($theme_preview_arr as $post_theme_file){
                    //判断文件是否被修改
                    if(!in_array($post_theme_file, $all_preview_dir)){
                        copy($temporary_dir.$post_theme_file, DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$id.'/preview/'.$post_theme_file);
                        @unlink($temporary_dir.$post_theme_file);
                        #插入预览图
                        $this->save_theme_preview($id,DATA_SERVER.'/uploads/'.$this->uploadFileDirectory.$id.'/preview/'.$post_theme_file);
                    }
                }

//                //预览图是否修改
//
//                foreach($theme_preview_arr as $file_exis){
//                    if(file_exists($temporary_dir.$file_exis)){
//                        $i++;
//                    }
//                }
//                if($i > 0){
//                    $colQuery .= " `theme_preview`, ";
//                    $valQuery .= " '".sql_quote(json_encode($edit_theme_arr))."', ";
//                }
            }

            //封面预览图
            if(isset($_POST['first_preview']) && trim($_POST['first_preview']) != '' && file_exists($temporary_dir.$_POST['first_preview'])) {
                //检测文件是否被修改
                $first_preview_is = $this->is_edit_item_file($id);
                $path_first_file_name = pathinfo($first_preview_is['theme_preview']);
                if($_POST['first_preview'] != $path_first_file_name['basename']){
                    $temporary_first_preview_file = $temporary_dir.$_POST['first_preview'];
                    copy($temporary_first_preview_file, DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$id.'/'.$_POST['first_preview']);
                    @unlink($temporary_first_preview_file);
                    $colQuery .= " `first_preview`, ";
                    $valQuery .= " '".$_POST['first_preview']."', ";
                    $e++;
                }
            }
            //主程序包
            if(isset($_POST['main_file']) && trim($_POST['main_file']) != '' && file_exists($temporary_dir.$_POST['main_file'])) {

                //检测文件是否被修改
                $main_file_is = $this->is_edit_item_file($id);
                $path_first_file_name = pathinfo($main_file_is['main_file']);
                if($_POST['main_file'] != $path_first_file_name['basename']){
                    $temporary_main_file = $temporary_dir.$_POST['main_file'];
                    copy($temporary_main_file, DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$id.'/'.$_POST['main_file']);
                    @unlink($temporary_main_file);;
                    $e++;
                    $colQuery .= " `main_file`, ";
                    $valQuery .= " '".$_POST['main_file']."', ";
                }
            }

            $mysql->query("
                INSERT INTO `temp_items` (
                    `item_id`,
                    `name`,
                    $colQuery
                    `reviewer_comment`,
                    `datetime`
                )
                VALUES (
                    '".intval($id)."',
                    '".sql_quote($item['name'])."',
                    $valQuery
                    '".sql_quote($_POST['comments_to_reviewer'])."',
                    NOW()
                )

            ");

            return true;
	}



	//作品编辑
	public function edit($id, $fromAdmin=false) {
		global $mysql, $langArray, $attributes;
        //作品详情
        $item_info = $this->get($id);

        $user_id = $_SESSION['user']['user_id'];
        if(!$user_id){
            return false;
        }
        //临时文件目录
        $temporary_dir = DATA_SERVER_PATH.'uploads/temporary/'.$user_id.'/';

		
		if(!isset($_POST['description']) || trim($_POST['description']) == '') {
			$error['description'] = $langArray['error_not_set_description'];
		}
		
		if($fromAdmin && (!isset($_POST['price']) || trim($_POST['price']) == '' || $_POST['price'] == '0')) {
			$error['price'] = $langArray['error_not_set_price'];
		}
		
		if(isset($_POST['demo_url']) && trim($_POST['demo_url']) && filter_var($_POST['demo_url'], FILTER_VALIDATE_URL) === false) {
			$error['demo_url'] = $langArray['error_demo_url'];
		}
		
		if(!isset($_POST['category'])) {
			$error['category'] = $langArray['error_not_set_category'];
		} elseif ( !is_numeric($_POST['category']) && !is_array($_POST['category']) ) {
			$error['category'] = $langArray['error_not_set_category'];
		}
        if(!isset($_POST['tags']) || empty($_POST['tags'])) {
            $error['tags_features'] = $langArray['error_not_set_tags'];
        }else{
            $tags_data = explode(',', $_POST['tags']);
            if(count($tags_data) > 6){
                $error['tags_features'] = $langArray['error_not_set_tags_tomore'];
            }
        }

        //作品非active状态禁止修改文件------------
        $edit_file_num = 0;
        //修改文件是否存在
        if(file_exists($temporary_dir.$_POST['thumbnail']) ||  file_exists($temporary_dir.$_POST['first_preview'])  || file_exists($temporary_dir.$_POST['main_file'])){
            $edit_file_num++;
        }
        //预览图
        $theme_preview_arr = explode(',', $_POST['theme_preview']);
        foreach($theme_preview_arr as $file_exis){
            if(file_exists($temporary_dir.$file_exis)){
                $edit_file_num++;
            }
        }
        if($item_info['status'] != 'active' && $edit_file_num > 0){
            $error['file_status_error'] = $langArray['file_status_error'];
        }
        //（缩略图）
        if(isset($_POST['thumbnail']) && trim($_POST['thumbnail']) != '') {

            $file = pathinfo($_POST['thumbnail']);
            if(!in_array(strtolower($file['extension']),$this->support_format(1))) {
                $error['thumbnail'] = $langArray['error_thumbnail_jpg'];
            }
        }

        // (预览图)
        if(isset($_POST['theme_preview'])){
            $theme_preview_arr = explode(',', $_POST['theme_preview']);
            if(!isset($theme_preview_arr) || !is_array($theme_preview_arr)) {
                $error['theme_preview'] = $langArray['error_not_set_theme_preview'];
            }else{
                foreach($theme_preview_arr as $theme_f){
                    $theme_file = pathinfo($theme_f);
                    if(!in_array(strtolower($theme_file['extension']),$this->support_format(0))) {
                        $error['theme_preview'] = $langArray['error_theme_preview_jpg'];
                    }
                }
            }
        }
        // (封面)
        if(isset($_POST['first_preview']) && trim($_POST['first_preview']) != '') {

            $file = pathinfo($_POST['first_preview']);
            if(!in_array(strtolower($file['extension']),$this->support_format(0))) {
                $error['first_preview'] = $langArray['error_theme_preview_jpg'];
            }
        }

        // （主程序包）
        if(isset($_POST['main_file']) && trim($_POST['main_file']) != '') {

            $file = pathinfo($_POST['main_file']);
            if(strtolower($file['extension']) != 'zip') {
                $error['main_file'] = $langArray['error_main_file_zip'];
            }
        }

		if(is_array($attributes)) {
			$attributesError = false;
			foreach($attributes as $a) {				
				if(!isset($_POST['attributes'][$a['id']])) {
					$attributesError = true;
					break;
				}				
			}
			
			if($attributesError) {
				$error['attributes'] = $langArray['error_set_all_attributes'];
			}
		}
		
		if(isset($error)) {
			return $error;
		} 
		
		$setQuery = '';		
		if($fromAdmin) {
			$setQuery .= " `price` = '".sql_quote($_POST['price'])."', ";

			if(isset($_POST['free_file'])) {

				$setQuery .= " `free_file` = 'true', ";
			}
			
			//周推荐至-时间
			if(isset($_POST['weekly_to']) && trim($_POST['weekly_to']) != '') {
				$setQuery .= " `weekly_to` = '".sql_quote($_POST['weekly_to'])."', ";
			}
		}		
		//演示地址
		if(!isset($_POST['demo_url'])) {
			$_POST['demo_url'] = '';
		}
		
		//作品状态是否为请求免费上架状态
		if(!isset($_POST['free_request'])) {
			$_POST['free_request'] = 'false';
		}
		
		//更新作品表
		$mysql->query("
			UPDATE `items`
			SET `description` = '".sql_quote($_POST['description'])."',
					`free_request` = '".sql_quote($_POST['free_request'])."',
					$setQuery
					`demo_url` = '".sql_quote($_POST['demo_url'])."'
			WHERE `id` = '".intval($id)."'
			LIMIT 1
		");
		
		require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
		$categoriesClass = new categories();				
		$allCategories = $categoriesClass->getAll();
		$mysql->query("DELETE FROM `items_to_category` WHERE `item_id` = '".intval($id)."'");
		if(is_array($_POST['category'])) {
			foreach($_POST['category'] AS $category_id) {
				$categories = $categoriesClass->getCategoryParents($allCategories, $category_id);
				$categories = explode(',', $categories);
				array_pop($categories);
				$categories = array_reverse($categories);
				$categories = ','.implode(',', $categories).',';
				$mysql->query("
					INSERT INTO `items_to_category` (
						`item_id`,
						`categories`
					) 
					VALUES (
						'".intval($id)."',
						'".sql_quote($categories)."'
					)
				");
			}
		} else {
			$categories = $categoriesClass->getCategoryParents($allCategories, $_POST['category']);
			$categories = explode(',', $categories);
			array_pop($categories);
			$categories = array_reverse($categories);
			$categories = ','.implode(',', $categories).',';
			$mysql->query("
				INSERT INTO `items_to_category` (
					`item_id`,
					`categories`
				) 
				VALUES (
					'".intval($id)."',
					'".sql_quote($categories)."'
				)
			");
		}
		
        //更新属性
		$mysql->query("
			DELETE FROM `items_attributes`
			WHERE `item_id` = '".intval($id)."'
		");		
		$_POST['attributes'] = (array)(isset($_POST['attributes']) ? $_POST['attributes'] : array());
		foreach($_POST['attributes'] as $cID=>$a) {
			if(is_array($a)) {
				foreach($a as $ai) {
					if(!trim($ai)) { continue; }
					$mysql->query("
						INSERT INTO `items_attributes` (
							`item_id`,
							`attribute_id`,
							`category_id`
						)
						VALUES (
							'".intval($id)."',
							'".sql_quote($ai)."',
							'".sql_quote($cID)."'
						)
					");
				}
			}
			else {
				if(!trim($a)) { continue; }
				$mysql->query("
					INSERT INTO `items_attributes` (
						`item_id`,
						`attribute_id`,
						`category_id`
					)
					VALUES (
						'".intval($id)."',
						'".sql_quote($a)."',
						'".sql_quote($cID)."'
					)
				");
			}
		}
		
		//免费
		if($fromAdmin) {
			if(isset($_POST['free_file'])) {
				$this->addUserStatus($id, 'freefile');
				$mysql->query("
					UPDATE `items`
					SET `free_file` = 'true'
					WHERE `id` = '".intval($id)."'
					LIMIT 1
				");
			} else {
				$mysql->query("
					UPDATE `items`
					SET `free_file` = 'false'
					WHERE `id` = '".intval($id)."'
					LIMIT 1
				");
			}
			if(isset($_POST['weekly_to']) && trim($_POST['weekly_to']) != '') {
				$this->addUserStatus($id, 'featured');
			}

        $key_num = 0;
        $setQuery_e = '';
        //缩略图
        if(isset($_POST['thumbnail']) && trim($_POST['thumbnail']) != '' && file_exists($temporary_dir.$_POST['thumbnail'])) {
            //检测文件是否被修改
            $thumbnail_is = $this->is_edit_item_file($id);
            $path_file_name = pathinfo($thumbnail_is['thumbnail']);
            if($_POST['thumbnail'] != $path_file_name['basename']){
                unlink(DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$id.'/'.$path_file_name['basename']);
                require_once ENGINE_PATH.'/classes/image.class.php';
                $imageClass = new Image();
                //缩略图
                $temporary_thumbnail_file = $temporary_dir.$_POST['thumbnail'];
                //裁剪缩略图
                $imageClass->crop($temporary_thumbnail_file, 90, 90);
                copy($temporary_thumbnail_file, DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$id.'/'.$_POST['thumbnail']);
                @unlink($temporary_thumbnail_file);
                $setQuery_e .= ", `thumbnail` = '".$_POST['thumbnail']."'";
                $key_num++;
            }
        }

        //预览图
        $theme_preview_arr = explode(',', $_POST['theme_preview']);
        if(isset($theme_preview_arr) && is_array($theme_preview_arr)) {
            //获取所有预览图
            $all_preview = $this->get_theme_preview($id);

            //预览图路径
            $all_preview_dir = array();
            foreach($all_preview as $pre){
                $view = pathinfo($pre['dir']);
                $all_preview_dir[] = $view['basename'];
                //文件是否修改
                if(!in_array($view['basename'], $theme_preview_arr)){
                    //删除预览
                    $this->del_preview_by_id($pre['id']);
                    unlink(DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$id.'/preview/'.$view['basename']);
                }
            }

            foreach($theme_preview_arr as $post_theme_file){
                //判断文件是否被修改
                if(!in_array($post_theme_file, $all_preview_dir)){
                    //判断文件格式
                    copy($temporary_dir.$post_theme_file, DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$id.'/preview/'.$post_theme_file);
                    @unlink($temporary_dir.$post_theme_file);
                    #插入预览图
                    $this->save_theme_preview($id,DATA_SERVER.'/uploads/'.$this->uploadFileDirectory.$id.'/preview/'.$post_theme_file);
                    $key_num++;
                }
            }
        }

        //封面预览图
        if(isset($_POST['first_preview']) && trim($_POST['first_preview']) != '' && file_exists($temporary_dir.$_POST['first_preview'])) {
            //检测文件是否被修改
            $first_preview_is = $this->is_edit_item_file($id);
            $path_first_file_name = pathinfo($first_preview_is['theme_preview']);
            if($_POST['first_preview'] != $path_first_file_name['basename']){
                $temporary_first_preview_file = $temporary_dir.$_POST['first_preview'];
                unlink(DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$id.'/'.$path_first_file_name['basename']);
                copy($temporary_first_preview_file, DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$id.'/'.$_POST['first_preview']);
                @unlink($temporary_first_preview_file);
                $setQuery_e .= ", `theme_preview` = '".$_POST['first_preview']."'";
                $key_num++;
            }
        }

        //主程序包
        if(isset($_POST['main_file']) && trim($_POST['main_file']) != '' && file_exists($temporary_dir.$_POST['main_file'])) {
            //检测文件是否被修改
            $main_file_is = $this->is_edit_item_file($id);
            $path_first_file_name = pathinfo($main_file_is['main_file']);
            if($_POST['main_file'] != $path_first_file_name['basename']){
                unlink(DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$id.'/'.$path_first_file_name['basename']);
                $temporary_main_file = $temporary_dir.$_POST['main_file'];
                copy($temporary_main_file, DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$id.'/'.$_POST['main_file']);
                @unlink($temporary_main_file);
                $key_num++;
                //更新文件名
                $setQuery_e .= ", `main_file` = '".$_POST['main_file']."'";
//                $setQuery_e .= ", `main_file_name` = '".sql_quote($_SESSION['temp']['edit_item']['main_file_name'])."' ";
            }
        }

        }

        if($key_num > 0){
            //更新作品状态
            $mysql->query("
				UPDATE `items`
				SET `status` = 'queue' $setQuery_e
				WHERE `id` = '".intval($id)."'
				LIMIT 1
			");
        }

        //编辑推荐标签（删除原有 然后新增）
        require_once ROOT_PATH.'/apps/tags/models/tags.class.php';
        $tags = new tags();
        //删除原绑定标签
        $tags -> del_by_item_id($id);
        //重写绑定标签
        $tags_data = explode(',', $_POST['tags']);
        $tag_arr = array();
        foreach($tags_data as $val){
            $tags -> add_tags(array('item_id' => $id,'tag_id' => $val));
        }
		return true;
	}


	public function delete($id, $unapprove=false) {
		global $mysql;

         //删除作品预览图
         $this->del_preview($id);

		$data = $this->get($id);
		
		#删除作品
		$mysql->query("
			DELETE FROM `items`
			WHERE `id` = '".intval($id)."'
		");
		
		$mysql->query("
			DELETE FROM `items_attributes`
			WHERE `item_id` = '".intval($id)."'
		");
		
		$mysql->query("
			DELETE FROM `items_collections`
			WHERE `item_id` = '".intval($id)."'
		");
		
		$mysql->query("
			DELETE FROM `items_comments`
			WHERE `item_id` = '".intval($id)."'
		");
		
		$mysql->query("
			DELETE FROM `items_faqs`
			WHERE `item_id` = '".intval($id)."'
		");
		
		$mysql->query("
			DELETE FROM `items_rates`
			WHERE `item_id` = '".intval($id)."'
		");
		
		$mysql->query("
			DELETE FROM `items_tags`
			WHERE `item_id` = '".intval($id)."'
		");
		
		$mysql->query("
			DELETE FROM `temp_items`
			WHERE `item_id` = '".intval($id)."'
		");
		
		$mysql->query("
			DELETE FROM `temp_items_tags`
			WHERE `item_id` = '".intval($id)."'
		");
		
		$mysql->query("
			DELETE FROM `items_to_category`
			WHERE `item_id` = '".intval($id)."'
		");

		if(!$unapprove) {
			$mysql->query("
				UPDATE `users`
				SET `items` = `items` - 1
				WHERE `user_id` = '".intval($data['user_id'])."'
				LIMIT 1
			");									
		}
		
		return true;		
	}

	public function deleteUpdate($id) {
		global $mysql;

#删除临时作品标签
		$mysql->query("
			DELETE FROM `temp_items_tags`
			WHERE `item_id` = '".intval($id)."'
		");


#删除临时作品
		$mysql->query("
			DELETE FROM `temp_items`
			WHERE `item_id` = '".intval($id)."'
		");
		
		return true;		
	}
	
/*
 * 管理员函数
 */	
	public function approve($id) {
		global $mysql, $data, $langArray,$meta,$config;
		
		if($data['status'] == 'active') {
			return true;
		}
				
		if(!isset($_POST['price']) || trim($_POST['price']) == '' || $_POST['price'] == '0') {
			return $langArray['error_set_price'];
		}
		
		$_POST['price'] = str_replace(',', '.', $_POST['price']);
		$setQuery = '';
		if(isset($_POST['free_file'])) {
			$mysql->query("
				UPDATE `items`
				SET `free_file` = 'false'					
			");
			$setQuery .= " `free_file` = 'true', ";
		}
		
		if(isset($_POST['weekly_to']) && trim($_POST['weekly_to']) != '') {
			$setQuery .= " `weekly_to` = '".sql_quote($_POST['weekly_to'])."', ";
		}


		$mysql->query("
			UPDATE `items`
			SET `price` = '".sql_quote($_POST['price'])."',
					$setQuery
					`status` = 'active'
			WHERE `id` = '".intval($id)."'
			LIMIT 1
		");
		
		$mysql->query("
			UPDATE `users`
			SET `items` = `items` + 1
			WHERE `user_id` = '".intval($data['user_id'])."'
			LIMIT 1
		");

        //判断有无客服管理模块
        require_once ROOT_PATH.'/apps/app_extends/models/app_extends.class.php';

        $app_extends=new app_extends();

        if($app_extends->is_service()){

            //通过用户id获取关联客服
            require_once ROOT_PATH.'/apps/service/models/service.class.php';
            $service = new service();
            $theservice = $service->getserviceByuserid($data['user']['user_id']);
            $item_url = $config['domain'].'/'.$languageURL.'items/'.$data['id'];
            $item_url = '<a href="'.$item_url.'" target="_blank">'.$item_url.'</a>';
            #给用户发邮件
            require_once ENGINE_PATH.'/classes/ email.class.php';
            $emailClass = new email();

            $emailClass->fromEmail = 'no-reply@'.$config['domain'];
            $emailClass->contentType = 'text/html';
            $emailClass->subject = '你的作品['.$data['name'].']审核通过啦！';
            $emailClass->message = 'Hi！['.$data['user']['username'].']：<br />
								&nbsp;&nbsp;&nbsp;&nbsp;恭喜你的作品审核通过啦！<br />
								&nbsp;&nbsp;&nbsp;&nbsp;作品名称：['.$data['name'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;作品地址：['.$item_url.']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;又可以赚钱啦！<br />
								&nbsp;&nbsp;&nbsp;&nbsp;要请小编吃饭哦！<br />
                                <br />
								&nbsp;&nbsp;&nbsp;&nbsp;专属小编：['.$theservice['user_name'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.$meta['meta_title'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.date('Y-m-d H:i:s',time()).']<br />';
            $emailClass->to($data['user']['email']);
            $emailClass->send();
            unset($emailClass);


        }


        return true;
	}
	
	public function unapprove($id) {
		global $mysql, $data, $langArray, $config,$meta;
		
		if($data['status'] == 'active') {
			return true;
		}
				
		if(!isset($_POST['comment_to_user']) || trim($_POST['comment_to_user']) == '') {
			return $langArray['error_set_comment_to_user'];
		}
		
		$mysql->query("
			UPDATE `items`
			SET `status` = 'unapproved'
			WHERE `id` = '".intval($id)."'
			LIMIT 1
		");
		
		$mysql->query("
			UPDATE `users`
			SET `items` = `items` + 1
			WHERE `user_id` = '".intval($data['user_id'])."'
			LIMIT 1
		");

        //判断有无客服管理模块
        require_once ROOT_PATH.'/apps/app_extends/models/app_extends.class.php';

        $app_extends=new app_extends();

        if($app_extends->is_service()){
            //通过用户id获取关联客服
            require_once ROOT_PATH.'/apps/users/models/service.class.php';
            $service = new service();
            $theservice = $service->getserviceByuserid($data['user']['user_id']);
#给用户发邮件
            require_once ENGINE_PATH.'/classes/email.class.php';
            $emailClass = new email();

            $emailClass->fromEmail = 'no-reply@'.$config['domain'];
            $emailClass->contentType = 'text/html';
            $emailClass->subject = '你的作品['.$data['name'].']被退回';
            $emailClass->message = 'Hi！['.$data['user']['username'].']：<br />
								&nbsp;&nbsp;&nbsp;&nbsp;很抱歉你的作品['.$data['name'].']因以下原因被退回：<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.$_POST['comment_to_user'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;不要失望好么？小编其实也很难过！<br />
								&nbsp;&nbsp;&nbsp;&nbsp;一定要再接再厉，小编为你加油！<br />
								&nbsp;&nbsp;&nbsp;&nbsp;Fighting！<br />
								<br />
								&nbsp;&nbsp;&nbsp;&nbsp;专属小编：['.$theservice['user_name'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.$meta['meta_title'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.date('Y-m-d H:i:s',time()).']<br />';

            $emailClass->to($data['user']['email']);
            $emailClass->send();
            unset($emailClass);
        }else{
            #给用户发邮件
            require_once ENGINE_PATH.'/classes/email.class.php';
            $emailClass = new email();

            $emailClass->fromEmail = 'no-reply@'.$config['domain'];
            $emailClass->subject = '['.$config['domain'].'] '.$langArray['email_unapprove_item_subject'];
            $emailClass->message = langMessageReplace($langArray['email_unapprove_item_text'], array(
                'THEMENAME' => $data['name'],
                'COMMENT' => $_POST['comment_to_user']
            ));
            $emailClass->to($data['user']['email']);
            $emailClass->send();
            unset($emailClass);
        }



		return true;


	}
	
	public function unapproveDelete($id) {
		global $mysql, $data, $langArray, $config,$meta;
		
		if($data['status'] == 'active') {
			return true;
		}
				
		if(!isset($_POST['comment_to_user']) || trim($_POST['comment_to_user']) == '') {
			return $langArray['error_set_comment_to_user'];
		}
		
		$this->delete($id, true);
        //判断有无客服管理模块
        require_once ROOT_PATH.'/apps/app_extends/models/app_extends.class.php';

        $app_extends=new app_extends();

        if($app_extends->is_service()){
            //通过用户id获取关联客服
            require_once ROOT_PATH.'/apps/service/models/service.class.php';
            $service = new service();
            $theservice = $service->getserviceByuserid($data['user']['user_id']);

#给用户发邮件
            require_once ENGINE_PATH.'/classes/email.class.php';
            $emailClass = new email();

            $emailClass->fromEmail = 'no-reply@'.$config['domain'];
            $emailClass->contentType = 'text/html';
            $emailClass->subject = '你的作品['.$data['name'].']被删除';
            $emailClass->message = 'Hi！['.$data['user']['username'].']：<br />
								&nbsp;&nbsp;&nbsp;&nbsp;很抱歉你的作品['.$data['name'].']因以下原因被删除：<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.$_POST['comment_to_user'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;不要失望好么？小编其实也很难过！<br />
								&nbsp;&nbsp;&nbsp;&nbsp;一定要再接再厉，小编为你加油！<br />
								&nbsp;&nbsp;&nbsp;&nbsp;Fighting！<br />
								<br />
								&nbsp;&nbsp;&nbsp;&nbsp;专属小编：['.$theservice['user_name'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.$meta['meta_title'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.date('Y-m-d H:i:s',time()).']<br />';

            $emailClass->to($data['user']['email']);
            $emailClass->send();
            unset($emailClass);
        }else{
            #给用户发邮件
            require_once ENGINE_PATH.'/classes/email.class.php';
            $emailClass = new email();

            $emailClass->fromEmail = 'no-reply@'.$config['domain'];
            $emailClass->subject = '['.$config['domain'].'] '.$langArray['email_unapprove_delete_item_subject'];
            $emailClass->message = langMessageReplace($langArray['email_unapprove_delete_item_text'], array(
                'THEMENAME' => $data['name'],
                'COMMENT' => $_POST['comment_to_user']
            ));
            $emailClass->to($data['user']['email']);
            $emailClass->send();
            unset($emailClass);
        }
		return true;
	}
	
	
	public function approveUpdate($id) {
		global $mysql, $data, $item, $langArray,$meta,$config;

		$setQuery = '';
		if(isset($_POST['price']) && is_numeric($_POST['price']) && $_POST['price'] != '0') {
			$_POST['price'] = str_replace(',', '.', $_POST['price']);
			$setQuery .= " `price` = '".sql_quote($_POST['price'])."', ";
		}

        //获取当前作品历史数据
        $mysql->query("
			SELECT *
			FROM `temp_items`
			WHERE `item_id` = '".intval($item['id'])."'
			LIMIT 1
		");
        $temp_item_data = $mysql->fetch_array();

        //获取作品信息
        $item_info = $this->get($item['id']);

        //检测缩略图更新状态
        if(!empty($temp_item_data['thumbnail'])){
            $setQuery .= " `thumbnail` = '".sql_quote($temp_item_data['thumbnail'])."', ";
            unlink(DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$item['id'].'/'.$item_info["thumbnail"]);
        }
        //检测封面更新状态
        if(!empty($temp_item_data['first_preview'])){
            $setQuery .= " `theme_preview` = '".sql_quote($temp_item_data['first_preview'])."', ";
            unlink(DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$item['id'].'/'.$item_info["theme_preview"]);

        }
        //检测文件包更新状态
        if(!empty($temp_item_data['main_file'])){
            $setQuery .= " `main_file` = '".sql_quote($temp_item_data['main_file'])."', ";
            $setQuery .= " `main_file_name` = '".sql_quote($temp_item_data['main_file_name'])."', ";
            unlink(DATA_SERVER_PATH.'/uploads/'.$this->uploadFileDirectory.$item['id'].'/'.$item_info["main_file"]);
        }

        //更改作品状态
        $mysql->query("
			UPDATE `items`
			SET $setQuery
			 `status` = 'active'
			WHERE `id` = '".intval($item['id'])."'
			LIMIT 1
		");

            //获取用户信息
            require_once ROOT_PATH.'/apps/users/models/users.class.php';
            $user = new users();
            $user_info = $user->getuserinfoById($item['user_id']);

            //通过用户id获取关联客服
//            require_once ROOT_PATH.'/apps/service/models/service.class.php';
//            $service = new service();
//            $theservice = $service->getserviceByuserid($item['user_id']);
//            $item_url = $config['domain'].'/'.$languageURL.'items/'.$item['id'];
//            $item_url = '<a href="'.$item_url.'" target="_blank">'.$item_url.'</a>';
//            #给用户发邮件
//            require_once ENGINE_PATH.'/classes/email.class.php';
//            $emailClass = new email();
//
//            $emailClass->fromEmail = 'no-reply@'.$config['domain'];
//            $emailClass->contentType = 'text/html';
//            $emailClass->subject = '你的作品['.$item['name'].']更新审核通过啦！';
//            $emailClass->message = 'Hi！['.$user_info['username'].']：<br />
//								&nbsp;&nbsp;&nbsp;&nbsp;恭喜你的作品更新审核通过啦！<br />
//								&nbsp;&nbsp;&nbsp;&nbsp;作品名称：['.$item['name'].']<br />
//								&nbsp;&nbsp;&nbsp;&nbsp;作品地址：['.$item_url.']<br />
//								&nbsp;&nbsp;&nbsp;&nbsp;又可以赚钱啦！<br />
//                                <br />
//								&nbsp;&nbsp;&nbsp;&nbsp;专属小编：['.$theservice['user_name'].']<br />
//								&nbsp;&nbsp;&nbsp;&nbsp;['.$meta['meta_title'].']<br />
//								&nbsp;&nbsp;&nbsp;&nbsp;['.date('Y-m-d H:i:s',time()).']<br />';
//            $emailClass->to($user_info['email']);
//            $emailClass->send();
//            unset($emailClass);


		$this->deleteUpdate($item['id']);
		
		return true;
	}
	
	public function unapproveDeleteUpdate($id) {
		global $mysql, $item, $data, $langArray, $config, $meta;
		
		if(!isset($_POST['comment_to_user']) || trim($_POST['comment_to_user']) == '') {
			return $langArray['error_set_comment_to_user'];
		}
		
		$this->deleteUpdate($item['id']);

//判断有无客服管理模块
        require_once ROOT_PATH.'/apps/app_extends/models/app_extends.class.php';

        $app_extends=new app_extends();

        if($app_extends->is_service()){
            //获取用户信息
            require_once ROOT_PATH.'/apps/users/models/users.class.php';
            $user = new users();
            $user_info = $user->getuserinfoById($item['user_id']);
            //通过用户id获取关联客服
            require_once ROOT_PATH.'/apps/service/models/service.class.php';
            $service = new service();
            $theservice = $service->getserviceByuserid($item['user_id']);
            $item_url = $config['domain'].'/'.$languageURL.'items/'.$item['id'];
            #给用户发邮件
            require_once ENGINE_PATH.'/classes/email.class.php';
            $emailClass = new email();

            $emailClass->fromEmail = 'no-reply@'.$config['domain'];
            $emailClass->contentType = 'text/html';
            $emailClass->subject = '你的作品['.$item['name'].']更新被拒绝';
            $emailClass->message = 'Hi！['.$user_info['username'].']：<br />
								&nbsp;&nbsp;&nbsp;&nbsp;很抱歉你的作品['.$item['name'].']更新因以下原因被拒绝：<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.$_POST['comment_to_user'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;不要失望好么？小编其实也很难过！<br />
								&nbsp;&nbsp;&nbsp;&nbsp;一定要再接再厉，小编为你加油！<br />
								&nbsp;&nbsp;&nbsp;&nbsp;Fighting！<br />
                                <br />
								&nbsp;&nbsp;&nbsp;&nbsp;专属小编：['.$theservice['user_name'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.$meta['meta_title'].']<br />
								&nbsp;&nbsp;&nbsp;&nbsp;['.date('Y-m-d H:i:s',time()).']<br />';
            $emailClass->to($user_info['email']);
            $emailClass->send();
            unset($emailClass);
        }else{
            #给用户发邮件
            require_once ENGINE_PATH.'/classes/email.class.php';
            $emailClass = new email();

            $emailClass->fromEmail = 'no-reply@'.$config['domain'];
            $emailClass->subject = '['.$config['domain'].'] '.$langArray['email_unapprove_delete_item_update_subject'];
            $emailClass->message = langMessageReplace($langArray['email_unapprove_delete_item_update_text'], array(
                'THEMENAME' => $item['name'],
                'COMMENT' => $_POST['comment_to_user']
            ));
            $emailClass->to($item['user']['email']);
            $emailClass->send();
            unset($emailClass);
        }
		return true;
	}
	
	public function isInUpdateQueue($id) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `temp_items`
			WHERE `item_id` = '".intval($id)."'
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}

		return true;
	}
	
	public function getItemsCount() {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `items`
			WHERE `status` = 'active'
		");
		
		return $mysql->num_rows();
	}
	
	
	public function isRate($id) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `items_rates`
			WHERE `item_id` = '".intval($id)."' AND `user_id` = '".intval($_SESSION['user']['user_id'])."'
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return $mysql->fetch_array();
	}
	
	public function rate($id, $rate) {
		global $mysql, $item;
		
		$row = $this->isRate($id);
		if(is_array($row)) {
			return $item;
		}
		
		$item['votes'] = $item['votes'] + 1;
		$item['score'] = $item['score'] + $rate;
		$item['rating'] = $item['score'] / $item['votes'];
		$item['rating'] = round($item['rating']);
		
		$mysql->query("
			UPDATE `items`
			SET `rating` = '".intval($item['rating'])."',
					`score` = '".intval($item['score'])."',
					`votes` = '".intval($item['votes'])."'
			WHERE `id` = '".intval($id)."'
		");
		
		$mysql->query("
			INSERT INTO `items_rates` (
				`item_id`,
				`user_id`,
				`rate`,
				`datetime`
			)
			VALUES (
				'".intval($id)."',
				'".intval($_SESSION['user']['user_id'])."',
				'".intval($rate)."',
				NOW()
			)
		");
		
#用户评星
		require_once ROOT_PATH.'/apps/users/models/users.class.php';
		$usersClass = new users();

		$user = $usersClass->get($item['user_id']);
		
		$user['votes'] = $user['votes'] + 1;
		$user['score'] = $user['score'] + $rate;
		$user['rating'] = $user['score'] / $user['votes'];
		$user['rating'] = round($user['rating']);
		
		$mysql->query("
			UPDATE `users`
			SET `rating` = '".intval($user['rating'])."',
					`score` = '".intval($user['score'])."',
					`votes` = '".intval($user['votes'])."'
			WHERE `user_id` = '".intval($user['user_id'])."'
		");
		
		return $item;
	}
	
	
	public function getRates($where='') {
		global $mysql;

		if($where!='') {
			$where = " AND ($where) ";
		}
		
		$mysql->query("
			SELECT *
			FROM `items_rates`
			WHERE `user_id` = '".intval($_SESSION['user']['user_id'])."' $where
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		while($d = $mysql->fetch_array()) {
			$return[$d['item_id']] = $d;
		}
		
		return $return;
	}
	
	
	public function getTagItems($tagID, $tagType, $start=0, $limit=0, $where='', $order='`datetime` DESC') {
		global $mysql;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS i.*,
			(SELECT GROUP_CONCAT(`categories` SEPARATOR '|') FROM `items_to_category` WHERE `item_id` = `i`.`id`) AS `categories`
			FROM `items_tags` AS it
			JOIN `items` AS i
			ON i.`id` = it.`item_id`
			WHERE it.`tag_id` = '".intval($tagID)."' AND it.`type` = '".sql_quote($tagType)."' $where
			ORDER BY $order
			$limitQuery
		");
			
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$this->usersWhere = '';
		$return = array();
		while($d = $mysql->fetch_array()) {
			$categories = explode('|', $d['categories']);
			unset($d['categories']);
			$d['categories'] = array();
			$row=0;
			foreach($categories AS $cat) {
				$categories1 = explode(',', $cat);
				foreach($categories1 as $c) {
					$c = trim($c);
					if($c != '') {
						$d['categories'][$row][$c] = $c;
					}
				}
				$row++;
			}
			$return[$d['id']] = $d;
			
			if($this->usersWhere != '') {
				$this->usersWhere .= ' OR ';
			}
			$this->usersWhere .= " `user_id` = '".intval($d['user_id'])."' ";
		}
		
		$this->foundRows = $mysql->getFoundRows();
		
		return $return;
	}
	
	//宣传操作
	private function addUserStatus($id, $type='freefile') {
		$item = $this->get($id);
		if(is_array($item)) {
			if(!$this->isExistUserStatus($item['user_id'], $type)) {
				$this->insertUserStatus($item['user_id'], $type);
			}
		}		
		return true;
	}
	//用户作品在站内的宣传记录 特色
	private function isExistUserStatus($id, $type) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `users_status`
			WHERE `user_id` = '".intval($id)."' AND `status` = '".sql_quote($type)."'
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return true;
	}
	//添加用户作品在站内的宣传记录 
	private function insertUserStatus($id, $type) {
		global $mysql;
		
		$mysql->query("
			INSERT INTO `users_status` (
				`user_id`,
				`status`,
				`datetime`
			)
			VALUES (
				'".intval($id)."',
				'".sql_quote($type)."',
				NOW()
			)
		");
		
		return true;
	}
	
	//改变作品免费状态
	public function ajax_edit_free_file_status($id=0,$freefile=''){
		global $mysql;
		if(isset($_POST['action']) && $_POST['action']=='ajax_edit') {
			$this->addUserStatus($id, 'freefile');
			$sql = $mysql->query("
				UPDATE `items`
				SET `free_file` = '".$freefile."'
				WHERE `id` = '".intval($id)."'
				LIMIT 1
			");
			if($sql){
				return true;
			}
		}
	}

    //作品格式支持
    public function support_format($type=0){
        if($type == 0){
            return array('jpg','png','gif','wma','mp3','wav','mp4','flv','wmv','swf');
        }elseif($type == 1){
            return array('jpg','png','gif');
        }elseif($type == 2){
            return array('wma','mp3','wav','mp4','flv','wmv','swf');
        }else{
            return array();
        }
    }

    //获取标签
    public function get_label($where='',$order = "ORDER BY `order_index` ASC"){
        global $mysql;
        $mysql->query("
			SELECT *
			FROM `label`
			$where
			$order
		", __FUNCTION__ );

        $return = array();
        while($d = $mysql->fetch_array()) {
            $return[] = $d;
        }
        return $return;
    }

    //添加标签
    public function add_label($type=0){
        global $mysql;

        if(!isset($_POST['label_name']) || trim($_POST['label_name']) == '') {
            $error['label_name'] = $langArray['error_fill_this_field'];
        }

        if(isset($error)) {
            return $error;
        }

        if(isset($_POST['visible']) && $_POST['visible'] == 0){
            $visible = $_POST['visible'];
        }else{
            $visible = 1;
        }
        $this->tableName = 'label';
        $this->orderWhere = " AND `type` = '".intval($type)."' ";
        $orderIndex = $this->getNextOrderIndex();
        //添加作品格式格式关联标签
        $mysql->query("
			INSERT INTO `label` (
				`label_name`,
				`status`,
				`order_index`
			)
			VALUES (
				'".sql_quote($_POST['label_name'])."',
				'".intval($visible)."',
				'".intval($orderIndex)."'
			)
		");
        return true;
    }

    //编辑作品格式选项标签
    public function format_edit($id){
        global $mysql;

        if(!isset($_POST['label_name']) || trim($_POST['label_name']) == '') {
            $error['label_name'] = $langArray['error_fill_this_field'];
        }

        if(isset($error)) {
            return $error;
        }
        $status = $_POST['visible'] == '0' ? '0' : '1';
        $sql = $mysql->query("
				UPDATE `label`
				SET
				`label_name` = '".$_POST['label_name']."',
                `status` = '".intval($status)."'
				WHERE `id` = '".intval($id)."'
				LIMIT 1
			");
        if($sql){
            return true;
        }
    }

    //通过预览id删除预览文件数据
    public function del_preview_by_id($id=0){
        global $mysql;

        $mysql->query("
			DELETE FROM `preview`
			WHERE `id` = '".intval($id)."'
		");
        return true;
    }

    public function record($itemID){
        global $mysql;
        $mysql->query("
            SELECT t1.datetime,price,t2.username,nickname
            FROM orders t1
            JOIN users t2
            ON t1.user_id = t2.user_id
            WHERE t1.item_id = '".intval($itemID)."'
        ");
        if($mysql->num_rows() == 0) {
            return false;
        }
        $arr=array();
        while( $d=$mysql->fetch_array()){
            $arr[]=  $d;
        }
        return $arr;
    }

    //检测作品中文件是否被修改
    public function is_edit_item_file($item_id){
        global $mysql;

        $mysql->query("
			SELECT  *
			FROM `items`
			WHERE `id` = '".intval($item_id)."'
			LIMIT 1
		");
        if($mysql->num_rows() > 0 ){
            return $mysql->fetch_array();
        }
    }

    //显示修改作品审核队列
    public function update_temp_queue($item_id){
        global $mysql;
        $query = $mysql->query("
        		UPDATE `temp_items`
        		SET `status` = '".intval(1)."'
        		WHERE `item_id` = '".intval($item_id)."'
        ");
        return true;
    }

}
?>