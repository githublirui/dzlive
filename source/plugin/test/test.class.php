<?php

/**
 *  (C)2013
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_test {

	/*
	 * �����û���Ϣ��ǣ�����ֵΪ�����ʾ����
	 * "���� ? �������� ? �����û���Ϣ" �û���Ϣģ���еı��
	 * �ĵ�: http://open.discuz.net/?ac=document&page=plugin_hook (����"profile_node")
	 */
	function profile_node($post, $start, $end) {
		return $start.'���ǲ��'.$end;
	}

}

?>