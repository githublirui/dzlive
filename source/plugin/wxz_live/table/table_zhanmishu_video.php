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

class table_wxz_live extends discuz_table {

	public function __construct() {
		$this->_table = 'wxz_live';
		$this->_pk = 'vid';

		parent::__construct();
	}


	public function get_one_video_byfield($field){
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
		return DB::fetch_first('SELECT * FROM '.DB::table($this->_table).$where.($sort ? ' ORDER BY '.DB::order($this->_pk, $sort) : '').DB::limit($start, $limit), null, $this->_pk ? $this->_pk : '');
	}

	public function get_type_video_num($field=array()){
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

	public function get_type_video($start, $limit, $sort = '',$type = '',$field) {
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


		return  DB::fetch_all('SELECT * FROM '.DB::table($this->_table).$where.($sort ? ' ORDER BY '.DB::order($this->_pk, $sort) : '').DB::limit($start, $limit), null, $this->_pk ? $this->_pk : '');

	}

}

?>