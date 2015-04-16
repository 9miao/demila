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

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $config ['root_path'] . '/core/functions.php';

session_id($_POST['sessID']);

include_once $config ['system_core'] . "/initEngine.php";


// 检查上传
if (!check_login_bool()) {
	echo "ERROR:invalid upload";
	exit ( 0 );
}

if (! isset ( $_FILES ["file"] )) {
	echo "ERROR:invalid upload";
	exit ( 0 );
}

require_once '../../models/files.class.php';
$filesClass = new files( );

$s = $filesClass->addFile();
if(is_array($s)) {
	echo json_encode(array(
		'status' => 'done',
		'file' => $s
	));
}
else {
	echo json_encode(array(
		'status' => $s
	));
}

exit ( 0 );
?>