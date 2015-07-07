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
_setTitle ( "SMTP");

$system = new system();
$data=$system->getAll(0,0,'mailconf');
$arr=array();
foreach($data as $a){
    $arr[$a["key"]]=$a["value"];
}

abr('data',$arr);

if(isset($_POST["edit"])){
    if($system->smtp_edit($_POST))
    {
        refresh ( "?m=" . $_GET ['m'] . "&c=smtpset", $langArray ['edit_complete'] );
    }

}


require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';



