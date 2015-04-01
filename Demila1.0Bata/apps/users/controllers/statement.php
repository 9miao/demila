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
_setTitle($langArray['statement_setTitle']);

if(!check_login_bool()) {
	$_SESSION['temp']['golink'] = '/'.$languageURL.'statement/';
	refresh('/'.$languageURL.'sign_in/');
}

	if(!isset($_GET['month'])) {
		$_GET['month'] = date('m');
	}
	if(!isset($_GET['year'])) {
		$_GET['year'] = date('Y');
	}
	if(!checkdate($_GET['month'], 1, $_GET['year'])) {
		$_GET['month'] = date('m');
		$_GET['year'] = date('Y');
	}
	
	abr('download_csv_info', langMessageReplace($langArray['download_csv_info'], array('URL' => '/'.$languageURL.'users/statement/?month='.$_GET['month'].'&$year='.$_GET['year'].'&export')));
	
	$registrationDate = explode(' ', $_SESSION['user']['register_datetime']);
	$registrationDate = explode('-', $registrationDate[0]);
	abr('registrationDate', $registrationDate);
	
	$today['month'] = date('m');
	$today['year'] = date('Y')+1;
	abr('today', $today);
	
	$nav['prev']['month'] = date('m', mktime(0, 0, 0, ($_GET['month']-1), 1, $_GET['year']));
	$nav['prev']['year'] = date('Y', mktime(0, 0, 0, ($_GET['month']-1), 1, $_GET['year']));
	$nav['next']['month'] = date('m', mktime(0, 0, 0, ($_GET['month']+1), 1, $_GET['year']));
	$nav['next']['year'] = date('Y', mktime(0, 0, 0, ($_GET['month']+1), 1, $_GET['year']));
	if($nav['prev']['month'] < $registrationDate[1] && $nav['prev']['year'] <= $registrationDate[0]) {
		$nav['prev']['show'] = 'false';
	}
	else {
		$nav['prev']['show'] = 'true';
	}
	if($nav['next']['month'] > date('m') && $nav['next']['year'] >= date('Y')) {
		$nav['next']['show'] = 'false';
	}
	else {
		$nav['next']['show'] = 'true';
	}
	abr('nav', $nav);
	
    //获取资金流动记录
	$logClass = new transaction_details();
    $statement = $logClass->getRecord($_SESSION['user']['user_id']);

    #获取资金记录
	//require_once ROOT_PATH.'/apps/items/models/orders.class.php';
	//$ordersClass = new orders();
	
	//$statement = $ordersClass->getStatement($_SESSION['user']['user_id'], $_GET['month'], $_GET['year']);
	abr('statement', $statement);
	
	if(isset($_GET['export'])) {
		
		header('Content-Type: application/text/x-csv; charset=utf-8; encoding=utf-8');
		header('Content-Disposition: attachment; filename="stetement_'.$_GET['month'].'_'.$_GET['year'].'.csv"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-Transfer-Encoding: binary");
		header('Expires: 0');
		@ob_clean();
		@flush();
		
		if(is_array($statement)) {
			foreach($statement as $s) {
				echo '"'.date('d M Y', strtotime($s['datetime'])).'",';
  
				if($s['type'] == 'deposit')
	        echo '"'.$langArray['deposit'].'",';
	      elseif($s['type'] == 'withdraw')
	        echo '"'.$langArray['withdraw_money'].'",';
				elseif($s['type'] == 'order' && $s['owner_id'] == $_SESSION['user']['user_id'])
	        echo '"'.$langArray['receive_money'].'",';
				else
	        echo '"'.$langArray['purchase_money'].'",';
	        	
				if($s['type'] == 'deposit')
	        echo '"$ '.number_format($s['price'], 2).'",';
	      elseif($s['type'] == 'withdraw')
	        echo '"$ -'.number_format($s['price'], 2).'",';
				elseif($s['type'] == 'order' && $s['owner_id'] == $_SESSION['user']['user_id'])
	        echo '"$ '.number_format($s['receive'], 2).'",';
				else
	        echo '"$ -'.number_format($s['price'], 2).'",';
	        
				if($s['type'] == 'deposit')
	        echo '"'.$langArray['deposit_money'].'",';
	      elseif($s['type'] == 'withdraw')
	        echo '"'.$langArray['earning_money'].'",';
				elseif($s['type'] == 'order' && $s['owner_id'] == $_SESSION['user']['user_id']) {
					if($s['referal'] == 'buy') 
	        	echo '"'.$langArray['sold_item'].' '.$s['item_name'].'",';
	        else
	        	echo '"'.$langArray['referal_money'].'",';	        		
				}
				else
	        echo '"'.$langArray['buy_item'].' '.$s['item_name'].'",';
	        
	      echo "\n";	      
			}
		}
		die();
		
	}
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/dashboard/" title="">'.$langArray['my_account'].'</a> \ <a href="/'.$languageURL.'users/statement/" title="">'.$langArray['statement'].'</a>');		
	
	
?>