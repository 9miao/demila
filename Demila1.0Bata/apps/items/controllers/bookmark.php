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

require_once ROOT_PATH.'/apps/collections/models/collections.class.php';
	$collectionsClass = new collections();
	
	if(check_login_bool() && isset($_POST['add_collection'])) {
		$s = $collectionsClass->bookmark($itemID);
		if($s === true) {
			refresh('/'.$languageURL.'items/'.$itemID, $langArray['complete_bookmark_item'], 'complete');
		}
		else {
			addErrorMessage($s, '还没有创建书签集', 'error');
		}
	}	
	
	if(check_login_bool()) {
		$collections = $collectionsClass->getAll(0, 0, " `user_id` = '".intval($_SESSION['user']['user_id'])."' ");
		abr('bookCollections', $collections);
	}
?>