<?php

/**
 * Description of Controller_base
 *
 * @author Administrator
 */
class Controller_base {

    //put your code here

    public function __construct() {
        $controller = $_GET['pmod'];
        $this->baseUrl = "admin.php?action=plugins&amp;operation=config&amp;identifier=%s&amp;pmod=%s";
        $this->noRootUrl = "action=plugins&amp;operation=config&amp;identifier=%s&amp;pmod=%s";
        $this->identifier = 'wxz_live';
        $this->baseUrl = sprintf($this->baseUrl, 'wxz_live', $controller);
        $this->noRootUrl = sprintf($this->noRootUrl, 'wxz_live', $controller);
        $this->curAct = $_GET['act'] ? $_GET['act'] : 'index';
    }

    public function init() {
        
    }

    /**
     * Ajax方式返回数据到客户端
     * @author masy <masy@51talk.com>
     * @param mixed $data 要返回的数据
     * @param String $info 提示信息
     * @param boolean $status 返回状态
     * @param String $status ajax返回类型 JSON XML
     * @return  void
     */
    public static function ajaxReturn($data, $info = '', $status = 1, $type = 'json') {
        $result = array();
        $result['status'] = $status;
        $result['info'] = $info;
        $result['data'] = $data;

        //判断ajax返回类型
        if ($type == 'json') {
            //返回JSON数据格式到客户端,包含状态信息
            header("Content-Type:text/html; charset=utf-8");
            exit(json_encode($result));
        } elseif ($type == 'xml') {
            //返回xml格式数据
            header("Content-Type:text/xml; charset=utf-8");
            exit(xml_encode($result));
        } elseif ($type == 'eval') {
            //返回可执行的js脚本
            header("Content-Type:text/html; charset=utf-8");
            exit($data);
        } elseif ($type == 'jsonp') {
            header("Content-Type:text/html; charset=utf-8");
            exit('try{' . Http::get("callback") . '(' . json_encode($result) . ')}catch(e){};');
        }
    }

    /**
     * Ajax方式返回错误信息
     * @param String $error_msg 提示信息
     * @param String $type ajax返回类型 JSON XML
     * @return  null
     */
    public static function ajaxError($error_msg = '', $type = 'json') {
        return self::ajaxReturn('', $error_msg, 0);
    }

    /**
     * Ajax方式返回正确信息
     * @param String|array $data 返回数据
     * @param String $msg 提示信息
     * @param String $type ajax返回类型 JSON XML
     * @return  null
     */
    public static function ajaxSucceed($data = '', $msg = '', $type = 'json') {
        return self::ajaxReturn($data, $msg, 1);
    }

}

?>
