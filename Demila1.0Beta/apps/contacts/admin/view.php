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
_setTitle($langArray['view']);

	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		refresh('?m='.$_GET['m'].'&c=list', 'INVALID ID', 'error');
	}

	if(!isset($_GET['p'])) {
		$_GET['p'] = '';
	}	
	
	$cms = new contacts();
	
	$data = $cms->get($_GET['id']);
	
	if(isset($_POST['send'])) {
		$s = $cms->sendAnswer();
		if($s === true) {
			refresh('?m='.$_GET['m'].'&c=list', $langArray['complete_answer_issue'], 'complete');
		}
		else {
			addErrorMessage($langArray['error_answer_issue'], '', 'error');
		}
	}
	
	$_POST = $data;
	
?>