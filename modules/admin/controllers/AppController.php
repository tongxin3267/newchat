<?php

/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2017/10/6
 * Time: 10:20
 */

namespace app\modules\admin\controllers;

use app\models\Admin;
use app\models\Banner;
use app\models\Cat;
use app\models\common\admin\log\CommonActionLog;
use app\models\common\admin\store\CommonAppDisabled;
use app\models\Coupon;
use app\models\Goods;
use app\models\GoodsCat;
use app\models\GoodsPic;
use app\models\HomeBlock;
use app\models\HomeNav;
use app\models\Option;
use app\models\PtCat;
use app\models\PtGoods;
use app\models\PtGoodsPic;
use app\models\PtSetting;
use app\models\Shop;
use app\models\ShopPic;
use app\models\Store;
use app\models\Video;
use app\models\YyCat;
use app\models\YyGoods;
use app\models\YyGoodsPic;
use app\models\YySetting;
use app\modules\admin\models\app\AppDisabledForm;
use app\modules\admin\models\AppEditForm;
use app\modules\admin\models\RemovalForm;
use yii\data\Pagination;
use Yii;
use app\models\AdminAuth;
use app\models\Apply;
use app\models\WechatApp;
use app\models\Renew;
use app\models\Distribution;
use app\models\Release;
use app\models\Sure;
use app\models\Audit;

class AppController extends Controller
{

    //我的小程序商城
    public function actionIndex()
    {

 		$data = Admin::find()->where(['id'=>\Yii::$app->admin->id])->one();
        if ($data->expire_time == 0 || time() < $data->expire_time) {

        }else{
            $data->level = 0;
            $data->permission = '[]';
            $data->save();
//           $this->redirect(\Yii::$app->urlManager->createUrl('admin/app/renew'));
        }
      
      
        //第三方平台的
        $appid = "wx2bdc1845f7e64617";


        $auth_code = \Yii::$app->request->getQueryParam('auth_code');
        $expires_in = \Yii::$app->request->getQueryParam('expires_in');
        if($auth_code){

            $ticket = AdminAuth::find()->where(['id'=>1])->one();

            $component_access_token = $ticket->component_access_token;


            $url ="https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=".$component_access_token;
            $data = '{

            "component_appid":"'.$appid.'" ,

            "authorization_code": "'.$auth_code.'"

        }';

            $ret = json_decode($this->https_post($url,$data));


            $authorizer_appid = $ret->authorization_info->authorizer_appid;


            $url ='https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token='.$component_access_token;
            $data = '{

            "component_appid":"'.$appid.'" ,

            "authorizer_appid": "'.$authorizer_appid.'"

        }';

            $res = json_decode($this->https_post($url,$data));

            $data_wxminiprograms = Apply::find()->where(['authorizer_appid'=>$res->authorization_info->authorizer_appid])->one();
          
          
            if($data_wxminiprograms){
				
                $data_wxminiprograms->uid = \Yii::$app->admin->id;
                $data_wxminiprograms->nick_name = $res->authorizer_info->nick_name;
                $data_wxminiprograms->token = "weixin";
                $data_wxminiprograms->head_img = $res->authorizer_info->head_img;
                $data_wxminiprograms->verify_type_info = $res->authorizer_info->verify_type_info;
                $data_wxminiprograms->qrcode_url = $res->authorizer_info->qrcode_url;
                $data_wxminiprograms->idc = $res->authorizer_info->idc;
                $data_wxminiprograms->principal_name = $res->authorizer_info->principal_name;
                $data_wxminiprograms->signature = $res->authorizer_info->signature;
                $data_wxminiprograms->miniprograminfo = $res->authorizer_info->miniprograminfo;
                $data_wxminiprograms->authorizer_appid = $res->authorization_info->authorizer_appid;
                $data_wxminiprograms->authorizer_access_token = $ret->authorization_info->authorizer_access_token;
                $data_wxminiprograms->authorizer_refresh_token = $res->authorization_info->authorizer_refresh_token;
                $data_wxminiprograms->is_delete = 0;
                $data_wxminiprograms->create_time = time();

                $res_1 = $data_wxminiprograms->save();

                $data_wechatapp = WechatApp::find()->where(['app_id'=> $res->authorization_info->authorizer_appid])->one();
                $id = $data_wechatapp->id ;

                $res_2 = WechatApp::updateAll(['user_id' => \Yii::$app->admin->id,'name'=>$res->authorizer_info->nick_name,'addtime'=>time(),'is_delete'=>0], ['app_id'=> $res->authorization_info->authorizer_appid]);
			
              
              	$data_store = Store::find()->where(['wechat_app_id'=>$id])->one(); 
             	$data_store->name = $res->authorizer_info->nick_name;
             	 $data_store->is_delete = 0;
              	$res_3 = $data_store->save();
  
            }else{
              
                 if(Apply::find()->where(['uid'=>\Yii::$app->admin->id])->one()){
               			return $this->render('index', [
                        'res' => "该账号已有小程序绑定，请前往公众平台取消授权后绑定其他账号",
                	]);
        		}else{
                       $model = new Apply();

                      $model->uid = \Yii::$app->admin->id;
                      $model->nick_name = $res->authorizer_info->nick_name;
                      $model->token = "weixin";
                      $model->head_img = $res->authorizer_info->head_img;
                      $model->verify_type_info = $res->authorizer_info->verify_type_info;
                      $model->qrcode_url = $res->authorizer_info->qrcode_url;
                      $model->idc = $res->authorizer_info->idc;
                      //      $model->business_info = $res->authorizer_info->business_info;
                      $model->principal_name = $res->authorizer_info->principal_name;
                      $model->signature = $res->authorizer_info->signature;
                      $model->miniprograminfo = $res->authorizer_info->miniprograminfo;
                      //   $model->func_info = $res->authorization_info->func_info;
                      $model->authorizer_appid = $res->authorization_info->authorizer_appid;
                      $model->authorizer_access_token = $ret->authorization_info->authorizer_access_token;
                      $model->authorizer_refresh_token = $res->authorization_info->authorizer_refresh_token;
                      $model->create_time = time();

                      $res_1 = $model->save();
                      if(!$res_1){
                           return $this->render('index', [
                              'res' => "存入数据失败，请完善小程序数据后重新授权",
                          ]);
                      }

                      $model = new WechatApp();

                      $model->user_id = \Yii::$app->admin->id;
                      $model->name = $res->authorizer_info->nick_name;
                      $model->app_id = $res->authorization_info->authorizer_appid;
                      $model->addtime = time();

                      $res_2 = $model->save();
                      if(!$res_2){
                             return $this->render('index', [
                                'res' => "存入数据失败，请完善小程序数据后重新授权",
                            ]);
                        }
                      $id = $model->id ;

                      $model = new Store();
                      $model->name = $res->authorizer_info->nick_name;
                      $model->admin_id = \Yii::$app->admin->id;
                      $model->user_id = \Yii::$app->admin->id;
                      $model->wechat_app_id = $id;
                      $res_3 = $model->save();
                      if(!$res_3){
                           return $this->render('index', [
                              'res' => "存入数据失败，请完善小程序数据后重新授权",
                          ]);
                      }
                      $model = new Audit();
                      $model->appid = $res->authorization_info->authorizer_appid;
                      $model->auditid = null;
                      $model->status = 4;
                      $model->reason = null;
                      $model->create_time = null;
                      $res_4 = $model->save();
                      if(!$res_4){
                          return $this->render('index', [
                              'res' => "存入数据失败，请完善小程序数据后重新授权",
                          ]);
                      }
                 }
        
               
            }
 
                $url = "/web/index.php?r=admin%2Fapp%2Findex";
                $this->redirect($url);
        

        }else{
           		 $code = \Yii::$app->request->getQueryParam('code');

                $query = Store::find()->where([
                    'admin_id' => \Yii::$app->admin->id,
                    'is_delete' => 0,
                    'is_recycle' => 0,
                ]);
                $count = $query->count();
                $pagination = new Pagination(['totalCount' => $count]);
                $list = $query->limit($pagination->limit)->offset($pagination->offset)->orderBy('id DESC')->all();
                 $expire_time = Admin::find()->where(['id'=>\Yii::$app->admin->id])->one();
                 if($expire_time['expire_time'] == 0){
                     $time = "永久";
                 }else{
                     $time = date('Y-m-d H:i:s',$expire_time['expire_time']);
                 }
 				 if($code){
                        return $this->render('index', [
                            'list' => $list,
                            'code' => $code,
                            'expire_time' => $time,
                            'pagination' => $pagination,
                            'app_max_count' => \Yii::$app->admin->identity->app_max_count,
                            'app_count' => Store::find()->where([
                                'admin_id' => \Yii::$app->admin->id,
                                'is_delete' => 0,
                            ])->count(),

                        ]);
                 }else{
                       return $this->render('index', [
                           'list' => $list,
                           'code' => '',
                           'expire_time' => $time,
                           'pagination' => $pagination,
                           'app_max_count' => \Yii::$app->admin->identity->app_max_count,
                           'app_count' => Store::find()->where([
                               'admin_id' => \Yii::$app->admin->id,
                               'is_delete' => 0,
                           ])->count(),

                       ]);
                   }
        }


    }


  
    /*

     * 发起POST网络提交

     * @params string $url : 网络地址

     * @params json $data ： 发送的json格式数据

     */

    private function https_post($url,$data)

    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        if (!empty($data)){

            curl_setopt($curl, CURLOPT_POST, 1);

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($curl);

        curl_close($curl);

        return $output;

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



    //子账户的小程序商城
    public function actionOtherApp($keyword = null)
    {
        $query = Store::find()->alias('s')->where([
            'AND',
            ['!=', 's.admin_id', \Yii::$app->admin->id],
            ['s.is_delete' => 0],
            ['a.is_delete' => 0],
        ])->leftJoin(['a' => Admin::tableName()], 's.admin_id=a.id');
        ;
        if ($keyword = trim($keyword)) {
            $query->andWhere([
                'OR',
                ['LIKE', 's.name', $keyword],
                ['LIKE', 'a.username', $keyword],
            ]);
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count]);
        $list = $query->limit($pagination->limit)->offset($pagination->offset)->orderBy('id DESC')
            ->select('s.*,a.username')->asArray()->all();
        return $this->render('other-app', [
            'list' => $list,
            'keyword' => $keyword,
            'pagination' => $pagination,
        ]);
    }

    //新增、编辑小程序
    public function actionEdit($name)
    {
        $form = new AppEditForm();
        $form->attributes = $name;
        $form->admin_id = \Yii::$app->admin->id;
        return $form->save();
    }

    //进入小程序后台
    public function actionEntry($id)
    {
        $condition = [
            'id' => $id,
            'admin_id' => \Yii::$app->admin->id,
            'is_delete' => 0,
        ];
        if (\Yii::$app->admin->id == 1) {
            unset($condition['admin_id']);
        }
        $store = Store::findOne($condition);
        if (!$store) {
            \Yii::$app->response->redirect(\Yii::$app->request->referrer)->send();
            return;
        }
     
     	$res = $this->actionAdd($store->id);
        if(!$res){
            \Yii::$app->response->redirect(\Yii::$app->request->referrer)->send();
           return;
       }
        \Yii::$app->session->set('store_id', $store->id);
        CommonActionLog::storeActionLog('', 'login', 0, [], \Yii::$app->admin->id);
        \Yii::$app->response->redirect(\Yii::$app->urlManager->createUrl(['mch/store/index']))->send();
    }

    //删除小程序商城
    public function actionDelete($id)
    {
        $condition = [
            'id' => $id,
            'admin_id' => \Yii::$app->admin->id,
            'is_delete' => 0,
        ];
        if (\Yii::$app->admin->id == 1) {
            unset($condition['admin_id']);
        }
        $store = Store::findOne($condition);
        if ($store) {
            $store->is_delete = 1;
            $store->save();
        }
        return [
            'code' => 0,
            'msg' => '操作成功',
        ];
    }

    //小程序回收站
    public function actionRecycle()
    {
        $query = Store::find()->where([
            'admin_id' => \Yii::$app->admin->id,
            'is_delete' => 0,
            'is_recycle' => 1,
        ]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count]);
        $list = $query->limit($pagination->limit)->offset($pagination->offset)->orderBy('id DESC')->all();
    
      
            return $this->render('recycle', [
                'list' => $list,
                'pagination' => $pagination,
            ]);
        

    }

    public function actionSetRecycle($id, $action)
    {
        $condition = [
            'id' => $id,
            'admin_id' => \Yii::$app->admin->id,
            'is_delete' => 0,
        ];
        if (\Yii::$app->admin->id == 1) {
            unset($condition['admin_id']);
        }
        $store = Store::findOne($condition);
        if ($store) {
            $store->is_recycle = $action ? 1 : 0;
            $store->save();
        }
        return [
            'code' => 0,
            'msg' => '操作成功',
        ];
    }

    public function actionOtherUser($keyword = null)
    {
        $query = Admin::find()->where(['is_delete' => 0]);
        if (trim($keyword)) {
            $query->andWhere(['like', 'username', $keyword]);
        }
        $list = $query->limit(10)->asArray()->all();
        return [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'list' => $list
            ]
        ];
    }

    public function actionRemoval()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new RemovalForm();
            $form->attributes = \Yii::$app->request->get();
            return $form->save();
        }
    }
  
  

    /**
     * 小程序商城 禁用|解禁
     * @return array|mixed
     */
    public function actionDisabled()
    {
        $data = Yii::$app->request->get();
        $common = new CommonAppDisabled();
        $common->attributes = $data;
        $disabled = $common->disabled();

        return $disabled;
    }
     //充值记录
    public function actionRecode(){
        if(\Yii::$app->request->isPost){
            $res =[];
            $m =0;
            $t =1;
            if(\Yii::$app->admin->id == 1){

                $recode = Renew::find()->select('id,user_id,total_fee,name,content,time,status,transaction_id')->orderBy('time DESC')->all();
                foreach($recode as $k=>$v){
                    $res[$m]['id'] = $t;
                    $res[$m]['user_id'] = $v->user_id;
                    $res[$m]['total_fee'] = $v->total_fee;
                    $res[$m]['name'] = $v->name;
                    $res[$m]['content'] = $v->content;
                    $res[$m]['time'] = $v->time;
                    $res[$m]['status'] = $v->status;
                    $res[$m]['timtransaction_ide'] = $v->transaction_id;
                    $m++;
                    $t++;
                }
                return $res;
            }else{
                $recode = Renew::find()->where(['user_id'=>\Yii::$app->admin->id])->select('id,user_id,total_fee,name,content,time,status,transaction_id')->orderBy('time DESC')->all();
                foreach($recode as $k=>$v){
                    $res[$m]['id'] = $t;
                    $res[$m]['user_id'] = $v->user_id;
                    $res[$m]['total_fee'] = $v->total_fee;
                    $res[$m]['name'] = $v->name;
                    $res[$m]['content'] = $v->content;
                    $res[$m]['time'] = $v->time;
                    $res[$m]['status'] = $v->status;
                    $res[$m]['timtransaction_ide'] = $v->transaction_id;
                    $m++;
                    $t++;
                }
                return $res;
            }

        }

        return $this->render('recode');
    }
  
     //小程序商城充值
    public function actionRenew(){

        if(\Yii::$app->request->isPost){
            $type = \Yii::$app->request->post('type');
            if($type == "base_one"){
                $outTradeNo = uniqid();   //商品订单号
                $payAmount = 0.01;     //付款金额，单位:元
                $orderName = "base_one_months";  //订单标题
            }
            if($type == "base_two"){
                $outTradeNo = uniqid();   //商品订单号
                $payAmount = 0.02;     //付款金额，单位:元
                $orderName = "base_three_months";  //订单标题
            }
            if($type == "base_three"){
                $outTradeNo = uniqid();   //商品订单号
                $payAmount = 0.03;     //付款金额，单位:元
                $orderName = "base_twelve_months";  //订单标题
            }
            if($type == "all_one"){
                $outTradeNo = uniqid();   //商品订单号
                $payAmount = 0.01;     //付款金额，单位:元
                $orderName = "all_one_months";  //订单标题
            }
            if($type == "all_two"){
                $outTradeNo = uniqid();   //商品订单号
                $payAmount = 0.02;     //付款金额，单位:元
                $orderName = "all_three_months";  //订单标题
            }
            if($type == "all_three"){
                $outTradeNo = uniqid();   //商品订单号
                $payAmount = 0.03;     //付款金额，单位:元
                $orderName = "all_twelve_months";  //订单标题
            }


            $notifyUrl = 'https://www.oggogg.com/web/index.php?r=admin%2Fpassport%2Frenews';   //付款成功后的回调地址(不要有问号)
            $payTime = time();   //付款时间
            $arr = $this->createJsBizPackage($payAmount,$outTradeNo,$orderName,$notifyUrl,$payTime);
            $url = 'http://qr.liantu.com/api.php?text='.$arr['code_url'];

           $data =[];
           $data['url'] = $url;
           $data['outTradeNo'] = $arr['out_trade_no'];
           $data['payAmount'] = $payAmount;
            return $data;

        }
        $outTradeNo = uniqid();   //商品订单号
        $payAmount = 0.01;     //付款金额，单位:元
        $orderName = "base_one_months";  //订单标题
        $notifyUrl = 'https://www.oggogg.com/web/index.php?r=admin%2Fpassport%2Frenews';   //付款成功后的回调地址(不要有问号)
        $payTime = time();   //付款时间
        $arr = $this->createJsBizPackage($payAmount,$outTradeNo,$orderName,$notifyUrl,$payTime);
        $url = 'http://qr.liantu.com/api.php?text='.$arr['code_url'];
        $data =[];
        $data['url'] = $url;
        $data['outTradeNo'] = $arr['out_trade_no'];
        $data['type'] = "base_one";
        $data['payAmount'] = $payAmount;
        return $this->render('renew',['data'=>$data]);
    }


    /**
     * 发起订单
     * @param float $totalFee 收款总费用 单位元
     * @param string $outTradeNo 唯一的订单号
     * @param string $orderName 订单名称
     * @param string $notifyUrl 支付结果通知url 不要有问号
     * @param string $timestamp 订单发起时间
     * @return array
     */
    public function createJsBizPackage($totalFee, $outTradeNo, $orderName, $notifyUrl, $timestamp)
    {
        $config = array(
            'mch_id' => '1513949871',  //微信支付商户号 PartnerID 通过微信支付商户资料审核后邮件发送
            'appid' => 'wxdb3f28ee363c2365',//公众号APPID 通过微信支付商户资料审核后邮件发送
            'key' => 'qwejhhe5yqqwewesduyqqwejhheuyq32', //https://pay.weixin.qq.com 帐户设置-安全设置-API安全-API密钥-设置API密钥
        );

        $orderName = iconv('GBK','UTF-8',$orderName);


        $unified = array(
            'appid' => $config['appid'],
            'attach' => 'pay',       //商家数据包，原样返回，如果填写中文，请注意转换为utf-8
            'body' => $orderName,
            'mch_id' => $config['mch_id'],
            'nonce_str' => self::createNonceStr(),
            'notify_url' => $notifyUrl,
            'out_trade_no' => $outTradeNo,
            'spbill_create_ip' => '192.168.31.132',
            'total_fee' => intval($totalFee * 100),    //单位 转为分
            'trade_type' => 'NATIVE',
        );
        $unified['sign'] = self::getSign($unified, $config['key']);

        $responseXml = self::curlPost('https://api.mch.weixin.qq.com/pay/unifiedorder', self::arrayToXml($unified));

        $unifiedOrder = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        if ($unifiedOrder === false) {
            die('parse xml error');
        }
        if ($unifiedOrder->return_code != 'SUCCESS') {
            die($unifiedOrder->return_msg);
        }
        if ($unifiedOrder->result_code != 'SUCCESS') {
            die($unifiedOrder->err_code);
        }
        $codeUrl = (array)($unifiedOrder->code_url);
        if(!$codeUrl[0]) exit('get code_url error');
        $arr = array(
            "appId" => $config['appid'],
            "timeStamp" => $timestamp,
            "nonceStr" => self::createNonceStr(),
            "package" => "prepay_id=" . $unifiedOrder->prepay_id,
            "signType" => 'MD5',
            "code_url" => $codeUrl[0],
            'out_trade_no' => $outTradeNo,

        );
        $arr['paySign'] = self::getSign($arr, $config['key']);
;
        return $arr;
    }


    public function notify()
    {
        $config = array(
            'mch_id' => '1513949871',  //微信支付商户号 PartnerID 通过微信支付商户资料审核后邮件发送
            'appid' => 'wxdb3f28ee363c2365',//公众号APPID 通过微信支付商户资料审核后邮件发送
            'key' => 'qwejhhe5yqqwewesduyqqwejhheuyq32', //https://pay.weixin.qq.com 帐户设置-安全设置-API安全-API密钥-设置API密钥
        );
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($postObj === false) {
            die('parse xml error');
        }
        if ($postObj->return_code != 'SUCCESS') {
            die($postObj->return_msg);
        }
        if ($postObj->result_code != 'SUCCESS') {
            die($postObj->err_code);
        }
        $arr = (array)$postObj;
        unset($arr['sign']);
        if (self::getSign($arr, $config['key']) == $postObj->sign) {
            echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
            return $postObj;
        }
    }

    /**
     * curl get
     *
     * @param string $url
     * @param array $options
     * @return mixed
     */
    public static function curlGet($url = '', $options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
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

    public static function createNonceStr($length = 32)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
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


    //提现中心
    public function actionRefer(){
        if (\Yii::$app->request->isPost){
            if(\Yii::$app->request->post('code')){
                $res =[];
                $m =0;
                $t =1;
                $data = Sure::find()->where(['user_id'=>\Yii::$app->admin->id])->select('username,total,time,status,sure_name,sure_time,sure_status,weixin')->all();
                foreach($data as $k=>$v){
                    $res[$m]['id'] = $t;
                    $res[$m]['username'] = $v->username;
                    $res[$m]['total'] = $v->total."元";
                    $res[$m]['time'] = date('Y-m-d H:m:s',$v->time);
                    if($v->status == 1){
                        $res[$m]['status'] = "正在审核中";
                    }elseif ($v->status == 2){
                        $res[$m]['status'] = "提现成功";
                    }elseif ($v->status == 3){
                        $res[$m]['status'] = "提现失败";
                    }
                    if(!$v->weixin){
                    	 $res[$m]['weixin'] = "无";
                    }else{
                    	 $res[$m]['weixin'] = $v->weixin;	
                    }
                  
                    if(!$v->sure_name){
                        $res[$m]['sure_name'] = "无";
                    }else{
                        $res[$m]['sure_name'] = $v->sure_name;
                    }
                    if(!$v->sure_time){
                        $res[$m]['sure_time'] = "无";
                    }else{
                        $res[$m]['sure_time'] = date('Y-m-d H:m:s',$v->sure_time);
                    }

                    $m++;
                    $t++;
                }
                return $res;
            }
          
             if(\Yii::$app->request->post('weixin_name')){
                if(Admin::updateAll(['weixin'=>\Yii::$app->request->post('weixin_name')],['id'=>\Yii::$app->admin->id])){
                    return 0;
                }else{
                    return 1;
                }
            }
            $admin = Admin::find()->where(['id'=>\Yii::$app->admin->id])->one();
            $data = Distribution::find()->where(['father_id'=>\Yii::$app->admin->id])->all();
            $sure = new Sure();
            $sure->user_id = \Yii::$app->admin->id;
            $sure->username = $admin->username;
            $sure->time = time();
            $sure->status = 1;
			$sure->weixin = $admin->weixin;
            foreach ($data as $k=>$v){
                $renew = Renew::find()->where(['user_id'=>$v->son_id,'one_royalty'=>'未提现'])->all();
                   foreach ($renew as $key=>$value){
                       $sure->total += $value->one_rate;
                    //   Renew::updateAll(['one_royalty'=>'提现中'],['transaction_id'=>$value->transaction_id]);
                   }

            }
            foreach ($data as $k=>$v){
                $two = Distribution::find()->where(['father_id'=>$v->son_id])->all();
                foreach ($two as $key=>$value){
                    $renews = Renew::find()->where(['user_id'=>$value->son_id,'two_royalty'=>'未提现'])->all();
                    foreach ($renews as $keys=>$values){
                        $sure->total += $values->two_rate;
                      //  Renew::updateAll(['two_royalty'=>'提现中'],['transaction_id'=>$values->transaction_id]);
                    }
                }

            }
            $r = Admin::find()->where(['id'=>\Yii::$app->admin->id])->one();
            $r->royaltying = $r->royaltying + $sure->total;
            $r->save();
            if($sure->total < 0.01){
                $re['code'] = 0;
                return $re;
            }else{
                $sure->insert();
                $id = $sure->attributes['id'];
                foreach ($data as $k=>$v){
                    $renew = Renew::find()->where(['user_id'=>$v->son_id,'one_royalty'=>'未提现'])->all();
                    foreach ($renew as $key=>$value){
                    
                        Renew::updateAll(['one_royalty'=>'提现中','one_refer_id'=>$id],['transaction_id'=>$value->transaction_id]);
                    }

                }
                foreach ($data as $k=>$v){
                    $two = Distribution::find()->where(['father_id'=>$v->son_id])->all();
                    foreach ($two as $key=>$value){
                        $renews = Renew::find()->where(['user_id'=>$value->son_id,'two_royalty'=>'未提现'])->all();
                        foreach ($renews as $keys=>$values){
                         
                              Renew::updateAll(['two_royalty'=>'提现中','two_refer_id'=>$id],['transaction_id'=>$values->transaction_id]);
                        }
                    }

                }
                $re['code'] = 1;
                $re['royaltying'] = $r->royaltying;
                return $re;
            }

        }

        $data = Admin::find()->where(['id'=>\Yii::$app->admin->id])->one();

        return $this->render('refer',['royalty'=>$data->royalty,'royaltyed'=>$data->royaltyed,'royaltying'=>$data->royaltying,'royaltys'=> round( $data->royalty - $data->royaltying - $data->royaltyed,2),'weixin'=>$data->weixin]);
    }

    //分享人员
    public function actionPeople()
    {

        if (\Yii::$app->request->isPost) {
         
            $res = [];
            $t = 0;
            $m = 1;
            $search = \Yii::$app->request->post('search');

            if(\Yii::$app->admin->id == 1){
                if($search){
                    $data = Distribution::find()->where(['like', 'son_name', $search])->orderBy('addtime DESC')->all();

                }else{
                    $data = Distribution::find()->orderBy('addtime DESC')->all();
                }

                foreach ($data as $k => $v){
                    $res[$t]['id'] = $m;
                    $res[$t]['name'] = $v->son_name;
                    $res[$t]['user_level'] = "无";
                    $res[$t]['time'] = date('Y-m-d H:m:s', $v->addtime);
                    $a = Admin::find()->where(['id' => $v->son_id])->one();
                    if ($a->level == 0) {
                        $res[$t]['account_level'] = "免费会员";
                    } elseif ($a->level == 1) {
                        $res[$t]['account_level'] = "基础会员";
                    } elseif ($a->level == 2) {
                        $res[$t]['account_level'] = "全能会员";
                    }
                    if (($a->expire_time - time()) < 0) {
                        $res[$t]['status'] = "免费状态";
                    } elseif ($a->expire_time == 0) {
                        $res[$t]['status'] = "长期正常状态";
                    } elseif (($a->expire_time - time()) > (7 * 24 * 60 * 60)) {
                        $res[$t]['status'] = "正常状态";
                    } elseif ((($a->expire_time - time()) < (7 * 24 * 60 * 60)) && (($a->expire_time - time()) > 0)) {
                        $res[$t]['status'] = "即将到期状态";
                    }
                    if ($a->release_xcx == 0) {
                        $res[$t]['release'] = "未发布";
                    } elseif ($a->release_xcx == 1) {
                        $res[$t]['release'] = "发布成功";
                    }

                    $t++;
                    $m++;

                }
                return $res;
            }
            if($search){

                $one = Distribution::find()->where(['father_id' => \Yii::$app->admin->id])->andWhere(['like', 'son_name', $search])->orderBy('addtime DESC')->all();

            }else{
                $one = Distribution::find()->where(['father_id' => \Yii::$app->admin->id])->orderBy('addtime DESC')->all();
            }


             foreach ($one as $k => $v) {

                $res[$t]['id'] = $m;
                $res[$t]['name'] = $v->son_name;
                $res[$t]['user_level'] = "一级";
                $res[$t]['time'] = date('Y-m-d H:m:s', $v->addtime);
                $a = Admin::find()->where(['id' => $v->son_id])->one();
                if ($a->level == 0) {
                    $res[$t]['account_level'] = "免费会员";
                } elseif ($a->level == 1) {
                    $res[$t]['account_level'] = "基础会员";
                } elseif ($a->level == 2) {
                    $res[$t]['account_level'] = "全能会员";
                }
                if (($a->expire_time - time()) < 0) {
                    $res[$t]['status'] = "免费状态";
                } elseif ($a->expire_time == 0) {
                    $res[$t]['status'] = "长期正常状态";
                } elseif (($a->expire_time - time()) > (7 * 24 * 60 * 60)) {
                    $res[$t]['status'] = "正常状态";
                } elseif ((($a->expire_time - time()) < (7 * 24 * 60 * 60)) && (($a->expire_time - time()) > 0)) {
                    $res[$t]['status'] = "即将到期状态";
                }
                if ($a->release_xcx == 0) {
                    $res[$t]['release'] = "未发布";
                } elseif ($a->release_xcx == 1) {
                    $res[$t]['release'] = "发布成功";
                }

                $t++;
                $m++;

            }
    		if($search){

                $one = Distribution::find()->where(['father_id' => \Yii::$app->admin->id])->orderBy('addtime DESC')->all();

            }
          
            foreach ($one as $k => $v) {
                if($search){

                    $two = Distribution::find()->where(['father_id' => $v->son_id])->select('id,son_name,son_id,addtime')->andWhere(['like', 'son_name', $search])->orderBy('addtime DESC')->all();
                }else{
                    $two = Distribution::find()->where(['father_id' => $v->son_id])->select('id,son_name,son_id,addtime')->orderBy('addtime DESC')->all();
                }
		
                foreach ($two as $key => $value) {
                  
                    $res[$t]['id'] = $m;
                    $res[$t]['name'] = $value->son_name;
                    $res[$t]['user_level'] = "二级";
                    $res[$t]['time'] = date('Y-m-d H:m:s', $value->addtime);
                    $a = Admin::find()->where(['id' => $value->son_id])->one();
                    if ($a->level == 0) {
                        $res[$t]['account_level'] = "免费会员";
                    } elseif ($a->level == 1) {
                        $res[$t]['account_level'] = "基础会员";
                    } elseif ($a->level == 2) {
                        $res[$t]['account_level'] = "全能会员";
                    }
                    if (($a->expire_time - time()) < 0) {
                        $res[$t]['status'] = "免费状态";
                    } elseif ($a->expire_time == 0) {
                        $res[$t]['status'] = "长期正常状态";
                    } elseif (($a->expire_time - time()) > (7 * 24 * 60 * 60)) {
                        $res[$t]['status'] = "正常状态";
                    } elseif ((($a->expire_time - time()) < (7 * 24 * 60 * 60)) && (($a->expire_time - time()) > 0)) {
                        $res[$t]['status'] = "即将到期状态";
                    }
                    if ($a->release_xcx == 0) {
                        $res[$t]['release'] = "未发布";
                    } elseif ($a->release_xcx == 1) {
                        $res[$t]['release'] = "发布成功";
                    }
                    $t++;
                    $m++;
                }
                 
            }
         return $res;

        }
        $sale_id = str_pad(\Yii::$app->admin->id,5,0,STR_PAD_LEFT);

        $sale_id = base64_encode($sale_id);
        $url = "https://www.oggogg.com/web/index.php?r=admin%2Fpassport%2Flogin&sale_id=".$sale_id."";
        return $this->render('people',['url'=> $url]);
    }

       //人员交易记录表
        public function actionPeoples(){
	

        if (\Yii::$app->request->isPost){
          
            $search = \Yii::$app->request->post('search');
            $res = [];
            $t = 0;
            $m = 1;
            if(\Yii::$app->admin->id == 1){
                if($search){
                    $recode = Renew::find()->where(['like', 'name', $search])->orderBy('time DESC')->all();
                }else{
                    $recode = Renew::find()->orderBy('time DESC')->all();
                }

                foreach($recode as $key => $value){
                    $res[$t]['id'] = $m;
                    $res[$t]['name'] = $value->name;
                    $res[$t]['total_fee'] = $value->total_fee."元";
                    $res[$t]['content'] = $value->content;
                    $res[$t]['one_rate'] = $value->one_rate."元";
                    $res[$t]['two_rate'] = $value->two_rate."元";
                    $res[$t]['time'] = $value->time;
                    $res[$t]['status'] = $value->status;
                    $res[$t]['transaction_id'] = $value->transaction_id;
                    $res[$t]['one_royalty'] = $value->one_royalty;
                    $res[$t]['two_royalty'] = $value->two_royalty;
                    $t++;
                    $m++;
                }
                $res['a'] = "admin";
                return $res;
            }
            if($search){

                $one = Distribution::find()->where(['father_id' => \Yii::$app->admin->id])->andWhere(['like', 'son_name', $search])->all();
            }else{
                $one = Distribution::find()->where(['father_id' => \Yii::$app->admin->id])->all();
            }



            foreach ($one as $k => $v){
                if($search){
                    $recode = Renew::find()->where(['user_id'=>$v->son_id])->andWhere(['like', 'name', $search])->orderBy('time DESC')->all();
                }else{
                    $recode = Renew::find()->where(['user_id'=>$v->son_id])->orderBy('time DESC')->all();
                }

                foreach($recode as $key => $value){
                    $res[$t]['id'] = $m;
                    $res[$t]['name'] = $value->name;
                    $res[$t]['total_fee'] = $value->total_fee;
                    $res[$t]['content'] = $value->content;
                    $res[$t]['rate'] = $value->one_rate;
                    $res[$t]['time'] = $value->time;
                    $res[$t]['status'] = $value->status;
                    $res[$t]['transaction_id'] = $value->transaction_id;
                    $res[$t]['royalty'] = $value->one_royalty;
                    $t++;
                    $m++;
                }

            }

           if($search){
                    $one = Distribution::find()->where(['father_id' => \Yii::$app->admin->id])->all();
                }
          
            foreach ($one as $key => $value) {

                if($search){
                    $two = Distribution::find()->where(['father_id' => $value->son_id])->andWhere(['like', 'son_name', $search])->orderBy('addtime DESC')->all();

                }else{
                    $two = Distribution::find()->where(['father_id' => $value->son_id])->orderBy('addtime DESC')->all();
                }
                foreach($two as $k => $v){
                    $recode = Renew::find()->where(['user_id'=>$v->son_id])->orderBy('time DESC')->all();
                    foreach($recode as $keys => $values){
                        $res[$t]['id'] = $m;
                        $res[$t]['name'] = $values->name;
                        $res[$t]['total_fee'] = $values->total_fee;
                        $res[$t]['content'] = $values->content;
                        $res[$t]['rate'] = $values->two_rate;
                        $res[$t]['time'] = $values->time;
                        $res[$t]['status'] = $values->status;
                        $res[$t]['transaction_id'] = $values->transaction_id;
                        $res[$t]['royalty'] = $values->two_royalty;
                        $t++;
                        $m++;
                    }
                }
            }

            return $res;
        }
    }

    //分销提成设置
    public function actionRoyalty(){
        if(\Yii::$app->request->isPost){
            $one = \Yii::$app->request->post('one');
            $two = \Yii::$app->request->post('two');
            if(Admin::updateAll(['royalty_one'=>$one,'royalty_two'=>$two],['id'=>\Yii::$app->admin->id])){
                return 0;
            }
        }
        $data = Admin::find()->where(['id'=>\Yii::$app->admin->id])->one();
        return $this->render('royalty',['one'=>$data->royalty_one,'two'=>$data->royalty_two]);
    }


    //提现审核
    public function actionSure(){

        if (\Yii::$app->request->isPost){
            $id = \Yii::$app->request->post('code');
            $type = \Yii::$app->request->post('type');
          
            $sure = Sure::find()->where(['id'=>$id])->one();
            $sure->sure_id = \Yii::$app->admin->id;
            $sure->sure_name = \Yii::$app->admin->identity->username;
            $sure->sure_time = time();
                 $admin = Admin::find()->where(['id'=>$sure->user_id])->one();
            if($type == 1){           //通过
                $sure->sure_status = 2;
              	$sure->status = 2;
                Renew::updateAll(['one_royalty'=>"已提现"],['one_refer_id'=>$id]);
                Renew::updateAll(['two_royalty'=>"已提现"],['two_refer_id'=>$id]);
				 $admin->royaltyed =  $admin->royaltyed + $sure->total;
                $admin->royaltying = $admin->royaltying  - $sure->total;
            }else{
                $sure->sure_status = 3;
                $sure->status = 3;
                Renew::updateAll(['one_royalty'=>"未提现"],['one_refer_id'=>$id]);
                Renew::updateAll(['two_royalty'=>"未提现"],['two_refer_id'=>$id]);
               $admin->royaltying = $admin->royaltying  - $sure->total;
            }
	
          	$admin->save();
         	 $sure->save();
            
             return 1;
          
//            $res = $this->sendMoney('1','oUqK-5w3bRxW4jKrv0SvfCabNn7E','aaa','');
//           return $res;
        }else{
      		  $search = \Yii::$app->request->get('search');
              $sure_status = \Yii::$app->request->get('sure_status');
              if(!$search){
                  $search = '';
              }
               if(!$sure_status){
                  $sure_status = 4;
              }
              if($sure_status == 4){
                  $query = Sure::find()->where(['like', 'username', $search]);
              }else{
                  $query = Sure::find()->where(['sure_status'=>$sure_status])->andWhere(['like', 'username', $search]);
              }

                  $countQuery = clone $query;
                  $pages = new Pagination(['totalCount'=>$countQuery->count(),'defaultPageSize'=>10,'validatePage' => false,]);//设置数据总量与每页数据大小（几条)
                  $data = $query->offset($pages->offset)//偏移量
                  ->limit($pages->limit)//此处获取的就是pageSize的值
                  ->all();

            return $this->render('sure',['data'=>$data,'pages'=>$pages,'search'=>$search,'sure_status'=>$sure_status]);
      	}
      

    }
    //发布小程序记录表
    public function actionXcx(){
   
      if (\Yii::$app->request->isPost){
            $res = [];
            $t = 0;
            $m = 1;
            if(\Yii::$app->admin->id == 1) {
                $recode = Release::find()->all();
            }else{
                $recode = Release::find()->where(['father_id' => \Yii::$app->admin->id])->all();
            }  
                foreach($recode as $key => $value){
                    $res[$t]['id'] = $m;
                    $res[$t]['name'] = $value->name;
                    if($value->status == 1){
                        $res[$t]['status'] = "发布成功";
                        $res[$t]['time'] = date('Y-m-d H:m:s', $value->time);
                    }else{
                        $res[$t]['status'] = "未发布";
                        $res[$t]['time'] = "无";
                    }
                    if($value->reward == 1){
                        $res[$t]['reward'] = "已发放";
                    }else{
                        $res[$t]['reward'] = "已满或未发放";
                    }
                    $t++;
                    $m++;
                }
                return $res;
        }
    }


    function unicode() {
        $str = uniqid(mt_rand(),1);
        $str=sha1($str);
        return md5($str);
    }

    function xmltoarray($xml) {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring),true);
        return $val;
    }
    function arraytoxmls($data){
        $str='<xml>';
        foreach($data as $k=>$v) {
            $str.='<'.$k.'>'.$v.'</'.$k.'>';
        }
        $str.='</xml>';
        return $str;
    }

    function curl($param="",$url) {

        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();                                      //初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);                 //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);                    //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);            //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);                      //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);           // 增加 HTTP Header（头）里的字段
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch,CURLOPT_SSLCERT,ROOT_PATH .'/statics/cert/apiclient_cert.pem'); //这个是证书的位置绝对路径
        curl_setopt($ch,CURLOPT_SSLKEY,Yii::$app->request->baseUrl .'/statics/cert/apiclient_key.pem'); //这个也是证书的位置绝对路径
        $data = curl_exec($ch);                                 //运行curl
        curl_close($ch);
        return $data;
    }
    function sendMoney($amount,$re_openid,$desc='测试',$check_name=''){

        $total_amount = (100) * $amount;

        $data=array(
            'mch_appid'=> 'wxe1133d01d0698c83' ,//商户账号appid
            'mchid'=> '1516596731',//商户号
            'nonce_str'=>$this->createNoncestr(),//随机字符串
            'partner_trade_no'=> date('YmdHis').rand(1000, 9999),//商户订单号
            'openid'=> $re_openid,//用户openid
            'check_name'=>'NO_CHECK',//校验用户姓名选项,
            're_user_name'=> $check_name,//收款用户姓名
            'amount'=>$total_amount,//金额
            'desc'=> $desc,//企业付款描述信息
            'spbill_create_ip'=> '127.0.0.1',//Ip地址
        );
        $secrect_key='e0bff57b6c07f5d7d8cdd44b846dc659';///这个就是个API密码。MD5 32位。
        $data=array_filter($data);
        ksort($data);
        $str='';
        foreach($data as $k=>$v) {
            $str.=$k.'='.$v.'&';
        }
        $str.='key='.$secrect_key;
        $data['sign']=md5($str);
        $xml= $this->arraytoxmls($data);

        $url='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers'; //调用接口
        $res= $this->curl($xml,$url);

        $return= $this->xmltoarray($res);


//        print_r($return);
        //返回来的结果
        // [return_code] => SUCCESS [return_msg] => Array ( ) [mch_appid] => wxd44b890e61f72c63 [mchid] => 1493475512 [nonce_str] => 616615516 [result_code] => SUCCESS [partner_trade_no] => 20186505080216815
        // [payment_no] => 1000018361251805057502564679 [payment_time] => 2018-05-15 15:29:50


        $responseObj = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);
        echo $res= $responseObj->return_code;  //SUCCESS  如果返回来SUCCESS,则发生成功，处理自己的逻辑

        return $res;
    }
	    /**
     * 第一次进入小程序后台初始化首页布局
     */
    public function actionAdd($store_id){
        $homeNav = new HomeNav();
        $store = new Store();
        $banner = new Banner();
        $homeBlock = new HomeBlock();
        $notice = new Option();
        $coupon = new Coupon();
        $cat = new Cat();
        $goods = new Goods();
        $goodsCat = new GoodsCat();
        $goodsPic = new GoodsPic();
        $yyCat = new YyCat();
        $yyGoods = new YyGoods();
        $yyPic = new YyGoodsPic();
        $yySetting = new YySetting();
        $shop = new Shop();
        $shopPic = new ShopPic();
        $video = new Video();
        $ptCat = new PtCat();
        $ptGoods = new PtGoods();
        $ptGoodsPic = new PtGoodsPic();
        $ptSetting = new PtSetting();
        if (
            $homeNav->findOne(['store_id' => $store_id]) ||
            $store->findOne(['id' => $store_id])->home_page_module ||
            $banner->findOne(['store_id' => $store_id]) ||
            $homeBlock->findOne(['store_id' => $store_id]) ||
            $notice->findOne(['store_id' => $store_id])->name == 'notice' ||
            $coupon->findOne(['store_id' => $store_id]) ||
            $goods->findOne(['store_id' => $store_id]) ||
            $goodsCat->findOne(['store_id' => $store_id]) ||
            $shop->findOne(['store_id' => $store_id]) ||
            $video->findOne(['store_id' => $store_id])
//            $ptCat->findOne(['store_id' => $store_id]) ||
//            $ptGoods->findOne(['store_id' => $store_id]) ||
//            $ptSetting->findOne(['store_id' => $store_id])
        ){
            return true;
        }
        $time = time();


        $transaction=Yii::$app->db->beginTransaction();
        try{

            $goodsItem = $goods->find()->where(['store_id'=>1])->all();
            foreach ($goodsItem as $v){
                //初始化goods表
                $goods = new Goods();
                $goods->store_id = $store_id;
                $goods->name = $v->name;
                $goods->price = $v->price;
                $goods->original_price = $v->original_price;
                $goods->detail = $v->detail;
                $goods->cat_id = $v->cat_id;
                $goods->status = $v->status;
                $goods->addtime = $v->addtime;
                $goods->is_delete = $v->is_delete;
                $goods->attr = $v->attr;
                $goods->service = $v->service;
                $goods->sort = $v->sort;
                $goods->virtual_sales = $v->virtual_sales;
                $goods->cover_pic = $v->cover_pic;
                $goods->video_url = $v->video_url;
                $goods->unit = $v->unit;
                $goods->individual_share = $v->individual_share;
                $goods->share_commission_first = $v->share_commission_first;
                $goods->share_commission_second = $v->share_commission_second;
                $goods->share_commission_third = $v->share_commission_third;
                $goods->weight = $v->weight;
                $goods->freight = $v->freight;
                $goods->full_cut = $v->full_cut;
                $goods->integral = $v->integral;
                $goods->use_attr = $v->use_attr;
                $goods->share_type = $v->share_type;
                $goods->quick_purchase = $v->quick_purchase;
                $goods->hot_cakes = $v->hot_cakes;
                $goods->cost_price = $v->cost_price;
                $goods->member_discount = $v->member_discount;
                $goods->rebate = $v->rebate;
                $goods->mch_id = $v->mch_id;
                $goods->goods_num = $v->goods_num;
                $goods->mch_sort = $v->mch_sort;
                $goods->confine_count = $v->confine_count;
                $goods->is_level = $v->is_level;
                $goods->type = $v->type;
                $goods->is_negotiable = $v->is_negotiable;
                $goods->attr_setting_type = $v->attr_setting_type;
                $goods->good_same_id = $v->id;

                $goods->save();
                //初始化goods_pic
                $good_id = $goods->attributes['id'];
                $goodsPicItem = $goodsPic->find()->where(['goods_id'=>$v->id])->all();
                foreach ($goodsPicItem as $val1){
                    $goodsPic = new GoodsPic();
                    $goodsPic->goods_id = $good_id;
                    $goodsPic->pic_url = $val1->pic_url;
                    $goodsPic->is_delete = $val1->is_delete;
                    $goodsPic->save();
                }

                //初始化分类关联
                $goodsCatItem = $goodsCat->find()->where(['goods_id'=>$v->id,'store_id'=>1])->all();
                foreach ($goodsCatItem as $val2){
                    $goodsCat = new GoodsCat();
                    $goodsCat->goods_id = $good_id;
                    $goodsCat->store_id = $store_id;
                    $goodsCat->cat_id = $val2->cat_id;
                    $goodsCat->addtime = $val2->addtime;
                    $goodsCat->is_delete = $val2->is_delete;
                    $goodsCat->save();
                }

            }

            //banner添加首页及拼团轮播图
            $bannerItem = $banner->find()->where(['store_id'=>1])->all();
            foreach ($bannerItem as $val){
                $banner = new Banner();
                $banner->store_id = $store_id;
                $banner->pic_url = $val->pic_url;
                $banner->title = $val->title;
                $banner->page_url = $val->page_url;
                $banner->sort = $val->sort;
                $banner->addtime = $time;
                $banner->is_delete = $val->is_delete;
                $banner->type = $val->type;
                $banner->open_type = $val->open_type;
                $banner->save();
            }

            //homeNav添加导航栏
            $homeNavItem = $homeNav->find()->where(['store_id'=>1])->all();
            foreach ($homeNavItem as $val){
                $homeNav = new HomeNav();
                $homeNav->store_id = $store_id;
                $homeNav->name = $val->name;
                $homeNav->url = $val->url;
                $homeNav->open_type = $val->open_type;
                $homeNav->pic_url = $val->pic_url;
                $homeNav->addtime = $time;
                $homeNav->sort = $val->sort;
                $homeNav->is_delete = $val->is_delete;
                $homeNav->is_hide = $val->is_hide;
                $homeNav->save();
            }

            //homeblock添加图片魔方
            $homeBlockOne = $homeBlock->find()->where(['store_id'=>1])->orderBy('id DESC')->one();
            $homeBlock->store_id = $store_id;
            $homeBlock->name = $homeBlockOne->name;
            $homeBlock->data = $homeBlockOne->data;
            $homeBlock->addtime = $time;
            $homeBlock->is_delete = 0;
            $homeBlock->style = 0;
            $homeBlock->save();
            $homeBlockId = $homeBlock->attributes['id'];

            //coupon添加优惠券
            $couponItem = $coupon->find()->where(['store_id'=>1])->all();
            foreach ($couponItem as $val){
                $coupon = new Coupon();
                $coupon->store_id = $store_id;
                $coupon->name = $val->name;
                $coupon->desc = $val->desc;
                $coupon->pic_url = $val->pic_url;
                $coupon->discount_type = $val->discount_type;
                $coupon->min_price = $val->min_price;
                $coupon->sub_price = $val->sub_price;
                $coupon->discount = $val->discount;
                $coupon->expire_type = $val->expire_type;
                $coupon->expire_day = $val->expire_day;
                $coupon->begin_time = $time;
                $coupon->end_time = $time+86400*5;
                $coupon->addtime = $time;
                $coupon->is_delete = $val->is_delete;
                $coupon->total_count = $val->total_count;
                $coupon->is_join = $val->is_join;
                $coupon->sort = $val->sort;
                $coupon->cat_id_list = $val->cat_id_list;
                $coupon->appoint_type = $val->appoint_type;
                $coupon->is_integral = $val->is_integral;
                $coupon->integral = $val->integral;
                $coupon->price = $val->price;
                $coupon->total_num = $val->total_num;
                $coupon->user_num = $val->user_num;
                $coupon->rule = $val->rule;
                $coupon->goods_id_list = '';
                $coupon->save();
            }


            //store添加首页模块布局及配置
            $storeOne = $store->findOne($store_id);
            $storeFirst = $store->findOne(1);
            $cat1_id = $cat->find()->where(['store_id'=>1,'parent_id'=>0,'is_delete'=>0,'is_show'=>1])->one();
            $storeOne->home_page_module = '[{"name":"notice"},{"name":"search"},{"name":"banner"},{"name":"nav"},{"name":"topic"},{"name":"block-'.$homeBlockId.'"},{"name":"coupon"},{"name":"mch"},{"name":"pintuan"},{"name":"yuyue"},{"name":"single_cat-'.$cat1_id.'"}]';
            $storeOne->show_customer_service = $storeFirst->show_customer_service;
            $storeOne->after_sale_time = $storeFirst->after_sale_time;
            $storeOne->cat_style = $storeFirst->cat_style;
            $storeOne->cat_goods_cols = $storeFirst->cat_goods_cols;
            $storeOne->is_offline = $storeFirst->is_offline;
            $storeOne->is_coupon = $storeFirst->is_coupon;
            $storeOne->send_type = $storeFirst->send_type;
    //        $storeOne->nav_count = 1;
            $storeOne->integration = $storeFirst->integration;
            $storeOne->dial = $storeFirst->dial;
            $storeOne->dial_pic = $storeFirst->dial_pic;
            $storeOne->cut_thread = $storeFirst->cut_thread;
            $storeOne->purchase_frame = $storeFirst->purchase_frame;
            $storeOne->is_recommend = $storeFirst->is_recommend;
            $storeOne->recommend_count = $storeFirst->recommend_count;

            $storeOne->update();

    //        $shop添加门店
            $shopItem = $shop->find()->where(['store_id'=>1])->all();
            foreach ($shopItem as $val){
                $shop = new Shop();
                $shop->store_id     = $store_id;
                $shop->name         = $val->name;
                $shop->mobile       = $val->mobile;
                $shop->address      = $val->address;
                $shop->is_delete    = $val->is_delete;
                $shop->addtime      = $val->addtime;
                $shop->longitude    = $val->longitude;
                $shop->latitude     = $val->latitude;
                $shop->score        = $val->score;
                $shop->cover_url    = $val->cover_url;
                $shop->pic_url      = $val->pic_url;
                $shop->shop_time    = $val->shop_time;
                $shop->content      = $val->content;
                $shop->is_default   = $val->is_default;
                $shop->save();
            }

    //        shopPic添加门店图片


    //        video添加视频专区
            $videoItem = $video->find()->where(['store_id'=>1])->all();
            foreach ($videoItem as $val){
                $video = new Video();
                $video->title = $val->title;
                $video->url = $val->url;
                $video->sort = $val->sort;
                $video->is_delete = $val->is_delete;
                $video->addtime = $time;
                $video->store_id = $store_id;
                $video->pic_url = $val->pic_url;
                $video->content = $val->content;
                $video->type = $val->type;
                $video->save();
            }

    //        $ptCat添加拼团分类
//            $ptCat1 = $ptCat->findOne(1);
//            $ptCat->name      = $ptCat1->name;
//            $ptCat->store_id  = $store_id;
//            $ptCat->pic_url   = $ptCat1->pic_url;
//            $ptCat->sort      = $ptCat1->sort;
//            $ptCat->addtime   = $ptCat1->addtime;
//            $ptCat->is_delete = $ptCat1->is_delete;
//            $ptCat->save();
//            $ptCatId = $ptCat->id;
//            $ptCat2 = $ptCat->findOne(2);
//            Yii::$app->db->createCommand()->batchInsert(PtCat::tableName(), ['name','store_id','pic_url','sort','addtime','is_delete'], [
//                [$ptCat2->name,$store_id,$ptCat2->pic_url,$ptCat2->sort,$ptCat2->addtime,$ptCat2->is_delete],
//            ])->execute();

    //        $ptGoods添加拼团商品
//            $ptGoods1 = $ptGoods->findOne(2);
//            $ptGoods->store_id      = $store_id;
//            $ptGoods->name           = $ptGoods1->name;
//            $ptGoods->original_price = $ptGoods1->original_price;
//            $ptGoods->price          = $ptGoods1->price;
//            $ptGoods->detail         = $ptGoods1->detail;
//            $ptGoods->cat_id         = $ptCatId;
//            $ptGoods->status         = $ptGoods1->status;
//            $ptGoods->grouptime      = $ptGoods1->grouptime;
//            $ptGoods->attr           = $ptGoods1->attr;
//            $ptGoods->service        = $ptGoods1->service;
//            $ptGoods->sort           = $ptGoods1->sort;
//            $ptGoods->virtual_sales  = $ptGoods1->virtual_sales;
//            $ptGoods->cover_pic      = $ptGoods1->cover_pic;
//            $ptGoods->weight         = $ptGoods1->weight;
//            $ptGoods->freight        = $ptGoods1->freight;
//            $ptGoods->unit           = $ptGoods1->unit;
//            $ptGoods->addtime        = $ptGoods1->addtime;
//            $ptGoods->is_delete      = $ptGoods1->is_delete;
//            $ptGoods->group_num      = $ptGoods1->group_num;
//            $ptGoods->is_hot         = $ptGoods1->is_hot;
//            $ptGoods->limit_time     = $ptGoods1->limit_time;
//            $ptGoods->is_only        = $ptGoods1->is_only;
//            $ptGoods->is_more        = $ptGoods1->is_more;
//            $ptGoods->colonel        = $ptGoods1->colonel;
//            $ptGoods->buy_limit      = $ptGoods1->buy_limit;
//            $ptGoods->type           = $ptGoods1->type;
//            $ptGoods->use_attr       = $ptGoods1->use_attr;
//            $ptGoods->one_buy_limit  = $ptGoods1->one_buy_limit;
//            $ptGoods->payment        = $ptGoods1->payment;
//            $ptGoods->save();
//            $ptGoodsId = $ptGoods->id;
//            $ptGoods2 = $ptGoods->findOne(3);
//            $ptGoods3 = $ptGoods->findOne(4);
//            $ptGoods4 = $ptGoods->findOne(9);
//            Yii::$app->db->createCommand()->batchInsert(PtGoods::tableName(), ['store_id','name','original_price','price','detail','cat_id','status','grouptime','attr','service','sort','virtual_sales','cover_pic','weight','freight','unit','addtime','is_delete','group_num','is_hot','limit_time','is_only','is_more','colonel','buy_limit','type','use_attr','one_buy_limit','payment'], [
//                [$store_id,$ptGoods2->name,$ptGoods2->original_price,$ptGoods2->price,$ptGoods2->detail,($ptCatId + 1),$ptGoods2->status,$ptGoods2->grouptime,$ptGoods2->attr,$ptGoods2->service,$ptGoods2->sort,$ptGoods2->virtual_sales,$ptGoods2->cover_pic,$ptGoods2->weight,$ptGoods2->freight,$ptGoods2->unit,$ptGoods2->addtime,$ptGoods2->is_delete,$ptGoods2->group_num,$ptGoods2->is_hot,$ptGoods2->limit_time,$ptGoods2->is_only,$ptGoods2->is_more,$ptGoods2->colonel,$ptGoods2->buy_limit,$ptGoods2->type,$ptGoods2->use_attr,$ptGoods2->one_buy_limit,$ptGoods2->payment],
//                [$store_id,$ptGoods3->name,$ptGoods3->original_price,$ptGoods3->price,$ptGoods3->detail,($ptCatId + 1),$ptGoods3->status,$ptGoods3->grouptime,$ptGoods3->attr,$ptGoods3->service,$ptGoods3->sort,$ptGoods3->virtual_sales,$ptGoods3->cover_pic,$ptGoods3->weight,$ptGoods3->freight,$ptGoods3->unit,$ptGoods3->addtime,$ptGoods3->is_delete,$ptGoods3->group_num,$ptGoods3->is_hot,$ptGoods3->limit_time,$ptGoods3->is_only,$ptGoods3->is_more,$ptGoods3->colonel,$ptGoods3->buy_limit,$ptGoods3->type,$ptGoods3->use_attr,$ptGoods3->one_buy_limit,$ptGoods3->payment],
//                [$store_id,$ptGoods4->name,$ptGoods4->original_price,$ptGoods4->price,$ptGoods4->detail,($ptCatId + 1),$ptGoods4->status,$ptGoods4->grouptime,$ptGoods4->attr,$ptGoods4->service,$ptGoods4->sort,$ptGoods4->virtual_sales,$ptGoods4->cover_pic,$ptGoods4->weight,$ptGoods4->freight,$ptGoods4->unit,$ptGoods4->addtime,$ptGoods4->is_delete,$ptGoods4->group_num,$ptGoods4->is_hot,$ptGoods4->limit_time,$ptGoods4->is_only,$ptGoods4->is_more,$ptGoods4->colonel,$ptGoods4->buy_limit,$ptGoods4->type,$ptGoods4->use_attr,$ptGoods4->one_buy_limit,$ptGoods4->payment],
//            ])->execute();

    //        $ptGoodsPic设置拼团商品图片
//            Yii::$app->db->createCommand()->batchInsert(PtGoodsPic::tableName(), ['goods_id','pic_url','is_delete'], [
//                [$ptGoodsId,'http://img14.360buyimg.com/n12/jfs/t19684/10/8194561/275174/7064f997/5a574e65N03acae29.jpg',0],
//                [$ptGoodsId,'http://img14.360buyimg.com/n12/jfs/t13879/262/2205608977/221662/bea55727/5a34dc30Na57108ad.jpg',0],
//                [$ptGoodsId,'http://img14.360buyimg.com/n12/jfs/t14965/135/707679680/119827/9d453357/5a34dc30N3a7a0da6.jpg',0],
//                [$ptGoodsId,'http://img14.360buyimg.com/n12/jfs/t16138/58/444622417/224213/90f646b1/5a34dc31N6fda5655.jpg',0],
//                [$ptGoodsId,'http://img14.360buyimg.com/n12/jfs/t16321/19/515604878/243075/a1c46fbb/5a34dc18N32e7c5a3.jpg',0],
//                [($ptGoodsId + 1),'http://img11.360buyimg.com/n12/jfs/t18781/352/921525177/237175/d5bc9023/5ab28a3eN09951a2b.jpg',0],
//                [($ptGoodsId + 1),'http://img11.360buyimg.com/n12/jfs/t17338/181/949819477/276659/d5f6ae93/5ab28a3bN94af1120.jpg',0],
//                [($ptGoodsId + 1),'http://img11.360buyimg.com/n12/jfs/t15667/305/2444943693/205574/f6521acd/5aaf1167Nfac6a77d.jpg',0],
//                [($ptGoodsId + 1),'http://img11.360buyimg.com/n12/jfs/t15694/158/2570608721/271586/97c217b9/5aaf1169N2486d9b8.jpg',0],
//                [($ptGoodsId + 1),'http://img11.360buyimg.com/n12/jfs/t15718/327/2538864482/246502/d04a8f3c/5aaf116bN6353ddf6.jpg',0],
//                [($ptGoodsId + 1),'http://img11.360buyimg.com/n12/jfs/t14539/123/2617886869/117497/3b3b941f/5aaf116dN297fc88f.jpg',0],
//                [($ptGoodsId + 2),'http://img10.360buyimg.com/n12/jfs/t16891/109/963635717/204692/9710b828/5ab28a37Ne7b3a1dc.jpg',0],
//                [($ptGoodsId + 2),'http://img10.360buyimg.com/n12/jfs/t16429/156/2560373877/228007/d0cf8ea4/5ab28a32Nce490594.jpg',0],
//                [($ptGoodsId + 2),'http://img10.360buyimg.com/n12/jfs/t17980/332/436394345/239301/26266c53/5a7ab114N5d8d13b9.jpg',0],
//                [($ptGoodsId + 2),'http://img10.360buyimg.com/n12/jfs/t15442/348/2225777371/169049/86836782/5a7ab11bN0d69adf1.jpg',0],
//                [($ptGoodsId + 2),'http://img10.360buyimg.com/n12/jfs/t17497/75/448194751/180009/b3b56d18/5a7ab120Nc833789a.jpg',0],
//                [($ptGoodsId + 2),'http://img10.360buyimg.com/n12/jfs/t15886/104/2016650368/88015/919c8f28/5a7ab122N4e6d10e4.jpg',0],
//                [($ptGoodsId + 3),'https://img.alicdn.com/bao/uploaded/i3/924534709/TB2GrTaafJNTKJjSspoXXc6mpXa_!!924534709.jpg',0],
//                [($ptGoodsId + 3),'https://img.alicdn.com/bao/uploaded/i3/924534709/TB2Iufme8USMeJjy1zjXXc0dXXa_!!924534709.jpg',0],
//                [($ptGoodsId + 3),'https://img.alicdn.com/bao/uploaded/i4/924534709/TB2VJ43gKtTMeFjSZFOXXaTiVXa_!!924534709.jpg',0],
//                [($ptGoodsId + 3),'https://img.alicdn.com/bao/uploaded/i1/924534709/TB2LuA6ehaJ.eBjSsziXXaJ_XXa_!!924534709.jpg',0],
//                [($ptGoodsId + 3),'https://img.alicdn.com/bao/uploaded/i1/TB1MLcvOpXXXXXUXpXXXXXXXXXX_!!0-item_pic.jpg',0],
//            ])->execute();

    //        $ptSetting初始化拼团设置
//            $ptSetting->store_id = $store_id;
//            $ptSetting->is_share = 0;
//            $ptSetting->is_sms = 0;
//            $ptSetting->is_print = 0;
//            $ptSetting->is_mail = 0;
//            $ptSetting->is_area = null;
//            $ptSetting->save();

            //option添加公告,设置拼团广告,设置用户中心菜单
            Yii::$app->db->createCommand()->batchInsert(Option::tableName(), ['store_id','group','name','value'], [
                [$store_id, 'admin','notice','"公告图标、背景色、公告文字自定义"'],
//                [$store_id,'','pt_ad','a:4:{i:0;a:3:{s:7:"pic_url";s:82:"https://www.oggogg.com/web/uploads/image/45/45ded246586359c9629abc6e538355d1.jpg";s:3:"url";s:31:"/pages/pt/details/details?gid=' . ($ptGoodsId + 2) . '";s:9:"open_type";s:8:"navigate";}i:1;a:3:{s:7:"pic_url";s:82:"https://www.oggogg.com/web/uploads/image/45/45ded246586359c9629abc6e538355d1.jpg";s:3:"url";s:31:"/pages/pt/details/details?gid=' . ($ptGoodsId + 2) . '";s:9:"open_type";s:8:"navigate";}i:2;a:3:{s:7:"pic_url";s:82:"https://www.oggogg.com/web/uploads/image/a4/a4b865296e7170028986c591dbc59992.jpg";s:3:"url";s:31:"/pages/pt/details/details?gid=' . ($ptGoodsId + 2) . '";s:9:"open_type";s:8:"navigate";}i:3;a:3:{s:7:"pic_url";s:82:"https://www.oggogg.com/web/uploads/image/ef/efe1869f463f869fd52545fc1efd1b40.jpg";s:3:"url";s:31:"/pages/pt/details/details?gid=' . ($ptGoodsId + 2) . '";s:9:"open_type";s:8:"navigate";}}'],
                [$store_id, '','user_center_data','"{\"user_center_bg\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/img-user-bg.png\",\"orders\":{\"status_0\":{\"text\":\"待付款\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-order-0.png\"},\"status_1\":{\"text\":\"待发货\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-order-1.png\"},\"status_2\":{\"text\":\"待收货\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-order-2.png\"},\"status_3\":{\"text\":\"已完成\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-order-3.png\"},\"status_4\":{\"text\":\"售后\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-order-4.png\"}},\"wallets\":{\"status_0\":{\"text\":\"积分\",\"id\":\"integral\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/wxapp\/images\/icon-user-integral.png\"},\"status_1\":{\"text\":\"余额\",\"id\":\"balance\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/wxapp\/images\/icon-user-balance.png\"},\"status_2\":{\"text\":\"我的钱包\",\"id\":\"wallet\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/wxapp\/images\/icon-user-wallet.png\"}},\"menus\":[{\"sign\":\"pintuan\",\"id\":\"pintuan\",\"name\":\"我的拼团\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-user-pt.png\",\"open_type\":\"navigator\",\"url\":\"\/pages\/pt\/order\/order\",\"tel\":\"\"},{\"sign\":\"book\",\"id\":\"yuyue\",\"name\":\"我的预约\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-user-yy.png\",\"open_type\":\"navigator\",\"url\":\"\/pages\/book\/order\/order\",\"tel\":\"\"},{\"id\":\"kaquan\",\"name\":\"我的卡券\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-user-card.png\",\"open_type\":\"navigator\",\"url\":\"\/pages\/card\/card\",\"tel\":\"\"},{\"sign\":\"coupon\",\"id\":\"youhuiquan\",\"name\":\"我的优惠券\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-user-yhq.png\",\"open_type\":\"navigator\",\"url\":\"\/pages\/coupon\/coupon\",\"tel\":\"\"},{\"sign\":\"coupon\",\"id\":\"lingquan\",\"name\":\"领券中心\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-user-lingqu.png\",\"open_type\":\"navigator\",\"url\":\"\/pages\/coupon-list\/coupon-list\",\"tel\":\"\"},{\"id\":\"shoucang\",\"name\":\"我的收藏\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-user-sc.png\",\"open_type\":\"navigator\",\"url\":\"\/pages\/favorite\/favorite\",\"tel\":\"\"},{\"id\":\"kefu\",\"name\":\"在线客服\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-user-kf.png\",\"open_type\":\"contact\",\"url\":\"\",\"tel\":\"\"},{\"id\":\"dianhua\",\"name\":\"联系我们\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-user-lx.png\",\"open_type\":\"tel\",\"url\":\"\",\"tel\":\"\"},{\"id\":\"fuwu\",\"name\":\"服务中心\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-help.png\",\"open_type\":\"navigator\",\"url\":\"\/pages\/article-list\/article-list?id=2\",\"tel\":\"\"},{\"id\":\"address\",\"name\":\"收货地址\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-user-dz.png\",\"open_type\":\"navigator\",\"url\":\"\/pages\/address\/address\",\"tel\":\"\"},{\"sign\":\"integralmall\",\"id\":\"integral\",\"name\":\"我的兑换\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/icon-user-yhq.png\",\"open_type\":\"navigator\",\"url\":\"\/pages\/integral-mall\/order\/order\",\"tel\":\"\"},{\"id\":\"authorization\",\"name\":\"绑定公众号\",\"icon\":\"http:\/\/www.oggogg.com\/web\/statics\/images\/user-center\/authorization.png\",\"open_type\":\"navigator\",\"url\":\"\/pages\/web\/authorization\/authorization\",\"tel\":\"\"}],\"copyright\":{\"text\":\"\",\"icon\":\"\",\"url\":\"\",\"open_type\":\"\",\"is_phone\":0,\"phone\":\"\"},\"menu_style\":\"1\",\"top_style\":0,\"is_wallet\":1,\"is_order\":1,\"manual_mobile_auth\":0}"'],
            ])->execute();

            $transaction->commit();
        }catch (Exception $e) {
            $transaction->rollback();
            return false;
        }
        return true;
    }
  	  
}
