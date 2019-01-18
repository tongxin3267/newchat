<?php
$this->title = '如何获取微信支付商户号';

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>如何获取微信支付商户号</title>
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
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxshh/wxshh_1.jpg" alt="" style="width: 80%;margin-left: 10%;">

    </div>
    <div>
        <span>第二步：点击1号红框内的账户中心，再点击2号红框内的商户信息，这时可以看到3号红框内我们需要的微信支付商户号。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxshh/wxshh_2.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第三步：将获取到的微信支付商户号填入小程序微信支付商户号框内。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxshh/wxshh_3.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
</div>
</body>
</html>