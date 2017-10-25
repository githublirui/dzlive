<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require dirname(__FILE__) . '/../../class/class_core.php';
$wxpayPath = DISCUZ_ROOT . "source/plugin/wxz_live/lib/wxpay/";
include_once DISCUZ_ROOT . "source/plugin/wxz_live/function/global.func.php";

$discuz = C::app();
$discuz->init_cron = false;
$discuz->init();

require_once "{$wxpayPath}lib/WxPay.Api.php";
require_once "{$wxpayPath}lib/WxPay.Notify.php";
require_once "{$wxpayPath}example/log.php";

//初始化日志
$logHandler = new CLogFileHandler("{$wxpayPath}/logs/" . date('Y-m-d') . '.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify {

    //查询订单
    public function Queryorder($transaction_id) {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);
        Log::DEBUG("query:" . json_encode($result));
        if (array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg) {
        Log::DEBUG("call back:" . json_encode($data));
        $notfiyOutput = array();

        if (!array_key_exists("transaction_id", $data)) {
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if (!$this->Queryorder($data["transaction_id"])) {
            $msg = "订单查询失败";
            return false;
        }

        $tableObj = C::t('#wxz_live#wxz_live_order');
        $orderInfo = $tableObj->getByOrderNo($data['out_trade_no']);

        if (!$orderInfo) {
            $msg = "订单不存在";
            return false;
        }

        if ($orderInfo['status'] != 1) {
            $msg = "订单已处理";
            return false;
        }

        if ($data['result_code'] != 'SUCCESS' || $data['return_code'] != 'SUCCESS') {
            $update = array(
                'status' => 3,
                'fail_reason' => $data['fail_reason'],
            );
            $tableObj->updateById($orderInfo['id'], $update);
            $msg = "失败订单";
            return false;
        }
        $update = array(
            'status' => 2,
            'trade_no' => $data['out_trade_no'],
            'success_at' => date('Y-m-d H:i:s'),
        );
        $tableObj->updateById($orderInfo['id'], $update);
        $tableObj->doSuccess($orderInfo);
        return true;
    }

}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
