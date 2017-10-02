<?php
namespace Qiniu\Processing;

use Qiniu;

/*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
final class ImageUrlBuilder
{
    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    protected $modeArr = array(0, 1, 2, 3, 4, 5);

    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    protected $formatArr = array('psd', 'jpeg', 'png', 'gif', 'webp', 'tiff', 'bmp');

    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    protected $gravityArr = array('NorthWest', 'North', 'NorthEast',
        'West', 'Center', 'East', 'SouthWest', 'South', 'SouthEast');

    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    public function thumbnail(
        $url,
        $mode,
        $width,
        $height,
        $format = null,
        $interlace = null,
        $quality = null,
        $ignoreError = 1
    ) {
    
        // url合法效验
        if (! $this->isUrl($url)) {
            return $url;
        }

        // 参数合法性效验
        if (! in_array(intval($mode), $this->modeArr, true)) {
            return $url;
        }

        if (! $width || ! $height) {
            return $url;
        }

        $thumbStr = 'imageView2/' . $mode . '/w/' . $width . '/h/' . $height . '/';

        // 拼接输出格式
        if (! is_null($format)
            && in_array($format, $this->formatArr)
        ) {
            $thumbStr .= 'format/' . $format . '/';
        }

        // 拼接渐进显示
        if (! is_null($interlace)
            && in_array(intval($interlace), array(0, 1), true)
        ) {
            $thumbStr .= 'interlace/' . $interlace . '/';
        }

        // 拼接图片质量
        if (! is_null($quality)
            && intval($quality) >= 0
            && intval($quality) <= 100
        ) {
            $thumbStr .= 'q/' . $quality . '/';
        }

        $thumbStr .= 'ignore-error/' . $ignoreError . '/';

        // 如果有query_string用|线分割实现多参数
        return $url . ($this->hasQuery($url) ? '|' : '?') . $thumbStr;
    }

    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    public function waterImg(
        $url,
        $image,
        $dissolve = 100,
        $gravity = 'SouthEast',
        $dx = null,
        $dy = null,
        $watermarkScale = null
    ) {
        // url合法效验
        if (! $this->isUrl($url)) {
            return $url;
        }

        $waterStr = 'watermark/1/image/' . \Qiniu\base64_urlSafeEncode($image) . '/';

        // 拼接水印透明度
        if (is_numeric($dissolve)
            && $dissolve <= 100
        ) {
            $waterStr .= 'dissolve/' . $dissolve . '/';
        }

        // 拼接水印位置
        if (in_array($gravity, $this->gravityArr, true)) {
            $waterStr .= 'gravity/' . $gravity . '/';
        }

        // 拼接横轴边距
        if (! is_null($dx)
            && is_numeric($dx)
        ) {
            $waterStr .= 'dx/' . $dx . '/';
        }

        // 拼接纵轴边距
        if (! is_null($dy)
            && is_numeric($dy)
        ) {
            $waterStr .= 'dy/' . $dy . '/';
        }

        // 拼接自适应原图的短边比例
        if (! is_null($watermarkScale)
            && is_numeric($watermarkScale)
            && $watermarkScale > 0
            && $watermarkScale < 1
        ) {
            $waterStr .= 'ws/' . $watermarkScale . '/';
        }

        // 如果有query_string用|线分割实现多参数
        return $url . ($this->hasQuery($url) ? '|' : '?') . $waterStr;
    }

    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    public function waterText(
        $url,
        $text,
        $font = '黑体',
        $fontSize = 0,
        $fontColor = null,
        $dissolve = 100,
        $gravity = 'SouthEast',
        $dx = null,
        $dy = null
    ) {
        // url合法效验
        if (! $this->isUrl($url)) {
            return $url;
        }

        $waterStr = 'watermark/2/text/'
            . \Qiniu\base64_urlSafeEncode($text) . '/font/'
            . \Qiniu\base64_urlSafeEncode($font) . '/';

        // 拼接文字大小
        if (is_int($fontSize)) {
            $waterStr .= 'fontsize/' . $fontSize . '/';
        }

        // 拼接文字颜色
        if (! is_null($fontColor)
            && $fontColor
        ) {
            $waterStr .= 'fill/' . \Qiniu\base64_urlSafeEncode($fontColor) . '/';
        }

        // 拼接水印透明度
        if (is_numeric($dissolve)
            && $dissolve <= 100
        ) {
            $waterStr .= 'dissolve/' . $dissolve . '/';
        }

        // 拼接水印位置
        if (in_array($gravity, $this->gravityArr, true)) {
            $waterStr .= 'gravity/' . $gravity . '/';
        }

        // 拼接横轴边距
        if (! is_null($dx)
            && is_numeric($dx)
        ) {
            $waterStr .= 'dx/' . $dx . '/';
        }

        // 拼接纵轴边距
        if (! is_null($dy)
            && is_numeric($dy)
        ) {
            $waterStr .= 'dy/' . $dy . '/';
        }

        // 如果有query_string用|线分割实现多参数
        return $url . ($this->hasQuery($url) ? '|' : '?') . $waterStr;
    }

    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    protected function isUrl($url)
    {
        $urlArr = parse_url($url);

        return $urlArr['scheme']
            && in_array($urlArr['scheme'], array('http', 'https'))
            && $urlArr['host']
            && $urlArr['path'];
    }

    /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    protected function hasQuery($url)
    {
        $urlArr = parse_url($url);

        return ! empty($urlArr['query']);
    }
}
