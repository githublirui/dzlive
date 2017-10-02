<?php
/**
 *	[微小智千聊直播(wxz_live.secqaa_qa)] (C)2017-2099 Powered by wxz-合肥微小智.
 *	Version: 1.0.0
 *	Date: 2017-10-2 18:56
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class secqaa_qa {

	public $version = '1.0.0';	//脚本版本号
	public $name = 'qa';	//验证问答名称 (可填写语言包项目)
	public $description = '';	//验证问答说明 (可填写语言包项目)
	public $copyright = '<a href="http://www.comsenz.com" target="_blank">Comsenz Inc.</a>';	//版权 (可填写语言包项目)
	public $customname = '';

	/**
	 * 返回安全问答的答案和问题 ($question 为问题，函数返回值为答案)
	 */
	public function make(&$question) {
		//TODO - Insert your code here
	}
}
?>