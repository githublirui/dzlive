<?php
/*
 *瑞思科人www.riscman.com
 *备用域名www.riscman.com
 *更多精品资源请访问瑞思科人官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_zhanmishu_video_autho extends discuz_table {

	public function __construct() {
		$this->_table = 'zhanmishu_video_autho';
		$this->_pk = 'aid';

		parent::__construct();
	}
	public function get_one_autho_byfield($field){
		if (!empty($field)) {
			$where = ' where ';
		}else{
			$where = '';
		}

		$i = 1;
		foreach ($field as $key => $value) {
			if ($i == count($field)) {
				$where = $where.' '.$key.' = \''.$value.'\' ';
			}else{
				$where = $where.' '.$key.' = \''.$value.'\' and ';
			}

			++$i;
		}
		return DB::fetch_first('SELECT * FROM '.DB::table($this->_table).$where.($sort ? ' autho BY '.DB::autho($this->_pk, $sort) : '').DB::limit($start, $limit), null, $this->_pk ? $this->_pk : '');
	}


	public function get_authos_num($field){
		if (!empty($field)) {
			$where = ' where ';
		}else{
			$where = '';
		}

		$i = 1;
		foreach ($field as $key => $value) {
			if ($i == count($field)) {
				$where = $where.' '.$key.' = \''.$value.'\' ';
			}else{
				$where = $where.' '.$key.' = \''.$value.'\' and ';
			}

			++$i;
		}
		$count = DB::fetch_first('SELECT count(*) as num FROM '.DB::table($this->_table).$where);
		return $count['num'];
	}


	public function get_type_autho($start = 0, $limit = 0, $sort = '',$type = '',$field) {
		if($sort) {
			$this->checkpk();
		}

		if (!empty($field)) {
			$where = ' where ';
		}else{
			$where = '';
		}

		$i = 1;
		foreach ($field as $key => $value) {
			if ($i == count($field)) {
				$where = $where.' '.$key.' = \''.$value.'\' ';
			}else{
				$where = $where.' '.$key.' = \''.$value.'\' and ';
			}

			++$i;
		}
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).$where.($sort ? ' autho BY '.DB::autho($this->_pk, $sort) : '').DB::limit($start, $limit), null, $this->_pk ? $this->_pk : '');
	}

	public function get_autho_num_byuidcid($cid,$uid,$device,$isautho='1'){
		if (!$cid || !$uid || !$device) {
			return false;
		}

		return DB::fetch_all('SELECT count(device) as num from %t where uid = %d and cid = %d and isautho=%d group by device',array($this->_table,$uid,$cid,$isautho));
	}

}

?>