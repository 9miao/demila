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
_setTitle($langArray['lost_usernames_setTitle']);


if(check_login_bool()) {
	refresh('/'.$languageURL.'edit/');
}

	if(isset($_POST['send'])) {
		$usersClass = new users();
		
		$s = $usersClass->lostUsername();
		if($s === true) {
			refresh('/'.$languageURL.'lost_username/', $langArray['complete_send_username'], 'complete');
		}
		else {
			addErrorMessage($langArray[$s], '', 'error');
		}
	}
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/lost_username/" title="">'.$langArray['lost_username'].'</a>');		
	

?>