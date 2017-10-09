<?php

class Controller_common extends Controller_base {

    public function __construct() {
        parent::__construct();

        //子导航
        $this->navs = array(
            array(
                'name' => '功能导航',
                'act' => 'index',
            ),
        );
        $this->title = "常用功能";
    }

    /**
     * ajax 删除表数据
     */
    public function ajaxDelTable() {
        ob_end_clean();

        $table = $_GET['tableName'];
        $id = $_GET['id'];

        if (!$table || !$id) {
            $this->ajaxError('参数错误');
        }

        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/table/table_wxz_live_base.php";

        $tableObj = new table_wxz_live_base(array('table' => $table, 'pk' => 'id'));

        $ret = $tableObj->delById($id);
        if ($ret) {
            $this->ajaxSucceed();
        } else {
            $this->ajaxError('删除失败');
        }
    }

    /**
     * 功能列表页面
     */
    public function index() {
        global $_G;
        include template('wxz_live:common/pages');
    }

}

?>
