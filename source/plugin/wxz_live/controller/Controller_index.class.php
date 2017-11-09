<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/**
 * 
 * 前台页面
 */
class Controller_index extends Controller_base {

    /**
     * 直播首页 
     */
    public function index() {
        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/lib/wxz_weixin.class.php";

        $wxzWeixin = new wxz_weixin();

        $jssdkConfig = $wxzWeixin->getJssdkConfig();

        //获取首页banner
        $tableObj = new table_wxz_live_base(array('table' => 'wxz_live_banner', 'pk' => 'id'));
        $condition = "is_show=1";
        $banners = $tableObj->getAll($condition, '*', 'sort_order desc');

        //首页配置
        $types = array(1, 2);
        $indexSettings = C::t('#wxz_live#wxz_live_setting')->getByType($types);
        $style = $indexSettings[1]['link'];

        //获取所有分类
        $categorys = C::t('#wxz_live#wxz_live_category')->getShowCategorys();

        include template("wxz_live:index/{$style}/index");
    }

    /**
     * ajax 获取列表
     */
    public function ajaxGetlive() {
        global $_G;
        $page = intval($_GET['page']);
        $cid = (int) intval($_GET['cid']);

        $isweixin = 1;
        $pindex = max(0, intval($_GET['page']));
        $psize = 5;
        $start = ($pindex) * $psize;
        $condition = "is_show=1";

        if ($cid) {
            $condition .= " AND category_id={$cid}";
        }

        $list = C::t('#wxz_live#wxz_live_room')->getAll($condition, '*', 'sort_order desc', "{$start},{$psize}");
        $tmp = array();

        foreach ($list as $key => $value) {
            $tmp[$key . '"'] = $value;
            if ($value['start_time'] != '0000-00-00 00:00:00') {
                $tmp[$key . '"']['start_time'] = strtotime($value['start_time']);
            } else {
                $tmp[$key . '"']['start_time'] = strtotime($value['create_at']);
            }

            $tmp[$key . '"']['end_time'] = strtotime($value['end_time']);
            $tmp[$key . '"']['linkurl'] = "{$_G['siteurl']}plugin.php?id=wxz_live:index&pmod=index&act=live&roomno={$value['room_no']}";
        }
        $list = $tmp;
        $result = array('s' => '1', 'msg' => $list, 'isweixin' => $isweixin);
        echo json_encode($result);
        exit;
    }

    /**
     * @desc 微信授权
     */
    public function wxoauth() {
        global $_G;
        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/lib/wxz_weixin.class.php";
        $wxzWeixin = new wxz_weixin();

        $code = $_GET['code'];

        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$wxzWeixin->appid}&secret={$wxzWeixin->appsecret}&code={$code}&grant_type=authorization_code";
        $resp = ihttp_get($url);
        if (is_error($resp)) {
            showmessage('系统错误, 详情: ' . $resp['message']);
        }
        $auth = @json_decode($resp['content'], true);
        if (is_array($auth) && !empty($auth['openid'])) {
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$auth['access_token']}&openid={$auth['openid']}&lang=zh_CN";
            $resp = ihttp_get($url);
            if (is_error($resp)) {
                showmessage('系统错误2');
            }
            $info = @json_decode($resp['content'], true);
            if (is_array($info) && !empty($info['openid'])) {
                $user = array();
                $user['openid'] = $info['openid'];
                $user['nickname'] = $info['nickname'];
                $user['sex'] = $info['sex'];
                $user['city'] = $info['city'];
                $user['province'] = $info['province'];
                $user['headimgurl'] = $info['headimgurl'];
                $user['ip'] = getip();

                if (!empty($user['headimgurl'])) {
                    $user['headimgurl'] = rtrim($user['headimgurl'], '0');
                    $user['headimgurl'] .= '132';
                }

                $ret = C::t('#wxz_live#wxz_live_user')->updateUser($user);

                $key = 'wxz_openid_' . $_G['wechat']['setting']['wechat_appId'];
                dsetcookie($key, $user['openid'], 2592000);

                $forward = getcookie('wxz_forward');
                header('Location: ' . $forward);
                exit();
            }
        }
        showmessage('系统错误, 详情: 未获取到access token');
    }

    /**
     *  直播分类页
     */
    public function category() {
        global $_G;
        include template('wxz_live:index/category');
    }

    /**
     * 直播详情页面
     */
    public function live() {
        global $_G;
        $roomNo = (string) $_GET['roomno'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        //$user_agent = "MicroMessenger"; //debug
        //获取直播间详情
        $liveInfo = C::t('#wxz_live#wxz_live_room')->getByRoomNo($roomNo);
        $liveSettingInfo = C::t('#wxz_live#wxz_live_room')->getRoomSetting($liveInfo['id']);

        $liveInfo = C::t('#wxz_live#wxz_live_room')->formatRoomData($liveInfo);

        $user = C::t('#wxz_live#wxz_live_user')->authUser($liveSettingInfo, false);

        $rid = (int) $liveInfo['id'];
        $uid = $user['id'];

        if (!$liveInfo) {
            showmessage('直播间不存在');
        }

        $shutup = 0; //黑名单
        //获取所有
        $tableViewerObj = new table_wxz_live_base(array('table' => 'wxz_live_viewer', 'pk' => 'id'));
        $tablePollingObj = new table_wxz_live_base(array('table' => 'wxz_live_polling', 'pk' => 'id'));
        $tableZanPicObj = new table_wxz_live_base(array('table' => 'wxz_live_zanpic', 'pk' => 'id'));
        $tableZanNumObj = new table_wxz_live_base(array('table' => 'wxz_live_zannum', 'pk' => 'id'));
        $tableGiftObj = new table_wxz_live_base(array('table' => 'wxz_live_gift', 'pk' => 'id'));
        $tableSetting = C::t('#wxz_live#wxz_live_setting');

        //直播间管理员
        $condition = "rid={$rid} AND role=2";
        $roomadmins = $tableViewerObj->getAll($condition, 'uid');
        $roomadmin = json_encode($roomadmins);

        $viewer = $this->intoroom($rid, $user); //浏览
        //
        //菜单
        $menusss = C::t('#wxz_live#wxz_live_room')->getMenus($rid);
        $menusss = array_values($menusss);

        //获取播放器详情
        $playerInfo = C::t('#wxz_live#wxz_live_room')->getPlayerInfoByRoomId($rid);
        $playerInfo = $this->_formatPlayer($playerInfo);

        //格式化播放器
        if (!$playerInfo['player_height'] || !$playerInfo['player_weight']) {
            $playerInfo['player_height'] = '720';
            $playerInfo['player_weight'] = '1280';
        }

        $style = $liveInfo['style'] ? $liveInfo['style'] : 1;

        //直播间付费
        $condition = "uid={$uid} AND order_type=2 AND rid={$rid}";
        $paylog = C::t('#wxz_live#wxz_live_order')->getRow($condition);

        //会员观看限制
        if ($liveInfo['limit'] == 5) {
            if ($user['is_vip'] != 2) {
                $vipLimit = 1;
            }
            if ($user['is_vip'] == 2 && strtotime($user['vip_end']) < time()) {
                $vipLimit = 1; //非会员禁止观看
                $vipValidLimit = 1; //过期
            }
        }

        $limit_time = 0;
        if ($liveInfo['limit'] == 3 && $paylog) {
            $limit_time = strtotime($paylog['create_at']) + $liveInfo['limit_data']['delayed'];
        }

        //点攒总数
        $condition = "rid={$rid}";
        $totalzannum = $tableZanNumObj->getRow($condition, 'sum(num) total_num');
        $totalzannum = $totalzannum && $totalzannum['total_num'] ? $totalzannum['total_num'] : 0;

        //点赞图片
        $condition = "rid={$rid} AND is_show=1";
        $zanlist = $tableZanPicObj->getAll($condition);
        $pic = array();
        foreach ($zanlist as $key => $v) {
            $pic[] = $v['pic'];
        }
        $pics = json_encode($pic);

        //评论列表
        $Comments = C::t('#wxz_live#wxz_live_room')->getComments($rid);
        $pid = $tablePollingObj->getAll("rid={$rid}", 'id', 'id desc', 1);
        $pid = $pid ? end($pid) : array();

        //分享参数
        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/lib/wxz_weixin.class.php";
        $wxzWeixin = new wxz_weixin();
        $jssdkConfig = $wxzWeixin->getJssdkConfig();

        //打赏
        $moneyNum = 6;
        $reward = $tableSetting->getByType(4);
        if ($reward) {
            $reward = array_merge($reward, unserialize($reward['desc']));
        }

        //礼物列表
        $condition = "rid={$rid} AND is_show=1";
        $gift = $tableGiftObj->getAll($condition, '*', 'sort_order desc');

        if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
            include template("wxz_live:index/{$style}/live_ios");
        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) {
//            include template("wxz_live:index/{$style}/live_android");
            include template("wxz_live:index/{$style}/live_ios");
        } else {
            include template("wxz_live:index/{$style}/live_ios");
        }
    }

    /**
     * 格式化播放器
     * @param type $playerInfo
     */
    private function _formatPlayer($playerInfo) {
        if (!$playerInfo['player_height'] || !$playerInfo['player_weight']) {
            $playerInfo['player_height'] = '720';
            $playerInfo['player_weight'] = '1280';
        }
        if ($playerInfo['type'] == 5) {
            $response = ihttp_request('http://shuidi.huajiao.com/pc/view.html?sn=' . $playerInfo['settings']['xsdroomid']);
            $url = 'https://live3.jia.360.cn/public/getInfoAndPlayV2?sn=' . $playerInfo['settings']['xsdroomid'];

            $response = ihttp_request($url);
            $roominfo = json_decode($response['content']);
            if (!$roominfo->playInfo->hls) {
                $playerInfo['settings']['hls'] = '';
                $playerInfo['settings']['rtmp'] = '';
                $playerInfo['settings']['img'] = $roominfo->publicInfo->thumbnail;
            } else {
                $playerInfo['settings']['hls'] = $roominfo->playInfo->hls;
                $playerInfo['settings']['rtmp'] = $roominfo->playInfo->rtmp;
                $playerInfo['settings']['img'] = $roominfo->publicInfo->thumbnail;
            }
        }
        if ($playerInfo['type'] == 8) {
            $loginurl = 'http://interface.yy.com/hls/get/0/' . $playerInfo['settings']['sid'] . '/' . $playerInfo['settings']['ssid'] . '?appid=0&excid=1200&type=m3u8&isHttps=0&callback=jsonp2';
            $response = ihttp_request($loginurl, array(), array(
                'CURLOPT_REFERER' => 'http://wap.yy.com/mobileweb/' . $playerInfo['settings']['sid'] . '/' . $playerInfo['settings']['ssid'] . '?tempId=' . $playerInfo['settings']['tpl'] . ''
            ));
            $result = json_decode(substr($response['content'], 7, -1), true);
            if ($result['code'] == 0) {
                $playerInfo['settings']['hls'] = $result['hls'];
            }
        }
        if ($playerInfo['type'] == 7) {
            $url = 'https://room.api.m.panda.tv/index.php?method=room.shareapi&roomid=' . $playerInfo['settings']['xmroomid'];
            $response = ihttp_request($url);
            $roominfo = json_decode($response['content']);

            if ($roominfo->data->videoinfo) {
                $url = 'https://api.m.panda.tv/stream/room/pull/get?roomid=' . $roominfo->data->roominfo->id . '&roomkey=' . $roominfo->data->videoinfo->room_key . '&definition_option=1&hardware=1';
                $roomid = $roominfo->data->roominfo->id;
                $response = ihttp_request($url);
                $key = json_decode($response['content']);
                $address = str_replace('http', 'https', $roominfo->data->videoinfo->address);
                $list['settings']['hls'] = $address . "?sign=" . $key->data->$roomid->sign . "&ts=" . $key->data->$roomid->ts;
                //$list['settings']['hls'] = 'http://hls-live-qn.xingyan.panda.tv/panda-xingyan/ceca414154bbc59f807fd2232008f732.m3u8';
                //$list['settings']['hls'] = 'https://pl-hls28.live.panda.tv/live_panda/6defc95c31023f0ce061f525626468fe.m3u8?sign=08fcf435b5e03c7fcd8494cb1da3d5e9&ts=59e71d70&rid=-52751807';
            } else {
                $playerInfo['settings']['hls'] = $roominfo->data->videoinfo->address;
            }
        }
        return $playerInfo;
    }

    /**
     * 进入房间
     * @param type $roomId
     * @param type $user
     */
    public function intoroom($roomId, $user) {
        $tablePlayerObj = new table_wxz_live_base(array('table' => 'wxz_live_viewer', 'pk' => 'id'));

        $condition = "rid={$roomId} AND uid={$user['id']}";
        $viewer = $tablePlayerObj->getRow($condition);

        if (!$viewer) {
            $data['rid'] = $roomId;
            $data['uid'] = $user['id'];
            $data['create_at'] = date('Y-m-d H:i:s');
            $data['id'] = $tablePlayerObj->insert($data, true);
            $viewer = $data;
        }
        return $viewer;
    }

    /**
     * 观看限制
     */
    public function limit() {
        $type = $_GET['type'];
        $rid = (int) $_GET['rid'];
        $password = $_GET['password'];

        $liveInfo = C::t('#wxz_live#wxz_live_room')->getById($rid);
        $liveSettingInfo = C::t('#wxz_live#wxz_live_room')->getRoomSetting($liveInfo['id']);

        $tableViewerObj = new table_wxz_live_base(array('table' => 'wxz_live_viewer', 'pk' => 'id'));

        $liveInfo['limit_data'] = unserialize($liveInfo['limit_data']);

        $user = C::t('#wxz_live#wxz_live_user')->authUser($liveSettingInfo);

        if (!$liveInfo) {
            $result['s'] = -1;
            $result['msg'] = '直播间不存在';
            echo json_encode($result);
            exit;
        }

        //验证码密码
        if ($liveInfo['limit'] == 1 || $liveInfo['limit'] == '4') {
            if ($liveInfo['limit_data']['password'] != $password) {
                $result['s'] = -1;
                $result['msg'] = '密码错误';
                echo json_encode($result);
                exit;
            }
        }

        if ($liveInfo['limit'] == '1') {
            $data['password'] = $password;
            $condition = "rid={$liveInfo['id']} AND uid={$user['id']}";
            $tableViewerObj->updateData($condition, $data);
            $result['s'] = 1;
            $result['msg'] = '密码正确';
            echo json_encode($result);
            exit;
        }

        if ($liveInfo['limit'] == '2' || $liveInfo['limit'] == '3') {

            //查询支付
            $condition = "uid={$user['id']} AND order_type=2 AND rid={$rid}";
            $log = C::t('#wxz_live#wxz_live_order')->getRow($condition);

            if (!$log) {
                $log['rid'] = $rid;
                $log['money'] = $liveInfo['limit_data']["amount"];
                $log['pay_money'] = $liveInfo['limit_data']["amount"];
                $log['uid'] = $user['id'];
                $log['order_type'] = 2;
                $log['order_no'] = C::t('#wxz_live#wxz_live_order')->getOrderNo();
                $log['create_at'] = date('Y-m-d H:i:s');

                $logid = C::t('#wxz_live#wxz_live_order')->insert($log, true);
            } else {
                $logid = $log['id'];
            }

            echo json_encode($this->limitPay($liveInfo['limit_data']["amount"], $log, $rid));
            exit;
        }
    }

    /**
     * 付费支付
     * @param type $money
     * @param type $log
     * @param type $rid
     * @return string
     */
    private function limitPay($money, $log, $rid) {
        global $_G;

        $wxpayPath = DISCUZ_ROOT . "./source/plugin/wxz_live/lib/wxpay/";
        require_once "{$wxpayPath}lib/WxPay.Config.php";

        $user = C::t('#wxz_live#wxz_live_user')->authUser('', false);
        $uid = $user['id'];

        $params = array(
            'fee' => $money,
            'user' => $user['openid'],
            'random' => $log['order_no'],
        );

        $attach = array('type' => 'paylive');
        $notifyUrl = "{$_G['siteurl']}/source/plugin/wxz_live/notify.php"; //

        $package = array();
        $package['appid'] = WxPayConfig::$appid;
        $package['mch_id'] = WxPayConfig::$mchid;
        $package['nonce_str'] = random(8);
        $package['body'] = '直播间付费';
        $package['attach'] = json_encode($attach);
        $package['out_trade_no'] = $params['random'];
        $package['total_fee'] = $params['fee'];
        $package['spbill_create_ip'] = getip();
        $package['time_start'] = date('YmdHis', time());
        $package['time_expire'] = date('YmdHis', time() + 600);
        $package['notify_url'] = $notifyUrl;
        $package['trade_type'] = 'JSAPI';
        $package['openid'] = $user['openid'];
//        $package['openid'] = 'o11S7wvMcT_2g8WPHVMZtGEL7mz4'; //debug
        ksort($package, SORT_STRING);
        $string1 = '';
        foreach ($package as $key => $v) {
            if (empty($v)) {
                continue;
            }
            $string1 .= "{$key}={$v}&";
        }
        $string1 .= "key=" . WxPayConfig::$key;
        $package['sign'] = strtoupper(md5($string1));
        $dat = array2xml($package);
        $response = ihttp_request('https://api.mch.weixin.qq.com/pay/unifiedorder', $dat);

        $xml = @isimplexml_load_string($response['content'], 'SimpleXMLElement', LIBXML_NOCDATA);

        if (is_error($response)) {
            $result = array('s' => '-1', 'msg' => strval($xml->return_msg));
            return $result;
        }

        if (strval($xml->return_code) == 'FAIL') {
            $result = array('s' => '-1', 'msg' => strval($xml->return_msg));
            return $result;
        }

        if (strval($xml->result_code) == 'FAIL') {
            $result = array('s' => '-1', 'msg' => strval($xml->return_msg));
            return $result;
        }

        $prepayid = $xml->prepay_id;
        $option['appId'] = WxPayConfig::$appid;
        $option['timeStamp'] = TIMESTAMP;
        $option['nonceStr'] = random(8);
        $option['package'] = 'prepay_id=' . $prepayid;
        $option['signType'] = 'MD5';
        ksort($option, SORT_STRING);
        foreach ($option as $key => $v) {
            $string .= "{$key}={$v}&";
        }
        $string .= "key=" . WxPayConfig::$key;
        $option['paySign'] = strtoupper(md5($string));

        $result = array('s' => '1', 'msg' => $option);

        return $result;
    }

    /**
     * 聊天室
     */
    public function chatInterface() {
        require_once DISCUZ_ROOT . "./source/plugin/wxz_live/lib/tis/interface.php";
    }

    /**
     * 添加评论
     */
    public function addcomment() {
        $rid = (int) $_GET['rid'];
        $toid = (int) $_GET['toid'];
        //获取直播间详情
        $liveInfo = C::t('#wxz_live#wxz_live_room')->getById($rid);

        $liveSettingInfo = C::t('#wxz_live#wxz_live_room')->getRoomSetting($liveInfo['id']);
        $liveInfo = C::t('#wxz_live#wxz_live_room')->formatRoomData($liveInfo);

        $user = C::t('#wxz_live#wxz_live_user')->authUser($liveSettingInfo);
        $uid = $user['id'];

        $tableCommentObj = new table_wxz_live_base(array('table' => 'wxz_live_comment', 'pk' => 'id'));
        $tablePollingObj = new table_wxz_live_base(array('table' => 'wxz_live_polling', 'pk' => 'id'));

        $condition = "id={$toid} AND rid={$rid}";
        $touser = $tableCommentObj->getAll($condition);

        $data = array(
            'uid' => $uid,
            'ip' => getip(),
            'is_auth' => $liveInfo['check_comment'] == 1 ? 2 : 1,
            'nickname' => $user['nickname'],
            'headimgurl' => $user['headimgurl'],
            'rid' => $rid,
            'content' => $_GET['content'],
            'toid' => $_GET['toid'],
            'touid' => (int) $touser['uid'],
            'tonickname' => (string) $touser['nickname'],
            'toheadimgurl' => (string) $touser['headimgurl'],
            'create_at' => date('Y-m-d H:i:d'),
        );

        $id = $tableCommentObj->insert($data, true);

        if ($id) {
            if ($liveInfo['check_comment'] == '1') {
                $result = array('s' => '1', 'msg' => '提交成功，审核成功后显示', 'isshow' => 0);
            } else {
                $pollingData = array(
                    'rid' => $rid,
                    'type' => 1,
                    'comment_id' => $id,
                );
                $pid = $tablePollingObj->insert($pollingData, true);

                $result = array('s' => '1', 'msg' => '提交成功', 'pid' => $pid, 'isshow' => 1);
            }
        } else {
            $result = array('s' => '-2', 'msg' => '提交失败，请联系管理员');
        }

        echo json_encode($result);
        exit;

        $result = array('s' => '-2', 'msg' => '您已被禁言！', 'pid' => $pid);
        echo json_encode($result);
        exit;
    }

    /**
     * 聊天室图片上传
     */
    public function headupload() {
        $rid = intval($_GET['rid']);
        $tableViewerObj = new table_wxz_live_base(array('table' => 'wxz_live_viewer', 'pk' => 'id'));
        $tableCommentObj = new table_wxz_live_base(array('table' => 'wxz_live_comment', 'pk' => 'id'));
        $tablePollingObj = new table_wxz_live_base(array('table' => 'wxz_live_polling', 'pk' => 'id'));

        $liveInfo = C::t('#wxz_live#wxz_live_room')->getById($rid);
        $liveSettingInfo = C::t('#wxz_live#wxz_live_room')->getRoomSetting($liveInfo['id']);
        $user = C::t('#wxz_live#wxz_live_user')->authUser($liveSettingInfo);
        $uid = $user['id'];

        if (!$user) {
            $result = array('s' => '-1', 'msg' => '授权出错！');
            echo json_encode($result);
            exit;
        }

        $condition = "rid={$rid} and uid={$uid}";
        $viewer = $tableViewerObj->getRow($condition);

        if ($viewer['isshutup'] == 1) {
            $pollingData = array(
                'rid' => $rid,
                'type' => 2
            );
            $pid = $tablePollingObj->insert($pollingData, true);
            $result = array('s' => '-2', 'msg' => '您已被禁言！', 'pid' => $pid);

            echo json_encode($result);
            exit;
        }

        $condition = "id='{$_GET['toid']}' AND rid={$rid}";
        $touser = $tableCommentObj->getRow($condition);

        $images = wxz_uploadimg();

        if (is_error($images)) {
            $result['error']['message'] = $images['message'];
            die(json_encode($result));
        }

        if (!($images['upload'])) {
            $result['error']['message'] = '上传失败，请重试！';
            die(json_encode($result));
        }


        $data = array(
            'uid' => $uid,
            'ip' => getip(),
            'is_auth' => $liveInfo['is_auth'] == 1 ? 2 : 1,
            'nickname' => $user['nickname'],
            'headimgurl' => $user['headimgurl'],
            'rid' => $rid,
            'content' => $images['upload'],
            'toid' => $_GET['toid'],
            'touid' => $touser['uid'],
            'tonickname' => $touser['nickname'],
            'toheadimgurl' => $touser['headimgurl'],
            'ispic' => 1,
            'create_at' => date('Y-m-d H:i:d'),
        );


        $id = $tableCommentObj->insert($data, true);

        if ($id) {
            if ($liveInfo['is_auth'] == '1') {
                $result = array('s' => '1', 'msg' => '提交成功，审核成功后显示');
            } else {

                $pollingData = array(
                    'rid' => $rid,
                    'type' => 1,
                    'comment_id' => $id,
                );
                $pid = $tablePollingObj->insert($pollingData, true);
                $result = array('s' => '1', 'msg' => '提交成功', 'pid' => $pid, 'content' => $images['upload']);
            }
        } else {
            $result = array('s' => '-2', 'msg' => '提交失败，请联系管理员');
        }

        $info = array(
            'name' => $images['upload'],
            'filename' => $images['upload'],
            'attachment' => $images['upload'],
            'url' => $images['upload'],
            'is_image' => 1,
        );

        die(json_encode($info));
    }

    /**
     * 评论分页
     */
    public function commentpage() {
        global $_G;

        $tableCommentObj = new table_wxz_live_base(array('table' => 'wxz_live_comment', 'pk' => 'id'));

        $rid = intval($_GET['rid']);
        $pindex = max(0, intval($_GET['page']));

        $psize = 15;
        $start = ($pindex) * $psize;
        $condition = " rid = '{$rid}' and is_auth = 1";
        $order = " id desc";
        $limit = $start . ',' . $psize;
        $field = "id,uid,nickname,headimgurl,content,ispacket,tonickname,create_at,touid,dsid,giftid,giftnum,giftpic,ispic";

        $list = $tableCommentObj->getAll($condition, $field, $order, $limit);
        $list = array_values($list);
        krsort($list);

        foreach ($list as $key => $v) {
            if ($v['giftid'] > 0) {
                $content = $v['nickname'] . '送出了<img src="' . $v['giftpic'] . '" width="45px" style="position: absolute;top: -15px;"><span style="margin-left:50px">x' . $v['giftnum'] . '</span>';

                $list[$key]['content'] = $content;
                $list[$key]['type'] = 'gift';
            } elseif ($v['dsid'] > 0) {
                if ($v['touid'] == 0) {
                    $content = $v['nickname'] . '给主播打赏了1个<span>红包</span>';
                } else {
                    $content = $v['nickname'] . '给' . $v['tonickname'] . '打赏了1个<span>红包</span>';
                }
                $list[$key]['content'] = $content;
                $list[$key]['type'] = 'reward';
            } elseif ($v['ispacket'] == 1) {
                $list[$key]['type'] = 'grouppacket';
            } else {
                $list[$key]['type'] = 'comment';
            }
        }
        $list = array_values($list);
        $list = $list ? $list : array();
        $result = array('s' => '1', 'content' => $list);
        echo json_encode($result);
        exit;
    }

    /**
     * 用户设置点赞 
     */
    public function setzan() {
        ob_end_clean();
        $tableZanNumObj = new table_wxz_live_base(array('table' => 'wxz_live_zannum', 'pk' => 'id'));

        $rid = intval($_GET['rid']);
        $user = C::t('#wxz_live#wxz_live_user')->authUser();
        $uid = $user['id'];

        $liveInfo = C::t('#wxz_live#wxz_live_room')->getById($rid);

        if (!$liveInfo) {
            $result = array('s' => '-1', 'msg' => '直播间不存在', 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
        }

        $condition = "rid={$rid} AND uid={$uid}";
        $zan = $tableZanNumObj->getRow($condition);

        if (!$zan) {
            $num = 1;
            $data = array(
                'uid' => $uid,
                'rid' => $rid,
                'num' => $num
            );
            $tableZanNumObj->insert($data);
        } else {
            $num = $zan['num'] + 1;
            $tableZanNumObj->updateById($zan['id'], array('num' => $num));
        }

        $condition = "rid={$rid}";
        $totalzannum = $tableZanNumObj->getRow($condition, 'sum(num) total_num');
        $totalzannum = $totalzannum && $totalzannum['total_num'] ? $totalzannum['total_num'] : 0;

        $result['s'] = 1;
        $result['num'] = $totalzannum;
        echo json_encode($result);
        exit;
    }

    /**
     * 打赏
     */
    public function setreward() {
        global $_G;
        $tableReward = new table_wxz_live_base(array('table' => 'wxz_live_reward', 'pk' => 'id'));

        $wxpayPath = DISCUZ_ROOT . "./source/plugin/wxz_live/lib/wxpay/";
        require_once "{$wxpayPath}lib/WxPay.Config.php";

        $rid = intval($_GET['rid']);
        $type = intval($_GET['type']);
        $money = floatval($_GET['money']);
        $touid = intval($_GET['touid']);
        $tonickname = intval($_GET['tonickname']);
        $toheadurl = intval($_GET['toheadurl']);

        $user = C::t('#wxz_live#wxz_live_user')->authUser('', false);
        $uid = $user['id'];

        $isweixin = 1;
        if ($type == 2) {
            $money = $money * 100;
        }

        if (empty($rid)) {
            $result = array('s' => '-1', 'msg' => '直播不存在', 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
        }


        if (empty($money) || $money < 1) {
            $result = array('s' => '-1', 'msg' => '最少为0.01元哦！', 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
        }

        if (!$uid || $uid == 0) {
            $result = array('s' => '-1', 'msg' => '用户不存在！', 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
        }

        if (!$user) {
            $result = array('s' => '-1', 'msg' => '用户不存在！', 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
        }

        if ($touid > 0) {
            $touser = C::t('#wxz_live#wxz_live_user')->getById($touid);
            if (!$touser) {
                $touser['nickname'] = $tonickname;
                $touser['headurl'] = $toheadurl;
            }
        }

        //生成订单
        $orderNo = C::t('#wxz_live#wxz_live_order')->getOrderNo();
        $orderData = array(
            'order_no' => $orderNo,
            'rid' => $rid,
            'uid' => $uid,
            'order_type' => 3,
            'money' => $money,
            'pay_money' => $money,
            'create_at' => date('Y-m-d H:i:s'),
        );
        $orderId = C::t('#wxz_live#wxz_live_order')->insert($orderData, true);

        $insertData = array(
            'uid' => $uid,
            'order_id' => $orderId,
            'fee' => $money,
            'rid' => $rid,
            'touid' => $touid,
            'tonickname' => $touser['nickname'],
            'toheadurl' => $touser['headurl'],
            'create_at' => date('Y-m-d H:i:s'),
        );

        $id = $tableReward->insert($insertData, true);

        $params = array(
            'fee' => $money,
            'user' => $user['openid'],
            'random' => $orderNo,
        );

        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/lib/wxz_weixin.class.php";
        $wxzWeixin = new wxz_weixin();

        $notifyUrl = "{$_G['siteurl']}/source/plugin/wxz_live/notify.php"; //

        $attach = array('type' => 'reward');

        $package = array();
        $package['appid'] = WxPayConfig::$appid;
        $package['mch_id'] = WxPayConfig::$mchid;
        $package['nonce_str'] = random(8);
        $package['body'] = '打赏';
        $package['attach'] = json_encode($attach);
        $package['out_trade_no'] = $params['random'];
        $package['total_fee'] = $params['fee'];
        $package['spbill_create_ip'] = getip();
        $package['time_start'] = date('YmdHis', time());
        $package['time_expire'] = date('YmdHis', time() + 600);
        $package['notify_url'] = $notifyUrl;
        $package['trade_type'] = 'JSAPI';
        $package['openid'] = $user['openid'];
//        $package['openid'] = 'o11S7wvMcT_2g8WPHVMZtGEL7mz4'; //debug

        ksort($package, SORT_STRING);
        $string1 = '';
        foreach ($package as $key => $v) {
            if (empty($v)) {
                continue;
            }
            $string1 .= "{$key}={$v}&";
        }

        $string1 .= "key=" . WxPayConfig::$key;
        $package['sign'] = strtoupper(md5($string1));
        $dat = array2xml($package);
        $response = ihttp_request('https://api.mch.weixin.qq.com/pay/unifiedorder', $dat);

        $xml = @isimplexml_load_string($response['content'], 'SimpleXMLElement', LIBXML_NOCDATA);

        if (is_error($response)) {
            $result = array('s' => '-1', 'msg' => strval($xml->return_msg), 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
            return $response;
        }


        if (strval($xml->return_code) == 'FAIL') {
            $result = array('s' => '-1', 'msg' => strval($xml->return_msg), 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
        }

        if (strval($xml->result_code) == 'FAIL') {
            $result = array('s' => '-1', 'msg' => strval($xml->return_msg), 'isweixin' => $isweixin);

            echo json_encode($result);
            exit;
        }

        $prepayid = $xml->prepay_id;
        $option['appId'] = WxPayConfig::$appid;
        $option['timeStamp'] = TIMESTAMP;
        $option['nonceStr'] = random(8);
        $option['package'] = 'prepay_id=' . $prepayid;
        $option['signType'] = 'MD5';
        ksort($option, SORT_STRING);
        foreach ($option as $key => $v) {
            $string .= "{$key}={$v}&";
        }
        $string .= "key=" . WxPayConfig::$key;
        $option['paySign'] = strtoupper(md5($string));
        $option['status'] = 1;
        $option['res'] = 'ok';

        $result = array('s' => '1', 'msg' => $option, 'isweixin' => $isweixin);
        if ($touid > 0) {
            $result['content'] = $user['nickname'] . '给' . $touser['nickname'] . '打赏了1个<span>红包</span>';
        } else {
            $result['content'] = $user['nickname'] . '给主播打赏了1个<span>红包</span>';
        }
        $result['id'] = $id;
        $result ['type'] = 'reward';
        echo json_encode($result);
        exit;
    }

    /**
     * 群红包
     */
    public function setpacket() {
        global $_G;
        $tableGrouppacket = new table_wxz_live_base(array('table' => 'wxz_live_grouppacket', 'pk' => 'id'));

        $wxpayPath = DISCUZ_ROOT . "./source/plugin/wxz_live/lib/wxpay/";
        require_once "{$wxpayPath}lib/WxPay.Config.php";

        $user = C::t('#wxz_live#wxz_live_user')->authUser('', false);
        $uid = $user['id'];

        $openid = $user['openid'];
        $rid = intval($_GET['rid']);
        $nums = intval($_GET['nums']);
        $remark = ($_GET['note']);
        $total_fee = floatval($_GET['total_fee']) * 100;
        $rtype = intval($_GET['rtype']);
        $isweixin = 1;
        if (empty($rid)) {
            $result = array('s' => '-1', 'msg' => '直播不存在', 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
        }

        if (empty($total_fee) || $total_fee < 1) {
            $result = array('s' => '-1', 'msg' => '最少为0.01元哦！', 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
        }

        if (!$user) {
            $result = array('s' => '-1', 'msg' => '您的信息不存在', 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
        }

        //生成订单
        $orderNo = C::t('#wxz_live#wxz_live_order')->getOrderNo();
        $orderData = array(
            'order_no' => $orderNo,
            'rid' => $rid,
            'uid' => $uid,
            'order_type' => 4,
            'money' => $total_fee,
            'pay_money' => $total_fee,
            'create_at' => date('Y-m-d H:i:s'),
        );
        $orderId = C::t('#wxz_live#wxz_live_order')->insert($orderData, true);

        //插入群红包
        $moneys = randBonus($total_fee / 100, $nums, $rtype);
        $data = array(
            'order_id' => $orderId,
            'uid' => $uid,
            'rid' => $rid,
            'type' => $rtype,
            'amount' => $total_fee,
            'num' => $nums,
            'status' => 0,
            'remark' => $remark,
            'json' => iserializer($moneys),
            'create_at' => date('Y-m-d H:i:s'),
        );
        $id = $tableGrouppacket->insert($data, true);

        $params = array(
            'fee' => $total_fee,
            'user' => $openid,
            'random' => $orderNo,
        );

        $attach = array('type' => 'grouppacket');
        $notifyUrl = "{$_G['siteurl']}/source/plugin/wxz_live/notify.php"; //

        $package = array();
        $package['appid'] = WxPayConfig::$appid;
        $package['mch_id'] = WxPayConfig::$mchid;
        $package['nonce_str'] = random(8);
        $package['body'] = '群红包';
        $package['attach'] = json_encode($attach);
        $package['out_trade_no'] = $params['random'];
        $package['total_fee'] = $params['fee'];
        $package['spbill_create_ip'] = getip();
        $package['time_start'] = date('YmdHis', time());
        $package['time_expire'] = date('YmdHis', time() + 600);
        $package['notify_url'] = $notifyUrl;
        $package['trade_type'] = 'JSAPI';
        $package['openid'] = $user['openid'];
//        $package['openid'] = 'o11S7wvMcT_2g8WPHVMZtGEL7mz4'; //debug

        ksort($package, SORT_STRING);
        $string1 = '';
        foreach ($package as $key => $v) {
            if (empty($v)) {
                continue;
            }
            $string1 .= "{$key}={$v}&";
        }
        $string1 .= "key=" . WxPayConfig::$key;
        $package['sign'] = strtoupper(md5($string1));
        $dat = array2xml($package);
        $response = ihttp_request('https://api.mch.weixin.qq.com/pay/unifiedorder', $dat);

        $xml = @isimplexml_load_string($response['content'], 'SimpleXMLElement', LIBXML_NOCDATA);

        if (is_error($response)) {
            $result = array('s' => '-1', 'msg' => strval($xml->return_msg), 'isweixin' => $isweixin);

            echo json_encode($result);
            exit;
            return $response;
        }

        if (strval($xml->return_code) == 'FAIL') {
            $result = array('s' => '-1', 'msg' => strval($xml->return_msg), 'isweixin' => $isweixin);

            echo json_encode($result);
            exit;
        }

        if (strval($xml->result_code) == 'FAIL') {
            $result = array('s' => '-1', 'msg' => strval($xml->return_msg), 'isweixin' => $isweixin);

            echo json_encode($result);
            exit;
        }
        $prepayid = $xml->prepay_id;
        $option['appId'] = WxPayConfig::$appid;
        $option['timeStamp'] = TIMESTAMP;
        $option['nonceStr'] = random(8);
        $option['package'] = 'prepay_id=' . $prepayid;
        $option['signType'] = 'MD5';
        ksort($option, SORT_STRING);
        foreach ($option as $key => $v) {
            $string .= "{$key}={$v}&";
        }
        $string .= "key=" . WxPayConfig::$key;
        $option['paySign'] = strtoupper(md5($string));
        $option['status'] = 1;
        $option['res'] = 'ok';
        $result = array('s' => '1', 'msg' => $option, 'isweixin' => $isweixin);

        $result['id'] = $id;
        $result['type'] = 'grouppacket';
        echo json_encode($result);
        exit;
    }

    /**
     * 赠送礼物
     */
    public function setgift() {
        global $_G;

        $tableGift = new table_wxz_live_base(array('table' => 'wxz_live_gift', 'pk' => 'id'));
        $tableGiftLog = new table_wxz_live_base(array('table' => 'wxz_live_giftlog', 'pk' => 'id'));

        $wxpayPath = DISCUZ_ROOT . "./source/plugin/wxz_live/lib/wxpay/";
        require_once "{$wxpayPath}lib/WxPay.Config.php";

        $user = C::t('#wxz_live#wxz_live_user')->authUser('', false);
        $uid = $user['id'];
        $openid = $user['openid'];

        $rid = intval($_GET['rid']);
        $num = intval($_GET['num']);
        $id = intval($_GET['gid']);
        $isweixin = 1;

        if (empty($rid)) {
            $result = array('s' => '-1', 'msg' => '直播不存在', 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
        }

        $gift = $tableGift->getById($id);
        $money = $gift['amount'] * $num;

        if (empty($money) || $money < 1) {
            $result = array('s' => '-1', 'msg' => '最少为0.01元哦！', 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
        }

        if (!$user) {
            $result = array('s' => '-1', 'msg' => '您的信息不存在', 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
        }

        //生成订单
        $orderNo = C::t('#wxz_live#wxz_live_order')->getOrderNo();
        $orderData = array(
            'order_no' => $orderNo,
            'rid' => $rid,
            'uid' => $uid,
            'order_type' => 5,
            'money' => $money,
            'pay_money' => $money,
            'create_at' => date('Y-m-d H:i:s'),
        );
        $orderId = C::t('#wxz_live#wxz_live_order')->insert($orderData, true);

        $datas = array(
            'uid' => $uid,
            'order_id' => $orderId,
            'giftid' => $id,
            'num' => $num,
            'rid' => $rid,
            'status' => 0,
            'create_at' => date('Y-m-d H:i:s'),
        );

        $id = $tableGiftLog->insert($datas, true);

        $params = array(
            'fee' => $money,
            'user' => $openid,
            'random' => $orderNo,
        );

        $attach = array('type' => 'gift');
        $notifyUrl = "{$_G['siteurl']}/source/plugin/wxz_live/notify.php"; //

        $package = array();
        $package['appid'] = WxPayConfig::$appid;
        $package['mch_id'] = WxPayConfig::$mchid;
        $package['nonce_str'] = random(8);
        $package['body'] = '赠送礼物';
        $package['attach'] = json_encode($attach);
        $package['out_trade_no'] = $params['random'];
        $package['total_fee'] = $params['fee'];
        $package['spbill_create_ip'] = getip();
        $package['time_start'] = date('YmdHis', time());
        $package['time_expire'] = date('YmdHis', time() + 600);
        $package['notify_url'] = $notifyUrl;
        $package['trade_type'] = 'JSAPI';
        $package['openid'] = $user['openid'];

        ksort($package, SORT_STRING);
        $string1 = '';
        foreach ($package as $key => $v) {
            if (empty($v)) {
                continue;
            }
            $string1 .= "{$key}={$v}&";
        }

        $string1 .= "key=" . WxPayConfig::$key;
        $package['sign'] = strtoupper(md5($string1));
        $dat = array2xml($package);
        $response = ihttp_request('https://api.mch.weixin.qq.com/pay/unifiedorder', $dat);
        $xml = @isimplexml_load_string($response['content'], 'SimpleXMLElement', LIBXML_NOCDATA);

        if (is_error($response)) {
            $result = array('s' => '-1', 'msg' => strval($xml->return_msg), 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
            return $response;
        }

        if (strval($xml->return_code) == 'FAIL') {
            $result = array('s' => '-1', 'msg' => strval($xml->return_msg), 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
        }

        if (strval($xml->result_code) == 'FAIL') {
            $result = array('s' => '-1', 'msg' => strval($xml->return_msg), 'isweixin' => $isweixin);
            echo json_encode($result);
            exit;
        }

        $prepayid = $xml->prepay_id;
        $option['appId'] = WxPayConfig::$appid;
        $option['timeStamp'] = TIMESTAMP;
        $option['nonceStr'] = random(8);
        $option['package'] = 'prepay_id=' . $prepayid;
        $option['signType'] = 'MD5';
        ksort($option, SORT_STRING);
        foreach ($option as $key => $v) {
            $string .= "{$key}={$v}&";
        }
        $string .= "key=" . WxPayConfig::$key;
        $option['paySign'] = strtoupper(md5($string));
        $option['status'] = 1;
        $option['res'] = 'ok';
        $result = array('s' => '1', 'msg' => $option, 'isweixin' => $isweixin);
        $result['id'] = $id;
        $result['type'] = 'gift';
        echo json_encode($result);
        exit;
    }

}