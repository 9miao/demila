<?php

require_once ROOT_PATH.'/apps/payments/models/payments_abstract.class.php';

class chinabank extends payments_abstract {

	public function generateForm($order_data = array()) {
		global $langArray, $currency, $config, $languageURL,$meta;

		/*
            支付宝支付配置
		*/
		$v_amount = $order_data['price'];//支付金额
        //判断是否使用代理支付
        if(isset($meta["use_demilapay"])){
            $a_oid = '0_'.$order_data['id'].'_'.$meta["demilapay_key"];//订单号

            $order_name = $order_data['item_name'];
            $order_id = $order_data['item_id'];
            $o_url = 'http://pay.demila.org/index.php/Home/Alipay';//demilapay
            $show_url = 'http://'.$config['domain'].'/items/'.$order_id.'/'.$order_name;
            $o_info = strtoupper(md5($v_amount.$o_url.$a_oid));//md5加密拼凑串,注意顺序不能变
            //构造表单
            $form[0] = '
		<form method="post" name="E_FORM" action="'.$o_url.'">
			<input type="hidden" name="WIDout_trade_no"  value="'.$a_oid.'" />
			<input type="hidden" name="WIDsubject"       value="'.$order_name.'" />
			<input type="hidden" name="WIDtotal_fee"     value="'.$v_amount.'" />
			<input type="hidden" name="WIDbody"          value="'.$o_info.'" />
			<input type="hidden" name="WIDshow_url"      value="'.$show_url.'" />
			<input type="hidden" name="WID_url"      value="'.$o_url.'" />
			<input type="hidden" name="WEBSITE"      value="'.$config['domain'].'" />
			<button id="purchase-button" class="btntheme2 btnsize" type="submit">确定</button>
		</form>
		';
        }else{
            $a_oid = '0_'.$order_data['id'];
            $pid = $meta['alipay_v_key'];
            $app_key = $meta['alipay_v_mid'];
            $u_email = $meta['alipay_v_num'];
            $order_name = $order_data['item_name'];
            $order_id = $order_data['item_id'];
            $o_url = 'http://'.$config['domain'].'/payments/alipay';//地址
            $show_url = 'http://'.$config['domain'].'/items/'.$order_id.'/'.$order_name;
            $o_info = strtoupper(md5($v_amount.$o_url.$a_oid.$u_email.$pid.$app_key));//md5加密拼凑串,注意顺序不能变
            //构造表单
            $form[0] = '
		<form method="post" name="E_FORM" action="'.$o_url.'">
			<input type="hidden" name="WIDseller_email"  value="'.$u_email.'" />
			<input type="hidden" name="WIDout_trade_no"  value="'.$a_oid.'" />
			<input type="hidden" name="WIDsubject"       value="'.$order_name.'" />
			<input type="hidden" name="WIDtotal_fee"     value="'.$v_amount.'" />
			<input type="hidden" name="WIDbody"          value="'.$o_info.'" />
			<input type="hidden" name="WIDshow_url"      value="'.$show_url.'" />
			<input type="hidden" name="WID_url"      value="'.$o_url.'" />
			<button id="purchase-button" class="btntheme2 btnsize" type="submit">确定</button>
		</form>
		';
        }
		return $form;
	}

	public function generateDepositForm($order_data = array()) {
		global $langArray, $currency, $config, $languageURL,$meta;
		/*
            支付宝支付配置
		*/
		$v_amount = $order_data['deposit'];//支付金额
        //判断是否使用代理支付
        if(isset($meta["use_demilapay"])){
            $a_oid = '1_'.$order_data['id'].'_'.$meta["demilapay_key"];//订单号

            $o_url = 'http://pay.demila.org/index.php/Home/Alipay';//demilapay

            $o_info = strtoupper(md5($v_amount.$o_url.$a_oid));//md5加密拼凑串,注意顺序不能变
            $order_name = '充值';
            $order_id = $order_data['item_id'];
            $show_url = '';
            //构造表单
            $form[0] = '
            <form method="post" name="E_FORM" action="'.$o_url.'">
                <input type="hidden" name="WIDout_trade_no"  value="'.$a_oid.'" />
                <input type="hidden" name="WIDsubject"       value="'.$order_name.'" />
                <input type="hidden" name="WIDtotal_fee"     value="'.$v_amount.'" />
                <input type="hidden" name="WIDbody"          value="'.$o_info.'" />
                <input type="hidden" name="WIDshow_url"      value="'.$show_url.'" />
                <input type="hidden" name="WID_url"      value="'.$o_url.'" />
                <input type="hidden" name="WEBSITE"      value="'.$config['domain'].'" />
                <button id="purchase-button" class="btntheme2 btnsize" type="submit">确定</button>
            </form>
            ';
        }else{
            $a_oid = '1_'.$order_data['id'];
            $pid = $meta['alipay_v_key'];
            $app_key = $meta['alipay_v_mid'];
            $u_email = $meta['alipay_v_num'];
            $o_url = 'http://'.$config['domain'].'/payments/alipay';//地址
            $o_info = strtoupper(md5($v_amount.$o_url.$a_oid.$u_email.$pid.$app_key));//md5加密拼凑串,注意顺序不能变
            $order_name = '充值';
            $order_id = $order_data['item_id'];
            $show_url = '';
            //构造表单
            $form[0] = '
            <form method="post" name="E_FORM" action="'.$o_url.'">
                <input type="hidden" name="WIDseller_email"  value="'.$u_email.'" />
                <input type="hidden" name="WIDout_trade_no"  value="'.$a_oid.'" />
                <input type="hidden" name="WIDsubject"       value="'.$order_name.'" />
                <input type="hidden" name="WIDtotal_fee"     value="'.$v_amount.'" />
                <input type="hidden" name="WIDbody"          value="'.$o_info.'" />
                <input type="hidden" name="WIDshow_url"      value="'.$show_url.'" />
                <input type="hidden" name="WID_url"      value="'.$o_url.'" />
                <button id="purchase-button" class="btntheme2 btnsize" type="submit">确定</button>
            </form>
            ';
        }

		return $form;
	}

}
