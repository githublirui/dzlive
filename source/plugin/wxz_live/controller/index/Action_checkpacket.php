<?php

/**
 * 点击红包
 */
class Action_checkpacket extends Controller_base {

    public function index() {
        $tableComment = new table_wxz_live_base(array('table' => 'wxz_live_comment', 'pk' => 'id'));
        $tableViewer = new table_wxz_live_base(array('table' => 'wxz_live_viewer', 'pk' => 'id'));
        $tableGrouppacket = new table_wxz_live_base(array('table' => 'wxz_live_grouppacket', 'pk' => 'id'));
        $tableGrouppacketLog = new table_wxz_live_base(array('table' => 'wxz_live_grouppacket_log', 'pk' => 'id'));

        $rid = $_GET['rid'];
        $hb_id = $_GET['sendid'];

        $condition = "id={$hb_id} AND rid={$rid}";
        $hb_msg = $tableComment->getRow($condition);

        $user = C::t('#wxz_live#wxz_live_user')->authUser('', false);
        $uid = $user['id'];

        $condition = "rid={$rid} AND uid={$uid}";
        $viewer = $tableViewer->getRow($condition);

        $grouppacket = $tableGrouppacket->getById($hb_msg['gid']);

        $condition = "rid={$rid} AND uid={$uid} AND comment_id={$hb_msg['id']} AND hid={$grouppacket['id']}";
        $log = $tableGrouppacketLog->getRow($condition);

        $redinfo['title'] = $grouppacket['remark'];
        $redinfo['type'] = $grouppacket['type'];
        $redinfo['money'] = $grouppacket['amount'];
        $redinfo['num'] = $grouppacket['num'];
        $moneys = iunserializer($hb_msg['samount']);
        $res['redinfo'] = $redinfo;

        if ($log) {
            $res['status'] = 3;
            $condition = "rid={$rid} AND comment_id={$hb_msg['id']} AND hid={$grouppacket['id']}";
            $res['redlist'] = $tableGrouppacketLog->getAll($condition);
            echo json_encode($res);
            exit;
        }

        if ($hb_msg['num'] == $hb_msg['send_num'] || empty($moneys)) {
            $res['status'] = 0;
            $condition = "rid={$rid} AND comment_id={$hb_msg['id']} AND hid={$grouppacket['id']}";
            $res['redlist'] = $tableGrouppacketLog->getAll($condition);
            echo json_encode($res);
            exit;
        }

        if ($hb_msg['yifa_amount'] >= $hb_msg['amount'] || $hb_msg['send_num'] >= $hb_msg['num']) {
            $res['status'] = 0;
            echo json_encode($res);
            exit;
        }

        $res['status'] = 1;
        echo json_encode($res);
        exit();
    }

    /**
     * 获取红包
     */
    public function getpacket() {
        $tableComment = new table_wxz_live_base(array('table' => 'wxz_live_comment', 'pk' => 'id'));
        $tableMoneyLog = new table_wxz_live_base(array('table' => 'wxz_live_money_log', 'pk' => 'id'));
        $tableViewer = new table_wxz_live_base(array('table' => 'wxz_live_viewer', 'pk' => 'id'));
        $tableGrouppacket = new table_wxz_live_base(array('table' => 'wxz_live_grouppacket', 'pk' => 'id'));
        $tableGrouppacketLog = new table_wxz_live_base(array('table' => 'wxz_live_grouppacket_log', 'pk' => 'id'));

        $user = C::t('#wxz_live#wxz_live_user')->authUser('', false);
        $uid = $user['id'];
        $rid = $_GET['rid'];
        $hb_id = $_GET['sendid'];

        $condition = "id={$hb_id} AND rid={$rid}";

        $hb_msg = $tableComment->getRow($condition);

        $grouppacket = $tableGrouppacket->getById($hb_msg['gid']);

        $viewer = $tableViewer->getRow($condition);

        $condition = "rid={$rid} AND uid={$uid} AND comment_id={$hb_msg['id']} AND hid={$grouppacket['id']}";
        $log = $tableGrouppacketLog->getRow($condition);

        $moneys = iunserializer($hb_msg['samount']);

        $redinfo['title'] = $grouppacket['remark'];
        $redinfo['type'] = $grouppacket['type'];
        $redinfo['money'] = $grouppacket['amount'];
        $redinfo['num'] = $grouppacket['num'];
        $res['redinfo'] = $redinfo;

        if ($log) {
            $res['status'] = 2;
            $condition = "rid={$rid} AND comment_id={$hb_msg['id']} AND hid={$grouppacket['id']}";
            $res['redlist'] = $tableGrouppacketLog->getAll($condition);
            echo json_encode($res);
            exit();
        }

        if ($hb_msg['num'] == $hb_msg['send_num'] || empty($moneys)) {
            $res['status'] = 0;
            echo json_encode($res);
            exit();
        }

        $fee = array_pop($moneys);
        if ($hb_msg['syifa'] == '') {
            $syifa = array('0' => $fee);
        } else {
            $syifa = iunserializer($hb_msg['syifa']);
            $syifa[] = $fee;
        }

        if ($hb_msg['yifa_amount'] >= $hb_msg['amount'] || $hb_msg['send_num'] >= $hb_msg['num']) {
            $res['status'] = 0;
            echo json_encode($res);
            exit();
        }

        if (intval($fee) > intval($hb_msg['amount'])) {
            $res['status'] = -1;
            $res['msg'] = '你的提现有点多';
            echo json_encode($res);
            exit();
        }

        $rec = array();
        $rec['hid'] = $grouppacket['id'];
        $rec['rid'] = $rid;
        $rec['uid'] = $uid;
        $rec['comment_id'] = $hb_msg['id'];
        $rec['amount'] = $fee;
        $rec['create_at'] = date('Y-m-d H:i:s');
        $rec['headimgurl'] = $user['headimgurl'];
        $rec['nickname'] = $user['nickname'];
        $rec['type'] = $grouppacket['type'];

        $id = $tableGrouppacketLog->insert($rec, true);


        $data = array(
            'uid' => $uid,
            'type' => 2,
            'amount' => $fee,
            'rid' => $rid,
            'fromid' => $id,
            'fromuid' => $hb_msg['uid'],
            'fromnickname' => $hb_msg['nickname'],
            'fromheadimgurl' => $hb_msg['headimgurl'],
            'create_at' => date('Y-m-d H:i:s'),
        );
        $tableMoneyLog->insert($data);

        $user_amount['amount'] = ($fee) + $viewer['amount'];

        $condition = "uid={$uid} AND rid={$rid}";
        $tableViewer->updateData($condition, $user_amount);

        $updataData = array('send_num' => $hb_msg['send_num'] + 1, 'yifa_amount' => $hb_msg['yifa_amount'] + $fee, 'samount' => iserializer($moneys), 'syifa' => iserializer($syifa));
        $tableComment->updateById($hb_msg['id'], $updataData);

        $condition = "rid={$rid} AND hid={$grouppacket['id']} AND comment_id={$hb_msg['id']}";
        $res['redlist'] = $tableGrouppacketLog->getAll($condition);
        $res['status'] = 2;
        echo json_encode($res);
    }

}

?>
