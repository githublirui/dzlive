<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

include_once DISCUZ_ROOT . "./source/plugin/wxz_live/table/table_wxz_live_base.php";

class table_wxz_live_setting extends table_wxz_live_base {

    /**
     * 配置说明
     * @var type 
     */
    public static $types = array(
        1 => '首页标题和风格数据',
        2 => '首页分享数据',
    );

    public function __construct() {
        $this->_table = 'wxz_live_setting';
        $this->_pk = 'id';

        parent::__construct();
    }

    /**
     * 通过type获取详情
     * @param type $type
     */
    public function getByType($type) {
        if (!$type) {
            return;
        }

        if (!is_array($type)) {
            $condition = "type={$type}";
            return $this->getRow($condition);
        } else {
            $condition = "type in(" . implode(',', $type) . ")";
            $list = $this->getAll($condition);
            foreach ($list as $row) {
                $result[$row['type']] = $row;
            }
            return $result;
        }
    }

    /**
     * 保存type数据
     * @param type $type
     * @param type $data
     */
    public function saveTypeData($type, $data) {
        if (!$type || !$data) {
            return;
        }
        $typeInfo = $this->getByType($type);
        if ($typeInfo) {
            $this->updateById($typeInfo['id'], $data);
        } else {
            $data['create_at'] = date('Y-m-d H:i:s');
            $data['type'] = $type;
            $this->insert($data);
        }
    }

}

?>