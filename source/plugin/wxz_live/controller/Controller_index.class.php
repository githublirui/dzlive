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
        global $_G;
        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/lib/wxz_weixin.class.php";
        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/table/table_wxz_live_base.php";

        $_G['wechat']['setting'] = unserialize($_G['setting']['mobilewechat']);
     
        $wxzWeixin = new wxz_weixin($_G['wechat']['setting']['wechat_appId'], $_G['wechat']['setting']['wechat_appsecret']);
        
        $jssdkConfig = $wxzWeixin->getJssdkConfig();
   
                
        //获取首页banner
        $tableObj = new table_wxz_live_base(array('table' => 'wxz_live_banner', 'pk' => 'id'));
        $condition = "is_show=1";
        $banners = $tableObj->getAll($condition, '*', 'sort_order desc');

        //首页配置
        $types = array(1, 2);
        $indexSettings = C::t('#wxz_live#wxz_live_setting')->getByType($types);
        $style = $indexSettings[1]['link'];

        //获取所有分类
        $categorys = C::t('#wxz_live#wxz_live_category')->getShowCategorys();

        include template("wxz_live:index/{$style}/index");
    }

    /**
     * ajax 获取列表
     */
    public function ajaxGetlive() {
        global $_G;
        $page = intval($_GET['page']);
        $cid = intval($_GET['cid']);

        $isweixin = 1;
        $pindex = max(0, intval($_GET['page']));
        $psize = 5;
        $start = ($pindex) * $psize;
        $condition = "is_show=1";

        if ($cid) {
            $condition .= " AND category_id={$cid}";
        }

        $list = C::t('#wxz_live#wxz_live_room')->getAll($condition, '*', 'sort_order desc', "{$start},{$psize}");
        $tmp = array();

        foreach ($list as $key => $value) {
            $tmp[$key . '"'] = $value;
            if ($value['start_time'] != '0000-00-00 00:00:00') {
                $tmp[$key . '"']['start_time'] = strtotime($value['start_time']);
            } else {
                $tmp[$key . '"']['start_time'] = strtotime($value['create_at']);
            }

            $tmp[$key . '"']['end_time'] = strtotime($value['end_time']);
            $tmp[$key . '"']['linkurl'] = "{$_G['siteurl']}plugin.php?id=wxz_live:index&pmod=index&act=live&roomno={$value['room_no']}";
        }
        $list = $tmp;
        $result = array('s' => '1', 'msg' => $list, 'isweixin' => $isweixin);
        echo json_encode($result);
        exit;
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
