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
     * 功能列表页面
     */
    public function index() {
        global $_G;
        include template('wxz_live:common/pages');
    }

}

?>
