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
        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/lib/wxz_weixin.class.php";
        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/table/table_wxz_live_base.php";

        $wxzWeixin = new wxz_weixin();

        $jssdkConfig = $wxzWeixin->getJssdkConfig();
//        dsetcookie('test', 'abc',3600);
//$sesson->set('test', 'afb');
        var_dump(C::t('#wxz_live#wxz_live_user')->authUser(array('default_avatar' => 123123)));
        die;

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
     * @desc 微信授权
     */
    public function wxoauth() {
        global $_G;
        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/lib/wxz_weixin.class.php";
        $wxzWeixin = new wxz_weixin();

        $code = $_GPC['code'];

        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$wxzWeixin->appid}&secret={$wxzWeixin->appsecret}&code={$code}&grant_type=authorization_code";
        $resp = ihttp_get($url);
        if (is_error($resp)) {
            cpmsg('系统错误, 详情: ' . $resp['message']);
        }
        $auth = @json_decode($resp['content'], true);
        if (is_array($auth) && !empty($auth['openid'])) {
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$auth['access_token']}&openid={$auth['openid']}&lang=zh_CN";
            $resp = ihttp_get($url);
            if (is_error($resp)) {
                cpmsg('系统错误2');
            }
            $info = @json_decode($resp['content'], true);
            if (is_array($info) && !empty($info['openid'])) {
                $user = array();
                $user['openid'] = $info['openid'];
                $user['nickname'] = $info['nickname'];
                $user['sex'] = $info['sex'];
                $user['city'] = $info['city'];
                $user['province'] = $info['province'];
                $user['headimgurl'] = $info['headimgurl'];
                $user['ip'] = getip();

                if (!empty($user['headimgurl'])) {
                    $user['headimgurl'] = rtrim($user['avatar'], '0');
                    $user['headimgurl'] .= '132';
                }

                $ret = C::t('#wxz_live#wxz_live_user')->updateUser($user);

                $key = 'wxz_openid_' . $_G['wechat']['setting']['wechat_appId'];
                dsetcookie($key, $user['openid'], 2592000);

                $forward = getcookie('wxz_forward');
                header('Location: ' . $forward);
                exit();
            }
        }
        cpmsg('系统错误, 详情: 未获取到access token');
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
