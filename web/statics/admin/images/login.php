<!DOCTYPE html>
<html>
<head lang="en">
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>会员登录 - 微帮</title>

    <meta name="keywords" content="">
    <meta name="description" content="">



</head>
<body class="login-bg">
<div >
    <div style="height:300px;">
        <img src=""  style="height:100%;margin-left:42.5%;background-color: orangered;" id = "erweima">
    </div>
    <div style="text-align: center;">
        <a href="javascrpt:" id ="login" class=""><button  style="font-size: 20px;border:none;border-radius: 5px;width: 150px;height:70px;background-color:deepskyblue;color: white;">点击扫码登陆</button></a>
    </div>
</div>
</body>
<script>
    $(document).on('click', '#login', function () {
        $.post({
            url: "index.php?r=admin%2Fpassport%2Fcode",
            type: "post",
            dataType: "json",
            data: [],
            complete: function (res) {
               
                $("#erweima").attr('src',res.responseJSON.qrcode);
                check_login(res.responseJSON.scene_id);
            }
        })
    });

    function check_login($login) {
      //  console.log($login);
        var scene_id = $login;
        $.post({
            url: "index.php?r=admin%2Fpassport%2Fcheck",
            type: "post",
            dataType: "json",
            data: {
                'scene_id': scene_id
            },
            success: function (res) {

                if (res.code == 0) {
                    setTimeout("check_login("+$login+")", 2000);
                }else if(res.code == 1){
                    var username = res.username;
                    var password = "123";
                    $.ajax({
                        url:'<?=Yii::$app->urlManager->createUrl('admin/passport/login')?>',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'username': username,
                            'password': password,
                            _csrf: _csrf,
                        },
                        success:function (res) {

                            if (res.code === 1) {
                                $.myAlert({
                                    content: res.msg
                                });
                            }else if (res.code === 7){
                                var id = res.app_id;
                                var str = "<?= \Yii::$app->urlManager->createUrl(['admin/app/entry']) ?>" + '&id=' + id;
                                location.href = str;
                            }else  {
                                location.href = "<?= \Yii::$app->urlManager->createUrl('admin/user/me') ?>";
                            }
                        }
                    })
                }


            }
        });


    }
</script>


</html>
