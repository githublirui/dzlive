<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}



$sql = <<<EOF

DROP TABLE IF EXISTS pre_wxz_live_user;
DROP TABLE IF EXISTS pre_wxz_live_room;
DROP TABLE IF EXISTS pre_wxz_live_category;

EOF;
runquery($sql);

$path = DISCUZ_ROOT . 'data/sysdata/cache_wxz_live.php';
$imgdir = DISCUZ_ROOT . 'data/attachment/wxz_live';
@unlink($path);

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