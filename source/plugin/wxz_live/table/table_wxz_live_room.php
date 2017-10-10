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

}

?>