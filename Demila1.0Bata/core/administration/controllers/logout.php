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


if (isset ( $_SESSION ['user'] )) {
	unset ( $_SESSION ['user'] );
}

if (isset ( $_COOKIE ['user_id'] ) || isset ( $_COOKIE ['verifyKey'] )) {
	setcookie ( "user_id", "", time () - 2592000, "/", "." . $config ['domain'] );
	setcookie ( "verifyKey", "", time () - 2592000, "/", "." . $config ['domain'] );
}

refresh ( '/' . $languageURL . adminURL . '/login/' );

?>