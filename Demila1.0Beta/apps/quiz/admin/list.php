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
_setTitle ( $langArray ['list'] );

	$cms = new quiz ( );
	
	if(isset($_GET['up']) || isset($_GET['down'])) {
		$cms->tableName = 'quiz';
		$cms->idColumn = 'id';
	
		if(isset($_GET['up']) && is_numeric($_GET['up'])) {
			$cms->moveUp($_GET['up']);
		}
		elseif(isset($_GET['down']) && is_numeric($_GET['down'])) {
			$cms->moveDown($_GET['down']);
		}
	}
	
	$data = $cms->getAll(START, LIMIT);
	abr('data', $data);

	$p = paging ( "?m=" . $_GET ['m'] . "&c=list&p=", "", PAGE, LIMIT, $cms->foundRows );
	abr ( 'paging', $p );
?>