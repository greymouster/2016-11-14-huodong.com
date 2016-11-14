<?php

namespace Admin\Model;
use Think\Model;

class FormDataModel extends Model{
    
    protected  $tableName = "form_data";
    
    //获取全部数据
    public  function  getAllData($where){
        return $this->where($where)->select();
    }

    public  function deleData($where){
        return $this->where($where)->delete();
    }

    public function getFormHtml($data){
          $html = "";
          foreach($data as $k=>$v){
              $html .= "<dl>";
              $html .= "<input type='hidden' name='items[$k][Type]' value='$v[Type]' />";
              if($v['Required']=='true'){
                  $html .= "<dt><input type='checkbox' name='items[$k][Required]' value='$v[Required]'  checked='checked'>必填</dt>";
              }else{
                  $html .= "<dt><input type='checkbox' name='items[$k][Required]' value='$v[Required]' >必填</dt>";
              }
              $html .= "<dd class='name-input'><input   name='items[$k][Title]' value='$v[Title]' disabled='true'></dd>";
              $html .= "<dd class='info'><input type='text' name='items[$k][Description]' class='form-control' value='$v[Description]' placeholder='提示信息写在这里！' disabled='true'/></dd>";
              $html .= "<dd class='remove'><img src='/Public/Admin/images/u39.jpg' onclick='RemoveItems(this);return false;'></dd>";
              if($v['Type'] == 'radio' || $v['Type'] == 'checkbox' || $v['Type'] == 'select'){
                  $html .= "<div class='add'>";
                  $html .= "<p>选项列表<img class='img-ad' src='/Public/Admin/images/add.png'/></p>";
                      if($v['Subitems']){
                          $Subitems = explode(',',$v['Subitems']);
                          //var_dump($Subitems);die;
                          foreach($Subitems as $k1=>$v1){
                              $html .= "<span ><input type='text' name='items[$k][Subitems][$k1]' value='$v1' disabled='true'>";
                              $html .= "<img class='img-de' src='/Public/Admin/images/delete.png' onclick='addSubitemsValue(this);return false;'> </span>";
                          }
                      }
                  $html .="</div>";
              }
              $html .= "</dl>";
          }

          return $html;
    }


    //获取预览效果的html
    public function getLookFormHtml($data){
        $html = "";
        foreach($data as $k=>$v){
            if($v['Type'] == 'input'){
                $html .= '<div class="control-group" style="margin-bottom:22px;">';
                $html .= '<label class="control-label" style="font-weight:bold;">'.$v["Title"].'</label>';
                $html .= '<div class="controls"><input class="input-xxlarge"  type="text" placeholder="'.$v["Description"].'"></div></div>';
            }elseif($v['Type']=='textarea'){
                $html .= '<div class="control-group" style="margin-bottom:22px;">';
                $html .= '<label class="control-label" style="font-weight:bold;">'.$v["Title"].'</label>';
                $html .= '<div class="controls"><textarea rows="10" cols="20" placeholder ="'.$v["Description"].'"></textarea>';
                $html .= '</div></div>';
            }elseif($v['Type']=='radio' || $v['Type']=='checkbox'){
                if($v['Subitems'] !=null && sizeof($v['Subitems'])>0){
                    $html .= '<div class="control-group controls-radio" style="margin-bottom:22px;">';
                    $html .= '<label class="control-label" style="font-weight:bold;">'.$v["Title"].'</label>';
                    $html .= '<div class="controls"></div></div>';
                    $Subitems = explode(',',$v['Subitems']);
                    foreach($Subitems as $k1=>$v1){
                        $html .= '<span><input type="'.$v['Type'].'"/>&nbsp'.$v1.'</span>';
                    }
                }
            }elseif($v['Type'] == 'select'){
                if($v['Subitems'] != null && sizeof($v['Subitems'])>0){
                    $html .= '<div class="control-group controls-radio" style="margin-bottom:22px;">';
                    $html .= '<label class="control-label" style="font-weight:bold;">'.$v['Title'].'</label>';
                    $html .= '<div class="controls">';
                    $html .= '<select><option>请选择</option>';
                    $Subitems = explode(',',$v['Subitems']);
                    foreach($Subitems as $k1=>$v1){
                        $html .= '<option value="">'.$v1.'</option>';
                    }
                    $html .= '</select></div></div>';
                }
            }
        }
        return $html;
    }
}