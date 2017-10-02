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

class table_zhanmishu_video_order extends discuz_table {

	public function __construct() {
		$this->_table = 'zhanmishu_video_order';
		$this->_pk = 'oid';

		parent::__construct();
	}
	public function get_one_order_byfield($field){
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


	public function get_orders_num($field){
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

	public function update_ordertype_bycid($cid,$order_type){
		if (!$cid) {
			return false;
		}

		DB::update($this->_table,array('order_type'=>$order_type),'cid='.$cid);
	}


	public function get_order_by_out_trade_no($out_trade_no){
		if (!$out_trade_no) {
			return false;
		}

		return DB::fetch_first('select * from %t where out_trade_no = %s',array($this->_table,$out_trade_no));
	}

	public function get_type_order($start = 0, $limit = 0, $sort = '',$type = '',$field) {
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
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).$where.($sort ? ' ORDER BY '.DB::order($this->_pk, $sort) : '').DB::limit($start, $limit), null, $this->_pk ? $this->_pk : '');
	}

}

?>