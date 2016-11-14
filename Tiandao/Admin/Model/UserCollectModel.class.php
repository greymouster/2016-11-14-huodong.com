<?php

namespace Admin\Model;
use Think\Model;
use Think\Page;

class UserCollectModel extends Model{


    public function addData($data){
        return $this->add($data);
    }

    public function getOneData($where){
        return $this->where($where)->find();
    }


    public function getAllData($where,$field){
        return $this->where($where)->field($field)->select();
    }

    public function delData($where){
        return $this->where($where)->delete();
    }
}