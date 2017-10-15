<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT . "./source/plugin/wxz_live/table/table_wxz_live_base.php";

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

        $condition = "room_id={$roomId}";
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

        $condition = "room_id={$roomId}";
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
        $condition = "room_id={$roomId}";
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
        $roomInfo['limit_data'] = $roomInfo['limit_data'] ? unserialize($roomInfo['limit_data']) : array();
        return $roomInfo;
    }

}

?>