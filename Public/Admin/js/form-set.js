var formCommonItems = [
{
    "Key":"I_-1",
    "Sort":-1,
    "Type":"input",
    "Category":"FIELD_COMPANY",
    "IsDefault":false,
    "Required":false,
    "Multiple":false,
    "Title":"城市",
    "Subitems":[],
    "Description":null,
    "IsHide":false,
    "Value":null,
    "TypeTitle":"单行文本框"
},{
    "Key":"I_-1",
    "Sort":-1,
    "Type":"input",
    "Category":"FIELD_COMPANY",
    "IsDefault":false,
    "Required":false,
    "Multiple":false,
    "Title":"QQ",
    "Subitems":[],
    "Description":null,
    "IsHide":false,
    "Value":null,
    "TypeTitle":"单行文本框"
},{
    "Key":"I_-1",
    "Sort":-1,
    "Type":"input",
    "Category":"FIELD_COMPANY",
    "IsDefault":false,
    "Required":false,
    "Multiple":false,
    "Title":"微信",
    "Subitems":[],
    "Description":null,
    "IsHide":false,
    "Value":null,
    "TypeTitle":"单行文本框"
}];

var formEmptyItems = [{
    "Key":"I_0",
    "Sort":0,
    "Type":"input",
    "Category":"CUSTOM",
    "IsDefault":false,
    "Required":false,
    "Multiple":false,"Title":"",
    "Subitems":[],
    "Description":null,
    "IsHide":false,
    "Value":null,
    "TypeTitle":"单行文本框"
},{
    "Key":"I_0",
    "Sort":0,
    "Type":"textarea",
    "Category":"CUSTOM",
    "IsDefault":false,
    "Required":false,
    "Multiple":false,
    "Title":"",
    "Subitems":[],
    "Description":null,
    "IsHide":false,
    "Value":null,
    "TypeTitle":"多行文本框"
},
    {
        "Key":"I_0",
        "Sort":0,
        "Type":"radio",
        "Category":"CUSTOM",
        "IsDefault":false,
        "Required":false,
        "Multiple":false,
        "Title":"",
        "Subitems":[],
        "Description":null,
        "IsHide":false,
        "Value":null,
        "TypeTitle":"单选按钮框"
    },{
        "Key":"I_0",
        "Sort":0,
        "Type":"checkbox",
        "Category":"CUSTOM",
        "IsDefault":false,
        "Required":false,
        "Multiple":false,
        "Title":"",
        "Subitems":[],
        "Description":null,
        "IsHide":false,
        "Value":null,
        "TypeTitle":"多选按钮框"
    },{
        "Key":"I_0",
        "Sort":0,
        "Type":"select",
        "Category":"CUSTOM",
        "IsDefault":false,
        "Required":false,
        "Multiple":false,
        "Title":"",
        "Subitems":[],
        "Description":null,
        "IsHide":false,
        "Value":null,
        "TypeTitle":"下拉选择框"
    }]
//定义存储formItemsJosn的数组
var formItemsJson = new Array();
//点击城市,微信,QQ的事件
function addEventFormCommonItem(index){
    //index 为0-2
    //判断定义好的json数组
    if(formCommonItems !=null && formCommonItems.length>0 && index>=0){
        //获取json中的数组值
        var commonItem = formCommonItems[index];
        if(commonItem !=null){
            formItemsJson.push(createTemplateFormItem(commonItem));
            renderEventFormTemplate();
        }
    }
}
//重新赋值
function createTemplateFormItem(item){
    if(item == null) {
        return null;
    }
    var sortTmp = parseInt($("#template_form_sort_max").val());
    sortTmp++;
    var result = {
        Id : "I_" + sortTmp,
        Sort : sortTmp,
        Type : item.Type,
        Category : "CUSTOM",
        IsDefault : item.IsDefault,
        Required : false,
        Multiple : item.Multiple,
        Title : item.Title,
        Description : item.Description,
        IsHide : item.IsHide,
        TypeTitle : item.TypeTitle,
        Subitems : new Array()
    };
    if(item.Subitems != null && item.Subitems.length > 0){
        for(var i = 0; i<item.Subitems.length; i++){
            result.Subitems.push(item.Subitems[i]);
        }
    }
    $("#template_form_sort_max").val(sortTmp);
    return result;
}
var count = 0;
//添加form表单
function renderEventFormTemplate(){
    count ++;
    if( count >10){
        alert("当前表单最多能设置10项");return false;
    }
    var itemsHtml = "";
    if(formItemsJson != null && formItemsJson.length > 0){
        for(i=0;i<formItemsJson.length;i++){
            var tmpItem = formItemsJson[i];
            var title= tmpItem.Title == "" ? tmpItem.TypeTitle : tmpItem.Title
            itemsHtml = "";
            itemsHtml += '<dl id="efi_'+i+'">';
            itemsHtml += '<input type="hidden" name="' + i + '[Type]" value="' + tmpItem.Type + '" />';
            itemsHtml += '<input type="hidden" name="' + i + '[Sort]" value="' + tmpItem.Sort + '" />';
            itemsHtml += '<dt><input type="checkbox" name="' + i + '[Required]" value="true" ' + (tmpItem.Required ? 'checked="true"' : '') + ' onchange="javascript:onChangeFormItemValue(0, this, ' + i + ', 0);">必填</dt>';
            itemsHtml += '<dd class="name-input"><input title="' + title + '" placeholder="' + title + '" name="' + i + '[Title]" value="' + (tmpItem.Title == null ? "" : tmpItem.Title.replace("\"", "\\\"").replace("\n", " ")) + '" onchange="javascript:onChangeFormItemValue(1, this, ' + i + ', 0);"></dd>';
            if(tmpItem.Type == "input" || tmpItem.Type == "textarea"){
				itemsHtml += '<dd class="info"><input type="text" name="' + i + '[Description]" class="form-control" value="' + (tmpItem.Description == null ? "" : tmpItem.Description.replace("\"", "\\\"").replace("\n", " ")) + '" onchange="javascript:onChangeFormItemValue(2, this, ' + i + ', 0);" placeholder="提示信息写在这里！"/></dd>';
			}
            itemsHtml += '<dd class="remove"><img src="/Public/Admin/images/u39.jpg" onclick="javascript:removeEventFormItem(' + i + ',this);return false;"></dd>';
            if (tmpItem.Type == "radio" || tmpItem.Type == "checkbox" || tmpItem.Type == "select") {
                itemsHtml += '<div class="add">';
                itemsHtml += renderEventFormItemValues(i, tmpItem);
                itemsHtml += '</div></dl>';
            }
        }

        $(".form-set .set-left .usual-form").append(itemsHtml);
    }
}

function onChangeFormItemValue(type, itemObj, index, subIndex) {
    if (formItemsJson != null && formItemsJson.length > index && index >= 0) {
        var eleItem = $(itemObj);
        if (type == 0)formItemsJson[index].Required = eleItem.prop("checked"); //必填项
        else if (type == 1)formItemsJson[index].Title = eleItem.val();
        else if (type == 2) formItemsJson[index].Description = eleItem.val();
        else if (type == 3) {
            if (formItemsJson[index].Subitems != null && formItemsJson[index].Subitems.length > subIndex && subIndex >= 0) {
                formItemsJson[index].Subitems[subIndex] = eleItem.val();
            }
        }
    }
}
//移除
function removeEventFormItem(index, _this) {
    if (formItemsJson != null && formItemsJson.length > index && index >= 0) {
        formItemsJson.splice(index, 1);
    }
    $(_this).parents('dl').remove();
    formItemsJsonTemp = formItemsJson.slice()

}
//添加自定义的节点
function addEventFormEmptyItem(index) {
    if (formEmptyItems != null && formEmptyItems.length > index && index >= 0) {
        var emptyItem = formEmptyItems[index];
        if (emptyItem != null) {
            formItemsJson.push(createTemplateFormItem(emptyItem));
            renderEventFormTemplate();
        }
        formItemsJsonTemp = formItemsJson.slice();
    }
}

//添加文本框及图片
function renderEventFormItemValues(i, tmpItem) {
    itemsHtml = ''
    itemsHtml += '<p>选项列表<img class="img-ad" src="/Public/Admin/images/add.png" onclick="javascript:addEventFormNode(' + i + ',this);return false;"/></p>'
    if (tmpItem.Subitems != null && tmpItem.Subitems.length > 0) {
        for (var j = 0; j < tmpItem.Subitems.length; j++) {
            itemsHtml += '<span ><input type="text" name="' + i + '[Subitems][' + j + ']" value="' + (tmpItem.Subitems[j] == null ? "" : tmpItem.Subitems[j].replace("\"", "\\\"").replace("\n", " ")) + '" onchange = "javascript:onChangeFormItemValue(3, this, ' + i + ', ' + j + ');" >';
            itemsHtml += '<img class="img-de" src="/Public/Admin/images/delete.png" onclick="javascript:removeEventFormItemValue(' + i + ',' + j + ',this);"> </span>';
        }
    }
    return itemsHtml;
}
//点击图片添加node节点
function addEventFormNode(index,_this) {
    if (formItemsJson != null && formItemsJson.length > index && index >= 0) {
        if (formItemsJson[index].Subitems == null)
            formItemsJson[index].Subitems = new Array();
        formItemsJson[index].Subitems.push("");
        var efis = $(_this).parent().parent();
        if (efis != null) {
            efis.empty();
            efis.append(renderEventFormItemValues(index, formItemsJson[index]));
        }
    }
}
//删除
function removeEventFormItemValue(index, subIndex,_this) {
    if (formItemsJson != null && formItemsJson.length > index && index >= 0 && subIndex >= 0) {
        var tmpItem = formItemsJson[index];
        if (tmpItem.Subitems != null && tmpItem.Subitems.length > subIndex) {
            formItemsJson[index].Subitems.splice(subIndex, 1);
            var efis = $(_this).parent().parent();
            if (efis != null) {
                efis.empty();
                efis.append(renderEventFormItemValues(index, formItemsJson[index]));
            }
        }
    }
}


$(function(){
     $('.form-set .set-left .set-bottom a.look').click(function(){
         var act_id = $(this).data('id');
         var version = $(this).data('version');
         var htmlTmp = resolveEventFormView(formItemsJson, 0);
         $.ajax({
             url : '/index.php/Admin/Activity/getLookFormData',
             data :{act_id:act_id,version:version},
             type :'POST',
             dataType : 'json',
             success:function(data){
                  if(data.status >0){
                      $('body').addClass('modal-open');
                      $('.modal').show();
                      $('.modal fieldset').empty().append(data.msg);
                      if(htmlTmp){
                          $('.modal fieldset').append(htmlTmp);
                      }
                  }else{

                      $('body').addClass('modal-open');
                      $('.modal').show();
                      $('.modal fieldset').append(htmlTmp);
                  }
             }
         });

     })
     
    function resolveEventFormView(items,index){
        var htmlTmp = "";
        if (items != null && items.length > 0) {
            var i = 0;
            var flagAddi = false;
            for (iii = 0; iii < items.length; iii++) {
                var tmpItem = items[iii];
                if(tmpItem.Type == 'input'){
                    htmlTmp += '<div class="control-group" style="margin-bottom:22px;">';
                    htmlTmp += '<label class="control-label" style="font-weight:bold;">'+tmpItem.Title+'</label>';
                    htmlTmp +='<div class="controls"><input class="input-xxlarge"  type="text" placeholder="'+(tmpItem.Description == null ? "" : tmpItem.Description.replace("\"", "\\\"").replace("\n", " ")) +'"></div></div>';
                }else if(tmpItem.Type =='textarea'){
                    htmlTmp += '<div class="control-group" style="margin-bottom:22px;">';
                    htmlTmp +='<label class="control-label" style="font-weight:bold;">'+tmpItem.Title+'</label>';
                    htmlTmp +='<div class="controls"><textarea rows="10" cols="20" placeholder ="'+(tmpItem.Description == null ? "" : tmpItem.Description.replace("\"", "\\\"").replace("\n", " "))+'"></textarea>';
                    htmlTmp +='</div></div>';
                }else if(tmpItem.Type =='radio' || tmpItem.Type =='checkbox'){
                    if (tmpItem.Subitems != null && tmpItem.Subitems.length > 0) {
                        htmlTmp +='<div class="control-group controls-radio" style="margin-bottom:22px;">';
                        htmlTmp +='<label class="control-label" style="font-weight:bold;">'+tmpItem.Title+'</label>';
                        htmlTmp +='<div class="controls"></div></div>';
                        for(var j = 0 ; j<tmpItem.Subitems.length;j++){
                            htmlTmp +='<span><input type="'+ tmpItem.Type + '" value="' + tmpItem.Subitems[j].replace("\"", "\\\"").replace("\n", " ") + '" />&nbsp'+tmpItem.Subitems[j]+'</span>';
                        }
                    }
                }else if(tmpItem.Type =="select"){
                     if (tmpItem.Subitems != null && tmpItem.Subitems.length > 0) {
                        htmlTmp +='<div class="control-group controls-radio" style="margin-bottom:22px;">';
                        htmlTmp +='<label class="control-label" style="font-weight:bold;">'+tmpItem.Title+'</label>';
                        htmlTmp +='<div class="controls">';
                        htmlTmp +='<select name="items[' + index + '][' + i + '].Value">';
                        htmlTmp +='<option>请选择</option>';
                        for(var j=0; j<tmpItem.Subitems.length;j++){
                             htmlTmp +='<option value="' + tmpItem.Subitems[j].replace("\"", "\\\"").replace("\n", " ") + '">' + tmpItem.Subitems[j] +'</option>"';    
                        }
                        htmlTmp += '</select></div></div>';
                     }
                }
            }
        }
        return htmlTmp;
    }
        /*关闭弹框*/
    $('.modal button.close').click(function(){
        $('body').removeClass('modal-open');
        $('.modal .modal-body fieldset .control-group').remove();
        $('.modal').hide();
    })
    $('.modal .modal-footer a.btn-create-default').click(function(){
        $('body').removeClass('modal-open');
        $('.modal .modal-body fieldset .control-group').remove();
        $('.modal').hide();
    })
});
/******************************修改表单的js
//删除当前一行的选项
function RemoveItems(_this){
    $(_this).parents('dl').remove();
}
function addSubitemsValue(_this){
    var $input='<span><input type="text" name="radio"><img class="img-de" src="images/delete.png"> </span>'
    $(this).parent().parent().append($input);
}
//删除当前多选的值
function RemoveSubitemsValue(_this){
    $(_this).parent().empty();
}
 ********************************/