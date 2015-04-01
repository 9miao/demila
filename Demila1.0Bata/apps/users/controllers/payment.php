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
_setTitle($langArray['make_payment_setTitle']);

$deposit_id = 0;
if(isset($_SESSION['tmp']['deposit_id'])) {
	$deposit_id = (int)$_SESSION['tmp']['deposit_id'];
}

require_once ROOT_PATH.'/apps/users/models/deposit.class.php';

$cms = new deposit();

$deposit_info = $cms->get($deposit_id);
//充值信息
if($deposit_info) {

	$payments = glob(dirname(dirname(dirname(__FILE__))) . '/payments/controllers/*.php');

	$payments_data = array();

//充值方式
	if($payments) {
		$order_obj = array();
		$key = 'chinabank';
		require_once ROOT_PATH.'/apps/payments/models/' . $key . '.class.php';
		$order_obj[$key] = new $key();
		
		$payments_data[$key] = array(
				'title' => '网银在线',
				'description' => '网银在线订单支付',
				'form' => $order_obj[$key]->generateDepositForm($deposit_info),
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
	addErrorMessage($langArray['deposit_is_expired'], '', 'error');
}