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
        if ($homeNav->findOne(['store_id' => $store_id]) || $store->findOne(['id' => $store_id])->home_page_module || $banner->findOne(['store_id' => $store_id]) || $homeBlock->findOne(['store_id' => $store_id]) || $notice->findOne(['store_id' => $store_id])->name == 'notice' || $coupon->findOne(['store_id' => $store_id]) || $cat->findOne(['store_id' => $store_id]) || $goods->findOne(['store_id' => $store_id]) || $goodsCat->findOne(['store_id' => $store_id]) || $yySetting->findOne(['store_id' => $store_id]) || $yyCat->findOne(['store_id' => $store_id])  || $yyGoods->findOne(['store_id' => $store_id]) || $shop->findOne(['store_id' => $store_id]) || $video->findOne(['store_id' => $store_id]) || $ptCat->findOne(['store_id' => $store_id]) || $ptGoods->findOne(['store_id' => $store_id]) || $ptSetting->findOne(['store_id' => $store_id])){
            return true;
        }
        $time = time();

        $transaction=Yii::$app->db->beginTransaction();
        try{



            //cat添加商品分类
            $cat->store_id = $store_id;
            $cat->parent_id = 0;
            $cat->name = '时尚女装';
            $cat->pic_url = 'https://www.oggogg.com/web/uploads/image/70/70bbf3a652936012dad02980a751ba57.jpg';
            $cat->sort = 1;
            $cat->addtime = $time;
            $cat->is_delete = 0;
            $cat->big_pic_url = 'https://www.oggogg.com/web/uploads/image/32/32928056280c2f958d6521bc130183f8.jpg';
            $cat->advert_pic = 'https://www.oggogg.com/web/uploads/image/f5/f5050d682931dbd2eb5b017d827fea48.jpg';
            $cat->advert_url = '/pages/cat/cat';
            $cat->is_show = 1;
            $cat->save();
    //        时尚女装分类ID
            $cat1_id = $cat->id;
            $cat2 = $cat->findOne(2);
            $cat3 = $cat->findOne(3);
            Yii::$app->db->createCommand()->batchInsert(Cat::tableName(), ['store_id','parent_id','name','pic_url','sort','addtime','is_delete','big_pic_url','advert_pic','advert_url','is_show'], [
                [$store_id,$cat1_id,$cat2->name,$cat2->pic_url,$cat2->sort,$cat2->addtime,$cat2->is_delete,$cat2->big_pic_url,$cat2->advert_pic,$cat2->advert_url,$cat2->is_show],
                [$store_id,$cat1_id,$cat3->name,$cat3->pic_url,$cat3->sort,$cat3->addtime,$cat3->is_delete,$cat3->big_pic_url,$cat3->advert_pic,$cat3->advert_url,$cat3->is_show],
            ])->execute();
            $cat4 = $cat->findOne(4);
            Yii::$app->db->createCommand()->batchInsert(Cat::tableName(), ['store_id','parent_id','name','pic_url','sort','addtime','is_delete','big_pic_url','advert_pic','advert_url','is_show'], [
                [$store_id,0,$cat4->name,$cat4->pic_url,$cat4->sort,$cat4->addtime,$cat4->is_delete,$cat4->big_pic_url,$cat4->advert_pic,$cat4->advert_url,$cat4->is_show]
            ])->execute();
    //        潮流男装分类id
            $cat4_id = $cat1_id + 3;
            $cat5 = $cat->findOne(5);
            $cat6 = $cat->findOne(6);
            Yii::$app->db->createCommand()->batchInsert(Cat::tableName(), ['store_id','parent_id','name','pic_url','sort','addtime','is_delete','big_pic_url','advert_pic','advert_url','is_show'], [
                [$store_id,$cat4_id,$cat5->name,$cat5->pic_url,$cat5->sort,$cat5->addtime,$cat5->is_delete,$cat5->big_pic_url,$cat5->advert_pic,$cat5->advert_url,$cat5->is_show],
                [$store_id,$cat4_id,$cat6->name,$cat6->pic_url,$cat6->sort,$cat6->addtime,$cat6->is_delete,$cat6->big_pic_url,$cat6->advert_pic,$cat6->advert_url,$cat6->is_show],
            ])->execute();
            $cat7 = $cat->findOne(7);
            Yii::$app->db->createCommand()->batchInsert(Cat::tableName(), ['store_id','parent_id','name','pic_url','sort','addtime','is_delete','big_pic_url','advert_pic','advert_url','is_show'], [
                [$store_id,0,$cat7->name,$cat7->pic_url,$cat7->sort,$cat7->addtime,$cat7->is_delete,$cat7->big_pic_url,$cat7->advert_pic,$cat7->advert_url,$cat7->is_show]
            ])->execute();
            $cat9 = $cat->findOne(9);
            Yii::$app->db->createCommand()->batchInsert(Cat::tableName(), ['store_id','parent_id','name','pic_url','sort','addtime','is_delete','big_pic_url','advert_pic','advert_url','is_show'], [
                [$store_id,0,$cat9->name,$cat9->pic_url,$cat9->sort,$cat9->addtime,$cat9->is_delete,$cat9->big_pic_url,$cat9->advert_pic,$cat9->advert_url,$cat9->is_show]
            ])->execute();
    //        美妆分类id
            $cat9_id = $cat1_id + 7;
            $cat10 = $cat->findOne(10);
            Yii::$app->db->createCommand()->batchInsert(Cat::tableName(), ['store_id','parent_id','name','pic_url','sort','addtime','is_delete','big_pic_url','advert_pic','advert_url','is_show'], [
                [$store_id,$cat9_id,$cat10->name,$cat10->pic_url,$cat10->sort,$cat10->addtime,$cat10->is_delete,$cat10->big_pic_url,$cat10->advert_pic,$cat10->advert_url,$cat10->is_show]
            ])->execute();


            //goods添加商品
            $goods1 = $goods->findOne(1);
            $goods2 = $goods->findOne(2);
            $goods3 = $goods->findOne(3);
            $goods4 = $goods->findOne(4);
            $goods5 = $goods->findOne(5);
            $goods6 = $goods->findOne(6);
            $goods7 = $goods->findOne(7);
            $goods8 = $goods->findOne(8);
            $goods9 = $goods->findOne(9);
            $goods10 = $goods->findOne(10);
            $goods25 = $goods->findOne(25);
            $goods26 = $goods->findOne(26);
            $goods28 = $goods->findOne(28);
            $goods29 = $goods->findOne(29);
            $goods31 = $goods->findOne(31);
            $goods32 = $goods->findOne(32);
            $goods34 = $goods->findOne(34);
            $goods38 = $goods->findOne(38);
            $goods39 = $goods->findOne(39);
            $goods40 = $goods->findOne(40);
            $goods41 = $goods->findOne(41);
            $goods48 = $goods->findOne(48);
            $goods49 = $goods->findOne(49);
            $goods64 = $goods->findOne(64);
            $goods67 = $goods->findOne(67);
            $goods68 = $goods->findOne(68);
            $goods72 = $goods->findOne(72);
            $goods73 = $goods->findOne(73);
            $goods74 = $goods->findOne(74);
            $goods75 = $goods->findOne(75);
            $goods76 = $goods->findOne(76);
            $goods77 = $goods->findOne(77);
            $goods->store_id          = $store_id;
            $goods->name              = $goods1->name;
            $goods->price             = $goods1->price;
            $goods->original_price    = $goods1->original_price;
            $goods->detail            = $goods1->detail;
            $goods->cat_id            = $goods1->cat_id;
            $goods->status            = $goods1->status;
            $goods->addtime           = $goods1->addtime;
            $goods->is_delete         = $goods1->is_delete;
            $goods->attr              = $goods1->attr;
            $goods->service           = $goods1->service;
            $goods->sort              = $goods1->sort;
            $goods->virtual_sales     = $goods1->virtual_sales;
            $goods->cover_pic         = $goods1->cover_pic;
            $goods->video_url         = $goods1->video_url;
            $goods->unit              = $goods1->unit;
            $goods->individual_share  = $goods1->individual_share;
            $goods->share_commission_first           = $goods1->share_commission_first;
            $goods->share_commission_second          = $goods1->share_commission_second;
            $goods->share_commission_third           = $goods1->share_commission_third;
            $goods->weight            = $goods1->weight;
            $goods->freight           = $goods1->freight;
            $goods->full_cut          = $goods1->full_cut;
            $goods->integral          = $goods1->integral;
            $goods->use_attr          = $goods1->use_attr;
            $goods->share_type        = $goods1->share_type;
            $goods->quick_purchase    = $goods1->quick_purchase;
            $goods->hot_cakes         = $goods1->hot_cakes;
            $goods->cost_price        = $goods1->cost_price;
            $goods->member_discount   = $goods1->member_discount;
            $goods->rebate            = $goods1->rebate;
            $goods->mch_id            = $goods1->mch_id;
            $goods->mch_sort          = $goods1->mch_sort;
            $goods->confine_count     = $goods1->confine_count;
            $goods->is_level          = $goods1->is_level;
            $goods->type              = $goods1->type;
            $goods->is_negotiable     = $goods1->is_negotiable;
            $goods->attr_setting_type = $goods1->attr_setting_type;
            $goods->save();
            $goods_first = $goods->id;

            Yii::$app->db->createCommand()->batchInsert(Goods::tableName(), ['store_id','name','price','original_price','detail','cat_id','status','addtime','is_delete','attr','service','sort','virtual_sales','cover_pic','video_url','unit','individual_share','share_commission_first','share_commission_second','share_commission_third','weight','freight','full_cut','integral','use_attr','share_type','quick_purchase','hot_cakes','cost_price','member_discount','rebate','mch_id','goods_num','mch_sort','confine_count','is_level','type','is_negotiable','attr_setting_type'], [
    //            [$store_id,$goods1->name,$goods1->price,$goods1->original_price,$goods1->detail,$goods1->cat_id,$goods1->status,$goods1->addtime,$goods1->is_delete,$goods1->attr,$goods1->service,$goods1->sort,$goods1->virtual_sales,$goods1->cover_pic,$goods1->video_url,$goods1->unit,$goods1->individual_share,$goods1->share_commission_first,$goods1->share_commission_second,$goods1->share_commission_third,$goods1->weight,$goods1->freight,$goods1->full_cut,$goods1->integral,$goods1->use_attr,$goods1->share_type,$goods1->quick_purchase,$goods1->hot_cakes,$goods1->cost_price,$goods1->member_discount,$goods1->rebate,$goods1->mch_id,$goods1->goods_num,$goods1->mch_sort,$goods1->confine_count,$goods1->is_level,$goods1->type,$goods1->is_negotiable,$goods1->attr_setting_type],
                [$store_id,$goods2->name,$goods2->price,$goods2->original_price,$goods2->detail,$goods2->cat_id,$goods2->status,$goods2->addtime,$goods2->is_delete,$goods2->attr,$goods2->service,$goods2->sort,$goods2->virtual_sales,$goods2->cover_pic,$goods2->video_url,$goods2->unit,$goods2->individual_share,$goods2->share_commission_first,$goods2->share_commission_second,$goods2->share_commission_third,$goods2->weight,$goods2->freight,$goods2->full_cut,$goods2->integral,$goods2->use_attr,$goods2->share_type,$goods2->quick_purchase,$goods2->hot_cakes,$goods2->cost_price,$goods2->member_discount,$goods2->rebate,$goods2->mch_id,$goods2->goods_num,$goods2->mch_sort,$goods2->confine_count,$goods2->is_level,$goods2->type,$goods2->is_negotiable,$goods2->attr_setting_type],
                [$store_id,$goods3->name,$goods3->price,$goods3->original_price,$goods3->detail,$goods3->cat_id,$goods3->status,$goods3->addtime,$goods3->is_delete,$goods3->attr,$goods3->service,$goods3->sort,$goods3->virtual_sales,$goods3->cover_pic,$goods3->video_url,$goods3->unit,$goods3->individual_share,$goods3->share_commission_first,$goods3->share_commission_second,$goods3->share_commission_third,$goods3->weight,$goods3->freight,$goods3->full_cut,$goods3->integral,$goods3->use_attr,$goods3->share_type,$goods3->quick_purchase,$goods3->hot_cakes,$goods3->cost_price,$goods3->member_discount,$goods3->rebate,$goods3->mch_id,$goods3->goods_num,$goods3->mch_sort,$goods3->confine_count,$goods3->is_level,$goods3->type,$goods3->is_negotiable,$goods3->attr_setting_type],
                [$store_id,$goods4->name,$goods4->price,$goods4->original_price,$goods4->detail,$goods4->cat_id,$goods4->status,$goods4->addtime,$goods4->is_delete,$goods4->attr,$goods4->service,$goods4->sort,$goods4->virtual_sales,$goods4->cover_pic,$goods4->video_url,$goods4->unit,$goods4->individual_share,$goods4->share_commission_first,$goods4->share_commission_second,$goods4->share_commission_third,$goods4->weight,$goods4->freight,$goods4->full_cut,$goods4->integral,$goods4->use_attr,$goods4->share_type,$goods4->quick_purchase,$goods4->hot_cakes,$goods4->cost_price,$goods4->member_discount,$goods4->rebate,$goods4->mch_id,$goods4->goods_num,$goods4->mch_sort,$goods4->confine_count,$goods4->is_level,$goods4->type,$goods4->is_negotiable,$goods4->attr_setting_type],
                [$store_id,$goods5->name,$goods5->price,$goods5->original_price,$goods5->detail,$goods5->cat_id,$goods5->status,$goods5->addtime,$goods5->is_delete,$goods5->attr,$goods5->service,$goods5->sort,$goods5->virtual_sales,$goods5->cover_pic,$goods5->video_url,$goods5->unit,$goods5->individual_share,$goods5->share_commission_first,$goods5->share_commission_second,$goods5->share_commission_third,$goods5->weight,$goods5->freight,$goods5->full_cut,$goods5->integral,$goods5->use_attr,$goods5->share_type,$goods5->quick_purchase,$goods5->hot_cakes,$goods5->cost_price,$goods5->member_discount,$goods5->rebate,$goods5->mch_id,$goods5->goods_num,$goods5->mch_sort,$goods5->confine_count,$goods5->is_level,$goods5->type,$goods5->is_negotiable,$goods5->attr_setting_type],
                [$store_id,$goods6->name,$goods6->price,$goods6->original_price,$goods6->detail,$goods6->cat_id,$goods6->status,$goods6->addtime,$goods6->is_delete,$goods6->attr,$goods6->service,$goods6->sort,$goods6->virtual_sales,$goods6->cover_pic,$goods6->video_url,$goods6->unit,$goods6->individual_share,$goods6->share_commission_first,$goods6->share_commission_second,$goods6->share_commission_third,$goods6->weight,$goods6->freight,$goods6->full_cut,$goods6->integral,$goods6->use_attr,$goods6->share_type,$goods6->quick_purchase,$goods6->hot_cakes,$goods6->cost_price,$goods6->member_discount,$goods6->rebate,$goods6->mch_id,$goods6->goods_num,$goods6->mch_sort,$goods6->confine_count,$goods6->is_level,$goods6->type,$goods6->is_negotiable,$goods6->attr_setting_type],
                [$store_id,$goods7->name,$goods7->price,$goods7->original_price,$goods7->detail,$goods7->cat_id,$goods7->status,$goods7->addtime,$goods7->is_delete,$goods7->attr,$goods7->service,$goods7->sort,$goods7->virtual_sales,$goods7->cover_pic,$goods7->video_url,$goods7->unit,$goods7->individual_share,$goods7->share_commission_first,$goods7->share_commission_second,$goods7->share_commission_third,$goods7->weight,$goods7->freight,$goods7->full_cut,$goods7->integral,$goods7->use_attr,$goods7->share_type,$goods7->quick_purchase,$goods7->hot_cakes,$goods7->cost_price,$goods7->member_discount,$goods7->rebate,$goods7->mch_id,$goods7->goods_num,$goods7->mch_sort,$goods7->confine_count,$goods7->is_level,$goods7->type,$goods7->is_negotiable,$goods7->attr_setting_type],
                [$store_id,$goods8->name,$goods8->price,$goods8->original_price,$goods8->detail,$goods8->cat_id,$goods8->status,$goods8->addtime,$goods8->is_delete,$goods8->attr,$goods8->service,$goods8->sort,$goods8->virtual_sales,$goods8->cover_pic,$goods8->video_url,$goods8->unit,$goods8->individual_share,$goods8->share_commission_first,$goods8->share_commission_second,$goods8->share_commission_third,$goods8->weight,$goods8->freight,$goods8->full_cut,$goods8->integral,$goods8->use_attr,$goods8->share_type,$goods8->quick_purchase,$goods8->hot_cakes,$goods8->cost_price,$goods8->member_discount,$goods8->rebate,$goods8->mch_id,$goods8->goods_num,$goods8->mch_sort,$goods8->confine_count,$goods8->is_level,$goods8->type,$goods8->is_negotiable,$goods8->attr_setting_type],
                [$store_id,$goods9->name,$goods9->price,$goods9->original_price,$goods9->detail,$goods9->cat_id,$goods9->status,$goods9->addtime,$goods9->is_delete,$goods9->attr,$goods9->service,$goods9->sort,$goods9->virtual_sales,$goods9->cover_pic,$goods9->video_url,$goods9->unit,$goods9->individual_share,$goods9->share_commission_first,$goods9->share_commission_second,$goods9->share_commission_third,$goods9->weight,$goods9->freight,$goods9->full_cut,$goods9->integral,$goods9->use_attr,$goods9->share_type,$goods9->quick_purchase,$goods9->hot_cakes,$goods9->cost_price,$goods9->member_discount,$goods9->rebate,$goods9->mch_id,$goods9->goods_num,$goods9->mch_sort,$goods9->confine_count,$goods9->is_level,$goods9->type,$goods9->is_negotiable,$goods9->attr_setting_type],
                [$store_id,$goods10->name,$goods10->price,$goods10->original_price,$goods10->detail,$goods10->cat_id,$goods10->status,$goods10->addtime,$goods10->is_delete,$goods10->attr,$goods10->service,$goods10->sort,$goods10->virtual_sales,$goods10->cover_pic,$goods10->video_url,$goods10->unit,$goods10->individual_share,$goods10->share_commission_first,$goods10->share_commission_second,$goods10->share_commission_third,$goods10->weight,$goods10->freight,$goods10->full_cut,$goods10->integral,$goods10->use_attr,$goods10->share_type,$goods10->quick_purchase,$goods10->hot_cakes,$goods10->cost_price,$goods10->member_discount,$goods10->rebate,$goods10->mch_id,$goods10->goods_num,$goods10->mch_sort,$goods10->confine_count,$goods10->is_level,$goods10->type,$goods10->is_negotiable,$goods10->attr_setting_type],
                [$store_id,$goods25->name,$goods25->price,$goods25->original_price,$goods25->detail,$goods25->cat_id,$goods25->status,$goods25->addtime,$goods25->is_delete,$goods25->attr,$goods25->service,$goods25->sort,$goods25->virtual_sales,$goods25->cover_pic,$goods25->video_url,$goods25->unit,$goods25->individual_share,$goods25->share_commission_first,$goods25->share_commission_second,$goods25->share_commission_third,$goods25->weight,$goods25->freight,$goods25->full_cut,$goods25->integral,$goods25->use_attr,$goods25->share_type,$goods25->quick_purchase,$goods25->hot_cakes,$goods25->cost_price,$goods25->member_discount,$goods25->rebate,$goods25->mch_id,$goods25->goods_num,$goods25->mch_sort,$goods25->confine_count,$goods25->is_level,$goods25->type,$goods25->is_negotiable,$goods25->attr_setting_type],
                [$store_id,$goods26->name,$goods26->price,$goods26->original_price,$goods26->detail,$goods26->cat_id,$goods26->status,$goods26->addtime,$goods26->is_delete,$goods26->attr,$goods26->service,$goods26->sort,$goods26->virtual_sales,$goods26->cover_pic,$goods26->video_url,$goods26->unit,$goods26->individual_share,$goods26->share_commission_first,$goods26->share_commission_second,$goods26->share_commission_third,$goods26->weight,$goods26->freight,$goods26->full_cut,$goods26->integral,$goods26->use_attr,$goods26->share_type,$goods26->quick_purchase,$goods26->hot_cakes,$goods26->cost_price,$goods26->member_discount,$goods26->rebate,$goods26->mch_id,$goods26->goods_num,$goods26->mch_sort,$goods26->confine_count,$goods26->is_level,$goods26->type,$goods26->is_negotiable,$goods26->attr_setting_type],
                [$store_id,$goods28->name,$goods28->price,$goods28->original_price,$goods28->detail,$goods28->cat_id,$goods28->status,$goods28->addtime,$goods28->is_delete,$goods28->attr,$goods28->service,$goods28->sort,$goods28->virtual_sales,$goods28->cover_pic,$goods28->video_url,$goods28->unit,$goods28->individual_share,$goods28->share_commission_first,$goods28->share_commission_second,$goods28->share_commission_third,$goods28->weight,$goods28->freight,$goods28->full_cut,$goods28->integral,$goods28->use_attr,$goods28->share_type,$goods28->quick_purchase,$goods28->hot_cakes,$goods28->cost_price,$goods28->member_discount,$goods28->rebate,$goods28->mch_id,$goods28->goods_num,$goods28->mch_sort,$goods28->confine_count,$goods28->is_level,$goods28->type,$goods28->is_negotiable,$goods28->attr_setting_type],
                [$store_id,$goods29->name,$goods29->price,$goods29->original_price,$goods29->detail,$goods29->cat_id,$goods29->status,$goods29->addtime,$goods29->is_delete,$goods29->attr,$goods29->service,$goods29->sort,$goods29->virtual_sales,$goods29->cover_pic,$goods29->video_url,$goods29->unit,$goods29->individual_share,$goods29->share_commission_first,$goods29->share_commission_second,$goods29->share_commission_third,$goods29->weight,$goods29->freight,$goods29->full_cut,$goods29->integral,$goods29->use_attr,$goods29->share_type,$goods29->quick_purchase,$goods29->hot_cakes,$goods29->cost_price,$goods29->member_discount,$goods29->rebate,$goods29->mch_id,$goods29->goods_num,$goods29->mch_sort,$goods29->confine_count,$goods29->is_level,$goods29->type,$goods29->is_negotiable,$goods29->attr_setting_type],
                [$store_id,$goods31->name,$goods31->price,$goods31->original_price,$goods31->detail,$goods31->cat_id,$goods31->status,$goods31->addtime,$goods31->is_delete,$goods31->attr,$goods31->service,$goods31->sort,$goods31->virtual_sales,$goods31->cover_pic,$goods31->video_url,$goods31->unit,$goods31->individual_share,$goods31->share_commission_first,$goods31->share_commission_second,$goods31->share_commission_third,$goods31->weight,$goods31->freight,$goods31->full_cut,$goods31->integral,$goods31->use_attr,$goods31->share_type,$goods31->quick_purchase,$goods31->hot_cakes,$goods31->cost_price,$goods31->member_discount,$goods31->rebate,$goods31->mch_id,$goods31->goods_num,$goods31->mch_sort,$goods31->confine_count,$goods31->is_level,$goods31->type,$goods31->is_negotiable,$goods31->attr_setting_type],
                [$store_id,$goods32->name,$goods32->price,$goods32->original_price,$goods32->detail,$goods32->cat_id,$goods32->status,$goods32->addtime,$goods32->is_delete,$goods32->attr,$goods32->service,$goods32->sort,$goods32->virtual_sales,$goods32->cover_pic,$goods32->video_url,$goods32->unit,$goods32->individual_share,$goods32->share_commission_first,$goods32->share_commission_second,$goods32->share_commission_third,$goods32->weight,$goods32->freight,$goods32->full_cut,$goods32->integral,$goods32->use_attr,$goods32->share_type,$goods32->quick_purchase,$goods32->hot_cakes,$goods32->cost_price,$goods32->member_discount,$goods32->rebate,$goods32->mch_id,$goods32->goods_num,$goods32->mch_sort,$goods32->confine_count,$goods32->is_level,$goods32->type,$goods32->is_negotiable,$goods32->attr_setting_type],
                [$store_id,$goods34->name,$goods34->price,$goods34->original_price,$goods34->detail,$goods34->cat_id,$goods34->status,$goods34->addtime,$goods34->is_delete,$goods34->attr,$goods34->service,$goods34->sort,$goods34->virtual_sales,$goods34->cover_pic,$goods34->video_url,$goods34->unit,$goods34->individual_share,$goods34->share_commission_first,$goods34->share_commission_second,$goods34->share_commission_third,$goods34->weight,$goods34->freight,$goods34->full_cut,$goods34->integral,$goods34->use_attr,$goods34->share_type,$goods34->quick_purchase,$goods34->hot_cakes,$goods34->cost_price,$goods34->member_discount,$goods34->rebate,$goods34->mch_id,$goods34->goods_num,$goods34->mch_sort,$goods34->confine_count,$goods34->is_level,$goods34->type,$goods34->is_negotiable,$goods34->attr_setting_type],
                [$store_id,$goods38->name,$goods38->price,$goods38->original_price,$goods38->detail,$goods38->cat_id,$goods38->status,$goods38->addtime,$goods38->is_delete,$goods38->attr,$goods38->service,$goods38->sort,$goods38->virtual_sales,$goods38->cover_pic,$goods38->video_url,$goods38->unit,$goods38->individual_share,$goods38->share_commission_first,$goods38->share_commission_second,$goods38->share_commission_third,$goods38->weight,$goods38->freight,$goods38->full_cut,$goods38->integral,$goods38->use_attr,$goods38->share_type,$goods38->quick_purchase,$goods38->hot_cakes,$goods38->cost_price,$goods38->member_discount,$goods38->rebate,$goods38->mch_id,$goods38->goods_num,$goods38->mch_sort,$goods38->confine_count,$goods38->is_level,$goods38->type,$goods38->is_negotiable,$goods38->attr_setting_type],
                [$store_id,$goods39->name,$goods39->price,$goods39->original_price,$goods39->detail,$goods39->cat_id,$goods39->status,$goods39->addtime,$goods39->is_delete,$goods39->attr,$goods39->service,$goods39->sort,$goods39->virtual_sales,$goods39->cover_pic,$goods39->video_url,$goods39->unit,$goods39->individual_share,$goods39->share_commission_first,$goods39->share_commission_second,$goods39->share_commission_third,$goods39->weight,$goods39->freight,$goods39->full_cut,$goods39->integral,$goods39->use_attr,$goods39->share_type,$goods39->quick_purchase,$goods39->hot_cakes,$goods39->cost_price,$goods39->member_discount,$goods39->rebate,$goods39->mch_id,$goods39->goods_num,$goods39->mch_sort,$goods39->confine_count,$goods39->is_level,$goods39->type,$goods39->is_negotiable,$goods39->attr_setting_type],
                [$store_id,$goods40->name,$goods40->price,$goods40->original_price,$goods40->detail,$goods40->cat_id,$goods40->status,$goods40->addtime,$goods40->is_delete,$goods40->attr,$goods40->service,$goods40->sort,$goods40->virtual_sales,$goods40->cover_pic,$goods40->video_url,$goods40->unit,$goods40->individual_share,$goods40->share_commission_first,$goods40->share_commission_second,$goods40->share_commission_third,$goods40->weight,$goods40->freight,$goods40->full_cut,$goods40->integral,$goods40->use_attr,$goods40->share_type,$goods40->quick_purchase,$goods40->hot_cakes,$goods40->cost_price,$goods40->member_discount,$goods40->rebate,$goods40->mch_id,$goods40->goods_num,$goods40->mch_sort,$goods40->confine_count,$goods40->is_level,$goods40->type,$goods40->is_negotiable,$goods40->attr_setting_type],
                [$store_id,$goods41->name,$goods41->price,$goods41->original_price,$goods41->detail,$goods41->cat_id,$goods41->status,$goods41->addtime,$goods41->is_delete,$goods41->attr,$goods41->service,$goods41->sort,$goods41->virtual_sales,$goods41->cover_pic,$goods41->video_url,$goods41->unit,$goods41->individual_share,$goods41->share_commission_first,$goods41->share_commission_second,$goods41->share_commission_third,$goods41->weight,$goods41->freight,$goods41->full_cut,$goods41->integral,$goods41->use_attr,$goods41->share_type,$goods41->quick_purchase,$goods41->hot_cakes,$goods41->cost_price,$goods41->member_discount,$goods41->rebate,$goods41->mch_id,$goods41->goods_num,$goods41->mch_sort,$goods41->confine_count,$goods41->is_level,$goods41->type,$goods41->is_negotiable,$goods41->attr_setting_type],
                [$store_id,$goods48->name,$goods48->price,$goods48->original_price,$goods48->detail,$goods48->cat_id,$goods48->status,$goods48->addtime,$goods48->is_delete,$goods48->attr,$goods48->service,$goods48->sort,$goods48->virtual_sales,$goods48->cover_pic,$goods48->video_url,$goods48->unit,$goods48->individual_share,$goods48->share_commission_first,$goods48->share_commission_second,$goods48->share_commission_third,$goods48->weight,$goods48->freight,$goods48->full_cut,$goods48->integral,$goods48->use_attr,$goods48->share_type,$goods48->quick_purchase,$goods48->hot_cakes,$goods48->cost_price,$goods48->member_discount,$goods48->rebate,$goods48->mch_id,$goods48->goods_num,$goods48->mch_sort,$goods48->confine_count,$goods48->is_level,$goods48->type,$goods48->is_negotiable,$goods48->attr_setting_type],
                [$store_id,$goods49->name,$goods49->price,$goods49->original_price,$goods49->detail,$goods49->cat_id,$goods49->status,$goods49->addtime,$goods49->is_delete,$goods49->attr,$goods49->service,$goods49->sort,$goods49->virtual_sales,$goods49->cover_pic,$goods49->video_url,$goods49->unit,$goods49->individual_share,$goods49->share_commission_first,$goods49->share_commission_second,$goods49->share_commission_third,$goods49->weight,$goods49->freight,$goods49->full_cut,$goods49->integral,$goods49->use_attr,$goods49->share_type,$goods49->quick_purchase,$goods49->hot_cakes,$goods49->cost_price,$goods49->member_discount,$goods49->rebate,$goods49->mch_id,$goods49->goods_num,$goods49->mch_sort,$goods49->confine_count,$goods49->is_level,$goods49->type,$goods49->is_negotiable,$goods49->attr_setting_type],
                [$store_id,$goods64->name,$goods64->price,$goods64->original_price,$goods64->detail,$goods64->cat_id,$goods64->status,$goods64->addtime,$goods64->is_delete,$goods64->attr,$goods64->service,$goods64->sort,$goods64->virtual_sales,$goods64->cover_pic,$goods64->video_url,$goods64->unit,$goods64->individual_share,$goods64->share_commission_first,$goods64->share_commission_second,$goods64->share_commission_third,$goods64->weight,$goods64->freight,$goods64->full_cut,$goods64->integral,$goods64->use_attr,$goods64->share_type,$goods64->quick_purchase,$goods64->hot_cakes,$goods64->cost_price,$goods64->member_discount,$goods64->rebate,$goods64->mch_id,$goods64->goods_num,$goods64->mch_sort,$goods64->confine_count,$goods64->is_level,$goods64->type,$goods64->is_negotiable,$goods64->attr_setting_type],
                [$store_id,$goods67->name,$goods67->price,$goods67->original_price,$goods67->detail,$goods67->cat_id,$goods67->status,$goods67->addtime,$goods67->is_delete,$goods67->attr,$goods67->service,$goods67->sort,$goods67->virtual_sales,$goods67->cover_pic,$goods67->video_url,$goods67->unit,$goods67->individual_share,$goods67->share_commission_first,$goods67->share_commission_second,$goods67->share_commission_third,$goods67->weight,$goods67->freight,$goods67->full_cut,$goods67->integral,$goods67->use_attr,$goods67->share_type,$goods67->quick_purchase,$goods67->hot_cakes,$goods67->cost_price,$goods67->member_discount,$goods67->rebate,$goods67->mch_id,$goods67->goods_num,$goods67->mch_sort,$goods67->confine_count,$goods67->is_level,$goods67->type,$goods67->is_negotiable,$goods67->attr_setting_type],
                [$store_id,$goods68->name,$goods68->price,$goods68->original_price,$goods68->detail,$goods68->cat_id,$goods68->status,$goods68->addtime,$goods68->is_delete,$goods68->attr,$goods68->service,$goods68->sort,$goods68->virtual_sales,$goods68->cover_pic,$goods68->video_url,$goods68->unit,$goods68->individual_share,$goods68->share_commission_first,$goods68->share_commission_second,$goods68->share_commission_third,$goods68->weight,$goods68->freight,$goods68->full_cut,$goods68->integral,$goods68->use_attr,$goods68->share_type,$goods68->quick_purchase,$goods68->hot_cakes,$goods68->cost_price,$goods68->member_discount,$goods68->rebate,$goods68->mch_id,$goods68->goods_num,$goods68->mch_sort,$goods68->confine_count,$goods68->is_level,$goods68->type,$goods68->is_negotiable,$goods68->attr_setting_type],
                [$store_id,$goods72->name,$goods72->price,$goods72->original_price,$goods72->detail,$goods72->cat_id,$goods72->status,$goods72->addtime,$goods72->is_delete,$goods72->attr,$goods72->service,$goods72->sort,$goods72->virtual_sales,$goods72->cover_pic,$goods72->video_url,$goods72->unit,$goods72->individual_share,$goods72->share_commission_first,$goods72->share_commission_second,$goods72->share_commission_third,$goods72->weight,$goods72->freight,$goods72->full_cut,$goods72->integral,$goods72->use_attr,$goods72->share_type,$goods72->quick_purchase,$goods72->hot_cakes,$goods72->cost_price,$goods72->member_discount,$goods72->rebate,$goods72->mch_id,$goods72->goods_num,$goods72->mch_sort,$goods72->confine_count,$goods72->is_level,$goods72->type,$goods72->is_negotiable,$goods72->attr_setting_type],
                [$store_id,$goods73->name,$goods73->price,$goods73->original_price,$goods73->detail,$goods73->cat_id,$goods73->status,$goods73->addtime,$goods73->is_delete,$goods73->attr,$goods73->service,$goods73->sort,$goods73->virtual_sales,$goods73->cover_pic,$goods73->video_url,$goods73->unit,$goods73->individual_share,$goods73->share_commission_first,$goods73->share_commission_second,$goods73->share_commission_third,$goods73->weight,$goods73->freight,$goods73->full_cut,$goods73->integral,$goods73->use_attr,$goods73->share_type,$goods73->quick_purchase,$goods73->hot_cakes,$goods73->cost_price,$goods73->member_discount,$goods73->rebate,$goods73->mch_id,$goods73->goods_num,$goods73->mch_sort,$goods73->confine_count,$goods73->is_level,$goods73->type,$goods73->is_negotiable,$goods73->attr_setting_type],
                [$store_id,$goods74->name,$goods74->price,$goods74->original_price,$goods74->detail,$goods74->cat_id,$goods74->status,$goods74->addtime,$goods74->is_delete,$goods74->attr,$goods74->service,$goods74->sort,$goods74->virtual_sales,$goods74->cover_pic,$goods74->video_url,$goods74->unit,$goods74->individual_share,$goods74->share_commission_first,$goods74->share_commission_second,$goods74->share_commission_third,$goods74->weight,$goods74->freight,$goods74->full_cut,$goods74->integral,$goods74->use_attr,$goods74->share_type,$goods74->quick_purchase,$goods74->hot_cakes,$goods74->cost_price,$goods74->member_discount,$goods74->rebate,$goods74->mch_id,$goods74->goods_num,$goods74->mch_sort,$goods74->confine_count,$goods74->is_level,$goods74->type,$goods74->is_negotiable,$goods74->attr_setting_type],
                [$store_id,$goods75->name,$goods75->price,$goods75->original_price,$goods75->detail,$goods75->cat_id,$goods75->status,$goods75->addtime,$goods75->is_delete,$goods75->attr,$goods75->service,$goods75->sort,$goods75->virtual_sales,$goods75->cover_pic,$goods75->video_url,$goods75->unit,$goods75->individual_share,$goods75->share_commission_first,$goods75->share_commission_second,$goods75->share_commission_third,$goods75->weight,$goods75->freight,$goods75->full_cut,$goods75->integral,$goods75->use_attr,$goods75->share_type,$goods75->quick_purchase,$goods75->hot_cakes,$goods75->cost_price,$goods75->member_discount,$goods75->rebate,$goods75->mch_id,$goods75->goods_num,$goods75->mch_sort,$goods75->confine_count,$goods75->is_level,$goods75->type,$goods75->is_negotiable,$goods75->attr_setting_type],
                [$store_id,$goods76->name,$goods76->price,$goods76->original_price,$goods76->detail,$goods76->cat_id,$goods76->status,$goods76->addtime,$goods76->is_delete,$goods76->attr,$goods76->service,$goods76->sort,$goods76->virtual_sales,$goods76->cover_pic,$goods76->video_url,$goods76->unit,$goods76->individual_share,$goods76->share_commission_first,$goods76->share_commission_second,$goods76->share_commission_third,$goods76->weight,$goods76->freight,$goods76->full_cut,$goods76->integral,$goods76->use_attr,$goods76->share_type,$goods76->quick_purchase,$goods76->hot_cakes,$goods76->cost_price,$goods76->member_discount,$goods76->rebate,$goods76->mch_id,$goods76->goods_num,$goods76->mch_sort,$goods76->confine_count,$goods76->is_level,$goods76->type,$goods76->is_negotiable,$goods76->attr_setting_type],
                [$store_id,$goods77->name,$goods77->price,$goods77->original_price,$goods77->detail,$goods77->cat_id,$goods77->status,$goods77->addtime,$goods77->is_delete,$goods77->attr,$goods77->service,$goods77->sort,$goods77->virtual_sales,$goods77->cover_pic,$goods77->video_url,$goods77->unit,$goods77->individual_share,$goods77->share_commission_first,$goods77->share_commission_second,$goods77->share_commission_third,$goods77->weight,$goods77->freight,$goods77->full_cut,$goods77->integral,$goods77->use_attr,$goods77->share_type,$goods77->quick_purchase,$goods77->hot_cakes,$goods77->cost_price,$goods77->member_discount,$goods77->rebate,$goods77->mch_id,$goods77->goods_num,$goods77->mch_sort,$goods77->confine_count,$goods77->is_level,$goods77->type,$goods77->is_negotiable,$goods77->attr_setting_type],
            ])->execute();

    //        $goodsCat添加图片
            Yii::$app->db->createCommand()->batchInsert(GoodsPic::tableName(), ['goods_id','pic_url','is_delete'], [
                [$goods_first,'http://img10.360buyimg.com/n12/jfs/t5641/230/9775132476/263782/3a93681c/598ab7f0Nfb7ea32f.jpg',0],//1
                [$goods_first,'http://img10.360buyimg.com/n12/jfs/t7072/362/1395831648/259542/9d0d549d/598ab7ffNca68be4b.jpg',0],//1
                [$goods_first,'http://img10.360buyimg.com/n12/jfs/t7156/363/1333114400/292118/3a154573/598ab7ffN6db6fa4f.jpg',0],//1
                [$goods_first,'http://img10.360buyimg.com/n12/jfs/t5944/144/8674633507/315097/93ecd22a/598ab7f7N6303e4c7.jpg',0],//1
                [$goods_first,'http://img10.360buyimg.com/n12/jfs/t5857/172/9858358070/287561/1754e3b9/598ab800N09667bf8.jpg',0],//1

                [($goods_first + 1),'http://img14.360buyimg.com/n12/jfs/t19684/10/8194561/275174/7064f997/5a574e65N03acae29.jpg',0],//2
                [($goods_first + 1),'http://img14.360buyimg.com/n12/jfs/t13879/262/2205608977/221662/bea55727/5a34dc30Na57108ad.jpg',0],//2
                [($goods_first + 1),'http://img14.360buyimg.com/n12/jfs/t14965/135/707679680/119827/9d453357/5a34dc30N3a7a0da6.jpg',0],//2
                [($goods_first + 1),'http://img14.360buyimg.com/n12/jfs/t16138/58/444622417/224213/90f646b1/5a34dc31N6fda5655.jpg',0],//2
                [($goods_first + 1),'http://img14.360buyimg.com/n12/jfs/t16321/19/515604878/243075/a1c46fbb/5a34dc18N32e7c5a3.jpg',0],//2

                [($goods_first + 2),'http://img13.360buyimg.com/n12/jfs/t7402/328/4226956420/264491/91ace2f2/5a51edddNa6131ef8.jpg',0],//3
                [($goods_first + 2),'http://img13.360buyimg.com/n12/jfs/t16108/11/1222424624/197873/465da8b6/5a51edddNefa967f8.jpg',0],//3
                [($goods_first + 2),'http://img13.360buyimg.com/n12/jfs/t14920/52/1540709715/108313/ade5fac0/5a51eddeN1e4fc28c.jpg',0],//3
                [($goods_first + 2),'http://img13.360buyimg.com/n12/jfs/t15610/180/1451745614/116382/a79fe368/5a51eddeNc9850da6.jpg',0],//3
                [($goods_first + 2),'http://img13.360buyimg.com/n12/jfs/t13060/365/2207610305/167809/fe8d7c02/5a51edcbNbd5c40c7.jpg',0],//3

                [($goods_first + 3),'http://img10.360buyimg.com/n12/jfs/t16360/321/2536990929/232120/59957359/5ab28a35Nc5b8f104.jpg',0],//4
                [($goods_first + 3),'http://img10.360buyimg.com/n12/jfs/t18259/103/937660110/279314/4b208cde/5ab28a2fN751dc7b7.jpg',0],//4
                [($goods_first + 3),'http://img10.360buyimg.com/n12/jfs/t17740/348/981219276/252253/e301e386/5ab23208Nf37ade77.jpg',0],//4
                [($goods_first + 3),'http://img10.360buyimg.com/n12/jfs/t19279/62/905684594/228765/4688cd4a/5ab23209N8eb9041f.jpg',0],//4
                [($goods_first + 3),'http://img10.360buyimg.com/n12/jfs/t19708/202/917744954/316183/7151a905/5ab23209N6fb7e503.jpg',0],//4
                [($goods_first + 3),'http://img10.360buyimg.com/n12/jfs/t17746/357/959568690/65169/2936596c/5ab2320bNeed77ccf.jpg',0],//4

                [($goods_first + 4),'http://img11.360buyimg.com/n12/jfs/t18781/352/921525177/237175/d5bc9023/5ab28a3eN09951a2b.jpg',0],//5
                [($goods_first + 4),'http://img11.360buyimg.com/n12/jfs/t17338/181/949819477/276659/d5f6ae93/5ab28a3bN94af1120.jpg',0],//5
                [($goods_first + 4),'http://img11.360buyimg.com/n12/jfs/t15667/305/2444943693/205574/f6521acd/5aaf1167Nfac6a77d.jpg',0],//5
                [($goods_first + 4),'http://img11.360buyimg.com/n12/jfs/t15694/158/2570608721/271586/97c217b9/5aaf1169N2486d9b8.jpg',0],//5
                [($goods_first + 4),'http://img11.360buyimg.com/n12/jfs/t15718/327/2538864482/246502/d04a8f3c/5aaf116bN6353ddf6.jpg',0],//5
                [($goods_first + 4),'http://img11.360buyimg.com/n12/jfs/t14539/123/2617886869/117497/3b3b941f/5aaf116dN297fc88f.jpg',0],//5

                [($goods_first + 5),'http://img10.360buyimg.com/n12/jfs/t16891/109/963635717/204692/9710b828/5ab28a37Ne7b3a1dc.jpg',0],//6
                [($goods_first + 5),'http://img10.360buyimg.com/n12/jfs/t16429/156/2560373877/228007/d0cf8ea4/5ab28a32Nce490594.jpg',0],//6
                [($goods_first + 5),'http://img10.360buyimg.com/n12/jfs/t17980/332/436394345/239301/26266c53/5a7ab114N5d8d13b9.jpg',0],//6
                [($goods_first + 5),'http://img10.360buyimg.com/n12/jfs/t15442/348/2225777371/169049/86836782/5a7ab11bN0d69adf1.jpg',0],//6
                [($goods_first + 5),'http://img10.360buyimg.com/n12/jfs/t17497/75/448194751/180009/b3b56d18/5a7ab120Nc833789a.jpg',0],//6
                [($goods_first + 5),'http://img10.360buyimg.com/n12/jfs/t15886/104/2016650368/88015/919c8f28/5a7ab122N4e6d10e4.jpg',0],//6

                [($goods_first + 6),'http://img14.360buyimg.com/n12/jfs/t15937/233/2471576999/226464/470ede29/5ab28a30N167fd04c.jpg',0],//7
                [($goods_first + 6),'http://img14.360buyimg.com/n12/jfs/t19264/291/963884250/274477/7c17c8cb/5ab28a2aN5e851d6d.jpg',0],//7
                [($goods_first + 6),'http://img14.360buyimg.com/n12/jfs/t14983/231/2236141681/251553/5de77c29/5a7ab1baNb19a942d.jpg',0],//7
                [($goods_first + 6),'http://img14.360buyimg.com/n12/jfs/t16519/17/2069643310/303759/ec7e5cda/5a7ab1bcNfe2bc398.jpg',0],//7
                [($goods_first + 6),'http://img14.360buyimg.com/n12/jfs/t18502/51/440389196/290389/4c8e41da/5a7ab1beNd07cf511.jpg',0],//7
                [($goods_first + 6),'http://img14.360buyimg.com/n12/jfs/t15082/361/2172783222/131799/283d0c21/5a7ab1bfNab6eba3d.jpg',0],//7

                [($goods_first + 7),'http://img10.360buyimg.com/n12/jfs/t17257/81/942908504/178545/a9950677/5ab32bb0N15ba5250.jpg',0],//8
                [($goods_first + 7),'http://img10.360buyimg.com/n12/jfs/t18928/248/922833849/195765/30ee57e/5ab32baeN45abe4b3.jpg',0],//8
                [($goods_first + 7),'http://img10.360buyimg.com/n12/jfs/t3133/236/1264270665/125326/14894119/57c90eb1N1daeaf0e.jpg',0],//8
                [($goods_first + 7),'http://img10.360buyimg.com/n12/jfs/t3253/53/1242748350/80982/79c6ce03/57c90eb2N131b84f3.jpg',0],//8
                [($goods_first + 7),'http://img10.360buyimg.com/n12/jfs/t3070/14/1270497854/68141/c40be892/57c90eb3N8949ff5a.jpg',0],//8
                [($goods_first + 7),'http://img10.360buyimg.com/n12/jfs/t3064/66/1266992193/81678/ca73ef82/57c90eb3Na00d03d5.jpg',0],//8
                [($goods_first + 7),'http://img10.360buyimg.com/n12/jfs/t3079/244/1294500333/47556/eb9f812f/57c90eb4N975f250c.jpg',0],//8

                [($goods_first + 8),'http://img11.360buyimg.com/n12/jfs/t16069/179/2557429172/250992/b24acf5f/5ab28a2bN07bf0dc1.jpg',0],//9
                [($goods_first + 8),'http://img11.360buyimg.com/n12/jfs/t16786/317/959834503/255424/bbb8f6af/5ab28a26Nac5841e5.jpg',0],//9
                [($goods_first + 8),'http://img11.360buyimg.com/n12/jfs/t19123/241/705866808/278494/1ad49de4/5aa0d8afNf9fe71a1.jpg',0],//9
                [($goods_first + 8),'http://img11.360buyimg.com/n12/jfs/t16342/194/2301585889/256795/1689a2da/5aa0d8b1N2d8e48bd.jpg',0],//9
                [($goods_first + 8),'http://img11.360buyimg.com/n12/jfs/t19225/135/680839225/250292/9450fd8b/5aa0d8b2N14bbed1a.jpg',0],//9
                [($goods_first + 8),'http://img11.360buyimg.com/n12/jfs/t17272/315/704200234/70708/5251fb50/5aa0d8b4N0661e260.jpg',0],//9

                [($goods_first + 9),'https://img.alicdn.com/bao/uploaded/i2/249302959/TB20Bj9fwfH8KJjy1zcXXcTzpXa_!!249302959.jpg',0],//10
                [($goods_first + 9),'https://img.alicdn.com/bao/uploaded/i2/249302959/TB2GgHgcbFkpuFjy1XcXXclapXa_!!249302959.jpg',0],//10
                [($goods_first + 9),'https://img.alicdn.com/bao/uploaded/i2/249302959/TB29mQrcUdnpuFjSZPhXXbChpXa_!!249302959.jpg',0],//10
                [($goods_first + 9),'https://img.alicdn.com/bao/uploaded/i4/249302959/TB2H6ohcHJmpuFjSZFwXXaE4VXa_!!249302959.jpg',0],//10

                [($goods_first + 10),'https://img.alicdn.com/bao/uploaded/i3/917264765/TB2EotFdZyYBuNkSnfoXXcWgVXa_!!917264765.jpg',0],//25
                [($goods_first + 10),'https://img.alicdn.com/bao/uploaded/i2/917264765/TB2Pg8Rm_lYBeNjSszcXXbwhFXa_!!917264765.jpg',0],//25
                [($goods_first + 10),'https://img.alicdn.com/bao/uploaded/i1/917264765/TB2yVwXmVuWBuNjSspnXXX1NVXa_!!917264765.jpg',0],//25
                [($goods_first + 10),'https://img.alicdn.com/bao/uploaded/i4/917264765/TB201T5kbSYBuNjSspfXXcZCpXa_!!917264765.jpg',0],//25

                [($goods_first + 11),'https://img.alicdn.com/bao/uploaded/i3/94008339/TB2aoedmTJYBeNjy1zeXXahzVXa_!!94008339.jpg',0],//26
                [($goods_first + 11),'https://img.alicdn.com/bao/uploaded/i4/94008339/TB2Mn0cmNGYBuNjy0FnXXX5lpXa_!!94008339.jpg',0],//26
                [($goods_first + 11),'https://img.alicdn.com/bao/uploaded/i3/94008339/TB2e7m5ecuYBuNkSmRyXXcA3pXa_!!94008339.jpg',0],//26
                [($goods_first + 11),'https://img.alicdn.com/bao/uploaded/i2/94008339/TB2RyyRdBnTBKNjSZPfXXbf1XXa_!!94008339.jpg',0],//26

                [($goods_first + 12),'https://img.alicdn.com/bao/uploaded/i4/556608090/TB2nBMlbbArBKNjSZFLXXc_dVXa_!!556608090.jpg',0],//28
                [($goods_first + 12),'https://img.alicdn.com/bao/uploaded/i1/556608090/TB29q_IfhWYBuNjy1zkXXXGGpXa_!!556608090.jpg',0],//28
                [($goods_first + 12),'https://img.alicdn.com/bao/uploaded/i2/556608090/TB2PxgsbamWBuNkHFJHXXaatVXa_!!556608090.jpg',0],//28
                [($goods_first + 12),'https://img.alicdn.com/bao/uploaded/i1/556608090/TB28PoobiMnBKNjSZFzXXc_qVXa_!!556608090.jpg',0],//28

                [($goods_first + 13),'https://img.alicdn.com/bao/uploaded/i2/1601145275/TB2JnE_dndYBeNkSmLyXXXfnVXa-1601145275.jpg',0],//29
                [($goods_first + 13),'https://img.alicdn.com/bao/uploaded/i3/1601145275/TB2bOBUdljTBKNjSZFDXXbVgVXa-1601145275.jpg',0],//29
                [($goods_first + 13),'https://img.alicdn.com/bao/uploaded/i3/1601145275/TB2xBmaa1ySBuNjy1zdXXXPxFXa-1601145275.jpg',0],//29
                [($goods_first + 13),'https://img.alicdn.com/bao/uploaded/i2/1601145275/TB2P3OgaTJYBeNjy1zeXXahzVXa-1601145275.jpg',0],//29

                [($goods_first + 14),'https://www.oggogg.com/web/uploads/image/58/5889a5ab32877e9f4135751e2b0ab11e.jpg',0],//31

                [($goods_first + 15),'https://www.oggogg.com/web/uploads/image/49/495d383c6b909a6614e4d070c58de69d.jpg',0],//32

                [($goods_first + 16),'https://img.alicdn.com/bao/uploaded/i4/422029023/TB2ebcdr1uSBuNjSsplXXbe8pXa_!!422029023.jpg',0],//34
                [($goods_first + 16),'https://img.alicdn.com/bao/uploaded/i1/422029023/TB2cSPajIuYBuNkSmRyXXcA3pXa_!!422029023.jpg',0],//34
                [($goods_first + 16),'https://img.alicdn.com/bao/uploaded/i4/422029023/TB2Ce.4r4GYBuNjy0FnXXX5lpXa_!!422029023.jpg',0],//34
                [($goods_first + 16),'https://img.alicdn.com/bao/uploaded/i3/422029023/TB2GVLBsStYBeNjSspaXXaOOFXa_!!422029023.jpg',0],//34

                [($goods_first + 17),'https://img.alicdn.com/bao/uploaded/i1/735615632/TB2knoBaDIlyKJjSZFrXXXn2VXa_!!735615632.jpg',0],//38
                [($goods_first + 17),'https://img.alicdn.com/bao/uploaded/i4/735615632/TB2uaoxaxwlyKJjSZFsXXar3XXa_!!735615632.jpg',0],//38
                [($goods_first + 17),'https://img.alicdn.com/bao/uploaded/i2/735615632/TB2e84ca1rAQeBjSZFNXXcgJVXa_!!735615632.jpg',0],//38
                [($goods_first + 17),'https://img.alicdn.com/bao/uploaded/i2/735615632/TB2N2Rca8LzQeBjSZFCXXXmtXXa_!!735615632.jpg',0],//38
                [($goods_first + 17),'https://img.alicdn.com/bao/uploaded/i4/735615632/TB2j_VvcMoQMeJjy0FnXXb8gFXa_!!735615632.jpg',0],//38

                [($goods_first + 18),'https://img.alicdn.com/bao/uploaded/i3/924534709/TB2GrTaafJNTKJjSspoXXc6mpXa_!!924534709.jpg',0],//39
                [($goods_first + 18),'https://img.alicdn.com/bao/uploaded/i3/924534709/TB2Iufme8USMeJjy1zjXXc0dXXa_!!924534709.jpg',0],//39
                [($goods_first + 18),'https://img.alicdn.com/bao/uploaded/i4/924534709/TB2VJ43gKtTMeFjSZFOXXaTiVXa_!!924534709.jpg',0],//39
                [($goods_first + 18),'https://img.alicdn.com/bao/uploaded/i1/924534709/TB2LuA6ehaJ.eBjSsziXXaJ_XXa_!!924534709.jpg',0],//39
                [($goods_first + 18),'https://img.alicdn.com/bao/uploaded/i1/TB1MLcvOpXXXXXUXpXXXXXXXXXX_!!0-item_pic.jpg',0],//39

                [($goods_first + 19),'https://www.oggogg.com/web/uploads/image/0c/0c374ace75bdd76ffdcdd91135cf38d3.jpg',0],//40

                [($goods_first + 20),'https://www.oggogg.com/web/uploads/image/60/6014944505149d6a5e0c2bb2dd3f99de7d0aac5d.jpeg',0],//41

                [($goods_first + 21),'https://www.oggogg.com/web/uploads/image/b0/b0ca029efb6d2740fc7b8780279b794bf0b68caf.jpg',0],//48
                [($goods_first + 21),'https://www.oggogg.com/web/uploads/image/fd/fd697dc2b04528ec0361a77ef6bfba9bb4f00006.jpg',0],//48

                [($goods_first + 22),'https://www.oggogg.com/web/uploads/image/44/44e8f6aec821d8543d92eac4ec611bdc070a77d1.jpg',0],//49

                [($goods_first + 23),'https://img.alicdn.com/bao/uploaded/i1/383803646/TB2u3F.r3mTBuNjy1XbXXaMrVXa_!!383803646.jpg',0],//64
                [($goods_first + 23),'https://img.alicdn.com/bao/uploaded/i4/383803646/TB2e3nOrWmWBuNjy1XaXXXCbXXa_!!383803646.jpg',0],//64
                [($goods_first + 23),'https://img.alicdn.com/bao/uploaded/i4/383803646/TB2u3vFr21TBuNjy0FjXXajyXXa_!!383803646.jpg',0],//64
                [($goods_first + 23),'https://img.alicdn.com/bao/uploaded/i1/383803646/TB2a2GNrVOWBuNjy0FiXXXFxVXa_!!383803646.jpg',0],//64
                [($goods_first + 23),'https://img.alicdn.com/bao/uploaded/i2/383803646/TB1WM97f5AnBKNjSZFvXXaTKXXa_!!0-item_pic.jpg',0],//64

                [($goods_first + 24),'http://img10.360buyimg.com/n12/jfs/t5641/230/9775132476/263782/3a93681c/598ab7f0Nfb7ea32f.jpg',0],//67
                [($goods_first + 24),'http://img10.360buyimg.com/n12/jfs/t7072/362/1395831648/259542/9d0d549d/598ab7ffNca68be4b.jpg',0],//67
                [($goods_first + 24),'http://img10.360buyimg.com/n12/jfs/t7156/363/1333114400/292118/3a154573/598ab7ffN6db6fa4f.jpg',0],//67
                [($goods_first + 24),'http://img10.360buyimg.com/n12/jfs/t5944/144/8674633507/315097/93ecd22a/598ab7f7N6303e4c7.jpg',0],//67
                [($goods_first + 24),'http://img10.360buyimg.com/n12/jfs/t5857/172/9858358070/287561/1754e3b9/598ab800N09667bf8.jpg',0],//67

                [($goods_first + 25),'https://www.oggogg.com/web/uploads/image/d5/d5cf9ceceb2bfc2261fc88e2cb54c6d1ba02b077.jpg',0],//68

                [($goods_first + 26),'https://img.alicdn.com/bao/uploaded/i2/3455531743/O1CN011OkKnaH9tUGXaWA_!!3455531743.jpg',0],//72
                [($goods_first + 26),'https://img.alicdn.com/bao/uploaded/i4/3455531743/TB1LhELdxTI8KJjSspiXXbM4FXa_!!0-item_pic.jpg',0],//72
                [($goods_first + 26),'https://img.alicdn.com/bao/uploaded/i3/3455531743/TB2643vdBTH8KJjy0FiXXcRsXXa_!!3455531743.jpg',0],//72
                [($goods_first + 26),'https://img.alicdn.com/bao/uploaded/i3/3455531743/TB2MjQDdxPI8KJjSspfXXcCFXXa_!!3455531743.jpg',0],//72
                [($goods_first + 26),'https://img.alicdn.com/bao/uploaded/i3/3455531743/TB2g35LeRLN8KJjSZPhXXc.spXa_!!3455531743.jpg',0],//72

                [($goods_first + 27),'https://img.alicdn.com/bao/uploaded/i1/735615632/TB2OqEAnHFlpuFjy0FgXXbRBVXa_!!735615632.jpg',0],//73
                [($goods_first + 27),'https://img.alicdn.com/bao/uploaded/i2/15632026825190223/T1lharFmFfXXXXXXXX_!!0-item_pic.jpg',0],//73
                [($goods_first + 27),'https://img.alicdn.com/bao/uploaded/i1/735615632/TB2CI3kfXXXXXb0XXXXXXXXXXXX_!!735615632.jpg',0],//73
                [($goods_first + 27),'https://img.alicdn.com/bao/uploaded/i3/735615632/TB2jPgwfXXXXXXbXXXXXXXXXXXX_!!735615632.jpg',0],//73
                [($goods_first + 27),'https://img.alicdn.com/bao/uploaded/i1/735615632/TB2vv1RadqUQKJjSZFIXXcOkFXa_!!735615632.jpg',0],//73

                [($goods_first + 28),'https://img.alicdn.com/bao/uploaded/i1/3676232520/O1CN011UUCXFL04yZMM1X_!!3676232520.jpg',0],//74
                [($goods_first + 28),'https://img.alicdn.com/bao/uploaded/i3/3676232520/O1CN011UUCXGzg8VYHG7f_!!3676232520.jpg',0],//74
                [($goods_first + 28),'https://img.alicdn.com/bao/uploaded/i4/3676232520/O1CN011UUCXGzgKwcKEYt_!!3676232520.jpg',0],//74
                [($goods_first + 28),'https://img.alicdn.com/bao/uploaded/i3/3676232520/O1CN011UUCXFlwWh00JlK_!!3676232520.jpg',0],//74
                [($goods_first + 28),'https://img.alicdn.com/bao/uploaded/i3/3676232520/O1CN011UUCXDGb8FC2qHR_!!3676232520.jpg',0],//74

                [($goods_first + 29),'https://img.alicdn.com/bao/uploaded/i4/1860270913/O1CN011IcC4c9Js0HDKIP_!!0-item_pic.jpg',0],//75
                [($goods_first + 29),'https://img.alicdn.com/bao/uploaded/i4/1860270913/TB22cc5hb3XS1JjSZFFXXcvupXa_!!1860270913.jpg',0],//75
                [($goods_first + 29),'https://img.alicdn.com/bao/uploaded/i4/1860270913/TB2l1ZveLBNTKJjSszbXXaFrFXa_!!1860270913.jpg',0],//75
                [($goods_first + 29),'https://img.alicdn.com/bao/uploaded/i1/1860270913/TB17Omre2JNTKJjSspoXXc6mpXa_!!0-item_pic.jpg',0],//75
                [($goods_first + 29),'https://img.alicdn.com/bao/uploaded/i4/1860270913/TB2KQQynEl7MKJjSZFDXXaOEpXa_!!1860270913.jpg',0],//75

                [($goods_first + 30),'https://img.alicdn.com/bao/uploaded/i2/95717838/O1CN0127lqpQ66cg2SzaP_!!95717838.jpg',0],//76
                [($goods_first + 30),'https://img.alicdn.com/bao/uploaded/i2/95717838/TB244PBD4GYBuNjy0FnXXX5lpXa_!!95717838.jpg',0],//76
                [($goods_first + 30),'https://img.alicdn.com/bao/uploaded/i3/95717838/TB2CxTBD4GYBuNjy0FnXXX5lpXa_!!95717838.jpg',0],//76
                [($goods_first + 30),'https://img.alicdn.com/bao/uploaded/i1/95717838/TB2w9UHDWmWBuNjy1XaXXXCbXXa_!!95717838.jpg',0],//76
                [($goods_first + 30),'https://img.alicdn.com/bao/uploaded/i1/95717838/TB2Zgt0vRyWBuNkSmFPXXXguVXa_!!95717838.png',0],//76
                [($goods_first + 30),'https://img.alicdn.com/bao/uploaded/i1/95717838/O1CN0127lqpR6YHGVtSSK_!!0-item_pic.jpg',0],//76

                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2017/585/339/4583933585_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2017/137/585/4584585731_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2017/867/285/4584582768_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2017/769/342/4580243967_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2017/837/886/4469688738_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2018/562/154/8630451265_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2018/940/202/8488202049_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2017/558/642/4580246855_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2017/107/195/4584591701_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2017/264/086/4674680462_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2017/108/268/4738862801_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2017/570/364/4581463075_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2018/209/789/8454987902_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2018/308/814/8630418803_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2018/647/771/8454177746_1472951930.jpg',0],//77
                [($goods_first + 31),'https://cbu01.alicdn.com/img/ibank/2018/103/289/8472982301_1472951930.jpg',0],//77
            ])->execute();



            //goodCat添加分类
            Yii::$app->db->createCommand()->batchInsert(GoodsCat::tableName(), ['goods_id','store_id','cat_id','addtime','is_delete'], [
                [$goods_first,$store_id,($cat1_id + 4),$time,0],//1
                [($goods_first + 1),$store_id,($cat1_id + 4),$time,0],//2
                [($goods_first + 2),$store_id,($cat1_id + 5),$time,0],//3
                [($goods_first + 3),$store_id,($cat1_id + 1),$time,0],//4
                [($goods_first + 4),$store_id,($cat1_id + 1),$time,0],//5
                [($goods_first + 5),$store_id,($cat1_id + 1),$time,0],//6
                [($goods_first + 6),$store_id,($cat1_id + 1),$time,0],//7
                [($goods_first + 7),$store_id,($cat1_id + 1),$time,0],//8
                [($goods_first + 8),$store_id,($cat1_id + 1),$time,0],//9
                [($goods_first + 9),$store_id,($cat1_id + 8),$time,0],//10

                [($goods_first + 10),$store_id,($cat1_id + 7),$time,0],//25
                [($goods_first + 11),$store_id,($cat1_id + 4),$time,0],//26
                [($goods_first + 12),$store_id,($cat1_id + 4),$time,0],//28
                [($goods_first + 13),$store_id,($cat1_id + 4),$time,0],//29

    //            [($goods_first + 14),$store_id,($cat1_id + 4),$time,0],//31
    //            [($goods_first + 15),$store_id,($cat1_id + 4),$time,0],//32
                [($goods_first + 16),$store_id,($cat1_id),$time,0],//34
                [($goods_first + 17),$store_id,($cat1_id + 6),$time,0],//38

                [($goods_first + 18),$store_id,($cat1_id + 5),$time,0],//39
                [($goods_first + 19),$store_id,($cat1_id + 4),$time,0],//40
                [($goods_first + 20),$store_id,($cat1_id + 5),$time,0],//41
    //            [($goods_first + 21),$store_id,($cat1_id + 4),$time,0],//48

    //            [($goods_first + 22),$store_id,($cat1_id + 4),$time,0],//49
                [($goods_first + 23),$store_id,($cat1_id),$time,0],//64
                [($goods_first + 24),$store_id,($cat1_id + 6),$time,0],//67
                [($goods_first + 25),$store_id,($cat1_id + 5),$time,0],//68
                [($goods_first + 26),$store_id,($cat1_id + 6),$time,0],//72
                [($goods_first + 27),$store_id,($cat1_id + 6),$time,0],//73
                [($goods_first + 28),$store_id,($cat1_id + 3),$time,0],//74
                [($goods_first + 29),$store_id,($cat1_id + 3),$time,0],//75
                [($goods_first + 30),$store_id,($cat1_id + 3),$time,0],//76
                [($goods_first + 31),$store_id,($cat1_id + 3),$time,0],//77
            ])->execute();

            //banner添加首页及拼团轮播图
            Yii::$app->db->createCommand()->batchInsert(Banner::tableName(), ['store_id','pic_url','title','page_url','sort','addtime','is_delete','type','open_type'], [
                [$store_id,'https://www.oggogg.com/web/uploads/image/c8/c8c83b353cdee70ad636098eeeb18b98.png','手机','/pages/shop/shop',100,$time,0,1,'navigate'],
                [$store_id,'https://www.oggogg.com/web/uploads/image/cf/cf147ab146626cd45dbf3d1f9b7acc1a.jpg','男装','/pages/shop/shop',100,$time,0,1,'navigate'],
                [$store_id,'https://www.oggogg.com/web/uploads/image/ad/ad407e92d01cb3c6d0f5ca626ba44b72.jpg','拼团','/pages/pt/index/index',100,$time,0,2,null],
            ])->execute();

            //homeNav添加导航栏
            Yii::$app->db->createCommand()->batchInsert(HomeNav::tableName(), ['store_id','name','url','open_type','pic_url','addtime','sort','is_delete','is_hide'], [
    //            [$store_id,'整点秒杀','/pages/miaosha/miaosha','navigate','https://www.oggogg.com/web/uploads/image/18/18db9e5c6eb1d3748c37f8e800eaec8e.png',$time,10,0,0],
                [$store_id,'拼团','/pages/pt/index/index','navigate','https://www.oggogg.com/web/uploads/image/e5/e5bc2043d0b534c1e3f2192085313e9c.png',$time,20,0,0],
                [$store_id,'预约','/pages/book/index/index','navigate','https://www.oggogg.com/web/uploads/image/17/1703b8fda871a7670ac2980562bf4e20.png',$time,30,0,0],
                [$store_id,'刮刮卡','/scratch/index/index','navigate','https://www.oggogg.com/web/uploads/image/82/821d1c974e299ebe1097ce7f940e233a.png',$time,40,0,0],
                [$store_id,'积分商城','/pages/integral-mall/index/index','navigate','https://www.oggogg.com/web/uploads/image/e2/e2cc1599cdec3e1fd26a1a703bb19c3d.png',$time,50,0,0],
                [$store_id,'砍价','/bargain/list/list','navigate','http://pcavxxbux.bkt.clouddn.com/web/uploads/image/bf/bf205c86919c57b237f9d18f8f683bd59b9d4318.png',$time,60,0,0],
                [$store_id,'视频专区','/pages/video/video-list','navigate','https://www.oggogg.com/web/uploads/image/5e/5eaaac5d3604b3dfe3af4ef270db8ee4.png',$time,70,0,0],
                [$store_id,'附近门店','/pages/shop/shop','navigate','https://www.oggogg.com/web/uploads/image/42/4261d3acf49ee68b063477d098ba7394.png',$time,95,0,0],
                [$store_id,'抽奖','/pond/pond/pond','navigate','https://www.oggogg.com/web/uploads/image/66/66575aa3ee1d9097e670ac5e3238373d.png',$time,90,0,0]
    //            [$store_id,'快速下单','/pages/quick-purchase/index/index','navigate','https://www.oggogg.com/web/uploads/image/3b/3b7b04157691a755cbbe5c0179bd06d0.png',$time,100,0,0],
            ])->execute();

            //homeblock添加图片魔方
            $homeBlock->store_id = $store_id;
            $homeBlock->name = '图片魔方';
            $homeBlock->data = '{"pic_list":[{"pic_url":"https:\/\/www.oggogg.com\/web\/uploads\/image\/b1\/b1976b4c9e8f1b8e3e0bf7d292b6b82c.jpg","url":"\/pages\/cat\/cat","open_type":"navigate"},{"pic_url":"https:\/\/www.oggogg.com\/web\/uploads\/image\/45\/45ded246586359c9629abc6e538355d1.jpg","url":"\/pages\/cat\/cat","open_type":"navigate"},{"pic_url":"https:\/\/www.oggogg.com\/web\/uploads\/image\/a4\/a4b865296e7170028986c591dbc59992.jpg","url":"\/pages\/fxhb\/open\/open","open_type":"navigate"},{"pic_url":"https:\/\/www.oggogg.com\/web\/uploads\/image\/ef\/efe1869f463f869fd52545fc1efd1b40.jpg","url":"\/pages\/cat\/cat","open_type":"navigate"}]}';
            $homeBlock->addtime = $time;
            $homeBlock->is_delete = 0;
            $homeBlock->style = 0;
            $homeBlock->save();
            $homeBlockId = $homeBlock->id;

            //coupon添加优惠券
            $coupon1 = $coupon->findOne(1);
            $coupon2 = $coupon->findOne(2);
            $coupon3 = $coupon->findOne(3);
            $coupon4 = $coupon->findOne(94);
            $coupon5 = $coupon->findOne(137);
            $coupon6 = $coupon->findOne(148);
            $coupon7 = $coupon->findOne(149);
            Yii::$app->db->createCommand()->batchInsert(Coupon::tableName(), ['store_id','name','desc','pic_url','discount_type','min_price','sub_price','discount','expire_type','expire_day','begin_time','end_time','addtime','is_delete','total_count','is_join','sort','cat_id_list','appoint_type','is_integral','integral','price','total_num','user_num','rule','goods_id_list'], [
                [$store_id,$coupon1->name,$coupon1->desc,$coupon1->pic_url,$coupon1->discount_type,$coupon1->min_price,$coupon1->sub_price,$coupon1->discount,$coupon1->expire_type,$coupon1->expire_day,$coupon1->begin_time,$coupon1->end_time,$coupon1->addtime,$coupon1->is_delete,$coupon1->total_count,$coupon1->is_join,$coupon1->sort,$coupon1->cat_id_list,$coupon1->appoint_type,$coupon1->is_integral,$coupon1->integral,$coupon1->price,$coupon1->total_num,$coupon1->user_num,$coupon1->rule,$coupon1->goods_id_list],
                [$store_id,$coupon2->name,$coupon2->desc,$coupon2->pic_url,$coupon2->discount_type,$coupon2->min_price,$coupon2->sub_price,$coupon2->discount,$coupon2->expire_type,$coupon2->expire_day,$coupon2->begin_time,$coupon2->end_time,$coupon2->addtime,$coupon2->is_delete,$coupon2->total_count,$coupon2->is_join,$coupon2->sort,$coupon2->cat_id_list,$coupon2->appoint_type,$coupon2->is_integral,$coupon2->integral,$coupon2->price,$coupon2->total_num,$coupon2->user_num,$coupon2->rule,$coupon2->goods_id_list],
                [$store_id,$coupon3->name,$coupon3->desc,$coupon3->pic_url,$coupon3->discount_type,$coupon3->min_price,$coupon3->sub_price,$coupon3->discount,$coupon3->expire_type,$coupon3->expire_day,$coupon3->begin_time,$coupon3->end_time,$coupon3->addtime,$coupon3->is_delete,$coupon3->total_count,$coupon3->is_join,$coupon3->sort,$coupon3->cat_id_list,$coupon3->appoint_type,$coupon3->is_integral,$coupon3->integral,$coupon3->price,$coupon3->total_num,$coupon3->user_num,$coupon3->rule,$coupon3->goods_id_list],
                [$store_id,$coupon4->name,$coupon4->desc,$coupon4->pic_url,$coupon4->discount_type,$coupon4->min_price,$coupon4->sub_price,$coupon4->discount,$coupon4->expire_type,$coupon4->expire_day,$coupon4->begin_time,$coupon4->end_time,$coupon4->addtime,$coupon4->is_delete,$coupon4->total_count,$coupon4->is_join,$coupon4->sort,$coupon4->cat_id_list,$coupon4->appoint_type,$coupon4->is_integral,$coupon4->integral,$coupon4->price,$coupon4->total_num,$coupon4->user_num,$coupon4->rule,$coupon4->goods_id_list],
                [$store_id,$coupon5->name,$coupon5->desc,$coupon5->pic_url,$coupon5->discount_type,$coupon5->min_price,$coupon5->sub_price,$coupon5->discount,$coupon5->expire_type,$coupon5->expire_day,$coupon5->begin_time,$coupon5->end_time,$coupon5->addtime,$coupon5->is_delete,$coupon5->total_count,$coupon5->is_join,$coupon5->sort,$coupon5->cat_id_list,$coupon5->appoint_type,$coupon5->is_integral,$coupon5->integral,$coupon5->price,$coupon5->total_num,$coupon5->user_num,$coupon5->rule,$coupon5->goods_id_list],
                [$store_id,$coupon6->name,$coupon6->desc,$coupon6->pic_url,$coupon6->discount_type,$coupon6->min_price,$coupon6->sub_price,$coupon6->discount,$coupon6->expire_type,$coupon6->expire_day,$coupon6->begin_time,$coupon6->end_time,$coupon6->addtime,$coupon6->is_delete,$coupon6->total_count,$coupon6->is_join,$coupon6->sort,$coupon6->cat_id_list,$coupon6->appoint_type,$coupon6->is_integral,$coupon6->integral,$coupon6->price,$coupon6->total_num,$coupon6->user_num,$coupon6->rule,$coupon6->goods_id_list],
                [$store_id,$coupon7->name,$coupon7->desc,$coupon7->pic_url,$coupon7->discount_type,$coupon7->min_price,$coupon7->sub_price,$coupon7->discount,$coupon7->expire_type,$coupon7->expire_day,$coupon7->begin_time,$coupon7->end_time,$coupon7->addtime,$coupon7->is_delete,$coupon7->total_count,$coupon7->is_join,$coupon7->sort,$coupon7->cat_id_list,$coupon7->appoint_type,$coupon7->is_integral,$coupon7->integral,$coupon7->price,$coupon7->total_num,$coupon7->user_num,$coupon7->rule,$coupon7->goods_id_list],
            ])->execute();



            //store添加首页模块布局及配置
            $storeOne = $store->findOne($store_id);
    //        $storeOne->home_page_module = '[{"name":"notice"},{"name":"search"},{"name":"banner"},{"name":"nav"},{"name":"topic"},{"name":"block-'.$homeBlockId.'"},{"name":"coupon"},{"name":"mch"},{"name":"miaosha"},{"name":"pintuan"},{"name":"yuyue"},{"name":"single_cat-'.$cat1_id.'"},{"name":"single_cat-'.($cat1_id+3).'"}]';
            $storeOne->home_page_module = '[{"name":"notice"},{"name":"search"},{"name":"banner"},{"name":"nav"},{"name":"topic"},{"name":"block-'.$homeBlockId.'"},{"name":"coupon"},{"name":"mch"},{"name":"pintuan"},{"name":"yuyue"},{"name":"single_cat-'.$cat1_id.'"},{"name":"single_cat-'.($cat1_id+3).'"}]';
            $storeOne->show_customer_service = 0;
            $storeOne->after_sale_time = 0;
            $storeOne->cat_style = 5;
            $storeOne->cat_goods_cols = 1;
            $storeOne->is_offline = 1;
            $storeOne->is_coupon = 1;
            $storeOne->send_type = 0;
    //        $storeOne->nav_count = 1;
            $storeOne->integration = '10积分抵扣1元';
            $storeOne->dial = 0;
            $storeOne->dial_pic = 'https://www.oggogg.com/web/uploads/image/03/03176905441bc3bf30202db540007e18.png0';
            $storeOne->cut_thread = 0;
            $storeOne->purchase_frame = 1;
            $storeOne->is_recommend = 1;
            $storeOne->recommend_count = 6;

            $storeOne->update();

    //        $shop添加门店
            $shop1 = $shop->findOne(1);
            $shop->store_id     = $store_id;
            $shop->name         = $shop1->name;
            $shop->mobile       = $shop1->mobile;
            $shop->address      = $shop1->address;
            $shop->is_delete    = $shop1->is_delete;
            $shop->addtime      = $shop1->addtime;
            $shop->longitude    = $shop1->longitude;
            $shop->latitude     = $shop1->latitude;
            $shop->score        =$shop1->score;
            $shop->cover_url    = $shop1->cover_url;
            $shop->pic_url      = $shop1->pic_url;
            $shop->shop_time    = $shop1->shop_time;
            $shop->content      = $shop1->content;
            $shop->is_default   = $shop1->is_default;
            $shop->save();
            $shopId = $shop->id;
            $shop2 = $shop->findOne(2);
            $shop3 = $shop->findOne(3);
            $shop4 = $shop->findOne(6);
            Yii::$app->db->createCommand()->batchInsert(Shop::tableName(), ['store_id','name','mobile','address','is_delete','addtime','longitude','latitude','score','cover_url','pic_url','shop_time','content','is_default'], [
                [$store_id,$shop2->name,$shop2->mobile,$shop2->address,$shop2->is_delete,$shop2->addtime,$shop2->longitude,$shop2->latitude,$shop2->score,$shop2->cover_url,$shop2->pic_url,$shop2->shop_time,$shop2->content,$shop2->is_default],
                [$store_id,$shop3->name,$shop3->mobile,$shop3->address,$shop3->is_delete,$shop3->addtime,$shop3->longitude,$shop3->latitude,$shop3->score,$shop3->cover_url,$shop3->pic_url,$shop3->shop_time,$shop3->content,$shop3->is_default],
                [$store_id,$shop4->name,$shop4->mobile,$shop4->address,$shop4->is_delete,$shop4->addtime,$shop4->longitude,$shop4->latitude,$shop4->score,$shop4->cover_url,$shop4->pic_url,$shop4->shop_time,$shop4->content,$shop4->is_default],
            ])->execute();

    //        shopPic添加门店图片


    //        $yyCat预约分类
            $yyCat1 = $yyCat->findOne(1);
            $yyCat->name      = $yyCat1->name;
            $yyCat->store_id  = $store_id;
            $yyCat->pic_url   = $yyCat1->pic_url;
            $yyCat->sort      = $yyCat1->sort;
            $yyCat->addtime   = $yyCat1->addtime;
            $yyCat->is_delete = $yyCat1->is_delete;
            $yyCat->save();
            $yyCatId = $yyCat->id;
            $yyCat2 = $yyCat->findOne(2);
            $yyCat3 = $yyCat->findOne(3);
            $yyCat4 = $yyCat->findOne(5);
            Yii::$app->db->createCommand()->batchInsert(YyCat::tableName(), ['name','store_id','pic_url','sort','addtime','is_delete'], [
                [$yyCat2->name,$store_id,$yyCat2->pic_url,$yyCat2->sort,$yyCat2->addtime,$yyCat2->is_delete],
                [$yyCat3->name,$store_id,$yyCat3->pic_url,$yyCat3->sort,$yyCat3->addtime,$yyCat3->is_delete],
                [$yyCat4->name,$store_id,$yyCat4->pic_url,$yyCat4->sort,$yyCat4->addtime,$yyCat4->is_delete],
            ])->execute();

    //        $yyGoods添加预约商品
            $yyGoods1 = $yyGoods->findOne(1);
            $yyGoods->name            = $yyGoods1->name;
            $yyGoods->price           = $yyGoods1->price;
            $yyGoods->original_price  = $yyGoods1->original_price;
            $yyGoods->detail          = $yyGoods1->detail;
            $yyGoods->cat_id          = $yyCatId;
            $yyGoods->status          = $yyGoods1->status;
            $yyGoods->service         = $yyGoods1->service;
            $yyGoods->sort            = $yyGoods1->sort;
            $yyGoods->virtual_sales   = $yyGoods1->virtual_sales;
            $yyGoods->cover_pic       = $yyGoods1->cover_pic;
            $yyGoods->addtime         = $yyGoods1->addtime;
            $yyGoods->is_delete       = $yyGoods1->is_delete;
            $yyGoods->sales           = $yyGoods1->sales;
            $yyGoods->shop_id         = $shopId . ',' . ($shopId + 1);
            $yyGoods->store_id        = $store_id;
            $yyGoods->buy_limit       = $yyGoods1->buy_limit;
            $yyGoods->stock           = $yyGoods1->stock;
            $yyGoods->attr            = $yyGoods1->attr;
            $yyGoods->use_attr        = $yyGoods1->use_attr;
            $yyGoods->save();
            $yyGoodsId = $yyGoods->id;
            $yyGoods2 = $yyGoods->findOne(2);
            $yyGoods3 = $yyGoods->findOne(3);
            $yyGoods4 = $yyGoods->findOne(4);
            Yii::$app->db->createCommand()->batchInsert(YyGoods::tableName(), ['name','price','original_price','detail','cat_id','status','service','sort','virtual_sales','cover_pic','addtime','is_delete','sales','shop_id','store_id','buy_limit','stock','attr','use_attr'], [
                [$yyGoods2->name,$yyGoods2->price,$yyGoods2->original_price,$yyGoods2->detail,($yyCatId+1),$yyGoods2->status,$yyGoods2->service,$yyGoods2->sort,$yyGoods2->virtual_sales,$yyGoods2->cover_pic,$yyGoods2->addtime,$yyGoods2->is_delete,$yyGoods2->sales,$shopId . ',' . ($shopId + 2),$store_id,$yyGoods2->buy_limit,$yyGoods2->stock,$yyGoods2->attr,$yyGoods2->use_attr],
                [$yyGoods3->name,$yyGoods3->price,$yyGoods3->original_price,$yyGoods3->detail,($yyCatId+2),$yyGoods3->status,$yyGoods3->service,$yyGoods3->sort,$yyGoods3->virtual_sales,$yyGoods3->cover_pic,$yyGoods3->addtime,$yyGoods3->is_delete,$yyGoods3->sales,$shopId . ',' . ($shopId + 3),$store_id,$yyGoods3->buy_limit,$yyGoods3->stock,$yyGoods3->attr,$yyGoods3->use_attr],
                [$yyGoods4->name,$yyGoods4->price,$yyGoods4->original_price,$yyGoods4->detail,($yyCatId+3),$yyGoods4->status,$yyGoods4->service,$yyGoods4->sort,$yyGoods4->virtual_sales,$yyGoods4->cover_pic,$yyGoods4->addtime,$yyGoods4->is_delete,$yyGoods4->sales,$shopId,$store_id,$yyGoods4->buy_limit,$yyGoods4->stock,$yyGoods4->attr,$yyGoods4->use_attr],
            ])->execute();

    //        yyGoodsPic添加预约图片
            Yii::$app->db->createCommand()->batchInsert(YyGoodsPic::tableName(), ['goods_id','pic_url','is_delete'], [
                [$yyGoodsId,'https://www.oggogg.com/web/uploads/image/f8/f845547922e4607f46c15fc1fd0a53d6.png',0],
                [($yyGoodsId + 1),'https://www.oggogg.com/web/uploads/image/43/434c4985ac5023459dacd704c1a91dba.jpg',0],
                [($yyGoodsId + 2),'https://www.oggogg.com/web/uploads/image/1d/1d2facea9486898751fc719ca78b6a35.jpg',0],
                [($yyGoodsId + 3),'https://www.oggogg.com/web/uploads/image/46/460bca792eee24d8c37b604fd4a438f7.png',0],
            ])->execute();

    //        YySetting添加预约设置
            $yySettingOne = $yySetting->findOne(1);
            $yySetting->store_id       = $store_id;
            $yySetting->cat            = $yySettingOne->cat;
            $yySetting->success_notice = $yySettingOne->success_notice;
            $yySetting->refund_notice  = $yySettingOne->refund_notice;
            $yySetting->is_share       = $yySettingOne->is_share;
            $yySetting->is_sms         = $yySettingOne->is_sms;
            $yySetting->is_print       = $yySettingOne->is_print;
            $yySetting->is_mail        = $yySettingOne->is_mail;
            $yySetting->save();

    //        video添加视频专区
            $video1 = $video->findOne(1);
            $video2 = $video->findOne(2);
            Yii::$app->db->createCommand()->batchInsert(Video::tableName(), ['title','url','sort','is_delete','addtime','store_id','pic_url','content','type'], [
                [$video1->title,'https://v.qq.com/x/page/h0148viun9g.html',$video1->sort,$video1->is_delete,$video1->addtime,$store_id,$video1->pic_url,$video1->content,$video1->type],
                ['创意服装广告','https://v.qq.com/x/page/h0148viun9g.html',$video2->sort,$video2->is_delete,$video2->addtime,$store_id,$video2->pic_url,'创意服装广告',$video2->type],
            ])->execute();

    //        $ptCat添加拼团分类
            $ptCat1 = $ptCat->findOne(1);
            $ptCat->name      = $ptCat1->name;
            $ptCat->store_id  = $store_id;
            $ptCat->pic_url   = $ptCat1->pic_url;
            $ptCat->sort      = $ptCat1->sort;
            $ptCat->addtime   = $ptCat1->addtime;
            $ptCat->is_delete = $ptCat1->is_delete;
            $ptCat->save();
            $ptCatId = $ptCat->id;
            $ptCat2 = $ptCat->findOne(2);
            Yii::$app->db->createCommand()->batchInsert(PtCat::tableName(), ['name','store_id','pic_url','sort','addtime','is_delete'], [
                [$ptCat2->name,$store_id,$ptCat2->pic_url,$ptCat2->sort,$ptCat2->addtime,$ptCat2->is_delete],
            ])->execute();

    //        $ptGoods添加拼团商品
            $ptGoods1 = $ptGoods->findOne(2);
            $ptGoods->store_id      = $store_id;
            $ptGoods->name           = $ptGoods1->name;
            $ptGoods->original_price = $ptGoods1->original_price;
            $ptGoods->price          = $ptGoods1->price;
            $ptGoods->detail         = $ptGoods1->detail;
            $ptGoods->cat_id         = $ptCatId;
            $ptGoods->status         = $ptGoods1->status;
            $ptGoods->grouptime      = $ptGoods1->grouptime;
            $ptGoods->attr           = $ptGoods1->attr;
            $ptGoods->service        = $ptGoods1->service;
            $ptGoods->sort           = $ptGoods1->sort;
            $ptGoods->virtual_sales  = $ptGoods1->virtual_sales;
            $ptGoods->cover_pic      = $ptGoods1->cover_pic;
            $ptGoods->weight         = $ptGoods1->weight;
            $ptGoods->freight        = $ptGoods1->freight;
            $ptGoods->unit           = $ptGoods1->unit;
            $ptGoods->addtime        = $ptGoods1->addtime;
            $ptGoods->is_delete      = $ptGoods1->is_delete;
            $ptGoods->group_num      = $ptGoods1->group_num;
            $ptGoods->is_hot         = $ptGoods1->is_hot;
            $ptGoods->limit_time     = $ptGoods1->limit_time;
            $ptGoods->is_only        = $ptGoods1->is_only;
            $ptGoods->is_more        = $ptGoods1->is_more;
            $ptGoods->colonel        = $ptGoods1->colonel;
            $ptGoods->buy_limit      = $ptGoods1->buy_limit;
            $ptGoods->type           = $ptGoods1->type;
            $ptGoods->use_attr       = $ptGoods1->use_attr;
            $ptGoods->one_buy_limit  = $ptGoods1->one_buy_limit;
            $ptGoods->payment        = $ptGoods1->payment;
            $ptGoods->save();
            $ptGoodsId = $ptGoods->id;
            $ptGoods2 = $ptGoods->findOne(3);
            $ptGoods3 = $ptGoods->findOne(4);
            $ptGoods4 = $ptGoods->findOne(9);
            Yii::$app->db->createCommand()->batchInsert(PtGoods::tableName(), ['store_id','name','original_price','price','detail','cat_id','status','grouptime','attr','service','sort','virtual_sales','cover_pic','weight','freight','unit','addtime','is_delete','group_num','is_hot','limit_time','is_only','is_more','colonel','buy_limit','type','use_attr','one_buy_limit','payment'], [
                [$store_id,$ptGoods2->name,$ptGoods2->original_price,$ptGoods2->price,$ptGoods2->detail,($ptCatId + 1),$ptGoods2->status,$ptGoods2->grouptime,$ptGoods2->attr,$ptGoods2->service,$ptGoods2->sort,$ptGoods2->virtual_sales,$ptGoods2->cover_pic,$ptGoods2->weight,$ptGoods2->freight,$ptGoods2->unit,$ptGoods2->addtime,$ptGoods2->is_delete,$ptGoods2->group_num,$ptGoods2->is_hot,$ptGoods2->limit_time,$ptGoods2->is_only,$ptGoods2->is_more,$ptGoods2->colonel,$ptGoods2->buy_limit,$ptGoods2->type,$ptGoods2->use_attr,$ptGoods2->one_buy_limit,$ptGoods2->payment],
                [$store_id,$ptGoods3->name,$ptGoods3->original_price,$ptGoods3->price,$ptGoods3->detail,($ptCatId + 1),$ptGoods3->status,$ptGoods3->grouptime,$ptGoods3->attr,$ptGoods3->service,$ptGoods3->sort,$ptGoods3->virtual_sales,$ptGoods3->cover_pic,$ptGoods3->weight,$ptGoods3->freight,$ptGoods3->unit,$ptGoods3->addtime,$ptGoods3->is_delete,$ptGoods3->group_num,$ptGoods3->is_hot,$ptGoods3->limit_time,$ptGoods3->is_only,$ptGoods3->is_more,$ptGoods3->colonel,$ptGoods3->buy_limit,$ptGoods3->type,$ptGoods3->use_attr,$ptGoods3->one_buy_limit,$ptGoods3->payment],
                [$store_id,$ptGoods4->name,$ptGoods4->original_price,$ptGoods4->price,$ptGoods4->detail,($ptCatId + 1),$ptGoods4->status,$ptGoods4->grouptime,$ptGoods4->attr,$ptGoods4->service,$ptGoods4->sort,$ptGoods4->virtual_sales,$ptGoods4->cover_pic,$ptGoods4->weight,$ptGoods4->freight,$ptGoods4->unit,$ptGoods4->addtime,$ptGoods4->is_delete,$ptGoods4->group_num,$ptGoods4->is_hot,$ptGoods4->limit_time,$ptGoods4->is_only,$ptGoods4->is_more,$ptGoods4->colonel,$ptGoods4->buy_limit,$ptGoods4->type,$ptGoods4->use_attr,$ptGoods4->one_buy_limit,$ptGoods4->payment],
            ])->execute();

    //        $ptGoodsPic设置拼团商品图片
            Yii::$app->db->createCommand()->batchInsert(PtGoodsPic::tableName(), ['goods_id','pic_url','is_delete'], [
                [$ptGoodsId,'http://img14.360buyimg.com/n12/jfs/t19684/10/8194561/275174/7064f997/5a574e65N03acae29.jpg',0],
                [$ptGoodsId,'http://img14.360buyimg.com/n12/jfs/t13879/262/2205608977/221662/bea55727/5a34dc30Na57108ad.jpg',0],
                [$ptGoodsId,'http://img14.360buyimg.com/n12/jfs/t14965/135/707679680/119827/9d453357/5a34dc30N3a7a0da6.jpg',0],
                [$ptGoodsId,'http://img14.360buyimg.com/n12/jfs/t16138/58/444622417/224213/90f646b1/5a34dc31N6fda5655.jpg',0],
                [$ptGoodsId,'http://img14.360buyimg.com/n12/jfs/t16321/19/515604878/243075/a1c46fbb/5a34dc18N32e7c5a3.jpg',0],
                [($ptGoodsId + 1),'http://img11.360buyimg.com/n12/jfs/t18781/352/921525177/237175/d5bc9023/5ab28a3eN09951a2b.jpg',0],
                [($ptGoodsId + 1),'http://img11.360buyimg.com/n12/jfs/t17338/181/949819477/276659/d5f6ae93/5ab28a3bN94af1120.jpg',0],
                [($ptGoodsId + 1),'http://img11.360buyimg.com/n12/jfs/t15667/305/2444943693/205574/f6521acd/5aaf1167Nfac6a77d.jpg',0],
                [($ptGoodsId + 1),'http://img11.360buyimg.com/n12/jfs/t15694/158/2570608721/271586/97c217b9/5aaf1169N2486d9b8.jpg',0],
                [($ptGoodsId + 1),'http://img11.360buyimg.com/n12/jfs/t15718/327/2538864482/246502/d04a8f3c/5aaf116bN6353ddf6.jpg',0],
                [($ptGoodsId + 1),'http://img11.360buyimg.com/n12/jfs/t14539/123/2617886869/117497/3b3b941f/5aaf116dN297fc88f.jpg',0],
                [($ptGoodsId + 2),'http://img10.360buyimg.com/n12/jfs/t16891/109/963635717/204692/9710b828/5ab28a37Ne7b3a1dc.jpg',0],
                [($ptGoodsId + 2),'http://img10.360buyimg.com/n12/jfs/t16429/156/2560373877/228007/d0cf8ea4/5ab28a32Nce490594.jpg',0],
                [($ptGoodsId + 2),'http://img10.360buyimg.com/n12/jfs/t17980/332/436394345/239301/26266c53/5a7ab114N5d8d13b9.jpg',0],
                [($ptGoodsId + 2),'http://img10.360buyimg.com/n12/jfs/t15442/348/2225777371/169049/86836782/5a7ab11bN0d69adf1.jpg',0],
                [($ptGoodsId + 2),'http://img10.360buyimg.com/n12/jfs/t17497/75/448194751/180009/b3b56d18/5a7ab120Nc833789a.jpg',0],
                [($ptGoodsId + 2),'http://img10.360buyimg.com/n12/jfs/t15886/104/2016650368/88015/919c8f28/5a7ab122N4e6d10e4.jpg',0],
                [($ptGoodsId + 3),'https://img.alicdn.com/bao/uploaded/i3/924534709/TB2GrTaafJNTKJjSspoXXc6mpXa_!!924534709.jpg',0],
                [($ptGoodsId + 3),'https://img.alicdn.com/bao/uploaded/i3/924534709/TB2Iufme8USMeJjy1zjXXc0dXXa_!!924534709.jpg',0],
                [($ptGoodsId + 3),'https://img.alicdn.com/bao/uploaded/i4/924534709/TB2VJ43gKtTMeFjSZFOXXaTiVXa_!!924534709.jpg',0],
                [($ptGoodsId + 3),'https://img.alicdn.com/bao/uploaded/i1/924534709/TB2LuA6ehaJ.eBjSsziXXaJ_XXa_!!924534709.jpg',0],
                [($ptGoodsId + 3),'https://img.alicdn.com/bao/uploaded/i1/TB1MLcvOpXXXXXUXpXXXXXXXXXX_!!0-item_pic.jpg',0],
            ])->execute();

    //        $ptSetting初始化拼团设置
            $ptSetting->store_id = $store_id;
            $ptSetting->is_share = 0;
            $ptSetting->is_sms = 0;
            $ptSetting->is_print = 0;
            $ptSetting->is_mail = 0;
            $ptSetting->is_area = null;
            $ptSetting->save();

            //option添加公告,设置拼团广告,设置用户中心菜单
            Yii::$app->db->createCommand()->batchInsert(Option::tableName(), ['store_id','group','name','value'], [
                [$store_id, 'admin','notice','"公告图标、背景色、公告文字自定义"'],
                [$store_id,'','pt_ad','a:4:{i:0;a:3:{s:7:"pic_url";s:82:"https://www.oggogg.com/web/uploads/image/45/45ded246586359c9629abc6e538355d1.jpg";s:3:"url";s:31:"/pages/pt/details/details?gid=' . ($ptGoodsId + 2) . '";s:9:"open_type";s:8:"navigate";}i:1;a:3:{s:7:"pic_url";s:82:"https://www.oggogg.com/web/uploads/image/45/45ded246586359c9629abc6e538355d1.jpg";s:3:"url";s:31:"/pages/pt/details/details?gid=' . ($ptGoodsId + 2) . '";s:9:"open_type";s:8:"navigate";}i:2;a:3:{s:7:"pic_url";s:82:"https://www.oggogg.com/web/uploads/image/a4/a4b865296e7170028986c591dbc59992.jpg";s:3:"url";s:31:"/pages/pt/details/details?gid=' . ($ptGoodsId + 2) . '";s:9:"open_type";s:8:"navigate";}i:3;a:3:{s:7:"pic_url";s:82:"https://www.oggogg.com/web/uploads/image/ef/efe1869f463f869fd52545fc1efd1b40.jpg";s:3:"url";s:31:"/pages/pt/details/details?gid=' . ($ptGoodsId + 2) . '";s:9:"open_type";s:8:"navigate";}}'],
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
