<?php

namespace Admin\Model;
use Think\Model;


class UserModel extends Model{
    
    protected $tableName = 'user'; //必须是protected

    public static function init() {
        return new self();
    }    
    
    //获取一条数据
    public function getOneData($where=null,$field=null){
        return $this->where($where)->field($field)->find();
    }


}