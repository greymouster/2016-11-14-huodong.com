<?php

/**
 * 分类
 */

namespace Admin\Model;

use Think\Model;

class CategoryModel extends Model {

    public function _after_insert($data, $option) {
        $this->where(array(
            'id' => array('eq', $data['id']),
        ))->save(array('sort_number' => $data['id']));
    }

    //更新排序字段
    public function incSortNumber($sortNumber, $prevSortNumber) {
        $sql = "UPDATE td_category AS a 
                JOIN td_category b 
                ON (a.sort_number = {$sortNumber} AND b.sort_number = {$prevSortNumber}) 
			    SET a.sort_number = b.sort_number, b.sort_number = a.sort_number ";
        return $this->execute($sql);
    }
    public function getOneData($where,$field){
        return $this->where($where)->field($field)->find();
    }
    public static function init() {
        return new self();
    }

}
