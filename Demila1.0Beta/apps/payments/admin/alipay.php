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
	_setTitle ( $langArray ['payments'] . ' › ' . ucfirst($_GET['c']) );
	
	$payments = scandir(dirname(dirname(__FILE__)) . '/controllers/');
	
	if(!in_array($_GET['c'] . '.php', $payments)) {
		refresh ( "/" . $languageURL . adminURL . "/?m=" . $_GET ['m'] . "&c=list" );
	}
	
	$key = $_GET['c'];
	
	$form = isset($_POST['form']) ? $_POST['form'] : array();
	
	if(isset($_POST['edit'])) {
		$cms = new system();
		$cms->editGroup ($key, $form);
		refresh ( "?m=" . $_GET ['m'] . "&c=list", $langArray ['edit_complete'] );
	}
	
	
	abr('group', $key);

	//pid
	if(isset($form[$key . '_v_mid'])) {
		abr('v_mid', $form[$key . '_v_mid']);
	} else {
		abr('v_mid', isset($meta[$key . '_v_mid']) ? $meta[$key . '_v_mid'] : '');
	}

    //appkey
    if(isset($form[$key . '_v_key'])) {
        abr('v_key', $form[$key . '_v_key']);
    } else {
        abr('v_key', isset($meta[$key . '_v_key']) ? $meta[$key . '_v_key'] : '');
    }
	//商户号
	if(isset($form[$key . '_v_num'])) {
		abr('v_num', $form[$key . '_v_num']);
	} else {
		abr('v_num', isset($meta[$key . '_v_num']) ? $meta[$key . '_v_num'] : '');
	}
	
	
	