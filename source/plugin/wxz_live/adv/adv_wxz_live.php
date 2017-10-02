<?php
/**
 *	[微小智千聊直播(wxz_live.adv_wxz_live)] (C)2017-2099 Powered by wxz-合肥微小智.
 *	Version: 1.0.0
 *	Date: 2017-10-2 18:56
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class adv_wxz_live {

	public $version = '1.0.0';	//脚本版本号
	public $name = 'wxz_live';				//广告类型名称 (可填写语言包项目)
	public $description = '';		//广告类型说明 (可填写语言包项目)
	public $copyright = '<a href="http://www.comsenz.com" target="_blank">Comsenz Inc.</a>';	//版权 (可填写语言包项目)
	public $targets = array('portal', 'home', 'member', 'forum', 'group', 'userapp', 'plugin', 'custom');	//广告类型适用的投放范围
	public $imagesizes = array();	//广告规格例：array('468x60', '658x60', '728x90', '760x90', '950x90')

	/**
	 * 返回设置项目
	 */
	public function getsetting() {
		//TODO - Insert your code here
	}

	/**
	 * 保存设置项目
	 */
	public function setsetting(&$advnew, &$parameters) {
		//TODO - Insert your code here
	}

	/**
	 * 广告显示时的运行代码
	 */
	public function evalcode() {
		//TODO - Insert your code here
	}

}
?>