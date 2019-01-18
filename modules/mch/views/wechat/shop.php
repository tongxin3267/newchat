<?php
$this->title = '微帮信息录入';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">


    <title>微信信息录入</title>

    <link href="<?= Yii::$app->request->baseUrl ?>/statics/shop/xinxi/part1.css" rel="stylesheet">
    <link href="<?= Yii::$app->request->baseUrl ?>/statics/shop/xinxi/part2.css" rel="stylesheet">
    <link href="<?= Yii::$app->request->baseUrl ?>/statics/shop/xinxi/part3.css" rel="stylesheet">
</head>
<body style="background: rgb(255, 255, 255) none repeat scroll 0% 0%;">
<div id="form" class="reg_form">

        <div class="step_status step1">
            <span class="status c_fff" id="one">1. 手机验证</span>
            <span class="status" id="two">2. 填写店铺信息</span>
            <span class="status" id="three">3. 录入成功</span>
        </div>

        <div class="setp_wrap" id="part1">
            <div class="input_box">
                <label for="">真实姓名</label>
                <input id="truename" class="reg_320"  placeholder="请输入真实姓名" type="text"><span id="SpanUserName" class="msg"></span>
            </div>

            <div class="input_box">
                <label for="">手机号</label>
                <input maxlength="11" id="mobile" class="tele" placeholder="请输入手机号" autocomplete="off" type="text">
                <input class="getCode send-sms-code" id="btnSend"  value="获取验证码" type="button">
                <span id="SpanPhone" class="msg" style="display: inline-block;"></span>
            </div>
            <div class="input_box">
                <label for="">短信验证码</label>
                <input maxlength="6" id="txtCode" class="reg_320" placeholder="请输入短信验证码" value="" type="text">
                <span id="SpanSmsCode" class="msg"></span>
            </div>
            <input value="下一步" onclick="" id="btnSave1" class="next_step" style="padding: 0px;" type="submit">

        </div>

        <div class="setp_wrap" id="part2" style="display: none;">

            <div class="input_box">
                <label for="" style="margin-top: 6px;">经营类目</label>
                <table id="RBType" class="mess-item" datatype="jinyinleimu" nullmsg="请选择经营类目" errormsg="类目选错，请重新选择" style="margin: 5px; max-width: 600px;">
                    <tbody><tr>
                       <td><input id="RBType_1" name="RBType" value="百货购物" type="radio"><label for="RBType_1">百货购物</label></td><td><input id="RBType_3" name="RBType" value="餐饮美食" type="radio"><label for="RBType_3">餐饮美食</label></td><td><input id="RBType_5" name="RBType" value="美容丽人" type="radio"><label for="RBType_5">美容丽人</label></td>
                    </tr><tr>
                        <td><input id="RBType_8" name="RBType" value="积分兑换" type="radio"><label for="RBType_8">积分兑换</label></td> <td><input id="RBType_9" name="RBType" value="玩具乐器" type="radio"><label for="RBType_9">玩具乐器</label></td><td><input id="RBType_14" name="RBType" value="家居" type="radio"><label for="RBType_14">家居</label></td>
                    </tr><tr>
                        <td><input id="RBType_15" name="RBType" value="成人用品" type="radio"><label for="RBType_15">成人用品</label></td><td><input id="RBType_16" name="RBType" value="服饰" type="radio"><label for="RBType_16">服饰</label></td><td><input id="RBType_17" name="RBType" value="母婴" type="radio"><label for="RBType_17">母婴</label></td>
                    </tr><tr>
                        <td><input id="RBType_18" name="RBType" value="个护清洁" type="radio"><label for="RBType_18">个护清洁</label></td><td><input id="RBType_19" name="RBType" value="家用电器" type="radio"><label for="RBType_19">家用电器</label></td><td><input id="RBType_20" name="RBType" value="鞋靴/箱包" type="radio"><label for="RBType_20">鞋靴/箱包</label></td>
                    </tr><tr>
                        <td><input id="RBType_21" name="RBType" value="运动户外" type="radio"><label for="RBType_21">运动户外</label></td><td><input id="RBType_23" name="RBType" value="鲜花绿植" type="radio"><label for="RBType_23">鲜花绿植</label></td> <td><input id="RBType_25" name="RBType" value="工具类" type="radio"><label for="RBType_25">工具类</label></td>
                    </tr><tr>
                       <td><input id="RBType_28" name="RBType" value="纺织品" type="radio"><label for="RBType_28">纺织品</label></td><td><input id="RBType_29" name="RBType" value="家具建材" type="radio"><label for="RBType_29">家具建材</label></td><td><input id="RBType_26" name="RBType" value="机械设备" type="radio"><label for="RBType_26">机械设备</label></td>
                    </tr><tr>
                        <td><input id="RBType_30" name="RBType" value="茶/酒/粮油/零食" type="radio"><label for="RBType_30">茶/酒/粮油/零食</label></td><td><input id="RBType_32" name="RBType" value="手表/眼镜/珠宝饰品" type="radio"><label for="RBType_32">手表/眼镜/珠宝饰品</label></td>
                    </tr>
                    </tbody></table>
            </div>
            <input value="下一步" onclick="" id="btnSave2" class="next_step" style="padding: 0px;" type="submit">
        </div>
        <div class="setp_wrap" id="part3" style="display: none;">
            <div id="successful" class="successful">
                <img src="<?= Yii::$app->request->baseUrl ?>/statics/shop/xinxi/successful.png" alt="">

                <p>恭喜申请成功！</p>
                <!--现在保存成功后直接跳转到首页-->
                <a href="<?=Yii::$app->urlManager->createUrl(['mch/wechat/mp-config'])?>" class="successful_bnt">开始管理店铺</a>
            </div>
            <div id="rwm" class="rwm">
                <img src="<?= Yii::$app->request->baseUrl ?>/statics/shop/xinxi/reweima.png" id="companyImgQrCode">
                <p>用微信扫二维码<br>关注我们</p>
            </div>
        </div>
</div>
<script>

    $("#truename").blur(function(){
        var name = $("#truename").val();
        if(!name){
            $("#SpanUserName").text("真实姓名不能为空");
        }
    });
    $("#truename").focus(function(){
        $("#SpanUserName").text("");
    });
    $("#mobile").blur(function(){
        var mobile = $("#mobile").val();
        if(!(/^1(3|4|5|7|8)\d{9}$/.test(mobile))){
            $("#SpanPhone").text("手机号码有误，请重填");
        }
    });
    $("#mobile").focus(function(){
        $("#SpanPhone").text("");
    });
    $("#txtCode").blur(function(){
        var txtCode = $("#txtCode").val();
        if(!txtCode){
            $("#SpanSmsCode").text("验证码不能为空");
        }
    });
    $("#txtCode").focus(function(){
        $("#SpanSmsCode").text("");
    });

    //获取验证码
    $(document).on('click', '.send-sms-code', function () {

        mobile = $("#mobile").val();
        $.ajax({
            url: '<?=Yii::$app->urlManager->createUrl(['admin/passport/send-register-sms-code'])?>',
            type: 'post',
            dataType: 'json',
            data: {
                _csrf: _csrf,
                mobile:mobile,
            },
            success: function (res) {

                if (res.code == 0) {
                    console.log(res);
                    starSendSmsTimeout(res.data.timeout);
                } else {
                    alert(res.msg);

                }
            },
        });
    });

    function starSendSmsTimeout(timeout) {
        send_sms_code_timeout = timeout;

        var timer = setInterval(function () {
            if (send_sms_code_timeout == 0) {
                clearInterval(timer);
                $("#btnSend").val("获取验证码");
            } else {
                $("#btnSend").val(send_sms_code_timeout+"秒后重新发送");
                send_sms_code_timeout--;
            }
        }, 1001);
    }

    $(document).on('click', '#btnSave1', function (){
        var name = $("#truename").val();
        var mobile = $("#mobile").val();
        var txtCode = $("#txtCode").val();
        if(!name){
            $("#SpanUserName").text("真实姓名不能为空");
        }
        if(!(/^1(3|4|5|7|8)\d{9}$/.test(mobile))){
            $("#SpanPhone").text("手机号码有误，请重填");
        }
        if(!txtCode){
            $("#SpanSmsCode").text("验证码不能为空");
        }
        if(name && txtCode && (/^1(3|4|5|7|8)\d{9}$/.test(mobile))){
            $.ajax({
                url: '<?=Yii::$app->urlManager->createUrl(['mch/wechat/phone'])?>',
                type: 'post',
                dataType: 'json',
                data: {
                    _csrf: _csrf,
                    username: name,
                    mobile:mobile,
                    sms_code: txtCode,
                },
                complete: function (res) {

                    if(res.responseJSON.code == 0){
                 
                        $("#part1").hide();
                        $("#part2").show();
                        $(".step_status").attr("class","step_status step2");
                        $("#one").attr("class","status");
                        $("#two").attr("class","status c_fff");
                    }else{
                        alert(res.responseJSON.text);
                    }

                },
            });
        }




    });
</script>
<script>
    $(document).on('click', '#btnSave2', function (){
        category = $('#RBType input[name="RBType"]:checked ').val();
        if(!category){
            alert("请选择经营类别");
        }else{
            $.ajax({
                url: '<?=Yii::$app->urlManager->createUrl(['mch/wechat/shop'])?>',
                type: 'post',
                dataType: 'json',
                data: {
                    _csrf: _csrf,
                    category: category,
                },
                complete: function (res) {

                    if(res.responseJSON.code == 0){
                        $("#part2").hide();
                        $("#part3").show();
                        $(".step_status").attr("class","step_status step3");
                        $("#two").attr("class","status");
                        $("#three").attr("class","status c_fff");
                    }else{
                        alert(res.responseJSON.text);
                    }

                },
            });
        }
    });
</script>
</body>
</html>
