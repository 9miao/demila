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

//上传队列审核
require_once ROOT_PATH.'/apps/items/models/items.class.php';
$items=new items();
$queue = $items->getAll(0, 0, " `status` = 'queue' ");
$queueCount=0;
if($queue)$queueCount=count($queue);
abr('queueCount', $queueCount);

//更新队列审核
$update = $items->getAllForUpdate();
$updateCount=0;
if($update)$updateCount=count($update);
abr('updateCount', $updateCount);

//提现队列审核
require_once ROOT_PATH.'/apps/users/models/deposit.class.php';
$deposit = new deposit ();
$deposit = $deposit->getWithdraws('','',' paid = "false"');
$depositCount=0;
if($deposit)$depositCount=count($deposit);
abr('depositCount', $depositCount);

//联系支持处理
require_once ROOT_PATH.'/apps/contacts/models/contacts.class.php';
$contacts=new contacts ();
$contacts = $contacts->getAll(0, 0);
$contactsCount=0;
if($contacts)$contactsCount=count($contacts);
abr('contactsCount', $contactsCount);

//举报评论处理
require_once ROOT_PATH.'/apps/items/models/comments.class.php';
$commentsClass = new comments();
$comments = $commentsClass->getAll(0, 0, " `report_by` <> '0' ");
$commentCount=0;
if($comments)$commentCount=count($comments);
abr('commentCount', $commentCount);

