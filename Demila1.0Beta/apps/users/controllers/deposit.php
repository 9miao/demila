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
	_setTitle($langArray['deposit_cash_set']);
	
	$command = get_id(2);
	$depositID = get_id(3);
		
	if(!check_login_bool() && $command != 'success' && $command != 'notify') {
		$_SESSION['temp']['golink'] = '/'.$languageURL.'deposit/';
		refresh('/'.$languageURL.'sign_in/');
	}
	
	if(isset($_SESSION['tmp']['deposit_id']) && $_SESSION['tmp']['deposit_id']) {
		$_SESSION['tmp']['deposit_id'] = 0;
	}
	
	if($command == 'success' && $depositID) {
		$depositClass = new deposit();
		$info = $depositClass->get($depositID);
		if($info && $info['paid'] == 'true') {
			refresh('http://' . $config['domain'] . '/' . $languageURL . 'deposit/', $langArray['complete_deposit'], 'complete');
		} else {
			refresh('http://' . $config['domain'] . '/' . $languageURL . 'deposit/', $langArray['error_deposit'], 'error');
		}
	}
	
	if(isset($_POST['amount'])) {
		$depositClass = new deposit();
		$depositID = $depositClass->add();
		if($depositID !== FALSE) {
			if(isset($_SESSION['tmp']['order_id'])) {
				unset($_SESSION['tmp']['order_id']);
			}
			$_SESSION['tmp']['deposit_id'] = $depositID;
			refresh('/'.$languageURL.'users/payment/');
		}
	}
	
	#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/deposit/" title="">'.$langArray['deposit'].'</a>');		
	
	$discount = array();
	if($meta['prepaid_price_discount']) {
		if(strpos($meta['prepaid_price_discount'], '%')) {
			$discount = $meta['prepaid_price_discount'];
		} else {
			$discount = $currency['symbol'] . $meta['prepaid_price_discount'];
		}
	}
	abr('right_discount', $discount);

?>