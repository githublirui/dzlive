<?php
/*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
include_once DISCUZ_ROOT.'./source/plugin/zhanmishu_video/conf/conf.php';

class Zms_video_Autoloader{
  
  /*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    public static function autoload($class) {
        $name = $class;
        if(false !== strpos($name,'\\')){
          $name = strstr($class, '\\', true);
        }
        if ($name == 'soapclient2' && !class_exists('SoapClient',false)) {
          exit('your server is not support soapclient');
          return ;
        }
        $filename = DISCUZ_ROOT.'source/plugin/zhanmishu_video/source/class/'.$name.'.php';
        if(is_file($filename)) {
            include $filename;
            return;
        }
        $filename = DISCUZ_ROOT.'source/plugin/zhanmishu_video/source/sdk/Qiniu/'.$name.'.php';
        if(is_file($filename)) {
            include $filename;
            return;
        }
    }
}

if (version_compare(phpversion(),'5.3.0','>=')) {
    spl_autoload_register('Zms_video_Autoloader::autoload',false,true);
}else{
    Zms_video_Autoloader::autoload("zhanmishu_course");
    Zms_video_Autoloader::autoload("zhanmishu_video");
    Zms_video_Autoloader::autoload("zhanmishu_api");
    Zms_video_Autoloader::autoload("Qiniu_Auth");
    Zms_video_Autoloader::autoload("Qiniu_Config");
    // Zms_video_Autoloader::autoload("Qiniu_Etag");
    // Zms_video_Autoloader::autoload("Qiniu_Zone");
    Zms_video_Autoloader::autoload("Qiniu_common_function");
    if (class_exists('SoapClient',false)) {
      Zms_video_Autoloader::autoload("soapclient2");
    }
}




?>