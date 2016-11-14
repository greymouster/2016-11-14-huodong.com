$(function () {
    $("input[type='button']").click(function () {
        var username = $.trim($("input[name='username']").val());
        var password = $.trim($("input[name='password']").val());
        var remeber;
        if ($("input[type='checkbox']").is(":checked")) {
            remeber = 1;
        } else {
            remeber = 0;
        }
        $.ajax({
            url: "/index.php/admin/admin/doLogin",
            data: {username: username, password: password, remeber: remeber},
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status == -1) {
                    $(".box").show().text(data.msg);
                }
                if (data.status == 0) {
                    $(".box").css('backgroundColor', 'green');
                    $(".box").show().text(data.msg);
                    setTimeout(function () {
                        window.location.href = "/index.php/Admin/Index/index";
                    }, 1500);
                }
            }
        });
    });
});