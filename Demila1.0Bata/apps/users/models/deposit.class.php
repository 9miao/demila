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


class deposit {
	
	public $foundRows = 0;
	public $usersWhere = '';	
	
	public function getAll($start=0, $limit=0, $where='') {
		global $mysql;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		if($where!='') {
			$where = " WHERE ".$where;
		}
		
		$mysql->query("
			SELECT *
			FROM `deposit`
			$where
			ORDER BY `datetime` DESC
			$limitQuery
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		while($d = $mysql->fetch_array()) {
			$return[] = $d;
		}
		
		return $return;
	}
	
	#通过订单ID获取用户充值详情
	public function get($id) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `deposit`
			WHERE `id` = '".intval($id)."'
		");

		return $mysql->fetch_array();
	}
	
	public function add() {
		global $mysql;
		
		if(!isset($_POST['amount']) || !is_numeric($_POST['amount'])) {
			return false;			
		}
		
		$mysql->query("
			INSERT INTO `deposit` (
				`user_id`,
				`deposit`,
				`datetime`				
			)
			VALUES (
				'".intval($_SESSION['user']['user_id'])."',
				'".sql_quote($_POST['amount'])."',
				NOW()
			)
		");
		
		return $mysql->insert_id();
	}
	
	//用户支付成功 订单完成
	public function depositIsPay($deposit_id,$recharge_type) {
		global $mysql, $langArray, $config;
		
		$row = $this->get($deposit_id);
		if($row) {

			//订单已成功
			if($row['paid'] == 'true') {
				return;
			}

			//对用户进行充值 添加到充值余额 同时增加总余额
			$mysql->query("
				UPDATE `users`
				SET `deposit` = `deposit` + '".sql_quote($row['deposit'])."',
						`total` = `total` + '".sql_quote($row['deposit'])."'
				WHERE `user_id` = '".intval($row['user_id'])."'
				LIMIT 1
			");

			//资金流动类
			require_once ROOT_PATH.'/apps/users/models/transaction_details.class.php';
			$logClass = new transaction_details();
            //记录资金流动(uid,type,value,info)
			if(floatval($row['deposit']) > 0){
			    $logClass->addRecord(intval($row['user_id']),'deposit',floatval($row['deposit']),$langArray['deposit_type'].':'.$recharge_type);
			}

			//改变充值订单状态
			$mysql->query("
				UPDATE `deposit`
				SET `paid` = 'true'								
				WHERE `id` = '".intval($deposit_id)."'
			");

			//检测用户登录状态 更新session
			if(isset($_SESSION['user'])) {
				$_SESSION['user']['deposit'] = floatval($_SESSION['user']['deposit']) + floatval($row['deposit']);
				$_SESSION['user']['total'] = floatval($_SESSION['user']['total']) + floatval($row['deposit']);
			}

			require_once ROOT_PATH.'/classes/history.class.php';
			$historyClass = new history();

			//添加到充值记录
			$historyClass->add($langArray['deposit_history'].$row['deposit'], $deposit_id, $row['user_id']);

            #CHECK REFERAL
			require_once ROOT_PATH.'/apps/users/models/users.class.php';
			$usersClass = new users();

			$user = $usersClass->get($row['user_id']);

			if($user['referal_id'] != '0') {
				//对推荐人进行分成  (暂时关闭对充值进行分成)
				//$this->referalMoney($row, $user);
				
			}
		}

	}
	


	//订单分成操作
	public function referalMoney($row, $user) {
		
		global $mysql;
		
		require_once ROOT_PATH.'/apps/users/models/users.class.php';
		$usersClass = new users();
		
		require_once ROOT_PATH.'/apps/system/models/system.class.php';
		$systemClass = new system();
		
		//获取推荐人在通过该用户购买作品获得到的分成次数
		$totals = $usersClass->getTotalReferals($user['user_id'], $user['referal_id']);
		
		//获取用户配置信息
		$configure = $systemClass->getAllKeyValue();
		
		//管理员设置了上限次数 且 所获分成次数达到上限
		if((int)$configure['referal_sum'] && ($totals+1) > (int)$configure['referal_sum']) {
			$mysql->query("
				UPDATE `users`
				SET `referal_id` = 0
				WHERE `user_id` = '".intval($user['user_id'])."'
				LIMIT 1 
			");
			return false;
		}
		
		//充值金额 * 分成比例
		$referalMoney = floatval($row['deposit']) * (int)$configure['referal_percent'] / 100;

		//对推荐人进行分成充值				
		$mysql->query("
			UPDATE `users`
			SET `earning` = `earning` + '".sql_quote($referalMoney)."',
					`total` = `total` + '".sql_quote($referalMoney)."',
					`referal_money` = `referal_money` + '".sql_quote($referalMoney)."'									
			WHERE `user_id` = '".intval($user['referal_id'])."'
			LIMIT 1
		");
		
		//将提成形成订单
		$mysql->query("
			INSERT INTO `orders` (
				`user_id`,
				`owner_id`,
				`item_id`,
				`item_name`,
				`price`,
				`datetime`,
				`receive`,
				`paid`,
				`paid_datetime`,
				`type`
			)
			VALUES (
				'".intval($user['user_id'])."',
				'".intval($user['referal_id'])."',
				'0',
				'deposit',
				'".sql_quote($row['deposit'])."',
				NOW(),
				'".sql_quote($referalMoney)."',
				'true',
				NOW(),
				'referal'
			)
		");
	}
	
	public function addWithdraw() {
		global $mysql, $langArray, $user;
		
		if(!isset($_POST['maximum_at_period_end']) || $_POST['maximum_at_period_end'] != 'true') {
			if(!isset($_POST['amount']) || !is_numeric($_POST['amount'])) {
				$error['amount'] = $langArray['wrong_amount'];
			}
			else {
				
				if(isset($_POST['service']) && $_POST['service'] == 'swift') {
					if($_POST['amount'] < 500) {
						$error['amount'] = $langArray['wrong_amount2'];
					}
					$maxAmount = $user['earning'] - 35;				
				}
				else {
					if($_POST['amount'] < 50) {
						$error['amount'] = $langArray['wrong_amount2'];
					}
					$maxAmount = $user['earning'];
				}
								
				if($_POST['amount'] > $maxAmount) {
					$error['amount'] = $langArray['wrong_amount2'];
				}
			}
		}
		
		if(!isset($_POST['service'])) {
			$error['service'] = $langArray['error_service'];
		}
		else {
			if($_POST['service'] == 'swift' && (!isset($_POST['instructions_from_author']) || trim($_POST['instructions_from_author']) == '')) {
				$error['service2'] = $langArray['error_details'];
			}
			elseif(!isset($_POST['payment_email_address']) || !isset($_POST['payment_email_address_confirmation']) || trim($_POST['payment_email_address']) == '' || $_POST['payment_email_address'] !== $_POST['payment_email_address_confirmation']) {
				$error['service2'] = $langArray['error_payment_email_address'];				
			}
		}

		if(isset($_POST['taxable_chinese_resident']) && $_POST['hobbyist'] == 'false' && trim($_POST['cbn']) == '' && trim($_POST['ccn']) == '') {
			$error['chinese'] = $langArray['error_chinese_resident'];
		}
		
		if(isset($error)) {
			return $error;
		}
		
		if(!isset($_POST['taxable_chinese_resident'])) {
			$_POST['taxable_chinese_resident'] = 'false';
		}
		else {
			if($_POST['hobbyist'] == 'true') {
				$_POST['taxable_chinese_resident'] = 'iam';
			}
			elseif($_POST['hobbyist'] == 'false') {			
				$_POST['taxable_chinese_resident'] = 'iamnot';
			}
		}
		
		if(!isset($_POST['cbn'])) {
			$_POST['cbn'] = '';
		}
		if(!isset($_POST['ccn'])) {
			$_POST['ccn'] = ''; 
		}
		
		$text = '';
		if($_POST['service'] == 'swift') {
			$text = $_POST['instructions_from_author'];
		}
		else {
			$text = $_POST['payment_email_address'];
		}
		
		if(isset($_POST['maximum_at_period_end']) && $_POST['maximum_at_period_end'] == 'true') {
			$_POST['amount'] = 'all to '.date('t M Y');
		}
		
		$mysql->query("
			INSERT INTO `withdraw` (
				`user_id`,
				`amount`,
				`method`,
				`text`,
				`chinese`,
				`cbn`,
				`ccn`,
				`datetime`
			)
			VALUES (
				'".intval($_SESSION['user']['user_id'])."',
				'".sql_quote($_POST['amount'])."',
				'".sql_quote($_POST['service'])."',
				'".sql_quote($text)."',
				'".sql_quote($_POST['taxable_chinese_resident'])."',
				'".sql_quote($_POST['cbn'])."',
				'".sql_quote($_POST['ccn'])."',
				NOW()
			)
		");
		
		return true;		
	}
	
	
	public function getWithdraws($start=0, $limit=0) {
		global $mysql;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		$mysql->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `withdraw`
			ORDER BY `datetime` DESC
			$limitQuery
		");
			
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		while($d = $mysql->fetch_array()) {
			$return[] = $d;
			
			if($this->usersWhere != '') {
				$this->usersWhere .= ' OR ';
			}
			$this->usersWhere .= " `user_id` = '".intval($d['user_id'])."' ";
		}
		
		$this->foundRows = $mysql->getFoundRows();
		
		return $return;
	}
	
	public function getWithdraw($id) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `withdraw`
			WHERE `id` = '".intval($id)."'
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return $mysql->fetch_array();
	}
	
	public function deleteWithdraw($id) {
		global $mysql;
		
		$row = $this->getWithdraw($id);
		if(!is_array($row) || $row['paid'] == 'true') {
			return true;
		}
		
		$mysql->query("
			DELETE FROM `withdraw`
			WHERE `id` = '".intval($id)."'
			LIMIT 1
		");
		
		return true;
	}
	
	//提现处理
	public function payoutWithdraw() {
		global $mysql, $langArray, $user, $data;
		
		if(!isset($_POST['payout']) || !is_numeric($_POST['payout']) || $_POST['payout'] < 1) {
			return $langArray['error_set_valid_sum'];
		}
		//判断金额
		if($_POST['payout'] > $user['earning']) {
			return $langArray['error_not_enought_money_earning'];
		}
		
		//查询该提现申请是否已经处理
		$mysql->query("
			SELECT COUNT(`id`) as count
			FROM `withdraw`
			WHERE `id` = '".intval($data['id'])."' and `paid`='true'
		");
		
		$r = $mysql->fetch_array();

		if($r['count'] > 0){
			return false;
		}else{
			//扣款
            $mysql->query("
				UPDATE `users`
				SET `earning` = `earning` - '".floatval($_POST['payout'])."',
						`total` = `total` - '".floatval($_POST['payout'])."'
				WHERE `user_id` = '".intval($user['user_id'])."'
				LIMIT 1
			");
			//改变状态
			$mysql->query("
				UPDATE `withdraw`
				SET `paid` = 'true',
						`paid_datetime` = NOW()
				WHERE `id` = '".intval($data['id'])."'
				LIMIT 1
			");
			//资金流动类
			require_once ROOT_PATH.'/apps/users/models/transaction_details.class.php';
			$logClass = new transaction_details();
			//记录资金流动(uid,type,value,info)
			if(floatval($_POST['payout']) > 0){
			    $logClass->addRecord(intval($user['user_id']),'withdraw',-floatval($_POST['payout']),$langArray['withdraw_method'].':'.$data['method']);
			}
			return true;
		}
	}
	
	
	public function getWithdrawCount($whereQuery) {
		global $mysql;
		
		if($whereQuery != '') {
			$whereQuery = " WHERE ".$whereQuery;
		}
		
		$mysql->query("
			SELECT COUNT(`id`) AS `num`, SUM(`amount`) AS `total`
			FROM `withdraw`
			$whereQuery
		");			
			
		return $mysql->fetch_array();
	}
	
}

?>