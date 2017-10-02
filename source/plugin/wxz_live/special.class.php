<?php
/**
 *	[微小智千聊直播(wxz_live.{modulename})] (C)2017-2099 Powered by wxz-合肥微小智.
 *	Version: 1.0.0
 *	Date: 2017-10-2 18:56
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_special {
	//TODO - Insert your code here

}

class threadplugin_wxz_live {

	public $name = 'XX主题';			//主题类型名称
	public $iconfile = 'icon.gif';	//发布主题链接中的前缀图标
	public $buttontext = '发布xx主题';	//发帖时按钮文字

	/**
	 * 发主题时页面新增的表单项目
	 * @param Integer $fid: 版块ID
	 * @return string 通过 return 返回即可输出到发帖页面中 
	 */
	public function newthread($fid) {
		//TODO - Insert your code here
		
		return 'TODO:newthread';
	}

	/**
	 * 主题发布前的数据判断 
	 * @param Integer $fid: 版块ID
	 */
	public function newthread_submit($fid) {
		//TODO - Insert your code here
		
	}

	/**
	 * 主题发布后的数据处理 
	 * @param Integer $fid: 版块ID
	 * @param Integer $tid: 当前帖子ID
	 */
	public function newthread_submit_end($fid, $tid) {
		//TODO - Insert your code here
		
	}

	/**
	 * 编辑主题时页面新增的表单项目
	 * @param Integer $fid: 版块ID
	 * @param Integer $tid: 当前帖子ID
	 * @return string 通过 return 返回即可输出到编辑主题页面中 
	 */
	public function editpost($fid, $tid) {
		//TODO - Insert your code here
		
		return 'TODO:editpost';
	}

	/**
	 * 主题编辑前的数据判断 
	 * @param Integer $fid: 版块ID
	 * @param Integer $tid: 当前帖子ID
	 */
	public function editpost_submit($fid, $tid) {
		//TODO - Insert your code here
		
	}

	/**
	 * 主题编辑后的数据处理 
	 * @param Integer $fid: 版块ID
	 * @param Integer $tid: 当前帖子ID
	 */
	public function editpost_submit_end($fid, $tid) {
		//TODO - Insert your code here
		
	}

	/**
	 * 回帖后的数据处理 
	 * @param Integer $fid: 版块ID
	 * @param Integer $tid: 当前帖子ID
	 */
	public function newreply_submit_end($fid, $tid) {
		//TODO - Insert your code here
		
	}

	/**
	 * 查看主题时页面新增的内容
	 * @param Integer $tid: 当前帖子ID
	 * @return string 通过 return 返回即可输出到主题首贴页面中
	 */
	public function viewthread($tid) {
		//TODO - Insert your code here
		
		return 'TODO:viewthread';
	}
}

?>