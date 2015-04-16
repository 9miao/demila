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

$orderID = 0;
if(isset($_SESSION['tmp']['order_id'])) {
	$orderID = (int)$_SESSION['tmp']['order_id'];
}

require_once ROOT_PATH.'/apps/items/models/orders.class.php';

$cms = new orders();

$order_info = $cms->get($orderID);


if($order_info) {
	
	$payments = glob(dirname(dirname(dirname(__FILE__))) . '/payments/controllers/*.php');
	
	$payments_data = array();

	
	if($payments) {
		$order_obj = array();
		$key = 'chinabank';
		require_once ROOT_PATH.'/apps/payments/models/' . $key . '.class.php';
		$order_obj[$key] = new $key();
		
		$payments_data[$key] = array(
				'title' => '网银在线',
				'description' => '网银在线订单支付',
				'form' => $order_obj[$key]->generateForm($order_info),
				'logo' => '',
		);
		
		if($payments_data) {
			abr('payments_data', $payments_data);			
		} else {
			addErrorMessage($langArray['no_payment_methods'], '', 'error');
		}
		
	} else {
		addErrorMessage($langArray['no_payment_methods'], '', 'error');
	}
	
} else {
	addErrorMessage($langArray['order_is_expired'], '', 'error');
}