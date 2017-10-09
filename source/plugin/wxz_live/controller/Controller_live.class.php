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
     * 直播间管理
     */
    public function playerSetting() {
        $id = $_GET['id'];

        //子导航
        $this->navs = array(
            array(
                'name' => '播放器设置',
                'act' => 'playerSetting',
            ),
        );
        $this->title = "直播间设置";

        $liveInfo = C::t('#wxz_live#wxz_live_room')->getById($id);

        if (!$liveInfo) {
            cpmsg('直播间不存在', $this->noRootUrl, 'error');
        }

        include template('wxz_live:live/playerSetting');
    }

    /**
     * 直播间列表页 
     */
    public function index() {
        $query['perpage'] = $_GET['perpage'] ? $_GET['perpage'] : 10;
        $query['category_name'] = trim($_GET['category_name']);
        $query['start_time'] = $_GET['start_time'];
        $query['end_time'] = $_GET['end_time'];
        $query['orderby'] = $_GET['orderby'] ? $_GET['orderby'] : 'sort_order';
        $query['ordersc'] = $_GET['ordersc'] ? $_GET['ordersc'] : 'desc';
        $page = (int) $_GET['page'];
        $page = $page <= 0 ? 1 : $page;

        $tableObj = C::t('#wxz_live#wxz_live_room');

        if (submitcheck('ordersubmit')) {
            foreach ($_GET['ids'] as $k => $id) {
                $ret = $tableObj->updateById($id, array('sort_order' => $_GET['sort_orders'][$k]));
            }
        }

        $condition = "1=1";
        if ($query['category_name']) {
            $conditionCat = "`name` like '%{$query['category_name']}%'";
            $categorys = C::t('#wxz_live#wxz_live_category')->getAll($conditionCat, 'id');
            if (!$categorys) {
                $condition .= " AND 1!=1";
            } else {
                $categoryIds = array_column($categorys, 'id');
                $condition .= " AND category_id in(" . implode(',', $categoryIds) . ")";
            }
        }

        if ($query['start_time']) {
            $condition .= " AND `start_time` >= '{$query['start_time']}'";
        }

        if ($query['end_time']) {
            $condition .= " AND `end_time` <= '{$query['end_time']} 23:59:59'";
        }

        $totalCount = $tableObj->count($condition);

        $mpurl = $this->baseUrl . "&" . http_build_query($query);

        $order = $query['orderby'] . ' ' . $query['ordersc'];
        $maxPage = ceil($totalCount / $query['perpage']);
        $page = $maxPage > 0 && $page >= $maxPage ? $maxPage : $page;

        $currentLimit = $query['perpage'] * ($page - 1);

        $limit = $currentLimit . ',' . $query['perpage'];

        $list = $tableObj->getAll($condition, '*', $order, $limit);

        foreach ($list as $k => $row) {
            $list[$k]['category'] = C::t('#wxz_live#wxz_live_category')->getById($row['category_id']);
        }

        $pageHtml = helper_page::multi($totalCount, $query['perpage'], $page, $mpurl);

        include template('wxz_live:live/index');
    }

    /**
     * 直播间列表页 
     */
    public function liveSave() {
        global $_G;
        $id = $_GET['id'];
        $tableObj = C::t('#wxz_live#wxz_live_room');
        if ($id) {
            $info = $tableObj->fetch($id);
            $info['url'] = "{$_G['siteurl']}plugin.php?id=wxz_live:index&pmod=index&act=live&roomno={$info['room_no']}";
            $encodeUrl = urlencode($info['url']);
            $info['qrcode'] = "?frame=no&action=plugins&operation=config&identifier=wxz_live&pmod=common&act=qrcode&data={$encodeUrl}";
        }

        //获取所有分类
        $categorys = C::t('#wxz_live#wxz_live_category')->getShowCategorys();

        if (submitcheck('save')) {
            $images = wxz_uploadimg();

            if ($id) {
                //更新直播分类
                $updateData = array(
                    'category_id' => $_GET['category_id'],
                    'title' => $_GET['title'],
                    'start_time' => $_GET['start_time'],
                    'end_time' => $_GET['end_time'],
                    'is_show' => $_GET['is_show'],
                    'sort_order' => $_GET['sort_order'],
                );
                $updateData['theme_pic'] = $images['theme_pic'] ? $images['theme_pic'] : $_GET['theme_pic'];
                $updateData['cover_pic'] = $images['cover_pic'] ? $images['cover_pic'] : $_GET['cover_pic'];
                $ret = $tableObj->update([$id], $updateData);
                if ($ret) {
                    cpmsg('更新成功', $this->noRootUrl, 'success');
                }
            } else {
                $roomNo = random(10); //房间号
                //添加直播分类
                $insertData = array(
                    'room_no' => $roomNo,
                    'category_id' => $_GET['category_id'],
                    'title' => $_GET['title'],
                    'start_time' => $_GET['start_time'],
                    'end_time' => $_GET['end_time'],
                    'is_show' => $_GET['is_show'],
                    'sort_order' => $_GET['sort_order'],
                    'create_at' => date('Y-m-d H:i:s'),
                );
                $insertData['theme_pic'] = $images['theme_pic'] ? $images['theme_pic'] : $_GET['theme_pic'];
                $insertData['cover_pic'] = $images['cover_pic'] ? $images['cover_pic'] : $_GET['cover_pic'];
                $ret = $tableObj->insert($insertData);
                if ($ret) {
                    cpmsg('添加成功', $this->noRootUrl, 'success');
                }
            }
        }


        include template('wxz_live:live/liveSave');
    }

}

?>
