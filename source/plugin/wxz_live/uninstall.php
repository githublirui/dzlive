<?php
/*
 *合肥微小智www.hfwxz.com
 *备用域名www.hfwxz.com
 *更多精品资源请访问合肥微小智官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}



$sql = <<<EOF

DROP TABLE pre_wxz_live_cat;
DROP TABLE pre_wxz_live_course;
DROP TABLE pre_wxz_live;
DROP TABLE pre_wxz_live_order;
DROP TABLE pre_wxz_live_autho;

EOF;
 runquery($sql);
 
$path = DISCUZ_ROOT.'data/sysdata/cache_wxz_live.php';
$imgdir = DISCUZ_ROOT.'data/attachment/wxz_live';
@unlink($path);
//fix 删除上传历史记录的图片
wxzfolder_del($imgdir);
$finish = TRUE;


function wxzfolder_del($path){
    if(is_dir($path)){
        $file_list= scandir($path);
        foreach ($file_list as $file)
        {
            if( $file!='.' && $file!='..')
            {
                wxzfolder_del($path.'/'.$file);
            }
        }
        @rmdir($path);  //这种方法不用判断文件夹是否为空,  因为不管开始时文件夹是否为空,到达这里的时候,都是空的     
    }else{
        @unlink($path);
    }
}

?>