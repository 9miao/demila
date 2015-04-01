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


class orders {
	
	public $row = '';
	public $whereQuery = '';
	public $usersWhere = '';
	
	public function getAll($where='', $order='`paid_datetime` DESC') {
		global $mysql;
		
		if($where!='') {
			$where = " WHERE ".$where;
		}
		
		$mysql->query("
			SELECT *
			FROM `orders`
			$where
			ORDER BY $order		
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
	
	//根据订单id获取订单详情
	public function get($id) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `orders`
			WHERE `id` = '".intval($id)."'
		");		
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return $mysql->fetch_array();
	}
	
	public function add($price, $extended='false') {
		global $mysql, $item;
		
		$mysql->query("
			INSERT INTO `orders` (
				`user_id`,
				`owner_id`,
				`item_id`,
				`item_name`,
				`price`,
				`datetime`,
				`extended`
			)
			VALUES (
				'".intval($_SESSION['user']['user_id'])."',
				'".intval($item['user_id'])."',
				'".intval($item['id'])."',
				'".sql_quote($item['name'])."',
				'".sql_quote($price)."',
				NOW(),
				'".sql_quote($extended)."'
			)
		");
		
		return $mysql->insert_id();
	}
	
	/* v 1.0 */
	public function IsPay($order_id) {
		$row = $this->get($order_id);
		if(!is_array($row)) {
			return false;
		}
		return $row['paid'] == 'true' ? true : false;
	}
	
	public function orderIsPay($order_id,$recharge_type) {
		global $mysql, $langArray, $config;
		
		//资金流动类
		require_once ROOT_PATH.'/apps/users/models/transaction_details.class.php';
		$logClass = new transaction_details();

		//根据订单号获取订单
		$row = $this->get($order_id);
		if(!is_array($row)) {
			return 'error_paing'; //ERROR
		}
					
		#LOAD USERS SOLD					
		require_once ROOT_PATH.'/apps/users/models/users.class.php';
		$usersClass = new users();
		
		//根据作品作者id获取作者详情			
		$user = $usersClass->get($row['owner_id']);
					
		#GET PERCENT
		require_once ROOT_PATH.'/apps/percents/models/percents.class.php';
		$percentsClass = new percents();
		
		//获取用户分成比例	（作者）	
		$percent = $percentsClass->getPercentRow($user);
		//分成比
		$percent = $percent['percent'];
		//分成金额			
		$receiveMoney = floatval($row['price']) * floatval($percent) / 100;
		
		//改变订单状态 更新作品作者分成
		$mysql->query("
			UPDATE `orders`
			SET `paid` = 'true',
				`paid_datetime` = NOW(),
				`receive` = '".sql_quote($receiveMoney)."'
			WHERE `id` = '".intval($order_id)."'
		");
		
		//充值至账户 更新销售量			
		$mysql->query("
			UPDATE `users`
			SET `earning` = `earning` + '".sql_quote($receiveMoney)."',
				`total` = `total` + '".sql_quote($receiveMoney)."',
				`sold` = `sold` + '".floatval($row['price'])."',
				`sales` = `sales` + 1
			WHERE `user_id` = '".intval($row['owner_id'])."'
			LIMIT 1
		");

		//记录资金流动(uid,type,value,info)
		if($referalMoney > 0){
		    $logClass->addRecord(intval($row['owner_id']),'sale_income',floatval($referalMoney),$langArray['item_name'].':'.$row['item_name']);
		}

		//获取购买用户信息		
		$you = $usersClass->get($row['user_id']);

		#是否存在推荐人				
		if($you['referal_id'] != '0') {
						
			$this->referalMoney($row, $you);
						
		}
					
		//累计购买过作品的数量
		$mysql->query("
			UPDATE `users`
			SET `buy` = `buy` + 1
			WHERE `user_id` = '".intval($row['user_id'])."'
			LIMIT 1 
		");
					
		//在售作品数量
		$setQuery = '';
		if($row['extended'] == 'true') {
			$setQuery = " `status` = 'extended_buy', ";
			$mysql->query("
				UPDATE `users`
				SET `items` = `items` - 1
				WHERE `user_id` = '".intval($row['owner_id'])."'
				LIMIT 1 
			");
		}
		
		//作品销售次数+1		
		$mysql->query("
			UPDATE `items`
			SET `sales` = `sales` + 1,
				$setQuery
				`earning` = `earning` + '".sql_quote($row['price'])."'
			WHERE `id` = '".intval($row['item_id'])."'
		");
					
		return true;
	}
	/* End v1.0 */
	
	
	//支付佣金
	public function referalMoney($row, $you) {
		global $mysql,$langArray;
		
		require_once ROOT_PATH.'/apps/users/models/users.class.php';
		$usersClass = new users();
		
		require_once ROOT_PATH.'/apps/system/models/system.class.php';
		$systemClass = new system();
		
		//返回推广者推荐从此用户获佣金次数
		$totals = $usersClass->getTotalReferals($you['user_id'], $you['referal_id']);
		
		//获取系统配置
		$configure = $systemClass->getAllKeyValue();
		
		
		//在此用户获取佣金上限 以后不会在此用户上获取佣金提成
		if((int)$configure['referal_sum'] && ($totals+1) > (int)$configure['referal_sum']) {
			$mysql->query("
				UPDATE `users`
				SET `referal_id` = 0
				WHERE `user_id` = '".intval($you['user_id'])."'
				LIMIT 1 
			");
			return false;
		}
		
		//可获佣金提成
		$referalMoney = floatval($row['price']) * (int)$configure['referal_percent'] / 100;
		
		//充值至用户			
		$mysql->query("
			UPDATE `users`
			SET `earning` = `earning` + '".sql_quote($referalMoney)."',
					`total` = `total` + '".sql_quote($referalMoney)."',
					`referal_money` = `referal_money` + '".sql_quote($referalMoney)."'
			WHERE `user_id` = '".intval($you['referal_id'])."'
			LIMIT 1
		");

		//资金流动类
		require_once ROOT_PATH.'/apps/users/models/transaction_details.class.php';
		$logClass = new transaction_details();
		//记录流水
		if($referalMoney > 0){
		    $logClass->addRecord(intval($you['referal_id']),'referal_income',floatval($referalMoney),$langArray['item_name'].':'.$row['item_name']);
		}

		//佣金记录形成订单
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
				'".intval($row['user_id'])."',
				'".intval($row['owner_id'])."',
				'".intval($row['item_id'])."',
				'".sql_quote($row['item_name'])."',
				'".sql_quote($row['price'])."',
				NOW(),
				'".sql_quote($referalMoney)."',
				'true',
				NOW(),
				'referal'
			)
		");
		
		$mysql->query("
			INSERT INTO `users_referals_count` (
				`user_id`,
				`referal_id`,
				`datetime`
			)
			VALUES (
				'".intval($you['user_id'])."',
				'".intval($you['referal_id'])."',
				NOW()
			)
		");
	}
	
	//使用余额购买作品
	public function buy($price, $extended=false) {
		global $mysql, $langArray, $item;
		
		//资金流动类
		require_once ROOT_PATH.'/apps/users/models/transaction_details.class.php';
		$logClass = new transaction_details();
		
		require_once ROOT_PATH.'/apps/users/models/users.class.php';
		$usersClass = new users();
		//获取当前购买用户详细信息
		$you = $usersClass->get($_SESSION['user']['user_id']);
		
		$deposit = 0;
		$earning = 0;
        
        //用户实际支付金额
        $t_pay_price = floatval($price)-floatval($item['your_profit']);
        $t_pay_price > 0 ? $t_pay_price : $t_pay_price = 0;
		//账号余额是否充足
		if($you['deposit'] >= $t_pay_price) {
			$deposit = $t_pay_price;
		}
		else {
			$deposit = $you['deposit'];
			$earning = floatval($t_pay_price) - floatval($you['deposit']);
		}

		
        //扣款购买
		$mysql->query("
			UPDATE `users`
			SET `deposit` = `deposit` - '".floatval($deposit)."',
					`earning` = `earning` - '".floatval($earning)."',
					`total` = `total` - '".floatval($t_pay_price)."'
			WHERE `user_id` = '".intval($you['user_id'])."'
			LIMIT 1
		");

		//记录资金流动(uid,type,value,info)
		if(floatval($t_pay_price) > 0){
		    $logClass->addRecord(intval($you['user_id']),'buy',-floatval($t_pay_price),$langArray['item_name'].':'.$item['name'].'('.$langArray['pay_buy_balance'].')');
		}
		
		$_SESSION['user']['deposit'] = floatval($_SESSION['user']['deposit']) - floatval($deposit);
		$_SESSION['user']['earning'] = floatval($_SESSION['user']['earning']) - floatval($earning);
		$_SESSION['user']['total'] = floatval($_SESSION['user']['total']) - floatval($t_pay_price);
		
        //对推广用户进行返佣金
		if($you['referal_id'] != '0') {
			
			$this->referalMoney(array(
				'price' => $price,
				'user_id' => $_SESSION['user']['user_id'],
				'owner_id' => $item['user_id'],
				'item_id' => $item['id'],
				'item_name' => $item['name']
			), $you);
			
		}

        //打款给作者
		$user = $usersClass->get($item['user_id']);
		
		require_once ROOT_PATH.'/apps/percents/models/percents.class.php';
		$percentsClass = new percents();
		//获取用户分成比例（作者）
		$percent = $percentsClass->getPercentRow($user);
		$percent = $percent['percent'];
		//作者所得分成
		$receiveMoney = floatval($price) * floatval($percent) / 100;
					
		$mysql->query("
			UPDATE `users`
			SET `earning` = `earning` + '".floatval($receiveMoney)."',
					`total` = `total` + '".floatval($receiveMoney)."',
					`sold` = `sold` + '".floatval($price)."',
					`sales` = `sales` + 1
			WHERE `user_id` = '".intval($user['user_id'])."'
			LIMIT 1
		");

		//记录资金流动(uid,type,value,info)
		if(floatval($receiveMoney) > 0){
		    $logClass->addRecord(intval($user['user_id']),'sale_income',floatval($receiveMoney),$langArray['item_name'].':'.$item['name']);
		}
		
#添加订单
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
				`paid_datetime`
			)
			VALUES (
				'".intval($_SESSION['user']['user_id'])."',
				'".intval($item['user_id'])."',
				'".intval($item['id'])."',
				'".sql_quote($item['name'])."',
				'".sql_quote($price)."',
				NOW(),
				'".sql_quote($receiveMoney)."',
				'true',
				NOW()
			)
		");	
		
		$mysql->query("
			UPDATE `users`
			SET `buy` = `buy` + 1
			WHERE `user_id` = '".intval($_SESSION['user']['user_id'])."'
			LIMIT 1 
		");
		
#更新作品
		$setQuery = '';
		if($extended) {
			$setQuery = " `status` = 'extended_buy', ";
		}
			
		$mysql->query("
			UPDATE `items`
			SET `sales` = `sales` + 1,
					$setQuery
					`earning` = `earning` + '".sql_quote($price)."'
			WHERE `id` = '".intval($item['id'])."'
		");		

		return true;
	}
	
	//判断用户是否购买过该作品
	public function isBuyed($id) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `orders`
			WHERE `user_id` = '".intval($_SESSION['user']['user_id'])."' AND `item_id` = '".intval($id)."'  AND paid = 'true' AND `type`='buy'
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$this->row = $mysql->fetch_array();
		
		return true;
	}
	
	
	public function getAllBuyed($where='') {
		global $mysql;
		
		if($where!='') {
			$where = " WHERE ".$where;
		}
		
		$mysql->query("
			SELECT *
			FROM `orders`
			$where
			ORDER BY `paid_datetime` DESC
		");
			
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$whereQuery = '';
		while($d = $mysql->fetch_array()) {
			if($this->whereQuery != '') {
				$this->whereQuery .= " OR ";
			}
			$this->whereQuery .= " `id` = '".intval($d['item_id'])."' ";
		}
		
		$mysql->query("
			SELECT *
			FROM `items`
			WHERE $this->whereQuery
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
	
	
	public function getWeekStats() {
		global $mysql;
		
		$buff = 6 - date('N');	
		$lastWeekDay = date('Y-m-d', mktime(0, 0, 0, date('m'), (date('d') - $buff - date('N')), date('Y')));
		$firstWeekDay = date('Y-m-d', mktime(0, 0, 0, date('m'), (date('d') + $buff + 2), date('Y')));
		
//		echo $lastWeekDay.' '.$firstWeekDay;
		
		$mysql->query("
			SELECT *
			FROM `orders`
			WHERE `owner_id` = '".intval($_SESSION['user']['user_id'])."' AND `paid` = 'true' AND `datetime` > '".$lastWeekDay."' AND `datetime` < '".$firstWeekDay."'
		");
		
		$weekStats = array('earning' => 0, 'sold' => 0);
		
		if($mysql->num_rows() == 0) {
			return $weekStats;
		}
		
		while($d = $mysql->fetch_array()) {
			$weekStats['sold']++;
			$weekStats['earning'] += $d['receive'];
		}
		
		return $weekStats;
	}
	
	
	public function getStatement($userID, $month, $year) {
		global $mysql;
		
		$lastMonth = date('Y-m-d 23:59:59', mktime(0, 0, 0, ($month-1), date('t', mktime(0, 0, 0, ($month-1), 1, $year)), $year));
		$nextMonth = date('Y-m-d 00:00:00', mktime(0, 0, 0, ($month+1), 1, $year));	
		
		$mysql->query("
			(
				SELECT `user_id`, `owner_id`, `price`, `receive`, `paid_datetime` as `datetime`, `item_name`, `type` as `referal`, CONCAT('order') as `type`
				FROM `orders`
				WHERE (`owner_id` = '".intval($userID)."' OR `user_id` = '".intval($userID)."') AND `paid` = 'true' AND `paid_datetime` > '".$lastMonth."' AND `paid_datetime` < '".$nextMonth."'
			)
			UNION		
			(
				SELECT `user_id`, CONCAT('') as `owner_id`, `deposit` as `price`, CONCAT('') as `receive`, `datetime`, CONCAT('') as `item_name`, CONCAT('') as `referal`, CONCAT('deposit') as `type`
				FROM `deposit`
				WHERE `user_id` = '".intval($userID)."' AND `paid` = 'true' AND `datetime` > '".$lastMonth."' AND `datetime` < '".$nextMonth."'
			)
			UNION
			(
				SELECT `user_id`, CONCAT('') as `owner_id`, `amount` as `price`, CONCAT('') as `receive`, `datetime`, CONCAT('') as `item_name`, CONCAT('') as `referal`, CONCAT('withdraw') as `type`
				FROM `withdraw`
				WHERE `user_id` = '".intval($userID)."' AND `paid` = 'true' AND `datetime` > '".$lastMonth."' AND `datetime` < '".$nextMonth."'
			)
			ORDER BY `datetime` DESC
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
	
	
	
	public function getTopSellers($start=0, $limit=0, $where='') {
		global $mysql;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		$mysql->query("
			SELECT *, COUNT(`item_id`) AS `sales` 
			FROM `orders`
			WHERE `type` = 'buy' AND `paid` = 'true' $where
			GROUP BY `item_id`
			ORDER BY `sales` DESC
			$limitQuery
		");
			
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		$whereQuery = '';
		while($d = $mysql->fetch_array()) {
			$return[$d['item_id']] = $d;
			
			if($whereQuery != '') {
				$whereQuery .= ' OR ';
			}
			$whereQuery .= " `id` = '".intval($d['item_id'])."' ";
		}
		
		
		
		$mysql->query("
               SELECT *
               FROM `items`
               LEFT JOIN `items_to_category` as ic ON `ic`.`item_id` = `items`.`id`  
               WHERE $whereQuery
         ");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$this->usersWhere = '';
		while($d = $mysql->fetch_array()) {
			$d['sales'] = $return[$d['id']]['sales'];
			
			$categories = explode('|', $d['categories']);
			unset($d['categories']);
			$d['categories'] = array();
			$row=0;
			foreach($categories AS $cat) {
				$categories1 = explode(',', $cat);
				foreach($categories1 as $c) {
					$c = trim($c);
					if($c != '') {
						$d['categories'][$row][$c] = $c;
					}
				}
				$row++;
			}
			
			$return[$d['id']] = $d;
			
			if($this->usersWhere != '') {
				$this->usersWhere .= ' OR ';
			}
			$this->usersWhere .= " `user_id` = '".intval($d['user_id'])."' ";
		}
		
		return $return;
	}
	
	public function getTopAuthors($start=0, $limit=0, $where='') {
		global $mysql;
		
		$limitQuery = '';
		if($limit!=0) {
			$limitQuery = " LIMIT $start,$limit ";
		}
		
		$mysql->query("
			SELECT *, COUNT(`owner_id`) AS `sales` 
			FROM `orders`
			WHERE `type` = 'buy' AND `paid` = 'true' $where
			GROUP BY `owner_id`
			ORDER BY `sales` DESC
			$limitQuery
		");
			
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		$whereQuery = '';
		while($d = $mysql->fetch_array()) {
			$return[$d['owner_id']] = $d;
			
			if($whereQuery != '') {
				$whereQuery .= ' OR ';
			}
			$whereQuery .= " `user_id` = '".intval($d['owner_id'])."' ";
		}
		
		$mysql->query("
			SELECT *
			FROM `users`
			WHERE $whereQuery
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		while($d = $mysql->fetch_array()) {
			$d['sales'] = $return[$d['user_id']]['sales'];
			$return[$d['user_id']] = $d;
		}
		
		return $return;
	}
	
	public function isItemBuyed($itemID, $usersWhere) {
		global $mysql;
		
		$mysql->query("
			SELECT *
			FROM `orders`
			WHERE `item_id` = '".intval($itemID)."' AND `type` = 'buy' AND `paid` = 'true' AND ($usersWhere)			
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$return = array();
		while($d = $mysql->fetch_array()) {
			$return[$d['user_id']] = $d;
		}
		
		return $return;
	}
	
	
	public function getSalesStatus($whereQuery='', $type='buy') {
		global $mysql;

		$mysql->query("
			SELECT COUNT(`id`) as `num`, SUM(`price`) as `total`, SUM(`receive`) AS `receive` 
			FROM `orders` 
			WHERE `type` = '".sql_quote($type)."' AND `paid` = 'true' $whereQuery 
			GROUP BY `type`
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		return $mysql->fetch_array();
	}
	
	public function getSalesStatusByDay($whereQuery='', $type='buy') {
		global $mysql;
		
		$mysql->query("
			SELECT COUNT(`id`) as `num`, SUM(`price`) as `total`, SUM(`receive`) AS `receive` , DATE(`datetime`) AS `date`
			FROM `orders` 
			WHERE `type` = '".sql_quote($type)."' AND `paid` = 'true' $whereQuery 
			GROUP BY DATE(`datetime`)
			ORDER BY DATE(`datetime`) ASC
		");
		
		if($mysql->num_rows() == 0) {
			return false;
		}
		
		$data = array();
		while ($d = $mysql->fetch_array()) {
			$data[$d['date']] = $d;
		}
		
		return $data;
	}
	
}

?>