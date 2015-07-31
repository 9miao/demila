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

	$key = get_id(1);
	
	$page = $pagesClass->getByKey($key);
	if(!is_array($page)) {
		header("HTTP/1.0 404 Not Found");
        header("Location: http://". DOMAIN ."/".$languageURL."error");	
	}
	abr('page', $page);


#设置元信息	
	if($page['meta_title'] != '') {
		$smarty->assign('title', $page['meta_title']);
	}
	else {
		$smarty->assign('title', $page['name']); 
	}
	
	if($page['meta_keywords'] != '') {
		$smarty->assign('meta_keywords', $page['meta_keywords']);
	}
	if($page['meta_description'] != '') {
		$smarty->assign('meta_description', $page['meta_description']);
	}

#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'pages/'.$page['key'].'.html" title="">'.$page['name'].'</a>');		
	
?>