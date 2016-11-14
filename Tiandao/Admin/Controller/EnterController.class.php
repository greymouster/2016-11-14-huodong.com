<?php

namespace Admin\Controller;

use Admin\Logic\EditDataLogic;

class EnterController extends BaseController {

    private static $ActmessageModel;
    private static $UserBmModel;
    private static $FormDataModel;
    public function __construct() {
        parent::__construct();
        if (empty(self::$ActmessageModel)) {
            self::$ActmessageModel = D('Actmessage');
        }
        if(empty(self::$UserBmModel)){
            self::$UserBmModel = D('UserBm');
        }
        if(empty(self::$FormDataModel)){
            self::$FormDataModel = D('FormData');
        }
    }

    public function index(){
        $actname = I('get.actname');
        $startDate = I('get.start_time');
        $endDate = I('get.end_time');
        //获取报名活动的用户
        $field = "a.*,act_name,act_status";
        $data = self::$UserBmModel ->searchData($actname,$startDate,$endDate);
        //获取所有的活动信息
        $field = "act_name,act_id";
        $info = self::$ActmessageModel->getAllData(array('act_current_status'=>4),$field);
        $this->assign(array(
            'data' => $data['data'],
            'page' => $data['page'],
            'info' => $info,
        ))->display();
    }

    public function pass(){
        $actId = I('post.actId', false, 'int');
        $userId = I('post.userId',false,'int');
        $pass = I('post.pass', false, 'htmlspecialchars');
        $where['act_id'] = array('eq' , $actId);
        $where['user_id'] = array('eq',$userId);
        $field = 'status';
        if ($pass == 'pass') {
            if (FALSE !== self::$UserBmModel->saveField($where, $field, $value = 1)) {
                $this->success('审核通过', '', TRUE);
            }
            $this->error('审核处理失败', '', TRUE);
        }
        if ($pass == 'nopass') {
            if (FALSE !== self::$UserBmModel->saveField($where, $field, $value = 2)) {
                $this->success('审核不通过', '', TRUE);
            }
            $this->error('审核处理失败', '', TRUE);
        }
        $this->error('审核处理失败', '', TRUE);
    }

    public function info() {
        $id = I('get.id', false, 'int');
        $userId = I('get.user_id',false,'int');
        $version = I('get.version',false,'int');
        //获取学生的信息
        $data = self::$UserBmModel->where(array('act_id' => $id,'user_id'=>$userId,'version'=>$version))->field('user_name,phone,email,values')->find();
        $values = explode(',',$data['values']);
        foreach($values as $k=>$v){
            $arr = explode('-',$v);
            $title = self::$FormDataModel->where(array('id'=>$arr[0],'act_id'=>$id))->field('Title')->find();
            $info['title'][] = $title['Title'];
            $info['value'][] = $arr[1];
        }
        $info = array_combine($info['title'],$info['value']);
        $this->assign(array(
             'data' => $data,
             'info' => $info,
        ))->display();
    }

}
