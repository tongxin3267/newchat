<?php
$this->title = '如何获取微信支付证书';

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>如何获取微信支付证书</title>
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
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxzs/wxzs_1.jpg" alt="" style="width: 80%;margin-left: 10%;">

    </div>
    <div>
        <span>第二步：点击账户中心，再点击API安全，如果1号框内未如图显示证书已安装则参考如何获取微信支付Api密钥安装证书。已安装过的点击下载证书。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxzs/wxzs_2.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第三步：点击下载。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxzs/wxzs_3.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第四步：输入短信验证码和操作密码后点击确认。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxzs/wxzs_4.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第五步：下载后得到一个名称为cert的压缩包。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxzs/wxzs_5.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>
    <div>
        <span>第六步：点击选择文件将下载的压缩包上传。再点击保存。</span>
        <img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/wxzs/wxzs_6.jpg" alt="" style="width: 80%;margin-left: 10%;">
    </div>

</div>
</body>
</html>