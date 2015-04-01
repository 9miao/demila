<?php

    //作品分类ajax接口
    if(isset($_POST['categoryID'])){
        $categoryID = $_POST['categoryID'];
        if(is_numeric($categoryID) || $categoryID == 'all') {
            require_once '../../../config.php';
            require_once $config ['root_path'] . '/core/functions.php';
            include_once $config ['system_core'] . "/initEngine.php";
            require_once ROOT_PATH . "/apps/categories/models/categories.class.php";
            $categoriesClass = new categories();
            if(is_numeric($categoryID)){
                
            	$allCategories = $categoriesClass->getAll(0, 0, " `visible` = 'true' ");
        		$categoryParent = $categoriesClass->getCategoryParents($allCategories, $categoryID);
        		$categoryParent = explode(',', $categoryParent);
        		$categoryParent = array_reverse($categoryParent);
        		array_shift($categoryParent);
                $whereQuery = " AND `id` IN (SELECT `item_id` FROM `items_to_category` WHERE `categories` LIKE '%,".intval($categoryID).",%') ";
            }else{
                $categoryParent = array('data'=>'');
            }
                    
            require_once ROOT_PATH.'/apps/items/models/items.class.php';
            require_once ROOT_PATH.'/apps/users/models/users.class.php';
            $itemsClass = new items();
            $users = new users();
            $order = '`datetime` DESC';
            $items = $itemsClass->getAll(0, 40, " `status` = 'active' ".$whereQuery, $order);
            $categories = $categoriesClass->getAll();
            $res_data = array();
            foreach($items as $data){
                //用户信息
                $user_info = $users->getuserinfoById($data['user_id']);
                $data['user_info']['item-author'] = $user_info['nickname'];
                
                $item_categories = array();
                foreach($data['categories'] as $cat){
                    foreach($cat as $c_cat){
                        $item_categories[] = $categories[$c_cat]['name'];
                    }
                }
                $data['item_categories'] =  $item_categories;
                $res_data[] = $data;
                
            }
            echo json_encode(array('data'=>$res_data));
        }else{
            echo json_encode(array('data'=>''));
        }
    }else{
        echo json_encode(array('data'=>''));
    }
    die();
?>