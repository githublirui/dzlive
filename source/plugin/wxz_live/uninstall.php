<?php
/*
 *��˼����www.riscman.com
 *��������www.riscman.com
 *���ྫƷ��Դ�������˼���˹ٷ���վ��ѻ�ȡ
 *����Դ��Դ�������ռ�,��������ѧϰ����������������ҵ��;����������24Сʱ��ɾ��!
 *����ַ�������Ȩ��,�뼰ʱ��֪����,���Ǽ���ɾ��!
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}



$sql = <<<EOF

DROP TABLE pre_zhanmishu_video_cat;
DROP TABLE pre_zhanmishu_video_course;
DROP TABLE pre_zhanmishu_video;
DROP TABLE pre_zhanmishu_video_order;
DROP TABLE pre_zhanmishu_video_autho;

EOF;
 runquery($sql);
 
$path = DISCUZ_ROOT.'data/sysdata/cache_zhanmishu_video.php';
$imgdir = DISCUZ_ROOT.'data/attachment/zhanmishu_video';
@unlink($path);
//fix ɾ���ϴ���ʷ��¼��ͼƬ
zmsfolder_del($imgdir);
$finish = TRUE;


function zmsfolder_del($path){
    if(is_dir($path)){
        $file_list= scandir($path);
        foreach ($file_list as $file)
        {
            if( $file!='.' && $file!='..')
            {
                zmsfolder_del($path.'/'.$file);
            }
        }
        @rmdir($path);  //���ַ��������ж��ļ����Ƿ�Ϊ��,  ��Ϊ���ܿ�ʼʱ�ļ����Ƿ�Ϊ��,���������ʱ��,���ǿյ�     
    }else{
        @unlink($path);
    }
}

?>