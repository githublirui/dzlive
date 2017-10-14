<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT . "./source/plugin/wxz_live/table/table_wxz_live_base.php";

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
        } else {
            $data['create_at'] = date('Y-m-d H:i:s');
            $ret = $this->insert($data, true);
        }

        return $ret;
    }

    public function getByOpenId($openid) {
        $condition = "openid='{$openid}'";
        return $this->getRow($condition);
    }

    /**
     * 用户授权插入用户
     * @param type $param
     */
    public function authUser($settings) {
        global $_G;
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $key = "wxz_live_user_info";

        $userInfo = getcookie($key);

        if ($userInfo) {
            $userInfo = unserialize($userInfo);
            if (!$userInfo['headimgurl']) {
                $userInfo['headimgurl'] = $settings['default_avatar'];
            }
            return $userInfo;
        }

        if (strpos($userAgent, 'MicroMessenger') === false) {
            $openid = getip();
            $openid = '221.216.152.136'; //debug
            if (!$openid) {
                showmessage("确认身份失败,无法访问");
            }
            $ipInfo = getIpInfo($openid);

            $data = array(
                'openid' => $openid,
                'province' => mb_substr($ipInfo['data']['region'], 0, -1),
                'ip' => $openid,
                'city' => mb_substr($ipInfo['data']['city'], 0, -1),
                'headimgurl' => $settings['default_avatar'],
                'nickname' => empty($ipInfo['data']['region']) ? '网友' : $ipInfo['data']['region'] . '网友',
                'sex' => 0,
            );
            $this->updateUser($data);
            $userInfo = $data;
        } else {
            include_once DISCUZ_ROOT . "./source/plugin/wxz_live/lib/wxz_weixin.class.php";
            $wxz_weixin = new wxz_weixin();
            $user = $wxz_weixin->mc_oauth_userinfo();
            if (!$user['headimgurl']) {
                $user['headimgurl'] = $settings['default_avatar'];
            }
            $userInfo = $user;
        }

        dsetcookie($key, serialize($userInfo), 2592000);
        return $userInfo;
    }

}

?>