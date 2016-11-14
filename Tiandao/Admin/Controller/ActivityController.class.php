<?php

/**
 * 活动推广类
 */

namespace Admin\Controller;

use Think\AjaxPage;
use Think\Page;
use Think\NewPage;
use Admin\Model\FormDataModel;
use Admin\Logic\EditDataLogic;

class ActivityController extends BaseController {

    private static $ActmessageModel;
    private static $ActMessageChannelModel;
    private static $studentsViewModel;
    private static $studentsModel;

    public function __construct() {
        parent::__construct();
        if (empty(self::$ActmessageModel)) {
            self::$ActmessageModel = D('Actmessage');
        }
        if (empty(self::$ActMessageChannelModel)) {
            self::$ActMessageChannelModel = D('ActMessageChannel');
        }
        if (empty(self::$studentsViewModel)) {
            self::$studentsViewModel = D('studentsView');
        }
        if (empty(self::$studentsModel)) {
            self::$studentsModel = M('Students');
        }
    }

    public function release() {
        //获取分类
        $cateData = EditDataLogic::getAllData('Category');
        //获取地点
        $placeData = EditDataLogic::getAllData('Place');
        $this->assign(array(
            'cateData' => $cateData,
            'placeData' => $placeData,
        ));
        $this->display();
    }

    //发布新活动
    public function add(){
        EditDataLogic::checkHost();
        $actId = I('post.act_id',false,'int');
        if($actId){
			//获取活动信息
            $actData = self::$ActmessageModel->getOneData(array('act_id'=>$actId),'act_name');
            $data['act_pub_status'] = 2;
            $data['act_current_status'] = 2;
             if(FALSE !== self::$ActmessageModel->saveData(array('act_id'=>$actId),$data)){
                 adminLog("发布新活动",$actData['act_name']);
				 $this->success('发布成功','',TRUE);
             }
        }
        $this->error('发布失败','',TRUE);
    }

    //保存为草稿
     public function addDraft(){
         sleep(1);
         EditDataLogic::checkHost();
         $data = I('post.');
         $data = self::$ActmessageModel->setData($data);
         $data['act_pub_status'] = 1;
         if (self::$ActmessageModel->create($data, 1)) {
             if ($id = self::$ActmessageModel->add()) {
                 $this->success('保存成功', $id, TRUE);
             }
         }
         $this->error(self::$ActmessageModel->getError(), '', TRUE);
     }

     //活动预览
     public function actPreview(){
         $actId = I('get.act_id',false,'int');
         if($actId){
             $data = self::$ActmessageModel->numData(array('a.act_id'=>$actId));
             foreach($data as $k=>$v){
                 $data[$k]['act_start_date'] = date('Y年m月d日',$v['act_start_date']);
                 $data[$k]['act_end_date'] = date('m月d日',$v['act_end_date']);
                 $data[$k]['act_start_time'] = date('H:i',$v['act_start_date']);
                 $data[$k]['act_end_time'] = date('H:i',$v['act_end_date']);
                 $data[$k]['act_desc'] = htmlspecialchars_decode($v['act_desc']);
             }
          //var_dump($data);die;

         }
         $this->assign('data',$data[0])->display();
     }


    //表单设置
    public function setForm() {
        $actId = I('get.act_id',false,'int');
        if(!$actId){
            return false;
        }
        $version = I('get.version',0,'int');
        //获取表单的信息数据
        $data = D('FormData')->getAllData(array('act_id'=>$actId,'Version'=>$version));
        if($data){
            $html = D('FormData')->getFormHtml($data);
            $lookHtml = D('FormData')->getLookFormHtml($data);
        }
        //var_dump($html);die;
        $this->assign(array(
             'data' => $data,
             'html'=>$html,
             'lookHtml'=>$lookHtml
        ))->display();
    }


    //自定义表单
    public function saveEventForm(){
        $data = I("post.");
        $actId = $data['activityId'];
        //通过活动id 去查询是否保存过数据
        $info = D('FormData')->where(array('act_id'=>$actId))->select();
        if($info){
            $this->error('保存失败','',TRUE);
        }
        $version = $data['version'];
        foreach($data as $k=>$v){
            if(is_array($v)){
                if(!isset($v['Subitems'])){
                    $data[$k]['Subitems'] = '';
                }else{
                    $data[$k]['Subitems'] = implode(',',$v['Subitems']);
                }
                $data[$k]['act_id'] = $data['activityId'];
                $data[$k]['Version'] = $data['version'];
                if(isset($v['Required'])){
                    $data[$k]['Required'] = $v['Required'];
                }else{
                    $data[$k]['Required'] = "false";
                }

            }
        }
        unset($data['activityId']);
        unset($data['sortmax']);
        unset($data['version']);
        foreach($data as $k=>$v){
            $info = D('FormData')->add($v);
            if(!$info){
                $this->error('保存失败','',TRUE);
                continue;
            }
        }
        if($info){
            if(self::$ActmessageModel->saveData(array('act_id'=>$actId),array('act_version'=>$version)) !== false){
                $this->success('保存成功','',TRUE);
            }
            $this->error('保存失败','',TRUE);
        }
        $this->error('保存失败','',TRUE);
    }
    /**
     * 修改表单页面
     */
    public function saveForm(){
        $actId = I('get.act_id',false,'int');
        $version = I('get.version',false,'int');
        //获取表单的信息数据
        $data = D('FormData')->getAllData(array('act_id'=>$actId,'Version'=>$version));
        //拼凑表单数据
        $formData = D('FormData')->getFormHtml($data);
        $this->assign('formData',$formData)->display();
    }

    public function saveFormData(){
        
    }

    //活动信息
    public function actMessage() {
        //根据用户名和搜索条件
        $realname = session('realname');
        $currentStatus = I('get.status', false, 'int');
        $actname = trim(I('get.actname', false, 'htmlspecialchars'));
        $st = I('get.start_time', false, 'htmlspecialchars');
        $et = I('get.end_time', false, 'htmlspecialchars');
        $data = self::$ActmessageModel->searchData($actname, $st, $et, $currentStatus, $realname);
        foreach ($data['data'] as $k => $v) {
            $data['data'][$k]['act_start_date'] = date('Y年m月d日', $v['act_start_date']);
            $data['data'][$k]['act_end_date'] = date('m月d日', $v['act_end_date']);
            $data['data'][$k]['act_success_time'] = date('Y-m-d H:i:s', $v['act_success_time']);
            $data['data'][$k]['current_status'] = self::$ActmessageModel->getCurrentStatus($v['act_current_status'],$v['act_date'],$v['act_end_date']);
        }
        //获取所有的活动信息
        $field = "act_name,act_id";
        $info = self::$ActmessageModel->getAllData(array('act_charge_name' => session('realname')), $field);
        $this->assign(array(
            'data' => $data['data'],
            'page' => $data['page'],
            'info' => $info,
        ));
        $this->display();
    }

    //删除活动信息
    public function delActivity() {
        $actId = I('get.actId', false, 'int');
        if (!$actId) {
            $this->error('请求参数有误', '', TRUE);
        }
		//获取活动信息
        $actData = self::$ActmessageModel->getOneData(array('act_id'=>$actId),'act_name');
        if (FALSE !== self::$ActmessageModel->deleteData(array('act_id' => $actId))) {
            adminLog("删除活动",$actData['act_name']);
			$this->success('删除成功', '', TRUE);
        }
        $this->error('删除失败', '', TRUE);
    }

    //活动下线
    public function outLine() {
        $actId = I('get.actId', false, 'int');
        if (!$actId) {
            $this->error('请求参数有误', '', TRUE);
        }
        //判断活动是否已经下线
        $data = self::$ActmessageModel->getOneData(array('act_id' => $actId), 'act_current_status');
        if ($data['act_current_status'] == 4) {
            if (FALSE !== self::$ActmessageModel->outLine(array('act_id' => $actId), 3)) {
                $this->success('此活动下线成功', '', TRUE);
            }
        }
        $this->error('此活动不在线上！', '', TRUE);
    }

    //编辑页面
    public function editMessage() {
        $id = I('get.act_id', false, 'int');
        //获取所有的信息
        $data = self::$ActmessageModel->getOneData(array('act_id' => $id));
        //时间戳转换
        $data['act_start_time'] = date('H:i',$data['act_start_date']);
        $data['act_end_time']  = date('H:i',$data['act_end_date']);
        $data['act_start_date']  = date('Y-m-d',$data['act_start_date']);
        $data['act_end_date']   = date('Y-m-d',$data['act_end_date']);
        $data['date_time'] = $data['act_date'];
        $data['act_time'] = date('H:i',$data['act_date']);
        $data['act_date']  = date('Y-m-d',$data['act_date']);
        
		//获取此活动下的所有分类
        $catIds = D('act_cat')->field('GROUP_CONCAT(cat_id) cat_id')->where(array('act_id'=>$id))->find();
        $data['cat_id']  =  $catIds['cat_id'];
        //获取分类
        $cateData = EditDataLogic::getAllData('Category');
        //获取地点
        $placeData = EditDataLogic::getAllData('Place');
        $this->assign(array(
            'data' => $data,
            'cateData' => $cateData,
            'placeData' => $placeData,
        ));
        $this->display();
    }

    //编辑活动
    public function edit() {
        EditDataLogic::checkHost();
		$data = I('post.');
		 //修改活动的分类前先删除此活动的分类
        D('act_cat')->where(array('act_id'=>$data['act_id']))->delete();
        foreach($data['cat_id'] as $k=>$v){
            D('act_cat')->add(array(
                'act_id' => $data['act_id'],
                'cat_id' => $v,
            ));
        }
        unset($data['cat_id']);
        $data = self::$ActmessageModel->setData($data);
        $data['act_pub_status'] = 1;
        $data['act_current_status'] = 1;
        if (FALSE !== self::$ActmessageModel->save($data)) {
            $this->success('修改成功', '', TRUE);
        }
        $this->error(self::$ActmessageModel->getError(), '', TRUE);
    }

    //批量下线
    public function batchOffline() {
        EditDataLogic::checkHost();
        $data = I('post.arr');
        $data = implode(',', $data);
        $where = array('act_id' => array('in', $data));
        if (FALSE !== self::$ActmessageModel->outLine($where, 1)) {
            $this->success('操作成功', '', TRUE);
        }
        $this->error('操作失败', '', TRUE);
    }

    //活动推广页面
    public function actPromotion() {
        //获取全部的渠道
        $channelData = EditDataLogic::getAllData('Channel');
        //获取活动的推广信息
        $id = I("get.act_id");
        $data = self::$ActMessageChannelModel->getAllData(array('act_id' => $id));
        //根据活动id查询出活动信息
        $actData = self::$ActmessageModel->getOneData(array('act_id'=>$id),'act_name');
		$this->assign(array(
            'channelData' => $channelData,
            'data' => $data,
			'actName'=>$actData['act_name']
        ));
        $this->display();
    }

    //添加活动推广
    public function addChannelMessage() {
        EditDataLogic::checkHost();
        $data = I('post.');
        $keys = array_keys($data);
        $result = array();
        foreach ($keys as $key) {
            foreach ($data[$key] as $k => $value) {
                $result[$k][$key] = $value;
                //$result[$k]['channel_url'] = C('ACT_URL') . "?act_id=" . $result[$k]['act_id'] . "&channel_id=" .$result[$k]['channel_id'] . "&channel_detal=" .urlencode($result[$k]['channel_detal']) . "&channel_alias=" . urlencode($result[$k]['channel_alias']);
                $result[$k]['channel_url'] = C('ACT_URL') .$result[$k]['act_id'] . ".html?channel_id=" . $result[$k]['channel_id'] . "&channel_detal=" . urlencode($result[$k]['channel_detal']) . "&channel_alias=" . urlencode($result[$k]['channel_alias'])."&channel_remarks=".urlencode($result[$k]['channel_remarks']);
            }
        }
        if (self::$ActMessageChannelModel->addAll($result)) {
            $this->success('保存成功', '', TRUE);
        }
        $this->error('保存失败', '', TRUE);
    }

    //删除
    public function delChannelMessage() {
        $id = I('post.id', false, 'int');
        if ($id) {
            $row = self::$ActMessageChannelModel->getOneData(array('id' => $id), $field = "channel_qrcode");
            $file = ltrim(strrchr($row['channel_qrcode'], '/'), '/');
            //先清空物理路径的图片
            unlink('./Public/Uploads/QR/' . $file);
            if (FALSE !== self::$ActMessageChannelModel->delData(array('id' => $id))) {
                $this->success('删除成功', '', TRUE);
            }
        }
        $this->error('删除失败', '', TRUE);
    }

    //修改
    public function editChannelMessage() {
        EditDataLogic::checkHost();
        $data = I('post.');
        $keys = array_keys($data);
        $result = array();
        foreach ($keys as $key) {
            foreach ($data[$key] as $k => $value) {
                $result[$k][$key] = $value;
               // $result[$k]['channel_url'] = C('ACT_URL') .$result[$k]['act_id'] . ".html?channel_id=" . $result[$k]['channel_id'] . "&channel_detal=" . $result[$k]['channel_detal'] . "&channel_alias=" . $result[$k]['channel_alias'];
                $result[$k]['channel_url'] = C('ACT_URL') .$result[$k]['act_id'] . ".html?channel_id=" . $result[$k]['channel_id'] . "&channel_detal=" . urlencode($result[$k]['channel_detal']) . "&channel_alias=" . urlencode($result[$k]['channel_alias'])."&channel_remarks=".urlencode($result[$k]['channel_remarks']);
            }
        }
        foreach ($result as $k => $v) {
            $row = self::$ActMessageChannelModel->saveAllData(array('id' => $result[$k]['id']), $v);
        }
        if ($row !== false) {
            $this->success('修改成功', '', TRUE);
        }
        $thsi->error('修改失败', '', TRUE);
    }

    //查看二维码和url
    public function show() {
        $id = I('get.act_id', false, 'int');
        if (!$id) {
            return false;
        }
        //获取全部的渠道
        $channelData = EditDataLogic::getAllData('Channel');
        $data = self::$ActMessageChannelModel->getMessageData($id);
        $user = session('realname');
        $this->assign(array(
            'data' => $data,
            'user' => $user,
            'channelData' => $channelData,
        ));
        $this->display();
    }

    //批量导出
    public function exportAllData() {
        $id = I('get.id', false, 'int');
        $data = self::$ActMessageChannelModel->getMessageData($id);
        self::$ActMessageChannelModel->export_exel($data);
    }

    //我推广的活动
    public function myActMessage() {
        $realname = session('realname');
        //获取全部的渠道
        $channelData = EditDataLogic::getAllData('Channel');
        //查询全部的活动
        $act = self::$ActmessageModel->getAllData(array('act_charge_name' => $realname));
        $this->assign(array(
            'username' => $realname,
            'act' => $act,
            'channelData' => $channelData,
        ));
        $this->display();
    }

    //搜索条件导出
    public function exportExel() {
        $actId = I('get.act_id', false, 'int');
        $status = I('get.act_current_status', false, 'int');
        if ($actId) {
            $where['a.act_id'] = array('eq', $actId);
        }
        if ($status) {
            $where['b.act_current_status'] = array('eq', $status);
        }
        $where['b.act_charge_name'] = array('eq', session('realname'));
        $data = self::$ActMessageChannelModel->numData($where);
        self::$ActMessageChannelModel->export_exel($data);
    }

    public function ajax_return() {
		$page = I('get.p',1,'int');
        $actId = I('post.act_id', false, 'int');
        $status = I('post.act_current_status', false, 'int');
        if ($actId) {
            $where['a.act_id'] = array('eq', $actId);
        }
        if ($status) {
            $where['b.act_current_status'] = array('eq', $status);
        }
        $where['b.act_charge_name'] = array('eq', session('realname'));
        $count = self::$ActMessageChannelModel->getCount($where);
        $page_size = 15;
        $Page = new AjaxPage($count, $page_size);
		//  搜索条件下 分页赋值
        foreach ($where as $key => $val) {
            $Page->parameter[$key] = urlencode($val);
        }
	
        $show = $Page->show();
        $data = self::$ActMessageChannelModel->numData($where, $Page->firstRow . ',' . $Page->listRows);
        $this->assign('data', $data);
        $this->assign('page', $show); // 赋值分页输出
		$this->assign('thispage',($page-1)*$page_size);
        $this->display();
    }

    //我的二维码页面
    public function myQRcode() {
		$page = I('get.p',1,'int');
        $data = self::$ActMessageChannelModel->getMyData($page);
        $this->assign(array(
            'data' => $data['data'],
            'page' => $data['page'],
			'thispage'=>$data['thispage'],
        ));
        $this->display();
    }

    //生成二维码
    public function createQRcode() {
        $url = I('post.url', false, 'htmlspecialchars');
        $id = I('post.id', false, 'int');
        //二维码的保存路径
        $path = './Public/Uploads/QR/';
        $QR = $path . $id . 'qrcode.png';
        vendor('phpqrcode.phpqrcode');
        \QRcode::png($url, $QR, QR_ECLEVEL_L, 7, 1);
        $data['channel_qrcode'] = $QR;
        if (FALSE !== self::$ActMessageChannelModel->saveAllData(array('id' => $id), $data)) {
            $this->success('生成二维码成功', '', TRUE);
        }
        $this->error('生成二维码失败', '', TRUE);
    }

    //查看二维码
    public function showQRcode() {
        $id = I('post.id', false, 'int');
        //查询二维码图片路径
        $data = self::$ActMessageChannelModel->getOneData(array('id' => $id), $field = "channel_qrcode");
        $file = ltrim($data['channel_qrcode'], '.');
        $this->success('', $file, TRUE);
    }

    //二维码下载
    public function downFile() {
        $id = I('get.id', false, 'int');
        //获取图片的名称
        $data = self::$ActMessageChannelModel->getOneData(array('id' => $id), $field = "channel_qrcode");
        $file = ltrim(strrchr($data['channel_qrcode'], '/'), '/');
        $data = down_file($file, '/Public/Uploads/QR/');
        exit();
    }

    //删除二维码
    public function delQRcode() {
        $id = I('post.id', false, 'int');
        $row = self::$ActMessageChannelModel->getOneData(array('id' => $id), $field = "channel_qrcode");
        $file = ltrim(strrchr($row['channel_qrcode'], '/'), '/');
        $data['channel_qrcode'] = '';
        //先清空物理路径的图片
        unlink('./Public/Uploads/QR/' . $file);
        if (FALSE !== self::$ActMessageChannelModel->saveAllData(array('id' => $id), $data)) {
            $this->success('删除二维码成功', '', TRUE);
        }
        $this->error('删除二维码失败', '', TRUE);
    }

    //活动数据
    public function actData() {
        $actId = I('get.act_id', false, 'int');
        $data = $this->actCommonData($actId);
        $this->assign(array(
            'data' => $data,

        ));
        $this->display();
    }

    //查看报名表
    public function actEnter() {
        $actId = I('get.act_id', false, 'int');
        $data = $this->actCommonData($actId);
        //先活动活动报名表的版本
        $version = self::$ActmessageModel->getOneData(array('act_id' => $actId),'act_version');
        //获取报名信息
        $count = D('UserBm')->where(array('act_id' => $actId,'version'=>$version['act_version']))->count();
        $page_size = 15;
		$Page = new Page($count,$page_size);
        $Page->setConfig('first','首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last','尾页');
        $show = $Page->show();
        $studentsData = D('UserBm')->where(array('act_id' => $actId,'version'=>$version['act_version']))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign(array(
            'data' => $data,
            'studentsData' => $studentsData,
            'page' => $show,
        ));
        $this->display();
    }

    //导出报名表
    public function exportStudentExel() {
        $actId = I('get.act_id', false, 'int');
        $version = self::$ActmessageModel->getOneData(array('act_id' => $actId),'act_version,act_name');
        $studentsData = D('UserBm')->where(array('act_id' => $actId,'version'=>$version['act_version']))->select();
        foreach($studentsData as $k=>$v){
            $values = explode(',',$v['values']);
            $arr2[] = $values;
            foreach($values as $k1=>$v1){
                $arr = explode('-',$v1);
                $title = D('FormData')->where(array('id'=>$arr[0],'act_id'=>$actId))->field('Title')->find();
                $info['title'][] = ($title['Title']);
            }
        }
        //取出表单的其他值
        foreach($arr2 as $k => &$v){
            foreach ($v as $key => &$value) {
                $studentsData[$k][] = substr(strrchr($value, '-'), 1);
            }
        }
        $info['title'] = array_unique($info['title']);
        $strTable = '<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">序号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="150px">姓名</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">手机号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">邮箱</td>';
        //取出其他标题
        foreach($info['title'] as $key=>$value){
              $strTable .= '<td style="text-align:center;font-size:12px;" width="*">'.$value.'</td>';
        }
        $strTable .= '</tr>';
        foreach ($studentsData as $k => $val) {
            $strTable .= '<tr>';
            $strTable .= '<td style="text-align:center;font-size:12px;">' . ++$kkk . '</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;">' . $val['user_name'] . ' </td>';
            $strTable .= '<td style="text-align:center;font-size:12px;">' . $val['phone'] . ' </td>';
            $strTable .= '<td style="text-align:center;font-size:12px;">' . $val['email'] . '</td>';
            for($a=0;$a<sizeof($arr2[0]);$a++){
                $strTable .= '<td style="text-align:center;font-size:12px;">' . $val[$a]. '</td>';
            }
            $strTable .= '</tr>';
        }


        $strTable .='</table>';
        downloadExcel($strTable, '活动('.$version['act_name'].')报名表');
        exit();
    }

    //查看活动来源
    public function actSource() {
        $actId = I('get.act_id', false, 'int');
        $data = $this->actCommonData($actId);
        $where['a.act_id'] = array('eq',$actId);
        $where['a.channel_id'] = array('neq',0);
        $count = D('PageNum')->getNum($where);
        $Page = new  NewPage($count,10);
        $show = $Page->fpage(array(3,4, 5, 6,7));
        $sourceData = D('PageNum')->getAlldata($where, $Page->limit);
        //2016-10-26修改 
        foreach($sourceData as $k=>$v){
            $sourceData[$k]['num'] = D('PageNum')->getCount(array('channel_id'=>$v['channel_id']));
            $sourceData[$k]['zhanbi'] = round($sourceData[$k]['num']/$data['num'],4)*100;
        }
        $this->assign(array(
            'data' => $data,
            'sourceData' => $sourceData,
            'page' => $show
        ))->display();
    }


    //获取公共的数据
    public function actCommonData($actId) {
        $data = self::$ActmessageModel->getOneData(array('act_id' => $actId));
        //拼凑时间
        $data['act_date'] = date('m月d日', $data['act_start_date']) . '-' . date('d日', $data['act_end_date']);
        //获取浏览量
        $data['num'] = D('PageNum')->where(array('act_id'=>$actId))->count();
        //获取报名量
        $data['count'] = D('UserBm')->where(array('act_id'=>$actId))->count();
        //获取收藏量

         $data['collectCount'] = D('UserCollect')->where(array('act_id'=>$actId))->count();

        return $data;
    }
    


    public function adChannel(){
        //获取全部的渠道
        $channelData = EditDataLogic::getAllData('Channel');
        $actId = I('get.act_id',false,'int');

        $this->assign(array(
            'actId' => $actId,
            'channelData' => $channelData,
        ))->display();
    }


    //导出活动来源信息
    public function get_exel(){
        $actId = I('get.act_id', false, 'int');
        $data = $this->actCommonData($actId);
        $where['a.act_id'] = array('eq',$actId);
        $where['a.channel_id'] = array('neq',0);
        $sourceData = D('PageNum')->getAlldata($where);
        foreach($sourceData as $k=>$v){
            $sourceData[$k]['num'] = D('PageNum')->getCount(array('channel_id'=>$v['channel_id']));
            $sourceData[$k]['zhanbi'] = round($sourceData[$k]['num']/$data['num'],4)*100;
        }
        D('PageNum')->export_exel($sourceData);
    }

    //获取图表统计
    public function ajaxGetTongJi(){
        //获取当前活动的浏览量
         $actId = I('post.actId',false,'int');
         $type = I('post.type',false,'int');
        //页面一加载就请求
         if($actId && $type == 1){
             //活动当前时间
             $time = time();
             $lastMonthTime = time()-30*24*3600;
             //获取近一个月的活动访问量
             $where['time'] = array('egt',("$lastMonthTime 00:00:00"));
             $where['time'] = array('elt',("$time 23:59:59"));
             $where['act_id'] = array('eq',$actId);
             $count = D('PageNum')->getCount($where);
             //获取近一个月的报名量
             $bmCount = D('UserBm')->where($where)->count();
             //活动报名率
             $probability = round($bmCount/$count,4)*100;
             exit(json_encode(array('bmCount'=>$bmCount,'count'=>$count,'probability'=>$probability,'startTime'=>date('Y-m-d',$lastMonthTime),'endTime'=>date('Y-m-d',time()),'head'=>'最近一个月')));
         }
         //点击单选按钮
        if($actId && $type == 2){
            $time = time();
            $lastWeekTime = time()-7*24*3600;
            //获取近一周的访问量
            $where['time'] = array('egt',("$lastWeekTime 00:00:00"));
            $where['time'] = array('elt',("$time 23:59:59"));
            $where['act_id'] = array('eq',$actId);
            $count = D('PageNum')->getCount($where);
            //获取近一周的报名量
            $bmCount = D('UserBm')->where($where)->count();
            //活动报名率
            $probability = round($bmCount/$count,4)*100;
            exit(json_encode(array('bmCount'=>$bmCount,'count'=>$count,'probability'=>$probability,'startTime'=>date('Y-m-d',$lastWeekTime),'endTime'=>date('Y-m-d',time()),'head'=>'近一周')));
        }
        if($actId && $type == 3){
            $time = time();
            $lastTime = time()-3*30*24*3600;
            //获取近三个月的访问量
            $where['time'] = array('egt',("$lastTime 00:00:00"));
            $where['time'] = array('elt',("$time 23:59:59"));
            $where['act_id'] = array('eq',$actId);
            $count = D('PageNum')->getCount($where);
            //获取近三个月的报名量
            $bmCount = D('UserBm')->where($where)->count();
            //活动报名率
            $probability = round($bmCount/$count,4)*100;
            exit(json_encode(array('bmCount'=>$bmCount,'count'=>$count,'probability'=>$probability,'startTime'=>date('Y-m-d',$lastTime),'endTime'=>date('Y-m-d',time()),'head'=>'最近三个月')));
        }
        if($actId && $type == 4){
             $time1 = I('post.time1');
             $time2 = I('post.time2');
             if($time1 && $time2)
                 $where['time'] = array('between', array(strtotime("$time1 00:00:00"), strtotime("$time2 23:59:59")));
             elseif($time1)
                 $where['time'] = array('egt',array(strtotime("$time 00:00:00")));
             elseif($time2)
                 $where['time'] = array('elt',array(strtotime("$time 23:59:59")));
            $where['act_id'] = array('eq',$actId);
            $count = D('PageNum')->getCount($where);
            //获取报名量
            $bmCount = D('UserBm')->where($where)->count();
            //活动报名率
            $probability = round($bmCount/$count,4)*100;
            exit(json_encode(array('bmCount'=>$bmCount,'count'=>$count,'probability'=>$probability,'startTime'=>$time1,'endTime'=>$time2,'head'=>$time1.'到'.$time2)));
        }
    }



    /**
     * 获取浏览信息
     */

    public function getLookFormData(){
        $actId = I('post.act_id',false,'int');
        $version = I('post.version',0,'int');
        //获取表单的信息数据
        $data = D('FormData')->getAllData(array('act_id'=>$actId,'Version'=>$version));
        if($data){
            $html = D('FormData')->getFormHtml($data);
            $lookHtml = D('FormData')->getLookFormHtml($data);
            exit(json_encode(array('status'=>1,'msg'=>$lookHtml)));
        }
        exit(json_encode(array('status'=>-1,'msg'=>'')));
    }
}
