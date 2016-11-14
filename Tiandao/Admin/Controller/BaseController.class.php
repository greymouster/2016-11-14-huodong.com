<?php

namespace Admin\Controller;

use Think\Controller;
use Admin\Logic\EditDataLogic;
use Admin\Model\AdminModel;

class BaseController extends Controller {

    public function __construct() {
        parent::__construct();
       //是否存在cookie  
        if (cookie("username")) {
            $username = cookie("username");
            $data = EditDataLogic::get_user_info($username);
            if ($data->code == 0) {
                session("user_id", $data->data->id);
                session("username", $data->data->username);
                session("realname", $data->data->realname);
            }
            return;
        }elseif (in_array(ACTION_NAME, array('login', 'logout', 'doLogin'))) {
            //过滤不需要登录的行为
             return;
        } else {
            if (session('user_id') > 0 ) {
                $this->check_priv(); //检查管理员菜单操作权限
            } else {
                $this->error('请先登陆', U('/Admin/Admin/login'), 1);
            }
        }
    }

    //检查管理员权限
    public function check_priv() {
        $ctl = CONTROLLER_NAME;
        $actl = ACTION_NAME;
        $noCheck = array('login', 'dologin', 'loginOut');
        //$act_list = session('act_list');
		$actList = AdminModel::init()->getOneData(array('username' => session('username')), 'act_list');
        $act_list = $actList['act_list'];
        if ($ctl == "Index" && $actl == 'index') {
            return true;
        } elseif ($ctl == "Admin" && $actl = "info") {
            return true;
        } elseif (strpos('ajax', $actl) || in_array($actl, $noCheck) || $act_list == "all") {
            return true;
        } else {
            $authId = D('Auth')->where("controller_name = '$ctl' AND acttion_name = '$actl'")->getField('id');
            $act_list = explode(',', $act_list);
            if ($authId) {
                if (!in_array($authId, $act_list)) {
                    $this->error('您的账号没有此菜单操作权限,超级管理员可分配权限', U('Admin/Admin/info'));
                    exit();
                } else {
                    return true;
                }
            }
        }
    }

}
