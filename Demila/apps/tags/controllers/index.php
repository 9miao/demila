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
_setLayout('clean');
	
	if(!isset($_GET['q'])) {
		$_GET['q'] = '';
	}
	if(!isset($_GET['limit'])) {
		$_GET['limit'] = 10;
	}

	$tagsClass = new tags();
	
	$tags = $tagsClass->getAll(0, $_GET['limit'], " `name` LIKE '%".sql_quote($_GET['q'])."%' ");
	if(is_array($tags)) {
		foreach($tags as $t) {
			echo $t['name']."\n";
		}
	}

?>