$(function () {
    /****************************公告的ajax*********************/
    //新增公告
    $(".acement-submit").click(function () {
        var title = $("input[name='title']").val();
        var content = $("textarea[name='content']").val();
        $.ajax({
            url: "/index.php/Admin/Notice/ajaxAddNotice",
            data: {title: title, content: content},
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status == -1) {
                    layer.alert(data.msg, {icon: 5});
                }
                if (data.status == 1) {
                    layer.msg(data.msg, {icon: 6});
                    setTimeout(function () {
                        window.location.href = "/index.php/Admin/Notice/index";
                    }, 1500);
                }
            }
        });
    });
    //修改公告
    $(".acement-edit").click(function () {
        var title = $("input[name='title']").val();
        var content = $("textarea[name='content']").val();
        var id = $("input[name='id']").val();
        $.ajax({
            url: "/index.php/Admin/Notice/ajaxEditNotice",
            data: {title: title, content: content, id: id},
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status == -1) {
                    layer.alert(data.msg, {icon: 5});
                }
                if (data.status == 1) {
                    layer.msg(data.msg, {icon: 6});
                    setTimeout(function () {
                        window.location.href = "/index.php/Admin/Notice/index";
                    }, 1500);
                }
            }
        });
    });
    //删除
    $(".acement-delete").click(function () {
        layer.confirm('确认删除？', {
            btn: ['确定', '取消'], //按钮
            offset: '200px',
        }, function () {
            // 确定
            var id = $("input[name='id']").val();
            $.ajax({
                url: "/index.php/Admin/Notice/ajaxDeleteNotice",
                data: {id: id},
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if (data.status == -1) {
                        layer.alert(data.msg, {icon: 5,offset:'200px'});
                    }
                    if (data.status == 1) {
                        layer.msg(data.msg, {icon: 6,offset:'200px'});
                        setTimeout(function () {
                            window.location.href = "/index.php/Admin/Notice/index";
                        }, 1500);
                    }
                }
            });
        }, function (index) {
            layer.close(index);
            return false;// 取消
        }
        );
    });
    //取消
    $(".acement-cancel").click(function () {
            window.location.href = "/index.php/Admin/Notice/index";
    })
    /****************************分类的ajax*********************/
    $(".act-sort .cate-add").click(function () {
        layer.prompt({
            title: '新增分类',
            formType: 0 , //prompt风格，支持0-2
        }, function (val) {
            $.ajax({
                url: "/index.php/Admin/System/ajaxAdd",
                data: {val: val, flag: 'category'},
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        layer.msg('新增分类成功', {icon: 6,offset:'200px'});
                        setTimeout(function () {
                            window.location.href = window.location.href;
                        }, 1500);
                    }else{
                        layer.alert(data.msg, {icon: 5,offset:'200px'});
                    }
                }
            });
        });
    })
    //升序
    $(".cate-sort-asc").click(function () {
        var sortNumber = $(this).attr('sort-number');
        var prevSortNumber = $(this).parent().parent().prev().find(".cate-sort-asc").attr('sort-number');
        if (prevSortNumber == undefined) {
            layer.alert('排序已经置顶', {icon: 5,offset:'200px'});
            return false;
        }
        $.ajax({
            url: "/index.php/Admin/System/ajaxAsc",
            data: {sortNumber: sortNumber, prevSortNumber: prevSortNumber, flag: 'category'},
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status == 1) {
                    layer.msg('更新排序成功', {icon: 6,offset:'200px'});
                    setTimeout(function () {
                        window.location.href = window.location.href;
                    }, 1500);
                } else {
                    layer.msg('更新排序失败', {icon: 5,offset:'200px'});
                }
            }
        });
    });
    //降序
    $(".cate-sort-desc").bind('click', function () {
        var sortNumber = $(this).attr('sort-number');
        var prevSortNumber = $(this).parent().parent().next().find(".cate-sort-asc").attr('sort-number');
        if (prevSortNumber == undefined) {
            layer.alert('排序已经置底', {icon: 5,offset:'200px'});
            return false;
        }
        $.ajax({
            url: "/index.php/Admin/System/ajaxAsc",
            data: {sortNumber: sortNumber, prevSortNumber: prevSortNumber, flag: 'category'},
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status == 1) {
                    layer.msg('更新排序成功', {icon: 6,offset:'200px'});
                    setTimeout(function () {
                        window.location.href = window.location.href;
                    }, 1500);
                }
            }
        });
    })
    //分类的编辑
    $(".cate-edit").bind('click', function () {
        var id = $(this).data('id');
        var cat_name = $(this).data('name');
        layer.prompt({
            title: '编辑分类',
            value: cat_name,
            formType: 0, //prompt风格，支持0-2
        }, function (val) {
            $.ajax({
                url: "/index.php/Admin/System/ajaxEditCategory",
                data: {catName: val, id: id},
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        layer.msg('编辑分类成功', {icon: 6});
                        setTimeout(function () {
                            window.location.href = window.location.href;
                        }, 1500);
                    }else{
                        layer.alert('编辑分类失败',{icon:5});
                    }
                }
            });
        });
    });

    //分类的删除
    $(".cate-delete").click(function () {
        var id = $(this).data('id');
        layer.confirm('确认删除？', {
            btn: ['确定', '取消'] //按钮
        }, function () {
            $.ajax({
                url: "/index.php/Admin/System/ajaxDelete",
                data: {id: id, flag: 'category'},
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        layer.msg('删除分类成功', {icon: 6});
                        setTimeout(function () {
                            window.location.href = window.location.href;
                        }, 1500);
                    }else{
                        layer.alert("删除分类失败",{icon:5});
                    }
                }
            });
        }, function (index) {
            layer.close(index);
            return false;// 取消
        }
        );
    })
    /************************活动地点ajax****************************/
    $(".place-add").click(function () {
        layer.prompt({
            title: '新增活动地点',
            formType: 0 //prompt风格，支持0-2
        }, function (val) {
            $.ajax({
                url: "/index.php/Admin/System/ajaxAdd",
                data: {val: val, flag: 'place'},
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        layer.msg('新增地点成功', {icon: 6});
                        setTimeout(function () {
                            window.location.href = window.location.href;
                        }, 1500);
                    }else{
                        layer.alert("新增地点失败",{icon:5});
                    }
                }
            });
        });
    });

    //升序
    $(".place-sort-asc").click(function () {
        var sortNumber = $(this).attr('sort-number');
        var prevSortNumber = $(this).parent().parent().prev().find(".place-sort-asc").attr('sort-number');
        if (prevSortNumber == undefined) {
            layer.alert('排序已经置顶', {icon: 5});
            return false;
        }
        $.ajax({
            url: "/index.php/Admin/System/ajaxAsc",
            data: {sortNumber: sortNumber, prevSortNumber: prevSortNumber, flag: 'place'},
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status == 1) {
                    layer.msg('更新排序成功', {icon: 6});
                    setTimeout(function () {
                        window.location.href = window.location.href;
                    }, 1500);
                }else{
                    layer.alert("更新排序失败",{icon:5});
                }
            }
        });
    });

    //降序
    $(".place-sort-desc").bind('click', function () {
        var sortNumber = $(this).attr('sort-number');
        var prevSortNumber = $(this).parent().parent().next().find(".place-sort-asc").attr('sort-number');
        if (prevSortNumber == undefined) {
            layer.alert('排序已经置底', {icon: 5});
            return false;
        }
        $.ajax({
            url: "/index.php/Admin/System/ajaxAsc",
            data: {sortNumber: sortNumber, prevSortNumber: prevSortNumber, flag: 'place'},
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status == 1) {
                    layer.msg('更新排序成功', {icon: 6});
                    setTimeout(function () {
                        window.location.href = window.location.href;
                    }, 1500);
                }else{
                    layer.alert("更新排序失败",{icon:5});
                }
            }
        });
    })
    //地点的删除
    $(".place-delete").click(function () {
        var id = $(this).data('id');
        layer.confirm('确认删除？', {
           
            btn: ['确定', '取消'] //按钮
        }, function () {
            $.ajax({
                url: "/index.php/Admin/System/ajaxDelete",
                data: {id: id, flag: 'place'},
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        layer.msg('删除地点成功', {icon: 6});
                        setTimeout(function () {
                            window.location.href = window.location.href;
                        }, 1500);
                    }else{
                        layer.alert("删除地点成功",{icon:5});
                    }
                }
            });
        }, function (index) {
            layer.close(index);
            return false;// 取消
        }
        );
    })
    /*************************发布活动*************************/
    $(".submit-file-desc").click(function () {
        var actId = $(this).attr('data-id');
        if(!actId){
            layer.alert("请先保存活动",{icon:5,offset:'80%'});
            return false;
        }
        window.location.href="/index.php/Admin/Activity/setForm/act_id/"+actId;


       /* $('#myForm').ajaxSubmit({
            type: 'POST',
            url: "/index.php/Admin/Activity/add",
            data:{actId:actId},
            success: function (data) {
                if (data.status == 0) {
                    layer.alert(data.info, {icon: 5,offset:'80%'});
                }
                if (data.status == 1) {
                    layer.msg(data.info, {icon: 6,offset:'80%'});
                    setTimeout(function () {
                        window.location.href = "/index.php/Admin/Activity/setForm/act_id/"+data.url;

                    }, 1500);
                }
            }
        });*/
        return false;
    });
   var wait = 5;
    function timer(o){
        if(wait == 0){
            o.removeAttribute("disabled");
            wait = 5;
            $(".submit-caogao").css('backgroundColor','#0D9DFF');
        }else{
            o.setAttribute("disabled",true);
            wait--;
            $(".submit-caogao").css('backgroundColor','#FF5A0D');
            setTimeout(function(){
                timer(o);
            },1000)
        }
    }

    //保存为草稿箱
    $(".submit-caogao").click(function () {
        timer(this);
		if($(".submit-file-desc").data('id')){
			layer.alert("此活动已保存",{icon:5,offset:'80%'});
			return false;
		}
        if ($("select[name='place_id']").val() == 0) {
            layer.alert("请选择活动地点", {icon: 5,offset:'80%'});
            return false;
        }
        if ($("select[name='cat_id']").val() == 0) {
            layer.alert("请选择活动分类", {icon: 5,offset:'80%'});
            return false;
        }
        $('#myForm').ajaxSubmit({
            type: 'POST',
            url: "/index.php/Admin/Activity/addDraft",
            beforeSubmit: function() {
                layer.msg("数据正在提交中....",{closeBtn:false,offset:'80%',icon:16});
            },
            success: function (data) {
                if (data.status == 0) {
                    layer.alert(data.info, {icon: 5,offset:'80%'});
                }
                if (data.status == 1) {
                  
                    $(".submit-yulan").attr('data-id',data.url);
                    $(".submit-file-desc").attr('data-id',data.url);
                    layer.msg(data.info, {icon: 6,offset:'80%'});
					setTimeout(function(){
						window.location.href = '/index.php/Admin/Activity/editMessage/act_id/'+data.url;
					},1500);
                }
            }
        });
        return false;
    });
    //yulan
    $(".submit-yulan").click(function(){
        var actId = $(this).attr('data-id');
        if(!actId){
            layer.alert('请先保存',{icon:5,offset:'80%'});
            return false;
        }
        layer.open({
            type: 2,
            title: '编辑广告位',
            offset:'50%',
            shadeClose: true,
            shade: 0,
            area: ['640px', '750PX'],
            content: '/index.php/Admin/Activity/actPreview/act_id/'+actId,
        });
    });


    $(".act_push_tuiguang").click(function(){
        var actId = $(this).attr('data-id');
        $.ajax({
            url : "/index.php/Admin/Activity/add",
            data : {act_id:actId},
            type : "POST",
            dataType : 'json',
            success : function(data){
                if(data.status == 1){
                    layer.msg(data.info,{icon:6});
                    setTimeout(function(){
                        window.location.href = '/index.php/Admin/Activity/actPromotion/act_id/'+actId;
                    },1500)
                }else{
                    layer.alert(data.info,{icon:5});
                }
            }
        });
    });
    /***************************推广渠道****************/
    $('.channel-add').click(function () {
        layer.prompt({
            title: '新增推广渠道',
            formType: 0 //prompt风格，支持0-2
        }, function (val) {
            $.ajax({
                url: "/index.php/Admin/System/ajaxAdd",
                data: {val: val, flag: 'channel'},
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        layer.msg('新增渠道成功', {icon: 6});
                        setTimeout(function () {
                            window.location.href = window.location.href;
                        }, 1500);
                    }else{
                        layer.alert("删除渠道失败",{icon:5});
                    }
                }
            });
        });
    });
    //删除
    $(".channel-delete").click(function () {
        var id = $(this).data(id);
        layer.confirm('确认删除？', {
             btn: ['确定', '取消'] //按钮
        }, function () {
            $.ajax({
                url: "/index.php/Admin/System/ajaxDelete",
                data: {id: id, flag: 'channel'},
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        layer.msg('删除渠道成功', {icon: 6});
                        setTimeout(function () {
                            window.location.href = window.location.href;
                        }, 1500);
                    }else{
                        layer.alert("删除渠道失败",{icon:5});
                    }
                }
            });
        }, function (index) {
            layer.close(index);
            return false;// 取消
        }
        );
    });
    /*************************广告**********************/
    $('.ad-add').click(function () {
        $('#adForm').ajaxSubmit({
            type: 'POST',
            url: "/index.php/Admin/Ad/ajaxAdd",
            success: function (data) {
                if (data.status == 1) {
                    //$('#adForm').reset();
                    $("input[type='text']").val('');
                    layer.msg(data.info, {icon: 6});
                    setTimeout(function () {
                        window.location.href = "/index.php/Admin/Ad/adList";
                    }, 1500);
                }else{
                    layer.alert(data.info, {icon: 5});
                }
            }
        });
        return false;
    });
    //删除广告位
    $(".ad-delete").click(function () {
        var adId = $(this).data('id');
        layer.confirm('确认删除？', {
            btn: ['确定', '取消'] //按钮
        }, function () {
            $.ajax({
                url: "/index.php/Admin/Ad/ajaxDelete",
                type: "POST",
                data: {adId: adId},
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        layer.msg(data.info, {icon: 6});
                        setTimeout(function () {
                            window.location.href = window.location.href;
                        }, 1500);
                    }else{
                        layer.alert(data.info, {icon: 5});
                    }
                }
            });
        }, function (index) {
            layer.close(index);
            return false;// 取消
        }
        );
    });
    //编辑广告位
    $(".ad-edit").click(function () {
        var url = $(this).attr('data-url');
        layer.open({
            type: 2,
            title: '编辑广告位',
            shadeClose: true,
            shade: 0,
            area: ['450px', '500px'],
            content: url,
        });
    });

    $(".ad-editAd").click(function () {
        $('#editForm').ajaxSubmit({
            type: 'POST',
            url: "/index.php/Admin/Ad/ajaxEditAd",
            success: function (data) {
                if (data.status == 1) {
                    window.parent.call_back(1,data.info);
                } else {
                    window.parent.call_back(0,data.info);
                }
            }
        });
        return false;
    });
    /*************************活动信息管理***********************/
    $(".act-delete-data").click(function () {
        var actId = $(this).attr('act-id');
        layer.confirm('确认删除？', {
            btn: ['确定', '取消'], //按钮
        }, function () {
            $.ajax({
                url: "/index.php/Admin/Activity/delActivity",
                data: {actId: actId},
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        layer.msg(data.info, {icon: 6});
                        setTimeout(function () {
                            window.location.href = window.location.href;
                        }, 1500);
                    }else{
                        layer.alert(data.info, {icon: 5});
                    }
                }
            });
        }, function (index) {
            layer.close(index);
            return false;// 取消
        });
    });
    //下线
    $(".act-out-line").click(function () {
        var actId = $(this).attr('act-id');
        layer.confirm('确认下线？', {
            btn: ['确定', '取消'],
        }, function () {
            $.ajax({
                url: "/index.php/Admin/Activity/outLine",
                data: {actId: actId},
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        layer.msg(data.info, {icon: 6});
                        setTimeout(function () {
                            window.location.href = window.location.href;
                        }, 1500);
                    }else{
                        layer.alert(data.info, {icon: 5});
                    }
                }
            });
        }, function (index) {
            layer.close(index);
            return false;
        })
    });
    //编辑活动
    $(".edit-file-desc").click(function () {
        $('#editActivity').ajaxSubmit({
            type: 'POST',
            url: "/index.php/Admin/Activity/edit",
            success: function (data) {
                if (data.status == 1) {
                    layer.alert(data.info,{icon:6,offset:'80%'});
                    var actId = $("input[name='act_id']").val();
                    $(".yulan").attr('data-id',actId);
                    $(".submit-file-desc").attr('data-id',actId);
                } else {
                    layer.alert(data.info,{icon:5,offset:'80%'});
					return false;
                }
            }
        });
        return false;
    });
  
    //批量下线
    $('.out-put-line').click(function () {
        //获取状态
        var arr = new Array();
        $("input[name='chkItem']:checked").each(function (event) {
            //获取当前选中的状态值
            var status = $(this).parent().parent().find('.current').attr('current-status');
            //根据状态值获取指定的id		   
            if (status == 4 || status == 3) {
                arr.push($(this).val());
            }
        })
        $.ajax({
            url: "/index.php/Admin/Activity/batchOffline",
            data: {arr: arr},
            type: "POST",
            dataType: "json",
            success: function (data) {
               if (data.status == 1) {
                    layer.msg(data.info, {icon: 6});
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                } else {
                    layer.alert(data.info, {icon: 5});
                }
            }
        });
    });
    /**********************************活动推广***********************/
    //添加
    $(".add-channel-message").click(function () {
        var  flag = false;

        $("form input[name*='channel_alias']").each(function(){
            if($(this).val() == ''){
                layer.alert('请填写渠道具体信息', {icon: 5});
                flag = true;
                return false;
            }
        });
        $("form input[name*='channel_detal']").each(function(){
            if($(this).val() == ''){
                layer.alert('请填写渠道名称', {icon: 5});
                flag = true;
                return false;
            }
        });
        $("form  select[name*='channel_id']").each(function(){
            if($(this).val() == ''){
                layer.alert('请选择渠道', {icon: 5});
                flag = true;
                return false;
            }
        });
        if(flag){
            flag = false;
            return false;
        }
        var data = $("#channelForm").serialize();
        $("#channelForm").ajaxSubmit({
            url: "/index.php/Admin/Activity/addChannelMessage",
            data: data,
            type: "POST",
            dataType: "json",
            beforeSubmit:function(){
                layer.msg("数据正在提交中....",{closeBtn:false,icon: 16});
                $(".add-channel-message").prop('disabled','true');
            },
            success: function (data) {
                if (data.status == 1) {
                    layer.msg(data.info, {icon: 6});
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                } else {
                    layer.alert(data.info, {icon: 5});
                }
            }
        });
        return false;
    });
    //删除
    $(".del-channel-message").click(function () {
        var id = $(this).attr('data-id');
        layer.confirm('确认删除？', {
            btn: ['确定', '取消'],
            offset:'300px'
        }, function () {
            $.ajax({
                url: "/index.php/Admin/Activity/delChannelMessage",
                data: {id: id},
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        layer.msg(data.info, {icon: 6});
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    } else {
                        layer.alert(data.info, {icon: 5});
                    }
                }

            });
        }, function (index) {
            layer.close(index);
            return false;
        })
    });
    //修改
    $(".edit-channel-message").click(function () {
        if ($("input[name='channel_detal[]']").val() == '') {
            layer.alert('请填写渠道具体信息', {icon: 5});
            return false;
        }
        if ($("input[name='channel_alias[]']").val() == '') {
            layer.alert('请填写渠道名称', {icon: 5});
            return false;
        }
        var data = $("#channelForm").serialize();
        $.ajax({
            url: "/index.php/Admin/Activity/editChannelMessage",
            data: data,
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status == 1) {
                    layer.msg(data.info, {icon: 6});
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                } else {
                    layer.alert(data.info, {icon: 5});
                }
            }
        });
        return false;
    });
    /***************************我推广的活动*******************/
    //复制url
    $('.copy-url').click(function () {
        var url = $(this).attr('data-url');
        if (url == "") {
            layer.alert('复制的url为空', {icon: 5});
            return false;
        }
        copyTextToClipboard(url);
    });
    function copyTextToClipboard(url) {
        var textArea = document.createElement("textarea");
        textArea.style.position = 'fixed';
        textArea.style.top = 0;
        textArea.style.left = 0;
        textArea.style.width = '2em';
        textArea.style.height = '2em';
        textArea.style.padding = 0;
        textArea.style.border = 'none';
        textArea.style.outline = 'none';
        textArea.style.boxShadow = 'none';
        textArea.style.background = 'transparent';
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        if (document.execCommand('copy')) {
            layer.alert('您复制的url为:<br/>' + url, {icon: 6});
        } else {
            layer.alert('复制失败', {icon: 5});
        }
        document.body.removeChild(textArea);
    }
    //生成二维码
    $(".create_qrcode").click(function () {
        var url = $(this).attr('data-url');
        var id = $(this).attr('data-id');
        var _this = $(this);
        $.ajax({
            url: "/index.php/Admin/Activity/createQRcode",
            data: {url: url, id: id},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    layer.msg(data.info, {icon: 6,offset:'200px'});
                    _this.css('color','gray');
                } else {
                    layer.alert(data.info, {icon: 5,offset:'200px'});
                }
            }
        });
    });
    //查看二维码
    $('.show-qrcode').click(function () {
        var id = $(this).attr('data-id');
        $.ajax({
            url: '/index.php/Admin/Activity/showQRcode',
            type: 'POST',
            data: {id: id},
            dataType: "json",
            success: function (data) {
                layer.open({
                    type: 1,
                    title: ['请右击复制或另存为图片', 'font-size:16px;'],
                    closeBtn: 2,
                    area: '300px 300px',
                    offset:'200px',
                    skin: 'layui-layer-nobg',
                    shadeClose: true,
                    content: "<img src='" + data.url + "'>"
                });
            }
        });
    });
    //删除二维码
    $('.del-qrcode').click(function () {
        var id = $(this).attr('data-id');
        $.ajax({
            url: '/index.php/Admin/Activity/delQRcode',
            type: 'POST',
            data: {id: id},
            dataType: "json",
            success: function (data) {
                if (data.status == 1) {
                    layer.msg(data.info, {icon: 6});
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                } else {
                    layer.alert(data.info, {icon: 5});
                }
            }
        });
    });
    /********************活动在线审核********************************/
    /*点击审核*/
    $('.check-sh').click(function () {

        var actId = $(this).attr('act-id');
        $.ajax({
            url : '/index.php/Admin/Online/checkAct',
            dataType:'json',
            success : function(data){
                if(data){
                    layer.msg(data.info,{icon:5});
                    return false;
                }else{
                    layer.confirm('是否审核通过', {
                        btn: ['审核通过', '驳回','取消'], //按钮
                        closeBtn:0
                    }, function () {
                        $.ajax({
                            url: '/index.php/Admin/Online/checkOnlie',
                            data: {actId: actId},
                            type: "POST",
                            dataType: "json",
                            success: function (data) {
                                if (data.status == 1) {
                                    layer.msg(data.info, {icon: 6});
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 1000);
                                } else {
                                    layer.msg(data.info, {icon: 5});
                                }
                            }
                        });
                    }, function () {
                        layer.prompt({
                            title: '请输入驳回理由',
                        }, function (value, index, elem) {
                            $.ajax({
                                url: '/index.php/Admin/Online/reback',
                                data: {reason: value, actId: actId},
                                type: 'POST',
                                dataType: "json",
                                success: function (data) {
                                    if (data.status == 1) {
                                        layer.msg(data.info, {icon: 6});
                                        setTimeout(function () {
                                            window.location.reload();
                                        }, 1000);
                                    } else {
                                        layer.msg(data.info, {icon: 5});
                                    }
                                }
                            });
                            layer.close(index);
                        });
                    });
                }
            }
        });
    });
    //撤销审核
    $('.reback-sh').click(function () {
        var actId = $(this).attr('act-id');
        $.ajax({
            url: '/index.php/Admin/Online/revokeAudit',
            data: {actId: actId},
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    layer.msg(data.info, {icon: 6});
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                } else {
                    layer.msg(data.info, {icon: 5});
                }
            }
        });
    });

    //批量审核上线
    $(".online").click(function () {
        //获取状态
        var arr = new Array();
        $("input[name='chkItem']:checked").each(function (event) {
            //获取当前选中的状态值
            var status = $(this).parent().parent().find('.current').attr('current-status');
            //只有待审核的才能批量审核
            if (status == 2) {
                arr.push($(this).val());
            }
        })
        $.ajax({
            url: "/index.php/Admin/Online/batchOnline",
            data: {arr: arr},
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status == 1) {
                    layer.msg(data.info, {icon: 6});
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                } else {
                    layer.alert(data.info, {icon: 5});
                }
            }
        });
    });
    /**************************置顶****************************/
	$('.top-a').click(function () {
        var url = $(this).attr('data-url');
        $.ajax({
            url : '/index.php/Admin/Online/top',
            dataType:'JSON',
            success : function(data){
                if(data){
                    layer.msg(data.info,{icon:5});
                    return false;
                }else{
                    layer.open({
                        type: 2,
                        title: '置顶',
                        shadeClose: true,
                        shade: false,
                        area: ['400px', '200px'],
                        content: url,
                    });
                }
            }
        });
    });
    //设置置顶
    $('.top-button').click(function () {
        var val = $("input[name='act_is_top']:checked").val();
        var actId = $(this).attr('act-id');
        $.ajax({
            url: '/index.php/Admin/Online/setTop',
            data: {actId: actId, isTop: val},
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    window.parent.call_back(1);
                } else {
                    window.parent.call_back(0);
                }
            }
        });

    });
    //取消置顶
    $('.top-b').click(function () {
        var actId = $(this).attr('act-id');
        $.ajax({
            url: '/index.php/Admin/Online/setTop',
            data: {actId: actId},
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                        layer.msg(data.info, {icon: 6});
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    } else {
                        layer.msg(data.info, {icon: 5});
                    }
            }
        });
    });

    //报名审核通过
    $('.pass').click(function () {
        var actId = $(this).data('id');
        var userId = $(this).data('userid');
        layer.confirm('确定处理吗?', {
            btn: ['通过', '不通过','取消'],
            closeBtn: 0
        }, function () {
            $.ajax({
                url: '/index.php/Admin/Enter/pass',
                data: {actId: actId,userId:userId, pass: 'pass'},
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        layer.msg(data.info, {icon: 6});
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    } else {
                        layer.msg(data.info, {icon: 5});
                    }
                }
            });

        }, function (index) {
            $.ajax({
                url: '/index.php/Admin/Enter/pass',
                data: {actId: actId,userId:userId, pass: 'nopass'},
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        layer.msg(data.info, {icon: 6});
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    } else {
                        layer.msg(data.info, {icon: 5});
                    }
                }
            })
        })
    });
    $(".add-group").click(function () {
        var groupName = $.trim($(".group-name").val());
        $.ajax({
            url: "/index.php/Admin/System/ajaxAddGroup",
            data: {groupName: groupName},
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status == 1) {
                    layer.msg(data.info, {icon: 6});
                    setTimeout(function () {
                        window.location.href = "/index.php/Admin/System/authInfo";
                    }, 1500);
                }
                if (data.status == 0) {
                    layer.alert(data.info, {icon: 5});
                }
            }
        });
    });
    $(".edit-group form dl dd input[type='search']").keyup(function () {
        var username = $.trim($(this).val());
        if (username.length > 0) {
            $.ajax({
                url: "/index.php/Admin/System/ajaxSearch",
                data: {username: username},
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if (data.info == 'null') {
                        $(".add-li").empty();
                    }
                    setTimeout(function(){
						var str = "";
						str += '<form id="addUser" style="border:0;">';
						$.each(data['url']['data'], function (index, val) {
							str += '<li><input type="checkbox" name="admin_id[]" value="' + val.admin_id + '"  />' + val.realname + '</li>';
						});
						str += '</form>';
						$(".add-li").empty().append(str);
					},2000);
                }
            });
        }
    })
    $(".addUser").click(function () {
        var groupId = $(this).attr("data-id");
        $("#addUser").ajaxSubmit({
            type: 'POST',
            data: {groupId: groupId},
            url: "/index.php/Admin/System/ajaxAddUser",
            success: function (data) {
                if (data.status == 1) {
                    layer.msg(data.info, {icon: 6});
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                } else {
                    layer.msg(data.info, {icon: 5});
                }

            }
        });
    });
    //移除用户
    $(".delete-user").click( function () {
        var adminId = $(this).attr("data-id");
        $.ajax({
            url: "/index.php/Admin/System/ajaxDeleteUser",
            data: {adminId: adminId},
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status == 1) {
                    layer.msg(data.info, {icon: 6});
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500)
                } else {
                    layer.alert(data.info, {icon: 5});
                }
            }
        });
    });

    //更改小组名称
    $(".edit-user-group").click(function () {
        var _input = $("input[name='group_name']");
        var groupId = _input.attr('data-id');
        var groupName = _input.val();
        $.ajax({
            url: "/index.php/Admin/System/ajaxEditGroup",
            data: {groupId: groupId, groupName: groupName},
            dataType: "json",
            type: "POST",
            success: function (data) {
                if (data.status == 1) {
                    layer.msg(data.info, {icon: 6});
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500)
                } else {
                    layer.alert(data.info, {icon: 5});
                }
            }
        });
    });


    //封禁用户
    $(".person-dis").click(function () {
        var adminId = $(this).attr("data-id");
        var _this = $(this);
        layer.confirm('确定封禁或解除？', {
            btn: ['确定', '取消'], //按钮
        }, function () {
         $.ajax({
             url :"/index.php/Admin/System/ajaxClosure",
             data : {adminId:adminId},
             type: "POST",
             dataType : "json",
             success : function(data){
                  if (data.status == 1) {
                    layer.msg(data.info, {icon: 6});
                   _this.text() =="已禁用" ? _this.text("封禁用户"):_this.text("已禁用");
                } else {
                    layer.alert(data.info, {icon: 5});
                }
             }
         });
         }, function (index) {
            layer.close(index);
            return false;// 取消
        })
    });
    /******************表单提交*************************/
   $(".set-form-submit").click(function(){
       var flag = false;
       $("input[name*='Title']").each(function(){
           if($(this).val()==''){
              alert('表单项名称不能为空');
               $(this).addClass('error');
               $(this).parent().parent().siblings('dl').children('.name-input').children('input').removeClass('error');
               $(this).blur(function(){
                   $(this).removeClass('error');
               })
               flag = true;
               return false;
           }
       });
       if(flag){
           flag = false;
           return false;
       }
        var data = $("#setFormData").serialize();
        layer.confirm('表单只能保存一次且不能修改,是否保存?',{
             btn:['保存','取消'],
             offset:'30%',
        },function(index){
            addClick(data,index);
            layer.close(index);
        },function(index){
            layer.close(index);
            return false;// 取消
        })
   });
    function addClick(data,index){
        $.ajax({
            url: "/index.php/Admin/Activity/saveEventForm",
            data: data,
            type: "POST",
            dataType: "json",
            success: function (data) {
                if(data.status == 1){
                    alert(data.info);
                    //$(".set-form-submit").text('修改').removeClass('submit set-form-submit').addClass('save-form-data');
                  // layer.alert(data.info,{icon:6,offset:'30%'});
                }else{
                    //layer.alert(data.info,{icon:5,offset:'40%'});
                    layer.close(index);
                }
            }
        });
    }
    //发布活动
    $(".act_push").click(function(){
        var flag = false;
        $("input[name*='Title']").each(function(){
            if($(this).val()==''){
                alert('表单项名称不能为空');
                flag = true;
            }
        });
        if(flag){
            flag = false;
            return false;
        }
        var data = $("#setFormData").serialize();
        var actId = $(this).attr('data-id');
        layer.confirm('确认发布活动?',{
            offset:'30%',
            btn:['确认','取消']
        },function(index){
            layer.close(index);
            addClick(data);
            $.ajax({
                url : "/index.php/Admin/Activity/add",
                data : {act_id:actId},
                type : "POST",
                dataType : 'json',
                success : function(data){
                    if(data.status == 1){
                        alert(data.info);
                       // layer.alert(data.info,{icon:6,offset:'30%'});
                        setTimeout(function(){
                            window.location.href = '/index.php/Admin/Activity/actMessage';
                        },1500)
                    }else{
                        alert(data.info);
                        //layer.alert(data.info,{icon:5});
                    }
                }
            });
        },function(index){
            layer.close(index);
            return false;// 取消
        })
    });
    $(".save-form-data").click(function(){
          alert('此活动表单已保存过');
          return false;
    });

})