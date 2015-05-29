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

header("Content-type:text/html;charset=utf-8");
_setView ( __FILE__ );
    _setTitle ( $langArray ['list'] );
	$cms = new app_extends();

    $extendsList=$cms->getAll();

    abr("data",$extendsList);

require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';

?>