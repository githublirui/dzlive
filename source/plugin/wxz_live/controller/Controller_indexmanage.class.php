<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class Controller_indexmanage extends Controller_base {

    public function __construct() {
        parent::__construct();

        //子导航
        $this->navs = array(
            array(
                'name' => '基础设置',
                'act' => 'index',
            ),
            array(
                'name' => '轮播图列表',
                'act' => 'bannerList',
            ),
            array(
                'name' => '添加轮播图',
                'act' => 'bannerSave',
            ),
        );
        $this->title = "首页管理";
    }

    /**
     * 首页基础设置
     */
    public function index() {
        global $_G;

        $infos = C::t('#wxz_live#wxz_live_setting')->getByType(array(1, 2));

        if (submitcheck('save')) {
            $images = wxz_uploadimg();
            $data1 = array(
                'title' => $_GET['title'],
                'link' => $_GET['style'],
            );
            $data2 = array(
                'title' => $_GET['share_title'],
                'desc' => $_GET['share_desc'],
            );

            $data2['img'] = $images['share_img'] ? $images['share_img'] : $_GET['share_img'];

            $ret1 = C::t('#wxz_live#wxz_live_setting')->saveTypeData(1, $data1);
            $ret2 = C::t('#wxz_live#wxz_live_setting')->saveTypeData(2, $data2);
            cpmsg('保存成功', $this->noRootUrl, 'success');
        }
        include template('wxz_live:live/indexSetting');
    }

    /**
     * 轮播图列表
     */
    public function bannerList() {
        $page = (int) $_GET['page'];
        $page = $page <= 0 ? 1 : $page;
        $query['perpage'] = 10;
        $tableObj = new table_wxz_live_base(array('table' => 'wxz_live_banner', 'pk' => 'id'));

        if (submitcheck('ordersubmit')) {
            foreach ($_GET['ids'] as $k => $id) {
                $ret = $tableObj->updateById($id, array('sort_order' => $_GET['sort_orders'][$k]));
            }
        }

        $condition = "1=1";

        $totalCount = $tableObj->count($condition);

        $mpurl = $this->baseUrl;

        $order = 'sort_order desc';

        $maxPage = ceil($totalCount / $query['perpage']);
        $page = $maxPage > 0 && $page >= $maxPage ? $maxPage : $page;

        $currentLimit = $query['perpage'] * ($page - 1);

        $limit = $currentLimit . ',' . $query['perpage'];

        $list = $tableObj->getAll($condition, '*', $order, $limit);
        $pageHtml = helper_page::multi($totalCount, $query['perpage'], $page, $mpurl);

        include template('wxz_live:live/bannerList');
    }

    /**
     * 保存轮播图
     */
    public function bannerSave() {
        global $_G;
        $id = (int) $_GET['id'];

        $tableObj = new table_wxz_live_base(array('table' => 'wxz_live_banner', 'pk' => 'id'));

        if ($id) {
            $info = $tableObj->getRow($id);
        }

        if (submitcheck('save')) {
            $images = wxz_uploadimg();
            $img = $images['img'] ? $images['img'] : $_GET['img'];

            if (!$img) {
                cpmsg('必须上传轮播图片', $this->noRootUrl . "&act=bannerSave&id={$id}", 'error');
            }

            if ($id) {
                $updateData = array(
                    'img' => $img,
                    'link' => $_GET['link'],
                    'is_show' => $_GET['is_show'],
                    'sort_order' => $_GET['sort_order'],
                );

                $ret = $tableObj->update([$id], $updateData);
                if ($ret) {
                    cpmsg('更新成功', $this->noRootUrl . "&act=bannerList", 'success');
                }
            } else {

                //添加直播分类
                $insertData = array(
                    'img' => $img,
                    'link' => $_GET['link'],
                    'is_show' => $_GET['is_show'],
                    'sort_order' => $_GET['sort_order'],
                    'create_at' => date('Y-m-d H:i:s'),
                );

                $ret = $tableObj->insert($insertData);
                if ($ret) {
                    cpmsg('添加成功', $this->noRootUrl . "&act=bannerList", 'success');
                }
            }
        }
        include template('wxz_live:live/indexBannerSave');
    }

}