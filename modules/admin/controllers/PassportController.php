<?php

/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2017/10/2
 * Time: 13:52
 */

namespace app\modules\admin\controllers;

use app\models\Option;
use app\models\Goods;
use app\modules\admin\models\LoginForm;
use app\modules\admin\models\password\RegisterForm;
use app\modules\admin\models\password\ResetPasswordForm;
use app\modules\admin\models\password\SendRegisterSmsCodeForm;
use app\modules\admin\models\password\SendSmsCodeForm;
use yii\web\HttpException;
use app\modules\admin\models\UserRegisterForm;
use app\models\Store;
use app\models\Login;
use app\models\Admin;
use app\models\Renew;
use app\models\Distribution;
use app\models\Release;

class PassportController extends Controller
{


    public $layout = 'passport';

    public function behaviors()
    {
        return [];
    }

    public function actions()
    {
        return [
            'captcha' => \app\utils\ClearCaptchaAction::className(),
            'sms-code-captcha' => \app\utils\ClearCaptchaAction::className(),
        ];
    }

    //登录
    public function actionLogin()
    {

        if (\Yii::$app->request->isAjax) {
            $form = new LoginForm();
            $form->attributes = \Yii::$app->request->post();
            $res = $form->login();
            if($res['code'] == 0){
                if(\Yii::$app->admin->id == 1){
                    return $res;
                }else{
                    $data = Store::find()->where(['admin_id'=>\Yii::$app->admin->id, 'is_delete' => 0,])->one();
                    if($data){
                        return [
                            'code' => 7,
                            'app_id' => $data->id,
                            'msg' => '登录成功',
                        ];
                    }else{
                        return $form->login();
                    }
                }
            }else{
                return $res;
            }
        } else {

              if(\Yii::$app->request->get('sale_id')){
                $sale_id = \Yii::$app->request->get('sale_id');
            }else{
                $sale_id = '';
            }

            return $this->render('login',['sale_id'=>$sale_id]);

        }

    }

  //检查是否有登录
    public function actionCheck(){
      $scene_id = \Yii::$app->request->post('scene_id');
 
      $data = Admin::find()->where(['login'=>$scene_id])->one();

      $res = array();
      $res['scene_id'] = $scene_id;

      if($data){
        $res['username'] = $data['openID'];
        $res['code'] = 1;
      }else{
        $res['username'] = '';
        $res['code'] = 0;
      }
      return $res;

    }
  

    public function actionReceived(){
        
        $encryptMsg = file_get_contents('php://input');
// file_put_contents('a.txt', $encryptMsg);
        $login = Login::findone(1);

        if($encryptMsg){

            $postObj = simplexml_load_string($encryptMsg, 'SimpleXMLElement', LIBXML_NOCDATA);
            $scenes = json_decode($postObj->EventKey, true);
            if($postObj->Event == 'subscribe'){
                $ticket = $postObj->Ticket; //Ticket
                $openids = $postObj->FromUserName; //openid
                $eventKey =$postObj->EventKey;
         
                $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$login['access_token']."&openid=".$openids."&lang=zh_CN";
                $res = $this->https_get($url);
           
                $result = json_decode($res,true);
                $nickname = $result['nickname'];
                $openid = $result['openid'];
            
               $data = Admin::find()->where(['openID'=>$openid])->one();
          
                if($data){
					Admin::updateAll(['username' => $nickname,'login' => substr($eventKey,-9),'addtime' => time()], ['openID' => $openid]);
                }else{
                  
                    $sale_id = substr($eventKey,-5);
                    $preg = '/[0]*/';
                    $sale_id = preg_replace($preg, '', $sale_id, 1);
                  	 $r = Admin::find()->where(['id'=>$sale_id])->one();
                    $admin_register = new Admin(); 
                 
                    $admin_register->username = $nickname;
                    $admin_register->openID = $openid;
                     $admin_register->login = substr($eventKey,-9);
                    $admin_register->addtime = time();
                    $admin_register->password = '$2y$13$YYTHylYW5SUyo49lLMqUt.tpuKH68CLmmGwika2xBTReSms0mVJny';
                    $admin_register->auth_key = 'N2sjJbvo7ALdj6hhibXiPu-M_7nylbYo';
                    $admin_register->expire_time = strtotime("+1 months",time());
                    $admin_register->permission = '["coupon","share","topic","video","miaosha","pintuan","book","fxhb","copyright","mch","integralmall","permission","pond","scratch","bargain","prince_comment","lottery"]';
                    $re = $admin_register->save();
              		if($re){
                          $admin_register->primaryKey; //生成主键id
                      }
              
                     $son_id = $admin_register->attributes['id'];
               
                  	 $b = new Release();
        			 $b->name = $nickname;
        			 $b->user_id = $son_id;
     

                    if($r){
                      	$b->father_id = $sale_id;
                        $distribution = new Distribution();
                        $distribution->father_id = $sale_id;
                        $distribution->son_id = $son_id;
                        $distribution->addtime = time();
                        $distribution->son_name = $nickname;
                        $ress = $distribution->save();
                        if($ress){
                            $distribution->primaryKey; //生成主键id
                        }
                
                    }else{
                      $b->father_id = null;
                    }
                    $b->insert();
                   
            		
				}
            }else if($postObj->Event == 'SCAN'){
                $ticket = $postObj->Ticket; //Ticket
                $openids = $postObj->FromUserName; //openid
                $eventKey =$postObj->EventKey;

                $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$login['access_token']."&openid=".$openids."&lang=zh_CN";
                $res = $this->https_get($url);
                $result = json_decode($res,true);
                $nickname = $result['nickname'];
                $openid = $result['openid'];


                $logins = Admin::find()->where(['openID'=>$openid])->one();
               
                if($logins){
            
                    $logins->login = $eventKey;
                    $logins->save();
                }

            }else if($postObj->Event == 'CLICK'){
            	if($postObj->EventKey == 'V1001'){
                	$contentStr = "添加微信号：weibang_kefu";
                   	$textTpl = "<xml>
                                  <ToUserName><![CDATA[%s]]></ToUserName>
                                  <FromUserName><![CDATA[%s]]></FromUserName>
                                  <CreateTime>%s</CreateTime>
                                  <MsgType><![CDATA[text]]></MsgType>
                                  <Content><![CDATA[%s]]></Content>
                                  <FuncFlag>%d</FuncFlag>
                                </xml>";
                    $resultStr = sprintf($textTpl, $postObj->FromUserName, $postObj->ToUserName, time(), $contentStr, 0);
                    return $resultStr;
                }
            }
        }
    }

    //生成登录二维码
    public function actionCode(){
      if(\Yii::$app->request->ispost){


        $appid = "wx342ff353ff5267b6";
        $appsecret = "2df2b5385fae75e4cb4051333497026f";
 		$sale_id = \Yii::$app->request->post('sale_id');

        $sale_id =  base64_decode($sale_id);

        $session = \Yii::$app->session;

        //当session没有值 或 过期时间到 则重新付值
        if(!isset($session['access_tokens']) || $session['access_tokens']['expire_time'] < time()){

            //获取$access_token
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret . "";

            $result = $this->curl_post($url);

            $access_tokens = json_decode($result, true);

            $login = Login::findone(1);
            $login->access_token = $access_tokens['access_token'];
            $login->save();

            $data = [
                'access_tokens' => $access_tokens,  //数据
                'expire_time' => time() + 7000,
            ];
            $session['access_tokens'] = $data;
        }

        if($sale_id){
            $scene_id = mt_rand(11,99).substr(time(),-2).$sale_id;
        }else{
            $scene_id = mt_rand(100,999).substr(time(),-6);
        }


        $qrcodes = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$scene_id.'}}}';

    //    if(!isset($session['ticket']) || $session['ticket']['expire_time'] < time()){

            $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" .$session['access_tokens']['access_tokens']['access_token'] . "";



            $momo = json_decode($qrcodes, true);

            $result = $this->curl_post($url, $momo);

            $rs = json_decode($result, true);

      //      $login = Login::findone(1);
       //     $login->ticket = $rs['ticket'];
     //       $login->save();

            $data = [
                'ticket' => $rs['ticket'],  //数据
                'expire_time' => time() + 1700,
            ];
            $session['ticket'] = $data;
   //     }
        $qrcode = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $session['ticket']['ticket'] . "";

        $res = array();
        $res['qrcode'] = $qrcode;
        $res['scene_id'] = $scene_id;
        return  $res;

    	}
	}
    /*

          * 发起GET网络提交

          * @params string $url : 网络地址

          */

    private function https_get($url)

    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($curl, CURLOPT_HEADER, FALSE) ;

        curl_setopt($curl, CURLOPT_TIMEOUT,60);

        if (curl_errno($curl)) {

            return 'Errno'.curl_error($curl);

        }

        else{$result=curl_exec($curl);}

        curl_close($curl);

        return $result;

    }



    //注销
    public function actionLogout()
    {
        \Yii::$app->admin->logout();
        \Yii::$app->response->redirect(\Yii::$app->urlManager->createUrl(['admin']))->send();
    }

    //发送短信验证码，修改密码用
    public function actionSendSmsCode()
    {
        $form = new SendSmsCodeForm();
        $form->attributes = \Yii::$app->request->post();
        return $form->send();
    }

    //通过短信验证重置密码
    public function actionResetPassword()
    {
        $form = new ResetPasswordForm();
        $form->attributes = \Yii::$app->request->post();
        return $form->save();
    }

    //注册
    public function actionRegister()
    {

        $open_register = Option::get('open_register', 0, 'admin', false);
        if (!$open_register) {
            throw new HttpException(403, '注册功能暂未开放。');
        }
        if (\Yii::$app->request->isPost) {
//            return "aaa";
            $form = new RegisterForm();
            $form->attributes = \Yii::$app->request->post();

            $result = $form->save();
            $result['status'] = 1;
            if($result['code'] == 0){
//                    $this->actionRegisters();
                $forms = new UserRegisterForm();
                $forms->attributes = $result;
                return $forms->save();
            }else{
                return $form->save();
            }
//            return $form->save();

        } else {
            return $this->render('register',['qrcode'=>$qrcode]);
        }
    }


    //注册 数据验证
    public function actionRegisterValidate()
    {

        $form = new RegisterForm();
        $form->attributes = \Yii::$app->request->post();

        $form->post_attrs = \Yii::$app->request->post();

        return $form->validateAttr();
    }

    //发送短信验证码，注册用
    public function actionSendRegisterSmsCode()
    {
        $form = new SendRegisterSmsCodeForm();
        $form->attributes = \Yii::$app->request->post();
        return $form->send();
    }

    public function actionFocus(){
        return $this->render('focus');
    }

    //注册协议
    public function actionAgreement(){
        return $this->render('agreement');
    }

    //获取appsecret教程
    public function actionAppsecret(){
        return $this->render('appsecret');
    }
    //获取微信商户支付号帮助
    public function actionWxshh(){
        return $this->render('wxshh');
    }
    //获取微信商户支付api帮助
    public function actionWxapi(){
        return $this->render('wxapi');
    }
    //获取微信商户支付证书帮助
    public function actionWxzs(){
        return $this->render('wxzs');
    }
  
    public static function curlPost($url = '', $postData = '', $options = array())
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    public static function createNonceStr($length = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    /**
     * 获取签名
     */
    public static function getSign($params, $key)
    {
        ksort($params, SORT_STRING);
        $unSignParaString = self::formatQueryParaMap($params, false);
        $signStr = strtoupper(md5($unSignParaString . "&key=" . $key));

        return $signStr;
    }

    protected static function formatQueryParaMap($paraMap, $urlEncode = false)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if (null != $v && "null" != $v) {
                if ($urlEncode) {
                    $v = urlencode($v);
                }
                $buff .= $k . "=" . $v . "&";
            }
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
         //支付回调
    public function actionRenews(){

        if(\Yii::$app->request->isPost) {
            $data = \Yii::$app->request->post();

            $postData = array(
                'appid' => 'wxdb3f28ee363c2365',
                'mch_id' => '1513949871',      
                'nonce_str' => self::createNonceStr(),
                'out_trade_no' => $data['out_trade_no'],
            );
            $postData['sign'] = self::getSign($postData,'qwejhhe5yqqwewesduyqqwejhheuyq32');

            $responseXml = self::curlPost('https://api.mch.weixin.qq.com/pay/orderquery', self::arrayToXml($postData));
            $unifiedOrder = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);

            if($unifiedOrder->trade_state == 'SUCCESS'){
                $res = new Renew();
                $admin = Admin::find()->where(['id'=>\Yii::$app->admin->id])->one();
              
                $res->user_id = \Yii::$app->admin->id;
                $res->name = $admin->username;
                $res->total_fee = $unifiedOrder->total_fee/100;
                $res->time = $unifiedOrder->time_end;
                $res->transaction_id = $unifiedOrder->transaction_id;
                $res->out_trade_no = $unifiedOrder->out_trade_no;
                $res->status = "支付成功";
   				$one_id = Distribution::find()->where(['son_id'=>\Yii::$app->admin->id])->select('father_id')->one();
                $one_id =$one_id->father_id;
                $two_id = Distribution::find()->where(['son_id'=>$one_id])->select('father_id')->one();
                $two_id =$two_id->father_id;
               if($data['type'] == "base_one"){
                    $admin->permission = '["coupon","topic","video","book","fxhb","copyright","permission","scratch","bargain","prince_comment"]';
                   if($admin->level == 0){
                        $admin->expire_time = strtotime("+1 months",time());
                    }elseif($admin->level == 1){
                        $admin->expire_time = strtotime("+1 months",$admin->expire_time);
                    }elseif ($admin->level == 2){
                        $admin->expire_time = strtotime("+1 months",($admin->expire_time-time())*1.8+time());
                    }
                   $admin->level = 1;
                   $res->content = "基础版1个月";
                   $res->one_rate = (Admin::findOne(1)->royalty_one/100)*($unifiedOrder->total_fee/100);
                   $res->two_rate = (Admin::findOne(1)->royalty_two/100)*($unifiedOrder->total_fee/100);
                   if($one_id){
                          $royalty = Admin::find()->where(['id'=>$one_id])->one();
                           $royalty->royalty = $royalty->royalty + $res->one_rate;
                           $royalty->save();
                    }
            
                       if($two_id){
                        $royaltys = Admin::find()->where(['id'=>$two_id])->one();
                        $royaltys->royalty = $royaltys->royalty + $res->two_rate;
                        $royaltys->save();
                    }
                }elseif ($data['type'] == "base_two"){
                   $admin->permission = '["coupon","topic","video","book","fxhb","copyright","permission","scratch","bargain","prince_comment"]';
               		if($admin->level == 0){
                       $admin->expire_time = strtotime("+3 months",time());
                   }elseif($admin->level == 1){
                       $admin->expire_time = strtotime("+3 months",$admin->expire_time);
                   }elseif ($admin->level == 2){
                       $admin->expire_time = strtotime("+3 months",($admin->expire_time-time())*1.8+time());
                   }
                   $admin->level = 1;
                   $res->content = "基础版3个月";
                   $res->one_rate = (Admin::findOne(1)->royalty_one/100)*($unifiedOrder->total_fee/100);
                   $res->two_rate = (Admin::findOne(1)->royalty_two/100)*($unifiedOrder->total_fee/100);
                  if($one_id){
                          $royalty = Admin::find()->where(['id'=>$one_id])->one();
                           $royalty->royalty = $royalty->royalty + $res->one_rate;
                           $royalty->save();
                    }
            
                       if($two_id){
                        $royaltys = Admin::find()->where(['id'=>$two_id])->one();
                        $royaltys->royalty = $royaltys->royalty + $res->two_rate;
                        $royaltys->save();
                    }
               }elseif ($data['type'] == "base_three"){
                   $admin->permission = '["coupon","topic","video","book","fxhb","copyright","permission","scratch","bargain","prince_comment"]';
 					 if($admin->level == 0){
                       $admin->expire_time = strtotime("+1 years",time());
                   }elseif($admin->level == 1){
                       $admin->expire_time = strtotime("+1 years",$admin->expire_time);
                   }elseif ($admin->level == 2){
                       $admin->expire_time = strtotime("+1 years",($admin->expire_time-time())*1.8+time());
                   }
                   $admin->level = 1;
                   $res->content = "基础版12个月";
                   $res->one_rate = (Admin::findOne(1)->royalty_one/100)*($unifiedOrder->total_fee/100);
                   $res->two_rate = (Admin::findOne(1)->royalty_two/100)*($unifiedOrder->total_fee/100);
                  if($one_id){
                          $royalty = Admin::find()->where(['id'=>$one_id])->one();
                           $royalty->royalty = $royalty->royalty + $res->one_rate;
                           $royalty->save();
                    }
            
                       if($two_id){
                        $royaltys = Admin::find()->where(['id'=>$two_id])->one();
                        $royaltys->royalty = $royaltys->royalty + $res->two_rate;
                        $royaltys->save();
                    }
               }elseif ($data['type'] == "all_one"){
                   $admin->permission = '["coupon","share","topic","video","miaosha","pintuan","book","fxhb","copyright","mch","integralmall","permission","pond","scratch","bargain","prince_comment","lottery"]';
     				   if($admin->level == 0){
                       $admin->expire_time = strtotime("+1 months",time());
                   }elseif($admin->level == 1){
                       $admin->expire_time = strtotime("+1 months",($admin->expire_time-time())*0.55+time());
                   }elseif ($admin->level == 2){
                       $admin->expire_time = strtotime("+1 months",$admin->expire_time);;
                   }
                   $admin->level = 2;
                   $res->content = "全能版1个月";
                  $res->one_rate = (Admin::findOne(1)->royalty_one/100)*($unifiedOrder->total_fee/100);
                   $res->two_rate = (Admin::findOne(1)->royalty_two/100)*($unifiedOrder->total_fee/100);
                    if($one_id){
                          $royalty = Admin::find()->where(['id'=>$one_id])->one();
                           $royalty->royalty = $royalty->royalty + $res->one_rate;
                           $royalty->save();
                    }
            
                       if($two_id){
                        $royaltys = Admin::find()->where(['id'=>$two_id])->one();
                        $royaltys->royalty = $royaltys->royalty + $res->two_rate;
                        $royaltys->save();
                    }
               }elseif ($data['type'] == "all_two"){
                   $admin->permission = '["coupon","share","topic","video","miaosha","pintuan","book","fxhb","copyright","mch","integralmall","permission","pond","scratch","bargain","prince_comment","lottery"]';
                 if($admin->level == 0){
                       $admin->expire_time = strtotime("+3 months",time());
                   }elseif($admin->level == 1){
                       $admin->expire_time = strtotime("+3 months",($admin->expire_time-time())*0.55+time());
                   }elseif ($admin->level == 2){
                       $admin->expire_time = strtotime("+3 months",$admin->expire_time);;
                   }
                   $admin->level = 2;
                   $res->content = "全能版3个月";
                  $res->one_rate = (Admin::findOne(1)->royalty_one/100)*($unifiedOrder->total_fee/100);
                   $res->two_rate = (Admin::findOne(1)->royalty_two/100)*($unifiedOrder->total_fee/100);
                	if($one_id){
                          $royalty = Admin::find()->where(['id'=>$one_id])->one();
                           $royalty->royalty = $royalty->royalty + $res->one_rate;
                           $royalty->save();
                    }
            
                       if($two_id){
                        $royaltys = Admin::find()->where(['id'=>$two_id])->one();
                        $royaltys->royalty = $royaltys->royalty + $res->two_rate;
                        $royaltys->save();
                    }
               }elseif ($data['type'] == "all_three"){
                   $admin->permission = '["coupon","share","topic","video","miaosha","pintuan","book","fxhb","copyright","mch","integralmall","permission","pond","scratch","bargain","prince_comment","lottery"]';
        		 if($admin->level == 0){
                       $admin->expire_time = strtotime("+1 years",time());
                   }elseif($admin->level == 1){
                       $admin->expire_time = strtotime("+1 years",($admin->expire_time-time())*0.55+time());
                   }elseif ($admin->level == 2){
                       $admin->expire_time = strtotime("+1 years",$admin->expire_time);;
                   }
                   $admin->level = 2;
                   $res->content = "全能版12个月";
                	 $res->one_rate = (Admin::findOne(1)->royalty_one/100)*($unifiedOrder->total_fee/100);
                   $res->two_rate = (Admin::findOne(1)->royalty_two/100)*($unifiedOrder->total_fee/100);
                   if($one_id){
                          $royalty = Admin::find()->where(['id'=>$one_id])->one();
                           $royalty->royalty = $royalty->royalty + $res->one_rate;
                           $royalty->save();
                    }
            
                       if($two_id){
                        $royaltys = Admin::find()->where(['id'=>$two_id])->one();
                        $royaltys->royalty = $royaltys->royalty + $res->two_rate;
                        $royaltys->save();
                    }
               }
                if($res->save()){
                    $intPostId= $res->primaryKey; //生成主键id
                }
                $a = $admin->save();
                if($a){
                    $result=array();
                    $result['code']=0;
                    $result['text']="支付成功";
                    return $result;
                }
            }else{

                $result=array();
                $result['code']=1;
                $result['text']="未支付";
                return $result;

            }


        }
    }
}
