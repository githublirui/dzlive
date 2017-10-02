<?php
/**
 *	[微小智千聊直播(wxz_live.magic_wxz_live)] (C)2017-2099 Powered by wxz-合肥微小智.
 *	Version: 1.0.0
 *	Date: 2017-10-2 18:56
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class magic_wxz_live {
	public $version = '1.0.0';	//脚本版本号
	public $name = 'wxz_live';				//道具名称 (可填写语言包项目)
	public $description = '';		//道具说明 (可填写语言包项目)
	public $price = '20';	//道具默认价格
	public $weight = '20';	//道具默认重量
	public $useevent = 0;
	public $targetgroupperm = false;
	public $copyright = '<a href="http://www.comsenz.com" target="_blank">Comsenz Inc.</a>';	//版权 (可填写语言包项目)
	public $magic = array();
	public $parameters = array();

	/**
	 * 返回设置项目
	 */
	public function getsetting(&$magic) {
		//TODO - Insert your code here
	}

	/**
	 * 保存设置项目
	 */
	public function setsetting(&$magicnew, &$parameters) {
		//TODO - Insert your code here
	}

	/**
	 * 道具使用
	 */
	public function usesubmit() {
		//TODO - Insert your code here
	}

	/**
	 * 道具显示
	 */
	public function show() {
		//TODO - Insert your code here
	}
}
?>