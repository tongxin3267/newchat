<?php

/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2017/10/3
 * Time: 11:34
 */

namespace app\modules\admin\controllers;

use app\opening\CloudAdmin;
use app\models\Admin;
use app\models\AdminRegister;
use app\modules\admin\models\UserRegisterForm;
use app\modules\admin\models\UserFocusForm;
use yii\data\Pagination;
use app\models\AdminAuth ;

class AuditController extends Controller
{

    protected $appid = 'wx2bdc1845f7e64617';            //第三方平台应用appid

    private $appsecret = '53905d776670e2ef1bca0ec064143e9d';     //第三方平台应用appsecret

    private $token = 'weixin';           //第三方平台应用token（消息校验Token）

    private $encodingAesKey ='oiO8KM82sa05W4S1BpGgROnx64QpzeW5KJNur6P7VZG';      //第三方平台应用Key（消息加解密Key）

    private $component_ticket= '';   //微信后台推送的ticket,用于获取第三方平台接口调用凭据



    public function actionIndex(){


        return $this->render('index');

    }


}
