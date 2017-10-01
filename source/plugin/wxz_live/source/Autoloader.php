<?php
/*
 *��˼����www.riscman.com
 *��������www.riscman.com
 *���ྫƷ��Դ�������˼���˹ٷ���վ��ѻ�ȡ
 *����Դ��Դ�������ռ�,��������ѧϰ����������������ҵ��;����������24Сʱ��ɾ��!
 *����ַ�������Ȩ��,�뼰ʱ��֪����,���Ǽ���ɾ��!
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
include_once DISCUZ_ROOT.'./source/plugin/zhanmishu_video/conf/conf.php';

class Zms_video_Autoloader{
  
  /*
 *��˼����www.riscman.com
 *��������www.riscman.com
 *���ྫƷ��Դ�������˼���˹ٷ���վ��ѻ�ȡ
 *����Դ��Դ�������ռ�,��������ѧϰ����������������ҵ��;����������24Сʱ��ɾ��!
 *����ַ�������Ȩ��,�뼰ʱ��֪����,���Ǽ���ɾ��!
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