<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: secqaa_calc.php 10395 2010-05-11 04:48:31Z monkey $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/*
 * 存放在插件目录的验证码类
 * 文档: http://open.discuz.net/?ac=document&page=plugin_classes#.E9.AA.8C.E8.AF.81.E7.A0.81.E7.B1.BB.28Discuz.21_X2.5_.E6.96.B0.E5.A2.9E.29
 */
session_start();

class seccode_plugin_captcha {

	var $version = '1.0';
	var $name = 'captcha_name';
	var $description = 'captcha_desc';
	var $copyright = '<a href="http://www.comsenz.com" target="_blank">Comsenz Inc.</a>';

	function check($value, $idhash) {
		return !empty($_SESSION['captcha']) && trim(strtolower($value)) == $_SESSION['captcha'];
	}

	function make($idhash) {
		echo '<div><img src="api/captcha/index.php" /></div>';
	}

}

?>
