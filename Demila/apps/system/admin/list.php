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
_setTitle ( $langArray ['list'] );

	$cms = new system ( );

	$data = $cms->getAll(0, 0, 'config');

	
	$tmp = array();
	foreach($data AS $key => $value) {
		$value['help'] = isset($langArray[$value['key'] . '_help']) ? $langArray[$value['key'] . '_help'] : false;
		$tmp[$key] = $value;
	}
	
	abr('data', $tmp);
require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';

?>