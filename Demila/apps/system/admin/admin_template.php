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

_setView ( __FILE__ );
_setTitle ( $langArray ['template']);


$arr=getDirList(ROOT_PATH."/templates/admin");
abr("templates",$arr);

$template=$meta["admin_template"];
abr("template",$template);


require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';

?>