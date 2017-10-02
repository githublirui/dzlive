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
 * 存放在插件目录的验证问答类
 * 文档: http://open.discuz.net/?ac=document&page=plugin_classes#.E9.AA.8C.E8.AF.81.E9.97.AE.E7.AD.94.E7.B1.BB
 */
class secqaa_plugin_calc {

	var $version = '1.0';
	var $name = 'calc_name';
	var $description = 'calc_desc';
	var $copyright = '<a href="http://www.comsenz.com" target="_blank">Comsenz Inc.</a>';
	var $customname = '';

	function make(&$question) {
		$a = rand(1, 90);
		$b = rand(1, 10);
		if(rand(0, 1)) {
			$question = $a.' + '.$b.' = ?';
			$answer = $a + $b;
		} else {
			$question = $a.' - '.$b.' = ?';
			$answer = $a - $b;
		}
		return $answer;
	}

}

?>
