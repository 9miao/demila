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

_setView(__FILE__);
_setTitle($langArray['items']);
    
    //快速改变作品状态
    if(isset($_POST['action']) && $_POST['action']=='ajax_edit' && isset($_POST['status']) && isset($_POST['item'])){  
        require_once ROOT_PATH.'/apps/items/models/items.class.php';
	    $itemsClass = new items(); 
	    //改变作品免费状态
	    $res_status = '';
	    $_POST['status'] == 'false' ? $res_status = 'true' : $res_status = 'false';
	    $res = $itemsClass ->ajax_edit_free_file_status($_POST['item'],$res_status);
	    if($res){
	    	$pic = '';
	    	$_POST['status'] == 'false' ? $pic = "http://".$_SERVER['SERVER_NAME']."/static//admin/images/icons/24x24/accept.png" : $pic = "http://".$_SERVER['SERVER_NAME']."/static///img/question.png"; 
	    	//构造返回数据
	    	$res_arr = array('status'=>$res_status,'pic'=>$pic);
	    	die(json_encode($res_arr));
        }else{
        	die();
        }
    }
	if(isset($_POST['q'])) {
		$_GET['q'] = $_POST['q'];
	}
	if(!isset($_GET['q'])) {
		$_GET['q'] = '';
	}
	if(!isset($_GET['order'])) {
		$_GET['order'] = '';
	}
	if(!isset($_GET['dir'])) {
		$_GET['dir'] = '';
	}
	
	$cms = new items ( );
	
	$whereQuery = '';
	if(trim($_GET['q']) != '') {
		$whereQuery = " AND `name` LIKE '%".sql_quote($_GET['q'])."%' ";
	}
	
	$orderQuery = '';
	switch($_GET['order']) {
		case 'name': 
			$orderQuery = "`name`";
			break;
			
		case 'price': 
			$orderQuery = "`price`";
			break;
			
		case 'sales': 
			$orderQuery = "`sales`";
			break;
			
		case 'earning': 
			$orderQuery = "`earning`";
			break;
			
		case 'free': 
			$orderQuery = "`free_request`";
			break;
			
		case 'freefile': 
			$orderQuery = "`free_file`";
			break;
			
		case 'weekly': 
			$orderQuery = "`weekly_to`";
			break;
			
		default:
			$orderQuery = "`datetime`";
	}
	switch($_GET['dir']) {
		case 'desc':
			$orderQuery .= " DESC";
			abr('orderDir', 'asc');
			break;
		
		default:
			$orderQuery .= " ASC";
			abr('orderDir', 'desc');
	}
	
	if(isset($_POST['user'])) {
		$_GET['user'] = $_POST['user'];
	}
	if(!isset($_GET['user'])) {
		$_GET['user'] = '';
	}
	
	if(is_numeric($_GET['user'])) {
		$whereQuery .= " AND `user_id` = '".intval($_GET['user'])."' ";
	}
	
	$data = $cms->getAll(START, LIMIT, " `status` = 'active' ".$whereQuery, $orderQuery);
	abr('data', $data);

	$p = paging ( "?m=" . $_GET ['m'] . "&c=list&p=", "&q=".$_GET['q']."&order=".$_GET['order']."&dir=".$_GET['dir']."&user=".$_GET['user'], PAGE, LIMIT, $cms->foundRows );
	abr ( 'paging', $p );
	
#加载用户
	require_once ROOT_PATH.'/apps/users/models/users.class.php';
	$usersClass = new users();

	$users = $usersClass->getAll(0, 0, '', '`username` ASC');
	abr('users', $users);

require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';
?>