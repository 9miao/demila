<?php
_setView ( ROOT_PATH . "/apps/" . $_GET ['m'] . "/admin/addservice.php" );
_setTitle ( $langArray ['edit'] );

	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		refresh('?m='.$_GET['m'].'&c=service', 'INVALID ID', 'error');
	}

	$cms = new service();
	
	if(isset($_POST['edit'])) {
		
		$status = $cms->edit ($_GET['id']);
		if ($status !== true) {
			abr('error', $status);
		} else {
			refresh ( "?m=" . $_GET ['m'] . "&c=service", $langArray ['edit_complete'] );
		}
	}else {
		$user_info = $cms->get($_GET['id']);
		abr('user_info',$user_info);
	}
?>