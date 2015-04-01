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
_setTitle($langArray['browse_categories_setTitle']);

	$categoryID = get_id(1);
	if(is_numeric($categoryID) || $categoryID == 'all') {
		


		require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
		$categoriesClass = new categories();
	
		$categories = $categoriesClass->getAll();
		abr('categories', $categories);		
			
	
		
	}	
	else {
		$allCategories = $categoriesClass->getAllWithChilds(0, " `visible` = 'true' ");
		$categoriesbrowseList = $categoriesClass->generatebrowseList($allCategories);
		abr('categoriesbrowseList', $categoriesbrowseList);		

	}
	
?>