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
_setTitle ( $langArray ['view'] );

	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		refresh('?m='.$_GET['m'].'&c=list', 'WRONG ID', 'error');
	}

	require_once ROOT_PATH . "/apps/bulletin/models/bulletin.class.php";
	$cms = new bulletin ( );
	
	$data = $cms->get($_GET['id']);
	
	if($data['send_to'] == 'city') {
		$cities = loadCities();
		$data['send_city'] = $cities[$data['send_id']]['name'];
	}
	elseif($data['send_to'] == 'group') {
		$bulletinGroupsClass = new bulletinGroups();
	
		$bGroup = $bulletinGroupsClass->get($data['send_id']);
		$data['send_group'] = $bGroup['name'];
	}
	
	abr('data', $data);
	
?>