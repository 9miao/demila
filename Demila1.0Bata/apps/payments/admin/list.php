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
	_setTitle ( $langArray ['payments']);
	
	$payments = glob(dirname(dirname(__FILE__)) . '/controllers/*.php');
	if(!is_array($payments)) $payments = array();

$tmp = array();
	
	$statuses = array(
		1 => $langArray['active'],
		0 => $langArray['unactive']
	);


    foreach($payments AS $row => $value) {
		$key = basename($value, '.php');
		
		if($value == '.' || $value == '..' || strpos($key, '_') !== false) continue;
		
		$status = $langArray['active'];

		
		$sort_order = 0;

		
		$tmp[] = array(
			'key' => $key,
			'title' => isset($langArray[$key]) ? $langArray[$key] : ucfirst($key),
			'status' => $status,
			'sort_order' => $sort_order
		);
	}

	abr('data', $tmp);
    $status="Alipay";
    if(isset($meta["use_demilapay"])){
        $status="Demilapay";
    }
    abr('status', $status);

?>