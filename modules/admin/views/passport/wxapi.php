<?php
$this->title = '如何获取微信支付Api密钥';

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>如何获取微信支付Api密钥</title>
      <style>
        span{
            font-size: 25px;
            color: red;
        }
    </style>
</head>
<body>
<div>
    <div>
        <span>第一步：登录微信商户支付平台(https://pay.weixin.qq.com/index.php/core/home/login)。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxapi/wxapi_1.jpg" alt="" style="width: 80%;margin-left: 10%;">

    </div>
    <div>
        <span>第二步：点击1号红框内的账户中心，再点击2号红框内的API安全，如果未安装过的话就点击3号红框内的安装操作证书。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxapi/wxapi_2.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第三步：点击红框内的安装控件。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxapi/wxapi_3.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第四步：点击红框内的立即安装。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxapi/wxapi_4.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第五步：安装完全后点击红框内的启用控件。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxapi/wxapi_5.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第六步：成功启用控件后可见上面红框的信息，再点击下方红框内的申请安装。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxapi/wxapi_6.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第七步：3个红框内对应填入相应的信息，点击确定。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxapi/wxapi_7.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第八步：成功后可见此图。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxapi/wxapi_8.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第九步：点击左边的API安全，再点击右边的设置秘钥。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxapi/wxapi_9.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第十步：点击确认。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxapi/wxapi_10.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第十一步：设置API秘钥，建议和appsecret设成一样，便于记忆，再填入操作密码和短信验证码，点击确定。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxapi/wxapi_11.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第十二步：将刚设置的API秘钥填入微信支付Api密钥框内。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxapi/wxapi_12.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
</div>
</body>
</html>