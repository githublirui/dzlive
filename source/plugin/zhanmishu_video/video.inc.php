<?php
/**
 *      [ruisiman!] (C)2014-2017 www.riscman.com.
 *      ��˼����-��˼���� ȫ���׷� http://www.riscman.com
 *
 *      Author: ruisiman.VIP $
 *    	qq:987515692 $
 */	

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT.'./source/plugin/zhanmishu_video/source/Autoloader.php';
include_once DISCUZ_ROOT.'./source/plugin/zhanmishu_video/source/function/common_function.php';




$mod = $_GET['mod'] ?  $_GET['mod'] : 'index';

if (in_array($mod, array('list','video','drm','order','buy','member','index','pay'))) {
	include_once DISCUZ_ROOT.'./source/plugin/zhanmishu_video/source/module/'.$mod.'.php';
}


 
?>