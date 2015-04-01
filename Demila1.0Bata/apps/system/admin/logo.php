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
	_setTitle ( $langArray ['edit'].' '.$langArray['site_logo'] );
	
	$cms = new system ( );
	
	$get_info = $cms->getLogo();
	
	if(isset($_POST['edit'])) {
		$status = $cms->editLogo ();
		if ($status !== true) {			
			abr('error', $status);
		} else {
			refresh ( "?m=" . $_GET ['m'] . "&c=logo", $langArray ['edit_complete'] );
		}
	}
	else {
		$_POST = $get_info;
	}
	
				
?>