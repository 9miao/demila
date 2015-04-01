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

if ($_GET ['controller'] != 'login') {
	admin_login ();
	
	if (isset ( $_GET ['m'] ) && ! isset ( $_SESSION ['user'] ['access'] [$_GET ['m']] )) {		
		if($_GET['m'] == 'users' && $_GET['c'] == 'edit' && $_GET['id'] == $_SESSION['user']['user_id']) {
			$personalEdit = 'yes';
			abr('personalEdit', $personalEdit);
		}
		else {
			refresh ( "/". $languageURL . adminURL . '/?access_error=' . $_GET ['m'], $langArray ['access_error'], 'error' );
		}
	}
}

abr ( "domain", $config ['domain'] );


?>