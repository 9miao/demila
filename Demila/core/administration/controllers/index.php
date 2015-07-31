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

require_once 'init.php';

//define ( 'LIMIT', 50 );

//加载管理员模板
if(!isset($_GET['m']) && !isset($_GET['c'])) {
	require_once ROOT_PATH.'/apps/admin/index.php';
}

$_templateFile = ROOT_PATH.'templates/admin/'.$meta["admin_template"].'/admin/index.html';

abr ( 'content_template', $_templateFile );

require_once 'system/checkInstalledModules.php';


?>