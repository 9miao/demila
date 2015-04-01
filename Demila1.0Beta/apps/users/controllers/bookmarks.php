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

_setView(__FILE__);
_setTitle($langArray['book_marks']);

if(!check_login_bool()) {
		$_SESSION['temp']['golink'] = '/'.$languageURL.'user/bookmarks/';
		refresh('/'.$languageURL.'sign_in/');
}

	require_once ROOT_PATH.'/apps/collections/models/collections.class.php';
	$collectionsClass = new collections();
	
	if(isset($_POST['add'])) {
		$collectionsClass->add();
		refresh('/'.$languageURL.'user/bookmarks/', $langArray['complete_add_collection'], 'complete');
	}	
	
	$collections = $collectionsClass->getAll(0, 0, " `user_id` = '".intval($_SESSION['user']['user_id'])."' ", true);
	abr('collections', $collections);
	
	$usersClass = new users();
	
	$users = $usersClass->getAll(0, 0, $collectionsClass->usersWhere);
	abr('users', $users);

#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/bookmarks/" title="">'.$langArray['collections'].'</a>');		
	
	
?>