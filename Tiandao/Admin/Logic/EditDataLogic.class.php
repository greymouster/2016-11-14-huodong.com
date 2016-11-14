<?php

namespace Admin\Logic;

class EditDataLogic {

    //查询数据
    public static function getAllData($table, $order = 'id asc') {
        return D("{$table}")->order($order)->select();
    }
   //查询单条数据
    public static function getOneData($table,$where=null){
        return D("{$table}")->where($where)->find();
    }

    //添加
    public static function addData($table, $data = null) {
        return D("{$table}")->add($data);
    }

    //修改
    public static function editData($table, $where = null, $field = null) {
        return D("{$table}")->where($where)->setField($field);
    }

    //删除
    public static function delData($table, $where = null) {
        return D("{$table}")->where($where)->delete();
    }

    //获取类型
    public static function getType($data) {
        switch ($data) {
            case '1':
                return '首页轮播';
                break;
            case '2':
                return '分类图';
                break;
            case '3':
                return '小道推荐';
                break;
            case '4':
                return '热门活动推荐';
                break;
            default :
                break;
        }
    }

    /**
     * [curl_post curl post方式请求接口]
     * @param  [type] $url  [接口的url]
     * @param  [type] $data [传递的参数]
     * @return [type]       [返回接口信息]
     */
    public static function curl_post($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $return = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);
        if(intval($status["http_code"]) == 200) {
            return $return;
        } else {
            echo $status["http_code"];
            return false;
        }
    }

    /**
     * http://192.168.30.225/dokuwiki/doku.php?id=start&do=index api文档
     * [get_user_info 获取用户信息]
     * @param  [type] $username [用户名]
     * @return [type]           [description]
     */
    public static function get_user_info($username) {
        $url = C('RTX_URL') . 'get_user_info';
        $data = array(
            'username' => $username,
            'APPID' => C('APPID'), //接口校验
            'APPSECRET' => C('APPSECRET'),
            'AUTH_KEY' => C('AUTH_KEY'), //密钥
        );

        $info = self::curl_post($url, $data);
        return json_decode($info);
    }

    /**
     * POST CHECK
     */
    public static function checkHost() {
        if (!IS_POST) {
            exit(json_encode(array('status' => -1, 'msg' => '非法请求')));
        }
    }

}
