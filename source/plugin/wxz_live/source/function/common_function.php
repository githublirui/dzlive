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

function remove_nopromissword($str){
	static $check = array('/\"/', '/\>/', '/\</', '/\'/', '/\(/', '/\)/');
	return preg_replace($check,"",$str);
}

function wxz_live_getconfig(){
	global $_G;
	loadcache('plugin');
	$config = $_G['cache']['plugin']['wxz_live'];
	$config['realname'] = $config['realname'] ? $config['realname'] : 'realname';
	$config['idcard'] = $config['idcard'] ? $config['idcard'] : 'idcard';
	$config['vipgroup'] = wxz_live_vipgroups($config['vipgroup']);
	$config['groupselect'] = unserialize($config['groupselect']);
	return $config;
}


function wxz_go_header($url)  {  
	echo '<html><head><meta http-equiv="Content-Language" content="zh-CN"><meta http-equiv="refresh"  
	content="0;url='.$url.'"><title>loading ... </title></head><body><div style="display:none"></div><script>window.location="'.$url.'";</script></body></html>';  
	exit();  
}  
function PutMovie($file) {
    header("Content-type: video/mp4");
    header("Accept-Ranges: bytes");
     
    $size = filesize($file);
    if(isset($_SERVER['HTTP_RANGE'])){
        header("HTTP/1.1 206 Partial Content");
        list($name, $range) = explode("=", $_SERVER['HTTP_RANGE']);
        list($begin, $end) =explode("-", $range);
        if($end == 0) $end = $size - 1;
    }
    else {
        $begin = 0; $end = $size - 1;
    }
    header("Content-Length: " . ($end - $begin + 1));
    header("Content-Disposition: filename=".basename($file));
    header("Content-Range: bytes ".$begin."-".$end."/".$size);
 
    $fp = fopen($file, 'rb');
    fseek($fp, $begin);
    while(!feof($fp)) {
        $p = min(1024, $end - $begin + 1);
        $begin += $p;
        echo fread($fp, $p);
    }
    fclose($fp);
}
function wxz_showtitle($name,$array=array()){
	if (empty($array)) {
		return '';
	}

	$str = '<div class="itemtitle"><h3>'.$name.'</h3><ul class="tab1">';
	foreach ($array as $key => $value) {
		$class=$value[2] =='1'?' class="current"':'';
		$str .= '<li'.$class.'><a href='.'"admin.php?action='.$value[1].'"><span>'.$value[0].'</span></a></li>';
	}
	$str .='</ul></div>';
	echo $str;
}


function wxz_uploadimg($savedir='wxz_live/',$thumb=false,$width='220',$height='220',$iskeybili='1'){ 

	//上传图片
	$images = array();
	foreach ($_FILES as $upfile_name => $value) {
		if ($_FILES[$upfile_name]['error'] == 0) {
	        require_once('source/class/discuz/discuz_upload.php');
	        $upload = new discuz_upload();
	        if($r1=$upload->init($_FILES[$upfile_name], 'common') && $r2=$upload->save(1)) {
	                $pic = $_G['setting']['attachurl'].'common/'.$upload->attach['attachment'];

	        }

			if ($thumb) {
				//缩略图
				require_once('source/class/class_image.php');
				//随机名称
				$ext = addslashes(strtolower(substr(strrchr($_FILES[$upfile_name]['name'], '.'), 1, 10)));
				$thumb = new image;
				$img_path = $savedir.substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 8).rand(10000,99999).'.'.$ext;

				$thumb->THumb($upload->attach['target'],$img_path,$width,$height,$iskeybili);

			}

			$img_path = $thumb ? $img_path : $pic;
			$images[$upfile_name] = 'data/attachment/'.$img_path;
		}
	}
	return $images;
}

function wxz_livestrtoarray_tomobile($str){
	$str = str_replace(array("\r\n", "\r", "\n"), array('$#','$#','$#'), $str);
	$arr = explode('$#', $str);

	$arr = array_filter($arr);
	return $arr;
}

function wxz_live_vipgroups($str){
	$arr = wxz_livestrtoarray_tomobile($str);
	if (empty($arr)) {
		return false;
	}
	$return = array();
	foreach ($arr as $key => $value) {
		$tmp  = explode('=', $value);
		$return[$tmp[0]] = $tmp[1];
	}

	return $return;
}

function wxz_rewriteoutput($type, $returntype, $host) {
	global $_G;
	$fextra = '';
	if($type == 'forum_forumdisplay') {
		list(,,, $fid, $page, $extra) = func_get_args();
		$r = array(
			'{fid}' => empty($_G['setting']['forumkeys'][$fid]) ? $fid : $_G['setting']['forumkeys'][$fid],
			'{page}' => $page ? $page : 1,
		);
	} elseif($type == 'wxz_live') {
		list(,,, $mod, $cid, $vod) = func_get_args();
		$r = array(
			'{mod}' => $mod,
			'{cid}' => $cid,
			'{vid}' => $vid,
		);
	} 
	$href = str_replace(array_keys($r), $r, $_G['setting']['rewriterule'][$type]).$fextra;
	if(!$returntype) {
		return '<a href="'.$host.$href.'"'.(!empty($extra) ? stripslashes($extra) : '').'>';
	} else {
		return $host.$href;
	}
}

function wxz_diconv($str,$in_charset,$out_charset){
	if (is_string($str)) {
		return diconv($str,$in_charset,$out_charset);
	}else if (is_array($str)) {
		foreach ($str as $key => $value) {
			$str[$key] = wxz_diconv($value,$in_charset,$out_charset);
		}

		return $str;
	}
	return $str;
}



?>