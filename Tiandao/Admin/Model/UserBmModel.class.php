<?php

namespace Admin\Model;
use Think\Model;
use Think\NewPage;
class UserBmModel extends Model{
    public function addData($data=null){
        return $this->add($data);
    }

    public function getOneData($where=null){
        return $this->where($where)->find();
    }
    //根据搜索条件获取分页
    public function searchData($actname = null, $st = null, $et = null) {
        $where = array();
        if ($actname) {
            $where['act_name'] = array('eq', $actname);
        }
        if ($st) {
            //$where['act_start_date'] = array('egt', strtotime("$startData 00:00:00"));
            $start = strtotime("$st 00:00:00");
            $end = strtotime("$st 23:59:59");
            $where['act_start_date'] = array('between', array($start, $end));
        }elseif ($et) {
            //$where['act_end_date'] = array('elt', strtotime("$endData 23:59:59"));
            $start = strtotime("$et 00:00:00");
            $end = strtotime("$et 23:59:59");
            $where['act_end_date'] = array('between', array($start,$end));
        }elseif($st && $et){
            $where['act_start_date'] = array('egt',strtotime("$st 00:00:00"));
            $where['act_end_date'] = array('elt',strtotime("$et 23:59:59"));
        }
        $count = $this->getCount($where);
        $Page = new  NewPage($count,5);
        $show = $Page->fpage(array(3,4, 5, 6,7));
        $data = $this->getAllData($where,$Page->limit);
        return array('data' => $data, 'page' => $show);
    }

    public function getAllData($where=null,$limit=null){
        return $this->alias('a')->join('LEFT JOIN __ACTMESSAGE__  b ON a.act_id = b.act_id ')->field($field)->order('id DESC')->where($where)->limit($limit)->select();
    }

    public function getCount($where){
        return $this->alias('a')->join('LEFT JOIN __ACTMESSAGE__  b ON a.act_id = b.act_id ')->where($where)->count();
    }
    //更新字段值
    public function saveField($where = null, $field = null, $value = null) {
        return $this->where($where)->setField($field, $value);
    }

    public function actData($where=null,$field=null){
        return $this->alias('a')->field('a.*,b.*,c.*,d.* ,GROUP_CONCAT(e.cat_name) cat_name')->join('JOIN __ACTMESSAGE__ b ON a.act_id = b.act_id  JOIN __PLACE__ c ON b.place_id = c.id JOIN  __ACT_CAT__ d ON  a.act_id = d.act_id  JOIN __CATEGORY__ e ON e.id = d.cat_id' )->group('b.act_id')->where($where)->select();
    }

}