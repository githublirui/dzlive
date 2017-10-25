<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT . "./source/plugin/wxz_live/table/table_wxz_live_base.php";

class table_wxz_live_order extends table_wxz_live_base {

    /**
     * 订单类型
     * @var type 
     */
    public static $orderTypes = array(
        1 => '购买会员',
    );

    public function __construct() {
        $this->_table = 'wxz_live_order';
        $this->_pk = 'id';

        parent::__construct();
    }

    /**
     * 生成订单编号
     * @return type
     */
    public function getOrderNo() {
        $randNo = sprintf('%04d', rand(1, 1000));
        return date("YmdHis") . $randNo;
    }

    /**
     * 通过订单号支付
     * @param type $orderNo
     */
    public function getByOrderNo($orderNo) {
        if (!$orderNo) {
            return false;
        }
        $condition = "order_no='{$orderNo}'";
        return $this->getRow($orderNo);
    }

    /**
     * 订单成功处理
     * @param type $orderInfo
     */
    public function doSuccess($orderInfo) {
        
    }

}

?>