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
	
	if(!isset($_GET['q'])) {
		$_GET['q'] = '';
	}
	if(!isset($_GET['limit'])) {
		$_GET['limit'] = 10;
	}
    if(!is_int($_GET['limit'])) die("Illegal Operation !");
	$tagsClass = new tags();
	$tags = $tagsClass->getAll(0, $_GET['limit'], " `name` LIKE '%".sql_quote($_GET['q'])."%' ");
	if(is_array($tags)) {
		foreach($tags as $t) {
			echo $t['name']."\n";
		}
	}

?>