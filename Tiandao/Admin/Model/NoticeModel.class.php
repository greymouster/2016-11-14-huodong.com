<?php

namespace Admin\Model;

use Think\Model;

/**
 * 公告
 */
class NoticeModel extends Model {

    //允许接收的字段
    protected $insertFields = 'title,content';
    //允许修改的字段
    protected $updateFields = 'id,user_id,title,content,update_time';
    // 定义表单验证的规则
    protected $_validate = array(
        array('title', 'require', '标题不能为空', 1),
        array('content', 'require', '公告内容不能为空', 1),
    );
    protected $_auto = array(
        array('user_id', 'getUserId', 3, 'callback'),
        array('update_time', 'getTime', 3, 'callback'),
    );

    //获取当前用户的id
    public function getUserId() {
        return session('user_id');
    }

    //当前时间
    public function getTime() {
        return time();
    }

    //获取一条数据
    public function getOneData($where = null, $field = null) {
        return $this->where($where)->field($field)->find();
    }

    //添加一条数据
    public function addData() {
        return $this->add();
    }

    //删除数据
    public function deleteData($where = null) {
        return $this->where($where)->delete();
    }

}
