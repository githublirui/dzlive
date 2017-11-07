<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}



$sql = <<<EOF

DROP TABLE IF EXISTS `pre_wxz_live_player`;
DROP TABLE IF EXISTS `pre_wxz_live_room_setting`;
DROP TABLE IF EXISTS `pre_wxz_live_room`;
DROP TABLE IF EXISTS `pre_wxz_live_category`;
DROP TABLE IF EXISTS `pre_wxz_live_user`;
DROP TABLE IF EXISTS `pre_wxz_live_viewer`;
DROP TABLE IF EXISTS `pre_wxz_live_menu`;
DROP TABLE IF EXISTS `pre_wxz_live_comment`;
DROP TABLE IF EXISTS `pre_wxz_live_polling`;
DROP TABLE IF EXISTS `pre_wxz_live_setting`;
DROP TABLE IF EXISTS `pre_wxz_live_banner`;
DROP TABLE IF EXISTS `pre_wxz_live_order`;
DROP TABLE IF EXISTS `pre_wxz_live_zanpic`;
DROP TABLE IF EXISTS `pre_wxz_live_zannum`;
DROP TABLE IF EXISTS `pre_wxz_live_reward`;
DROP TABLE IF EXISTS `pre_wxz_live_gift`;
DROP TABLE IF EXISTS `pre_wxz_live_grouppacket`;
DROP TABLE IF EXISTS `pre_wxz_live_giftlog`;
DROP TABLE IF EXISTS `pre_wxz_live_money_log`;
DROP TABLE IF EXISTS `pre_wxz_live_grouppacket_log`;

EOF;
runquery($sql);

if(DISCUZ_VERSION != 'X2') {
    $path = DISCUZ_ROOT . 'data/sysdata/cache_wxz_live.php';
    @unlink($path);
}

$imgdir = DISCUZ_ROOT . 'data/attachment/wxz_live';
wxzfolder_del($imgdir);
$finish = TRUE;

function wxzfolder_del($path) {
    if (is_dir($path)) {
        $file_list = scandir($path);
        foreach ($file_list as $file) {
            if ($file != '.' && $file != '..') {
                wxzfolder_del($path . '/' . $file);
            }
        }
        @rmdir($path);
    } else {
        @unlink($path);
    }
}

?>