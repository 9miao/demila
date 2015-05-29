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
_setTitle ( $langArray ['attributes'] );

	if(!isset($_GET['id']) && !is_numeric($_GET['id'])) {
		refresh("/" . $languageURL . adminURL . "/?m=" . $_GET ['m'] . "&c=list");
	}

	$cms = new attributes ( );

	if(isset($_GET['up']) || isset($_GET['down'])) {
		$cms->tableName = 'attributes';
		$cms->idColumn = 'id';
		$cms->orderWhere = " AND `category_id` = '".intval($_GET['id'])."' ";
	
		if(isset($_GET['up']) && is_numeric($_GET['up'])) {
			$cms->moveUp($_GET['up']);
		}
		elseif(isset($_GET['down']) && is_numeric($_GET['down'])) {
			$cms->moveDown($_GET['down']);
		}
	}
	
	$data = $cms->getAll(START, LIMIT, " `category_id` = '".intval($_GET['id'])."' ");
	abr('data', $data);

	$p = paging ( "?m=" . $_GET ['m'] . "&c=attr&id=".$_GET['id']."&p=", "", PAGE, LIMIT, $cms->foundRows );
	abr ( 'paging', $p );
	
	$categoriesClass = new categories();
	$pdata = $categoriesClass->get($_GET['id']);
	abr('pdata', $pdata);
require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';

?>