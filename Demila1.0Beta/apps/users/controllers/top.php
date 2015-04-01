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
_setTitle($langArray['top_authors']);

	$usersClass = new users();

	$limit = 60;
	$start = (PAGE-1)*$limit;
	
	abr('number', ($start+1));
	
	$users = $usersClass->getAll($start, $limit, " `items` > 0 AND `status` = 'activate' ", "`sales` DESC");
	abr('users', $users);
	
	abr('paging', paging('/'.$languageURL.'top_authors/?p=', '', PAGE, $limit, $usersClass->foundRows));
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/top/" title="">'.$langArray['top_authors'].'</a>');		
	

?>