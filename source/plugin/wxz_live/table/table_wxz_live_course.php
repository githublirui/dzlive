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

class table_wxz_live_course extends discuz_table {

	public function __construct() {
		$this->_table = 'wxz_live_course';
		$this->_pk = 'cid';

		parent::__construct();
	}
	public function get_one_course_byfield($field){
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
	public function get_type_course_num($field=array()){

		if (!empty($field)) {
			$where = ' where ';
		}else{
			$where = '';
		}
		if ($field['cat_id']) {
			$cat = C::t("#wxz_live#wxz_live_cat")->fetch($field['cat_id']);
			if ($cat['parent_id'] == '0') {
				$cats = C::t("#wxz_live#wxz_live_cat")->get_type_video_cat(0, 0, '','',array('parent_id'=>$field['cat_id']));
			
				if (!empty($cats)) {
					$where .= ' cat_id in ('.implode(',',array_keys($cats)).') ';
					unset($field['cat_id']);
					if (!empty($field)) {
						$where .=' and ';
					}
				}
			}
		}

		$tmp = array();
		foreach ($field as $key => $value) {
			if (is_array($value)) {
				if ($value['type'] =='like') {
					$tmp[] = $key.' like \'%'.$value['value'].'%\' ';
				}
			}else{
				$tmp[] = $key.' = \''.$value.'\' ';
			}
				
		}

		$where = $where.implode(' and ', $tmp);
		$count = DB::fetch_first('SELECT count(*) as num FROM '.DB::table($this->_table).$where);
		return $count['num'];
			
	}
	public function get_type_course($start = 0, $limit = 0, $sort = '',$type = '',$field) {
		if (is_array($sort) && !empty($sort)) {
			$order = ' ORDER BY ';
			$tmp=array();
			foreach ($sort as $key => $value) {
				$tmp[] = DB::order($key, $value);
			}
			$order .= implode(',', $tmp);
			
		}else if($sort) {
			$this->checkpk();
			$order = $sort ? ' ORDER BY '.DB::order($this->_pk, $sort) : '';
		}


		if (!empty($field)) {
			$where = ' where ';
		}else{
			$where = '';
		}
		if ($field['cat_id']) {
			$cat = C::t("#wxz_live#wxz_live_cat")->fetch($field['cat_id']);
			if ($cat['parent_id'] == '0') {
				$cats = C::t("#wxz_live#wxz_live_cat")->get_type_video_cat(0, 0, '','',array('parent_id'=>$field['cat_id']));
			
				if (!empty($cats)) {
					$where .= ' cat_id in ('.implode(',',array_keys($cats)).') ';
					unset($field['cat_id']);
					if (!empty($field)) {
						$where .=' and ';
					}
				}
			}
		}

		$tmp = array();
		foreach ($field as $key => $value) {
			
			if (is_array($value)) {
				if ($value['type'] =='like') {
					$tmp[] = $key.' like \'%'.$value['value'].'%\' ';
				}
			}else{
				$tmp[] = $key.' = \''.$value.'\' ';
			}
				
		}

		$where = $where.implode(' and ', $tmp);
 
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).$where.$order.DB::limit($start, $limit), null, $this->_pk ? $this->_pk : '');
	}

}


?>