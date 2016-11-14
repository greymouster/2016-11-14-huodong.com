<?php

namespace Admin\Controller;

use Think\AjaxPage;
use Admin\Logic\EditDataLogic;

class OnlineController extends BaseController {

    private static $ActmessageModel;

    public function __construct() {
        parent::__construct();
        if (empty(self::$ActmessageModel)) {
            self::$ActmessageModel = D('Actmessage');
        }
    }

    public function index() {
        //根据条件搜索全部的信息活动
        extract(I('get.'));
        $actname = trim($actname);
        $data = self::$ActmessageModel->searchData($actname, $start_date, $end_date, $status, '', $placeid, $cateid, $group_id,2);
        foreach ($data['data'] as $k => $v) {
            $data['data'][$k]['act_start_date'] = date('Y年m月d日', $v['act_start_date']);
            $data['data'][$k]['act_end_date'] = date('m月d日', $v['act_end_date']);
            $data['data'][$k]['act_success_time'] = date('Y-m-d H:i:s', $v['act_success_time']);
            $data['data'][$k]['current_status'] = self::$ActmessageModel->getCurrentStatus($v['act_current_status'],$v['act_date'],$v['act_end_date']);
        }
        //获取分类
        $cateData = EditDataLogic::getAllData('Category');
        //获取地点
        $placeData = EditDataLogic::getAllData('Place');
        //获取分组
        $groupData = EditDataLogic::getAllData('group');
        $this->assign(array(
            'cateData' => $cateData,
            'placeData' => $placeData,
            'groupData' => $groupData,
            'data' => $data['data'],
            'page' => $data['page'],
            'checked' => $checked,
        ));
        $this->display();
    }
	
	//是否有权限审核
    public function checkAct() {
        
    }

    //审核上线
    public function checkOnlie() {
        if (IS_POST && IS_AJAX) {
            $actId = I('post.actId', false, 'int');
            $where = "act_id = $actId";
            $data['act_current_status'] = 4;
			//获取活动信息
            $actData = self::$ActmessageModel->getOneData($where,'act_name');
            if (FALSE !== self::$ActmessageModel->saveData($where, $data)) {
                adminLog("审核活动",$actData['act_name']);
				$this->success('审核通过', '', TRUE);
            }
            $this->error('审核失败请重试', '', TRUE);
        }
    }

    //驳回
    public function reback() {
        if (IS_POST && IS_AJAX) {
            $reason = I('post.reason', false, 'htmlspecialchars');
            $actId = I('post.actId', false, 'int');
            //因为驳回理由为空的时候,页面不能提交 故不在此判断
            $data = array('act_reason' => $reason, 'act_current_status' => 6);
            $where = array('act_id' => $actId);
			//获取活动信息
            $actData = self::$ActmessageModel->getOneData($where,'act_name');
            if (FALSE !== self::$ActmessageModel->saveData($where, $data)) {
                adminLog("驳回活动",$actData['act_name']);
                $this->success('驳回成功', '', TRUE);
            }
            $this->error('驳回失败', '', TRUE);
        }
    }

    //撤销
    public function revokeAudit() {
        if (IS_POST && IS_AJAX) {
            $actId = I('post.actId', false, 'int');
            $data = array('act_current_status' => 2);
            $where = array('act_id' => $actId);
			//获取活动信息
            $actData = self::$ActmessageModel->getOneData($where,'act_name');
            if (FALSE !== self::$ActmessageModel->saveData($where, $data)) {
                adminLog("撤销审核",$actData['act_name']);
				$this->success('撤销成功', '', TRUE);
            }
            $this->error('撤销失败请重试', '', TRUE);
        }
    }

    //批量审核上线
    public function batchOnline() {
        $data = I('post.arr');
        $data = implode(',', $data);
        if (empty($data)) {
            $this->error('操作失败', '', false);
        }
        $where = array('act_id' => array('in', $data));
        $info = self::$ActmessageModel->getAllData($where);
        if (FALSE !== self::$ActmessageModel->outLine($where, 4)) {
            $this->success('操作成功', '', TRUE);
        }
        $this->error('操作失败', '', TRUE);
    }

    //是否有权限置顶
    public function top() {
        //$this->display();
    }
	
    //置顶页面显示   
    public function isTop(){
        $this->display();
    }
	
    public function setTop() {
        if (IS_POST) {
            $actId = I('post.actId', false, 'int');
            $isTop = I('post.isTop', false, 'int');
            $data['act_is_top'] = $isTop ? $isTop : 50;
            if (FALSE !== self::$ActmessageModel->saveTop(array('act_id' => $actId), $data)) {
                //获取活动的名称和置顶的序号
                $actData = self::$ActmessageModel->getOneData(array('act_id'=>$actId),'act_name,act_is_top');
                $act_is_top = $actData['act_is_top'] == 50 ? 0 : $actData['act_is_top'];
                $act_is_top == 0 ? adminLog('取消置顶',$actData['act_name']):adminLog('置顶','将'.$actData['act_name'].'置顶为'.$act_is_top);
				$this->success('操作成功', '', TRUE);
            }
            $this->error('操作失败', '', TRUE);
        }
    }

    //活动数据统计
    public function totalData() {
        $this->display();
    }

    public function ajax_return() {
        $status = I('post.act_current_status', false, 'int');
        $startDate = I('post.start_date', false, 'htmlspecialchars');
        $endDate = I('post.end_date', false, 'htmlspecialchars');
        $where = $this->getWhere($status, $startDate, $endDate);
        $count = self::$ActmessageModel->where($where)->count();
        $page_size = 15;
		$Page = new AjaxPage($count, $page_size);
        //  搜索条件下 分页赋值
        foreach ($where as $key => $val) {
            $Page->parameter[$key] = urlencode($val);
        }
        $show = $Page->show();
        $data = $this->getData($where, $Page->firstRow . ',' . $Page->listRows);
        $this->assign('data', $data);
        $this->assign('show', $show);
        $this->display();
    }

    //导出数据
    public function exportExel() {
        $status = I('get.act_current_status', false, 'int');
        $startDate = I('get.start_date', false, 'htmlspecialchars');
        $endDate = I('get.end_date', false, 'htmlspecialchars');
        $where = $this->getWhere($status, $startDate, $endDate);
        //获取全部的活动
        $data = $this->getData($where);
        $this->export_exel($data);
    }

    //where条件
    public function getWhere($currentStatus, $startDate, $endDate) {

        //发布状态
        if ($currentStatus == 3 ||$currentStatus == 6) {
            $where['act_current_status'] = array('eq',$currentStatus);
        }elseif($currentStatus == 4){
            $where['act_current_status'] = array('eq',$currentStatus);
            $where['act_date'] = array('elt',time());
            $where['act_end_date'] = array('egt',time());
        }elseif($currentStatus == 5){
            $where['act_current_status'] = array('eq',4);
            $where['act_end_date'] = array('elt',time());
        }elseif($currentStatus == 7){
            $where['act_current_status'] = array('eq',4);
            $where['act_date'] = array('egt',time());
        }
        if ($startDate) {
            $where['act_start_date'] = array('egt', strtotime("$startDate 00:00:00"));
        }
        if ($endDate) {
            $where['act_end_date'] = array('elt', strtotime("$endDate 23:59:59"));
        }
        return $where;
    }

    public function getData($where, $limit = null) {
        //获取全部的活动
        $data = self::$ActmessageModel->getAllData($where, null, $limit);
        foreach ($data as $k => $v) {
            $data[$k]['current_status'] = self::$ActmessageModel->getCurrentStatus($v['act_current_status'],$v['act_date'],$v['act_end_date']);
            //获取浏览量
            $data[$k]['num'] = D('PageNum')->where(array('act_id'=>$v['act_id']))->count();
            //获取报名量
            $data[$k]['count'] = D('UserBm')->where(array('act_id'=>$v['act_id']))->count();
        }
        return $data;
    }

    public function export_exel($data) {
        $strTable = '<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:300px;">活动名称</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="300">活动状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">活动访问量</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">渠道报名量</td>';
        $strTable .= '</tr>';

        foreach ($data as $k => $val) {
            $strTable .= '<tr>';
            $strTable .= '<td style="text-align:center;font-size:12px;">' . $val['act_name'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['current_status'] . ' </td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['num'] . ' </td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['count'] . '</td>';
            $strTable .= '</tr>';
        }
        $strTable .='</table>';
        downloadExcel($strTable, 'act-data');
        exit();
    }

}
