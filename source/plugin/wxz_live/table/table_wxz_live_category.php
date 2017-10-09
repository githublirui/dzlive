<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT . "./source/plugin/wxz_live/table/table_wxz_live_base.php";

class table_wxz_live_category extends table_wxz_live_base {

    public function __construct() {
        $this->_table = 'wxz_live_category';
        $this->_pk = 'id';

        parent::__construct();
    }

    /**
     * 获取所有展示的分类
     */
    public function getShowCategorys() {
        $condition = "is_show=2";
        return $this->getAll($condition, '*', 'sort_order desc');
    }

}

?>