<?php
/**
 * author: wxf
 */

namespace app\modules\mch\controllers;

use app\models\FxhbSetting;
use app\models\Option;
use app\models\WechatTemplateMessage;
use app\models\YySetting;
use app\modules\mch\models\group\NoticeForm;
use app\modules\mch\models\WechatSettingForm;
use yii\db\Exception;
use app\models\WechatApp;
use app\modules\mch\models\UploadForm;
use yii\web\UploadedFile;
use app\models\Apply;
use app\models\Audit;
use app\models\AdminAuth;
use app\models\Admin;
use app\modules\admin\models\password\Password;


class WechatController extends Controller
{
      protected $appid = 'wx2bdc1845f7e64617';            //第三方平台应用appid

    private $appsecret = '53905d776670e2ef1bca0ec064143e9d';     //第三方平台应用appsecret

    private $token = 'weixin';           //第三方平台应用token（消息校验Token）

    private $encodingAesKey = 'oiO8KM82sa05W4S1BpGgROnx64QpzeW5KJNur6P7VZG';      //第三方平台应用Key（消息加解密Key）

    private $component_ticket = '';   //微信后台推送的ticket,用于获取第三方平台接口调用凭据

    private $component_access_token;  //第三方平台component_access_token

    private $authorizer_appid;

    private $authorizer_access_token;

    private $authorizer_refresh_token;
  
   public function actionMpConfig()
    {
     
     
        $data = Apply::find()->where(['uid'=>\Yii::$app->admin->id,'is_delete'=> 0])->one();
        $this->authorizer_refresh_token = $data->authorizer_refresh_token;
        $this->authorizer_access_token = $data->authorizer_access_token;
        $this->authorizer_appid = $data->authorizer_appid;
        $datas = AdminAuth::find()->where(['id'=>1])->one();
        $this->component_access_token = $datas->component_access_token;

        if($data->authorizer_expires < time()){

            $this->update_authorizer_access_token($data->authorizer_appid,$data->authorizer_refresh_token);
            $data = Apply::find()->where(['uid'=>\Yii::$app->admin->id])->one();
            $this->authorizer_refresh_token = $data->authorizer_refresh_token;
            $this->authorizer_access_token = $data->authorizer_access_token;
            $this->authorizer_appid = $data->authorizer_appid;
            $datas = AdminAuth::find()->where(['id'=>1])->one();
            $this->component_access_token = $datas->component_access_token;

        }
		
   	 if (\Yii::$app->request->isPost) {

            $form = new WechatSettingForm();
            $form->attributes = \Yii::$app->request->post();

            $form->model = $this->wechat_app;
            return $form->save();
        } else {
            $url = \Yii::$app->request->getHostInfo().\Yii::$app->request->url;
       		if($url != "https://www.oggogg.com/web/role.php?r=mch%2Fwechat%2Fmp-config"){
              $res = Admin::find()->where(['id'=>\Yii::$app->admin->id])->one();
               if(!$res['mobile'] || !$res['truename'] || !$res['category']){
               $this->redirect(\Yii::$app->urlManager->createUrl(['mch/wechat/shop']));
            	}
            }
              
      
                $ret = $this->getLastAudit();

                if($ret->errcode == 0 && $ret){

                    $data_audit = Audit::find()->where(['appid'=>$data->authorizer_appid])->one();
                    if($data_audit->status == 5){
                        $result['status'] = 5;
                        $result['auditid'] = $data_audit->auditid;
                    }else{

                        $result['errcode'] = $ret->errcode;
                        $result['errmsg'] = $ret->errmsg;
                        $result['auditid'] = $ret->auditid;
                        $result['status'] = $ret->status;
                        $result['reason'] = $ret->reason;

                    }
                }else if($ret->errcode == 85058 || !$ret){

                    $result['status'] = 4;
                }

                return $this->render('mp-config', [
                    'model' => $this->wechat_app,
                    'ret' => $result,
                ]);
            


        }

    }


    /*

        * 查询最新一次提交的审核状态

        * */

    public function getLastAudit()

    {

        $url = "https://api.weixin.qq.com/wxa/get_latest_auditstatus?access_token=".$this->authorizer_access_token;

        $ret = json_decode($this->https_get($url));
    
        if($ret->errcode == 0) {

            $reason = $ret->reason ? $ret->reason : '';

            $model = Audit::find()->where(['appid'=>$this->authorizer_appid])->one();
          
			if($model && $model->status != 5){
               $model->auditid = $ret->auditid;
                $model->status = $ret->status;
                $model->reason = $reason;
                $model->save();
                return $ret;
            }else{
               return false;
            }

        } else {
            return $ret;
        }

    }

    /**
     * 微信所有模块消息配置
     * @return string
     * @throws Exception
     */
    public function actionTemplateMsg()
    {
        if (\Yii::$app->request->isPost) {
            $data = \Yii::$app->request->post();
            try {
                $transaction = \Yii::$app->db->beginTransaction();
                //商城
                $store = WechatTemplateMessage::findOne(['store_id' => $this->store->id]);
                if (!$store) {
                    $store = new WechatTemplateMessage();
                }
                $store->store_id = $this->store->id;
                $store->attributes = $data;
                $store->save();

                //分销
                Option::setList([
                    [
                        'name' => 'cash_success_tpl',
                        'value' => $data['cash_success_tpl'],
                    ],
                    [
                        'name' => 'cash_fail_tpl',
                        'value' => $data['cash_fail_tpl'],
                    ],
                    [
                        'name' => 'apply_tpl',
                        'value' => $data['apply_tpl'],
                    ],
                ], $this->store->id, 'share');


                // 拼团
                $pintuan = new NoticeForm();
                $pintuan->pintuan_success_notice = $data['pintuan_success_notice'];
                $pintuan->pintuan_fail_notice = $data['pintuan_fail_notice'];
                $pintuan->pintuan_refund_notice = $data['pintuan_refund_notice'];
                $pintuan->store_id = $this->store->id;
                $pintuan->save();

                // 预约
                $setting = YySetting::findOne(['store_id' => $this->store->id]);
                if (!$setting) {
                    $setting = new YySetting();
                    $setting->store_id = $this->store->id;
                    $setting->cat = 0;
                }
                $setting->success_notice = $data['yy_success_notice'];
                $setting->refund_notice = $data['yy_refund_notice'];
                $setting->save();

                // 多商户
                Option::set('mch_tpl_msg', [
                    'apply' => \Yii::$app->request->post('mch_tpl_1', ''),
                    'order' => \Yii::$app->request->post('mch_tpl_2', ''),
                ], $this->store->id);

                //抽奖
                Option::set('lottery_success_notice', $data['lottery_success_notice'], $this->store->id, 'lottery');

                $fxhbTplMsg = FxhbSetting::findOne(['store_id' => $this->store->id]);
                if (!$fxhbTplMsg) {
                    $fxhbTplMsg = new FxhbSetting();
                }
                $fxhbTplMsg->tpl_msg_id = $data['tpl_msg_id'];
                $fxhbTplMsg->store_id = $this->store->id;
                $fxhbTplMsg->save();

                $transaction->commit();
                return [
                    'code' => 0,
                    'msg' => '保存成功'
                ];

            } catch (Exception $e) {
                $transaction->rollBack();
                throw new Exception($e);
            }

        } else {
            $storeTplMsg = WechatTemplateMessage::find()->where(['store_id' => $this->store->id])->asArray()->one();
            $shareTplMsg = Option::getList('cash_success_tpl,cash_fail_tpl,apply_tpl', $this->store->id, 'share', '');

            $form = new NoticeForm();
            $form->store_id = $this->store->id;
            $ptTplMsg = $form->getModel();

            $bookTplMsg = YySetting::find()->where(['store_id' => $this->store->id])->asArray()->one();
            $mchTplMsg = Option::get('mch_tpl_msg', $this->store->id, ['apply' => '', 'order' => '']);
            $fxhbTplMsg = FxhbSetting::find()->where(['store_id' => $this->store->id])->asArray()->one();
            $lotteryTplMsg = Option::getList('lottery_success_notice', $this->store->id, 'lottery','');

            // 当前用户插件权限
            $userAuth = $this->getUserAuth();
            $tplMsg = [
                'store' => $storeTplMsg,
                'share' => $shareTplMsg,
                'pintuan' => $ptTplMsg,
                'book' => $bookTplMsg,
                'mch' => $mchTplMsg,
                'fxhb' => $fxhbTplMsg,
                'lottery' => $lotteryTplMsg,
            ];

            foreach ($tplMsg as $k => $item) {
                // $k === store 商城的模版消息不需要判断权限
                if (in_array($k, $userAuth) || $k === 'store') {
                    $tplMsg[$k]['is_show'] = true;
                    continue;

                }
                $tplMsg[$k]['is_show'] = false;
            }

            return $this->render('template-msg', [
                'tplMsg' => $tplMsg,
            ]);
        }
    }

    public function actionUpload(){

        $params=\Yii::$app->request->post();

        $model = new UploadForm();

        if (\Yii::$app->request->isPost) {

            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            $data = WechatApp::find()->where('user_id',\Yii::$app->admin->id)->one();

           $res =  $model->imageFile->saveAs('uploads/cert/' . $data->app_id . '.' . $model->imageFile->extension);

            $file = 'uploads/cert/' . $data->app_id . '.' . $model->imageFile->extension;
            $path = 'uploads/cert/' . $data->app_id . '/';
            $this->get_zip_originalsize($file,$path);

            $file_path = 'uploads/cert/' . $data->app_id . '/'.'apiclient_cert.pem';
            if(file_exists($file_path)){
                $fp = fopen($file_path,"r");
                $apiclient_cert = fread($fp,filesize($file_path));//指定读取大小，这里把整个文件内容读取出来
                fclose($fp);
            }
            $file_path = 'uploads/cert/' . $data->app_id . '/'.'apiclient_key.pem';
            if(file_exists($file_path)){
                $fp = fopen($file_path,"r");
                $apiclient_key = fread($fp,filesize($file_path));//指定读取大小，这里把整个文件内容读取出来
                fclose($fp);
            }

            $data = WechatApp::find()->where('user_id',\Yii::$app->admin->id)->one();
            $data->cert_pem = $apiclient_cert;
            $data->key_pem = $apiclient_key;
            $data->save();

            $res=array();
            if ($data->save()) {
                // 文件上传成功
                $res['code'] = 0;
                $res['cert_pem'] = $apiclient_cert;
                $res['key_pem'] = $apiclient_key;
                return $res ;
            }else{
                $res['code'] = 1;
                return $res ;
            }
        }

    }


    function get_zip_originalsize($filename, $path) {
        //先判断待解压的文件是否存在
     if(!file_exists($filename)){
         die("文件 $filename 不存在！");
      }
      //将文件名和路径转成windows系统默认的gb2312编码，否则将会读取不到
      $filename = iconv("utf-8","gb2312",$filename);
      $path = iconv("utf-8","gb2312",$path);
      //打开压缩包
      $resource = zip_open($filename);
      $i = 1;
      //遍历读取压缩包里面的一个个文件
      while ($dir_resource = zip_read($resource)) {
                    //如果能打开则继续
        if (zip_entry_open($resource,$dir_resource)) {
                          //获取当前项目的名称,即压缩包里面当前对应的文件名
          $file_name = $path.zip_entry_name($dir_resource);
          //以最后一个“/”分割,再用字符串截取出路径部分
          $file_path = substr($file_name,0,strrpos($file_name, "/"));
          //如果路径不存在，则创建一个目录，true表示可以创建多级目录
          if(!is_dir($file_path)){
                                mkdir($file_path,0777,true);
          }
          //如果不是目录，则写入文件
          if(!is_dir($file_name)){
                               //读取这个文件
            $file_size = zip_entry_filesize($dir_resource);
            //最大读取6M，如果文件过大，跳过解压，继续下一个
            if($file_size<(1024*1024*30)){
                                      $file_content = zip_entry_read($dir_resource,$file_size);
             file_put_contents($file_name,$file_content);
           }else{
                                      echo "<p> ".$i++." 此文件已被跳过，原因：文件过大， -> ".iconv("gb2312","utf-8",$file_name)." </p>";
            }
          }
          //关闭当前
          zip_entry_close($dir_resource);
        }
      }
      //关闭压缩包
      zip_close($resource);

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


    private function update_authorizer_access_token($appid,$refresh_token)

    {

        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=' . $this->component_access_token;

        $data = '{"component_appid":"' . $this->appid . '","authorizer_appid":"' . $appid . '","authorizer_refresh_token":"' . $refresh_token . '"}';

        $ret = json_decode($this->https_post($url, $data));

        if (isset($ret->authorizer_access_token)) {
            $data = Apply::find()->where(['uid'=>\Yii::$app->admin->id])->one();
            $data->authorizer_access_token = $ret->authorizer_access_token;
            $data->authorizer_expires = time() + 7200;
            $data->authorizer_refresh_token = $ret->authorizer_refresh_token;
            $data->save();
//            Apply::find()->updateAll(['authorizer_appid' => $appid], ['authorizer_access_token' => $ret->authorizer_access_token, 'authorizer_expires' => (time() + 7200), 'authorizer_refresh_token' => $ret->authorizer_refresh_token]);

            return $ret;

        } else {

            return null;

        }

    }
  
    public function actionPhone(){
        if(\Yii::$app->request->isPost){
            $truename = \Yii::$app->request->post('username');
            $mobile = \Yii::$app->request->post('mobile');
            $sms_code = \Yii::$app->request->post('sms_code');


            $res = array();
            $data = \Yii::$app->session->get(Password::RESET_PASSWORD_SMS_CODE);

            //校验用户验证短信验证码的次数
            $max_validate_count = 20;
            $validate_count = \Yii::$app->session->get(Password::RESET_PASSWORD_SMS_CODE_VALIDATE_COUNT, 0);
            if ($validate_count >= $max_validate_count){
                $res['code'] = 1;
                $res['text'] = "您输入的短信验证码错误次数过多，请刷新页后面重试。";
                return $res;
            }

            if(strval($data['code']) !== strval($sms_code)){
                $validate_count = intval(\Yii::$app->session->get(Password::RESET_PASSWORD_SMS_CODE_VALIDATE_COUNT, 0));
                \Yii::$app->session->set(Password::RESET_PASSWORD_SMS_CODE_VALIDATE_COUNT, $validate_count + 1);
                $res['code'] = 1;
                $res['text'] = "短信验证码错误。";
                return $res;
            }

            $result = Admin::find()->where(['id'=>\Yii::$app->admin->id])->one();
            $result->truename = $truename;
            $result->mobile = $mobile;

            if($result->save()){
                $res['code'] = 0;
                $res['text'] = "验证成功。";
                return $res;
            }else{
                $res['code'] = 1;
                $res['text'] = "验证失败。";
                return $res;
            }

        }

    }

    public function actionShop(){
        if(\Yii::$app->request->isPost){
            $category = \Yii::$app->request->post('category');
            $result = Admin::find()->where(['id'=>\Yii::$app->admin->id])->one();
            $result->category = $category;
            if($result->save()){
                $res['code'] = 0;
                $res['text'] = "验证成功。";
                return $res;
            }else{
                $res['code'] = 1;
                $res['text'] = "验证失败。";
                return $res;
            }
        }
        return $this->render('shop');
    }
  

}