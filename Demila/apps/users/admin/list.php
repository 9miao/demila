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
_setTitle ( $langArray ['list'] );

	if(isset($_POST['q'])) {
		$_GET['q'] = $_POST['q'];
	}
	if(!isset($_GET['q'])) {
		$_GET['q'] = '';
	}
	if(!isset($_GET['order'])) {
		$_GET['order'] = '';
	}
	if(!isset($_GET['dir'])) {
		$_GET['dir'] = '';
	}
	if(!isset($_GET['u_type'])) {
		$_GET['u_type'] = '';
	}

	$cms = new users ( );

	$whereQuery = '';
	if(trim($_GET['q']) != '') {
		$whereQuery = " `username` LIKE '%".sql_quote($_GET['q'])."%' ";
		switch($_GET['u_type']) {
			case 1:
				$whereQuery  .= "and `quiz` = 'false'";
				break;
			case 2:
				$whereQuery  .= "and `exclusive_author` = 'false' and `quiz`='true'";
				break;
			case 3:
				$whereQuery  .= "and `exclusive_author` = 'true' and `quiz`='true'";
				break;
			case 4:
				$whereQuery  .= "and `featured_author` = 'true' and `quiz`='true'";
				break;
		}
	}else{
		switch($_GET['u_type']) {
			case 1:
				$whereQuery  .= "`quiz` = 'false'";
				break;
			case 2:
				$whereQuery  .= "`exclusive_author` = 'false' and `quiz`='true'";
				break;
			case 3:
				$whereQuery  .= "`exclusive_author` = 'true' and `quiz`='true'";
				break;
			case 4:
				$whereQuery  .= "`featured_author` = 'true' and `quiz`='true'";
				break;
		}
	}

	
	
	$orderQuery = '';
	switch($_GET['order']) {
		case 'money': 
			$orderQuery = "`total`";
			break;
			
		case 'sales': 
			$orderQuery = "`sales`";
			break;
			
		case 'sold': 
			$orderQuery = "`sold`";
			break;
			
		case 'items': 
			$orderQuery = "`items`";
			break;
			
		case 'referals': 
			$orderQuery = "`referals`";
			break;
			
		case 'referal_money': 
			$orderQuery = "`referal_money`";
			break;
			
		default:
			$orderQuery = "`username`";
	}
	switch($_GET['dir']) {
		case 'desc':
			$orderQuery .= " DESC";
			abr('orderDir', 'asc');
			break;
		
		default:
			$orderQuery .= " ASC";
			abr('orderDir', 'desc');
	}

	
	
	$data = $cms->getAll(START, LIMIT, $whereQuery, $orderQuery); 
	
	if(is_array($data)) {
		#加载佣金分成
		require_once ROOT_PATH.'/apps/percents/models/percents.class.php';
		$percentsClass = new percents();
		#加载余额
		require_once ROOT_PATH.'/apps/users/models/balance.class.php';
		$balanceClass = new balance();
	
		$percents = $percentsClass->getAll();
		
		foreach($data as $k=>$d) {
			
			$comision = $percentsClass->getPercentRow($d);
			$data[$k]['commission'] = $comision['percent'];
			
//			if($data[$k]['commission_percent'] < 1) {
//				foreach($percents as $p) {
//					if($d['sold'] >= $p['from'] && ($d['sold'] < $p['to'] || $p['to'] == '0')) {
//						$data[$k]['commission'] = $p['percent'];
//						break;
//					}
//				}
//			} else {
//				$data[$k]['commission'] = $data[$k]['commission_percent'];
//			}
			$data[$k]['sum'] = $balanceClass->getTotalUserBalanceByType($d['user_id']);
		}		
	} 
	abr('data', $data);

	$p = paging ( "?m=" . $_GET ['m'] . "&c=list&p=", "&q=".$_GET['q']."&u_type=".$_GET['u_type']."&order=".$_GET['order']."&dir=".$_GET['dir'], PAGE, LIMIT, $cms->foundRows );
	abr ( 'paging', $p );
			
?>