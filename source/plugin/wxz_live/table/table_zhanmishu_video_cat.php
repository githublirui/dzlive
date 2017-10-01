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

class table_zhanmishu_video_cat extends discuz_table {

	public function __construct() {
		$this->_table = 'zhanmishu_video_cat';
		$this->_pk = 'cat_id';

		parent::__construct();
	}


	public function get_one_video_cat_byfield($field){
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

	public function get_type_video_cat_num($field=array()){
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

	public function get_type_video_cat($start, $limit, $sort = '',$type = '',$field) {
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


		return  DB::fetch_all('SELECT * FROM '.DB::table($this->_table).$where.($sort ? ' ORDER BY '.DB::order($this->_pk, $sort) : '').DB::limit($start, $limit), null, $this->_pk ? $this->_pk : '',array(),'cat_id');

	}

	public function get_cat_by_level($level='0',$sort=array('cat_touchorder'=>'asc')){

		if (is_array($sort) && !empty($sort)) {
			foreach ($sort as $key => $value) {
				$order = ' ORDER BY '.DB::order($key, $value);
				break;
			}
			
		}else if(is_string($sort) && $sort) {
			$this->checkpk();
			$order = $sort ? ' ORDER BY '.DB::order($this->_pk, $sort) : '';
		}
		if (strlen($level) > 0) {
			$where = ' where level = '.$level.' ';
		}
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).$where.$order);

	}

}

?>