<?php

/**
 * Description of wxz_weixin
 *
 * @author Administrator
 */
include_once DISCUZ_ROOT . "./source/plugin/wechat/wechat.lib.class.php";

class wxz_weixin extends WeChatClient {

    public function __construct($appid, $appsecret = '') {
        parent::__construct($appid, $appsecret);
        $this->_appid = $appid;
        $this->_appsecret = $appsecret;
    }

    /**
     * 获取getJsApiTicket
     * @return type
     */
    public function getJsApiTicket() {
        global $_G;
        $appid = $this->_appid;
        $appsecret = $this->_appsecret;

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
            "appId" => $this->_appid,
            "nonceStr" => $nonceStr,
            "timestamp" => "$timestamp",
            "signature" => $signature,
        );
        return $config;
    }

}

?>
