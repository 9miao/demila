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
_setTitle ( $langArray ['add'] );

	$cms = new categories ( );
	
	if (isset ( $_POST ['add'] )) {
		$status = $cms->add ();
		if ($status !== true) {
			abr('error', $status);
		} else {
			refresh ( "?m=" . $_GET ['m'] . "&c=list", $langArray ['add_complete'] );
		}
	}
	else {
		$_POST['visible'] = 'true';
	}
	
#加载主类别
	$mysql->query("
		SELECT *
		FROM `categories`
		WHERE `sub_of` = '0'
		ORDER BY `order_index` ASC
	", __FUNCTION__ );
	
	if($mysql->num_rows() > 0) {
		while($d = $mysql->fetch_array()) {
			$categories[$d['id']] = $d;
		}		
		abr('categories', $categories);
	}
require_once ROOT_PATH.'/apps/lists/leftlist_admin.php';

?>