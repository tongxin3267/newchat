<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2017/6/24
 * Time: 22:31
 */

namespace app\modules\api\controllers;

use app\models\User;
use app\modules\api\models\LoginForm;

class PassportController extends Controller
{
    public function actionLogin()
    {
   
  
        $form = new LoginForm();
        $form->attributes = \Yii::$app->request->post();

        $form->appid = $this->appid;
     	$form->appsecret = $this->appsecret;
        $form->store_id = $this->store->id;
     //	 file_put_contents('w.txt', $this->appid);
     //	 file_put_contents('ww.txt', $this->appsecret);
	//	file_put_contents('www.txt', $this->wechats);
        if(\Yii::$app->fromAlipayApp()) {
            return $form->loginAlipay();
        } else {
            return $form->login();
        }
    }
}
