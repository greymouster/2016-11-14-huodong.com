<?php

namespace Admin\Model;

use Think\Model;
use Think\NewPage;

class ActmessageModel extends Model {

    // 定义表单验证的规则
    protected $_validate = array(
        array('act_name', 'require', '活动名称不能为空', 1),
        array('spec_address', 'require', '具体地点不能为空', 1),
        array('act_start_date','require','活动开始时间不能为空',1),
        array('act_end_date','require','活动结束时间不能为空',1),
        array('act_cost', 'require', '活动经费不能为空', 1),
        array('act_cost', 'currency', '活动经费必须是货币类型', 1),
    );

    //前置
    public function _before_insert(&$data, $option) {
        if($data['act_online'] == 2 && !$data['act_date'] && !$data['act_time']){
            $this->error = '请选择定时上线时间';
            return false;
        }
        /*** *****上传图片******* */
		
        if (isset($_FILES['act_file']) && $_FILES['act_file']['error'] == 0) {
			
			$getImageW_H = getimagesize($_FILES['act_file']['tmp_name']);
			if($getImageW_H[0]>720 || $getImageW_H[1]>405){
				$this->error = '请上传图片的尺寸为:720px  X 405px';
				return false;
			}

            $ret = uploadOne('act_file', 'Activity', array(
                array(720,405),
            ));
            if ($ret['ok'] == 1) {
                $data['act_file'] = getServicePath(). '/Public/Uploads/' . $ret['images'][1];
            } else {
                $this->error = $ret['error'];
                return FALSE;
            }
        }
    }

    public function _before_update(&$data, $option) {
        if($data['act_online'] == 2 && !$data['act_date'] && !$data['act_time']){
            $this->error = '请选择定时上线时间';
            return false;
        }
        $id = I('post.act_id', false, 'int');
        /** ******上传图片******* */
        if (isset($_FILES['act_file']) && $_FILES['act_file']['error'] == 0) {
			
			$getImageW_H = getimagesize($_FILES['act_file']['tmp_name']);
			if($getImageW_H[0]>720 || $getImageW_H[1]>405){
				$this->error = '请上传图片的尺寸为:720px  X 405px';
				return false;
			}

            $ret = uploadOne('act_file', 'Activity', array(
                array(720,405),
            ));
            if ($ret['ok'] == 1) {
                $data['act_file'] = getServicePath() . '/Public/Uploads/' . $ret['images'][1];
            } else {
                $this->error = $ret['error'];
                return FALSE;
            }
        }else{
			$pic = $this->where(array('act_id' => $id))->field('act_file')->find();
			if($pic){
				$data['act_file'] = $pic['act_file'];
			}
		}
        

    }

    //根据后台条件获取活动
    public function searchData($actname = '', $st = null, $et = null, $currentStatus = null, $realname = null, $placeid = null, $cateid = null, $group_id = null,$act_pub_status=null) {
        $where = array();
        //分组
        if ($group_id) {
            $where['d.group_id'] = array('eq', $group_id);
        }
        //用户名
        if ($realname) {
            $where['a.act_charge_name'] = array('eq', $realname);
        }
        //发布状态
        if ($currentStatus==1 || $currentStatus == 2 || $currentStatus == 3 ||$currentStatus == 6) {
            $where['a.act_current_status'] = array('eq',$currentStatus);
        }elseif($currentStatus == 4){
            $where['a.act_current_status'] = array('eq',$currentStatus);
            $where['a.act_date'] = array('elt',time());
            $where['a.act_end_date'] = array('egt',time());
        }elseif($currentStatus == 5){
            $where['a.act_current_status'] = array('eq',4);
            $where['a.act_end_date'] = array('elt',time());
        }elseif($currentStatus == 7){
            $where['a.act_current_status'] = array('eq',4);
            $where['a.act_date'] = array('egt',time());
        }
        //活动名称
        if ($actname) {
            $where['a.act_name'] = array('like', "%$actname%");
        }
        //根据时间
        if ($st) {
            $start = strtotime("$st 00:00:00");
            $end = strtotime("$st 23:59:59");
            $where['a.act_start_date'] = array('between', array($start,$end));
        } elseif ($et) {
            $start = strtotime("$et 00:00:00");
            $end = strtotime("$et 23:59:59");
            $where['a.act_end_date'] = array('between', array($start,$end));
        }elseif($st && $et){
            $where['a.act_start_date'] = array('egt',strtotime("$st 00:00:00"));
            $where['a.act_end_date'] = array('elt',strtotime("$et 23:59:59"));
        }
        //根据地点
        if ($placeid) {
            $placeid = implode(',', $placeid);
            $where['a.place_id'] = array('in', $placeid);
        }
        //根据类型
        if ($cateid) {
            $cateid = implode(',', $cateid);
            //查询出分类下的活动id
            $actCatModel = D('act_cat');
            $act_ids = $actCatModel->field('GROUP_CONCAT(DISTINCT act_id SEPARATOR ",") act_id')->where(array(
                           'cat_id' => array('in',$cateid),
                        ))->find();
            $where['a.act_id']  = array('in', $act_ids['act_id']);
        }
        if($act_pub_status){
            $where['a.act_pub_status'] = array('eq',$act_pub_status);
         }
        //返回搜索数据
        return $this->getSearchData($where);
    }
    
    //根据前台搜索条件获取前台数据
    public function getHomeDataList($placeName=null,$catId=null,$time=null,$limit=null,$actId=null,$sortData=null){
         if($placeName){
             $where['b.place_name'] = array('eq',$placeName);
         } 
         if($catId){
            $actCatModel = D('act_cat');
            $act_ids = $actCatModel->field('GROUP_CONCAT(act_id  SEPARATOR ",")  act_id ')->where(array(
                           'cat_id' => array('eq',$catId),
                        ))->find();
            $where['a.act_id'] = array('in',$act_ids['act_id']);
         }
         if($time){
             $start = strtotime("$time 00:00:00");
             $end = strtotime("$time 23:59:59");
             $where['a.act_start_date'] = array('between', array($start,$end));
         }

         if($actId){
             $where['a.act_id']  = array('eq',$actId);
          }
          //默认排序
          if($sortData && $sortData =='mrpx'){
              $order = "a.act_is_top ASC,a.act_id ASC";
          }
          if($sortData && $sortData == "jgcddg"){
              $order = "a.act_cost ASC";
          }
          if($sortData && $sortData == "jgcgdd"){
              $order = "a.act_cost DESC";
          }
          if($sortData && $sortData == 'zxsx'){
              $order = "a.act_id DESC";
          }
          //最近开始的活动
          if($sortData && $sortData == 'zjks'){
              $where['a.act_start_date'] = array('egt',time());
              $order = "a.act_id ASC";
          }
         //上线的
         $where['a.act_current_status'] = array('eq',4);
         $where['a.act_end_date'] = array('egt',time());
         $where['a.act_date']  = array('elt',time());
         return $this->numData($where,$limit,$order);
    }

    //获取搜索的信息
    public function getSearchData($where) {
        $count = $this->getCount($where);
		$page_size = 6;
        $Page = new NewPage($count, $page_size);
        $show = $Page->fpage(array(3,4, 5, 6,7));
        $data = $this->numData($where, $Page->limit);
        return array('data' => $data, 'page' => $show);
    }

    //获取总的条数
    public function getCount($where) {
       return $this->alias('a')->where($where)->count();
    }

    //获取总的数据
    public function numData($where=NULL, $limit = NULL,$order='a.act_is_top ASC,a.act_id DESC') {
        
        return $this->alias('a')->field('a.* ,b.place_name,b.id as place_id,c.*,GROUP_CONCAT(e.cat_name SEPARATOR ",") cat_name')->join('LEFT JOIN __PLACE__ b ON b.id = a.place_id  LEFT JOIN __ADMIN__ c ON c.realname = a.act_charge_name LEFT JOIN __ACT_CAT__ d ON d.act_id = a.act_id LEFT JOIN __CATEGORY__ e ON e.id = d.cat_id')->where($where)->limit($limit)->order($order)->group('a.act_id')->select();
	}
    //获取所有的活动
    public function getAllData($where = null, $field = null, $limit = null) {
        return $this->where($where)->limit($limit)->field($field)->select();
    }

    //获取一条活动信息
    public function getOneData($where = null, $field = null) {
        return $this->where($where)->field($field)->find();
    }

    //删除活动信息
    public function deleteData($where = null) {
        return $this->where($where)->delete();
    }

    //修改数据
    public function saveData($where = null, $data = null) {
        return $this->where($where)->save($data);
    }

    //活动下线
    public function outLine($where = null, $value = null) {
        return $this->where($where)->setField('act_current_status', $value);
    }

    //置顶
    public function saveTop($where, $data) {
        return $this->where($where)->setField($data);
    }

    //获取当前的状态
    public function getCurrentStatus($data,$act_date,$end_date) {
        switch ($data) {
            case 1:
                return '未发布';
                break;
            case 2:
                return '待审核';
                break;
            case 3:
                return '已下线';
                break;
            case 4:
                if($act_date <= time() && $end_date >= time()){
                    return '已上线';
                }elseif($end_date <= time()){
                    return '已结束';
                }else{
                   return '审核通过';
                }

                break;
            case 5:
                return '已结束';
                break;
            case 6:
                return '驳回';
                break;
            default:
                break;
        }
    }

    public function setData($data){
        $data['act_current_status']  = 1;
        $data['act_success_time']    = time();
        $data['act_start_date']  = strtotime($data['act_start_date'].$data['act_start_time']);
        $data['act_end_date']  =  strtotime($data['act_end_date'].$data['act_end_time']);
        if (!empty($data['act_date']) && !empty($data['act_time'])) {
            $data['act_date'] = strtotime($data['act_date'].$data['act_time']);
        }
        unset($data['act_start_time']);
        unset($data['act_end_time']);
        unset($data['act_time']);
        return $data;
    }

    //添加和修改前的数据处理
    public function makeData($data) {
        if (!empty($data['act_week'])) {
            $data['act_week'] = implode(',', $data['act_week']);
        }

        if (!empty($data['act_date']) && !empty($data['act_time'])) {
            $data['act_date'] = strtotime($data['act_date']);
            $data['act_time'] = strtotime($data['act_time']);
        }
        $data['act_success_time'] = time();
        return $data;
    }
	
    //添加之后
    protected function _after_insert($data,$option){
         $cat_ids = I("post.cat_id");
         if($cat_ids){
             $actCatModel = D('act_cat');
             foreach($cat_ids as $v){
                $actCatModel->add(array(
                    'act_id' => $data['act_id'],
                    'cat_id' => $v
                ));
             }
         }
    }
     
	//删除之前
    protected function _before_delete($option){
        $actId = I('get.actId',false,'int');

        if($actId){
            $actCatModel = D('act_cat');
            //删除活动前先删除活动下的分类
            $actCatModel->where(array(
                 'act_id' => array('eq',$actId),
            ))->delete();
            //删除对应的表单的
            /*D('FormData')->where(array(
                'act_id' => array('eq',$actId),
            ))->delete();*/
        }
    } 
}
