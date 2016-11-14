<?php

namespace Admin\Model;

use Think\Model;

class AdminModel extends Model {

    //获取一条数据
    public function getOneData($where, $field = NULL) {
        return $this->where($where)->field($field)->find();
    }

    public function getData($where, $field = NULL) {
        return $this->alias('a')->join('LEFT JOIN __GROUP__  b ON  a.group_id = b.id')->where($where)->field($field)->select();
    }

    public function saveData($where, $data) {
        return $this->where($where)->save($data);
    }

    public function saveField($where = null, $field = null, $value = null) {
        return $this->where($where)->setField($field, $value);
    }

    public static function init() {
        return new self;
    }

}
