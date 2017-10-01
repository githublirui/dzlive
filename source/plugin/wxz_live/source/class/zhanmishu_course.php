<?php
/*
 *合肥微小智www.hfwxz.com
 *备用域名www.hfwxz.com
 *更多精品资源请访问合肥微小智官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class zhanmishu_course{
	
	public $config=array();

	public $cachefile = 'wxz_live';
	
	public function get_cache_file(){
		return DISCUZ_ROOT.'./data/sysdata/cache_'.$this->cachefile.'.php';
	}

	public function __construct($config=array(),$code='',$verify='',$cookiehead = '')
	{
		if (empty($config)) {
			if (!function_exists('zms_video_getconfig')) {
				include DISCUZ_ROOT.'./source/plugin/wxz_live/source/function/common_function.php';
			}
			$config = zms_video_getconfig();
		}
		$this->config = $config;

	}
	public function ZmsIsWepayExists(){
		return is_dir(DISCUZ_ROOT.'./source/plugin/zhanmishu_wepay');

	}

	public function get_one_course_byfield($field){
		return C::t("#wxz_live#wxz_live_course")->get_one_course_byfield($field);
	}
	public function auto_to_utf8($data){
		if (is_string($data)) {
			return diconv($data,CHARSET,'UTF-8');
		}else if (is_array($data)) {
			$tmp = array();
			foreach ($data as $key => $value) {
				$nkey = diconv($key,CHARSET,'UTF-8');
				$nvalue = diconv($value,CHARSET,'UTF-8');
				$tmp[$nkey] = $nvalue;
			}
			return $tmp;
		}

		return false;
	}

	public function get_type_course_num($field=array()){
		return C::t("#wxz_live#wxz_live_course")->get_type_course_num($field);
	}
	public function add_courseselltimes($cid,$num='1'){
		$course = $this->get_course_bycid($cid);
		return C::t("#wxz_live#wxz_live_course")->update($cid,array('selltimes'=>$course['selltimes'] + $num));
	}

	public function get_rand_trade_no($cid,$hid,$oid){
		return 'k'.$cid.'h'.$hid.'o'.$oid.'t'.TIMESTAMP.substr(str_shuffle('abcdefghijklmnopqrstuvwxyz1234567890'), 0, 6);
	}

	public function get_type_course_fmt($start = 0, $limit = 0, $sort = '',$type = '',$field){
		global $url,$wxz_liveconf;
		$url = ADMINSCRIPT.'?action='.$url;
		$courses = $this->get_type_course($start, $limit, $sort,$type,$field);
		$courses_fmt = array();
		
		foreach ($courses as $key => $value) {
			$courses_fmt[$key]['cid'] = $value['cid'];
			$courses_fmt[$key]['username'] = $this->get_usernamebyuid($value['uid']);
			$courses_fmt[$key]['course_name'] = $value['course_name'];
			$courses_fmt[$key]['issell'] = $value['issell'] ? lang('plugin/wxz_live', 'haveonsellk') : lang('plugin/wxz_live', 'haveoutsellk');
			$courses_fmt[$key]['selltimes'] = $value['selltimes'];
			$courses_fmt[$key]['course_price'] = intval($value['course_price']) / 100;
			$courses_fmt[$key]['diff'] = $wxz_liveconf['diff'][$value['diff']];
			$courses_fmt[$key]['progress'] = $wxz_liveconf['progress'][$value['progress']];
			$courses_fmt[$key]['course_img'] = '<a href="'.$value['course_img'].'" target="_blank"><img src="'.$value['course_img'].'" width="40px" height="40px"></a>';
			$courses_fmt[$key]['course_intro'] = $value['course_intro'];
			$courses_fmt[$key]['dateline'] = date('Y-m-d H:i:s',$value['dateline']);
			$sellact = $value['issell'] ? lang('plugin/wxz_live', 'outsellk') : lang('plugin/wxz_live', 'onsellk');
			$courses_fmt[$key]['act'] = '<a href="'.$url.'&act=adminvideo&m=add&cid='.$value['cid'].'">'.lang('plugin/wxz_live', 'add_video').'</a>&nbsp;&nbsp;&nbsp;<a href="'.$url.'&act=adminvideo&m=admin&cid='.$value['cid'].'">'.lang('plugin/wxz_live', 'admin_course').'</a>&nbsp;&nbsp;&nbsp;<a href="'.$url.'&act=editk&editk=yes&cid='.$value['cid'].'&formhash='.FORMHASH.'">'.lang('plugin/wxz_live', 'edit').'</a>&nbsp;&nbsp;&nbsp;<a href="'.$url.'&act=outsellk&outsellk=yes&cid='.$value['cid'].'&formhash='.FORMHASH.'">'.$sellact.'</a>&nbsp;&nbsp;&nbsp;<a href="'.$url.'&act=delk&delk=yes&cid='.$value['cid'].'&formhash='.FORMHASH.'">'.lang('plugin/wxz_live', 'delete').'</a>';
		}

		return $courses_fmt;
	}



	public function get_usernamebyuid($uid){
		if (!$uid) {
			return '';
		}
		$user = getuserbyuid($uid);
		return $user['username'];
	}

	public function get_course_bycid($cid,$isaddview=false,$isupdateorder=false){
		if (!$cid) {
			return false;
		}
		$c = C::t("#wxz_live#wxz_live_course")->fetch($cid);

		$up = array();
		if ($isaddview) {
			$up['views'] = $c['views'] + 1;
		}
		if ($isupdateorder) {
			$num = C::t("#wxz_live#wxz_live_order")->get_orders_num(array('cid'=>$c['cid'],'ispayed'=>'1'));
			$up['learns'] = $num;
		}
		if (!empty($up)) {
			C::t("#wxz_live#wxz_live_course")->update($cid,$up);	
		}

		return $c;
	}


	public function  delete_k($cid){
		if (!$cid) {
			# code...
			return false;
		}

		return C::t("#wxz_live#wxz_live_course")->update($cid,array('isdel'=>'1'));
	}
	public function  set_course_upatesale($cid){
		if (!$cid) {
			# code...
			return false;
		}
		$k = $this->get_course_bycid($cid);
		if (empty($k)) {
			return false;
		}
		$issell = $k['issell'] ? '0' : '1';

		return C::t("#wxz_live#wxz_live_course")->update($cid,array('issell'=>$issell));
	}

	public function get_type_course($start = 0, $limit = 0, $sort = '',$type = '',$field){
		return C::t("#wxz_live#wxz_live_course")->get_type_course($start, $limit, $sort,$type,$field);
	}

	public function get_recommend_course($cid){
		return $this->get_type_course(0, 3, '','',array('isdel'=>'0'));
	}

	public function get_cat_tree($pid='0',$issel='0',$isfromcache=true){


		if ($isfromcache) {
			if (is_array($tree = $this->get_cat_tree_formcache())) {
				if ($pid == '0') {
					return $tree;
				}
				foreach ($tree as $key => $value) {
					if ($value['cat_id'] == $pid) {
						$return =  $value;
						break;
					}
				}
				return $return;
			}			
		}

		$field = array();
		if (strlen($pid) > 0) {
			$field['parent_id'] = intval($pid);
		}
		if (strlen($isdel) > 0) {
			$field['isdel'] = $issel;
		}

		$cat = C::t("#wxz_live#wxz_live_cat")->get_type_video_cat('','','sort','',$field);
		if (empty($cat)) {
			return false;
		}

		if (!empty($cat)) {
			foreach ($cat as $key => $value) {
				if ($value['cat_id']) {
					$cat[$key]['son'] = $this->get_cat_tree($value['cat_id'],'0',false);
				}
				
			}
			$cat[$key]['son'] = $this->catetree_sort($cat[$key]['son']);
		}
		$cat = $this->catetree_sort($cat);

		return $cat;
	}

	public function update_catecache(){

		$catetreevar = $this->get_cat_tree('0','0',false,false);

		$this->ZmswriteToCache('cat',$catetreevar);
		// require_once libfile('function/cache');
		// $datacache = "\$catetreevar=".arrayeval($catetreevar).";\n";
		// writetocache('wxz_livecate', $datacache);
	}

	public function get_cat_tree_formcache(){
		return $this->ZmsGetFromCache('cat');
	}

	public function ZmsGetFromCache($key){
		if(file_exists($cachefile = $this->get_cache_file())) {
			@include $cachefile;
			
			return $wxz_live_cache[$key];
		}
		return false;		
	}


	public function ZmswriteToCache($key,$data){
		if(file_exists($cachefile = $this->get_cache_file())) {
			@include $cachefile;
		}
		$wxz_live_cache[$key] = $data;

		require_once libfile('function/cache');
		$wxz_live_cache_str = "\$wxz_live_cache=".arrayeval($wxz_live_cache).";\n";
		writetocache('wxz_live', $wxz_live_cache_str);

	}

	public 	function catetree_sort($arrays,$sort_key='sort',$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){ 
		if(is_array($arrays)){ 
			foreach ($arrays as $array){ 
				if(is_array($array)){ 
					$key_arrays[] = $array[$sort_key]; 
				}else{ 
					return false; 
				} 
			} 
		}else{ 
			return false; 
		}
		array_multisort($key_arrays,$sort_order,$sort_type,$arrays); 
		return $arrays; 
	}

	public function get_cat_select(){
		$cat = $this->get_cat_tree('0','0',false);
		$return = array();
		foreach ($cat as $key => $value) {
			$return[] = array($value['cat_id'],$value['cat_name']);
			if (is_array($value['son']) && !empty($value['son'])) {
				foreach ($value['son'] as $k => $v) {
					$return[] = array($v['cat_id'],'&nbsp;&nbsp;&nbsp;&nbsp;--'.$v['cat_name']);
				}
			}
		}

		return $return;
	}

	public function get_pidbycat_id($cat_id){
		if (!$cat_id) {
			return '0';
		}

		$cat = C::t("#wxz_live#wxz_live_cat")->fetch($cat_id);
		if (empty($cat)) {
			return '0';
		}
		return isset($cat['parent_id']) ? $cat['parent_id'] : $cat_id;
	}

	public function check_liveorvideo($str){
		if (ereg("^http(s?)://[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*$", $str)){    
			return 'url';    
		}else if (strlen($str)  >= 1) {
			return 'str';
		}
		return false;
	}
	public function checklowerlimit($action, $uid = 0, $coef = 1, $fid = 0, $returnonly = 0){
		global $_G;
		$uid = $uid ? $uid : $_G['uid'];
		$config = &$this->config;
		$action = array('extcredits'.$config['paytype_extcredits']=>$action);
		return checklowerlimit($action, $uid, '1', '0', '1');
	}
	public function updatemembercount($uid,$num,$title='',$intro=''){
		global $_G;
		$uid = $uid ? $uid : $_G['uid'];
		if (strlen($num) < 1 || !$uid) {
			return false;
		}
		$config = &$this->config;
		updatemembercount($uid,array('extcredits'.$config['paytype_extcredits']=>$num),false,'','','',$title,$intro);
		return true;
	}

	public function course_group_toarray($course_group){
		return array_filter(explode('#', trim($course_group)));
	}

	public function get_group_icons(){
		return DB::fetch_all('select groupid,icon,grouptitle,color from %t',array('common_usergroup'),'groupid');
	}
}

?>