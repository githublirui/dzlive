<?php

class Controller_common extends Controller_base {

    public function __construct() {
        parent::__construct();

        //子导航
        $this->navs = array(
            array(
                'name' => '功能导航',
                'act' => 'index',
            ),
            array(
                'name' => '微信支付',
                'act' => 'wxpay',
            ),
        );
        $this->title = "常用功能";
    }

    /**
     * ajax 删除表数据
     */
    public function ajaxDelTable() {
        ob_end_clean();

        $table = $_GET['tableName'];
        $id = $_GET['id'];

        if (!$table || !$id) {
            $this->ajaxError('参数错误');
        }

        $tableObj = new table_wxz_live_base(array('table' => $table, 'pk' => 'id'));

        $ret = $tableObj->delById($id);
        if ($ret) {
            $this->ajaxSucceed();
        } else {
            $this->ajaxError('删除失败');
        }
    }

    /**
     * 功能列表页面
     */
    public function index() {
        global $_G;
        include template('wxz_live:common/pages');
    }

    /**
     * 生成二维码
     */
    public function qrcode() {
        ob_end_clean();

        include_once DISCUZ_ROOT . "./source/plugin/wxz_live/lib/qrcode/phpqrcode.php";

        $data = $_GET['data'];
        $data = urldecode($data);

        $errorCorrectionLevel = "L";
        $matrixPointSize = "4";
        QRcode::png($data, false, $errorCorrectionLevel, $matrixPointSize);
    }

    /**
     * @desc 微信支付配置
     * @param
     * @return
     */
    public function wxpay() {
        global $_G;

        $tableObj = C::t('#wxz_live#wxz_live_setting');
        $info = [];

        $setting = $tableObj->getByType(3);
        if ($setting) {
            $info = iunserializer($setting['desc']);
        }

        if (submitcheck('save')) {
            $images = wxz_uploadimg();



            $getSetting = array(
                'actname' => trim($_GET['actname']),
                'sname' => trim($_GET['sname']),
                'wishing' => trim($_GET['wishing']),
                'appid' => trim($_GET['appid']),
                'secret' => trim($_GET['secret']),
                'mchid' => trim($_GET['mchid']),
                'password' => trim($_GET['password']),
                'ip' => trim($_GET['ip']),
            );
            
            $getSetting['img'] = $images['img'] ? $images['img'] : $_GET['img'];
            
            $saveData = array(
                'type' => 3,
                'desc' => serialize($getSetting),
                'create_at' => date('Y-m-d H:i:d'),
            );

            //修改证书
            $certPath = DISCUZ_ROOT . "source/plugin/wxz_live/lib/wxpay/cert/";
            $certs = array('apiclient_cert', 'apiclient_key', 'rootca');
            foreach ($certs as $cert) {
                $content = trim($_GET[$cert]);
                $path = $certPath . $cert . '.pem';
                if ($content) {
                    file_put_contents($path, $content);
                }
            }

            if ($info) {
                $ret = $tableObj->updateById($setting['id'], $saveData);
                if ($ret) {
                    cpmsg('设置成功', $this->curNoRootUrlAct, 'success');
                }
            } else {
                $saveData['create_at'] = date('Y-m-d H:i:s');
                $ret = $tableObj->insert($saveData);
                if ($ret) {
                    cpmsg('设置成功', $this->curNoRootUrlAct, 'success');
                }
            }
        }
        include template('wxz_live:common/wxpay');
    }

}

?>
