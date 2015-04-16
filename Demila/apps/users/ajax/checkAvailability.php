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

define ( 'USING_LANGUAGE', false );

require_once '../../../config.php';
require_once $config ['root_path'] . '/core/functions.php';
include_once $config ['system_core'] . "/initEngine.php";

require_once ROOT_PATH.'/data/uploads/language/lang.php';

if(!isset($_POST['username']) || trim($_POST['username']) == '') {
	die('
	 jQuery("#suggestion_result_container").html("<div class=\"box-warning\">'.$langArray['error_not_set_username'].'</div>");
	 jQuery("#ajax_username_checking").css("display", "none");
	');
}
if(!preg_match('/^[a-zA-Z0-9_]+$/i', $_POST['username'])) {
	die('
	 jQuery("#suggestion_result_container").html("<div class=\"box-error\">'.$langArray['error_not_valid_username'].'</div>");
	 jQuery("#ajax_username_checking").css("display", "none");
	');
}

require_once ROOT_PATH.'/apps/users/models/users.class.php';
$usersClass = new users();

if($usersClass->isExistUsername($_POST['username'])) {
	die('
		jQuery("#suggestion_result_container").html("<div class=\"box-error\">'.$langArray['error_exist_username'].'</div>");		
		jQuery("#ajax_username_checking").css("display", "none");
	');
}


die('
	jQuery("#suggestion_result_container").html("<div class=\"box-success \">'.$langArray['error_free_username'].'</div>");		
	jQuery("#ajax_username_checking").css("display", "none");
');

?>