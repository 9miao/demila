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

class session {
	
	function __construct($expire_time = '') {
		/*
		 * 修复错误
		 */
		if (! isset ( $_SERVER ['HTTP_USER_AGENT'] ) || $_SERVER ['HTTP_USER_AGENT'] == '') {
			//die ( '未设置用户代理!' );
		}
		
		session_start ();// or die ( "Session start error!" );
		
		return true;
	}
	
	function logout() {
		global $cache;
		/*
		 * @修复
		 */
		session_regenerate_id ();
		$_SESSION = array ();
		session_unset ();
		@session_destroy ();
		
		return true;
	}

}
?>