<?php

/**
 * 检查邮箱地址格式
 * @param $email 邮箱地址
 */
function checkEmail($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    }
    return false;
}

/**
 * 密码加密
 */
function encrypt($pwd) {
    return md5(C(MD5_KEY) . $pwd);
}

/**
 * [_getCode 获取随机手机验证码]
 * @param [type] num 获取随机数的个数
 * @return [type] 返回随机的字符串
 */
function getCode($num = 6) {
    $chars = "1234567890";
    $code = "";
    for ($i = 0; $i < $num; $i++) {
        $code .= substr($chars, mt_rand(0, 9), 1);
    }
    return $code;
}

/**
 * 打印函数
 */
function p($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

/**
 * 上传图片
 */
function uploadOne($imgName, $dirName, $thumb = array()) {
    if (isset($_FILES[$imgName]) && $_FILES[$imgName]['error'] == 0) {
        $rootPath = C('IMAGE_SAVE_PATH');
        $upload = new \Think\Upload(array(
            'rootPath' => $rootPath,
        )); // 实例化上传类
        $upload->maxSize = (int) C('IMG_maxSize') * 1024 * 1024; // 设置附件上传大小
        $upload->exts = C('IMG_exts'); // 设置附件上传类型
        /// $upload->rootPath = $rootPath; // 设置附件上传根目录
        $upload->savePath = $dirName . '/'; // 图片二级目录的名称
        // 上传文件 
        // 上传时指定一个要上传的图片的名称，否则会把表单中所有的图片都处理，之后再想其他图片时就再找不到图片了
        $info = $upload->upload(array($imgName => $_FILES[$imgName]));
        if (!$info) {
            return array(
                'ok' => 0,
                'error' => $upload->getError(),
            );
        } else {
            $ret['ok'] = 1;
            $ret['images'][0] = $logoName = $info[$imgName]['savepath'] . $info[$imgName]['savename'];
            // 判断是否生成缩略图
            if ($thumb) {
                $image = new \Think\Image();
                // 循环生成缩略图
                foreach ($thumb as $k => $v) {
                    $ret['images'][$k + 1] = $info[$imgName]['savepath'] . 'thumb_' . $k . '_' . $info[$imgName]['savename'];
                    // 打开要处理的图片
                    $image->open($rootPath . $logoName);
                    $image->thumb($v[0], $v[1])->save($rootPath . $ret['images'][$k + 1]);
                }
            }
            return $ret;
        }
    }
}

/**
 * 导出excel
 * @param $strTable	表格内容
 * @param $filename 文件名
 */
function downloadExcel($strTable, $filename) {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=" . $filename . "_" . date('Y-m-d') . ".xls");
    header('Expires:0');
    header('Pragma:public');
    echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . $strTable . '</html>';
}

/**
 * 下载文件
 * @param $file_name 文件名
 * @param $file_sub_dir 文件的子路径
 */
function down_file($file_name, $file_sub_dir) {
    $file_name = iconv("utf-8", "gb2312", $file_name);
    //绝对路径
    $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_sub_dir . $file_name;
    $fp = fopen($file_path, "r");
    //获取下载文件的大小
    $file_size = filesize($file_path);
    //返回的文件
    header("Content-type: application/octet-stream");
    //按照字节大小返回
    header("Accept-Ranges: bytes");
    //返回文件大小
    header("Accept-Length: $file_size");
    //这里客户端的弹出对话框，对应的文件名
    header("Content-Disposition: attachment; filename=" . $file_name);
    //向客户端回送数据
    $buffer = 1024;
    //为了下载的安全，我们最好做一个文件字节读取计数器
    $file_count = 0;
    //这句话用于判断文件是否结束
    while (!feof($fp) && ($file_size - $file_count > 0)) {
        $file_data = fread($fp, $buffer);
        //统计读了多少个字节
        $file_count+=$buffer;
        //把部分数据回送给浏览器;
        echo $file_data;
    }
    //关闭文件
    fclose($fp);
}

/**
 * $string 明文或密文
 * $operation 加密ENCODE或解密DECODE
 * $key 密钥
 * $expiry 密钥有效期
 */
function authcode($string, $operation = 'ENCODE', $key = 'K5HJ89Yd345K', $expiry = 0) {
    $ckey_length = 4;
    $key = md5($key);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}
/**
 * 管理员操作记录
 * @param type $log_info 记录信息
 */
function adminLog($log_info,$log_object){
    $add['admin_name'] = session('realname');
    $add['log_time']  = time();
    $add['log_ip']  = get_client_ip();
    $add['log_info'] = $log_info;
    $add['log_object'] = $log_object;
    M('admin_log')->add($add);
} 

function  getServicePath(){
    return ($_SERVER['REMOTE_PORT'] =="403" ? "https://" :"http://").$_SERVER['HTTP_HOST'];
}


/**
 *objarray_to_array 对象数组转换为数组
 *@param [obj]  $obj 对象数组
 *@return [array]  返回数组
 */
function objarray_to_array($obj) {
    $ret = array();
    foreach ($obj as $key => $value) {
        if (gettype($value) == "array" || gettype($value) == "object"){
            $ret[$key] =objarray_to_array($value);
        }else{
            $ret[$key] = $value;
        }
    }
    return $ret;
}