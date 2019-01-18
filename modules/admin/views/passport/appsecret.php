<?php
$this->title = '如何获取appsecret';

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>如何获取appsecret</title>
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
            <span>第一步：打开微信公众平台(https://mp.weixin.qq.com/),使用注册小程序时的邮箱登录，注意不是服务号的账号。</span>
            <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/appsecret/appsecret_1.jpg" alt="" style="width: 80%;margin-left: 10%;">

        </div>
        <div>
            <span>第二步：点击左下角的设置按钮，再点击中间页面选项卡的开发设置，这时可以看到我们需要的AppSecret(小程序秘钥)。如果是第一次则点击右边的设置，忘记了可以点击重置。获取后在本地保存起来。</span>
            <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/appsecret/appsecret_2.jpg" alt="" style="width: 80%;margin-left: 10%;">
        </div>
        <div>
            <span>第三步：将获取到的32位appsecret填入小程序AppSecret框内。</span>
            <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/appsecret/appsecret_3.jpg" alt="" style="width: 80%;margin-left: 10%;">
        </div>
    </div>
</body>
</html>