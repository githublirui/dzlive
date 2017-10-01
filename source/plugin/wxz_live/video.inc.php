<?php
/**
 *      [ruisiman!] (C)2014-2017 www.hfwxz.com.
 *      合肥微小智-合肥微小智 全网首发 http://www.hfwxz.com
 *
 *      Author: ruisiman.VIP $
 *    	qq:987515692 $
 */	

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT.'./source/plugin/wxz_live/source/Autoloader.php';
include_once DISCUZ_ROOT.'./source/plugin/wxz_live/source/function/common_function.php';




$mod = $_GET['mod'] ?  $_GET['mod'] : 'index';

if (in_array($mod, array('list','video','drm','order','buy','member','index','pay'))) {
	include_once DISCUZ_ROOT.'./source/plugin/wxz_live/source/module/'.$mod.'.php';
}


 
?>