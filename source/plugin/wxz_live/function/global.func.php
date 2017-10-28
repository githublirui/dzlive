<?php

global $_G;
include_once DISCUZ_ROOT . "./source/plugin/wxz_live/function/wqglobal.func.php";

if ($_G['setting']['debug']) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
}

/**
 * 运行 controller
 * @author lirui <649037629@qq.com> 
 */
function runController() {
    $controller = $_GET['pmod'];
    $action = $_GET['act'] ? $_GET['act'] : 'index';

    $path = DISCUZ_ROOT . "./source/plugin/wxz_live/controller/Controller_base.class.php";
    include_once $path;

    $path = DISCUZ_ROOT . "./source/plugin/wxz_live/controller/Controller_{$controller}.class.php";

    if (!file_exists($path)) {
        if (defined('IN_ADMINCP')) {
            cpmsg('页面不存在', '', 'error');
        } else {
            showmessage('页面不存在');
        }
    }

    include_once $path;

    $class = "Controller_{$controller}";

    $controller = new $class();

    if (!method_exists($controller, $action)) {
        if (defined('IN_ADMINCP')) {
            cpmsg('页面不存在1', '', 'error');
        } else {
            showmessage('页面不存在1');
        }
    }

    $controller->init();
    //运行
    $controller->$action();
}

/**
 * 上传图片
 * @param type $savedir
 * @param image $thumb
 * @param type $width
 * @param type $height
 * @param type $iskeybili
 * @return string
 */
function wxz_uploadimg($savedir = 'wxz_live/', $thumb = false, $width = '220', $height = '220', $iskeybili = '1') {

    //上传图片
    $images = array();
    foreach ($_FILES as $upfile_name => $value) {
        if ($_FILES[$upfile_name]['error'] == 0) {
            require_once('source/class/discuz/discuz_upload.php');
            $upload = new discuz_upload();
            $r1 = $upload->init($_FILES[$upfile_name], 'common');

            if (!$upload->attach['isimage']) {
                return error(-1, '只能上传图片格式');
            }

            if ($r1) {
                $r2 = $upload->save(1);
                if ($r2) {
                    $pic = $_G['setting']['attachurl'] . 'common/' . $upload->attach['attachment'];
                }
            }


            if ($thumb) {
                //缩略图
                require_once('source/class/class_image.php');
                //随机名称
                $ext = addslashes(strtolower(substr(strrchr($_FILES[$upfile_name]['name'], '.'), 1, 10)));
                $thumb = new image;
                $img_path = $savedir . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 8) . rand(10000, 99999) . '.' . $ext;

                $thumb->THumb($upload->attach['target'], $img_path, $width, $height, $iskeybili);
            }

            $img_path = $thumb ? $img_path : $pic;
            $images[$upfile_name] = 'data/attachment/' . $img_path;
        }
    }
    return $images;
}

//打印输出数组信息
function printf_info($data) {
    foreach ($data as $key => $value) {
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

?>
