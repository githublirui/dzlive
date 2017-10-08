<?php

class Controller_category extends Controller_base {

    public function __construct() {
        parent::__construct();

        //子导航
        $this->navs = array(
            array(
                'name' => '分类列表',
                'act' => 'index',
            ),
            array(
                'name' => '添加分类',
                'act' => 'add',
            ),
        );
        $this->title = "分类管理";
    }

    /**
     * 直播间列表页 
     */
    public function index() {
        $query['perpage'] = $_GET['perpage'] ? $_GET['perpage'] : 10;
        $query['name'] = trim($_GET['name']);
        $query['startTime'] = $_GET['startTime'];
        $query['endTime'] = $_GET['endTime'];
        $query['orderby'] = $_GET['orderby'] ? $_GET['orderby'] : 'sort_order';
        $query['ordersc'] = $_GET['ordersc'] ? $_GET['ordersc'] : 'desc';
        $page = (int) $_GET['page'];
        $page = $page <= 0 ? 1 : $page;
   
        if (submitcheck('ordersubmit')) {
            foreach ($_GET['ids'] as $k => $id) {
                $ret = C::t('#wxz_live#wxz_live_category')->updateById($id, array('sort_order' => $_GET['sort_orderS'][$k]));
            }
        }
        $condition = "1=1";
        if ($query['name']) {
            $condition .= " AND `name` like '%{$query['name']}%'";
        }

        if ($query['startTime']) {
            $condition .= " AND `create_at` >= '{$query['startTime']}'";
        }

        if ($query['endTime']) {
            $condition .= " AND `create_at` <= '{$query['startTime']} 23:59:59'";
        }

        $totalCount = C::t('#wxz_live#wxz_live_category')->count($condition);

        $mpurl = $this->baseUrl . "&" . http_build_query($query);

        $order = $query['orderby'] . ' ' . $query['ordersc'];
        $maxPage = ceil($totalCount / $query['perpage']);
        $page = $maxPage > 0 && $page >= $maxPage ? $maxPage : $page;
      
        $currentLimit = $query['perpage'] * ($page - 1);
     
        $limit = $currentLimit . ',' . $query['perpage'];

        $list = C::t('#wxz_live#wxz_live_category')->getAll($condition, '*', $order, $limit);
        $pageHtml = helper_page::multi($totalCount, $query['perpage'], $page, $mpurl);

        include template('wxz_live:category/index');
    }

    /**
     * 直播间列表页 
     */
    public function add() {
        global $_G;
        $id = $_GET['id'];

        if ($id) {
            $info = C::t('#wxz_live#wxz_live_category')->fetch($id);
        }

        if (submitcheck('add')) {
            if (!$_GET['name']) {
                cpmsg('分类名称不能为空', $this->noRootUrl . "&act=add", 'error');
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
        include template('wxz_live:category/add');
    }

}

?>
