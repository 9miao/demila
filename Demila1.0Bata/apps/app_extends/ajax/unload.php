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

if (isset ( $_POST ['unload'] ) && isset ( $_POST ['extend_name']) && isset ( $_POST ['m']) ) {
    require_once ROOT_PATH . "/apps/app_extends/models/app_extends.class.php";
    $cms = new app_extends();
    if($_POST["extend_name"]=="截图包上传") {
        $cms->unload ($_POST["extend_name"],$_POST ['m'],'preview');
    }
    $cms->unload ($_POST["extend_name"],$_POST ['m']);
    die ( json_encode ( array_merge ( $_POST, array (
        'status' => 'true'
    ) ) ) );
}

echo json_encode ( array_merge ( $_POST, array (
    'status' => 'unknown error'
) ) );
die ();

?>