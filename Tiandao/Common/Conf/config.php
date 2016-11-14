<?php
return array(
    //"SHOW_PAGE_TRACE"  => true,//显示页面Trace信息
    'DEFAULT_MODULE'        =>  'Admin',  // 默认模块
    'DEFAULT_CONTROLLER'    =>  'Admin', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'login', // 默认操作名称
    'LOAD_EXT_CONFIG'=>'db', // 加载数据库配置文件
    'MD5_KEY' => 'Tiandao.com', //MD5密钥
	'ApiUrl'  => 'http://newapi.tdedu.org/', //邮箱api
	 
	 /*************** 图片相关配置 *********************/
	'IMAGE_PREFIX' => '/Public/Uploads/',  // 显示图片时的前缀
	'IMAGE_SAVE_PATH' => './Public/Uploads/',
	'IMG_maxSize' => 2, // 单位M
	'IMG_exts' => array('jpg', 'gif', 'png', 'jpeg', 'pjpeg'),
	//推广活动的url
	'ACT_URL' => "http://m.tiandaoedu.com/tdhd/",
	'DEFAULT_FILTER' => 'trim,htmlspecialchars',
	/********************RTX接口配置**************/
	'RTX_URL' =>'http://pm.tdedu.org/api/',
	//接口校验
	'APPID'=>'849492',
	'APPSECRET'=>"69a23FG42H3D42f34",	
	//可逆加密KEY
	'AUTH_KEY' => 'K5HJ89Yd345K',
    /****************移动端数据接口**************/
    'MOBILE_URL' => 'http://m.tiandaoedu.com/index.php/Activity/',
);