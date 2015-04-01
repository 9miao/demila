<?php
header("Content-type: text/html; charset=utf-8");
/* *
 * 功能：即时到账交易接口接入页
 * 版本：3.3
 * 修改日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************注意*************************
 * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 */
if(isset($meta["use_demilapay"])){
    $pid = $meta['alipay_v_key1'];
    $app_key = $meta['alipay_v_mid1'];
}else{
    $pid = $meta['alipay_v_key'];
    $app_key = $meta['alipay_v_mid'];
}
$o_url = $_POST['WID_url'];//地址
$v_amount = $_POST['WIDtotal_fee'];
$u_email = $_POST['WIDseller_email'];
$oid = $_POST['WIDout_trade_no'];
$o_info = strtoupper(md5($v_amount.$o_url.$oid.$u_email.$pid.$app_key));//md5加密拼凑串,注意顺序不能变
if($_POST['WIDbody']!= $o_info){
    echo '<script>alert("订单有误或操作非法");location.href="http://'. $config['domain'].'";</script>';
    die;
}
require_once ROOT_PATH.'/classes/Alipay/alipay.config.php';
require_once ROOT_PATH.'/classes/Alipay/lib/alipay_submit.class.php';
/**************************请求参数**************************/

        //支付类型
        $payment_type = "1";
        //必填，不能修改
//判断是否使用代理支付
        if(isset($meta["use_demilapay"])){
            //服务器异步通知页面路径
            $notify_url  = 'http://pay.demila.org/index.php/Home/Alinotify';//地址";
            //需http://格式的完整路径，不能加?id=123这类自定义参数

            //页面跳转同步通知页面路径
            $return_url = 'http://pay.demila.org/index.php/Home/Alireturn';
            //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        }else{
            //服务器异步通知页面路径
            $notify_url  = 'http://'.$config['domain'].'/payments/ali_notify';//地址";
            //需http://格式的完整路径，不能加?id=123这类自定义参数

            //页面跳转同步通知页面路径
            $return_url = 'http://'.$config['domain'].'/payments/ali_return';;
        }


        //卖家支付宝帐户
        $seller_email = $_POST['WIDseller_email'];
        //必填

        //商户订单号
        $out_trade_no = $_POST['WIDout_trade_no'];
        //商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject = $_POST['WIDsubject'];
        //必填

        //付款金额
        $total_fee = $_POST['WIDtotal_fee'];
        //必填

        //订单描述

        $body = $_POST['WIDbody'];
        //商品展示地址
        $show_url = $_POST['WIDshow_url'];
        //需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html

        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数

        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1


/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array(
		"service" => "create_direct_pay_by_user",
		"partner" => trim($alipay_config['partner']),
		"payment_type"	=> $payment_type,
		"notify_url"	=> $notify_url,
		"return_url"	=> $return_url,
		"seller_email"	=> $seller_email,
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"total_fee"	=> $total_fee,
		"body"	=> $body,
		"show_url"	=> $show_url,
		"anti_phishing_key"	=> $anti_phishing_key,
		"exter_invoke_ip"	=> $exter_invoke_ip,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
echo $html_text;

?>