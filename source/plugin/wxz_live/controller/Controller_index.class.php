<?php

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
        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/table/table_wxz_live_base.php";

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
        $cid = intval($_GET['cid']);

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
            cpmsg('系统错误, 详情: ' . $resp['message']);
        }
        $auth = @json_decode($resp['content'], true);
        if (is_array($auth) && !empty($auth['openid'])) {
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$auth['access_token']}&openid={$auth['openid']}&lang=zh_CN";
            $resp = ihttp_get($url);
            if (is_error($resp)) {
                cpmsg('系统错误2');
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
                    $user['headimgurl'] = rtrim($user['avatar'], '0');
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
        cpmsg('系统错误, 详情: 未获取到access token');
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
        $roomNo = $_GET['roomno'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        //获取直播间详情
        $liveInfo = C::t('#wxz_live#wxz_live_room')->getByRoomNo($roomNo);
        $liveSettingInfo = C::t('#wxz_live#wxz_live_room')->getRoomSetting($liveInfo['id']);

        $liveInfo = C::t('#wxz_live#wxz_live_room')->formatRoomData($liveInfo);

        $user = C::t('#wxz_live#wxz_live_user')->authUser($liveSettingInfo);

        $rid = $liveInfo['id'];
        $uid = $user['id'];

        if (!$liveInfo) {
            showmessage('直播间不存在');
        }

        $shutup = 0; //黑名单
        $totalzannum = 0; //赞总数
        //获取所有
        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/table/table_wxz_live_base.php";
        $tableViewerObj = new table_wxz_live_base(array('table' => 'wxz_live_viewer', 'pk' => 'id'));
        $tablePollingObj = new table_wxz_live_base(array('table' => 'wxz_live_polling', 'pk' => 'id'));

        //直播间管理员
        $condition = "room_id={$rid} AND role=2";
        $roomadmins = $tableViewerObj->getAll($condition, 'uid');
        $roomadmin = json_encode($roomadmins);

        $pic = array();
        $pics = json_encode($pic); //获取所有赞图片

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


        //评论列表
        $Comments = C::t('#wxz_live#wxz_live_room')->getComments($rid);
        $pid = $tablePollingObj->getAll("rid={$rid}", 'id', 'id desc', 1);
        $pid = $pid ? end($pid) : array();

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
        if ($list['type'] == 8) {
            $loginurl = 'http://interface.yy.com/hls/get/0/' . $playerInfo['settings']['sid'] . '/' . $playerInfo['settings']['ssid'] . '?appid=0&excid=1200&type=m3u8&isHttps=0&callback=jsonp2';
            $response = ihttp_request($loginurl, array(), array(
                'CURLOPT_REFERER' => 'http://wap.yy.com/mobileweb/' . $playerInfo['settings']['sid'] . '/' . $playerInfo['settings']['ssid'] . '?tempId=' . $playerInfo['settings']['tpl'] . ''
            ));
            $result = json_decode(substr($response['content'], 7, -1), true);
            if ($result['code'] == 0) {
                $playerInfo['settings']['hls'] = $result['hls'];
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
        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/table/table_wxz_live_base.php";

        $tablePlayerObj = new table_wxz_live_base(array('table' => 'wxz_live_viewer', 'pk' => 'id'));

        $condition = "room_id={$roomId} AND uid={$user['id']}";
        $viewer = $tablePlayerObj->getRow($condition);

        if (!$viewer) {
            $data['room_id'] = $roomId;
            $data['uid'] = $user['id'];
            $data['create_at'] = date('Y-m-d H:i:s');
            $data['id'] = $tablePlayerObj->insert($data, true);
            $viewer = $data;
        }
        return $viewer;
    }

    /**
     * @desc
     * @param
     * @return
     */
    public function limit() {
        $type = $_GET['type'];
        $roomId = $_GET['rid'];
        $password = $_GET['password'];

        $liveInfo = C::t('#wxz_live#wxz_live_room')->getById($roomId);
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
            $condition = "room_id={$liveInfo['id']} AND uid={$user['id']}";
            $tableViewerObj->updateData($condition, $data);
            $result['s'] = 1;
            $result['msg'] = '密码正确';
            echo json_encode($result);
            exit;
        }
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
        $uid = $userp['id'];

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

}

?>
