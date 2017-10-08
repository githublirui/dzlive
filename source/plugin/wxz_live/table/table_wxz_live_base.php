<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/**
 *  公共表基础类 
 */
class table_wxz_live_base extends discuz_table {

    /**
     * 查询条数
     * @param type $condition
     * @return type
     */
    public function count($condition = '') {
        $sql = "SELECT count(*) FROM " . DB::table($this->_table);
        if ($condition) {
            $sql .= " where {$condition}";
        }
        $count = (int) DB::result_first($sql);
        return $count;
    }

    /**
     * 获取一条记录
     * @param type $condition
     * @param type $field
     */
    public function getRow($condition, $field = '*') {
        $sql = "SELECT {$field} FROM " . DB::table($this->_table);

        if ($condition) {
            $sql .= " WHERE {$condition}";
        }

        $query = DB::query($sql);
        return DB::fetch($query);
    }

    /**
     * 获取所有数据
     * @param type $condition
     */
    public function getAll($condition, $field = '*', $order = '', $limit = '') {
        $sql = "SELECT {$field} FROM " . DB::table($this->_table);

        if ($condition) {
            $sql .= " WHERE {$condition}";
        }

        if ($order) {
            $sql .= " order by {$order}";
        }

        if ($limit) {
            $sql .= " limit {$limit}";
        }
        $query = DB::query($sql);

        while ($value = DB::fetch($query)) {
            $result[$value[$this->_pk]] = $value;
        }

        return $result;
    }

    /**
     * 
     * @param type $id
     * @param type $data
     */
    public function updateById($id, $data) {
        return DB::update($this->_table, $data, "id={$id}");
    }

}

?>