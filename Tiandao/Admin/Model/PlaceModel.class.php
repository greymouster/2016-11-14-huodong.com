<?php

/**
 * 地点管理
 */

namespace Admin\Model;

use Think\Model;

class PlaceModel extends Model {

    public function _after_insert($data, $option) {
        $this->where(array(
            'id' => array('eq', $data['id']),
        ))->save(array('sort_number' => $data['id']));
    }

    //排序
    public function incSortNumber($sortNumber, $prevSortNumber) {
        $sql = "UPDATE td_place AS a 
                JOIN td_place b 
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
