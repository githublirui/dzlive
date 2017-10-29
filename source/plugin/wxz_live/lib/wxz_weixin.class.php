<?php

/**
 * Description of wxz_weixin
 *
 * @author Administrator
 */
include_once DISCUZ_ROOT . "./source/plugin/wechat/wechat.lib.class.php";

class wxz_weixin extends WeChatClient {

    public $appid;
    public $appsecret;

    public function __construct($appid = '', $appsecret = '') {
        global $_G;

        $_G['wechat']['setting'] = unserialize($_G['setting']['mobilewechat']);

        if (!$appid) {
            $appid = $_G['wechat']['setting']['wechat_appId'];
        }
        if (!$appsecret) {
            $appsecret = $_G['wechat']['setting']['wechat_appsecret'];
        }

        parent::__construct($appid, $appsecret);

        $this->appid = $appid;
        $this->appsecret = $appsecret;
    }

    /**
     * 获取getJsApiTicket
     * @return type
     */
    public function getJsApiTicket() {
        global $_G;
        $appid = $this->appid;
        $appsecret = $this->appsecret;

        $cachename = "wechat_jsticket_" . $appid;
        loadcache($cachename);

        $cache = $_G['cache'][$cachename];

        if (!empty($cache) && !empty($cache['ticket']) && $cache['expire'] > TIMESTAMP) {
            return $cache['ticket'];
        }

        $access_token = $this->getAccessToken();
        if (!$access_token) {
            error(-1, '获取微信公众号 jsapi_ticket 结果错误, 错误信息: ');
        }

        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi";
        $content = ihttp_get($url);
        if (is_error($content)) {
            return error(-1, '调用接口获取微信公众号 jsapi_ticket 失败, 错误信息: ' . $content['message']);
        }
        $result = @json_decode($content['content'], true);
        if (empty($result) || intval(($result['errcode'])) != 0 || $result['errmsg'] != 'ok') {
            return error(-1, '获取微信公众号 jsapi_ticket 结果错误, 错误信息: ' . $result['errmsg']);
        }
        $record = array();
        $record['ticket'] = $result['ticket'];
        $record['expire'] = TIMESTAMP + $result['expires_in'] - 200;
        $this->account['jsapi_ticket'] = $record;
        savecache($cachename, $record);
        return $record['ticket'];
    }

    public function getJssdkConfig($url = '') {
        global $_G;
        $jsapiTicket = $this->getJsApiTicket();
        if (is_error($jsapiTicket)) {
            $jsapiTicket = $jsapiTicket['message'];
        }

        $siteUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];

        $nonceStr = random(16);
        $timestamp = TIMESTAMP;
        $url = empty($url) ? $siteUrl : $url;
        $string1 = "jsapi_ticket={$jsapiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
        $signature = sha1($string1);
        $config = array(
            "appId" => $this->appid,
            "nonceStr" => $nonceStr,
            "timestamp" => "$timestamp",
            "signature" => $signature,
        );
        return $config;
    }

    /**
     * 获取授权用户信息并插入用户表
     */
    public function mc_oauth_userinfo() {
        global $_G;
        $key = 'wxz_openid_' . $this->appid;
        $openId = getcookie($key);

        if ($openId) {
            $userInfo = C::t('#wxz_live#wxz_live_user')->getByOpenId($openId);
            if ($userInfo) {
                return $userInfo;
            }
        }

        $state = $_SERVER['REQUEST_URI'];
        $stateKey = substr(md5($state), 0, 8);

        dsetcookie('wxz_forward', $state, 120);

        $oauthUrl = "{$_G['siteurl']}plugin.php?id=wxz_live:index&pmod=index&act=wxoauth";
        $oauthUrl = $this->getOAuthConnectUri($oauthUrl, $stateKey, 'snsapi_userinfo');
        header('Location: ' . $oauthUrl);
        exit;
    }

    /**
     * 发送模板消息
     * @param type $touser
     * @param type $template_id
     * @param type $postdata
     * @param type $url
     * @param type $topcolor
     * @return boolean
     */
    public function sendTplNotice($touser, $template_id, $postdata, $url = '', $topcolor = '#FF683F') {
        if (empty($touser)) {
            return error(-1, '参数错误,粉丝openid不能为空');
        }
        if (empty($template_id)) {
            return error(-1, '参数错误,模板标示不能为空');
        }
        if (empty($postdata) || !is_array($postdata)) {
            return error(-1, '参数错误,请根据模板规则完善消息内容');
        }
        $token = $this->getAccessToken();
        if (is_error($token)) {
            return $token;
        }

        $data = array();
        $data['touser'] = $touser;
        $data['template_id'] = trim($template_id);
        $data['url'] = trim($url);
        $data['topcolor'] = trim($topcolor);
        $data['data'] = $postdata;
        $data = json_encode($data);
        $post_url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$token}";
        $response = ihttp_request($post_url, $data);
        if (is_error($response)) {
            return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
        }
        $result = @json_decode($response['content'], true);
        if (empty($result)) {
            return error(-1, "接口调用失败, 元数据: {$response['meta']}");
        } elseif (!empty($result['errcode'])) {
            return error(-1, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']},信息详情：{$this->error_code($result['errcode'])}");
        }
        return true;
    }

}

?>
