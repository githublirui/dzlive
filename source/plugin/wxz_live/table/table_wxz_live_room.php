<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_wxz_live_room extends table_wxz_live_base {

    public function __construct() {
        $this->_table = 'wxz_live_room';
        $this->_pk = 'id';

        parent::__construct();
    }

    /**
     * 通过直播间ID获取详情
     * @param type $roomNo
     */
    public function getByRoomNo($roomNo) {
        $condition = "room_no='{$roomNo}'";
        return $this->getRow($condition);
    }

    /**
     * 通过直播间ID获取播放器信息
     * @param type $roomId
     */
    public function getPlayerInfoByRoomId($roomId) {
        if (!$roomId) {
            return false;
        }
        $tablePlayerObj = new table_wxz_live_base(array('table' => 'wxz_live_player', 'pk' => 'id'));

        $condition = "rid={$roomId}";
        $info = $tablePlayerObj->getRow($roomId);
        if (!$info) {
            return false;
        }
        $info['settings'] = unserialize($info['settings']);

        return $info;
    }

    /**
     * 通过直播间ID获取直播间配置
     * @param type $roomId
     */
    public function getSettingInfoByRoomId($roomId) {
        if (!$roomId) {
            return false;
        }
        $tablePlayerObj = new table_wxz_live_base(array('table' => 'wxz_live_room_setting', 'pk' => 'id'));

        $condition = "rid={$roomId}";
        $info = $tablePlayerObj->getRow($roomId);
        if (!$info) {
            return false;
        }
        return $info;
    }

    /**
     * 获取直播间配置
     * @param type $roomId
     */
    public function getRoomSetting($roomId) {
        if (!$roomId) {
            return false;
        }

        $tablePlayerObj = new table_wxz_live_base(array('table' => 'wxz_live_room_setting', 'pk' => 'id'));
        $condition = "rid={$roomId}";
        $info = $tablePlayerObj->getRow($roomId);

        if (!$info) {
            return false;
        }
        return $info;
    }

    /**
     * 格式化直播间数据
     */
    public function formatRoomData($roomInfo) {
        if (!$roomInfo) {
            return false;
        }
        $roomInfo['limit_data'] = $roomInfo['limit_data'] ? unserialize($roomInfo['limit_data']) : array();
        $roomInfo['online_user_config'] = $roomInfo['online_user_config'] ? unserialize($roomInfo['online_user_config']) : array();
        return $roomInfo;
    }

    /**
     * 获取直播间所有菜单
     * @param type $rid
     */
    public function getMenus($rid) {
        $tableObj = new table_wxz_live_base(array('table' => 'wxz_live_menu', 'pk' => 'id'));
        $condition = "rid={$rid} AND is_show=1";
        $menus = $tableObj->getAll($condition, '*', 'sort_order desc');
        foreach ($menus as $k => $menu) {
            $menus[$k]['settings'] = $menus[$k]['settings'] ? unserialize($menus[$k]['settings']) : '';
        }
        return $menus;
    }

    /**
     * 获取直播间评论列表
     * @param type $rid
     */
    public function getComments($rid) {
        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/lib/emo.php";

        $tableObj = new table_wxz_live_base(array('table' => 'wxz_live_comment', 'pk' => 'id'));

        $condition = "rid = {$rid} and is_auth=1";
        $field = "id,uid,rid,nickname,headimgurl,create_at,content,ispacket,tonickname,touid,dsid,giftid,giftnum,giftpic,ispic";
        $Comments = $tableObj->getAll($condition, $field, 'id desc', '0,15');
        $Comments = array_values($Comments);
        krsort($Comments);

        foreach ($Comments as $key => $v) {

            if ($v['giftid'] > 0) {
                $content = $v['nickname'] . '送出了<img src="' . $v['giftpic'] . '" width="45px" style="position: absolute;top: -15px;"><span style="margin-left:50px">x' . $v['giftnum'] . '</span>';
                $Comments[$key]['content'] = $content;
                $Comments[$key]['type'] = 'gift';
            } elseif ($v['dsid'] > 0) {
                if ($v['touid'] == 0) {
                    $content = $v['nickname'] . '给主播打赏了1个<span>红包</span>';
                } else {
                    $content = $v['nickname'] . '给' . $v['tonickname'] . '打赏了1个<span>红包</span>';
                }
                $Comments[$key]['content'] = $content;
                $Comments[$key]['type'] = 'reward';
            } elseif ($v['ispacket'] == 1) {
                $Comments[$key]['type'] = 'grouppacket';
            } else {
                foreach ($emoIndex as $k => $va) {

                    if (strpos($v['content'], $k) !== false) {
                        $Comments[$key]['content'] = str_replace($k, "<img class='emojia' src='" . $emo[$va]['1'] . "'/>", $v['content']);
                    }
                }
                $Comments[$key]['type'] = 'comment';
            }
        }
        $Comments = array_values($Comments);
        return $Comments;
    }

}

?>