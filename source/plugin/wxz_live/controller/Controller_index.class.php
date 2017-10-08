<?php

/**
 * 
 * 前台页面
 */
class Controller_index extends Controller_base {

    /**
     * 直播首页 
     */
    public function index() {
        include template('wxz_live:index/index');
    }

    /**
     *  直播分类页
     */
    public function category() {
        global $_G;
        include template('wxz_live:index/category');
    }

    /**
     * 直播详情页面
     */
    public function live() {
        include template('wxz_live:index/live');
    }

}

?>
