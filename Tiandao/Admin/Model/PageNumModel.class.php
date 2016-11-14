<?php

namespace  Admin\Model;
use Think\Model;

class  PageNumModel extends Model{
    public function getAllData($where,$limit){
        return   $this->alias('a') -> join('LEFT JOIN __MESSAGE_CHANNEL__  b  ON a.channel_id = b.channel_id
                    LEFT JOIN __ACTMESSAGE__ c ON a.act_id = c.act_id LEFT JOIN __CHANNEL__ d ON a.channel_id = d.id ')
                  ->group('a.channel_id')->field('a.*,b.*,c.act_name,d.channel_name')->where($where)->limit($limit)->select();
    }

    public function getCount($where){
        return  $this->where($where)->count();

    }

    public function getNum($where){
        return  $this->alias('a') -> join('LEFT JOIN __MESSAGE_CHANNEL__  b  ON a.channel_id = b.channel_id
                    LEFT JOIN __ACTMESSAGE__ c ON a.act_id = c.act_id LEFT JOIN __CHANNEL__ d ON a.channel_id = d.id ')
            ->field('a.*,b.*,c.act_name,d.channel_name')->where($where)->count('distinct a.channel_id');
    }

    public function export_exel($data) {
        $strTable = '<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">活动名称</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">渠道</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">渠道名称</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">渠道唯一标识</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">备注信息</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">来源个数</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">来源占比</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">来源链接</td>';
        $strTable .= '</tr>';

        foreach ($data as $k => $val) {
            $strTable .= '<tr>';
            $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;' . $val['act_name'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['channel_name'] . ' </td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['channel_alias'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['channel_detal'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['channel_remarks'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['num'] . ' </td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['zhanbi'] .'%'. '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['channel_url'] . '</td>';
            $strTable .= '</tr>';
        }
        $strTable .='</table>';
        downloadExcel($strTable, '渠道来源表');
        exit();
    }
}