<?php

namespace Qiniu\Processing;

use Qiniu\Http\Client;
use Qiniu\Http\Error;

final class Operation
{

    private $auth;
    private $token_expire;
    private $domain;

    public function __construct($domain, $auth = null, $token_expire = 3600)
    {
        $this->auth = $auth;
        $this->domain = $domain;
        $this->token_expire = $token_expire;
    }


    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    public function execute($key, $fops)
    {
        $url = $this->buildUrl($key, $fops);
        $resp = Client::get($url);
        if (!$resp->ok()) {
            return array(null, new Error($url, $resp));
        }
        if ($resp->json() !== null) {
            return array($resp->json(), null);
        }
        return array($resp->body, null);
    }

    public function buildUrl($key, $fops, $protocol = 'http')
    {
        if (is_array($fops)) {
            $fops = implode('|', $fops);
        }

        $url = $protocol."://$this->domain/$key?$fops";
        if ($this->auth !== null) {
            $url = $this->auth->privateDownloadUrl($url, $this->token_expire);
        }

        return $url;
    }
}
