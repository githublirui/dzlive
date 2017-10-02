<?php

/**
 *  (C)2013
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_test {

	/*
	 * 贴内用户信息标记，返回值为标记显示内容
	 * "界面 ? 界面设置 ? 帖内用户信息" 用户信息模板中的标记
	 * 文档: http://open.discuz.net/?ac=document&page=plugin_hook (搜索"profile_node")
	 */
	function profile_node($post, $start, $end) {
		return $start.'我是插件'.$end;
	}

}

?>