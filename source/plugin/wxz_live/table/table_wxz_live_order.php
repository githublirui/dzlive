<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_wxz_live_order extends table_wxz_live_base {

    /**
     * 订单类型
     * @var type 
     */
    public static $orderTypes = array(
        1 => '购买会员',
        2 => '付费观看',
        3 => '打赏观众',
        4 => '群红包',
        5 => '赠送礼物',
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
        return $this->getRow($condition);
    }

    /**
     * 订单成功处理
     * @param type $orderInfo
     * @param type $attach
     */
    public function doSuccess($orderInfo, $attach = '') {
        if ($attach && isset($attach['type'])) {
            $method = "doSuccess" . ucfirst($attach['type']);
            if (method_exists($this, $method)) {
                return $this->$method($orderInfo);
            }
        }
    }

    /**
     * 通过订单id获取打赏记录
     * @param type $orderId
     */
    public function getRewardByOrderId($orderId, $field = '*') {
        if (!$orderId) {
            return;
        }

        $table = new table_wxz_live_base(array('table' => 'wxz_live_reward', 'pk' => 'id'));
        $condition = "order_id={$orderId}";

        return $table->getRow($condition, $field);
    }

    /**
     * 通过订单id获取群红包记录
     * @param type $orderId
     */
    public function getGrouppacketByOrderId($orderId, $field = '*') {
        if (!$orderId) {
            return;
        }

        $table = new table_wxz_live_base(array('table' => 'wxz_live_grouppacket', 'pk' => 'id'));
        $condition = "order_id={$orderId}";
        return $table->getRow($condition, $field);
    }

    /**
     * 打赏支付成功处理
     * @param type $orderInfo
     */
    public function doSuccessReward($orderInfo) {
        $tableViewerObj = new table_wxz_live_base(array('table' => 'wxz_live_viewer', 'pk' => 'id'));
        $tableComment = new table_wxz_live_base(array('table' => 'wxz_live_comment', 'pk' => 'id'));
        $tableMoneyLog = new table_wxz_live_base(array('table' => 'wxz_live_money_log', 'pk' => 'id'));
        $tablePolling = new table_wxz_live_base(array('table' => 'wxz_live_polling', 'pk' => 'id'));
        $tableReward = new table_wxz_live_base(array('table' => 'wxz_live_reward', 'pk' => 'id'));

        $reward = $this->getRewardByOrderId($orderInfo['id']);
        $user = C::t('#wxz_live#wxz_live_user')->getById($orderInfo['uid']);

        $tableReward->updateById($reward['id'], array('status' => '1'));

        $condition = "uid={$user['id']} AND dsid={$reward['id']} AND rid={$reward['rid']}";
        $comment = $tableComment->getRow($condition);

        if (empty($comment)) {
            $data = array(
                'uid' => $user['id'],
                'ip' => getip(),
                'is_auth' => 1,
                'nickname' => $user['nickname'],
                'headimgurl' => $user['headimgurl'],
                'rid' => $reward['rid'],
                'content' => '打赏成功',
                'dsid' => $reward['id'],
                'dsamount' => $reward['fee'],
                'dsstatus' => 1,
                'create_at' => date('Y-m-d H:i:s'),
            );

            $pollingData['type'] = 6;

            if ($reward['touid'] > 0) {
                $touser = C::t('#wxz_live#wxz_live_user')->getById($orderInfo['touid']);
                $condition = "uid={$orderInfo['touid']} AND rid={$reward['rid']}";
                $viewer = $tableViewerObj->getRow($condition);
                if ($touser) {
                    $data['touid'] = $touser['id'];
                    $data['tonickname'] = $touser['nickname'];
                    $data['toheadimgurl'] = $touser['headimgurl'];
                    if ($viewer['role'] == 1) {
                        $pollingData['type'] = 7;
                    }
                } else {
                    $data['tonickname'] = $reward['tonickname'];
                    $data['toheadimgurl'] = $reward['toheadurl'];
                    $pollingData['type'] = 7;
                }
            }

            $id = $tableComment->insert($data, true);

            if ($reward['touid'] > 0) {
                $data = array(
                    'uid' => $data['touid'],
                    'type' => 1,
                    'amount' => $data['dsamount'],
                    'rid' => $data['rid'],
                    'fromid' => $id,
                    'fromuid' => $data['uid'],
                    'fromnickname' => $data['nickname'],
                    'fromheadimgurl' => $data['headimgurl'],
                    'create_at' => date('Y-m-d H:i:s'),
                );
                $tableMoneyLog->insert($data);

                $updateData = array(
                    'amount' => $viewer['amount'] + $reward['fee'],
                );
                $tableViewerObj->updateById($viewer['id'], $updateData);
            }

            $pollingData['rid'] = $reward['rid'];
            $pollingData['comment_id'] = $id;
            $tablePolling->insert($pollingData);
        }
    }

    /**
     * 群红包支付成功处理
     * @param type $orderInfo
     */
    public function doSuccessGrouppacket($orderInfo) {
        $grouppacket = $this->getGrouppacketByOrderId($orderInfo['id']);

        $user = C::t('#wxz_live#wxz_live_user')->getById($orderInfo['uid']);
        $tableGrouppacket = new table_wxz_live_base(array('table' => 'wxz_live_grouppacket', 'pk' => 'id'));
        $tableComment = new table_wxz_live_base(array('table' => 'wxz_live_comment', 'pk' => 'id'));
        $tablePolling = new table_wxz_live_base(array('table' => 'wxz_live_polling', 'pk' => 'id'));

        $tableGrouppacket->updateById($grouppacket['id'], array('status' => '1'));

        $condition = "uid={$user['id']} AND gid={$grouppacket['id']} AND rid={$grouppacket['rid']}";
        $comment = $tableComment->getRow($condition);

        if (empty($comment)) {
            $data = array(
                'uid' => $grouppacket['uid'],
                'content' => $grouppacket['remark'],
                'is_auth' => 1,
                'type' => $grouppacket['type'],
                'headimgurl' => $user['headimgurl'],
                'nickname' => $user['nickname'],
                'num' => $grouppacket['num'],
                'amount' => $grouppacket['amount'],
                'ispacket' => 1,
                'rid' => $grouppacket['rid'],
                'gid' => $grouppacket['id'],
                'samount' => $grouppacket['json'],
                'create_at' => date('Y-m-d H:i:s'),
            );

            $id = $tableComment->insert($data, true);

            $pollingData = array(
                'rid' => $grouppacket['rid'],
                'type' => 4,
                'comment_id' => $id,
            );
            $tablePolling->insert($pollingData);
        }
    }

    /**
     * 赠送礼物支付成功处理
     * @param type $orderInfo
     */
    public function doSuccessGift($orderInfo) {
        $tableGiftLog = new table_wxz_live_base(array('table' => 'wxz_live_giftlog', 'pk' => 'id'));
        $tableGift = new table_wxz_live_base(array('table' => 'wxz_live_gift', 'pk' => 'id'));
        $tableComment = new table_wxz_live_base(array('table' => 'wxz_live_comment', 'pk' => 'id'));
        $tablePolling = new table_wxz_live_base(array('table' => 'wxz_live_polling', 'pk' => 'id'));

        $condition = "order_id={$orderInfo['id']}";
        $giftLog = $tableGiftLog->getRow($condition);
        $user = C::t('#wxz_live#wxz_live_user')->getById($orderInfo['uid']);
        $gift = $tableGift->getById($giftLog['giftid']);

        $tableGiftLog->updateById($giftLog['id'], array('status' => '1'));

        $condition = "giftid={$giftLog['id']} AND uid={$user['id']} AND rid={$giftLog['rid']}";
        $comment = $tableComment->getRow($condition);
        if (empty($comment)) {
            $data = array(
                'uid' => $user['id'],
                'ip' => getip(),
                'is_auth' => 1,
                'nickname' => $user['nickname'],
                'headimgurl' => $user['headimgurl'],
                'rid' => $giftLog['rid'],
                'content' => '赠送礼物成功',
                'giftid' => $giftLog['id'],
                'giftnum' => $giftLog['num'],
                'giftpic' => $gift['pic'],
                'giftstatus' => 1,
                'create_at' => date('Y-m-d H:i:s'),
            );

            $id = $tableComment->insert($data, true);

            $pollingData = array(
                'rid' => $giftLog['rid'],
                'type' => 5,
                'comment_id' => $id
            );

            $tablePolling->insert($pollingData);
        }
    }

}

?>