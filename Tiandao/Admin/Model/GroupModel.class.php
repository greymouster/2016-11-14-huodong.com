<?php

namespace Admin\Model;

use Think\Model;
use Admin\Model\AdminModel;

class GroupModel extends Model {

    public static function init() {
        return new self;
    }

    //增加数据
    public function addData($data) {
        return $this->add($data);
    }

    //修改数据
    public function saveData($where = NULL, $data = NULL) {
        return $this->where($where)->save($data);
    }

    public function getOneData($where) {
        return $this->where($where)->find();
    }
	
	 //删除
    public function delData($where){
        return $this->where($where)->delete();
    }


    public function _before_delete($options){
		$groupId  = I('post.id',false,'int');
		AdminModel::init()->saveField(array('group_id'=>$groupId),'group_id',0);
    }


}
