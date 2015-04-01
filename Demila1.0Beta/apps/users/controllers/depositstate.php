<?
//修改充值状态
if(isset($_GET["type"]) && $_GET["type"]==1){
    require_once ROOT_PATH . '/apps/users/models/deposit.class.php';
    $cms = new deposit();
    $order_id=$_GET["order_id"];
    $order_info = $cms->get($order_id);
    if ($order_info['paid'] == 'false') {
        $cms->depositIsPay($order_id, '支付宝');
    }
        refresh('http://' . $config['domain'] . '/' . $languageURL . 'users/deposit/success/' . $order_id . '/');
        echo 'success';

}
if(isset($_GET["type"]) && $_GET["type"]==0){
    //支付宝直接购买订单
    require_once ROOT_PATH . '/apps/items/models/orders.class.php';
    $cms = new orders();
    $order_id=$_GET["order_id"];
    $order_info = $cms->get($order_id);
    if ($order_info['paid'] == 'false') {
        $cms->orderIsPay($order_id, '支付宝支付');
    }
        refresh('http://' . $config['domain'] . '/' . $languageURL . 'users/downloads/');//下载页面
        echo "success";        //请不要修改或删除

}