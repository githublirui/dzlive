<?php


if (!defined('QINIU_FUNCTIONS_VERSION')) {define('QINIU_FUNCTIONS_VERSION', Qiniu_Config::SDK_VER);}

/*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
final class Qiniu_common_function {



   /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    static function crc32_file($file)
    {
        $hash = hash_file('crc32b', $file);
        $array = unpack('N', pack('H*', $hash));
        return sprintf('%u', $array[1]);
    }

   /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    static function crc32_data($data)
    {
        $hash = hash('crc32b', $data);
        $array = unpack('N', pack('H*', $hash));
        return sprintf('%u', $array[1]);
    }

   /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    static function base64_urlSafeEncode($data)
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }

   /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    static function base64_urlSafeDecode($str)
    {
        $find = array('-', '_');
        $replace = array('+', '/');
        return base64_decode(str_replace($find, $replace, $str));
    }

   /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    static function json_decode($json, $assoc = false, $depth = 512)
    {
        static $jsonErrors = array(
            JSON_ERROR_DEPTH => 'JSON_ERROR_DEPTH - Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'JSON_ERROR_STATE_MISMATCH - Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'JSON_ERROR_CTRL_CHAR - Unexpected control character found',
            JSON_ERROR_SYNTAX => 'JSON_ERROR_SYNTAX - Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'JSON_ERROR_UTF8 - Malformed UTF-8 characters, possibly incorrectly encoded'
        );

        if (empty($json)) {
            return null;
        }
        $data = \json_decode($json, $assoc, $depth);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $last = json_last_error();
            throw new \InvalidArgumentException(
                'Unable to parse JSON data: '
                . (isset($jsonErrors[$last])
                    ? $jsonErrors[$last]
                    : 'Unknown error')
            );
        }

        return $data;
    }

   /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    static function entry($bucket, $key)
    {
        $en = $bucket;
        if (!empty($key)) {
            $en = $bucket . ':' . $key;
        }
        return base64_urlSafeEncode($en);
    }

    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    static function setWithoutEmpty(&$array, $key, $value)
    {
        if (!empty($value)) {
            $array[$key] = $value;
        }
        return $array;
    }
    
    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    // static function thumbnail(
    //     $url,
    //     $mode,
    //     $width,
    //     $height,
    //     $format = null,
    //     $quality = null,
    //     $interlace = null,
    //     $ignoreError = 1
    // ) {
    //     static $imageUrlBuilder = null;
    //     if (is_null($imageUrlBuilder)) {
    //         $imageUrlBuilder = new \Qiniu\Processing\ImageUrlBuilder;
    //     }

    //     return call_user_func_array(array($imageUrlBuilder, 'thumbnail'), func_get_args());
    // }

    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    // static function waterImg(
    //     $url,
    //     $image,
    //     $dissolve = 100,
    //     $gravity = 'SouthEast',
    //     $dx = null,
    //     $dy = null,
    //     $watermarkScale = null
    // ) {
    //     static $imageUrlBuilder = null;
    //     if (is_null($imageUrlBuilder)) {
    //         $imageUrlBuilder = new \Qiniu\Processing\ImageUrlBuilder;
    //     }

    //     return call_user_func_array(array($imageUrlBuilder, 'waterImg'), func_get_args());
    // }

    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    // static function waterText(
    //     $url,
    //     $text,
    //     $font = '黑体',
    //     $fontSize = 0,
    //     $fontColor = null,
    //     $dissolve = 100,
    //     $gravity = 'SouthEast',
    //     $dx = null,
    //     $dy = null
    // ) {
    //     static $imageUrlBuilder = null;
    //     if (is_null($imageUrlBuilder)) {
    //         $imageUrlBuilder = new \Qiniu\Processing\ImageUrlBuilder;
    //     }

    //     return call_user_func_array(array($imageUrlBuilder, 'waterText'), func_get_args());
    // }
}