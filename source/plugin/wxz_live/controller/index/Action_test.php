<?php

class Action_test extends Controller_base {

    /**
     * @desc
     * @param
     * @return
     */
    public function index() {
        echo 'test';
    }

    /**
     * @desc
     * @param
     * @return
     */
    public function test1() {
        var_dump($this->createWebUrl('test', array('pmod' => 'live', 'do' => 'test', 'p' => 1, 't' => 2), true));
    }

    /**
     * @desc
     * @param
     * @return
     */
    public function test() {
        global $_G;
        include template('wxz_live:index/live');
    }

    /**
     * 支付测试
     */
    public function payment() {
        global $_G;
        $tableOrder = C::t('#wxz_live#wxz_live_order');

        $user = C::t('#wxz_live#wxz_live_user')->authUser(array(), false);

        $orderTypes = table_wxz_live_order::$orderTypes;
        $orderTypeValues = array_keys($orderTypes);

        $uid = $user['id'];
        $orderType = $_GET['order_type']; //订单类型
        $money = $_GET['money']; //按分计算
        $payMoney = $_GET['pay_money']; //需要支付的金额

        if (!$payMoney) {
            $payMoney = $money;
        }

        if (!is_numeric($money)) {
            //            showmessage('订单金额参数错误: ' . $money);//debug
        }

        if (!in_array($orderType, $orderTypeValues)) {
//            showmessage('订单类型参数错误: ' . $orderType);//debug
        }

        //生成订单
        $orderNo = $tableOrder->getOrderNo();
        $orderData = array(
            'order_no' => $orderNo,
            'uid' => $uid,
            'order_type' => $orderType,
            'money' => $money,
            'pay_money' => $payMoney,
            'create_at' => date('Y-m-d H:i:d'),
        );
        $orderId = $tableOrder->insert($orderData, true);

        //微信jsapipay
        $wxpayPath = DISCUZ_ROOT . "./source/plugin/wxz_live/lib/wxpay/";
        require_once "{$wxpayPath}lib/WxPay.Api.php";
        require_once "{$wxpayPath}example/WxPay.JsApiPay.php";
        require_once "{$wxpayPath}example/log.php";

        //初始化日志
        $logHandler = new CLogFileHandler("{$wxpayPath}/logs/" . date('Y-m-d') . '.log');
        $log = Log::Init($logHandler, 15);

        //①、获取用户openid
        $tools = new JsApiPay();
//        $openId = $tools->GetOpenid();
        $openId = $user['openid'];
        $notifyUrl = "{$_G['siteurl']}/source/plugin/wxz_live/notify.php";
        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("test1");
        $input->SetAttach("test2");
        $input->SetOut_trade_no($orderNo);
        $input->SetTotal_fee($payMoney);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($orderTypes[$orderType]);
        $input->SetNotify_url($notifyUrl);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);

        $jsApiParameters = $tools->GetJsApiParameters($order);

        //获取共享收货地址js函数参数
//        $editAddress = $tools->GetEditAddressParameters();
        include template("wxz_live:pay/jsapi");
    }

}

?>
