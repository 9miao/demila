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

	$collectionID = get_id(2);

	$collectionsClass = new collections();
	
	$collection = $collectionsClass->get($collectionID);
	if(!is_array($collection) || ($collection['public'] == 'false' && check_login_bool() && $collection['user_id'] != $_SESSION['user']['user_id'])) {
		refresh('/'.$languageURL.'collections/', $langArray['wrong_collection'], 'error');
	}
	
	if(isset($_POST['rating'])) {
		$_GET['rating'] = $_POST['rating'];
	}
	
	if(!isset($_GET['rating']) || !is_numeric($_GET['rating']) || $_GET['rating'] > 5) {
		$_GET['rating'] = 5;
	}
	elseif($_GET['rating'] < 1) {
		$_GET['rating'] = 1;
	}
	
	$collection = $collectionsClass->rate($collectionID, $_GET['rating']);

	$stars = '';
	for($i=1;$i<6;$i++) {
		if($collection['rating'] >= $i) {
			$stars .= '<img src="/static/img/star-on.png" alt="" class="left" />';
		}
		else {
			$stars .= '<img src="/static/img/star-off.png" alt="" class="left" />';
		}
	}
	
	die('
		'.$stars.'	
	');

?>