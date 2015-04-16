<?php
    require_once '../../../config.php';
	require_once $config ['root_path'] . '/core/functions.php';
	include_once $config ['system_core'] . "/initEngine.php";

	//重新发送邮件
	if(isset($_POST['res_send']) && $_POST['res_send'] == 'yes' && isset($_POST['user_id']) && !empty($_POST['user_id']) && $_SESSION['THE_USER_RES_SEND_MAIL_4_M'] && $_SESSION['THE_USER_RES_SEND_MAIL_4_M'] == $_POST['user_id']){
		require_once ROOT_PATH.'/apps/users/models/users.class.php';
		$usersClass = new users();
		$res = $usersClass->res_send($_SESSION['THE_USER_RES_SEND_MAIL_4_M']);
		if($res){
			die(json_encode(array('status'=>'success')));
		}else{
			die(json_encode(array('status'=>'error')));
		}
	}


	//重置邮箱
	if(isset($_POST['res_mail']) && $_POST['res_mail'] == 'yes' && isset($_POST['user_id']) && !empty($_POST['user_id']) && $_SESSION['THE_USER_RES_SEND_MAIL_4_M'] && $_SESSION['THE_USER_RES_SEND_MAIL_4_M'] == $_POST['user_id']){
		require_once ROOT_PATH.'/apps/users/models/users.class.php';
		$usersClass = new users();
		$res = $usersClass->res_mail($_SESSION['THE_USER_RES_SEND_MAIL_4_M']);
		if($res){
			die(json_encode(array('status'=>'success','mail'=>$_SESSION['THE_USER_RES_SEND_MAIL_4_M_MAIL'])));
		}else{
			die(json_encode(array('status'=>'error')));
		}
	}

	die();
?>