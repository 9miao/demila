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
_setTitle($langArray['collections']);

	$collectionsClass = new collections();
	
	$limit = 20;
	$start = (PAGE-1)*$limit;
	
	$order = '';
	if(!isset($_GET['sort_by'])) {
		$_GET['sort_by'] = '';
	}
	switch($_GET['sort_by']) {
		case 'name':
			$order = '`name`';
			break;
		case 'average_rating':
			$order = '`rating`';
			break;
		
		default:
			$order = '`datetime`';
			break;
	}
	if(!isset($_GET['order']) || $_GET['order'] == '' || $_GET['order'] == 'desc') {
		$_GET['order'] = 'desc';
		$order .= ' DESC';
	}
	else {
		$_GET['order'] = 'asc';
		$order .= ' ASC';
	}
	
	$collections = $collectionsClass->getAll(START, LIMIT, " `public` = 'true' ", false, $order);
	if(is_array($collections)) {
		
		require_once ROOT_PATH.'/apps/users/models/users.class.php';
		$usersClass = new users();
		
		$users = $usersClass->getAll(0, 0, $collectionsClass->usersWhere);
		abr('users', $users);
				
	}
	abr('collections', $collections);

	abr('paging', paging('/'.$languageURL.'collections/?p=', '&sort_by='.$_GET['sort_by'].'&order='.$_GET['order'], PAGE, $limit, $collectionsClass->foundRows));
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'collections/" title="">'.$langArray['public_collections'].'</a>');		
	
	
?>