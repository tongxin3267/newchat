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
$this->title = '注册账户';
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
    <title>会员注册 - 微帮</title>
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

<!-- header -->
<div class="header-container">
    <div class="banner-box">
        <div class="top-nav header-main">
            <div class="header default-transition-fast">
                <div class="header-wrapper auto clearfix">
                    <div class="header-left">
                        <div class="header-logo">
                            <a href="" class="logo hide-text">
                              <img src="<?= Yii::$app->request->baseUrl ?>/statics/admin/images/register/header_logo.png" alt="" style="width: 200px;margin-top: -20%;">
                            </a>
                        </div>

                    </div>

                    <div class="header-nav-right">
                        <div class="welcome-container" id="userinfoContainer">
                            <div class="logout-container clearfix">
                                <a href="<?= Yii::$app->urlManager->createUrl(['admin/passport/login', 'return_url' => Yii::$app->urlManager->createUrl(['admin'])]) ?>" class="login-button">请登录</a>

                            </div>
                            <div class="userinfo-container">
                                <div class="username-container" id="usernameContainer">
                                    <span class="link-spacing"></span>
                                    <a href="user/" class="username-text" id="usernameText" target="/user/">
                                        <span class="username-text-show text-overflow" id="usernameShow"></span>
                                        <span id="userId"></span>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- header end-->

<!--注册主体-->
<div class="register-main auto" id="app">
    <h2>欢迎注册微帮账号</h2>
    <div class="register-box clearfix step-1">
        <form action="" name="register" id="register" method="post" onSubmit="return false">
            <div class="reg-left">
                <table cellspacing="0" cellpadding="0">

                    <tr>
                        <td width="400">
                            <div>
                                <p>用户名</p>
                                <input v-model="username" autocomplete="off" value="" type="text">
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
                            <div>
                                <p>设置密码</p>
                                <input v-model="password"  maxlength="20" autocomplete="off" type="password">
                            </div>
                        </td>
                        <td class="validate-tips"><div class="tip"><div id="passwordTip" style="margin: 0px; padding: 0px; background: transparent none repeat scroll 0% 0%;" class="onShow"><div class="onShow"><i></i></div></div></div></td>
                    </tr>
                    <tr>
                        <td>
                            <div>
                                <p>确认密码</p>
                                <input v-model="password2" maxlength="20" autocomplete="off" type="password">
                            </div>
                        </td>
                        <td class="validate-tips"><div class="tip"><div id="password2Tip" style="margin: 0px; padding: 0px; background: transparent none repeat scroll 0% 0%;" class="onShow"><div class="onShow"><i></i></div></div></div></td>
                    </tr>

                    <tr>
                        <td>
                            <div lass="input-group">
                                <p>验证码</p>
                                <input name="mobilecode" v-model="sms_code" id="mobilecode" maxlength="6" class="input-captcha input" value="" type="text">
                                <input id="sendtime" value="" type="hidden">

                                <span style="margin-left: 10%;" class="send-captcha send-captcha-disabled ">
                                    <a v-if="send_sms_code_timeout > 0" href="javascript:"
                                       >获取验证码</a>
                                    <a v-else href="javascript:" class="btn btn-primary show-sms-code-modal send-sms-code" data-toggle="modal" >获取验证码</a>
                                </span>
                            </div>
                            <a class="btn btn-light disabled "  style="margin-left: 75%;margin-top: -15.5%;" v-if="send_sms_code_timeout > 0">{{send_sms_code_timeout}}秒再次获取</a>
                        </td>
                        <td class="validate-tips"><div class="tip"><div id="mobilecodeTip" style="margin: 0px; padding: 0px; background: transparent none repeat scroll 0% 0%;" class="onShow"><div class="onShow"><i></i></div></div></div></td>
                    </tr>

                    <tr>
                        <td>
                            <input value="注册" class="reg-submit-gray reg-submit default-transition submit" type="button">
                        </td>
                        <td></td>
                    </tr>
                  	<tr>
                        <td style="font-size:10px;text-align: center;">注册即表示同意《<a target="_blank" href="<?= Yii::$app->urlManager->createUrl(['admin/passport/agreement']) ?>">微帮注册协议</a>》</td>
                    </tr>
                    </tbody></table>
            </div>
            <div class="reg-right">
       
                <h2>关注我们</h2>
                <p class="company-qrcode"></p>
                <p class="company-qrcode-name">微帮微信公众号</p>
            </div>
        </form>
    </div>
    <div class="register-box clearfix step-2"  style="display: none">

        <div>您已注册成功，请点击<a href="<?= Yii::$app->urlManager->createUrl(['admin/passport/login', 'return_url' => Yii::$app->urlManager->createUrl(['admin'])]) ?>">登录</a></div>

    </div>

</div>

<!-- buyFooter -->
<div class="signfooter" style="margin-top: 7%;">
    <div class="auto clearfix">

        <div class="footer-signfooter">
            <p>Copyright © 2013-2018 Niaoyun.com. All Rights Reserved. 微帮版权所有</p>
            <p></p>
        </div>
    </div>

</div>
<!-- footer end -->




<div style="display: none; position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; cursor: move; opacity: 0; background: rgb(255, 255, 255) none repeat scroll 0% 0%;"></div></body>
</html>


<script>
    var app = new Vue({
        el: '#app',
        data: {
            username: '',
            password: '',
            password2: '',
            name: '',
            mobile: '',
            sms_code: '',
            desc: '',
            captcha_code: '',
            send_sms_code_timeout: 0,
            send_sms_error: '',
        },
    });

    $(document).on('click', '.refresh-captcha', function () {
        var img = $(this);
        var refresh_url = img.attr('data-refresh');
        $.ajax({
            url: refresh_url,
            dataType: 'json',
            success: function (res) {
                img.attr('src', res.url);
            }
        });
    });


    $(document).on('click', '.submit', function () {
        var btn = $(this);
        btn.btnLoading();
        $.ajax({
            url: '<?=Yii::$app->urlManager->createUrl(['admin/passport/register-validate'])?>',
            type: 'post',
            dataType: 'json',
            data: {
                _csrf: _csrf,
                username: app.username,
                password: app.password,
                password2: app.password2,
            },
            success: function (res) {
                // console.log("111");
                if (res.code == 0) {


                } else {
                    $.myToast({
                        content: res.msg,
                    });
                }
            },
            complete: function (res) {
                console.log(res);
                btn.btnReset();
            }
        });

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {
                _csrf: _csrf,
                username: app.username,
                password: app.password,
                password2: app.password2,
                name: "WEIBANG",
                mobile: app.mobile,
                sms_code: app.sms_code,
                desc: "WEIBANG_DETAIL",
            },
            // console.log(data);
            success: function (res) {
                // console.log(res);
                if (res.code == 0) {
                    $('.step-1').hide();
                    $('.step-2').show();

                } else {
                    $.myToast({
                        content: res.msg,
                    });
                }
            },
            complete: function (res) {
                // console.log(res);
                btn.btnReset();
            }
        });
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