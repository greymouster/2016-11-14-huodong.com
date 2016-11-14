<?php

/**
 * 接口控制器
 */

namespace Admin\Controller;

use Think\Controller;
use Admin\Logic\EditDataLogic;

class InterfaceUriController extends Controller {

    private $adModel;
    private $actModel;
    private $placeModel;
    private $cateModel;
    private $formDataModel;
    public function __construct() {
        if (empty($this->adModel)) {
            $this->adModel = D('Ad');
        }
        if (empty($this->actModel)) {
            $this->actModel = D('Actmessage');
        }
        if (empty($this->placeModel)) {
            $this->placeModel = D('Place');
        }
        if (empty($this->cateModel)) {
            $this->cateModel = D('Category');
        }
        if (empty($this->formDataModel)) {
            $this->formDataModel = D('FormData');
        }
    }

    /**
     * @param type 请求的类型
     * @param limit 截取的条数
     * @return json数据  广告类型数据
     */
    public function getAdData() {
        $type = I('post.type', false, 'int');
        $limit = I('post.limit', false, 'int');
        $keyword = I('post.keyword');
        $order = "sort_number DESC,ad_id Asc";
        if (!IS_POST) {
            exit(json_encode(array('stauts' => 0, 'flag' => '请求参数有误')));
        }
        if($keyword){
            $adName = $this->adModel->getAllData(array('ad_name_sort' =>$keyword),'ad_name');
            exit(json_encode(array('status'=>1,'flag'=>'请求成功','data'=>$adName)));
        }
        $info = $this->adModel->getAllData(array('media_type' => $type), 'ad_pic,ad_link,ad_name', $order, $limit);
        if ($info) {
            exit(json_encode(array('status' => 1, 'flag' => '请求成功', 'data' => $info)));
        } else {
            exit(json_encode(array('status' => 0, 'flag' => '请求失败', 'data' => $this->adModel->getError())));
        }
    }

    /**
     * 直接调用此方法返回城市的所有数据
     */
    public function getCity() {
        $city = $this->placeModel->select();
        if ($city) {
            exit(json_encode(array('status' => 1, 'falg' => '请求成功', 'data' => $city)));
        }
        exit(json_encode(array('status' => 0, 'falg' => '请求失败', 'data' => $this->placeModel->getError())));
    }

    /**
     * 获取类型
     */
    public function getCateData() {
        $cate = $this->cateModel->select();
        if ($cate) {
            exit(json_encode(array('status' => 1, 'falg' => '请求成功', 'data' => $cate)));
        }
        exit(json_encode(array('status' => 0, 'falg' => '请求失败', 'data' => $this->cateModel->getError())));
    }

    /**
     * 根据条件获取活动列表信息
     */
    public function getActList() {
        $catName = I('post.catName', false, 'htmlspecialchars');
        $city = I('post.city',false,'htmlspecialchars');
        $limit = I('post.limit',false,'int');
        $time = I('post.time',false,'htmlspecialchars');
        $actId = I('post.act_id',false,'int');
        $sortData = I('post.sort',false,'htmlspecialchars');
        if($catName || $city || $time || $actId || $sortData ){
            //根据类型简称 获取类型全称
            $catName = $this->cateModel->where(array('abb_short'=>$catName))->field('cat_name,id')->find();
            //根据地点简称 获取地点全称
            $placeName = $this->placeModel->where(array('abb_short'=>$city))->field('place_name')->find();
            $data = $this->actModel->getHomeDataList($placeName['place_name'],$catName['id'],$time,$limit,$actId,$sortData);
        }else{
            $where['a.act_current_status'] = array('eq',4);
            $where['a.act_end_date'] = array('egt',time());
            $where['a.act_date']  = array('elt',time());
            $data = $this->actModel->numData($where,$limit=10);
        }
        if ($data) {
            exit(json_encode(array('status' => 1, 'falg' => '请求成功', 'data' =>$data)));
        }
        exit(json_encode(array('status' => 0, 'falg' => '请求失败', 'data' => $this->actModel->getError())));
    }

    /**
     * 获取表单数据
     */
    public function getFormData(){
        $actId = I('post.actId',false,'int');
        if(!$actId){
            exit(json_encode(array('status'=>0,'falg'=>'请求参数有误')));
        }
        //根据活动活动表单的版本号
        $act = $this->actModel->where(array('act_id'=>$actId))->field('act_version')->find();
        $data = $this->formDataModel->where(array("act_id"=>$actId,'Version'=>$act['act_version']))->select();
        if ($data) {
            exit(json_encode(array('status' => 1, 'falg' => '请求成功', 'data' =>$data,'act_version'=>$act['act_version'])));
        }
        exit(json_encode(array('status' => 0, 'falg' => '请求失败')));
    }

    /**
     * 获取收藏活动接口
     */
    public function userActData(){
        $userId = I('post.user_id',false,'int');
        $result = D('UserCollect')->getAllData(array('user_id'=>$userId),'act_id');
        foreach($result as $k=>$v){
            $actIds[] = $v['act_id'];
        }
        $actIds = implode(',',$actIds);
        if($actIds){
            $where['act_id'] = array('in',$actIds);
            $data = $this->actModel->getAllData($where,'act_name,act_id,act_file,act_end_date');
            exit(json_encode(array('status'=>1,'falg'=>'请求成功','data'=>$data)));
        }
        exit(json_encode(array('status'=>0,'falg'=>'请求失败')));
    }

    /**
     * 获取已结束的活动接口
     */
     public function getEndData(){
         $userId = I('post.user_id',false,'int');
         $result = D('UserCollect')->getAllData(array('user_id'=>$userId),'act_id');
         foreach($result as $k=>$v){
             $actIds[] = $v['act_id'];
         }
         $actIds = implode(',',$actIds);
         if($actIds){
             $where['act_id'] = array('in',$actIds);
             $where['act_end_date'] = array('elt',time());
             $data = $this->actModel->numData($where);
             exit(json_encode(array('status'=>1,'falg'=>'请求成功','data'=>$data)));
         }
         exit(json_encode(array('status'=>0,'falg'=>'请求失败')));
     }

     /**
      * 报名
      */
     public function addBmData(){
         if(IS_POST){
             $data = $_POST;
             $where['act_id'] = array('eq',$data['act_id']);
             $where['user_id'] = array('eq',$data['user_id']);
             if(D('UserBm')->getOneData($where)){
                 exit(json_encode(array('status'=>-1,'msg'=>'请勿重复报名')));
             }

             //根据活动id 获取当前活动是否需要审核
             $info = $this->actModel->where(array('act_id'=>$data['act_id']))->find();
             if($info['act_status'] == 0){
                 $data['status'] = 1;
             }else{
                 $data['status'] = 0;
             }

             if(D('UserBm')->addData($data)){
                 exit(json_encode(array('status'=>1,'msg'=>'报名成功,等待审核')));
             }
                 exit(json_encode(array('status'=>-1,'msg'=>'报名失败请重新报名')));
         }
     }

     /**
      * 获取报名成功并且审核通过的用户
      */
      public function getUnderwayData(){
            $userId = I('post.user_id',false,'int');
            $actId = I('post.act_id',false,'int');
            if($userId){
                $where['a.user_id'] = array('eq',$userId);
                $where['a.status'] = array('eq', 1);
                $where['act_end_date'] = array('egt',time());
                $where['act_current_status'] = array('eq',4);
            }
            if($actId){
                $where['a.act_id'] = array('eq',$actId);
                $where['act_current_status'] = array('eq',4);
            }
            $data = D('UserBm')->actData($where);
            if($data){
                exit(json_encode(array('status'=>1,'data'=>$data)));
            }
            exit(json_encode(array('status'=>-1,'data'=>$data)));
      }

      /**
       * 获取待确认的
       */

      public function getConfirmData(){
          $userId = I('post.user_id',false,'int');
          $where['a.user_id'] = array('eq',$userId);
          $where['a.status'] = array('eq',0);
          $data = D('UserBm') -> getAllData($where);
          if($data){
              exit(json_encode(array('status'=>1,'data'=>$data)));
          }
          exit(json_encode(array('status'=>-1,'data'=>$data)));
      }

      /**
       * 添加收藏
       */
     public function actCollect(){
            if(!IS_POST){
                exit(json_encode(array('status'=>-1,'msg'=>'请求参数有误','result'=>'')));
            }
            $data['user_id'] = I('post.user_id',false,'int');
            $data['act_id'] = I('post.act_id',false,'int');
            if($data['user_id'] && $data['act_id'] ){
                if(D('UserCollect')->addData($data)) {
                    exit(json_encode(array('status' => 1, 'msg' => '收藏成功', 'result' => '')));
                }
                exit(json_encode(array('status'=>-1,'msg'=>'收藏失败','result'=>'')));
            }
     }

    /**
     * 获取是否收藏
     */
     public function getOneCollect(){
           if(!IS_POST){
               exit(json_encode(array('status'=>-1,'msg'=>'请求参数有误','result'=>'')));
           }
           $userId = I('post.user_id',false,'int');
           $actId = I('post.act_id',false,'int');
           $where['user_id'] = array('eq',$userId);
           $where['act_id'] = array('eq',$actId);
           if($userId  && $actId){
               if($result = D('UserCollect')->getOneData($where)) {
                   exit(json_encode(array('status' => 1, 'msg' => '成功', 'result' =>$result)));
               }
           }
           exit(json_encode(array('status'=>-1,'msg'=>'失败','result'=>'')));
     }
     /**
      * 获取全部的收藏活动
      */
     public function getAllCollect(){
           if(!IS_POST){
               exit(json_encode(array('status'=>-1,'msg'=>'请求参数有误','result'=>'')));
           }
           $userId = I('post.user_id',false,'int');
           if($userId){
               if($result = D('UserCollect')->getAllData(array('user_id'=>$userId),'act_id')){
                   exit(json_encode(array('status'=>1,'msg'=>'成功','result'=>$result)));
               }
           }
         exit(json_encode(array('status'=>-1,'msg'=>'失败','result'=>'')));
     }
    /**
     * 取消收藏
     */
     public function delCollect(){
         if(!IS_POST){
             exit(json_encode(array('status'=>-1,'msg'=>'请求参数有误','result'=>'')));
         }
         $actId = I('post.act_id',false,'int');
         $userId = I('post.user_id',false,'int');
         if($userId && $actId){
             if(FALSE !== D('UserCollect')->delData(array('act_id'=>$actId,'user_id'=>$userId))){
                  exit(json_encode(array('status'=>1,'msg'=>'成功')));
             }
         }
         exit(json_encode(array('status'=>-1,'msg'=>'失败')));
     }
     /**
      * 获取是否报名
      */

     public function getBmData(){
         if(!IS_POST){
             exit(json_encode(array('status'=>-1,'msg'=>'请求参数有误','result'=>'')));
         }
         $userId = I('post.user_id',false,'int');
         $actId = I('post.act_id',false,'int');
         $where['user_id'] = array('eq',$userId);
         $where['act_id'] = array('eq',$actId);
         if($userId  && $actId){
             if($result = D('UserBm')->getOneData($where)) {
                 exit(json_encode(array('status' => 1, 'msg' => '成功', 'result' =>$result)));
             }
         }
         exit(json_encode(array('status'=>-1,'msg'=>'失败','result'=>'')));
     }

    /**
     * 统计浏览量
     */
     public function clickNum(){
         $data = I('post.');
         M()->startTrans();
         $add = M('PageNum')->add($data);
         if(!$add){
             M()->rollback();
         }
         M()->commit();
     }

     /**
      * 获取是否必填
      */
     public function getRequired(){
         $id = I('post.id',false,'int');
         $data = D('FormData')->where(array('id'=>$id))->field('Required,Title')->find();
         if($data){
             exit(json_encode(array('status'=>1,'result'=>$data)));
         }
         exit(json_encode(array('status'=>-1,'result'=>'')));
     }


     /**
      * 获取全称
      */
     public function getFullName(){
         $catName = I('post.catName');
         $city = I('post.city');
         if($catName || $city){
             $catList = $this->cateModel->getOneData(array('abb_short'=>$catName),'cat_name');
             $city = $this->placeModel->getOneData(array('abb_short'=>$city),'place_name');
             $data['catName'] = $catList['cat_name'];
             $data['city'] = $city['place_name'];
             exit(json_encode(array('status'=>1,'data'=>$data)));
         }
         exit(json_encode(array('status'=>-1,'data'=>'')));
     }


     /**
      * 获取上线和下线及获取的数据
      */
    public function getLineData(){
        $actId = I('post.act_id',false,'int');
        $where['a.act_id'] = array('eq',$actId);
        $where['a.act_current_status'] = array('eq','4');
        $data = $this->actModel->numData($where);
		
        if($data){
            exit(json_encode(array('status'=>1,'data'=>$data)));
        }
        exit(json_encode(array('status'=>-1,'data'=>"sql或接口地址出错")));

    }
}
