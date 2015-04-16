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
_setTitle($langArray['reset_password_setTitle']);


if(check_login_bool()) {
	refresh('/'.$languageURL.'edit/');
}

	if(isset($_POST['send'])) {
		$usersClass = new users();
		
		$s = $usersClass->changePassword();
		if($s === true) {
			refresh('/'.$languageURL.'reset_password/', $langArray['complete_reset_password'], 'complete');
		}
		else {
			addErrorMessage($langArray[$s], '', 'error');
		}
	}

#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/reset_password/" title="">'.$langArray['reset_password'].'</a>');		
	
	
?>