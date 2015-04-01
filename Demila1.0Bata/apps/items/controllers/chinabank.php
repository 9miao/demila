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


$key=$config['v_key'];

$v_oid     =trim($_POST['v_oid']);       // 商户发送的v_oid定单编号
$v_pmode   =trim($_POST['v_pmode']);    // 支付方式（字符串）
$v_pstatus =trim($_POST['v_pstatus']);   //  支付状态 ：20（支付成功）；30（支付失败）
$v_pstring =trim($_POST['v_pstring']);   // 支付结果信息 ： 支付完成（当v_pstatus=20时）；失败原因（当v_pstatus=30时,字符串）；
$v_amount  =trim($_POST['v_amount']);     // 订单实际支付金额
$v_moneytype  =trim($_POST['v_moneytype']); //订单实际支付币种
$remark1   =trim($_POST['remark1' ]);      //备注字段1
$remark2   =trim($_POST['remark2' ]);     //备注字段2
$v_md5str  =trim($_POST['v_md5str' ]);   //拼凑后的MD5校验值

 
$order_id = $remark1;//订单id

$md5string=strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$key));
if ($v_md5str==$md5string)
{
	if($v_pstatus=="20")
	{
		echo '支付成功，可进行逻辑处理！';
		require_once ROOT_PATH.'/apps/items/models/orders.class.php';
		$cms = new orders();
		$order_info = $cms->get($order_id);
		if($order_info) {
			$cms->orderIsPay($order_id,'网银在线支付');//订单成功
			refresh('http://' . $config['domain'] . '/' . $languageURL . 'users/downloads/');//下载页面				
		}
	}else{
		echo "支付失败";
	}

}










