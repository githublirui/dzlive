<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_wxz_live_user extends table_wxz_live_base {

    public function __construct() {
        $this->_table = 'wxz_live_user';
        $this->_pk = 'id';

        parent::__construct();
    }

    /**
     * 更新用户信息
     */
    public function updateUser($data) {
        if (!$data['openid']) {
            return;
        }

        $condition = "openid='{$data['openid']}'";
        $info = $this->getRow($condition, 'id');

        if ($info) {
            $ret = $this->updateById($info['id'], $data);
            $data['id'] = $info['id'];
        } else {
            $data['create_at'] = date('Y-m-d H:i:s');
            $ret = $this->insert($data, true);
            $data['id'] = $ret;
        }

        return $data;
    }

    public function getByOpenId($openid) {
        $condition = "openid='{$openid}'";
        return $this->getRow($condition);
    }

    /**
     * 用户授权插入用户
     * @param type $param
     */
    public function authUser($settings = '', $fromCache = true) {
        global $_G;
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $key = "wxz_live_user_info";

        $userInfo = getcookie($key);

        if ($userInfo && $fromCache) {
            $userInfo = unserialize($userInfo);
            if (!$userInfo['headimgurl']) {
                $userInfo['headimgurl'] = $settings['default_avatar'];
            }
            return $userInfo;
        }

        if (strpos($userAgent, 'MicroMessenger') === false) {
            $openid = getip();
//            $openid = '221.216.152.136'; //debug
//            $openid = '185.186.147.191'; //debug
            if (!$openid) {
                showmessage("确认身份失败,无法访问");
            }
            $ipInfo = getIpInfo($openid);

            $data = array(
                'openid' => $openid,
                'province' => mb_substr($ipInfo['data']['region'], 0, -1),
                'ip' => $openid,
                'city' => mb_substr($ipInfo['data']['city'], 0, -1),
//                'nickname' => empty($ipInfo['data']['region']) ? '网友' : $ipInfo['data']['region'] . '网友',
                'nickname' => '匿名网友',
                'sex' => 0,
            );
            $userInfo = $this->updateUser($data);
        } else {
            include_once DISCUZ_ROOT . "./source/plugin/wxz_live/lib/wxz_weixin.class.php";
            $wxz_weixin = new wxz_weixin();
            $user = $wxz_weixin->mc_oauth_userinfo();
            $userInfo = $user;
        }

        dsetcookie($key, serialize($userInfo), 2592000);
        if (!$userInfo['headimgurl']) {
            $userInfo['headimgurl'] = $settings['default_avatar'];
        }
        return $userInfo;
    }

}