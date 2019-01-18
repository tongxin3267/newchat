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
$this->title = '关注公众号';
$logo = Option::get('logo', 0, 'admin', null);
$logo = $logo ? $logo : Yii::$app->request->baseUrl . '/statics/admin/images/logo.png';

$copyright = Option::get('copyright', 0, 'admin');
$copyright = $copyright ? $copyright : '微帮小程序';//P_MOD

$passport_bg = Option::get('passport_bg', 0, 'admin', Yii::$app->request->baseUrl . '/statics/admin/images/passport-bg.jpg');
?>
<style>
    html {
        position: relative;
        min-height: 100%;
        min-width: 800px;
        overflow-x: auto;
    }

    body {
        background-image: none;
        padding-bottom: 70px;
        min-height: 100%;
        overflow: hidden;
        background-color: #fff;
    }

    .main {
        height: 100%;
    }

    form .custom-control-input:checked ~ .custom-control-indicator {
        border-color: transparent;
    }

    .header {
        background: #f6f6f6;
        padding: 50px 0;
        text-align: center;
        margin-bottom: 40px;
    }

    .header .logo-a {
        display: inline-block;
        height: 50px;
        padding: 0;
        margin-bottom: 20px;
    }

    .logo {
        display: inline-block;
        height: 100%;
    }

    .footer {
        position: absolute;
        height: 70px;
        background: #f6f6f6;
        bottom: 0;
        left: 0;
        width: 100%;
    }

    .copyright {
        padding: 24px 0;
    }

    .step-block {
        margin: 0 auto;
        max-width: 800px;
        margin-bottom: 60px;
    }

    .step-block .step-item {
        text-align: center;
        position: relative;
        color: #888;
    }

    .step-block .step-item:after {
        display: block;
        content: " ";
        height: 1px;
        width: calc(100% - 20px);
        background: #ccc;
        position: absolute;
        top: 20px;
        left: calc(50% + 20px);
    }

    .step-block .step-item:last-child:after {
        display: none;
    }

    .step-block .step-icon {
        width: 40px;
        height: 40px;
        text-align: center;
        line-height: 40px;
        border-radius: 999px;
        display: inline-block;
        background: #d9d9d9;
        color: #fff !important;
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .step-block .step-item.active {
        color: #307ed7;
    }

    .step-block .step-item.active .step-icon {
        background: #307ed7;
    }

    .step {
        max-width: 500px;
        margin: 0 auto;
    }

    .step .row {
        transform: translate(-70px, 0);
    }

    .send-code-timeout {
        position: absolute;
        left: 100%;
        top: 0;
    }
</style>
<div class="main" id="app">

    <div>
        <div class="step-block row">

        </div>

        <div class="step step-1 mb-4">
            <img style="margin-bottom: 40px"
                 src="<?= Yii::$app->request->baseUrl ?>/statics/admin/images/focus.jpg">
            <div>请扫码关注公众号，否则无法登陆。关注后请点击<a href="<?= Yii::$app->urlManager->createUrl(['admin/passport/login', 'return_url' => Yii::$app->urlManager->createUrl(['admin'])]) ?>">登陆</a></div>
        </div>

    </div>

</div>



</div>


