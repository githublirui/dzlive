<?php
/**
 *	[微小智千聊直播(wxz_live.task_wxz_live)] (C)2017-2099 Powered by wxz-合肥微小智.
 *	Version: 1.0.0
 *	Date: 2017-10-2 18:56
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class task_wxz_live {

	public $version = '1.0.0';	//脚本版本号
	public $name = 'wxz_live';	//任务名称 (可填写语言包项目)
	public $description = '';	//任务说明 (可填写语言包项目)
	public $copyright = '<a href="http://www.comsenz.com" target="_blank">Comsenz Inc.</a>';	//版权 (可填写语言包项目)
	public $icon = '';		//默认图标
	public $period = '';	//默认任务间隔周期
	public $periodtype = 0;//默认任务间隔周期单位
	public $conditions = array();	//任务附加条件

	/**
	 * 申请任务成功后的附加处理
	 */
	public function  preprocess($task) {
		//TODO - Insert your code here
	}

	/**
	 * 判断任务是否完成 (返回 TRUE:成功 FALSE:失败 0:任务进行中进度未知或尚未开始  大于0的正数:任务进行中返回任务进度)
	 */
	public function csc($task = array()) {
		//TODO - Insert your code here
	}

	/**
	 * 完成任务后的附加处理
	 */
	public function sufprocess($task) {
		//TODO - Insert your code here
	}

	/**
	 * 任务显示
	 */
	public function view() {
		//TODO - Insert your code here
	}

	/**
	 * 任务安装的附加处理
	 */
	public function install() {
		//TODO - Insert your code here
	}

	/**
	 * 任务卸载的附加处理
	 */
	public function uninstall() {
		//TODO - Insert your code here
	}

	/**
	 * 任务升级的附加处理
	 */
	public function upgrade() {
		//TODO - Insert your code here
	}
}
?>