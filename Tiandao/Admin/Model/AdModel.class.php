<?php

namespace Admin\Model;

use Think\Model;

class AdModel extends Model {

    //允许接收的字段
    protected $insertFields = 'ad_name,ad_link,media_type,sort_number,ad_pic,ad_time';
    //允许修改的字段
    protected $updateFields = 'ad_id,ad_name,ad_link,media_type,sort_number,ad_pic,ad_time';
    protected $_validate = array(
        array('ad_name', 'require', '广告名称不能为空', 1),
        array('ad_link', 'require', '广告链接不能为空', 1),
    );

    public function _before_insert(&$data, $option) {
        $data['ad_time'] = time();
        if($data['media_type'] ==2 ){
            $adNameSort = explode('-',$data['ad_link']);
             //var_dump($adNameSort);die;
            //获取广告名称的简称
            $data['ad_name_sort'] = $adNameSort[3];
        }else{
            $data['ad_name_sort'] = "all";
        }
        /*         * *********************上传图片******* */
        if (isset($_FILES['ad_pic']) && $_FILES['ad_pic']['error'] == 0) {
			
			$getImageW_H = getimagesize($_FILES['ad_pic']['tmp_name']);
			if($getImageW_H[0]>720 || $getImageW_H[1]>405){
				$this->error = '请上传图片的尺寸为:720px  X 405px';
				return false;
			}

            $ret = uploadOne('ad_pic', 'Ad', array(
                array(720,720),
            ));
            if ($ret['ok'] == 1) {
                $data['ad_pic'] = getServicePath() . '/Public/Uploads/' . $ret['images'][1];
            } else {
                $this->error = $ret['error'];
                return FALSE;
            }
        }
    }

    protected function _before_update(&$data, $option) {
        $id = I('post.ad_id', false, 'int');
        if($data['media_type'] ==2 ){
            $adNameSort = explode('-',$data['ad_link']);
             //var_dump($adNameSort);die;
            //获取广告名称的简称
            $data['ad_name_sort'] = $adNameSort[3];
        }else{
            $data['ad_name_sort'] = "all";
        }
        /*         * ******************上传图片******* */
        if (isset($_FILES['ad_pic']) && $_FILES['ad_pic']['error'] == 0) {
			
			$getImageW_H = getimagesize($_FILES['ad_pic']['tmp_name']);
			if($getImageW_H[0]>720 || $getImageW_H[1]>405){
				$this->error = '请上传图片的尺寸为:720px  X 405px';
				return false;
			}

            $ret = uploadOne('ad_pic', 'Ad', array(
                array(720, 720),
            ));
            if ($ret['ok'] == 1) {
                $data['ad_pic'] = getServicePath() . '/Public/Uploads/' . $ret['images'][1];
            } else {
                $this->error = $ret['error'];
                return FALSE;
            }

           /* $pic = $this->field('ad_pic')->find($id);
            if ($pic) {
                $str = strrpos($pic['ad_pic'], 'A');
                $imgSub = substr($pic['ad_pic'], $str);
                $img = str_replace('thumb_0_', '', $imgSub);
                unlink('./Public/Uploads/' . $img);
                unlink('./Public/Uploads/' . $imgSub);
            }*/
        }else{
			$pic = $this->field('ad_pic')->find($id);
			if($pic){
				$data['ad_pic'] = $pic['ad_pic'];
			}
		}
        

    }

    protected function _before_delete($option) {
        $id = I('post.adId', false, 'int');
        $pic = $this->field('ad_pic')->find($id);
        if ($pic) {
            $str = strrpos($pic['ad_pic'], 'A');
            $imgSub = substr($pic['ad_pic'], $str);
            $img = str_replace('thumb_0_', '', $imgSub);
            unlink('./Public/Uploads/' . $img);
            unlink('./Public/Uploads/' . $imgSub);
        }
    }

    //删除数据
    public function deleteData($where = null) {
        return $this->where($where)->delete();
    }

    //获取数据
    public function getAllData($where = NULL, $field = NULL,$order=NULL,$limit=NUll) {
        return $this->where($where)->field($field)->order($order)->limit($limit)->select();
    }

}
