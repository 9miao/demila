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

admin_login();


if (isset ( $_POST ['edit'] ) && isset ( $_POST ['value']) && $_POST ['value']== 'sendmail') {

    require_once ROOT_PATH . "/apps/system/models/system.class.php";
    $sys = new system();

    $sys->unuse_smtp();

    die ( json_encode ( array_merge ( $_POST, array (
        'status' => 'true'
    ) ) ) );
}


if (isset ( $_POST ['edit'] ) && isset ( $_POST ['value']) && $_POST ['value']== 'smtp') {
    require_once ROOT_PATH . "/apps/system/models/system.class.php";

    $sys = new system();

    $sys->use_smtp();

    die ( json_encode ( array_merge ( $_POST, array (
        'status' => 'true'
    ) ) ) );
}

if (isset ( $_POST ['email'] ) ) {
    require_once ROOT_PATH . "/apps/system/models/system.class.php";
    $system = new system();
    $smtpconf=$system->getAllKeyValue();
    require_once ENGINE_PATH.'/classes/email.class.php';
    $emailClass = new email();
    $emailClass->email_sock($smtpconf["smtp_host"],$smtpconf["smtp_port"],0,'error',10,1,$smtpconf["smtp_user"],$smtpconf["smtp_pass"],$smtpconf["smtp_from"]);
    if($emailClass->send_mail_sock("测试邮件","这是一封测试邮件",$_POST['email'],$smtpconf["smtp_from_name"])==1) {
        die (json_encode(array('status' =>'发送成功')));
    }else{
        die (json_encode(array('status' => '发送失败，请正确配置SMTP')));
    }
}

if (isset ( $_POST ['edit'] ) && isset ( $_POST ['value']) && $_POST ['value']== 'template') {
    require_once ROOT_PATH . "/apps/system/models/system.class.php";
    $sys = new system();
    $sys->edit_template($_POST["template"]);
    die ( json_encode ( array_merge ( $_POST, array (
        'status' => 'true'
    ) ) ) );
}




echo json_encode ( array_merge ( $_POST, array (
    'status' => 'unknown error'
) ) );
die ();

?>