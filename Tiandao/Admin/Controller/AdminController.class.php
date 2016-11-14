<?php

/**
 * Auth:run
 * date:2016-8-8  
 * 接口文档地址
 * http://192.168.30.225/dokuwiki/doku.php?id=start&do=index
 */

namespace Admin\Controller;

use Admin\Model\AdminModel;
use Admin\Logic\EditDataLogic;

class AdminController extends   BaseController {

    //登录
    public function login() {
	
        $this->display('login');
    }

    //检验登录
    public function doLogin() {
        EditDataLogic::checkHost();
        $username = trim(I('post.username', false, 'htmlspecialchars'));
        $password = trim(I('post.password', false, 'htmlspecialchars'));
        $remeber = I('post.remeber', false, 'int');
        $data = array(
            'username' => $username, //用户名
            'password' => authcode($password, 'ENCODE', C('AUTH_KEY'), 0), //密码
            'ip' => get_client_ip($type = 0), //IP
            'APPID' => C('APPID'), //接口校验
            'APPSECRET' => C('APPSECRET'),
            'AUTH_KEY' => C('AUTH_KEY'), //密钥
        );
        $url = C('RTX_URL') . 'login';
        $obj = EditDataLogic::curl_post($url, $data);
        $result = json_decode($obj);
        if ($result->code == 0) {
            $isLock = AdminModel::init()->getOneData(array('username' => $result->data->username), 'is_lock,act_list');
            session('act_list', $isLock['act_list']);
            if ($isLock['is_lock'] != '禁用') {
                session('user_id', $result->data->id);
                session('username', $result->data->username);
                session('realname', $result->data->realname);
                if ($remeber == 1) {
                    cookie('username', $result->data->username);
                }
                $this->ajaxReturn(array('status' => $result->code, 'msg' => '登录成功'), 'json');
            }
            $this->ajaxReturn(array('status' => -1, 'msg' => '此账号已被禁用,请联系管理员!'), 'json');
        }
        $this->ajaxReturn(array('status' => -1, 'msg' => $result->data->msg), 'json');
    }

    //用户信息
    public function info() {
        //获取用户信息
        $id = I('get.id', false, 'int');
        if ($id) {
            $where = array('admin_id' => $id);
            $user = AdminModel::init()->getOneData($where, 'username');
        }
        $username = $user['username'] ? $user['username'] : session('username');
        $data = EditDataLogic::get_user_info($username);
        $data->data->email = $data->data->username . "@tiandaoedu.com";
        //获取用户组
        $group = D('Admin')->getData(array('username' => $username));
        $this->assign(array('data' => $data, 'group' => $group[0]['group_name']))->display();
    }
    //修改密码
    public function setPasswd(){
        $this->display();
    }

    public function setPwd(){
        EditDataLogic::checkHost();
        $oldpwd = trim(I('post.oldpwd'));
        $newpwd = trim(I('post.newpwd'));
        $rnpwd = trim(I('post.rnpwd'));
        if($oldpwd ==null ||$newpwd == null || $rnpwd == null ||strlen($oldpwd)<6 || strlen($newpwd)<6 || strlen($rnpwd)<6){
            $this->ajaxReturn(array('status' => -1, 'msg' => '请输入正确的密码'), 'json');
        }
        if($newpwd != $rnpwd){
            $this->ajaxReturn(array('status' => -1, 'msg' => '两次密码不一致'), 'json');
        }
        $data = array(
            'username' => session('username'), //用户名
            'password' => authcode($oldpwd, 'ENCODE', C('AUTH_KEY'), 0),
            'npassword' => authcode($newpwd, 'ENCODE', C('AUTH_KEY'), 0),
            'rnpassword' => authcode($rnpwd, 'ENCODE', C('AUTH_KEY'), 0),
            'APPID' => C('APPID'), //接口校验
            'APPSECRET' => C('APPSECRET'),
            'AUTH_KEY' => C('AUTH_KEY'), //密钥
        );
        $url = C('RTX_URL') . 'edit_pass';
        $obj = EditDataLogic::curl_post($url, $data);
        $result = json_decode($obj);
        if($result->code == 100314){
            $this->ajaxReturn(array('status' => -1, 'msg' => '原密码错误'), 'json');
        }
        if($result->code == 100310){
            $this->ajaxReturn(array('status' => -1, 'msg' => '新密码与旧密码重复'), 'json');
        }

        if($result->code == 0){
            $this->ajaxReturn(array('status' => 1, 'msg' => '修改密码成功'), 'json');
        }
        $this->ajaxReturn(array('status'=>-1,'msg'=>'修改密码失败','json'));
    }
	
    //退出登录
    public function loginOut() {
        setcookie('username', '', time() - 3600, '/');
        session_unset();
        session_destroy();
        header("location:" . U('Admin/login'));
    }

}
