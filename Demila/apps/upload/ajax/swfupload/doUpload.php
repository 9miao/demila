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

//文件删除接口
if(isset($_POST['action'])){
    if($_POST['filename'] && $_POST['filetype']){
        if($_POST['action'] == 'a_delete'){
            $is_del_file = DATA_SERVER_PATH.'uploads/temporary/'.$_SESSION['user']['user_id'].'/'.$_POST['filename'];
        }else{
            $is_del_file = DATA_SERVER_PATH.'uploads/temporary/'.$_SESSION['user']['user_id'].'/'.$_POST['filetype'].'/'.$_POST['filename'];
        }
        //删除文件
        @unlink($is_del_file);
        //更新session
        $type = $_POST['filetype'];
        $edit = $_POST['filename'];
        unset($_SESSION['temp']['uploaded_files'][$type][$edit]);
        die('success');
    }else{
        die('error');
    }
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