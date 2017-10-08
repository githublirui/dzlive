<?php

class Controller_live extends Controller_base {

    public function __construct() {
        parent::__construct();

        //子导航
        $this->navs = array(
            array(
                'name' => '直播间列表',
                'act' => 'index',
            ),
            array(
                'name' => '添加直播间',
                'act' => 'add',
            ),
        );
        $this->title = "直播间管理";
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

        $ret = C::t("#wxz_live#{$table}")->delete([$id]);
        if ($ret) {
            $this->ajaxSucceed();
        } else {
            $this->ajaxError('删除失败');
        }
    }

    /**
     * 直播间列表页 
     */
    public function index() {
        include template('wxz_live:live/index');
    }

    /**
     * 直播间列表页 
     */
    public function add() {
        if (submitcheck('add')) {
            echo 13333;
            die;
        }
        include template('wxz_live:live/add');
    }

}

?>
