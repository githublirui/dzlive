<?php
/*
 *合肥微小智www.hfwxz.com
 *备用域名www.hfwxz.com
 *更多精品资源请访问合肥微小智官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
include_once DISCUZ_ROOT.'./source/plugin/wxz_live/conf/conf.php';

class Wxz_live_Autoloader{
  
  /*
 *合肥微小智www.hfwxz.com
 *备用域名www.hfwxz.com
 *更多精品资源请访问合肥微小智官方网站免费获取
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
        $filename = DISCUZ_ROOT.'source/plugin/wxz_live/source/class/'.$name.'.php';
        if(is_file($filename)) {
            include $filename;
            return;
        }
        $filename = DISCUZ_ROOT.'source/plugin/wxz_live/source/sdk/Qiniu/'.$name.'.php';
        if(is_file($filename)) {
            include $filename;
            return;
        }
    }
}

if (version_compare(phpversion(),'5.3.0','>=')) {
    spl_autoload_register('Wxz_live_Autoloader::autoload',false,true);
}else{
    Wxz_live_Autoloader::autoload("wxz_course");
    Wxz_live_Autoloader::autoload("wxz_live");
    Wxz_live_Autoloader::autoload("wxz_api");
    Wxz_live_Autoloader::autoload("Qiniu_Auth");
    Wxz_live_Autoloader::autoload("Qiniu_Config");
    // Wxz_live_Autoloader::autoload("Qiniu_Etag");
    // Wxz_live_Autoloader::autoload("Qiniu_Zone");
    Wxz_live_Autoloader::autoload("Qiniu_common_function");
    if (class_exists('SoapClient',false)) {
      Wxz_live_Autoloader::autoload("soapclient2");
    }
}




?>