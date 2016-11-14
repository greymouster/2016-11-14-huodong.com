<?php

namespace Admin\Model;

use Think\Model;
use Think\Page;

class ActMessageChannelModel extends Model {

    protected $tableName = 'message_channel';

    //获取活动推广信息
    public function getAllData($where = null) {
        return $this->where($where)->select();
    }

    //删除数据
    public function delData($where = null) {
        return $this->where($where)->delete();
    }

    //修改全部数据
    public function saveAllData($where = null, $data = null) {
        return $this->where($where)->data($data)->save();
    }

    //获取一条数据信息
    public function getOneData($where = null, $field = null) {
        return $this->where($where)->field($field)->find();
    }

    //获取全部信息
    public function getMessageData($id) {
        $where = array();
        $channelId = I('get.channel_id', false, 'int');
        //渠道
        if ($channelId) {
            $where['channel_id'] = array('eq', $channelId);
        }
        //活动名称
        if ($id) {
            $where['a.act_id'] = array('eq', $id);
        }
        return $this->numData($where);
    }

    //获取总的条数
    public function getCount($where) {
        return $this->alias('a')->join('LEFT JOIN __ACTMESSAGE__ b on a.act_id=b.act_id LEFT JOIN __CHANNEL__ c  on c.id = a.channel_id')->where($where)->count();
    }

    //获取总的记录数
    public function numData($where, $limit = null) {
        return $this->alias('a')->join('LEFT JOIN __ACTMESSAGE__ b on a.act_id=b.act_id LEFT JOIN __CHANNEL__ c  on c.id = a.channel_id')->field('a.*,b.act_charge_name,b.act_current_status,b.act_name,c.channel_name')->where($where)->limit($limit)->select();
    }

    public function getMyData($page=null) {
        $where['b.act_charge_name'] = session('realname');
		$where['a.channel_qrcode'] = array('neq','');
        $count = $this->getCount($where);
		$page_size = 15;
        $Page = new Page($count, $page_size);
		$Page->setConfig('first','首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last','尾页');
		$show = $Page->show();
        $data = $this->numData($where, $Page->firstRow . ',' . $Page->listRows);
        return array('data' => $data, 'page' => $show,'thispage'=>($page-1)*$page_size);
    }

    public function export_exel($data) {
        $strTable = '<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">编号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">所属活动</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">渠道</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">渠道名称</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">渠道具体信息</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">备注信息</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">推广的URL</td>';
        $strTable .= '</tr>';

        foreach ($data as $k => $val) {
            $strTable .= '<tr>';
            $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;' . $val['id'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['act_name'] . ' </td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['channel_name'] . ' </td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['channel_alias'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['channel_detal'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['channel_remarks'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['channel_url'] . '</td>';
            $strTable .= '</tr>';
        }
        $strTable .='</table>';
        downloadExcel($strTable, 'act');
        exit();
    }

}
