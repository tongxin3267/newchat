<?php
/**
 * Created by IntelliJ IDEA
 * Date Time: 2018/7/11 11:58
 */

defined('YII_ENV') or exit('Access Denied');

use app\models\Option;

/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2017/10/2
 * Time: 14:08
 */
$this->title = '信息验证';
$logo = Option::get('logo', 0, 'admin', null);
$logo = $logo ? $logo : Yii::$app->request->baseUrl . '/statics/admin/images/logo.png';

$copyright = Option::get('copyright', 0, 'admin');
$copyright = $copyright ? $copyright : '微帮小程序';//P_MOD

$passport_bg = Option::get('passport_bg', 0, 'admin', Yii::$app->request->baseUrl . '/statics/admin/images/passport-bg.jpg');
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>信息验证 - 微帮</title>
    <meta name="keywords" content="">
    <meta name="description" content="">


    <style>
        .send-code-timeout {
            position: absolute;
            left: 100%;
            top: 0;
        }
    </style>

    <!--[if lte IE 9]>
    <script src="/template/Home/Niaoyun/PC/Static/lib/browser/PIE.js"></script>
    <![endif]-->
    <!--[if IE 7]>
    <script charset="utf-8">
        $(function () {
            $("a").attr("hidefocus", true);
        });
    </script>
    <![endif]-->


    <link href="<?= Yii::$app->request->baseUrl ?>/statics/admin/css/register.css" rel="stylesheet">
</head>
<body>


<!--注册主体-->
<div class="register-main " id="app" style="width:100%;">
    <h4>信息验证</h4>
    <div class="register-box clearfix step-1">
        <form action="" name="register" id="register" method="post" onSubmit="return false">
            <div class="reg-left">
                <table cellspacing="0" cellpadding="0">

                    <tr>
                        <td width="400">
                            <div>
                                <p>真实姓名</p>
                                <input v-model="username" autocomplete="off" value="" type="text">
                            </div>
                        </td>
                        <td class="validate-tips"><div class="tip"><div id="mobileTip" style="margin: 0px; padding: 0px; background: transparent none repeat scroll 0% 0%;" class="onShow"><div class="onShow"><i></i></div></div></div></td>
                    </tr>
                    <tr>
                        <td width="400">
                            <div>
                                <p>经营类目</p>
                                <input v-model="type" autocomplete="off" value="" type="text">
                            </div>
                        </td>
                        <td class="validate-tips"><div class="tip"><div id="mobileTip" style="margin: 0px; padding: 0px; background: transparent none repeat scroll 0% 0%;" class="onShow"><div class="onShow"><i></i></div></div></div></td>
                    </tr>
                    <tr>
                        <td width="400">
                            <div>
                                <p>手机号</p>
                                <input v-model="mobile" maxlength="11" autocomplete="off" value="" type="text">
                            </div>
                        </td>
                        <td class="validate-tips"><div class="tip"><div id="mobileTip" style="margin: 0px; padding: 0px; background: transparent none repeat scroll 0% 0%;" class="onShow"><div class="onShow"><i></i></div></div></div></td>
                    </tr>

                    <tr>
                        <td>
                            <div lass="input-group">
                                <p>验证码</p>
                                <input name="mobilecode" v-model="sms_code" id="mobilecode" maxlength="6" class="input-captcha input" value="" type="text">
                                <input id="sendtime" value="" type="hidden">

                                <span style="margin-left: 10%;" class="send-captcha send-captcha-disabled ">
                                    <a v-if="send_sms_code_timeout > 0" href="javascript:" hidden
                                    ></a>
                                    <a v-else href="javascript:" class="btn btn-primary show-sms-code-modal send-sms-code" data-toggle="modal" >获取验证码</a>
                                </span>
                            </div>
                            <a class="btn btn-light disabled "  style="margin-left: 75%;margin-top: -15.5%;" v-if="send_sms_code_timeout > 0">{{send_sms_code_timeout}}秒再次获取</a>
                        </td>
                        <td class="validate-tips"><div class="tip"><div id="mobilecodeTip" style="margin: 0px; padding: 0px; background: transparent none repeat scroll 0% 0%;" class="onShow"><div class="onShow"><i></i></div></div></div></td>
                    </tr>

                    <tr>
                        <td>
                            <input value="提交" class="reg-submit-gray reg-submit default-transition submit" type="button">
                        </td>
                        <td></td>
                    </tr>

                    </tbody></table>
            </div>

        </form>
    </div>
</div>

<div style="display: none; position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; cursor: move; opacity: 0; background: rgb(255, 255, 255) none repeat scroll 0% 0%;"></div></body>
</html>


<script>
    var app = new Vue({
        el: '#app',
        data: {
            username: '',
            type:'',
            mobile: '',
            sms_code: '',
            send_sms_code_timeout: 0,
            send_sms_error: '',
        },
    });

    $(document).on('click', '.submit', function () {
        var btn = $(this);
        btn.btnLoading();
  		 if(!app.username){
            alert("真实姓名不能为空");
             btn.btnReset();
        }else{
             if(!app.type){
                 alert("经营类目不能为空");
                 btn.btnReset();
             }else{
                 if(!app.mobile){
                     alert("手机号不能为空");
                     btn.btnReset();
                 }else{
                     if(!(/^1(3|4|5|7|8)\d{9}$/.test(app.mobile))){
                         alert("手机号码有误，请重填");
                         btn.btnReset();
                     } else{
                         if(!app.sms_code){
                             alert("验证码不能为空");
                             btn.btnReset();
                         }else{
                             $.ajax({
                                 url: '<?=Yii::$app->urlManager->createUrl(['mch/wechat/phone'])?>',
                                 type: 'post',
                                 dataType: 'json',
                                 data: {
                                     _csrf: _csrf,
                                     username: app.username,
                                     type: app.type,
                                     mobile: app.mobile,
                                     sms_code: app.sms_code,
                                 },
                                 complete: function (res) {
                                   console.log(res);
                                     if(res.responseJSON.code == 0){
                                         alert(res.responseJSON.text);
                                         location.href = "<?= \Yii::$app->urlManager->createUrl('mch/wechat/mp-config') ?>";
                                     }else{
                                         alert(res.responseJSON.text);
                                     }
                                     btn.btnReset();
                                 },
                             });
                         }

                     }
                 }
             }
         }

    });

    $(document).on('click', '.send-sms-code', function () {

        var btn = $(this);
        btn.btnLoading();
        app.send_sms_error = false;
        $.ajax({
            url: '<?=Yii::$app->urlManager->createUrl(['admin/passport/send-register-sms-code'])?>',
            type: 'post',
            dataType: 'json',
            data: {
                _csrf: _csrf,
                mobile: app.mobile,
                captcha_code: app.captcha_code,
            },
            success: function (res) {

                if (res.code == 0) {

                    //  $('#smsCodeModal').modal('hide');
                    starSendSmsTimeout(res.data.timeout);
                } else {
                    alert(res.msg);
                    //  app.send_sms_error = res.msg;
                }
            },
            complete: function () {
                btn.btnReset();
            },
        });
    });

    function starSendSmsTimeout(timeout) {
        app.send_sms_code_timeout = timeout;
        var timer = setInterval(function () {
            if (app.send_sms_code_timeout == 0) {
                clearInterval(timer);
            } else {
                app.send_sms_code_timeout--;
            }
        }, 1001);
    }

</script>