<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/**
 * 用户管理
 */
class Controller_user extends Controller_base {

    public function __construct() {
        parent::__construct();

        //子导航
        $this->navs = array(
            array(
                'name' => '用户列表',
                'act' => 'index',
            ),
        );
        $this->title = "用户管理";
    }

    /**
     * 用户列表页 
     */
    public function index() {
        $query['perpage'] = $_GET['perpage'] ? $_GET['perpage'] : 10;
        $query['nickname'] = trim($_GET['nickname']);
        $query['openid'] = trim($_GET['openid']);
        $query['city'] = trim($_GET['city']);
        $query['orderby'] = $_GET['orderby'] ? $_GET['orderby'] : 'create_at';
        $query['ordersc'] = $_GET['ordersc'] ? $_GET['ordersc'] : 'desc';
        $page = (int) $_GET['page'];
        $page = $page <= 0 ? 1 : $page;

        $tableObj = C::t('#wxz_live#wxz_live_user');

        $condition = "1=1";
        if ($query['nickname']) {
            $condition .= " AND `nickname` like '%{$query['nickname']}%'";
        }
        if ($query['city']) {
            $condition .= " AND `city` like '%{$query['city']}%'";
        }

        if ($query['openid']) {
            $condition = " `openid` = '{$query['openid']}'";
        }

        $totalCount = $tableObj->count($condition);

        $mpurl = $this->baseUrl . "&" . http_build_query($query);

        $order = $query['orderby'] . ' ' . $query['ordersc'];
        $maxPage = ceil($totalCount / $query['perpage']);
        $page = $maxPage > 0 && $page >= $maxPage ? $maxPage : $page;

        $currentLimit = $query['perpage'] * ($page - 1);

        $limit = $currentLimit . ',' . $query['perpage'];

        $list = $tableObj->getAll($condition, '*', $order, $limit);
        $pageHtml = helper_page::multi($totalCount, $query['perpage'], $page, $mpurl);

        include template('wxz_live:user/index');
    }

    /*
     * 用户修改
     * 
     * 
     */

    public function editUser() {
        $id = (int) $_GET['id'];

        $tableObj = C::t('#wxz_live#wxz_live_user');

        $info = $tableObj->fetch($id);

        if (submitcheck('save')) {
            //更新用户
            $updateData = array(
                'is_vip' => $_GET['is_vip'],
            );
            if ($updateData['is_vip'] == 2) {
                $updateData['vip_start'] = $_GET['vip_start'];
                $updateData['vip_end'] = $_GET['vip_end'];
            }
            $ret = $tableObj->update([$id], $updateData);
            if ($ret) {
                cpmsg('更新成功', $this->noRootUrl, 'success');
            }
        }

        include template('wxz_live:user/editUser');
    }

}