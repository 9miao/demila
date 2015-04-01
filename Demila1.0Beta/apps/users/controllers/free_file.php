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
_setTitle($langArray['free_file']);

	$usersClass = new users();

	$limit = 60;
	$start = (PAGE-1)*$limit;
	
	abr('number', ($start+1));
	
	//$users = $usersClass->getAll($start, $limit, " `items` > 0 AND `status` = 'activate' ", "`sales` DESC");
	//免费作品
	$freeItem = $itemsClass->getAll($start, $limit, " `status` = 'active' AND `free_file` = 'true' ");
	abr('freeItem', $freeItem);
	
	abr('paging', paging('/'.$languageURL.'free_file/?p=', '', PAGE, $limit, $itemsClass->foundRows));
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/free_file/" title="">'.$langArray['free_file'].'</a>');		
	

?>