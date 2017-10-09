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
                'act' => 'liveSave',
            ),
        );
        $this->title = "直播间管理";
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
    public function liveSave() {
        global $_G;
        $id = $_GET['id'];

        if ($id) {
            $info = C::t('#wxz_live#wxz_live_room')->fetch($id);
        }
        
        //获取所有分类
        $categorys = C::t('#wxz_live#wxz_live_category')->getShowCategorys();
  
        if (submitcheck('save')) {
            if (!$_GET['name']) {
                cpmsg('分类名称不能为空', $this->noRootUrl . "&act=add&id={$id}", 'error');
            }

            $images = wxz_uploadimg();

            if ($id) {
                //更新直播分类
                $updateData = array(
                    'activity_id' => 1,
                    'name' => $_GET['name'],
                    'desc' => $_GET['desc'],
                    'is_show' => $_GET['is_show'],
                    'sort_order' => $_GET['sort_order'],
                );
                $updateData['icon'] = $images['icon'] ? $images['icon'] : $_GET['icon'];
                $ret = C::t('#wxz_live#wxz_live_category')->update([$id], $updateData);
                if ($ret) {
                    cpmsg('更新成功', $this->noRootUrl, 'success');
                }
            } else {
                $roomId = random(10);
                //添加直播分类
                $insertData = array(
                    'activity_id' => 1,
                    'uid' => $_G['uid'],
                    'name' => $_GET['name'],
                    'icon' => $_GET['icon'],
                    'desc' => $_GET['desc'],
                    'is_show' => $_GET['is_show'],
                    'sort_order' => $_GET['sort_order'],
                    'create_at' => date('Y-m-d H:i:s'),
                );
                $insertData['icon'] = $images['icon'] ? $images['icon'] : $_GET['icon'];
                $ret = C::t('#wxz_live#wxz_live_category')->insert($insertData);
                if ($ret) {
                    cpmsg('添加成功', $this->noRootUrl, 'success');
                }
            }
        }


        include template('wxz_live:live/liveSave');
    }

}

?>
