<?php
namespace Qiniu\Storage;

use Qiniu\Config;
use Qiniu\Http\HttpClient;
use Qiniu\Storage\ResumeUploader;
use Qiniu\Storage\FormUploader;

/*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
final class UploadManager
{
    private $config;

    public function __construct(Config $config = null)
    {
        if ($config === null) {
            $config = new Config();
        }
        $this->config = $config;
    }

    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    public function put(
        $upToken,
        $key,
        $data,
        $params = null,
        $mime = 'application/octet-stream',
        $checkCrc = false
    ) {
        $params = self::trimParams($params);
        return FormUploader::put(
            $upToken,
            $key,
            $data,
            $this->config,
            $params,
            $mime,
            $checkCrc
        );
    }


    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    public function putFile(
        $upToken,
        $key,
        $filePath,
        $params = null,
        $mime = 'application/octet-stream',
        $checkCrc = false
    ) {
        $file = fopen($filePath, 'rb');
        if ($file === false) {
            throw new \Exception("file can not open", 1);
        }
        $params = self::trimParams($params);
        $stat = fstat($file);
        $size = $stat['size'];
        if ($size <= Config::BLOCK_SIZE) {
            $data = fread($file, $size);
            fclose($file);
            if ($data === false) {
                throw new \Exception("file can not read", 1);
            }
            return FormUploader::put(
                $upToken,
                $key,
                $data,
                $this->config,
                $params,
                $mime,
                $checkCrc
            );
        }
        $up = new ResumeUploader(
            $upToken,
            $key,
            $file,
            $size,
            $params,
            $mime,
            $this->config
        );
        $ret = $up->upload();
        fclose($file);
        return $ret;
    }

    public static function trimParams($params)
    {
        if ($params === null) {
            return null;
        }
        $ret = array();
        foreach ($params as $k => $v) {
            $pos = strpos($k, 'x:');
            if ($pos === 0 && !empty($v)) {
                $ret[$k] = $v;
            }
        }
        return $ret;
    }
}
