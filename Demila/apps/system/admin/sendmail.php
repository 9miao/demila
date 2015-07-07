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
_setTitle ( $langArray ['sendmail']);

$system = new system();

$smtp = $system ->is_smtp();

$is_smtp=false;

if($smtp){
    $is_smtp=true;
}

abr('is_smtp',$is_smtp);

require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';

?>